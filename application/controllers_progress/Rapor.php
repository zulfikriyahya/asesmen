<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rapor extends CI_Controller
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
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $no_update = $this->db->field_exists('nip_kepsek', 'rapor_admin_setting');
        if (!$no_update) {
            $field = [
                'nip_kepsek'   => ['type' => 'int', 'constraint' => 1, 'default' => 0],
                'nip_walikelas' => ['type' => 'int', 'constraint' => 1, 'default' => 0],
            ];
            $this->dbforge->add_column('rapor_admin_setting', $field);
        }
        redirect('rapor/raporkkm');
    }

    public function saveRaporAdmin()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $input = [
            'id_setting'           => $tp->id_tp . $smt->id_smt,
            'id_tp'                => $tp->id_tp,
            'id_smt'               => $smt->id_smt,
            'tgl_rapor_pts'        => $this->input->post('tgl_rapor_pts', true),
            'nip_kepsek'           => $this->input->post('nip_kepsek', true),
            'nip_walikelas'        => $this->input->post('nip_walikelas', true),
            'tgl_rapor_akhir'      => $this->input->post('tgl_rapor_akhir', true),
            'tgl_rapor_kelas_akhir' => $this->input->post('tgl_rapor_kelas_akhir', true),
            'kkm_tunggal'          => $this->input->post('kkm_tunggal', true),
            'kkm'                  => $this->input->post('kkm', true),
            'bobot_ph'             => $this->input->post('bobot_ph', true),
            'bobot_pts'            => $this->input->post('bobot_pts', true),
            'bobot_pas'            => $this->input->post('bobot_pas', true),
        ];
        $data['status'] = $this->db->replace('rapor_admin_setting', $input);
        $this->output_json($data);
    }

    public function raporkkm()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = $mapel_guru->mapel_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->mapel_kelas))) : [];
        $kelases    = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);

        $arrMapel = [];
        $arrKelas = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                if ($key_kelas !== false) {
                    $arrKelas[$m->id_mapel][] = [
                        'id_kelas'   => $kls->kelas,
                        'nama_kelas' => $kelases[$key_kelas]->nama_kelas,
                    ];
                }
            }
        }

        $ekstra         = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $arrEkstra      = [];
        $arrKelasEkstra = [];
        foreach ($ekstra as $m) {
            $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
            foreach ($m->kelas_ekstra as $kls) {
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                if ($key_kelas !== false) {
                    $arrKelasEkstra[$m->id_ekstra][] = [
                        'id_kelas'   => $kls->kelas,
                        'nama_kelas' => $kelases[$key_kelas]->nama_kelas,
                    ];
                }
            }
        }

        $data = [
            'user'        => $user,
            'judul'       => 'KKM dan Bobot',
            'subjudul'    => 'Input KKM dan Bobot Nilai',
            'setting'     => $this->dashboard->getSetting(),
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'guru'        => $guru,
            'mapel'       => $arrMapel,
            'kelas'       => $arrKelas,
            'ekstra'      => $arrEkstra,
            'kelas_ekstra' => $arrKelasEkstra,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kkm/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function datakkm($mapel, $kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $kkm = $kelas != null ? $this->rapor->getKkm($mapel . $kelas . $tp->id_tp . $smt->id_smt . '1') : '';

        $data = [
            'mapel'   => $mapel,
            'kelas'   => $kelas,
            'kkm'     => $kkm,
            'tp'      => $tp->id_tp,
            'smt'     => $smt->id_smt,
            'setting' => $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt),
        ];
        $this->output_json($data);
    }

    public function datakkmEkstra($ekstra, $kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $kkm = $kelas != null ? $this->rapor->getKkm($ekstra . $kelas . $tp->id_tp . $smt->id_smt . '2') : '';

        $data = [
            'ekstra'  => $ekstra,
            'kelas'   => $kelas,
            'kkm'     => $kkm,
            'tp'      => $tp->id_tp,
            'smt'     => $smt->id_smt,
            'setting' => $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt),
        ];
        $this->output_json($data);
    }

    public function saveKkm()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $input = [
            'id_kkm'    => $this->input->post('id_kkm', true),
            'id_tp'     => $tp->id_tp,
            'id_smt'    => $smt->id_smt,
            'bobot_ph'  => $this->input->post('bobot_ph', true),
            'bobot_pts' => $this->input->post('bobot_pts', true),
            'bobot_pas' => $this->input->post('bobot_pas', true),
            'kkm'       => $this->input->post('kkm', true),
            'beban_jam' => $this->input->post('beban', true),
            'jenis'     => $this->input->post('jenis_kkm', true),
            'id_kelas'  => $this->input->post('id_kelas', true),
            'id_mapel'  => $this->input->post('id_mapel', true),
        ];
        $data['status'] = $this->db->replace('rapor_kkm', $input);
        $this->output_json($data);
    }

    public function raporkikd()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));
        $kelases    = $this->kelas->getKelasList($tp->id_tp, $smt->id_smt);

        $arrMapel = [];
        $arrKelas = [];
        if ($mapel != null) {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                foreach ($m->kelas_mapel as $kls) {
                    $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                    if ($key_kelas !== false) {
                        $arrKelas[$m->id_mapel][] = [
                            'id_kelas'   => $kls->kelas,
                            'nama_kelas' => $kelases[$key_kelas]->nama_kelas,
                        ];
                    }
                }
            }
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Indikator KD',
            'subjudul'   => 'Ringkasan Materi Penilaian',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'mapel'      => $arrMapel,
            'kelas'      => $arrKelas,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kikd/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function datakikd($mapel, $kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $kikds = $this->rapor->getKikdMapelKelas($mapel, $kelas, $tp->id_tp, $smt->id_smt);

        $arrKiKd = [];
        if ($kelas != null) {
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                for ($i = 0; $i < 8; $i++) {
                    $no     = $i + 1;
                    $id_key = $mapel . $kelas . $asp . $no;
                    $key_ki = array_search($id_key, array_column($kikds, 'id_kikd'));
                    $arrKiKd[$asp][$id_key] = $key_ki !== false
                        ? ['materi_kikd' => $kikds[$key_ki]->materi_kikd]
                        : ['materi_kikd' => ''];
                }
            }
        }

        $data = [
            'mapel' => $mapel,
            'kelas' => $kelas,
            'kikd'  => $arrKiKd,
        ];
        $this->output_json($data);
    }

    public function saveKikd()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $sjson   = $this->input->post('materi', true);
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $updated = false;

        foreach ((array) $sjson as $aspek => $mapel_kelas) {
            foreach ($mapel_kelas as $idmk => $kikd) {
                foreach ($kikd as $id => $materi) {
                    $input = [
                        'id_kikd'        => $id,
                        'id_mapel_kelas' => $idmk,
                        'aspek'          => $aspek,
                        'id_tp'          => $tp->id_tp,
                        'id_smt'         => $smt->id_smt,
                        'materi_kikd'    => $materi,
                    ];
                    $updated = $this->db->replace('rapor_kikd', $input);
                }
            }
        }

        $this->output_json(['status' => $updated, 'json' => $sjson]);
    }

    public function raporNilai()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = $mapel_guru->mapel_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->mapel_kelas))) : [];

        $siswas        = [];
        $arrMapel      = [];
        $arrKelasMapel = [];
        $levelsMapel   = [];
        $harian        = [];
        $pts           = [];
        $pas           = [];

        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $kelas_guru = $this->kelas->get_one($kls->kelas);
                if ($kelas_guru != null) {
                    $levelsMapel[]                                     = $kelas_guru->level_id;
                    $arrKelasMapel[$m->id_mapel][]                     = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                    $siswas[$m->id_mapel][$kelas_guru->nama_kelas]     = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                    $harian[$m->id_mapel][$kelas_guru->nama_kelas]     = $this->rapor->cekNilaiHarianKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                    $pts[$m->id_mapel][$kelas_guru->nama_kelas]        = $this->rapor->cekNilaiPtsKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                    $pas[$m->id_mapel][$kelas_guru->nama_kelas]        = $this->rapor->cekNilaiAkhirKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                }
            }
        }

        $ekstra         = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $arrEkstra      = [];
        $arrKelasEkstra = [];
        $ektras         = [];
        $siswae         = [];

        foreach ($ekstra as $m) {
            $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
            foreach ($m->kelas_ekstra as $kls) {
                $kelas_guru = $this->kelas->get_one($kls->kelas);
                if ($kelas_guru != null) {
                    $arrKelasEkstra[$m->id_ekstra][]                   = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                    $siswae[$m->id_ekstra][$kelas_guru->nama_kelas]    = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                    $ektras[$m->id_ekstra][$kelas_guru->nama_kelas]    = $this->rapor->cekNilaiEkstraKelas($m->id_ekstra, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                }
            }
        }

        $data = [
            'user'        => $user,
            'judul'       => 'Input Nilai',
            'subjudul'    => 'Input Nilai Rapor',
            'setting'     => $this->dashboard->getSetting(),
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'guru'        => $guru,
            'mapel'       => $arrMapel,
            'kelas_mapel' => $arrKelasMapel,
            'level'       => array_unique($levelsMapel),
            'siswas'      => $siswas,
            'harian'      => $harian,
            'pts'         => $pts,
            'pas'         => $pas,
            'ekstra'      => $arrEkstra,
            'kelas_ekstra' => $arrKelasEkstra,
            'ekstras'     => $ektras,
            'siswae'      => $siswae,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function raporNilaiGuru($filter = null, $id_mapel = null)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $jabatan_guru = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        foreach ($jabatan_guru as $jabatan) {
            $jabatan->mapel_kelas = $jabatan->mapel_kelas == null ? [] : unserialize($jabatan->mapel_kelas);
            $jabatan->ekstra_kelas = $jabatan->ekstra_kelas == null ? [] : unserialize($jabatan->ekstra_kelas);
        }

        $ret[''] = 'Pilih Mapel';
        $ret[''] = 'Pilih Eskul';

        $data = [
            'user'            => $user,
            'judul'           => 'Semua Nilai',
            'subjudul'        => 'Semua Nilai Rapor',
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $tp,
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $smt,
            'guru'            => $guru,
            'mapel'           => ['' => 'Pilih Mapel'] + $this->dropdown->getAllMapel(),
            'ekstra'          => ['' => 'Pilih Eskul'] + $this->dropdown->getAllEkskul(),
            'filter'          => ['' => 'Filter berdasarkan', '1' => 'Mata Pelajaran', '2' => 'Ekstrakurikuler'],
            'ekstra_selected' => $id_mapel,
            'mapel_selected'  => $id_mapel,
            'filter_selected' => $filter,
        ];

        if ($id_mapel == null) {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/nilaiguru');
            $this->load->view('members/guru/templates/footer');
            return;
        }

        $setting     = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm_ekstra  = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '2');
        $siswas      = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $nilai       = [];
        $arrKiKd     = [];

        $aspek = ['1', '2'];
        foreach ($aspek as $asp) {
            for ($i = 0; $i < 8; $i++) {
                $no = $i + 1;
                $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
            }
        }

        $guru_mapel = '';
        foreach ($jabatan_guru as $jab) {
            foreach ($jab->ekstra_kelas as $mk) {
                if ($mk['id_ekstra'] == $id_mapel) {
                    foreach ($mk['kelas_ekstra'] as $km) {
                        if ($km['kelas'] == $guru->wali_kelas) {
                            $guru_mapel = $jab->nama_guru;
                        }
                    }
                }
            }
        }

        $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
        foreach ($siswas as $siswa) {
            $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
        }

        $data['siswas']     = $siswas;
        $data['nilai']      = $nilai;
        $data['kikd']       = $arrKiKd;
        $data['guru_mapel'] = $guru_mapel;
        $data['kkm_ekstra'] = $kkm_ekstra;

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/nilaiguru');
        $this->load->view('members/guru/templates/footer');
    }

    public function raporCekNilai($filter = null, $id_mapel = null)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $jabatan_guru = $this->master->getGuruMapel($tp->id_tp, $smt->id_smt);
        foreach ($jabatan_guru as $jabatan) {
            $jabatan->mapel_kelas  = $jabatan->mapel_kelas == null ? [] : unserialize($jabatan->mapel_kelas);
            $jabatan->ekstra_kelas = $jabatan->ekstra_kelas == null ? [] : unserialize($jabatan->ekstra_kelas);
        }

        $data = [
            'user'            => $user,
            'judul'           => 'Semua Nilai',
            'subjudul'        => 'Semua Nilai Rapor',
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $tp,
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $smt,
            'guru'            => $guru,
            'mapel'           => ['' => 'Pilih Mapel'] + $this->dropdown->getAllMapel(),
            'ekstra'          => ['' => 'Pilih Eskul'] + $this->dropdown->getAllEkskul(),
            'filter'          => ['' => 'Filter berdasarkan', '1' => 'Mata Pelajaran', '2' => 'Ekstrakurikuler'],
            'ekstra_selected' => $id_mapel,
            'mapel_selected'  => $id_mapel,
            'filter_selected' => $filter,
        ];

        if ($id_mapel == null) {
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/rapor/nilai/periksa');
            $this->load->view('members/guru/templates/footer');
            return;
        }

        $jenis   = $filter == '1' ? '1' : '2';
        $kkm     = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . $jenis);
        $siswas  = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
        $nilai   = [];
        $arrKiKd = [];

        $aspek = ['1', '2'];
        foreach ($aspek as $asp) {
            for ($i = 0; $i < 8; $i++) {
                $no = $i + 1;
                $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
            }
        }

        $guru_mapel = '';
        foreach ($jabatan_guru as $jab) {
            foreach ($jab->ekstra_kelas as $mk) {
                if ($mk['id_ekstra'] == $id_mapel) {
                    foreach ($mk['kelas_ekstra'] as $km) {
                        if ($km['kelas'] == $guru->wali_kelas) {
                            $guru_mapel = $jab->nama_guru;
                        }
                    }
                }
            }
        }

        $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
        foreach ($siswas as $siswa) {
            $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
        }

        $data['siswas']     = $siswas;
        $data['nilai']      = $nilai;
        $data['kikd']       = $arrKiKd;
        $data['kkm']        = $kkm;
        $data['guru_mapel'] = $guru_mapel;

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/periksa');
        $this->load->view('members/guru/templates/footer');
    }

    public function inputHarian($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels     = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));

        $mapel = '';
        $kelas = [];
        foreach ($mapels as $m) {
            if ($m->id_mapel === $id_mapel) {
                $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            }
            foreach ($m->kelas_mapel as $kls) {
                if ($kls->kelas === $id_kelas) {
                    $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                }
            }
        }

        $siswas  = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm     = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');

        $arrKiKd = [];
        foreach (['1', '2'] as $asp) {
            for ($i = 0; $i < 8; $i++) {
                $no  = $i + 1;
                $r   = $this->rapor->getKikdMapel($id_mapel . $id_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                if ($r == null) {
                    $r = $this->rapor->getKikdMapel($id_mapel . $id_kelas . $asp . $no, $tp->id_tp - 1, $smt->id_smt);
                }
                $arrKiKd[$asp][$id_mapel . $id_kelas . $asp . $no] = $r;
            }
        }

        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $nilai = [];
        foreach ($siswas as $siswa) {
            $ns = $this->rapor->getNilaiHarianKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        }

        $data = [
            'user'          => $user,
            'judul'         => 'Nilai Harian Kelas',
            'subjudul'      => 'Input Nilai Harian Mapel',
            'setting'       => $this->dashboard->getSetting(),
            'tp'            => $this->dashboard->getTahun(),
            'tp_active'     => $tp,
            'smt'           => $this->dashboard->getSemester(),
            'smt_active'    => $smt,
            'guru'          => $guru,
            'mapel'         => $mapel,
            'kelas'         => $kelas,
            'siswa'         => $siswas,
            'nilai'         => $nilai,
            'kkm'           => $kkm,
            'kikd'          => $arrKiKd,
            'setting_rapor' => $setting,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/harian');
        $this->load->view('members/guru/templates/footer');
    }

    public function downloadNilaiHarian($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiHarianKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($siswas as $ind => $siswa) {
            $siswa->no       = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            foreach (['p1', 'p2', 'p3', 'p4', 'p5', 'p6', 'p7', 'p8', 'k1', 'k2', 'k3', 'k4', 'k5', 'k6', 'k7', 'k8'] as $col) {
                $siswa->$col = isset($nilais[$siswa->id_siswa]) ? ($nilais[$siswa->id_siswa]->$col ?? '') : '';
            }
        }

        $kikds = $this->rapor->getKikdMapelKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);
        foreach ($kikds as $ki) {
            $nn = substr($ki->id_kikd, -1);
            if ($ki->aspek == 1) {
                $ki->nop   = $nn;
                $ki->kodep = 'P' . $nn;
                $ki->p     = $ki->materi_kikd;
            } else {
                $ki->nok   = $nn;
                $ki->kodek = 'K' . $nn;
                $ki->k     = $ki->materi_kikd;
            }
        }

        if (count($kikds) == 0) {
            $kikds[] = ['nok' => 1, 'kodek' => 'K1', 'k' => 'Praktik/Portofolio/Proyek yang dinilai (lihat tabel KATA KERJA sebelah kanan)', 'nop' => 1, 'kodep' => 'P1', 'p' => 'Materi yang dinilai (lihat tabel KATA KERJA sebelah kanan)'];
        }

        $this->output_json(['siswa' => $siswas, 'kikd' => $kikds]);
    }

    public function uploadNilaiHarian()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa  = $this->input->post('siswa');
        $p_kikd   = $this->input->post('kikd');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_harian'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa']        = $siswa['id'];
            $siswa['id_mapel']        = $id_mapel;
            $siswa['id_kelas']        = $id_kelas;
            $siswa['id_tp']           = $tp->id_tp;
            $siswa['id_smt']          = $smt->id_smt;
            unset($siswa['id'], $siswa['nisn'], $siswa['namasiswa']);
            $datas[] = $siswa;
        }

        $kikdp = [];
        $kikdk = [];
        foreach ($p_kikd as $kikd) {
            $kikdp[] = ['id_kikd' => $id_mapel . $id_kelas . '1' . $kikd['no'], 'id_mapel_kelas' => $id_mapel . $id_kelas, 'aspek' => 1, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $kikd['materipengetahuanyangdinilai'] != null ? strip_tags($kikd['materipengetahuanyangdinilai'] ?? '') : ''];
            $kikdk[] = ['id_kikd' => $id_mapel . $id_kelas . '2' . $kikd['no'], 'id_mapel_kelas' => $id_mapel . $id_kelas, 'aspek' => 2, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'materi_kikd' => $kikd['materiketerampilanyangdinilai'] != null ? strip_tags($kikd['materiketerampilanyangdinilai'] ?? '') : ''];
        }

        $updated = 0;
        $this->db->trans_start();
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_nilai_harian', $data)) $updated++;
        }
        foreach (array_merge($kikdp, $kikdk) as $kd) {
            if ($kd != null) $this->db->replace('rapor_kikd', $kd);
        }
        $this->db->trans_complete();

        $this->output_json($updated);
    }

    public function importHarian()
    {
        $posts   = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ((array) $posts as $data) {
            if ($this->db->replace('rapor_nilai_harian', $data)) $updated++;
        }
        $this->db->trans_complete();
        $this->output_json(['updated' => $updated]);
    }

    public function inputPts($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels     = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));

        $mapel = '';
        $kelas = [];
        foreach ($mapels as $m) {
            if ($m->id_mapel === $id_mapel) {
                $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            }
            foreach ($m->kelas_mapel as $kls) {
                if ($kls->kelas === $id_kelas) {
                    $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                }
            }
        }

        $siswas  = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm     = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');

        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $nilai = [];
        foreach ($siswas as $siswa) {
            $ns = $this->rapor->getNilaiPtsKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        }

        $data = [
            'user'          => $user,
            'judul'         => 'Nilai PTS Kelas',
            'subjudul'      => 'Input Nilai PTS Mapel',
            'setting'       => $this->dashboard->getSetting(),
            'tp'            => $this->dashboard->getTahun(),
            'tp_active'     => $tp,
            'smt'           => $this->dashboard->getSemester(),
            'smt_active'    => $smt,
            'guru'          => $guru,
            'mapel'         => $mapel,
            'kelas'         => $kelas,
            'siswa'         => $siswas,
            'nilai'         => $nilai,
            'kkm'           => $kkm,
            'setting_rapor' => $setting,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/pts');
        $this->load->view('members/guru/templates/footer');
    }

    public function downloadTemplatePts($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiPtsKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($siswas as $ind => $siswa) {
            $siswa->no       = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai    = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->nilai : '';
            $siswa->predikat = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->predikat : '';
        }

        $this->output_json(['siswa' => $siswas]);
    }

    public function uploadNilaiPts()
    {
        $p_siswa  = $this->input->post('siswa');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $this->load->model('Dashboard_model', 'dashboard');
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_pts'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa']     = $siswa['id'];
            $siswa['id_mapel']     = $id_mapel;
            $siswa['id_kelas']     = $id_kelas;
            $siswa['id_tp']        = $tp->id_tp;
            $siswa['id_smt']       = $smt->id_smt;
            unset($siswa['id'], $siswa['nisn'], $siswa['namasiswa']);
            $datas[] = $siswa;
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_nilai_pts', $data)) $updated++;
        }
        $this->output_json($updated);
    }

    public function importPts()
    {
        $inputs  = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ($inputs as $data) {
            if ($this->db->replace('rapor_nilai_pts', $data)) $updated++;
        }
        $this->db->trans_complete();
        echo json_encode($updated);
    }

    public function inputPas($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapels     = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));

        $mapel = '';
        $kelas = [];
        foreach ($mapels as $m) {
            if ($m->id_mapel === $id_mapel) {
                $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            }
            foreach ($m->kelas_mapel as $kls) {
                if ($kls->kelas === $id_kelas) {
                    $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                }
            }
        }

        $siswas  = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm     = $this->rapor->getKkm($id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');

        $dummyNilai = ['nhar' => '', 'npts' => '', 'npas' => ''];
        $nilai = [];
        foreach ($siswas as $siswa) {
            $ns = $this->rapor->getNilaiAkhirKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        }

        $data = [
            'user'          => $user,
            'judul'         => 'Nilai Akhir Kelas',
            'subjudul'      => 'Input Nilai Akhir Mapel',
            'setting'       => $this->dashboard->getSetting(),
            'tp'            => $this->dashboard->getTahun(),
            'tp_active'     => $tp,
            'smt'           => $this->dashboard->getSemester(),
            'smt_active'    => $smt,
            'guru'          => $guru,
            'mapel'         => $mapel,
            'kelas'         => $kelas,
            'siswa'         => $siswas,
            'nilai'         => $nilai,
            'kkm'           => $kkm,
            'setting_rapor' => $setting,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/pas');
        $this->load->view('members/guru/templates/footer');
    }

    public function downloadTemplatePas($id_mapel, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiAkhirKelas($id_mapel, $id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($siswas as $ind => $siswa) {
            $siswa->no       = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai    = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->npas : '';
        }

        $this->output_json(['siswa' => $siswas]);
    }

    public function uploadNilaiPas()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa  = $this->input->post('siswa');
        $id_mapel = $this->input->post('id_mapel');
        $id_kelas = $this->input->post('id_kelas');
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_akhir'] = $id_mapel . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa']       = $siswa['id'];
            $siswa['id_mapel']       = $id_mapel;
            $siswa['id_kelas']       = $id_kelas;
            $siswa['id_tp']          = $tp->id_tp;
            $siswa['id_smt']         = $smt->id_smt;
            unset($siswa['id'], $siswa['nisn'], $siswa['namasiswa']);
            $datas[] = $siswa;
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_nilai_akhir', $data)) $updated++;
        }
        $this->output_json($updated);
    }

    public function importPas()
    {
        $inputs  = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ($inputs as $data) {
            if ($this->db->replace('rapor_nilai_akhir', $data)) $updated++;
        }
        $this->db->trans_complete();
        echo json_encode($updated);
    }

    public function inputEkstra($id_ekstra, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $ekstra_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $ekstras     = json_decode(json_encode(unserialize($ekstra_guru->ekstra_kelas)));

        $ekstra = '';
        $kelas  = [];
        foreach ($ekstras as $m) {
            if ($m->id_ekstra === $id_ekstra) {
                $ekstra = ['id_ekstra' => $m->id_ekstra, 'nama_ekstra' => $m->nama_ekstra];
            }
            foreach ($m->kelas_ekstra as $kls) {
                if ($kls->kelas === $id_kelas) {
                    $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
                }
            }
        }

        $siswas  = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $kkm     = $this->rapor->getKkm($id_ekstra . $id_kelas . $tp->id_tp . $smt->id_smt . '2');

        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $nilai = [];
        foreach ($siswas as $siswa) {
            $ns = $this->rapor->getNilaiEkstraKelas($id_ekstra, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Nilai Ekstrakurikuler',
            'subjudul'   => 'Input Nilai Ekstra',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'ekstra'     => $ekstra,
            'kelas'      => $kelas,
            'siswa'      => $siswas,
            'nilai'      => $nilai,
            'kkm'        => $kkm,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/nilai/ekstra');
        $this->load->view('members/guru/templates/footer');
    }

    public function downloadTemplateEkstra($id_ekstra, $id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilais = $this->rapor->getAllNilaiEkstraKelas($id_ekstra, $id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($siswas as $ind => $siswa) {
            $siswa->no       = $ind + 1;
            $siswa->no_induk = $siswa->nisn != null ? '\'' . $siswa->nisn : '\'' . $siswa->nis;
            $siswa->nilai    = isset($nilais[$siswa->id_siswa]) ? $nilais[$siswa->id_siswa]->nilai : '';
        }

        $this->output_json(['siswa' => $siswas]);
    }

    public function uploadNilaiEkstra()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $p_siswa  = $this->input->post('siswa');
        $id_ekstra = $this->input->post('id_ekstra');
        $id_kelas = $this->input->post('id_kelas');
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($p_siswa as $siswa) {
            $siswa['id_nilai_ekstra'] = $id_ekstra . $id_kelas . $siswa['id'] . $tp->id_tp . $smt->id_smt;
            $siswa['id_siswa']        = $siswa['id'];
            $siswa['id_ekstra']       = $id_ekstra;
            $siswa['id_kelas']        = $id_kelas;
            $siswa['id_tp']           = $tp->id_tp;
            $siswa['id_smt']          = $smt->id_smt;
            unset($siswa['id'], $siswa['nisn'], $siswa['namasiswa']);
            $datas[] = $siswa;
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_nilai_ekstra', $data)) $updated++;
        }
        echo json_encode($updated);
    }

    public function importEkstra()
    {
        $inputs  = $this->input->post('siswa', true);
        $updated = 0;
        $this->db->trans_start();
        foreach ($inputs as $data) {
            if ($this->db->replace('rapor_nilai_ekstra', $data)) $updated++;
        }
        $this->db->trans_complete();
        echo json_encode($updated);
    }

    public function raporSikap()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);

        $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas)));

        $arrMapel = [];
        $arrKelas = [];
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }

        $dummySikap = [];
        for ($i = 0; $i < 10; $i++) {
            $no = $i + 1;
            $dummySikap[] = ['id_sikap' => 1 . $no, 'jenis' => '1', 'kode' => $no, 'sikap' => ''];
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Input Nilai Sikap',
            'subjudul'   => 'Input Nilai Sikap',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'mapel'      => $arrMapel,
            'kelas'      => $arrKelas,
            'dummy_sikap' => $dummySikap,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/sikap/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function saveSikap()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('sikap', true));
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $update = false;

        foreach ($input as $d) {
            $row = [
                'id_sikap' => $d->id_sikap,
                'id_kelas' => $d->kelas,
                'jenis'    => $d->jenis,
                'kode'     => $d->kode,
                'sikap'    => $d->sikap,
                'id_tp'    => $tp->id_tp,
                'id_smt'   => $smt->id_smt,
            ];
            $update = $this->db->replace('rapor_data_sikap', $row);
        }

        $this->output_json(['status' => $update]);
    }

    public function raporSpiritual()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas    = $this->kelas->get_one($id_kelas, $tp->id_tp, $smt->id_smt);

        $dummySpiritual = [];
        for ($i = 0; $i < 10; $i++) {
            $no = $i + 1;
            $dummySpiritual[] = [
                'id_sikap' => $id_kelas . 1 . $no,
                'jenis'    => '1',
                'kode'     => $no,
                'sikap'    => $this->rapor->getDummyDeskripsiSpiritual()[$i],
            ];
        }

        $data = [
            'user'            => $user,
            'judul'           => 'Nilai Spiritual',
            'subjudul'        => 'Input Nilai Spiritual',
            'setting'         => $this->dashboard->getSetting(),
            'tp'              => $this->dashboard->getTahun(),
            'tp_active'       => $tp,
            'smt'             => $this->dashboard->getSemester(),
            'smt_active'      => $smt,
            'guru'            => $guru,
            'kelas'           => $kelas,
            'dummy_spiritual' => $dummySpiritual,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/sikap/spiritual');
        $this->load->view('members/guru/templates/footer');
    }

    public function importSpiritual($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[11];
            if ($id_siswa != 'id') {
                $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '1', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 1, 'nilai' => serialize(['predikat' => $in[3], 'sl1' => $in[4], 'sl2' => $in[5], 'sl3' => $in[6], 'mb1' => $in[7], 'mb2' => $in[8], 'mb3' => $in[9]]), 'deskripsi' => $in[10], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            }
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_nilai_sikap', $data)) $updated++;
        }
        echo json_encode($updated);
    }

    public function raporSosial()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas    = $this->kelas->get_one($id_kelas, $tp->id_tp, $smt->id_smt);

        $dummySosial = [];
        for ($i = 0; $i < 10; $i++) {
            $no = $i + 1;
            $dummySosial[] = [
                'id_sikap' => $id_kelas . 2 . $no,
                'jenis'    => '2',
                'kode'     => $no,
                'sikap'    => $this->rapor->getDummyDeskripsiSosial()[$i],
            ];
        }

        $data = [
            'user'         => $user,
            'judul'        => 'Nilai Sosial',
            'subjudul'     => 'Input Nilai Sosial',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'guru'         => $guru,
            'kelas'        => $kelas,
            'dummy_sosial' => $dummySosial,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/sikap/sosial');
        $this->load->view('members/guru/templates/footer');
    }

    public function importSosial($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[13];
            if ($id_siswa != 'id') {
                $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '2', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 2, 'nilai' => serialize(['predikat' => $in[3], 'a1' => $in[4], 'a2' => $in[5], 'a3' => $in[6], 'b1' => $in[7], 'b2' => $in[8], 'b3' => $in[9], 'c1' => $in[10], 'c2' => $in[11]]), 'deskripsi' => $in[12], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            }
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_nilai_sikap', $data)) $updated++;
        }
        echo json_encode($updated);
    }

    public function raporPrestasi()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas    = $this->kelas->get_one($id_kelas);

        $dummyRank    = ['1 ~ 3', '4 ~ 10', '11 ~ 15', '16 ~ 20', '21 ~ 25', '26 > >'];
        $dummyKode    = ['1', '4', '11', '16', '21', '26'];
        $dummyDeskSaran = [];
        for ($i = 0; $i < 6; $i++) {
            $no = $i + 1;
            $dummyDeskSaran[] = [
                'id_catatan' => $id_kelas . 1 . $no,
                'jenis'      => '3',
                'kode'       => $dummyKode[$i],
                'deskripsi'  => $this->rapor->getDummyDeskripsiRanking()[$i],
                'rank'       => $dummyRank[$i],
            ];
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Prestasi Siswa',
            'subjudul'   => 'Input Prestasi Siswa',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'kelas'      => $kelas,
            'mapels'     => $this->master->getAllMapel(),
            'dummy'      => $dummyDeskSaran,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/prestasi/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function savePrestasi()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input  = json_decode($this->input->post('catatan', true));
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $update = false;

        foreach ($input as $d) {
            $row = [
                'id_catatan' => $d->id_catatan,
                'id_kelas'   => $d->kelas,
                'jenis'      => $d->jenis,
                'kode'       => $d->kode,
                'rank'       => $d->rank,
                'deskripsi'  => $d->deskripsi,
                'id_tp'      => $tp->id_tp,
                'id_smt'     => $smt->id_smt,
            ];
            $update = $this->db->replace('rapor_data_catatan', $row);
        }

        $this->output_json(['status' => $update]);
    }

    public function importPrestasi($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[12];
            $datas[]  = ['id_ranking' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'ranking' => $in[4], 'deskripsi' => $in[5], 'p1' => $in[6], 'p1_desk' => $in[7], 'p2' => $in[8], 'p2_desk' => $in[9], 'p3' => $in[10], 'p3_desk' => $in[11]];
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_prestasi', $data)) $updated++;
        }
        echo json_encode($updated);
    }

    public function raporCatatan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas    = $this->kelas->get_one($id_kelas);

        $dummyRank       = ['1 ~ 3', '4 ~ 10', '11 ~ 15', '16 > >'];
        $dummyKode       = ['1', '4', '11', '16'];
        $dummyDeskAbsensi = [];
        for ($i = 0; $i < 4; $i++) {
            $no = $i + 1;
            $dummyDeskAbsensi[] = [
                'id_catatan' => $id_kelas . 1 . $no,
                'jenis'      => '1',
                'kode'       => $dummyKode[$i],
                'deskripsi'  => $this->rapor->getDummyDeskripsiAbsensi()[$i],
                'rank'       => $dummyRank[$i],
            ];
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Catatan Wali Kelas',
            'subjudul'   => 'Input Catatan Wali Kelas',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'kelas'      => $kelas,
            'dummy'      => $dummyDeskAbsensi,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/catatan/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function saveCatatan()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input  = json_decode($this->input->post('catatan', true));
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $update = false;

        foreach ($input as $d) {
            $row = [
                'id_catatan' => $d->id_catatan,
                'id_kelas'   => $d->kelas,
                'jenis'      => $d->jenis,
                'kode'       => $d->kode,
                'rank'       => $d->rank,
                'deskripsi'  => $d->deskripsi,
                'id_tp'      => $tp->id_tp,
                'id_smt'     => $smt->id_smt,
            ];
            $update = $this->db->replace('rapor_data_catatan', $row);
        }

        $this->output_json(['status' => $update]);
    }

    public function importCatatan($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[10];
            if ($id_siswa != 'id') {
                $datas[] = ['id_catatan_wali' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'nilai' => serialize(['op1' => $in[3], 'op2' => $in[4], 'op3' => $in[5], 's' => $in[6], 'i' => $in[7], 'a' => $in[8]]), 'deskripsi' => $in[9], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
            }
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_catatan_wali', $data)) $updated++;
        }
        echo json_encode($updated);
    }

    public function raporFisik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas    = $this->kelas->get_one($id_kelas);

        $jenis        = ['1', '2', '3', '4'];
        $dummyDeskFisik = [];
        for ($i = 0; $i < 4; $i++) {
            $no = $i + 1;
            foreach ($jenis as $jns) {
                $dummyDeskFisik[] = [
                    'id_fisik'  => $id_kelas . $jns . $no,
                    'jenis'     => $jns,
                    'kode'      => $no,
                    'deskripsi' => $this->rapor->getDummyDeskripsiFisik($jns)[$i],
                ];
            }
        }

        $data = [
            'user'       => $user,
            'judul'      => 'Data Fisik Siswa',
            'subjudul'   => 'Input Data Fisik Siswa',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'kelas'      => $kelas,
            'dummy'      => $dummyDeskFisik,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/fisik/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function saveFisik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $kelas  = $this->input->post('kelas', true);
        $input  = json_decode($this->input->post('fisik', true));
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $update = false;

        foreach ($input as $d) {
            $row = [
                'id_fisik'  => $kelas . $d->jenis . $d->kode,
                'id_kelas'  => $kelas,
                'jenis'     => $d->jenis,
                'kode'      => $d->kode,
                'deskripsi' => $d->deskripsi,
                'id_tp'     => $tp->id_tp,
                'id_smt'    => $smt->id_smt,
            ];
            $update = $this->db->replace('rapor_data_fisik', $row);
        }

        $this->output_json(['status' => $update]);
    }

    public function importFisik($id_kelas)
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input = json_decode($this->input->post('nilai', true));
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        $datas = [];
        foreach ($input as $in) {
            $id_siswa = $in[11];
            $tinggi   = $smt->id_smt == 1 ? $in[3] : $in[4];
            $berat    = $smt->id_smt == 1 ? $in[5] : $in[6];
            if ($id_siswa != 'id') {
                $datas[] = ['id_fisik' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_kelas' => $id_kelas, 'id_siswa' => $id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'tinggi' => $tinggi, 'berat' => $berat, 'kondisi' => serialize(['telinga' => $in[7], 'mata' => $in[8], 'gigi' => $in[9], 'lain' => $in[10]])];
            }
        }

        $updated = 0;
        foreach ($datas as $data) {
            if ($this->db->replace('rapor_fisik', $data)) $updated++;
        }
        echo json_encode($updated);
    }

    public function raporNaik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelas    = $this->kelas->get_one($id_kelas);
        $siswas   = $this->rapor->getKenaikanSiswa($id_kelas, $tp->id_tp, $smt->id_smt);

        $data = [
            'user'       => $user,
            'judul'      => 'Kenaikan Kelas',
            'subjudul'   => 'Siswa Kelas',
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'guru'       => $guru,
            'kelas'      => $kelas,
            'siswas'     => $siswas,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/kenaikan/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function saveNaik()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $input   = json_decode($this->input->post('naik', true));
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();
        $updated = 0;

        foreach ($input as $d) {
            $row = [
                'id_naik'  => $d->id_siswa . $tp->id_tp . $smt->id_smt,
                'id_siswa' => $d->id_siswa,
                'id_tp'    => $tp->id_tp,
                'id_smt'   => $smt->id_smt,
                'naik'     => $d->naik,
            ];
            if ($this->db->replace('rapor_naik', $row)) $updated++;
        }
        echo json_encode($updated);
    }

    public function cetakPts()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $guru        = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas    = $guru->wali_kelas;
        $kelas       = $this->kelas->get_one($id_kelas);
        $siswas      = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $jurusan     = $this->kelas->getJurusanById($kelas->jurusan_id);
        $kelompoks   = $this->master->getKodeKelompokMapel();
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $settingRapor   = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);

        $arrk = [];
        foreach ($kategori_mapel as $km) {
            if (!in_array($km->kode_kel_mapel, $arrk)) {
                $arrk[] = $km->kode_kel_mapel;
            }
        }

        $mapels = $this->master->getAllMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);

        $arr_mapels = [];
        $arr_siswas = [];
        $kkm        = [];

        foreach ($mapels as $mapel) {
            $arr_mapels[] = $mapel->id_mapel;
        }

        foreach ($siswas as $siswa) {
            $arr_siswas[] = $siswa->id_siswa;
            foreach ($mapels as $mapel) {
                if (isset($settingRapor) && $settingRapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $settingRapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
            }
        }

        $data = [
            'user'         => $user,
            'judul'        => 'Rapor PTS',
            'subjudul'     => 'Cetak Rapor PTS',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'guru'         => $guru,
            'siswas'       => $siswas,
            'kelas'        => $kelas->nama_kelas,
            'mapels'       => $mapels,
            'kelompoks'    => $kelompoks,
            'nilai_pts'    => $this->rapor->getArrNilaiMapelPtsSiswa($arr_mapels, $arr_siswas, $tp->id_tp, $smt->id_smt),
            'nilai_harian' => $this->rapor->getArrNilaiMapelHarianSiswa($arr_mapels, $arr_siswas, $tp->id_tp, $smt->id_smt),
            'kkm'          => $kkm,
            'rapor'        => $settingRapor,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/cetak/pts');
        $this->load->view('members/guru/templates/footer');
    }

    public function cetakAkhir()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user = $this->ion_auth->user()->row();
        $tp   = $this->dashboard->getTahunActive();
        $smt  = $this->dashboard->getSemesterActive();

        $guru        = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas    = $guru->wali_kelas;
        $kelas       = $this->kelas->get_one($id_kelas);
        $jurusan     = $this->kelas->getJurusanById($kelas->jurusan_id);
        $kelompoks   = $this->master->getKodeKelompokMapel();
        $siswas      = $this->rapor->getDetailSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $kategori_mapel = $this->master->getKategoriKelompokMapel();
        $settingRapor   = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);

        $arrk = [];
        foreach ($kategori_mapel as $km) {
            if (!in_array($km->kode_kel_mapel, $arrk)) {
                $arrk[] = $km->kode_kel_mapel;
            }
        }

        $mapels  = $this->master->getAllMapel(empty($arrk) ? null : $arrk, isset($jurusan->mapel_peminatan) ? $jurusan->mapel_peminatan : null);
        $ekstras = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $other   = $smt->id_smt === '1' ? '2' : '1';

        $nilai_sikap  = $this->rapor->getNilaiSikapByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai_rapor  = $this->rapor->getNilaiRaporByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $prestasis    = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans     = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }

        $kkm         = [];
        $sikap       = [];
        $nilai       = [];
        $fisik       = [];
        $desks       = [];
        $absensi     = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];

        $dummySikap  = ['predikat' => ''];
        $dummyNilai  = ['p_deskripsi' => '', 'k_rata_rata' => '', 'k_deskripsi' => '', 'k_predikat' => '', 'nilai' => '', 'predikat' => ''];
        $dummyDesks  = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => ''];
        $dummyAbsen  = ['s' => ' - ', 'i' => ' - ', 'a' => ' - ', 'saran' => ''];
        $dummyFisik  = ['kondisi' => ['telinga' => '', 'mata' => '', 'gigi' => '', 'lain' => ''], 'smt' . $smt->id_smt => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp], 'smt' . $other => ['tinggi' => '', 'berat' => '', 'tp' => $tp->id_tp]];
        $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];

        foreach ($siswas as $siswa) {
            $id_siswa = $siswa->id_siswa;

            $ns1 = isset($nilai_sikap[$id_siswa . '1']) ? $nilai_sikap[$id_siswa . '1'] : null;
            $ns2 = isset($nilai_sikap[$id_siswa . '2']) ? $nilai_sikap[$id_siswa . '2'] : null;
            $sikap[$id_siswa][1] = ['deskripsi' => $ns1 ? $ns1->deskripsi : '', 'predikat' => $ns1 ? unserialize($ns1->nilai) : $dummySikap];
            $sikap[$id_siswa][2] = ['deskripsi' => $ns2 ? $ns2->deskripsi : '', 'predikat' => $ns2 ? unserialize($ns2->nilai) : $dummySikap];

            foreach ($mapels as $mapel) {
                $key_mapel = array_search($mapel->id_mapel . $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, array_column($nilai_rapor, 'id_nilai_harian'));
                $nilai[$id_siswa][$mapel->id_mapel] = $key_mapel !== false ? $nilai_rapor[$key_mapel] : $dummyNilai;

                if (isset($settingRapor->kkm_tunggal) && $settingRapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $settingRapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
            }

            $desks[$id_siswa]   = isset($prestasis[$id_siswa]) ? $prestasis[$id_siswa] : $dummyDesks;
            $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa] : ['nilai' => $dummyAbsen];

            $nf  = $this->rapor->getFisikKelas($id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
            $nf2 = $this->rapor->getFisikKelas($id_kelas, $id_siswa, $tp->id_tp, $other);
            $fisik[$id_siswa] = $nf != null
                ? ['kondisi' => unserialize($nf->kondisi), 'smt' . $nf->id_smt => ['tinggi' => $nf->tinggi, 'berat' => $nf->berat], 'smt' . $other => ['tinggi' => $nf2 != null ? $nf2->tinggi : '', 'berat' => $nf2 != null ? $nf2->berat : '']]
                : $dummyFisik;

            foreach ($ekstras as $ext) {
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    if ($id_ekstra != null) {
                        $mapelEkstra[$id_ekstra]         = $this->kelas->getEkskulById($id_ekstra);
                        $ne                              = $this->rapor->getEkstraKelas($id_ekstra, $id_siswa, $tp->id_tp, $smt->id_smt);
                        $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? $dummyEkstra : $ne;
                    }
                }
            }
        }

        $data = [
            'user'         => $user,
            'judul'        => 'Rapor Akhir',
            'subjudul'     => 'Cetak Rapor Akhir',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'guru'         => $guru,
            'siswas'       => $siswas,
            'kelas'        => $kelas,
            'mapels'       => $mapels,
            'kelompoks'    => $kelompoks,
            'ekstras'      => $ekstras,
            'nilai'        => $nilai,
            'sikap'        => $sikap,
            'fisik'        => $fisik,
            'deskripsi'    => $desks,
            'absensi'      => $absensi,
            'nilai_ekstra' => $nilaiEkstra,
            'mapel_ekstra' => $mapelEkstra,
            'kkm'          => $kkm,
            'rapor'        => $settingRapor,
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/cetak/akhir');
        $this->load->view('members/guru/templates/footer');
    }

    private function _buildLegerData($id_kelas, $tp, $smt, $siswas, $mapels, $ekstras, $catatans, $setting_rapor)
    {
        $kkm         = [];
        $sikap       = [];
        $nilai       = [];
        $nilaiPts    = [];
        $desks       = [];
        $absensi     = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];

        $dummySikap  = ['predikat' => ''];
        $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];

        foreach ($siswas as $siswa) {
            $id_siswa = $siswa->id_siswa;

            $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt, '1');
            $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt, '2');
            $sikap[$id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
            $sikap[$id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];

            $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
            $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa]->nilai : $dummyAbsen;

            foreach ($mapels as $mapel) {
                $dummyNilai = ['k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
                $nr  = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
                $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);
                $nilai[$id_siswa][$mapel->id_mapel]    = $nr == null ? $dummyNilai : $nr;
                $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;

                if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $setting_rapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
            }

            foreach ($ekstras as $ext) {
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    if ($id_ekstra != null) {
                        $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                        $ne = $this->rapor->getEkstraKelas($id_ekstra, $id_siswa, $tp->id_tp, $smt->id_smt);
                        $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                    }
                }
            }
        }

        return compact('kkm', 'sikap', 'nilai', 'nilaiPts', 'desks', 'absensi', 'mapelEkstra', 'nilaiEkstra');
    }

    public function cetakLeger()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelases  = $this->kelas->get_one($id_kelas);
        $siswas   = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $mapels   = $this->master->getAllMapel();
        $ekstras  = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }

        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $built = $this->_buildLegerData($id_kelas, $tp, $smt, $siswas, $mapels, $ekstras, $catatans, $setting_rapor);

        $data = [
            'user'         => $user,
            'judul'        => 'Leger Kelas',
            'subjudul'     => 'Cetak Leger Kelas',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'guru'         => $guru,
            'kelases'      => $kelases,
            'mapels'       => $mapels,
            'siswas'       => $siswas,
            'ekstras'      => $ekstras,
            'nilai'        => (array) json_decode(json_encode($built['nilai'])),
            'nilai_pts'    => (array) json_decode(json_encode($built['nilaiPts'])),
            'sikap'        => $built['sikap'],
            'deskripsi'    => $built['desks'],
            'absensi'      => $built['absensi'],
            'nilai_ekstra' => $built['nilaiEkstra'],
            'mapel_ekstra' => $built['mapelEkstra'],
            'kkm'          => $built['kkm'],
            'rapor'        => $setting_rapor,
            'naik'         => $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt),
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/leger/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function downloadLeger()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $user     = $this->ion_auth->user()->row();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $siswas   = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $mapels   = $this->master->getAllMapel();
        $ekstras  = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }

        $setting_rapor = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $built = $this->_buildLegerData($id_kelas, $tp, $smt, $siswas, $mapels, $ekstras, $catatans, $setting_rapor);

        $this->output_json([
            'siswas'       => $siswas,
            'mapels'       => $mapels,
            'ekstras'      => $ekstras,
            'nilai'        => $built['nilai'],
            'nilai_pts'    => $built['nilaiPts'],
            'sikap'        => $built['sikap'],
            'absensi'      => $built['absensi'],
            'nilai_ekstra' => $built['nilaiEkstra'],
            'mapel_ekstra' => $built['mapelEkstra'],
            'kkm'          => $built['kkm'],
            'rapor'        => $setting_rapor,
        ]);
    }

    public function dkn()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $user     = $this->ion_auth->user()->row();
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();
        $guru     = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $id_kelas = $guru->wali_kelas;
        $kelases  = $this->kelas->get_one($id_kelas);
        $siswas   = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $mapels   = $this->master->getAllMapel();
        $ekstras  = $this->kelas->getKelasEkskul($id_kelas, $tp->id_tp, $smt->id_smt);
        $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);

        foreach ($catatans as $catatan) {
            $catatan->nilai = unserialize($catatan->nilai);
        }

        $setting_rapor  = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        $dummySikap     = ['predikat' => ''];
        $dummyEkstra    = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
        $dummyDesks     = ['ranking' => '', 'rank_deskripsi' => '', 'p1' => '', 'p1_desk' => '', 'p2' => '', 'p2_desk' => '', 'p3' => '', 'p3_desk' => '', 'saran' => ''];
        $dummyAbsen     = ['s' => '', 'i' => '', 'a' => ''];

        $kkm         = [];
        $sikap       = [];
        $nilai       = [];
        $nilaiPts    = [];
        $desks       = [];
        $absensi     = [];
        $mapelEkstra = [];
        $nilaiEkstra = [];

        foreach ($siswas as $siswa) {
            $id_siswa = $siswa->id_siswa;

            $ns1 = $this->rapor->getNilaiSikapKelas($id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt, '1');
            $ns2 = $this->rapor->getNilaiSikapKelas($id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt, '2');
            $sikap[$id_siswa][1] = ['deskripsi' => $ns1 == null ? '' : $ns1->deskripsi, 'predikat' => $ns1 == null ? $dummySikap : unserialize($ns1->nilai)];
            $sikap[$id_siswa][2] = ['deskripsi' => $ns2 == null ? '' : $ns2->deskripsi, 'predikat' => $ns2 == null ? $dummySikap : unserialize($ns2->nilai)];

            foreach ($mapels as $mapel) {
                $dummyNilai = ['mapel' => $mapel->nama_mapel, 'k_rata_rata' => '', 'k_predikat' => '', 'p_rata_rata' => '', 'nilai_pas' => '', 'nilai' => '', 'predikat' => ''];
                $nr  = $this->rapor->getNilaiRapor($mapel->id_mapel, $id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
                $pts = $this->rapor->getNilaiMapelPtsSiswa($mapel->id_mapel, $id_siswa, $tp->id_tp, $smt->id_smt);

                if ($nr != null) $nr['mapel'] = $mapel->nama_mapel;
                $nilai[$id_siswa][$mapel->id_mapel]    = $nr == null ? $dummyNilai : $nr;
                $nilaiPts[$id_siswa][$mapel->id_mapel] = $pts == null ? 0 : $pts->nilai;

                if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
                    $kkm[$mapel->id_mapel] = $setting_rapor;
                } else {
                    $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
                }
            }

            $nd = $this->rapor->getRaporDeskripsi($id_kelas, $id_siswa, $tp->id_tp, $smt->id_smt);
            $desks[$id_siswa]   = $nd == null ? json_decode(json_encode($dummyDesks)) : $nd;
            $absensi[$id_siswa] = $nd == null ? $dummyAbsen : unserialize($nd->nilai);

            foreach ($ekstras as $ext) {
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    if ($id_ekstra != null) {
                        $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                        $ne = $this->rapor->getEkstraKelas($id_ekstra, $id_siswa, $tp->id_tp, $smt->id_smt);
                        $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                    }
                }
            }
        }

        $data = [
            'user'         => $user,
            'judul'        => 'Daftar Kumpulan Nilai Kelas',
            'subjudul'     => 'Cetak DKN',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'guru'         => $guru,
            'kelases'      => $kelases,
            'mapels'       => $mapels,
            'siswas'       => $siswas,
            'ekstras'      => $ekstras,
            'nilai'        => $nilai,
            'nilai_pts'    => $nilaiPts,
            'sikap'        => $sikap,
            'deskripsi'    => $desks,
            'absensi'      => $absensi,
            'nilai_ekstra' => $nilaiEkstra,
            'mapel_ekstra' => $mapelEkstra,
            'kkm'          => $kkm,
            'rapor'        => $setting_rapor,
            'naik'         => $this->rapor->getKenaikanRapor($id_kelas, $tp->id_tp, $smt->id_smt),
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('members/guru/rapor/dkn/data');
        $this->load->view('members/guru/templates/footer');
    }
}
