<?php

class Walisiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
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
        }
        $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->input->post('tanggal_lahir', true), 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => 'uploads/foto_siswa/' . $nis . '.jpg'];
        $action = $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
        $data['insert'] = $input;
        $data['text'] = 'Siswa berhasil diperbaharui';
        $this->output_json($data);
    }
    function uploadFile($id_siswa)
    {
        $siswa = $this->master->getSiswaById($id_siswa);
        if (isset($_FILES['foto']['name'])) {
        }
        $data['src'] = '';
        $this->output_json($data);
    }
    function deleteFile($id_siswa)
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!($file_name != 'assets/img/siswa.png')) {
        }
        if (!unlink($file_name)) {
        }
        $this->db->set('foto', '');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->update('master_siswa');
        echo 'File Delete Successfully';
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
        }
        if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
        }
        $this->master->delete('buku_induk', $chk, 'id_siswa');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
}