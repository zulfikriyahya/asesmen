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
        if ($group !== 'admin' && $group !== 'guru') {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
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
        $page   = $this->input->post('page', true);
        $limit  = $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $offset = ($page - 1) * $limit;
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();

        $count_siswa = $this->users->getUserSiswaTotalPage($search);
        $lists       = $this->users->getUserSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $search);

        $this->output_json([
            'lists'   => $lists,
            'total'   => $count_siswa,
            'pages'   => ceil($count_siswa / $limit),
            'search'  => $search,
            'perpage' => $limit,
        ]);
    }

    private function registerSiswa($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        if ($reg) {
            return ['status' => true, 'id' => $reg];
        }
        return ['status' => false, 'id' => null];
    }

    private function aktifkan($siswa)
    {
        $nama       = explode(' ', $siswa->nama ?? '');
        $first_name = $nama[0];
        $last_name  = end($nama);
        $username   = trim($siswa->username ?? '');
        $password   = trim($siswa->password ?? '');
        $email      = $siswa->nis . '@siswa.com';

        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group           = ['3'];

        $user_siswa = $this->db->get_where('users', ['email' => $email])->row();
        if ($user_siswa != null) {
            $this->ion_auth->delete_user($user_siswa->id);
        }

        $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        if ($id_user) {
            $this->db->set('id_user', $id_user)->where('id_siswa', $siswa->id_siswa)->update('master_siswa');
            return ['status' => true, 'msg' => 'Akun ' . $siswa->nama . ' berhasil diaktifkan.'];
        }
        return ['status' => false, 'msg' => 'Gagal mengaktifkan akun siswa.'];
    }

    public function activate($id)
    {
        $siswa = $this->users->getDataSiswa($id);
        $data  = $this->aktifkan($siswa);
        $this->output_json($data);
    }

    public function aktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum        = 0;
        foreach ($siswaAktif as $siswa) {
            if ($siswa->aktif == 0) {
                $result = $this->aktifkan($siswa);
                if ($result['status']) {
                    $jum++;
                }
            }
        }
        $this->output_json(['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa diaktifkan.']);
    }

    private function nonaktifkan($user, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            return ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        }
        $deleted = $this->ion_auth->delete_user((int) $user->id);
        return $deleted
            ? ['status' => true, 'msg' => 'Akun ' . $nama . ' berhasil dinonaktifkan.']
            : ['status' => false, 'msg' => 'Gagal menonaktifkan akun ' . $nama . '.'];
    }

    public function deactivate($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $user = $this->users->getUsers($username);
            $data = $this->nonaktifkan($user, $nama);
        }
        $this->output_json($data);
    }

    public function reset_login($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $deleted = $this->db->where('login', $username)->delete('login_attempts');
            $data    = $deleted
                ? ['status' => true, 'msg' => 'Login attempts ' . $nama . ' berhasil direset.']
                : ['status' => false, 'msg' => 'User ' . $nama . ' gagal direset.'];
        }
        $this->output_json($data);
    }

    public function nonaktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum        = 0;
        foreach ($siswaAktif as $siswa) {
            if ($siswa->aktif > 0) {
                $result = $this->nonaktifkan($siswa, $siswa->nama);
                if ($result['status']) {
                    $jum++;
                }
            }
        }
        $this->output_json(['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa dinonaktifkan.']);
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
            $guru         = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
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
        $oldPass  = $this->input->post('old', true);
        $newPass  = $this->input->post('new', true);

        $this->form_validation->set_rules('username', 'Username', 'required|numeric|trim|min_length[6]|is_unique[master_siswa.username]');
        $this->form_validation->set_rules('old', 'Password Lama', 'required|numeric|trim|min_length[6]');
        $this->form_validation->set_rules('new', 'Password Baru', 'required|numeric|trim|min_length[6]');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'status' => false,
                'errors' => [
                    'username' => form_error('username'),
                    'old'      => form_error('old'),
                    'new'      => form_error('new'),
                ],
            ];
        } else {
            $update = $this->master->update('master_siswa', ['username' => $username, 'password' => $newPass], 'id_siswa', $id_siswa);
            $data   = ['status' => (bool) $update, 'msg' => $update ? 'Data berhasil diperbarui.' : 'Gagal memperbarui data.'];
        }
        $this->output_json($data);
    }

    public function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

        if ($this->form_validation->run() === FALSE) {
            $data = [
                'status' => false,
                'errors' => [
                    'old'         => form_error('old'),
                    'new'         => form_error('new'),
                    'new_confirm' => form_error('new_confirm'),
                ],
            ];
        } else {
            $identity = $this->session->userdata('identity');
            $change   = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            $data     = $change
                ? ['status' => true, 'msg' => 'Password berhasil diubah.']
                : ['status' => false, 'msg' => $this->ion_auth->errors()];
        }
        $this->output_json($data);
    }

    public function delete($id)
    {
        $this->is_has_access();
        $data['status'] = (bool) $this->ion_auth->delete_user($id);
        $this->output_json($data);
    }

    private function hash_password($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
