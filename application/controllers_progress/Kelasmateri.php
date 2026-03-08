<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelasmateri extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
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
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    private function _getBaseData($judul, $subjudul)
    {
        $user    = $this->ion_auth->user()->row();
        $setting = $this->dashboard->getSetting();
        $tp      = $this->dashboard->getTahunActive();
        $smt     = $this->dashboard->getSemesterActive();

        return [
            'user'       => $user,
            'judul'      => $judul,
            'subjudul'   => $subjudul,
            'setting'    => $setting,
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'jurusan'    => $this->dropdown->getAllJurusan(),
            'level'      => $this->dropdown->getAllLevel($setting->jenjang),
            'kelas'      => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
        ];
    }

    private function _buildJadwalTanggal($jadmpl)
    {
        $arr_h = [];
        foreach ($jadmpl as $h) {
            foreach ($h as $v) {
                foreach ($v as $vk) {
                    if (!isset($arr_h[$vk->id_mapel])) {
                        $arr_h[$vk->id_mapel] = [];
                    }
                    $arr_h[$vk->id_mapel][$vk->id_kelas][$vk->id_hari][] = $vk->jam_ke;
                }
            }
        }
        return $arr_h;
    }

    private function _buildMateriData($id_guru, $jenis, $tp, $smt)
    {
        $materi        = $this->kelas->getAllMateriKelas($id_guru, $jenis);
        $kelas_materi  = [];
        $jadwal_materi = [];

        foreach ($materi as $m) {
            $km = $this->kelas->getNamaKelasById(unserialize($m->materi_kelas));
            $kelas_materi[$m->id_materi]  = $km ?? $this->kelas->getNamaKelasByKode(unserialize($m->materi_kelas));
            $jadwal_materi[$m->id_materi] = $this->kelas->getJadwalByMateri($m->id_materi, $jenis, $tp->id_tp, $smt->id_smt);
        }

        return [$materi, $kelas_materi, $jadwal_materi];
    }

    public function index()
    {
        $jenis = $this->input->get('jenis');
        $data  = $this->_getBaseData('Materi Belajar', 'Materi');
        $tp    = $data['tp_active'];
        $smt   = $data['smt_active'];

        if ($this->ion_auth->is_admin()) {
            $id_guru = $this->input->get('id');
            $allGuru = $this->dropdown->getAllGuru();
            array_unshift($allGuru, ['00' => 'Semua Guru']);

            [$materi, $kelas_materi, $jadwal_materi] = $this->_buildMateriData($id_guru, '1', $tp, $smt);

            $data['profile']       = $this->dashboard->getProfileAdmin($data['user']->id);
            $data['gurus']         = $allGuru;
            $data['id_guru']       = $id_guru ?? '';
            $data['materi']        = $materi;
            $data['kelas_materi']  = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
            [$materi, $kelas_materi, $jadwal_materi] = $this->_buildMateriData($guru->id_guru, '1', $tp, $smt);

            $data['gurus']         = [$guru->id_guru => $guru->nama_guru];
            $data['guru']          = $guru;
            $data['id_guru']       = $guru->id_guru;
            $data['materi']        = $materi;
            $data['kelas_materi']  = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function materi()
    {
        $data   = $this->_getBaseData('Materi Belajar', 'Materi');
        $tp     = $data['tp_active'];
        $smt    = $data['smt_active'];
        $jadmpl = $this->kelas->getJadwalMapel($tp->id_tp, $smt->id_smt);

        $data['jenis']          = '1';
        $data['jadwal_mapel']   = $jadmpl;
        $data['tanggal_jadwal'] = $this->_buildJadwalTanggal($jadmpl);

        if ($this->ion_auth->is_admin()) {
            $id_guru = $this->input->get('id');
            $allGuru = $this->dropdown->getAllGuru();
            $allGuru['00'] = 'Semua Guru';

            [$materi, $kelas_materi, $jadwal_materi] = $this->_buildMateriData($id_guru, '1', $tp, $smt);

            $data['profile']       = $this->dashboard->getProfileAdmin($data['user']->id);
            $data['gurus']         = $allGuru;
            $data['id_guru']       = $id_guru ?? '';
            $data['materi']        = $materi;
            $data['kelas_materi']  = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
            [$materi, $kelas_materi, $jadwal_materi] = $this->_buildMateriData($guru->id_guru, '1', $tp, $smt);

            $data['gurus']         = [$guru->id_guru => $guru->nama_guru];
            $data['guru']          = $guru;
            $data['id_guru']       = $guru->id_guru;
            $data['materi']        = $materi;
            $data['kelas_materi']  = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function tugas()
    {
        $data   = $this->_getBaseData('Tugas Kelas', 'Tugas');
        $tp     = $data['tp_active'];
        $smt    = $data['smt_active'];
        $jadmpl = $this->kelas->getJadwalMapel($tp->id_tp, $smt->id_smt);

        $data['jenis']          = '2';
        $data['jadwal_mapel']   = $jadmpl;
        $data['tanggal_jadwal'] = $this->_buildJadwalTanggal($jadmpl);

        if ($this->ion_auth->is_admin()) {
            $id_guru = $this->input->get('id');
            $allGuru = $this->dropdown->getAllGuru();
            $allGuru['00'] = 'Semua Guru';

            [$materi, $kelas_materi, $jadwal_materi] = $this->_buildMateriData($id_guru, '2', $tp, $smt);

            $data['profile']       = $this->dashboard->getProfileAdmin($data['user']->id);
            $data['gurus']         = $allGuru;
            $data['id_guru']       = $id_guru ?? '';
            $data['materi']        = $materi;
            $data['kelas_materi']  = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($data['user']->id, $tp->id_tp, $smt->id_smt);
            [$materi, $kelas_materi, $jadwal_materi] = $this->_buildMateriData($guru->id_guru, '2', $tp, $smt);

            $data['gurus']         = [$guru->id_guru => $guru->nama_guru];
            $data['guru']          = $guru;
            $data['id_guru']       = $guru->id_guru;
            $data['materi']        = $materi;
            $data['kelas_materi']  = $kelas_materi;
            $data['jadwal_materi'] = $jadwal_materi;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/materi/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function data($guru = null)
    {
        $tp  = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $this->output_json($this->kelas->getMateriKelas($guru, $tp->id_tp, $smt->id_smt), false);
    }

    public function add($jenis, $id_materi = null)
    {
        $title = $jenis == '1' ? 'Materi' : 'Tugas';
        $user  = $this->ion_auth->user()->row();
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();

        $data = [
            'user'       => $user,
            'judul'      => $title,
            'subjudul'   => $id_materi == null ? 'Buat ' . $title . ' Baru' : 'Edit ' . $title,
            'setting'    => $this->dashboard->getSetting(),
            'tp'         => $this->dashboard->getTahun(),
            'tp_active'  => $tp,
            'smt'        => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'kelas'      => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'id_materi'  => $id_materi,
            'jenis'      => $jenis,
        ];

        if ($this->ion_auth->is_admin()) {
            $materi          = $id_materi != null ? $this->kelas->getMateriKelasById($id_materi, $jenis) : null;
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $data['materi']  = $materi;
            $data['id_guru'] = $materi->id_guru ?? null;
            $data['gurus']   = $this->dropdown->getAllGuru();

            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('kelas/materi/add');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru            = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['materi']  = $id_materi != null ? $this->kelas->getMateriKelasById($id_materi, $jenis) : null;
            $data['gurus']   = [$guru->id_guru => $guru->nama_guru];
            $data['guru']    = $guru;
            $data['id_guru'] = $guru->id_guru;

            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('kelas/materi/add');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function dataAddKelas($guru)
    {
        $tp    = $this->dashboard->getTahunActive();
        $smt   = $this->dashboard->getSemesterActive();
        $guru  = $this->kelas->getGuruMapelKelas($guru, $tp->id_tp, $smt->id_smt);
        $kelas = unserialize($guru->mapel_kelas);
        $this->output_json($kelas);
    }

    public function dataAddJadwal()
    {
        $id_kelas = $this->input->get('kelas');
        $id_mapel = $this->input->get('mapel');
        $tp       = $this->dashboard->getTahunActive();
        $smt      = $this->dashboard->getSemesterActive();

        $this->output_json([
            'mapel'  => $this->kelas->getJadwalMapelByMapel($id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt),
            'terisi' => $this->kelas->getJadwalTerisi('kelas_jadwal_materi', $id_kelas, $id_mapel, $tp->id_tp, $smt->id_smt),
        ]);
    }

    public function saveJadwal()
    {
        $id_materi = $this->input->post('id_materi', true);
        $id_mapel  = $this->input->post('id_mapel', true);
        $id_kelas  = $this->input->post('id_kelas', true);
        $jenis     = $this->input->post('jenis', true);
        $jam_ke    = $this->input->post('jam_ke', true);
        $jadwal    = $this->input->post('jadwal_materi', true);
        $tp        = $this->dashboard->getTahunActive();
        $smt       = $this->dashboard->getSemesterActive();
        $jdwl      = str_replace('-', '', $jadwal ?? '');

        $update = $this->db->replace('kelas_jadwal_materi', [
            'id_kjm'        => $id_kelas . $tp->id_tp . $smt->id_smt . $jdwl . $jam_ke . $jenis,
            'id_tp'         => $tp->id_tp,
            'id_smt'        => $smt->id_smt,
            'id_kelas'      => $id_kelas,
            'id_materi'     => $id_materi,
            'id_mapel'      => $id_mapel,
            'jadwal_materi' => $jadwal,
            'jenis'         => $jenis,
        ]);

        $this->logging->saveLog(3, 'merubah jadwal materi');
        $this->output_json($update);
    }

    public function hapusJadwal($id)
    {
        $this->db->set('id_materi', '0');
        $this->db->where('id_kjm', $id);
        $this->output_json($this->db->update('kelas_jadwal_materi'));
    }

    public function saveMateri()
    {
        $jenis     = $this->input->post('jenis', true);
        $id_materi = $this->input->post('id_materi', true);
        $kelas_raw = $this->input->post('kelas', true);
        $attach    = json_decode($this->input->post('attach', true));
        $tp        = $this->dashboard->getTahunActive();
        $smt       = $this->dashboard->getSemesterActive();

        $src_file = [];
        foreach ($attach as $at) {
            if ($at->name != null) {
                $src_file[] = ['src' => $at->src, 'size' => $at->size, 'type' => $at->type, 'name' => $at->name];
            }
        }

        $id_kelas = is_array($kelas_raw) ? $kelas_raw : [];

        $isi_materi = $this->input->post('isi_materi', false);
        $dom        = new DOMDocument();
        $dom->loadHTML($isi_materi, LIBXML_HTML_NODEFDTD);
        $images = $dom->getElementsByTagName('img');
        $numimg = 1;

        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if (strpos($src, 'http') !== false) {
                $forReplace = explode('uploads', $src);
                $image->setAttribute('src', 'uploads' . $forReplace[1]);
            } else {
                $splited     = explode(',', substr($src, 5), 2);
                $mime_split  = explode('/', explode(';', $splited[0], 2)[0], 2);
                $extension   = (count($mime_split) == 2 && $mime_split[1] === 'jpeg') ? 'jpg' : ($mime_split[1] ?? 'jpg');
                $output_file = 'img_' . date('YmdHis') . $numimg . '.' . $extension;
                file_put_contents('./uploads/materi/' . $output_file, base64_decode($splited[1]));
                $image->setAttribute('src', 'uploads/materi/' . $output_file);
                $numimg++;
            }
        }

        $isi  = $dom->saveHTML();
        $data = [
            'jenis'        => $jenis,
            'id_tp'        => $tp->id_tp,
            'id_smt'       => $smt->id_smt,
            'kode_materi'  => $this->input->post('kode_materi', true),
            'id_guru'      => $this->input->post('guru', true),
            'id_mapel'     => $this->input->post('mapel', true),
            'judul_materi' => $this->input->post('judul', true),
            'isi_materi'   => $isi,
            'materi_kelas' => serialize($id_kelas),
            'file'         => serialize($src_file),
            'updated_on'   => date('Y-m-d H:i:s'),
        ];

        if ($id_materi === '') {
            $data['created_on'] = date('Y-m-d H:i:s');
        } else {
            $cek = $this->kelas->getMateriKelasById($id_materi, $jenis);
            if ($cek->id_tp != $tp->id_tp || $cek->id_smt != $smt->id_smt) {
                $data['created_on'] = date('Y-m-d H:i:s');
            }
        }

        $saved = $this->master->create('kelas_materi', $data);
        $this->logging->saveLog(3, 'membuat materi');
        $this->output_json(['status' => $saved, 'message' => 'Materi berhasil dibuat']);
    }

    public function copyMateri($id_materi, $jenis)
    {
        $tp     = $this->dashboard->getTahunActive();
        $smt    = $this->dashboard->getSemesterActive();
        $materi = $this->kelas->getMateriKelasById($id_materi, $jenis);

        $data = [
            'jenis'        => $jenis,
            'id_tp'        => $tp->id_tp,
            'id_smt'       => $smt->id_smt,
            'kode_materi'  => $materi->kode_materi,
            'id_guru'      => $materi->id_guru,
            'id_mapel'     => $materi->id_mapel ?? 0,
            'judul_materi' => $materi->judul_materi,
            'isi_materi'   => $materi->isi_materi,
            'materi_kelas' => $materi->materi_kelas,
            'file'         => $materi->file,
            'created_on'   => date('Y-m-d H:i:s'),
            'updated_on'   => date('Y-m-d H:i:s'),
        ];

        $this->logging->saveLog(3, 'membuat materi');
        $this->output_json($this->master->create('kelas_materi', $data));
    }

    public function aktifkanMateri()
    {
        $method = $this->input->post('method', true);
        $id     = $this->input->post('id_materi', true);
        $stat   = $method == '1' ? '0' : '1';

        $this->db->set('status', $stat);
        $this->db->where('id_materi', $id);
        $this->db->update('kelas_materi');
        $this->logging->saveLog(3, 'mengaktifkan materi');
        $this->output_json(['status' => true]);
    }

    public function hapusMateri()
    {
        $id = $this->input->post('id_materi', true);
        if ($this->master->delete('kelas_materi', $id, 'id_materi')) {
            $this->master->delete('kelas_jadwal_materi', $id, 'id_materi');
            $this->logging->saveLog(5, 'menghapus materi');
            $this->output_json(['status' => true]);
        } else {
            $this->output_json(['status' => false]);
        }
    }

    public function deleteAllMateri()
    {
        $ids = json_decode($this->input->post('ids', true));
        if ($this->master->delete('kelas_materi', $ids, 'id_materi')) {
            $this->master->delete('kelas_jadwal_materi', $ids, 'id_materi');
            $this->logging->saveLog(5, 'menghapus materi');
            $this->output_json(['status' => true]);
        } else {
            $this->output_json(['status' => false]);
        }
    }

    public function uploadFile()
    {
        $max_size = $this->input->post('max-size', true);

        if (!isset($_FILES['file_uploads']['name'])) {
            $this->output_json(['status' => false]);
            return;
        }

        $config = [
            'upload_path'   => './uploads/materi/',
            'allowed_types' => 'jpg|jpeg|png|gif|mpeg|mpg|mpeg3|mp3|wav|wave|mp4|avi|doc|docx|xls|xlsx|ppt|pptx|csv|pdf|rtf|txt',
            'max_size'      => $max_size,
            'overwrite'     => TRUE,
        ];
        $this->upload->initialize($config);
        $this->upload->do_upload('file_uploads');
        $result = $this->upload->data();

        $this->output_json([
            'src'      => 'uploads/materi/' . $result['file_name'],
            'filename' => pathinfo($result['file_name'], PATHINFO_FILENAME),
            'status'   => true,
            'type'     => $_FILES['file_uploads']['type'],
            'size'     => $_FILES['file_uploads']['size'],
        ]);
    }

    public function deleteFile()
    {
        $src = $this->input->post('src');
        echo unlink($src) ? 'File Delete Successfully' : 'Gagal';
    }

    private function getListDate($day, $month, $year)
    {
        $list    = [];
        $numdays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($d = 1; $d <= $numdays; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month && date('N', $time) == $day) {
                $list[] = date('Y-m-d', $time);
            }
        }

        return $list;
    }
}
