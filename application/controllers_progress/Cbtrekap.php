<?php

class Cbtrekap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, $encode = true)
    {
        if (!$encode) {
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if ($subArray) {
                sort($subArray);
            }
        }
    }

    private function loadModels()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
    }

    public function index()
    {
        $this->loadModels();
        $user = $this->ion_auth->user()->row();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $koreksi = $this->cbt->getTotalKoreksi();

        $data = [
            'user'      => $user,
            'judul'     => 'Rekap Hasil Penilaian',
            'subjudul'  => 'Penilaian',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'jenis'     => $this->cbt->getDistinctJenisUjian(),
            'kelas'     => $this->cbt->getDistinctKelas(),
            'tahuns'    => $this->cbt->getDistinctTahun(),
            'semester'  => $this->cbt->getDistinctSmt(),
            'ruangs'    => $this->cbt->getAllRuang(),
            'sesis'     => $this->dropdown->getAllSesi(),
            'kelases'   => $this->cbt->getKelas(),
            'banks'     => $this->cbt->getAllBankSoal(),
            'koreksi'   => $koreksi,
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt);
            $rekapNilai = $this->cbt->getRekapJadwal();
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt, $guru->id_guru);
            $rekapNilai = $this->cbt->getRekapJadwal($guru->id_guru);
        }

        foreach ($data_jadwal as $rekap) {
            $terpakai = isset($jadwal_dikerjakan[$rekap->id_jadwal]) ? count($jadwal_dikerjakan[$rekap->id_jadwal]) : 0;
            $rekap->mengerjakan = $terpakai;
            $hanya_pg = $rekap->tampil_pg > 0
                && $rekap->tampil_kompleks == 0
                && $rekap->tampil_jodohkan == 0
                && $rekap->tampil_isian == 0
                && $rekap->tampil_esai == 0;
            $rekap->hanya_pg = $hanya_pg;
            $rekap->dikoreksi = !(!$hanya_pg && isset($koreksi[$rekap->id_jadwal]) && isset($koreksi[$rekap->id_jadwal][0]));
        }

        $data['rekaps'] = array_merge($data_jadwal, $rekapNilai);
        $data['ada_rekap'] = $this->ion_auth->is_admin()
            ? $this->cbt->getAllRekap()
            : $this->cbt->getAllRekap($guru->id_guru);

        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/rekap/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/rekap/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function perMapel()
    {
        $this->loadModels();
        $user = $this->ion_auth->user()->row();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data = [
            'user'      => $user,
            'judul'     => 'Hasil Siswa',
            'subjudul'  => 'Status Siswa',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'jenis'     => $this->cbt->getDistinctJenisUjian(),
            'kelas'     => $this->cbt->getDistinctKelas(),
            'tahun'     => $this->cbt->getDistinctTahun(),
            'semester'  => $this->cbt->getDistinctSmt(),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/rekap/permapel');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/rekap/permapel');
            $this->load->view('members/guru/templates/footer');
        }
    }

    private function buildRekapData($jadwal, $tp_obj, $smt_obj, $arrkelas, $nama_kelas)
    {
        $soals = $this->cbt->getNomorSoalByBank($jadwal->id_bank);
        $tahun = $tp_obj->tahun;
        $smt = $smt_obj->nama_smt;

        $pgb = [];
        foreach ($soals as $id => $soal) {
            if ($soal->jenis == '1') {
                $pgb[] = ['no_soal' => $id, 'jawab' => $soal->jawaban];
            }
        }

        return [
            'rekap' => [
                'id_tp'          => $tp_obj->id_tp,
                'tp'             => $tahun,
                'id_smt'         => $smt_obj->id_smt,
                'smt'            => $smt,
                'id_jadwal'      => $jadwal->id_jadwal,
                'id_jenis'       => $jadwal->id_jenis,
                'kode_jenis'     => $jadwal->kode_jenis,
                'id_bank'        => $jadwal->id_bank,
                'bank_kode'      => $jadwal->bank_kode,
                'bank_kelas'     => $jadwal->bank_kelas,
                'nama_kelas'     => serialize($nama_kelas),
                'bank_level'     => $jadwal->bank_level,
                'id_mapel'       => $jadwal->id_mapel,
                'nama_mapel'     => $jadwal->nama_mapel,
                'kode'           => $jadwal->kode,
                'tgl_mulai'      => $jadwal->tgl_mulai,
                'tgl_selesai'    => $jadwal->tgl_selesai,
                'tampil_pg'      => $jadwal->tampil_pg,
                'jawaban_pg'     => serialize($pgb),
                'bobot_pg'       => $jadwal->bobot_pg,
                'soal_kompleks'  => serialize(['tampil' => $jadwal->tampil_kompleks, 'bobot' => $jadwal->bobot_kompleks, 'jawaban' => []]),
                'soal_jodohkan'  => serialize(['tampil' => $jadwal->tampil_jodohkan, 'bobot' => $jadwal->bobot_jodohkan, 'jawaban' => []]),
                'soal_isian'     => serialize(['tampil' => $jadwal->tampil_isian, 'bobot' => $jadwal->bobot_isian, 'jawaban' => []]),
                'soal_essai'     => serialize(['tampil' => $jadwal->tampil_esai, 'bobot' => $jadwal->bobot_esai, 'jawaban' => []]),
                'id_guru'        => $jadwal->id_guru,
                'nama_guru'      => $jadwal->nama_guru,
            ],
            'pgb' => $pgb,
        ];
    }

    private function buildNilaiSiswa($jadwal, $tp_obj, $smt_obj, $arrkelas)
    {
        $tahun = $tp_obj->tahun;
        $smt = $smt_obj->nama_smt;
        $siswas = $this->cbt->getSiswaByKelasArray($tp_obj->id_tp, $smt_obj->id_smt, $arrkelas);
        $durasies = $this->cbt->getIdSiswaFromDurasiByJadwal($jadwal->id_jadwal);
        $jawabans = $this->cbt->getIdSiswaFromJawabanByJadwal($jadwal->id_jadwal);
        $nilais = $this->cbt->getAllNilaiSiswa($jadwal->id_jadwal);
        $nilai = [];
        foreach ($siswas as $siswa) {
            $skor_pg = $skor_pg2 = $skor_jod = $skor_is = $skor_es = 0;
            $benar_pg = 0;
            $pgs = $pg2s = $jods = $iss = $ess = [];
            if (isset($nilais[$siswa->id_siswa])) {
                $n = $nilais[$siswa->id_siswa];
                $benar_pg = $n->pg_benar;
                $skor_pg  = $n->pg_nilai;
                $skor_pg2 = $n->kompleks_nilai;
                $skor_jod = $n->jodohkan_nilai;
                $skor_is  = $n->isian_nilai;
                $skor_es  = $n->essai_nilai;
            }
            if (isset($jawabans[$siswa->id_siswa])) {
                foreach ($jawabans[$siswa->id_siswa] as $jawaban) {
                    if ($jawaban->jenis_soal == '1') {
                        $pgs[] = ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa];
                    }
                }
            }
            $nilai[] = [
                'id_jadwal'      => $jadwal->id_jadwal,
                'id_tp'          => $tp_obj->id_tp,
                'tp'             => $tahun,
                'id_smt'         => $smt_obj->id_smt,
                'smt'            => $smt,
                'id_jenis'       => $jadwal->id_jenis,
                'kode_jenis'     => $jadwal->kode_jenis,
                'id_bank'        => $jadwal->id_bank,
                'id_mapel'       => $jadwal->id_mapel,
                'id_siswa'       => $siswa->id_siswa,
                'nama_siswa'     => $siswa->nama,
                'no_peserta'     => $siswa->nomor_peserta,
                'id_kelas'       => $siswa->id_kelas,
                'kelas'          => $siswa->nama_kelas,
                'mulai'          => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->mulai : '',
                'selesai'        => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->selesai : '',
                'durasi'         => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->lama_ujian : '',
                'bobot_pg'       => $jadwal->bobot_pg,
                'jawaban_pg'     => serialize($pgs),
                'nilai_pg'       => round($skor_pg, 2),
                'soal_kompleks'  => serialize(['bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2s, 'nilai' => $skor_pg2]),
                'soal_jodohkan'  => serialize(['bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jods, 'nilai' => $skor_jod]),
                'soal_isian'     => serialize(['bobot' => $jadwal->bobot_isian, 'jawaban' => $iss, 'nilai' => $skor_is]),
                'soal_essai'     => serialize(['bobot' => $jadwal->bobot_esai, 'jawaban' => $ess, 'nilai' => $skor_es]),
                'id_guru'        => $jadwal->id_guru,
            ];
        }
        return $nilai;
    }

    private function getArrKelas($jadwal)
    {
        $kelass = unserialize($jadwal->bank_kelas ?? '');
        $arrkelas = [];
        foreach ($kelass as $kls) {
            if ($kls['kelas_id'] != null) {
                $arrkelas[] = $kls['kelas_id'];
            }
        }
        return $arrkelas;
    }

    public function backupNilai($id_jadwal)
    {
        $this->loadModels();
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $terpakai = isset($jadwal_dikerjakan[$id_jadwal]) && count($jadwal_dikerjakan[$id_jadwal]) > 0;
        $generated = $this->generateNilaiUjian($id_jadwal);

        if (!$terpakai || !$generated) {
            $save = isset($jadwal_dikerjakan[$id_jadwal]) ? count($jadwal_dikerjakan[$id_jadwal]) : 0;
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
            $this->output_json(true);
            return;
        }

        $this->db->trans_start();
        $jadwal = $this->cbt->getJadwalById($id_jadwal);
        $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
        $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
        $arrkelas = $this->getArrKelas($jadwal);
        $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
        $built = $this->buildRekapData($jadwal, $id_tp, $id_smt, $arrkelas, $nama_kelas);

        $this->db->where('id_jadwal', $id_jadwal)->delete('cbt_rekap');
        $result = $this->db->insert('cbt_rekap', $built['rekap']);
        $this->db->set('rekap', 1)->where('id_jadwal', $id_jadwal)->update('cbt_jadwal');

        $nilai = $this->buildNilaiSiswa($jadwal, $id_tp, $id_smt, $arrkelas);
        $this->db->where('id_jadwal', $id_jadwal)->delete('cbt_rekap_nilai');
        $this->load->model('Master_model', 'master');
        $save = $this->master->create('cbt_rekap_nilai', $nilai, true);
        $this->db->trans_complete();

        $this->session->set_flashdata(
            'rekapnilai',
            $result
                ? '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert">Berhasil merekap nilai.</div>'
                : '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>'
        );
        $this->output_json(true);
    }

    public function bulkBackup()
    {
        $this->loadModels();
        $ids = json_decode($this->input->post('ids', true));
        sleep(1);
        $this->db->trans_start();
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $jadwals = $this->cbt->getJadwalByArrId($ids);
        $result = false;
        $generated = 0;
        foreach ($jadwals as $jadwal) {
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0;
            if (!$terpakai) {
                continue;
            }
            if (!$this->generateNilaiUjian($jadwal->id_jadwal)) {
                continue;
            }
            $generated++;
            $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
            $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
            $arrkelas = $this->getArrKelas($jadwal);
            $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $built = $this->buildRekapData($jadwal, $id_tp, $id_smt, $arrkelas, $nama_kelas);

            $this->db->where('id_jadwal', $jadwal->id_jadwal)->delete('cbt_rekap');
            $result = $this->db->insert('cbt_rekap', $built['rekap']);
            $this->db->set('rekap', 1)->where('id_jadwal', $jadwal->id_jadwal)->update('cbt_jadwal');

            $nilai = $this->buildNilaiSiswa($jadwal, $id_tp, $id_smt, $arrkelas);
            $this->db->where('id_jadwal', $jadwal->id_jadwal)->delete('cbt_rekap_nilai');
            $this->master->create('cbt_rekap_nilai', $nilai, true);
        }
        $this->db->trans_complete();

        if ($generated > 0 && $result) {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert">Berhasil merekap <b>' . count($ids) . '</b> nilai</div>');
        } else {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Gagal merekap nilai. Beberapa siswa belum selesai atau belum dikoreksi.</div>');
        }
        $this->output_json(true);
    }

    public function hapusRekap()
    {
        $ids = json_decode($this->input->post('ids', true));
        sleep(1);
        $this->db->where_in('id_jadwal', $ids)->delete('cbt_rekap');
        $delRekap = $this->db->affected_rows() >= 0;
        $this->db->where_in('id_jadwal', $ids)->delete('cbt_rekap_nilai');
        $delNilai = $this->db->affected_rows() >= 0;

        if ($delNilai && $delRekap) {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert">Berhasil menghapus <b>' . count($ids) . '</b> nilai</div>');
        } else {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Hapus nilai gagal</div>');
        }
        $this->output_json(['success' => $delNilai && $delRekap, 'total' => count($ids)]);
    }

    public function getNilaiKelas()
    {
        $this->loadModels();
        $kelas = $this->input->get('kelas');
        $level = $this->master->getKelasById($kelas);
        $jenis = $this->input->get('jenis');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $mapel = $this->input->get('mapel');
        $user = $this->ion_auth->user()->row();

        if ($this->ion_auth->is_admin()) {
            $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel);
            $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel);
        } else {
            $tpg = $this->dashboard->getTahunByTahun($tahun);
            $smtg = $this->dashboard->getSemesterByNama($smt);
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tpg->id_tp, $smtg->id_smt);
            $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel, $guru->id_guru);
            $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel, $guru->id_guru);
        }

        foreach ($jadwals as $key => $jadwal) {
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
            $jadwal->jawaban_pg = unserialize($jadwal->jawaban_pg ?? '');
            $jadwal->jawaban_esai = unserialize($jadwal->jawaban_esai ?? '');
            $ids = array_column($jadwal->bank_kelas, 'kelas_id');
            if (!in_array($kelas, $ids)) {
                unset($jadwals[$key]);
            }
        }

        $arrSiswa = [];
        $arrNilai = [];
        foreach ($rekaps as $rekap) {
            $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
            $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
            $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
            $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
            $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
            $arrSiswa[$rekap->id_siswa] = ['id_siswa' => $rekap->id_siswa, 'nomor_peserta' => $rekap->nomor_peserta, 'nama' => $rekap->nama];
            $arrNilai[$rekap->id_siswa][$rekap->id_jadwal] = $rekap;
        }

        usort($arrSiswa, fn($a, $b) => $a['nama'] <=> $b['nama']);
        usort($jadwals, fn($a, $b) => $a->id_jadwal <=> $b->id_jadwal);

        $this->output_json(['siswa' => $arrSiswa, 'nilai' => $arrNilai, 'info' => array_values($jadwals)]);
    }

    public function olahNilai()
    {
        $this->loadModels();
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kelas = $this->input->get('kelas');
        $jadwal = $this->input->get('jadwal');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $rekap = $this->cbt->getRekapByJadwalKelas($jadwal);

        $data = [
            'user'           => $user,
            'judul'          => 'Ekspor Hasil Siswa',
            'subjudul'       => 'Ekspor Hasil Siswa',
            'setting'        => $this->dashboard->getSetting(),
            'tp'             => $this->dashboard->getTahun(),
            'tp_active'      => $tp,
            'smt'            => $this->dashboard->getSemester(),
            'smt_active'     => $smt,
            'kelas_selected' => $kelas,
            'jadwal_selected' => $jadwal,
            'guru'           => $guru,
            'jadwal'         => $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru),
        ];

        if ($rekap !== null) {
            $kls = @unserialize($rekap->nama_kelas);
            $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
            $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
            $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
            $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
            $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
            $data['rekap'] = $rekap;
            $data['kelas'] = $kls;
            $data['mapel'] = $rekap->id_mapel;
            $data['nama_kelas'] = $kelas === null ? 'Silahkan pilih kelas' : ($kls[$kelas] ?? '');
        }

        if ($kelas !== null && $rekap !== null) {
            $siswas = $this->cbt->getAllNilaiRekapByJenis($rekap->tp, $rekap->smt, $rekap->kode_jenis, $kelas, '0', $jadwal, $guru->id_guru);
            foreach ($siswas as $siswa) {
                $siswa->jawaban_pg = $this->unserialize_with_key($siswa->jawaban_pg);
                $siswa->soal_kompleks = json_decode(json_encode(unserialize($siswa->soal_kompleks)));
                $siswa->soal_jodohkan = json_decode(json_encode(unserialize($siswa->soal_jodohkan)));
                $siswa->soal_isian = json_decode(json_encode(unserialize($siswa->soal_isian)));
                $siswa->soal_essai = json_decode(json_encode(unserialize($siswa->soal_essai)));
            }
            $data['siswas'] = $siswas;
        }

        $ya = $this->input->get('ya');
        $data['convert'] = ['ya' => $ya, 'yb' => $this->input->get('yb'), 'xa' => $this->input->get('xa'), 'xb' => $this->input->get('xb')];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/rekap/olah');
        $this->load->view('members/guru/templates/footer');
    }

    public function export()
    {
        $this->loadModels();
        $user = $this->ion_auth->user()->row();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data = [
            'user'      => $user,
            'judul'     => 'Ekspor Hasil Penilaian',
            'subjudul'  => 'Ekspor Nilai',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'jenis'     => $this->cbt->getDistinctJenisUjian(),
            'kelas'     => $this->cbt->getDistinctKelas(),
            'tahuns'    => $this->cbt->getDistinctTahun(),
            'semester'  => $this->cbt->getDistinctSmt(),
        ];

        if ($this->ion_auth->is_admin()) {
            $jadwals = $this->cbt->getAllRekap();
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $jadwals = $this->cbt->getAllRekap($guru->id_guru);
            $data['guru'] = $guru;
        }

        foreach ($jadwals as $jadwal) {
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
            $jadwal->nama_kelas = unserialize($jadwal->nama_kelas ?? '');
        }
        $data['rekaps'] = $jadwals;

        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/rekap/ekspor');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/rekap/ekspor');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function getJenisPenilaian()
    {
        $this->load->model('Cbt_model', 'cbt');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $this->output_json($this->cbt->getJenisRekap($tahun, $smt));
    }

    function unserialize_with_key($serialized)
    {
        $arr = unserialize($serialized);
        $result = [];
        foreach ($arr as $value) {
            $result[$value['no_soal']] = $value['jawab'];
        }
        return $result;
    }

    public function generateNilaiUjian($jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $info = $this->cbt->getJadwalById($jadwal);
        $bagi_pg     = $info->tampil_pg / 100;
        $bobot_pg    = $info->bobot_pg / 100;
        $bagi_pg2    = $info->tampil_kompleks / 100;
        $bobot_pg2   = $info->bobot_kompleks / 100;
        $bagi_jodoh  = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian  = $info->tampil_isian / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $bagi_essai  = $info->tampil_esai / 100;
        $bobot_essai = $info->bobot_esai / 100;

        $kelas_bank = unserialize($info->bank_kelas ?? '');
        $kelases = array_column($kelas_bank, 'kelas_id');
        $siswas = $this->cbt->getSiswaByKelas($info->id_tp, $info->id_smt, $kelases);
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank);

        $jawabans_siswa = [];
        foreach ($jawabans as $jawaban_siswa) {
            if ($jawaban_siswa->jawaban_siswa === null) {
                continue;
            }
            if ($jawaban_siswa->jenis_soal == '2') {
                $jawaban_siswa->opsi_a          = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_siswa   = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar   = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban         = @unserialize($jawaban_siswa->jawaban ?? '');
                $jawaban_siswa->jawaban_benar   = array_filter(array_map('strtoupper', $jawaban_siswa->jawaban_benar ?? ['']), 'strlen');
                $jawaban_siswa->jawaban         = array_filter(array_map('strtoupper', $jawaban_siswa->jawaban ?? ['']), 'strlen');
            }
            if ($jawaban_siswa->jenis_soal == '3') {
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban       = @unserialize($jawaban_siswa->jawaban ?? '');
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
                $jawaban_siswa->jawaban       = json_decode(json_encode($jawaban_siswa->jawaban));
                $arrAlphabet = range('A', 'Z');

                if (isset($jawaban_siswa->jawaban_siswa->links)) {
                    $arrjwbnSiswa = [];
                    foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                        if ($idx > 0) {
                            $arrjwbnSiswa[$idx] = [];
                            foreach ($jbs as $idxs => $jb) {
                                if ($idxs > 0 && $jb === '1') {
                                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                                }
                            }
                        }
                    }
                    $jawaban_siswa->jawaban_siswa = json_decode(json_encode(['links' => $arrjwbnSiswa]));
                }

                $arrjwbn = [];
                foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                    if ($idx > 0) {
                        $arrjwbn[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if ($idxs > 0 && $jb === '1') {
                                $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                            }
                        }
                    }
                }
                $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            }
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
        }

        $insets = [];
        foreach ($siswas as $siswa) {
            $ada_jawaban       = isset($jawabans_siswa[$siswa->id_siswa]);
            $ada_jawaban_pg    = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            $ada_jawaban_pg2   = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);

            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
            if ($nilai_input === null || $nilai_input->dikoreksi != '1') {
                continue;
            }

            $benar_pg = 0;
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            if ($info->tampil_pg > 0 && count($jawaban_pg) > 0) {
                foreach ($jawaban_pg as $jwb_pg) {
                    if ($jwb_pg !== null && $jwb_pg->jawaban_siswa !== null) {
                        if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                            $benar_pg++;
                        }
                    }
                }
            }
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;

            $benar_pg2 = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            if ($info->tampil_kompleks > 0 && count($jawaban_pg2) > 0) {
                foreach ($jawaban_pg2 as $jawab_pg2) {
                    $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                    $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                    $arr_benar = array_intersect((array) $jawab_pg2->jawaban_siswa, (array) $jawab_pg2->jawaban);
                    $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
                }
            }
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $input_pg2 = ($nilai_input !== null && $nilai_input->kompleks_nilai !== null) ? $nilai_input->kompleks_nilai : 0;
            $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);

            $benar_jod = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod = 0;
            $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
            if ($info->tampil_jodohkan > 0 && count($jawaban_jodoh) > 0) {
                foreach ($jawaban_jodoh as $jawab_jod) {
                    $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                    $items = 0;
                    $item_benar = 0;
                    $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                    if (isset($jawab_jod->jawaban_siswa->links)) {
                        $array1 = (array) $jawab_jod->jawaban_benar->links;
                        $this->sortArrays($array1);
                        $array2 = (array) $jawab_jod->jawaban_siswa->links;
                        $this->sortArrays($array2);
                        foreach ($array1 as $key => $subArray1) {
                            $items += count($subArray1);
                            if (isset($array2[$key])) {
                                $item_benar += count(array_intersect($subArray1, $array2[$key]));
                            }
                        }
                    }
                    $benar_jod += $items > 0 ? 1 / $items * $item_benar : 0;
                    $otomatis_jod = $jawab_jod->nilai_otomatis;
                }
            }
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $input_jod = ($nilai_input !== null && $nilai_input->jodohkan_nilai !== null) ? $nilai_input->jodohkan_nilai : 0;
            $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);

            $benar_is = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is = 0;
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            if ($info->tampil_isian > 0 && count($jawaban_is) > 0) {
                foreach ($jawaban_is as $jawab_is) {
                    $skor_koreksi_is += $jawab_is->nilai_koreksi;
                    $otomatis_is = $jawab_is->nilai_otomatis;
                    if ($jawab_is !== null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '')) {
                        $benar_is++;
                    }
                }
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $input_is = ($nilai_input !== null && $nilai_input->isian_nilai !== null) ? $nilai_input->isian_nilai : 0;
            $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);

            $benar_es = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es = 0;
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            if ($info->tampil_esai > 0 && count($jawaban_es) > 0) {
                foreach ($jawaban_es as $jawab_es) {
                    $skor_koreksi_es += $jawab_es->nilai_koreksi;
                    $otomatis_es = $jawab_es->nilai_otomatis;
                    if ($jawab_es !== null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '')) {
                        $benar_es++;
                    }
                }
            }
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $input_es = ($nilai_input !== null && $nilai_input->essai_nilai !== null) ? $nilai_input->essai_nilai : 0;
            $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);

            $insets[] = [
                'id_nilai'       => $siswa->id_siswa . '0' . $jadwal,
                'id_siswa'       => $siswa->id_siswa,
                'id_jadwal'      => $jadwal,
                'pg_benar'       => $benar_pg,
                'pg_nilai'       => round($skor_pg, 2),
                'kompleks_nilai' => round($skor_pg2, 2),
                'jodohkan_nilai' => round($skor_jod, 2),
                'isian_nilai'    => round($skor_is, 2),
                'essai_nilai'    => round($skor_es, 2),
            ];
        }

        if (count($insets) > 0) {
            $this->db->update_batch('cbt_nilai', $insets, 'id_nilai');
            return true;
        }
        return false;
    }
}
