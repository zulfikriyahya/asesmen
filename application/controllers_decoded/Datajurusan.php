<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datajurusan extends CI_Controller
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
        $data = ['user' => $user, 'judul' => 'Jurusan', 'subjudul' => 'Daftar Jurusan', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $kode_peminatan = $this->dropdown->getAllKodePeminatan();
        $data['kode_peminatan'] = $kode_peminatan;
        $arr_kode = [];
        foreach ($kode_peminatan as $kode) {
            $arr_kode[] = $kode->kode_kel_mapel;
        }
        $data['mapel_peminatan'] = $this->dropdown->getMapelPeminatan($arr_kode);
        $jurusans = $this->master->getDataJurusan();
        $jurusan_mapels = [];
        foreach ($jurusans as $jurusan) {
            $jurusan_mapels[$jurusan->id_jurusan] = $this->master->getDataJurusanMapel(explode(',', $jurusan->mapel_peminatan ?? ''));
        }
        $data['jurusans'] = $jurusans;
        $data['jurusan_mapels'] = $jurusan_mapels;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/jurusan/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function add()
    {
        $mapels = [];
        $check_mapel = $this->input->post('mapel', true);
        if (!$check_mapel) {
        }
        $row_mapels = count($this->input->post('mapel', true));
        $i = 0;
        if (!($i <= $row_mapels)) {
        }
        array_push($mapels, $this->input->post('mapel[' . $i . ']', true));
        $i++;
    }
    public function data()
    {
        $this->output_json($this->master->getDataTableJurusan(), false);
    }
    public function save()
    {
        $rows = count($this->input->post('nama_jurusan', true));
        $mode = $this->input->post('mode', true);
        $i = 1;
        if (!($i <= $rows)) {
        }
        $nama_jurusan = 'nama_jurusan[' . $i . ']';
        $this->form_validation->set_rules($nama_jurusan, 'Jurusan', 'required');
        $this->form_validation->set_message('required', '{field} Wajib diisi');
        if ($this->form_validation->run() === FALSE) {
        }
        if ($mode == 'add') {
        }
        if (!($mode == 'edit')) {
        }
        $update[] = array('id_jurusan' => $this->input->post('id_jurusan[' . $i . ']', true), 'nama_jurusan' => $this->input->post($nama_jurusan, true));
        $status = TRUE;
        $i++;
    }
    public function update()
    {
        $data = $this->master->updateJurusan();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
        }
        $messages = [];
        $tables = [];
        $tabless = $this->db->list_tables();
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_jurusan' || $field->name == 'jurusan_id')) {
                }
                array_push($tables, $table);
            }
        }
        foreach ($tables as $table) {
            if (!($table != 'master_jurusan')) {
            }
            if ($table == 'master_kelas') {
            }
            $this->db->where_in('id_jurusan', $chk);
            $num = $this->db->count_all_results($table);
            if (!($num > 0)) {
            }
            array_push($messages, $table);
        }
        if (count($messages) > 0) {
        }
        if (!$this->master->delete('master_jurusan', $chk, 'id_jurusan')) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
    public function load_jurusan()
    {
        $data = $this->master->getJurusan();
        $this->output_json($data);
    }
    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Import Jurusan', 'subjudul' => 'Import Jurusan', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if (!($import_data != null)) {
        }
        $data['import'] = $import_data;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/jurusan/import');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function do_import()
    {
        $data = json_decode($this->input->post('jurusan', true));
        $jurusan = [];
        foreach ($data as $j) {
            $jurusan[] = ['nama_jurusan' => $j->nama, 'kode_jurusan' => $j->kode];
        }
        $save = $this->master->create('master_jurusan', $jurusan, true);
        $this->output->set_content_type('application/json')->set_output($save);
    }
    function updateById()
    {
        $id = $this->input->post('id_jurusan');
        $nama = $this->input->post('username', true);
        $kode = $this->input->post('email', true);
        $this->db->set('nama_jurusan', $nama);
        $this->db->set('kode_jurusan', $kode);
        $this->db->where('id_jurusan', $id);
        return $this->db->update('master_jurusan');
    }
    public function hapusById()
    {
        $id = $this->input->post('id');
        $this->db->where('id_jurusan', $id);
        return $this->db->delete('master_jurusan');
    }
    function exist($table, $data)
    {
        $query = $this->db->get_where($table, $data);
        $count = $query->num_rows();
        if ($count === 0) {
            return false;
        } else {
            return true;
        }
    }
}