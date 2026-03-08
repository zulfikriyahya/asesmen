<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dataalumni extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin()) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Rapor_model', 'rapor');
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

    private function buildInputForms($alumni)
    {
        $inputData = [
            ['label' => 'Nama Lengkap',    'name' => 'nama',          'value' => $alumni->nama,          'icon' => 'far fa-user',          'class' => '',     'type' => 'text'],
            ['label' => 'NIS',             'name' => 'nis',           'value' => $alumni->nis,           'icon' => 'far fa-id-card',       'class' => '',     'type' => 'number'],
            ['label' => 'NISN',            'name' => 'nisn',          'value' => $alumni->nisn,          'icon' => 'far fa-id-card',       'class' => '',     'type' => 'text'],
            ['label' => 'Jenis Kelamin',   'name' => 'jenis_kelamin', 'value' => $alumni->jenis_kelamin, 'icon' => 'fas fa-venus-mars',    'class' => '',     'type' => 'text'],
            ['label' => 'Diterima di kelas', 'name' => 'kelas_awal',   'value' => $alumni->kelas_awal,    'icon' => 'fas fa-graduation-cap', 'class' => '',     'type' => 'text'],
            ['label' => 'Tgl diterima',    'name' => 'tahun_masuk',   'value' => $alumni->tahun_masuk,   'icon' => 'far fa-calendar-alt',  'class' => 'tahun', 'type' => 'text'],
        ];
        $inputBio = [
            ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir',    'value' => $alumni->tempat_lahir, 'icon' => 'far fa-map',     'class' => '',     'type' => 'text'],
            ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir',   'value' => $alumni->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'],
            ['name' => 'agama',        'label' => 'Agama',           'value' => $alumni->agama,        'icon' => 'far fa-calendar', 'class' => '',     'type' => 'text'],
            ['name' => 'alamat',       'label' => 'Alamat',          'value' => $alumni->alamat,       'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'rt',           'label' => 'Rt',              'value' => $alumni->rt,           'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'rw',           'label' => 'Rw',              'value' => $alumni->rw,           'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'kelurahan',    'label' => 'Kelurahan/Desa',  'value' => $alumni->kelurahan,    'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'kecamatan',    'label' => 'Kecamatan',       'value' => $alumni->kecamatan,    'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'kabupaten',    'label' => 'Kabupaten/Kota',  'value' => $alumni->kabupaten,    'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'kode_pos',     'label' => 'Kode Pos',        'value' => $alumni->kode_pos,     'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
            ['name' => 'hp',           'label' => 'Hp',              'value' => $alumni->hp,           'icon' => 'far fa-user',    'class' => '',     'type' => 'text'],
        ];
        $inputOrtu = [
            ['name' => 'nama_ayah',        'label' => 'Nama Ayah',        'value' => $alumni->nama_ayah,        'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'pendidikan_ayah',  'label' => 'Pendidikan Ayah',  'value' => $alumni->pendidikan_ayah,  'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'pekerjaan_ayah',   'label' => 'Pekerjaan Ayah',   'value' => $alumni->pekerjaan_ayah,   'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'nohp_ayah',        'label' => 'No. HP Ayah',      'value' => $alumni->nohp_ayah,        'icon' => 'far fa-user', 'type' => 'number'],
            ['name' => 'alamat_ayah',      'label' => 'Alamat Ayah',      'value' => $alumni->alamat_ayah,      'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'nama_ibu',         'label' => 'Nama Ibu',         'value' => $alumni->nama_ibu,         'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'pendidikan_ibu',   'label' => 'Pendidikan Ibu',   'value' => $alumni->pendidikan_ibu,   'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'pekerjaan_ibu',    'label' => 'Pekerjaan Ibu',    'value' => $alumni->pekerjaan_ibu,    'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'nohp_ibu',         'label' => 'No. HP Ibu',       'value' => $alumni->nohp_ibu,         'icon' => 'far fa-user', 'type' => 'number'],
            ['name' => 'alamat_ibu',       'label' => 'Alamat Ibu',       'value' => $alumni->alamat_ibu,       'icon' => 'far fa-user', 'type' => 'text'],
        ];
        $inputWali = [
            ['name' => 'nama_wali',       'label' => 'Nama Wali',       'value' => $alumni->nama_wali,       'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $alumni->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'pekerjaan_wali',  'label' => 'Pekerjaan Wali',  'value' => $alumni->pekerjaan_wali,  'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'alamat_wali',     'label' => 'Alamat Wali',     'value' => $alumni->alamat_wali,     'icon' => 'far fa-user', 'type' => 'text'],
            ['name' => 'nohp_wali',       'label' => 'No. HP Wali',     'value' => $alumni->nohp_wali,       'icon' => 'far fa-user', 'type' => 'number'],
        ];
        return [
            'input_data' => json_decode(json_encode($inputData), false),
            'input_bio'  => json_decode(json_encode($inputBio), false),
            'input_ortu' => json_decode(json_encode($inputOrtu), false),
            'input_wali' => json_decode(json_encode($inputWali), false),
        ];
    }

    private function getLevel($jenjang)
    {
        if ($jenjang == '1') return '6';
        if ($jenjang == '2') return '9';
        if ($jenjang == '3') return '3';
        return '12';
    }

    public function index()
    {
        $tahun = $this->input->get('tahun', true);
        $kelas_akhir = $this->input->get('kelas', true);
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $allTp = $this->dashboard->getTahun();
        $level = $this->getLevel($setting->jenjang);
        $jumlah_lulus = $this->rapor->getJumlahLulus($tp->id_tp - 1, '2', $level);
        $idSearch = array_search($tp->id_tp - 1, array_column($allTp, 'id_tp'));
        $tpBefore = $idSearch !== false ? $allTp[$idSearch]->tahun : '';
        $splitTahun = explode('/', $tpBefore ?? '');
        $alumnis = $this->master->getAlumniByTahun($splitTahun[1] ?? '');
        $data = [
            'user'           => $user,
            'judul'          => 'Data Kelulusan & Alumni',
            'subjudul'       => 'Data Alumni',
            'setting'        => $setting,
            'tp'             => $allTp,
            'tp_active'      => $tp,
            'smt'            => $this->dashboard->getSemester(),
            'smt_active'     => $smt,
            'profile'        => $this->dashboard->getProfileAdmin($user->id),
            'tahun_lulus'    => $this->master->getDistinctTahunLulus(),
            'kelas_akhir'    => $this->master->getDistinctKelasAkhir(),
            'tahun_selected' => $tahun,
            'kelas_selected' => $kelas_akhir,
            'jumlah_lulus'   => $jumlah_lulus > count($alumnis) ? $jumlah_lulus : 0,
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function generateAlumni()
    {
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $allTp = $this->dashboard->getTahun();
        $searchId = array_search('1', array_column($allTp, 'active'));
        $tpBefore = $allTp[$searchId - 1]->tahun ?? '';
        $splitTahun = explode('/', $tpBefore);
        $level = $this->getLevel($setting->jenjang);
        $siswas = $this->rapor->getSiswaLulus($tp->id_tp - 1, '2', $level);
        $ids = [];
        $this->db->trans_start();
        foreach ($siswas as $siswa) {
            if ($siswa->naik === null || $siswa->naik != '0') {
                $ids[] = $siswa->id_siswa;
                $this->db->where('id_siswa', $siswa->id_siswa)
                    ->set('status', '2')
                    ->set('tahun_lulus', $splitTahun[1] ?? '')
                    ->set('no_ijazah', '- -')
                    ->set('kelas_akhir', $siswa->kelas_akhir)
                    ->update('buku_induk');
            }
        }
        $this->db->trans_complete();
        $this->output_json($ids);
    }

    public function luluskan()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $idkelases = [];
        $alumnikelas = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->kelas_baru;
            $alumnikelas[$d->kelas_baru][] = ['id' => $d->id_siswa];
        }
        $idkelases = array_unique($idkelases);
        $res = [];
        foreach ($idkelases as $ik) {
            $kelas = $this->kelas->get_one($ik, $tp->id_tp - 1, '2');
            $kelas_baru = $this->kelas->getKelasByNama($kelas->nama_kelas, $tp->id_tp, $smt->id_smt);
            $jumlah = serialize($alumnikelas[$ik]);
            $kelas_data = [
                'nama_kelas'    => $kelas->nama_kelas,
                'kode_kelas'    => $kelas->kode_kelas,
                'jurusan_id'    => $kelas->jurusan_id,
                'id_tp'         => $tp->id_tp,
                'id_smt'        => $smt->id_smt,
                'level_id'      => $kelas->level_id,
                'guru_id'       => $kelas->guru_id,
                'alumni_id'     => $kelas->alumni_id,
                'jumlah_alumni' => $jumlah,
            ];
            if ($kelas_baru === null) {
                $this->db->insert('master_kelas', $kelas_data);
                $idk = $this->db->insert_id();
            } else {
                $this->db->where('id_kelas', $kelas_baru->id_kelas)->update('master_kelas', $kelas_data);
                $idk = $kelas_baru->id_kelas;
            }
            foreach ($alumnikelas[$ik] as $s) {
                $insert = [
                    'id_kelas_alumni' => $tp->id_tp . $smt->id_smt . $s['id'],
                    'id_tp'           => $tp->id_tp,
                    'id_smt'          => $smt->id_smt,
                    'id_kelas'        => $idk,
                    'id_siswa'        => $s['id'],
                ];
                $res[] = $this->db->replace('kelas_alumni', $insert);
            }
        }
        $this->output_json(['res' => $alumnikelas]);
    }

    public function detail($id)
    {
        $alumni = $this->master->getAlumniById($id);
        $user = $this->ion_auth->user()->row();
        $data = array_merge([
            'user'    => $user,
            'judul'   => 'Alumni',
            'subjudul' => 'Edit Data Alumni',
            'alumni'  => $alumni,
            'setting' => $this->dashboard->getSetting(),
            'tp'      => $this->dashboard->getTahun(),
            'tp_active' => $this->dashboard->getTahunActive(),
            'smt'     => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'profile' => $this->dashboard->getProfileAdmin($user->id),
        ], $this->buildInputForms($alumni));
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/edit');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function add()
    {
        $user = $this->ion_auth->user()->row();
        $data = [
            'user'      => $user,
            'judul'     => 'Alumni',
            'subjudul'  => 'Tambah Data Alumni',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $this->dashboard->getTahunActive(),
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'profile'   => $this->dashboard->getProfileAdmin($user->id),
            'tipe'      => 'add',
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/add');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function create()
    {
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]|is_unique[master_siswa.nis]');
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]|is_unique[master_siswa.nisn]');
        if ($this->form_validation->run() == false) {
            $this->output_json(['insert' => false, 'text' => 'Data Sudah ada, Pastikan NIS, NISN dan Username belum digunakan alumni lain']);
            return;
        }
        $insert = [
            'nama'         => $this->input->post('nama_alumni', true),
            'nis'          => $nis,
            'nisn'         => $nisn,
            'jenis_kelamin' => $this->input->post('jenis_kelamin', true),
            'foto'         => 'uploads/foto_siswa/' . $nis . 'jpg',
        ];
        $this->db->set('uid', 'UUID()', false);
        $this->db->insert('master_siswa', $insert);
        $last_id = $this->db->insert_id();
        $uid = $this->db->select('uid')->from('master_siswa')->where('id_siswa', $last_id)->get()->row();
        $induk = [
            'id_siswa'   => $last_id,
            'uid'        => $uid->uid,
            'kelas_akhir' => $this->input->post('kelas_akhir', true),
            'tahun_lulus' => $this->input->post('tahun_lulus', true),
            'no_ijazah'  => $this->input->post('no_ijazah', true),
            'status'     => 2,
        ];
        $this->output_json(['insert' => $this->db->insert('buku_induk', $induk), 'text' => 'Alumni berhasil ditambahkan']);
    }

    public function edit()
    {
        $id = $this->input->get('id', true);
        $alumni = $this->master->getAlumniById($id);
        $user = $this->ion_auth->user()->row();
        $data = array_merge([
            'user'      => $user,
            'judul'     => 'Alumni',
            'subjudul'  => 'Edit Data Alumni',
            'alumni'    => $alumni,
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $this->dashboard->getTahunActive(),
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'profile'   => $this->dashboard->getProfileAdmin($user->id),
        ], $this->buildInputForms($alumni));
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/edit');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function updateData()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $alumni = $this->master->getAlumniById($id_siswa);
        $u_nis = $alumni->nis === $nis ? '' : '|is_unique[master_siswa.nis]';
        $u_nisn = $alumni->nisn === $nisn ? '' : '|is_unique[master_siswa.nisn]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        if ($this->form_validation->run() == false) {
            $this->output_json(['insert' => false, 'text' => 'Data Sudah ada, Pastikan NIS, dan NISN belum digunakan alumni lain']);
            return;
        }
        $fields = ['nisn', 'nis', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_keluarga', 'anak_ke', 'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'kode_pos', 'hp', 'nama_ayah', 'nohp_ayah', 'pendidikan_ayah', 'pekerjaan_ayah', 'alamat_ayah', 'nama_ibu', 'nohp_ibu', 'pendidikan_ibu', 'pekerjaan_ibu', 'alamat_ibu', 'nama_wali', 'pendidikan_wali', 'pekerjaan_wali', 'nohp_wali', 'alamat_wali', 'tahun_masuk', 'kelas_awal', 'tgl_lahir_ayah', 'tgl_lahir_ibu', 'tgl_lahir_wali', 'sekolah_asal'];
        $input = [];
        foreach ($fields as $f) {
            $input[$f] = $this->input->post($f, true);
        }
        $input['foto'] = 'uploads/foto_siswa/' . $nis . '.jpg';
        $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
        $this->output_json(['insert' => $input, 'text' => 'Alumni berhasil diperbaharui']);
    }

    public function uploadFile($id_siswa)
    {
        $alumni = $this->master->getAlumniById($id_siswa);
        if (!isset($_FILES['foto']['name'])) {
            $this->output_json(['src' => '']);
            return;
        }
        $config = [
            'upload_path'   => './uploads/foto_siswa/',
            'allowed_types' => 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF',
            'overwrite'     => true,
            'file_name'     => $alumni->nis,
        ];
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('foto')) {
            $this->output_json(['src' => '', 'status' => false]);
            return;
        }
        $result = $this->upload->data();
        $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name'])
            ->where('id_siswa', $id_siswa)
            ->update('master_siswa');
        $this->output_json([
            'src'      => base_url() . 'uploads/foto_siswa/' . $result['file_name'],
            'filename' => pathinfo($result['file_name'], PATHINFO_FILENAME),
            'status'   => true,
            'type'     => $_FILES['foto']['type'],
            'size'     => $_FILES['foto']['size'],
        ]);
    }

    public function deleteFoto()
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (file_exists($file_name)) {
            unlink($file_name);
        }
        echo 'File Delete Successfully';
    }

    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
            return;
        }
        $this->master->delete('master_siswa', $chk, 'id_siswa');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }

    public function do_import()
    {
        $input = json_decode($this->input->post('alumni', true));
        $this->db->trans_start();
        $save = false;
        foreach ($input as $key => $val) {
            $row = (array) $val;
            $row['foto'] = 'uploads/foto_siswa/' . $row['nis'] . '.jpg';
            $save = $this->db->insert('master_siswa', $row);
        }
        $this->db->trans_complete();
        $this->output->set_content_type('application/json')->set_output(json_encode($save));
    }

    public function editKelulusan()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $status = $this->db
            ->set('kelas_akhir', $this->input->post('kelas_akhir', true))
            ->set('tahun_lulus', $this->input->post('tahun_lulus', true))
            ->set('no_ijazah', $this->input->post('no_ijazah', true))
            ->where('id_siswa', $id_siswa)
            ->update('master_siswa');
        $this->output_json(['status' => $status]);
    }
}
