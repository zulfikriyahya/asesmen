<?php

class Cbtnilai extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        $this->load->library(['datatables', 'form_validation']);
        $this->load->library('upload');
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    private function arrToUpper($val)
    {
        return strtoupper($val ?? '');
    }
    private function sortArrays(&$array)
    {
        foreach ($array as &$subArray) {
            if (!$subArray) {
            }
            sort($subArray);
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
        }
        $id_guru = null;
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
                }
                $arrKelas[$kls->kelas] = $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas);
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
            }
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
                }
                $arrjwbnSiswa[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                    $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                }
            }
            if ($jawaban_siswa->jawaban_siswa) {
            }
            $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
            $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
            $arrjwbn = [];
            foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                if (!($idx > 0)) {
                }
                $arrjwbn[$idx] = [];
                foreach ($jbs as $idxs => $jb) {
                    if (!($idxs > 0)) {
                    }
                    if (!($jb === '1')) {
                    }
                    $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
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
        }
        $skor->dikoreksi = $nilai_input->dikoreksi;
        $jawaban_pg = $ada_jawaban_pg ? $jawabans_siswa[$siswa->id_siswa]['1'] : [];
        $benar_pg = 0;
        $salah_pg = 0;
        if (!($info->tampil_pg > 0)) {
        }
        if (!($jawaban_pg && count($jawaban_pg) > 0)) {
        }
        foreach ($jawaban_pg as $num => $jwb_pg) {
            $benar = false;
            if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
            }
            if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban ?? '')) {
            }
            $salah_pg += 1;
            $benar = false;
            $ks = array_search($jwb_pg->nomor_soal, array_column($soal[1], 'nomor_soal'));
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
            }
            foreach ($jawab_pg2->jawaban_siswa as $js) {
                if (!in_array($js, $jawab_pg2->jawaban)) {
                }
                array_push($arr_benar, true);
            }
            if (!($jawab_pg2->jawaban && count($jawab_pg2->jawaban) > 0)) {
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
            }
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
                }
                $differentCount += count($subArray1);
                $item_kurang += count($subArray1);
                $arrBenar[$key]->kurang += count($subArray1);
            }
            $sameCounts[3][$ks] = $sameCount;
            $differentCounts[3][$ks] = $differentCount;
            $point_soal = 1 / $items * $item_benar * $point_benar;
            $benar_jod += 1 / $items * $item_benar;
            $headSoal = array_shift($arrSoal);
            $arrJwbSoal = [];
            foreach ($arrSoal as $kolSoal) {
                $jwb = new stdClass();
                foreach ($kolSoal as $pos => $kol) {
                    if (!($kol == '1')) {
                    }
                    $jwb->subtitle[] = $headSoal[$pos];
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
                    }
                    $sub = $headJawab[$po];
                    $jwbs->subtitle[] = $sub;
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
            }
            $benar_is++;
            $ks = array_search($jawab_is->nomor_soal, array_column($soal[4], 'nomor_soal'));
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
            }
            $benar_es++;
            $ks = array_search($jawab_es->nomor_soal, array_column($soal[5], 'nomor_soal'));
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
            }
            $dur_siswa = $durasi;
        }
        $log_siswa = [];
        foreach ($logs as $log) {
            if (!($log->id_siswa == $siswa->id_siswa)) {
            }
            array_push($log_siswa, $log);
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
        }
        $this->db->set($jenis, $jml);
        $this->db->where('id_nilai', $siswa . '0' . $jadwal);
        $this->db->update('cbt_nilai');
        $data['success'] = $updated;
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
                }
                $jawaban_siswa->opsi_a = @unserialize($jawaban_siswa->opsi_a ?? '');
                $jawaban_siswa->jawaban_siswa = @unserialize($jawaban_siswa->jawaban_siswa ?? '');
                $jawaban_siswa->jawaban_benar = @unserialize($jawaban_siswa->jawaban_benar ?? '');
                $jawaban_siswa->jawaban_benar = array_map([$this, 'arrToUpper'], $jawaban_siswa->jawaban_benar ?? ['']);
                $jawaban_siswa->jawaban_benar = array_filter($jawaban_siswa->jawaban_benar, 'strlen');
                if (!($jawaban_siswa->jenis_soal == '3')) {
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
                    }
                    $arrjwbnSiswa[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($idxs > 0)) {
                        }
                        if (!($jb === '1')) {
                        }
                        $arrjwbnSiswa[$idx][] = $arrAlphabet[$idxs - 1];
                    }
                }
                if ($jawaban_siswa->jawaban_siswa) {
                }
                $jawaban_siswa->jawaban_siswa = ['links' => $arrjwbnSiswa];
                $jawaban_siswa->jawaban_siswa = json_decode(json_encode($jawaban_siswa->jawaban_siswa));
                $arrjwbn = [];
                foreach ($jawaban_siswa->jawaban_benar->jawaban as $idx => $jbs) {
                    if (!($idx > 0)) {
                    }
                    $arrjwbn[$idx] = [];
                    foreach ($jbs as $idxs => $jb) {
                        if (!($idxs > 0)) {
                        }
                        if (!($jb === '1')) {
                        }
                        $arrjwbn[$idx][] = $arrAlphabet[$idxs - 1];
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
            }
            if (!(count($jawaban_pg) > 0)) {
            }
            foreach ($jawaban_pg as $jwb_pg) {
                if (!($jwb_pg != null && $jwb_pg->jawaban_siswa != null)) {
                }
                if (strtoupper($jwb_pg->jawaban_siswa ?? '') == strtoupper($jwb_pg->jawaban_benar ?? '')) {
                }
                $salah_pg += 1;
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
                if (!$jawab_pg2->jawaban_siswa) {
                }
                foreach ($jawab_pg2->jawaban_siswa as $js) {
                    if (!in_array($js, $jawab_pg2->jawaban_benar)) {
                    }
                    array_push($arr_benar, true);
                }
                if (!(count($jawab_pg2->jawaban_benar) > 0)) {
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
                }
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
                    }
                    $differentCount += count($subArray1);
                    $item_kurang += count($subArray1);
                    $arrBenar[$key]->kurang += count($subArray1);
                }
                $point_soal = 1 / $items * $item_benar * $point_benar;
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
                }
                $benar_is++;
                $otomatis_is = $jawab_is->nilai_otomatis;
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
                }
                $benar_es++;
                $otomatis_es = $jawab_es->nilai_otomatis;
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
        }
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/nilai/nilai_essai');
        $this->load->view('members/guru/templates/footer');
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
            }
            array_push($blm_selesai, $nilai->id_siswa);
        }
        $data['success'] = $update;
        $data['data'] = $nilais;
        $data['blm_selesai'] = count($blm_selesai);
        $this->output_json($data);
    }
}