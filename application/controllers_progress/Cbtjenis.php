<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtjenis extends CI_Controller
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
        $this->load->model('Dashboard_model', 'dashboard');

        $user = $this->ion_auth->user()->row();

        $data = [
            'user'       => $user,
            'judul'      => 'Jenis Ujian',
            'subjudul'   => 'Data Jenis Ujian',
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/jenis/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function data()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getJenis(), false);
    }

    public function add()
    {
        $this->load->model('Master_model', 'master');

        $insert = [
            'nama_jenis' => $this->input->post('nama_jenis', true),
            'kode_jenis' => $this->input->post('kode_jenis', true),
        ];

        $this->master->create('cbt_jenis', $insert, false);
        $this->output_json(['status' => $insert]);
    }

    public function update()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output->set_content_type('application/json')->set_output($this->cbt->updateJenis());
    }

    public function delete()
    {
        $this->load->model('Master_model', 'master');

        $chk = $this->input->post('checked', true);

        if (!$chk) {
            $this->output_json(['status' => false]);
            return;
        }

        $this->master->delete('cbt_jenis', $chk, 'id_jenis');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
}
