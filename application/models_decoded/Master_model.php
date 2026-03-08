<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Master_model extends CI_Model
{
    public function create($table, $data, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->insert($table, $data);
            return $insert;
        } else {
            $insert = $this->db->insert_batch($table, $data);
            return $insert;
        }
    }
    public function update($table, $data, $pk, $id = null, $batch = false)
    {
        if ($batch === false) {
            $insert = $this->db->update($table, $data, array($pk => $id));
            return $insert;
        } else {
            $insert = $this->db->update_batch($table, $data, $pk);
            return $insert;
        }
    }
    public function delete($table, $data, $pk)
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->db->where_in($pk, $data);
        $deleted = $this->db->delete($table);
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        return $deleted;
    }
    public function delete_not($table, $data, $pk, $col, $not)
    {
        $this->db->where_in($pk, $data);
        $this->db->where($col . '!=' . $not);
        return $this->db->delete($table);
    }
    public function getDataKelas()
    {
        $this->datatables->select('id_kelas, nama_kelas, id_jurusan, nama_jurusan');
        $this->datatables->from('master_kelas');
        $this->datatables->join('master_jurusan', 'jurusan_id=id_jurusan');
        $this->datatables->add_column('bulk_select', '<div class="text-center"><input type="checkbox" class="check" name="checked[]" value="$1"/></div>', 'id_kelas, nama_kelas, id_jurusan, nama_jurusan');
        return $this->datatables->generate();
    }
    public function getKelasById($id)
    {
        $this->db->select('id_kelas, nama_kelas, level_id');
        $this->db->from('master_kelas');
        $this->db->where('id_kelas', $id);
        $this->db->order_by('nama_kelas');
        return $this->db->get()->row();
    }
    public function getDataJurusan()
    {
        $this->db->select('*');
        $this->db->from('master_jurusan');
        return $this->db->get()->result();
    }
    public function getDataJurusanMapel($arrIds)
    {
        $this->db->select('id_mapel, nama_mapel');
        $this->db->from('master_mapel');
        $this->db->where_in('id_mapel', $arrIds);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_mapel] = $row->nama_mapel;
            }
            return $ret;
        }
    }
    public function getDataTableJurusan()
    {
        $this->datatables->select('*');
        $this->datatables->from('master_jurusan');
        $this->db->order_by('id_jurusan');
        return $this->datatables->generate();
    }
    public function getJurusanById($id)
    {
        $this->db->where_in('id_jurusan', $id);
        $this->db->order_by('nama_jurusan');
        return $this->db->get('master_jurusan')->result();
    }
    function updateJurusan()
    {
        $id = $this->input->post('id_jurusan');
        $name = $this->input->post('nama_jurusan', true);
        $kode = $this->input->post('kode_jurusan', true);
        $mapels = [];
        $check_mapel = $this->input->post('mapel', true);
        if (!$check_mapel) {
            $this->db->set('nama_jurusan', $name);
            $this->db->set('kode_jurusan', $kode);
            $this->db->set('mapel_peminatan', implode(',', $mapels));
            $this->db->set('status', '1');
            $this->db->where('id_jurusan', $id);
            return $this->db->update('master_jurusan');
        } else {
            $row_mapels = count($this->input->post('mapel', true));
            $i = 0;
            if (!($i <= $row_mapels)) {
            }
            array_push($mapels, $this->input->post('mapel[' . $i . ']', true));
            $i++;
        }
    }
    public function inputJurusan()
    {
        $data = ['nama_jurusan' => $this->input->post('nama_jurusan', true), 'kode_jurusan' => $this->input->post('kode_jurusan', true)];
        return $this->db->insert('master_jurusan', $data);
    }
    public function getAllDataSiswa($id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, c.nama_kelas');
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa b', 'b.id_siswa=a.id_siswa AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt . '', 'left');
        $this->db->join('master_kelas c', 'c.id_kelas=b.id_kelas', 'left');
        $this->db->order_by('b.id_kelas');
        $this->db->order_by('a.nama');
        $query = $this->db->get();
        return $query->result();
    }
    public function getSiswaByKelas($id_tp, $id_smt, $id_kelas)
    {
        $this->db->select('b.*');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->where('b.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->order_by('b.nama', 'ASC');
        return $this->db->get()->result();
    }
    public function getDataSiswa($id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('a.id_siswa, a.foto, a.nama, a.nis, a.nisn, a.jenis_kelamin, f.level_id, f.nama_kelas, b.status');
        $this->datatables->from('master_siswa a');
        $this->datatables->join('buku_induk b', 'a.id_siswa=b.id_siswa', 'left');
        $this->datatables->join('users c', 'a.username=c.username');
        $this->datatables->join('kelas_siswa d', 'd.id_siswa = a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        $this->datatables->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
        $this->db->order_by('f.nama_kelas', 'ASC');
        $this->db->order_by('b.status', 'ASC');
        return $this->datatables->generate();
    }
    public function getAllSiswa($id_tp, $id_smt, $offset, $limit, $search = null, $sort = null, $order = null)
    {
        $this->db->select('a.id_siswa, a.foto, a.nama, a.nis, a.nisn, a.jenis_kelamin, f.level_id, f.nama_kelas,' . ' (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS status');
        $this->db->from('master_siswa a');
        $this->db->limit($limit, $offset);
        $this->db->order_by('a.nama', 'ASC');
        $this->db->join('kelas_siswa d', 'd.id_siswa = a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        if (!($search != null)) {
            return $this->db->get()->result();
        } else {
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.nis', $search);
            $this->db->or_like('a.nisn', $search);
            return $this->db->get()->result();
        }
    }
    public function getSiswaPage($id_tp, $id_smt, $offset, $limit, $filter, $search = null, $sort = null, $order = null)
    {
        $this->db->select('a.id_siswa, a.foto, a.nama, a.nis, a.nisn, a.jenis_kelamin, d.id_kelas, ' . 'f.nama_kelas, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif');
        $this->db->from('master_siswa a');
        $this->db->limit($limit, $offset);
        $this->db->join('kelas_siswa d', 'd.id_siswa=a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        if ($filter == '5') {
            $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = "1"');
            $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
            $this->db->where('f.id_kelas IS NULL');
            $this->db->order_by('f.nama_kelas', 'ASC');
            $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
            $this->db->order_by('a.nama', 'ASC');
            if (!($search != null)) {
            }
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.nis', $search);
            $this->db->or_like('a.nisn', $search);
            return $this->db->get()->result();
        } else {
            $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = ' . $filter);
            $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
            if (!($filter == '1')) {
            }
            $this->db->where('f.id_kelas IS NOT NULL');
            $this->db->order_by('f.nama_kelas', 'ASC');
            $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
            $this->db->order_by('a.nama', 'ASC');
            if (!($search != null)) {
            }
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.nis', $search);
            $this->db->or_like('a.nisn', $search);
            return $this->db->get()->result();
        }
    }
    public function getSiswaTotalPage($id_tp, $id_smt, $filter, $search = null)
    {
        $this->db->select('a.id_siswa');
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa d', 'd.id_siswa=a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        if ($filter == '5') {
            $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = "1"');
            $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
            $this->db->where('f.id_kelas IS NULL');
            if (!($search != null)) {
            }
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.nis', $search);
            $this->db->or_like('a.nisn', $search);
            return $this->db->get()->num_rows();
        } else {
            $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = ' . $filter);
            $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
            if (!($filter == '1')) {
            }
            $this->db->where('f.id_kelas IS NOT NULL');
            if (!($search != null)) {
            }
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.nis', $search);
            $this->db->or_like('a.nisn', $search);
            return $this->db->get()->num_rows();
        }
    }
    public function getDataSiswaByKelas($id_tp, $id_smt, $id_kelas, $offset, $limit, $search = null, $sort = null, $order = null)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.jenis_kelamin, b.username, b.password, b.foto,' . ' f.nama_kelas, (SELECT COUNT(id) FROM users WHERE users.username = b.username) AS aktif');
        $this->db->from('kelas_siswa a');
        if (!($limit > 0)) {
            $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'right');
            if (!($search != null)) {
            }
            $this->db->like('b.nama', $search);
            $this->db->or_like('b.nis', $search);
            $this->db->or_like('b.nisn', $search);
            $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            $this->db->where('a.id_kelas', $id_kelas);
            return $this->db->get()->result();
        } else {
            $this->db->limit($limit, $offset);
            $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'right');
            if (!($search != null)) {
            }
            $this->db->like('b.nama', $search);
            $this->db->or_like('b.nis', $search);
            $this->db->or_like('b.nisn', $search);
            $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            $this->db->where('a.id_kelas', $id_kelas);
            return $this->db->get()->result();
        }
    }
    public function getDataSiswaByKelasPage($id_tp, $id_smt, $id_kelas, $search = null)
    {
        $this->db->select('a.id_siswa');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        if (!($search != null)) {
            return $this->db->get()->num_rows();
        } else {
            $this->db->like('b.nama', $search);
            $this->db->or_like('b.nis', $search);
            $this->db->or_like('b.nisn', $search);
            return $this->db->get()->num_rows();
        }
    }
    public function getSiswaById($id)
    {
        $this->db->select('a.*, b.status');
        $this->db->from('master_siswa a');
        $this->db->join('buku_induk b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->where('a.id_siswa', $id);
        return $this->db->get()->row();
    }
    public function getSiswaByArrNisn($arr_nisn, $arr_nis, $arr_username)
    {
        $this->db->select('id_siswa, nama, nisn, nis, username');
        $this->db->from('master_siswa');
        $this->db->where_in('nisn', $arr_nisn);
        $this->db->or_where_in('nis', $arr_nis);
        $this->db->or_where_in('username', $arr_username);
        return $this->db->get()->result();
    }
    public function getSiswaKelasBaru($id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('b.id_siswa, b.nama, f.id_kelas, f.nama_kelas, f.kode_kelas');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function getDataSiswaById($id_tp, $id_smt, $idSiswa)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('b.id_siswa, b.nama, b.jenis_kelamin, b.nis, b.nisn, b.username, b.password,' . ' b.foto, c.sesi_id, d.kode_ruang, e.kode_sesi, f.nama_kelas, g.nomor_peserta,' . ' h.set_siswa, i.kode_ruang as ruang_kelas, j.kode_sesi as sesi_kelas');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->join('cbt_sesi_siswa c', 'c.siswa_id=a.id_siswa', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=c.ruang_id', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=c.sesi_id', 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('cbt_kelas_ruang h', 'h.id_kelas=a.id_kelas', 'left');
        $this->db->join('cbt_ruang i', 'i.id_ruang=h.id_ruang', 'left');
        $this->db->join('cbt_sesi j', 'j.id_sesi=h.id_sesi', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_siswa', $idSiswa);
        return $this->db->get()->row();
    }
    public function getAgamaSiswa()
    {
        $this->db->select('agama');
        $this->db->distinct();
        $this->db->from('master_siswa a');
        $this->db->where('a.agama is NOT NULL', NULL, FALSE);
        $this->db->where('a.agama != "0"', NULL, FALSE);
        $this->db->not_like('a.agama', 'Pilih');
        $result = $this->db->get()->result();
        $ret['-'] = 'Bukan Mapel Agama';
        foreach ($result as $row) {
            $ret[$row->agama] = $row->agama;
        }
        return $ret;
    }
    public function getJurusan()
    {
        $this->db->select('id_jurusan, nama_jurusan');
        $this->db->from('master_kelas');
        $this->db->join('master_jurusan', 'jurusan_id=id_jurusan');
        $this->db->order_by('nama_jurusan', 'ASC');
        $this->db->group_by('id_jurusan');
        $query = $this->db->get();
        return $query->result();
    }
    public function getAllJurusan($id = null)
    {
        if ($id === null) {
            $this->db->order_by('nama_jurusan', 'ASC');
            return $this->db->get('jurusan')->result();
        } else {
            $this->db->select('jurusan_id');
            $this->db->from('jurusan_mapel');
            $this->db->where('mapel_id', $id);
            $jurusan = $this->db->get()->result();
            $id_jurusan = [];
            foreach ($jurusan as $j) {
                $id_jurusan[] = $j->jurusan_id;
            }
            if (!($id_jurusan === [])) {
            }
            $id_jurusan = null;
            $this->db->select('*');
            $this->db->from('master_jurusan');
            $this->db->where_not_in('id_jurusan', $id_jurusan);
            return $this->db->get()->result();
        }
    }
    public function getKelasByJurusan($id)
    {
        $query = $this->db->get_where('master_kelas', array('jurusan_id' => $id));
        return $query->result();
    }
    public function getDataGuru($tp, $smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('a.id_guru, a.nama_guru, a.nip, a.kode_guru, a.jenis_kelamin, a.foto, b.id_jabatan, b.id_kelas, b.mapel_kelas, c.id_level, c.level, d.nama_kelas, e.tahun, f.nama_smt');
        $this->datatables->from('master_guru a');
        $this->datatables->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->datatables->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->datatables->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->datatables->join('master_tp e', 'b.id_tp=e.id_tp', 'left');
        $this->datatables->join('master_smt f', 'b.id_smt=f.id_smt', 'left');
        return $this->datatables->generate();
    }
    public function getAllDataGuru($tp, $smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, a.nip, a.kode_guru, a.jenis_kelamin, a.foto, b.id_jabatan, b.id_kelas, b.mapel_kelas, b.ekstra_kelas, c.id_level, c.level, d.nama_kelas, e.tahun, f.nama_smt, (SELECT COUNT(id) FROM users e WHERE e.username = a.username) AS status');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->db->join('master_tp e', 'b.id_tp=e.id_tp', 'left');
        $this->db->join('master_smt f', 'b.id_smt=f.id_smt', 'left');
        $this->db->order_by('c.id_level', 'desc');
        $this->db->order_by('a.id_guru', 'asc');
        return $this->db->get()->result();
    }
    public function getGuruById($id, $id_tp = null, $id_smt = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('*');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        if (!($id_tp != null && $id_smt != null)) {
            $this->db->where('a.id_guru', $id);
            return $this->db->get()->row();
        } else {
            $this->db->join('master_kelas d', 'a.id_guru=d.guru_id AND d.id_tp=' . $id_tp . ' AND d.id_smt=' . $id_smt, 'left');
            $this->db->where('a.id_guru', $id);
            return $this->db->get()->row();
        }
    }
    public function getGuruByArrId($arr_id)
    {
        $this->db->select('nama_guru, nip');
        $this->db->from('master_guru');
        if (!(count($arr_id) > 0)) {
            return $this->db->get()->result();
        } else {
            $this->db->where_in('id_guru', $arr_id);
            return $this->db->get()->result();
        }
    }
    public function getUserIdGuruByUsername($username)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('*');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->where('a.username', $username);
        return $this->db->get()->row();
    }
    public function getDetailJabatanGuru($id_guru)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, b.id_tp, b.id_smt, b.mapel_kelas, b.ekstra_kelas, c.id_level, c.level, d.id_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas', 'left');
        $this->db->where('a.id_guru', $id_guru);
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_tp][$row->id_smt] = $row;
        }
        return $ret;
    }
    public function getJabatanGuru($id_guru, $tp, $smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, b.mapel_kelas, b.ekstra_kelas, c.id_level, c.level, d.id_kelas, d.nama_kelas');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->db->where('a.id_guru', $id_guru);
        return $this->db->get()->row();
    }
    public function getGuruMapel($tp, $smt)
    {
        $this->db->select('a.mapel_kelas, a.ekstra_kelas, a.id_jabatan, a.id_kelas, b.id_guru, b.nama_guru');
        $this->db->from('jabatan_guru a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        return $this->db->get()->result();
    }
    public function getKodeKelompokMapel()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->order_by('kode_kel_mapel');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kode_kel_mapel] = $row;
        }
        return $ret;
    }
    public function getDataKelompokMapel()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->where('id_parent', '0');
        $this->db->order_by('kode_kel_mapel');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_kel_mapel] = $row;
        }
        return $ret;
    }
    public function getKategoriKelompokMapel()
    {
        $this->db->select('kode_kel_mapel, kategori');
        $this->db->where('kategori', 'WAJIB')->or_where('kategori', 'PAI (Kemenag)');
        $this->db->from('master_kelompok_mapel');
        return $this->db->get()->result();
    }
    public function getDataSubKelompokMapel()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->where('id_parent <> 0');
        $this->db->order_by('kode_kel_mapel');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_kel_mapel] = $row;
        }
        return $ret;
    }
    public function getDataMapel()
    {
        $this->datatables->select('id_mapel, nama_mapel, kode');
        $this->datatables->from('master_mapel');
        return $this->datatables->generate();
    }
    public function getAllMapel($arrKelompok = null, $arrMapel = null)
    {
        if (!($arrMapel != null)) {
            if (!($arrMapel != null)) {
            }
            $this->db->or_where_in('id_mapel', explode(',', $arrMapel));
            $this->db->where('status', '1');
            $this->db->order_by('urutan_tampil');
            return $this->db->get('master_mapel')->result();
        } else {
            $this->db->where_in('kelompok', $arrKelompok);
            if (!($arrMapel != null)) {
            }
            $this->db->or_where_in('id_mapel', explode(',', $arrMapel));
            $this->db->where('status', '1');
            $this->db->order_by('urutan_tampil');
            return $this->db->get('master_mapel')->result();
        }
    }
    public function getAllStatusMapel($arrKelompok = null, $arrMapel = null)
    {
        if (!($arrMapel != null)) {
            if (!($arrMapel != null)) {
            }
            $this->db->or_where_in('id_mapel', explode(',', $arrMapel));
            $this->db->order_by('urutan_tampil');
            return $this->db->get('master_mapel')->result();
        } else {
            $this->db->where_in('kelompok', $arrKelompok);
            if (!($arrMapel != null)) {
            }
            $this->db->or_where_in('id_mapel', explode(',', $arrMapel));
            $this->db->order_by('urutan_tampil');
            return $this->db->get('master_mapel')->result();
        }
    }
    public function getAllMapelByKelompok($jenjang)
    {
        $this->db->where('status', '1');
        $this->db->order_by('urutan');
        $this->db->order_by('urutan_tampil');
        $result = $this->db->get('master_mapel')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kelompok][] = $row;
        }
        return $ret;
    }
    public function getAllMapelNonAktif($jenjang)
    {
        $this->db->where('status', '0');
        return $this->db->get('master_mapel')->result();
    }
    public function getMapelById($id, $single = false)
    {
        if ($single === false) {
            $this->db->where_in('id_mapel', $id);
            $this->db->order_by('nama_mapel');
            $query = $this->db->get('master_mapel')->result();
            return $query;
        } else {
            $query = $this->db->get_where('master_mapel', array('id_mapel' => $id))->row();
            return $query;
        }
    }
    function updateMapel()
    {
        $id = $this->input->post('id_mapel');
        $name = $this->input->post('nama_mapel', true);
        $kode = $this->input->post('kode_mapel', true);
        $kelompok = $this->input->post('kelompok', true);
        $status = $this->input->post('status', true);
        $urut = $this->input->post('urutan_tampil', true);
        $this->db->set('nama_mapel', $name);
        $this->db->set('kode', $kode);
        if (!($kelompok != null)) {
            $this->db->set('status', $status);
            $this->db->set('urutan_tampil', $urut);
            $this->db->where('id_mapel', $id);
            return $this->db->update('master_mapel');
        } else {
            $this->db->set('kelompok', $kelompok);
            $this->db->set('status', $status);
            $this->db->set('urutan_tampil', $urut);
            $this->db->where('id_mapel', $id);
            return $this->db->update('master_mapel');
        }
    }
    public function getAllEkstra()
    {
        return $this->db->get('master_ekstra')->result();
    }
    public function getEkstraById($id, $single = false)
    {
        if ($single === false) {
            $this->db->where_in('id_ekstra', $id);
            $this->db->order_by('nama_ekstra');
            $query = $this->db->get('master_ekstra')->result();
            return $query;
        } else {
            $query = $this->db->get_where('master_ekstra', array('id_ekstra' => $id))->row();
            return $query;
        }
    }
    function updateEkstra()
    {
        $id = $this->input->post('id_ekstra');
        $name = $this->input->post('nama_ekstra', true);
        $kode = $this->input->post('kode_ekstra', true);
        $this->db->set('nama_ekstra', $name);
        $this->db->set('kode_ekstra', $kode);
        $this->db->where('id_ekstra', $id);
        return $this->db->update('master_ekstra');
    }
    public function getKelasGuru()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('kelas_guru.id, guru.id_guru, guru.nip, guru.nama_guru, GROUP_CONCAT(master_kelas.nama_kelas) as kelas');
        $this->datatables->from('kelas_guru');
        $this->datatables->join('master_kelas', 'kelas_id=id_kelas');
        $this->datatables->join('master_guru', 'guru_id=id_guru');
        $this->datatables->group_by('guru.nama_guru');
        return $this->datatables->generate();
    }
    public function getKelasByGuru($id)
    {
        $this->db->select('kelas.id_kelas');
        $this->db->from('kelas_guru');
        $this->db->join('master_kelas', 'kelas_guru.kelas_id=kelas.id_kelas');
        $this->db->where('guru_id', $id);
        return $this->db->get()->result();
    }
    public function getAllJabatanGuru($id)
    {
        $result = $this->db->get_where('jabatan_guru', 'id_guru=' . $id)->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_tp][$row->id_smt] = $row->id_kelas;
            }
            return $ret;
        }
    }
    public function getJurusanMapel()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('jurusan_mapel.id, mapel.id_mapel, mapel.nama_mapel, jurusan.id_jurusan, GROUP_CONCAT(jurusan.nama_jurusan) as nama_jurusan');
        $this->datatables->from('jurusan_mapel');
        $this->datatables->join('master_mapel', 'mapel_id=id_mapel');
        $this->datatables->join('master_jurusan', 'jurusan_id=id_jurusan');
        $this->datatables->group_by('master_mapel.nama_mapel');
        return $this->datatables->generate();
    }
    public function getMapel($id = null)
    {
        $this->db->select('mapel_id');
        $this->db->from('jurusan_mapel');
        if (!($id !== null)) {
            $mapel = $this->db->get()->result();
            $id_mapel = [];
            foreach ($mapel as $d) {
                $id_mapel[] = $d->mapel_id;
            }
            if (!($id_mapel === [])) {
            }
            $id_mapel = null;
            $this->db->select('id_mapel, nama_mapel');
            $this->db->from('master_mapel');
            $this->db->where_not_in('id_mapel', $id_mapel);
            return $this->db->get()->result();
        } else {
            $this->db->where_not_in('mapel_id', [$id]);
            $mapel = $this->db->get()->result();
            $id_mapel = [];
            foreach ($mapel as $d) {
                $id_mapel[] = $d->mapel_id;
            }
            if (!($id_mapel === [])) {
            }
            $id_mapel = null;
            $this->db->select('id_mapel, nama_mapel');
            $this->db->from('master_mapel');
            $this->db->where_not_in('id_mapel', $id_mapel);
            return $this->db->get()->result();
        }
    }
    public function getJurusanByIdMapel($id)
    {
        $this->db->select('jurusan.id_jurusan');
        $this->db->from('jurusan_mapel');
        $this->db->join('master_jurusan', 'jurusan_mapel.jurusan_id=jurusan.id_jurusan');
        $this->db->where('mapel_id', $id);
        return $this->db->get()->result();
    }
    public function getTahunActive()
    {
        $this->db->select('id_tp, tahun');
        $this->db->from('master_tp');
        $this->db->where('active', 1);
        return $this->db->get()->row();
    }
    public function getSemesterActive()
    {
        $this->db->select('id_smt, nama_smt, smt');
        $this->db->from('master_smt');
        $this->db->where('active', 1);
        return $this->db->get()->row();
    }
    public function getJmlHariEfektif($id)
    {
        $this->db->select('*');
        $this->db->from('master_hari_efektif');
        $this->db->where('id_hari_efektif', $id);
        return $this->db->get()->row();
    }
    public function getDistinctTahunLulus()
    {
        $this->db->select('tahun_lulus');
        $this->db->distinct();
        $result = $this->db->get('buku_induk')->result();
        $ret = [];
        foreach ($result as $row) {
            if (!($row->tahun_lulus != '')) {
            }
            $ret[$row->tahun_lulus] = $row->tahun_lulus;
        }
        return $ret;
    }
    public function getDistinctKelasAkhir()
    {
        $this->db->select('kelas_akhir');
        $this->db->distinct();
        $result = $this->db->get('buku_induk')->result();
        $ret = [];
        foreach ($result as $row) {
            if (!($row->kelas_akhir != '')) {
            }
            $ret[$row->kelas_akhir] = $row->kelas_akhir;
        }
        return $ret;
    }
    public function getAlumniByTahun($tahun, $kelas = null)
    {
        $this->db->select('*');
        $this->db->from('buku_induk a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->where('a.tahun_lulus', $tahun);
        if (!($kelas != null)) {
            return $this->db->get()->result();
        } else {
            $this->db->where('a.kelas_akhir', $kelas);
            return $this->db->get()->result();
        }
    }
    public function getAlumniById($id)
    {
        $this->db->select('*');
        $this->db->from('master_siswa a');
        $this->db->join('buku_induk b', 'a.id_siswa=b.id_siswa');
        $this->db->where('a.id_siswa', $id);
        return $this->db->get()->row();
    }
    public function getAllWaliKelas()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_tp, a.id_smt, a.id_guru, b.nama_guru, c.id_level, c.level, d.id_kelas, d.nama_kelas');
        $this->db->from('jabatan_guru a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'a.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'a.id_kelas=d.id_kelas', 'left');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                if (!($row->id_level == '4')) {
                }
                $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
            }
            return $ret;
        }
    }
    public function getAllGuru()
    {
        $this->db->select('id_guru');
        $this->db->from('jabatan_guru');
        $guru = $this->db->get()->result();
        $id_guru = [];
        foreach ($guru as $d) {
            $id_guru[] = $d->id_guru;
        }
        $this->db->select('id_guru, nip, nama_guru');
        $this->db->from('master_guru');
        $this->db->where_in('id_guru', $id_guru);
        return $this->db->get()->result();
    }
    public function getAllKelas($tp = null, $smt = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_kelas, a.id_tp, a.id_smt, a.nama_kelas, a.kode_kelas, a.level_id, b.nama_jurusan, b.kode_jurusan, c.nama_guru');
        $this->db->from('master_kelas a');
        if (!($tp != null && $smt != null)) {
            $this->db->join('jabatan_guru f', 'f.id_kelas=a.id_kelas', 'left');
            $this->db->join('master_jurusan b', 'a.jurusan_id=b.id_jurusan', 'left');
            $this->db->join('master_guru c', 'f.id_guru=c.id_guru', 'left');
            $this->db->order_by('a.nama_kelas');
            $result = $this->db->get()->result();
            $ret = [];
            if ($tp != null && $smt != null) {
            }
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
            }
            return $ret;
        } else {
            $this->db->where('a.id_tp', $tp)->where('a.id_smt', $smt);
            $this->db->join('jabatan_guru f', 'f.id_kelas=a.id_kelas', 'left');
            $this->db->join('master_jurusan b', 'a.jurusan_id=b.id_jurusan', 'left');
            $this->db->join('master_guru c', 'f.id_guru=c.id_guru', 'left');
            $this->db->order_by('a.nama_kelas');
            $result = $this->db->get()->result();
            $ret = [];
            if ($tp != null && $smt != null) {
            }
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
            }
            return $ret;
        }
    }
    public function getAllKelasSiswa()
    {
        $this->db->select('*');
        $this->db->from('kelas_siswa');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas][$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function getDataInduk()
    {
        $this->db->select('a.*, b.*,');
        $this->db->from('master_siswa a');
        $this->db->join('buku_induk b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->order_by('a.nama', 'ASC');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
}