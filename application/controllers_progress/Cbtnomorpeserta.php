<?php

class Cbtnomorpeserta extends CI_Controller
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
        $this->load->library('upload');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data = [
            'user'     => $user,
            'judul'    => 'Nomor Peserta',
            'subjudul' => 'Generate Nomor Peserta Ujian',
            'profile'  => $this->dashboard->getProfileAdmin($user->id),
            'setting'  => $this->dashboard->getSetting(),
            'tp'       => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'      => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'jadwal'   => $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt),
            'kelas'    => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'ruang'    => $this->dropdown->getAllRuang(),
            'sesi'     => $this->dropdown->getAllSesi(),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/nomorpeserta/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function saveNomor()
    {
        $input = json_decode($this->input->post('siswa', true));
        $arrNomor = $this->cbt->getAllNomorPeserta();
        $tp = $this->dashboard->getTahunActive();
        $update = false;
        foreach ($input as $in) {
            $nomorAda = isset($arrNomor[$in->id]) ? $arrNomor[$in->id] : null;
            if ($nomorAda !== null && $nomorAda->nomor_peserta == $in->nomor && $nomorAda->id_siswa != $in->id) {
                $update = false;
            } else {
                $insert = [
                    'id_nomor'      => $in->id . $tp->id_tp,
                    'id_siswa'      => $in->id,
                    'id_tp'         => $tp->id_tp,
                    'nomor_peserta' => $in->nomor,
                ];
                $update = $this->db->replace('cbt_nomor_peserta', $insert);
            }
        }
        $this->output_json($update);
    }

    public function resetNomor()
    {
        $input = json_decode($this->input->get('kelas', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->cbt->getSiswaByKelasArray($tp->id_tp, $smt->id_smt, $input);
        $update = false;
        foreach ($siswas as $siswa) {
            $insert = [
                'id_nomor'      => $siswa->id_siswa . $tp->id_tp,
                'id_siswa'      => $siswa->id_siswa,
                'id_tp'         => $tp->id_tp,
                'nomor_peserta' => '',
            ];
            $update = $this->db->replace('cbt_nomor_peserta', $insert);
        }
        $this->output_json(['status' => $update]);
    }

    public function getSiswaKelas($arr_kelas)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kelas = json_decode(urldecode($arr_kelas));
        $siswas = $this->cbt->getSiswaByKelasArray($tp->id_tp, $smt->id_smt, $kelas);
        $arrNomor = $this->cbt->getAllNomorPeserta();
        $this->output_json(['siswa' => $siswas, 'nomor' => $arrNomor]);
    }
}
