<?php

class Kelasmateri extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->helper('my');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Log_model', 'logging');
        $this->form_validation->set_error_delimiters('', '');
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
        $jenis = $this->input->get('jenis');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Materi Belajar', 'subjudul' => 'Materi', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $materi = $this->kelas->getAllMateriKelas($guru->id_guru, '1');
        $kelas_materi = [];
        $jadwal_materi = [];
        foreach ($materi as $m) {
            $kelas_materi[$m->id_materi] = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, $jenis, $tp->id_tp, $smt->id_smt);
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['gurus'] = $nguru;
        $data['guru'] = $guru;
        $data['id_guru'] = $guru->id_guru;
        $data['materi'] = $materi;
        $data['kelas_materi'] = $kelas_materi;
        $data['jadwal_materi'] = $jadwal_materi;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('kelas/materi/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function materi()
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Materi Belajar', 'subjudul' => 'Materi', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $arr_kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $arr_kelas;
        $data['jenis'] = '1';
        $jadmpl = $this->kelas->getJadwalMapel($tp->id_tp, $smt->id_smt);
        $data['jadwal_mapel'] = $jadmpl;
        $arr_h = [];
        foreach ($jadmpl as $j => $h) {
            foreach ($h as $v) {
                foreach ($v as $kk => $vk) {
                    if (isset($arr_h[$vk->id_mapel])) {
                    }
                    $arr_h[$vk->id_mapel] = [];
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        $data['tanggal_jadwal'] = $arr_h;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $materi = $this->kelas->getAllMateriKelas($guru->id_guru, '1');
        $kelas_materi = [];
        $jadwal_materi = [];
        foreach ($materi as $m) {
            $kelas_materi[$m->id_materi] = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '1', $tp->id_tp, $smt->id_smt);
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['gurus'] = $nguru;
        $data['guru'] = $guru;
        $data['id_guru'] = $guru->id_guru;
        $data['materi'] = $materi;
        $data['kelas_materi'] = $kelas_materi;
        $data['jadwal_materi'] = $jadwal_materi;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('kelas/materi/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function tugas()
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Tugas Kelas', 'subjudul' => 'Tugas', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $arr_kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $arr_kelas;
        $data['jenis'] = '2';
        $jadmpl = $this->kelas->getJadwalMapel($tp->id_tp, $smt->id_smt);
        $data['jadwal_mapel'] = $jadmpl;
        $arr_h = [];
        foreach ($jadmpl as $j => $h) {
            foreach ($h as $v) {
                foreach ($v as $kk => $vk) {
                    if (isset($arr_h[$vk->id_mapel])) {
                    }
                    $arr_h[$vk->id_mapel] = [];
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        $data['tanggal_jadwal'] = $arr_h;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $materi = $this->kelas->getAllMateriKelas($guru->id_guru, '2');
        $kelas_materi = [];
        $jadwal_materi = [];
        foreach ($materi as $m) {
            $kelas_materi[$m->id_materi] = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '2', $tp->id_tp, $smt->id_smt);
        }
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['gurus'] = $nguru;
        $data['guru'] = $guru;
        $data['id_guru'] = $guru->id_guru;
        $data['materi'] = $materi;
        $data['kelas_materi'] = $kelas_materi;
        $data['jadwal_materi'] = $jadwal_materi;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('kelas/materi/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function data($guru = null)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->kelas->getMateriKelas($guru, $tp->id_tp, $smt->id_smt), false);
    }
    public function add($jenis, $id_materi = null)
    {
        $title = $jenis == '1' ? 'Materi' : 'Tugas';
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => $title, 'subjudul' => $id_materi == null ? 'Buat ' . $title . ' Baru' : 'Edit ' . $title, 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['id_materi'] = $id_materi;
        $data['jenis'] = $jenis;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if ($id_materi == null) {
        }
        $data['materi'] = $this->kelas->getMateriKelasById($id_materi, $jenis);
        $nguru[$guru->id_guru] = $guru->nama_guru;
        $data['gurus'] = $nguru;
        $data['guru'] = $guru;
        $data['id_guru'] = $guru->id_guru;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('kelas/materi/add');
        $this->load->view('members/guru/templates/footer');
    }
    public function dataAddKelas($guru)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->kelas->getGuruMapelKelas($guru, $tp->id_tp, $smt->id_smt);
        $kelas = unserialize($guru->mapel_kelas);
        $this->output_json($kelas);
    }
    public function dataAddJadwal()
    {
        $id_kelas = $this->input->get('kelas');
        $id_mapel = $this->input->get('mapel');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $mapel = $this->kelas->getJadwalMapelByMapel($id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
        $jadwal_terisi = $this->kelas->getJadwalTerisi('kelas_jadwal_materi', $id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
        $this->output_json(['mapel' => $mapel, 'terisi' => $jadwal_terisi]);
    }
    public function saveJadwal()
    {
        $id_materi = $this->input->post('id_materi', true);
        $id_mapel = $this->input->post('id_mapel', true);
        $id_kelas = $this->input->post('id_kelas', true);
        $jenis = $this->input->post('jenis', true);
        $jam_ke = $this->input->post('jam_ke', true);
        $jadwal = $this->input->post('jadwal_materi', true);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $jdwl = str_replace('-', '', $jadwal ?? '');
        $insert = ['id_kjm' => $id_kelas . $tp->id_tp . $smt->id_smt . $jdwl . $jam_ke . $jenis, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $id_kelas, 'id_materi' => $id_materi, 'id_mapel' => $id_mapel, 'jadwal_materi' => $jadwal, 'jenis' => $jenis];
        $update = $this->db->replace('kelas_jadwal_materi', $insert);
        $this->logging->saveLog(3, 'merubah jadwal materi');
        $this->output_json($update);
    }
    public function hapusJadwal($id)
    {
        $this->db->set('id_materi', '0');
        $this->db->where('id_kjm', $id);
        $update = $this->db->update('kelas_jadwal_materi');
        $this->output_json($update);
    }
    public function saveMateri()
    {
        $jenis = $this->input->post('jenis', true);
        $id_materi = $this->input->post('id_materi', true);
        $kelas = count($this->input->post('kelas', true));
        $attach = json_decode($this->input->post('attach', true));
        $src_file = [];
        foreach ($attach as $at) {
            if (!($at->name != null)) {
            }
            $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
        }
        $id_kelas = [];
        $i = 0;
        if (!($i < $kelas)) {
        }
        $id_kelas[] = $this->input->post('kelas[' . $i . ']', true);
        $i++;
    }
    public function copyMateri($id_materi, $jenis)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $materi = $this->kelas->getMateriKelasById($id_materi, $jenis);
        $data = ['jenis' => $jenis, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'kode_materi' => $materi->kode_materi, 'id_guru' => $materi->id_guru, 'id_mapel' => $materi->id_mapel == null ? 0 : $materi->id_mapel, 'judul_materi' => $materi->judul_materi, 'isi_materi' => $materi->isi_materi, 'materi_kelas' => $materi->materi_kelas, 'file' => $materi->file, 'created_on' => date('Y-m-d H:i:s'), 'updated_on' => date('Y-m-d H:i:s')];
        $result = $this->master->create('kelas_materi', $data);
        $this->logging->saveLog(3, 'membuat materi');
        $this->output_json($result);
    }
    public function aktifkanMateri()
    {
        $method = $this->input->post('method', true);
        $id = $this->input->post('id_materi', true);
        $stat = $method == '1' ? '0' : '1';
        $this->db->set('status', $stat);
        $this->db->where('id_materi', $id);
        $this->db->update('kelas_materi');
        $this->logging->saveLog(3, 'mengaktifkan materi');
        $this->output_json(['status' => true]);
    }
    public function hapusMateri()
    {
        $id = $this->input->post('id_materi', true);
        if (!$this->master->delete('kelas_materi', $id, 'id_materi')) {
        }
        if (!$this->master->delete('kelas_jadwal_materi', $id, 'id_materi')) {
        }
        $this->logging->saveLog(5, 'menghapus materi');
        $this->output_json(['status' => true]);
    }
    public function deleteAllMateri()
    {
        $ids = json_decode($this->input->post('ids', true));
        if (!$this->master->delete('kelas_materi', $ids, 'id_materi')) {
        }
        if (!$this->master->delete('kelas_jadwal_materi', $ids, 'id_materi')) {
        }
        $this->logging->saveLog(5, 'menghapus materi');
        $this->output_json(['status' => true]);
    }
    function uploadFile()
    {
        $max_size = $this->input->post('max-size', true);
        if (!isset($_FILES['file_uploads']['name'])) {
        }
        $config['upload_path'] = './uploads/materi/';
        $config['allowed_types'] = 'jpg|jpeg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi|doc|docx|xls|xlsx|ppt|pptx|csv|pdf|rtf|txt';
        $config['max_size'] = $max_size;
        $config['overwrite'] = TRUE;
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file_uploads')) {
        }
        $result = $this->upload->data();
        $data['src'] = 'uploads/materi/' . $result['file_name'];
        $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
        $data['status'] = true;
        $data['type'] = $_FILES['file_uploads']['type'];
        $data['size'] = $_FILES['file_uploads']['size'];
        $this->output_json($data);
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        if (unlink($src)) {
        }
        echo 'Gagal';
    }
    function getListDate($day, $month, $year)
    {
        $list = array();
        $numdays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $d = 1;
        if (!($d <= $numdays)) {
            return $list;
        } else {
            $time = mktime(12, 0, 0, $month, $d, $year);
            $day_of_week = date('N', $time);
            if (!(date('m', $time) == $month && $day_of_week == $day)) {
                goto BiAq_;
            }
            array_push($list, date('Y-m-d', $time));
            $d++;
            if (!($d <= $numdays)) {
                goto Gkmuo;
            }
        }
    }
}