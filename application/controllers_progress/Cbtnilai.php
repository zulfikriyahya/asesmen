<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtnilai extends CI_Controller
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

        $this->load->library(['datatables', 'form_validation', 'upload']);
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, bool $encode = true): void
    {
        $output = $encode ? json_encode($data) : $data;
        $this->output->set_content_type('application/json')->set_output($output);
    }

    private function arrToUpper($val): string
    {
        return strtoupper($val ?? '');
    }

    private function sortArrays(array &$array): void
    {
        foreach ($array as &$subArray) {
            if ($subArray) {
                sort($subArray);
            }
        }
    }

    /**
     * Ekstrak jawaban jodohkan dari format matrix menjadi format links.
     */
    private function parseJodohkanLinks($matrix): array
    {
        $arrAlphabet = range('A', 'Z');
        $result      = [];

        foreach ($matrix->jawaban as $idx => $jbs) {
            if ($idx <= 0) continue;
            $result[$idx] = [];
            foreach ($jbs as $idxs => $jb) {
                if ($idxs <= 0) continue;
                if ($jb === '1') {
                    $result[$idx][] = $arrAlphabet[$idxs - 1];
                }
            }
        }

        return $result;
    }

    /**
     * Parse dan normalisasi satu jawaban siswa (semua jenis soal).
     */
    private function parseJawabanSiswa(object $jawaban_siswa): object
    {
        if ($jawaban_siswa->jenis_soal == '2') {
            $jawaban_siswa->opsi_a         = @unserialize($jawaban_siswa->opsi_a ?? '');
            $jawaban_siswa->jawaban_siswa  = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar  = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_benar  = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar ?? []);
            $jawaban_siswa->jawaban_benar  = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
        } else {
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
        }

        $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
        $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));

        return $jawaban_siswa;
    }

    /**
     * Parse jawaban jodohkan dan convert ke format links di jawaban_siswa dan jawaban_benar.
     */
    private function parseJodohkanSiswa(object $jawaban_siswa): object
    {
        $jawaban_siswa->jawaban        = @unserialize($jawaban_siswa->jawaban ?? '');
        $jawaban_siswa->jawaban        = json_decode(json_encode($jawaban_siswa->jawaban));
        $jawaban_siswa->jawaban_siswa  = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
        $jawaban_siswa->jawaban_benar  = @unserialize($jawaban_siswa->jawaban_benar ?? '');
        $jawaban_siswa->jawaban_siswa  = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
        $jawaban_siswa->jawaban_benar  = json_decode(json_encode($jawaban_siswa->jawaban_benar));

        if ($jawaban_siswa->jawaban_siswa && isset($jawaban_siswa->jawaban_siswa->jawaban)) {
            $links = $this->parseJodohkanLinks($jawaban_siswa->jawaban_siswa);
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode(['links' => $links]));
        }

        if ($jawaban_siswa->jawaban_benar && isset($jawaban_siswa->jawaban_benar->jawaban)) {
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($this->parseJodohkanLinks($jawaban_siswa->jawaban_benar)));
        }

        return $jawaban_siswa;
    }

    /**
     * Hitung skor jodohkan dari satu jawaban.
     */
    private function hitungSkorJodohkan(object $jawab_jod): array
    {
        $item_benar = 0;
        $items      = 0;
        $arrBenar   = [];

        if (!isset($jawab_jod->jawaban_siswa->links)) {
            return ['item_benar' => 0, 'items' => 0, 'arrBenar' => []];
        }

        $array1 = (array) $jawab_jod->jawaban_benar->links;
        $array2 = (array) $jawab_jod->jawaban_siswa->links;
        $this->sortArrays($array1);
        $this->sortArrays($array2);

        foreach ($array1 as $key => $subArray1) {
            $arrBenar[$key]        = new stdClass();
            $arrBenar[$key]->benar = 0;
            $arrBenar[$key]->salah = 0;
            $arrBenar[$key]->kurang = 0;
            $items += count($subArray1);

            if (isset($array2[$key])) {
                $sameItems             = array_intersect($subArray1, $array2[$key]);
                $item_benar           += count($sameItems);
                $arrBenar[$key]->benar += count($sameItems);
                $diffItems1            = array_diff($subArray1, $array2[$key]);
                $arrBenar[$key]->kurang += count($diffItems1);
            } else {
                $arrBenar[$key]->kurang += count($subArray1);
            }
        }

        return ['item_benar' => $item_benar, 'items' => $items, 'arrBenar' => $arrBenar];
    }

    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dropdown_model',  'dropdown');
        $this->load->model('Kelas_model',     'kelas');

        $user    = $this->ion_auth->user()->row();
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $jadwal_selected = $this->input->get('jadwal');

        $id_guru = null;
        if ($this->ion_auth->in_group('guru')) {
            $guru    = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $id_guru = $guru->id_guru;
        }

        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrKelas   = [];

        if ($mapel !== null) {
            foreach ($mapel as $m) {
                foreach ($m->kelas_mapel as $kls) {
                    if ($kls->kelas) {
                        $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
                    }
                }
            }
        }

        $data = [
            'user'             => $user,
            'judul'            => 'Hasil Ujian Siswa',
            'subjudul'         => 'Nilai Siswa',
            'setting'          => $this->dashboard->getSetting(),
            'tp'               => $this->dashboard->getTahun(),
            'tp_active'        => $tp,
            'smt'              => $this->dashboard->getSemester(),
            'smt_active'       => $smt,
            'ruang'            => $this->dropdown->getAllRuang(),
            'sesi'             => $this->dropdown->getAllSesi(),
            'kelas_selected'   => $this->input->get('kelas'),
            'jadwal_selected'  => $jadwal_selected,
            'jadwal'           => [],
            'siswas'           => [],
            'kelas'            => $arrKelas,
        ];

        if (isset($guru)) {
            $data['guru'] = $guru;
        }

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/nilai/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function detail()
    {
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dashboard_model', 'dashboard');

        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $jadwal  = $this->input->get('jadwal');
        $siswa   = $this->cbt->getSiswaById($tp->id_tp, $smt->id_smt, $this->input->get('siswa'));
        $info    = $this->cbt->getJadwalById($jadwal);

        $bagi_pg     = $info->tampil_pg       / 100;
        $bobot_pg    = $info->bobot_pg        / 100;
        $bagi_pg2    = $info->tampil_kompleks / 100;
        $bobot_pg2   = $info->bobot_kompleks  / 100;
        $bagi_jodoh  = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan  / 100;
        $bagi_isian  = $info->tampil_isian    / 100;
        $bobot_isian = $info->bobot_isian     / 100;
        $bagi_essai  = $info->tampil_esai     / 100;
        $bobot_essai = $info->bobot_esai      / 100;

        $jawabans       = $this->cbt->getJawabanSiswaByJadwal($jadwal, $siswa->id_siswa);
        $soal           = [];
        $jawabans_siswa = [];

        foreach ($jawabans as $jwb) {
            if ($jwb->jenis_soal == '3') {
                $jwb = $this->parseJodohkanSiswa($jwb);
            } else {
                $jwb = $this->parseJawabanSiswa($jwb);
            }
            $jawabans_siswa[$jwb->id_siswa][$jwb->jenis_soal][] = $jwb;
            $soal[$jwb->jenis_soal][] = $jwb;
        }

        $ada_jawaban       = isset($jawabans_siswa[$siswa->id_siswa]);
        $nilai_input       = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
        $skor              = new stdClass();
        $skor->dikoreksi   = $nilai_input ? $nilai_input->dikoreksi : null;

        // PG
        $jawaban_pg = ($ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']))
            ? $jawabans_siswa[$siswa->id_siswa]['1']
            : [];
        $benar_pg = 0;
        $salah_pg = 0;

        foreach ($jawaban_pg as $jwb_pg) {
            $benar = $jwb_pg !== null && $jwb_pg->jawaban_siswa !== null
                && strtoupper($jwb_pg->jawaban_siswa ?? '') === strtoupper($jwb_pg->jawaban ?? '');

            if ($benar) {
                $benar_pg++;
            } else {
                $salah_pg++;
            }

            $ks = array_search($jwb_pg->nomor_soal, array_column($soal[1], 'nomor_soal'));
            $soal[1][$ks]->point   = $benar && $info->bobot_pg > 0 ? round($info->bobot_pg / $info->tampil_pg, 2) : 0;
            $soal[1][$ks]->analisa = $benar
                ? '<i class="fa fa-check-circle text-green text-lg"></i>'
                : '<i class="fa fa-times-circle text-red text-lg"></i>';
        }

        $skor->skor_pg = $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;

        // PG Kompleks
        $jawaban_pg2      = ($ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']))
            ? $jawabans_siswa[$siswa->id_siswa]['2']
            : [];
        $benar_pg2        = 0;
        $skor_koreksi_pg2 = 0.0;
        $otomatis_pg2     = 0;

        foreach ($jawaban_pg2 as $jawab_pg2) {
            $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
            $arr_benar         = [];

            if ($jawab_pg2->jawaban_siswa) {
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    if (in_array($js, $jawab_pg2->jawaban)) {
                        $arr_benar[] = true;
                    }
                }
            }

            $jml_jawaban = count($jawab_pg2->jawaban);
            $benar_pg2  += $jml_jawaban > 0 ? 1 / $jml_jawaban * count($arr_benar) : 0;

            $point_benar = $info->bobot_kompleks > 0 ? round($info->bobot_kompleks / $info->tampil_kompleks, 2) : 0;
            $point_item  = $jml_jawaban > 0 ? $point_benar / $jml_jawaban : 0;
            $pk          = $point_item * count($arr_benar);
            $jml_benar   = count($arr_benar);
            $analisa     = $jml_benar == $jml_jawaban
                ? '<i class="fa fa-check-circle text-green text-lg"></i>'
                : ($jml_benar > 0
                    ? '<i class="fa fa-check-circle text-yellow text-lg"></i>'
                    : '<i class="fa fa-times-circle text-red text-lg"></i>');

            $ks = array_search($jawab_pg2->nomor_soal, array_column($soal[2], 'nomor_soal'));
            $soal[2][$ks]->analisa       = $analisa;
            $soal[2][$ks]->point         = $jawab_pg2->nilai_koreksi;
            $soal[2][$ks]->point_koreksi = $jawab_pg2->nilai_koreksi;
            $soal[2][$ks]->point_otomatis = round($pk, 2);
            $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
        }

        $s_pg2    = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
        $input_pg2 = ($nilai_input !== null && $nilai_input->kompleks_nilai !== null) ? $nilai_input->kompleks_nilai : 0;
        $skor_pg2  = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
        $skor->skor_kompleks = $skor_pg2;

        // Jodohkan
        $jawaban_jodoh    = ($ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']))
            ? $jawabans_siswa[$siswa->id_siswa]['3']
            : [];
        $benar_jod        = 0;
        $skor_koreksi_jod = 0.0;
        $otomatis_jod     = 0;

        foreach ($jawaban_jodoh as $jawab_jod) {
            $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
            $point_benar       = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
            $result            = $this->hitungSkorJodohkan($jawab_jod);
            $item_benar        = $result['item_benar'];
            $items             = $result['items'];
            $arrBenar          = $result['arrBenar'];
            $point_soal        = $items > 0 ? 1 / $items * $item_benar * $point_benar : 0;

            $benar_jod += $items > 0 ? 1 / $items * $item_benar : 0;

            // Build tabel soal
            $arrSoal    = $jawab_jod->jawaban->jawaban;
            $headSoal   = array_shift($arrSoal);
            $arrJwbSoal = [];
            foreach ($arrSoal as $kolSoal) {
                $jwb = new stdClass();
                foreach ($kolSoal as $pos => $kol) {
                    if ($kol == '1') $jwb->subtitle[] = $headSoal[$pos];
                }
                $jwb->title   = array_shift($kolSoal);
                $arrJwbSoal[] = $jwb;
            }

            // Build tabel jawab
            $arrJawab    = isset($jawab_jod->jawaban_siswa->jawaban) ? $jawab_jod->jawaban_siswa->jawaban : [];
            $headJawab   = array_shift($arrJawab);
            $arrJwbJawab = [];
            foreach ($arrJawab as $kolJawab) {
                $jwbs = new stdClass();
                foreach ($kolJawab as $po => $kol) {
                    if ($kol == '1') $jwbs->subtitle[] = $headJawab[$po];
                }
                $jwbs->title   = array_shift($kolJawab);
                $arrJwbJawab[] = $jwbs;
            }

            $analisa = $item_benar == $items
                ? '<i class="fa fa-check-circle text-green text-lg"></i>'
                : ($item_benar == 0
                    ? '<i class="fa fa-times-circle text-red text-lg"></i>'
                    : '<i class="fa fa-times-circle text-yellow text-lg"></i>');

            $ks = array_search($jawab_jod->nomor_soal, array_column($soal[3], 'nomor_soal'));
            $soal[3][$ks]->type_soal     = $jawab_jod->jawaban->type ?? null;
            $soal[3][$ks]->tabel_soal    = $arrJwbSoal;
            $soal[3][$ks]->tabel_jawab   = $arrJwbJawab;
            $soal[3][$ks]->tabel_benar   = $arrBenar;
            $soal[3][$ks]->point_soal    = $point_soal;
            $soal[3][$ks]->point         = $jawab_jod->nilai_koreksi;
            $soal[3][$ks]->point_koreksi = $jawab_jod->nilai_koreksi;
            $soal[3][$ks]->point_otomatis = round($point_soal, 2);
            $soal[3][$ks]->analisa       = $analisa;
            $otomatis_jod = $jawab_jod->nilai_otomatis;
        }

        $s_jod    = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
        $input_jod = ($nilai_input !== null && $nilai_input->jodohkan_nilai !== null) ? $nilai_input->jodohkan_nilai : 0;
        $skor_jod  = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
        $skor->skor_jodohkan = $skor_jod;

        // Isian
        $jawaban_is     = ($ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']))
            ? $jawabans_siswa[$siswa->id_siswa]['4']
            : [];
        $benar_is       = 0;
        $skor_koreksi_is = 0.0;
        $otomatis_is    = 0;

        foreach ($jawaban_is as $jawab_is) {
            $skor_koreksi_is += $jawab_is->nilai_koreksi;
            $otomatis_is      = $jawab_is->nilai_otomatis;
            $benar = $jawab_is !== null
                && strtolower($jawab_is->jawaban_siswa ?? '') === strtolower($jawab_is->jawaban ?? '');
            if ($benar) $benar_is++;

            $point = $benar && $info->bobot_isian > 0 ? round($info->bobot_isian / $info->tampil_isian, 2) : 0;
            $analisa = $benar
                ? '<i class="fa fa-check-circle text-green text-lg"></i>'
                : '<i class="fa fa-times-circle text-yellow text-lg"></i>';

            $ks = array_search($jawab_is->nomor_soal, array_column($soal[4], 'nomor_soal'));
            $soal[4][$ks]->point          = $jawab_is->nilai_koreksi;
            $soal[4][$ks]->point_koreksi  = $jawab_is->nilai_koreksi;
            $soal[4][$ks]->point_otomatis = $point;
            $soal[4][$ks]->analisa        = $analisa;
        }

        $s_is    = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
        $input_is = ($nilai_input !== null && $nilai_input->isian_nilai !== null) ? $nilai_input->isian_nilai : 0;
        $skor_is  = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
        $skor->skor_isian = $skor_is;

        // Esai
        $jawaban_es     = ($ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']))
            ? $jawabans_siswa[$siswa->id_siswa]['5']
            : [];
        $benar_es       = 0;
        $skor_koreksi_es = 0.0;
        $otomatis_es    = 0;

        foreach ($jawaban_es as $jawab_es) {
            $skor_koreksi_es += $jawab_es->nilai_koreksi;
            $otomatis_es      = $jawab_es->nilai_otomatis;
            $benar = $jawab_es !== null
                && strtolower($jawab_es->jawaban_siswa ?? '') === strtolower($jawab_es->jawaban ?? '');
            if ($benar) $benar_es++;

            $point = $benar && $info->bobot_esai > 0 ? round($info->bobot_esai / $info->tampil_esai, 2) : 0;
            $analisa = $benar
                ? '<i class="fa fa-check-circle text-green text-lg"></i>'
                : '<i class="fa fa-times-circle text-yellow text-lg"></i>';

            $ks = array_search($jawab_es->nomor_soal, array_column($soal[5], 'nomor_soal'));
            $soal[5][$ks]->point          = $jawab_es->nilai_koreksi;
            $soal[5][$ks]->point_koreksi  = $jawab_es->nilai_koreksi;
            $soal[5][$ks]->point_otomatis = $point;
            $soal[5][$ks]->analisa        = $analisa;
        }

        $s_es    = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
        $input_es = ($nilai_input !== null && $nilai_input->essai_nilai !== null) ? $nilai_input->essai_nilai : 0;
        $skor_es  = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
        $skor->skor_essai = $skor_es;

        $skor->skor_total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;

        $durasies  = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs      = $this->cbt->getLogUjianByJadwal($jadwal);
        $dur_siswa = null;
        $log_siswa = [];

        foreach ($durasies as $durasi) {
            if ($durasi->id_siswa == $siswa->id_siswa) {
                $dur_siswa = $durasi;
                break;
            }
        }
        foreach ($logs as $log) {
            if ($log->id_siswa == $siswa->id_siswa) {
                $log_siswa[] = $log;
            }
        }

        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $data = [
            'user'      => $user,
            'judul'     => 'Koreksi Hasil Siswa',
            'subjudul'  => 'Hasil Siswa',
            'setting'   => $this->dashboard->getSetting(),
            'durasi'    => $dur_siswa,
            'log'       => $log_siswa,
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'info'      => $info,
            'siswa'     => $siswa,
            'soal'      => $soal,
            'skor'      => $skor,
            'ada_nilai' => $nilai_input !== null,
            'guru'      => $guru,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/nilai/detail');
        $this->load->view('members/guru/templates/footer');
    }

    public function simpanKoreksi()
    {
        $siswa  = $this->input->post('siswa',  true);
        $jadwal = $this->input->post('jadwal', true);
        $jenis  = $this->input->post('jenis',  true);
        $nilais = json_decode($this->input->post('nilai', true));

        $updated = [];
        $jml     = 0;

        foreach ($nilais as $nilai) {
            $jml       += $nilai->koreksi;
            $updated[]  = ['id_soal_siswa' => $nilai->id_soal, 'nilai_koreksi' => $nilai->koreksi, 'nilai_otomatis' => 1];
        }

        $result = $this->db->update_batch('cbt_soal_siswa', $updated, 'id_soal_siswa');

        if ($result) {
            $this->db->set($jenis, $jml)
                ->where('id_nilai', $siswa . '0' . $jadwal)
                ->update('cbt_nilai');
        }

        $this->output_json(['success' => $result]);
    }

    public function tandaiKoreksi()
    {
        $siswa  = $this->input->post('siswa',  true);
        $jadwal = $this->input->post('jadwal', true);

        $updated = $this->db->set('dikoreksi', 1)
            ->where('id_nilai', $siswa . '0' . $jadwal)
            ->update('cbt_nilai');

        $this->output_json(['success' => $updated]);
    }

    public function tandaisemua()
    {
        $this->load->model('Cbt_model', 'cbt');

        $id_jadwal = $this->input->post('id_jadwal', true);
        $siswas    = $this->input->post('ids',       true);
        $updated   = 0;

        foreach ($siswas as $id_siswa => $memulai) {
            $info    = $this->cbt->getJadwalById($id_jadwal);
            $jawabans = $this->cbt->getJawabanByBank($info->id_bank, $id_siswa);

            $jawabans_siswa = [];
            foreach ($jawabans as $jwb) {
                if ($jwb->jenis_soal == '3') {
                    $jwb = $this->parseJodohkanSiswa($jwb);
                } else {
                    $jwb = $this->parseJawabanSiswa($jwb);
                }
                $jawabans_siswa[$jwb->jenis_soal][] = $jwb;
            }

            $bagi_pg     = $info->tampil_pg       / 100;
            $bobot_pg    = $info->bobot_pg        / 100;
            $bagi_pg2    = $info->tampil_kompleks / 100;
            $bobot_pg2   = $info->bobot_kompleks  / 100;
            $bagi_jodoh  = $info->tampil_jodohkan / 100;
            $bobot_jodoh = $info->bobot_jodohkan  / 100;
            $bagi_isian  = $info->tampil_isian    / 100;
            $bobot_isian = $info->bobot_isian     / 100;
            $bagi_essai  = $info->tampil_esai     / 100;
            $bobot_essai = $info->bobot_esai      / 100;

            // PG
            $jawaban_pg = $jawabans_siswa['1'] ?? [];
            $benar_pg   = 0;
            foreach ($jawaban_pg as $jwb_pg) {
                if (
                    $jwb_pg !== null && $jwb_pg->jawaban_siswa !== null
                    && strtoupper($jwb_pg->jawaban_siswa ?? '') === strtoupper($jwb_pg->jawaban_benar ?? '')
                ) {
                    $benar_pg++;
                }
            }
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;

            // PG Kompleks
            $jawaban_pg2      = $jawabans_siswa['2'] ?? [];
            $benar_pg2        = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2     = 0;
            foreach ($jawaban_pg2 as $jawab_pg2) {
                $otomatis_pg2      = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $arr_benar         = [];
                if ($jawab_pg2->jawaban_siswa) {
                    foreach ($jawab_pg2->jawaban_siswa as $js) {
                        if (in_array($js, $jawab_pg2->jawaban_benar)) {
                            $arr_benar[] = true;
                        }
                    }
                }
                $jml_benar = count($jawab_pg2->jawaban_benar);
                $benar_pg2 += $jml_benar > 0 ? 1 / $jml_benar * count($arr_benar) : 0;
            }
            $s_pg2  = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;

            // Jodohkan
            $jawaban_jodoh    = $jawabans_siswa['3'] ?? [];
            $benar_jod        = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod     = 0;
            foreach ($jawaban_jodoh as $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $result            = $this->hitungSkorJodohkan($jawab_jod);
                $items             = $result['items'];
                $item_benar        = $result['item_benar'];
                $benar_jod        += $items > 0 ? 1 / $items * $item_benar : 0;
                $otomatis_jod      = $jawab_jod->nilai_otomatis;
            }
            $s_jod  = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $skor_jod = $otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod;

            // Isian
            $jawaban_is     = $jawabans_siswa['4'] ?? [];
            $benar_is       = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is    = 0;
            foreach ($jawaban_is as $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $otomatis_is      = $jawab_is->nilai_otomatis;
                $benar = $jawab_is !== null
                    && strtolower($jawab_is->jawaban_siswa ?? '') === strtolower($jawab_is->jawaban_benar ?? '');
                if ($benar) $benar_is++;
            }
            $s_is   = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;

            // Esai
            $jawaban_es     = $jawabans_siswa['5'] ?? [];
            $benar_es       = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es    = 0;
            foreach ($jawaban_es as $jawab_es) {
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $otomatis_es      = $jawab_es->nilai_otomatis;
                $benar = $jawab_es !== null
                    && strtolower($jawab_es->jawaban_siswa ?? '') === strtolower($jawab_es->jawaban_benar ?? '');
                if ($benar) $benar_es++;
            }
            $s_es   = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;

            $insert = [
                'id_nilai'       => $id_siswa . '0' . $id_jadwal,
                'id_siswa'       => $id_siswa,
                'id_jadwal'      => $id_jadwal,
                'pg_benar'       => $benar_pg,
                'pg_nilai'       => round($skor_pg,  2),
                'kompleks_nilai' => round($skor_pg2, 2),
                'jodohkan_nilai' => round($skor_jod, 2),
                'isian_nilai'    => round($skor_is,  2),
                'essai_nilai'    => round($skor_es,  2),
                'dikoreksi'      => $memulai === '2' ? '0' : '1',
            ];

            $this->db->replace('cbt_nilai', $insert);
            $updated++;
        }

        $this->output_json(['success' => $updated, 'siswa' => $siswas]);
    }

    public function inputEssai()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dropdown_model',  'dropdown');

        $kelas_selected  = $this->input->get('kelas');
        $jadwal_selected = $this->input->get('jadwal');
        $info = $this->cbt->getJadwalById($jadwal_selected);
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas_selected);
        $ids    = array_column($siswas, 'id_siswa');
        $nilai  = $this->cbt->getNilaiAllSiswa([$jadwal_selected], $ids);

        foreach ($siswas as $siswa) {
            $n = $nilai[$siswa->id_siswa] ?? null;
            $siswa->skor_pg     = $n ? $n->pg_nilai        : '0';
            $siswa->skor_pg2    = $n ? $n->kompleks_nilai  : '0';
            $siswa->skor_jod    = $n ? $n->jodohkan_nilai  : '0';
            $siswa->skor_isian  = $n ? $n->isian_nilai     : '0';
            $siswa->skor_essai  = $n ? $n->essai_nilai     : '0';
        }

        $user = $this->ion_auth->user()->row();

        $data = [
            'user'            => $user,
            'judul'           => 'Input Nilai Manual',
            'subjudul'        => '',
            'profile'         => $this->dashboard->getProfileAdmin($user->id),
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'smt'             => $this->dashboard->getSemester(),
            'tp_active'       => $tp,
            'smt_active'      => $smt,
            'nama_kelas'      => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kelas_selected),
            'kelas_selected'  => $kelas_selected,
            'jadwal_selected' => $jadwal_selected,
            'jadwal'          => $info,
            'siswas'          => $siswas,
        ];

        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/nilai/nilai_essai');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/nilai/nilai_essai');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function simpanKoreksiEssai()
    {
        $this->load->model('Cbt_model', 'cbt');

        $jadwal = $this->input->post('jadwal', true);
        $nilais = json_decode($this->input->post('nilai', true));
        $update = 0;
        $blm_selesai = [];

        foreach ($nilais as $nilai) {
            $nilai_siswa = $this->cbt->getNilaiSiswaByJadwal($jadwal, $nilai->id_siswa);

            if ($nilai_siswa !== null) {
                $replace = [
                    'id_nilai'       => $nilai_siswa->id_nilai,
                    'id_siswa'       => $nilai_siswa->id_siswa,
                    'id_jadwal'      => $nilai_siswa->id_jadwal,
                    'pg_benar'       => $nilai_siswa->pg_benar,
                    'pg_nilai'       => $nilai_siswa->pg_nilai,
                    'kompleks_nilai' => isset($nilai->kompleks_nilai) && $nilai->kompleks_nilai !== null ? $nilai->kompleks_nilai : '0',
                    'jodohkan_nilai' => isset($nilai->jodohkan_nilai) && $nilai->jodohkan_nilai !== null ? $nilai->jodohkan_nilai : '0',
                    'isian_nilai'    => isset($nilai->isian_nilai)    && $nilai->isian_nilai    !== null ? $nilai->isian_nilai    : '0',
                    'essai_nilai'    => isset($nilai->essai_nilai)    && $nilai->essai_nilai    !== null ? $nilai->essai_nilai    : '0',
                    'dikoreksi'      => '1',
                ];
                $this->db->replace('cbt_nilai', $replace);
                $update++;
            } else {
                $blm_selesai[] = $nilai->id_siswa;
            }
        }

        $this->output_json([
            'success'     => $update,
            'data'        => $nilais,
            'blm_selesai' => count($blm_selesai),
        ]);
    }
}
