<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model', 'cbt');
    }

    public function output_json($data, $encode = true)
    {
        if (!$encode) {
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    public function admin_box($setting, $tp, $smt)
    {
        $where = '';
        if ($setting->jenjang == '1') {
            $where = 'jenjang=0 OR jenjang=1';
        }
        $box = [
            ['box' => 'blue',    'total' => $this->dashboard->total('master_siswa'),                                    'title' => 'Siswa',            'url' => 'datasiswa',  'icon' => 'users'],
            ['box' => 'cyan',    'total' => $this->dashboard->total('master_kelas', 'id_tp=' . $tp . ' AND id_smt=' . $smt), 'title' => 'Rombel',      'url' => 'datakelas',  'icon' => 'bell'],
            ['box' => 'teal',    'total' => $this->dashboard->total('master_guru'),                                     'title' => 'Guru',             'url' => 'dataguru',   'icon' => 'user'],
            ['box' => 'fuchsia', 'total' => $this->dashboard->totalWaliKelas($tp, $smt),                                'title' => 'Wali Kelas',       'url' => 'dataguru',   'icon' => 'user'],
            ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where),                            'title' => 'Mapel',            'url' => 'datamapel',  'icon' => 'book'],
            ['box' => 'yellow',  'total' => $this->dashboard->total('master_ekstra'),                                   'title' => 'Ekstrakurikuler',  'url' => 'dataekstra', 'icon' => 'book'],
        ];
        return json_decode(json_encode($box), false);
    }

    public function guru_box($setting)
    {
        $where = '';
        if ($setting->jenjang == '1') {
            $where = 'jenjang=0 OR jenjang=1';
        }
        $box = [
            ['box' => 'teal',    'total' => $this->dashboard->total('master_kelas'), 'title' => 'Rombel', 'icon' => 'user'],
            ['box' => 'blue',    'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa',  'icon' => 'users'],
            ['box' => 'fuchsia', 'total' => $this->dashboard->total('master_guru'),  'title' => 'Guru',   'icon' => 'user'],
            ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'icon' => 'book'],
        ];
        return json_decode(json_encode($box), false);
    }

    public function ujian_box()
    {
        $box = [
            ['box' => 'indigo', 'total' => $this->dashboard->total('cbt_ruang'),      'title' => 'Ruang Ujian', 'url' => 'cbtruang',    'icon' => 'school'],
            ['box' => 'maroon', 'total' => $this->dashboard->total('cbt_sesi'),        'title' => 'Sesi',        'url' => 'cbtsesi',     'icon' => 'clock'],
            ['box' => 'green',  'total' => $this->dashboard->total('cbt_bank_soal'),   'title' => 'Bank Soal',   'url' => 'cbtbanksoal', 'icon' => 'folder'],
            ['box' => 'teal',   'total' => $this->dashboard->totalJadwal(),            'title' => 'Jadwal',      'url' => 'cbtjadwal',   'icon' => 'clock'],
        ];
        return json_decode(json_encode($box), false);
    }

    public function menu_siswa_box()
    {
        $box = [
            ['title' => 'Jadwal Pelajaran', 'icon' => 'ic_online.png',     'link' => 'siswa/jadwalpelajaran'],
            ['title' => 'Materi',           'icon' => 'ic_elearning.png',  'link' => 'siswa/materi'],
            ['title' => 'Tugas',            'icon' => 'ic_questions.png',  'link' => 'siswa/tugas'],
            ['title' => 'Ujian / Ulangan',  'icon' => 'ic_question.png',   'link' => 'siswa/cbt'],
            ['title' => 'Nilai Hasil',      'icon' => 'ic_exam.png',       'link' => 'siswa/hasil'],
            ['title' => 'Absensi',          'icon' => 'ic_clipboard.png',  'link' => 'siswa/kehadiran'],
            ['title' => 'Catatan Guru',     'icon' => 'ic_student.png',    'link' => 'siswa/catatan'],
        ];
        return json_decode(json_encode($box), false);
    }

    public function index()
    {
        $setting = $this->dashboard->getSetting();
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $default_token = json_decode(json_encode(['token' => '', 'auto' => '0', 'jarak' => '1', 'elapsed' => '00:00:00']));
        $token = $this->cbt->getToken();

        $data = [
            'user'      => $user,
            'judul'     => 'Beranda',
            'subjudul'  => 'Halaman Utama',
            'setting'   => $setting,
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'kelases'   => $tp !== null ? $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt) : [],
            'token'     => $token !== null ? $token : $default_token,
            'ada_ujian' => $this->cbt->getDataJadwalByTgl(date('Y-m-d')),
            'mapels'    => $this->master->getAllMapel(),
            'pengawas'  => $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt, null, null),
            'ruangs'    => $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []),
            'gurus'     => $this->dropdown->getAllGuru(),
        ];

        $day = date('N', strtotime(date('Y-m-d')));
        $jadwal = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, null, $day);
        $kbms = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt);
        foreach ($kbms as $kbm) {
            $kbm->istirahat = unserialize($kbm->istirahat);
        }
        $arrJadwalKelas = [];
        foreach ($jadwal as $item) {
            $arrJadwalKelas[$item->id_kelas][$item->jam_ke] = $item;
        }
        $arrKbm = [];
        foreach ($kbms as $item) {
            $arrKbm[$item->id_kelas] = $item;
        }
        $data['jadwals'] = $arrJadwalKelas;
        $data['kbms'] = $arrKbm;

        $tglJadwals = $this->cbt->getAllJadwalByJenis(null, $tp->id_tp, $smt->id_smt);
        foreach ($tglJadwals as $tgl => $jadwalss) {
            foreach ($jadwalss as $mpl => $jadwals) {
                foreach ($jadwals as $j) {
                    $j->bank_kelas = unserialize($j->bank_kelas ?? '');
                    foreach ($j->bank_kelas as $kb) {
                        if ($kb['kelas_id'] != '') {
                            $j->peserta[] = $this->cbt->getKelasUjian($kb['kelas_id']);
                        }
                    }
                }
            }
        }
        $data['jadwals_ujian'] = $tglJadwals;

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['info_box'] = $this->admin_box($setting, $tp->id_tp, $smt->id_smt);
            $data['ujian_box'] = $this->ujian_box();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('dashboard/admin');
            $this->load->view('_templates/dashboard/_footer');
        } elseif ($this->ion_auth->in_group('guru')) {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $data['info_box'] = $this->guru_box($setting);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/dashboard/index');
            $this->load->view('members/guru/templates/footer');
        } elseif ($this->ion_auth->in_group('siswa')) {
            $data['menu_box'] = $this->menu_siswa_box();
            $this->load->view('members/siswa/templates/header', $data);
            $this->load->view('members/siswa/dashboard/index');
            $this->load->view('members/siswa/templates/footer');
        }
    }

    public function checkTokenJadwal()
    {
        $token = $this->cbt->getToken();
        $token->now = date('d-m-Y H:i:s');
        $this->output_json([
            'ada_ujian' => $this->cbt->getDataJadwalByTgl(date('Y-m-d')),
            'token'     => $token,
        ]);
    }

    public function gantiTahun()
    {
        $aktif = $this->input->post('active', true);
        $tahuns = $this->input->post('id_tp', true);
        $update = [];
        foreach ($tahuns as $i => $id_tp) {
            $tahun = $this->input->post('tahun[' . $i . ']', true);
            $active = ($id_tp === $aktif) ? 1 : 0;
            $update[] = ['id_tp' => $id_tp, 'tahun' => $tahun, 'active' => $active];
        }
        $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
        $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
        $this->output_json(['update' => $update, 'status' => true]);
    }

    public function gantiSemester()
    {
        $aktif = $this->input->post('active', true);
        $smts = $this->input->post('id_smt', true);
        $update = [];
        foreach ($smts as $i => $id_smt) {
            $smt = $this->input->post('smt[' . $i . ']', true);
            $active = ($id_smt === $aktif) ? 1 : 0;
            $update[] = ['id_smt' => $id_smt, 'smt' => $smt, 'active' => $active];
        }
        $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
        $this->logging->saveLog(4, 'mengganti semester aktif');
        $this->output_json(['update' => $update, 'status' => true]);
    }

    public function getNotifikasi() {}

    public function getLog($limit)
    {
        $this->output_json($this->logging->loadAktifitas($limit));
    }

    public function hapusLog()
    {
        $this->db->trans_start();
        $deleted = $this->db->empty_table('log')
            ? ['status' => true, 'message' => 'berhasil']
            : ['status' => false, 'message' => 'gagal'];
        $this->db->trans_complete();
        $this->output_json($deleted);
    }

    public function getLogSiswa($limit)
    {
        $this->output_json($this->logging->loadAktifitasSiswa($limit));
    }

    public function getPengumuman($for)
    {
        $this->output_json($this->dashboard->loadPengumuman($for));
    }

    public function getJadwalHariIni($id_kelas, $id_hari)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, $id_kelas, $id_hari));
    }

    public function getJadwalKbm($id_kelas)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $jadwal = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt, $id_kelas);
        $this->output_json(['jadwal' => $jadwal, 'istirahat' => unserialize($jadwal->istirahat)]);
    }
}
