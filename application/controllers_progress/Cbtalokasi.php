<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtalokasi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin()) {
            show_error(
                'Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
        }

        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, bool $encode = true): void
    {
        $output = $encode ? json_encode($data) : $data;
        $this->output->set_content_type('application/json')->set_output($output);
    }

    public function index()
    {
        $this->load->model('Dropdown_model',  'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();

        $data = [
            'user'            => $user,
            'judul'           => 'Alokasi Waktu',
            'subjudul'        => 'Alokasi Waktu Ujian',
            'setting'         => $setting,
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $tp,
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $smt,
            'jenis'           => ['' => 'belum ada jadwal ujian'],
            'filter'          => ['0' => 'Semua', '1' => 'Tanggal'],
            'kelas'           => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'ruang'           => $this->dropdown->getAllRuang(),
            'levels'          => [],
            'jenis_selected'  => $this->input->get('jenis',  true),
            'level_selected'  => $this->input->get('level',  true),
            'filter_selected' => $this->input->get('filter', true),
            'dari_selected'   => $this->input->get('dari',   true),
            'sampai_selected' => $this->input->get('sampai', true),
        ];

        $jenis_selected = $data['jenis_selected'];
        $level_selected = $data['level_selected'];
        $dari_selected  = $data['dari_selected'];
        $sampai_selected = $data['sampai_selected'];

        $jadwals = ($jenis_selected !== null && $level_selected !== null)
            ? $this->cbt->getJadwalByJenis($jenis_selected, $level_selected, $dari_selected, $sampai_selected)
            : [];

        $data['jadwals'] = $jadwals;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/alokasi/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function saveAlokasi()
    {
        $input  = json_decode($this->input->post('alokasi', true));
        $insert = [];

        foreach ($input as $d) {
            if ($d->id_jadwal == '0') continue;
            $insert[] = ['id_jadwal' => $d->id_jadwal, 'jam_ke' => $d->jam_ke];
        }

        $update = $this->db->update_batch('cbt_jadwal', $insert, 'id_jadwal');
        $this->output_json(['status' => $update]);
    }
}
