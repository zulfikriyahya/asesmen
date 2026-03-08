<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Walistruktur extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error(
                'Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
        }

        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
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
        $user  = $this->ion_auth->user()->row();
        $tp    = $this->master->getTahunActive();
        $smt   = $this->master->getSemesterActive();
        $guru  = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $struktur_raw = $this->kelas->getStrukturKelas($guru->wali_kelas);
        $struktur     = $struktur_raw ?? json_decode(json_encode($this->kelas->dummyStruktur()));

        $siswa   = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $siswas  = ['' => 'Pilih Siswa'];
        foreach ($siswa as $s) {
            $siswas[$s->id_siswa] = $s->nama;
        }

        $data = [
            'user'      => $user,
            'judul'     => 'Struktur Organisasi',
            'subjudul'  => 'Struktur Organisasi',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'      => $guru,
            'gurus'     => $this->dropdown->getAllGuru(),
            'struktur'  => $struktur,
            'siswas'    => $siswas,
            'id_kelas'  => $guru->wali_kelas,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/struktur');
        $this->load->view('members/guru/templates/footer');
    }

    public function save()
    {
        $data = [
            'id_kelas'            => $this->input->post('id_kelas', true),
            'ketua'               => $this->input->post('ketua', true),
            'wakil_ketua'         => $this->input->post('wakil_ketua', true),
            'sekretaris_1'        => $this->input->post('sekretaris_1', true),
            'sekretaris_2'        => $this->input->post('sekretaris_2', true),
            'bendahara_1'         => $this->input->post('bendahara_1', true),
            'bendahara_2'         => $this->input->post('bendahara_2', true),
            'sie_ekstrakurikuler' => $this->input->post('sie_ekstrakurikuler', true),
            'sie_upacara'         => $this->input->post('sie_upacara', true),
            'sie_olahraga'        => $this->input->post('sie_olahraga', true),
            'sie_keagamaan'       => $this->input->post('sie_keagamaan', true),
            'sie_keamanan'        => $this->input->post('sie_keamanan', true),
            'sie_ketertiban'      => $this->input->post('sie_ketertiban', true),
            'sie_kebersihan'      => $this->input->post('sie_kebersihan', true),
            'sie_keindahan'       => $this->input->post('sie_keindahan', true),
            'sie_kesehatan'       => $this->input->post('sie_kesehatan', true),
            'sie_kekeluargaan'    => $this->input->post('sie_kekeluargaan', true),
            'sie_humas'           => $this->input->post('sie_humas', true),
        ];

        $insert = $this->db->replace('kelas_struktur', $data);
        $this->output_json($insert);
    }
}
