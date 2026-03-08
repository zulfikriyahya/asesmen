<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Usersiswa extends CI_Controller
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
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }

    public function is_has_access()
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $group   = $this->ion_auth->get_users_groups($user_id)->row()->name;

        if (!in_array($group, ['admin', 'guru'])) {
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

    public function data()
    {
        $this->is_has_access();
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->users->getUserSiswa($tp->id_tp, $smt->id_smt), false);
    }

    public function index()
    {
        $this->is_has_access();
        $user = $this->ion_auth->user()->row();
        $data = [
            'user'       => $user,
            'judul'      => 'User Management',
            'subjudul'   => 'Data User Siswa',
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('users/siswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function list()
    {
        $page   = (int) $this->input->post('page', true);
        $limit  = (int) $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $offset = ($page - 1) * $limit;

        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $count = $this->users->getUserSiswaTotalPage($search);
        $lists = $this->users->getUserSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $search);

        $this->output_json([
            'lists'   => $lists,
            'total'   => $count,
            'pages'   => ceil($count / $limit),
            'search'  => $search,
            'perpage' => $limit,
        ]);
    }

    private function aktifkan($siswa)
    {
        $nama       = explode(' ', $siswa->nama ?? '');
        $first_name = $nama[0];
        $last_name  = end($nama);
        $username   = trim($siswa->username ?? '');
        $password   = trim($siswa->password ?? '');
        $email      = ($siswa->nis ?? $username) . '@siswa.com';

        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];

        $user_siswa = $this->db->get_where('users', ['email' => $email])->row();
        if ($user_siswa) {
            $this->ion_auth->delete_user($user_siswa->id);
        }

        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, ['3']);

        if (!$id_user) {
            return ['status' => false, 'msg' => 'Gagal mengaktifkan akun siswa.'];
        }

        $this->db->set('id_user', $id_user)->where('id_siswa', $siswa->id_siswa ?? $siswa->id)->update('master_siswa');

        return ['status' => true, 'msg' => 'Akun ' . $siswa->nama . ' diaktifkan.'];
    }

    public function activate($id)
    {
        $siswa = $this->users->getDataSiswa($id);

        if (!$siswa) {
            $this->output_json(['status' => false, 'msg' => 'Data siswa tidak ditemukan.']);
            return;
        }

        $data = $this->aktifkan($siswa);
        $this->output_json($data);
    }

    public function aktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum = 0;

        foreach ($siswaAktif as $siswa) {
            if ($siswa->aktif == 0) {
                $this->aktifkan($siswa);
                $jum++;
            }
        }

        $this->output_json(['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa diaktifkan.']);
    }

    private function nonaktifkan($user, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            return ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        }

        if (!$user) {
            return ['status' => false, 'msg' => 'User ' . $nama . ' tidak ditemukan.'];
        }

        $deleted = $this->ion_auth->delete_user($user->id);

        return [
            'status' => (bool) $deleted,
            'msg'    => $deleted ? 'Akun ' . $nama . ' dinonaktifkan.' : 'Gagal menonaktifkan ' . $nama . '.',
        ];
    }

    public function deactivate($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'msg' => 'You must be an administrator to view this page.']);
            return;
        }

        $user = $this->users->getUsers($username);
        $data = $this->nonaktifkan($user, $nama);
        $this->output_json($data);
    }

    public function nonaktifkanSemua()
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'msg' => 'You must be an administrator to view this page.']);
            return;
        }

        $siswaAktif = $this->users->getSiswaAktif();
        $jum = 0;

        foreach ($siswaAktif as $siswa) {
            if ($siswa->aktif > 0) {
                $user = $this->users->getUsers($siswa->username);
                $this->nonaktifkan($user, $siswa->nama);
                $jum++;
            }
        }

        $this->output_json(['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa dinonaktifkan.']);
    }

    public function reset_login($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $this->output_json(['status' => false, 'msg' => 'You must be an administrator to view this page.']);
            return;
        }

        $this->db->where('login', $username);
        $deleted = $this->db->delete('login_attempts');

        $this->output_json([
            'status' => (bool) $deleted,
            'msg'    => $deleted ? 'User ' . $nama . ' berhasil direset.' : 'User ' . $nama . ' gagal direset.',
        ]);
    }

    public function edit($id)
    {
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $siswa = $this->master->getDataSiswaById($tp->id_tp, $smt->id_smt, $id);
        $user  = $this->ion_auth->user()->row();

        $data = [
            'user'       => $user,
            'judul'      => 'User Management',
            'subjudul'   => 'Edit Data User',
            'setting'    => $this->dashboard->getSetting(),
            'siswa'      => $siswa,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
        ];

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
        $this->is_has_access();
        $this->output_json(['status' => (bool) $this->ion_auth->delete_user($id)]);
    }

    private function registerSiswa($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);

        return [
            'status' => (bool) $reg,
            'id'     => $reg ?: null,
        ];
    }
}
