## File: application/controllers_progress/Useradmin.php

```php
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
```

---

## File: application/controllers_progress/Userguru.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Userguru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
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
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $this->dashboard->getTahunActive();
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $this->dashboard->getSemesterActive();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('users/guru/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $id = $this->users->getGuruByUsername($user->username);
            $this->edit($id->id_guru);
        }
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
            $data = ['status' => false, 'msg' => 'Username ' . $username . ' tidak tersedia (sudah digunakan).'];
        } else {
            if ($this->ion_auth->email_check($email)) {
            }
            $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
            $data = ['status' => true, 'msg' => 'Akun ' . $guru->nama_guru . ' diaktifkan.'];
            $this->db->set('id_user', $id_user);
            $this->db->where('id_guru', $id);
            $this->db->update('master_guru');
        }
        $data['pass'] = $password;
        $this->output_json($data);
    }
    public function deactivate($id = NULL)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $id = (int) $id;
            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            }
            $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
        }
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;
        foreach ($guruAktif as $guru) {
            if ($guru->aktif > 0) {
            } else {
                $this->activate($guru->id_guru);
                $jum += 1;
            }
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
                $del = $this->deactivate($guru->id, '');
                $this->output_json($del);
                $jum += 1;
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
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['groups'] = $this->ion_auth->groups()->result();
            $data['kelass'] = $this->users->getKelas($tp->id_tp, $smt->id_smt);
            $data['mapels'] = $this->users->getMapel();
            $data['levels'] = $this->users->getLevelGuru();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('users/guru/edit');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('users/guru/edit');
            $this->load->view('members/guru/templates/footer');
        }
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
            $data = ['status' => false, 'errors' => ['username' => 'Username sudah digunakan']];
        } else {
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
        }
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
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $this->db->where('login', $username);
            if ($this->db->delete('login_attempts')) {
            }
            $data = ['status' => false, 'msg' => ' gagal direset'];
        }
        $this->output_json($data, true);
    }
}
```

---

## File: application/controllers_progress/Usersiswa.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Usersiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables', 'form_validation']);
        } else {
            redirect('auth');
            $this->load->library(['datatables', 'form_validation']);
        }
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function is_has_access()
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $group = $this->ion_auth->get_users_groups($user_id)->row()->name;
        if (!(!$group === 'admin' or !$group === 'guru')) {
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
        $this->is_has_access();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->users->getUserSiswa($tp->id_tp, $smt->id_smt), false);
    }
    public function index()
    {
        $this->is_has_access();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Data User Siswa', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('users/siswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function list()
    {
        $page = $this->input->post('page', true);
        $limit = $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $offset = ($page - 1) * $limit;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $count_siswa = $this->users->getUserSiswaTotalPage($search);
        $lists = $this->users->getUserSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $search);
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit];
        $this->output_json($data);
    }
    private function registerSiswa($username, $password, $email, $additional_data, $group)
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
    private function aktifkan($siswa)
    {
        $nama = explode(' ', $siswa->nama ?? '');
        $first_name = $nama[0];
        $last_name = end($nama);
        $username = trim($siswa->username ?? '');
        $password = trim($siswa->password ?? '');
        $email = $siswa->nis . '@siswa.com';
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('3');
        $user_siswa = $this->db->get_where('users', 'email="' . $email . '"')->row();
        $deleted = true;
        if (!($user_siswa != null)) {
            if ($deleted) {
            }
            $data = ['status' => false, 'msg' => 'Akun siswa tidak tersedia (sudah digunakan).'];
            return $data;
        } else {
            $deleted = $this->ion_auth->delete_user($user_siswa->id);
            if ($deleted) {
            }
            $data = ['status' => false, 'msg' => 'Akun siswa tidak tersedia (sudah digunakan).'];
            return $data;
        }
    }
    public function activate($id)
    {
        $siswa = $this->users->getDataSiswa($id);
        $data = $this->aktifkan($siswa);
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum = 0;
        foreach ($siswaAktif as $siswa) {
            if (!($siswa->aktif == 0)) {
            } else {
                $this->aktifkan($siswa);
                $jum += 1;
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa diaktifkan.'];
        $this->output_json($data);
    }
    private function nonaktifkan($user, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
            return $data;
        } else {
            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            }
            $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
            return $data;
        }
    }
    public function deactivate($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $user = $this->users->getUsers($username);
            $data = $this->nonaktifkan($user, $nama);
        }
        $this->output_json($data, true);
    }
    public function reset_login($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $this->db->where('login', $username);
            if ($this->db->delete('login_attempts')) {
            }
            $data = ['status' => false, 'msg' => 'User ' . $nama . ' gagal direset'];
        }
        $this->output_json($data, true);
    }
    public function nonaktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum = 0;
        foreach ($siswaAktif as $siswa) {
            if (!($siswa->aktif > 0)) {
            } else {
                $del = $this->nonaktifkan($siswa, $siswa->nama);
                if ($del['status']) {
                }
                $this->output_json($del);
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa dinonaktifkan.'];
        $this->output_json($data);
    }
    public function edit($id)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswa = $this->master->getDataSiswaById($tp->id_tp, $smt->id_smt, $id);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'setting' => $this->dashboard->getSetting()];
        $data['siswa'] = $siswa;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('users/siswa/edit');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('users/siswa/edit');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function update()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $username = $this->input->post('username', true);
        $oldPass = $this->input->post('old', true);
        $newPass = $this->input->post('new', true);
        $this->form_validation->set_rules('username', 'Username', 'required|numeric|trim|min_length[6]|is_unique[master_siswa.username]');
        $this->form_validation->set_rules('old', 'Password Lama', 'required|numeric|trim|min_length[6]');
        $this->form_validation->set_rules('new', 'Password Baru', 'required|numeric|trim|min_length[6]');
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
        $this->is_has_access();
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
        $this->output_json($data);
    }
    private function hash_password($password)
    {
        if (!(empty($password) || strpos($password, ' ') !== FALSE || strlen($password) > 4096)) {
            return password_hash($password, PASSWORD_BCRYPT);
        } else {
            return FALSE;
        }
    }
}
```

---

## File: application/controllers_progress/Users.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables', 'form_validation']);
        } else {
            redirect('auth');
            $this->load->library(['datatables', 'form_validation']);
        }
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'admindashboard');
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
    public function data($id = null)
    {
        $this->is_admin();
        $this->output_json($this->users->getDataUsers($id), false);
    }
    public function index()
    {
        $this->is_admin();
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'User Management', 'subjudul' => 'Data User'];
        $data['tp'] = $this->admindashboard->getTahun();
        $data['tp_active'] = $this->admindashboard->getTahunActive();
        $data['smt'] = $this->admindashboard->getSemester();
        $data['smt_active'] = $this->admindashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/data');
        $this->load->view('_templates/dashboard/footer.php');
    }
    public function edit($id)
    {
        $level = $this->ion_auth->get_users_groups($id)->result();
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'users' => $this->ion_auth->user($id)->row(), 'groups' => $this->ion_auth->groups()->result(), 'level' => $level[0]];
        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/edit');
        $this->load->view('_templates/dashboard/footer.php');
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
}
```

---

## File: application/controllers_progress/Walicatatan.php

```php
<?php

class Walicatatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
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
        $data = ['user' => $user, 'judul' => 'Catatan Wali Kelas', 'subjudul' => 'Catatan Kelas', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['catatan_kelas'] = $this->kelas->getCatatanKelas($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $data['catatan_siswa'] = $this->kelas->getCatatanSiswa($tp->id_tp, $smt->id_smt, $guru->wali_kelas);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/catatan');
        $this->load->view('members/guru/templates/footer');
    }
    public function siswa()
    {
        $id_siswa = $this->input->get('id_siswa');
        $id_kelas = $this->input->get('id_kelas');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Catatan Siswa', 'subjudul' => 'Catatan Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['siswa'] = $this->master->getSiswaById($id_siswa);
        $data['catatan_siswa'] = $this->kelas->getAllCatatanSiswa($id_siswa, $tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = $id_kelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/persiswa');
        $this->load->view('members/guru/templates/footer');
    }
    public function saveCatatanKelas()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $text = $this->input->post('text', true);
        $level = $this->input->post('level', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '1', 'level' => $level, 'id_kelas' => $guru->wali_kelas, 'text' => $text, 'reading' => serialize([])];
        $insert = $this->master->create('kelas_catatan_wali', $data);
        $this->output_json($insert);
    }
    public function saveCatatanSiswa()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_siswa = $this->input->post('id_siswa');
        $text = $this->input->post('text', true);
        $level = $this->input->post('level', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '2', 'level' => $level, 'id_kelas' => $guru->wali_kelas, 'id_siswa' => $id_siswa, 'text' => $text, 'reading' => serialize([])];
        $insert = $this->master->create('kelas_catatan_wali', $data);
        $this->output_json($insert);
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

## File: application/controllers_progress/Walisiswa.php

```php
<?php

class Walisiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
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
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $kelas = $this->master->getKelasById($guru->wali_kelas);
        $data = ['user' => $user, 'judul' => 'Daftar Siswa', 'subjudul' => 'Siswa Kelas ' . $kelas->nama_kelas, 'setting' => $this->dashboard->getSetting()];
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['siswas'] = $this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $guru->wali_kelas, 0, 0);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/kelas');
        $this->load->view('members/guru/templates/footer');
    }
    public function dataKelas()
    {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->output_json($this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $guru->wali_kelas), false);
    }
    public function list()
    {
        $page = $this->input->post('page', true);
        $limit = $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $id_kelas = $this->input->post('kelas', true);
        $offset = ($page - 1) * $limit;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $count_siswa = $this->master->getDataSiswaByKelasPage($tp->id_tp, $smt->id_smt, $id_kelas, $search);
        $lists = $this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $id_kelas, $offset, $limit, $search);
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit];
        $this->output_json($data);
    }
    public function edit($id)
    {
        $siswa = $this->master->getSiswaById($id);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $siswa->nama, 'icon' => 'far fa-user', 'req' => 'required', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $siswa->nis, 'icon' => 'far fa-id-card', 'req' => 'required', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $siswa->nisn, 'icon' => 'far fa-id-card', 'req' => '', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $siswa->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'req' => 'required', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $siswa->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'req' => 'required', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $siswa->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'req' => 'required', 'class' => 'tahun', 'type' => 'text'], ['name' => 'sekolah_asal', 'label' => 'Sekolah Asal', 'value' => $siswa->sekolah_asal, 'icon' => 'fas fa-graduation-cap', 'req' => '', 'class' => '', 'type' => 'text']];
        $inputBio = [['name' => 'status_keluarga', 'label' => 'Status dalam Keluarga', 'value' => $siswa->status_keluarga == '' ? '1' : $siswa->status_keluarga, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['name' => 'anak_ke', 'label' => 'Anak ke', 'value' => $siswa->anak_ke, 'icon' => 'far fa-user', 'class' => '', 'type' => 'number'], ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $siswa->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $siswa->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $siswa->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $siswa->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $siswa->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $siswa->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $siswa->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $siswa->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $siswa->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'provinsi', 'label' => 'Provinsi', 'value' => $siswa->provinsi, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $siswa->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $siswa->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $siswa->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $siswa->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $siswa->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $siswa->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $siswa->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $siswa->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $siswa->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $siswa->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $siswa->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $siswa->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $siswa->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $siswa->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $siswa->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $siswa->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $siswa->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Edit Data Siswa', 'siswa' => $siswa, 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/edit');
        $this->load->view('members/guru/templates/footer');
    }
    public function updateData()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $siswa = $this->master->getSiswaById($id_siswa);
        $u_nis = $siswa->nis === $nis ? '' : '|is_unique[master_siswa.nis]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        if ($this->form_validation->run() == FALSE) {
            $data['insert'] = false;
            $data['text'] = 'Data Sudah ada, Pastikan NIS, dan NISN belum digunakan siswa lain';
        } else {
            $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->input->post('tanggal_lahir', true), 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => 'uploads/foto_siswa/' . $nis . '.jpg'];
            $action = $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
            $data['insert'] = $input;
            $data['text'] = 'Siswa berhasil diperbaharui';
        }
        $this->output_json($data);
    }
    function uploadFile($id_siswa)
    {
        $siswa = $this->master->getSiswaById($id_siswa);
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/foto_siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = $siswa->nis;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile($id_siswa)
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!($file_name != 'assets/img/siswa.png')) {
        } else {
            if (!unlink($file_name)) {
            }
            $this->db->set('foto', '');
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            echo 'File Delete Successfully';
        }
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
            }
            $this->master->delete('buku_induk', $chk, 'id_siswa');
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
}
```

---

## File: application/controllers_progress/Walistruktur.php

```php
<?php

class Walistruktur extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
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
        $data = ['user' => $user, 'judul' => 'Struktur Organisasi', 'subjudul' => 'Struktur Organisasi', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $struktur = $this->kelas->getStrukturKelas($guru->wali_kelas);
        if ($struktur == null) {
            $data['struktur'] = json_decode(json_encode($this->kelas->dummyStruktur()));
        } else {
            $data['struktur'] = $struktur;
        }
        $data['guru'] = $guru;
        $data['gurus'] = $this->dropdown->getAllGuru();
        $siswa = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $siswas[''] = 'Pilih Siswa';
        foreach ($siswa as $key => $value) {
            $siswas[$value->id_siswa] = $value->nama;
        }
        $data['siswas'] = $siswas;
        $data['id_kelas'] = $guru->wali_kelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/struktur');
        $this->load->view('members/guru/templates/footer');
    }
    public function save()
    {
        $data = ['id_kelas' => $this->input->post('id_kelas'), 'ketua' => $this->input->post('ketua'), 'wakil_ketua' => $this->input->post('wakil_ketua'), 'sekretaris_1' => $this->input->post('sekretaris_1'), 'sekretaris_2' => $this->input->post('sekretaris_2'), 'bendahara_1' => $this->input->post('bendahara_1'), 'bendahara_2' => $this->input->post('bendahara_2'), 'sie_ekstrakurikuler' => $this->input->post('sie_ekstrakurikuler'), 'sie_upacara' => $this->input->post('sie_upacara'), 'sie_olahraga' => $this->input->post('sie_olahraga'), 'sie_keagamaan' => $this->input->post('sie_keagamaan'), 'sie_keamanan' => $this->input->post('sie_keamanan'), 'sie_ketertiban' => $this->input->post('sie_ketertiban'), 'sie_kebersihan' => $this->input->post('sie_kebersihan'), 'sie_keindahan' => $this->input->post('sie_keindahan'), 'sie_kesehatan' => $this->input->post('sie_kesehatan'), 'sie_kekeluargaan' => $this->input->post('sie_kekeluargaan'), 'sie_humas' => $this->input->post('sie_humas')];
        $insert = $this->db->replace('kelas_struktur', $data);
        $this->output_json($insert);
    }
}
```

---
