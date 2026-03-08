<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataekstra extends CI_Controller
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
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Ekstrakurikuler', 'subjudul' => 'Data Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['ekskul'] = $this->dropdown->getAllEkskul();
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $kelasEks = [];
        foreach ($kelas as $key => $kls) {
            $kelasEks[$key] = $this->kelas->getKelasEkskul($key, $tp->id_tp, $smt->id_smt);
        }
        $data['ekskul_kelas'] = $kelasEks;
        $data['kelas'] = $kelas;
        $data['pembimbing'] = $this->dropdown->getAllGuru();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/ekstra/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $insert = ['nama_ekstra' => $this->input->post('nama_ekstra', true), 'kode_ekstra' => $this->input->post('kode_ekstra', true)];
        $data = $this->master->create('master_ekstra', $insert);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function read()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_ekstra');
        echo $this->datatables->generate();
    }
    public function update()
    {
        $data = $this->master->updateEkstra();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete($id)
    {
        $messages = [];
        $tables = [];
        $tabless = $this->db->list_tables();
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_ekstra' || $field->name == 'ekstra_id')) {
                } else {
                    array_push($tables, $table);
                }
            }
        }
        $this->output_json($tables);
        foreach ($tables as $table) {
            if (!($table != 'master_ekstra')) {
            } else {
                $this->db->where('id_ekstra', $id);
                $num = $this->db->count_all_results($table);
                if (!($num > 0)) {
                }
                array_push($messages, $table);
            }
        }
        if ($messages && count($messages) > 0) {
            $this->output_json(['status' => false, 'total' => 'Mapel digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        } else {
            if ($this->master->delete('master_ekstra', [$id], 'id_ekstra')) {
            }
            $this->output_json(['status' => false, 'message' => 'Ekskul gagal dihapus']);
        }
    }
    public function save()
    {
        $check_kelas = json_decode(json_encode(json_decode($this->input->post('kelas', true))));
        $tp = $this->master->getTahunActive()->id_tp;
        $smt = $this->master->getSemesterActive()->id_smt;
        $row_insert = 0;
        $update = [];
        foreach ($check_kelas as $key => $kls) {
            $check_ekskul = $this->input->post('ekskul' . $kls->kls_id, true);
            if (!$check_ekskul) {
            } else {
                $row_ekskul = count($this->input->post('ekskul' . $kls->kls_id, true));
                $ekstra = [];
                $j = 0;
                if (!($j <= $row_ekskul)) {
                }
                $kelaseks = $this->input->post('ekskul' . $kls->kls_id . '[' . $j . ']', true);
                $ekstra[] = ['ekstra' => $kelaseks];
                $j++;
            }
        }
        $res['status'] = true;
        $res['update'] = $update;
        $this->output_json($res);
    }
    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Import Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if (!($import_data != null)) {
            $data['tp'] = $this->dashboard->getTahun();
        } else {
            $data['import'] = $import_data;
            $data['tp'] = $this->dashboard->getTahun();
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/ekstra/import');
        $this->load->view('_templates/dashboard/_footer');
    }
}