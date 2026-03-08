<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Guruview extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
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
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDetailGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if (!($user == null)) {
        }
        redirect('auth');
        $data = ['user' => $user, 'judul' => 'Profile', 'subjudul' => 'Profile Saya', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['guru'] = $guru;
        $inputsProfile = [['label' => 'Nama Lengkap', 'name' => 'nama_guru', 'value' => $guru->nama_guru, 'icon' => 'far fa-user', 'type' => 'text'], ['label' => 'Email', 'name' => 'email', 'value' => $guru->email, 'icon' => 'far fa-envelope', 'type' => 'text'], ['label' => 'NIP / NUPTK', 'name' => 'nip', 'value' => $guru->nip, 'icon' => 'far fa-id-card', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $guru->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'type' => 'text'], ['label' => 'No. Handphone', 'name' => 'no_hp', 'value' => $guru->no_hp, 'icon' => 'fa fa-phone', 'type' => 'number'], ['label' => 'Agama', 'name' => 'agama', 'value' => $guru->agama, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputsAlamat = [['label' => 'NIK', 'name' => 'no_ktp', 'value' => $guru->no_ktp, 'icon' => 'far fa-id-card', 'type' => 'number'], ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'value' => $guru->tempat_lahir, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Tgl. Lahir', 'name' => 'tgl_lahir', 'value' => $guru->tgl_lahir, 'icon' => 'fa fa-calendar', 'type' => 'text'], ['label' => 'Alamat', 'name' => 'alamat_jalan', 'value' => $guru->alamat_jalan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kecamatan', 'name' => 'kecamatan', 'value' => $guru->kecamatan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kota/Kab.', 'name' => 'kabupaten', 'value' => $guru->kabupaten, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Provinsi', 'name' => 'provinsi', 'value' => $guru->provinsi, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kode Pos', 'name' => 'kode_pos', 'value' => $guru->kode_pos, 'icon' => 'fa fa-envelope', 'type' => 'number']];
        $data['input_profile'] = json_decode(json_encode($inputsProfile), FALSE);
        $data['input_alamat'] = json_decode(json_encode($inputsAlamat), FALSE);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/profile');
        $this->load->view('members/guru/templates/footer');
    }
    public function save()
    {
        $id_guru = $this->input->post('id_guru', true);
        $nip = $this->input->post('nip', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $email = $this->input->post('email', true);
        $jenis_kelamin = $this->input->post('jenis_kelamin', true);
        $no_hp = $this->input->post('no_hp', true);
        $agama = $this->input->post('agama', true);
        $no_ktp = $this->input->post('no_ktp', true);
        $tempat_lahir = $this->input->post('tempat_lahir', true);
        $tgl_lahir = $this->input->post('tgl_lahir', true);
        $alamat_jalan = $this->input->post('alamat_jalan', true);
        $kecamatan = $this->input->post('kecamatan', true);
        $kabupaten = $this->input->post('kabupaten', true);
        $provinsi = $this->input->post('provinsi', true);
        $kode_pos = $this->input->post('kode_pos', true);
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $dbdata = $this->master->getGuruById($id_guru, $tp->id_tp, $smt->id_smt);
        $u_nip = $dbdata->nip === $nip ? '' : '|is_unique[master_guru.nip]';
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[8]|max_length[30]' . $u_nip);
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[1]|max_length[50]');
        if ($this->form_validation->run() == FALSE) {
        }
        $input = ['nip' => $nip, 'nama_guru' => $nama_guru, 'email' => $email, 'jenis_kelamin' => $jenis_kelamin, 'no_hp' => $no_hp, 'agama' => $agama, 'no_ktp' => $no_ktp, 'tempat_lahir' => $tempat_lahir, 'tgl_lahir' => $this->strContains($tgl_lahir, '0000-') ? null : $tgl_lahir, 'alamat_jalan' => $alamat_jalan, 'kecamatan' => $kecamatan, 'kabupaten' => $kabupaten, 'provinsi' => $provinsi, 'kode_pos' => $kode_pos];
        $action = $this->master->update('master_guru', $input, 'id_guru', $id_guru);
        if ($action) {
        }
        $this->output_json(['status' => false]);
    }
    function strContains($string, $val)
    {
        return strpos($string, $val) !== false;
    }
    function uploadFile($id_guru)
    {
        $guru = $this->master->getGuruById($id_guru);
        if (isset($_FILES['foto']['name'])) {
        }
        $data['src'] = '';
        $this->output_json($data);
    }
    function deleteFile($id_guru)
    {
        $src = $this->input->get('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!($file_name != 'user.jpg')) {
        }
        if (!unlink($file_name)) {
        }
        $this->db->set('foto', '');
        $this->db->where('id_guru', $id_guru);
        $this->db->update('master_guru');
        echo 'File Delete Successfully';
    }
}