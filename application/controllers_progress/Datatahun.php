<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Datatahun extends CI_Controller
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
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
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
        $jml  = $this->master->getJmlHariEfektif($tp->id_tp . $smt->id_smt);

        $data = [
            'user'       => $user,
            'judul'      => 'Tahun Pelajaran dan Semester',
            'subjudul'   => 'Atur Tahun Pelajaran dan Semester',
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'jml_hari'   => $jml == null ? '0' : $jml->jml_hari_efektif,
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/tahun/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function data()
    {
        $this->output_json($this->dashboard->getDataTahun(), false);
    }

    public function gantiTahun()
    {
        $aktif   = $this->input->post('active', true);
        $inputTp = json_decode($this->input->post('tahun', false));
        $update  = [];

        foreach ($inputTp as $tps) {
            $update[] = ['id_tp' => $tps->id, 'tahun' => $tps->tp, 'active' => $tps->id === $aktif ? 1 : 0];
        }

        $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
        $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
        $this->output_json(['msg' => 'Merubah Tahun Aktif', 'update' => $update, 'status' => true]);
    }

    public function gantiSemester()
    {
        $aktif    = $this->input->post('active', true);
        $inputSmt = json_decode($this->input->post('semester', false));
        $update   = [];

        foreach ($inputSmt as $tps) {
            $update[] = ['id_smt' => $tps->id, 'smt' => $tps->Semester, 'active' => $tps->id === $aktif ? 1 : 0];
        }

        $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
        $this->logging->saveLog(4, 'mengganti semester aktif');
        $this->output_json(['msg' => 'Merubah Semester Aktif', 'update' => $update, 'status' => true]);
    }

    public function add()
    {
        $method = $this->input->post('method', true);
        $tahun  = $this->input->post('tahun', true);

        if ($method === 'add') {
            $data = $this->master->create('master_tp', ['tahun' => $tahun]);
            $this->logging->saveLog(3, 'menambah tahun pelajaran');
        } else {
            $id   = $this->input->post('id_tahun', true);
            $data = $this->master->update('master_tp', ['id_tp' => $id, 'tahun' => $tahun], 'id_tp', $id);
            $this->logging->saveLog(4, 'mengedit tahun pelajaran');
        }

        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function saveHariEfektif()
    {
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $update = $this->db->replace('master_hari_efektif', [
            'id_hari_efektif'  => $tp->id_tp . $smt->id_smt,
            'jml_hari_efektif' => $this->input->post('jml_hari', true),
        ]);

        $this->output_json(['status' => $update]);
    }

    public function hapusTahun()
    {
        $id = $this->input->post('hapus', true);
        if ($this->dashboard->hapus('master_tp', $id, 'id_tp')) {
            $this->logging->saveLog(5, 'menghapus tahun pelajaran');
            $data = ['status' => true];
        } else {
            $data = ['status' => false];
        }
        $data['msg'] = 'Menghapus Tahun Pelajaran';
        $this->output_json($data);
    }

    public function hapus()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
            return;
        }
        $this->dashboard->hapus('master_tp', $chk, 'id_tp');
        $this->logging->saveLog(5, 'menghapus tahun pelajaran');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }
}
