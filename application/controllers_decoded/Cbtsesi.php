<?php

class Cbtsesi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
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
        $data = ['user' => $user, 'judul' => 'Sesi Ujian', 'subjudul' => 'Data Sesi Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesi/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->output_json($this->cbt->getSesi(), false);
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
    public function edit($id)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Sesi Siswa', 'subjudul' => 'Atur Sesi Siswa', 'sesi' => $this->cbt->getSesiById($id)];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesi/edit');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
        }
        if (!$this->master->delete('cbt_sesi', $chk, 'id_sesi')) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
    public function sesisiswa()
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Sesi Ujian', 'subjudul' => 'Data Sesi Ujian'];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesisiswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
}