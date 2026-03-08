<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datamapel extends CI_Controller
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
        $this->load->dbforge();
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
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
        if (!$this->db->field_exists('urutan_tampil', 'master_mapel')) {
            $this->dbforge->add_column('master_mapel', [
                'urutan_tampil' => ['type' => 'INT', 'constraint' => 3, 'after' => 'urutan'],
            ]);
        }
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = [
            'user'              => $user,
            'judul'             => 'Mata Pelajaran',
            'subjudul'          => 'Daftar Mata Pelajaran',
            'profile'           => $this->dashboard->getProfileAdmin($user->id),
            'setting'           => $setting,
            'tp'                => $this->dashboard->getTahun(),
            'tp_active'         => $this->dashboard->getTahunActive(),
            'smt'               => $this->dashboard->getSemester(),
            'smt_active'        => $this->dashboard->getSemesterActive(),
            'kategori'          => ['WAJIB', 'PAI (Kemenag)', 'PEMINATAN AKADEMIK', 'AKADEMIK KEJURUAN', 'LINTAS MINAT', 'MULOK'],
            'kelompok_mapel'    => $this->master->getDataKelompokMapel(),
            'sub_kelompok_mapel' => $this->master->getDataSubKelompokMapel(),
            'kelompok'          => $this->dropdown->getDataKelompokMapel(),
            'status'            => ['Nonaktif', 'Aktif'],
            'mapel_non_aktif'   => $this->master->getAllMapelNonAktif($setting->jenjang),
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/mapel/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function addKelompokMapel()
    {
        $id = $this->input->post('id_kel_mapel');
        $insert = [
            'nama_kel_mapel' => $this->input->post('nama_kel_mapel', true),
            'kode_kel_mapel' => $this->input->post('kode_kel_mapel', true),
            'kategori'       => $this->input->post('kategori', true),
            'id_parent'      => $this->input->post('id_parent', true),
        ];
        if ($id !== null) {
            $this->db->where('id_kel_mapel', $id)->update('master_kelompok_mapel', $insert);
            $data = $this->db->affected_rows() >= 0;
        } else {
            $data = $this->master->create('master_kelompok_mapel', $insert);
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function hapusKelompok()
    {
        $id = $this->input->post('id_kel');
        $kode = $this->input->post('kode');
        $messages = [];

        $this->db->where_in('kelompok', $kode);
        if ($this->db->count_all_results('master_mapel') > 0) {
            $messages[] = 'Mata Pelajaran';
        }

        $this->db->where_in('id_parent', $id);
        if ($this->db->count_all_results('master_kelompok_mapel') > 0) {
            $messages[] = 'Sub Kelompok';
        }

        if (!empty($messages)) {
            $this->output_json(['status' => false, 'message' => 'Kelompok digunakan di: ' . implode(', ', $messages)]);
            return;
        }

        $this->master->delete('master_kelompok_mapel', $id, 'id_kel_mapel');
        $this->output_json(['status' => true, 'message' => 'berhasil']);
    }

    public function create()
    {
        $setting = $this->dashboard->getSetting();
        $insert = [
            'nama_mapel'    => $this->input->post('nama_mapel', true),
            'kode'          => $this->input->post('kode_mapel', true),
            'kelompok'      => $this->input->post('kelompok', true),
            'urutan_tampil' => $this->input->post('urutan_tampil', true),
            'jenjang'       => $setting->jenjang,
        ];
        $data = $this->master->create('master_mapel', $insert);
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function getDataKelompok()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_kelompok_mapel');
        $this->datatables->where('id_parent', '0');
        $this->db->order_by('kode_kel_mapel');
        echo $this->datatables->generate();
    }

    public function getDataSubKelompok()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_kelompok_mapel');
        $this->datatables->where('id_parent <> 0');
        $this->db->order_by('kode_kel_mapel');
        echo $this->datatables->generate();
    }

    public function read()
    {
        $this->datatables->select('id_mapel, urutan_tampil, nama_mapel, kode, kelompok, deletable, status');
        $this->datatables->from('master_mapel');
        $this->db->order_by('kelompok');
        $this->db->order_by('urutan_tampil');
        echo $this->datatables->generate();
    }

    public function update()
    {
        $data = $this->master->updateMapel();
        $this->output->set_content_type('application/json')->set_output($data);
    }

    public function aktifkan($id)
    {
        $update = $this->db->set('status', '1')->where('id_mapel', $id)->update('master_mapel');
        $this->output_json($update);
    }

    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false, 'total' => 'Tidak ada data yang dipilih!']);
            return;
        }

        $tables = [];
        foreach ($this->db->list_tables() as $table) {
            foreach ($this->db->field_data($table) as $field) {
                if ($field->name == 'id_mapel' || $field->name == 'mapel_id') {
                    $tables[] = $table;
                    break;
                }
            }
        }

        $messages = [];
        foreach ($tables as $table) {
            if ($table == 'master_mapel' || $table == 'cbt_soal') {
                continue;
            }
            $this->db->where_in('id_mapel', $chk);
            if ($this->db->count_all_results($table) > 0) {
                $messages[] = $table;
            }
        }

        if (!empty($messages)) {
            $this->output_json(['status' => false, 'message' => 'Mapel digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
            return;
        }

        $this->master->delete('master_mapel', $chk, 'id_mapel');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }

    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = [
            'user'      => $user,
            'judul'     => 'Mata Pelajaran',
            'subjudul'  => 'Import Mata Pelajaran',
            'profile'   => $this->dashboard->getProfileAdmin($user->id),
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $this->dashboard->getTahunActive(),
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];
        if ($import_data !== null) {
            $data['import'] = $import_data;
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/mapel/import');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function do_import()
    {
        $inputs = $this->input->post('mapel', true);
        $save = $this->master->create('master_mapel', $inputs, true);
        $this->output->set_content_type('application/json')->set_output($save);
    }
}
