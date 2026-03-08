<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelascatatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
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

    public function index()
    {
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $id_kelas = $this->input->get('kelas', true);
        $id_mapel = $this->input->get('mapel', true);

        $data = [
            'user'            => $user,
            'judul'           => 'Catatan Guru',
            'subjudul'        => 'Catatan Selama Pembelajaran',
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $tp,
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $smt,
            'kelas_selected'  => $id_kelas,
            'mapel_selected'  => $id_mapel,
        ];

        if ($id_kelas != null) {
            $cat_kelas = $this->kelas->getCatatanMapelKelas($id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
            foreach ($cat_kelas as $ck) {
                $ck->reading = unserialize($ck->reading);
            }
            $data['cat_kelas'] = $cat_kelas;
            $data['cat_siswa'] = $this->kelas->getCatatanMapelSiswa($tp->id_tp, $smt->id_smt, $id_kelas, $id_mapel);
        }

        $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $arrId      = [];

        if ($mapel != null) {
            foreach ($mapel as $mpl) {
                foreach ($mpl->kelas_mapel as $km) {
                    $arrId[] = $km->kelas;
                }
            }
        }

        $kelasses = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
        $arrMapel = [];
        $arrKelas = [];

        if ($mapel != null) {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                foreach ($m->kelas_mapel as $kls_mapel) {
                    foreach ($kelasses as $key => $kelass) {
                        if ($kls_mapel->kelas == $key) {
                            $arrKelas[$m->id_mapel][$key] = $kelass;
                        }
                    }
                }
            }
        }

        $data['guru']    = $guru;
        $data['id_guru'] = $guru->id_guru;
        $data['mapel']   = $arrMapel;
        $data['kelas']   = $arrKelas;

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/kelas/catatan/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function siswa()
    {
        $id_siswa = $this->input->get('id');
        $id_mapel = $this->input->get('mapel');
        $id_kelas = $this->input->get('kelas');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->master->getTahunActive();
        $smt      = $this->master->getSemesterActive();

        $data = [
            'user'          => $user,
            'judul'         => 'Catatan Siswa',
            'subjudul'      => 'Catatan Siswa',
            'setting'       => $this->dashboard->getSetting(),
            'tp'            => $this->dashboard->getTahun(),
            'tp_active'     => $tp,
            'smt'           => $this->dashboard->getSemester(),
            'smt_active'    => $smt,
            'siswa'         => $this->master->getSiswaById($id_siswa),
            'catatan_siswa' => $this->kelas->getAllCatatanMapelSiswa($id_siswa, $id_mapel, $tp->id_tp, $smt->id_smt),
            'mapel'         => $id_mapel,
            'kelas'         => $id_kelas,
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('members/guru/kelas/catatan/persiswa');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/kelas/catatan/persiswa');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function saveCatatanKelas()
    {
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $insert = [
            'id_tp'    => $tp->id_tp,
            'id_smt'   => $smt->id_smt,
            'type'     => '1',
            'id_mapel' => $this->input->post('id_mapel', true),
            'id_kelas' => $this->input->post('id_kelas'),
            'id_guru'  => $guru->id_guru,
            'level'    => $this->input->post('level', true),
            'text'     => $this->input->post('text', true),
            'reading'  => serialize([]),
        ];

        $this->output_json($this->master->create('kelas_catatan_mapel', $insert));
    }

    public function saveCatatanSiswa()
    {
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $insert = [
            'id_tp'    => $tp->id_tp,
            'id_smt'   => $smt->id_smt,
            'type'     => '2',
            'id_mapel' => $this->input->post('id_mapel', true),
            'id_siswa' => $this->input->post('id_siswa'),
            'id_guru'  => $guru->id_guru,
            'level'    => $this->input->post('level', true),
            'text'     => $this->input->post('text', true),
            'reading'  => serialize([]),
        ];

        $this->output_json($this->master->create('kelas_catatan_mapel', $insert));
    }

    public function hapus($id_catatan)
    {
        $this->output_json($this->master->delete('kelas_catatan_mapel', $id_catatan, 'id_catatan'));
    }
}
