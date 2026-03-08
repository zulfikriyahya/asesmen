<?php

class Cbtstatus extends CI_Controller
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
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Status Ujian Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['jadwal'] = $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['sesi'] = $this->dropdown->getAllSesi();
        $data['pengawas'] = $this->cbt->getPengawasByGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $jadwals = $this->cbt->getJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $arrKls = [];
        foreach ($jadwals as $jad) {
            $kls = unserialize($jad->bank_kelas ?? '');
            foreach ($kls as $kl) {
                array_push($arrKls, $kl['kelas_id']);
            }
        }
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/cbt/status/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function status_ruang()
    {
        $ruang = $this->input->get('ruang');
        $sesi = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Status Ujian Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->db->trans_start();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $info = $this->cbt->getJadwalById($jadwal);
        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $sesi, $info->bank_level);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi, $ruang);
        $ids_pengawas = [];
        if (!($pengawas && count($pengawas) > 0)) {
        }
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $arrDur = [];
        foreach ($siswas as $siswa) {
            $dur_siswa = null;
            foreach ($durasies as $durasi) {
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
                if ($durasi->lama_ujian == null) {
                }
                $lamanya = $durasi->lama_ujian;
                if (strpos($lamanya, ':') !== false) {
                }
                $durasi->lama_ujian .= 'm';
                $dur_siswa = $durasi;
            }
            $log_siswa = [];
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
                array_push($log_siswa, $log);
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $this->db->trans_complete();
        $data['siswa'] = $siswas;
        $data['durasi_siswa'] = $arrDur;
        $data['info'] = $info;
        $data['ids_pengawas'] = $ids_pengawas;
        $guru_ngawas = [];
        if (!($ids_pengawas && count($ids_pengawas) > 0)) {
        }
        $guru_ngawas = $this->master->getGuruByArrId($ids_pengawas);
        $data['pengawas'] = $guru_ngawas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/cbt/status/status');
        $this->load->view('members/guru/templates/footer');
    }
    public function getJadwalUjianByJadwal()
    {
        $jadwal = $this->input->get('id_jadwal');
        $info = $this->cbt->getJadwalById($jadwal);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $kelas = unserialize($info->bank_kelas ?? '');
        $kelases = [];
        foreach ($kelas as $key => $value) {
            $kelases[$value['kelas_id']] = $this->dropdown->getNamaKelasById($info->id_tp, $info->id_smt, $value['kelas_id']);
        }
        $this->output_json($kelases);
    }
    public function getJadwalUjianByKelas()
    {
        $kelas = $this->input->get('id_kelas');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        if ($this->ion_auth->in_group('guru')) {
        }
        $id_guru = null;
        $jadwals = $this->cbt->getAllJadwal($tp->id_tp, $smt->id_smt, $id_guru);
        $jdwl = [];
        foreach ($jadwals as $jadwal) {
            $kls = unserialize($jadwal->bank_kelas ?? '');
            foreach ($kls as $kl) {
                if (!($kl['kelas_id'] == $kelas)) {
                }
                $jdwl[$jadwal->id_jadwal] = $jadwal->bank_kode;
            }
        }
        $this->output_json($jdwl);
    }
    public function getSiswaKelas()
    {
        $kelas = $this->input->get('kelas');
        $jadwal = $this->input->get('jadwal');
        $this->db->trans_start();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $info = $this->cbt->getJadwalById($jadwal);
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal);
        $ids_pengawas = [];
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $arrDur = [];
        foreach ($siswas as $siswa) {
            $dur_siswa = null;
            foreach ($durasies as $durasi) {
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
                $mulai = new DateTime($durasi->mulai);
                $interval = $mulai->diff(new DateTime());
                $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                $durasi->ada_waktu = $minutes < $info->durasi_ujian;
                if ($durasi->lama_ujian == null) {
                }
                $lamanya = $durasi->lama_ujian;
                if (strpos($lamanya, ':') !== false) {
                }
                $durasi->lama_ujian .= 'm';
                $dur_siswa = $durasi;
            }
            $log_siswa = [];
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
                array_push($log_siswa, $log);
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $this->db->trans_complete();
        $data['siswa'] = $siswas;
        $data['durasi'] = $arrDur;
        $data['info'] = $info;
        $data['pengawas'] = $this->master->getGuruByArrId($ids_pengawas);
        $this->output_json($data);
    }
    public function getSiswaRuang()
    {
        $ruang = $this->input->get('ruang');
        $sesi = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $this->db->trans_start();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $info = $this->cbt->getJadwalById($jadwal);
        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $sesi, $info->bank_level);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi, $ruang);
        $ids_pengawas = [];
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $arrDur = [];
        foreach ($siswas as $siswa) {
            $dur_siswa = null;
            foreach ($durasies as $durasi) {
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
                $mulai = new DateTime($durasi->mulai);
                $interval = $mulai->diff(new DateTime());
                $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                $durasi->ada_waktu = $minutes < $info->durasi_ujian;
                if ($durasi->lama_ujian == null) {
                }
                $lamanya = $durasi->lama_ujian;
                if (strpos($lamanya, ':') !== false) {
                }
                $durasi->lama_ujian .= 'm';
                $dur_siswa = $durasi;
            }
            $log_siswa = [];
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
                array_push($log_siswa, $log);
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $this->db->trans_complete();
        $data['siswa'] = $siswas;
        $data['durasi'] = $arrDur;
        $data['info'] = $info;
        $data['pengawas'] = $this->master->getGuruByArrId($ids_pengawas);
        $this->output_json($data);
    }
    public function detail()
    {
        $siswa = $this->input->get('siswa');
        $jadwal = $this->input->get('jadwal');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Detail Status Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['siswa'] = $this->master->getSiswaById($siswa);
        $data['soal'] = $this->cbt->getSoalSiswaByJadwal($jadwal, $siswa);
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/status/detail');
        $this->load->view('members/guru/templates/footer');
    }
}