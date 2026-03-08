<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bukurapor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin()) {
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
        $this->load->model([
            'Dashboard_model' => 'dashboard',
            'Rapor_model'     => 'rapor',
            'Kelas_model'     => 'kelas',
            'Dropdown_model'  => 'dropdown',
            'Master_model'    => 'master',
        ]);

        if ($this->db->table_exists('buku_nilai') && $this->dashboard->total('buku_nilai') > 0) {
            $this->restoreNilai();
        }

        $id_tp    = $this->input->get('tp',  true);
        $id_smt   = $this->input->get('smt', true);
        $id_kelas = $this->input->get('kls', true);

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();

        $data = [
            'user'     => $user,
            'judul'    => 'Kumpulan Nilai Rapor',
            'subjudul' => 'Nilai Rapor Siswa',
            'setting'  => $setting,
        ];

        $kelases = $this->kelas->getAllKelas();
        $all_kls = [];
        foreach ($kelases as $row) {
            $all_kls[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
        }

        $kelas = $all_kls[$id_tp][$id_smt][$id_kelas] ?? null;
        if ($kelas === null) {
            show_error('Kelas tidak ditemukan.', 404);
        }

        $jurusan        = $this->kelas->getJurusanById($kelas->id_jurusan);
        $kelompoks      = $this->master->getKodeKelompokMapel();
        $siswas         = $this->rapor->getDetailSiswa($id_kelas, $id_tp, $id_smt);
        $kategori_mapel = $this->master->getKategoriKelompokMapel();

        $arrk = [];
        foreach ($kategori_mapel as $km) {
            if (!in_array($km->kode_kel_mapel, $arrk)) {
                $arrk[] = $km->kode_kel_mapel;
            }
        }

        $mapels       = $this->master->getAllStatusMapel(empty($arrk) ? null : $arrk, $jurusan->mapel_peminatan ?? null);
        $ekstras      = $this->kelas->getKelasEkskul($id_kelas, $id_tp, $id_smt);
        $settingRapor = $this->rapor->getRaporSetting($id_tp, $id_smt);

        $other       = ($id_smt === '1') ? '2' : '1';
        $nilai_sikap = $this->rapor->getNilaiSikapByKelas($id_kelas, $id_tp, $id_smt);
        $nilai_rapor = $this->rapor->getNilaiRaporByKelas($id_kelas, $id_tp, $id_smt);
        $prestasis   = $this->rapor->getPrestasiByKelas($id_kelas, $id_tp, $id_smt);
        $catatans    = $this->rapor->getCatatanWaliByKelas($id_kelas, $id_tp, $id_smt);

        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai ?? '');
        }

        $sikap       = [];
        $nilai       = [];
        $fisik       = [];
        $desks       = [];
        $absensi     = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];
        $dummySikap  = ['predikat' => ''];
        $dummyDesks  = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => ''];
        $dummyAbsen  = ['s' => ' - ', 'i' => ' - ', 'a' => ' - ', 'saran' => ''];

        foreach ($siswas as $siswa) {
            $id_siswa = $siswa->id_siswa;

            $sikap[$id_siswa][1] = ['deskripsi' => '', 'predikat' => $dummySikap];
            $sikap[$id_siswa][2] = ['deskripsi' => '', 'predikat' => $dummySikap];
            if (!empty($nilai_sikap) && isset($nilai_sikap[$id_siswa])) {
                $sikap[$id_siswa] = $nilai_sikap[$id_siswa];
            }

            foreach ($mapels as $mapel) {
                $key_mapel = array_search(
                    $mapel->id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt,
                    array_column($nilai_rapor, 'id_nilai_harian')
                );
                if ($key_mapel !== false) {
                    $nilai[$id_siswa][$mapel->id_mapel] = $nilai_rapor[$key_mapel];
                }
            }

            $desks[$id_siswa]   = $prestasis[$id_siswa] ?? $dummyDesks;
            $absensi[$id_siswa] = $catatans[$id_siswa]  ?? ['nilai' => $dummyAbsen];

            $dummyFisik = [
                'kondisi'        => ['telinga' => '', 'mata' => '', 'gigi' => '', 'lain' => ''],
                'smt' . $id_smt  => ['tinggi' => '', 'berat' => '', 'tp' => $id_tp],
                'smt' . $other   => ['tinggi' => '', 'berat' => '', 'tp' => $id_tp],
            ];

            $nf  = $this->rapor->getFisikKelas($id_kelas, $id_siswa, $id_tp, $id_smt);
            $nf2 = $this->rapor->getFisikKelas($id_kelas, $id_siswa, $id_tp, $other);

            $fisik[$id_siswa] = $nf !== null
                ? [
                    'kondisi'        => unserialize($nf->kondisi ?? ''),
                    'smt' . $nf->id_smt => ['tinggi' => $nf->tinggi,  'berat' => $nf->berat],
                    'smt' . $other      => ['tinggi' => $nf2 ? $nf2->tinggi : '', 'berat' => $nf2 ? $nf2->berat : ''],
                ]
                : $dummyFisik;

            foreach ($ekstras as $ext) {
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra ?? '')));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    if ($id_ekstra === null) continue;

                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $id_siswa, $id_tp, $id_smt);
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne ?? ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                }
            }
        }

        $data['siswas']       = $siswas;
        $data['mapels']       = $mapels;
        $data['kelompoks']    = $kelompoks;
        $data['sikap']        = $sikap;
        $data['nilai']        = $nilai;
        $data['fisik']        = $fisik;
        $data['desks']        = $desks;
        $data['absensi']      = $absensi;
        $data['mapelEkstra']  = $mapelEkstra;
        $data['nilaiEkstra']  = $nilaiEkstra;
        $data['settingRapor'] = $settingRapor;
        $data['kelas']        = $kelas;
        $data['profile']      = $this->dashboard->getProfileAdmin($user->id);

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('rapor/bukurapor');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function editNilaiRapor()
    {
        $this->load->model([
            'Rapor_model'     => 'rapor',
            'Dashboard_model' => 'dashboard',
        ]);

        $user     = $this->ion_auth->user()->row();
        $setting  = $this->dashboard->getSetting();
        $id_siswa = $this->input->get('siswa', true);
        $id_tp    = $this->input->get('tp',    true);
        $id_smt   = $this->input->get('smt',   true);
        $mode     = $this->input->get('mode',  true);

        $data = [
            'user'       => $user,
            'judul'      => 'Buku Induk',
            'subjudul'   => 'Buku Induk',
            'setting'    => $setting,
            'tp_sel'     => $id_tp  ? $this->dashboard->getTahunById($id_tp)     : null,
            'smt_sel'    => $id_smt ? $this->dashboard->getSemesterById($id_smt) : null,
            'mode'       => $mode,
            'id_siswa'   => $id_siswa,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'siswa'      => $this->rapor->getDetailSiswaById($id_siswa, $id_tp, $id_smt),
        ];

        if ($mode === '1') {
            $data['sikap'] = $this->rapor->getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt);
        }

        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('rapor/editrapor');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function getDataKelas()
    {
        $this->load->model([
            'Dropdown_model'  => 'dropdown',
            'Master_model'    => 'master',
            'Dashboard_model' => 'dashboard',
        ]);

        $id_tp    = $this->input->get('tp',  true);
        $id_smt   = $this->input->get('smt', true);
        $id_kelas = $this->input->get('kls', true);
        $user     = $this->ion_auth->user()->row();

        $jabatan_guru = null;

        if ($this->ion_auth->is_admin()) {
            $kelass = $this->dropdown->getAllKelas($id_tp, $id_smt);
        } else {
            $guru         = $this->dashboard->getDataGuruByUserId($user->id, $id_tp, $id_smt);
            $kelass       = $this->dropdown->getAllKelasByArrayId($id_tp, $id_smt, [$id_kelas]);
            $jabatan_guru = $this->master->getAllJabatanGuru($guru->id_guru);
        }

        $this->output_json(['kelas' => $kelass, 'jabatan' => $jabatan_guru]);
    }

    public function backupNilai()
    {
        $this->load->model([
            'Rapor_model'     => 'rapor',
            'Dashboard_model' => 'dashboard',
            'Master_model'    => 'master',
        ]);

        $setting       = $this->dashboard->getSetting();
        $tps           = $this->dashboard->getTahun();
        $smts          = $this->dashboard->getSemester();
        $mapels        = $this->master->getAllMapel();
        $setting_rapor = $this->rapor->getAllRaporSetting();
        $kkms          = $this->rapor->getAllKkm();
        $nilai_rapor   = $this->rapor->getAllNilaiRapor();
        $nilai_extra   = $this->rapor->getAllNilaiEkstra();
        $nilai_sikap   = $this->rapor->getAllNilaiSikap();
        $rapor_fisik   = $this->rapor->getAllFisik();

        $all_nilai  = [];
        $nilai_hph  = [];
        $nilai_hpts = [];
        $nilai_hpas = [];
        $nilai_nr   = [];

        foreach ($nilai_rapor as $nilai) {
            $id_tp    = $nilai->id_tp;
            $id_smt   = $nilai->id_smt;
            $id_siswa = $nilai->id_siswa;
            $id_kelas = $nilai->id_kelas;
            $id_mapel = $nilai->id_mapel;

            $sr           = $setting_rapor[$id_tp][$id_smt];
            $kkm_tunggal  = $sr->kkm_tunggal == '1';
            $all_kkm      = $kkms[$id_tp][$id_smt][$id_kelas] ?? [];
            $kkm_mapel    = $all_kkm[1][$id_mapel] ?? null;
            $kkm_val      = $kkm_tunggal ? $sr->kkm : ($kkm_mapel->kkm ?? '');

            foreach ($mapels as $mapel) {
                if ($mapel->id_mapel != $id_mapel) continue;

                $nilai_hph[$id_siswa][]  = ['id_mapel' => $id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_val, 'p_nilai' => $nilai->p_rata_rata, 'p_pred' => $nilai->p_predikat, 'p_desk' => $nilai->p_deskripsi, 'k_nilai' => $nilai->k_rata_rata, 'k_pred' => $nilai->k_predikat, 'k_desk' => $nilai->k_deskripsi];
                $nilai_hpts[$id_siswa][] = ['id_mapel' => $id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_val, 'nilai' => $nilai->nilai_pts, 'pred' => $nilai->pts_predikat];
                $nilai_hpas[$id_siswa][] = ['id_mapel' => $id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_val, 'nilai' => $nilai->nilai_pas];
                $nilai_nr[$id_siswa][]   = ['id_mapel' => $id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_val, 'nilai' => $nilai->nilai_rapor, 'pred' => $nilai->rapor_predikat];
            }

            $nilai_ekstra = [];
            if (isset($nilai_extra[$id_tp][$id_smt][$id_siswa])) {
                foreach ($nilai_extra[$id_tp][$id_smt][$id_siswa] as $ekstra) {
                    $kkm_ekstra = $all_kkm[2][$ekstra->id_ekstra]->kkm ?? '';
                    $nilai_ekstra[$id_siswa][] = ['mapel' => $ekstra->kode_ekstra, 'id_ekstra' => $ekstra->id_ekstra, 'nama_ekstra' => $ekstra->nama_ekstra, 'kkm' => $kkm_tunggal ? $sr->kkm : $kkm_ekstra, 'nilai' => $ekstra->nilai, 'pred' => $ekstra->predikat, 'desk' => $ekstra->deskripsi];
                }
            }

            $spiritual  = $nilai_sikap[$id_tp][$id_smt][$id_siswa][1] ?? null;
            $sosial     = $nilai_sikap[$id_tp][$id_smt][$id_siswa][2] ?? null;
            $fisik_data = [];

            if (isset($rapor_fisik[$id_siswa][$id_tp][$id_smt])) {
                $fisik_data[] = $rapor_fisik[$id_siswa][$id_tp][$id_smt];
            }

            $all_nilai[$id_tp][$id_smt][$id_siswa] = [
                'uid'           => $nilai->uid,
                'id_siswa'      => $id_siswa,
                'tp'            => $nilai->tahun,
                'smt'           => $nilai->nama_smt,
                'kelas'         => $nilai->nama_kelas,
                'level'         => $nilai->level_id,
                'wali_kelas'    => $nilai->nama_guru,
                'jurusan'       => $nilai->nama_jurusan,
                'hph'           => serialize($nilai_hph[$id_siswa]  ?? []),
                'hpts'          => serialize($nilai_hpts[$id_siswa] ?? []),
                'hpas'          => serialize($nilai_hpas[$id_siswa] ?? []),
                'nilai_rapor'   => serialize($nilai_nr[$id_siswa]   ?? []),
                'ekstra'        => serialize($nilai_ekstra[$id_siswa] ?? ''),
                'spritual'      => $spiritual === null ? serialize([]) : serialize(['desk' => $spiritual->deskripsi, 'nilai' => unserialize($spiritual->nilai)['predikat']]),
                'sosial'        => $sosial    === null ? serialize([]) : serialize(['desk' => $sosial->deskripsi,    'nilai' => unserialize($sosial->nilai)['predikat']]),
                'rank'          => serialize(['rank' => $nilai->ranking, 'saran' => $nilai->rank_deskripsi]),
                'prestasi'      => serialize([['nilai' => $nilai->p1, 'desk' => $nilai->p1_desk], ['nilai' => $nilai->p2, 'desk' => $nilai->p2_desk], ['nilai' => $nilai->p3, 'desk' => $nilai->p3_desk]]),
                'absen'         => $nilai->absen ?? serialize([]),
                'saran'         => $nilai->saran ?? '-',
                'fisik'         => serialize($fisik_data),
                'naik'          => $nilai->naik  ?? '1',
                'setting_rapor' => serialize((array) $sr),
                'setting_mapel' => serialize((array) $mapels),
            ];
        }

        $insert    = [];
        $ids_siswa = [];

        foreach ($tps as $tp) {
            foreach ($smts as $smt) {
                if (!isset($all_nilai[$tp->id_tp][$smt->id_smt])) continue;
                foreach ($all_nilai[$tp->id_tp][$smt->id_smt] as $n) {
                    $ids_siswa[$n['id_siswa']] = $n['id_siswa'];
                    if (!$this->rapor->exists($n['uid'], $n['tp'], $n['smt'], $n['kelas'])) {
                        $insert[] = $n;
                    }
                }
            }
        }

        $this->db->trans_start();
        if (!empty($insert)) {
            $this->db->insert_batch('buku_nilai', $insert);
            $this->rapor->deleteNilaiRapor();
        }
        $this->db->trans_complete();

        $this->output_json(['all_nilai' => $all_nilai, 'insert' => $insert, 'ids' => $ids_siswa]);
    }

    public function restoreNilai()
    {
        $this->load->model([
            'Dashboard_model' => 'dashboard',
            'Rapor_model'     => 'rapor',
            'Kelas_model'     => 'kelas',
            'Master_model'    => 'master',
        ]);

        $tps    = $this->dashboard->getTahun();
        $smts   = $this->dashboard->getSemester();
        $mapels = $this->master->getAllMapel();
        $siswas = $this->rapor->getDataKumpulanRapor();
        $kelass = $this->kelas->getAllKelas();

        $hph = $hpts = $hpas = $nilai_rapor_arr = [];
        $ekstra = $spritual = $sosial = $rank = $prestasi = $absen = $fisik = [];

        foreach ($siswas as $id => $siswa) {
            $tp_idx  = array_search($siswa->tp,  array_column($tps,  'tahun'));
            $smt_idx = array_search($siswa->smt, array_column($smts, 'nama_smt'));
            $tp      = $tps[$tp_idx];
            $smt     = $smts[$smt_idx];

            $id_kelas = '';
            foreach ($kelass as $kelas) {
                if ($kelas->id_tp == $tp->id_tp && $kelas->id_smt == $smt->id_smt && $kelas->nama_kelas == $siswa->kelas) {
                    $id_kelas = $kelas->id_kelas;
                    break;
                }
            }

            $tp_id  = $tp->id_tp;
            $smt_id = $smt->id_smt;

            $hph[$tp_id][$smt_id][$id][$id_kelas]           = unserialize($siswa->hph);
            $hpts[$tp_id][$smt_id][$id][$id_kelas]          = unserialize($siswa->hpts);
            $hpas[$tp_id][$smt_id][$id][$id_kelas]          = unserialize($siswa->hpas);
            $nilai_rapor_arr[$tp_id][$smt_id][$id][$id_kelas] = unserialize($siswa->nilai_rapor);
            $ekstra[$tp_id][$smt_id][$id][$id_kelas]        = unserialize($siswa->ekstra);
            $spritual[$tp_id][$smt_id][$id][$id_kelas]      = unserialize($siswa->spritual);
            $sosial[$tp_id][$smt_id][$id][$id_kelas]        = unserialize($siswa->sosial);
            $rank[$tp_id][$smt_id][$id][$id_kelas]          = unserialize($siswa->rank);
            $prestasi[$tp_id][$smt_id][$id][$id_kelas]      = unserialize($siswa->prestasi);
            $absen[$tp_id][$smt_id][$id][$id_kelas]         = ['nilai' => $siswa->absen, 'deskripsi' => $siswa->saran];
            $fisik[$tp_id][$smt_id][$id][$id_kelas]         = unserialize($siswa->fisik);

            foreach ($fisik[$tp_id][$smt_id][$id][$id_kelas] as $f) {
                $f->kondisi = unserialize($f->kondisi);
            }
        }

        $hph_insert = $hpts_insert = $hpas_insert = [];
        $ekstra_insert = $spritual_insert = $sosial_insert = [];
        $rank_insert = $absen_insert = [];

        foreach ($tps as $tp) {
            foreach ($smts as $smt) {
                $tp_id  = $tp->id_tp;
                $smt_id = $smt->id_smt;

                if (isset($hph[$tp_id][$smt_id])) {
                    foreach ($hph[$tp_id][$smt_id] as $id => $phs) {
                        foreach ($phs as $kls => $nilai) {
                            foreach ($nilai as $ph) {
                                $p = (int) $ph['p_nilai'];
                                $k = (int) $ph['k_nilai'];
                                $hph_insert[] = ['id_nilai_harian' => $ph['id_mapel'] . $kls . $id . $tp_id . $smt_id, 'id_siswa' => $id, 'id_mapel' => $ph['id_mapel'], 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'p_rata_rata' => $p, 'p1' => $p + 1, 'p2' => $p - 1, 'p3' => $p, 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_predikat' => $ph['p_pred'], 'p_deskripsi' => $ph['p_desk'], 'k_rata_rata' => $k, 'k1' => $k + 1, 'k2' => $k - 1, 'k3' => $k, 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_predikat' => $ph['k_pred'], 'k_deskripsi' => $ph['k_desk'], 'jml' => ''];
                            }
                        }
                    }
                }

                if (isset($hpts[$tp_id][$smt_id])) {
                    foreach ($hpts[$tp_id][$smt_id] as $id => $pht) {
                        foreach ($pht as $kls => $nilai) {
                            foreach ($nilai as $ph) {
                                $hpts_insert[] = ['id_nilai_pts' => $ph['id_mapel'] . $kls . $id . $tp_id . $smt_id, 'id_siswa' => $id, 'id_mapel' => $ph['id_mapel'], 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'nilai' => $ph['nilai'], 'predikat' => $ph['pred']];
                            }
                        }
                    }
                }

                if (isset($hpas[$tp_id][$smt_id])) {
                    foreach ($hpas[$tp_id][$smt_id] as $id => $pha) {
                        foreach ($pha as $kls => $nilai) {
                            foreach ($nilai as $ph) {
                                $nr  = $nilai_rapor_arr[$tp_id][$smt_id][$id][$kls];
                                $idx = array_search($ph['id_mapel'], array_column($nr, 'id_mapel'));
                                $hnr = $nr[$idx];
                                $hpas_insert[] = ['id_nilai_akhir' => $ph['id_mapel'] . $kls . $id . $tp_id . $smt_id, 'id_siswa' => $id, 'id_mapel' => $ph['id_mapel'], 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'nilai' => $ph['nilai'], 'akhir' => $hnr['nilai'], 'predikat' => $hnr['pred']];
                            }
                        }
                    }
                }

                if (isset($ekstra[$tp_id][$smt_id])) {
                    foreach ($ekstra[$tp_id][$smt_id] as $id => $pha) {
                        foreach ($pha as $kls => $nilai) {
                            if (empty($nilai)) continue;
                            foreach ($nilai as $ph) {
                                $ekstra_insert[] = ['id_nilai_ekstra' => $ph['id_ekstra'] . $kls . $id . $tp_id . $smt_id, 'id_siswa' => $id, 'id_ekstra' => $ph['id_ekstra'], 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'nilai' => $ph['nilai'], 'predikat' => $ph['pred'], 'deskripsi' => $ph['desk']];
                            }
                        }
                    }
                }

                if (isset($spritual[$tp_id][$smt_id])) {
                    foreach ($spritual[$tp_id][$smt_id] as $id => $pht) {
                        foreach ($pht as $kls => $nilai) {
                            $spritual_insert[] = ['id_nilai_sikap' => $kls . $id . $tp_id . $smt_id . '1', 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'jenis' => '1', 'nilai' => serialize(['predikat' => $nilai['nilai'], 'sl1' => '', 'sl2' => '', 'sl3' => '', 'mb1' => '', 'mb2' => '', 'mb3' => '']), 'deskripsi' => $nilai['desk']];
                        }
                    }
                }

                if (isset($sosial[$tp_id][$smt_id])) {
                    foreach ($sosial[$tp_id][$smt_id] as $id => $pht) {
                        foreach ($pht as $kls => $nilai) {
                            $sosial_insert[] = ['id_nilai_sikap' => $kls . $id . $tp_id . $smt_id . '2', 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'jenis' => '2', 'nilai' => serialize(['predikat' => $nilai['nilai'], 'sl1' => '', 'sl2' => '', 'sl3' => '', 'mb1' => '', 'mb2' => '', 'mb3' => '']), 'deskripsi' => $nilai['desk']];
                        }
                    }
                }

                if (isset($rank[$tp_id][$smt_id])) {
                    foreach ($rank[$tp_id][$smt_id] as $id => $pht) {
                        foreach ($pht as $kls => $nilai) {
                            $prt = $prestasi[$tp_id][$smt_id][$id][$kls];
                            $rank_insert[] = ['id_ranking' => $kls . $id . $tp_id . $smt_id, 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'ranking' => $nilai['rank'], 'deskripsi' => $nilai['saran'], 'p1' => $prt[0]['nilai'], 'p1_desk' => $prt[0]['desk'], 'p2' => $prt[1]['nilai'], 'p2_desk' => $prt[1]['desk'], 'p3' => $prt[2]['nilai'], 'p3_desk' => $prt[2]['desk']];
                        }
                    }
                }

                if (isset($absen[$tp_id][$smt_id])) {
                    foreach ($absen[$tp_id][$smt_id] as $id => $pht) {
                        foreach ($pht as $kls => $nilai) {
                            $absen_insert[] = ['id_catatan_wali' => $kls . $id . $tp_id . $smt_id, 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp_id, 'id_smt' => $smt_id, 'nilai' => $nilai['nilai'], 'deskripsi' => $nilai['deskripsi']];
                        }
                    }
                }
            }
        }

        $this->db->trans_start();
        $res = 0;

        $batches = [
            'rapor_prestasi'     => $rank_insert,
            'rapor_catatan_wali' => $absen_insert,
            'rapor_nilai_ekstra' => $ekstra_insert,
            'rapor_nilai_akhir'  => $hpas_insert,
            'rapor_nilai_pts'    => $hpts_insert,
            'rapor_nilai_harian' => $hph_insert,
        ];

        foreach ($batches as $table => $rows) {
            if (!empty($rows)) {
                $res += $this->db->insert_batch($table, $rows);
            }
        }

        $sikap_all = array_merge($spritual_insert, $sosial_insert);
        if (!empty($sikap_all)) {
            $res += $this->db->insert_batch('rapor_nilai_sikap', $sikap_all);
        }

        if ($res) {
            $this->db->empty_table('buku_nilai');
        }

        $this->db->trans_complete();
        return $res;
    }

    public function edit()
    {
        $this->_renderRaporPage('edit', 'Nilai Rapor');
    }

    public function ledger()
    {
        $this->_renderRaporPage('ledger', 'Ledger');
    }

    public function dkn()
    {
        $this->_renderRaporPage('dkn', 'DKN');
    }

    private function _renderRaporPage(string $view, string $judul): void
    {
        $this->load->model([
            'Dashboard_model' => 'dashboard',
            'Rapor_model'     => 'rapor',
        ]);

        $kelas    = $this->input->get('kelas',    true);
        $tahun    = $this->input->get('tahun',    true);
        $semester = $this->input->get('semester', true);
        $user     = $this->ion_auth->user()->row();
        $setting  = $this->dashboard->getSetting();

        $data = [
            'user'       => $user,
            'judul'      => $judul,
            'subjudul'   => "Nilai Rapor Kelas $kelas, TP:$tahun, SMT:$semester",
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];

        $siswas = $this->rapor->getDataKumpulanRapor($kelas, $tahun, $semester);

        foreach ($siswas as $siswa) {
            $siswa->hph          = unserialize($siswa->hph);
            $siswa->hpts         = unserialize($siswa->hpts);
            $siswa->hpas         = unserialize($siswa->hpas);
            $siswa->nilai_rapor  = unserialize($siswa->nilai_rapor);
            $siswa->ekstra       = unserialize($siswa->ekstra);
            $siswa->spritual     = unserialize($siswa->spritual);
            $siswa->sosial       = unserialize($siswa->sosial);
            $siswa->rank         = unserialize($siswa->rank);
            $siswa->prestasi     = unserialize($siswa->prestasi);
            $siswa->absen        = unserialize($siswa->absen);
            $siswa->fisik        = unserialize($siswa->fisik);
            $siswa->setting_rapor = unserialize($siswa->setting_rapor);
            $siswa->setting_mapel = unserialize($siswa->setting_mapel);

            foreach ($siswa->fisik as $f) {
                $f->kondisi = unserialize($f->kondisi);
            }
        }

        $data['siswas']  = $siswas;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view("setting/$view");
        $this->load->view('_templates/dashboard/_footer');
    }
}
