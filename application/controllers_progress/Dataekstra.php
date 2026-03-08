<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dataekstra extends CI_Controller
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
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $kelasEks = [];
        foreach ($kelas as $key => $kls) {
            $kelasEks[$key] = $this->kelas->getKelasEkskul($key, $tp->id_tp, $smt->id_smt);
        }
        $data = [
            'user'        => $user,
            'judul'       => 'Ekstrakurikuler',
            'subjudul'    => 'Data Mata Pelajaran',
            'profile'     => $this->dashboard->getProfileAdmin($user->id),
            'setting'     => $this->dashboard->getSetting(),
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'ekskul'      => $this->dropdown->getAllEkskul(),
            'ekskul_kelas' => $kelasEks,
            'kelas'       => $kelas,
            'pembimbing'  => $this->dropdown->getAllGuru(),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/ekstra/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function create()
    {
        $insert = [
            'nama_ekstra' => $this->input->post('nama_ekstra', true),
            'kode_ekstra' => $this->input->post('kode_ekstra', true),
        ];
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
        $tables = [];
        foreach ($this->db->list_tables() as $table) {
            foreach ($this->db->field_data($table) as $field) {
                if ($field->name == 'id_ekstra' || $field->name == 'ekstra_id') {
                    $tables[] = $table;
                    break;
                }
            }
        }
        $messages = [];
        foreach ($tables as $table) {
            if ($table == 'master_ekstra') {
                continue;
            }
            $this->db->where('id_ekstra', $id);
            if ($this->db->count_all_results($table) > 0) {
                $messages[] = $table;
            }
        }
        if (!empty($messages)) {
            $this->output_json(['status' => false, 'total' => 'Ekskul digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
            return;
        }
        $deleted = $this->master->delete('master_ekstra', [$id], 'id_ekstra');
        $this->output_json(['status' => $deleted]);
    }

    public function save()
    {
        $check_kelas = json_decode($this->input->post('kelas', true));
        $tp = $this->master->getTahunActive()->id_tp;
        $smt = $this->master->getSemesterActive()->id_smt;
        $update = [];
        foreach ($check_kelas as $kls) {
            $check_ekskul = $this->input->post('ekskul' . $kls->kls_id, true);
            if (!$check_ekskul) {
                continue;
            }
            $ekstra = [];
            foreach ($check_ekskul as $kelaseks) {
                $ekstra[] = ['ekstra' => $kelaseks];
            }
        }
        $this->output_json(['status' => true, 'update' => $update]);
    }

    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = [
            'user'      => $user,
            'judul'     => 'Mata Pelajaran',
            'subjudul'  => 'Import Mata Pelajaran',
            'profile'   => $this->dashboard->getProfileAdmin($user->id),
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $this->dashboard->getTahunActive(),
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];
        if ($import_data !== null) {
            $data['import'] = $import_data;
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/ekstra/import');
        $this->load->view('_templates/dashboard/_footer');
    }
}
