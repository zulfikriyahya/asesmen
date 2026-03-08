<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Install extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        include APPPATH . 'config/database.php';

        if ($db['default']['database'] != '') {
            $this->load->database();
            $this->load->dbforge();
        }

        $this->load->model('Install_model', 'install');
        $this->load->model('Dashboard_model', 'dashboard');
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
        $res = $this->install->check_installer();

        if ($res == '0') {
            redirect('update');
            return;
        }

        $data        = $this->getSaved();
        $data->error = $res;

        $this->load->view('install/header', ['data' => $data]);
        $this->load->view('install/step');
        $this->load->view('install/footer');
    }

    public function steps()
    {
        $data = $this->getSaved();
        $this->load->view('install/header', ['data' => $data]);
        $this->load->view('install/step');
        $this->load->view('install/footer');
    }

    private function getSaved()
    {
        include APPPATH . 'config/database.php';

        $data = [
            'hostname'    => $db['default']['hostname'],
            'username'    => $db['default']['username'],
            'password'    => $db['default']['password'],
            'database'    => $db['default']['database'],
            'nama_admin'  => '',
            'user_admin'  => '',
            'pass_admin'  => '',
            'aplikasi'    => '',
            'sekolah'     => '',
            'jenjang'     => '',
            'satuan'      => '',
            'kepsek'      => '',
            'alamat'      => '',
            'desa'        => '',
            'kec'         => '',
            'kota'        => '',
            'prov'        => '',
            'current_page' => 2,
        ];

        if (!$this->db->table_exists('users')) {
            $data['msg'] = 'Table `users` belum dibuat';
            return json_decode(json_encode($data));
        }

        $admin = $this->db->get('users')->row();

        if ($admin != null) {
            $data['nama_admin'] = $admin->first_name . ' ' . $admin->last_name;
            $data['user_admin'] = $admin->username;
            $data['pass_admin'] = $admin->password;
        }

        $setting = $this->dashboard->getSetting();

        if ($setting != null) {
            $data['aplikasi'] = $setting->nama_aplikasi;
            $data['sekolah']  = $setting->sekolah;
            $data['jenjang']  = $setting->jenjang;
            $data['satuan']   = $setting->satuan_pendidikan;
            $data['kepsek']   = $setting->kepsek;
            $data['alamat']   = $setting->alamat;
            $data['desa']     = $setting->desa;
            $data['kec']      = $setting->kecamatan;
            $data['kota']     = $setting->kota;
            $data['prov']     = $setting->provinsi;
        }

        if ($admin == null) {
            $data['current_page'] = 2;
        } elseif ($setting == null) {
            $data['current_page'] = 3;
        } else {
            $data['current_page'] = 4;
        }

        return json_decode(json_encode($data));
    }

    public function checkDatabase()
    {
        $hostname = $this->input->post('hostname', true);
        $hostuser = $this->input->post('hostuser', true);
        $hostpass = $this->input->post('hostpass', true);
        $database = $this->input->post('database', true);

        if (!$this->validate_host($hostname, $hostuser, $database)) {
            $this->output_json(['host' => false, 'host_msg' => 'Tidak boleh ada yang kosong']);
            return;
        }

        $template_path = './assets/app/db/database.php';
        $output_path   = APPPATH . 'config/database.php';

        $database_file = file_get_contents($template_path);
        $new = str_replace('%HOSTNAME%', $hostname, $database_file);
        $new = str_replace('%USERNAME%', $hostuser, $new);
        $new = str_replace('%PASSWORD%', $hostpass, $new);
        $new = str_replace('%DATABASE%', $database, $new);

        @chmod($output_path, 0777);

        if (!is_writable($output_path)) {
            $this->output_json(['host' => false, 'host_msg' => 'Tidak ada akses ke file database.php, pastikan permission sudah diizinkan']);
            return;
        }

        $handle = fopen($output_path, 'w+');
        fwrite($handle, $new);
        fclose($handle);

        $this->output_json(['host' => true, 'host_msg' => 'Sukses']);
    }

    public function createDb()
    {
        $page = $this->input->post('page', true);

        if ($page != '0') {
            $this->output_json(['host' => true, 'host_msg' => 'Step salah', 'database' => false, 'table' => false]);
            return;
        }

        $hostname = $this->input->post('hostname', true);
        $hostuser = $this->input->post('hostuser', true);
        $hostpass = $this->input->post('hostpass', true);
        $database = $this->input->post('database', true);

        $this->output_json([
            'table'    => $this->create_tables($hostname, $hostuser, $hostpass, $database),
            'host'     => true,
            'host_msg' => 'Sukses',
            'database' => true,
        ]);
    }

    private function validate_host($host, $usr, $db)
    {
        return !empty($host) && !empty($usr) && !empty($db);
    }

    private function create_database($hostname, $hostuser, $hostpass, $database)
    {
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, '');
        if (mysqli_connect_errno()) return false;
        $mysqli->query('CREATE DATABASE IF NOT EXISTS ' . $database);
        $mysqli->close();
        return true;
    }

    private function create_tables($hostname, $hostuser, $hostpass, $database)
    {
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, $database);
        if (mysqli_connect_errno()) return false;
        $query = file_get_contents('./assets/app/db/master.sql');
        $mysqli->multi_query($query);
        $mysqli->close();
        return true;
    }

    public function createSetting()
    {
        $insert = [
            'id_setting'       => 1,
            'sekolah'          => $this->input->post('nama_sekolah', true),
            'jenjang'          => $this->input->post('jenjang', true),
            'satuan_pendidikan' => $this->input->post('satuan_pendidikan', true),
            'alamat'           => $this->input->post('alamat', true),
            'desa'             => $this->input->post('desa', true),
            'kota'             => $this->input->post('kota', true),
            'kecamatan'        => $this->input->post('kec', true),
            'telp'             => $this->input->post('tlp', true),
            'kepsek'           => $this->input->post('kepsek', true),
            'nama_aplikasi'    => $this->input->post('nama_aplikasi', true),
        ];

        $this->output_json([
            'insert' => $this->db->insert('setting', $insert),
            'saved'  => $this->getSaved(),
        ]);
    }

    public function createAdmin()
    {
        $nama     = $this->input->post('nama_lengkap', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $parts    = explode(' ', $nama ?? '');

        $this->output_json([
            'admin' => $this->ion_auth->register(
                $username,
                $password,
                strtolower($nama ?? '') . '@admin.com',
                ['first_name' => $parts[0], 'last_name' => end($parts)],
                ['1']
            ),
        ]);
    }

    public function createApp()
    {
        $nama     = $this->input->post('nama_lengkap', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $parts    = explode(' ', $nama ?? '');

        $insert = [
            'id_setting'       => 1,
            'sekolah'          => $this->input->post('nama_sekolah', true),
            'jenjang'          => $this->input->post('jenjang', true),
            'satuan_pendidikan' => $this->input->post('satuan', true),
            'alamat'           => $this->input->post('alamat', true),
            'desa'             => $this->input->post('desa', true),
            'kota'             => $this->input->post('kota', true),
            'kecamatan'        => $this->input->post('kec', true),
            'provinsi'         => $this->input->post('prov', true),
            'kepsek'           => $this->input->post('kepsek', true),
            'nama_aplikasi'    => $this->input->post('nama_aplikasi', true),
        ];

        $this->output_json([
            'insert' => $this->db->insert('setting', $insert),
            'admin'  => $this->ion_auth->register(
                $username,
                $password,
                strtolower($nama ?? '') . '@admin.com',
                ['first_name' => $parts[0], 'last_name' => end($parts)],
                ['1']
            ),
        ]);
    }
}
