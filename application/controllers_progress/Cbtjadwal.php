<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtjadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error(
                'Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
        }

        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, bool $encode = true): void
    {
        $output = $encode ? json_encode($data) : $data;
        $this->output->set_content_type('application/json')->set_output($output);
    }

    public function index()
    {
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model',     'kelas');
        $this->load->model('Dropdown_model',  'dropdown');

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $level   = $this->input->get('level', true) ?? '0';
        $mode    = $this->input->get('mode');
        $type    = $this->input->get('type');

        $data = [
            'user'       => $user,
            'judul'      => 'Jadwal Penilaian',
            'subjudul'   => 'PH/PTS/PAT/USBK',
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'mode'       => $mode ?? '1',
            'ruangs'     => $this->cbt->getAllRuang(),
            'sesis'      => $this->dropdown->getAllSesi(),
            'jenis'      => $this->cbt->getAllJenisUjian(),
            'jadwal'     => json_decode(json_encode($this->cbt->dummyJadwal())),
            'jmlIst'     => [],
            'jmlMapel'   => [],
            'level'      => $level,
            'levels'     => $this->dropdown->getAllLevel($setting->jenjang),
            'kelas'      => $this->cbt->getKelas($tp->id_tp, $smt->id_smt),
            'ada_ujian'  => $this->cbt->getDataJadwalByTgl(date('Y-m-d')),
            'id_filter'  => $type ?? '',
            'id_guru'    => null,
            'id_mapel'   => null,
            'id_level'   => null,
            'filters'    => ['0' => 'Semua', '2' => 'Mapel', '3' => 'Level'],
        ];

        if ($mode) {
            $terpakai        = $this->cbt->getJadwalTerpakai();
            $jadwal_terpakai = [];
            foreach ($terpakai as $idj => $rows) {
                $jadwal_terpakai[$idj] = count($rows);
            }
            $data['total_siswa'] = $jadwal_terpakai;
        }

        $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel   = [];

        if ($mapel) {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
            }
        }

        $data['guru']   = $guru;
        $data['mapels'] = $arrMapel;

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/jadwal/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function add($id_jadwal)
    {
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model',     'kelas');
        $this->load->model('Dropdown_model',  'dropdown');

        $enable  = $this->input->get('enable', true);
        $user    = $this->ion_auth->user()->row();
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();

        $data = [
            'user'         => $user,
            'judul'        => $id_jadwal == 0 ? 'Tambah Jadwal Ujian' : 'Edit Jadwal Ujian',
            'subjudul'     => 'Jadwal',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'jadwal'       => $id_jadwal == 0
                ? json_decode(json_encode($this->cbt->dummyJadwal()))
                : $this->cbt->getJadwalById($id_jadwal),
            'ruangs'       => $this->cbt->getAllRuang(),
            'sesis'        => $this->dropdown->getAllSesi(),
            'jenis'        => $this->cbt->getAllJenisUjian(),
            'kelas'        => $this->cbt->getKelas($tp->id_tp, $smt->id_smt),
            'disable_opsi' => ($enable !== null && $enable == 1),
        ];

        $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel   = [];

        if ($mapel) {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
            }
        }

        $data['guru']  = $guru;
        $data['mapel'] = $arrMapel;

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/jadwal/add');
        $this->load->view('members/guru/templates/footer');
    }

    public function getBankMapel($id_mapel)
    {
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dashboard_model', 'dashboard');

        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $banks    = $this->cbt->getAllBankSoalByMapel($tp->id_tp, $smt->id_smt, $id_mapel);
        $filtered = [];

        foreach ($banks as $key => $bank) {
            $cek_soal = $this->cbt->getJumlahJenisSoal($key);
            $ada = (isset($cek_soal['1']) ? count($cek_soal['1']) : 0) == (int) $bank->tampil_pg
                && (isset($cek_soal['2']) ? count($cek_soal['2']) : 0) == (int) $bank->tampil_kompleks
                && (isset($cek_soal['3']) ? count($cek_soal['3']) : 0) == (int) $bank->tampil_jodohkan
                && (isset($cek_soal['4']) ? count($cek_soal['4']) : 0) == (int) $bank->tampil_isian
                && (isset($cek_soal['5']) ? count($cek_soal['5']) : 0) == (int) $bank->tampil_esai;

            if ($ada) {
                $filtered[$key] = $bank->bank_kode;
            }
        }

        $this->output_json($filtered);
    }

    public function saveJadwal()
    {
        $this->load->model('Cbt_model',       'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model',       'logging');

        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        if ($this->input->post()) {
            $res     = $this->cbt->saveJadwalUjian($tp->id_tp, $smt->id_smt);
            $message = $res ? 'Jadwal berhasil disimpan' : 'Jadwal sudah ada';
            $status  = $res;
        } else {
            $message = 'Kesalahan 404';
            $status  = false;
        }

        $id = $this->input->post('id_jadwal', true);
        if ($id) {
            $this->logging->saveLog(4, 'mengedit jadwal pelajaran');
        }

        $this->output_json(['success' => $status, 'message' => $message]);
    }

    public function deleteJadwal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model',    'cbt');
        $this->load->model('Log_model',    'logging');

        $id                = $this->input->get('id_jadwal', true);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $terpakai          = isset($jadwal_dikerjakan[$id]) && count($jadwal_dikerjakan[$id]) > 0;
        $jadwal            = $this->cbt->getJadwalById($id);

        if ($terpakai && $jadwal->rekap == 0) {
            $this->output_json(['status' => false, 'message' => 'Hasil Ujian belum direkap']);
            return;
        }

        if ($terpakai) {
            $this->output_json(['status' => false, 'message' => 'Jadwal Ujian sedang digunakan']);
            return;
        }

        $this->master->delete('cbt_jadwal', $id, 'id_jadwal');
        $this->logging->saveLog(5, 'menghapus jadwal ujian');
        $this->output_json(['status' => true, 'message' => 'Jadwal berhasil dihapus']);
    }

    public function deleteAllJadwal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model',    'cbt');
        $this->load->model('Log_model',    'logging');

        $arrId             = json_decode($this->input->post('checked', true));
        $jadwals           = $this->cbt->getJadwalByArrId($arrId);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $backuped          = [];
        $digunakan         = [];

        foreach ($jadwals as $jadwal) {
            $terpakai    = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0 ? 1 : 0;
            $backuped[]  = $jadwal->rekap;
            $digunakan[] = $terpakai;
        }

        $count_terpakai = array_count_values($digunakan);
        $counts         = array_count_values($backuped);

        if (($count_terpakai[1] ?? 0) > 0 && ($counts[0] ?? 0) > 0) {
            $this->output_json([
                'status'    => false,
                'message'   => 'Hasil Ujian belum direkap',
                'digunakan' => $count_terpakai,
                'backup'    => $counts,
            ]);
            return;
        }

        if (($count_terpakai[1] ?? 0) > 0) {
            $this->output_json([
                'status'    => false,
                'message'   => 'Jadwal Ujian sedang digunakan',
                'digunakan' => $count_terpakai,
                'backup'    => $counts,
            ]);
            return;
        }

        $this->master->delete('cbt_jadwal', $arrId, 'id_jadwal');
        $this->logging->saveLog(5, 'menghapus jadwal ujian');
        $this->output_json([
            'status'    => true,
            'message'   => 'Jadwal berhasil dihapus',
            'digunakan' => $count_terpakai,
            'backup'    => $counts,
        ]);
    }
}
