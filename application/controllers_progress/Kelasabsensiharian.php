<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelasabsensiharian extends CI_Controller
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
        $user = $this->ion_auth->user()->row();
        $tp   = $this->master->getTahunActive();
        $smt  = $this->master->getSemesterActive();

        $data = [
            'user'       => $user,
            'judul'      => 'Kehadiran Harian Siswa',
            'subjudul'   => 'Data Kehadiran Siswa',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'kelas'      => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'mapel'      => $this->dropdown->getAllMapel(),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['guru']    = $this->dropdown->getAllGuru();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/absenharian/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru            = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru']    = $guru;
            $data['id_guru'] = $guru->id_guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/absenharian/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function loadAbsensi()
    {
        $id_kelas = $this->input->post('kelas', true);
        $tahun    = $this->input->post('thn', true);
        $bulan    = str_pad($this->input->post('bln', true), 2, '0', STR_PAD_LEFT);
        $tanggal  = str_pad($this->input->post('tgl', true), 2, '0', STR_PAD_LEFT);
        $hari     = $this->input->post('hari', true);
        $id_tp    = $this->master->getTahunActive()->id_tp;
        $id_smt   = $this->master->getSemesterActive()->id_smt;

        $info      = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        $istirahat = $info != null ? unserialize($info->istirahat) : [];

        $jadwal     = $this->dashboard->loadJadwalHariIni($id_tp, $id_smt, $id_kelas, $hari);
        $arrIdMapel = array_column($jadwal, 'id_mapel');

        $jadwal_materi = count($arrIdMapel) > 0
            ? $this->kelas->getAllMateriByTgl($id_kelas, $tahun . '-' . $bulan . '-' . $tanggal, $arrIdMapel)
            : [];

        $arrIdKjm = [];
        foreach ($jadwal_materi as $jmtr) {
            foreach ($jmtr as $jam) {
                foreach ($jam as $jns) {
                    $arrIdKjm[] = $jns->id_kjm;
                }
            }
        }

        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        $log   = [];

        foreach ($siswa as $s) {
            $status = [];
            if (count($arrIdKjm) > 0) {
                $status_materi = $this->kelas->getRekapStatusMateri($s->id_siswa, $arrIdKjm);
                foreach ($status_materi as $stat) {
                    $status[$stat->jam_ke][$stat->id_mapel][$stat->jenis] = $stat;
                }
            }
            $log[$s->id_siswa] = ['nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->nama_kelas, 'status' => $status];
        }

        $this->output_json([
            'log'       => $log,
            'info'      => $info,
            'jadwal'    => $jadwal,
            'materi'    => $jadwal_materi,
            'istirahat' => $istirahat,
        ]);
    }
}
