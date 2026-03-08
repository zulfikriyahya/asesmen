<?php

defined('BASEPATH') or exit('No direct script access allowed');

class JurusanMapel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin()) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
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

    public function index()
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Jurusan Mata Kuliah', 'subjudul' => 'Data Jurusan Mata Kuliah'];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/data');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function data()
    {
        $this->output_json($this->master->getJurusanMapel(), false);
    }

    public function getJurusanId($id)
    {
        $this->output_json($this->master->getAllJurusan($id));
    }

    public function add()
    {
        $data = [
            'user'    => $this->ion_auth->user()->row(),
            'judul'   => 'Tambah Jurusan Mata Kuliah',
            'subjudul' => 'Tambah Data Jurusan Mata Kuliah',
            'mapel'   => $this->master->getMapel(),
        ];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/add');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function edit($id)
    {
        $data = [
            'user'        => $this->ion_auth->user()->row(),
            'judul'       => 'Edit Jurusan Mata Kuliah',
            'subjudul'    => 'Edit Data Jurusan Mata Kuliah',
            'mapel'       => $this->master->getMapelById($id, true),
            'id_mapel'    => $id,
            'all_jurusan' => $this->master->getAllJurusan(),
            'jurusan'     => $this->master->getJurusanByIdMapel($id),
        ];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/edit');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function save()
    {
        $this->form_validation->set_rules('mapel_id',   'Mata Kuliah', 'required');
        $this->form_validation->set_rules('jurusan_id[]', 'Jurusan',    'required');

        if ($this->form_validation->run() == FALSE) {
            $this->output_json(['status' => false, 'errors' => ['mapel_id' => form_error('mapel_id'), 'jurusan_id[]' => form_error('jurusan_id[]')]]);
            return;
        }

        $mapel_id   = $this->input->post('mapel_id', true);
        $jurusan_id = $this->input->post('jurusan_id', true);
        $input      = [];

        foreach ($jurusan_id as $val) {
            $input[] = ['mapel_id' => $mapel_id, 'jurusan_id' => $val];
        }

        $this->master->delete('jurusan_mapel', $mapel_id, 'mapel_id');
        $action = $this->master->create('jurusan_mapel', $input, true);
        $this->output_json(['status' => (bool) $action]);
    }

    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
            return;
        }
        $this->master->delete('jurusan_mapel', $chk, 'mapel_id');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
}
