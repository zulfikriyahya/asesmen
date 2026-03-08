<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datatahun extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
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
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Tahun Pelajaran dan Semester', 'subjudul' => 'Atur Tahun Pelajaran dan Semester', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $jml = $this->master->getJmlHariEfektif($tp->id_tp . $smt->id_smt);
        $data['jml_hari'] = $jml == null ? '0' : $jml->jml_hari_efektif;
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
        $aktif = $this->input->post('active', true);
        $inputTp = json_decode($this->input->post('tahun', false));
        foreach ($inputTp as $tps) {
            $id_tp = $tps->id;
            $tahun = $tps->tp;
            if ($id_tp === $aktif) {
                $active = 1;
            } else {
                $active = 0;
            }
            $update[] = array('id_tp' => $id_tp, 'tahun' => $tahun, 'active' => $active);
        }
        $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
        $data['msg'] = 'Merubah Tahun Aktif';
        $data['update'] = $update;
        $data['status'] = true;
        $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
        $this->output_json($data);
    }
    public function gantiSemester()
    {
        $aktif = $this->input->post('active', true);
        $inputSmt = json_decode($this->input->post('semester', false));
        foreach ($inputSmt as $tps) {
            $id_smt = $tps->id;
            $smt = $tps->Semester;
            if ($id_smt === $aktif) {
                $active = 1;
            } else {
                $active = 0;
            }
            $update[] = array('id_smt' => $id_smt, 'smt' => $smt, 'active' => $active);
        }
        $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
        $data['msg'] = 'Merubah Semester Aktif';
        $data['update'] = $update;
        $data['status'] = true;
        $this->logging->saveLog(4, 'mengganti semester aktif');
        $this->output_json($data);
    }
    public function add()
    {
        $method = $this->input->post('method', true);
        $tahun = $this->input->post('tahun', true);
        if ($method === 'add') {
            $insert = ['tahun' => $tahun];
            $data = $this->master->create('master_tp', $insert);
            $this->logging->saveLog(3, 'menambah tahun pelajaran');
        } else {
            $id = $this->input->post('id_tahun', true);
            $update = array('id_tp' => $id, 'tahun' => $tahun);
            $data = $this->master->update('master_tp', $update, 'id_tp', $id);
            $this->logging->saveLog(4, 'mengedit tahun pelajaran');
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function saveHariEfektif()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $input = ['id_hari_efektif' => $tp->id_tp . $smt->id_smt, 'jml_hari_efektif' => $this->input->post('jml_hari', true)];
        $update = $this->db->replace('master_hari_efektif', $input);
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function hapusTahun()
    {
        $id = $this->input->post('hapus', true);
        if ($this->dashboard->hapus('master_tp', $id, 'id_tp')) {
            $this->logging->saveLog(5, 'menghapus tahun pelajaran');
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        $data['msg'] = 'Menghapus Tahun Pelajaran';
        $this->output_json($data);
    }
    public function hapus()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->dashboard->hapus('master_tp', $chk, 'id_tp')) {
            }
            $this->logging->saveLog(5, 'menghapus tahun pelajaran');
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
}