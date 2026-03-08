<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Useradmin extends CI_Controller
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
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function is_admin()
    {
        if ($this->ion_auth->is_admin()) {
        } else {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
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
    public function data()
    {
        $this->is_admin();
        $this->output_json($this->users->getDataadmin(), false);
    }
    public function index()
    {
        $this->is_admin();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Admin Management', 'subjudul' => 'Data Admin', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/data');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function edit($id)
    {
        $level = $this->ion_auth->get_users_groups($id)->result();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Administrator', 'subjudul' => 'Edit Data Admin', 'users' => $this->ion_auth->user($id)->row(), 'groups' => $this->ion_auth->groups()->result(), 'level' => $level[0], 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/edit');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function create()
    {
        $this->is_admin();
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]|max_length[20]|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|matches[password]|required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email'), 'password' => form_error('password'), 'confirm_password' => form_error('confirm_password')];
        } else {
            $username = $this->input->post('username', true);
            $password = $this->input->post('password', true);
            $email = $this->input->post('email', true);
            $additional_data = ['first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true)];
            $group = array('1');
            if ($this->ion_auth->username_check($username)) {
            }
            if ($this->ion_auth->email_check($email)) {
            }
            $this->ion_auth->register($username, $password, $email, $additional_data, $group);
            $data = ['status' => true, 'msg' => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'];
        }
        $this->output_json($data);
    }
    public function edit_info()
    {
        $this->is_admin();
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['username' => $this->input->post('username', true), 'first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true), 'email' => $this->input->post('email', true)];
            $update = $this->master->update('users', $input, 'id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function edit_status()
    {
        $this->is_admin();
        $this->form_validation->set_rules('status', 'Status', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['status' => form_error('status')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['active' => $this->input->post('status', true)];
            $update = $this->master->update('users', $input, 'id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function edit_level()
    {
        $this->is_admin();
        $this->form_validation->set_rules('level', 'Level', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['level' => form_error('level')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['group_id' => $this->input->post('level', true)];
            $update = $this->master->update('users_groups', $input, 'user_id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($this->form_validation->run() === FALSE) {
            $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        } else {
            $identity = $this->session->userdata('identity');
            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            if ($change) {
            }
            $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
        }
        $this->output_json($data);
    }
    public function delete($id)
    {
        $this->is_admin();
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
        $this->output_json($data);
    }
    function uploadFile($id_user)
    {
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/profiles/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = 'foto_' . $id_user;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/profiles/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!unlink($file_name)) {
        } else {
            echo 'File Delete Successfully';
        }
    }
    function saveProfile()
    {
        $nama = $this->input->post('nama_lengkap');
        $jabatan = $this->input->post('jabatan');
        $foto = $this->input->post('foto');
        $user = $this->ion_auth->user()->row();
        $insert = ['id_user' => $user->id, 'nama_lengkap' => $nama, 'jabatan' => $jabatan, 'foto' => str_replace(base_url(), '', $foto ?? '')];
        $update = $this->db->replace('users_profile', $insert);
        $res['status'] = $update;
        $this->output_json($res);
    }
}