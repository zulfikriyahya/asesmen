<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dbmanager extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
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
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Backup dan Restore', 'subjudul' => 'Backup Semua Database dan File', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $list = directory_map('./backups/');
        $arrFile = [];
        foreach ($list as $key => $value) {
            $nfile = explode('.', $value ?? '');
            $nama = $nfile[0];
            $type = $nfile[1];
            $tgl = filemtime('./backups/' . $value);
            $size = $this->formatSizeUnits(filesize('./backups/' . $value));
            if (!($type !== 'html')) {
            } else {
                $arrFile[$key] = ['type' => $type, 'nama' => $nama, 'tgl' => $tgl, 'size' => $size, 'src' => $value];
            }
        }
        $data['list'] = $arrFile;
        $data['tables'] = $this->db->list_tables();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/db');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function manage()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Bersihkan Data', 'subjudul' => 'Hapus Data', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data_tables = [];
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            $data_tables[$table] = $this->settings->toJSON($table);
        }
        $data['tables'] = $data_tables;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/manage');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function truncate()
    {
        $tables = $this->db->list_tables();
        $this->settings->truncate($tables);
        $this->output_json(['status' => true]);
    }
    public function backupDb()
    {
        $this->load->dbutil();
        $this->dbutil->optimize_database();
        $prefs = ['tables' => $this->db->list_tables(), 'ignore' => array(), 'format' => 'zip', 'filename' => 'backup.sql', 'add_drop' => TRUE, 'add_insert' => TRUE, 'newline' => '
'];
        $backup = $this->dbutil->backup($prefs);
        $this->load->helper('file');
        write_file('./backups/backup-db-' . date('Y-m-d-H-i-s') . '.sql.zip', $backup);
        $this->output_json(['type' => 'database', 'message' => 'Database berhasil dibackup']);
    }
    public function backupData()
    {
        $this->load->library('zip');
        $this->zip->read_dir('uploads');
        $this->zip->archive('./backups/backup-file-' . date('Y-m-d-H-i-s') . '.zip');
        $this->output_json(['type' => 'file', 'message' => 'File data berhasil dibackup']);
    }
    public function hapusBackup($src)
    {
        if (unlink('./backups/' . $src)) {
            $this->output_json(['status' => true, 'message' => 'Backup berhasil dihapus']);
        } else {
            $this->output_json(['status' => false, 'message' => 'Gagal menghapus backup']);
        }
    }
    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            return $bytes;
        } else {
            if ($bytes >= 1048576) {
            }
            if ($bytes >= 1024) {
            }
            if ($bytes > 1) {
            }
            if ($bytes == 1) {
            }
            $bytes = '0 bytes';
            return $bytes;
        }
    }
}