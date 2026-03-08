<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bukuinduk extends CI_Controller
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

        $this->load->library(['datatables', 'form_validation']);
        $this->form_validation->set_error_delimiters('', '');
    }

    public function output_json($data, bool $encode = true): void
    {
        $output = $encode ? json_encode($data) : $data;
        $this->output->set_content_type('application/json')->set_output($output);
    }

    public function generateTahunMasuk(string $tp, int $level): int
    {
        $tahun = (int) explode('/', $tp)[0];

        if ($level == 9) return $tahun - 2;
        if ($level == 8) return $tahun - 1;
        return $tahun;
    }

    public function index()
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Rapor_model',     'rapor');

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();

        $data = [
            'user'       => $user,
            'judul'      => 'Buku Induk',
            'subjudul'   => 'Buku Induk',
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'profile'    => $this->dashboard->getProfileAdmin($user->id),
        ];

        // Sinkronisasi buku_induk dengan master_siswa
        $count_siswa = $this->db->count_all('master_siswa');
        $count_induk = $this->db->count_all('buku_induk');

        if ($count_siswa > $count_induk) {
            $uids = $this->db->select('id_siswa, uid')->from('master_siswa')->get()->result();
            foreach ($uids as $uid) {
                $exists = $this->db->select('id_siswa')->from('buku_induk')->where('id_siswa', $uid->id_siswa)->get()->num_rows();
                if ($exists === 0) {
                    $this->db->insert('buku_induk', $uid);
                }
            }
        }

        $siswas      = $this->master->getDataInduk();
        $fisik_siswa = $this->rapor->getAllRaporFisik();
        $data_siswa  = [];
        $noinduk     = [];

        foreach ($siswas as $id_siswa => $siswa) {
            $rapor_fisik = $fisik_siswa[$id_siswa] ?? [];

            foreach ($rapor_fisik as $rf) {
                $rf->fisik = unserialize($rf->fisik);
                foreach ($rf->fisik as $value) {
                    $value->kondisi = unserialize($value->kondisi);
                }
            }

            $tahunMasuk = $siswa->tahun_masuk !== null
                ? (int) explode('-', $siswa->tahun_masuk)[0]
                : 0;

            $data_tahun = $this->buildDataTahun($tahunMasuk, $setting->jenjang);

            $berat    = [];
            $tinggi   = [];
            $penyakit = [];
            $kelainan = [];

            foreach ($data_tahun as $dtp) {
                $berat[$dtp][1]    = '';
                $berat[$dtp][2]    = '';
                $tinggi[$dtp][1]   = '';
                $tinggi[$dtp][2]   = '';
                $penyakit[$dtp][1] = '';
                $penyakit[$dtp][2] = '';
                $kelainan[$dtp][1] = '';
                $kelainan[$dtp][2] = '';

                if (isset($rapor_fisik[$dtp])) {
                    foreach ($rapor_fisik[$dtp]->fisik as $rf) {
                        $berat[$dtp][$rf->id_smt]  = $rf->berat;
                        $tinggi[$dtp][$rf->id_smt] = $rf->tinggi;
                    }
                }
            }

            $noinduk[$siswa->id_siswa] = [
                'nis'  => $siswa->nis,
                'nisn' => $siswa->nisn,
            ];

            $data_siswa[$siswa->id_siswa] = [
                'nis'   => $siswa->nis,
                'nisn'  => $siswa->nisn,
                'page1' => [
                    'A' => [
                        'title' => 'KETERANGAN TENTANG DIRI SISWA',
                        'value' => [
                            'Nama Siswa'              => ['Nama Lengkap' => $siswa->nama, 'Nama Panggilan' => ''],
                            'Jenis Kelamin'           => $siswa->jenis_kelamin,
                            'Tempat dan Tgl Lahir'    => $siswa->tempat_lahir,
                            'Agama'                   => $siswa->agama,
                            'Kewarganegaraan'         => $siswa->warga_negara,
                            'Anak ke'                 => $siswa->anak_ke,
                            'Jumlah Sdr. Kandung'     => '',
                            'Jumlah Sdr. Tiri'        => '',
                            'Jumlah Sdr. Angkat'      => '',
                            'Anak Yatim/Yatim Piatu'  => '',
                            'Bahasa Sehari-hari'      => '',
                        ],
                    ],
                    'B' => [
                        'title' => 'KETERANGAN TEMPAT TINGGAL',
                        'value' => [
                            'Alamat'          => $siswa->alamat,
                            'Nomor Telepon'   => $siswa->hp,
                            'Tinggal Bersama' => '',
                            'Jarak ke Sekolah' => '',
                        ],
                    ],
                    'C' => [
                        'title' => 'KETERANGAN KESEHATAN',
                        'value' => [
                            'Golongan Darah'  => '',
                            'Keadaan Jasmani' => '[table]',
                        ],
                        'table' => [
                            'tahun'    => $data_tahun,
                            'berat'    => $berat,
                            'tinggi'   => $tinggi,
                            'penyakit' => $penyakit,
                            'kelainan' => $kelainan,
                        ],
                    ],
                    'D' => [
                        'title' => 'KETERANGAN PENDIDIKAN',
                        'value' => [
                            'Pendidikan Sebelumnya'  => ['Lulusan Dari' => $siswa->sekolah_asal, 'Nomor Ijazah' => ''],
                            'Pindahan'               => ['Dari Sekolah' => '', 'Alasan' => ''],
                            'Diterima Disekolah Ini' => ['Di Tingkat' => $siswa->kelas_awal, 'Kelompok' => '', 'Jurusan' => '', 'Tanggal' => $siswa->tahun_masuk],
                        ],
                    ],
                ],
                'page2' => [
                    'E' => [
                        'title' => 'KETERANGAN TENTANG AYAH KANDUNG',
                        'value' => [
                            'Nama'                    => $siswa->nama_ayah,
                            'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ayah,
                            'Agama'                   => '',
                            'Kewarganegaraan'         => '',
                            'Pendidikan'              => $siswa->pendidikan_ayah,
                            'Pekerjaan'               => $siswa->pekerjaan_ayah,
                            'Penghasilan per Bulan'   => '',
                            'Alamat / Nomor Telepon'  => $siswa->nohp_ayah,
                            'Keberadaan Ayah'         => 'Masih Hidup / Meninggal Dunia Tahun: ........',
                        ],
                    ],
                    'F' => [
                        'title' => 'KETERANGAN TENTANG IBU KANDUNG',
                        'value' => [
                            'Nama'                    => $siswa->nama_ibu,
                            'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_ibu,
                            'Agama'                   => '',
                            'Kewarganegaraan'         => '',
                            'Pendidikan'              => $siswa->pendidikan_ibu,
                            'Pekerjaan'               => $siswa->pekerjaan_ibu,
                            'Penghasilan per Bulan'   => '',
                            'Alamat / Nomor Telepon'  => $siswa->nohp_ibu,
                            'Keberadaan Ibu'          => 'Masih Hidup / Meninggal Dunia Tahun',
                        ],
                    ],
                    'G' => [
                        'title' => 'KETERANGAN TENTANG WALI',
                        'value' => [
                            'Nama'                    => $siswa->nama_wali,
                            'Tempat dan Tanggal Lahir' => $siswa->tgl_lahir_wali,
                            'Agama'                   => '',
                            'Kewarganegaraan'         => '',
                            'Pendidikan'              => $siswa->pendidikan_wali,
                            'Pekerjaan'               => $siswa->pekerjaan_wali,
                            'Penghasilan per Bulan'   => '',
                            'Alamat / Nomor Telepon'  => $siswa->nohp_wali,
                        ],
                    ],
                    'H' => [
                        'title' => 'KEGEMARAN SISWA',
                        'value' => [
                            'Kesenian'   => '',
                            'Olah Raga'  => '',
                            'Organisasi' => '',
                            'Lain-lain'  => '',
                        ],
                    ],
                ],
                'page3' => [
                    'I' => [
                        'title' => 'KETERANGAN PERKEMBANGAN SISWA',
                        'value' => [
                            'Menerima Bea Siswa'   => '[tahun]',
                            'Meninggalkan Sekolah' => ['Tanggal' => '', 'Alasan' => ''],
                            'Akhir Pendidikan'     => ['Tamat Belajar' => $siswa->tahun_lulus, 'Nomor Ijazah' => $siswa->no_ijazah],
                        ],
                        'tahun' => [
                            'Tahun ............/ TK ……………………..dari……………………...',
                            'Tahun ............/ TK ……………………..dari……………………...',
                            'Tahun ............/ TK ……………………..dari……………………...',
                        ],
                    ],
                    'J' => [
                        'title' => 'KETERANGAN SETELAH SELESAI PENDIDIKAN',
                        'value' => [
                            'Melanjutkan di' => '',
                            'Bekerja'        => ['Tanggal Mulai Bekerja' => '', 'Nama Tempat Bekerja' => '', 'Penghasilan' => ''],
                        ],
                    ],
                    'K' => [
                        'title' => 'LAIN - LAIN',
                        'value' => ['Catatan Yang Penting' => ''],
                    ],
                ],
            ];
        }

        $level = $setting->jenjang == '1' ? '6' : ($setting->jenjang == '2' ? '9' : '12');

        $data['rapor_fisik']  = $rapor_fisik ?? [];
        $data['noinduk']      = $noinduk;
        $data['siswas']       = $siswas;
        $data['detail']       = $data_siswa;
        $data['jumlah_lulus'] = $this->rapor->getJumlahLulus($tp->id_tp - 1, '2', $level);

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('setting/induk');
        $this->load->view('_templates/dashboard/_footer');
    }

    private function buildDataTahun(int $tahun, string $jenjang): array
    {
        $count = $jenjang == '1' ? 6 : 3;
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = ($tahun + $i) . '/' . ($tahun + $i + 1);
        }
        return $result;
    }
}
