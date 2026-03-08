<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'admindashboard');
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

    public function data($id = null)
    {
        $this->is_admin();
        $this->output_json($this->users->getDataUsers($id), false);
    }

    public function index()
    {
        $this->is_admin();
        $data = [
            'user'       => $this->ion_auth->user()->row(),
            'judul'      => 'User Management',
            'subjudul'   => 'Data User',
            'tp'         => $this->admindashboard->getTahun(),
            'tp_active'  => $this->admindashboard->getTahunActive(),
            'smt'        => $this->admindashboard->getSemester(),
            'smt_active' => $this->admindashboard->getSemesterActive(),
        ];

        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/data');
        $this->load->view('_templates/dashboard/footer.php');
    }

    public function edit($id)
    {
        $level = $this->ion_auth->get_users_groups($id)->result();
        $data  = [
            'user'     => $this->ion_auth->user()->row(),
            'judul'    => 'User Management',
            'subjudul' => 'Edit Data User',
            'users'    => $this->ion_auth->user($id)->row(),
            'groups'   => $this->ion_auth->groups()->result(),
            'level'    => $level[0],
        ];

        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/edit');
        $this->load->view('_templates/dashboard/footer.php');
    }

    public function edit_info()
    {
        $this->is_admin();
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
        $this->is_admin();
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
        $this->is_admin();
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
        $this->is_admin();
        $this->output_json(['status' => (bool) $this->ion_auth->delete_user($id)]);
    }
}
