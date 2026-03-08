<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtcetak extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }

        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error(
                'Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>',
                403,
                'Akses Terlarang'
            );
        }

        $this->load->library(['datatables', 'form_validation', 'upload']);
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, bool $encode = true): void
    {
        $output = $encode ? json_encode($data) : $data;
        $this->output->set_content_type('application/json')->set_output($output);
    }

    private function loadBaseData(string $judul, string $subjudul): array
    {
        $this->load->model('Dashboard_model', 'dashboard');

        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        return [
            'user'       => $user,
            'judul'      => $judul,
            'subjudul'   => $subjudul,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
        ];
    }

    private function renderView(array $data, string $view): void
    {
        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view($view);
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view($view);
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function index()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model',    'cbt');

        $data       = $this->loadBaseData('Cetak Data Penilaian', 'Cetak');
        $tp         = $data['tp_active'];
        $smt        = $data['smt_active'];
        $data['kop'] = $this->cbt->getSettingKopAbsensi();

        if (!$this->ion_auth->is_admin()) {
            $data['guru']     = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
            $pengawas         = $this->cbt->getPengawasHariIni(date('Y-m-d'));
            $ids_pengawas     = [];

            foreach ($pengawas as $pws) {
                foreach (explode(',', $pws->id_guru ?? '') as $id) {
                    if ($id !== '' && !in_array($id, $ids_pengawas)) {
                        $ids_pengawas[] = $id;
                    }
                }
            }

            $data['pengawas']     = $pengawas;
            $data['ids_pengawas'] = $ids_pengawas;
        }

        $this->renderView($data, 'cbt/cetak/data');
    }

    public function kartuPeserta()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Rapor_model',    'rapor');
        $this->load->model('Cbt_model',      'cbt');

        $data       = $this->loadBaseData('Cetak Kartu Peserta', 'Cetak');
        $tp         = $data['tp_active'];
        $smt        = $data['smt_active'];

        $data['kartu']         = $this->cbt->getSettingKartu();
        $data['kelas']         = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['ruang']         = $this->dropdown->getAllRuang();
        $data['setting_rapor'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);

        if (!$this->ion_auth->is_admin()) {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
        }

        $this->renderView($data, 'cbt/cetak/kartu');
    }

    public function uploadFile($logo)
    {
        if (!isset($_FILES['logo']['name'])) {
            $this->output_json(['src' => '']);
            return;
        }

        $config = [
            'upload_path'   => './uploads/settings/',
            'allowed_types' => 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF',
            'overwrite'     => true,
            'file_name'     => $logo,
        ];
        $this->upload->initialize($config);
        $this->upload->do_upload('logo');

        $result = $this->upload->data();
        $this->output_json([
            'src'      => base_url() . 'uploads/settings/' . $result['file_name'],
            'filename' => pathinfo($result['file_name'], PATHINFO_FILENAME),
            'status'   => true,
            'type'     => $_FILES['logo']['type'],
            'size'     => $_FILES['logo']['size'],
        ]);
    }

    public function deleteFile()
    {
        $src       = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (unlink($file_name)) {
            echo 'File Delete Successfully';
        }
    }

    public function saveKartu()
    {
        $insert = [
            'id_set_kartu' => 123456,
            'header_1'     => $this->input->post('header_1', true),
            'header_2'     => $this->input->post('header_2', true),
            'header_3'     => $this->input->post('header_3', true),
            'header_4'     => $this->input->post('header_4', true),
            'tanggal'      => $this->input->post('tanggal',  true),
        ];
        $this->output_json($this->db->replace('cbt_kop_kartu', $insert));
    }

    public function getSiswaKelas()
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Kelas_model',     'kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $sesi   = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $kelas  = $this->input->get('kelas');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();

        $ikelas = $kelas === 'all'
            ? $this->kelas->getIdKelas($tp->id_tp, $smt->id_smt)
            : $this->master->getKelasById($kelas);

        $s      = $sesi ?: null;
        $isesi  = $s !== null ? $this->cbt->getSesiById($s) : null;
        $ijadwal = ($jadwal !== null && $jadwal !== 'null')
            ? $this->cbt->getJadwalById($jadwal, $s)
            : null;

        $pengawass = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi);
        $pengawas  = [];
        foreach ($pengawass as $p) {
            $ids = explode(',', $p->id_guru ?? '');
            if (count($ids) > 0) {
                $pengawas = $this->master->getGuruByArrId($ids);
            }
        }

        $siswas = $this->cbt->getRuangSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas, $s);

        $this->output_json([
            'siswa' => array_values($siswas),
            'info'  => ['kelas' => $ikelas, 'sesi' => $isesi, 'jadwal' => $ijadwal, 'pengawas' => $pengawas],
        ]);
    }

    public function getSiswaRuang()
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $ruang  = $this->input->get('ruang');
        $sesi   = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();

        $iruang  = $this->cbt->getRuangById($ruang);
        $s       = ($sesi === 'null') ? null : $sesi;
        $isesi   = $s !== null ? $this->cbt->getSesiById($s) : null;
        $ijadwal = ($jadwal !== null && $jadwal !== 'null')
            ? $this->cbt->getJadwalById($jadwal, $s)
            : null;

        $pengawass = $this->cbt->getPengawas($tp->id_tp . $smt->id_smt . $jadwal . $ruang . $sesi);
        $pengawas  = [];
        if ($pengawass !== null && count(explode(',', $pengawass->id_guru ?? '')) > 0) {
            $pengawas = $this->master->getGuruByArrId(explode(',', $pengawass->id_guru ?? ''));
        }

        $this->output_json([
            'siswa' => $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $s),
            'info'  => ['ruang' => $iruang, 'sesi' => $isesi, 'jadwal' => $ijadwal, 'pengawas' => $pengawas],
        ]);
    }

    public function saveKop()
    {
        $insert = [
            'id_kop'      => 123456,
            'header_1'    => $this->input->post('header_1',   true),
            'header_2'    => $this->input->post('header_2',   true),
            'header_3'    => $this->input->post('header_3',   true),
            'header_4'    => $this->input->post('header_4',   true),
            'proktor'     => $this->input->post('proktor',    true),
            'pengawas_1'  => $this->input->post('pengawas_1', true),
            'pengawas_2'  => $this->input->post('pengawas_2', true),
        ];
        $this->output_json($this->db->replace('cbt_kop_absensi', $insert));
    }

    public function absenPeserta()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model',      'cbt');

        $data       = $this->loadBaseData('Cetak Daftar Kehadiran', 'Cetak');
        $tp         = $data['tp_active'];
        $smt        = $data['smt_active'];

        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $data['mapel']  = $this->dropdown->getAllJadwalMapel($tp->id_tp, $smt->id_smt);
        $data['kelas']  = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['ruang']  = $this->dropdown->getAllRuang();
        $data['sesi']   = $this->dropdown->getAllSesi();
        $data['kop']    = $this->cbt->getSettingKopAbsensi();

        if (!$this->ion_auth->is_admin()) {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
        }

        $this->renderView($data, 'cbt/cetak/absen');
    }

    public function beritaAcara()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model',      'cbt');

        $data    = $this->loadBaseData('Cetak Berita Acara', 'Cetak');
        $tp      = $data['tp_active'];
        $smt     = $data['smt_active'];

        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $data['mapel']  = $this->dropdown->getAllJadwalMapel($tp->id_tp, $smt->id_smt);
        $data['kelas']  = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['ruang']  = $this->dropdown->getAllRuang();
        $data['sesi']   = $this->dropdown->getAllSesi();
        $data['kop']    = $this->cbt->getSettingKopBeritaAcara();

        if (!$this->ion_auth->is_admin()) {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
        }

        $this->renderView($data, 'cbt/cetak/beritaacara');
    }

    public function saveKopBerita()
    {
        $insert = [
            'id_kop'   => 123456,
            'header_1' => $this->input->post('header_1', true),
            'header_2' => $this->input->post('header_2', true),
            'header_3' => $this->input->post('header_3', true),
            'header_4' => $this->input->post('header_4', true),
        ];
        $this->output_json($this->db->replace('cbt_kop_berita', $insert));
    }

    public function pesertaUjian($mode = null)
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model',      'cbt');

        $data    = $this->loadBaseData('Cetak Daftar Peserta', 'Cetak');
        $tp      = $data['tp_active'];
        $smt     = $data['smt_active'];

        $data['kelass'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['ruangs'] = $this->dropdown->getAllRuang();
        $data['sesis']  = $this->cbt->getAllKodeSesi();
        $data['kop']    = $this->dashboard->getSetting();
        $data['ujian']  = $this->dropdown->getAllJenisUjian();
        $data['mode']   = $mode;
        $data['siswa']  = ($mode == '1' || $mode === null)
            ? $this->cbt->getAllPesertaByRuang($tp->id_tp, $smt->id_smt)
            : $this->cbt->getAllPesertaByKelas($tp->id_tp, $smt->id_smt);
        $data['guru']   = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/cetak/pesertaujian');
        $this->load->view('members/guru/templates/footer');
    }

    public function pengawas()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model',      'cbt');

        $data            = $this->loadBaseData('Jadwal Pengawas', 'Cetak Jadwal Pengawas');
        $tp              = $data['tp_active'];
        $smt             = $data['smt_active'];
        $jenis_selected  = $this->input->get('jenis',  true);
        $filter_selected = $this->input->get('filter', true);
        $dari_selected   = $this->input->get('dari',   true);
        $sampai_selected = $this->input->get('sampai', true);

        $data['jenis']           = ['' => 'belum ada jadwal ujian'];
        $data['filter']          = ['0' => 'Semua', '1' => 'Tanggal'];
        $data['jenis_selected']  = $jenis_selected;
        $data['jenis_ujian']     = $this->cbt->getJenisById($jenis_selected);
        $data['filter_selected'] = $filter_selected;
        $data['dari_selected']   = $dari_selected;
        $data['sampai_selected'] = $sampai_selected;
        $data['ruang_sesi']      = $this->cbt->getRuangSesi($tp->id_tp, $smt->id_smt);
        $data['sesi']            = $this->dropdown->getAllSesi();

        $pengawas  = $jenis_selected !== null ? $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt) : [];
        $jadwals   = $jenis_selected !== null
            ? $this->cbt->getJadwalByJenis($jenis_selected, '0', $dari_selected, $sampai_selected)
            : [];

        $arrLevel  = array_unique(array_column($jadwals, 'bank_level'));
        $kelas_level = !empty($arrLevel)
            ? $this->cbt->getDistinctKelasLevel($tp->id_tp, $smt->id_smt, $arrLevel)
            : [];

        $arrKls = array_column($kelas_level, 'id_kelas');
        $ruangs = !empty($arrKls)
            ? $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls)
            : [];

        $gurus   = $this->dropdown->getAllGuru();
        $result  = [];
        $perRuang = [];

        foreach ($ruangs as $id_ruang => $ruang) {
            foreach ($ruang as $id_sesi => $sesi) {
                foreach ($kelas_level as $kl) {
                    foreach ($jadwals as $jadwal) {
                        if ($jadwal->bank_level != $kl->level_id) continue;

                        $nr  = $ruangs[$id_ruang][$id_sesi]->nama_ruang;
                        $ns  = $ruangs[$id_ruang][$id_sesi]->nama_sesi;
                        $ir  = $ruangs[$id_ruang][$id_sesi]->ruang_id;
                        $is  = $ruangs[$id_ruang][$id_sesi]->sesi_id;

                        $sel = (isset($pengawas[$jadwal->id_jadwal][$ir][$is]))
                            ? explode(',', $pengawas[$jadwal->id_jadwal][$ir][$is]->id_guru ?? '')
                            : [];

                        $jp  = 0;
                        $jpp = count($sel);
                        $pw  = '';

                        foreach ($sel as $p) {
                            if (!isset($gurus[$p])) continue;
                            $pw .= $gurus[$p];
                            $jp++;
                            if ($jp < $jpp) {
                                $pw .= '<br>';
                            }
                        }

                        $siswas  = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ir, $is);
                        $forAdd  = (object) [
                            'jml_siswa' => count($siswas),
                            'tanggal'   => $jadwal->tgl_mulai,
                            'ruang'     => $nr,
                            'sesi'      => $ns,
                            'mapel'     => $jadwal->nama_mapel,
                            'waktu'     => $jadwal->jam_ke,
                            'pengawas'  => $pw,
                        ];

                        $result[] = $forAdd;
                        if (isset($perRuang[$forAdd->ruang])) {
                            $perRuang[$forAdd->ruang][] = $forAdd;
                        } else {
                            $perRuang[$forAdd->ruang] = [$forAdd];
                        }
                    }
                }
            }
        }

        $data['pengawas']      = $pengawas;
        $data['kelas_level']   = $kelas_level;
        $data['ruang']         = $ruangs;
        $data['jadwals']       = $result;
        $data['jadwals_ruang'] = $perRuang;
        $data['guru']          = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/cetak/pengawas');
        $this->load->view('members/guru/templates/footer');
    }
}
