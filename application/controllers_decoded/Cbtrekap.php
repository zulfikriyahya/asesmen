<?php

class Cbtrekap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            }
            sort($subArray);
        }
    }
    public function index()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Rekap Hasil Penilaian', 'subjudul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tahuns'] = $this->cbt->getDistinctTahun();
        $data['semester'] = $this->cbt->getDistinctSmt();
        $data['ruangs'] = $this->cbt->getAllRuang();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['kelases'] = $this->cbt->getKelas();
        $data['banks'] = $this->cbt->getAllBankSoal();
        $koreksi = $this->cbt->getTotalKoreksi();
        $data['koreksi'] = $koreksi;
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $rekapNilai = $this->cbt->getRekapJadwal($guru->id_guru);
        foreach ($data_jadwal as $rekap) {
            $terpakai = isset($jadwal_dikerjakan[$rekap->id_jadwal]) ? count($jadwal_dikerjakan[$rekap->id_jadwal]) : 0;
            $rekap->mengerjakan = $terpakai;
            $hanya_pg = $rekap->tampil_pg > 0 && $rekap->tampil_kompleks == 0 && $rekap->tampil_jodohkan == 0 && $rekap->tampil_isian == 0 && $rekap->tampil_esai == 0;
            $rekap->hanya_pg = $hanya_pg;
            if (!$hanya_pg && isset($koreksi[$rekap->id_jadwal]) && isset($koreksi[$rekap->id_jadwal][0])) {
            }
            $rekap->dikoreksi = true;
        }
        $rekapJadwal = $data_jadwal;
        $rekaps = array_merge($rekapJadwal, $rekapNilai);
        $data['rekaps'] = $rekaps;
        $data['ada_rekap'] = $this->cbt->getAllRekap($guru->id_guru);
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/rekap/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function perMapel()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Hasil Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tahun'] = $this->cbt->getDistinctTahun();
        $data['semester'] = $this->cbt->getDistinctSmt();
        if ($this->ion_auth->is_admin()) {
        }
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/rekap/permapel');
        $this->load->view('members/guru/templates/footer');
    }
    public function backupNilai($id_jadwal)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $result = false;
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $terpakai = isset($jadwal_dikerjakan[$id_jadwal]) && count($jadwal_dikerjakan[$id_jadwal]) > 0;
        $generated = $this->generateNilaiUjian($id_jadwal);
        if ($terpakai && $generated) {
        }
        $result = false;
        $save = isset($jadwal_dikerjakan[$id_jadwal]) ? count($jadwal_dikerjakan[$id_jadwal]) : 0;
        if ($generated && $result) {
        }
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
        $this->output_json(true);
    }
    public function bulkBackup()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $ids = json_decode($this->input->post('ids', true));
        sleep(1);
        $data['total'] = count($ids);
        $this->db->trans_start();
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $jadwals = $this->cbt->getJadwalByArrId($ids);
        $data['jadwal'] = $jadwals;
        $result = false;
        $save = false;
        $generated = 0;
        foreach ($jadwals as $jadwal) {
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0;
            if (!$terpakai) {
            }
            $gen = $this->generateNilaiUjian($jadwal->id_jadwal);
            if (!$gen) {
            }
            $generated++;
            $soals = $this->cbt->getNomorSoalByBank($jadwal->id_bank);
            $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
            $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
            $tahun = $id_tp->tahun;
            $smt = $id_smt->nama_smt;
            $kelass = unserialize($jadwal->bank_kelas ?? '');
            $arrkelas = [];
            foreach ($kelass as $kls) {
                if (!($kls['kelas_id'] != null)) {
                }
                array_push($arrkelas, $kls['kelas_id']);
            }
            $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $pgb = [];
            $pg2b = [];
            $jodb = [];
            $isb = [];
            $esb = [];
            foreach ($soals as $id => $soal) {
                if ($soal->jenis == '1') {
                }
                if ($soal->jenis == '2') {
                }
                if ($soal->jenis == '3') {
                }
                if ($soal->jenis == '4') {
                }
                if ($soal->jenis == '5') {
                }
            }
            $soal_kompleks = ['tampil' => $jadwal->tampil_kompleks, 'bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2b];
            $soal_jodohkan = ['tampil' => $jadwal->tampil_jodohkan, 'bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jodb];
            $soal_isian = ['tampil' => $jadwal->tampil_isian, 'bobot' => $jadwal->bobot_isian, 'jawaban' => $isb];
            $soal_essai = ['tampil' => $jadwal->tampil_esai, 'bobot' => $jadwal->bobot_esai, 'jawaban' => $esb];
            $this->db->where('id_jadwal', $jadwal->id_jadwal);
            $this->db->delete('cbt_rekap');
            $insert = ['id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jadwal' => $jadwal->id_jadwal, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'bank_kode' => $jadwal->bank_kode, 'bank_kelas' => $jadwal->bank_kelas, 'nama_kelas' => serialize($nama_kelas), 'bank_level' => $jadwal->bank_level, 'id_mapel' => $jadwal->id_mapel, 'nama_mapel' => $jadwal->nama_mapel, 'kode' => $jadwal->kode, 'tgl_mulai' => $jadwal->tgl_mulai, 'tgl_selesai' => $jadwal->tgl_selesai, 'tampil_pg' => $jadwal->tampil_pg, 'jawaban_pg' => serialize($pgb), 'bobot_pg' => $jadwal->bobot_pg, 'soal_kompleks' => serialize($soal_kompleks), 'soal_jodohkan' => serialize($soal_jodohkan), 'soal_isian' => serialize($soal_isian), 'soal_essai' => serialize($soal_essai), 'id_guru' => $jadwal->id_guru, 'nama_guru' => $jadwal->nama_guru];
            $result = $this->db->insert('cbt_rekap', $insert);
            if (!$result) {
            }
            $this->db->set('rekap', 1);
            $this->db->where('id_jadwal', $jadwal->id_jadwal);
            $this->db->update('cbt_jadwal');
            $siswas = $this->cbt->getSiswaByKelasArray($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $arrSiswa = [];
            foreach ($siswas as $siswa) {
                array_push($arrSiswa, $siswa->id_siswa);
            }
            $durasies = $this->cbt->getIdSiswaFromDurasiByJadwal($jadwal->id_jadwal);
            $jawabans = $this->cbt->getIdSiswaFromJawabanByJadwal($jadwal->id_jadwal);
            $nilais = $this->cbt->getAllNilaiSiswa($jadwal->id_jadwal);
            $nilai = [];
            foreach ($siswas as $siswa) {
                $dikoreksi = [];
                $benar_pg = 0;
                $salah_pg = 0;
                $skor_pg = 0;
                $skor_pg2 = 0;
                $skor_jod = 0;
                $skor_is = 0;
                $skor_es = 0;
                if (!isset($nilais[$siswa->id_siswa])) {
                }
                array_push($dikoreksi, $nilais[$siswa->id_siswa]->dikoreksi);
                $benar_pg = $nilais[$siswa->id_siswa]->pg_benar;
                $salah_pg = $jadwal->tampil_pg - $benar_pg;
                $skor_pg = $nilais[$siswa->id_siswa]->pg_nilai;
                $skor_pg2 = $nilais[$siswa->id_siswa]->kompleks_nilai;
                $skor_jod = $nilais[$siswa->id_siswa]->jodohkan_nilai;
                $skor_is = $nilais[$siswa->id_siswa]->isian_nilai;
                $skor_es = $nilais[$siswa->id_siswa]->essai_nilai;
                $pgs = [];
                $pg2s = [];
                $jods = [];
                $iss = [];
                $ess = [];
                if (!isset($jawabans[$siswa->id_siswa])) {
                }
                foreach ($jawabans[$siswa->id_siswa] as $jawaban) {
                    if ($jawaban->jenis_soal == '1') {
                    }
                    if ($jawaban->jenis_soal == '2') {
                    }
                    if ($jawaban->jenis_soal == '3') {
                    }
                    if ($jawaban->jenis_soal == '4') {
                    }
                    if ($jawaban->jenis_soal == '5') {
                    }
                }
                $soal_pg2 = ['bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2s, 'nilai' => $skor_pg2];
                $soal_jod = ['bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jods, 'nilai' => $skor_jod];
                $soal_is = ['bobot' => $jadwal->bobot_isian, 'jawaban' => $iss, 'nilai' => $skor_is];
                $soal_es = ['bobot' => $jadwal->bobot_esai, 'jawaban' => $ess, 'nilai' => $skor_es];
                $nilai[] = ['id_jadwal' => $jadwal->id_jadwal, 'id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'id_mapel' => $jadwal->id_mapel, 'id_siswa' => $siswa->id_siswa, 'nama_siswa' => $siswa->nama, 'no_peserta' => $siswa->nomor_peserta, 'id_kelas' => $siswa->id_kelas, 'kelas' => $siswa->nama_kelas, 'mulai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->mulai : '', 'selesai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->selesai : '', 'durasi' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->lama_ujian : '', 'bobot_pg' => $jadwal->bobot_pg, 'jawaban_pg' => serialize($pgs), 'nilai_pg' => round($skor_pg, 2), 'soal_kompleks' => serialize($soal_pg2), 'soal_jodohkan' => serialize($soal_jod), 'soal_isian' => serialize($soal_is), 'soal_essai' => serialize($soal_es), 'id_guru' => $jadwal->id_guru];
            }
            $this->db->where('id_jadwal', $jadwal->id_jadwal);
            $this->db->delete('cbt_rekap_nilai');
            $save = $this->master->create('cbt_rekap_nilai', $nilai, true);
        }
        $this->db->trans_complete();
        $sukses = $generated > 0 && $result;
        if ($generated > 0 && $result) {
        }
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
        $this->output_json(true);
    }
    public function hapusRekap()
    {
        $ids = json_decode($this->input->post('ids', true));
        sleep(1);
        $data['total'] = count($ids);
        $this->db->where_in('id_jadwal', $ids);
        $delRekap = $this->db->delete('cbt_rekap');
        $this->db->where_in('id_jadwal', $ids);
        $delNilai = $this->db->delete('cbt_rekap_nilai');
        if ($delNilai && $delRekap) {
        }
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert"> Hapus nilai gagal </div>');
        $data['success'] = $delNilai && $delRekap;
        $this->output_json($data);
    }
    function getDataFromArray1ByUserId($array, $userId)
    {
        foreach ($array as $key => $data) {
            if (!($data->id_siswa == $userId)) {
            }
            return $array;
        }
        return array();
    }
    public function getJenisPenilaian()
    {
        $this->load->model('Cbt_model', 'cbt');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $jadwals = $this->cbt->getJenisRekap($tahun, $smt);
    }
    public function getNilaiKelas()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $kelas = $this->input->get('kelas');
        $level = $this->master->getKelasById($kelas);
        $jenis = $this->input->get('jenis');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $mapel = $this->input->get('mapel');
        $user = $this->ion_auth->user()->row();
        if ($this->ion_auth->is_admin()) {
        }
        $tpg = $this->dashboard->getTahunByTahun($tahun);
        $smtg = $this->dashboard->getSemesterByNama($smt);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tpg->id_tp, $smtg->id_smt);
        $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel, $guru->id_guru);
        foreach ($jadwals as $key => $jadwal) {
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
            $jadwal->jawaban_pg = unserialize($jadwal->jawaban_pg);
            $jadwal->jawaban_esai = unserialize($jadwal->jawaban_esai);
            $ids = [];
            foreach ($jadwal->bank_kelas as $id) {
                array_push($ids, $id['kelas_id']);
            }
            if (in_array($kelas, $ids)) {
            }
            unset($jadwals[$key]);
        }
        $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel, $guru->id_guru);
        $arrSiswa = [];
        if (!(count($rekaps) > 0)) {
        }
        foreach ($rekaps as $rekap) {
            $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
            $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
            $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
            $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
            $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
            $arrSiswa[$rekap->id_siswa] = ['id_siswa' => $rekap->id_siswa, 'nomor_peserta' => $rekap->nomor_peserta, 'nama' => $rekap->nama];
        }
        usort($arrSiswa, function ($a, $b) {
            return $a['nama'] <=> $b['nama'];
        });
        $arrNilai = [];
        foreach ($rekaps as $key => $item) {
            $arrNilai[$item->id_siswa][$item->id_jadwal] = $item;
        }
        usort($jadwals, function ($a, $b) {
            return $a->id_jadwal <=> $b->id_jadwal;
        });
        $data['siswa'] = $arrSiswa;
        $data['nilai'] = $arrNilai;
        $data['info'] = array_values($jadwals);
        $this->output_json($data);
    }
    public function olahNilai()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $kelas = $this->input->get('kelas');
        $level = $this->master->getKelasById($kelas);
        $jadwal = $this->input->get('jadwal');
        $user = $this->ion_auth->user()->row();
        $rekap = $this->cbt->getRekapByJadwalKelas($jadwal);
        $data = ['user' => $user, 'judul' => 'Ekspor Hasil Siswa', 'subjudul' => 'Ekspor Hasil Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas_selected'] = $kelas;
        $data['jadwal_selected'] = $jadwal;
        $kls = @unserialize($rekap->nama_kelas);
        $data['kelas'] = $kls;
        if (!($rekap != null)) {
        }
        $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
        $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
        $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
        $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
        $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
        $data['rekap'] = $rekap;
        $data['mapel'] = $rekap->id_mapel;
        $data['nama_kelas'] = $kelas == null ? 'Silahkan pilih kelas' : $kls[$kelas];
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        if (!($kelas != null)) {
        }
        $siswas = $this->cbt->getAllNilaiRekapByJenis($rekap->tp, $rekap->smt, $rekap->kode_jenis, $kelas, '0', $jadwal, $guru->id_guru);
        foreach ($siswas as $siswa) {
            $siswa->jawaban_pg = $this->unserialize_with_key($siswa->jawaban_pg);
            $siswa->soal_kompleks = json_decode(json_encode(unserialize($siswa->soal_kompleks)));
            $siswa->soal_jodohkan = json_decode(json_encode(unserialize($siswa->soal_jodohkan)));
            $siswa->soal_isian = json_decode(json_encode(unserialize($siswa->soal_isian)));
            $siswa->soal_essai = json_decode(json_encode(unserialize($siswa->soal_essai)));
        }
        $data['siswas'] = $siswas;
        $ya = $this->input->get('ya');
        $yb = $this->input->get('yb');
        $xa = $this->input->get('xa');
        $xb = $this->input->get('xb');
        if (!($ya != null)) {
        }
        $convert = ['ya' => $ya, 'yb' => $yb, 'xa' => $xa, 'xb' => $xb];
        $data['convert'] = $convert;
        $data['jadwal'] = $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/rekap/olah');
        $this->load->view('members/guru/templates/footer');
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
    public function export()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Ekspor Hasil Penilaian', 'subjudul' => 'Ekspor Nilai', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tahuns'] = $this->cbt->getDistinctTahun();
        $data['semester'] = $this->cbt->getDistinctSmt();
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $jadwals = $this->cbt->getAllRekap($guru->id_guru);
        foreach ($jadwals as $key => $jadwal) {
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
            $jadwal->nama_kelas = unserialize($jadwal->nama_kelas ?? '');
        }
        $data['rekaps'] = $jadwals;
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/rekap/ekspor');
        $this->load->view('members/guru/templates/footer');
    }
    public function generateNilaiUjian($jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $info = $this->cbt->getJadwalById($jadwal);
        $bagi_pg = $info->tampil_pg / 100;
        $bobot_pg = $info->bobot_pg / 100;
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian = $info->tampil_isian / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_essai = $info->bobot_esai / 100;
        $kelas_bank = unserialize($info->bank_kelas ?? '');
        $kelases = [];
        foreach ($kelas_bank as $key => $value) {
            array_push($kelases, $value['kelas_id']);
        }
        $siswas = $this->cbt->getSiswaByKelas($info->id_tp, $info->id_smt, $kelases);
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank);
        $soal = [];
        $jawabans_siswa = [];
        foreach ($jawabans as $jawaban_siswa) {
            if (!($jawaban_siswa->jawaban_siswa != null)) {
            }
            if (!($jawaban_siswa->jenis_soal == '2')) {
            }
            $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $jawaban_siswa->jawaban_benar = array_map('strtoupper', $jawaban_siswa->jawaban_benar ?? ['']);
            $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar ?? [''], 'strlen');
            $jawaban_siswa->jawaban = array_map('strtoupper', $jawaban_siswa->jawaban ?? ['']);
            $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban ?? [''], 'strlen');
            if (!($jawaban_siswa->jenis_soal == '3')) {
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
            $arrAlphabet = range('A', 'Z');
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            $arrjwbnSiswa = [];
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                }
                $arrjwbnSiswa[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                }
            }
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $arrjwbn = [];
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                }
                $arrjwbn[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                }
            }
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
        }
        $insets = [];
        foreach ($siswas as $siswa) {
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
            if (!($nilai_input != null && $nilai_input->dikoreksi == '1')) {
            }
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            $benar_pg = 0;
            $salah_pg = 0;
            if (!($info->tampil_pg > 0)) {
            }
            if (!(count($jawaban_pg) > 0)) {
            }
            foreach ($jawaban_pg as $jwb_pg) {
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                }
                $salah_pg += 1;
            }
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            $benar_pg2 = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!(count($jawaban_pg2) > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $arr_benar = [];
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    if (!in_array($js, $jawab_pg2->jawaban)) {
                    }
                    array_push($arr_benar, true);
                }
                $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
            }
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $input_pg2 = 0;
            if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
            }
            $input_pg2 = $nilai_input->kompleks_nilai;
            $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
            $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
            $benar_jod = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod = 0;
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $item_benar = 0;
                $item_salah = 0;
                $item_kurang = 0;
                $items = 0;
                $arrBenar = [];
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                }
                $array1 = (array) $jawab_jod->jawaban_benar->links;
                $this->sortArrays($array1);
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $this->sortArrays($array2);
                $sameCount = 0;
                $differentCount = 0;
                foreach ($array1 as $key => $subArray1) {
                    $arrBenar[$key] = new stdClass();
                    $arrBenar[$key]->benar = 0;
                    $arrBenar[$key]->salah = 0;
                    $arrBenar[$key]->kurang = 0;
                    $items += count($subArray1);
                    if (isset($array2[$key])) {
                    }
                    $arrBenar[$key]->kurang += count($subArray1);
                }
                $point_soal = 1 / $items * $item_benar * $point_benar;
                $benar_jod += 1 / $items * $item_benar;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
            }
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $input_jod = 0;
            if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
            }
            $input_jod = $nilai_input->jodohkan_nilai;
            $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            $benar_is = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is = 0;
            if (!($info->tampil_isian > 0)) {
            }
            if (!(count($jawaban_is) > 0)) {
            }
            foreach ($jawaban_is as $num => $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
                if (!$benar) {
                }
                $benar_is++;
                $otomatis_is = $jawab_is->nilai_otomatis;
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $input_is = 0;
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $input_is = $nilai_input->isian_nilai;
            $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            $benar_es = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es = 0;
            if (!($info->tampil_esai > 0)) {
            }
            if (!(count($jawaban_es) > 0)) {
            }
            foreach ($jawaban_es as $num => $jawab_es) {
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
                if (!$benar) {
                }
                $benar_es++;
                $otomatis_es = $jawab_es->nilai_otomatis;
            }
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $input_es = 0;
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $input_es = $nilai_input->essai_nilai;
            $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
            $insert['id_nilai'] = $siswa->id_siswa . '0' . $jadwal;
            $insert['id_siswa'] = $siswa->id_siswa;
            $insert['id_jadwal'] = $jadwal;
            $insert['pg_benar'] = $benar_pg;
            $insert['pg_nilai'] = round($skor_pg, 2);
            $insert['kompleks_nilai'] = round($skor_pg2, 2);
            $insert['jodohkan_nilai'] = round($skor_jod, 2);
            $insert['isian_nilai'] = round($skor_is, 2);
            $insert['essai_nilai'] = round($skor_es, 2);
            array_push($insets, $insert);
        }
        if (count($insets) > 0) {
            $this->db->update_batch('cbt_nilai', $insets, 'id_nilai');
            $update = true;
            return $update;
        } else {
            $update = false;
            return $update;
        }
    }
}