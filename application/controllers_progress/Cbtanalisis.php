<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtanalisis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error(
                'Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
        }

        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, bool $encode = true): void
    {
        $output = $encode ? json_encode($data) : $data;
        $this->output->set_content_type('application/json')->set_output($output);
    }

    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dropdown_model',  'dropdown');

        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $thn_sel = $this->input->get('thn')    ?? $tp->id_tp;
        $smt_sel = $this->input->get('smt')    ?? $smt->id_smt;
        $jadwal  = $this->input->get('jadwal');

        $data = [
            'user'            => $user,
            'judul'           => 'Analisa Soal',
            'subjudul'        => 'Analisa Soal Ujian',
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $tp,
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $smt,
            'tp_selected'     => $thn_sel,
            'smt_selected'    => $smt_sel,
            'jadwal_selected' => $jadwal,
        ];

        if ($jadwal !== null) {
            $info          = $this->cbt->getJadwalById($jadwal);
            $all_jawaban   = $this->cbt->getJawabanByBank($info->id_bank);
            $jawabans_siswa = [];

            foreach ($all_jawaban as $jawaban_siswa) {
                $jawabans_siswa[$jawaban_siswa->jenis_soal][$jawaban_siswa->nomor_soal][$jawaban_siswa->id_siswa] = $jawaban_siswa->jawaban_siswa;
            }

            $nilai_pg  = $this->cbt->getAllNilaiSiswa($jadwal);
            $all_soals = $this->cbt->getSoalByBank($info->id_bank);

            if (isset($all_soals[1])) {
                foreach ($all_soals[1] as $no => $soal) {
                    $soal->jawaban_siswa  = [];
                    $soal->skor_siswa     = [];
                    $soal->jumlah_benar   = 0;
                    $soal->jumlah_salah   = 0;

                    $total_siswa = 0;
                    $x           = [];
                    $jwbn_siswa  = $jawabans_siswa[1][$no] ?? [];

                    foreach ($jwbn_siswa as $id => $jawab_siswa) {
                        $total_siswa++;
                        if ($jawab_siswa == $soal->jawaban) {
                            $soal->jumlah_benar++;
                            $x[] = 1;
                        } else {
                            $soal->jumlah_salah++;
                            $x[] = 0;
                        }
                    }

                    $benar     = $soal->jumlah_benar;
                    $jml_siswa = $total_siswa;
                    $kesukaran = 0;
                    $status_soal = 'sukar';

                    if ($jml_siswa > 0) {
                        $kesukaran = round($benar / $jml_siswa, 2);
                        if ($kesukaran >= 0.7) {
                            $status_soal = 'mudah';
                        } elseif ($kesukaran >= 0.3) {
                            $status_soal = 'sedang';
                        }
                    }

                    $soal->tingkat_kesukaran = $kesukaran;
                    $soal->status_kesukaran  = $status_soal;

                    if ($jml_siswa % 2 === 1) {
                        $jml_siswa--;
                    }

                    $bagi                    = $jml_siswa / 2;
                    $y                       = [];
                    $yng_benar_golonganatas  = 0;
                    $yng_benar_golonganbawah = 0;
                    $no_urut                 = 1;

                    foreach ($nilai_pg as $id => $nilai) {
                        $y[] = $nilai->pg_benar;
                        if (isset($jwbn_siswa[$id])) {
                            $siswa_menjawab = $jwbn_siswa[$id];
                            if ($no_urut <= $bagi && $siswa_menjawab == $soal->jawaban) {
                                $yng_benar_golonganatas++;
                            } elseif ($no_urut > $bagi && $siswa_menjawab == $soal->jawaban) {
                                $yng_benar_golonganbawah++;
                            }
                        }
                        $no_urut++;
                    }

                    $soal->total_siswa  = $total_siswa;
                    $soal->benar_atas   = $yng_benar_golonganatas;
                    $soal->benar_bawah  = $yng_benar_golonganbawah;

                    $pearson          = $this->pearson($x, $y);
                    $soal->nilai_valid = $pearson;
                    $soal->table_r     = $this->nilaiSignifikansi($total_siswa);
                    $soal->status_valid = $this->nilaiSignifikansi($total_siswa) <= $pearson ? 'Valid' : 'Tidak valid';

                    $bagi_daya     = $bagi > 0 ? $bagi : 1;
                    $daya_pembeda  = $yng_benar_golonganatas / $bagi_daya - $yng_benar_golonganbawah / $bagi_daya;
                    $soal->daya_pembeda = $daya_pembeda;

                    if ($daya_pembeda >= 0.7) {
                        $soal->status_daya = 'Baik Sekali';
                    } elseif ($daya_pembeda >= 0.4) {
                        $soal->status_daya = 'Baik';
                    } elseif ($daya_pembeda >= 0.2) {
                        $soal->status_daya = 'Cukup';
                    } else {
                        $soal->status_daya = 'Jelek';
                    }
                }
            }

            $data['info']  = $info;
            $data['soals'] = $all_soals;
            $data['nilai'] = $nilai_pg;
        }

        $guru = $this->dashboard->getDataGuruByUserId($user->id, $thn_sel, $smt_sel);

        $data['guru']       = $guru;
        $data['kodejadwal'] = $this->dropdown->getAllJadwalGuru($thn_sel, $smt_sel, $guru->id_guru);

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/analisis/data');
        $this->load->view('members/guru/templates/footer');
    }

    private function pearson(array $x, array $y): float
    {
        $cx = count($x);
        $cy = count($y);

        if ($cx === 0 || $cy === 0) {
            return -1;
        }

        if ($cx < $cy) {
            $diff = $cy - $cx;
            for ($i = 0; $i < $diff; $i++) {
                array_pop($y);
            }
        }

        $n      = count($x);
        $sum_x  = array_sum($x);
        $sum_y  = array_sum($y);
        $sum_xy = 0;
        $sum_x2 = 0;
        $sum_y2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sum_xy += $x[$i] * $y[$i];
            $sum_x2 += $x[$i] ** 2;
            $sum_y2 += $y[$i] ** 2;
        }

        $denom = sqrt(($n * $sum_x2 - $sum_x ** 2) * ($n * $sum_y2 - $sum_y ** 2));

        return $denom == 0 ? 0 : ($n * $sum_xy - $sum_x * $sum_y) / $denom;
    }

    public function getNilaiKelas()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $kelas  = $this->input->get('kelas');
        $sesi   = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();

        $info    = $this->cbt->getJadwalById($jadwal, $sesi);
        $siswas  = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas);

        $this->output_json([
            'siswa'   => $siswas,
            'jawaban' => [],
            'info'    => $info,
        ]);
    }

    public function getJadwalUjianByJadwal()
    {
        $this->load->model('Cbt_model',      'cbt');
        $this->load->model('Dropdown_model', 'dropdown');

        $jadwal = $this->input->get('jadwal');
        $info   = $this->cbt->getJadwalById($jadwal);
        $kelas  = unserialize($info->bank_kelas ?? '');

        $kelases = [];
        foreach ($kelas as $value) {
            $kelases[$value['kelas_id']] = $this->dropdown->getNamaKelasById($info->id_tp, $info->id_smt, $value['kelas_id']);
        }

        $this->output_json($kelases);
    }

    public function kalkulasi()
    {
        $jadwal = $this->input->get('jadwal');
        $this->output_json($this->generateNilaiUjian($jadwal));
    }

    public function generateNilaiUjian($jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');

        $info       = $this->cbt->getJadwalById($jadwal);
        $bagi_pg    = $info->tampil_pg       / 100;
        $bobot_pg   = $info->bobot_pg        / 100;
        $bagi_pg2   = $info->tampil_kompleks / 100;
        $bobot_pg2  = $info->bobot_kompleks  / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian = $info->tampil_isian    / 100;
        $bobot_isian = $info->bobot_isian    / 100;
        $bagi_essai = $info->tampil_esai     / 100;
        $bobot_essai = $info->bobot_esai     / 100;

        $kelas_bank = unserialize($info->bank_kelas ?? '');
        $kelases    = array_column($kelas_bank, 'kelas_id');
        $siswas     = $this->cbt->getSiswaByKelas($info->id_tp, $info->id_smt, $kelases);

        $jawabans       = $this->cbt->getJawabanByBank($info->id_bank);
        $jawabans_siswa = [];

        foreach ($jawabans as $jawaban_siswa) {
            if ($jawaban_siswa->jenis_soal == '2') {
                $jawaban_siswa->opsi_a         = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_siswa  = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar  = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban_benar  = array_map('strtoupper', $jawaban_siswa->jawaban_benar ?? []);
                $jawaban_siswa->jawaban_benar  = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
            } else {
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            }

            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
        }

        $insets = [];

        foreach ($siswas as $siswa) {
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);

            // PG
            $jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1'])
                ? $jawabans_siswa[$siswa->id_siswa]['1']
                : [];
            $benar_pg = 0;
            $salah_pg = 0;

            if ($info->tampil_pg > 0 && !empty($jawaban_pg)) {
                foreach ($jawaban_pg as $jwb_pg) {
                    if ($jwb_pg !== null && $jwb_pg->jawaban_siswa !== null) {
                        if (strtoupper($jwb_pg->jawaban_siswa) === strtoupper($jwb_pg->jawaban_benar ?? '')) {
                            $benar_pg++;
                        } else {
                            $salah_pg++;
                        }
                    }
                }
            }
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;

            // PG Kompleks
            $jawaban_pg2    = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2'])
                ? $jawabans_siswa[$siswa->id_siswa]['2']
                : [];
            $benar_pg2      = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2   = 0;

            foreach ($jawaban_pg2 as $jawab_pg2) {
                $otomatis_pg2      = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $arr_benar         = [];
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    if (in_array($js, $jawab_pg2->jawaban_benar)) {
                        $arr_benar[] = true;
                    }
                }
                $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
            }

            $s_pg2     = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $input_pg2 = ($nilai_input !== null && $nilai_input->kompleks_nilai !== null) ? $nilai_input->kompleks_nilai : 0;
            $skor_pg2  = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);

            // Jodohkan
            $jawaban_jodoh    = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3'])
                ? $jawabans_siswa[$siswa->id_siswa]['3']
                : [];
            $benar_jod        = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod     = 0;

            foreach ($jawaban_jodoh as $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $arrSoal           = $jawab_jod->jawaban_benar->jawaban;
                $headSoal          = array_shift($arrSoal);
                $arrJwbSoal        = [];
                $items             = 0;

                foreach ($arrSoal as $kolSoal) {
                    $jwb = new stdClass();
                    foreach ($kolSoal as $pos => $kol) {
                        if ($kol == '1') {
                            $jwb->subtitle[] = $headSoal[$pos];
                            $items++;
                        }
                    }
                    $jwb->title   = array_shift($kolSoal);
                    $arrJwbSoal[] = $jwb;
                }

                $arrJawab   = $jawab_jod->jawaban_siswa->jawaban;
                $headJawab  = array_shift($arrJawab);
                $arrJwbJawab = [];

                foreach ($arrJawab as $kolJawab) {
                    $jwbs = new stdClass();
                    foreach ($kolJawab as $po => $kol) {
                        if ($kol == '1') {
                            $jwbs->subtitle[] = $headJawab[$po];
                        }
                    }
                    $arrJwbJawab[] = $jwbs;
                }

                $item_benar = 0;
                $item_salah = 0;

                foreach ($arrJwbJawab as $p => $ajjs) {
                    foreach ($ajjs->subtitle as $ajs) {
                        if (in_array($ajs, $arrJwbSoal[$p]->subtitle)) {
                            $item_benar++;
                        } else {
                            $item_salah++;
                        }
                    }
                }

                $benar_jod   += 1 / ($items ?: 1) * $item_benar;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
            }

            $s_jod    = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $input_jod = ($nilai_input !== null && $nilai_input->jodohkan_nilai !== null) ? $nilai_input->jodohkan_nilai : 0;
            $skor_jod  = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);

            // Isian
            $jawaban_is     = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4'])
                ? $jawabans_siswa[$siswa->id_siswa]['4']
                : [];
            $benar_is       = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is    = 0;

            foreach ($jawaban_is as $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $otomatis_is      = $jawab_is->nilai_otomatis;
                $benar = $jawab_is !== null
                    && strtolower($jawab_is->jawaban_siswa ?? '') === strtolower($jawab_is->jawaban_benar ?? '');
                if ($benar) {
                    $benar_is++;
                }
            }

            $s_is    = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $input_is = ($nilai_input !== null && $nilai_input->isian_nilai !== null) ? $nilai_input->isian_nilai : 0;
            $skor_is  = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);

            // Esai
            $jawaban_es     = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5'])
                ? $jawabans_siswa[$siswa->id_siswa]['5']
                : [];
            $benar_es       = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es    = 0;

            foreach ($jawaban_es as $jawab_es) {
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $otomatis_es      = $jawab_es->nilai_otomatis;
                $benar = $jawab_es !== null
                    && strtolower($jawab_es->jawaban_siswa ?? '') === strtolower($jawab_es->jawaban_benar ?? '');
                if ($benar) {
                    $benar_es++;
                }
            }

            $s_es    = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $input_es = ($nilai_input !== null && $nilai_input->essai_nilai !== null) ? $nilai_input->essai_nilai : 0;
            $skor_es  = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);

            $insets[] = [
                'id_nilai'        => $siswa->id_siswa . '0' . $jadwal,
                'id_siswa'        => $siswa->id_siswa,
                'id_jadwal'       => $jadwal,
                'pg_benar'        => $benar_pg,
                'pg_nilai'        => round($skor_pg,  2),
                'kompleks_nilai'  => round($skor_pg2, 2),
                'jodohkan_nilai'  => round($skor_jod, 2),
                'isian_nilai'     => round($skor_is,  2),
                'essai_nilai'     => round($skor_es,  2),
            ];
        }

        return $this->db->update_batch('cbt_nilai', $insets, 'id_nilai');
    }

    private function nilaiSignifikansi(int $jml): float
    {
        $list = [
            3 => 0.997,
            4 => 0.95,
            5 => 0.878,
            6 => 0.811,
            7 => 0.754,
            8 => 0.707,
            9 => 0.666,
            10 => 0.632,
            11 => 0.602,
            12 => 0.576,
            13 => 0.553,
            14 => 0.532,
            15 => 0.514,
            16 => 0.497,
            17 => 0.482,
            18 => 0.468,
            19 => 0.456,
            20 => 0.444,
            21 => 0.433,
            22 => 0.423,
            23 => 0.413,
            24 => 0.404,
            25 => 0.396,
            26 => 0.388,
            27 => 0.381,
            28 => 0.374,
            29 => 0.367,
            30 => 0.361,
            31 => 0.355,
            32 => 0.349,
            33 => 0.344,
            34 => 0.339,
            35 => 0.334,
            36 => 0.329,
            37 => 0.325,
            38 => 0.32,
            39 => 0.316,
            40 => 0.312,
            41 => 0.308,
            42 => 0.304,
            43 => 0.301,
            44 => 0.297,
            45 => 0.294,
            46 => 0.291,
            47 => 0.288,
            48 => 0.284,
            49 => 0.281,
            50 => 0.279,
            55 => 0.266,
            60 => 0.254,
            65 => 0.244,
            70 => 0.235,
            75 => 0.227,
            80 => 0.22,
            85 => 0.213,
            90 => 0.207,
            95 => 0.202,
            100 => 0.195,
            125 => 0.176,
            150 => 0.159,
            175 => 0.149,
            200 => 0.138,
            300 => 0.113,
            400 => 0.098,
            500 => 0.088,
            600 => 0.08,
            700 => 0.074,
            800 => 0.07,
            900 => 0.065,
            1000 => 0.062,
        ];

        if (isset($list[$jml])) {
            return $list[$jml];
        }

        $key = $this->getClosest($jml, array_keys($list));
        return $list[max($key, 4)];
    }

    private function getClosest(int $search, array $arr): int
    {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest === null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }
        return $closest;
    }
}
