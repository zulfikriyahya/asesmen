<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dbclear extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if ($this->ion_auth->is_admin()) {
        }
        show_error('Hanya Admin yang boleh mengakses halaman ini', 403, 'Akses dilarang');
        $this->load->library('upload');
        $this->load->dbforge();
        $this->load->model('Settings_model', 'settings');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->helper('directory');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Bersihkan Data', 'subjudul' => 'Hapus Data', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $excludes = ['buku_induk', 'api_setting', 'api_token', 'bulan', 'hari', 'setting', 'cbt_jenis', 'cbt_ruang', 'cbt_sesi', 'cbt_token', 'level_guru', 'level_kelas', 'master_tp', 'master_smt', 'master_hari_efektif', 'users', 'groups', 'users_groups', 'login_attempts', 'users_profile', 'rapor_admin_setting', 'running_text'];
        $data_tables = [];
        $tables = $this->db->list_tables();
        foreach ($tables as $table) {
            if (isset($json[$table])) {
            }
            if (in_array($table, $excludes)) {
            }
            if ($table == 'buku_nilai') {
            }
            $this->dbforge->drop_table($table, true);
        }
        $data['tables'] = $data_tables;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/manage');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function hapusTable()
    {
        $table = $this->input->post('table', true);
        $this->load->dbutil();
        $prefs = ['tables' => array($table), 'ignore' => array(), 'format' => 'txt', 'filename' => $table . '.sql', 'add_drop' => TRUE, 'add_insert' => TRUE, 'newline' => '
'];
        $backup = $this->dbutil->backup(array($prefs));
        $this->load->helper('file');
        write_file('./backups/backup_' . $table . '_' . date('Y_m_d_H_i_s') . '.sql', $backup);
        $this->db->truncate($table);
        $this->output_json(['type' => 'database', 'message' => 'Database berhasil dihapus']);
    }
    public function truncate()
    {
        $tables = $this->db->list_tables();
        $this->settings->truncate($tables);
        $this->output_json(['status' => true]);
    }
    private function keterangan()
    {
        $data = ['api_setting' => '1', 'api_token' => '1', 'buku_induk' => '1', 'bulan' => '0', 'cbt_bank_soal' => '2', 'cbt_durasi_siswa' => '2', 'cbt_jadwal' => '2', 'cbt_jadwal_ujian' => '2', 'cbt_jenis' => '0', 'cbt_kelas_ruang' => '2', 'cbt_kop_absensi' => '1', 'cbt_kop_berita' => '1', 'cbt_kop_kartu' => '1', 'cbt_nilai' => '2', 'cbt_nomor_peserta' => '2', 'cbt_pengawas' => '2', 'cbt_rekap' => '2', 'cbt_rekap_nilai' => '2', 'cbt_ruang' => '1', 'cbt_sesi' => '1', 'cbt_sesi_siswa' => '2', 'cbt_soal' => '2', 'cbt_soal_siswa' => '2', 'cbt_token' => '1', 'groups' => '0', 'hari' => '0', 'jabatan_guru' => '1', 'kelas_catatan_mapel' => '2', 'kelas_catatan_wali' => '2', 'kelas_ekstra' => '1', 'kelas_jadwal_kbm' => '2', 'kelas_jadwal_mapel' => '2', 'kelas_jadwal_materi' => '2', 'kelas_jadwal_tugas' => '2', 'kelas_materi' => '2', 'kelas_siswa' => '2', 'kelas_struktur' => '2', 'kelas_tugas' => '2', 'level_guru' => '0', 'level_kelas' => '0', 'log' => '2', 'login_attempts' => '0', 'log_materi' => '2', 'log_tugas' => '2', 'log_ujian' => '2', 'master_ekstra' => '1', 'master_guru' => '1', 'master_hari_efektif' => '1', 'master_jurusan' => '1', 'master_kelas' => '1', 'master_kelompok_mapel' => '1', 'master_mapel' => '1', 'master_siswa' => '1', 'master_smt' => '0', 'master_tp' => '0', 'post' => '2', 'post_comments' => '2', 'post_reply' => '2', 'rapor_admin_setting' => '1', 'rapor_catatan_wali' => '1', 'rapor_data_catatan' => '1', 'rapor_data_fisik' => '1', 'rapor_data_sikap' => '1', 'rapor_fisik' => '1', 'rapor_kikd' => '1', 'rapor_kkm' => '1', 'rapor_naik' => '1', 'rapor_nilai_akhir' => '1', 'rapor_nilai_ekstra' => '1', 'rapor_nilai_harian' => '1', 'rapor_nilai_pts' => '1', 'rapor_nilai_sikap' => '1', 'rapor_prestasi' => '1', 'running_text' => '1', 'setting' => '1', 'users' => '0', 'users_groups' => '0', 'users_profile' => '0'];
        return $data;
    }
}