<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);
        $this->form_validation->set_error_delimiters(
            $this->config->item('error_start_delimiter', 'ion_auth'),
            $this->config->item('error_end_delimiter', 'ion_auth')
        );
        $this->lang->load('auth');
    }

    public function output_json($data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    public function index()
    {
        $this->load->model('Settings_model', 'settings');

        if (count($this->db->list_tables()) == 0) {
            redirect('install');
        }

        $setting = $this->settings->getSetting();

        if ($setting == null) {
            redirect('install');
        }

        if ($this->ion_auth->logged_in()) {
            redirect('dashboard');
        }

        $this->data['setting']   = $setting;
        $this->data['identity']  = [
            'name'         => 'identity',
            'id'           => 'identity',
            'type'         => 'text',
            'placeholder'  => 'Username',
            'autofocus'    => 'autofocus',
            'class'        => 'form-control',
            'autocomplete' => 'off',
        ];
        $this->data['password']  = [
            'name'        => 'password',
            'id'          => 'password',
            'type'        => 'password',
            'placeholder' => 'Password',
            'class'       => 'form-control',
        ];
        $this->data['message'] = validation_errors()
            ? validation_errors()
            : $this->session->flashdata('message');

        $this->load->view('_templates/auth/_header', $this->data);
        $this->load->view('auth/login');
        $this->load->view('_templates/auth/_footer');
    }

    public function cek_login()
    {
        $this->form_validation->set_rules(
            'identity',
            str_replace(':', '', $this->lang->line('login_identity_label')) ?? '',
            'required|trim'
        );
        $this->form_validation->set_rules(
            'password',
            str_replace(':', '', $this->lang->line('login_password_label')) ?? '',
            'required|trim'
        );

        if ($this->form_validation->run() === TRUE) {
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
                $this->cek_akses();
                return;
            }

            if ($this->ion_auth->is_max_login_attempts_exceeded($this->input->post('identity'))) {
                $data = [
                    'status' => false,
                    'failed' => 'Anda sudah 3x melakukan percobaan login, silakan hubungi Administrator',
                    'akses'  => 'attempts',
                ];
                $this->output_json($data);
                return;
            }

            $data = ['status' => false, 'failed' => 'Incorrect Login', 'akses' => 'no attempts'];
        } else {
            $invalid = [
                'identity' => form_error('identity'),
                'password' => form_error('password'),
            ];
            $data = ['status' => false, 'invalid' => $invalid, 'akses' => 'no valid'];
        }

        $this->output_json($data);
    }

    public function cek_akses()
    {
        if (!$this->ion_auth->logged_in()) {
            $status = false;
            $url    = 'auth';
        } else {
            $status = true;
            $this->load->model('Log_model', 'logging');
            $this->logging->saveLog(1, 'Login');
            $url = 'dashboard';
        }

        $data = [
            'status' => $status,
            'url'    => $url,
            'role'   => $this->ion_auth->is_admin()
                ? 'admin'
                : ($this->ion_auth->in_group('guru') ? 'guru' : 'siswa'),
        ];

        $this->output_json($data);
    }

    public function logout()
    {
        $this->ion_auth->logout();
        redirect('login', 'refresh');
    }

    public function change_password()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $this->form_validation->set_rules(
            'old',
            $this->lang->line('change_password_validation_old_password_label'),
            'required'
        );
        $this->form_validation->set_rules(
            'new',
            $this->lang->line('change_password_validation_new_password_label'),
            'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]'
        );
        $this->form_validation->set_rules(
            'new_confirm',
            $this->lang->line('change_password_validation_new_password_confirm_label'),
            'required'
        );

        $user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() === FALSE) {
            $this->data['message']             = validation_errors()
                ? validation_errors()
                : $this->session->flashdata('message');
            $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
            $this->data['old_password']        = ['name' => 'old', 'id' => 'old', 'type' => 'password'];
            $this->data['new_password']        = ['name' => 'new', 'id' => 'new', 'type' => 'password'];
            $this->data['new_password_confirm'] = ['name' => 'new_confirm', 'id' => 'new_confirm', 'type' => 'password'];
            $this->data['user_id']             = ['name' => 'user_id', 'id' => 'user_id', 'type' => 'hidden', 'value' => $user->id];
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'change_password', $this->data);
            return;
        }

        $identity = $this->session->userdata('identity');
        $change   = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

        if ($change) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            $this->logout();
            return;
        }

        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect('auth/change_password', 'refresh');
    }

    public function forgot_password()
    {
        $this->data['title'] = $this->lang->line('forgot_password_heading');

        if ($this->config->item('identity', 'ion_auth') != 'email') {
            $this->form_validation->set_rules(
                'identity',
                $this->lang->line('forgot_password_identity_label'),
                'required'
            );
        } else {
            $this->form_validation->set_rules(
                'identity',
                $this->lang->line('forgot_password_validation_email_label'),
                'required|valid_email'
            );
        }

        if ($this->form_validation->run() === FALSE) {
            $this->data['message']  = validation_errors()
                ? validation_errors()
                : $this->session->flashdata('message');
            $this->data['type']     = $this->config->item('identity', 'ion_auth');
            $this->data['identity'] = [
                'name'         => 'identity',
                'id'           => 'identity',
                'class'        => 'form-control',
                'autocomplete' => 'off',
                'autofocus'    => 'autofocus',
            ];

            if ($this->config->item('identity', 'ion_auth') != 'email') {
                $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
            } else {
                $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
            }

            $this->load->view('_templates/auth/_header', $this->data);
            $this->load->view('auth/forgot_password');
            $this->load->view('_templates/auth/_footer');
            return;
        }

        $identity_column = $this->config->item('identity', 'ion_auth');
        $identity        = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();

        if (empty($identity)) {
            if ($identity_column != 'email') {
                $this->ion_auth->set_error('forgot_password_identity_not_found');
            } else {
                $this->ion_auth->set_error('forgot_password_email_not_found');
            }
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect('auth/forgot_password', 'refresh');
            return;
        }

        $forgotten = $this->ion_auth->forgotten_password($identity->{$identity_column});

        if ($forgotten) {
            $this->session->set_flashdata('success', $this->ion_auth->messages());
            redirect('auth/forgot_password', 'refresh');
            return;
        }

        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect('auth/forgot_password', 'refresh');
    }

    public function reset_password($code = NULL)
    {
        if (!$code) {
            show_404();
        }

        $this->data['title'] = $this->lang->line('reset_password_heading');

        $this->form_validation->set_rules(
            'new',
            $this->lang->line('reset_password_validation_new_password_label'),
            'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]'
        );
        $this->form_validation->set_rules(
            'new_confirm',
            $this->lang->line('reset_password_validation_new_password_confirm_label'),
            'required'
        );

        $user = $this->ion_auth->forgotten_password_check($code);

        if (!$user) {
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect('auth/forgot_password', 'refresh');
            return;
        }

        if ($this->form_validation->run() === FALSE) {
            $this->data['message']              = validation_errors()
                ? validation_errors()
                : $this->session->flashdata('message');
            $this->data['min_password_length']  = $this->config->item('min_password_length', 'ion_auth');
            $this->data['new_password']         = [
                'name'    => 'new',
                'id'      => 'new',
                'type'    => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            ];
            $this->data['new_password_confirm'] = [
                'name'    => 'new_confirm',
                'id'      => 'new_confirm',
                'type'    => 'password',
                'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$',
            ];
            $this->data['user_id']              = [
                'name'  => 'user_id',
                'id'    => 'user_id',
                'type'  => 'hidden',
                'value' => $user->id,
            ];
            $this->data['csrf']                 = $this->_get_csrf_nonce();
            $this->data['code']                 = $code;

            $this->load->view('_templates/auth/_header');
            $this->load->view('auth/reset_password', $this->data);
            $this->load->view('_templates/auth/_footer');
            return;
        }

        $identity = $user->{$this->config->item('identity', 'ion_auth')};

        if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {
            $this->ion_auth->clear_forgotten_password_code($identity);
            show_error($this->lang->line('error_csrf'));
        }

        $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));

        if ($change) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect('auth/login', 'refresh');
            return;
        }

        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect('auth/reset_password/' . $code, 'refresh');
    }

    public function activate($id, $code = FALSE)
    {
        $activation = FALSE;

        if ($code !== FALSE) {
            $activation = $this->ion_auth->activate($id, $code);
        } else {
            if (!$this->ion_auth->is_admin()) {
                show_error('You must be an administrator to activate users.');
            }
            $activation = $this->ion_auth->activate($id);
        }

        if ($activation) {
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect('auth', 'refresh');
            return;
        }

        $this->session->set_flashdata('message', $this->ion_auth->errors());
        redirect('auth/forgot_password', 'refresh');
    }

    public function deactivate($id = NULL)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            show_error('You must be an administrator to view this page.');
        }

        $id = (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules(
            'confirm',
            $this->lang->line('deactivate_validation_confirm_label'),
            'required'
        );
        $this->form_validation->set_rules(
            'id',
            $this->lang->line('deactivate_validation_user_id_label'),
            'required|alpha_numeric'
        );

        if ($this->form_validation->run() === FALSE) {
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();
            $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'deactivate_user', $this->data);
            return;
        }

        if ($this->input->post('confirm') != 'yes') {
            redirect('auth', 'refresh');
            return;
        }

        if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
            show_error($this->lang->line('error_csrf'));
        }

        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            $this->ion_auth->deactivate($id);
        }

        redirect('auth', 'refresh');
    }

    public function create_user()
    {
        $this->data['title'] = $this->lang->line('create_user_heading');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        $tables          = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');

        $this->data['identity_column'] = $identity_column;

        $this->form_validation->set_rules(
            'first_name',
            $this->lang->line('create_user_validation_fname_label'),
            'trim|required'
        );
        $this->form_validation->set_rules(
            'last_name',
            $this->lang->line('create_user_validation_lname_label'),
            'trim|required'
        );

        if ($identity_column !== 'email') {
            $this->form_validation->set_rules(
                'identity',
                $this->lang->line('create_user_validation_identity_label'),
                'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']'
            );
        }

        $this->form_validation->set_rules(
            'email',
            $this->lang->line('create_user_validation_email_label'),
            'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]'
        );
        $this->form_validation->set_rules(
            'phone',
            $this->lang->line('create_user_validation_phone_label'),
            'trim'
        );
        $this->form_validation->set_rules(
            'company',
            $this->lang->line('create_user_validation_company_label'),
            'trim'
        );
        $this->form_validation->set_rules(
            'password',
            $this->lang->line('create_user_validation_password_label'),
            'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]'
        );
        $this->form_validation->set_rules(
            'password_confirm',
            $this->lang->line('create_user_validation_password_confirm_label'),
            'required'
        );

        if ($this->form_validation->run() === TRUE) {
            $email           = strtolower($this->input->post('email'));
            $identity        = $identity_column === 'email' ? $email : $this->input->post('identity');
            $password        = $this->input->post('password');
            $additional_data = [
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'company'    => $this->input->post('company'),
                'phone'      => $this->input->post('phone'),
            ];

            if ($this->ion_auth->register($identity, $password, $email, $additional_data)) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('auth', 'refresh');
                return;
            }
        }

        $this->data['message'] = validation_errors()
            ? validation_errors()
            : ($this->ion_auth->errors()
                ? $this->ion_auth->errors()
                : $this->session->flashdata('message'));

        $this->data['first_name']        = ['name' => 'first_name',        'id' => 'first_name',        'type' => 'text',     'value' => $this->form_validation->set_value('first_name')];
        $this->data['last_name']         = ['name' => 'last_name',         'id' => 'last_name',         'type' => 'text',     'value' => $this->form_validation->set_value('last_name')];
        $this->data['identity']          = ['name' => 'identity',          'id' => 'identity',          'type' => 'text',     'value' => $this->form_validation->set_value('identity')];
        $this->data['email']             = ['name' => 'email',             'id' => 'email',             'type' => 'text',     'value' => $this->form_validation->set_value('email')];
        $this->data['company']           = ['name' => 'company',           'id' => 'company',           'type' => 'text',     'value' => $this->form_validation->set_value('company')];
        $this->data['phone']             = ['name' => 'phone',             'id' => 'phone',             'type' => 'text',     'value' => $this->form_validation->set_value('phone')];
        $this->data['password']          = ['name' => 'password',          'id' => 'password',          'type' => 'password', 'value' => $this->form_validation->set_value('password')];
        $this->data['password_confirm']  = ['name' => 'password_confirm',  'id' => 'password_confirm',  'type' => 'password', 'value' => $this->form_validation->set_value('password_confirm')];

        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'create_user', $this->data);
    }

    public function redirectUser()
    {
        if ($this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        } else {
            redirect('/', 'refresh');
        }
    }

    public function edit_user($id)
    {
        $this->data['title'] = $this->lang->line('edit_user_heading');

        if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && $this->ion_auth->user()->row()->id != $id)) {
            redirect('auth', 'refresh');
        }

        $user          = $this->ion_auth->user($id)->row();
        $groups        = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name',  $this->lang->line('edit_user_validation_lname_label'),  'trim|required');
        $this->form_validation->set_rules('phone',      $this->lang->line('edit_user_validation_phone_label'),   'trim');
        $this->form_validation->set_rules('company',    $this->lang->line('edit_user_validation_company_label'), 'trim');

        if (isset($_POST) && !empty($_POST)) {
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                show_error($this->lang->line('error_csrf'));
            }

            if ($this->input->post('password')) {
                $this->form_validation->set_rules(
                    'password',
                    $this->lang->line('edit_user_validation_password_label'),
                    'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]'
                );
                $this->form_validation->set_rules(
                    'password_confirm',
                    $this->lang->line('edit_user_validation_password_confirm_label'),
                    'required'
                );
            }

            if ($this->form_validation->run() === TRUE) {
                $data = [
                    'first_name' => $this->input->post('first_name'),
                    'last_name'  => $this->input->post('last_name'),
                    'company'    => $this->input->post('company'),
                    'phone'      => $this->input->post('phone'),
                ];

                if ($this->input->post('password')) {
                    $data['password'] = $this->input->post('password');
                }

                if ($this->ion_auth->is_admin()) {
                    $this->ion_auth->remove_from_group('', $id);
                    $groupData = $this->input->post('groups');
                    if (isset($groupData) && !empty($groupData)) {
                        foreach ($groupData as $grp) {
                            $this->ion_auth->add_to_group($grp, $id);
                        }
                    }
                }

                if ($this->ion_auth->update($user->id, $data)) {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                } else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }

                $this->redirectUser();
                return;
            }
        }

        $this->data['csrf']          = $this->_get_csrf_nonce();
        $this->data['message']       = validation_errors()
            ? validation_errors()
            : ($this->ion_auth->errors()
                ? $this->ion_auth->errors()
                : $this->session->flashdata('message'));
        $this->data['user']          = $user;
        $this->data['groups']        = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->data['first_name']       = ['name' => 'first_name',       'id' => 'first_name',       'type' => 'text',     'value' => $this->form_validation->set_value('first_name',       $user->first_name)];
        $this->data['last_name']        = ['name' => 'last_name',        'id' => 'last_name',        'type' => 'text',     'value' => $this->form_validation->set_value('last_name',        $user->last_name)];
        $this->data['company']          = ['name' => 'company',          'id' => 'company',          'type' => 'text',     'value' => $this->form_validation->set_value('company',          $user->company)];
        $this->data['phone']            = ['name' => 'phone',            'id' => 'phone',            'type' => 'text',     'value' => $this->form_validation->set_value('phone',            $user->phone)];
        $this->data['password']         = ['name' => 'password',         'id' => 'password',         'type' => 'password'];
        $this->data['password_confirm'] = ['name' => 'password_confirm', 'id' => 'password_confirm', 'type' => 'password'];

        $this->_render_page('auth/edit_user', $this->data);
    }

    public function create_group()
    {
        $this->data['title'] = $this->lang->line('create_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        $this->form_validation->set_rules(
            'group_name',
            $this->lang->line('create_group_validation_name_label'),
            'trim|required|alpha_dash'
        );

        if ($this->form_validation->run() === TRUE) {
            $new_group_id = $this->ion_auth->create_group(
                $this->input->post('group_name'),
                $this->input->post('description')
            );

            if ($new_group_id) {
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('auth', 'refresh');
                return;
            }

            $this->session->set_flashdata('message', $this->ion_auth->errors());
        }

        $this->data['message']    = validation_errors()
            ? validation_errors()
            : ($this->ion_auth->errors()
                ? $this->ion_auth->errors()
                : $this->session->flashdata('message'));
        $this->data['group_name'] = ['name' => 'group_name',  'id' => 'group_name',  'type' => 'text', 'value' => $this->form_validation->set_value('group_name')];
        $this->data['description'] = ['name' => 'description', 'id' => 'description', 'type' => 'text', 'value' => $this->form_validation->set_value('description')];

        $this->_render_page('auth/create_group', $this->data);
    }

    public function edit_group($id)
    {
        if (!$id || empty($id)) {
            redirect('auth', 'refresh');
        }

        $this->data['title'] = $this->lang->line('edit_group_title');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('auth', 'refresh');
        }

        $group = $this->ion_auth->group($id)->row();

        $this->form_validation->set_rules(
            'group_name',
            $this->lang->line('edit_group_validation_name_label'),
            'trim|required|alpha_dash'
        );

        if (isset($_POST) && !empty($_POST)) {
            if ($this->form_validation->run() === TRUE) {
                $group_update = $this->ion_auth->update_group(
                    $id,
                    $_POST['group_name'],
                    ['description' => $_POST['group_description']]
                );

                if ($group_update) {
                    $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
                } else {
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                }

                redirect('auth', 'refresh');
                return;
            }
        }

        $this->data['message']    = validation_errors()
            ? validation_errors()
            : ($this->ion_auth->errors()
                ? $this->ion_auth->errors()
                : $this->session->flashdata('message'));
        $this->data['group']      = $group;
        $this->data['group_name'] = [
            'name'  => 'group_name',
            'id'    => 'group_name',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('group_name', $group->name),
        ];

        if ($this->config->item('admin_group', 'ion_auth') === $group->name) {
            $this->data['group_name']['readonly'] = 'readonly';
        }

        $this->data['group_description'] = [
            'name'  => 'group_description',
            'id'    => 'group_description',
            'type'  => 'text',
            'value' => $this->form_validation->set_value('group_description', $group->description),
        ];

        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'edit_group', $this->data);
    }

    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key   = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);
        return [$key => $value];
    }

    public function _valid_csrf_nonce()
    {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return TRUE;
        }
        return FALSE;
    }

    public function _render_page($view, $data = NULL, $returnhtml = FALSE)
    {
        $viewdata = empty($data) ? $this->data : $data;
        $view_html = $this->load->view($view, $viewdata, $returnhtml);
        if ($returnhtml) {
            return $view_html;
        }
    }
}
