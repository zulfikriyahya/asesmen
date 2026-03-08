<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dbmanager extends CI_Controller
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
        $user    = $this->ion_auth->user()->row();
        $list    = directory_map('./backups/');
        $arrFile = [];

        foreach ($list as $key => $value) {
            $nfile = explode('.', $value ?? '');
            $type  = $nfile[1] ?? '';
            if ($type === 'html') continue;
            $arrFile[$key] = [
                'type' => $type,
                'nama' => $nfile[0],
                'tgl'  => filemtime('./backups/' . $value),
                'size' => $this->formatSizeUnits(filesize('./backups/' . $value)),
                'src'  => $value,
            ];
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Backup dan Restore',
            'subjudul'   => 'Backup Semua Database dan File',
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $this->dashboard->getTahunActive(),
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $this->dashboard->getSemesterActive(),
            'list'       => $arrFile,
            'tables'     => $this->db->list_tables(),
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/db');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function truncate()
    {
        $this->settings->truncate($this->db->list_tables());
        $this->output_json(['status' => true]);
    }

    public function backupDb()
    {
        $this->load->dbutil();
        $this->dbutil->optimize_database();

        $backup = $this->dbutil->backup([
            'tables'     => $this->db->list_tables(),
            'ignore'     => [],
            'format'     => 'zip',
            'filename'   => 'backup.sql',
            'add_drop'   => TRUE,
            'add_insert' => TRUE,
            'newline'    => "\n",
        ]);

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

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)       return number_format($bytes / 1024, 2) . ' KB';
        if ($bytes > 1)           return $bytes . ' bytes';
        if ($bytes == 1)          return '1 byte';
        return '0 bytes';
    }
}
