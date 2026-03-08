<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin()) {
            show_error('Hanya Admin yang boleh mengakses halaman ini', 403, 'Akses dilarang');
        }
        $this->load->library('upload');
        $this->load->model('Settings_model', 'settings');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->helper('directory');
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
        $data = [
            'user'       => $user,
            'judul'      => 'Profile Sekolah',
            'subjudul'   => '',
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function dbManager()
    {
        $data = [
            'user'       => $this->ion_auth->user()->row(),
            'judul'      => 'Backup dan Restore',
            'subjudul'   => 'Backup dan Restore',
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'setting'    => $this->settings->getSetting(),
            'list'       => directory_map('./backups/'),
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/db');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function uploadFile($logo)
    {
        if (!isset($_FILES['logo']['name'])) {
            $this->output_json(['src' => '']);
            return;
        }

        $config = [
            'upload_path'   => './uploads/settings/',
            'allowed_types' => 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF',
            'overwrite'     => true,
            'file_name'     => $logo,
        ];
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('logo')) {
            $this->output_json(['src' => '', 'error' => $this->upload->display_errors()]);
            return;
        }

        $result = $this->upload->data();
        $this->output_json([
            'src'      => base_url() . 'uploads/settings/' . $result['file_name'],
            'filename' => pathinfo($result['file_name'], PATHINFO_FILENAME),
            'status'   => true,
            'type'     => $_FILES['logo']['type'],
            'size'     => $_FILES['logo']['size'],
        ]);
    }

    public function deleteFile()
    {
        $src       = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');

        if (unlink($file_name)) {
            echo 'File Delete Successfully';
        }
    }

    public function saveSetting()
    {
        $insert = [
            'sekolah'           => $this->input->post('nama_sekolah', true),
            'nss'               => $this->input->post('nss', true),
            'npsn'              => $this->input->post('npsn', true),
            'jenjang'           => $this->input->post('jenjang', true),
            'satuan_pendidikan' => $this->input->post('satuan_pendidikan', true),
            'alamat'            => $this->input->post('alamat', true),
            'desa'              => $this->input->post('desa', true),
            'kota'              => $this->input->post('kota', true),
            'kecamatan'         => $this->input->post('kec', true),
            'kode_pos'          => $this->input->post('kode_pos', true),
            'provinsi'          => $this->input->post('provinsi', true),
            'web'               => $this->input->post('web', true),
            'fax'               => $this->input->post('fax', true),
            'email'             => $this->input->post('email', true),
            'telp'              => $this->input->post('tlp', true),
            'kepsek'            => $this->input->post('kepsek', true),
            'nip'               => $this->input->post('nip', true),
            'tanda_tangan'      => str_replace(base_url(), '', $this->input->post('tanda_tangan', true) ?? ''),
            'nama_aplikasi'     => $this->input->post('nama_aplikasi', true),
            'logo_kanan'        => str_replace(base_url(), '', $this->input->post('logo_kanan', true) ?? ''),
            'logo_kiri'         => str_replace(base_url(), '', $this->input->post('logo_kiri', true) ?? ''),
        ];

        $this->db->where('id_setting', 1);
        $this->output_json($this->db->update('setting', $insert));
    }
}
