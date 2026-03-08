<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelasjadwal extends CI_Controller
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

        $data = [
            'user'       => $user,
            'judul'      => 'Jadwal Pelajaran',
            'subjudul'   => 'Set Jadwal Pelajaran',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'kelas'      => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'id_kelas'   => '0',
            'method'     => '',
            'jmlIst'     => [],
            'jmlMapel'   => [],
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/jadwal/data');
            $this->load->view('_templates/dashboard/_footer');
        }
    }

    public function kelas($kelas)
    {
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $kelas);
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $kelas);

        $jadwal_mapel = [];
        if ($jadm != null) {
            foreach ($jadm as $j) {
                $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $kelas)];
            }
        }

        $data = [
            'user'        => $user,
            'judul'       => 'Jadwal Pelajaran',
            'subjudul'    => 'Set Jadwal Pelajaran',
            'setting'     => $this->dashboard->getSetting(),
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'kelas'       => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'jadwal_kbm'  => $jadk ?? json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false])),
            'id_kelas'    => $kelas,
            'method'      => 'edit',
            'jadwal_mapel' => $jadwal_mapel,
            'mapels'      => $this->dropdown->getAllKodeMapel(),
        ];

        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/jadwal/edit');
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

        $this->db->where(['id_tp' => $input[0]->id_tp, 'id_smt' => $input[0]->id_smt, 'id_kelas' => $id_kelas]);
        $this->db->delete('kelas_jadwal_mapel');

        $data = [];
        foreach ($input as $d) {
            $data[] = [
                'id_jadwal' => $d->id_tp . $d->id_smt . $id_kelas . $d->id_hari . $d->jam_ke,
                'id_tp'     => $d->id_tp,
                'id_smt'    => $d->id_smt,
                'id_kelas'  => $id_kelas,
                'id_hari'   => $d->id_hari,
                'jam_ke'    => $d->jam_ke,
                'id_mapel'  => $d->id_mapel,
            ];
        }

        $update = $this->db->insert_batch('kelas_jadwal_mapel', $data);
        $this->output_json(['status' => $update]);
    }
}
