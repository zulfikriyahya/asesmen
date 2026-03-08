<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Userguru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error(
                'Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
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
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    public function data()
    {
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->users->getUserGuru($tp->id_tp, $smt->id_smt), false);
    }

    public function index()
    {
        $user  = $this->ion_auth->user()->row();
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;

        $data = [
            'user'     => $user,
            'judul'    => 'User Management',
            'subjudul' => 'Data User Guru',
            'profile'  => $this->dashboard->getProfileAdmin($user->id),
            'setting'  => $this->dashboard->getSetting(),
            'tp'       => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];

        if ($group === 'admin') {
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

        if (!$guru) {
            $this->output_json(['status' => false, 'msg' => 'Data guru tidak ditemukan.']);
            return;
        }

        $nama       = explode(' ', $guru->nama_guru ?? '');
        $first_name = $nama[0];
        $last_name  = count($nama) > 1 ? end($nama) : $nama[0];
        $username   = trim($guru->username ?? '');
        $password   = trim($guru->password ?? '');
        $email      = strtolower($username) . '@guru.com';

        if ($this->ion_auth->username_check($username)) {
            $this->output_json(['status' => false, 'msg' => 'Username ' . $username . ' tidak tersedia (sudah digunakan).']);
            return;
        }

        if ($this->ion_auth->email_check($email)) {
            $this->ion_auth->delete_user(
                $this->db->get_where('users', ['email' => $email])->row()->id
            );
        }

        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, ['2']);

        if (!$id_user) {
            $this->output_json(['status' => false, 'msg' => 'Gagal mengaktifkan akun guru.']);
            return;
        }

        $this->db->set('id_user', $id_user)->where('id_guru', $id)->update('master_guru');

        $this->output_json(['status' => true, 'msg' => 'Akun ' . $guru->nama_guru . ' diaktifkan.', 'pass' => $password]);
    }

    public function deactivate($id = null)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'msg' => 'You must be an administrator to view this page.']);
            return;
        }

        $id   = (int) $id;
        $guru = $this->users->getDataGuru($id);

        if (!$guru || empty($guru->username)) {
            $this->output_json(['status' => false, 'msg' => 'Data guru tidak ditemukan.']);
            return;
        }

        $email = strtolower(trim($guru->username)) . '@guru.com';
        $user  = $this->db->get_where('users', ['email' => $email])->row();

        if (!$user) {
            $this->output_json(['status' => false, 'msg' => 'Akun user tidak ditemukan.']);
            return;
        }

        $deleted = $this->ion_auth->delete_user($user->id);
        $this->output_json(['status' => (bool) $deleted, 'msg' => $deleted ? 'Akun guru dinonaktifkan.' : 'Gagal menonaktifkan akun.']);
    }

    public function aktifkanSemua()
    {
        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;

        foreach ($guruAktif as $guru) {
            if ($guru->aktif == 0) {
                $this->activate($guru->id_guru);
                $jum++;
            }
        }

        $this->output_json(['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru diaktifkan.']);
    }

    public function nonaktifkanSemua()
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'msg' => 'You must be an administrator to view this page.']);
            return;
        }

        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;

        foreach ($guruAktif as $guru) {
            if ($guru->aktif > 0) {
                $this->deactivate($guru->id_guru);
                $jum++;
            }
        }

        $this->output_json(['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru dinonaktifkan.']);
    }

    public function edit($id)
    {
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->users->getDetailGuru($id);
        $user = $this->ion_auth->user()->row();

        $data = [
            'user'       => $user,
            'judul'      => 'User Management',
            'subjudul'   => 'Edit Data User',
            'setting'    => $this->dashboard->getSetting(),
            'users'      => $this->users->getUsers($guru->username),
            'guru'       => $guru,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
        ];

        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;

        if ($group === 'admin') {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['groups']  = $this->ion_auth->groups()->result();
            $data['kelass']  = $this->users->getKelas($tp->id_tp, $smt->id_smt);
            $data['mapels']  = $this->users->getMapel();
            $data['levels']  = $this->users->getLevelGuru();
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
        $id_guru  = $this->input->post('id_guru', true);
        $username = $this->input->post('username', true);
        $min_length = $this->config->item('min_password_length', 'ion_auth');

        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $min_length . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        $guru_lain = $this->master->getUserIdGuruByUsername($username);
        if ($guru_lain && $guru_lain->id_guru != $id_guru) {
            $this->output_json(['status' => false, 'errors' => ['username' => 'Username sudah digunakan']]);
            return;
        }

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

        $guru     = $this->db->get_where('master_guru', ['id_guru' => $id_guru])->row();
        $nama     = explode(' ', $guru->nama_guru ?? '');
        $username = trim($username ?? '');
        $password = trim($this->input->post('new', true) ?? '');
        $email    = strtolower($username) . '@guru.com';

        $additional_data = [
            'first_name' => $nama[0],
            'last_name'  => end($nama),
        ];

        $user_guru = $this->db->get_where('users', ['email' => $email])->row();
        if ($user_guru) {
            $this->ion_auth->delete_user((int) $user_guru->id);
        }

        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, ['2']);

        if ($id_user) {
            $this->db->set('id_user', $id_user)->where('id_guru', $id_guru)->update('master_guru');
            $this->output_json(['status' => true, 'text' => 'Username/password berhasil diubah.']);
        } else {
            $this->output_json(['status' => false, 'text' => 'Gagal mengganti username/password.']);
        }
    }

    public function reset_login()
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'msg' => 'You must be an administrator to view this page.']);
            return;
        }

        $username = $this->input->get('username', true);

        $this->db->where('login', $username);
        $deleted = $this->db->delete('login_attempts');

        $this->output_json([
            'status' => (bool) $deleted,
            'msg'    => $deleted ? 'Login attempts berhasil direset.' : $username . ' gagal direset.',
        ]);
    }

    private function registerGuru($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

        return [
            'status' => (bool) $reg,
            'id'     => $reg ?: null,
        ];
    }
}
