<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelasabsensibulanan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Dibatasi');
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
            'judul'      => 'Daftar Hadir Bulanan',
            'subjudul'   => 'Daftar Hadir Bulanan Siswa',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'bulan'      => $this->dropdown->getBulan(),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['kelas']   = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['guru']    = $this->dropdown->getAllGuru();
            $data['mapel']   = $this->dropdown->getAllMapel();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/absenbulanan/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru        = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $mapel_guru  = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel       = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
            $arrMapel    = [];
            $arrKelas    = [];
            $arrId       = [];

            if ($mapel != null) {
                foreach ($mapel as $m) {
                    $arrMapel[$m->id_mapel] = $m->nama_mapel;
                    foreach ($m->kelas_mapel as $kls) {
                        $arrKelas[$m->id_mapel][] = [
                            'id_kelas'   => $kls->kelas,
                            'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas),
                        ];
                    }
                }
                foreach ($mapel[0]->kelas_mapel as $id_mapel) {
                    $arrId[] = $id_mapel->kelas;
                }
            }

            $data['guru']     = $guru;
            $data['id_guru']  = $guru->id_guru;
            $data['mapel']    = $arrMapel;
            $data['arrkelas'] = $arrKelas;
            $data['kelas']    = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/absenbulanan/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function loadAbsensiMapel()
    {
        $id_kelas = $this->input->post('kelas', true);
        $id_mapel = $this->input->post('mapel', true);
        $tahun    = $this->input->post('thn', true);
        $bulan    = $this->input->post('bln', true);
        $id_tp    = $this->master->getTahunActive()->id_tp;
        $id_smt   = $this->master->getSemesterActive()->id_smt;
        $jadwal   = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);

        if ($jadwal == null) {
            $this->output_json(['jadwal' => $jadwal]);
            return;
        }

        $jadwal->istirahat = unserialize($jadwal->istirahat);
        $tgl           = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $jadwal_materi = [];

        for ($i = 0; $i < $tgl; $i++) {
            $t = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $b = str_pad($bulan, 2, '0', STR_PAD_LEFT);
            $jadwal_materi[$t] = (array) $this->kelas->getAllMateriByTgl($id_kelas, $tahun . '-' . $b . '-' . $t, [$id_mapel]);
        }

        $this->output_json(['jadwal' => $jadwal, 'materi' => $jadwal_materi]);
    }

    private function total_hari($id_day, $bulan, $taun)
    {
        $dates      = [];
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $idday      = $id_day == '7' ? 0 : $id_day;

        for ($i = 1; $i <= $total_days; $i++) {
            if (date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday) {
                $dates[] = date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i));
            }
        }

        return $dates;
    }
}
