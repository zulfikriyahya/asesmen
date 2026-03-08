## File: application/models_decoded/Cbt_model.php

```php
<?php

class Cbt_model extends CI_Model
{
    public function get_where($table, $pk, $id, $join = null, $order = null)
    {
        $this->db->from($table);
        foreach ($join as $table => $field) {
            $this->db->join($table, $field);
        }
        foreach ($order as $field => $sort) {
            $this->db->order_by($field, $sort);
        }
        if (!($order !== null)) {
        }
        $this->db->select('*');
        if (!($join !== null)) {
        }
        $this->db->where($pk, $id);
        return $query;
        $query = $this->db->get();
    }
    public function saveLog($id_siswa, $id_jadwal, $type, $desc)
    {
        $ip = $this->input->ip_address();
        return $this->insertLog($id_siswa, $id_jadwal, $type, $desc, $agent, $os, $ip);
        $agent = $this->agent->mobile();
        $agent = $this->agent->browser() . ' ' . $this->agent->version();
        $os = $this->agent->platform();
        $agent = 'unknown';
        return 'error';
        if ($this->agent->is_browser()) {
        }
        if ($agent == 'unknown') {
        }
        if ($this->agent->is_mobile()) {
        }
    }
    private function insertLog($id_siswa, $id_jadwal, $type, $desc, $agent, $os, $ip)
    {
        $this->db->set('log_desc', $desc);
        return $insert;
        $insert = $this->db->insert('log_ujian', $data);
        if ($log != null) {
        }
        $data = array('id_log' => $id_siswa . '0' . $id_jadwal . $type, 'id_siswa' => $id_siswa, 'id_jadwal' => $id_jadwal, 'log_type' => $type, 'log_desc' => $desc, 'address' => $ip, 'agent' => $agent, 'device' => $os);
        $this->db->set('log_type', $type);
        $log = $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . $type)->get('log_ujian')->row();
        $this->db->where('id_log', $id_siswa . '0' . $id_jadwal . $type);
        $insert = $this->db->update('log_ujian');
    }
    public function getDataSiswa($username, $id_tp, $id_smt)
    {
        $this->db->join('kelas_siswa b', 'a.id_siswa=b.id_siswa AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->where('username', $username);
        $query = $this->db->get()->row();
        $this->db->select('a.id_siswa, a.nisn, a.nis, a.nama, a.jenis_kelamin, a.username, a.password, a.agama, a.foto,' . ' b.id_kelas_siswa, b.id_tp, b.id_smt, b.id_siswa, b.id_kelas,' . ' c.nama_kelas, c.kode_kelas, c.level_id, ' . ' d.kelas_id, d.ruang_id, d.sesi_id');
        $this->db->from('master_siswa a');
        $this->db->join('cbt_sesi_siswa d', 'a.id_siswa=d.siswa_id', 'left');
        $this->db->join('master_kelas c', 'b.id_kelas=c.id_kelas AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt, 'left');
        return $query;
    }
    public function getDataSiswaById($id_tp, $id_smt, $idSiswa)
    {
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->select('b.id_siswa, b.nama, b.jenis_kelamin, b.nis, b.nisn, b.username, b.password,' . ' b.foto, c.sesi_id, d.kode_ruang, e.kode_sesi, f.nama_kelas, g.nomor_peserta,' . ' h.set_siswa, i.kode_ruang as ruang_kelas, j.kode_sesi as sesi_kelas');
        $this->db->join('cbt_kelas_ruang h', 'h.id_kelas=a.id_kelas', 'left');
        $this->db->from('kelas_siswa a');
        $this->db->join('cbt_sesi e', 'e.id_sesi=c.sesi_id', 'left');
        $this->db->join('cbt_ruang i', 'i.id_ruang=h.id_ruang', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=c.ruang_id', 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->join('cbt_sesi j', 'j.id_sesi=h.id_sesi', 'left');
        return $this->db->get()->row();
        $this->db->where('a.id_siswa', $idSiswa);
        $this->db->join('cbt_sesi_siswa c', 'c.siswa_id=a.id_siswa', 'left');
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getWaktuSesiById($id_sesi)
    {
        $this->db->select('*');
        $result = $this->db->get('cbt_sesi')->row();
        return $result;
        $this->db->where('id_sesi', $id_sesi);
    }
    public function getAllRuang()
    {
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_ruang] = $row->kode_ruang;
        }
        $this->db->select('id_ruang, nama_ruang, kode_ruang');
        if (!$result) {
        }
        $result = $this->db->get('cbt_ruang')->result();
        $ret = [];
    }
    public function getKelasByLevel($level, $arrKelas)
    {
        $result = $this->db->get('master_kelas')->result();
        return $result;
        $this->db->where('level_id', $level);
        $this->db->select('id_kelas, kode_kelas');
        $this->db->where_in('id_kelas', $arrKelas);
    }
    public function getAllJurusan()
    {
        if (!$result) {
        }
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_jurusan] = $row->kode_jurusan;
        }
        $result = $this->db->get('master_jurusan')->result();
        $ret = [];
    }
    public function getPengawas($id_pengawas)
    {
        $this->db->from('cbt_pengawas');
        return $this->db->get()->row();
        $this->db->where('id_pengawas', $id_pengawas);
        $this->db->select('id_pengawas, id_jadwal, id_tp, id_smt, id_ruang, id_sesi, id_guru');
    }
    public function getPengawasByGuru($tp, $smt, $id_guru)
    {
        $this->db->from('cbt_pengawas a');
        return $this->db->get()->result();
        $this->db->join('cbt_jadwal b', 'b.id_jadwal=a.id_jadwal');
        $this->db->select('a.id_pengawas, a.id_jadwal, a.id_tp, a.id_smt, a.id_ruang, a.id_sesi, a.id_guru,' . ' b.id_jadwal, b.tgl_mulai, b.tgl_selesai, c.bank_kode, d.kode_jenis');
        $this->db->where('a.id_tp', $tp);
        $this->db->join('cbt_bank_soal c', 'b.id_bank=c.id_bank');
        $this->db->like('a.id_guru', $id_guru);
        $this->db->where('a.id_smt', $smt);
        $this->db->join('cbt_jenis d', 'd.id_jenis=b.id_jenis', 'left');
    }
    public function getPengawasByJadwal($tp, $smt, $id_jadwal, $sesi = null, $ruang = null)
    {
        $this->db->select('id_pengawas, id_guru');
        $this->db->where('id_tp', $tp);
        if (!($sesi != null)) {
        }
        return $this->db->get()->result();
        if (!($ruang != null)) {
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->where('id_ruang', $ruang);
        $this->db->where('id_sesi', $sesi);
        $this->db->from('cbt_pengawas');
        $this->db->where('id_smt', $smt);
    }
    public function getAllPengawas($tp, $smt, $ruang = null, $sesi = null)
    {
        if (!($sesi != null)) {
        }
        $this->db->where('id_sesi', $sesi);
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal][$row->id_ruang][$row->id_sesi] = $row;
        }
        $ret = [];
        $result = $this->db->get()->result();
        return $ret;
        $this->db->select('id_pengawas, id_jadwal, id_ruang, id_sesi, id_guru');
        if (!$result) {
        }
        $this->db->where('id_ruang', $ruang);
        $this->db->where('id_smt', $smt);
        $this->db->where('id_tp', $tp);
        $this->db->from('cbt_pengawas');
        if (!($ruang != null)) {
        }
    }
    public function getDistinctRuang($tp, $smt, $arrKelas)
    {
        $this->db->join('cbt_ruang b', 'b.id_ruang=a.ruang_id');
        $this->db->where_in('kelas_id', $arrKelas);
        $this->db->select('a.ruang_id, a.sesi_id, b.kode_ruang, b.nama_ruang, c.kode_sesi, c.nama_sesi');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->distinct('a.ruang_id');
        if (!(count($arrKelas) > 0)) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->ruang_id][$row->sesi_id] = $row;
        }
        return $ret;
        $ret = [];
        if (!$result) {
        }
        $this->db->join('cbt_sesi c', 'c.id_sesi=a.sesi_id');
        $this->db->order_by('c.nama_sesi', 'ASC');
        $this->db->order_by('b.nama_ruang', 'ASC');
        $result = $this->db->get()->result();
    }
    public function getKelasUjian($kelas_id)
    {
        $this->db->select('kelas_id, ruang_id, sesi_id');
        if (!$result) {
        }
        $this->db->where('kelas_id', $kelas_id);
        foreach ($result as $key => $row) {
            $ret[$row->ruang_id][$row->sesi_id][] = $row->kelas_id;
        }
        $this->db->from('cbt_sesi_siswa');
        return $ret;
        $result = $this->db->get()->result();
        $ret = [];
    }
    public function getDistinctKelasLevel($tp, $smt, $arrLevel)
    {
        $this->db->from('master_kelas');
        $result = $this->db->get()->result();
        $this->db->where_in('level_id', $arrLevel);
        $this->db->select('id_kelas, level_id');
        $this->db->where('id_smt', $smt);
        $this->db->where('id_tp', $tp);
        return $result;
        $this->db->distinct();
    }
    public function getAllJenisUjian()
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_jenis] = $row->kode_jenis;
        }
        return $ret;
        $this->db->select('id_jenis, nama_jenis, kode_jenis');
        if (!$result) {
        }
        $result = $this->db->get('cbt_jenis')->result();
        $ret[''] = 'Jenis Penilaian :';
    }
    public function getAllJenisUjianByArrJenis($arrJenis)
    {
        if (!$result) {
        }
        return $ret;
        $result = $this->db->get('cbt_jenis')->result();
        $this->db->where_in('id_jenis', $arrJenis);
        foreach ($result as $key => $row) {
            $ret[$row->id_jenis] = $row->kode_jenis;
        }
        $ret[''] = 'Jenis Penilaian :';
    }
    public function getPengawasHariIni($tgl)
    {
        $this->db->join('cbt_pengawas b', 'b.id_jadwal=a.id_jadwal');
        $this->db->where('status', '1');
        return $this->db->get()->result();
        $this->db->where("a.tgl_mulai <= '{$tgl}' AND a.tgl_selesai >= '{$tgl}'");
        $this->db->from('cbt_jadwal a');
    }
    public function getJadwalGuru($tp, $smt, $guru)
    {
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_guru_id=' . $guru);
        $this->db->select('a.id_jadwal, a.tgl_mulai, b.bank_kode, b.bank_kelas');
        $this->db->from('cbt_jadwal a');
        $this->db->where('a.id_smt', $smt);
        $this->db->where('a.id_tp', $tp);
        return $this->db->get()->result();
    }
    public function getJadwalKelas($tp, $smt)
    {
        $this->db->select('a.id_jadwal, a.tgl_mulai, b.bank_kode, b.bank_kelas');
        $this->db->from('cbt_jadwal a');
        $this->db->where('a.id_smt', $smt);
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->where('a.id_tp', $tp);
        return $this->db->get()->result();
    }
    public function getJadwalByJenis($jenis, $level, $dari, $sampai)
    {
        $this->db->order_by('a.tgl_mulai', 'ASC');
        $this->db->where('c.bank_level', $level);
        return $this->db->get()->result();
        if (!($sampai != null)) {
        }
        if (!($dari != null)) {
        }
        $this->db->where('a.tgl_mulai >=', $dari);
        $this->db->where('a.id_jenis', $jenis);
        $this->db->select('a.id_jadwal, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai, a.jam_ke,' . ' c.bank_kode, c.bank_level, c.bank_kelas, b.kode_jenis, b.nama_jenis, d.kode, d.nama_mapel');
        $this->db->order_by('a.jam_ke', 'ASC');
        $this->db->where('a.tgl_mulai <=', $sampai);
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        if (!($level != '0')) {
        }
    }
    public function getAllJadwalByJenis($jenis, $tp, $smt)
    {
        $this->db->from('cbt_jadwal a');
        $this->db->order_by('a.tgl_mulai', 'ASC');
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('c.bank_level', 'ASC');
        $this->db->select('a.id_jadwal, a.id_jenis, a.tgl_mulai, ' . 'c.bank_kode, c.bank_level, c.bank_kelas, b.kode_jenis, b.nama_jenis, d.id_mapel, d.kode, d.nama_mapel');
        $this->db->order_by('a.jam_ke', 'ASC');
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        foreach ($result as $key => $row) {
            $ret[$row->tgl_mulai][$row->id_mapel][] = $row;
        }
        $this->db->where('a.id_tp', $tp);
        if (!($jenis != null)) {
        }
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
        }
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        return $ret;
        $this->db->where('a.id_jenis', $jenis);
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
    }
    public function getAllBankSoal($guru = null)
    {
        if (!$result) {
        }
        $ret['0'] = 'Pilih Bank Soal :';
        if (!($guru !== null)) {
        }
        $this->db->select('id_bank, bank_kode');
        return $ret;
        $result = $this->db->get('cbt_bank_soal')->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_bank] = $row->bank_kode;
        }
        $this->db->where('bank_guru_id', $guru);
    }
    public function getAllBankSoalByTp($id_tp, $id_smt, $guru = null)
    {
        $result = $this->db->get('cbt_bank_soal')->result();
        $ret = [];
        $this->db->where('status', '1');
        $this->db->where('id_tp', $id_tp);
        foreach ($result as $key => $row) {
            $ret[$row->id_bank] = $row;
        }
        $this->db->select('id_bank, bank_kode, bank_mapel_id, tampil_pg, tampil_kompleks, tampil_jodohkan, tampil_isian, tampil_esai');
        $this->db->where('status_soal', '1');
        $this->db->where('id_smt', $id_smt);
        if (!($guru !== null)) {
        }
        $this->db->where('bank_guru_id', $guru);
        return $ret;
        if (!$result) {
        }
    }
    public function getAllBankSoalByMapel($id_tp, $id_smt, $mapel)
    {
        $this->db->from('cbt_bank_soal');
        $ret = [];
        $this->db->where('id_smt', $id_smt);
        return $ret;
        if (!$result) {
        }
        $this->db->select('id_bank, bank_kode, bank_mapel_id, tampil_pg, tampil_kompleks, tampil_jodohkan, tampil_isian, tampil_esai, status');
        $this->db->where('bank_mapel_id', $mapel);
        $this->db->where('status', '1');
        foreach ($result as $key => $row) {
            $ret[$row->id_bank] = $row;
        }
        $this->db->where('id_tp', $id_tp);
        $result = $this->db->get()->result();
    }
    public function getJumlahJenisSoal($id_bank)
    {
        $this->db->where('bank_id', $id_bank);
        $ret = [];
        $result = $this->db->get()->result();
        foreach ($result as $row) {
            $ret[$row->jenis][] = $row;
        }
        $this->db->select('id_soal, jenis');
        $this->db->where('tampilkan', '1');
        return $ret;
        if (!$result) {
        }
        $this->db->from('cbt_soal');
    }
    public function getJenis()
    {
        $this->datatables->from('cbt_jenis');
        $this->datatables->select('*');
        return $this->datatables->generate();
    }
    public function getJenisById($id)
    {
        $this->db->select('id_jenis, nama_jenis, kode_jenis');
        return $this->db->get()->row();
        $this->db->where(['id_jenis' => $id]);
        $this->db->from('cbt_jenis');
    }
    function updateJenis()
    {
        return $this->db->update('cbt_jenis');
        $this->db->set('nama_jenis', $name);
        $id = $this->input->post('id_jenis');
        $kode = $this->input->post('kode_jenis', true);
        $this->db->where('id_jenis', $id);
        $name = $this->input->post('nama_jenis', true);
        $this->db->set('kode_jenis', $kode);
    }
    public function getRuang()
    {
        return $this->datatables->generate();
        $this->datatables->from('cbt_ruang');
        $this->datatables->select('*, (SELECT COUNT(id_sesi) FROM cbt_sesi) AS jum_sesi');
    }
    public function getRuangById($id)
    {
        $this->db->from('cbt_ruang');
        return $this->db->get()->row();
        $this->db->select('id_ruang, nama_ruang, kode_ruang');
        $this->db->where(['id_ruang' => $id]);
    }
    public function getRuangSesi($tp, $smt)
    {
        $this->db->order_by('c.nama_sesi', 'ASC');
        return $ret;
        $ret = [];
        $this->db->join('cbt_sesi c', 'c.id_sesi=a.sesi_id');
        $this->db->select('a.siswa_id, a.sesi_id, a.ruang_id, a.kelas_id, ' . 'b.nama_ruang, b.kode_ruang, c.nama_sesi, c.kode_sesi, d.nama_kelas');
        $result = $this->db->get()->result();
        $this->db->join('cbt_ruang b', 'b.id_ruang=a.ruang_id');
        foreach ($result as $row) {
            $ret[$row->sesi_id][$row->ruang_id][$row->kelas_id] = $row->nama_kelas;
        }
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('master_kelas d', 'd.id_kelas=a.kelas_id');
        if (!$result) {
        }
        $this->db->order_by('b.nama_ruang', 'ASC');
    }
    function updateRuang()
    {
        $kode = $this->input->post('kode_ruang', true);
        $this->db->where('id_ruang', $id);
        return $this->db->update('cbt_ruang');
        $this->db->set('kode_ruang', $kode);
        $name = $this->input->post('nama_ruang', true);
        $this->db->set('nama_ruang', $name);
        $id = $this->input->post('id_ruang');
    }
    public function getSesi()
    {
        $this->datatables->select('*');
        $this->datatables->from('cbt_sesi c');
        return $this->datatables->generate();
    }
    public function getAllKodeSesi()
    {
        $this->db->from('cbt_sesi');
        foreach ($result as $row) {
            $ret[$row->kode_sesi] = $row;
        }
        $ret = [];
        if (!$result) {
        }
        return $ret;
        $result = $this->db->get()->result();
        $this->db->select('id_sesi, nama_sesi, kode_sesi, waktu_mulai, waktu_akhir');
    }
    public function getSesiById($id)
    {
        $this->db->select('id_sesi, nama_sesi, kode_sesi, waktu_mulai, waktu_akhir');
        $this->db->where(['id_sesi' => $id]);
        return $this->db->get()->row();
        $this->db->from('cbt_sesi');
    }
    public function getSesiBySiswa($siswa_id)
    {
        $this->db->where('siswa_id', $siswa_id);
        $query = $this->db->get('siswa_sesi')->result();
        return $query;
    }
    function updateSesi()
    {
        $this->db->where('id_sesi', $id);
        $kode = $this->input->post('kode_sesi', true);
        return $this->db->update('cbt_sesi');
        $this->db->set('waktu_mulai', $mulai);
        $this->db->set('aktif', 1);
        $this->db->set('kode_sesi', $kode);
        $name = $this->input->post('nama_sesi', true);
        $this->db->set('waktu_akhir', $akhir);
        $this->db->set('nama_sesi', $name);
        $id = $this->input->post('id_sesi');
        $akhir = $this->input->post('waktu_akhir', true);
        $mulai = $this->input->post('waktu_mulai', true);
    }
    public function getSiswaCbtInfo($id_siswa, $id_tp, $id_smt)
    {
        $this->db->join('cbt_sesi_siswa b', 'a.id_siswa=b.siswa_id', 'left');
        $this->db->join('cbt_sesi sk', 'b.sesi_id=sk.id_sesi', 'left');
        $this->db->where('a.id_tp', $id_tp);
        return $this->db->get()->row();
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->join('cbt_ruang rk', 'b.ruang_id=rk.id_ruang', 'left');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('a.id_kelas_siswa, a.id_tp, a.id_smt, a.id_siswa, a.id_kelas,' . ' b.siswa_id, b.kelas_id, b.ruang_id, b.sesi_id,' . ' rk.id_ruang, rk.nama_ruang, rk.kode_ruang,' . ' sk.id_sesi, sk.nama_sesi, sk.kode_sesi, sk.waktu_mulai, sk.waktu_akhir');
    }
    public function getRuangSesiSiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->from('kelas_siswa a');
        return $this->db->get()->result();
        $this->db->join('cbt_sesi_siswa e', 'a.id_siswa=e.siswa_id', 'left');
        $this->db->join('cbt_sesi sk', 'e.sesi_id=sk.id_sesi', 'left');
        $this->db->join('cbt_ruang rk', 'e.ruang_id=rk.id_ruang', 'left');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->select('a.id_siswa, a.id_kelas,' . ' b.nama, b.nis, b.username,' . ' c.nama_kelas, c.kode_kelas,' . ' e.sesi_id, e.ruang_id,' . ' rk.id_ruang, rk.kode_ruang,' . ' sk.id_sesi, sk.kode_sesi');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->order_by('b.nama', 'ASC');
        $this->db->where('a.id_smt', $id_smt);
    }
    public function getSiswaByKelas($id_tp, $id_smt, $id_kelas)
    {
        $this->db->where('f.siswa_id is NOT NULL', NULL, FALSE);
        $this->db->where('a.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        return $this->db->get()->result();
        $this->db->join('cbt_sesi e', 'e.id_sesi=c.sesi_id', 'left');
        $this->db->where('c.siswa_id is NOT NULL', NULL, FALSE);
        $this->db->order_by('b.nama', 'ASC');
        if (is_array($id_kelas)) {
        }
        $this->db->join('cbt_sesi_siswa c', 'c.siswa_id=a.id_siswa', 'left');
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password,' . ' b.foto, d.kode_ruang, e.kode_sesi, f.nama_kelas, f.kode_kelas, g.nomor_peserta');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->where_in('a.id_kelas', $id_kelas);
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('g.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->join('cbt_ruang d', 'd.id_ruang=c.ruang_id', 'left');
        $this->db->where('b.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->where('a.id_smt', $id_smt);
    }
    public function getSiswaById($id_tp, $id_smt, $idSiswa)
    {
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->row();
        $this->db->where('a.id_siswa', $idSiswa);
        $this->db->join('cbt_kelas_ruang h', 'h.id_kelas=a.id_kelas', 'left');
        $this->db->from('kelas_siswa a');
        $this->db->join('cbt_sesi j', 'j.id_sesi=h.id_sesi', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('cbt_sesi_siswa c', 'c.siswa_id=a.id_siswa', 'left');
        $this->db->join('cbt_ruang i', 'i.id_ruang=h.id_ruang', 'left');
        $this->db->join('cbt_ruang d', 'd.id_ruang=c.ruang_id', 'left');
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password,' . ' b.foto, d.kode_ruang, e.kode_sesi, f.nama_kelas, f.kode_kelas, g.nomor_peserta,' . ' h.set_siswa, i.kode_ruang as ruang_kelas, j.kode_sesi as sesi_kelas');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=c.sesi_id', 'left');
    }
    public function getAllPesertaByRuang($id_tp, $id_smt)
    {
        $this->db->order_by('f.kode_kelas');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        return $ret;
        $this->db->order_by('d.kode_ruang');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->order_by('b.nama');
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $ret = [];
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
        foreach ($result as $row) {
            $ret[$row->kode_ruang][$row->kode_sesi][] = $row;
        }
        $this->db->from('cbt_sesi_siswa a');
        $this->db->order_by('e.kode_sesi');
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto, f.level_id,' . ' f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->order_by('f.level_id');
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        $result = $this->db->get()->result();
    }
    public function getAllPesertaByKelas($id_tp, $id_smt)
    {
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        $this->db->order_by('f.kode_kelas');
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        foreach ($result as $row) {
            $ret[$row->kode_kelas][] = $row;
        }
        $this->db->order_by('b.nama');
        $ret = [];
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto,' . ' f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $result = $this->db->get()->result();
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->order_by('f.level_id');
        return $ret;
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        $this->db->from('cbt_sesi_siswa a');
    }
    public function getSiswaByRuang($id_tp, $id_smt, $id_ruang, $sesi, $level = null)
    {
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->order_by('b.nama');
        $this->db->where('a.sesi_id', $sesi);
        return $this->db->get()->result();
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas' . ' AND f.level_id=' . $level . '');
        $this->db->select('a.ruang_id, a.sesi_id, b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto,' . ' f.id_kelas, f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        if ($level === null) {
        }
        $this->db->where('a.ruang_id', $id_ruang);
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
    }
    public function getRuangSiswaByKelas($id_tp, $id_smt, $kelas, $sesi)
    {
        $this->db->join('master_kelas f', 'f.id_kelas=c.id_kelas');
        if (!($sesi != null)) {
        }
        $this->db->where_in('a.kelas_id', $kelas);
        $this->db->join('master_siswa b', 'b.id_siswa=a.siswa_id', 'left');
        $this->db->order_by('b.nama');
        $this->db->join('cbt_ruang d', 'd.id_ruang=a.ruang_id', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=a.sesi_id', 'left');
        return $this->db->get()->result();
        $this->db->where('a.sesi_id', $sesi);
        $this->db->from('cbt_sesi_siswa a');
        $this->db->join('kelas_siswa c', 'c.id_siswa=b.id_siswa AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt . '');
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.username, b.password, b.foto,' . ' f.nama_kelas, f.kode_kelas,' . ' d.nama_ruang, d.kode_ruang,' . ' e.kode_sesi, e.nama_sesi,' . ' g.nomor_peserta');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.siswa_id AND g.id_tp=' . $id_tp, 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=b.id_siswa AND =i.status=1');
    }
    public function getSiswaByKelasArray($id_tp, $id_smt, $arr_kelas)
    {
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->order_by('f.kode_kelas', 'ASC');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->where_in('a.id_kelas', $arr_kelas);
        return $this->db->get()->result();
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_tp', $id_tp);
        if (in_array('Semua', $arr_kelas)) {
        }
        $this->db->join('level_kelas l', 'l.id_level=f.level_id');
        $this->db->order_by('l.level', 'ASC');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('a.id_siswa, a.id_kelas,' . ' b.nama, b.nis, b.nisn, b.username, b.password,' . ' f.nama_kelas, f.kode_kelas, l.level, g.nomor_peserta');
        $this->db->order_by('b.nama', 'ASC');
    }
    public function getKelasList($tp, $smt)
    {
        $this->db->where('a.id_smt', $smt);
        $this->db->select('a.id_kelas, a.nama_kelas, a.kode_kelas, c.nama_jurusan, b.id_ruang, b.id_sesi, b.set_siswa');
        $this->db->order_by('a.level_id', 'ASC');
        $this->db->join('master_jurusan c', 'c.id_jurusan=a.jurusan_id', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->from('master_kelas a');
        $this->db->join('level_kelas d', 'd.id_level=a.level_id', 'left');
        return $query->result();
        $query = $this->db->get();
        $this->db->join('cbt_kelas_ruang b', 'a.id_kelas=b.id_kelas', 'left');
        $this->db->order_by('a.nama_kelas', 'ASC');
    }
    public function getKelas($tp = null, $smt = null)
    {
        $this->db->select('a.id_kelas, a.nama_kelas, a.kode_kelas, b.level');
        $this->db->join('level_kelas b', 'b.id_level=a.level_id', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->from('master_kelas a');
        $this->db->order_by('a.nama_kelas', 'ASC');
        if (!($smt != null)) {
        }
        if (!($tp != null)) {
        }
        return $this->db->get()->result();
    }
    public function getDataTableBank($guru = null)
    {
        $this->datatables->from('cbt_bank_soal a');
        $this->datatables->select('a.id_bank, a.bank_kode, a.bank_level, a.tampil_pg, a.tampil_esai, a.status, b.nama_mapel, c.nama_guru');
        if (!($guru !== null)) {
        }
        $this->datatables->join('cbt_jenis e', 'e.id_jenis=a.bank_jenis_id', 'left');
        return $this->datatables->generate();
        $this->datatables->join('master_jurusan d', 'd.id_jurusan=a.bank_jurusan_id', 'left');
        $this->datatables->join('master_mapel b', 'b.id_mapel=a.bank_mapel_id', 'left');
        $this->datatables->join('master_guru c', 'c.id_guru=a.bank_guru_id', 'left');
        $this->datatables->where('a.bank_guru_id', $guru);
    }
    public function getDataBank($guru = null, $mapel = null, $level = null)
    {
        $result = $this->db->get()->result();
        foreach ($result as $row) {
            $ret[$row->id_tp][$row->id_smt][] = $row;
        }
        $ret = [];
        $this->db->select('a.id_bank, a.id_tp, a.id_smt, a.bank_kode, a.bank_level, a.bank_kelas, a.date, a.status,' . ' a.tampil_pg, a.tampil_kompleks, a.tampil_jodohkan, a.tampil_isian, a.tampil_esai, a.bank_guru_id,' . ' b.nama_mapel, c.id_guru, c.nama_guru,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal,' . ' (SELECT COUNT(id_jadwal) FROM cbt_jadwal WHERE cbt_jadwal.id_bank = a.id_bank AND cbt_jadwal.status="1") AS digunakan');
        return $ret;
        $this->db->where('a.bank_mapel_id', $mapel);
        $this->db->order_by('a.bank_level', 'ASC');
        $this->db->join('master_mapel b', 'b.id_mapel=a.bank_mapel_id', 'left');
        if (!($mapel !== null)) {
        }
        $this->db->where('a.bank_level', $level);
        $this->db->join('master_guru c', 'c.id_guru=a.bank_guru_id', 'left');
        $this->db->from('cbt_bank_soal a');
        if (!($guru !== null)) {
        }
        $this->db->where('a.bank_guru_id', $guru);
        if (!($level !== null)) {
        }
    }
    public function getDataBankById($id)
    {
        $this->db->from('cbt_bank_soal a');
        $this->db->where('a.id_bank', $id);
        $this->db->join('master_jurusan d', 'd.id_jurusan=a.bank_jurusan_id', 'left');
        $this->db->select('a.*, b.nama_mapel, b.kode, c.nama_guru, d.nama_jurusan, d.kode_jurusan,' . ' (SELECT COUNT(id_jadwal) FROM cbt_jadwal WHERE cbt_jadwal.id_bank = a.id_bank AND cbt_jadwal.status="1") AS digunakan');
        $this->db->join('master_mapel b', 'b.id_mapel=a.bank_mapel_id', 'left');
        $this->db->join('master_guru c', 'c.id_guru=a.bank_guru_id', 'left');
        return $this->db->get()->row();
    }
    public function getTotalSoal($id_bank, $jenis = null)
    {
        $this->db->where('bank_id', $id_bank);
        $this->db->where('jenis', $jenis);
        return $this->db->get('cbt_soal')->num_rows();
        if (!($jenis != null)) {
        }
    }
    public function getNomorSoalById($id_soal)
    {
        $this->db->select('nomor_soal, jenis, bank_id');
        return $this->db->get('cbt_soal')->row();
        $this->db->where('id_soal', $id_soal);
    }
    public function getFileSoalById($id_soal)
    {
        return $this->db->get('cbt_soal')->row();
        $this->db->where('id_soal', $id_soal);
        $this->db->select('file');
    }
    public function getSoalByBank($id_bank)
    {
        $this->db->order_by('jenis');
        $this->db->where('bank_id', $id_bank);
        $this->db->from('cbt_soal');
        $this->db->select('id_soal, bank_id, mapel_id, jenis, nomor_soal, soal, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban');
        foreach ($result as $row) {
            $ret[$row->jenis][$row->nomor_soal] = $row;
        }
        $ret = [];
        $this->db->order_by('nomor_soal');
        return $ret;
        $result = $this->db->get()->result();
    }
    public function getAllSoalByBank($id_bank, $jenis = null)
    {
        $this->db->select('id_soal, bank_id, mapel_id, jenis, nomor_soal, soal, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban, tampilkan');
        if (!($jenis != null)) {
        }
        $this->db->where('jenis', $jenis);
        return $this->db->get('cbt_soal')->result();
        $this->db->where('bank_id', $id_bank);
    }
    public function getSoalByNomor($id_bank, $nomor, $jenis)
    {
        $this->db->where('bank_id', $id_bank);
        $this->db->select('*');
        return $this->db->get('cbt_soal')->row();
        $this->db->where('nomor_soal', $nomor);
        $this->db->where('jenis', $jenis);
    }
    public function getNomorSoalByBankJenis($id_bank, $jenis)
    {
        $this->db->where('bank_id', $id_bank);
        $ret = [];
        return $ret;
        $this->db->select('id_soal, jenis, nomor_soal');
        $this->db->where('jenis', $jenis);
        foreach ($result as $key => $row) {
            $ret[$row->nomor_soal] = $row;
        }
        $result = $this->db->get('cbt_soal')->result();
    }
    public function getNomorSoalByBank($id_bank, $jenis = null)
    {
        $result = $this->db->get('cbt_soal')->result();
        return $ret;
        $this->db->where('bank_id', $id_bank);
        $this->db->select('id_soal, jenis, nomor_soal, jawaban');
        $this->db->where('tampilkan', '1');
        $this->db->where('jenis', $jenis);
        if (!($jenis != null)) {
        }
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_soal] = $row;
        }
    }
    public function getNomorSoalByArrIdBank($arr_id_bank, $jenis = null)
    {
        if (!($jenis != null)) {
        }
        return $this->db->get('cbt_soal')->result();
        $this->db->where_in('bank_id', $arr_id_bank);
        $this->db->select('id_soal, jenis, nomor_soal, jawaban');
        $this->db->where('jenis', $jenis);
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
        $this->db->or_where('opsi_e NOT NULL');
        $this->db->select('id_soal, bank_id, jenis, nomor_soal');
        return $this->db->get('cbt_soal')->result();
        $this->db->where('bank_id', $id_bank)->where('soal NOT NULL')->or_where('opsi_a NOT NULL')->or_where('opsi_b NOT NULL')->or_where('opsi_c NOT NULL')->or_where('opsi_d NOT NULL')->or_where('jawaban NOT NULL');
        if (!($jenjang == '3')) {
        }
    }
    public function cekSoalBelumKomplit($jenis, $opsi_ganda)
    {
        if (!($opsi_ganda == '4')) {
        }
        $ret = [];
        return $ret;
        $this->db->where('soal IS NULL')->or_where('soal =""');
        $this->db->where('jenis', $jenis);
        $this->db->select('id_soal, bank_id, jenis, nomor_soal, mapel_id');
        $this->db->from('cbt_soal');
        $this->db->where('opsi_a IS NULL')->or_where('opsi_a =""');
        $this->db->where('opsi_e IS NULL')->or_where('opsi_e =""');
        $result = $this->db->get()->result();
        if (!($jenis == '1')) {
        }
        $this->db->where('jawaban IS NULL')->or_where('jawaban =""');
        foreach ($result as $key => $row) {
            $ret[$row->bank_id][] = $row;
        }
        $this->db->where('opsi_d IS NULL')->or_where('opsi_d =""');
        $this->db->where('opsi_d IS NULL')->or_where('opsi_d =""');
        $this->db->where('opsi_a IS NULL')->or_where('opsi_a =""');
        $this->db->where('opsi_b IS NULL')->or_where('opsi_b =""');
        if (!($jenis == '2')) {
        }
        if (!($opsi_ganda == '5')) {
        }
        $this->db->where('opsi_c IS NULL')->or_where('opsi_c =""');
    }
    public function getNomorSoalTerbesar($id_bank, $jenis)
    {
        $this->db->where('bank_id', $id_bank)->where('jenis', $jenis);
        $this->db->order_by('nomor_soal', 'DESC');
        return $this->db->get('cbt_soal')->row();
        $this->db->select('nomor_soal');
    }
    public function dummy($jenjang)
    {
        $data = array('id_bank' => '', 'bank_jenis_id' => '', 'bank_kode' => '', 'bank_mapel_id' => '', 'bank_level' => '', 'bank_kelas' => serialize([]), 'bank_guru_id' => '', 'jml_soal' => '0', 'bobot_pg' => '0', 'tampil_pg' => '0', 'opsi' => $jenjang == '1' ? '3' : ($jenjang == '2' ? '4' : ($jenjang == '3' ? '5' : '')), 'jml_kompleks' => '0', 'tampil_kompleks' => '0', 'bobot_kompleks' => '0', 'jml_jodohkan' => '0', 'tampil_jodohkan' => '0', 'bobot_jodohkan' => '0', 'jml_isian' => '0', 'tampil_isian' => '0', 'bobot_isian' => '0', 'jml_esai' => '0', 'bobot_esai' => '0', 'tampil_esai' => '0', 'kkm' => '', 'soal_agama' => '-', 'status' => '1');
        return $data;
    }
    public function saveBankSoal($tp, $smt)
    {
        $kelas = [];
        if (!($i <= $rows)) {
        }
        $kelas[] = ['kelas_id' => $this->input->post('kelas[' . $i . ']', true)];
        $i = 0;
        return $insert_id;
        return $this->db->update('cbt_bank_soal', $data);
        $id = $this->input->post('id_bank', true);
        $data = array('id_tp' => $tp, 'id_smt' => $smt, 'bank_kode' => strip_tags($this->input->post('kode', TRUE) ?? ''), 'bank_jenis_id' => strip_tags($this->input->post('jenis', TRUE) ?? ''), 'bank_mapel_id' => strip_tags($this->input->post('mapel', TRUE) ?? ''), 'bank_kelas' => $jumlah, 'bank_level' => $this->input->post('level', TRUE), 'bank_guru_id' => strip_tags($this->input->post('guru', TRUE) ?? ''), 'jml_soal' => strip_tags($this->input->post('tampil_pg', TRUE) ?? ''), 'tampil_pg' => strip_tags($this->input->post('tampil_pg', TRUE) ?? ''), 'bobot_pg' => strip_tags($this->input->post('bobot_pg', TRUE) ?? ''), 'opsi' => strip_tags($this->input->post('opsi', TRUE) ?? ''), 'jml_kompleks' => strip_tags($this->input->post('tampil_kompleks', TRUE) ?? ''), 'tampil_kompleks' => strip_tags($this->input->post('tampil_kompleks', TRUE) ?? ''), 'bobot_kompleks' => strip_tags($this->input->post('bobot_kompleks', TRUE) ?? ''), 'jml_jodohkan' => strip_tags($this->input->post('tampil_jodohkan', TRUE) ?? ''), 'tampil_jodohkan' => strip_tags($this->input->post('tampil_jodohkan', TRUE) ?? ''), 'bobot_jodohkan' => strip_tags($this->input->post('bobot_jodohkan', TRUE) ?? ''), 'jml_isian' => strip_tags($this->input->post('tampil_isian', TRUE) ?? ''), 'tampil_isian' => strip_tags($this->input->post('tampil_isian', TRUE) ?? ''), 'bobot_isian' => strip_tags($this->input->post('bobot_isian', TRUE) ?? ''), 'jml_esai' => strip_tags($this->input->post('bobot_esai', TRUE) ?? ''), 'bobot_esai' => strip_tags($this->input->post('bobot_esai', TRUE) ?? ''), 'tampil_esai' => strip_tags($this->input->post('tampil_esai', TRUE) ?? ''), 'status' => strip_tags($this->input->post('status', TRUE) ?? ''), 'soal_agama' => strip_tags($this->input->post('soal_agama', TRUE) ?? ''));
        if (!$id) {
        }
        $insert_id = $this->db->insert_id();
        $i++;
        $jumlah = serialize($kelas);
        $rows = count($this->input->post('kelas', true));
        $this->db->where('id_bank', $id);
        $this->db->insert('cbt_bank_soal', $data);
    }
    public function dummyJadwal()
    {
        return array('id_bank' => '', 'id_jadwal' => '', 'id_jenis' => '', 'tgl_mulai' => '', 'tgl_selesai' => '', 'durasi_ujian' => '', 'bank_kelas' => serialize([]), 'acak_soal' => '', 'acak_opsi' => '', 'hasil_tampil' => '', 'token' => '', 'status' => '', 'ulang' => '', 'jarak' => '', 'reset_login' => '');
    }
    public function getDistinctJenisJadwal($tp, $smt)
    {
        $this->db->where('id_tp', $tp);
        $this->db->from('cbt_jadwal');
        return $this->db->get()->result();
        $this->db->select('id_jenis');
        $this->db->distinct();
        $this->db->where('id_smt', $smt);
    }
    public function getDataJadwal($tp, $smt, $guru = null, $rekap = null)
    {
        $this->db->join('level_kelas g', 'b.bank_level=g.id_level');
        $this->db->where('a.rekap', $rekap);
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp');
        if (!($guru !== null)) {
        }
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->where('b.bank_guru_id', $guru);
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt');
        if (!($rekap !== null)) {
        }
        $this->db->order_by('b.bank_level', 'ASC');
        $this->db->from('cbt_jadwal a');
        $query = $this->db->get()->result();
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai,' . ' a.tgl_selesai, a.status, a.ulang, a.reset_login, a.rekap, a.jam_ke,' . ' e.id_tp, e.tahun, f.id_smt, f.nama_smt, g.level, b.bank_kode, b.bank_level, b.bank_kelas,' . ' c.kode_jenis, d.kode, d.nama_mapel,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai, b.bank_guru_id,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal');
        $this->db->order_by('a.tgl_mulai', 'DESC');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        return $query;
    }
    public function getAllDataJadwal($guru = null, $mapel = null, $level = null)
    {
        $this->db->order_by('a.tgl_mulai', 'ASC');
        if (!($level !== null)) {
        }
        $this->db->order_by('a.id_tp', 'DESC');
        $this->db->from('cbt_jadwal a');
        $this->db->where('b.bank_mapel_id', $mapel);
        if (!($guru !== null)) {
        }
        $this->db->order_by('b.bank_level', 'ASC');
        $query = $this->db->get()->result();
        foreach ($query as $key => $row) {
            $ret['<b>' . $row->kode_jenis . '</b>  ' . $row->tahun . ' smt ' . $row->nama_smt][$row->level][] = $row;
        }
        if (!($mapel !== null)) {
        }
        return $ret;
        $this->db->where('b.bank_guru_id', $guru);
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt');
        $this->db->select('a.id_jadwal, a.tgl_mulai, a.tgl_selesai, a.status, a.durasi_ujian, a.acak_soal,' . ' a.acak_opsi, a.id_bank, a.id_jenis, a.hasil_tampil, a.status, a.ulang, a.reset_login, a.rekap,' . ' a.jam_ke, a.token, e.tahun, f.nama_smt, g.level, b.bank_kode, b.bank_level, b.bank_kelas, c.kode_jenis, d.kode, d.nama_mapel,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai, b.bank_guru_id,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal');
        $this->db->where('b.bank_level', $level);
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp');
        $this->db->order_by('a.id_smt', 'DESC');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('level_kelas g', 'b.bank_level=g.id_level');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $ret = [];
    }
    public function getJadwalTerpakai($id_jadwal = null)
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal][$row->id_siswa] = $row;
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $result = $this->db->get()->result();
        if (!($id_jadwal != null)) {
        }
        $this->db->select('id_bank,id_jadwal,id_siswa');
        $this->db->from('cbt_soal_siswa');
        return $ret;
        $ret = [];
    }
    public function getBankTerpakai($id_banks = null)
    {
        return $ret;
        $this->db->where_in('id_bank', $id_banks);
        $this->db->from('cbt_soal_siswa');
        $this->db->select('id_bank,id_soal,id_siswa');
        foreach ($result as $key => $row) {
            $ret[$row->id_bank][$row->id_siswa] = $row;
        }
        $result = $this->db->get()->result();
        $ret = [];
        if (!($id_banks != null)) {
        }
    }
    public function getCountBankTerpakai($id_bank = null)
    {
        $this->db->select('id_bank,COUNT(id_siswa) as siswa');
        $this->db->from('cbt_soal_siswa');
        if (!($id_bank != null)) {
        }
        $this->db->where('id_bank', $id_bank);
        return $this->db->get()->result();
        $this->db->group_by('id_bank');
    }
    public function getRekapByJadwalKelas($jadwal, $guru = null)
    {
        return $this->db->get()->row();
        $this->db->from('cbt_rekap');
        $this->db->where('id_jadwal', $jadwal);
        $this->db->where('id_guru', $guru);
        if (!($guru !== null)) {
        }
    }
    public function getRekapJadwal($guru = null)
    {
        return $query->result();
        $this->db->from('cbt_rekap');
        $this->db->select('*');
        $this->db->where('id_guru', $guru);
        if (!($guru !== null)) {
        }
        $query = $this->db->get();
        $this->db->order_by('tgl_mulai', 'DESC');
    }
    public function getAllRekapByJenis($tp, $smt, $jenis, $level, $mapel, $jadwal = null, $guru = null)
    {
        if (!($mapel != '0')) {
        }
        $this->db->where('bank_level', $level);
        $this->db->where('id_mapel', $mapel);
        if (!($jadwal != null)) {
        }
        $this->db->where('id_guru', $guru);
        $this->db->from('cbt_rekap');
        if (!($guru != null)) {
        }
        return $this->db->get()->result();
        $this->db->order_by('id_mapel', 'ASC');
        $this->db->where('id_jadwal', $jadwal);
        $this->db->where('kode_jenis', $jenis);
        $this->db->where('smt', $smt);
        $this->db->where('tp', $tp);
    }
    public function getAllRekapByJadwal($tp, $smt, $jenis, $level, $jadwal, $guru = null)
    {
        $this->db->order_by('id_mapel', 'ASC');
        return $this->db->get()->result();
        $this->db->where('id_jadwal', $jadwal);
        if (!($jadwal != '0')) {
        }
        $this->db->where('kode_jenis', $jenis);
        if (!($guru != null)) {
        }
        $this->db->where('smt', $smt);
        $this->db->where('tp', $tp);
        $this->db->where('bank_level', $level);
        $this->db->where('id_guru', $guru);
        $this->db->from('cbt_rekap');
    }
    public function getAllNilaiRekapByJenis($tp, $smt, $kode_jenis, $id_kelas, $id_mapel, $id_jadwal = null, $id_guru = null)
    {
        $this->db->where('a.kode_jenis', $kode_jenis);
        $this->db->select('a.*, b.nomor_peserta, c.nama');
        $this->db->join('cbt_nomor_peserta b', 'b.id_siswa=a.id_siswa AND b.id_tp=a.id_tp', 'left');
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        if (!($id_jadwal != null)) {
        }
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->join('master_siswa c', 'c.id_siswa=a.id_siswa', 'left');
        $this->db->where('a.smt', $smt);
        $this->db->where('a.id_jadwal', $id_jadwal);
        $this->db->where('a.tp', $tp);
        if (!($id_guru != null)) {
        }
        return $this->db->get()->result();
        $this->db->where('a.id_guru', $id_guru);
        $this->db->order_by('c.nama', 'ASC');
        $this->db->where('a.id_kelas', $id_kelas);
        if (!($id_mapel != '0')) {
        }
        $this->db->from('cbt_rekap_nilai a');
    }
    public function getAllNilaiRekapByJadwal($tp, $smt, $kode_jenis, $id_kelas, $id_jadwal, $id_guru = null)
    {
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND =i.status=1');
        $this->db->where('a.id_jadwal', $id_jadwal);
        if (!($id_guru != null)) {
        }
        $this->db->from('cbt_rekap_nilai a');
        $this->db->where('a.tp', $tp);
        $this->db->where('a.smt', $smt);
        $this->db->where('a.kode_jenis', $kode_jenis);
        $this->db->order_by('c.nama', 'ASC');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->select('a.*, b.nomor_peserta, c.nama');
        $this->db->join('cbt_nomor_peserta b', 'b.id_siswa=a.id_siswa AND b.id_tp=a.id_tp', 'left');
        if (!($id_jadwal != '0')) {
        }
        return $this->db->get()->result();
        $this->db->join('master_siswa c', 'c.id_siswa=a.id_siswa', 'left');
        $this->db->where('a.id_guru', $id_guru);
    }
    public function getAllRekap($guru = null)
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal] = $row;
        }
        $result = $this->db->get()->result();
        $this->db->select('id_rekap, id_tp, tp, id_smt, smt, id_jadwal, id_jenis, kode_jenis, id_bank, bank_kelas, nama_kelas, bank_kode, bank_level, id_mapel, nama_mapel, kode, tgl_mulai, tgl_selesai, id_guru, nama_guru');
        $this->db->from('cbt_rekap');
        return $ret;
        if (!($guru != null)) {
        }
        $ret = [];
        $this->db->where('id_guru', $guru);
    }
    public function getJadwalById($id_jadwal, $sesi = null)
    {
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->from('cbt_jadwal a');
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->where('a.id_jadwal', $id_jadwal);
        return $this->db->get()->row();
        $this->db->select('a.*, b.opsi, b.bank_kode, b.bank_level, b.bank_kelas,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai,' . ' b.bobot_pg, b.bobot_kompleks, b.bobot_jodohkan, b.bobot_isian, b.bobot_esai,' . ' b.id_bank, b.bank_guru_id, c.kode_jenis, c.nama_jenis,' . ' d.id_mapel, d.kode, d.nama_mapel, f.id_guru, f.nama_guru');
        $this->db->join('cbt_sesi e', 'e.id_sesi=' . $sesi, 'left');
        $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        if (!($sesi != null)) {
        }
    }
    public function getJadwalByIdBank($id_bank)
    {
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        return $this->db->get()->row();
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->select('a.*, b.opsi, b.bank_kode, b.bank_level, b.bank_kelas,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai,' . ' b.bobot_pg, b.bobot_kompleks, b.bobot_jodohkan, b.bobot_isian, b.bobot_esai,' . ' b.id_bank, b.bank_guru_id, c.kode_jenis, c.nama_jenis,' . ' d.id_mapel, d.kode, d.nama_mapel, f.id_guru, f.nama_guru');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->from('cbt_jadwal a');
        $this->db->where('a.id_bank', $id_bank);
        $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
    }
    public function getAllJadwal($tp, $smt, $id_guru = null)
    {
        $this->db->where('b.id_tp', $tp);
        $this->db->where('a.bank_guru_id', $id_guru);
        $this->db->from('cbt_bank_soal a');
        $this->db->join('cbt_jadwal b', 'b.id_bank=a.id_bank');
        return $this->db->get()->result();
        $this->db->where('b.id_smt', $smt);
        $this->db->select('a.bank_kode, a.bank_kelas, b.id_jadwal');
        if (!($id_guru != null)) {
        }
    }
    public function getJadwalByArrId($arr_id_jadwal, $sesi = null)
    {
        $this->db->from('cbt_jadwal a');
        $this->db->where_in('a.id_jadwal', $arr_id_jadwal);
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id', 'left');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank', 'left');
        $this->db->join('cbt_jenis c', 'c.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_guru f', 'f.id_guru=b.bank_guru_id', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=' . $sesi, 'left');
        if (!($sesi != null)) {
        }
        $this->db->select('a.*, b.opsi, b.bank_kode, b.bank_level, b.bank_kelas,' . ' b.tampil_pg, b.tampil_kompleks, b.tampil_jodohkan, b.tampil_isian, b.tampil_esai,' . ' b.bobot_pg, b.bobot_kompleks, b.bobot_jodohkan, b.bobot_isian, b.bobot_esai,' . ' b.id_bank, b.bank_guru_id, c.kode_jenis, c.nama_jenis,' . ' d.id_mapel, d.kode, d.nama_mapel, f.id_guru, f.nama_guru');
        return $this->db->get()->result();
    }
    public function cekJadwalBankSoal($id_bank)
    {
        $this->db->where_in('id_bank', $id_bank);
        $this->db->select('id_bank');
        return $this->db->get()->num_rows();
        $this->db->from('cbt_jadwal');
        if (is_array($id_bank)) {
        }
        $this->db->where('id_bank', $id_bank);
    }
    public function cekJadwalSudahMulai($id_jadwal)
    {
        return $this->get_where('cbt_durasi_siswa', 'id_jadwal', $id_jadwal)->num_rows();
    }
    public function saveJadwalUjian($id_tp, $id_smt)
    {
        $acak_opsi = $this->input->post('acak_opsi', TRUE);
        $status = $this->input->post('status', TRUE);
        $id = $this->input->post('id_jadwal', true);
        $check = $this->db->where('id_bank', $bank_id)->where('id_jenis', $jenis_id)->get('cbt_jadwal')->row();
        return $insert_id;
        $durasi = strip_tags($this->input->post('durasi_ujian', TRUE) ?? '');
        $jenis_id = strip_tags($this->input->post('jenis_id', TRUE) ?? '');
        $insert_id = $this->db->insert_id();
        $jarak = strip_tags($this->input->post('jarak', TRUE) ?? '');
        $selesai = strip_tags($this->input->post('tgl_selesai', TRUE) ?? '');
        if ($check != null) {
        }
        $hasil_tampil = $this->input->post('hasil_tampil', TRUE);
        $this->db->insert('cbt_jadwal', $data);
        $bank_id = strip_tags($this->input->post('bank_id', TRUE) ?? '');
        $mulai = strip_tags($this->input->post('tgl_mulai', TRUE) ?? '');
        if ($check != null && $check->id_jadwal != $id) {
        }
        return false;
        return $this->db->update('cbt_jadwal', $data);
        $token = $this->input->post('token', TRUE);
        $acak_soal = $this->input->post('acak_soal', TRUE);
        if ($id == '') {
        }
        $data = array('id_tp' => $id_tp, 'id_smt' => $id_smt, 'id_bank' => $bank_id, 'id_jenis' => $jenis_id, 'tgl_mulai' => $mulai, 'tgl_selesai' => $selesai, 'durasi_ujian' => $durasi, 'jarak' => $jarak, 'acak_soal' => !$acak_soal ? '0' : $acak_soal, 'acak_opsi' => !$acak_opsi ? '0' : $acak_opsi, 'hasil_tampil' => !$hasil_tampil ? '0' : $hasil_tampil, 'token' => !$token ? '0' : $token, 'status' => !$status ? '0' : $status, 'reset_login' => !$reset_login ? '0' : $reset_login);
        return false;
        $reset_login = $this->input->post('reset_login', TRUE);
        $this->db->where('id_jadwal', $id);
    }
    public function getJadwalTgl($guru = null)
    {
        $this->db->from('cbt_jadwal');
        $query = $this->db->get();
        $this->db->distinct();
        return $query->result();
        $this->db->select('tgl_mulai');
    }
    public function getDataJadwalByTgl($tgl)
    {
        $query = $this->db->get();
        $this->db->where("tgl_mulai <= '{$tgl}' AND tgl_selesai >= '{$tgl}'");
        $this->db->distinct();
        $this->db->from('cbt_jadwal');
        return $query->result();
        $this->db->select('tgl_mulai, tgl_selesai');
    }
    public function getDataGuru()
    {
        $this->db->join('cbt_pengawas b', 'b.id_guru = a.id_guru', 'left');
        $this->db->select('a.id_guru, a.nama_guru, b.id_pengawas, b.id_jadwal');
        $this->db->from('master_guru a');
        return $this->db->get()->result();
    }
    public function saveToken($post_token)
    {
        $data = array('token' => $tkn, 'auto' => $auto, 'jarak' => $jarak, 'updated' => $post_token->updated);
        $jarak = $post_token->jarak;
        return $insert_id;
        $auto = $post_token->auto;
        return $this->db->update('cbt_token', $data);
        $this->db->where('id_token', $id);
        $this->db->insert('cbt_token', $data);
        $tkn = $post_token->token;
        $insert_id = $this->db->insert_id();
        if (!$id) {
        }
        $id = isset($post_token->id_token) ? $post_token->id_token : false;
    }
    public function updateToken($token, $auto)
    {
        return $this->db->get('cbt_token')->row();
        $this->db->set('auto', $auto, FALSE);
        $this->db->where('token', $token);
        $this->db->update('cbt_token');
    }
    public function getToken()
    {
        return $this->db->get('cbt_token')->row();
    }
    public function getJadwalCbtKelas($id_tp, $id_smt)
    {
        return $this->db->get()->result();
        $this->db->select('a.id_jadwal, b.bank_kelas');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->from('cbt_jadwal a');
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
    }
    public function getInfoJadwal($id_bank)
    {
        $this->db->where('a.id_bank', $id_bank);
        $this->db->join('cbt_jadwal b', 'a.id_bank=b.id_bank');
        $this->db->from('cbt_bank_soal a');
        $this->db->select('a.id_bank, b.acak_soal, b.acak_opsi, a.opsi,' . ' a.tampil_pg, a.tampil_kompleks, a.tampil_jodohkan, a.tampil_isian, a.tampil_esai,' . ' a.bobot_pg,  a.bobot_kompleks,  a.bobot_jodohkan,  a.bobot_isian,  a.bobot_esai');
        return $this->db->get()->row();
    }
    public function getAllIdSoal($id_bank)
    {
        if (!$result) {
        }
        return $ret;
        $this->db->where('tampilkan', '1');
        $this->db->where('bank_id', $id_bank);
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->jenis][] = $row;
        }
        $this->db->from('cbt_soal');
        $result = $this->db->get()->result();
        $this->db->select('id_soal, jenis, jawaban');
    }
    public function getJadwalCbt($id_tp, $id_smt, $level)
    {
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai,' . ' a.durasi_ujian, a.pengawas, a.acak_soal, a.acak_opsi, a.hasil_tampil, a.token, a.status, a.ulang,' . ' a.reset_login, a.rekap, a.jam_ke, a.jarak,' . ' c.bank_kode, c.bank_level, c.bank_kelas, c.tampil_pg, c.tampil_kompleks, c.tampil_jodohkan,' . ' c.tampil_isian, c.tampil_esai, c.soal_agama, ' . ' c.bobot_pg, c.bobot_kompleks, c.bobot_jodohkan, c.bobot_isian, c.bobot_esai, b.kode_jenis,' . ' b.nama_jenis, d.kode, d.nama_mapel');
        $this->db->where('c.bank_level', $level);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.status', '1');
        $this->db->from('cbt_jadwal a');
        $this->db->where('c.status', '1');
        $this->db->order_by('a.jam_ke');
        return $retur;
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        foreach ($result as $row) {
            $retur[$row->id_jadwal] = $row;
        }
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        $this->db->where('c.status_soal', '1');
        $result = $this->db->get()->result();
        $retur = [];
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getJadwalByKelas($id_tp, $id_smt, $kelas)
    {
        $this->db->where('c.status', '1');
        $this->db->from('cbt_jadwal a');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->like('c.bank_kelas', $kelas);
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai,' . ' a.durasi_ujian, a.pengawas, a.acak_soal, a.acak_opsi, a.hasil_tampil, a.token, a.status, a.ulang,' . ' a.reset_login, a.rekap, a.jam_ke, a.jarak,' . ' c.bank_kode, c.bank_level, c.bank_kelas, c.tampil_pg, c.tampil_kompleks, c.tampil_jodohkan,' . ' c.tampil_isian, c.tampil_esai, c.soal_agama, ' . ' c.bobot_pg, c.bobot_kompleks, c.bobot_jodohkan, c.bobot_isian, c.bobot_esai, b.kode_jenis,' . ' b.nama_jenis, d.kode, d.nama_mapel');
        return $retur;
        $this->db->where('c.status_soal', '1');
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis');
        $this->db->where('a.id_tp', $id_tp);
        $result = $this->db->get()->result();
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_jadwal] = $row;
        }
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id');
        $this->db->where('a.status', '1');
        $this->db->order_by('a.jam_ke');
    }
    public function getCbt($id_jadwal)
    {
        $this->db->join('master_mapel d', 'd.id_mapel=c.bank_mapel_id', 'left');
        $this->db->select('a.id_jadwal, a.id_tp, a.id_smt, a.id_bank, a.id_jenis, a.tgl_mulai, a.tgl_selesai,' . ' a.durasi_ujian, a.pengawas, a.acak_soal, a.acak_opsi, a.hasil_tampil, a.token, a.status, a.ulang,' . ' a.reset_login, a.rekap, a.jam_ke, a.jarak,' . ' b.nama_jenis, b.kode_jenis,' . ' c.bank_kode, c.bank_level, c.bank_kelas, c.bank_mapel_id, c.bank_jurusan_id,' . ' c.bank_guru_id, c.bank_nama, c.jml_soal, c.jml_esai, c.tampil_pg, c.tampil_esai, c.bobot_pg,' . ' c.bobot_esai, c.opsi, c.date, c.status, c.soal_agama, c.id_tp, c.id_smt, c.deskripsi, c.jml_kompleks,' . ' c.tampil_kompleks, c.bobot_kompleks, c.jml_jodohkan, c.tampil_jodohkan, c.bobot_jodohkan, c.jml_isian,' . ' c.tampil_isian, c.bobot_isian, c.status_soal,' . ' d.id_mapel, d.nama_mapel, d.kode,' . ' e.id_guru, e.nama_guru,' . ' f.id_jurusan, f.nama_jurusan, f.kode_jurusan,' . ' g.tahun,' . ' h.smt, h.nama_smt,' . ' (SELECT COUNT(id_soal) FROM cbt_soal WHERE cbt_soal.bank_id = a.id_bank) AS total_soal');
        $this->db->join('cbt_jenis b', 'b.id_jenis=a.id_jenis', 'left');
        $this->db->join('master_guru e', 'e.id_guru=c.bank_guru_id', 'left');
        $this->db->from('cbt_jadwal a');
        $this->db->where('a.id_jadwal', $id_jadwal);
        $this->db->join('master_smt h', 'h.id_smt=a.id_smt', 'left');
        $this->db->join('master_jurusan f', 'f.id_jurusan=c.bank_jurusan_id', 'left');
        $this->db->join('master_tp g', 'g.id_tp=a.id_tp', 'left');
        return $this->db->get()->row();
        $this->db->join('cbt_bank_soal c', 'c.id_bank=a.id_bank', 'left');
    }
    public function getCbtById($id_jadwal)
    {
        return $this->db->get()->row();
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->select('*');
        $this->db->from('cbt_jadwal');
    }
    public function getIdRuangById($array)
    {
        $this->db->select('nama_ruang');
        if (!$result) {
        }
        return $ret;
        $this->db->where('id_ruang', $array);
        $ret = [];
        $result = $this->db->get()->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_ruang] = $row->kode_ruang;
        }
        $this->db->from('cbt_ruang');
    }
    public function getNamaRuangById($id)
    {
        $this->db->where('id_ruang', $id);
        return '';
        $result = $this->db->get()->row();
        if ($result) {
        }
        $this->db->from('cbt_ruang');
        $this->db->select('nama_ruang');
        return $result->nama_ruang;
    }
    public function getNamaSesiById($id)
    {
        $this->db->from('cbt_sesi');
        return $this->db->get()->row()->nama_sesi;
        $this->db->select('nama_sesi');
        $this->db->where(['id_sesi' => $id]);
    }
    public function getNamaKelasById($id)
    {
        return $this->db->get()->row()->nama_kelas;
        $this->db->where(['id_kelas' => $id]);
        $this->db->select('nama_kelas');
        $this->db->from('master_kelas');
    }
    public function getNamaGuruById($id)
    {
        $this->db->where('id_guru', $id);
        return $this->db->get()->row()->nama_guru;
        $this->db->from('master_guru');
        $this->db->select('nama_guru');
    }
    public function getElapsed($id)
    {
        $this->db->where('id_durasi', $id);
        return $this->db->get()->row();
        $this->db->from('cbt_durasi_siswa');
        $this->db->select('id_durasi, id_siswa, id_jadwal, status, lama_ujian, mulai, selesai, reset');
    }
    public function getSoalSiswa($id_bank, $id_siswa)
    {
        $this->db->where('a.id_siswa', $id_siswa);
        return $this->db->get()->result();
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal', 'left');
        $this->db->order_by('a.no_soal_alias');
        $this->db->where('a.id_bank', $id_bank);
        $this->db->select('a.*, b.jenis, b.nomor_soal, b.jawaban');
        $this->db->from('cbt_soal_siswa a');
        $this->db->order_by('a.jenis_soal');
    }
    public function getJumlahSoalSiswa($id_bank, $id_siswa)
    {
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_bank', $id_bank);
        $this->db->from('cbt_soal_siswa');
        $this->db->select('id_soal_siswa');
        return $this->db->get()->num_rows();
    }
    public function getALLSoalSiswa($id_bank, $id_siswa)
    {
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->order_by('a.no_soal_alias');
        $this->db->where('a.id_bank', $id_bank);
        $this->db->select('a.id_soal_siswa, a.id_bank, a.id_jadwal, a.id_soal, a.id_siswa, a.jenis_soal,' . ' a.no_soal_alias, a.opsi_alias_a, a.opsi_alias_b, a.opsi_alias_c, a.opsi_alias_d, a.opsi_alias_e,' . ' a.jawaban_alias, a.jawaban_siswa, a.jawaban_benar, a.point_essai, a.soal_end, a.point_soal,' . ' b.id_soal, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d,' . ' b.opsi_e, b.tampilkan');
        $this->db->where('a.id_siswa', $id_siswa);
        return $this->db->get()->result();
    }
    public function getJumlahJawaban($id_bank, $id_siswa)
    {
        $this->db->select('jawaban_siswa, id_siswa, id_bank');
        return $this->db->get()->result();
        $this->db->where('id_bank', $id_bank);
        $this->db->where('id_siswa', $id_siswa);
        $this->db->from('cbt_soal_siswa');
    }
    public function getSoalSiswaByJadwal($id_jadwal, $id_siswa)
    {
        $this->db->from('cbt_soal_siswa a');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->where('b.tampilkan', '1');
        return $this->db->get()->result();
        $this->db->order_by('a.jenis_soal');
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('a.id_jadwal', $id_jadwal);
        $this->db->select('a.*, b.jenis, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d, b.opsi_e');
        $this->db->order_by('b.nomor_soal');
    }
    public function getSoalSiswaByNomor($id_soal_siswa)
    {
        $this->db->join('cbt_bank_soal c', 'b.id_bank=a.id_bank');
        $this->db->from('cbt_soal_siswa a');
        $this->db->order_by('a.no_soal_alias');
        $this->db->where('a.id_soal_siswa', $id_soal_siswa);
        return $this->db->get()->row();
        $this->db->select('a.id_soal_siswa, a.id_bank, a.opsi_alias_a, a.opsi_alias_b, a.opsi_alias_c, a.opsi_alias_d,' . ' a.opsi_alias_e, a.no_soal_alias, a.jawaban_alias, a.soal_end, a.jawaban_siswa,' . ' b.id_soal, b.jenis, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d, b.opsi_e, b.tampilkan,' . ' c.tampil_pg, c.tampil_kompleks, c.tampil_jodohkan, c.tampil_isian, c.tampil_esai,');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
    }
    public function getSettingKartu()
    {
        $this->db->from('cbt_kop_kartu');
        $this->db->select('*');
        return $this->db->get()->row();
    }
    public function getSettingKopAbsensi()
    {
        $this->db->select('a.*, b.logo_kanan, b.logo_kiri, b.kepsek, b.tanda_tangan');
        $this->db->join('setting b', 'b.id_setting=1', 'left');
        return $this->db->get()->row();
        $this->db->from('cbt_kop_absensi a');
    }
    public function getSettingKopBeritaAcara()
    {
        $this->db->from('cbt_kop_berita a');
        $this->db->join('setting d', 'd.id_setting=1', 'left');
        $this->db->select('a.*, d.logo_kanan, d.logo_kiri, d.kepsek, d.nip, d.tanda_tangan, d.sekolah');
        return $this->db->get()->row();
    }
    public function getDurasiSiswa($id)
    {
        return $this->db->get_where('cbt_durasi_siswa', 'id_durasi=' . $id)->row();
    }
    public function getFilterJawabanSiswa($jadwal, $arrIdSiswa)
    {
        $this->db->where_in('id_siswa', $arrIdSiswa);
        $this->db->where('id_jadwal', $jadwal);
        return $this->db->get('cbt_soal_siswa')->result();
    }
    public function getFilterDurasiSiswa($jadwal, $arrIdSiswa)
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_durasi] = $row;
        }
        return $ret;
        $result = $this->db->get_where('cbt_durasi_siswa')->result();
        $ret = [];
        $this->db->where('id_jadwal', $jadwal);
    }
    public function getJawabanByBank($id_bank, $id_siswa = null)
    {
        return $this->db->get()->result();
        $this->db->where('a.id_bank=', $id_bank);
        if (!($id_siswa != null)) {
        }
        $this->db->select('a.*, b.nomor_soal, b.jawaban');
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->where('a.id_siswa=', $id_siswa);
        $this->db->from('cbt_soal_siswa a');
    }
    public function getJawabanSiswa($id)
    {
        $this->db->from('cbt_soal_siswa');
        return $this->db->get()->row();
        $this->db->select('id_soal_siswa, id_bank, id_jadwal, id_soal, id_siswa, jenis_soal, no_soal_alias, opsi_alias_a, opsi_alias_b, opsi_alias_c, opsi_alias_d, opsi_alias_e, jawaban_alias, jawaban_siswa, jawaban_benar, point_soal');
        $this->db->where('id_soal_siswa=', $id);
    }
    public function getJawabanSiswaByJadwal($id_jadwal, $id_siswa = null)
    {
        $this->db->where('b.tampilkan', '1');
        $this->db->from('cbt_soal_siswa a');
        return $this->db->get()->result();
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->order_by('b.nomor_soal');
        $this->db->order_by('a.jenis_soal');
        $this->db->where('a.id_jadwal=', $id_jadwal);
        if (is_array($id_siswa)) {
        }
        $this->db->join('cbt_soal b', 'b.id_soal=a.id_soal');
        $this->db->select('a.*, b.jenis, b.nomor_soal, b.soal, b.jawaban, b.opsi_a, b.opsi_b, b.opsi_c, b.opsi_d, b.opsi_e, b.tampilkan');
        if (!($id_siswa != null)) {
        }
        $this->db->where_in('a.id_siswa', $id_siswa);
    }
    public function getIdSiswaFromJawabanByJadwal($id_jadwal)
    {
        return $retur;
        $retur = [];
        foreach ($result as $row) {
            $retur[$row->id_siswa][] = $row;
        }
        $result = $this->db->get_where('cbt_soal_siswa', 'id_jadwal=' . $id_jadwal)->result();
    }
    public function getDurasiSiswaByJadwal($id_jadwal, $id_siswa = null)
    {
        $this->db->select('id_durasi, id_siswa, id_jadwal, status, lama_ujian, mulai, selesai, reset');
        return $this->db->get()->result();
        $this->db->from('cbt_durasi_siswa');
        if (!($id_siswa != null)) {
        }
        $this->db->where('id_siswa=', $id_siswa);
        $this->db->where('id_jadwal=', $id_jadwal);
    }
    public function getIdSiswaFromDurasiByJadwal($id_jadwal)
    {
        return $retur;
        $result = $this->db->get_where('cbt_durasi_siswa', 'id_jadwal=' . $id_jadwal)->result();
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        $retur = [];
    }
    public function getLogUjianByJadwal($id_jadwal)
    {
        $this->db->select('id_log, log_time, id_siswa, id_jadwal, log_type, log_desc, address, agent, device, reset');
        return $this->db->get()->result();
        $this->db->where('id_jadwal=', $id_jadwal);
        $this->db->from('log_ujian');
    }
    public function getIdSiswaFromLogUjianByJadwal($id_jadwal)
    {
        $result = $this->db->get_where('log_ujian', 'id_jadwal=' . $id_jadwal)->result();
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        return $retur;
        $retur = [];
    }
    public function getNilaiSiswa($arr_jadwal, $id_siswa)
    {
        $this->db->select('*');
        $retur = [];
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where_in('id_jadwal', $arr_jadwal);
        return $retur;
        $result = $this->db->get()->result();
        $this->db->from('cbt_nilai');
        foreach ($result as $row) {
            $retur[$row->id_jadwal] = $row;
        }
    }
    public function getNilaiSiswaByJadwal($id_jadwal, $id_siswa)
    {
        $this->db->where('id_jadwal', $id_jadwal);
        return $this->db->get()->row();
        $this->db->from('cbt_nilai');
        $this->db->select('*');
        $this->db->where('id_siswa', $id_siswa);
    }
    public function getNilaiAllSiswa($arr_jadwal, $arr_id_siswa)
    {
        $this->db->select('*');
        $retur = [];
        $result = $this->db->get()->result();
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        $this->db->where_in('id_siswa', $arr_id_siswa);
        $this->db->from('cbt_nilai');
        $this->db->where_in('id_jadwal', $arr_jadwal);
        return $retur;
    }
    public function getAllNilaiSiswa($id_jadwal)
    {
        $result = $this->db->get()->result();
        return $retur;
        $this->db->from('cbt_nilai');
        foreach ($result as $row) {
            $retur[$row->id_siswa] = $row;
        }
        $this->db->where('id_jadwal', $id_jadwal);
        $this->db->select('*');
        $retur = [];
    }
    public function getTotalKoreksi()
    {
        $this->db->from('cbt_nilai');
        $this->db->select('id_jadwal, dikoreksi, id_siswa');
        $retur = [];
        return $retur;
        $result = $this->db->get()->result();
        foreach ($result as $row) {
            if (!($row->id_siswa != null)) {
            }
            $retur[$row->id_jadwal][$row->dikoreksi][] = $row->id_siswa;
        }
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
        $result = $this->db->get('cbt_nomor_peserta')->result();
        $this->db->select('*');
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
    }
    public function getDistinctTahun()
    {
        $this->db->select('tp');
        return $ret;
        $this->db->distinct();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->tp] = $row->tp;
        }
        $result = $this->db->get('cbt_rekap_nilai')->result();
    }
    public function getDistinctSmt()
    {
        $this->db->distinct();
        $result = $this->db->get('cbt_rekap_nilai')->result();
        return $ret;
        $ret = [];
        $this->db->select('smt');
        foreach ($result as $row) {
            $ret[$row->smt] = $row->smt;
        }
    }
    public function getDistinctJenisUjian()
    {
        return $ret;
        $result = $this->db->get('cbt_rekap_nilai')->result();
        $ret = [];
        $this->db->distinct();
        $this->db->select('tp, smt, kode_jenis');
        foreach ($result as $row) {
            $ret[$row->tp][$row->smt][$row->kode_jenis] = $row->kode_jenis;
        }
    }
    public function getDistinctJenis()
    {
        $this->db->select('id_jenis, tp, smt, kode_jenis');
        $ret = [];
        $this->db->distinct();
        $result = $this->db->get('cbt_rekap_nilai')->result();
        return $ret;
        foreach ($result as $row) {
            $ret[$row->tp][$row->smt][$row->id_jenis] = $row->kode_jenis;
        }
    }
    public function getDistinctKelas($id_jadwal = null)
    {
        foreach ($result as $row) {
            $ret[$row->tp][$row->smt][$row->kode_jenis][$row->id_kelas] = $row->nama_kelas;
            if (!($row->id_kelas != '')) {
            }
        }
        $this->db->distinct();
        $this->db->from('cbt_rekap_nilai a');
        $result = $this->db->get()->result();
        if (!($id_jadwal != null)) {
        }
        return $ret;
        $this->db->join('master_kelas b', 'b.id_kelas=a.id_kelas');
        $this->db->select('a.tp, a.smt, a.kode_jenis, a.id_kelas, b.nama_kelas');
        $ret = [];
        $this->db->where('id_jadwal', $id_jadwal);
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
        return $this->db->get($table)->num_rows();
        if (!($where != null)) {
        }
        $this->db->where($where);
    }
    public function hapus($table, $data, $pk)
    {
        $this->db->where_in($pk, $data);
        return $this->db->delete($table);
    }
    public function getProfileAdmin($id_user)
    {
        return $this->db->get()->row();
        $this->db->where('a.id', $id_user);
        $this->db->select('b.*');
        $this->db->join('users_profile b', 'a.id=b.id_user', 'left');
        $this->db->from('users a');
    }
    public function totalWaliKelas($id_tp, $id_smt)
    {
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_jabatan', '4');
        return $this->db->get('jabatan_guru')->num_rows();
    }
    public function totalSiswaKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->num_rows();
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->select('a.id_siswa');
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
        return $this->datatables->generate();
        $this->datatables->from('master_tp');
        $this->datatables->select('id_tp, tahun, active');
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
        $this->db->where('active', 1);
        $this->db->select('id_tp, tahun');
        $this->db->from('master_tp');
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
        $this->db->from('master_smt');
        $this->db->select('id_smt, nama_smt, smt');
        return $this->db->get()->row();
        $this->db->where('active', 1);
    }
    public function getDataGuruByUserId($id_user, $id_tp, $id_smt)
    {
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        return $this->db->get()->row();
        $this->db->where('a.id_user', $id_user);
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->join('level_kelas g', 'f.level_id=g.id_level', 'left');
        $this->db->from('master_guru a');
        $this->db->select('a.id_guru, a.nama_guru, a.nip, a.id_user, a.foto, b.id_jabatan, b.id_kelas as wali_kelas, f.level_id, g.level');
    }
    public function getDataGuruById($id_guru, $id_tp, $id_smt)
    {
        $this->db->join('level_kelas g', 'f.level_id=g.id_level', 'left');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->select('a.id_guru, a.nama_guru, a.nip, a.id_user, a.foto, b.id_jabatan, b.id_kelas as wali_kelas, f.level_id, g.level');
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        return $this->db->get()->row();
        $this->db->from('master_guru a');
        $this->db->where('a.id_guru', $id_guru);
    }
    public function getListGuruByUserId($id_tp, $id_smt)
    {
        foreach ($query as $guru) {
            $rest[$guru->id_guru] = $guru;
        }
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->select('a.id_guru, a.nama_guru, a.id_user, a.foto, b.id_jabatan, b.id_kelas as wali_kelas, f.level_id, g.level');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->from('master_guru a');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $rest = [];
        $query = $this->db->get()->result();
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        return $rest;
        $this->db->join('level_kelas g', 'f.level_id=g.id_level', 'left');
    }
    public function getDetailGuruByUserId($id_user, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        $this->db->join('master_kelas f', 'a.id_guru=f.guru_id AND f.id_tp=' . $id_tp . ' AND f.id_smt=' . $id_smt, 'left');
        $this->db->from('master_guru a');
        $this->db->join('level_guru e', 'b.id_jabatan=e.id_level', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->where('a.id_user', $id_user);
        return $this->db->get()->row();
    }
    public function getKelasByMapel($id_mapel = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        return $this->db->get()->row();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('master_mapel b', 'a.mapel_id=b.id_mapel', 'left');
        $this->db->join('level_guru d', 'a.level_id=d.id_level', 'left');
    }
    public function get_where($table, $pk, $id, $join = null, $order = null)
    {
        return $this->db->get();
        if (!($order !== null)) {
        }
        $this->db->select('*');
        foreach ($order as $field => $sort) {
            $this->db->order_by($field, $sort);
        }
        if (!($join !== null)) {
        }
        foreach ($join as $table => $field) {
            $this->db->join($table, $field);
        }
        $this->db->where($pk, $id);
        $this->db->from($table);
    }
    public function create($table, $data)
    {
        return $this->db->insert($table, $data);
    }
    public function update($table, $data, $pk, $id = null, $batch = false)
    {
        $insert = $this->db->update($table, $data, array($pk => $id));
        return $insert;
        if ($batch === false) {
        }
        $insert = $this->db->update_batch($table, $data, $pk);
    }
    public function getDataSiswa($username, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->where('username', $username);
        $this->db->join('master_kelas c', 'b.id_kelas=c.id_kelas AND c.id_tp=' . $id_tp . ' AND c.id_smt=' . $id_smt, 'left');
        $this->db->join('kelas_siswa b', 'a.id_siswa=b.id_siswa AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt, 'left');
        return $this->db->get()->row();
        $this->db->from('master_siswa a');
        $this->db->join('cbt_sesi_siswa d', 'a.id_siswa=d.siswa_id', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }
    public function loadPengumuman($id_for)
    {
        $this->db->where('kepada', $id_for);
        $this->db->from('pengumuman a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        return $this->db->get()->result();
        $this->db->select('a.*, b.nama_guru, b.foto');
    }
    public function loadJadwalHariIni($id_tp, $id_smt, $id_kelas = null, $id_hari = null)
    {
        $this->db->from('kelas_jadwal_mapel a');
        if (!($id_hari != null)) {
        }
        $this->db->where('a.id_hari', $id_hari);
        $this->db->select('*');
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        if (!($id_kelas != null)) {
        }
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getJadwalKbm($id_tp, $id_smt, $id_kelas = null)
    {
        $this->db->from('kelas_jadwal_kbm');
        $this->db->where('id_tp', $id_tp);
        if ($id_kelas != null) {
        }
        $this->db->where('id_smt', $id_smt);
        $this->db->select('*');
        return $query;
        $this->db->where('id_kelas', $id_kelas);
        $query = $this->db->get()->result();
        $query = $this->db->get()->row();
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
        foreach ($result as $key => $row) {
            $ret[$row->id_bln] = $row->nama_bln;
        }
        $ret = [];
        if (!$result) {
        }
        return $ret;
        $result = $this->db->get('bulan')->result();
    }
    public function getAllSesi()
    {
        $result = $this->db->get('cbt_sesi')->result();
        $this->db->select('id_sesi, nama_sesi, kode_sesi');
        return $ret;
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_sesi] = $row->nama_sesi;
        }
        if (!$result) {
        }
    }
    public function getAllRuang()
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_ruang] = $row->nama_ruang;
        }
        $ret = [];
        $result = $this->db->get('cbt_ruang')->result();
        if (!$result) {
        }
        return $ret;
    }
    public function getAllWaktuSesi()
    {
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_sesi] = ['mulai' => $row->waktu_mulai, 'akhir' => $row->waktu_akhir];
        }
        $result = $this->db->get('cbt_sesi')->result();
        if (!$result) {
        }
        return $ret;
    }
    public function getDataKelompokMapel()
    {
        $this->db->select('*');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->kode_kel_mapel] = $row->nama_kel_mapel;
        }
        return $ret;
        $this->db->order_by('kode_kel_mapel');
        $this->db->from('master_kelompok_mapel');
    }
    public function getAllMapel()
    {
        $ret = [];
        $this->db->select('id_mapel,nama_mapel,urutan_tampil');
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_mapel] = $row->nama_mapel;
        }
        $result = $this->db->get('master_mapel')->result();
        $this->db->where('status', '1');
        $this->db->order_by('urutan_tampil');
        return $ret;
    }
    public function getAllKodeMapel()
    {
        $ret[''] = 'Tidak ada';
        $this->db->where('status', '1');
        $result = $this->db->get('master_mapel')->result();
        $this->db->order_by('urutan_tampil');
        foreach ($result as $key => $row) {
            $ret[$row->id_mapel] = $row->kode;
        }
        return $ret;
        if (!$result) {
        }
    }
    public function getAllMapelPeminatan()
    {
        $ress = [];
        $this->db->where('kategori <> "WAJIB"')->where('kategori <> "PAI (Kemenag)"')->where('kategori <> "MULOK"');
        foreach ($result as $key => $row) {
            $ret[$row->id_mapel] = $row->nama_mapel;
        }
        $this->db->select('*');
        $result = $this->db->get('master_mapel')->result();
        foreach ($res as $key => $row) {
            $ress[$row->id_kel_mapel] = $row->kode_kel_mapel;
        }
        $this->db->from('master_kelompok_mapel');
        $this->db->order_by('urutan_tampil');
        if (!$res) {
        }
        $this->db->where_in('kelompok', $ress);
        if (!$result) {
        }
        return $ret;
        if (!(count($ress) > 0)) {
        }
        $ret = [];
        $res = $this->db->get('master_mapel')->result();
    }
    public function getAllKodePeminatan()
    {
        $this->db->select('*');
        $this->db->where('kategori <> "PAI (Kemenag)"');
        if (!$res) {
        }
        $res = $this->db->get('master_mapel')->result();
        $this->db->from('master_kelompok_mapel');
        return $ress;
        $this->db->where('kategori <> "MULOK"');
        $ress = [];
        $this->db->where('kategori <> "WAJIB"');
        foreach ($res as $key => $row) {
            $ress[$row->id_kel_mapel] = $row;
        }
    }
    public function getMapelPeminatan($arr_kelompok)
    {
        if (count($arr_kelompok) > 0) {
        }
        $ret = [];
        return $ret;
        return [];
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->kelompok][$row->id_mapel] = $row->nama_mapel;
        }
        $this->db->where_in('kelompok', $arr_kelompok);
        $this->db->order_by('urutan_tampil');
        $result = $this->db->get('master_mapel')->result();
    }
    public function getAllLevel($jenjang)
    {
        $levels = ['7' => '7', '8' => '8', '9' => '9'];
        return $levels;
        if ($jenjang == '1') {
        }
        $levels = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
        $levels = [];
        if ($jenjang == '3') {
        }
        $levels = ['10' => '10', '11' => '11', '12' => '12'];
        if ($jenjang == '2') {
        }
    }
    public function getAllKelas($tp, $smt, $level = null)
    {
        $ret = [];
        $this->db->from('master_kelas');
        if (!$result) {
        }
        if (!($level != null)) {
        }
        return $ret;
        $this->db->where('id_tp', $tp);
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        $this->db->where('id_smt', $smt);
        $this->db->where('level_id' . $level);
        $this->db->select('*');
        $this->db->order_by('level_id', 'ASC');
        $this->db->order_by('nama_kelas', 'ASC');
        $result = $this->db->get()->result();
    }
    public function getAllKeyKodeKelas($tp, $smt)
    {
        $ret = [];
        $this->db->where('id_smt', $smt);
        $this->db->select('*');
        $this->db->where('id_tp', $tp);
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->kode_kelas] = $row->nama_kelas;
        }
        if (!$result) {
        }
        $result = $this->db->get()->result();
        $this->db->from('master_kelas');
    }
    public function getAllKodeKelas($tp = null, $smt = null)
    {
        $result = $this->db->get()->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = $row->kode_kelas;
        }
        $this->db->from('master_kelas');
        if (!$result) {
        }
        $this->db->where('id_tp', $tp);
        return $ret;
        $ret = [];
        $this->db->where('id_smt', $smt);
        $this->db->select('*');
        if (!($smt != null)) {
        }
        if (!($tp != null)) {
        }
    }
    public function getNamaKelasById($tp, $smt, $id)
    {
        $this->db->select('nama_kelas');
        $this->db->where('id_tp', $tp);
        return null;
        $this->db->where('id_smt', $smt);
        $result = $this->db->get('master_kelas')->row();
        if ($result != null) {
        }
        return $result->nama_kelas;
        $this->db->where('id_kelas', $id);
    }
    public function getAllKelasByArrayId($tp, $smt, $arrId)
    {
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where_in('id_kelas', $arrId);
        $ret = [];
        return $ret;
        $result = $this->db->get()->result();
        $this->db->select('*');
    }
    public function getAllEkskul()
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_ekstra] = $row->nama_ekstra;
        }
        $result = $this->db->get('master_ekstra')->result();
        return $ret;
        $ret = [];
        if (!$result) {
        }
    }
    public function getAllKodeEkskul()
    {
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_ekstra] = $row->kode_ekstra;
        }
        $ret = [];
        $result = $this->db->get('master_ekstra')->result();
        return $ret;
    }
    public function getAllJurusan()
    {
        $ret = [];
        if (!$result) {
        }
        $result = $this->db->get('master_jurusan')->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_jurusan] = $row->kode_jurusan;
        }
        return $ret;
    }
    public function getAllGuru()
    {
        $this->db->select('a.id_guru, a.nama_guru');
        $this->db->from('master_guru a');
        $this->db->join('users e', 'a.username=e.username');
        foreach ($result as $key => $row) {
            $ret[$row->id_guru] = $row->nama_guru;
        }
        $result = $this->db->get()->result();
        return $ret;
        $ret['0'] = 'Pilih Guru :';
        if (!$result) {
        }
    }
    public function getAllLevelGuru()
    {
        $result = $this->db->get('level_guru')->result();
        return $ret;
        $ret[''] = 'Pilih Jabatan :';
        foreach ($result as $key => $row) {
            $ret[$row->id_level] = $row->level;
        }
        if (!$result) {
        }
    }
    public function getAllJenisUjian()
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_jenis] = $row->nama_jenis . ' (' . $row->kode_jenis . ')';
        }
        return $ret;
        $result = $this->db->get('cbt_jenis')->result();
        $ret = [];
        if (!$result) {
        }
    }
    public function getAllBankSoal()
    {
        $ret[''] = 'Pilih Bank Soal :';
        $result = $this->db->get('cbt_bank_soal')->result();
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_bank] = $row->bank_kode;
        }
        if (!$result) {
        }
    }
    public function getAllJadwal($tp, $smt)
    {
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        $this->db->where('a.id_smt', $smt);
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal] = $row->bank_kode;
        }
        $this->db->where('a.id_tp', $tp);
        $ret = [];
        $this->db->from('cbt_jadwal a');
        return $ret;
        $result = $this->db->get()->result();
        if (!$result) {
        }
    }
    public function getAllJadwalMapel($tp, $smt)
    {
        $this->db->join('master_mapel d', 'd.id_mapel=b.bank_mapel_id');
        return array_unique($ret);
        if (!$result) {
        }
        $result = $this->db->get()->result();
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal] = $row->nama_mapel;
        }
        $this->db->select('a.id_jadwal, b.bank_kode, d.nama_mapel');
        $this->db->from('cbt_jadwal a');
        $ret = [];
        $this->db->where('a.id_smt', $smt);
        $this->db->where('a.id_tp', $tp);
    }
    public function getAllJadwalGuru($tp, $smt, $guru)
    {
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal] = $row->bank_kode;
        }
        $this->db->where('a.id_tp', $tp);
        return $ret;
        $this->db->where('a.id_smt', $smt);
        $result = $this->db->get()->result();
        $this->db->from('cbt_jadwal a');
        if (!$result) {
        }
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_guru_id=' . $guru);
    }
    public function getAllJenisJadwal($tp, $smt, $jenis, $mapel)
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_jadwal] = $row->bank_kode;
        }
        $this->db->where('a.id_smt', $smt);
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_mapel_id=' . $mapel . ' ');
        $this->db->where('a.id_jenis', $jenis);
        if (!$result) {
        }
        return $ret;
        $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        if ($mapel == '0') {
        }
        $ret = [];
        $this->db->from('cbt_jadwal a');
        $this->db->where('a.id_tp', $tp);
        $result = $this->db->get()->result();
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
        $CI =& get_instance();
        return '0';
        $CI->load->database();
        if ($database == '') {
        }
        if ($CI->db->table_exists('users')) {
        }
        return '2';
        if ($CI->db->get('setting')->row()) {
        }
        $database = $db['default']['database'];
        return '4';
        return '3';
        if (!$this->dbutil->database_exists($database)) {
        }
        return '1';
        $this->load->dbutil();
        return '5';
        if ($CI->db->get('users')->row()) {
        }
        include APPPATH . 'config/database.php';
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
        $this->error_start_delimiter = $error_prefix->getValue($this->form_validation);
        $this->identity_column = $this->config->item('identity', 'ion_auth');
        $this->message_end_delimiter = $this->error_end_delimiter;
        $this->messages = [];
        $error_prefix = $form_validation_class->getProperty('_error_prefix');
        $this->tables = $this->config->item('tables', 'ion_auth');
        $this->config->load('ion_auth', TRUE);
        $this->error_end_delimiter = $error_suffix->getValue($this->form_validation);
        $this->_ion_hooks = new stdClass();
        $this->load->helper('cookie');
        $this->load->library('form_validation');
        $this->errors = [];
        $this->error_start_delimiter = $this->config->item('error_start_delimiter', 'ion_auth');
        $this->load->helper('date');
        $error_suffix->setAccessible(TRUE);
        $this->message_start_delimiter = $this->error_start_delimiter;
        $error_prefix->setAccessible(TRUE);
        if ($delimiters_source === 'form_validation') {
        }
        $this->join = $this->config->item('join', 'ion_auth');
        $this->trigger_events('model_constructor');
        $delimiters_source = $this->config->item('delimiters_source', 'ion_auth');
        $this->message_end_delimiter = $this->config->item('message_end_delimiter', 'ion_auth');
        $this->lang->load('ion_auth');
        $this->db = $CI->db;
        $this->hash_method = $this->config->item('hash_method', 'ion_auth');
        $this->error_end_delimiter = $this->config->item('error_end_delimiter', 'ion_auth');
        $group_name = $this->config->item('database_group_name', 'ion_auth');
        $CI =& get_instance();
        if (empty($group_name)) {
        }
        $this->db = $this->load->database($group_name, TRUE, TRUE);
        $form_validation_class = new ReflectionClass('CI_Form_validation');
        $error_suffix = $form_validation_class->getProperty('_error_suffix');
        $this->message_start_delimiter = $this->config->item('message_start_delimiter', 'ion_auth');
    }
    public function db()
    {
        return $this->db;
    }
    public function hash_password($password, $identity = NULL)
    {
        if (!($algo !== FALSE && $params !== FALSE)) {
        }
        return password_hash($password, $algo, $params);
        $params = $this->_get_hash_parameters($identity);
        return FALSE;
        if (!(empty($password) || strpos($password, ' ') !== FALSE || strlen($password) > self::MAX_PASSWORD_SIZE_BYTES)) {
        }
        return FALSE;
        $algo = $this->_get_hash_algo();
    }
    public function verify_password($password, $hash_password_db, $identity = NULL)
    {
        return FALSE;
        return password_verify($password, $hash_password_db);
        return $this->_password_verify_sha1_legacy($identity, $password, $hash_password_db);
        if (!(empty($password) || empty($hash_password_db) || strpos($password, ' ') !== FALSE || strlen($password) > self::MAX_PASSWORD_SIZE_BYTES)) {
        }
        if (strpos($hash_password_db, '$') === 0) {
        }
    }
    public function rehash_password_if_needed($hash, $identity, $password)
    {
        if ($this->_set_password_db($identity, $password)) {
        }
        $this->trigger_events(['rehash_password', 'rehash_password_successful']);
        if (!password_needs_rehash($hash, $algo, $params)) {
        }
        $algo = $this->_get_hash_algo();
        $params = $this->_get_hash_parameters($identity);
        $this->trigger_events(['rehash_password', 'rehash_password_unsuccessful']);
        if (!($algo !== FALSE && $params !== FALSE)) {
        }
    }
    public function get_user_by_activation_code($user_code)
    {
        $user = $this->where('activation_selector', $token->selector)->users()->row();
        $token = $this->_retrieve_selector_validator_couple($user_code);
        return FALSE;
        if (!$user) {
        }
        return $user;
        if (!$this->verify_password($token->validator, $user->activation_code)) {
        }
    }
    public function activate($id, $code = FALSE)
    {
        $this->trigger_events(['post_activate', 'post_activate_unsuccessful']);
        $this->trigger_events('pre_activate');
        $this->trigger_events(['post_activate', 'post_activate_successful']);
        $this->db->update($this->tables['users'], $data, ['id' => $id]);
        $this->trigger_events('extra_where');
        if (!($code === FALSE || $user && $user->id === $id)) {
        }
        $user = $this->get_user_by_activation_code($code);
        $this->set_message('activate_successful');
        if (!($this->db->affected_rows() === 1)) {
        }
        $this->set_error('activate_unsuccessful');
        return FALSE;
        if (!($code !== FALSE)) {
        }
        $data = ['activation_selector' => NULL, 'activation_code' => NULL, 'active' => 1];
        return TRUE;
    }
    public function deactivate($id = NULL)
    {
        $this->set_error('deactivate_current_user_unsuccessful');
        $this->set_message('deactivate_successful');
        $this->activation_code = $token->user_code;
        $this->trigger_events('extra_where');
        if (!isset($id)) {
        }
        $this->trigger_events('deactivate');
        $this->set_error('deactivate_unsuccessful');
        if (!($this->ion_auth->logged_in() && $this->user()->row()->id == $id)) {
        }
        $token = $this->_generate_selector_validator_couple(20, 40);
        if ($return) {
        }
        return FALSE;
        $data = ['activation_selector' => $token->selector, 'activation_code' => $token->validator_hashed, 'active' => 0];
        $return = $this->db->affected_rows() == 1;
        $this->set_error('deactivate_unsuccessful');
        return FALSE;
        $this->db->update($this->tables['users'], $data, ['id' => $id]);
        return $return;
    }
    public function clear_forgotten_password_code($identity)
    {
        return TRUE;
        $data = ['forgotten_password_selector' => NULL, 'forgotten_password_code' => NULL, 'forgotten_password_time' => NULL];
        $this->db->update($this->tables['users'], $data, [$this->identity_column => $identity]);
        if (!empty($identity)) {
        }
        return FALSE;
    }
    public function clear_remember_code($identity)
    {
        $data = ['remember_selector' => NULL, 'remember_code' => NULL];
        return FALSE;
        return TRUE;
        $this->db->update($this->tables['users'], $data, [$this->identity_column => $identity]);
        if (!empty($identity)) {
        }
    }
    public function reset_password($identity, $new)
    {
        $this->set_error('password_change_unsuccessful');
        $this->trigger_events(['post_change_password', 'post_change_password_successful']);
        if ($this->identity_check($identity)) {
        }
        $this->trigger_events('pre_change_password');
        return FALSE;
        $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
        $this->set_message('password_change_successful');
        return $return;
        $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
        if ($return) {
        }
        $return = $this->_set_password_db($identity, $new);
    }
    public function change_password($identity, $old, $new)
    {
        $this->set_error('password_change_unsuccessful');
        $this->set_error('password_change_unsuccessful');
        if (!($query->num_rows() !== 1)) {
        }
        $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
        $this->trigger_events(['post_change_password', 'post_change_password_unsuccessful']);
        return FALSE;
        $user = $query->row();
        if (!$this->verify_password($old, $user->password, $identity)) {
        }
        $this->set_message('password_change_successful');
        return $result;
        $result = $this->_set_password_db($identity, $new);
        $query = $this->db->select('id, password')->where($this->identity_column, $identity)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if ($result) {
        }
        $this->trigger_events('pre_change_password');
        $this->trigger_events(['post_change_password', 'post_change_password_successful']);
        $this->trigger_events('extra_where');
        return FALSE;
        $this->set_error('password_change_unsuccessful');
    }
    public function username_check($username = '')
    {
        if (!empty($username)) {
        }
        $this->trigger_events('extra_where');
        return $this->db->where('username', $username)->limit(1)->count_all_results($this->tables['users']) > 0;
        $this->trigger_events('username_check');
        return FALSE;
    }
    public function email_check($email = '')
    {
        return FALSE;
        if (!empty($email)) {
        }
        return $this->db->where('email', $email)->limit(1)->count_all_results($this->tables['users']) > 0;
        $this->trigger_events('email_check');
        $this->trigger_events('extra_where');
    }
    public function identity_check($identity = '')
    {
        return $this->db->where($this->identity_column, $identity)->limit(1)->count_all_results($this->tables['users']) > 0;
        return FALSE;
        if (!empty($identity)) {
        }
        $this->trigger_events('identity_check');
    }
    public function get_user_id_from_identity($identity = '')
    {
        if (!empty($identity)) {
        }
        $user = $query->row();
        return FALSE;
        $query = $this->db->select('id')->where($this->identity_column, $identity)->limit(1)->get($this->tables['users']);
        return FALSE;
        return $user->id;
        if (!($query->num_rows() !== 1)) {
        }
    }
    public function forgotten_password($identity)
    {
        $this->trigger_events('extra_where');
        $update = ['forgotten_password_selector' => $token->selector, 'forgotten_password_code' => $token->validator_hashed, 'forgotten_password_time' => time()];
        $this->trigger_events(['post_forgotten_password', 'post_forgotten_password_unsuccessful']);
        if ($this->db->affected_rows() === 1) {
        }
        if (!empty($identity)) {
        }
        $this->trigger_events(['post_forgotten_password', 'post_forgotten_password_unsuccessful']);
        return FALSE;
        $this->db->update($this->tables['users'], $update, [$this->identity_column => $identity]);
        return $token->user_code;
        return FALSE;
        $token = $this->_generate_selector_validator_couple(20, 80);
        $this->trigger_events(['post_forgotten_password', 'post_forgotten_password_successful']);
    }
    public function get_user_by_forgotten_password_code($user_code)
    {
        $token = $this->_retrieve_selector_validator_couple($user_code);
        if (!$user) {
        }
        $user = $this->where('forgotten_password_selector', $token->selector)->users()->row();
        if (!$this->verify_password($token->validator, $user->forgotten_password_code)) {
        }
        return FALSE;
        return $user;
    }
    public function register($identity, $password, $email, $additional_data = array(), $groups = array())
    {
        foreach ($groups as $group) {
            $this->add_to_group($group, $id);
        }
        if (!($password === FALSE)) {
        }
        $password = $this->hash_password($password);
        return isset($id) ? $id : FALSE;
        $query = $this->db->get_where($this->tables['groups'], ['name' => $this->config->item('default_group', 'ion_auth')], 1)->row();
        return FALSE;
        $data = [$this->identity_column => $identity, 'username' => $identity, 'password' => $password, 'email' => $email, 'ip_address' => $ip_address, 'created_on' => time(), 'active' => $manual_activation === FALSE ? 1 : 0];
        $groups[] = $default_group->id;
        $this->set_error('account_creation_missing_default_group');
        $id = $this->db->insert_id($this->tables['users'] . '_id_seq');
        if (!(isset($default_group->id) && empty($groups))) {
        }
        $this->set_error('account_creation_unsuccessful');
        if ($this->identity_check($identity)) {
        }
        if (!(!$this->config->item('default_group', 'ion_auth') && empty($groups))) {
        }
        $this->trigger_events('pre_register');
        $this->db->insert($this->tables['users'], $user_data);
        $this->trigger_events('extra_set');
        return FALSE;
        $this->trigger_events('post_register');
        $user_data = array_merge($this->_filter_data($this->tables['users'], $additional_data), $data);
        return FALSE;
        $this->set_error('account_creation_duplicate_identity');
        $manual_activation = $this->config->item('manual_activation', 'ion_auth');
        $this->set_error('account_creation_invalid_default_group');
        $ip_address = $this->input->ip_address();
        $default_group = $query;
        return FALSE;
        if (!(!isset($query->id) && empty($groups))) {
        }
        if (empty($groups)) {
        }
    }
    public function login($identity, $password, $remember = FALSE)
    {
        $this->set_session($user);
        $this->clear_remember_code($identity);
        if (!($query->num_rows() === 1)) {
        }
        $this->trigger_events('pre_login');
        $this->rehash_password_if_needed($user->password, $identity, $password);
        $this->trigger_events(['post_login', 'post_login_successful']);
        $this->update_last_login($user->id);
        return FALSE;
        return FALSE;
        $this->set_error('login_timeout');
        $this->remember_user($identity);
        $this->hash_password($password);
        $this->trigger_events('extra_where');
        $this->increase_login_attempts($identity);
        if (!$this->is_max_login_attempts_exceeded($identity)) {
        }
        if (!$this->config->item('remember_users', 'ion_auth')) {
        }
        $this->trigger_events('post_login_unsuccessful');
        if (!$this->verify_password($password, $user->password, $identity)) {
        }
        $this->trigger_events('post_login_unsuccessful');
        $user = $query->row();
        if (!($user->active == 0)) {
        }
        $this->set_message('login_successful');
        $this->hash_password($password);
        return FALSE;
        $this->set_error('login_unsuccessful');
        $this->set_error('login_unsuccessful');
        $this->clear_login_attempts($identity);
        $query = $this->db->select($this->identity_column . ', email, id, password, active, last_login')->where($this->identity_column, $identity)->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        return TRUE;
        $this->clear_forgotten_password_code($identity);
        $this->set_error('login_unsuccessful_not_active');
        if (!(empty($identity) || empty($password))) {
        }
        if ($remember) {
        }
        $this->session->sess_regenerate(FALSE);
        $this->trigger_events('post_login_unsuccessful');
        return FALSE;
    }
    public function recheck_session()
    {
        $recheck = NULL !== $this->config->item('recheck_timer', 'ion_auth') ? $this->config->item('recheck_timer', 'ion_auth') : 0;
        if ($query->num_rows() === 1) {
        }
        return FALSE;
        $last_login = $this->session->userdata('last_check');
        $query = $this->db->select('id')->where([$this->identity_column => $this->session->userdata('identity'), 'active' => '1'])->limit(1)->order_by('id', 'desc')->get($this->tables['users']);
        if (!($last_login + $recheck < time())) {
        }
        $this->session->set_userdata('last_check', time());
        $identity = $this->config->item('identity', 'ion_auth');
        return (bool) $this->session->userdata('identity');
        if (!($recheck !== 0)) {
        }
        $this->session->unset_userdata([$identity, 'id', 'user_id']);
        $this->trigger_events('logout');
    }
    public function is_max_login_attempts_exceeded($identity, $ip_address = NULL)
    {
        return $attempts >= $max_attempts;
        $max_attempts = $this->config->item('maximum_login_attempts', 'ion_auth');
        if (!($max_attempts > 0)) {
        }
        $attempts = $this->get_attempts_num($identity, $ip_address);
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
        }
        return FALSE;
    }
    public function get_attempts_num($identity, $ip_address = NULL)
    {
        if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
        }
        if (isset($ip_address)) {
        }
        $qres = $this->db->get($this->tables['login_attempts']);
        $ip_address = $this->input->ip_address();
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
        }
        $this->db->select('1', FALSE);
        $this->db->where('time >', time() - $this->config->item('lockout_time', 'ion_auth'), FALSE);
        return $qres->num_rows();
        return 0;
        $this->db->where('login', $identity);
        $this->db->where('ip_address', $ip_address);
    }
    public function get_last_attempt_time($identity, $ip_address = NULL)
    {
        $ip_address = $this->input->ip_address();
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
        }
        $this->db->order_by('id', 'desc');
        $this->db->where('ip_address', $ip_address);
        $qres = $this->db->get($this->tables['login_attempts'], 1);
        if (!($qres->num_rows() > 0)) {
        }
        $this->db->select('time');
        $this->db->where('login', $identity);
        if (isset($ip_address)) {
        }
        return $qres->row()->time;
        return 0;
        if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
        }
    }
    public function get_last_attempt_ip($identity)
    {
        $this->db->where('login', $identity);
        $this->db->order_by('id', 'desc');
        return '';
        $this->db->select('ip_address');
        $qres = $this->db->get($this->tables['login_attempts'], 1);
        if (!($qres->num_rows() > 0)) {
        }
        if (!($this->config->item('track_login_attempts', 'ion_auth') && $this->config->item('track_login_ip_address', 'ion_auth'))) {
        }
        return $qres->row()->ip_address;
    }
    public function increase_login_attempts($identity)
    {
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
        }
        if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
        }
        $data['ip_address'] = $this->input->ip_address();
        $data = ['ip_address' => '', 'login' => $identity, 'time' => time()];
        return $this->db->insert($this->tables['login_attempts'], $data);
        return FALSE;
    }
    public function clear_login_attempts($identity, $old_attempts_expire_period = 86400, $ip_address = NULL)
    {
        $this->db->where('ip_address', $ip_address);
        $old_attempts_expire_period = max($old_attempts_expire_period, $this->config->item('lockout_time', 'ion_auth'));
        return FALSE;
        $this->db->or_where('time <', time() - $old_attempts_expire_period, FALSE);
        if (isset($ip_address)) {
        }
        return $this->db->delete($this->tables['login_attempts']);
        if (!$this->config->item('track_login_ip_address', 'ion_auth')) {
        }
        if (!$this->config->item('track_login_attempts', 'ion_auth')) {
        }
        $ip_address = $this->input->ip_address();
        $this->db->where('login', $identity);
    }
    public function limit($limit)
    {
        return $this;
        $this->trigger_events('limit');
        $this->_ion_limit = $limit;
    }
    public function offset($offset)
    {
        $this->_ion_offset = $offset;
        return $this;
        $this->trigger_events('offset');
    }
    public function where($where, $value = NULL)
    {
        $this->trigger_events('where');
        if (is_array($where)) {
        }
        $where = [$where => $value];
        array_push($this->_ion_where, $where);
        return $this;
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
        return $this;
        $this->_ion_select[] = $select;
    }
    public function order_by($by, $order = 'desc')
    {
        $this->_ion_order = $order;
        return $this;
        $this->_ion_order_by = $by;
        $this->trigger_events('order_by');
    }
    public function row()
    {
        $row = $this->response->row();
        $this->trigger_events('row');
        return $row;
    }
    public function row_array()
    {
        return $row;
        $this->trigger_events(['row', 'row_array']);
        $row = $this->response->row_array();
    }
    public function result()
    {
        $result = $this->response->result();
        return $result;
        $this->trigger_events('result');
    }
    public function result_array()
    {
        return $result;
        $this->trigger_events(['result', 'result_array']);
        $result = $this->response->result_array();
    }
    public function num_rows()
    {
        $this->trigger_events(['num_rows']);
        return $result;
        $result = $this->response->num_rows();
    }
    public function users($groups = NULL)
    {
        $this->response = $this->db->get($this->tables['users']);
        $this->db->join($this->tables['users_groups'], $this->tables['users_groups'] . '.' . $this->join['users'] . '=' . $this->tables['users'] . '.id', 'inner');
        $this->_ion_select = [];
        $this->_ion_order = NULL;
        if (empty($group_names)) {
        }
        $this->_ion_limit = NULL;
        $this->db->limit($this->_ion_limit);
        $this->_ion_like = [];
        $this->_ion_order_by = NULL;
        if (!(isset($this->_ion_order_by) && isset($this->_ion_order))) {
        }
        $group_ids = [];
        $this->_ion_limit = NULL;
        if (!isset($groups)) {
        }
        foreach ($this->_ion_select as $select) {
            $this->db->select($select);
        }
        $this->db->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['groups'] . '.id', 'inner');
        $this->db->order_by($this->_ion_order_by, $this->_ion_order);
        return $this;
        if (is_array($groups)) {
        }
        $this->db->{$or_where_in}($this->tables['users_groups'] . '.' . $this->join['groups'], $group_ids);
        foreach ($this->_ion_like as $like) {
            $this->db->or_like($like['like'], $like['value'], $like['position']);
        }
        $or_where_in = !empty($group_ids) && !empty($group_names) ? 'or_where_in' : 'where_in';
        $groups = [$groups];
        if (empty($group_ids)) {
        }
        if (!isset($this->_ion_limit)) {
        }
        if (!(isset($this->_ion_where) && !empty($this->_ion_where))) {
        }
        if (!(isset($groups) && !empty($groups))) {
        }
        if (isset($this->_ion_select) && !empty($this->_ion_select)) {
        }
        if (!(isset($this->_ion_like) && !empty($this->_ion_like))) {
        }
        foreach ($groups as $group) {
            $group_ids[] = $group;
            if (is_numeric($group)) {
            }
            $group_names[] = $group;
        }
        $this->trigger_events('extra_where');
        $group_names = [];
        $this->trigger_events('users');
        if (isset($this->_ion_limit) && isset($this->_ion_offset)) {
        }
        $this->db->distinct();
        $this->db->limit($this->_ion_limit, $this->_ion_offset);
        $this->_ion_where = [];
        foreach ($this->_ion_where as $where) {
            $this->db->where($where);
        }
        $this->db->select([$this->tables['users'] . '.*', $this->tables['users'] . '.id as id', $this->tables['users'] . '.id as user_id']);
        $this->_ion_offset = NULL;
        $this->db->where_in($this->tables['groups'] . '.name', $group_names);
    }
    public function user($id = NULL)
    {
        $this->users();
        $this->where($this->tables['users'] . '.id', $id);
        $this->trigger_events('user');
        $this->limit(1);
        $id = isset($id) ? $id : $this->session->userdata('user_id');
        return $this;
        $this->order_by($this->tables['users'] . '.id', 'desc');
    }
    public function get_users_groups($id = FALSE)
    {
        $this->trigger_events('get_users_group');
        return $this->db->select($this->tables['users_groups'] . '.' . $this->join['groups'] . ' as id, ' . $this->tables['groups'] . '.name, ' . $this->tables['groups'] . '.description')->where($this->tables['users_groups'] . '.' . $this->join['users'], $id)->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . '=' . $this->tables['groups'] . '.id')->get($this->tables['users_groups']);
        $id || $id = $this->session->userdata('user_id');
    }
    public function in_group($check_group, $id = FALSE, $check_all = FALSE)
    {
        return $check_all;
        $id || $id = $this->session->userdata('user_id');
        foreach ($users_groups as $group) {
            $groups_array[$group->id] = $group->name;
        }
        $groups_array = $this->_cache_user_in_group[$id];
        $check_group = [$check_group];
        $groups_array = [];
        foreach ($check_group as $key => $value) {
            return !$check_all;
            $groups = is_numeric($value) ? array_keys($groups_array) : $groups_array;
            if (!(in_array($value, $groups) xor $check_all)) {
            }
        }
        if (isset($this->_cache_user_in_group[$id])) {
        }
        $this->trigger_events('in_group');
        $this->_cache_user_in_group[$id] = $groups_array;
        if (is_array($check_group)) {
        }
        $users_groups = $this->get_users_groups($id)->result();
    }
    public function add_to_group($group_ids, $user_id = FALSE)
    {
        $group_ids = [$group_ids];
        $this->trigger_events('add_to_group');
        $user_id || $user_id = $this->session->userdata('user_id');
        return $return;
        if (is_array($group_ids)) {
        }
        foreach ($group_ids as $group_id) {
            $return++;
            $group = $this->group($group_id)->result();
            $this->_cache_user_in_group[$user_id][$group_id] = $group_name;
            $group_name = $group[0]->name;
            if (isset($this->_cache_groups[$group_id])) {
            }
            $this->_cache_groups[$group_id] = $group_name;
            if (!$this->db->insert($this->tables['users_groups'], [$this->join['groups'] => (float) $group_id, $this->join['users'] => (float) $user_id])) {
            }
            $group_name = $this->_cache_groups[$group_id];
        }
        $return = 0;
    }
    public function remove_from_group($group_ids = FALSE, $user_id = FALSE)
    {
        if (!$return = $this->db->delete($this->tables['users_groups'], [$this->join['users'] => (float) $user_id])) {
        }
        $this->_cache_user_in_group[$user_id] = [];
        if (is_array($group_ids)) {
        }
        if (!empty($user_id)) {
        }
        return $return;
        $group_ids = [$group_ids];
        if (!empty($group_ids)) {
        }
        $return = TRUE;
        return FALSE;
        $this->trigger_events('remove_from_group');
        foreach ($group_ids as $group_id) {
            $this->db->delete($this->tables['users_groups'], [$this->join['groups'] => (float) $group_id, $this->join['users'] => (float) $user_id]);
            unset($this->_cache_user_in_group[$user_id][$group_id]);
            if (!(isset($this->_cache_user_in_group[$user_id]) && isset($this->_cache_user_in_group[$user_id][$group_id]))) {
            }
        }
    }
    public function groups()
    {
        $this->db->limit($this->_ion_limit);
        if (isset($this->_ion_limit) && isset($this->_ion_offset)) {
        }
        $this->db->limit($this->_ion_limit, $this->_ion_offset);
        if (!(isset($this->_ion_where) && !empty($this->_ion_where))) {
        }
        if (!isset($this->_ion_limit)) {
        }
        $this->_ion_limit = NULL;
        $this->response = $this->db->get($this->tables['groups']);
        $this->trigger_events('groups');
        $this->_ion_where = [];
        $this->db->order_by($this->_ion_order_by, $this->_ion_order);
        return $this;
        foreach ($this->_ion_where as $where) {
            $this->db->where($where);
        }
        $this->_ion_offset = NULL;
        if (!(isset($this->_ion_order_by) && isset($this->_ion_order))) {
        }
        $this->_ion_limit = NULL;
    }
    public function group($id = NULL)
    {
        $this->trigger_events('group');
        if (!isset($id)) {
        }
        return $this->groups();
        $this->limit(1);
        $this->order_by('id', 'desc');
        $this->where($this->tables['groups'] . '.id', $id);
    }
    public function update($id, array $data)
    {
        if (!($data['password'] === FALSE)) {
        }
        $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
        $this->trigger_events('extra_where');
        if (!empty($data['password'])) {
        }
        $this->db->trans_begin();
        if (!($this->db->trans_status() === FALSE)) {
        }
        return FALSE;
        if (!(array_key_exists($this->identity_column, $data) || array_key_exists('password', $data) || array_key_exists('email', $data))) {
        }
        $this->db->trans_commit();
        $this->set_error('update_unsuccessful');
        $this->db->trans_rollback();
        $user = $this->user($id)->row();
        if (!(array_key_exists($this->identity_column, $data) && $this->identity_check($data[$this->identity_column]) && $user->{$this->identity_column} !== $data[$this->identity_column])) {
        }
        $data['password'] = $this->hash_password($data['password'], $user->{$this->identity_column});
        $data = $this->_filter_data($this->tables['users'], $data);
        $this->trigger_events('pre_update_user');
        if (!array_key_exists('password', $data)) {
        }
        $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
        $this->set_message('update_successful');
        unset($data['password']);
        return TRUE;
        $this->set_error('update_unsuccessful');
        $this->set_error('account_creation_duplicate_identity');
        $this->db->update($this->tables['users'], $data, ['id' => $user->id]);
        $this->set_error('update_unsuccessful');
        $this->trigger_events(['post_update_user', 'post_update_user_unsuccessful']);
        return FALSE;
        $this->db->trans_rollback();
        $this->trigger_events(['post_update_user', 'post_update_user_successful']);
        $this->db->trans_rollback();
        return FALSE;
    }
    public function delete_user($id)
    {
        $this->db->trans_commit();
        $this->set_error('delete_unsuccessful');
        return TRUE;
        $this->trigger_events(['post_delete_user', 'post_delete_user_unsuccessful']);
        $this->remove_from_group(NULL, $id);
        $this->set_message('delete_successful');
        $this->db->trans_rollback();
        $this->trigger_events('pre_delete_user');
        $this->trigger_events(['post_delete_user', 'post_delete_user_successful']);
        if (!($this->db->trans_status() === FALSE)) {
        }
        $this->db->trans_begin();
        return FALSE;
        $this->db->delete($this->tables['users'], ['id' => $id]);
    }
    public function update_last_login($id)
    {
        return $this->db->affected_rows() == 1;
        $this->db->update($this->tables['users'], ['last_login' => time()], ['id' => $id]);
        $this->trigger_events('update_last_login');
        $this->load->helper('date');
        $this->trigger_events('extra_where');
    }
    public function set_lang($lang = 'en')
    {
        $expire = $this->config->item('user_expire', 'ion_auth');
        $expire = self::MAX_COOKIE_LIFETIME;
        return TRUE;
        if ($this->config->item('user_expire', 'ion_auth') === 0) {
        }
        set_cookie(['name' => 'lang_code', 'value' => $lang, 'expire' => $expire]);
        $this->trigger_events('set_lang');
    }
    public function set_session($user)
    {
        return TRUE;
        $this->trigger_events('post_set_session');
        $this->trigger_events('pre_set_session');
        $this->session->set_userdata($session_data);
        $session_data = ['identity' => $user->{$this->identity_column}, $this->identity_column => $user->{$this->identity_column}, 'email' => $user->email, 'user_id' => $user->id, 'old_last_login' => $user->last_login, 'last_check' => time()];
    }
    public function remember_user($identity)
    {
        if ($identity) {
        }
        $this->trigger_events(['post_remember_user', 'remember_user_unsuccessful']);
        set_cookie(['name' => $this->config->item('remember_cookie_name', 'ion_auth'), 'value' => $token->user_code, 'expire' => $expire]);
        $this->trigger_events('pre_remember_user');
        if (!($this->db->affected_rows() > -1)) {
        }
        return FALSE;
        $expire = $this->config->item('user_expire', 'ion_auth');
        return FALSE;
        $token = $this->_generate_selector_validator_couple();
        $expire = self::MAX_COOKIE_LIFETIME;
        if ($this->config->item('user_expire', 'ion_auth') === 0) {
        }
        if (!$token->validator_hashed) {
        }
        $this->db->update($this->tables['users'], ['remember_selector' => $token->selector, 'remember_code' => $token->validator_hashed], [$this->identity_column => $identity]);
        $this->trigger_events(['post_remember_user', 'remember_user_successful']);
        return TRUE;
    }
    public function login_remembered_user()
    {
        $this->trigger_events(['post_login_remembered_user', 'post_login_remembered_user_unsuccessful']);
        return FALSE;
        $this->trigger_events('extra_where');
        return FALSE;
        $this->clear_forgotten_password_code($identity);
        $this->update_last_login($user->id);
        $this->set_session($user);
        if (!$this->config->item('user_extend_on_login', 'ion_auth')) {
        }
        $this->remember_user($identity);
        delete_cookie($this->config->item('remember_cookie_name', 'ion_auth'));
        $this->session->sess_regenerate(FALSE);
        $user = $query->row();
        $token = $this->_retrieve_selector_validator_couple($remember_cookie);
        return TRUE;
        $identity = $user->{$this->identity_column};
        $this->trigger_events(['post_login_remembered_user', 'post_login_remembered_user_unsuccessful']);
        $remember_cookie = get_cookie($this->config->item('remember_cookie_name', 'ion_auth'));
        $this->trigger_events(['post_login_remembered_user', 'post_login_remembered_user_successful']);
        if (!($token === FALSE)) {
        }
        if (!$this->verify_password($token->validator, $user->remember_code, $identity)) {
        }
        if (!($query->num_rows() === 1)) {
        }
        $this->trigger_events('pre_login_remembered_user');
        $query = $this->db->select($this->identity_column . ', id, email, remember_code, last_login')->where('remember_selector', $token->selector)->where('active', 1)->limit(1)->get($this->tables['users']);
    }
    public function create_group($group_name = FALSE, $group_description = '', $additional_data = array())
    {
        $this->set_error('group_already_exists');
        $group_id = $this->db->insert_id($this->tables['groups'] . '_id_seq');
        $this->set_error('group_name_required');
        return FALSE;
        $this->trigger_events('extra_group_set');
        $data = ['name' => $group_name, 'description' => $group_description];
        $existing_group = $this->db->get_where($this->tables['groups'], ['name' => $group_name])->num_rows();
        return FALSE;
        if (empty($additional_data)) {
        }
        $this->set_message('group_creation_successful');
        $this->db->insert($this->tables['groups'], $data);
        $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);
        if (!($existing_group !== 0)) {
        }
        return $group_id;
        if ($group_name) {
        }
    }
    public function update_group($group_id = FALSE, $group_name = FALSE, $additional_data = array())
    {
        $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);
        if (empty($group_name)) {
        }
        $group = $this->db->get_where($this->tables['groups'], ['id' => $group_id])->row();
        if (!(isset($existing_group->id) && $existing_group->id != $group_id)) {
        }
        $data['name'] = $group_name;
        $this->db->update($this->tables['groups'], $data, ['id' => $group_id]);
        return FALSE;
        $existing_group = $this->db->get_where($this->tables['groups'], ['name' => $group_name])->row();
        if (!empty($group_id)) {
        }
        if (empty($additional_data)) {
        }
        $this->set_error('group_already_exists');
        if (!($this->config->item('admin_group', 'ion_auth') === $group->name && $group_name !== $group->name)) {
        }
        $this->set_error('group_name_admin_not_alter');
        return FALSE;
        $this->set_message('group_update_successful');
        $data = [];
        return FALSE;
        return TRUE;
    }
    public function delete_group($group_id = FALSE)
    {
        $this->db->trans_commit();
        $this->set_message('group_delete_successful');
        $this->set_error('group_delete_unsuccessful');
        if (!($this->db->trans_status() === FALSE)) {
        }
        $this->db->trans_begin();
        $this->trigger_events('pre_delete_group');
        $this->trigger_events(['post_delete_group', 'post_delete_group_notallowed']);
        $this->db->delete($this->tables['groups'], ['id' => $group_id]);
        return FALSE;
        $this->trigger_events(['post_delete_group', 'post_delete_group_successful']);
        $this->db->delete($this->tables['users_groups'], [$this->join['groups'] => $group_id]);
        $this->db->trans_rollback();
        return FALSE;
        return FALSE;
        $this->trigger_events(['post_delete_group', 'post_delete_group_unsuccessful']);
        return TRUE;
        $this->set_error('group_delete_notallowed');
        if (!(!$group_id || empty($group_id))) {
        }
        if (!($group->name == $this->config->item('admin_group', 'ion_auth'))) {
        }
        $group = $this->group($group_id)->row();
    }
    public function set_hook($event, $name, $class, $method, $arguments)
    {
        $this->_ion_hooks->{$event}[$name]->method = $method;
        $this->_ion_hooks->{$event}[$name]->arguments = $arguments;
        $this->_ion_hooks->{$event}[$name]->class = $class;
        $this->_ion_hooks->{$event}[$name] = new stdClass();
    }
    public function remove_hook($event, $name)
    {
        unset($this->_ion_hooks->{$event}[$name]);
        if (!isset($this->_ion_hooks->{$event}[$name])) {
        }
    }
    public function remove_hooks($event)
    {
        unset($this->_ion_hooks->{$event});
        if (!isset($this->_ion_hooks->{$event})) {
        }
    }
    protected function _call_hook($event, $name)
    {
        return FALSE;
        $hook = $this->_ion_hooks->{$event}[$name];
        if (!(isset($this->_ion_hooks->{$event}[$name]) && method_exists($this->_ion_hooks->{$event}[$name]->class, $this->_ion_hooks->{$event}[$name]->method))) {
        }
        return call_user_func_array([$hook->class, $hook->method], $hook->arguments);
    }
    public function trigger_events($events)
    {
        foreach ($events as $event) {
            $this->trigger_events($event);
        }
        foreach ($this->_ion_hooks->{$events} as $name => $hook) {
            $this->_call_hook($events, $name);
        }
        if (is_array($events) && !empty($events)) {
        }
        if (!(isset($this->_ion_hooks->{$events}) && !empty($this->_ion_hooks->{$events}))) {
        }
    }
    public function set_message_delimiters($start_delimiter, $end_delimiter)
    {
        $this->message_end_delimiter = $end_delimiter;
        return TRUE;
        $this->message_start_delimiter = $start_delimiter;
    }
    public function set_error_delimiters($start_delimiter, $end_delimiter)
    {
        return TRUE;
        $this->error_start_delimiter = $start_delimiter;
        $this->error_end_delimiter = $end_delimiter;
    }
    public function set_message($message)
    {
        $this->messages[] = $message;
        return $message;
    }
    public function messages()
    {
        foreach ($this->messages as $message) {
            $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
            $_output .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
        }
        return $_output;
        $_output = '';
    }
    public function messages_array($langify = TRUE)
    {
        if ($langify) {
        }
        foreach ($this->messages as $message) {
            $messageLang = $this->lang->line($message) ? $this->lang->line($message) : '##' . $message . '##';
            $_output[] = $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
        }
        $_output = [];
        return $this->messages;
        return $_output;
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
        return $_output;
        foreach ($this->errors as $error) {
            $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
            $_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
        }
        $_output = '';
    }
    public function errors_array($langify = TRUE)
    {
        $_output = [];
        foreach ($this->errors as $error) {
            $errorLang = $this->lang->line($error) ? $this->lang->line($error) : '##' . $error . '##';
            $_output[] = $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
        }
        return $_output;
        return $this->errors;
        if ($langify) {
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
        $this->db->update($this->tables['users'], $data, [$this->identity_column => $identity]);
        $data = ['password' => $hash, 'remember_code' => NULL, 'forgotten_password_code' => NULL, 'forgotten_password_time' => NULL];
        $this->trigger_events('extra_where');
        return FALSE;
        return $this->db->affected_rows() == 1;
        if (!($hash === FALSE)) {
        }
    }
    protected function _filter_data($table, $data)
    {
        $filtered_data = [];
        $columns = $this->db->list_fields($table);
        return $filtered_data;
        if (!is_array($data)) {
        }
        foreach ($columns as $column) {
            if (!array_key_exists($column, $data)) {
            }
            $filtered_data[$column] = $data[$column];
        }
    }
    protected function _random_token($result_length = 32)
    {
        return bin2hex(mcrypt_create_iv($result_length / 2, MCRYPT_DEV_URANDOM));
        if (!function_exists('mcrypt_create_iv')) {
        }
        $result_length = 32;
        if (!function_exists('openssl_random_pseudo_bytes')) {
        }
        return bin2hex(openssl_random_pseudo_bytes($result_length / 2));
        if (!(!isset($result_length) || intval($result_length) <= 8)) {
        }
        return bin2hex(random_bytes($result_length / 2));
        if (!function_exists('random_bytes')) {
        }
        return FALSE;
    }
    protected function _get_hash_parameters($identity = NULL)
    {
        switch ($this->hash_method) {
            case 'bcrypt':
                $params = ['cost' => $is_admin ? $this->config->item('bcrypt_admin_cost', 'ion_auth') : $this->config->item('bcrypt_default_cost', 'ion_auth')];
            case 'argon2':
                $params = $is_admin ? $this->config->item('argon2_admin_params', 'ion_auth') : $this->config->item('argon2_default_params', 'ion_auth');
            default:
        }
        $is_admin = TRUE;
        if (!($user_id && $this->in_group($this->config->item('admin_group', 'ion_auth'), $user_id))) {
        }
        if (!$identity) {
        }
        $is_admin = FALSE;
        $user_id = $this->get_user_id_from_identity($identity);
        $params = FALSE;
        return $params;
    }
    protected function _get_hash_algo()
    {
        return $algo;
        $algo = FALSE;
        switch ($this->hash_method) {
            case 'bcrypt':
                $algo = PASSWORD_BCRYPT;
            case 'argon2':
                $algo = PASSWORD_ARGON2I;
            default:
        }
    }
    protected function _generate_selector_validator_couple($selector_size = 40, $validator_size = 128)
    {
        $selector = $this->_random_token($selector_size);
        $validator_hashed = $this->hash_password($validator);
        return (object) ['selector' => $selector, 'validator_hashed' => $validator_hashed, 'user_code' => $user_code];
        $user_code = "{$selector}.{$validator}";
        $validator = $this->_random_token($validator_size);
    }
    protected function _retrieve_selector_validator_couple($user_code)
    {
        if (!$user_code) {
        }
        if (!(count($tokens) === 2)) {
        }
        return (object) ['selector' => $tokens[0], 'validator' => $tokens[1]];
        $tokens = explode('.', $user_code);
        return FALSE;
    }
    protected function _password_verify_sha1_legacy($identity, $password, $hashed_password_db)
    {
        if ($salt_length) {
        }
        if ($result) {
        }
        $this->trigger_events('pre_sha1_password_migration');
        $query = $this->db->select('salt')->where($this->identity_column, $identity)->limit(1)->get($this->tables['users']);
        return $result;
        if ($this->config->item('store_salt', 'ion_auth')) {
        }
        $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_successful']);
        $result = $this->_set_password_db($identity, $password);
        $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_unsuccessful']);
        $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_unsuccessful']);
        $hashed_password = sha1($password . $salt_db->salt);
        $salt_db = $query->row();
        $salt = substr($hashed_password_db, 0, $salt_length);
        $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_unsuccessful']);
        return FALSE;
        return FALSE;
        if ($hashed_password === $hashed_password_db) {
        }
        $this->trigger_events(['post_sha1_password_migration', 'post_sha1_password_migration_unsuccessful']);
        $salt_length = $this->config->item('salt_length', 'ion_auth');
        $hashed_password = $salt . substr(sha1($salt . $password), 0, -$salt_length);
        if (!($query->num_rows() !== 1)) {
        }
        return FALSE;
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
        $os = $this->agent->platform();
        return $this->insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip);
        if ($agent == 'unknown') {
        }
        if ($this->agent->is_mobile()) {
        }
        if ($this->agent->is_browser()) {
        }
        $agent = $this->agent->browser() . ' ' . $this->agent->version();
        return 'error';
        $ip = $this->input->ip_address();
        $agent = 'unknown';
        $agent = $this->agent->mobile();
    }
    private function insertLog($table, $id_siswa, $id_kjm, $jamke, $mapel, $desc, $agent, $os, $ip)
    {
        $data = array('id_log' => $id_siswa . $id_kjm, 'log_time' => date('Y-m-d H:i:s'), 'id_siswa' => $id_siswa, 'id_materi' => $id_kjm, 'id_mapel' => $mapel, 'jam_ke' => $jamke, 'log_desc' => $desc, 'address' => $ip, 'agent' => $agent, 'device' => $os);
        return $this->db->insert($table, $data);
    }
    public function getKelasList($tp, $smt)
    {
        return $query->result();
        $this->db->join('master_siswa e', 'e.id_siswa=a.siswa_id', 'left');
        $this->db->where('a.id_tp', $tp);
        $this->db->join('level_kelas c', 'c.id_level=a.level_id', 'left');
        $this->db->from('master_kelas a');
        $this->db->join('master_guru d', 'd.id_guru=f.id_guru', 'left');
        $this->db->order_by('a.level_id', 'ASC');
        $this->db->join('master_jurusan b', 'b.id_jurusan=a.jurusan_id', 'left');
        $this->db->select('a.*, b.nama_jurusan, d.nama_guru, e.nama, (SELECT COUNT(id_kelas_siswa) FROM kelas_siswa k WHERE a.id_kelas=k.id_kelas) AS jml_siswa');
        $this->db->join('jabatan_guru f', 'f.id_jabatan=4 AND f.id_kelas=a.id_kelas', 'left');
        $query = $this->db->get();
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('a.nama_kelas', 'ASC');
    }
    public function getJmlSiswaKelas($id_kelas)
    {
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->count_all_results();
        $this->db->from('kelas_siswa');
    }
    public function get_all($limit, $offset)
    {
        $result = $this->db->get('master_kelas', $limit, $offset);
        return array();
        return $result->result_array();
        if ($result->num_rows() > 0) {
        }
    }
    public function getAllKelas()
    {
        return $this->db->get()->result();
        $this->db->select('a.id_kelas, a.id_tp, a.id_smt, a.nama_kelas, a.kode_kelas, a.level_id, b.id_jurusan, b.nama_jurusan, b.kode_jurusan, c.id_guru, c.nama_guru');
        $this->db->join('master_jurusan b', 'a.jurusan_id=b.id_jurusan', 'left');
        $this->db->join('jabatan_guru f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->order_by('a.nama_kelas');
        $this->db->join('master_guru c', 'f.id_guru=c.id_guru', 'left');
        $this->db->from('master_kelas a');
    }
    public function count_all()
    {
        $this->db->from('master_kelas');
        return $this->db->count_all_results();
    }
    public function get_search($limit, $offset)
    {
        $this->db->limit($limit, $offset);
        $keyword = $this->session->userdata('keyword');
        return array();
        $result = $this->db->get('master_kelas');
        $this->db->like('nama_kelas', $keyword);
        $this->db->like('jumlah_siswa', $keyword);
        if ($result->num_rows() > 0) {
        }
        return $result->result_array();
    }
    public function count_all_search()
    {
        $this->db->like('jumlah_siswa', $keyword);
        $this->db->like('nama_kelas', $keyword);
        $keyword = $this->session->userdata('keyword');
        return $this->db->count_all_results();
        $this->db->from('master_kelas');
    }
    public function get_one($id, $id_tp = null, $id_smt = null)
    {
        $this->db->join('master_siswa si', 'si.id_siswa=k.siswa_id', 'left');
        $this->db->join('master_guru g', 'g.id_guru=f.id_guru', 'left');
        if (!($id_smt != null)) {
        }
        $this->db->where('k.id_tp', $id_tp);
        $this->db->select('*');
        $this->db->join('level_kelas l', 'l.id_level=k.level_id', 'left');
        $this->db->join('master_jurusan j', 'j.id_jurusan=k.jurusan_id', 'left');
        $this->db->order_by('nama_kelas', 'ASC');
        $this->db->from('master_kelas k');
        return $this->db->get()->row();
        $this->db->join('jabatan_guru f', 'f.id_kelas=k.id_kelas', 'left');
        $this->db->where('k.id_kelas', $id);
        $this->db->where('k.id_smt', $id_smt);
        if (!($id_tp != null)) {
        }
    }
    public function getKelasByNama($nama_kelas, $id_tp = null, $id_smt = null)
    {
        $this->db->where('k.id_tp', $id_tp);
        $this->db->from('master_kelas k');
        $this->db->join('level_kelas l', 'l.id_level=k.level_id', 'left');
        $this->db->join('master_guru g', 'g.id_guru=f.id_guru', 'left');
        $this->db->where('k.nama_kelas', $nama_kelas);
        $this->db->select('*');
        return $this->db->get()->row();
        $this->db->order_by('nama_kelas', 'ASC');
        $this->db->join('jabatan_guru f', 'f.id_kelas=k.id_kelas', 'left');
        $this->db->where('k.id_smt', $id_smt);
        if (!($id_tp != null)) {
        }
        if (!($id_smt != null)) {
        }
        $this->db->join('master_siswa si', 'si.id_siswa=k.siswa_id', 'left');
        $this->db->join('master_jurusan j', 'j.id_jurusan=k.jurusan_id', 'left');
    }
    public function getNamaKelasByNama($id_tp, $id_smt)
    {
        return $ret;
        $this->db->where('id_tp', $id_tp);
        foreach ($result as $row) {
            $ret[$row->nama_kelas] = $row->id_kelas;
        }
        $this->db->where('id_smt', $id_smt);
        if (!$result) {
        }
        $this->db->from('master_kelas');
        $ret = [];
        $this->db->select('id_kelas, nama_kelas');
        $result = $this->db->get()->result();
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
        if (!$result) {
        }
        $result = $this->db->get('master_jurusan')->result();
        $ret[''] = 'Pilih Jurusan :';
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_jurusan] = $row->nama_jurusan;
        }
    }
    public function getJurusanById($id)
    {
        $this->db->where('id_jurusan', $id);
        return $this->db->get('master_jurusan')->row();
    }
    public function get_level()
    {
        return $ret;
        $ret[''] = 'Pilih Level :';
        $result = $this->db->get('level_kelas')->result();
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_level] = $row->level;
        }
    }
    public function getLevel($jenjang)
    {
        $levels = ['' => 'Pilih Level', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
        $levels = ['' => 'Pilih Level', '7' => '7', '8' => '8', '9' => '9'];
        if ($jenjang == '1') {
        }
        if ($jenjang == '2') {
        }
        $levels = ['' => 'Pilih Level', '10' => '10', '11' => '11', '12' => '12'];
        if ($jenjang == '3') {
        }
        return $levels;
        $levels = [];
    }
    public function get_guru()
    {
        $ret[''] = 'Pilih Guru :';
        foreach ($result as $key => $row) {
            $ret[$row->id_guru] = $row->nama_guru;
        }
        if (!$result) {
        }
        return $ret;
        $result = $this->db->get('master_guru')->result();
    }
    public function getWaliKelas($tp, $smt)
    {
        if (!$result) {
        }
        $ret[''] = 'Pilih Guru :';
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru', 'left');
        $this->db->from('jabatan_guru a');
        $this->db->where('id_jabatan', '4')->where('id_tp', $tp)->where('id_smt', $smt);
        return $ret;
        $this->db->select('a.id_guru, b.nama_guru');
        $result = $this->db->get()->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_guru] = $row->nama_guru;
        }
    }
    public function getKelasEkskul($kelas, $tp, $smt)
    {
        $this->db->where('id_kelas', $kelas);
        $this->db->select('*');
        return $this->db->get()->result();
        $this->db->from('kelas_ekstra');
        $this->db->where('id_smt', $smt);
        $this->db->where('id_tp', $tp);
    }
    public function getEkskulById($id)
    {
        return $this->db->get()->row();
        $this->db->where('id_ekstra', $id);
        $this->db->from('master_ekstra');
        $this->db->select('*');
    }
    public function getAllSiswa($tp, $smt)
    {
        return $this->db->get()->result();
        $this->db->select('a.id_siswa, a.nama, b.id_kelas, a.nis');
        $this->db->join('kelas_siswa b', 'b.id_siswa=a.id_siswa AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt, 'left');
        $this->db->from('master_siswa a');
        $this->db->join('buku_induk c', 'c.id_siswa=a.id_siswa AND c.status=1');
        $this->db->order_by('a.nama', 'ASC');
    }
    public function get_siswa_kelas($id, $tp, $smt)
    {
        $this->db->from('kelas_siswa a');
        $this->db->select('a.id_siswa, a.id_kelas, b.nis, b.nama');
        $this->db->order_by('b.nama', 'ASC');
        return $this->db->get()->result();
        $this->db->join('buku_induk i', 'i.id_siswa=a.id_siswa AND i.status=1');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->where_in('a.id_kelas', $id);
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
    }
    public function get_status_siswa_kelas($id, $tp, $smt)
    {
        if (!$result) {
        }
        $this->db->where('a.id_smt', $smt);
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa] = $row;
        }
        $result = $this->db->get()->result();
        $this->db->where_in('a.id_kelas', $id);
        $this->db->where('a.id_tp', $tp);
        $ret = [];
        $this->db->select('a.id_siswa, a.id_kelas');
        $this->db->from('kelas_siswa a');
        return $ret;
    }
    public function getJadwalKbm($tp, $smt, $kelas)
    {
        $this->db->from('kelas_jadwal_kbm');
        $this->db->where('id_kelas', $kelas);
        $this->db->select('*');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        return $this->db->get()->row();
    }
    public function getJadwalKbmByArrKelas($tp, $smt, $arr_kelas)
    {
        if (!$result) {
        }
        $result = $this->db->get()->result();
        $this->db->where('id_tp', $tp);
        $this->db->where_in('id_kelas', $arr_kelas);
        return $ret;
        $this->db->from('kelas_jadwal_kbm');
        $this->db->select('*');
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = [];
            if (isset($ret[$row->id_kelas])) {
            }
            array_push($ret[$row->id_kelas], $row);
            array_push($ret[$row->id_kelas], $row);
        }
        $this->db->where('id_smt', $smt);
        $ret = [];
    }
    public function getJadwalMapel($tp, $smt)
    {
        $this->db->where('id_tp', $tp);
        foreach ($result as $key => $row) {
            if (isset($ret[$row->id_mapel][$row->id_kelas])) {
            }
            $ret[$row->id_mapel][$row->id_kelas] = [];
            if (!($row->id_mapel != '')) {
            }
            array_push($ret[$row->id_mapel][$row->id_kelas], $row);
            array_push($ret[$row->id_mapel][$row->id_kelas], $row);
        }
        return $ret;
        $ret = [];
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->where('id_smt', $smt);
        if (!$result) {
        }
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $result = $this->db->get()->result();
        $this->db->select('*');
    }
    public function getJadwalMapelGroupHari($tp, $smt)
    {
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->select('id_tp, id_smt, MAX(id_hari) as id_hari, MAX(jam_ke) as jam_ke');
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->from('kelas_jadwal_mapel');
        $this->db->group_by('id_hari');
        return $this->db->get()->result();
    }
    public function getJadwalMapelGroupJam($tp, $smt, $kelas)
    {
        $this->db->where('id_kelas', $kelas, FALSE);
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->from('kelas_jadwal_mapel');
        $this->db->select('id_tp, id_smt, MAX(id_hari) as id_hari, id_kelas, MAX(jam_ke) as jam_ke');
        return $this->db->get()->result();
        $this->db->group_by('jam_ke');
    }
    public function getJadwalMapelByJam($hari)
    {
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->select('*');
        return $this->db->get()->result();
        $this->db->where('id_hari', $hari, FALSE);
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
    }
    public function getJadwalMapelByMapel($kelas, $mapel, $tp, $smt)
    {
        $this->db->select('*');
        $this->db->where_in('a.id_kelas', $kelas);
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->where('a.id_tp', $tp, FALSE);
        return $this->db->get()->result();
        $this->db->where('a.id_smt', $smt, FALSE);
        if (!($mapel != null)) {
        }
        $this->db->where('a.id_mapel', $mapel, FALSE);
    }
    public function getJadwalTerisi($table, $kelas, $mapel, $tp, $smt)
    {
        return $this->db->get()->result();
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->where('id_mapel', $mapel, FALSE);
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->select('*');
        $this->db->where_in('id_kelas', $kelas);
        $this->db->from($table);
    }
    public function getJadwalMapelByHari($tp, $smt, $jam, $kelas)
    {
        $this->db->where('id_tp', $tp, FALSE);
        $this->db->join('master_mapel b', 'a.id_mapel=b.id_mapel', 'left');
        $this->db->where('jam_ke', $jam, FALSE);
        $this->db->where('id_smt', $smt, FALSE);
        $this->db->select('*');
        return $this->db->get()->result();
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->where('id_kelas', $kelas, FALSE);
    }
    public function getDummyJadwalMapel($tp, $smt, $jam, $kelas)
    {
        return $inputData;
        $inputData = [];
        if (!($i < 7)) {
        }
        $i++;
        $i = 1;
        $data = json_decode(json_encode(['id_tp' => $tp, 'id_smt' => $smt, 'id_hari' => $i, 'jam_ke' => $jam, 'id_kelas' => $kelas, 'id_mapel' => '0', 'nama_mapel' => '', 'kode' => '']));
        array_push($inputData, $data);
    }
    public function getDummyMateri()
    {
        return array('id_materi' => '', 'kode_materi' => '', 'id_guru' => '', 'id_mapel' => '', 'id_jadwal' => '', 'materi_kelas' => serialize([]), 'kelas_guru' => serialize([]), 'judul_materi' => '', 'isi_materi' => '', 'file' => '', 'link_file' => '', 'tgl_mulai' => '', 'created_on' => '', 'updated_on' => '');
    }
    public function getTableMateriKelas($id_guru = null)
    {
        $this->datatables->join('jabatan_guru c', 'a.id_guru=c.id_guru');
        return $this->datatables->generate();
        $this->datatables->join('kelas_jadwal_mapel d', 'a.id_mapel=d.id_mapel');
        $this->datatables->from('kelas_materi a');
        $this->datatables->select('*');
        $this->datatables->join('master_guru b', 'a.id_guru=b.id_guru');
    }
    public function getMateriKelas($id_guru, $tp, $smt)
    {
        $this->db->select('a.id_materi, a.kode_materi, a.kode_mapel, a.judul_materi, a.materi_kelas, f.nama_smt, e.tahun,' . ' a.id_mapel, a.created_on, a.updated_on, a.file, a.status, a.id_tp, a.id_smt, b.nama_guru, d.nama_mapel, d.kode');
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_tp', $tp);
        return $this->db->get()->result();
        $this->db->from('kelas_materi a');
        $this->db->order_by('a.created_on', 'DESC');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru');
        $this->db->where('a.id_smt', $smt);
        $this->db->where('a.id_guru', $id_guru);
    }
    public function getAllMateriByKelas($tp, $smt)
    {
        $ret = [];
        $this->db->join('master_mapel c', 'a.id_mapel=c.id_mapel', 'left');
        $this->db->where('a.id_smt', $smt);
        $this->db->order_by('a.created_on', 'DESC');
        $result = $this->db->get()->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_mapel][$row->jenis][$row->id_materi] = $row->kode_materi;
        }
        $this->db->where('a.id_tp', $tp);
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru');
        if (!$result) {
        }
        $this->db->where('a.status', '1');
        $this->db->select('a.jenis, a.id_mapel, a.id_materi, a.kode_materi');
        $this->db->from('kelas_materi a');
        return $ret;
    }
    public function getAllJadwalMateriByKelas($tp, $smt)
    {
        $this->db->select('a.jenis, a.id_materi, a.id_tp, a.id_smt, a.id_mapel, a.id_kjm, a.id_kelas, a.jadwal_materi,' . ' c.kode_materi, c.judul_materi, c.created_on, c.updated_on, c.file, c.status,' . ' b.nama_guru, d.nama_mapel, d.kode');
        $ret = [];
        $this->db->where('a.id_tp', $tp);
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi c', 'a.id_materi=c.id_materi', 'left');
        $this->db->where('a.id_smt', $smt);
        foreach ($result as $key => $row) {
            $ret[$row->jenis][$row->id_kjm] = $row;
        }
        $this->db->join('master_guru b', 'c.id_guru=b.id_guru');
        if (!$result) {
        }
        return $ret;
        $result = $this->db->get()->result();
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->order_by('c.created_on', 'DESC');
    }
    public function getAllMateriKelas($id_guru, $jenis)
    {
        $this->db->where('a.jenis', $jenis);
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel OR a.kode_mapel=d.kode', 'left');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('master_tp e', 'a.id_tp=e.id_tp', 'left');
        $this->db->order_by('a.created_on', 'DESC');
        $this->db->join('master_smt f', 'a.id_smt=f.id_smt', 'left');
        $this->db->where('a.id_guru', $id_guru);
        if (!($id_guru != '0')) {
        }
        $this->db->from('kelas_materi a');
        return $this->db->get()->result();
        $this->db->select('a.id_materi, a.kode_materi, a.kode_mapel, a.judul_materi, a.materi_kelas, f.nama_smt, e.tahun, f.smt,' . ' a.id_mapel, a.created_on, a.updated_on, a.file, a.status, a.id_tp, a.id_smt, b.nama_guru, d.nama_mapel, d.kode');
    }
    public function getMateriKelasById($id_materi, $jenis)
    {
        $this->db->where('a.jenis', $jenis);
        return $this->db->get()->row();
        $this->db->join('master_mapel d', 'a.id_mapel=d.id_mapel', 'left');
        $this->db->join('jabatan_guru c', 'a.id_guru=c.id_guru', 'left');
        $this->db->select('a.*, b.nama_guru, b.foto, d.id_mapel, d.nama_mapel, c.mapel_kelas as kelas_guru');
        $this->db->where('a.id_materi', $id_materi);
        $this->db->from('kelas_materi a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
    }
    public function getMateriKelasSiswa($id_kjm, $jenis)
    {
        $this->db->select('a.id_kjm, a.id_materi, a.jadwal_materi, b.*, c.nama_guru, c.foto, e.id_mapel, e.nama_mapel, d.mapel_kelas as kelas_guru');
        $this->db->from('kelas_jadwal_materi a');
        $this->db->where('a.id_kjm', $id_kjm);
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi');
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru');
        return $this->db->get()->row();
        $this->db->join('jabatan_guru d', 'b.id_guru=d.id_guru');
        $this->db->where('a.jenis', $jenis);
        $this->db->join('master_mapel e', 'b.id_mapel=e.id_mapel');
    }
    public function getGuruMapelKelas($id_guru, $tp, $smt)
    {
        return $this->db->get()->row();
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->db->from('master_guru a');
        $this->db->where('a.id_guru', $id_guru);
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
    }
    public function getMapelGuruKelas($tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->from('master_guru a');
        return $this->db->get()->result();
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
    }
    public function getListGuruMapelKelas($tp, $smt)
    {
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->from('master_guru a');
        foreach ($result as $guru) {
            $rest[$guru->id_guru] = $guru;
        }
        return $rest;
        $rest = [];
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->db->select('a.id_guru, a.nama_guru, a.kode_guru, b.mapel_kelas, b.ekstra_kelas, d.nama_kelas');
        $result = $this->db->get()->result();
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
    }
    public function getIdKelas($tp, $smt)
    {
        $this->db->select('id_kelas');
        if (!$result) {
        }
        $this->db->where('id_smt', $smt);
        foreach ($result as $key => $row) {
            array_push($ret, $row->id_kelas);
        }
        return $ret;
        $ret = [];
        $this->db->where('id_tp', $tp);
        $result = $this->db->get('master_kelas')->result();
    }
    public function getNamaKelasById($arr_id)
    {
        $this->db->where_in('id_kelas', $arr_id);
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        $result = $this->db->get('master_kelas')->result();
        return $ret;
        if (!$result) {
        }
        $this->db->select('id_kelas, nama_kelas');
        $ret = null;
    }
    public function getNamaKelasByKode($arr_kode)
    {
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        $ret = null;
        $result = $this->db->get('master_kelas')->result();
        return $ret;
        $this->db->where_in('kode_kelas', $arr_kode);
        $this->db->select('id_kelas, nama_kelas');
    }
    public function getJadwalByMateri($id, $jenis, $tp, $smt)
    {
        return $ret;
        $ret = [];
        foreach ($result as $key => $row) {
            array_push($ret[$row->id_kelas], $row);
            array_push($ret[$row->id_kelas], $row);
            $ret[$row->id_kelas] = [];
            if (isset($ret[$row->id_kelas])) {
            }
        }
        $this->db->where('id_materi', $id);
        $this->db->where('id_tp', $tp);
        if (!$result) {
        }
        $this->db->where('id_smt', $smt);
        $this->db->where('jenis', $jenis);
        $this->db->select('id_kjm, id_kelas, jadwal_materi, (SELECT COUNT(id_materi) FROM log_materi WHERE kelas_jadwal_materi.id_kjm=log_materi.id_materi) AS jml_siswa');
        $result = $this->db->get('kelas_jadwal_materi')->result();
    }
    public function getKodeMateriMapel($id_tp, $id_smt, $id_mapel, $id_guru = null)
    {
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_guru', $id_guru);
        $this->db->join('kelas_jadwal_materi c', 'a.id_materi=c.id_materi');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, c.id_kjm, c.jadwal_materi, c.id_kelas, d.nama_guru');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('master_guru d', 'a.id_guru=d.id_guru');
        if (!($id_guru != null)) {
        }
        $this->db->from('kelas_materi a');
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        return $this->db->get()->result();
    }
    public function getAllKodeMateri($id_tp, $id_smt, $id_guru = null)
    {
        if (!($id_guru != null)) {
        }
        return $this->db->get()->result();
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('a.id_mapel, a.id_materi, a.jenis, a.kode_materi, a.materi_kelas, a.id_guru, b.kode as kode_mapel, c.id_kjm, c.jadwal_materi');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->join('kelas_jadwal_materi c', 'a.id_materi=c.id_materi');
        $this->db->from('kelas_materi a');
        $this->db->where('a.id_guru', $id_guru);
    }
    public function getKelasSiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->from('kelas_siswa a');
        return $this->db->get()->result();
        $this->db->order_by('b.nama', 'ASC');
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, b.jenis_kelamin, c.nama_kelas, c.level_id');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getKelasSiswaDuaSmt($id_kelas, $id_tp, $id_smt)
    {
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.nama_kelas, c.level_id');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        return $this->db->get()->result();
    }
    public function getStatusMateriSiswaByJadwal($id_siswa, $arr_id_kjm)
    {
        foreach ($result as $key => $row) {
            $ret[$row->id_materi] = $row;
        }
        $result = $this->db->get()->result();
        $this->db->from('log_materi');
        $ret = [];
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where_in('id_materi', $arr_id_kjm);
        return $ret;
        if (!$result) {
        }
        $this->db->select('*');
    }
    public function getStatusMateriSiswa($id_kjm = null)
    {
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa] = $row;
        }
        $this->db->select('a.*, b.jadwal_materi');
        if (!$result) {
        }
        $this->db->join('kelas_jadwal_materi b', 'b.id_kjm=a.id_materi');
        if (!($id_kjm != null)) {
        }
        $this->db->from('log_materi a');
        $this->db->where('a.id_materi', $id_kjm);
        return $ret;
        $result = $this->db->get()->result();
    }
    public function getNilaiMateriSiswa($id_siswa)
    {
        $this->db->join('kelas_jadwal_materi b', 'a.id_materi=b.id_kjm');
        foreach ($result as $key => $row) {
            $ret[$row->jenis][] = $row;
        }
        $this->db->select('a.nilai, a.catatan, b.jadwal_materi, c.kode_materi, c.judul_materi, c.jenis, d.nama_mapel, d.kode');
        $this->db->join('master_mapel d', 'c.id_mapel=d.id_mapel');
        $this->db->join('kelas_materi c', 'b.id_materi=c.id_materi');
        if (!$result) {
        }
        $this->db->where('a.id_siswa', $id_siswa);
        $ret = [];
        return $ret;
        $result = $this->db->get()->result();
        $this->db->from('log_materi a');
    }
    public function getStatusSiswaByMapel($table, $id_mapel)
    {
        $this->db->where('id_mapel', $id_mapel);
        $this->db->select('*');
        return $this->db->get()->result();
        $this->db->from($table);
    }
    public function getLogFileSiswa($table, $id_log)
    {
        $query = $this->db->get();
        $this->db->where('id_log', $id_log);
        $this->db->from($table);
        $this->db->select('*');
        return $query->row();
    }
    public function getLoginSiswa($username)
    {
        $this->db->from('users a');
        if ($query->num_rows() > 0) {
        }
        $this->db->join('log b', 'a.id=b.id_user', 'left');
        $this->db->select('a.id, b.*');
        $this->db->order_by('b.log_time', 'DESC');
        $query = $this->db->get();
        return $query->row()->log_time;
        return null;
        $this->db->where('a.username', $username);
    }
    public function loadJadwalSiswaHariIni($id_tp, $id_smt, $id_kelas, $id_hari, $with_key = true)
    {
        foreach ($result as $key => $row) {
            $ret[$row->jam_ke] = $row;
        }
        $this->db->where('a.id_kelas', $id_kelas);
        $ret = [];
        if ($with_key) {
        }
        $this->db->where('a.id_hari', $id_hari);
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->select('*');
        $this->db->where('a.id_tp', $id_tp);
        return $ret;
        return $result;
        $result = $this->db->get()->result();
        if (!$result) {
        }
        $this->db->from('kelas_jadwal_mapel a');
        $this->db->where('a.id_smt', $id_smt);
    }
    public function loadJadwalSiswaSeminggu($id_tp, $id_smt, $id_kelas)
    {
        $ret = [];
        $result = $this->db->get()->result();
        $this->db->where('a.id_smt', $id_smt);
        $this->db->join('master_mapel b', 'b.id_mapel=a.id_mapel', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->select('*');
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_hari][$row->jam_ke] = $row;
        }
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->from('kelas_jadwal_mapel a');
        if (!$result) {
        }
    }
    public function getMateriSiswa($id_kelas, $tgl, $jenis)
    {
        $this->db->from('kelas_jadwal_materi a');
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_kelas', $id_kelas);
        $result = $this->db->get()->result();
        return $ret;
        if (!$result) {
        }
        $ret = [];
        foreach ($result as $key => $row) {
            $subs_jam = $len_kls + 10;
            $ret[$jam] = $row;
            $len = $sisa === 3 ? 2 : 1;
            $len_kls = strlen($row->id_kelas);
            $jam = substr($row->id_kjm, strlen($row->id_kjm) - $len, 1);
            $sisa = strlen($row->id_kjm) - $subs_jam;
        }
        $this->db->where('a.jadwal_materi', $tgl);
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.judul_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.nama_mapel');
        $this->db->where('a.jenis', $jenis);
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
    }
    public function getMateriSiswaSeminggu($id_tp, $id_smt, $id_kelas, $jenis)
    {
        $this->db->where('a.id_kelas', $id_kelas);
        $result = $this->db->get()->result();
        $this->db->where('a.jenis', $jenis);
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.judul_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.nama_mapel');
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $this->db->where('a.id_smt', $id_smt);
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $subs_jam = $len_kls + 10;
            $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
            $sisa = strlen($row->id_kjm) - $subs_jam;
            $len = $sisa === 3 ? 2 : 1;
            $len_kls = strlen($row->id_kelas);
            $ret[$row->jadwal_materi][$jam] = $row;
        }
        $ret = [];
        $this->db->where('a.id_tp', $id_tp);
        return $ret;
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->from('kelas_jadwal_materi a');
    }
    public function getAllMateriByTgl($id_kelas, $tgl, $arr_mapel)
    {
        $this->db->select('a.*, b.id_materi, b.kode_materi, b.materi_kelas, b.tgl_mulai, c.nama_guru, d.kode, d.nama_mapel');
        $this->db->where('a.jadwal_materi', $tgl);
        return $ret;
        $this->db->join('master_guru c', 'b.id_guru=c.id_guru', 'left');
        if (!(count($arr_mapel) > 0)) {
        }
        $this->db->where_in('a.id_mapel', $arr_mapel);
        $this->db->join('master_mapel d', 'b.id_mapel=d.id_mapel', 'left');
        $result = $this->db->get()->result();
        foreach ($result as $key => $row) {
            $len_kls = strlen($row->id_kelas);
            $subs_jam = $len_kls + 10;
            $len = $sisa === 3 ? 2 : 1;
            $ret[$row->id_mapel][$jam][$row->jenis] = $row;
            $sisa = strlen($row->id_kjm) - $subs_jam;
            $row->materi_kelas = unserialize($row->materi_kelas ?? '');
            $jam = substr($row->id_kjm, strlen($row->id_kjm) - $sisa, $len);
        }
        $ret = [];
        $this->db->join('kelas_materi b', 'a.id_materi=b.id_materi AND b.status=1');
        $this->db->where('a.id_kelas', $id_kelas);
        if (!$result) {
        }
        $this->db->from('kelas_jadwal_materi a');
    }
    public function getRekapStatusMapel($id_siswa, $date, $id_mapel)
    {
        $this->db->join('kelas_materi c', 'b.id_materi=c.id_materi', 'left');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->select('a.jam_ke, a.log_time, c.jenis, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam, d.nama_mapel, d.kode, d.id_mapel');
        $this->db->join('master_mapel d', 'c.id_mapel=d.id_mapel', 'left');
        return $this->db->get()->result();
        $this->db->join('kelas_jadwal_materi b', 'a.id_materi=b.id_kjm', 'left');
        $this->db->where('DATE(a.log_time)', $date);
        $this->db->from('log_materi a');
        $this->db->where('a.id_siswa', $id_siswa);
    }
    public function getRekapStatusMateri($id_siswa, $arr_id_kjm)
    {
        $this->db->from('log_materi a');
        $this->db->join('master_mapel d', 'c.id_mapel=d.id_mapel', 'left');
        return $this->db->get()->result();
        $this->db->join('kelas_jadwal_materi b', 'a.id_materi=b.id_kjm', 'left');
        $this->db->select('a.jam_ke, a.log_time, a.finish_time, c.jenis, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam, d.nama_mapel, d.kode, d.id_mapel');
        $this->db->where_in('a.id_materi', $arr_id_kjm);
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->join('kelas_materi c', 'b.id_materi=c.id_materi', 'left');
    }
    public function getRekapBulananMapel($id_siswa, $bulan)
    {
        $this->db->from('log_materi a');
        $this->db->select('a.log_time as materi, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam_materi');
        $this->db->where('MONTH(a.log_time)', $bulan);
        return $this->db->get()->result();
        $this->db->where('a.id_siswa', $id_siswa);
    }
    public function getRekapBulananSiswa($id_mapel, $id_kelas, $tahun, $bulan)
    {
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('MONTH(a.jadwal_materi)', $bulan)->where('YEAR(a.jadwal_materi)', $tahun);
        if (!($id_mapel != null)) {
        }
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->join('log_materi b', 'b.id_materi=a.id_kjm');
        if (!$result) {
        }
        $this->db->select('a.*, b.log_time, b.finish_time, b.id_siswa, b.jam_ke, DAYOFMONTH(b.log_time) as tanggal, MONTH(b.log_time) as bulan, YEAR(b.log_time) as tahun, TIME_FORMAT(b.log_time, "%H:%i") as jam');
        return $ret;
        $ret = [];
        $result = $this->db->get()->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa][$row->jenis][$row->jadwal_materi][$row->jam_ke] = $row;
        }
        $this->db->from('kelas_jadwal_materi a');
    }
    public function getRekapBulananMateri($id_siswa, $date, $id_materi)
    {
        $ret = [];
        $this->db->where('a.id_materi', $id_materi);
        $this->db->where('a.id_siswa', $id_siswa);
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa] = $row;
        }
        $this->db->select('a.log_time, DAYOFMONTH(a.log_time) as tanggal, MONTH(a.log_time) as bulan, YEAR(a.log_time) as tahun, TIME_FORMAT(a.log_time, "%H:%i") as jam');
        $this->db->from('log_materi a');
        return $ret;
        $this->db->where('DATE(a.log_time)', $date);
        $result = $this->db->get()->row();
    }
    public function getRekapMateriSemester($id_kelas, $id_materi = null)
    {
        $this->db->select('id_siswa, id_log, log_time, finish_time, id_materi,' . ' DAYOFMONTH(log_time) as tanggal,' . ' MONTH(log_time) as bulan,' . ' YEAR(log_time) as tahun,' . ' TIME_FORMAT(log_time, "%H:%i") as jam,' . ' nilai');
        $this->db->from('log_materi');
        foreach ($result as $key => $row) {
            $jenis = substr($row->id_materi, strlen($row->id_materi) - 1, 1);
            $sisa = strlen($row->id_materi) - ($len_kls + 10);
            $subs_bln = $len_kls + $len_tp_smt + $len_tahun;
            $len = $sisa === 3 ? 2 : 1;
            $len_tp_smt = 2;
            $tgl = substr($row->id_materi, $subs_tgl, 2);
            $len_bln = 2;
            $bulan = substr($row->id_materi, $subs_bln, 2);
            $len_tahun = 4;
            $ret[$jenis][$row->id_siswa][$bulan][$tgl][$jam] = $row;
            $jam = substr($row->id_materi, strlen($row->id_materi) - $sisa, $len);
            $len_kls = strlen($id_kelas);
            $subs_tgl = $subs_bln + $len_bln;
            $len_hari = 2;
        }
        $ret = [];
        if (!$result) {
        }
        return $ret;
        $this->db->where('id_materi', $id_materi);
        if (!($id_materi != null)) {
        }
        $result = $this->db->get()->result();
    }
    public function getStrukturKelas($kelas)
    {
        $this->db->where('id_kelas', $kelas);
        return $this->db->get('kelas_struktur')->row();
    }
    public function getCatatanKelas($kelas, $id_tp, $id_smt)
    {
        $this->db->where('type', '1');
        return $this->db->get('kelas_catatan_wali')->result();
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_kelas', $kelas);
        $this->db->where('id_smt', $id_smt);
    }
    public function getCatatanSiswa($id_tp, $id_smt, $id_kelas)
    {
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.foto,' . ' (SELECT COUNT(id_siswa) FROM kelas_catatan_wali c WHERE c.id_siswa = b.id_siswa AND c.type = \'2\') AS jml_catatan');
        $this->db->where('a.id_tp', $id_tp);
        return $this->db->get()->result();
    }
    public function getAllCatatanSiswa($id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        return $this->db->get()->result();
        $this->db->where('id_siswa', $id_siswa);
        $this->db->from('kelas_catatan_wali');
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_tp', $id_tp);
    }
    public function getCatatanMapelKelas($kelas, $mapel, $id_tp, $id_smt)
    {
        $this->db->order_by('tgl', 'DESC');
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_mapel', $mapel);
        $this->db->where('type', '1');
        $this->db->where('id_kelas', $kelas);
        return $this->db->get('kelas_catatan_mapel')->result();
        $this->db->where('id_tp', $id_tp);
    }
    public function getCatatanMapelSiswa($id_tp, $id_smt, $id_kelas, $id_mapel)
    {
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.foto,' . ' (SELECT COUNT(id_siswa) FROM kelas_catatan_mapel c WHERE c.id_siswa = b.id_siswa AND c.id_mapel = ' . $id_mapel . ' AND c.type = \'2\') AS jml_catatan');
        $this->db->from('kelas_siswa a');
        return $this->db->get()->result();
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
    }
    public function getAllCatatanMapelSiswa($id_siswa, $id_mapel, $id_tp, $id_smt)
    {
        return $this->db->get()->result();
        $this->db->select('*');
        $this->db->where('id_smt', $id_smt);
        $this->db->order_by('tgl', 'DESC');
        $this->db->where('id_mapel', $id_mapel);
        $this->db->from('kelas_catatan_mapel');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_siswa', $id_siswa);
    }
    public function getCatatanMapelBySiswa($id_kelas, $id_tp, $id_smt)
    {
        return $this->db->get()->result();
        $this->db->select('a.*, b.nama_guru, b.nip, b.foto');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->from('kelas_catatan_mapel a');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru');
    }
    public function getCatatanSiswaBySiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('jabatan_guru b', 'b.id_kelas=a.id_kelas');
        $this->db->from('kelas_catatan_wali a');
        $this->db->join('master_guru c', 'c.id_guru=b.id_guru');
        return $this->db->get()->result();
        $this->db->select('a.*, c.nama_guru, c.nip, c.foto');
    }
    public function getCatatanMapelSiswaDetail($id_catatan)
    {
        $this->db->where('a.id_catatan', $id_catatan);
        $this->db->join('level_guru d', 'd.id_level=c.id_jabatan');
        $this->db->select('a.*, b.nama_guru, b.nip, b.foto, d.level as jabatan, e.nama_mapel, e.kode');
        $this->db->join('jabatan_guru c', 'c.id_guru=a.id_guru');
        $this->db->from('kelas_catatan_mapel a');
        return $this->db->get()->row();
        $this->db->join('master_mapel e', 'e.id_mapel=a.id_mapel');
        $this->db->join('master_guru b', 'b.id_guru=a.id_guru');
    }
    public function getCatatanKelasSiswaDetail($id_catatan)
    {
        $this->db->from('kelas_catatan_wali a');
        $this->db->join('level_guru e', 'e.id_level=b.id_jabatan');
        $this->db->where('a.id_catatan', $id_catatan);
        $this->db->join('jabatan_guru b', 'b.id_kelas=a.id_kelas');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        $this->db->join('master_guru c', 'c.id_guru=b.id_guru');
        return $this->db->get()->row();
        $this->db->select('a.*, c.nama_guru, c.nip, c.foto, e.level as jabatan, f.nama_kelas');
    }
    public function getReading($table, $id_catatan)
    {
        $this->db->where('id_catatan', $id_catatan);
        $this->db->select('reading, type, readed');
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
        if ($this->agent->is_browser()) {
        }
        $this->insertLog($user_id, $group->id, $group->name, $type, $desc, $agent, $os, $ip);
        $group = $this->ion_auth->get_users_groups($user_id)->row();
        $agent = $this->agent->mobile();
        $agent = 'Data user gagal di dapatkan';
        $user_id = $this->ion_auth->user()->row()->id;
        $agent = $this->agent->browser() . ' ' . $this->agent->version();
        $ip = $this->input->ip_address();
        if ($this->agent->is_mobile()) {
        }
        $os = $this->agent->platform();
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
        if (!($limit != null)) {
        }
        $this->db->select('a.*, b.first_name, b.last_name, d.name');
        $this->db->join('users b', 'b.id=a.id_user', 'left');
        $this->db->limit($limit, 0);
        $this->db->join('groups d', 'd.id=a.id_group');
        return $this->db->get()->result();
        $this->db->from('log a');
        $this->db->order_by('a.log_time', 'DESC');
    }
    public function loadAktifitasSiswa($limit = null)
    {
        $this->db->join('groups d', 'd.id=a.id_group');
        $this->db->where('a.id_group', '3');
        $this->db->select('a.*, b.first_name, b.last_name, d.name');
        $this->db->order_by('a.log_time', 'DESC');
        return $this->db->get()->result();
        $this->db->join('users b', 'b.id=a.id_user', 'left');
        $this->db->limit($limit, 0);
        if (!($limit != null)) {
        }
        $this->db->from('log a');
        $this->db->query('SET SQL_BIG_SELECTS=1');
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
        }
        return $insert;
        $insert = $this->db->insert_batch($table, $data);
        $insert = $this->db->insert($table, $data);
    }
    public function update($table, $data, $pk, $id = null, $batch = false)
    {
        $insert = $this->db->update($table, $data, array($pk => $id));
        $insert = $this->db->update_batch($table, $data, $pk);
        return $insert;
        if ($batch === false) {
        }
    }
    public function delete($table, $data, $pk)
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $deleted = $this->db->delete($table);
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        return $deleted;
        $this->db->where_in($pk, $data);
    }
    public function delete_not($table, $data, $pk, $col, $not)
    {
        $this->db->where($col . '!=' . $not);
        $this->db->where_in($pk, $data);
        return $this->db->delete($table);
    }
    public function getDataKelas()
    {
        $this->datatables->select('id_kelas, nama_kelas, id_jurusan, nama_jurusan');
        $this->datatables->from('master_kelas');
        return $this->datatables->generate();
        $this->datatables->add_column('bulk_select', '<div class="text-center"><input type="checkbox" class="check" name="checked[]" value="$1"/></div>', 'id_kelas, nama_kelas, id_jurusan, nama_jurusan');
        $this->datatables->join('master_jurusan', 'jurusan_id=id_jurusan');
    }
    public function getKelasById($id)
    {
        $this->db->where('id_kelas', $id);
        $this->db->from('master_kelas');
        $this->db->order_by('nama_kelas');
        return $this->db->get()->row();
        $this->db->select('id_kelas, nama_kelas, level_id');
    }
    public function getDataJurusan()
    {
        return $this->db->get()->result();
        $this->db->from('master_jurusan');
        $this->db->select('*');
    }
    public function getDataJurusanMapel($arrIds)
    {
        if (!$result) {
        }
        $this->db->from('master_mapel');
        $result = $this->db->get()->result();
        return $ret;
        $ret = [];
        $this->db->select('id_mapel, nama_mapel');
        $this->db->where_in('id_mapel', $arrIds);
        foreach ($result as $key => $row) {
            $ret[$row->id_mapel] = $row->nama_mapel;
        }
    }
    public function getDataTableJurusan()
    {
        $this->datatables->from('master_jurusan');
        return $this->datatables->generate();
        $this->datatables->select('*');
        $this->db->order_by('id_jurusan');
    }
    public function getJurusanById($id)
    {
        return $this->db->get('master_jurusan')->result();
        $this->db->order_by('nama_jurusan');
        $this->db->where_in('id_jurusan', $id);
    }
    function updateJurusan()
    {
        $this->db->set('nama_jurusan', $name);
        $this->db->set('status', '1');
        if (!($i <= $row_mapels)) {
        }
        $this->db->set('kode_jurusan', $kode);
        $this->db->where('id_jurusan', $id);
        return $this->db->update('master_jurusan');
        $row_mapels = count($this->input->post('mapel', true));
        $kode = $this->input->post('kode_jurusan', true);
        $i = 0;
        $mapels = [];
        $i++;
        if (!$check_mapel) {
        }
        $check_mapel = $this->input->post('mapel', true);
        $id = $this->input->post('id_jurusan');
        array_push($mapels, $this->input->post('mapel[' . $i . ']', true));
        $this->db->set('mapel_peminatan', implode(',', $mapels));
        $name = $this->input->post('nama_jurusan', true);
    }
    public function inputJurusan()
    {
        $data = ['nama_jurusan' => $this->input->post('nama_jurusan', true), 'kode_jurusan' => $this->input->post('kode_jurusan', true)];
        return $this->db->insert('master_jurusan', $data);
    }
    public function getAllDataSiswa($id_tp, $id_smt)
    {
        return $query->result();
        $this->db->from('master_siswa a');
        $query = $this->db->get();
        $this->db->order_by('a.nama');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('master_kelas c', 'c.id_kelas=b.id_kelas', 'left');
        $this->db->join('kelas_siswa b', 'b.id_siswa=a.id_siswa AND b.id_tp=' . $id_tp . ' AND b.id_smt=' . $id_smt . '', 'left');
        $this->db->select('a.*, c.nama_kelas');
        $this->db->order_by('b.id_kelas');
    }
    public function getSiswaByKelas($id_tp, $id_smt, $id_kelas)
    {
        $this->db->where('a.id_tp', $id_tp);
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('b.*');
        $this->db->where('a.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        return $this->db->get()->result();
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('b.id_siswa is NOT NULL', NULL, FALSE);
        $this->db->order_by('b.nama', 'ASC');
    }
    public function getDataSiswa($id_tp, $id_smt)
    {
        return $this->datatables->generate();
        $this->datatables->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('a.id_siswa, a.foto, a.nama, a.nis, a.nisn, a.jenis_kelamin, f.level_id, f.nama_kelas, b.status');
        $this->db->order_by('f.nama_kelas', 'ASC');
        $this->datatables->join('users c', 'a.username=c.username');
        $this->db->order_by('b.status', 'ASC');
        $this->datatables->join('buku_induk b', 'a.id_siswa=b.id_siswa', 'left');
        $this->datatables->from('master_siswa a');
        $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
        $this->datatables->join('kelas_siswa d', 'd.id_siswa = a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
    }
    public function getAllSiswa($id_tp, $id_smt, $offset, $limit, $search = null, $sort = null, $order = null)
    {
        $this->db->or_like('a.nisn', $search);
        $this->db->from('master_siswa a');
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        return $this->db->get()->result();
        $this->db->select('a.id_siswa, a.foto, a.nama, a.nis, a.nisn, a.jenis_kelamin, f.level_id, f.nama_kelas,' . ' (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS status');
        $this->db->or_like('a.nis', $search);
        $this->db->join('kelas_siswa d', 'd.id_siswa = a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        if (!($search != null)) {
        }
        $this->db->order_by('a.nama', 'ASC');
        $this->db->like('a.nama', $search);
        $this->db->limit($limit, $offset);
    }
    public function getSiswaPage($id_tp, $id_smt, $offset, $limit, $filter, $search = null, $sort = null, $order = null)
    {
        $this->db->from('master_siswa a');
        if (!($filter == '1')) {
        }
        $this->db->limit($limit, $offset);
        return $this->db->get()->result();
        $this->db->where('f.id_kelas IS NULL');
        $this->db->or_like('a.nisn', $search);
        $this->db->order_by('a.nama', 'ASC');
        if (!($search != null)) {
        }
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->or_like('a.nis', $search);
        $this->db->like('a.nama', $search);
        $this->db->select('a.id_siswa, a.foto, a.nama, a.nis, a.nisn, a.jenis_kelamin, d.id_kelas, ' . 'f.nama_kelas, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif');
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->where('f.id_kelas IS NOT NULL');
        $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = ' . $filter);
        $this->db->order_by('f.nama_kelas', 'ASC');
        if ($filter == '5') {
        }
        $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
        $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = "1"');
        $this->db->join('kelas_siswa d', 'd.id_siswa=a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
    }
    public function getSiswaTotalPage($id_tp, $id_smt, $filter, $search = null)
    {
        $this->db->select('a.id_siswa');
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->where('f.id_kelas IS NOT NULL');
        $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = ' . $filter);
        if ($filter == '5') {
        }
        $this->db->or_like('a.nis', $search);
        $this->db->like('a.nama', $search);
        $this->db->where('f.id_kelas IS NULL');
        $this->db->join('buku_induk u', 'u.id_siswa=a.id_siswa AND u.status = "1"');
        $this->db->or_like('a.nisn', $search);
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        if (!($filter == '1')) {
        }
        $this->db->from('master_siswa a');
        $this->db->join('kelas_siswa d', 'd.id_siswa=a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        return $this->db->get()->num_rows();
        if (!($search != null)) {
        }
    }
    public function getDataSiswaByKelas($id_tp, $id_smt, $id_kelas, $offset, $limit, $search = null, $sort = null, $order = null)
    {
        return $this->db->get()->result();
        $this->db->like('b.nama', $search);
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        if (!($search != null)) {
        }
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->or_like('b.nis', $search);
        $this->db->limit($limit, $offset);
        $this->db->select('b.id_siswa, b.nama, b.nis, b.nisn, b.jenis_kelamin, b.username, b.password, b.foto,' . ' f.nama_kelas, (SELECT COUNT(id) FROM users WHERE users.username = b.username) AS aktif');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->from('kelas_siswa a');
        $this->db->or_like('b.nisn', $search);
        $this->db->where('a.id_tp', $id_tp);
        if (!($limit > 0)) {
        }
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'right');
    }
    public function getDataSiswaByKelasPage($id_tp, $id_smt, $id_kelas, $search = null)
    {
        $this->db->where('a.id_tp', $id_tp);
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_kelas', $id_kelas);
        return $this->db->get()->num_rows();
        $this->db->or_like('b.nis', $search);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->like('b.nama', $search);
        if (!($search != null)) {
        }
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->select('a.id_siswa');
        $this->db->or_like('b.nisn', $search);
    }
    public function getSiswaById($id)
    {
        return $this->db->get()->row();
        $this->db->join('buku_induk b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->where('a.id_siswa', $id);
        $this->db->from('master_siswa a');
        $this->db->select('a.*, b.status');
    }
    public function getSiswaByArrNisn($arr_nisn, $arr_nis, $arr_username)
    {
        $this->db->or_where_in('username', $arr_username);
        $this->db->from('master_siswa');
        $this->db->where_in('nisn', $arr_nisn);
        $this->db->or_where_in('nis', $arr_nis);
        return $this->db->get()->result();
        $this->db->select('id_siswa, nama, nisn, nis, username');
    }
    public function getSiswaKelasBaru($id_tp, $id_smt)
    {
        if (!$result) {
        }
        $ret = [];
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas');
        $this->db->where('a.id_smt', $id_smt);
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa] = $row;
        }
        $this->db->select('b.id_siswa, b.nama, f.id_kelas, f.nama_kelas, f.kode_kelas');
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa');
        $this->db->from('kelas_siswa a');
        return $ret;
        $result = $this->db->get()->result();
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getDataSiswaById($id_tp, $id_smt, $idSiswa)
    {
        $this->db->join('cbt_ruang i', 'i.id_ruang=h.id_ruang', 'left');
        $this->db->where('a.id_siswa', $idSiswa);
        $this->db->select('b.id_siswa, b.nama, b.jenis_kelamin, b.nis, b.nisn, b.username, b.password,' . ' b.foto, c.sesi_id, d.kode_ruang, e.kode_sesi, f.nama_kelas, g.nomor_peserta,' . ' h.set_siswa, i.kode_ruang as ruang_kelas, j.kode_sesi as sesi_kelas');
        $this->db->join('cbt_ruang d', 'd.id_ruang=c.ruang_id', 'left');
        $this->db->join('cbt_sesi_siswa c', 'c.siswa_id=a.id_siswa', 'left');
        $this->db->join('master_kelas f', 'f.id_kelas=a.id_kelas', 'left');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('cbt_sesi j', 'j.id_sesi=h.id_sesi', 'left');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->join('cbt_kelas_ruang h', 'h.id_kelas=a.id_kelas', 'left');
        return $this->db->get()->row();
        $this->db->join('master_siswa b', 'b.id_siswa=a.id_siswa', 'left');
        $this->db->join('cbt_sesi e', 'e.id_sesi=c.sesi_id', 'left');
        $this->db->join('cbt_nomor_peserta g', 'g.id_siswa=a.id_siswa AND g.id_tp=' . $id_tp, 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }
    public function getAgamaSiswa()
    {
        foreach ($result as $row) {
            $ret[$row->agama] = $row->agama;
        }
        $this->db->not_like('a.agama', 'Pilih');
        $result = $this->db->get()->result();
        $this->db->where('a.agama != "0"', NULL, FALSE);
        $this->db->where('a.agama is NOT NULL', NULL, FALSE);
        $this->db->distinct();
        $this->db->select('agama');
        $ret['-'] = 'Bukan Mapel Agama';
        return $ret;
        $this->db->from('master_siswa a');
    }
    public function getJurusan()
    {
        $this->db->join('master_jurusan', 'jurusan_id=id_jurusan');
        $this->db->group_by('id_jurusan');
        return $query->result();
        $this->db->order_by('nama_jurusan', 'ASC');
        $this->db->from('master_kelas');
        $this->db->select('id_jurusan, nama_jurusan');
        $query = $this->db->get();
    }
    public function getAllJurusan($id = null)
    {
        $this->db->from('jurusan_mapel');
        $this->db->from('master_jurusan');
        $this->db->order_by('nama_jurusan', 'ASC');
        $jurusan = $this->db->get()->result();
        if ($id === null) {
        }
        return $this->db->get()->result();
        $id_jurusan = [];
        $this->db->where_not_in('id_jurusan', $id_jurusan);
        foreach ($jurusan as $j) {
            $id_jurusan[] = $j->jurusan_id;
        }
        $id_jurusan = null;
        $this->db->where('mapel_id', $id);
        $this->db->select('jurusan_id');
        return $this->db->get('jurusan')->result();
        if (!($id_jurusan === [])) {
        }
        $this->db->select('*');
    }
    public function getKelasByJurusan($id)
    {
        $query = $this->db->get_where('master_kelas', array('jurusan_id' => $id));
        return $query->result();
    }
    public function getDataGuru($tp, $smt)
    {
        $this->datatables->join('master_smt f', 'b.id_smt=f.id_smt', 'left');
        $this->datatables->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        return $this->datatables->generate();
        $this->datatables->from('master_guru a');
        $this->datatables->join('master_tp e', 'b.id_tp=e.id_tp', 'left');
        $this->datatables->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('a.id_guru, a.nama_guru, a.nip, a.kode_guru, a.jenis_kelamin, a.foto, b.id_jabatan, b.id_kelas, b.mapel_kelas, c.id_level, c.level, d.nama_kelas, e.tahun, f.nama_smt');
        $this->datatables->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
    }
    public function getAllDataGuru($tp, $smt)
    {
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->from('master_guru a');
        $this->db->join('master_tp e', 'b.id_tp=e.id_tp', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->order_by('c.id_level', 'desc');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        return $this->db->get()->result();
        $this->db->select('a.id_guru, a.nama_guru, a.nip, a.kode_guru, a.jenis_kelamin, a.foto, b.id_jabatan, b.id_kelas, b.mapel_kelas, b.ekstra_kelas, c.id_level, c.level, d.nama_kelas, e.tahun, f.nama_smt, (SELECT COUNT(id) FROM users e WHERE e.username = a.username) AS status');
        $this->db->join('master_smt f', 'b.id_smt=f.id_smt', 'left');
        $this->db->order_by('a.id_guru', 'asc');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
    }
    public function getGuruById($id, $id_tp = null, $id_smt = null)
    {
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'a.id_guru=d.guru_id AND d.id_tp=' . $id_tp . ' AND d.id_smt=' . $id_smt, 'left');
        return $this->db->get()->row();
        if (!($id_tp != null && $id_smt != null)) {
        }
        $this->db->select('*');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->from('master_guru a');
        $this->db->where('a.id_guru', $id);
    }
    public function getGuruByArrId($arr_id)
    {
        if (!(count($arr_id) > 0)) {
        }
        $this->db->where_in('id_guru', $arr_id);
        $this->db->from('master_guru');
        return $this->db->get()->result();
        $this->db->select('nama_guru, nip');
    }
    public function getUserIdGuruByUsername($username)
    {
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('*');
        $this->db->from('master_guru a');
        return $this->db->get()->row();
        $this->db->where('a.username', $username);
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
    }
    public function getDetailJabatanGuru($id_guru)
    {
        $this->db->select('a.id_guru, a.nama_guru, b.id_tp, b.id_smt, b.mapel_kelas, b.ekstra_kelas, c.id_level, c.level, d.id_kelas, d.nama_kelas');
        $ret = [];
        $this->db->where('a.id_guru', $id_guru);
        $this->db->from('master_guru a');
        return $ret;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas', 'left');
        foreach ($result as $row) {
            $ret[$row->id_tp][$row->id_smt] = $row;
        }
        $result = $this->db->get()->result();
    }
    public function getJabatanGuru($id_guru, $tp, $smt)
    {
        $this->db->select('a.id_guru, a.nama_guru, b.mapel_kelas, b.ekstra_kelas, c.id_level, c.level, d.id_kelas, d.nama_kelas');
        $this->db->join('master_kelas d', 'b.id_kelas=d.id_kelas AND d.id_tp=' . $tp . ' AND d.id_smt=' . $smt . '', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->where('a.id_guru', $id_guru);
        $this->db->from('master_guru a');
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        return $this->db->get()->row();
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
    }
    public function getGuruMapel($tp, $smt)
    {
        $this->db->select('a.mapel_kelas, a.ekstra_kelas, a.id_jabatan, a.id_kelas, b.id_guru, b.nama_guru');
        return $this->db->get()->result();
        $this->db->from('jabatan_guru a');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru');
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
    }
    public function getKodeKelompokMapel()
    {
        $ret = [];
        $result = $this->db->get()->result();
        $this->db->select('*');
        foreach ($result as $row) {
            $ret[$row->kode_kel_mapel] = $row;
        }
        $this->db->from('master_kelompok_mapel');
        return $ret;
        $this->db->order_by('kode_kel_mapel');
    }
    public function getDataKelompokMapel()
    {
        $this->db->where('id_parent', '0');
        $this->db->order_by('kode_kel_mapel');
        $result = $this->db->get()->result();
        foreach ($result as $row) {
            $ret[$row->id_kel_mapel] = $row;
        }
        $this->db->select('*');
        return $ret;
        $this->db->from('master_kelompok_mapel');
        $ret = [];
    }
    public function getKategoriKelompokMapel()
    {
        $this->db->from('master_kelompok_mapel');
        $this->db->where('kategori', 'WAJIB')->or_where('kategori', 'PAI (Kemenag)');
        $this->db->select('kode_kel_mapel, kategori');
        return $this->db->get()->result();
    }
    public function getDataSubKelompokMapel()
    {
        $this->db->where('id_parent <> 0');
        $this->db->select('*');
        $result = $this->db->get()->result();
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_kel_mapel] = $row;
        }
        $this->db->order_by('kode_kel_mapel');
        return $ret;
        $this->db->from('master_kelompok_mapel');
    }
    public function getDataMapel()
    {
        $this->datatables->select('id_mapel, nama_mapel, kode');
        $this->datatables->from('master_mapel');
        return $this->datatables->generate();
    }
    public function getAllMapel($arrKelompok = null, $arrMapel = null)
    {
        $this->db->where_in('kelompok', $arrKelompok);
        $this->db->where('status', '1');
        if (!($arrMapel != null)) {
        }
        $this->db->order_by('urutan_tampil');
        if (!($arrMapel != null)) {
        }
        return $this->db->get('master_mapel')->result();
        $this->db->or_where_in('id_mapel', explode(',', $arrMapel));
    }
    public function getAllStatusMapel($arrKelompok = null, $arrMapel = null)
    {
        if (!($arrMapel != null)) {
        }
        return $this->db->get('master_mapel')->result();
        if (!($arrMapel != null)) {
        }
        $this->db->order_by('urutan_tampil');
        $this->db->where_in('kelompok', $arrKelompok);
        $this->db->or_where_in('id_mapel', explode(',', $arrMapel));
    }
    public function getAllMapelByKelompok($jenjang)
    {
        $ret = [];
        return $ret;
        $this->db->where('status', '1');
        $this->db->order_by('urutan_tampil');
        $result = $this->db->get('master_mapel')->result();
        $this->db->order_by('urutan');
        foreach ($result as $row) {
            $ret[$row->kelompok][] = $row;
        }
    }
    public function getAllMapelNonAktif($jenjang)
    {
        $this->db->where('status', '0');
        return $this->db->get('master_mapel')->result();
    }
    public function getMapelById($id, $single = false)
    {
        $this->db->order_by('nama_mapel');
        return $query;
        $query = $this->db->get_where('master_mapel', array('id_mapel' => $id))->row();
        $this->db->where_in('id_mapel', $id);
        $query = $this->db->get('master_mapel')->result();
        if ($single === false) {
        }
    }
    function updateMapel()
    {
        $kelompok = $this->input->post('kelompok', true);
        $kode = $this->input->post('kode_mapel', true);
        $status = $this->input->post('status', true);
        $name = $this->input->post('nama_mapel', true);
        $this->db->where('id_mapel', $id);
        return $this->db->update('master_mapel');
        $urut = $this->input->post('urutan_tampil', true);
        $this->db->set('status', $status);
        $id = $this->input->post('id_mapel');
        $this->db->set('urutan_tampil', $urut);
        $this->db->set('kode', $kode);
        if (!($kelompok != null)) {
        }
        $this->db->set('kelompok', $kelompok);
        $this->db->set('nama_mapel', $name);
    }
    public function getAllEkstra()
    {
        return $this->db->get('master_ekstra')->result();
    }
    public function getEkstraById($id, $single = false)
    {
        return $query;
        $query = $this->db->get('master_ekstra')->result();
        $query = $this->db->get_where('master_ekstra', array('id_ekstra' => $id))->row();
        if ($single === false) {
        }
        $this->db->where_in('id_ekstra', $id);
        $this->db->order_by('nama_ekstra');
    }
    function updateEkstra()
    {
        return $this->db->update('master_ekstra');
        $id = $this->input->post('id_ekstra');
        $this->db->set('nama_ekstra', $name);
        $this->db->where('id_ekstra', $id);
        $name = $this->input->post('nama_ekstra', true);
        $this->db->set('kode_ekstra', $kode);
        $kode = $this->input->post('kode_ekstra', true);
    }
    public function getKelasGuru()
    {
        return $this->datatables->generate();
        $this->datatables->select('kelas_guru.id, guru.id_guru, guru.nip, guru.nama_guru, GROUP_CONCAT(master_kelas.nama_kelas) as kelas');
        $this->datatables->group_by('guru.nama_guru');
        $this->datatables->join('master_kelas', 'kelas_id=id_kelas');
        $this->datatables->from('kelas_guru');
        $this->datatables->join('master_guru', 'guru_id=id_guru');
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }
    public function getKelasByGuru($id)
    {
        $this->db->where('guru_id', $id);
        $this->db->join('master_kelas', 'kelas_guru.kelas_id=kelas.id_kelas');
        $this->db->select('kelas.id_kelas');
        $this->db->from('kelas_guru');
        return $this->db->get()->result();
    }
    public function getAllJabatanGuru($id)
    {
        return $ret;
        $result = $this->db->get_where('jabatan_guru', 'id_guru=' . $id)->result();
        foreach ($result as $key => $row) {
            $ret[$row->id_tp][$row->id_smt] = $row->id_kelas;
        }
        $ret = [];
        if (!$result) {
        }
    }
    public function getJurusanMapel()
    {
        $this->datatables->from('jurusan_mapel');
        $this->datatables->join('master_jurusan', 'jurusan_id=id_jurusan');
        return $this->datatables->generate();
        $this->datatables->group_by('master_mapel.nama_mapel');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->join('master_mapel', 'mapel_id=id_mapel');
        $this->datatables->select('jurusan_mapel.id, mapel.id_mapel, mapel.nama_mapel, jurusan.id_jurusan, GROUP_CONCAT(jurusan.nama_jurusan) as nama_jurusan');
    }
    public function getMapel($id = null)
    {
        $this->db->select('mapel_id');
        foreach ($mapel as $d) {
            $id_mapel[] = $d->mapel_id;
        }
        $id_mapel = null;
        $this->db->where_not_in('mapel_id', [$id]);
        $this->db->from('jurusan_mapel');
        if (!($id_mapel === [])) {
        }
        if (!($id !== null)) {
        }
        $id_mapel = [];
        $this->db->from('master_mapel');
        $mapel = $this->db->get()->result();
        $this->db->where_not_in('id_mapel', $id_mapel);
        $this->db->select('id_mapel, nama_mapel');
        return $this->db->get()->result();
    }
    public function getJurusanByIdMapel($id)
    {
        return $this->db->get()->result();
        $this->db->join('master_jurusan', 'jurusan_mapel.jurusan_id=jurusan.id_jurusan');
        $this->db->from('jurusan_mapel');
        $this->db->select('jurusan.id_jurusan');
        $this->db->where('mapel_id', $id);
    }
    public function getTahunActive()
    {
        $this->db->select('id_tp, tahun');
        $this->db->from('master_tp');
        return $this->db->get()->row();
        $this->db->where('active', 1);
    }
    public function getSemesterActive()
    {
        $this->db->where('active', 1);
        return $this->db->get()->row();
        $this->db->select('id_smt, nama_smt, smt');
        $this->db->from('master_smt');
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
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->tahun_lulus] = $row->tahun_lulus;
            if (!($row->tahun_lulus != '')) {
            }
        }
        return $ret;
        $this->db->distinct();
        $this->db->select('tahun_lulus');
        $result = $this->db->get('buku_induk')->result();
    }
    public function getDistinctKelasAkhir()
    {
        foreach ($result as $row) {
            $ret[$row->kelas_akhir] = $row->kelas_akhir;
            if (!($row->kelas_akhir != '')) {
            }
        }
        $ret = [];
        $result = $this->db->get('buku_induk')->result();
        $this->db->select('kelas_akhir');
        return $ret;
        $this->db->distinct();
    }
    public function getAlumniByTahun($tahun, $kelas = null)
    {
        return $this->db->get()->result();
        if (!($kelas != null)) {
        }
        $this->db->where('a.kelas_akhir', $kelas);
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->where('a.tahun_lulus', $tahun);
        $this->db->from('buku_induk a');
        $this->db->select('*');
    }
    public function getAlumniById($id)
    {
        $this->db->where('a.id_siswa', $id);
        $this->db->join('buku_induk b', 'a.id_siswa=b.id_siswa');
        $this->db->select('*');
        return $this->db->get()->row();
        $this->db->from('master_siswa a');
    }
    public function getAllWaliKelas()
    {
        $this->db->from('jabatan_guru a');
        $this->db->join('master_kelas d', 'a.id_kelas=d.id_kelas', 'left');
        $this->db->join('master_guru b', 'a.id_guru=b.id_guru', 'left');
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            if (!($row->id_level == '4')) {
            }
            $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
        }
        $ret = [];
        $this->db->select('a.id_tp, a.id_smt, a.id_guru, b.nama_guru, c.id_level, c.level, d.id_kelas, d.nama_kelas');
        return $ret;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('level_guru c', 'a.id_jabatan=c.id_level', 'left');
        $result = $this->db->get()->result();
    }
    public function getAllGuru()
    {
        return $this->db->get()->result();
        $guru = $this->db->get()->result();
        $this->db->select('id_guru');
        $this->db->from('jabatan_guru');
        $this->db->from('master_guru');
        $id_guru = [];
        $this->db->select('id_guru, nip, nama_guru');
        $this->db->where_in('id_guru', $id_guru);
        foreach ($guru as $d) {
            $id_guru[] = $d->id_guru;
        }
    }
    public function getAllKelas($tp = null, $smt = null)
    {
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas] = $row;
        }
        $this->db->select('a.id_kelas, a.id_tp, a.id_smt, a.nama_kelas, a.kode_kelas, a.level_id, b.nama_jurusan, b.kode_jurusan, c.nama_guru');
        if ($tp != null && $smt != null) {
        }
        $this->db->from('master_kelas a');
        if (!$result) {
        }
        $this->db->order_by('a.nama_kelas');
        if (!$result) {
        }
        $this->db->query('SET SQL_BIG_SELECTS=1');
        if (!($tp != null && $smt != null)) {
        }
        $this->db->where('a.id_tp', $tp)->where('a.id_smt', $smt);
        $ret = [];
        $this->db->join('master_guru c', 'f.id_guru=c.id_guru', 'left');
        $this->db->join('master_jurusan b', 'a.jurusan_id=b.id_jurusan', 'left');
        foreach ($result as $key => $row) {
            $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = $row;
        }
        $this->db->join('jabatan_guru f', 'f.id_kelas=a.id_kelas', 'left');
        $result = $this->db->get()->result();
    }
    public function getAllKelasSiswa()
    {
        $this->db->select('*');
        $ret = [];
        return $ret;
        $this->db->from('kelas_siswa');
        if (!$result) {
        }
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas][$row->id_siswa] = $row;
        }
        $result = $this->db->get()->result();
    }
    public function getDataInduk()
    {
        $result = $this->db->get()->result();
        return $ret;
        $ret = [];
        $this->db->join('buku_induk b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->select('a.*, b.*,');
        if (!$result) {
        }
        $this->db->order_by('a.nama', 'ASC');
        $this->db->from('master_siswa a');
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa] = $row;
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
        return $this->db->get()->result();
        $this->db->select('a.*, b.nama_guru, b.foto, (SELECT COUNT(post_comments.id_comment) FROM post_comments WHERE a.id_post = post_comments.id_post) AS jml');
        $this->db->where('a.dari', $id_user);
        $this->db->order_by('a.updated', 'desc');
        if (!($id_user != 0)) {
        }
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->from('post a');
    }
    public function getPostForUser($kepada, $kelas = null)
    {
        $this->db->order_by('a.updated', 'desc');
        $this->db->select('a.*, b.nama_guru, b.foto, (SELECT COUNT(post_comments.id_comment) FROM post_comments WHERE a.id_post = post_comments.id_post) AS jml');
        return $this->db->get()->result();
        if (!($kepada != null)) {
        }
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->where('(a.kepada LIKE ' . $kepada . ') OR (a.kepada LIKE ' . $kelas . ')');
        $this->db->from('post a');
    }
    public function getIdComments($id_post)
    {
        return $this->db->get('post_comments')->result();
        $this->db->where('id_post', $id_post);
        $this->db->select('id_comment');
    }
    public function getIdReplies($id_comment)
    {
        if (is_array($id_comment)) {
        }
        $this->db->where('id_comment', $id_comment);
        $this->db->where_in('id_comment', $id_comment);
        return $this->db->get('post_reply')->result();
        $this->db->select('id_reply');
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
        foreach ($result as $key => $row) {
            $ret[$row->id_mapel] = $row;
        }
        return $ret;
        $result = $this->db->get('rapor_kkm')->result();
        $ret = [];
        if (!$result) {
        }
    }
    public function getRaporSetting($id_tp, $id_smt)
    {
        $this->db->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_admin_setting')->row();
    }
    public function getDetailSiswa($id_kelas, $id_tp, $id_smt)
    {
        $this->db->order_by('b.nama', 'ASC');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->where('a.id_smt', $id_smt);
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.*, c.*');
        $this->db->where('a.id_tp', $id_tp);
        return $this->db->get()->result();
    }
    public function getDetailSiswaById($id_siswa, $id_tp, $id_smt)
    {
        $this->db->where('b.id_smt', $id_smt);
        $this->db->order_by('a.nama', 'ASC');
        $this->db->join('master_kelas c', 'b.id_kelas=c.id_kelas');
        $this->db->join('kelas_siswa b', 'a.id_siswa=b.id_siswa');
        $this->db->select('a.nama, a.nis, a.nisn, c.nama_kelas');
        $this->db->where('a.id_siswa', $id_siswa);
        $this->db->where('b.id_tp', $id_tp);
        $this->db->query('SET SQL_BIG_SELECTS=1');
        return $this->db->get()->row();
        $this->db->from('master_siswa a');
    }
    public function cekNilaiHarianKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->from('rapor_nilai_harian');
        return $this->db->get()->num_rows();
        $this->db->where('id_tp', $id_tp);
        $this->db->select('p_rata_rata');
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('p_rata_rata !=', 'NULL');
        $this->db->where('id_kelas', $id_kelas);
    }
    public function getNilaiHarianKelas($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        return $this->db->get()->row();
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_nilai_harian', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
    }
    public function getAllNilaiHarianKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->from('rapor_nilai_harian');
        $this->db->select('*');
        $this->db->where('id_mapel', $id_mapel);
        $result = $this->db->get()->result();
        $this->db->where('id_tp', $id_tp);
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        $this->db->where('id_smt', $id_smt);
        $ret = [];
        $this->db->where('id_kelas', $id_kelas);
        return $ret;
        if (!$result) {
        }
    }
    public function cekNilaiPtsKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->num_rows();
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_mapel', $id_mapel);
        $this->db->select('predikat');
        $this->db->where('predikat !=', 'NULL');
        $this->db->from('rapor_nilai_pts');
    }
    public function getIdNilaiPts($arr_id)
    {
        $this->db->where_in('id_nilai_pts', $arr_id);
        $this->db->from('rapor_nilai_pts');
        return $ret;
        if (!$result) {
        }
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_nilai_pts] = $row;
        }
        $this->db->select('id_nilai_pts');
        $result = $this->db->get()->result();
    }
    public function getNilaiPtsKelas($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        return $this->db->get()->row();
        $this->db->where('id_nilai_pts', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        $this->db->from('rapor_nilai_pts');
        $this->db->select('*');
    }
    public function getAllNilaiPtsKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_tp', $id_tp);
        $this->db->select('*');
        $this->db->from('rapor_nilai_pts');
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        $result = $this->db->get()->result();
        $this->db->where('id_smt', $id_smt);
        if (!$result) {
        }
        $this->db->where('id_kelas', $id_kelas);
        $ret = [];
        return $ret;
    }
    public function getEkstraKelas($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_ekstra', $id_mapel);
        $this->db->select('nilai, predikat, deskripsi');
        $this->db->from('rapor_nilai_ekstra');
        return $this->db->get()->row();
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_tp', $id_tp);
    }
    public function cekNilaiEkstraKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_ekstra', $id_mapel);
        $this->db->select('id_nilai_ekstra');
        $this->db->from('rapor_nilai_ekstra');
        $this->db->where('id_tp', $id_tp);
        return $this->db->get()->num_rows();
        $this->db->where('id_smt', $id_smt);
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
        $result = $this->db->get()->result();
        $this->db->where('id_tp', $id_tp);
        if (!$result) {
        }
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        $this->db->where('id_smt', $id_smt);
        $ret = [];
        $this->db->where('id_ekstra', $id_ekstra);
        return $ret;
        $this->db->from('rapor_nilai_ekstra');
        $this->db->select('*');
        $this->db->where('id_kelas', $id_kelas);
    }
    public function cekNilaiAkhirKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('id_nilai_akhir');
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_mapel', $id_mapel);
        $this->db->from('rapor_nilai_akhir');
        return $this->db->get()->num_rows();
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
    }
    public function getNilaiAkhirKelas($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->where('a.id_nilai_harian', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->select('a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        return $this->db->get()->row();
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }
    public function getAllNilaiAkhirKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('a.id_tp', $id_tp);
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $this->db->where('a.id_kelas', $id_kelas);
        if (!$result) {
        }
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_smt', $id_smt);
        return $ret;
        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        $result = $this->db->get()->result();
    }
    public function getNilaiAkhirByMapel($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->from('rapor_nilai_harian a');
        $this->db->where('a.id_mapel', $id_mapel);
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
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        $this->db->select('*');
        $this->db->where('jenis', $jenis);
        return $this->db->get()->result();
        $this->db->where('id_kelas', $kelas);
        $this->db->from('rapor_data_sikap');
    }
    public function getNilaiSikapKelas($id_kelas, $id_siswa, $id_tp, $id_smt, $jenis)
    {
        $this->db->where('id_nilai_sikap', $id_kelas . $id_siswa . $id_tp . $id_smt . $jenis);
        return $this->db->get()->row();
        $this->db->from('rapor_nilai_sikap');
        $this->db->select('*');
    }
    public function getAllNilaiSikapKelas($id_kelas)
    {
        $this->db->from('rapor_nilai_sikap');
        return $this->db->get()->result();
        $this->db->select('*');
        $this->db->where('id_kelas', $id_kelas);
    }
    public function getNilaiSikapByJenis($id_kelas, $jenis, $id_tp, $id_smt)
    {
        $this->db->from('rapor_nilai_sikap');
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_tp', $id_tp);
        return $this->db->get()->result();
        $this->db->where('id_kelas', $id_kelas);
        $this->db->select('*');
        $this->db->where('jenis', $jenis);
    }
    public function getNilaiSikapByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->where('id_tp', $id_tp);
        $this->db->from('rapor_nilai_sikap');
        return $this->db->get()->result();
        $this->db->where('id_kelas', $id_kelas);
        $this->db->where('id_smt', $id_smt);
    }
    public function getNilaiSikapBySiswa($id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('*');
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->result();
        $this->db->where('id_siswa', $id_siswa);
        $this->db->from('rapor_nilai_sikap');
    }
    public function getDeskripsiCatatanByJenis($kelas, $jenis, $id_tp, $id_smt)
    {
        $this->db->where('jenis', $jenis)->where('id_kelas', $kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        return $this->db->get('rapor_data_catatan')->result();
    }
    public function getCatatanKelas($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->where('id_catatan_wali', $id_kelas . $id_siswa . $id_tp . $id_smt);
        $this->db->from('rapor_catatan_wali');
        $this->db->select('*');
        return $this->db->get()->row();
    }
    public function getAllCatatanKelas($id_kelas)
    {
        $this->db->select('*');
        $this->db->where('id_kelas', $id_kelas);
        return $this->db->get()->result();
        $this->db->from('rapor_catatan_wali');
    }
    public function getRankingKelas($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        return $this->db->get()->row();
        $this->db->from('rapor_prestasi');
        $this->db->where('id_ranking', $id_kelas . $id_siswa . $id_tp . $id_smt);
        $this->db->select('*');
    }
    public function getAllRankingKelas($id_kelas)
    {
        $this->db->from('rapor_prestasi');
        return $this->db->get()->result();
        $this->db->select('*');
        $this->db->where('id_kelas', $id_kelas);
    }
    public function getAllDeskripsiFisikKelas()
    {
        if (!$result) {
        }
        $ret = [];
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_kelas][$row->id_tp][$row->id_smt] = $row;
        }
        $result = $this->db->get('rapor_data_fisik')->result();
    }
    public function getAllRaporFisik()
    {
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa][$row->id_tp][$row->id_smt] = $row;
        }
        if (!$result) {
        }
        $result = $this->db->get('rapor_fisik')->result();
        $ret = [];
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
        $this->db->where('id_kelas', $id_kelas);
        $this->db->from('rapor_fisik');
        return $this->db->get()->result();
        $this->db->select('*');
    }
    public function getJmlNilaiMapelHarianSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('p_rata_rata, k_rata_rata, jml');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->where('id_smt', $id_smt);
        $this->db->from('rapor_nilai_harian');
        return $this->db->get()->row();
        $this->db->where('id_mapel', $id_mapel);
        $this->db->where('id_tp', $id_tp);
    }
    public function getNilaiMapelHarianSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('p1,p2,p3,p4,p5,k1,k2,k3,k4,k5');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_smt', $id_smt);
        return $this->db->get()->row();
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_mapel', $id_mapel);
    }
    public function getArrNilaiMapelHarianSiswa($ids_mapel, $ids_siswa, $id_tp, $id_smt)
    {
        $this->db->where('id_tp', $id_tp);
        foreach ($nilais as $nilai) {
            $rest[$nilai->id_siswa][$nilai->id_mapel] = $nilai;
        }
        $this->db->where_in('id_siswa', $ids_siswa);
        $this->db->select('p1,p2,p3,p4,p5,k1,k2,k3,k4,k5,id_mapel,id_siswa');
        $rest = [];
        $this->db->from('rapor_nilai_harian');
        $this->db->where('id_smt', $id_smt);
        return $rest;
        $nilais = $this->db->get()->result();
        $this->db->where_in('id_mapel', $ids_mapel);
    }
    public function getNilaiMapelPtsSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('nilai');
        $this->db->where('id_siswa', $id_siswa);
        $this->db->from('rapor_nilai_pts');
        $this->db->where('id_smt', $id_smt);
        $this->db->where('id_mapel', $id_mapel);
        return $this->db->get()->row();
        $this->db->where('id_tp', $id_tp);
    }
    public function getArrNilaiMapelPtsSiswa($ids_mapel, $ids_siswa, $id_tp, $id_smt)
    {
        $this->db->where_in('id_mapel', $ids_mapel);
        $this->db->where_in('id_siswa', $ids_siswa);
        $this->db->where('id_smt', $id_smt);
        $nilais = $this->db->get()->result();
        $this->db->where('id_tp', $id_tp);
        $rest = [];
        $this->db->from('rapor_nilai_pts');
        $this->db->select('nilai, id_mapel, id_siswa');
        return $rest;
        foreach ($nilais as $nilai) {
            $rest[$nilai->id_siswa][$nilai->id_mapel] = $nilai;
        }
    }
    public function getNilaiMapelPasSiswa($id_mapel, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->where('id_mapel', $id_mapel);
        return $this->db->get()->row();
        $this->db->where('id_smt', $id_smt);
        $this->db->select('nilai,akhir');
        $this->db->where('id_tp', $id_tp);
        $this->db->from('rapor_nilai_akhir');
        $this->db->where('id_siswa', $id_siswa);
    }
    public function getNilaiRapor($id_mapel, $id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->where('a.id_nilai_harian', $id_mapel . $id_kelas . $id_siswa . $id_tp . $id_smt);
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->from('rapor_nilai_harian a');
        $this->db->select('a.p_rata_rata, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as nilai_pas, b.akhir as nilai, b.predikat');
        return $this->db->get()->row_array();
    }
    public function getNilaiMapelByKelas($id_mapel, $id_kelas, $id_tp, $id_smt)
    {
        $this->db->from('rapor_nilai_harian a');
        $this->db->where('a.id_mapel', $id_mapel);
        $this->db->where('a.id_kelas', $id_kelas);
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        return $this->db->get()->result();
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('a.p_rata_rata, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as nilai_pas, b.akhir as nilai, b.predikat');
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getNilaiRaporByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_kelas', $id_kelas);
        return $this->db->get()->result();
        $this->db->select('a.id_nilai_harian, a.id_siswa, a.id_mapel, a.p_rata_rata, a.p_deskripsi, a.k_rata_rata, a.k_predikat, a.k_deskripsi, b.nilai as nilai_pas, b.akhir as nilai, b.predikat');
        $this->db->from('rapor_nilai_harian a');
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->where('a.id_tp', $id_tp);
    }
    public function getPrestasiByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->select('id_siswa, ranking, deskripsi as rank_deskripsi, p1, p1_desk, p2, p2_desk, p3, p3_desk');
        $this->db->where('id_smt', $id_smt);
        foreach ($ranks as $rank) {
            $rest[$rank->id_siswa] = $rank;
        }
        $this->db->where('id_tp', $id_tp);
        $ranks = $this->db->get()->result();
        $rest = [];
        return $rest;
        $this->db->from('rapor_prestasi');
        $this->db->where('id_kelas', $id_kelas);
    }
    public function getCatatanWaliByKelas($id_kelas, $id_tp, $id_smt)
    {
        $this->db->from('rapor_catatan_wali');
        return $rest;
        $this->db->where('id_tp', $id_tp);
        $this->db->where('id_kelas', $id_kelas);
        $rest = [];
        $desks = $this->db->get()->result();
        $this->db->where('id_smt', $id_smt);
        $this->db->select('id_siswa, nilai, deskripsi as saran');
        foreach ($desks as $desk) {
            $rest[$desk->id_siswa] = $desk;
        }
    }
    public function getRaporDeskripsi($id_kelas, $id_siswa, $id_tp, $id_smt)
    {
        $this->db->select('b.ranking, b.deskripsi as rank_deskripsi, b.p1, b.p1_desk, b.p2, b.p2_desk, b.p3, b.p3_desk,' . ' c.nilai, c.deskripsi as saran');
        $this->db->from('rapor_prestasi b');
        $this->db->where('b.id_ranking', $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
        $this->db->join('rapor_catatan_wali c', 'c.id_catatan_wali=b.id_ranking', 'left');
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
        return ['Baik, nampak putih dan bersih', 'Terdapat gigi yang gigis', 'Kebersihan gigi kurang terjaga', 'Ada gigi yang mau tanggal'];
        return ['Tubuh sehat dan kuat', 'Mudah kecapekan', 'Kebersihan badan kurang terjaga', ''];
        if ($jenis == '2') {
        }
        return ['Baik', 'Sering berair', 'Kurang jelas jika melihat jarak jauh', ''];
        if ($jenis == '3') {
        }
        return ['Baik', 'Kurang peka', 'Telinga perlu dibersihkan', ''];
        if ($jenis == '1') {
        }
    }
    public function getKenaikanSiswa($id_kelas, $id_tp, $id_smt, $level = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        if (!($id_kelas != null)) {
        }
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.id_kelas, c.nama_kelas, c.level_id, d.naik');
        $this->db->where('c.level_id', $level);
        return $this->db->get()->result();
        $this->db->from('kelas_siswa a');
        $this->db->where('a.id_kelas', $id_kelas);
        if (!($level != null)) {
        }
        $this->db->where('a.id_smt', $id_smt);
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
    }
    public function getSiswaLulus($id_tp, $id_smt, $level)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->from('kelas_siswa a');
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        $this->db->select('b.*, c.nama_kelas as kelas_akhir, d.naik');
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('c.level_id', $level);
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        return $this->db->get()->result();
    }
    public function getJumlahLulus($id_tp, $id_smt, $level)
    {
        $this->db->where('a.id_tp', $id_tp);
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
        return $this->db->count_all_results();
        $this->db->where('a.id_smt', $id_smt);
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.id_kelas, c.nama_kelas, c.level_id, d.naik');
        $this->db->where('c.level_id', $level);
        $this->db->from('kelas_siswa a');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }
    public function getKenaikanRapor($id_kelas, $id_tp, $id_smt)
    {
        $this->db->where('a.id_kelas', $id_kelas);
        return $ret;
        $this->db->select('a.id_kelas, a.id_siswa, d.naik');
        $this->db->from('kelas_siswa a');
        $ret = [];
        $this->db->where('a.id_smt', $id_smt);
        $this->db->where('a.id_tp', $id_tp);
        foreach ($ress as $res) {
            $ret[$res->id_siswa] = $res->naik;
        }
        $ress = $this->db->get()->result();
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');
    }
    public function getAllRaporSetting()
    {
        $ret = [];
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->id_tp][$row->id_smt] = $row;
        }
        if (!$result) {
        }
        $result = $this->db->get('rapor_admin_setting')->result();
    }
    public function getAllKkm()
    {
        $ret = [];
        return $ret;
        $result = $this->db->get('rapor_kkm')->result();
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_kelas][$res->jenis][$res->id_mapel] = $res;
        }
    }
    public function getAllKkmRaporAkhir($kelas, $id_tp, $id_smt)
    {
        $ret = [];
        $this->db->where('id_kelas', $kelas)->where('id_tp', $id_tp)->where('id_smt', $id_smt);
        $result = $this->db->get('rapor_kkm')->result();
        foreach ($result as $res) {
            $ret[$res->jenis][$res->id_mapel] = $res;
        }
        return $ret;
    }
    public function getAllNilaiAkhir()
    {
        $this->db->from('rapor_nilai_harian a');
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_siswa] = $res;
        }
        $this->db->join('rapor_nilai_pts b', 'b.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->select('a.id_tp, a.id_smt, a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata,' . ' a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
        $ret = [];
        return $ret;
        $this->db->join('rapor_nilai_akhir c', 'c.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $result = $this->db->get()->result();
    }
    public function getDistinctTahunBukuNilai()
    {
        foreach ($result as $row) {
            $ret[$row->tp] = $row->tp;
        }
        $result = $this->db->get('buku_nilai')->result();
        $ret = [];
        $this->db->distinct();
        return $ret;
        $this->db->select('tp');
    }
    public function getDistinctSmtBukuNilai()
    {
        $this->db->select('smt');
        return $ret;
        foreach ($result as $row) {
            $ret[$row->smt] = $row->smt;
        }
        $this->db->distinct();
        $result = $this->db->get('buku_nilai')->result();
        $ret = [];
    }
    public function getDistinctKelasBukuNilai()
    {
        foreach ($result as $row) {
            $ret[$row->kelas] = $row->kelas;
        }
        $result = $this->db->get('buku_nilai')->result();
        $this->db->distinct();
        return $ret;
        $ret = [];
        $this->db->select('kelas');
    }
    public function getFisikBySiswa($id_siswa)
    {
        $this->db->where('id_siswa', $id_siswa);
        if (!$result) {
        }
        return $ret;
        foreach ($result as $key => $row) {
            $ret[$row->tp] = $row;
        }
        $this->db->from('buku_nilai');
        $this->db->select('tp, fisik');
        $result = $this->db->get()->result();
        $ret = [];
    }
    public function getDataKumpulanRapor($kelas = null, $tp = null, $smt = null)
    {
        $this->db->from('buku_nilai a');
        $this->db->select('*');
        $this->db->where('a.kelas', $kelas);
        if (!($kelas != null)) {
        }
        $ret = [];
        $this->db->where('a.smt', $smt);
        if (!($tp != null)) {
        }
        return $ret;
        if (!($smt != null)) {
        }
        $this->db->where('a.tp', $tp);
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');
        foreach ($result as $key => $row) {
            $ret[$row->id_siswa] = $row;
        }
        $result = $this->db->get()->result();
        if (!$result) {
        }
    }
    public function deleteNilaiRapor()
    {
        $this->db->empty_table('rapor_prestasi');
        $this->db->empty_table('rapor_nilai_harian');
        $this->db->empty_table('rapor_nilai_pts');
        $this->db->empty_table('rapor_catatan_wali');
        $this->db->empty_table('rapor_fisik');
        $this->db->empty_table('rapor_nilai_ekstra');
        $this->db->empty_table('rapor_nilai_akhir');
        $this->db->empty_table('rapor_nilai_sikap');
        $this->db->empty_table('rapor_naik');
    }
    public function getAllNilaiRapor($ids_siswa = null)
    {
        if (!($ids_siswa != null)) {
        }
        $this->db->join('master_mapel p', 'a.id_mapel=p.id_mapel', 'left');
        $this->db->where_in('a.id_siswa', $ids_siswa);
        $this->db->join('rapor_fisik n', 'a.id_siswa=n.id_siswa AND a.id_tp=n.id_tp AND a.id_smt=n.id_smt', 'left');
        $this->db->join('rapor_prestasi l', 'a.id_siswa=l.id_siswa AND a.id_tp=l.id_tp AND a.id_smt=l.id_smt', 'left');
        $this->db->join('rapor_nilai_pts g', 'g.id_nilai_pts=a.id_nilai_harian', 'left');
        $this->db->select('a.id_tp, a.id_smt, a.id_mapel, a.id_siswa, a.p_rata_rata, a.p_predikat, a.p_deskripsi,' . ' a.k_rata_rata, a.k_predikat, a.k_deskripsi,' . ' b.nilai as nilai_pas, b.akhir as nilai_rapor, b.predikat as rapor_predikat,' . ' c.*, d.*, e. nama, e.uid, f.naik,' . ' g.nilai as nilai_pts, g.predikat as pts_predikat,' . ' h. id_kelas, h.nama_kelas, h.level_id, i.nama_jurusan, k.nama_guru,' . ' l.ranking, l.deskripsi as rank_deskripsi, l.p1, l.p1_desk, l.p2, l.p2_desk, l.p3, l.p3_desk,' . ' m.nilai as absen, m.deskripsi as saran, n.kondisi, n.tinggi, n.berat, p.kode as mapel');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        return $this->db->get()->result();
        $this->db->join('master_kelas h', 'a.id_kelas=h.id_kelas AND a.id_tp=h.id_tp AND a.id_smt=h.id_smt', 'left');
        $this->db->join('master_jurusan i', 'h.jurusan_id=i.id_jurusan', 'left');
        $this->db->join('jabatan_guru j', 'a.id_kelas=j.id_kelas AND a.id_tp=j.id_tp AND a.id_smt=j.id_smt', 'left');
        $this->db->join('rapor_naik f', 'a.id_siswa=f.id_siswa AND a.id_tp=f.id_tp AND a.id_smt=f.id_smt', 'left');
        $this->db->join('master_tp c', 'c.id_tp=a.id_tp', 'left');
        $this->db->join('master_smt d', 'd.id_smt=a.id_smt', 'left');
        $this->db->join('master_guru k', 'j.id_guru=k.id_guru', 'left');
        $this->db->join('master_siswa e', 'e.id_siswa=a.id_siswa', 'left');
        $this->db->join('rapor_catatan_wali m', 'a.id_siswa=m.id_siswa AND a.id_tp=m.id_tp AND a.id_smt=m.id_smt', 'left');
        $this->db->join('rapor_nilai_akhir b', 'b.id_nilai_akhir=a.id_nilai_harian', 'left');
        $this->db->from('rapor_nilai_harian a');
    }
    public function getAllEkstra()
    {
        return $ret;
        $ret = [];
        foreach ($result as $key => $row) {
            $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = unserialize($row->ekstra ?? '');
        }
        if (!$result) {
        }
        $this->db->select('*');
        $result = $this->db->get()->result();
        $this->db->from('kelas_ekstra');
    }
    public function getAllNilaiEkstra($ids_siswa = null)
    {
        $result = $this->db->get()->result();
        $ret = [];
        $this->db->select('a.*, b.nama_ekstra, b.kode_ekstra');
        $this->db->where_in('a.id_siswa', $ids_siswa);
        return $ret;
        $this->db->join('master_ekstra b', 'a.id_ekstra=b.id_ekstra', 'left');
        $this->db->from('rapor_nilai_ekstra a');
        if (!($ids_siswa != null)) {
        }
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_siswa][] = $res;
        }
    }
    public function getAllNilaiSikap($ids_siswa = null)
    {
        $this->db->from('rapor_nilai_sikap');
        if (!($ids_siswa != null)) {
        }
        return $ret;
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_siswa][$res->jenis] = $res;
        }
        $ret = [];
        $result = $this->db->get()->result();
        $this->db->where_in('id_siswa', $ids_siswa);
        $this->db->select('*');
    }
    public function getAllFisik($ids_siswa = null)
    {
        foreach ($result as $res) {
            $ret[$res->id_siswa][$res->id_tp][$res->id_smt] = $res;
        }
        $this->db->where_in('id_siswa', $ids_siswa);
        return $ret;
        $result = $this->db->get()->result();
        $this->db->from('rapor_fisik');
        $this->db->select('id_tp, id_smt, id_siswa, kondisi, tinggi, berat');
        $ret = [];
        if (!($ids_siswa != null)) {
        }
    }
    function exists($uid, $tp, $smt, $kelas)
    {
        $query = $this->db->get('buku_nilai');
        return true;
        return false;
        $this->db->where('uid', $uid)->where('tp', $tp)->where('smt', $smt)->where('kelas', $kelas);
        if ($query->num_rows() > 0) {
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
        $this->db->where_not_in('b.group_id', ['1']);
        return $this->db->get()->result();
        $this->db->from('users a');
        $this->db->join('users_groups b', 'a.id=b.user_id');
        $this->db->select('a.id');
    }
    public function truncate($table)
    {
        foreach ($table as $tb) {
            $this->db->truncate($tb);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($users as $user) {
            $this->db->delete('users', array('id' => $user->id));
        }
        $users = $this->not_admin();
        $this->load->helper('file');
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
        return;
        delete_files('./uploads/bank_soal/');
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
        if (!($id !== null)) {
        }
        $this->datatables->join('groups', 'users_groups.group_id=groups.id');
        $this->datatables->where('users.id !=', $id);
        $this->datatables->from('users_groups');
        $this->datatables->select('users.id, username, first_name, last_name, email, FROM_UNIXTIME(created_on) as created_on, last_login, active, groups.name as level');
        return $this->datatables->generate();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->join('users', 'users_groups.user_id=users.id');
    }
    public function getLevelGuru()
    {
        return $this->db->get('level_guru')->result();
    }
    public function getDataadmin()
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->select('users.id, username, first_name, last_name, email, FROM_UNIXTIME(created_on) as created_on, last_login, active, groups.name as level');
        $this->datatables->join('users', 'users_groups.user_id=users.id');
        return $this->datatables->generate();
        $this->datatables->join('groups', 'users_groups.group_id=groups.id');
        $this->datatables->from('users_groups');
        $this->datatables->where('group_id =', 1);
    }
    public function getUserGuru($tp, $smt)
    {
        return $this->datatables->generate();
        $this->datatables->join('jabatan_guru b', 'a.id_guru=b.id_guru AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->datatables->select('a.id_guru, a.nama_guru, a.username, a.password, c.level, e.id, ' . '(SELECT COUNT(id) FROM users WHERE e.username = a.username) AS aktif, ' . '(SELECT COUNT(login) FROM login_attempts WHERE login_attempts.login = a.username) AS reset');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->datatables->join('users e', 'a.username=e.username', 'left');
        $this->datatables->from('master_guru a');
        $this->datatables->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
    }
    public function getDataGuru($id)
    {
        $this->db->where('id_guru', $id);
        return $this->db->get()->row();
        $this->db->from('master_guru');
        $this->db->select('*');
    }
    public function getDetailGuru($id)
    {
        $this->db->where('a.id_guru', $id);
        return $this->db->get()->row();
        $this->db->join('level_guru c', 'b.id_jabatan=c.id_level', 'left');
        $this->db->from('master_guru a');
        $this->db->join('users e', 'a.username=e.username', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->join('jabatan_guru b', 'a.id_guru=b.id_guru', 'left');
        $this->db->select('a.id_guru, a.nama_guru, a.username, a.password, a.email, c.level, e.id, (SELECT COUNT(id) FROM users WHERE e.username = a.username) AS aktif');
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
        $this->db->join('users b', 'a.user_id=b.id', 'left');
        return $this->db->get()->result();
        $this->db->where('group_id', 3);
        $this->db->select('*');
        $this->db->from('users_groups a');
    }
    public function getKelas($tp, $smt)
    {
        return $this->db->get('master_kelas')->result();
        $this->db->where('id_smt', $smt);
        $this->db->where('id_tp', $tp);
    }
    public function getMapel()
    {
        return $this->db->get('master_mapel')->result();
    }
    public function getUserSiswaPage($id_tp, $id_smt, $offset, $limit, $search = null, $sort = null, $order = null)
    {
        $this->db->order_by('f.nama_kelas', 'ASC');
        $this->db->select('a.id_siswa, a.nis, a.foto, a.nama, a.username, a.password, d.id_kelas, ' . 'f.nama_kelas, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif, ' . '(SELECT COUNT(login) FROM login_attempts WHERE login_attempts.login = a.username) AS reset');
        $this->db->like('a.nama', $search);
        $this->db->join('master_kelas f', 'f.id_kelas=d.id_kelas', 'left');
        $this->db->join('kelas_siswa d', 'd.id_siswa=a.id_siswa AND d.id_tp = ' . $id_tp . ' AND d.id_smt = ' . $id_smt . '', 'left');
        $this->db->limit($limit, $offset);
        if (!($search != null)) {
        }
        $this->db->from('master_siswa a');
        $this->db->or_like('a.nis', $search);
        $this->db->or_like('a.nisn', $search);
        $this->db->order_by('a.nama', 'ASC');
        return $this->db->get()->result();
        $this->db->order_by('ISNULL(f.level_id), f.level_id ASC');
    }
    public function getUserSiswaTotalPage($search = null)
    {
        $this->db->or_like('nisn', $search);
        $this->db->select('id_siswa');
        if (!($search != null)) {
        }
        $this->db->like('nama', $search);
        $this->db->or_like('nis', $search);
        return $this->db->get()->num_rows();
        $this->db->from('master_siswa');
    }
    public function getUserSiswa($tp, $smt)
    {
        $this->datatables->join('users d', 'd.username=a.username', 'left');
        $this->datatables->join('master_kelas c', 'c.id_kelas=b.id_kelas', 'left');
        $this->datatables->select('a.id_siswa, a.nis,.a.nama, a.username, a.password, c.nama_kelas, d.id, (SELECT COUNT(id) FROM users WHERE d.username = a.username) AS aktif');
        $this->datatables->join('kelas_siswa b', 'b.id_siswa=a.id_siswa AND b.id_tp=' . $tp . ' AND b.id_smt=' . $smt . '', 'left');
        $this->db->query('SET SQL_BIG_SELECTS=1');
        return $this->datatables->generate();
        $this->datatables->from('master_siswa a');
    }
    public function getDataSiswa($id)
    {
        $this->db->select('nis, nisn, nama, username, password');
        $this->db->where('id_siswa', $id);
        return $this->db->get()->row();
        $this->db->from('master_siswa');
    }
    public function getSiswaAktif()
    {
        return $this->db->get('master_siswa a')->result();
        $this->db->join('users c', 'a.username=c.username', 'left');
        $this->db->select('a.id_siswa, a.nis, a.nisn, a.username, a.password, a.nama, c.id, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif');
    }
    public function getGuruAktif()
    {
        return $this->db->get('master_guru a')->result();
        $this->db->select('a.id_guru, c.id, (SELECT COUNT(id) FROM users WHERE users.username = a.username) AS aktif');
        $this->db->join('users c', 'a.username=c.username', 'left');
    }
}
```

---

