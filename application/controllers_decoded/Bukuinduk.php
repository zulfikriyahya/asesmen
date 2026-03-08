<?php

class Bukuinduk extends CI_Controller
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
        $this->form_validation->set_error_delimiters('', '');
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    function generateTahunMasuk($tp, $level)
    {
        $tahun = explode('/', $tp ?? '')[0];
        $thn = $tahun;
        if ($level == 9) {
            $thn = $tahun - 2;
            return $thn;
        } else {
            if ($level == 8) {
                goto gC72e;
            }
            if ($level == 7) {
                goto L1vUy;
            }
            return $thn;
        }
    }
    public function index()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Rapor_model', 'rapor');
        $user = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $data = ['user' => $user, 'judul' => 'Buku Induk', 'subjudul' => 'Buku Induk', 'setting' => $setting];
        $arrTp = $this->dashboard->getTahun();
        $arrSmt = $this->dashboard->getSemester();
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $data['tp'] = $arrTp;
        $data['tp_active'] = $tp;
        $data['smt'] = $arrSmt;
        $data['smt_active'] = $smt;
        $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
        $count_siswa = $this->db->count_all('master_siswa');
        $count_induk = $this->db->count_all('buku_induk');
        if (!($count_siswa > $count_induk)) {
        }
        $uids = $this->db->select('id_siswa, uid')->from('master_siswa')->get()->result();
        foreach ($uids as $uid) {
            $check = $this->db->select('id_siswa')->from('buku_induk')->where('id_siswa', $uid->id_siswa);
            if (!($check->get()->num_rows() == 0)) {
            }
            $this->db->insert('buku_induk', $uid);
        }
        $siswas = $this->master->getDataInduk();
        $deskFisik = $this->rapor->getAllDeskripsiFisikKelas();
        $fisik_siswa = $this->rapor->getAllRaporFisik();
        $data_siswa = [];
        $thn_siswa = [];
        foreach ($siswas as $id_siswa => $siswa) {
            $rapor_fisik = isset($fisik_siswa[$id_siswa]) ? $fisik_siswa[$id_siswa] : [];
            foreach ($rapor_fisik as $rf) {
                $rf->fisik = unserialize($rf->fisik);
                foreach ($rf->fisik as $value) {
                    $value->kondisi = unserialize($value->kondisi);
                }
            }
            if ($siswa->tahun_masuk != null) {
            }
            $tahunMasuk = '';
            if ($setting->jenjang == '1') {
            }
            $data_tahun = [intval($tahunMasuk) . '/' . (intval($tahunMasuk) + 1), intval($tahunMasuk) + 1 . '/' . (intval($tahunMasuk) + 2), intval($tahunMasuk) + 2 . '/' . (intval($tahunMasuk) + 3)];
            $berat = [];
            $tinggi = [];
            $penyakit = [];
            $kelainan = [];
            foreach ($data_tahun as $dtp) {
                $berat[$dtp][1] = '';
                $berat[$dtp][2] = '';
                $tinggi[$dtp][1] = '';
                $tinggi[$dtp][2] = '';
                $penyakit[$dtp][1] = '';
                $penyakit[$dtp][2] = '';
                $kelainan[$dtp][1] = '';
                $kelainan[$dtp][2] = '';
                if (!isset($rapor_fisik[$dtp])) {
                }
                foreach ($rapor_fisik[$dtp]->fisik as $rf) {
                    $berat[$dtp][$rf->id_smt] = $rf->berat;
                    $tinggi[$dtp][$rf->id_smt] = $rf->tinggi;
                }
            }
            $noinduk[$siswa->id_siswa] = ['nis' => $siswa->nis, 'nisn' => $siswa->nisn];
            $data_siswa[$siswa->id_siswa] = ['nis' => $siswa->nis, 'nisn' => $siswa->nisn, 'page1' => ['A' => ['title' => 'KETERANGAN TENTANG DIRI SISWA', 'value' => ['Nama Siswa' => ['Nama Lengkap' => $siswa->nama, 'Nama Panggilan' => ''], 'Jenis Kelamin' => $siswa->jenis_kelamin, 'Tempat dan Tgl Lahir' => $siswa->tempat_lahir, 'Agama' => $siswa->agama, 'Kewarganegaraan' => $siswa->warga_negara, 'Anak ke' => $siswa->anak_ke, 'Jumlah Sdr. Kandung' => '', 'Jumlah Sdr. Tiri' => '', 'Jumlah Sdr. Angkat' => '', 'Anak Yatim/Yatim Piatu' => '', 'Bahasa Sehari-hari' => '']], 'B' => ['title' => 'KETERANGAN TEMPAT TINGGAL', 'value' => ['Alamat' => $siswa->alamat, 'Nomor Telepon' => $siswa->hp, 'Tinggal Bersama' => '', 'Jarak ke Sekolah' => '']], 'C' => ['title' => 'KETERANGAN KESEHATAN', 'value' => ['Golongan Darah' => '', 'Keadaan Jasmani' => '[table]'], 'table' => ['tahun' => $data_tahun, 'berat' => $berat, 'tinggi' => $tinggi, 'penyakit' => $penyakit, 'kelainan' => $kelainan]], 'D' => ['title' => 'KETERANGAN PENDIDIKAN', 'value' => ['Pendidikan Sebelumnya' => ['Lulusan Dari' => $siswa->sekolah_asal, 'Nomor Ijazah' => ''], 'Pindahan' => ['Dari Sekolah' => '', 'Alasan' => ''], 'Diterima Disekolah Ini' => ['Di Tingkat' => $siswa->kelas_awal, 'Kelompok' => '', 'Jurusan' => '', 'Tanggal' => $siswa->tahun_masuk]]]], 'page2' => ['E' => ['title' => 'KETERANGAN TENTANG AYAH KANDUNG', 'value' => ['Nama' => $siswa->nama_ayah, 'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah, 'Agama' => '', 'Kewarganegaraan' => '', 'Pendidikan' => $siswa->pendidikan_ayah, 'Pekerjaan' => $siswa->pekerjaan_ayah, 'Penghasilan per Bulan' => '', 'Alamat / Nomor Telepon' => $siswa->nohp_ayah, 'Keberadaan Ayah' => 'Masih Hidup / Meninggal Dunia Tahun: ........']], 'F' => ['title' => 'KETERANGAN TENTANG IBU KANDUNG', 'value' => ['Nama' => $siswa->nama_ayah, 'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah, 'Agama' => '', 'Kewarganegaraan' => '', 'Pendidikan' => $siswa->pendidikan_ayah, 'Pekerjaan' => $siswa->pekerjaan_ayah, 'Penghasilan per Bulan' => '', 'Alamat / Nomor Telepon' => $siswa->nohp_ayah, 'Keberadaan Ibu' => 'Masih Hidup / Meninggal Dunia Tahun']], 'G' => ['title' => 'KETERANGAN TENTANG WALI', 'value' => ['Nama' => $siswa->nama_ayah, 'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah, 'Agama' => '', 'Kewarganegaraan' => '', 'Pendidikan' => $siswa->pendidikan_ayah, 'Pekerjaan' => $siswa->pekerjaan_ayah, 'Penghasilan per Bulan' => '', 'Alamat / Nomor Telepon' => $siswa->nohp_ayah]], 'H' => ['title' => 'KEGEMARAN SISWA', 'value' => ['Kesenian' => '', 'Olah Raga' => '', 'Organisasi' => '', 'Lain–lain' => '']]], 'page3' => ['I' => ['title' => 'KETERANGAN PERKEMBANGAN SISWA', 'value' => ['Menerima Bea Siswa' => '[tahun]', 'Meninggalkan Sekolah' => ['Tanggal' => '', 'Alasan' => ''], 'Akhir Pendidikan' => ['Tamat Belajar' => $siswa->tahun_lulus, 'Nomor Ijazah' => $siswa->no_ijazah]], 'tahun' => ['Tahun ............/ TK ……………………..dari……………………..', 'Tahun ............/ TK ……………………..dari……………………..', 'Tahun ............/ TK ……………………..dari……………………..']], 'J' => ['title' => 'KETERANGAN SETELAH SELESAI PENDIDIKAN', 'value' => ['Melanjutkan di' => '', 'Bekerja' => ['Tanggal Mulai Bekerja' => '', 'Nama Tempat Bekerja' => '', 'Penghasilan' => '']]], 'K' => ['title' => 'LAIN – LAIN', 'value' => ['Catatan Yang Penting' => '']]]];
        }
        $data['rapor_fisik'] = $rapor_fisik;
        $data['noinduk'] = $noinduk;
        $data['siswas'] = $siswas;
        $data['detail'] = $data_siswa;
        $data['arr_test'] = $thn_siswa;
        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : ($setting->jenjang == '1' ? '3' : '12'));
        $data['jumlah_lulus'] = $this->rapor->getJumlahLulus($tp->id_tp - 1, '2', $level);
        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/induk');
        $this->load->view('_templates/dashboard/_footer');
    }
}