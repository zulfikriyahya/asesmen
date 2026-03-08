<?php

class Kelas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function saveLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc)
    {
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
            if ($agent == 'unknown') {
                goto P5O3x;
            }
            $os = $this->agent->platform();
            $ip = $this->input->ip_address();
            return $this->insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip);
        } else {
            if ($this->agent->is_mobile()) {
                goto HZ5Nt;
            }
            $agent = 'unknown';
            if ($agent == 'unknown') {
                goto P5O3x;
            }
            $os = $this->agent->platform();
            $ip = $this->input->ip_address();
            return $this->insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip);
        }
    }
    private function insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip)
    {
        $data = array('id_log' => $id_siswa . $id_kjm, 'log_time' => date('Y-m-d H:i:s'), 'id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'id_mapel' => $mapel, 'jam_ke' => $jamke, 'log_desc' => $desc, 'address' => $ip, 'agent' => $agent, 'device' => $os);
        return $this->db->insert($table, $data);
    }
    public function getKelasList($tp, $smt)
    {
        $this->db->select('a.*, b.nama_jurusan, d.nama_guru, e.nama, (SELECT COUNT(id_kelas_siswa) FROM kelas_siswa k WHERE a.id_kelas=k.id_kelas) AS jml_siswa');
        $this->db->from('master_kelas a');
        $this->db->join('master_jurusan b', 'b.id_jurusan=a.jurusan_id', 'left');
        $this->db->join('level_kelas c', 'c.id_level=a.level_id', 'left');
        $this->db->join('jabatan_guru f', 'f.id_jabatan=4 AND f.id_kelas=a.id_kelas', 'left');
        $this->db->join('master_guru d', 'd.id_guru=f.id_guru', 'left');
        $this->db->join('master_siswa e', 'e.id_siswa=a.siswa_id', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('a.level_id', 'ASC');
        $this->db->order_by('a.nama_kelas', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    public function getJmlSiswaKelas($id_kelas)
    {
        $this->db->from('kelas_siswa');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->count_all_results();
    }
    public function get_all($limit, $offset)
    {
        $result = $this->db->get('master_kelas', $limit, $offset);
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return array();
        }
    }
    public function getAllKelas()
    {
        $this->db->select('a.id_kelas, a.id_tp, a.id_smt, a.nama_kelas, a.kode_kelas, a.level_id, b.id_jurusan, b.nama_jurusan, b.kode_jurusan, c.id_guru, c.nama_guru');
        $this->db->from('master_kelas a');
        $this->db->join('jabatan_guru f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->join('master_jurusan b', 'a.jurusan_id=b.id_jurusan', 'left');
        $this->db->join('master_guru c', 'f.id_guru=c.id_guru', 'left');
        $this->db->order_by('a.nama_kelas');
        return $this->db->get()->result();
    }
    public function count_all()
    {
        $this->db->from('master_kelas');
        return $this->db->count_all_results();
    }
    public function get_search($limit, $offset)
    {
        $keyword = $this->session->userdata('keyword');
        $this->db->like('nama_kelas', $keyword);
        $this->db->like('jumlah_siswa', $keyword);
        $this->db->limit($limit, $offset);
        $result = $this->db->get('master_kelas');
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return array();
        }
    }
    public function count_all_search()
    {
        $keyword = $this->session->userdata('keyword');
        $this->db->from('master_kelas');
        $this->db->like('nama_kelas', $keyword);
        $this->db->like('jumlah_siswa', $keyword);
        return $this->db->count_all_results();
    }
    public function get_one($id, $id_tp = null, $id_smt = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas k');
        $this->db->join('master_jurusan j', 'j.id_jurusan=k.jurusan_id', 'left');
        $this->db->join('level_kelas l', 'l.id_level=k.level_id', 'left');
        $this->db->join('jabatan_guru f', 'f.id_kelas=k.id_kelas', 'left');
        $this->db->join('master_guru g', 'g.id_guru=f.id_guru', 'left');
        $this->db->join('master_siswa si', 'si.id_siswa=k.siswa_id', 'left');
        $this->db->order_by('nama_kelas', 'ASC');
        $this->db->where('k.id_kelas', $id);
        if (!($id_tp != null)) {
            if (!($id_smt != null)) {
                goto CZgXR;
            }
            $this->db->where('k.id_smt', $id_smt);
            return $this->db->get()->row();
        } else {
            $this->db->where('k.id_tp', $id_tp);
            if (!($id_smt != null)) {
                goto CZgXR;
            }
            $this->db->where('k.id_smt', $id_smt);
            return $this->db->get()->row();
        }
    }
    public function getKelasByNama($nama_kelas, $id_tp = null, $id_smt = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas k');
        $this->db->join('master_jurusan j', 'j.id_jurusan=k.jurusan_id', 'left');
        $this->db->join('level_kelas l', 'l.id_level=k.level_id', 'left');
        $this->db->join('jabatan_guru f', 'f.id_kelas=k.id_kelas', 'left');
        $this->db->join('master_guru g', 'g.id_guru=f.id_guru', 'left');
        $this->db->join('master_siswa si', 'si.id_siswa=k.siswa_id', 'left');
        $this->db->order_by('nama_kelas', 'ASC');
        $this->db->where('k.nama_kelas', $nama_kelas);
        if (!($id_tp != null)) {
            if (!($id_smt != null)) {
                goto gea19;
            }
            $this->db->where('k.id_smt', $id_smt);
            return $this->db->get()->row();
        } else {
            $this->db->where('k.id_tp', $id_tp);
            if (!($id_smt != null)) {
                goto gea19;
            }
            $this->db->where('k.id_smt', $id_smt);
            return $this->db->get()->row();
        }
    }
    public function getNamaKelasByNama($id_tp, $id_smt)
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->nama_kelas] = $row->id_kelas;
                SQ_oR:
            }
            return $ret;
        }
    }
    public function dummy()
    {
        return ['id_kelas' => '', 'nama_kelas' => '', 'kode_kelas' => '', 'jurusan_id' => '', 'level_id' => '', 'guru_id' => '', 'siswa_id' => '', 'jumlah_siswa' => serialize([])];
    }
    public function dummyStruktur()
    {
        return array('id_kelas' => '', 'kepsek' => '', 'waka' => '', 'wali' => '', 'ketua' => '', 'wakil_ketua' => '', 'sekretaris_1' => '', 'sekretaris_2' => '', 'bendahara_1' => '', 'bendahara_2' => '', 'sie_ekstrakurikuler' => '', 'sie_upacara' => '', 'sie_olahraga' => '', 'sie_keagamaan' => '', 'sie_keamanan' => '', 'sie_ketertiban' => '', 'sie_kebersihan' => '', 'sie_keindahan' => '', 'sie_kesehatan' => '', 'sie_kekeluargaan' => '', 'sie_humas' => '');
    }
    public function destroy($id)
    {
        $this->db->where('id_kelas', $id);
        $this->db->delete('master_kelas');
    }
    public function get_jurusan()
    {
        $result = $this->db->get('master_jurusan')->result();
        $ret[''] = 'Pilih Jurusan :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jurusan] = $row->nama_jurusan;
                NNT7N:
            }
            return $ret;
        }
    }
    public function getJurusanById($id)
    {
        $this->db->where('id_jurusan', $id);
        return $this->db->get('master_jurusan')->row();
    }
    public function get_level()
    {
        $result = $this->db->get('level_kelas')->result();
        $ret[''] = 'Pilih Level :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_level] = $row->level;
                ws_9i:
            }
            return $ret;
        }
    }
    public function getLevel($jenjang)
    {
        $levels = [];
        if ($jenjang == '1') {
            $levels = ['' => 'Pilih Level', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
            return $levels;
        } else {
            if ($jenjang == '2') {
                goto buF76;
            }
            if ($jenjang == '3') {
                goto mOG_M;
            }
            return $levels;
        }
    }
    public function get_guru()
    {
        $result = $this->db->get('master_guru')->result();
        $ret[''] = 'Pilih Guru :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_guru] = $row->nama_guru;
                ntjKd:
            }
            return $ret;
        }
    }
    public function getWaliKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, b.nama_guru');
        $this->db->from('jabatan_guru a');
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru', 'left');
        $this->db->where('id_jabatan', '4')->where('id_tp', $tp)->where('id_smt', $smt);
        $result = $this->db->get()->result();
        $ret[''] = 'Pilih Guru :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_guru] = $row->nama_guru;
                Yt10F:
            }
            return $ret;
        }
    }
    public function getKelasEkskul($kelas, $tp, $smt)
    {
        $this->db->select('*');
        $this->db->from('kelas_ekstra');
        $this->db->where('id_kelas', $kelas);
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        return $this->db->get()->result();
    }
    public function getEkskulById($id)
    {
        $this->db->select('*');
        $this->db->from('master_ekstra');
        $this->db->where('id_ekstra', $id);
        return $this->db->get()->row();
    }
    public function getAllSiswa($tp, $smt)
    {
        $this->db->select('a.id_siswa, a.nama, b.id_kelas, a.nis');
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa b', 'b.id_siswa=a.id_siswa AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt, 'left');
        $this->db->join('buku_induk c', 'c.id_siswa=a.id_siswa AND c.status=1');
        $this->db->order_by('a.nama', 'ASC');
        return $this->db->get()->result();
    }
    public function get_siswa_kelas($id, $tp, $smt)
    {
        $this->db->select('a.id_siswa, a.id_kelas, b.nis, b.nama');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND i.status=1');
        $this->db->where_in('a.id_kelas', $id);
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('b.nama', 'ASC');
        return $this->db->get()->result();
    }
    public function get_status_siswa_kelas($id, $tp, $smt)
    {
        $this->db->select('a.id_siswa, a.id_kelas');
        $this->db->from('kelas_siswa a');
        $this->db->where_in('a.id_kelas', $id);
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
                jcEqH:
            }
            return $ret;
        }
    }
    public function getJadwalKbm($tp, $smt, $kelas)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_kbm');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $this->db->where('id_kelas', $kelas);
        return $this->db->get()->row();
    }
    public function getJadwalKbmByArrKelas($tp, $smt, $arr_kelas)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_kbm');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $this->db->where_in('id_kelas', $arr_kelas);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                goto D_vwa;
                cWX8A:
                x0oWG:
                goto A6VsF;
                A6VsF:
                WDSEb:
                goto KbhVs;
                bpo_w:
                $ret[$row->id_kelas] = [];
                goto EbDwU;
                D_vwa:
                if (isset($ret[$row->id_kelas])) {
                    goto HQi5G;
                }
                goto bpo_w;
                EbDwU:
                array_push($ret[$row->id_kelas], $row);
                goto u1g41;
                LDlgZ:
                array_push($ret[$row->id_kelas], $row);
                goto cWX8A;
                qAp3_:
                HQi5G:
                goto LDlgZ;
                u1g41:
                goto x0oWG;
                goto qAp3_;
                KbhVs:
            }
            return $ret;
        }
    }
    public function getJadwalMapel($tp, $smt)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                goto CbAic;
                JaJNT:
                if (isset($ret[$row->id_mapel][$row->id_kelas])) {
                    goto eZzxd;
                }
                goto NDgJ3;
                CNgXX:
                eZzxd:
                goto gW0C7;
                PsPMt:
                goto q9gtw;
                goto CNgXX;
                NDgJ3:
                $ret[$row->id_mapel][$row->id_kelas] = [];
                goto Bc6ge;
                Kr_WG:
                scYCu:
                goto egvD9;
                CbAic:
                if (!($row->id_mapel != '')) {
                    goto o3mym;
                }
                goto JaJNT;
                zrvkv:
                q9gtw:
                goto YwsVM;
                Bc6ge:
                array_push($ret[$row->id_mapel][$row->id_kelas], $row);
                goto PsPMt;
                gW0C7:
                array_push($ret[$row->id_mapel][$row->id_kelas], $row);
                goto zrvkv;
                YwsVM:
                o3mym:
                goto Kr_WG;
                egvD9:
            }
            return $ret;
        }
    }
    public function getJadwalMapelGroupHari($tp, $smt)
    {
        $this->db->select('id_tp, id_smt, MAX(id_hari) as id_hari, MAX(jam_ke) as jam_ke');
        $this->db->from('kelas_jadwal_mapel');
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->group_by('id_hari');
        return $this->db->get()->result();
    }
    public function getJadwalMapelGroupJam($tp, $smt, $kelas)
    {
        $this->db->select('id_tp, id_smt, MAX(id_hari) as id_hari, id_kelas, MAX(jam_ke) as jam_ke');
        $this->db->from('kelas_jadwal_mapel');
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->where('id_kelas', $kelas, FALSE);
        $this->db->group_by('jam_ke');
        return $this->db->get()->result();
    }
    public function getJadwalMapelByJam($hari)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $this->db->where('id_hari', $hari, FALSE);
        return $this->db->get()->result();
    }
    public function getJadwalMapelByMapel($kelas, $mapel, $tp, $smt)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $this->db->where('a.id_tp', $tp, FALSE);
        $this->db->where('a.id_smt', $smt, FALSE);
        if (!($mapel != null)) {
            $this->db->where_in('a.id_kelas', $kelas);
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_mapel', $mapel, FALSE);
            $this->db->where_in('a.id_kelas', $kelas);
            return $this->db->get()->result();
        }
    }
    public function getJadwalTerisi($table, $kelas, $mapel, $tp, $smt)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->where('id_mapel', $mapel, FALSE);
        $this->db->where_in('id_kelas', $kelas);
        return $this->db->get()->result();
    }
    public function getJadwalMapelByHari($tp, $smt, $jam, $kelas)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $this->db->where('jam_ke', $jam, FALSE);
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->where('id_kelas', $kelas, FALSE);
        return $this->db->get()->result();
    }
    public function getDummyJadwalMapel($tp, $smt, $jam, $kelas)
    {
        $inputData = [];
        $i = 1;
        if (!($i < 7)) {
            return $inputData;
        } else {
            $data = json_decode(json_encode(['id_tp' => $tp, 'id_smt' => $smt, 'id_hari' => $i, 'jam_ke' => $jam, 'id_kelas' => $kelas, 'id_mapel' => '0', 'nama_mapel' => '', 'kode' => '']));
            array_push($inputData, $data);
            $i++;
            if (!($i < 7)) {
                goto Rr_gg;
            }
        }
    }
    public function getDummyMateri()
    {
        return array('id_materi' => '', 'kode_materi' => '', 'id_guru' => '', 'id_mapel' => '', 'id_jadwal' => '', 'materi_kelas' => serialize([]), 'kelas_guru' => serialize([]), 'judul_materi' => '', 'isi_materi' => '', 'file' => '', 'link_file' => '', 'tgl_mulai' => '', 'created_on' => '', 'updated_on' => '');
    }
    public function getTableMateriKelas($id_guru = null)
    {
        $this->datatables->select('*');
        $this->datatables->from('kelas_materi a');
        $this->datatables->join('master_guru b', 'a.id_guru=b.id_guru');
        $this->datatables->join('jabatan_guru c', 'a.id_guru=c.id_guru');
        $this->datatables->join('kelas_jadwal_mapel d', 'a.id_mapel=d.id_mapel');
        return $this->datatables->generate();
    }
    public function getMateriKelas($id_guru, $tp, $smt)
    {
        $this->db->select('a.id_materi, a.kode_materi, a.kode_mapel, a.judul_materi, a.materi_kelas, f.nama_smt, e.tahun,' . ' a.id_mapel, a.created_on, a.updated_on, a.file, a.status, a.id_tp, a.id_smt, b.nama_guru, d.nama_mapel, d.kode');
        $this->db->from('kelas_materi a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt');
        $this->db->where('a.id_guru', $id_guru);
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('a.created_on', 'DESC');
        return $this->db->get()->result();
    }
    public function getAllMateriByKelas($tp, $smt)
    {
        $this->db->select('a.jenis, a.id_mapel, a.id_materi, a.kode_materi');
        $this->db->from('kelas_materi a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru');
        $this->db->join('master_mapel c', 'a.id_mapel=c.id_mapel', 'left');
        $this->db->where('a.status', '1');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('a.created_on', 'DESC');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_mapel][$row->jenis][$row->id_materi] = $row->kode_materi;
                ndJ7c:
            }
            return $ret;
        }
    }
    public function getAllJadwalMateriByKelas($tp, $smt)
    {
        $this->db->select('a.jenis, a.id_materi, a.id_tp, a.id_smt, a.id_mapel, a.id_kjm, a.id_kelas, a.jadwal_materi,' . ' c.kode_materi, c.judul_materi, c.created_on, c.updated_on, c.file, c.status,' . ' b.nama_guru, d.nama_mapel, d.kode');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi c', 'a.id_materi=c.id_materi', 'left');
        $this->db->join('master_guru b', 'c.id_guru=b.id_guru');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('c.created_on', 'DESC');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->jenis][$row->id_kjm] = $row;
                uT2JF:
            }
            return $ret;
        }
    }
    public function getAllMateriKelas($id_guru, $jenis)
    {
        $this->db->select('a.id_materi, a.kode_materi, a.kode_mapel, a.judul_materi, a.materi_kelas, f.nama_smt, e.tahun, f.smt,' . ' a.id_mapel, a.created_on, a.updated_on, a.file, a.status, a.id_tp, a.id_smt, b.nama_guru, d.nama_mapel, d.kode');
        $this->db->from('kelas_materi a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel OR a.kode_mapel=d.kode', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp', 'left');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt', 'left');
        $this->db->where('a.jenis', $jenis);
        if (!($id_guru != '0')) {
            $this->db->order_by('a.created_on', 'DESC');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_guru', $id_guru);
            $this->db->order_by('a.created_on', 'DESC');
            return $this->db->get()->result();
        }
    }
    public function getMateriKelasById($id_materi, $jenis)
    {
        $this->db->select('a.*, b.nama_guru, b.foto, d.id_mapel, d.nama_mapel, c.mapel_kelas as kelas_guru');
        $this->db->from('kelas_materi a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('jabatan_guru c', 'a.id_guru=c.id_guru', 'left');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_materi', $id_materi);
        $this->db->where('a.jenis', $jenis);
        return $this->db->get()->row();
    }
    public function getMateriKelasSiswa($id_kjm, $jenis)
    {
        $this->db->select('a.id_kjm, a.id_materi, a.jadwal_materi, b.*, c.nama_guru, c.foto, e.id_mapel, e.nama_mapel, d.mapel_kelas as kelas_guru');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi');
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru');
        $this->db->join('jabatan_guru d', 'b.id_guru=d.id_guru');
        $this->db->join('master_mapel e', 'b.id_mapel=e.id_mapel');
        $this->db->where('a.jenis', $jenis);
        $this->db->where('a.id_kjm', $id_kjm);
        return $this->db->get()->row();
    }
    public function getGuruMapelKelas($id_guru, $tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->db->where('a.id_guru', $id_guru);
        return $this->db->get()->row();
    }
    public function getMapelGuruKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        return $this->db->get()->result();
    }
    public function getListGuruMapelKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $result = $this->db->get()->result();
        $rest = [];
        foreach ($result as $guru) {
            $rest[$guru->id_guru] = $guru;
        }
        return $rest;
    }
    public function getIdKelas($tp, $smt)
    {
        $this->db->select('id_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get('master_kelas')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                array_push($ret, $row->id_kelas);
                Uhm2F:
            }
            return $ret;
        }
    }
    public function getNamaKelasById($arr_id)
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->where_in('id_kelas', $arr_id);
        $result = $this->db->get('master_kelas')->result();
        $ret = null;
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->nama_kelas;
                DVG5_:
            }
            return $ret;
        }
    }
    public function getNamaKelasByKode($arr_kode)
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->where_in('kode_kelas', $arr_kode);
        $result = $this->db->get('master_kelas')->result();
        $ret = null;
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->nama_kelas;
                AHQVr:
            }
            return $ret;
        }
    }
    public function getJadwalByMateri($id, $jenis, $tp, $smt)
    {
        $this->db->select('id_kjm, id_kelas, jadwal_materi, (SELECT COUNT(id_materi) FROM log_materi WHERE kelas_jadwal_materi.id_kjm=log_materi.id_materi) AS jml_siswa');
        $this->db->where('id_materi', $id);
        $this->db->where('jenis', $jenis);
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get('kelas_jadwal_materi')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                goto jO8Ef;
                LXEEL:
                ahkgn:
                goto HGMPD;
                VPfFd:
                array_push($ret[$row->id_kelas], $row);
                goto RHUJL;
                TH1f7:
                EA_pg:
                goto KaZA7;
                RHUJL:
                goto EA_pg;
                goto LXEEL;
                HGMPD:
                array_push($ret[$row->id_kelas], $row);
                goto TH1f7;
                KaZA7:
                eYMsn:
                goto ohI1e;
                el_Vf:
                $ret[$row->id_kelas] = [];
                goto VPfFd;
                jO8Ef:
                if (isset($ret[$row->id_kelas])) {
                    goto ahkgn;
                }
                goto el_Vf;
                ohI1e:
            }
            return $ret;
        }
    }
    public function getKodeMateriMapel($id_tp, $id_smt, $id_mapel, $id_guru = null)
    {
        $this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, c.id_kjm, c.jadwal_materi, c.id_kelas, d.nama_guru');
        $this->db->from('kelas_materi a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->join('kelas_jadwal_materi c', 'a.id_materi=c.id_materi');
        $this->db->join('master_guru d', 'a.id_guru=d.id_guru');
        if (!($id_guru != null)) {
            $this->db->where('a.id_mapel', $id_mapel);
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_guru', $id_guru);
            $this->db->where('a.id_mapel', $id_mapel);
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            return $this->db->get()->result();
        }
    }
    public function getAllKodeMateri($id_tp, $id_smt, $id_guru = null)
    {
        $this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, c.id_kjm, c.jadwal_materi');
        $this->db->from('kelas_materi a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->join('kelas_jadwal_materi c', 'a.id_materi=c.id_materi');
        if (!($id_guru != null)) {
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_guru', $id_guru);
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            return $this->db->get()->result();
        }
    }
    public function getKelasSiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, b.jenis_kelamin, c.nama_kelas, c.level_id');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->order_by('b.nama', 'ASC');
        return $this->db->get()->result();
    }
    public function getKelasSiswaDuaSmt($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.nama_kelas, c.level_id');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getStatusMateriSiswaByJadwal($id_siswa, $arr_id_kjm)
    {
        $this->db->select('*');
        $this->db->from('log_materi');
        $this->db->where_in('id_materi', $arr_id_kjm);
        $this->db->where('id_siswa', $id_siswa);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_materi] = $row;
                s1PdQ:
            }
            return $ret;
        }
    }
    public function getStatusMateriSiswa($id_kjm = null)
    {
        $this->db->select('a.*, b.jadwal_materi');
        $this->db->from('log_materi a');
        $this->db->join('kelas_jadwal_materi b', 'b.id_kjm=a.id_materi');
        if (!($id_kjm != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto KwXyv;
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
                B6fWz:
            }
            return $ret;
        } else {
            $this->db->where('a.id_materi', $id_kjm);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto KwXyv;
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
                B6fWz:
            }
            return $ret;
        }
    }
    public function getNilaiMateriSiswa($id_siswa)
    {
        $this->db->select('a.nilai, a.catatan, b.jadwal_materi, c.kode_materi, c.judul_materi, c.jenis, d.nama_mapel, d.kode');
        $this->db->from('log_materi a');
        $this->db->join('kelas_jadwal_materi b', 'a.id_materi=b.id_kjm');
        $this->db->join('kelas_materi c', 'b.id_materi=c.id_materi');
        $this->db->join('master_mapel d', 'c.id_mapel=d.id_mapel');
        $this->db->where('a.id_siswa', $id_siswa);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->jenis][] = $row;
                DrQvR:
            }
            return $ret;
        }
    }
    public function getStatusSiswaByMapel($table, $id_mapel)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('id_mapel', $id_mapel);
        return $this->db->get()->result();
    }
    public function getLogFileSiswa($table, $id_log)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where('id_log', $id_log);
        $query = $this->db->get();
        return $query->row();
    }
    public function getLoginSiswa($username)
    {
        $this->db->select('a.id, b.*');
        $this->db->from('users a');
        $this->db->join('log b', 'a.id=b.id_user', 'left');
        $this->db->where('a.username', $username);
        $this->db->order_by('b.log_time', 'DESC');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row()->log_time;
        } else {
            return null;
        }
    }
    public function loadJadwalSiswaHariIni($id_tp, $id_smt, $id_kelas, $id_hari, $with_key = true)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_hari', $id_hari);
        $result = $this->db->get()->result();
        if ($with_key) {
            $ret = [];
            if (!$result) {
                goto NXxZB;
            }
            foreach ($result as $key => $row) {
                $ret[$row->jam_ke] = $row;
                gvByg:
            }
            return $ret;
        } else {
            return $result;
        }
    }
    public function loadJadwalSiswaSeminggu($id_tp, $id_smt, $id_kelas)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_hari][$row->jam_ke] = $row;
                uuPcy:
            }
            return $ret;
        }
    }
    public function getMateriSiswa($id_kelas, $tgl, $jenis)
    {
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.judul_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.nama_mapel');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.jenis', $jenis);
        $this->db->where('a.jadwal_materi', $tgl);
        $this->db->where('a.id_kelas', $id_kelas);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                goto n3i0T;
                CT9pH:
                $subs_jam = $len_kls + 10;
                goto BOlkj;
                ognM2:
                $ret[$jam] = $row;
                goto wIAAD;
                wIAAD:
                PDk3U:
                goto zY7aZ;
                I5PMW:
                $len = $sisa === 3 ? 2 : 1;
                goto cacE0;
                n3i0T:
                $len_kls = strlen($row->id_kelas);
                goto CT9pH;
                cacE0:
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $len, 1);
                goto ognM2;
                BOlkj:
                $sisa = strlen($row->id_kjm) - $subs_jam;
                goto I5PMW;
                zY7aZ:
            }
            return $ret;
        }
    }
    public function getMateriSiswaSeminggu($id_tp, $id_smt, $id_kelas, $jenis)
    {
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.judul_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.nama_mapel');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.jenis', $jenis);
        $this->db->where('a.id_kelas', $id_kelas);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                goto lxI5s;
                eyfKB:
                $subs_jam = $len_kls + 10;
                goto GI4VY;
                rquLe:
                VVr1w:
                goto R8fUh;
                Oft3i:
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
                goto g6cX0;
                GI4VY:
                $sisa = strlen($row->id_kjm) - $subs_jam;
                goto kszZU;
                kszZU:
                $len = $sisa === 3 ? 2 : 1;
                goto Oft3i;
                lxI5s:
                $len_kls = strlen($row->id_kelas);
                goto eyfKB;
                g6cX0:
                $ret[$row->jadwal_materi][$jam] = $row;
                goto rquLe;
                R8fUh:
            }
            return $ret;
        }
    }
    public function getAllMateriByTgl($id_kelas, $tgl, $arr_mapel)
    {
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.kode, d.nama_mapel');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.jadwal_materi', $tgl);
        if (!(count($arr_mapel) > 0)) {
            $this->db->where('a.id_kelas', $id_kelas);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto DKL07;
            }
            foreach ($result as $key => $row) {
                goto MSrA6;
                MSrA6:
                $len_kls = strlen($row->id_kelas);
                goto oHIs9;
                oHIs9:
                $subs_jam = $len_kls + 10;
                goto HRR7h;
                laodu:
                $len = $sisa === 3 ? 2 : 1;
                goto L71DT;
                huuYd:
                y0BwP:
                goto afANu;
                k2Cs6:
                $ret[$row->id_mapel][$jam][$row->jenis] = $row;
                goto huuYd;
                HRR7h:
                $sisa = strlen($row->id_kjm) - $subs_jam;
                goto laodu;
                Q13nv:
                $row->materi_kelas = unserialize($row->materi_kelas ?? '');
                goto k2Cs6;
                L71DT:
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
                goto Q13nv;
                afANu:
            }
            return $ret;
        } else {
            $this->db->where_in('a.id_mapel', $arr_mapel);
            $this->db->where('a.id_kelas', $id_kelas);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto DKL07;
            }
            foreach ($result as $key => $row) {
                goto MSrA6;
                MSrA6:
                $len_kls = strlen($row->id_kelas);
                goto oHIs9;
                oHIs9:
                $subs_jam = $len_kls + 10;
                goto HRR7h;
                laodu:
                $len = $sisa === 3 ? 2 : 1;
                goto L71DT;
                huuYd:
                y0BwP:
                goto afANu;
                k2Cs6:
                $ret[$row->id_mapel][$jam][$row->jenis] = $row;
                goto huuYd;
                HRR7h:
                $sisa = strlen($row->id_kjm) - $subs_jam;
                goto laodu;
                Q13nv:
                $row->materi_kelas = unserialize($row->materi_kelas ?? '');
                goto k2Cs6;
                L71DT:
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
                goto Q13nv;
                afANu:
            }
            return $ret;
        }
    }
    public function getRekapStatusMapel($id_siswa, $date, $id_mapel)
    {
        $this->db->select('a.jam_ke, a.log_time, c.jenis, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam, d.nama_mapel, d.kode, d.id_mapel');
        $this->db->from('log_materi a');
        $this->db->join('kelas_jadwal_materi b', 'a.id_materi=b.id_kjm', 'left');
        $this->db->join('kelas_materi c', 'b.id_materi=c.id_materi', 'left');
        $this->db->join('master_mapel d', 'c.id_mapel=d.id_mapel', 'left');
        $this->db->where('DATE(a.log_time)', $date);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('a.id_mapel', $id_mapel);
        return $this->db->get()->result();
    }
    public function getRekapStatusMateri($id_siswa, $arr_id_kjm)
    {
        $this->db->select('a.jam_ke, a.log_time, a.finish_time, c.jenis, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam, d.nama_mapel, d.kode, d.id_mapel');
        $this->db->from('log_materi a');
        $this->db->join('kelas_jadwal_materi b', 'a.id_materi=b.id_kjm', 'left');
        $this->db->join('kelas_materi c', 'b.id_materi=c.id_materi', 'left');
        $this->db->join('master_mapel d', 'c.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where_in('a.id_materi', $arr_id_kjm);
        return $this->db->get()->result();
    }
    public function getRekapBulananMapel($id_siswa, $bulan)
    {
        $this->db->select('a.log_time as materi, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam_materi');
        $this->db->from('log_materi a');
        $this->db->where('MONTH(a.log_time)', $bulan);
        $this->db->where('a.id_siswa', $id_siswa);
        return $this->db->get()->result();
    }
    public function getRekapBulananSiswa($id_mapel, $id_kelas, $tahun, $bulan)
    {
        $this->db->select('a.*, b.log_time, b.finish_time, b.id_siswa, b.jam_ke, DAYOFMONTH(b.log_time) as tanggal, MONTH(b.log_time) as bulan, YEAR(b.log_time) as tahun, TIME_FORMAT(b.log_time, "%H:%i") as jam');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('log_materi b', 'b.id_materi=a.id_kjm');
        $this->db->where('a.id_kelas', $id_kelas);
        if (!($id_mapel != null)) {
            $this->db->where('MONTH(a.jadwal_materi)', $bulan)->where('YEAR(a.jadwal_materi)', $tahun);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto lcNZJ;
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa][$row->jenis][$row->jadwal_materi][$row->jam_ke] = $row;
                aUGal:
            }
            return $ret;
        } else {
            $this->db->where('a.id_mapel', $id_mapel);
            $this->db->where('MONTH(a.jadwal_materi)', $bulan)->where('YEAR(a.jadwal_materi)', $tahun);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto lcNZJ;
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa][$row->jenis][$row->jadwal_materi][$row->jam_ke] = $row;
                aUGal:
            }
            return $ret;
        }
    }
    public function getRekapBulananMateri($id_siswa, $date, $id_materi)
    {
        $this->db->select('a.log_time, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam');
        $this->db->from('log_materi a');
        $this->db->where('DATE(a.log_time)', $date);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('a.id_materi', $id_materi);
        $result = $this->db->get()->row();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
                osJxY:
            }
            return $ret;
        }
    }
    public function getRekapMateriSemester($id_kelas, $id_materi = null)
    {
        $this->db->select('id_siswa, id_log, log_time, finish_time, id_materi,' . ' DAYOFMONTH(log_time) as tanggal,' . ' MONTH(log_time) as bulan,' . ' YEAR(log_time) as tahun,' . ' TIME_FORMAT(log_time, "%H:%i") as jam,' . ' nilai');
        $this->db->from('log_materi');
        if (!($id_materi != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto neAf3;
            }
            foreach ($result as $key => $row) {
                goto vDFvc;
                fst4r:
                $jenis = substr($row->id_materi, strlen($row->id_materi) - 1, 1);
                goto EduIH;
                i0SyR:
                $sisa = strlen($row->id_materi) - ($len_kls + 10);
                goto TE84R;
                rlh_4:
                $subs_bln = $len_kls + $len_tp_smt + $len_tahun;
                goto NBjzf;
                TE84R:
                $len = $sisa === 3 ? 2 : 1;
                goto vviVf;
                Of2ut:
                $len_tp_smt = 2;
                goto FRp3Z;
                CxAsx:
                $tgl = substr($row->id_materi, $subs_tgl, 2);
                goto tjfEi;
                klbZN:
                $len_bln = 2;
                goto h_YsC;
                vviVf:
                $bulan = substr($row->id_materi, $subs_bln, 2);
                goto CxAsx;
                icWoH:
                lDbgs:
                goto YfngD;
                FRp3Z:
                $len_tahun = 4;
                goto klbZN;
                EduIH:
                $ret[$jenis][$row->id_siswa][$bulan][$tgl][$jam] = $row;
                goto icWoH;
                tjfEi:
                $jam = substr($row->id_materi, strlen($row->id_materi) - $sisa, $len);
                goto fst4r;
                vDFvc:
                $len_kls = strlen($id_kelas);
                goto Of2ut;
                NBjzf:
                $subs_tgl = $subs_bln + $len_bln;
                goto i0SyR;
                h_YsC:
                $len_hari = 2;
                goto rlh_4;
                YfngD:
            }
            return $ret;
        } else {
            $this->db->where('id_materi', $id_materi);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
                goto neAf3;
            }
            foreach ($result as $key => $row) {
                goto vDFvc;
                fst4r:
                $jenis = substr($row->id_materi, strlen($row->id_materi) - 1, 1);
                goto EduIH;
                i0SyR:
                $sisa = strlen($row->id_materi) - ($len_kls + 10);
                goto TE84R;
                rlh_4:
                $subs_bln = $len_kls + $len_tp_smt + $len_tahun;
                goto NBjzf;
                TE84R:
                $len = $sisa === 3 ? 2 : 1;
                goto vviVf;
                Of2ut:
                $len_tp_smt = 2;
                goto FRp3Z;
                CxAsx:
                $tgl = substr($row->id_materi, $subs_tgl, 2);
                goto tjfEi;
                klbZN:
                $len_bln = 2;
                goto h_YsC;
                vviVf:
                $bulan = substr($row->id_materi, $subs_bln, 2);
                goto CxAsx;
                icWoH:
                lDbgs:
                goto YfngD;
                FRp3Z:
                $len_tahun = 4;
                goto klbZN;
                EduIH:
                $ret[$jenis][$row->id_siswa][$bulan][$tgl][$jam] = $row;
                goto icWoH;
                tjfEi:
                $jam = substr($row->id_materi, strlen($row->id_materi) - $sisa, $len);
                goto fst4r;
                vDFvc:
                $len_kls = strlen($id_kelas);
                goto Of2ut;
                NBjzf:
                $subs_tgl = $subs_bln + $len_bln;
                goto i0SyR;
                h_YsC:
                $len_hari = 2;
                goto rlh_4;
                YfngD:
            }
            return $ret;
        }
    }
    public function getStrukturKelas($kelas)
    {
        $this->db->where('id_kelas', $kelas);
        return $this->db->get('kelas_struktur')->row();
    }
    public function getCatatanKelas($kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_kelas', $kelas);
        $this->db->where('type', '1');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get('kelas_catatan_wali')->result();
    }
    public function getCatatanSiswa($id_tp, $id_smt, $id_kelas)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.foto,' . ' (SELECT COUNT(id_siswa) FROM kelas_catatan_wali c WHERE c.id_siswa = b.id_siswa AND c.type = \'2\') AS jml_catatan');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        return $this->db->get()->result();
    }
    public function getAllCatatanSiswa($id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('kelas_catatan_wali');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getCatatanMapelKelas($kelas, $mapel, $id_tp, $id_smt)
    {
        $this->db->where('id_kelas', $kelas);
        $this->db->where('id_mapel', $mapel);
        $this->db->where('type', '1');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->order_by('tgl', 'DESC');
        return $this->db->get('kelas_catatan_mapel')->result();
    }
    public function getCatatanMapelSiswa($id_tp, $id_smt, $id_kelas, $id_mapel)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.foto,' . ' (SELECT COUNT(id_siswa) FROM kelas_catatan_mapel c WHERE c.id_siswa = b.id_siswa AND c.id_mapel = ' . $id_mapel . ' AND c.type = \'2\') AS jml_catatan');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        return $this->db->get()->result();
    }
    public function getAllCatatanMapelSiswa($id_siswa, $id_mapel, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('kelas_catatan_mapel');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->order_by('tgl', 'DESC');
        return $this->db->get()->result();
    }
    public function getCatatanMapelBySiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.*, b.nama_guru, b.nip, b.foto');
        $this->db->from('kelas_catatan_mapel a');
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getCatatanSiswaBySiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.*, c.nama_guru, c.nip, c.foto');
        $this->db->from('kelas_catatan_wali a');
        $this->db->join('jabatan_guru b', 'b.id_kelas=a.id_kelas');
        $this->db->join('master_guru c', 'c.id_guru=b.id_guru');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getCatatanMapelSiswaDetail($id_catatan)
    {
        $this->db->select('a.*, b.nama_guru, b.nip, b.foto, d.level as jabatan, e.nama_mapel, e.kode');
        $this->db->from('kelas_catatan_mapel a');
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru');
        $this->db->join('jabatan_guru c', 'c.id_guru=a.id_guru');
        $this->db->join('level_guru d', 'd.id_level=c.id_jabatan');
        $this->db->join('master_mapel e', 'e.id_mapel=a.id_mapel');
        $this->db->where('a.id_catatan', $id_catatan);
        return $this->db->get()->row();
    }
    public function getCatatanKelasSiswaDetail($id_catatan)
    {
        $this->db->select('a.*, c.nama_guru, c.nip, c.foto, e.level as jabatan, f.nama_kelas');
        $this->db->from('kelas_catatan_wali a');
        $this->db->join('jabatan_guru b', 'b.id_kelas=a.id_kelas');
        $this->db->join('master_guru c', 'c.id_guru=b.id_guru');
        $this->db->join('level_guru e', 'e.id_level=b.id_jabatan');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        $this->db->where('a.id_catatan', $id_catatan);
        return $this->db->get()->row();
    }
    public function getReading($table, $id_catatan)
    {
        $this->db->select('reading, type, readed');
        $this->db->where('id_catatan', $id_catatan);
        return $this->db->get($table)->row();
    }
}