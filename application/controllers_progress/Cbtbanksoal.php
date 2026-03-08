<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbtbanksoal extends CI_Controller
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

        $this->load->library('upload');
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
        $this->load->model('Master_model',   'master');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',      'cbt');

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->master->getTahunActive();
        $smt     = $this->master->getSemesterActive();
        $type    = $this->input->get('type');
        $mode    = $this->input->get('mode') ?? '1';

        $data = [
            'user'      => $user,
            'judul'     => 'Bank Soal',
            'subjudul'  => 'Soal',
            'setting'   => $setting,
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'levels'    => $this->dropdown->getAllLevel($setting->jenjang),
            'mapels'    => $this->dropdown->getAllMapel(),
            'mode'      => $mode,
        ];

        if ($this->ion_auth->is_admin()) {
            $banks = $type !== null ? $this->cbt->getDataBank(null, $type) : [];

            $data['profile']   = $this->dashboard->getProfileAdmin($user->id);
            $data['gurus']     = $this->dropdown->getAllGuru();
            $data['kelas']     = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
            $data['filters']   = ['0' => 'Semua', '1' => 'Guru', '2' => 'Mapel', '3' => 'Level'];
            $data['id_filter'] = $type ?? '';
            $data['id_guru']   = null;
            $data['id_mapel']  = null;
            $data['id_level']  = null;
            $data['banks']     = $banks;
            $data['total_siswa'] = $this->getBankTerpakai($banks);

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/banksoal/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru  = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $banks = $this->cbt->getDataBank($guru->id_guru);

            $data['guru']      = $guru;
            $data['gurus']     = [$guru->id_guru => $guru->nama_guru];
            $data['kelas']     = $this->cbt->getKelas($tp->id_tp, $smt->id_smt);
            $data['filters']   = ['0' => 'Semua', '2' => 'Mapel', '3' => 'Level'];
            $data['id_filter'] = $type ?? '';
            $data['id_guru']   = $guru->id_guru;
            $data['id_mapel']  = '';
            $data['id_level']  = '';
            $data['banks']     = $banks;
            $data['total_siswa'] = $this->getBankTerpakai($banks);

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/banksoal/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    private function getBankTerpakai(array $banks): array
    {
        $ids = [];
        foreach ($banks as $bank) {
            foreach ($bank as $tp_group) {
                foreach ($tp_group as $smt_group) {
                    $ids[] = $smt_group->id_bank;
                }
            }
        }

        $jadwal_terpakai = [];
        if (!empty($ids)) {
            foreach ($this->cbt->getBankTerpakai($ids) as $idj => $rows) {
                if ($rows) {
                    $jadwal_terpakai[$idj] = count($rows);
                }
            }
        }
        return $jadwal_terpakai;
    }

    public function data($guru = null)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getDataBank($guru), false);
    }

    public function dataTable($guru = null)
    {
        $this->load->model('Cbt_model', 'cbt');
        $this->output_json($this->cbt->getDataTableBank($guru), false);
    }

    public function getMapelGuru()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model',  'kelas');

        $id_guru    = $this->input->get('id_guru', true);
        $tp         = $this->master->getTahunActive();
        $smt        = $this->master->getSemesterActive();
        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel   = [];

        if ($mapel !== null) {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
            }
        }

        $this->output_json($arrMapel);
    }

    public function getGuruMapel()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model',  'kelas');

        $id_mapel   = $this->input->get('id_mapel', true);
        $tp         = $this->master->getTahunActive();
        $smt        = $this->master->getSemesterActive();
        $mapel_guru = $this->kelas->getMapelGuruKelas($tp->id_tp, $smt->id_smt);
        $arrGuru    = [];

        foreach ($mapel_guru as $guru) {
            $mapel = json_decode(json_encode(unserialize($guru->mapel_kelas ?? '')));
            if ($mapel === null) continue;
            foreach ($mapel as $m) {
                if (isset($m->id_mapel) && $m->id_mapel == $id_mapel) {
                    $arrGuru[$guru->id_guru] = $guru->nama_guru;
                }
            }
        }

        $this->output_json($arrGuru);
    }

    public function getKelasLevel()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Kelas_model',  'kelas');
        $this->load->model('Cbt_model',    'cbt');

        $level    = $this->input->get('level',   true);
        $id_guru  = $this->input->get('id_guru', true);
        $id_mapel = $this->input->get('mapel',   true);
        $tp       = $this->master->getTahunActive();
        $smt      = $this->master->getSemesterActive();

        $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);
        $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
        $arrMapel   = [];
        $arrKelas   = [];

        if ($mapel !== false && $mapel !== null) {
            foreach ($mapel as $m) {
                $arrMapel[$m->id_mapel] = $m->nama_mapel;
                if ($id_mapel === $m->id_mapel) {
                    foreach ($m->kelas_mapel as $kls) {
                        $arrKelas[] = $kls->kelas;
                    }
                }
            }
        }

        $this->output_json([
            'mapel' => $arrMapel,
            'kelas' => !empty($arrKelas) ? $this->cbt->getKelasByLevel($level, $arrKelas) : [],
        ]);
    }

    public function addBank()
    {
        $this->load->model('Dropdown_model',  'dropdown');
        $this->load->model('Master_model',    'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model',     'kelas');
        $this->load->model('Cbt_model',       'cbt');

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->master->getTahunActive();
        $smt     = $this->master->getSemesterActive();

        $data = [
            'user'        => $user,
            'judul'       => 'Bank Soal',
            'subjudul'    => 'Buat Bank Soal',
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'setting'     => $setting,
            'bank'        => json_decode(json_encode($this->cbt->dummy($setting->jenjang))),
            'jenis'       => $this->cbt->getAllJenisUjian(),
            'jurusan'     => $this->cbt->getAllJurusan(),
            'level'       => $this->dropdown->getAllLevel($setting->jenjang),
            'mapel_agama' => $this->master->getAgamaSiswa(),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['kelas']   = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
            $data['id_guru'] = '';
            $data['gurus']   = $this->dropdown->getAllGuru();
            $data['mapel']   = $this->dropdown->getAllMapel();

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/banksoal/add');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
            $arrMapel   = [];
            $arrKelas   = [];

            if ($mapel !== false && $mapel !== null) {
                foreach ($mapel as $m) {
                    $arrMapel[$m->id_mapel] = $m->nama_mapel;
                    foreach ($m->kelas_mapel as $kls) {
                        $arrKelas[$m->id_mapel][] = [
                            'id_kelas'   => $kls->kelas,
                            'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas),
                        ];
                    }
                }
            }

            $arrId = [];
            if (!empty($mapel)) {
                foreach ($mapel[0]->kelas_mapel as $id_mapel) {
                    $arrId[] = $id_mapel->kelas;
                }
            }

            $data['gurus']     = [$guru->id_guru => $guru->nama_guru];
            $data['guru']      = $guru;
            $data['id_guru']   = $guru->id_guru;
            $data['mapel_guru'] = $mapel_guru;
            $data['mapel']     = $arrMapel;
            $data['arrkelas']  = $arrKelas;
            $data['kelas']     = !empty($arrId)
                ? $this->dropdown->getAllKelasByArrayId($tp->id_tp, $smt->id_smt, $arrId)
                : [];

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/banksoal/add');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function editBank()
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dropdown_model',  'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model',     'kelas');
        $this->load->model('Cbt_model',       'cbt');

        $id_bank = $this->input->get('id_bank', true);
        $id_guru = $this->input->get('id_guru', true);
        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->master->getTahunActive();
        $smt     = $this->master->getSemesterActive();

        $data = [
            'user'        => $user,
            'judul'       => 'Edit Bank Soal',
            'subjudul'    => 'Edit Bank Soal',
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'bulan'       => $this->dropdown->getBulan(),
            'setting'     => $setting,
            'jenis'       => $this->cbt->getAllJenisUjian(),
            'jurusan'     => $this->cbt->getAllJurusan(),
            'level'       => $this->dropdown->getAllLevel($setting->jenjang),
            'kelas'       => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'bank'        => $this->cbt->getDataBankById($id_bank),
            'mapel_agama' => $this->master->getAgamaSiswa(),
        ];

        if ($this->ion_auth->is_admin()) {
            $mapel_guru = $this->kelas->getGuruMapelKelas($id_guru, $tp->id_tp, $smt->id_smt);

            $data['profile']    = $this->dashboard->getProfileAdmin($user->id);
            $data['id_guru']    = $id_guru;
            $data['gurus']      = $this->dropdown->getAllGuru();
            $data['mapel']      = $this->dropdown->getAllMapel();
            $data['mapel_guru'] = $mapel_guru;

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/banksoal/add');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $mapel_guru = $this->kelas->getGuruMapelKelas($guru->id_guru, $tp->id_tp, $smt->id_smt);
            $mapel      = json_decode(json_encode(unserialize($mapel_guru->mapel_kelas ?? '')));
            $arrMapel   = [];

            if ($mapel !== false && $mapel !== null) {
                foreach ($mapel as $m) {
                    $arrMapel[$m->id_mapel] = $m->nama_mapel;
                }
            }

            $data['gurus']      = [$guru->id_guru => $guru->nama_guru];
            $data['mapel_guru'] = $mapel_guru;
            $data['guru']       = $guru;
            $data['id_guru']    = $guru->id_guru;
            $data['mapel']      = $arrMapel;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/banksoal/add');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function saveBank()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Log_model',    'logging');
        $this->load->model('Cbt_model',    'cbt');

        $status = false;
        if ($this->input->post()) {
            $tp  = $this->master->getTahunActive();
            $smt = $this->master->getSemesterActive();
            $this->cbt->saveBankSoal($tp->id_tp, $smt->id_smt);
            $status = true;
        }

        $id = $this->input->post('id_bank', true);
        if ($id) {
            $this->logging->saveLog(4, 'mengedit bank soal');
        }

        $this->output_json(['status' => $status]);
    }

    public function deleteBank()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Log_model',    'logging');
        $this->load->model('Cbt_model',    'cbt');

        $id = $this->input->get('id_bank', true);

        if ($this->cbt->cekJadwalBankSoal($id) > 0) {
            $this->output_json(['status' => false, 'message' => 'Ada jadwal ujian yang menggunakan bank soal ini']);
            return;
        }

        $this->master->delete('cbt_soal',      $id, 'bank_id');
        $this->master->delete('cbt_bank_soal', $id, 'id_bank');
        $this->logging->saveLog(5, 'menghapus bank soal');
        $this->output_json(['status' => true, 'message' => 'berhasil']);
    }

    public function deleteAllBank()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Log_model',    'logging');
        $this->load->model('Cbt_model',    'cbt');

        $ids = json_decode($this->input->post('ids', true));

        if ($this->cbt->cekJadwalBankSoal($ids) > 0) {
            $this->output_json(['status' => false, 'message' => 'Ada jadwal ujian yang menggunakan bank soal ini']);
            return;
        }

        $this->master->delete('cbt_soal',      $ids, 'bank_id');
        $this->master->delete('cbt_bank_soal', $ids, 'id_bank');
        $this->logging->saveLog(5, 'menghapus bank soal');
        $this->output_json(['status' => true, 'message' => 'berhasil']);
    }

    public function detail($id)
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $user    = $this->ion_auth->user()->row();
        $tp      = $this->master->getTahunActive();
        $smt     = $this->master->getSemesterActive();
        $terpakai = $this->cbt->getBankTerpakai([$id]);

        $data = [
            'user'        => $user,
            'judul'       => 'Detail Soal',
            'subjudul'    => 'Detail Soal',
            'setting'     => $this->dashboard->getSetting(),
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'bank'        => $this->cbt->getDataBankById($id),
            'soals'       => $this->cbt->getAllSoalByBank($id),
            'kelas'       => $this->cbt->getKelas($tp->id_tp, $smt->id_smt),
            'total_siswa' => isset($terpakai[$id]) ? count($terpakai[$id]) : 0,
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/banksoal/detail');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/banksoal/detail');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function saveSelected()
    {
        $this->load->model('Cbt_model', 'cbt');

        $bank_id = $this->input->post('id_bank', true);
        $jml     = $this->input->post('soal',    true);
        $soal    = $jml !== null ? count($jml) : 0;
        $unchek  = json_decode($this->input->post('uncheck', true));
        $arrId   = [];

        for ($i = 0; $i <= $soal; $i++) {
            $id = $this->input->post('soal[' . $i . ']', true);
            if ($id !== null) {
                $arrId[] = $id;
            }
        }

        $updated = 0;
        foreach ($arrId as $id) {
            $this->db->set('tampilkan', 1)->where('id_soal', $id)->update('cbt_soal');
            $updated++;
        }
        foreach ($unchek as $id) {
            $this->db->set('tampilkan', 0)->where('id_soal', $id)->update('cbt_soal');
        }

        sleep(1);

        $bank          = $this->cbt->getDataBankById($bank_id);
        $soals         = $this->cbt->getAllSoalByBank($bank_id);
        $tampil_counts = array_count_values(array_column($soals, 'tampilkan'));
        $total_tampil  = $tampil_counts['1'] ?? 0;
        $seharusnya    = $bank->tampil_pg + $bank->tampil_kompleks + $bank->tampil_jodohkan + $bank->tampil_isian + $bank->tampil_esai;

        $this->db->set('status_soal', $total_tampil < $seharusnya ? '0' : '1')
            ->where('id_bank', $bank_id)
            ->update('cbt_bank_soal');

        $this->output_json(['check' => $updated]);
    }

    public function copyBankSoal($id_bank)
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Log_model',       'logging');
        $this->load->model('Cbt_model',       'cbt');

        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $bank  = $this->cbt->getDataBankById($id_bank);
        $soals = $this->cbt->getAllSoalByBank($id_bank);

        $copy_data = [
            'id_tp' => $tp->id_tp,
            'id_smt' => $smt->id_smt,
            'bank_jenis_id' => $bank->bank_jenis_id,
            'bank_kode' => $bank->bank_kode . '_COPY',
            'bank_level' => $bank->bank_level,
            'bank_kelas' => $bank->bank_kelas,
            'bank_mapel_id' => $bank->bank_mapel_id,
            'bank_jurusan_id' => $bank->bank_jurusan_id,
            'bank_guru_id' => $bank->bank_guru_id,
            'bank_nama' => $bank->bank_nama,
            'kkm' => $bank->kkm,
            'deskripsi' => $bank->deskripsi,
            'jml_soal' => $bank->jml_soal,
            'tampil_pg' => $bank->tampil_pg,
            'bobot_pg' => $bank->bobot_pg,
            'jml_kompleks' => $bank->jml_kompleks,
            'tampil_kompleks' => $bank->tampil_kompleks,
            'bobot_kompleks' => $bank->bobot_kompleks,
            'jml_jodohkan' => $bank->jml_jodohkan,
            'tampil_jodohkan' => $bank->tampil_jodohkan,
            'bobot_jodohkan' => $bank->bobot_jodohkan,
            'jml_isian' => $bank->jml_isian,
            'tampil_isian' => $bank->tampil_isian,
            'bobot_isian' => $bank->bobot_isian,
            'jml_esai' => $bank->jml_esai,
            'tampil_esai' => $bank->tampil_esai,
            'bobot_esai' => $bank->bobot_esai,
            'opsi' => $bank->opsi,
            'date' => date('Y-m-d H:i:s'),
            'status' => $bank->status,
            'soal_agama' => $bank->soal_agama,
        ];

        $result = $this->master->create('cbt_bank_soal', $copy_data);
        $id     = $this->db->insert_id();

        if (!empty($soals)) {
            foreach ($soals as $soal) {
                unset($soal->id_soal);
                $soal->bank_id     = $id;
                $soal->created_on  = time();
                $soal->updated_on  = time();
            }
            $this->db->insert_batch('cbt_soal', $soals);
            $this->logging->saveLog(3, 'membuat bank soal');
        }

        $this->output_json($result);
    }

    public function buatsoal($id_bank)
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dropdown_model',  'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $_no  = $this->input->get('no',  true);
        $_jns = $this->input->get('jns', true);
        $tab  = $this->input->get('tab', true);
        $user = $this->ion_auth->user()->row();
        $tp   = $this->master->getTahunActive();
        $smt  = $this->master->getSemesterActive();
        $setting  = $this->dashboard->getSetting();
        $act_tab  = $_jns ?? '1';
        $jenis    = $tab  ?? $act_tab;
        $bank     = $this->cbt->getDataBankById($id_bank);

        $data_komplit = $this->cbt->cekSoalBelumKomplit($jenis, $bank->opsi);

        $data = [
            'user'             => $user,
            'judul'            => 'Buat Soal',
            'subjudul'         => 'Buat Soal',
            'tp'               => $this->dashboard->getTahun(),
            'tp_active'        => $tp,
            'smt'              => $this->dashboard->getSemester(),
            'smt_active'       => $smt,
            'setting'          => $setting,
            'p_no'             => $_no ?? '1',
            'p_jns'            => $act_tab,
            'tab_active'       => $jenis,
            'bank'             => $bank,
            'soal'             => null,
            'soal_ada'         => $this->cbt->cekSoalAda($id_bank, $jenis),
            'soal_belum_komplit' => $data_komplit[$id_bank] ?? [],
            'jml_pg'           => $jenis == '1' ? $this->cbt->getNomorSoalTerbesar($id_bank, 1) : null,
            'soals'            => $this->cbt->getAllSoalByBank($id_bank, $jenis),
            'jurusan'          => $this->cbt->getAllJurusan(),
            'level'            => $this->dropdown->getAllLevel($setting->jenjang),
            'kelas'            => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'guru'             => $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt),
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('cbt/banksoal/soal');
        $this->load->view('members/guru/templates/footer');
    }

    public function getSoalByNomor()
    {
        $this->load->model('Cbt_model', 'cbt');

        $bank_id = $this->input->get('bank_id', true);
        $nomor   = $this->input->get('nomor',   true);
        $jenis   = $this->input->get('jenis',   true);
        $soal    = $this->cbt->getSoalByNomor($bank_id, $nomor, $jenis);

        if ($soal !== null) {
            $soal->file = unserialize($soal->file ?? '');
            $this->output_json($soal);
        } else {
            $this->output_json(['bank_id' => $bank_id, 'jenis' => $jenis, 'nomor_soal' => $nomor]);
        }
    }

    public function tambahSoal()
    {
        $bank  = $this->input->post('bank',  true);
        $nomor = $this->input->post('nomor', true);
        $jenis = $this->input->post('jenis', true);

        $insert = $this->db->insert('cbt_soal', [
            'bank_id'    => $bank,
            'nomor_soal' => $nomor,
            'jenis'      => $jenis,
            'tampilkan'  => 0,
            'created_on' => time(),
            'updated_on' => time(),
        ]);

        $this->output_json($insert);
    }

    public function importsoal($id)
    {
        $this->load->model('Master_model',    'master');
        $this->load->model('Dropdown_model',  'dropdown');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model',       'cbt');

        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->master->getTahunActive();
        $smt     = $this->master->getSemesterActive();

        $data = [
            'user'       => $user,
            'judul'      => 'Import Bank Soal',
            'subjudul'   => 'Import Bank Soal',
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'setting'    => $setting,
            'bank'       => $this->cbt->getDataBankById($id),
            'jenis'      => $this->cbt->getAllJenisUjian(),
            'jurusan'    => $this->cbt->getAllJurusan(),
            'level'      => $this->dropdown->getAllLevel($setting->jenjang),
            'kelas'      => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/banksoal/import');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('cbt/banksoal/import');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function import()
    {
        $this->load->model('Cbt_model', 'cbt');

        $input  = $this->input->post('ganda');
        $json   = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $input), true);
        $result = [
            'error' => json_last_error_msg(),
            'soal'  => json_decode(preg_replace('﻿', '', $input)),
        ];

        $this->output_json($result);
    }

    public function getSoalSiswa($id_bank)
    {
        $this->load->model('Cbt_model', 'cbt');

        $soals = $this->cbt->getAllSoalByBank($id_bank);
        foreach ($soals as $soal) {
            if (isset($soal->file)) {
                $soal->file = unserialize($soal->file ?? '');
            }
        }

        $this->output_json(['soal' => $soals]);
    }

    public function innerXML($node)
    {
        $doc  = $node->ownerDocument;
        $frag = $doc->createDocumentFragment();
        foreach ($node->childNodes as $child) {
            $frag->appendChild($child->cloneNode(true));
        }
        return $doc->saveXML($frag);
    }

    private function file_config(): void
    {
        $config['upload_path']    = FCPATH . 'uploads/bank_soal/';
        $config['allowed_types']  = 'jpeg|jpg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4';
        $config['encrypt_name']   = true;
        $this->load->library('upload', $config);
    }

    private function validasi(int $jenis): void
    {
        $this->form_validation->set_rules('soal', 'Soal', 'required');
        if ($jenis == 1) {
            $this->form_validation->set_rules('jawaban_pg', 'Kunci Jawaban', 'required');
        } else {
            $this->form_validation->set_rules('jawaban_essai', 'Kunci Jawaban', 'required');
        }
    }

    public function saveSoal()
    {
        $this->load->model('Master_model', 'master');
        $this->load->model('Log_model',    'logging');

        $method     = $this->input->post('method',     true);
        $jenis      = (int) $this->input->post('jenis', true);
        $bank_id    = $this->input->post('bank_id',    true);
        $nomor_soal = $this->input->post('nomor_soal', true);
        $soal       = $this->input->post('soal',       false);

        $this->validasi($jenis);
        $this->file_config();

        $data = ['bank_id' => $bank_id, 'jenis' => $jenis, 'nomor_soal' => $nomor_soal, 'soal' => $soal];

        if ($jenis == 1) {
            foreach (['a', 'b', 'c', 'd', 'e'] as $abj) {
                $data['opsi_' . $abj] = $this->input->post('jawaban_' . $abj, false);
            }
            $data['jawaban'] = $this->input->post('jawaban_pg', true);
        } else {
            $data['jawaban'] = $this->input->post('jawaban_essai', false);
        }

        if ($this->form_validation->run() === FALSE) {
            $this->output_json(['status' => '400 Validation failed']);
            return;
        }

        if ($method === 'add') {
            $this->output_json(['status' => $this->master->create('cbt_soal', $data)]);
            return;
        }

        if ($method === 'edit') {
            $id_soal = $this->input->post('id_soal', true);
            $this->db->where('id_soal', $id_soal)->update('cbt_soal', $data);
            $this->output_json(['status' => true]);
            return;
        }

        $this->output_json(['status' => '400 Method not found']);
    }

    public function base64_to_jpeg(string $base64_string, string $output_file): string
    {
        $ifp  = fopen($output_file, 'wb');
        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);
        return $output_file;
    }

    public function hapusSoal()
    {
        $this->load->model('Cbt_model', 'cbt');

        $id_soal  = $this->input->post('soal_id', true);
        $result   = $this->cbt->getNomorSoalById($id_soal);
        $all_soal = $this->cbt->getNomorSoalByBankJenis($result->bank_id, $result->jenis);

        $this->db->where('id_soal', $id_soal);
        $deleted = $this->db->delete('cbt_soal');

        if (!$deleted) {
            $this->output_json($deleted);
            return;
        }

        $update     = [];
        $nomor_baru = 1;
        foreach ($all_soal as $soal) {
            $update[] = ['id_soal' => $soal->id_soal, 'nomor_soal' => $nomor_baru++];
        }

        if (!empty($update)) {
            $this->db->update_batch('cbt_soal', $update, 'id_soal');
        }

        $this->output_json($deleted);
    }

    public function uploadFile()
    {
        $this->load->model('Cbt_model', 'cbt');

        $id_soal = $this->input->get('id_soal', true);
        $soal    = $this->cbt->getFileSoalById($id_soal);
        $files   = ($soal === null || $soal->file === null) ? [] : unserialize($soal->file ?? '');

        if (!isset($_FILES['file_uploads']['name'])) {
            $this->output_json(['files' => $files]);
            return;
        }

        $nama_file_asal = $_FILES['file_uploads']['name'];
        $kode_file      = $id_soal . '_' . time();

        $config = [
            'upload_path'   => './uploads/bank_soal/',
            'allowed_types' => 'mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi',
            'file_name'     => $kode_file,
        ];
        $this->upload->initialize($config);
        $this->upload->do_upload('file_uploads');

        $file = $this->upload->data();
        $ext  = pathinfo($file['file_name'], PATHINFO_EXTENSION);
        $src  = 'uploads/bank_soal/' . $kode_file . '.' . $ext;
        $type = $_FILES['file_uploads']['type'];

        $files[] = ['file_name' => $nama_file_asal, 'alias' => $kode_file, 'src' => $src, 'type' => $type];

        $this->db->set('file', serialize($files))->where('id_soal', $id_soal)->update('cbt_soal');

        $this->output_json([
            'src'      => $src,
            'filename' => $nama_file_asal,
            'status'   => true,
            'type'     => $type,
            'size'     => $_FILES['file_uploads']['size'],
            'soal'     => $soal,
            'files'    => $files,
        ]);
    }

    public function upload_image()
    {
        if (!isset($_FILES['file']['name'])) {
            $this->output_json(['status' => false]);
            return;
        }

        $config = [
            'upload_path'   => './uploads/bank_soal/',
            'allowed_types' => 'jpg|jpeg|png|gif|mp3|ogg|wav|mp4|mpeg|webm',
            'file_name'     => 'file_' . date('YmdHis'),
        ];
        $this->upload->initialize($config);
        $this->upload->do_upload('file');

        $uploaded = $this->upload->data();
        $this->output_json(['status' => true, 'filename' => 'uploads/bank_soal/' . $uploaded['file_name']]);
    }

    public function uploadSoalImage()
    {
        $name = $this->input->post('name');
        $src  = $this->input->post('src');
        str_replace('%2B', '+', $src ?? '');

        $this->output_json([
            'status' => file_put_contents('./uploads/bank_soal/' . $name, base64_decode($src)),
            'src'    => 'uploads/bank_soal/' . $name,
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

    public function doImport()
    {
        $this->load->model('Cbt_model', 'cbt');

        $bank_id       = $this->input->post('id_bank', true);
        $string        = $this->input->post('data',    false);
        $bank          = $this->cbt->getDataBankById($bank_id);
        $jml_seharusnya = $bank->tampil_pg + $bank->tampil_kompleks + $bank->tampil_jodohkan + $bank->tampil_isian + $bank->tampil_esai;
        $json          = json_decode($string);
        $datas         = [];

        foreach ($json as $jenis => $values) {
            $data_soal = [];
            foreach ($values as $val) {
                if (!isset($val->NO)) continue;
                $no = trim($val->NO ?? '');
                if (!isset($val->SOAL) || $val->SOAL == '') continue;
                $data_soal[$no]['soal']  = $val->SOAL;
                if (!isset($val->KUNCI)) continue;
                $data_soal[$no]['kunci'] = $val->KUNCI;
            }
            $datas[$jenis] = $data_soal;
        }

        $data_insert = [];
        foreach ($datas as $jenis => $keys) {
            foreach ($keys as $no => $v) {
                $isi_soal = $v['soal'] ?? '';
                if ($isi_soal == '') continue;
                $insert             = ['jenis' => $jenis, 'nomor_soal' => $no, 'soal' => $isi_soal, 'file' => serialize([])];
                $insert['jawaban']  = $v['kunci'] ?? '';
                $data_insert[]      = $insert;
            }
        }

        $total_soal = count($data_insert);
        $inserted   = [];

        foreach ($data_insert as $dins) {
            $inserted[] = array_merge([
                'bank_id' => $bank_id,
                'jenis' => $dins['jenis'],
                'nomor_soal' => $dins['nomor_soal'],
                'soal' => $dins['soal'],
                'deskripsi' => '',
                'kesulitan' => '8',
                'timer' => '0',
                'timer_menit' => '0',
                'file' => $dins['file'],
                'tampilkan' => $total_soal == $jml_seharusnya ? '1' : '0',
                'created_on' => time(),
                'updated_on' => time(),
                'opsi_a' => $dins['opsi_a'] ?? '',
                'opsi_b' => $dins['opsi_b'] ?? '',
                'opsi_c' => $dins['opsi_c'] ?? '',
                'opsi_d' => $dins['opsi_d'] ?? '',
                'opsi_e' => $dins['opsi_e'] ?? '',
                'jawaban' => $dins['jawaban'],
            ]);
        }

        $insert_result = 0;
        if (!empty($inserted)) {
            $this->db->where('bank_id', $bank_id)->delete('cbt_soal');
            $insert_result = $this->db->insert_batch('cbt_soal', $inserted);
        }

        $this->output_json([
            'data_insert' => $inserted,
            'total'       => count($inserted),
            'json'        => $json,
            'insert'      => $insert_result,
        ]);
    }

    public function uploadSoal()
    {
        $this->load->model('Cbt_model', 'cbt');

        $bank_id  = $this->input->post('id_bank', true);
        $datas    = $this->input->post('soal',    false);
        $bank     = $this->cbt->getDataBankById($bank_id);

        $jml_spg1 = $jml_spg2 = $jml_sjod = $jml_siss = $jml_sess = 0;
        $data_insert = [];

        foreach ($datas as $jenis => $nomor) {
            foreach ($nomor as $no => $v) {
                $isi_soal = isset($v['soal'])
                    ? $this->decode_data(rawurldecode($v['soal']), $bank_id, $jenis, $no)
                    : '';
                if ($isi_soal == '') continue;

                $insert             = ['jenis' => $jenis, 'nomor_soal' => $no, 'soal' => $isi_soal, 'file' => serialize([])];
                $insert['jawaban']  = $this->decode_data(rawurldecode($v['kunci'] ?? ''), $bank_id, $jenis, $no);
                $jml_sess++;
                $data_insert[] = $insert;
            }
        }

        $sttmpl = [
            '1' => $jml_spg1 >= $bank->tampil_pg       ? '1' : '0',
            '2' => $jml_spg2 >= $bank->tampil_kompleks  ? '1' : '0',
            '3' => $jml_sjod >= $bank->tampil_jodohkan  ? '1' : '0',
            '4' => $jml_siss >= $bank->tampil_isian     ? '1' : '0',
            '5' => $jml_sess >= $bank->tampil_esai      ? '1' : '0',
        ];

        $status_soal = !in_array('0', $sttmpl) ? '1' : '0';
        $inserted    = [];

        foreach ($data_insert as $dins) {
            $inserted[] = [
                'bank_id' => $bank_id,
                'jenis' => $dins['jenis'],
                'nomor_soal' => $dins['nomor_soal'],
                'soal' => $dins['soal'],
                'deskripsi' => '',
                'kesulitan' => '8',
                'timer' => '0',
                'timer_menit' => '0',
                'file' => $dins['file'],
                'created_on' => time(),
                'updated_on' => time(),
                'opsi_a' => $dins['opsi_a'] ?? '',
                'opsi_b' => $dins['opsi_b'] ?? '',
                'opsi_c' => $dins['opsi_c'] ?? '',
                'opsi_d' => $dins['opsi_d'] ?? '',
                'opsi_e' => $dins['opsi_e'] ?? '',
                'jawaban' => $dins['jawaban'],
                'tampilkan' => $sttmpl[$dins['jenis']],
            ];
        }

        $insert_result = 0;
        if (!empty($inserted)) {
            $this->db->where('bank_id', $bank_id)->delete('cbt_soal');
            $insert_result = $this->db->insert_batch('cbt_soal', $inserted);
        }

        $this->db->set('status_soal', $status_soal)->where('id_bank', $bank_id)->update('cbt_bank_soal');

        $this->output_json([
            'data_insert' => $inserted,
            'total'       => count($inserted),
            'insert'      => $insert_result,
            'selesai'     => true,
        ]);
    }

    public function decode_data(string $html, $id_bank, $jenis, $nomor): string
    {
        if (empty($html)) {
            return '';
        }

        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = true;
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);

        return $html;
    }
}
