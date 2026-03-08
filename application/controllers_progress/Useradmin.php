<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Useradmin extends CI_Controller
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

        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function is_admin()
    {
        if (!$this->ion_auth->is_admin()) {
            show_error(
                'Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
        }
    }

    public function output_json($data, $encode = true)
    {
        if (!$encode) {
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    private function base_data($user, $judul, $subjudul)
    {
        return [
            'user'       => $user,
            'judul'      => $judul,
            'subjudul'   => $subjudul,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];
    }

    public function data()
    {
        $this->output_json($this->users->getDataadmin(), false);
    }

    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $data = $this->base_data($user, 'Admin Management', 'Data Admin');

        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/data');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function edit($id)
    {
        $user  = $this->ion_auth->user()->row();
        $level = $this->ion_auth->get_users_groups($id)->result();

        $data = $this->base_data($user, 'Administrator', 'Edit Data Admin');
        $data['users']  = $this->ion_auth->user($id)->row();
        $data['groups'] = $this->ion_auth->groups()->result();
        $data['level']  = $level[0];

        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/edit');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function create()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]|max_length[20]');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required|trim|matches[password]');

        if ($this->form_validation->run() === false) {
            $data = [
                'status' => false,
                'errors' => [
                    'username'         => form_error('username'),
                    'first_name'       => form_error('first_name'),
                    'last_name'        => form_error('last_name'),
                    'email'            => form_error('email'),
                    'password'         => form_error('password'),
                    'confirm_password' => form_error('confirm_password'),
                ],
            ];
            $this->output_json($data);
            return;
        }

        $username = $this->input->post('username', true);
        $email    = $this->input->post('email', true);

        if ($this->ion_auth->username_check($username)) {
            $this->output_json(['status' => false, 'msg' => 'Username sudah digunakan.']);
            return;
        }

        if ($this->ion_auth->email_check($email)) {
            $this->output_json(['status' => false, 'msg' => 'Email sudah digunakan.']);
            return;
        }

        $additional_data = [
            'first_name' => $this->input->post('first_name', true),
            'last_name'  => $this->input->post('last_name', true),
        ];

        $this->ion_auth->register($username, $this->input->post('password', true), $email, $additional_data, ['1']);

        $this->output_json(['status' => true, 'msg' => 'User berhasil dibuat.']);
    }

    public function edit_info()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        if ($this->form_validation->run() === false) {
            $data = [
                'status' => false,
                'errors' => [
                    'username'   => form_error('username'),
                    'first_name' => form_error('first_name'),
                    'last_name'  => form_error('last_name'),
                    'email'      => form_error('email'),
                ],
            ];
            $this->output_json($data);
            return;
        }

        $id    = $this->input->post('id', true);
        $input = [
            'username'   => $this->input->post('username', true),
            'first_name' => $this->input->post('first_name', true),
            'last_name'  => $this->input->post('last_name', true),
            'email'      => $this->input->post('email', true),
        ];

        $update = $this->master->update('users', $input, 'id', $id);
        $this->output_json(['status' => (bool) $update]);
    }

    public function edit_status()
    {
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() === false) {
            $this->output_json(['status' => false, 'errors' => ['status' => form_error('status')]]);
            return;
        }

        $id     = $this->input->post('id', true);
        $input  = ['active' => $this->input->post('status', true)];
        $update = $this->master->update('users', $input, 'id', $id);

        $this->output_json(['status' => (bool) $update]);
    }

    public function edit_level()
    {
        $this->form_validation->set_rules('level', 'Level', 'required');

        if ($this->form_validation->run() === false) {
            $this->output_json(['status' => false, 'errors' => ['level' => form_error('level')]]);
            return;
        }

        $id     = $this->input->post('id', true);
        $input  = ['group_id' => $this->input->post('level', true)];
        $update = $this->master->update('users_groups', $input, 'user_id', $id);

        $this->output_json(['status' => (bool) $update]);
    }

    public function change_password()
    {
        $min_length = $this->config->item('min_password_length', 'ion_auth');

        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $min_length . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        if ($this->form_validation->run() === false) {
            $this->output_json([
                'status' => false,
                'errors' => [
                    'old'         => form_error('old'),
                    'new'         => form_error('new'),
                    'new_confirm' => form_error('new_confirm'),
                ],
            ]);
            return;
        }

        $identity = $this->session->userdata('identity');
        $change   = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

        if ($change) {
            $this->output_json(['status' => true, 'msg' => 'Password berhasil diubah.']);
        } else {
            $this->output_json(['status' => false, 'msg' => $this->ion_auth->errors()]);
        }
    }

    public function delete($id)
    {
        $data['status'] = (bool) $this->ion_auth->delete_user($id);
        $this->output_json($data);
    }

    public function uploadFile($id_user)
    {
        if (empty($_FILES['foto']['name'])) {
            $this->output_json(['src' => '']);
            return;
        }

        $config = [
            'upload_path'   => './uploads/profiles/',
            'allowed_types' => 'gif|jpg|png|jpeg',
            'overwrite'     => true,
            'file_name'     => 'foto_' . $id_user,
        ];

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('foto')) {
            $this->output_json(['status' => false, 'msg' => $this->upload->display_errors()]);
            return;
        }

        $result = $this->upload->data();

        $this->output_json([
            'status'   => true,
            'src'      => base_url('uploads/profiles/' . $result['file_name']),
            'filename' => pathinfo($result['file_name'], PATHINFO_FILENAME),
            'type'     => $_FILES['foto']['type'],
            'size'     => $_FILES['foto']['size'],
        ]);
    }

    public function deleteFile()
    {
        $src       = $this->input->post('src', true);
        $file_name = str_replace(base_url(), '', $src ?? '');

        if (file_exists($file_name) && unlink($file_name)) {
            echo 'File Delete Successfully';
        } else {
            echo 'File tidak ditemukan atau gagal dihapus';
        }
    }

    public function saveProfile()
    {
        $user   = $this->ion_auth->user()->row();
        $foto   = $this->input->post('foto', true);
        $insert = [
            'id_user'      => $user->id,
            'nama_lengkap' => $this->input->post('nama_lengkap', true),
            'jabatan'      => $this->input->post('jabatan', true),
            'foto'         => str_replace(base_url(), '', $foto ?? ''),
        ];

        $update = $this->db->replace('users_profile', $insert);
        $this->output_json(['status' => (bool) $update]);
    }
}
