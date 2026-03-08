<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Siswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('user_agent');
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

    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if ($subArray) sort($subArray);
        }
    }

    public function index() {}

    private function arrToUpper($val)
    {
        return strtoupper($val ?? '');
    }

    public function getPost()
    {
        $this->load->model('Post_model', 'post');
        $kode = $this->input->get('kelas', true);
        $post = $this->post->getPostForUser('\'%siswa%\'', '\'%' . $kode . '%\'');
        $this->output_json($post);
    }

    public function getComment($id_post, $page)
    {
        $perPage = 5;
        $offset  = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_post', $id_post);
        $this->db->limit($perPage, $offset);
        $this->output_json($this->db->get()->result());
    }

    public function getReplies($id_comment, $page)
    {
        $perPage = 5;
        $offset  = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id_comment);
        $this->db->limit($perPage, $offset);
        $this->output_json($this->db->get()->result());
    }

    public function saveKomentar()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $data = ['type' => '1', 'id_post' => $this->input->post('id_post'), 'dari' => $siswa->id_siswa, 'dari_group' => 3, 'text' => $this->input->post('text')];
        $this->db->replace('post_comments', $data);
        $id = $this->db->insert_id();

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id);
        $this->output_json($this->db->get()->result());
    }

    public function saveBalasan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $siswa->id_siswa, 'dari_group' => 3, 'text' => $this->input->post('text')];
        $this->db->replace('post_reply', $data);
        $id = $this->db->insert_id();

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_reply', $id);
        $this->output_json($this->db->get()->result());
    }

    public function jadwalPelajaran()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $jadwal_kbm = $jadk == null
            ? json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $siswa->id_kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]))
            : $jadk;

        $jadm          = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $jadwal_mapel  = [];
        if ($jadm != null) {
            foreach ($jadm as $j) {
                $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $siswa->id_kelas)];
            }
        }

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Jadwal Pelajaran',
            'subjudul'     => 'Set Jadwal Pelajaran',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'jadwal_kbm'   => $jadwal_kbm,
            'id_kelas'     => $siswa->id_kelas,
            'method'       => 'edit',
            'jadwal_mapel' => $jadwal_mapel,
            'mapels'       => $this->master->getAllMapel(),
            'running_text' => $this->dashboard->getRunningText(),
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/jadwal/data');
        $this->load->view('members/siswa/templates/footer');
    }

    public function kehadiran()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $today   = date('Y-m-d');
        $day     = date('N', strtotime($today));
        $kbm     = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $result  = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, $siswa->id_kelas, null);
        $jadwals = [];
        foreach ($result as $row) {
            $jadwals[$row->id_hari][$row->jam_ke] = $row;
        }

        $mapels     = $this->master->getAllMapel();
        $arrIdMapel = array_column($mapels, 'id_mapel');

        $sebulan = ['log' => [], 'materis' => []];
        if ($kbm != null) {
            $bulan  = date('m');
            $tahun  = date('Y');
            $tgl    = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $materi_sebulan = [];
            for ($i = 0; $i < $tgl; $i++) {
                $t = $i + 1 < 10 ? '0' . ($i + 1) : ($i + 1);
                $materi_sebulan[$t] = $this->kelas->getAllMateriByTgl($siswa->id_kelas, $tahun . '-' . $bulan . '-' . $t, $arrIdMapel);
            }
            $sebulan = ['log' => $materi_sebulan, 'materis' => $materi_sebulan];
        }

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Absensi',
            'subjudul'     => 'Kehadiran Siswa',
            'setting'      => $this->dashboard->getSetting(),
            'sebulan'      => $sebulan,
            'kbm'          => $kbm,
            'mapels'       => $mapels,
            'jadwals'      => $jadwals,
            'jadwal'       => isset($jadwals[$day]) && $day != 7 ? $jadwals[$day] : [],
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'running_text' => $this->dashboard->getRunningText(),
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/absensi/data');
        $this->load->view('members/siswa/templates/footer');
    }

    public function materi()
    {
        $this->getTugasMateri('1');
    }

    public function tugas()
    {
        $this->getTugasMateri('2');
    }

    private function getTugasMateri($jenis)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $user    = $this->ion_auth->user()->row();
        $siswa   = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $setting = $this->dashboard->getSetting();

        $jadwal_seminggu = $this->kelas->loadJadwalSiswaSeminggu($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $materi_seminggu = $this->kelas->getMateriSiswaSeminggu($tp->id_tp, $smt->id_smt, $siswa->id_kelas, $jenis);
        $mapels = $this->dropdown->getAllMapel();
        $last_week = [
            date('Y-m-d', strtotime('-7 days')),
            date('Y-m-d', strtotime('-6 days')),
            date('Y-m-d', strtotime('-5 days')),
            date('Y-m-d', strtotime('-4 days')),
            date('Y-m-d', strtotime('-3 days')),
            date('Y-m-d', strtotime('-2 days')),
            date('Y-m-d', strtotime('-1 days')),
            date('Y-m-d'),
        ];

        $materis = [];
        $logs    = [];
        foreach ($last_week as $day) {
            $idhari      = date('N', strtotime($day));
            $materis[$day] = [];
            if (!isset($jadwal_seminggu[$idhari])) continue;

            foreach ($jadwal_seminggu[$idhari] as $kjam => $val) {
                $dummy             = new stdClass();
                $dummy->id_mapel   = $val->id_mapel;
                $dummy->id_jadwal  = $val->id_jadwal;
                $dummy->nama_mapel = isset($mapels[$val->id_mapel]) ? $mapels[$val->id_mapel] : '';
                $materis[$day][$kjam] = isset($materi_seminggu[$day][$kjam]) ? $materi_seminggu[$day][$kjam] : $dummy;
            }

            $arrIdKjms = [];
            foreach ($materis[$day] as $mtr) {
                if (isset($mtr->id_kjm)) $arrIdKjms[] = $mtr->id_kjm;
            }
            $logs[$day] = count($arrIdKjms) > 0
                ? $this->kelas->getStatusMateriSiswaByJadwal($siswa->id_siswa, $arrIdKjms)
                : [];
        }

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => $jenis == '1' ? 'Materi' : 'Tugas',
            'subjudul'     => $jenis == '1' ? 'materi' : 'tugas',
            'setting'      => $setting,
            'week'         => $last_week,
            'jadwals'      => $jadwal_seminggu,
            'materis'      => $materis,
            'logs'         => $logs,
            'jenis'        => $jenis,
            'kbm'          => $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'jurusan'      => $this->dropdown->getAllJurusan(),
            'level'        => $this->dropdown->getAllLevel($setting->jenjang),
            'kelas'        => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'running_text' => $this->dashboard->getRunningText(),
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/materi/data');
        $this->load->view('members/siswa/templates/footer');
    }

    public function seminggu()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $id_siswa = $this->input->get('id_siswa', true);
        $id_kelas = $this->input->get('id_kelas', true);
        $tgl      = $this->input->get('tgl', true);
        $jenis    = $this->input->get('jenis', true);
        $mapels   = $this->dropdown->getAllMapel();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $numday   = date('N', strtotime($tgl));
        $jadwal   = $this->kelas->loadJadwalSiswaHariIni($tp->id_tp, $smt->id_smt, $id_kelas, $numday);
        $materi_hari_ini = $this->kelas->getMateriSiswa($id_kelas, $tgl, $jenis);

        $materi = [];
        foreach ($jadwal as $key => $value) {
            $materi['materi'][$key] = isset($materi_hari_ini[$key])
                ? $materi_hari_ini[$key]
                : ['id_mapel' => $value->id_mapel, 'id_jadwal' => $value->id_jadwal, 'nama_mapel' => isset($mapels[$value->id_mapel]) ? $mapels[$value->id_mapel] : ''];
        }

        $arrIdKjm = [];
        foreach ($materi['materi'] as $mtr) {
            if (isset($mtr->id_kjm)) $arrIdKjm[] = $mtr->id_kjm;
        }

        $materi['jadwal'] = $jadwal;
        if (count($arrIdKjm) > 0) {
            $materi['logs'] = (array) $this->kelas->getStatusMateriSiswaByJadwal($id_siswa, $arrIdKjm);
        }

        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $id_kelas);
        $jadk->istirahat    = unserialize($jadk->istirahat ?? '');
        $materi['kbm']      = $jadk;
        $materi['seminggu'] = $this->kelas->loadJadwalSiswaSeminggu($tp->id_tp, $smt->id_smt, $id_kelas);

        $this->output_json($materi);
    }

    public function bukaMateri($id_kjm, $jamke)
    {
        $this->bukaTugasMateri($id_kjm, $jamke, '1');
    }

    public function bukaTugas($id_kjm, $jamke)
    {
        $this->bukaTugasMateri($id_kjm, $jamke, '2');
    }

    private function bukaTugasMateri($id_kjm, $jamke, $jenis)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $logs = $this->kelas->getStatusMateriSiswa($id_kjm);
        if (isset($logs[$siswa->id_siswa])) {
            $logs[$siswa->id_siswa]->file = unserialize($logs[$siswa->id_siswa]->file ?? '');
        }

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => $jenis == '1' ? 'Materi' : 'Tugas',
            'subjudul'     => 'Kerjakan',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'jamke'        => $jamke,
            'materi'       => $this->kelas->getMateriKelasSiswa($id_kjm, $jenis),
            'kjm'          => $id_kjm,
            'logs'         => isset($logs[$siswa->id_siswa]) ? $logs[$siswa->id_siswa] : null,
            'running_text' => $this->dashboard->getRunningText(),
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/materi/view');
        $this->load->view('members/siswa/templates/footer');
    }

    public function saveLogMateri()
    {
        $this->load->model('Kelas_model', 'kelas');
        $this->output_json($this->kelas->saveLog('log_materi', $this->input->get('id_siswa', true), $this->input->get('id_kjm', true), $this->input->get('jamke', true), $this->input->get('mapel', true), 'Membuka materi'));
    }

    public function saveLogTugas()
    {
        $this->load->model('Kelas_model', 'kelas');
        $this->output_json($this->kelas->saveLog('log_materi', $this->input->get('id_siswa', true), $this->input->get('id_kjm', true), $this->input->get('jamke', true), $this->input->get('mapel', true), 'Membuka tugas'));
    }

    public function saveFileMateriSelesai()
    {
        $id_siswa   = $this->input->post('id_siswa', true);
        $id_kjm     = $this->input->post('id_kjm', true);
        $isi_materi = $this->input->post('isi_materi', true);
        $jamke      = $this->input->post('jamke', true);
        $attach     = json_decode($this->input->post('attach', true));

        $src_file = [];
        foreach ($attach as $at) {
            if ($at->name != null) {
                $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
            }
        }

        $id_log = $id_siswa . $id_kjm;
        $insert = ['id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'finish_time' => date('Y-m-d H:i:s'), 'jam_ke' => $jamke, 'log_desc' => 'Menyelesaikan materi', 'text' => $isi_materi, 'file' => serialize($src_file)];

        $this->db->where('id_log', $id_log);
        if ($this->db->get('log_materi')->num_rows() > 0) {
            $this->db->where('id_log', $id_log);
            $update = $this->db->update('log_materi', $insert);
        } else {
            $this->db->set('id_log', $id_log);
            $update = $this->db->insert('log_materi', $insert);
        }
        $this->output_json(['status' => $update]);
    }

    public function saveFileTugasSelesai()
    {
        $id_siswa  = $this->input->post('id_siswa', true);
        $id_kjm    = $this->input->post('id_kjm', true);
        $isi_tugas = $this->input->post('isi_tugas', true);
        $jamke     = $this->input->post('jamke', true);
        $attach    = json_decode($this->input->post('attach', true));

        $src_file = [];
        foreach ($attach as $at) {
            if ($at->name != null) {
                $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
            }
        }

        $id_log = $id_siswa . $id_kjm;
        $insert = ['id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'jam_ke' => $jamke, 'log_desc' => 'Menyelesaikan tugas', 'text' => $isi_tugas, 'file' => serialize($src_file)];

        $this->db->where('id_log', $id_log);
        if ($this->db->get('log_tugas')->num_rows() > 0) {
            $this->db->where('id_log', $id_log);
            $update = $this->db->update('log_tugas', $insert);
        } else {
            $this->db->set('id_log', $id_log);
            $update = $this->db->insert('log_tugas', $insert);
        }
        $this->output_json(['status' => $update]);
    }

    public function uploadFile()
    {
        $max_size = $this->input->post('max-size', true);
        if (!isset($_FILES['file_uploads']['name'])) {
            $this->output_json([]);
            return;
        }
        $config = [
            'upload_path'   => './uploads/file_siswa/',
            'allowed_types' => 'jpg|jpeg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi|doc|docx|xls|xlsx|ppt|pptx|csv|pdf|rtf|txt',
            'max_size'      => $max_size,
            'overwrite'     => false,
        ];
        $this->upload->initialize($config);
        if (!$this->upload->do_upload('file_uploads')) {
            $this->output_json(['error' => $this->upload->display_errors()]);
            return;
        }
        $result = $this->upload->data();
        $this->output_json([
            'src'      => 'uploads/file_siswa/' . $result['file_name'],
            'filename' => pathinfo($result['file_name'], PATHINFO_FILENAME),
            'status'   => true,
            'type'     => $_FILES['file_uploads']['type'],
            'size'     => $_FILES['file_uploads']['size'],
        ]);
    }

    public function deleteFile()
    {
        $src = $this->input->post('src');
        if (unlink($src)) {
            echo 'File Delete Successfully';
        }
    }

    public function leavecbt($id_jadwal, $id_siswa)
    {
        $this->db->set('agent', 'illegal agent');
        $this->db->set('device', 'illegal device');
        $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1');
        $this->db->update('log_ujian');
        redirect('logout', 'refresh');
    }

    public function cbt()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $today    = strtotime(date('Y-m-d'));
        $cbt_info = $this->cbt->getSiswaCbtInfo($siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $cbt_info->no_peserta = $this->cbt->getNomorPeserta($siswa->id_siswa);
        $cbt_jadwal           = $this->cbt->getJadwalCbt($tp->id_tp, $smt->id_smt, $siswa->level_id);

        $jadwal_ujian_aktif = [];
        $timer              = [];
        foreach ($cbt_jadwal as $jadwal) {
            $kk           = unserialize($jadwal->bank_kelas ?? '');
            $arrKelasCbt  = array_column($kk, 'kelas_id');
            $timer[$jadwal->id_jadwal] = $this->cbt->getElapsed($siswa->id_siswa . '0' . $jadwal->id_jadwal);

            if (!($cbt_info != null && in_array($cbt_info->id_kelas, $arrKelasCbt) && $jadwal->status === '1')) continue;

            $mulai   = strtotime($jadwal->tgl_mulai);
            $selesai = strtotime($jadwal->tgl_selesai);
            if (!($today >= $mulai && $today <= $selesai)) continue;
            if (!($jadwal->soal_agama == '-' || $jadwal->soal_agama == '0' || $jadwal->soal_agama == $siswa->agama)) continue;

            if (!isset($jadwal_ujian_aktif[$jadwal->tgl_mulai])) {
                $jadwal_ujian_aktif[$jadwal->tgl_mulai] = [];
            }
            $jadwal_ujian_aktif[$jadwal->tgl_mulai][] = $jadwal;
        }

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Penilaian',
            'setting'      => $this->dashboard->getSetting(),
            'cbt_info'     => $cbt_info,
            'cbt_jadwal'   => $jadwal_ujian_aktif,
            'guru'         => $this->cbt->getDataGuru(),
            'sesi'         => $this->dropdown->getAllWaktuSesi(),
            'elapsed'      => $timer,
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'running_text' => $this->dashboard->getRunningText(),
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/cbt/data');
        $this->load->view('members/siswa/templates/footer');
    }

    public function konfirmasi($id_jadwal)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $curr_agent = $this->agent->is_browser()
            ? $this->agent->browser() . ' ' . $this->agent->version()
            : ($this->agent->is_mobile() ? $this->agent->mobile() : 'unknown');

        $info     = $this->cbt->getJadwalById($id_jadwal);
        $bank     = $this->cbt->getCbt($id_jadwal);
        $cbt_info = $this->cbt->getSiswaCbtInfo($siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $pengawass = $this->cbt->getPengawas($tp->id_tp . $smt->id_smt . $id_jadwal . $cbt_info->id_ruang . $cbt_info->id_sesi);
        $pengawas  = ($pengawass != null && count(explode(',', $pengawass->id_guru ?? '')) > 0)
            ? $this->master->getGuruByArrId(explode(',', $pengawass->id_guru ?? ''))
            : [];

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Penilaian',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'support'      => $curr_agent != 'unknown',
            'valid'        => true,
            'bank'         => $bank,
            'kelas'        => $this->cbt->getKelas($tp->id_tp, $smt->id_smt),
            'guru'         => $this->cbt->getDataGuru(),
            'pengawas'     => $pengawas,
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/cbt/konfirmasi');
        $this->load->view('members/siswa/templates/footer');
    }

    public function validasiSiswa()
    {
        $id_jadwal   = $this->input->post('jadwal');
        $id_siswa    = $this->input->post('siswa');
        $id_bank     = $this->input->post('bank');
        $token_siswa = $this->input->post('token');
        $this->load->model('Cbt_model', 'cbt');
        $this->db->trans_start();

        $info        = $this->cbt->getJadwalById($id_jadwal);
        $token_valid = true;
        if ($info->token == '1') {
            $token       = $this->cbt->getToken();
            $token_valid = $token != null && $token->token == $token_siswa;
        }
        $data['token']     = $token_valid;
        $data['token_msg'] = $token_valid ? '' : 'Token salah';
        if (!$token_valid) {
            $this->output_json($data);
            return;
        }

        $curr_agent = $this->agent->is_browser()
            ? $this->agent->browser() . ' ' . $this->agent->version()
            : ($this->agent->is_mobile() ? $this->agent->mobile() : 'unknown');
        $support = $curr_agent != 'unknown';
        $data['support'] = $support;
        if (!$support) {
            $this->output_json($data);
            return;
        }

        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        $elapsed = $this->cbt->getElapsed($id_siswa . '0' . $id_jadwal);

        $data['izinkan'] = true;
        $data['log']     = $log;

        if ($elapsed == null) {
            $this->output_json($data);
            return;
        }

        $mulai    = new DateTime($elapsed->mulai);
        $interval = $mulai->diff(new DateTime());
        $minutes  = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
        $ada_waktu = $minutes < $info->durasi_ujian;

        $data['interval']  = ['days' => $interval->days, 'hari' => $interval->d, 'jam' => $interval->h, 'menit' => $interval->i, 'detik' => $interval->s, 'total' => $minutes];
        $data['warn']      = ['durasi_ujian' => $info->durasi_ujian, 'siswa_mulai' => $elapsed->mulai, 'durasi_siswa' => $elapsed->lama_ujian, 'timer_elapsed' => $minutes, 'terlampaui' => $minutes - $info->durasi_ujian, 'status' => $ada_waktu ? 0 : 1, 'msg' => $ada_waktu ? '' : 'Waktu ujian sudah habis'];
        $data['ada_waktu'] = $ada_waktu;
        $data['elapsed']   = $this->cbt->getElapsed($id_siswa . '0' . $id_jadwal);

        if (!$ada_waktu) {
            $this->output_json($data);
            return;
        }

        $soal = $this->cbt->getJumlahSoalSiswa($id_bank, $id_siswa);
        if ($soal == 0) {
            $nomor_soal = $this->createQueueNumber($id_siswa, $id_bank, $id_jadwal);
            if (count($nomor_soal) > 0) {
                $this->db->insert_batch('cbt_soal_siswa', $nomor_soal);
            }
        }

        $data['jml_soal'] = $this->cbt->getJumlahSoalSiswa($id_bank, $id_siswa);
        $this->db->trans_complete();
        $this->output_json($data);
    }

    public function createQueueNumber($id_siswa, $id_bank, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $cek_soal = $this->cbt->getAllIdSoal($id_bank);
        $jadwal   = $this->cbt->getInfoJadwal($id_bank);

        $num1 = isset($cek_soal['1']) ? count($cek_soal['1']) : 0;
        $num2 = isset($cek_soal['2']) ? count($cek_soal['2']) : 0;
        $num3 = isset($cek_soal['3']) ? count($cek_soal['3']) : 0;
        $num4 = isset($cek_soal['4']) ? count($cek_soal['4']) : 0;
        $num5 = isset($cek_soal['5']) ? count($cek_soal['5']) : 0;
        $total = $num1 + $num2 + $num3 + $num4 + $num5;

        if (
            $num1 != (int) $jadwal->tampil_pg
            || $num2 != (int) $jadwal->tampil_kompleks
            || $num3 != (int) $jadwal->tampil_jodohkan
            || $num4 != (int) $jadwal->tampil_isian
            || $num5 != (int) $jadwal->tampil_esai
        ) {
            return [];
        }

        $opsis    = $jadwal->opsi;
        $arrOpsi  = array_slice(['A', 'B', 'C', 'D', 'E'], 0, max(2, min((int) $opsis, 5)));
        $arrNum   = range(1, $total);
        if ($jadwal->acak_soal == '1') shuffle($arrNum);

        $items = [];
        $j     = 0;
        foreach ($cek_soal as $jenis => $soals) {
            foreach ($soals as $soal) {
                if ($jenis == '1' && $jadwal->acak_opsi == '1') shuffle($arrOpsi);

                $item_soal = [
                    'id_soal_siswa'  => $id_siswa . '0' . $id_jadwal . $id_bank . $arrNum[$j],
                    'id_bank'        => $id_bank,
                    'id_jadwal'      => $id_jadwal,
                    'id_soal'        => $soal->id_soal,
                    'id_siswa'       => $id_siswa,
                    'jenis_soal'     => $jenis,
                    'no_soal_alias'  => $arrNum[$j],
                    'jawaban_benar'  => $soal->jawaban,
                    'soal_end'       => $j + 1 === count($arrNum) ? '1' : '0',
                ];

                if ($jenis == '1') {
                    $item_soal['opsi_alias_a'] = isset($arrOpsi[0]) ? $arrOpsi[0] : null;
                    $item_soal['opsi_alias_b'] = isset($arrOpsi[1]) ? $arrOpsi[1] : null;
                    $item_soal['opsi_alias_c'] = isset($arrOpsi[2]) ? $arrOpsi[2] : null;
                    $item_soal['opsi_alias_d'] = isset($arrOpsi[3]) ? $arrOpsi[3] : null;
                    $item_soal['opsi_alias_e'] = isset($arrOpsi[4]) ? $arrOpsi[4] : null;
                }

                $items[] = $item_soal;
                $j++;
            }
        }

        usort($items, function ($a, $b) {
            return $a['no_soal_alias'] <=> $b['no_soal_alias'];
        });
        return $items;
    }

    public function penilaian($id_jadwal)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $id_durasi = $siswa->id_siswa . '0' . $id_jadwal;
        $durasi    = $this->cbt->getElapsed($id_durasi);

        if ($durasi == null || $durasi->selesai != null) {
            redirect('siswa/cbt');
            return;
        }

        $mulai = new DateTime($durasi->mulai);
        $diff  = $mulai->diff(new DateTime());
        $durasi->diff = ['days' => $diff->days, 'hari' => $diff->d, 'jam' => $diff->h, 'menit' => $diff->i, 'detik' => $diff->s, 'format' => $diff->format('%H:%I:%S')];

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Penilaian',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'jadwal'       => $this->cbt->getCbt($id_jadwal),
            'elapsed'      => $durasi,
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/cbt/ujian');
        $this->load->view('members/siswa/templates/footer');
    }

    public function checkTimer($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_durasi = $id_siswa . '0' . $id_jadwal;
        $durasi    = $this->cbt->getElapsed($id_durasi);

        if ($durasi == null) return false;

        $mulai   = new DateTime($durasi->mulai);
        $diff    = $mulai->diff(new DateTime());
        $elapsed = $diff->format('%H:%I:%S');

        $this->db->set('lama_ujian', $elapsed);
        $this->db->set('reset', 0);
        $this->db->where('id_durasi', $id_durasi);
        $this->db->update('cbt_durasi_siswa');

        return $this->cbt->getElapsed($id_durasi);
    }

    public function loadNomorSoal()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $id_siswa  = $this->input->post('siswa');
        $id_jadwal = $this->input->post('jadwal');
        $id_bank   = $this->input->post('bank');
        $nomor     = $this->input->post('nomor');
        $timer     = $this->input->post('timer');
        $durasi    = $this->checkTimer($id_siswa, $id_jadwal);
        $tp        = $this->dashboard->getTahunActive();
        $smt       = $this->dashboard->getSemesterActive();
        $siswa     = $this->cbt->getDataSiswaById($tp->id_tp, $smt->id_smt, $id_siswa);
        $soals     = $this->cbt->getALLSoalSiswa($id_bank, $siswa->id_siswa);

        foreach ($soals as $s) {
            if ($s->jenis_soal == '3') {
                $s->jawaban = unserialize($s->jawaban ?? '');
            }
            if ($s->jawaban_siswa != null) {
                $s->jawaban_siswa = unserialize($s->jawaban_siswa ?? '');
            }
        }

        $id_soal_siswa = $siswa->id_siswa . '0' . $id_jadwal . $id_bank . $nomor;
        $ind_soal      = array_search($id_soal_siswa, array_column($soals, 'id_soal_siswa'));
        $item_soal     = $soals[$ind_soal];

        $max_jawaban = [];
        $opsis       = [];

        $arrJawaban = [];
        $modal      = '<div class="d-flex flex-wrap justify-content-center grid-nomor-pg">';
        foreach ($soals as $key => $soal) {
            $terjawab = $soal->jawaban_siswa != null && $soal->jawaban_siswa != '';
            $color    = !$terjawab ? 'outline-secondary' : 'primary';
            $selected = $nomor == $soal->no_soal_alias ? 'active' : '';
            $modal .= '<div class="mb-4"><div id="box' . $soal->no_soal_alias . '" class="d-flex flex-column" style="width: 70px; height: 60px;">'
                . '<button id="btn' . $soal->no_soal_alias . '" class="btn btn-' . $color . ' border border-dark ' . $selected . '" '
                . 'data-pos="' . $key . '" data-nomorsoal="' . $soal->no_soal_alias . '" '
                . 'data-idsoal="' . $soal->id_soal . '" data-jenis="' . $soal->jenis_soal . '" '
                . 'onclick="loadSoal(this)" style="width: 50px; height: 50px;">'
                . '<span style="font-size: 14pt"><b>' . $soal->no_soal_alias . '</b></span></button>';

            if ($terjawab) {
                $txt_badge = $soal->jenis_soal == '1' ? $soal->jawaban_alias : '&check;';
                $arrJawaban[] = $soal->jawaban_alias;
                $modal .= '<div id="badge' . $soal->no_soal_alias . '" class="badge badge-pill badge-success border border-dark"'
                    . ' style="font-size:12pt; width: 30px; height: 30px; margin-top: -60px; margin-left: 30px;">' . $txt_badge . '</div>';
            }
            $modal .= '</div></div>';
        }
        $modal .= '</div>';

        $data = [
            'durasi'             => $durasi,
            'timer'              => $timer,
            'soal_id'            => $item_soal->id_soal,
            'soal_siswa_id'      => $item_soal->id_soal_siswa,
            'soal_nomor'         => $item_soal->no_soal_alias,
            'soal_nomor_asli'    => $item_soal->nomor_soal,
            'soal_jenis'         => $item_soal->jenis_soal,
            'soal_soal'          => $item_soal->soal,
            'soal_opsi'          => json_decode(json_encode($opsis)),
            'soal_jawaban_siswa' => $item_soal->jawaban_siswa,
            'max_jawaban'        => $max_jawaban,
            'soal_modal'         => $modal,
            'soal_total'         => count($soals),
            'soal_terjawab'      => count($arrJawaban),
            'soal_akhir'         => $modal,
        ];
        $this->output_json($data);
    }

    public function saveSoalSiswa()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $shuffle = json_decode($this->input->post('shuffle', false));

        foreach ($shuffle as $s) {
            $id_siswa  = $s->id_siswa;
            $id_jadwal = $s->id_jadwal;
            $id_bank   = $s->id_bank;
            $jenis     = $s->jenis;
            $nomor     = $s->nomor_soal;
            $soal      = $this->cbt->getSoalByNomor($id_bank, $nomor, $jenis);
            $id_soal   = $soal->id_soal;
            $id_key    = $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor;

            $insert = ['id_bank' => $id_bank, 'id_jadwal' => $id_jadwal, 'id_soal' => $id_soal, 'id_siswa' => $id_siswa, 'jenis_soal' => $jenis, 'no_soal_alias' => $s->no_soal_alias, 'opsi_alias_a' => $s->opsi_alias_a ?? null, 'opsi_alias_b' => $s->opsi_alias_b ?? null, 'opsi_alias_c' => $s->opsi_alias_c ?? null, 'opsi_alias_d' => $s->opsi_alias_d ?? null, 'opsi_alias_e' => $s->opsi_alias_e ?? null, 'jawaban_benar' => $soal->jawaban, 'soal_end' => $s->soal_end];

            $this->db->where('id_soal_siswa', $id_key);
            if ($this->db->get('cbt_soal_siswa')->num_rows() > 0) {
                $this->master->update('cbt_soal_siswa', $insert, 'id_soal_siswa', $id_key);
            } else {
                $insert['id_soal_siswa'] = $id_key;
                $this->master->create('cbt_soal_siswa', $insert, false);
            }
        }

        $this->output_json(['soals' => $this->cbt->getSoalSiswa($shuffle[0]->id_bank, $shuffle[0]->id_siswa)]);
    }

    public function saveLogUjian($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->saveLog($id_siswa, $id_jadwal, 1, 'Memulai Ujian'));
    }

    public function saveJawaban()
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_bank   = $this->input->post('bank', true);
        $timer     = $this->input->post('waktu', true);
        $id_siswa  = $this->input->post('siswa', true);
        $id_jadwal = $this->input->post('jadwal', true);
        $elapsed   = $this->input->post('elapsed', true);
        $id_durasi = $id_siswa . '0' . $id_jadwal;

        if ($elapsed != '0') {
            $this->db->set('lama_ujian', $elapsed);
            $this->db->where('id_durasi', $id_durasi);
            $this->db->update('cbt_durasi_siswa');
        }

        $jawab = $this->input->post('data', false);
        if ($jawab != null && isset($jawab['jenis'])) {
            $this->db->set('jawaban_alias', '');
            $this->db->set('jawaban_siswa', $jawab['jawaban_siswa']);
            $this->db->where('id_soal_siswa', $jawab['id_soal_siswa']);
            $update = $this->db->update('cbt_soal_siswa');
        } else {
            $update = true;
        }

        $data = ['status' => $update];

        if ($update && $id_bank != null) {
            $arrJawaban = [];
            foreach ($this->cbt->getJumlahJawaban($id_bank, $id_siswa) as $j) {
                if ($j->jawaban_siswa != null && $j->jawaban_siswa != '') {
                    $arrJawaban[] = $j;
                }
            }
            $data['soal_terjawab'] = count($arrJawaban);
        }

        if ($update && $timer != null) {
            $this->selesaiUjian();
        }

        $this->output_json($data);
    }

    public function selesaiUjian()
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_siswa  = $this->input->post('siswa');
        $id_jadwal = $this->input->post('jadwal');

        $data['status_nilai'] = $this->olahNilai($id_siswa, $id_jadwal);
        $this->db->set('selesai', date('Y-m-d H:i:s'));
        $this->db->set('status', 2);
        $this->db->where('id_durasi', $id_siswa . '0' . $id_jadwal);
        $data['status'] = $this->db->update('cbt_durasi_siswa');
        $this->cbt->saveLog($id_siswa, $id_jadwal, 2, 'Menyelesaikan Ujian');
        $this->output_json($data);
    }

    public function resetTimer()
    {
        $id_durasi = $this->input->post('id_durasi', true);
        $reset     = $this->input->post('reset', true);

        if ($reset == '1') {
            $this->db->set('lama_ujian', '00:00:00');
        }
        $this->db->set('reset', $reset);
        $this->db->where('id_durasi', $id_durasi);
        $this->output_json(['status' => $this->db->update('cbt_durasi_siswa')]);
    }

    public function ulangiUjian($id_durasi, $id_bank)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $soals = $this->cbt->getAllSoalByBank($id_bank);

        if (!$this->master->delete('cbt_durasi_siswa', $id_durasi, 'id_durasi')) {
            $this->output_json(['status' => false]);
            return;
        }

        foreach ([0, 1] as $i) {
            foreach ($soals as $soal) {
                $this->db->where('id_soal_siswa', $id_durasi . $id_bank . ($i + 1) . $soal->nomor_soal);
                $this->db->delete('cbt_soal_siswa');
            }
        }
    }

    public function applyAction()
    {
        $this->load->model('Cbt_model', 'cbt');
        $json      = json_decode($this->input->post('aksi', true));
        $id_jadwal = $this->input->post('jadwal', true);
        $this->db->trans_start();
        $data = ['update_reset' => true, 'update_selesai' => true, 'update_ulangi' => true];

        if (count($json->reset) > 0) {
            $data['reset'] = true;
            $this->db->set('reset', 1);
            $this->db->where_in('id_log', $json->reset);
            $this->db->update('log_ujian');
        }

        if (count($json->force) > 0) {
            $data['selesai'] = true;
            foreach ($json->log as $ids) {
                $data['status_nilai'] = $this->olahNilai($ids, $id_jadwal);
                $this->cbt->saveLog($ids, $id_jadwal, 2, 'Menyelesaikan Ujian');
            }
            $this->db->set('selesai', date('Y-m-d H:i:s'));
            $this->db->set('status', 2);
            $this->db->set('reset', 3);
            $this->db->where_in('id_durasi', $json->force);
            $data['update_selesai'] = $this->db->update('cbt_durasi_siswa');
        }

        if (count($json->ulang) > 0) {
            $data['ulangi'] = true;
            $this->db->where_in('id_durasi', $json->hapus);
            $this->db->delete('cbt_durasi_siswa');
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->where_in('id_siswa', $json->ulang);
            $this->db->delete('log_ujian');
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->where_in('id_siswa', $json->ulang);
            $this->db->delete('cbt_nilai');
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->where_in('id_siswa', $json->ulang);
            $data['update_ulangi'] = $this->db->delete('cbt_soal_siswa');
        }

        $this->db->trans_complete();
        $this->output_json($data);
    }

    private function _hitungSkorJodohkan($jawaban_jodoh, $bagi_jodoh, $bobot_jodoh)
    {
        $benar_jod       = 0;
        $skor_koreksi    = 0.0;
        $otomatis        = 0;
        $point_per_soal  = $bobot_jodoh > 0 && $bagi_jodoh > 0 ? round($bobot_jodoh * 100 / ($bagi_jodoh * 100), 2) : 0;

        foreach ($jawaban_jodoh as $jawab_jod) {
            $skor_koreksi += $jawab_jod->nilai_koreksi;
            $item_benar   = 0;
            $items        = 0;
            $otomatis     = $jawab_jod->nilai_otomatis;

            if (isset($jawab_jod->jawaban_siswa->links)) {
                $array1 = (array) $jawab_jod->jawaban_benar->links;
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $this->sortArrays($array1);
                $this->sortArrays($array2);
                foreach ($array1 as $key => $subArray1) {
                    $items += count($subArray1);
                    if (isset($array2[$key])) {
                        $item_benar += count(array_intersect($subArray1, $array2[$key]));
                    }
                }
            }
            $benar_jod += $items > 0 ? 1 / $items * $item_benar : 0;
        }

        $s_jod  = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
        return [$otomatis == 0 ? $s_jod : $skor_koreksi, $benar_jod];
    }

    public function olahNilai($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $info      = $this->cbt->getJadwalById($id_jadwal);
        $jawabans  = $this->cbt->getJawabanByBank($info->id_bank, $id_siswa);
        $jawabans_siswa = [];

        foreach ($jawabans as $js) {
            if ($js->jenis_soal == '2') {
                $js->jawaban_siswa  = @unserialize($js->jawaban_siswa ?? '');
                $js->jawaban_benar  = array_filter(array_map([$this, 'arrToUpper'], @unserialize($js->jawaban_benar ?? '')), 'strlen');
            } elseif ($js->jenis_soal == '3') {
                $js->jawaban_siswa  = json_decode(json_encode(@unserialize($js->jawaban_siswa ?? '')));
                $js->jawaban_benar  = json_decode(json_encode(@unserialize($js->jawaban_benar ?? '')));
                if (isset($js->jawaban_siswa->jawaban)) {
                    $arrjwbn = [];
                    $alpha   = range('A', 'Z');
                    foreach ($js->jawaban_siswa->jawaban as $idx => $jbs) {
                        if ($idx == 0) continue;
                        $arrjwbn[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if ($idxs > 0 && $jb === '1') $arrjwbn[$idx][] = $alpha[$idxs - 1];
                        }
                    }
                    $js->jawaban_siswa = json_decode(json_encode(['links' => $arrjwbn]));
                }
                if (isset($js->jawaban_benar->jawaban)) {
                    $arrjwbn = [];
                    $alpha   = range('A', 'Z');
                    foreach ($js->jawaban_benar->jawaban as $idx => $jbs) {
                        if ($idx == 0) continue;
                        $arrjwbn[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if ($idxs > 0 && $jb === '1') $arrjwbn[$idx][] = $alpha[$idxs - 1];
                        }
                    }
                    $js->jawaban_benar->links = json_decode(json_encode($arrjwbn));
                }
            }
            $jawabans_siswa[$js->jenis_soal][] = $js;
        }

        $bagi_pg    = $info->tampil_pg / 100;
        $bobot_pg    = $info->bobot_pg / 100;
        $bagi_pg2   = $info->tampil_kompleks / 100;
        $bobot_pg2   = $info->bobot_kompleks / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian = $info->tampil_isian / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_essai = $info->bobot_esai / 100;

        $benar_pg  = 0;
        $jawaban_pg = isset($jawabans_siswa['1']) ? $jawabans_siswa['1'] : [];
        foreach ($jawaban_pg as $jwb) {
            if ($jwb != null && $jwb->jawaban_siswa != null && strtoupper($jwb->jawaban_siswa ?? '') == strtoupper($jwb->jawaban_benar ?? '')) {
                $benar_pg++;
            }
        }
        $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;

        $benar_pg2 = 0;
        $skor_koreksi_pg2 = 0.0;
        $otomatis_pg2 = 0;
        foreach (isset($jawabans_siswa['2']) ? $jawabans_siswa['2'] : [] as $jawab) {
            $otomatis_pg2       = $jawab->nilai_otomatis;
            $skor_koreksi_pg2  += $jawab->nilai_koreksi;
            $arr_benar = [];
            if (is_array($jawab->jawaban_siswa)) {
                foreach ($jawab->jawaban_siswa as $js) {
                    if (in_array($js, $jawab->jawaban_benar)) $arr_benar[] = true;
                }
            }
            if (count($jawab->jawaban_benar) > 0) {
                $benar_pg2 += 1 / count($jawab->jawaban_benar) * count($arr_benar);
            }
        }
        $s_pg2   = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
        $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;

        [$skor_jod] = $this->_hitungSkorJodohkan(isset($jawabans_siswa['3']) ? $jawabans_siswa['3'] : [], $bagi_jodoh, $bobot_jodoh);

        $benar_is = 0;
        $skor_koreksi_is = 0.0;
        $otomatis_is = 0;
        foreach (isset($jawabans_siswa['4']) ? $jawabans_siswa['4'] : [] as $jawab) {
            $skor_koreksi_is += $jawab->nilai_koreksi;
            $otomatis_is      = $jawab->nilai_otomatis;
            if ($jawab != null && strtolower($jawab->jawaban_siswa ?? '') == strtolower($jawab->jawaban_benar ?? '')) $benar_is++;
        }
        $s_is   = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
        $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;

        $benar_es = 0;
        $skor_koreksi_es = 0.0;
        $otomatis_es = 0;
        foreach (isset($jawabans_siswa['5']) ? $jawabans_siswa['5'] : [] as $jawab) {
            $skor_koreksi_es += $jawab->nilai_koreksi;
            $otomatis_es      = $jawab->nilai_otomatis;
            if ($jawab != null && strtolower($jawab->jawaban_siswa ?? '') == strtolower($jawab->jawaban_benar ?? '')) $benar_es++;
        }
        $s_es   = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
        $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;

        $insert = ['id_nilai' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'pg_benar' => $benar_pg, 'pg_nilai' => round($skor_pg, 2), 'kompleks_nilai' => round($skor_pg2, 2), 'jodohkan_nilai' => round($skor_jod, 2), 'isian_nilai' => round($skor_is, 2), 'essai_nilai' => round($skor_es, 2)];
        return $this->db->replace('cbt_nilai', $insert);
    }

    public function hasil()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $logs            = $this->kelas->getNilaiMateriSiswa($siswa->id_siswa);
        $this->db->trans_start();
        $jadwals         = $this->cbt->getJadwalByKelas($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $skors           = [];
        $durasies        = [];
        $jawabans        = [];
        $kelass_unset    = [];

        foreach ($jadwals as $kj => $jadwal) {
            $kelass        = unserialize($jadwal->bank_kelas ?? '');
            $arr_kls_jadwal = [];
            foreach ($kelass as $kll) {
                foreach ($kll as $kl) {
                    if ($kl != null) $arr_kls_jadwal[] = $kl;
                }
            }
            if (!in_array($siswa->id_kelas, $arr_kls_jadwal)) {
                unset($jadwals[$kj]);
                $kelass_unset[] = $kj;
                continue;
            }

            $jadwal->bank_kelas = $kelass;
            $info = $jadwal;
            $bagi_pg    = $info->tampil_pg / 100;
            $bobot_pg    = $info->bobot_pg / 100;
            $bagi_pg2   = $info->tampil_kompleks / 100;
            $bobot_pg2   = $info->bobot_kompleks / 100;
            $bagi_jodoh = $info->tampil_jodohkan / 100;
            $bobot_jodoh = $info->bobot_jodohkan / 100;
            $bagi_isian = $info->tampil_isian / 100;
            $bobot_isian = $info->bobot_isian / 100;
            $bagi_essai = $info->tampil_esai / 100;
            $bobot_essai = $info->bobot_esai / 100;

            $jawabans       = $this->cbt->getJawabanSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
            $jawabans_siswa = [];
            $alpha          = range('A', 'Z');

            foreach ($jawabans as $js) {
                if ($js->jenis_soal == '2') {
                    $js->jawaban_siswa = @unserialize($js->jawaban_siswa ?? '');
                    $js->jawaban_benar = array_filter(array_map([$this, 'arrToUpper'], @unserialize($js->jawaban_benar ?? '')), 'strlen');
                    $js->jawaban       = array_filter(array_map([$this, 'arrToUpper'], @unserialize($js->jawaban ?? '')), 'strlen');
                } elseif ($js->jenis_soal == '3') {
                    $js->jawaban_siswa = json_decode(json_encode(@unserialize($js->jawaban_siswa ?? '')));
                    $js->jawaban_benar = json_decode(json_encode(@unserialize($js->jawaban_benar ?? '')));
                    $js->jawaban       = json_decode(json_encode(@unserialize($js->jawaban ?? '')));
                    if (isset($js->jawaban_siswa->jawaban)) {
                        $arr = [];
                        foreach ($js->jawaban_siswa->jawaban as $idx => $jbs) {
                            if ($idx == 0) continue;
                            $arr[$idx] = [];
                            foreach ($jbs as $idxs => $jb) {
                                if ($idxs > 0 && $jb === '1') $arr[$idx][] = $alpha[$idxs - 1];
                            }
                        }
                        $js->jawaban_siswa = json_decode(json_encode(['links' => $arr]));
                    }
                    if (isset($js->jawaban_benar->jawaban)) {
                        $arr = [];
                        foreach ($js->jawaban_benar->jawaban as $idx => $jbs) {
                            if ($idx == 0) continue;
                            $arr[$idx] = [];
                            foreach ($jbs as $idxs => $jb) {
                                if ($idxs > 0 && $jb === '1') $arr[$idx][] = $alpha[$idxs - 1];
                            }
                        }
                        $js->jawaban_benar->links = json_decode(json_encode($arr));
                    }
                }
                $jawabans_siswa[$js->id_siswa][$js->jenis_soal][] = $js;
            }

            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
            $skor        = new stdClass();
            $skor->dikoreksi = $nilai_input != null ? $nilai_input->dikoreksi : 0;

            $jwb_pg    = isset($jawabans_siswa[$siswa->id_siswa]['1']) ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
            $benar_pg  = 0;
            foreach ($jwb_pg as $j) {
                if ($j != null && $j->jawaban_siswa != null && strtoupper($j->jawaban_siswa ?? '') == strtoupper($j->jawaban ?? '')) $benar_pg++;
            }
            $skor->skor_pg    = $bagi_pg == 0 ? 0 : round($benar_pg / $bagi_pg * $bobot_pg, 2);
            $skor->benar_pg   = $benar_pg;

            $jwb_pg2       = isset($jawabans_siswa[$siswa->id_siswa]['2']) ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
            $benar_pg2     = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            foreach ($jwb_pg2 as $j) {
                $skor_koreksi_pg2 += $j->nilai_koreksi;
                $otomatis_pg2      = $j->nilai_otomatis;
                $arr_benar = [];
                if ($j->jawaban_siswa) {
                    foreach ($j->jawaban_siswa as $js) {
                        if (in_array($js, $j->jawaban)) $arr_benar[] = true;
                    }
                }
                if (count($j->jawaban) > 0) $benar_pg2 += 1 / count($j->jawaban) * count($arr_benar);
            }
            $s_pg2      = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $input_pg2  = ($nilai_input != null && $nilai_input->kompleks_nilai != null) ? $nilai_input->kompleks_nilai : 0;
            $skor->skor_kompleks = round($input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2), 2);
            $skor->benar_kompleks = round($benar_pg2, 2);

            [$skor_jod, $benar_jod] = $this->_hitungSkorJodohkan(isset($jawabans_siswa[$siswa->id_siswa]['3']) ? $jawabans_siswa[$siswa->id_siswa]['3'] : [], $bagi_jodoh, $bobot_jodoh);
            $input_jod  = ($nilai_input != null && $nilai_input->jodohkan_nilai != null) ? $nilai_input->jodohkan_nilai : 0;
            $skor->skor_jodohkan  = round($input_jod != 0 ? $input_jod : $skor_jod, 2);
            $skor->benar_jodohkan = round($benar_jod, 2);

            $jwb_is    = isset($jawabans_siswa[$siswa->id_siswa]['4']) ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
            $benar_is  = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is = 0;
            foreach ($jwb_is as $j) {
                $skor_koreksi_is += $j->nilai_koreksi;
                $otomatis_is      = $j->nilai_otomatis;
                if ($j != null && strtolower($j->jawaban_siswa ?? '') == strtolower($j->jawaban ?? '')) $benar_is++;
            }
            $s_is       = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $input_is   = ($nilai_input != null && $nilai_input->isian_nilai != null) ? $nilai_input->isian_nilai : 0;
            $skor->skor_isian  = round($input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is), 2);
            $skor->benar_isian = $benar_is;

            $jwb_es    = isset($jawabans_siswa[$siswa->id_siswa]['5']) ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
            $benar_es  = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es = 0;
            foreach ($jwb_es as $j) {
                $skor_koreksi_es += $j->nilai_koreksi;
                $otomatis_es      = $j->nilai_otomatis;
                if ($j != null && strtolower($j->jawaban_siswa ?? '') == strtolower($j->jawaban ?? '')) $benar_es++;
            }
            $s_es       = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $input_es   = ($nilai_input != null && $nilai_input->essai_nilai != null) ? $nilai_input->essai_nilai : 0;
            $skor->skor_essai  = round($input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es), 2);
            $skor->benar_esai  = $benar_es;

            $skor->skor_total = round($skor->skor_pg + $skor->skor_kompleks + $skor->skor_jodohkan + $skor->skor_isian + $skor->skor_essai, 2);
            $skors[$jadwal->id_jadwal]    = $skor;
            $durasies[$jadwal->id_jadwal] = $this->cbt->getDurasiSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
        }

        $this->db->trans_complete();
        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Nilai',
            'subjudul'     => 'Nilai Hasil Belajar',
            'setting'      => $this->dashboard->getSetting(),
            'nilai_materi' => isset($logs[1]) ? $logs[1] : [],
            'nilai_tugas'  => isset($logs[2]) ? $logs[2] : [],
            'skor'         => $skors,
            'durasi'       => $durasies,
            'jadwal'       => $jadwals,
            'jawaban'      => $jawabans,
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'running_text' => $this->dashboard->getRunningText(),
            'kelass'       => $kelass_unset,
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/nilai/data');
        $this->load->view('members/siswa/templates/footer');
    }

    public function catatan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $catatan = [];
        foreach ($this->kelas->getCatatanMapelBySiswa($siswa->id_kelas, $tp->id_tp, $smt->id_smt) as $cat) {
            if ($cat->type === '2' && $cat->id_siswa === $siswa->id_siswa || $cat->type === '1' && $cat->id_kelas === $siswa->id_kelas) {
                $catatan[] = ['id_catatan' => $cat->id_catatan, 'nama_guru' => $cat->nama_guru, 'foto_guru' => $cat->foto && file_exists($cat->foto) ? $cat->foto : 'uploads/profiles/' . $cat->nip . (file_exists('uploads/profiles/' . $cat->nip . '.jpg') ? '.jpg' : '.png'), 'id_siswa' => $siswa->id_siswa, 'tgl' => $cat->tgl, 'table' => 'mapel', 'level' => $cat->level, 'type' => $cat->type, 'readed' => $cat->readed, 'reading' => unserialize($cat->reading ?? '')];
            }
        }
        foreach ($this->kelas->getCatatanSiswaBySiswa($siswa->id_kelas, $tp->id_tp, $smt->id_smt) as $cat) {
            if ($cat->type === '2' && $cat->id_siswa === $siswa->id_siswa || $cat->type === '1' && $cat->id_kelas === $siswa->id_kelas) {
                $catatan[] = ['id_catatan' => $cat->id_catatan, 'nama_guru' => $cat->nama_guru, 'foto_guru' => $cat->foto && file_exists($cat->foto) ? $cat->foto : 'uploads/profiles/' . $cat->nip . (file_exists('uploads/profiles/' . $cat->nip . '.jpg') ? '.jpg' : '.png'), 'id_siswa' => $siswa->id_siswa, 'tgl' => $cat->tgl, 'table' => 'wali', 'level' => $cat->level, 'readed' => $cat->readed, 'type' => $cat->type, 'reading' => unserialize($cat->reading ?? '')];
            }
        }
        rsort($catatan);

        $data = [
            'user'         => $user,
            'siswa'        => $siswa,
            'judul'        => 'Catatan',
            'subjudul'     => 'Catatan Dari Guru',
            'setting'      => $this->dashboard->getSetting(),
            'catatan'      => (array) json_decode(json_encode($catatan)),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'running_text' => $this->dashboard->getRunningText(),
        ];

        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/catatan/data');
        $this->load->view('members/siswa/templates/footer');
    }

    public function detailCatatan($table, $id_catatan)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $user  = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);

        $detail = $table == 'mapel'
            ? $this->kelas->getCatatanMapelSiswaDetail($id_catatan)
            : $this->kelas->getCatatanKelasSiswaDetail($id_catatan);

        if (!$detail) {
            $this->output_json(['reading' => [], 'detail' => null]);
            return;
        }

        $detail->id_siswa = $siswa->id_siswa;
        $reading = $detail->reading != null ? unserialize($detail->reading ?? '') : [];
        $this->output_json(['reading' => $reading, 'detail' => $detail]);
    }

    public function readed($table, $id_catatan)
    {
        $this->load->model('Kelas_model', 'kelas');
        $tbl  = $table == 'mapel' ? 'kelas_catatan_mapel' : 'kelas_catatan_wali';
        $cat  = $this->kelas->getReading($tbl, $id_catatan);
        $readed = $cat->readed == '0' ? date('Y-m-d H:i:s') : '0';
        $this->db->set('readed', $readed);
        $this->db->where('id_catatan', $id_catatan);
        $this->output_json($this->db->update($tbl));
    }

    public function getTimer($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json(['durasi' => $this->cbt->getDurasiSiswa($id_siswa . '0' . $id_jadwal)]);
    }

    public function total_hari($id_day, $bulan, $taun)
    {
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $idday      = $id_day == '7' ? 0 : $id_day;
        $dates      = [];
        for ($i = 1; $i <= $total_days; $i++) {
            if (date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday) {
                $dates[] = date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i));
            }
        }
        return $dates;
    }
}
