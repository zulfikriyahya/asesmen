## File: application/models_decoded/Cbt_model.php

```php
<?php

class Cbt_model extends CI_Model
{
    public function get_where($table, $pk, $id, $join = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($pk, $id);
        if (!($join !== null)) {
            if (!($order !== null)) {
            }
            foreach ($order as $field => $sort) {
                $this->db->order_by($field, $sort);
            }
            $query = $this->db->get();
            return $query;
        } else {
            foreach ($join as $table => $field) {
                $this->db->join($table, $field);
            }
            if (!($order !== null)) {
            }
            foreach ($order as $field => $sort) {
                $this->db->order_by($field, $sort);
            }
            $query = $this->db->get();
            return $query;
        }
    }
    public function saveLog($id_siswa, $id_jadwal, $type, $desc)
    {
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
            if ($agent == 'unknown') {
            }
            $os = $this->agent->platform();
            $ip = $this->input->ip_address();
            return $this->insertLog($id_siswa, $id_jadwal, $type, $desc, $agent, $os, $ip);
        } else {
            if ($this->agent->is_mobile()) {
            }
            $agent = 'unknown';
            if ($agent == 'unknown') {
            }
            $os = $this->agent->platform();
            $ip = $this->input->ip_address();
            return $this->insertLog($id_siswa, $id_jadwal, $type, $desc, $agent, $os, $ip);
        }
    }
    private function insertLog($id_siswa, $id_jadwal, $type, $desc, $agent, $os, $ip)
    {
        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . $type)->get('log_ujian')->row();
        if ($log != null) {
            $this->db->set('log_type', $type);
            $this->db->set('log_desc', $desc);
            $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . $type);
            $insert = $this->db->update('log_ujian');
            return $insert;
        } else {
            $data = array('id_log' => $id_siswa . '0' . $id_jadwal . $type, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'log_type' => $type, 'log_desc' => $desc, 'address' => $ip, 'agent' => $agent, 'device' => $os);
            $insert = $this->db->insert('log_ujian', $data);
            return $insert;
        }
    }
    public function getDataSiswa($username, $id_tp, $id_smt)
    {
        $this->db->select('a.id_siswa, a.nisn, a.nis, a.nama, a.jenis_kelamin, a.username, a.password, a.agama, a.foto,' . ' b.id_kelas_siswa, b.id_tp, b.id_smt, b.id_siswa, b.id_kelas,' . ' c.nama_kelas, c.kode_kelas, c.level_id, ' . ' d.kelas_id, d.ruang_id, d.sesi_id');
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa b', 'a.id_siswa=b.id_siswa AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('master_kelas c', 'b.id_kelas=c.id_kelas AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt, 'left');
        $this->db->join('cbt_sesi_siswa d', 'a.id_siswa=d.siswa_id', 'left');
        $this->db->where('username', $username);
        $query = $this->db->get()->row();
        return $query;
    }
    public function getDataSiswaById($id_tp, $id_smt, $idSiswa)
    {
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
    public function getWaktuSesiById($id_sesi)
    {
        $this->db->select('*');
        $this->db->where('id_sesi', $id_sesi);
        $result = $this->db->get('cbt_sesi')->row();
        return $result;
    }
    public function getAllRuang()
    {
        $this->db->select('id_ruang, nama_ruang, kode_ruang');
        $result = $this->db->get('cbt_ruang')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_ruang] = $row->kode_ruang;
            }
            return $ret;
        }
    }
    public function getKelasByLevel($level, $arrKelas)
    {
        $this->db->select('id_kelas, kode_kelas');
        $this->db->where('level_id', $level);
        $this->db->where_in('id_kelas', $arrKelas);
        $result = $this->db->get('master_kelas')->result();
        return $result;
    }
    public function getAllJurusan()
    {
        $result = $this->db->get('master_jurusan')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jurusan] = $row->kode_jurusan;
            }
            return $ret;
        }
    }
    public function getPengawas($id_pengawas)
    {
        $this->db->select('id_pengawas, id_jadwal, id_tp, id_smt, id_ruang, id_sesi, id_guru');
        $this->db->from('cbt_pengawas');
        $this->db->where('id_pengawas', $id_pengawas);
        return $this->db->get()->row();
    }
    public function getPengawasByGuru($tp, $smt, $id_guru)
    {
        $this->db->select('a.id_pengawas, a.id_jadwal, a.id_tp, a.id_smt, a.id_ruang, a.id_sesi, a.id_guru,' . ' b.id_jadwal, b.tgl_mulai, b.tgl_selesai, c.bank_kode, d.kode_jenis');
        $this->db->from('cbt_pengawas a');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->like('a.id_guru', $id_guru);
        $this->db->join('cbt_jadwal b', 'b.id_jadwal=a.id_jadwal');
        $this->db->join('cbt_bank_soal c', 'b.id_bank=c.id_bank');
        $this->db->join('cbt_jenis d', 'd.id_jenis=b.id_jenis', 'left');
        return $this->db->get()->result();
    }
    public function getPengawasByJadwal($tp, $smt, $id_jadwal, $sesi = null, $ruang = null)
    {
        $this->db->select('id_pengawas, id_guru');
        $this->db->from('cbt_pengawas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $this->db->where('id_jadwal', $id_jadwal);
        if (!($sesi != null)) {
            if (!($ruang != null)) {
            }
            $this->db->where('id_ruang', $ruang);
            return $this->db->get()->result();
        } else {
            $this->db->where('id_sesi', $sesi);
            if (!($ruang != null)) {
            }
            $this->db->where('id_ruang', $ruang);
            return $this->db->get()->result();
        }
    }
    public function getAllPengawas($tp, $smt, $ruang = null, $sesi = null)
    {
        $this->db->select('id_pengawas, id_jadwal, id_ruang, id_sesi, id_guru');
        $this->db->from('cbt_pengawas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        if (!($ruang != null)) {
            if (!($sesi != null)) {
            }
            $this->db->where('id_sesi', $sesi);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal][$row->id_ruang][$row->id_sesi] = $row;
            }
            return $ret;
        } else {
            $this->db->where('id_ruang', $ruang);
            if (!($sesi != null)) {
            }
            $this->db->where('id_sesi', $sesi);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal][$row->id_ruang][$row->id_sesi] = $row;
            }
            return $ret;
        }
    }
    public function getDistinctRuang($tp, $smt, $arrKelas)
    {
        $this->db->distinct('a.ruang_id');
        $this->db->select('a.ruang_id, a.sesi_id, b.kode_ruang, b.nama_ruang, c.kode_sesi, c.nama_sesi');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('cbt_ruang b', 'b.id_ruang=a.ruang_id');
        $this->db->join('cbt_sesi c', 'c.id_sesi=a.sesi_id');
        if (!(count($arrKelas) > 0)) {
            $this->db->order_by('b.nama_ruang', 'ASC');
            $this->db->order_by('c.nama_sesi', 'ASC');
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->ruang_id][$row->sesi_id] = $row;
            }
            return $ret;
        } else {
            $this->db->where_in('kelas_id', $arrKelas);
            $this->db->order_by('b.nama_ruang', 'ASC');
            $this->db->order_by('c.nama_sesi', 'ASC');
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->ruang_id][$row->sesi_id] = $row;
            }
            return $ret;
        }
    }
    public function getKelasUjian($kelas_id)
    {
        $this->db->select('kelas_id, ruang_id, sesi_id');
        $this->db->from('cbt_sesi_siswa');
        $this->db->where('kelas_id', $kelas_id);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->ruang_id][$row->sesi_id][] = $row->kelas_id;
            }
            return $ret;
        }
    }
    public function getDistinctKelasLevel($tp, $smt, $arrLevel)
    {
        $this->db->select('id_kelas, level_id');
        $this->db->distinct();
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $this->db->where_in('level_id', $arrLevel);
        $result = $this->db->get()->result();
        return $result;
    }
    public function getAllJenisUjian()
    {
        $this->db->select('id_jenis, nama_jenis, kode_jenis');
        $result = $this->db->get('cbt_jenis')->result();
        $ret[''] = 'Jenis Penilaian :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jenis] = $row->kode_jenis;
            }
            return $ret;
        }
    }
    public function getAllJenisUjianByArrJenis($arrJenis)
    {
        $this->db->where_in('id_jenis', $arrJenis);
        $result = $this->db->get('cbt_jenis')->result();
        $ret[''] = 'Jenis Penilaian :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jenis] = $row->kode_jenis;
            }
            return $ret;
        }
    }
    public function getPengawasHariIni($tgl)
    {
        $this->db->from('cbt_jadwal a');
        $this->db->where("a.tgl_mulai <= '{$tgl}' AND a.tgl_selesai >= '{$tgl}'");
        $this->db->join('cbt_pengawas b', 'b.id_jadwal=a.id_jadwal');
        $this->db->where('status', '1');
        return $this->db->get()->result();
    }
    public function getJadwalGuru($tp, $smt, $guru)
    {
        $this->db->select('a.id_jadwal, a.tgl_mulai, b.bank_kode, b.bank_kelas');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_guru_id=' . $guru);
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        return $this->db->get()->result();
    }
    public function getJadwalKelas($tp, $smt)
    {
        $this->db->select('a.id_jadwal, a.tgl_mulai, b.bank_kode, b.bank_kelas');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        return $this->db->get()->result();
    }
    public function getJadwalByJenis($jenis, $level, $dari, $sampai)
    {
        $this->db->select('a.id_jadwal, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai, a.jam_ke,' . ' c.bank_kode, c.bank_level, c.bank_kelas, b.kode_jenis, b.nama_jenis, d.kode, d.nama_mapel');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        $this->db->where('a.id_jenis', $jenis);
        if (!($level != '0')) {
            if (!($dari != null)) {
            }
            $this->db->where('a.tgl_mulai >=', $dari);
            if (!($sampai != null)) {
            }
            $this->db->where('a.tgl_mulai <=', $sampai);
            $this->db->order_by('a.tgl_mulai', 'ASC');
            $this->db->order_by('a.jam_ke', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('c.bank_level', $level);
            if (!($dari != null)) {
            }
            $this->db->where('a.tgl_mulai >=', $dari);
            if (!($sampai != null)) {
            }
            $this->db->where('a.tgl_mulai <=', $sampai);
            $this->db->order_by('a.tgl_mulai', 'ASC');
            $this->db->order_by('a.jam_ke', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getAllJadwalByJenis($jenis, $tp, $smt)
    {
        $this->db->select('a.id_jadwal, a.id_jenis, a.tgl_mulai, ' . 'c.bank_kode, c.bank_level, c.bank_kelas, b.kode_jenis, b.nama_jenis, d.id_mapel, d.kode, d.nama_mapel');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        if (!($jenis != null)) {
            $this->db->where('a.id_tp', $tp);
            $this->db->where('a.id_smt', $smt);
            $this->db->order_by('a.tgl_mulai', 'ASC');
            $this->db->order_by('a.jam_ke', 'ASC');
            $this->db->order_by('c.bank_level', 'ASC');
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->tgl_mulai][$row->id_mapel][] = $row;
            }
            return $ret;
        } else {
            $this->db->where('a.id_jenis', $jenis);
            $this->db->where('a.id_tp', $tp);
            $this->db->where('a.id_smt', $smt);
            $this->db->order_by('a.tgl_mulai', 'ASC');
            $this->db->order_by('a.jam_ke', 'ASC');
            $this->db->order_by('c.bank_level', 'ASC');
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->tgl_mulai][$row->id_mapel][] = $row;
            }
            return $ret;
        }
    }
    public function getAllBankSoal($guru = null)
    {
        $this->db->select('id_bank, bank_kode');
        if (!($guru !== null)) {
            $result = $this->db->get('cbt_bank_soal')->result();
            $ret['0'] = 'Pilih Bank Soal :';
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_bank] = $row->bank_kode;
            }
            return $ret;
        } else {
            $this->db->where('bank_guru_id', $guru);
            $result = $this->db->get('cbt_bank_soal')->result();
            $ret['0'] = 'Pilih Bank Soal :';
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_bank] = $row->bank_kode;
            }
            return $ret;
        }
    }
    public function getAllBankSoalByTp($id_tp, $id_smt, $guru = null)
    {
        $this->db->select('id_bank, bank_kode, bank_mapel_id, tampil_pg, tampil_kompleks, tampil_jodohkan, tampil_isian, tampil_esai');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->where('status', '1');
        $this->db->where('status_soal', '1');
        if (!($guru !== null)) {
            $result = $this->db->get('cbt_bank_soal')->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_bank] = $row;
            }
            return $ret;
        } else {
            $this->db->where('bank_guru_id', $guru);
            $result = $this->db->get('cbt_bank_soal')->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_bank] = $row;
            }
            return $ret;
        }
    }
    public function getAllBankSoalByMapel($id_tp, $id_smt, $mapel)
    {
        $this->db->select('id_bank, bank_kode, bank_mapel_id, tampil_pg, tampil_kompleks, tampil_jodohkan, tampil_isian, tampil_esai, status');
        $this->db->from('cbt_bank_soal');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->where('bank_mapel_id', $mapel);
        $this->db->where('status', '1');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_bank] = $row;
            }
            return $ret;
        }
    }
    public function getJumlahJenisSoal($id_bank)
    {
        $this->db->select('id_soal, jenis');
        $this->db->from('cbt_soal');
        $this->db->where('bank_id', $id_bank);
        $this->db->where('tampilkan', '1');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->jenis][] = $row;
            }
            return $ret;
        }
    }
    public function getJenis()
    {
        $this->datatables->select('*');
        $this->datatables->from('cbt_jenis');
        return $this->datatables->generate();
    }
    public function getJenisById($id)
    {
        $this->db->select('id_jenis, nama_jenis, kode_jenis');
        $this->db->from('cbt_jenis');
        $this->db->where(['id_jenis' => $id]);
        return $this->db->get()->row();
    }
    function updateJenis()
    {
        $id = $this->input->post('id_jenis');
        $name = $this->input->post('nama_jenis', true);
        $kode = $this->input->post('kode_jenis', true);
        $this->db->set('nama_jenis', $name);
        $this->db->set('kode_jenis', $kode);
        $this->db->where('id_jenis', $id);
        return $this->db->update('cbt_jenis');
    }
    public function getRuang()
    {
        $this->datatables->select('*, (SELECT COUNT(id_sesi) FROM cbt_sesi) AS jum_sesi');
        $this->datatables->from('cbt_ruang');
        return $this->datatables->generate();
    }
    public function getRuangById($id)
    {
        $this->db->select('id_ruang, nama_ruang, kode_ruang');
        $this->db->from('cbt_ruang');
        $this->db->where(['id_ruang' => $id]);
        return $this->db->get()->row();
    }
    public function getRuangSesi($tp, $smt)
    {
        $this->db->select('a.siswa_id, a.sesi_id, a.ruang_id, a.kelas_id, ' . 'b.nama_ruang, b.kode_ruang, c.nama_sesi, c.kode_sesi, d.nama_kelas');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('cbt_ruang b', 'b.id_ruang=a.ruang_id');
        $this->db->join('cbt_sesi c', 'c.id_sesi=a.sesi_id');
        $this->db->join('master_kelas d', 'd.id_kelas=a.kelas_id');
        $this->db->order_by('b.nama_ruang', 'ASC');
        $this->db->order_by('c.nama_sesi', 'ASC');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->sesi_id][$row->ruang_id][$row->kelas_id] = $row->nama_kelas;
            }
            return $ret;
        }
    }
    function updateRuang()
    {
        $id = $this->input->post('id_ruang');
        $name = $this->input->post('nama_ruang', true);
        $kode = $this->input->post('kode_ruang', true);
        $this->db->set('nama_ruang', $name);
        $this->db->set('kode_ruang', $kode);
        $this->db->where('id_ruang', $id);
        return $this->db->update('cbt_ruang');
    }
    public function getSesi()
    {
        $this->datatables->select('*');
        $this->datatables->from('cbt_sesi c');
        return $this->datatables->generate();
    }
    public function getAllKodeSesi()
    {
        $this->db->select('id_sesi, nama_sesi, kode_sesi, waktu_mulai, waktu_akhir');
        $this->db->from('cbt_sesi');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->kode_sesi] = $row;
            }
            return $ret;
        }
    }
    public function getSesiById($id)
    {
        $this->db->select('id_sesi, nama_sesi, kode_sesi, waktu_mulai, waktu_akhir');
        $this->db->from('cbt_sesi');
        $this->db->where(['id_sesi' => $id]);
        return $this->db->get()->row();
    }
    public function getSesiBySiswa($siswa_id)
    {
        $this->db->where('siswa_id', $siswa_id);
        $query = $this->db->get('siswa_sesi')->result();
        return $query;
    }
    function updateSesi()
    {
        $id = $this->input->post('id_sesi');
        $name = $this->input->post('nama_sesi', true);
        $kode = $this->input->post('kode_sesi', true);
        $mulai = $this->input->post('waktu_mulai', true);
        $akhir = $this->input->post('waktu_akhir', true);
        $this->db->set('nama_sesi', $name);
        $this->db->set('kode_sesi', $kode);
        $this->db->set('waktu_mulai', $mulai);
        $this->db->set('waktu_akhir', $akhir);
        $this->db->set('aktif', 1);
        $this->db->where('id_sesi', $id);
        return $this->db->update('cbt_sesi');
    }
    public function getSiswaCbtInfo($id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('a.id_kelas_siswa, a.id_tp, a.id_smt, a.id_siswa, a.id_kelas,' . ' b.siswa_id, b.kelas_id, b.ruang_id, b.sesi_id,' . ' rk.id_ruang, rk.nama_ruang, rk.kode_ruang,' . ' sk.id_sesi, sk.nama_sesi, sk.kode_sesi, sk.waktu_mulai, sk.waktu_akhir');
        $this->db->from('kelas_siswa a');
        $this->db->join('cbt_sesi_siswa b', 'a.id_siswa=b.siswa_id', 'left');
        $this->db->join('cbt_ruang rk', 'b.ruang_id=rk.id_ruang', 'left');
        $this->db->join('cbt_sesi sk', 'b.sesi_id=sk.id_sesi', 'left');
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->row();
    }
    public function getRuangSesiSiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.id_siswa, a.id_kelas,' . ' b.nama, b.nis, b.username,' . ' c.nama_kelas, c.kode_kelas,' . ' e.sesi_id, e.ruang_id,' . ' rk.id_ruang, rk.kode_ruang,' . ' sk.id_sesi, sk.kode_sesi');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->join('cbt_sesi_siswa e', 'a.id_siswa=e.siswa_id', 'left');
        $this->db->join('cbt_ruang rk', 'e.ruang_id=rk.id_ruang', 'left');
        $this->db->join('cbt_sesi sk', 'e.sesi_id=sk.id_sesi', 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->order_by('b.nama', 'ASC');
        return $this->db->get()->result();
    }
    public function getSiswaByKelas($id_tp, $id_smt, $id_kelas)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password,' . ' b.foto, d.kode_ruang, e.kode_sesi, f.nama_kelas, f.kode_kelas, g.nomor_peserta');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->join('cbt_sesi_siswa c', 'c.siswa_id=a.id_siswa', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=c.ruang_id', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=c.sesi_id', 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->where('b.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->where('c.siswa_id is NOT NULL', NULL, FALSE);
        $this->db->where('f.siswa_id is NOT NULL', NULL, FALSE);
        $this->db->where('g.id_siswa is NOT NULL', NULL, FALSE);
        if (is_array($id_kelas)) {
            $this->db->where_in('a.id_kelas', $id_kelas);
            $this->db->order_by('b.nama', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->order_by('b.nama', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getSiswaById($id_tp, $id_smt, $idSiswa)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password,' . ' b.foto, d.kode_ruang, e.kode_sesi, f.nama_kelas, f.kode_kelas, g.nomor_peserta,' . ' h.set_siswa, i.kode_ruang as ruang_kelas, j.kode_sesi as sesi_kelas');
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
    public function getAllPesertaByRuang($id_tp, $id_smt)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto, f.level_id,' . ' f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
        $this->db->order_by('d.kode_ruang');
        $this->db->order_by('e.kode_sesi');
        $this->db->order_by('f.level_id');
        $this->db->order_by('f.kode_kelas');
        $this->db->order_by('b.nama');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kode_ruang][$row->kode_sesi][] = $row;
        }
        return $ret;
    }
    public function getAllPesertaByKelas($id_tp, $id_smt)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto,' . ' f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
        $this->db->order_by('f.level_id');
        $this->db->order_by('f.kode_kelas');
        $this->db->order_by('b.nama');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kode_kelas][] = $row;
        }
        return $ret;
    }
    public function getSiswaByRuang($id_tp, $id_smt, $id_ruang, $sesi, $level = null)
    {
        $this->db->select('a.ruang_id, a.sesi_id, b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto,' . ' f.id_kelas, f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        if ($level === null) {
            $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
            $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
            $this->db->where('a.ruang_id', $id_ruang);
            $this->db->where('a.sesi_id', $sesi);
            $this->db->order_by('b.nama');
            return $this->db->get()->result();
        } else {
            $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas' . ' AND f.level_id=' . $level . '');
            $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
            $this->db->where('a.ruang_id', $id_ruang);
            $this->db->where('a.sesi_id', $sesi);
            $this->db->order_by('b.nama');
            return $this->db->get()->result();
        }
    }
    public function getRuangSiswaByKelas($id_tp, $id_smt, $kelas, $sesi)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto,' . ' f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
        $this->db->where_in('a.kelas_id', $kelas);
        if (!($sesi != null)) {
            $this->db->order_by('b.nama');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.sesi_id', $sesi);
            $this->db->order_by('b.nama');
            return $this->db->get()->result();
        }
    }
    public function getSiswaByKelasArray($id_tp, $id_smt, $arr_kelas)
    {
        $this->db->select('a.id_siswa, a.id_kelas,' . ' b.nama, b.nis, b.nisn, b.username, b.password,' . ' f.nama_kelas, f.kode_kelas, l.level, g.nomor_peserta');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        $this->db->join('level_kelas l', 'l.id_level=f.level_id');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        if (in_array('Semua', $arr_kelas)) {
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            $this->db->order_by('l.level', 'ASC');
            $this->db->order_by('f.kode_kelas', 'ASC');
            $this->db->order_by('b.nama', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where_in('a.id_kelas', $arr_kelas);
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            $this->db->order_by('l.level', 'ASC');
            $this->db->order_by('f.kode_kelas', 'ASC');
            $this->db->order_by('b.nama', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getKelasList($tp, $smt)
    {
        $this->db->select('a.id_kelas, a.nama_kelas, a.kode_kelas, c.nama_jurusan, b.id_ruang, b.id_sesi, b.set_siswa');
        $this->db->from('master_kelas a');
        $this->db->join('cbt_kelas_ruang b', 'a.id_kelas=b.id_kelas', 'left');
        $this->db->join('master_jurusan c', 'c.id_jurusan=a.jurusan_id', 'left');
        $this->db->join('level_kelas d', 'd.id_level=a.level_id', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('a.level_id', 'ASC');
        $this->db->order_by('a.nama_kelas', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    public function getKelas($tp = null, $smt = null)
    {
        $this->db->select('a.id_kelas, a.nama_kelas, a.kode_kelas, b.level');
        $this->db->from('master_kelas a');
        $this->db->join('level_kelas b', 'b.id_level=a.level_id', 'left');
        if (!($tp != null)) {
            if (!($smt != null)) {
            }
            $this->db->where('a.id_smt', $smt);
            $this->db->order_by('a.nama_kelas', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_tp', $tp);
            if (!($smt != null)) {
            }
            $this->db->where('a.id_smt', $smt);
            $this->db->order_by('a.nama_kelas', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getDataTableBank($guru = null)
    {
        $this->datatables->select('a.id_bank, a.bank_kode, a.bank_level, a.tampil_pg, a.tampil_esai, a.status, b.nama_mapel, c.nama_guru');
        $this->datatables->from('cbt_bank_soal a');
        $this->datatables->join('master_mapel b', 'b.id_mapel=a.bank_mapel_id', 'left');
        $this->datatables->join('master_guru c', 'c.id_guru=a.bank_guru_id', 'left');
        $this->datatables->join('master_jurusan d', 'd.id_jurusan=a.bank_jurusan_id', 'left');
        $this->datatables->join('cbt_jenis e', 'e.id_jenis=a.bank_jenis_id', 'left');
        if (!($guru !== null)) {
            return $this->datatables->generate();
        } else {
            $this->datatables->where('a.bank_guru_id', $guru);
            return $this->datatables->generate();
        }
    }
    public function getDataBank($guru = null, $mapel = null, $level = null)
    {
        $this->db->select('a.id_bank, a.id_tp, a.id_smt, a.bank_kode, a.bank_level, a.bank_kelas, a.date, a.status,' . ' a.tampil_pg, a.tampil_kompleks, a.tampil_jodohkan, a.tampil_isian, a.tampil_esai, a.bank_guru_id,' . ' b.nama_mapel, c.id_guru, c.nama_guru,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal,' . ' (SELECT COUNT(id_jadwal) FROM cbt_jadwal WHERE cbt_jadwal.id_bank = a.id_bank AND cbt_jadwal.status="1") AS digunakan');
        $this->db->from('cbt_bank_soal a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.bank_mapel_id', 'left');
        $this->db->join('master_guru c', 'c.id_guru=a.bank_guru_id', 'left');
        if (!($guru !== null)) {
            if (!($mapel !== null)) {
            }
            $this->db->where('a.bank_mapel_id', $mapel);
            if (!($level !== null)) {
            }
            $this->db->where('a.bank_level', $level);
            $this->db->order_by('a.bank_level', 'ASC');
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $row) {
                $ret[$row->id_tp][$row->id_smt][] = $row;
            }
            return $ret;
        } else {
            $this->db->where('a.bank_guru_id', $guru);
            if (!($mapel !== null)) {
            }
            $this->db->where('a.bank_mapel_id', $mapel);
            if (!($level !== null)) {
            }
            $this->db->where('a.bank_level', $level);
            $this->db->order_by('a.bank_level', 'ASC');
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $row) {
                $ret[$row->id_tp][$row->id_smt][] = $row;
            }
            return $ret;
        }
    }
    public function getDataBankById($id)
    {
        $this->db->select('a.*, b.nama_mapel, b.kode, c.nama_guru, d.nama_jurusan, d.kode_jurusan,' . ' (SELECT COUNT(id_jadwal) FROM cbt_jadwal WHERE cbt_jadwal.id_bank = a.id_bank AND cbt_jadwal.status="1") AS digunakan');
        $this->db->from('cbt_bank_soal a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.bank_mapel_id', 'left');
        $this->db->join('master_guru c', 'c.id_guru=a.bank_guru_id', 'left');
        $this->db->join('master_jurusan d', 'd.id_jurusan=a.bank_jurusan_id', 'left');
        $this->db->where('a.id_bank', $id);
        return $this->db->get()->row();
    }
    public function getTotalSoal($id_bank, $jenis = null)
    {
        $this->db->where('bank_id', $id_bank);
        if (!($jenis != null)) {
            return $this->db->get('cbt_soal')->num_rows();
        } else {
            $this->db->where('jenis', $jenis);
            return $this->db->get('cbt_soal')->num_rows();
        }
    }
    public function getNomorSoalById($id_soal)
    {
        $this->db->select('nomor_soal, jenis, bank_id');
        $this->db->where('id_soal', $id_soal);
        return $this->db->get('cbt_soal')->row();
    }
    public function getFileSoalById($id_soal)
    {
        $this->db->select('file');
        $this->db->where('id_soal', $id_soal);
        return $this->db->get('cbt_soal')->row();
    }
    public function getSoalByBank($id_bank)
    {
        $this->db->select('id_soal, bank_id, mapel_id, jenis, nomor_soal, soal, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban');
        $this->db->from('cbt_soal');
        $this->db->where('bank_id', $id_bank);
        $this->db->order_by('jenis');
        $this->db->order_by('nomor_soal');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->jenis][$row->nomor_soal] = $row;
        }
        return $ret;
    }
    public function getAllSoalByBank($id_bank, $jenis = null)
    {
        $this->db->select('id_soal, bank_id, mapel_id, jenis, nomor_soal, soal, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban, tampilkan');
        $this->db->where('bank_id', $id_bank);
        if (!($jenis != null)) {
            return $this->db->get('cbt_soal')->result();
        } else {
            $this->db->where('jenis', $jenis);
            return $this->db->get('cbt_soal')->result();
        }
    }
    public function getSoalByNomor($id_bank, $nomor, $jenis)
    {
        $this->db->select('*');
        $this->db->where('bank_id', $id_bank);
        $this->db->where('nomor_soal', $nomor);
        $this->db->where('jenis', $jenis);
        return $this->db->get('cbt_soal')->row();
    }
    public function getNomorSoalByBankJenis($id_bank, $jenis)
    {
        $this->db->select('id_soal, jenis, nomor_soal');
        $this->db->where('bank_id', $id_bank);
        $this->db->where('jenis', $jenis);
        $result = $this->db->get('cbt_soal')->result();
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->nomor_soal] = $row;
        }
        return $ret;
    }
    public function getNomorSoalByBank($id_bank, $jenis = null)
    {
        $this->db->select('id_soal, jenis, nomor_soal, jawaban');
        $this->db->where('bank_id', $id_bank);
        $this->db->where('tampilkan', '1');
        if (!($jenis != null)) {
            $result = $this->db->get('cbt_soal')->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_soal] = $row;
            }
            return $ret;
        } else {
            $this->db->where('jenis', $jenis);
            $result = $this->db->get('cbt_soal')->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_soal] = $row;
            }
            return $ret;
        }
    }
    public function getNomorSoalByArrIdBank($arr_id_bank, $jenis = null)
    {
        $this->db->select('id_soal, jenis, nomor_soal, jawaban');
        $this->db->where_in('bank_id', $arr_id_bank);
        if (!($jenis != null)) {
            return $this->db->get('cbt_soal')->result();
        } else {
            $this->db->where('jenis', $jenis);
            return $this->db->get('cbt_soal')->result();
        }
    }
    public function cekSoalAda($id_bank, $jenis)
    {
        $this->db->select('id_soal, bank_id, jenis, nomor_soal');
        $this->db->where('bank_id', $id_bank);
        $this->db->where('jenis', $jenis);
        return $this->db->get('cbt_soal')->result();
    }
    public function cekSoalKomplit($id_bank, $jenjang)
    {
        $this->db->select('id_soal, bank_id, jenis, nomor_soal');
        $this->db->where('bank_id', $id_bank)->where('soal NOT NULL')->or_where('opsi_a NOT NULL')->or_where('opsi_b NOT NULL')->or_where('opsi_c NOT NULL')->or_where('opsi_d NOT NULL')->or_where('jawaban NOT NULL');
        if (!($jenjang == '3')) {
            return $this->db->get('cbt_soal')->result();
        } else {
            $this->db->or_where('opsi_e NOT NULL');
            return $this->db->get('cbt_soal')->result();
        }
    }
    public function cekSoalBelumKomplit($jenis, $opsi_ganda)
    {
        $this->db->select('id_soal, bank_id, jenis, nomor_soal, mapel_id');
        $this->db->from('cbt_soal');
        $this->db->where('jenis', $jenis);
        $this->db->where('soal IS NULL')->or_where('soal =""');
        if (!($jenis == '1')) {
            if (!($jenis == '2')) {
            }
            $this->db->where('opsi_a IS NULL')->or_where('opsi_a =""');
            $this->db->where('jawaban IS NULL')->or_where('jawaban =""');
            $ret = [];
            $result = $this->db->get()->result();
            foreach ($result as $key => $row) {
                $ret[$row->bank_id][] = $row;
            }
            return $ret;
        } else {
            $this->db->where('opsi_a IS NULL')->or_where('opsi_a =""');
            $this->db->where('opsi_b IS NULL')->or_where('opsi_b =""');
            $this->db->where('opsi_c IS NULL')->or_where('opsi_c =""');
            if (!($opsi_ganda == '4')) {
            }
            $this->db->where('opsi_d IS NULL')->or_where('opsi_d =""');
            if (!($opsi_ganda == '5')) {
            }
            $this->db->where('opsi_d IS NULL')->or_where('opsi_d =""');
            $this->db->where('opsi_e IS NULL')->or_where('opsi_e =""');
            if (!($jenis == '2')) {
            }
            $this->db->where('opsi_a IS NULL')->or_where('opsi_a =""');
            $this->db->where('jawaban IS NULL')->or_where('jawaban =""');
            $ret = [];
            $result = $this->db->get()->result();
            foreach ($result as $key => $row) {
                $ret[$row->bank_id][] = $row;
            }
            return $ret;
        }
    }
    public function getNomorSoalTerbesar($id_bank, $jenis)
    {
        $this->db->select('nomor_soal');
        $this->db->where('bank_id', $id_bank)->where('jenis', $jenis);
        $this->db->order_by('nomor_soal', 'DESC');
        return $this->db->get('cbt_soal')->row();
    }
    public function dummy($jenjang)
    {
        $data = array('id_bank' => '', 'bank_jenis_id' => '', 'bank_kode' => '', 'bank_mapel_id' => '', 'bank_level' => '', 'bank_kelas' => serialize([]), 'bank_guru_id' => '', 'jml_soal' => '0', 'bobot_pg' => '0', 'tampil_pg' => '0', 'opsi' => $jenjang == '1' ? '3' : ($jenjang == '2' ? '4' : ($jenjang == '3' ? '5' : '')), 'jml_kompleks' => '0', 'tampil_kompleks' => '0', 'bobot_kompleks' => '0', 'jml_jodohkan' => '0', 'tampil_jodohkan' => '0', 'bobot_jodohkan' => '0', 'jml_isian' => '0', 'tampil_isian' => '0', 'bobot_isian' => '0', 'jml_esai' => '0', 'bobot_esai' => '0', 'tampil_esai' => '0', 'kkm' => '', 'soal_agama' => '-', 'status' => '1');
        return $data;
    }
    public function saveBankSoal($tp, $smt)
    {
        $id = $this->input->post('id_bank', true);
        $rows = count($this->input->post('kelas', true));
        $kelas = [];
        $i = 0;
        if (!($i <= $rows)) {
            $jumlah = serialize($kelas);
            $data = array('id_tp' => $tp, 'id_smt' => $smt, 'bank_kode' => strip_tags($this->input->post('kode', TRUE) ?? ''), 'bank_jenis_id' => strip_tags($this->input->post('jenis', TRUE) ?? ''), 'bank_mapel_id' => strip_tags($this->input->post('mapel', TRUE) ?? ''), 'bank_kelas' => $jumlah, 'bank_level' => $this->input->post('level', TRUE), 'bank_guru_id' => strip_tags($this->input->post('guru', TRUE) ?? ''), 'jml_soal' => strip_tags($this->input->post('tampil_pg', TRUE) ?? ''), 'tampil_pg' => strip_tags($this->input->post('tampil_pg', TRUE) ?? ''), 'bobot_pg' => strip_tags($this->input->post('bobot_pg', TRUE) ?? ''), 'opsi' => strip_tags($this->input->post('opsi', TRUE) ?? ''), 'jml_kompleks' => strip_tags($this->input->post('tampil_kompleks', TRUE) ?? ''), 'tampil_kompleks' => strip_tags($this->input->post('tampil_kompleks', TRUE) ?? ''), 'bobot_kompleks' => strip_tags($this->input->post('bobot_kompleks', TRUE) ?? ''), 'jml_jodohkan' => strip_tags($this->input->post('tampil_jodohkan', TRUE) ?? ''), 'tampil_jodohkan' => strip_tags($this->input->post('tampil_jodohkan', TRUE) ?? ''), 'bobot_jodohkan' => strip_tags($this->input->post('bobot_jodohkan', TRUE) ?? ''), 'jml_isian' => strip_tags($this->input->post('tampil_isian', TRUE) ?? ''), 'tampil_isian' => strip_tags($this->input->post('tampil_isian', TRUE) ?? ''), 'bobot_isian' => strip_tags($this->input->post('bobot_isian', TRUE) ?? ''), 'jml_esai' => strip_tags($this->input->post('bobot_esai', TRUE) ?? ''), 'bobot_esai' => strip_tags($this->input->post('bobot_esai', TRUE) ?? ''), 'tampil_esai' => strip_tags($this->input->post('tampil_esai', TRUE) ?? ''), 'status' => strip_tags($this->input->post('status', TRUE) ?? ''), 'soal_agama' => strip_tags($this->input->post('soal_agama', TRUE) ?? ''));
            if (!$id) {
            }
            $this->db->where('id_bank', $id);
            return $this->db->update('cbt_bank_soal', $data);
        } else {
            $kelas[] = ['kelas_id' => $this->input->post('kelas[' . $i . ']', true)];
            $i++;
            if (!($i <= $rows)) {
            }
        }
    }
    public function dummyJadwal()
    {
        return array('id_bank' => '', 'id_jadwal' => '', 'id_jenis' => '', 'tgl_mulai' => '', 'tgl_selesai' => '', 'durasi_ujian' => '', 'bank_kelas' => serialize([]), 'acak_soal' => '', 'acak_opsi' => '', 'hasil_tampil' => '', 'token' => '', 'status' => '', 'ulang' => '', 'jarak' => '', 'reset_login' => '');
    }
    public function getDistinctJenisJadwal($tp, $smt)
    {
        $this->db->select('id_jenis');
        $this->db->distinct();
        $this->db->from('cbt_jadwal');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        return $this->db->get()->result();
    }
    public function getDataJadwal($tp, $smt, $guru = null, $rekap = null)
    {
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai,' . ' a.tgl_selesai, a.status, a.ulang, a.reset_login, a.rekap, a.jam_ke,' . ' e.id_tp, e.tahun, f.id_smt, f.nama_smt, g.level, b.bank_kode, b.bank_level, b.bank_kelas,' . ' c.kode_jenis, d.kode, d.nama_mapel,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai, b.bank_guru_id,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt');
        $this->db->join('level_kelas g', 'b.bank_level=g.id_level');
        if (!($guru !== null)) {
            if (!($rekap !== null)) {
            }
            $this->db->where('a.rekap', $rekap);
            $this->db->order_by('a.tgl_mulai', 'DESC');
            $this->db->order_by('b.bank_level', 'ASC');
            $query = $this->db->get()->result();
            return $query;
        } else {
            $this->db->where('b.bank_guru_id', $guru);
            if (!($rekap !== null)) {
            }
            $this->db->where('a.rekap', $rekap);
            $this->db->order_by('a.tgl_mulai', 'DESC');
            $this->db->order_by('b.bank_level', 'ASC');
            $query = $this->db->get()->result();
            return $query;
        }
    }
    public function getAllDataJadwal($guru = null, $mapel = null, $level = null)
    {
        $this->db->select('a.id_jadwal, a.tgl_mulai, a.tgl_selesai, a.status, a.durasi_ujian, a.acak_soal,' . ' a.acak_opsi, a.id_bank, a.id_jenis, a.hasil_tampil, a.status, a.ulang, a.reset_login, a.rekap,' . ' a.jam_ke, a.token, e.tahun, f.nama_smt, g.level, b.bank_kode, b.bank_level, b.bank_kelas, c.kode_jenis, d.kode, d.nama_mapel,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai, b.bank_guru_id,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt');
        $this->db->join('level_kelas g', 'b.bank_level=g.id_level');
        if (!($guru !== null)) {
            if (!($mapel !== null)) {
            }
            $this->db->where('b.bank_mapel_id', $mapel);
            if (!($level !== null)) {
            }
            $this->db->where('b.bank_level', $level);
            $this->db->order_by('b.bank_level', 'ASC');
            $this->db->order_by('a.id_tp', 'DESC');
            $this->db->order_by('a.id_smt', 'DESC');
            $this->db->order_by('a.tgl_mulai', 'ASC');
            $query = $this->db->get()->result();
            $ret = [];
            foreach ($query as $key => $row) {
                $ret['<b>' . $row->kode_jenis . '</b>  ' . $row->tahun . ' smt ' . $row->nama_smt][$row->level][] = $row;
            }
            return $ret;
        } else {
            $this->db->where('b.bank_guru_id', $guru);
            if (!($mapel !== null)) {
            }
            $this->db->where('b.bank_mapel_id', $mapel);
            if (!($level !== null)) {
            }
            $this->db->where('b.bank_level', $level);
            $this->db->order_by('b.bank_level', 'ASC');
            $this->db->order_by('a.id_tp', 'DESC');
            $this->db->order_by('a.id_smt', 'DESC');
            $this->db->order_by('a.tgl_mulai', 'ASC');
            $query = $this->db->get()->result();
            $ret = [];
            foreach ($query as $key => $row) {
                $ret['<b>' . $row->kode_jenis . '</b>  ' . $row->tahun . ' smt ' . $row->nama_smt][$row->level][] = $row;
            }
            return $ret;
        }
    }
    public function getJadwalTerpakai($id_jadwal = null)
    {
        $this->db->select('id_bank,id_jadwal,id_siswa');
        $this->db->from('cbt_soal_siswa');
        if (!($id_jadwal != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal][$row->id_siswa] = $row;
            }
            return $ret;
        } else {
            $this->db->where('id_jadwal', $id_jadwal);
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal][$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function getBankTerpakai($id_banks = null)
    {
        $this->db->select('id_bank,id_soal,id_siswa');
        $this->db->from('cbt_soal_siswa');
        if (!($id_banks != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_bank][$row->id_siswa] = $row;
            }
            return $ret;
        } else {
            $this->db->where_in('id_bank', $id_banks);
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_bank][$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function getCountBankTerpakai($id_bank = null)
    {
        $this->db->select('id_bank,COUNT(id_siswa) as siswa');
        $this->db->from('cbt_soal_siswa');
        if (!($id_bank != null)) {
            $this->db->group_by('id_bank');
            return $this->db->get()->result();
        } else {
            $this->db->where('id_bank', $id_bank);
            $this->db->group_by('id_bank');
            return $this->db->get()->result();
        }
    }
    public function getRekapByJadwalKelas($jadwal, $guru = null)
    {
        $this->db->from('cbt_rekap');
        $this->db->where('id_jadwal', $jadwal);
        if (!($guru !== null)) {
            return $this->db->get()->row();
        } else {
            $this->db->where('id_guru', $guru);
            return $this->db->get()->row();
        }
    }
    public function getRekapJadwal($guru = null)
    {
        $this->db->select('*');
        $this->db->from('cbt_rekap');
        if (!($guru !== null)) {
            $this->db->order_by('tgl_mulai', 'DESC');
            $query = $this->db->get();
            return $query->result();
        } else {
            $this->db->where('id_guru', $guru);
            $this->db->order_by('tgl_mulai', 'DESC');
            $query = $this->db->get();
            return $query->result();
        }
    }
    public function getAllRekapByJenis($tp, $smt, $jenis, $level, $mapel, $jadwal = null, $guru = null)
    {
        $this->db->from('cbt_rekap');
        if (!($mapel != '0')) {
            if (!($jadwal != null)) {
            }
            $this->db->where('id_jadwal', $jadwal);
            if (!($guru != null)) {
            }
            $this->db->where('id_guru', $guru);
            $this->db->where('tp', $tp);
            $this->db->where('smt', $smt);
            $this->db->where('kode_jenis', $jenis);
            $this->db->where('bank_level', $level);
            $this->db->order_by('id_mapel', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('id_mapel', $mapel);
            if (!($jadwal != null)) {
            }
            $this->db->where('id_jadwal', $jadwal);
            if (!($guru != null)) {
            }
            $this->db->where('id_guru', $guru);
            $this->db->where('tp', $tp);
            $this->db->where('smt', $smt);
            $this->db->where('kode_jenis', $jenis);
            $this->db->where('bank_level', $level);
            $this->db->order_by('id_mapel', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getAllRekapByJadwal($tp, $smt, $jenis, $level, $jadwal, $guru = null)
    {
        $this->db->from('cbt_rekap');
        if (!($jadwal != '0')) {
            if (!($guru != null)) {
            }
            $this->db->where('id_guru', $guru);
            $this->db->where('tp', $tp);
            $this->db->where('smt', $smt);
            $this->db->where('kode_jenis', $jenis);
            $this->db->where('bank_level', $level);
            $this->db->order_by('id_mapel', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('id_jadwal', $jadwal);
            if (!($guru != null)) {
            }
            $this->db->where('id_guru', $guru);
            $this->db->where('tp', $tp);
            $this->db->where('smt', $smt);
            $this->db->where('kode_jenis', $jenis);
            $this->db->where('bank_level', $level);
            $this->db->order_by('id_mapel', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getAllNilaiRekapByJenis($tp, $smt, $kode_jenis, $id_kelas, $id_mapel, $id_jadwal = null, $id_guru = null)
    {
        $this->db->select('a.*, b.nomor_peserta, c.nama');
        $this->db->from('cbt_rekap_nilai a');
        $this->db->join('cbt_nomor_peserta b', 'b.id_siswa=a.id_siswa AND b.id_tp=a.id_tp', 'left');
        $this->db->join('master_siswa c', 'c.id_siswa=a.id_siswa', 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        if (!($id_mapel != '0')) {
            if (!($id_jadwal != null)) {
            }
            $this->db->where('a.id_jadwal', $id_jadwal);
            if (!($id_guru != null)) {
            }
            $this->db->where('a.id_guru', $id_guru);
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->where('a.tp', $tp);
            $this->db->where('a.smt', $smt);
            $this->db->where('a.kode_jenis', $kode_jenis);
            $this->db->order_by('c.nama', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_mapel', $id_mapel);
            if (!($id_jadwal != null)) {
            }
            $this->db->where('a.id_jadwal', $id_jadwal);
            if (!($id_guru != null)) {
            }
            $this->db->where('a.id_guru', $id_guru);
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->where('a.tp', $tp);
            $this->db->where('a.smt', $smt);
            $this->db->where('a.kode_jenis', $kode_jenis);
            $this->db->order_by('c.nama', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getAllNilaiRekapByJadwal($tp, $smt, $kode_jenis, $id_kelas, $id_jadwal, $id_guru = null)
    {
        $this->db->select('a.*, b.nomor_peserta, c.nama');
        $this->db->from('cbt_rekap_nilai a');
        $this->db->join('cbt_nomor_peserta b', 'b.id_siswa=a.id_siswa AND b.id_tp=a.id_tp', 'left');
        $this->db->join('master_siswa c', 'c.id_siswa=a.id_siswa', 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        if (!($id_jadwal != '0')) {
            if (!($id_guru != null)) {
            }
            $this->db->where('a.id_guru', $id_guru);
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->where('a.tp', $tp);
            $this->db->where('a.smt', $smt);
            $this->db->where('a.kode_jenis', $kode_jenis);
            $this->db->order_by('c.nama', 'ASC');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_jadwal', $id_jadwal);
            if (!($id_guru != null)) {
            }
            $this->db->where('a.id_guru', $id_guru);
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->where('a.tp', $tp);
            $this->db->where('a.smt', $smt);
            $this->db->where('a.kode_jenis', $kode_jenis);
            $this->db->order_by('c.nama', 'ASC');
            return $this->db->get()->result();
        }
    }
    public function getAllRekap($guru = null)
    {
        $this->db->select('id_rekap, id_tp, tp, id_smt, smt, id_jadwal, id_jenis, kode_jenis, id_bank, bank_kelas, nama_kelas, bank_kode, bank_level, id_mapel, nama_mapel, kode, tgl_mulai, tgl_selesai, id_guru, nama_guru');
        $this->db->from('cbt_rekap');
        if (!($guru != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row;
            }
            return $ret;
        } else {
            $this->db->where('id_guru', $guru);
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row;
            }
            return $ret;
        }
    }
    public function getJadwalById($id_jadwal, $sesi = null)
    {
        $this->db->select('a.*, b.opsi, b.bank_kode, b.bank_level, b.bank_kelas,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai,' . ' b.bobot_pg, b.bobot_kompleks, b.bobot_jodohkan, b.bobot_isian, b.bobot_esai,' . ' b.id_bank, b.bank_guru_id, c.kode_jenis, c.nama_jenis,' . ' d.id_mapel, d.kode, d.nama_mapel, f.id_guru, f.nama_guru');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        if (!($sesi != null)) {
            $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
            $this->db->where('a.id_jadwal', $id_jadwal);
            return $this->db->get()->row();
        } else {
            $this->db->join('cbt_sesi e', 'e.id_sesi=' . $sesi, 'left');
            $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
            $this->db->where('a.id_jadwal', $id_jadwal);
            return $this->db->get()->row();
        }
    }
    public function getJadwalByIdBank($id_bank)
    {
        $this->db->select('a.*, b.opsi, b.bank_kode, b.bank_level, b.bank_kelas,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai,' . ' b.bobot_pg, b.bobot_kompleks, b.bobot_jodohkan, b.bobot_isian, b.bobot_esai,' . ' b.id_bank, b.bank_guru_id, c.kode_jenis, c.nama_jenis,' . ' d.id_mapel, d.kode, d.nama_mapel, f.id_guru, f.nama_guru');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
        $this->db->where('a.id_bank', $id_bank);
        return $this->db->get()->row();
    }
    public function getAllJadwal($tp, $smt, $id_guru = null)
    {
        $this->db->select('a.bank_kode, a.bank_kelas, b.id_jadwal');
        $this->db->from('cbt_bank_soal a');
        $this->db->join('cbt_jadwal b', 'b.id_bank=a.id_bank');
        if (!($id_guru != null)) {
            $this->db->where('b.id_tp', $tp);
            $this->db->where('b.id_smt', $smt);
            return $this->db->get()->result();
        } else {
            $this->db->where('a.bank_guru_id', $id_guru);
            $this->db->where('b.id_tp', $tp);
            $this->db->where('b.id_smt', $smt);
            return $this->db->get()->result();
        }
    }
    public function getJadwalByArrId($arr_id_jadwal, $sesi = null)
    {
        $this->db->select('a.*, b.opsi, b.bank_kode, b.bank_level, b.bank_kelas,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai,' . ' b.bobot_pg, b.bobot_kompleks, b.bobot_jodohkan, b.bobot_isian, b.bobot_esai,' . ' b.id_bank, b.bank_guru_id, c.kode_jenis, c.nama_jenis,' . ' d.id_mapel, d.kode, d.nama_mapel, f.id_guru, f.nama_guru');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        if (!($sesi != null)) {
            $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
            $this->db->where_in('a.id_jadwal', $arr_id_jadwal);
            return $this->db->get()->result();
        } else {
            $this->db->join('cbt_sesi e', 'e.id_sesi=' . $sesi, 'left');
            $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
            $this->db->where_in('a.id_jadwal', $arr_id_jadwal);
            return $this->db->get()->result();
        }
    }
    public function cekJadwalBankSoal($id_bank)
    {
        $this->db->select('id_bank');
        $this->db->from('cbt_jadwal');
        if (is_array($id_bank)) {
            $this->db->where_in('id_bank', $id_bank);
            return $this->db->get()->num_rows();
        } else {
            $this->db->where('id_bank', $id_bank);
            return $this->db->get()->num_rows();
        }
    }
    public function cekJadwalSudahMulai($id_jadwal)
    {
        return $this->get_where('cbt_durasi_siswa', 'id_jadwal', $id_jadwal)->num_rows();
    }
    public function saveJadwalUjian($id_tp, $id_smt)
    {
        $id = $this->input->post('id_jadwal', true);
        $acak_soal = $this->input->post('acak_soal', TRUE);
        $acak_opsi = $this->input->post('acak_opsi', TRUE);
        $hasil_tampil = $this->input->post('hasil_tampil', TRUE);
        $token = $this->input->post('token', TRUE);
        $status = $this->input->post('status', TRUE);
        $reset_login = $this->input->post('reset_login', TRUE);
        $bank_id = strip_tags($this->input->post('bank_id', TRUE) ?? '');
        $jenis_id = strip_tags($this->input->post('jenis_id', TRUE) ?? '');
        $mulai = strip_tags($this->input->post('tgl_mulai', TRUE) ?? '');
        $selesai = strip_tags($this->input->post('tgl_selesai', TRUE) ?? '');
        $durasi = strip_tags($this->input->post('durasi_ujian', TRUE) ?? '');
        $jarak = strip_tags($this->input->post('jarak', TRUE) ?? '');
        $check = $this->db->where('id_bank', $bank_id)->where('id_jenis', $jenis_id)->get('cbt_jadwal')->row();
        $data = array('id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_bank' => $bank_id, 'id_jenis' => $jenis_id, 'tgl_mulai' => $mulai, 'tgl_selesai' => $selesai, 'durasi_ujian' => $durasi, 'jarak' => $jarak, 'acak_soal' => !$acak_soal ? '0' : $acak_soal, 'acak_opsi' => !$acak_opsi ? '0' : $acak_opsi, 'hasil_tampil' => !$hasil_tampil ? '0' : $hasil_tampil, 'token' => !$token ? '0' : $token, 'status' => !$status ? '0' : $status, 'reset_login' => !$reset_login ? '0' : $reset_login);
        if ($id == '') {
            if ($check != null) {
            }
            $this->db->insert('cbt_jadwal', $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            if ($check != null && $check->id_jadwal != $id) {
            }
            $this->db->where('id_jadwal', $id);
            return $this->db->update('cbt_jadwal', $data);
        }
    }
    public function getJadwalTgl($guru = null)
    {
        $this->db->distinct();
        $this->db->select('tgl_mulai');
        $this->db->from('cbt_jadwal');
        $query = $this->db->get();
        return $query->result();
    }
    public function getDataJadwalByTgl($tgl)
    {
        $this->db->distinct();
        $this->db->select('tgl_mulai, tgl_selesai');
        $this->db->from('cbt_jadwal');
        $this->db->where("tgl_mulai <= '{$tgl}' AND tgl_selesai >= '{$tgl}'");
        $query = $this->db->get();
        return $query->result();
    }
    public function getDataGuru()
    {
        $this->db->select('a.id_guru, a.nama_guru, b.id_pengawas, b.id_jadwal');
        $this->db->from('master_guru a');
        $this->db->join('cbt_pengawas b', 'b.id_guru = a.id_guru', 'left');
        return $this->db->get()->result();
    }
    public function saveToken($post_token)
    {
        $id = isset($post_token->id_token) ? $post_token->id_token : false;
        $tkn = $post_token->token;
        $auto = $post_token->auto;
        $jarak = $post_token->jarak;
        $data = array('token' => $tkn, 'auto' => $auto, 'jarak' => $jarak, 'updated' => $post_token->updated);
        if (!$id) {
            $this->db->insert('cbt_token', $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            $this->db->where('id_token', $id);
            return $this->db->update('cbt_token', $data);
        }
    }
    public function updateToken($token, $auto)
    {
        $this->db->set('auto', $auto, FALSE);
        $this->db->where('token', $token);
        $this->db->update('cbt_token');
        return $this->db->get('cbt_token')->row();
    }
    public function getToken()
    {
        return $this->db->get('cbt_token')->row();
    }
    public function getJadwalCbtKelas($id_tp, $id_smt)
    {
        $this->db->select('a.id_jadwal, b.bank_kelas');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getInfoJadwal($id_bank)
    {
        $this->db->select('a.id_bank, b.acak_soal, b.acak_opsi, a.opsi,' . ' a.tampil_pg, a.tampil_kompleks, a.tampil_jodohkan, a.tampil_isian, a.tampil_esai,' . ' a.bobot_pg,  a.bobot_kompleks,  a.bobot_jodohkan,  a.bobot_isian,  a.bobot_esai');
        $this->db->from('cbt_bank_soal a');
        $this->db->join('cbt_jadwal b', 'a.id_bank=b.id_bank');
        $this->db->where('a.id_bank', $id_bank);
        return $this->db->get()->row();
    }
    public function getAllIdSoal($id_bank)
    {
        $this->db->select('id_soal, jenis, jawaban');
        $this->db->from('cbt_soal');
        $this->db->where('tampilkan', '1');
        $this->db->where('bank_id', $id_bank);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->jenis][] = $row;
            }
            return $ret;
        }
    }
    public function getJadwalCbt($id_tp, $id_smt, $level)
    {
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai,' . ' a.durasi_ujian, a.pengawas, a.acak_soal, a.acak_opsi, a.hasil_tampil, a.token, a.status, a.ulang,' . ' a.reset_login, a.rekap, a.jam_ke, a.jarak,' . ' c.bank_kode, c.bank_level, c.bank_kelas, c.tampil_pg, c.tampil_kompleks, c.tampil_jodohkan,' . ' c.tampil_isian, c.tampil_esai, c.soal_agama, ' . ' c.bobot_pg, c.bobot_kompleks, c.bobot_jodohkan, c.bobot_isian, c.bobot_esai, b.kode_jenis,' . ' b.nama_jenis, d.kode, d.nama_mapel');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.status', '1');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('c.status', '1');
        $this->db->where('c.status_soal', '1');
        $this->db->where('c.bank_level', $level);
        $this->db->order_by('a.jam_ke');
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_jadwal] = $row;
        }
        return $retur;
    }
    public function getJadwalByKelas($id_tp, $id_smt, $kelas)
    {
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai,' . ' a.durasi_ujian, a.pengawas, a.acak_soal, a.acak_opsi, a.hasil_tampil, a.token, a.status, a.ulang,' . ' a.reset_login, a.rekap, a.jam_ke, a.jarak,' . ' c.bank_kode, c.bank_level, c.bank_kelas, c.tampil_pg, c.tampil_kompleks, c.tampil_jodohkan,' . ' c.tampil_isian, c.tampil_esai, c.soal_agama, ' . ' c.bobot_pg, c.bobot_kompleks, c.bobot_jodohkan, c.bobot_isian, c.bobot_esai, b.kode_jenis,' . ' b.nama_jenis, d.kode, d.nama_mapel');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.status', '1');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('c.status', '1');
        $this->db->where('c.status_soal', '1');
        $this->db->like('c.bank_kelas', $kelas);
        $this->db->order_by('a.jam_ke');
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_jadwal] = $row;
        }
        return $retur;
    }
    public function getCbt($id_jadwal)
    {
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai,' . ' a.durasi_ujian, a.pengawas, a.acak_soal, a.acak_opsi, a.hasil_tampil, a.token, a.status, a.ulang,' . ' a.reset_login, a.rekap, a.jam_ke, a.jarak,' . ' b.nama_jenis, b.kode_jenis,' . ' c.bank_kode, c.bank_level, c.bank_kelas, c.bank_mapel_id, c.bank_jurusan_id,' . ' c.bank_guru_id, c.bank_nama, c.jml_soal, c.jml_esai, c.tampil_pg, c.tampil_esai, c.bobot_pg,' . ' c.bobot_esai, c.opsi, c.date, c.status, c.soal_agama, c.id_tp, c.id_smt, c.deskripsi, c.jml_kompleks,' . ' c.tampil_kompleks, c.bobot_kompleks, c.jml_jodohkan, c.tampil_jodohkan, c.bobot_jodohkan, c.jml_isian,' . ' c.tampil_isian, c.bobot_isian, c.status_soal,' . ' d.id_mapel, d.nama_mapel, d.kode,' . ' e.id_guru, e.nama_guru,' . ' f.id_jurusan, f.nama_jurusan, f.kode_jurusan,' . ' g.tahun,' . ' h.smt, h.nama_smt,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis', 'left');
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank', 'left');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id', 'left');
        $this->db->join('master_guru e', 'e.id_guru=c.bank_guru_id', 'left');
        $this->db->join('master_jurusan f', 'f.id_jurusan=c.bank_jurusan_id', 'left');
        $this->db->join('master_tp g', 'g.id_tp=a.id_tp', 'left');
        $this->db->join('master_smt h', 'h.id_smt=a.id_smt', 'left');
        $this->db->where('a.id_jadwal', $id_jadwal);
        return $this->db->get()->row();
    }
    public function getCbtById($id_jadwal)
    {
        $this->db->select('*');
        $this->db->from('cbt_jadwal');
        $this->db->where('id_jadwal', $id_jadwal);
        return $this->db->get()->row();
    }
    public function getIdRuangById($array)
    {
        $this->db->select('nama_ruang');
        $this->db->from('cbt_ruang');
        $this->db->where('id_ruang', $array);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_ruang] = $row->kode_ruang;
            }
            return $ret;
        }
    }
    public function getNamaRuangById($id)
    {
        $this->db->select('nama_ruang');
        $this->db->from('cbt_ruang');
        $this->db->where('id_ruang', $id);
        $result = $this->db->get()->row();
        if ($result) {
            return $result->nama_ruang;
        } else {
            return '';
        }
    }
    public function getNamaSesiById($id)
    {
        $this->db->select('nama_sesi');
        $this->db->from('cbt_sesi');
        $this->db->where(['id_sesi' => $id]);
        return $this->db->get()->row()->nama_sesi;
    }
    public function getNamaKelasById($id)
    {
        $this->db->select('nama_kelas');
        $this->db->from('master_kelas');
        $this->db->where(['id_kelas' => $id]);
        return $this->db->get()->row()->nama_kelas;
    }
    public function getNamaGuruById($id)
    {
        $this->db->select('nama_guru');
        $this->db->from('master_guru');
        $this->db->where('id_guru', $id);
        return $this->db->get()->row()->nama_guru;
    }
    public function getElapsed($id)
    {
        $this->db->select('id_durasi, id_siswa, id_jadwal, status, lama_ujian, mulai, selesai, reset');
        $this->db->from('cbt_durasi_siswa');
        $this->db->where('id_durasi', $id);
        return $this->db->get()->row();
    }
    public function getSoalSiswa($id_bank, $id_siswa)
    {
        $this->db->select('a.*, b.jenis, b.nomor_soal, b.jawaban');
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal', 'left');
        $this->db->where('a.id_bank', $id_bank);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->order_by('a.jenis_soal');
        $this->db->order_by('a.no_soal_alias');
        return $this->db->get()->result();
    }
    public function getJumlahSoalSiswa($id_bank, $id_siswa)
    {
        $this->db->select('id_soal_siswa');
        $this->db->from('cbt_soal_siswa');
        $this->db->where('id_bank', $id_bank);
        $this->db->where('id_siswa', $id_siswa);
        return $this->db->get()->num_rows();
    }
    public function getALLSoalSiswa($id_bank, $id_siswa)
    {
        $this->db->select('a.id_soal_siswa, a.id_bank, a.id_jadwal, a.id_soal, a.id_siswa, a.jenis_soal,' . ' a.no_soal_alias, a.opsi_alias_a, a.opsi_alias_b, a.opsi_alias_c, a.opsi_alias_d, a.opsi_alias_e,' . ' a.jawaban_alias, a.jawaban_siswa, a.jawaban_benar, a.point_essai, a.soal_end, a.point_soal,' . ' b.id_soal, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d,' . ' b.opsi_e, b.tampilkan');
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->where('a.id_bank', $id_bank);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->order_by('a.no_soal_alias');
        return $this->db->get()->result();
    }
    public function getJumlahJawaban($id_bank, $id_siswa)
    {
        $this->db->select('jawaban_siswa, id_siswa, id_bank');
        $this->db->from('cbt_soal_siswa');
        $this->db->where('id_bank', $id_bank);
        $this->db->where('id_siswa', $id_siswa);
        return $this->db->get()->result();
    }
    public function getSoalSiswaByJadwal($id_jadwal, $id_siswa)
    {
        $this->db->select('a.*, b.jenis, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d, b.opsi_e');
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->where('a.id_jadwal', $id_jadwal);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('b.tampilkan', '1');
        $this->db->order_by('a.jenis_soal');
        $this->db->order_by('b.nomor_soal');
        return $this->db->get()->result();
    }
    public function getSoalSiswaByNomor($id_soal_siswa)
    {
        $this->db->select('a.id_soal_siswa, a.id_bank, a.opsi_alias_a, a.opsi_alias_b, a.opsi_alias_c, a.opsi_alias_d,' . ' a.opsi_alias_e, a.no_soal_alias, a.jawaban_alias, a.soal_end, a.jawaban_siswa,' . ' b.id_soal, b.jenis, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d, b.opsi_e, b.tampilkan,' . ' c.tampil_pg, c.tampil_kompleks, c.tampil_jodohkan, c.tampil_isian, c.tampil_esai,');
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->join('cbt_bank_soal c', 'b.id_bank=a.id_bank');
        $this->db->where('a.id_soal_siswa', $id_soal_siswa);
        $this->db->order_by('a.no_soal_alias');
        return $this->db->get()->row();
    }
    public function getSettingKartu()
    {
        $this->db->select('*');
        $this->db->from('cbt_kop_kartu');
        return $this->db->get()->row();
    }
    public function getSettingKopAbsensi()
    {
        $this->db->select('a.*, b.logo_kanan, b.logo_kiri, b.kepsek, b.tanda_tangan');
        $this->db->from('cbt_kop_absensi a');
        $this->db->join('setting b', 'b.id_setting=1', 'left');
        return $this->db->get()->row();
    }
    public function getSettingKopBeritaAcara()
    {
        $this->db->select('a.*, d.logo_kanan, d.logo_kiri, d.kepsek, d.nip, d.tanda_tangan, d.sekolah');
        $this->db->from('cbt_kop_berita a');
        $this->db->join('setting d', 'd.id_setting=1', 'left');
        return $this->db->get()->row();
    }
    public function getDurasiSiswa($id)
    {
        return $this->db->get_where('cbt_durasi_siswa', 'id_durasi=' . $id)->row();
    }
    public function getFilterJawabanSiswa($jadwal, $arrIdSiswa)
    {
        $this->db->where('id_jadwal', $jadwal);
        $this->db->where_in('id_siswa', $arrIdSiswa);
        return $this->db->get('cbt_soal_siswa')->result();
    }
    public function getFilterDurasiSiswa($jadwal, $arrIdSiswa)
    {
        $this->db->where('id_jadwal', $jadwal);
        $result = $this->db->get_where('cbt_durasi_siswa')->result();
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_durasi] = $row;
        }
        return $ret;
    }
    public function getJawabanByBank($id_bank, $id_siswa = null)
    {
        $this->db->select('a.*, b.nomor_soal, b.jawaban');
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        if (!($id_siswa != null)) {
            $this->db->where('a.id_bank=', $id_bank);
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_siswa=', $id_siswa);
            $this->db->where('a.id_bank=', $id_bank);
            return $this->db->get()->result();
        }
    }
    public function getJawabanSiswa($id)
    {
        $this->db->select('id_soal_siswa, id_bank, id_jadwal, id_soal, id_siswa, jenis_soal, no_soal_alias, opsi_alias_a, opsi_alias_b, opsi_alias_c, opsi_alias_d, opsi_alias_e, jawaban_alias, jawaban_siswa, jawaban_benar, point_soal');
        $this->db->from('cbt_soal_siswa');
        $this->db->where('id_soal_siswa=', $id);
        return $this->db->get()->row();
    }
    public function getJawabanSiswaByJadwal($id_jadwal, $id_siswa = null)
    {
        $this->db->select('a.*, b.jenis, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d, b.opsi_e, b.tampilkan');
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        if (!($id_siswa != null)) {
            $this->db->where('a.id_jadwal=', $id_jadwal);
            $this->db->where('b.tampilkan', '1');
            $this->db->order_by('a.jenis_soal');
            $this->db->order_by('b.nomor_soal');
            return $this->db->get()->result();
        } else {
            if (is_array($id_siswa)) {
            }
            $this->db->where('a.id_siswa', $id_siswa);
            $this->db->where('a.id_jadwal=', $id_jadwal);
            $this->db->where('b.tampilkan', '1');
            $this->db->order_by('a.jenis_soal');
            $this->db->order_by('b.nomor_soal');
            return $this->db->get()->result();
        }
    }
    public function getIdSiswaFromJawabanByJadwal($id_jadwal)
    {
        $result = $this->db->get_where('cbt_soal_siswa', 'id_jadwal=' . $id_jadwal)->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_siswa][] = $row;
        }
        return $retur;
    }
    public function getDurasiSiswaByJadwal($id_jadwal, $id_siswa = null)
    {
        $this->db->select('id_durasi, id_siswa, id_jadwal, status, lama_ujian, mulai, selesai, reset');
        $this->db->from('cbt_durasi_siswa');
        $this->db->where('id_jadwal=', $id_jadwal);
        if (!($id_siswa != null)) {
            return $this->db->get()->result();
        } else {
            $this->db->where('id_siswa=', $id_siswa);
            return $this->db->get()->result();
        }
    }
    public function getIdSiswaFromDurasiByJadwal($id_jadwal)
    {
        $result = $this->db->get_where('cbt_durasi_siswa', 'id_jadwal=' . $id_jadwal)->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        return $retur;
    }
    public function getLogUjianByJadwal($id_jadwal)
    {
        $this->db->select('id_log, log_time, id_siswa, id_jadwal, log_type, log_desc, address, agent, device, reset');
        $this->db->from('log_ujian');
        $this->db->where('id_jadwal=', $id_jadwal);
        return $this->db->get()->result();
    }
    public function getIdSiswaFromLogUjianByJadwal($id_jadwal)
    {
        $result = $this->db->get_where('log_ujian', 'id_jadwal=' . $id_jadwal)->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        return $retur;
    }
    public function getNilaiSiswa($arr_jadwal, $id_siswa)
    {
        $this->db->select('*');
        $this->db->from('cbt_nilai');
        $this->db->where_in('id_jadwal', $arr_jadwal);
        $this->db->where('id_siswa', $id_siswa);
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_jadwal] = $row;
        }
        return $retur;
    }
    public function getNilaiSiswaByJadwal($id_jadwal, $id_siswa)
    {
        $this->db->select('*');
        $this->db->from('cbt_nilai');
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->where('id_siswa', $id_siswa);
        return $this->db->get()->row();
    }
    public function getNilaiAllSiswa($arr_jadwal, $arr_id_siswa)
    {
        $this->db->select('*');
        $this->db->from('cbt_nilai');
        $this->db->where_in('id_jadwal', $arr_jadwal);
        $this->db->where_in('id_siswa', $arr_id_siswa);
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        return $retur;
    }
    public function getAllNilaiSiswa($id_jadwal)
    {
        $this->db->select('*');
        $this->db->from('cbt_nilai');
        $this->db->where('id_jadwal', $id_jadwal);
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        return $retur;
    }
    public function getTotalKoreksi()
    {
        $this->db->select('id_jadwal, dikoreksi, id_siswa');
        $this->db->from('cbt_nilai');
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            if (!($row->id_siswa != null)) {
            } else {
                $retur[$row->id_jadwal][$row->dikoreksi][] = $row->id_siswa;
            }
        }
        return $retur;
    }
    public function getNilaiAnalisis($id_jadwal)
    {
        return $this->db->get_where('cbt_nilai', 'id_jadwal=' . $id_jadwal)->result();
    }
    public function getLogUjian($siswa_id, $id_jadwal)
    {
        return $this->db->get_where('log_ujian', 'id_siswa=' . $siswa_id . ' AND id_jadwal=' . $id_jadwal)->result();
    }
    public function getNomorPeserta($id_siswa)
    {
        return $this->db->get_where('cbt_nomor_peserta', 'id_siswa=' . $id_siswa)->row();
    }
    public function getAllNomorPeserta()
    {
        $this->db->select('*');
        $result = $this->db->get('cbt_nomor_peserta')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
    }
    public function getDistinctTahun()
    {
        $this->db->select('tp');
        $this->db->distinct();
        $result = $this->db->get('cbt_rekap_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->tp] = $row->tp;
        }
        return $ret;
    }
    public function getDistinctSmt()
    {
        $this->db->select('smt');
        $this->db->distinct();
        $result = $this->db->get('cbt_rekap_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->smt] = $row->smt;
        }
        return $ret;
    }
    public function getDistinctJenisUjian()
    {
        $this->db->select('tp, smt, kode_jenis');
        $this->db->distinct();
        $result = $this->db->get('cbt_rekap_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->tp][$row->smt][$row->kode_jenis] = $row->kode_jenis;
        }
        return $ret;
    }
    public function getDistinctJenis()
    {
        $this->db->select('id_jenis, tp, smt, kode_jenis');
        $this->db->distinct();
        $result = $this->db->get('cbt_rekap_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->tp][$row->smt][$row->id_jenis] = $row->kode_jenis;
        }
        return $ret;
    }
    public function getDistinctKelas($id_jadwal = null)
    {
        $this->db->select('a.tp, a.smt, a.kode_jenis, a.id_kelas, b.nama_kelas');
        $this->db->distinct();
        $this->db->from('cbt_rekap_nilai a');
        if (!($id_jadwal != null)) {
            $this->db->join('master_kelas b', 'b.id_kelas=a.id_kelas');
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $row) {
                if (!($row->id_kelas != '')) {
                } else {
                    $ret[$row->tp][$row->smt][$row->kode_jenis][$row->id_kelas] = $row->nama_kelas;
                }
            }
            return $ret;
        } else {
            $this->db->where('id_jadwal', $id_jadwal);
            $this->db->join('master_kelas b', 'b.id_kelas=a.id_kelas');
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $row) {
                if (!($row->id_kelas != '')) {
                } else {
                    $ret[$row->tp][$row->smt][$row->kode_jenis][$row->id_kelas] = $row->nama_kelas;
                }
            }
            return $ret;
        }
    }
}
```

---

## File: application/models_decoded/Dashboard_model.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard_model extends CI_Model
{
    public function getSetting()
    {
        return $this->db->get('setting')->row();
    }
    public function getRunningText()
    {
        return $this->db->get('running_text')->result();
    }
    public function total($table, $where = null)
    {
        if (!($where != null)) {
            return $this->db->get($table)->num_rows();
        } else {
            $this->db->where($where);
            return $this->db->get($table)->num_rows();
        }
    }
    public function hapus($table, $data, $pk)
    {
        $this->db->where_in($pk, $data);
        return $this->db->delete($table);
    }
    public function getProfileAdmin($id_user)
    {
        $this->db->select('b.*');
        $this->db->from('users a');
        $this->db->join('users_profile b', 'a.id=b.id_user', 'left');
        $this->db->where('a.id', $id_user);
        return $this->db->get()->row();
    }
    public function totalWaliKelas($id_tp, $id_smt)
    {
        $this->db->where('id_jabatan', '4');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get('jabatan_guru')->num_rows();
    }
    public function totalSiswaKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.id_siswa');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        return $this->db->get()->num_rows();
    }
    public function totalPengawas()
    {
        $this->db->select('*');
        $this->db->where('id_jadwal !=', 'a:0:{}');
        return $this->db->get('cbt_pengawas')->num_rows();
    }
    public function totalJadwal()
    {
        $this->db->select('*');
        return $this->db->get('cbt_jadwal')->num_rows();
    }
    public function getDataTahun()
    {
        $this->datatables->select('id_tp, tahun, active');
        $this->datatables->from('master_tp');
        return $this->datatables->generate();
    }
    public function getTahun()
    {
        $this->db->order_by('tahun', 'ASC');
        return $this->db->get('master_tp')->result();
    }
    public function getTahunById($id)
    {
        return $this->db->get_where('master_tp', 'id_tp=' . $id)->row();
    }
    public function getTahunByTahun($tahun)
    {
        return $this->db->get_where('master_tp', 'tahun=' . '"' . $tahun . '"')->row();
    }
    public function getTahunActive()
    {
        $this->db->select('id_tp, tahun');
        $this->db->from('master_tp');
        $this->db->where('active', 1);
        return $this->db->get()->row();
    }
    public function getSemester()
    {
        $this->db->order_by('smt', 'ASC');
        return $this->db->get('master_smt')->result();
    }
    public function getSemesterById($id)
    {
        return $this->db->get_where('master_smt', 'id_smt=' . $id)->row();
    }
    public function getSemesterByNama($nama_smt)
    {
        return $this->db->get_where('master_smt', 'nama_smt=' . '"' . $nama_smt . '"')->row();
    }
    public function getSemesterActive()
    {
        $this->db->select('id_smt, nama_smt, smt');
        $this->db->from('master_smt');
        $this->db->where('active', 1);
        return $this->db->get()->row();
    }
    public function getDataGuruByUserId($id_user, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, a.nip, a.id_user, a.foto, b.id_jabatan, b.id_kelas as wali_kelas, f.level_id, g.level');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->join('level_kelas g', 'f.level_id=g.id_level', 'left');
        $this->db->where('a.id_user', $id_user);
        return $this->db->get()->row();
    }
    public function getDataGuruById($id_guru, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, a.nip, a.id_user, a.foto, b.id_jabatan, b.id_kelas as wali_kelas, f.level_id, g.level');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->join('level_kelas g', 'f.level_id=g.id_level', 'left');
        $this->db->where('a.id_guru', $id_guru);
        return $this->db->get()->row();
    }
    public function getListGuruByUserId($id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, a.id_user, a.foto, b.id_jabatan, b.id_kelas as wali_kelas, f.level_id, g.level');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->join('level_kelas g', 'f.level_id=g.id_level', 'left');
        $query = $this->db->get()->result();
        $rest = [];
        foreach ($query as $guru) {
            $rest[$guru->id_guru] = $guru;
        }
        return $rest;
    }
    public function getDetailGuruByUserId($id_user, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('*');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->where('a.id_user', $id_user);
        return $this->db->get()->row();
    }
    public function getKelasByMapel($id_mapel = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('*');
        $this->db->from('master_kelas');
        $this->db->join('master_mapel b', 'a.mapel_id=b.id_mapel', 'left');
        $this->db->join('level_guru d', 'a.level_id=d.id_level', 'left');
        return $this->db->get()->row();
    }
    public function get_where($table, $pk, $id, $join = null, $order = null)
    {
        $this->db->select('*');
        $this->db->from($table);
        $this->db->where($pk, $id);
        if (!($join !== null)) {
            if (!($order !== null)) {
            }
            foreach ($order as $field => $sort) {
                $this->db->order_by($field, $sort);
            }
            return $this->db->get();
        } else {
            foreach ($join as $table => $field) {
                $this->db->join($table, $field);
            }
            if (!($order !== null)) {
            }
            foreach ($order as $field => $sort) {
                $this->db->order_by($field, $sort);
            }
            return $this->db->get();
        }
    }
    public function create($table, $data)
    {
        return $this->db->insert($table, $data);
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
    public function getDataSiswa($username, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('*');
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa b', 'a.id_siswa=b.id_siswa AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('master_kelas c', 'b.id_kelas=c.id_kelas AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt, 'left');
        $this->db->join('cbt_sesi_siswa d', 'a.id_siswa=d.siswa_id', 'left');
        $this->db->where('username', $username);
        return $this->db->get()->row();
    }
    public function loadPengumuman($id_for)
    {
        $this->db->select('a.*, b.nama_guru, b.foto');
        $this->db->from('pengumuman a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->where('kepada', $id_for);
        return $this->db->get()->result();
    }
    public function loadJadwalHariIni($id_tp, $id_smt, $id_kelas = null, $id_hari = null)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        if (!($id_kelas != null)) {
            if (!($id_hari != null)) {
            }
            $this->db->where('a.id_hari', $id_hari);
            return $this->db->get()->result();
        } else {
            $this->db->where('a.id_kelas', $id_kelas);
            if (!($id_hari != null)) {
            }
            $this->db->where('a.id_hari', $id_hari);
            return $this->db->get()->result();
        }
    }
    public function getJadwalKbm($id_tp, $id_smt, $id_kelas = null)
    {
        $this->db->select('*');
        $this->db->from('kelas_jadwal_kbm');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        if ($id_kelas != null) {
            $this->db->where('id_kelas', $id_kelas);
            $query = $this->db->get()->row();
            return $query;
        } else {
            $query = $this->db->get()->result();
            return $query;
        }
    }
}
```

---

## File: application/models_decoded/Dropdown_model.php

```php
<?php

class Dropdown_model extends CI_Model
{
    public function getBulan()
    {
        $result = $this->db->get('bulan')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_bln] = $row->nama_bln;
            }
            return $ret;
        }
    }
    public function getAllSesi()
    {
        $this->db->select('id_sesi, nama_sesi, kode_sesi');
        $result = $this->db->get('cbt_sesi')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_sesi] = $row->nama_sesi;
            }
            return $ret;
        }
    }
    public function getAllRuang()
    {
        $result = $this->db->get('cbt_ruang')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_ruang] = $row->nama_ruang;
            }
            return $ret;
        }
    }
    public function getAllWaktuSesi()
    {
        $result = $this->db->get('cbt_sesi')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_sesi] = ['mulai' => $row->waktu_mulai, 'akhir' => $row->waktu_akhir];
            }
            return $ret;
        }
    }
    public function getDataKelompokMapel()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->order_by('kode_kel_mapel');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kode_kel_mapel] = $row->nama_kel_mapel;
        }
        return $ret;
    }
    public function getAllMapel()
    {
        $this->db->select('id_mapel,nama_mapel,urutan_tampil');
        $this->db->order_by('urutan_tampil');
        $this->db->where('status', '1');
        $result = $this->db->get('master_mapel')->result();
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
    public function getAllKodeMapel()
    {
        $this->db->order_by('urutan_tampil');
        $this->db->where('status', '1');
        $result = $this->db->get('master_mapel')->result();
        $ret[''] = 'Tidak ada';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_mapel] = $row->kode;
            }
            return $ret;
        }
    }
    public function getAllMapelPeminatan()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->where('kategori <> "WAJIB"')->where('kategori <> "PAI (Kemenag)"')->where('kategori <> "MULOK"');
        $res = $this->db->get('master_mapel')->result();
        $ress = [];
        if (!$res) {
            $ret = [];
            if (!(count($ress) > 0)) {
            }
            $this->db->where_in('kelompok', $ress);
            $this->db->order_by('urutan_tampil');
            $result = $this->db->get('master_mapel')->result();
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_mapel] = $row->nama_mapel;
            }
            return $ret;
        } else {
            foreach ($res as $key => $row) {
                $ress[$row->id_kel_mapel] = $row->kode_kel_mapel;
            }
            $ret = [];
            if (!(count($ress) > 0)) {
            }
            $this->db->where_in('kelompok', $ress);
            $this->db->order_by('urutan_tampil');
            $result = $this->db->get('master_mapel')->result();
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_mapel] = $row->nama_mapel;
            }
            return $ret;
        }
    }
    public function getAllKodePeminatan()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->where('kategori <> "WAJIB"');
        $this->db->where('kategori <> "PAI (Kemenag)"');
        $this->db->where('kategori <> "MULOK"');
        $res = $this->db->get('master_mapel')->result();
        $ress = [];
        if (!$res) {
            return $ress;
        } else {
            foreach ($res as $key => $row) {
                $ress[$row->id_kel_mapel] = $row;
            }
            return $ress;
        }
    }
    public function getMapelPeminatan($arr_kelompok)
    {
        if (count($arr_kelompok) > 0) {
            $this->db->where_in('kelompok', $arr_kelompok);
            $this->db->order_by('urutan_tampil');
            $result = $this->db->get('master_mapel')->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->kelompok][$row->id_mapel] = $row->nama_mapel;
            }
            return $ret;
        } else {
            return [];
        }
    }
    public function getAllLevel($jenjang)
    {
        $levels = [];
        if ($jenjang == '1') {
            $levels = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
            return $levels;
        } else {
            if ($jenjang == '2') {
            }
            if ($jenjang == '3') {
            }
            return $levels;
        }
    }
    public function getAllKelas($tp, $smt, $level = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $this->db->order_by('level_id', 'ASC');
        $this->db->order_by('nama_kelas', 'ASC');
        if (!($level != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->nama_kelas;
            }
            return $ret;
        } else {
            $this->db->where('level_id' . $level);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->nama_kelas;
            }
            return $ret;
        }
    }
    public function getAllKeyKodeKelas($tp, $smt)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->kode_kelas] = $row->nama_kelas;
            }
            return $ret;
        }
    }
    public function getAllKodeKelas($tp = null, $smt = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        if (!($tp != null)) {
            if (!($smt != null)) {
            }
            $this->db->where('id_smt', $smt);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->kode_kelas;
            }
            return $ret;
        } else {
            $this->db->where('id_tp', $tp);
            if (!($smt != null)) {
            }
            $this->db->where('id_smt', $smt);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->kode_kelas;
            }
            return $ret;
        }
    }
    public function getNamaKelasById($tp, $smt, $id)
    {
        $this->db->select('nama_kelas');
        $this->db->where('id_kelas', $id);
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get('master_kelas')->row();
        if ($result != null) {
            return $result->nama_kelas;
        } else {
            return null;
        }
    }
    public function getAllKelasByArrayId($tp, $smt, $arrId)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where_in('id_kelas', $arrId);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas] = $row->nama_kelas;
            }
            return $ret;
        }
    }
    public function getAllEkskul()
    {
        $result = $this->db->get('master_ekstra')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_ekstra] = $row->nama_ekstra;
            }
            return $ret;
        }
    }
    public function getAllKodeEkskul()
    {
        $result = $this->db->get('master_ekstra')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_ekstra] = $row->kode_ekstra;
            }
            return $ret;
        }
    }
    public function getAllJurusan()
    {
        $result = $this->db->get('master_jurusan')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jurusan] = $row->kode_jurusan;
            }
            return $ret;
        }
    }
    public function getAllGuru()
    {
        $this->db->select('a.id_guru, a.nama_guru');
        $this->db->from('master_guru a');
        $this->db->join('users e', 'a.username=e.username');
        $result = $this->db->get()->result();
        $ret['0'] = 'Pilih Guru :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_guru] = $row->nama_guru;
            }
            return $ret;
        }
    }
    public function getAllLevelGuru()
    {
        $result = $this->db->get('level_guru')->result();
        $ret[''] = 'Pilih Jabatan :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_level] = $row->level;
            }
            return $ret;
        }
    }
    public function getAllJenisUjian()
    {
        $result = $this->db->get('cbt_jenis')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jenis] = $row->nama_jenis . ' (' . $row->kode_jenis . ')';
            }
            return $ret;
        }
    }
    public function getAllBankSoal()
    {
        $result = $this->db->get('cbt_bank_soal')->result();
        $ret[''] = 'Pilih Bank Soal :';
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_bank] = $row->bank_kode;
            }
            return $ret;
        }
    }
    public function getAllJadwal($tp, $smt)
    {
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row->bank_kode;
            }
            return $ret;
        }
    }
    public function getAllJadwalMapel($tp, $smt)
    {
        $this->db->select('a.id_jadwal, b.bank_kode, d.nama_mapel');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return array_unique($ret);
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row->nama_mapel;
            }
            return array_unique($ret);
        }
    }
    public function getAllJadwalGuru($tp, $smt, $guru)
    {
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_guru_id=' . $guru);
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row->bank_kode;
            }
            return $ret;
        }
    }
    public function getAllJenisJadwal($tp, $smt, $jenis, $mapel)
    {
        $this->db->from('cbt_jadwal a');
        if ($mapel == '0') {
            $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
            $this->db->where('a.id_tp', $tp);
            $this->db->where('a.id_smt', $smt);
            $this->db->where('a.id_jenis', $jenis);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row->bank_kode;
            }
            return $ret;
        } else {
            $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_mapel_id=' . $mapel . ' ');
            $this->db->where('a.id_tp', $tp);
            $this->db->where('a.id_smt', $smt);
            $this->db->where('a.id_jenis', $jenis);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_jadwal] = $row->bank_kode;
            }
            return $ret;
        }
    }
}
```

---

## File: application/models_decoded/Install_model.php

```php
<?php

class Install_model extends CI_Model
{
    function install_success()
    {
        return $this->check_installer();
    }
    function check_installer()
    {
        include APPPATH . 'config/database.php';
        $database = $db['default']['database'];
        $this->load->dbutil();
        if ($database == '') {
            return '1';
        } else {
            if (!$this->dbutil->database_exists($database)) {
            }
            $CI =& get_instance();
            $CI->load->database();
            if ($CI->db->table_exists('users')) {
            }
            return '2';
        }
    }
}
```

---

## File: application/models_decoded/Ion_auth_model.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Ion_auth_model extends CI_Model
{
    const MAX_COOKIE_LIFETIME = 63072000;
    const MAX_PASSWORD_SIZE_BYTES = 4096;
    public $tables = array();
    public $activation_code;
    public $new_password;
    public $identity;
    public $_ion_where = array();
    public $_ion_select = array();
    public $_ion_like = array();
    public $_ion_limit = NULL;
    public $_ion_offset = NULL;
    public $_ion_order_by = NULL;
    public $_ion_order = NULL;
    protected $_ion_hooks;
    protected $response = NULL;
    protected $messages;
    protected $errors;
    protected $error_start_delimiter;
    protected $error_end_delimiter;
    public $_cache_user_in_group = array();
    protected $_cache_groups = array();
    protected $db;
    private $identity_column;
    private $join;
    private $hash_method;
    private $message_start_delimiter;
    private $message_end_delimiter;
    public function __construct()
    {
        $this->config->load('ion_auth', TRUE);
        $this->load->helper('cookie');
        $this->load->helper('date');
        $this->lang->load('ion_auth');
        $group_name = $this->config->item('database_group_name', 'ion_auth');
        if (empty($group_name)) {
            $CI =& get_instance();
            $this->db = $CI->db;
        } else {
            $this->db = $this->load->database($group_name, TRUE, TRUE);
        }
        $this->tables = $this->config->item('tables', 'ion_auth');
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        $this->join = $this->config->item('join', 'ion_auth');
        $this->hash_method = $this->config->item('hash_method', 'ion_auth');
        $this->messages = [];
        $this->errors = [];
        $delimiters_source = $this->config->item('delimiters_source', 'ion_auth');
        if ($delimiters_source === 'form_validation') {
        }
        $this->message_start_delimiter = $this->config->item('message_start_delimiter', 'ion_auth');
        $this->message_end_delimiter = $this->config->item('message_end_delimiter', 'ion_auth');
        $this->error_start_delimiter = $this->config->item('error_start_delimiter', 'ion_auth');
        $this->error_end_delimiter = $this->config->item('error_end_delimiter', 'ion_auth');
        $this->_ion_hooks = new stdClass();
        $this->trigger_events('model_constructor');
    }
    public function db()
    {
        return $this->db;
    }
    public function hash_password($password, $identity = NULL)
    {
        if (!(empty($password) || strpos($password, ' ') !== FALSE || strlen($password) > self::MAX_PASSWORD_SIZE_BYTES)) {
            $algo = $this->_get_hash_algo();
            $params = $this->_get_hash_parameters($identity);
            if (!($algo !== FALSE && $params !== FALSE)) {
            }
            return password_hash($password, $algo, $params);
        } else {
            return FALSE;
        }
    }
    public function verify_password($password, $hash_password_db, $identity = NULL)
    {
        if (!(empty($password) || empty($hash_password_db) || strpos($password, ' ') !== FALSE || strlen($password) > self::MAX_PASSWORD_SIZE_BYTES)) {
            if (strpos($hash_password_db, '$') === 0) {
            }
            return $this->_password_verify_sha1_legacy($identity, $password, $hash_password_db);
        } else {
            return FALSE;
        }
    }
    public function rehash_password_if_needed($hash, $identity, $password)
    {
        $algo = $this->_get_hash_algo();
        $params = $this->_get_hash_parameters($identity);
        if (!($algo !== FALSE && $params !== FALSE)) {
        } else {
            if (!password_needs_rehash($hash, $algo, $params)) {
            }
            if ($this->_set_password_db($identity, $password)) {
            }
            $this->trigger_events(['rehash_password', 'rehash_password_unsuccessful']);
        }
    }
    public function get_user_by_activation_code($user_code)
    {
        $token = $this->_retrieve_selector_validator_couple($user_code);
        $user = $this->where('activation_selector', $token->selector)->users()->row();
        if (!$user) {
            return FALSE;
        } else {
            if (!$this->verify_password($token->validator, $user->activation_code)) {
            }
            return $user;
        }
    }
    public function activate($id, $code = FALSE)
    {
        $this->trigger_events('pre_activate');
        if (!($code !== FALSE)) {
            if (!($code === FALSE || $user && $user->id === $id)) {
            }
            $data = ['activation_selector' => NULL, 'activation_code' => NULL, 'active' => 1];
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $data, ['id' => $id]);
            if (!($this->db->affected_rows() === 1)) {
            }
            $this->trigger_events(['post_activate', 'post_activate_successful']);
            $this->set_message('activate_successful');
            return TRUE;
        } else {
            $user = $this->get_user_by_activation_code($code);
            if (!($code === FALSE || $user && $user->id === $id)) {
            }
            $data = ['activation_selector' => NULL, 'activation_code' => NULL, 'active' => 1];
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $data, ['id' => $id]);
            if (!($this->db->affected_rows() === 1)) {
            }
            $this->trigger_events(['post_activate', 'post_activate_successful']);
            $this->set_message('activate_successful');
            return TRUE;
        }
    }
    public function deactivate($id = NULL)
    {
        $this->trigger_events('deactivate');
        if (!isset($id)) {
            $this->set_error('deactivate_unsuccessful');
            return FALSE;
        } else {
            if (!($this->ion_auth->logged_in() && $this->user()->row()->id == $id)) {
            }
            $this->set_error('deactivate_current_user_unsuccessful');
            return FALSE;
        }
    }
    public function clear_forgotten_password_code($identity)
    {
        if (!empty($identity)) {
            $data = ['forgotten_password_selector' => NULL, 'forgotten_password_code' => NULL, 'forgotten_password_time' => NULL];
            $this->db->update($this->tables['users'], $data, [$this->identity_column => $identity]);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function clear_remember_code($identity)
    {
        if (!empty($identity)) {
            $data = ['remember_selector' => NULL, 'remember_code' => NULL];
            $this->db->update($this->tables['users'], $data, [$this->identity_column => $identity]);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function reset_password($identity, $new)
    {
        $this->trigger_events('pre_change_password');
        if ($this->identity_check($identity)) {
            $return = $this->_set_password_db($identity, $new);
            if ($return) {
            }
            $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
            $this->set_error('password_change_unsuccessful');
            return $return;
        } else {
            $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
            return FALSE;
        }
    }
    public function change_password($identity, $old, $new)
    {
        $this->trigger_events('pre_change_password');
        $this->trigger_events('extra_where');
        $query = $this->db->select('id, password')->where($this->identity_column, $identity)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if (!($query->num_rows() !== 1)) {
            $user = $query->row();
            if (!$this->verify_password($old, $user->password, $identity)) {
            }
            $result = $this->_set_password_db($identity, $new);
            if ($result) {
            }
            $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
            $this->set_error('password_change_unsuccessful');
            return $result;
        } else {
            $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
            $this->set_error('password_change_unsuccessful');
            return FALSE;
        }
    }
    public function username_check($username = '')
    {
        $this->trigger_events('username_check');
        if (!empty($username)) {
            $this->trigger_events('extra_where');
            return $this->db->where('username', $username)->limit(1)->count_all_results($this->tables['users']) > 0;
        } else {
            return FALSE;
        }
    }
    public function email_check($email = '')
    {
        $this->trigger_events('email_check');
        if (!empty($email)) {
            $this->trigger_events('extra_where');
            return $this->db->where('email', $email)->limit(1)->count_all_results($this->tables['users']) > 0;
        } else {
            return FALSE;
        }
    }
    public function identity_check($identity = '')
    {
        $this->trigger_events('identity_check');
        if (!empty($identity)) {
            return $this->db->where($this->identity_column, $identity)->limit(1)->count_all_results($this->tables['users']) > 0;
        } else {
            return FALSE;
        }
    }
    public function get_user_id_from_identity($identity = '')
    {
        if (!empty($identity)) {
            $query = $this->db->select('id')->where($this->identity_column, $identity)->limit(1)->get($this->tables['users']);
            if (!($query->num_rows() !== 1)) {
            }
            return FALSE;
        } else {
            return FALSE;
        }
    }
    public function forgotten_password($identity)
    {
        if (!empty($identity)) {
            $token = $this->_generate_selector_validator_couple(20, 80);
            $update = ['forgotten_password_selector' => $token->selector, 'forgotten_password_code' => $token->validator_hashed, 'forgotten_password_time' => time()];
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $update, [$this->identity_column => $identity]);
            if ($this->db->affected_rows() === 1) {
            }
            $this->trigger_events(['post_forgotten_password', 'post_forgotten_password_unsuccessful']);
            return FALSE;
        } else {
            $this->trigger_events(['post_forgotten_password', 'post_forgotten_password_unsuccessful']);
            return FALSE;
        }
    }
    public function get_user_by_forgotten_password_code($user_code)
    {
        $token = $this->_retrieve_selector_validator_couple($user_code);
        $user = $this->where('forgotten_password_selector', $token->selector)->users()->row();
        if (!$user) {
            return FALSE;
        } else {
            if (!$this->verify_password($token->validator, $user->forgotten_password_code)) {
            }
            return $user;
        }
    }
    public function register($identity, $password, $email, $additional_data = array(), $groups = array())
    {
        $this->trigger_events('pre_register');
        $manual_activation = $this->config->item('manual_activation', 'ion_auth');
        if ($this->identity_check($identity)) {
            $this->set_error('account_creation_duplicate_identity');
            return FALSE;
        } else {
            if (!(!$this->config->item('default_group', 'ion_auth') && empty($groups))) {
            }
            $this->set_error('account_creation_missing_default_group');
            return FALSE;
        }
    }
    public function login($identity, $password, $remember = FALSE)
    {
        $this->trigger_events('pre_login');
        if (!(empty($identity) || empty($password))) {
            $this->trigger_events('extra_where');
            $query = $this->db->select($this->identity_column . ', email, id, password, active, last_login')->where($this->identity_column, $identity)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
            if (!$this->is_max_login_attempts_exceeded($identity)) {
            }
            $this->hash_password($password);
            $this->trigger_events('post_login_unsuccessful');
            $this->set_error('login_timeout');
            return FALSE;
        } else {
            $this->set_error('login_unsuccessful');
            return FALSE;
        }
    }
    public function recheck_session()
    {
        $recheck = NULL !== $this->config->item('recheck_timer', 'ion_auth') ? $this->config->item('recheck_timer', 'ion_auth') : 0;
        if (!($recheck !== 0)) {
            return (bool) $this->session->userdata('identity');
        } else {
            $last_login = $this->session->userdata('last_check');
            if (!($last_login + $recheck < time())) {
            }
            $query = $this->db->select('id')->where([$this->identity_column => $this->session->userdata('identity'), 'active' => '1'])->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
            if ($query->num_rows() === 1) {
            }
            $this->trigger_events('logout');
            $identity = $this->config->item('identity', 'ion_auth');
            $this->session->unset_userdata([$identity, 'id', 'user_id']);
            return FALSE;
        }
    }
    public function is_max_login_attempts_exceeded($identity, $ip_address = NULL)
    {
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
            return FALSE;
        } else {
            $max_attempts = $this->config->item('maximum_login_attempts', 'ion_auth');
            if (!($max_attempts > 0)) {
            }
            $attempts = $this->get_attempts_num($identity, $ip_address);
            return $attempts >= $max_attempts;
        }
    }
    public function get_attempts_num($identity, $ip_address = NULL)
    {
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
            return 0;
        } else {
            $this->db->select('1', FALSE);
            $this->db->where('login', $identity);
            if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
            }
            if (isset($ip_address)) {
            }
            $ip_address = $this->input->ip_address();
            $this->db->where('ip_address', $ip_address);
            $this->db->where('time >', time() - $this->config->item('lockout_time', 'ion_auth'), FALSE);
            $qres = $this->db->get($this->tables['login_attempts']);
            return $qres->num_rows();
        }
    }
    public function get_last_attempt_time($identity, $ip_address = NULL)
    {
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
            return 0;
        } else {
            $this->db->select('time');
            $this->db->where('login', $identity);
            if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
            }
            if (isset($ip_address)) {
            }
            $ip_address = $this->input->ip_address();
            $this->db->where('ip_address', $ip_address);
            $this->db->order_by('id', 'desc');
            $qres = $this->db->get($this->tables['login_attempts'], 1);
            if (!($qres->num_rows() > 0)) {
            }
            return $qres->row()->time;
        }
    }
    public function get_last_attempt_ip($identity)
    {
        if (!($this->config->item('track_login_attempts', 'ion_auth') && $this->config->item('track_login_ip_address', 'ion_auth'))) {
            return '';
        } else {
            $this->db->select('ip_address');
            $this->db->where('login', $identity);
            $this->db->order_by('id', 'desc');
            $qres = $this->db->get($this->tables['login_attempts'], 1);
            if (!($qres->num_rows() > 0)) {
            }
            return $qres->row()->ip_address;
        }
    }
    public function increase_login_attempts($identity)
    {
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
            return FALSE;
        } else {
            $data = ['ip_address' => '', 'login' => $identity, 'time' => time()];
            if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
            }
            $data['ip_address'] = $this->input->ip_address();
            return $this->db->insert($this->tables['login_attempts'], $data);
        }
    }
    public function clear_login_attempts($identity, $old_attempts_expire_period = 86400, $ip_address = NULL)
    {
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
            return FALSE;
        } else {
            $old_attempts_expire_period = max($old_attempts_expire_period, $this->config->item('lockout_time', 'ion_auth'));
            $this->db->where('login', $identity);
            if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
            }
            if (isset($ip_address)) {
            }
            $ip_address = $this->input->ip_address();
            $this->db->where('ip_address', $ip_address);
            $this->db->or_where('time <', time() - $old_attempts_expire_period, FALSE);
            return $this->db->delete($this->tables['login_attempts']);
        }
    }
    public function limit($limit)
    {
        $this->trigger_events('limit');
        $this->_ion_limit = $limit;
        return $this;
    }
    public function offset($offset)
    {
        $this->trigger_events('offset');
        $this->_ion_offset = $offset;
        return $this;
    }
    public function where($where, $value = NULL)
    {
        $this->trigger_events('where');
        if (is_array($where)) {
            array_push($this->_ion_where, $where);
            return $this;
        } else {
            $where = [$where => $value];
            array_push($this->_ion_where, $where);
            return $this;
        }
    }
    public function like($like, $value = NULL, $position = 'both')
    {
        $this->trigger_events('like');
        array_push($this->_ion_like, ['like' => $like, 'value' => $value, 'position' => $position]);
        return $this;
    }
    public function select($select)
    {
        $this->trigger_events('select');
        $this->_ion_select[] = $select;
        return $this;
    }
    public function order_by($by, $order = 'desc')
    {
        $this->trigger_events('order_by');
        $this->_ion_order_by = $by;
        $this->_ion_order = $order;
        return $this;
    }
    public function row()
    {
        $this->trigger_events('row');
        $row = $this->response->row();
        return $row;
    }
    public function row_array()
    {
        $this->trigger_events(['row', 'row_array']);
        $row = $this->response->row_array();
        return $row;
    }
    public function result()
    {
        $this->trigger_events('result');
        $result = $this->response->result();
        return $result;
    }
    public function result_array()
    {
        $this->trigger_events(['result', 'result_array']);
        $result = $this->response->result_array();
        return $result;
    }
    public function num_rows()
    {
        $this->trigger_events(['num_rows']);
        $result = $this->response->num_rows();
        return $result;
    }
    public function users($groups = NULL)
    {
        $this->trigger_events('users');
        if (isset($this->_ion_select) && !empty($this->_ion_select)) {
            foreach ($this->_ion_select as $select) {
                $this->db->select($select);
            }
            $this->_ion_select = [];
            if (!isset($groups)) {
            }
            if (is_array($groups)) {
            }
            $groups = [$groups];
            if (!(isset($groups) && !empty($groups))) {
            }
            $this->db->distinct();
            $this->db->join($this->tables['users_groups'], $this->tables['users_groups'] . '.' . $this->join['users'] . '=' . $this->tables['users'] . '.id', 'inner');
            $group_ids = [];
            $group_names = [];
            foreach ($groups as $group) {
                if (is_numeric($group)) {
                    $group_ids[] = $group;
                } else {
                    $group_names[] = $group;
                }
            }
            $or_where_in = !empty($group_ids) && !empty($group_names) ? 'or_where_in' : 'where_in';
            if (empty($group_names)) {
            }
            $this->db->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['groups'] . '.id', 'inner');
            $this->db->where_in($this->tables['groups'] . '.name', $group_names);
            if (empty($group_ids)) {
            }
            $this->db->{$or_where_in}($this->tables['users_groups'] . '.' . $this->join['groups'], $group_ids);
            $this->trigger_events('extra_where');
            if (!(isset($this->_ion_where) && !empty($this->_ion_where))) {
            }
            foreach ($this->_ion_where as $where) {
                $this->db->where($where);
            }
            $this->_ion_where = [];
            if (!(isset($this->_ion_like) && !empty($this->_ion_like))) {
            }
            foreach ($this->_ion_like as $like) {
                $this->db->or_like($like['like'], $like['value'], $like['position']);
            }
            $this->_ion_like = [];
            if (isset($this->_ion_limit) && isset($this->_ion_offset)) {
            }
            if (!isset($this->_ion_limit)) {
            }
            $this->db->limit($this->_ion_limit);
            $this->_ion_limit = NULL;
            if (!(isset($this->_ion_order_by) && isset($this->_ion_order))) {
            }
            $this->db->order_by($this->_ion_order_by, $this->_ion_order);
            $this->_ion_order = NULL;
            $this->_ion_order_by = NULL;
            $this->response = $this->db->get($this->tables['users']);
            return $this;
        } else {
            $this->db->select([$this->tables['users'] . '.*', $this->tables['users'] . '.id as id', $this->tables['users'] . '.id as user_id']);
            if (!isset($groups)) {
            }
            if (is_array($groups)) {
            }
            $groups = [$groups];
            if (!(isset($groups) && !empty($groups))) {
            }
            $this->db->distinct();
            $this->db->join($this->tables['users_groups'], $this->tables['users_groups'] . '.' . $this->join['users'] . '=' . $this->tables['users'] . '.id', 'inner');
            $group_ids = [];
            $group_names = [];
            foreach ($groups as $group) {
                if (is_numeric($group)) {
                    $group_ids[] = $group;
                } else {
                    $group_names[] = $group;
                }
            }
            $or_where_in = !empty($group_ids) && !empty($group_names) ? 'or_where_in' : 'where_in';
            if (empty($group_names)) {
            }
            $this->db->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['groups'] . '.id', 'inner');
            $this->db->where_in($this->tables['groups'] . '.name', $group_names);
            if (empty($group_ids)) {
            }
            $this->db->{$or_where_in}($this->tables['users_groups'] . '.' . $this->join['groups'], $group_ids);
            $this->trigger_events('extra_where');
            if (!(isset($this->_ion_where) && !empty($this->_ion_where))) {
            }
            foreach ($this->_ion_where as $where) {
                $this->db->where($where);
            }
            $this->_ion_where = [];
            if (!(isset($this->_ion_like) && !empty($this->_ion_like))) {
            }
            foreach ($this->_ion_like as $like) {
                $this->db->or_like($like['like'], $like['value'], $like['position']);
            }
            $this->_ion_like = [];
            if (isset($this->_ion_limit) && isset($this->_ion_offset)) {
            }
            if (!isset($this->_ion_limit)) {
            }
            $this->db->limit($this->_ion_limit);
            $this->_ion_limit = NULL;
            if (!(isset($this->_ion_order_by) && isset($this->_ion_order))) {
            }
            $this->db->order_by($this->_ion_order_by, $this->_ion_order);
            $this->_ion_order = NULL;
            $this->_ion_order_by = NULL;
            $this->response = $this->db->get($this->tables['users']);
            return $this;
        }
    }
    public function user($id = NULL)
    {
        $this->trigger_events('user');
        $id = isset($id) ? $id : $this->session->userdata('user_id');
        $this->limit(1);
        $this->order_by($this->tables['users'] . '.id', 'desc');
        $this->where($this->tables['users'] . '.id', $id);
        $this->users();
        return $this;
    }
    public function get_users_groups($id = FALSE)
    {
        $this->trigger_events('get_users_group');
        $id || $id = $this->session->userdata('user_id');
        return $this->db->select($this->tables['users_groups'] . '.' . $this->join['groups'] . ' as id, ' . $this->tables['groups'] . '.name, ' . $this->tables['groups'] . '.description')->where($this->tables['users_groups'] . '.' . $this->join['users'], $id)->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . '=' . $this->tables['groups'] . '.id')->get($this->tables['users_groups']);
    }
    public function in_group($check_group, $id = FALSE, $check_all = FALSE)
    {
        $this->trigger_events('in_group');
        $id || $id = $this->session->userdata('user_id');
        if (is_array($check_group)) {
            if (isset($this->_cache_user_in_group[$id])) {
            }
            $users_groups = $this->get_users_groups($id)->result();
            $groups_array = [];
            foreach ($users_groups as $group) {
                $groups_array[$group->id] = $group->name;
            }
            $this->_cache_user_in_group[$id] = $groups_array;
            foreach ($check_group as $key => $value) {
                $groups = is_numeric($value) ? array_keys($groups_array) : $groups_array;
                if (!(in_array($value, $groups) xor $check_all)) {
                } else {
                    return !$check_all;
                }
            }
            return $check_all;
        } else {
            $check_group = [$check_group];
            if (isset($this->_cache_user_in_group[$id])) {
            }
            $users_groups = $this->get_users_groups($id)->result();
            $groups_array = [];
            foreach ($users_groups as $group) {
                $groups_array[$group->id] = $group->name;
            }
            $this->_cache_user_in_group[$id] = $groups_array;
            foreach ($check_group as $key => $value) {
                $groups = is_numeric($value) ? array_keys($groups_array) : $groups_array;
                if (!(in_array($value, $groups) xor $check_all)) {
                } else {
                    return !$check_all;
                }
            }
            return $check_all;
        }
    }
    public function add_to_group($group_ids, $user_id = FALSE)
    {
        $this->trigger_events('add_to_group');
        $user_id || $user_id = $this->session->userdata('user_id');
        if (is_array($group_ids)) {
            $return = 0;
            foreach ($group_ids as $group_id) {
                if (!$this->db->insert($this->tables['users_groups'], [$this->join['groups'] => (float) $group_id, $this->join['users'] => (float) $user_id])) {
                } else {
                    if (isset($this->_cache_groups[$group_id])) {
                    }
                    $group = $this->group($group_id)->result();
                    $group_name = $group[0]->name;
                    $this->_cache_groups[$group_id] = $group_name;
                    $this->_cache_user_in_group[$user_id][$group_id] = $group_name;
                    $return++;
                }
            }
            return $return;
        } else {
            $group_ids = [$group_ids];
            $return = 0;
            foreach ($group_ids as $group_id) {
                if (!$this->db->insert($this->tables['users_groups'], [$this->join['groups'] => (float) $group_id, $this->join['users'] => (float) $user_id])) {
                } else {
                    if (isset($this->_cache_groups[$group_id])) {
                    }
                    $group = $this->group($group_id)->result();
                    $group_name = $group[0]->name;
                    $this->_cache_groups[$group_id] = $group_name;
                    $this->_cache_user_in_group[$user_id][$group_id] = $group_name;
                    $return++;
                }
            }
            return $return;
        }
    }
    public function remove_from_group($group_ids = FALSE, $user_id = FALSE)
    {
        $this->trigger_events('remove_from_group');
        if (!empty($user_id)) {
            if (!empty($group_ids)) {
            }
            if (!$return = $this->db->delete($this->tables['users_groups'], [$this->join['users'] => (float) $user_id])) {
            }
            $this->_cache_user_in_group[$user_id] = [];
            return $return;
        } else {
            return FALSE;
        }
    }
    public function groups()
    {
        $this->trigger_events('groups');
        if (!(isset($this->_ion_where) && !empty($this->_ion_where))) {
            if (isset($this->_ion_limit) && isset($this->_ion_offset)) {
            }
            if (!isset($this->_ion_limit)) {
            }
            $this->db->limit($this->_ion_limit);
            $this->_ion_limit = NULL;
            if (!(isset($this->_ion_order_by) && isset($this->_ion_order))) {
            }
            $this->db->order_by($this->_ion_order_by, $this->_ion_order);
            $this->response = $this->db->get($this->tables['groups']);
            return $this;
        } else {
            foreach ($this->_ion_where as $where) {
                $this->db->where($where);
            }
            $this->_ion_where = [];
            if (isset($this->_ion_limit) && isset($this->_ion_offset)) {
            }
            if (!isset($this->_ion_limit)) {
            }
            $this->db->limit($this->_ion_limit);
            $this->_ion_limit = NULL;
            if (!(isset($this->_ion_order_by) && isset($this->_ion_order))) {
            }
            $this->db->order_by($this->_ion_order_by, $this->_ion_order);
            $this->response = $this->db->get($this->tables['groups']);
            return $this;
        }
    }
    public function group($id = NULL)
    {
        $this->trigger_events('group');
        if (!isset($id)) {
            $this->limit(1);
            $this->order_by('id', 'desc');
            return $this->groups();
        } else {
            $this->where($this->tables['groups'] . '.id', $id);
            $this->limit(1);
            $this->order_by('id', 'desc');
            return $this->groups();
        }
    }
    public function update($id, array $data)
    {
        $this->trigger_events('pre_update_user');
        $user = $this->user($id)->row();
        $this->db->trans_begin();
        if (!(array_key_exists($this->identity_column, $data) && $this->identity_check($data[$this->identity_column]) && $user->{$this->identity_column} !== $data[$this->identity_column])) {
            $data = $this->_filter_data($this->tables['users'], $data);
            if (!(array_key_exists($this->identity_column, $data) || array_key_exists('password', $data) || array_key_exists('email', $data))) {
            }
            if (!array_key_exists('password', $data)) {
            }
            if (!empty($data['password'])) {
            }
            unset($data['password']);
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $data, ['id' => $user->id]);
            if (!($this->db->trans_status() === FALSE)) {
            }
            $this->db->trans_rollback();
            $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
            $this->set_error('update_unsuccessful');
            return FALSE;
        } else {
            $this->db->trans_rollback();
            $this->set_error('account_creation_duplicate_identity');
            $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
            $this->set_error('update_unsuccessful');
            return FALSE;
        }
    }
    public function delete_user($id)
    {
        $this->trigger_events('pre_delete_user');
        $this->db->trans_begin();
        $this->remove_from_group(NULL, $id);
        $this->db->delete($this->tables['users'], ['id' => $id]);
        if (!($this->db->trans_status() === FALSE)) {
            $this->db->trans_commit();
            $this->trigger_events(['post_delete_user', 'post_delete_user_successful']);
            $this->set_message('delete_successful');
            return TRUE;
        } else {
            $this->db->trans_rollback();
            $this->trigger_events(['post_delete_user', 'post_delete_user_unsuccessful']);
            $this->set_error('delete_unsuccessful');
            return FALSE;
        }
    }
    public function update_last_login($id)
    {
        $this->trigger_events('update_last_login');
        $this->load->helper('date');
        $this->trigger_events('extra_where');
        $this->db->update($this->tables['users'], ['last_login' => time()], ['id' => $id]);
        return $this->db->affected_rows() == 1;
    }
    public function set_lang($lang = 'en')
    {
        $this->trigger_events('set_lang');
        if ($this->config->item('user_expire', 'ion_auth') === 0) {
            $expire = self::MAX_COOKIE_LIFETIME;
            set_cookie(['name' => 'lang_code', 'value' => $lang, 'expire' => $expire]);
            return TRUE;
        } else {
            $expire = $this->config->item('user_expire', 'ion_auth');
            set_cookie(['name' => 'lang_code', 'value' => $lang, 'expire' => $expire]);
            return TRUE;
        }
    }
    public function set_session($user)
    {
        $this->trigger_events('pre_set_session');
        $session_data = ['identity' => $user->{$this->identity_column}, $this->identity_column => $user->{$this->identity_column}, 'email' => $user->email, 'user_id' => $user->id, 'old_last_login' => $user->last_login, 'last_check' => time()];
        $this->session->set_userdata($session_data);
        $this->trigger_events('post_set_session');
        return TRUE;
    }
    public function remember_user($identity)
    {
        $this->trigger_events('pre_remember_user');
        if ($identity) {
            $token = $this->_generate_selector_validator_couple();
            if (!$token->validator_hashed) {
            }
            $this->db->update($this->tables['users'], ['remember_selector' => $token->selector, 'remember_code' => $token->validator_hashed], [$this->identity_column => $identity]);
            if (!($this->db->affected_rows() > -1)) {
            }
            if ($this->config->item('user_expire', 'ion_auth') === 0) {
            }
            $expire = $this->config->item('user_expire', 'ion_auth');
            set_cookie(['name' => $this->config->item('remember_cookie_name', 'ion_auth'), 'value' => $token->user_code, 'expire' => $expire]);
            $this->trigger_events(['post_remember_user', 'remember_user_successful']);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function login_remembered_user()
    {
        $this->trigger_events('pre_login_remembered_user');
        $remember_cookie = get_cookie($this->config->item('remember_cookie_name', 'ion_auth'));
        $token = $this->_retrieve_selector_validator_couple($remember_cookie);
        if (!($token === FALSE)) {
            $this->trigger_events('extra_where');
            $query = $this->db->select($this->identity_column . ', id, email, remember_code, last_login')->where('remember_selector', $token->selector)->where('active', 1)->limit(1)->get($this->tables['users']);
            if (!($query->num_rows() === 1)) {
            }
            $user = $query->row();
            $identity = $user->{$this->identity_column};
            if (!$this->verify_password($token->validator, $user->remember_code, $identity)) {
            }
            $this->update_last_login($user->id);
            $this->set_session($user);
            $this->clear_forgotten_password_code($identity);
            if (!$this->config->item('user_extend_on_login', 'ion_auth')) {
            }
            $this->remember_user($identity);
            $this->session->sess_regenerate(FALSE);
            $this->trigger_events(['post_login_remembered_user', 'post_login_remembered_user_successful']);
            return TRUE;
        } else {
            $this->trigger_events(['post_login_remembered_user', 'post_login_remembered_user_unsuccessful']);
            return FALSE;
        }
    }
    public function create_group($group_name = FALSE, $group_description = '', $additional_data = array())
    {
        if ($group_name) {
            $existing_group = $this->db->get_where($this->tables['groups'], ['name' => $group_name])->num_rows();
            if (!($existing_group !== 0)) {
            }
            $this->set_error('group_already_exists');
            return FALSE;
        } else {
            $this->set_error('group_name_required');
            return FALSE;
        }
    }
    public function update_group($group_id = FALSE, $group_name = FALSE, $additional_data = array())
    {
        if (!empty($group_id)) {
            $data = [];
            if (empty($group_name)) {
            }
            $existing_group = $this->db->get_where($this->tables['groups'], ['name' => $group_name])->row();
            if (!(isset($existing_group->id) && $existing_group->id != $group_id)) {
            }
            $this->set_error('group_already_exists');
            return FALSE;
        } else {
            return FALSE;
        }
    }
    public function delete_group($group_id = FALSE)
    {
        if (!(!$group_id || empty($group_id))) {
            $group = $this->group($group_id)->row();
            if (!($group->name == $this->config->item('admin_group', 'ion_auth'))) {
            }
            $this->trigger_events(['post_delete_group', 'post_delete_group_notallowed']);
            $this->set_error('group_delete_notallowed');
            return FALSE;
        } else {
            return FALSE;
        }
    }
    public function set_hook($event, $name, $class, $method, $arguments)
    {
        $this->_ion_hooks->{$event}[$name] = new stdClass();
        $this->_ion_hooks->{$event}[$name]->class = $class;
        $this->_ion_hooks->{$event}[$name]->method = $method;
        $this->_ion_hooks->{$event}[$name]->arguments = $arguments;
    }
    public function remove_hook($event, $name)
    {
        if (!isset($this->_ion_hooks->{$event}[$name])) {
        } else {
            unset($this->_ion_hooks->{$event}[$name]);
        }
    }
    public function remove_hooks($event)
    {
        if (!isset($this->_ion_hooks->{$event})) {
        } else {
            unset($this->_ion_hooks->{$event});
        }
    }
    protected function _call_hook($event, $name)
    {
        if (!(isset($this->_ion_hooks->{$event}[$name]) && method_exists($this->_ion_hooks->{$event}[$name]->class, $this->_ion_hooks->{$event}[$name]->method))) {
            return FALSE;
        } else {
            $hook = $this->_ion_hooks->{$event}[$name];
            return call_user_func_array([$hook->class, $hook->method], $hook->arguments);
        }
    }
    public function trigger_events($events)
    {
        if (is_array($events) && !empty($events)) {
            foreach ($events as $event) {
                $this->trigger_events($event);
            }
        } else {
            if (!(isset($this->_ion_hooks->{$events}) && !empty($this->_ion_hooks->{$events}))) {
            }
            foreach ($this->_ion_hooks->{$events} as $name => $hook) {
                $this->_call_hook($events, $name);
            }
        }
    }
    public function set_message_delimiters($start_delimiter, $end_delimiter)
    {
        $this->message_start_delimiter = $start_delimiter;
        $this->message_end_delimiter = $end_delimiter;
        return TRUE;
    }
    public function set_error_delimiters($start_delimiter, $end_delimiter)
    {
        $this->error_start_delimiter = $start_delimiter;
        $this->error_end_delimiter = $end_delimiter;
        return TRUE;
    }
    public function set_message($message)
    {
        $this->messages[] = $message;
        return $message;
    }
    public function messages()
    {
        $_output = '';
        foreach ($this->messages as $message) {
            $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
            $_output .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
        }
        return $_output;
    }
    public function messages_array($langify = TRUE)
    {
        if ($langify) {
            $_output = [];
            foreach ($this->messages as $message) {
                $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
                $_output[] = $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
            }
            return $_output;
        } else {
            return $this->messages;
        }
    }
    public function clear_messages()
    {
        $this->messages = [];
        return TRUE;
    }
    public function set_error($error)
    {
        $this->errors[] = $error;
        return $error;
    }
    public function errors()
    {
        $_output = '';
        foreach ($this->errors as $error) {
            $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
            $_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
        }
        return $_output;
    }
    public function errors_array($langify = TRUE)
    {
        if ($langify) {
            $_output = [];
            foreach ($this->errors as $error) {
                $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
                $_output[] = $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
            }
            return $_output;
        } else {
            return $this->errors;
        }
    }
    public function clear_errors()
    {
        $this->errors = [];
        return TRUE;
    }
    protected function _set_password_db($identity, $password)
    {
        $hash = $this->hash_password($password, $identity);
        if (!($hash === FALSE)) {
            $data = ['password' => $hash, 'remember_code' => NULL, 'forgotten_password_code' => NULL, 'forgotten_password_time' => NULL];
            $this->trigger_events('extra_where');
            $this->db->update($this->tables['users'], $data, [$this->identity_column => $identity]);
            return $this->db->affected_rows() == 1;
        } else {
            return FALSE;
        }
    }
    protected function _filter_data($table, $data)
    {
        $filtered_data = [];
        $columns = $this->db->list_fields($table);
        if (!is_array($data)) {
            return $filtered_data;
        } else {
            foreach ($columns as $column) {
                if (!array_key_exists($column, $data)) {
                } else {
                    $filtered_data[$column] = $data[$column];
                }
            }
            return $filtered_data;
        }
    }
    protected function _random_token($result_length = 32)
    {
        if (!(!isset($result_length) || intval($result_length) <= 8)) {
            if (!function_exists('random_bytes')) {
            }
            return bin2hex(random_bytes($result_length / 2));
        } else {
            $result_length = 32;
            if (!function_exists('random_bytes')) {
            }
            return bin2hex(random_bytes($result_length / 2));
        }
    }
    protected function _get_hash_parameters($identity = NULL)
    {
        $is_admin = FALSE;
        if (!$identity) {
            $params = FALSE;
            switch ($this->hash_method) {
                case 'bcrypt':
                    $params = ['cost' => $is_admin ? $this->config->item('bcrypt_admin_cost', 'ion_auth') : $this->config->item('bcrypt_default_cost', 'ion_auth')];
                case 'argon2':
                    $params = $is_admin ? $this->config->item('argon2_admin_params', 'ion_auth') : $this->config->item('argon2_default_params', 'ion_auth');
                default:
            }
            return $params;
        } else {
            $user_id = $this->get_user_id_from_identity($identity);
            if (!($user_id && $this->in_group($this->config->item('admin_group', 'ion_auth'), $user_id))) {
            }
            $is_admin = TRUE;
            $params = FALSE;
            switch ($this->hash_method) {
                case 'bcrypt':
                    $params = ['cost' => $is_admin ? $this->config->item('bcrypt_admin_cost', 'ion_auth') : $this->config->item('bcrypt_default_cost', 'ion_auth')];
                case 'argon2':
                    $params = $is_admin ? $this->config->item('argon2_admin_params', 'ion_auth') : $this->config->item('argon2_default_params', 'ion_auth');
                default:
            }
            return $params;
        }
    }
    protected function _get_hash_algo()
    {
        $algo = FALSE;
        switch ($this->hash_method) {
            case 'bcrypt':
                $algo = PASSWORD_BCRYPT;
            case 'argon2':
                $algo = PASSWORD_ARGON2I;
            default:
        }
        return $algo;
    }
    protected function _generate_selector_validator_couple($selector_size = 40, $validator_size = 128)
    {
        $selector = $this->_random_token($selector_size);
        $validator = $this->_random_token($validator_size);
        $validator_hashed = $this->hash_password($validator);
        $user_code = "{$selector}.{$validator}";
        return (object) ['selector' => $selector, 'validator_hashed' => $validator_hashed, 'user_code' => $user_code];
    }
    protected function _retrieve_selector_validator_couple($user_code)
    {
        if (!$user_code) {
            return FALSE;
        } else {
            $tokens = explode('.', $user_code);
            if (!(count($tokens) === 2)) {
            }
            return (object) ['selector' => $tokens[0], 'validator' => $tokens[1]];
        }
    }
    protected function _password_verify_sha1_legacy($identity, $password, $hashed_password_db)
    {
        $this->trigger_events('pre_sha1_password_migration');
        if ($this->config->item('store_salt', 'ion_auth')) {
            $query = $this->db->select('salt')->where($this->identity_column, $identity)->limit(1)->get($this->tables['users']);
            $salt_db = $query->row();
            if (!($query->num_rows() !== 1)) {
            }
            $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_unsuccessful']);
            return FALSE;
        } else {
            $salt_length = $this->config->item('salt_length', 'ion_auth');
            if ($salt_length) {
            }
            $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_unsuccessful']);
            return FALSE;
        }
    }
}
```

---

## File: application/models_decoded/Kelas_model.php

```php
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
            }
            $os = $this->agent->platform();
            $ip = $this->input->ip_address();
            return $this->insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip);
        } else {
            if ($this->agent->is_mobile()) {
            }
            $agent = 'unknown';
            if ($agent == 'unknown') {
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
            }
            $this->db->where('k.id_smt', $id_smt);
            return $this->db->get()->row();
        } else {
            $this->db->where('k.id_tp', $id_tp);
            if (!($id_smt != null)) {
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
            }
            $this->db->where('k.id_smt', $id_smt);
            return $this->db->get()->row();
        } else {
            $this->db->where('k.id_tp', $id_tp);
            if (!($id_smt != null)) {
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
            }
            if ($jenjang == '3') {
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
                if (isset($ret[$row->id_kelas])) {
                    array_push($ret[$row->id_kelas], $row);
                } else {
                    $ret[$row->id_kelas] = [];
                    array_push($ret[$row->id_kelas], $row);
                }
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
                if (!($row->id_mapel != '')) {
                } else {
                    if (isset($ret[$row->id_mapel][$row->id_kelas])) {
                    }
                    $ret[$row->id_mapel][$row->id_kelas] = [];
                    array_push($ret[$row->id_mapel][$row->id_kelas], $row);
                }
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
                if (isset($ret[$row->id_kelas])) {
                    array_push($ret[$row->id_kelas], $row);
                } else {
                    $ret[$row->id_kelas] = [];
                    array_push($ret[$row->id_kelas], $row);
                }
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
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        } else {
            $this->db->where('a.id_materi', $id_kjm);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
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
            }
            foreach ($result as $key => $row) {
                $ret[$row->jam_ke] = $row;
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
                $len_kls = strlen($row->id_kelas);
                $subs_jam = $len_kls + 10;
                $sisa = strlen($row->id_kjm) - $subs_jam;
                $len = $sisa === 3 ? 2 : 1;
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $len, 1);
                $ret[$jam] = $row;
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
                $len_kls = strlen($row->id_kelas);
                $subs_jam = $len_kls + 10;
                $sisa = strlen($row->id_kjm) - $subs_jam;
                $len = $sisa === 3 ? 2 : 1;
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
                $ret[$row->jadwal_materi][$jam] = $row;
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
            }
            foreach ($result as $key => $row) {
                $len_kls = strlen($row->id_kelas);
                $subs_jam = $len_kls + 10;
                $sisa = strlen($row->id_kjm) - $subs_jam;
                $len = $sisa === 3 ? 2 : 1;
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
                $row->materi_kelas = unserialize($row->materi_kelas ?? '');
                $ret[$row->id_mapel][$jam][$row->jenis] = $row;
            }
            return $ret;
        } else {
            $this->db->where_in('a.id_mapel', $arr_mapel);
            $this->db->where('a.id_kelas', $id_kelas);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $len_kls = strlen($row->id_kelas);
                $subs_jam = $len_kls + 10;
                $sisa = strlen($row->id_kjm) - $subs_jam;
                $len = $sisa === 3 ? 2 : 1;
                $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
                $row->materi_kelas = unserialize($row->materi_kelas ?? '');
                $ret[$row->id_mapel][$jam][$row->jenis] = $row;
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
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa][$row->jenis][$row->jadwal_materi][$row->jam_ke] = $row;
            }
            return $ret;
        } else {
            $this->db->where('a.id_mapel', $id_mapel);
            $this->db->where('MONTH(a.jadwal_materi)', $bulan)->where('YEAR(a.jadwal_materi)', $tahun);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa][$row->jenis][$row->jadwal_materi][$row->jam_ke] = $row;
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
            }
            foreach ($result as $key => $row) {
                $len_kls = strlen($id_kelas);
                $len_tp_smt = 2;
                $len_tahun = 4;
                $len_bln = 2;
                $len_hari = 2;
                $subs_bln = $len_kls + $len_tp_smt + $len_tahun;
                $subs_tgl = $subs_bln + $len_bln;
                $sisa = strlen($row->id_materi) - ($len_kls + 10);
                $len = $sisa === 3 ? 2 : 1;
                $bulan = substr($row->id_materi, $subs_bln, 2);
                $tgl = substr($row->id_materi, $subs_tgl, 2);
                $jam = substr($row->id_materi, strlen($row->id_materi) - $sisa, $len);
                $jenis = substr($row->id_materi, strlen($row->id_materi) - 1, 1);
                $ret[$jenis][$row->id_siswa][$bulan][$tgl][$jam] = $row;
            }
            return $ret;
        } else {
            $this->db->where('id_materi', $id_materi);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $len_kls = strlen($id_kelas);
                $len_tp_smt = 2;
                $len_tahun = 4;
                $len_bln = 2;
                $len_hari = 2;
                $subs_bln = $len_kls + $len_tp_smt + $len_tahun;
                $subs_tgl = $subs_bln + $len_bln;
                $sisa = strlen($row->id_materi) - ($len_kls + 10);
                $len = $sisa === 3 ? 2 : 1;
                $bulan = substr($row->id_materi, $subs_bln, 2);
                $tgl = substr($row->id_materi, $subs_tgl, 2);
                $jam = substr($row->id_materi, strlen($row->id_materi) - $sisa, $len);
                $jenis = substr($row->id_materi, strlen($row->id_materi) - 1, 1);
                $ret[$jenis][$row->id_siswa][$bulan][$tgl][$jam] = $row;
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
```

---

## File: application/models_decoded/Log_model.php

```php
<?php

class Log_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('user_agent');
    }
    public function saveLog($type, $desc)
    {
        $user_id = $this->ion_auth->user()->row()->id;
        $group = $this->ion_auth->get_users_groups($user_id)->row();
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
        } else {
            if ($this->agent->is_mobile()) {
            }
            $agent = 'Data user gagal di dapatkan';
        }
        $os = $this->agent->platform();
        $ip = $this->input->ip_address();
        $this->insertLog($user_id, $group->id, $group->name, $type, $desc, $agent, $os, $ip);
    }
    private function insertLog($id_user, $group_id, $group_name, $type, $desc, $agent, $os, $ip)
    {
        $data = array('id_user' => $id_user, 'id_group' => $group_id, 'name_group' => $group_name, 'log_desc' => $desc, 'address' => $ip, 'agent' => $agent, 'device' => $os);
        $this->db->insert('log', $data);
    }
    public function loadNotifikasi()
    {
    }
    public function loadChat()
    {
    }
    public function loadAktifitas($limit = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.first_name, b.last_name, d.name');
        $this->db->from('log a');
        $this->db->join('users b', 'b.id=a.id_user', 'left');
        $this->db->join('groups d', 'd.id=a.id_group');
        if (!($limit != null)) {
            $this->db->order_by('a.log_time', 'DESC');
            return $this->db->get()->result();
        } else {
            $this->db->limit($limit, 0);
            $this->db->order_by('a.log_time', 'DESC');
            return $this->db->get()->result();
        }
    }
    public function loadAktifitasSiswa($limit = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.first_name, b.last_name, d.name');
        $this->db->from('log a');
        $this->db->join('users b', 'b.id=a.id_user', 'left');
        $this->db->join('groups d', 'd.id=a.id_group');
        if (!($limit != null)) {
            $this->db->where('a.id_group', '3');
            $this->db->order_by('a.log_time', 'DESC');
            return $this->db->get()->result();
        } else {
            $this->db->limit($limit, 0);
            $this->db->where('a.id_group', '3');
            $this->db->order_by('a.log_time', 'DESC');
            return $this->db->get()->result();
        }
    }
}
```

---

## File: application/models_decoded/Master_model.php

```php
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
            } else {
                $ret[$row->tahun_lulus] = $row->tahun_lulus;
            }
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
            } else {
                $ret[$row->kelas_akhir] = $row->kelas_akhir;
            }
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
                } else {
                    $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
                }
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
```

---

## File: application/models_decoded/Post_model.php

```php
<?php

class Post_model extends CI_Model
{
    public function getPostUser($id_user)
    {
        $this->db->select('a.*, b.nama_guru, b.foto, (SELECT COUNT(post_comments.id_comment) FROM post_comments WHERE a.id_post = post_comments.id_post) AS jml');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        if (!($id_user != 0)) {
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        } else {
            $this->db->where('a.dari', $id_user);
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        }
    }
    public function getPostForUser($kepada, $kelas = null)
    {
        $this->db->select('a.*, b.nama_guru, b.foto, (SELECT COUNT(post_comments.id_comment) FROM post_comments WHERE a.id_post = post_comments.id_post) AS jml');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        if (!($kepada != null)) {
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        } else {
            $this->db->where('(a.kepada LIKE ' . $kepada . ') OR (a.kepada LIKE ' . $kelas . ')');
            $this->db->order_by('a.updated', 'desc');
            return $this->db->get()->result();
        }
    }
    public function getIdComments($id_post)
    {
        $this->db->select('id_comment');
        $this->db->where('id_post', $id_post);
        return $this->db->get('post_comments')->result();
    }
    public function getIdReplies($id_comment)
    {
        $this->db->select('id_reply');
        if (is_array($id_comment)) {
            $this->db->where_in('id_comment', $id_comment);
            return $this->db->get('post_reply')->result();
        } else {
            $this->db->where('id_comment', $id_comment);
            return $this->db->get('post_reply')->result();
        }
    }
}
```

---

## File: application/models_decoded/Rapor_model.php

```php
<?php

class Rapor_model extends CI_Model
{
    public function getKikdMapel($id, $id_tp, $id_smt)
    {
        $this->db->where('id_kikd', $id)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_kikd')->row();
    }
    public function getKikdMapelKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_mapel_kelas', $id_mapel . $id_kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_kikd')->result();
    }
    public function getKkm($id)
    {
        $this->db->where('id_kkm', $id);
        return $this->db->get('rapor_kkm')->row();
    }
    public function getArrKkm($ids)
    {
        $this->db->where_in('id_kkm', $ids);
        $result = $this->db->get('rapor_kkm')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_mapel] = $row;
            }
            return $ret;
        }
    }
    public function getRaporSetting($id_tp, $id_smt)
    {
        $this->db->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_admin_setting')->row();
    }
    public function getDetailSiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.*, c.*');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->order_by('b.nama', 'ASC');
        return $this->db->get()->result();
    }
    public function getDetailSiswaById($id_siswa, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.nama, a.nis, a.nisn, c.nama_kelas');
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->join('master_kelas c', 'b.id_kelas=c.id_kelas');
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('b.id_tp', $id_tp);
        $this->db->where('b.id_smt', $id_smt);
        $this->db->order_by('a.nama', 'ASC');
        return $this->db->get()->row();
    }
    public function cekNilaiHarianKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('p_rata_rata');
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->where('p_rata_rata !=', 'NULL');
        return $this->db->get()->num_rows();
    }
    public function getNilaiHarianKelas($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_nilai_harian', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllNilaiHarianKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function cekNilaiPtsKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('predikat');
        $this->db->from('rapor_nilai_pts');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->where('predikat !=', 'NULL');
        return $this->db->get()->num_rows();
    }
    public function getIdNilaiPts($arr_id)
    {
        $this->db->select('id_nilai_pts');
        $this->db->from('rapor_nilai_pts');
        $this->db->where_in('id_nilai_pts', $arr_id);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_nilai_pts] = $row;
            }
            return $ret;
        }
    }
    public function getNilaiPtsKelas($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_pts');
        $this->db->where('id_nilai_pts', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllNilaiPtsKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_pts');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function getEkstraKelas($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('nilai, predikat, deskripsi');
        $this->db->from('rapor_nilai_ekstra');
        $this->db->where('id_ekstra', $id_mapel);
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->row();
    }
    public function cekNilaiEkstraKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('id_nilai_ekstra');
        $this->db->from('rapor_nilai_ekstra');
        $this->db->where('id_ekstra', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->num_rows();
    }
    public function getNilaiEkstraKelas($id_ekstra, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_ekstra');
        $this->db->where('id_nilai_ekstra', $id_ekstra . $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllNilaiEkstraKelas($id_ekstra, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_ekstra');
        $this->db->where('id_ekstra', $id_ekstra);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function cekNilaiAkhirKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('id_nilai_akhir');
        $this->db->from('rapor_nilai_akhir');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->num_rows();
    }
    public function getNilaiAkhirKelas($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_nilai_harian', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllNilaiAkhirKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function getNilaiAkhirByMapel($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getDeskripsiSikap($kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_kelas', $kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_data_sikap')->result();
    }
    public function getAllDeskripsiSikap($kelas)
    {
        $this->db->where('id_kelas', $kelas);
        return $this->db->get('rapor_data_sikap')->result();
    }
    public function getDeskripsiSikapByJenis($kelas, $jenis, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_data_sikap');
        $this->db->where('id_kelas', $kelas);
        $this->db->where('jenis', $jenis);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getNilaiSikapKelas($id_kelas, $id_siswa, $id_tp, $id_smt, $jenis)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');
        $this->db->where('id_nilai_sikap', $id_kelas . $id_siswa . $id_tp . $id_smt . $jenis);
        return $this->db->get()->row();
    }
    public function getAllNilaiSikapKelas($id_kelas)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->get()->result();
    }
    public function getNilaiSikapByJenis($id_kelas, $jenis, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('jenis', $jenis);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getNilaiSikapByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getDeskripsiCatatanByJenis($kelas, $jenis, $id_tp, $id_smt)
    {
        $this->db->where('jenis', $jenis)->where('id_kelas', $kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_data_catatan')->result();
    }
    public function getCatatanKelas($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_catatan_wali');
        $this->db->where('id_catatan_wali', $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllCatatanKelas($id_kelas)
    {
        $this->db->select('*');
        $this->db->from('rapor_catatan_wali');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->get()->result();
    }
    public function getRankingKelas($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_prestasi');
        $this->db->where('id_ranking', $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllRankingKelas($id_kelas)
    {
        $this->db->select('*');
        $this->db->from('rapor_prestasi');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->get()->result();
    }
    public function getAllDeskripsiFisikKelas()
    {
        $result = $this->db->get('rapor_data_fisik')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_kelas][$row->id_tp][$row->id_smt] = $row;
            }
            return $ret;
        }
    }
    public function getAllRaporFisik()
    {
        $result = $this->db->get('rapor_fisik')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa][$row->id_tp][$row->id_smt] = $row;
            }
            return $ret;
        }
    }
    public function getDeskripsiFisikKelas($kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_fisik', $kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_data_fisik')->row();
    }
    public function getFisikKelas($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->from('rapor_fisik');
        $this->db->where('id_fisik', $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getAllFisikKelas($id_kelas)
    {
        $this->db->select('*');
        $this->db->from('rapor_fisik');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->get()->result();
    }
    public function getJmlNilaiMapelHarianSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('p_rata_rata, k_rata_rata, jml');
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->row();
    }
    public function getNilaiMapelHarianSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('p1,p2,p3,p4,p5,k1,k2,k3,k4,k5');
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->row();
    }
    public function getArrNilaiMapelHarianSiswa($ids_mapel, $ids_siswa, $id_tp, $id_smt)
    {
        $this->db->select('p1,p2,p3,p4,p5,k1,k2,k3,k4,k5,id_mapel,id_siswa');
        $this->db->from('rapor_nilai_harian');
        $this->db->where_in('id_mapel', $ids_mapel);
        $this->db->where_in('id_siswa', $ids_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $nilais = $this->db->get()->result();
        $rest = [];
        foreach ($nilais as $nilai) {
            $rest[$nilai->id_siswa][$nilai->id_mapel] = $nilai;
        }
        return $rest;
    }
    public function getNilaiMapelPtsSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('nilai');
        $this->db->from('rapor_nilai_pts');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->row();
    }
    public function getArrNilaiMapelPtsSiswa($ids_mapel, $ids_siswa, $id_tp, $id_smt)
    {
        $this->db->select('nilai, id_mapel, id_siswa');
        $this->db->from('rapor_nilai_pts');
        $this->db->where_in('id_mapel', $ids_mapel);
        $this->db->where_in('id_siswa', $ids_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $nilais = $this->db->get()->result();
        $rest = [];
        foreach ($nilais as $nilai) {
            $rest[$nilai->id_siswa][$nilai->id_mapel] = $nilai;
        }
        return $rest;
    }
    public function getNilaiMapelPasSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('nilai,akhir');
        $this->db->from('rapor_nilai_akhir');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->row();
    }
    public function getNilaiRapor($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('a.p_rata_rata, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as nilai_pas, b.akhir as nilai, b.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_nilai_harian', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row_array();
    }
    public function getNilaiMapelByKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.p_rata_rata, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as nilai_pas, b.akhir as nilai, b.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getNilaiRaporByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.id_nilai_harian, a.id_siswa, a.id_mapel, a.p_rata_rata, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as nilai_pas, b.akhir as nilai, b.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getPrestasiByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('id_siswa, ranking, deskripsi as rank_deskripsi, p1, p1_desk, p2, p2_desk, p3, p3_desk');
        $this->db->from('rapor_prestasi');
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $ranks = $this->db->get()->result();
        $rest = [];
        foreach ($ranks as $rank) {
            $rest[$rank->id_siswa] = $rank;
        }
        return $rest;
    }
    public function getCatatanWaliByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('id_siswa, nilai, deskripsi as saran');
        $this->db->from('rapor_catatan_wali');
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $desks = $this->db->get()->result();
        $rest = [];
        foreach ($desks as $desk) {
            $rest[$desk->id_siswa] = $desk;
        }
        return $rest;
    }
    public function getRaporDeskripsi($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('b.ranking, b.deskripsi as rank_deskripsi, b.p1, b.p1_desk, b.p2, b.p2_desk, b.p3, b.p3_desk,' . ' c.nilai, c.deskripsi as saran');
        $this->db->from('rapor_prestasi b');
        $this->db->join('rapor_catatan_wali c', 'c.id_catatan_wali=b.id_ranking', 'left');
        $this->db->where('b.id_ranking', $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }
    public function getDummyDeskripsiSpiritual()
    {
        return ['berdoa sebelum dan sesudah melakukan kegiatan', 'menjalankan ibadah sesuai dengan agamanya', 'memberi salam pada saat awal dan akhir kegiatan', 'bersyukur atas nikmat dan karunia Tuhan Yang Maha Esa', 'mensyukuri kemampuan manusia dalam mengendalikan diri', 'bersyukur ketika berhasil mengerjakan sesuatu', 'berserah diri (tawakal) kepada Tuhan setelah berikhtiar atau melakukan usaha', 'memelihara hubungan baik dengan sesama umat', 'bersyukur sebagai bangsa Indonesia', 'menghormati orang lain yang menjalankan ibadah sesuai dengan agamanya'];
    }
    public function getDummyDeskripsiSosial()
    {
        return ['jujur', 'disiplin', 'tanggung jawab', 'santun', 'percaya diri', 'peduli', 'toleransi', 'gotong royong', 'rajin', 'tidak mudah menyerah'];
    }
    public function getDummyDeskripsiAbsensi()
    {
        return ['Kehadiran cukup baik namun perlu ditingkatkan.', 'Usahakan hadir setiap hari.', 'Jangan terlalu banyak alpa, diharapkan selalu hadir ke sekolah', 'Kehadiranmu sangat jarang sekali'];
    }
    public function getDummyDeskripsiCatatan()
    {
        return ['Selalu berusaha untuk mematuhi tata tertib sekolah dan patuh terhadap Guru.', 'Selalu berusaha untuk mandiri dan tepat waktu dalam mengerjakan tugas.', 'Mempunyai kemampuan dan motivasi yang tinggi untuk menggunakan waktu secara efisien.', 'Diharapkan merubah penampilannya menjadi lebih rapi, seperti tentang potong rambut dan cara berpakaian.', 'Masih perlu memperbanyak teman bergaul dan teman diskusi, kurangi aktifitas menyendiri.', 'Diharapkan dapat meningkatkan komitmennya untuk lebih serius saat mengerjakan tugas dan tidak mudah menyerah.'];
    }
    public function getDummyDeskripsiRanking()
    {
        return ['Prestasinya sangat baik, perlu dipertahankan.', 'Prestasi baik, perlu dipertahankan dan dtingkatkan.', 'Prestasi cukup, perlu ditingkatkan belajar dan berdoa.', 'Perlu ditingkatkan belajarnya, jangan lupa berdoa.', 'Perlu dimaksimalkan belajarnya, usaha keras dan berdoa.', 'Perlu usaha keras, maksimalkan belajarnya, lebih giat berdoa dan beribadah.'];
    }
    public function getDummyDeskripsiFisik($jenis)
    {
        if ($jenis == '1') {
            return ['Baik', 'Kurang peka', 'Telinga perlu dibersihkan', ''];
        } else {
            if ($jenis == '2') {
            }
            if ($jenis == '3') {
            }
            return ['Tubuh sehat dan kuat', 'Mudah kecapekan', 'Kebersihan badan kurang terjaga', ''];
        }
    }
    public function getKenaikanSiswa($id_kelas, $id_tp, $id_smt, $level = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.id_kelas, c.nama_kelas, c.level_id, d.naik');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        if (!($level != null)) {
            if (!($id_kelas != null)) {
            }
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            return $this->db->get()->result();
        } else {
            $this->db->where('c.level_id', $level);
            if (!($id_kelas != null)) {
            }
            $this->db->where('a.id_kelas', $id_kelas);
            $this->db->where('a.id_tp', $id_tp);
            $this->db->where('a.id_smt', $id_smt);
            return $this->db->get()->result();
        }
    }
    public function getSiswaLulus($id_tp, $id_smt, $level)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('b.*, c.nama_kelas as kelas_akhir, d.naik');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        $this->db->where('c.level_id', $level);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
    }
    public function getJumlahLulus($id_tp, $id_smt, $level)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.id_kelas, c.nama_kelas, c.level_id, d.naik');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        $this->db->where('c.level_id', $level);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->count_all_results();
    }
    public function getKenaikanRapor($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('a.id_kelas, a.id_siswa, d.naik');
        $this->db->from('kelas_siswa a');
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $ress = $this->db->get()->result();
        $ret = [];
        foreach ($ress as $res) {
            $ret[$res->id_siswa] = $res->naik;
        }
        return $ret;
    }
    public function getAllRaporSetting()
    {
        $result = $this->db->get('rapor_admin_setting')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_tp][$row->id_smt] = $row;
            }
            return $ret;
        }
    }
    public function getAllKkm()
    {
        $result = $this->db->get('rapor_kkm')->result();
        $ret = [];
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_kelas][$res->jenis][$res->id_mapel] = $res;
        }
        return $ret;
    }
    public function getAllKkmRaporAkhir($kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_kelas', $kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        $result = $this->db->get('rapor_kkm')->result();
        $ret = [];
        foreach ($result as $res) {
            $ret[$res->jenis][$res->id_mapel] = $res;
        }
        return $ret;
    }
    public function getAllNilaiAkhir()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_tp, a.id_smt, a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata,' . ' a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_siswa] = $res;
        }
        return $ret;
    }
    public function getDistinctTahunBukuNilai()
    {
        $this->db->select('tp');
        $this->db->distinct();
        $result = $this->db->get('buku_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->tp] = $row->tp;
        }
        return $ret;
    }
    public function getDistinctSmtBukuNilai()
    {
        $this->db->select('smt');
        $this->db->distinct();
        $result = $this->db->get('buku_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->smt] = $row->smt;
        }
        return $ret;
    }
    public function getDistinctKelasBukuNilai()
    {
        $this->db->select('kelas');
        $this->db->distinct();
        $result = $this->db->get('buku_nilai')->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kelas] = $row->kelas;
        }
        return $ret;
    }
    public function getFisikBySiswa($id_siswa)
    {
        $this->db->select('tp, fisik');
        $this->db->from('buku_nilai');
        $this->db->where('id_siswa', $id_siswa);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->tp] = $row;
            }
            return $ret;
        }
    }
    public function getDataKumpulanRapor($kelas = null, $tp = null, $smt = null)
    {
        $this->db->select('*');
        $this->db->from('buku_nilai a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        if (!($tp != null)) {
            if (!($smt != null)) {
            }
            $this->db->where('a.smt', $smt);
            if (!($kelas != null)) {
            }
            $this->db->where('a.kelas', $kelas);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        } else {
            $this->db->where('a.tp', $tp);
            if (!($smt != null)) {
            }
            $this->db->where('a.smt', $smt);
            if (!($kelas != null)) {
            }
            $this->db->where('a.kelas', $kelas);
            $result = $this->db->get()->result();
            $ret = [];
            if (!$result) {
            }
            foreach ($result as $key => $row) {
                $ret[$row->id_siswa] = $row;
            }
            return $ret;
        }
    }
    public function deleteNilaiRapor()
    {
        $this->db->empty_table('rapor_nilai_harian');
        $this->db->empty_table('rapor_nilai_akhir');
        $this->db->empty_table('rapor_naik');
        $this->db->empty_table('rapor_nilai_pts');
        $this->db->empty_table('rapor_prestasi');
        $this->db->empty_table('rapor_catatan_wali');
        $this->db->empty_table('rapor_fisik');
        $this->db->empty_table('rapor_nilai_ekstra');
        $this->db->empty_table('rapor_nilai_sikap');
    }
    public function getAllNilaiRapor($ids_siswa = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_tp, a.id_smt, a.id_mapel, a.id_siswa, a.p_rata_rata, a.p_predikat, a.p_deskripsi,' . ' a.k_rata_rata, a.k_predikat, a.k_deskripsi,' . ' b.nilai as nilai_pas, b.akhir as nilai_rapor, b.predikat as rapor_predikat,' . ' c.*, d.*, e. nama, e.uid, f.naik,' . ' g.nilai as nilai_pts, g.predikat as pts_predikat,' . ' h. id_kelas, h.nama_kelas, h.level_id, i.nama_jurusan, k.nama_guru,' . ' l.ranking, l.deskripsi as rank_deskripsi, l.p1, l.p1_desk, l.p2, l.p2_desk, l.p3, l.p3_desk,' . ' m.nilai as absen, m.deskripsi as saran, n.kondisi, n.tinggi, n.berat, p.kode as mapel');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->join('master_tp c', 'c.id_tp=a.id_tp', 'left');
        $this->db->join('master_smt d', 'd.id_smt=a.id_smt', 'left');
        $this->db->join('master_siswa e', 'e.id_siswa=a.id_siswa', 'left');
        $this->db->join('rapor_naik f', 'a.id_siswa=f.id_siswa AND a.id_tp=f.id_tp AND a.id_smt=f.id_smt', 'left');
        $this->db->join('rapor_nilai_pts g', 'g.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->join('master_kelas h', 'a.id_kelas=h.id_kelas AND a.id_tp=h.id_tp AND a.id_smt=h.id_smt', 'left');
        $this->db->join('master_jurusan i', 'h.jurusan_id=i.id_jurusan', 'left');
        $this->db->join('jabatan_guru j', 'a.id_kelas=j.id_kelas AND a.id_tp=j.id_tp AND a.id_smt=j.id_smt', 'left');
        $this->db->join('master_guru k', 'j.id_guru=k.id_guru', 'left');
        $this->db->join('rapor_prestasi l', 'a.id_siswa=l.id_siswa AND a.id_tp=l.id_tp AND a.id_smt=l.id_smt', 'left');
        $this->db->join('rapor_catatan_wali m', 'a.id_siswa=m.id_siswa AND a.id_tp=m.id_tp AND a.id_smt=m.id_smt', 'left');
        $this->db->join('rapor_fisik n', 'a.id_siswa=n.id_siswa AND a.id_tp=n.id_tp AND a.id_smt=n.id_smt', 'left');
        $this->db->join('master_mapel p', 'a.id_mapel=p.id_mapel', 'left');
        if (!($ids_siswa != null)) {
            return $this->db->get()->result();
        } else {
            $this->db->where_in('a.id_siswa', $ids_siswa);
            return $this->db->get()->result();
        }
    }
    public function getAllEkstra()
    {
        $this->db->select('*');
        $this->db->from('kelas_ekstra');
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        } else {
            foreach ($result as $key => $row) {
                $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = unserialize($row->ekstra ?? '');
            }
            return $ret;
        }
    }
    public function getAllNilaiEkstra($ids_siswa = null)
    {
        $this->db->select('a.*, b.nama_ekstra, b.kode_ekstra');
        $this->db->from('rapor_nilai_ekstra a');
        $this->db->join('master_ekstra b', 'a.id_ekstra=b.id_ekstra', 'left');
        if (!($ids_siswa != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $res) {
                $ret[$res->id_tp][$res->id_smt][$res->id_siswa][] = $res;
            }
            return $ret;
        } else {
            $this->db->where_in('a.id_siswa', $ids_siswa);
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $res) {
                $ret[$res->id_tp][$res->id_smt][$res->id_siswa][] = $res;
            }
            return $ret;
        }
    }
    public function getAllNilaiSikap($ids_siswa = null)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');
        if (!($ids_siswa != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $res) {
                $ret[$res->id_tp][$res->id_smt][$res->id_siswa][$res->jenis] = $res;
            }
            return $ret;
        } else {
            $this->db->where_in('id_siswa', $ids_siswa);
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $res) {
                $ret[$res->id_tp][$res->id_smt][$res->id_siswa][$res->jenis] = $res;
            }
            return $ret;
        }
    }
    public function getAllFisik($ids_siswa = null)
    {
        $this->db->select('id_tp, id_smt, id_siswa, kondisi, tinggi, berat');
        $this->db->from('rapor_fisik');
        if (!($ids_siswa != null)) {
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $res) {
                $ret[$res->id_siswa][$res->id_tp][$res->id_smt] = $res;
            }
            return $ret;
        } else {
            $this->db->where_in('id_siswa', $ids_siswa);
            $result = $this->db->get()->result();
            $ret = [];
            foreach ($result as $res) {
                $ret[$res->id_siswa][$res->id_tp][$res->id_smt] = $res;
            }
            return $ret;
        }
    }
    function exists($uid, $tp, $smt, $kelas)
    {
        $this->db->where('uid', $uid)->where('tp', $tp)->where('smt', $smt)->where('kelas', $kelas);
        $query = $this->db->get('buku_nilai');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
```

---

## File: application/models_decoded/Settings_model.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Settings_model extends CI_Model
{
    public function not_admin()
    {
        $this->db->select('a.id');
        $this->db->from('users a');
        $this->db->join('users_groups b', 'a.id=b.user_id');
        $this->db->where_not_in('b.group_id', ['1']);
        return $this->db->get()->result();
    }
    public function truncate($table)
    {
        $this->load->helper('file');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($table as $tb) {
            $this->db->truncate($tb);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        delete_files('./uploads/bank_soal/');
        $users = $this->not_admin();
        foreach ($users as $user) {
            $this->db->delete('users', array('id' => $user->id));
        }
        return;
    }
    public function getSetting()
    {
        return $this->db->get('setting')->row();
    }
    function toJSON($table)
    {
        $query = $this->db->get($table);
        return json_encode($query->result(), JSON_PRETTY_PRINT);
    }
    function rowSize($table)
    {
        $query = $this->db->get($table);
        return $query->num_rows();
    }
}
```

---

## File: application/models_decoded/Users_model.php

```php
<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Users_model extends CI_Model
{
    public function getDatausers($id = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('users.id, username, first_name, last_name, email, FROM_UNIXTIME(created_on) as created_on, last_login, active, groups.name as level');
        $this->datatables->from('users_groups');
        $this->datatables->join('users', 'users_groups.user_id=users.id');
        $this->datatables->join('groups', 'users_groups.group_id=groups.id');
        if (!($id !== null)) {
            return $this->datatables->generate();
        } else {
            $this->datatables->where('users.id !=', $id);
            return $this->datatables->generate();
        }
    }
    public function getLevelGuru()
    {
        return $this->db->get('level_guru')->result();
    }
    public function getDataadmin()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('users.id, username, first_name, last_name, email, FROM_UNIXTIME(created_on) as created_on, last_login, active, groups.name as level');
        $this->datatables->from('users_groups');
        $this->datatables->join('users', 'users_groups.user_id=users.id');
        $this->datatables->join('groups', 'users_groups.group_id=groups.id');
        $this->datatables->where('group_id =', 1);
        return $this->datatables->generate();
    }
    public function getUserGuru($tp, $smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('a.id_guru, a.nama_guru, a.username, a.password, c.level, e.id, ' . '(SELECT COUNT(id) FROM users WHERE e.username = a.username) AS aktif, ' . '(SELECT COUNT(login) FROM login_attempts WHERE login_attempts.login = a.username) AS reset');
        $this->datatables->from('master_guru a');
        $this->datatables->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->datatables->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->datatables->join('users e', 'a.username=e.username', 'left');
        return $this->datatables->generate();
    }
    public function getDataGuru($id)
    {
        $this->db->select('*');
        $this->db->from('master_guru');
        $this->db->where('id_guru', $id);
        return $this->db->get()->row();
    }
    public function getDetailGuru($id)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_guru, a.nama_guru, a.username, a.password, a.email, c.level, e.id, (SELECT COUNT(id) FROM users WHERE e.username = a.username) AS aktif');
        $this->db->from('master_guru a');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('users e', 'a.username=e.username', 'left');
        $this->db->where('a.id_guru', $id);
        return $this->db->get()->row();
    }
    public function getGuruByUsername($username)
    {
        $this->db->where('username', $username);
        return $this->db->get('master_guru')->row();
    }
    public function getSiswaByUsername($username)
    {
        $this->db->where('username', $username);
        return $this->db->get('master_siswa')->row();
    }
    public function getUsers($username)
    {
        $this->db->where('username', $username);
        return $this->db->get('users')->row();
    }
    public function getGroupSiswa()
    {
        $this->db->select('*');
        $this->db->from('users_groups a');
        $this->db->join('users b', 'a.user_id=b.id', 'left');
        $this->db->where('group_id', 3);
        return $this->db->get()->result();
    }
    public function getKelas($tp, $smt)
    {
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        return $this->db->get('master_kelas')->result();
    }
    public function getMapel()
    {
        return $this->db->get('master_mapel')->result();
    }
    public function getUserSiswaPage($id_tp, $id_smt, $offset, $limit, $search = null, $sort = null, $order = null)
    {
        $this->db->select('a.id_siswa, a.nis, a.foto, a.nama, a.username, a.password, d.id_kelas, ' . 'f.nama_kelas, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif, ' . '(SELECT COUNT(login) FROM login_attempts WHERE login_attempts.login = a.username) AS reset');
        $this->db->from('master_siswa a');
        $this->db->limit($limit, $offset);
        $this->db->join('kelas_siswa d', 'd.id_siswa=a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
        $this->db->order_by('f.nama_kelas', 'ASC');
        $this->db->order_by('a.nama', 'ASC');
        if (!($search != null)) {
            return $this->db->get()->result();
        } else {
            $this->db->like('a.nama', $search);
            $this->db->or_like('a.nis', $search);
            $this->db->or_like('a.nisn', $search);
            return $this->db->get()->result();
        }
    }
    public function getUserSiswaTotalPage($search = null)
    {
        $this->db->select('id_siswa');
        $this->db->from('master_siswa');
        if (!($search != null)) {
            return $this->db->get()->num_rows();
        } else {
            $this->db->like('nama', $search);
            $this->db->or_like('nis', $search);
            $this->db->or_like('nisn', $search);
            return $this->db->get()->num_rows();
        }
    }
    public function getUserSiswa($tp, $smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('a.id_siswa, a.nis,.a.nama, a.username, a.password, c.nama_kelas, d.id, (SELECT COUNT(id) FROM users WHERE d.username = a.username) AS aktif');
        $this->datatables->from('master_siswa a');
        $this->datatables->join('kelas_siswa b', 'b.id_siswa=a.id_siswa AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->datatables->join('master_kelas c', 'c.id_kelas=b.id_kelas', 'left');
        $this->datatables->join('users d', 'd.username=a.username', 'left');
        return $this->datatables->generate();
    }
    public function getDataSiswa($id)
    {
        $this->db->select('nis, nisn, nama, username, password');
        $this->db->from('master_siswa');
        $this->db->where('id_siswa', $id);
        return $this->db->get()->row();
    }
    public function getSiswaAktif()
    {
        $this->db->select('a.id_siswa, a.nis, a.nisn, a.username, a.password, a.nama, c.id, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif');
        $this->db->join('users c', 'a.username=c.username', 'left');
        return $this->db->get('master_siswa a')->result();
    }
    public function getGuruAktif()
    {
        $this->db->select('a.id_guru, c.id, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif');
        $this->db->join('users c', 'a.username=c.username', 'left');
        return $this->db->get('master_guru a')->result();
    }
}
```

---

