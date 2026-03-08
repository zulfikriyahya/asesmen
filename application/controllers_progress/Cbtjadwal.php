<?php

class Cbtjadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
    }
    public function index()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $lvl = $this->input->get('level', true);
        $level = $lvl == null ? '0' : $lvl;
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Jadwal Penilaian', 'subjudul' => 'PH/PTS/PAT/USBK', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $mode = $this->input->get('mode');
        $type = $this->input->get('type');
        $data['mode'] = $mode == null ? '1' : $mode;
        $data['ruangs'] = $this->cbt->getAllRuang();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $data['jadwal'] = json_decode(json_encode($this->cbt->dummyJadwal()));
        $data['jmlIst'] = [];
        $data['jmlMapel'] = [];
        $data['level'] = $level;
        if (!$mode) {
            $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        } else {
            $terpakai = $this->cbt->getJadwalTerpakai();
            $jadwal_terpakai = [];
            foreach ($terpakai as $idj => $rows) {
                $jadwal_terpakai[$idj] = count($rows);
            }
            $data['total_siswa'] = $jadwal_terpakai;
            $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        }
        $data['levels'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel = [];
        if (!$mapel) {
        }
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $data['mapels'] = $arrMapel;
        $data['filters'] = ['0' => 'Semua', '2' => 'Mapel', '3' => 'Level'];
        $data['id_filter'] = $type == null ? '' : $type;
        if ($type == '0') {
        }
        if ($type == '2') {
        }
        if ($type == '3') {
        }
        $data['id_guru'] = null;
        $data['id_mapel'] = null;
        $data['id_level'] = null;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/jadwal/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function add($id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $enable = $this->input->get('enable', true);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => $id_jadwal == 0 ? 'Tambah Jadwal Ujian' : 'Edit Jadwal Ujian', 'subjudul' => 'Jadwal', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($id_jadwal == 0) {
            $data['jadwal'] = json_decode(json_encode($this->cbt->dummyJadwal()));
        } else {
            $data['jadwal'] = $this->cbt->getJadwalById($id_jadwal);
        }
        $gurus = $this->dropdown->getAllGuru();
        $data['ruangs'] = $this->cbt->getAllRuang();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $data['disable_opsi'] = $enable != null && $enable == 1;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $arrMapel = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $data['mapel'] = $arrMapel;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/jadwal/add');
        $this->load->view('members/guru/templates/footer');
    }
    public function getBankMapel($id_mapel)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $banks = $this->cbt->getAllBankSoalByMapel($tp->id_tp, $smt->id_smt, $id_mapel);
        $filtered = [];
        foreach ($banks as $key => $bank) {
            $cek_soal = $this->cbt->getJumlahJenisSoal($key);
            $num1 = isset($cek_soal['1']) ? count($cek_soal['1']) : 0;
            $num2 = isset($cek_soal['2']) ? count($cek_soal['2']) : 0;
            $num3 = isset($cek_soal['3']) ? count($cek_soal['3']) : 0;
            $num4 = isset($cek_soal['4']) ? count($cek_soal['4']) : 0;
            $num5 = isset($cek_soal['5']) ? count($cek_soal['5']) : 0;
            $ada1 = $num1 == (int) $bank->tampil_pg;
            $ada2 = $num2 == (int) $bank->tampil_kompleks;
            $ada3 = $num3 == (int) $bank->tampil_jodohkan;
            $ada4 = $num4 == (int) $bank->tampil_isian;
            $ada5 = $num5 == (int) $bank->tampil_esai;
            if (!($ada1 && $ada2 && $ada3 && $ada4 && $ada5)) {
            } else {
                $filtered[$key] = $bank->bank_kode;
            }
        }
        $this->output_json($filtered);
    }
    public function saveJadwal()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        if ($this->input->post()) {
            $res = $this->cbt->saveJadwalUjian($tp->id_tp, $smt->id_smt);
            $data['message'] = $res ? 'Jadwal berhasil disimpan' : 'Jadwal sudah ada';
            $status = $res;
        } else {
            $data['message'] = 'Kesalahan 404';
            $status = FALSE;
        }
        $data['success'] = $status;
        $id = $this->input->post('id_jadwal', true);
        if (!$id) {
        }
        $this->logging->saveLog(4, 'mengedit jadwal pelajaran');
        $this->output_json($data);
    }
    public function deleteJadwal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $id = $this->input->get('id_jadwal', true);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $terpakai = isset($jadwal_dikerjakan[$id]) && count($jadwal_dikerjakan[$id]) > 0;
        $data['status'] = false;
        $jadwal = $this->cbt->getJadwalById($id);
        if ($terpakai && $jadwal->rekap == 0) {
            $data['status'] = false;
            $data['message'] = 'Hasil Ujian belum direkap';
        } else {
            if ($this->master->delete('cbt_jadwal', $id, 'id_jadwal')) {
            }
            $data['status'] = false;
            $data['message'] = 'Jadwal Ujian sedang digunakan';
        }
        $this->output_json($data);
    }
    public function deleteAllJadwal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $arrId = json_decode($this->input->post('checked', true));
        ob_start();
        $jadwals = $this->cbt->getJadwalByArrId($arrId);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $backuped = [];
        $digunakan = [];
        foreach ($jadwals as $jadwal) {
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0 ? 1 : 0;
            array_push($backuped, $jadwal->rekap);
            array_push($digunakan, $terpakai);
        }
        $count_terpakai = array_count_values($digunakan);
        $counts = array_count_values($backuped);
        if ($count_terpakai[1] > 0 && $counts[0] > 0) {
            ob_end_clean();
            $data['status'] = false;
            $data['message'] = 'Hasil Ujian belum direkap';
        } else {
            if ($this->master->delete('cbt_jadwal', $arrId, 'id_jadwal')) {
            }
            ob_end_clean();
            $data['status'] = false;
            $data['message'] = 'Jadwal Ujian sedang digunakan';
        }
        $data['digunakan'] = $count_terpakai;
        $data['backup'] = $counts;
        $this->output_json($data);
    }
}