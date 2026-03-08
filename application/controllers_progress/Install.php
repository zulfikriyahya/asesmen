<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Install extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        include APPPATH . 'config/database.php';
        if (!($db['default']['database'] != '')) {
            $this->load->model('Install_model', 'install');
        } else {
            $this->load->database();
            $this->load->dbforge();
            $this->load->model('Install_model', 'install');
        }
        $this->load->model('Dashboard_model', 'dashboard');
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
        $res = $this->install->check_installer();
        if ($res == '0') {
            redirect('update');
        } else {
            if ($res == '2') {
            }
            if ($res == '3') {
            }
            $data['msg'] = 'belum ada data sekolah';
            $data = $this->getSaved();
            $data->error = $res;
            $this->load->view('install/header', ['data' => $data]);
            $this->load->view('install/step');
            $this->load->view('install/footer');
        }
    }
    function getSaved()
    {
        include APPPATH . 'config/database.php';
        $database = $db['default']['database'];
        $data['hostname'] = $db['default']['hostname'];
        $data['username'] = $db['default']['username'];
        $data['password'] = $db['default']['password'];
        $data['database'] = $database;
        $data['nama_admin'] = '';
        $data['user_admin'] = '';
        $data['pass_admin'] = '';
        $data['aplikasi'] = '';
        $data['sekolah'] = '';
        $data['jenjang'] = '';
        $data['satuan'] = '';
        $data['kepsek'] = '';
        $data['alamat'] = '';
        $data['desa'] = '';
        $data['kec'] = '';
        $data['kota'] = '';
        $data['prov'] = '';
        $current_page = 2;
        if ($this->db->table_exists('users')) {
            $admin = $this->db->get('users')->row();
            if (!($admin != null)) {
            }
            $data['nama_admin'] = $admin->first_name . ' ' . $admin->last_name;
            $data['user_admin'] = $admin->username;
            $data['pass_admin'] = $admin->password;
            $setting = $this->dashboard->getSetting();
            if (!($setting != null)) {
            }
            $data['aplikasi'] = $setting->nama_aplikasi;
            $data['sekolah'] = $setting->sekolah;
            $data['jenjang'] = $setting->jenjang;
            $data['satuan'] = $setting->satuan_pendidikan;
            $data['kepsek'] = $setting->kepsek;
            $data['alamat'] = $setting->alamat;
            $data['desa'] = $setting->desa;
            $data['kec'] = $setting->kecamatan;
            $data['kota'] = $setting->kota;
            $data['prov'] = $setting->provinsi;
            $current_page = $admin == null ? 2 : ($setting == null ? 3 : 4);
            $data['current_page'] = $current_page;
            return json_decode(json_encode($data));
        } else {
            $current_page = 2;
            $data['msg'] = 'Table `users` belum dibuat';
            $data['current_page'] = $current_page;
            return json_decode(json_encode($data));
        }
    }
    public function steps()
    {
        $data = $this->getSaved();
        $this->load->view('install/header', ['data' => $data]);
        $this->load->view('install/step');
        $this->load->view('install/footer');
    }
    public function checkDatabase()
    {
        $hostname = $this->input->post('hostname', true);
        $hostuser = $this->input->post('hostuser', true);
        $hostpass = $this->input->post('hostpass', true);
        $database = $this->input->post('database', true);
        if ($this->validate_host($hostname, $hostuser, $database)) {
            $template_path = './assets/app/db/database.php';
            $output_path = APPPATH . 'config/database.php';
            $database_file = file_get_contents($template_path);
            $new = str_replace('%HOSTNAME%', $hostname, $database_file);
            $new = str_replace('%USERNAME%', $hostuser, $new);
            $new = str_replace('%PASSWORD%', $hostpass, $new);
            $new = str_replace('%DATABASE%', $database, $new);
            $handle = fopen($output_path, 'w+');
            @chmod($output_path, 0777);
            if (is_writable($output_path)) {
            }
            $data['host'] = false;
            $data['host_msg'] = 'tidak ada akses ke file database.php, pastikan permission sudah dizinkan';
        } else {
            $data['host'] = false;
            $data['host_msg'] = 'tidak boleh ada yang kosong';
        }
        $this->output_json($data);
    }
    public function createDb()
    {
        $page = $this->input->post('page', true);
        if ($page == '0') {
            $hostname = $this->input->post('hostname', true);
            $hostuser = $this->input->post('hostuser', true);
            $hostpass = $this->input->post('hostpass', true);
            $database = $this->input->post('database', true);
            $data['table'] = $this->create_tables($hostname, $hostuser, $hostpass, $database);
            $data['host'] = true;
            $data['host_msg'] = 'sukses';
            $data['database'] = true;
        } else {
            $data['host'] = true;
            $data['host_msg'] = 'step salah';
            $data['database'] = false;
            $data['table'] = false;
        }
        $this->output_json($data);
    }
    function validate_host($host, $usr, $db)
    {
        return !empty($host) && !empty($usr) && !empty($db);
    }
    function create_database($hostname, $hostuser, $hostpass, $database)
    {
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, '');
        if (!mysqli_connect_errno()) {
            $mysqli->query('CREATE DATABASE IF NOT EXISTS ' . $database);
            $mysqli->close();
            return true;
        } else {
            return false;
        }
    }
    function create_tables($hostname, $hostuser, $hostpass, $database)
    {
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, $database);
        if (!mysqli_connect_errno()) {
            $query = file_get_contents('./assets/app/db/master.sql');
            $mysqli->multi_query($query);
            $mysqli->close();
            return true;
        } else {
            return false;
        }
    }
    public function createSetting()
    {
        $nama_aplikasi = $this->input->post('nama_aplikasi', true);
        $sekolah = $this->input->post('nama_sekolah', true);
        $jenjang = $this->input->post('jenjang', true);
        $satuan_pendidikan = $this->input->post('satuan_pendidikan', true);
        $kepsek = $this->input->post('kepsek', true);
        $alamat = $this->input->post('alamat', true);
        $kota = $this->input->post('kota', true);
        $kec = $this->input->post('kec', true);
        $desa = $this->input->post('desa', true);
        $tlp = $this->input->post('tlp', true);
        $insert = ['id_setting' => 1, 'sekolah' => $sekolah, 'jenjang' => $jenjang, 'satuan_pendidikan' => $satuan_pendidikan, 'alamat' => $alamat, 'desa' => $desa, 'kota' => $kota, 'kecamatan' => $kec, 'telp' => $tlp, 'kepsek' => $kepsek, 'nama_aplikasi' => $nama_aplikasi];
        $data['insert'] = $this->db->insert('setting', $insert);
        $data['saved'] = $this->getSaved();
        $this->output_json($data);
    }
    public function createAdmin()
    {
        $nama = $this->input->post('nama_lengkap', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $namaAdmin = explode(' ', $nama ?? '');
        $first_name = $namaAdmin[0];
        $last_name = end($namaAdmin);
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('1');
        $email = strtolower($nama ?? '') . '@admin.com';
        $create = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['admin'] = $create;
        $this->output_json($data);
    }
    public function createApp()
    {
        $nama = $this->input->post('nama_lengkap', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $nama_aplikasi = $this->input->post('nama_aplikasi', true);
        $sekolah = $this->input->post('nama_sekolah', true);
        $jenjang = $this->input->post('jenjang', true);
        $satuan_pendidikan = $this->input->post('satuan', true);
        $kepsek = $this->input->post('kepsek', true);
        $alamat = $this->input->post('alamat', true);
        $kota = $this->input->post('kota', true);
        $kec = $this->input->post('kec', true);
        $desa = $this->input->post('desa', true);
        $prov = $this->input->post('prov', true);
        $insert = ['id_setting' => 1, 'sekolah' => $sekolah, 'jenjang' => $jenjang, 'satuan_pendidikan' => $satuan_pendidikan, 'alamat' => $alamat, 'desa' => $desa, 'kota' => $kota, 'kecamatan' => $kec, 'provinsi' => $prov, 'kepsek' => $kepsek, 'nama_aplikasi' => $nama_aplikasi];
        $namaAdmin = explode(' ', $nama ?? '');
        $first_name = $namaAdmin[0];
        $last_name = end($namaAdmin);
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('1');
        $email = strtolower($nama ?? '') . '@admin.com';
        $create = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['insert'] = $this->db->insert('setting', $insert);
        $data['admin'] = $create;
        $this->output_json($data);
    }
}
