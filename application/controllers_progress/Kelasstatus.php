<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelasstatus extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
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

    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');

        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $data = [
            'user'       => $user,
            'judul'      => 'Nilai Harian Siswa',
            'subjudul'   => 'Nilai',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['gurus']   = $this->dropdown->getAllGuru();
            $data['kelas']   = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['mapels']  = $this->dropdown->getAllMapel();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/status/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
            $arrMapel   = [];
            $arrKelas   = [];
            $arrId      = [];

            if ($mapel != null) {
                foreach ($mapel as $m) {
                    $arrMapel[$m->id_mapel] = $m->nama_mapel;
                    foreach ($m->kelas_mapel as $kls) {
                        $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
                    }
                }
                foreach ($mapel[0]->kelas_mapel as $km) {
                    $arrId[] = $km->kelas;
                }
            }

            $data['guru']    = $guru;
            $data['gurus']   = [$guru->id_guru => $guru->nama_guru];
            $data['id_guru'] = $guru->id_guru;
            $data['mapel']   = $mapel;
            $data['mapels']  = $arrMapel;
            $data['kelas']   = $arrKelas;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/status/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function getMateriGuru()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');

        $id_guru = $this->input->get('id', true);
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $materi  = $this->kelas->getAllKodeMateri($tp->id_tp, $smt->id_smt, $id_guru);

        $arrKelasMateri = [];
        $arrKelasTugas  = [];

        foreach ($materi as $m) {
            $kode_mapel = $m->kode_mapel ?? '--';
            $entry      = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'kelas' => unserialize($m->materi_kelas ?? '')];
            if ($m->jenis == '1') {
                $arrKelasMateri[] = $entry;
            } else {
                $arrKelasTugas[] = $entry;
            }
        }

        $this->output_json(['materi' => $arrKelasMateri, 'tugas' => $arrKelasTugas]);
    }

    public function getMateriMapel()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');

        $id_mapel = $this->input->get('id', true);
        $id_guru  = $this->input->get('id_guru', true);
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $materi   = $this->kelas->getKodeMateriMapel($tp->id_tp, $smt->id_smt, $id_mapel, $id_guru);

        $arrKelasMateri = [];
        $arrKelasTugas  = [];
        $arrKelas       = [];

        foreach ($materi as $m) {
            $kode_mapel = $m->kode_mapel ?? '--';
            if ($m->jenis == '1') {
                $arrKelasMateri[$m->id_kelas]   = [];
                $arrKelasMateri[$m->id_kelas][] = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'guru' => $m->nama_guru, 'jenis' => $m->jenis];
            } else {
                $arrKelasTugas[$m->id_kelas]   = [];
                $arrKelasTugas[$m->id_kelas][] = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'guru' => $m->nama_guru, 'jenis' => $m->jenis];
            }
            $arrKelas[$m->jenis]   = [];
            $arrKelas[$m->jenis][] = $m->id_kelas;
        }

        $this->output_json(['materi' => $arrKelasMateri, 'tugas' => $arrKelasTugas, 'kelas' => $arrKelas]);
    }

    public function loadStatus()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');

        $label    = $this->input->post('label', true);
        $id_kelas = $this->input->post('id_kelas', true);
        $id_kjm   = $this->input->post('id_kjm', true);
        $id_tp    = $this->master->getTahunActive()->id_tp;
        $id_smt   = $this->master->getSemesterActive()->id_smt;
        $jenis    = $label === 'Materi' ? '1' : '2';

        $siswa  = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        $logs   = $this->kelas->getStatusMateriSiswa($id_kjm);
        $info   = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        $materi = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis);

        if (!$materi) {
            $this->output_json(['status' => false]);
            return;
        }

        if ($info != null) {
            $info->istirahat = unserialize($info->istirahat ?? '');
        }

        $numday  = date('N', strtotime($materi->jadwal_materi));
        $jadwals = $this->kelas->loadJadwalSiswaHariIni($id_tp, $id_smt, $id_kelas, $numday, false);
        $key     = array_search($materi->id_mapel, array_column($jadwals, 'id_mapel'));
        $jadwal  = $jadwals[$key] ?? null;

        $ist     = $info != null ? json_decode(json_encode($info->istirahat)) : [];
        $arrIst  = [];
        $arrDur  = [];

        foreach ($ist as $istirahat) {
            $arrIst[]                = $istirahat->ist;
            $arrDur[$istirahat->ist] = $istirahat->dur;
        }

        $jam_mapel = [];
        if ($info != null) {
            $jamMulai  = new DateTime($info->kbm_jam_mulai);
            $jamSampai = new DateTime($info->kbm_jam_mulai);

            for ($i = 0; $i < $info->kbm_jml_mapel_hari; $i++) {
                $jamke = $i + 1;
                if (in_array($jamke, $arrIst)) continue;
                try {
                    $jamSampai->add(new DateInterval('PT' . $info->kbm_jam_pel . 'M'));
                    $jam_mapel[$jamke] = ['dari' => $jamMulai->format('H:i'), 'sampai' => $jamSampai->format('H:i'), 'tgl' => $materi->jadwal_materi];
                    $jamMulai->add(new DateInterval('PT' . $info->kbm_jam_pel . 'M'));
                } catch (Exception $e) {
                }
            }
        }

        $this->output_json(['status' => true, 'siswa' => $siswa, 'logs' => $logs, 'jam_mapel' => $jam_mapel, 'materi' => $materi]);
    }

    public function saveNilai()
    {
        $id_log  = $this->input->post('id_log', true);
        $insert  = ['nilai' => $this->input->post('nilai', true), 'catatan' => $this->input->post('catatan', true)];
        $q       = $this->db->get_where('log_materi', ['id_log' => $id_log]);

        if ($q->num_rows() > 0) {
            $this->db->where('id_log', $id_log);
            $update = $this->db->update('log_materi', $insert);
        } else {
            $this->db->set('id_log', $id_log);
            $update = $this->db->insert('log_materi', $insert);
        }

        $this->output_json($update);
    }
}
