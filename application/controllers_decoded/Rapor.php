<?php

class Rapor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
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
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $this->load->model('Dashboard_model', 'dashboard');
        $no_update = $this->db->field_exists('nip_kepsek', 'rapor_admin_setting');
        if ($no_update) {
        }
        $field = array('nip_kepsek' => array('type' => 'int', 'constraint' => 1, 'default' => 0), 'nip_walikelas' => array('type' => 'int', 'constraint' => 1, 'default' => 0));
        $this->dbforge->add_column('rapor_admin_setting', $field);
        $user = $this->ion_auth->user()->row();
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
                }
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
            }
        }
        $data['guru'] = $guru;
        $data['mapel'] = $arrMapel;
        $data['kelas'] = $arrKelas;
        $ekstra = $mapel_guru->ekstra_kelas != null ? json_decode(json_encode(unserialize($mapel_guru->ekstra_kelas))) : [];
        $arrEkstra = [];
        $arrKelasEkstra = [];
        if (!(count($ekstra) > 0)) {
        }
        foreach ($ekstra as $m) {
            $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
            foreach ($m->kelas_ekstra as $kls) {
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                if (!($key_kelas !== false)) {
                }
                $arrKelasEkstra[$m->id_ekstra][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
            }
        }
        $data['ekstra'] = $arrEkstra;
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
        }
        $kkm = $this->rapor->getKkm($mapel . $kelas . $tp->id_tp . $smt->id_smt . '1');
        $data['mapel'] = $mapel;
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
        }
        $kkm = $this->rapor->getKkm($ekstra . $kelas . $tp->id_tp . $smt->id_smt . '2');
        $data['ekstra'] = $ekstra;
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
        }
        foreach ($mapel as $m) {
            $arrMapel[$m->id_mapel] = $m->nama_mapel;
            foreach ($m->kelas_mapel as $kls) {
                $key_kelas = array_search($kls->kelas, array_column($kelases, 'id_kelas'));
                if (!($key_kelas !== false)) {
                }
                $arrKelas[$m->id_mapel][] = ['id_kelas' => $kls->kelas, 'nama_kelas' => $kelases[$key_kelas]->nama_kelas];
            }
        }
        $data['guru'] = $guru;
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
        }
        $aspek = ['1', '2'];
        foreach ($aspek as $asp) {
            $i = 0;
            if (!($i < 8)) {
            }
            $no = $i + 1;
            $key_ki = array_search($mapel . $kelas . $asp . $no, array_column($kikds, 'id_kikd'));
            if ($key_ki !== false) {
            }
            $arrKiKd[$asp][$mapel . $kelas . $asp . $no] = ['materi_kikd' => ''];
            $i++;
        }
        $data['mapel'] = $mapel;
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
                }
                $levelsMapel[] = $kelas_guru->level_id;
                $arrKelasMapel[$m->id_mapel][] = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                $siswas[$m->id_mapel][$kelas_guru->nama_kelas] = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                $harian[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiHarianKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                $pts[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiPtsKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
                $pas[$m->id_mapel][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiAkhirKelas($m->id_mapel, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
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
        }
        foreach ($ekstra as $m) {
            $arrEkstra[$m->id_ekstra] = $m->nama_ekstra;
            foreach ($m->kelas_ekstra as $kls) {
                $kelas_guru = $this->kelas->get_one($kls->kelas);
                if (!($kelas_guru != null)) {
                }
                $arrKelasEkstra[$m->id_ekstra][] = ['id_kelas' => $kelas_guru->id_kelas, 'level' => $kelas_guru->level_id, 'nama_kelas' => $kelas_guru->nama_kelas];
                $siswae[$m->id_ekstra][$kelas_guru->nama_kelas] = count($this->kelas->getKelasSiswa($kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt));
                $ektras[$m->id_ekstra][$kelas_guru->nama_kelas] = $this->rapor->cekNilaiEkstraKelas($m->id_ekstra, $kelas_guru->id_kelas, $tp->id_tp, $smt->id_smt);
            }
        }
        $data['ekstras'] = $ektras;
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
        }
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        if ($setting->kkm_tunggal == '1') {
            $kkm = $setting;
            $kkm_ekstra = $setting;
            $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
            $nilai = [];
            $arrKiKd[] = [];
            if (!($guru->wali_kelas != null)) {
                goto fdTL7;
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                goto fWKIY;
                jca1p:
                $no = $i + 1;
                goto DQ5qp;
                DZKVt:
                EYHeJ:
                goto SL_rg;
                fWKIY:
                $i = 0;
                goto XffPj;
                XffPj:
                lzLZK:
                goto Rsknx;
                Rsknx:
                if (!($i < 8)) {
                    goto EYHeJ;
                }
                goto jca1p;
                SL_rg:
                zZHPO:
                goto H3M0M;
                tRjZd:
                OEV8o:
                goto rcOES;
                utM0n:
                goto lzLZK;
                goto DZKVt;
                rcOES:
                $i++;
                goto utM0n;
                DQ5qp:
                $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                goto tRjZd;
                H3M0M:
            }
            if ($filter == '1') {
                goto E2zsj;
            }
            $guru_mapel = '';
            foreach ($jabatan_guru as $jab) {
                goto HxQQi;
                MDtqx:
                idkQR:
                goto MGKui;
                HxQQi:
                foreach ($jab->ekstra_kelas as $mk) {
                    goto ZEykX;
                    ARvLl:
                    foreach ($mk['kelas_ekstra'] as $km) {
                        goto P1U1e;
                        P1U1e:
                        if (!($km['kelas'] == $guru->wali_kelas)) {
                            goto oMlK0;
                        }
                        goto gKmGn;
                        oxx7O:
                        pKrT5:
                        goto NiIch;
                        gKmGn:
                        $guru_mapel = $jab->nama_guru;
                        goto pmpbk;
                        pmpbk:
                        oMlK0:
                        goto oxx7O;
                        NiIch:
                    }
                    goto oFmPy;
                    ZEykX:
                    if (!($mk['id_ekstra'] == $id_mapel)) {
                        goto NXCvT;
                    }
                    goto ARvLl;
                    OoQsF:
                    EEcdM:
                    goto EkoAV;
                    oFmPy:
                    UOpO2:
                    goto LOWHi;
                    LOWHi:
                    NXCvT:
                    goto OoQsF;
                    EkoAV:
                }
                goto vjv9C;
                vjv9C:
                I0ZY2:
                goto MDtqx;
                MGKui:
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $i = 0;
            if (!($i < count($siswas))) {
                goto N1YxR;
            }
            $siswa = $siswas[$i];
            $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
            $i++;
        } else {
            $kkm = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '1');
            $kkm_ekstra = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . '2');
            $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
            $nilai = [];
            $arrKiKd[] = [];
            if (!($guru->wali_kelas != null)) {
                goto fdTL7;
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                goto fWKIY;
                jca1p:
                $no = $i + 1;
                goto DQ5qp;
                DZKVt:
                EYHeJ:
                goto SL_rg;
                fWKIY:
                $i = 0;
                goto XffPj;
                XffPj:
                lzLZK:
                goto Rsknx;
                Rsknx:
                if (!($i < 8)) {
                    goto EYHeJ;
                }
                goto jca1p;
                SL_rg:
                zZHPO:
                goto H3M0M;
                tRjZd:
                OEV8o:
                goto rcOES;
                utM0n:
                goto lzLZK;
                goto DZKVt;
                rcOES:
                $i++;
                goto utM0n;
                DQ5qp:
                $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                goto tRjZd;
                H3M0M:
            }
            if ($filter == '1') {
                goto E2zsj;
            }
            $guru_mapel = '';
            foreach ($jabatan_guru as $jab) {
                goto HxQQi;
                MDtqx:
                idkQR:
                goto MGKui;
                HxQQi:
                foreach ($jab->ekstra_kelas as $mk) {
                    goto ZEykX;
                    ARvLl:
                    foreach ($mk['kelas_ekstra'] as $km) {
                        goto P1U1e;
                        P1U1e:
                        if (!($km['kelas'] == $guru->wali_kelas)) {
                            goto oMlK0;
                        }
                        goto gKmGn;
                        oxx7O:
                        pKrT5:
                        goto NiIch;
                        gKmGn:
                        $guru_mapel = $jab->nama_guru;
                        goto pmpbk;
                        pmpbk:
                        oMlK0:
                        goto oxx7O;
                        NiIch:
                    }
                    goto oFmPy;
                    ZEykX:
                    if (!($mk['id_ekstra'] == $id_mapel)) {
                        goto NXCvT;
                    }
                    goto ARvLl;
                    OoQsF:
                    EEcdM:
                    goto EkoAV;
                    oFmPy:
                    UOpO2:
                    goto LOWHi;
                    LOWHi:
                    NXCvT:
                    goto OoQsF;
                    EkoAV:
                }
                goto vjv9C;
                vjv9C:
                I0ZY2:
                goto MDtqx;
                MGKui:
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $i = 0;
            if (!($i < count($siswas))) {
                goto N1YxR;
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
        }
        $setting = $this->rapor->getRaporSetting($tp->id_tp, $smt->id_smt);
        if ($setting->kkm_tunggal == '1') {
            $kkm = $setting;
            $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
            $nilai = [];
            $arrKiKd[] = [];
            if (!($guru->wali_kelas != null)) {
                goto KZRMm;
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                goto p0tbs;
                FFzLn:
                $no = $i + 1;
                goto kRVHG;
                p0tbs:
                $i = 0;
                goto Ubfhi;
                E9yv2:
                goto ExZxW;
                goto Ukz0T;
                Ubfhi:
                ExZxW:
                goto JKgix;
                Xd2vY:
                QXqwI:
                goto LwS4C;
                Ukz0T:
                y6wtE:
                goto Xd2vY;
                IR0dY:
                lbKzX:
                goto oYZ6o;
                JKgix:
                if (!($i < 8)) {
                    goto y6wtE;
                }
                goto FFzLn;
                kRVHG:
                $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                goto IR0dY;
                oYZ6o:
                $i++;
                goto E9yv2;
                LwS4C:
            }
            if ($filter == '1') {
                goto rr0Xm;
            }
            $guru_mapel = '';
            foreach ($jabatan_guru as $jab) {
                goto dTur9;
                dTur9:
                foreach ($jab->ekstra_kelas as $mk) {
                    goto tyQJs;
                    LXhGK:
                    foreach ($mk['kelas_ekstra'] as $km) {
                        goto zZ4Ge;
                        JK81w:
                        Iityl:
                        goto GGtqf;
                        oBSiP:
                        nqO5t:
                        goto JK81w;
                        hqCro:
                        $guru_mapel = $jab->nama_guru;
                        goto oBSiP;
                        zZ4Ge:
                        if (!($km['kelas'] == $guru->wali_kelas)) {
                            goto nqO5t;
                        }
                        goto hqCro;
                        GGtqf:
                    }
                    goto WsjyO;
                    WsjyO:
                    LOv3X:
                    goto nQqFQ;
                    u7BJR:
                    Eu55N:
                    goto A96B0;
                    tyQJs:
                    if (!($mk['id_ekstra'] == $id_mapel)) {
                        goto yNUjm;
                    }
                    goto LXhGK;
                    nQqFQ:
                    yNUjm:
                    goto u7BJR;
                    A96B0:
                }
                goto w4Vl8;
                ehxsK:
                aEu05:
                goto V3Rfe;
                w4Vl8:
                lfOi2:
                goto ehxsK;
                V3Rfe:
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $i = 0;
            if (!($i < count($siswas))) {
                goto RoHkV;
            }
            $siswa = $siswas[$i];
            $ne = $this->rapor->getEkstraKelas($id_mapel, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
            $nilai[$siswa->id_siswa] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
            $i++;
        } else {
            $jenis = $filter == '1' ? '1' : '2';
            $kkm = $this->rapor->getKkm($id_mapel . $guru->wali_kelas . $tp->id_tp . $smt->id_smt . $jenis);
            $siswas = $this->kelas->getKelasSiswa($guru->wali_kelas, $tp->id_tp, $smt->id_smt);
            $nilai = [];
            $arrKiKd[] = [];
            if (!($guru->wali_kelas != null)) {
                goto KZRMm;
            }
            $aspek = ['1', '2'];
            foreach ($aspek as $asp) {
                goto p0tbs;
                FFzLn:
                $no = $i + 1;
                goto kRVHG;
                p0tbs:
                $i = 0;
                goto Ubfhi;
                E9yv2:
                goto ExZxW;
                goto Ukz0T;
                Ubfhi:
                ExZxW:
                goto JKgix;
                Xd2vY:
                QXqwI:
                goto LwS4C;
                Ukz0T:
                y6wtE:
                goto Xd2vY;
                IR0dY:
                lbKzX:
                goto oYZ6o;
                JKgix:
                if (!($i < 8)) {
                    goto y6wtE;
                }
                goto FFzLn;
                kRVHG:
                $arrKiKd[$asp][$id_mapel . $guru->wali_kelas . $asp . $no] = $this->rapor->getKikdMapel($id_mapel . $guru->wali_kelas . $asp . $no, $tp->id_tp, $smt->id_smt);
                goto IR0dY;
                oYZ6o:
                $i++;
                goto E9yv2;
                LwS4C:
            }
            if ($filter == '1') {
                goto rr0Xm;
            }
            $guru_mapel = '';
            foreach ($jabatan_guru as $jab) {
                goto dTur9;
                dTur9:
                foreach ($jab->ekstra_kelas as $mk) {
                    goto tyQJs;
                    LXhGK:
                    foreach ($mk['kelas_ekstra'] as $km) {
                        goto zZ4Ge;
                        JK81w:
                        Iityl:
                        goto GGtqf;
                        oBSiP:
                        nqO5t:
                        goto JK81w;
                        hqCro:
                        $guru_mapel = $jab->nama_guru;
                        goto oBSiP;
                        zZ4Ge:
                        if (!($km['kelas'] == $guru->wali_kelas)) {
                            goto nqO5t;
                        }
                        goto hqCro;
                        GGtqf:
                    }
                    goto WsjyO;
                    WsjyO:
                    LOv3X:
                    goto nQqFQ;
                    u7BJR:
                    Eu55N:
                    goto A96B0;
                    tyQJs:
                    if (!($mk['id_ekstra'] == $id_mapel)) {
                        goto yNUjm;
                    }
                    goto LXhGK;
                    nQqFQ:
                    yNUjm:
                    goto u7BJR;
                    A96B0:
                }
                goto w4Vl8;
                ehxsK:
                aEu05:
                goto V3Rfe;
                w4Vl8:
                lfOi2:
                goto ehxsK;
                V3Rfe:
            }
            $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
            $i = 0;
            if (!($i < count($siswas))) {
                goto RoHkV;
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
            }
            $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            foreach ($m->kelas_mapel as $kls) {
                if (!($kls->kelas === $id_kelas)) {
                }
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $siswa = $siswas[$i];
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $ns = $this->rapor->getNilaiHarianKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $i++;
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
            }
            $nn = substr($ki->id_kikd, -1);
            $ki->nok = $nn;
            $ki->kodek = 'K' . $nn;
            $ki->k = $ki->materi_kikd;
        }
        if (!(count($kikds) == 0)) {
        }
        $kikds[] = ['nok' => 1, 'kodek' => 'K1', 'k' => 'Praktik/Portofolio/Proyek yang dinilai (lihat tabel KATA KERJA sebelah kanan)', 'nop' => 1, 'kodep' => 'P1', 'p' => 'Materi yang dinilai (lihat tabel KATA KERJA sebelah kanan)'];
        $this->output_json(['siswa' => $siswas, 'kikd' => $kikds]);
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
            }
            $updated++;
        }
        foreach ($kikdp as $kip) {
            if (!($kip != null)) {
            }
            $this->db->replace('rapor_kikd', $kip);
        }
        foreach ($kikdk as $kik) {
            if (!($kik != null)) {
            }
            $this->db->replace('rapor_kikd', $kik);
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
            }
            $updated++;
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
            }
            $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            foreach ($m->kelas_mapel as $kls) {
                if (!($kls->kelas === $id_kelas)) {
                }
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $siswa = $siswas[$i];
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $ns = $this->rapor->getNilaiPtsKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $i++;
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
            }
            $updated++;
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
            }
            $updated++;
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
            }
            $mapel = ['id_mapel' => $m->id_mapel, 'nama_mapel' => $m->nama_mapel];
            foreach ($m->kelas_mapel as $kls) {
                if (!($kls->kelas === $id_kelas)) {
                }
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $siswa = $siswas[$i];
        $dummyNilai = ['nhar' => '', 'npts' => '', 'npas' => ''];
        $ns = $this->rapor->getNilaiAkhirKelas($id_mapel, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $i++;
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
            }
            $updated++;
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
            }
            $updated++;
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
            }
            $ekstra = ['id_ekstra' => $m->id_ekstra, 'nama_ekstra' => $m->nama_ekstra];
            foreach ($m->kelas_ekstra as $kls) {
                if (!($kls->kelas === $id_kelas)) {
                }
                $kelas = ['id_kelas' => $kls->kelas, 'nama_kelas' => $this->dropdown->getNamaKelasById($tp->id_tp, $smt->id_smt, $kls->kelas)];
            }
        }
        $siswas = $this->kelas->getKelasSiswa($id_kelas, $tp->id_tp, $smt->id_smt);
        $nilai = [];
        $i = 0;
        if (!($i < count($siswas))) {
        }
        $siswa = $siswas[$i];
        $dummyNilai = ['p1' => '', 'p2' => '', 'p3' => '', 'p4' => '', 'p5' => '', 'p6' => '', 'p7' => '', 'p8' => '', 'p_rata_rata' => '', 'p_predikat' => '=', 'p_deskripsi' => '', 'k1' => '', 'k2' => '', 'k3' => '', 'k4' => '', 'k5' => '', 'k6' => '', 'k7' => '', 'k8' => '', 'k_rata_rata' => '', 'k_predikat' => '', 'k_deskripsi' => ''];
        $ns = $this->rapor->getNilaiEkstraKelas($id_ekstra, $id_kelas, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
        $nilai[$siswa->id_siswa] = $ns == null ? $dummyNilai : $ns;
        $i++;
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
            }
            $updated++;
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
            }
            $updated++;
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
            }
            $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '1', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 1, 'nilai' => serialize(['predikat' => $in[3], 'sl1' => $in[4], 'sl2' => $in[5], 'sl3' => $in[6], 'mb1' => $in[7], 'mb2' => $in[8], 'mb3' => $in[9]]), 'deskripsi' => $in[10], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_sikap', $data);
            if (!$update) {
            }
            $updated++;
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
            }
            $datas[] = ['id_nilai_sikap' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt . '2', 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'jenis' => 2, 'nilai' => serialize(['predikat' => $in[3], 'a1' => $in[4], 'a2' => $in[5], 'a3' => $in[6], 'b1' => $in[7], 'b2' => $in[8], 'b3' => $in[9], 'c1' => $in[10], 'c2' => $in[11]]), 'deskripsi' => $in[12], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_nilai_sikap', $data);
            if (!$update) {
            }
            $updated++;
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
            }
            $updated++;
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
            }
            $datas[] = ['id_catatan_wali' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_siswa' => $id_siswa, 'id_kelas' => $id_kelas, 'nilai' => serialize(['op1' => $in[3], 'op2' => $in[4], 'op3' => $in[5], 's' => $in[6], 'i' => $in[7], 'a' => $in[8]]), 'deskripsi' => $in[9], 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt];
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_catatan_wali', $data);
            if (!$update) {
            }
            $updated++;
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
            }
            $datas[] = ['id_fisik' => $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, 'id_kelas' => $id_kelas, 'id_siswa' => $id_siswa, 'id_tp' => $tp->id_tp, 'id_smt' => $smt->id_smt, 'tinggi' => $tinggi, 'berat' => $berat, 'kondisi' => serialize(['telinga' => $in[7], 'mata' => $in[8], 'gigi' => $in[9], 'lain' => $in[10]])];
        }
        $updated = 0;
        foreach ($datas as $data) {
            $update = $this->db->replace('rapor_fisik', $data);
            if (!$update) {
            }
            $updated++;
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
            }
            $updated++;
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
            }
            array_push($arrk, $km->kode_kel_mapel);
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
        }
        $siswa = $siswas[$i];
        $id_siswa = $siswa->id_siswa;
        $arr_siswas[] = $id_siswa;
        foreach ($mapels as $mapel) {
            if (isset($settingRapor) && $settingRapor->kkm_tunggal == '1') {
            }
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
        }
        $i++;
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
            }
            array_push($arrk, $km->kode_kel_mapel);
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
            $nilai_sikap = $this->rapor->getNilaiSikapByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            $nilai_rapor = $this->rapor->getNilaiRaporByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            foreach ($catatans as $catatan) {
                $catatan->nilai = unserialize($catatan->nilai);
                wx9Vu:
            }
            $i = 0;
            if (!($i < count($siswas))) {
                goto fmMUu;
            }
            $siswa = $siswas[$i];
            $id_siswa = $siswa->id_siswa;
            $dummySikap = ['predikat' => ''];
            if (count($nilai_sikap) > 0) {
                goto mP0Vj;
            }
            $sikap[$id_siswa][1] = ['deskripsi' => '', 'predikat' => $dummySikap];
            $sikap[$id_siswa][2] = ['deskripsi' => '', 'predikat' => $dummySikap];
            foreach ($mapels as $mapel) {
                goto CyUpZ;
                ik9tU:
                $nilai[$id_siswa][$mapel->id_mapel] = $nr;
                goto QwmGY;
                CyUpZ:
                $dummyNilai = ['p_deskripsi' => '', 'k_rata_rata' => '', 'k_deskripsi' => '', 'k_predikat' => '', 'nilai' => '', 'predikat' => ''];
                goto RPN8R;
                X3E1X:
                lHMRD:
                goto isWyv;
                RPN8R:
                $key_mapel = array_search($mapel->id_mapel . $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, array_column($nilai_rapor, 'id_nilai_harian'));
                goto l1gVd;
                l1gVd:
                if (!($key_mapel !== false)) {
                    goto Eh3YV;
                }
                goto dBMMx;
                QwmGY:
                Eh3YV:
                goto X3E1X;
                dBMMx:
                $nr = $nilai_rapor[$key_mapel];
                goto ik9tU;
                isWyv:
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
                goto lmkW9;
                SCXxe:
                foreach ($arrEkstra as $ar) {
                    goto NsDYe;
                    EO0Ip:
                    E16J4:
                    goto HX8Rw;
                    CTfhi:
                    LQ264:
                    goto EO0Ip;
                    NsDYe:
                    $id_ekstra = $ar->ekstra;
                    goto BT75K;
                    y1ucI:
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? $dummyEkstra : $ne;
                    goto CTfhi;
                    BT75K:
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    goto aMoVK;
                    yHOo7:
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    goto y1ucI;
                    aMoVK:
                    if (!($id_ekstra != null)) {
                        goto LQ264;
                    }
                    goto yHOo7;
                    HX8Rw:
                }
                goto Nz2kv;
                Nz2kv:
                Dmrpx:
                goto pUjCt;
                pUjCt:
                ifsjN:
                goto qKN8y;
                lmkW9:
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                goto dc8CL;
                dc8CL:
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                goto SCXxe;
                qKN8y:
            }
            $i++;
        } else {
            $other = '1';
            $nilai_sikap = $this->rapor->getNilaiSikapByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            $nilai_rapor = $this->rapor->getNilaiRaporByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            $prestasis = $this->rapor->getPrestasiByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            $catatans = $this->rapor->getCatatanWaliByKelas($id_kelas, $tp->id_tp, $smt->id_smt);
            foreach ($catatans as $catatan) {
                $catatan->nilai = unserialize($catatan->nilai);
                wx9Vu:
            }
            $i = 0;
            if (!($i < count($siswas))) {
                goto fmMUu;
            }
            $siswa = $siswas[$i];
            $id_siswa = $siswa->id_siswa;
            $dummySikap = ['predikat' => ''];
            if (count($nilai_sikap) > 0) {
                goto mP0Vj;
            }
            $sikap[$id_siswa][1] = ['deskripsi' => '', 'predikat' => $dummySikap];
            $sikap[$id_siswa][2] = ['deskripsi' => '', 'predikat' => $dummySikap];
            foreach ($mapels as $mapel) {
                goto CyUpZ;
                ik9tU:
                $nilai[$id_siswa][$mapel->id_mapel] = $nr;
                goto QwmGY;
                CyUpZ:
                $dummyNilai = ['p_deskripsi' => '', 'k_rata_rata' => '', 'k_deskripsi' => '', 'k_predikat' => '', 'nilai' => '', 'predikat' => ''];
                goto RPN8R;
                X3E1X:
                lHMRD:
                goto isWyv;
                RPN8R:
                $key_mapel = array_search($mapel->id_mapel . $id_kelas . $id_siswa . $tp->id_tp . $smt->id_smt, array_column($nilai_rapor, 'id_nilai_harian'));
                goto l1gVd;
                l1gVd:
                if (!($key_mapel !== false)) {
                    goto Eh3YV;
                }
                goto dBMMx;
                QwmGY:
                Eh3YV:
                goto X3E1X;
                dBMMx:
                $nr = $nilai_rapor[$key_mapel];
                goto ik9tU;
                isWyv:
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
                goto lmkW9;
                SCXxe:
                foreach ($arrEkstra as $ar) {
                    goto NsDYe;
                    EO0Ip:
                    E16J4:
                    goto HX8Rw;
                    CTfhi:
                    LQ264:
                    goto EO0Ip;
                    NsDYe:
                    $id_ekstra = $ar->ekstra;
                    goto BT75K;
                    y1ucI:
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? $dummyEkstra : $ne;
                    goto CTfhi;
                    BT75K:
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    goto aMoVK;
                    yHOo7:
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    goto y1ucI;
                    aMoVK:
                    if (!($id_ekstra != null)) {
                        goto LQ264;
                    }
                    goto yHOo7;
                    HX8Rw:
                }
                goto Nz2kv;
                Nz2kv:
                Dmrpx:
                goto pUjCt;
                pUjCt:
                ifsjN:
                goto qKN8y;
                lmkW9:
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                goto dc8CL;
                dc8CL:
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                goto SCXxe;
                qKN8y:
            }
            $i++;
        }
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
            $dummyAbsen = ['s' => '', 'i' => '', 'a' => ''];
            $absensi[$id_siswa] = isset($catatans[$id_siswa]) ? $catatans[$id_siswa]->nilai : $dummyAbsen;
            if (isset($setting_rapor->kkm_tunggal) && $setting_rapor->kkm_tunggal == '1') {
            }
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            foreach ($ekstras as $ext) {
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    if (!($id_ekstra != null)) {
                    }
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                }
            }
        }
        $i++;
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
            }
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            foreach ($ekstras as $ext) {
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    if (!($id_ekstra != null)) {
                    }
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
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
        }
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
            }
            $kkm[$mapel->id_mapel] = $this->rapor->getKkm($mapel->id_mapel . $id_kelas . $tp->id_tp . $smt->id_smt . '1');
            foreach ($ekstras as $ext) {
                $dummyEkstra = ['deskripsi' => '', 'nilai' => '', 'predikat' => ''];
                $arrEkstra = json_decode(json_encode(unserialize($ext->ekstra)));
                foreach ($arrEkstra as $ar) {
                    $id_ekstra = $ar->ekstra;
                    $mapelEkstra[$id_ekstra] = $this->kelas->getEkskulById($id_ekstra);
                    if (!($id_ekstra != null)) {
                    }
                    $ne = $this->rapor->getEkstraKelas($id_ekstra, $siswa->id_siswa, $tp->id_tp, $smt->id_smt);
                    $nilaiEkstra[$id_siswa][$id_ekstra] = $ne == null ? json_decode(json_encode($dummyEkstra)) : $ne;
                }
            }
        }
        $i++;
    }
}