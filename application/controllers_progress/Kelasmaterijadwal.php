<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelasmaterijadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
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
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $bln  = $smt->id_smt == '1' ? '7' : '1';
        $thn  = explode('/', $tp->tahun ?? '');
        $thn  = $smt->id_smt == '1' ? ($thn[0] ?? '') : ($thn[1] ?? '');

        $data = [
            'user'          => $user,
            'judul'         => 'Jadwal Pelajaran',
            'subjudul'      => 'Set Jadwal Pelajaran',
            'setting'       => $this->dashboard->getSetting(),
            'tp'            => $this->dashboard->getTahun(),
            'tp_active'     => $tp,
            'smt'           => $this->dashboard->getSemester(),
            'smt_active'    => $smt,
            'kelas'         => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'id_kelas'      => '0',
            'method'        => '',
            'jmlIst'        => [],
            'jmlMapel'      => [],
            'thn_selected'  => $tp->tahun,
            'bln_selected'  => $bln,
            'date_selected' => $thn . '-' . $bln . '-' . date('d'),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materijadwal/data');
            $this->load->view('_templates/dashboard/_footer');
        }
    }

    public function kelas()
    {
        $tahun  = $this->input->get('tahun');
        $bulan  = $this->input->get('bulan');
        $kelas  = $this->input->get('kelas');
        $date   = $this->input->get('date');
        $user   = $this->ion_auth->user()->row();
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $jadk   = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $kelas);
        $jadm   = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $kelas);

        $jadwal_mapel = [];
        if ($jadm != null) {
            foreach ($jadm as $j) {
                $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $kelas)];
            }
        }

        $semua_materi = $this->kelas->getAllJadwalMateriByKelas($tp->id_tp, $smt->id_smt);
        $week = [
            date('Y-m-d', strtotime('monday this week', strtotime($date))),
            date('Y-m-d', strtotime('tuesday this week', strtotime($date))),
            date('Y-m-d', strtotime('wednesday this week', strtotime($date))),
            date('Y-m-d', strtotime('thursday this week', strtotime($date))),
            date('Y-m-d', strtotime('friday this week', strtotime($date))),
            date('Y-m-d', strtotime('saturday this week', strtotime($date))),
        ];

        $data = [
            'user'                 => $user,
            'judul'                => 'Jadwal Materi / Tugas',
            'subjudul'             => 'Set Jadwal Materi / Tugas',
            'setting'              => $this->dashboard->getSetting(),
            'tp'                   => $this->dashboard->getTahun(),
            'tp_active'            => $tp,
            'smt'                  => $this->dashboard->getSemester(),
            'smt_active'           => $smt,
            'kelas'                => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'jadwal_kbm'           => $jadk ?? json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false])),
            'id_kelas'             => $kelas,
            'method'               => 'edit',
            'jadwal_mapel'         => $jadwal_mapel,
            'mapels'               => $this->master->getAllMapel(),
            'thn_selected'         => $tahun,
            'bln_selected'         => $bulan,
            'date_selected'        => $date,
            'week'                 => $week,
            'opsi_materi'          => $this->kelas->getAllMateriByKelas($tp->id_tp, $smt->id_smt),
            'detail_jadwal_materi' => $semua_materi[1] ?? [],
            'detail_jadwal_tugas'  => $semua_materi[2] ?? [],
        ];

        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materijadwal/edit');
            $this->load->view('_templates/dashboard/_footer');
        }
    }

    public function setJadwal()
    {
        $istirahat = [];
        for ($i = 1; $i < 5; $i++) {
            $jamke = $this->input->post('ist' . $i, true);
            $durasi = $this->input->post('dur_ist' . $i, true);
            if (!$jamke) continue;
            $istirahat[] = ['ist' => $jamke, 'dur' => $durasi];
        }

        $id_tp    = $this->master->getTahunActive()->id_tp;
        $id_smt   = $this->master->getSemesterActive()->id_smt;
        $id_kelas = $this->input->post('id_kelas', true);

        $update = $this->db->replace('kelas_jadwal_kbm', [
            'id_kbm'             => $id_tp . $id_smt . $id_kelas,
            'id_tp'              => $id_tp,
            'id_smt'             => $id_smt,
            'id_kelas'           => $id_kelas,
            'kbm_jam_pel'        => $this->input->post('jam_mapel', true),
            'kbm_jam_mulai'      => $this->input->post('jam_mulai', true),
            'kbm_jml_mapel_hari' => $this->input->post('jml_mapel', true),
            'istirahat'          => serialize($istirahat),
        ]);

        $this->logging->saveLog(3, 'merubah jadwal pelajaran');
        $this->output_json(['status' => $update]);
    }

    public function setMapel()
    {
        $input    = json_decode($this->input->post('data', true));
        $id_kelas = $this->input->post('id_kelas', true);

        foreach ($input as $d) {
            $this->db->replace('kelas_jadwal_mapel', [
                'id_jadwal' => $d->id_tp . $d->id_smt . $id_kelas . $d->id_hari . $d->jam_ke,
                'id_tp'     => $d->id_tp,
                'id_smt'    => $d->id_smt,
                'id_kelas'  => $id_kelas,
                'id_hari'   => $d->id_hari,
                'jam_ke'    => $d->jam_ke,
                'id_mapel'  => $d->id_mapel,
            ]);
        }

        $this->output_json(['status' => true]);
    }

    public function saveJadwal()
    {
        $input_materi = json_decode($this->input->post('materi', true));
        $input_tugas  = json_decode($this->input->post('tugas', true));

        foreach ($input_materi as $im) {
            $this->db->replace('kelas_jadwal_materi', ['jenis' => '1', 'id_kjm' => $im->id_kjm, 'id_tp' => $im->id_tp, 'id_smt' => $im->id_smt, 'id_kelas' => $im->id_kelas, 'id_materi' => $im->id_materi, 'id_mapel' => $im->id_mapel, 'jadwal_materi' => $im->jadwal_materi]);
        }

        foreach ($input_tugas as $im) {
            $update = $this->db->replace('kelas_jadwal_materi', ['jenis' => '2', 'id_kjm' => $im->id_kjm, 'id_tp' => $im->id_tp, 'id_smt' => $im->id_smt, 'id_kelas' => $im->id_kelas, 'id_materi' => $im->id_materi, 'id_mapel' => $im->id_mapel, 'jadwal_materi' => $im->jadwal_materi]);
        }

        $this->logging->saveLog(3, 'merubah jadwal materi dan tugas');
        $this->output_json(['status' => $update ?? false]);
    }
}
