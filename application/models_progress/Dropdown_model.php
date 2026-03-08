<?php

class Dropdown_model extends CI_Model
{
    public function getBulan()
    {
        $result = $this->db->get('bulan')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_bln] = $row->nama_bln;
        }
        return $ret;
    }
    public function getAllSesi()
    {
        $this->db->select('id_sesi, nama_sesi, kode_sesi');
        $result = $this->db->get('cbt_sesi')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_sesi] = $row->nama_sesi;
        }
        return $ret;
    }
    public function getAllRuang()
    {
        $result = $this->db->get('cbt_ruang')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_ruang] = $row->nama_ruang;
        }
        return $ret;
    }
    public function getAllWaktuSesi()
    {
        $result = $this->db->get('cbt_sesi')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_sesi] = ['mulai' => $row->waktu_mulai, 'akhir' => $row->waktu_akhir];
        }
        return $ret;
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
        }
        foreach ($result as $row) {
            $ret[$row->id_mapel] = $row->nama_mapel;
        }
        return $ret;
    }
    public function getAllKodeMapel()
    {
        $this->db->order_by('urutan_tampil');
        $this->db->where('status', '1');
        $result = $this->db->get('master_mapel')->result();
        $ret[''] = 'Tidak ada';
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_mapel] = $row->kode;
        }
        return $ret;
    }
    public function getAllMapelPeminatan()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->where('kategori <> "WAJIB"')
            ->where('kategori <> "PAI (Kemenag)"')
            ->where('kategori <> "MULOK"');
        $res = $this->db->get()->result();
        $ress = [];
        if ($res) {
            foreach ($res as $row) {
                $ress[$row->id_kel_mapel] = $row->kode_kel_mapel;
            }
        }
        $ret = [];
        if (count($ress) > 0) {
            $this->db->where_in('kelompok', $ress);
        }
        $this->db->order_by('urutan_tampil');
        $result = $this->db->get('master_mapel')->result();
        if ($result) {
            foreach ($result as $row) {
                $ret[$row->id_mapel] = $row->nama_mapel;
            }
        }
        return $ret;
    }
    public function getAllKodePeminatan()
    {
        $this->db->select('*');
        $this->db->from('master_kelompok_mapel');
        $this->db->where('kategori <> "WAJIB"');
        $this->db->where('kategori <> "PAI (Kemenag)"');
        $this->db->where('kategori <> "MULOK"');
        $res = $this->db->get()->result();
        $ress = [];
        if (!$res) {
            return $ress;
        }
        foreach ($res as $row) {
            $ress[$row->id_kel_mapel] = $row;
        }
        return $ress;
    }
    public function getMapelPeminatan($arr_kelompok)
    {
        if (count($arr_kelompok) > 0) {
            $this->db->where_in('kelompok', $arr_kelompok);
            $this->db->order_by('urutan_tampil');
            $result = $this->db->get('master_mapel')->result();
            $ret = [];
            if ($result) {
                foreach ($result as $row) {
                    $ret[$row->kelompok][$row->id_mapel] = $row->nama_mapel;
                }
            }
            return $ret;
        }
        return [];
    }
    public function getAllLevel($jenjang)
    {
        if ($jenjang == '1') {
            return ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
        } elseif ($jenjang == '2') {
            return ['7' => '7', '8' => '8', '9' => '9'];
        } elseif ($jenjang == '3') {
            return ['10' => '10', '11' => '11', '12' => '12'];
        }
        return [];
    }
    public function getAllKelas($tp, $smt, $level = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $this->db->order_by('level_id', 'ASC');
        $this->db->order_by('nama_kelas', 'ASC');
        if ($level != null) {
            $this->db->where('level_id', $level);
        }
        $result = $this->db->get()->result();
        $ret = [];
        if ($result) {
            foreach ($result as $row) {
                $ret[$row->id_kelas] = $row->nama_kelas;
            }
        }
        return $ret;
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
        }
        foreach ($result as $row) {
            $ret[$row->kode_kelas] = $row->nama_kelas;
        }
        return $ret;
    }
    public function getAllEkskul()
    {
        $result = $this->db->get('master_ekstra')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_ekstra] = $row->nama_ekstra;
        }
        return $ret;
    }
    public function getAllKodeEkskul()
    {
        $result = $this->db->get('master_ekstra')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_ekstra] = $row->kode_ekstra;
        }
        return $ret;
    }
    public function getAllJurusan()
    {
        $result = $this->db->get('master_jurusan')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_jurusan] = $row->kode_jurusan;
        }
        return $ret;
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
        }
        foreach ($result as $row) {
            $ret[$row->id_guru] = $row->nama_guru;
        }
        return $ret;
    }
    public function getAllLevelGuru()
    {
        $result = $this->db->get('level_guru')->result();
        $ret[''] = 'Pilih Jabatan :';
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_level] = $row->level;
        }
        return $ret;
    }
    public function getAllJenisUjian()
    {
        $result = $this->db->get('cbt_jenis')->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_jenis] = $row->nama_jenis . ' (' . $row->kode_jenis . ')';
        }
        return $ret;
    }
    public function getAllBankSoal()
    {
        $result = $this->db->get('cbt_bank_soal')->result();
        $ret[''] = 'Pilih Bank Soal :';
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_bank] = $row->bank_kode;
        }
        return $ret;
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
        }
        foreach ($result as $row) {
            $ret[$row->id_jadwal] = $row->bank_kode;
        }
        return $ret;
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
        }
        foreach ($result as $row) {
            $ret[$row->id_jadwal] = $row->nama_mapel;
        }
        return array_unique($ret);
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
        }
        foreach ($result as $row) {
            $ret[$row->id_jadwal] = $row->bank_kode;
        }
        return $ret;
    }
    public function getAllJenisJadwal($tp, $smt, $jenis, $mapel)
    {
        $this->db->from('cbt_jadwal a');
        if ($mapel == '0') {
            $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank');
        } else {
            $this->db->join('cbt_bank_soal b', 'b.id_bank=a.id_bank AND b.bank_mapel_id=' . $mapel);
        }
        $this->db->where('a.id_tp', $tp);
        $this->db->where('a.id_smt', $smt);
        $this->db->where('a.id_jenis', $jenis);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_jadwal] = $row->bank_kode;
        }
        return $ret;
    }

    public function getAllNamaKelas($tp, $smt)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        $this->db->where('id_tp', $tp);
        $this->db->where('id_smt', $smt);
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        return $ret;
    }

    public function getAllKodeKelas($tp = null, $smt = null)
    {
        $this->db->select('*');
        $this->db->from('master_kelas');
        if ($tp != null) {
            $this->db->where('id_tp', $tp);
        }
        if ($smt != null) {
            $this->db->where('id_smt', $smt);
        }
        $result = $this->db->get()->result();
        $ret = [];
        if (!$result) {
            return $ret;
        }
        foreach ($result as $row) {
            $ret[$row->id_kelas] = $row->kode_kelas;
        }
        return $ret;
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
        }
        return null;
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
        }
        foreach ($result as $row) {
            $ret[$row->id_kelas] = $row->nama_kelas;
        }
        return $ret;
    }
}
