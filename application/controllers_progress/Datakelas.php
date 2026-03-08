<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datakelas extends CI_Controller
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

    private function getLevel($jenjang)
    {
        if ($jenjang == '1') return '6';
        if ($jenjang == '2') return '9';
        if ($jenjang == '3') return '3';
        return '12';
    }

    private function buildKelasData($kelas, $tp, $smt, $jumlah_siswa = null)
    {
        return [
            'nama_kelas'   => $kelas->nama_kelas,
            'kode_kelas'   => $kelas->kode_kelas,
            'jurusan_id'   => $kelas->jurusan_id,
            'id_tp'        => $tp->id_tp,
            'id_smt'       => $smt->id_smt,
            'level_id'     => $kelas->level_id,
            'guru_id'      => $kelas->guru_id,
            'siswa_id'     => $kelas->siswa_id,
            'jumlah_siswa' => $jumlah_siswa ?? $kelas->jumlah_siswa,
        ];
    }

    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $chek = $this->kelas->count_all();
        $kelas = $chek > 0 ? $this->kelas->getKelasList($tp->id_tp, $smt->id_smt) : [];
        $kelas_lama = $chek > 0 ? $this->kelas->getKelasList($tp->id_tp - 1, '2') : [];
        $data = [
            'user'       => $user,
            'judul'      => 'Kelas',
            'subjudul'   => 'Data Kelas',
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'kelas'      => $kelas,
            'kelas_lama' => $kelas_lama,
            'jurusan'    => $this->kelas->get_jurusan(),
            'level'      => $this->kelas->getLevel($setting->jenjang),
            'guru'       => $this->kelas->get_guru(),
            'siswa'      => $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function detail($id)
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $struktur = $this->kelas->getStrukturKelas($id);
        $data = [
            'user'      => $user,
            'judul'     => 'Detail Kelas',
            'subjudul'  => 'Detail Kelas',
            'setting'   => $setting,
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'   => $this->dashboard->getProfileAdmin($user->id),
            'kelas'     => $this->kelas->get_one($id),
            'jurusan'   => $this->kelas->get_jurusan(),
            'level'     => $this->kelas->getLevel($setting->jenjang),
            'guru'      => $this->kelas->get_guru(),
            'siswas'    => $this->kelas->get_siswa_kelas($id, $tp->id_tp, $smt->id_smt),
            'struktur'  => $struktur !== null ? $struktur : json_decode(json_encode($this->kelas->dummyStruktur())),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/detail');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function add()
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data = [
            'user'       => $user,
            'judul'      => 'Kelas',
            'subjudul'   => 'Tambah Kelas',
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'kelas'      => json_decode(json_encode($this->kelas->dummy())),
            'jurusan'    => $this->kelas->get_jurusan(),
            'level'      => $this->kelas->getLevel($setting->jenjang),
            'guru'       => $this->kelas->get_guru(),
            'siswa'      => $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt),
            'siswakelas' => [],
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/add');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function edit($id = '')
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data = [
            'user'       => $user,
            'judul'      => 'Kelas',
            'subjudul'   => 'Edit Kelas',
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'id_kelas'   => $id,
            'kelas'      => $this->kelas->get_one($id),
            'jurusan'    => $this->kelas->get_jurusan(),
            'level'      => $this->kelas->getLevel($setting->jenjang),
            'guru'       => $this->kelas->getWaliKelas($tp->id_tp, $smt->id_smt),
            'siswa'      => $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt),
            'siswakelas' => $this->kelas->get_siswa_kelas($id, $tp->id_tp, $smt->id_smt),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/add');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function save()
    {
        $id = $this->input->post('id_kelas', true);
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $siswas = $this->input->post('siswa', true);
        $siswakelas = [];
        foreach ($siswas as $id_siswa) {
            if ($id_siswa !== null) {
                $siswakelas[] = ['id' => $id_siswa];
            }
        }
        $this->update_kelas($id);
        $this->output_json(['status' => true, 'siswakelas' => $siswakelas]);
    }

    public function update_kelas($id)
    {
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $siswakelas = $this->kelas->get_status_siswa_kelas($id, $id_tp, $id_smt);
        if (count($siswakelas) > 0) {
            foreach ($siswakelas as $id_siswa => $sis) {
                $this->db->replace('kelas_siswa', [
                    'id_kelas_siswa' => $id_tp . $id_smt . $id_siswa,
                    'id_tp'          => $id_tp,
                    'id_smt'         => $id_smt,
                    'id_kelas'       => 0,
                    'id_siswa'       => $id_siswa,
                ]);
            }
        }
        $siswas = $this->input->post('siswa', true);
        foreach ($siswas as $id_siswa) {
            if ($id_siswa !== null) {
                $this->db->replace('kelas_siswa', [
                    'id_kelas_siswa' => $id_tp . $id_smt . $id_siswa,
                    'id_tp'          => $id_tp,
                    'id_smt'         => $id_smt,
                    'id_kelas'       => $id,
                    'id_siswa'       => $id_siswa,
                ]);
            }
        }
    }

    public function manage()
    {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data = [
            'user'      => $user,
            'judul'     => 'Copy Kelas',
            'subjudul'  => 'Copy Data Kelas ke SMT II',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'   => $this->dashboard->getProfileAdmin($user->id),
            'kelas'     => $this->dropdown->getAllKelas($tp->id_tp, '1'),
            'kelas2'    => $this->dropdown->getAllKelas($tp->id_tp, '2'),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/persemester');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function getFromSmt1($kelas)
    {
        $tp = $this->dashboard->getTahunActive();
        $data1 = $this->kelas->getKelasSiswa($kelas, $tp->id_tp, '1');
        $data2 = $this->kelas->getKelasSiswa($kelas, $tp->id_tp, '2');
        $ids = array_column($data2, 'id_siswa');
        $this->output_json(['smt1' => $data1, 'smt2' => $ids]);
    }

    public function copyFromSmt1()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kelas1 = $this->input->post('kelas_lama', true);
        $kelas2 = $this->input->post('kelas_baru', true);
        $kelas = $this->kelas->get_one($kelas1, $tp->id_tp, '1');
        $kelas_data = $this->buildKelasData($kelas, $tp, $smt);
        $kelas_data['nama_kelas'] = $kelas2;
        $this->db->insert('master_kelas', $kelas_data);
        $idk = $this->db->insert_id();
        $res = [];
        foreach (unserialize($kelas->jumlah_siswa) as $value) {
            $id_siswa = $value['id'];
            if ($id_siswa !== null) {
                $res[] = $this->db->replace('kelas_siswa', [
                    'id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $id_siswa,
                    'id_tp'          => $tp->id_tp,
                    'id_smt'         => $smt->id_smt,
                    'id_kelas'       => $idk,
                    'id_siswa'       => $id_siswa,
                ]);
            }
        }
        $this->output_json($res);
    }

    public function copySiswaFromSmt1()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $idkelases = [];
        $siswakelas = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->id_kelas;
            $siswakelas[$d->id_kelas][] = ['id' => $d->id_siswa];
        }
        $res = [];
        foreach (array_unique($idkelases) as $ik) {
            if ($ik === '') {
                continue;
            }
            $kelas = $this->kelas->get_one($ik, $tp->id_tp, '1');
            $kelas_data = $this->buildKelasData($kelas, $tp, $smt, serialize($siswakelas[$ik]));
            $this->db->insert('master_kelas', $kelas_data);
            $idk = $this->db->insert_id();
            foreach ($siswakelas[$ik] as $s) {
                $res[] = $this->db->replace('kelas_siswa', [
                    'id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $s['id'],
                    'id_tp'          => $tp->id_tp,
                    'id_smt'         => $smt->id_smt,
                    'id_kelas'       => $idk,
                    'id_siswa'       => $s['id'],
                ]);
            }
        }
        $this->output_json($res);
    }

    public function kenaikan()
    {
        $kelas = $this->input->get('kelas', true);
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $level = $this->getLevel($setting->jenjang);
        $data = [
            'user'       => $user,
            'judul'      => 'Kenaikkan Kelas',
            'subjudul'   => 'Naik Kelas Siswa',
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'kelas_lama' => $this->dropdown->getAllKelas($tp->id_tp - 1, '2', '!=' . $level),
            'kelas_baru' => $this->dropdown->getAllKelas($tp->id_tp, '1'),
        ];
        if ($kelas !== null) {
            $lvlKls = $this->kelas->get_one($kelas, $tp->id_tp - 1, '2');
            $data['siswa_kelas_baru'] = $this->master->getSiswaKelasBaru($tp->id_tp, $smt->id_smt);
            $data['siswas'] = $this->rapor->getKenaikanSiswa($kelas, $tp->id_tp - 1, '2');
            $data['kelas_selected'] = $kelas;
            $data['kelases'] = $this->dropdown->getAllKelas($tp->id_tp - 1, '2', '=' . ($lvlKls->level_id + 1));
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/naikkelas');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function naikKelas()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $idkelases = [];
        $siswakelas = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->kelas_baru;
            $siswakelas[$d->kelas_baru][] = ['id' => $d->id_siswa];
        }
        $res = [];
        foreach (array_unique($idkelases) as $ik) {
            $kelas = $this->kelas->get_one($ik, $tp->id_tp - 1, '2');
            $kelas_baru = $this->kelas->getKelasByNama($kelas->nama_kelas, $tp->id_tp, $smt->id_smt);
            $jumlah = serialize($siswakelas[$ik]);
            $kelas_data = $this->buildKelasData($kelas, $tp, $smt, $jumlah);
            if ($kelas_baru === null) {
                $this->db->insert('master_kelas', $kelas_data);
                $idk = $this->db->insert_id();
            } else {
                $this->db->where('id_kelas', $kelas_baru->id_kelas)->update('master_kelas', $kelas_data);
                $idk = $kelas_baru->id_kelas;
            }
            foreach ($siswakelas[$ik] as $s) {
                $res[] = $this->db->replace('kelas_siswa', [
                    'id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $s['id'],
                    'id_tp'          => $tp->id_tp,
                    'id_smt'         => $smt->id_smt,
                    'id_kelas'       => $idk,
                    'id_siswa'       => $s['id'],
                ]);
            }
        }
        $this->output_json(['res' => $siswakelas]);
    }

    public function hapus($id_kelas)
    {
        $this->output_json([
            'siswa' => $this->master->delete('kelas_siswa', $id_kelas, 'id_kelas'),
            'kelas' => $this->master->delete('master_kelas', $id_kelas, 'id_kelas'),
        ]);
    }
}
