<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Userguru extends CI_Controller
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
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function data()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->users->getUserGuru($tp->id_tp, $smt->id_smt), false);
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Data User Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if ($group === 'admin') {
        }
        $id = $this->users->getGuruByUsername($user->username);
        $this->edit($id->id_guru);
    }
    public function activate($id)
    {
        $guru = $this->users->getDataGuru($id);
        $nama = explode(' ', $guru->nama_guru ?? '');
        $first_name = $nama[0];
        $last_name = count($nama) > 2 ? $nama[1] : end($nama);
        $username = trim($guru->username ?? '');
        $password = trim($guru->password ?? '');
        $email = strtolower($guru->username ?? '') . '@guru.com';
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('2');
        if ($this->ion_auth->username_check($username)) {
        }
        if ($this->ion_auth->email_check($email)) {
        }
        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data = ['status' => true, 'msg' => 'Akun ' . $guru->nama_guru . ' diaktifkan.'];
        $this->db->set('id_user', $id_user);
        $this->db->where('id_guru', $id);
        $this->db->update('master_guru');
        $data['pass'] = $password;
        $this->output_json($data);
    }
    public function deactivate($id = NULL)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
        $id = (int) $id;
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
        }
        $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;
        foreach ($guruAktif as $guru) {
            if ($guru->aktif > 0) {
            }
            $this->activate($guru->id_guru);
            $jum += 1;
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru diaktifkan.'];
        $this->output_json($data);
    }
    public function nonaktifkanSemua()
    {
        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;
        foreach ($guruAktif as $guru) {
            if ($guru->aktif > 0) {
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru dinonaktifkan.'];
        $this->output_json($data);
    }
    public function edit($id)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->users->getDetailGuru($id);
        $users = $this->users->getUsers($guru->username);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'setting' => $this->dashboard->getSetting()];
        $data['users'] = $users;
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        if ($group === 'admin') {
        }
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('users/guru/edit');
        $this->load->view('members/guru/templates/footer');
    }
    public function editLogin()
    {
        $id_guru = $this->input->post('id_guru', true);
        $username = $this->input->post('username', true);
        $pass = $this->input->post('new', true);
        $guru_lain = $this->master->getUserIdGuruByUsername($username);
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($guru_lain && $guru_lain->id_guru != $id_guru) {
        }
        if ($this->form_validation->run() === FALSE) {
        }
        $guru = $this->db->get_where('master_guru', 'id_guru="' . $id_guru . '"')->row();
        $nama = explode(' ', $guru->nama_guru ?? '');
        $first_name = $nama[0];
        $last_name = end($nama);
        $username = trim($username ?? '');
        $password = trim($pass ?? '');
        $email = strtolower($username) . '@guru.com';
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('2');
        $user_guru = $this->db->get_where('users', 'email="' . $email . '"')->row();
        $deleted = true;
        if (!($user_guru != null)) {
        }
        $deleted = $this->ion_auth->delete_user((int) $user_guru->id);
        if ($deleted) {
        }
        $status = false;
        $msg = 'Gagal mengganti username/passsword';
        $data['status'] = $status;
        $data['text'] = $msg;
        $this->output_json($data);
    }
    function buangspasi($teks)
    {
        $teks = trim($teks ?? '');
        $hasil = $teks;
        if (!strpos($teks, ' ')) {
            return $hasil;
        } else {
            $remove[] = '\'';
            $remove[] = '.';
            $remove[] = ' ';
            $hasil = str_replace($remove, '', $teks ?? '');
            if (!strpos($teks, ' ')) {
            }
        }
    }
    private function registerGuru($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['status'] = true;
        $data['id'] = $reg;
        if (!($reg == false)) {
            return $data;
        } else {
            $data['status'] = false;
            return $data;
        }
    }
    public function reset_login()
    {
        $username = $this->input->get('username', true);
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
        $this->db->where('login', $username);
        if ($this->db->delete('login_attempts')) {
        }
        $data = ['status' => false, 'msg' => ' gagal direset'];
        $this->output_json($data, true);
    }
}