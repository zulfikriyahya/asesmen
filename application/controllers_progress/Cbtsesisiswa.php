<?php

class Cbtsesisiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Atur Ruang dan Sesi Siswa', 'subjudul' => 'Ruang dan Sesi Siswa', 'setting' => $this->dashboard->getSetting(), 'kelas' => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt), 'ruang_kelas' => $this->cbt->getKelasList($tp->id_tp, $smt->id_smt), 'sesi' => $this->dropdown->getAllSesi(), 'ruang' => $this->cbt->getAllRuang(), 'tp' => $this->dashboard->getTahun(), 'tp_active' => $tp, 'smt' => $this->dashboard->getSemester(), 'smt_active' => $smt, 'profile' => $this->dashboard->getProfileAdmin($user->id)];
        $kls = $this->input->get('kls', true);
        $kelas_selected = $kls != null ? $kls : '0';
        $siswas = [];
        if (!($kelas_selected != '0')) {
            $data['siswas'] = $siswas;
        } else {
            $siswas = $this->cbt->getRuangSesiSiswa($kls, $tp->id_tp, $smt->id_smt);
            $data['siswas'] = $siswas;
        }
        $data['kelas_selected'] = $kelas_selected;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesisiswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function getAllRuang()
    {
        $this->output_json($this->cbt->getAllRuang());
    }
    public function getAllSesi()
    {
        $this->output_json($this->dropdown->getAllSesi());
    }
    public function add()
    {
        $insert = ['nama_sesi' => $this->input->post('nama_sesi', true), 'kode_sesi' => $this->input->post('kode_sesi', true), 'waktu_mulai' => $this->input->post('waktu_mulai', true), 'waktu_akhir' => $this->input->post('waktu_akhir', true)];
        $this->master->create('cbt_sesi', $insert, false);
        $data['status'] = $insert;
        $this->output_json($data);
    }
    public function update()
    {
        $data = $this->cbt->updateSesi();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('cbt_sesi', $chk, 'id_sesi')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function editsesisiswa()
    {
        $rs = $this->input->post('ruang-sesi', true);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $update = false;
        foreach ($rs as $id => $klss) {
            foreach ($klss as $idkls => $kls) {
                $data = ['siswa_id' => $id, 'kelas_id' => $idkls, 'ruang_id' => $kls['ruang'], 'sesi_id' => $kls['sesi'], 'tp_id' => $tp->id_tp, 'smt_id' => $smt->id_smt];
                $update = $this->db->replace('cbt_sesi_siswa', $data);
            }
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function editsesikelas()
    {
        $input = json_decode($this->input->post('kelas_sesi', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($input as $d) {
            $siswas = $this->kelas->getKelasSiswa($d->kelas_id, $tp->id_tp, $smt->id_smt);
            foreach ($siswas as $siswa) {
                $data = ['siswa_id' => $siswa->id_siswa, 'kelas_id' => $siswa->id_kelas, 'ruang_id' => $d->ruang_id, 'sesi_id' => $d->sesi_id, 'tp_id' => $tp->id_tp, 'smt_id' => $smt->id_smt];
                $this->db->replace('cbt_sesi_siswa', $data);
            }
            $data = ['id_kelas_ruang' => $d->kelas_id . $tp->id_tp . $smt->id_smt, 'id_kelas' => $d->kelas_id, 'id_ruang' => $d->ruang_id, 'id_sesi' => $d->sesi_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'set_siswa' => $d->set_siswa];
            $update = $this->db->replace('cbt_kelas_ruang', $data);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
}