<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Kelas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function saveLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc)
    {
        $agent = $this->agent->is_browser()
            ? $this->agent->browser() . ' ' . $this->agent->version()
            : 'unknown';

        return $this->insertLog(
            $table,
            $id_siswa,
            $id_kjm,
            $jamke,
            $mapel,
            $desc,
            $agent,
            $this->agent->platform(),
            $this->input->ip_address()
        );
    }

    private function insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip)
    {
        return $this->db->insert($table, [
            'id_log'    => $id_siswa . $id_kjm,
            'log_time'  => date('Y-m-d H:i:s'),
            'id_siswa'  => $id_siswa,
            'id_materi' => $id_kjm,
            'id_mapel'  => $mapel,
            'jam_ke'    => $jamke,
            'log_desc'  => $desc,
            'address'   => $ip,
            'agent'     => $agent,
            'device'    => $os,
        ]);
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
        return $this->db->get()->result();
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
        return $result->num_rows() > 0 ? $result->result_array() : [];
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
        return $result->num_rows() > 0 ? $result->result_array() : [];
    }

    public function count_all_search()
    {
        $keyword = $this->session->userdata('keyword');
        $this->db->from('master_kelas');
        $this->db->like('nama_kelas', $keyword);
        $this->db->like('jumlah_siswa', $keyword);
        return $this->db->count_all_results();
    }

    private function _baseKelasWith($alias = 'k')
    {
        $this->db->select('*');
        $this->db->from('master_kelas ' . $alias);
        $this->db->join('master_jurusan j', 'j.id_jurusan=' . $alias . '.jurusan_id', 'left');
        $this->db->join('level_kelas l', 'l.id_level=' . $alias . '.level_id', 'left');
        $this->db->join('jabatan_guru f', 'f.id_kelas=' . $alias . '.id_kelas', 'left');
        $this->db->join('master_guru g', 'g.id_guru=f.id_guru', 'left');
        $this->db->join('master_siswa si', 'si.id_siswa=' . $alias . '.siswa_id', 'left');
        $this->db->order_by('nama_kelas', 'ASC');
    }

    public function get_one($id, $id_tp = null, $id_smt = null)
    {
        $this->_baseKelasWith('k');
        $this->db->where('k.id_kelas', $id);

        if ($id_tp !== null) {
            $this->db->where('k.id_tp', $id_tp);
        }
        if ($id_smt !== null) {
            $this->db->where('k.id_smt', $id_smt);
        }

        return $this->db->get()->row();
    }

    public function getKelasByNama($nama_kelas, $id_tp = null, $id_smt = null)
    {
        $this->_baseKelasWith('k');
        $this->db->where('k.nama_kelas', $nama_kelas);

        if ($id_tp !== null) {
            $this->db->where('k.id_tp', $id_tp);
        }
        if ($id_smt !== null) {
            $this->db->where('k.id_smt', $id_smt);
        }

        return $this->db->get()->row();
    }

    public function getNamaKelasByNama($id_tp, $id_smt)
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $result = $this->db->get()->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->nama_kelas] = $row->id_kelas;
        }
        return $ret;
    }

    public function dummy()
    {
        return [
            'id_kelas'     => '',
            'nama_kelas'   => '',
            'kode_kelas'   => '',
            'jurusan_id'   => '',
            'level_id'     => '',
            'guru_id'      => '',
            'siswa_id'     => '',
            'jumlah_siswa' => serialize([]),
        ];
    }

    public function dummyStruktur()
    {
        return [
            'id_kelas'           => '',
            'kepsek'             => '',
            'waka'               => '',
            'wali'               => '',
            'ketua'              => '',
            'wakil_ketua'        => '',
            'sekretaris_1'       => '',
            'sekretaris_2'       => '',
            'bendahara_1'        => '',
            'bendahara_2'        => '',
            'sie_ekstrakurikuler' => '',
            'sie_upacara'        => '',
            'sie_olahraga'       => '',
            'sie_keagamaan'      => '',
            'sie_keamanan'       => '',
            'sie_ketertiban'     => '',
            'sie_kebersihan'     => '',
            'sie_keindahan'      => '',
            'sie_kesehatan'      => '',
            'sie_kekeluargaan'   => '',
            'sie_humas'          => '',
        ];
    }

    public function destroy($id)
    {
        $this->db->where('id_kelas', $id);
        $this->db->delete('master_kelas');
    }

    public function get_jurusan()
    {
        $result  = $this->db->get('master_jurusan')->result();
        $ret[''] = 'Pilih Jurusan :';
        foreach ($result as $row) {
            $ret[$row->id_jurusan] = $row->nama_jurusan;
        }
        return $ret;
    }

    public function getJurusanById($id)
    {
        $this->db->where('id_jurusan', $id);
        return $this->db->get('master_jurusan')->row();
    }

    public function get_level()
    {
        $result  = $this->db->get('level_kelas')->result();
        $ret[''] = 'Pilih Level :';
        foreach ($result as $row) {
            $ret[$row->id_level] = $row->level;
        }
        return $ret;
    }

    public function getLevel($jenjang)
    {
        if ($jenjang == '1') {
            return ['' => 'Pilih Level', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
        }
        return [];
    }

    public function get_guru()
    {
        $result  = $this->db->get('master_guru')->result();
        $ret[''] = 'Pilih Guru :';
        foreach ($result as $row) {
            $ret[$row->id_guru] = $row->nama_guru;
        }
        return $ret;
    }

    public function getWaliKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, b.nama_guru');
        $this->db->from('jabatan_guru a');
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru', 'left');
        $this->db->where('id_jabatan', '4')->where('id_tp', $tp)->where('id_smt', $smt);
        $result  = $this->db->get()->result();
        $ret[''] = 'Pilih Guru :';
        foreach ($result as $row) {
            $ret[$row->id_guru] = $row->nama_guru;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_kelas][] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            if ($row->id_mapel != '') {
                $ret[$row->id_mapel][$row->id_kelas] = [$row];
            }
        }
        return $ret;
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
        $this->db->where_in('a.id_kelas', $kelas);

        if ($mapel !== null) {
            $this->db->where('a.id_mapel', $mapel, FALSE);
        }

        return $this->db->get()->result();
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
        for ($i = 1; $i < 7; $i++) {
            $inputData[] = json_decode(json_encode([
                'id_tp'      => $tp,
                'id_smt'     => $smt,
                'id_hari'    => $i,
                'jam_ke'     => $jam,
                'id_kelas'   => $kelas,
                'id_mapel'   => '0',
                'nama_mapel' => '',
                'kode'       => '',
            ]));
        }
        return $inputData;
    }

    public function getDummyMateri()
    {
        return [
            'id_materi'    => '',
            'kode_materi'  => '',
            'id_guru'      => '',
            'id_mapel'     => '',
            'id_jadwal'    => '',
            'materi_kelas' => serialize([]),
            'kelas_guru'   => serialize([]),
            'judul_materi' => '',
            'isi_materi'   => '',
            'file'         => '',
            'link_file'    => '',
            'tgl_mulai'    => '',
            'created_on'   => '',
            'updated_on'   => '',
        ];
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
        $this->db->select('a.id_materi, a.kode_materi, a.kode_mapel, a.judul_materi, a.materi_kelas, f.nama_smt, e.tahun,'
            . ' a.id_mapel, a.created_on, a.updated_on, a.file, a.status, a.id_tp, a.id_smt, b.nama_guru, d.nama_mapel, d.kode');
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
        foreach ($result as $row) {
            $ret[$row->id_mapel][$row->jenis][$row->id_materi] = $row->kode_materi;
        }
        return $ret;
    }

    public function getAllJadwalMateriByKelas($tp, $smt)
    {
        $this->db->select('a.jenis, a.id_materi, a.id_tp, a.id_smt, a.id_mapel, a.id_kjm, a.id_kelas, a.jadwal_materi,'
            . ' c.kode_materi, c.judul_materi, c.created_on, c.updated_on, c.file, c.status,'
            . ' b.nama_guru, d.nama_mapel, d.kode');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi c', 'a.id_materi=c.id_materi', 'left');
        $this->db->join('master_guru b', 'c.id_guru=b.id_guru');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('c.created_on', 'DESC');
        $result = $this->db->get()->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->jenis][$row->id_kjm] = $row;
        }
        return $ret;
    }

    public function getAllMateriKelas($id_guru, $jenis)
    {
        $this->db->select('a.id_materi, a.kode_materi, a.kode_mapel, a.judul_materi, a.materi_kelas, f.nama_smt, e.tahun, f.smt,'
            . ' a.id_mapel, a.created_on, a.updated_on, a.file, a.status, a.id_tp, a.id_smt, b.nama_guru, d.nama_mapel, d.kode');
        $this->db->from('kelas_materi a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel OR a.kode_mapel=d.kode', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp', 'left');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt', 'left');
        $this->db->where('a.jenis', $jenis);

        if ($id_guru != '0') {
            $this->db->where('a.id_guru', $id_guru);
        }

        $this->db->order_by('a.created_on', 'DESC');
        return $this->db->get()->result();
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
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt, 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt, 'left');
        $this->db->where('a.id_guru', $id_guru);
        return $this->db->get()->row();
    }

    public function getMapelGuruKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt, 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt, 'left');
        return $this->db->get()->result();
    }

    public function getListGuruMapelKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt, 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt, 'left');
        $result = $this->db->get()->result();

        $ret = [];
        foreach ($result as $guru) {
            $ret[$guru->id_guru] = $guru;
        }
        return $ret;
    }

    public function getIdKelas($tp, $smt)
    {
        $this->db->select('id_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get('master_kelas')->result();

        return array_column($result, 'id_kelas');
    }

    public function getNamaKelasById($arr_id)
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->where_in('id_kelas', $arr_id);
        $result = $this->db->get('master_kelas')->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        return $ret ?: null;
    }

    public function getNamaKelasByKode($arr_kode)
    {
        $this->db->select('id_kelas, nama_kelas');
        $this->db->where_in('kode_kelas', $arr_kode);
        $result = $this->db->get('master_kelas')->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        return $ret ?: null;
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
        foreach ($result as $row) {
            $ret[$row->id_kelas][] = $row;
        }
        return $ret;
    }

    public function getKodeMateriMapel($id_tp, $id_smt, $id_mapel, $id_guru = null)
    {
        $this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, c.id_kjm, c.jadwal_materi, c.id_kelas, d.nama_guru');
        $this->db->from('kelas_materi a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->join('kelas_jadwal_materi c', 'a.id_materi=c.id_materi');
        $this->db->join('master_guru d', 'a.id_guru=d.id_guru');

        if ($id_guru !== null) {
            $this->db->where('a.id_guru', $id_guru);
        }

        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }

    public function getAllKodeMateri($id_tp, $id_smt, $id_guru = null)
    {
        $this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, c.id_kjm, c.jadwal_materi');
        $this->db->from('kelas_materi a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->join('kelas_jadwal_materi c', 'a.id_materi=c.id_materi');

        if ($id_guru !== null) {
            $this->db->where('a.id_guru', $id_guru);
        }

        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
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
        foreach ($result as $row) {
            $ret[$row->id_materi] = $row;
        }
        return $ret;
    }

    public function getStatusMateriSiswa($id_kjm = null)
    {
        $this->db->select('a.*, b.jadwal_materi');
        $this->db->from('log_materi a');
        $this->db->join('kelas_jadwal_materi b', 'b.id_kjm=a.id_materi');

        if ($id_kjm !== null) {
            $this->db->where('a.id_materi', $id_kjm);
        }

        $result = $this->db->get()->result();
        $ret    = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->jenis][] = $row;
        }
        return $ret;
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
        return $this->db->get()->row();
    }

    public function getLoginSiswa($username)
    {
        $this->db->select('a.id, b.*');
        $this->db->from('users a');
        $this->db->join('log b', 'a.id=b.id_user', 'left');
        $this->db->where('a.username', $username);
        $this->db->order_by('b.log_time', 'DESC');
        $query = $this->db->get();
        return $query->num_rows() > 0 ? $query->row()->log_time : null;
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

        if (!$with_key) {
            return $result;
        }

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->jam_ke] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_hari][$row->jam_ke] = $row;
        }
        return $ret;
    }

    private function _parseJamKe($id_kjm, $id_kelas)
    {
        $len_kls  = strlen($id_kelas);
        $subs_jam = $len_kls + 10;
        $sisa     = strlen($id_kjm) - $subs_jam;
        $len      = $sisa === 3 ? 2 : 1;
        return substr($id_kjm, strlen($id_kjm) - $sisa, $len);
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
        foreach ($result as $row) {
            $jam        = $this->_parseJamKe($row->id_kjm, $id_kelas);
            $ret[$jam]  = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $jam                           = $this->_parseJamKe($row->id_kjm, $id_kelas);
            $ret[$row->jadwal_materi][$jam] = $row;
        }
        return $ret;
    }

    public function getAllMateriByTgl($id_kelas, $tgl, $arr_mapel)
    {
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.kode, d.nama_mapel');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.jadwal_materi', $tgl);

        if (count($arr_mapel) > 0) {
            $this->db->where_in('a.id_mapel', $arr_mapel);
        }

        $this->db->where('a.id_kelas', $id_kelas);
        $result = $this->db->get()->result();

        $ret = [];
        foreach ($result as $row) {
            $jam                              = $this->_parseJamKe($row->id_kjm, $id_kelas);
            $row->materi_kelas                = unserialize($row->materi_kelas ?? '');
            $ret[$row->id_mapel][$jam][$row->jenis] = $row;
        }
        return $ret;
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

        if ($id_mapel !== null) {
            $this->db->where('a.id_mapel', $id_mapel);
        }

        $this->db->where('MONTH(a.jadwal_materi)', $bulan)->where('YEAR(a.jadwal_materi)', $tahun);
        $result = $this->db->get()->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa][$row->jenis][$row->jadwal_materi][$row->jam_ke] = $row;
        }
        return $ret;
    }

    public function getRekapBulananMateri($id_siswa, $date, $id_materi)
    {
        $this->db->select('a.log_time, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam');
        $this->db->from('log_materi a');
        $this->db->where('DATE(a.log_time)', $date);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('a.id_materi', $id_materi);
        $result = $this->db->get()->row();

        if (!$result) {
            return [];
        }

        return [$id_siswa => $result];
    }

    private function _parseRekapMateri($result, $id_kelas)
    {
        $ret        = [];
        $len_kls    = strlen($id_kelas);
        $len_tp_smt = 2;
        $len_tahun  = 4;
        $len_bln    = 2;
        $subs_bln   = $len_kls + $len_tp_smt + $len_tahun;
        $subs_tgl   = $subs_bln + $len_bln;

        foreach ($result as $row) {
            $sisa  = strlen($row->id_materi) - ($len_kls + 10);
            $len   = $sisa === 3 ? 2 : 1;
            $bulan = substr($row->id_materi, $subs_bln, 2);
            $tgl   = substr($row->id_materi, $subs_tgl, 2);
            $jam   = substr($row->id_materi, strlen($row->id_materi) - $sisa, $len);
            $jenis = substr($row->id_materi, strlen($row->id_materi) - 1, 1);
            $ret[$jenis][$row->id_siswa][$bulan][$tgl][$jam] = $row;
        }
        return $ret;
    }

    public function getRekapMateriSemester($id_kelas, $id_materi = null)
    {
        $this->db->select('id_siswa, id_log, log_time, finish_time, id_materi,'
            . ' DAYOFMONTH(log_time) as tanggal,'
            . ' MONTH(log_time) as bulan,'
            . ' YEAR(log_time) as tahun,'
            . ' TIME_FORMAT(log_time, "%H:%i") as jam,'
            . ' nilai');
        $this->db->from('log_materi');

        if ($id_materi !== null) {
            $this->db->where('id_materi', $id_materi);
        }

        return $this->_parseRekapMateri($this->db->get()->result(), $id_kelas);
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
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.foto,'
            . ' (SELECT COUNT(id_siswa) FROM kelas_catatan_wali c WHERE c.id_siswa = b.id_siswa AND c.type = \'2\') AS jml_catatan');
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
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.foto,'
            . ' (SELECT COUNT(id_siswa) FROM kelas_catatan_mapel c WHERE c.id_siswa = b.id_siswa AND c.id_mapel = ' . $id_mapel . ' AND c.type = \'2\') AS jml_catatan');
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
