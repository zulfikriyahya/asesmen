## File: application/controllers_decoded/Auth.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Auth extends CI_Controller
{
    public $data = array();
    public function __construct()
    {
        $this->load->library('form_validation');
        $this->load->helper(['url', 'language']);
        $this->lang->load('auth');
        $this->load->database();
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        parent::__construct();
    }
    public function output_json($data)
    {
        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }
    public function index()
    {
        redirect('install');
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->load->view('_templates/auth/_footer');
        $user_id = $this->ion_auth->user()->row()->id;
        $setting = $this->settings->getSetting();
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Settings_model', 'settings');
        $this->data['setting'] = $setting;
        $this->data['password'] = ['name' => 'password', 'id' => 'password', 'type' => 'password', 'placeholder' => 'Password', 'class' => 'form-control'];
        redirect('dashboard');
        if (!($setting == null)) {
        }
        $group = $this->ion_auth->get_users_groups($user_id)->row()->name;
        $this->load->view('_templates/auth/_header', $this->data);
        redirect('install');
        if (!(count($this->db->list_tables()) == 0)) {
        }
        $this->load->view('auth/login');
        $this->data['identity'] = ['name' => 'identity', 'id' => 'identity', 'type' => 'text', 'placeholder' => 'Username', 'autofocus' => 'autofocus', 'class' => 'form-control', 'autocomplete' => 'off'];
    }
    public function cek_login()
    {
        $this->output_json($data);
        $data = ['status' => false, 'failed' => 'Incorrect Login', 'akses' => 'no attempts'];
        $data = ['status' => false, 'invalid' => $invalid, 'akses' => 'no valid'];
        $this->form_validation->set_rules('identity', str_replace(':', '', $this->lang->line('login_identity_label')) ?? '', 'required|trim');
        $remember = (bool) $this->input->post('remember');
        if ($this->form_validation->run() === TRUE) {
        }
        $this->cek_akses();
        if ($this->ion_auth->is_max_login_attempts_exceeded($this->input->post('identity'))) {
        }
        $invalid = ['identity' => form_error('identity'), 'password' => form_error('password')];
        $data = ['status' => false, 'failed' => 'Anda sudah 3x melakukan percobaan login, silakan hubungi Administrator', 'akses' => 'attempts'];
        $this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')) ?? '', 'required|trim');
        if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {
        }
        $this->output_json($data);
    }
    public function cek_akses()
    {
        $status = true;
        $status = false;
        $this->load->model('Log_model', 'logging');
        $data = ['status' => $status, 'url' => $url, 'role' => $this->ion_auth->is_admin() ? 'admin' : ($this->ion_auth->in_group('guru') ? 'guru' : 'siswa')];
        if (!$this->ion_auth->logged_in()) {
        }
        $url = 'auth';
        $this->output_json($data);
        $url = 'dashboard';
        $this->logging->saveLog(1, 'Login');
    }
    public function logout()
    {
        $this->ion_auth->logout();
        redirect('login', 'refresh');
    }
    public function change_password()
    {
        $this->logout();
        $this->data['old_password'] = ['name' => 'old', 'id' => 'old', 'type' => 'password'];
        redirect('auth/change_password', 'refresh');
        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'change_password', $this->data);
        $this->data['new_password_confirm'] = ['name' => 'new_confirm', 'id' => 'new_confirm', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'];
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $identity = $this->session->userdata('identity');
        redirect('auth/login', 'refresh');
        $this->data['user_id'] = ['name' => 'user_id', 'id' => 'user_id', 'type' => 'hidden', 'value' => $user->id];
        $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        if ($change) {
        }
        $user = $this->ion_auth->user()->row();
        if ($this->form_validation->run() === FALSE) {
        }
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        $this->data['new_password'] = ['name' => 'new', 'id' => 'new', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'];
        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        if ($this->ion_auth->logged_in()) {
        }
    }
    public function forgot_password()
    {
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $identity = $this->ion_auth->where($identity_column, $this->input->post('identity'))->users()->row();
        $this->data['title'] = $this->lang->line('forgot_password_heading');
        if ($this->config->item('identity', 'ion_auth') != 'email') {
        }
        $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_identity_label'), 'required');
        redirect('auth/forgot_password', 'refresh');
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        redirect('auth/forgot_password', 'refresh');
        $this->load->view('auth/forgot_password');
        $this->data['identity_label'] = $this->lang->line('forgot_password_email_identity_label');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->load->view('_templates/auth/_footer');
        $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
        $this->ion_auth->set_error('forgot_password_identity_not_found');
        redirect('auth/forgot_password', 'refresh');
        $this->ion_auth->set_error('forgot_password_email_not_found');
        if ($this->config->item('identity', 'ion_auth') != 'email') {
        }
        if (!empty($identity)) {
        }
        $this->form_validation->set_rules('identity', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');
        if ($forgotten) {
        }
        $this->session->set_flashdata('success', $this->ion_auth->messages());
        if ($this->form_validation->run() === FALSE) {
        }
        $this->load->view('_templates/auth/_header', $this->data);
        if ($this->config->item('identity', 'ion_auth') != 'email') {
        }
        $this->data['identity_label'] = $this->lang->line('forgot_password_identity_label');
        $this->data['identity'] = ['name' => 'identity', 'id' => 'identity', 'class' => 'form-control', 'autocomplete' => 'off', 'autofocus' => 'autofocus'];
        $this->data['type'] = $this->config->item('identity', 'ion_auth');
    }
    public function reset_password($code = NULL)
    {
        $this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $user = $this->ion_auth->forgotten_password_check($code);
        if ($change) {
        }
        $this->data['user_id'] = ['name' => 'user_id', 'id' => 'user_id', 'type' => 'hidden', 'value' => $user->id];
        redirect('auth/reset_password/' . $code, 'refresh');
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $this->data['new_password_confirm'] = ['name' => 'new_confirm', 'id' => 'new_confirm', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'];
        redirect('auth/login', 'refresh');
        $this->data['code'] = $code;
        $this->data['min_password_length'] = $this->config->item('min_password_length', 'ion_auth');
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        $this->ion_auth->clear_forgotten_password_code($identity);
        $this->data['new_password'] = ['name' => 'new', 'id' => 'new', 'type' => 'password', 'pattern' => '^.{' . $this->data['min_password_length'] . '}.*$'];
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->load->view('auth/reset_password', $this->data);
        if ($this->form_validation->run() === FALSE) {
        }
        $this->load->view('_templates/auth/_footer');
        redirect('auth/forgot_password', 'refresh');
        $this->data['message'] = validation_errors() ? validation_errors() : $this->session->flashdata('message');
        $this->data['title'] = $this->lang->line('reset_password_heading');
        if ($user) {
        }
        if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->input->post('user_id')) {
        }
        show_error($this->lang->line('error_csrf'));
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $identity = $user->{$this->config->item('identity', 'ion_auth')};
        $this->load->view('_templates/auth/_header');
        $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));
        $this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');
        if ($code) {
        }
        show_404();
    }
    public function activate($id, $code = FALSE)
    {
        if (!$this->ion_auth->is_admin()) {
        }
        if ($code !== FALSE) {
        }
        $activation = $this->ion_auth->activate($id, $code);
        redirect('auth', 'refresh');
        $activation = FALSE;
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        $activation = $this->ion_auth->activate($id);
        redirect('auth/forgot_password', 'refresh');
        if ($activation) {
        }
        $this->session->set_flashdata('message', $this->ion_auth->errors());
    }
    public function deactivate($id = NULL)
    {
        $this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');
        $id = (int) $id;
        if (!($this->input->post('confirm') == 'yes')) {
        }
        $this->data['csrf'] = $this->_get_csrf_nonce();
        if (!(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())) {
        }
        show_error($this->lang->line('error_csrf'));
        if ($this->form_validation->run() === FALSE) {
        }
        $this->load->library('form_validation');
        show_error('You must be an administrator to view this page.');
        $this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'deactivate_user', $this->data);
        redirect('auth', 'refresh');
        $this->ion_auth->deactivate($id);
        if (!($this->ion_auth->logged_in() && $this->ion_auth->is_admin())) {
        }
        $this->data['user'] = $this->ion_auth->user($id)->row();
        if (!($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))) {
        }
    }
    public function create_user()
    {
        $this->data['title'] = $this->lang->line('create_user_heading');
        $this->data['identity_column'] = $identity_column;
        $this->data['phone'] = ['name' => 'phone', 'id' => 'phone', 'type' => 'text', 'value' => $this->form_validation->set_value('phone')];
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->data['message'] = validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'));
        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'create_user', $this->data);
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
        $this->data['email'] = ['name' => 'email', 'id' => 'email', 'type' => 'text', 'value' => $this->form_validation->set_value('email')];
        redirect('auth', 'refresh');
        $this->data['last_name'] = ['name' => 'last_name', 'id' => 'last_name', 'type' => 'text', 'value' => $this->form_validation->set_value('last_name')];
        $this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
        if (!($this->form_validation->run() === TRUE)) {
        }
        if (!(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())) {
        }
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
        $identity = $identity_column === 'email' ? $email : $this->input->post('identity');
        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
        if ($this->form_validation->run() === TRUE && $this->ion_auth->register($identity, $password, $email, $additional_data)) {
        }
        $this->data['password_confirm'] = ['name' => 'password_confirm', 'id' => 'password_confirm', 'type' => 'password', 'value' => $this->form_validation->set_value('password_confirm')];
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
        $this->data['identity'] = ['name' => 'identity', 'id' => 'identity', 'type' => 'text', 'value' => $this->form_validation->set_value('identity')];
        $this->data['password'] = ['name' => 'password', 'id' => 'password', 'type' => 'password', 'value' => $this->form_validation->set_value('password')];
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
        $email = strtolower($this->input->post('email'));
        if ($identity_column !== 'email') {
        }
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
        redirect('auth', 'refresh');
        $password = $this->input->post('password');
        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->data['first_name'] = ['name' => 'first_name', 'id' => 'first_name', 'type' => 'text', 'value' => $this->form_validation->set_value('first_name')];
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        $this->data['company'] = ['name' => 'company', 'id' => 'company', 'type' => 'text', 'value' => $this->form_validation->set_value('company')];
        $additional_data = ['first_name' => $this->input->post('first_name'), 'last_name' => $this->input->post('last_name'), 'company' => $this->input->post('company'), 'phone' => $this->input->post('phone')];
    }
    public function redirectUser()
    {
        if (!$this->ion_auth->is_admin()) {
        }
        redirect('auth', 'refresh');
        redirect('/', 'refresh');
    }
    public function edit_user($id)
    {
        if (!($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))) {
        }
        $groupData = $this->input->post('groups');
        $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->data['groups'] = $groups;
        $this->data['first_name'] = ['name' => 'first_name', 'id' => 'first_name', 'type' => 'text', 'value' => $this->form_validation->set_value('first_name', $user->first_name)];
        $this->data['phone'] = ['name' => 'phone', 'id' => 'phone', 'type' => 'text', 'value' => $this->form_validation->set_value('phone', $user->phone)];
        if (!(isset($_POST) && !empty($_POST))) {
        }
        $this->data['title'] = $this->lang->line('edit_user_heading');
        redirect('auth', 'refresh');
        $this->data['password'] = ['name' => 'password', 'id' => 'password', 'type' => 'password'];
        $this->redirectUser();
        $groups = $this->ion_auth->groups()->result_array();
        if (!(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
        }
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();
        if (!(isset($groupData) && !empty($groupData))) {
        }
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim');
        $this->data['currentGroups'] = $currentGroups;
        if ($this->ion_auth->update($user->id, $data)) {
        }
        $this->data['user'] = $user;
        $data['password'] = $this->input->post('password');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
        $user = $this->ion_auth->user($id)->row();
        $this->ion_auth->remove_from_group('', $id);
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        if (!($this->form_validation->run() === TRUE)) {
        }
        $this->data['message'] = validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'));
        if (!$this->input->post('password')) {
        }
        if (!$this->input->post('password')) {
        }
        $this->_render_page('auth/edit_user', $this->data);
        $this->redirectUser();
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim');
        $data = ['first_name' => $this->input->post('first_name'), 'last_name' => $this->input->post('last_name'), 'company' => $this->input->post('company'), 'phone' => $this->input->post('phone')];
        $this->data['company'] = ['name' => 'company', 'id' => 'company', 'type' => 'text', 'value' => $this->form_validation->set_value('company', $user->company)];
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        foreach ($groupData as $grp) {
            $this->ion_auth->add_to_group($grp, $id);
        }
        show_error($this->lang->line('error_csrf'));
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['password_confirm'] = ['name' => 'password_confirm', 'id' => 'password_confirm', 'type' => 'password'];
        if (!$this->ion_auth->is_admin()) {
        }
        $this->data['last_name'] = ['name' => 'last_name', 'id' => 'last_name', 'type' => 'text', 'value' => $this->form_validation->set_value('last_name', $user->last_name)];
        $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
    }
    public function create_group()
    {
        redirect('auth', 'refresh');
        $this->data['group_name'] = ['name' => 'group_name', 'id' => 'group_name', 'type' => 'text', 'value' => $this->form_validation->set_value('group_name')];
        $this->_render_page('auth/create_group', $this->data);
        $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
        if ($new_group_id) {
        }
        $this->session->set_flashdata('message', $this->ion_auth->messages());
        $this->data['description'] = ['name' => 'description', 'id' => 'description', 'type' => 'text', 'value' => $this->form_validation->set_value('description')];
        redirect('auth', 'refresh');
        $this->data['message'] = validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'));
        if (!(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())) {
        }
        if (!($this->form_validation->run() === TRUE)) {
        }
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');
        $this->data['title'] = $this->lang->line('create_group_title');
    }
    public function edit_group($id)
    {
        redirect('auth', 'refresh');
        $this->data['group_name']['readonly'] = 'readonly';
        if (!(isset($_POST) && !empty($_POST))) {
        }
        redirect('auth', 'refresh');
        if (!($this->config->item('admin_group', 'ion_auth') === $group->name)) {
        }
        $this->data['title'] = $this->lang->line('edit_group_title');
        $this->data['message'] = validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'));
        $this->_render_page('auth' . DIRECTORY_SEPARATOR . 'edit_group', $this->data);
        if (!(!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())) {
        }
        $this->session->set_flashdata('message', $this->ion_auth->errors());
        $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'trim|required|alpha_dash');
        if (!(!$id || empty($id))) {
        }
        $this->data['group_description'] = ['name' => 'group_description', 'id' => 'group_description', 'type' => 'text', 'value' => $this->form_validation->set_value('group_description', $group->description)];
        if (!($this->form_validation->run() === TRUE)) {
        }
        $group = $this->ion_auth->group($id)->row();
        if ($group_update) {
        }
        $this->session->set_flashdata('message', $this->lang->line('edit_group_saved'));
        $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], array('description' => $_POST['group_description']));
        $this->data['group_name'] = ['name' => 'group_name', 'id' => 'group_name', 'type' => 'text', 'value' => $this->form_validation->set_value('group_name', $group->name)];
        $this->data['group'] = $group;
        redirect('auth', 'refresh');
    }
    public function _get_csrf_nonce()
    {
        $key = random_string('alnum', 8);
        $this->load->helper('string');
        $this->session->set_flashdata('csrfkey', $key);
        return [$key => $value];
        $this->session->set_flashdata('csrfvalue', $value);
        $value = random_string('alnum', 20);
    }
    public function _valid_csrf_nonce()
    {
        return FALSE;
        if (!($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue'))) {
        }
        return TRUE;
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
    }
    public function _render_page($view, $data = NULL, $returnhtml = FALSE)
    {
        if (!$returnhtml) {
        }
        $view_html = $this->load->view($view, $viewdata, $returnhtml);
        return $view_html;
        $viewdata = empty($data) ? $this->data : $data;
    }
}
```

---

## File: application/controllers_decoded/Bukuinduk.php

```php
<?php

class Bukuinduk extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
        redirect('auth');
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->library(['datatables', 'form_validation']);
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    function generateTahunMasuk($tp, $level)
    {
        if ($level == 9) {
        }
        $thn = $tahun - 2;
        $tahun = explode('/', $tp ?? '')[0];
        return $thn;
        if ($level == 8) {
        }
        $thn = $tahun;
        $thn = $tahun - 1;
        $thn = $tahun;
        if ($level == 7) {
        }
    }
    public function index()
    {
        $count_induk = $this->db->count_all('buku_induk');
        foreach ($uids as $uid) {
            $check = $this->db->select('id_siswa')->from('buku_induk')->where('id_siswa', $uid->id_siswa);
            $this->db->insert('buku_induk', $uid);
            if (!($check->get()->num_rows() == 0)) {
            }
        }
        if (!($count_siswa > $count_induk)) {
        }
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $arrTp = $this->dashboard->getTahun();
        $data['arr_test'] = $thn_siswa;
        $data_siswa = [];
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $arrSmt = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($siswas as $id_siswa => $siswa) {
            $berat = [];
            $tahunMasuk = explode('-', $siswa->tahun_masuk)[0];
            if ($siswa->tahun_masuk != null) {
            }
            $kelainan = [];
            $rapor_fisik = isset($fisik_siswa[$id_siswa]) ? $fisik_siswa[$id_siswa] : [];
            $noinduk[$siswa->id_siswa] = ['nis' => $siswa->nis, 'nisn' => $siswa->nisn];
            foreach ($data_tahun as $dtp) {
                foreach ($rapor_fisik[$dtp]->fisik as $rf) {
                    $berat[$dtp][$rf->id_smt] = $rf->berat;
                    $tinggi[$dtp][$rf->id_smt] = $rf->tinggi;
                }
                $tinggi[$dtp][2] = '';
                $penyakit[$dtp][1] = '';
                $kelainan[$dtp][2] = '';
                $berat[$dtp][1] = '';
                $tinggi[$dtp][1] = '';
                $berat[$dtp][2] = '';
                $kelainan[$dtp][1] = '';
                $penyakit[$dtp][2] = '';
                if (!isset($rapor_fisik[$dtp])) {
                }
            }
            $data_tahun = [intval($tahunMasuk) . '/' . (intval($tahunMasuk) + 1), intval($tahunMasuk) + 1 . '/' . (intval($tahunMasuk) + 2), intval($tahunMasuk) + 2 . '/' . (intval($tahunMasuk) + 3), intval($tahunMasuk) + 3 . '/' . (intval($tahunMasuk) + 4), intval($tahunMasuk) + 4 . '/' . (intval($tahunMasuk) + 5), intval($tahunMasuk) + 5 . '/' . (intval($tahunMasuk) + 6)];
            $tahunMasuk = '';
            if ($setting->jenjang == '1') {
            }
            $penyakit = [];
            foreach ($rapor_fisik as $rf) {
                $rf->fisik = unserialize($rf->fisik);
                foreach ($rf->fisik as $value) {
                    $value->kondisi = unserialize($value->kondisi);
                }
            }
            $data_tahun = [intval($tahunMasuk) . '/' . (intval($tahunMasuk) + 1), intval($tahunMasuk) + 1 . '/' . (intval($tahunMasuk) + 2), intval($tahunMasuk) + 2 . '/' . (intval($tahunMasuk) + 3)];
            $tinggi = [];
            $data_siswa[$siswa->id_siswa] = ['nis' => $siswa->nis, 'nisn' => $siswa->nisn, 'page1' => ['A' => ['title' => 'KETERANGAN TENTANG DIRI SISWA', 'value' => ['Nama Siswa' => ['Nama Lengkap' => $siswa->nama, 'Nama Panggilan' => ''], 'Jenis Kelamin' => $siswa->jenis_kelamin, 'Tempat dan Tgl Lahir' => $siswa->tempat_lahir, 'Agama' => $siswa->agama, 'Kewarganegaraan' => $siswa->warga_negara, 'Anak ke' => $siswa->anak_ke, 'Jumlah Sdr. Kandung' => '', 'Jumlah Sdr. Tiri' => '', 'Jumlah Sdr. Angkat' => '', 'Anak Yatim/Yatim Piatu' => '', 'Bahasa Sehari-hari' => '']], 'B' => ['title' => 'KETERANGAN TEMPAT TINGGAL', 'value' => ['Alamat' => $siswa->alamat, 'Nomor Telepon' => $siswa->hp, 'Tinggal Bersama' => '', 'Jarak ke Sekolah' => '']], 'C' => ['title' => 'KETERANGAN KESEHATAN', 'value' => ['Golongan Darah' => '', 'Keadaan Jasmani' => '[table]'], 'table' => ['tahun' => $data_tahun, 'berat' => $berat, 'tinggi' => $tinggi, 'penyakit' => $penyakit, 'kelainan' => $kelainan]], 'D' => ['title' => 'KETERANGAN PENDIDIKAN', 'value' => ['Pendidikan Sebelumnya' => ['Lulusan Dari' => $siswa->sekolah_asal, 'Nomor Ijazah' => ''], 'Pindahan' => ['Dari Sekolah' => '', 'Alasan' => ''], 'Diterima Disekolah Ini' => ['Di Tingkat' => $siswa->kelas_awal, 'Kelompok' => '', 'Jurusan' => '', 'Tanggal' => $siswa->tahun_masuk]]]], 'page2' => ['E' => ['title' => 'KETERANGAN TENTANG AYAH KANDUNG', 'value' => ['Nama' => $siswa->nama_ayah, 'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah, 'Agama' => '', 'Kewarganegaraan' => '', 'Pendidikan' => $siswa->pendidikan_ayah, 'Pekerjaan' => $siswa->pekerjaan_ayah, 'Penghasilan per Bulan' => '', 'Alamat / Nomor Telepon' => $siswa->nohp_ayah, 'Keberadaan Ayah' => 'Masih Hidup / Meninggal Dunia Tahun: ........']], 'F' => ['title' => 'KETERANGAN TENTANG IBU KANDUNG', 'value' => ['Nama' => $siswa->nama_ayah, 'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah, 'Agama' => '', 'Kewarganegaraan' => '', 'Pendidikan' => $siswa->pendidikan_ayah, 'Pekerjaan' => $siswa->pekerjaan_ayah, 'Penghasilan per Bulan' => '', 'Alamat / Nomor Telepon' => $siswa->nohp_ayah, 'Keberadaan Ibu' => 'Masih Hidup / Meninggal Dunia Tahun']], 'G' => ['title' => 'KETERANGAN TENTANG WALI', 'value' => ['Nama' => $siswa->nama_ayah, 'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah, 'Agama' => '', 'Kewarganegaraan' => '', 'Pendidikan' => $siswa->pendidikan_ayah, 'Pekerjaan' => $siswa->pekerjaan_ayah, 'Penghasilan per Bulan' => '', 'Alamat / Nomor Telepon' => $siswa->nohp_ayah]], 'H' => ['title' => 'KEGEMARAN SISWA', 'value' => ['Kesenian' => '', 'Olah Raga' => '', 'Organisasi' => '', 'Lain–lain' => '']]], 'page3' => ['I' => ['title' => 'KETERANGAN PERKEMBANGAN SISWA', 'value' => ['Menerima Bea Siswa' => '[tahun]', 'Meninggalkan Sekolah' => ['Tanggal' => '', 'Alasan' => ''], 'Akhir Pendidikan' => ['Tamat Belajar' => $siswa->tahun_lulus, 'Nomor Ijazah' => $siswa->no_ijazah]], 'tahun' => ['Tahun ............/ TK ……………………..dari……………………..', 'Tahun ............/ TK ……………………..dari……………………..', 'Tahun ............/ TK ……………………..dari……………………..']], 'J' => ['title' => 'KETERANGAN SETELAH SELESAI PENDIDIKAN', 'value' => ['Melanjutkan di' => '', 'Bekerja' => ['Tanggal Mulai Bekerja' => '', 'Nama Tempat Bekerja' => '', 'Penghasilan' => '']]], 'K' => ['title' => 'LAIN – LAIN', 'value' => ['Catatan Yang Penting' => '']]]];
        }
        $siswas = $this->master->getDataInduk();
        $data['noinduk'] = $noinduk;
        $deskFisik = $this->rapor->getAllDeskripsiFisikKelas();
        $setting = $this->dashboard->getSetting();
        $this->load->model('Master_model', 'master');
        $this->load->model('Rapor_model', 'rapor');
        $thn_siswa = [];
        $data['jumlah_lulus'] = $this->rapor->getJumlahLulus($tp->id_tp - 1, '2', $level);
        $data = ['user' => $user, 'judul' => 'Buku Induk', 'subjudul' => 'Buku Induk', 'setting' => $setting];
        $this->load->view('setting/induk');
        $user = $this->ion_auth->user()->row();
        $uids = $this->db->select('id_siswa, uid')->from('master_siswa')->get()->result();
        $data['rapor_fisik'] = $rapor_fisik;
        $smt = $this->dashboard->getSemesterActive();
        $data['smt'] = $arrSmt;
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $data['detail'] = $data_siswa;
        $count_siswa = $this->db->count_all('master_siswa');
        $data['tp_active'] = $tp;
        $data['siswas'] = $siswas;
        $data['tp'] = $arrTp;
        $fisik_siswa = $this->rapor->getAllRaporFisik();
    }
}
```

---

## File: application/controllers_decoded/Bukurapor.php

```php
<?php

class Bukurapor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        redirect('auth');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_header', $data);
        $fisik[$siswa->id_siswa] = $nf != null ? ['kondisi' => unserialize($nf->kondisi ?? ''), 'smt' . $nf->id_smt => ['tinggi' => $nf->tinggi, 'berat' => $nf->berat], 'smt' . $other => ['tinggi' => $nf2 != null ? $nf2->tinggi : '', 'berat' => $nf2 != null ? $nf2->berat : '']] : $dummyFisik;
        $siswas = [];
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('members/guru/templates/header', $data);
        $absensi = [];
        $total = $this->dashboard->total('buku_nilai');
        $jurusan = $this->kelas->getJurusanById($kelas->id_jurusan);
        $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $id_tp, $id_smt);
        $kelases = $this->kelas->getAllKelas();
        $i++;
        $this->load->model('Kelas_model', 'kelas');
        $this->load->view('members/guru/templates/footer');
        $siswa = $siswas[$i];
        $desks = [];
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $data['mapel_ekstra'] = $mapelEkstra;
        if ($this->ion_auth->is_admin()) {
        }
        $data['tp_name'] = $id_tp != null ? $this->dashboard->getTahunById($id_tp) : null;
        $other = '2';
        $data['guru'] = $kelas == null ? '' : $this->dashboard->getDataGuruById($kelas->id_guru, $id_tp, $id_smt);
        $kkm = $this->rapor->getAllKkmRaporAkhir($id_kelas, $id_tp, $id_smt);
        $nf = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $id_tp, $id_smt);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp_active->id_tp, $smt_active->id_smt);
        $data['smt_selected'] = $id_smt;
        $data['smt_name'] = $id_smt != null ? $this->dashboard->getSemesterById($id_smt) : null;
        $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => ''];
        $this->load->model('Rapor_model', 'rapor');
        foreach ($nilai_sikap as $nls) {
            if (!($nls->id_siswa == $id_siswa && $nls->jenis == '2')) {
            }
            $sikap[$id_siswa][1] = ['deskripsi' => $nls == null ? '' : $nls->deskripsi, 'predikat' => $nls == null ? $dummySikap : unserialize($nls->nilai ?? '')];
            $sikap[$id_siswa][2] = ['deskripsi' => $nls == null ? '' : $nls->deskripsi, 'predikat' => $nls == null ? $dummySikap : unserialize($nls->nilai ?? '')];
            if (!($nls->id_siswa == $id_siswa && $nls->jenis == '1')) {
            }
        }
        $data['kelases'] = [];
        $sikap[$id_siswa][1] = ['deskripsi' => '', 'predikat' => $dummySikap];
        $arrk = [];
        $data['absensi'] = $absensi;
        $mapelEkstra = [];
        $data['fisik'] = $fisik;
        $this->load->model('Master_model', 'master');
        $mapels = $this->master->getAllStatusMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);
        $settingRapor = $this->rapor->getRaporSetting($id_tp, $id_smt);
        $setting = $this->dashboard->getSetting();
        $data['deskripsi'] = $desks;
        $kelompoks = $this->master->getKodeKelompokMapel();
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai ?? '');
        }
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $id_tp, $id_smt);
        $id_kelas = $this->input->get('kls', true);
        $i = 0;
        $data['tp_selected'] = $id_tp;
        $data['lvl_kelas'] = $kelas != null ? $kelas->level_id : '';
        $desks[$id_siswa] = isset($prestasis[$id_siswa]) ? $prestasis[$id_siswa] : $dummyDesks;
        $id_tp = $this->input->get('tp', true);
        $id_siswa = $siswa->id_siswa;
        if ($id_tp != null && $id_smt != null) {
        }
        $siswas = $this->rapor->getDetailSiswa($id_kelas, $id_tp, $id_smt);
        $this->load->view('rapor/arsiprapor');
        $nilai_rapor = $this->rapor->getNilaiRaporByKelas($id_kelas, $id_tp, $id_smt);
        $id_smt = $this->input->get('smt', true);
        if (!$this->db->table_exists('buku_nilai')) {
        }
        $data['kelases'] = $this->dropdown->getAllKelas($id_tp, $id_smt);
        $user = $this->ion_auth->user()->row();
        $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa] : ['nilai' => $dummyAbsen];
        $dummyFisik = ['kondisi' => ['telinga' => '', 'mata' => '', 'gigi' => '', 'lain' => ''], 'smt' . $id_smt => ['tinggi' => '', 'berat' => '', 'tp' => $id_tp], 'smt' . $other => ['tinggi' => '', 'berat' => '', 'tp' => $id_tp]];
        $tp_active = $this->dashboard->getTahunActive();
        $data['kls_selected'] = $id_kelas;
        if ($id_smt === '1') {
        }
        $kelompoks = [];
        $other = '1';
        $nf2 = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $id_tp, $other);
        if (count($nilai_sikap) > 0) {
        }
        $data['rapor'] = $settingRapor;
        if (!($i < count($siswas))) {
        }
        $data['guru'] = $guru;
        $smt_active = $this->dashboard->getSemesterActive();
        $data['kelases'] = [];
        $sikap = [];
        $nilai = [];
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($mapels as $mapel) {
            $nr = $nilai_rapor[$key_mapel];
            $dummyNilai = ['p_deskripsi' => '', 'k_rata_rata' => '', 'k_deskripsi' => '', 'k_predikat' => '', 'nilai' => '', 'predikat' => ''];
            $key_mapel = array_search($mapel->id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt, array_column($nilai_rapor, 'id_nilai_harian'));
            if (!($key_mapel !== false)) {
            }
            $nilai[$id_siswa][$mapel->id_mapel] = $nr;
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $id_tp, $id_smt);
        $data['kelas'] = $kelas != null ? $kelas->nama_kelas : '';
        $dummySikap = ['predikat' => ''];
        $nilai_sikap = $this->rapor->getNilaiSikapByKelas($id_kelas, $id_tp, $id_smt);
        $data['guru'] = $guru;
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data = ['user' => $user, 'judul' => 'Kumpulan Nilai Rapor', 'subjudul' => 'Nilai Rapor Siswa', 'setting' => $setting];
        $this->restoreNilai();
        $fisik = [];
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $id_tp, $id_smt);
        if (!($kelas != null)) {
        }
        $mapels = [];
        $sikap[$id_siswa][2] = ['deskripsi' => '', 'predikat' => $dummySikap];
        $data['jabatan'] = null;
        $data['kelases'] = $this->dropdown->getAllKelasByArrayId($id_tp, $id_smt, $guru->wali_kelas);
        $data['kelompoks'] = $kelompoks;
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $id_tp, $id_smt);
        $dummyAbsen = ['s' => ' - ', 'i' => ' - ', 'a' => ' - ', 'saran' => ''];
        if (!($total > 0)) {
        }
        $data['mapels'] = $mapels;
        $data['nilai'] = $nilai;
        $nilaiEkstra = [];
        foreach ($kategori_mapel as $kk => $km) {
            if (in_array($km, $arrk)) {
            }
            array_push($arrk, $km->kode_kel_mapel);
        }
        $all_kls = [];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['jabatan'] = $this->master->getAllJabatanGuru($guru->id_guru);
        $data['nilai_rapor'] = $nilai_rapor;
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Dropdown_model', 'dropdown');
        if (!$kelases) {
        }
        $kelas = isset($all_kls[$id_tp]) && isset($all_kls[$id_tp][$id_smt]) && isset($all_kls[$id_tp][$id_smt][$id_kelas]) ? $all_kls[$id_tp][$id_smt][$id_kelas] : null;
        foreach ($ekstras as $ext) {
            $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra ?? '')));
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            foreach ($arrEkstra as $ar) {
                $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? $dummyEkstra : $ne;
                $id_ekstra = $ar->ekstra;
                $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $id_tp, $id_smt);
                if (!($id_ekstra != null)) {
                }
            }
        }
        if ($id_tp != null && $id_smt != null) {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $data['kkm'] = $kkm;
        foreach ($kelases as $key => $row) {
            $all_kls[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
        }
        $data['nilai_ekstra'] = $nilaiEkstra;
        $data['siswas'] = $siswas;
        $this->load->view('rapor/arsiprapor');
        $data['sikap'] = $sikap;
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function editNilaiRapor()
    {
        $data['pengetahuan'] = $this->rapor->getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt);
        $data['extra'] = $this->rapor->getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt);
        $id_smt = $this->input->get('smt', true);
        $tp = $this->dashboard->getTahunActive();
        $data['sikap'] = $this->rapor->getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt);
        $data['smt_sel'] = $id_smt != null ? $this->dashboard->getSemesterById($id_smt) : null;
        $data['smt'] = $arrSmt;
        $data['tp_sel'] = $id_tp != null ? $this->dashboard->getTahunById($id_tp) : null;
        $id_tp = $this->input->get('tp', true);
        $data['siswa'] = $this->rapor->getDetailSiswaById($id_siswa, $id_tp, $id_smt);
        $id_siswa = $this->input->get('siswa', true);
        $data = ['user' => $user, 'judul' => 'Buku Induk', 'subjudul' => 'Buku Induk', 'setting' => $setting];
        if ($mode == '2') {
        }
        $this->load->view('rapor/editrapor');
        $data['keterampilan'] = $this->rapor->getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->view('rapor/editrapor');
        $data['guru'] = $guru;
        $this->load->view('_templates/dashboard/_footer');
        if ($mode == '1') {
        }
        $arrTp = $this->dashboard->getTahun();
        $this->load->view('members/guru/templates/header', $data);
        $setting = $this->dashboard->getSetting();
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->dashboard->getSemesterActive();
        $data['id_siswa'] = $id_siswa;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if ($mode == '3') {
        }
        $data['smt_active'] = $smt;
        $this->load->view('_templates/dashboard/_header', $data);
        $arrSmt = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $user = $this->ion_auth->user()->row();
        if ($mode == '4') {
        }
        $mode = $this->input->get('mode', true);
        $this->load->model('Rapor_model', 'rapor');
        $this->load->view('members/guru/templates/footer');
        $data['tp'] = $arrTp;
        $data['mode'] = $mode;
    }
    public function getDataKelas()
    {
        $this->output_json(['kelas' => $kelass, 'jabatan' => $jabatan_guru]);
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $kelass = $this->dropdown->getAllKelas($id_tp, $id_smt);
        $id_tp = $this->input->get('tp', true);
        $jabatan_guru = $this->master->getAllJabatanGuru($guru->id_guru);
        $kelass = $this->dropdown->getAllKelasByArrayId($id_tp, $id_smt, [$id_kelas]);
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $id_kelas = $this->input->get('kls', true);
        $this->load->model('Master_model', 'master');
        $id_smt = $this->input->get('smt', true);
        if ($this->ion_auth->is_admin()) {
        }
        $jabatan_guru = null;
        $this->load->model('Dropdown_model', 'dropdown');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $id_tp, $id_smt);
    }
    public function backupNilai()
    {
        $res['ids'] = $ids_siswa;
        $this->load->model('Rapor_model', 'rapor');
        $kkms = $this->rapor->getAllKkm();
        $nilai_hpts = [];
        $nilai_sikap = $this->rapor->getAllNilaiSikap();
        $this->db->trans_complete();
        $smts = $this->dashboard->getSemester();
        $insert = [];
        $tps = $this->dashboard->getTahun();
        $mapels = $this->master->getAllMapel();
        $setting = $this->dashboard->getSetting();
        $res['nilai_ekstra'] = $mapels;
        $this->db->insert_batch('buku_nilai', $insert);
        $all_nilai = [];
        $res['insert'] = $insert;
        foreach ($tps as $tp) {
            foreach ($smts as $smt) {
                if (!(isset($all_nilai[$tp->id_tp]) && isset($all_nilai[$tp->id_tp][$smt->id_smt]))) {
                }
                foreach ($all_nilai[$tp->id_tp][$smt->id_smt] as $nilai) {
                    if ($this->rapor->exists($nilai['uid'], $nilai['tp'], $nilai['smt'], $nilai['kelas'])) {
                    }
                    $insert[] = $nilai;
                    $ids_siswa[$nilai['id_siswa']] = $nilai['id_siswa'];
                }
            }
        }
        $gurus = $this->master->getAllWaliKelas();
        $rapor_fisik = $this->rapor->getAllFisik();
        $this->output_json($res);
        $nilai_rapor = $this->rapor->getAllNilaiRapor();
        $setting_rapor = $this->rapor->getAllRaporSetting();
        $nilai_hph = [];
        $nilai_nr = [];
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        if (!(count($insert) > 0)) {
        }
        $this->rapor->deleteNilaiRapor();
        $nilai_ekstra = [];
        foreach ($nilai_rapor as $nilai) {
            foreach ($mapels as $mapel) {
                $nilai_hpts[$nilai->id_siswa][] = ['id_mapel' => $nilai->id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_tunggal ? $setting_rapor[$nilai->id_tp][$nilai->id_smt]->kkm : ($kkm_mapel == null ? '' : $kkm_mapel->kkm), 'nilai' => $nilai->nilai_pts, 'pred' => $nilai->pts_predikat];
                $nilai_hpas[$nilai->id_siswa][] = ['id_mapel' => $nilai->id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_tunggal ? $setting_rapor[$nilai->id_tp][$nilai->id_smt]->kkm : ($kkm_mapel == null ? '' : $kkm_mapel->kkm), 'nilai' => $nilai->nilai_pas];
                $nilai_nr[$nilai->id_siswa][] = ['id_mapel' => $nilai->id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_tunggal ? $setting_rapor[$nilai->id_tp][$nilai->id_smt]->kkm : ($kkm_mapel == null ? '' : $kkm_mapel->kkm), 'nilai' => $nilai->nilai_rapor, 'pred' => $nilai->rapor_predikat];
                if (!($mapel->id_mapel == $nilai->id_mapel)) {
                }
                $nilai_hph[$nilai->id_siswa][] = ['id_mapel' => $nilai->id_mapel, 'mapel' => $nilai->mapel, 'kkm' => $kkm_tunggal ? $setting_rapor[$nilai->id_tp][$nilai->id_smt]->kkm : ($kkm_mapel == null ? '' : $kkm_mapel->kkm), 'p_nilai' => $nilai->p_rata_rata, 'p_pred' => $nilai->p_predikat, 'p_desk' => $nilai->p_deskripsi, 'k_nilai' => $nilai->k_rata_rata, 'k_pred' => $nilai->k_predikat, 'k_desk' => $nilai->k_deskripsi];
            }
            $sosial = null;
            if (!(isset($nilai_extra[$nilai->id_tp]) && isset($nilai_extra[$nilai->id_tp][$nilai->id_smt]) && isset($nilai_extra[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa]))) {
            }
            foreach ($nilai_extra[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa] as $ekstra) {
                $kkm_ekstra = $all_kkm[2][$ekstra->id_ekstra]->kkm;
                if (!(isset($all_kkm[2]) && isset($all_kkm[2][$ekstra->id_ekstra]))) {
                }
                $kkm_ekstra = '';
                $nilai_ekstra[$nilai->id_siswa][] = ['mapel' => $ekstra->kode_ekstra, 'id_ekstra' => $ekstra->id_ekstra, 'nama_ekstra' => $ekstra->nama_ekstra, 'kkm' => $kkm_tunggal ? $setting_rapor[$nilai->id_tp][$nilai->id_smt]->kkm : $kkm_ekstra, 'nilai' => $ekstra->nilai, 'pred' => $ekstra->predikat, 'desk' => $ekstra->deskripsi];
            }
            $fisik[] = $rapor_fisik[$nilai->id_siswa][$nilai->id_tp][$nilai->id_smt];
            $fisik = [];
            $spiritual = isset($nilai_sikap[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa][1]) ? $nilai_sikap[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa][1] : null;
            $sosial = isset($nilai_sikap[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa][2]) ? $nilai_sikap[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa][2] : null;
            if (!(isset($nilai_sikap[$nilai->id_tp]) && isset($nilai_sikap[$nilai->id_tp][$nilai->id_smt]) && isset($nilai_sikap[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa]))) {
            }
            if (!isset($rapor_fisik[$nilai->id_siswa])) {
            }
            $nilai_ekstra = [];
            $kkm_mapel = null;
            $kkm_tunggal = $setting_rapor[$nilai->id_tp][$nilai->id_smt]->kkm_tunggal == '1';
            $all_nilai[$nilai->id_tp][$nilai->id_smt][$nilai->id_siswa] = ['uid' => $nilai->uid, 'id_siswa' => $nilai->id_siswa, 'tp' => $nilai->tahun, 'smt' => $nilai->nama_smt, 'kelas' => $nilai->nama_kelas, 'level' => $nilai->level_id, 'wali_kelas' => $nilai->nama_guru, 'jurusan' => $nilai->nama_jurusan, 'hph' => serialize(isset($nilai_hph[$nilai->id_siswa]) ? $nilai_hph[$nilai->id_siswa] : []), 'hpts' => serialize(isset($nilai_hpts[$nilai->id_siswa]) ? $nilai_hpts[$nilai->id_siswa] : []), 'hpas' => serialize(isset($nilai_hpas[$nilai->id_siswa]) ? $nilai_hpas[$nilai->id_siswa] : []), 'nilai_rapor' => serialize(isset($nilai_nr[$nilai->id_siswa]) ? $nilai_nr[$nilai->id_siswa] : []), 'ekstra' => serialize(isset($nilai_ekstra[$nilai->id_siswa]) ? $nilai_ekstra[$nilai->id_siswa] : ''), 'spritual' => $spiritual == null ? serialize([]) : serialize(['desk' => $spiritual->deskripsi, 'nilai' => unserialize($spiritual->nilai)['predikat']]), 'sosial' => $sosial == null ? serialize([]) : serialize(['desk' => $sosial->deskripsi, 'nilai' => unserialize($sosial->nilai)['predikat']]), 'rank' => serialize(['rank' => $nilai->ranking, 'saran' => $nilai->rank_deskripsi]), 'prestasi' => serialize([['nilai' => $nilai->p1, 'desk' => $nilai->p1_desk], ['nilai' => $nilai->p2, 'desk' => $nilai->p2_desk], ['nilai' => $nilai->p3, 'desk' => $nilai->p3_desk]]), 'absen' => $nilai->absen != null ? $nilai->absen : serialize([]), 'saran' => $nilai->saran != null ? $nilai->saran : '-', 'fisik' => serialize($fisik), 'naik' => $nilai->naik != null ? $nilai->naik : '1', 'setting_rapor' => serialize((array) $setting_rapor[$nilai->id_tp][$nilai->id_smt]), 'setting_mapel' => serialize((array) $mapels)];
            $kkm_mapel = isset($all_kkm[1]) && isset($all_kkm[1][$nilai->id_mapel]) ? $all_kkm[1][$nilai->id_mapel] : null;
            $all_kkm = [];
            $spiritual = null;
            if (!(isset($kkms[$nilai->id_tp]) && isset($kkms[$nilai->id_tp][$nilai->id_smt]) && isset($kkms[$nilai->id_tp][$nilai->id_smt][$nilai->id_kelas]))) {
            }
            $all_kkm = $kkms[$nilai->id_tp][$nilai->id_smt][$nilai->id_kelas];
        }
        $ids_siswa = [];
        $this->db->trans_start();
        $nilai_extra = $this->rapor->getAllNilaiEkstra();
        $res['all_nilai'] = $all_nilai;
        $nilai_hpas = [];
        $kelas_ekstra = $this->rapor->getAllEkstra();
    }
    public function restoreNilai()
    {
        $tps = $this->dashboard->getTahun();
        if (!(count($absen_insert) > 0)) {
        }
        $absen_insert = [];
        if (!(count($rank_insert) > 0)) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $res += $this->db->insert_batch('rapor_prestasi', $rank_insert);
        $rank_insert = [];
        foreach ($tps as $tp) {
            foreach ($smts as $smt) {
                if (!(isset($sosial[$tp->id_tp]) && isset($sosial[$tp->id_tp][$smt->id_smt]))) {
                }
                if (!(isset($ekstra[$tp->id_tp]) && isset($ekstra[$tp->id_tp][$smt->id_smt]))) {
                }
                if (!(isset($hpas[$tp->id_tp]) && isset($hpas[$tp->id_tp][$smt->id_smt]))) {
                }
                if (!(isset($spritual[$tp->id_tp]) && isset($spritual[$tp->id_tp][$smt->id_smt]))) {
                }
                foreach ($spritual[$tp->id_tp][$smt->id_smt] as $id => $pht) {
                    foreach ($pht as $kls => $nilai) {
                        $vals = ['id_nilai_sikap' => $kls . $id . $tp->id_tp . $smt->id_smt . '1', 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'jenis' => '1', 'nilai' => serialize(['predikat' => $nilai['nilai'], 'sl1' => '', 'sl2' => '', 'sl3' => '', 'mb1' => '', 'mb2' => '', 'mb3' => '']), 'deskripsi' => $nilai['desk']];
                        $spritual_insert[] = $vals;
                    }
                }
                if (!(isset($rank[$tp->id_tp]) && isset($rank[$tp->id_tp][$smt->id_smt]))) {
                }
                foreach ($rank[$tp->id_tp][$smt->id_smt] as $id => $pht) {
                    foreach ($pht as $kls => $nilai) {
                        $vals = ['id_ranking' => $kls . $id . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'ranking' => $nilai['rank'], 'deskripsi' => $nilai['saran'], 'p1' => $prt[0]['nilai'], 'p1_desk' => $prt[0]['desk'], 'p2' => $prt[1]['nilai'], 'p2_desk' => $prt[1]['desk'], 'p3' => $prt[2]['nilai'], 'p3_desk' => $prt[2]['desk']];
                        $prt = $prestasi[$tp->id_tp][$smt->id_smt][$id][$kls];
                        $rank_insert[] = $vals;
                    }
                }
                foreach ($absen[$tp->id_tp][$smt->id_smt] as $id => $pht) {
                    foreach ($pht as $kls => $nilai) {
                        $absen_insert[] = $vals;
                        $vals = ['id_catatan_wali' => $kls . $id . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'nilai' => $nilai['nilai'], 'deskripsi' => $nilai['deskripsi']];
                    }
                }
                foreach ($sosial[$tp->id_tp][$smt->id_smt] as $id => $pht) {
                    foreach ($pht as $kls => $nilai) {
                        $vals = ['id_nilai_sikap' => $kls . $id . $tp->id_tp . $smt->id_smt . '2', 'id_siswa' => $id, 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'jenis' => '2', 'nilai' => serialize(['predikat' => $nilai['nilai'], 'sl1' => '', 'sl2' => '', 'sl3' => '', 'mb1' => '', 'mb2' => '', 'mb3' => '']), 'deskripsi' => $nilai['desk']];
                        $sosial_insert[] = $vals;
                    }
                }
                foreach ($hpts[$tp->id_tp][$smt->id_smt] as $id => $pht) {
                    foreach ($pht as $kls => $nilai) {
                        foreach ($nilai as $ph) {
                            $hpts_insert[] = $vals;
                            $vals = ['id_nilai_pts' => $ph['id_mapel'] . $kls . $id . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id, 'id_mapel' => $ph['id_mapel'], 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'nilai' => $ph['nilai'], 'predikat' => $ph['pred']];
                        }
                    }
                }
                foreach ($hpas[$tp->id_tp][$smt->id_smt] as $id => $pha) {
                    foreach ($pha as $kls => $nilai) {
                        foreach ($nilai as $ph) {
                            $nr = $nilai_rapor[$tp->id_tp][$smt->id_smt][$id][$kls];
                            $hnr = $nr[$index];
                            $hpas_insert[] = $vals;
                            $vals = ['id_nilai_akhir' => $ph['id_mapel'] . $kls . $id . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id, 'id_mapel' => $ph['id_mapel'], 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'nilai' => $ph['nilai'], 'akhir' => $hnr['nilai'], 'predikat' => $hnr['pred']];
                            $index = array_search($ph['id_mapel'], array_column($nr, 'id_mapel'));
                        }
                    }
                }
                if (!(isset($hph[$tp->id_tp]) && isset($hph[$tp->id_tp][$smt->id_smt]))) {
                }
                if (!(isset($absen[$tp->id_tp]) && isset($absen[$tp->id_tp][$smt->id_smt]))) {
                }
                foreach ($hph[$tp->id_tp][$smt->id_smt] as $id => $phs) {
                    foreach ($phs as $kls => $nilai) {
                        foreach ($nilai as $ph) {
                            $p_rata = (int) $ph['p_nilai'];
                            $k_rata = (int) $ph['k_nilai'];
                            $hph_insert[] = $vals;
                            $vals = ['id_nilai_harian' => $ph['id_mapel'] . $kls . $id . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id, 'id_mapel' => $ph['id_mapel'], 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'p_rata_rata' => $p_rata, 'p1' => $p_rata + 1, 'p2' => $p_rata - 1, 'p3' => $p_rata, 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_predikat' => $ph['p_pred'], 'p_deskripsi' => $ph['p_desk'], 'k_rata_rata' => $k_rata, 'k1' => $k_rata + 1, 'k2' => $k_rata - 1, 'k3' => $k_rata, 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_predikat' => $ph['k_pred'], 'k_deskripsi' => $ph['k_desk'], 'jml' => ''];
                        }
                    }
                }
                if (!(isset($hpts[$tp->id_tp]) && isset($hpts[$tp->id_tp][$smt->id_smt]))) {
                }
                foreach ($ekstra[$tp->id_tp][$smt->id_smt] as $id => $pha) {
                    foreach ($pha as $kls => $nilai) {
                        if (!($nilai != '')) {
                        }
                        foreach ($nilai as $ph) {
                            $ekstra_insert[] = $vals;
                            $vals = ['id_nilai_ekstra' => $ph['id_ekstra'] . $kls . $id . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id, 'id_ekstra' => $ph['id_ekstra'], 'id_kelas' => $kls, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'nilai' => $ph['nilai'], 'predikat' => $ph['pred'], 'deskripsi' => $ph['desk']];
                        }
                    }
                }
            }
        }
        $this->db->trans_start();
        $absen = [];
        $rank = [];
        if (!(count($spritual_insert) > 0)) {
        }
        $res += $this->db->insert_batch('rapor_nilai_ekstra', $ekstra_insert);
        $hpas_insert = [];
        $res += $this->db->insert_batch('rapor_nilai_sikap', $sosial_insert);
        $spritual = [];
        $mapels = $this->master->getAllMapel();
        $res += $this->db->insert_batch('rapor_nilai_pts', $hpts_insert);
        foreach ($siswas as $id => $siswa) {
            $index_smt = array_search($siswa->smt, array_column($smts, 'nama_smt'));
            $absen[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = ['nilai' => $siswa->absen, 'deskripsi' => $siswa->saran];
            $tp = $tps[$index_tp];
            $hpas[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->hpas);
            $hpts[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->hpts);
            $id_kelas = '';
            foreach ($kelass as $kelas) {
                if (!($kelas->id_tp == $tp->id_tp && $kelas->id_smt == $smt->id_smt && $kelas->nama_kelas == $siswa->kelas)) {
                }
                $id_kelas = $kelas->id_kelas;
            }
            $prestasi[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->prestasi);
            $rank[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->rank);
            $spritual[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->spritual);
            $nilai_rapor[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->nilai_rapor);
            $fisik[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->fisik);
            $hph[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->hph);
            $sosial[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->sosial);
            $smt = $smts[$index_smt];
            foreach ($fisik[$tp->id_tp][$smt->id_smt][$id][$id_kelas] as $value) {
                $value->kondisi = unserialize($value->kondisi);
            }
            $ekstra[$tp->id_tp][$smt->id_smt][$id][$id_kelas] = unserialize($siswa->ekstra);
            $index_tp = array_search($siswa->tp, array_column($tps, 'tahun'));
        }
        $hpts = [];
        $res += $this->db->insert_batch('rapor_catatan_wali', $absen_insert);
        $res += $this->db->insert_batch('rapor_nilai_harian', $hph_insert);
        if (!(count($hph_insert) > 0)) {
        }
        if (!(count($hpts_insert) > 0)) {
        }
        if (!(count($hpas_insert) > 0)) {
        }
        return $res;
        $this->load->model('Master_model', 'master');
        if (!$res) {
        }
        $nilai_rapor = [];
        $this->db->trans_complete();
        $res = 0;
        $smts = $this->dashboard->getSemester();
        $kelass = $this->kelas->getAllKelas();
        $hpas = [];
        $ekstra = [];
        $siswas = $this->rapor->getDataKumpulanRapor();
        $hpts_insert = [];
        $sosial_insert = [];
        if (!(count($sosial_insert) > 0)) {
        }
        $fisik = [];
        $sosial = [];
        $res += $this->db->insert_batch('rapor_nilai_akhir', $hpas_insert);
        $this->db->empty_table('buku_nilai');
        $hph_insert = [];
        $spritual_insert = [];
        $gurus = $this->master->getAllWaliKelas();
        $this->load->model('Kelas_model', 'kelas');
        $hph = [];
        if (!(count($ekstra_insert) > 0)) {
        }
        $fisik_insert = [];
        $res += $this->db->insert_batch('rapor_nilai_sikap', $spritual_insert);
        $ekstra_insert = [];
        $prestasi = [];
        $this->load->model('Rapor_model', 'rapor');
    }
    public function edit()
    {
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Edit Nilai', 'subjudul' => 'Nilai Rapor Kelas ' . $kelas . ', TP:' . $tahun . ', SMT:' . $semester, 'setting' => $setting];
        $this->load->model('Dashboard_model', 'dashboard');
        $tahun = $this->input->get('tahun', true);
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $this->load->view('_templates/dashboard/_footer');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['siswas'] = $siswas;
        $this->load->view('setting/datarapor');
        $data['guru'] = $guru;
        $this->load->view('setting/datarapor');
        $setting = $this->dashboard->getSetting();
        $siswas = $this->rapor->getDataKumpulanRapor($kelas, $tahun, $semester);
        $data['smt_active'] = $smt;
        foreach ($siswas as $siswa) {
            $siswa->sosial = unserialize($siswa->sosial);
            $siswa->fisik = unserialize($siswa->fisik);
            $siswa->setting_rapor = unserialize($siswa->setting_rapor);
            $siswa->absen = unserialize($siswa->absen);
            $siswa->spritual = unserialize($siswa->spritual);
            $siswa->ekstra = unserialize($siswa->ekstra);
            $siswa->hpts = unserialize($siswa->hpts);
            $siswa->prestasi = unserialize($siswa->prestasi);
            $siswa->setting_mapel = unserialize($siswa->setting_mapel);
            $siswa->hph = unserialize($siswa->hph);
            $siswa->nilai_rapor = unserialize($siswa->nilai_rapor);
            foreach ($siswa->fisik as $value) {
                $value->kondisi = unserialize($value->kondisi);
            }
            $siswa->hpas = unserialize($siswa->hpas);
            $siswa->rank = unserialize($siswa->rank);
        }
        $data['tp'] = $this->dashboard->getTahun();
        $kelas = $this->input->get('kelas', true);
        $semester = $this->input->get('semester', true);
        $user = $this->ion_auth->user()->row();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->model('Rapor_model', 'rapor');
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/templates/footer');
    }
    public function ledger()
    {
        $this->load->view('setting/datarapor');
        $setting = $this->dashboard->getSetting();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $kelas = $this->input->get('kelas', true);
        $data['tp'] = $this->dashboard->getTahun();
        $data['siswas'] = $siswas;
        foreach ($siswas as $siswa) {
            $siswa->ekstra = unserialize($siswa->ekstra);
            $siswa->nilai_rapor = unserialize($siswa->nilai_rapor);
            $siswa->hpas = unserialize($siswa->hpas);
            $siswa->setting_mapel = unserialize($siswa->setting_mapel);
            $siswa->rank = unserialize($siswa->rank);
            $siswa->fisik = unserialize($siswa->fisik);
            $siswa->sosial = unserialize($siswa->sosial);
            $siswa->setting_rapor = unserialize($siswa->setting_rapor);
            $siswa->prestasi = unserialize($siswa->prestasi);
            $siswa->spritual = unserialize($siswa->spritual);
            $siswa->absen = unserialize($siswa->absen);
            $siswa->hpts = unserialize($siswa->hpts);
            foreach ($siswa->fisik as $value) {
                $value->kondisi = unserialize($value->kondisi);
            }
            $siswa->hph = unserialize($siswa->hph);
        }
        $smt = $this->dashboard->getSemesterActive();
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/footer');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $tahun = $this->input->get('tahun', true);
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Rapor_model', 'rapor');
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $siswas = $this->rapor->getDataKumpulanRapor($kelas, $tahun, $semester);
        $tp = $this->dashboard->getTahunActive();
        $data['smt_active'] = $smt;
        $this->load->view('setting/datarapor');
        $semester = $this->input->get('semester', true);
        $data = ['user' => $user, 'judul' => 'Edit Nilai', 'subjudul' => 'Nilai Rapor Kelas ' . $kelas . ', TP:' . $tahun . ', SMT:' . $semester, 'setting' => $setting];
        $nguru[$guru->id_guru] = $guru->nama_guru;
    }
    public function dkn()
    {
        $user = $this->ion_auth->user()->row();
        $data['smt_active'] = $smt;
        $data = ['user' => $user, 'judul' => 'Edit Nilai', 'subjudul' => 'Nilai Rapor Kelas ' . $kelas . ', TP:' . $tahun . ', SMT:' . $semester, 'setting' => $setting];
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Rapor_model', 'rapor');
        $data['siswas'] = $siswas;
        $this->load->view('setting/datarapor');
        foreach ($siswas as $siswa) {
            $siswa->hpas = unserialize($siswa->hpas);
            $siswa->fisik = unserialize($siswa->fisik);
            $siswa->rank = unserialize($siswa->rank);
            $siswa->spritual = unserialize($siswa->spritual);
            $siswa->setting_mapel = unserialize($siswa->setting_mapel);
            $siswa->sosial = unserialize($siswa->sosial);
            $siswa->hpts = unserialize($siswa->hpts);
            $siswa->nilai_rapor = unserialize($siswa->nilai_rapor);
            $siswa->prestasi = unserialize($siswa->prestasi);
            $siswa->ekstra = unserialize($siswa->ekstra);
            $siswa->absen = unserialize($siswa->absen);
            $siswa->hph = unserialize($siswa->hph);
            foreach ($siswa->fisik as $value) {
                $value->kondisi = unserialize($value->kondisi);
            }
            $siswa->setting_rapor = unserialize($siswa->setting_rapor);
        }
        $data['tp_active'] = $tp;
        $semester = $this->input->get('semester', true);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $setting = $this->dashboard->getSetting();
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_footer');
        $siswas = $this->rapor->getDataKumpulanRapor($kelas, $tahun, $semester);
        $tahun = $this->input->get('tahun', true);
        $data['tp'] = $this->dashboard->getTahun();
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->model('Dashboard_model', 'dashboard');
        $kelas = $this->input->get('kelas', true);
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('setting/datarapor');
        $data['guru'] = $guru;
        $smt = $this->dashboard->getSemesterActive();
    }
    function group_by($key, $data)
    {
        return $result;
        foreach ($data as $val) {
            $result[''][] = $val;
            $result[$val->{$key}][] = $val;
            if (array_key_exists($key, $val)) {
            }
        }
        $result = array();
    }
}
```

---

## File: application/controllers_decoded/Cbtalokasi.php

```php
<?php

class Cbtalokasi extends CI_Controller
{
    public function __construct()
    {
        redirect('auth');
        $this->load->library(['datatables', 'form_validation']);
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        parent::__construct();
        if ($this->ion_auth->is_admin()) {
        }
        $this->form_validation->set_error_delimiters('', '');
        if (!$this->ion_auth->logged_in()) {
        }
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        if ($setting->jenjang == '3') {
        }
        $jadwals = [];
        $tp = $this->dashboard->getTahunActive();
        if ($setting->jenjang == '2') {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $data['filter'] = ['0' => 'Semua', '1' => 'Tanggal'];
        if ($setting->jenjang == '1') {
        }
        $levels = ['0' => 'Pilih Level', '7' => '7', '8' => '8', '9' => '9'];
        $this->load->view('cbt/alokasi/data');
        $id_jenis = $this->cbt->getDistinctJenisJadwal($tp->id_tp, $smt->id_smt);
        $data['ruang'] = $this->dropdown->getAllRuang();
        $setting = $this->dashboard->getSetting();
        $dari_selected = $this->input->get('dari', true);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['jenis_selected'] = $jenis_selected;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        foreach ($jadwals as $key => $row) {
            $ret[$row->tgl_mulai] = [];
            array_push($ret[$row->tgl_mulai], $row);
            array_push($ret[$row->tgl_mulai], $row);
            if (isset($ret[$row->tgl_mulai])) {
            }
        }
        $data['jenis'] = $this->cbt->getAllJenisUjianByArrJenis($ids);
        foreach ($id_jenis as $jenis) {
            array_push($ids, $jenis->id_jenis);
        }
        $data['level_selected'] = $level_selected;
        $data['jenis'] = ['' => 'belum ada jadwal ujian'];
        $ret = [];
        $data['levels'] = $levels;
        $this->load->model('Cbt_model', 'cbt');
        $data['tp_active'] = $tp;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_footer');
        $filter_selected = $this->input->get('filter', true);
        $smt = $this->dashboard->getSemesterActive();
        $ids = [];
        $data['sampai_selected'] = $sampai_selected;
        $levels = ['0' => 'Pilih Level', '10' => '10', '11' => '11', '12' => '12'];
        if (!($id_jenis && count($id_jenis) > 0)) {
        }
        $levels = [];
        $this->load->view('_templates/dashboard/_header', $data);
        $user = $this->ion_auth->user()->row();
        $data['smt_active'] = $smt;
        $data['filter_selected'] = $filter_selected;
        $data = ['user' => $user, 'judul' => 'Alokasi Waktu', 'subjudul' => 'Alokasi Waktu Ujian', 'setting' => $setting];
        $data['dari_selected'] = $dari_selected;
        $this->load->model('Dropdown_model', 'dropdown');
        if (!($jenis_selected != null && $level_selected != null)) {
        }
        $jenis_selected = $this->input->get('jenis', true);
        $sampai_selected = $this->input->get('sampai', true);
        $jadwals = $this->cbt->getJadwalByJenis($jenis_selected, $level_selected, $dari_selected, $sampai_selected);
        if ($ids && count($ids) > 0) {
        }
        $data['jadwals'] = $jadwals;
        $level_selected = $this->input->get('level', true);
        $levels = ['0' => 'Pilih Level', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function saveAlokasi()
    {
        $update = $this->db->update_batch('cbt_jadwal', $insert, 'id_jadwal');
        $insert = [];
        $input = json_decode($this->input->post('alokasi', true));
        foreach ($input as $d) {
            array_push($insert, ['id_jadwal' => $d->id_jadwal, 'jam_ke' => $d->jam_ke]);
            if (!($d->id_jadwal != '0')) {
            }
        }
        $this->output_json($data);
        $data['status'] = $update;
    }
}
```

---

## File: application/controllers_decoded/Cbtanalisis.php

```php
<?php

class Cbtanalisis extends CI_Controller
{
    public function __construct()
    {
        $this->form_validation->set_error_delimiters('', '');
        redirect('auth');
        parent::__construct();
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library(['datatables', 'form_validation']);
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $data['guru'] = $guru;
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->model('Cbt_model', 'cbt');
        $data['tp'] = $this->dashboard->getTahun();
        $smt = $this->dashboard->getSemesterActive();
        $all_jawaban = $this->cbt->getJawabanByBank($info->id_bank);
        $nilai_pg = $this->cbt->getAllNilaiSiswa($jadwal);
        $data['smt_active'] = $smt;
        $thn_sel = $thn_sel == null ? $tp->id_tp : $thn_sel;
        $data['tp_active'] = $tp;
        $info = $this->cbt->getJadwalById($jadwal);
        $this->load->model('Dropdown_model', 'dropdown');
        $data['nilai'] = $nilai_pg;
        $data['kodejadwal'] = $this->dropdown->getAllJadwalGuru($thn_sel, $smt_sel, $guru->id_guru);
        $user = $this->ion_auth->user()->row();
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $jawabans_siswa = [];
        $this->load->view('cbt/analisis/data');
        $data = ['user' => $user, 'judul' => 'Analisa Soal', 'subjudul' => 'Analisa Soal Ujian', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/templates/footer');
        $jadwal = $this->input->get('jadwal');
        $this->load->view('_templates/dashboard/_header', $data);
        foreach ($all_soals[1] as $no => $soal) {
            if ($kesukaran >= 0.3) {
            }
            $soal->status_daya = 'Baik';
            $cek = $jml_siswa % 2;
            $kesukaran = round($benar / $jml_siswa, 2);
            foreach ($jwbn_siswa as $id => $jawab_siswa) {
                $soal->jumlah_benar++;
                $soal->jawaban_siswa['jawab_e'][$id] = $jawab_siswa;
                array_push($x, 0);
                $soal->jawaban_siswa['jawab_a'][$id] = $jawab_siswa;
                $soal->jumlah_salah++;
                if ($jawab_siswa == 'D') {
                }
                if ($jawab_siswa == 'A') {
                }
                array_push($x, 1);
                if ($jawab_siswa == $soal->jawaban) {
                }
                $soal->jawaban_siswa['jawab_c'][$id] = $jawab_siswa;
                $soal->jawaban_siswa['jawab_d'][$id] = $jawab_siswa;
                $total_siswa++;
                if ($jawab_siswa == 'C') {
                }
                $soal->jawaban_siswa['jawab_b'][$id] = $jawab_siswa;
                if ($jawab_siswa == 'E') {
                }
                if ($jawab_siswa == 'B') {
                }
            }
            $soal->daya_pembeda = $daya_pembeda;
            $bagi = $jml_siswa / 2;
            if ($kesukaran >= 0.7) {
            }
            foreach ($nilai_pg as $id => $nilai) {
                $no++;
                $yng_benar_golonganbawah++;
                if (!($siswa_menjawab == $soal->jawaban)) {
                }
                $siswa_menjawab = $jwbn_siswa[$id];
                if (!isset($jwbn_siswa[$id])) {
                }
                if ($no <= $bagi) {
                }
                array_push($y, $nilai->pg_benar);
                if (!($siswa_menjawab == $soal->jawaban)) {
                }
                $yng_benar_golonganatas++;
            }
            $soal->jumlah_benar = 0;
            $soal->total_siswa = $total_siswa;
            $y = [];
            $soal->jumlah_salah = 0;
            $soal->jawaban_siswa = [];
            $soal->status_daya = 'Baik Sekali';
            $kesukaran = 0;
            $jwbn_siswa = isset($jawabans_siswa[1][$no]) && isset($jawabans_siswa[1][$no]) ? $jawabans_siswa[1][$no] : [];
            $daya_pembeda = $yng_benar_golonganatas / $bagi_daya;
            $yng_benar_golonganbawah = 0;
            $status_soal = 'mudah';
            if (!($cek == 1)) {
            }
            $soal->status_daya = 'Jelek';
            $soal->status_kesukaran = $status_soal;
            $yng_benar_golonganatas = 0;
            $jml_siswa = $total_siswa;
            $total_siswa = 0;
            $soal->skor_siswa = [];
            $daya_pembeda = $yng_benar_golonganatas / $bagi_daya - $yng_benar_golonganbawah / $bagi_daya;
            $benar = $soal->jumlah_benar;
            $status_soal = '';
            $jml_siswa--;
            $soal->tingkat_kesukaran = $kesukaran;
            $validitas = $this->nilaiSignifikansi($total_siswa) <= $pearson ? 'Valid' : 'Tidak valid';
            $status_soal = 'sedang';
            if ($yng_benar_golonganatas == 0 && $yng_benar_golonganbawah == 0) {
            }
            $soal->status_daya = 'Cukup';
            $salah = $soal->jumlah_salah;
            $soal->benar_bawah = $yng_benar_golonganbawah;
            $status_soal = 'sukar';
            $bagi_daya = $bagi > 0 ? $bagi : 1;
            $pearson = $this->pearson($x, $y);
            if ($yng_benar_golonganatas == 0 && $yng_benar_golonganbawah != 0) {
            }
            $no = 1;
            $daya_pembeda = 0;
            $soal->table_r = $this->nilaiSignifikansi($total_siswa);
            $x = [];
            $soal->benar_atas = $yng_benar_golonganatas;
            $soal->status_valid = $validitas;
            $daya_pembeda = 0 - $yng_benar_golonganbawah / $bagi_daya;
            if (!($jml_siswa > 0)) {
            }
            $pos_a = 0;
            $pos_b = $bagi;
            if ($daya_pembeda >= 0.7) {
            }
            $soal->nilai_valid = $pearson;
            if ($yng_benar_golonganatas != 0 && $yng_benar_golonganbawah == 0) {
            }
            if ($daya_pembeda >= 0.2) {
            }
            if ($daya_pembeda >= 0.4) {
            }
        }
        $smt_sel = $this->input->get('smt');
        if (!($jadwal != null)) {
        }
        $data['info'] = $info;
        if (!isset($all_soals[1])) {
        }
        $data['kodejadwal'] = $this->dropdown->getAllJadwal($thn_sel, $smt_sel);
        $data['smt_selected'] = $smt_sel;
        foreach ($all_jawaban as $jawaban_siswa) {
            $jawabans_siswa[$jawaban_siswa->jenis_soal][$jawaban_siswa->nomor_soal][$jawaban_siswa->id_siswa] = $jawaban_siswa->jawaban_siswa;
            array_push($ids, $jawaban_siswa->id_siswa);
        }
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('cbt/analisis/data');
        $data['soals'] = $all_soals;
        $tp = $this->dashboard->getTahunActive();
        $ids = [];
        $thn_sel = $this->input->get('thn');
        $smt_sel = $smt_sel == null ? $smt->id_smt : $smt_sel;
        $data['jadwal_selected'] = $jadwal;
        $data['tp_selected'] = $thn_sel;
        $all_soals = $this->cbt->getSoalByBank($info->id_bank);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Dashboard_model', 'dashboard');
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $thn_sel, $smt_sel);
    }
    private function pearson($x, $y)
    {
        $i = 0;
        $by += pow($yr, 2);
        $i++;
        return -1;
        $d = $cx - $cy;
        if (!($i < $d)) {
        }
        $xr = $x[$i] - $xs;
        $by = 0;
        $bx = 0;
        if (!($cx < $cy)) {
        }
        $b = sqrt($bx * $by);
        return $ret;
        $bx += pow($xr, 2);
        $i = 0;
        if (!($cx === 0 || $cy === 0)) {
        }
        $i++;
        $yr = $y[$i] - $ys;
        $ret = $a / $b;
        $a += $xr * $yr;
        $ys = array_sum($y) / count($y);
        $y = array_values($y);
        $cy = count($y);
        return -1;
        if (!($b > 0)) {
        }
        $i++;
        if (!(count($x) !== count($y))) {
        }
        $x = array_values($x);
        $ret = -1;
        array_pop($y);
        if (!($i < $d)) {
        }
        if (!($cx > $cy)) {
        }
        $a = 0;
        $d = $cy - $cx;
        if (!($i < count($x))) {
        }
        $cx = count($x);
        $i = 0;
        array_pop($x);
        $xs = array_sum($x) / count($x);
    }
    public function getNilaiKelas()
    {
        $data['info'] = $info;
        $this->load->model('Cbt_model', 'cbt');
        $info = $this->cbt->getJadwalById($jadwal, $sesi);
        $sesi = $this->input->get('sesi');
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($data);
        $kelas = $this->input->get('kelas');
        $data['siswa'] = $siswas;
        $data['jawaban'] = $arrDur;
        $arrDur = [];
        foreach ($siswas as $siswa) {
            $j++;
            $arrJawab_pg[$siswa->id_siswa][] = $this->cbt->getJawabanSiswa($siswa->id_siswa . '0' . $jadwal . $info->id_bank . 1 . ($i + 1));
            $arrJawab_essai[$siswa->id_siswa][] = array('id_jawaban' => 0, 'jawaban' => '', 'jawaban_benar' => '', 'koreksi' => 0);
            $jawaban = $this->cbt->getJawabanSiswa($siswa->id_siswa . '0' . $jadwal . $info->id_bank . 2 . ($j + 1));
            $i++;
            $j = 0;
            if (!($i < $info->tampil_pg)) {
            }
            $arrJawab_essai = [];
            $arrDur[$siswa->id_siswa] = ['dur' => $this->cbt->getDurasiSiswa($siswa->id_siswa . '0' . $jadwal), 'jawab_pg' => $arrJawab_pg[$siswa->id_siswa], 'jawab_essai' => $jawab_essai, 'log' => $this->cbt->getLogUjian($siswa->id_siswa, $jadwal)];
            if ($jawaban != null) {
            }
            $jawab_essai = isset($arrJawab_essai[$siswa->id_siswa]) ? $arrJawab_essai[$siswa->id_siswa] : [];
            $i = 0;
            $arrJawab_pg = [];
            if (!($j < $info->tampil_esai)) {
            }
            $arrJawab_essai[$siswa->id_siswa][] = $jawaban;
        }
        $jadwal = $this->input->get('jadwal');
        $tp = $this->dashboard->getTahunActive();
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas);
    }
    public function getJadwalUjianByJadwal()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $tp = $this->input->get('thn');
        $kelases = [];
        $smt = $this->input->get('smt');
        foreach ($kelas as $key => $value) {
            $kelases[$value['kelas_id']] = $this->dropdown->getNamaKelasById($info->id_tp, $info->id_smt, $value['kelas_id']);
        }
        $this->load->model('Cbt_model', 'cbt');
        $kelas = unserialize($info->bank_kelas ?? '');
        $jadwal = $this->input->get('jadwal');
        $this->output_json($kelases);
        $info = $this->cbt->getJadwalById($jadwal);
    }
    public function kalkulasi()
    {
        $update = $this->generateNilaiUjian($jadwal);
        $this->output_json($update);
        $jadwal = $this->input->get('jadwal');
    }
    public function generateNilaiUjian($jadwal)
    {
        $insets = [];
        foreach ($siswas as $siswa) {
            foreach ($jawaban_es as $num => $jawab_es) {
                if (!$benar) {
                }
                $otomatis_es = $jawab_es->nilai_otomatis;
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban_benar ?? '');
                $benar_es++;
            }
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            $input_es = 0;
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $input_jod = 0;
            $otomatis_jod = 0;
            $input_is = 0;
            $benar_is = 0;
            $input_es = $nilai_input->essai_nilai;
            $insert['jodohkan_nilai'] = round($skor_jod, 2);
            $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
            if (!($jawaban_pg && count($jawaban_pg) > 0)) {
            }
            $input_is = $nilai_input->isian_nilai;
            $input_pg2 = $nilai_input->kompleks_nilai;
            $insert['id_siswa'] = $siswa->id_siswa;
            $benar_pg2 = 0;
            if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
            }
            if (!($info->tampil_jodohkan > 0)) {
            }
            $input_jod = $nilai_input->jodohkan_nilai;
            foreach ($jawaban_is as $num => $jawab_is) {
                $otomatis_is = $jawab_is->nilai_otomatis;
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban_benar ?? '');
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                if (!$benar) {
                }
                $benar_is++;
            }
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
            $insert['id_jadwal'] = $jadwal;
            $skor_koreksi_es = 0.0;
            $otomatis_is = 0;
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                foreach ($arrJwbJawab as $p => $ajjs) {
                    foreach ($ajjs->subtitle as $pp => $ajs) {
                        $item_benar++;
                        $item_salah++;
                        if (in_array($ajs, $arrJwbSoal[$p]->subtitle)) {
                        }
                    }
                }
                $benar_jod += 1 / $items * $item_benar;
                $item_salah = 0;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
                $item_benar = 0;
                foreach ($arrJawab as $kolJawab) {
                    array_push($arrJwbJawab, $jwbs);
                    foreach ($kolJawab as $po => $kol) {
                        $jwbs->subtitle[] = $sub;
                        $sub = $headJawab[$po];
                        if (!($kol == '1')) {
                        }
                    }
                    $jwbs = new stdClass();
                }
                $arrJwbSoal = [];
                $arrJwbJawab = [];
                $arrSoal = $jawab_jod->jawaban_benar->jawaban;
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $items = 0;
                $headSoal = array_shift($arrSoal);
                $headJawab = array_shift($arrJawab);
                foreach ($arrSoal as $kolSoal) {
                    $jwb = new stdClass();
                    foreach ($kolSoal as $pos => $kol) {
                        $items++;
                        $jwb->subtitle[] = $headSoal[$pos];
                        if (!($kol == '1')) {
                        }
                    }
                    $jwb->title = array_shift($kolSoal);
                    array_push($arrJwbSoal, $jwb);
                }
                $arrJawab = $jawab_jod->jawaban_siswa->jawaban;
            }
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            if (!($jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            $input_pg2 = 0;
            $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            if (!($jawaban_pg2 && count($jawaban_pg2) > 0)) {
            }
            $insert['isian_nilai'] = round($skor_is, 2);
            $otomatis_pg2 = 0;
            $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
            if (!($info->tampil_esai > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    array_push($arr_benar, true);
                    if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                    }
                }
                $arr_benar = [];
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
            }
            foreach ($jawaban_pg as $jwb_pg) {
                if (strtoupper($jwb_pg->jawaban_siswa) == strtoupper($jwb_pg->jawaban_benar ?? '')) {
                }
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                $benar_pg += 1;
                $salah_pg += 1;
            }
            $skor_koreksi_pg2 = 0.0;
            $benar_es = 0;
            $otomatis_es = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            array_push($insets, $insert);
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $insert['pg_benar'] = $benar_pg;
            $salah_pg = 0;
            $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
            $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
            $insert['kompleks_nilai'] = round($skor_pg2, 2);
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            if (!($info->tampil_isian > 0)) {
            }
            if (!($info->tampil_pg > 0)) {
            }
            if (!($jawaban_es && count($jawaban_es) > 0)) {
            }
            $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
            $benar_jod = 0;
            $benar_pg = 0;
            $insert['pg_nilai'] = round($skor_pg, 2);
            $skor_koreksi_is = 0.0;
            $skor_koreksi_jod = 0.0;
            if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
            }
            $insert['essai_nilai'] = round($skor_es, 2);
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            if (!($jawaban_is && count($jawaban_is) > 0)) {
            }
            $insert['id_nilai'] = $siswa->id_siswa . '0' . $jadwal;
            $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
        }
        $soal = [];
        $bobot_pg = $info->bobot_pg / 100;
        $this->load->model('Cbt_model', 'cbt');
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank);
        foreach ($siswas as $key => $value) {
            array_push($ids, $value->id_siswa);
        }
        $kelases = [];
        return $update;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $siswas = $this->cbt->getSiswaByKelas($info->id_tp, $info->id_smt, $kelases);
        $kelas_bank = unserialize($info->bank_kelas ?? '');
        $jawabans_siswa = [];
        $info = $this->cbt->getJadwalById($jadwal);
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $ids = [];
        foreach ($jawabans as $jawaban_siswa) {
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            if (!($jawaban_siswa->jenis_soal == '2')) {
            }
            $jawaban_siswa->jawaban_benar = array_map('strtoupper', $jawaban_siswa->jawaban_benar ?? ['']);
            $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar ?? [''], 'strlen');
            if (!($jawaban_siswa->jenis_soal == '3')) {
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
        }
        $bagi_pg = $info->tampil_pg / 100;
        $bagi_isian = $info->tampil_isian / 100;
        $bobot_essai = $info->bobot_esai / 100;
        $update = $this->db->update_batch('cbt_nilai', $insets, 'id_nilai');
        foreach ($kelas_bank as $key => $value) {
            array_push($kelases, $value['kelas_id']);
        }
        $bobot_pg2 = $info->bobot_kompleks / 100;
    }
    private function nilaiSignifikansi($jml)
    {
        $keys = $this->getClosest($jml, array_keys($list));
        $list = [3 => [5 => 0.997], [1 => 0.999], 4 => [5 => 0.95], [1 => 0.99], 5 => [5 => 0.878], [1 => 0.959], 6 => [5 => 0.8110000000000001], [1 => 0.917], 7 => [5 => 0.754], [1 => 0.874], 8 => [5 => 0.707], [1 => 0.834], 9 => [5 => 0.666], [1 => 0.798], 10 => [5 => 0.632], [1 => 0.765], 11 => [5 => 0.602], [1 => 0.735], 12 => [5 => 0.576], [1 => 0.708], 13 => [5 => 0.553], [1 => 0.6840000000000001], 14 => [5 => 0.532], [1 => 0.661], 15 => [5 => 0.514], [1 => 0.641], 16 => [5 => 0.497], [1 => 0.623], 17 => [5 => 0.482], [1 => 0.606], 18 => [5 => 0.468], [1 => 0.59], 19 => [5 => 0.456], [1 => 0.575], 20 => [5 => 0.444], [1 => 0.5610000000000001], 21 => [5 => 0.433], [1 => 0.549], 22 => [5 => 0.423], [1 => 0.537], 23 => [5 => 0.413], [1 => 0.526], 24 => [5 => 0.404], [1 => 0.515], 25 => [5 => 0.396], [1 => 0.505], 26 => [5 => 0.388], [1 => 0.496], 27 => [5 => 0.381], [1 => 0.487], 28 => [5 => 0.374], [1 => 0.478], 29 => [5 => 0.367], [1 => 0.47], 30 => [5 => 0.361], [1 => 0.463], 31 => [5 => 0.355], [1 => 0.456], 32 => [5 => 0.349], [1 => 0.449], 33 => [5 => 0.344], [1 => 0.442], 34 => [5 => 0.339], [1 => 0.436], 35 => [5 => 0.334], [1 => 0.43], 36 => [5 => 0.329], [1 => 0.424], 37 => [5 => 0.325], [1 => 0.418], 38 => [5 => 0.32], [1 => 0.413], 39 => [5 => 0.316], [1 => 0.408], 40 => [5 => 0.312], [1 => 0.403], 41 => [5 => 0.308], [1 => 0.398], 42 => [5 => 0.304], [1 => 0.393], 43 => [5 => 0.301], [1 => 0.389], 44 => [5 => 0.297], [1 => 0.384], 45 => [5 => 0.294], [1 => 0.38], 46 => [5 => 0.291], [1 => 0.376], 47 => [5 => 0.288], [1 => 0.372], 48 => [5 => 0.284], [1 => 0.368], 49 => [5 => 0.281], [1 => 0.364], 50 => [5 => 0.279], [1 => 0.361], 55 => [5 => 0.266], [1 => 0.345], 60 => [5 => 0.254], [1 => 0.33], 65 => [5 => 0.244], [1 => 0.317], 70 => [5 => 0.235], [1 => 0.306], 75 => [5 => 0.227], [1 => 0.296], 80 => [5 => 0.22], [1 => 0.286], 85 => [5 => 0.213], [1 => 0.278], 90 => [5 => 0.207], [1 => 0.27], 95 => [5 => 0.202], [1 => 0.263], 100 => [5 => 0.195], [1 => 0.256], 125 => [5 => 0.176], [1 => 0.23], 150 => [5 => 0.159], [1 => 0.21], 175 => [5 => 0.149], [1 => 0.194], 200 => [5 => 0.138], [1 => 0.191], 300 => [5 => 0.113], [1 => 0.181], 400 => [5 => 0.098], [1 => 0.148], 500 => [5 => 0.08799999999999999], [1 => 0.128], 600 => [5 => 0.08], [1 => 0.115], 700 => [5 => 0.074], [1 => 0.105], 800 => [5 => 0.07000000000000001], [1 => 0.091], 900 => [5 => 0.065], [1 => 0.08599999999999999], 1000 => [5 => 0.062], [1 => 0.081]];
        return $list[$keys]['1'];
        if (isset($list[$keys]['5'])) {
        }
        $keys = 4;
        if (!($keys < 4)) {
        }
        if (isset($list[$jml]['5'])) {
        }
        return $list[$jml]['1'];
        return $list[$keys]['5'];
        if (isset($list[$jml])) {
        }
        return $list[$jml]['5'];
    }
    function getClosest($search, $arr)
    {
        $closest = null;
        foreach ($arr as $item) {
            if (!($closest === null || abs($search - $closest) > abs($item - $search))) {
            }
            $closest = $item;
        }
        return $closest;
    }
}
```

---

## File: application/controllers_decoded/Cbtbanksoal.php

```php
<?php

class Cbtbanksoal extends CI_Controller
{
    public function __construct()
    {
        $this->load->library('upload');
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library(['datatables', 'form_validation']);
        redirect('auth');
        parent::__construct();
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        if (!$this->ion_auth->logged_in()) {
        }
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $banks = $this->cbt->getDataBank($guru->id_guru, $id_mapel);
        foreach ($banks as $bank) {
            foreach ($bank as $tp) {
                foreach ($tp as $smt) {
                    $ids[] = $smt->id_bank;
                }
            }
        }
        $banks = [];
        $data['id_mapel'] = '';
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('cbt/banksoal/data');
        $banks = $this->cbt->getDataBank($id_guru);
        $type = $this->input->get('type');
        $data['id_guru'] = null;
        $user = $this->ion_auth->user()->row();
        if ($type == '2') {
        }
        $data['filters'] = ['0' => 'Semua', '2' => 'Mapel', '3' => 'Level'];
        $data['id_mapel'] = $id_mapel;
        $data['id_guru'] = '';
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['tp_active'] = $tp;
        $data['id_level'] = '';
        $data['id_filter'] = $type == null ? '' : $type;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $jadwal_terpakai = [];
        $data['filters'] = ['0' => 'Semua', '1' => 'Guru', '2' => 'Mapel', '3' => 'Level'];
        $data['id_mapel'] = null;
        $data['smt'] = $this->dashboard->getSemester();
        $banks = $this->cbt->getDataBank($guru->id_guru, null, $id_level);
        $data['id_guru'] = '';
        $data['levels'] = $this->dropdown->getAllLevel($setting->jenjang);
        $this->load->view('members/guru/templates/footer');
        $setting = $this->dashboard->getSetting();
        $data['id_guru'] = '';
        $data['id_level'] = null;
        if ($type == '3') {
        }
        if (!($ids && count($ids) > 0)) {
        }
        $data['gurus'] = $nguru;
        if ($type == '2') {
        }
        $terpakai = $this->cbt->getBankTerpakai($ids);
        $tp = $this->master->getTahunActive();
        $id_level = $this->input->get('id');
        $id_mapel = $this->input->get('id');
        $this->load->view('members/guru/templates/header', $data);
        $data['id_guru'] = $guru->id_guru;
        $data['id_mapel'] = '';
        $this->load->view('_templates/dashboard/_header', $data);
        $data['id_level'] = $id_level;
        $this->load->model('Dashboard_model', 'dashboard');
        $data['id_guru'] = '';
        foreach ($terpakai as $idj => $rows) {
            $jadwal_terpakai[$idj] = count($rows);
            if (!$rows) {
            }
        }
        if ($type == '1') {
        }
        $data['total_siswa'] = $jadwal_terpakai;
        $data['id_mapel'] = $id_mapel;
        $data = ['user' => $user, 'judul' => 'Bank Soal', 'subjudul' => 'Soal', 'setting' => $setting];
        $data['id_filter'] = $type == null ? '' : $type;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $banks = $this->cbt->getDataBank($guru->id_guru);
        $data['id_level'] = '';
        $this->load->model('Cbt_model', 'cbt');
        if ($type == '3') {
        }
        $mode = $this->input->get('mode');
        $data['gurus'] = $this->dropdown->getAllGuru();
        $id_guru = $this->input->get('id');
        $data['mode'] = $mode == null ? '1' : $mode;
        $data['banks'] = $banks;
        $this->load->model('Master_model', 'master');
        $terpakai = $this->cbt->getBankTerpakai($ids);
        $this->load->model('Dropdown_model', 'dropdown');
        $id_mapel = $this->input->get('id');
        if ($this->ion_auth->is_admin()) {
        }
        $data['id_level'] = '';
        $smt = $this->master->getSemesterActive();
        if (!($ids && count($ids) > 0)) {
        }
        if (!($banks && count($banks) > 0)) {
        }
        $this->load->view('cbt/banksoal/data');
        if (!($type != null)) {
        }
        $banks = $this->cbt->getDataBank(null, null, $id_level);
        $data['id_level'] = '';
        $data['id_level'] = $id_level;
        $data['mapels'] = $this->dropdown->getAllMapel();
        $data['tp'] = $this->dashboard->getTahun();
        if (!($banks && count($banks) > 0)) {
        }
        if (!($type != null)) {
        }
        $id_level = $this->input->get('id');
        if ($type == '0') {
        }
        $data['id_mapel'] = '';
        $banks = $this->cbt->getDataBank();
        $data['id_guru'] = null;
        $ids = [];
        $data['id_mapel'] = '';
        foreach ($banks as $bank) {
            foreach ($bank as $tp) {
                foreach ($tp as $smt) {
                    $ids[] = $smt->id_bank;
                }
            }
        }
        $data['id_mapel'] = null;
        $banks = $this->cbt->getDataBank(null, $id_mapel);
        $data['id_guru'] = $id_guru;
        $data['total_siswa'] = $jadwal_terpakai;
        $data['banks'] = $banks;
        $jadwal_terpakai = [];
        $data['id_level'] = null;
        foreach ($terpakai as $idj => $rows) {
            if (!$rows) {
            }
            $jadwal_terpakai[$idj] = count($rows);
        }
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $ids = [];
        $banks = [];
    }
    public function data($guru = null)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getDataBank($guru), false);
    }
    public function dataTable($guru = null)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getDataTableBank($guru), false);
    }
    public function getMapelGuru()
    {
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $this->load->model('Kelas_model', 'kelas');
        $smt = $this->master->getSemesterActive();
        if (!($mapel != null)) {
        }
        $tp = $this->master->getTahunActive();
        $this->output_json($arrMapel);
        $arrMapel = [];
        $this->load->model('Master_model', 'master');
        $id_guru = $this->input->get('id_guru', true);
    }
    public function getGuruMapel()
    {
        $mapel_guru = $this->kelas->getMapelGuruKelas($tp->id_tp, $smt->id_smt);
        $tp = $this->master->getTahunActive();
        $id_mapel = $this->input->get('id_mapel', true);
        $arrGuru = [];
        $this->load->model('Kelas_model', 'kelas');
        $smt = $this->master->getSemesterActive();
        $this->load->model('Master_model', 'master');
        $this->output_json($arrGuru);
        foreach ($mapel_guru as $guru) {
            if (!($mapel != null)) {
            }
            foreach ($mapel as $m) {
                $arrGuru[$guru->id_guru] = $guru->nama_guru;
                if (!(isset($m->id_mapel) && $m->id_mapel == $id_mapel)) {
                }
            }
            $mapel = json_decode(json_encode(unserialize($guru->mapel_kelas ?? '')));
        }
    }
    public function getKelasLevel()
    {
        $id_mapel = $this->input->get('mapel', true);
        $arrKelas = [];
        $arrMapel = [];
        $this->load->model('Kelas_model', 'kelas');
        foreach ($mapel as $m) {
            foreach ($m->kelas_mapel as $kls) {
                array_push($arrKelas, $kls->kelas);
            }
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            if (!($id_mapel === $m->id_mapel)) {
            }
        }
        $level = $this->input->get('level', true);
        $this->output_json(['mapel' => $arrMapel, 'kelas' => count($arrKelas) > 0 ? $this->cbt->getKelasByLevel($level, $arrKelas) : []]);
        $this->load->model('Master_model', 'master');
        $id_guru = $this->input->get('id_guru', true);
        $tp = $this->master->getTahunActive();
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $smt = $this->master->getSemesterActive();
        if (!($mapel !== false)) {
        }
        $this->load->model('Cbt_model', 'cbt');
        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
    }
    public function addBank()
    {
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['jurusan'] = $this->cbt->getAllJurusan();
        $data['bank'] = json_decode(json_encode($this->cbt->dummy($setting->jenjang)));
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['id_guru'] = '';
        $data['mapel_agama'] = $this->master->getAgamaSiswa();
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt_active'] = $smt;
        $arrKelas = [];
        if ($this->ion_auth->is_admin()) {
        }
        if (!($mapel && count($mapel) > 0)) {
        }
        $data['kelas'] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
        $data['gurus'] = $nguru;
        $setting = $this->dashboard->getSetting();
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $this->load->model('Master_model', 'master');
        $this->load->view('_templates/dashboard/_footer');
        $data['mapel'] = $this->dropdown->getAllMapel();
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $data['mapel_guru'] = $mapel_guru;
        $arrId = [];
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $tp = $this->master->getTahunActive();
        $data['setting'] = $this->dashboard->getSetting();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/banksoal/add');
        $data['arrkelas'] = $arrKelas;
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $user = $this->ion_auth->user()->row();
        foreach ($mapel[0]->kelas_mapel as $id_mapel) {
            array_push($arrId, $id_mapel->kelas);
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['mapel'] = $arrMapel;
        if (!($mapel !== false)) {
        }
        $arrMapel = [];
        $smt = $this->master->getSemesterActive();
        $this->load->view('members/guru/templates/header', $data);
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->view('cbt/banksoal/add');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $data['id_guru'] = $guru->id_guru;
        $data['smt'] = $this->dashboard->getSemester();
        $data['gurus'] = $this->dropdown->getAllGuru();
        $data = ['user' => $user, 'judul' => 'Bank Soal', 'subjudul' => 'Buat Bank Soal'];
        $this->load->model('Kelas_model', 'kelas');
    }
    public function editBank()
    {
        if (!($mapel !== false)) {
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $this->load->model('Master_model', 'master');
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Edit Bank Soal', 'subjudul' => 'Edit Bank Soal'];
        $data['setting'] = $this->dashboard->getSetting();
        $data['mapel'] = $this->dropdown->getAllMapel();
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->master->getSemesterActive();
        $data['id_guru'] = $guru->id_guru;
        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        $this->load->model('Kelas_model', 'kelas');
        $this->load->view('_templates/dashboard/_footer');
        $data['jurusan'] = $this->cbt->getAllJurusan();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['gurus'] = $this->dropdown->getAllGuru();
        $data['mapel_guru'] = $mapel_guru;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['mapel_guru'] = $mapel_guru;
        $data['bank'] = $this->cbt->getDataBankById($id_bank);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $tp = $this->master->getTahunActive();
        $this->load->view('cbt/banksoal/add');
        $this->load->view('members/guru/templates/header', $data);
        $data['bulan'] = $this->dropdown->getBulan();
        $data['mapel_agama'] = $this->master->getAgamaSiswa();
        $data['mapel'] = $arrMapel;
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $id_guru = $this->input->get('id_guru', true);
        $id_bank = $this->input->get('id_bank', true);
        $user = $this->ion_auth->user()->row();
        $data['gurus'] = $nguru;
        $data['id_guru'] = $id_guru;
        $setting = $this->dashboard->getSetting();
        $data['guru'] = $guru;
        $this->load->view('cbt/banksoal/add');
        $this->load->model('Cbt_model', 'cbt');
        $arrMapel = [];
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt_active'] = $smt;
    }
    public function saveBank()
    {
        if ($this->input->post()) {
        }
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $id = $this->input->post('id_bank', true);
        $this->logging->saveLog(4, 'mengedit bank soal');
        $tp = $this->master->getTahunActive();
        $status = FALSE;
        $this->output_json($data);
        $this->logging->saveLog(3, 'menambah bank soal');
        $smt = $this->master->getSemesterActive();
        $status = TRUE;
        $data['status'] = $status;
        $this->cbt->saveBankSoal($tp->id_tp, $smt->id_smt);
        $this->load->model('Master_model', 'master');
        if (!$id) {
        }
    }
    public function deleteBank()
    {
        $id = $this->input->get('id_bank', true);
        $this->load->model('Cbt_model', 'cbt');
        if (!$this->master->delete('cbt_soal', $id, 'bank_id')) {
        }
        $this->load->model('Log_model', 'logging');
        $this->load->model('Master_model', 'master');
        $this->output_json(['status' => false, 'message' => 'Ada jadwal ujian yang menggunakan bank soal ini']);
        if ($this->cbt->cekJadwalBankSoal($id) > 0) {
        }
        $this->output_json(['status' => true, 'message' => 'berhasil']);
        if (!$this->master->delete('cbt_bank_soal', $id, 'id_bank')) {
        }
        $this->logging->saveLog(5, 'menghapus bank soal');
    }
    public function deleteAllBank()
    {
        if (!$this->master->delete('cbt_soal', $ids, 'bank_id')) {
        }
        $ids = json_decode($this->input->post('ids', true));
        if (!$this->master->delete('cbt_bank_soal', $ids, 'id_bank')) {
        }
        $this->output_json(['status' => false, 'message' => 'Ada jadwal ujian yang menggunakan bank soal ini']);
        $this->logging->saveLog(5, 'menghapus bank soal');
        $this->output_json(['status' => true, 'message' => 'berhasil']);
        $this->load->model('Log_model', 'logging');
        $this->load->model('Master_model', 'master');
        if ($this->cbt->cekJadwalBankSoal($ids) > 0) {
        }
        $this->load->model('Cbt_model', 'cbt');
    }
    public function detail($id)
    {
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $data['bank'] = $this->cbt->getDataBankById($id);
        $this->load->view('cbt/banksoal/detail');
        $tp = $this->master->getTahunActive();
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $data['soals'] = $this->cbt->getAllSoalByBank($id);
        $data['smt_active'] = $smt;
        $data['total_siswa'] = isset($terpakai[$id]) ? count($terpakai[$id]) : 0;
        $this->load->view('_templates/dashboard/_header', $data);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['smt'] = $this->dashboard->getSemester();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        $this->load->view('cbt/banksoal/detail');
        $data['setting'] = $this->dashboard->getSetting();
        $terpakai = $this->cbt->getBankTerpakai([$id]);
        $this->load->model('Dashboard_model', 'dashboard');
        $data = ['user' => $user, 'judul' => 'Detail Soal', 'subjudul' => 'Detail Soal'];
        $this->load->model('Cbt_model', 'cbt');
        $data['tp_active'] = $tp;
        $this->load->model('Master_model', 'master');
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('members/guru/templates/footer');
        if ($this->ion_auth->is_admin()) {
        }
    }
    public function saveSelected()
    {
        sleep(1);
        $jenis = $this->input->post('jenis', true);
        $bank_id = $this->input->post('id_bank', true);
        $soal = $jml != null ? count($jml) : 0;
        $total_soal_tampil = isset(array_count_values(array_column($soals, 'tampilkan'))['1']) ? array_count_values(array_column($soals, 'tampilkan'))['1'] : 0;
        $total_soal_seharusnya_tampil = $bank->tampil_pg + $bank->tampil_kompleks + $bank->tampil_jodohkan + $bank->tampil_isian + $bank->tampil_esai;
        $this->db->where('id_bank', $bank_id);
        $data['check'] = $updated;
        $i++;
        $id = $this->input->post('soal[' . $i . ']', true);
        foreach ($arrId as $id) {
            $this->db->update('cbt_soal');
            $this->db->set('tampilkan', 1);
            $updated++;
            $this->db->where('id_soal', $id);
        }
        if (!($id != null)) {
        }
        $this->db->update('cbt_bank_soal');
        $arrId = [];
        $jml = $this->input->post('soal', true);
        $updated = 0;
        $this->output_json($data);
        foreach ($unchek as $id) {
            $this->db->set('tampilkan', 0);
            $this->db->update('cbt_soal');
            $this->db->where('id_soal', $id);
        }
        $unchek = json_decode($this->input->post('uncheck', true));
        if (!($i <= $soal)) {
        }
        $tampil_kurang = $total_soal_tampil < $total_soal_seharusnya_tampil;
        array_push($arrId, $id);
        $this->load->model('Cbt_model', 'cbt');
        $this->db->set('status_soal', $status_soal);
        $status_soal = $tampil_kurang ? '0' : '1';
        $i = 0;
        $bank = $this->cbt->getDataBankById($bank_id);
        $soals = $this->cbt->getAllSoalByBank($bank_id);
    }
    public function copyBankSoal($id_bank)
    {
        $bank = $this->cbt->getDataBankById($id_bank);
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Master_model', 'master');
        if (!($soals && count($soals) > 0)) {
        }
        $this->db->insert_batch('cbt_soal', $soals);
        $result = $this->master->create('cbt_bank_soal', $data);
        $id = $this->db->insert_id();
        $soals = $this->cbt->getAllSoalByBank($id_bank);
        $this->logging->saveLog(3, 'membuat bank soal');
        $tp = $this->dashboard->getTahunActive();
        foreach ($soals as $soal) {
            $soal->updated_on = time();
            $soal->bank_id = $id;
            $soal->created_on = time();
            unset($soal->id_soal);
        }
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($result);
        $this->load->model('Log_model', 'logging');
        $this->load->model('Dashboard_model', 'dashboard');
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'bank_jenis_id' => $bank->bank_jenis_id, 'bank_kode' => $bank->bank_kode . '_COPY', 'bank_level' => $bank->bank_level, 'bank_kelas' => $bank->bank_kelas, 'bank_mapel_id' => $bank->bank_mapel_id, 'bank_jurusan_id' => $bank->bank_jurusan_id, 'bank_guru_id' => $bank->bank_guru_id, 'bank_nama' => $bank->bank_nama, 'kkm' => $bank->kkm, 'deskripsi' => $bank->deskripsi, 'jml_soal' => $bank->jml_soal, 'tampil_pg' => $bank->tampil_pg, 'bobot_pg' => $bank->bobot_pg, 'jml_kompleks' => $bank->jml_kompleks, 'tampil_kompleks' => $bank->tampil_kompleks, 'bobot_kompleks' => $bank->bobot_kompleks, 'jml_jodohkan' => $bank->jml_jodohkan, 'tampil_jodohkan' => $bank->tampil_jodohkan, 'bobot_jodohkan' => $bank->bobot_jodohkan, 'jml_isian' => $bank->jml_isian, 'tampil_isian' => $bank->tampil_isian, 'bobot_isian' => $bank->bobot_isian, 'jml_esai' => $bank->jml_esai, 'tampil_esai' => $bank->tampil_esai, 'bobot_esai' => $bank->bobot_esai, 'opsi' => $bank->opsi, 'date' => date('Y-m-d H:i:s'), 'status' => $bank->status, 'soal_agama' => $bank->soal_agama];
    }
    public function buatsoal($id_bank)
    {
        $data['soals'] = $this->cbt->getAllSoalByBank($id_bank, $jenis);
        $data['jurusan'] = $this->cbt->getAllJurusan();
        $act_tab = $_jns != null ? $_jns : '1';
        $tp = $this->master->getTahunActive();
        $_jns = $this->input->get('jns', true);
        if ($jenis == '1') {
        }
        $data = ['user' => $user, 'judul' => 'Buat Soal', 'subjudul' => 'Buat Soal'];
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['bank'] = $bank;
        $data_komplit = $this->cbt->cekSoalBelumKomplit($jenis, $bank->opsi);
        if ($this->ion_auth->is_admin()) {
        }
        $data['soal_ada'] = $this->cbt->cekSoalAda($id_bank, $jenis);
        $user = $this->ion_auth->user()->row();
        $bank = $this->cbt->getDataBankById($id_bank);
        $this->load->view('cbt/banksoal/soal');
        $data['jml_jodohkan'] = $this->cbt->getNomorSoalTerbesar($id_bank, 3);
        $this->load->view('_templates/dashboard/_footer');
        $data['jml_pg2'] = $this->cbt->getNomorSoalTerbesar($id_bank, 2);
        $smt = $this->master->getSemesterActive();
        $jenis = $tab == null ? $act_tab : $tab;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['setting'] = $setting;
        $data['soal'] = null;
        $this->load->model('Dropdown_model', 'dropdown');
        if ($jenis == '4') {
        }
        $data['p_jns'] = $act_tab;
        $tab = $this->input->get('tab', true);
        if ($jenis == '2') {
        }
        $this->load->view('cbt/banksoal/soal');
        $data['soal_belum_komplit'] = isset($data_komplit[$id_bank]) ? $data_komplit[$id_bank] : [];
        $data['smt'] = $this->dashboard->getSemester();
        if ($jenis == '3') {
        }
        $data['smt_active'] = $smt;
        $data['p_no'] = $_no != null ? $_no : '1';
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('members/guru/templates/header', $data);
        $setting = $this->dashboard->getSetting();
        $this->load->model('Cbt_model', 'cbt');
        $data['jml_essai'] = $this->cbt->getNomorSoalTerbesar($id_bank, 5);
        $data['tab_active'] = $jenis;
        if ($jenis == '5') {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['jml_isian'] = $this->cbt->getNomorSoalTerbesar($id_bank, 4);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('members/guru/templates/footer');
        $_no = $this->input->get('no', true);
        $data['jml_pg'] = $this->cbt->getNomorSoalTerbesar($id_bank, 1);
    }
    public function getSoalByNomor()
    {
        if ($jenis == '2') {
        }
        $bank_id = $this->input->get('bank_id', true);
        $t = @unserialize($soal->opsi_a ?? '');
        if ($j !== false) {
        }
        $data->opsi_a = $t;
        if (!($nomor != 1)) {
        }
        $data->jawaban = $j;
        if ($data != null) {
        }
        $j = @unserialize($soal->jawaban ?? '');
        if ($jenis == '3') {
        }
        $this->load->model('Cbt_model', 'cbt');
        $data->opsi_a = false;
        $jenis = $this->input->get('jenis', true);
        $data = ['bank_id' => $bank_id, 'jenis' => $jenis, 'nomor_soal' => $nomor];
        $data->jawaban = $j;
        if ($j !== false) {
        }
        if ($t !== false) {
        }
        $data->file = unserialize($soal->file ?? '');
        $data->jawaban = false;
        $soal = $this->cbt->getSoalByNomor($bank_id, $nomor, $jenis);
        $nomor = $this->input->get('nomor', true);
        $this->output_json($data);
        $data->jawaban = false;
        $data = $soal;
        $j = @unserialize($soal->jawaban ?? '');
    }
    public function tambahSoal()
    {
        $data = ['bank_id' => $bank, 'nomor_soal' => $nomor, 'jenis' => $jenis, 'tampilkan' => 0, 'created_on' => time(), 'updated_on' => time()];
        $jenis = $this->input->post('jenis', true);
        $bank = $this->input->post('bank', true);
        $nomor = $this->input->post('nomor', true);
        $insert = $this->db->insert('cbt_soal', $data);
        $this->output_json($insert);
    }
    public function importsoal($id)
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $this->load->model('Master_model', 'master');
        $data['smt'] = $this->dashboard->getSemester();
        $smt = $this->master->getSemesterActive();
        $data['jurusan'] = $this->cbt->getAllJurusan();
        $this->load->model('Dashboard_model', 'dashboard');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $tp = $this->master->getTahunActive();
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/banksoal/import');
        $user = $this->ion_auth->user()->row();
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('members/guru/templates/footer');
        $data['setting'] = $setting;
        $this->load->view('cbt/banksoal/import');
        $data = ['user' => $user, 'judul' => 'Import Bank Soal', 'subjudul' => 'Import Bank Soal'];
        $setting = $this->dashboard->getSetting();
        $this->load->model('Dropdown_model', 'dropdown');
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tp'] = $this->dashboard->getTahun();
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['smt_active'] = $smt;
        $data['bank'] = $this->cbt->getDataBankById($id);
        if ($this->ion_auth->is_admin()) {
        }
    }
    public function import()
    {
        $this->output_json($result);
        $bank = $this->cbt->getDataBankById($bank_id);
        $obj = json_decode($str);
        $this->load->model('Cbt_model', 'cbt');
        $input = $this->input->post('ganda');
        $soal = json_decode(json_encode($json));
        $str = preg_replace('﻿', '', $input);
        $result['error'] = json_last_error_msg();
        $result['soal'] = $obj;
        $bank_id = $this->input->post('bank_id', true);
        $json = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $input), true);
    }
    public function getSoalSiswa($id_bank)
    {
        $this->load->model('Cbt_model', 'cbt');
        $soals = $this->cbt->getAllSoalByBank($id_bank);
        $data['soal'] = $soals;
        $this->output_json($data);
        foreach ($soals as $soal) {
            if ($soal->jenis == '3') {
            }
            $soal->opsi_a = unserialize($soal->opsi_a ?? '');
            $soal->jawaban = unserialize($soal->jawaban ?? '');
            if ($soal->jenis == '2') {
            }
            $soal->jawaban = unserialize($soal->jawaban ?? '');
            if (!isset($soal->file)) {
            }
            $soal->file = unserialize($soal->file ?? '');
        }
    }
    function innerXML($node)
    {
        foreach ($node->childNodes as $child) {
            $frag->appendChild($child->cloneNode(TRUE));
        }
        $frag = $doc->createDocumentFragment();
        return $doc->saveXML($frag);
        $doc = $node->ownerDocument;
    }
    public function file_config()
    {
        $config['allowed_types'] = 'jpeg|jpg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4';
        $config['upload_path'] = FCPATH . 'uploads/bank_soal/';
        $config['encrypt_name'] = TRUE;
        return $this->load->library('upload', $config);
        $allowed_type = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3', 'audio/x-wav', 'audio/wave', 'audio/wav', 'video/mp4', 'application/octet-stream'];
    }
    public function validasi($jenis)
    {
        $this->form_validation->set_rules('jawaban_essai', 'Kunci Jawaban', 'required');
        $this->form_validation->set_rules('soal', 'Soal', 'required');
        if ($jenis == 1) {
        }
        $this->form_validation->set_rules('jawaban2_a', 'Kunci Jawaban', 'required');
        if ($jenis == 2) {
        }
        $this->form_validation->set_rules('jawaban[][]', 'Kunci Jawaban', 'required');
        $this->form_validation->set_rules('jawaban_pg', 'Kunci Jawaban', 'required');
        $this->form_validation->set_rules('jawaban_isian', 'Kunci Jawaban', 'required');
        if ($jenis == 4) {
        }
        $this->form_validation->set_rules('jawaban_benar_pg2[]', 'Kunci Jawaban', 'required');
        if ($jenis == 3) {
        }
    }
    public function saveSoal()
    {
        $data['created_on'] = time();
        $jawabans = [];
        $this->logging->saveLog(3, 'membuat soal');
        $j = 0;
        $this->master->create('cbt_soal', $data);
        $jwb = $this->input->post('jawaban_benar_pg2[' . $i . ']', true);
        if ($method === 'edit') {
        }
        $result['status'] = 'error';
        if (!($i <= $jwb_pg2)) {
        }
        if (!($j === 0)) {
        }
        $i++;
        $data = ['bank_id' => $bank_id, 'jenis' => $jenis, 'nomor_soal' => $nomor_soal, 'soal' => $soal];
        $result['status'] = '400 Method not found';
        $this->master->update('cbt_soal', $data, 'id_soal', $id_soal);
        $data['opsi_a'] = serialize($opsis);
        $abjad = ['a', 'b', 'c', 'd', 'e'];
        $data['jawaban'] = $this->input->post('jawaban_pg', true);
        $result['status'] = 'Soal berhasil dibuat';
        $i++;
        $soal = $this->input->post('soal', false);
        $jenis = $this->input->post('jenis', true);
        $opsis = [];
        $this->validasi($jenis);
        $result['error'] = form_error();
        $this->file_config();
        if (!($op != null)) {
        }
        $i = 97;
        $jwb_jodohkan = ['model' => $this->input->post('model', true), 'type' => $this->input->post('type', true), 'jawaban' => $jawabans];
        $j++;
        $data['updated_on'] = time();
        $this->load->model('Master_model', 'master');
        if ($jenis == 4) {
        }
        $data['jawaban'] = $this->input->post('jawaban_essai', false);
        if ($jenis == 1) {
        }
        $result['status'] = 'Soal berhasil diupdate';
        $opsis[chr($i)] = $op;
        $data['jawaban'] = $this->input->post('jawaban_isian', true);
        $this->load->model('Log_model', 'logging');
        $jwb_pg2 = count($this->input->post('jawaban_benar_pg2', true));
        if (!($i < count($jawabans))) {
        }
        $nomor_soal = $this->input->post('nomor_soal', true);
        $i = 0;
        $id_soal = $this->input->post('soal_id', true);
        $this->output_json($result);
        if (!($j < count($jawabans[$i]))) {
        }
        $op = $this->input->post('jawaban2_' . chr($i), false);
        $bank_id = $this->input->post('bank_id', true);
        $this->logging->saveLog(4, 'mengedit soal');
        if ($method === 'add') {
        }
        foreach ($abjad as $abj) {
            $data['opsi_' . $abj] = $this->input->post('jawaban_' . $abj, false);
        }
        $jawabans = $this->input->post('jawaban', false);
        if (!($i < 117)) {
        }
        $method = $this->input->post('method', true);
        array_push($jawabans, $jwb);
        if ($this->form_validation->run() === FALSE) {
        }
        $i++;
        $data['jawaban'] = serialize($jwb_jodohkan);
        if ($jenis == 2) {
        }
        if ($jenis == 3) {
        }
        $data['updated_on'] = time();
        $data['jawaban'] = serialize($jawabans);
        $i = 0;
        $jawabans[$i][$j] = $this->decode_data($jawabans[$i][$j], $bank_id, $jenis, $nomor_soal);
    }
    function base64_to_jpeg($base64_string, $output_file)
    {
        fwrite($ifp, base64_decode($data[1]));
        return $output_file;
        $ifp = fopen($output_file, 'wb');
        fclose($ifp);
        $data = explode(',', $base64_string);
    }
    public function hapusSoal()
    {
        foreach ($all_soal as $soal) {
            $nomor_baru++;
            $update[] = ['id_soal' => $soal->id_soal, 'nomor_soal' => $nomor_baru];
        }
        $result = $this->cbt->getNomorSoalById($id_soal);
        $nomor_baru = 1;
        $this->db->where('id_soal', $id_soal);
        $id_soal = $this->input->post('soal_id', true);
        if (!$deleted) {
        }
        $nomor = $result->nomor_soal;
        $update = [];
        $deleted = $this->db->delete('cbt_soal');
        $this->output_json($deleted);
        $all_soal = $this->cbt->getNomorSoalByBankJenis($result->bank_id, $result->jenis);
        $this->load->model('Cbt_model', 'cbt');
        $this->db->update_batch('cbt_soal', $update, 'id_soal');
        if (!(count($update) > 0)) {
        }
    }
    function uploadFile()
    {
        $kode_file = $id_soal . '_' . time();
        $data['filename'] = $nama_file_asal;
        $src = 'uploads/bank_soal/' . $kode_file . '.' . $ext;
        $data['files'] = $files;
        $filename = '';
        $data['status'] = false;
        $files = $soal == null || $soal->file == null ? [] : unserialize($soal->file ?? '');
        $data['soal'] = $soal;
        $type = $_FILES['file_uploads']['type'];
        $soal = $this->cbt->getFileSoalById($id_soal);
        $src = '';
        $id_soal = $this->input->get('id_soal', true);
        $config['allowed_types'] = 'mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi';
        $nama_file_asal = $_FILES['file_uploads']['name'];
        $data['size'] = $_FILES['file_uploads']['size'];
        $data['src'] = $src;
        $data['src'] = $this->upload->display_errors();
        $this->db->where('id_soal', $id_soal);
        $data['type'] = $type;
        if (!isset($_FILES['file_uploads']['name'])) {
        }
        $data['status'] = true;
        $this->output_json($data);
        $config['file_name'] = $kode_file;
        $this->load->model('Cbt_model', 'cbt');
        $ext = pathinfo($file['file_name'], PATHINFO_EXTENSION);
        $files[] = ['file_name' => $nama_file_asal, 'alias' => $kode_file, 'src' => $src, 'type' => $type];
        $this->upload->initialize($config);
        $this->db->update('cbt_soal');
        $config['upload_path'] = './uploads/bank_soal/';
        if (!$this->upload->do_upload('file_uploads')) {
        }
        $file = $this->upload->data();
        $this->db->set('file', serialize($files));
    }
    function upload_image()
    {
        $config['allowed_types'] = 'jpg|jpeg|png|gif|mp3|ogg|wav|mp4|mpeg|webm';
        $status = true;
        if (!$this->upload->do_upload('file')) {
        }
        $config['upload_path'] = './uploads/bank_soal/';
        $this->upload->initialize($config);
        $status = false;
        $this->upload->display_errors();
        $data['filename'] = 'uploads/bank_soal/' . $uploaded['file_name'];
        $config['file_name'] = 'file_' . date('YmdHis');
        $this->output_json($data);
        $uploaded = $this->upload->data();
        if (!isset($_FILES['file']['name'])) {
        }
        $status = false;
        $data['status'] = $status;
    }
    function uploadSoalImage()
    {
        $name = $this->input->post('name');
        str_replace('%2B', '+', $src ?? '');
        $src = $this->input->post('src');
        $data['src'] = 'uploads/bank_soal/' . $name;
        $data['status'] = file_put_contents('./uploads/bank_soal/' . $name, base64_decode($src));
        $this->output_json($data);
    }
    function deleteFile()
    {
        if (!unlink($file_name)) {
        }
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        echo 'File Delete Successfully';
    }
    function doImport()
    {
        $jml_seharusnya = $bank->tampil_pg + $bank->tampil_kompleks + $bank->tampil_jodohkan + $bank->tampil_isian + $bank->tampil_esai;
        $data['data_insert'] = $inserted;
        $datas = [];
        foreach ($json as $jenis => $values) {
            foreach ($values as $val) {
                $data_soal[$no]['kunci'][strtolower($val->KUNCI ?? '')] = strtoupper($val->OPSI ?? '');
                if (!(isset($val->SOAL) && $val->SOAL != '')) {
                }
                if (!isset($val->KD_KOLOM)) {
                }
                $data_soal[$no]['opsi'][strtoupper($val->OPSI ?? '')] = $val->JAWABAN;
                if ($jenis == '3') {
                }
                if (!isset($val->KD_BARIS)) {
                }
                if (!isset($val->OPSI)) {
                }
                $data_soal[$no]['opsi'][strtoupper($val->OPSI ?? '')] = $val->JAWABAN;
                if (!isset($val->KUNCI)) {
                }
                if ($jenis == '2') {
                }
                $data_soal[$no]['soal'] = $val->SOAL;
                $data_soal[$no]['kunci'][strtoupper($val->KD_KUNCI ?? '')] = strtoupper($val->KUNCI ?? '');
                if (!(isset($val->KUNCI) && strtolower($val->KUNCI ?? '') == 'v')) {
                }
                $data_soal[$no]['kolom'][strtoupper($val->KD_KOLOM ?? '')] = $val->KOLOM;
                if (!isset($val->NO)) {
                }
                if (!(isset($val->KUNCI) && strtolower($val->KUNCI ?? '') == 'v')) {
                }
                if ($jenis == '1') {
                }
                if (!isset($val->KUNCI)) {
                }
                $data_soal[$no]['baris'][strtoupper($val->KD_BARIS ?? '')] = $val->BARIS;
                $data_soal[$no]['kunci'] = $val->KUNCI;
                if (!isset($val->OPSI)) {
                }
                $data_soal[$no]['kunci'][strtoupper($val->OPSI ?? '')] = strtolower($val->KUNCI ?? '');
                $no = trim($val->NO ?? '');
            }
            $data_soal = [];
            $datas[$jenis] = $data_soal;
        }
        $jml = [];
        $data['total'] = count($inserted);
        $this->db->where('bank_id', $bank_id);
        $bank = $this->cbt->getDataBankById($bank_id);
        $string = $this->input->post('data', false);
        $data_insert = [];
        if (!$this->db->delete('cbt_soal')) {
        }
        $data['insert'] = $this->db->insert_batch('cbt_soal', $inserted);
        if (count($inserted) > 0) {
        }
        $this->load->model('Cbt_model', 'cbt');
        $data['json'] = $json;
        $bank_id = $this->input->post('id_bank', true);
        foreach ($datas as $jenis => $keys) {
            foreach ($keys as $no => $v) {
                if (!isset($v['kunci'])) {
                }
                $jml_kolom = count($baris[0]);
                foreach ($v['kunci'] as $kunci => $jawaban) {
                    $kuncis[] = strtolower($kunci ?? '');
                    if (!($jawaban == 'v')) {
                    }
                }
                if (!isset($v['kunci'])) {
                }
                $insert['jawaban'] = isset($v['kunci']) && isset($v['kunci']['v']) ? $v['kunci']['v'] : '';
                if ($jenis == '1') {
                }
                foreach ($v['kolom'] as $kd_kol => $kol) {
                    $kolom[$kd_kol] = $kol;
                    array_push($header, $kol);
                    if (!($kol != '')) {
                    }
                    foreach ($v['kunci'] as $kd_bar => $kd_kol) {
                        if (!($kd_kol != '')) {
                        }
                        $arrKol[$kd_bar] = explode(',', $kd_kol ?? '');
                    }
                }
                array_push($header, '#');
                if ($jenis == '4') {
                }
                if (!isset($v['opsi'])) {
                }
                foreach ($v['opsi'] as $opsi => $jawaban) {
                    $opsis[strtolower($opsi ?? '')] = $jawaban;
                }
                if (!($isi_soal != '')) {
                }
                $insert['opsi_d'] = isset($v['opsi']) && isset($v['opsi']['D']) ? $v['opsi']['D'] : '';
                $insert['jawaban'] = serialize($jwb_jodohkan);
                $jml_baris = count($baris);
                $header = [];
                $insert = ['jenis' => $jenis, 'nomor_soal' => $no, 'soal' => $isi_soal, 'file' => serialize([])];
                $data_insert[] = $insert;
                $jwbnBaris = [];
                $jwb_jodohkan = ['model' => $jml_baris == $jml_kolom ? '1' : '2', 'type' => $type, 'jawaban' => $baris];
                $types = [];
                array_push($baris, $header);
                if ($jenis == '2') {
                }
                $insert['jawaban'] = $v['kunci'];
                $insert['opsi_e'] = isset($v['opsi']) && isset($v['opsi']['E']) ? $v['opsi']['E'] : '';
                $insert['opsi_a'] = isset($v['opsi']) && isset($v['opsi']['A']) ? $v['opsi']['A'] : '';
                $kuncis = [];
                foreach ($jwbnBaris as $brs => $jml) {
                    if (!(isset($jmlType[1]) && $jmlType[1] > 1)) {
                    }
                    $jmlType = array_count_values($jml);
                    array_push($types, 'checkbox');
                }
                $insert['opsi_a'] = serialize($opsis);
                $insert['jawaban'] = serialize($kuncis);
                $kolom = [];
                $insert['opsi_c'] = isset($v['opsi']) && isset($v['opsi']['C']) ? $v['opsi']['C'] : '';
                $type = count($types) > 0 ? '1' : '2';
                $baris = [];
                $opsis = [];
                $arrKol = [];
                if ($jenis == '3') {
                }
                $isi_soal = isset($v['soal']) ? $v['soal'] : '';
                foreach ($v['baris'] as $kd_bar => $bar) {
                    array_shift($jwbn);
                    array_push($baris, $jwbn);
                    if (!($kd_bar != '')) {
                    }
                    $jwbnBaris[$kd_bar] = $jwbn;
                    if (!($kd_bar != '')) {
                    }
                    $jwbn = [];
                    if (!(count($jwbn) > 0)) {
                    }
                    foreach ($kolom as $kk => $val) {
                        if (!($kd_bar != '' && $val != '' && isset($arrKol[$kd_bar]))) {
                        }
                        array_push($jwbn, $match ? '1' : '0');
                        $match = in_array($kk, $arrKol[$kd_bar]);
                    }
                    array_push($jwbn, $bar);
                }
                if (!isset($v['kunci'])) {
                }
                $insert['opsi_b'] = isset($v['opsi']) && isset($v['opsi']['B']) ? $v['opsi']['B'] : '';
                $insert['jawaban'] = strip_tags($v['kunci'] ?? '');
            }
        }
        $total_soal = count($data_insert);
        $inserted = [];
        $json = json_decode($string);
        $data['insert'] = 0;
        foreach ($data_insert as $dins) {
            $inserted[] = ['bank_id' => $bank_id, 'jenis' => $dins['jenis'], 'nomor_soal' => $dins['nomor_soal'], 'soal' => $dins['soal'], 'deskripsi' => '', 'kesulitan' => '8', 'timer' => '0', 'timer_menit' => '0', 'file' => $dins['file'], 'tampilkan' => '0', 'created_on' => time(), 'updated_on' => time(), 'opsi_a' => isset($dins['opsi_a']) ? $dins['opsi_a'] : '', 'opsi_b' => isset($dins['opsi_b']) ? $dins['opsi_b'] : '', 'opsi_c' => isset($dins['opsi_c']) ? $dins['opsi_c'] : '', 'opsi_d' => isset($dins['opsi_d']) ? $dins['opsi_d'] : '', 'opsi_e' => isset($dins['opsi_e']) ? $dins['opsi_e'] : '', 'jawaban' => $dins['jawaban'], 'tampilkan' => $total_soal == $jml_seharusnya ? '1' : '0'];
        }
        $this->output_json($data);
    }
    function uploadSoal()
    {
        $tmpl['5'] = $jml_sess == $bank->tampil_esai ? '1' : '0';
        $this->load->model('Cbt_model', 'cbt');
        $tmpl['1'] = $jml_spg1 == $bank->tampil_pg ? '1' : '0';
        $sttmpl['4'] = $jml_siss >= $bank->tampil_isian ? '1' : '0';
        $inserted = [];
        $data['insert'] = 0;
        $data_insert = [];
        $datas = $this->input->post('soal', false);
        $this->db->where('bank_id', $bank_id);
        $bank_id = $this->input->post('id_bank', true);
        $tmpl['4'] = $jml_siss == $bank->tampil_isian ? '1' : '0';
        $sttmpl['1'] = $jml_spg1 >= $bank->tampil_pg ? '1' : '0';
        $soal_updated = $this->db->update('cbt_bank_soal');
        $this->output_json($data);
        if (count($inserted) > 0) {
        }
        $data['selesai'] = $soal_updated;
        $status_soal = $sttmpl['1'] == '1' && $sttmpl['2'] == '1' && $sttmpl['3'] == '1' && $sttmpl['4'] == '1' && $sttmpl['5'] == '1' ? '1' : '0';
        $jml_spg1 = 0;
        if (!count($inserted)) {
        }
        $sttmpl['3'] = $jml_sjod >= $bank->tampil_jodohkan ? '1' : '0';
        $data['insert'] = $this->db->insert_batch('cbt_soal', $inserted);
        foreach ($data_insert as $dins) {
            $inserted[] = ['bank_id' => $bank_id, 'jenis' => $dins['jenis'], 'nomor_soal' => $dins['nomor_soal'], 'soal' => $dins['soal'], 'deskripsi' => '', 'kesulitan' => '8', 'timer' => '0', 'timer_menit' => '0', 'file' => $dins['file'], 'created_on' => time(), 'updated_on' => time(), 'opsi_a' => $dins['opsi_a'] ?? '', 'opsi_b' => $dins['opsi_b'] ?? '', 'opsi_c' => $dins['opsi_c'] ?? '', 'opsi_d' => $dins['opsi_d'] ?? '', 'opsi_e' => $dins['opsi_e'] ?? '', 'jawaban' => $dins['jawaban'], 'tampilkan' => $tmpl[$dins['jenis']]];
        }
        $total_soal = count($data_insert);
        $jml_spg2 = 0;
        $this->db->where('id_bank', $bank_id);
        $this->db->set('status_soal', $status_soal);
        $jml_sjod = 0;
        $data['total'] = count($inserted);
        $tmpl['2'] = $jml_spg2 == $bank->tampil_kompleks ? '1' : '0';
        foreach ($datas as $jenis => $nomor) {
            foreach ($nomor as $no => $v) {
                $insert['jawaban'] = isset($v['kunci']) && count($v['kunci']) > 0 ? $v['kunci'][0] : '';
                $jml_spg1++;
                $jml_baris = count($baris);
                $jml_kolom = count($baris[0]);
                if (!isset($v['kunci'])) {
                }
                $insert['opsi_d'] = isset($v['opsi']) && isset($v['opsi']['D']) ? $this->decode_data(rawurldecode($v['opsi']['D']), $bank_id, $jenis, $no) : '';
                $insert['jawaban'] = $this->decode_data(rawurldecode($v['kunci']), $bank_id, $jenis, $no);
                array_push($baris, $header);
                if (!isset($v['kunci'])) {
                }
                $insert['jawaban'] = $v['kunci'];
                $jml_sess++;
                if (!($isi_soal != '')) {
                }
                $insert['opsi_a'] = serialize($opsis);
                $jml_siss++;
                $header = [];
                $jml_spg2++;
                $jwbnBaris = [];
                if ($jenis == 1) {
                }
                $insert = ['jenis' => $jenis, 'nomor_soal' => $no, 'soal' => $isi_soal, 'file' => serialize([])];
                if (!isset($v['opsi'])) {
                }
                foreach ($v['opsi'] as $opsi => $jawaban) {
                    $opsis[strtolower($opsi ?? '')] = $this->decode_data(rawurldecode($jawaban), $bank_id, $jenis, $no);
                }
                $insert['opsi_a'] = isset($v['opsi']) && isset($v['opsi']['A']) ? $this->decode_data(rawurldecode($v['opsi']['A']), $bank_id, $jenis, $no) : '';
                $arrKol = [];
                $insert['jawaban'] = serialize($jwb_jodohkan);
                $isi_soal = isset($v['soal']) ? $this->decode_data(rawurldecode($v['soal']), $bank_id, $jenis, $no) : '';
                foreach ($v['kunci'] as $jawaban) {
                    array_push($kuncis, strtolower($jawaban ?? ''));
                }
                $insert['jawaban'] = serialize($kuncis);
                $insert['opsi_b'] = isset($v['opsi']) && isset($v['opsi']['B']) ? $this->decode_data(rawurldecode($v['opsi']['B']), $bank_id, $jenis, $no) : '';
                if (!isset($v['kunci'])) {
                }
                $opsis = [];
                $insert['opsi_c'] = isset($v['opsi']) && isset($v['opsi']['C']) ? $this->decode_data(rawurldecode($v['opsi']['C']), $bank_id, $jenis, $no) : '';
                $jwb_jodohkan = ['model' => $jml_baris == $jml_kolom ? '1' : '2', 'type' => $type, 'jawaban' => $baris];
                foreach ($jwbnBaris as $brs => $jml) {
                    $jmlType = array_count_values($jml);
                    if (!(isset($jmlType[1]) && $jmlType[1] > 1)) {
                    }
                    array_push($types, 'checkbox');
                }
                $jml_sjod++;
                if ($jenis == '3') {
                }
                if ($jenis == '4') {
                }
                $insert['opsi_e'] = isset($v['opsi']) && isset($v['opsi']['E']) ? $this->decode_data(rawurldecode($v['opsi']['E']), $bank_id, $jenis, $no) : '';
                foreach ($v['kolom'] as $kd_kol => $kol) {
                    array_push($header, $this->decode_data(rawurldecode($kol), $bank_id, $jenis, $no));
                    if (!($kol != '')) {
                    }
                    foreach ($v['kunci'] as $kd_bar => $kd_kol) {
                        $arrKol[$kd_bar] = explode(',', $kd_kol ?? '');
                        if (!($kd_kol != '')) {
                        }
                    }
                    $kolom[$kd_kol] = $kol;
                }
                foreach ($v['baris'] as $kd_bar => $bar) {
                    $jwbn = [];
                    foreach ($kolom as $kk => $val) {
                        if (!($kd_bar != '' && $val != '' && isset($arrKol[$kd_bar]))) {
                        }
                        array_push($jwbn, $match ? '1' : '0');
                        $match = in_array($kk, $arrKol[$kd_bar]);
                    }
                    if (!($kd_bar != '')) {
                    }
                    array_push($jwbn, $this->decode_data(rawurldecode($bar), $bank_id, $jenis, $no));
                    array_push($baris, $jwbn);
                    if (!(count($jwbn) > 0)) {
                    }
                    $jwbnBaris[$kd_bar] = $jwbn;
                    if (!($kd_bar != '')) {
                    }
                    array_shift($jwbn);
                }
                $data_insert[] = $insert;
                $types = [];
                array_push($header, '#');
                if ($jenis == '2') {
                }
                $type = count($types) > 0 ? '1' : '2';
                $kolom = [];
                $kuncis = [];
                $baris = [];
            }
        }
        $sttmpl['5'] = $jml_sess >= $bank->tampil_esai ? '1' : '0';
        if (!$this->db->delete('cbt_soal')) {
        }
        $sttmpl['2'] = $jml_spg2 >= $bank->tampil_kompleks ? '1' : '0';
        $data['data_insert'] = $inserted;
        $bank = $this->cbt->getDataBankById($bank_id);
        $jml_siss = 0;
        $tmpl['3'] = $jml_sjod == $bank->tampil_jodohkan ? '1' : '0';
        $jml_sess = 0;
    }
    function decode_data($html, $id_bank, $jenis, $nomor)
    {
        $images = $dom->getElementsByTagName('img');
        return '';
        if (empty($html)) {
        }
        return str_replace('<?xml encoding="UTF-8">', '', $res ?? '');
        $dom->preserveWhiteSpace = false;
        if ($images) {
        }
        foreach ($images as $image) {
            $extension = $mime_split[1];
            $mime = $splited[0];
            if (!($extension == 'jpeg')) {
            }
            if (!(count($mime_split) == 2)) {
            }
            try {
                $bytes = random_bytes(10);
            } catch (Exception $e) {
            }
            file_put_contents('./uploads/bank_soal/' . $output_file, base64_decode($data));
            $splited = explode(',', substr($base64_image_string, 5), 2);
            $base64_image_string = $image->getAttribute('src');
            $extension = 'jpg';
            $data = $splited[1];
            $mime_split = explode('/', $mime_split_without_base64[0], 2);
            $output_file = 'img_' . $id_bank . $jenis . $nomor . '_' . bin2hex($bytes) . '.' . $extension;
            $numimg++;
            $image->setAttribute('src', 'uploads/bank_soal/' . $output_file);
            $mime_split_without_base64 = explode(';', $mime, 2);
            $image->setAttribute('src', str_replace(base_url(), '', $src ?? ''));
            $src = $image->getAttribute('src');
            if (substr($src, 0, 5) === 'data:') {
            }
        }
        $res = $dom->saveHTML();
        return $html;
        $dom->formatOutput = true;
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);
        $numimg = 1;
    }
}
```

---

## File: application/controllers_decoded/Cbtcetak.php

```php
<?php

class Cbtcetak extends CI_Controller
{
    public function __construct()
    {
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        redirect('auth');
        parent::__construct();
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library('upload');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('cbt/cetak/data');
        $this->load->view('members/guru/templates/header', $data);
        $this->load->model('Master_model', 'master');
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $smt = $this->master->getSemesterActive();
        $data['ids_pengawas'] = $ids_pengawas;
        $tp = $this->master->getTahunActive();
        $data['kop'] = $this->cbt->getSettingKopAbsensi();
        $this->load->view('cbt/cetak/data');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['pengawas'] = $pengawas;
        $user = $this->ion_auth->user()->row();
        if ($this->ion_auth->is_admin()) {
        }
        $data['smt_active'] = $smt;
        $data['tp'] = $this->dashboard->getTahun();
        $pengawas = $this->cbt->getPengawasHariIni(date('Y-m-d'));
        $data = ['user' => $user, 'judul' => 'Cetak Data Penilaian', 'subjudul' => 'Cetak', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $ids_pengawas = [];
        $this->load->view('members/guru/templates/footer');
        foreach ($pengawas as $pws) {
            foreach ($ids as $id) {
                $ids_pengawas[] = $id;
                if (!(!in_array($id, $ids_pengawas) && $id != '')) {
                }
            }
            $ids = explode(',', $pws->id_guru ?? '');
        }
    }
    public function kartuPeserta()
    {
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $data['kartu'] = $this->cbt->getSettingKartu();
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $user = $this->ion_auth->user()->row();
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('cbt/cetak/kartu');
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['ruang'] = $this->dropdown->getAllRuang();
        $this->load->model('Dropdown_model', 'dropdown');
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/cetak/kartu');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['smt_active'] = $smt;
        $data = ['user' => $user, 'judul' => 'Cetak Kartu Peserta', 'subjudul' => 'Cetak', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $data['setting_rapor'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('members/guru/templates/footer');
        $this->load->model('Rapor_model', 'rapor');
    }
    function uploadFile($logo)
    {
        $data['src'] = base_url() . 'uploads/settings/' . $result['file_name'];
        $this->upload->initialize($config);
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
        $data['type'] = $_FILES['logo']['type'];
        $data['status'] = true;
        $config['overwrite'] = true;
        $data['src'] = '';
        $config['upload_path'] = './uploads/settings/';
        $data['status'] = false;
        $data['size'] = $_FILES['logo']['size'];
        $data['src'] = $this->upload->display_errors();
        if (isset($_FILES['logo']['name'])) {
        }
        if (!$this->upload->do_upload('logo')) {
        }
        $this->output_json($data);
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $result = $this->upload->data();
        $config['file_name'] = $logo;
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        echo 'File Delete Successfully';
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!unlink($file_name)) {
        }
    }
    public function saveKartu()
    {
        $update = $this->db->replace('cbt_kop_kartu', $insert);
        $this->output_json($update);
        $header_4 = $this->input->post('header_4', true);
        $insert = ['id_set_kartu' => 123456, 'header_1' => $header_1, 'header_2' => $header_2, 'header_3' => $header_3, 'header_4' => $header_4, 'tanggal' => $tanggal];
        $header_2 = $this->input->post('header_2', true);
        $header_1 = $this->input->post('header_1', true);
        $tanggal = $this->input->post('tanggal', true);
        $header_3 = $this->input->post('header_3', true);
    }
    public function getSiswaKelas()
    {
        $kelas = $this->input->get('kelas');
        $isesi = null;
        $kelas = $ikelas;
        $ikelas = $this->master->getKelasById($kelas);
        $this->load->model('Kelas_model', 'kelas');
        $pengawas = [];
        $pengawass = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi);
        foreach ($pengawass as $p) {
            if (!(count(explode(',', $p->id_guru ?? '')) > 0)) {
            }
            $pengawas = $this->master->getGuruByArrId(explode(',', $p->id_guru ?? ''));
        }
        $data['info'] = ['kelas' => $ikelas, 'sesi' => $isesi, 'jadwal' => $ijadwal, 'pengawas' => $pengawas];
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($data);
        foreach ($siswas as $siswa) {
            array_push($data['siswa'], $siswa);
        }
        $s = !$sesi ? null : $sesi;
        if (!($s != null)) {
        }
        if ($kelas == 'all') {
        }
        $ijadwal = null;
        $tp = $this->dashboard->getTahunActive();
        $data['siswa'] = [];
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $sesi = $this->input->get('sesi');
        $ijadwal = $this->cbt->getJadwalById($jadwal, $s);
        $siswas = $this->cbt->getRuangSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas, $s);
        $jadwal = $this->input->get('jadwal');
        $this->load->model('Master_model', 'master');
        if (!($jadwal != null && $jadwal != 'null')) {
        }
        $isesi = $this->cbt->getSesiById($s);
        $ikelas = $this->kelas->getIdKelas($tp->id_tp, $smt->id_smt);
    }
    public function getSiswaRuang()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $jadwal = $this->input->get('jadwal');
        $ruang = $this->input->get('ruang');
        $isesi = $this->cbt->getSesiById($s);
        $tp = $this->dashboard->getTahunActive();
        $data['siswa'] = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $s);
        $pengawas = [];
        $this->output_json($data);
        $this->load->model('Master_model', 'master');
        $ijadwal = $this->cbt->getJadwalById($jadwal, $s);
        if (!($pengawass != null && count(explode(',', $pengawass->id_guru ?? '')) > 0)) {
        }
        $pengawass = $this->cbt->getPengawas($tp->id_tp . $smt->id_smt . $jadwal . $ruang . $sesi);
        $iruang = $this->cbt->getRuangById($ruang);
        $ijadwal = null;
        if (!($s != null)) {
        }
        if (!($jadwal != null && $jadwal != 'null')) {
        }
        $isesi = null;
        $pengawas = $this->master->getGuruByArrId(explode(',', $pengawass->id_guru ?? ''));
        $s = $sesi == 'null' ? null : $sesi;
        $data['info'] = ['ruang' => $iruang, 'sesi' => $isesi, 'jadwal' => $ijadwal, 'pengawas' => $pengawas];
        $this->load->model('Cbt_model', 'cbt');
        $smt = $this->dashboard->getSemesterActive();
        $sesi = $this->input->get('sesi');
    }
    public function saveKop()
    {
        $insert = ['id_kop' => 123456, 'header_1' => $header_1, 'header_2' => $header_2, 'header_3' => $header_3, 'header_4' => $header_4, 'proktor' => $proktor, 'pengawas_1' => $pengawas_1, 'pengawas_2' => $pengawas_2];
        $pengawas_2 = $this->input->post('pengawas_2', true);
        $header_3 = $this->input->post('header_3', true);
        $this->output_json($update);
        $header_2 = $this->input->post('header_2', true);
        $header_1 = $this->input->post('header_1', true);
        $header_4 = $this->input->post('header_4', true);
        $update = $this->db->replace('cbt_kop_absensi', $insert);
        $pengawas_1 = $this->input->post('pengawas_1', true);
        $proktor = $this->input->post('proktor', true);
    }
    public function absenPeserta()
    {
        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $data['kop'] = $this->cbt->getSettingKopAbsensi();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $data['mapel'] = $this->dropdown->getAllJadwalMapel($tp->id_tp, $smt->id_smt);
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $data['sesi'] = $this->dropdown->getAllSesi();
        $this->load->view('cbt/cetak/absen');
        $data['tp_active'] = $tp;
        $this->load->model('Dropdown_model', 'dropdown');
        if ($this->ion_auth->is_admin()) {
        }
        $data = ['user' => $user, 'judul' => 'Cetak Daftar Kehadiran', 'subjudul' => 'Cetak', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('cbt/cetak/absen');
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $data['ruang'] = $this->dropdown->getAllRuang();
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/guru/templates/footer');
        $user = $this->ion_auth->user()->row();
        $this->load->view('members/guru/templates/header', $data);
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function beritaAcara()
    {
        $this->load->view('members/guru/templates/header', $data);
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('cbt/cetak/beritaacara');
        $data['kop'] = $this->cbt->getSettingKopBeritaAcara();
        $data = ['user' => $user, 'judul' => 'Cetak Berita Acara', 'subjudul' => 'Cetak', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        if ($this->ion_auth->is_admin()) {
        }
        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->model('Dropdown_model', 'dropdown');
        $data['smt_active'] = $smt;
        $data['mapel'] = $this->dropdown->getAllJadwalMapel($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_footer');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['sesi'] = $this->dropdown->getAllSesi();
        $data['ruang'] = $this->dropdown->getAllRuang();
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $this->load->view('cbt/cetak/beritaacara');
        $tp = $this->dashboard->getTahunActive();
    }
    public function saveKopBerita()
    {
        $header_4 = $this->input->post('header_4', true);
        $header_3 = $this->input->post('header_3', true);
        $insert = ['id_kop' => 123456, 'header_1' => $header_1, 'header_2' => $header_2, 'header_3' => $header_3, 'header_4' => $header_4];
        $header_2 = $this->input->post('header_2', true);
        $update = $this->db->replace('cbt_kop_berita', $insert);
        $this->output_json($update);
        $header_1 = $this->input->post('header_1', true);
    }
    public function pesertaUjian($mode = null)
    {
        $data['ruangs'] = $this->dropdown->getAllRuang();
        $this->load->model('Dropdown_model', 'dropdown');
        $data['kelass'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $data['kop'] = $this->dashboard->getSetting();
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/guru/templates/header', $data);
        if ($this->ion_auth->is_admin()) {
        }
        $tp = $this->dashboard->getTahunActive();
        $data['siswa'] = $this->cbt->getAllPesertaByRuang($tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['siswa'] = $this->cbt->getAllPesertaByKelas($tp->id_tp, $smt->id_smt);
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('members/guru/templates/footer');
        $this->load->view('cbt/cetak/pesertaujian');
        $data['sesis'] = $this->cbt->getAllKodeSesi();
        $this->load->model('Cbt_model', 'cbt');
        $data = ['user' => $user, 'judul' => 'Cetak Daftar Peserta', 'subjudul' => 'Cetak', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('cbt/cetak/pesertaujian');
        $user = $this->ion_auth->user()->row();
        $data['tp_active'] = $tp;
        $data['tp'] = $this->dashboard->getTahun();
        $smt = $this->dashboard->getSemesterActive();
        $data['ujian'] = $this->dropdown->getAllJenisUjian();
        $data['mode'] = $mode;
        if ($mode == '1' || $mode == null) {
        }
    }
    public function pengawas()
    {
        $data['dari_selected'] = $dari_selected;
        $kelas_level = $this->cbt->getDistinctKelasLevel($tp->id_tp, $smt->id_smt, $arrLevel);
        if ($this->ion_auth->is_admin()) {
        }
        if (!(count($id_jenis) > 0)) {
        }
        $this->load->view('cbt/cetak/pengawas');
        $data['jenis_selected'] = $jenis_selected;
        $this->load->model('Cbt_model', 'cbt');
        $result = [];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data = ['user' => $user, 'judul' => 'Jadwal Pengawas', 'subjudul' => 'Cetak Jadwal Pengawas', 'setting' => $setting];
        foreach ($kelas_level as $kl) {
            array_push($arrKls, $kl->id_kelas);
        }
        $id_jenis = $this->cbt->getDistinctJenisJadwal($tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $this->load->view('_templates/dashboard/_header', $data);
        foreach ($jadwals as $jadwal) {
            if (in_array($jadwal->bank_level, $arrLevel)) {
            }
            array_push($arrLevel, $jadwal->bank_level);
        }
        $ids = [];
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/guru/templates/header', $data);
        foreach ($id_jenis as $jenis) {
            array_push($ids, $jenis->id_jenis);
        }
        $kelas_level = [];
        $gurus = $this->dropdown->getAllGuru();
        foreach ($jadwal_pengawas as $jadwal_pengawa) {
            foreach ($jadwal_pengawa as $r => $jp) {
                foreach ($jp as $s => $j) {
                    foreach ($j as $m => $km) {
                        $pw = '';
                        $jp = 0;
                        $ns = $ruangs[$r][$s]->nama_sesi;
                        $forAdd = json_decode(json_encode(['jml_siswa' => count($siswas), 'tanggal' => $km->tgl_mulai, 'ruang' => $nr, 'sesi' => $ns, 'mapel' => $km->nama_mapel, 'waktu' => $km->jam_ke, 'pengawas' => $pw]));
                        $jpp = count($sel);
                        array_push($result, $forAdd);
                        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ir, $is);
                        if (isset($perRuang[$forAdd->ruang])) {
                        }
                        $nr = $ruangs[$r][$s]->nama_ruang;
                        $perRuang[$forAdd->ruang] = [];
                        $is = $ruangs[$r][$s]->sesi_id;
                        array_push($perRuang[$forAdd->ruang], $forAdd);
                        $sel = isset($pengawas[$km->id_jadwal]) && isset($pengawas[$km->id_jadwal][$ir]) && isset($pengawas[$km->id_jadwal][$ir][$is]) ? explode(',', $pengawas[$km->id_jadwal][$ir][$is]->id_guru ?? '') : [];
                        foreach ($sel as $p) {
                            if (!($jp < $jpp)) {
                            }
                            if (!isset($gurus[$p])) {
                            }
                            $pw .= $gurus[$p];
                            $jp += 1;
                            $pw .= '<br>';
                        }
                        $ir = $ruangs[$r][$s]->ruang_id;
                        array_push($perRuang[$forAdd->ruang], $forAdd);
                    }
                }
            }
        }
        $data['ruang_sesi'] = $this->cbt->getRuangSesi($tp->id_tp, $smt->id_smt);
        $sampai_selected = $this->input->get('sampai', true);
        $data['filter_selected'] = $filter_selected;
        $data['jenis_ujian'] = $jenis_ujian;
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        foreach ($ruangs as $id_ruang => $ruang) {
            foreach ($ruang as $id_sesi => $sesi) {
                foreach ($kelas_level as $kl) {
                    foreach ($jadwals as $jadwal) {
                        $jadwal_pengawas[$jadwal->tgl_mulai][$id_ruang][$id_sesi][$jadwal->kode] = $jadwal;
                        if (!($jadwal->bank_level == $kl->level_id)) {
                        }
                    }
                }
            }
        }
        if (!(count($arrLevel) > 0)) {
        }
        $pengawas = [];
        $data['sampai_selected'] = $sampai_selected;
        $data['kelas_level'] = $kelas_level;
        $data['jenis'] = $this->cbt->getAllJenisUjianByArrJenis($ids);
        $data['smt'] = $this->dashboard->getSemester();
        $jadwals = $this->cbt->getJadwalByJenis($jenis_selected, '0', $dari_selected, $sampai_selected);
        $dari_selected = $this->input->get('dari', true);
        $user = $this->ion_auth->user()->row();
        $ruangs = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls);
        $jadwals = [];
        $this->load->view('cbt/cetak/pengawas');
        $this->load->model('Dropdown_model', 'dropdown');
        $filter_selected = $this->input->get('filter', true);
        $arrLevel = [];
        $tp = $this->dashboard->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        $jadwal_pengawas = [];
        $data['smt_active'] = $smt;
        $setting = $this->dashboard->getSetting();
        $jenis_ujian = $this->cbt->getJenisById($jenis_selected);
        $data['jadwals'] = $result;
        $data['sesi'] = $this->dropdown->getAllSesi();
        $data['jadwals_ruang'] = $perRuang;
        $data['jenis'] = ['' => 'belum ada jadwal ujian'];
        if (count($ids) > 0) {
        }
        $this->load->view('members/guru/templates/footer');
        if (!(count($arrKls) > 0)) {
        }
        if (!($jenis_selected != null)) {
        }
        if (!($jenis_selected != null)) {
        }
        $jenis_selected = $this->input->get('jenis', true);
        $pengawas = $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_footer');
        $data['pengawas'] = $pengawas;
        $data['filter'] = ['0' => 'Semua', '1' => 'Tanggal'];
        $arrKls = [];
        $data['ruang'] = $ruangs;
        $perRuang = [];
    }
}
```

---

## File: application/controllers_decoded/Cbtjadwal.php

```php
<?php

class Cbtjadwal extends CI_Controller
{
    public function __construct()
    {
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->library(['datatables', 'form_validation']);
        parent::__construct();
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        redirect('auth');
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function index()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $data['id_guru'] = '';
        $data['id_level'] = $id_level;
        $data['id_level'] = '';
        if ($type == '2') {
        }
        if ($type == '3') {
        }
        $data['jadwal'] = json_decode(json_encode($this->cbt->dummyJadwal()));
        $data['id_guru'] = '';
        $lvl = $this->input->get('level', true);
        $data['jmlMapel'] = [];
        $data['jadwals'] = $this->cbt->getAllDataJadwal($guru->id_guru, $id_mapel);
        $data['tp'] = $this->dashboard->getTahun();
        $type = $this->input->get('type');
        if ($this->ion_auth->is_admin()) {
        }
        $data['id_filter'] = $type == null ? '' : $type;
        $terpakai = $this->cbt->getJadwalTerpakai();
        $data['id_guru'] = null;
        $data['jadwals'] = $this->cbt->getAllDataJadwal(null, null, $id_level);
        $data['id_level'] = null;
        $data['mode'] = $mode == null ? '1' : $mode;
        $this->load->model('Dashboard_model', 'dashboard');
        $data['id_mapel'] = null;
        $data['id_mapel'] = $id_mapel;
        $data['jadwals'] = $this->cbt->getAllDataJadwal();
        $data['id_mapel'] = null;
        $id_mapel = $this->input->get('id');
        $data['total_siswa'] = $jadwal_terpakai;
        $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        $tp = $this->dashboard->getTahunActive();
        $data['id_level'] = null;
        $data['tp_active'] = $tp;
        $data['id_mapel'] = null;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['filters'] = ['0' => 'Semua', '2' => 'Mapel', '3' => 'Level'];
        $data['id_guru'] = '';
        $data['id_level'] = '';
        if (!$mapel) {
        }
        $data = ['user' => $user, 'judul' => 'Jadwal Penilaian', 'subjudul' => 'PH/PTS/PAT/USBK', 'setting' => $setting];
        $this->load->view('members/guru/templates/header', $data);
        $id_level = $this->input->get('id');
        $data['id_level'] = null;
        $this->load->view('cbt/jadwal/data');
        $data['id_mapel'] = $id_mapel;
        $mode = $this->input->get('mode');
        $data['gurus'] = $this->dropdown->getAllGuru();
        $data['levels'] = $this->dropdown->getAllLevel($setting->jenjang);
        foreach ($terpakai as $idj => $rows) {
            $jadwal_terpakai[$idj] = count($rows);
        }
        $data['id_level'] = $id_level;
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $data['id_mapel'] = '';
        $data['id_level'] = null;
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $jadwal_terpakai = [];
        $data['id_mapel'] = '';
        $this->load->view('members/guru/templates/footer');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['id_mapel'] = null;
        $data['id_guru'] = null;
        $data['filters'] = ['0' => 'Semua', '1' => 'Guru', '2' => 'Mapel', '3' => 'Level'];
        $this->load->view('_templates/dashboard/_header', $data);
        $data['id_mapel'] = '';
        $data['guru'] = $guru;
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_footer');
        $data['id_level'] = '';
        $data['id_guru'] = null;
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        if ($type == '0') {
        }
        $data['id_guru'] = '';
        $this->load->model('Dropdown_model', 'dropdown');
        $id_level = $this->input->get('id');
        $this->load->model('Cbt_model', 'cbt');
        $data['jadwals'] = $this->cbt->getAllDataJadwal($id_guru);
        $arrMapel = [];
        $data['ruangs'] = $this->cbt->getAllRuang();
        $this->load->view('cbt/jadwal/data');
        $data['jadwals'] = $this->cbt->getAllDataJadwal($guru->id_guru, null, $id_level);
        $data['level'] = $level;
        $data['id_guru'] = null;
        $setting = $this->dashboard->getSetting();
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $data['id_guru'] = $id_guru;
        $id_mapel = $this->input->get('id');
        if ($type == '1') {
        }
        $data['jmlIst'] = [];
        $level = $lvl == null ? '0' : $lvl;
        $data['jadwals'] = $this->cbt->getAllDataJadwal($guru->id_guru);
        $data['id_filter'] = $type == null ? '' : $type;
        if (!$mode) {
        }
        $data['jadwals'] = $this->cbt->getAllDataJadwal(null, $id_mapel);
        $id_guru = $this->input->get('id');
        $data['mapels'] = $arrMapel;
        $this->load->model('Kelas_model', 'kelas');
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        if ($type == '2') {
        }
        if ($type == '0') {
        }
        $data['mapels'] = $this->dropdown->getAllMapel();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $user = $this->ion_auth->user()->row();
        $data['smt_active'] = $smt;
        if ($type == '3') {
        }
    }
    public function add($id_jadwal)
    {
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $this->load->model('Dashboard_model', 'dashboard');
        $data['disable_opsi'] = $enable != null && $enable == 1;
        $data['mapel'] = $arrMapel;
        $this->load->view('members/guru/templates/header', $data);
        $data['mapel'] = $this->dropdown->getAllMapel();
        $gurus = $this->dropdown->getAllGuru();
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $enable = $this->input->get('enable', true);
        $data['ruangs'] = $this->cbt->getAllRuang();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $this->load->model('Cbt_model', 'cbt');
        $data['smt_active'] = $smt;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        if ($this->ion_auth->is_admin()) {
        }
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $this->load->view('_templates/dashboard/_footer');
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $data['jadwal'] = json_decode(json_encode($this->cbt->dummyJadwal()));
        $data['jadwal'] = $this->cbt->getJadwalById($id_jadwal);
        $data = ['user' => $user, 'judul' => $id_jadwal == 0 ? 'Tambah Jadwal Ujian' : 'Edit Jadwal Ujian', 'subjudul' => 'Jadwal', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('cbt/jadwal/add');
        $this->load->view('cbt/jadwal/add');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('members/guru/templates/footer');
        if ($id_jadwal == 0) {
        }
        $data['guru'] = $gurus;
        $arrMapel = [];
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['tp_active'] = $tp;
    }
    public function getBankMapel($id_mapel)
    {
        foreach ($banks as $key => $bank) {
            $ada2 = $num2 == (int) $bank->tampil_kompleks;
            $ada4 = $num4 == (int) $bank->tampil_isian;
            $num4 = isset($cek_soal['4']) ? count($cek_soal['4']) : 0;
            $ada5 = $num5 == (int) $bank->tampil_esai;
            $filtered[$key] = $bank->bank_kode;
            $ada3 = $num3 == (int) $bank->tampil_jodohkan;
            $num2 = isset($cek_soal['2']) ? count($cek_soal['2']) : 0;
            if (!($ada1 && $ada2 && $ada3 && $ada4 && $ada5)) {
            }
            $cek_soal = $this->cbt->getJumlahJenisSoal($key);
            $num5 = isset($cek_soal['5']) ? count($cek_soal['5']) : 0;
            $num3 = isset($cek_soal['3']) ? count($cek_soal['3']) : 0;
            $ada1 = $num1 == (int) $bank->tampil_pg;
            $num1 = isset($cek_soal['1']) ? count($cek_soal['1']) : 0;
        }
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $banks = $this->cbt->getAllBankSoalByMapel($tp->id_tp, $smt->id_smt, $id_mapel);
        $filtered = [];
        $tp = $this->dashboard->getTahunActive();
        $this->output_json($filtered);
    }
    public function saveJadwal()
    {
        $status = $res;
        $id = $this->input->post('id_jadwal', true);
        $this->output_json($data);
        $res = $this->cbt->saveJadwalUjian($tp->id_tp, $smt->id_smt);
        if ($this->input->post()) {
        }
        $data['success'] = $status;
        $this->logging->saveLog(4, 'mengedit jadwal pelajaran');
        $this->logging->saveLog(3, 'menambah jadwal pelajaran');
        $data['message'] = 'Kesalahan 404';
        if (!$id) {
        }
        $this->load->model('Log_model', 'logging');
        $status = FALSE;
        $tp = $this->dashboard->getTahunActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->dashboard->getSemesterActive();
        $data['message'] = $res ? 'Jadwal berhasil disimpan' : 'Jadwal sudah ada';
        $this->load->model('Cbt_model', 'cbt');
    }
    public function deleteJadwal()
    {
        $data['message'] = 'Jadwal Ujian sedang digunakan';
        $id = $this->input->get('id_jadwal', true);
        $jadwal = $this->cbt->getJadwalById($id);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $data['status'] = false;
        if ($terpakai && $jadwal->rekap == 0) {
        }
        $this->output_json($data);
        $terpakai = isset($jadwal_dikerjakan[$id]) && count($jadwal_dikerjakan[$id]) > 0;
        $data['message'] = 'berhasil';
        $this->logging->saveLog(5, 'menghapus jadwal ujian');
        $this->load->model('Master_model', 'master');
        $hapusNilaiSiswa = $this->master->delete('cbt_soal_siswa', $id, 'id_jadwal');
        $data['status'] = $hapusNilaiSiswa && $hapusDurasiSiswa;
        $data['status'] = false;
        $this->load->model('Log_model', 'logging');
        $this->load->model('Cbt_model', 'cbt');
        $data['message'] = 'Hasil Ujian belum direkap';
        if ($this->master->delete('cbt_jadwal', $id, 'id_jadwal')) {
        }
        $data['status'] = false;
        $hapusDurasiSiswa = $this->master->delete('cbt_durasi_siswa', $id, 'id_jadwal');
    }
    public function deleteAllJadwal()
    {
        $count_terpakai = array_count_values($digunakan);
        $data['status'] = false;
        $hapusDurasiSiswa = $this->master->delete('cbt_durasi_siswa', $arrId, 'id_jadwal');
        $data['message'] = 'Hasil Ujian belum direkap';
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $data['backup'] = $counts;
        $jadwals = $this->cbt->getJadwalByArrId($arrId);
        $this->load->model('Master_model', 'master');
        $digunakan = [];
        ob_end_clean();
        if ($count_terpakai[1] > 0 && $counts[0] > 0) {
        }
        $hapusNilaiSiswa = $this->master->delete('cbt_soal_siswa', $arrId, 'id_jadwal');
        $data['message'] = 'berhasil';
        $this->logging->saveLog(5, 'menghapus jadwal ujian');
        $data['status'] = false;
        ob_end_clean();
        $this->load->model('Cbt_model', 'cbt');
        $counts = array_count_values($backuped);
        $backuped = [];
        ob_start();
        ob_end_clean();
        foreach ($jadwals as $jadwal) {
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0 ? 1 : 0;
            array_push($backuped, $jadwal->rekap);
            array_push($digunakan, $terpakai);
        }
        $data['digunakan'] = $count_terpakai;
        $data['status'] = $hapusNilaiSiswa && $hapusDurasiSiswa;
        $this->load->model('Log_model', 'logging');
        $data['message'] = 'Jadwal Ujian sedang digunakan';
        if ($this->master->delete('cbt_jadwal', $arrId, 'id_jadwal')) {
        }
        $this->output_json($data);
        $arrId = json_decode($this->input->post('checked', true));
    }
}
```

---

## File: application/controllers_decoded/Cbtjenis.php

```php
<?php

class Cbtjenis extends CI_Controller
{
    public function __construct()
    {
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library(['datatables', 'form_validation']);
        parent::__construct();
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        redirect('auth');
        if (!$this->ion_auth->logged_in()) {
        }
        if ($this->ion_auth->is_admin()) {
        }
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('cbt/jenis/data');
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Jenis Ujian', 'subjudul' => 'Data Jenis Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
    }
    public function data()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getJenis(), false);
    }
    public function add()
    {
        $this->load->model('Master_model', 'master');
        $this->output_json($data);
        $data['status'] = $insert;
        $this->master->create('cbt_jenis', $insert, false);
        $insert = ['nama_jenis' => $this->input->post('nama_jenis', true), 'kode_jenis' => $this->input->post('kode_jenis', true)];
    }
    public function update()
    {
        $data = $this->cbt->updateJenis();
        $this->output->set_content_type('application/json')->set_output($data);
        $this->load->model('Cbt_model', 'cbt');
    }
    public function delete()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('checked', true);
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $this->output_json(['status' => false]);
        if (!$chk) {
        }
        if (!$this->master->delete('cbt_jenis', $chk, 'id_jenis')) {
        }
    }
    public function saveLog($type, $desc)
    {
        $this->logging->saveLog($type, $desc);
        $user = $this->ion_auth->user()->row();
        $this->load->model('Log_model', 'logging');
    }
}
```

---

## File: application/controllers_decoded/Cbtnilai.php

```php
<?php

class Cbtnilai extends CI_Controller
{
    public function __construct()
    {
        $this->form_validation->set_error_delimiters('', '');
        redirect('auth');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
    }
    private function arrToUpper($val)
    {
        return strtoupper($val ?? '');
    }
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            sort($subArray);
            if (!$subArray) {
            }
        }
    }
    public function index()
    {
        $data['siswas'] = $siswas;
        $this->load->view('members/guru/templates/footer');
        foreach ($siswas as $key => $value) {
            array_push($ids, $value->id_siswa);
        }
        $data['tp_active'] = $tp;
        $data['kelas_selected'] = $kelas_selected;
        $this->load->model('Dropdown_model', 'dropdown');
        $bobot_pg = $info->bobot_pg / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $ids = [];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas_selected);
        $data['jadwal'] = [];
        $info = $this->cbt->getJadwalById($jadwal_selected);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal_selected);
        if ($this->ion_auth->is_admin()) {
        }
        $ya = $this->input->get('ya');
        foreach ($jawabans as $jawaban_siswa) {
            $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
            $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar);
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                foreach ($jbs as $idxs => $jb) {
                    if (!($jb === '1')) {
                    }
                    if (!($idxs > 0)) {
                    }
                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                }
                if (!($idx > 0)) {
                }
                $arrjwbnSiswa[$idx] = [];
            }
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa->links = json_decode(json_encode($arrjwbnSiswa));
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            if (!($jawaban_siswa->jenis_soal == '3')) {
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $arrjwbnSiswa = [];
            $jawaban_siswa->jawaban = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban);
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                $arrjwbn[$idx] = [];
                if (!($idx > 0)) {
                }
                foreach ($jbs as $idxs => $jb) {
                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                }
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            if (!($jawaban_siswa->jenis_soal == '2')) {
            }
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban, 'strlen');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $arrAlphabet = range('A', 'Z');
            $arrjwbn = [];
        }
        foreach ($kelas_bank as $key => $value) {
            if (!($value['kelas_id'] != '')) {
            }
            $kelases[$value['kelas_id']] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $value['kelas_id']);
        }
        $data = ['user' => $user, 'judul' => 'Hasil Ujian Siswa', 'subjudul' => 'Nilai Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->db->trans_complete();
        if (!($mapel != null)) {
        }
        $jdwl = [];
        $jadwals = $this->cbt->getAllJadwal($tp->id_tp, $smt->id_smt, $id_guru);
        $jadwal_selected = $this->input->get('jadwal');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $xb = $this->input->get('xb');
        $jawabans_siswa = [];
        $yb = $this->input->get('yb');
        $kelas_bank = unserialize($info->bank_kelas ?? '');
        $this->load->view('_templates/dashboard/_header', $data);
        if (!($ya != null)) {
        }
        $bagi_isian = $info->tampil_isian / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $id_guru = null;
        if ($jadwal_selected != null) {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $data['info'] = $info;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $jawabans = $this->cbt->getJawabanSiswaByJadwal($jadwal_selected, $ids);
        $bagi_essai = $info->tampil_esai / 100;
        $data['jadwal'] = $jdwl;
        $data['ruang'] = $this->dropdown->getAllRuang();
        $soal = [];
        $data['smt'] = $this->dashboard->getSemester();
        $data['jadwal_selected'] = $jadwal_selected;
        $data['convert'] = $convert;
        $data['sesi'] = $this->dropdown->getAllSesi();
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $data['siswas'] = [];
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        $xa = $this->input->get('xa');
        $data['jadwal_selected'] = $jadwal_selected;
        $id_guru = $guru->id_guru;
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                if (!$kls->kelas) {
                }
                $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
            }
        }
        foreach ($jadwals as $jadwal) {
            foreach ($kls as $kl) {
                if (!($kl['kelas_id'] == $kelas_selected)) {
                }
                $jdwl[$jadwal->id_jadwal] = $jadwal->bank_kode;
            }
            $kls = unserialize($jadwal->bank_kelas ?? '');
        }
        $user = $this->ion_auth->user()->row();
        $bobot_essai = $info->bobot_esai / 100;
        $data['guru'] = $guru;
        $smt = $this->dashboard->getSemesterActive();
        $data['kelas'] = $arrKelas;
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('cbt/nilai/data');
        $convert = ['ya' => $ya, 'yb' => $yb, 'xa' => $xa, 'xb' => $xb];
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $this->load->model('Dashboard_model', 'dashboard');
        $arrKelas = [];
        $this->db->trans_start();
        $this->load->model('Kelas_model', 'kelas');
        $data['smt_active'] = $smt;
        $kelas_selected = $this->input->get('kelas');
        $logs = $this->cbt->getLogUjianByJadwal($jadwal_selected);
        $this->load->view('cbt/nilai/data');
        $this->load->model('Cbt_model', 'cbt');
        $kelases = [];
        foreach ($siswas as $siswa) {
            $dur_siswa = '';
            $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
            $loading = '';
            $siswa->skor_essai = round($skor_es, 2);
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
            $input_jod = $nilai_input->jodohkan_nilai;
            $siswa->durasi_ujian = $lamanya;
            if (!($n < $info->tampil_pg)) {
            }
            $siswa->skor_katrol = round(($ya - $yb) / 100 * $total + $yb, 2);
            $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            $benar_pg2 = 0;
            $benar_es = 0;
            foreach ($jawaban_is as $num => $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $soal[4][$ks]->point_otomatis = $point;
                $benar_is++;
                $soal[4][$ks]->point_koreksi = $jawab_is->nilai_koreksi;
                $point = !$benar ? 0 : ($info->bobot_isian > 0 ? round($info->bobot_isian / $info->tampil_isian, 2) : 0);
                $soal[4][$ks]->point = $jawab_is->nilai_koreksi;
                if (!$benar) {
                }
                if ($jawab_is->nilai_otomatis == '0') {
                }
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
                $ks = array_search($jawab_is->nomor_soal, array_column($soal[4], 'nomor_soal'));
                $otomatis_is = $jawab_is->nilai_otomatis;
                $soal[4][$ks]->point = $point;
            }
            $otomatis_jod = 0;
            foreach ($jawaban_pg as $num => $jwb_pg) {
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                $benar = false;
                $benar_pg += 1;
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                }
                $benar = true;
                $arrJawabanPg[$num] = ['jawaban' => strtoupper($jwb_pg->jawaban_siswa ?? ''), 'benar' => $benar];
                $benar = false;
            }
            $siswa->skor_isian = round($skor_is, 2);
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
            }
            $skor_koreksi_is = 0.0;
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            $input_jod = 0;
            if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
            }
            $siswa->skor_katrol = '';
            if (!($info->tampil_esai > 0)) {
            }
            $siswa->skor_total = round($total, 2);
            $n = 0;
            $otomatis_is = 0;
            $mulai = '- -  :  - -';
            $input_is = 0;
            $input_pg2 = $nilai_input->kompleks_nilai;
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $siswa->skor_kompleks = round($skor_pg2, 2);
            if (!($info->tampil_isian > 0)) {
            }
            $otomatis_es = 0;
            $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            $skor_pg = $benar_pg / $bagi_pg * $bobot_pg;
            $siswa->skor_jodohkan = round($skor_jod, 2);
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal_selected, $siswa->id_siswa);
            $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            $lamanya = '';
            $siswa->dikoreksi = $nilai_input->dikoreksi;
            $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
            $skor_pg = 0;
            $input_es = $nilai_input->essai_nilai;
            $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
            $selesai = '- -  :  - -';
            $siswa->jawaban_pg = $arrJawabanPg;
            $benar_is = 0;
            $input_pg2 = 0;
            if (!($nilai_input != null)) {
            }
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
            $benar_jod = 0;
            $skor_koreksi_pg2 = 0.0;
            $input_es = 0;
            $n++;
            $otomatis_pg2 = 0;
            if ($ya != null) {
            }
            $arrJawabanPg = [];
            foreach ($jawaban_es as $num => $jawab_es) {
                $soal[5][$ks]->point_otomatis = $point;
                $benar_es++;
                $ks = array_search($jawab_es->nomor_soal, array_column($soal[5], 'nomor_soal'));
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
                $soal[5][$ks]->point_koreksi = $jawab_es->nilai_koreksi;
                $soal[5][$ks]->point = $point;
                if (!$benar) {
                }
                $soal[5][$ks]->point = $jawab_es->nilai_koreksi;
                $point = !$benar ? 0 : ($info->bobot_esai > 0 ? round($info->bobot_esai / $info->tampil_esai, 2) : 0);
                if ($jawab_es->nilai_otomatis == '0') {
                }
                $skor_koreksi_es += (int) $jawab_es->nilai_koreksi;
                $otomatis_es = $jawab_es->nilai_otomatis;
            }
            $siswa->skor_pg = round($skor_pg, 2);
            if (!($total < $xb)) {
            }
            if (!($info->tampil_pg > 0)) {
            }
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $xa = $total;
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $pk = $point_item * count($arr_benar);
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $soal[2][$ks]->point_otomatis = $point;
                $soal[2][$ks]->point_koreksi = $jawab_pg2->nilai_koreksi;
                $soal[2][$ks]->point = $point;
                if ($jawab_pg2->nilai_otomatis == '0') {
                }
                $point = round($pk, 2);
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
                if (!(count($jawab_pg2->jawaban) > 0)) {
                }
                $point_item = count($jawab_pg2->jawaban) > 0 ? $point_benar / count($jawab_pg2->jawaban) : 0;
                $point_benar = $info->bobot_kompleks > 0 ? round($info->bobot_kompleks / $info->tampil_kompleks, 2) : 0;
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    if (!in_array($js, $jawab_pg2->jawaban)) {
                    }
                    array_push($arr_benar, true);
                }
                $arr_benar = [];
                $ks = array_search($jawab_pg2->nomor_soal, array_column($soal[2], 'nomor_soal'));
                if (!$jawab_pg2->jawaban_siswa) {
                }
                $soal[2][$ks]->point = $jawab_pg2->nilai_koreksi;
            }
            $siswa->mulai_ujian = $mulai;
            $benar_pg = 0;
            $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
            foreach ($logs as $log) {
                if (!($log != null)) {
                }
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
                if (!($log != null)) {
                }
                $sudahSelesai = false;
                $sudahMulai = false;
                $sudahMulai = true;
                if ($log->log_type == '1') {
                }
                $mulai = date('H:i', strtotime($log->log_time));
                $sudahSelesai = true;
                $selesai = date('H:i', strtotime($log->log_time));
                $loading = $sudahSelesai ? '<i class="fa fa-check"></i> ' : ($sudahMulai ? '<i class="fa fa-spinner fa-spin"></i> ' : '');
            }
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $siswa->selesai_ujian = $selesai;
            $siswa->lama_ujian = $loading . $dur_siswa;
            if (!($total > $xa)) {
            }
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            if (!($info->tampil_kompleks > 0)) {
            }
            $arrJawabanPg[$n + 1] = ['jawaban' => '', 'benar' => false];
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $input_is = $nilai_input->isian_nilai;
            $skor_koreksi_es = 0.0;
            $differentCounts = [];
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $item_kurang = 0;
                $soal[3][$ks]->tabel_jawab = $arrJwbJawab;
                if (!isset($jawab_jod->jawaban_siswa->jawaban)) {
                }
                $soal[3][$ks]->tabel_benar = $arrBenar;
                $soal[3][$ks]->tabel_soal = $arrJwbSoal;
                $soal[3][$ks]->point_otomatis = $point;
                $benar_jod += 1 / $items * $item_benar;
                $analisa = '<i class="fa fa-times-circle text-red text-lg"></i>';
                $headSoal = array_shift($arrSoal);
                $arrSoal = $jawab_jod->jawaban->jawaban;
                $arrJwbSoal = [];
                $differentCounts[3][$ks] = $differentCount;
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                $sameCounts[3][$ks] = $sameCount;
                $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
                $ks = array_search($jawab_jod->nomor_soal, array_column($soal[3], 'nomor_soal'));
                $soal[3][$ks]->point = $jawab_jod->nilai_koreksi;
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $arrJwbJawab = [];
                $typeSoal = $jawab_jod->jawaban->type;
                $arrJawab = [];
                $item_benar = 0;
                foreach ($arrSoal as $kolSoal) {
                    foreach ($kolSoal as $pos => $kol) {
                        $jwb->subtitle[] = $headSoal[$pos];
                        if (!($kol == '1')) {
                        }
                    }
                    $arrJwbSoal[] = $jwb;
                    $jwb->title = array_shift($kolSoal);
                    $jwb = new stdClass();
                }
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                }
                $arrJawab = $jawab_jod->jawaban_siswa->jawaban;
                $point = round($point_soal, 2);
                $otomatis_jod = $jawab_jod->nilai_otomatis;
                $item_salah = 0;
                $headJawab = array_shift($arrJawab);
                if ($jawab_jod->nilai_otomatis == '0') {
                }
                if ($item_benar == $items && $item_salah == 0 && $item_kurang == 0) {
                }
                $sameCount = 0;
                $array1 = (array) $jawab_jod->jawaban_benar->links;
                $this->sortArrays($array1);
                $analisa = '<i class="fa fa-check-circle text-green text-lg"></i>';
                $soal[3][$ks]->point_koreksi = $jawab_jod->nilai_koreksi;
                $items = 0;
                $soal[3][$ks]->type_soal = $typeSoal;
                foreach ($arrJawab as $kolJawab) {
                    $jwbs->title = array_shift($kolJawab);
                    $jwbs = new stdClass();
                    $arrJwbJawab[] = $jwbs;
                    foreach ($kolJawab as $po => $kol) {
                        if (!($kol == '1')) {
                        }
                        $jwbs->subtitle[] = $sub;
                        $sub = $headJawab[$po];
                    }
                }
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $differentCount = 0;
                $arrBenar = [];
                $this->sortArrays($array2);
                if ($item_benar == 0) {
                }
                $soal[3][$ks]->point = $point;
                foreach ($array1 as $key => $subArray1) {
                    $arrBenar[$key]->kurang += count($subArray1);
                    $diffItems2 = array_diff($subArray2, $subArray1);
                    $arrBenar[$key]->benar += count($sameItems);
                    $differentCount += count($subArray1);
                    $item_benar += count($sameItems);
                    $differentCount += count($diffItems1) + count($diffItems2);
                    $item_kurang += count($subArray1);
                    $arrBenar[$key]->salah = 0;
                    $item_kurang += count($diffItems1) + count($diffItems2);
                    if (isset($array2[$key])) {
                    }
                    $arrBenar[$key]->benar = 0;
                    $subArray2 = $array2[$key];
                    $arrBenar[$key]->kurang += count($diffItems1);
                    $arrBenar[$key] = new stdClass();
                    $diffItems1 = array_diff($subArray1, $subArray2);
                    $sameItems = array_intersect($subArray1, $subArray2);
                    $items += count($subArray1);
                    $sameCount += count($sameItems);
                    $arrBenar[$key]->kurang = 0;
                }
                $soal[3][$ks]->point_soal = $point_soal;
                $soal[3][$ks]->analisa = $analisa;
                $point_soal = 1 / $items * $item_benar * $point_benar;
            }
            $skor_koreksi_jod = 0.0;
            $xb = $total;
            if (count($jawaban_pg) > 0) {
            }
            $sameCounts = [];
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            foreach ($durasies as $durasi) {
                $dur_siswa = $durasi->mulai . ' m';
                $lamanya = $durasi->lama_ujian;
                $dd = $ej . $em;
                if (strpos($lamanya, ':') !== false) {
                }
                if ($durasi->lama_ujian == null) {
                }
                $ej = $elap[0] == '00' ? '' : intval($elap[0]) . 'j ';
                $ed = $elap[2] == '00' ? 0 : 1;
                $dur_siswa = $dd == '' ? '0 m' : $dd;
                $elap = explode(':', $lamanya ?? '');
                $em = $elap[1] == '00' ? '' : intval($elap[1]) + $ed . 'm';
                $mins = (strtotime($durasi->selesai) - strtotime($durasi->mulai)) / 60;
                $dur_siswa = round($mins, 2) . ' m';
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
            }
        }
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $bagi_pg = $info->tampil_pg / 100;
        if ($this->ion_auth->in_group('guru')) {
        }
    }
    public function detail()
    {
        $data['smt'] = $this->dashboard->getSemester();
        foreach ($jawabans as $jawaban_siswa) {
            $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban, 'strlen');
            $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar ?? ['']);
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            if (!($jawaban_siswa->jenis_soal == '3')) {
            }
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa->links = json_decode(json_encode($arrjwbnSiswa));
            $arrAlphabet = range('A', 'Z');
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                foreach ($jbs as $idxs => $jb) {
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                }
                if (!($idx > 0)) {
                }
                $arrjwbn[$idx] = [];
            }
            $arrjwbnSiswa = [];
            $jawaban_siswa->jawaban = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban ?? ['']);
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
            $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
            if ($jawaban_siswa->jawaban_siswa) {
            }
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            $arrjwbn = [];
            if (!($jawaban_siswa->jenis_soal == '2')) {
            }
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                }
                $arrjwbnSiswa[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    if (!($jb === '1')) {
                    }
                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                    if (!($idxs > 0)) {
                    }
                }
            }
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
        }
        $tp = $this->dashboard->getTahunActive();
        foreach ($jawaban_pg2 as $num => $jawab_pg2) {
            $analisa = '<i class="fa fa-times-circle text-red text-lg"></i>';
            if ($jml_benar > 0 && $jml_benar < count($jawab_pg2->jawaban)) {
            }
            $soal[2][$ks]->point_koreksi = $jawab_pg2->nilai_koreksi;
            if (!$jawab_pg2->jawaban_siswa) {
            }
            $soal[2][$ks]->analisa = $analisa;
            $analisa = '<i class="fa fa-check-circle text-green text-lg"></i>';
            $arr_benar = [];
            foreach ($jawab_pg2->jawaban_siswa as $js) {
                if (!in_array($js, $jawab_pg2->jawaban)) {
                }
                array_push($arr_benar, true);
            }
            if ($jml_benar == count($jawab_pg2->jawaban)) {
            }
            $point_benar = $info->bobot_kompleks > 0 ? round($info->bobot_kompleks / $info->tampil_kompleks, 2) : 0;
            $point = round($pk, 2);
            $pk = $point_item * count($arr_benar);
            $soal[2][$ks]->point = $jawab_pg2->nilai_koreksi;
            $soal[2][$ks]->point = $point;
            $ks = array_search($jawab_pg2->nomor_soal, array_column($soal[2], 'nomor_soal'));
            if ($jawab_pg2->nilai_otomatis == '0') {
            }
            $soal[2][$ks]->point_otomatis = $point;
            $point_item = count($jawab_pg2->jawaban) > 0 ? $point_benar / count($jawab_pg2->jawaban) : 0;
            $jml_benar = count($arr_benar);
            if (!($jawab_pg2->jawaban && count($jawab_pg2->jawaban) > 0)) {
            }
            $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
            $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
        }
        $input_is = 0;
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $jadwal = $this->input->get('jadwal');
        if (!($info->tampil_kompleks > 0)) {
        }
        $bagi_pg = $info->tampil_pg / 100;
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $input_pg2 = 0;
        $input_jod = 0;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tp_active'] = $tp;
        $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
        $skor_koreksi_pg2 = 0.0;
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        if (!($nilai_input != null)) {
        }
        $bobot_isian = $info->bobot_isian / 100;
        $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
        $skor->dikoreksi = $nilai_input->dikoreksi;
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $otomatis_es = 0;
        $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
        $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
        $bobot_pg = $info->bobot_pg / 100;
        $benar_is = 0;
        $smt = $this->dashboard->getSemesterActive();
        if (!($info->tampil_isian > 0)) {
        }
        $skor->skor_pg = $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
        $benar_jod = 0;
        $benar_pg2 = 0;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $skor_koreksi_is = 0.0;
        if ($this->ion_auth->is_admin()) {
        }
        $skor->skor_isian = $skor_is;
        $skor_koreksi_es = 0.0;
        $dur_siswa = null;
        $skor->skor_essai = $skor_es;
        $this->load->view('members/guru/templates/footer');
        $this->load->view('members/guru/templates/header', $data);
        $siswa = $this->cbt->getSiswaById($tp->id_tp, $smt->id_smt, $this->input->get('siswa'));
        if (!(count($jawaban_is) > 0)) {
        }
        $jawabans = $this->cbt->getJawabanSiswaByJadwal($jadwal, $siswa->id_siswa);
        $benar_es = 0;
        foreach ($jawaban_pg as $num => $jwb_pg) {
            $benar = false;
            $analisa = $benar ? '<i class="fa fa-check-circle text-green text-lg"></i>' : '<i class="fa fa-times-circle text-red text-lg"></i>';
            $salah_pg += 1;
            if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
            }
            if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
            }
            $soal[1][$ks]->analisa = $analisa;
            $benar = true;
            $ks = array_search($jwb_pg->nomor_soal, array_column($soal[1], 'nomor_soal'));
            $benar = false;
            $soal[1][$ks]->point = !$benar ? 0 : ($info->bobot_pg > 0 ? round($info->bobot_pg / $info->tampil_pg, 2) : 0);
            $benar_pg += 1;
        }
        $bagi_isian = $info->tampil_isian / 100;
        $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
        $nilai_siswa = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
        $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
        $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
        foreach ($jawaban_jodoh as $num => $jawab_jod) {
            $soal[3][$ks]->point_koreksi = $jawab_jod->nilai_koreksi;
            $analisa = '<i class="fa fa-check-circle text-green text-lg"></i>';
            $sameCount = 0;
            foreach ($arrJawab as $kolJawab) {
                $jwbs->title = array_shift($kolJawab);
                $jwbs = new stdClass();
                $arrJwbJawab[] = $jwbs;
                foreach ($kolJawab as $po => $kol) {
                    $sub = $headJawab[$po];
                    $jwbs->subtitle[] = $sub;
                    if (!($kol == '1')) {
                    }
                }
            }
            $otomatis_jod = $jawab_jod->nilai_otomatis;
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            $arrJwbSoal = [];
            foreach ($arrSoal as $kolSoal) {
                $jwb = new stdClass();
                $jwb->title = array_shift($kolSoal);
                foreach ($kolSoal as $pos => $kol) {
                    $jwb->subtitle[] = $headSoal[$pos];
                    if (!($kol == '1')) {
                    }
                }
                $arrJwbSoal[] = $jwb;
            }
            $headJawab = array_shift($arrJawab);
            $soal[3][$ks]->point_soal = $point_soal;
            $soal[3][$ks]->point = $jawab_jod->nilai_koreksi;
            $arrBenar = [];
            if ($item_benar == $items && $item_salah == 0 && $item_kurang == 0) {
            }
            $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
            $typeSoal = $jawab_jod->jawaban->type;
            $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
            $soal[3][$ks]->tabel_jawab = $arrJwbJawab;
            $soal[3][$ks]->type_soal = $typeSoal;
            $benar_jod += 1 / $items * $item_benar;
            $this->sortArrays($array1);
            $item_salah = 0;
            if ($jawab_jod->nilai_otomatis == '0') {
            }
            $arrJawab = $jawab_jod->jawaban_siswa->jawaban;
            $headSoal = array_shift($arrSoal);
            $array1 = (array) $jawab_jod->jawaban_benar->links;
            $ks = array_search($jawab_jod->nomor_soal, array_column($soal[3], 'nomor_soal'));
            $arrJawab = [];
            if ($item_benar == 0) {
            }
            $soal[3][$ks]->point = $point;
            $soal[3][$ks]->tabel_soal = $arrJwbSoal;
            $items = 0;
            $soal[3][$ks]->point_otomatis = $point;
            if (!isset($jawab_jod->jawaban_siswa->links)) {
            }
            $analisa = '<i class="fa fa-times-circle text-red text-lg"></i>';
            $soal[3][$ks]->analisa = $analisa;
            $arrJwbJawab = [];
            if (!isset($jawab_jod->jawaban_siswa->jawaban)) {
            }
            $differentCount = 0;
            $point_soal = 1 / $items * $item_benar * $point_benar;
            $differentCounts[3][$ks] = $differentCount;
            $array2 = (array) $jawab_jod->jawaban_siswa->links;
            $sameCounts[3][$ks] = $sameCount;
            $arrSoal = $jawab_jod->jawaban->jawaban;
            $soal[3][$ks]->tabel_benar = $arrBenar;
            $this->sortArrays($array2);
            $item_benar = 0;
            foreach ($array1 as $key => $subArray1) {
                $arrBenar[$key] = new stdClass();
                $item_benar += count($sameItems);
                $sameCount += count($sameItems);
                $arrBenar[$key]->benar = 0;
                $arrBenar[$key]->kurang = 0;
                $diffItems1 = array_diff($subArray1, $subArray2);
                $item_kurang += count($subArray1);
                $arrBenar[$key]->benar += count($sameItems);
                $items += count($subArray1);
                $differentCount += count($diffItems1) + count($diffItems2);
                $differentCount += count($subArray1);
                $diffItems2 = array_diff($subArray2, $subArray1);
                $arrBenar[$key]->kurang += count($diffItems1);
                $item_kurang += count($diffItems1) + count($diffItems2);
                if (isset($array2[$key])) {
                }
                $sameItems = array_intersect($subArray1, $subArray2);
                $arrBenar[$key]->kurang += count($subArray1);
                $arrBenar[$key]->salah = 0;
                $subArray2 = $array2[$key];
            }
            $item_kurang = 0;
            $point = round($point_soal, 2);
        }
        $data['ada_nilai'] = $nilai_siswa != null;
        $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
        if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
        }
        $user = $this->ion_auth->user()->row();
        if (!(count($jawaban_es) > 0)) {
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $skor->skor_jodohkan = $skor_jod;
        $jawabans_siswa = [];
        if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
        }
        $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
        $this->load->view('_templates/dashboard/_footer');
        $skor->skor_kompleks = $skor_pg2;
        $data['tp'] = $this->dashboard->getTahun();
        $bagi_essai = $info->tampil_esai / 100;
        $input_pg2 = $nilai_input->kompleks_nilai;
        $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
        $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
        $this->load->view('cbt/nilai/detail');
        $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
        $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
        $bobot_essai = $info->bobot_esai / 100;
        $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
        $input_jod = $nilai_input->jodohkan_nilai;
        $skor_koreksi_jod = 0.0;
        if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
        }
        foreach ($jawaban_is as $num => $jawab_is) {
            $ks = array_search($jawab_is->nomor_soal, array_column($soal[4], 'nomor_soal'));
            $soal[4][$ks]->point = $jawab_is->nilai_koreksi;
            $otomatis_is = $jawab_is->nilai_otomatis;
            $soal[4][$ks]->point = $point;
            $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
            if ($benar) {
            }
            $soal[4][$ks]->point_koreksi = $jawab_is->nilai_koreksi;
            $analisa = '<i class="fa fa-check-circle text-green text-lg"></i>';
            $soal[4][$ks]->analisa = $analisa;
            $skor_koreksi_is += $jawab_is->nilai_koreksi;
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            if ($jawab_is->nilai_otomatis == '0') {
            }
            $point = !$benar ? 0 : ($info->bobot_isian > 0 ? round($info->bobot_isian / $info->tampil_isian, 2) : 0);
            $benar_is++;
            $soal[4][$ks]->point_otomatis = $point;
            if (!$benar) {
            }
        }
        $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
        $data = ['user' => $user, 'judul' => 'Koreksi Hasil Siswa', 'subjudul' => 'Hasil Siswa', 'setting' => $this->dashboard->getSetting(), 'durasi' => $dur_siswa, 'log' => $log_siswa];
        $input_is = $nilai_input->isian_nilai;
        if (!($jawaban_pg && count($jawaban_pg) > 0)) {
        }
        $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
        $this->load->view('cbt/nilai/detail');
        $data['soal'] = $soal;
        $this->load->model('Dashboard_model', 'dashboard');
        if (!($info->tampil_pg > 0)) {
        }
        $data['info'] = $info;
        $differentCounts = [];
        $log_siswa = [];
        $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
        $skor->skor_total = $total;
        $salah_pg = 0;
        $data['guru'] = $guru;
        if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
        }
        $skor = new stdClass();
        if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
        }
        $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
        foreach ($logs as $log) {
            if (!($log->id_siswa == $siswa->id_siswa)) {
            }
            array_push($log_siswa, $log);
        }
        $input_es = $nilai_input->essai_nilai;
        foreach ($jawaban_es as $num => $jawab_es) {
            if ($benar) {
            }
            $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
            $skor_koreksi_es += $jawab_es->nilai_koreksi;
            $analisa = '<i class="fa fa-check-circle text-green text-lg"></i>';
            $soal[5][$ks]->point_koreksi = $jawab_es->nilai_koreksi;
            $soal[5][$ks]->point_otomatis = $point;
            $soal[5][$ks]->point = $jawab_es->nilai_koreksi;
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            $point = !$benar ? 0 : ($info->bobot_esai > 0 ? round($info->bobot_esai / $info->tampil_esai, 2) : 0);
            $soal[5][$ks]->point = $point;
            $ks = array_search($jawab_es->nomor_soal, array_column($soal[5], 'nomor_soal'));
            $soal[5][$ks]->analisa = $analisa;
            if ($jawab_es->nilai_otomatis == '0') {
            }
            $benar_es++;
            if (!$benar) {
            }
            $otomatis_es = $jawab_es->nilai_otomatis;
        }
        $soal = [];
        $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
        $otomatis_jod = 0;
        $otomatis_pg2 = 0;
        $input_es = 0;
        $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
        $sameCounts = [];
        foreach ($durasies as $durasi) {
            $dur_siswa = $durasi;
            if (!($durasi->id_siswa == $siswa->id_siswa)) {
            }
        }
        $data['siswa'] = $siswa;
        if (!($info->tampil_esai > 0)) {
        }
        if (!($jawaban_pg2 && count($jawaban_pg2) > 0)) {
        }
        $this->load->model('Cbt_model', 'cbt');
        $otomatis_is = 0;
        $benar_pg = 0;
        $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
        $data['skor'] = $skor;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $info = $this->cbt->getJadwalById($jadwal);
    }
    public function simpanKoreksi()
    {
        if (!$updated) {
        }
        $this->db->update('cbt_nilai');
        $jadwal = $this->input->post('jadwal', true);
        $ids = [];
        $jml = 0;
        $siswa = $this->input->post('siswa', true);
        $jenis = $this->input->post('jenis', true);
        $updated = $this->db->update_batch('cbt_soal_siswa', $updated, 'id_soal_siswa');
        $data['success'] = $updated;
        foreach ($nilais as $nilai) {
            $updated[] = ['id_soal_siswa' => $nilai->id_soal, 'nilai_koreksi' => $nilai->koreksi, 'nilai_otomatis' => 1];
            array_push($ids, $nilai->id_soal);
            $jml += $nilai->koreksi;
        }
        $updated = [];
        $nilais = json_decode($this->input->post('nilai', true));
        $this->output_json($data);
        $this->db->where('id_nilai', $siswa . '0' . $jadwal);
        $this->db->set($jenis, $jml);
    }
    public function tandaiKoreksi()
    {
        $this->db->set('dikoreksi', 1);
        $this->output_json($data);
        $updated = $this->db->update('cbt_nilai');
        $data['success'] = $updated;
        $this->db->where('id_nilai', $siswa . '0' . $jadwal);
        $jadwal = $this->input->post('jadwal', true);
        $siswa = $this->input->post('siswa', true);
    }
    public function tandaisemua()
    {
        $this->output_json($data);
        $updated = 0;
        foreach ($siswas as $id_siswa => $memulai) {
            if (!(count($jawaban_es) > 0)) {
            }
            $bagi_jodoh = $info->tampil_jodohkan / 100;
            $bagi_pg2 = $info->tampil_kompleks / 100;
            $benar_jod = 0;
            $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
            foreach ($jawaban_pg as $jwb_pg) {
                $salah_pg += 1;
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban_benar ?? '')) {
                }
                $benar_pg += 1;
            }
            $info = $this->cbt->getJadwalById($id_jadwal);
            if (!$upd) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                if (!$jawab_pg2->jawaban_siswa) {
                }
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                }
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    array_push($arr_benar, true);
                    if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                    }
                }
                $arr_benar = [];
            }
            $otomatis_pg2 = 0;
            $bagi_isian = $info->tampil_isian / 100;
            $otomatis_es = 0;
            foreach ($jawabans as $jawaban_siswa) {
                $jawabans_siswa[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
                if (!$jawaban_siswa->jawaban_siswa) {
                }
                $arrjwbnSiswa = [];
                foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                    foreach ($jbs as $idxs => $jb) {
                        $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                        if (!($jb === '1')) {
                        }
                        if (!($idxs > 0)) {
                        }
                    }
                    if (!($idx > 0)) {
                    }
                    $arrjwbn[$idx] = [];
                }
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                if (!($jawaban_siswa->jenis_soal == '2')) {
                }
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                if ($jawaban_siswa->jawaban_siswa) {
                }
                $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
                $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar ?? ['']);
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
                }
                $arrjwbn = [];
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
                $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
                $jawaban_siswa->jawaban_siswa->links = json_decode(json_encode($arrjwbnSiswa));
                foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    }
                    $arrjwbnSiswa[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($jb === '1')) {
                        }
                        $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                        if (!($idxs > 0)) {
                        }
                    }
                }
                $arrAlphabet = range('A', 'Z');
                $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            }
            if (!(count($jawaban_pg2) > 0)) {
            }
            foreach ($jawaban_is as $num => $jawab_is) {
                $benar_is++;
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                if (!$benar) {
                }
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban_benar ?? '');
                $otomatis_is = $jawab_is->nilai_otomatis;
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $bobot_pg2 = $info->bobot_kompleks / 100;
            $jawaban_pg2 = isset($jawabans_siswa['2']) ? $jawabans_siswa['2'] : [];
            $benar_es = 0;
            if (!(count($jawaban_is) > 0)) {
            }
            $sameCounts = [];
            $benar_pg = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!($info->tampil_esai > 0)) {
            }
            $benar_is = 0;
            if (!($info->tampil_pg > 0)) {
            }
            $benar_pg2 = 0;
            $bobot_jodoh = $info->bobot_jodohkan / 100;
            $differentCounts = [];
            $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;
            $skor_koreksi_is = 0.0;
            $updated++;
            $bobot_isian = $info->bobot_isian / 100;
            $skor_koreksi_pg2 = 0.0;
            $skor_koreksi_jod = 0.0;
            $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;
            $bobot_essai = $info->bobot_esai / 100;
            $insert = ['id_nilai' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'pg_benar' => $benar_pg, 'pg_nilai' => round($skor_pg, 2), 'kompleks_nilai' => round($skor_pg2, 2), 'jodohkan_nilai' => round($skor_jod, 2), 'isian_nilai' => round($skor_is, 2), 'essai_nilai' => round($skor_es, 2), 'dikoreksi' => $memulai === '2' ? '0' : '1'];
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $benar_jod += 1 / $items * $item_benar;
                $items = 0;
                $item_kurang = 0;
                foreach ($array1 as $key => $subArray1) {
                    $differentCount += count($diffItems1) + count($diffItems2);
                    $differentCount += count($subArray1);
                    $arrBenar[$key]->benar = 0;
                    $arrBenar[$key]->benar += count($sameItems);
                    $item_kurang += count($diffItems1) + count($diffItems2);
                    $diffItems2 = array_diff($subArray2, $subArray1);
                    $item_kurang += count($subArray1);
                    $sameItems = array_intersect($subArray1, $subArray2);
                    $sameCount += count($sameItems);
                    $arrBenar[$key]->kurang += count($diffItems1);
                    $items += count($subArray1);
                    $arrBenar[$key] = new stdClass();
                    if (isset($array2[$key])) {
                    }
                    $diffItems1 = array_diff($subArray1, $subArray2);
                    $subArray2 = $array2[$key];
                    $arrBenar[$key]->kurang = 0;
                    $item_benar += count($sameItems);
                    $arrBenar[$key]->kurang += count($subArray1);
                    $arrBenar[$key]->salah = 0;
                }
                $otomatis_jod = $jawab_jod->nilai_otomatis;
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $point_soal = 1 / $items * $item_benar * $point_benar;
                $item_salah = 0;
                $sameCount = 0;
                $arrBenar = [];
                $this->sortArrays($array1);
                $this->sortArrays($array2);
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                $item_benar = 0;
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $differentCount = 0;
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                }
                $array1 = (array) $jawab_jod->jawaban_benar->links;
            }
            $ada_jawaban_essai = isset($jawabans_siswa['5']);
            $jawabans = $this->cbt->getJawabanByBank($info->id_bank, $id_siswa);
            $jawaban_pg = isset($jawabans_siswa['1']) ? $jawabans_siswa['1'] : [];
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $jawabans_siswa = [];
            $bagi_essai = $info->tampil_esai / 100;
            $bobot_pg = $info->bobot_pg / 100;
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa['5'] : [];
            $bagi_pg = $info->tampil_pg / 100;
            $skor_jod = $otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod;
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa['4'] : [];
            $otomatis_jod = 0;
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $skor_koreksi_es = 0.0;
            if (!(count($jawaban_pg) > 0)) {
            }
            $test_data[] = $insert;
            $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;
            $ada_jawaban_isian = isset($jawabans_siswa['4']);
            if (!($info->tampil_isian > 0)) {
            }
            $jawaban_jodoh = isset($jawabans_siswa['3']) ? $jawabans_siswa['3'] : [];
            $salah_pg = 0;
            $otomatis_is = 0;
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            foreach ($jawaban_es as $num => $jawab_es) {
                if (!$benar) {
                }
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban_benar ?? '');
                $otomatis_es = $jawab_es->nilai_otomatis;
                $benar_es++;
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
            }
            $upd = $this->db->replace('cbt_nilai', $insert);
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
        }
        $test_data = [];
        $id_jadwal = $this->input->post('id_jadwal', true);
        $data['siswa'] = $siswas;
        $siswas = $this->input->post('ids', true);
        $this->load->model('Cbt_model', 'cbt');
        $data['success'] = $updated;
    }
    public function inputEssai()
    {
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas_selected);
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $kelas_selected = $this->input->get('kelas');
        $data['tp_active'] = $tp;
        $this->load->view('cbt/nilai/nilai_essai');
        foreach ($siswas as $siswa) {
            $siswa->skor_jod = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->jodohkan_nilai : '0';
            $siswa->skor_essai = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->essai_nilai : '0';
            $siswa->skor_pg = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->pg_nilai : '0';
            $siswa->skor_isian = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->isian_nilai : '0';
            $siswa->skor_pg2 = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->kompleks_nilai : '0';
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->model('Dropdown_model', 'dropdown');
        $data['smt_active'] = $smt;
        $this->load->view('cbt/nilai/nilai_essai');
        $data['nama_kelas'] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kelas_selected);
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $data['guru'] = $guru;
        $jadwal_selected = $this->input->get('jadwal');
        $info = $this->cbt->getJadwalById($jadwal_selected);
        $ids = [];
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $this->load->view('members/guru/templates/footer');
        $data['kelas_selected'] = $kelas_selected;
        $data['siswas'] = $siswas;
        $data['jadwal_selected'] = $jadwal_selected;
        $nilai = $this->cbt->getNilaiAllSiswa([$jadwal_selected], $ids);
        $data = ['user' => $user, 'judul' => 'Input Nilai Manual', 'subjudul' => '', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        foreach ($siswas as $key => $val) {
            array_push($ids, $val->id_siswa);
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['jadwal'] = $info;
    }
    public function simpanKoreksiEssai()
    {
        $data['data'] = $nilais;
        $nilais = json_decode($this->input->post('nilai', true));
        $jadwal = $this->input->post('jadwal', true);
        $update = 0;
        $blm_selesai = [];
        $this->load->model('Cbt_model', 'cbt');
        $data['blm_selesai'] = count($blm_selesai);
        $this->output_json($data);
        $data['success'] = $update;
        foreach ($nilais as $nilai) {
            $update++;
            $nilai_siswa = $this->cbt->getNilaiSiswaByJadwal($jadwal, $nilai->id_siswa);
            array_push($blm_selesai, $nilai->id_siswa);
            $replace = ['id_nilai' => $nilai_siswa->id_nilai, 'id_siswa' => $nilai_siswa->id_siswa, 'id_jadwal' => $nilai_siswa->id_jadwal, 'pg_benar' => $nilai_siswa->pg_benar, 'pg_nilai' => $nilai_siswa->pg_nilai, 'kompleks_nilai' => isset($nilai->kompleks_nilai) && $nilai->kompleks_nilai != null ? $nilai->kompleks_nilai : '0', 'jodohkan_nilai' => isset($nilai->jodohkan_nilai) && $nilai->jodohkan_nilai != null ? $nilai->jodohkan_nilai : '0', 'isian_nilai' => isset($nilai->isian_nilai) && $nilai->isian_nilai != null ? $nilai->isian_nilai : '0', 'essai_nilai' => isset($nilai->essai_nilai) && $nilai->essai_nilai != null ? $nilai->essai_nilai : '0', 'dikoreksi' => '1'];
            if ($nilai_siswa != null) {
            }
            $up = $this->db->replace('cbt_nilai', $replace);
            if (!$up) {
            }
        }
    }
}
```

---

## File: application/controllers_decoded/Cbtnomorpeserta.php

```php
<?php

class Cbtnomorpeserta extends CI_Controller
{
    public function __construct()
    {
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Cbt_model', 'cbt');
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Dropdown_model', 'dropdown');
        parent::__construct();
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library('upload');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Master_model', 'master');
        $this->load->library(['datatables', 'form_validation']);
        redirect('auth');
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function index()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_footer');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Nomor Peserta', 'subjudul' => 'Generate Nomor Peserta Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['sesi'] = $this->dropdown->getAllSesi();
        $this->load->view('cbt/nomorpeserta/data');
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $smt = $this->dashboard->getSemesterActive();
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['tp_active'] = $tp;
        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
    }
    public function saveNomor()
    {
        $input = json_decode($this->input->post('siswa', true));
        $this->output_json($update);
        foreach ($input as $in) {
            $nomorAda = isset($arrNomor[$in->id]) ? $arrNomor[$in->id] : null;
            if ($nomorAda != null && $nomorAda->nomor_peserta == $in->nomor && $nomorAda->id_siswa != $in->id) {
            }
            $update = false;
            $insert = ['id_nomor' => $in->id . $tp->id_tp, 'id_siswa' => $in->id, 'id_tp' => $tp->id_tp, 'nomor_peserta' => $in->nomor];
            $update = $this->db->replace('cbt_nomor_peserta', $insert);
        }
        $arrNomor = $this->cbt->getAllNomorPeserta();
        $tp = $this->dashboard->getTahunActive();
        $update = false;
    }
    public function resetNomor()
    {
        $tp = $this->dashboard->getTahunActive();
        $this->output_json($res);
        $input = json_decode($this->input->get('kelas', true));
        $res['status'] = $update;
        $siswas = $this->cbt->getSiswaByKelasArray($tp->id_tp, $smt->id_smt, $input);
        $smt = $this->dashboard->getSemesterActive();
        foreach ($siswas as $siswa) {
            $insert = ['id_nomor' => $siswa->id_siswa . $tp->id_tp, 'id_siswa' => $siswa->id_siswa, 'id_tp' => $tp->id_tp, 'nomor_peserta' => ''];
            $update = $this->db->replace('cbt_nomor_peserta', $insert);
        }
    }
    public function getSiswaKelas($arr_kelas)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $arrNomor = $this->cbt->getAllNomorPeserta();
        $data['siswa'] = $siswas;
        $this->output_json($data);
        $data['nomor'] = $arrNomor;
        $kelas = json_decode(urldecode($arr_kelas));
        $siswas = $this->cbt->getSiswaByKelasArray($tp->id_tp, $smt->id_smt, $kelas);
    }
}
```

---

## File: application/controllers_decoded/Cbtpengawas.php

```php
<?php

class Cbtpengawas extends CI_Controller
{
    public function __construct()
    {
        redirect('auth');
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Master_model', 'master');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
        if ($this->ion_auth->is_admin()) {
        }
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $data['kelas'] = $kelass;
        $data['tp'] = $this->dashboard->getTahun();
        $setting = $this->dashboard->getSetting();
        $data['tgl_jadwals'] = $tglJadwals;
        $id_jenis = $this->cbt->getDistinctJenisJadwal($tp->id_tp, $smt->id_smt);
        foreach ($id_jenis as $jenis) {
            array_push($ids, $jenis->id_jenis);
        }
        $data['tp_active'] = $tp;
        $data['jenis'] = ['' => 'belum ada jadwal ujian'];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_header', $data);
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['ruang_sesi'] = $this->cbt->getRuangSesi($tp->id_tp, $smt->id_smt);
        $data['sesi'] = $this->dropdown->getAllSesi();
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []);
        if (!($jenis_selected != null)) {
        }
        $tglJadwals = $this->cbt->getAllJadwalByJenis($jenis_selected, $tp->id_tp, $smt->id_smt);
        if ($ids && count($ids) > 0) {
        }
        $data['smt_active'] = $smt;
        $data['pengawas'] = $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Atur Pengawas', 'subjudul' => 'Pengawas Ujian/Ulangan', 'setting' => $setting];
        $kelass = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        if (!($id_jenis && count($id_jenis) > 0)) {
        }
        $tglJadwals = [];
        $jenis_selected = $this->input->get('jenis', true);
        $smt = $this->dashboard->getSemesterActive();
        $ids = [];
        $data['gurus'] = $this->dropdown->getAllGuru();
        foreach ($tglJadwals as $tgl => $jadwalss) {
            foreach ($jadwalss as $mpl => $jadwals) {
                foreach ($jadwals as $jadwal) {
                    foreach ($jadwal->bank_kelas as $kb) {
                        $jadwal->peserta[] = $klss;
                        if (!($kb['kelas_id'] != '')) {
                        }
                        $klss = $this->cbt->getKelasUjian($kb['kelas_id']);
                    }
                    $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
                }
            }
        }
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('cbt/pengawas/data');
        $data['jenis'] = $this->cbt->getAllJenisUjianByArrJenis($ids);
        $tp = $this->dashboard->getTahunActive();
        $data['jenis_selected'] = $jenis_selected;
    }
    public function savePengawas()
    {
        $data['error'] = '--';
        $this->output_json($data);
        $data['status'] = $updated;
        $tp = $this->dashboard->getTahunActive();
        $id_smt = $smt->id_smt;
        $id_tp = $tp->id_tp;
        $updated = 0;
        foreach ($input as $d) {
            $update = $this->db->replace('cbt_pengawas', $dataInsert);
            $dataInsert = ['id_pengawas' => $id_pengawas, 'id_jadwal' => $jadwal, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_ruang' => $ruang, 'id_sesi' => $sesi, 'id_guru' => implode(',', $d->guru)];
            $ruang = $d->ruang;
            $id_pengawas = $id_tp . $id_smt . $jadwal . $ruang . $sesi;
            $sesi = $d->sesi;
            $jadwal = $d->jadwal;
            if (!$update) {
            }
            $updated++;
        }
        $input = json_decode($this->input->post('data', true));
        $smt = $this->dashboard->getSemesterActive();
    }
}
```

---

## File: application/controllers_decoded/Cbtrekap.php

```php
<?php

class Cbtrekap extends CI_Controller
{
    public function __construct()
    {
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        parent::__construct();
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library('upload');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            }
            sort($subArray);
        }
    }
    public function index()
    {
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['rekaps'] = $rekaps;
        $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $data['rekaps'] = $rekaps;
        $data['tp_active'] = $tp;
        $data['tp'] = $this->dashboard->getTahun();
        $data['kelases'] = $this->cbt->getKelas();
        $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt);
        $data['ruangs'] = $this->cbt->getAllRuang();
        $rekapNilai = $this->cbt->getRekapJadwal($guru->id_guru);
        $this->load->model('Master_model', 'master');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Rekap Hasil Penilaian', 'subjudul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/templates/header', $data);
        $this->load->model('Dropdown_model', 'dropdown');
        $smt = $this->master->getSemesterActive();
        $this->load->view('members/guru/templates/footer');
        $this->load->view('cbt/rekap/data');
        $rekaps = array_merge($rekapJadwal, $rekapNilai);
        $rekapJadwal = $data_jadwal;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $tp = $this->master->getTahunActive();
        $koreksi = $this->cbt->getTotalKoreksi();
        $rekapJadwal = $data_jadwal;
        $data['semester'] = $this->cbt->getDistinctSmt();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $data['ada_rekap'] = $this->cbt->getAllRekap();
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('cbt/rekap/data');
        $data['banks'] = $this->cbt->getAllBankSoal();
        if ($this->ion_auth->is_admin()) {
        }
        foreach ($data_jadwal as $rekap) {
            $rekap->dikoreksi = false;
            if (!$hanya_pg && isset($koreksi[$rekap->id_jadwal]) && isset($koreksi[$rekap->id_jadwal][0])) {
            }
            $hanya_pg = $rekap->tampil_pg > 0 && $rekap->tampil_kompleks == 0 && $rekap->tampil_jodohkan == 0 && $rekap->tampil_isian == 0 && $rekap->tampil_esai == 0;
            $rekap->dikoreksi = true;
            $rekap->mengerjakan = $terpakai;
            $terpakai = isset($jadwal_dikerjakan[$rekap->id_jadwal]) ? count($jadwal_dikerjakan[$rekap->id_jadwal]) : 0;
            $rekap->hanya_pg = $hanya_pg;
        }
        $data['tahuns'] = $this->cbt->getDistinctTahun();
        $data['smt_active'] = $smt;
        $rekaps = array_merge($rekapJadwal, $rekapNilai);
        $data['koreksi'] = $koreksi;
        $data['ada_rekap'] = $this->cbt->getAllRekap($guru->id_guru);
        $rekapNilai = $this->cbt->getRekapJadwal();
        $this->load->model('Cbt_model', 'cbt');
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['guru'] = $guru;
        foreach ($data_jadwal as $rekap) {
            $rekap->hanya_pg = $hanya_pg;
            $terpakai = isset($jadwal_dikerjakan[$rekap->id_jadwal]) ? count($jadwal_dikerjakan[$rekap->id_jadwal]) : 0;
            $rekap->dikoreksi = true;
            $rekap->mengerjakan = $terpakai;
            $rekap->dikoreksi = false;
            $hanya_pg = $rekap->tampil_pg > 0 && $rekap->tampil_kompleks == 0 && $rekap->tampil_jodohkan == 0 && $rekap->tampil_isian == 0 && $rekap->tampil_esai == 0;
            if (!$hanya_pg && isset($koreksi[$rekap->id_jadwal]) && isset($koreksi[$rekap->id_jadwal][0])) {
            }
        }
        $data['kelas'] = $this->cbt->getDistinctKelas();
    }
    public function perMapel()
    {
        $this->load->view('_templates/dashboard/_footer');
        $user = $this->ion_auth->user()->row();
        $this->load->model('Master_model', 'master');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->model('Cbt_model', 'cbt');
        $data['semester'] = $this->cbt->getDistinctSmt();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('members/guru/templates/footer');
        $data = ['user' => $user, 'judul' => 'Hasil Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('cbt/rekap/permapel');
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tp_active'] = $tp;
        $data['tp'] = $this->dashboard->getTahun();
        $smt = $this->master->getSemesterActive();
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        if ($this->ion_auth->is_admin()) {
        }
        $data['tahun'] = $this->cbt->getDistinctTahun();
        $this->load->view('cbt/rekap/permapel');
        $this->load->view('members/guru/templates/header', $data);
        $data['smt_active'] = $smt;
        $tp = $this->master->getTahunActive();
    }
    public function backupNilai($id_jadwal)
    {
        $save = $this->master->create('cbt_rekap_nilai', $nilai, true);
        $soal_essai = ['tampil' => $jadwal->tampil_esai, 'bobot' => $jadwal->bobot_esai, 'jawaban' => $esb];
        $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
        $durasies = $this->cbt->getIdSiswaFromDurasiByJadwal($id_jadwal);
        $terpakai = isset($jadwal_dikerjakan[$id_jadwal]) && count($jadwal_dikerjakan[$id_jadwal]) > 0;
        foreach ($siswas as $siswa) {
            $soal_jod = ['bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jods, 'nilai' => $skor_jod];
            $pg2s = [];
            foreach ($jawabans[$siswa->id_siswa] as $jawaban) {
                if ($jawaban->jenis_soal == '5') {
                }
                if ($jawaban->jenis_soal == '4') {
                }
                if ($jawaban->jenis_soal == '3') {
                }
                if ($jawaban->jenis_soal == '2') {
                }
                if ($jawaban->jenis_soal == '1') {
                }
                array_push($ess, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                array_push($iss, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                array_push($pgs, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                array_push($jods, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                array_push($pg2s, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
            }
            $skor_jod = 0;
            $pgs = [];
            if (!isset($nilais[$siswa->id_siswa])) {
            }
            $skor_pg = 0;
            $soal_es = ['bobot' => $jadwal->bobot_esai, 'jawaban' => $ess, 'nilai' => $skor_es];
            if (!isset($jawabans[$siswa->id_siswa])) {
            }
            $dikoreksi = [];
            $salah_pg = 0;
            $benar_pg = $nilais[$siswa->id_siswa]->pg_benar;
            $soal_pg2 = ['bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2s, 'nilai' => $skor_pg2];
            $skor_es = $nilais[$siswa->id_siswa]->essai_nilai;
            $soal_is = ['bobot' => $jadwal->bobot_isian, 'jawaban' => $iss, 'nilai' => $skor_is];
            $jods = [];
            array_push($dikoreksi, $nilais[$siswa->id_siswa]->dikoreksi);
            $ess = [];
            $skor_pg = $nilais[$siswa->id_siswa]->pg_nilai;
            $skor_is = 0;
            $nilai[] = ['id_jadwal' => $id_jadwal, 'id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'id_mapel' => $jadwal->id_mapel, 'id_siswa' => $siswa->id_siswa, 'nama_siswa' => $siswa->nama, 'no_peserta' => $siswa->nomor_peserta, 'id_kelas' => $siswa->id_kelas, 'kelas' => $siswa->nama_kelas, 'mulai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->mulai : '', 'selesai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->selesai : '', 'durasi' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->lama_ujian : '', 'bobot_pg' => $jadwal->bobot_pg, 'jawaban_pg' => serialize($pgs), 'nilai_pg' => round($skor_pg, 2), 'soal_kompleks' => serialize($soal_pg2), 'soal_jodohkan' => serialize($soal_jod), 'soal_isian' => serialize($soal_is), 'soal_essai' => serialize($soal_es), 'id_guru' => $jadwal->id_guru];
            $skor_jod = $nilais[$siswa->id_siswa]->jodohkan_nilai;
            $salah_pg = $jadwal->tampil_pg - $benar_pg;
            $skor_es = 0;
            $skor_is = $nilais[$siswa->id_siswa]->isian_nilai;
            $benar_pg = 0;
            $iss = [];
            $skor_pg2 = $nilais[$siswa->id_siswa]->kompleks_nilai;
            $skor_pg2 = 0;
        }
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $generated = $this->generateNilaiUjian($id_jadwal);
        foreach ($soals as $id => $soal) {
            if ($soal->jenis == '2') {
            }
            if ($soal->jenis == '3') {
            }
            if ($soal->jenis == '1') {
            }
            if ($soal->jenis == '5') {
            }
            if ($soal->jenis == '4') {
            }
            array_push($isb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
            array_push($jodb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
            array_push($pgb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
            array_push($pg2b, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
            array_push($esb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
        }
        $result = false;
        $save = isset($jadwal_dikerjakan[$id_jadwal]) ? count($jadwal_dikerjakan[$id_jadwal]) : 0;
        $this->db->update('cbt_jadwal');
        if ($generated && $result) {
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert"> Berhasil merekap nilai ' . $save . ' siswa</div>');
        $isb = [];
        $jawabans = $this->cbt->getIdSiswaFromJawabanByJadwal($id_jadwal);
        foreach ($siswas as $siswa) {
            array_push($arrSiswa, $siswa->id_siswa);
        }
        $esb = [];
        $pg2b = [];
        $soals = $this->cbt->getNomorSoalByBank($jadwal->id_bank);
        $soal_kompleks = ['tampil' => $jadwal->tampil_kompleks, 'bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2b];
        $jodb = [];
        $nilais = $this->cbt->getAllNilaiSiswa($id_jadwal);
        $kelass = unserialize($jadwal->bank_kelas ?? '');
        $soal_jodohkan = ['tampil' => $jadwal->tampil_jodohkan, 'bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jodb];
        $this->load->model('Master_model', 'master');
        $this->db->set('rekap', 1);
        $result = false;
        $this->output_json(true);
        $siswas = $this->cbt->getSiswaByKelasArray($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
        $this->load->model('Dropdown_model', 'dropdown');
        $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
        $this->db->delete('cbt_rekap');
        if ($terpakai && $generated) {
        }
        $arrSiswa = [];
        $result = $this->db->insert('cbt_rekap', $insert);
        foreach ($kelass as $kls) {
            array_push($arrkelas, $kls['kelas_id']);
            if (!($kls['kelas_id'] != null)) {
            }
        }
        if (!$result) {
        }
        $this->db->trans_complete();
        $tahun = $id_tp->tahun;
        $this->db->where('id_jadwal', $id_jadwal);
        $pgb = [];
        $this->db->delete('cbt_rekap_nilai');
        $soal_isian = ['tampil' => $jadwal->tampil_isian, 'bobot' => $jadwal->bobot_isian, 'jawaban' => $isb];
        $jadwal = $this->cbt->getJadwalById($id_jadwal);
        $insert = ['id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jadwal' => $id_jadwal, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'bank_kode' => $jadwal->bank_kode, 'bank_kelas' => $jadwal->bank_kelas, 'nama_kelas' => serialize($nama_kelas), 'bank_level' => $jadwal->bank_level, 'id_mapel' => $jadwal->id_mapel, 'nama_mapel' => $jadwal->nama_mapel, 'kode' => $jadwal->kode, 'tgl_mulai' => $jadwal->tgl_mulai, 'tgl_selesai' => $jadwal->tgl_selesai, 'tampil_pg' => $jadwal->tampil_pg, 'jawaban_pg' => serialize($pgb), 'bobot_pg' => $jadwal->bobot_pg, 'soal_kompleks' => serialize($soal_kompleks), 'soal_jodohkan' => serialize($soal_jodohkan), 'soal_isian' => serialize($soal_isian), 'soal_essai' => serialize($soal_essai), 'id_guru' => $jadwal->id_guru, 'nama_guru' => $jadwal->nama_guru];
        $nilai = [];
        $this->load->model('Cbt_model', 'cbt');
        $arrkelas = [];
        $this->db->where('id_jadwal', $id_jadwal);
        $this->load->model('Dashboard_model', 'dashboard');
        $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
        $this->db->trans_start();
        $smt = $id_smt->nama_smt;
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
    }
    public function bulkBackup()
    {
        $data['jadwal'] = $jadwals;
        $this->output_json(true);
        $this->db->trans_complete();
        $generated = 0;
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert"> Berhasil merekap <b>' . count($ids) . '</b> nilai </div>');
        $sukses = $generated > 0 && $result;
        $data['total'] = count($ids);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        sleep(1);
        if ($generated > 0 && $result) {
        }
        foreach ($jadwals as $jadwal) {
            $this->db->delete('cbt_rekap_nilai');
            $this->db->where('id_jadwal', $jadwal->id_jadwal);
            $arrkelas = [];
            $tahun = $id_tp->tahun;
            foreach ($kelass as $kls) {
                array_push($arrkelas, $kls['kelas_id']);
                if (!($kls['kelas_id'] != null)) {
                }
            }
            $save = $this->master->create('cbt_rekap_nilai', $nilai, true);
            foreach ($siswas as $siswa) {
                $skor_is = 0;
                $jods = [];
                $soal_jod = ['bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jods, 'nilai' => $skor_jod];
                $skor_jod = 0;
                if (!isset($jawabans[$siswa->id_siswa])) {
                }
                $ess = [];
                $nilai[] = ['id_jadwal' => $jadwal->id_jadwal, 'id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'id_mapel' => $jadwal->id_mapel, 'id_siswa' => $siswa->id_siswa, 'nama_siswa' => $siswa->nama, 'no_peserta' => $siswa->nomor_peserta, 'id_kelas' => $siswa->id_kelas, 'kelas' => $siswa->nama_kelas, 'mulai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->mulai : '', 'selesai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->selesai : '', 'durasi' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->lama_ujian : '', 'bobot_pg' => $jadwal->bobot_pg, 'jawaban_pg' => serialize($pgs), 'nilai_pg' => round($skor_pg, 2), 'soal_kompleks' => serialize($soal_pg2), 'soal_jodohkan' => serialize($soal_jod), 'soal_isian' => serialize($soal_is), 'soal_essai' => serialize($soal_es), 'id_guru' => $jadwal->id_guru];
                $pgs = [];
                array_push($dikoreksi, $nilais[$siswa->id_siswa]->dikoreksi);
                $skor_is = $nilais[$siswa->id_siswa]->isian_nilai;
                $soal_pg2 = ['bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2s, 'nilai' => $skor_pg2];
                $soal_is = ['bobot' => $jadwal->bobot_isian, 'jawaban' => $iss, 'nilai' => $skor_is];
                if (!isset($nilais[$siswa->id_siswa])) {
                }
                $skor_es = 0;
                $salah_pg = 0;
                $skor_pg = 0;
                $skor_pg = $nilais[$siswa->id_siswa]->pg_nilai;
                $soal_es = ['bobot' => $jadwal->bobot_esai, 'jawaban' => $ess, 'nilai' => $skor_es];
                $benar_pg = 0;
                $skor_pg2 = 0;
                $salah_pg = $jadwal->tampil_pg - $benar_pg;
                foreach ($jawabans[$siswa->id_siswa] as $jawaban) {
                    if ($jawaban->jenis_soal == '2') {
                    }
                    array_push($pgs, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                    array_push($jods, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                    array_push($iss, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                    array_push($pg2s, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                    if ($jawaban->jenis_soal == '3') {
                    }
                    if ($jawaban->jenis_soal == '5') {
                    }
                    if ($jawaban->jenis_soal == '1') {
                    }
                    array_push($ess, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                    if ($jawaban->jenis_soal == '4') {
                    }
                }
                $dikoreksi = [];
                $benar_pg = $nilais[$siswa->id_siswa]->pg_benar;
                $skor_pg2 = $nilais[$siswa->id_siswa]->kompleks_nilai;
                $skor_jod = $nilais[$siswa->id_siswa]->jodohkan_nilai;
                $iss = [];
                $pg2s = [];
                $skor_es = $nilais[$siswa->id_siswa]->essai_nilai;
            }
            foreach ($soals as $id => $soal) {
                array_push($pg2b, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                array_push($esb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                array_push($pgb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                if ($soal->jenis == '2') {
                }
                array_push($jodb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                if ($soal->jenis == '4') {
                }
                array_push($isb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                if ($soal->jenis == '3') {
                }
                if ($soal->jenis == '5') {
                }
                if ($soal->jenis == '1') {
                }
            }
            if (!$gen) {
            }
            $this->db->set('rekap', 1);
            $smt = $id_smt->nama_smt;
            $siswas = $this->cbt->getSiswaByKelasArray($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $soal_essai = ['tampil' => $jadwal->tampil_esai, 'bobot' => $jadwal->bobot_esai, 'jawaban' => $esb];
            $gen = $this->generateNilaiUjian($jadwal->id_jadwal);
            $esb = [];
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0;
            $soal_jodohkan = ['tampil' => $jadwal->tampil_jodohkan, 'bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jodb];
            $pg2b = [];
            $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $result = $this->db->insert('cbt_rekap', $insert);
            $kelass = unserialize($jadwal->bank_kelas ?? '');
            foreach ($siswas as $siswa) {
                array_push($arrSiswa, $siswa->id_siswa);
            }
            $this->db->where('id_jadwal', $jadwal->id_jadwal);
            $generated++;
            $soals = $this->cbt->getNomorSoalByBank($jadwal->id_bank);
            if (!$result) {
            }
            if (!$terpakai) {
            }
            $pgb = [];
            $arrSiswa = [];
            $jodb = [];
            $soal_isian = ['tampil' => $jadwal->tampil_isian, 'bobot' => $jadwal->bobot_isian, 'jawaban' => $isb];
            $isb = [];
            $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
            $this->db->delete('cbt_rekap');
            $durasies = $this->cbt->getIdSiswaFromDurasiByJadwal($jadwal->id_jadwal);
            $jawabans = $this->cbt->getIdSiswaFromJawabanByJadwal($jadwal->id_jadwal);
            $this->db->where('id_jadwal', $jadwal->id_jadwal);
            $this->db->update('cbt_jadwal');
            $nilai = [];
            $soal_kompleks = ['tampil' => $jadwal->tampil_kompleks, 'bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2b];
            $nilais = $this->cbt->getAllNilaiSiswa($jadwal->id_jadwal);
            $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
            $insert = ['id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jadwal' => $jadwal->id_jadwal, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'bank_kode' => $jadwal->bank_kode, 'bank_kelas' => $jadwal->bank_kelas, 'nama_kelas' => serialize($nama_kelas), 'bank_level' => $jadwal->bank_level, 'id_mapel' => $jadwal->id_mapel, 'nama_mapel' => $jadwal->nama_mapel, 'kode' => $jadwal->kode, 'tgl_mulai' => $jadwal->tgl_mulai, 'tgl_selesai' => $jadwal->tgl_selesai, 'tampil_pg' => $jadwal->tampil_pg, 'jawaban_pg' => serialize($pgb), 'bobot_pg' => $jadwal->bobot_pg, 'soal_kompleks' => serialize($soal_kompleks), 'soal_jodohkan' => serialize($soal_jodohkan), 'soal_isian' => serialize($soal_isian), 'soal_essai' => serialize($soal_essai), 'id_guru' => $jadwal->id_guru, 'nama_guru' => $jadwal->nama_guru];
        }
        $this->load->model('Dropdown_model', 'dropdown');
        $jadwals = $this->cbt->getJadwalByArrId($ids);
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
        $ids = json_decode($this->input->post('ids', true));
        $this->load->model('Cbt_model', 'cbt');
        $this->db->trans_start();
        $result = false;
        $this->load->model('Dashboard_model', 'dashboard');
        $save = false;
        $this->load->model('Master_model', 'master');
    }
    public function hapusRekap()
    {
        $this->output_json($data);
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert"> Berhasil menghapus <b>' . count($ids) . '</b> nilai </div>');
        $ids = json_decode($this->input->post('ids', true));
        $delRekap = $this->db->delete('cbt_rekap');
        sleep(1);
        $data['total'] = count($ids);
        if ($delNilai && $delRekap) {
        }
        $this->db->where_in('id_jadwal', $ids);
        $delNilai = $this->db->delete('cbt_rekap_nilai');
        $data['success'] = $delNilai && $delRekap;
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert"> Hapus nilai gagal </div>');
        $this->db->where_in('id_jadwal', $ids);
    }
    function getDataFromArray1ByUserId($array, $userId)
    {
        foreach ($array as $key => $data) {
            return $array;
            if (!($data->id_siswa == $userId)) {
            }
        }
        return array();
    }
    public function getJenisPenilaian()
    {
        $this->load->model('Cbt_model', 'cbt');
        $jadwals = $this->cbt->getJenisRekap($tahun, $smt);
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
    }
    public function getNilaiKelas()
    {
        foreach ($jadwals as $key => $jadwal) {
            $jadwal->jawaban_pg = unserialize($jadwal->jawaban_pg);
            $ids = [];
            $jadwal->jawaban_esai = unserialize($jadwal->jawaban_esai);
            unset($jadwals[$key]);
            if (in_array($kelas, $ids)) {
            }
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
            foreach ($jadwal->bank_kelas as $id) {
                array_push($ids, $id['kelas_id']);
            }
        }
        $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel, $guru->id_guru);
        usort($arrSiswa, function ($a, $b) {
            return $a['nama'] <=> $b['nama'];
        });
        $mapel = $this->input->get('mapel');
        if (!(count($rekaps) > 0)) {
        }
        $smtg = $this->dashboard->getSemesterByNama($smt);
        foreach ($jadwals as $key => $jadwal) {
            $jadwal->jawaban_esai = unserialize($jadwal->jawaban_esai ?? '');
            if (in_array($kelas, $ids)) {
            }
            $ids = [];
            foreach ($jadwal->bank_kelas as $id) {
                array_push($ids, $id['kelas_id']);
            }
            unset($jadwals[$key]);
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
            $jadwal->jawaban_pg = unserialize($jadwal->jawaban_pg ?? '');
        }
        $data['siswa'] = $arrSiswa;
        foreach ($rekaps as $key => $item) {
            $arrNilai[$item->id_siswa][$item->id_jadwal] = $item;
        }
        $this->output_json($data);
        $level = $this->master->getKelasById($kelas);
        foreach ($rekaps as $rekap) {
            $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
            $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
            $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
            $arrSiswa[$rekap->id_siswa] = ['id_siswa' => $rekap->id_siswa, 'nomor_peserta' => $rekap->nomor_peserta, 'nama' => $rekap->nama];
            $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
            $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
        }
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $kelas = $this->input->get('kelas');
        $jenis = $this->input->get('jenis');
        $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel);
        $arrSiswa = [];
        usort($jadwals, function ($a, $b) {
            return $a->id_jadwal <=> $b->id_jadwal;
        });
        $data['nilai'] = $arrNilai;
        $this->load->model('Master_model', 'master');
        $tahun = $this->input->get('tahun');
        $tpg = $this->dashboard->getTahunByTahun($tahun);
        $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel, $guru->id_guru);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tpg->id_tp, $smtg->id_smt);
        $arrNilai = [];
        if ($this->ion_auth->is_admin()) {
        }
        $user = $this->ion_auth->user()->row();
        $smt = $this->input->get('smt');
        $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel);
        $data['info'] = array_values($jadwals);
    }
    public function olahNilai()
    {
        $this->load->model('Master_model', 'master');
        $kls = @unserialize($rekap->nama_kelas);
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($siswas as $siswa) {
            $siswa->jawaban_pg = $this->unserialize_with_key($siswa->jawaban_pg);
            $siswa->soal_jodohkan = json_decode(json_encode(unserialize($siswa->soal_jodohkan)));
            $siswa->soal_isian = json_decode(json_encode(unserialize($siswa->soal_isian)));
            $siswa->soal_essai = json_decode(json_encode(unserialize($siswa->soal_essai)));
            $siswa->soal_kompleks = json_decode(json_encode(unserialize($siswa->soal_kompleks)));
        }
        $xa = $this->input->get('xa');
        $yb = $this->input->get('yb');
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $data['nama_kelas'] = $kelas == null ? 'Silahkan pilih kelas' : $kls[$kelas];
        $this->load->view('_templates/dashboard/_footer');
        $jadwal = $this->input->get('jadwal');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('cbt/rekap/olah');
        $data['kelas'] = $kls;
        $data['rekap'] = $rekap;
        $ya = $this->input->get('ya');
        $this->load->model('Cbt_model', 'cbt');
        $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
        $data['siswas'] = $siswas;
        $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
        $siswas = $this->cbt->getAllNilaiRekapByJenis($rekap->tp, $rekap->smt, $rekap->kode_jenis, $kelas, '0', $jadwal, $guru->id_guru);
        $xa = $this->input->get('xa');
        $convert = ['ya' => $ya, 'yb' => $yb, 'xa' => $xa, 'xb' => $xb];
        if (!($kelas != null)) {
        }
        $data['convert'] = $convert;
        $xb = $this->input->get('xb');
        $this->load->view('members/guru/templates/footer');
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $user = $this->ion_auth->user()->row();
        $kelas = $this->input->get('kelas');
        $data['jadwal'] = $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        if (!($rekap != null)) {
        }
        foreach ($siswas as $siswa) {
            $siswa->soal_essai = json_decode(json_encode(unserialize($siswa->soal_essai)));
            $siswa->jawaban_pg = $this->unserialize_with_key($siswa->jawaban_pg);
            $siswa->soal_jodohkan = json_decode(json_encode(unserialize($siswa->soal_jodohkan)));
            $siswa->soal_kompleks = json_decode(json_encode(unserialize($siswa->soal_kompleks)));
            $siswa->soal_isian = json_decode(json_encode(unserialize($siswa->soal_isian)));
        }
        $data['smt_active'] = $smt;
        $data['mapel'] = $rekap->id_mapel;
        $siswas = $this->cbt->getAllNilaiRekapByJenis($rekap->tp, $rekap->smt, $rekap->kode_jenis, $kelas, '0', $jadwal);
        $this->load->model('Dropdown_model', 'dropdown');
        $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
        $xb = $this->input->get('xb');
        $data['guru'] = $guru;
        $level = $this->master->getKelasById($kelas);
        $ya = $this->input->get('ya');
        $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('members/guru/templates/header', $data);
        if (!($kelas != null)) {
        }
        $data['tp_active'] = $tp;
        if (!($ya != null)) {
        }
        $data['kelas_selected'] = $kelas;
        $convert = ['ya' => $ya, 'yb' => $yb, 'xa' => $xa, 'xb' => $xb];
        $this->load->view('cbt/rekap/olah');
        $yb = $this->input->get('yb');
        if ($this->ion_auth->is_admin()) {
        }
        $data = ['user' => $user, 'judul' => 'Ekspor Hasil Siswa', 'subjudul' => 'Ekspor Hasil Siswa', 'setting' => $this->dashboard->getSetting()];
        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        if (!($ya != null)) {
        }
        $rekap = $this->cbt->getRekapByJadwalKelas($jadwal);
        $data['convert'] = $convert;
        $data['siswas'] = $siswas;
        $data['jadwal_selected'] = $jadwal;
    }
    function unserialize_with_key($serialized)
    {
        foreach ($arr as $value) {
            $result[$value['no_soal']] = $value['jawab'];
        }
        return $result;
        $arr = unserialize($serialized);
        $result = [];
    }
    public function export()
    {
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $data = ['user' => $user, 'judul' => 'Ekspor Hasil Penilaian', 'subjudul' => 'Ekspor Nilai', 'setting' => $this->dashboard->getSetting()];
        foreach ($jadwals as $jadwal) {
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
            $jadwal->nama_kelas = unserialize($jadwal->nama_kelas);
        }
        $data['tahuns'] = $this->cbt->getDistinctTahun();
        $this->load->view('_templates/dashboard/_footer');
        foreach ($jadwals as $key => $jadwal) {
            $jadwal->nama_kelas = unserialize($jadwal->nama_kelas ?? '');
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
        }
        $data['rekaps'] = $jadwals;
        $this->load->view('members/guru/templates/footer');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('cbt/rekap/ekspor');
        $data['smt_active'] = $smt;
        $data['tp'] = $this->dashboard->getTahun();
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Master_model', 'master');
        $user = $this->ion_auth->user()->row();
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->master->getTahunActive();
        $jadwals = $this->cbt->getAllRekap($guru->id_guru);
        $this->load->view('_templates/dashboard/_header', $data);
        $data['rekaps'] = $jadwals;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $jadwals = $this->cbt->getAllRekap();
        $this->load->model('Dashboard_model', 'dashboard');
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $this->load->view('cbt/rekap/ekspor');
        $data['semester'] = $this->cbt->getDistinctSmt();
        $smt = $this->master->getSemesterActive();
    }
    public function generateNilaiUjian($jadwal)
    {
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $bagi_essai = $info->tampil_esai / 100;
        $insets = [];
        $kelases = [];
        $bagi_pg = $info->tampil_pg / 100;
        foreach ($siswas as $siswa) {
            $insert['pg_benar'] = $benar_pg;
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $benar_jod += 1 / $items * $item_benar;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                }
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $this->sortArrays($array1);
                foreach ($array1 as $key => $subArray1) {
                    $items += count($subArray1);
                    if (isset($array2[$key])) {
                    }
                    $arrBenar[$key]->salah = 0;
                    $diffItems2 = array_diff($subArray2, $subArray1);
                    $arrBenar[$key] = new stdClass();
                    $subArray2 = $array2[$key];
                    $sameItems = array_intersect($subArray1, $subArray2);
                    $arrBenar[$key]->kurang = 0;
                    $arrBenar[$key]->kurang += count($diffItems1);
                    $diffItems1 = array_diff($subArray1, $subArray2);
                    $arrBenar[$key]->benar = 0;
                    $arrBenar[$key]->benar += count($sameItems);
                    $arrBenar[$key]->kurang += count($subArray1);
                    $item_benar += count($sameItems);
                }
                $sameCount = 0;
                $items = 0;
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                $differentCount = 0;
                $item_benar = 0;
                $point_soal = 1 / $items * $item_benar * $point_benar;
                $array1 = (array) $jawab_jod->jawaban_benar->links;
                $this->sortArrays($array2);
                $item_salah = 0;
                $item_kurang = 0;
                $arrBenar = [];
            }
            $input_is = 0;
            $benar_jod = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
            }
            $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            $insert['pg_nilai'] = round($skor_pg, 2);
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            $insert['isian_nilai'] = round($skor_is, 2);
            if (!(count($jawaban_pg2) > 0)) {
            }
            $skor_koreksi_jod = 0.0;
            if (!($info->tampil_pg > 0)) {
            }
            if (!(count($jawaban_pg) > 0)) {
            }
            $insert['jodohkan_nilai'] = round($skor_jod, 2);
            $otomatis_es = 0;
            $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            $salah_pg = 0;
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
            $insert['essai_nilai'] = round($skor_es, 2);
            $otomatis_is = 0;
            if (!($info->tampil_isian > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    array_push($arr_benar, true);
                    if (!in_array($js, $jawab_pg2->jawaban)) {
                    }
                }
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $arr_benar = [];
                $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
            }
            $input_es = 0;
            $insert['id_siswa'] = $siswa->id_siswa;
            foreach ($jawaban_is as $num => $jawab_is) {
                $otomatis_is = $jawab_is->nilai_otomatis;
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $benar_is++;
                if (!$benar) {
                }
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
            }
            $benar_is = 0;
            $benar_pg = 0;
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $benar_pg2 = 0;
            foreach ($jawaban_es as $num => $jawab_es) {
                $benar_es++;
                $otomatis_es = $jawab_es->nilai_otomatis;
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
                if (!$benar) {
                }
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
            }
            $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
            $input_pg2 = 0;
            $input_is = $nilai_input->isian_nilai;
            $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
            $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
            }
            $benar_es = 0;
            $otomatis_jod = 0;
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $input_jod = 0;
            if (!($info->tampil_esai > 0)) {
            }
            if (!(count($jawaban_es) > 0)) {
            }
            $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
            $input_es = $nilai_input->essai_nilai;
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            if (!($nilai_input != null && $nilai_input->dikoreksi == '1')) {
            }
            $insert['kompleks_nilai'] = round($skor_pg2, 2);
            $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
            foreach ($jawaban_pg as $jwb_pg) {
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                $salah_pg += 1;
                $benar_pg += 1;
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                }
            }
            if (!(count($jawaban_is) > 0)) {
            }
            array_push($insets, $insert);
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $skor_koreksi_is = 0.0;
            $insert['id_nilai'] = $siswa->id_siswa . '0' . $jadwal;
            $input_jod = $nilai_input->jodohkan_nilai;
            $input_pg2 = $nilai_input->kompleks_nilai;
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $insert['id_jadwal'] = $jadwal;
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            $skor_koreksi_es = 0.0;
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
        }
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank);
        if (count($insets) > 0) {
        }
        $bobot_essai = $info->bobot_esai / 100;
        $soal = [];
        $this->load->model('Cbt_model', 'cbt');
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $kelas_bank = unserialize($info->bank_kelas ?? '');
        foreach ($kelas_bank as $key => $value) {
            array_push($kelases, $value['kelas_id']);
        }
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $info = $this->cbt->getJadwalById($jadwal);
        $update = false;
        $bobot_pg = $info->bobot_pg / 100;
        $siswas = $this->cbt->getSiswaByKelas($info->id_tp, $info->id_smt, $kelases);
        $jawabans_siswa = [];
        $bagi_isian = $info->tampil_isian / 100;
        $this->db->update_batch('cbt_nilai', $insets, 'id_nilai');
        foreach ($jawabans as $jawaban_siswa) {
            if (!($jawaban_siswa->jenis_soal == '3')) {
            }
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_siswa->links = json_decode(json_encode($arrjwbnSiswa));
            $arrAlphabet = range('A', 'Z');
            $arrjwbnSiswa = [];
            $jawaban_siswa->jawaban = array_map('strtoupper', $jawaban_siswa->jawaban ?? ['']);
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                $arrjwbn[$idx] = [];
                if (!($idx > 0)) {
                }
                foreach ($jbs as $idxs => $jb) {
                    if (!($jb === '1')) {
                    }
                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                    if (!($idxs > 0)) {
                    }
                }
            }
            if (!($jawaban_siswa->jenis_soal == '2')) {
            }
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban ?? [''], 'strlen');
            $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $arrjwbn = [];
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            if (!($jawaban_siswa->jawaban_siswa != null)) {
            }
            $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar ?? [''], 'strlen');
            $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
            $jawaban_siswa->jawaban_benar = array_map('strtoupper', $jawaban_siswa->jawaban_benar ?? ['']);
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                foreach ($jbs as $idxs => $jb) {
                    if (!($jb === '1')) {
                    }
                    if (!($idxs > 0)) {
                    }
                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                }
                if (!($idx > 0)) {
                }
                $arrjwbnSiswa[$idx] = [];
            }
        }
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        return $update;
        $bobot_isian = $info->bobot_isian / 100;
        $update = true;
    }
}
```

---

## File: application/controllers_decoded/Cbtruang.php

```php
<?php

class Cbtruang extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Cbt_model', 'cbt');
        parent::__construct();
        redirect('auth');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Ruang Ujian', 'subjudul' => 'Data Ruang Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('cbt/ruang/data');
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->output_json($this->cbt->getRuang(), false);
    }
    public function add()
    {
        $this->output_json($data);
        $this->master->create('cbt_ruang', $insert, false);
        $data['status'] = $insert;
        $insert = ['nama_ruang' => $this->input->post('nama_ruang', true), 'kode_ruang' => $this->input->post('kode_ruang', true)];
    }
    public function update()
    {
        $data = $this->cbt->updateRuang();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        if (!$this->master->delete('cbt_ruang', $chk, 'id_ruang')) {
        }
        if (!$chk) {
        }
        $this->output_json(['status' => false]);
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $chk = $this->input->post('checked', true);
    }
}
```

---

## File: application/controllers_decoded/Cbtsesi.php

```php
<?php

class Cbtsesi extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Master_model', 'master');
        if (!$this->ion_auth->logged_in()) {
        }
        parent::__construct();
        redirect('auth');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->library(['datatables', 'form_validation']);
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Cbt_model', 'cbt');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data = ['user' => $user, 'judul' => 'Sesi Ujian', 'subjudul' => 'Data Sesi Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $user = $this->ion_auth->user()->row();
        $this->load->view('cbt/sesi/data');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
    }
    public function data()
    {
        $this->output_json($this->cbt->getSesi(), false);
    }
    public function add()
    {
        $insert = ['nama_sesi' => $this->input->post('nama_sesi', true), 'kode_sesi' => $this->input->post('kode_sesi', true), 'waktu_mulai' => $this->input->post('waktu_mulai', true), 'waktu_akhir' => $this->input->post('waktu_akhir', true)];
        $this->master->create('cbt_sesi', $insert, false);
        $data['status'] = $insert;
        $this->output_json($data);
    }
    public function update()
    {
        $data = $this->cbt->updateSesi();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function edit($id)
    {
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp_active'] = $tp;
        $smt = $this->dashboard->getSemesterActive();
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_footer');
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Sesi Siswa', 'subjudul' => 'Atur Sesi Siswa', 'sesi' => $this->cbt->getSesiById($id)];
        $this->load->view('cbt/sesi/edit');
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $this->output_json(['status' => false]);
        if (!$this->master->delete('cbt_sesi', $chk, 'id_sesi')) {
        }
    }
    public function sesisiswa()
    {
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('cbt/sesisiswa/data');
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Sesi Ujian', 'subjudul' => 'Data Sesi Ujian'];
    }
}
```

---

## File: application/controllers_decoded/Cbtsesisiswa.php

```php
<?php

class Cbtsesisiswa extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Cbt_model', 'cbt');
        redirect('auth');
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Dashboard_model', 'dashboard');
        parent::__construct();
        $this->load->model('Master_model', 'master');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'judul' => 'Atur Ruang dan Sesi Siswa', 'subjudul' => 'Ruang dan Sesi Siswa', 'setting' => $this->dashboard->getSetting(), 'kelas' => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt), 'ruang_kelas' => $this->cbt->getKelasList($tp->id_tp, $smt->id_smt), 'sesi' => $this->dropdown->getAllSesi(), 'ruang' => $this->cbt->getAllRuang(), 'tp' => $this->dashboard->getTahun(), 'tp_active' => $tp, 'smt' => $this->dashboard->getSemester(), 'smt_active' => $smt, 'profile' => $this->dashboard->getProfileAdmin($user->id)];
        $data['siswas'] = $siswas;
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('cbt/sesisiswa/data');
        $siswas = $this->cbt->getRuangSesiSiswa($kls, $tp->id_tp, $smt->id_smt);
        $siswas = [];
        $kls = $this->input->get('kls', true);
        $kelas_selected = $kls != null ? $kls : '0';
        $data['kelas_selected'] = $kelas_selected;
        $user = $this->ion_auth->user()->row();
        if (!($kelas_selected != '0')) {
        }
    }
    public function getAllRuang()
    {
        $this->output_json($this->cbt->getAllRuang());
    }
    public function getAllSesi()
    {
        $this->output_json($this->dropdown->getAllSesi());
    }
    public function add()
    {
        $this->output_json($data);
        $this->master->create('cbt_sesi', $insert, false);
        $data['status'] = $insert;
        $insert = ['nama_sesi' => $this->input->post('nama_sesi', true), 'kode_sesi' => $this->input->post('kode_sesi', true), 'waktu_mulai' => $this->input->post('waktu_mulai', true), 'waktu_akhir' => $this->input->post('waktu_akhir', true)];
    }
    public function update()
    {
        $data = $this->cbt->updateSesi();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        if (!$this->master->delete('cbt_sesi', $chk, 'id_sesi')) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $chk = $this->input->post('checked', true);
        $this->output_json(['status' => false]);
        if (!$chk) {
        }
    }
    public function editsesisiswa()
    {
        $smt = $this->dashboard->getSemesterActive();
        $data['status'] = $update;
        $update = false;
        foreach ($rs as $id => $klss) {
            foreach ($klss as $idkls => $kls) {
                $data = ['siswa_id' => $id, 'kelas_id' => $idkls, 'ruang_id' => $kls['ruang'], 'sesi_id' => $kls['sesi'], 'tp_id' => $tp->id_tp, 'smt_id' => $smt->id_smt];
                $update = $this->db->replace('cbt_sesi_siswa', $data);
            }
        }
        $this->output_json($data);
        $rs = $this->input->post('ruang-sesi', true);
        $tp = $this->dashboard->getTahunActive();
    }
    public function editsesikelas()
    {
        $data['status'] = $update;
        $smt = $this->dashboard->getSemesterActive();
        $tp = $this->dashboard->getTahunActive();
        $input = json_decode($this->input->post('kelas_sesi', true));
        $this->output_json($data);
        foreach ($input as $d) {
            foreach ($siswas as $siswa) {
                $data = ['siswa_id' => $siswa->id_siswa, 'kelas_id' => $siswa->id_kelas, 'ruang_id' => $d->ruang_id, 'sesi_id' => $d->sesi_id, 'tp_id' => $tp->id_tp, 'smt_id' => $smt->id_smt];
                $this->db->replace('cbt_sesi_siswa', $data);
            }
            $data = ['id_kelas_ruang' => $d->kelas_id . $tp->id_tp . $smt->id_smt, 'id_kelas' => $d->kelas_id, 'id_ruang' => $d->ruang_id, 'id_sesi' => $d->sesi_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'set_siswa' => $d->set_siswa];
            $siswas = $this->kelas->getKelasSiswa($d->kelas_id, $tp->id_tp, $smt->id_smt);
            $update = $this->db->replace('cbt_kelas_ruang', $data);
        }
    }
}
```

---

## File: application/controllers_decoded/Cbtstatus.php

```php
<?php

class Cbtstatus extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        redirect('auth');
        $this->load->model('Dashboard_model', 'dashboard');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->model('Master_model', 'master');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->library('upload');
        parent::__construct();
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
        $this->load->view('members/guru/templates/footer');
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls);
        $data['sesi'] = $this->dropdown->getAllSesi();
        $this->load->view('cbt/status/data');
        $data['ruang'] = $this->dropdown->getAllRuang();
        $jadwals = $this->cbt->getJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        foreach ($jadwals as $jad) {
            $kls = unserialize($jad->bank_kelas ?? '');
            foreach ($kls as $kl) {
                array_push($arrKls, $kl['kelas_id']);
            }
        }
        $data['jadwal'] = $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $smt = $this->dashboard->getSemesterActive();
        if ($this->ion_auth->is_admin()) {
        }
        foreach ($jadwals as $jad) {
            $kls = unserialize($jad->bank_kelas ?? '');
            foreach ($kls as $kl) {
                array_push($arrKls, $kl['kelas_id']);
            }
        }
        $data = ['user' => $user, 'judul' => 'Status Ujian Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $jadwals = $this->cbt->getJadwalKelas($tp->id_tp, $smt->id_smt);
        $arrKls = [];
        $data['sesi'] = $this->dropdown->getAllSesi();
        $data['smt_active'] = $smt;
        $data['tp_active'] = $tp;
        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_footer');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $arrKls = [];
        $this->load->view('members/guru/cbt/status/data');
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_header', $data);
        $data['pengawas'] = $this->cbt->getPengawasByGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls);
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function status_ruang()
    {
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $data['guru'] = $guru;
        $tp = $this->dashboard->getTahunActive();
        $data['info'] = $info;
        $data['durasi_siswa'] = $arrDur;
        if (!($pengawas && count($pengawas) > 0)) {
        }
        $data['siswa'] = $siswas;
        $guru_ngawas = [];
        $guru_ngawas = $this->master->getGuruByArrId($ids_pengawas);
        $this->db->trans_complete();
        $this->load->view('members/guru/templates/header', $data);
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->db->trans_start();
        foreach ($siswas as $siswa) {
            foreach ($durasies as $durasi) {
                $durasi->lama_ujian = round($mins, 2) . ' m';
                $mins = (strtotime($durasi->selesai) - strtotime($durasi->mulai)) / 60;
                if ($durasi->lama_ujian == null) {
                }
                $dur_siswa = $durasi;
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
                if (strpos($lamanya, ':') !== false) {
                }
                $ed = $elap[2] == '00' ? 0 : 1;
                $em = $elap[1] == '00' ? '' : intval($elap[1]) + $ed . ' m';
                $lamanya = $durasi->lama_ujian;
                $durasi->lama_ujian .= 'm';
                $ej = $elap[0] == '00' ? '' : intval($elap[0]) . ' j ';
                $dd = $ej . $em;
                $elap = explode(':', $lamanya ?? '');
                $durasi->lama_ujian = $dd == '' ? '0 m' : $dd;
            }
            foreach ($logs as $log) {
                array_push($log_siswa, $log);
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
            }
            $dur_siswa = null;
            $log_siswa = [];
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $data['smt'] = $this->dashboard->getSemester();
        $data['pengawas'] = $guru_ngawas;
        $ruang = $this->input->get('ruang');
        $arrDur = [];
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $data['ids_pengawas'] = $ids_pengawas;
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $ids_pengawas = [];
        $data = ['user' => $user, 'judul' => 'Status Ujian Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('members/guru/cbt/status/status');
        $this->load->view('members/guru/templates/footer');
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi, $ruang);
        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $sesi, $info->bank_level);
        $user = $this->ion_auth->user()->row();
        $jadwal = $this->input->get('jadwal');
        $info = $this->cbt->getJadwalById($jadwal);
        $data['tp_active'] = $tp;
        $sesi = $this->input->get('sesi');
        if (!($ids_pengawas && count($ids_pengawas) > 0)) {
        }
    }
    public function getJadwalUjianByJadwal()
    {
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $kelas = unserialize($info->bank_kelas ?? '');
        foreach ($kelas as $key => $value) {
            $kelases[$value['kelas_id']] = $this->dropdown->getNamaKelasById($info->id_tp, $info->id_smt, $value['kelas_id']);
        }
        $tp = $this->dashboard->getTahunActive();
        $data['smt_active'] = $smt;
        $smt = $this->dashboard->getSemesterActive();
        $kelases = [];
        $this->output_json($kelases);
        $info = $this->cbt->getJadwalById($jadwal);
        $jadwal = $this->input->get('id_jadwal');
    }
    public function getJadwalUjianByKelas()
    {
        $id_guru = $guru->id_guru;
        $jadwals = $this->cbt->getAllJadwal($tp->id_tp, $smt->id_smt, $id_guru);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->output_json($jdwl);
        $kelas = $this->input->get('id_kelas');
        foreach ($jadwals as $jadwal) {
            foreach ($kls as $kl) {
                if (!($kl['kelas_id'] == $kelas)) {
                }
                $jdwl[$jadwal->id_jadwal] = $jadwal->bank_kode;
            }
            $kls = unserialize($jadwal->bank_kelas ?? '');
        }
        $jdwl = [];
        if ($this->ion_auth->in_group('guru')) {
        }
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $id_guru = null;
        $tp = $this->dashboard->getTahunActive();
    }
    public function getSiswaKelas()
    {
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $tp = $this->dashboard->getTahunActive();
        $data['pengawas'] = $this->master->getGuruByArrId($ids_pengawas);
        $ids_pengawas = [];
        $jadwal = $this->input->get('jadwal');
        foreach ($siswas as $siswa) {
            $log_siswa = [];
            foreach ($durasies as $durasi) {
                $mins = (strtotime($durasi->selesai) - strtotime($durasi->mulai)) / 60;
                $ej = $elap[0] == '00' ? '' : intval($elap[0]) . ' j ';
                $durasi->lama_ujian .= 'm';
                $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                $mulai = new DateTime($durasi->mulai);
                if (strpos($lamanya, ':') !== false) {
                }
                $ed = $elap[2] == '00' ? 0 : 1;
                $lamanya = $durasi->lama_ujian;
                $durasi->lama_ujian = round($mins, 2) . ' m';
                $durasi->lama_ujian = $dd == '' ? '0 m' : $dd;
                $dur_siswa = $durasi;
                $dd = $ej . $em;
                $durasi->ada_waktu = $minutes < $info->durasi_ujian;
                $interval = $mulai->diff(new DateTime());
                $elap = explode(':', $lamanya ?? '');
                if ($durasi->lama_ujian == null) {
                }
                $em = $elap[1] == '00' ? '' : intval($elap[1]) + $ed . ' m';
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
            }
            foreach ($logs as $log) {
                array_push($log_siswa, $log);
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
            }
            $dur_siswa = null;
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $data['durasi'] = $arrDur;
        $data['siswa'] = $siswas;
        $this->db->trans_complete();
        $this->db->trans_start();
        $info = $this->cbt->getJadwalById($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal);
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $kelas = $this->input->get('kelas');
        $this->output_json($data);
        $data['info'] = $info;
        $arrDur = [];
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas);
        $smt = $this->dashboard->getSemesterActive();
    }
    public function getSiswaRuang()
    {
        $data['siswa'] = $siswas;
        $ids_pengawas = [];
        $info = $this->cbt->getJadwalById($jadwal);
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $this->db->trans_complete();
        $data['info'] = $info;
        $arrDur = [];
        $ruang = $this->input->get('ruang');
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi, $ruang);
        $jadwal = $this->input->get('jadwal');
        $this->db->trans_start();
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $data['pengawas'] = $this->master->getGuruByArrId($ids_pengawas);
        foreach ($siswas as $siswa) {
            foreach ($durasies as $durasi) {
                $lamanya = $durasi->lama_ujian;
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                }
                $interval = $mulai->diff(new DateTime());
                $ej = $elap[0] == '00' ? '' : intval($elap[0]) . ' j ';
                if (strpos($lamanya, ':') !== false) {
                }
                $durasi->ada_waktu = $minutes < $info->durasi_ujian;
                $mulai = new DateTime($durasi->mulai);
                $dd = $ej . $em;
                if ($durasi->lama_ujian == null) {
                }
                $durasi->lama_ujian .= 'm';
                $em = $elap[1] == '00' ? '' : intval($elap[1]) + $ed . ' m';
                $durasi->lama_ujian = round($mins, 2) . ' m';
                $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                $elap = explode(':', $lamanya ?? '');
                $mins = (strtotime($durasi->selesai) - strtotime($durasi->mulai)) / 60;
                $dur_siswa = $durasi;
                $durasi->lama_ujian = $dd == '' ? '0 m' : $dd;
                $ed = $elap[2] == '00' ? 0 : 1;
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
            $dur_siswa = null;
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                }
                array_push($log_siswa, $log);
            }
            $log_siswa = [];
        }
        $smt = $this->dashboard->getSemesterActive();
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $data['durasi'] = $arrDur;
        $tp = $this->dashboard->getTahunActive();
        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $sesi, $info->bank_level);
        $this->output_json($data);
        $sesi = $this->input->get('sesi');
    }
    public function detail()
    {
        $this->load->view('cbt/status/detail');
        $data['guru'] = $guru;
        $siswa = $this->input->get('siswa');
        $this->load->view('_templates/dashboard/_footer');
        $user = $this->ion_auth->user()->row();
        $data['tp'] = $this->dashboard->getTahun();
        $data = ['user' => $user, 'judul' => 'Detail Status Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $smt = $this->dashboard->getSemesterActive();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('members/guru/templates/header', $data);
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $data['siswa'] = $this->master->getSiswaById($siswa);
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/status/detail');
        $this->load->view('members/guru/templates/footer');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['soal'] = $this->cbt->getSoalSiswaByJadwal($jadwal, $siswa);
        $jadwal = $this->input->get('jadwal');
        $tp = $this->dashboard->getTahunActive();
        if ($this->ion_auth->is_admin()) {
        }
    }
}
```

---

## File: application/controllers_decoded/Cbttoken.php

```php
<?php

class Cbttoken extends CI_Controller
{
    public function __construct()
    {
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->library(['datatables', 'form_validation']);
        redirect('auth');
        $this->load->model('Cbt_model', 'cbt');
        parent::__construct();
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Master_model', 'master');
        $this->load->dbforge();
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Log_model', 'logging');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
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
        $tkn['auto'] = '0';
        $smt = $this->master->getSemesterActive();
        $this->load->view('_templates/dashboard/_footer');
        $data['guru'] = $guru;
        $tkn['elapsed'] = '00:00:00';
        $tp = $this->master->getTahunActive();
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/cbt/token/data');
        $data['token'] = $token != null ? $token : json_decode(json_encode($tkn));
        $token = $this->cbt->getToken();
        $data['tp_active'] = $tp;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tp'] = $this->dashboard->getTahun();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('members/guru/templates/footer');
        $this->load->view('cbt/token/data');
        $data = ['user' => $user, 'judul' => 'Token Ujian', 'subjudul' => 'Token', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
        $user = $this->ion_auth->user()->row();
        $tkn['token'] = '';
        $tkn['jarak'] = '1';
        if ($this->ion_auth->is_admin()) {
        }
    }
    public function generateToken()
    {
        $token = $this->cbt->getToken();
        $this->output_json($token);
        if ($force == '1') {
        }
        $post_token->updated = $updated;
        $total_minutes = $diff->days * 24 * 60;
        $new = $this->createNewToken();
        $token = $this->cbt->getToken();
        $this->cbt->saveToken($post_token);
        $post_token->updated = $updated;
        $total_minutes += $diff->i;
        $updated = date('Y-m-d H:i:s');
        $this->cbt->saveToken($post_token);
        if (!($total_minutes >= $post_token->jarak)) {
        }
        $diff = $mulai->diff(new DateTime());
        $token->now = $updated;
        $post_token = json_decode($this->input->get('data'));
        $total_minutes += $diff->h * 60;
        $force = $this->input->get('force');
        $post_token->token = $new;
        $new = $this->createNewToken();
        $mulai = new DateTime($token->updated);
        $post_token->token = $new;
    }
    public function loadToken()
    {
        $data['elapsed'] = '00:00:00';
        $data['token'] = '';
        $token->now = date('Y-m-d H:i:s');
        foreach ($dataflds as $fild) {
            $table_changed = $this->dbforge->modify_column('cbt_token', $field);
            if (!($fild->name == 'updated')) {
            }
            $field = ['updated' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => '')];
            if (!($fild->type != 'varchar')) {
            }
        }
        $table_changed = false;
        $this->output_json($token);
        $data['auto'] = '0';
        $dataflds = $this->db->field_data('cbt_token');
        $this->output_json($data);
        $token = $this->cbt->getToken();
        if ($token == null) {
        }
    }
    private function createNewToken()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $new_token;
        if (!($i < 6)) {
        }
        $random_character = $chars[mt_rand(0, $input_length - 1)];
        $new_token = '';
        $i++;
        $input_length = strlen($chars);
        $i = 0;
        $new_token .= $random_character;
    }
}
```

---

## File: application/controllers_decoded/Compare.php

```php
<?php

exit('No direct script access allowed');
if (defined('BASEPATH')) {
}
class Compare extends CI_Controller
{
    function __construct()
    {
        $this->CHARACTER_SET = 'utf8 COLLATE utf8_general_ci';
        $this->DB2 = $this->load->database('live', TRUE);
        parent::__construct();
        $this->DB1 = $this->load->database('main_garuda', TRUE);
    }
    function index()
    {
        $sql_commands_to_run = is_array($tables_to_create) && !empty($tables_to_create) ? array_merge($sql_commands_to_run, $this->manage_tables($tables_to_create, 'create')) : array();
        $tables_to_create = array_diff($development_tables, $live_tables);
        echo '<h2>The database appears to be up to date</h2>
';
        $sql_commands_to_run = is_array($tables_to_drop) && !empty($tables_to_drop) ? array_merge($sql_commands_to_run, $this->manage_tables($tables_to_drop, 'drop')) : array();
        echo '<p>The following SQL commands need to be executed to bring the Live database tables up to date: </p>
';
        $sql_commands_to_run = array();
        echo '<pre>
';
        $tables_to_update = $this->compare_table_structures($development_tables, $live_tables);
        $sql_commands_to_run = is_array($tables_to_update) && !empty($tables_to_update) ? array_merge($sql_commands_to_run, $this->update_existing_tables($tables_to_update)) : '';
        foreach ($sql_commands_to_run as $sql_command) {
            echo "{$sql_command}\n";
        }
        $live_tables = $this->DB2->list_tables();
        $tables_to_drop = array_diff($live_tables, $development_tables);
        if (is_array($sql_commands_to_run) && !empty($sql_commands_to_run)) {
        }
        echo '<h2>The database is out of Sync!</h2>
';
        $tables_to_update = array_diff($tables_to_update, $tables_to_create);
        echo '<pre style=\'padding: 20px; background-color: #FFFAF0;\'>
';
        $development_tables = $this->DB1->list_tables();
    }
    function manage_tables($tables, $action)
    {
        if (!($action == 'drop')) {
        }
        foreach ($tables as $table) {
            $query = $this->DB1->query("SHOW CREATE TABLE `{$table}` -- create tables");
            $table_structure = $query->row_array();
            $sql_commands_to_run[] = $table_structure['Create Table'] . ';';
        }
        foreach ($tables as $table) {
            $sql_commands_to_run[] = "DROP TABLE {$table};";
        }
        return $sql_commands_to_run;
        $sql_commands_to_run = array();
        if (!($action == 'create')) {
        }
    }
    function compare_table_structures($development_tables, $live_tables)
    {
        return $tables_need_updating;
        $live_table_structures = $development_table_structures = array();
        foreach ($development_tables as $table) {
            $tables_need_updating[] = $table;
            if (!($this->count_differences($development_table, $live_table) > 0)) {
            }
            $development_table = $development_table_structures[$table];
            $live_table = isset($live_table_structures[$table]) ? $live_table_structures[$table] : '';
        }
        foreach ($live_tables as $table) {
            $live_table_structures[$table] = $table_structure['Create Table'];
            $query = $this->DB2->query("SHOW CREATE TABLE `{$table}` -- live");
            $table_structure = $query->row_array();
        }
        foreach ($development_tables as $table) {
            $query = $this->DB1->query("SHOW CREATE TABLE `{$table}` -- dev");
            $table_structure = $query->row_array();
            $development_table_structures[$table] = $table_structure['Create Table'];
        }
        $tables_need_updating = array();
    }
    function count_differences($old, $new)
    {
        $old = explode(' ', $old ?? '');
        if (!($old == $new)) {
        }
        $new = explode(' ', $new ?? '');
        $i = 0;
        return $differences;
        $differences = 0;
        if (!($old[$i] != $new[$i])) {
        }
        if (!($i < $length)) {
        }
        $differences++;
        return $differences;
        $new = trim(preg_replace('/\s+/', '', $new) ?? '');
        $old = trim(preg_replace('/\s+/', '', $old) ?? '');
        $length = max(count($old), count($new));
        $i++;
    }
    function update_existing_tables($tables)
    {
        foreach ($tables as $table) {
            $table_structure_live[$table] = $this->table_field_data((array) $this->DB2, $table);
            $table_structure_development[$table] = $this->table_field_data((array) $this->DB1, $table);
        }
        $table_structure_live = array();
        if (!(is_array($tables) && !empty($tables))) {
        }
        return $sql_commands_to_run;
        $table_structure_development = array();
        $sql_commands_to_run = array();
        $sql_commands_to_run = array_merge($sql_commands_to_run, $this->determine_field_changes($table_structure_development, $table_structure_live));
    }
    function table_field_data($database, $table)
    {
        $conn = mysqli_connect($database['hostname'], $database['username'], $database['password']);
        mysql_select_db($database['database']);
        if (!$row = mysql_fetch_assoc($result)) {
        }
        return $fields;
        $fields[] = $row;
        $result = mysql_query("SHOW COLUMNS FROM `{$table}`");
    }
    function determine_field_changes($source_field_structures, $destination_field_structures)
    {
        return $sql_commands_to_run;
        $sql_commands_to_run = array();
        foreach ($source_field_structures as $table => $fields) {
            foreach ($fields as $field) {
                $add_field .= isset($field['Extra']) && $field['Extra'] != '' ? ' ' . $field['Extra'] : '';
                $sql_commands_to_run[] = $add_field;
                if (!(isset($fields[$n]) && isset($destination_field_structures[$table][$n]) && $fields[$n]['Field'] == $destination_field_structures[$table][$n]['Field'])) {
                }
                if ($this->in_array_recursive($field['Field'], $destination_field_structures[$table])) {
                }
                $add_field .= ';';
                $n++;
                $sql_commands_to_run[] = $modify_field;
                $n = 0;
                $previous_field = $fields[$n]['Field'];
                $add_field .= isset($field['Null']) && $field['Null'] == 'YES' ? ' Null' : '';
                if (!(is_array($differences) && !empty($differences))) {
                }
                if (!($modify_field != '' && !in_array($modify_field, $sql_commands_to_run))) {
                }
                $differences = array_diff($fields[$n], $destination_field_structures[$table][$n]);
                $modify_field = '';
                $modify_field .= isset($previous_field) && $previous_field != '' ? ' AFTER ' . $previous_field : '';
                $modify_field .= ';';
                $modify_field .= isset($fields[$n]['Null']) && $fields[$n]['Null'] == 'YES' ? ' NULL' : ' NOT NULL';
                if (!($n < count($fields))) {
                }
                $add_field .= ' DEFAULT ' . $field['Default'];
                $modify_field .= isset($fields[$n]['Extra']) && $fields[$n]['Extra'] != '' ? ' ' . $fields[$n]['Extra'] : '';
                $modify_field = "ALTER TABLE {$table} MODIFY COLUMN `" . $fields[$n]['Field'] . '` ' . $fields[$n]['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
                $modify_field .= isset($fields[$n]['Default']) && $fields[$n]['Default'] != '' ? ' DEFAULT \'' . $fields[$n]['Default'] . '\'' : '';
                $add_field = "ALTER TABLE {$table} ADD COLUMN `" . $field['Field'] . '` ' . $field['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
            }
        }
    }
    function in_array_recursive($needle, $haystack, $strict = false)
    {
        return false;
        foreach ($haystack as $array => $item) {
            return true;
            if (!(($strict ? $item === $needle : $item == $needle) || is_array($item) && in_array_recursive($needle, $item, $strict))) {
            }
            $item = $item['Field'];
        }
    }
}
```

---

## File: application/controllers_decoded/Dashboard.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        if ($this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        parent::__construct();
    }
    public function admin_box($setting, $tp, $smt)
    {
        $where = '';
        return $info_box;
        if ($setting->jenjang == '2') {
        }
        if ($setting->jenjang == '1') {
        }
        $box = [['box' => 'blue', 'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa', 'url' => 'datasiswa', 'icon' => 'users'], ['box' => 'cyan', 'total' => $this->dashboard->total('master_kelas', 'id_tp=' . $tp . ' AND id_smt=' . $smt), 'title' => 'Rombel', 'url' => 'datakelas', 'icon' => 'bell'], ['box' => 'teal', 'total' => $this->dashboard->total('master_guru'), 'title' => 'Guru', 'url' => 'dataguru', 'icon' => 'user'], ['box' => 'fuchsia', 'total' => $this->dashboard->totalWaliKelas($tp, $smt), 'title' => 'Wali Kelas', 'url' => 'dataguru', 'icon' => 'user'], ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'url' => 'datamapel', 'icon' => 'book'], ['box' => 'yellow', 'total' => $this->dashboard->total('master_ekstra'), 'title' => 'Ekstrakurikuler', 'url' => 'dataekstra', 'icon' => 'book']];
        $where = 'jenjang=0 OR jenjang=1';
        $where = 'jenjang=2 OR jenjang=1';
        $info_box = json_decode(json_encode($box), FALSE);
    }
    public function guru_box($setting)
    {
        $where = 'jenjang=0 OR jenjang=1';
        $info_box = json_decode(json_encode($box), FALSE);
        return $info_box;
        if ($setting->jenjang == '1') {
        }
        if ($setting->jenjang == '2') {
        }
        $where = '';
        $box = [['box' => 'teal', 'total' => $this->dashboard->total('master_kelas'), 'title' => 'Rombel', 'icon' => 'user'], ['box' => 'blue', 'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa', 'icon' => 'users'], ['box' => 'fuchsia', 'total' => $this->dashboard->total('master_guru'), 'title' => 'Guru', 'icon' => 'user'], ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'icon' => 'book']];
        $where = 'jenjang=2 OR jenjang=1';
    }
    public function ujian_box()
    {
        $info_box = json_decode(json_encode($box), FALSE);
        return $info_box;
        $box = [['box' => 'indigo', 'total' => $this->dashboard->total('cbt_ruang'), 'title' => 'Ruang Ujian', 'url' => 'cbtruang', 'icon' => 'school'], ['box' => 'maroon', 'total' => $this->dashboard->total('cbt_sesi'), 'title' => 'Sesi', 'url' => 'cbtsesi', 'icon' => 'clock'], ['box' => 'green', 'total' => $this->dashboard->total('cbt_bank_soal'), 'title' => 'Bank Soal', 'url' => 'cbtbanksoal', 'icon' => 'folder'], ['box' => 'teal', 'total' => $this->dashboard->totalJadwal(), 'title' => 'Jadwal', 'url' => 'cbtjadwal', 'icon' => 'clock']];
    }
    public function menu_siswa_box()
    {
        $info_box = json_decode(json_encode($box), FALSE);
        $box = [['title' => 'Jadwal Pelajaran', 'icon' => 'ic_online.png', 'link' => 'siswa/jadwalpelajaran'], ['title' => 'Materi', 'icon' => 'ic_elearning.png', 'link' => 'siswa/materi'], ['title' => 'Tugas', 'icon' => 'ic_questions.png', 'link' => 'siswa/tugas'], ['title' => 'Ujian / Ulangan', 'icon' => 'ic_question.png', 'link' => 'siswa/cbt'], ['title' => 'Nilai Hasil', 'icon' => 'ic_exam.png', 'link' => 'siswa/hasil'], ['title' => 'Absensi', 'icon' => 'ic_clipboard.png', 'link' => 'siswa/kehadiran'], ['title' => 'Catatan Guru', 'icon' => 'ic_student.png', 'link' => 'siswa/catatan']];
        return $info_box;
    }
    public function index()
    {
        $this->load->view('disable_login', $data);
        $data['tp_active'] = $tp;
        $data['jadwals'] = $arrJadwalKelas[$siswa->id_kelas] ?? [];
        $data['menu'] = $this->menu_siswa_box();
        $this->load->view('dashboard');
        $tkn['jarak'] = '1';
        $this->load->view('members/guru/dashboard');
        $data['kbms'] = $arrKbm;
        if ($guru == null) {
        }
        $data['login'] = $siswa;
        $tkn['auto'] = '0';
        $data['gurus'] = $this->dropdown->getAllGuru();
        if (!($tp != null)) {
        }
        $this->load->view('members/guru/templates/footer');
        foreach ($tglJadwals as $tgl => $jadwalss) {
            foreach ($jadwalss as $mpl => $jadwals) {
                foreach ($jadwals as $jadwal) {
                    $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
                    foreach ($jadwal->bank_kelas as $kb) {
                        $jadwal->peserta[] = $p;
                        if (!($kb['kelas_id'] != '')) {
                        }
                        $p = $this->cbt->getKelasUjian($kb['kelas_id']);
                    }
                }
            }
        }
        if ($siswa == null) {
        }
        $tkn['elapsed'] = '00:00:00';
        $token = $this->cbt->getToken();
        $this->load->view('disable_login', $data);
        $data['smt'] = $this->dashboard->getSemester();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('members/guru/templates/header', $data);
        $data['running_text'] = $this->dashboard->getRunningText();
        $data['token'] = $token != null ? $token : json_decode(json_encode($tkn));
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $kelass = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->in_group('siswa')) {
        }
        $tp = $this->dashboard->getTahunActive();
        $data['ujian_box'] = $this->ujian_box();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['ujian_box'] = $this->ujian_box();
        $user = $this->ion_auth->user()->row();
        $this->load->view('_templates/dashboard/_footer');
        $data['mapels'] = $this->master->getAllMapel();
        $data['info_box'] = $this->admin_box($setting, $tp->id_tp, $smt->id_smt);
        $siswa = $this->dashboard->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $kbms = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt);
        $arrJadwalKelas = [];
        $data['jadwals'] = $arrJadwalKelas;
        $setting = $this->dashboard->getSetting();
        $kelass = [];
        $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []);
        foreach ($jadwal as $key => $item) {
            $arrJadwalKelas[$item->id_kelas][$item->jam_ke] = $item;
        }
        $data['guru'] = $guru;
        foreach ($kbms as $kbm) {
            $kbm->istirahat = unserialize($kbm->istirahat);
        }
        $data['info_box'] = $this->admin_box($setting, $tp->id_tp, $smt->id_smt);
        $jadwal = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, null, $day);
        $data['kelases'] = $kelass;
        $data['siswa'] = $siswa;
        foreach ($kbms as $key => $item) {
            $arrKbm[$item->id_kelas] = $item;
        }
        $data['pengawas'] = $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt, null, null);
        $data['kbms'] = $arrKbm[$siswa->id_kelas] ?? null;
        if ($this->ion_auth->in_group('guru')) {
        }
        $tkn['token'] = '';
        $this->load->view('members/siswa/templates/header', $data);
        $data['jadwals_ujian'] = $tglJadwals;
        $this->load->view('members/siswa/templates/footer');
        $tglJadwals = $this->cbt->getAllJadwalByJenis(null, $tp->id_tp, $smt->id_smt);
        $arrKbm = [];
        $this->load->view('members/siswa/dashboard');
        if ($this->ion_auth->is_admin()) {
        }
        $data = ['user' => $user, 'judul' => 'Beranda', 'subjudul' => 'Halaman Utama', 'setting' => $setting];
        $day = date('N', strtotime(date('Y-m-d')));
        $data['smt_active'] = $smt;
    }
    public function checkTokenJadwal()
    {
        $token = $this->cbt->getToken();
        $data['token'] = $token;
        $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        $token->now = date('d-m-Y H:i:s');
        $this->output_json($data);
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function gantiTahun()
    {
        if (!($i <= $rows)) {
        }
        $aktif = $this->input->post('active', true);
        $this->output_json($data);
        if ($id_tp === $aktif) {
        }
        $rows = count($this->input->post('tahun', true));
        $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
        $i = 0;
        $i++;
        $data['update'] = $update;
        $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
        $id_tp = $this->input->post('id_tp[' . $i . ']', true);
        $update[] = array('id_tp' => $id_tp, 'tahun' => $tahun, 'active' => $active);
        $active = 0;
        $data['status'] = true;
        $active = 1;
        $tahun = $this->input->post('tahun[' . $i . ']', true);
    }
    public function gantiSemester()
    {
        $update[] = array('id_smt' => $id_smt, 'smt' => $smt, 'active' => $active);
        $i = 1;
        $rows = count($this->input->post('smt', true));
        $this->output_json($data);
        $smt = $this->input->post('smt[' . $i . ']', true);
        if (!($i <= $rows)) {
        }
        $i++;
        $this->logging->saveLog(4, 'mengganti semester aktif');
        $active = 1;
        $aktif = $this->input->post('active', true);
        $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
        $data['update'] = $update;
        if ($id_smt === $aktif) {
        }
        $data['status'] = true;
        $active = 0;
        $id_smt = $this->input->post('id_smt[' . $i . ']', true);
    }
    public function getNotifikasi()
    {
    }
    public function getLog($limit)
    {
        $this->output_json($this->logging->loadAktifitas($limit));
    }
    public function hapusLog()
    {
        $deleted = ['status' => true, 'message' => 'berhasil'];
        $this->db->trans_complete();
        $this->output_json($deleted);
        $this->db->trans_start();
        $deleted = ['status' => false, 'message' => 'gagal'];
        if ($this->db->empty_table('log')) {
        }
    }
    public function getLogSiswa($limit)
    {
        $this->output_json($this->logging->loadAktifitasSiswa($limit));
    }
    public function getPengumuman($for)
    {
        $this->output_json($this->dashboard->loadPengumuman($for));
    }
    public function getJadwalHariIni($id_kelas, $id_hari)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, $id_kelas, $id_hari));
    }
    public function getJadwalKbm($id_kelas)
    {
        $jadwal = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt, $id_kelas);
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json(array('jadwal' => $jadwal, 'istirahat' => $istirahat));
        $istirahat = unserialize($jadwal->istirahat);
        $tp = $this->dashboard->getTahunActive();
    }
}
```

---

## File: application/controllers_decoded/Dataalumni.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataalumni extends CI_Controller
{
    public function __construct()
    {
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Rapor_model', 'rapor');
        parent::__construct();
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Kelas_model', 'kelas');
        $this->load->library('upload');
        $this->load->model('Dropdown_model', 'dropdown');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        redirect('auth');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function index()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $uids = $this->db->select('id_siswa, uid')->from('master_siswa')->get()->result();
        $kelas_akhir = $this->input->get('kelas', true);
        $data['tahun_selected'] = $tahun;
        $tahun = $this->input->get('tahun', true);
        if ($tahun != null && $tahun != '') {
        }
        $data['jumlah_lulus'] = 0;
        $this->load->view('_templates/dashboard/_header', $data);
        foreach ($uids as $uid) {
            if (!($check->get()->num_rows() == 0)) {
            }
            $check = $this->db->select('id_siswa')->from('buku_induk')->where('id_siswa', $uid->id_siswa);
            $this->db->insert('buku_induk', $uid);
        }
        $this->load->view('master/alumni/data');
        $this->load->view('_templates/dashboard/_footer');
        $jumlah_lulus = $this->rapor->getJumlahLulus($tp->id_tp - 1, '2', $level);
        $count_induk = $this->db->count_all('buku_induk');
        $splitTahun = explode('/', $tpBefore ?? '');
        if ($jumlah_lulus > count($alumnis)) {
        }
        $data['tahun_lulus'] = $this->master->getDistinctTahunLulus();
        $smt = $this->dashboard->getSemesterActive();
        $data['kelas_akhir'] = $this->master->getDistinctKelasAkhir();
        $data['alumnis'] = $this->master->getAlumniByTahun($tahun, $kelas_akhir);
        $data['tp_active'] = $tp;
        if (!($count_siswa > $count_induk)) {
        }
        $setting = $this->dashboard->getSetting();
        $data['smt_active'] = $smt;
        $idSearch = array_search($tp->id_tp - 1, array_column($allTp, 'id_tp'));
        $tpBefore = $allTp[$idSearch]->tahun;
        $user = $this->ion_auth->user()->row();
        $data['jumlah_lulus'] = $jumlah_lulus;
        $allTp = $this->dashboard->getTahun();
        $tp = $this->dashboard->getTahunActive();
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $data = ['user' => $user, 'judul' => 'Data Kelulusan & Alumni', 'subjudul' => 'Data Alumni', 'setting' => $setting];
        $data['kelas_selected'] = $kelas_akhir;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $alumnis = $this->master->getAlumniByTahun($splitTahun[1]);
        if ($tahun == null) {
        }
        $data['tp'] = $allTp;
        $count_siswa = $this->db->count_all('master_siswa');
    }
    public function generateAlumni()
    {
        $this->db->trans_start();
        $tpBefore = $allTp[$searchId - 1]->tahun;
        $setting = $this->dashboard->getSetting();
        $idBefore = $allTp[$searchId - 1]->id_tp;
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $tp = $this->dashboard->getTahunActive();
        $searchId = array_search('1', array_column($allTp, 'active'));
        foreach ($siswas as $siswa) {
            $this->db->update('buku_induk');
            if ($siswa->naik != null && $siswa->naik == '0') {
            }
            $this->db->set('kelas_akhir', $siswa->kelas_akhir);
            $this->db->set('tahun_lulus', $splitTahun[1]);
            $this->db->set('status', '2');
            $this->db->where('id_siswa', $siswa->id_siswa);
            $ids[] = $siswa->id_siswa;
            $this->db->set('no_ijazah', '- -');
        }
        $ids = [];
        $splitTahun = explode('/', $tpBefore ?? '');
        $smt = $this->dashboard->getSemesterActive();
        $this->db->trans_complete();
        $allTp = $this->dashboard->getTahun();
        $siswas = $this->rapor->getSiswaLulus($tp->id_tp - 1, '2', $level);
        $this->output_json($ids);
    }
    public function luluskan()
    {
        $idks = [];
        $alumnikelas = [];
        $mode = $this->input->post('mode', true);
        $smt = $this->dashboard->getSemesterActive();
        foreach ($posts as $d) {
            $idkelases[] = $d->kelas_baru;
            $alumnikelas[$d->kelas_baru][] = ['id' => $d->id_siswa];
        }
        $data['res'] = $alumnikelas;
        $tp = $this->dashboard->getTahunActive();
        $idkelases = [];
        foreach ($idkelases as $ik) {
            array_push($idks, $this->db->insert_id());
            array_push($idks, $kelas_baru->id_kelas);
            $kelas = $this->kelas->get_one($ik, $tp->id_tp - 1, '2');
            $jumlah = serialize($alumnikelas[$ik]);
            if ($kelas_baru == null) {
            }
            $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'alumni_id' => $kelas->alumni_id, 'jumlah_alumni' => $jumlah);
            $this->db->update('master_kelas', $data);
            $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'alumni_id' => $kelas->alumni_id, 'jumlah_alumni' => $jumlah);
            foreach ($alumnikelas[$ik] as $s) {
                foreach ($jmlLama as $lama) {
                    array_push($idks, $kelas_baru->id_kelas);
                    if (!($lama['id'] != $s['id'])) {
                    }
                    array_push($jmlLama, ['id' => $s['id']]);
                }
            }
            $this->db->where('id_kelas', $kelas_baru->id_kelas);
            $jmlLama = unserialize($kelas_baru->jumlah_alumni ?? '');
            if ($mode == 'peralumni') {
            }
            $jumlah = serialize($jmlLama);
            $kelas_baru = $this->kelas->getKelasByNama($kelas->nama_kelas, $tp->id_tp, $smt->id_smt);
            $this->db->insert('master_kelas', $data);
            $jumlah = serialize($alumnikelas[$ik]);
            foreach ($idks as $idk) {
                foreach ($alumnikelas[$ik] as $s) {
                    $res[] = $this->db->replace('kelas_alumni', $insert);
                    $insert = ['id_kelas_alumni' => $tp->id_tp . $smt->id_smt . $s['id'], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $s['id']];
                }
            }
        }
        $posts = json_decode($this->input->post('kelas', true));
        $idkelases = array_unique($idkelases);
        $this->output_json($data);
        $res = [];
    }
    public function detail($id)
    {
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $user = $this->ion_auth->user()->row();
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $this->load->view('master/alumni/edit');
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $alumni->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $alumni->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $alumni->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $alumni->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $alumni->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $data = ['user' => $user, 'judul' => 'Alumni', 'subjudul' => 'Edit Data Alumni', 'alumni' => $alumni, 'setting' => $this->dashboard->getSetting()];
        $inputBio = [['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $alumni->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $alumni->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $alumni->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $alumni->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $alumni->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $alumni->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $alumni->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $alumni->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $alumni->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $alumni->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $alumni->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $alumni->nama, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $alumni->nis, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $alumni->nisn, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $alumni->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $alumni->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $alumni->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'class' => 'tahun', 'type' => 'text']];
        $alumni = $this->master->getAlumniById($id);
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $alumni->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $alumni->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $alumni->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $alumni->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $alumni->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $alumni->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $alumni->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $alumni->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $alumni->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $alumni->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $this->load->view('_templates/dashboard/_footer');
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function add()
    {
        $data = ['user' => $user, 'judul' => 'Alumni', 'subjudul' => 'Tambah Data Alumni', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_footer');
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['tipe'] = 'add';
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('master/alumni/add');
        $user = $this->ion_auth->user()->row();
    }
    public function create()
    {
        $this->db->insert('master_siswa', $insert);
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $data['text'] = 'Alumni berhasil ditambahkan';
        $nis = $this->input->post('nis', true);
        $this->output_json($data);
        $insert = ['nama' => $this->input->post('nama_alumni', true), 'nis' => $nis, 'nisn' => $nisn, 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'foto' => 'uploads/foto_siswa/' . $nis . 'jpg'];
        $last_id = $this->db->insert_id();
        $data['insert'] = $this->db->insert('buku_induk', $induk);
        $data['text'] = 'Data Sudah ada, Pastikan NIS, NISN dan Username belum digunakan alumni lain';
        $u_nisn = '|is_unique[master_siswa.nisn]';
        if ($this->form_validation->run() == FALSE) {
        }
        $uid = $this->db->select('uid')->from('master_siswa')->where('id_siswa', $last_id)->get()->row();
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        $nisn = $this->input->post('nisn', true);
        $u_nis = '|is_unique[master_siswa.nis]';
        $data['insert'] = false;
        $this->db->set('uid', 'UUID()', FALSE);
        $induk = ['id_siswa' => $last_id, 'uid' => $uid->uid, 'kelas_akhir' => $this->input->post('kelas_akhir', true), 'tahun_lulus' => $this->input->post('tahun_lulus', true), 'no_ijazah' => $this->input->post('no_ijazah', true), 'status' => 2];
    }
    public function edit()
    {
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $alumni->nama, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $alumni->nis, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $alumni->nisn, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $alumni->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $alumni->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $alumni->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'class' => 'tahun', 'type' => 'text']];
        $this->load->view('_templates/dashboard/_footer');
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $alumni = $this->master->getAlumniById($id);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Alumni', 'subjudul' => 'Edit Data Alumni', 'alumni' => $alumni, 'setting' => $this->dashboard->getSetting()];
        $inputBio = [['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $alumni->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $alumni->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $alumni->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $alumni->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $alumni->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $alumni->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $alumni->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $alumni->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $alumni->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $alumni->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $alumni->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $id = $this->input->get('id', true);
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $alumni->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $alumni->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $alumni->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $alumni->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $alumni->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $alumni->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $alumni->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $alumni->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $alumni->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $alumni->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $alumni->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $alumni->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $alumni->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $alumni->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $alumni->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $this->load->view('master/alumni/edit');
        $this->load->view('_templates/dashboard/_header', $data);
    }
    public function updateData()
    {
        $u_nis = $alumni->nis === $nis ? '' : '|is_unique[mater_alumni.nis]';
        $alumni = $this->master->getAlumniById($id_siswa);
        $nisn = $this->input->post('nisn', true);
        $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->input->post('tanggal_lahir', true), 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => 'uploads/foto_siswa/' . $nis . '.jpg'];
        if ($this->form_validation->run() == FALSE) {
        }
        $data['text'] = 'Alumni berhasil diperbaharui';
        $action = $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $data['insert'] = $input;
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        $this->output_json($data);
        $id_siswa = $this->input->post('id_siswa', true);
        $data['insert'] = false;
        $data['text'] = 'Data Sudah ada, Pastikan NIS, dan NISN belum digunakan alumni lain';
        $u_nisn = $alumni->nisn === $nisn ? '' : '|is_unique[mater_alumni.nisn]';
        $nis = $this->input->post('nis', true);
    }
    function uploadFile($id_siswa)
    {
        if (isset($_FILES['foto']['name'])) {
        }
        $data['type'] = $_FILES['foto']['type'];
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
        $this->output_json($data);
        $result = $this->upload->data();
        $config['file_name'] = $alumni->nis;
        $data['status'] = true;
        $data['src'] = '';
        $this->db->where('id_siswa', $id_siswa);
        $data['status'] = false;
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $alumni = $this->master->getAlumniById($id_siswa);
        $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
        $data['size'] = $_FILES['foto']['size'];
        if (!$this->upload->do_upload('foto')) {
        }
        $data['src'] = $this->upload->display_errors();
        $config['upload_path'] = './uploads/foto_siswa/';
        $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
        $config['overwrite'] = true;
        $this->upload->initialize($config);
        $this->db->update('master_siswa');
    }
    function deleteFoto()
    {
        $file_name = str_replace(base_url(), '', $src ?? '');
        $src = $this->input->post('src');
        echo 'File Delete Successfully';
        unlink($file_name);
    }
    public function delete()
    {
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $this->output_json(['status' => false]);
        if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
        }
        $chk = $this->input->post('checked', true);
        if (!$chk) {
        }
    }
    public function do_import()
    {
        foreach ($input as $key1 => $val1) {
            foreach (((array) $input)[$key1] as $key => $val) {
                $data[$key] = $val;
            }
            $data = [];
            $save = $this->db->insert('master_siswa', $data);
            $data['foto'] = 'uploads/foto_siswa/' . $data['nis'] . '.jpg';
        }
        $this->output->set_content_type('application/json')->set_output($save);
        $input = json_decode($this->input->post('alumni', true));
        $this->db->trans_complete();
        $this->db->trans_start();
    }
    public function editKelulusan()
    {
        $kelas_akhir = $this->input->post('kelas_akhir', true);
        $no_ijazah = $this->input->post('no_ijazah', true);
        $this->db->set('no_ijazah', $no_ijazah);
        $status = $this->db->update('master_siswa');
        $this->db->where('id_siswa', $id_siswa);
        $this->output_json($data);
        $id_siswa = $this->input->post('id_siswa', true);
        $this->db->set('kelas_akhir', $kelas_akhir);
        $data['status'] = $status;
        $this->db->set('tahun_lulus', $thn);
        $thn = $this->input->post('tahun_lulus', true);
    }
}
```

---

## File: application/controllers_decoded/Dataekstra.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataekstra extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        redirect('auth');
        if ($this->ion_auth->is_admin()) {
        }
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Kelas_model', 'kelas');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $data['kelas'] = $kelas;
        $user = $this->ion_auth->user()->row();
        $this->load->view('master/ekstra/data');
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $data['ekskul_kelas'] = $kelasEks;
        $data['smt_active'] = $smt;
        $this->load->view('_templates/dashboard/_header', $data);
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['ekskul'] = $this->dropdown->getAllEkskul();
        foreach ($kelas as $key => $kls) {
            $kelasEks[$key] = $this->kelas->getKelasEkskul($key, $tp->id_tp, $smt->id_smt);
        }
        $tp = $this->dashboard->getTahunActive();
        $data['pembimbing'] = $this->dropdown->getAllGuru();
        $smt = $this->dashboard->getSemesterActive();
        $kelasEks = [];
        $data = ['user' => $user, 'judul' => 'Ekstrakurikuler', 'subjudul' => 'Data Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $data = $this->master->create('master_ekstra', $insert);
        $insert = ['nama_ekstra' => $this->input->post('nama_ekstra', true), 'kode_ekstra' => $this->input->post('kode_ekstra', true)];
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
        foreach ($tabless as $table) {
            foreach ($fields as $field) {
                if (!($field->name == 'id_ekstra' || $field->name == 'ekstra_id')) {
                }
                array_push($tables, $table);
            }
            $fields = $this->db->field_data($table);
        }
        if ($this->master->delete('master_ekstra', [$id], 'id_ekstra')) {
        }
        $tabless = $this->db->list_tables();
        $tables = [];
        $this->output_json($tables);
        foreach ($tables as $table) {
            if (!($num > 0)) {
            }
            if (!($table != 'master_ekstra')) {
            }
            $num = $this->db->count_all_results($table);
            $this->db->where('id_ekstra', $id);
            array_push($messages, $table);
        }
        $messages = [];
        if ($messages && count($messages) > 0) {
        }
        $this->output_json(['status' => false, 'message' => 'Ekskul gagal dihapus']);
        $this->output_json(['status' => true, 'message' => 'Ekskul berhasil dihapus']);
        $this->output_json(['status' => false, 'total' => 'Mapel digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
    }
    public function save()
    {
        $update = [];
        $row_insert = 0;
        foreach ($check_kelas as $key => $kls) {
            $ekstra[] = ['ekstra' => $kelaseks];
            $ekstras = ['id_kelas_ekstra' => $kls->kls_id . $tp . $smt, 'id_kelas' => $kls->kls_id, 'id_tp' => $tp, 'id_smt' => $smt, 'ekstra' => serialize($ekstra)];
            $j = 0;
            if (!($j <= $row_ekskul)) {
            }
            $row_ekskul = count($this->input->post('ekskul' . $kls->kls_id, true));
            $kelaseks = $this->input->post('ekskul' . $kls->kls_id . '[' . $j . ']', true);
            $ekstra = [];
            $update[] = $this->db->replace('kelas_ekstra', $ekstras);
            if (!$check_ekskul) {
            }
            $j++;
            $check_ekskul = $this->input->post('ekskul' . $kls->kls_id, true);
        }
        $smt = $this->master->getSemesterActive()->id_smt;
        $this->output_json($res);
        $tp = $this->master->getTahunActive()->id_tp;
        $check_kelas = json_decode(json_encode(json_decode($this->input->post('kelas', true))));
        $res['update'] = $update;
        $res['status'] = true;
    }
    public function import($import_data = null)
    {
        $this->load->view('master/ekstra/import');
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Import Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['import'] = $import_data;
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_footer');
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        if (!($import_data != null)) {
        }
    }
}
```

---

## File: application/controllers_decoded/Dataguru.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataguru extends CI_Controller
{
    public function __construct()
    {
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        if ($this->ion_auth->is_admin()) {
        }
        redirect('auth');
        $this->load->library(['datatables', 'form_validation']);
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
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
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $ret = [];
        $this->load->view('_templates/dashboard/_header', $data);
        $data['gurus'] = $this->master->getAllDataGuru($tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        foreach ($mapels as $key => $row) {
            $ret[$row->id_mapel] = $row;
        }
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'judul' => 'Guru', 'subjudul' => 'Data Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['mode'] = $mode == null ? '1' : '2';
        $setting = $this->dashboard->getSetting();
        $this->load->model('Dropdown_model', 'dropdown');
        $data['smt_active'] = $smt;
        $this->load->view('master/guru/data');
        $data['smt'] = $this->dashboard->getSemester();
        $mode = $this->input->get('mode', true);
        $data['extras'] = $this->dropdown->getAllKodeEkskul();
        $data['kelass'] = $this->master->getAllKelas($tp->id_tp, $smt->id_smt);
        $this->load->model('Master_model', 'master');
        $data['mapels'] = $ret;
        $mapels = $this->master->getAllMapel();
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Dashboard_model', 'dashboard');
        if (!$mapels) {
        }
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function data()
    {
        $this->load->model('Master_model', 'master');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->master->getDataGuru($tp->id_tp, $smt->id_smt), false);
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function edit($id)
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['tp'] = $this->dashboard->getTahun();
        $tp = $this->master->getTahunActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $inputsAlamat = [['label' => 'NIK', 'name' => 'no_ktp', 'value' => $guru->no_ktp, 'icon' => 'far fa-id-card', 'type' => 'number'], ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'value' => $guru->tempat_lahir, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Tgl. Lahir', 'name' => 'tgl_lahir', 'value' => $guru->tgl_lahir, 'icon' => 'fa fa-calendar', 'type' => 'text'], ['label' => 'Alamat', 'name' => 'alamat_jalan', 'value' => $guru->alamat_jalan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kecamatan', 'name' => 'kecamatan', 'value' => $guru->kecamatan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kota/Kab.', 'name' => 'kabupaten', 'value' => $guru->kabupaten, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Provinsi', 'name' => 'provinsi', 'value' => $guru->provinsi, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kode Pos', 'name' => 'kode_pos', 'value' => $guru->kode_pos, 'icon' => 'fa fa-envelope', 'type' => 'number']];
        $data = ['user' => $user, 'judul' => 'Edit Guru', 'subjudul' => 'Edit Data Guru', 'mapel' => $this->master->getAllMapel(), 'guru' => $guru, 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['id_active'] = $id;
        $this->load->model('Master_model', 'master');
        $data['input_alamat'] = json_decode(json_encode($inputsAlamat), FALSE);
        $inputsProfile = [['label' => 'Nama Lengkap', 'name' => 'nama_guru', 'value' => $guru->nama_guru, 'icon' => 'far fa-user', 'type' => 'text'], ['label' => 'Email', 'name' => 'email', 'value' => $guru->email, 'icon' => 'far fa-envelope', 'type' => 'text'], ['label' => 'NIP / NUPTK', 'name' => 'nip', 'value' => $guru->nip, 'icon' => 'far fa-id-card', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $guru->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'type' => 'text'], ['label' => 'No. Handphone', 'name' => 'no_hp', 'value' => $guru->no_hp, 'icon' => 'fa fa-phone', 'type' => 'number'], ['label' => 'Agama', 'name' => 'agama', 'value' => $guru->agama, 'icon' => 'far fa-user', 'type' => 'text']];
        $data['input_profile'] = json_decode(json_encode($inputsProfile), FALSE);
        $data['smt'] = $this->dashboard->getSemester();
        $setting = $this->dashboard->getSetting();
        $this->load->view('master/guru/edit');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data['smt_active'] = $smt;
        $data['tp_active'] = $tp;
        $smt = $this->master->getSemesterActive();
        $guru = $this->master->getGuruById($id, $tp->id_tp, $smt->id_smt);
    }
    public function create()
    {
        $password = $this->input->post('password', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $this->output_json(['status' => false]);
        $this->output_json($data);
        $data = ['status' => false, 'errors' => ['nip' => form_error('nip'), 'nama_guru' => form_error('nama_guru'), 'username' => form_error('username'), 'password' => form_error('password')]];
        $username = $this->input->post('username', true);
        $nip = $this->input->post('nip', true);
        if ($action) {
        }
        $u_nip = 'is_unique[master_guru.nip]';
        $this->load->model('Master_model', 'master');
        $action = $this->master->create('master_guru', $input);
        $this->form_validation->set_rules('nip', 'NIP', 'required|numeric|trim|' . $u_nip);
        $input = ['nip' => trim($nip ?? ''), 'nama_guru' => trim($nama_guru ?? ''), 'username' => trim($username ?? ''), 'password' => trim($password ?? ''), 'foto' => 'uploads/profiles/' . trim($nip ?? '00') . '.jpg'];
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[2]');
        $this->output_json(['status' => true]);
        $u_username = '|is_unique[master_guru.username]';
        $this->form_validation->set_rules('username', 'Username', 'required|trim' . $u_username);
        if ($this->form_validation->run() == FALSE) {
        }
    }
    public function save()
    {
        $u_email = '|is_unique[guru.email]';
        $method = $this->input->post('method', true);
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[8]' . $u_nip);
        $input = ['nip' => $nip, 'nama_guru' => $nama_guru, 'email' => $email, 'mapel_id' => $mapel];
        $nip = $this->input->post('nip', true);
        $mapel = $this->input->post('password', true);
        $this->form_validation->set_rules('mapel', 'Mata Kuliah', 'required');
        $data = ['status' => false, 'errors' => ['nip' => form_error('nip'), 'nama_guru' => form_error('nama_guru'), 'email' => form_error('email'), 'mapel' => form_error('mapel')]];
        if ($this->form_validation->run() == FALSE) {
        }
        $this->output_json(['status' => true]);
        $this->output_json(['status' => false]);
        $id_guru = $this->input->post('id_guru', true);
        $email = $this->input->post('email', true);
        $u_nip = $dbdata->nip === $nip ? '' : '|is_unique[guru.nip]';
        $action = $this->master->create('master_guru', $input);
        $u_email = $dbdata->email === $email ? '' : '|is_unique[guru.email]';
        $action = $this->master->update('master_guru', $input, 'id_guru', $id_guru);
        if ($method === 'add') {
        }
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[3]');
        $this->output_json($data);
        $this->load->model('Master_model', 'master');
        $nama_guru = $this->input->post('nama_guru', true);
        if ($action) {
        }
        $u_nip = '|is_unique[guru.nip]';
        if (!($method === 'edit')) {
        }
        if ($method == 'add') {
        }
        $dbdata = $this->master->getGuruById($id_guru);
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $u_email);
    }
    public function deleteGuru()
    {
        foreach ($tables as $table) {
            if ($table == 'master_kelas') {
            }
            $this->db->where('id_guru', $chk);
            $this->db->where('guru_id', $chk);
            $num = $this->db->count_all_results($table);
            if (!($num > 0)) {
            }
            $num = $this->db->count_all_results($table);
            array_push($messages, $table);
            if (!($table != 'master_guru')) {
            }
        }
        $this->load->model('Master_model', 'master');
        $this->output_json($data);
        $data['status'] = $this->master->delete('master_guru', $chk, 'id_guru');
        $tables = [];
        $chk = $this->input->post('id_guru', true);
        $tabless = $this->db->list_tables();
        $this->output_json(['count' => count($messages), 'status' => false, 'message' => 'Data guru digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        foreach ($tabless as $table) {
            foreach ($fields as $field) {
                array_push($tables, $table);
                if (!($field->name == 'id_guru' || $field->name == 'guru_id')) {
                }
            }
            $fields = $this->db->field_data($table);
        }
        if (count($messages) > 0) {
        }
        $messages = [];
    }
    public function detail($id_guru)
    {
        $data['kelas'] = $this->master->getAllKelas();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_header', $data);
        $setting = $this->dashboard->getSetting();
        $this->load->model('Master_model', 'master');
        $data = ['user' => $user, 'judul' => 'Detail Guru', 'subjudul' => 'Info Jabatan Guru', 'mapel' => $this->master->getAllMapel(), 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $this->load->view('master/guru/detail');
        $data['id_guru'] = $id_guru;
        $this->load->model('Dashboard_model', 'dashboard');
        $data['guru'] = ['detail' => $this->master->getGuruByArrId([$id_guru])[0], 'jabatan' => $this->master->getDetailJabatanGuru($id_guru), 'materi' => $this->db->get_where('kelas_materi', 'id_guru=' . $id_guru)->num_rows(), 'catatan_mapel' => $this->db->get_where('kelas_catatan_mapel', 'id_guru=' . $id_guru)->num_rows(), 'bank_soal' => $this->db->get_where('cbt_bank_soal', 'bank_guru_id=' . $id_guru)->num_rows(), 'pengawas' => $this->db->get_where('cbt_pengawas', 'id_guru LIKE "%' . $id_guru . '%"')->num_rows(), 'posts' => $this->db->get_where('post', 'dari=' . $id_guru)->num_rows(), 'comments' => $this->db->get_where('post_comments', 'dari=' . $id_guru)->num_rows(), 'replies' => $this->db->get_where('post_reply', 'dari=' . $id_guru)->num_rows()];
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_footer');
    }
    public function delete()
    {
        if (!$chk) {
        }
        $this->output_json(['status' => false]);
        $chk = $this->input->post('checked', true);
        $this->load->model('Master_model', 'master');
        $this->output_json(['status' => true, 'total' => count($chk)]);
        if (!$this->master->delete('master_guru', $chk, 'id_guru')) {
        }
    }
    public function forceDelete()
    {
        $id_guru = $this->input->post('id_guru', true);
        $this->output_json($data);
        $data['status'] = $this->master->delete('master_guru', $id_guru, 'id_guru');
        $this->load->model('Master_model', 'master');
    }
    public function create_user()
    {
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $this->load->model('Master_model', 'master');
        $nama = explode(' ', $data->nama_guru ?? '');
        if ($this->ion_auth->username_check($username)) {
        }
        $data = $this->master->getGuruById($id);
        $email = $data->email;
        $last_name = end($nama);
        $first_name = $nama[0];
        $data = ['status' => true, 'msg' => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'];
        $this->output_json($data);
        $id = $this->input->get('id', true);
        $data = ['status' => false, 'msg' => 'Username tidak tersedia (sudah digunakan).'];
        $data = ['status' => false, 'msg' => 'Email tidak tersedia (sudah digunakan).'];
        $group = array('2');
        $username = $data->nip;
        $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        if ($this->ion_auth->email_check($email)) {
        }
        $password = $data->nip;
    }
    public function import($import_data = null)
    {
        $this->load->model('Master_model', 'master');
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('master/guru/add');
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_footer');
        if (!($import_data != null)) {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['import'] = $import_data;
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Guru', 'subjudul' => 'Tambah Data Guru', 'mapel' => $this->master->getAllMapel(), 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
    }
    public function do_import()
    {
        $input = $this->input->post('guru', true);
        $data = ['status' => false, 'errors' => $errors];
        $save = $this->master->create('master_guru', $data_insert, true);
        foreach ($input as $guru) {
            $errors[] = ['nama' => form_error('2'), 'nip' => form_error('3'), 'username' => form_error('5'), 'password' => form_error('6')];
            $this->form_validation->set_rules('6', 'Password', 'required|trim|min_length[5]|max_length[30]');
            $this->form_validation->set_data($guru);
            $this->form_validation->set_rules('3', 'NIP', 'required|trim|min_length[6]|max_length[30]|is_unique[master_guru.nip]');
            $this->form_validation->set_rules('5', 'Username', 'required|trim|min_length[3]|max_length[30]|is_unique[master_guru.username]');
            $this->form_validation->set_rules('2', 'Nama Guru', 'required|trim|min_length[1]|max_length[50]');
            if (!($this->form_validation->run() == FALSE)) {
            }
        }
        $data = ['status' => true, 'data' => $save, 'insert' => $data_insert];
        $data_insert = [];
        $errors = [];
        foreach ($input as $guru) {
            if (!($extension == 'jpeg')) {
            }
            $extension = 'jpg';
            if (!isset($guru['7'])) {
            }
            $output_file = trim($guru['3'] ?? '00') . '.' . $extension;
            $data_insert[] = ['nama_guru' => trim($guru['2'] ?? ''), 'nip' => trim($guru['3'] ?? ''), 'kode_guru' => trim($guru['4'] ?? ''), 'username' => trim($guru['5'] ?? ''), 'password' => trim($guru['6'] ?? ''), 'foto' => $foto];
            $foto = 'uploads/profiles/' . trim($guru['3'] ?? '00') . '.jpg';
            $foto = 'uploads/profiles/' . $output_file;
            $base64_image_string = $guru['7'];
            file_put_contents('./uploads/profiles/' . $output_file, base64_decode($base64_image_string));
            $extension = $guru['8'];
        }
        if (count($errors) > 0) {
        }
        $this->output_json($data);
        $this->load->model('Master_model', 'master');
    }
    public function editJabatan($id)
    {
        $data['kelass'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $this->load->model('Master_model', 'master');
        $data['guru'] = $guru;
        $data['mapels'] = $this->dropdown->getAllMapel();
        $data['before'] = ['kelass' => $this->dropdown->getAllKelas($tp2, $smt2), 'guru' => $guru_before];
        $guru_before->mapel_kelas = json_decode(json_encode(unserialize($guru_before->mapel_kelas ?? '')));
        $guru = $this->master->getJabatanGuru($id, $tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'judul' => 'Jabatan Guru', 'subjudul' => 'Edit Jabatan Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $smt = $this->dashboard->getSemesterActive();
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $this->load->model('Dropdown_model', 'dropdown');
        $data['tp_active'] = $tp;
        $this->load->view('master/guru/editmapel');
        $tp2 = $smt->id_smt == '1' ? $tp->id_tp - 1 : $tp->id_tp;
        $data['ekskul'] = $this->dropdown->getAllEkskul();
        $guru_before->ekstra_kelas = json_decode(json_encode(unserialize($guru_before->ekstra_kelas ?? '')));
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Dashboard_model', 'dashboard');
        $guru_before = $this->master->getJabatanGuru($id, $tp2, $smt2);
        if (!($group === 'admin')) {
        }
        $data['groups'] = $this->ion_auth->groups()->result();
        $data['kur'] = $smt;
        $data['levels'] = $this->dropdown->getAllLevelGuru();
        $smt2 = $smt->id_smt == '1' ? '2' : '1';
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function saveJabatan()
    {
        $ekstras = [];
        $update = $this->db->replace('jabatan_guru', $data);
        if (!isset($kelass2[$kelasekstra])) {
        }
        $row_mapels = count($this->input->post('mapel', true));
        if (!$check) {
        }
        $nama_mapel = $this->input->post('nama_mapel' . $mapel, true);
        if ($this->input->post()) {
        }
        if (!$check) {
        }
        $tp2 = $smt->id_smt == '1' ? $tp->id_tp - 1 : $tp->id_tp;
        $tmp_nama = $kelass2[$kelasmapel];
        $smt = $this->master->getSemesterActive();
        if (!isset($kelass1[$tmp_nama])) {
        }
        $wali = $this->input->post('kelas_wali', true);
        $kelas = [];
        if ($copy) {
        }
        if (!($i <= $row_ekstras)) {
        }
        $id_guru = $this->input->post('id_guru', true);
        $id_level = $this->input->post('level', true);
        $ekstra = $this->input->post('ekstra[' . $i . ']', true);
        $data = ['id_jabatan_guru' => $id_guru . $tp->id_tp . $smt->id_smt, 'id_guru' => $id_guru, 'id_jabatan' => $id_level, 'id_kelas' => $kelas_wali == null ? 0 : $kelas_wali, 'mapel_kelas' => $kelas_mapel_guru, 'ekstra_kelas' => $kelas_ekstra_guru, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        $kelas[] = ['kelas' => $kelasmapel];
        $kelas_wali = $kelass1[$tmp_wali];
        $kelas = [];
        $check_ekstra = $this->input->post('ekstra', true);
        $check = $this->input->post('kelasekstra' . $ekstra, true);
        $kelas[] = ['kelas' => $kelass1[$tmp_nama]];
        $i = 0;
        $j = 0;
        $mapels[] = ['id_mapel' => $mapel, 'nama_mapel' => $nama_mapel, 'kelas_mapel' => $kelas];
        $this->load->model('Master_model', 'master');
        $tp = $this->master->getTahunActive();
        $check_mapel = $this->input->post('mapel', true);
        $kelasmapel = $this->input->post('kelasmapel' . $mapel . '[' . $j . ']', true);
        $smt2 = $smt->id_smt == '1' ? '2' : '1';
        if (!($j <= $row_kelas)) {
        }
        $kelas[] = ['kelas' => $kelasekstra];
        $kelasekstra = $this->input->post('kelasekstra' . $ekstra . '[' . $j . ']', true);
        $j = 0;
        $copy = $this->input->post('copy', true) != null;
        $j++;
        $tmp_wali = $kelass2[$wali];
        $row_ekstras = count($this->input->post('ekstra', true));
        if (!isset($kelass2[$kelasmapel])) {
        }
        $kelas_ekstra_guru = serialize($ekstras);
        $this->output_json($res);
        $kelass2 = $this->dropdown->getAllKelas($tp2, $smt2);
        $res['msg'] = $update ? 'Data berhasil disimpan' : 'Gagal menyimpan data';
        $row_kelas = count($this->input->post('kelasmapel' . $mapel, true));
        $tmp_nama2 = $kelass2[$kelasekstra];
        $ekstras[] = ['id_ekstra' => $ekstra, 'nama_ekstra' => $nama_ekstra, 'kelas_ekstra' => $kelas];
        $row_kelas = count($this->input->post('kelasekstra' . $ekstra, true));
        if (!$check_ekstra) {
        }
        $mapel = $this->input->post('mapel[' . $i . ']', true);
        $i++;
        $i = 0;
        $nama_ekstra = $this->input->post('nama_ekstra' . $ekstra, true);
        $mapels = [];
        $i++;
        if (!($j <= $row_kelas)) {
        }
        if ($copy) {
        }
        $this->load->model('Kelas_model', 'kelas');
        $kelas[] = ['kelas' => $kelass1[$tmp_nama2]];
        $kelass1 = $this->kelas->getNamaKelasByNama($tp->id_tp, $smt->id_smt);
        if (!$check_mapel) {
        }
        $check = $this->input->post('kelasmapel' . $mapel, true);
        $this->load->model('Dropdown_model', 'dropdown');
        if (!($i <= $row_mapels)) {
        }
        $res['status'] = FALSE;
        $kelas_mapel_guru = serialize($mapels);
        $res['msg'] = 'Error post data';
        $j++;
        if ($copy) {
        }
        $res['status'] = $update;
        $kelas_wali = $wali;
    }
    public function getDataKelas()
    {
        $data['mpl_terisi'] = $mapel_terisi;
        $tp = $this->dashboard->getTahunActive();
        $jbtn = [];
        $data['jabatan'] = $jbtn;
        $this->load->model('Master_model', 'master');
        $mapel_terisi = [];
        foreach ($jabatans as $jabatan) {
            $mpl_kls = $jabatan->mapel_kelas = json_decode(json_encode(unserialize($jabatan->mapel_kelas ?? '')));
            $jbtn[$jabatan->id_jabatan][$jabatan->id_kelas] = ['nama' => $jabatan->nama_guru, 'id' => $jabatan->id_guru];
            foreach ($mpl_kls as $mpls) {
                foreach ($mpls->kelas_mapel as $mpl) {
                    $klss[] = $mpl->kelas;
                }
                $klss = [];
                $mapel_terisi[$mpls->id_mapel][$jabatan->id_guru] = ['id_guru' => $jabatan->id_guru, 'guru' => $jabatan->nama_guru, 'kelas' => $klss];
            }
            $eks_kls = $jabatan->ekstra_kelas = json_decode(json_encode(unserialize($jabatan->ekstra_kelas ?? '')));
            foreach ($eks_kls as $eks) {
                $ekstra_terisi[$eks->id_ekstra][$jabatan->id_guru] = ['id_guru' => $jabatan->id_guru, 'guru' => $jabatan->nama_guru, 'kelas' => $klse];
                $klse = [];
                foreach ($eks->kelas_ekstra as $ek) {
                    $klse[] = $ek->kelas;
                }
            }
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($data);
        $data['eks_terisi'] = $ekstra_terisi;
        $jabatans = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        $ekstra_terisi = [];
        $this->load->model('Users_model', 'users');
        $data['kelas'] = $this->users->getKelas($tp->id_tp, $smt->id_smt);
    }
    public function addjabatan()
    {
        $insert = ['id_level' => $id, 'level' => $this->input->post('level', true)];
        $mode = $this->input->post('mode', true);
        $data = ['success' => $replaced, 'msg' => $replaced ? 'Sukses ' . $s_mode . ' jabatan' : 'Gagal ' . $s_mode . ' jabatan'];
        $replaced = $this->db->replace('level_guru', $insert);
        $id = $this->input->post('id_level', true);
        $s_mode = $mode == '1' ? 'menyimpan' : 'menghapus';
        $replaced = $this->db->delete('level_guru', 'id_level=' . $id);
        if ($mode == '1') {
        }
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_decoded/Datajurusan.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datajurusan extends CI_Controller
{
    public function __construct()
    {
        if (!$this->ion_auth->logged_in()) {
        }
        redirect('auth');
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Dashboard_model', 'dashboard');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
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
        $arr_kode = [];
        $jurusans = $this->master->getDataJurusan();
        $data['jurusans'] = $jurusans;
        $data['mapel_peminatan'] = $this->dropdown->getMapelPeminatan($arr_kode);
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        foreach ($kode_peminatan as $kode) {
            $arr_kode[] = $kode->kode_kel_mapel;
        }
        $data = ['user' => $user, 'judul' => 'Jurusan', 'subjudul' => 'Daftar Jurusan', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        foreach ($jurusans as $jurusan) {
            $jurusan_mapels[$jurusan->id_jurusan] = $this->master->getDataJurusanMapel(explode(',', $jurusan->mapel_peminatan ?? ''));
        }
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $data['jurusan_mapels'] = $jurusan_mapels;
        $this->load->view('_templates/dashboard/_footer');
        $jurusan_mapels = [];
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('master/jurusan/data');
        $this->load->view('_templates/dashboard/_header', $data);
        $kode_peminatan = $this->dropdown->getAllKodePeminatan();
        $data['kode_peminatan'] = $kode_peminatan;
    }
    public function add()
    {
        $i++;
        array_push($mapels, $this->input->post('mapel[' . $i . ']', true));
        $mapels = [];
        $this->master->create('master_jurusan', $insert, false);
        if (!$check_mapel) {
        }
        $check_mapel = $this->input->post('mapel', true);
        $insert = ['nama_jurusan' => $this->input->post('nama_jurusan', true), 'kode_jurusan' => $this->input->post('kode_jurusan', true), 'mapel_peminatan' => implode(',', $mapels)];
        $row_mapels = count($this->input->post('mapel', true));
        $data['status'] = $insert;
        if (!($i <= $row_mapels)) {
        }
        $this->output_json($data);
        $i = 0;
    }
    public function data()
    {
        $this->output_json($this->master->getDataTableJurusan(), false);
    }
    public function save()
    {
        $data['insert'] = $insert;
        $status = FALSE;
        $this->form_validation->set_rules($nama_jurusan, 'Jurusan', 'required');
        $nama_jurusan = 'nama_jurusan[' . $i . ']';
        $i = 1;
        $this->form_validation->set_message('required', '{field} Wajib diisi');
        $data['status'] = $status;
        if ($mode == 'add') {
        }
        if ($this->form_validation->run() === FALSE) {
        }
        $i++;
        if (!($mode == 'edit')) {
        }
        if (!($i <= $rows)) {
        }
        $this->master->create('master_jurusan', $insert, true);
        $data['errors'] = $error;
        $this->master->update('master_jurusan', $update, 'id_jurusan', null, true);
        if ($status) {
        }
        if ($mode == 'add') {
        }
        $this->output_json($data);
        $update[] = array('id_jurusan' => $this->input->post('id_jurusan[' . $i . ']', true), 'nama_jurusan' => $this->input->post($nama_jurusan, true));
        $error[] = [$nama_jurusan => form_error($nama_jurusan)];
        $status = TRUE;
        $insert[] = ['nama_jurusan' => $this->input->post($nama_jurusan, true)];
        $data['update'] = $update;
        if (!($mode == 'edit')) {
        }
        if (!isset($error)) {
        }
        $rows = count($this->input->post('nama_jurusan', true));
        $mode = $this->input->post('mode', true);
    }
    public function update()
    {
        $data = $this->master->updateJurusan();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        $this->output_json(['status' => true, 'total' => count($chk)]);
        foreach ($tables as $table) {
            $num = $this->db->count_all_results($table);
            if (!($table != 'master_jurusan')) {
            }
            if (!($num > 0)) {
            }
            array_push($messages, $table);
            if ($table == 'master_kelas') {
            }
            $this->db->where_in('jurusan_id', $chk);
            $num = $this->db->count_all_results($table);
            $this->db->where_in('id_jurusan', $chk);
        }
        $messages = [];
        if (!$chk) {
        }
        $tables = [];
        if (count($messages) > 0) {
        }
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_jurusan' || $field->name == 'jurusan_id')) {
                }
                array_push($tables, $table);
            }
        }
        $this->output_json(['status' => false, 'total' => 'Data Jurusan digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        $tabless = $this->db->list_tables();
        $this->output_json(['status' => false, 'total' => 'Tidak ada data yang dipilih!']);
        if (!$this->master->delete('master_jurusan', $chk, 'id_jurusan')) {
        }
    }
    public function load_jurusan()
    {
        $data = $this->master->getJurusan();
        $this->output_json($data);
    }
    public function import($import_data = null)
    {
        $data = ['user' => $user, 'judul' => 'Import Jurusan', 'subjudul' => 'Import Jurusan', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
        if (!($import_data != null)) {
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_footer');
        $user = $this->ion_auth->user()->row();
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $data['import'] = $import_data;
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('master/jurusan/import');
    }
    public function do_import()
    {
        foreach ($data as $j) {
            $jurusan[] = ['nama_jurusan' => $j->nama, 'kode_jurusan' => $j->kode];
        }
        $save = $this->master->create('master_jurusan', $jurusan, true);
        $jurusan = [];
        $this->output->set_content_type('application/json')->set_output($save);
        $data = json_decode($this->input->post('jurusan', true));
    }
    function updateById()
    {
        $this->db->set('kode_jurusan', $kode);
        $nama = $this->input->post('username', true);
        return $this->db->update('master_jurusan');
        $this->db->set('nama_jurusan', $nama);
        $kode = $this->input->post('email', true);
        $this->db->where('id_jurusan', $id);
        $id = $this->input->post('id_jurusan');
    }
    public function hapusById()
    {
        $id = $this->input->post('id');
        $this->db->where('id_jurusan', $id);
        return $this->db->delete('master_jurusan');
    }
    function exist($table, $data)
    {
        return false;
        $count = $query->num_rows();
        $query = $this->db->get_where($table, $data);
        return true;
        if ($count === 0) {
        }
    }
}
```

---

## File: application/controllers_decoded/Datakelas.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datakelas extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model', 'kelas');
        if (!$this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Rapor_model', 'rapor');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library(['datatables', 'form_validation']);
        parent::__construct();
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $data['jurusan'] = $this->kelas->get_jurusan();
        $kelas_lama = $this->kelas->getKelasList($tp->id_tp - 1, '2');
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('master/kelas/data');
        $tp = $this->dashboard->getTahunActive();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['guru'] = $this->kelas->get_guru();
        $data = ['user' => $user, 'judul' => 'Kelas', 'subjudul' => 'Data Kelas', 'setting' => $setting];
        $kelas_lama = [];
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $this->load->view('_templates/dashboard/_footer');
        $setting = $this->dashboard->getSetting();
        $chek = $this->kelas->count_all();
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['siswa'] = $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $kelas = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $kelas;
        $data['kelas_lama'] = $kelas_lama;
        if (!($chek > 0)) {
        }
        $kelas = [];
        $data['smt_active'] = $smt;
    }
    public function detail($id)
    {
        $data['struktur'] = json_decode(json_encode($this->kelas->dummyStruktur()));
        $smt = $this->dashboard->getSemesterActive();
        $data = ['user' => $user, 'judul' => 'Detail Kelas', 'subjudul' => 'Detail Kelas', 'setting' => $setting];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        if ($struktur == null) {
        }
        $data['tp_active'] = $tp;
        $tp = $this->dashboard->getTahunActive();
        $data['jurusan'] = $this->kelas->get_jurusan();
        $data['kelas'] = $this->kelas->get_one($id);
        $data['guru'] = $this->kelas->get_guru();
        $setting = $this->dashboard->getSetting();
        $user = $this->ion_auth->user()->row();
        $data['siswas'] = $this->kelas->get_siswa_kelas($id, $tp->id_tp, $smt->id_smt);
        $data['struktur'] = $struktur;
        $struktur = $this->kelas->getStrukturKelas($id);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/detail');
        $this->load->view('_templates/dashboard/_footer');
        $data['smt_active'] = $smt;
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function add()
    {
        $data['siswakelas'] = array();
        $smt = $this->dashboard->getSemesterActive();
        $data['kelas'] = json_decode(json_encode($this->kelas->dummy()));
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $setting = $this->dashboard->getSetting();
        $siswa = $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $data['siswa'] = $siswa;
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $tp = $this->dashboard->getTahunActive();
        $data['guru'] = $this->kelas->get_guru();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt_active'] = $smt;
        $data = ['user' => $user, 'judul' => 'Kelas', 'subjudul' => 'Tambah Kelas', 'setting' => $setting];
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['jurusan'] = $this->kelas->get_jurusan();
        $this->load->view('master/kelas/add');
    }
    public function edit($id = '')
    {
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('master/kelas/add');
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Kelas', 'subjudul' => 'Edit Kelas', 'setting' => $setting];
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $data['jurusan'] = $this->kelas->get_jurusan();
        $data['siswakelas'] = $this->kelas->get_siswa_kelas($id, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();
        $data['tp'] = $this->dashboard->getTahun();
        $data['siswa'] = $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['guru'] = $this->kelas->getWaliKelas($tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = $id;
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['kelas'] = $this->kelas->get_one($id);
    }
    public function save()
    {
        array_push($siswakelas, ['id' => $id_siswa]);
        if (!($i <= count($siswas))) {
        }
        $status = FALSE;
        if ($this->form_validation->run() == TRUE) {
        }
        $this->db->where('id_kelas', $id);
        $id_new = null;
        $jumlah = serialize($siswakelas);
        $config = array(array('field' => 'nama_kelas', 'label' => 'Nama Kelas', 'rules' => 'trim'), array('field' => 'kode_kelas', 'label' => 'Kode Kelas', 'rules' => 'trim'), array('field' => 'jurusan_id', 'label' => 'Jurusan', 'rules' => 'trim'), array('field' => 'level_id', 'label' => 'Level', 'rules' => 'trim'), array('field' => 'guru_id', 'label' => 'Guru', 'rules' => 'trim'), array('field' => 'siswa_id', 'label' => 'Siswa', 'rules' => 'trim'));
        $siswakelas = [];
        foreach ($insert as $ins) {
            if (!$this->db->replace('kelas_siswa', $ins)) {
            }
            $siswa_inserted++;
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        $idsiswa = isset($siswas[$i]) ? $siswas[$i] : null;
        $data['update'] = $updated;
        $status = FALSE;
        $id = $this->input->post('id_kelas', true);
        if (!($i <= count($siswas))) {
        }
        if (!(count($siswa_kelas) > 0)) {
        }
        $insert[$id_tp . $id_smt . $idsiswa] = ['id_kelas_siswa' => $id_tp . $id_smt . $idsiswa, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $new_id_kelas, 'id_siswa' => $idsiswa];
        foreach ($siswa_kelas as $id_siswa => $sis) {
            $insert[$id_tp . $id_smt . $id_siswa] = ['id_kelas_siswa' => $id_tp . $id_smt . $id_siswa, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => 0, 'id_siswa' => $id_siswa];
        }
        if (!($idsiswa != null)) {
        }
        $this->output_json($data);
        $siswa_kelas = $this->kelas->get_status_siswa_kelas($id, $id_tp, $id_smt);
        $status = $this->db->update('master_kelas', $insert);
        if (isset($insert[$id_tp . $id_smt . $idsiswa])) {
        }
        $this->form_validation->set_rules($config);
        $siswas = $this->input->post('siswa', true);
        $data['insert'] = $insert;
        $status = $this->db->insert('master_kelas', $insert);
        if (!($id_siswa != null)) {
        }
        $updated = false;
        $this->db->where('id_jabatan_guru', $guru_id . $id_tp . $id_smt);
        $i = 0;
        $siswa_inserted = 0;
        $updated = $this->db->update('jabatan_guru');
        $insert = array('nama_kelas' => $this->input->post('nama_kelas', TRUE), 'kode_kelas' => $this->input->post('kode_kelas', TRUE), 'jurusan_id' => $this->input->post('jurusan_id', TRUE), 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'level_id' => $this->input->post('level_id', TRUE), 'guru_id' => $this->input->post('guru_id', TRUE) ?? '', 'siswa_id' => $this->input->post('siswa_id', TRUE), 'jumlah_siswa' => $jumlah);
        if ($this->form_validation->run() == TRUE) {
        }
        if (!$updated) {
        }
        $insert = [];
        if (!$status) {
        }
        $this->db->set('id_kelas', $id);
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $id_tp = $this->master->getTahunActive()->id_tp;
        $i = 0;
        $insert[$id_tp . $id_smt . $idsiswa]['id_kelas'] = $new_id_kelas;
        $i++;
        $new_id_kelas = $id != null && $id != '' ? $id : $id_new;
        $guru_id = $this->input->post('guru_id', TRUE);
        $id_siswa = isset($siswas[$i]) ? $siswas[$i] : null;
        $data['status'] = $status;
        if ($id != null && $id != '') {
        }
        $this->form_validation->set_rules($config);
        if (!($id != null && $id != '')) {
        }
        $data['siswa'] = $siswa_inserted;
        $i++;
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $id_new = $this->db->insert_id();
    }
    public function update_kelas($id)
    {
        $insert = ['id_kelas_siswa' => $id_tp . $id_smt . $id_siswa, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $id, 'id_siswa' => $id_siswa];
        if (!($i <= $rowsSelect)) {
        }
        $rowsSelect = count($this->input->post('siswa', true));
        return $siswakelas;
        $i = 0;
        $i++;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        foreach ($siswakelas as $id_siswa => $sis) {
            $insert = ['id_kelas_siswa' => $id_tp . $id_smt . $id_siswa, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => 0, 'id_siswa' => $id_siswa];
            $this->db->replace('kelas_siswa', $insert);
        }
        $id_siswa = $this->input->post('siswa[' . $i . ']', true);
        if (!(count($siswakelas) > 0)) {
        }
        $siswakelas = $this->kelas->get_status_siswa_kelas($id, $id_tp, $id_smt);
        if (!($id_siswa != null)) {
        }
        $this->db->replace('kelas_siswa', $insert);
        $id_tp = $this->master->getTahunActive()->id_tp;
    }
    public function manage()
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['smt_active'] = $smt;
        $data['kelas2'] = $this->dropdown->getAllKelas($tp->id_tp, '2');
        $this->load->view('master/kelas/persemester');
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, '1');
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'judul' => 'Copy Kelas', 'subjudul' => 'Copy Data Kelas ke SMT II', 'setting' => $this->dashboard->getSetting()];
    }
    public function getFromSmt1($kelas)
    {
        $data2 = $this->kelas->getKelasSiswa($kelas, $tp->id_tp, '2');
        $data1 = $this->kelas->getKelasSiswa($kelas, $tp->id_tp, '1');
        if (!(count($data2) > 0)) {
        }
        $this->output_json(['smt1' => $data1, 'smt2' => $ids]);
        foreach ($data2 as $s) {
            $ids[] = $s->id_siswa;
        }
        $ids = [];
        $tp = $this->dashboard->getTahunActive();
    }
    public function copyFromSmt1()
    {
        $arrSiswa = unserialize($kelas->jumlah_siswa);
        $smt = $this->dashboard->getSemesterActive();
        $kelas2 = $this->input->post('kelas_baru', true);
        $tp = $this->dashboard->getTahunActive();
        foreach ($arrSiswa as $value) {
            $res[] = $this->db->replace('kelas_siswa', $insert);
            if (!($id_siswa != null)) {
            }
            $id_siswa = $value['id'];
            $insert = ['id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $id_siswa];
        }
        $res = [];
        $data = array('nama_kelas' => $kelas2, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $kelas->jumlah_siswa);
        $idk = $this->db->insert_id();
        $kelas1 = $this->input->post('kelas_lama', true);
        $this->db->insert('master_kelas', $data);
        $this->output_json($res);
        $kelas = $this->kelas->get_one($kelas1, $tp->id_tp, '1');
    }
    public function copySiswaFromSmt1()
    {
        $idkelases = [];
        foreach ($idkelases as $ik) {
            $jumlah = serialize($siswakelas[$ik]);
            $this->db->insert('master_kelas', $data);
            $idk = $this->db->insert_id();
            if (!($ik != '')) {
            }
            $kelas = $this->kelas->get_one($ik, $tp->id_tp, '1');
            $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $jumlah);
            foreach ($siswakelas[$ik] as $s) {
                $insert = ['id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $s['id'], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $s['id']];
                $res[] = $this->db->replace('kelas_siswa', $insert);
            }
        }
        $idkelases = array_unique($idkelases);
        foreach ($posts as $d) {
            $siswakelas[$d->id_kelas][] = ['id' => $d->id_siswa];
            $idkelases[] = $d->id_kelas;
        }
        $posts = json_decode($this->input->post('kelas', true));
        $res = [];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswakelas = [];
        $this->output_json($res);
    }
    public function kenaikan()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $data['kelas_selected'] = $kelas;
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('master/kelas/naikkelas');
        $lvlKls = $this->kelas->get_one($kelas, $tp->id_tp - 1, '2');
        $setting = $this->dashboard->getSetting();
        $data['kelas_lama'] = $this->dropdown->getAllKelas($tp->id_tp - 1, '2', '!=' . $level);
        $tp = $this->dashboard->getTahunActive();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['siswas'] = $this->rapor->getKenaikanSiswa($kelas, $tp->id_tp - 1, '2');
        $data['smt_active'] = $smt;
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        if (!($kelas != null)) {
        }
        $this->load->view('_templates/dashboard/_footer');
        $data = ['user' => $user, 'judul' => 'Kenaikkan Kelas', 'subjudul' => 'Naik Kelas Siswa', 'setting' => $setting];
        $kelas = $this->input->get('kelas', true);
        $data['tp_active'] = $tp;
        $data['kelas_baru'] = $this->dropdown->getAllKelas($tp->id_tp, '1');
        $data['kelases'] = $this->dropdown->getAllKelas($tp->id_tp - 1, '2', '=' . ($lvlKls->level_id + 1));
        $data['siswa_kelas_baru'] = $this->master->getSiswaKelasBaru($tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function naikKelas()
    {
        $mode = $this->input->post('mode', true);
        foreach ($idkelases as $ik) {
            $kelas_baru = $this->kelas->getKelasByNama($kelas->nama_kelas, $tp->id_tp, $smt->id_smt);
            $this->db->insert('master_kelas', $data);
            $this->db->update('master_kelas', $data);
            foreach ($siswakelas[$ik] as $s) {
                foreach ($jmlLama as $lama) {
                    if (!($lama['id'] != $s['id'])) {
                    }
                    array_push($idks, $kelas_baru->id_kelas);
                    array_push($jmlLama, ['id' => $s['id']]);
                }
            }
            if ($kelas_baru == null) {
            }
            $jumlah = serialize($siswakelas[$ik]);
            $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $jumlah);
            array_push($idks, $kelas_baru->id_kelas);
            $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $jumlah);
            foreach ($idks as $idk) {
                foreach ($siswakelas[$ik] as $s) {
                    $insert = ['id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $s['id'], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $s['id']];
                    $res[] = $this->db->replace('kelas_siswa', $insert);
                }
            }
            if ($mode == 'persiswa') {
            }
            $this->db->where('id_kelas', $kelas_baru->id_kelas);
            $kelas = $this->kelas->get_one($ik, $tp->id_tp - 1, '2');
            $jumlah = serialize($jmlLama);
            $jumlah = serialize($siswakelas[$ik]);
            array_push($idks, $this->db->insert_id());
            $jmlLama = unserialize($kelas_baru->jumlah_siswa);
        }
        $siswakelas = [];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $idkelases = array_unique($idkelases);
        $data['res'] = $siswakelas;
        $this->output_json($data);
        $idkelases = [];
        $idks = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->kelas_baru;
            $siswakelas[$d->kelas_baru][] = ['id' => $d->id_siswa];
        }
        $res = [];
    }
    public function hapus($id_kelas)
    {
        $delete['kelas'] = $this->master->delete('master_kelas', $id_kelas, 'id_kelas');
        $delete['siswa'] = $this->master->delete('kelas_siswa', $id_kelas, 'id_kelas');
        $this->output_json($delete);
    }
}
```

---

## File: application/controllers_decoded/Datamapel.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datamapel extends CI_Controller
{
    public function __construct()
    {
        $this->load->dbforge();
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Master_model', 'master');
        redirect('auth');
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    private function updateUrutanTampil()
    {
        $mapels = $this->db->select('*')->from('master_mapel')->get()->result();
        $insert = [];
        $this->db->update_batch('master_mapel', $insert);
        foreach ($mapels as $mapel) {
            $insert = ['id_mapel' => $mapel->id_mapel, 'nama_mapel' => $mapel->id_mapel, 'kode' => $mapel->id_mapel, 'kelompok' => $mapel->id_mapel, 'bobot_p' => $mapel->id_mapel, 'bobot_k' => $mapel->id_mapel, 'jenjang' => $mapel->id_mapel, 'urutan' => $mapel->id_mapel, 'urutan_tampil' => $mapel->id_mapel, 'status' => $mapel->id_mapel, 'deletable' => $mapel->id_mapel];
        }
        if (!(count($insert) > 0)) {
        }
    }
    public function index()
    {
        $data['kelompok'] = $this->dropdown->getDataKelompokMapel();
        $data['smt'] = $this->dashboard->getSemester();
        $data['sub_kelompok_mapel'] = $this->master->getDataSubKelompokMapel();
        $this->load->view('_templates/dashboard/_footer');
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        $this->load->view('_templates/dashboard/_header', $data);
        if ($this->db->field_exists('urutan_tampil', 'master_mapel')) {
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->dbforge->add_column('master_mapel', $fields);
        $this->load->view('master/mapel/data');
        $data['kategori'] = ['WAJIB', 'PAI (Kemenag)', 'PEMINATAN AKADEMIK', 'AKADEMIK KEJURUAN', 'LINTAS MINAT', 'MULOK'];
        $setting = $this->dashboard->getSetting();
        $fields = array('urutan_tampil' => array('type' => 'int(3)', 'after' => 'urutan'));
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Daftar Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['status'] = ['Nonaktif', 'Aktif'];
        $data['kelompok_mapel'] = $this->master->getDataKelompokMapel();
        $data['mapel_non_aktif'] = $this->master->getAllMapelNonAktif($setting->jenjang);
        $data['smt_active'] = $this->dashboard->getSemesterActive();
    }
    public function addKelompokMapel()
    {
        $this->db->where('id_kel_mapel', $id);
        $data = $this->master->create('master_kelompok_mapel', $insert);
        $this->output->set_content_type('application/json')->set_output($data);
        if ($id != null) {
        }
        $id = $this->input->post('id_kel_mapel');
        $data = $this->db->update('master_kelompok_mapel', $insert);
        $insert = ['nama_kel_mapel' => $this->input->post('nama_kel_mapel', true), 'kode_kel_mapel' => $this->input->post('kode_kel_mapel', true), 'kategori' => $this->input->post('kategori', true), 'id_parent' => $this->input->post('id_parent', true)];
    }
    public function hapusKelompok()
    {
        $this->output_json(['status' => true, 'message' => 'berhasil']);
        $messages = [];
        $nums = $this->db->count_all_results('master_kelompok_mapel');
        if (count($messages) > 0) {
        }
        if (!($nums > 0)) {
        }
        array_push($messages, 'Mata Pelajaran');
        $this->output_json(['status' => false, 'message' => 'Kelompok Mapel digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        $kode = $this->input->post('kode');
        array_push($messages, 'Sub Kelompok');
        if (!$this->master->delete('master_kelompok_mapel', $id, 'id_kel_mapel')) {
        }
        $this->db->where_in('kelompok', $kode);
        $id_parent = $this->input->post('id_parent');
        if (!($numm > 0)) {
        }
        $numm = $this->db->count_all_results('master_mapel');
        $id = $this->input->post('id_kel');
        $this->db->where_in('id_parent', $id);
    }
    public function create()
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = $this->master->create('master_mapel', $insert);
        $setting = $this->dashboard->getSetting();
        $insert = ['nama_mapel' => $this->input->post('nama_mapel', true), 'kode' => $this->input->post('kode_mapel', true), 'kelompok' => $this->input->post('kelompok', true), 'urutan_tampil' => $this->input->post('urutan_tampil', true), 'jenjang' => $setting->jenjang];
    }
    public function getDataKelompok()
    {
        echo $this->datatables->generate();
        $this->datatables->where('id_parent', '0');
        $this->datatables->select('*');
        $this->db->order_by('kode_kel_mapel');
        $this->datatables->from('master_kelompok_mapel');
    }
    public function getDataSubKelompok()
    {
        echo $this->datatables->generate();
        $this->datatables->select('*');
        $this->datatables->from('master_kelompok_mapel');
        $this->datatables->where('id_parent <> 0');
        $this->db->order_by('kode_kel_mapel');
    }
    public function read()
    {
        $this->datatables->from('master_mapel');
        $this->db->order_by('urutan_tampil');
        $this->datatables->select('id_mapel, urutan_tampil, nama_mapel, kode, kelompok, deletable, status');
        $setting = $this->dashboard->getSetting();
        $this->db->order_by('kelompok');
        echo $this->datatables->generate();
    }
    public function update()
    {
        $data = $this->master->updateMapel();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function aktifkan($id)
    {
        $this->db->where('id_mapel', $id);
        $this->db->set('status', '1');
        $this->output_json($update);
        $update = $this->db->update('master_mapel');
    }
    public function delete()
    {
        $this->output_json(['status' => false, 'total' => 'Tidak ada data yang dipilih!']);
        if (!$this->master->delete('master_mapel', $chk, 'id_mapel')) {
        }
        $tables = [];
        if (!$chk) {
        }
        foreach ($tables as $table) {
            $this->db->where_in('mapel_id', $chk);
            $this->db->where_in('id_mapel', $chk);
            array_push($messages, $table);
            if ($table == 'cbt_soal') {
            }
            if (!($table != 'master_mapel')) {
            }
            if (!($num > 0)) {
            }
            $num = $this->db->count_all_results($table);
            $num = $this->db->count_all_results($table);
        }
        $chk = $this->input->post('checked', true);
        $this->output_json(['status' => true, 'total' => count($chk)]);
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_mapel' || $field->name == 'mapel_id')) {
                }
                array_push($tables, $table);
            }
        }
        if (count($messages) > 0) {
        }
        $this->output_json(['status' => false, 'total' => 'Mapel digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        $messages = [];
        $tabless = $this->db->list_tables();
    }
    public function import($import_data = null)
    {
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('master/mapel/import');
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Import Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if (!($import_data != null)) {
        }
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $data['import'] = $import_data;
    }
    public function do_import()
    {
        $this->output->set_content_type('application/json')->set_output($save);
        $inputs = $this->input->post('mapel', true);
        $save = $this->master->create('master_mapel', $inputs, true);
    }
}
```

---

## File: application/controllers_decoded/Datasiswa.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datasiswa extends CI_Controller
{
    public function __construct()
    {
        redirect('auth');
        parent::__construct();
        $this->form_validation->set_error_delimiters('', '');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library('upload');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_header', $data);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $searchTp = array_search('1', array_column($tp, 'active'));
        $tpAktif = $tp[$searchTp];
        $smt = $this->dashboard->getSemester();
        $data['tp'] = $tp;
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->view('master/siswa/data');
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Data Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_footer');
        $tp = $this->dashboard->getTahun();
        $data['tp_active'] = $tpAktif;
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $smt;
        $smtAktif = $smt[$searchSmt];
        $data['smt_active'] = $smtAktif;
        $data['kelass'] = $this->dropdown->getAllKelas($tpAktif->id_tp, $smtAktif->id_smt);
        $this->load->model('Dashboard_model', 'dashboard');
        $searchSmt = array_search('1', array_column($smt, 'active'));
    }
    public function data()
    {
        $smt = $this->dashboard->getSemesterActive();
        $tp = $this->dashboard->getTahunActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        $this->output_json($this->master->getDataSiswa($tp->id_tp, $smt->id_smt), false);
    }
    public function list()
    {
        $this->output_json($data);
        $tp = $this->dashboard->getTahunActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $offset = ($page - 1) * $limit;
        $lists = $this->master->getSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $filter, $search);
        $smt = $this->dashboard->getSemesterActive();
        $count_siswa = $this->master->getSiswaTotalPage($tp->id_tp, $smt->id_smt, $filter, $search);
        $filter = $this->input->post('filter', true);
        $search = $this->input->post('search', true);
        $page = $this->input->post('page', true);
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit, 'filter' => $filter];
        $this->load->model('Master_model', 'master');
        $limit = $this->input->post('limit', true);
    }
    public function add()
    {
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_footer');
        $this->load->model('Dashboard_model', 'dashboard');
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/siswa/add');
        $data['tipe'] = 'add';
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Tambah Data Siswa', 'setting' => $this->dashboard->getSetting()];
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
    }
    public function create()
    {
        $this->output_json($data);
        $id = $this->db->insert_id();
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $data['insert'] = false;
        if ($this->form_validation->run() == FALSE) {
        }
        $nisn = $this->input->post('nisn', true);
        $u_name = '|is_unique[master_siswa.username]';
        $this->db->insert('buku_induk', $induk);
        $induk = ['id_siswa' => $id, 'uid' => $siswa->uid, 'status' => 1];
        $nis = $this->input->post('nis', true);
        $data['insert'] = $this->db->insert('master_siswa', $insert);
        $data['text'] = 'Siswa berhasil ditambahkan';
        $u_nisn = '|is_unique[master_siswa.nisn]';
        $this->db->set('uid', 'UUID()', FALSE);
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        $username = $this->input->post('username', true);
        $data['text'] = 'Data Sudah ada, Pastikan NIS, NISN dan Username belum digunakan siswa lain';
        $u_nis = '|is_unique[master_siswa.nis]';
        $this->load->model('Master_model', 'master');
        $insert = ['nama' => $this->input->post('nama_siswa', true), 'nis' => $nis, 'nisn' => $nisn, 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'username' => $username, 'password' => $this->input->post('password', true), 'foto' => 'uploads/foto_siswa/' . $nis . 'jpg'];
        $siswa = $this->master->getSiswaById($id);
        $this->form_validation->set_rules('username', 'Username', 'required|trim' . $u_name);
    }
    public function edit($id)
    {
        $data['tp_active'] = $tp;
        $smt = $this->master->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/siswa/edit');
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['smt_active'] = $smt;
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Edit Data Siswa', 'siswa' => $siswa, 'setting' => $this->dashboard->getSetting()];
        $this->load->view('master/siswa/edit');
        $user = $this->ion_auth->user()->row();
        $inputBio = [['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $siswa->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $siswa->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $siswa->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $siswa->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $siswa->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $siswa->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $siswa->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $siswa->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $siswa->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $siswa->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $siswa->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $tp = $this->master->getTahunActive();
        $inputOrtu = [['name' => 'status_keluarga', 'label' => 'Status Keluarga', 'value' => $siswa->status_keluarga, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'anak_ke', 'label' => 'Anak ke', 'value' => $siswa->anak_ke, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $siswa->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $siswa->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $siswa->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $siswa->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $siswa->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $siswa->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $siswa->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $siswa->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number']];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->model('Master_model', 'master');
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $this->load->view('members/guru/templates/footer');
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('members/guru/templates/header', $data);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $siswa->nama, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $siswa->nis, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $siswa->nisn, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $siswa->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $siswa->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $siswa->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'class' => 'tahun', 'type' => 'text'], ['name' => 'sekolah_asal', 'label' => 'Sekolah Asal', 'value' => $siswa->sekolah_asal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'status', 'label' => 'Status', 'value' => $siswa->status, 'icon' => 'far fa-user', 'class' => 'status', 'type' => 'text']];
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $siswa->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $siswa->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $siswa->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $siswa->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $siswa = $this->master->getSiswaById($id);
        if ($this->ion_auth->is_admin()) {
        }
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
    }
    public function updateData()
    {
        $tgl_lahir = $this->input->post('tanggal_lahir', true);
        $this->output_json($data);
        $data['insert'] = $input;
        if ($this->form_validation->run() == FALSE) {
        }
        $tgl_masuk = $this->input->post('tahun_masuk', true);
        $u_nisn = $siswa->nisn === $nisn ? '' : '|is_unique[master_siswa.nisn]';
        $data['insert'] = false;
        $this->db->update('buku_induk');
        $nisn = $this->input->post('nisn', true);
        $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->strContains($tgl_lahir, '0000-') ? null : $tgl_lahir, 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->strContains($tgl_masuk, '0000-') ? null : $tgl_masuk, 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => $siswa->foto != null && $siswa->foto != '' ? $siswa->foto : 'uploads/foto_siswa/' . $nis . '.jpg'];
        $data['text'] = 'Siswa berhasil diperbaharui';
        $this->db->where('id_siswa', $siswa->id_siswa);
        $nis = $this->input->post('nis', true);
        $siswa = $this->master->getSiswaById($id_siswa);
        $id_siswa = $this->input->post('id_siswa', true);
        $this->db->set('status', $this->input->post('status', true));
        $u_nis = $siswa->nis === $nis ? '' : '|is_unique[master_siswa.nis]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $this->load->model('Master_model', 'master');
        $data['text'] = 'NIS kurang dari 6 angka, atau data Sudah ada, Pastikan NIS, dan NISN belum digunakan siswa lain';
        $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
    }
    function strContains($string, $val)
    {
        return strpos($string, $val) !== false;
    }
    function uploadFile($id_siswa)
    {
        $data['src'] = '';
        $siswa = $this->master->getSiswaById($id_siswa);
        $this->upload->initialize($config);
        $result = $this->upload->data();
        $config['overwrite'] = true;
        $data['type'] = $_FILES['foto']['type'];
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        if (!$this->upload->do_upload('foto')) {
        }
        $config['upload_path'] = './uploads/foto_siswa/';
        $data['src'] = $this->upload->display_errors();
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
        $this->output_json($data);
        $data['size'] = $_FILES['foto']['size'];
        if (isset($_FILES['foto']['name'])) {
        }
        $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
        $this->db->where('id_siswa', $id_siswa);
        $data['status'] = true;
        $data['status'] = false;
        $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
        $this->load->model('Master_model', 'master');
        $config['file_name'] = $siswa->nis;
        $this->db->update('master_siswa');
    }
    function deleteFile($id_siswa)
    {
        $file_name = str_replace(base_url(), '', $src ?? '');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->set('foto', '');
        $this->db->update('master_siswa');
        if (!($file_name != 'assets/img/siswa.png')) {
        }
        echo 'File Delete Successfully';
        if (!unlink($file_name)) {
        }
        $src = $this->input->post('src');
    }
    public function delete()
    {
        foreach ($chk as $id) {
            $this->db->update('buku_induk');
            $this->db->where('id_siswa', $id);
            $this->db->set('status', '3');
        }
        if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
        }
        $this->master->delete('buku_induk', $chk, 'id_siswa');
        $this->load->model('Master_model', 'master');
        if ($aksi == 'hapus') {
        }
        if ($aksi == 'pindah') {
        }
        foreach ($chk as $id) {
            $this->db->where('id_siswa', $id);
            $this->db->update('buku_induk');
            $this->db->set('status', '4');
        }
        if ($aksi == 'keluar') {
        }
        if (!$chk) {
        }
        $this->output_json(['status' => true, 'total' => count($chk), 'last' => $last]);
        $chk = $this->input->post('checked', true);
        $last = $aksi;
        $this->output_json(['status' => false]);
        $aksi = $this->input->post('aksi', true);
    }
    public function do_import()
    {
        foreach ($input as $value) {
            $siswa = ['nisn' => $value['2'] ?? '', 'nis' => $value['3'] ?? '', 'nama' => $value['4'] ?? '', 'jenis_kelamin' => $value['5'] ?? '', 'username' => $value['6'] ?? '', 'password' => $value['7'] ?? '', 'kelas_awal' => $value['8'] ?? '', 'tahun_masuk' => $value['9'] ?? '', 'sekolah_asal' => $value['10'] ?? '', 'tempat_lahir' => $value['11'] ?? '', 'tanggal_lahir' => $value['12'] ?? '', 'agama' => $value['13'] ?? '', 'hp' => $value['14'] ?? '0', 'email' => $value['15'] ?? '', 'anak_ke' => $value['16'] ?? '1', 'status_keluarga' => $value['17'] ?? '1', 'alamat' => $value['18'] ?? '', 'rt' => $value['19'] ?? '', 'rw' => $value['20'] ?? '', 'kelurahan' => $value['21'] ?? '', 'kecamatan' => $value['22'] ?? '', 'kabupaten' => $value['23'] ?? '', 'provinsi' => $value['24'] ?? '', 'kode_pos' => $value['25'] ?? '', 'nama_ayah' => $value['26'] ?? '', 'tgl_lahir_ayah' => $value['27'] ?? '', 'pendidikan_ayah' => $value['28'] ?? '', 'pekerjaan_ayah' => $value['29'] ?? '', 'nohp_ayah' => $value['30'] ?? '', 'alamat_ayah' => $value['31'] ?? '', 'nama_ibu' => $value['32'] ?? '', 'tgl_lahir_ibu' => $value['33'] ?? '', 'pendidikan_ibu' => $value['34'] ?? '', 'pekerjaan_ibu' => $value['35'] ?? '', 'nohp_ibu' => $value['36'] ?? '', 'alamat_ibu' => $value['37'] ?? '', 'nama_wali' => $value['38'] ?? '', 'tgl_lahir_wali' => $value['39'] ?? '', 'pendidikan_wali' => $value['40'] ?? '', 'pekerjaan_wali' => $value['41'] ?? '', 'nohp_wali' => $value['42'] ?? '', 'alamat_wali' => $value['43'] ?? ''];
            $save = $this->db->insert('master_siswa', $siswa);
            $siswa['foto'] = 'uploads/foto_siswa/' . $siswa['nis'] . '.jpg';
            $this->db->set('uid', 'UUID()', FALSE);
        }
        $data = ['status' => true, 'errors' => []];
        $this->output_json($data);
        $this->db->trans_complete();
        if (count($errors) > 0) {
        }
        $data = ['status' => false, 'errors' => $errors, 'duplikat' => $duplikat];
        foreach ($input as $value) {
            $errors[$data['nama']] = ['nama' => form_error('nama'), 'nis' => form_error('nis'), 'nisn' => form_error('nisn'), 'username' => form_error('username'), 'password' => form_error('password')];
            if (!($this->form_validation->run() == FALSE)) {
            }
            $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]|is_unique[master_siswa.nisn]');
            $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]|is_unique[master_siswa.nis]');
            $this->form_validation->set_data($data);
            $data = ['nisn' => $value['2'] ?? '', 'nis' => $value['3'] ?? '', 'nama' => $value['4'] ?? '', 'username' => $value['6'] ?? '', 'password' => $value['7'] ?? ''];
            $this->form_validation->set_rules('password', 'Password', 'required|trim|is_unique[master_siswa.username]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[master_siswa.username]');
            $duplikat[] = $data;
        }
        $uids = $this->db->select('id_siswa, uid')->from('master_siswa')->get()->result();
        $input = $this->input->post('siswa', true);
        $errors = [];
        $duplikat = [];
        $this->db->trans_start();
        $this->output_json($data);
        foreach ($uids as $uid) {
            if (!($check->get()->num_rows() == 0)) {
            }
            $check = $this->db->select('id_siswa')->from('buku_induk')->where('id_siswa', $uid->id_siswa);
            $this->db->insert('buku_induk', $uid);
        }
    }
    public function update()
    {
        $data = ['user' => $user, 'judul' => 'Update Data Siswa', 'subjudul' => 'Update Data Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp_active'] = $tp;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->model('Dropdown_model', 'dropdown');
        $tp = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        $data['tipe'] = 'update';
        $this->load->model('Dashboard_model', 'dashboard');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $this->load->view('master/siswa/update');
    }
    public function downloadData($id_kelas)
    {
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
        }
        $this->output_json(['status' => true, 'siswa' => $siswas]);
        $siswas = $this->master->getSiswaByKelas($tp->id_tp, $smt->id_smt, $id_kelas);
        $smt = $this->master->getSemesterActive();
        $tp = $this->master->getTahunActive();
        $this->load->model('Master_model', 'master');
    }
    public function updateAll()
    {
        foreach ($input as $value) {
            $siswa = ['nisn' => $value['2'] ?? '', 'nis' => $value['3'] ?? '', 'nama' => $value['4'] ?? '', 'jenis_kelamin' => $value['5'] ?? '', 'username' => $value['6'] ?? '', 'password' => $value['7'] ?? '', 'kelas_awal' => $value['8'] ?? '', 'tahun_masuk' => $value['9'] ?? '', 'sekolah_asal' => $value['10'] ?? '', 'tempat_lahir' => $value['11'] ?? '', 'tanggal_lahir' => $value['12'] ?? '', 'agama' => $value['13'] ?? '', 'hp' => $value['14'] ?? '0', 'email' => $value['15'] ?? '', 'anak_ke' => $value['16'] ?? '1', 'status_keluarga' => $value['17'] ?? '1', 'alamat' => $value['18'] ?? '', 'rt' => $value['19'] ?? '', 'rw' => $value['20'] ?? '', 'kelurahan' => $value['21'] ?? '', 'kecamatan' => $value['22'] ?? '', 'kabupaten' => $value['23'] ?? '', 'provinsi' => $value['24'] ?? '', 'kode_pos' => $value['25'] ?? '', 'nama_ayah' => $value['26'] ?? '', 'tgl_lahir_ayah' => $value['27'] ?? '', 'pendidikan_ayah' => $value['28'] ?? '', 'pekerjaan_ayah' => $value['29'] ?? '', 'nohp_ayah' => $value['30'] ?? '', 'alamat_ayah' => $value['31'] ?? '', 'nama_ibu' => $value['32'] ?? '', 'tgl_lahir_ibu' => $value['33'] ?? '', 'pendidikan_ibu' => $value['34'] ?? '', 'pekerjaan_ibu' => $value['35'] ?? '', 'nohp_ibu' => $value['36'] ?? '', 'alamat_ibu' => $value['37'] ?? '', 'nama_wali' => $value['38'] ?? '', 'tgl_lahir_wali' => $value['39'] ?? '', 'pendidikan_wali' => $value['40'] ?? '', 'pekerjaan_wali' => $value['41'] ?? '', 'nohp_wali' => $value['42'] ?? '', 'alamat_wali' => $value['43'] ?? ''];
            $save = $this->db->update('master_siswa', $siswa, array('id_siswa' => $value['44']));
            $siswa['foto'] = 'uploads/foto_siswa/' . $value['3'] . '.jpg';
        }
        $data = ['status' => $save ?? false, 'errors' => []];
        $this->output_json($data);
        $this->db->trans_complete();
        $input = $this->input->post('siswa', true);
        $this->db->trans_start();
    }
    public function update_foto()
    {
        $input = $this->input->post('siswa', true);
        if (count($errors) > 0) {
        }
        $duplikat = [];
        foreach ($input as $value) {
            $siswa = ['nis' => $value['nis'] ?? '', 'foto' => $foto];
            $base64_image_string = $value['foto'];
            $extension = 'jpg';
            if (!($extension == 'jpeg')) {
            }
            $foto = 'uploads/foto_siswa/' . trim($value['nis'] ?? '00') . '.jpg';
            if (!isset($value['foto'])) {
            }
            $save = $this->db->update('master_siswa', $siswa, array('id_siswa' => $value['id']));
            $output_file = trim($value['nis'] ?? '00') . '.' . $extension;
            $foto = 'uploads/foto_siswa/' . $output_file;
            file_put_contents('./uploads/foto_siswa/' . $output_file, base64_decode($base64_image_string));
            $extension = $value['ext'];
        }
        $this->output_json($data);
        $this->db->trans_start();
        $data = ['status' => true, 'errors' => []];
        $this->db->trans_complete();
        $data = ['status' => false, 'errors' => $errors, 'duplikat' => $duplikat];
        $this->output_json($data);
        $errors = [];
        foreach ($input as $value) {
            $duplikat[] = $value;
            if (!($this->form_validation->run() == FALSE)) {
            }
            $this->form_validation->set_data($value);
            $errors[$value['nama']] = ['nis' => form_error('nis')];
            $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]');
        }
    }
    public function updateNisByNisn()
    {
        $this->output_json($save);
        foreach ($input as $val) {
            $save = $this->db->update('master_siswa');
            $this->db->set('nis', trim($val->nis ?? ''));
            $this->db->where('nisn', trim($val->nisn ?? ''));
        }
        $this->db->trans_complete();
        $input = json_decode($this->input->post('siswa', true));
    }
    public function editLogin()
    {
        $deleted = true;
        $last_name = end($nama);
        $tp = $this->master->getTahunActive();
        $data = ['status' => false, 'errors' => ['username' => 'Username sudah digunakan']];
        $status = $this->db->update('master_siswa');
        $this->load->model('Master_model', 'master');
        $group = array('3');
        $id_siswa = $this->input->post('id_siswa', true);
        $status = false;
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        if ($siswa_lain && $siswa_lain->id_siswa != $id_siswa) {
        }
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $this->output_json($data);
        $pass = $this->input->post('new', true);
        $this->db->set('username', $username);
        if ($this->form_validation->run() === FALSE) {
        }
        $first_name = $nama[0];
        $nama = explode(' ', $siswa->nama ?? '');
        $deleted = $this->ion_auth->delete_user($user_siswa->id);
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $username = trim($username ?? '');
        $siswa_lain = $this->dashboard->getDataSiswa($username, $tp->id_tp, $smt->id_smt);
        $data['status'] = $status;
        $smt = $this->master->getSemesterActive();
        $siswa = $this->db->get_where('master_siswa', 'id_siswa="' . $id_siswa . '"')->row();
        $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $this->db->set('password', $password);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['text'] = $msg;
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $msg = !$status ? 'Gagal mengganti username/passsword.' : 'berhasil mengganti username/passsword.';
        $username = $this->input->post('username', true);
        $msg = 'Gagal mengganti username/passsword.';
        $this->db->where('id_siswa', $id_siswa);
        if (!($user_siswa != null)) {
        }
        $user_siswa = $this->db->get_where('users', 'email="' . $email . '"')->row();
        $email = $siswa->nis . '@siswa.com';
        $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        if ($deleted) {
        }
        $password = trim($pass ?? '');
    }
    private function registerSiswa($username, $password, $email, $additional_data, $group)
    {
        $data['id'] = $reg;
        $data['status'] = false;
        $data['status'] = true;
        if (!($reg == false)) {
        }
        return $data;
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
    }
}
```

---

## File: application/controllers_decoded/Datatahun.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datatahun extends CI_Controller
{
    public function __construct()
    {
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
        if ($this->ion_auth->is_admin()) {
        }
        if (!$this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->load->model('Master_model', 'master');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $data['jml_hari'] = $jml == null ? '0' : $jml->jml_hari_efektif;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data = ['user' => $user, 'judul' => 'Tahun Pelajaran dan Semester', 'subjudul' => 'Atur Tahun Pelajaran dan Semester', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['smt_active'] = $smt;
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('_templates/dashboard/_header', $data);
        $jml = $this->master->getJmlHariEfektif($tp->id_tp . $smt->id_smt);
        $this->load->view('master/tahun/data');
    }
    public function data()
    {
        $this->output_json($this->dashboard->getDataTahun(), false);
    }
    public function gantiTahun()
    {
        $data['update'] = $update;
        $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
        foreach ($inputTp as $tps) {
            $tahun = $tps->tp;
            $active = 0;
            if ($id_tp === $aktif) {
            }
            $active = 1;
            $update[] = array('id_tp' => $id_tp, 'tahun' => $tahun, 'active' => $active);
            $id_tp = $tps->id;
        }
        $data['msg'] = 'Merubah Tahun Aktif';
        $data['status'] = true;
        $inputTp = json_decode($this->input->post('tahun', false));
        $aktif = $this->input->post('active', true);
        $this->output_json($data);
        $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
    }
    public function gantiSemester()
    {
        $data['msg'] = 'Merubah Semester Aktif';
        $data['status'] = true;
        $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
        $data['update'] = $update;
        $this->logging->saveLog(4, 'mengganti semester aktif');
        foreach ($inputSmt as $tps) {
            if ($id_smt === $aktif) {
            }
            $update[] = array('id_smt' => $id_smt, 'smt' => $smt, 'active' => $active);
            $smt = $tps->Semester;
            $active = 0;
            $id_smt = $tps->id;
            $active = 1;
        }
        $inputSmt = json_decode($this->input->post('semester', false));
        $this->output_json($data);
        $aktif = $this->input->post('active', true);
    }
    public function add()
    {
        $method = $this->input->post('method', true);
        $this->output->set_content_type('application/json')->set_output($data);
        $id = $this->input->post('id_tahun', true);
        if ($method === 'add') {
        }
        $data = $this->master->create('master_tp', $insert);
        $data = $this->master->update('master_tp', $update, 'id_tp', $id);
        $this->logging->saveLog(3, 'menambah tahun pelajaran');
        $this->logging->saveLog(4, 'mengedit tahun pelajaran');
        $insert = ['tahun' => $tahun];
        $tahun = $this->input->post('tahun', true);
        $update = array('id_tp' => $id, 'tahun' => $tahun);
    }
    public function saveHariEfektif()
    {
        $update = $this->db->replace('master_hari_efektif', $input);
        $input = ['id_hari_efektif' => $tp->id_tp . $smt->id_smt, 'jml_hari_efektif' => $this->input->post('jml_hari', true)];
        $smt = $this->dashboard->getSemesterActive();
        $data['status'] = $update;
        $this->output_json($data);
        $tp = $this->dashboard->getTahunActive();
    }
    public function hapusTahun()
    {
        $id = $this->input->post('hapus', true);
        $data['msg'] = 'Menghapus Tahun Pelajaran';
        $this->output_json($data);
        $data['status'] = false;
        $data['status'] = true;
        if ($this->dashboard->hapus('master_tp', $id, 'id_tp')) {
        }
        $this->logging->saveLog(5, 'menghapus tahun pelajaran');
    }
    public function hapus()
    {
        $this->logging->saveLog(5, 'menghapus tahun pelajaran');
        if (!$chk) {
        }
        if (!$this->dashboard->hapus('master_tp', $chk, 'id_tp')) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $chk = $this->input->post('checked', true);
        $this->output_json(['status' => false]);
    }
}
```

---

## File: application/controllers_decoded/Dbclear.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dbclear extends CI_Controller
{
    public function __construct()
    {
        $this->load->helper('directory');
        $this->load->model('Dashboard_model', 'dashboard');
        if ($this->ion_auth->is_admin()) {
        }
        parent::__construct();
        $this->load->library('upload');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->dbforge();
        $this->load->model('Settings_model', 'settings');
        redirect('auth');
        show_error('Hanya Admin yang boleh mengakses halaman ini', 403, 'Akses dilarang');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $json = json_decode($json);
        $json = (array) $json;
        foreach ($tables as $table) {
            if (in_array($table, $excludes)) {
            }
            if (isset($json[$table])) {
            }
            $nums = $this->db->get('buku_nilai')->num_rows();
            if ($table == 'buku_nilai') {
            }
            $table_info = ['ket' => $this->keterangan()[$table], 'size' => $this->settings->rowSize($table), 'table' => $table, 'name' => ucwords($name)];
            $this->dbforge->drop_table('buku_nilai', true);
            $name = str_replace('_', ' ', $table ?? '');
            $data_tables[$table_info['ket']][] = $table_info;
            if (in_array($table, $excludes)) {
            }
            if (!($nums == 0)) {
            }
            $this->dbforge->drop_table($table, true);
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Bersihkan Data', 'subjudul' => 'Hapus Data', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['smt'] = $this->dashboard->getSemester();
        $data_tables = [];
        $data['tables'] = $data_tables;
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('setting/manage');
        $user = $this->ion_auth->user()->row();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $excludes = ['buku_induk', 'api_setting', 'api_token', 'bulan', 'hari', 'setting', 'cbt_jenis', 'cbt_ruang', 'cbt_sesi', 'cbt_token', 'level_guru', 'level_kelas', 'master_tp', 'master_smt', 'master_hari_efektif', 'users', 'groups', 'users_groups', 'login_attempts', 'users_profile', 'rapor_admin_setting', 'running_text'];
        $tables = $this->db->list_tables();
        $json = file_get_contents('./assets/app/db/database.json');
        $this->load->view('_templates/dashboard/_footer');
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function hapusTable()
    {
        $prefs = ['tables' => array($table), 'ignore' => array(), 'format' => 'txt', 'filename' => $table . '.sql', 'add_drop' => TRUE, 'add_insert' => TRUE, 'newline' => '
'];
        $this->output_json(['type' => 'database', 'message' => 'Database berhasil dihapus']);
        $this->load->dbutil();
        $this->load->helper('file');
        $backup = $this->dbutil->backup(array($prefs));
        $table = $this->input->post('table', true);
        write_file('./backups/backup_' . $table . '_' . date('Y_m_d_H_i_s') . '.sql', $backup);
        $this->db->truncate($table);
    }
    public function truncate()
    {
        $this->settings->truncate($tables);
        $this->output_json(['status' => true]);
        $tables = $this->db->list_tables();
    }
    private function keterangan()
    {
        $data = ['api_setting' => '1', 'api_token' => '1', 'buku_induk' => '1', 'bulan' => '0', 'cbt_bank_soal' => '2', 'cbt_durasi_siswa' => '2', 'cbt_jadwal' => '2', 'cbt_jadwal_ujian' => '2', 'cbt_jenis' => '0', 'cbt_kelas_ruang' => '2', 'cbt_kop_absensi' => '1', 'cbt_kop_berita' => '1', 'cbt_kop_kartu' => '1', 'cbt_nilai' => '2', 'cbt_nomor_peserta' => '2', 'cbt_pengawas' => '2', 'cbt_rekap' => '2', 'cbt_rekap_nilai' => '2', 'cbt_ruang' => '1', 'cbt_sesi' => '1', 'cbt_sesi_siswa' => '2', 'cbt_soal' => '2', 'cbt_soal_siswa' => '2', 'cbt_token' => '1', 'groups' => '0', 'hari' => '0', 'jabatan_guru' => '1', 'kelas_catatan_mapel' => '2', 'kelas_catatan_wali' => '2', 'kelas_ekstra' => '1', 'kelas_jadwal_kbm' => '2', 'kelas_jadwal_mapel' => '2', 'kelas_jadwal_materi' => '2', 'kelas_jadwal_tugas' => '2', 'kelas_materi' => '2', 'kelas_siswa' => '2', 'kelas_struktur' => '2', 'kelas_tugas' => '2', 'level_guru' => '0', 'level_kelas' => '0', 'log' => '2', 'login_attempts' => '0', 'log_materi' => '2', 'log_tugas' => '2', 'log_ujian' => '2', 'master_ekstra' => '1', 'master_guru' => '1', 'master_hari_efektif' => '1', 'master_jurusan' => '1', 'master_kelas' => '1', 'master_kelompok_mapel' => '1', 'master_mapel' => '1', 'master_siswa' => '1', 'master_smt' => '0', 'master_tp' => '0', 'post' => '2', 'post_comments' => '2', 'post_reply' => '2', 'rapor_admin_setting' => '1', 'rapor_catatan_wali' => '1', 'rapor_data_catatan' => '1', 'rapor_data_fisik' => '1', 'rapor_data_sikap' => '1', 'rapor_fisik' => '1', 'rapor_kikd' => '1', 'rapor_kkm' => '1', 'rapor_naik' => '1', 'rapor_nilai_akhir' => '1', 'rapor_nilai_ekstra' => '1', 'rapor_nilai_harian' => '1', 'rapor_nilai_pts' => '1', 'rapor_nilai_sikap' => '1', 'rapor_prestasi' => '1', 'running_text' => '1', 'setting' => '1', 'users' => '0', 'users_groups' => '0', 'users_profile' => '0'];
        return $data;
    }
}
```

---

## File: application/controllers_decoded/Dbmanager.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dbmanager extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Admin yang boleh mengakses halaman ini', 403, 'Akses dilarang');
        $this->load->helper('directory');
        redirect('auth');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->library('upload');
        $this->load->model('Settings_model', 'settings');
        $this->load->model('Dashboard_model', 'dashboard');
        if ($this->ion_auth->is_admin()) {
        }
        parent::__construct();
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function index()
    {
        $data['tables'] = $this->db->list_tables();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        foreach ($list as $key => $value) {
            $tgl = filemtime('./backups/' . $value);
            $arrFile[$key] = ['type' => $type, 'nama' => $nama, 'tgl' => $tgl, 'size' => $size, 'src' => $value];
            if (!($type !== 'html')) {
            }
            $size = $this->formatSizeUnits(filesize('./backups/' . $value));
            $type = $nfile[1];
            $nfile = explode('.', $value ?? '');
            $nama = $nfile[0];
        }
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_footer');
        $arrFile = [];
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('setting/db');
        $user = $this->ion_auth->user()->row();
        $list = directory_map('./backups/');
        $data['list'] = $arrFile;
        $data['tp'] = $this->dashboard->getTahun();
        $data = ['user' => $user, 'judul' => 'Backup dan Restore', 'subjudul' => 'Backup Semua Database dan File', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
    }
    public function manage()
    {
        foreach ($tables as $table) {
            $data_tables[$table] = $this->settings->toJSON($table);
        }
        $user = $this->ion_auth->user()->row();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['tables'] = $data_tables;
        $data['tp'] = $this->dashboard->getTahun();
        $data_tables = [];
        $this->load->view('_templates/dashboard/_footer');
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/manage');
        $data = ['user' => $user, 'judul' => 'Bersihkan Data', 'subjudul' => 'Hapus Data', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tables = $this->db->list_tables();
    }
    public function truncate()
    {
        $tables = $this->db->list_tables();
        $this->output_json(['status' => true]);
        $this->settings->truncate($tables);
    }
    public function backupDb()
    {
        $backup = $this->dbutil->backup($prefs);
        $this->output_json(['type' => 'database', 'message' => 'Database berhasil dibackup']);
        $this->dbutil->optimize_database();
        $prefs = ['tables' => $this->db->list_tables(), 'ignore' => array(), 'format' => 'zip', 'filename' => 'backup.sql', 'add_drop' => TRUE, 'add_insert' => TRUE, 'newline' => '
'];
        $this->load->helper('file');
        write_file('./backups/backup-db-' . date('Y-m-d-H-i-s') . '.sql.zip', $backup);
        $this->load->dbutil();
    }
    public function backupData()
    {
        $this->load->library('zip');
        $this->output_json(['type' => 'file', 'message' => 'File data berhasil dibackup']);
        $this->zip->archive('./backups/backup-file-' . date('Y-m-d-H-i-s') . '.zip');
        $this->zip->read_dir('uploads');
    }
    public function hapusBackup($src)
    {
        if (unlink('./backups/' . $src)) {
        }
        $this->output_json(['status' => true, 'message' => 'Backup berhasil dihapus']);
        $this->output_json(['status' => false, 'message' => 'Gagal menghapus backup']);
    }
    function formatSizeUnits($bytes)
    {
        $bytes = $bytes . ' byte';
        $bytes = number_format($bytes / 1024, 2) . ' KB';
        if ($bytes >= 1048576) {
        }
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1024) {
        }
        $bytes = '0 bytes';
        return $bytes;
        if ($bytes == 1) {
        }
        if ($bytes >= 1073741824) {
        }
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes > 1) {
        }
        $bytes = $bytes . ' bytes';
    }
}
```

---

## File: application/controllers_decoded/Guruview.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Guruview extends CI_Controller
{
    public function __construct()
    {
        redirect('auth');
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
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
        $data['tp_active'] = $tp;
        $this->load->view('members/guru/templates/footer');
        $data['tp'] = $this->dashboard->getTahun();
        redirect('auth');
        $data['input_profile'] = json_decode(json_encode($inputsProfile), FALSE);
        $this->load->view('members/guru/profile');
        $guru = $this->dashboard->getDetailGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $smt = $this->master->getSemesterActive();
        $data['smt_active'] = $smt;
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('members/guru/templates/header', $data);
        $data['input_alamat'] = json_decode(json_encode($inputsAlamat), FALSE);
        $data = ['user' => $user, 'judul' => 'Profile', 'subjudul' => 'Profile Saya', 'setting' => $this->dashboard->getSetting()];
        $user = $this->ion_auth->user()->row();
        $inputsAlamat = [['label' => 'NIK', 'name' => 'no_ktp', 'value' => $guru->no_ktp, 'icon' => 'far fa-id-card', 'type' => 'number'], ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'value' => $guru->tempat_lahir, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Tgl. Lahir', 'name' => 'tgl_lahir', 'value' => $guru->tgl_lahir, 'icon' => 'fa fa-calendar', 'type' => 'text'], ['label' => 'Alamat', 'name' => 'alamat_jalan', 'value' => $guru->alamat_jalan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kecamatan', 'name' => 'kecamatan', 'value' => $guru->kecamatan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kota/Kab.', 'name' => 'kabupaten', 'value' => $guru->kabupaten, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Provinsi', 'name' => 'provinsi', 'value' => $guru->provinsi, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kode Pos', 'name' => 'kode_pos', 'value' => $guru->kode_pos, 'icon' => 'fa fa-envelope', 'type' => 'number']];
        $data['guru'] = $guru;
        $inputsProfile = [['label' => 'Nama Lengkap', 'name' => 'nama_guru', 'value' => $guru->nama_guru, 'icon' => 'far fa-user', 'type' => 'text'], ['label' => 'Email', 'name' => 'email', 'value' => $guru->email, 'icon' => 'far fa-envelope', 'type' => 'text'], ['label' => 'NIP / NUPTK', 'name' => 'nip', 'value' => $guru->nip, 'icon' => 'far fa-id-card', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $guru->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'type' => 'text'], ['label' => 'No. Handphone', 'name' => 'no_hp', 'value' => $guru->no_hp, 'icon' => 'fa fa-phone', 'type' => 'number'], ['label' => 'Agama', 'name' => 'agama', 'value' => $guru->agama, 'icon' => 'far fa-user', 'type' => 'text']];
        if (!($user == null)) {
        }
        $tp = $this->master->getTahunActive();
    }
    public function save()
    {
        $alamat_jalan = $this->input->post('alamat_jalan', true);
        $email = $this->input->post('email', true);
        $kode_pos = $this->input->post('kode_pos', true);
        $tempat_lahir = $this->input->post('tempat_lahir', true);
        $this->output_json(['status' => false]);
        $action = $this->master->update('master_guru', $input, 'id_guru', $id_guru);
        $agama = $this->input->post('agama', true);
        $smt = $this->master->getSemesterActive();
        if ($this->form_validation->run() == FALSE) {
        }
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[1]|max_length[50]');
        $kabupaten = $this->input->post('kabupaten', true);
        $tp = $this->master->getTahunActive();
        $u_nip = $dbdata->nip === $nip ? '' : '|is_unique[master_guru.nip]';
        $no_hp = $this->input->post('no_hp', true);
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[8]|max_length[30]' . $u_nip);
        $data = ['status' => false, 'errors' => ['nip' => form_error('nip'), 'nama_guru' => form_error('nama_guru')]];
        $this->output_json($data);
        $provinsi = $this->input->post('provinsi', true);
        $dbdata = $this->master->getGuruById($id_guru, $tp->id_tp, $smt->id_smt);
        $kecamatan = $this->input->post('kecamatan', true);
        $input = ['nip' => $nip, 'nama_guru' => $nama_guru, 'email' => $email, 'jenis_kelamin' => $jenis_kelamin, 'no_hp' => $no_hp, 'agama' => $agama, 'no_ktp' => $no_ktp, 'tempat_lahir' => $tempat_lahir, 'tgl_lahir' => $this->strContains($tgl_lahir, '0000-') ? null : $tgl_lahir, 'alamat_jalan' => $alamat_jalan, 'kecamatan' => $kecamatan, 'kabupaten' => $kabupaten, 'provinsi' => $provinsi, 'kode_pos' => $kode_pos];
        if ($action) {
        }
        $id_guru = $this->input->post('id_guru', true);
        $jenis_kelamin = $this->input->post('jenis_kelamin', true);
        $this->output_json(['status' => true]);
        $nip = $this->input->post('nip', true);
        $no_ktp = $this->input->post('no_ktp', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $tgl_lahir = $this->input->post('tgl_lahir', true);
    }
    function strContains($string, $val)
    {
        return strpos($string, $val) !== false;
    }
    function uploadFile($id_guru)
    {
        if (isset($_FILES['foto']['name'])) {
        }
        $config['overwrite'] = true;
        $config['upload_path'] = './uploads/profiles/';
        $data['status'] = false;
        $config['file_name'] = $guru->nip;
        $data['src'] = '';
        $data['size'] = $_FILES['foto']['size'];
        $this->db->where('id_guru', $id_guru);
        if (!$this->upload->do_upload('foto')) {
        }
        $data['status'] = true;
        $this->output_json($data);
        $guru = $this->master->getGuruById($id_guru);
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
        $data['type'] = $_FILES['foto']['type'];
        $data['src'] = $this->upload->display_errors();
        $this->db->update('master_guru');
        $this->db->set('foto', 'uploads/profiles/' . $result['file_name']);
        $data['src'] = base_url() . 'uploads/profiles/' . $result['file_name'];
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $this->upload->initialize($config);
        $result = $this->upload->data();
    }
    function deleteFile($id_guru)
    {
        $this->db->set('foto', '');
        $src = $this->input->get('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!unlink($file_name)) {
        }
        $this->db->where('id_guru', $id_guru);
        $this->db->update('master_guru');
        echo 'File Delete Successfully';
        if (!($file_name != 'user.jpg')) {
        }
    }
}
```

---

## File: application/controllers_decoded/Hasilujian.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class HasilUjian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_model', 'master');
        $this->load->library(['datatables']);
        if ($this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->load->model('Ujian_model', 'ujian');
        $this->user = $this->ion_auth->user()->row();
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function data()
    {
        $nip_guru = $this->user->username;
        $nip_guru = null;
        $this->output_json($this->ujian->getHasilUjian($nip_guru), false);
        if (!$this->ion_auth->in_group('guru')) {
        }
    }
    public function NilaiMhs($id)
    {
        $this->output_json($this->ujian->HslUjianById($id, true), false);
    }
    public function index()
    {
        $data = ['user' => $this->user, 'judul' => 'Ujian', 'subjudul' => 'Hasil Ujian'];
        $this->load->view('_templates/dashboard/_footer.php');
        $this->load->view('ujian/hasil');
        $this->load->view('_templates/dashboard/_header.php', $data);
    }
    public function detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $data = ['user' => $this->user, 'judul' => 'Ujian', 'subjudul' => 'Detail Hasil Ujian', 'ujian' => $ujian, 'nilai' => $nilai];
        $this->load->view('ujian/detail_hasil');
        $this->load->view('_templates/dashboard/_footer.php');
        $this->load->view('_templates/dashboard/_header.php', $data);
        $nilai = $this->ujian->bandingNilai($id);
    }
    public function cetak($id)
    {
        $this->load->view('ujian/cetak', $data);
        $data = ['ujian' => $ujian, 'hasil' => $hasil, 'mhs' => $mhs];
        $hasil = $this->ujian->HslUjian($id, $mhs->id_siswa)->row();
        $ujian = $this->ujian->getUjianById($id);
        $mhs = $this->ujian->getIdMahasiswa($this->user->username);
    }
    public function cetak_detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $hasil = $this->ujian->HslUjianById($id)->result();
        $nilai = $this->ujian->bandingNilai($id);
        $this->load->view('ujian/cetak_detail', $data);
        $data = ['ujian' => $ujian, 'nilai' => $nilai, 'hasil' => $hasil];
    }
}
```

---

## File: application/controllers_decoded/Install.php

```php
<?php

/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
defined('BASEPATH') or exit('No direct script access allowed');
class Install extends CI_Controller
{
    function __construct()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        include APPPATH . 'config/database.php';
        $this->load->database();
        if (!($db['default']['database'] != '')) {
        }
        $this->load->dbforge();
        parent::__construct();
        $this->load->model('Install_model', 'install');
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
        $data = $this->getSaved();
        $this->load->view('install/header', ['data' => $data]);
        $data->error = $res;
        $data['msg'] = 'belum ada administrator';
        $data['msg'] = 'belum ada data sekolah';
        redirect('update');
        if ($res == '0') {
        }
        $this->load->view('install/step');
        if ($res == '2') {
        }
        $res = $this->install->check_installer();
        if ($res == '3') {
        }
        $this->load->view('install/footer');
        $data['msg'] = 'sebagian tabel belum dibuat';
    }
    function getSaved()
    {
        $setting = $this->dashboard->getSetting();
        $data['jenjang'] = $setting->jenjang;
        $data['database'] = $database;
        $data['kota'] = $setting->kota;
        $data['kec'] = '';
        $data['user_admin'] = $admin->username;
        $admin = $this->db->get('users')->row();
        $data['alamat'] = '';
        $data['prov'] = '';
        $data['prov'] = $setting->provinsi;
        $data['current_page'] = $current_page;
        return json_decode(json_encode($data));
        $data['user_admin'] = '';
        $data['kota'] = '';
        $data['kepsek'] = $setting->kepsek;
        $data['pass_admin'] = $admin->password;
        include APPPATH . 'config/database.php';
        $data['kec'] = $setting->kecamatan;
        $data['jenjang'] = '';
        $data['alamat'] = $setting->alamat;
        $data['kepsek'] = '';
        $current_page = 2;
        $data['aplikasi'] = '';
        $current_page = 2;
        $data['password'] = $db['default']['password'];
        $data['sekolah'] = '';
        $data['pass_admin'] = '';
        $data['desa'] = '';
        $data['nama_admin'] = '';
        $data['username'] = $db['default']['username'];
        $data['sekolah'] = $setting->sekolah;
        $data['msg'] = 'Table `users` belum dibuat';
        if (!($admin != null)) {
        }
        $data['aplikasi'] = $setting->nama_aplikasi;
        $data['satuan'] = $setting->satuan_pendidikan;
        if ($this->db->table_exists('users')) {
        }
        $database = $db['default']['database'];
        $data['hostname'] = $db['default']['hostname'];
        if (!($setting != null)) {
        }
        $data['nama_admin'] = $admin->first_name . ' ' . $admin->last_name;
        $current_page = $admin == null ? 2 : ($setting == null ? 3 : 4);
        $data['desa'] = $setting->desa;
        $data['satuan'] = '';
    }
    public function steps()
    {
        $this->load->view('install/header', ['data' => $data]);
        $this->load->view('install/step');
        $this->load->view('install/footer');
        $data = $this->getSaved();
    }
    public function checkDatabase()
    {
        $data['host'] = false;
        $new = str_replace('%USERNAME%', $hostuser, $new);
        $data['host'] = false;
        if (is_writable($output_path)) {
        }
        $template_path = './assets/app/db/database.php';
        $database_file = file_get_contents($template_path);
        if ($this->validate_host($hostname, $hostuser, $database)) {
        }
        $hostname = $this->input->post('hostname', true);
        $new = str_replace('%PASSWORD%', $hostpass, $new);
        $data['host_msg'] = 'behasil';
        $database = $this->input->post('database', true);
        $data['database'] = true;
        $data['host'] = false;
        $data['host_msg'] = 'gagal membuat nama database';
        $data['host'] = true;
        $new = str_replace('%HOSTNAME%', $hostname, $database_file);
        $data['host_msg'] = 'tidak ada akses ke file database.php, pastikan permission sudah dizinkan';
        $data['host'] = true;
        $handle = fopen($output_path, 'w+');
        $new = str_replace('%DATABASE%', $database, $new);
        if (fwrite($handle, $new)) {
        }
        $hostuser = $this->input->post('hostuser', true);
        $this->output_json($data);
        $output_path = APPPATH . 'config/database.php';
        $data['database'] = $this->create_database($hostname, $hostuser, $hostpass, $database);
        $data['table'] = $this->create_tables($hostname, $hostuser, $hostpass, $database);
        $data['host_msg'] = 'sukses';
        @chmod($output_path, 0777);
        $data['host_msg'] = 'tidak boleh ada yang kosong';
        $hostpass = $this->input->post('hostpass', true);
    }
    public function createDb()
    {
        if ($page == '0') {
        }
        $data['database'] = true;
        $hostpass = $this->input->post('hostpass', true);
        $hostuser = $this->input->post('hostuser', true);
        $data['table'] = $this->create_tables($hostname, $hostuser, $hostpass, $database);
        $this->output_json($data);
        $data['host_msg'] = 'sukses';
        $hostname = $this->input->post('hostname', true);
        $data['database'] = false;
        $data['table'] = false;
        $data['host'] = true;
        $page = $this->input->post('page', true);
        $data['host'] = true;
        $database = $this->input->post('database', true);
        $data['host_msg'] = 'step salah';
    }
    function validate_host($host, $usr, $db)
    {
        return !empty($host) && !empty($usr) && !empty($db);
    }
    function create_database($hostname, $hostuser, $hostpass, $database)
    {
        $mysqli->query('CREATE DATABASE IF NOT EXISTS ' . $database);
        return true;
        $mysqli->close();
        if (!mysqli_connect_errno()) {
        }
        return false;
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, '');
    }
    function create_tables($hostname, $hostuser, $hostpass, $database)
    {
        return true;
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, $database);
        return false;
        $mysqli->close();
        $query = file_get_contents('./assets/app/db/master.sql');
        $mysqli->multi_query($query);
        if (!mysqli_connect_errno()) {
        }
    }
    public function createSetting()
    {
        $jenjang = $this->input->post('jenjang', true);
        $desa = $this->input->post('desa', true);
        $data['insert'] = $this->db->insert('setting', $insert);
        $tlp = $this->input->post('tlp', true);
        $kec = $this->input->post('kec', true);
        $satuan_pendidikan = $this->input->post('satuan_pendidikan', true);
        $this->output_json($data);
        $kota = $this->input->post('kota', true);
        $insert = ['id_setting' => 1, 'sekolah' => $sekolah, 'jenjang' => $jenjang, 'satuan_pendidikan' => $satuan_pendidikan, 'alamat' => $alamat, 'desa' => $desa, 'kota' => $kota, 'kecamatan' => $kec, 'telp' => $tlp, 'kepsek' => $kepsek, 'nama_aplikasi' => $nama_aplikasi];
        $data['saved'] = $this->getSaved();
        $nama_aplikasi = $this->input->post('nama_aplikasi', true);
        $kepsek = $this->input->post('kepsek', true);
        $sekolah = $this->input->post('nama_sekolah', true);
        $alamat = $this->input->post('alamat', true);
    }
    public function createAdmin()
    {
        $password = $this->input->post('password', true);
        $last_name = end($namaAdmin);
        $namaAdmin = explode(' ', $nama ?? '');
        $group = array('1');
        $nama = $this->input->post('nama_lengkap', true);
        $create = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $email = strtolower($nama ?? '') . '@admin.com';
        $this->output_json($data);
        $data['admin'] = $create;
        $first_name = $namaAdmin[0];
        $username = $this->input->post('username', true);
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
    }
    public function createApp()
    {
        $insert = ['id_setting' => 1, 'sekolah' => $sekolah, 'jenjang' => $jenjang, 'satuan_pendidikan' => $satuan_pendidikan, 'alamat' => $alamat, 'desa' => $desa, 'kota' => $kota, 'kecamatan' => $kec, 'provinsi' => $prov, 'kepsek' => $kepsek, 'nama_aplikasi' => $nama_aplikasi];
        $last_name = end($namaAdmin);
        $nama_aplikasi = $this->input->post('nama_aplikasi', true);
        $namaAdmin = explode(' ', $nama ?? '');
        $alamat = $this->input->post('alamat', true);
        $first_name = $namaAdmin[0];
        $satuan_pendidikan = $this->input->post('satuan', true);
        $sekolah = $this->input->post('nama_sekolah', true);
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $data['admin'] = $create;
        $nama = $this->input->post('nama_lengkap', true);
        $email = strtolower($nama ?? '') . '@admin.com';
        $kepsek = $this->input->post('kepsek', true);
        $create = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $jenjang = $this->input->post('jenjang', true);
        $data['insert'] = $this->db->insert('setting', $insert);
        $username = $this->input->post('username', true);
        $kec = $this->input->post('kec', true);
        $desa = $this->input->post('desa', true);
        $this->output_json($data);
        $kota = $this->input->post('kota', true);
        $password = $this->input->post('password', true);
        $group = array('1');
        $prov = $this->input->post('prov', true);
    }
}
```

---

## File: application/controllers_decoded/Jurusanmapel.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class JurusanMapel extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->form_validation->set_error_delimiters('', '');
        if (!$this->ion_auth->logged_in()) {
        }
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Master_model', 'master');
        redirect('auth');
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Jurusan Mata Kuliah', 'subjudul' => 'Data Jurusan Mata Kuliah'];
        $this->load->view('relasi/jurusanmapel/data');
        $this->load->view('_templates/dashboard/_footer.php');
        $this->load->view('_templates/dashboard/_header.php', $data);
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
        $this->load->view('_templates/dashboard/_footer.php');
        $this->load->view('_templates/dashboard/_header.php', $data);
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Tambah Jurusan Mata Kuliah', 'subjudul' => 'Tambah Data Jurusan Mata Kuliah', 'mapel' => $this->master->getMapel()];
        $this->load->view('relasi/jurusanmapel/add');
    }
    public function edit($id)
    {
        $this->load->view('_templates/dashboard/_footer.php');
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/edit');
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Edit Jurusan Mata Kuliah', 'subjudul' => 'Edit Data Jurusan Mata Kuliah', 'mapel' => $this->master->getMapelById($id, true), 'id_mapel' => $id, 'all_jurusan' => $this->master->getAllJurusan(), 'jurusan' => $this->master->getJurusanByIdMapel($id)];
    }
    public function save()
    {
        $this->form_validation->set_rules('jurusan_id[]', 'Jurusan', 'required');
        if ($method === 'add') {
        }
        $method = $this->input->post('method', true);
        $mapel_id = $this->input->post('mapel_id', true);
        if (!($method === 'edit')) {
        }
        $this->output_json($data);
        foreach ($jurusan_id as $key => $val) {
            $input[] = ['mapel_id' => $mapel_id, 'jurusan_id' => $val];
        }
        $action = $this->master->create('jurusan_mapel', $input, true);
        $this->output_json($data);
        $this->form_validation->set_rules('mapel_id', 'Mata Kuliah', 'required');
        $id = $this->input->post('mapel_id', true);
        $input = [];
        $jurusan_id = $this->input->post('jurusan_id', true);
        $data = ['status' => false, 'errors' => ['mapel_id' => form_error('mapel_id'), 'jurusan_id[]' => form_error('jurusan_id[]')]];
        $this->master->delete('jurusan_mapel', $id, 'mapel_id');
        if ($this->form_validation->run() == FALSE) {
        }
        $data['status'] = $action ? TRUE : FALSE;
        $action = $this->master->create('jurusan_mapel', $input, true);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        $this->output_json(['status' => false]);
        if (!$this->master->delete('jurusan_mapel', $chk, 'mapel_id')) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
        if (!$chk) {
        }
    }
}
```

---

## File: application/controllers_decoded/Kelasabsensibulanan.php

```php
<?php

class Kelasabsensibulanan extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Dibatasi');
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        redirect('auth');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Master_model', 'master');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        $data['mapel'] = $arrMapel;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('members/guru/templates/header', $data);
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $arrMapel = [];
        if ($this->ion_auth->is_admin()) {
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if (!($mapel != null)) {
        }
        $data['arrkelas'] = $arrKelas;
        if (!($mapel != null)) {
        }
        $this->load->view('_templates/dashboard/_footer');
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $this->load->view('kelas/absenbulanan/data');
        $data = ['user' => $user, 'judul' => 'Daftar Hadir Bulanan', 'subjudul' => 'Daftar Hadir Bulanan Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('_templates/dashboard/_header', $data);
        $data['kelas'] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
        $arrId = [];
        $this->load->view('members/guru/templates/footer');
        $data['smt_active'] = $smt;
        $data['id_guru'] = $guru->id_guru;
        $data['guru'] = $this->dropdown->getAllGuru();
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $data['tp'] = $this->dashboard->getTahun();
        $tp = $this->master->getTahunActive();
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        foreach ($mapel[0]->kelas_mapel as $id_mapel) {
            array_push($arrId, $id_mapel->kelas);
        }
        $arrKelas = [];
        $data['guru'] = $guru;
        $smt = $this->master->getSemesterActive();
        $data['bulan'] = $this->dropdown->getBulan();
        foreach ($mapel as $m) {
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $this->load->view('kelas/absenbulanan/data');
        $data['mapel'] = $this->dropdown->getAllMapel();
    }
    public function loadAbsensiMapel()
    {
        if ($jadwal != null) {
        }
        $id_smt = $this->master->getSemesterActive()->id_smt;
        if (!($i < $tgl)) {
        }
        $log = [];
        $b = $bulan < 10 ? '0' . $bulan : $bulan;
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_kelas = $this->input->post('kelas', true);
        $bulan = $this->input->post('bln', true);
        $infos = $this->kelas->getJadwalMapelByMapel($id_kelas, $id_mapel, $id_tp, $id_smt);
        $jadwal_materi = [];
        $tgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $this->output_json(['log' => $log, 'jadwal' => $jadwal, 'materi' => $jadwal_materi, 'mapels' => $mapel_bulan_ini]);
        $this->output_json(['jadwal' => $jadwal]);
        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        foreach ($siswa as $s) {
            $i++;
            $b = $bulan < 10 ? '0' . $bulan : $bulan;
            $arrMateri = [];
            $t = $i + 1 < 10 ? '0' . ($i + 1) : $i + 1;
            $arrMateri[1][] = $materi_perbulan != null && isset($materi_perbulan[$s->id_siswa]) && isset($materi_perbulan[$s->id_siswa][1]) && isset($materi_perbulan[$s->id_siswa][1][$tahun . '-' . $b . '-' . $t]) ? $materi_perbulan[$s->id_siswa][1][$tahun . '-' . $b . '-' . $t] : null;
            $i = 0;
            if (!($i < $tgl)) {
            }
            $arrMateri[2][] = $materi_perbulan != null && isset($materi_perbulan[$s->id_siswa]) && isset($materi_perbulan[$s->id_siswa][2]) && isset($materi_perbulan[$s->id_siswa][2][$tahun . '-' . $b . '-' . $t]) ? $materi_perbulan[$s->id_siswa][2][$tahun . '-' . $b . '-' . $t] : null;
            $log[$s->id_siswa] = ['nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->nama_kelas, 'materi' => $arrMateri[1], 'tugas' => $arrMateri[2]];
        }
        $jadwal = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        $mapel_bulan_ini = [];
        $i++;
        $jadwal_materi[$t] = (array) $this->kelas->getAllMateriByTgl($id_kelas, $tahun . '-' . $b . '-' . $t, [$id_mapel]);
        $tahun = $this->input->post('thn', true);
        foreach ($infos as $info) {
            $dates = $this->total_hari($info->id_hari, $bulan, $tahun);
            foreach ($dates as $date) {
                $mapel_bulan_ini[$d[2]][$info->jam_ke] = $date;
                $d = explode('-', $date ?? '');
            }
        }
        $jadwal->istirahat = unserialize($jadwal->istirahat);
        $materi_perbulan = $this->kelas->getRekapBulananSiswa($id_mapel, $id_kelas, $tahun, $bulan);
        $id_mapel = $this->input->post('mapel', true);
        $i = 0;
        $t = $i + 1 < 10 ? '0' . ($i + 1) : $i + 1;
    }
    function total_hari($id_day, $bulan, $taun)
    {
        if (!(date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday)) {
        }
        $days++;
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $idday = $id_day == '7' ? 0 : $id_day;
        $days = 0;
        return $dates;
        $i++;
        if (!($i < $total_days)) {
        }
        $dates = [];
        array_push($dates, date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i)));
        $i = 1;
    }
}
```

---

## File: application/controllers_decoded/Kelasabsensiharian.php

```php
<?php

class Kelasabsensiharian extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model', 'kelas');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Dropdown_model', 'dropdown');
        redirect('auth');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->library(['datatables', 'form_validation']);
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        if ($this->ion_auth->is_admin()) {
        }
        $data['guru'] = $this->dropdown->getAllGuru();
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->master->getTahunActive();
        $this->load->view('kelas/absenharian/data');
        $data['tp_active'] = $tp;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['mapel'] = $this->dropdown->getAllMapel();
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Kehadiran Harian Siswa', 'subjudul' => 'Data Kehadiran Siswa', 'setting' => $this->dashboard->getSetting()];
        $user = $this->ion_auth->user()->row();
        $this->load->view('kelas/absenharian/data');
        $data['smt_active'] = $smt;
        $data['id_guru'] = $guru->id_guru;
        $data['guru'] = $guru;
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['tp'] = $this->dashboard->getTahun();
        $smt = $this->master->getSemesterActive();
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
    }
    public function loadAbsensi()
    {
        if ($info != null) {
        }
        if (!($info != null)) {
        }
        foreach ($siswa as $s) {
            $log[$s->id_siswa] = ['nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->nama_kelas, 'status' => $status];
            $status = [];
            $status_materi = [];
            foreach ($status_materi as $stat) {
                $status[$stat->jam_ke][$stat->id_mapel][$stat->jenis] = $stat;
            }
            if (!(count($arrIdKjm) > 0)) {
            }
            $status_materi = $this->kelas->getRekapStatusMateri($s->id_siswa, $arrIdKjm);
        }
        $log = [];
        $id_kelas = $this->input->post('kelas', true);
        $arrIdMapel = [];
        $tahun = $this->input->post('thn', true);
        $istirahat = [];
        $jadwal = $this->dashboard->loadJadwalHariIni($id_tp, $id_smt, $id_kelas, $hari);
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $tanggal = str_pad($tanggal, 2, '0', STR_PAD_LEFT);
        $istirahat = unserialize($info->istirahat);
        $bulan = $this->input->post('bln', true);
        $this->output_json(array('test' => [$id_kelas, $tahun . '-' . $bulan . '-' . $tanggal, $arrIdMapel], 'log' => $log, 'info' => $info, 'jadwal' => $jadwal, 'materi' => $jadwal_materi, 'istirahat' => $istirahat));
        $info = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        $id_tp = $this->master->getTahunActive()->id_tp;
        foreach ($jadwal as $jd) {
            array_push($arrIdMapel, $jd->id_mapel);
        }
        $tanggal = $this->input->post('tgl', true);
        $arrIdKjm = [];
        $jadwal_materi = [];
        foreach ($jadwal_materi as $jmtr) {
            foreach ($jmtr as $jam) {
                foreach ($jam as $jns) {
                    array_push($arrIdKjm, $jns->id_kjm);
                }
            }
        }
        $hari = $this->input->post('hari', true);
        $jadwal_materi = $this->kelas->getAllMateriByTgl($id_kelas, $tahun . '-' . $bulan . '-' . $tanggal, $arrIdMapel);
        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        if (!(count($arrIdMapel) > 0)) {
        }
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
    }
}
```

---

## File: application/controllers_decoded/Kelascatatan.php

```php
<?php

class Kelascatatan extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Master_model', 'master');
        redirect('auth');
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $kelasses = $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId);
        $this->load->view('members/guru/kelas/catatan/data');
        $data['smt_active'] = $smt;
        $data['kelas'] = $arrKelas;
        $data['mapel'] = $arrMapel;
        if ($this->ion_auth->is_admin()) {
        }
        $data['tp_active'] = $tp;
        if (!(count($arrId) > 0)) {
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $kelasses = [];
        $data['cat_kelas'] = $cat_kelas;
        $data['tp'] = $this->dashboard->getTahun();
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        foreach ($mapel as $m) {
            foreach ($m->kelas_mapel as $kls_mapel) {
                foreach ($kelasses as $key => $kelass) {
                    if (!($kls_mapel->kelas == $key)) {
                    }
                    $arrKelas[$m->id_mapel][$key] = $kelass;
                }
            }
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $data['guru'] = $guru;
        $data['kelas_selected'] = $id_kelas;
        $data['id_guru'] = $guru->id_guru;
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Catatan Guru', 'subjudul' => 'Catatan Selama Pembelajaran', 'setting' => $setting];
        $data['mapel_selected'] = $id_mapel;
        $data['mapel'] = $this->dropdown->getAllMapel();
        $arrId = [];
        $id_mapel = $this->input->get('mapel', true);
        if (!($mapel != null)) {
        }
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $cat_kelas = $this->kelas->getCatatanMapelKelas($id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/kelas/catatan/data');
        if (!($mapel != null)) {
        }
        $this->load->view('members/guru/templates/footer');
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('members/guru/templates/header', $data);
        if (!($id_kelas != null)) {
        }
        foreach ($cat_kelas as $ck) {
            $ck->reading = unserialize($ck->reading);
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $arrMapel = [];
        $data['cat_siswa'] = $this->kelas->getCatatanMapelSiswa($tp->id_tp, $smt->id_smt, $id_kelas, $id_mapel);
        $id_kelas = $this->input->get('kelas', true);
        $tp = $this->dashboard->getTahunActive();
        foreach ($mapel as $mpl) {
            foreach ($mpl->kelas_mapel as $id_mapel) {
                array_push($arrId, $id_mapel->kelas);
            }
        }
        $this->load->view('_templates/dashboard/_footer');
        $arrKelas = [];
    }
    public function siswa()
    {
        $data['tp_active'] = $tp;
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('members/guru/templates/header', $data);
        $id_mapel = $this->input->get('mapel');
        $this->load->view('members/guru/kelas/catatan/persiswa');
        $data['siswa'] = $this->master->getSiswaById($id_siswa);
        $data = ['user' => $user, 'judul' => 'Catatan Siswa', 'subjudul' => 'Catatan Siswa', 'setting' => $this->dashboard->getSetting()];
        $data['kelas'] = $id_kelas;
        $data['mapel'] = $id_mapel;
        $this->load->view('members/guru/templates/footer');
        $id_kelas = $this->input->get('kelas');
        $smt = $this->master->getSemesterActive();
        $this->load->view('_templates/dashboard/_footer');
        if ($this->ion_auth->is_admin()) {
        }
        $data['catatan_siswa'] = $this->kelas->getAllCatatanMapelSiswa($id_siswa, $id_mapel, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/kelas/catatan/persiswa');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $id_siswa = $this->input->get('id');
        $data['smt_active'] = $smt;
        $tp = $this->master->getTahunActive();
        $data['guru'] = $guru;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
    }
    public function saveCatatanKelas()
    {
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $tp = $this->dashboard->getTahunActive();
        $level = $this->input->post('level', true);
        $id_kelas = $this->input->post('id_kelas');
        $user = $this->ion_auth->user()->row();
        $text = $this->input->post('text', true);
        $this->output_json($insert);
        $id_mapel = $this->input->post('id_mapel', true);
        $insert = $this->master->create('kelas_catatan_mapel', $data);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '1', 'id_mapel' => $id_mapel, 'id_kelas' => $id_kelas, 'id_guru' => $guru->id_guru, 'level' => $level, 'text' => $text, 'reading' => serialize([])];
        $tgl = date('Y-m-d');
    }
    public function saveCatatanSiswa()
    {
        $level = $this->input->post('level', true);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $smt = $this->dashboard->getSemesterActive();
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '2', 'id_mapel' => $id_mapel, 'id_siswa' => $id_siswa, 'id_guru' => $guru->id_guru, 'level' => $level, 'text' => $text, 'reading' => serialize([])];
        $text = $this->input->post('text', true);
        $this->output_json($insert);
        $tp = $this->dashboard->getTahunActive();
        $id_mapel = $this->input->post('id_mapel', true);
        $id_siswa = $this->input->post('id_siswa');
        $insert = $this->master->create('kelas_catatan_mapel', $data);
    }
    public function hapus($id_catatan)
    {
        $delete = $this->master->delete('kelas_catatan_mapel', $id_catatan, 'id_catatan');
        $this->output_json($delete);
    }
}
```

---

## File: application/controllers_decoded/Kelasjadwal.php

```php
<?php

class Kelasjadwal extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Log_model', 'logging');
        $this->load->model('Cbt_model', 'cbt');
        redirect('auth');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
        if ($this->ion_auth->logged_in()) {
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
        $this->load->model('Kelas_model', 'kelas');
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
        $this->load->view('members/guru/kelas/jadwal/data');
        $this->load->view('members/guru/templates/footer');
        $this->load->view('kelas/jadwal/data');
        $data['method'] = '';
        $tp = $this->dashboard->getTahunActive();
        $data['tp_active'] = $tp;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $data['jmlMapel'] = [];
        if ($this->ion_auth->in_group('guru')) {
        }
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['id_kelas'] = '0';
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $this->dashboard->getSetting()];
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/header', $data);
        $data['jmlIst'] = [];
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
    }
    public function kelas($kelas)
    {
        $data['jadwal_kbm'] = $jadk;
        $data = ['user' => $user, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        if ($jadk == null) {
        }
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_footer');
        $data['mapels'] = $this->dropdown->getAllKodeMapel();
        $data['id_kelas'] = $kelas;
        if ($this->ion_auth->is_admin()) {
        }
        foreach ($jadm as $j) {
            $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $kelas)];
        }
        $this->load->view('members/guru/templates/header', $data);
        if ($jadm == null) {
        }
        $user = $this->ion_auth->user()->row();
        if (!($i < $jml_mapel)) {
        }
        $data['jadwal_kbm'] = json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]));
        $data['method'] = 'edit';
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $kelas);
        $data['smt'] = $this->dashboard->getSemester();
        $i = 0;
        $this->load->view('members/guru/kelas/jadwal/data');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['jadwal_mapel'] = $jadwal_mapel;
        $this->load->view('kelas/jadwal/data');
        $jml_mapel = $jadk == null ? 1 : $jadk->kbm_jml_mapel_hari;
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $kelas);
        if ($this->ion_auth->in_group('guru')) {
        }
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $jadwal_mapel[] = ['jadwal' => $this->kelas->getDummyJadwalMapel($tp->id_tp, $smt->id_smt, $i + 1, $kelas)];
        $data['guru'] = $this->dashboard->getDetailGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $data['method'] = 'add';
        $i++;
        $setting = $this->dashboard->getSetting();
        $this->load->view('members/guru/templates/footer');
    }
    public function setJadwal()
    {
        $istirahat = [];
        $this->logging->saveLog(3, 'merubah jadwal pelajaran');
        if (!($i < 5)) {
        }
        $durasi = $this->input->post('dur_ist' . $i, true);
        $i = 1;
        $istirahat[] = ['ist' => $jamke, 'dur' => $durasi];
        if (!$jamke) {
        }
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $jamke = $this->input->post('ist' . $i, true);
        $this->output_json($data);
        $i++;
        $update = $this->db->replace('kelas_jadwal_kbm', $insert);
        $id_kelas = $this->input->post('id_kelas', true);
        $insert = ['id_kbm' => $id_tp . $id_smt . $id_kelas, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $id_kelas, 'kbm_jam_pel' => $this->input->post('jam_mapel', true), 'kbm_jam_mulai' => $this->input->post('jam_mulai', true), 'kbm_jml_mapel_hari' => $this->input->post('jml_mapel', true), 'istirahat' => serialize($istirahat)];
        $data['status'] = $update;
        $id_tp = $this->master->getTahunActive()->id_tp;
    }
    public function setMapel()
    {
        $this->db->delete('kelas_jadwal_mapel');
        $data = [];
        $array = array('id_tp' => $input[0]->id_tp, 'id_smt' => $input[0]->id_smt, 'id_kelas' => $id_kelas);
        $update = $this->db->insert_batch('kelas_jadwal_mapel', $data);
        $id_kelas = $this->input->post('id_kelas', true);
        foreach ($input as $d) {
            $data[] = ['id_jadwal' => $d->id_tp . $d->id_smt . $id_kelas . $d->id_hari . $d->jam_ke, 'id_tp' => $d->id_tp, 'id_smt' => $d->id_smt, 'id_kelas' => $id_kelas, 'id_hari' => $d->id_hari, 'jam_ke' => $d->jam_ke, 'id_mapel' => $d->id_mapel];
        }
        $input = json_decode($this->input->post('data', true));
        $this->output_json($res);
        $res['status'] = $update;
        $this->db->where($array);
    }
}
```

---

## File: application/controllers_decoded/Kelasmaterijadwal.php

```php
<?php

class Kelasmaterijadwal extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Log_model', 'logging');
        $this->load->model('Cbt_model', 'cbt');
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        redirect('auth');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $data['method'] = '';
        $data['tp'] = $this->dashboard->getTahun();
        $data['date_selected'] = $thn . '-' . $bln . '-' . date('d');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $thn = $smt->id_smt == '1' ? $tahun[0] : $tahun[1];
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('kelas/materijadwal/data');
        $data['thn_selected'] = $tp->tahun;
        $data['guru'] = $guru;
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->view('kelas/materijadwal/data');
        $tahun = explode('/', $tp->tahun ?? '');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['smt_active'] = $smt;
        $this->load->view('members/guru/templates/footer');
        $data['id_kelas'] = '0';
        if ($this->ion_auth->in_group('guru')) {
        }
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $bln = $smt->id_smt == '1' ? '7' : '1';
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['bln_selected'] = $bln;
        $data['jmlIst'] = [];
        $data['jmlMapel'] = [];
    }
    public function kelas()
    {
        $tahun = $this->input->get('tahun');
        $data['detail_jadwal_materi'] = isset($semua_materi[1]) ? $semua_materi[1] : [];
        $data['jadwal_mapel'] = $jadwal_mapel;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $tp = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        $jadwal_mapel[] = ['jadwal' => $this->kelas->getDummyJadwalMapel($tp->id_tp, $smt->id_smt, $i + 1, $kelas)];
        $data['tp_active'] = $tp;
        $this->load->view('_templates/dashboard/_footer');
        $i = 0;
        $this->load->view('members/guru/templates/header', $data);
        $data['smt_active'] = $smt;
        $kelas = $this->input->get('kelas');
        $data['guru'] = $this->dashboard->getDetailGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $data['date_selected'] = $date;
        $semua_materi = $this->kelas->getAllJadwalMateriByKelas($tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        if ($jadk == null) {
        }
        $data['method'] = 'add';
        $data['jadwal_kbm'] = $jadk;
        if ($jadm == null) {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $data['detail_jadwal_tugas'] = isset($semua_materi[2]) ? $semua_materi[2] : [];
        $smt = $this->dashboard->getSemesterActive();
        foreach ($jadm as $j) {
            $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $kelas)];
        }
        if ($this->ion_auth->in_group('guru')) {
        }
        $data['thn_selected'] = $tahun;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = $kelas;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('kelas/materijadwal/data');
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $kelas);
        $bulan = $this->input->get('bulan');
        $this->load->view('members/guru/templates/footer');
        $data['mapels'] = $this->master->getAllMapel();
        $jml_mapel = $jadk == null ? 1 : $jadk->kbm_jml_mapel_hari;
        $week = [date('Y-m-d', strtotime('monday this week', strtotime($date))), date('Y-m-d', strtotime('tuesday this week', strtotime($date))), date('Y-m-d', strtotime('wednesday this week', strtotime($date))), date('Y-m-d', strtotime('thursday this week', strtotime($date))), date('Y-m-d', strtotime('friday this week', strtotime($date))), date('Y-m-d', strtotime('saturday this week', strtotime($date)))];
        if (!($i < $jml_mapel)) {
        }
        $date = $this->input->get('date');
        $this->load->view('kelas/materijadwal/data');
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $kelas);
        $data['bln_selected'] = $bulan;
        $data['opsi_materi'] = $this->kelas->getAllMateriByKelas($tp->id_tp, $smt->id_smt);
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Jadwal Materi / Tugas', 'subjudul' => 'Set Jadwal Materi / Tugas', 'setting' => $setting];
        $data['jadwal_kbm'] = json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]));
        $data['week'] = $week;
        $i++;
        $data['method'] = 'edit';
    }
    public function setJadwal()
    {
        $istirahat[] = ['ist' => $jamke, 'dur' => $durasi];
        $this->output_json($data);
        $istirahat = [];
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_kelas = $this->input->post('id_kelas', true);
        if (!$jamke) {
        }
        $insert = ['id_kbm' => $id_tp . $id_smt . $id_kelas, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $id_kelas, 'kbm_jam_pel' => $this->input->post('jam_mapel', true), 'kbm_jam_mulai' => $this->input->post('jam_mulai', true), 'kbm_jml_mapel_hari' => $this->input->post('jml_mapel', true), 'istirahat' => serialize($istirahat)];
        $i++;
        $jamke = $this->input->post('ist' . $i, true);
        $update = $this->db->replace('kelas_jadwal_kbm', $insert);
        $this->logging->saveLog(3, 'merubah jadwal pelajaran');
        $i = 1;
        $data['status'] = $update;
        if (!($i < 5)) {
        }
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $durasi = $this->input->post('dur_ist' . $i, true);
    }
    public function setMapel()
    {
        foreach ($input as $d) {
            $data = ['id_jadwal' => $d->id_tp . $d->id_smt . $id_kelas . $d->id_hari . $d->jam_ke, 'id_tp' => $d->id_tp, 'id_smt' => $d->id_smt, 'id_kelas' => $id_kelas, 'id_hari' => $d->id_hari, 'jam_ke' => $d->jam_ke, 'id_mapel' => $d->id_mapel];
            $update = $this->db->replace('kelas_jadwal_mapel', $data);
        }
        $res['status'] = $update;
        $input = json_decode($this->input->post('data', true));
        $id_kelas = $this->input->post('id_kelas', true);
        $this->output_json($res);
    }
    public function saveJadwal()
    {
        $this->output_json($update);
        $input_materi = json_decode($this->input->post('materi', true));
        foreach ($input_tugas as $im) {
            $insert = ['jenis' => '2', 'id_kjm' => $im->id_kjm, 'id_tp' => $im->id_tp, 'id_smt' => $im->id_smt, 'id_kelas' => $im->id_kelas, 'id_materi' => $im->id_materi, 'id_mapel' => $im->id_mapel, 'jadwal_materi' => $im->jadwal_materi];
            $update = $this->db->replace('kelas_jadwal_materi', $insert);
        }
        $this->logging->saveLog(3, 'merubah jadwal materi dan tugas');
        foreach ($input_materi as $im) {
            $insert = ['jenis' => '1', 'id_kjm' => $im->id_kjm, 'id_tp' => $im->id_tp, 'id_smt' => $im->id_smt, 'id_kelas' => $im->id_kelas, 'id_materi' => $im->id_materi, 'id_mapel' => $im->id_mapel, 'jadwal_materi' => $im->jadwal_materi];
            $update = $this->db->replace('kelas_jadwal_materi', $insert);
        }
        $input_tugas = json_decode($this->input->post('tugas', true));
    }
}
```

---

## File: application/controllers_decoded/Kelasmateri.php

```php
<?php

class Kelasmateri extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        redirect('auth');
        $this->load->library('upload');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model', 'kelas');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->helper('my');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $kelas_materi = [];
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/templates/footer');
        $user = $this->ion_auth->user()->row();
        $data['id_guru'] = $guru->id_guru;
        if (!($id_guru != null)) {
        }
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $setting = $this->dashboard->getSetting();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('kelas/materi/data');
        $jadwal_materi = [];
        $kelas_materi = [];
        $data['kelas_materi'] = $kelas_materi;
        $jenis = $this->input->get('jenis');
        $id_guru = $this->input->get('id');
        if ($this->ion_auth->is_admin()) {
        }
        $data['jadwal_materi'] = $jadwal_materi;
        $data['id_guru'] = $id_guru == null ? '' : $id_guru;
        $data['tp_active'] = $tp;
        $this->load->view('kelas/materi/data');
        $jadwal_materi = [];
        $data['tp'] = $this->dashboard->getTahun();
        $data['gurus'] = $nguru;
        $data['materi'] = $materi;
        array_unshift($allGuru, ['00' => 'Semua Guru']);
        $data['gurus'] = $allGuru;
        foreach ($materi as $m) {
            $km = $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
            if (!($km == null)) {
            }
            $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, $jenis, $tp->id_tp, $smt->id_smt);
            $kelas_materi[$m->id_materi] = $km;
        }
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $allGuru = $this->dropdown->getAllGuru();
        $materi = $this->kelas->getAllMateriKelas($id_guru, '1');
        $data['smt_active'] = $smt;
        $smt = $this->dashboard->getSemesterActive();
        $materi = $this->kelas->getAllMateriKelas($guru->id_guru, '1');
        $data['smt'] = $this->dashboard->getSemester();
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $materi = [];
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'Materi Belajar', 'subjudul' => 'Materi', 'setting' => $setting];
        $data['guru'] = $guru;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $data['jadwal_materi'] = $jadwal_materi;
        $data['materi'] = $materi;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $tp = $this->dashboard->getTahunActive();
        $data['kelas_materi'] = $kelas_materi;
        foreach ($materi as $m) {
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, $jenis, $tp->id_tp, $smt->id_smt);
            $kelas_materi[$m->id_materi] = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
        }
    }
    public function materi()
    {
        $kelas_materi = [];
        $data['jadwal_mapel'] = $jadmpl;
        $arr_kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['kelas_materi'] = $kelas_materi;
        $smt = $this->dashboard->getSemesterActive();
        $data['materi'] = $materi;
        $data['tanggal_jadwal'] = $arr_h;
        $data = ['user' => $user, 'judul' => 'Materi Belajar', 'subjudul' => 'Materi', 'setting' => $setting];
        $data['jadwal_materi'] = $jadwal_materi;
        $id_guru = $this->input->get('id');
        $data['kelas_materi'] = $kelas_materi;
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('kelas/materi/data');
        $jadmpl = $this->kelas->getJadwalMapel($tp->id_tp, $smt->id_smt);
        $data['id_guru'] = $id_guru == null ? '' : $id_guru;
        $arr_h = [];
        $this->load->view('_templates/dashboard/_header', $data);
        $data['jenis'] = '1';
        $kelas_materi = [];
        $materi = [];
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $setting = $this->dashboard->getSetting();
        foreach ($jadmpl as $j => $h) {
            foreach ($h as $v) {
                foreach ($v as $kk => $vk) {
                    $arr_h[$vk->id_mapel] = [];
                    if (in_array($vk->id_hari, $arr_h[$vk->id_mapel])) {
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                    if (isset($arr_h[$vk->id_mapel])) {
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        $data['guru'] = $guru;
        $this->load->view('kelas/materi/data');
        if ($this->ion_auth->is_admin()) {
        }
        $data['tp_active'] = $tp;
        $data['kelas'] = $arr_kelas;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $allGuru['00'] = 'Semua Guru';
        $jadwal_materi = [];
        $materi = $this->kelas->getAllMateriKelas($id_guru, '1');
        $allGuru = $this->dropdown->getAllGuru();
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('members/guru/templates/footer');
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        $data['id_guru'] = $guru->id_guru;
        $materi = $this->kelas->getAllMateriKelas($guru->id_guru, '1');
        if (!($id_guru != null)) {
        }
        $data['materi'] = $materi;
        $jadwal_materi = [];
        foreach ($materi as $m) {
            $arrKls = unserialize($m->materi_kelas);
            $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            if (!(count($arrKls) > 0)) {
            }
            $km = $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
            if (!($km == null)) {
            }
            $kelas_materi[$m->id_materi] = $km;
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '1', $tp->id_tp, $smt->id_smt);
        }
        $data['jadwal_materi'] = $jadwal_materi;
        $data['gurus'] = $nguru;
        $this->load->view('_templates/dashboard/_footer');
        $data['smt_active'] = $smt;
        $data['gurus'] = $allGuru;
        foreach ($materi as $m) {
            $kelas_materi[$m->id_materi] = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '1', $tp->id_tp, $smt->id_smt);
        }
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $this->load->view('members/guru/templates/header', $data);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
    }
    public function tugas()
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['gurus'] = $nguru;
        $data['materi'] = $materi;
        $data['tp_active'] = $tp;
        $materi = $this->kelas->getAllMateriKelas($id_guru, '2');
        if ($this->ion_auth->is_admin()) {
        }
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['jadwal_mapel'] = $jadmpl;
        foreach ($materi as $m) {
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '2', $tp->id_tp, $smt->id_smt);
            $kelas_materi[$m->id_materi] = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
        }
        $data['kelas_materi'] = $kelas_materi;
        $this->load->view('members/guru/templates/footer');
        $data['gurus'] = $allGuru;
        $setting = $this->dashboard->getSetting();
        $data['kelas'] = $arr_kelas;
        $data['smt'] = $this->dashboard->getSemester();
        $data['id_guru'] = $id_guru == null ? '' : $id_guru;
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $arr_h = [];
        $id_guru = $this->input->get('id');
        $data['materi'] = $materi;
        $data['id_guru'] = $guru->id_guru;
        foreach ($jadmpl as $j => $h) {
            foreach ($h as $v) {
                foreach ($v as $kk => $vk) {
                    $arr_h[$vk->id_mapel] = [];
                    if (in_array($vk->id_hari, $arr_h[$vk->id_mapel])) {
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                    if (isset($arr_h[$vk->id_mapel])) {
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        $this->load->view('kelas/materi/data');
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        foreach ($materi as $m) {
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '2', $tp->id_tp, $smt->id_smt);
            $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $arrKls = unserialize($m->materi_kelas);
            if (!($km == null)) {
            }
            $kelas_materi[$m->id_materi] = $km;
            if (!(count($arrKls) > 0)) {
            }
            $km = $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
        }
        $user = $this->ion_auth->user()->row();
        $materi = [];
        $materi = $this->kelas->getAllMateriKelas($guru->id_guru, '2');
        $this->load->view('kelas/materi/data');
        $data['tanggal_jadwal'] = $arr_h;
        $data['kelas_materi'] = $kelas_materi;
        $jadmpl = $this->kelas->getJadwalMapel($tp->id_tp, $smt->id_smt);
        $kelas_materi = [];
        $jadwal_materi = [];
        $data['jadwal_materi'] = $jadwal_materi;
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $allGuru = $this->dropdown->getAllGuru();
        $data['tp'] = $this->dashboard->getTahun();
        $data = ['user' => $user, 'judul' => 'Tugas Kelas', 'subjudul' => 'Tugas', 'setting' => $setting];
        $data['jadwal_materi'] = $jadwal_materi;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $allGuru['00'] = 'Semua Guru';
        $data['guru'] = $guru;
        $kelas_materi = [];
        $this->load->view('members/guru/templates/header', $data);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $arr_kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $jadwal_materi = [];
        $smt = $this->dashboard->getSemesterActive();
        $data['jenis'] = '2';
        if (!($id_guru != null)) {
        }
    }
    public function data($guru = null)
    {
        $tp = $this->dashboard->getTahunActive();
        $this->output_json($this->kelas->getMateriKelas($guru, $tp->id_tp, $smt->id_smt), false);
        $smt = $this->dashboard->getSemesterActive();
    }
    public function add($jenis, $id_materi = null)
    {
        $data['gurus'] = $nguru;
        $data['materi'] = $this->kelas->getMateriKelasById($id_materi, $jenis);
        $data['jenis'] = $jenis;
        $this->load->view('kelas/materi/add');
        $data = ['user' => $user, 'judul' => $title, 'subjudul' => $id_materi == null ? 'Buat ' . $title . ' Baru' : 'Edit ' . $title, 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/templates/header', $data);
        $user = $this->ion_auth->user()->row();
        $data['guru'] = $guru;
        $materi = $this->kelas->getMateriKelasById($id_materi, $jenis);
        if ($this->ion_auth->is_admin()) {
        }
        if ($id_materi == null) {
        }
        $this->load->view('_templates/dashboard/_footer');
        if ($id_materi == null) {
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('members/guru/templates/footer');
        $data['gurus'] = $this->dropdown->getAllGuru();
        $data['materi'] = $materi;
        $data['materi'] = json_decode(json_encode($this->kelas->getDummyMateri()));
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['smt'] = $this->dashboard->getSemester();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp_active'] = $tp;
        $data['id_guru'] = $materi->id_guru;
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['id_guru'] = '';
        $data['materi'] = json_decode(json_encode($this->kelas->getDummyMateri()));
        $data['id_materi'] = $id_materi;
        $this->load->view('kelas/materi/add');
        $data['id_guru'] = $guru->id_guru;
        $title = $jenis == '1' ? 'Materi' : 'Tugas';
        $tp = $this->dashboard->getTahunActive();
    }
    public function dataAddKelas($guru)
    {
        $guru = $this->kelas->getGuruMapelKelas($guru, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        $kelas = unserialize($guru->mapel_kelas);
        $this->output_json($kelas);
        $smt = $this->dashboard->getSemesterActive();
    }
    public function dataAddJadwal()
    {
        $id_mapel = $this->input->get('mapel');
        $this->output_json(['mapel' => $mapel, 'terisi' => $jadwal_terisi]);
        $tp = $this->dashboard->getTahunActive();
        $id_kelas = $this->input->get('kelas');
        $smt = $this->dashboard->getSemesterActive();
        $mapel = $this->kelas->getJadwalMapelByMapel($id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
        $jadwal_terisi = $this->kelas->getJadwalTerisi('kelas_jadwal_materi', $id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
    }
    public function saveJadwal()
    {
        $id_kelas = $this->input->post('id_kelas', true);
        $this->output_json($update);
        $jadwal = $this->input->post('jadwal_materi', true);
        $update = $this->db->replace('kelas_jadwal_materi', $insert);
        $jdwl = str_replace('-', '', $jadwal ?? '');
        $this->logging->saveLog(3, 'merubah jadwal materi');
        $smt = $this->dashboard->getSemesterActive();
        $tp = $this->dashboard->getTahunActive();
        $jenis = $this->input->post('jenis', true);
        $id_mapel = $this->input->post('id_mapel', true);
        $insert = ['id_kjm' => $id_kelas . $tp->id_tp . $smt->id_smt . $jdwl . $jam_ke . $jenis, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $id_kelas, 'id_materi' => $id_materi, 'id_mapel' => $id_mapel, 'jadwal_materi' => $jadwal, 'jenis' => $jenis];
        $jam_ke = $this->input->post('jam_ke', true);
        $id_materi = $this->input->post('id_materi', true);
    }
    public function hapusJadwal($id)
    {
        $this->db->where('id_kjm', $id);
        $this->db->set('id_materi', '0');
        $this->output_json($update);
        $update = $this->db->update('kelas_jadwal_materi');
    }
    public function saveMateri()
    {
        $data['id_materi'] = $id_materi;
        $cek_materi = $this->kelas->getMateriKelasById($id_materi, $jenis);
        $id_kelas = [];
        $saved = $this->master->create('kelas_materi', $data);
        if (!($i < $kelas)) {
        }
        $result['result_id'] = $this->db->insert_id();
        $src_file = [];
        if ($id_materi === '') {
        }
        $result['status'] = $saved;
        $attach = json_decode($this->input->post('attach', true));
        $data['updated_on'] = date('Y-m-d H:i:s');
        $result['message'] = 'Materi berhasil diupdate';
        $saved = $this->master->update('kelas_materi', $data, 'id_materi', $id_materi);
        $kelas = count($this->input->post('kelas', true));
        $dom = new DOMDocument();
        $result['status'] = $saved;
        $i++;
        $result['status'] = $saved;
        $result['message'] = 'Materi berhasil dibuat';
        $jenis = $this->input->post('jenis', true);
        $this->logging->saveLog(4, 'mengedit materi');
        $tp = $this->dashboard->getTahunActive();
        $data['created_on'] = date('Y-m-d H:i:s');
        if ($cek_materi->id_tp == $tp->id_tp && $cek_materi->id_smt == $smt->id_smt) {
        }
        $id_materi = $this->input->post('id_materi', true);
        $saved = $this->master->create('kelas_materi', $data);
        $this->logging->saveLog(3, 'membuat materi');
        $dom->loadHTML($isi_materi, LIBXML_HTML_NODEFDTD);
        $isi = $dom->saveHTML();
        $data['updated_on'] = date('Y-m-d H:i:s');
        $images = $dom->getElementsByTagName('img');
        $numimg = 1;
        $smt = $this->dashboard->getSemesterActive();
        $i = 0;
        $data['created_on'] = date('Y-m-d H:i:s');
        $this->output_json($result);
        $data['updated_on'] = date('Y-m-d H:i:s');
        $isi_materi = $this->input->post('isi_materi', false);
        $this->logging->saveLog(3, 'membuat materi');
        $data = ['jenis' => $jenis, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'kode_materi' => $this->input->post('kode_materi', true), 'id_guru' => $this->input->post('guru', true), 'id_mapel' => $this->input->post('mapel', true), 'judul_materi' => $this->input->post('judul', true), 'isi_materi' => $isi, 'materi_kelas' => serialize($id_kelas), 'file' => serialize($src_file)];
        $id_kelas[] = $this->input->post('kelas[' . $i . ']', true);
        foreach ($attach as $at) {
            if (!($at->name != null)) {
            }
            $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
        }
        $result['message'] = 'Materi berhasil dibuat';
        foreach ($images as $image) {
            $base64_image_string = $image->getAttribute('src');
            $extension = $mime_split[1];
            $image->setAttribute('src', 'uploads/materi/' . $output_file);
            if (!(count($mime_split) == 2)) {
            }
            if (strpos($base64_image_string, 'http') !== false) {
            }
            $output_file = '';
            $data = $splited[1];
            $mime = $splited[0];
            $numimg++;
            $forReplace = explode($pathUpload, $base64_image_string);
            $mime_split_without_base64 = explode(';', $mime, 2);
            file_put_contents('./uploads/materi/' . $output_file, base64_decode($data));
            $output_file = 'img_' . date('YmdHis') . $numimg . '.' . $extension;
            $image->setAttribute('src', $pathUpload . $forReplace[1]);
            $pathUpload = 'uploads';
            $mime_split = explode('/', $mime_split_without_base64[0], 2);
            if (!($extension == 'jpeg')) {
            }
            $splited = explode(',', substr($base64_image_string, 5), 2);
            $extension = 'jpg';
        }
    }
    public function copyMateri($id_materi, $jenis)
    {
        $this->output_json($result);
        $smt = $this->dashboard->getSemesterActive();
        $result = $this->master->create('kelas_materi', $data);
        $data = ['jenis' => $jenis, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'kode_materi' => $materi->kode_materi, 'id_guru' => $materi->id_guru, 'id_mapel' => $materi->id_mapel == null ? 0 : $materi->id_mapel, 'judul_materi' => $materi->judul_materi, 'isi_materi' => $materi->isi_materi, 'materi_kelas' => $materi->materi_kelas, 'file' => $materi->file, 'created_on' => date('Y-m-d H:i:s'), 'updated_on' => date('Y-m-d H:i:s')];
        $this->logging->saveLog(3, 'membuat materi');
        $tp = $this->dashboard->getTahunActive();
        $materi = $this->kelas->getMateriKelasById($id_materi, $jenis);
    }
    public function aktifkanMateri()
    {
        $method = $this->input->post('method', true);
        $this->output_json(['status' => true]);
        $this->db->update('kelas_materi');
        $this->db->where('id_materi', $id);
        $stat = $method == '1' ? '0' : '1';
        $this->logging->saveLog(3, 'mengaktifkan materi');
        $this->db->set('status', $stat);
        $id = $this->input->post('id_materi', true);
    }
    public function hapusMateri()
    {
        if (!$this->master->delete('kelas_materi', $id, 'id_materi')) {
        }
        $id = $this->input->post('id_materi', true);
        $this->output_json(['status' => true]);
        if (!$this->master->delete('kelas_jadwal_materi', $id, 'id_materi')) {
        }
        $this->logging->saveLog(5, 'menghapus materi');
    }
    public function deleteAllMateri()
    {
        $ids = json_decode($this->input->post('ids', true));
        if (!$this->master->delete('kelas_materi', $ids, 'id_materi')) {
        }
        $this->output_json(['status' => true]);
        $this->logging->saveLog(5, 'menghapus materi');
        if (!$this->master->delete('kelas_jadwal_materi', $ids, 'id_materi')) {
        }
    }
    function uploadFile()
    {
        $data['type'] = $_FILES['file_uploads']['type'];
        $this->output_json($data);
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $config['overwrite'] = TRUE;
        $max_size = $this->input->post('max-size', true);
        $data['src'] = 'uploads/materi/' . $result['file_name'];
        if (!$this->upload->do_upload('file_uploads')) {
        }
        $this->upload->initialize($config);
        if (!isset($_FILES['file_uploads']['name'])) {
        }
        $data['status'] = false;
        $data['src'] = $this->upload->display_errors();
        $result = $this->upload->data();
        $data['size'] = $_FILES['file_uploads']['size'];
        $data['status'] = true;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi|doc|docx|xls|xlsx|ppt|pptx|csv|pdf|rtf|txt';
        $config['max_size'] = $max_size;
        $config['upload_path'] = './uploads/materi/';
    }
    function deleteFile()
    {
        echo 'Gagal';
        if (unlink($src)) {
        }
        echo 'File Delete Successfully';
        $src = $this->input->post('src');
    }
    function getListDate($day, $month, $year)
    {
        array_push($list, date('Y-m-d', $time));
        if (!($d <= $numdays)) {
        }
        $list = array();
        $d = 1;
        if (!(date('m', $time) == $month && $day_of_week == $day)) {
        }
        $day_of_week = date('N', $time);
        $numdays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $time = mktime(12, 0, 0, $month, $d, $year);
        return $list;
        $d++;
    }
}
```

---

## File: application/controllers_decoded/Kelasnilai.php

```php
<?php

class Kelasnilai extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Master_model', 'master');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->form_validation->set_error_delimiters('', '');
        redirect('auth');
        $this->load->model('Dashboard_model', 'dashboard');
        if (!$this->ion_auth->logged_in()) {
        }
        parent::__construct();
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
        $data['guru'] = $guru;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        if ($this->ion_auth->is_admin()) {
        }
        $arrKelas = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $data['tp_active'] = $tp;
        $this->load->view('kelas/nilai/data');
        $tp = $this->dashboard->getTahunActive();
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel = [];
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('kelas/nilai/data');
        $user = $this->ion_auth->user()->row();
        $data['arrkelas'] = $arrKelas;
        $this->load->view('_templates/dashboard/_header', $data);
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        if (!($mapel != null)) {
        }
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('members/guru/templates/header', $data);
        $data['smt_active'] = $smt;
        $data['mapel'] = $arrMapel;
        $data = ['user' => $user, 'judul' => 'Rekapitulasi Nilai Siswa', 'subjudul' => 'Nilai dalam satu semester', 'setting' => $this->dashboard->getSetting()];
        $data['smt'] = $this->dashboard->getSemester();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        foreach ($mapel[0]->kelas_mapel as $id_mapel) {
            array_push($arrId, $id_mapel->kelas);
        }
        $this->load->view('members/guru/templates/footer');
        $data['kelas'] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
        if (!($mapel != null)) {
        }
        $arrId = [];
        $data['mapel'] = $this->dropdown->getAllMapel();
        $data['id_guru'] = $guru->id_guru;
    }
    public function loadNilaiMapel()
    {
        $log_materi = [];
        $mapel = $this->input->get('mapel');
        $log = [];
        $kelas = $this->input->get('kelas');
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $data = ['log' => $log, 'materi' => $jadwal_materi, 'bulans' => $arrBulan, 'mapels' => $jadwal_per_bulan, 'nilai' => $log_siswa, 'cols' => $cols];
        $jadwal_materi = [];
        foreach ($siswa as $s) {
            $log[$s->id_siswa] = ['nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->nama_kelas, 'nilai_materi' => isset($log_siswa[1][$s->id_siswa]) ? $log_siswa[1][$s->id_siswa] : [], 'nilai_tugas' => isset($log_siswa[2][$s->id_siswa]) ? $log_siswa[2][$s->id_siswa] : []];
        }
        $data['mapels'] = [];
        if ($smt == '1') {
        }
        $cols = 0;
        $tahun = $this->input->get('tahun');
        $this->output_json($data);
        $jadwal_per_bulan = [];
        $stahun = $this->input->get('stahun');
        foreach ($arrBulan as $bulan) {
            foreach ($infos as $info) {
                $mtr = null;
                $tgs = null;
                $jadwal_per_bulan[$info->id_hari][$info->jam_ke] = $info;
                foreach ($dates as $date) {
                    $d = explode('-', $date ?? '');
                    $t = $d[2];
                    $jj = $this->kelas->getAllMateriByTgl($kelas, $date, [$mapel]);
                    $mtr = isset($jj[$mapel]) && isset($jj[$mapel][$info->jam_ke]) && isset($jj[$mapel][$info->jam_ke][1]) ? $jj[$mapel][$info->jam_ke][1] : null;
                    $b = $d[1];
                    $jadwal_materi[$b][$t][$info->jam_ke][1] = $mtr;
                    $cols++;
                    $tgs = isset($jj[$mapel]) && isset($jj[$mapel][$info->jam_ke]) && isset($jj[$mapel][$info->jam_ke][2]) ? $jj[$mapel][$info->jam_ke][2] : null;
                    $jadwal_materi[$b][$t][$info->jam_ke][2] = $tgs;
                }
                $dates = $this->total_hari($info->id_hari, $bulan, $stahun);
            }
        }
        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'Nopember', 'Desember'];
        $arrBulan = ['07', '08', '09', '10', '11', '12'];
        $smt = $this->input->get('smt');
        $arrBulan = ['01', '02', '03', '04', '05', '06'];
        if (count($siswa) > 0 && count($jadwal_per_bulan) > 0) {
        }
        $infos = $this->kelas->getJadwalMapelByMapel($kelas, $mapel, $tahun, $smt);
        $siswa = $this->kelas->getKelasSiswa($kelas, $tahun, $smt);
        $log_siswa = $this->kelas->getRekapMateriSemester($kelas);
    }
    function total_hari($id_day, $bulan, $taun)
    {
        if (!($i < $total_days)) {
        }
        $idday = $id_day == '7' ? 0 : $id_day;
        $days++;
        array_push($dates, date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i)));
        $dates = [];
        if (!(date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday)) {
        }
        return $dates;
        $days = 0;
        $i = 1;
        $i++;
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
    }
}
```

---

## File: application/controllers_decoded/Kelasstatus.php

```php
<?php

class Kelasstatus extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        redirect('auth');
        $this->form_validation->set_error_delimiters('', '');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
    }
    public function index()
    {
        if ($this->ion_auth->is_admin()) {
        }
        foreach ($mapel[0]->kelas_mapel as $id_mapel) {
            array_push($arrId, $id_mapel->kelas);
        }
        $this->load->view('kelas/status/data');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('_templates/dashboard/_footer');
        $data['kelas'] = $arrKelas;
        if (!($mapel != null)) {
        }
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $guru = $this->dropdown->getAllGuru();
        $smt = $this->dashboard->getSemesterActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['gurus'] = $nguru;
        $data['mapels'] = $this->dropdown->getAllMapel();
        $this->load->view('kelas/status/data');
        $data['id_guru'] = $guru->id_guru;
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->view('members/guru/templates/header', $data);
        if (!($mapel != null)) {
        }
        $data['mapels'] = $arrMapel;
        $arrId = [];
        $arrKelas = [];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $arrMapel = [];
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $data['mapel'] = $mapel;
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
            }
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $this->load->model('Kelas_model', 'kelas');
        $data['smt_active'] = $smt;
        $data['gurus'] = $guru;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('members/guru/templates/footer');
        $data = ['user' => $user, 'judul' => 'Nilai Harian Siswa', 'subjudul' => 'Nilai', 'setting' => $this->dashboard->getSetting()];
        $user = $this->ion_auth->user()->row();
    }
    public function getMateriGuru()
    {
        foreach ($materi as $m) {
            $arrKelasTugas[] = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'kelas' => unserialize($m->materi_kelas ?? '')];
            $kode_mapel = $m->kode_mapel == null ? '--' : $m->kode_mapel;
            if ($m->jenis == '1') {
            }
            $arrKelasMateri[] = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'kelas' => unserialize($m->materi_kelas ?? '')];
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->output_json(array('materi' => $arrKelasMateri, 'tugas' => $arrKelasTugas));
        $materi = $this->kelas->getAllKodeMateri($tp->id_tp, $smt->id_smt, $id_guru);
        $id_guru = $this->input->get('id', true);
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Kelas_model', 'kelas');
        $tp = $this->dashboard->getTahunActive();
        $arrKelasTugas = [];
        $arrKelasMateri = [];
    }
    public function getMateriMapel()
    {
        $arrKelasMateri = [];
        $this->load->model('Kelas_model', 'kelas');
        $id_guru = $this->input->get('id_guru', true);
        $arrKelasTugas = [];
        $smt = $this->dashboard->getSemesterActive();
        $materi = $this->kelas->getKodeMateriMapel($tp->id_tp, $smt->id_smt, $id_mapel, $id_guru);
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($materi as $m) {
            if (isset($arrKelas[$m->jenis])) {
            }
            $arrMateri = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'guru' => $m->nama_guru, 'jenis' => $m->jenis];
            $arrKelas[$m->jenis] = [];
            if (isset($arrKelasTugas[$m->id_kelas])) {
            }
            if ($m->jenis == '1') {
            }
            if (isset($arrKelasMateri[$m->id_kelas])) {
            }
            $arrKelasTugas[$m->id_kelas][] = $arrTugas;
            if (in_array($m->id_kelas, $arrKelas[$m->jenis])) {
            }
            $arrKelasTugas[$m->id_kelas] = [];
            $arrKelas[$m->jenis][] = $m->id_kelas;
            $arrKelasMateri[$m->id_kelas][] = $arrMateri;
            $arrKelas[$m->jenis][] = $m->id_kelas;
            $arrKelasMateri[$m->id_kelas] = [];
            $kode_mapel = $m->kode_mapel == null ? '--' : $m->kode_mapel;
            $arrTugas = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'guru' => $m->nama_guru, 'jenis' => $m->jenis];
        }
        $arrKelas = [];
        $tp = $this->dashboard->getTahunActive();
        $this->output_json(array('materi' => $arrKelasMateri, 'tugas' => $arrKelasTugas, 'kelas' => $arrKelas));
        $id_mapel = $this->input->get('id', true);
    }
    public function loadStatus()
    {
        $numday = date('N', strtotime($materi->jadwal_materi));
        if (in_array($jamke, $arrIst)) {
        }
        $detail = [];
        try {
            $jamMulai->add(new DateInterval('PT' . $info->kbm_jam_pel . 'M'));
            $jam_mapel[$jamke] = ['dari' => $jamMulai->format('H:i'), 'sampai' => $jamSampai->format('H:i'), 'tgl' => $materi->jadwal_materi];
            $jamSampai->add(new DateInterval('PT' . $info->kbm_jam_pel . 'M'));
        } catch (Exception $e) {
        }
        $jenis = $label === 'Materi' ? '1' : '2';
        $label = $this->input->post('label', true);
        $log = [];
        $this->output_json(['log' => $log, 'jadwal' => $info, 'materi' => $materi, 'detail' => $detail]);
        $info = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        $jam_mapel = [];
        $i = 0;
        $kelas_materi = $this->kelas->getNamaKelasById([$id_kelas]);
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($siswa as $s) {
            if (!$selesai) {
            }
            $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
            $jam_siswa = new DateTime(date('Y-m-d H:i:s', strtotime($mulai)));
            $log[$s->id_siswa] = ['nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->nama_kelas, 'login' => $this->kelas->getLoginSiswa($s->username), 'mulai' => $mulai, 'selesai' => $selesai, 'text' => isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->text : '', 'nilai' => isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->nilai : '', 'catatan' => isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->catatan : '', 'jam_ke' => isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->jam_ke : null, 'jadwal_materi' => isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->jadwal_materi : null, 'file' => isset($logs[$s->id_siswa]) && $logs[$s->id_siswa]->file != null ? unserialize($logs[$s->id_siswa]->file ?? '') : [], 'diff' => $diff, 'j_materi' => $jam_materi['sampai']];
            $diff = null;
            $interval = $jam_siswa->diff($jam_jadwal);
            $time_siswa = strtotime($mulai);
            $diff = ['days' => $interval->days, 'hari' => $interval->d, 'jam' => $interval->h, 'menit' => $interval->i, 'detik' => $interval->s, 'total' => $minutes, 'interval' => (int) $interval->format('%r%H:%i:%s'), 'terlambat' => $time_siswa - $time_jadwal > 0];
            $jam_jadwal = new DateTime(date('Y-m-d H:i:s', strtotime($materi->jadwal_materi . ' ' . $jam_materi['sampai'])));
            $time_jadwal = strtotime($materi->jadwal_materi . ' ' . $jam_materi['sampai']);
            $mulai = isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->log_time : null;
            $selesai = isset($logs[$s->id_siswa]) ? $logs[$s->id_siswa]->finish_time : null;
        }
        $this->load->model('Kelas_model', 'kelas');
        $arrDur = [];
        $info->istirahat = unserialize($info->istirahat ?? '');
        if (!$materi) {
        }
        $this->load->model('Master_model', 'master');
        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        foreach ($ist as $istirahat) {
            $arrIst[] = $istirahat->ist;
            $arrDur[$istirahat->ist] = $istirahat->dur;
        }
        $jamSampai = new DateTime($info->kbm_jam_mulai);
        $id_kjm = $this->input->post('id_kjm', true);
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $jadwals = $this->kelas->loadJadwalSiswaHariIni($id_tp, $id_smt, $id_kelas, $numday, false);
        $detail = ['mapel' => $materi->nama_mapel, 'judul' => $materi->judul_materi, 'guru' => $materi->nama_guru, 'kelas' => $kelas_materi[$id_kelas], 'jam_ke' => $jadwal->jam_ke, 'waktu' => $jam_materi];
        $logs = $this->kelas->getStatusMateriSiswa($id_kjm);
        $id_tp = $this->master->getTahunActive()->id_tp;
        $materi = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis);
        $jamke = $i + 1;
        $i++;
        $jam_materi = $jam_mapel[$jadwal->jam_ke];
        if (!($i < $info->kbm_jml_mapel_hari)) {
        }
        $jam_materi = [];
        $arrIst = [];
        $id_kelas = $this->input->post('id_kelas', true);
        $jadwal = $jadwals[$key];
        if (!($info != null)) {
        }
        try {
            $jam_mapel[$jamke] = ['dari' => $jamMulai->format('H:i'), 'sampai' => $jamSampai->format('H:i'), 'tgl' => $materi->jadwal_materi];
            $jamMulai->add(new DateInterval('PT' . $arrDur[$jamke] . 'M'));
            $jamSampai->add(new DateInterval('PT' . $arrDur[$jamke] . 'M'));
        } catch (Exception $e) {
        }
        $ist = json_decode(json_encode($info->istirahat));
        $jamMulai = new DateTime($info->kbm_jam_mulai);
        $key = array_search($materi->id_mapel, array_column($jadwals, 'id_mapel'));
    }
    public function saveNilai()
    {
        $this->db->set('id_log', $id_log);
        $method = $this->input->post('method', true);
        $update = $this->db->insert('log_materi', $insert);
        $this->db->where('id_log', $id_log);
        $this->output_json($update);
        $this->db->where('id_log', $id_log);
        $update = $this->db->update('log_materi', $insert);
        $id_log = $this->input->post('id_log', true);
        $label = $this->input->post('label', true);
        $nilai = $this->input->post('nilai', true);
        $q = $this->db->get('log_materi');
        if ($q->num_rows() > 0) {
        }
        $insert = ['nilai' => $nilai, 'catatan' => $catatan];
        $catatan = $this->input->post('catatan', true);
    }
}
```

---

## File: application/controllers_decoded/Pengumuman.php

```php
<?php

class Pengumuman extends CI_Controller
{
    public function __construct()
    {
        redirect('auth');
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Post_model', 'post');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->library(['datatables', 'form_validation']);
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('pengumuman/data');
        $this->load->view('_templates/dashboard/_header', $data);
        $smt = $this->master->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $kelas = $this->dropdown->getAllKeyKodeKelas($tp->id_tp, $smt->id_smt);
        $data['running_text'] = $this->dashboard->getRunningText();
        $data['pengumumans'] = $this->post->getPostUser($guru->id_guru);
        $this->load->view('members/guru/templates/header', $data);
        if ($this->ion_auth->is_admin()) {
        }
        $data['smt_active'] = $smt;
        $data = ['user' => $user, 'judul' => 'Pengumuman', 'setting' => $this->dashboard->getSetting()];
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $user = $this->ion_auth->user()->row();
        $data['subjudul'] = 'Pengumuman Anda';
        $tp = $this->master->getTahunActive();
        $data['tp_active'] = $tp;
        $this->load->view('pengumuman/data');
        $data['gurus'] = $this->dropdown->getAllGuru();
        $data['pengumumans'] = $this->post->getPostUser(0);
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
        $data['kelas'] = $kelas;
        $data['guru'] = $guru;
        $data['subjudul'] = 'Semua Pengumuman';
        $this->load->view('members/guru/templates/footer');
    }
    public function kepada($kepada, $id_kepada = null)
    {
        if ($this->ion_auth->is_admin()) {
        }
        foreach ($pengumumans as $pengumuman) {
            $comments[$pengumuman->id_post] = $comment;
            $this->db->from('post_comments a');
            $this->db->order_by('a.tanggal', 'desc');
            $comment = $this->db->get()->result();
            $this->db->where('a.id_post', $pengumuman->id_post);
            foreach ($comment as $comm) {
                $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
                $balasan[$pengumuman->id_post][$comm->id_comment] = $this->db->get()->result();
                $this->db->select('a.*, b.nama_guru, b.foto');
                $this->db->from('post_reply a');
                $this->db->where('a.id_comment', $comm->id_comment);
                $this->db->order_by('a.tanggal', 'desc');
            }
            $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
            $this->db->select('a.*, b.nama_guru, b.foto');
        }
        $data['tp_active'] = $tp;
        if ($kepada === 'semua_siswa') {
        }
        $comments = [];
        $data['pengumumans'] = $pengumumans;
        $user = $this->ion_auth->user()->row();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $data['smt_active'] = $smt;
        $this->load->view('pengumuman/data');
        $data['kepada'] = urldecode($kepada);
        $this->load->view('_templates/dashboard/_footer');
        $pengumumans = $this->db->get()->result();
        $balasan = [];
        $data['kepada'] = 'Semua Guru';
        if ($kepada === 'semua_guru') {
        }
        $this->load->view('members/guru/templates/footer');
        $this->load->view('members/guru/templates/header', $data);
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Pengumuman', 'subjudul' => 'Semua Pengumuman', 'setting' => $this->dashboard->getSetting()];
        $data['comments'] = $comments;
        $this->db->select('a.*, b.nama_guru, b.foto');
        $data['gurus'] = $this->dropdown->getAllGuru();
        $this->db->order_by('a.tanggal', 'desc');
        $this->load->view('_templates/dashboard/_header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $this->db->from('post a');
        $data['smt'] = $this->dashboard->getSemester();
        $data['kelas'] = $kelas;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('pengumuman/data');
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['kepada'] = 'Semua Siswa';
        $data['balasans'] = $balasan;
    }
    public function getPost()
    {
        $post = $this->post->getPostForUser(null);
        $this->output_json($post);
    }
    public function getComment($id_post, $page)
    {
        $this->db->from('post_comments a');
        $this->db->limit($perPage, $offset);
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $comment = $this->db->get()->result();
        $offset = $page * $perPage;
        $this->output_json($comment);
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $perPage = 5;
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->where('a.id_post', $id_post);
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
    }
    public function getReplies($id_comment, $page)
    {
        $replies = $this->db->get()->result();
        $this->db->limit($perPage, $offset);
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $offset = $page * $perPage;
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->output_json($replies);
        $perPage = 5;
        $this->db->from('post_reply a');
        $this->db->where('a.id_comment', $id_comment);
    }
    public function save()
    {
        $dari = $this->input->post('dari');
        $kepada = json_decode(json_encode($this->input->post('kepada[]', true)));
        $data = ['kepada' => serialize($kepada), 'dari' => $dari, 'dari_group' => $dari == '0' ? '1' : '2', 'text' => $this->input->post('text'), 'tanggal' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')];
        $this->output_json($insert);
        $insert = $this->db->replace('post', $data);
    }
    public function saveKomentar()
    {
        $data = ['id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $this->db->from('post_comments a');
        $tp = $this->master->getTahunActive();
        $dari_group = 2;
        if ($this->ion_auth->is_admin()) {
        }
        $dari = '0';
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $insert = $this->db->replace('post_comments', $data);
        $smt = $this->master->getSemesterActive();
        $this->db->order_by('a.tanggal', 'desc');
        $user = $this->ion_auth->user()->row();
        $dari = $guru->id_guru;
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $id = $this->db->insert_id();
        $dari_group = 1;
        $comment = $this->db->get()->result();
        $this->output_json($comment);
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->where('a.id_comment', $id);
    }
    public function saveBalasan()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $dari = '0';
        $id = $this->db->insert_id();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->from('post_reply a');
        $this->db->where('a.id_reply', $id);
        $insert = $this->db->replace('post_reply', $data);
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        if ($this->ion_auth->is_admin()) {
        }
        $dari_group = 1;
        $this->output_json($replies);
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $user = $this->ion_auth->user()->row();
        $replies = $this->db->get()->result();
        $dari_group = 2;
        $tp = $this->master->getTahunActive();
        $dari = $guru->id_guru;
        $smt = $this->master->getSemesterActive();
    }
    public function hapusPost($id_post)
    {
        $this->db->where('id_post', $id_post);
        $comments = $this->post->getIdComments($id_post);
        $deleted = $this->db->delete('post');
        $this->db->where('id_post', $id_post);
        $this->output_json($deleted);
        $this->db->trans_complete();
        if (!$this->db->delete('post_comments')) {
        }
        $this->db->trans_start();
        foreach ($comments as $comment) {
            $this->db->where('id_comment', $comment->id_comment);
            $deleted['balasan'] = $this->db->delete('post_reply');
        }
    }
    public function hapusKomentar($id_comment)
    {
        $this->db->where('id_comment', $id_comment);
        $this->db->trans_complete();
        $this->db->where('id_comment', $id_comment);
        $deleted['komentar'] = $this->db->delete('post_comments');
        $this->output_json($deleted);
        $this->db->trans_start();
        $deleted['balasan'] = $this->db->delete('post_reply');
    }
    public function hapusBalasan($id_reply)
    {
        $deleted['balasan'] = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
        $this->db->where('id_reply', $id_reply);
        $this->db->trans_start();
    }
    public function getRunningText()
    {
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->output_json($data);
    }
    public function saveRunningText()
    {
        foreach ($input as $d) {
            $update = $this->db->replace('running_text', $data);
            array_push($updates, $update);
            $data = ['id_text' => $d->id_text, 'text' => $d->text];
        }
        $input = json_decode($this->input->post('text', true));
        $this->output_json($data);
        $data['status'] = $updates;
        $updates = [];
    }
    public function hapusRunningText($id)
    {
        $this->db->where('id_text', $id);
        $this->output_json($deleted);
        $deleted = $this->db->delete('running_text');
    }
}
```

---

## File: application/controllers_decoded/Rapor.php

```php
<?php

class Rapor extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Rapor_model', 'rapor');
        $this->load->dbforge();
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->database();
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Dropdown_model', 'dropdown');
        redirect('auth');
        parent::__construct();
        $this->load->model('Master_model', 'master');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $tp = $this->dashboard->getTahunActive();
        $no_update = $this->db->field_exists('nip_kepsek', 'rapor_admin_setting');
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->dbforge->add_column('rapor_admin_setting', $field);
        $this->load->model('Dashboard_model', 'dashboard');
        redirect('rapor/raporkkm');
        if ($this->ion_auth->is_admin()) {
        }
        $data['tp_active'] = $tp;
        if ($no_update) {
        }
        $data['rapor'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $field = array('nip_kepsek' => array('type' => 'int', 'constraint' => 1, 'default' => 0), 'nip_walikelas' => array('type' => 'int', 'constraint' => 1, 'default' => 0));
        $data['smt_active'] = $smt;
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_header', $data);
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('setting/rapor');
        $data = ['user' => $user, 'judul' => 'Pengaturan Rapor', 'subjudul' => 'Pengaturan Rapor', 'setting' => $this->dashboard->getSetting()];
        $user = $this->ion_auth->user()->row();
        $this->load->view('_templates/dashboard/_footer');
        $smt = $this->dashboard->getSemesterActive();
        $data['kkm_drop'] = ['Tidak', 'Ya'];
    }
    public function saveRaporAdmin()
    {
        $input = ['id_setting' => $tp->id_tp . $smt->id_smt, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'tgl_rapor_pts' => $this->input->post('tgl_rapor_pts', true), 'nip_kepsek' => $this->input->post('nip_kepsek', true), 'nip_walikelas' => $this->input->post('nip_walikelas', true), 'tgl_rapor_akhir' => $this->input->post('tgl_rapor_akhir', true), 'tgl_rapor_kelas_akhir' => $this->input->post('tgl_rapor_kelas_akhir', true), 'kkm_tunggal' => $this->input->post('kkm_tunggal', true), 'kkm' => $this->input->post('kkm', true), 'bobot_ph' => $this->input->post('bobot_ph', true), 'bobot_pts' => $this->input->post('bobot_pts', true), 'bobot_pas' => $this->input->post('bobot_pas', true)];
        $update = $this->db->replace('rapor_admin_setting', $input);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['status'] = $update;
        $tp = $this->dashboard->getTahunActive();
        $this->output_json($data);
        $smt = $this->dashboard->getSemesterActive();
    }
    public function raporkkm()
    {
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/templates/footer');
        $data = ['user' => $user, 'judul' => 'KKM dan Bobot', 'subjudul' => 'Input KKM dan Bobot Nilai', 'setting' => $this->dashboard->getSetting()];
        $arrKelas = [];
        $data['tp_active'] = $tp;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $ekstra = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $user = $this->ion_auth->user()->row();
        $data['guru'] = $guru;
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
                if (!($key_kelas !== false)) {
                }
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
            }
        }
        $data['kelas_ekstra'] = $arrKelasEkstra;
        $mapel = $mapel_guru->mapel_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->mapel_kelas))) : [];
        $kelases = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/rapor/kkm/data');
        foreach ($ekstra as $m) {
            foreach ($m->kelas_ekstra as $kls) {
                $arrKelasEkstra[$m->id_ekstra][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                if (!($key_kelas !== false)) {
                }
            }
            $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
        }
        $data['ekstra'] = $arrEkstra;
        if (!(count($ekstra) > 0)) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $data['kelas'] = $arrKelas;
        $tp = $this->dashboard->getTahunActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $arrEkstra = [];
        $smt = $this->dashboard->getSemesterActive();
        $data['smt'] = $this->dashboard->getSemester();
        $arrKelasEkstra = [];
        $data['mapel'] = $arrMapel;
        $data['smt_active'] = $smt;
        $arrMapel = [];
    }
    public function datakkm($mapel, $kelas)
    {
        $smt = $this->dashboard->getSemesterActive();
        $data['kelas'] = $kelas;
        $kkm = '';
        $tp = $this->dashboard->getTahunActive();
        $kkm = $this->rapor->getKkm($mapel . $kelas . $tp->id_tp . $smt->id_smt . '1');
        $data['tp'] = $tp->id_tp;
        $this->load->model('Dashboard_model', 'dashboard');
        $data['smt'] = $smt->id_smt;
        $this->output_json($data);
        $data['setting'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['kkm'] = $kkm;
        if (!($kelas != null)) {
        }
        $data['mapel'] = $mapel;
    }
    public function datakkmEkstra($ekstra, $kelas)
    {
        $data['kkm'] = $kkm;
        $data['tp'] = $tp->id_tp;
        if (!($kelas != null)) {
        }
        $kkm = $this->rapor->getKkm($ekstra . $kelas . $tp->id_tp . $smt->id_smt . '2');
        $data['setting'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm = '';
        $data['kelas'] = $kelas;
        $tp = $this->dashboard->getTahunActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $data['ekstra'] = $ekstra;
        $smt = $this->dashboard->getSemesterActive();
        $data['smt'] = $smt->id_smt;
        $this->output_json($data);
    }
    public function saveKkm()
    {
        $update = $this->db->replace('rapor_kkm', $input);
        $smt = $this->dashboard->getSemesterActive();
        $data['status'] = $update;
        $input = ['id_kkm' => $this->input->post('id_kkm', true), 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'bobot_ph' => $this->input->post('bobot_ph', true), 'bobot_pts' => $this->input->post('bobot_pts', true), 'bobot_pas' => $this->input->post('bobot_pas', true), 'kkm' => $this->input->post('kkm', true), 'beban_jam' => $this->input->post('beban', true), 'jenis' => $this->input->post('jenis_kkm', true), 'id_kelas' => $this->input->post('id_kelas', true), 'id_mapel' => $this->input->post('id_mapel', true)];
        $tp = $this->dashboard->getTahunActive();
        $this->output_json($data);
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function raporkikd()
    {
        $data = ['user' => $user, 'judul' => 'Indikator KD', 'subjudul' => 'Ringkasan Materi Penilaian', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/rapor/kikd/data');
        $arrKelas = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                if (!($key_kelas !== false)) {
                }
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
            }
        }
        $data['kelas'] = $arrKelas;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data['guru'] = $guru;
        $data['smt'] = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $data['mapel'] = $arrMapel;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['smt_active'] = $smt;
        $arrMapel = [];
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        if (!($mapel != null)) {
        }
        $this->load->view('members/guru/templates/footer');
        $kelases = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function datakikd($mapel, $kelas)
    {
        $data['kikd'] = $arrKiKd;
        $data['mapel'] = $mapel;
        $aspek = ['1', '2'];
        $kikds = $this->rapor->getKikdMapelKelas($mapel, $kelas, $tp->id_tp, $smt->id_smt);
        foreach ($aspek as $asp) {
            $i = 0;
            if (!($i < 8)) {
            }
            $key_ki = array_search($mapel . $kelas . $asp . $no, array_column($kikds, 'id_kikd'));
            if ($key_ki !== false) {
            }
            $i++;
            $no = $i + 1;
            $arrKiKd[$asp][$mapel . $kelas . $asp . $no] = $kikds[$key_ki];
            $arrKiKd[$asp][$mapel . $kelas . $asp . $no] = ['materi_kikd' => ''];
        }
        $this->output_json($data);
        $arrKiKd[] = [];
        if (!($kelas != null)) {
        }
        $tp = $this->dashboard->getTahunActive();
        $data['kelas'] = $kelas;
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function saveKikd()
    {
        $data['status'] = $updated;
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($data);
        $data['json'] = $sjson;
        $updated = false;
        $sjson = $this->input->post('materi', true);
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        foreach ((array) $sjson as $aspek => $mapel_kelas) {
            foreach ($mapel_kelas as $idmk => $kikd) {
                foreach ($kikd as $id => $materi) {
                    $updated = $this->db->replace('rapor_kikd', $input);
                    $input = ['id_kikd' => $id, 'id_mapel_kelas' => $idmk, 'aspek' => $aspek, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $materi];
                }
            }
        }
    }
    public function raporNilai()
    {
        $user = $this->ion_auth->user()->row();
        $ekstra = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $data['siswas'] = $siswas;
        $this->load->model('Dashboard_model', 'dashboard');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('members/guru/templates/header', $data);
        $data['ekstra'] = $arrEkstra;
        $data['siswae'] = $siswae;
        $pts = [];
        if (!(count($ekstra) > 0)) {
        }
        $harian = [];
        $data['tp_active'] = $tp;
        $siswas = [];
        $levelsMapel = [];
        $pas = [];
        $data['level'] = array_unique($levelsMapel);
        $this->load->view('members/guru/rapor/nilai/data');
        foreach ($ekstra as $m) {
            foreach ($m->kelas_ekstra as $kls) {
                $siswae[$m->id_ekstra][$kelas_guru->nama_kelas] = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                $ektras[$m->id_ekstra][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiEkstraKelas($m->id_ekstra, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                if (!($kelas_guru != null)) {
                }
                $kelas_guru = $this->kelas->get_one($kls->kelas);
                $arrKelasEkstra[$m->id_ekstra][] = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
            }
            $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
        }
        $arrKelasEkstra = [];
        $arrEkstra = [];
        $this->load->view('members/guru/templates/footer');
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $siswae = [];
        $ektras = [];
        $data = ['user' => $user, 'judul' => 'Input Nilai', 'subjudul' => 'Input Nilai Rapor', 'setting' => $this->dashboard->getSetting()];
        $data['pts'] = $pts;
        $data['guru'] = $guru;
        $data['harian'] = $harian;
        $data['kelas_ekstra'] = $arrKelasEkstra;
        foreach ($mapel as $m) {
            foreach ($m->kelas_mapel as $kls) {
                $pas[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiAkhirKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                $siswas[$m->id_mapel][$kelas_guru->nama_kelas] = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                $arrKelasMapel[$m->id_mapel][] = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                if (!($kelas_guru != null)) {
                }
                $kelas_guru = $this->kelas->get_one($kls->kelas);
                $harian[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiHarianKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                $pts[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiPtsKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                $levelsMapel[] = $kelas_guru->level_id;
            }
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $mapel = $mapel_guru->mapel_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->mapel_kelas))) : [];
        $arrKelasMapel = [];
        $smt = $this->dashboard->getSemesterActive();
        $data['ekstras'] = $ektras;
        $data['pas'] = $pas;
        $tp = $this->dashboard->getTahunActive();
        $data['smt_active'] = $smt;
        $data['kelas_mapel'] = $arrKelasMapel;
        $data['tp'] = $this->dashboard->getTahun();
        $arrMapel = [];
        $data['mapel'] = $arrMapel;
    }
    public function raporNilaiGuru($filter = null, $id_mapel = null)
    {
        $data['tp'] = $this->dashboard->getTahun();
        $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $i++;
        $guru_mapel = '';
        $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $data['filter_selected'] = $filter;
        $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
        $dropEskul = $this->dropdown->getAllEkskul();
        if (!($id_mapel != null)) {
        }
        $i = 0;
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['nilai'] = $nilai;
        $ret[''] = 'Pilih Mapel';
        $user = $this->ion_auth->user()->row();
        $ret[''] = 'Pilih Eskul';
        $data['mapel'] = $ret + $dropMapel;
        $i++;
        $smt = $this->dashboard->getSemesterActive();
        $data['mapel_selected'] = $id_mapel;
        $jabatan_guru = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        $aspek = ['1', '2'];
        if (!($guru->wali_kelas != null)) {
        }
        $kkm_ekstra = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '2');
        foreach ($jabatan_guru as $jab) {
            foreach ($jab->ekstra_kelas as $mk) {
                foreach ($mk['kelas_ekstra'] as $km) {
                    if (!($km['kelas'] == $guru->wali_kelas)) {
                    }
                    $guru_mapel = $jab->nama_guru;
                }
                if (!($mk['id_ekstra'] == $id_mapel)) {
                }
            }
        }
        $data['smt'] = $this->dashboard->getSemester();
        if ($setting->kkm_tunggal == '1') {
        }
        $siswa = $siswas[$i];
        $data = ['user' => $user, 'judul' => 'Semua Nilai', 'subjudul' => 'Semua Nilai Rapor', 'setting' => $this->dashboard->getSetting()];
        $data['siswa'] = $siswas;
        $data['kkm_ekstra'] = $kkm_ekstra;
        $kkm = $setting;
        $data['filter'] = ['' => 'Filter berdasarkan', '1' => 'Mata Pelajaran', '2' => 'Ekstrakurikuler'];
        foreach ($jabatan_guru as $jabatan) {
            $jabatan->ekstra_kelas = $jabatan->ekstra_kelas == null ? [] : unserialize($jabatan->ekstra_kelas);
            $jabatan->mapel_kelas = $jabatan->mapel_kelas == null ? [] : unserialize($jabatan->mapel_kelas);
        }
        $this->load->view('members/guru/templates/header', $data);
        if (!($i < count($siswas))) {
        }
        $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
        $this->load->view('members/guru/templates/footer');
        $this->load->view('members/guru/rapor/nilai/nilaiguru');
        $data['guru_mapel'] = $guru_mapel;
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $guru_mapel = '';
        $data['ekstra'] = $ret + $dropEskul;
        $siswa = $siswas[$i];
        $kkm_ekstra = $setting;
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($aspek as $asp) {
            $no = $i + 1;
            $i = 0;
            if (!($i < 8)) {
            }
            $i++;
            $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
        }
        $arrKiKd[] = [];
        $data['guru'] = $guru;
        $nilai = [];
        if (!($i < count($siswas))) {
        }
        $data['kkm'] = $kkm;
        foreach ($jabatan_guru as $jab) {
            foreach ($jab->mapel_kelas as $mk) {
                if (!($mk['id_mapel'] == $id_mapel)) {
                }
                foreach ($mk['kelas_mapel'] as $km) {
                    $guru_mapel = $jab->nama_guru;
                    if (!($km['kelas'] == $guru->wali_kelas)) {
                    }
                }
            }
        }
        $kkm = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '1');
        $i = 0;
        $data['ekstra_selected'] = $id_mapel;
        $dropMapel = $this->dropdown->getAllMapel();
        if ($filter == '1') {
        }
        $ns = $this->rapor->getNilaiHarianKelas($id_mapel, $guru->wali_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $tp = $this->dashboard->getTahunActive();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? json_decode(json_encode($dummyNilai)) : $ns;
    }
    public function raporCekNilai($filter = null, $id_mapel = null)
    {
        $guru_mapel = '';
        $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $arrKiKd[] = [];
        $nilai = [];
        $kkm = $setting;
        $tp = $this->dashboard->getTahunActive();
        $data['ekstra_selected'] = $id_mapel;
        $data['filter_selected'] = $filter;
        $data['siswa'] = $siswas;
        $data['tp'] = $this->dashboard->getTahun();
        $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
        $jabatan_guru = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $dropMapel = $this->dropdown->getAllMapel();
        $aspek = ['1', '2'];
        $data['nilai'] = $nilai;
        foreach ($aspek as $asp) {
            $no = $i + 1;
            $i = 0;
            if (!($i < 8)) {
            }
            $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
            $i++;
        }
        if ($filter == '1') {
        }
        if (!($i < count($siswas))) {
        }
        $data['ekstra'] = $ret + $dropEskul;
        $this->load->model('Dashboard_model', 'dashboard');
        foreach ($jabatan_guru as $jabatan) {
            $jabatan->ekstra_kelas = $jabatan->ekstra_kelas == null ? [] : unserialize($jabatan->ekstra_kelas);
            $jabatan->mapel_kelas = $jabatan->mapel_kelas == null ? [] : unserialize($jabatan->mapel_kelas);
        }
        $data['mapel_selected'] = $id_mapel;
        $data = ['user' => $user, 'judul' => 'Semua Nilai', 'subjudul' => 'Semua Nilai Rapor', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru];
        if ($setting->kkm_tunggal == '1') {
        }
        $ret[''] = 'Pilih Mapel';
        $ret[''] = 'Pilih Eskul';
        $data['kkm'] = $kkm;
        $data['mapel'] = $ret + $dropMapel;
        $i++;
        $jenis = $filter == '1' ? '1' : '2';
        $data['smt_active'] = $smt;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? json_decode(json_encode($dummyNilai)) : $ns;
        $ns = $this->rapor->getNilaiHarianKelas($id_mapel, $guru->wali_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $siswa = $siswas[$i];
        $data['smt'] = $this->dashboard->getSemester();
        foreach ($jabatan_guru as $jab) {
            foreach ($jab->mapel_kelas as $mk) {
                if (!($mk['id_mapel'] == $id_mapel)) {
                }
                foreach ($mk['kelas_mapel'] as $km) {
                    if (!($km['kelas'] == $guru->wali_kelas)) {
                    }
                    $guru_mapel = $jab->nama_guru;
                }
            }
        }
        $this->load->view('members/guru/templates/header', $data);
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $data['guru_mapel'] = $guru_mapel;
        if (!($id_mapel != null)) {
        }
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['filter'] = ['' => 'Filter berdasarkan', '1' => 'Mata Pelajaran', '2' => 'Ekstrakurikuler'];
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        foreach ($jabatan_guru as $jab) {
            foreach ($jab->ekstra_kelas as $mk) {
                foreach ($mk['kelas_ekstra'] as $km) {
                    $guru_mapel = $jab->nama_guru;
                    if (!($km['kelas'] == $guru->wali_kelas)) {
                    }
                }
                if (!($mk['id_ekstra'] == $id_mapel)) {
                }
            }
        }
        $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
        $user = $this->ion_auth->user()->row();
        $guru_mapel = '';
        if (!($guru->wali_kelas != null)) {
        }
        $kkm = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . $jenis);
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('members/guru/templates/footer');
        $this->load->view('members/guru/rapor/nilai/periksa');
        $siswa = $siswas[$i];
        $i++;
        $data['tp_active'] = $tp;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $i = 0;
        $dropEskul = $this->dropdown->getAllEkskul();
    }
    public function inputHarian($id_mapel, $id_kelas)
    {
        $data['tp_active'] = $tp;
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        if (!($setting != null)) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $siswa = $siswas[$i];
        $user = $this->ion_auth->user()->row();
        $nilai = [];
        $i++;
        $data['kikd'] = $arrKiKd;
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        if ($setting->kkm_tunggal == '1') {
        }
        $i = 0;
        $data['tp'] = $this->dashboard->getTahun();
        $mapel = '';
        $ns = $this->rapor->getNilaiHarianKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $kelas = [];
        $this->load->view('members/guru/templates/header', $data);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        foreach ($mapels as $m) {
            $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            if (!($m->id_mapel === $id_mapel)) {
            }
            foreach ($m->kelas_mapel as $kls) {
                if (!($kls->kelas === $id_kelas)) {
                }
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $tp = $this->dashboard->getTahunActive();
        $data['setting_rapor'] = $setting;
        $aspek = ['1', '2'];
        $smt = $this->dashboard->getSemesterActive();
        $kkm = null;
        $data['smt_active'] = $smt;
        foreach ($aspek as $asp) {
            $r = $this->rapor->getKikdMapel($id_mapel . $id_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
            $arrKiKd[$asp][$id_mapel . $id_kelas . $asp . $no] = $r;
            $i++;
            $r = $this->rapor->getKikdMapel($id_mapel . $id_kelas . $asp . $no, $tp->id_tp - 1, $smt->id_smt);
            $no = $i + 1;
            $i = 0;
            if (!($i < 8)) {
            }
            if (!($r == null)) {
            }
        }
        $arrKiKd[] = [];
        $kkm = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/rapor/nilai/harian');
        if (!($id_kelas != null)) {
        }
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Nilai Harian Kelas ', 'subjudul' => 'Input Nilai Harian Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'mapel' => $mapel, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm];
        if (!($i < count($siswas))) {
        }
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $kkm = $setting;
        $this->load->view('members/guru/templates/footer');
    }
    public function downloadNilaiHarian($id_mapel, $id_kelas)
    {
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        foreach ($siswas as $ind => $siswa) {
            $siswa->p7 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p7 ?? '' : '';
            $siswa->p5 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p5 ?? '' : '';
            $siswa->k2 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k2 ?? '' : '';
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->p3 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p3 ?? '' : '';
            $siswa->k5 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k5 ?? '' : '';
            $siswa->no = $ind + 1;
            $siswa->k3 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k3 ?? '' : '';
            $siswa->p1 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p1 ?? '' : '';
            $siswa->k7 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k7 ?? '' : '';
            $siswa->p2 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p2 ?? '' : '';
            $siswa->k1 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k1 ?? '' : '';
            $siswa->p4 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p4 ?? '' : '';
            $siswa->k4 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k4 ?? '' : '';
            $siswa->p6 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p6 ?? '' : '';
            $siswa->k6 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k6 ?? '' : '';
            $siswa->k8 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k8 ?? '' : '';
            $siswa->p8 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p8 ?? '' : '';
        }
        $this->output_json(['siswa' => $siswas, 'kikd' => $kikds]);
        $kikds = $this->rapor->getKikdMapelKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        $kikds[] = ['nok' => 1, 'kodek' => 'K1', 'k' => 'Praktik/Portofolio/Proyek yang dinilai (lihat tabel KATA KERJA sebelah kanan)', 'nop' => 1, 'kodep' => 'P1', 'p' => 'Materi yang dinilai (lihat tabel KATA KERJA sebelah kanan)'];
        $smt = $this->dashboard->getSemesterActive();
        foreach ($kikds as $ki) {
            if ($ki->aspek == 1) {
            }
            $ki->p = $ki->materi_kikd;
            $nn = substr($ki->id_kikd, -1);
            $ki->kodek = 'K' . $nn;
            $ki->nop = $nn;
            $ki->nok = $nn;
            $nn = substr($ki->id_kikd, -1);
            $ki->k = $ki->materi_kikd;
            $ki->kodep = 'P' . $nn;
        }
        $this->load->model('Dashboard_model', 'dashboard');
        if (!(count($kikds) == 0)) {
        }
        $nilais = $this->rapor->getAllNilaiHarianKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
    }
    public function uploadNilaiHarian()
    {
        $kikdp = [];
        $tp = $this->dashboard->getTahunActive();
        $kikdk = [];
        $this->db->trans_complete();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($p_siswa as $siswa) {
            unset($siswa['nisn']);
            $datas[] = $siswa;
            $siswa['id_tp'] = $tp->id_tp;
            unset($siswa['namasiswa']);
            $siswa['id_siswa'] = $siswa['id'];
            $siswa['id_mapel'] = $id_mapel;
            $siswa['id_kelas'] = $id_kelas;
            unset($siswa['id']);
            $siswa['id_nilai_harian'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_smt'] = $smt->id_smt;
        }
        $id_kelas = $this->input->post('id_kelas');
        $p_kikd = $this->input->post('kikd');
        foreach ($p_kikd as $kikd) {
            $kikdp[] = ['id_kikd' => $id_mapel . $id_kelas . '1' . $kikd['no'], 'id_mapel_kelas' => $id_mapel . $id_kelas, 'aspek' => 1, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $kikd['materipengetahuanyangdinilai'] != null ? strip_tags($kikd['materipengetahuanyangdinilai'] ?? '') : ''];
            $kikdk[] = ['id_kikd' => $id_mapel . $id_kelas . '2' . $kikd['no'], 'id_mapel_kelas' => $id_mapel . $id_kelas, 'aspek' => 2, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $kikd['materiketerampilanyangdinilai'] != null ? strip_tags($kikd['materiketerampilanyangdinilai'] ?? '') : ''];
        }
        $this->db->trans_start();
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa = $this->input->post('siswa');
        $this->output_json($updated);
        $updated = 0;
        foreach ($kikdp as $kip) {
            if (!($kip != null)) {
            }
            $this->db->replace('rapor_kikd', $kip);
        }
        $datas = [];
        foreach ($kikdk as $kik) {
            $this->db->replace('rapor_kikd', $kik);
            if (!($kik != null)) {
            }
        }
        $id_mapel = $this->input->post('id_mapel');
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_harian', $data);
            $updated++;
            if (!$update) {
            }
        }
    }
    public function importHarian()
    {
        $this->db->trans_start();
        $posts = $this->input->post('siswa', true);
        $this->db->trans_complete();
        $data['updated'] = $updated;
        foreach ((array) $posts as $data) {
            if (!$update) {
            }
            $updated++;
            $update = $this->db->replace('rapor_nilai_harian', $data);
        }
        $this->output_json($data);
        $updated = 0;
    }
    public function inputPts($id_mapel, $id_kelas)
    {
        $i++;
        $this->load->view('members/guru/rapor/nilai/pts');
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $data = ['user' => $user, 'judul' => 'Nilai PTS Kelas ', 'subjudul' => 'Input Nilai PTS Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'mapel' => $mapel, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm];
        $this->load->model('Dashboard_model', 'dashboard');
        $data['setting_rapor'] = $setting;
        if (!($setting != null)) {
        }
        $this->load->view('members/guru/templates/footer');
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $ns = $this->rapor->getNilaiPtsKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $kkm = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
        $data['tp_active'] = $tp;
        $i = 0;
        if ($setting->kkm_tunggal == '1') {
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $tp = $this->dashboard->getTahunActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $kkm = null;
        foreach ($mapels as $m) {
            if (!($m->id_mapel === $id_mapel)) {
            }
            foreach ($m->kelas_mapel as $kls) {
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                if (!($kls->kelas === $id_kelas)) {
                }
            }
            $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
        }
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $mapel = '';
        $data['smt_active'] = $smt;
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $data['tp'] = $this->dashboard->getTahun();
        $kelas = [];
        $siswa = $siswas[$i];
        if (!($i < count($siswas))) {
        }
        $kkm = $setting;
        $this->load->view('members/guru/templates/header', $data);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function downloadTemplatePts($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->output_json(['siswa' => $siswas]);
        foreach ($siswas as $ind => $siswa) {
            $siswa->nilai = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->nilai : '';
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->predikat = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->predikat : '';
            $siswa->no = $ind + 1;
        }
        $nilais = $this->rapor->getAllNilaiPtsKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
    }
    public function uploadNilaiPts()
    {
        $p_siswa = $this->input->post('siswa');
        $this->output_json($updated);
        $updated = 0;
        $this->load->model('Dashboard_model', 'dashboard');
        $datas = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_siswa'] = $siswa['id'];
            unset($siswa['id']);
            unset($siswa['namasiswa']);
            $siswa['id_kelas'] = $id_kelas;
            $datas[] = $siswa;
            $siswa['id_mapel'] = $id_mapel;
            $siswa['id_smt'] = $smt->id_smt;
            $siswa['id_nilai_pts'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            unset($siswa['nisn']);
            $siswa['id_tp'] = $tp->id_tp;
        }
        $id_kelas = $this->input->post('id_kelas');
        foreach ($datas as $data) {
            if (!$update) {
            }
            $updated++;
            $update = $this->db->replace('rapor_nilai_pts', $data);
        }
        $id_mapel = $this->input->post('id_mapel');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
    }
    public function importPts()
    {
        $updated = 0;
        echo json_encode($updated);
        $this->db->trans_start();
        $this->db->trans_complete();
        $inputs = $this->input->post('siswa', true);
        foreach ($inputs as $data) {
            $updated++;
            if (!$update) {
            }
            $update = $this->db->replace('rapor_nilai_pts', $data);
        }
    }
    public function inputPas($id_mapel, $id_kelas)
    {
        $this->load->view('members/guru/rapor/nilai/pas');
        $data = ['user' => $user, 'judul' => 'Nilai Akhir Kelas ', 'subjudul' => 'Input Nilai Akhir Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'mapel' => $mapel, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm, 'setting_rapor' => $setting];
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $i = 0;
        $mapel = '';
        $ns = $this->rapor->getNilaiAkhirKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        if (!($i < count($siswas))) {
        }
        if (!($setting != null)) {
        }
        $smt = $this->dashboard->getSemesterActive();
        $kkm = $setting;
        $i++;
        if ($setting->kkm_tunggal == '1') {
        }
        $data['smt_active'] = $smt;
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $kkm = null;
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $kelas = [];
        $data['tp_active'] = $tp;
        $kkm = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
        $nilai = [];
        $this->load->view('members/guru/templates/footer');
        foreach ($mapels as $m) {
            if (!($m->id_mapel === $id_mapel)) {
            }
            foreach ($m->kelas_mapel as $kls) {
                if (!($kls->kelas === $id_kelas)) {
                }
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
            $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
        }
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $siswa = $siswas[$i];
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $this->load->view('members/guru/templates/header', $data);
        $dummyNilai = ['nhar' => '', 'npts' => '', 'npas' => ''];
    }
    public function downloadTemplatePas($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->dashboard->getSemesterActive();
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->npas : '';
        }
        $tp = $this->dashboard->getTahunActive();
        $nilais = $this->rapor->getAllNilaiAkhirKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->output_json(['siswa' => $siswas]);
    }
    public function uploadNilaiPas()
    {
        $id_mapel = $this->input->post('id_mapel');
        $smt = $this->dashboard->getSemesterActive();
        $id_kelas = $this->input->post('id_kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_akhir', $data);
            if (!$update) {
            }
            $updated++;
        }
        $this->output_json($updated);
        $p_siswa = $this->input->post('siswa');
        foreach ($p_siswa as $siswa) {
            unset($siswa['namasiswa']);
            $siswa['id_kelas'] = $id_kelas;
            $siswa['id_tp'] = $tp->id_tp;
            unset($siswa['id']);
            unset($siswa['nisn']);
            $siswa['id_smt'] = $smt->id_smt;
            $datas[] = $siswa;
            $siswa['id_siswa'] = $siswa['id'];
            $siswa['id_nilai_akhir'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_mapel'] = $id_mapel;
        }
        $tp = $this->dashboard->getTahunActive();
    }
    public function importPas()
    {
        $this->db->trans_complete();
        echo json_encode($updated);
        foreach ($inputs as $data) {
            if (!$update) {
            }
            $updated++;
            $update = $this->db->replace('rapor_nilai_akhir', $data);
        }
        $inputs = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
    }
    public function inputEkstra($id_ekstra, $id_kelas)
    {
        $i = 0;
        $kelas = [];
        $ekstra = '';
        $data['smt'] = $this->dashboard->getSemester();
        $smt = $this->dashboard->getSemesterActive();
        $data['smt_active'] = $smt;
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $ekstras = json_decode(json_encode(unserialize($ekstra_guru->ekstra_kelas)));
        foreach ($ekstras as $m) {
            if (!($m->id_ekstra === $id_ekstra)) {
            }
            foreach ($m->kelas_ekstra as $kls) {
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                if (!($kls->kelas === $id_kelas)) {
                }
            }
            $ekstra = ['id_ekstra' => $m->id_ekstra, 'nama_ekstra' => $m->nama_ekstra];
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $i++;
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'judul' => 'Nilai Ekstrakurikuler ', 'subjudul' => 'Input Nilai PTS Ekstra ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'ekstra' => $ekstra, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm];
        $this->load->view('members/guru/templates/footer');
        $kkm = $setting;
        $siswa = $siswas[$i];
        if (!($i < count($siswas))) {
        }
        $kkm = $this->rapor->getKkm($id_ekstra . $id_kelas . $tp->id_tp . $smt->id_smt . '2');
        $this->load->view('members/guru/rapor/nilai/ekstra');
        $data['tp_active'] = $tp;
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $ekstra_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        if ($setting->kkm_tunggal == '1') {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $ns = $this->rapor->getNilaiEkstraKelas($id_ekstra, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
    }
    public function downloadTemplateEkstra($id_ekstra, $id_kelas)
    {
        $smt = $this->dashboard->getSemesterActive();
        $nilais = $this->rapor->getAllNilaiEkstraKelas($id_ekstra, $id_kelas, $tp->id_tp, $smt->id_smt);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        foreach ($siswas as $ind => $siswa) {
            $siswa->nilai = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->nilai : '';
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->no = $ind + 1;
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->output_json(['siswa' => $siswas]);
    }
    public function uploadNilaiEkstra()
    {
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_ekstra'] = $id_ekstra . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_ekstra'] = $id_ekstra;
            unset($siswa['nisn']);
            $siswa['id_siswa'] = $siswa['id'];
            $datas[] = $siswa;
            $siswa['id_tp'] = $tp->id_tp;
            $siswa['id_kelas'] = $id_kelas;
            unset($siswa['id']);
            unset($siswa['namasiswa']);
            $siswa['id_smt'] = $smt->id_smt;
        }
        $id_ekstra = $this->input->post('id_ekstra');
        $smt = $this->dashboard->getSemesterActive();
        $p_siswa = $this->input->post('siswa');
        $updated = 0;
        foreach ($datas as $data) {
            if (!$update) {
            }
            $update = $this->db->replace('rapor_nilai_ekstra', $data);
            $updated++;
        }
        $tp = $this->dashboard->getTahunActive();
        $this->load->model('Dashboard_model', 'dashboard');
        echo json_encode($updated);
        $id_kelas = $this->input->post('id_kelas');
    }
    public function importEkstra()
    {
        $this->db->trans_start();
        $this->db->trans_complete();
        $inputs = $this->input->post('siswa', true);
        $updated = 0;
        echo json_encode($updated);
        foreach ($inputs as $data) {
            $updated++;
            $update = $this->db->replace('rapor_nilai_ekstra', $data);
            if (!$update) {
            }
        }
    }
    public function raporSikap()
    {
        $id_kelas = $guru->wali_kelas;
        $this->load->view('members/guru/rapor/sikap/data');
        $smt = $this->dashboard->getSemesterActive();
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        if (!($i < 10)) {
        }
        $i = 0;
        $this->load->model('Dashboard_model', 'dashboard');
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $s = ['id_sikap' => 2 . $no, 'jenis' => '2', 'kode' => $no, 'sikap' => ''];
        $sikap = json_decode(json_encode($dummySikap));
        $s = ['id_sikap' => 1 . $no, 'jenis' => '1', 'kode' => $no, 'sikap' => ''];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $data['tp_active'] = $tp;
        $data['tp'] = $this->dashboard->getTahun();
        if (!(count($sikap) === 0)) {
        }
        $arrMapel = [];
        $data['guru'] = $guru;
        $data['smt_active'] = $smt;
        $i++;
        $no = $i + 1;
        $dummySikap = [];
        if (!($i < 10)) {
        }
        $data['mapel'] = $arrMapel;
        $tp = $this->dashboard->getTahunActive();
        $arrKelas = [];
        $data = ['user' => $user, 'judul' => 'Input Nilai Sikap', 'subjudul' => 'Input Nilai Sikap', 'setting' => $this->dashboard->getSetting()];
        $sikap = $this->rapor->getDeskripsiSikap($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $data['sikap'] = $sikap;
        $no = $i + 1;
        $user = $this->ion_auth->user()->row();
        $i = 0;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        array_push($dummySikap, $s);
        array_push($dummySikap, $s);
        $data['kelas'] = $arrKelas;
        $i++;
        $this->load->view('members/guru/templates/header', $data);
    }
    public function saveSikap()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $data['status'] = $update;
        foreach ($input as $d) {
            $update = $this->db->replace('rapor_data_sikap', $data);
            $data = ['id_sikap' => $d->id_sikap, 'id_kelas' => $d->kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'sikap' => $d->sikap, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        }
        $this->output_json($data);
        $input = json_decode($this->input->post('sikap', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
    }
    public function raporSpiritual()
    {
        $this->load->view('members/guru/rapor/sikap/spiritual');
        $dummySpiritual = [];
        if (!($i < 10)) {
        }
        $nilai = [];
        $tp = $this->dashboard->getTahunActive();
        $spiritual = $this->rapor->getDeskripsiSikapByJenis($id_kelas, '1', $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Nilai Spiritual Kelas ', 'subjudul' => 'Input Nilai', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'spiritual' => $spiritual];
        array_push($dummySpiritual, $s);
        $data['smt'] = $this->dashboard->getSemester();
        $siswa = $siswas[$i];
        $data['tp_active'] = $tp;
        $i++;
        $this->load->model('Dashboard_model', 'dashboard');
        if (!($i < count($siswas))) {
        }
        $dummyNilai = ['predikat' => '', 'sl1' => '', 'sl2' => '', 'sl3' => '', 'mb1' => '', 'mb2' => '', 'mb3' => ''];
        $ns = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
        $i = 0;
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        if (!(count($spiritual) === 0)) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $no = $i + 1;
        $smt = $this->dashboard->getSemesterActive();
        $kelas = $this->kelas->get_one($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : unserialize($ns->nilai);
        $spiritual = json_decode(json_encode($dummySpiritual));
        $this->load->view('members/guru/templates/footer');
        $s = ['id_sikap' => $id_kelas . 1 . $no, 'jenis' => '1', 'kode' => $no, 'sikap' => $this->rapor->getDummyDeskripsiSpiritual()[$i]];
        $i = 0;
        $this->load->view('members/guru/templates/header', $data);
        $user = $this->ion_auth->user()->row();
        $i++;
        $data['smt_active'] = $smt;
        $id_kelas = $guru->wali_kelas;
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function importSpiritual($id_kelas)
    {
        $input = json_decode($this->input->post('nilai', true));
        foreach ($input as $in) {
            $id_siswa = $in[11];
            $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '1', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 1, 'nilai' => serialize(['predikat' => $in[3], 'sl1' => $in[4], 'sl2' => $in[5], 'sl3' => $in[6], 'mb1' => $in[7], 'mb2' => $in[8], 'mb3' => $in[9]]), 'deskripsi' => $in[10], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            if (!($id_siswa != 'id')) {
            }
        }
        echo json_encode($updated);
        $this->load->model('Dashboard_model', 'dashboard');
        $updated = 0;
        $smt = $this->dashboard->getSemesterActive();
        foreach ($datas as $data) {
            if (!$update) {
            }
            $updated++;
            $update = $this->db->replace('rapor_nilai_sikap', $data);
        }
        $datas = [];
        $tp = $this->dashboard->getTahunActive();
    }
    public function raporSosial()
    {
        $nilai = [];
        $ns = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
        $data = ['user' => $user, 'judul' => 'Nilai Sosial Kelas ', 'subjudul' => 'Input Nilai PTS Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'sosial' => $sosial];
        $this->load->model('Dashboard_model', 'dashboard');
        if (!(count($sosial) === 0)) {
        }
        $dummyNilai = ['predikat' => '', 'sl1' => '', 'sl2' => '', 'sl3' => '', 'mb1' => '', 'mb2' => '', 'mb3' => ''];
        $i++;
        $this->load->view('members/guru/rapor/sikap/sosial');
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $i = 0;
        $tp = $this->dashboard->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        if (!($i < count($siswas))) {
        }
        $this->load->view('members/guru/templates/header', $data);
        $siswa = $siswas[$i];
        array_push($dummySosial, $s);
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $id_kelas = $guru->wali_kelas;
        $data['smt'] = $this->dashboard->getSemester();
        $i++;
        $dummySosial = [];
        $user = $this->ion_auth->user()->row();
        $sosial = json_decode(json_encode($dummySosial));
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $i = 0;
        $sosial = $this->rapor->getDeskripsiSikapByJenis($id_kelas, '2', $tp->id_tp, $smt->id_smt);
        $no = $i + 1;
        $s = ['id_sikap' => $id_kelas . 2 . $no, 'jenis' => '2', 'kode' => $no, 'sikap' => $this->rapor->getDummyDeskripsiSosial()[$i]];
        $kelas = $this->kelas->get_one($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : unserialize($ns->nilai);
        if (!($i < 10)) {
        }
    }
    public function importSosial($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $datas = [];
        foreach ($datas as $data) {
            if (!$update) {
            }
            $update = $this->db->replace('rapor_nilai_sikap', $data);
            $updated++;
        }
        echo json_encode($updated);
        $updated = 0;
        $tp = $this->dashboard->getTahunActive();
        foreach ($input as $in) {
            $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '2', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 2, 'nilai' => serialize(['predikat' => $in[3], 'a1' => $in[4], 'a2' => $in[5], 'a3' => $in[6], 'b1' => $in[7], 'b2' => $in[8], 'b3' => $in[9], 'c1' => $in[10], 'c2' => $in[11]]), 'deskripsi' => $in[12], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            if (!($id_siswa != 'id')) {
            }
            $id_siswa = $in[13];
        }
        $smt = $this->dashboard->getSemesterActive();
        $input = json_decode($this->input->post('nilai', true));
    }
    public function raporPrestasi()
    {
        $nilaiRata_k = [];
        $s = ['id_catatan' => $id_kelas . 1 . $no, 'jenis' => '3', 'kode' => $dummyKode[$i], 'deskripsi' => $this->rapor->getDummyDeskripsiRanking()[$i], 'rank' => $dummyRank[$i]];
        $this->load->view('members/guru/templates/footer');
        if (!($i < count($siswas))) {
        }
        $dummyRank = ['1 ~ 3', '4 ~ 10', '11 ~ 15', '16 ~ 20', '21 ~ 25', '26 > >'];
        $siswa = $siswas[$i];
        $nilaiRata_p = [];
        $no = $i + 1;
        $dummyNilai = ['ranking' => '', 'deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => ''];
        $data['smt_active'] = $smt;
        $i = 0;
        $id_siswa = $siswa->id_siswa;
        $data['tp'] = $this->dashboard->getTahun();
        $tp = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $kelas = $this->kelas->get_one($id_kelas);
        $this->load->view('members/guru/rapor/prestasi/data');
        $nilaiRata[] = [];
        $dummyKode = ['1', '4', '11', '16', '21', '26'];
        $i++;
        foreach ($mapels as $mapel) {
            $nilaiRata_p[$id_siswa][$mapel->id_mapel] = $h == null ? 0 : $h->p_rata_rata;
            $nilaiRata_k[$id_siswa][$mapel->id_mapel] = $h == null ? 0 : $h->k_rata_rata;
            $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
            $nilaiHarian[$id_siswa][$mapel->id_mapel] = $h == null ? 0 : $h->jml;
            $h = $this->rapor->getJmlNilaiMapelHarianSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
            $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
            $nilaiPas[$id_siswa][$mapel->id_mapel] = $pas == null ? 0 : $pas->akhir;
            $pas = $this->rapor->getNilaiMapelPasSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
        }
        $this->load->view('members/guru/templates/header', $data);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $nilaiPas = [];
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        if (!($i < 6)) {
        }
        $user = $this->ion_auth->user()->row();
        $deskPrestasi = json_decode(json_encode($dummyDeskSaran));
        $this->load->model('Dashboard_model', 'dashboard');
        $i++;
        array_push($dummyDeskSaran, $s);
        $i = 0;
        $nilaiHarian = [];
        if (!(count($deskPrestasi) === 0)) {
        }
        $nilaiPts = [];
        $data = ['user' => $user, 'judul' => 'Ranking & Prestasi Kelas ', 'subjudul' => 'Input Nilai', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'nilaiHarian' => $nilaiHarian, 'nilaiRata_p' => $nilaiRata_p, 'nilaiRata_k' => $nilaiRata_k, 'nilaiRata' => $nilaiRata, 'nilaiPts' => $nilaiPts, 'nilaiPas' => $nilaiPas, 'deskRanking' => $deskPrestasi, 'mapels' => $mapels];
        $dummyDeskSaran = [];
        $deskPrestasi = $this->rapor->getDeskripsiCatatanByJenis($id_kelas, '1', $tp->id_tp, $smt->id_smt);
        $data['tp_active'] = $tp;
        $setting = $this->dashboard->getSetting();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapels = $this->master->getAllMapel();
        $nilai = [];
        $id_kelas = $guru->wali_kelas;
        $ns = $this->rapor->getRankingKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
    }
    public function savePrestasi()
    {
        $this->output_json($data);
        $data['status'] = $update;
        $tp = $this->dashboard->getTahunActive();
        foreach ($input as $d) {
            $update = $this->db->replace('rapor_data_catatan', $data);
            $data = ['id_catatan' => $d->id_catatan, 'id_kelas' => $d->kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'rank' => $d->rank, 'deskripsi' => $d->deskripsi, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('catatan', true));
        $smt = $this->dashboard->getSemesterActive();
    }
    public function importPrestasi($id_kelas)
    {
        $datas = [];
        $updated = 0;
        $input = json_decode($this->input->post('nilai', true));
        echo json_encode($updated);
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_prestasi', $data);
            $updated++;
            if (!$update) {
            }
        }
        $tp = $this->dashboard->getTahunActive();
        foreach ($input as $in) {
            $id_siswa = $in[12];
            $datas[] = ['id_ranking' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'ranking' => $in[4], 'deskripsi' => $in[5], 'p1' => $in[6], 'p1_desk' => $in[7], 'p2' => $in[8], 'p2_desk' => $in[9], 'p3' => $in[10], 'p3_desk' => $in[11]];
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $smt = $this->dashboard->getSemesterActive();
    }
    public function raporCatatan()
    {
        $nilai = [];
        if (!($i < 4)) {
        }
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        $dummyNilai = ['s' => '', 'i' => '', 'a' => '', 'op1' => '', 'op2' => '', 'op3' => ''];
        $this->load->view('members/guru/templates/footer');
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : unserialize($ns->nilai);
        $data['tp_active'] = $tp;
        $i++;
        $deskCatatan = json_decode(json_encode($dummyDeskCatatan));
        array_push($dummyDeskAbsensi, $s);
        array_push($dummyDeskCatatan, $s);
        $deskAbsensi = json_decode(json_encode($dummyDeskAbsensi));
        $s = ['id_sikap' => $id_kelas . 2 . $no, 'jenis' => '2', 'kode' => $no, 'deskripsi' => $this->rapor->getDummyDeskripsiCatatan()[$i], 'rank' => ''];
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        $dummyDeskCatatan = [];
        $i = 0;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/guru/rapor/catatan/data');
        $no = $i + 1;
        $deskAbsensi = $this->rapor->getDeskripsiCatatanByJenis($id_kelas, '1', $tp->id_tp, $smt->id_smt);
        if (!(count($deskCatatan) === 0)) {
        }
        if (!(count($deskAbsensi) === 0)) {
        }
        $dummyKode = ['1', '4', '11', '16'];
        $dummyRank = ['1 ~ 3', '4 ~ 10', '11 ~ 15', '16 > >'];
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        if (!($i < count($siswas))) {
        }
        $data['smt_active'] = $smt;
        $i++;
        $i++;
        $no = $i + 1;
        $kelas = $this->kelas->get_one($id_kelas);
        if (!($i < 6)) {
        }
        $dummyDeskAbsensi = [];
        $id_kelas = $guru->wali_kelas;
        $i = 0;
        $siswa = $siswas[$i];
        $s = ['id_catatan' => $id_kelas . 1 . $no, 'jenis' => '1', 'kode' => $dummyKode[$i], 'deskripsi' => $this->rapor->getDummyDeskripsiAbsensi()[$i], 'rank' => $dummyRank[$i]];
        $i = 0;
        $ns = $this->rapor->getCatatanKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $deskCatatan = $this->rapor->getDeskripsiCatatanByJenis($id_kelas, '2', $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Absensi & Catatan Kelas ', 'subjudul' => 'Input Nilai', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'deskAbsensi' => $deskAbsensi, 'deskCatatan' => $deskCatatan];
    }
    public function saveCatatan()
    {
        $tp = $this->dashboard->getTahunActive();
        $data['status'] = $update;
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($data);
        $input = json_decode($this->input->post('catatan', true));
        foreach ($input as $d) {
            $update = $this->db->replace('rapor_data_catatan', $data);
            $data = ['id_catatan' => $d->id_catatan, 'id_kelas' => $d->kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'rank' => $d->rank, 'deskripsi' => $d->deskripsi, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        }
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function importCatatan($id_kelas)
    {
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_catatan_wali', $data);
            if (!$update) {
            }
            $updated++;
        }
        $input = json_decode($this->input->post('nilai', true));
        $smt = $this->dashboard->getSemesterActive();
        echo json_encode($updated);
        $updated = 0;
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[10];
            $datas[] = ['id_catatan_wali' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'nilai' => serialize(['op1' => $in[3], 'op2' => $in[4], 'op3' => $in[5], 's' => $in[6], 'i' => $in[7], 'a' => $in[8]]), 'deskripsi' => $in[9], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            if (!($id_siswa != 'id')) {
            }
        }
    }
    public function raporFisik()
    {
        $other = '2';
        $smt = $this->dashboard->getSemesterActive();
        $deskFisik = $this->rapor->getDeskripsiFisikKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $other = '1';
        $nilai[$siswa->id_siswa] = $ns != null ? ['kondisi' => unserialize($ns->kondisi), 'smt' . $ns->id_smt => ['tinggi' => $ns->tinggi, 'berat' => $ns->berat, 'tp' => $ns->id_tp], 'smt' . $other => ['tinggi' => $ns2 != null ? $ns2->tinggi : '', 'berat' => $ns2 != null ? $ns2->berat : '', 'tp' => $tp->id_tp]] : $dummyNilai;
        $data['tp_active'] = $tp;
        $dummyNilai = ['kondisi' => ['telinga' => '', 'mata' => '', 'gigi' => '', 'lain' => ''], 'smt' . $smt->id_smt => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp], 'smt' . $other => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp]];
        $tp = $this->dashboard->getTahunActive();
        $deskFisik = json_decode(json_encode($dummyDeskFisik));
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $siswa = $siswas[$i];
        $i = 0;
        $this->load->view('members/guru/templates/header', $data);
        $data['smt_active'] = $smt;
        foreach ($jenis as $jns) {
            $s = ['id_fisik' => $id_kelas . $jns . $no, 'jenis' => $jns, 'kode' => $no, 'deskripsi' => $this->rapor->getDummyDeskripsiFisik($jns)[$i]];
            array_push($dummyDeskFisik, $s);
        }
        $no = $i + 1;
        if (!($deskFisik == null)) {
        }
        if ($smt->id_smt === '1') {
        }
        $data = ['user' => $user, 'judul' => 'Absensi & Catatan Kelas ', 'subjudul' => 'Input Nilai', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'deskFisik' => $deskFisik];
        $user = $this->ion_auth->user()->row();
        $kelas = $this->kelas->get_one($id_kelas);
        $data['smt'] = $this->dashboard->getSemester();
        $ns = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $dummyDeskFisik = [];
        $ns2 = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $other);
        $i++;
        $id_kelas = $guru->wali_kelas;
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/rapor/fisik/data');
        $i = 0;
        $i++;
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/guru/templates/footer');
        if (!($i < count($siswas))) {
        }
        $jenis = ['1', '2', '3', '4'];
        if (!($i < 4)) {
        }
        $data['tp'] = $this->dashboard->getTahun();
    }
    public function saveFisik()
    {
        $input = json_decode($this->input->post('fisik', true));
        $smt = $this->dashboard->getSemesterActive();
        $kelas = $this->input->post('kelas', true);
        foreach ($input as $d) {
            $update = $this->db->replace('rapor_data_fisik', $data);
            $jns = $d[0];
            $data = ['id_fisik' => $kelas . $jns . $kode, 'id_kelas' => $kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'deskripsi' => $d->deskripsi, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            $kode = $d[0];
        }
        $data['status'] = $update;
        $tp = $this->dashboard->getTahunActive();
        $update = false;
        $this->load->model('Dashboard_model', 'dashboard');
        $this->output_json($data);
    }
    public function importFisik($id_kelas)
    {
        $datas = [];
        echo json_encode($updated);
        $tp = $this->dashboard->getTahunActive();
        foreach ($input as $in) {
            if (!($id_siswa != 'id')) {
            }
            $datas[] = ['id_fisik' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_kelas' => $id_kelas, 'id_siswa' => $id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'tinggi' => $tinggi, 'berat' => $berat, 'kondisi' => serialize(['telinga' => $in[7], 'mata' => $in[8], 'gigi' => $in[9], 'lain' => $in[10]])];
            $id_siswa = $in[11];
            $berat = $smt->id_smt == 1 ? $in[5] : $in[6];
            $tinggi = $smt->id_smt == 1 ? $in[3] : $in[4];
        }
        $updated = 0;
        $smt = $this->dashboard->getSemesterActive();
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_fisik', $data);
            $updated++;
            if (!$update) {
            }
        }
        $input = json_decode($this->input->post('nilai', true));
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function raporNaik()
    {
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $siswas = $this->rapor->getKenaikanSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->model('Dashboard_model', 'dashboard');
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kenaikan/data');
        $id_kelas = $guru->wali_kelas;
        $smt = $this->dashboard->getSemesterActive();
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $data = ['user' => $user, 'judul' => 'Kenaikan Kelas ', 'subjudul' => 'Siswa Kelas ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswas' => $siswas];
        $kelas = $this->kelas->get_one($id_kelas);
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function saveNaik()
    {
        $tp = $this->dashboard->getTahunActive();
        foreach ($input as $d) {
            $data = ['id_naik' => $d->id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $d->id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'naik' => $d->naik];
            $update = $this->db->replace('rapor_naik', $data);
            if (!$update) {
            }
            $updated++;
        }
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Dashboard_model', 'dashboard');
        $updated = 0;
        echo json_encode($updated);
        $input = json_decode($this->input->post('naik', true));
    }
    public function cetakPts()
    {
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $data['siswas'] = $siswas;
        $siswa = $siswas[$i];
        $nilaiHarian = [];
        $data['tp_active'] = $tp;
        $this->load->view('members/guru/rapor/cetak/pts');
        $arr_siswas = [];
        $kkm = [];
        $data = ['user' => $user, 'judul' => 'Rapor PTS', 'subjudul' => 'Cetak Rapor PTS', 'setting' => $setting];
        $nilaiPts = [];
        $nilaiHarian = $this->rapor->getArrNilaiMapelHarianSiswa($arr_mapels, $arr_siswas, $tp->id_tp, $smt->id_smt);
        $kelas = $this->kelas->get_one($id_kelas);
        $tp = $this->dashboard->getTahunActive();
        $jurusan = $this->kelas->getJurusanById($kelas->jurusan_id);
        foreach ($kategori_mapel as $kk => $km) {
            if (in_array($km, $arrk)) {
            }
            array_push($arrk, $km->kode_kel_mapel);
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $i = 0;
        $id_kelas = $guru->wali_kelas;
        $nilaiPts = $this->rapor->getArrNilaiMapelPtsSiswa($arr_mapels, $arr_siswas, $tp->id_tp, $smt->id_smt);
        $id_siswa = $siswa->id_siswa;
        $data['nilai_pts'] = $nilaiPts;
        foreach ($mapels as $mapel) {
            $kkm[$mapel->id_mapel] = $settingRapor;
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            if (isset($settingRapor) && $settingRapor->kkm_tunggal == '1') {
            }
        }
        $data['smt'] = $this->dashboard->getSemester();
        $data['kelompoks'] = $kelompoks;
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->db->trans_complete();
        $data['nilai_harian'] = $nilaiHarian;
        $data['kelas'] = $kelas->nama_kelas;
        $setting = $this->dashboard->getSetting();
        $kelompoks = $this->master->getKodeKelompokMapel();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $mapels = $this->master->getAllMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $data['rapor'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['mapels'] = $mapels;
        $settingRapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        if (!($i < count($siswas))) {
        }
        foreach ($mapels as $mapel) {
            $arr_mapels[] = $mapel->id_mapel;
        }
        $this->load->view('members/guru/templates/header', $data);
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => ''];
        $i++;
        $data['kkm'] = $kkm;
        $arr_siswas[] = $id_siswa;
        $this->db->trans_start();
        $arr_mapels = [];
        $arrk = [];
    }
    public function cetakAkhir()
    {
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $other = '2';
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $nilai_rapor = $this->rapor->getNilaiRaporByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $dummyFisik = ['kondisi' => ['telinga' => '', 'mata' => '', 'gigi' => '', 'lain' => ''], 'smt' . $smt->id_smt => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp], 'smt' . $other => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp]];
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $data['fisik'] = $fisik;
        $mapels = $this->master->getAllMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);
        $jurusan = $this->kelas->getJurusanById($kelas->jurusan_id);
        $data['lvl_kelas'] = $kelas->level;
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data['kelas'] = $kelas->nama_kelas;
        $this->load->view('members/guru/templates/header', $data);
        $nf2 = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $other);
        foreach ($mapels as $mapel) {
            $nilai[$id_siswa][$mapel->id_mapel] = $nr;
            $dummyNilai = ['p_deskripsi' => '', 'k_rata_rata' => '', 'k_deskripsi' => '', 'k_predikat' => '', 'nilai' => '', 'predikat' => ''];
            $key_mapel = array_search($mapel->id_mapel . $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, array_column($nilai_rapor, 'id_nilai_harian'));
            if (!($key_mapel !== false)) {
            }
            $nr = $nilai_rapor[$key_mapel];
        }
        $data['mapels'] = $mapels;
        $dummyAbsen = ['s' => ' - ', 'i' => ' - ', 'a' => ' - ', 'saran' => ''];
        foreach ($nilai_sikap as $nls) {
            $sikap[$id_siswa][2] = ['deskripsi' => $nls == null ? '' : $nls->deskripsi, 'predikat' => $nls == null ? $dummySikap : unserialize($nls->nilai)];
            if (!($nls->id_siswa == $id_siswa && $nls->jenis == '1')) {
            }
            $sikap[$id_siswa][1] = ['deskripsi' => $nls == null ? '' : $nls->deskripsi, 'predikat' => $nls == null ? $dummySikap : unserialize($nls->nilai)];
            if (!($nls->id_siswa == $id_siswa && $nls->jenis == '2')) {
            }
        }
        $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa] : ['nilai' => $dummyAbsen];
        $nilai = [];
        $sikap = [];
        $id_kelas = $guru->wali_kelas;
        $siswas = $this->rapor->getDetailSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $nilaiEkstra = [];
        $data['nilai_ekstra'] = $nilaiEkstra;
        $kkm = [];
        $fisik = [];
        $data['tp_active'] = $tp;
        $other = '1';
        foreach ($kategori_mapel as $kk => $km) {
            if (in_array($km, $arrk)) {
            }
            array_push($arrk, $km->kode_kel_mapel);
        }
        $data['deskripsi'] = $desks;
        $this->load->view('members/guru/templates/footer');
        if (!($i < count($siswas))) {
        }
        if ($smt->id_smt === '1') {
        }
        $data['absensi'] = $absensi;
        $sikap[$id_siswa][2] = ['deskripsi' => '', 'predikat' => $dummySikap];
        $nilai_sikap = $this->rapor->getNilaiSikapByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $mapelEkstra = [];
        if (count($nilai_sikap) > 0) {
        }
        $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => ''];
        $id_siswa = $siswa->id_siswa;
        $settingRapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $sikap[$id_siswa][1] = ['deskripsi' => '', 'predikat' => $dummySikap];
        $data['rapor'] = $settingRapor;
        $i++;
        $data['nilai_rapor'] = $nilai_rapor;
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $desks = [];
        $data['nilai'] = $nilai;
        $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['smt_name'] = $this->dashboard->getSemesterById($smt->id_smt);
        $desks[$id_siswa] = isset($prestasis[$id_siswa]) ? $prestasis[$id_siswa] : $dummyDesks;
        $nf = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $this->load->model('Dashboard_model', 'dashboard');
        $dummySikap = ['predikat' => ''];
        $data['mapel_ekstra'] = $mapelEkstra;
        $i = 0;
        $data['kelompoks'] = $kelompoks;
        foreach ($ekstras as $ext) {
            foreach ($arrEkstra as $ar) {
                $id_ekstra = $ar->ekstra;
                $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? $dummyEkstra : $ne;
                $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                if (!($id_ekstra != null)) {
                }
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
        }
        $data['tp_name'] = $this->dashboard->getTahunById($tp->id_tp);
        $smt = $this->dashboard->getSemesterActive();
        $data['smt'] = $this->dashboard->getSemester();
        $siswa = $siswas[$i];
        $data['kkm'] = $kkm;
        $data['siswas'] = $siswas;
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $kelompoks = $this->master->getKodeKelompokMapel();
        $data['guru'] = $guru;
        $data['sikap'] = $sikap;
        $tp = $this->dashboard->getTahunActive();
        $absensi = [];
        $fisik[$siswa->id_siswa] = $nf != null ? ['kondisi' => unserialize($nf->kondisi), 'smt' . $nf->id_smt => ['tinggi' => $nf->tinggi, 'berat' => $nf->berat], 'smt' . $other => ['tinggi' => $nf2 != null ? $nf2->tinggi : '', 'berat' => $nf2 != null ? $nf2->berat : '']] : $dummyFisik;
        $kkm = $this->rapor->getAllKkmRaporAkhir($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/rapor/cetak/akhir');
        $kelas = $this->kelas->get_one($id_kelas);
        $data = ['user' => $user, 'judul' => 'Rapor Akhir', 'subjudul' => 'Cetak Rapor Akhir', 'setting' => $setting];
        $arrk = [];
    }
    public function cetakLeger()
    {
        $nilaiPts = [];
        $data['ekstras'] = $ekstras;
        $this->load->view('members/guru/rapor/leger/data');
        $data = ['user' => $user, 'judul' => 'Leger Kelas ', 'subjudul' => 'Cetak Leger Kelas ', 'setting' => $setting];
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $data['nilai_ekstra'] = $nilaiEkstra;
        $user = $this->ion_auth->user()->row();
        $smt = $this->dashboard->getSemesterActive();
        $data['sikap'] = $sikap;
        $data['tp_active'] = $tp;
        $nilaiEkstra = [];
        $data['mapel_ekstra'] = $mapelEkstra;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['nilai_pts'] = (array) json_decode(json_encode($nilaiPts));
        $mapels = $this->master->getAllMapel();
        $this->load->view('members/guru/templates/header', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $mapelEkstra = [];
        $data['nilai'] = (array) json_decode(json_encode($nilai));
        foreach ($mapels as $mapel) {
            $dummyNilai = ['k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
            $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
            $nilai[$id_siswa][$mapel->id_mapel] = $nr == null ? $dummyNilai : $nr;
            $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
            foreach ($ekstras as $ext) {
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                foreach ($arrEkstra as $ar) {
                    if (!($id_ekstra != null)) {
                    }
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    $id_ekstra = $ar->ekstra;
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                }
            }
            $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
            $sikap[$siswa->id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];
            $nr = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
            $dummySikap = ['predikat' => ''];
            $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
            $sikap[$siswa->id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa]->nilai : $dummyAbsen;
            if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
            }
            $kkm[$mapel->id_mapel] = $setting_rapor;
            $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
        }
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $i++;
        $absensi = [];
        if (!($i < count($siswas))) {
        }
        $data['rapor'] = $setting_rapor;
        $data['kelases'] = $kelases;
        $kkm = [];
        $data['smt_active'] = $smt;
        $data['siswas'] = $siswas;
        $id_kelas = $guru->wali_kelas;
        $nilai = [];
        $data['deskripsi'] = $desks;
        $data['mapels'] = $mapels;
        $id_siswa = $siswa->id_siswa;
        $desks = [];
        $siswa = $siswas[$i];
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $i = 0;
        $sikap = [];
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['kkm'] = $kkm;
        $this->load->model('Dashboard_model', 'dashboard');
        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kelases = $this->kelas->get_one($id_kelas);
        $data['smt'] = $this->dashboard->getSemester();
        $data['absensi'] = $absensi;
    }
    public function downloadLeger()
    {
        $nilaiEkstra = [];
        $k5 = [];
        $kkm = [];
        if (!($i < count($siswas))) {
        }
        $this->output_json($data);
        $k3 = [];
        $sikap = [];
        $p4[] = $nilai->p4;
        $mapelEkstra = [];
        $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt);
        $i = 0;
        $data['tp'] = $this->dashboard->getTahun();
        $id_siswa = $siswa->id_siswa;
        $p2[] = $nilai->p2;
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['kkm'] = $kkm;
        $nisn = [];
        $id_kelas = $guru->wali_kelas;
        $data['siswas'] = $siswas;
        $data['mapel_ekstra'] = $mapelEkstra;
        $no = [];
        $mapels = $this->master->getAllMapel();
        $i = 0;
        $desks = [];
        $nama[] = $siswa->nama;
        $k6 = [];
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $absensi = [];
        $p7[] = $nilai->p7;
        $p5[] = $nilai->p5;
        $tp = $this->dashboard->getTahunActive();
        $k1 = [];
        $data['absensi'] = $absensi;
        $data['nilai_ekstra'] = $nilaiEkstra;
        $nilaiPts = [];
        $k4[] = $nilai->k4;
        $nilai = [];
        $p8[] = $nilai->p8;
        $p8 = [];
        $p2 = [];
        $data['deskripsi'] = $desks;
        $k4 = [];
        $data['nilai'] = (array) json_decode(json_encode($nilai));
        $setting = $this->dashboard->getSetting();
        $k3[] = $nilai->k3;
        $nilai = $nilai[$siswa->id_siswa];
        $p1[] = $nilai->p1;
        $data['rapor'] = $setting_rapor;
        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $i++;
        $user = $this->ion_auth->user()->row();
        $this->load->model('Dashboard_model', 'dashboard');
        $p1 = [];
        $data['nilai_pts'] = (array) json_decode(json_encode($nilaiPts));
        $k7[] = $nilai->k7;
        $data['mapels'] = $mapels;
        $k2 = [];
        $siswa = $siswas[$i];
        $p7 = [];
        $k6[] = $nilai->k6;
        $data['kelases'] = $kelases;
        $data['tp_active'] = $tp;
        $data['ekstras'] = $ekstras;
        $no[] = $i + 1;
        $p3 = [];
        if (!($i < count($siswas))) {
        }
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $kelases = $this->kelas->get_one($id_kelas);
        $k2[] = $nilai->k2;
        $siswa = $siswas[$i];
        $k5[] = $nilai->k5;
        $nisn[] = $siswa->nisn;
        $data['smt_active'] = $smt;
        $k8 = [];
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($mapels as $mapel) {
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => '', 'saran' => ''];
            if ($setting_rapor->kkm_tunggal == '1') {
            }
            $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
            foreach ($ekstras as $ext) {
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    if (!($id_ekstra != null)) {
                    }
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                }
            }
            $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
            $sikap[$siswa->id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
            $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
            $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
            $kkm[$mapel->id_mapel] = $setting_rapor;
            $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa]->nilai : ['nilai' => $dummyAbsen];
            $sikap[$siswa->id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];
            $nr = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
            $dummySikap = ['predikat' => ''];
            $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$id_siswa][$mapel->id_mapel] = $nr == null ? $dummyNilai : $nr;
            $dummyNilai = ['k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
        }
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $data['sikap'] = $sikap;
        $k8[] = $nilai->k8;
        $k7 = [];
        $k1[] = $nilai->k1;
        $p4 = [];
        $i++;
        $p6 = [];
        $p5 = [];
        $p6[] = $nilai->p6;
        $nama = [];
        $p3[] = $nilai->p3;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
    }
    public function dkn()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $data['kelases'] = $kelases;
        $setting = $this->dashboard->getSetting();
        $this->load->view('members/guru/templates/footer');
        $data['kkm'] = $kkm;
        $data['deskripsi'] = $desks;
        $data['absensi'] = $absensi;
        $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilaiPts = [];
        $smt = $this->dashboard->getSemesterActive();
        $kelases = $this->kelas->get_one($id_kelas);
        $this->load->view('members/guru/templates/header', $data);
        $nilaiEkstra = [];
        $sikap = [];
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Daftar Kumpulan Nilai Kelas ', 'subjudul' => 'Cetak DKN ', 'setting' => $setting];
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['rapor'] = $setting_rapor;
        $data['nilai'] = $nilai;
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $data['nilai_pts'] = $nilaiPts;
        $kkm = [];
        $mapels = $this->master->getAllMapel();
        $user = $this->ion_auth->user()->row();
        $id_kelas = $guru->wali_kelas;
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $data['siswas'] = $siswas;
        if (!($i < count($siswas))) {
        }
        $data['mapel_ekstra'] = $mapelEkstra;
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($mapels as $mapel) {
            foreach ($ekstras as $ext) {
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                foreach ($arrEkstra as $ar) {
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    if (!($id_ekstra != null)) {
                    }
                    $id_ekstra = $ar->ekstra;
                }
            }
            $sikap[$siswa->id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];
            $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
            $absensi[$id_siswa] = $nd == null ? $dummyAbsen : unserialize($nd->nilai);
            $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
            $nd = $this->rapor->getRaporDeskripsi($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
            $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
            if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
            }
            $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => '', 'saran' => ''];
            $kkm[$mapel->id_mapel] = $setting_rapor;
            $desks[$id_siswa] = $nd == null ? json_decode(json_encode($dummyDesks)) : $nd;
            $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
            $nr['mapel'] = $mapel->nama_mapel;
            $nilai[$id_siswa][$mapel->id_mapel] = $nr == null ? $dummyNilai : $nr;
            $nr = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
            $dummyNilai = ['mapel' => $mapel->nama_mapel, 'k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
            $sikap[$siswa->id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
            $dummySikap = ['predikat' => ''];
        }
        $mapelEkstra = [];
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $i++;
        $siswa = $siswas[$i];
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        $id_siswa = $siswa->id_siswa;
        $this->load->view('members/guru/rapor/dkn/data');
        $data['smt_active'] = $smt;
        $data['nilai_ekstra'] = $nilaiEkstra;
        $data['sikap'] = $sikap;
        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['ekstras'] = $ekstras;
        $absensi = [];
        $data['mapels'] = $mapels;
        $desks = [];
        $i = 0;
    }
}
```

---

## File: application/controllers_decoded/Settings.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Settings extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        show_error('Hanya Admin yang boleh mengakses halaman ini', 403, 'Akses dilarang');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->helper('directory');
        if ($this->ion_auth->is_admin()) {
        }
        parent::__construct();
        $this->load->model('Settings_model', 'settings');
        redirect('auth');
        $this->load->library('upload');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data = ['user' => $user, 'judul' => 'Profile Sekolah', 'subjudul' => '', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_footer');
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('setting/data');
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_header', $data);
        $user = $this->ion_auth->user()->row();
    }
    public function dbManager()
    {
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Backup dan Restore', 'subjudul' => 'Backup dan Restore'];
        $data['setting'] = $this->settings->getSetting();
        $data['list'] = directory_map('./backups/');
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('setting/db');
        $data['smt_active'] = $this->dashboard->getSemesterActive();
    }
    function uploadFile($logo)
    {
        $data['size'] = $_FILES['logo']['size'];
        $data['status'] = false;
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $data['type'] = $_FILES['logo']['type'];
        $result = $this->upload->data();
        $config['overwrite'] = true;
        if (!$this->upload->do_upload('logo')) {
        }
        $config['file_name'] = $logo;
        $data['src'] = base_url() . 'uploads/settings/' . $result['file_name'];
        $data['src'] = '';
        if (isset($_FILES['logo']['name'])) {
        }
        $config['upload_path'] = './uploads/settings/';
        $this->output_json($data);
        $data['status'] = true;
        $data['src'] = $this->upload->display_errors();
        $this->upload->initialize($config);
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
    }
    function deleteFile()
    {
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!unlink($file_name)) {
        }
        $src = $this->input->post('src');
        echo 'File Delete Successfully';
    }
    public function saveSetting()
    {
        $insert = ['sekolah' => $sekolah, 'nss' => $nss, 'npsn' => $npsn, 'jenjang' => $jenjang, 'satuan_pendidikan' => $satuan_pendidikan, 'alamat' => $alamat, 'desa' => $desa, 'kota' => $kota, 'kecamatan' => $kec, 'kode_pos' => $kodepos, 'provinsi' => $prov, 'web' => $web, 'fax' => $fax, 'email' => $email, 'telp' => $tlp, 'kepsek' => $kepsek, 'nip' => $nip, 'tanda_tangan' => str_replace(base_url(), '', $tanda_tangan ?? ''), 'nama_aplikasi' => $nama_aplikasi, 'logo_kanan' => str_replace(base_url(), '', $logo_kanan ?? ''), 'logo_kiri' => str_replace(base_url(), '', $logo_kiri ?? '')];
        $prov = $this->input->post('provinsi', true);
        $tlp = $this->input->post('tlp', true);
        $kota = $this->input->post('kota', true);
        $update = $this->db->update('setting', $insert);
        $sekolah = $this->input->post('nama_sekolah', true);
        $desa = $this->input->post('desa', true);
        $nip = $this->input->post('nip', true);
        $tanda_tangan = $this->input->post('tanda_tangan', true);
        $web = $this->input->post('web', true);
        $email = $this->input->post('email', true);
        $npsn = $this->input->post('npsn', true);
        $satuan_pendidikan = $this->input->post('satuan_pendidikan', true);
        $logo_kiri = $this->input->post('logo_kiri', true);
        $kepsek = $this->input->post('kepsek', true);
        $alamat = $this->input->post('alamat', true);
        $nss = $this->input->post('nss', true);
        $kodepos = $this->input->post('kode_pos', true);
        $this->db->where('id_setting', 1);
        $this->output_json($update);
        $logo_kanan = $this->input->post('logo_kanan', true);
        $kec = $this->input->post('kec', true);
        $jenjang = $this->input->post('jenjang', true);
        $fax = $this->input->post('fax', true);
        $nama_aplikasi = $this->input->post('nama_aplikasi', true);
    }
}
```

---

## File: application/controllers_decoded/Siswa.php

```php
<?php

class Siswa extends CI_Controller
{
    public function __construct()
    {
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('user_agent');
        if ($this->ion_auth->logged_in()) {
        }
        $this->form_validation->set_error_delimiters('', '');
        parent::__construct();
        $this->load->library('upload');
        redirect('auth');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            }
            sort($subArray);
        }
    }
    public function index()
    {
    }
    private function arrToUpper($val)
    {
        return strtoupper($val ?? '');
    }
    public function getPost()
    {
        $kode = $this->input->get('kelas', true);
        $this->output_json($post);
        $this->load->model('Post_model', 'post');
        $post = $this->post->getPostForUser('\'%siswa%\'', '\'%' . $kode . '%\'');
    }
    public function getComment($id_post, $page)
    {
        $comment = $this->db->get()->result();
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->from('post_comments a');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->where('a.id_post', $id_post);
        $this->output_json($comment);
        $this->db->limit($perPage, $offset);
        $perPage = 5;
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $offset = $page * $perPage;
    }
    public function getReplies($id_comment, $page)
    {
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->where('a.id_comment', $id_comment);
        $offset = $page * $perPage;
        $this->db->from('post_reply a');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->limit($perPage, $offset);
        $this->output_json($replies);
        $replies = $this->db->get()->result();
        $perPage = 5;
    }
    public function saveKomentar()
    {
        $this->output_json($comment);
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $user = $this->ion_auth->user()->row();
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $smt = $this->dashboard->getSemesterActive();
        $id = $this->db->insert_id();
        $this->db->order_by('a.tanggal', 'desc');
        $tp = $this->dashboard->getTahunActive();
        $data = ['type' => '1', 'id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->where('a.id_comment', $id);
        $this->load->model('Cbt_model', 'cbt');
        $dari = $siswa->id_siswa;
        $this->db->from('post_comments a');
        $this->load->model('Dashboard_model', 'dashboard');
        $comment = $this->db->get()->result();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $insert = $this->db->replace('post_comments', $data);
        $dari_group = 3;
    }
    public function saveBalasan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $replies = $this->db->get()->result();
        $this->db->from('post_reply a');
        $insert = $this->db->replace('post_reply', $data);
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $dari = $siswa->id_siswa;
        $dari_group = 3;
        $this->db->where('a.id_reply', $id);
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $id = $this->db->insert_id();
        $tp = $this->dashboard->getTahunActive();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Post_model', 'post');
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($replies);
    }
    public function jadwalPelajaran()
    {
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $data['method'] = 'add';
        $data['smt_active'] = $smt;
        $setting = $this->dashboard->getSetting();
        $i++;
        if ($jadm == null) {
        }
        $user = $this->ion_auth->user()->row();
        $this->load->view('members/siswa/templates/header', $data);
        $i = 0;
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('members/siswa/jadwal/data');
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/siswa/templates/footer');
        $this->load->model('Dashboard_model', 'dashboard');
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $data['mapels'] = $this->master->getAllMapel();
        $jadwal_mapel[] = ['jadwal' => $this->kelas->getDummyJadwalMapel($tp->id_tp, $smt->id_smt, $i + 1, $siswa->id_kelas)];
        $data['jadwal_mapel'] = $jadwal_mapel;
        if ($jadk == null) {
        }
        $tp = $this->dashboard->getTahunActive();
        $data['method'] = 'edit';
        $data['jadwal_kbm'] = $jadk;
        foreach ($jadm as $j) {
            $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $siswa->id_kelas)];
        }
        $data['tp_active'] = $tp;
        $smt = $this->dashboard->getSemesterActive();
        $this->load->model('Kelas_model', 'kelas');
        $jml_mapel = $jadk == null ? 1 : $jadk->kbm_jml_mapel_hari;
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $this->load->model('Master_model', 'master');
        $data['tp'] = $this->dashboard->getTahun();
        if (!($i < $jml_mapel)) {
        }
        $data['jadwal_kbm'] = json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $siswa->id_kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]));
        $data['smt'] = $this->dashboard->getSemester();
        $data['id_kelas'] = $siswa->id_kelas;
        $data['running_text'] = $this->dashboard->getRunningText();
    }
    public function kehadiran()
    {
        $this->load->view('members/siswa/templates/footer');
        $bulan = date('m');
        $data['jadwals'] = $jadwals;
        $mapels = $this->master->getAllMapel();
        $today = date('Y-m-d');
        $kbm->istirahat = unserialize($kbm->istirahat ?? '');
        $this->load->model('Cbt_model', 'cbt');
        $data['tp'] = $this->dashboard->getTahun();
        $tgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $i = 0;
        $materi_sebulan = [];
        $result = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, $siswa->id_kelas, null);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['tp_active'] = $tp;
        $smt = $this->dashboard->getSemesterActive();
        $data['jadwal'] = isset($jadwals[$day]) && $day != 7 ? $jadwals[$day] : [];
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->model('Master_model', 'master');
        $tp = $this->dashboard->getTahunActive();
        $tahun = date('Y');
        $jadwals = [];
        $user = $this->ion_auth->user()->row();
        $data['running_text'] = $this->dashboard->getRunningText();
        if (!($i < $tgl)) {
        }
        $data['sebulan'] = ['log' => isset($logs[$siswa->id_siswa]) ? $logs[$siswa->id_siswa] : [], 'materis' => $materi_sebulan];
        foreach ($mapels as $mpl) {
            array_push($arrIdMapel, $mpl->id_mapel);
        }
        $t = $i + 1 < 10 ? '0' . ($i + 1) : $i + 1;
        $data['kbm'] = $kbm;
        $this->load->model('Kelas_model', 'kelas');
        $day = date('N', strtotime($today));
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Absensi', 'subjudul' => 'Kehadiran Siswa', 'setting' => $this->dashboard->getSetting()];
        foreach ($result as $row) {
            $jadwals[$row->id_hari][$row->jam_ke] = $row;
        }
        if ($kbm != null) {
        }
        $logs = $this->kelas->getRekapBulananSiswa(null, $siswa->id_kelas, $tahun, $bulan);
        $data['mapels'] = $mapels;
        $arrIdMapel = [];
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $materi_sebulan[$t] = $this->kelas->getAllMateriByTgl($siswa->id_kelas, $tahun . '-' . $bulan . '-' . $t, $arrIdMapel);
        $i++;
        $data['smt_active'] = $smt;
        $data['sebulan'] = ['log' => [], 'materis' => []];
        $this->load->view('members/siswa/absensi/data');
        $kbm = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
    }
    public function materi()
    {
        $this->getTugasMateri('1');
    }
    public function tugas()
    {
        $this->getTugasMateri('2');
    }
    private function getTugasMateri($jenis)
    {
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => $jenis == '1' ? 'Materi' : 'Tugas', 'subjudul' => $jenis == '1' ? 'materi' : 'tugas', 'setting' => $setting];
        $materis = [];
        $data['tp_active'] = $tp;
        $today = date('Y-m-d');
        $data['jadwals'] = $jadwal_seminggu;
        $last_week = [date('Y-m-d', strtotime('-7 days')), date('Y-m-d', strtotime('-6 days')), date('Y-m-d', strtotime('-5 days')), date('Y-m-d', strtotime('-4 days')), date('Y-m-d', strtotime('-3 days')), date('Y-m-d', strtotime('-2 days')), date('Y-m-d', strtotime('-1 days')), date('Y-m-d')];
        $this->load->model('Dashboard_model', 'dashboard');
        $data['tp'] = $this->dashboard->getTahun();
        $data['logs'] = $logs;
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $jenis == null ? '1' : '2';
        $smt = $this->dashboard->getSemesterActive();
        $jadwal_seminggu = $this->kelas->loadJadwalSiswaSeminggu($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $data['jenis'] = $jenis;
        $materi_seminggu = $this->kelas->getMateriSiswaSeminggu($tp->id_tp, $smt->id_smt, $siswa->id_kelas, $jenis);
        $mapels = $this->dropdown->getAllMapel();
        $logs = [];
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['materis'] = $materis;
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('members/siswa/templates/header', $data);
        $data['kbm'] = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('members/siswa/materi/data');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->view('members/siswa/templates/footer');
        $this->load->model('Kelas_model', 'kelas');
        foreach ($last_week as $day) {
            $logs[$day] = $log;
            $log = $this->kelas->getStatusMateriSiswaByJadwal($siswa->id_siswa, $arrIdKjms);
            $arrIdKjms = [];
            if (!(count($arrIdKjms) > 0)) {
            }
            if (!isset($jadwal_seminggu[$idhari])) {
            }
            foreach ($jadwal_seminggu[$idhari] as $kjam => $val) {
                $materis[$day][$kjam] = isset($materi_seminggu[$day]) && isset($materi_seminggu[$day][$kjam]) ? $materi_seminggu[$day][$kjam] : $dummy;
                $dummy->nama_mapel = isset($mapels[$val->id_mapel]) ? $mapels[$val->id_mapel] : '';
                $dummy = new stdClass();
                $dummy->id_mapel = $val->id_mapel;
                $dummy->id_jadwal = $val->id_jadwal;
            }
            foreach ($materis[$day] as $mtr) {
                if (!isset($mtr->id_kjm)) {
                }
                array_push($arrIdKjms, $mtr->id_kjm);
            }
            $idhari = date('N', strtotime($day));
            $log = [];
            $materis[$day] = [];
        }
        $data['running_text'] = $this->dashboard->getRunningText();
        $setting = $this->dashboard->getSetting();
        $data['week'] = $last_week;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $user = $this->ion_auth->user()->row();
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function seminggu()
    {
        $jadwal = $this->kelas->loadJadwalSiswaHariIni($tp->id_tp, $smt->id_smt, $id_kelas, $numday);
        $this->load->model('Kelas_model', 'kelas');
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $id_kelas);
        $arrIdKjm = [];
        $id_siswa = $this->input->get('id_siswa', true);
        $this->load->model('Dropdown_model', 'dropdown');
        $materi['kbm'] = $jadk;
        $jadk->istirahat = unserialize($jadk->istirahat ?? '');
        $tp = $this->dashboard->getTahunActive();
        $materi = [];
        if (!(count($arrIdKjm) > 0)) {
        }
        $today = date($tgl);
        foreach ($materi['materi'] as $mtr) {
            array_push($arrIdKjm, $mtr->id_kjm);
            if (!isset($mtr->id_kjm)) {
            }
        }
        $materi['logs'] = (array) $this->kelas->getStatusMateriSiswaByJadwal($id_siswa, $arrIdKjm);
        $mapels = $this->dropdown->getAllMapel();
        $materi['seminggu'] = $this->kelas->loadJadwalSiswaSeminggu($tp->id_tp, $smt->id_smt, $id_kelas);
        $materi_hari_ini = $this->kelas->getMateriSiswa($id_kelas, $today, $jenis);
        $id_kelas = $this->input->get('id_kelas', true);
        $jenis = $this->input->get('jenis', true);
        $smt = $this->dashboard->getSemesterActive();
        $numday = date('N', strtotime($tgl));
        $tgl = $this->input->get('tgl', true);
        $materi['jadwal'] = $jadwal;
        $this->output_json($materi);
        foreach ($jadwal as $key => $value) {
            $materi['materi'][$key] = isset($materi_hari_ini[$key]) ? $materi_hari_ini[$key] : ['id_mapel' => $value->id_mapel, 'id_jadwal' => $value->id_jadwal, 'nama_mapel' => isset($mapels[$value->id_mapel]) ? $mapels[$value->id_mapel] : ''];
        }
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function bukaMateri($id_kjm, $jamke)
    {
        $this->bukaTugasMateri($id_kjm, $jamke, '1');
    }
    public function bukaTugas($id_kjm, $jamke)
    {
        $this->bukaTugasMateri($id_kjm, $jamke, '2');
    }
    private function bukaTugasMateri($id_kjm, $jamke, $jenis)
    {
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/materi/view');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $data['running_text'] = $this->dashboard->getRunningText();
        $data['jamke'] = $jamke;
        $data['tp_active'] = $tp;
        $smt = $this->dashboard->getSemesterActive();
        $data['materi'] = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis);
        $this->load->model('Dashboard_model', 'dashboard');
        $logs = $this->kelas->getStatusMateriSiswa($id_kjm);
        $this->load->view('members/siswa/templates/footer');
        $logs[$siswa->id_siswa]->file = unserialize($logs[$siswa->id_siswa]->file ?? '');
        $this->load->model('Kelas_model', 'kelas');
        $data['kjm'] = $id_kjm;
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data['logs'] = isset($logs[$siswa->id_siswa]) ? $logs[$siswa->id_siswa] : null;
        $data['smt_active'] = $smt;
        $this->load->model('Cbt_model', 'cbt');
        if (!isset($logs[$siswa->id_siswa])) {
        }
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => $jenis == '1' ? 'Materi' : 'Tugas', 'subjudul' => 'Kerjakan', 'setting' => $this->dashboard->getSetting()];
    }
    public function saveLogMateri()
    {
        $id_kjm = $this->input->get('id_kjm', true);
        $jamke = $this->input->get('jamke', true);
        $this->load->model('Kelas_model', 'kelas');
        $mapel = $this->input->get('mapel', true);
        $this->output_json($this->kelas->saveLog('log_materi', $id_siswa, $id_kjm, $jamke, $mapel, 'Membuka materi'));
        $id_siswa = $this->input->get('id_siswa', true);
    }
    public function saveLogTugas()
    {
        $jamke = $this->input->get('jamke', true);
        $this->output_json($this->kelas->saveLog('log_materi', $id_siswa, $id_kjm, $jamke, $mapel, 'Membuka tugas'));
        $this->load->model('Kelas_model', 'kelas');
        $id_kjm = $this->input->get('id_kjm', true);
        $mapel = $this->input->get('mapel', true);
        $id_siswa = $this->input->get('id_siswa', true);
    }
    public function saveFileMateriSelesai()
    {
        $update = $this->db->insert('log_materi', $insert);
        $update = $this->db->update('log_materi', $insert);
        $q = $this->db->get('log_materi');
        if ($q->num_rows() > 0) {
        }
        $id_siswa = $this->input->post('id_siswa', true);
        foreach ($attach as $at) {
            if (!($at->name != null)) {
            }
            $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
        }
        $insert = ['id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'finish_time' => date('Y-m-d H:i:s'), 'jam_ke' => $jamke, 'log_desc' => 'Menyelesaikan materi', 'text' => $isi_materi, 'file' => serialize($src_file)];
        $this->db->where('id_log', $id_log);
        $src_file = [];
        $attach = json_decode($this->input->post('attach', true));
        $isi_materi = $this->input->post('isi_materi', true);
        $this->db->where('id_log', $id_log);
        $id_kjm = $this->input->post('id_kjm', true);
        $data['status'] = $update;
        $this->db->set('id_log', $id_log);
        $jamke = $this->input->post('jamke', true);
        $this->output_json($data);
        $id_log = $id_siswa . $id_kjm;
    }
    public function saveFileTugasSelesai()
    {
        $update = $this->db->update('log_tugas', $insert);
        if ($q->num_rows() > 0) {
        }
        $this->db->where('id_log', $id_log);
        $this->db->where('id_log', $id_log);
        $q = $this->db->get('log_tugas');
        $jamke = $this->input->post('jamke', true);
        $id_kjm = $this->input->post('id_kjm', true);
        foreach ($attach as $at) {
            if (!($at->name != null)) {
            }
            $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
        }
        $id_log = $id_siswa . $id_kjm;
        $attach = json_decode($this->input->post('attach', true));
        $id_siswa = $this->input->post('id_siswa', true);
        $data['status'] = $update;
        $insert = ['id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'jam_ke' => $jamke, 'log_desc' => 'Menyelesaikan tugas', 'text' => $isi_tugas, 'file' => serialize($src_file)];
        $this->output_json($data);
        $src_file = [];
        $update = $this->db->insert('log_tugas', $insert);
        $isi_tugas = $this->input->post('isi_tugas', true);
        $this->db->set('id_log', $id_log);
    }
    function uploadFile()
    {
        $config['overwrite'] = FALSE;
        $result = $this->upload->data();
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $data['status'] = true;
        $data['src'] = 'uploads/file_siswa/' . $result['file_name'];
        $config['upload_path'] = './uploads/file_siswa/';
        $data['status'] = false;
        $config['allowed_types'] = 'jpg|jpeg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi|doc|docx|xls|xlsx|ppt|pptx|csv|pdf|rtf|txt';
        $data['src'] = $this->upload->display_errors();
        $data['size'] = $_FILES['file_uploads']['size'];
        $data['type'] = $_FILES['file_uploads']['type'];
        if (!isset($_FILES['file_uploads']['name'])) {
        }
        $max_size = $this->input->post('max-size', true);
        $config['max_size'] = $max_size;
        $this->upload->initialize($config);
        $this->output_json($data);
        if (!$this->upload->do_upload('file_uploads')) {
        }
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        echo 'File Delete Successfully';
        if (!unlink($src)) {
        }
    }
    public function leavecbt($id_jadwal, $id_siswa)
    {
        $this->db->set('agent', 'illegal agent');
        $this->db->update('log_ujian');
        redirect('logout', 'refresh');
        $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1');
        $this->db->set('device', 'illegal device');
    }
    public function cbt()
    {
        $this->load->view('members/siswa/cbt/data');
        $data['cbt_jadwal'] = $jadwal_ujian_aktif;
        $jadwal_ujian_aktif = [];
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $this->load->model('Kelas_model', 'kelas');
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        foreach ($cbt_jadwal as $key => $jadwal) {
            $mulai = strtotime($jadwal->tgl_mulai);
            if (!($today >= $mulai && $today <= $selesai)) {
            }
            $jadwal_ujian_aktif[$jadwal->tgl_mulai] = [];
            if (isset($jadwal_ujian_aktif[$jadwal->tgl_mulai])) {
            }
            array_push($jadwal_ujian_aktif[$jadwal->tgl_mulai], $jadwal);
            array_push($jadwal_ujian_aktif[$jadwal->tgl_mulai], $jadwal);
            $timer[$jadwal->id_jadwal] = $this->cbt->getElapsed($siswa->id_siswa . '0' . $jadwal->id_jadwal);
            if (!($jadwal->soal_agama == '-' || $jadwal->soal_agama == '0' || $jadwal->soal_agama == $siswa->agama)) {
            }
            $kk = unserialize($jadwal->bank_kelas ?? '');
            $selesai = strtotime($jadwal->tgl_selesai);
            if (!($cbt_info != null && in_array($cbt_info->id_kelas, $arrKelasCbt) && $jadwal->status === '1')) {
            }
            foreach ($kk as $k) {
                array_push($arrKelasCbt, $k['kelas_id']);
            }
            $arrKelasCbt = [];
        }
        $data['elapsed'] = $timer;
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->load->view('members/siswa/templates/footer');
        $this->load->model('Dashboard_model', 'dashboard');
        $timer = [];
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->model('Dropdown_model', 'dropdown');
        $data['sesi'] = $this->dropdown->getAllWaktuSesi();
        $cbt_jadwal = $this->cbt->getJadwalCbt($tp->id_tp, $smt->id_smt, $siswa->level_id);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $data['guru'] = $this->cbt->getDataGuru();
        $this->load->view('members/siswa/templates/header', $data);
        $today = strtotime(date('Y-m-d'));
        $this->load->model('Cbt_model', 'cbt');
        $cbt_info->no_peserta = $this->cbt->getNomorPeserta($siswa->id_siswa);
        $smt = $this->dashboard->getSemesterActive();
        $data['tp_active'] = $tp;
        $data['cbt_info'] = $cbt_info;
        $tp = $this->dashboard->getTahunActive();
        $cbt_info = $this->cbt->getSiswaCbtInfo($siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
    }
    public function konfirmasi($id_jadwal)
    {
        $data['guru'] = $guru;
        $curr_agent = $this->agent->mobile();
        $data['smt'] = $this->dashboard->getSemester();
        $info = $this->cbt->getJadwalById($id_jadwal);
        $pengawass = $this->cbt->getPengawas($tp->id_tp . $smt->id_smt . $id_jadwal . $cbt_info->id_ruang . $cbt_info->id_sesi);
        if ($this->agent->is_browser()) {
        }
        $curr_agent = 'unknown';
        $data['valid'] = $valid;
        $this->load->view('members/siswa/templates/header', $data);
        if (!$this->db->update('log_ujian')) {
        }
        $pengawas = [];
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $this->load->model('Master_model', 'master');
        if (!$valid) {
        }
        $bank = $this->cbt->getCbt($id_jadwal);
        $valid = true;
        $data['bank'] = $bank;
        $this->load->view('members/siswa/templates/footer');
        $this->db->set('agent', $curr_agent);
        if ($this->agent->is_mobile()) {
        }
        $user = $this->ion_auth->user()->row();
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->db->where('id_log', $siswa->id_siswa . '0' . $id_jadwal . '1');
        $data['pengawas'] = $pengawas;
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/siswa/cbt/konfirmasi');
        if ($log != null) {
        }
        $this->load->model('Cbt_model', 'cbt');
        $cbt_info = $this->cbt->getSiswaCbtInfo($siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $valid = $log->address == $curr_address && $log->agent == $curr_agent && $log->device == $curr_device;
        $curr_address = $this->input->ip_address();
        $data['support'] = $curr_agent != 'unknown';
        $this->db->set('device', $curr_device);
        $curr_device = $this->agent->platform();
        $this->db->set('reset', 0);
        if ($info->reset_login == '1') {
        }
        $tp = $this->dashboard->getTahunActive();
        if (!($pengawass != null && count(explode(',', $pengawass->id_guru ?? '')) > 0)) {
        }
        $log = $this->db->where('id_log', $siswa->id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        $this->db->set('address', $curr_address);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        if (!($log->reset == 1)) {
        }
        $pengawas = $this->master->getGuruByArrId(explode(',', $pengawass->id_guru ?? ''));
        $log = $this->db->where('id_log', $siswa->id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $curr_agent = $this->agent->browser() . ' ' . $this->agent->version();
        $valid = true;
        $guru = $this->cbt->getDataGuru();
    }
    public function validasiSiswa()
    {
        $data['jml_soal'] = $this->cbt->getJumlahSoalSiswa($id_bank, $id_siswa);
        $mulai_baru_d = true;
        if ($log->reset == '0') {
        }
        $dt = explode(':', $elapsed->lama_ujian ?? '');
        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        $mulai_baru = false;
        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        if ($this->db->update('log_ujian')) {
        }
        $mulai_baru_d = $elapsed->reset == '3';
        if (!(count($nomor_soal) > 0)) {
        }
        $curr_agent = 'unknown';
        $mulai = new DateTime($elapsed->mulai);
        $nomor_soal = $this->createQueueNumber($id_siswa, $id_bank, $id_jadwal);
        $this->db->set('mulai', date('Y-m-d H:i:s'));
        if ($elapsed == null) {
        }
        $insert = ['id_durasi' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'status' => 1, 'mulai' => date('Y-m-d H:i:s'), 'lama_ujian' => '00:00:00', 'reset' => 0];
        $data['elapsed'] = $this->cbt->getElapsed($id_siswa . '0' . $id_jadwal);
        $inserted = $this->cbt->saveLog($id_siswa, $id_jadwal, 1, 'Memulai Ujian');
        $data['ada_waktu'] = $ada_waktu;
        $ada_waktu = $minutes < $info->durasi_ujian;
        $data['warn'] = ['durasi_ujian' => $info->durasi_ujian, 'siswa_mulai' => $elapsed->mulai, 'durasi_siswa' => $elapsed->lama_ujian, 'timer_elapsed' => $minutes, 'terlampaui' => $minutes - $info->durasi_ujian, 'status' => $ada_waktu ? 0 : 1, 'msg' => $ada_waktu ? '' : 'Waktu ujian sudah habis'];
        $this->db->where('id_durasi', $id_siswa . '0' . $id_jadwal);
        $izinkan = true;
        $curr_address = $this->input->ip_address();
        $izinkan = true;
        $cek_reset_waktu = true;
        $id_jadwal = $this->input->post('jadwal');
        if (!($info->token == '1')) {
        }
        $curr_device = $this->agent->platform();
        $this->db->set('reset', 0);
        $mulai_baru_d = false;
        $cek_reset_waktu = false;
        $data['token_msg'] = $token_valid ? '' : 'Token salah';
        $token_valid = $token->token == $token_siswa ? true : false;
        $data['interval'] = ['days' => $interval->days, 'hari' => $interval->d, 'jam' => $interval->h, 'menit' => $interval->i, 'detik' => $interval->s, 'total' => $minutes];
        $token = $this->cbt->getToken();
        $this->db->set('device', $curr_device);
        if (!$support) {
        }
        if ($log->address == $curr_address && $log->agent == $curr_agent && $log->device == $curr_device) {
        }
        if ($log == null) {
        }
        $this->db->where('id_durasi', $id_siswa . '0' . $id_jadwal);
        $this->db->set('reset', 0);
        $data['log'] = $log;
        $this->db->delete('cbt_soal_siswa', array('id_jadwal' => $id_jadwal, 'id_siswa' => $id_siswa, 'id_bank' => $id_bank));
        $mulai_baru = false;
        $this->db->set('reset', 0);
        $token_valid = true;
        $data['update_reset'] = $this->db->update('cbt_durasi_siswa');
        $info = $this->cbt->getJadwalById($id_jadwal);
        $izinkan = false;
        $this->db->set('lama_ujian', '00:00:00');
        $this->db->trans_start();
        if (!$ada_waktu) {
        }
        if (!($mulai_baru && $mulai_baru_d)) {
        }
        if ($soal > 0) {
        }
        $ada_waktu = true;
        if (!(count($nomor_soal) > 0)) {
        }
        $id_siswa = $this->input->post('siswa');
        $time->sub(new DateInterval('PT' . $dt[0] . 'H' . $dt[1] . 'M' . $dt[2] . 'S'));
        if ($this->agent->is_mobile()) {
        }
        $this->db->set('reset', 0);
        $izinkan = false;
        $mulai_baru = true;
        $izinkan = true;
        $ada_waktu = true;
        $token_siswa = $this->input->post('token');
        if ($info->reset_login == '1') {
        }
        $this->db->set('mulai', $time->format('Y-m-d H:i:s'));
        $interval = $mulai->diff(new DateTime());
        $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1');
        $this->db->insert_batch('cbt_soal_siswa', $nomor_soal);
        $data['izinkan'] = $izinkan;
        $mulai_baru = false;
        $izinkan = true;
        if (!$token_valid) {
        }
        $izinkan = false;
        $ada_waktu = true;
        $mulai_baru = false;
        $curr_agent = $this->agent->mobile();
        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        $data['support'] = $support;
        $data['token'] = $token_valid;
        if ($token == null) {
        }
        $this->db->set('mulai', date('Y-m-d H:i:s'));
        $data['update_reset'] = $this->db->update('cbt_durasi_siswa');
        $data['token_msg'] = 'Token tidak ada';
        $curr_agent = $this->agent->browser() . ' ' . $this->agent->version();
        $time = new DateTime();
        $this->db->where('id_durasi', $id_siswa . '0' . $id_jadwal);
        $this->db->insert('cbt_durasi_siswa', $insert);
        $data['update_reset'] = $this->db->update('cbt_durasi_siswa');
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
        if ($elapsed->reset == '2') {
        }
        if ($elapsed->reset == '1') {
        }
        $mulai_baru = false;
        $soal = $this->cbt->getJumlahSoalSiswa($id_bank, $id_siswa);
        $token_valid = false;
        $this->db->trans_complete();
        $id_bank = $this->input->post('bank');
        $elapsed = $this->cbt->getElapsed($id_siswa . '0' . $id_jadwal);
        $this->db->set('agent', $curr_agent);
        if ($this->agent->is_browser()) {
        }
        $ada_waktu = false;
        $this->db->set('address', $curr_address);
        $support = $curr_agent != 'unknown';
        $nomor_soal = $this->createQueueNumber($id_siswa, $id_bank, $id_jadwal);
        $mulai_baru = false;
        if ($inserted) {
        }
        $this->db->insert_batch('cbt_soal_siswa', $nomor_soal);
        $this->db->set('lama_ujian', '00:00:00');
        if (!($izinkan || $cek_reset_waktu)) {
        }
        $this->output_json($data);
        if ($elapsed->reset == '3') {
        }
        $this->load->model('Cbt_model', 'cbt');
        $ada_waktu = true;
    }
    public function createQueueNumber($id_siswa, $id_bank, $id_jadwal)
    {
        $arrOpsi = ['A', 'B', 'C', 'D'];
        $ada1 = $num1 == (int) $jadwal->tampil_pg;
        return [];
        usort($items, function ($a, $b) {
            return $a['no_soal_alias'] <=> $b['no_soal_alias'];
        });
        $arrOpsi = ['A', 'B', 'C'];
        $this->load->model('Cbt_model', 'cbt');
        if ($opsis == '2') {
        }
        $total = $num1 + $num2 + $num3 + $num4 + $num5;
        $num4 = isset($cek_soal['4']) ? count($cek_soal['4']) : 0;
        $items = [];
        if ($ada1 && $ada2 && $ada3 && $ada4 && $ada5) {
        }
        $num2 = isset($cek_soal['2']) ? count($cek_soal['2']) : 0;
        $arrNum = range(1, $total);
        $jadwal = $this->cbt->getInfoJadwal($id_bank);
        $ada5 = $num5 == (int) $jadwal->tampil_esai;
        $opsis = $jadwal->opsi;
        $num3 = isset($cek_soal['3']) ? count($cek_soal['3']) : 0;
        $ada4 = $num4 == (int) $jadwal->tampil_isian;
        if ($opsis == '4') {
        }
        $cek_soal = $this->cbt->getAllIdSoal($id_bank);
        $ada2 = $num2 == (int) $jadwal->tampil_kompleks;
        $j = 0;
        if (!($jadwal->acak_soal == '1')) {
        }
        $num5 = isset($cek_soal['5']) ? count($cek_soal['5']) : 0;
        $arrOpsi = ['A', 'B', 'C', 'D', 'E'];
        foreach ($cek_soal as $jenis => $soals) {
            foreach ($soals as $soal) {
                if ($jenis == '2') {
                }
                $item_soal['opsi_alias_a'] = $arrOpsi[0];
                $item_soal['id_bank'] = $id_bank;
                $item_soal['jawaban_benar'] = $soal->jawaban;
                $item_soal['point_soal'] = $jadwal->bobot_jodohkan > 0 ? round($jadwal->bobot_jodohkan / $jadwal->tampil_jodohkan, 2) : 0;
                array_push($items, $item_soal);
                $item_soal['opsi_alias_c'] = '';
                $item_soal['opsi_alias_e'] = isset($arrOpsi[4]) ? $arrOpsi[4] : '';
                if ($jenis == '3') {
                }
                $item_soal['point_soal'] = $jadwal->bobot_kompleks > 0 ? round($jadwal->bobot_kompleks / $jadwal->tampil_kompleks, 2) : 0;
                $item_soal['point_soal'] = $jadwal->bobot_isian > 0 ? round($jadwal->bobot_isian / $jadwal->tampil_isian, 2) : 0;
                $item_soal['opsi_alias_d'] = '';
                $item_soal['opsi_alias_e'] = '';
                $item_soal['point_soal'] = $jadwal->bobot_pg > 0 ? round($jadwal->bobot_pg / $jadwal->tampil_pg, 2) : 0;
                $item_soal['point_soal'] = $jadwal->bobot_esai > 0 ? round($jadwal->bobot_esai / $jadwal->tampil_esai, 2) : 0;
                if (!($jenis == '1')) {
                }
                if ($jenis == '4') {
                }
                $item_soal['id_jadwal'] = $id_jadwal;
                $item_soal['opsi_alias_b'] = $arrOpsi[1];
                $item_soal['id_soal_siswa'] = $id_siswa . '0' . $id_jadwal . $id_bank . $arrNum[$j];
                $item_soal['id_siswa'] = $id_siswa;
                $item_soal['no_soal_alias'] = $arrNum[$j];
                if (!($jadwal->acak_opsi == '1')) {
                }
                $item_soal['opsi_alias_b'] = '';
                $j++;
                $item_soal['jenis_soal'] = $jenis;
                $item_soal['opsi_alias_a'] = 'A';
                $item_soal['soal_end'] = $j + 1 === count($arrNum) ? '1' : '0';
                if ($jenis == '1') {
                }
                $item_soal['opsi_alias_c'] = isset($arrOpsi[2]) ? $arrOpsi[2] : '';
                $item_soal['opsi_alias_d'] = isset($arrOpsi[3]) ? $arrOpsi[3] : '';
                shuffle($arrOpsi);
                if ($jenis == '5') {
                }
                $item_soal['id_soal'] = $soal->id_soal;
            }
        }
        $ada3 = $num3 == (int) $jadwal->tampil_jodohkan;
        return $items;
        $arrOpsi = ['A', 'B'];
        shuffle($arrNum);
        $num1 = isset($cek_soal['1']) ? count($cek_soal['1']) : 0;
        if ($opsis == '3') {
        }
    }
    public function penilaian($id_jadwal)
    {
        $mulai = new DateTime($durasi->mulai);
        $this->load->model('Dashboard_model', 'dashboard');
        $data['smt_active'] = $smt;
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $data['smt'] = $this->dashboard->getSemester();
        $this->load->view('members/siswa/templates/footer');
        $diff = $mulai->diff(new DateTime());
        $data['running_text'] = $this->dashboard->getRunningText();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->model('Cbt_model', 'cbt');
        $data['tp'] = $this->dashboard->getTahun();
        $user = $this->ion_auth->user()->row();
        if (!($durasi == null || $durasi->selesai != null)) {
        }
        $id_durasi = $siswa->id_siswa . '0' . $id_jadwal;
        $data['elapsed'] = $durasi;
        $tp = $this->dashboard->getTahunActive();
        redirect('siswa/cbt');
        $data['tp_active'] = $tp;
        $durasi = $this->cbt->getElapsed($id_durasi);
        $smt = $this->dashboard->getSemesterActive();
        $data['jadwal'] = $this->cbt->getCbt($id_jadwal);
        $this->load->view('members/siswa/cbt/ujian');
        $durasi->diff = ['days' => $diff->days, 'hari' => $diff->d, 'jam' => $diff->h, 'menit' => $diff->i, 'detik' => $diff->s, 'format' => $diff->format('%H:%I:%S')];
    }
    public function checkTimer($id_siswa, $id_jadwal)
    {
        if ($durasi != null) {
        }
        $this->load->model('Cbt_model', 'cbt');
        $this->db->update('cbt_durasi_siswa');
        $durasi = $this->cbt->getElapsed($id_durasi);
        $this->db->update('cbt_durasi_siswa');
        $this->db->set('lama_ujian', $elapsed);
        $diff = $mulai->diff(new DateTime());
        $durasi = $this->cbt->getElapsed($id_durasi);
        $this->db->update('cbt_durasi_siswa');
        return $durasi;
        $elapsed = $diff->format('%H:%I:%S');
        $durasi = $this->cbt->getElapsed($id_durasi);
        if ($durasi->reset == '1') {
        }
        $id_durasi = $id_siswa . '0' . $id_jadwal;
        $this->db->where('id_durasi', $id_durasi);
        $this->db->set('lama_ujian', $elapsed);
        if ($durasi->reset == '0') {
        }
        $durasi = false;
        $this->db->where('id_durasi', $id_durasi);
        $this->db->where('id_durasi', $id_durasi);
        $this->db->set('reset', 0);
        $this->db->set('lama_ujian', '00:00:00');
        $durasi = false;
        if ($durasi->reset == '3') {
        }
        $durasi = $this->cbt->getElapsed($id_durasi);
        $this->db->set('reset', 0);
        $mulai = new DateTime($durasi->mulai);
    }
    public function loadNomorSoal()
    {
        $nomor = $this->input->post('nomor');
        $arrJawaban = [];
        $id_soal_siswa = $siswa->id_siswa . '0' . $id_jadwal . $id_bank . $nomor;
        $ada_jawab = $soals[$s]->jawaban_siswa != null;
        $data['soal_opsi'] = json_decode(json_encode($opsis));
        $item_soal = $soals[$ind_soal];
        $data['durasi'] = $durasi;
        if (!($soals[$s]->jenis_soal == '3')) {
        }
        if (!$ada_jawab) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $ind_soal = array_search($id_soal_siswa, array_column($soals, 'id_soal_siswa'));
        $opsis = [];
        if (!($s < count($soals))) {
        }
        $opsis = [];
        $ada_jawab = $item_soal->jawaban_siswa != null;
        $data['max_jawaban'] = $max_jawaban;
        foreach ($jawaban_siswa->jawaban as $key => $jawaban) {
            $tbody[$key] = [];
            foreach ($jawaban as $index => $nbaris) {
                array_push($tbody[$key], $nbaris);
                if ($index === 0) {
                }
                array_push($tbody[$key], '');
            }
            array_push($tbody, $jawaban);
            if ($key === 0) {
            }
            if ($ada_jawab) {
            }
            $theader = $jawaban;
        }
        foreach ($item_soal->opsi_a as $key => $opsi) {
            $item = ['opsi' => $opsi, 'value' => $key, 'checked' => in_array(strtoupper($key ?? ''), $jwbSiswa) ? 'checked="true"' : ''];
            array_push($opsis, $item);
        }
        foreach ($soals as $key => $soal) {
            $terjawab = false;
            $color = !$terjawab ? 'outline-secondary' : 'primary';
            $terjawab = $soal->jawaban_siswa != '';
            array_push($arrJawaban, $soal->jawaban_alias);
            $txt_badge = $soal->jenis_soal == '1' ? $soal->jawaban_alias : '&check;';
            if ($soal->jenis_soal === '3') {
            }
            foreach ($soal->jawaban_siswa->jawaban as $keyi => $jwbn_siswa) {
                foreach ($jwbn_siswa as $keyj => $jwbn) {
                    $arrJawaban3[] = $jwbn;
                    if (!($keyj > 0)) {
                    }
                }
                if (!($keyi > 0)) {
                }
            }
            $modal .= '</div></div>';
            $terjawab = in_array('1', $arrJawaban3);
            if (!$terjawab) {
            }
            if ($soal->jawaban_siswa != null) {
            }
            $arrJawaban3 = [];
            $selected = $nomor == $soal->no_soal_alias ? 'active' : '';
            $terjawab = false;
            $modal .= '<div id="badge' . $soal->no_soal_alias . '" class="badge badge-pill badge-success border border-dark"' . ' style="font-size:12pt; width: 30px; height: 30px; margin-top: -60px; margin-left: 30px;">' . $txt_badge . '</div>';
            $modal .= '<div class="mb-4">' . '<div id="box' . $soal->no_soal_alias . '" class="d-flex flex-column" style="width: 70px; height: 60px;">' . '<button id="btn' . $soal->no_soal_alias . '" class="btn btn-' . $color . ' border border-dark ' . $selected . '" ' . 'data-pos="' . $key . '" data-nomorsoal="' . $soal->no_soal_alias . '" ' . 'data-idsoal="' . $soal->id_soal . '" data-jenis="' . $soal->jenis_soal . '" ' . 'onclick="loadSoal(this)" ' . 'style="width: 50px; height: 50px;">' . '<span style="font-size: 14pt"><b>' . $soal->no_soal_alias . '</b></span>' . '</button>';
            if (isset($soal->jawaban_siswa->jawaban)) {
            }
        }
        $max_jawaban = [];
        $data['soal_akhir'] = $modal;
        if (!isset($jawaban_siswa->jawaban)) {
        }
        $data['soal_total'] = count($soals);
        if ($item_soal->jenis_soal == '3') {
        }
        $id_jadwal = $this->input->post('jadwal');
        usort($opsis, function ($a, $b) {
            return $a['value'] <=> $b['value'];
        });
        $data['soal_modal'] = $modal;
        $theader = [];
        $data['soal_terjawab'] = count($arrJawaban);
        $tbody = [];
        $timer = $this->input->post('timer');
        $tp = $this->dashboard->getTahunActive();
        $data['soal_jawaban_siswa'] = $item_soal->jawaban_siswa;
        $id_siswa = $this->input->post('siswa');
        $s = 0;
        if (!isset($jwbs['jawaban'])) {
        }
        usort($opsis, function ($a, $b) {
            return $a['valAlias'] <=> $b['valAlias'];
        });
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($data);
        $smt = $this->dashboard->getSemesterActive();
        $max_jawaban = [count(array_filter(unserialize($item_soal->jawaban ?? '')))];
        $durasi = $this->checkTimer($id_siswa, $id_jadwal);
        $modal = '<div class="d-flex flex-wrap justify-content-center grid-nomor-pg">';
        $data['soal_nomor_asli'] = $item_soal->nomor_soal;
        $opsis = [['valAlias' => $item_soal->opsi_alias_a, 'opsi' => $item_soal->opsi_a, 'value' => 'A', 'checked' => 'A' === $jwbSiswa ? 'checked' : ''], ['valAlias' => $item_soal->opsi_alias_b, 'opsi' => $item_soal->opsi_b, 'value' => 'B', 'checked' => 'B' === $jwbSiswa ? 'checked' : ''], ['valAlias' => $item_soal->opsi_alias_c, 'opsi' => $item_soal->opsi_c, 'value' => 'C', 'checked' => 'C' === $jwbSiswa ? 'checked' : ''], ['valAlias' => $item_soal->opsi_alias_d, 'opsi' => $item_soal->opsi_d, 'value' => 'D', 'checked' => 'D' === $jwbSiswa ? 'checked' : ''], ['valAlias' => $item_soal->opsi_alias_e, 'opsi' => $item_soal->opsi_e, 'value' => 'E', 'checked' => 'E' === $jwbSiswa ? 'checked' : '']];
        $jwbSiswa = $item_soal->jawaban_siswa != null ? strtoupper($item_soal->jawaban_siswa ?? '') : '';
        if ($item_soal->jenis_soal == '2') {
        }
        $soals[$s]->jawaban = unserialize($soals[$s]->jawaban ?? '');
        $s++;
        $data['soal_jenis'] = $item_soal->jenis_soal;
        $data['timer'] = $timer;
        $id_bank = $this->input->post('bank');
        $jwbSiswa = $item_soal->jawaban_siswa != null ? $item_soal->jawaban_siswa : [];
        foreach ($jwbs['jawaban'] as $jwb) {
            $i++;
            $i = 1;
            $max_jawaban[$jwb[0]] += 1;
            if (!($jwb[$i] == '1')) {
            }
            $max_jawaban[$jwb[0]] = 0;
            if (!($i < count($jwb))) {
            }
        }
        $jwbs = $item_soal->jawaban;
        $item_soal->opsi_a = unserialize($item_soal->opsi_a ?? '');
        $soals[$s]->jawaban_siswa = unserialize($soals[$s]->jawaban_siswa ?? '');
        if ($item_soal->jenis_soal == '1') {
        }
        $data['soal_soal'] = $item_soal->soal;
        $data['soal_nomor'] = $item_soal->no_soal_alias;
        $soals = $this->cbt->getALLSoalSiswa($id_bank, $siswa->id_siswa);
        $data['soal_siswa_id'] = $item_soal->id_soal_siswa;
        $jawaban_siswa = $ada_jawab ? $item_soal->jawaban_siswa : json_decode(json_encode($item_soal->jawaban));
        $item_soal->jawaban_siswa = unserialize($item_soal->jawaban_siswa ?? '');
        $siswa = $this->cbt->getDataSiswaById($tp->id_tp, $smt->id_smt, $id_siswa);
        $data['soal_id'] = $item_soal->id_soal;
        $modal .= '</div>';
        $opsis = ['tabel' => isset($jwbs['jawaban']) ? $jwbs['jawaban'] : [], 'thead' => $theader, 'tbody' => $tbody, 'model' => isset($item_soal->jawaban['model']) ? $item_soal->jawaban['model'] : '2', 'type' => $item_soal->jawaban['type']];
    }
    public function saveSoalSiswa()
    {
        $id_siswa = $shuffle[0]->id_siswa;
        $this->output_json($data);
        foreach ($shuffle as $s) {
            $jml = $this->db->get('cbt_soal_siswa')->num_rows();
            $id_bank = $s->id_bank;
            $jenis = $s->jenis;
            $id_soal = $soal->id_soal;
            $soal = $this->cbt->getSoalByNomor($id_bank, $nomor, $jenis);
            $this->master->create('cbt_soal_siswa', $insert, false);
            $id_jadwal = $s->id_jadwal;
            $id_siswa = $s->id_siswa;
            $this->db->where('id_soal_siswa', $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor);
            $insert = ['id_soal_siswa' => $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor, 'id_bank' => $id_bank, 'id_jadwal' => $id_jadwal, 'id_soal' => $id_soal, 'id_siswa' => $id_siswa, 'jenis_soal' => $jenis, 'no_soal_alias' => $s->no_soal_alias, 'opsi_alias_a' => isset($s->opsi_alias_a) ? $s->opsi_alias_a : null, 'opsi_alias_b' => isset($s->opsi_alias_b) ? $s->opsi_alias_b : null, 'opsi_alias_c' => isset($s->opsi_alias_c) ? $s->opsi_alias_c : null, 'opsi_alias_d' => isset($s->opsi_alias_d) ? $s->opsi_alias_d : null, 'opsi_alias_e' => isset($s->opsi_alias_e) ? $s->opsi_alias_e : null, 'jawaban_benar' => $soal->jawaban, 'soal_end' => $s->soal_end];
            if ($jml > 0) {
            }
            $insert = ['id_bank' => $id_bank, 'id_jadwal' => $id_jadwal, 'id_soal' => $id_soal, 'id_siswa' => $id_siswa, 'jenis_soal' => $jenis, 'no_soal_alias' => $s->no_soal_alias, 'opsi_alias_a' => isset($s->opsi_alias_a) ? $s->opsi_alias_a : null, 'opsi_alias_b' => isset($s->opsi_alias_b) ? $s->opsi_alias_b : null, 'opsi_alias_c' => isset($s->opsi_alias_c) ? $s->opsi_alias_c : null, 'opsi_alias_d' => isset($s->opsi_alias_d) ? $s->opsi_alias_d : null, 'opsi_alias_e' => isset($s->opsi_alias_e) ? $s->opsi_alias_e : null, 'jawaban_benar' => $soal->jawaban, 'soal_end' => $s->soal_end];
            $nomor = $s->nomor_soal;
            $this->master->update('cbt_soal_siswa', $insert, 'id_soal_siswa', $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor);
        }
        $this->load->model('Master_model', 'master');
        $shuffle = json_decode($this->input->post('shuffle', false));
        $this->load->model('Cbt_model', 'cbt');
        $id_bank = $shuffle[0]->id_bank;
        $data['soals'] = $this->cbt->getSoalSiswa($id_bank, $id_siswa);
    }
    public function saveLogUjian($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->saveLog($id_siswa, $id_jadwal, 1, 'Memulai Ujian'));
    }
    public function saveJawaban()
    {
        if ($jawab['jenis'] == 3) {
        }
        $jawab_isian = $this->input->post('jawaban', false);
        $timer = $this->input->post('waktu', true);
        $id_siswa = $this->input->post('siswa', true);
        $this->db->set('jawaban_siswa', $jawab['jawaban_siswa']);
        $this->db->set('jawaban_siswa', $jawab_isian);
        $update = $this->db->update('cbt_soal_siswa');
        if (!($elapsed != '0')) {
        }
        foreach ($terjawab as $jawab) {
            if (!($jawab->jawaban_siswa != null && $jawab->jawaban_siswa != '')) {
            }
            array_push($arrJawaban, $jawab);
        }
        $this->load->model('Cbt_model', 'cbt');
        $this->db->set('jawaban_siswa', serialize(json_decode($jawab['jawaban_siswa'])));
        $elapsed = $this->input->post('elapsed', true);
        $this->output_json($data);
        $id_jadwal = $this->input->post('jadwal', true);
        $this->db->set('lama_ujian', $elapsed);
        $data['soal_terjawab'] = count($arrJawaban);
        if (!($update && $timer != null)) {
        }
        if ($jawab['jenis'] == 2) {
        }
        $this->db->set('jawaban_siswa', $jawab['jawaban_siswa']);
        if (!($jawab != null && isset($jawab['jenis']))) {
        }
        $terjawab = $this->cbt->getJumlahJawaban($id_bank, $id_siswa);
        $data['status'] = $update;
        if (!($update && $id_bank != null)) {
        }
        $update = true;
        $jawab = $this->input->post('data', false);
        $this->db->where('id_durasi', $id_durasi);
        $this->db->set('jawaban_alias', '');
        $arrJawaban = [];
        $this->db->update('cbt_durasi_siswa');
        $this->db->set('jawaban_alias', $jawab['jawaban_alias']);
        $this->selesaiUjian();
        $id_durasi = $id_siswa . '0' . $id_jadwal;
        if ($jawab['jenis'] == 1) {
        }
        $this->db->set('jawaban_alias', '');
        $this->db->set('jawaban_siswa', serialize(json_decode($jawab['jawaban_siswa'])));
        $this->db->set('jawaban_alias', '');
        $this->db->where('id_soal_siswa', $jawab['id_soal_siswa']);
        $this->db->set('jawaban_alias', '');
        $id_bank = $this->input->post('bank', true);
        if ($jawab['jenis'] == 4) {
        }
    }
    public function selesaiUjian()
    {
        $data['status_nilai'] = $this->olahNilai($id_siswa, $id_jadwal);
        $this->db->set('selesai', date('Y-m-d H:i:s'));
        $id_jadwal = $this->input->post('jadwal');
        $this->db->set('status', 2);
        $update = $this->db->update('cbt_durasi_siswa');
        $this->cbt->saveLog($id_siswa, $id_jadwal, 2, 'Menyelesaikan Ujian');
        $this->db->where('id_durasi', $id_siswa . '0' . $id_jadwal);
        $this->load->model('Cbt_model', 'cbt');
        $data['status'] = $update;
        $this->output_json($data);
        $id_siswa = $this->input->post('siswa');
    }
    public function resetTimer()
    {
        $update = $this->db->update('cbt_durasi_siswa');
        $this->db->where('id_durasi', $id_durasi);
        $id_durasi = $this->input->post('id_durasi', true);
        $this->db->set('reset', $reset);
        $data['status'] = $update;
        $this->db->set('lama_ujian', '00:00:00');
        $reset = $this->input->post('reset', true);
        $this->output_json($data);
        if (!($reset == '1')) {
        }
    }
    public function ulangiUjian($id_durasi, $id_bank)
    {
        $this->load->model('Cbt_model', 'cbt');
        $i = 0;
        $soals = $this->cbt->getAllSoalByBank($id_bank);
        if ($this->master->delete('cbt_durasi_siswa', $id_durasi, 'id_durasi')) {
        }
        $data['status'] = true;
        $i++;
        $data['status'] = false;
        $this->output_json($data);
        $this->load->model('Master_model', 'master');
        if (!($i < 2)) {
        }
        foreach ($soals as $soal) {
            $this->db->where('id_soal_siswa', $id_durasi . $id_bank . ($i + 1) . $soal->nomor_soal);
            $this->db->delete('cbt_soal_siswa');
        }
    }
    public function applyAction()
    {
        $data['update_selesai'] = $this->db->update('cbt_durasi_siswa');
        $this->db->trans_complete();
        if (!(count($json->ulang) > 0)) {
        }
        $this->db->set('reset', 3);
        $data['update_ulangi'] = $this->db->delete('cbt_soal_siswa');
        $this->db->where_in('id_siswa', $json->ulang);
        $data['reset'] = true;
        $data['update_reset'] = true;
        $this->db->set('reset', 1);
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->trans_start();
        $this->output_json($data);
        foreach ($json->log as $ids) {
            $this->cbt->saveLog($ids, $id_jadwal, 2, 'Menyelesaikan Ujian');
            $data['status_nilai'] = $this->olahNilai($ids, $id_jadwal);
        }
        $data['update_selesai'] = true;
        $this->db->set('selesai', date('Y-m-d H:i:s'));
        if (!(count($json->reset) > 0)) {
        }
        $data['ulangi'] = true;
        $id_jadwal = $this->input->post('jadwal', true);
        if (!$this->db->delete('log_ujian')) {
        }
        $this->db->where_in('id_siswa', $json->ulang);
        $this->db->where_in('id_durasi', $json->hapus);
        $data['selesai'] = true;
        $data['update_ulangi'] = true;
        if (!$this->db->delete('cbt_nilai')) {
        }
        $json = json_decode($this->input->post('aksi', true));
        $this->db->update('log_ujian');
        $this->db->where('id_jadwal', $id_jadwal);
        if (!$this->db->delete('cbt_durasi_siswa')) {
        }
        $this->db->where_in('id_log', $json->reset);
        $this->db->where_in('id_durasi', $json->force);
        $this->load->model('Cbt_model', 'cbt');
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->where_in('id_siswa', $json->ulang);
        $this->db->set('status', 2);
        if (!(count($json->force) > 0)) {
        }
    }
    public function olahNilai($id_siswa, $id_jadwal)
    {
        $jawaban_jodoh = isset($jawabans_siswa['3']) ? $jawabans_siswa['3'] : [];
        $bagi_isian = $info->tampil_isian / 100;
        $otomatis_pg2 = 0;
        $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
        $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa['5'] : [];
        foreach ($jawaban_pg2 as $num => $jawab_pg2) {
            $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
            $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
            $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
            foreach ($jawab_pg2->jawaban_siswa as $js) {
                array_push($arr_benar, true);
                if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                }
            }
            if (!is_array($jawab_pg2->jawaban_siswa)) {
            }
            if (!(count($jawab_pg2->jawaban_benar) > 0)) {
            }
            $arr_benar = [];
        }
        $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa['4'] : [];
        $skor_koreksi_pg2 = 0.0;
        $skor_jod = $otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $jawaban_pg2 = isset($jawabans_siswa['2']) ? $jawabans_siswa['2'] : [];
        if (!($info->tampil_esai > 0)) {
        }
        foreach ($jawaban_es as $num => $jawab_es) {
            $otomatis_es = $jawab_es->nilai_otomatis;
            if (!$benar) {
            }
            $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban_benar ?? '');
            $benar_es++;
            $skor_koreksi_es += $jawab_es->nilai_koreksi;
        }
        if (!(count($jawaban_pg2) > 0)) {
        }
        $bobot_isian = $info->bobot_isian / 100;
        if (!($info->tampil_kompleks > 0)) {
        }
        $skor_koreksi_is = 0.0;
        return $this->db->replace('cbt_nilai', $insert);
        $jawaban_pg = isset($jawabans_siswa['1']) ? $jawabans_siswa['1'] : [];
        $otomatis_es = 0;
        $benar_jod = 0;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        if (!($info->tampil_pg > 0)) {
        }
        $jawabans_siswa = [];
        $bobot_essai = $info->bobot_esai / 100;
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_pg = $info->bobot_pg / 100;
        $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
        foreach ($jawaban_jodoh as $num => $jawab_jod) {
            $arrBenar = [];
            $item_salah = 0;
            if (!isset($jawab_jod->jawaban_siswa->links)) {
            }
            $this->sortArrays($array1);
            $items = 0;
            $array1 = (array) $jawab_jod->jawaban_benar->links;
            $this->sortArrays($array2);
            foreach ($array1 as $key => $subArray1) {
                $arrBenar[$key]->kurang += count($subArray1);
                $arrBenar[$key]->kurang = 0;
                $subArray2 = $array2[$key];
                if (isset($array2[$key])) {
                }
                $diffItems1 = array_diff($subArray1, $subArray2);
                $arrBenar[$key]->kurang += count($diffItems1);
                $diffItems2 = array_diff($subArray2, $subArray1);
                $arrBenar[$key]->benar = 0;
                $arrBenar[$key]->benar += count($sameItems);
                $items += count($subArray1);
                $item_benar += count($sameItems);
                $arrBenar[$key]->salah = 0;
                $arrBenar[$key] = new stdClass();
                $sameItems = array_intersect($subArray1, $subArray2);
            }
            $otomatis_jod = $jawab_jod->nilai_otomatis;
            $benar_jod += 1 / $items * $item_benar;
            $point_soal = 1 / $items * $item_benar * $point_benar;
            $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
            $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
            $differentCount = 0;
            $item_kurang = 0;
            $sameCount = 0;
            $item_benar = 0;
            $array2 = (array) $jawab_jod->jawaban_siswa->links;
        }
        $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;
        if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
        }
        if (!($info->tampil_isian > 0)) {
        }
        $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
        $otomatis_is = 0;
        $info = $this->cbt->getJadwalById($id_jadwal);
        $benar_pg2 = 0;
        foreach ($jawabans as $jawaban_siswa) {
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            if (!($jawaban_siswa->jenis_soal == '2')) {
            }
            $jawabans_siswa[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $arrjwbn = [];
            $jawaban_siswa->jawaban_siswa->links = json_decode(json_encode($arrjwbnSiswa));
            $arrjwbnSiswa = [];
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            if (!($jawaban_siswa->jenis_soal == '3')) {
            }
            $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $arrAlphabet = range('A', 'Z');
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                }
                $arrjwbnSiswa[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                }
            }
            $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
            $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar);
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                $arrjwbn[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                }
                if (!($idx > 0)) {
                }
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
        }
        $this->load->model('Cbt_model', 'cbt');
        $skor_koreksi_es = 0.0;
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank, $id_siswa);
        $benar_pg = 0;
        $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;
        $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
        if (!(count($jawaban_pg) > 0)) {
        }
        $benar_is = 0;
        $salah_pg = 0;
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $otomatis_jod = 0;
        $ada_jawaban_essai = isset($jawabans_siswa['5']);
        $insert = ['id_nilai' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'pg_benar' => $benar_pg, 'pg_nilai' => round($skor_pg, 2), 'kompleks_nilai' => round($skor_pg2, 2), 'jodohkan_nilai' => round($skor_jod, 2), 'isian_nilai' => round($skor_is, 2), 'essai_nilai' => round($skor_es, 2)];
        $ada_jawaban_isian = isset($jawabans_siswa['4']);
        $benar_es = 0;
        if (!(count($jawaban_is) > 0)) {
        }
        foreach ($jawaban_is as $num => $jawab_is) {
            if (!$benar) {
            }
            $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban_benar ?? '');
            $skor_koreksi_is += $jawab_is->nilai_koreksi;
            $benar_is++;
            $otomatis_is = $jawab_is->nilai_otomatis;
        }
        $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
        foreach ($jawaban_pg as $jwb_pg) {
            if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban_benar ?? '')) {
            }
            $salah_pg += 1;
            if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
            }
            $benar_pg += 1;
        }
        $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
        if (!(count($jawaban_es) > 0)) {
        }
        $skor_koreksi_jod = 0.0;
        $bagi_pg = $info->tampil_pg / 100;
    }
    public function hasil()
    {
        $logs = $this->kelas->getNilaiMateriSiswa($siswa->id_siswa);
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data['tp'] = $this->dashboard->getTahun();
        $data['jawaban'] = $jawabans;
        $data['durasi'] = $durasies;
        $data['jadwal'] = $jadwals;
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/siswa/nilai/data');
        $data['nilai_tugas'] = isset($logs[2]) ? $logs[2] : [];
        $jawabans = [];
        $user = $this->ion_auth->user()->row();
        $this->load->view('members/siswa/templates/footer');
        $kelass_unset = [];
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['skor'] = $skors;
        $this->load->model('Kelas_model', 'kelas');
        $this->db->trans_complete();
        $this->load->model('Cbt_model', 'cbt');
        $this->load->view('members/siswa/templates/header', $data);
        $this->db->trans_start();
        $data['nilai_materi'] = isset($logs[1]) ? $logs[1] : [];
        $data['running_text'] = $this->dashboard->getRunningText();
        $durasies = [];
        $skors = [];
        $data['kelass'] = $kelass_unset;
        $jadwals = $this->cbt->getJadwalByKelas($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $tp = $this->dashboard->getTahunActive();
        foreach ($jadwals as $kj => $jadwal) {
            $benar_is = 0;
            $bobot_essai = $info->bobot_esai / 100;
            if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
            }
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            $benar_pg2 = 0;
            $skor_koreksi_is = 0.0;
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
                $item_kurang = 0;
                $item_salah = 0;
                $array1 = (array) $jawab_jod->jawaban_benar->links;
                $this->sortArrays($array1);
                $arrBenar = [];
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $benar_jod += 1 / $items * $item_benar;
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $point_soal = 1 / $items * $item_benar * $point_benar;
                $items = 0;
                $this->sortArrays($array2);
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                }
                $sameCount = 0;
                foreach ($array1 as $key => $subArray1) {
                    $diffItems2 = array_diff($subArray2, $subArray1);
                    $subArray2 = $array2[$key];
                    $items += count($subArray1);
                    $sameItems = array_intersect($subArray1, $subArray2);
                    if (isset($array2[$key])) {
                    }
                    $arrBenar[$key]->kurang += count($diffItems1);
                    $arrBenar[$key]->salah = 0;
                    $arrBenar[$key]->benar += count($sameItems);
                    $arrBenar[$key]->benar = 0;
                    $arrBenar[$key]->kurang += count($subArray1);
                    $item_benar += count($sameItems);
                    $arrBenar[$key]->kurang = 0;
                    $diffItems1 = array_diff($subArray1, $subArray2);
                    $arrBenar[$key] = new stdClass();
                }
                $differentCount = 0;
                $item_benar = 0;
            }
            if (!(count($jawaban_is) > 0)) {
            }
            $skor_koreksi_es = 0.0;
            $skor->skor_essai = round($skor_es, 2);
            $input_pg2 = 0;
            $input_jod = 0;
            $skor->skor_total = round($total, 2);
            $benar_pg = 0;
            $bobot_isian = $info->bobot_isian / 100;
            if (!($nilai_input != null)) {
            }
            $skor->skor_pg = $skor_pg = $bagi_pg == 0 ? 0 : round($benar_pg / $bagi_pg * $bobot_pg, 2);
            $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            $bobot_jodoh = $info->bobot_jodohkan / 100;
            if (!($info->tampil_pg > 0)) {
            }
            $benar_es = 0;
            $bagi_essai = $info->tampil_esai / 100;
            $skor->benar_kompleks = round($benar_pg2, 2);
            $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
            $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
            $skor->benar_isian = $benar_is;
            if (!($info->tampil_esai > 0)) {
            }
            $input_es = $nilai_input->essai_nilai;
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
            }
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
            $kelass = unserialize($jadwal->bank_kelas ?? '');
            $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
            $bagi_isian = $info->tampil_isian / 100;
            $otomatis_es = 0;
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $jawabans_siswa = [];
            $bobot_pg = $info->bobot_pg / 100;
            $arr_kls_jadwal = [];
            if (!in_array($siswa->id_kelas, $arr_kls_jadwal)) {
            }
            unset($jadwals[$kj]);
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            $bagi_pg = $info->tampil_pg / 100;
            foreach ($kelass as $kll) {
                foreach ($kll as $kl) {
                    if (!($kl != null)) {
                    }
                    $arr_kls_jadwal[] = $kl;
                }
            }
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            $skor->skor_isian = round($skor_is, 2);
            $otomatis_jod = 0;
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            $salah_pg = 0;
            foreach ($jawabans as $jawaban_siswa) {
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
                }
                $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
                foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    }
                    foreach ($jbs as $idxs => $jb) {
                        $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                        if (!($jb === '1')) {
                        }
                        if (!($idxs > 0)) {
                        }
                    }
                    $arrjwbn[$idx] = [];
                }
                $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar);
                $arrjwbnSiswa = [];
                foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    }
                    foreach ($jbs as $idxs => $jb) {
                        $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                        if (!($idxs > 0)) {
                        }
                        if (!($jb === '1')) {
                        }
                    }
                    $arrjwbnSiswa[$idx] = [];
                }
                $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                if (!($jawaban_siswa->jenis_soal == '2')) {
                }
                $arrjwbn = [];
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_siswa->links = json_decode(json_encode($arrjwbnSiswa));
                $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban, 'strlen');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
                $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
                $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
                $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                if (!$jawaban_siswa->jawaban_siswa) {
                }
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban);
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
                $arrAlphabet = range('A', 'Z');
                if ($jawaban_siswa->jawaban_siswa) {
                }
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            }
            $skor->benar_pg = $benar_pg;
            $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            $input_jod = $nilai_input->jodohkan_nilai;
            $bobot_pg2 = $info->bobot_kompleks / 100;
            $skor->dikoreksi = $nilai_input->dikoreksi;
            $bagi_pg2 = $info->tampil_kompleks / 100;
            if (!($info->tampil_isian > 0)) {
            }
            $skor->skor_jodohkan = round($skor_jod, 2);
            $skor_koreksi_jod = 0.0;
            $skors[$jadwal->id_jadwal] = $skor;
            $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
            foreach ($jawaban_is as $num => $jawab_is) {
                $benar_is++;
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $otomatis_is = $jawab_is->nilai_otomatis;
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
                if (!$benar) {
                }
            }
            $input_is = $nilai_input->isian_nilai;
            $input_pg2 = $nilai_input->kompleks_nilai;
            $skor = new stdClass();
            $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
            $skor_koreksi_pg2 = 0.0;
            $input_es = 0;
            if (!(count($jawaban_pg2) > 0)) {
            }
            $otomatis_pg2 = 0;
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
            $skor->skor_kompleks = round($skor_pg2, 2);
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $benar_jod = 0;
            $jawabans = $this->cbt->getJawabanSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
            $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            foreach ($jawaban_pg as $num => $jwb_pg) {
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                }
                $benar_pg += 1;
                $salah_pg += 1;
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                $benar = false;
                $benar = true;
                $benar = false;
            }
            $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
            $otomatis_is = 0;
            foreach ($jawaban_es as $num => $jawab_es) {
                $otomatis_es = $jawab_es->nilai_otomatis;
                if (!$benar) {
                }
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $benar_es++;
            }
            $kelass_unset[] = $kj;
            $skor->benar_esai = $benar_es;
            $info = $jadwal;
            if (!(count($jawaban_pg) > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
                if (!(count($jawab_pg2->jawaban) > 0)) {
                }
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    array_push($arr_benar, true);
                    if (!in_array($js, $jawab_pg2->jawaban)) {
                    }
                }
                if (!$jawab_pg2->jawaban_siswa) {
                }
                $arr_benar = [];
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $jml_benar = count($arr_benar);
            }
            $bagi_jodoh = $info->tampil_jodohkan / 100;
            if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
            }
            if (!(count($jawaban_es) > 0)) {
            }
            $input_is = 0;
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $skor->benar_jodohkan = round($benar_jod, 2);
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
            $durasies[$jadwal->id_jadwal] = $this->cbt->getDurasiSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
        }
        $smt = $this->dashboard->getSemesterActive();
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Nilai', 'subjudul' => 'Nilai Hasil Belajar', 'setting' => $this->dashboard->getSetting()];
        $data['smt_active'] = $smt;
    }
    public function catatan()
    {
        $user = $this->ion_auth->user()->row();
        rsort($catatan);
        $this->load->model('Cbt_model', 'cbt');
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt_active'] = $smt;
        $data['tp_active'] = $tp;
        $tp = $this->dashboard->getTahunActive();
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Catatan', 'subjudul' => 'Catatan Dari Guru', 'setting' => $this->dashboard->getSetting()];
        foreach ($catatan_siswa as $cat) {
            if (!($cat->type === '2' && $cat->id_siswa === $siswa->id_siswa || $cat->type === '1' && $cat->id_kelas === $siswa->id_kelas)) {
            }
            $catatan[] = ['id_catatan' => $cat->id_catatan, 'nama_guru' => $cat->nama_guru, 'foto_guru' => $cat->foto && file_exists($cat->foto) ? $cat->foto : 'uploads/profiles/' . $cat->nip . (file_exists('uploads/profiles/' . $cat->nip . '.jpg') ? '.jpg' : '.png'), 'id_siswa' => $siswa->id_siswa, 'tgl' => $cat->tgl, 'table' => 'wali', 'level' => $cat->level, 'readed' => $cat->readed, 'type' => $cat->type, 'reading' => unserialize($cat->reading ?? '')];
        }
        $smt = $this->dashboard->getSemesterActive();
        $catatan_siswa = $this->kelas->getCatatanSiswaBySiswa($siswa->id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->model('Kelas_model', 'kelas');
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $catatan_mapel = $this->kelas->getCatatanMapelBySiswa($siswa->id_kelas, $tp->id_tp, $smt->id_smt);
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->view('members/siswa/templates/footer');
        $this->load->view('members/siswa/templates/header', $data);
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->load->view('members/siswa/catatan/data');
        foreach ($catatan_mapel as $cat) {
            if (!($cat->type === '2' && $cat->id_siswa === $siswa->id_siswa || $cat->type === '1' && $cat->id_kelas === $siswa->id_kelas)) {
            }
            $catatan[] = ['id_catatan' => $cat->id_catatan, 'nama_guru' => $cat->nama_guru, 'foto_guru' => $cat->foto && file_exists($cat->foto) ? $cat->foto : 'uploads/profiles/' . $cat->nip . (file_exists('uploads/profiles/' . $cat->nip . '.jpg') ? '.jpg' : '.png'), 'id_siswa' => $siswa->id_siswa, 'tgl' => $cat->tgl, 'table' => 'mapel', 'level' => $cat->level, 'type' => $cat->type, 'readed' => $cat->readed, 'reading' => unserialize($cat->reading ?? '')];
        }
        $catatan = [];
        $data['catatan'] = (array) json_decode(json_encode($catatan));
    }
    public function detailCatatan($table, $id_catatan)
    {
        if ($siswa && $table == 'mapel') {
        }
        $user = $this->ion_auth->user()->row();
        $detail = $this->kelas->getCatatanKelasSiswaDetail($id_catatan);
        $this->load->model('Dashboard_model', 'dashboard');
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        $reading = [];
        $smt = $this->dashboard->getSemesterActive();
        if (!$detail) {
        }
        $detail = $this->kelas->getCatatanMapelSiswaDetail($id_catatan);
        $this->output_json(['reading' => $reading, 'detail' => $detail]);
        $reading = $detail->reading != null ? unserialize($detail->reading ?? '') : [];
        $this->load->model('Kelas_model', 'kelas');
        $detail->id_siswa = $siswa->id_siswa;
        $this->load->model('Cbt_model', 'cbt');
    }
    public function readed($table, $id_catatan)
    {
        if (in_array($siswa->id_siswa, $reading)) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $tbl = 'kelas_catatan_mapel';
        $reading = unserialize($cat->reading ?? '');
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $this->db->set('readed', $readed);
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($update);
        $tbl = 'kelas_catatan_wali';
        $this->db->where('id_catatan', $id_catatan);
        $cat = $this->kelas->getReading($tbl, $id_catatan);
        $update = $this->db->update($tbl);
        $tp = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        array_push($reading, $siswa->id_siswa);
        $this->load->model('Cbt_model', 'cbt');
        if ($table == 'mapel') {
        }
        if ($cat->type == '1') {
        }
        $readed = $cat->readed == '0' ? date('Y-m-d H:i:s') : '0';
        $this->db->set('reading', serialize($reading));
        $this->load->model('Kelas_model', 'kelas');
    }
    public function getTimer($id_siswa, $id_jadwal)
    {
        $data['durasi'] = $this->cbt->getDurasiSiswa($id_siswa . '0' . $id_jadwal);
        $this->output_json($data);
        $this->load->model('Cbt_model', 'cbt');
    }
    function total_hari($id_day, $bulan, $taun)
    {
        array_push($dates, date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i)));
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $days = 0;
        $i = 1;
        if (!(date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday)) {
        }
        if (!($i < $total_days)) {
        }
        $dates = [];
        $idday = $id_day == '7' ? 0 : $id_day;
        $i++;
        return $dates;
        $days++;
    }
}
```

---

## File: application/controllers_decoded/Update.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Update extends CI_Controller
{
    function __construct()
    {
        $this->load->database();
        $this->load->library('encryption');
        $this->load->dbforge();
        parent::__construct();
        include APPPATH . 'config/database.php';
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $this->load->view('install/update');
        $json = (array) $json;
        $this->load->view('install/footer');
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $this->load->view('install/header', $data);
        $data['json'] = $json;
    }
    function object_to_array($data)
    {
        return $result;
        $result = [];
        if (!(is_array($data) || is_object($data))) {
        }
        foreach ($data as $key => $value) {
            $result[$key] = is_array($data) || is_object($data) ? $this->object_to_array($value) : $value;
        }
        return $data;
    }
    public function checkDatabase()
    {
        $json = file_get_contents('./assets/app/db/database.json');
        $data = ['db' => $fields, 'create' => $create_tables, 'modify' => $edit_columns, 'add' => $add_columns, 'counts' => $counts, 'json' => $json, 'current' => $currentDb];
        $full_tables = array_unique($full_tables);
        foreach ($tabless as $table) {
            $datafld[$i]->extra = $query[$i]->extra;
            $fields[$table] = $datafld;
            if (!($query[$i]->extra != '')) {
            }
            $sql = 'SELECT `column_name`, `numeric_precision`, `extra`, `is_nullable`' . ' FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            $i = 0;
            if (!($datafld[$i]->name == $query[$i]->column_name)) {
            }
            $currentDb = FALSE;
            $query = $query->result_object();
            $retval[$i]->extra = $query[$i]->extra;
            $i++;
            if ($query[$i]->extra == 'auto_increment') {
            }
            $c = count($query);
            $retval[$i] = new stdClass();
            $datafld = $this->db->field_data($table);
            $datafld[$i]->auto_increment = true;
            $retval = array();
            if (!($i < $c)) {
            }
            $currentDb[$table] = $retval;
            if (!(($query = $this->db->query($sql)) === FALSE)) {
            }
            $retval[$i]->name = $query[$i]->column_name;
        }
        $fields = [];
        $tabless = $this->db->list_tables();
        $json = (array) $json;
        $tbl_ada = array_keys($fields);
        $create_tables = [];
        $full_tables = array_merge($tbl_baru, $tbl_ada);
        $this->output_json($data);
        foreach ($full_tables as $table) {
            if (!isset($json[$table])) {
            }
            if ($this->db->table_exists($table)) {
            }
            foreach ($json[$table] as $jtbl) {
                foreach ($fields[$table] as $ftbl) {
                    if (!($jtbl->name == $ftbl->name)) {
                    }
                    $edit_columns[$table][] = $jtbl;
                    if (!($jtbl->default != $ftbl->default || $jtbl->max_length != $ftbl->max_length || $jtbl->type != $ftbl->type)) {
                    }
                }
                if ($this->db->field_exists($jtbl->name, $table)) {
                }
                $add_columns[$table][] = $jtbl;
            }
            $create_tables[$table] = $json[$table];
        }
        $json = json_decode($json);
        $currentDb = [];
        $this->db->db_debug = FALSE;
        $tbl_baru = array_keys($json);
        $counts = count($create_tables) + count($add_columns) + count($edit_columns);
        sort($full_tables);
        $add_columns = [];
        $edit_columns = [];
        $db_debug = $this->db->db_debug;
    }
    public function updateDatabase()
    {
        $json = (array) $json;
        foreach ($tabless as $table) {
            $fields[$table] = $this->db->field_data($table);
        }
        $tbl_ada = array_keys($fields);
        $full_tables = array_unique($full_tables);
        echo true;
        $full_tables = array_merge($tbl_baru, $tbl_ada);
        $json = file_get_contents('./assets/app/db/database.json');
        $fields = [];
        foreach ($full_tables as $table) {
            if (!isset($json[$table])) {
            }
            $this->db->query('ALTER TABLE  `' . $table . '` ENGINE = InnoDB');
            foreach ($json[$table] as $tbl => $jtbl) {
                $this->dbforge->add_key($jtbl->name, true);
                $this->dbforge->add_field($field);
                $field = [$jtbl->name => ['type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => $jtbl->primary_key == 0]];
                if (!($jtbl->primary_key == 1)) {
                }
            }
            if ($this->db->table_exists($table)) {
            }
            foreach ($json[$table] as $jtbl) {
                foreach ($fields[$table] as $ftbl) {
                    $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'default' => $jtbl->default));
                    $field = array($jtbl->name . ' datetime default current_timestamp' . $onUpdate);
                    if ($jtbl->primary_key == 0) {
                    }
                    $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false));
                    if ($jtbl->auto_increment == true) {
                    }
                    if ($jtbl->default == 'CURRENT_TIMESTAMP') {
                    }
                    $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false, 'auto_increment' => true));
                    if (!($jtbl->default != $ftbl->default || $jtbl->max_length != $ftbl->max_length || $jtbl->type != $ftbl->type)) {
                    }
                    $this->dbforge->modify_column($table, $field);
                    if (!($jtbl->name == $ftbl->name)) {
                    }
                    $this->dbforge->add_key($jtbl->name, true);
                    $onUpdate = isset($jtbl->extra) ? ' ' . strtolower($jtbl->extra ?? '') : '';
                    $this->dbforge->modify_column($table, $field);
                }
                $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'default' => $jtbl->default));
                if ($this->db->field_exists($jtbl->name, $table)) {
                }
                $this->dbforge->add_key($jtbl->name, true);
                $this->dbforge->add_column($table, $field);
                $this->dbforge->add_column($table, $field);
                if ($jtbl->primary_key == 0) {
                }
                $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false));
            }
            if (!isset($json[$table])) {
            }
            $this->dbforge->create_table($table, TRUE);
        }
        sort($full_tables);
        $json = json_decode($json);
        $tbl_baru = array_keys($json);
        $tabless = $this->db->list_tables();
    }
    public function checkDb()
    {
        $full_tables = array_merge($tbl_seharusnya, $tbl_ada);
        $tbl_seharusnya = array_keys($json);
        $json = (array) $json;
        $script_create_column = [];
        $json = file_get_contents('./assets/app/db/database.json');
        $create_tables = [];
        $script_edit_column = [];
        $data = ['fields' => $fields, 'create_tables' => $create_tables, 'count_tbl' => count($create_tables), 'add_columns_to_table' => $add_columns, 'count_col' => count($add_columns), 'edit_columns' => $edit_columns, 'count_mod' => count($edit_columns), 'add_tbl' => $this->encryption->encrypt(json_encode($script_create_table)), 'add_col' => $this->encryption->encrypt(json_encode($script_create_column)), 'mod_col' => $this->encryption->encrypt(json_encode($script_edit_column))];
        $db_debug = $this->db->db_debug;
        $tbl_ada = array_keys($fields);
        $this->db->db_debug = $db_debug;
        $full_tables = array_unique($full_tables);
        $this->output_json($data);
        $script_create_table = [];
        foreach ($full_tables as $table) {
            if (!(count($modif_column) > 0)) {
            }
            if (!(count($add_column) > 0)) {
            }
            $script = 'CREATE TABLE `' . $table . '` (';
            $create_tables[] = $json[$table];
            $script_create_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $add_column) . ';';
            $pri = '';
            foreach ($json[$table]->columns as $column) {
                if ($column->type == 'int') {
                }
                $length = '(' . ($column->max_length + 1) . ')';
                $length = '';
                $script .= '`' . $column->name . '` ' . $column->type . $length . $nullable . $default . $extra . $comment . ', ';
                if ($column->max_length == null) {
                }
                $length = '';
                $comment = $column->comment == '' ? '' : ' COMMENT \'' . $column->comment . '\'';
                $nullable = $column->nullable == 'NO' ? ' NOT NULL' : '';
                $pri .= $column->primary != '' ? 'PRIMARY KEY (`' . $column->name . '`)' : '';
                if ($column->type != 'longtext' && $column->type != 'mediumtext' && $column->type != 'text') {
                }
                $extra = $column->extra == '' ? '' : ' ' . strtoupper($column->extra ?? '');
                $default = $column->default == null ? '' : ' DEFAULT ' . $column->default;
                $length = '(' . $column->max_length . ')';
            }
            if (!$this->db->table_exists($table)) {
            }
            $script_create_table[$table] = $script;
            foreach ($json[$table]->columns as $jtbl) {
                $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                array_push($add_column, 'ADD `' . $jtbl->name . '` ' . $jtbl->type . $length . $nullable . $default . $extra . $comment);
                $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                $length = '(' . ($jtbl->max_length + 1) . ')';
                if ($this->db->field_exists($jtbl->name, $table)) {
                }
                $add_columns[$table][] = $jtbl;
                $extra .= ' PRIMARY KEY';
                $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                foreach ($fields[$table]->columns as $ftbl) {
                    $ftbl->default = strtoupper($ftbl->default ?? '');
                    $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                    if (!($jtbl->default != null)) {
                    }
                    if (!($ftbl->extra != null)) {
                    }
                    if (!($jtbl->comment != $ftbl->comment)) {
                    }
                    if (!($jtbl->col_type != $ftbl->col_type || $jtbl->nullable != $ftbl->nullable || $jtbl->default != $ftbl->default || $jtbl->extra != $ftbl->extra || $jtbl->comment != $ftbl->comment)) {
                    }
                    $ftbl->default = str_replace('()', '', $ftbl->default ?? '');
                    $edit_columns[$table][$jtbl->name]['extra'] = $jtbl->extra;
                    if (!($jtbl->primary != $ftbl->primary)) {
                    }
                    array_push($modif_column, 'ADD UNIQUE KEY `' . $jtbl->name . '` (`' . $jtbl->name . '`)');
                    array_push($modif_column, 'MODIFY `' . $jtbl->name . '` ' . $jtbl->col_type . $nullable . $default . $extra . $comment);
                    $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                    if (!($jtbl->name == $ftbl->name)) {
                    }
                    $ftbl->extra = strtoupper($ftbl->extra ?? '');
                    if (strtolower($jtbl->primary ?? '') == 'uni') {
                    }
                    $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                    if (!($jtbl->extra != null)) {
                    }
                    $edit_columns[$table][$jtbl->name]['col_type'] = $jtbl->col_type;
                    if (!($jtbl->default != $ftbl->default)) {
                    }
                    array_push($modif_column, 'ADD PRIMARY KEY (`' . $jtbl->name . '`)');
                    $jtbl->extra = str_replace('()', '', $jtbl->extra ?? '');
                    $edit_columns[$table][$jtbl->name]['comment'] = $jtbl->comment;
                    $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                    $edit_columns[$table][$jtbl->name]['primary'] = $jtbl->primary;
                    $ftbl->extra = str_replace('()', '', $ftbl->extra ?? '');
                    if (!($jtbl->nullable != $ftbl->nullable)) {
                    }
                    $edit_columns[$table][$jtbl->name]['nullable'] = $jtbl->nullable;
                    $edit_columns[$table][$jtbl->name]['default'] = $jtbl->default;
                    if (!($jtbl->col_type != $ftbl->col_type)) {
                    }
                    if (!($jtbl->extra != $ftbl->extra)) {
                    }
                    $jtbl->extra = strtoupper($jtbl->extra ?? '');
                    if (strtolower($jtbl->primary ?? '') == 'pri') {
                    }
                    $jtbl->default = str_replace('()', '', $jtbl->default ?? '');
                    $jtbl->default = strtoupper($jtbl->default ?? '');
                    if (!($ftbl->default != null)) {
                    }
                }
                $length = '';
                if ($jtbl->max_length == null) {
                }
                if (!(strtoupper($extra ?? '') == ' AUTO_INCREMENT')) {
                }
                $length = '';
                $length = '(' . $jtbl->max_length . ')';
                $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                if ($jtbl->type != 'longtext' && $jtbl->type != 'mediumtext' && $jtbl->type != 'text') {
                }
                if ($jtbl->type == 'int') {
                }
            }
            $modif_column = [];
            $script .= $pri . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
            if (!isset($json[$table])) {
            }
            $script_edit_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $modif_column) . ';';
            $add_column = [];
        }
        $this->db->db_debug = FALSE;
        $json = json_decode($json);
        foreach ($tabless as $table) {
            $retval[$i]->nullable = $query[$i]->is_nullable;
            $query = $query->result_object();
            $fields = FALSE;
            $retval = array();
            $sql = 'SELECT `column_name`, `column_type`, `collation_name`, `data_type`, `character_maximum_length`, `numeric_precision`,' . ' `column_default`, `column_key`, `column_comment`, `extra`, `is_nullable`
			FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            $retval[$i]->col_type = $query[$i]->column_type;
            $i++;
            $fields[$table] = (object) ['table_name' => $table, 'columns' => $retval];
            $i = 0;
            $retval[$i]->default = $query[$i]->column_default;
            $retval[$i] = new stdClass();
            $retval[$i]->extra = $query[$i]->extra;
            if (!($i < $c)) {
            }
            $retval[$i]->collation = $query[$i]->collation_name;
            $retval[$i]->primary = $query[$i]->column_key;
            $retval[$i]->comment = $query[$i]->column_comment;
            $c = count($query);
            $retval[$i]->max_length = $query[$i]->character_maximum_length > 0 ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
            if (!(($query = $this->db->query($sql)) === FALSE)) {
            }
            $retval[$i]->name = $query[$i]->column_name;
            $retval[$i]->type = $query[$i]->data_type;
        }
        $add_columns = [];
        sort($full_tables);
        $edit_columns = [];
        $fields = [];
        $tabless = $this->db->list_tables();
    }
    public function createTable()
    {
        $this->output_json($data);
        $queries = '';
        str_replace('%2B', '+', $scripts ?? '');
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        $scripts = $this->input->post('data', true);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $data['message'] = 'Update kolom';
        sleep(1);
        $data['success'] = $this->runQuery($queries);
    }
    public function createColumn()
    {
        $data['message'] = 'Modify kolom';
        $queries = '';
        $scripts = $this->input->post('data', true);
        $this->updateUID();
        sleep(1);
        $this->output_json($data);
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        if (!(strpos('`uid`', $queries) !== false)) {
        }
        $scripts = json_decode($this->encryption->decrypt($scripts));
        str_replace('%2B', '+', $scripts ?? '');
        $data['success'] = $this->runQuery($queries);
    }
    public function editColumn()
    {
        $data['success'] = $this->runQuery($queries);
        $this->output_json($data);
        $scripts = $this->input->post('data', true);
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        $queries = '';
        $data['message'] = 'Update selesai';
        str_replace('%2B', '+', $scripts ?? '');
    }
    function runQuery($script)
    {
        $hostname = $this->db->hostname;
        if (!mysqli_connect_errno()) {
        }
        $mysqli->multi_query($script);
        $hostuser = $this->db->username;
        $mysqli->close();
        $hostpass = $this->db->password;
        return true;
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, $database);
        return mysqli_connect_errno();
        $database = $this->db->database;
    }
    function updateUID()
    {
        foreach ($siswas as $siswa) {
            $input[] = array('id_siswa' => $siswa->id_siswa, 'uid' => $this->uuid->v4());
        }
        $input = array();
        $siswas = $this->db->get('master_siswa')->result();
        return $this->db->update_batch('master_siswa', $input, 'id_siswa');
        $this->load->library('Uuid', 'uuid');
    }
    function make_base()
    {
    }
}
```

---

## File: application/controllers_decoded/Useradmin.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Useradmin extends CI_Controller
{
    public function __construct()
    {
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        redirect('auth');
        $this->load->model('Users_model', 'users');
        $this->load->library('upload');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->library(['datatables', 'form_validation']);
        parent::__construct();
        if ($this->ion_auth->is_admin()) {
        }
        $this->load->model('Master_model', 'master');
    }
    public function is_admin()
    {
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        if (!$encode) {
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function data()
    {
        $this->is_admin();
        $this->output_json($this->users->getDataadmin(), false);
    }
    public function index()
    {
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/data');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Admin Management', 'subjudul' => 'Data Admin', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->is_admin();
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function edit($id)
    {
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $level = $this->ion_auth->get_users_groups($id)->result();
        $data = ['user' => $user, 'judul' => 'Administrator', 'subjudul' => 'Edit Data Admin', 'users' => $this->ion_auth->user($id)->row(), 'groups' => $this->ion_auth->groups()->result(), 'level' => $level[0], 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $this->load->view('users/admin/edit');
        $this->load->view('_templates/dashboard/_header.php', $data);
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        $this->load->view('_templates/dashboard/_footer.php');
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function create()
    {
        $password = $this->input->post('password', true);
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $data = ['status' => false, 'msg' => 'Username tidak tersedia (sudah digunakan).'];
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $email = $this->input->post('email', true);
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|matches[password]|required');
        $this->output_json($data);
        if ($this->form_validation->run() === FALSE) {
        }
        $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email'), 'password' => form_error('password'), 'confirm_password' => form_error('confirm_password')];
        $data = ['status' => true, 'msg' => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'];
        $username = $this->input->post('username', true);
        $group = array('1');
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]|max_length[20]|required');
        if ($this->ion_auth->username_check($username)) {
        }
        $this->is_admin();
        if ($this->ion_auth->email_check($email)) {
        }
        $data = ['status' => false, 'msg' => 'Email tidak tersedia (sudah digunakan).'];
        $data['status'] = false;
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $additional_data = ['first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true)];
        $this->ion_auth->register($username, $password, $email, $additional_data, $group);
    }
    public function edit_info()
    {
        if ($this->form_validation->run() === FALSE) {
        }
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->is_admin();
        $id = $this->input->post('id', true);
        $update = $this->master->update('users', $input, 'id', $id);
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $data['status'] = $update ? true : false;
        $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email')];
        $this->output_json($data);
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $data['status'] = false;
        $input = ['username' => $this->input->post('username', true), 'first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true), 'email' => $this->input->post('email', true)];
    }
    public function edit_status()
    {
        if ($this->form_validation->run() === FALSE) {
        }
        $data['status'] = false;
        $data['errors'] = ['status' => form_error('status')];
        $this->form_validation->set_rules('status', 'Status', 'required');
        $update = $this->master->update('users', $input, 'id', $id);
        $input = ['active' => $this->input->post('status', true)];
        $data['status'] = $update ? true : false;
        $id = $this->input->post('id', true);
        $this->is_admin();
        $this->output_json($data);
    }
    public function edit_level()
    {
        $data['status'] = $update ? true : false;
        $this->form_validation->set_rules('level', 'Level', 'required');
        $data['errors'] = ['level' => form_error('level')];
        $this->output_json($data);
        $data['status'] = false;
        $this->is_admin();
        if ($this->form_validation->run() === FALSE) {
        }
        $id = $this->input->post('id', true);
        $input = ['group_id' => $this->input->post('level', true)];
        $update = $this->master->update('users_groups', $input, 'user_id', $id);
    }
    public function change_password()
    {
        if ($change) {
        }
        $data['status'] = true;
        $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
        $identity = $this->session->userdata('identity');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        if ($this->form_validation->run() === FALSE) {
        }
        $this->output_json($data);
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
    }
    public function delete($id)
    {
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
        $this->is_admin();
        $this->output_json($data);
    }
    function uploadFile($id_user)
    {
        if (isset($_FILES['foto']['name'])) {
        }
        $data['status'] = false;
        $this->upload->initialize($config);
        $data['src'] = base_url() . 'uploads/profiles/' . $result['file_name'];
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
        $data['size'] = $_FILES['foto']['size'];
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $data['src'] = $this->upload->display_errors();
        $config['file_name'] = 'foto_' . $id_user;
        $this->output_json($data);
        $data['status'] = true;
        if (!$this->upload->do_upload('foto')) {
        }
        $config['upload_path'] = './uploads/profiles/';
        $config['overwrite'] = true;
        $data['src'] = '';
        $data['type'] = $_FILES['foto']['type'];
        $result = $this->upload->data();
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        echo 'File Delete Successfully';
        if (!unlink($file_name)) {
        }
    }
    function saveProfile()
    {
        $foto = $this->input->post('foto');
        $update = $this->db->replace('users_profile', $insert);
        $jabatan = $this->input->post('jabatan');
        $res['status'] = $update;
        $nama = $this->input->post('nama_lengkap');
        $user = $this->ion_auth->user()->row();
        $insert = ['id_user' => $user->id, 'nama_lengkap' => $nama, 'jabatan' => $jabatan, 'foto' => str_replace(base_url(), '', $foto ?? '')];
        $this->output_json($res);
    }
}
```

---

## File: application/controllers_decoded/Userguru.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Userguru extends CI_Controller
{
    public function __construct()
    {
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        parent::__construct();
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->load->model('Dashboard_model', 'dashboard');
        redirect('auth');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Master_model', 'master');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function data()
    {
        $this->output_json($this->users->getUserGuru($tp->id_tp, $smt->id_smt), false);
        $smt = $this->dashboard->getSemesterActive();
        $tp = $this->dashboard->getTahunActive();
    }
    public function index()
    {
        $this->load->view('_templates/dashboard/_footer');
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $id = $this->users->getGuruByUsername($user->username);
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        $user = $this->ion_auth->user()->row();
        $this->edit($id->id_guru);
        $this->load->view('users/guru/data');
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        if ($group === 'admin') {
        }
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('_templates/dashboard/_header', $data);
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Data User Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
    }
    public function activate($id)
    {
        $group = array('2');
        if ($this->ion_auth->username_check($username)) {
        }
        $guru = $this->users->getDataGuru($id);
        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $password = trim($guru->password ?? '');
        $data['pass'] = $password;
        $data = ['status' => false, 'msg' => 'Username ' . $email . ' tidak tersedia (sudah digunakan).'];
        $last_name = count($nama) > 2 ? $nama[1] : end($nama);
        $first_name = $nama[0];
        $email = strtolower($guru->username ?? '') . '@guru.com';
        $this->db->set('id_user', $id_user);
        $data = ['status' => false, 'msg' => 'Username ' . $username . ' tidak tersedia (sudah digunakan).'];
        $data = ['status' => true, 'msg' => 'Akun ' . $guru->nama_guru . ' diaktifkan.'];
        $username = trim($guru->username ?? '');
        $this->output_json($data);
        $this->db->update('master_guru');
        $this->db->where('id_guru', $id);
        if ($this->ion_auth->email_check($email)) {
        }
        $nama = explode(' ', $guru->nama_guru ?? '');
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
    }
    public function deactivate($id = NULL)
    {
        $data = ['status' => $deleted, 'msg' => 'telah dinonaktifkan.'];
        $id = (int) $id;
        $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
        }
        $deleted = $this->ion_auth->delete_user($id);
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
        $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        foreach ($guruAktif as $guru) {
            $this->activate($guru->id_guru);
            $jum += 1;
            if ($guru->aktif > 0) {
            }
        }
        $jum = 0;
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru diaktifkan.'];
        $this->output_json($data);
        $guruAktif = $this->users->getGuruAktif();
    }
    public function nonaktifkanSemua()
    {
        foreach ($guruAktif as $guru) {
            $del = $this->deactivate($guru->id, '');
            $this->output_json($del);
            if ($guru->aktif > 0) {
            }
            $jum += 1;
        }
        $jum = 0;
        $guruAktif = $this->users->getGuruAktif();
        $this->output_json($data);
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru dinonaktifkan.'];
    }
    public function edit($id)
    {
        $data['smt_active'] = $smt;
        $this->load->view('_templates/dashboard/_header', $data);
        $data['levels'] = $this->users->getLevelGuru();
        $users = $this->users->getUsers($guru->username);
        $this->load->view('members/guru/templates/header', $data);
        $data['guru'] = $guru;
        $data['groups'] = $this->ion_auth->groups()->result();
        $data['kelass'] = $this->users->getKelas($tp->id_tp, $smt->id_smt);
        $this->load->view('users/guru/edit');
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $data['tp'] = $this->dashboard->getTahun();
        $data['users'] = $users;
        $smt = $this->dashboard->getSemesterActive();
        if ($group === 'admin') {
        }
        $user = $this->ion_auth->user()->row();
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'setting' => $this->dashboard->getSetting()];
        $guru = $this->users->getDetailGuru($id);
        $data['mapels'] = $this->users->getMapel();
        $tp = $this->dashboard->getTahunActive();
        $this->load->view('users/guru/edit');
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_footer');
    }
    public function editLogin()
    {
        $guru = $this->db->get_where('master_guru', 'id_guru="' . $id_guru . '"')->row();
        $pass = $this->input->post('new', true);
        $this->db->set('id_user', $id_user);
        $last_name = end($nama);
        $deleted = $this->ion_auth->delete_user((int) $user_guru->id);
        $group = array('2');
        $nama = explode(' ', $guru->nama_guru ?? '');
        $username = trim($username ?? '');
        $data['status'] = $status;
        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $deleted = true;
        $this->db->set('username', $username);
        $this->db->set('password', $password);
        $password = trim($pass ?? '');
        if ($this->form_validation->run() === FALSE) {
        }
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        if ($deleted) {
        }
        $this->db->where('id_guru', $id_guru);
        $username = $this->input->post('username', true);
        $msg = $status ? 'Update berhasil' : 'Gagal mengganti username/passsword';
        $email = strtolower($username) . '@guru.com';
        $user_guru = $this->db->get_where('users', 'email="' . $email . '"')->row();
        $msg = 'Gagal mengganti username/passsword';
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $status = false;
        $data = ['status' => false, 'errors' => ['username' => 'Username sudah digunakan']];
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $this->output_json($data);
        if ($guru_lain && $guru_lain->id_guru != $id_guru) {
        }
        $id_guru = $this->input->post('id_guru', true);
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $first_name = $nama[0];
        $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        $guru_lain = $this->master->getUserIdGuruByUsername($username);
        $data['text'] = $msg;
        $status = $this->db->update('master_guru');
        if (!($user_guru != null)) {
        }
    }
    function buangspasi($teks)
    {
        $remove[] = ' ';
        $hasil = $teks;
        if (!strpos($teks, ' ')) {
        }
        $remove[] = '\'';
        $hasil = str_replace($remove, '', $teks ?? '');
        $teks = trim($teks ?? '');
        $remove[] = '.';
        return $hasil;
    }
    private function registerGuru($username, $password, $email, $additional_data, $group)
    {
        $data['status'] = true;
        $data['status'] = false;
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['id'] = $reg;
        return $data;
        if (!($reg == false)) {
        }
    }
    public function reset_login()
    {
        if ($this->db->delete('login_attempts')) {
        }
        $data = ['status' => false, 'msg' => ' gagal direset'];
        $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        $this->db->where('login', $username);
        $this->output_json($data, true);
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
        $data = ['status' => true, 'msg' => ' berhasil direset'];
        $username = $this->input->get('username', true);
    }
}
```

---

## File: application/controllers_decoded/Usersiswa.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Usersiswa extends CI_Controller
{
    public function __construct()
    {
        if ($this->ion_auth->logged_in()) {
        }
        redirect('auth');
        parent::__construct();
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Users_model', 'users');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
    }
    public function is_has_access()
    {
        $user_id = $this->ion_auth->user()->row()->id;
        if (!(!$group === 'admin' or !$group === 'guru')) {
        }
        $group = $this->ion_auth->get_users_groups($user_id)->row()->name;
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
        $data = json_encode($data);
    }
    public function data()
    {
        $smt = $this->dashboard->getSemesterActive();
        $this->is_has_access();
        $tp = $this->dashboard->getTahunActive();
        $this->output_json($this->users->getUserSiswa($tp->id_tp, $smt->id_smt), false);
    }
    public function index()
    {
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('users/siswa/data');
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Data User Siswa', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $user = $this->ion_auth->user()->row();
        $this->load->view('_templates/dashboard/_footer');
        $this->is_has_access();
        $data['smt'] = $this->dashboard->getSemester();
    }
    public function list()
    {
        $limit = $this->input->post('limit', true);
        $smt = $this->dashboard->getSemesterActive();
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit];
        $this->output_json($data);
        $search = $this->input->post('search', true);
        $offset = ($page - 1) * $limit;
        $lists = $this->users->getUserSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $search);
        $tp = $this->dashboard->getTahunActive();
        $count_siswa = $this->users->getUserSiswaTotalPage($search);
        $page = $this->input->post('page', true);
    }
    private function registerSiswa($username, $password, $email, $additional_data, $group)
    {
        if (!($reg == false)) {
        }
        $data['id'] = $reg;
        return $data;
        $data['status'] = true;
        $data['status'] = false;
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
    }
    private function aktifkan($siswa)
    {
        $user_siswa = $this->db->get_where('users', 'email="' . $email . '"')->row();
        $deleted = true;
        $group = array('3');
        $deleted = $this->ion_auth->delete_user($user_siswa->id);
        return $data;
        $username = trim($siswa->username ?? '');
        if ($deleted) {
        }
        $data = ['status' => $reg, 'msg' => !$reg ? 'Akun ' . $siswa->nama . ' gagal diaktifkan.' : 'Akun ' . $siswa->nama . ' diaktifkan.'];
        $email = $siswa->nis . '@siswa.com';
        $reg = $this->registerSiswa($username, $password, $email, $additional_data, $group);
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $data = ['status' => false, 'msg' => 'Akun siswa tidak tersedia (sudah digunakan).'];
        $last_name = end($nama);
        $first_name = $nama[0];
        $password = trim($siswa->password ?? '');
        if (!($user_siswa != null)) {
        }
        $nama = explode(' ', $siswa->nama ?? '');
    }
    public function activate($id)
    {
        $data = $this->aktifkan($siswa);
        $siswa = $this->users->getDataSiswa($id);
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa diaktifkan.'];
        $jum = 0;
        foreach ($siswaAktif as $siswa) {
            if (!($siswa->aktif == 0)) {
            }
            $this->aktifkan($siswa);
            $jum += 1;
        }
        $this->output_json($data);
        $siswaAktif = $this->users->getSiswaAktif();
    }
    private function nonaktifkan($user, $nama)
    {
        if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
        }
        $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        return $data;
        $deleted = $this->ion_auth->delete_user($user->id);
        $data = ['status' => $deleted, 'msg' => $deleted ? 'Siswa ' . urldecode($nama) . ' dinonaktifkan.' : 'Siswa ' . urldecode($nama) . ' gagal dinonaktifkan.'];
        $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
    }
    public function deactivate($username, $nama)
    {
        $this->output_json($data, true);
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
        $data = $this->nonaktifkan($user, $nama);
        $user = $this->users->getUsers($username);
        $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
    }
    public function reset_login($username, $nama)
    {
        $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        }
        $data = ['status' => true, 'msg' => 'User ' . $nama . ' berhasil direset'];
        if ($this->db->delete('login_attempts')) {
        }
        $this->output_json($data, true);
        $this->db->where('login', $username);
        $data = ['status' => false, 'msg' => 'User ' . $nama . ' gagal direset'];
    }
    public function nonaktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $this->output_json($data);
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa dinonaktifkan.'];
        $jum = 0;
        foreach ($siswaAktif as $siswa) {
            $jum += 1;
            $del = $this->nonaktifkan($siswa, $siswa->nama);
            if ($del['status']) {
            }
            $this->output_json($del);
            if (!($siswa->aktif > 0)) {
            }
        }
    }
    public function edit($id)
    {
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $this->load->view('users/siswa/edit');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        if ($this->ion_auth->is_admin()) {
        }
        $siswa = $this->master->getDataSiswaById($tp->id_tp, $smt->id_smt, $id);
        $data['siswa'] = $siswa;
        $this->load->view('_templates/dashboard/_footer');
        $this->load->view('users/siswa/edit');
        $data['tp'] = $this->dashboard->getTahun();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['guru'] = $guru;
        $this->load->view('_templates/dashboard/_header', $data);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $data['smt'] = $this->dashboard->getSemester();
        $smt = $this->dashboard->getSemesterActive();
        $data['smt_active'] = $smt;
    }
    public function update()
    {
        $oldPass = $this->input->post('old', true);
        $id_siswa = $this->input->post('id_siswa', true);
        $this->form_validation->set_rules('old', 'Password Lama', 'required|numeric|trim|min_length[6]');
        $this->form_validation->set_rules('username', 'Username', 'required|numeric|trim|min_length[6]|is_unique[master_siswa.username]');
        $this->form_validation->set_rules('new', 'Password Baru', 'required|numeric|trim|min_length[6]');
        $username = $this->input->post('username', true);
        $newPass = $this->input->post('new', true);
    }
    public function change_password()
    {
        $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        if ($change) {
        }
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        if ($this->form_validation->run() === FALSE) {
        }
        $identity = $this->session->userdata('identity');
        $this->output_json($data);
        $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
        $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        $data['status'] = true;
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
    }
    public function delete($id)
    {
        $this->output_json($data);
        $this->is_has_access();
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
    }
    private function hash_password($password)
    {
        if (!(empty($password) || strpos($password, ' ') !== FALSE || strlen($password) > 4096)) {
        }
        return password_hash($password, PASSWORD_BCRYPT);
        return FALSE;
    }
}
```

---

## File: application/controllers_decoded/Users.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Users extends CI_Controller
{
    public function __construct()
    {
        if ($this->ion_auth->logged_in()) {
        }
        $this->load->model('Dashboard_model', 'admindashboard');
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Users_model', 'users');
        $this->load->library(['datatables', 'form_validation']);
        redirect('auth');
        $this->load->model('Master_model', 'master');
        parent::__construct();
    }
    public function is_admin()
    {
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
    }
    public function data($id = null)
    {
        $this->is_admin();
        $this->output_json($this->users->getDataUsers($id), false);
    }
    public function index()
    {
        $data['tp'] = $this->admindashboard->getTahun();
        $this->load->view('users/data');
        $this->is_admin();
        $this->load->view('_templates/dashboard/footer.php');
        $data['tp_active'] = $this->admindashboard->getTahunActive();
        $data['smt_active'] = $this->admindashboard->getSemesterActive();
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'User Management', 'subjudul' => 'Data User'];
        $data['smt'] = $this->admindashboard->getSemester();
        $this->load->view('_templates/dashboard/header.php', $data);
    }
    public function edit($id)
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'users' => $this->ion_auth->user($id)->row(), 'groups' => $this->ion_auth->groups()->result(), 'level' => $level[0]];
        $this->load->view('_templates/dashboard/footer.php');
        $level = $this->ion_auth->get_users_groups($id)->result();
        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/edit');
    }
    public function edit_info()
    {
        $data['status'] = false;
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $id = $this->input->post('id', true);
        $update = $this->master->update('users', $input, 'id', $id);
        $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email')];
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->is_admin();
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        if ($this->form_validation->run() === FALSE) {
        }
        $data['status'] = $update ? true : false;
        $input = ['username' => $this->input->post('username', true), 'first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true), 'email' => $this->input->post('email', true)];
        $this->output_json($data);
    }
    public function edit_status()
    {
        $data['status'] = false;
        $data['status'] = $update ? true : false;
        $update = $this->master->update('users', $input, 'id', $id);
        $data['errors'] = ['status' => form_error('status')];
        if ($this->form_validation->run() === FALSE) {
        }
        $id = $this->input->post('id', true);
        $this->is_admin();
        $input = ['active' => $this->input->post('status', true)];
        $this->output_json($data);
        $this->form_validation->set_rules('status', 'Status', 'required');
    }
    public function edit_level()
    {
        $update = $this->master->update('users_groups', $input, 'user_id', $id);
        $this->is_admin();
        $data['status'] = false;
        $this->output_json($data);
        $this->form_validation->set_rules('level', 'Level', 'required');
        $id = $this->input->post('id', true);
        $input = ['group_id' => $this->input->post('level', true)];
        $data['status'] = $update ? true : false;
        $data['errors'] = ['level' => form_error('level')];
        if ($this->form_validation->run() === FALSE) {
        }
    }
    public function change_password()
    {
        $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $data['status'] = true;
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($change) {
        }
        $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
        $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
        if ($this->form_validation->run() === FALSE) {
        }
        $identity = $this->session->userdata('identity');
        $this->output_json($data);
    }
    public function delete($id)
    {
        $this->is_admin();
        $this->output_json($data);
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
    }
}
```

---

## File: application/controllers_decoded/Walicatatan.php

```php
<?php

class Walicatatan extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->model('Master_model', 'master');
        if (!$this->ion_auth->logged_in()) {
        }
        parent::__construct();
        redirect('auth');
        $this->load->library(['datatables', 'form_validation']);
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
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
        $this->load->view('members/guru/templates/header', $data);
        $data['guru'] = $guru;
        $tp = $this->master->getTahunActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Catatan Wali Kelas', 'subjudul' => 'Catatan Kelas', 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/wali/catatan');
        $data['tp'] = $this->dashboard->getTahun();
        $data['catatan_siswa'] = $this->kelas->getCatatanSiswa($tp->id_tp, $smt->id_smt, $guru->wali_kelas);
        $this->load->view('members/guru/templates/footer');
        $smt = $this->master->getSemesterActive();
        $data['catatan_kelas'] = $this->kelas->getCatatanKelas($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $data['smt_active'] = $smt;
        $data['tp_active'] = $tp;
    }
    public function siswa()
    {
        $id_kelas = $this->input->get('id_kelas');
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['siswa'] = $this->master->getSiswaById($id_siswa);
        $data = ['user' => $user, 'judul' => 'Catatan Siswa', 'subjudul' => 'Catatan Siswa', 'setting' => $this->dashboard->getSetting()];
        $data['catatan_siswa'] = $this->kelas->getAllCatatanSiswa($id_siswa, $tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = $id_kelas;
        $this->load->view('members/guru/templates/header', $data);
        $data['tp_active'] = $tp;
        $this->load->view('members/guru/wali/persiswa');
        $smt = $this->master->getSemesterActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $id_siswa = $this->input->get('id_siswa');
        $this->load->view('members/guru/templates/footer');
        $data['tp'] = $this->dashboard->getTahun();
        $tp = $this->master->getTahunActive();
        $data['guru'] = $guru;
    }
    public function saveCatatanKelas()
    {
        $user = $this->ion_auth->user()->row();
        $level = $this->input->post('level', true);
        $tp = $this->dashboard->getTahunActive();
        $text = $this->input->post('text', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '1', 'level' => $level, 'id_kelas' => $guru->wali_kelas, 'text' => $text, 'reading' => serialize([])];
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->output_json($insert);
        $smt = $this->dashboard->getSemesterActive();
        $insert = $this->master->create('kelas_catatan_wali', $data);
    }
    public function saveCatatanSiswa()
    {
        $user = $this->ion_auth->user()->row();
        $insert = $this->master->create('kelas_catatan_wali', $data);
        $id_siswa = $this->input->post('id_siswa');
        $text = $this->input->post('text', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '2', 'level' => $level, 'id_kelas' => $guru->wali_kelas, 'id_siswa' => $id_siswa, 'text' => $text, 'reading' => serialize([])];
        $level = $this->input->post('level', true);
        $this->output_json($insert);
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $smt = $this->dashboard->getSemesterActive();
        $tp = $this->dashboard->getTahunActive();
    }
    public function updateCatatanKelas()
    {
    }
    public function hapus($id_catatan)
    {
        $delete = $this->master->delete('kelas_catatan_wali', $id_catatan, 'id_catatan');
        $this->output_json($delete);
    }
}
```

---

## File: application/controllers_decoded/Walisiswa.php

```php
<?php

class Walisiswa extends CI_Controller
{
    public function __construct()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        redirect('auth');
        $this->load->model('Master_model', 'master');
        $this->load->library(['datatables', 'form_validation']);
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->form_validation->set_error_delimiters('', '');
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        parent::__construct();
    }
    public function output_json($data, $encode = true)
    {
        $this->output->set_content_type('application/json')->set_output($data);
        $data = json_encode($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $this->load->view('members/guru/templates/footer');
        $data['tp_active'] = $tp;
        $tp = $this->master->getTahunActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $tp = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data = ['user' => $user, 'judul' => 'Daftar Siswa', 'subjudul' => 'Siswa Kelas ' . $kelas->nama_kelas, 'setting' => $this->dashboard->getSetting()];
        $this->load->view('members/guru/wali/kelas');
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data['guru'] = $guru;
        $smt = $this->master->getSemesterActive();
        $data['siswas'] = $this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $guru->wali_kelas, 0, 0);
        $data['smt_active'] = $smt;
        $data['tp'] = $this->dashboard->getTahun();
        $this->load->view('members/guru/templates/header', $data);
        $kelas = $this->master->getKelasById($guru->wali_kelas);
    }
    public function dataKelas()
    {
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->output_json($this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $guru->wali_kelas), false);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
    }
    public function list()
    {
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit];
        $id_kelas = $this->input->post('kelas', true);
        $count_siswa = $this->master->getDataSiswaByKelasPage($tp->id_tp, $smt->id_smt, $id_kelas, $search);
        $lists = $this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $id_kelas, $offset, $limit, $search);
        $tp = $this->dashboard->getTahunActive();
        $page = $this->input->post('page', true);
        $search = $this->input->post('search', true);
        $limit = $this->input->post('limit', true);
        $offset = ($page - 1) * $limit;
        $this->output_json($data);
        $smt = $this->dashboard->getSemesterActive();
    }
    public function edit($id)
    {
        $tp = $this->master->getTahunActive();
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $siswa = $this->master->getSiswaById($id);
        $data['smt'] = $this->dashboard->getSemester();
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $siswa->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $siswa->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $siswa->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $siswa->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $siswa->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $user = $this->ion_auth->user()->row();
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $siswa->nama, 'icon' => 'far fa-user', 'req' => 'required', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $siswa->nis, 'icon' => 'far fa-id-card', 'req' => 'required', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $siswa->nisn, 'icon' => 'far fa-id-card', 'req' => '', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $siswa->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'req' => 'required', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $siswa->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'req' => 'required', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $siswa->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'req' => 'required', 'class' => 'tahun', 'type' => 'text'], ['name' => 'sekolah_asal', 'label' => 'Sekolah Asal', 'value' => $siswa->sekolah_asal, 'icon' => 'fas fa-graduation-cap', 'req' => '', 'class' => '', 'type' => 'text']];
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Edit Data Siswa', 'siswa' => $siswa, 'setting' => $this->dashboard->getSetting()];
        $smt = $this->master->getSemesterActive();
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $siswa->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $siswa->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $siswa->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $siswa->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $siswa->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $siswa->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $siswa->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $siswa->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $siswa->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $siswa->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $data['tp'] = $this->dashboard->getTahun();
        $inputBio = [['name' => 'status_keluarga', 'label' => 'Status dalam Keluarga', 'value' => $siswa->status_keluarga == '' ? '1' : $siswa->status_keluarga, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['name' => 'anak_ke', 'label' => 'Anak ke', 'value' => $siswa->anak_ke, 'icon' => 'far fa-user', 'class' => '', 'type' => 'number'], ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $siswa->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $siswa->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $siswa->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $siswa->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $siswa->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $siswa->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $siswa->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $siswa->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $siswa->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'provinsi', 'label' => 'Provinsi', 'value' => $siswa->provinsi, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $siswa->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $siswa->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/footer');
        $this->load->view('members/guru/templates/header', $data);
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $this->load->view('members/guru/wali/edit');
    }
    public function updateData()
    {
        $this->output_json($data);
        $u_nis = $siswa->nis === $nis ? '' : '|is_unique[master_siswa.nis]';
        $id_siswa = $this->input->post('id_siswa', true);
        if ($this->form_validation->run() == FALSE) {
        }
        $nisn = $this->input->post('nisn', true);
        $action = $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $data['text'] = 'Siswa berhasil diperbaharui';
        $nis = $this->input->post('nis', true);
        $data['text'] = 'Data Sudah ada, Pastikan NIS, dan NISN belum digunakan siswa lain';
        $data['insert'] = $input;
        $data['insert'] = false;
        $siswa = $this->master->getSiswaById($id_siswa);
        $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->input->post('tanggal_lahir', true), 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => 'uploads/foto_siswa/' . $nis . '.jpg'];
    }
    function uploadFile($id_siswa)
    {
        $data['src'] = $this->upload->display_errors();
        $data['status'] = true;
        $this->db->where('id_siswa', $id_siswa);
        $this->db->update('master_siswa');
        if (isset($_FILES['foto']['name'])) {
        }
        $config['upload_path'] = './uploads/foto_siswa/';
        $siswa = $this->master->getSiswaById($id_siswa);
        $result = $this->upload->data();
        $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
        $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
        $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
        $data['status'] = false;
        $data['size'] = $_FILES['foto']['size'];
        $data['src'] = '';
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('foto')) {
        }
        $config['file_name'] = $siswa->nis;
        $this->output_json($data);
        $config['overwrite'] = true;
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $data['type'] = $_FILES['foto']['type'];
    }
    function deleteFile($id_siswa)
    {
        $this->db->update('master_siswa');
        if (!($file_name != 'assets/img/siswa.png')) {
        }
        if (!unlink($file_name)) {
        }
        echo 'File Delete Successfully';
        $file_name = str_replace(base_url(), '', $src ?? '');
        $this->db->set('foto', '');
        $src = $this->input->post('src');
        $this->db->where('id_siswa', $id_siswa);
    }
    public function delete()
    {
        if (!$chk) {
        }
        $this->output_json(['status' => false]);
        $this->output_json(['status' => true, 'total' => count($chk)]);
        $this->master->delete('buku_induk', $chk, 'id_siswa');
        if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
        }
        $chk = $this->input->post('checked', true);
    }
}
```

---

## File: application/controllers_decoded/Walistruktur.php

```php
<?php

class Walistruktur extends CI_Controller
{
    public function __construct()
    {
        $this->load->library(['datatables', 'form_validation']);
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        parent::__construct();
        redirect('auth');
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        if (!$this->ion_auth->logged_in()) {
        }
        $this->load->model('Dropdown_model', 'dropdown');
    }
    public function output_json($data, $encode = true)
    {
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
        if (!$encode) {
        }
    }
    public function index()
    {
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if ($struktur == null) {
        }
        $data['id_kelas'] = $guru->wali_kelas;
        $siswas[''] = 'Pilih Siswa';
        $data['struktur'] = $struktur;
        $data['struktur'] = json_decode(json_encode($this->kelas->dummyStruktur()));
        $this->load->view('members/guru/templates/footer');
        $data['siswas'] = $siswas;
        $data = ['user' => $user, 'judul' => 'Struktur Organisasi', 'subjudul' => 'Struktur Organisasi', 'setting' => $this->dashboard->getSetting()];
        $data['smt'] = $this->dashboard->getSemester();
        $user = $this->ion_auth->user()->row();
        $data['gurus'] = $this->dropdown->getAllGuru();
        $this->load->view('members/guru/wali/struktur');
        $smt = $this->master->getSemesterActive();
        $data['tp_active'] = $tp;
        $siswa = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $struktur = $this->kelas->getStrukturKelas($guru->wali_kelas);
        $this->load->view('members/guru/templates/header', $data);
        $data['smt_active'] = $smt;
        foreach ($siswa as $key => $value) {
            $siswas[$value->id_siswa] = $value->nama;
        }
        $tp = $this->master->getTahunActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['guru'] = $guru;
    }
    public function save()
    {
        $insert = $this->db->replace('kelas_struktur', $data);
        $this->output_json($insert);
        $data = ['id_kelas' => $this->input->post('id_kelas'), 'ketua' => $this->input->post('ketua'), 'wakil_ketua' => $this->input->post('wakil_ketua'), 'sekretaris_1' => $this->input->post('sekretaris_1'), 'sekretaris_2' => $this->input->post('sekretaris_2'), 'bendahara_1' => $this->input->post('bendahara_1'), 'bendahara_2' => $this->input->post('bendahara_2'), 'sie_ekstrakurikuler' => $this->input->post('sie_ekstrakurikuler'), 'sie_upacara' => $this->input->post('sie_upacara'), 'sie_olahraga' => $this->input->post('sie_olahraga'), 'sie_keagamaan' => $this->input->post('sie_keagamaan'), 'sie_keamanan' => $this->input->post('sie_keamanan'), 'sie_ketertiban' => $this->input->post('sie_ketertiban'), 'sie_kebersihan' => $this->input->post('sie_kebersihan'), 'sie_keindahan' => $this->input->post('sie_keindahan'), 'sie_kesehatan' => $this->input->post('sie_kesehatan'), 'sie_kekeluargaan' => $this->input->post('sie_kekeluargaan'), 'sie_humas' => $this->input->post('sie_humas')];
    }
}
```

---

## File: application/controllers_decoded/Welcome.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->view('welcome_message');
    }
}
```

---

