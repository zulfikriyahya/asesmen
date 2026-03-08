<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataguru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
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
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $mode = $this->input->get('mode', true);
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Guru', 'subjudul' => 'Data Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['mode'] = $mode == null ? '1' : '2';
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $mapels = $this->master->getAllMapel();
        $ret = [];
        if (!$mapels) {
        }
        foreach ($mapels as $key => $row) {
            $ret[$row->id_mapel] = $row;
        }
        $data['mapels'] = $ret;
        $data['extras'] = $this->dropdown->getAllKodeEkskul();
        $data['kelass'] = $this->master->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['gurus'] = $this->master->getAllDataGuru($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->master->getDataGuru($tp->id_tp, $smt->id_smt), false);
    }
    public function edit($id)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $guru = $this->master->getGuruById($id, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Edit Guru', 'subjudul' => 'Edit Data Guru', 'mapel' => $this->master->getAllMapel(), 'guru' => $guru, 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['id_active'] = $id;
        $inputsProfile = [['label' => 'Nama Lengkap', 'name' => 'nama_guru', 'value' => $guru->nama_guru, 'icon' => 'far fa-user', 'type' => 'text'], ['label' => 'Email', 'name' => 'email', 'value' => $guru->email, 'icon' => 'far fa-envelope', 'type' => 'text'], ['label' => 'NIP / NUPTK', 'name' => 'nip', 'value' => $guru->nip, 'icon' => 'far fa-id-card', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $guru->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'type' => 'text'], ['label' => 'No. Handphone', 'name' => 'no_hp', 'value' => $guru->no_hp, 'icon' => 'fa fa-phone', 'type' => 'number'], ['label' => 'Agama', 'name' => 'agama', 'value' => $guru->agama, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputsAlamat = [['label' => 'NIK', 'name' => 'no_ktp', 'value' => $guru->no_ktp, 'icon' => 'far fa-id-card', 'type' => 'number'], ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'value' => $guru->tempat_lahir, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Tgl. Lahir', 'name' => 'tgl_lahir', 'value' => $guru->tgl_lahir, 'icon' => 'fa fa-calendar', 'type' => 'text'], ['label' => 'Alamat', 'name' => 'alamat_jalan', 'value' => $guru->alamat_jalan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kecamatan', 'name' => 'kecamatan', 'value' => $guru->kecamatan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kota/Kab.', 'name' => 'kabupaten', 'value' => $guru->kabupaten, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Provinsi', 'name' => 'provinsi', 'value' => $guru->provinsi, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kode Pos', 'name' => 'kode_pos', 'value' => $guru->kode_pos, 'icon' => 'fa fa-envelope', 'type' => 'number']];
        $data['input_profile'] = json_decode(json_encode($inputsProfile), FALSE);
        $data['input_alamat'] = json_decode(json_encode($inputsAlamat), FALSE);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/edit');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $this->load->model('Master_model', 'master');
        $nip = $this->input->post('nip', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $u_nip = 'is_unique[master_guru.nip]';
        $u_username = '|is_unique[master_guru.username]';
        $this->form_validation->set_rules('nip', 'NIP', 'required|numeric|trim|' . $u_nip);
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim' . $u_username);
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == FALSE) {
        }
        $input = ['nip' => trim($nip ?? ''), 'nama_guru' => trim($nama_guru ?? ''), 'username' => trim($username ?? ''), 'password' => trim($password ?? ''), 'foto' => 'uploads/profiles/' . trim($nip ?? '00') . '.jpg'];
        $action = $this->master->create('master_guru', $input);
        if ($action) {
        }
        $this->output_json(['status' => false]);
    }
    public function save()
    {
        $this->load->model('Master_model', 'master');
        $method = $this->input->post('method', true);
        $id_guru = $this->input->post('id_guru', true);
        $nip = $this->input->post('nip', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $email = $this->input->post('email', true);
        $mapel = $this->input->post('password', true);
        if ($method == 'add') {
        }
        $dbdata = $this->master->getGuruById($id_guru);
        $u_nip = $dbdata->nip === $nip ? '' : '|is_unique[guru.nip]';
        $u_email = $dbdata->email === $email ? '' : '|is_unique[guru.email]';
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[8]' . $u_nip);
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $u_email);
        $this->form_validation->set_rules('mapel', 'Mata Kuliah', 'required');
        if ($this->form_validation->run() == FALSE) {
        }
        $input = ['nip' => $nip, 'nama_guru' => $nama_guru, 'email' => $email, 'mapel_id' => $mapel];
        if ($method === 'add') {
        }
        if (!($method === 'edit')) {
        }
        $action = $this->master->update('master_guru', $input, 'id_guru', $id_guru);
        if ($action) {
        }
        $this->output_json(['status' => false]);
    }
    public function deleteGuru()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('id_guru', true);
        $messages = [];
        $tables = [];
        $tabless = $this->db->list_tables();
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_guru' || $field->name == 'guru_id')) {
                }
                array_push($tables, $table);
            }
        }
        foreach ($tables as $table) {
            if (!($table != 'master_guru')) {
            }
            if ($table == 'master_kelas') {
            }
            $this->db->where('id_guru', $chk);
            $num = $this->db->count_all_results($table);
            if (!($num > 0)) {
            }
            array_push($messages, $table);
        }
        if (count($messages) > 0) {
        }
        $data['status'] = $this->master->delete('master_guru', $chk, 'id_guru');
        $this->output_json($data);
    }
    public function detail($id_guru)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Detail Guru', 'subjudul' => 'Info Jabatan Guru', 'mapel' => $this->master->getAllMapel(), 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['kelas'] = $this->master->getAllKelas();
        $data['id_guru'] = $id_guru;
        $data['guru'] = ['detail' => $this->master->getGuruByArrId([$id_guru])[0], 'jabatan' => $this->master->getDetailJabatanGuru($id_guru), 'materi' => $this->db->get_where('kelas_materi', 'id_guru=' . $id_guru)->num_rows(), 'catatan_mapel' => $this->db->get_where('kelas_catatan_mapel', 'id_guru=' . $id_guru)->num_rows(), 'bank_soal' => $this->db->get_where('cbt_bank_soal', 'bank_guru_id=' . $id_guru)->num_rows(), 'pengawas' => $this->db->get_where('cbt_pengawas', 'id_guru LIKE "%' . $id_guru . '%"')->num_rows(), 'posts' => $this->db->get_where('post', 'dari=' . $id_guru)->num_rows(), 'comments' => $this->db->get_where('post_comments', 'dari=' . $id_guru)->num_rows(), 'replies' => $this->db->get_where('post_reply', 'dari=' . $id_guru)->num_rows()];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/detail');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function delete()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('checked', true);
        if (!$chk) {
        }
        if (!$this->master->delete('master_guru', $chk, 'id_guru')) {
        }
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
    public function forceDelete()
    {
        $this->load->model('Master_model', 'master');
        $id_guru = $this->input->post('id_guru', true);
        $data['status'] = $this->master->delete('master_guru', $id_guru, 'id_guru');
        $this->output_json($data);
    }
    public function create_user()
    {
        $this->load->model('Master_model', 'master');
        $id = $this->input->get('id', true);
        $data = $this->master->getGuruById($id);
        $nama = explode(' ', $data->nama_guru ?? '');
        $first_name = $nama[0];
        $last_name = end($nama);
        $username = $data->nip;
        $password = $data->nip;
        $email = $data->email;
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('2');
        if ($this->ion_auth->username_check($username)) {
        }
        if ($this->ion_auth->email_check($email)) {
        }
        $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data = ['status' => true, 'msg' => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'];
        $this->output_json($data);
    }
    public function import($import_data = null)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Guru', 'subjudul' => 'Tambah Data Guru', 'mapel' => $this->master->getAllMapel(), 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        if (!($import_data != null)) {
        }
        $data['import'] = $import_data;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/add');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function do_import()
    {
        $this->load->model('Master_model', 'master');
        $input = $this->input->post('guru', true);
        $errors = [];
        foreach ($input as $guru) {
            $this->form_validation->set_data($guru);
            $this->form_validation->set_rules('2', 'Nama Guru', 'required|trim|min_length[1]|max_length[50]');
            $this->form_validation->set_rules('3', 'NIP', 'required|trim|min_length[6]|max_length[30]|is_unique[master_guru.nip]');
            $this->form_validation->set_rules('5', 'Username', 'required|trim|min_length[3]|max_length[30]|is_unique[master_guru.username]');
            $this->form_validation->set_rules('6', 'Password', 'required|trim|min_length[5]|max_length[30]');
            if (!($this->form_validation->run() == FALSE)) {
            }
            $errors[] = ['nama' => form_error('2'), 'nip' => form_error('3'), 'username' => form_error('5'), 'password' => form_error('6')];
        }
        if (count($errors) > 0) {
        }
        $data_insert = [];
        foreach ($input as $guru) {
            $foto = 'uploads/profiles/' . trim($guru['3'] ?? '00') . '.jpg';
            if (!isset($guru['7'])) {
            }
            $base64_image_string = $guru['7'];
            $extension = $guru['8'];
            if (!($extension == 'jpeg')) {
            }
            $extension = 'jpg';
            $output_file = trim($guru['3'] ?? '00') . '.' . $extension;
            file_put_contents('./uploads/profiles/' . $output_file, base64_decode($base64_image_string));
            $foto = 'uploads/profiles/' . $output_file;
            $data_insert[] = ['nama_guru' => trim($guru['2'] ?? ''), 'nip' => trim($guru['3'] ?? ''), 'kode_guru' => trim($guru['4'] ?? ''), 'username' => trim($guru['5'] ?? ''), 'password' => trim($guru['6'] ?? ''), 'foto' => $foto];
        }
        $save = $this->master->create('master_guru', $data_insert, true);
        $data = ['status' => true, 'data' => $save, 'insert' => $data_insert];
        $this->output_json($data);
    }
    public function editJabatan($id)
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->master->getJabatanGuru($id, $tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Jabatan Guru', 'subjudul' => 'Edit Jabatan Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        if (!($group === 'admin')) {
        }
        $data['groups'] = $this->ion_auth->groups()->result();
        $data['kelass'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['mapels'] = $this->dropdown->getAllMapel();
        $data['levels'] = $this->dropdown->getAllLevelGuru();
        $data['ekskul'] = $this->dropdown->getAllEkskul();
        $data['kur'] = $smt;
        $smt2 = $smt->id_smt == '1' ? '2' : '1';
        $tp2 = $smt->id_smt == '1' ? $tp->id_tp - 1 : $tp->id_tp;
        $guru_before = $this->master->getJabatanGuru($id, $tp2, $smt2);
        $guru_before->mapel_kelas = json_decode(json_encode(unserialize($guru_before->mapel_kelas ?? '')));
        $guru_before->ekstra_kelas = json_decode(json_encode(unserialize($guru_before->ekstra_kelas ?? '')));
        $data['before'] = ['kelass' => $this->dropdown->getAllKelas($tp2, $smt2), 'guru' => $guru_before];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/editmapel');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function saveJabatan()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model', 'kelas');
        $id_guru = $this->input->post('id_guru', true);
        $id_level = $this->input->post('level', true);
        $wali = $this->input->post('kelas_wali', true);
        $copy = $this->input->post('copy', true) != null;
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $smt2 = $smt->id_smt == '1' ? '2' : '1';
        $tp2 = $smt->id_smt == '1' ? $tp->id_tp - 1 : $tp->id_tp;
        $kelass1 = $this->kelas->getNamaKelasByNama($tp->id_tp, $smt->id_smt);
        $kelass2 = $this->dropdown->getAllKelas($tp2, $smt2);
        if ($copy) {
            $tmp_wali = $kelass2[$wali];
            $kelas_wali = $kelass1[$tmp_wali];
            $mapels = [];
            $check_mapel = $this->input->post('mapel', true);
            if (!$check_mapel) {
            }
            $row_mapels = count($this->input->post('mapel', true));
            $i = 0;
            if (!($i <= $row_mapels)) {
            }
            $mapel = $this->input->post('mapel[' . $i . ']', true);
            $nama_mapel = $this->input->post('nama_mapel' . $mapel, true);
            $check = $this->input->post('kelasmapel' . $mapel, true);
            if (!$check) {
            }
            $row_kelas = count($this->input->post('kelasmapel' . $mapel, true));
            $kelas = [];
            $j = 0;
            if (!($j <= $row_kelas)) {
            }
            $kelasmapel = $this->input->post('kelasmapel' . $mapel . '[' . $j . ']', true);
            if ($copy) {
            }
            $kelas[] = ['kelas' => $kelasmapel];
            $j++;
        } else {
            $kelas_wali = $wali;
            $mapels = [];
            $check_mapel = $this->input->post('mapel', true);
            if (!$check_mapel) {
            }
            $row_mapels = count($this->input->post('mapel', true));
            $i = 0;
            if (!($i <= $row_mapels)) {
            }
            $mapel = $this->input->post('mapel[' . $i . ']', true);
            $nama_mapel = $this->input->post('nama_mapel' . $mapel, true);
            $check = $this->input->post('kelasmapel' . $mapel, true);
            if (!$check) {
            }
            $row_kelas = count($this->input->post('kelasmapel' . $mapel, true));
            $kelas = [];
            $j = 0;
            if (!($j <= $row_kelas)) {
            }
            $kelasmapel = $this->input->post('kelasmapel' . $mapel . '[' . $j . ']', true);
            if ($copy) {
            }
            $kelas[] = ['kelas' => $kelasmapel];
            $j++;
        }
    }
    public function getDataKelas()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Users_model', 'users');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $jabatans = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        $mapel_terisi = [];
        $ekstra_terisi = [];
        $jbtn = [];
        foreach ($jabatans as $jabatan) {
            $mpl_kls = $jabatan->mapel_kelas = json_decode(json_encode(unserialize($jabatan->mapel_kelas ?? '')));
            $eks_kls = $jabatan->ekstra_kelas = json_decode(json_encode(unserialize($jabatan->ekstra_kelas ?? '')));
            foreach ($mpl_kls as $mpls) {
                $klss = [];
                foreach ($mpls->kelas_mapel as $mpl) {
                    $klss[] = $mpl->kelas;
                }
                $mapel_terisi[$mpls->id_mapel][$jabatan->id_guru] = ['id_guru' => $jabatan->id_guru, 'guru' => $jabatan->nama_guru, 'kelas' => $klss];
            }
            foreach ($eks_kls as $eks) {
                $klse = [];
                foreach ($eks->kelas_ekstra as $ek) {
                    $klse[] = $ek->kelas;
                }
                $ekstra_terisi[$eks->id_ekstra][$jabatan->id_guru] = ['id_guru' => $jabatan->id_guru, 'guru' => $jabatan->nama_guru, 'kelas' => $klse];
            }
            $jbtn[$jabatan->id_jabatan][$jabatan->id_kelas] = ['nama' => $jabatan->nama_guru, 'id' => $jabatan->id_guru];
        }
        $data['jabatan'] = $jbtn;
        $data['mpl_terisi'] = $mapel_terisi;
        $data['eks_terisi'] = $ekstra_terisi;
        $data['kelas'] = $this->users->getKelas($tp->id_tp, $smt->id_smt);
        $this->output_json($data);
    }
    public function addjabatan()
    {
        $mode = $this->input->post('mode', true);
        $id = $this->input->post('id_level', true);
        $s_mode = $mode == '1' ? 'menyimpan' : 'menghapus';
        if ($mode == '1') {
        }
        $replaced = $this->db->delete('level_guru', 'id_level=' . $id);
        $data = ['success' => $replaced, 'msg' => $replaced ? 'Sukses ' . $s_mode . ' jabatan' : 'Gagal ' . $s_mode . ' jabatan'];
        $this->output_json($data);
    }
}