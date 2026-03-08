## File: application/controllers_progress/Cbtjadwal.php

```php
<?php

class Cbtjadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
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
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $lvl = $this->input->get('level', true);
        $level = $lvl == null ? '0' : $lvl;
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Jadwal Penilaian', 'subjudul' => 'PH/PTS/PAT/USBK', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $mode = $this->input->get('mode');
        $type = $this->input->get('type');
        $data['mode'] = $mode == null ? '1' : $mode;
        $data['ruangs'] = $this->cbt->getAllRuang();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $data['jadwal'] = json_decode(json_encode($this->cbt->dummyJadwal()));
        $data['jmlIst'] = [];
        $data['jmlMapel'] = [];
        $data['level'] = $level;
        if (!$mode) {
            $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        } else {
            $terpakai = $this->cbt->getJadwalTerpakai();
            $jadwal_terpakai = [];
            foreach ($terpakai as $idj => $rows) {
                $jadwal_terpakai[$idj] = count($rows);
            }
            $data['total_siswa'] = $jadwal_terpakai;
            $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        }
        $data['levels'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel = [];
        if (!$mapel) {
        }
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $data['mapels'] = $arrMapel;
        $data['filters'] = ['0' => 'Semua', '2' => 'Mapel', '3' => 'Level'];
        $data['id_filter'] = $type == null ? '' : $type;
        if ($type == '0') {
        }
        if ($type == '2') {
        }
        if ($type == '3') {
        }
        $data['id_guru'] = null;
        $data['id_mapel'] = null;
        $data['id_level'] = null;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/jadwal/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function add($id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $enable = $this->input->get('enable', true);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => $id_jadwal == 0 ? 'Tambah Jadwal Ujian' : 'Edit Jadwal Ujian', 'subjudul' => 'Jadwal', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($id_jadwal == 0) {
            $data['jadwal'] = json_decode(json_encode($this->cbt->dummyJadwal()));
        } else {
            $data['jadwal'] = $this->cbt->getJadwalById($id_jadwal);
        }
        $gurus = $this->dropdown->getAllGuru();
        $data['ruangs'] = $this->cbt->getAllRuang();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['jenis'] = $this->cbt->getAllJenisUjian();
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $data['disable_opsi'] = $enable != null && $enable == 1;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $arrMapel = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
        }
        $data['mapel'] = $arrMapel;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/jadwal/add');
        $this->load->view('members/guru/templates/footer');
    }
    public function getBankMapel($id_mapel)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $banks = $this->cbt->getAllBankSoalByMapel($tp->id_tp, $smt->id_smt, $id_mapel);
        $filtered = [];
        foreach ($banks as $key => $bank) {
            $cek_soal = $this->cbt->getJumlahJenisSoal($key);
            $num1 = isset($cek_soal['1']) ? count($cek_soal['1']) : 0;
            $num2 = isset($cek_soal['2']) ? count($cek_soal['2']) : 0;
            $num3 = isset($cek_soal['3']) ? count($cek_soal['3']) : 0;
            $num4 = isset($cek_soal['4']) ? count($cek_soal['4']) : 0;
            $num5 = isset($cek_soal['5']) ? count($cek_soal['5']) : 0;
            $ada1 = $num1 == (int) $bank->tampil_pg;
            $ada2 = $num2 == (int) $bank->tampil_kompleks;
            $ada3 = $num3 == (int) $bank->tampil_jodohkan;
            $ada4 = $num4 == (int) $bank->tampil_isian;
            $ada5 = $num5 == (int) $bank->tampil_esai;
            if (!($ada1 && $ada2 && $ada3 && $ada4 && $ada5)) {
            } else {
                $filtered[$key] = $bank->bank_kode;
            }
        }
        $this->output_json($filtered);
    }
    public function saveJadwal()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        if ($this->input->post()) {
            $res = $this->cbt->saveJadwalUjian($tp->id_tp, $smt->id_smt);
            $data['message'] = $res ? 'Jadwal berhasil disimpan' : 'Jadwal sudah ada';
            $status = $res;
        } else {
            $data['message'] = 'Kesalahan 404';
            $status = FALSE;
        }
        $data['success'] = $status;
        $id = $this->input->post('id_jadwal', true);
        if (!$id) {
        }
        $this->logging->saveLog(4, 'mengedit jadwal pelajaran');
        $this->output_json($data);
    }
    public function deleteJadwal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $id = $this->input->get('id_jadwal', true);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $terpakai = isset($jadwal_dikerjakan[$id]) && count($jadwal_dikerjakan[$id]) > 0;
        $data['status'] = false;
        $jadwal = $this->cbt->getJadwalById($id);
        if ($terpakai && $jadwal->rekap == 0) {
            $data['status'] = false;
            $data['message'] = 'Hasil Ujian belum direkap';
        } else {
            if ($this->master->delete('cbt_jadwal', $id, 'id_jadwal')) {
            }
            $data['status'] = false;
            $data['message'] = 'Jadwal Ujian sedang digunakan';
        }
        $this->output_json($data);
    }
    public function deleteAllJadwal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $arrId = json_decode($this->input->post('checked', true));
        ob_start();
        $jadwals = $this->cbt->getJadwalByArrId($arrId);
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $backuped = [];
        $digunakan = [];
        foreach ($jadwals as $jadwal) {
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0 ? 1 : 0;
            array_push($backuped, $jadwal->rekap);
            array_push($digunakan, $terpakai);
        }
        $count_terpakai = array_count_values($digunakan);
        $counts = array_count_values($backuped);
        if ($count_terpakai[1] > 0 && $counts[0] > 0) {
            ob_end_clean();
            $data['status'] = false;
            $data['message'] = 'Hasil Ujian belum direkap';
        } else {
            if ($this->master->delete('cbt_jadwal', $arrId, 'id_jadwal')) {
            }
            ob_end_clean();
            $data['status'] = false;
            $data['message'] = 'Jadwal Ujian sedang digunakan';
        }
        $data['digunakan'] = $count_terpakai;
        $data['backup'] = $counts;
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Cbtjenis.php

```php
<?php

class Cbtjenis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
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
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
    }
    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Jenis Ujian', 'subjudul' => 'Data Jenis Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/jenis/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getJenis(), false);
    }
    public function add()
    {
        $this->load->model('Master_model', 'master');
        $insert = ['nama_jenis' => $this->input->post('nama_jenis', true), 'kode_jenis' => $this->input->post('kode_jenis', true)];
        $this->master->create('cbt_jenis', $insert, false);
        $data['status'] = $insert;
        $this->output_json($data);
    }
    public function update()
    {
        $this->load->model('Cbt_model', 'cbt');
        $data = $this->cbt->updateJenis();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('cbt_jenis', $chk, 'id_jenis')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function saveLog($type, $desc)
    {
        $this->load->model('Log_model', 'logging');
        $user = $this->ion_auth->user()->row();
        $this->logging->saveLog($type, $desc);
    }
}
```

---

## File: application/controllers_progress/Cbtnilai.php

```php
<?php

class Cbtnilai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->form_validation->set_error_delimiters('', '');
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
    private function arrToUpper($val)
    {
        return strtoupper($val ?? '');
    }
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            } else {
                sort($subArray);
            }
        }
    }
    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $user = $this->ion_auth->user()->row();
        $this->db->trans_start();
        $data = ['user' => $user, 'judul' => 'Hasil Ujian Siswa', 'subjudul' => 'Nilai Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['sesi'] = $this->dropdown->getAllSesi();
        $kelas_selected = $this->input->get('kelas');
        $jadwal_selected = $this->input->get('jadwal');
        $data['kelas_selected'] = $kelas_selected;
        $ya = $this->input->get('ya');
        $yb = $this->input->get('yb');
        $xa = $this->input->get('xa');
        $xb = $this->input->get('xb');
        if ($this->ion_auth->in_group('guru')) {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $id_guru = $guru->id_guru;
        } else {
            $id_guru = null;
        }
        if ($jadwal_selected != null) {
        }
        $data['jadwal'] = [];
        $data['siswas'] = [];
        $this->db->trans_complete();
        if ($this->ion_auth->is_admin()) {
        }
        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $data['jadwal_selected'] = $jadwal_selected;
        $arrKelas = [];
        if (!($mapel != null)) {
        }
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                if (!$kls->kelas) {
                } else {
                    $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
                }
            }
        }
        $data['kelas'] = $arrKelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/nilai/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function detail()
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswa = $this->cbt->getSiswaById($tp->id_tp, $smt->id_smt, $this->input->get('siswa'));
        $jadwal = $this->input->get('jadwal');
        $info = $this->cbt->getJadwalById($jadwal);
        $bagi_pg = $info->tampil_pg / 100;
        $bobot_pg = $info->bobot_pg / 100;
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian = $info->tampil_isian / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_essai = $info->bobot_esai / 100;
        $jawabans = $this->cbt->getJawabanSiswaByJadwal($jadwal, $siswa->id_siswa);
        $soal = [];
        $jawabans_siswa = [];
        foreach ($jawabans as $jawaban_siswa) {
            if (!($jawaban_siswa->jenis_soal == '2')) {
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
            } else {
                $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar ?? ['']);
                $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                $jawaban_siswa->jawaban = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban ?? ['']);
                $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban, 'strlen');
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
            $arrAlphabet = range('A', 'Z');
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            $arrjwbnSiswa = [];
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                } else {
                    $arrjwbnSiswa[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($idxs > 0)) {
                        } else {
                            if (!($jb === '1')) {
                            }
                            $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                        }
                    }
                }
            }
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $arrjwbn = [];
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                } else {
                    $arrjwbn[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($idxs > 0)) {
                        } else {
                            if (!($jb === '1')) {
                            }
                            $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                        }
                    }
                }
            }
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
        }
        $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
        $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
        $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
        $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
        $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
        $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
        $skor = new stdClass();
        $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
        if (!($nilai_input != null)) {
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
        } else {
            $skor->dikoreksi = $nilai_input->dikoreksi;
            $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
        }
        $benar_pg = 0;
        $salah_pg = 0;
        if (!($info->tampil_pg > 0)) {
        }
        if (!($jawaban_pg && count($jawaban_pg) > 0)) {
        }
        foreach ($jawaban_pg as $num => $jwb_pg) {
            $benar = false;
            if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                $ks = array_search($jwb_pg->nomor_soal, array_column($soal[1], 'nomor_soal'));
            } else {
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                }
                $salah_pg += 1;
                $benar = false;
                $ks = array_search($jwb_pg->nomor_soal, array_column($soal[1], 'nomor_soal'));
            }
            $soal[1][$ks]->point = !$benar ? 0 : ($info->bobot_pg > 0 ? round($info->bobot_pg / $info->tampil_pg, 2) : 0);
            $analisa = $benar ? '<i class="fa fa-check-circle text-green text-lg"></i>' : '<i class="fa fa-times-circle text-red text-lg"></i>';
            $soal[1][$ks]->analisa = $analisa;
        }
        $skor->skor_pg = $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
        $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
        $benar_pg2 = 0;
        $skor_koreksi_pg2 = 0.0;
        $otomatis_pg2 = 0;
        if (!($info->tampil_kompleks > 0)) {
        }
        if (!($jawaban_pg2 && count($jawaban_pg2) > 0)) {
        }
        foreach ($jawaban_pg2 as $num => $jawab_pg2) {
            $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
            $arr_benar = [];
            if (!$jawab_pg2->jawaban_siswa) {
                if (!($jawab_pg2->jawaban && count($jawab_pg2->jawaban) > 0)) {
                }
            } else {
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    if (!in_array($js, $jawab_pg2->jawaban)) {
                    } else {
                        array_push($arr_benar, true);
                    }
                }
                if (!($jawab_pg2->jawaban && count($jawab_pg2->jawaban) > 0)) {
                }
            }
            $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
            $point_benar = $info->bobot_kompleks > 0 ? round($info->bobot_kompleks / $info->tampil_kompleks, 2) : 0;
            $point_item = count($jawab_pg2->jawaban) > 0 ? $point_benar / count($jawab_pg2->jawaban) : 0;
            $pk = $point_item * count($arr_benar);
            $jml_benar = count($arr_benar);
            if ($jml_benar == count($jawab_pg2->jawaban)) {
            }
            if ($jml_benar > 0 && $jml_benar < count($jawab_pg2->jawaban)) {
            }
            $analisa = '<i class="fa fa-times-circle text-red text-lg"></i>';
            $ks = array_search($jawab_pg2->nomor_soal, array_column($soal[2], 'nomor_soal'));
            $point = round($pk, 2);
            $soal[2][$ks]->analisa = $analisa;
            if ($jawab_pg2->nilai_otomatis == '0') {
            }
            $soal[2][$ks]->point = $jawab_pg2->nilai_koreksi;
            $soal[2][$ks]->point_koreksi = $jawab_pg2->nilai_koreksi;
            $soal[2][$ks]->point_otomatis = $point;
            $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
        }
        $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
        $input_pg2 = 0;
        if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
        }
        $input_pg2 = $nilai_input->kompleks_nilai;
        $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
        $skor->skor_kompleks = $skor_pg2;
        $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
        $benar_jod = 0;
        $skor_koreksi_jod = 0.0;
        $otomatis_jod = 0;
        $sameCounts = [];
        $differentCounts = [];
        if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
        }
        foreach ($jawaban_jodoh as $num => $jawab_jod) {
            $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
            $typeSoal = $jawab_jod->jawaban->type;
            $arrSoal = $jawab_jod->jawaban->jawaban;
            $ks = array_search($jawab_jod->nomor_soal, array_column($soal[3], 'nomor_soal'));
            $item_benar = 0;
            $item_salah = 0;
            $item_kurang = 0;
            $items = 0;
            $arrBenar = [];
            $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
            if (!isset($jawab_jod->jawaban_siswa->links)) {
                $point_soal = 1 / $items * $item_benar * $point_benar;
            } else {
                $array1 = (array) $jawab_jod->jawaban_benar->links;
                $this->sortArrays($array1);
                $array2 = (array) $jawab_jod->jawaban_siswa->links;
                $this->sortArrays($array2);
                $sameCount = 0;
                $differentCount = 0;
                foreach ($array1 as $key => $subArray1) {
                    $arrBenar[$key] = new stdClass();
                    $arrBenar[$key]->benar = 0;
                    $arrBenar[$key]->salah = 0;
                    $arrBenar[$key]->kurang = 0;
                    $items += count($subArray1);
                    if (isset($array2[$key])) {
                        $subArray2 = $array2[$key];
                        $sameItems = array_intersect($subArray1, $subArray2);
                        $sameCount += count($sameItems);
                        $item_benar += count($sameItems);
                        $arrBenar[$key]->benar += count($sameItems);
                        $diffItems1 = array_diff($subArray1, $subArray2);
                        $diffItems2 = array_diff($subArray2, $subArray1);
                        $differentCount += count($diffItems1) + count($diffItems2);
                        $item_kurang += count($diffItems1) + count($diffItems2);
                        $arrBenar[$key]->kurang += count($diffItems1);
                    } else {
                        $differentCount += count($subArray1);
                        $item_kurang += count($subArray1);
                        $arrBenar[$key]->kurang += count($subArray1);
                    }
                }
                $sameCounts[3][$ks] = $sameCount;
                $differentCounts[3][$ks] = $differentCount;
                $point_soal = 1 / $items * $item_benar * $point_benar;
            }
            $benar_jod += 1 / $items * $item_benar;
            $headSoal = array_shift($arrSoal);
            $arrJwbSoal = [];
            foreach ($arrSoal as $kolSoal) {
                $jwb = new stdClass();
                foreach ($kolSoal as $pos => $kol) {
                    if (!($kol == '1')) {
                    } else {
                        $jwb->subtitle[] = $headSoal[$pos];
                    }
                }
                $jwb->title = array_shift($kolSoal);
                $arrJwbSoal[] = $jwb;
            }
            $soal[3][$ks]->type_soal = $typeSoal;
            $soal[3][$ks]->tabel_soal = $arrJwbSoal;
            $arrJawab = [];
            if (!isset($jawab_jod->jawaban_siswa->jawaban)) {
            }
            $arrJawab = $jawab_jod->jawaban_siswa->jawaban;
            $headJawab = array_shift($arrJawab);
            $arrJwbJawab = [];
            foreach ($arrJawab as $kolJawab) {
                $jwbs = new stdClass();
                foreach ($kolJawab as $po => $kol) {
                    if (!($kol == '1')) {
                    } else {
                        $sub = $headJawab[$po];
                        $jwbs->subtitle[] = $sub;
                    }
                }
                $jwbs->title = array_shift($kolJawab);
                $arrJwbJawab[] = $jwbs;
            }
            $soal[3][$ks]->tabel_jawab = $arrJwbJawab;
            $soal[3][$ks]->tabel_benar = $arrBenar;
            $soal[3][$ks]->point_soal = $point_soal;
            $point = round($point_soal, 2);
            if ($jawab_jod->nilai_otomatis == '0') {
            }
            $soal[3][$ks]->point = $jawab_jod->nilai_koreksi;
            $soal[3][$ks]->point_koreksi = $jawab_jod->nilai_koreksi;
            $soal[3][$ks]->point_otomatis = $point;
            if ($item_benar == $items && $item_salah == 0 && $item_kurang == 0) {
            }
            if ($item_benar == 0) {
            }
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            $soal[3][$ks]->analisa = $analisa;
            $otomatis_jod = $jawab_jod->nilai_otomatis;
        }
        $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
        $input_jod = 0;
        if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
        }
        $input_jod = $nilai_input->jodohkan_nilai;
        $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
        $skor->skor_jodohkan = $skor_jod;
        $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
        $benar_is = 0;
        $skor_koreksi_is = 0.0;
        $otomatis_is = 0;
        if (!($info->tampil_isian > 0)) {
        }
        if (!(count($jawaban_is) > 0)) {
        }
        foreach ($jawaban_is as $num => $jawab_is) {
            $skor_koreksi_is += $jawab_is->nilai_koreksi;
            $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
            if (!$benar) {
                $ks = array_search($jawab_is->nomor_soal, array_column($soal[4], 'nomor_soal'));
            } else {
                $benar_is++;
                $ks = array_search($jawab_is->nomor_soal, array_column($soal[4], 'nomor_soal'));
            }
            $point = !$benar ? 0 : ($info->bobot_isian > 0 ? round($info->bobot_isian / $info->tampil_isian, 2) : 0);
            if ($jawab_is->nilai_otomatis == '0') {
            }
            $soal[4][$ks]->point = $jawab_is->nilai_koreksi;
            $soal[4][$ks]->point_koreksi = $jawab_is->nilai_koreksi;
            $soal[4][$ks]->point_otomatis = $point;
            if ($benar) {
            }
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            $soal[4][$ks]->analisa = $analisa;
            $otomatis_is = $jawab_is->nilai_otomatis;
        }
        $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
        $input_is = 0;
        if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
        }
        $input_is = $nilai_input->isian_nilai;
        $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
        $skor->skor_isian = $skor_is;
        $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
        $benar_es = 0;
        $skor_koreksi_es = 0.0;
        $otomatis_es = 0;
        if (!($info->tampil_esai > 0)) {
        }
        if (!(count($jawaban_es) > 0)) {
        }
        foreach ($jawaban_es as $num => $jawab_es) {
            $skor_koreksi_es += $jawab_es->nilai_koreksi;
            $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
            if (!$benar) {
                $ks = array_search($jawab_es->nomor_soal, array_column($soal[5], 'nomor_soal'));
            } else {
                $benar_es++;
                $ks = array_search($jawab_es->nomor_soal, array_column($soal[5], 'nomor_soal'));
            }
            $point = !$benar ? 0 : ($info->bobot_esai > 0 ? round($info->bobot_esai / $info->tampil_esai, 2) : 0);
            if ($jawab_es->nilai_otomatis == '0') {
            }
            $soal[5][$ks]->point = $jawab_es->nilai_koreksi;
            $soal[5][$ks]->point_koreksi = $jawab_es->nilai_koreksi;
            $soal[5][$ks]->point_otomatis = $point;
            if ($benar) {
            }
            $analisa = '<i class="fa fa-times-circle text-yellow text-lg"></i>';
            $soal[5][$ks]->analisa = $analisa;
            $otomatis_es = $jawab_es->nilai_otomatis;
        }
        $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
        $input_es = 0;
        if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
        }
        $input_es = $nilai_input->essai_nilai;
        $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
        $skor->skor_essai = $skor_es;
        $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
        $skor->skor_total = $total;
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $dur_siswa = null;
        foreach ($durasies as $durasi) {
            if (!($durasi->id_siswa == $siswa->id_siswa)) {
            } else {
                $dur_siswa = $durasi;
            }
        }
        $log_siswa = [];
        foreach ($logs as $log) {
            if (!($log->id_siswa == $siswa->id_siswa)) {
            } else {
                array_push($log_siswa, $log);
            }
        }
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Koreksi Hasil Siswa', 'subjudul' => 'Hasil Siswa', 'setting' => $this->dashboard->getSetting(), 'durasi' => $dur_siswa, 'log' => $log_siswa];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['info'] = $info;
        $data['siswa'] = $siswa;
        $data['soal'] = $soal;
        $data['skor'] = $skor;
        $nilai_siswa = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
        $data['ada_nilai'] = $nilai_siswa != null;
        if ($this->ion_auth->is_admin()) {
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/nilai/detail');
        $this->load->view('members/guru/templates/footer');
    }
    public function simpanKoreksi()
    {
        $siswa = $this->input->post('siswa', true);
        $jadwal = $this->input->post('jadwal', true);
        $jenis = $this->input->post('jenis', true);
        $nilais = json_decode($this->input->post('nilai', true));
        $updated = [];
        $ids = [];
        $jml = 0;
        foreach ($nilais as $nilai) {
            array_push($ids, $nilai->id_soal);
            $jml += $nilai->koreksi;
            $updated[] = ['id_soal_siswa' => $nilai->id_soal, 'nilai_koreksi' => $nilai->koreksi, 'nilai_otomatis' => 1];
        }
        $updated = $this->db->update_batch('cbt_soal_siswa', $updated, 'id_soal_siswa');
        if (!$updated) {
            $data['success'] = $updated;
        } else {
            $this->db->set($jenis, $jml);
            $this->db->where('id_nilai', $siswa . '0' . $jadwal);
            $this->db->update('cbt_nilai');
            $data['success'] = $updated;
        }
        $this->output_json($data);
    }
    public function tandaiKoreksi()
    {
        $siswa = $this->input->post('siswa', true);
        $jadwal = $this->input->post('jadwal', true);
        $this->db->set('dikoreksi', 1);
        $this->db->where('id_nilai', $siswa . '0' . $jadwal);
        $updated = $this->db->update('cbt_nilai');
        $data['success'] = $updated;
        $this->output_json($data);
    }
    public function tandaisemua()
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_jadwal = $this->input->post('id_jadwal', true);
        $siswas = $this->input->post('ids', true);
        $updated = 0;
        $test_data = [];
        foreach ($siswas as $id_siswa => $memulai) {
            $info = $this->cbt->getJadwalById($id_jadwal);
            $jawabans = $this->cbt->getJawabanByBank($info->id_bank, $id_siswa);
            $jawabans_siswa = [];
            foreach ($jawabans as $jawaban_siswa) {
                if (!($jawaban_siswa->jenis_soal == '2')) {
                    if (!($jawaban_siswa->jenis_soal == '3')) {
                    }
                } else {
                    $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                    $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                    $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                    $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar ?? ['']);
                    $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                    if (!($jawaban_siswa->jenis_soal == '3')) {
                    }
                }
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
                $arrAlphabet = range('A', 'Z');
                if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
                }
                $arrjwbnSiswa = [];
                if (!$jawaban_siswa->jawaban_siswa) {
                }
                foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    } else {
                        $arrjwbnSiswa[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if (!($idxs > 0)) {
                            } else {
                                if (!($jb === '1')) {
                                }
                                $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                            }
                        }
                    }
                }
                if ($jawaban_siswa->jawaban_siswa) {
                }
                $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $arrjwbn = [];
                foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    } else {
                        $arrjwbn[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if (!($idxs > 0)) {
                            } else {
                                if (!($jb === '1')) {
                                }
                                $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                            }
                        }
                    }
                }
                $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
                $jawabans_siswa[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            }
            $ada_jawaban_isian = isset($jawabans_siswa['4']);
            $ada_jawaban_essai = isset($jawabans_siswa['5']);
            $bagi_pg = $info->tampil_pg / 100;
            $bobot_pg = $info->bobot_pg / 100;
            $bagi_pg2 = $info->tampil_kompleks / 100;
            $bobot_pg2 = $info->bobot_kompleks / 100;
            $bagi_jodoh = $info->tampil_jodohkan / 100;
            $bobot_jodoh = $info->bobot_jodohkan / 100;
            $bagi_isian = $info->tampil_isian / 100;
            $bobot_isian = $info->bobot_isian / 100;
            $bagi_essai = $info->tampil_esai / 100;
            $bobot_essai = $info->bobot_esai / 100;
            $jawaban_pg = isset($jawabans_siswa['1']) ? $jawabans_siswa['1'] : [];
            $benar_pg = 0;
            $salah_pg = 0;
            if (!($info->tampil_pg > 0)) {
                $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            } else {
                if (!(count($jawaban_pg) > 0)) {
                }
                foreach ($jawaban_pg as $jwb_pg) {
                    if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                    } else {
                        if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban_benar ?? '')) {
                        }
                        $salah_pg += 1;
                    }
                }
                $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            }
            $jawaban_pg2 = isset($jawabans_siswa['2']) ? $jawabans_siswa['2'] : [];
            $benar_pg2 = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!(count($jawaban_pg2) > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $arr_benar = [];
                if (!$jawab_pg2->jawaban_siswa) {
                    if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                    }
                } else {
                    foreach ($jawab_pg2->jawaban_siswa as $js) {
                        if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                        } else {
                            array_push($arr_benar, true);
                        }
                    }
                    if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                    }
                }
                $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
            }
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;
            $jawaban_jodoh = isset($jawabans_siswa['3']) ? $jawabans_siswa['3'] : [];
            $benar_jod = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod = 0;
            $sameCounts = [];
            $differentCounts = [];
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $item_benar = 0;
                $item_salah = 0;
                $item_kurang = 0;
                $items = 0;
                $arrBenar = [];
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                    $point_soal = 1 / $items * $item_benar * $point_benar;
                } else {
                    $array1 = (array) $jawab_jod->jawaban_benar->links;
                    $this->sortArrays($array1);
                    $array2 = (array) $jawab_jod->jawaban_siswa->links;
                    $this->sortArrays($array2);
                    $sameCount = 0;
                    $differentCount = 0;
                    foreach ($array1 as $key => $subArray1) {
                        $arrBenar[$key] = new stdClass();
                        $arrBenar[$key]->benar = 0;
                        $arrBenar[$key]->salah = 0;
                        $arrBenar[$key]->kurang = 0;
                        $items += count($subArray1);
                        if (isset($array2[$key])) {
                            $subArray2 = $array2[$key];
                            $sameItems = array_intersect($subArray1, $subArray2);
                            $sameCount += count($sameItems);
                            $item_benar += count($sameItems);
                            $arrBenar[$key]->benar += count($sameItems);
                            $diffItems1 = array_diff($subArray1, $subArray2);
                            $diffItems2 = array_diff($subArray2, $subArray1);
                            $differentCount += count($diffItems1) + count($diffItems2);
                            $item_kurang += count($diffItems1) + count($diffItems2);
                            $arrBenar[$key]->kurang += count($diffItems1);
                        } else {
                            $differentCount += count($subArray1);
                            $item_kurang += count($subArray1);
                            $arrBenar[$key]->kurang += count($subArray1);
                        }
                    }
                    $point_soal = 1 / $items * $item_benar * $point_benar;
                }
                $benar_jod += 1 / $items * $item_benar;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
            }
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $skor_jod = $otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod;
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa['4'] : [];
            $benar_is = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is = 0;
            if (!($info->tampil_isian > 0)) {
            }
            if (!(count($jawaban_is) > 0)) {
            }
            foreach ($jawaban_is as $num => $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban_benar ?? '');
                if (!$benar) {
                    $otomatis_is = $jawab_is->nilai_otomatis;
                } else {
                    $benar_is++;
                    $otomatis_is = $jawab_is->nilai_otomatis;
                }
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa['5'] : [];
            $benar_es = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es = 0;
            if (!($info->tampil_esai > 0)) {
            }
            if (!(count($jawaban_es) > 0)) {
            }
            foreach ($jawaban_es as $num => $jawab_es) {
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban_benar ?? '');
                if (!$benar) {
                    $otomatis_es = $jawab_es->nilai_otomatis;
                } else {
                    $benar_es++;
                    $otomatis_es = $jawab_es->nilai_otomatis;
                }
            }
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;
            $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
            $insert = ['id_nilai' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'pg_benar' => $benar_pg, 'pg_nilai' => round($skor_pg, 2), 'kompleks_nilai' => round($skor_pg2, 2), 'jodohkan_nilai' => round($skor_jod, 2), 'isian_nilai' => round($skor_is, 2), 'essai_nilai' => round($skor_es, 2), 'dikoreksi' => $memulai === '2' ? '0' : '1'];
            $test_data[] = $insert;
            $upd = $this->db->replace('cbt_nilai', $insert);
            if (!$upd) {
            }
            $updated++;
        }
        $data['success'] = $updated;
        $data['siswa'] = $siswas;
        $this->output_json($data);
    }
    public function inputEssai()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $kelas_selected = $this->input->get('kelas');
        $jadwal_selected = $this->input->get('jadwal');
        $info = $this->cbt->getJadwalById($jadwal_selected);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas_selected);
        $ids = [];
        foreach ($siswas as $key => $val) {
            array_push($ids, $val->id_siswa);
        }
        $nilai = $this->cbt->getNilaiAllSiswa([$jadwal_selected], $ids);
        foreach ($siswas as $siswa) {
            $siswa->skor_pg = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->pg_nilai : '0';
            $siswa->skor_pg2 = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->kompleks_nilai : '0';
            $siswa->skor_jod = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->jodohkan_nilai : '0';
            $siswa->skor_isian = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->isian_nilai : '0';
            $siswa->skor_essai = isset($nilai[$siswa->id_siswa]) ? $nilai[$siswa->id_siswa]->essai_nilai : '0';
        }
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Input Nilai Manual', 'subjudul' => '', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $data['nama_kelas'] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kelas_selected);
        $data['kelas_selected'] = $kelas_selected;
        $data['jadwal_selected'] = $jadwal_selected;
        $data['jadwal'] = $info;
        $data['siswas'] = $siswas;
        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/nilai/nilai_essai');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/nilai/nilai_essai');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function simpanKoreksiEssai()
    {
        $this->load->model('Cbt_model', 'cbt');
        $jadwal = $this->input->post('jadwal', true);
        $nilais = json_decode($this->input->post('nilai', true));
        $update = 0;
        $blm_selesai = [];
        foreach ($nilais as $nilai) {
            $nilai_siswa = $this->cbt->getNilaiSiswaByJadwal($jadwal, $nilai->id_siswa);
            if ($nilai_siswa != null) {
                $replace = ['id_nilai' => $nilai_siswa->id_nilai, 'id_siswa' => $nilai_siswa->id_siswa, 'id_jadwal' => $nilai_siswa->id_jadwal, 'pg_benar' => $nilai_siswa->pg_benar, 'pg_nilai' => $nilai_siswa->pg_nilai, 'kompleks_nilai' => isset($nilai->kompleks_nilai) && $nilai->kompleks_nilai != null ? $nilai->kompleks_nilai : '0', 'jodohkan_nilai' => isset($nilai->jodohkan_nilai) && $nilai->jodohkan_nilai != null ? $nilai->jodohkan_nilai : '0', 'isian_nilai' => isset($nilai->isian_nilai) && $nilai->isian_nilai != null ? $nilai->isian_nilai : '0', 'essai_nilai' => isset($nilai->essai_nilai) && $nilai->essai_nilai != null ? $nilai->essai_nilai : '0', 'dikoreksi' => '1'];
                $up = $this->db->replace('cbt_nilai', $replace);
                if (!$up) {
                }
                $update++;
            } else {
                array_push($blm_selesai, $nilai->id_siswa);
            }
        }
        $data['success'] = $update;
        $data['data'] = $nilais;
        $data['blm_selesai'] = count($blm_selesai);
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Cbtnomorpeserta.php

```php
<?php

class Cbtnomorpeserta extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Nomor Peserta', 'subjudul' => 'Generate Nomor Peserta Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['sesi'] = $this->dropdown->getAllSesi();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/nomorpeserta/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function saveNomor()
    {
        $input = json_decode($this->input->post('siswa', true));
        $arrNomor = $this->cbt->getAllNomorPeserta();
        $tp = $this->dashboard->getTahunActive();
        $update = false;
        foreach ($input as $in) {
            $nomorAda = isset($arrNomor[$in->id]) ? $arrNomor[$in->id] : null;
            if ($nomorAda != null && $nomorAda->nomor_peserta == $in->nomor && $nomorAda->id_siswa != $in->id) {
                $update = false;
            } else {
                $insert = ['id_nomor' => $in->id . $tp->id_tp, 'id_siswa' => $in->id, 'id_tp' => $tp->id_tp, 'nomor_peserta' => $in->nomor];
                $update = $this->db->replace('cbt_nomor_peserta', $insert);
            }
        }
        $this->output_json($update);
    }
    public function resetNomor()
    {
        $input = json_decode($this->input->get('kelas', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->cbt->getSiswaByKelasArray($tp->id_tp, $smt->id_smt, $input);
        foreach ($siswas as $siswa) {
            $insert = ['id_nomor' => $siswa->id_siswa . $tp->id_tp, 'id_siswa' => $siswa->id_siswa, 'id_tp' => $tp->id_tp, 'nomor_peserta' => ''];
            $update = $this->db->replace('cbt_nomor_peserta', $insert);
        }
        $res['status'] = $update;
        $this->output_json($res);
    }
    public function getSiswaKelas($arr_kelas)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kelas = json_decode(urldecode($arr_kelas));
        $siswas = $this->cbt->getSiswaByKelasArray($tp->id_tp, $smt->id_smt, $kelas);
        $arrNomor = $this->cbt->getAllNomorPeserta();
        $data['siswa'] = $siswas;
        $data['nomor'] = $arrNomor;
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Cbtpengawas.php

```php
<?php

class Cbtpengawas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Atur Pengawas', 'subjudul' => 'Pengawas Ujian/Ulangan', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $kelass = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $kelass;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['gurus'] = $this->dropdown->getAllGuru();
        $id_jenis = $this->cbt->getDistinctJenisJadwal($tp->id_tp, $smt->id_smt);
        $ids = [];
        if (!($id_jenis && count($id_jenis) > 0)) {
            if ($ids && count($ids) > 0) {
            }
        } else {
            foreach ($id_jenis as $jenis) {
                array_push($ids, $jenis->id_jenis);
            }
            if ($ids && count($ids) > 0) {
            }
        }
        $data['jenis'] = ['' => 'belum ada jadwal ujian'];
        $jenis_selected = $this->input->get('jenis', true);
        $data['jenis_selected'] = $jenis_selected;
        $tglJadwals = [];
        if (!($jenis_selected != null)) {
        }
        $tglJadwals = $this->cbt->getAllJadwalByJenis($jenis_selected, $tp->id_tp, $smt->id_smt);
        foreach ($tglJadwals as $tgl => $jadwalss) {
            foreach ($jadwalss as $mpl => $jadwals) {
                foreach ($jadwals as $jadwal) {
                    $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
                    foreach ($jadwal->bank_kelas as $kb) {
                        if (!($kb['kelas_id'] != '')) {
                        } else {
                            $klss = $this->cbt->getKelasUjian($kb['kelas_id']);
                            $jadwal->peserta[] = $klss;
                        }
                    }
                }
            }
        }
        $data['tgl_jadwals'] = $tglJadwals;
        $data['ruang'] = $this->dropdown->getAllRuang();
        $data['sesi'] = $this->dropdown->getAllSesi();
        $data['ruang_sesi'] = $this->cbt->getRuangSesi($tp->id_tp, $smt->id_smt);
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []);
        $data['pengawas'] = $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/pengawas/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function savePengawas()
    {
        $input = json_decode($this->input->post('data', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $id_tp = $tp->id_tp;
        $id_smt = $smt->id_smt;
        $updated = 0;
        foreach ($input as $d) {
            $ruang = $d->ruang;
            $sesi = $d->sesi;
            $jadwal = $d->jadwal;
            $id_pengawas = $id_tp . $id_smt . $jadwal . $ruang . $sesi;
            $dataInsert = ['id_pengawas' => $id_pengawas, 'id_jadwal' => $jadwal, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_ruang' => $ruang, 'id_sesi' => $sesi, 'id_guru' => implode(',', $d->guru)];
            $update = $this->db->replace('cbt_pengawas', $dataInsert);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $data['error'] = '--';
        $data['status'] = $updated;
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Cbtrekap.php

```php
<?php

class Cbtrekap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->form_validation->set_error_delimiters('', '');
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
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            } else {
                sort($subArray);
            }
        }
    }
    public function index()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Rekap Hasil Penilaian', 'subjudul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tahuns'] = $this->cbt->getDistinctTahun();
        $data['semester'] = $this->cbt->getDistinctSmt();
        $data['ruangs'] = $this->cbt->getAllRuang();
        $data['sesis'] = $this->dropdown->getAllSesi();
        $data['kelases'] = $this->cbt->getKelas();
        $data['banks'] = $this->cbt->getAllBankSoal();
        $koreksi = $this->cbt->getTotalKoreksi();
        $data['koreksi'] = $koreksi;
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt);
            $rekapNilai = $this->cbt->getRekapJadwal();
            foreach ($data_jadwal as $rekap) {
                $terpakai = isset($jadwal_dikerjakan[$rekap->id_jadwal]) ? count($jadwal_dikerjakan[$rekap->id_jadwal]) : 0;
                $rekap->mengerjakan = $terpakai;
                $hanya_pg = $rekap->tampil_pg > 0 && $rekap->tampil_kompleks == 0 && $rekap->tampil_jodohkan == 0 && $rekap->tampil_isian == 0 && $rekap->tampil_esai == 0;
                $rekap->hanya_pg = $hanya_pg;
                if (!$hanya_pg && isset($koreksi[$rekap->id_jadwal]) && isset($koreksi[$rekap->id_jadwal][0])) {
                    $rekap->dikoreksi = false;
                } else {
                    $rekap->dikoreksi = true;
                }
            }
            $rekapJadwal = $data_jadwal;
            $rekaps = array_merge($rekapJadwal, $rekapNilai);
            $data['rekaps'] = $rekaps;
            $data['ada_rekap'] = $this->cbt->getAllRekap();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/rekap/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data_jadwal = $this->cbt->getDataJadwal($tp->id_tp, $smt->id_smt, $guru->id_guru);
            $rekapNilai = $this->cbt->getRekapJadwal($guru->id_guru);
            foreach ($data_jadwal as $rekap) {
                $terpakai = isset($jadwal_dikerjakan[$rekap->id_jadwal]) ? count($jadwal_dikerjakan[$rekap->id_jadwal]) : 0;
                $rekap->mengerjakan = $terpakai;
                $hanya_pg = $rekap->tampil_pg > 0 && $rekap->tampil_kompleks == 0 && $rekap->tampil_jodohkan == 0 && $rekap->tampil_isian == 0 && $rekap->tampil_esai == 0;
                $rekap->hanya_pg = $hanya_pg;
                if (!$hanya_pg && isset($koreksi[$rekap->id_jadwal]) && isset($koreksi[$rekap->id_jadwal][0])) {
                    $rekap->dikoreksi = false;
                } else {
                    $rekap->dikoreksi = true;
                }
            }
            $rekapJadwal = $data_jadwal;
            $rekaps = array_merge($rekapJadwal, $rekapNilai);
            $data['rekaps'] = $rekaps;
            $data['ada_rekap'] = $this->cbt->getAllRekap($guru->id_guru);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/rekap/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function perMapel()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Hasil Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tahun'] = $this->cbt->getDistinctTahun();
        $data['semester'] = $this->cbt->getDistinctSmt();
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/rekap/permapel');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/rekap/permapel');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function backupNilai($id_jadwal)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $result = false;
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $terpakai = isset($jadwal_dikerjakan[$id_jadwal]) && count($jadwal_dikerjakan[$id_jadwal]) > 0;
        $generated = $this->generateNilaiUjian($id_jadwal);
        if ($terpakai && $generated) {
            $this->db->trans_start();
            $jadwal = $this->cbt->getJadwalById($id_jadwal);
            $soals = $this->cbt->getNomorSoalByBank($jadwal->id_bank);
            $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
            $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
            $tahun = $id_tp->tahun;
            $smt = $id_smt->nama_smt;
            $kelass = unserialize($jadwal->bank_kelas ?? '');
            $arrkelas = [];
            foreach ($kelass as $kls) {
                if (!($kls['kelas_id'] != null)) {
                } else {
                    array_push($arrkelas, $kls['kelas_id']);
                }
            }
            $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $pgb = [];
            $pg2b = [];
            $jodb = [];
            $isb = [];
            $esb = [];
            foreach ($soals as $id => $soal) {
                if ($soal->jenis == '1') {
                    array_push($pgb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                } else {
                    if ($soal->jenis == '2') {
                    }
                    if ($soal->jenis == '3') {
                    }
                    if ($soal->jenis == '4') {
                    }
                    if ($soal->jenis == '5') {
                    }
                }
            }
            $soal_kompleks = ['tampil' => $jadwal->tampil_kompleks, 'bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2b];
            $soal_jodohkan = ['tampil' => $jadwal->tampil_jodohkan, 'bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jodb];
            $soal_isian = ['tampil' => $jadwal->tampil_isian, 'bobot' => $jadwal->bobot_isian, 'jawaban' => $isb];
            $soal_essai = ['tampil' => $jadwal->tampil_esai, 'bobot' => $jadwal->bobot_esai, 'jawaban' => $esb];
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->delete('cbt_rekap');
            $insert = ['id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jadwal' => $id_jadwal, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'bank_kode' => $jadwal->bank_kode, 'bank_kelas' => $jadwal->bank_kelas, 'nama_kelas' => serialize($nama_kelas), 'bank_level' => $jadwal->bank_level, 'id_mapel' => $jadwal->id_mapel, 'nama_mapel' => $jadwal->nama_mapel, 'kode' => $jadwal->kode, 'tgl_mulai' => $jadwal->tgl_mulai, 'tgl_selesai' => $jadwal->tgl_selesai, 'tampil_pg' => $jadwal->tampil_pg, 'jawaban_pg' => serialize($pgb), 'bobot_pg' => $jadwal->bobot_pg, 'soal_kompleks' => serialize($soal_kompleks), 'soal_jodohkan' => serialize($soal_jodohkan), 'soal_isian' => serialize($soal_isian), 'soal_essai' => serialize($soal_essai), 'id_guru' => $jadwal->id_guru, 'nama_guru' => $jadwal->nama_guru];
            $result = $this->db->insert('cbt_rekap', $insert);
            if (!$result) {
            }
            $this->db->set('rekap', 1);
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->update('cbt_jadwal');
            $siswas = $this->cbt->getSiswaByKelasArray($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
            $arrSiswa = [];
            foreach ($siswas as $siswa) {
                array_push($arrSiswa, $siswa->id_siswa);
            }
            $durasies = $this->cbt->getIdSiswaFromDurasiByJadwal($id_jadwal);
            $jawabans = $this->cbt->getIdSiswaFromJawabanByJadwal($id_jadwal);
            $nilais = $this->cbt->getAllNilaiSiswa($id_jadwal);
            $nilai = [];
            foreach ($siswas as $siswa) {
                $dikoreksi = [];
                $benar_pg = 0;
                $salah_pg = 0;
                $skor_pg = 0;
                $skor_pg2 = 0;
                $skor_jod = 0;
                $skor_is = 0;
                $skor_es = 0;
                if (!isset($nilais[$siswa->id_siswa])) {
                    $pgs = [];
                } else {
                    array_push($dikoreksi, $nilais[$siswa->id_siswa]->dikoreksi);
                    $benar_pg = $nilais[$siswa->id_siswa]->pg_benar;
                    $salah_pg = $jadwal->tampil_pg - $benar_pg;
                    $skor_pg = $nilais[$siswa->id_siswa]->pg_nilai;
                    $skor_pg2 = $nilais[$siswa->id_siswa]->kompleks_nilai;
                    $skor_jod = $nilais[$siswa->id_siswa]->jodohkan_nilai;
                    $skor_is = $nilais[$siswa->id_siswa]->isian_nilai;
                    $skor_es = $nilais[$siswa->id_siswa]->essai_nilai;
                    $pgs = [];
                }
                $pg2s = [];
                $jods = [];
                $iss = [];
                $ess = [];
                if (!isset($jawabans[$siswa->id_siswa])) {
                }
                foreach ($jawabans[$siswa->id_siswa] as $jawaban) {
                    if ($jawaban->jenis_soal == '1') {
                        array_push($pgs, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                    } else {
                        if ($jawaban->jenis_soal == '2') {
                        }
                        if ($jawaban->jenis_soal == '3') {
                        }
                        if ($jawaban->jenis_soal == '4') {
                        }
                        if ($jawaban->jenis_soal == '5') {
                        }
                    }
                }
                $soal_pg2 = ['bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2s, 'nilai' => $skor_pg2];
                $soal_jod = ['bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jods, 'nilai' => $skor_jod];
                $soal_is = ['bobot' => $jadwal->bobot_isian, 'jawaban' => $iss, 'nilai' => $skor_is];
                $soal_es = ['bobot' => $jadwal->bobot_esai, 'jawaban' => $ess, 'nilai' => $skor_es];
                $nilai[] = ['id_jadwal' => $id_jadwal, 'id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'id_mapel' => $jadwal->id_mapel, 'id_siswa' => $siswa->id_siswa, 'nama_siswa' => $siswa->nama, 'no_peserta' => $siswa->nomor_peserta, 'id_kelas' => $siswa->id_kelas, 'kelas' => $siswa->nama_kelas, 'mulai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->mulai : '', 'selesai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->selesai : '', 'durasi' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->lama_ujian : '', 'bobot_pg' => $jadwal->bobot_pg, 'jawaban_pg' => serialize($pgs), 'nilai_pg' => round($skor_pg, 2), 'soal_kompleks' => serialize($soal_pg2), 'soal_jodohkan' => serialize($soal_jod), 'soal_isian' => serialize($soal_is), 'soal_essai' => serialize($soal_es), 'id_guru' => $jadwal->id_guru];
            }
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->delete('cbt_rekap_nilai');
            $save = $this->master->create('cbt_rekap_nilai', $nilai, true);
            $this->db->trans_complete();
        } else {
            $result = false;
            $save = isset($jadwal_dikerjakan[$id_jadwal]) ? count($jadwal_dikerjakan[$id_jadwal]) : 0;
        }
        if ($generated && $result) {
        }
        $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
        $this->output_json(true);
    }
    public function bulkBackup()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $ids = json_decode($this->input->post('ids', true));
        sleep(1);
        $data['total'] = count($ids);
        $this->db->trans_start();
        $jadwal_dikerjakan = $this->cbt->getJadwalTerpakai();
        $jadwals = $this->cbt->getJadwalByArrId($ids);
        $data['jadwal'] = $jadwals;
        $result = false;
        $save = false;
        $generated = 0;
        foreach ($jadwals as $jadwal) {
            $terpakai = isset($jadwal_dikerjakan[$jadwal->id_jadwal]) && count($jadwal_dikerjakan[$jadwal->id_jadwal]) > 0;
            if (!$terpakai) {
            } else {
                $gen = $this->generateNilaiUjian($jadwal->id_jadwal);
                if (!$gen) {
                }
                $generated++;
                $soals = $this->cbt->getNomorSoalByBank($jadwal->id_bank);
                $id_tp = $this->dashboard->getTahunById($jadwal->id_tp);
                $id_smt = $this->dashboard->getSemesterById($jadwal->id_smt);
                $tahun = $id_tp->tahun;
                $smt = $id_smt->nama_smt;
                $kelass = unserialize($jadwal->bank_kelas ?? '');
                $arrkelas = [];
                foreach ($kelass as $kls) {
                    if (!($kls['kelas_id'] != null)) {
                    } else {
                        array_push($arrkelas, $kls['kelas_id']);
                    }
                }
                $nama_kelas = $this->dropdown->getAllKelasByArrayId($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
                $pgb = [];
                $pg2b = [];
                $jodb = [];
                $isb = [];
                $esb = [];
                foreach ($soals as $id => $soal) {
                    if ($soal->jenis == '1') {
                        array_push($pgb, ['no_soal' => $id, 'jawab' => $soal->jawaban]);
                    } else {
                        if ($soal->jenis == '2') {
                        }
                        if ($soal->jenis == '3') {
                        }
                        if ($soal->jenis == '4') {
                        }
                        if ($soal->jenis == '5') {
                        }
                    }
                }
                $soal_kompleks = ['tampil' => $jadwal->tampil_kompleks, 'bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2b];
                $soal_jodohkan = ['tampil' => $jadwal->tampil_jodohkan, 'bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jodb];
                $soal_isian = ['tampil' => $jadwal->tampil_isian, 'bobot' => $jadwal->bobot_isian, 'jawaban' => $isb];
                $soal_essai = ['tampil' => $jadwal->tampil_esai, 'bobot' => $jadwal->bobot_esai, 'jawaban' => $esb];
                $this->db->where('id_jadwal', $jadwal->id_jadwal);
                $this->db->delete('cbt_rekap');
                $insert = ['id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jadwal' => $jadwal->id_jadwal, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'bank_kode' => $jadwal->bank_kode, 'bank_kelas' => $jadwal->bank_kelas, 'nama_kelas' => serialize($nama_kelas), 'bank_level' => $jadwal->bank_level, 'id_mapel' => $jadwal->id_mapel, 'nama_mapel' => $jadwal->nama_mapel, 'kode' => $jadwal->kode, 'tgl_mulai' => $jadwal->tgl_mulai, 'tgl_selesai' => $jadwal->tgl_selesai, 'tampil_pg' => $jadwal->tampil_pg, 'jawaban_pg' => serialize($pgb), 'bobot_pg' => $jadwal->bobot_pg, 'soal_kompleks' => serialize($soal_kompleks), 'soal_jodohkan' => serialize($soal_jodohkan), 'soal_isian' => serialize($soal_isian), 'soal_essai' => serialize($soal_essai), 'id_guru' => $jadwal->id_guru, 'nama_guru' => $jadwal->nama_guru];
                $result = $this->db->insert('cbt_rekap', $insert);
                if (!$result) {
                }
                $this->db->set('rekap', 1);
                $this->db->where('id_jadwal', $jadwal->id_jadwal);
                $this->db->update('cbt_jadwal');
                $siswas = $this->cbt->getSiswaByKelasArray($id_tp->id_tp, $id_smt->id_smt, $arrkelas);
                $arrSiswa = [];
                foreach ($siswas as $siswa) {
                    array_push($arrSiswa, $siswa->id_siswa);
                }
                $durasies = $this->cbt->getIdSiswaFromDurasiByJadwal($jadwal->id_jadwal);
                $jawabans = $this->cbt->getIdSiswaFromJawabanByJadwal($jadwal->id_jadwal);
                $nilais = $this->cbt->getAllNilaiSiswa($jadwal->id_jadwal);
                $nilai = [];
                foreach ($siswas as $siswa) {
                    $dikoreksi = [];
                    $benar_pg = 0;
                    $salah_pg = 0;
                    $skor_pg = 0;
                    $skor_pg2 = 0;
                    $skor_jod = 0;
                    $skor_is = 0;
                    $skor_es = 0;
                    if (!isset($nilais[$siswa->id_siswa])) {
                        $pgs = [];
                    } else {
                        array_push($dikoreksi, $nilais[$siswa->id_siswa]->dikoreksi);
                        $benar_pg = $nilais[$siswa->id_siswa]->pg_benar;
                        $salah_pg = $jadwal->tampil_pg - $benar_pg;
                        $skor_pg = $nilais[$siswa->id_siswa]->pg_nilai;
                        $skor_pg2 = $nilais[$siswa->id_siswa]->kompleks_nilai;
                        $skor_jod = $nilais[$siswa->id_siswa]->jodohkan_nilai;
                        $skor_is = $nilais[$siswa->id_siswa]->isian_nilai;
                        $skor_es = $nilais[$siswa->id_siswa]->essai_nilai;
                        $pgs = [];
                    }
                    $pg2s = [];
                    $jods = [];
                    $iss = [];
                    $ess = [];
                    if (!isset($jawabans[$siswa->id_siswa])) {
                    }
                    foreach ($jawabans[$siswa->id_siswa] as $jawaban) {
                        if ($jawaban->jenis_soal == '1') {
                            array_push($pgs, ['no_soal' => $jawaban->id_soal, 'jawab' => $jawaban->jawaban_siswa]);
                        } else {
                            if ($jawaban->jenis_soal == '2') {
                            }
                            if ($jawaban->jenis_soal == '3') {
                            }
                            if ($jawaban->jenis_soal == '4') {
                            }
                            if ($jawaban->jenis_soal == '5') {
                            }
                        }
                    }
                    $soal_pg2 = ['bobot' => $jadwal->bobot_kompleks, 'jawaban' => $pg2s, 'nilai' => $skor_pg2];
                    $soal_jod = ['bobot' => $jadwal->bobot_jodohkan, 'jawaban' => $jods, 'nilai' => $skor_jod];
                    $soal_is = ['bobot' => $jadwal->bobot_isian, 'jawaban' => $iss, 'nilai' => $skor_is];
                    $soal_es = ['bobot' => $jadwal->bobot_esai, 'jawaban' => $ess, 'nilai' => $skor_es];
                    $nilai[] = ['id_jadwal' => $jadwal->id_jadwal, 'id_tp' => $id_tp->id_tp, 'tp' => $tahun, 'id_smt' => $id_smt->id_smt, 'smt' => $smt, 'id_jenis' => $jadwal->id_jenis, 'kode_jenis' => $jadwal->kode_jenis, 'id_bank' => $jadwal->id_bank, 'id_mapel' => $jadwal->id_mapel, 'id_siswa' => $siswa->id_siswa, 'nama_siswa' => $siswa->nama, 'no_peserta' => $siswa->nomor_peserta, 'id_kelas' => $siswa->id_kelas, 'kelas' => $siswa->nama_kelas, 'mulai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->mulai : '', 'selesai' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->selesai : '', 'durasi' => isset($durasies[$siswa->id_siswa]) ? $durasies[$siswa->id_siswa]->lama_ujian : '', 'bobot_pg' => $jadwal->bobot_pg, 'jawaban_pg' => serialize($pgs), 'nilai_pg' => round($skor_pg, 2), 'soal_kompleks' => serialize($soal_pg2), 'soal_jodohkan' => serialize($soal_jod), 'soal_isian' => serialize($soal_is), 'soal_essai' => serialize($soal_es), 'id_guru' => $jadwal->id_guru];
                }
                $this->db->where('id_jadwal', $jadwal->id_jadwal);
                $this->db->delete('cbt_rekap_nilai');
                $save = $this->master->create('cbt_rekap_nilai', $nilai, true);
            }
        }
        $this->db->trans_complete();
        $sukses = $generated > 0 && $result;
        if ($generated > 0 && $result) {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert"> Berhasil merekap <b>' . count($ids) . '</b> nilai </div>');
        } else {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert">Jadwal Ujian masih berlangsung, ' . $save . ' nilai siswa berhasil direkap.<br>Beberapa siswa belum selesai atau belum dikoreksi</div>');
        }
        $this->output_json(true);
    }
    public function hapusRekap()
    {
        $ids = json_decode($this->input->post('ids', true));
        sleep(1);
        $data['total'] = count($ids);
        $this->db->where_in('id_jadwal', $ids);
        $delRekap = $this->db->delete('cbt_rekap');
        $this->db->where_in('id_jadwal', $ids);
        $delNilai = $this->db->delete('cbt_rekap_nilai');
        if ($delNilai && $delRekap) {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-success align-content-center w-100" role="alert"> Berhasil menghapus <b>' . count($ids) . '</b> nilai </div>');
        } else {
            $this->session->set_flashdata('rekapnilai', '<div id="flashdata" class="alert alert-default-danger align-content-center w-100" role="alert"> Hapus nilai gagal </div>');
        }
        $data['success'] = $delNilai && $delRekap;
        $this->output_json($data);
    }
    function getDataFromArray1ByUserId($array, $userId)
    {
        foreach ($array as $key => $data) {
            if (!($data->id_siswa == $userId)) {
            } else {
                return $array;
            }
        }
        return array();
    }
    public function getJenisPenilaian()
    {
        $this->load->model('Cbt_model', 'cbt');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $jadwals = $this->cbt->getJenisRekap($tahun, $smt);
    }
    public function getNilaiKelas()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $kelas = $this->input->get('kelas');
        $level = $this->master->getKelasById($kelas);
        $jenis = $this->input->get('jenis');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $mapel = $this->input->get('mapel');
        $user = $this->ion_auth->user()->row();
        if ($this->ion_auth->is_admin()) {
            $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel);
            foreach ($jadwals as $key => $jadwal) {
                $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
                $jadwal->jawaban_pg = unserialize($jadwal->jawaban_pg ?? '');
                $jadwal->jawaban_esai = unserialize($jadwal->jawaban_esai ?? '');
                $ids = [];
                foreach ($jadwal->bank_kelas as $id) {
                    array_push($ids, $id['kelas_id']);
                }
                if (in_array($kelas, $ids)) {
                } else {
                    unset($jadwals[$key]);
                }
            }
            $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel);
        } else {
            $tpg = $this->dashboard->getTahunByTahun($tahun);
            $smtg = $this->dashboard->getSemesterByNama($smt);
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tpg->id_tp, $smtg->id_smt);
            $jadwals = $this->cbt->getAllRekapByJadwal($tahun, $smt, $jenis, $level->level_id, $mapel, $guru->id_guru);
            foreach ($jadwals as $key => $jadwal) {
                $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
                $jadwal->jawaban_pg = unserialize($jadwal->jawaban_pg);
                $jadwal->jawaban_esai = unserialize($jadwal->jawaban_esai);
                $ids = [];
                foreach ($jadwal->bank_kelas as $id) {
                    array_push($ids, $id['kelas_id']);
                }
                if (in_array($kelas, $ids)) {
                } else {
                    unset($jadwals[$key]);
                }
            }
            $rekaps = $this->cbt->getAllNilaiRekapByJadwal($tahun, $smt, $jenis, $kelas, $mapel, $guru->id_guru);
        }
        $arrSiswa = [];
        if (!(count($rekaps) > 0)) {
        }
        foreach ($rekaps as $rekap) {
            $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
            $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
            $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
            $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
            $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
            $arrSiswa[$rekap->id_siswa] = ['id_siswa' => $rekap->id_siswa, 'nomor_peserta' => $rekap->nomor_peserta, 'nama' => $rekap->nama];
        }
        usort($arrSiswa, function ($a, $b) {
            return $a['nama'] <=> $b['nama'];
        });
        $arrNilai = [];
        foreach ($rekaps as $key => $item) {
            $arrNilai[$item->id_siswa][$item->id_jadwal] = $item;
        }
        usort($jadwals, function ($a, $b) {
            return $a->id_jadwal <=> $b->id_jadwal;
        });
        $data['siswa'] = $arrSiswa;
        $data['nilai'] = $arrNilai;
        $data['info'] = array_values($jadwals);
        $this->output_json($data);
    }
    public function olahNilai()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $kelas = $this->input->get('kelas');
        $level = $this->master->getKelasById($kelas);
        $jadwal = $this->input->get('jadwal');
        $user = $this->ion_auth->user()->row();
        $rekap = $this->cbt->getRekapByJadwalKelas($jadwal);
        $data = ['user' => $user, 'judul' => 'Ekspor Hasil Siswa', 'subjudul' => 'Ekspor Hasil Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas_selected'] = $kelas;
        $data['jadwal_selected'] = $jadwal;
        $kls = @unserialize($rekap->nama_kelas);
        $data['kelas'] = $kls;
        if (!($rekap != null)) {
            if ($this->ion_auth->is_admin()) {
            }
        } else {
            $rekap->jawaban_pg = $this->unserialize_with_key($rekap->jawaban_pg);
            $rekap->soal_kompleks = json_decode(json_encode(unserialize($rekap->soal_kompleks)));
            $rekap->soal_jodohkan = json_decode(json_encode(unserialize($rekap->soal_jodohkan)));
            $rekap->soal_isian = json_decode(json_encode(unserialize($rekap->soal_isian)));
            $rekap->soal_essai = json_decode(json_encode(unserialize($rekap->soal_essai)));
            $data['rekap'] = $rekap;
            $data['mapel'] = $rekap->id_mapel;
            $data['nama_kelas'] = $kelas == null ? 'Silahkan pilih kelas' : $kls[$kelas];
            if ($this->ion_auth->is_admin()) {
            }
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        if (!($kelas != null)) {
        }
        $siswas = $this->cbt->getAllNilaiRekapByJenis($rekap->tp, $rekap->smt, $rekap->kode_jenis, $kelas, '0', $jadwal, $guru->id_guru);
        foreach ($siswas as $siswa) {
            $siswa->jawaban_pg = $this->unserialize_with_key($siswa->jawaban_pg);
            $siswa->soal_kompleks = json_decode(json_encode(unserialize($siswa->soal_kompleks)));
            $siswa->soal_jodohkan = json_decode(json_encode(unserialize($siswa->soal_jodohkan)));
            $siswa->soal_isian = json_decode(json_encode(unserialize($siswa->soal_isian)));
            $siswa->soal_essai = json_decode(json_encode(unserialize($siswa->soal_essai)));
        }
        $data['siswas'] = $siswas;
        $ya = $this->input->get('ya');
        $yb = $this->input->get('yb');
        $xa = $this->input->get('xa');
        $xb = $this->input->get('xb');
        if (!($ya != null)) {
        }
        $convert = ['ya' => $ya, 'yb' => $yb, 'xa' => $xa, 'xb' => $xb];
        $data['convert'] = $convert;
        $data['jadwal'] = $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/rekap/olah');
        $this->load->view('members/guru/templates/footer');
    }
    function unserialize_with_key($serialized)
    {
        $arr = unserialize($serialized);
        $result = [];
        foreach ($arr as $value) {
            $result[$value['no_soal']] = $value['jawab'];
        }
        return $result;
    }
    public function export()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Ekspor Hasil Penilaian', 'subjudul' => 'Ekspor Nilai', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jenis'] = $this->cbt->getDistinctJenisUjian();
        $data['kelas'] = $this->cbt->getDistinctKelas();
        $data['tahuns'] = $this->cbt->getDistinctTahun();
        $data['semester'] = $this->cbt->getDistinctSmt();
        if ($this->ion_auth->is_admin()) {
            $jadwals = $this->cbt->getAllRekap();
            foreach ($jadwals as $jadwal) {
                $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
                $jadwal->nama_kelas = unserialize($jadwal->nama_kelas);
            }
            $data['rekaps'] = $jadwals;
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/rekap/ekspor');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $jadwals = $this->cbt->getAllRekap($guru->id_guru);
            foreach ($jadwals as $key => $jadwal) {
                $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
                $jadwal->nama_kelas = unserialize($jadwal->nama_kelas ?? '');
            }
            $data['rekaps'] = $jadwals;
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/rekap/ekspor');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function generateNilaiUjian($jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $info = $this->cbt->getJadwalById($jadwal);
        $bagi_pg = $info->tampil_pg / 100;
        $bobot_pg = $info->bobot_pg / 100;
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian = $info->tampil_isian / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_essai = $info->bobot_esai / 100;
        $kelas_bank = unserialize($info->bank_kelas ?? '');
        $kelases = [];
        foreach ($kelas_bank as $key => $value) {
            array_push($kelases, $value['kelas_id']);
        }
        $siswas = $this->cbt->getSiswaByKelas($info->id_tp, $info->id_smt, $kelases);
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank);
        $soal = [];
        $jawabans_siswa = [];
        foreach ($jawabans as $jawaban_siswa) {
            if (!($jawaban_siswa->jawaban_siswa != null)) {
            } else {
                if (!($jawaban_siswa->jenis_soal == '2')) {
                }
                $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                $jawaban_siswa->jawaban_benar = array_map('strtoupper', $jawaban_siswa->jawaban_benar ?? ['']);
                $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar ?? [''], 'strlen');
                $jawaban_siswa->jawaban = array_map('strtoupper', $jawaban_siswa->jawaban ?? ['']);
                $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban ?? [''], 'strlen');
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
                $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
                $arrAlphabet = range('A', 'Z');
                if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
                }
                $arrjwbnSiswa = [];
                if (!$jawaban_siswa->jawaban_siswa) {
                }
                foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    } else {
                        $arrjwbnSiswa[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if (!($idxs > 0)) {
                            } else {
                                if (!($jb === '1')) {
                                }
                                $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                            }
                        }
                    }
                }
                if ($jawaban_siswa->jawaban_siswa) {
                }
                $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $arrjwbn = [];
                foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    } else {
                        $arrjwbn[$idx] = [];
                        foreach ($jbs as $idxs => $jb) {
                            if (!($idxs > 0)) {
                            } else {
                                if (!($jb === '1')) {
                                }
                                $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                            }
                        }
                    }
                }
                $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
                $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
                $soal[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
            }
        }
        $insets = [];
        foreach ($siswas as $siswa) {
            $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
            $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
            $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
            $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
            $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
            $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
            $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal, $siswa->id_siswa);
            if (!($nilai_input != null && $nilai_input->dikoreksi == '1')) {
            } else {
                $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
                $benar_pg = 0;
                $salah_pg = 0;
                if (!($info->tampil_pg > 0)) {
                }
                if (!(count($jawaban_pg) > 0)) {
                }
                foreach ($jawaban_pg as $jwb_pg) {
                    if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                    } else {
                        if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                        }
                        $salah_pg += 1;
                    }
                }
                $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
                $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
                $benar_pg2 = 0;
                $skor_koreksi_pg2 = 0.0;
                $otomatis_pg2 = 0;
                if (!($info->tampil_kompleks > 0)) {
                }
                if (!(count($jawaban_pg2) > 0)) {
                }
                foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                    $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                    $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                    $arr_benar = [];
                    foreach ($jawab_pg2->jawaban_siswa as $js) {
                        if (!in_array($js, $jawab_pg2->jawaban)) {
                        } else {
                            array_push($arr_benar, true);
                        }
                    }
                    $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
                }
                $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
                $input_pg2 = 0;
                if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
                }
                $input_pg2 = $nilai_input->kompleks_nilai;
                $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
                $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
                $benar_jod = 0;
                $skor_koreksi_jod = 0.0;
                $otomatis_jod = 0;
                if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
                }
                foreach ($jawaban_jodoh as $num => $jawab_jod) {
                    $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                    $item_benar = 0;
                    $item_salah = 0;
                    $item_kurang = 0;
                    $items = 0;
                    $arrBenar = [];
                    $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                    if (!isset($jawab_jod->jawaban_siswa->links)) {
                        $point_soal = 1 / $items * $item_benar * $point_benar;
                    } else {
                        $array1 = (array) $jawab_jod->jawaban_benar->links;
                        $this->sortArrays($array1);
                        $array2 = (array) $jawab_jod->jawaban_siswa->links;
                        $this->sortArrays($array2);
                        $sameCount = 0;
                        $differentCount = 0;
                        foreach ($array1 as $key => $subArray1) {
                            $arrBenar[$key] = new stdClass();
                            $arrBenar[$key]->benar = 0;
                            $arrBenar[$key]->salah = 0;
                            $arrBenar[$key]->kurang = 0;
                            $items += count($subArray1);
                            if (isset($array2[$key])) {
                                $subArray2 = $array2[$key];
                                $sameItems = array_intersect($subArray1, $subArray2);
                                $item_benar += count($sameItems);
                                $arrBenar[$key]->benar += count($sameItems);
                                $diffItems1 = array_diff($subArray1, $subArray2);
                                $diffItems2 = array_diff($subArray2, $subArray1);
                                $arrBenar[$key]->kurang += count($diffItems1);
                            } else {
                                $arrBenar[$key]->kurang += count($subArray1);
                            }
                        }
                        $point_soal = 1 / $items * $item_benar * $point_benar;
                    }
                    $benar_jod += 1 / $items * $item_benar;
                    $otomatis_jod = $jawab_jod->nilai_otomatis;
                }
                $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
                $input_jod = 0;
                if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
                }
                $input_jod = $nilai_input->jodohkan_nilai;
                $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
                $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
                $benar_is = 0;
                $skor_koreksi_is = 0.0;
                $otomatis_is = 0;
                if (!($info->tampil_isian > 0)) {
                }
                if (!(count($jawaban_is) > 0)) {
                }
                foreach ($jawaban_is as $num => $jawab_is) {
                    $skor_koreksi_is += $jawab_is->nilai_koreksi;
                    $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
                    if (!$benar) {
                        $otomatis_is = $jawab_is->nilai_otomatis;
                    } else {
                        $benar_is++;
                        $otomatis_is = $jawab_is->nilai_otomatis;
                    }
                }
                $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
                $input_is = 0;
                if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
                }
                $input_is = $nilai_input->isian_nilai;
                $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
                $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
                $benar_es = 0;
                $skor_koreksi_es = 0.0;
                $otomatis_es = 0;
                if (!($info->tampil_esai > 0)) {
                }
                if (!(count($jawaban_es) > 0)) {
                }
                foreach ($jawaban_es as $num => $jawab_es) {
                    $skor_koreksi_es += $jawab_es->nilai_koreksi;
                    $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
                    if (!$benar) {
                        $otomatis_es = $jawab_es->nilai_otomatis;
                    } else {
                        $benar_es++;
                        $otomatis_es = $jawab_es->nilai_otomatis;
                    }
                }
                $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
                $input_es = 0;
                if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
                }
                $input_es = $nilai_input->essai_nilai;
                $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
                $insert['id_nilai'] = $siswa->id_siswa . '0' . $jadwal;
                $insert['id_siswa'] = $siswa->id_siswa;
                $insert['id_jadwal'] = $jadwal;
                $insert['pg_benar'] = $benar_pg;
                $insert['pg_nilai'] = round($skor_pg, 2);
                $insert['kompleks_nilai'] = round($skor_pg2, 2);
                $insert['jodohkan_nilai'] = round($skor_jod, 2);
                $insert['isian_nilai'] = round($skor_is, 2);
                $insert['essai_nilai'] = round($skor_es, 2);
                array_push($insets, $insert);
            }
        }
        if (count($insets) > 0) {
            $this->db->update_batch('cbt_nilai', $insets, 'id_nilai');
            $update = true;
            return $update;
        } else {
            $update = false;
            return $update;
        }
    }
}
```

---

## File: application/controllers_progress/Cbtruang.php

```php
<?php

class Cbtruang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Ruang Ujian', 'subjudul' => 'Data Ruang Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/ruang/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->output_json($this->cbt->getRuang(), false);
    }
    public function add()
    {
        $insert = ['nama_ruang' => $this->input->post('nama_ruang', true), 'kode_ruang' => $this->input->post('kode_ruang', true)];
        $this->master->create('cbt_ruang', $insert, false);
        $data['status'] = $insert;
        $this->output_json($data);
    }
    public function update()
    {
        $data = $this->cbt->updateRuang();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('cbt_ruang', $chk, 'id_ruang')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
}
```

---

## File: application/controllers_progress/Cbtsesi.php

```php
<?php

class Cbtsesi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Sesi Ujian', 'subjudul' => 'Data Sesi Ujian', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesi/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->output_json($this->cbt->getSesi(), false);
    }
    public function add()
    {
        $insert = ['nama_sesi' => $this->input->post('nama_sesi', true), 'kode_sesi' => $this->input->post('kode_sesi', true), 'waktu_mulai' => $this->input->post('waktu_mulai', true), 'waktu_akhir' => $this->input->post('waktu_akhir', true)];
        $this->master->create('cbt_sesi', $insert, false);
        $data['status'] = $insert;
        $this->output_json($data);
    }
    public function update()
    {
        $data = $this->cbt->updateSesi();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function edit($id)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Sesi Siswa', 'subjudul' => 'Atur Sesi Siswa', 'sesi' => $this->cbt->getSesiById($id)];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesi/edit');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('cbt_sesi', $chk, 'id_sesi')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function sesisiswa()
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Sesi Ujian', 'subjudul' => 'Data Sesi Ujian'];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesisiswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
}
```

---

## File: application/controllers_progress/Cbtsesisiswa.php

```php
<?php

class Cbtsesisiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Atur Ruang dan Sesi Siswa', 'subjudul' => 'Ruang dan Sesi Siswa', 'setting' => $this->dashboard->getSetting(), 'kelas' => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt), 'ruang_kelas' => $this->cbt->getKelasList($tp->id_tp, $smt->id_smt), 'sesi' => $this->dropdown->getAllSesi(), 'ruang' => $this->cbt->getAllRuang(), 'tp' => $this->dashboard->getTahun(), 'tp_active' => $tp, 'smt' => $this->dashboard->getSemester(), 'smt_active' => $smt, 'profile' => $this->dashboard->getProfileAdmin($user->id)];
        $kls = $this->input->get('kls', true);
        $kelas_selected = $kls != null ? $kls : '0';
        $siswas = [];
        if (!($kelas_selected != '0')) {
            $data['siswas'] = $siswas;
        } else {
            $siswas = $this->cbt->getRuangSesiSiswa($kls, $tp->id_tp, $smt->id_smt);
            $data['siswas'] = $siswas;
        }
        $data['kelas_selected'] = $kelas_selected;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/sesisiswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function getAllRuang()
    {
        $this->output_json($this->cbt->getAllRuang());
    }
    public function getAllSesi()
    {
        $this->output_json($this->dropdown->getAllSesi());
    }
    public function add()
    {
        $insert = ['nama_sesi' => $this->input->post('nama_sesi', true), 'kode_sesi' => $this->input->post('kode_sesi', true), 'waktu_mulai' => $this->input->post('waktu_mulai', true), 'waktu_akhir' => $this->input->post('waktu_akhir', true)];
        $this->master->create('cbt_sesi', $insert, false);
        $data['status'] = $insert;
        $this->output_json($data);
    }
    public function update()
    {
        $data = $this->cbt->updateSesi();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('cbt_sesi', $chk, 'id_sesi')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function editsesisiswa()
    {
        $rs = $this->input->post('ruang-sesi', true);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $update = false;
        foreach ($rs as $id => $klss) {
            foreach ($klss as $idkls => $kls) {
                $data = ['siswa_id' => $id, 'kelas_id' => $idkls, 'ruang_id' => $kls['ruang'], 'sesi_id' => $kls['sesi'], 'tp_id' => $tp->id_tp, 'smt_id' => $smt->id_smt];
                $update = $this->db->replace('cbt_sesi_siswa', $data);
            }
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function editsesikelas()
    {
        $input = json_decode($this->input->post('kelas_sesi', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($input as $d) {
            $siswas = $this->kelas->getKelasSiswa($d->kelas_id, $tp->id_tp, $smt->id_smt);
            foreach ($siswas as $siswa) {
                $data = ['siswa_id' => $siswa->id_siswa, 'kelas_id' => $siswa->id_kelas, 'ruang_id' => $d->ruang_id, 'sesi_id' => $d->sesi_id, 'tp_id' => $tp->id_tp, 'smt_id' => $smt->id_smt];
                $this->db->replace('cbt_sesi_siswa', $data);
            }
            $data = ['id_kelas_ruang' => $d->kelas_id . $tp->id_tp . $smt->id_smt, 'id_kelas' => $d->kelas_id, 'id_ruang' => $d->ruang_id, 'id_sesi' => $d->sesi_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'set_siswa' => $d->set_siswa];
            $update = $this->db->replace('cbt_kelas_ruang', $data);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Cbtstatus.php

```php
<?php

class Cbtstatus extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Status Ujian Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['jadwal'] = $this->dropdown->getAllJadwal($tp->id_tp, $smt->id_smt);
            $data['ruang'] = $this->dropdown->getAllRuang();
            $data['sesi'] = $this->dropdown->getAllSesi();
            $jadwals = $this->cbt->getJadwalKelas($tp->id_tp, $smt->id_smt);
            $arrKls = [];
            foreach ($jadwals as $jad) {
                $kls = unserialize($jad->bank_kelas ?? '');
                foreach ($kls as $kl) {
                    array_push($arrKls, $kl['kelas_id']);
                }
            }
            $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/status/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $data['jadwal'] = $this->dropdown->getAllJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
            $data['ruang'] = $this->dropdown->getAllRuang();
            $data['sesi'] = $this->dropdown->getAllSesi();
            $data['pengawas'] = $this->cbt->getPengawasByGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
            $jadwals = $this->cbt->getJadwalGuru($tp->id_tp, $smt->id_smt, $guru->id_guru);
            $arrKls = [];
            foreach ($jadwals as $jad) {
                $kls = unserialize($jad->bank_kelas ?? '');
                foreach ($kls as $kl) {
                    array_push($arrKls, $kl['kelas_id']);
                }
            }
            $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, $arrKls);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/cbt/status/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function status_ruang()
    {
        $ruang = $this->input->get('ruang');
        $sesi = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Status Ujian Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $this->db->trans_start();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $info = $this->cbt->getJadwalById($jadwal);
        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $sesi, $info->bank_level);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi, $ruang);
        $ids_pengawas = [];
        if (!($pengawas && count($pengawas) > 0)) {
            $arrDur = [];
        } else {
            foreach ($pengawas as $pws) {
                $ids_pengawas = explode(',', $pws->id_guru ?? '');
            }
            $arrDur = [];
        }
        foreach ($siswas as $siswa) {
            $dur_siswa = null;
            foreach ($durasies as $durasi) {
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                } else {
                    if ($durasi->lama_ujian == null) {
                    }
                    $lamanya = $durasi->lama_ujian;
                    if (strpos($lamanya, ':') !== false) {
                    }
                    $durasi->lama_ujian .= 'm';
                    $dur_siswa = $durasi;
                }
            }
            $log_siswa = [];
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                } else {
                    array_push($log_siswa, $log);
                }
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $this->db->trans_complete();
        $data['siswa'] = $siswas;
        $data['durasi_siswa'] = $arrDur;
        $data['info'] = $info;
        $data['ids_pengawas'] = $ids_pengawas;
        $guru_ngawas = [];
        if (!($ids_pengawas && count($ids_pengawas) > 0)) {
        }
        $guru_ngawas = $this->master->getGuruByArrId($ids_pengawas);
        $data['pengawas'] = $guru_ngawas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/cbt/status/status');
        $this->load->view('members/guru/templates/footer');
    }
    public function getJadwalUjianByJadwal()
    {
        $jadwal = $this->input->get('id_jadwal');
        $info = $this->cbt->getJadwalById($jadwal);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $kelas = unserialize($info->bank_kelas ?? '');
        $kelases = [];
        foreach ($kelas as $key => $value) {
            $kelases[$value['kelas_id']] = $this->dropdown->getNamaKelasById($info->id_tp, $info->id_smt, $value['kelas_id']);
        }
        $this->output_json($kelases);
    }
    public function getJadwalUjianByKelas()
    {
        $kelas = $this->input->get('id_kelas');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        if ($this->ion_auth->in_group('guru')) {
            $user = $this->ion_auth->user()->row();
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $id_guru = $guru->id_guru;
        } else {
            $id_guru = null;
        }
        $jadwals = $this->cbt->getAllJadwal($tp->id_tp, $smt->id_smt, $id_guru);
        $jdwl = [];
        foreach ($jadwals as $jadwal) {
            $kls = unserialize($jadwal->bank_kelas ?? '');
            foreach ($kls as $kl) {
                if (!($kl['kelas_id'] == $kelas)) {
                } else {
                    $jdwl[$jadwal->id_jadwal] = $jadwal->bank_kode;
                }
            }
        }
        $this->output_json($jdwl);
    }
    public function getSiswaKelas()
    {
        $kelas = $this->input->get('kelas');
        $jadwal = $this->input->get('jadwal');
        $this->db->trans_start();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $info = $this->cbt->getJadwalById($jadwal);
        $siswas = $this->cbt->getSiswaByKelas($tp->id_tp, $smt->id_smt, $kelas);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal);
        $ids_pengawas = [];
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $arrDur = [];
        foreach ($siswas as $siswa) {
            $dur_siswa = null;
            foreach ($durasies as $durasi) {
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                } else {
                    $mulai = new DateTime($durasi->mulai);
                    $interval = $mulai->diff(new DateTime());
                    $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                    $durasi->ada_waktu = $minutes < $info->durasi_ujian;
                    if ($durasi->lama_ujian == null) {
                    }
                    $lamanya = $durasi->lama_ujian;
                    if (strpos($lamanya, ':') !== false) {
                    }
                    $durasi->lama_ujian .= 'm';
                    $dur_siswa = $durasi;
                }
            }
            $log_siswa = [];
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                } else {
                    array_push($log_siswa, $log);
                }
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $this->db->trans_complete();
        $data['siswa'] = $siswas;
        $data['durasi'] = $arrDur;
        $data['info'] = $info;
        $data['pengawas'] = $this->master->getGuruByArrId($ids_pengawas);
        $this->output_json($data);
    }
    public function getSiswaRuang()
    {
        $ruang = $this->input->get('ruang');
        $sesi = $this->input->get('sesi');
        $jadwal = $this->input->get('jadwal');
        $this->db->trans_start();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $info = $this->cbt->getJadwalById($jadwal);
        $siswas = $this->cbt->getSiswaByRuang($tp->id_tp, $smt->id_smt, $ruang, $sesi, $info->bank_level);
        $durasies = $this->cbt->getDurasiSiswaByJadwal($jadwal);
        $logs = $this->cbt->getLogUjianByJadwal($jadwal);
        $pengawas = $this->cbt->getPengawasByJadwal($tp->id_tp, $smt->id_smt, $jadwal, $sesi, $ruang);
        $ids_pengawas = [];
        foreach ($pengawas as $pws) {
            $ids_pengawas = explode(',', $pws->id_guru ?? '');
        }
        $arrDur = [];
        foreach ($siswas as $siswa) {
            $dur_siswa = null;
            foreach ($durasies as $durasi) {
                if (!($durasi->id_siswa == $siswa->id_siswa)) {
                } else {
                    $mulai = new DateTime($durasi->mulai);
                    $interval = $mulai->diff(new DateTime());
                    $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
                    $durasi->ada_waktu = $minutes < $info->durasi_ujian;
                    if ($durasi->lama_ujian == null) {
                    }
                    $lamanya = $durasi->lama_ujian;
                    if (strpos($lamanya, ':') !== false) {
                    }
                    $durasi->lama_ujian .= 'm';
                    $dur_siswa = $durasi;
                }
            }
            $log_siswa = [];
            foreach ($logs as $log) {
                if (!($log->id_siswa == $siswa->id_siswa)) {
                } else {
                    array_push($log_siswa, $log);
                }
            }
            $arrDur[$siswa->id_siswa] = ['dur' => $dur_siswa, 'log' => $log_siswa];
        }
        $this->db->trans_complete();
        $data['siswa'] = $siswas;
        $data['durasi'] = $arrDur;
        $data['info'] = $info;
        $data['pengawas'] = $this->master->getGuruByArrId($ids_pengawas);
        $this->output_json($data);
    }
    public function detail()
    {
        $siswa = $this->input->get('siswa');
        $jadwal = $this->input->get('jadwal');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Detail Status Siswa', 'subjudul' => 'Status Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['siswa'] = $this->master->getSiswaById($siswa);
        $data['soal'] = $this->cbt->getSoalSiswaByJadwal($jadwal, $siswa);
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/status/detail');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/status/detail');
            $this->load->view('members/guru/templates/footer');
        }
    }
}
```

---

## File: application/controllers_progress/Cbttoken.php

```php
<?php

class Cbttoken extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Token Ujian', 'subjudul' => 'Token', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $token = $this->cbt->getToken();
        $tkn['token'] = '';
        $tkn['auto'] = '0';
        $tkn['jarak'] = '1';
        $tkn['elapsed'] = '00:00:00';
        $data['token'] = $token != null ? $token : json_decode(json_encode($tkn));
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/token/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/cbt/token/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function generateToken()
    {
        $post_token = json_decode($this->input->get('data'));
        $force = $this->input->get('force');
        $token = $this->cbt->getToken();
        $updated = date('Y-m-d H:i:s');
        if ($force == '1') {
            $new = $this->createNewToken();
        } else {
            $mulai = new DateTime($token->updated);
            $diff = $mulai->diff(new DateTime());
            $total_minutes = $diff->days * 24 * 60;
            $total_minutes += $diff->h * 60;
            $total_minutes += $diff->i;
            if (!($total_minutes >= $post_token->jarak)) {
            }
            $new = $this->createNewToken();
        }
        $post_token->token = $new;
        $post_token->updated = $updated;
        $this->cbt->saveToken($post_token);
        $token = $this->cbt->getToken();
        $token->now = $updated;
        $this->output_json($token);
    }
    public function loadToken()
    {
        $dataflds = $this->db->field_data('cbt_token');
        $table_changed = false;
        foreach ($dataflds as $fild) {
            if (!($fild->name == 'updated')) {
            } else {
                if (!($fild->type != 'varchar')) {
                }
                $field = ['updated' => array('type' => 'VARCHAR', 'constraint' => 20, 'default' => '')];
                $table_changed = $this->dbforge->modify_column('cbt_token', $field);
            }
        }
        $token = $this->cbt->getToken();
        if ($token == null) {
            $data['token'] = '';
            $data['auto'] = '0';
            $data['elapsed'] = '00:00:00';
            $this->output_json($data);
        } else {
            $token->now = date('Y-m-d H:i:s');
            $this->output_json($token);
        }
    }
    private function createNewToken()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($chars);
        $new_token = '';
        $i = 0;
        if (!($i < 6)) {
            return $new_token;
        } else {
            $random_character = $chars[mt_rand(0, $input_length - 1)];
            $new_token .= $random_character;
            $i++;
            if (!($i < 6)) {
            }
        }
    }
}
```

---

## File: application/controllers_progress/Compare.php

```php
<?php

goto bFjy2;
bX7zZ:
f8iLo:
goto vgjTG;
JIrUm:
exit('No direct script access allowed');
goto bX7zZ;
bFjy2:
if (defined('BASEPATH')) {
    goto f8iLo;
}
goto JIrUm;
vgjTG:
class Compare extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->CHARACTER_SET = 'utf8 COLLATE utf8_general_ci';
        $this->DB1 = $this->load->database('main_garuda', TRUE);
        $this->DB2 = $this->load->database('live', TRUE);
    }
    function index()
    {
        $sql_commands_to_run = array();
        $development_tables = $this->DB1->list_tables();
        $live_tables = $this->DB2->list_tables();
        $tables_to_create = array_diff($development_tables, $live_tables);
        $tables_to_drop = array_diff($live_tables, $development_tables);
        $sql_commands_to_run = is_array($tables_to_create) && !empty($tables_to_create) ? array_merge($sql_commands_to_run, $this->manage_tables($tables_to_create, 'create')) : array();
        $sql_commands_to_run = is_array($tables_to_drop) && !empty($tables_to_drop) ? array_merge($sql_commands_to_run, $this->manage_tables($tables_to_drop, 'drop')) : array();
        $tables_to_update = $this->compare_table_structures($development_tables, $live_tables);
        $tables_to_update = array_diff($tables_to_update, $tables_to_create);
        $sql_commands_to_run = is_array($tables_to_update) && !empty($tables_to_update) ? array_merge($sql_commands_to_run, $this->update_existing_tables($tables_to_update)) : '';
        if (is_array($sql_commands_to_run) && !empty($sql_commands_to_run)) {
            echo '<h2>The database is out of Sync!</h2>
';
            echo '<p>The following SQL commands need to be executed to bring the Live database tables up to date: </p>
';
            echo '<pre style=\'padding: 20px; background-color: #FFFAF0;\'>
';
            foreach ($sql_commands_to_run as $sql_command) {
                echo "{$sql_command}\n";
            }
            echo '<pre>
';
        } else {
            echo '<h2>The database appears to be up to date</h2>
';
        }
    }
    function manage_tables($tables, $action)
    {
        $sql_commands_to_run = array();
        if (!($action == 'create')) {
            if (!($action == 'drop')) {
            }
            foreach ($tables as $table) {
                $sql_commands_to_run[] = "DROP TABLE {$table};";
            }
            return $sql_commands_to_run;
        } else {
            foreach ($tables as $table) {
                $query = $this->DB1->query("SHOW CREATE TABLE `{$table}` -- create tables");
                $table_structure = $query->row_array();
                $sql_commands_to_run[] = $table_structure['Create Table'] . ';';
            }
            if (!($action == 'drop')) {
            }
            foreach ($tables as $table) {
                $sql_commands_to_run[] = "DROP TABLE {$table};";
            }
            return $sql_commands_to_run;
        }
    }
    function compare_table_structures($development_tables, $live_tables)
    {
        $tables_need_updating = array();
        $live_table_structures = $development_table_structures = array();
        foreach ($development_tables as $table) {
            $query = $this->DB1->query("SHOW CREATE TABLE `{$table}` -- dev");
            $table_structure = $query->row_array();
            $development_table_structures[$table] = $table_structure['Create Table'];
        }
        foreach ($live_tables as $table) {
            $query = $this->DB2->query("SHOW CREATE TABLE `{$table}` -- live");
            $table_structure = $query->row_array();
            $live_table_structures[$table] = $table_structure['Create Table'];
        }
        foreach ($development_tables as $table) {
            $development_table = $development_table_structures[$table];
            $live_table = isset($live_table_structures[$table]) ? $live_table_structures[$table] : '';
            if (!($this->count_differences($development_table, $live_table) > 0)) {
            } else {
                $tables_need_updating[] = $table;
            }
        }
        return $tables_need_updating;
    }
    function count_differences($old, $new)
    {
        $differences = 0;
        $old = trim(preg_replace('/\s+/', '', $old) ?? '');
        $new = trim(preg_replace('/\s+/', '', $new) ?? '');
        if (!($old == $new)) {
            $old = explode(' ', $old ?? '');
            $new = explode(' ', $new ?? '');
            $length = max(count($old), count($new));
            $i = 0;
            if (!($i < $length)) {
            }
            if (!($old[$i] != $new[$i])) {
            }
            $differences++;
            $i++;
        } else {
            return $differences;
        }
    }
    function update_existing_tables($tables)
    {
        $sql_commands_to_run = array();
        $table_structure_development = array();
        $table_structure_live = array();
        if (!(is_array($tables) && !empty($tables))) {
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->determine_field_changes($table_structure_development, $table_structure_live));
            return $sql_commands_to_run;
        } else {
            foreach ($tables as $table) {
                $table_structure_development[$table] = $this->table_field_data((array) $this->DB1, $table);
                $table_structure_live[$table] = $this->table_field_data((array) $this->DB2, $table);
            }
            $sql_commands_to_run = array_merge($sql_commands_to_run, $this->determine_field_changes($table_structure_development, $table_structure_live));
            return $sql_commands_to_run;
        }
    }
    function table_field_data($database, $table)
    {
        $conn = mysqli_connect($database['hostname'], $database['username'], $database['password']);
        mysql_select_db($database['database']);
        $result = mysql_query("SHOW COLUMNS FROM `{$table}`");
        if (!$row = mysql_fetch_assoc($result)) {
            return $fields;
        } else {
            $fields[] = $row;
            if (!$row = mysql_fetch_assoc($result)) {
            }
        }
    }
    function determine_field_changes($source_field_structures, $destination_field_structures)
    {
        $sql_commands_to_run = array();
        foreach ($source_field_structures as $table => $fields) {
            foreach ($fields as $field) {
                if ($this->in_array_recursive($field['Field'], $destination_field_structures[$table])) {
                    $modify_field = '';
                    $n = 0;
                    if (!($n < count($fields))) {
                    }
                    if (!(isset($fields[$n]) && isset($destination_field_structures[$table][$n]) && $fields[$n]['Field'] == $destination_field_structures[$table][$n]['Field'])) {
                    }
                    $differences = array_diff($fields[$n], $destination_field_structures[$table][$n]);
                    if (!(is_array($differences) && !empty($differences))) {
                    }
                    $modify_field = "ALTER TABLE {$table} MODIFY COLUMN `" . $fields[$n]['Field'] . '` ' . $fields[$n]['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
                    $modify_field .= isset($fields[$n]['Default']) && $fields[$n]['Default'] != '' ? ' DEFAULT \'' . $fields[$n]['Default'] . '\'' : '';
                    $modify_field .= isset($fields[$n]['Null']) && $fields[$n]['Null'] == 'YES' ? ' NULL' : ' NOT NULL';
                    $modify_field .= isset($fields[$n]['Extra']) && $fields[$n]['Extra'] != '' ? ' ' . $fields[$n]['Extra'] : '';
                    $modify_field .= isset($previous_field) && $previous_field != '' ? ' AFTER ' . $previous_field : '';
                    $modify_field .= ';';
                    $previous_field = $fields[$n]['Field'];
                    if (!($modify_field != '' && !in_array($modify_field, $sql_commands_to_run))) {
                    }
                    $sql_commands_to_run[] = $modify_field;
                    $n++;
                } else {
                    $add_field = "ALTER TABLE {$table} ADD COLUMN `" . $field['Field'] . '` ' . $field['Type'] . ' CHARACTER SET ' . $this->CHARACTER_SET;
                    $add_field .= isset($field['Null']) && $field['Null'] == 'YES' ? ' Null' : '';
                    $add_field .= ' DEFAULT ' . $field['Default'];
                    $add_field .= isset($field['Extra']) && $field['Extra'] != '' ? ' ' . $field['Extra'] : '';
                    $add_field .= ';';
                    $sql_commands_to_run[] = $add_field;
                }
            }
        }
        return $sql_commands_to_run;
    }
    function in_array_recursive($needle, $haystack, $strict = false)
    {
        foreach ($haystack as $array => $item) {
            $item = $item['Field'];
            if (!(($strict ? $item === $needle : $item == $needle) || is_array($item) && in_array_recursive($needle, $item, $strict))) {
            } else {
                return true;
            }
        }
        return false;
    }
}
```

---

## File: application/controllers_progress/Dashboard.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->model('Master_model', 'master');
        } else {
            redirect('auth');
            $this->load->model('Master_model', 'master');
        }
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Cbt_model', 'cbt');
    }
    public function admin_box($setting, $tp, $smt)
    {
        $where = '';
        if ($setting->jenjang == '1') {
            $where = 'jenjang=0 OR jenjang=1';
            $box = [['box' => 'blue', 'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa', 'url' => 'datasiswa', 'icon' => 'users'], ['box' => 'cyan', 'total' => $this->dashboard->total('master_kelas', 'id_tp=' . $tp . ' AND id_smt=' . $smt), 'title' => 'Rombel', 'url' => 'datakelas', 'icon' => 'bell'], ['box' => 'teal', 'total' => $this->dashboard->total('master_guru'), 'title' => 'Guru', 'url' => 'dataguru', 'icon' => 'user'], ['box' => 'fuchsia', 'total' => $this->dashboard->totalWaliKelas($tp, $smt), 'title' => 'Wali Kelas', 'url' => 'dataguru', 'icon' => 'user'], ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'url' => 'datamapel', 'icon' => 'book'], ['box' => 'yellow', 'total' => $this->dashboard->total('master_ekstra'), 'title' => 'Ekstrakurikuler', 'url' => 'dataekstra', 'icon' => 'book']];
            $info_box = json_decode(json_encode($box), FALSE);
            return $info_box;
        } else {
            if ($setting->jenjang == '2') {
            }
            $box = [['box' => 'blue', 'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa', 'url' => 'datasiswa', 'icon' => 'users'], ['box' => 'cyan', 'total' => $this->dashboard->total('master_kelas', 'id_tp=' . $tp . ' AND id_smt=' . $smt), 'title' => 'Rombel', 'url' => 'datakelas', 'icon' => 'bell'], ['box' => 'teal', 'total' => $this->dashboard->total('master_guru'), 'title' => 'Guru', 'url' => 'dataguru', 'icon' => 'user'], ['box' => 'fuchsia', 'total' => $this->dashboard->totalWaliKelas($tp, $smt), 'title' => 'Wali Kelas', 'url' => 'dataguru', 'icon' => 'user'], ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'url' => 'datamapel', 'icon' => 'book'], ['box' => 'yellow', 'total' => $this->dashboard->total('master_ekstra'), 'title' => 'Ekstrakurikuler', 'url' => 'dataekstra', 'icon' => 'book']];
            $info_box = json_decode(json_encode($box), FALSE);
            return $info_box;
        }
    }
    public function guru_box($setting)
    {
        $where = '';
        if ($setting->jenjang == '1') {
            $where = 'jenjang=0 OR jenjang=1';
            $box = [['box' => 'teal', 'total' => $this->dashboard->total('master_kelas'), 'title' => 'Rombel', 'icon' => 'user'], ['box' => 'blue', 'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa', 'icon' => 'users'], ['box' => 'fuchsia', 'total' => $this->dashboard->total('master_guru'), 'title' => 'Guru', 'icon' => 'user'], ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'icon' => 'book']];
            $info_box = json_decode(json_encode($box), FALSE);
            return $info_box;
        } else {
            if ($setting->jenjang == '2') {
            }
            $box = [['box' => 'teal', 'total' => $this->dashboard->total('master_kelas'), 'title' => 'Rombel', 'icon' => 'user'], ['box' => 'blue', 'total' => $this->dashboard->total('master_siswa'), 'title' => 'Siswa', 'icon' => 'users'], ['box' => 'fuchsia', 'total' => $this->dashboard->total('master_guru'), 'title' => 'Guru', 'icon' => 'user'], ['box' => 'success', 'total' => $this->dashboard->total('master_mapel', $where), 'title' => 'Mapel', 'icon' => 'book']];
            $info_box = json_decode(json_encode($box), FALSE);
            return $info_box;
        }
    }
    public function ujian_box()
    {
        $box = [['box' => 'indigo', 'total' => $this->dashboard->total('cbt_ruang'), 'title' => 'Ruang Ujian', 'url' => 'cbtruang', 'icon' => 'school'], ['box' => 'maroon', 'total' => $this->dashboard->total('cbt_sesi'), 'title' => 'Sesi', 'url' => 'cbtsesi', 'icon' => 'clock'], ['box' => 'green', 'total' => $this->dashboard->total('cbt_bank_soal'), 'title' => 'Bank Soal', 'url' => 'cbtbanksoal', 'icon' => 'folder'], ['box' => 'teal', 'total' => $this->dashboard->totalJadwal(), 'title' => 'Jadwal', 'url' => 'cbtjadwal', 'icon' => 'clock']];
        $info_box = json_decode(json_encode($box), FALSE);
        return $info_box;
    }
    public function menu_siswa_box()
    {
        $box = [['title' => 'Jadwal Pelajaran', 'icon' => 'ic_online.png', 'link' => 'siswa/jadwalpelajaran'], ['title' => 'Materi', 'icon' => 'ic_elearning.png', 'link' => 'siswa/materi'], ['title' => 'Tugas', 'icon' => 'ic_questions.png', 'link' => 'siswa/tugas'], ['title' => 'Ujian / Ulangan', 'icon' => 'ic_question.png', 'link' => 'siswa/cbt'], ['title' => 'Nilai Hasil', 'icon' => 'ic_exam.png', 'link' => 'siswa/hasil'], ['title' => 'Absensi', 'icon' => 'ic_clipboard.png', 'link' => 'siswa/kehadiran'], ['title' => 'Catatan Guru', 'icon' => 'ic_student.png', 'link' => 'siswa/catatan']];
        $info_box = json_decode(json_encode($box), FALSE);
        return $info_box;
    }
    public function index()
    {
        $setting = $this->dashboard->getSetting();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Beranda', 'subjudul' => 'Halaman Utama', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $kelass = [];
        if (!($tp != null)) {
            $data['kelases'] = $kelass;
        } else {
            $kelass = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['kelases'] = $kelass;
        }
        $day = date('N', strtotime(date('Y-m-d')));
        $jadwal = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, null, $day);
        $kbms = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt);
        foreach ($kbms as $kbm) {
            $kbm->istirahat = unserialize($kbm->istirahat);
        }
        $arrJadwalKelas = [];
        foreach ($jadwal as $key => $item) {
            $arrJadwalKelas[$item->id_kelas][$item->jam_ke] = $item;
        }
        $arrKbm = [];
        foreach ($kbms as $key => $item) {
            $arrKbm[$item->id_kelas] = $item;
        }
        if ($this->ion_auth->in_group('siswa')) {
        }
        $token = $this->cbt->getToken();
        $tkn['token'] = '';
        $tkn['auto'] = '0';
        $tkn['jarak'] = '1';
        $tkn['elapsed'] = '00:00:00';
        $data['token'] = $token != null ? $token : json_decode(json_encode($tkn));
        $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        $data['jadwals'] = $arrJadwalKelas;
        $data['kbms'] = $arrKbm;
        $data['mapels'] = $this->master->getAllMapel();
        $tglJadwals = $this->cbt->getAllJadwalByJenis(null, $tp->id_tp, $smt->id_smt);
        foreach ($tglJadwals as $tgl => $jadwalss) {
            foreach ($jadwalss as $mpl => $jadwals) {
                foreach ($jadwals as $jadwal) {
                    $jadwal->bank_kelas = unserialize($jadwal->bank_kelas);
                    foreach ($jadwal->bank_kelas as $kb) {
                        if (!($kb['kelas_id'] != '')) {
                        } else {
                            $p = $this->cbt->getKelasUjian($kb['kelas_id']);
                            $jadwal->peserta[] = $p;
                        }
                    }
                }
            }
        }
        $data['jadwals_ujian'] = $tglJadwals;
        $data['pengawas'] = $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt, null, null);
        $data['ruangs'] = $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []);
        $data['gurus'] = $this->dropdown->getAllGuru();
        if ($this->ion_auth->is_admin()) {
        }
        if ($this->ion_auth->in_group('guru')) {
        }
    }
    public function checkTokenJadwal()
    {
        $data['ada_ujian'] = $this->cbt->getDataJadwalByTgl(date('Y-m-d'));
        $token = $this->cbt->getToken();
        $token->now = date('d-m-Y H:i:s');
        $data['token'] = $token;
        $this->output_json($data);
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
    public function gantiTahun()
    {
        $aktif = $this->input->post('active', true);
        $rows = count($this->input->post('tahun', true));
        $i = 0;
        if (!($i <= $rows)) {
            $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
            $data['update'] = $update;
            $data['status'] = true;
            $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
            $this->output_json($data);
        } else {
            $id_tp = $this->input->post('id_tp[' . $i . ']', true);
            $tahun = $this->input->post('tahun[' . $i . ']', true);
            if ($id_tp === $aktif) {
            }
            $active = 0;
            $update[] = array('id_tp' => $id_tp, 'tahun' => $tahun, 'active' => $active);
            $i++;
            if (!($i <= $rows)) {
            }
        }
    }
    public function gantiSemester()
    {
        $aktif = $this->input->post('active', true);
        $rows = count($this->input->post('smt', true));
        $i = 1;
        if (!($i <= $rows)) {
            $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
            $data['update'] = $update;
            $data['status'] = true;
            $this->logging->saveLog(4, 'mengganti semester aktif');
            $this->output_json($data);
        } else {
            $id_smt = $this->input->post('id_smt[' . $i . ']', true);
            $smt = $this->input->post('smt[' . $i . ']', true);
            if ($id_smt === $aktif) {
            }
            $active = 0;
            $update[] = array('id_smt' => $id_smt, 'smt' => $smt, 'active' => $active);
            $i++;
            if (!($i <= $rows)) {
            }
        }
    }
    public function getNotifikasi()
    {
    }
    public function getLog($limit)
    {
        $this->output_json($this->logging->loadAktifitas($limit));
    }
    public function hapusLog()
    {
        $this->db->trans_start();
        if ($this->db->empty_table('log')) {
            $deleted = ['status' => true, 'message' => 'berhasil'];
        } else {
            $deleted = ['status' => false, 'message' => 'gagal'];
        }
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
        $istirahat = unserialize($jadwal->istirahat);
        $this->output_json(array('jadwal' => $jadwal, 'istirahat' => $istirahat));
    }
}
```

---

## File: application/controllers_progress/Dataalumni.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataalumni extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Rapor_model', 'rapor');
        $this->form_validation->set_error_delimiters('', '');
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
        $tahun = $this->input->get('tahun', true);
        $kelas_akhir = $this->input->get('kelas', true);
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Data Kelulusan & Alumni', 'subjudul' => 'Data Alumni', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $allTp = $this->dashboard->getTahun();
        $data['tp'] = $allTp;
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tahun_lulus'] = $this->master->getDistinctTahunLulus();
        $data['kelas_akhir'] = $this->master->getDistinctKelasAkhir();
        $data['tahun_selected'] = $tahun;
        $data['kelas_selected'] = $kelas_akhir;
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $jumlah_lulus = $this->rapor->getJumlahLulus($tp->id_tp - 1, '2', $level);
        $idSearch = array_search($tp->id_tp - 1, array_column($allTp, 'id_tp'));
        $tpBefore = $allTp[$idSearch]->tahun;
        $splitTahun = explode('/', $tpBefore ?? '');
        $alumnis = $this->master->getAlumniByTahun($splitTahun[1]);
        if ($jumlah_lulus > count($alumnis)) {
            $data['jumlah_lulus'] = $jumlah_lulus;
        } else {
            $data['jumlah_lulus'] = 0;
        }
        if ($tahun == null) {
        }
        if ($tahun != null && $tahun != '') {
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function generateAlumni()
    {
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $allTp = $this->dashboard->getTahun();
        $searchId = array_search('1', array_column($allTp, 'active'));
        $idBefore = $allTp[$searchId - 1]->id_tp;
        $tpBefore = $allTp[$searchId - 1]->tahun;
        $splitTahun = explode('/', $tpBefore ?? '');
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $siswas = $this->rapor->getSiswaLulus($tp->id_tp - 1, '2', $level);
        $ids = [];
        $this->db->trans_start();
        foreach ($siswas as $siswa) {
            if ($siswa->naik != null && $siswa->naik == '0') {
            } else {
                $ids[] = $siswa->id_siswa;
                $this->db->where('id_siswa', $siswa->id_siswa);
                $this->db->set('status', '2');
                $this->db->set('tahun_lulus', $splitTahun[1]);
                $this->db->set('no_ijazah', '- -');
                $this->db->set('kelas_akhir', $siswa->kelas_akhir);
                $this->db->update('buku_induk');
            }
        }
        $this->db->trans_complete();
        $this->output_json($ids);
    }
    public function luluskan()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $mode = $this->input->post('mode', true);
        $idkelases = [];
        $alumnikelas = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->kelas_baru;
            $alumnikelas[$d->kelas_baru][] = ['id' => $d->id_siswa];
        }
        $idkelases = array_unique($idkelases);
        $res = [];
        $idks = [];
        foreach ($idkelases as $ik) {
            $kelas = $this->kelas->get_one($ik, $tp->id_tp - 1, '2');
            $kelas_baru = $this->kelas->getKelasByNama($kelas->nama_kelas, $tp->id_tp, $smt->id_smt);
            if ($kelas_baru == null) {
                $jumlah = serialize($alumnikelas[$ik]);
                $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'alumni_id' => $kelas->alumni_id, 'jumlah_alumni' => $jumlah);
                $this->db->insert('master_kelas', $data);
                array_push($idks, $this->db->insert_id());
            } else {
                if ($mode == 'peralumni') {
                }
                $jumlah = serialize($alumnikelas[$ik]);
                array_push($idks, $kelas_baru->id_kelas);
                $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'alumni_id' => $kelas->alumni_id, 'jumlah_alumni' => $jumlah);
                $this->db->where('id_kelas', $kelas_baru->id_kelas);
                $this->db->update('master_kelas', $data);
            }
            foreach ($idks as $idk) {
                foreach ($alumnikelas[$ik] as $s) {
                    $insert = ['id_kelas_alumni' => $tp->id_tp . $smt->id_smt . $s['id'], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $s['id']];
                    $res[] = $this->db->replace('kelas_alumni', $insert);
                }
            }
        }
        $data['res'] = $alumnikelas;
        $this->output_json($data);
    }
    public function detail($id)
    {
        $alumni = $this->master->getAlumniById($id);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $alumni->nama, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $alumni->nis, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $alumni->nisn, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $alumni->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $alumni->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $alumni->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'class' => 'tahun', 'type' => 'text']];
        $inputBio = [['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $alumni->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $alumni->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $alumni->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $alumni->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $alumni->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $alumni->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $alumni->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $alumni->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $alumni->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $alumni->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $alumni->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $alumni->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $alumni->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $alumni->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $alumni->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $alumni->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $alumni->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $alumni->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $alumni->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $alumni->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $alumni->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $alumni->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $alumni->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $alumni->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $alumni->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $alumni->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Alumni', 'subjudul' => 'Edit Data Alumni', 'alumni' => $alumni, 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/edit');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function add()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Alumni', 'subjudul' => 'Tambah Data Alumni', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tipe'] = 'add';
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/add');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $u_nis = '|is_unique[master_siswa.nis]';
        $u_nisn = '|is_unique[master_siswa.nisn]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        if ($this->form_validation->run() == FALSE) {
            $data['insert'] = false;
            $data['text'] = 'Data Sudah ada, Pastikan NIS, NISN dan Username belum digunakan alumni lain';
        } else {
            $insert = ['nama' => $this->input->post('nama_alumni', true), 'nis' => $nis, 'nisn' => $nisn, 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'foto' => 'uploads/foto_siswa/' . $nis . 'jpg'];
            $this->db->set('uid', 'UUID()', FALSE);
            $this->db->insert('master_siswa', $insert);
            $last_id = $this->db->insert_id();
            $uid = $this->db->select('uid')->from('master_siswa')->where('id_siswa', $last_id)->get()->row();
            $induk = ['id_siswa' => $last_id, 'uid' => $uid->uid, 'kelas_akhir' => $this->input->post('kelas_akhir', true), 'tahun_lulus' => $this->input->post('tahun_lulus', true), 'no_ijazah' => $this->input->post('no_ijazah', true), 'status' => 2];
            $data['insert'] = $this->db->insert('buku_induk', $induk);
            $data['text'] = 'Alumni berhasil ditambahkan';
        }
        $this->output_json($data);
    }
    public function edit()
    {
        $id = $this->input->get('id', true);
        $alumni = $this->master->getAlumniById($id);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $alumni->nama, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $alumni->nis, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $alumni->nisn, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $alumni->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $alumni->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $alumni->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'class' => 'tahun', 'type' => 'text']];
        $inputBio = [['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $alumni->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $alumni->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $alumni->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $alumni->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $alumni->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $alumni->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $alumni->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $alumni->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $alumni->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $alumni->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $alumni->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $alumni->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $alumni->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $alumni->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $alumni->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $alumni->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $alumni->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $alumni->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $alumni->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $alumni->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $alumni->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $alumni->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $alumni->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $alumni->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $alumni->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $alumni->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Alumni', 'subjudul' => 'Edit Data Alumni', 'alumni' => $alumni, 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/alumni/edit');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function updateData()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $alumni = $this->master->getAlumniById($id_siswa);
        $u_nis = $alumni->nis === $nis ? '' : '|is_unique[mater_alumni.nis]';
        $u_nisn = $alumni->nisn === $nisn ? '' : '|is_unique[mater_alumni.nisn]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        if ($this->form_validation->run() == FALSE) {
            $data['insert'] = false;
            $data['text'] = 'Data Sudah ada, Pastikan NIS, dan NISN belum digunakan alumni lain';
        } else {
            $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->input->post('tanggal_lahir', true), 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => 'uploads/foto_siswa/' . $nis . '.jpg'];
            $action = $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
            $data['insert'] = $input;
            $data['text'] = 'Alumni berhasil diperbaharui';
        }
        $this->output_json($data);
    }
    function uploadFile($id_siswa)
    {
        $alumni = $this->master->getAlumniById($id_siswa);
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/foto_siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = $alumni->nis;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFoto()
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        unlink($file_name);
        echo 'File Delete Successfully';
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function do_import()
    {
        $input = json_decode($this->input->post('alumni', true));
        $this->db->trans_start();
        foreach ($input as $key1 => $val1) {
            $data = [];
            foreach (((array) $input)[$key1] as $key => $val) {
                $data[$key] = $val;
            }
            $data['foto'] = 'uploads/foto_siswa/' . $data['nis'] . '.jpg';
            $save = $this->db->insert('master_siswa', $data);
        }
        $this->db->trans_complete();
        $this->output->set_content_type('application/json')->set_output($save);
    }
    public function editKelulusan()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $thn = $this->input->post('tahun_lulus', true);
        $no_ijazah = $this->input->post('no_ijazah', true);
        $kelas_akhir = $this->input->post('kelas_akhir', true);
        $this->db->set('kelas_akhir', $kelas_akhir);
        $this->db->set('tahun_lulus', $thn);
        $this->db->set('no_ijazah', $no_ijazah);
        $this->db->where('id_siswa', $id_siswa);
        $status = $this->db->update('master_siswa');
        $data['status'] = $status;
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Dataekstra.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataekstra extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Ekstrakurikuler', 'subjudul' => 'Data Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['ekskul'] = $this->dropdown->getAllEkskul();
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $kelasEks = [];
        foreach ($kelas as $key => $kls) {
            $kelasEks[$key] = $this->kelas->getKelasEkskul($key, $tp->id_tp, $smt->id_smt);
        }
        $data['ekskul_kelas'] = $kelasEks;
        $data['kelas'] = $kelas;
        $data['pembimbing'] = $this->dropdown->getAllGuru();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/ekstra/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $insert = ['nama_ekstra' => $this->input->post('nama_ekstra', true), 'kode_ekstra' => $this->input->post('kode_ekstra', true)];
        $data = $this->master->create('master_ekstra', $insert);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function read()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_ekstra');
        echo $this->datatables->generate();
    }
    public function update()
    {
        $data = $this->master->updateEkstra();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete($id)
    {
        $messages = [];
        $tables = [];
        $tabless = $this->db->list_tables();
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_ekstra' || $field->name == 'ekstra_id')) {
                } else {
                    array_push($tables, $table);
                }
            }
        }
        $this->output_json($tables);
        foreach ($tables as $table) {
            if (!($table != 'master_ekstra')) {
            } else {
                $this->db->where('id_ekstra', $id);
                $num = $this->db->count_all_results($table);
                if (!($num > 0)) {
                }
                array_push($messages, $table);
            }
        }
        if ($messages && count($messages) > 0) {
            $this->output_json(['status' => false, 'total' => 'Mapel digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        } else {
            if ($this->master->delete('master_ekstra', [$id], 'id_ekstra')) {
            }
            $this->output_json(['status' => false, 'message' => 'Ekskul gagal dihapus']);
        }
    }
    public function save()
    {
        $check_kelas = json_decode(json_encode(json_decode($this->input->post('kelas', true))));
        $tp = $this->master->getTahunActive()->id_tp;
        $smt = $this->master->getSemesterActive()->id_smt;
        $row_insert = 0;
        $update = [];
        foreach ($check_kelas as $key => $kls) {
            $check_ekskul = $this->input->post('ekskul' . $kls->kls_id, true);
            if (!$check_ekskul) {
            } else {
                $row_ekskul = count($this->input->post('ekskul' . $kls->kls_id, true));
                $ekstra = [];
                $j = 0;
                if (!($j <= $row_ekskul)) {
                }
                $kelaseks = $this->input->post('ekskul' . $kls->kls_id . '[' . $j . ']', true);
                $ekstra[] = ['ekstra' => $kelaseks];
                $j++;
            }
        }
        $res['status'] = true;
        $res['update'] = $update;
        $this->output_json($res);
    }
    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Import Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if (!($import_data != null)) {
            $data['tp'] = $this->dashboard->getTahun();
        } else {
            $data['import'] = $import_data;
            $data['tp'] = $this->dashboard->getTahun();
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/ekstra/import');
        $this->load->view('_templates/dashboard/_footer');
    }
}
```

---

## File: application/controllers_progress/Dataguru.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dataguru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
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
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
    }
    public function index()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $mode = $this->input->get('mode', true);
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Guru', 'subjudul' => 'Data Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['mode'] = $mode == null ? '1' : '2';
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $mapels = $this->master->getAllMapel();
        $ret = [];
        if (!$mapels) {
            $data['mapels'] = $ret;
        } else {
            foreach ($mapels as $key => $row) {
                $ret[$row->id_mapel] = $row;
            }
            $data['mapels'] = $ret;
        }
        $data['extras'] = $this->dropdown->getAllKodeEkskul();
        $data['kelass'] = $this->master->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['gurus'] = $this->master->getAllDataGuru($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->master->getDataGuru($tp->id_tp, $smt->id_smt), false);
    }
    public function edit($id)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $guru = $this->master->getGuruById($id, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Edit Guru', 'subjudul' => 'Edit Data Guru', 'mapel' => $this->master->getAllMapel(), 'guru' => $guru, 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['id_active'] = $id;
        $inputsProfile = [['label' => 'Nama Lengkap', 'name' => 'nama_guru', 'value' => $guru->nama_guru, 'icon' => 'far fa-user', 'type' => 'text'], ['label' => 'Email', 'name' => 'email', 'value' => $guru->email, 'icon' => 'far fa-envelope', 'type' => 'text'], ['label' => 'NIP / NUPTK', 'name' => 'nip', 'value' => $guru->nip, 'icon' => 'far fa-id-card', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $guru->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'type' => 'text'], ['label' => 'No. Handphone', 'name' => 'no_hp', 'value' => $guru->no_hp, 'icon' => 'fa fa-phone', 'type' => 'number'], ['label' => 'Agama', 'name' => 'agama', 'value' => $guru->agama, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputsAlamat = [['label' => 'NIK', 'name' => 'no_ktp', 'value' => $guru->no_ktp, 'icon' => 'far fa-id-card', 'type' => 'number'], ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'value' => $guru->tempat_lahir, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Tgl. Lahir', 'name' => 'tgl_lahir', 'value' => $guru->tgl_lahir, 'icon' => 'fa fa-calendar', 'type' => 'text'], ['label' => 'Alamat', 'name' => 'alamat_jalan', 'value' => $guru->alamat_jalan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kecamatan', 'name' => 'kecamatan', 'value' => $guru->kecamatan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kota/Kab.', 'name' => 'kabupaten', 'value' => $guru->kabupaten, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Provinsi', 'name' => 'provinsi', 'value' => $guru->provinsi, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kode Pos', 'name' => 'kode_pos', 'value' => $guru->kode_pos, 'icon' => 'fa fa-envelope', 'type' => 'number']];
        $data['input_profile'] = json_decode(json_encode($inputsProfile), FALSE);
        $data['input_alamat'] = json_decode(json_encode($inputsAlamat), FALSE);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/edit');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $this->load->model('Master_model', 'master');
        $nip = $this->input->post('nip', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);
        $u_nip = 'is_unique[master_guru.nip]';
        $u_username = '|is_unique[master_guru.username]';
        $this->form_validation->set_rules('nip', 'NIP', 'required|numeric|trim|' . $u_nip);
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('username', 'Username', 'required|trim' . $u_username);
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data = ['status' => false, 'errors' => ['nip' => form_error('nip'), 'nama_guru' => form_error('nama_guru'), 'username' => form_error('username'), 'password' => form_error('password')]];
            $this->output_json($data);
        } else {
            $input = ['nip' => trim($nip ?? ''), 'nama_guru' => trim($nama_guru ?? ''), 'username' => trim($username ?? ''), 'password' => trim($password ?? ''), 'foto' => 'uploads/profiles/' . trim($nip ?? '00') . '.jpg'];
            $action = $this->master->create('master_guru', $input);
            if ($action) {
            }
            $this->output_json(['status' => false]);
        }
    }
    public function save()
    {
        $this->load->model('Master_model', 'master');
        $method = $this->input->post('method', true);
        $id_guru = $this->input->post('id_guru', true);
        $nip = $this->input->post('nip', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $email = $this->input->post('email', true);
        $mapel = $this->input->post('password', true);
        if ($method == 'add') {
            $u_nip = '|is_unique[guru.nip]';
            $u_email = '|is_unique[guru.email]';
        } else {
            $dbdata = $this->master->getGuruById($id_guru);
            $u_nip = $dbdata->nip === $nip ? '' : '|is_unique[guru.nip]';
            $u_email = $dbdata->email === $email ? '' : '|is_unique[guru.email]';
        }
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[8]' . $u_nip);
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email' . $u_email);
        $this->form_validation->set_rules('mapel', 'Mata Kuliah', 'required');
        if ($this->form_validation->run() == FALSE) {
        }
        $input = ['nip' => $nip, 'nama_guru' => $nama_guru, 'email' => $email, 'mapel_id' => $mapel];
        if ($method === 'add') {
        }
        if (!($method === 'edit')) {
        }
        $action = $this->master->update('master_guru', $input, 'id_guru', $id_guru);
        if ($action) {
        }
        $this->output_json(['status' => false]);
    }
    public function deleteGuru()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('id_guru', true);
        $messages = [];
        $tables = [];
        $tabless = $this->db->list_tables();
        foreach ($tabless as $table) {
            $fields = $this->db->field_data($table);
            foreach ($fields as $field) {
                if (!($field->name == 'id_guru' || $field->name == 'guru_id')) {
                } else {
                    array_push($tables, $table);
                }
            }
        }
        foreach ($tables as $table) {
            if (!($table != 'master_guru')) {
            } else {
                if ($table == 'master_kelas') {
                }
                $this->db->where('id_guru', $chk);
                $num = $this->db->count_all_results($table);
                if (!($num > 0)) {
                }
                array_push($messages, $table);
            }
        }
        if (count($messages) > 0) {
            $this->output_json(['count' => count($messages), 'status' => false, 'message' => 'Data guru digunakan di ' . count($messages) . ' tabel:<br>' . implode('<br>', $messages)]);
        } else {
            $data['status'] = $this->master->delete('master_guru', $chk, 'id_guru');
            $this->output_json($data);
        }
    }
    public function detail($id_guru)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Detail Guru', 'subjudul' => 'Info Jabatan Guru', 'mapel' => $this->master->getAllMapel(), 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['kelas'] = $this->master->getAllKelas();
        $data['id_guru'] = $id_guru;
        $data['guru'] = ['detail' => $this->master->getGuruByArrId([$id_guru])[0], 'jabatan' => $this->master->getDetailJabatanGuru($id_guru), 'materi' => $this->db->get_where('kelas_materi', 'id_guru=' . $id_guru)->num_rows(), 'catatan_mapel' => $this->db->get_where('kelas_catatan_mapel', 'id_guru=' . $id_guru)->num_rows(), 'bank_soal' => $this->db->get_where('cbt_bank_soal', 'bank_guru_id=' . $id_guru)->num_rows(), 'pengawas' => $this->db->get_where('cbt_pengawas', 'id_guru LIKE "%' . $id_guru . '%"')->num_rows(), 'posts' => $this->db->get_where('post', 'dari=' . $id_guru)->num_rows(), 'comments' => $this->db->get_where('post_comments', 'dari=' . $id_guru)->num_rows(), 'replies' => $this->db->get_where('post_reply', 'dari=' . $id_guru)->num_rows()];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/detail');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function delete()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('master_guru', $chk, 'id_guru')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function forceDelete()
    {
        $this->load->model('Master_model', 'master');
        $id_guru = $this->input->post('id_guru', true);
        $data['status'] = $this->master->delete('master_guru', $id_guru, 'id_guru');
        $this->output_json($data);
    }
    public function create_user()
    {
        $this->load->model('Master_model', 'master');
        $id = $this->input->get('id', true);
        $data = $this->master->getGuruById($id);
        $nama = explode(' ', $data->nama_guru ?? '');
        $first_name = $nama[0];
        $last_name = end($nama);
        $username = $data->nip;
        $password = $data->nip;
        $email = $data->email;
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('2');
        if ($this->ion_auth->username_check($username)) {
            $data = ['status' => false, 'msg' => 'Username tidak tersedia (sudah digunakan).'];
        } else {
            if ($this->ion_auth->email_check($email)) {
            }
            $this->ion_auth->register($username, $password, $email, $additional_data, $group);
            $data = ['status' => true, 'msg' => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'];
        }
        $this->output_json($data);
    }
    public function import($import_data = null)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Guru', 'subjudul' => 'Tambah Data Guru', 'mapel' => $this->master->getAllMapel(), 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        if (!($import_data != null)) {
            $data['tp'] = $this->dashboard->getTahun();
        } else {
            $data['import'] = $import_data;
            $data['tp'] = $this->dashboard->getTahun();
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/add');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function do_import()
    {
        $this->load->model('Master_model', 'master');
        $input = $this->input->post('guru', true);
        $errors = [];
        foreach ($input as $guru) {
            $this->form_validation->set_data($guru);
            $this->form_validation->set_rules('2', 'Nama Guru', 'required|trim|min_length[1]|max_length[50]');
            $this->form_validation->set_rules('3', 'NIP', 'required|trim|min_length[6]|max_length[30]|is_unique[master_guru.nip]');
            $this->form_validation->set_rules('5', 'Username', 'required|trim|min_length[3]|max_length[30]|is_unique[master_guru.username]');
            $this->form_validation->set_rules('6', 'Password', 'required|trim|min_length[5]|max_length[30]');
            if (!($this->form_validation->run() == FALSE)) {
            } else {
                $errors[] = ['nama' => form_error('2'), 'nip' => form_error('3'), 'username' => form_error('5'), 'password' => form_error('6')];
            }
        }
        if (count($errors) > 0) {
            $data = ['status' => false, 'errors' => $errors];
        } else {
            $data_insert = [];
            foreach ($input as $guru) {
                $foto = 'uploads/profiles/' . trim($guru['3'] ?? '00') . '.jpg';
                if (!isset($guru['7'])) {
                    $data_insert[] = ['nama_guru' => trim($guru['2'] ?? ''), 'nip' => trim($guru['3'] ?? ''), 'kode_guru' => trim($guru['4'] ?? ''), 'username' => trim($guru['5'] ?? ''), 'password' => trim($guru['6'] ?? ''), 'foto' => $foto];
                } else {
                    $base64_image_string = $guru['7'];
                    $extension = $guru['8'];
                    if (!($extension == 'jpeg')) {
                    }
                    $extension = 'jpg';
                    $output_file = trim($guru['3'] ?? '00') . '.' . $extension;
                    file_put_contents('./uploads/profiles/' . $output_file, base64_decode($base64_image_string));
                    $foto = 'uploads/profiles/' . $output_file;
                    $data_insert[] = ['nama_guru' => trim($guru['2'] ?? ''), 'nip' => trim($guru['3'] ?? ''), 'kode_guru' => trim($guru['4'] ?? ''), 'username' => trim($guru['5'] ?? ''), 'password' => trim($guru['6'] ?? ''), 'foto' => $foto];
                }
            }
            $save = $this->master->create('master_guru', $data_insert, true);
            $data = ['status' => true, 'data' => $save, 'insert' => $data_insert];
        }
        $this->output_json($data);
    }
    public function editJabatan($id)
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->master->getJabatanGuru($id, $tp->id_tp, $smt->id_smt);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Jabatan Guru', 'subjudul' => 'Edit Jabatan Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        if (!($group === 'admin')) {
            $data['kelass'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        } else {
            $data['groups'] = $this->ion_auth->groups()->result();
            $data['kelass'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        }
        $data['mapels'] = $this->dropdown->getAllMapel();
        $data['levels'] = $this->dropdown->getAllLevelGuru();
        $data['ekskul'] = $this->dropdown->getAllEkskul();
        $data['kur'] = $smt;
        $smt2 = $smt->id_smt == '1' ? '2' : '1';
        $tp2 = $smt->id_smt == '1' ? $tp->id_tp - 1 : $tp->id_tp;
        $guru_before = $this->master->getJabatanGuru($id, $tp2, $smt2);
        $guru_before->mapel_kelas = json_decode(json_encode(unserialize($guru_before->mapel_kelas ?? '')));
        $guru_before->ekstra_kelas = json_decode(json_encode(unserialize($guru_before->ekstra_kelas ?? '')));
        $data['before'] = ['kelass' => $this->dropdown->getAllKelas($tp2, $smt2), 'guru' => $guru_before];
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/guru/editmapel');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function saveJabatan()
    {
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model', 'kelas');
        $id_guru = $this->input->post('id_guru', true);
        $id_level = $this->input->post('level', true);
        $wali = $this->input->post('kelas_wali', true);
        $copy = $this->input->post('copy', true) != null;
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $smt2 = $smt->id_smt == '1' ? '2' : '1';
        $tp2 = $smt->id_smt == '1' ? $tp->id_tp - 1 : $tp->id_tp;
        $kelass1 = $this->kelas->getNamaKelasByNama($tp->id_tp, $smt->id_smt);
        $kelass2 = $this->dropdown->getAllKelas($tp2, $smt2);
        if ($copy) {
            $tmp_wali = $kelass2[$wali];
            $kelas_wali = $kelass1[$tmp_wali];
        } else {
            $kelas_wali = $wali;
        }
        $mapels = [];
        $check_mapel = $this->input->post('mapel', true);
        if (!$check_mapel) {
        }
        $row_mapels = count($this->input->post('mapel', true));
        $i = 0;
        if (!($i <= $row_mapels)) {
        }
        $mapel = $this->input->post('mapel[' . $i . ']', true);
        $nama_mapel = $this->input->post('nama_mapel' . $mapel, true);
        $check = $this->input->post('kelasmapel' . $mapel, true);
        if (!$check) {
        }
        $row_kelas = count($this->input->post('kelasmapel' . $mapel, true));
        $kelas = [];
        $j = 0;
        if (!($j <= $row_kelas)) {
        }
        $kelasmapel = $this->input->post('kelasmapel' . $mapel . '[' . $j . ']', true);
        if ($copy) {
        }
        $kelas[] = ['kelas' => $kelasmapel];
        $j++;
    }
    public function getDataKelas()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Users_model', 'users');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $jabatans = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        $mapel_terisi = [];
        $ekstra_terisi = [];
        $jbtn = [];
        foreach ($jabatans as $jabatan) {
            $mpl_kls = $jabatan->mapel_kelas = json_decode(json_encode(unserialize($jabatan->mapel_kelas ?? '')));
            $eks_kls = $jabatan->ekstra_kelas = json_decode(json_encode(unserialize($jabatan->ekstra_kelas ?? '')));
            foreach ($mpl_kls as $mpls) {
                $klss = [];
                foreach ($mpls->kelas_mapel as $mpl) {
                    $klss[] = $mpl->kelas;
                }
                $mapel_terisi[$mpls->id_mapel][$jabatan->id_guru] = ['id_guru' => $jabatan->id_guru, 'guru' => $jabatan->nama_guru, 'kelas' => $klss];
            }
            foreach ($eks_kls as $eks) {
                $klse = [];
                foreach ($eks->kelas_ekstra as $ek) {
                    $klse[] = $ek->kelas;
                }
                $ekstra_terisi[$eks->id_ekstra][$jabatan->id_guru] = ['id_guru' => $jabatan->id_guru, 'guru' => $jabatan->nama_guru, 'kelas' => $klse];
            }
            $jbtn[$jabatan->id_jabatan][$jabatan->id_kelas] = ['nama' => $jabatan->nama_guru, 'id' => $jabatan->id_guru];
        }
        $data['jabatan'] = $jbtn;
        $data['mpl_terisi'] = $mapel_terisi;
        $data['eks_terisi'] = $ekstra_terisi;
        $data['kelas'] = $this->users->getKelas($tp->id_tp, $smt->id_smt);
        $this->output_json($data);
    }
    public function addjabatan()
    {
        $mode = $this->input->post('mode', true);
        $id = $this->input->post('id_level', true);
        $s_mode = $mode == '1' ? 'menyimpan' : 'menghapus';
        if ($mode == '1') {
            $insert = ['id_level' => $id, 'level' => $this->input->post('level', true)];
            $replaced = $this->db->replace('level_guru', $insert);
        } else {
            $replaced = $this->db->delete('level_guru', 'id_level=' . $id);
        }
        $data = ['success' => $replaced, 'msg' => $replaced ? 'Sukses ' . $s_mode . ' jabatan' : 'Gagal ' . $s_mode . ' jabatan'];
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Datajurusan.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datajurusan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Jurusan', 'subjudul' => 'Daftar Jurusan', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $kode_peminatan = $this->dropdown->getAllKodePeminatan();
        $data['kode_peminatan'] = $kode_peminatan;
        $arr_kode = [];
        foreach ($kode_peminatan as $kode) {
            $arr_kode[] = $kode->kode_kel_mapel;
        }
        $data['mapel_peminatan'] = $this->dropdown->getMapelPeminatan($arr_kode);
        $jurusans = $this->master->getDataJurusan();
        $jurusan_mapels = [];
        foreach ($jurusans as $jurusan) {
            $jurusan_mapels[$jurusan->id_jurusan] = $this->master->getDataJurusanMapel(explode(',', $jurusan->mapel_peminatan ?? ''));
        }
        $data['jurusans'] = $jurusans;
        $data['jurusan_mapels'] = $jurusan_mapels;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/jurusan/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function add()
    {
        $mapels = [];
        $check_mapel = $this->input->post('mapel', true);
        if (!$check_mapel) {
            $insert = ['nama_jurusan' => $this->input->post('nama_jurusan', true), 'kode_jurusan' => $this->input->post('kode_jurusan', true), 'mapel_peminatan' => implode(',', $mapels)];
            $this->master->create('master_jurusan', $insert, false);
            $data['status'] = $insert;
            $this->output_json($data);
        } else {
            $row_mapels = count($this->input->post('mapel', true));
            $i = 0;
            if (!($i <= $row_mapels)) {
            }
            array_push($mapels, $this->input->post('mapel[' . $i . ']', true));
            $i++;
        }
    }
    public function data()
    {
        $this->output_json($this->master->getDataTableJurusan(), false);
    }
    public function save()
    {
        $rows = count($this->input->post('nama_jurusan', true));
        $mode = $this->input->post('mode', true);
        $i = 1;
        if (!($i <= $rows)) {
            if ($status) {
            }
            if (!isset($error)) {
            }
            $data['errors'] = $error;
            $data['status'] = $status;
            $this->output_json($data);
        } else {
            $nama_jurusan = 'nama_jurusan[' . $i . ']';
            $this->form_validation->set_rules($nama_jurusan, 'Jurusan', 'required');
            $this->form_validation->set_message('required', '{field} Wajib diisi');
            if ($this->form_validation->run() === FALSE) {
            }
            if ($mode == 'add') {
            }
            if (!($mode == 'edit')) {
            }
            $update[] = array('id_jurusan' => $this->input->post('id_jurusan[' . $i . ']', true), 'nama_jurusan' => $this->input->post($nama_jurusan, true));
            $status = TRUE;
            $i++;
            if (!($i <= $rows)) {
            }
        }
    }
    public function update()
    {
        $data = $this->master->updateJurusan();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false, 'total' => 'Tidak ada data yang dipilih!']);
        } else {
            $messages = [];
            $tables = [];
            $tabless = $this->db->list_tables();
            foreach ($tabless as $table) {
                $fields = $this->db->field_data($table);
                foreach ($fields as $field) {
                    if (!($field->name == 'id_jurusan' || $field->name == 'jurusan_id')) {
                    } else {
                        array_push($tables, $table);
                    }
                }
            }
            foreach ($tables as $table) {
                if (!($table != 'master_jurusan')) {
                } else {
                    if ($table == 'master_kelas') {
                    }
                    $this->db->where_in('id_jurusan', $chk);
                    $num = $this->db->count_all_results($table);
                    if (!($num > 0)) {
                    }
                    array_push($messages, $table);
                }
            }
            if (count($messages) > 0) {
            }
            if (!$this->master->delete('master_jurusan', $chk, 'id_jurusan')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function load_jurusan()
    {
        $data = $this->master->getJurusan();
        $this->output_json($data);
    }
    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Import Jurusan', 'subjudul' => 'Import Jurusan', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if (!($import_data != null)) {
            $data['tp'] = $this->dashboard->getTahun();
        } else {
            $data['import'] = $import_data;
            $data['tp'] = $this->dashboard->getTahun();
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/jurusan/import');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function do_import()
    {
        $data = json_decode($this->input->post('jurusan', true));
        $jurusan = [];
        foreach ($data as $j) {
            $jurusan[] = ['nama_jurusan' => $j->nama, 'kode_jurusan' => $j->kode];
        }
        $save = $this->master->create('master_jurusan', $jurusan, true);
        $this->output->set_content_type('application/json')->set_output($save);
    }
    function updateById()
    {
        $id = $this->input->post('id_jurusan');
        $nama = $this->input->post('username', true);
        $kode = $this->input->post('email', true);
        $this->db->set('nama_jurusan', $nama);
        $this->db->set('kode_jurusan', $kode);
        $this->db->where('id_jurusan', $id);
        return $this->db->update('master_jurusan');
    }
    public function hapusById()
    {
        $id = $this->input->post('id');
        $this->db->where('id_jurusan', $id);
        return $this->db->delete('master_jurusan');
    }
    function exist($table, $data)
    {
        $query = $this->db->get_where($table, $data);
        $count = $query->num_rows();
        if ($count === 0) {
            return false;
        } else {
            return true;
        }
    }
}
```

---

## File: application/controllers_progress/Datakelas.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datakelas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Rapor_model', 'rapor');
        $this->form_validation->set_error_delimiters('', '');
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
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Kelas', 'subjudul' => 'Data Kelas', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $chek = $this->kelas->count_all();
        $kelas = [];
        $kelas_lama = [];
        if (!($chek > 0)) {
            $data['kelas'] = $kelas;
        } else {
            $kelas = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);
            $kelas_lama = $this->kelas->getKelasList($tp->id_tp - 1, '2');
            $data['kelas'] = $kelas;
        }
        $data['kelas_lama'] = $kelas_lama;
        $data['jurusan'] = $this->kelas->get_jurusan();
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['guru'] = $this->kelas->get_guru();
        $data['siswa'] = $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function detail($id)
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Detail Kelas', 'subjudul' => 'Detail Kelas', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['kelas'] = $this->kelas->get_one($id);
        $data['jurusan'] = $this->kelas->get_jurusan();
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['guru'] = $this->kelas->get_guru();
        $data['siswas'] = $this->kelas->get_siswa_kelas($id, $tp->id_tp, $smt->id_smt);
        $struktur = $this->kelas->getStrukturKelas($id);
        if ($struktur == null) {
            $data['struktur'] = json_decode(json_encode($this->kelas->dummyStruktur()));
        } else {
            $data['struktur'] = $struktur;
        }
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/detail');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function add()
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Kelas', 'subjudul' => 'Tambah Kelas', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['kelas'] = json_decode(json_encode($this->kelas->dummy()));
        $data['jurusan'] = $this->kelas->get_jurusan();
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['guru'] = $this->kelas->get_guru();
        $siswa = $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt);
        $data['siswa'] = $siswa;
        $data['siswakelas'] = array();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/add');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function edit($id = '')
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Kelas', 'subjudul' => 'Edit Kelas', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['id_kelas'] = $id;
        $data['kelas'] = $this->kelas->get_one($id);
        $data['jurusan'] = $this->kelas->get_jurusan();
        $data['level'] = $this->kelas->getLevel($setting->jenjang);
        $data['guru'] = $this->kelas->getWaliKelas($tp->id_tp, $smt->id_smt);
        $data['siswa'] = $this->kelas->getAllSiswa($tp->id_tp, $smt->id_smt);
        $data['siswakelas'] = $this->kelas->get_siswa_kelas($id, $tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/add');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function save()
    {
        $id = $this->input->post('id_kelas', true);
        $guru_id = $this->input->post('guru_id', TRUE);
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $siswas = $this->input->post('siswa', true);
        $config = array(array('field' => 'nama_kelas', 'label' => 'Nama Kelas', 'rules' => 'trim'), array('field' => 'kode_kelas', 'label' => 'Kode Kelas', 'rules' => 'trim'), array('field' => 'jurusan_id', 'label' => 'Jurusan', 'rules' => 'trim'), array('field' => 'level_id', 'label' => 'Level', 'rules' => 'trim'), array('field' => 'guru_id', 'label' => 'Guru', 'rules' => 'trim'), array('field' => 'siswa_id', 'label' => 'Siswa', 'rules' => 'trim'));
        $siswakelas = [];
        $i = 0;
        if (!($i <= count($siswas))) {
        }
        $id_siswa = isset($siswas[$i]) ? $siswas[$i] : null;
        if (!($id_siswa != null)) {
        }
        array_push($siswakelas, ['id' => $id_siswa]);
        $i++;
    }
    public function update_kelas($id)
    {
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $siswakelas = $this->kelas->get_status_siswa_kelas($id, $id_tp, $id_smt);
        if (!(count($siswakelas) > 0)) {
            $rowsSelect = count($this->input->post('siswa', true));
        } else {
            foreach ($siswakelas as $id_siswa => $sis) {
                $insert = ['id_kelas_siswa' => $id_tp . $id_smt . $id_siswa, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => 0, 'id_siswa' => $id_siswa];
                $this->db->replace('kelas_siswa', $insert);
            }
            $rowsSelect = count($this->input->post('siswa', true));
        }
        $i = 0;
        if (!($i <= $rowsSelect)) {
        }
        $id_siswa = $this->input->post('siswa[' . $i . ']', true);
        if (!($id_siswa != null)) {
        }
        $insert = ['id_kelas_siswa' => $id_tp . $id_smt . $id_siswa, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $id, 'id_siswa' => $id_siswa];
        $this->db->replace('kelas_siswa', $insert);
        $i++;
    }
    public function manage()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Copy Kelas', 'subjudul' => 'Copy Data Kelas ke SMT II', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, '1');
        $data['kelas2'] = $this->dropdown->getAllKelas($tp->id_tp, '2');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/kelas/persemester');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function getFromSmt1($kelas)
    {
        $tp = $this->dashboard->getTahunActive();
        $data1 = $this->kelas->getKelasSiswa($kelas, $tp->id_tp, '1');
        $data2 = $this->kelas->getKelasSiswa($kelas, $tp->id_tp, '2');
        $ids = [];
        if (!(count($data2) > 0)) {
            $this->output_json(['smt1' => $data1, 'smt2' => $ids]);
        } else {
            foreach ($data2 as $s) {
                $ids[] = $s->id_siswa;
            }
            $this->output_json(['smt1' => $data1, 'smt2' => $ids]);
        }
    }
    public function copyFromSmt1()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kelas1 = $this->input->post('kelas_lama', true);
        $kelas2 = $this->input->post('kelas_baru', true);
        $kelas = $this->kelas->get_one($kelas1, $tp->id_tp, '1');
        $data = array('nama_kelas' => $kelas2, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $kelas->jumlah_siswa);
        $this->db->insert('master_kelas', $data);
        $idk = $this->db->insert_id();
        $res = [];
        $arrSiswa = unserialize($kelas->jumlah_siswa);
        foreach ($arrSiswa as $value) {
            $id_siswa = $value['id'];
            if (!($id_siswa != null)) {
            } else {
                $insert = ['id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $id_siswa];
                $res[] = $this->db->replace('kelas_siswa', $insert);
            }
        }
        $this->output_json($res);
    }
    public function copySiswaFromSmt1()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $idkelases = [];
        $siswakelas = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->id_kelas;
            $siswakelas[$d->id_kelas][] = ['id' => $d->id_siswa];
        }
        $idkelases = array_unique($idkelases);
        $res = [];
        foreach ($idkelases as $ik) {
            if (!($ik != '')) {
            } else {
                $kelas = $this->kelas->get_one($ik, $tp->id_tp, '1');
                $jumlah = serialize($siswakelas[$ik]);
                $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $jumlah);
                $this->db->insert('master_kelas', $data);
                $idk = $this->db->insert_id();
                foreach ($siswakelas[$ik] as $s) {
                    $insert = ['id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $s['id'], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $s['id']];
                    $res[] = $this->db->replace('kelas_siswa', $insert);
                }
            }
        }
        $this->output_json($res);
    }
    public function kenaikan()
    {
        $kelas = $this->input->get('kelas', true);
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Kenaikkan Kelas', 'subjudul' => 'Naik Kelas Siswa', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $data['kelas_lama'] = $this->dropdown->getAllKelas($tp->id_tp - 1, '2', '!=' . $level);
        $data['kelas_baru'] = $this->dropdown->getAllKelas($tp->id_tp, '1');
        if (!($kelas != null)) {
            $this->load->view('_templates/dashboard/_header', $data);
        } else {
            $data['siswa_kelas_baru'] = $this->master->getSiswaKelasBaru($tp->id_tp, $smt->id_smt);
            $data['siswas'] = $this->rapor->getKenaikanSiswa($kelas, $tp->id_tp - 1, '2');
            $data['kelas_selected'] = $kelas;
            $lvlKls = $this->kelas->get_one($kelas, $tp->id_tp - 1, '2');
            $data['kelases'] = $this->dropdown->getAllKelas($tp->id_tp - 1, '2', '=' . ($lvlKls->level_id + 1));
            $this->load->view('_templates/dashboard/_header', $data);
        }
        $this->load->view('master/kelas/naikkelas');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function naikKelas()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $posts = json_decode($this->input->post('kelas', true));
        $mode = $this->input->post('mode', true);
        $idkelases = [];
        $siswakelas = [];
        foreach ($posts as $d) {
            $idkelases[] = $d->kelas_baru;
            $siswakelas[$d->kelas_baru][] = ['id' => $d->id_siswa];
        }
        $idkelases = array_unique($idkelases);
        $res = [];
        $idks = [];
        foreach ($idkelases as $ik) {
            $kelas = $this->kelas->get_one($ik, $tp->id_tp - 1, '2');
            $kelas_baru = $this->kelas->getKelasByNama($kelas->nama_kelas, $tp->id_tp, $smt->id_smt);
            if ($kelas_baru == null) {
                $jumlah = serialize($siswakelas[$ik]);
                $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $jumlah);
                $this->db->insert('master_kelas', $data);
                array_push($idks, $this->db->insert_id());
            } else {
                if ($mode == 'persiswa') {
                }
                $jumlah = serialize($siswakelas[$ik]);
                array_push($idks, $kelas_baru->id_kelas);
                $data = array('nama_kelas' => $kelas->nama_kelas, 'kode_kelas' => $kelas->kode_kelas, 'jurusan_id' => $kelas->jurusan_id, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'level_id' => $kelas->level_id, 'guru_id' => $kelas->guru_id, 'siswa_id' => $kelas->siswa_id, 'jumlah_siswa' => $jumlah);
                $this->db->where('id_kelas', $kelas_baru->id_kelas);
                $this->db->update('master_kelas', $data);
            }
            foreach ($idks as $idk) {
                foreach ($siswakelas[$ik] as $s) {
                    $insert = ['id_kelas_siswa' => $tp->id_tp . $smt->id_smt . $s['id'], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'id_kelas' => $idk, 'id_siswa' => $s['id']];
                    $res[] = $this->db->replace('kelas_siswa', $insert);
                }
            }
        }
        $data['res'] = $siswakelas;
        $this->output_json($data);
    }
    public function hapus($id_kelas)
    {
        $delete['siswa'] = $this->master->delete('kelas_siswa', $id_kelas, 'id_kelas');
        $delete['kelas'] = $this->master->delete('master_kelas', $id_kelas, 'id_kelas');
        $this->output_json($delete);
    }
}
```

---

## File: application/controllers_progress/Datamapel.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datamapel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->dbforge();
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
    private function updateUrutanTampil()
    {
        $mapels = $this->db->select('*')->from('master_mapel')->get()->result();
        $insert = [];
        foreach ($mapels as $mapel) {
            $insert = ['id_mapel' => $mapel->id_mapel, 'nama_mapel' => $mapel->id_mapel, 'kode' => $mapel->id_mapel, 'kelompok' => $mapel->id_mapel, 'bobot_p' => $mapel->id_mapel, 'bobot_k' => $mapel->id_mapel, 'jenjang' => $mapel->id_mapel, 'urutan' => $mapel->id_mapel, 'urutan_tampil' => $mapel->id_mapel, 'status' => $mapel->id_mapel, 'deletable' => $mapel->id_mapel];
        }
        if (!(count($insert) > 0)) {
        } else {
            $this->db->update_batch('master_mapel', $insert);
        }
    }
    public function index()
    {
        if ($this->db->field_exists('urutan_tampil', 'master_mapel')) {
            $user = $this->ion_auth->user()->row();
        } else {
            $fields = array('urutan_tampil' => array('type' => 'int(3)', 'after' => 'urutan'));
            $this->dbforge->add_column('master_mapel', $fields);
            $user = $this->ion_auth->user()->row();
        }
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Daftar Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $setting];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['kategori'] = ['WAJIB', 'PAI (Kemenag)', 'PEMINATAN AKADEMIK', 'AKADEMIK KEJURUAN', 'LINTAS MINAT', 'MULOK'];
        $data['kelompok_mapel'] = $this->master->getDataKelompokMapel();
        $data['sub_kelompok_mapel'] = $this->master->getDataSubKelompokMapel();
        $data['kelompok'] = $this->dropdown->getDataKelompokMapel();
        $data['status'] = ['Nonaktif', 'Aktif'];
        $data['mapel_non_aktif'] = $this->master->getAllMapelNonAktif($setting->jenjang);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/mapel/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function addKelompokMapel()
    {
        $id = $this->input->post('id_kel_mapel');
        $insert = ['nama_kel_mapel' => $this->input->post('nama_kel_mapel', true), 'kode_kel_mapel' => $this->input->post('kode_kel_mapel', true), 'kategori' => $this->input->post('kategori', true), 'id_parent' => $this->input->post('id_parent', true)];
        if ($id != null) {
            $this->db->where('id_kel_mapel', $id);
            $data = $this->db->update('master_kelompok_mapel', $insert);
        } else {
            $data = $this->master->create('master_kelompok_mapel', $insert);
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function hapusKelompok()
    {
        $id = $this->input->post('id_kel');
        $kode = $this->input->post('kode');
        $id_parent = $this->input->post('id_parent');
        $messages = [];
        $this->db->where_in('kelompok', $kode);
        $numm = $this->db->count_all_results('master_mapel');
        if (!($numm > 0)) {
            $this->db->where_in('id_parent', $id);
        } else {
            array_push($messages, 'Mata Pelajaran');
            $this->db->where_in('id_parent', $id);
        }
        $nums = $this->db->count_all_results('master_kelompok_mapel');
        if (!($nums > 0)) {
        }
        array_push($messages, 'Sub Kelompok');
        if (count($messages) > 0) {
        }
        if (!$this->master->delete('master_kelompok_mapel', $id, 'id_kel_mapel')) {
        }
        $this->output_json(['status' => true, 'message' => 'berhasil']);
    }
    public function create()
    {
        $setting = $this->dashboard->getSetting();
        $insert = ['nama_mapel' => $this->input->post('nama_mapel', true), 'kode' => $this->input->post('kode_mapel', true), 'kelompok' => $this->input->post('kelompok', true), 'urutan_tampil' => $this->input->post('urutan_tampil', true), 'jenjang' => $setting->jenjang];
        $data = $this->master->create('master_mapel', $insert);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function getDataKelompok()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_kelompok_mapel');
        $this->datatables->where('id_parent', '0');
        $this->db->order_by('kode_kel_mapel');
        echo $this->datatables->generate();
    }
    public function getDataSubKelompok()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_kelompok_mapel');
        $this->datatables->where('id_parent <> 0');
        $this->db->order_by('kode_kel_mapel');
        echo $this->datatables->generate();
    }
    public function read()
    {
        $setting = $this->dashboard->getSetting();
        $this->datatables->select('id_mapel, urutan_tampil, nama_mapel, kode, kelompok, deletable, status');
        $this->datatables->from('master_mapel');
        $this->db->order_by('kelompok');
        $this->db->order_by('urutan_tampil');
        echo $this->datatables->generate();
    }
    public function update()
    {
        $data = $this->master->updateMapel();
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function aktifkan($id)
    {
        $this->db->set('status', '1');
        $this->db->where('id_mapel', $id);
        $update = $this->db->update('master_mapel');
        $this->output_json($update);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false, 'total' => 'Tidak ada data yang dipilih!']);
        } else {
            $messages = [];
            $tables = [];
            $tabless = $this->db->list_tables();
            foreach ($tabless as $table) {
                $fields = $this->db->field_data($table);
                foreach ($fields as $field) {
                    if (!($field->name == 'id_mapel' || $field->name == 'mapel_id')) {
                    } else {
                        array_push($tables, $table);
                    }
                }
            }
            foreach ($tables as $table) {
                if (!($table != 'master_mapel')) {
                } else {
                    if ($table == 'cbt_soal') {
                    }
                    $this->db->where_in('id_mapel', $chk);
                    $num = $this->db->count_all_results($table);
                    if (!($num > 0)) {
                    }
                    array_push($messages, $table);
                }
            }
            if (count($messages) > 0) {
            }
            if (!$this->master->delete('master_mapel', $chk, 'id_mapel')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
    public function import($import_data = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Mata Pelajaran', 'subjudul' => 'Import Mata Pelajaran', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if (!($import_data != null)) {
            $data['tp'] = $this->dashboard->getTahun();
        } else {
            $data['import'] = $import_data;
            $data['tp'] = $this->dashboard->getTahun();
        }
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/mapel/import');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function do_import()
    {
        $inputs = $this->input->post('mapel', true);
        $save = $this->master->create('master_mapel', $inputs, true);
        $this->output->set_content_type('application/json')->set_output($save);
    }
}
```

---

## File: application/controllers_progress/Datasiswa.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datasiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
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
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Data Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahun();
        $smt = $this->dashboard->getSemester();
        $data['tp'] = $tp;
        $data['smt'] = $smt;
        $searchTp = array_search('1', array_column($tp, 'active'));
        $searchSmt = array_search('1', array_column($smt, 'active'));
        $tpAktif = $tp[$searchTp];
        $smtAktif = $smt[$searchSmt];
        $data['tp_active'] = $tpAktif;
        $data['smt_active'] = $smtAktif;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['kelass'] = $this->dropdown->getAllKelas($tpAktif->id_tp, $smtAktif->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/siswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->master->getDataSiswa($tp->id_tp, $smt->id_smt), false);
    }
    public function list()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $page = $this->input->post('page', true);
        $limit = $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $filter = $this->input->post('filter', true);
        $offset = ($page - 1) * $limit;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $count_siswa = $this->master->getSiswaTotalPage($tp->id_tp, $smt->id_smt, $filter, $search);
        $lists = $this->master->getSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $filter, $search);
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit, 'filter' => $filter];
        $this->output_json($data);
    }
    public function add()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Tambah Data Siswa', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tipe'] = 'add';
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/siswa/add');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function create()
    {
        $this->load->model('Master_model', 'master');
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $username = $this->input->post('username', true);
        $u_nis = '|is_unique[master_siswa.nis]';
        $u_nisn = '|is_unique[master_siswa.nisn]';
        $u_name = '|is_unique[master_siswa.username]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]' . $u_nisn);
        $this->form_validation->set_rules('username', 'Username', 'required|trim' . $u_name);
        if ($this->form_validation->run() == FALSE) {
            $data['insert'] = false;
            $data['text'] = 'Data Sudah ada, Pastikan NIS, NISN dan Username belum digunakan siswa lain';
        } else {
            $insert = ['nama' => $this->input->post('nama_siswa', true), 'nis' => $nis, 'nisn' => $nisn, 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'username' => $username, 'password' => $this->input->post('password', true), 'foto' => 'uploads/foto_siswa/' . $nis . 'jpg'];
            $this->db->set('uid', 'UUID()', FALSE);
            $data['insert'] = $this->db->insert('master_siswa', $insert);
            $id = $this->db->insert_id();
            $siswa = $this->master->getSiswaById($id);
            $induk = ['id_siswa' => $id, 'uid' => $siswa->uid, 'status' => 1];
            $this->db->insert('buku_induk', $induk);
            $data['text'] = 'Siswa berhasil ditambahkan';
        }
        $this->output_json($data);
    }
    public function edit($id)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $siswa = $this->master->getSiswaById($id);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $siswa->nama, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $siswa->nis, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $siswa->nisn, 'icon' => 'far fa-id-card', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $siswa->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $siswa->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $siswa->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'class' => 'tahun', 'type' => 'text'], ['name' => 'sekolah_asal', 'label' => 'Sekolah Asal', 'value' => $siswa->sekolah_asal, 'icon' => 'fas fa-graduation-cap', 'class' => '', 'type' => 'text'], ['name' => 'status', 'label' => 'Status', 'value' => $siswa->status, 'icon' => 'far fa-user', 'class' => 'status', 'type' => 'text']];
        $inputBio = [['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $siswa->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $siswa->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $siswa->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $siswa->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $siswa->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $siswa->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $siswa->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $siswa->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $siswa->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $siswa->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $siswa->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputOrtu = [['name' => 'status_keluarga', 'label' => 'Status Keluarga', 'value' => $siswa->status_keluarga, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'anak_ke', 'label' => 'Anak ke', 'value' => $siswa->anak_ke, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $siswa->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $siswa->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $siswa->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $siswa->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $siswa->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $siswa->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $siswa->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $siswa->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number']];
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $siswa->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $siswa->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $siswa->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $siswa->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Edit Data Siswa', 'siswa' => $siswa, 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        if ($this->ion_auth->is_admin()) {
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('master/siswa/edit');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('master/siswa/edit');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function updateData()
    {
        $this->load->model('Master_model', 'master');
        $id_siswa = $this->input->post('id_siswa', true);
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $siswa = $this->master->getSiswaById($id_siswa);
        $u_nis = $siswa->nis === $nis ? '' : '|is_unique[master_siswa.nis]';
        $u_nisn = $siswa->nisn === $nisn ? '' : '|is_unique[master_siswa.nisn]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        if ($this->form_validation->run() == FALSE) {
            $data['insert'] = false;
            $data['text'] = 'NIS kurang dari 6 angka, atau data Sudah ada, Pastikan NIS, dan NISN belum digunakan siswa lain';
        } else {
            $tgl_lahir = $this->input->post('tanggal_lahir', true);
            $tgl_masuk = $this->input->post('tahun_masuk', true);
            $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->strContains($tgl_lahir, '0000-') ? null : $tgl_lahir, 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->strContains($tgl_masuk, '0000-') ? null : $tgl_masuk, 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => $siswa->foto != null && $siswa->foto != '' ? $siswa->foto : 'uploads/foto_siswa/' . $nis . '.jpg'];
            $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
            $this->db->set('status', $this->input->post('status', true));
            $this->db->where('id_siswa', $siswa->id_siswa);
            $this->db->update('buku_induk');
            $data['insert'] = $input;
            $data['text'] = 'Siswa berhasil diperbaharui';
        }
        $this->output_json($data);
    }
    function strContains($string, $val)
    {
        return strpos($string, $val) !== false;
    }
    function uploadFile($id_siswa)
    {
        $this->load->model('Master_model', 'master');
        $siswa = $this->master->getSiswaById($id_siswa);
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/foto_siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = $siswa->nis;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile($id_siswa)
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!($file_name != 'assets/img/siswa.png')) {
        } else {
            if (!unlink($file_name)) {
            }
            $this->db->set('foto', '');
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            echo 'File Delete Successfully';
        }
    }
    public function delete()
    {
        $this->load->model('Master_model', 'master');
        $chk = $this->input->post('checked', true);
        $aksi = $this->input->post('aksi', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            $last = $aksi;
            if ($aksi == 'pindah') {
            }
            if ($aksi == 'keluar') {
            }
            if ($aksi == 'hapus') {
            }
            $this->output_json(['status' => true, 'total' => count($chk), 'last' => $last]);
        }
    }
    public function do_import()
    {
        $input = $this->input->post('siswa', true);
        $errors = [];
        $duplikat = [];
        foreach ($input as $value) {
            $data = ['nisn' => $value['2'] ?? '', 'nis' => $value['3'] ?? '', 'nama' => $value['4'] ?? '', 'username' => $value['6'] ?? '', 'password' => $value['7'] ?? ''];
            $this->form_validation->set_data($data);
            $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]|is_unique[master_siswa.nis]');
            $this->form_validation->set_rules('nisn', 'NISN', 'required|numeric|trim|min_length[6]|max_length[20]|is_unique[master_siswa.nisn]');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[master_siswa.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|trim|is_unique[master_siswa.username]');
            if (!($this->form_validation->run() == FALSE)) {
            } else {
                $duplikat[] = $data;
                $errors[$data['nama']] = ['nama' => form_error('nama'), 'nis' => form_error('nis'), 'nisn' => form_error('nisn'), 'username' => form_error('username'), 'password' => form_error('password')];
            }
        }
        if (count($errors) > 0) {
            $data = ['status' => false, 'errors' => $errors, 'duplikat' => $duplikat];
        } else {
            $this->db->trans_start();
            foreach ($input as $value) {
                $siswa = ['nisn' => $value['2'] ?? '', 'nis' => $value['3'] ?? '', 'nama' => $value['4'] ?? '', 'jenis_kelamin' => $value['5'] ?? '', 'username' => $value['6'] ?? '', 'password' => $value['7'] ?? '', 'kelas_awal' => $value['8'] ?? '', 'tahun_masuk' => $value['9'] ?? '', 'sekolah_asal' => $value['10'] ?? '', 'tempat_lahir' => $value['11'] ?? '', 'tanggal_lahir' => $value['12'] ?? '', 'agama' => $value['13'] ?? '', 'hp' => $value['14'] ?? '0', 'email' => $value['15'] ?? '', 'anak_ke' => $value['16'] ?? '1', 'status_keluarga' => $value['17'] ?? '1', 'alamat' => $value['18'] ?? '', 'rt' => $value['19'] ?? '', 'rw' => $value['20'] ?? '', 'kelurahan' => $value['21'] ?? '', 'kecamatan' => $value['22'] ?? '', 'kabupaten' => $value['23'] ?? '', 'provinsi' => $value['24'] ?? '', 'kode_pos' => $value['25'] ?? '', 'nama_ayah' => $value['26'] ?? '', 'tgl_lahir_ayah' => $value['27'] ?? '', 'pendidikan_ayah' => $value['28'] ?? '', 'pekerjaan_ayah' => $value['29'] ?? '', 'nohp_ayah' => $value['30'] ?? '', 'alamat_ayah' => $value['31'] ?? '', 'nama_ibu' => $value['32'] ?? '', 'tgl_lahir_ibu' => $value['33'] ?? '', 'pendidikan_ibu' => $value['34'] ?? '', 'pekerjaan_ibu' => $value['35'] ?? '', 'nohp_ibu' => $value['36'] ?? '', 'alamat_ibu' => $value['37'] ?? '', 'nama_wali' => $value['38'] ?? '', 'tgl_lahir_wali' => $value['39'] ?? '', 'pendidikan_wali' => $value['40'] ?? '', 'pekerjaan_wali' => $value['41'] ?? '', 'nohp_wali' => $value['42'] ?? '', 'alamat_wali' => $value['43'] ?? ''];
                $siswa['foto'] = 'uploads/foto_siswa/' . $siswa['nis'] . '.jpg';
                $this->db->set('uid', 'UUID()', FALSE);
                $save = $this->db->insert('master_siswa', $siswa);
            }
            $uids = $this->db->select('id_siswa, uid')->from('master_siswa')->get()->result();
            foreach ($uids as $uid) {
                $check = $this->db->select('id_siswa')->from('buku_induk')->where('id_siswa', $uid->id_siswa);
                if (!($check->get()->num_rows() == 0)) {
                } else {
                    $this->db->insert('buku_induk', $uid);
                }
            }
            $this->db->trans_complete();
            $data = ['status' => true, 'errors' => []];
        }
        $this->output_json($data);
    }
    public function update()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Update Data Siswa', 'subjudul' => 'Update Data Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp_active'] = $tp;
        $data['smt_active'] = $smt;
        $data['tp'] = $this->dashboard->getTahun();
        $data['smt'] = $this->dashboard->getSemester();
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['tipe'] = 'update';
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/siswa/update');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function downloadData($id_kelas)
    {
        $this->load->model('Master_model', 'master');
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $siswas = $this->master->getSiswaByKelas($tp->id_tp, $smt->id_smt, $id_kelas);
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
        }
        $this->output_json(['status' => true, 'siswa' => $siswas]);
    }
    public function updateAll()
    {
        $input = $this->input->post('siswa', true);
        $this->db->trans_start();
        foreach ($input as $value) {
            $siswa = ['nisn' => $value['2'] ?? '', 'nis' => $value['3'] ?? '', 'nama' => $value['4'] ?? '', 'jenis_kelamin' => $value['5'] ?? '', 'username' => $value['6'] ?? '', 'password' => $value['7'] ?? '', 'kelas_awal' => $value['8'] ?? '', 'tahun_masuk' => $value['9'] ?? '', 'sekolah_asal' => $value['10'] ?? '', 'tempat_lahir' => $value['11'] ?? '', 'tanggal_lahir' => $value['12'] ?? '', 'agama' => $value['13'] ?? '', 'hp' => $value['14'] ?? '0', 'email' => $value['15'] ?? '', 'anak_ke' => $value['16'] ?? '1', 'status_keluarga' => $value['17'] ?? '1', 'alamat' => $value['18'] ?? '', 'rt' => $value['19'] ?? '', 'rw' => $value['20'] ?? '', 'kelurahan' => $value['21'] ?? '', 'kecamatan' => $value['22'] ?? '', 'kabupaten' => $value['23'] ?? '', 'provinsi' => $value['24'] ?? '', 'kode_pos' => $value['25'] ?? '', 'nama_ayah' => $value['26'] ?? '', 'tgl_lahir_ayah' => $value['27'] ?? '', 'pendidikan_ayah' => $value['28'] ?? '', 'pekerjaan_ayah' => $value['29'] ?? '', 'nohp_ayah' => $value['30'] ?? '', 'alamat_ayah' => $value['31'] ?? '', 'nama_ibu' => $value['32'] ?? '', 'tgl_lahir_ibu' => $value['33'] ?? '', 'pendidikan_ibu' => $value['34'] ?? '', 'pekerjaan_ibu' => $value['35'] ?? '', 'nohp_ibu' => $value['36'] ?? '', 'alamat_ibu' => $value['37'] ?? '', 'nama_wali' => $value['38'] ?? '', 'tgl_lahir_wali' => $value['39'] ?? '', 'pendidikan_wali' => $value['40'] ?? '', 'pekerjaan_wali' => $value['41'] ?? '', 'nohp_wali' => $value['42'] ?? '', 'alamat_wali' => $value['43'] ?? ''];
            $siswa['foto'] = 'uploads/foto_siswa/' . $value['3'] . '.jpg';
            $save = $this->db->update('master_siswa', $siswa, array('id_siswa' => $value['44']));
        }
        $this->db->trans_complete();
        $data = ['status' => $save ?? false, 'errors' => []];
        $this->output_json($data);
    }
    public function update_foto()
    {
        $input = $this->input->post('siswa', true);
        $errors = [];
        $duplikat = [];
        foreach ($input as $value) {
            $this->form_validation->set_data($value);
            $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]');
            if (!($this->form_validation->run() == FALSE)) {
            } else {
                $duplikat[] = $value;
                $errors[$value['nama']] = ['nis' => form_error('nis')];
            }
        }
        if (count($errors) > 0) {
            $data = ['status' => false, 'errors' => $errors, 'duplikat' => $duplikat];
        } else {
            $this->db->trans_start();
            foreach ($input as $value) {
                $foto = 'uploads/foto_siswa/' . trim($value['nis'] ?? '00') . '.jpg';
                if (!isset($value['foto'])) {
                    $siswa = ['nis' => $value['nis'] ?? '', 'foto' => $foto];
                } else {
                    $base64_image_string = $value['foto'];
                    $extension = $value['ext'];
                    if (!($extension == 'jpeg')) {
                    }
                    $extension = 'jpg';
                    $output_file = trim($value['nis'] ?? '00') . '.' . $extension;
                    file_put_contents('./uploads/foto_siswa/' . $output_file, base64_decode($base64_image_string));
                    $foto = 'uploads/foto_siswa/' . $output_file;
                    $siswa = ['nis' => $value['nis'] ?? '', 'foto' => $foto];
                }
                $save = $this->db->update('master_siswa', $siswa, array('id_siswa' => $value['id']));
            }
            $this->db->trans_complete();
            $data = ['status' => true, 'errors' => []];
        }
        $this->output_json($data);
    }
    public function updateNisByNisn()
    {
        $input = json_decode($this->input->post('siswa', true));
        foreach ($input as $val) {
            $this->db->set('nis', trim($val->nis ?? ''));
            $this->db->where('nisn', trim($val->nisn ?? ''));
            $save = $this->db->update('master_siswa');
        }
        $this->db->trans_complete();
        $this->output_json($save);
    }
    public function editLogin()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $id_siswa = $this->input->post('id_siswa', true);
        $username = $this->input->post('username', true);
        $pass = $this->input->post('new', true);
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $siswa_lain = $this->dashboard->getDataSiswa($username, $tp->id_tp, $smt->id_smt);
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($siswa_lain && $siswa_lain->id_siswa != $id_siswa) {
            $data = ['status' => false, 'errors' => ['username' => 'Username sudah digunakan']];
        } else {
            if ($this->form_validation->run() === FALSE) {
            }
            $siswa = $this->db->get_where('master_siswa', 'id_siswa="' . $id_siswa . '"')->row();
            $nama = explode(' ', $siswa->nama ?? '');
            $first_name = $nama[0];
            $last_name = end($nama);
            $username = trim($username ?? '');
            $password = trim($pass ?? '');
            $email = $siswa->nis . '@siswa.com';
            $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
            $group = array('3');
            $user_siswa = $this->db->get_where('users', 'email="' . $email . '"')->row();
            $deleted = true;
            if (!($user_siswa != null)) {
            }
            $deleted = $this->ion_auth->delete_user($user_siswa->id);
            if ($deleted) {
            }
            $status = false;
            $msg = 'Gagal mengganti username/passsword.';
            $data['status'] = $status;
            $data['text'] = $msg;
        }
        $this->output_json($data);
    }
    private function registerSiswa($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['status'] = true;
        $data['id'] = $reg;
        if (!($reg == false)) {
            return $data;
        } else {
            $data['status'] = false;
            return $data;
        }
    }
}
```

---

## File: application/controllers_progress/Datatahun.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Datatahun extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model', 'logging');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Tahun Pelajaran dan Semester', 'subjudul' => 'Atur Tahun Pelajaran dan Semester', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $jml = $this->master->getJmlHariEfektif($tp->id_tp . $smt->id_smt);
        $data['jml_hari'] = $jml == null ? '0' : $jml->jml_hari_efektif;
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('master/tahun/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function data()
    {
        $this->output_json($this->dashboard->getDataTahun(), false);
    }
    public function gantiTahun()
    {
        $aktif = $this->input->post('active', true);
        $inputTp = json_decode($this->input->post('tahun', false));
        foreach ($inputTp as $tps) {
            $id_tp = $tps->id;
            $tahun = $tps->tp;
            if ($id_tp === $aktif) {
                $active = 1;
            } else {
                $active = 0;
            }
            $update[] = array('id_tp' => $id_tp, 'tahun' => $tahun, 'active' => $active);
        }
        $this->dashboard->update('master_tp', $update, 'id_tp', null, true);
        $data['msg'] = 'Merubah Tahun Aktif';
        $data['update'] = $update;
        $data['status'] = true;
        $this->logging->saveLog(4, 'mengganti tahun ajaran aktif');
        $this->output_json($data);
    }
    public function gantiSemester()
    {
        $aktif = $this->input->post('active', true);
        $inputSmt = json_decode($this->input->post('semester', false));
        foreach ($inputSmt as $tps) {
            $id_smt = $tps->id;
            $smt = $tps->Semester;
            if ($id_smt === $aktif) {
                $active = 1;
            } else {
                $active = 0;
            }
            $update[] = array('id_smt' => $id_smt, 'smt' => $smt, 'active' => $active);
        }
        $this->dashboard->update('master_smt', $update, 'id_smt', null, true);
        $data['msg'] = 'Merubah Semester Aktif';
        $data['update'] = $update;
        $data['status'] = true;
        $this->logging->saveLog(4, 'mengganti semester aktif');
        $this->output_json($data);
    }
    public function add()
    {
        $method = $this->input->post('method', true);
        $tahun = $this->input->post('tahun', true);
        if ($method === 'add') {
            $insert = ['tahun' => $tahun];
            $data = $this->master->create('master_tp', $insert);
            $this->logging->saveLog(3, 'menambah tahun pelajaran');
        } else {
            $id = $this->input->post('id_tahun', true);
            $update = array('id_tp' => $id, 'tahun' => $tahun);
            $data = $this->master->update('master_tp', $update, 'id_tp', $id);
            $this->logging->saveLog(4, 'mengedit tahun pelajaran');
        }
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function saveHariEfektif()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $input = ['id_hari_efektif' => $tp->id_tp . $smt->id_smt, 'jml_hari_efektif' => $this->input->post('jml_hari', true)];
        $update = $this->db->replace('master_hari_efektif', $input);
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function hapusTahun()
    {
        $id = $this->input->post('hapus', true);
        if ($this->dashboard->hapus('master_tp', $id, 'id_tp')) {
            $this->logging->saveLog(5, 'menghapus tahun pelajaran');
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }
        $data['msg'] = 'Menghapus Tahun Pelajaran';
        $this->output_json($data);
    }
    public function hapus()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->dashboard->hapus('master_tp', $chk, 'id_tp')) {
            }
            $this->logging->saveLog(5, 'menghapus tahun pelajaran');
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
}
```

---

## File: application/controllers_progress/Dbclear.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dbclear extends CI_Controller
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
        $this->load->dbforge();
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
                if (in_array($table, $excludes)) {
                }
                $name = str_replace('_', ' ', $table ?? '');
                $table_info = ['ket' => $this->keterangan()[$table], 'size' => $this->settings->rowSize($table), 'table' => $table, 'name' => ucwords($name)];
                $data_tables[$table_info['ket']][] = $table_info;
            } else {
                if (in_array($table, $excludes)) {
                }
                if ($table == 'buku_nilai') {
                }
                $this->dbforge->drop_table($table, true);
            }
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
```

---

## File: application/controllers_progress/Dbmanager.php

```php
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
```

---

## File: application/controllers_progress/Guruview.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Guruview extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library('upload');
        } else {
            redirect('auth');
            $this->load->library('upload');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Master_model', 'master');
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
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDetailGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        if (!($user == null)) {
            $data = ['user' => $user, 'judul' => 'Profile', 'subjudul' => 'Profile Saya', 'setting' => $this->dashboard->getSetting()];
        } else {
            redirect('auth');
            $data = ['user' => $user, 'judul' => 'Profile', 'subjudul' => 'Profile Saya', 'setting' => $this->dashboard->getSetting()];
        }
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['guru'] = $guru;
        $inputsProfile = [['label' => 'Nama Lengkap', 'name' => 'nama_guru', 'value' => $guru->nama_guru, 'icon' => 'far fa-user', 'type' => 'text'], ['label' => 'Email', 'name' => 'email', 'value' => $guru->email, 'icon' => 'far fa-envelope', 'type' => 'text'], ['label' => 'NIP / NUPTK', 'name' => 'nip', 'value' => $guru->nip, 'icon' => 'far fa-id-card', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $guru->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'type' => 'text'], ['label' => 'No. Handphone', 'name' => 'no_hp', 'value' => $guru->no_hp, 'icon' => 'fa fa-phone', 'type' => 'number'], ['label' => 'Agama', 'name' => 'agama', 'value' => $guru->agama, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputsAlamat = [['label' => 'NIK', 'name' => 'no_ktp', 'value' => $guru->no_ktp, 'icon' => 'far fa-id-card', 'type' => 'number'], ['label' => 'Tempat Lahir', 'name' => 'tempat_lahir', 'value' => $guru->tempat_lahir, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Tgl. Lahir', 'name' => 'tgl_lahir', 'value' => $guru->tgl_lahir, 'icon' => 'fa fa-calendar', 'type' => 'text'], ['label' => 'Alamat', 'name' => 'alamat_jalan', 'value' => $guru->alamat_jalan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kecamatan', 'name' => 'kecamatan', 'value' => $guru->kecamatan, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kota/Kab.', 'name' => 'kabupaten', 'value' => $guru->kabupaten, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Provinsi', 'name' => 'provinsi', 'value' => $guru->provinsi, 'icon' => 'fa fa-map-marker', 'type' => 'text'], ['label' => 'Kode Pos', 'name' => 'kode_pos', 'value' => $guru->kode_pos, 'icon' => 'fa fa-envelope', 'type' => 'number']];
        $data['input_profile'] = json_decode(json_encode($inputsProfile), FALSE);
        $data['input_alamat'] = json_decode(json_encode($inputsAlamat), FALSE);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/profile');
        $this->load->view('members/guru/templates/footer');
    }
    public function save()
    {
        $id_guru = $this->input->post('id_guru', true);
        $nip = $this->input->post('nip', true);
        $nama_guru = $this->input->post('nama_guru', true);
        $email = $this->input->post('email', true);
        $jenis_kelamin = $this->input->post('jenis_kelamin', true);
        $no_hp = $this->input->post('no_hp', true);
        $agama = $this->input->post('agama', true);
        $no_ktp = $this->input->post('no_ktp', true);
        $tempat_lahir = $this->input->post('tempat_lahir', true);
        $tgl_lahir = $this->input->post('tgl_lahir', true);
        $alamat_jalan = $this->input->post('alamat_jalan', true);
        $kecamatan = $this->input->post('kecamatan', true);
        $kabupaten = $this->input->post('kabupaten', true);
        $provinsi = $this->input->post('provinsi', true);
        $kode_pos = $this->input->post('kode_pos', true);
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $dbdata = $this->master->getGuruById($id_guru, $tp->id_tp, $smt->id_smt);
        $u_nip = $dbdata->nip === $nip ? '' : '|is_unique[master_guru.nip]';
        $this->form_validation->set_rules('nip', 'NIP', 'required|trim|min_length[8]|max_length[30]' . $u_nip);
        $this->form_validation->set_rules('nama_guru', 'Nama Guru', 'required|trim|min_length[1]|max_length[50]');
        if ($this->form_validation->run() == FALSE) {
            $data = ['status' => false, 'errors' => ['nip' => form_error('nip'), 'nama_guru' => form_error('nama_guru')]];
            $this->output_json($data);
        } else {
            $input = ['nip' => $nip, 'nama_guru' => $nama_guru, 'email' => $email, 'jenis_kelamin' => $jenis_kelamin, 'no_hp' => $no_hp, 'agama' => $agama, 'no_ktp' => $no_ktp, 'tempat_lahir' => $tempat_lahir, 'tgl_lahir' => $this->strContains($tgl_lahir, '0000-') ? null : $tgl_lahir, 'alamat_jalan' => $alamat_jalan, 'kecamatan' => $kecamatan, 'kabupaten' => $kabupaten, 'provinsi' => $provinsi, 'kode_pos' => $kode_pos];
            $action = $this->master->update('master_guru', $input, 'id_guru', $id_guru);
            if ($action) {
            }
            $this->output_json(['status' => false]);
        }
    }
    function strContains($string, $val)
    {
        return strpos($string, $val) !== false;
    }
    function uploadFile($id_guru)
    {
        $guru = $this->master->getGuruById($id_guru);
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/profiles/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = $guru->nip;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/profiles/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $this->db->set('foto', 'uploads/profiles/' . $result['file_name']);
            $this->db->where('id_guru', $id_guru);
            $this->db->update('master_guru');
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile($id_guru)
    {
        $src = $this->input->get('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!($file_name != 'user.jpg')) {
        } else {
            if (!unlink($file_name)) {
            }
            $this->db->set('foto', '');
            $this->db->where('id_guru', $id_guru);
            $this->db->update('master_guru');
            echo 'File Delete Successfully';
        }
    }
}
```

---

## File: application/controllers_progress/Hasilujian.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class HasilUjian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables']);
        } else {
            redirect('auth');
            $this->load->library(['datatables']);
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Ujian_model', 'ujian');
        $this->user = $this->ion_auth->user()->row();
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
    public function data()
    {
        $nip_guru = null;
        if (!$this->ion_auth->in_group('guru')) {
            $this->output_json($this->ujian->getHasilUjian($nip_guru), false);
        } else {
            $nip_guru = $this->user->username;
            $this->output_json($this->ujian->getHasilUjian($nip_guru), false);
        }
    }
    public function NilaiMhs($id)
    {
        $this->output_json($this->ujian->HslUjianById($id, true), false);
    }
    public function index()
    {
        $data = ['user' => $this->user, 'judul' => 'Ujian', 'subjudul' => 'Hasil Ujian'];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $nilai = $this->ujian->bandingNilai($id);
        $data = ['user' => $this->user, 'judul' => 'Ujian', 'subjudul' => 'Detail Hasil Ujian', 'ujian' => $ujian, 'nilai' => $nilai];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/detail_hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function cetak($id)
    {
        $mhs = $this->ujian->getIdMahasiswa($this->user->username);
        $hasil = $this->ujian->HslUjian($id, $mhs->id_siswa)->row();
        $ujian = $this->ujian->getUjianById($id);
        $data = ['ujian' => $ujian, 'hasil' => $hasil, 'mhs' => $mhs];
        $this->load->view('ujian/cetak', $data);
    }
    public function cetak_detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $nilai = $this->ujian->bandingNilai($id);
        $hasil = $this->ujian->HslUjianById($id)->result();
        $data = ['ujian' => $ujian, 'nilai' => $nilai, 'hasil' => $hasil];
        $this->load->view('ujian/cetak_detail', $data);
    }
}
```

---

## File: application/controllers_progress/Install.php

```php
<?php

/*   ________________________________________
    |                 GarudaCBT              |
    |    https://github.com/garudacbt/cbt    |
    |________________________________________|
*/
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
```

---

## File: application/controllers_progress/Jurusanmapel.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class JurusanMapel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Jurusan Mata Kuliah', 'subjudul' => 'Data Jurusan Mata Kuliah'];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/data');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function data()
    {
        $this->output_json($this->master->getJurusanMapel(), false);
    }
    public function getJurusanId($id)
    {
        $this->output_json($this->master->getAllJurusan($id));
    }
    public function add()
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Tambah Jurusan Mata Kuliah', 'subjudul' => 'Tambah Data Jurusan Mata Kuliah', 'mapel' => $this->master->getMapel()];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/add');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function edit($id)
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Edit Jurusan Mata Kuliah', 'subjudul' => 'Edit Data Jurusan Mata Kuliah', 'mapel' => $this->master->getMapelById($id, true), 'id_mapel' => $id, 'all_jurusan' => $this->master->getAllJurusan(), 'jurusan' => $this->master->getJurusanByIdMapel($id)];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('relasi/jurusanmapel/edit');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function save()
    {
        $method = $this->input->post('method', true);
        $this->form_validation->set_rules('mapel_id', 'Mata Kuliah', 'required');
        $this->form_validation->set_rules('jurusan_id[]', 'Jurusan', 'required');
        if ($this->form_validation->run() == FALSE) {
            $data = ['status' => false, 'errors' => ['mapel_id' => form_error('mapel_id'), 'jurusan_id[]' => form_error('jurusan_id[]')]];
            $this->output_json($data);
        } else {
            $mapel_id = $this->input->post('mapel_id', true);
            $jurusan_id = $this->input->post('jurusan_id', true);
            $input = [];
            foreach ($jurusan_id as $key => $val) {
                $input[] = ['mapel_id' => $mapel_id, 'jurusan_id' => $val];
            }
            if ($method === 'add') {
            }
            if (!($method === 'edit')) {
            }
            $id = $this->input->post('mapel_id', true);
            $this->master->delete('jurusan_mapel', $id, 'mapel_id');
            $action = $this->master->create('jurusan_mapel', $input, true);
            $data['status'] = $action ? TRUE : FALSE;
        }
        $this->output_json($data);
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('jurusan_mapel', $chk, 'mapel_id')) {
            }
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
}
```

---

## File: application/controllers_progress/Kelasabsensibulanan.php

```php
<?php

class Kelasabsensibulanan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Dibatasi');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Daftar Hadir Bulanan', 'subjudul' => 'Daftar Hadir Bulanan Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['bulan'] = $this->dropdown->getBulan();
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['guru'] = $this->dropdown->getAllGuru();
            $data['mapel'] = $this->dropdown->getAllMapel();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/absenbulanan/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $nguru[$guru->id_guru] = $guru->nama_guru;
            $data['guru'] = $guru;
            $data['id_guru'] = $guru->id_guru;
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
            $arrMapel = [];
            $arrKelas = [];
            if (!($mapel != null)) {
            }
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                foreach ($m->kelas_mapel as $kls) {
                    $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                }
            }
            $arrId = [];
            if (!($mapel != null)) {
            }
            foreach ($mapel[0]->kelas_mapel as $id_mapel) {
                array_push($arrId, $id_mapel->kelas);
            }
            $data['mapel'] = $arrMapel;
            $data['arrkelas'] = $arrKelas;
            $data['kelas'] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/absenbulanan/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function loadAbsensiMapel()
    {
        $id_kelas = $this->input->post('kelas', true);
        $id_mapel = $this->input->post('mapel', true);
        $tahun = $this->input->post('thn', true);
        $bulan = $this->input->post('bln', true);
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $jadwal = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        if ($jadwal != null) {
            $jadwal->istirahat = unserialize($jadwal->istirahat);
            $tgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $jadwal_materi = [];
            $i = 0;
            if (!($i < $tgl)) {
            }
            $t = $i + 1 < 10 ? '0' . ($i + 1) : $i + 1;
            $b = $bulan < 10 ? '0' . $bulan : $bulan;
            $jadwal_materi[$t] = (array) $this->kelas->getAllMateriByTgl($id_kelas, $tahun . '-' . $b . '-' . $t, [$id_mapel]);
            $i++;
        } else {
            $this->output_json(['jadwal' => $jadwal]);
        }
    }
    function total_hari($id_day, $bulan, $taun)
    {
        $days = 0;
        $dates = [];
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $idday = $id_day == '7' ? 0 : $id_day;
        $i = 1;
        if (!($i < $total_days)) {
            return $dates;
        } else {
            if (!(date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday)) {
            }
            $days++;
            array_push($dates, date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i)));
            $i++;
            if (!($i < $total_days)) {
            }
        }
    }
}
```

---

## File: application/controllers_progress/Kelasabsensiharian.php

```php
<?php

class Kelasabsensiharian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Kehadiran Harian Siswa', 'subjudul' => 'Data Kehadiran Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['mapel'] = $this->dropdown->getAllMapel();
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['guru'] = $this->dropdown->getAllGuru();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/absenharian/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $nguru[$guru->id_guru] = $guru->nama_guru;
            $data['guru'] = $guru;
            $data['id_guru'] = $guru->id_guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/absenharian/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function loadAbsensi()
    {
        $id_kelas = $this->input->post('kelas', true);
        $tahun = $this->input->post('thn', true);
        $bulan = $this->input->post('bln', true);
        $tanggal = $this->input->post('tgl', true);
        $hari = $this->input->post('hari', true);
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $bulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $tanggal = str_pad($tanggal, 2, '0', STR_PAD_LEFT);
        $info = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        if ($info != null) {
            $istirahat = unserialize($info->istirahat);
        } else {
            $istirahat = [];
        }
        $jadwal = $this->dashboard->loadJadwalHariIni($id_tp, $id_smt, $id_kelas, $hari);
        $arrIdMapel = [];
        foreach ($jadwal as $jd) {
            array_push($arrIdMapel, $jd->id_mapel);
        }
        $jadwal_materi = [];
        if (!(count($arrIdMapel) > 0)) {
        }
        $jadwal_materi = $this->kelas->getAllMateriByTgl($id_kelas, $tahun . '-' . $bulan . '-' . $tanggal, $arrIdMapel);
        $arrIdKjm = [];
        foreach ($jadwal_materi as $jmtr) {
            foreach ($jmtr as $jam) {
                foreach ($jam as $jns) {
                    array_push($arrIdKjm, $jns->id_kjm);
                }
            }
        }
        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        $log = [];
        if (!($info != null)) {
        }
        foreach ($siswa as $s) {
            $status_materi = [];
            if (!(count($arrIdKjm) > 0)) {
                $status = [];
            } else {
                $status_materi = $this->kelas->getRekapStatusMateri($s->id_siswa, $arrIdKjm);
                $status = [];
            }
            foreach ($status_materi as $stat) {
                $status[$stat->jam_ke][$stat->id_mapel][$stat->jenis] = $stat;
            }
            $log[$s->id_siswa] = ['nama' => $s->nama, 'nis' => $s->nis, 'kelas' => $s->nama_kelas, 'status' => $status];
        }
        $this->output_json(array('test' => [$id_kelas, $tahun . '-' . $bulan . '-' . $tanggal, $arrIdMapel], 'log' => $log, 'info' => $info, 'jadwal' => $jadwal, 'materi' => $jadwal_materi, 'istirahat' => $istirahat));
    }
}
```

---

## File: application/controllers_progress/Kelascatatan.php

```php
<?php

class Kelascatatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
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
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Catatan Guru', 'subjudul' => 'Catatan Selama Pembelajaran', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $id_kelas = $this->input->get('kelas', true);
        $id_mapel = $this->input->get('mapel', true);
        $data['kelas_selected'] = $id_kelas;
        $data['mapel_selected'] = $id_mapel;
        if (!($id_kelas != null)) {
            if ($this->ion_auth->is_admin()) {
            }
        } else {
            $cat_kelas = $this->kelas->getCatatanMapelKelas($id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt);
            foreach ($cat_kelas as $ck) {
                $ck->reading = unserialize($ck->reading);
            }
            $data['cat_kelas'] = $cat_kelas;
            $data['cat_siswa'] = $this->kelas->getCatatanMapelSiswa($tp->id_tp, $smt->id_smt, $id_kelas, $id_mapel);
            if ($this->ion_auth->is_admin()) {
            }
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['id_guru'] = $guru->id_guru;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $arrId = [];
        if (!($mapel != null)) {
        }
        foreach ($mapel as $mpl) {
            foreach ($mpl->kelas_mapel as $id_mapel) {
                array_push($arrId, $id_mapel->kelas);
            }
        }
        $kelasses = [];
        if (!(count($arrId) > 0)) {
        }
        $kelasses = $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId);
        $arrMapel = [];
        $arrKelas = [];
        if (!($mapel != null)) {
        }
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls_mapel) {
                foreach ($kelasses as $key => $kelass) {
                    if (!($kls_mapel->kelas == $key)) {
                    } else {
                        $arrKelas[$m->id_mapel][$key] = $kelass;
                    }
                }
            }
        }
        $data['mapel'] = $arrMapel;
        $data['kelas'] = $arrKelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/kelas/catatan/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function siswa()
    {
        $id_siswa = $this->input->get('id');
        $id_mapel = $this->input->get('mapel');
        $id_kelas = $this->input->get('kelas');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Catatan Siswa', 'subjudul' => 'Catatan Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['siswa'] = $this->master->getSiswaById($id_siswa);
        $data['catatan_siswa'] = $this->kelas->getAllCatatanMapelSiswa($id_siswa, $id_mapel, $tp->id_tp, $smt->id_smt);
        $data['mapel'] = $id_mapel;
        $data['kelas'] = $id_kelas;
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('members/guru/kelas/catatan/persiswa');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/kelas/catatan/persiswa');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function saveCatatanKelas()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $this->input->post('id_kelas');
        $id_mapel = $this->input->post('id_mapel', true);
        $text = $this->input->post('text', true);
        $level = $this->input->post('level', true);
        $tgl = date('Y-m-d');
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '1', 'id_mapel' => $id_mapel, 'id_kelas' => $id_kelas, 'id_guru' => $guru->id_guru, 'level' => $level, 'text' => $text, 'reading' => serialize([])];
        $insert = $this->master->create('kelas_catatan_mapel', $data);
        $this->output_json($insert);
    }
    public function saveCatatanSiswa()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_siswa = $this->input->post('id_siswa');
        $id_mapel = $this->input->post('id_mapel', true);
        $text = $this->input->post('text', true);
        $level = $this->input->post('level', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '2', 'id_mapel' => $id_mapel, 'id_siswa' => $id_siswa, 'id_guru' => $guru->id_guru, 'level' => $level, 'text' => $text, 'reading' => serialize([])];
        $insert = $this->master->create('kelas_catatan_mapel', $data);
        $this->output_json($insert);
    }
    public function hapus($id_catatan)
    {
        $delete = $this->master->delete('kelas_catatan_mapel', $id_catatan, 'id_catatan');
        $this->output_json($delete);
    }
}
```

---

## File: application/controllers_progress/Kelasjadwal.php

```php
<?php

class Kelasjadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables', 'form_validation']);
        } else {
            redirect('auth');
            $this->load->library(['datatables', 'form_validation']);
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = '0';
        $data['method'] = '';
        $data['jmlIst'] = [];
        $data['jmlMapel'] = [];
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/jadwal/data');
            $this->load->view('_templates/dashboard/_footer');
        } else if ($this->ion_auth->in_group('guru')) {
        }
    }
    public function kelas($kelas)
    {
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $kelas);
        if ($jadk == null) {
            $data['jadwal_kbm'] = json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]));
        } else {
            $data['jadwal_kbm'] = $jadk;
        }
        $data['id_kelas'] = $kelas;
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $kelas);
        $jml_mapel = $jadk == null ? 1 : $jadk->kbm_jml_mapel_hari;
        if ($jadm == null) {
        }
        foreach ($jadm as $j) {
            $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $kelas)];
        }
        $data['method'] = 'edit';
        $data['jadwal_mapel'] = $jadwal_mapel;
        $data['mapels'] = $this->dropdown->getAllKodeMapel();
        if ($this->ion_auth->is_admin()) {
        }
        if ($this->ion_auth->in_group('guru')) {
        }
    }
    public function setJadwal()
    {
        $istirahat = [];
        $i = 1;
        if (!($i < 5)) {
            $id_tp = $this->master->getTahunActive()->id_tp;
            $id_smt = $this->master->getSemesterActive()->id_smt;
            $id_kelas = $this->input->post('id_kelas', true);
            $insert = ['id_kbm' => $id_tp . $id_smt . $id_kelas, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $id_kelas, 'kbm_jam_pel' => $this->input->post('jam_mapel', true), 'kbm_jam_mulai' => $this->input->post('jam_mulai', true), 'kbm_jml_mapel_hari' => $this->input->post('jml_mapel', true), 'istirahat' => serialize($istirahat)];
            $update = $this->db->replace('kelas_jadwal_kbm', $insert);
            $this->logging->saveLog(3, 'merubah jadwal pelajaran');
            $data['status'] = $update;
            $this->output_json($data);
        } else {
            $jamke = $this->input->post('ist' . $i, true);
            $durasi = $this->input->post('dur_ist' . $i, true);
            if (!$jamke) {
            }
            $istirahat[] = ['ist' => $jamke, 'dur' => $durasi];
            $i++;
            if (!($i < 5)) {
            }
        }
    }
    public function setMapel()
    {
        $input = json_decode($this->input->post('data', true));
        $id_kelas = $this->input->post('id_kelas', true);
        $array = array('id_tp' => $input[0]->id_tp, 'id_smt' => $input[0]->id_smt, 'id_kelas' => $id_kelas);
        $this->db->where($array);
        $this->db->delete('kelas_jadwal_mapel');
        $data = [];
        foreach ($input as $d) {
            $data[] = ['id_jadwal' => $d->id_tp . $d->id_smt . $id_kelas . $d->id_hari . $d->jam_ke, 'id_tp' => $d->id_tp, 'id_smt' => $d->id_smt, 'id_kelas' => $id_kelas, 'id_hari' => $d->id_hari, 'jam_ke' => $d->jam_ke, 'id_mapel' => $d->id_mapel];
        }
        $update = $this->db->insert_batch('kelas_jadwal_mapel', $data);
        $res['status'] = $update;
        $this->output_json($res);
    }
}
```

---

## File: application/controllers_progress/Kelasmaterijadwal.php

```php
<?php

class Kelasmaterijadwal extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables', 'form_validation']);
        } else {
            redirect('auth');
            $this->load->library(['datatables', 'form_validation']);
        }
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = '0';
        $data['method'] = '';
        $data['jmlIst'] = [];
        $data['jmlMapel'] = [];
        $data['thn_selected'] = $tp->tahun;
        $bln = $smt->id_smt == '1' ? '7' : '1';
        $tahun = explode('/', $tp->tahun ?? '');
        $thn = $smt->id_smt == '1' ? $tahun[0] : $tahun[1];
        $data['bln_selected'] = $bln;
        $data['date_selected'] = $thn . '-' . $bln . '-' . date('d');
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materijadwal/data');
            $this->load->view('_templates/dashboard/_footer');
        } else if ($this->ion_auth->in_group('guru')) {
        }
    }
    public function kelas()
    {
        $tahun = $this->input->get('tahun');
        $bulan = $this->input->get('bulan');
        $kelas = $this->input->get('kelas');
        $date = $this->input->get('date');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Jadwal Materi / Tugas', 'subjudul' => 'Set Jadwal Materi / Tugas', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $kelas);
        if ($jadk == null) {
            $data['jadwal_kbm'] = json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]));
        } else {
            $data['jadwal_kbm'] = $jadk;
        }
        $data['id_kelas'] = $kelas;
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $kelas);
        $jml_mapel = $jadk == null ? 1 : $jadk->kbm_jml_mapel_hari;
        if ($jadm == null) {
        }
        foreach ($jadm as $j) {
            $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $kelas)];
        }
        $data['method'] = 'edit';
        $data['jadwal_mapel'] = $jadwal_mapel;
        $data['mapels'] = $this->master->getAllMapel();
        $week = [date('Y-m-d', strtotime('monday this week', strtotime($date))), date('Y-m-d', strtotime('tuesday this week', strtotime($date))), date('Y-m-d', strtotime('wednesday this week', strtotime($date))), date('Y-m-d', strtotime('thursday this week', strtotime($date))), date('Y-m-d', strtotime('friday this week', strtotime($date))), date('Y-m-d', strtotime('saturday this week', strtotime($date)))];
        $data['thn_selected'] = $tahun;
        $data['bln_selected'] = $bulan;
        $data['date_selected'] = $date;
        $data['week'] = $week;
        $data['opsi_materi'] = $this->kelas->getAllMateriByKelas($tp->id_tp, $smt->id_smt);
        $semua_materi = $this->kelas->getAllJadwalMateriByKelas($tp->id_tp, $smt->id_smt);
        $data['detail_jadwal_materi'] = isset($semua_materi[1]) ? $semua_materi[1] : [];
        $data['detail_jadwal_tugas'] = isset($semua_materi[2]) ? $semua_materi[2] : [];
        if ($this->ion_auth->is_admin()) {
        }
        if ($this->ion_auth->in_group('guru')) {
        }
    }
    public function setJadwal()
    {
        $istirahat = [];
        $i = 1;
        if (!($i < 5)) {
            $id_tp = $this->master->getTahunActive()->id_tp;
            $id_smt = $this->master->getSemesterActive()->id_smt;
            $id_kelas = $this->input->post('id_kelas', true);
            $insert = ['id_kbm' => $id_tp . $id_smt . $id_kelas, 'id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_kelas' => $id_kelas, 'kbm_jam_pel' => $this->input->post('jam_mapel', true), 'kbm_jam_mulai' => $this->input->post('jam_mulai', true), 'kbm_jml_mapel_hari' => $this->input->post('jml_mapel', true), 'istirahat' => serialize($istirahat)];
            $update = $this->db->replace('kelas_jadwal_kbm', $insert);
            $this->logging->saveLog(3, 'merubah jadwal pelajaran');
            $data['status'] = $update;
            $this->output_json($data);
        } else {
            $jamke = $this->input->post('ist' . $i, true);
            $durasi = $this->input->post('dur_ist' . $i, true);
            if (!$jamke) {
            }
            $istirahat[] = ['ist' => $jamke, 'dur' => $durasi];
            $i++;
            if (!($i < 5)) {
            }
        }
    }
    public function setMapel()
    {
        $input = json_decode($this->input->post('data', true));
        $id_kelas = $this->input->post('id_kelas', true);
        foreach ($input as $d) {
            $data = ['id_jadwal' => $d->id_tp . $d->id_smt . $id_kelas . $d->id_hari . $d->jam_ke, 'id_tp' => $d->id_tp, 'id_smt' => $d->id_smt, 'id_kelas' => $id_kelas, 'id_hari' => $d->id_hari, 'jam_ke' => $d->jam_ke, 'id_mapel' => $d->id_mapel];
            $update = $this->db->replace('kelas_jadwal_mapel', $data);
        }
        $res['status'] = $update;
        $this->output_json($res);
    }
    public function saveJadwal()
    {
        $input_materi = json_decode($this->input->post('materi', true));
        $input_tugas = json_decode($this->input->post('tugas', true));
        foreach ($input_materi as $im) {
            $insert = ['jenis' => '1', 'id_kjm' => $im->id_kjm, 'id_tp' => $im->id_tp, 'id_smt' => $im->id_smt, 'id_kelas' => $im->id_kelas, 'id_materi' => $im->id_materi, 'id_mapel' => $im->id_mapel, 'jadwal_materi' => $im->jadwal_materi];
            $update = $this->db->replace('kelas_jadwal_materi', $insert);
        }
        foreach ($input_tugas as $im) {
            $insert = ['jenis' => '2', 'id_kjm' => $im->id_kjm, 'id_tp' => $im->id_tp, 'id_smt' => $im->id_smt, 'id_kelas' => $im->id_kelas, 'id_materi' => $im->id_materi, 'id_mapel' => $im->id_mapel, 'jadwal_materi' => $im->jadwal_materi];
            $update = $this->db->replace('kelas_jadwal_materi', $insert);
        }
        $this->logging->saveLog(3, 'merubah jadwal materi dan tugas');
        $this->output_json($update);
    }
}
```

---

## File: application/controllers_progress/Kelasmateri.php

```php
<?php

class Kelasmateri extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
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
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
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
            $id_guru = $this->input->get('id');
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $allGuru = $this->dropdown->getAllGuru();
            array_unshift($allGuru, ['00' => 'Semua Guru']);
            $data['gurus'] = $allGuru;
            $data['id_guru'] = $id_guru == null ? '' : $id_guru;
            $materi = [];
            $kelas_materi = [];
            $jadwal_materi = [];
            if (!($id_guru != null)) {
            }
            $materi = $this->kelas->getAllMateriKelas($id_guru, '1');
            foreach ($materi as $m) {
                $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
                if (!($km == null)) {
                    $kelas_materi[$m->id_materi] = $km;
                } else {
                    $km = $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
                    $kelas_materi[$m->id_materi] = $km;
                }
                $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, $jenis, $tp->id_tp, $smt->id_smt);
            }
            $data['materi'] = $materi;
            $data['kelas_materi'] = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
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
                        if (in_array($vk->id_hari, $arr_h[$vk->id_mapel])) {
                        }
                    } else {
                        $arr_h[$vk->id_mapel] = [];
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        $data['tanggal_jadwal'] = $arr_h;
        if ($this->ion_auth->is_admin()) {
            $id_guru = $this->input->get('id');
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $allGuru = $this->dropdown->getAllGuru();
            $allGuru['00'] = 'Semua Guru';
            $data['gurus'] = $allGuru;
            $data['id_guru'] = $id_guru == null ? '' : $id_guru;
            $materi = [];
            $kelas_materi = [];
            $jadwal_materi = [];
            if (!($id_guru != null)) {
            }
            $materi = $this->kelas->getAllMateriKelas($id_guru, '1');
            foreach ($materi as $m) {
                $arrKls = unserialize($m->materi_kelas);
                if (!(count($arrKls) > 0)) {
                } else {
                    $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
                    if (!($km == null)) {
                    }
                    $km = $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
                    $kelas_materi[$m->id_materi] = $km;
                    $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '1', $tp->id_tp, $smt->id_smt);
                }
            }
            $data['materi'] = $materi;
            $data['kelas_materi'] = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
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
                        if (in_array($vk->id_hari, $arr_h[$vk->id_mapel])) {
                        }
                    } else {
                        $arr_h[$vk->id_mapel] = [];
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        $data['tanggal_jadwal'] = $arr_h;
        if ($this->ion_auth->is_admin()) {
            $id_guru = $this->input->get('id');
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $allGuru = $this->dropdown->getAllGuru();
            $allGuru['00'] = 'Semua Guru';
            $data['gurus'] = $allGuru;
            $data['id_guru'] = $id_guru == null ? '' : $id_guru;
            $materi = [];
            $kelas_materi = [];
            $jadwal_materi = [];
            if (!($id_guru != null)) {
            }
            $materi = $this->kelas->getAllMateriKelas($id_guru, '2');
            foreach ($materi as $m) {
                $arrKls = unserialize($m->materi_kelas);
                if (!(count($arrKls) > 0)) {
                } else {
                    $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
                    if (!($km == null)) {
                    }
                    $km = $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
                    $kelas_materi[$m->id_materi] = $km;
                    $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, '2', $tp->id_tp, $smt->id_smt);
                }
            }
            $data['materi'] = $materi;
            $data['kelas_materi'] = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
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
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            if ($id_materi == null) {
            }
            $materi = $this->kelas->getMateriKelasById($id_materi, $jenis);
            $data['materi'] = $materi;
            $data['id_guru'] = $materi->id_guru;
            $data['gurus'] = $this->dropdown->getAllGuru();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/add');
            $this->load->view('_templates/dashboard/_footer');
        } else {
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
            } else {
                $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
            }
        }
        $id_kelas = [];
        $i = 0;
        if (!($i < $kelas)) {
            $isi_materi = $this->input->post('isi_materi', false);
            $dom = new DOMDocument();
            $dom->loadHTML($isi_materi, LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');
            $numimg = 1;
            foreach ($images as $image) {
                $base64_image_string = $image->getAttribute('src');
                if (strpos($base64_image_string, 'http') !== false) {
                    $pathUpload = 'uploads';
                    $forReplace = explode($pathUpload, $base64_image_string);
                    $image->setAttribute('src', $pathUpload . $forReplace[1]);
                } else {
                    $splited = explode(',', substr($base64_image_string, 5), 2);
                    $mime = $splited[0];
                    $data = $splited[1];
                    $mime_split_without_base64 = explode(';', $mime, 2);
                    $mime_split = explode('/', $mime_split_without_base64[0], 2);
                    $output_file = '';
                    if (!(count($mime_split) == 2)) {
                    }
                    $extension = $mime_split[1];
                    if (!($extension == 'jpeg')) {
                    }
                    $extension = 'jpg';
                    $output_file = 'img_' . date('YmdHis') . $numimg . '.' . $extension;
                    file_put_contents('./uploads/materi/' . $output_file, base64_decode($data));
                    $image->setAttribute('src', 'uploads/materi/' . $output_file);
                    $numimg++;
                }
            }
            $isi = $dom->saveHTML();
            $tp = $this->dashboard->getTahunActive();
            $smt = $this->dashboard->getSemesterActive();
            $data = ['jenis' => $jenis, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'kode_materi' => $this->input->post('kode_materi', true), 'id_guru' => $this->input->post('guru', true), 'id_mapel' => $this->input->post('mapel', true), 'judul_materi' => $this->input->post('judul', true), 'isi_materi' => $isi, 'materi_kelas' => serialize($id_kelas), 'file' => serialize($src_file)];
            if ($id_materi === '') {
            }
            $cek_materi = $this->kelas->getMateriKelasById($id_materi, $jenis);
            if ($cek_materi->id_tp == $tp->id_tp && $cek_materi->id_smt == $smt->id_smt) {
            }
            $data['created_on'] = date('Y-m-d H:i:s');
            $data['updated_on'] = date('Y-m-d H:i:s');
            $saved = $this->master->create('kelas_materi', $data);
            $result['status'] = $saved;
            $result['message'] = 'Materi berhasil dibuat';
            $this->logging->saveLog(3, 'membuat materi');
            $this->output_json($result);
        } else {
            $id_kelas[] = $this->input->post('kelas[' . $i . ']', true);
            $i++;
            if (!($i < $kelas)) {
            }
        }
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
        } else {
            if (!$this->master->delete('kelas_jadwal_materi', $id, 'id_materi')) {
            }
            $this->logging->saveLog(5, 'menghapus materi');
            $this->output_json(['status' => true]);
        }
    }
    public function deleteAllMateri()
    {
        $ids = json_decode($this->input->post('ids', true));
        if (!$this->master->delete('kelas_materi', $ids, 'id_materi')) {
        } else {
            if (!$this->master->delete('kelas_jadwal_materi', $ids, 'id_materi')) {
            }
            $this->logging->saveLog(5, 'menghapus materi');
            $this->output_json(['status' => true]);
        }
    }
    function uploadFile()
    {
        $max_size = $this->input->post('max-size', true);
        if (!isset($_FILES['file_uploads']['name'])) {
            $this->output_json($data);
        } else {
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
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        if (unlink($src)) {
            echo 'File Delete Successfully';
        } else {
            echo 'Gagal';
        }
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
            }
            array_push($list, date('Y-m-d', $time));
            $d++;
            if (!($d <= $numdays)) {
            }
        }
    }
}
```

---

## File: application/controllers_progress/Kelasnilai.php

```php
<?php

class Kelasnilai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Rekapitulasi Nilai Siswa', 'subjudul' => 'Nilai dalam satu semester', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['mapel'] = $this->dropdown->getAllMapel();
            $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/nilai/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $nguru[$guru->id_guru] = $guru->nama_guru;
            $data['guru'] = $guru;
            $data['id_guru'] = $guru->id_guru;
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
            $arrMapel = [];
            $arrKelas = [];
            if (!($mapel != null)) {
            }
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                foreach ($m->kelas_mapel as $kls) {
                    $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                }
            }
            $arrId = [];
            if (!($mapel != null)) {
            }
            foreach ($mapel[0]->kelas_mapel as $id_mapel) {
                array_push($arrId, $id_mapel->kelas);
            }
            $data['mapel'] = $arrMapel;
            $data['arrkelas'] = $arrKelas;
            $data['kelas'] = count($arrId) > 0 ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId) : [];
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/nilai/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function loadNilaiMapel()
    {
        $kelas = $this->input->get('kelas');
        $mapel = $this->input->get('mapel');
        $tahun = $this->input->get('tahun');
        $smt = $this->input->get('smt');
        $stahun = $this->input->get('stahun');
        $siswa = $this->kelas->getKelasSiswa($kelas, $tahun, $smt);
        if ($smt == '1') {
            $arrBulan = ['07', '08', '09', '10', '11', '12'];
        } else {
            $arrBulan = ['01', '02', '03', '04', '05', '06'];
        }
        $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'Nopember', 'Desember'];
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $infos = $this->kelas->getJadwalMapelByMapel($kelas, $mapel, $tahun, $smt);
        $log_siswa = $this->kelas->getRekapMateriSemester($kelas);
        $jadwal_per_bulan = [];
        $jadwal_materi = [];
        $log_materi = [];
        $cols = 0;
        foreach ($arrBulan as $bulan) {
            foreach ($infos as $info) {
                $jadwal_per_bulan[$info->id_hari][$info->jam_ke] = $info;
                $dates = $this->total_hari($info->id_hari, $bulan, $stahun);
                $mtr = null;
                $tgs = null;
                foreach ($dates as $date) {
                    $d = explode('-', $date ?? '');
                    $b = $d[1];
                    $t = $d[2];
                    $jj = $this->kelas->getAllMateriByTgl($kelas, $date, [$mapel]);
                    $mtr = isset($jj[$mapel]) && isset($jj[$mapel][$info->jam_ke]) && isset($jj[$mapel][$info->jam_ke][1]) ? $jj[$mapel][$info->jam_ke][1] : null;
                    $tgs = isset($jj[$mapel]) && isset($jj[$mapel][$info->jam_ke]) && isset($jj[$mapel][$info->jam_ke][2]) ? $jj[$mapel][$info->jam_ke][2] : null;
                    $jadwal_materi[$b][$t][$info->jam_ke][1] = $mtr;
                    $jadwal_materi[$b][$t][$info->jam_ke][2] = $tgs;
                    $cols++;
                }
            }
        }
        $log = [];
        if (count($siswa) > 0 && count($jadwal_per_bulan) > 0) {
        }
        $data['mapels'] = [];
        $this->output_json($data);
    }
    function total_hari($id_day, $bulan, $taun)
    {
        $days = 0;
        $dates = [];
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $idday = $id_day == '7' ? 0 : $id_day;
        $i = 1;
        if (!($i < $total_days)) {
            return $dates;
        } else {
            if (!(date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday)) {
            }
            $days++;
            array_push($dates, date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i)));
            $i++;
            if (!($i < $total_days)) {
            }
        }
    }
}
```

---

## File: application/controllers_progress/Kelasstatus.php

```php
<?php

class Kelasstatus extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
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
            $data = json_encode($data);
            $this->output->set_content_type('application/json')->set_output($data);
        }
    }
    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Kelas_model', 'kelas');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Nilai Harian Siswa', 'subjudul' => 'Nilai', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $guru = $this->dropdown->getAllGuru();
            $data['gurus'] = $guru;
            $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['mapels'] = $this->dropdown->getAllMapel();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/status/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $nguru[$guru->id_guru] = $guru->nama_guru;
            $data['guru'] = $guru;
            $data['gurus'] = $nguru;
            $data['id_guru'] = $guru->id_guru;
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
            $arrMapel = [];
            $arrKelas = [];
            if (!($mapel != null)) {
            }
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                foreach ($m->kelas_mapel as $kls) {
                    $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
                }
            }
            $arrId = [];
            if (!($mapel != null)) {
            }
            foreach ($mapel[0]->kelas_mapel as $id_mapel) {
                array_push($arrId, $id_mapel->kelas);
            }
            $data['mapel'] = $mapel;
            $data['mapels'] = $arrMapel;
            $data['kelas'] = $arrKelas;
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $materi = $this->kelas->getAllKodeMateri($tp->id_tp, $smt->id_smt, $id_guru);
        $arrKelasMateri = [];
        $arrKelasTugas = [];
        foreach ($materi as $m) {
            $kode_mapel = $m->kode_mapel == null ? '--' : $m->kode_mapel;
            if ($m->jenis == '1') {
                $arrKelasMateri[] = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'kelas' => unserialize($m->materi_kelas ?? '')];
            } else {
                $arrKelasTugas[] = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'kelas' => unserialize($m->materi_kelas ?? '')];
            }
        }
        $this->output_json(array('materi' => $arrKelasMateri, 'tugas' => $arrKelasTugas));
    }
    public function getMateriMapel()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $id_mapel = $this->input->get('id', true);
        $id_guru = $this->input->get('id_guru', true);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $materi = $this->kelas->getKodeMateriMapel($tp->id_tp, $smt->id_smt, $id_mapel, $id_guru);
        $arrKelasMateri = [];
        $arrKelasTugas = [];
        $arrKelas = [];
        foreach ($materi as $m) {
            $kode_mapel = $m->kode_mapel == null ? '--' : $m->kode_mapel;
            if ($m->jenis == '1') {
                $arrMateri = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'guru' => $m->nama_guru, 'jenis' => $m->jenis];
                if (isset($arrKelasMateri[$m->id_kelas])) {
                }
                $arrKelasMateri[$m->id_kelas] = [];
                $arrKelasMateri[$m->id_kelas][] = $arrMateri;
            } else {
                $arrTugas = ['id_materi' => $m->id_materi, 'id_kjm' => $m->id_kjm, 'jadwal' => $m->jadwal_materi, 'kode' => $m->kode_materi, 'mapel' => $kode_mapel, 'guru' => $m->nama_guru, 'jenis' => $m->jenis];
                if (isset($arrKelasTugas[$m->id_kelas])) {
                }
                $arrKelasTugas[$m->id_kelas] = [];
                $arrKelasTugas[$m->id_kelas][] = $arrTugas;
            }
            if (isset($arrKelas[$m->jenis])) {
            }
            $arrKelas[$m->jenis] = [];
            $arrKelas[$m->jenis][] = $m->id_kelas;
        }
        $this->output_json(array('materi' => $arrKelasMateri, 'tugas' => $arrKelasTugas, 'kelas' => $arrKelas));
    }
    public function loadStatus()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $label = $this->input->post('label', true);
        $id_kelas = $this->input->post('id_kelas', true);
        $id_kjm = $this->input->post('id_kjm', true);
        $id_tp = $this->master->getTahunActive()->id_tp;
        $id_smt = $this->master->getSemesterActive()->id_smt;
        $jenis = $label === 'Materi' ? '1' : '2';
        $siswa = $this->kelas->getKelasSiswa($id_kelas, $id_tp, $id_smt);
        $logs = $this->kelas->getStatusMateriSiswa($id_kjm);
        $info = $this->dashboard->getJadwalKbm($id_tp, $id_smt, $id_kelas);
        if (!($info != null)) {
            $materi = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis);
        } else {
            $info->istirahat = unserialize($info->istirahat ?? '');
            $materi = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis);
        }
        $detail = [];
        $jam_materi = [];
        if (!$materi) {
        }
        $kelas_materi = $this->kelas->getNamaKelasById([$id_kelas]);
        $numday = date('N', strtotime($materi->jadwal_materi));
        $jadwals = $this->kelas->loadJadwalSiswaHariIni($id_tp, $id_smt, $id_kelas, $numday, false);
        $key = array_search($materi->id_mapel, array_column($jadwals, 'id_mapel'));
        $jadwal = $jadwals[$key];
        $ist = json_decode(json_encode($info->istirahat));
        $arrDur = [];
        $arrIst = [];
        foreach ($ist as $istirahat) {
            $arrIst[] = $istirahat->ist;
            $arrDur[$istirahat->ist] = $istirahat->dur;
        }
        $jamMulai = new DateTime($info->kbm_jam_mulai);
        $jamSampai = new DateTime($info->kbm_jam_mulai);
        $jam_mapel = [];
        $i = 0;
        if (!($i < $info->kbm_jml_mapel_hari)) {
        }
        $jamke = $i + 1;
        if (in_array($jamke, $arrIst)) {
        }
        try {
            $jamSampai->add(new DateInterval('PT' . $info->kbm_jam_pel . 'M'));
            $jam_mapel[$jamke] = ['dari' => $jamMulai->format('H:i'), 'sampai' => $jamSampai->format('H:i'), 'tgl' => $materi->jadwal_materi];
            $jamMulai->add(new DateInterval('PT' . $info->kbm_jam_pel . 'M'));
        } catch (Exception $e) {
        }
        $i++;
    }
    public function saveNilai()
    {
        $method = $this->input->post('method', true);
        $label = $this->input->post('label', true);
        $id_log = $this->input->post('id_log', true);
        $nilai = $this->input->post('nilai', true);
        $catatan = $this->input->post('catatan', true);
        $insert = ['nilai' => $nilai, 'catatan' => $catatan];
        $this->db->where('id_log', $id_log);
        $q = $this->db->get('log_materi');
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
```

---

## File: application/controllers_progress/Pengumuman.php

```php
<?php

class Pengumuman extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Post_model', 'post');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Pengumuman', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['gurus'] = $this->dropdown->getAllGuru();
        $kelas = $this->dropdown->getAllKeyKodeKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $kelas;
        $data['running_text'] = $this->dashboard->getRunningText();
        if ($this->ion_auth->is_admin()) {
            $data['subjudul'] = 'Semua Pengumuman';
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['pengumumans'] = $this->post->getPostUser(0);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('pengumuman/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['subjudul'] = 'Pengumuman Anda';
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $data['pengumumans'] = $this->post->getPostUser($guru->id_guru);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('pengumuman/data');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function kepada($kepada, $id_kepada = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Pengumuman', 'subjudul' => 'Semua Pengumuman', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['gurus'] = $this->dropdown->getAllGuru();
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $kelas;
        $this->db->select('a.*, b.nama_guru, b.foto');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $pengumumans = $this->db->get()->result();
        $comments = [];
        $balasan = [];
        foreach ($pengumumans as $pengumuman) {
            $this->db->select('a.*, b.nama_guru, b.foto');
            $this->db->from('post_comments a');
            $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
            $this->db->order_by('a.tanggal', 'desc');
            $this->db->where('a.id_post', $pengumuman->id_post);
            $comment = $this->db->get()->result();
            foreach ($comment as $comm) {
                $this->db->select('a.*, b.nama_guru, b.foto');
                $this->db->from('post_reply a');
                $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
                $this->db->order_by('a.tanggal', 'desc');
                $this->db->where('a.id_comment', $comm->id_comment);
                $balasan[$pengumuman->id_post][$comm->id_comment] = $this->db->get()->result();
            }
            $comments[$pengumuman->id_post] = $comment;
        }
        $data['pengumumans'] = $pengumumans;
        $data['comments'] = $comments;
        $data['balasans'] = $balasan;
        if ($kepada === 'semua_guru') {
            $data['kepada'] = 'Semua Guru';
        } else {
            if ($kepada === 'semua_siswa') {
            }
            $data['kepada'] = urldecode($kepada);
        }
        if ($this->ion_auth->is_admin()) {
        }
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('pengumuman/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function getPost()
    {
        $post = $this->post->getPostForUser(null);
        $this->output_json($post);
    }
    public function getComment($id_post, $page)
    {
        $perPage = 5;
        $offset = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_post', $id_post);
        $this->db->limit($perPage, $offset);
        $comment = $this->db->get()->result();
        $this->output_json($comment);
    }
    public function getReplies($id_comment, $page)
    {
        $perPage = 5;
        $offset = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id_comment);
        $this->db->limit($perPage, $offset);
        $replies = $this->db->get()->result();
        $this->output_json($replies);
    }
    public function save()
    {
        $kepada = json_decode(json_encode($this->input->post('kepada[]', true)));
        $dari = $this->input->post('dari');
        $data = ['kepada' => serialize($kepada), 'dari' => $dari, 'dari_group' => $dari == '0' ? '1' : '2', 'text' => $this->input->post('text'), 'tanggal' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')];
        $insert = $this->db->replace('post', $data);
        $this->output_json($insert);
    }
    public function saveKomentar()
    {
        $dari = '0';
        $dari_group = 1;
        if ($this->ion_auth->is_admin()) {
            $data = ['id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        } else {
            $user = $this->ion_auth->user()->row();
            $tp = $this->master->getTahunActive();
            $smt = $this->master->getSemesterActive();
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $dari = $guru->id_guru;
            $dari_group = 2;
            $data = ['id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        }
        $insert = $this->db->replace('post_comments', $data);
        $id = $this->db->insert_id();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id);
        $comment = $this->db->get()->result();
        $this->output_json($comment);
    }
    public function saveBalasan()
    {
        $dari = '0';
        $dari_group = 1;
        if ($this->ion_auth->is_admin()) {
            $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        } else {
            $user = $this->ion_auth->user()->row();
            $tp = $this->master->getTahunActive();
            $smt = $this->master->getSemesterActive();
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $dari = $guru->id_guru;
            $dari_group = 2;
            $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        }
        $insert = $this->db->replace('post_reply', $data);
        $id = $this->db->insert_id();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_reply', $id);
        $replies = $this->db->get()->result();
        $this->output_json($replies);
    }
    public function hapusPost($id_post)
    {
        $this->db->trans_start();
        $comments = $this->post->getIdComments($id_post);
        foreach ($comments as $comment) {
            $this->db->where('id_comment', $comment->id_comment);
            $deleted['balasan'] = $this->db->delete('post_reply');
        }
        $this->db->where('id_post', $id_post);
        if (!$this->db->delete('post_comments')) {
            $this->db->trans_complete();
        } else {
            $this->db->where('id_post', $id_post);
            $deleted = $this->db->delete('post');
            $this->db->trans_complete();
        }
        $this->output_json($deleted);
    }
    public function hapusKomentar($id_comment)
    {
        $this->db->trans_start();
        $this->db->where('id_comment', $id_comment);
        $deleted['komentar'] = $this->db->delete('post_comments');
        $this->db->where('id_comment', $id_comment);
        $deleted['balasan'] = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }
    public function hapusBalasan($id_reply)
    {
        $this->db->trans_start();
        $this->db->where('id_reply', $id_reply);
        $deleted['balasan'] = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }
    public function getRunningText()
    {
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->output_json($data);
    }
    public function saveRunningText()
    {
        $input = json_decode($this->input->post('text', true));
        $updates = [];
        foreach ($input as $d) {
            $data = ['id_text' => $d->id_text, 'text' => $d->text];
            $update = $this->db->replace('running_text', $data);
            array_push($updates, $update);
        }
        $data['status'] = $updates;
        $this->output_json($data);
    }
    public function hapusRunningText($id)
    {
        $this->db->where('id_text', $id);
        $deleted = $this->db->delete('running_text');
        $this->output_json($deleted);
    }
}
```

---

## File: application/controllers_progress/Rapor.php

```php
<?php

class Rapor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->dbforge();
        $this->load->database();
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Rapor_model', 'rapor');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Master_model', 'master');
        $this->form_validation->set_error_delimiters('', '');
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
        $this->load->model('Dashboard_model', 'dashboard');
        $no_update = $this->db->field_exists('nip_kepsek', 'rapor_admin_setting');
        if ($no_update) {
            $user = $this->ion_auth->user()->row();
        } else {
            $field = array('nip_kepsek' => array('type' => 'int', 'constraint' => 1, 'default' => 0), 'nip_walikelas' => array('type' => 'int', 'constraint' => 1, 'default' => 0));
            $this->dbforge->add_column('rapor_admin_setting', $field);
            $user = $this->ion_auth->user()->row();
        }
        $data = ['user' => $user, 'judul' => 'Pengaturan Rapor', 'subjudul' => 'Pengaturan Rapor', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $data['rapor'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $data['kkm_drop'] = ['Tidak', 'Ya'];
        if ($this->ion_auth->is_admin()) {
        }
        redirect('rapor/raporkkm');
    }
    public function saveRaporAdmin()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $input = ['id_setting' => $tp->id_tp . $smt->id_smt, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'tgl_rapor_pts' => $this->input->post('tgl_rapor_pts', true), 'nip_kepsek' => $this->input->post('nip_kepsek', true), 'nip_walikelas' => $this->input->post('nip_walikelas', true), 'tgl_rapor_akhir' => $this->input->post('tgl_rapor_akhir', true), 'tgl_rapor_kelas_akhir' => $this->input->post('tgl_rapor_kelas_akhir', true), 'kkm_tunggal' => $this->input->post('kkm_tunggal', true), 'kkm' => $this->input->post('kkm', true), 'bobot_ph' => $this->input->post('bobot_ph', true), 'bobot_pts' => $this->input->post('bobot_pts', true), 'bobot_pas' => $this->input->post('bobot_pas', true)];
        $update = $this->db->replace('rapor_admin_setting', $input);
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function raporkkm()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'KKM dan Bobot', 'subjudul' => 'Input KKM dan Bobot Nilai', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = $mapel_guru->mapel_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->mapel_kelas))) : [];
        $arrMapel = [];
        $arrKelas = [];
        $kelases = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                if (!($key_kelas !== false)) {
                } else {
                    $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
                }
            }
        }
        $data['guru'] = $guru;
        $data['mapel'] = $arrMapel;
        $data['kelas'] = $arrKelas;
        $ekstra = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $arrEkstra = [];
        $arrKelasEkstra = [];
        if (!(count($ekstra) > 0)) {
            $data['ekstra'] = $arrEkstra;
        } else {
            foreach ($ekstra as $m) {
                $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
                foreach ($m->kelas_ekstra as $kls) {
                    $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                    if (!($key_kelas !== false)) {
                    } else {
                        $arrKelasEkstra[$m->id_ekstra][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
                    }
                }
            }
            $data['ekstra'] = $arrEkstra;
        }
        $data['kelas_ekstra'] = $arrKelasEkstra;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kkm/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function datakkm($mapel, $kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kkm = '';
        if (!($kelas != null)) {
            $data['mapel'] = $mapel;
        } else {
            $kkm = $this->rapor->getKkm($mapel . $kelas . $tp->id_tp . $smt->id_smt . '1');
            $data['mapel'] = $mapel;
        }
        $data['kelas'] = $kelas;
        $data['kkm'] = $kkm;
        $data['tp'] = $tp->id_tp;
        $data['smt'] = $smt->id_smt;
        $data['setting'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $this->output_json($data);
    }
    public function datakkmEkstra($ekstra, $kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kkm = '';
        if (!($kelas != null)) {
            $data['ekstra'] = $ekstra;
        } else {
            $kkm = $this->rapor->getKkm($ekstra . $kelas . $tp->id_tp . $smt->id_smt . '2');
            $data['ekstra'] = $ekstra;
        }
        $data['kelas'] = $kelas;
        $data['kkm'] = $kkm;
        $data['tp'] = $tp->id_tp;
        $data['smt'] = $smt->id_smt;
        $data['setting'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $this->output_json($data);
    }
    public function saveKkm()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $input = ['id_kkm' => $this->input->post('id_kkm', true), 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'bobot_ph' => $this->input->post('bobot_ph', true), 'bobot_pts' => $this->input->post('bobot_pts', true), 'bobot_pas' => $this->input->post('bobot_pas', true), 'kkm' => $this->input->post('kkm', true), 'beban_jam' => $this->input->post('beban', true), 'jenis' => $this->input->post('jenis_kkm', true), 'id_kelas' => $this->input->post('id_kelas', true), 'id_mapel' => $this->input->post('id_mapel', true)];
        $update = $this->db->replace('rapor_kkm', $input);
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function raporkikd()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Indikator KD', 'subjudul' => 'Ringkasan Materi Penilaian', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $arrMapel = [];
        $arrKelas = [];
        $kelases = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);
        if (!($mapel != null)) {
            $data['guru'] = $guru;
        } else {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                foreach ($m->kelas_mapel as $kls) {
                    $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                    if (!($key_kelas !== false)) {
                    } else {
                        $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
                    }
                }
            }
            $data['guru'] = $guru;
        }
        $data['mapel'] = $arrMapel;
        $data['kelas'] = $arrKelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kikd/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function datakikd($mapel, $kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $kikds = $this->rapor->getKikdMapelKelas($mapel, $kelas, $tp->id_tp, $smt->id_smt);
        $arrKiKd[] = [];
        if (!($kelas != null)) {
            $data['mapel'] = $mapel;
        } else {
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                $i = 0;
                if (!($i < 8)) {
                } else {
                    $no = $i + 1;
                    $key_ki = array_search($mapel . $kelas . $asp . $no, array_column($kikds, 'id_kikd'));
                    if ($key_ki !== false) {
                    }
                    $arrKiKd[$asp][$mapel . $kelas . $asp . $no] = ['materi_kikd' => ''];
                    $i++;
                    if (!($i < 8)) {
                    }
                }
            }
            $data['mapel'] = $mapel;
        }
        $data['kelas'] = $kelas;
        $data['kikd'] = $arrKiKd;
        $this->output_json($data);
    }
    public function saveKikd()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $sjson = $this->input->post('materi', true);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $updated = false;
        foreach ((array) $sjson as $aspek => $mapel_kelas) {
            foreach ($mapel_kelas as $idmk => $kikd) {
                foreach ($kikd as $id => $materi) {
                    $input = ['id_kikd' => $id, 'id_mapel_kelas' => $idmk, 'aspek' => $aspek, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $materi];
                    $updated = $this->db->replace('rapor_kikd', $input);
                }
            }
        }
        $data['status'] = $updated;
        $data['json'] = $sjson;
        $this->output_json($data);
    }
    public function raporNilai()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Input Nilai', 'subjudul' => 'Input Nilai Rapor', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = $mapel_guru->mapel_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->mapel_kelas))) : [];
        $siswas = [];
        $arrMapel = [];
        $arrKelasMapel = [];
        $levelsMapel = [];
        $harian = [];
        $pts = [];
        $pas = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $kelas_guru = $this->kelas->get_one($kls->kelas);
                if (!($kelas_guru != null)) {
                } else {
                    $levelsMapel[] = $kelas_guru->level_id;
                    $arrKelasMapel[$m->id_mapel][] = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                    $siswas[$m->id_mapel][$kelas_guru->nama_kelas] = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                    $harian[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiHarianKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                    $pts[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiPtsKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                    $pas[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiAkhirKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                }
            }
        }
        $data['mapel'] = $arrMapel;
        $data['kelas_mapel'] = $arrKelasMapel;
        $data['level'] = array_unique($levelsMapel);
        $data['siswas'] = $siswas;
        $data['harian'] = $harian;
        $data['pts'] = $pts;
        $data['pas'] = $pas;
        $ekstra = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $arrEkstra = [];
        $arrKelasEkstra = [];
        $ektras = [];
        $siswae = [];
        if (!(count($ekstra) > 0)) {
            $data['ekstras'] = $ektras;
        } else {
            foreach ($ekstra as $m) {
                $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
                foreach ($m->kelas_ekstra as $kls) {
                    $kelas_guru = $this->kelas->get_one($kls->kelas);
                    if (!($kelas_guru != null)) {
                    } else {
                        $arrKelasEkstra[$m->id_ekstra][] = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                        $siswae[$m->id_ekstra][$kelas_guru->nama_kelas] = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                        $ektras[$m->id_ekstra][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiEkstraKelas($m->id_ekstra, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                    }
                }
            }
            $data['ekstras'] = $ektras;
        }
        $data['siswae'] = $siswae;
        $data['ekstra'] = $arrEkstra;
        $data['kelas_ekstra'] = $arrKelasEkstra;
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function raporNilaiGuru($filter = null, $id_mapel = null)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Semua Nilai', 'subjudul' => 'Semua Nilai Rapor', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $ret[''] = 'Pilih Mapel';
        $dropMapel = $this->dropdown->getAllMapel();
        $data['mapel'] = $ret + $dropMapel;
        $ret[''] = 'Pilih Eskul';
        $dropEskul = $this->dropdown->getAllEkskul();
        $data['ekstra'] = $ret + $dropEskul;
        $data['filter'] = ['' => 'Filter berdasarkan', '1' => 'Mata Pelajaran', '2' => 'Ekstrakurikuler'];
        $data['ekstra_selected'] = $id_mapel;
        $data['mapel_selected'] = $id_mapel;
        $data['filter_selected'] = $filter;
        $jabatan_guru = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        foreach ($jabatan_guru as $jabatan) {
            $jabatan->mapel_kelas = $jabatan->mapel_kelas == null ? [] : unserialize($jabatan->mapel_kelas);
            $jabatan->ekstra_kelas = $jabatan->ekstra_kelas == null ? [] : unserialize($jabatan->ekstra_kelas);
        }
        if (!($id_mapel != null)) {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/nilaiguru');
            $this->load->view('members/guru/templates/footer');
        } else {
            $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            if ($setting->kkm_tunggal == '1') {
            }
            $kkm = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '1');
            $kkm_ekstra = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '2');
            $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
            $nilai = [];
            $arrKiKd[] = [];
            if (!($guru->wali_kelas != null)) {
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                $i = 0;
                if (!($i < 8)) {
                } else {
                    $no = $i + 1;
                    $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                    $i++;
                    if (!($i < 8)) {
                    }
                }
            }
            if ($filter == '1') {
            }
            $guru_mapel = '';
            foreach ($jabatan_guru as $jab) {
                foreach ($jab->ekstra_kelas as $mk) {
                    if (!($mk['id_ekstra'] == $id_mapel)) {
                    } else {
                        foreach ($mk['kelas_ekstra'] as $km) {
                            if (!($km['kelas'] == $guru->wali_kelas)) {
                            } else {
                                $guru_mapel = $jab->nama_guru;
                            }
                        }
                    }
                }
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $i = 0;
            if (!($i < count($siswas))) {
            }
            $siswa = $siswas[$i];
            $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
            $i++;
        }
    }
    public function raporCekNilai($filter = null, $id_mapel = null)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $data = ['user' => $user, 'judul' => 'Semua Nilai', 'subjudul' => 'Semua Nilai Rapor', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $ret[''] = 'Pilih Mapel';
        $dropMapel = $this->dropdown->getAllMapel();
        $data['mapel'] = $ret + $dropMapel;
        $ret[''] = 'Pilih Eskul';
        $dropEskul = $this->dropdown->getAllEkskul();
        $data['ekstra'] = $ret + $dropEskul;
        $data['filter'] = ['' => 'Filter berdasarkan', '1' => 'Mata Pelajaran', '2' => 'Ekstrakurikuler'];
        $data['ekstra_selected'] = $id_mapel;
        $data['mapel_selected'] = $id_mapel;
        $data['filter_selected'] = $filter;
        $jabatan_guru = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        foreach ($jabatan_guru as $jabatan) {
            $jabatan->mapel_kelas = $jabatan->mapel_kelas == null ? [] : unserialize($jabatan->mapel_kelas);
            $jabatan->ekstra_kelas = $jabatan->ekstra_kelas == null ? [] : unserialize($jabatan->ekstra_kelas);
        }
        if (!($id_mapel != null)) {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/periksa');
            $this->load->view('members/guru/templates/footer');
        } else {
            $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            if ($setting->kkm_tunggal == '1') {
            }
            $jenis = $filter == '1' ? '1' : '2';
            $kkm = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . $jenis);
            $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
            $nilai = [];
            $arrKiKd[] = [];
            if (!($guru->wali_kelas != null)) {
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                $i = 0;
                if (!($i < 8)) {
                } else {
                    $no = $i + 1;
                    $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                    $i++;
                    if (!($i < 8)) {
                    }
                }
            }
            if ($filter == '1') {
            }
            $guru_mapel = '';
            foreach ($jabatan_guru as $jab) {
                foreach ($jab->ekstra_kelas as $mk) {
                    if (!($mk['id_ekstra'] == $id_mapel)) {
                    } else {
                        foreach ($mk['kelas_ekstra'] as $km) {
                            if (!($km['kelas'] == $guru->wali_kelas)) {
                            } else {
                                $guru_mapel = $jab->nama_guru;
                            }
                        }
                    }
                }
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $i = 0;
            if (!($i < count($siswas))) {
            }
            $siswa = $siswas[$i];
            $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
            $i++;
        }
    }
    public function inputHarian($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $mapel = '';
        $kelas = [];
        foreach ($mapels as $m) {
            if (!($m->id_mapel === $id_mapel)) {
                foreach ($m->kelas_mapel as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            } else {
                $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
                foreach ($m->kelas_mapel as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
            $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            $kkm = null;
            if (!($setting != null)) {
            }
            if ($setting->kkm_tunggal == '1') {
            }
            $kkm = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            $arrKiKd[] = [];
            if (!($id_kelas != null)) {
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                $i = 0;
                if (!($i < 8)) {
                } else {
                    $no = $i + 1;
                    $r = $this->rapor->getKikdMapel($id_mapel . $id_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                    if (!($r == null)) {
                    }
                    $r = $this->rapor->getKikdMapel($id_mapel . $id_kelas . $asp . $no, $tp->id_tp - 1, $smt->id_smt);
                    $arrKiKd[$asp][$id_mapel . $id_kelas . $asp . $no] = $r;
                    $i++;
                    if (!($i < 8)) {
                    }
                }
            }
            $data = ['user' => $user, 'judul' => 'Nilai Harian Kelas ', 'subjudul' => 'Input Nilai Harian Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'mapel' => $mapel, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm];
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $data['kikd'] = $arrKiKd;
            $data['setting_rapor'] = $setting;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/harian');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
            $ns = $this->rapor->getNilaiHarianKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
    public function downloadNilaiHarian($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiHarianKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->p1 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p1 ?? '' : '';
            $siswa->p2 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p2 ?? '' : '';
            $siswa->p3 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p3 ?? '' : '';
            $siswa->p4 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p4 ?? '' : '';
            $siswa->p5 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p5 ?? '' : '';
            $siswa->p6 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p6 ?? '' : '';
            $siswa->p7 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p7 ?? '' : '';
            $siswa->p8 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->p8 ?? '' : '';
            $siswa->k1 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k1 ?? '' : '';
            $siswa->k2 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k2 ?? '' : '';
            $siswa->k3 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k3 ?? '' : '';
            $siswa->k4 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k4 ?? '' : '';
            $siswa->k5 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k5 ?? '' : '';
            $siswa->k6 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k6 ?? '' : '';
            $siswa->k7 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k7 ?? '' : '';
            $siswa->k8 = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->k8 ?? '' : '';
        }
        $kikds = $this->rapor->getKikdMapelKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($kikds as $ki) {
            if ($ki->aspek == 1) {
                $nn = substr($ki->id_kikd, -1);
                $ki->nop = $nn;
                $ki->kodep = 'P' . $nn;
                $ki->p = $ki->materi_kikd;
            } else {
                $nn = substr($ki->id_kikd, -1);
                $ki->nok = $nn;
                $ki->kodek = 'K' . $nn;
                $ki->k = $ki->materi_kikd;
            }
        }
        if (!(count($kikds) == 0)) {
            $this->output_json(['siswa' => $siswas, 'kikd' => $kikds]);
        } else {
            $kikds[] = ['nok' => 1, 'kodek' => 'K1', 'k' => 'Praktik/Portofolio/Proyek yang dinilai (lihat tabel KATA KERJA sebelah kanan)', 'nop' => 1, 'kodep' => 'P1', 'p' => 'Materi yang dinilai (lihat tabel KATA KERJA sebelah kanan)'];
            $this->output_json(['siswa' => $siswas, 'kikd' => $kikds]);
        }
    }
    public function uploadNilaiHarian()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa = $this->input->post('siswa');
        $p_kikd = $this->input->post('kikd');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        $kikdp = [];
        $kikdk = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_harian'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa'] = $siswa['id'];
            $siswa['id_mapel'] = $id_mapel;
            $siswa['id_kelas'] = $id_kelas;
            $siswa['id_tp'] = $tp->id_tp;
            $siswa['id_smt'] = $smt->id_smt;
            unset($siswa['id']);
            unset($siswa['nisn']);
            unset($siswa['namasiswa']);
            $datas[] = $siswa;
        }
        foreach ($p_kikd as $kikd) {
            $kikdp[] = ['id_kikd' => $id_mapel . $id_kelas . '1' . $kikd['no'], 'id_mapel_kelas' => $id_mapel . $id_kelas, 'aspek' => 1, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $kikd['materipengetahuanyangdinilai'] != null ? strip_tags($kikd['materipengetahuanyangdinilai'] ?? '') : ''];
            $kikdk[] = ['id_kikd' => $id_mapel . $id_kelas . '2' . $kikd['no'], 'id_mapel_kelas' => $id_mapel . $id_kelas, 'aspek' => 2, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $kikd['materiketerampilanyangdinilai'] != null ? strip_tags($kikd['materiketerampilanyangdinilai'] ?? '') : ''];
        }
        $updated = 0;
        $this->db->trans_start();
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_harian', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        foreach ($kikdp as $kip) {
            if (!($kip != null)) {
            } else {
                $this->db->replace('rapor_kikd', $kip);
            }
        }
        foreach ($kikdk as $kik) {
            if (!($kik != null)) {
            } else {
                $this->db->replace('rapor_kikd', $kik);
            }
        }
        $this->db->trans_complete();
        $this->output_json($updated);
    }
    public function importHarian()
    {
        $posts = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ((array) $posts as $data) {
            $update = $this->db->replace('rapor_nilai_harian', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $this->db->trans_complete();
        $data['updated'] = $updated;
        $this->output_json($data);
    }
    public function inputPts($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $mapel = '';
        $kelas = [];
        foreach ($mapels as $m) {
            if (!($m->id_mapel === $id_mapel)) {
                foreach ($m->kelas_mapel as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            } else {
                $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
                foreach ($m->kelas_mapel as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
            $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            $kkm = null;
            if (!($setting != null)) {
            }
            if ($setting->kkm_tunggal == '1') {
            }
            $kkm = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            $data = ['user' => $user, 'judul' => 'Nilai PTS Kelas ', 'subjudul' => 'Input Nilai PTS Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'mapel' => $mapel, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm];
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $data['setting_rapor'] = $setting;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/pts');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
            $ns = $this->rapor->getNilaiPtsKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
    public function downloadTemplatePts($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiPtsKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->nilai : '';
            $siswa->predikat = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->predikat : '';
        }
        $this->output_json(['siswa' => $siswas]);
    }
    public function uploadNilaiPts()
    {
        $p_siswa = $this->input->post('siswa');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_pts'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa'] = $siswa['id'];
            $siswa['id_mapel'] = $id_mapel;
            $siswa['id_kelas'] = $id_kelas;
            $siswa['id_tp'] = $tp->id_tp;
            $siswa['id_smt'] = $smt->id_smt;
            unset($siswa['id']);
            unset($siswa['nisn']);
            unset($siswa['namasiswa']);
            $datas[] = $siswa;
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_pts', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $this->output_json($updated);
    }
    public function importPts()
    {
        $inputs = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ($inputs as $data) {
            $update = $this->db->replace('rapor_nilai_pts', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $this->db->trans_complete();
        echo json_encode($updated);
    }
    public function inputPas($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $mapel = '';
        $kelas = [];
        foreach ($mapels as $m) {
            if (!($m->id_mapel === $id_mapel)) {
                foreach ($m->kelas_mapel as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            } else {
                $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
                foreach ($m->kelas_mapel as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
            $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            $kkm = null;
            if (!($setting != null)) {
            }
            if ($setting->kkm_tunggal == '1') {
            }
            $kkm = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            $data = ['user' => $user, 'judul' => 'Nilai Akhir Kelas ', 'subjudul' => 'Input Nilai Akhir Mapel ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'mapel' => $mapel, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm, 'setting_rapor' => $setting];
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/pas');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $dummyNilai = ['nhar' => '', 'npts' => '', 'npas' => ''];
            $ns = $this->rapor->getNilaiAkhirKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
    public function downloadTemplatePas($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiAkhirKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->npas : '';
        }
        $this->output_json(['siswa' => $siswas]);
    }
    public function uploadNilaiPas()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa = $this->input->post('siswa');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_akhir'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa'] = $siswa['id'];
            $siswa['id_mapel'] = $id_mapel;
            $siswa['id_kelas'] = $id_kelas;
            $siswa['id_tp'] = $tp->id_tp;
            $siswa['id_smt'] = $smt->id_smt;
            unset($siswa['id']);
            unset($siswa['nisn']);
            unset($siswa['namasiswa']);
            $datas[] = $siswa;
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_akhir', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $this->output_json($updated);
    }
    public function importPas()
    {
        $inputs = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ($inputs as $data) {
            $update = $this->db->replace('rapor_nilai_akhir', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $this->db->trans_complete();
        echo json_encode($updated);
    }
    public function inputEkstra($id_ekstra, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $ekstra_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $ekstras = json_decode(json_encode(unserialize($ekstra_guru->ekstra_kelas)));
        $ekstra = '';
        $kelas = [];
        foreach ($ekstras as $m) {
            if (!($m->id_ekstra === $id_ekstra)) {
                foreach ($m->kelas_ekstra as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            } else {
                $ekstra = ['id_ekstra' => $m->id_ekstra, 'nama_ekstra' => $m->nama_ekstra];
                foreach ($m->kelas_ekstra as $kls) {
                    if (!($kls->kelas === $id_kelas)) {
                    } else {
                        $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                    }
                }
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
            $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            if ($setting->kkm_tunggal == '1') {
            }
            $kkm = $this->rapor->getKkm($id_ekstra . $id_kelas . $tp->id_tp . $smt->id_smt . '2');
            $data = ['user' => $user, 'judul' => 'Nilai Ekstrakurikuler ', 'subjudul' => 'Input Nilai PTS Ekstra ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'ekstra' => $ekstra, 'kelas' => $kelas, 'siswa' => $siswas, 'nilai' => $nilai, 'kkm' => $kkm];
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/ekstra');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
            $ns = $this->rapor->getNilaiEkstraKelas($id_ekstra, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
    public function downloadTemplateEkstra($id_ekstra, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiEkstraKelas($id_ekstra, $id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($siswas as $ind => $siswa) {
            $siswa->no = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->nilai : '';
        }
        $this->output_json(['siswa' => $siswas]);
    }
    public function uploadNilaiEkstra()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa = $this->input->post('siswa');
        $id_ekstra = $this->input->post('id_ekstra');
        $id_kelas = $this->input->post('id_kelas');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_ekstra'] = $id_ekstra . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa'] = $siswa['id'];
            $siswa['id_ekstra'] = $id_ekstra;
            $siswa['id_kelas'] = $id_kelas;
            $siswa['id_tp'] = $tp->id_tp;
            $siswa['id_smt'] = $smt->id_smt;
            unset($siswa['id']);
            unset($siswa['nisn']);
            unset($siswa['namasiswa']);
            $datas[] = $siswa;
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_ekstra', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function importEkstra()
    {
        $inputs = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ($inputs as $data) {
            $update = $this->db->replace('rapor_nilai_ekstra', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        $this->db->trans_complete();
        echo json_encode($updated);
    }
    public function raporSikap()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Input Nilai Sikap', 'subjudul' => 'Input Nilai Sikap', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $arrMapel = [];
        $arrKelas = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $dummySikap = [];
        $i = 0;
        if (!($i < 10)) {
        }
        $no = $i + 1;
        $s = ['id_sikap' => 1 . $no, 'jenis' => '1', 'kode' => $no, 'sikap' => ''];
        array_push($dummySikap, $s);
        $i++;
    }
    public function saveSikap()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('sikap', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($input as $d) {
            $data = ['id_sikap' => $d->id_sikap, 'id_kelas' => $d->kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'sikap' => $d->sikap, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            $update = $this->db->replace('rapor_data_sikap', $data);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function raporSpiritual()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas, $tp->id_tp, $smt->id_smt);
        $dummySpiritual = [];
        $i = 0;
        if (!($i < 10)) {
        }
        $no = $i + 1;
        $s = ['id_sikap' => $id_kelas . 1 . $no, 'jenis' => '1', 'kode' => $no, 'sikap' => $this->rapor->getDummyDeskripsiSpiritual()[$i]];
        array_push($dummySpiritual, $s);
        $i++;
    }
    public function importSpiritual($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[11];
            if (!($id_siswa != 'id')) {
            } else {
                $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '1', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 1, 'nilai' => serialize(['predikat' => $in[3], 'sl1' => $in[4], 'sl2' => $in[5], 'sl3' => $in[6], 'mb1' => $in[7], 'mb2' => $in[8], 'mb3' => $in[9]]), 'deskripsi' => $in[10], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            }
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_sikap', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function raporSosial()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas, $tp->id_tp, $smt->id_smt);
        $dummySosial = [];
        $i = 0;
        if (!($i < 10)) {
        }
        $no = $i + 1;
        $s = ['id_sikap' => $id_kelas . 2 . $no, 'jenis' => '2', 'kode' => $no, 'sikap' => $this->rapor->getDummyDeskripsiSosial()[$i]];
        array_push($dummySosial, $s);
        $i++;
    }
    public function importSosial($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[13];
            if (!($id_siswa != 'id')) {
            } else {
                $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '2', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 2, 'nilai' => serialize(['predikat' => $in[3], 'a1' => $in[4], 'a2' => $in[5], 'a3' => $in[6], 'b1' => $in[7], 'b2' => $in[8], 'b3' => $in[9], 'c1' => $in[10], 'c2' => $in[11]]), 'deskripsi' => $in[12], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            }
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_sikap', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function raporPrestasi()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();
        $mapels = $this->master->getAllMapel();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas);
        $dummyDeskSaran = [];
        $dummyRank = ['1 ~ 3', '4 ~ 10', '11 ~ 15', '16 ~ 20', '21 ~ 25', '26 > >'];
        $dummyKode = ['1', '4', '11', '16', '21', '26'];
        $i = 0;
        if (!($i < 6)) {
        }
        $no = $i + 1;
        $s = ['id_catatan' => $id_kelas . 1 . $no, 'jenis' => '3', 'kode' => $dummyKode[$i], 'deskripsi' => $this->rapor->getDummyDeskripsiRanking()[$i], 'rank' => $dummyRank[$i]];
        array_push($dummyDeskSaran, $s);
        $i++;
    }
    public function savePrestasi()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('catatan', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($input as $d) {
            $data = ['id_catatan' => $d->id_catatan, 'id_kelas' => $d->kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'rank' => $d->rank, 'deskripsi' => $d->deskripsi, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            $update = $this->db->replace('rapor_data_catatan', $data);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function importPrestasi($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[12];
            $datas[] = ['id_ranking' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'ranking' => $in[4], 'deskripsi' => $in[5], 'p1' => $in[6], 'p1_desk' => $in[7], 'p2' => $in[8], 'p2_desk' => $in[9], 'p3' => $in[10], 'p3_desk' => $in[11]];
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_prestasi', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function raporCatatan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas);
        $dummyDeskAbsensi = [];
        $dummyRank = ['1 ~ 3', '4 ~ 10', '11 ~ 15', '16 > >'];
        $dummyKode = ['1', '4', '11', '16'];
        $i = 0;
        if (!($i < 4)) {
        }
        $no = $i + 1;
        $s = ['id_catatan' => $id_kelas . 1 . $no, 'jenis' => '1', 'kode' => $dummyKode[$i], 'deskripsi' => $this->rapor->getDummyDeskripsiAbsensi()[$i], 'rank' => $dummyRank[$i]];
        array_push($dummyDeskAbsensi, $s);
        $i++;
    }
    public function saveCatatan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('catatan', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($input as $d) {
            $data = ['id_catatan' => $d->id_catatan, 'id_kelas' => $d->kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'rank' => $d->rank, 'deskripsi' => $d->deskripsi, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            $update = $this->db->replace('rapor_data_catatan', $data);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function importCatatan($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[10];
            if (!($id_siswa != 'id')) {
            } else {
                $datas[] = ['id_catatan_wali' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'nilai' => serialize(['op1' => $in[3], 'op2' => $in[4], 'op3' => $in[5], 's' => $in[6], 'i' => $in[7], 'a' => $in[8]]), 'deskripsi' => $in[9], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            }
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_catatan_wali', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function raporFisik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas);
        $dummyDeskFisik = [];
        $jenis = ['1', '2', '3', '4'];
        $i = 0;
        if (!($i < 4)) {
        }
        $no = $i + 1;
        foreach ($jenis as $jns) {
            $s = ['id_fisik' => $id_kelas . $jns . $no, 'jenis' => $jns, 'kode' => $no, 'deskripsi' => $this->rapor->getDummyDeskripsiFisik($jns)[$i]];
            array_push($dummyDeskFisik, $s);
        }
        $i++;
    }
    public function saveFisik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $kelas = $this->input->post('kelas', true);
        $input = json_decode($this->input->post('fisik', true));
        $update = false;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        foreach ($input as $d) {
            $kode = $d[0];
            $jns = $d[0];
            $data = ['id_fisik' => $kelas . $jns . $kode, 'id_kelas' => $kelas, 'jenis' => $d->jenis, 'kode' => $d->kode, 'deskripsi' => $d->deskripsi, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            $update = $this->db->replace('rapor_data_fisik', $data);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function importFisik($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[11];
            $tinggi = $smt->id_smt == 1 ? $in[3] : $in[4];
            $berat = $smt->id_smt == 1 ? $in[5] : $in[6];
            if (!($id_siswa != 'id')) {
            } else {
                $datas[] = ['id_fisik' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_kelas' => $id_kelas, 'id_siswa' => $id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'tinggi' => $tinggi, 'berat' => $berat, 'kondisi' => serialize(['telinga' => $in[7], 'mata' => $in[8], 'gigi' => $in[9], 'lain' => $in[10]])];
            }
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_fisik', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function raporNaik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas);
        $siswas = $this->rapor->getKenaikanSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'judul' => 'Kenaikan Kelas ', 'subjudul' => 'Siswa Kelas ', 'setting' => $this->dashboard->getSetting(), 'guru' => $guru, 'kelas' => $kelas, 'siswas' => $siswas];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kenaikan/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function saveNaik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('naik', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $updated = 0;
        foreach ($input as $d) {
            $data = ['id_naik' => $d->id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $d->id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'naik' => $d->naik];
            $update = $this->db->replace('rapor_naik', $data);
            if (!$update) {
            } else {
                $updated++;
            }
        }
        echo json_encode($updated);
    }
    public function cetakPts()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->db->trans_start();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $jurusan = $this->kelas->getJurusanById($kelas->jurusan_id);
        $kelompoks = $this->master->getKodeKelompokMapel();
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $arrk = [];
        foreach ($kategori_mapel as $kk => $km) {
            if (in_array($km, $arrk)) {
            } else {
                array_push($arrk, $km->kode_kel_mapel);
            }
        }
        $mapels = $this->master->getAllMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);
        $nilaiHarian = [];
        $nilaiPts = [];
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => ''];
        $settingRapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm = [];
        $arr_mapels = [];
        $arr_siswas = [];
        foreach ($mapels as $mapel) {
            $arr_mapels[] = $mapel->id_mapel;
        }
        $i = 0;
        if (!($i < count($siswas))) {
            $nilaiPts = $this->rapor->getArrNilaiMapelPtsSiswa($arr_mapels, $arr_siswas, $tp->id_tp, $smt->id_smt);
            $nilaiHarian = $this->rapor->getArrNilaiMapelHarianSiswa($arr_mapels, $arr_siswas, $tp->id_tp, $smt->id_smt);
            $data = ['user' => $user, 'judul' => 'Rapor PTS', 'subjudul' => 'Cetak Rapor PTS', 'setting' => $setting];
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $data['guru'] = $guru;
            $data['siswas'] = $siswas;
            $data['kelas'] = $kelas->nama_kelas;
            $data['mapels'] = $mapels;
            $data['kelompoks'] = $kelompoks;
            $data['nilai_pts'] = $nilaiPts;
            $data['nilai_harian'] = $nilaiHarian;
            $data['kkm'] = $kkm;
            $data['rapor'] = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
            $this->db->trans_complete();
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/cetak/pts');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $id_siswa = $siswa->id_siswa;
            $arr_siswas[] = $id_siswa;
            foreach ($mapels as $mapel) {
                if (isset($settingRapor) && $settingRapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $settingRapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
            }
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
    public function cetakAkhir()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas = $this->kelas->get_one($id_kelas);
        $jurusan = $this->kelas->getJurusanById($kelas->jurusan_id);
        $kelompoks = $this->master->getKodeKelompokMapel();
        $siswas = $this->rapor->getDetailSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $arrk = [];
        foreach ($kategori_mapel as $kk => $km) {
            if (in_array($km, $arrk)) {
            } else {
                array_push($arrk, $km->kode_kel_mapel);
            }
        }
        $mapels = $this->master->getAllMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $settingRapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm = [];
        $sikap = [];
        $nilai = [];
        $fisik = [];
        $desks = [];
        $absensi = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];
        if ($smt->id_smt === '1') {
            $other = '2';
        } else {
            $other = '1';
        }
        $nilai_sikap = $this->rapor->getNilaiSikapByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai_rapor = $this->rapor->getNilaiRaporByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $siswa = $siswas[$i];
        $id_siswa = $siswa->id_siswa;
        $dummySikap = ['predikat' => ''];
        if (count($nilai_sikap) > 0) {
        }
        $sikap[$id_siswa][1] = ['deskripsi' => '', 'predikat' => $dummySikap];
        $sikap[$id_siswa][2] = ['deskripsi' => '', 'predikat' => $dummySikap];
        foreach ($mapels as $mapel) {
            $dummyNilai = ['p_deskripsi' => '', 'k_rata_rata' => '', 'k_deskripsi' => '', 'k_predikat' => '', 'nilai' => '', 'predikat' => ''];
            $key_mapel = array_search($mapel->id_mapel . $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, array_column($nilai_rapor, 'id_nilai_harian'));
            if (!($key_mapel !== false)) {
            } else {
                $nr = $nilai_rapor[$key_mapel];
                $nilai[$id_siswa][$mapel->id_mapel] = $nr;
            }
        }
        $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => ''];
        $dummyAbsen = ['s' => ' - ', 'i' => ' - ', 'a' => ' - ', 'saran' => ''];
        $desks[$id_siswa] = isset($prestasis[$id_siswa]) ? $prestasis[$id_siswa] : $dummyDesks;
        $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa] : ['nilai' => $dummyAbsen];
        $dummyFisik = ['kondisi' => ['telinga' => '', 'mata' => '', 'gigi' => '', 'lain' => ''], 'smt' . $smt->id_smt => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp], 'smt' . $other => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp]];
        $nf = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nf2 = $this->rapor->getFisikKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $other);
        $fisik[$siswa->id_siswa] = $nf != null ? ['kondisi' => unserialize($nf->kondisi), 'smt' . $nf->id_smt => ['tinggi' => $nf->tinggi, 'berat' => $nf->berat], 'smt' . $other => ['tinggi' => $nf2 != null ? $nf2->tinggi : '', 'berat' => $nf2 != null ? $nf2->berat : '']] : $dummyFisik;
        foreach ($ekstras as $ext) {
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
            foreach ($arrEkstra as $ar) {
                $id_ekstra = $ar->ekstra;
                $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                if (!($id_ekstra != null)) {
                } else {
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? $dummyEkstra : $ne;
                }
            }
        }
        $i++;
    }
    public function cetakLeger()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $setting = $this->dashboard->getSetting();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Leger Kelas ', 'subjudul' => 'Cetak Leger Kelas ', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelases = $this->kelas->get_one($id_kelas);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $mapels = $this->master->getAllMapel();
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm = [];
        $sikap = [];
        $nilai = [];
        $nilaiPts = [];
        $desks = [];
        $absensi = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];
        $i = 0;
        if (!($i < count($siswas))) {
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['kelases'] = $kelases;
            $data['mapels'] = $mapels;
            $data['siswas'] = $siswas;
            $data['ekstras'] = $ekstras;
            $data['nilai'] = (array) json_decode(json_encode($nilai));
            $data['nilai_pts'] = (array) json_decode(json_encode($nilaiPts));
            $data['sikap'] = $sikap;
            $data['deskripsi'] = $desks;
            $data['absensi'] = $absensi;
            $data['nilai_ekstra'] = $nilaiEkstra;
            $data['mapel_ekstra'] = $mapelEkstra;
            $data['kkm'] = $kkm;
            $data['rapor'] = $setting_rapor;
            $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/leger/data');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $id_siswa = $siswa->id_siswa;
            foreach ($mapels as $mapel) {
                $dummySikap = ['predikat' => ''];
                $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
                $sikap[$siswa->id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
                $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
                $sikap[$siswa->id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];
                $dummyNilai = ['k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
                $nr = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
                $nilai[$id_siswa][$mapel->id_mapel] = $nr == null ? $dummyNilai : $nr;
                $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
                $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
                $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
                $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa]->nilai : $dummyAbsen;
                if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $setting_rapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
                foreach ($ekstras as $ext) {
                    $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                    $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                    foreach ($arrEkstra as $ar) {
                        $id_ekstra = $ar->ekstra;
                        $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                        if (!($id_ekstra != null)) {
                        } else {
                            $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                            $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                        }
                    }
                }
            }
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
    public function downloadLeger()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $setting = $this->dashboard->getSetting();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelases = $this->kelas->get_one($id_kelas);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $mapels = $this->master->getAllMapel();
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm = [];
        $sikap = [];
        $nilai = [];
        $nilaiPts = [];
        $desks = [];
        $absensi = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $siswa = $siswas[$i];
        $id_siswa = $siswa->id_siswa;
        foreach ($mapels as $mapel) {
            $dummySikap = ['predikat' => ''];
            $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
            $sikap[$siswa->id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
            $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
            $sikap[$siswa->id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];
            $dummyNilai = ['k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
            $nr = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$id_siswa][$mapel->id_mapel] = $nr == null ? $dummyNilai : $nr;
            $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
            $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
            $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => '', 'saran' => ''];
            $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
            $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa]->nilai : ['nilai' => $dummyAbsen];
            if ($setting_rapor->kkm_tunggal == '1') {
                $kkm[$mapel->id_mapel] = $setting_rapor;
            } else {
                $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            }
            foreach ($ekstras as $ext) {
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    if (!($id_ekstra != null)) {
                    } else {
                        $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                        $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                    }
                }
            }
        }
        $i++;
    }
    public function dkn()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $setting = $this->dashboard->getSetting();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Daftar Kumpulan Nilai Kelas ', 'subjudul' => 'Cetak DKN ', 'setting' => $setting];
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelases = $this->kelas->get_one($id_kelas);
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $mapels = $this->master->getAllMapel();
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }
        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm = [];
        $sikap = [];
        $nilai = [];
        $nilaiPts = [];
        $desks = [];
        $absensi = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];
        $i = 0;
        if (!($i < count($siswas))) {
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['kelases'] = $kelases;
            $data['mapels'] = $mapels;
            $data['siswas'] = $siswas;
            $data['ekstras'] = $ekstras;
            $data['nilai'] = $nilai;
            $data['nilai_pts'] = $nilaiPts;
            $data['sikap'] = $sikap;
            $data['deskripsi'] = $desks;
            $data['absensi'] = $absensi;
            $data['nilai_ekstra'] = $nilaiEkstra;
            $data['mapel_ekstra'] = $mapelEkstra;
            $data['kkm'] = $kkm;
            $data['rapor'] = $setting_rapor;
            $data['naik'] = $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/dkn/data');
            $this->load->view('members/guru/templates/footer');
        } else {
            $siswa = $siswas[$i];
            $id_siswa = $siswa->id_siswa;
            foreach ($mapels as $mapel) {
                $dummySikap = ['predikat' => ''];
                $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '1');
                $sikap[$siswa->id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
                $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt, '2');
                $sikap[$siswa->id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];
                $dummyNilai = ['mapel' => $mapel->nama_mapel, 'k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
                $nr = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
                $nr['mapel'] = $mapel->nama_mapel;
                $nilai[$id_siswa][$mapel->id_mapel] = $nr == null ? $dummyNilai : $nr;
                $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
                $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;
                $dummyDesks = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => '', 'saran' => ''];
                $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
                $nd = $this->rapor->getRaporDeskripsi($id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                $desks[$id_siswa] = $nd == null ? json_decode(json_encode($dummyDesks)) : $nd;
                $absensi[$id_siswa] = $nd == null ? $dummyAbsen : unserialize($nd->nilai);
                if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $setting_rapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
                foreach ($ekstras as $ext) {
                    $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                    $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                    foreach ($arrEkstra as $ar) {
                        $id_ekstra = $ar->ekstra;
                        $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                        if (!($id_ekstra != null)) {
                        } else {
                            $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                            $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                        }
                    }
                }
            }
            $i++;
            if (!($i < count($siswas))) {
            }
        }
    }
}
```

---

## File: application/controllers_progress/Settings.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Settings extends CI_Controller
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
        $data = ['user' => $user, 'judul' => 'Profile Sekolah', 'subjudul' => '', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function dbManager()
    {
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'Backup dan Restore', 'subjudul' => 'Backup dan Restore'];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $data['setting'] = $this->settings->getSetting();
        $data['list'] = directory_map('./backups/');
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/db');
        $this->load->view('_templates/dashboard/_footer');
    }
    function uploadFile($logo)
    {
        if (isset($_FILES['logo']['name'])) {
            $config['upload_path'] = './uploads/settings/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = $logo;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('logo')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/settings/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $data['type'] = $_FILES['logo']['type'];
            $data['size'] = $_FILES['logo']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!unlink($file_name)) {
        } else {
            echo 'File Delete Successfully';
        }
    }
    public function saveSetting()
    {
        $sekolah = $this->input->post('nama_sekolah', true);
        $nss = $this->input->post('nss', true);
        $npsn = $this->input->post('npsn', true);
        $jenjang = $this->input->post('jenjang', true);
        $satuan_pendidikan = $this->input->post('satuan_pendidikan', true);
        $alamat = $this->input->post('alamat', true);
        $kota = $this->input->post('kota', true);
        $desa = $this->input->post('desa', true);
        $kec = $this->input->post('kec', true);
        $prov = $this->input->post('provinsi', true);
        $kodepos = $this->input->post('kode_pos', true);
        $tlp = $this->input->post('tlp', true);
        $web = $this->input->post('web', true);
        $fax = $this->input->post('fax', true);
        $email = $this->input->post('email', true);
        $kepsek = $this->input->post('kepsek', true);
        $nip = $this->input->post('nip', true);
        $tanda_tangan = $this->input->post('tanda_tangan', true);
        $nama_aplikasi = $this->input->post('nama_aplikasi', true);
        $logo_kanan = $this->input->post('logo_kanan', true);
        $logo_kiri = $this->input->post('logo_kiri', true);
        $insert = ['sekolah' => $sekolah, 'nss' => $nss, 'npsn' => $npsn, 'jenjang' => $jenjang, 'satuan_pendidikan' => $satuan_pendidikan, 'alamat' => $alamat, 'desa' => $desa, 'kota' => $kota, 'kecamatan' => $kec, 'kode_pos' => $kodepos, 'provinsi' => $prov, 'web' => $web, 'fax' => $fax, 'email' => $email, 'telp' => $tlp, 'kepsek' => $kepsek, 'nip' => $nip, 'tanda_tangan' => str_replace(base_url(), '', $tanda_tangan ?? ''), 'nama_aplikasi' => $nama_aplikasi, 'logo_kanan' => str_replace(base_url(), '', $logo_kanan ?? ''), 'logo_kiri' => str_replace(base_url(), '', $logo_kiri ?? '')];
        $this->db->where('id_setting', 1);
        $update = $this->db->update('setting', $insert);
        $this->output_json($update);
    }
}
```

---

## File: application/controllers_progress/Siswa.php

```php
<?php

class Siswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library('upload');
        } else {
            redirect('auth');
            $this->load->library('upload');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('user_agent');
        $this->form_validation->set_error_delimiters('', '');
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
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            } else {
                sort($subArray);
            }
        }
    }
    public function index()
    {
    }
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
        $offset = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_post', $id_post);
        $this->db->limit($perPage, $offset);
        $comment = $this->db->get()->result();
        $this->output_json($comment);
    }
    public function getReplies($id_comment, $page)
    {
        $perPage = 5;
        $offset = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id_comment);
        $this->db->limit($perPage, $offset);
        $replies = $this->db->get()->result();
        $this->output_json($replies);
    }
    public function saveKomentar()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $dari = $siswa->id_siswa;
        $dari_group = 3;
        $data = ['type' => '1', 'id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $insert = $this->db->replace('post_comments', $data);
        $id = $this->db->insert_id();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id);
        $comment = $this->db->get()->result();
        $this->output_json($comment);
    }
    public function saveBalasan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Post_model', 'post');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $dari = $siswa->id_siswa;
        $dari_group = 3;
        $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $insert = $this->db->replace('post_reply', $data);
        $id = $this->db->insert_id();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_reply', $id);
        $replies = $this->db->get()->result();
        $this->output_json($replies);
    }
    public function jadwalPelajaran()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Jadwal Pelajaran', 'subjudul' => 'Set Jadwal Pelajaran', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        if ($jadk == null) {
            $data['jadwal_kbm'] = json_decode(json_encode(['id_tp' => $tp->tahun, 'id_smt' => $smt->smt, 'id_kelas' => $siswa->id_kelas, 'kbm_jam_pel' => '', 'kbm_jam_mulai' => '', 'kbm_jml_mapel_hari' => '', 'istirahat' => serialize([]), 'ada' => false]));
        } else {
            $data['jadwal_kbm'] = $jadk;
        }
        $data['id_kelas'] = $siswa->id_kelas;
        $jadm = $this->kelas->getJadwalMapelGroupJam($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $jml_mapel = $jadk == null ? 1 : $jadk->kbm_jml_mapel_hari;
        if ($jadm == null) {
        }
        foreach ($jadm as $j) {
            $jadwal_mapel[] = ['jadwal' => $this->kelas->getJadwalMapelByHari($tp->id_tp, $smt->id_smt, $j->jam_ke, $siswa->id_kelas)];
        }
        $data['method'] = 'edit';
        $data['jadwal_mapel'] = $jadwal_mapel;
        $data['mapels'] = $this->master->getAllMapel();
        $data['running_text'] = $this->dashboard->getRunningText();
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Absensi', 'subjudul' => 'Kehadiran Siswa', 'setting' => $this->dashboard->getSetting()];
        $today = date('Y-m-d');
        $day = date('N', strtotime($today));
        $kbm = $this->dashboard->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $result = $this->dashboard->loadJadwalHariIni($tp->id_tp, $smt->id_smt, $siswa->id_kelas, null);
        $jadwals = [];
        foreach ($result as $row) {
            $jadwals[$row->id_hari][$row->jam_ke] = $row;
        }
        $mapels = $this->master->getAllMapel();
        $arrIdMapel = [];
        foreach ($mapels as $mpl) {
            array_push($arrIdMapel, $mpl->id_mapel);
        }
        if ($kbm != null) {
            $bulan = date('m');
            $tahun = date('Y');
            $tgl = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $materi_sebulan = [];
            $i = 0;
            if (!($i < $tgl)) {
            }
            $t = $i + 1 < 10 ? '0' . ($i + 1) : $i + 1;
            $materi_sebulan[$t] = $this->kelas->getAllMateriByTgl($siswa->id_kelas, $tahun . '-' . $bulan . '-' . $t, $arrIdMapel);
            $i++;
        } else {
            $data['sebulan'] = ['log' => [], 'materis' => []];
            $data['kbm'] = $kbm;
            $data['mapels'] = $mapels;
            $data['jadwals'] = $jadwals;
            $data['jadwal'] = isset($jadwals[$day]) && $day != 7 ? $jadwals[$day] : [];
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $tp;
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $smt;
            $data['running_text'] = $this->dashboard->getRunningText();
            $this->load->view('members/siswa/templates/header', $data);
            $this->load->view('members/siswa/absensi/data');
            $this->load->view('members/siswa/templates/footer');
        }
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => $jenis == '1' ? 'Materi' : 'Tugas', 'subjudul' => $jenis == '1' ? 'materi' : 'tugas', 'setting' => $setting];
        $jenis == null ? '1' : '2';
        $today = date('Y-m-d');
        $jadwal_seminggu = $this->kelas->loadJadwalSiswaSeminggu($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $materi_seminggu = $this->kelas->getMateriSiswaSeminggu($tp->id_tp, $smt->id_smt, $siswa->id_kelas, $jenis);
        $mapels = $this->dropdown->getAllMapel();
        $last_week = [date('Y-m-d', strtotime('-7 days')), date('Y-m-d', strtotime('-6 days')), date('Y-m-d', strtotime('-5 days')), date('Y-m-d', strtotime('-4 days')), date('Y-m-d', strtotime('-3 days')), date('Y-m-d', strtotime('-2 days')), date('Y-m-d', strtotime('-1 days')), date('Y-m-d')];
        $materis = [];
        $logs = [];
        foreach ($last_week as $day) {
            $idhari = date('N', strtotime($day));
            $materis[$day] = [];
            if (!isset($jadwal_seminggu[$idhari])) {
            } else {
                foreach ($jadwal_seminggu[$idhari] as $kjam => $val) {
                    $dummy = new stdClass();
                    $dummy->id_mapel = $val->id_mapel;
                    $dummy->id_jadwal = $val->id_jadwal;
                    $dummy->nama_mapel = isset($mapels[$val->id_mapel]) ? $mapels[$val->id_mapel] : '';
                    $materis[$day][$kjam] = isset($materi_seminggu[$day]) && isset($materi_seminggu[$day][$kjam]) ? $materi_seminggu[$day][$kjam] : $dummy;
                }
                $arrIdKjms = [];
                foreach ($materis[$day] as $mtr) {
                    if (!isset($mtr->id_kjm)) {
                    } else {
                        array_push($arrIdKjms, $mtr->id_kjm);
                    }
                }
                $log = [];
                if (!(count($arrIdKjms) > 0)) {
                }
                $log = $this->kelas->getStatusMateriSiswaByJadwal($siswa->id_siswa, $arrIdKjms);
                $logs[$day] = $log;
            }
        }
        $data['week'] = $last_week;
        $data['jadwals'] = $jadwal_seminggu;
        $data['materis'] = $materis;
        $data['logs'] = $logs;
        $data['jenis'] = $jenis;
        $data['kbm'] = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jurusan'] = $this->dropdown->getAllJurusan();
        $data['level'] = $this->dropdown->getAllLevel($setting->jenjang);
        $data['kelas'] = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['running_text'] = $this->dashboard->getRunningText();
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
        $tgl = $this->input->get('tgl', true);
        $jenis = $this->input->get('jenis', true);
        $mapels = $this->dropdown->getAllMapel();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $today = date($tgl);
        $numday = date('N', strtotime($tgl));
        $jadwal = $this->kelas->loadJadwalSiswaHariIni($tp->id_tp, $smt->id_smt, $id_kelas, $numday);
        $materi_hari_ini = $this->kelas->getMateriSiswa($id_kelas, $today, $jenis);
        $materi = [];
        foreach ($jadwal as $key => $value) {
            $materi['materi'][$key] = isset($materi_hari_ini[$key]) ? $materi_hari_ini[$key] : ['id_mapel' => $value->id_mapel, 'id_jadwal' => $value->id_jadwal, 'nama_mapel' => isset($mapels[$value->id_mapel]) ? $mapels[$value->id_mapel] : ''];
        }
        $arrIdKjm = [];
        foreach ($materi['materi'] as $mtr) {
            if (!isset($mtr->id_kjm)) {
            } else {
                array_push($arrIdKjm, $mtr->id_kjm);
            }
        }
        if (!(count($arrIdKjm) > 0)) {
            $materi['jadwal'] = $jadwal;
        } else {
            $materi['logs'] = (array) $this->kelas->getStatusMateriSiswaByJadwal($id_siswa, $arrIdKjm);
            $materi['jadwal'] = $jadwal;
        }
        $jadk = $this->kelas->getJadwalKbm($tp->id_tp, $smt->id_smt, $id_kelas);
        $jadk->istirahat = unserialize($jadk->istirahat ?? '');
        $materi['kbm'] = $jadk;
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => $jenis == '1' ? 'Materi' : 'Tugas', 'subjudul' => 'Kerjakan', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['jamke'] = $jamke;
        $data['materi'] = $this->kelas->getMateriKelasSiswa($id_kjm, $jenis);
        $logs = $this->kelas->getStatusMateriSiswa($id_kjm);
        if (!isset($logs[$siswa->id_siswa])) {
            $data['kjm'] = $id_kjm;
        } else {
            $logs[$siswa->id_siswa]->file = unserialize($logs[$siswa->id_siswa]->file ?? '');
            $data['kjm'] = $id_kjm;
        }
        $data['logs'] = isset($logs[$siswa->id_siswa]) ? $logs[$siswa->id_siswa] : null;
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/materi/view');
        $this->load->view('members/siswa/templates/footer');
    }
    public function saveLogMateri()
    {
        $this->load->model('Kelas_model', 'kelas');
        $id_siswa = $this->input->get('id_siswa', true);
        $id_kjm = $this->input->get('id_kjm', true);
        $jamke = $this->input->get('jamke', true);
        $mapel = $this->input->get('mapel', true);
        $this->output_json($this->kelas->saveLog('log_materi', $id_siswa, $id_kjm, $jamke, $mapel, 'Membuka materi'));
    }
    public function saveLogTugas()
    {
        $this->load->model('Kelas_model', 'kelas');
        $id_siswa = $this->input->get('id_siswa', true);
        $id_kjm = $this->input->get('id_kjm', true);
        $jamke = $this->input->get('jamke', true);
        $mapel = $this->input->get('mapel', true);
        $this->output_json($this->kelas->saveLog('log_materi', $id_siswa, $id_kjm, $jamke, $mapel, 'Membuka tugas'));
    }
    public function saveFileMateriSelesai()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $id_kjm = $this->input->post('id_kjm', true);
        $isi_materi = $this->input->post('isi_materi', true);
        $jamke = $this->input->post('jamke', true);
        $attach = json_decode($this->input->post('attach', true));
        $src_file = [];
        foreach ($attach as $at) {
            if (!($at->name != null)) {
            } else {
                $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
            }
        }
        $id_log = $id_siswa . $id_kjm;
        $insert = ['id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'finish_time' => date('Y-m-d H:i:s'), 'jam_ke' => $jamke, 'log_desc' => 'Menyelesaikan materi', 'text' => $isi_materi, 'file' => serialize($src_file)];
        $this->db->where('id_log', $id_log);
        $q = $this->db->get('log_materi');
        if ($q->num_rows() > 0) {
            $this->db->where('id_log', $id_log);
            $update = $this->db->update('log_materi', $insert);
        } else {
            $this->db->set('id_log', $id_log);
            $update = $this->db->insert('log_materi', $insert);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function saveFileTugasSelesai()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $id_kjm = $this->input->post('id_kjm', true);
        $isi_tugas = $this->input->post('isi_tugas', true);
        $jamke = $this->input->post('jamke', true);
        $attach = json_decode($this->input->post('attach', true));
        $src_file = [];
        foreach ($attach as $at) {
            if (!($at->name != null)) {
            } else {
                $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
            }
        }
        $id_log = $id_siswa . $id_kjm;
        $insert = ['id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'jam_ke' => $jamke, 'log_desc' => 'Menyelesaikan tugas', 'text' => $isi_tugas, 'file' => serialize($src_file)];
        $this->db->where('id_log', $id_log);
        $q = $this->db->get('log_tugas');
        if ($q->num_rows() > 0) {
            $this->db->where('id_log', $id_log);
            $update = $this->db->update('log_tugas', $insert);
        } else {
            $this->db->set('id_log', $id_log);
            $update = $this->db->insert('log_tugas', $insert);
        }
        $data['status'] = $update;
        $this->output_json($data);
    }
    function uploadFile()
    {
        $max_size = $this->input->post('max-size', true);
        if (!isset($_FILES['file_uploads']['name'])) {
            $this->output_json($data);
        } else {
            $config['upload_path'] = './uploads/file_siswa/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi|doc|docx|xls|xlsx|ppt|pptx|csv|pdf|rtf|txt';
            $config['max_size'] = $max_size;
            $config['overwrite'] = FALSE;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('file_uploads')) {
            }
            $result = $this->upload->data();
            $data['src'] = 'uploads/file_siswa/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $data['type'] = $_FILES['file_uploads']['type'];
            $data['size'] = $_FILES['file_uploads']['size'];
            $this->output_json($data);
        }
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        if (!unlink($src)) {
        } else {
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $today = strtotime(date('Y-m-d'));
        $cbt_info = $this->cbt->getSiswaCbtInfo($siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $cbt_info->no_peserta = $this->cbt->getNomorPeserta($siswa->id_siswa);
        $cbt_jadwal = $this->cbt->getJadwalCbt($tp->id_tp, $smt->id_smt, $siswa->level_id);
        $jadwal_ujian_aktif = [];
        $timer = [];
        foreach ($cbt_jadwal as $key => $jadwal) {
            $kk = unserialize($jadwal->bank_kelas ?? '');
            $arrKelasCbt = [];
            foreach ($kk as $k) {
                array_push($arrKelasCbt, $k['kelas_id']);
            }
            if (!($cbt_info != null && in_array($cbt_info->id_kelas, $arrKelasCbt) && $jadwal->status === '1')) {
                $timer[$jadwal->id_jadwal] = $this->cbt->getElapsed($siswa->id_siswa . '0' . $jadwal->id_jadwal);
            } else {
                $mulai = strtotime($jadwal->tgl_mulai);
                $selesai = strtotime($jadwal->tgl_selesai);
                if (!($today >= $mulai && $today <= $selesai)) {
                }
                if (!($jadwal->soal_agama == '-' || $jadwal->soal_agama == '0' || $jadwal->soal_agama == $siswa->agama)) {
                }
                if (isset($jadwal_ujian_aktif[$jadwal->tgl_mulai])) {
                }
                $jadwal_ujian_aktif[$jadwal->tgl_mulai] = [];
                array_push($jadwal_ujian_aktif[$jadwal->tgl_mulai], $jadwal);
                $timer[$jadwal->id_jadwal] = $this->cbt->getElapsed($siswa->id_siswa . '0' . $jadwal->id_jadwal);
            }
        }
        $data['cbt_info'] = $cbt_info;
        $data['cbt_jadwal'] = $jadwal_ujian_aktif;
        $data['guru'] = $this->cbt->getDataGuru();
        $data['sesi'] = $this->dropdown->getAllWaktuSesi();
        $data['elapsed'] = $timer;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/cbt/data');
        $this->load->view('members/siswa/templates/footer');
    }
    public function konfirmasi($id_jadwal)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['running_text'] = $this->dashboard->getRunningText();
        $curr_address = $this->input->ip_address();
        if ($this->agent->is_browser()) {
            $curr_agent = $this->agent->browser() . ' ' . $this->agent->version();
        } else {
            if ($this->agent->is_mobile()) {
            }
            $curr_agent = 'unknown';
        }
        $curr_device = $this->agent->platform();
        $data['support'] = $curr_agent != 'unknown';
        $info = $this->cbt->getJadwalById($id_jadwal);
        if ($info->reset_login == '1') {
        }
        $valid = true;
        $data['valid'] = $valid;
        if (!$valid) {
        }
        $bank = $this->cbt->getCbt($id_jadwal);
        $data['kelas'] = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
        $guru = $this->cbt->getDataGuru();
        $cbt_info = $this->cbt->getSiswaCbtInfo($siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $pengawass = $this->cbt->getPengawas($tp->id_tp . $smt->id_smt . $id_jadwal . $cbt_info->id_ruang . $cbt_info->id_sesi);
        $pengawas = [];
        if (!($pengawass != null && count(explode(',', $pengawass->id_guru ?? '')) > 0)) {
        }
        $pengawas = $this->master->getGuruByArrId(explode(',', $pengawass->id_guru ?? ''));
        $data['bank'] = $bank;
        $data['guru'] = $guru;
        $data['pengawas'] = $pengawas;
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/cbt/konfirmasi');
        $this->load->view('members/siswa/templates/footer');
    }
    public function validasiSiswa()
    {
        $id_jadwal = $this->input->post('jadwal');
        $id_siswa = $this->input->post('siswa');
        $id_bank = $this->input->post('bank');
        $token_siswa = $this->input->post('token');
        $this->load->model('Cbt_model', 'cbt');
        $this->db->trans_start();
        $info = $this->cbt->getJadwalById($id_jadwal);
        $token_valid = true;
        if (!($info->token == '1')) {
            $data['token'] = $token_valid;
        } else {
            $token = $this->cbt->getToken();
            if ($token == null) {
            }
            $token_valid = $token->token == $token_siswa ? true : false;
            $data['token_msg'] = $token_valid ? '' : 'Token salah';
            $data['token'] = $token_valid;
        }
        if (!$token_valid) {
        }
        $curr_address = $this->input->ip_address();
        if ($this->agent->is_browser()) {
        }
        if ($this->agent->is_mobile()) {
        }
        $curr_agent = 'unknown';
        $curr_device = $this->agent->platform();
        $support = $curr_agent != 'unknown';
        $data['support'] = $support;
        if (!$support) {
        }
        $mulai_baru = false;
        $cek_reset_waktu = false;
        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . '1')->get('log_ujian')->row();
        if ($log == null) {
        }
        if ($info->reset_login == '1') {
        }
        $izinkan = true;
        $mulai_baru = false;
        $data['izinkan'] = $izinkan;
        $data['log'] = $log;
        $mulai_baru_d = false;
        $ada_waktu = false;
        if (!($izinkan || $cek_reset_waktu)) {
        }
        $elapsed = $this->cbt->getElapsed($id_siswa . '0' . $id_jadwal);
        if ($elapsed == null) {
        }
        $mulai_baru_d = $elapsed->reset == '3';
        if ($elapsed->reset == '1') {
        }
        if ($elapsed->reset == '2') {
        }
        if ($elapsed->reset == '3') {
        }
        $mulai = new DateTime($elapsed->mulai);
        $interval = $mulai->diff(new DateTime());
        $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
        $data['interval'] = ['days' => $interval->days, 'hari' => $interval->d, 'jam' => $interval->h, 'menit' => $interval->i, 'detik' => $interval->s, 'total' => $minutes];
        $ada_waktu = $minutes < $info->durasi_ujian;
        $data['warn'] = ['durasi_ujian' => $info->durasi_ujian, 'siswa_mulai' => $elapsed->mulai, 'durasi_siswa' => $elapsed->lama_ujian, 'timer_elapsed' => $minutes, 'terlampaui' => $minutes - $info->durasi_ujian, 'status' => $ada_waktu ? 0 : 1, 'msg' => $ada_waktu ? '' : 'Waktu ujian sudah habis'];
        $data['ada_waktu'] = $ada_waktu;
        $data['elapsed'] = $this->cbt->getElapsed($id_siswa . '0' . $id_jadwal);
        if (!$ada_waktu) {
        }
        $soal = $this->cbt->getJumlahSoalSiswa($id_bank, $id_siswa);
        if ($soal > 0) {
        }
        $nomor_soal = $this->createQueueNumber($id_siswa, $id_bank, $id_jadwal);
        if (!(count($nomor_soal) > 0)) {
        }
        $this->db->insert_batch('cbt_soal_siswa', $nomor_soal);
        $data['jml_soal'] = $this->cbt->getJumlahSoalSiswa($id_bank, $id_siswa);
        $this->db->trans_complete();
        $this->output_json($data);
    }
    public function createQueueNumber($id_siswa, $id_bank, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $cek_soal = $this->cbt->getAllIdSoal($id_bank);
        $jadwal = $this->cbt->getInfoJadwal($id_bank);
        $num1 = isset($cek_soal['1']) ? count($cek_soal['1']) : 0;
        $num2 = isset($cek_soal['2']) ? count($cek_soal['2']) : 0;
        $num3 = isset($cek_soal['3']) ? count($cek_soal['3']) : 0;
        $num4 = isset($cek_soal['4']) ? count($cek_soal['4']) : 0;
        $num5 = isset($cek_soal['5']) ? count($cek_soal['5']) : 0;
        $total = $num1 + $num2 + $num3 + $num4 + $num5;
        $ada1 = $num1 == (int) $jadwal->tampil_pg;
        $ada2 = $num2 == (int) $jadwal->tampil_kompleks;
        $ada3 = $num3 == (int) $jadwal->tampil_jodohkan;
        $ada4 = $num4 == (int) $jadwal->tampil_isian;
        $ada5 = $num5 == (int) $jadwal->tampil_esai;
        if ($ada1 && $ada2 && $ada3 && $ada4 && $ada5) {
            $opsis = $jadwal->opsi;
            if ($opsis == '2') {
            }
            if ($opsis == '3') {
            }
            if ($opsis == '4') {
            }
            $arrOpsi = ['A', 'B', 'C', 'D', 'E'];
            $arrNum = range(1, $total);
            if (!($jadwal->acak_soal == '1')) {
            }
            shuffle($arrNum);
            $items = [];
            $j = 0;
            foreach ($cek_soal as $jenis => $soals) {
                foreach ($soals as $soal) {
                    if (!($jenis == '1')) {
                        $item_soal['id_soal_siswa'] = $id_siswa . '0' . $id_jadwal . $id_bank . $arrNum[$j];
                    } else {
                        if (!($jadwal->acak_opsi == '1')) {
                        }
                        shuffle($arrOpsi);
                        $item_soal['id_soal_siswa'] = $id_siswa . '0' . $id_jadwal . $id_bank . $arrNum[$j];
                    }
                    $item_soal['id_bank'] = $id_bank;
                    $item_soal['id_jadwal'] = $id_jadwal;
                    $item_soal['id_soal'] = $soal->id_soal;
                    $item_soal['id_siswa'] = $id_siswa;
                    $item_soal['jenis_soal'] = $jenis;
                    $item_soal['no_soal_alias'] = $arrNum[$j];
                    if ($jenis == '1') {
                    }
                    if ($jenis == '2') {
                    }
                    if ($jenis == '3') {
                    }
                    if ($jenis == '4') {
                    }
                    if ($jenis == '5') {
                    }
                    $item_soal['jawaban_benar'] = $soal->jawaban;
                    $item_soal['soal_end'] = $j + 1 === count($arrNum) ? '1' : '0';
                    array_push($items, $item_soal);
                    $j++;
                }
            }
            usort($items, function ($a, $b) {
                return $a['no_soal_alias'] <=> $b['no_soal_alias'];
            });
            return $items;
        } else {
            return [];
        }
    }
    public function penilaian($id_jadwal)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Penilaian', 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['running_text'] = $this->dashboard->getRunningText();
        $data['jadwal'] = $this->cbt->getCbt($id_jadwal);
        $id_durasi = $siswa->id_siswa . '0' . $id_jadwal;
        $durasi = $this->cbt->getElapsed($id_durasi);
        $mulai = new DateTime($durasi->mulai);
        $diff = $mulai->diff(new DateTime());
        $durasi->diff = ['days' => $diff->days, 'hari' => $diff->d, 'jam' => $diff->h, 'menit' => $diff->i, 'detik' => $diff->s, 'format' => $diff->format('%H:%I:%S')];
        if (!($durasi == null || $durasi->selesai != null)) {
            $data['elapsed'] = $durasi;
        } else {
            redirect('siswa/cbt');
            $data['elapsed'] = $durasi;
        }
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/cbt/ujian');
        $this->load->view('members/siswa/templates/footer');
    }
    public function checkTimer($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_durasi = $id_siswa . '0' . $id_jadwal;
        $durasi = $this->cbt->getElapsed($id_durasi);
        if ($durasi != null) {
            $mulai = new DateTime($durasi->mulai);
            $diff = $mulai->diff(new DateTime());
            $elapsed = $diff->format('%H:%I:%S');
            if ($durasi->reset == '0') {
            }
            if ($durasi->reset == '1') {
            }
            if ($durasi->reset == '3') {
            }
            $this->db->set('lama_ujian', $elapsed);
            $this->db->set('reset', 0);
            $this->db->where('id_durasi', $id_durasi);
            $this->db->update('cbt_durasi_siswa');
            $durasi = $this->cbt->getElapsed($id_durasi);
            return $durasi;
        } else {
            $durasi = false;
            return $durasi;
        }
    }
    public function loadNomorSoal()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $id_siswa = $this->input->post('siswa');
        $id_jadwal = $this->input->post('jadwal');
        $id_bank = $this->input->post('bank');
        $nomor = $this->input->post('nomor');
        $timer = $this->input->post('timer');
        $durasi = $this->checkTimer($id_siswa, $id_jadwal);
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswa = $this->cbt->getDataSiswaById($tp->id_tp, $smt->id_smt, $id_siswa);
        $soals = $this->cbt->getALLSoalSiswa($id_bank, $siswa->id_siswa);
        $s = 0;
        if (!($s < count($soals))) {
            $id_soal_siswa = $siswa->id_siswa . '0' . $id_jadwal . $id_bank . $nomor;
            $ind_soal = array_search($id_soal_siswa, array_column($soals, 'id_soal_siswa'));
            $item_soal = $soals[$ind_soal];
            $max_jawaban = [];
            if ($item_soal->jenis_soal == '1') {
            }
            if ($item_soal->jenis_soal == '2') {
            }
            if ($item_soal->jenis_soal == '3') {
            }
            $opsis = [];
            $data['durasi'] = $durasi;
            $data['timer'] = $timer;
            $data['soal_id'] = $item_soal->id_soal;
            $data['soal_siswa_id'] = $item_soal->id_soal_siswa;
            $data['soal_nomor'] = $item_soal->no_soal_alias;
            $data['soal_nomor_asli'] = $item_soal->nomor_soal;
            $data['soal_jenis'] = $item_soal->jenis_soal;
            $data['soal_soal'] = $item_soal->soal;
            $data['soal_opsi'] = json_decode(json_encode($opsis));
            $data['soal_jawaban_siswa'] = $item_soal->jawaban_siswa;
            $data['max_jawaban'] = $max_jawaban;
            $arrJawaban = [];
            $modal = '<div class="d-flex flex-wrap justify-content-center grid-nomor-pg">';
            foreach ($soals as $key => $soal) {
                if ($soal->jawaban_siswa != null) {
                    if ($soal->jenis_soal === '3') {
                    }
                    $terjawab = $soal->jawaban_siswa != '';
                } else {
                    $terjawab = false;
                }
                $color = !$terjawab ? 'outline-secondary' : 'primary';
                $selected = $nomor == $soal->no_soal_alias ? 'active' : '';
                $modal .= '<div class="mb-4">' . '<div id="box' . $soal->no_soal_alias . '" class="d-flex flex-column" style="width: 70px; height: 60px;">' . '<button id="btn' . $soal->no_soal_alias . '" class="btn btn-' . $color . ' border border-dark ' . $selected . '" ' . 'data-pos="' . $key . '" data-nomorsoal="' . $soal->no_soal_alias . '" ' . 'data-idsoal="' . $soal->id_soal . '" data-jenis="' . $soal->jenis_soal . '" ' . 'onclick="loadSoal(this)" ' . 'style="width: 50px; height: 50px;">' . '<span style="font-size: 14pt"><b>' . $soal->no_soal_alias . '</b></span>' . '</button>';
                if (!$terjawab) {
                }
                $txt_badge = $soal->jenis_soal == '1' ? $soal->jawaban_alias : '&check;';
                array_push($arrJawaban, $soal->jawaban_alias);
                $modal .= '<div id="badge' . $soal->no_soal_alias . '" class="badge badge-pill badge-success border border-dark"' . ' style="font-size:12pt; width: 30px; height: 30px; margin-top: -60px; margin-left: 30px;">' . $txt_badge . '</div>';
                $modal .= '</div></div>';
            }
            $modal .= '</div>';
            $data['soal_modal'] = $modal;
            $data['soal_total'] = count($soals);
            $data['soal_terjawab'] = count($arrJawaban);
            $data['soal_akhir'] = $modal;
            $this->output_json($data);
        } else {
            if (!($soals[$s]->jenis_soal == '3')) {
            }
            $soals[$s]->jawaban = unserialize($soals[$s]->jawaban ?? '');
            $ada_jawab = $soals[$s]->jawaban_siswa != null;
            if (!$ada_jawab) {
            }
            $soals[$s]->jawaban_siswa = unserialize($soals[$s]->jawaban_siswa ?? '');
            $s++;
            if (!($s < count($soals))) {
            }
        }
    }
    public function saveSoalSiswa()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $shuffle = json_decode($this->input->post('shuffle', false));
        foreach ($shuffle as $s) {
            $id_siswa = $s->id_siswa;
            $id_jadwal = $s->id_jadwal;
            $id_bank = $s->id_bank;
            $jenis = $s->jenis;
            $nomor = $s->nomor_soal;
            $soal = $this->cbt->getSoalByNomor($id_bank, $nomor, $jenis);
            $id_soal = $soal->id_soal;
            $this->db->where('id_soal_siswa', $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor);
            $jml = $this->db->get('cbt_soal_siswa')->num_rows();
            if ($jml > 0) {
                $insert = ['id_bank' => $id_bank, 'id_jadwal' => $id_jadwal, 'id_soal' => $id_soal, 'id_siswa' => $id_siswa, 'jenis_soal' => $jenis, 'no_soal_alias' => $s->no_soal_alias, 'opsi_alias_a' => isset($s->opsi_alias_a) ? $s->opsi_alias_a : null, 'opsi_alias_b' => isset($s->opsi_alias_b) ? $s->opsi_alias_b : null, 'opsi_alias_c' => isset($s->opsi_alias_c) ? $s->opsi_alias_c : null, 'opsi_alias_d' => isset($s->opsi_alias_d) ? $s->opsi_alias_d : null, 'opsi_alias_e' => isset($s->opsi_alias_e) ? $s->opsi_alias_e : null, 'jawaban_benar' => $soal->jawaban, 'soal_end' => $s->soal_end];
                $this->master->update('cbt_soal_siswa', $insert, 'id_soal_siswa', $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor);
            } else {
                $insert = ['id_soal_siswa' => $id_siswa . '0' . $id_jadwal . $id_bank . $jenis . $nomor, 'id_bank' => $id_bank, 'id_jadwal' => $id_jadwal, 'id_soal' => $id_soal, 'id_siswa' => $id_siswa, 'jenis_soal' => $jenis, 'no_soal_alias' => $s->no_soal_alias, 'opsi_alias_a' => isset($s->opsi_alias_a) ? $s->opsi_alias_a : null, 'opsi_alias_b' => isset($s->opsi_alias_b) ? $s->opsi_alias_b : null, 'opsi_alias_c' => isset($s->opsi_alias_c) ? $s->opsi_alias_c : null, 'opsi_alias_d' => isset($s->opsi_alias_d) ? $s->opsi_alias_d : null, 'opsi_alias_e' => isset($s->opsi_alias_e) ? $s->opsi_alias_e : null, 'jawaban_benar' => $soal->jawaban, 'soal_end' => $s->soal_end];
                $this->master->create('cbt_soal_siswa', $insert, false);
            }
        }
        $id_siswa = $shuffle[0]->id_siswa;
        $id_bank = $shuffle[0]->id_bank;
        $data['soals'] = $this->cbt->getSoalSiswa($id_bank, $id_siswa);
        $this->output_json($data);
    }
    public function saveLogUjian($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->saveLog($id_siswa, $id_jadwal, 1, 'Memulai Ujian'));
    }
    public function saveJawaban()
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_bank = $this->input->post('bank', true);
        $timer = $this->input->post('waktu', true);
        $id_siswa = $this->input->post('siswa', true);
        $id_jadwal = $this->input->post('jadwal', true);
        $elapsed = $this->input->post('elapsed', true);
        $id_durasi = $id_siswa . '0' . $id_jadwal;
        if (!($elapsed != '0')) {
            $update = true;
        } else {
            $this->db->set('lama_ujian', $elapsed);
            $this->db->where('id_durasi', $id_durasi);
            $this->db->update('cbt_durasi_siswa');
            $update = true;
        }
        $jawab = $this->input->post('data', false);
        if (!($jawab != null && isset($jawab['jenis']))) {
        }
        if ($jawab['jenis'] == 1) {
        }
        if ($jawab['jenis'] == 2) {
        }
        if ($jawab['jenis'] == 3) {
        }
        if ($jawab['jenis'] == 4) {
        }
        $this->db->set('jawaban_alias', '');
        $this->db->set('jawaban_siswa', $jawab['jawaban_siswa']);
        $this->db->where('id_soal_siswa', $jawab['id_soal_siswa']);
        $update = $this->db->update('cbt_soal_siswa');
        $data['status'] = $update;
        if (!($update && $id_bank != null)) {
        }
        $arrJawaban = [];
        $terjawab = $this->cbt->getJumlahJawaban($id_bank, $id_siswa);
        foreach ($terjawab as $jawab) {
            if (!($jawab->jawaban_siswa != null && $jawab->jawaban_siswa != '')) {
            } else {
                array_push($arrJawaban, $jawab);
            }
        }
        $data['soal_terjawab'] = count($arrJawaban);
        if (!($update && $timer != null)) {
        }
        $this->selesaiUjian();
        $this->output_json($data);
    }
    public function selesaiUjian()
    {
        $this->load->model('Cbt_model', 'cbt');
        $id_siswa = $this->input->post('siswa');
        $id_jadwal = $this->input->post('jadwal');
        $data['status_nilai'] = $this->olahNilai($id_siswa, $id_jadwal);
        $this->db->set('selesai', date('Y-m-d H:i:s'));
        $this->db->set('status', 2);
        $this->db->where('id_durasi', $id_siswa . '0' . $id_jadwal);
        $update = $this->db->update('cbt_durasi_siswa');
        $this->cbt->saveLog($id_siswa, $id_jadwal, 2, 'Menyelesaikan Ujian');
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function resetTimer()
    {
        $id_durasi = $this->input->post('id_durasi', true);
        $reset = $this->input->post('reset', true);
        if (!($reset == '1')) {
            $this->db->set('reset', $reset);
        } else {
            $this->db->set('lama_ujian', '00:00:00');
            $this->db->set('reset', $reset);
        }
        $this->db->where('id_durasi', $id_durasi);
        $update = $this->db->update('cbt_durasi_siswa');
        $data['status'] = $update;
        $this->output_json($data);
    }
    public function ulangiUjian($id_durasi, $id_bank)
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Cbt_model', 'cbt');
        $soals = $this->cbt->getAllSoalByBank($id_bank);
        if ($this->master->delete('cbt_durasi_siswa', $id_durasi, 'id_durasi')) {
            $i = 0;
            if (!($i < 2)) {
            }
            foreach ($soals as $soal) {
                $this->db->where('id_soal_siswa', $id_durasi . $id_bank . ($i + 1) . $soal->nomor_soal);
                $this->db->delete('cbt_soal_siswa');
            }
            $i++;
        } else {
            $data['status'] = false;
            $this->output_json($data);
        }
    }
    public function applyAction()
    {
        $this->load->model('Cbt_model', 'cbt');
        $json = json_decode($this->input->post('aksi', true));
        $id_jadwal = $this->input->post('jadwal', true);
        $this->db->trans_start();
        $data['update_reset'] = true;
        if (!(count($json->reset) > 0)) {
            $data['update_selesai'] = true;
        } else {
            $data['reset'] = true;
            $this->db->set('reset', 1);
            $this->db->where_in('id_log', $json->reset);
            $this->db->update('log_ujian');
            $data['update_selesai'] = true;
        }
        if (!(count($json->force) > 0)) {
        }
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
        $data['update_ulangi'] = true;
        if (!(count($json->ulang) > 0)) {
        }
        $data['ulangi'] = true;
        $this->db->where_in('id_durasi', $json->hapus);
        if (!$this->db->delete('cbt_durasi_siswa')) {
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->where_in('id_siswa', $json->ulang);
        if (!$this->db->delete('log_ujian')) {
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->where_in('id_siswa', $json->ulang);
        if (!$this->db->delete('cbt_nilai')) {
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->where_in('id_siswa', $json->ulang);
        $data['update_ulangi'] = $this->db->delete('cbt_soal_siswa');
        $this->db->trans_complete();
        $this->output_json($data);
    }
    public function olahNilai($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $info = $this->cbt->getJadwalById($id_jadwal);
        $jawabans = $this->cbt->getJawabanByBank($info->id_bank, $id_siswa);
        $jawabans_siswa = [];
        foreach ($jawabans as $jawaban_siswa) {
            if (!($jawaban_siswa->jenis_soal == '2')) {
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
            } else {
                $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar);
                $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                if (!($jawaban_siswa->jenis_soal == '3')) {
                }
            }
            $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
            $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
            $arrAlphabet = range('A', 'Z');
            if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
            }
            $arrjwbnSiswa = [];
            if (!$jawaban_siswa->jawaban_siswa) {
            }
            foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                } else {
                    $arrjwbnSiswa[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($idxs > 0)) {
                        } else {
                            if (!($jb === '1')) {
                            }
                            $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                        }
                    }
                }
            }
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $arrjwbn = [];
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                } else {
                    $arrjwbn[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($idxs > 0)) {
                        } else {
                            if (!($jb === '1')) {
                            }
                            $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                        }
                    }
                }
            }
            $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
            $jawabans_siswa[$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
        }
        $ada_jawaban_isian = isset($jawabans_siswa['4']);
        $ada_jawaban_essai = isset($jawabans_siswa['5']);
        $bagi_pg = $info->tampil_pg / 100;
        $bobot_pg = $info->bobot_pg / 100;
        $bagi_pg2 = $info->tampil_kompleks / 100;
        $bobot_pg2 = $info->bobot_kompleks / 100;
        $bagi_jodoh = $info->tampil_jodohkan / 100;
        $bobot_jodoh = $info->bobot_jodohkan / 100;
        $bagi_isian = $info->tampil_isian / 100;
        $bobot_isian = $info->bobot_isian / 100;
        $bagi_essai = $info->tampil_esai / 100;
        $bobot_essai = $info->bobot_esai / 100;
        $jawaban_pg = isset($jawabans_siswa['1']) ? $jawabans_siswa['1'] : [];
        $benar_pg = 0;
        $salah_pg = 0;
        if (!($info->tampil_pg > 0)) {
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            $jawaban_pg2 = isset($jawabans_siswa['2']) ? $jawabans_siswa['2'] : [];
            $benar_pg2 = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!(count($jawaban_pg2) > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $arr_benar = [];
                if (!is_array($jawab_pg2->jawaban_siswa)) {
                    if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                    }
                } else {
                    foreach ($jawab_pg2->jawaban_siswa as $js) {
                        if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                        } else {
                            array_push($arr_benar, true);
                        }
                    }
                    if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                    }
                }
                $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
            }
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;
            $jawaban_jodoh = isset($jawabans_siswa['3']) ? $jawabans_siswa['3'] : [];
            $benar_jod = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod = 0;
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $item_benar = 0;
                $item_salah = 0;
                $item_kurang = 0;
                $items = 0;
                $arrBenar = [];
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                    $point_soal = 1 / $items * $item_benar * $point_benar;
                } else {
                    $array1 = (array) $jawab_jod->jawaban_benar->links;
                    $this->sortArrays($array1);
                    $array2 = (array) $jawab_jod->jawaban_siswa->links;
                    $this->sortArrays($array2);
                    $sameCount = 0;
                    $differentCount = 0;
                    foreach ($array1 as $key => $subArray1) {
                        $arrBenar[$key] = new stdClass();
                        $arrBenar[$key]->benar = 0;
                        $arrBenar[$key]->salah = 0;
                        $arrBenar[$key]->kurang = 0;
                        $items += count($subArray1);
                        if (isset($array2[$key])) {
                            $subArray2 = $array2[$key];
                            $sameItems = array_intersect($subArray1, $subArray2);
                            $item_benar += count($sameItems);
                            $arrBenar[$key]->benar += count($sameItems);
                            $diffItems1 = array_diff($subArray1, $subArray2);
                            $diffItems2 = array_diff($subArray2, $subArray1);
                            $arrBenar[$key]->kurang += count($diffItems1);
                        } else {
                            $arrBenar[$key]->kurang += count($subArray1);
                        }
                    }
                    $point_soal = 1 / $items * $item_benar * $point_benar;
                }
                $benar_jod += 1 / $items * $item_benar;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
            }
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $skor_jod = $otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod;
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa['4'] : [];
            $benar_is = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is = 0;
            if (!($info->tampil_isian > 0)) {
            }
            if (!(count($jawaban_is) > 0)) {
            }
            foreach ($jawaban_is as $num => $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban_benar ?? '');
                if (!$benar) {
                    $otomatis_is = $jawab_is->nilai_otomatis;
                } else {
                    $benar_is++;
                    $otomatis_is = $jawab_is->nilai_otomatis;
                }
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa['5'] : [];
            $benar_es = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es = 0;
            if (!($info->tampil_esai > 0)) {
            }
            if (!(count($jawaban_es) > 0)) {
            }
            foreach ($jawaban_es as $num => $jawab_es) {
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban_benar ?? '');
                if (!$benar) {
                    $otomatis_es = $jawab_es->nilai_otomatis;
                } else {
                    $benar_es++;
                    $otomatis_es = $jawab_es->nilai_otomatis;
                }
            }
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;
            $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
            $insert = ['id_nilai' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'pg_benar' => $benar_pg, 'pg_nilai' => round($skor_pg, 2), 'kompleks_nilai' => round($skor_pg2, 2), 'jodohkan_nilai' => round($skor_jod, 2), 'isian_nilai' => round($skor_is, 2), 'essai_nilai' => round($skor_es, 2)];
            return $this->db->replace('cbt_nilai', $insert);
        } else {
            if (!(count($jawaban_pg) > 0)) {
            }
            foreach ($jawaban_pg as $jwb_pg) {
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                } else {
                    if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban_benar ?? '')) {
                    }
                    $salah_pg += 1;
                }
            }
            $skor_pg = $bagi_pg == 0 ? 0 : $benar_pg / $bagi_pg * $bobot_pg;
            $jawaban_pg2 = isset($jawabans_siswa['2']) ? $jawabans_siswa['2'] : [];
            $benar_pg2 = 0;
            $skor_koreksi_pg2 = 0.0;
            $otomatis_pg2 = 0;
            if (!($info->tampil_kompleks > 0)) {
            }
            if (!(count($jawaban_pg2) > 0)) {
            }
            foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                $arr_benar = [];
                if (!is_array($jawab_pg2->jawaban_siswa)) {
                    if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                    }
                } else {
                    foreach ($jawab_pg2->jawaban_siswa as $js) {
                        if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                        } else {
                            array_push($arr_benar, true);
                        }
                    }
                    if (!(count($jawab_pg2->jawaban_benar) > 0)) {
                    }
                }
                $benar_pg2 += 1 / count($jawab_pg2->jawaban_benar) * count($arr_benar);
            }
            $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
            $skor_pg2 = $otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2;
            $jawaban_jodoh = isset($jawabans_siswa['3']) ? $jawabans_siswa['3'] : [];
            $benar_jod = 0;
            $skor_koreksi_jod = 0.0;
            $otomatis_jod = 0;
            if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
            }
            foreach ($jawaban_jodoh as $num => $jawab_jod) {
                $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                $item_benar = 0;
                $item_salah = 0;
                $item_kurang = 0;
                $items = 0;
                $arrBenar = [];
                $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                if (!isset($jawab_jod->jawaban_siswa->links)) {
                    $point_soal = 1 / $items * $item_benar * $point_benar;
                } else {
                    $array1 = (array) $jawab_jod->jawaban_benar->links;
                    $this->sortArrays($array1);
                    $array2 = (array) $jawab_jod->jawaban_siswa->links;
                    $this->sortArrays($array2);
                    $sameCount = 0;
                    $differentCount = 0;
                    foreach ($array1 as $key => $subArray1) {
                        $arrBenar[$key] = new stdClass();
                        $arrBenar[$key]->benar = 0;
                        $arrBenar[$key]->salah = 0;
                        $arrBenar[$key]->kurang = 0;
                        $items += count($subArray1);
                        if (isset($array2[$key])) {
                            $subArray2 = $array2[$key];
                            $sameItems = array_intersect($subArray1, $subArray2);
                            $item_benar += count($sameItems);
                            $arrBenar[$key]->benar += count($sameItems);
                            $diffItems1 = array_diff($subArray1, $subArray2);
                            $diffItems2 = array_diff($subArray2, $subArray1);
                            $arrBenar[$key]->kurang += count($diffItems1);
                        } else {
                            $arrBenar[$key]->kurang += count($subArray1);
                        }
                    }
                    $point_soal = 1 / $items * $item_benar * $point_benar;
                }
                $benar_jod += 1 / $items * $item_benar;
                $otomatis_jod = $jawab_jod->nilai_otomatis;
            }
            $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
            $skor_jod = $otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod;
            $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa['4'] : [];
            $benar_is = 0;
            $skor_koreksi_is = 0.0;
            $otomatis_is = 0;
            if (!($info->tampil_isian > 0)) {
            }
            if (!(count($jawaban_is) > 0)) {
            }
            foreach ($jawaban_is as $num => $jawab_is) {
                $skor_koreksi_is += $jawab_is->nilai_koreksi;
                $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban_benar ?? '');
                if (!$benar) {
                    $otomatis_is = $jawab_is->nilai_otomatis;
                } else {
                    $benar_is++;
                    $otomatis_is = $jawab_is->nilai_otomatis;
                }
            }
            $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
            $skor_is = $otomatis_is == 0 ? $s_is : $skor_koreksi_is;
            $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa['5'] : [];
            $benar_es = 0;
            $skor_koreksi_es = 0.0;
            $otomatis_es = 0;
            if (!($info->tampil_esai > 0)) {
            }
            if (!(count($jawaban_es) > 0)) {
            }
            foreach ($jawaban_es as $num => $jawab_es) {
                $skor_koreksi_es += $jawab_es->nilai_koreksi;
                $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban_benar ?? '');
                if (!$benar) {
                    $otomatis_es = $jawab_es->nilai_otomatis;
                } else {
                    $benar_es++;
                    $otomatis_es = $jawab_es->nilai_otomatis;
                }
            }
            $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
            $skor_es = $otomatis_es == 0 ? $s_es : $skor_koreksi_es;
            $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
            $insert = ['id_nilai' => $id_siswa . '0' . $id_jadwal, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'pg_benar' => $benar_pg, 'pg_nilai' => round($skor_pg, 2), 'kompleks_nilai' => round($skor_pg2, 2), 'jodohkan_nilai' => round($skor_jod, 2), 'isian_nilai' => round($skor_is, 2), 'essai_nilai' => round($skor_es, 2)];
            return $this->db->replace('cbt_nilai', $insert);
        }
    }
    public function hasil()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Nilai', 'subjudul' => 'Nilai Hasil Belajar', 'setting' => $this->dashboard->getSetting()];
        $logs = $this->kelas->getNilaiMateriSiswa($siswa->id_siswa);
        $data['nilai_materi'] = isset($logs[1]) ? $logs[1] : [];
        $data['nilai_tugas'] = isset($logs[2]) ? $logs[2] : [];
        $this->db->trans_start();
        $jadwals = $this->cbt->getJadwalByKelas($tp->id_tp, $smt->id_smt, $siswa->id_kelas);
        $skors = [];
        $durasies = [];
        $jawabans = [];
        $kelass_unset = [];
        foreach ($jadwals as $kj => $jadwal) {
            $kelass = unserialize($jadwal->bank_kelas ?? '');
            $arr_kls_jadwal = [];
            foreach ($kelass as $kll) {
                foreach ($kll as $kl) {
                    if (!($kl != null)) {
                    } else {
                        $arr_kls_jadwal[] = $kl;
                    }
                }
            }
            if (!in_array($siswa->id_kelas, $arr_kls_jadwal)) {
                unset($jadwals[$kj]);
                $kelass_unset[] = $kj;
            } else {
                $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
                $info = $jadwal;
                $bagi_pg = $info->tampil_pg / 100;
                $bobot_pg = $info->bobot_pg / 100;
                $bagi_pg2 = $info->tampil_kompleks / 100;
                $bobot_pg2 = $info->bobot_kompleks / 100;
                $bagi_jodoh = $info->tampil_jodohkan / 100;
                $bobot_jodoh = $info->bobot_jodohkan / 100;
                $bagi_isian = $info->tampil_isian / 100;
                $bobot_isian = $info->bobot_isian / 100;
                $bagi_essai = $info->tampil_esai / 100;
                $bobot_essai = $info->bobot_esai / 100;
                $jawabans = $this->cbt->getJawabanSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
                $jawabans_siswa = [];
                foreach ($jawabans as $jawaban_siswa) {
                    if (!($jawaban_siswa->jenis_soal == '2')) {
                        if (!($jawaban_siswa->jenis_soal == '3')) {
                        }
                    } else {
                        $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                        $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                        $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                        $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                        $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar);
                        $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                        $jawaban_siswa->jawaban = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban);
                        $jawaban_siswa->jawaban = array_filter($jawaban_siswa->jawaban, 'strlen');
                        if (!($jawaban_siswa->jenis_soal == '3')) {
                        }
                    }
                    $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                    $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                    $jawaban_siswa->jawaban = @unserialize($jawaban_siswa->jawaban ?? '');
                    $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                    $jawaban_siswa->jawaban_benar = json_decode(json_encode($jawaban_siswa->jawaban_benar));
                    $jawaban_siswa->jawaban = json_decode(json_encode($jawaban_siswa->jawaban));
                    $arrAlphabet = range('A', 'Z');
                    if (!(!isset($jawaban_siswa->jawaban_siswa) || !isset($jawaban_siswa->jawaban_siswa->links))) {
                    }
                    $arrjwbnSiswa = [];
                    if (!$jawaban_siswa->jawaban_siswa) {
                    }
                    foreach ($jawaban_siswa->jawaban_siswa->jawaban as $idx => $jbs) {
                        if (!($idx > 0)) {
                        } else {
                            $arrjwbnSiswa[$idx] = [];
                            foreach ($jbs as $idxs => $jb) {
                                if (!($idxs > 0)) {
                                } else {
                                    if (!($jb === '1')) {
                                    }
                                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                                }
                            }
                        }
                    }
                    if ($jawaban_siswa->jawaban_siswa) {
                    }
                    $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
                    $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                    $arrjwbn = [];
                    foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                        if (!($idx > 0)) {
                        } else {
                            $arrjwbn[$idx] = [];
                            foreach ($jbs as $idxs => $jb) {
                                if (!($idxs > 0)) {
                                } else {
                                    if (!($jb === '1')) {
                                    }
                                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
                                }
                            }
                        }
                    }
                    $jawaban_siswa->jawaban_benar->links = json_decode(json_encode($arrjwbn));
                    $jawabans_siswa[$jawaban_siswa->id_siswa][$jawaban_siswa->jenis_soal][] = $jawaban_siswa;
                }
                $ada_jawaban = isset($jawabans_siswa[$siswa->id_siswa]);
                $ada_jawaban_pg = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['1']);
                $ada_jawaban_pg2 = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['2']);
                $ada_jawaban_jodoh = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['3']);
                $ada_jawaban_isian = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['4']);
                $ada_jawaban_essai = $ada_jawaban && isset($jawabans_siswa[$siswa->id_siswa]['5']);
                $skor = new stdClass();
                $nilai_input = $this->cbt->getNilaiSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
                if (!($nilai_input != null)) {
                }
                $skor->dikoreksi = $nilai_input->dikoreksi;
                $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
                $benar_pg = 0;
                $salah_pg = 0;
                if (!($info->tampil_pg > 0)) {
                }
                if (!(count($jawaban_pg) > 0)) {
                }
                foreach ($jawaban_pg as $num => $jwb_pg) {
                    $benar = false;
                    if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                    } else {
                        if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
                        }
                        $salah_pg += 1;
                        $benar = false;
                    }
                }
                $skor->skor_pg = $skor_pg = $bagi_pg == 0 ? 0 : round($benar_pg / $bagi_pg * $bobot_pg, 2);
                $skor->benar_pg = $benar_pg;
                $jawaban_pg2 = $ada_jawaban_pg2 ? $jawabans_siswa[$siswa->id_siswa]['2'] : [];
                $benar_pg2 = 0;
                $skor_koreksi_pg2 = 0.0;
                $otomatis_pg2 = 0;
                if (!($info->tampil_kompleks > 0)) {
                }
                if (!(count($jawaban_pg2) > 0)) {
                }
                foreach ($jawaban_pg2 as $num => $jawab_pg2) {
                    $skor_koreksi_pg2 += $jawab_pg2->nilai_koreksi;
                    $arr_benar = [];
                    if (!$jawab_pg2->jawaban_siswa) {
                        if (!(count($jawab_pg2->jawaban) > 0)) {
                        }
                    } else {
                        foreach ($jawab_pg2->jawaban_siswa as $js) {
                            if (!in_array($js, $jawab_pg2->jawaban)) {
                            } else {
                                array_push($arr_benar, true);
                            }
                        }
                        if (!(count($jawab_pg2->jawaban) > 0)) {
                        }
                    }
                    $benar_pg2 += 1 / count($jawab_pg2->jawaban) * count($arr_benar);
                    $jml_benar = count($arr_benar);
                    $otomatis_pg2 = $jawab_pg2->nilai_otomatis;
                }
                $s_pg2 = $bagi_pg2 == 0 ? 0 : $benar_pg2 / $bagi_pg2 * $bobot_pg2;
                $input_pg2 = 0;
                if (!($nilai_input != null && $nilai_input->kompleks_nilai != null)) {
                }
                $input_pg2 = $nilai_input->kompleks_nilai;
                $skor_pg2 = $input_pg2 != 0 ? $input_pg2 : ($otomatis_pg2 == 0 ? $s_pg2 : $skor_koreksi_pg2);
                $skor->skor_kompleks = round($skor_pg2, 2);
                $skor->benar_kompleks = round($benar_pg2, 2);
                $jawaban_jodoh = $ada_jawaban_jodoh ? $jawabans_siswa[$siswa->id_siswa]['3'] : [];
                $benar_jod = 0;
                $skor_koreksi_jod = 0.0;
                $otomatis_jod = 0;
                if (!($info->tampil_jodohkan > 0 && $jawaban_jodoh && count($jawaban_jodoh) > 0)) {
                }
                foreach ($jawaban_jodoh as $num => $jawab_jod) {
                    $skor_koreksi_jod += $jawab_jod->nilai_koreksi;
                    $item_benar = 0;
                    $item_salah = 0;
                    $item_kurang = 0;
                    $items = 0;
                    $arrBenar = [];
                    $point_benar = $info->bobot_jodohkan > 0 ? round($info->bobot_jodohkan / $info->tampil_jodohkan, 2) : 0;
                    if (!isset($jawab_jod->jawaban_siswa->links)) {
                        $point_soal = 1 / $items * $item_benar * $point_benar;
                    } else {
                        $array1 = (array) $jawab_jod->jawaban_benar->links;
                        $this->sortArrays($array1);
                        $array2 = (array) $jawab_jod->jawaban_siswa->links;
                        $this->sortArrays($array2);
                        $sameCount = 0;
                        $differentCount = 0;
                        foreach ($array1 as $key => $subArray1) {
                            $arrBenar[$key] = new stdClass();
                            $arrBenar[$key]->benar = 0;
                            $arrBenar[$key]->salah = 0;
                            $arrBenar[$key]->kurang = 0;
                            $items += count($subArray1);
                            if (isset($array2[$key])) {
                                $subArray2 = $array2[$key];
                                $sameItems = array_intersect($subArray1, $subArray2);
                                $item_benar += count($sameItems);
                                $arrBenar[$key]->benar += count($sameItems);
                                $diffItems1 = array_diff($subArray1, $subArray2);
                                $diffItems2 = array_diff($subArray2, $subArray1);
                                $arrBenar[$key]->kurang += count($diffItems1);
                            } else {
                                $arrBenar[$key]->kurang += count($subArray1);
                            }
                        }
                        $point_soal = 1 / $items * $item_benar * $point_benar;
                    }
                    $benar_jod += 1 / $items * $item_benar;
                    $otomatis_jod = $jawab_jod->nilai_otomatis;
                }
                $s_jod = $bagi_jodoh == 0 ? 0 : $benar_jod / $bagi_jodoh * $bobot_jodoh;
                $input_jod = 0;
                if (!($nilai_input != null && $nilai_input->jodohkan_nilai != null)) {
                }
                $input_jod = $nilai_input->jodohkan_nilai;
                $skor_jod = $input_jod != 0 ? $input_jod : ($otomatis_jod == 0 ? $s_jod : $skor_koreksi_jod);
                $skor->skor_jodohkan = round($skor_jod, 2);
                $skor->benar_jodohkan = round($benar_jod, 2);
                $jawaban_is = $ada_jawaban_isian ? $jawabans_siswa[$siswa->id_siswa]['4'] : [];
                $benar_is = 0;
                $skor_koreksi_is = 0.0;
                $otomatis_is = 0;
                if (!($info->tampil_isian > 0)) {
                }
                if (!(count($jawaban_is) > 0)) {
                }
                foreach ($jawaban_is as $num => $jawab_is) {
                    $skor_koreksi_is += $jawab_is->nilai_koreksi;
                    $benar = $jawab_is != null && strtolower($jawab_is->jawaban_siswa ?? '') == strtolower($jawab_is->jawaban ?? '');
                    if (!$benar) {
                        $otomatis_is = $jawab_is->nilai_otomatis;
                    } else {
                        $benar_is++;
                        $otomatis_is = $jawab_is->nilai_otomatis;
                    }
                }
                $s_is = $bagi_isian == 0 ? 0 : $benar_is / $bagi_isian * $bobot_isian;
                $input_is = 0;
                if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
                }
                $input_is = $nilai_input->isian_nilai;
                $skor_is = $input_is != 0 ? $input_is : ($otomatis_is == 0 ? $s_is : $skor_koreksi_is);
                $skor->skor_isian = round($skor_is, 2);
                $skor->benar_isian = $benar_is;
                $jawaban_es = $ada_jawaban_essai ? $jawabans_siswa[$siswa->id_siswa]['5'] : [];
                $benar_es = 0;
                $skor_koreksi_es = 0.0;
                $otomatis_es = 0;
                if (!($info->tampil_esai > 0)) {
                }
                if (!(count($jawaban_es) > 0)) {
                }
                foreach ($jawaban_es as $num => $jawab_es) {
                    $skor_koreksi_es += $jawab_es->nilai_koreksi;
                    $benar = $jawab_es != null && strtolower($jawab_es->jawaban_siswa ?? '') == strtolower($jawab_es->jawaban ?? '');
                    if (!$benar) {
                        $otomatis_es = $jawab_es->nilai_otomatis;
                    } else {
                        $benar_es++;
                        $otomatis_es = $jawab_es->nilai_otomatis;
                    }
                }
                $s_es = $bagi_essai == 0 ? 0 : $benar_es / $bagi_essai * $bobot_essai;
                $input_es = 0;
                if (!($nilai_input != null && $nilai_input->isian_nilai != null)) {
                }
                $input_es = $nilai_input->essai_nilai;
                $skor_es = $input_es != 0 ? $input_es : ($otomatis_es == 0 ? $s_es : $skor_koreksi_es);
                $skor->skor_essai = round($skor_es, 2);
                $skor->benar_esai = $benar_es;
                $total = $skor_pg + $skor_pg2 + $skor_jod + $skor_is + $skor_es;
                $skor->skor_total = round($total, 2);
                $skors[$jadwal->id_jadwal] = $skor;
                $durasies[$jadwal->id_jadwal] = $this->cbt->getDurasiSiswaByJadwal($jadwal->id_jadwal, $siswa->id_siswa);
            }
        }
        $this->db->trans_complete();
        $data['skor'] = $skors;
        $data['durasi'] = $durasies;
        $data['jadwal'] = $jadwals;
        $data['jawaban'] = $jawabans;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['running_text'] = $this->dashboard->getRunningText();
        $data['kelass'] = $kelass_unset;
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/nilai/data');
        $this->load->view('members/siswa/templates/footer');
    }
    public function catatan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        $data = ['user' => $user, 'siswa' => $siswa, 'judul' => 'Catatan', 'subjudul' => 'Catatan Dari Guru', 'setting' => $this->dashboard->getSetting()];
        $catatan_mapel = $this->kelas->getCatatanMapelBySiswa($siswa->id_kelas, $tp->id_tp, $smt->id_smt);
        $catatan = [];
        foreach ($catatan_mapel as $cat) {
            if (!($cat->type === '2' && $cat->id_siswa === $siswa->id_siswa || $cat->type === '1' && $cat->id_kelas === $siswa->id_kelas)) {
            } else {
                $catatan[] = ['id_catatan' => $cat->id_catatan, 'nama_guru' => $cat->nama_guru, 'foto_guru' => $cat->foto && file_exists($cat->foto) ? $cat->foto : 'uploads/profiles/' . $cat->nip . (file_exists('uploads/profiles/' . $cat->nip . '.jpg') ? '.jpg' : '.png'), 'id_siswa' => $siswa->id_siswa, 'tgl' => $cat->tgl, 'table' => 'mapel', 'level' => $cat->level, 'type' => $cat->type, 'readed' => $cat->readed, 'reading' => unserialize($cat->reading ?? '')];
            }
        }
        $catatan_siswa = $this->kelas->getCatatanSiswaBySiswa($siswa->id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($catatan_siswa as $cat) {
            if (!($cat->type === '2' && $cat->id_siswa === $siswa->id_siswa || $cat->type === '1' && $cat->id_kelas === $siswa->id_kelas)) {
            } else {
                $catatan[] = ['id_catatan' => $cat->id_catatan, 'nama_guru' => $cat->nama_guru, 'foto_guru' => $cat->foto && file_exists($cat->foto) ? $cat->foto : 'uploads/profiles/' . $cat->nip . (file_exists('uploads/profiles/' . $cat->nip . '.jpg') ? '.jpg' : '.png'), 'id_siswa' => $siswa->id_siswa, 'tgl' => $cat->tgl, 'table' => 'wali', 'level' => $cat->level, 'readed' => $cat->readed, 'type' => $cat->type, 'reading' => unserialize($cat->reading ?? '')];
            }
        }
        rsort($catatan);
        $data['catatan'] = (array) json_decode(json_encode($catatan));
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->load->view('members/siswa/templates/header', $data);
        $this->load->view('members/siswa/catatan/data');
        $this->load->view('members/siswa/templates/footer');
    }
    public function detailCatatan($table, $id_catatan)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        if ($siswa && $table == 'mapel') {
            $detail = $this->kelas->getCatatanMapelSiswaDetail($id_catatan);
        } else {
            $detail = $this->kelas->getCatatanKelasSiswaDetail($id_catatan);
        }
        $reading = [];
        if (!$detail) {
        }
        $detail->id_siswa = $siswa->id_siswa;
        $reading = $detail->reading != null ? unserialize($detail->reading ?? '') : [];
        $this->output_json(['reading' => $reading, 'detail' => $detail]);
    }
    public function readed($table, $id_catatan)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Cbt_model', 'cbt');
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $siswa = $this->cbt->getDataSiswa($user->username, $tp->id_tp, $smt->id_smt);
        if ($table == 'mapel') {
            $tbl = 'kelas_catatan_mapel';
        } else {
            $tbl = 'kelas_catatan_wali';
        }
        $cat = $this->kelas->getReading($tbl, $id_catatan);
        $readed = $cat->readed == '0' ? date('Y-m-d H:i:s') : '0';
        if ($cat->type == '1') {
        }
        $this->db->set('readed', $readed);
        $this->db->where('id_catatan', $id_catatan);
        $update = $this->db->update($tbl);
        $this->output_json($update);
    }
    public function getTimer($id_siswa, $id_jadwal)
    {
        $this->load->model('Cbt_model', 'cbt');
        $data['durasi'] = $this->cbt->getDurasiSiswa($id_siswa . '0' . $id_jadwal);
        $this->output_json($data);
    }
    function total_hari($id_day, $bulan, $taun)
    {
        $days = 0;
        $dates = [];
        $total_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $taun);
        $idday = $id_day == '7' ? 0 : $id_day;
        $i = 1;
        if (!($i < $total_days)) {
            return $dates;
        } else {
            if (!(date('N', strtotime($taun . '-' . $bulan . '-' . $i)) == $idday)) {
            }
            $days++;
            array_push($dates, date('Y-m-d', strtotime($taun . '-' . $bulan . '-' . $i)));
            $i++;
            if (!($i < $total_days)) {
            }
        }
    }
}
```

---

## File: application/controllers_progress/Update.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Update extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        include APPPATH . 'config/database.php';
        $this->load->dbforge();
        $this->load->database();
        $this->load->library('encryption');
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
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $data['json'] = $json;
        $this->load->view('install/header', $data);
        $this->load->view('install/update');
        $this->load->view('install/footer');
    }
    function object_to_array($data)
    {
        if (!(is_array($data) || is_object($data))) {
            return $data;
        } else {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = is_array($data) || is_object($data) ? $this->object_to_array($value) : $value;
            }
            return $result;
        }
    }
    public function checkDatabase()
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $tabless = $this->db->list_tables();
        $fields = [];
        $currentDb = [];
        foreach ($tabless as $table) {
            $datafld = $this->db->field_data($table);
            $sql = 'SELECT `column_name`, `numeric_precision`, `extra`, `is_nullable`' . ' FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            if (!(($query = $this->db->query($sql)) === FALSE)) {
                $query = $query->result_object();
            } else {
                $currentDb = FALSE;
                $query = $query->result_object();
            }
            $retval = array();
            $i = 0;
            $c = count($query);
            if (!($i < $c)) {
            }
            if (!($datafld[$i]->name == $query[$i]->column_name)) {
            }
            if (!($query[$i]->extra != '')) {
            }
            if ($query[$i]->extra == 'auto_increment') {
            }
            $datafld[$i]->extra = $query[$i]->extra;
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->column_name;
            $retval[$i]->extra = $query[$i]->extra;
            $i++;
        }
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $tbl_baru = array_keys($json);
        $tbl_ada = array_keys($fields);
        $full_tables = array_merge($tbl_baru, $tbl_ada);
        $full_tables = array_unique($full_tables);
        sort($full_tables);
        $create_tables = [];
        $add_columns = [];
        $edit_columns = [];
        foreach ($full_tables as $table) {
            if ($this->db->table_exists($table)) {
                if (!isset($json[$table])) {
                }
                foreach ($json[$table] as $jtbl) {
                    if ($this->db->field_exists($jtbl->name, $table)) {
                        foreach ($fields[$table] as $ftbl) {
                            if (!($jtbl->name == $ftbl->name)) {
                            } else {
                                if (!($jtbl->default != $ftbl->default || $jtbl->max_length != $ftbl->max_length || $jtbl->type != $ftbl->type)) {
                                }
                                $edit_columns[$table][] = $jtbl;
                            }
                        }
                    } else {
                        $add_columns[$table][] = $jtbl;
                    }
                }
            } else {
                $create_tables[$table] = $json[$table];
            }
        }
        $counts = count($create_tables) + count($add_columns) + count($edit_columns);
        $data = ['db' => $fields, 'create' => $create_tables, 'modify' => $edit_columns, 'add' => $add_columns, 'counts' => $counts, 'json' => $json, 'current' => $currentDb];
        $this->output_json($data);
    }
    public function updateDatabase()
    {
        $tabless = $this->db->list_tables();
        $fields = [];
        foreach ($tabless as $table) {
            $fields[$table] = $this->db->field_data($table);
        }
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $tbl_baru = array_keys($json);
        $tbl_ada = array_keys($fields);
        $full_tables = array_merge($tbl_baru, $tbl_ada);
        $full_tables = array_unique($full_tables);
        sort($full_tables);
        foreach ($full_tables as $table) {
            if ($this->db->table_exists($table)) {
                if (!isset($json[$table])) {
                }
                foreach ($json[$table] as $jtbl) {
                    if ($this->db->field_exists($jtbl->name, $table)) {
                        foreach ($fields[$table] as $ftbl) {
                            if (!($jtbl->name == $ftbl->name)) {
                            } else {
                                if (!($jtbl->default != $ftbl->default || $jtbl->max_length != $ftbl->max_length || $jtbl->type != $ftbl->type)) {
                                }
                                if ($jtbl->primary_key == 0) {
                                }
                                if ($jtbl->auto_increment == true) {
                                }
                                $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false));
                                $this->dbforge->add_key($jtbl->name, true);
                                $this->dbforge->modify_column($table, $field);
                            }
                        }
                    } else {
                        if ($jtbl->primary_key == 0) {
                        }
                        $field = array($jtbl->name => array('type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => false));
                        $this->dbforge->add_key($jtbl->name, true);
                        $this->dbforge->add_column($table, $field);
                    }
                }
            } else {
                if (!isset($json[$table])) {
                }
                foreach ($json[$table] as $tbl => $jtbl) {
                    $field = [$jtbl->name => ['type' => $jtbl->type, 'constraint' => $jtbl->max_length, 'null' => $jtbl->primary_key == 0]];
                    $this->dbforge->add_field($field);
                    if (!($jtbl->primary_key == 1)) {
                    } else {
                        $this->dbforge->add_key($jtbl->name, true);
                    }
                }
                $this->dbforge->create_table($table, TRUE);
                $this->db->query('ALTER TABLE  `' . $table . '` ENGINE = InnoDB');
            }
        }
        echo true;
    }
    public function checkDb()
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $tabless = $this->db->list_tables();
        $fields = [];
        foreach ($tabless as $table) {
            $sql = 'SELECT `column_name`, `column_type`, `collation_name`, `data_type`, `character_maximum_length`, `numeric_precision`,' . ' `column_default`, `column_key`, `column_comment`, `extra`, `is_nullable`
			FROM `information_schema`.`columns` WHERE table_schema = "' . $this->db->database . '" AND table_name = "' . $table . '"';
            if (!(($query = $this->db->query($sql)) === FALSE)) {
                $query = $query->result_object();
            } else {
                $fields = FALSE;
                $query = $query->result_object();
            }
            $retval = array();
            $i = 0;
            $c = count($query);
            if (!($i < $c)) {
            }
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->column_name;
            $retval[$i]->col_type = $query[$i]->column_type;
            $retval[$i]->type = $query[$i]->data_type;
            $retval[$i]->collation = $query[$i]->collation_name;
            $retval[$i]->max_length = $query[$i]->character_maximum_length > 0 ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
            $retval[$i]->default = $query[$i]->column_default;
            $retval[$i]->comment = $query[$i]->column_comment;
            $retval[$i]->extra = $query[$i]->extra;
            $retval[$i]->nullable = $query[$i]->is_nullable;
            $retval[$i]->primary = $query[$i]->column_key;
            $i++;
        }
        $json = file_get_contents('./assets/app/db/database.json');
        $json = json_decode($json);
        $json = (array) $json;
        $tbl_seharusnya = array_keys($json);
        $tbl_ada = array_keys($fields);
        $full_tables = array_merge($tbl_seharusnya, $tbl_ada);
        $full_tables = array_unique($full_tables);
        sort($full_tables);
        $create_tables = [];
        $script_create_table = [];
        $add_columns = [];
        $script_create_column = [];
        $edit_columns = [];
        $script_edit_column = [];
        foreach ($full_tables as $table) {
            if (!$this->db->table_exists($table)) {
                $create_tables[] = $json[$table];
                $script = 'CREATE TABLE `' . $table . '` (';
                $pri = '';
                foreach ($json[$table]->columns as $column) {
                    if ($column->max_length == null) {
                        $length = '';
                    } else {
                        if ($column->type != 'longtext' && $column->type != 'mediumtext' && $column->type != 'text') {
                        }
                        $length = '';
                    }
                    $nullable = $column->nullable == 'NO' ? ' NOT NULL' : '';
                    $default = $column->default == null ? '' : ' DEFAULT ' . $column->default;
                    $extra = $column->extra == '' ? '' : ' ' . strtoupper($column->extra ?? '');
                    $comment = $column->comment == '' ? '' : ' COMMENT \'' . $column->comment . '\'';
                    $script .= '`' . $column->name . '` ' . $column->type . $length . $nullable . $default . $extra . $comment . ', ';
                    $pri .= $column->primary != '' ? 'PRIMARY KEY (`' . $column->name . '`)' : '';
                }
                $script .= $pri . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
                $script_create_table[$table] = $script;
            } else {
                if (!isset($json[$table])) {
                }
                $add_column = [];
                $modif_column = [];
                foreach ($json[$table]->columns as $jtbl) {
                    if ($this->db->field_exists($jtbl->name, $table)) {
                        foreach ($fields[$table]->columns as $ftbl) {
                            if (!($jtbl->name == $ftbl->name)) {
                            } else {
                                if (!($jtbl->col_type != $ftbl->col_type)) {
                                }
                                $edit_columns[$table][$jtbl->name]['col_type'] = $jtbl->col_type;
                                if (!($jtbl->nullable != $ftbl->nullable)) {
                                }
                                $edit_columns[$table][$jtbl->name]['nullable'] = $jtbl->nullable;
                                if (!($jtbl->default != null)) {
                                }
                                $jtbl->default = str_replace('()', '', $jtbl->default ?? '');
                                $jtbl->default = strtoupper($jtbl->default ?? '');
                                if (!($ftbl->default != null)) {
                                }
                                $ftbl->default = str_replace('()', '', $ftbl->default ?? '');
                                $ftbl->default = strtoupper($ftbl->default ?? '');
                                if (!($jtbl->default != $ftbl->default)) {
                                }
                                $edit_columns[$table][$jtbl->name]['default'] = $jtbl->default;
                                if (!($jtbl->extra != null)) {
                                }
                                $jtbl->extra = str_replace('()', '', $jtbl->extra ?? '');
                                $jtbl->extra = strtoupper($jtbl->extra ?? '');
                                if (!($ftbl->extra != null)) {
                                }
                                $ftbl->extra = str_replace('()', '', $ftbl->extra ?? '');
                                $ftbl->extra = strtoupper($ftbl->extra ?? '');
                                if (!($jtbl->extra != $ftbl->extra)) {
                                }
                                $edit_columns[$table][$jtbl->name]['extra'] = $jtbl->extra;
                                if (!($jtbl->comment != $ftbl->comment)) {
                                }
                                $edit_columns[$table][$jtbl->name]['comment'] = $jtbl->comment;
                                if (!($jtbl->primary != $ftbl->primary)) {
                                }
                                $edit_columns[$table][$jtbl->name]['primary'] = $jtbl->primary;
                                if (strtolower($jtbl->primary ?? '') == 'pri') {
                                }
                                if (strtolower($jtbl->primary ?? '') == 'uni') {
                                }
                                if (!($jtbl->col_type != $ftbl->col_type || $jtbl->nullable != $ftbl->nullable || $jtbl->default != $ftbl->default || $jtbl->extra != $ftbl->extra || $jtbl->comment != $ftbl->comment)) {
                                }
                                $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                                $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                                $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                                $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                                array_push($modif_column, 'MODIFY `' . $jtbl->name . '` ' . $jtbl->col_type . $nullable . $default . $extra . $comment);
                            }
                        }
                    } else {
                        $add_columns[$table][] = $jtbl;
                        if ($jtbl->max_length == null) {
                        }
                        if ($jtbl->type != 'longtext' && $jtbl->type != 'mediumtext' && $jtbl->type != 'text') {
                        }
                        $length = '';
                        $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                        $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                        $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                        if (!(strtoupper($extra ?? '') == ' AUTO_INCREMENT')) {
                        }
                        $extra .= ' PRIMARY KEY';
                        $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                        array_push($add_column, 'ADD `' . $jtbl->name . '` ' . $jtbl->type . $length . $nullable . $default . $extra . $comment);
                        foreach ($fields[$table]->columns as $ftbl) {
                            if (!($jtbl->name == $ftbl->name)) {
                            } else {
                                if (!($jtbl->col_type != $ftbl->col_type)) {
                                }
                                $edit_columns[$table][$jtbl->name]['col_type'] = $jtbl->col_type;
                                if (!($jtbl->nullable != $ftbl->nullable)) {
                                }
                                $edit_columns[$table][$jtbl->name]['nullable'] = $jtbl->nullable;
                                if (!($jtbl->default != null)) {
                                }
                                $jtbl->default = str_replace('()', '', $jtbl->default ?? '');
                                $jtbl->default = strtoupper($jtbl->default ?? '');
                                if (!($ftbl->default != null)) {
                                }
                                $ftbl->default = str_replace('()', '', $ftbl->default ?? '');
                                $ftbl->default = strtoupper($ftbl->default ?? '');
                                if (!($jtbl->default != $ftbl->default)) {
                                }
                                $edit_columns[$table][$jtbl->name]['default'] = $jtbl->default;
                                if (!($jtbl->extra != null)) {
                                }
                                $jtbl->extra = str_replace('()', '', $jtbl->extra ?? '');
                                $jtbl->extra = strtoupper($jtbl->extra ?? '');
                                if (!($ftbl->extra != null)) {
                                }
                                $ftbl->extra = str_replace('()', '', $ftbl->extra ?? '');
                                $ftbl->extra = strtoupper($ftbl->extra ?? '');
                                if (!($jtbl->extra != $ftbl->extra)) {
                                }
                                $edit_columns[$table][$jtbl->name]['extra'] = $jtbl->extra;
                                if (!($jtbl->comment != $ftbl->comment)) {
                                }
                                $edit_columns[$table][$jtbl->name]['comment'] = $jtbl->comment;
                                if (!($jtbl->primary != $ftbl->primary)) {
                                }
                                $edit_columns[$table][$jtbl->name]['primary'] = $jtbl->primary;
                                if (strtolower($jtbl->primary ?? '') == 'pri') {
                                }
                                if (strtolower($jtbl->primary ?? '') == 'uni') {
                                }
                                if (!($jtbl->col_type != $ftbl->col_type || $jtbl->nullable != $ftbl->nullable || $jtbl->default != $ftbl->default || $jtbl->extra != $ftbl->extra || $jtbl->comment != $ftbl->comment)) {
                                }
                                $nullable = $jtbl->nullable == 'NO' ? ' NOT NULL' : '';
                                $default = $jtbl->default == null ? '' : ' DEFAULT ' . $jtbl->default;
                                $extra = $jtbl->extra == '' ? '' : ' ' . strtoupper($jtbl->extra ?? '');
                                $comment = $jtbl->comment == '' ? '' : ' COMMENT \'' . $jtbl->comment . '\'';
                                array_push($modif_column, 'MODIFY `' . $jtbl->name . '` ' . $jtbl->col_type . $nullable . $default . $extra . $comment);
                            }
                        }
                    }
                }
                if (!(count($add_column) > 0)) {
                }
                $script_create_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $add_column) . ';';
                if (!(count($modif_column) > 0)) {
                }
                $script_edit_column[$table] = 'ALTER TABLE `' . $table . '` ' . implode(', ', $modif_column) . ';';
            }
        }
        $this->db->db_debug = $db_debug;
        $data = ['fields' => $fields, 'create_tables' => $create_tables, 'count_tbl' => count($create_tables), 'add_columns_to_table' => $add_columns, 'count_col' => count($add_columns), 'edit_columns' => $edit_columns, 'count_mod' => count($edit_columns), 'add_tbl' => $this->encryption->encrypt(json_encode($script_create_table)), 'add_col' => $this->encryption->encrypt(json_encode($script_create_column)), 'mod_col' => $this->encryption->encrypt(json_encode($script_edit_column))];
        $this->output_json($data);
    }
    public function createTable()
    {
        $scripts = $this->input->post('data', true);
        str_replace('%2B', '+', $scripts ?? '');
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $queries = '';
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        $data['success'] = $this->runQuery($queries);
        $data['message'] = 'Update kolom';
        $this->output_json($data);
    }
    public function createColumn()
    {
        $scripts = $this->input->post('data', true);
        str_replace('%2B', '+', $scripts ?? '');
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $queries = '';
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        if (!(strpos('`uid`', $queries) !== false)) {
            $data['success'] = $this->runQuery($queries);
        } else {
            $this->updateUID();
            $data['success'] = $this->runQuery($queries);
        }
        $data['message'] = 'Modify kolom';
        $this->output_json($data);
    }
    public function editColumn()
    {
        $scripts = $this->input->post('data', true);
        str_replace('%2B', '+', $scripts ?? '');
        sleep(1);
        $scripts = json_decode($this->encryption->decrypt($scripts));
        $queries = '';
        foreach ($scripts as $script) {
            $queries .= $script;
        }
        $data['success'] = $this->runQuery($queries);
        $data['message'] = 'Update selesai';
        $this->output_json($data);
    }
    function runQuery($script)
    {
        $hostname = $this->db->hostname;
        $hostuser = $this->db->username;
        $hostpass = $this->db->password;
        $database = $this->db->database;
        $mysqli = new mysqli($hostname, $hostuser, $hostpass, $database);
        if (!mysqli_connect_errno()) {
            $mysqli->multi_query($script);
            $mysqli->close();
            return true;
        } else {
            return mysqli_connect_errno();
        }
    }
    function updateUID()
    {
        $this->load->library('Uuid', 'uuid');
        $siswas = $this->db->get('master_siswa')->result();
        $input = array();
        foreach ($siswas as $siswa) {
            $input[] = array('id_siswa' => $siswa->id_siswa, 'uid' => $this->uuid->v4());
        }
        return $this->db->update_batch('master_siswa', $input, 'id_siswa');
    }
    function make_base()
    {
    }
}
```

---

## File: application/controllers_progress/Useradmin.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Useradmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if ($this->ion_auth->is_admin()) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library('upload');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function is_admin()
    {
        if ($this->ion_auth->is_admin()) {
        } else {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
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
    public function data()
    {
        $this->is_admin();
        $this->output_json($this->users->getDataadmin(), false);
    }
    public function index()
    {
        $this->is_admin();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Admin Management', 'subjudul' => 'Data Admin', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/data');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function edit($id)
    {
        $level = $this->ion_auth->get_users_groups($id)->result();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Administrator', 'subjudul' => 'Edit Data Admin', 'users' => $this->ion_auth->user($id)->row(), 'groups' => $this->ion_auth->groups()->result(), 'level' => $level[0], 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('users/admin/edit');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function create()
    {
        $this->is_admin();
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[6]|max_length[20]|required');
        $this->form_validation->set_rules('confirm_password', 'Confirm password', 'trim|matches[password]|required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email'), 'password' => form_error('password'), 'confirm_password' => form_error('confirm_password')];
        } else {
            $username = $this->input->post('username', true);
            $password = $this->input->post('password', true);
            $email = $this->input->post('email', true);
            $additional_data = ['first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true)];
            $group = array('1');
            if ($this->ion_auth->username_check($username)) {
            }
            if ($this->ion_auth->email_check($email)) {
            }
            $this->ion_auth->register($username, $password, $email, $additional_data, $group);
            $data = ['status' => true, 'msg' => 'User berhasil dibuat. NIP digunakan sebagai password pada saat login.'];
        }
        $this->output_json($data);
    }
    public function edit_info()
    {
        $this->is_admin();
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['username' => $this->input->post('username', true), 'first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true), 'email' => $this->input->post('email', true)];
            $update = $this->master->update('users', $input, 'id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function edit_status()
    {
        $this->is_admin();
        $this->form_validation->set_rules('status', 'Status', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['status' => form_error('status')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['active' => $this->input->post('status', true)];
            $update = $this->master->update('users', $input, 'id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function edit_level()
    {
        $this->is_admin();
        $this->form_validation->set_rules('level', 'Level', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['level' => form_error('level')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['group_id' => $this->input->post('level', true)];
            $update = $this->master->update('users_groups', $input, 'user_id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($this->form_validation->run() === FALSE) {
            $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        } else {
            $identity = $this->session->userdata('identity');
            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            if ($change) {
            }
            $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
        }
        $this->output_json($data);
    }
    public function delete($id)
    {
        $this->is_admin();
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
        $this->output_json($data);
    }
    function uploadFile($id_user)
    {
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/profiles/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = 'foto_' . $id_user;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/profiles/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile()
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!unlink($file_name)) {
        } else {
            echo 'File Delete Successfully';
        }
    }
    function saveProfile()
    {
        $nama = $this->input->post('nama_lengkap');
        $jabatan = $this->input->post('jabatan');
        $foto = $this->input->post('foto');
        $user = $this->ion_auth->user()->row();
        $insert = ['id_user' => $user->id, 'nama_lengkap' => $nama, 'jabatan' => $jabatan, 'foto' => str_replace(base_url(), '', $foto ?? '')];
        $update = $this->db->replace('users_profile', $insert);
        $res['status'] = $update;
        $this->output_json($res);
    }
}
```

---

## File: application/controllers_progress/Userguru.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Userguru extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
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
    public function data()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->users->getUserGuru($tp->id_tp, $smt->id_smt), false);
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Data User Guru', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        if ($group === 'admin') {
            $data['tp'] = $this->dashboard->getTahun();
            $data['tp_active'] = $this->dashboard->getTahunActive();
            $data['smt'] = $this->dashboard->getSemester();
            $data['smt_active'] = $this->dashboard->getSemesterActive();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('users/guru/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $id = $this->users->getGuruByUsername($user->username);
            $this->edit($id->id_guru);
        }
    }
    public function activate($id)
    {
        $guru = $this->users->getDataGuru($id);
        $nama = explode(' ', $guru->nama_guru ?? '');
        $first_name = $nama[0];
        $last_name = count($nama) > 2 ? $nama[1] : end($nama);
        $username = trim($guru->username ?? '');
        $password = trim($guru->password ?? '');
        $email = strtolower($guru->username ?? '') . '@guru.com';
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('2');
        if ($this->ion_auth->username_check($username)) {
            $data = ['status' => false, 'msg' => 'Username ' . $username . ' tidak tersedia (sudah digunakan).'];
        } else {
            if ($this->ion_auth->email_check($email)) {
            }
            $id_user = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
            $data = ['status' => true, 'msg' => 'Akun ' . $guru->nama_guru . ' diaktifkan.'];
            $this->db->set('id_user', $id_user);
            $this->db->where('id_guru', $id);
            $this->db->update('master_guru');
        }
        $data['pass'] = $password;
        $this->output_json($data);
    }
    public function deactivate($id = NULL)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $id = (int) $id;
            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            }
            $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
        }
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;
        foreach ($guruAktif as $guru) {
            if ($guru->aktif > 0) {
            } else {
                $this->activate($guru->id_guru);
                $jum += 1;
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru diaktifkan.'];
        $this->output_json($data);
    }
    public function nonaktifkanSemua()
    {
        $guruAktif = $this->users->getGuruAktif();
        $jum = 0;
        foreach ($guruAktif as $guru) {
            if ($guru->aktif > 0) {
                $del = $this->deactivate($guru->id, '');
                $this->output_json($del);
                $jum += 1;
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' Guru dinonaktifkan.'];
        $this->output_json($data);
    }
    public function edit($id)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->users->getDetailGuru($id);
        $users = $this->users->getUsers($guru->username);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'setting' => $this->dashboard->getSetting()];
        $data['users'] = $users;
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $group = $this->ion_auth->get_users_groups($user->id)->row()->name;
        if ($group === 'admin') {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['groups'] = $this->ion_auth->groups()->result();
            $data['kelass'] = $this->users->getKelas($tp->id_tp, $smt->id_smt);
            $data['mapels'] = $this->users->getMapel();
            $data['levels'] = $this->users->getLevelGuru();
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('users/guru/edit');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('users/guru/edit');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function editLogin()
    {
        $id_guru = $this->input->post('id_guru', true);
        $username = $this->input->post('username', true);
        $pass = $this->input->post('new', true);
        $guru_lain = $this->master->getUserIdGuruByUsername($username);
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($guru_lain && $guru_lain->id_guru != $id_guru) {
            $data = ['status' => false, 'errors' => ['username' => 'Username sudah digunakan']];
        } else {
            if ($this->form_validation->run() === FALSE) {
            }
            $guru = $this->db->get_where('master_guru', 'id_guru="' . $id_guru . '"')->row();
            $nama = explode(' ', $guru->nama_guru ?? '');
            $first_name = $nama[0];
            $last_name = end($nama);
            $username = trim($username ?? '');
            $password = trim($pass ?? '');
            $email = strtolower($username) . '@guru.com';
            $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
            $group = array('2');
            $user_guru = $this->db->get_where('users', 'email="' . $email . '"')->row();
            $deleted = true;
            if (!($user_guru != null)) {
            }
            $deleted = $this->ion_auth->delete_user((int) $user_guru->id);
            if ($deleted) {
            }
            $status = false;
            $msg = 'Gagal mengganti username/passsword';
            $data['status'] = $status;
            $data['text'] = $msg;
        }
        $this->output_json($data);
    }
    function buangspasi($teks)
    {
        $teks = trim($teks ?? '');
        $hasil = $teks;
        if (!strpos($teks, ' ')) {
            return $hasil;
        } else {
            $remove[] = '\'';
            $remove[] = '.';
            $remove[] = ' ';
            $hasil = str_replace($remove, '', $teks ?? '');
            if (!strpos($teks, ' ')) {
            }
        }
    }
    private function registerGuru($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['status'] = true;
        $data['id'] = $reg;
        if (!($reg == false)) {
            return $data;
        } else {
            $data['status'] = false;
            return $data;
        }
    }
    public function reset_login()
    {
        $username = $this->input->get('username', true);
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $this->db->where('login', $username);
            if ($this->db->delete('login_attempts')) {
            }
            $data = ['status' => false, 'msg' => ' gagal direset'];
        }
        $this->output_json($data, true);
    }
}
```

---

## File: application/controllers_progress/Usersiswa.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Usersiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables', 'form_validation']);
        } else {
            redirect('auth');
            $this->load->library(['datatables', 'form_validation']);
        }
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function is_has_access()
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $group = $this->ion_auth->get_users_groups($user_id)->row()->name;
        if (!(!$group === 'admin' or !$group === 'guru')) {
        } else {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
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
    public function data()
    {
        $this->is_has_access();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->users->getUserSiswa($tp->id_tp, $smt->id_smt), false);
    }
    public function index()
    {
        $this->is_has_access();
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Data User Siswa', 'profile' => $this->dashboard->getProfileAdmin($user->id), 'setting' => $this->dashboard->getSetting()];
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $this->dashboard->getTahunActive();
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $this->dashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('users/siswa/data');
        $this->load->view('_templates/dashboard/_footer');
    }
    public function list()
    {
        $page = $this->input->post('page', true);
        $limit = $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $offset = ($page - 1) * $limit;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $count_siswa = $this->users->getUserSiswaTotalPage($search);
        $lists = $this->users->getUserSiswaPage($tp->id_tp, $smt->id_smt, $offset, $limit, $search);
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit];
        $this->output_json($data);
    }
    private function registerSiswa($username, $password, $email, $additional_data, $group)
    {
        $reg = $this->ion_auth->register($username, $password, $email, $additional_data, $group);
        $data['status'] = true;
        $data['id'] = $reg;
        if (!($reg == false)) {
            return $data;
        } else {
            $data['status'] = false;
            return $data;
        }
    }
    private function aktifkan($siswa)
    {
        $nama = explode(' ', $siswa->nama ?? '');
        $first_name = $nama[0];
        $last_name = end($nama);
        $username = trim($siswa->username ?? '');
        $password = trim($siswa->password ?? '');
        $email = $siswa->nis . '@siswa.com';
        $additional_data = ['first_name' => $first_name, 'last_name' => $last_name];
        $group = array('3');
        $user_siswa = $this->db->get_where('users', 'email="' . $email . '"')->row();
        $deleted = true;
        if (!($user_siswa != null)) {
            if ($deleted) {
            }
            $data = ['status' => false, 'msg' => 'Akun siswa tidak tersedia (sudah digunakan).'];
            return $data;
        } else {
            $deleted = $this->ion_auth->delete_user($user_siswa->id);
            if ($deleted) {
            }
            $data = ['status' => false, 'msg' => 'Akun siswa tidak tersedia (sudah digunakan).'];
            return $data;
        }
    }
    public function activate($id)
    {
        $siswa = $this->users->getDataSiswa($id);
        $data = $this->aktifkan($siswa);
        $this->output_json($data);
    }
    public function aktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum = 0;
        foreach ($siswaAktif as $siswa) {
            if (!($siswa->aktif == 0)) {
            } else {
                $this->aktifkan($siswa);
                $jum += 1;
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa diaktifkan.'];
        $this->output_json($data);
    }
    private function nonaktifkan($user, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
            return $data;
        } else {
            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
            }
            $data = ['status' => false, 'msg' => 'Anda bukan admin.'];
            return $data;
        }
    }
    public function deactivate($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $user = $this->users->getUsers($username);
            $data = $this->nonaktifkan($user, $nama);
        }
        $this->output_json($data, true);
    }
    public function reset_login($username, $nama)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            $data = ['status' => false, 'msg' => 'You must be an administrator to view this page.'];
        } else {
            $this->db->where('login', $username);
            if ($this->db->delete('login_attempts')) {
            }
            $data = ['status' => false, 'msg' => 'User ' . $nama . ' gagal direset'];
        }
        $this->output_json($data, true);
    }
    public function nonaktifkanSemua()
    {
        $siswaAktif = $this->users->getSiswaAktif();
        $jum = 0;
        foreach ($siswaAktif as $siswa) {
            if (!($siswa->aktif > 0)) {
            } else {
                $del = $this->nonaktifkan($siswa, $siswa->nama);
                if ($del['status']) {
                }
                $this->output_json($del);
            }
        }
        $data = ['status' => true, 'jumlah' => $jum, 'msg' => $jum . ' siswa dinonaktifkan.'];
        $this->output_json($data);
    }
    public function edit($id)
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $siswa = $this->master->getDataSiswaById($tp->id_tp, $smt->id_smt, $id);
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'setting' => $this->dashboard->getSetting()];
        $data['siswa'] = $siswa;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('users/siswa/edit');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('users/siswa/edit');
            $this->load->view('members/guru/templates/footer');
        }
    }
    public function update()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $username = $this->input->post('username', true);
        $oldPass = $this->input->post('old', true);
        $newPass = $this->input->post('new', true);
        $this->form_validation->set_rules('username', 'Username', 'required|numeric|trim|min_length[6]|is_unique[master_siswa.username]');
        $this->form_validation->set_rules('old', 'Password Lama', 'required|numeric|trim|min_length[6]');
        $this->form_validation->set_rules('new', 'Password Baru', 'required|numeric|trim|min_length[6]');
    }
    public function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($this->form_validation->run() === FALSE) {
            $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        } else {
            $identity = $this->session->userdata('identity');
            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            if ($change) {
            }
            $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
        }
        $this->output_json($data);
    }
    public function delete($id)
    {
        $this->is_has_access();
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
        $this->output_json($data);
    }
    private function hash_password($password)
    {
        if (!(empty($password) || strpos($password, ' ') !== FALSE || strlen($password) > 4096)) {
            return password_hash($password, PASSWORD_BCRYPT);
        } else {
            return FALSE;
        }
    }
}
```

---

## File: application/controllers_progress/Users.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
            $this->load->library(['datatables', 'form_validation']);
        } else {
            redirect('auth');
            $this->load->library(['datatables', 'form_validation']);
        }
        $this->load->model('Users_model', 'users');
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'admindashboard');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function is_admin()
    {
        if ($this->ion_auth->is_admin()) {
        } else {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
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
    public function data($id = null)
    {
        $this->is_admin();
        $this->output_json($this->users->getDataUsers($id), false);
    }
    public function index()
    {
        $this->is_admin();
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'User Management', 'subjudul' => 'Data User'];
        $data['tp'] = $this->admindashboard->getTahun();
        $data['tp_active'] = $this->admindashboard->getTahunActive();
        $data['smt'] = $this->admindashboard->getSemester();
        $data['smt_active'] = $this->admindashboard->getSemesterActive();
        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/data');
        $this->load->view('_templates/dashboard/footer.php');
    }
    public function edit($id)
    {
        $level = $this->ion_auth->get_users_groups($id)->result();
        $data = ['user' => $this->ion_auth->user()->row(), 'judul' => 'User Management', 'subjudul' => 'Edit Data User', 'users' => $this->ion_auth->user($id)->row(), 'groups' => $this->ion_auth->groups()->result(), 'level' => $level[0]];
        $this->load->view('_templates/dashboard/header.php', $data);
        $this->load->view('users/edit');
        $this->load->view('_templates/dashboard/footer.php');
    }
    public function edit_info()
    {
        $this->is_admin();
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['username' => form_error('username'), 'first_name' => form_error('first_name'), 'last_name' => form_error('last_name'), 'email' => form_error('email')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['username' => $this->input->post('username', true), 'first_name' => $this->input->post('first_name', true), 'last_name' => $this->input->post('last_name', true), 'email' => $this->input->post('email', true)];
            $update = $this->master->update('users', $input, 'id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function edit_status()
    {
        $this->is_admin();
        $this->form_validation->set_rules('status', 'Status', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['status' => form_error('status')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['active' => $this->input->post('status', true)];
            $update = $this->master->update('users', $input, 'id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function edit_level()
    {
        $this->is_admin();
        $this->form_validation->set_rules('level', 'Level', 'required');
        if ($this->form_validation->run() === FALSE) {
            $data['status'] = false;
            $data['errors'] = ['level' => form_error('level')];
        } else {
            $id = $this->input->post('id', true);
            $input = ['group_id' => $this->input->post('level', true)];
            $update = $this->master->update('users_groups', $input, 'user_id', $id);
            $data['status'] = $update ? true : false;
        }
        $this->output_json($data);
    }
    public function change_password()
    {
        $this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
        $this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');
        if ($this->form_validation->run() === FALSE) {
            $data = ['status' => false, 'errors' => ['old' => form_error('old'), 'new' => form_error('new'), 'new_confirm' => form_error('new_confirm')]];
        } else {
            $identity = $this->session->userdata('identity');
            $change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));
            if ($change) {
            }
            $data = ['status' => false, 'msg' => $this->ion_auth->errors()];
        }
        $this->output_json($data);
    }
    public function delete($id)
    {
        $this->is_admin();
        $data['status'] = $this->ion_auth->delete_user($id) ? true : false;
        $this->output_json($data);
    }
}
```

---

## File: application/controllers_progress/Walicatatan.php

```php
<?php

class Walicatatan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Catatan Wali Kelas', 'subjudul' => 'Catatan Kelas', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['catatan_kelas'] = $this->kelas->getCatatanKelas($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $data['catatan_siswa'] = $this->kelas->getCatatanSiswa($tp->id_tp, $smt->id_smt, $guru->wali_kelas);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/catatan');
        $this->load->view('members/guru/templates/footer');
    }
    public function siswa()
    {
        $id_siswa = $this->input->get('id_siswa');
        $id_kelas = $this->input->get('id_kelas');
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Catatan Siswa', 'subjudul' => 'Catatan Siswa', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['siswa'] = $this->master->getSiswaById($id_siswa);
        $data['catatan_siswa'] = $this->kelas->getAllCatatanSiswa($id_siswa, $tp->id_tp, $smt->id_smt);
        $data['id_kelas'] = $id_kelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/persiswa');
        $this->load->view('members/guru/templates/footer');
    }
    public function saveCatatanKelas()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $text = $this->input->post('text', true);
        $level = $this->input->post('level', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '1', 'level' => $level, 'id_kelas' => $guru->wali_kelas, 'text' => $text, 'reading' => serialize([])];
        $insert = $this->master->create('kelas_catatan_wali', $data);
        $this->output_json($insert);
    }
    public function saveCatatanSiswa()
    {
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $user = $this->ion_auth->user()->row();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_siswa = $this->input->post('id_siswa');
        $text = $this->input->post('text', true);
        $level = $this->input->post('level', true);
        $data = ['id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'type' => '2', 'level' => $level, 'id_kelas' => $guru->wali_kelas, 'id_siswa' => $id_siswa, 'text' => $text, 'reading' => serialize([])];
        $insert = $this->master->create('kelas_catatan_wali', $data);
        $this->output_json($insert);
    }
    public function updateCatatanKelas()
    {
    }
    public function hapus($id_catatan)
    {
        $delete = $this->master->delete('kelas_catatan_wali', $id_catatan, 'id_catatan');
        $this->output_json($delete);
    }
}
```

---

## File: application/controllers_progress/Walisiswa.php

```php
<?php

class Walisiswa extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->form_validation->set_error_delimiters('', '');
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
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $kelas = $this->master->getKelasById($guru->wali_kelas);
        $data = ['user' => $user, 'judul' => 'Daftar Siswa', 'subjudul' => 'Siswa Kelas ' . $kelas->nama_kelas, 'setting' => $this->dashboard->getSetting()];
        $data['guru'] = $guru;
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['siswas'] = $this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $guru->wali_kelas, 0, 0);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/kelas');
        $this->load->view('members/guru/templates/footer');
    }
    public function dataKelas()
    {
        $user = $this->ion_auth->user()->row();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->output_json($this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $guru->wali_kelas), false);
    }
    public function list()
    {
        $page = $this->input->post('page', true);
        $limit = $this->input->post('limit', true);
        $search = $this->input->post('search', true);
        $id_kelas = $this->input->post('kelas', true);
        $offset = ($page - 1) * $limit;
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $count_siswa = $this->master->getDataSiswaByKelasPage($tp->id_tp, $smt->id_smt, $id_kelas, $search);
        $lists = $this->master->getDataSiswaByKelas($tp->id_tp, $smt->id_smt, $id_kelas, $offset, $limit, $search);
        $data = ['lists' => $lists, 'total' => $count_siswa, 'pages' => ceil($count_siswa / $limit), 'search' => $search, 'perpage' => $limit];
        $this->output_json($data);
    }
    public function edit($id)
    {
        $siswa = $this->master->getSiswaById($id);
        $inputData = [['label' => 'Nama Lengkap', 'name' => 'nama', 'value' => $siswa->nama, 'icon' => 'far fa-user', 'req' => 'required', 'class' => '', 'type' => 'text'], ['label' => 'NIS', 'name' => 'nis', 'value' => $siswa->nis, 'icon' => 'far fa-id-card', 'req' => 'required', 'class' => '', 'type' => 'number'], ['name' => 'nisn', 'label' => 'NISN', 'value' => $siswa->nisn, 'icon' => 'far fa-id-card', 'req' => '', 'class' => '', 'type' => 'text'], ['label' => 'Jenis Kelamin', 'name' => 'jenis_kelamin', 'value' => $siswa->jenis_kelamin, 'icon' => 'fas fa-venus-mars', 'req' => 'required', 'class' => '', 'type' => 'text'], ['name' => 'kelas_awal', 'label' => 'Diterima di kelas', 'value' => $siswa->kelas_awal, 'icon' => 'fas fa-graduation-cap', 'req' => 'required', 'class' => '', 'type' => 'text'], ['name' => 'tahun_masuk', 'label' => 'Tgl diterima', 'value' => $siswa->tahun_masuk, 'icon' => 'tahun far fa-calendar-alt', 'req' => 'required', 'class' => 'tahun', 'type' => 'text'], ['name' => 'sekolah_asal', 'label' => 'Sekolah Asal', 'value' => $siswa->sekolah_asal, 'icon' => 'fas fa-graduation-cap', 'req' => '', 'class' => '', 'type' => 'text']];
        $inputBio = [['name' => 'status_keluarga', 'label' => 'Status dalam Keluarga', 'value' => $siswa->status_keluarga == '' ? '1' : $siswa->status_keluarga, 'icon' => 'far fa-user', 'class' => '', 'type' => 'text'], ['name' => 'anak_ke', 'label' => 'Anak ke', 'value' => $siswa->anak_ke, 'icon' => 'far fa-user', 'class' => '', 'type' => 'number'], ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'value' => $siswa->tempat_lahir, 'icon' => 'far fa-map', 'class' => '', 'type' => 'text'], ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'value' => $siswa->tanggal_lahir, 'icon' => 'far fa-calendar', 'class' => 'tahun', 'type' => 'text'], ['class' => '', 'name' => 'agama', 'label' => 'Agama', 'value' => $siswa->agama, 'icon' => 'far fa-calendar', 'type' => 'text'], ['class' => '', 'name' => 'alamat', 'label' => 'Alamat', 'value' => $siswa->alamat, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rt', 'label' => 'Rt', 'value' => $siswa->rt, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'rw', 'label' => 'Rw', 'value' => $siswa->rw, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'value' => $siswa->kelurahan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kecamatan', 'label' => 'Kecamatan', 'value' => $siswa->kecamatan, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kabupaten', 'label' => 'Kabupaten/Kota', 'value' => $siswa->kabupaten, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'provinsi', 'label' => 'Provinsi', 'value' => $siswa->provinsi, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'kode_pos', 'label' => 'Kode Pos', 'value' => $siswa->kode_pos, 'icon' => 'far fa-user', 'type' => 'text'], ['class' => '', 'name' => 'hp', 'label' => 'Hp', 'value' => $siswa->hp, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputOrtu = [['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'value' => $siswa->nama_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ayah', 'label' => 'Pendidikan Ayah', 'value' => $siswa->pendidikan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ayah', 'label' => 'Pekerjaan Ayah', 'value' => $siswa->pekerjaan_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ayah', 'label' => 'No. HP Ayah', 'value' => $siswa->nohp_ayah, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'value' => $siswa->alamat_ayah, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'value' => $siswa->nama_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_ibu', 'label' => 'Pendidikan Ibu', 'value' => $siswa->pendidikan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_ibu', 'label' => 'Pekerjaan Ibu', 'value' => $siswa->pekerjaan_ibu, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_ibu', 'label' => 'No. HP Ibu', 'value' => $siswa->nohp_ibu, 'icon' => 'far fa-user', 'type' => 'number'], ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'value' => $siswa->alamat_ibu, 'icon' => 'far fa-user', 'type' => 'text']];
        $inputWali = [['name' => 'nama_wali', 'label' => 'Nama Wali', 'value' => $siswa->nama_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pendidikan_wali', 'label' => 'Pendidikan Wali', 'value' => $siswa->pendidikan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'pekerjaan_wali', 'label' => 'Pekerjaan Wali', 'value' => $siswa->pekerjaan_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'alamat_wali', 'label' => 'Alamat Wali', 'value' => $siswa->alamat_wali, 'icon' => 'far fa-user', 'type' => 'text'], ['name' => 'nohp_wali', 'label' => 'No. HP Wali', 'value' => $siswa->nohp_wali, 'icon' => 'far fa-user', 'type' => 'number']];
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Siswa', 'subjudul' => 'Edit Data Siswa', 'siswa' => $siswa, 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['input_data'] = json_decode(json_encode($inputData), FALSE);
        $data['input_bio'] = json_decode(json_encode($inputBio), FALSE);
        $data['input_ortu'] = json_decode(json_encode($inputOrtu), FALSE);
        $data['input_wali'] = json_decode(json_encode($inputWali), FALSE);
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/edit');
        $this->load->view('members/guru/templates/footer');
    }
    public function updateData()
    {
        $id_siswa = $this->input->post('id_siswa', true);
        $nis = $this->input->post('nis', true);
        $nisn = $this->input->post('nisn', true);
        $siswa = $this->master->getSiswaById($id_siswa);
        $u_nis = $siswa->nis === $nis ? '' : '|is_unique[master_siswa.nis]';
        $this->form_validation->set_rules('nis', 'NIS', 'required|numeric|trim|min_length[6]|max_length[30]' . $u_nis);
        if ($this->form_validation->run() == FALSE) {
            $data['insert'] = false;
            $data['text'] = 'Data Sudah ada, Pastikan NIS, dan NISN belum digunakan siswa lain';
        } else {
            $input = ['nisn' => $this->input->post('nisn', true), 'nis' => $this->input->post('nis', true), 'nama' => $this->input->post('nama', true), 'jenis_kelamin' => $this->input->post('jenis_kelamin', true), 'tempat_lahir' => $this->input->post('tempat_lahir', true), 'tanggal_lahir' => $this->input->post('tanggal_lahir', true), 'agama' => $this->input->post('agama', true), 'status_keluarga' => $this->input->post('status_keluarga', true), 'anak_ke' => $this->input->post('anak_ke', true), 'alamat' => $this->input->post('alamat', true), 'rt' => $this->input->post('rt', true), 'rw' => $this->input->post('rw', true), 'kelurahan' => $this->input->post('kelurahan', true), 'kecamatan' => $this->input->post('kecamatan', true), 'kabupaten' => $this->input->post('kabupaten', true), 'provinsi' => $this->input->post('provinsi', true), 'kode_pos' => $this->input->post('kode_pos', true), 'hp' => $this->input->post('hp', true), 'nama_ayah' => $this->input->post('nama_ayah', true), 'nohp_ayah' => $this->input->post('nohp_ayah', true), 'pendidikan_ayah' => $this->input->post('pendidikan_ayah', true), 'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah', true), 'alamat_ayah' => $this->input->post('alamat_ayah', true), 'nama_ibu' => $this->input->post('nama_ibu', true), 'nohp_ibu' => $this->input->post('nohp_ibu', true), 'pendidikan_ibu' => $this->input->post('pendidikan_ibu', true), 'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu', true), 'alamat_ibu' => $this->input->post('alamat_ibu', true), 'nama_wali' => $this->input->post('nama_wali', true), 'pendidikan_wali' => $this->input->post('pendidikan_wali', true), 'pekerjaan_wali' => $this->input->post('pekerjaan_wali', true), 'nohp_wali' => $this->input->post('nohp_wali', true), 'alamat_wali' => $this->input->post('alamat_wali', true), 'tahun_masuk' => $this->input->post('tahun_masuk', true), 'kelas_awal' => $this->input->post('kelas_awal', true), 'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah', true), 'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu', true), 'tgl_lahir_wali' => $this->input->post('tgl_lahir_wali', true), 'sekolah_asal' => $this->input->post('sekolah_asal', true), 'foto' => 'uploads/foto_siswa/' . $nis . '.jpg'];
            $action = $this->master->update('master_siswa', $input, 'id_siswa', $id_siswa);
            $data['insert'] = $input;
            $data['text'] = 'Siswa berhasil diperbaharui';
        }
        $this->output_json($data);
    }
    function uploadFile($id_siswa)
    {
        $siswa = $this->master->getSiswaById($id_siswa);
        if (isset($_FILES['foto']['name'])) {
            $config['upload_path'] = './uploads/foto_siswa/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG|JPG|PNG|GIF';
            $config['overwrite'] = true;
            $config['file_name'] = $siswa->nis;
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('foto')) {
            }
            $result = $this->upload->data();
            $data['src'] = base_url() . 'uploads/foto_siswa/' . $result['file_name'];
            $data['filename'] = pathinfo($result['file_name'], PATHINFO_FILENAME);
            $data['status'] = true;
            $this->db->set('foto', 'uploads/foto_siswa/' . $result['file_name']);
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            $data['type'] = $_FILES['foto']['type'];
            $data['size'] = $_FILES['foto']['size'];
        } else {
            $data['src'] = '';
        }
        $this->output_json($data);
    }
    function deleteFile($id_siswa)
    {
        $src = $this->input->post('src');
        $file_name = str_replace(base_url(), '', $src ?? '');
        if (!($file_name != 'assets/img/siswa.png')) {
        } else {
            if (!unlink($file_name)) {
            }
            $this->db->set('foto', '');
            $this->db->where('id_siswa', $id_siswa);
            $this->db->update('master_siswa');
            echo 'File Delete Successfully';
        }
    }
    public function delete()
    {
        $chk = $this->input->post('checked', true);
        if (!$chk) {
            $this->output_json(['status' => false]);
        } else {
            if (!$this->master->delete('master_siswa', $chk, 'id_siswa')) {
            }
            $this->master->delete('buku_induk', $chk, 'id_siswa');
            $this->output_json(['status' => true, 'total' => count($chk)]);
        }
    }
}
```

---

## File: application/controllers_progress/Walistruktur.php

```php
<?php

class Walistruktur extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        } else {
            if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
            }
            show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->form_validation->set_error_delimiters('', '');
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
        $data = ['user' => $user, 'judul' => 'Struktur Organisasi', 'subjudul' => 'Struktur Organisasi', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $struktur = $this->kelas->getStrukturKelas($guru->wali_kelas);
        if ($struktur == null) {
            $data['struktur'] = json_decode(json_encode($this->kelas->dummyStruktur()));
        } else {
            $data['struktur'] = $struktur;
        }
        $data['guru'] = $guru;
        $data['gurus'] = $this->dropdown->getAllGuru();
        $siswa = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $siswas[''] = 'Pilih Siswa';
        foreach ($siswa as $key => $value) {
            $siswas[$value->id_siswa] = $value->nama;
        }
        $data['siswas'] = $siswas;
        $data['id_kelas'] = $guru->wali_kelas;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/wali/struktur');
        $this->load->view('members/guru/templates/footer');
    }
    public function save()
    {
        $data = ['id_kelas' => $this->input->post('id_kelas'), 'ketua' => $this->input->post('ketua'), 'wakil_ketua' => $this->input->post('wakil_ketua'), 'sekretaris_1' => $this->input->post('sekretaris_1'), 'sekretaris_2' => $this->input->post('sekretaris_2'), 'bendahara_1' => $this->input->post('bendahara_1'), 'bendahara_2' => $this->input->post('bendahara_2'), 'sie_ekstrakurikuler' => $this->input->post('sie_ekstrakurikuler'), 'sie_upacara' => $this->input->post('sie_upacara'), 'sie_olahraga' => $this->input->post('sie_olahraga'), 'sie_keagamaan' => $this->input->post('sie_keagamaan'), 'sie_keamanan' => $this->input->post('sie_keamanan'), 'sie_ketertiban' => $this->input->post('sie_ketertiban'), 'sie_kebersihan' => $this->input->post('sie_kebersihan'), 'sie_keindahan' => $this->input->post('sie_keindahan'), 'sie_kesehatan' => $this->input->post('sie_kesehatan'), 'sie_kekeluargaan' => $this->input->post('sie_kekeluargaan'), 'sie_humas' => $this->input->post('sie_humas')];
        $insert = $this->db->replace('kelas_struktur', $data);
        $this->output_json($insert);
    }
}
```

---

## File: application/controllers_progress/Welcome.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Welcome extends CI_Controller
{
    public function index()
    {
        $this->load->view('welcome_message');
    }
}
```

---
