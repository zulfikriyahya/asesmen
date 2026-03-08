<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Datajurusan extends CI_Controller
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
        $kode_peminatan = $this->dropdown->getAllKodePeminatan();
        $arr_kode = array_column($kode_peminatan, 'kode_kel_mapel');
        $jurusans = $this->master->getDataJurusan();
        $jurusan_mapels = [];
        foreach ($jurusans as $jurusan) {
            $jurusan_mapels[$jurusan->id_jurusan] = $this->master->getDataJurusanMapel(explode(',', $jurusan->mapel_peminatan ?? ''));
        }
        $data = [
            'user'            => $user,
            'judul'           => 'Jurusan',
            'subjudul'        => 'Daftar Jurusan',
            'profile'         => $this->dashboard->getProfileAdmin($user->id),
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $this->dashboard->getTahunActive(),
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $this->dashboard->getSemesterActive(),
            'kode_peminatan'  => $kode_peminatan,
            'mapel_peminatan' => $this->dropdown->getMapelPeminatan($arr_kode),
            'jurusans'        => $jurusans,
            'jurusan_mapels'  => $jurusan_mapels,
        ];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/jurusan/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function add()
    {
        $check_mapel = $this->input->post('mapel', true);
        $mapels = $check_mapel ? array_values($check_mapel) : [];
        $insert = [
            'nama_jurusan'    => $this->input->post('nama_jurusan', true),
            'kode_jurusan'    => $this->input->post('kode_jurusan', true),
            'mapel_peminatan' => implode(',', $mapels),
        ];
        $this->master->create('master_jurusan', $insert, false);
        $this->output_json(['status' => $insert]);
    }

    public function data()
    {
        $this->output_json($this->master->getDataTableJurusan(), false);
    }

    public function save()
    {
        $rows = count($this->input->post('nama_jurusan', true));
        $mode = $this->input->post('mode', true);
        $update = [];
        $status = false;
        for ($i = 1; $i <= $rows; $i++) {
            $nama_jurusan = 'nama_jurusan[' . $i . ']';
            $this->form_validation->set_rules($nama_jurusan, 'Jurusan', 'required');
            $this->form_validation->set_message('required', '{field} Wajib diisi');
            if ($this->form_validation->run() === false) {
                $this->output_json(['status' => false, 'errors' => form_error($nama_jurusan)]);
                return;
            }
            $update[] = [
                'id_jurusan'   => $this->input->post('id_jurusan[' . $i . ']', true),
                'nama_jurusan' => $this->input->post($nama_jurusan, true),
            ];
            $status = true;
        }
        if ($mode == 'edit' && $status) {
            $this->dashboard->update('master_jurusan', $update, 'id_jurusan', null, true);
        }
        $this->output_json(['status' => $status, 'update' => $update]);
    }

    public function update()
    {
        $data = $this->master->updateJurusan();
        $this->output->set_content_type('application/json')->set_output($data);
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
                if ($field->name == 'id_jurusan' || $field->name == 'jurusan_id') {
                    $tables[] = $table;
                    break;
                }
            }
        }
        $messages = [];
        foreach ($tables as $table) {
            if ($table == 'master_jurusan' || $table == 'master_kelas') {
                continue;
            }
            $this->db->where_in('id_jurusan', $chk);
            if ($this->db->count_all_results($table) > 0) {
                $messages[] = $table;
            }
        }
        if (!empty($messages)) {
            $this->output_json(['status' => false, 'message' => 'Jurusan digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
            return;
        }
        $this->master->delete('master_jurusan', $chk, 'id_jurusan');
        $this->output_json(['status' => true, 'total' => count($chk)]);
    }

    public function load_jurusan()
    {
        $this->output_json($this->master->getJurusan());
    }

    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = [
            'user'      => $user,
            'judul'     => 'Import Jurusan',
            'subjudul'  => 'Import Jurusan',
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
        $this->load->view('master/jurusan/import');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function do_import()
    {
        $data = json_decode($this->input->post('jurusan', true));
        $jurusan = [];
        foreach ($data as $j) {
            $jurusan[] = ['nama_jurusan' => $j->nama, 'kode_jurusan' => $j->kode];
        }
        $save = $this->master->create('master_jurusan', $jurusan, true);
        $this->output->set_content_type('application/json')->set_output($save);
    }

    public function updateById()
    {
        $id = $this->input->post('id_jurusan');
        $this->db->set('nama_jurusan', $this->input->post('username', true))
            ->set('kode_jurusan', $this->input->post('email', true))
            ->where('id_jurusan', $id)
            ->update('master_jurusan');
    }

    public function hapusById()
    {
        $id = $this->input->post('id');
        $this->db->where('id_jurusan', $id)->delete('master_jurusan');
    }

    public function exist($table, $data)
    {
        return $this->db->get_where($table, $data)->num_rows() > 0;
    }
}
