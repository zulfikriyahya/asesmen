<?php

defined('BASEPATH') or exit('No direct script access allowed');

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
        foreach ($result as $row) {
            $ret[$row->id_mapel] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_nilai_pts] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
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
        foreach ($result as $row) {
            $ret[$row->id_kelas][$row->id_tp][$row->id_smt] = $row;
        }
        return $ret;
    }

    public function getAllRaporFisik()
    {
        $result = $this->db->get('rapor_fisik')->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa][$row->id_tp][$row->id_smt] = $row;
        }
        return $ret;
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
        $this->db->select('b.ranking, b.deskripsi as rank_deskripsi, b.p1, b.p1_desk, b.p2, b.p2_desk, b.p3, b.p3_desk, c.nilai, c.deskripsi as saran');
        $this->db->from('rapor_prestasi b');
        $this->db->join('rapor_catatan_wali c', 'c.id_catatan_wali=b.id_ranking', 'left');
        $this->db->where('b.id_ranking', $id_kelas . $id_siswa . $id_tp . $id_smt);
        return $this->db->get()->row();
    }

    public function getDummyDeskripsiSpiritual()
    {
        return [
            'berdoa sebelum dan sesudah melakukan kegiatan',
            'menjalankan ibadah sesuai dengan agamanya',
            'memberi salam pada saat awal dan akhir kegiatan',
            'bersyukur atas nikmat dan karunia Tuhan Yang Maha Esa',
            'mensyukuri kemampuan manusia dalam mengendalikan diri',
            'bersyukur ketika berhasil mengerjakan sesuatu',
            'berserah diri (tawakal) kepada Tuhan setelah berikhtiar atau melakukan usaha',
            'memelihara hubungan baik dengan sesama umat',
            'bersyukur sebagai bangsa Indonesia',
            'menghormati orang lain yang menjalankan ibadah sesuai dengan agamanya',
        ];
    }

    public function getDummyDeskripsiSosial()
    {
        return ['jujur', 'disiplin', 'tanggung jawab', 'santun', 'percaya diri', 'peduli', 'toleransi', 'gotong royong', 'rajin', 'tidak mudah menyerah'];
    }

    public function getDummyDeskripsiAbsensi()
    {
        return [
            'Kehadiran cukup baik namun perlu ditingkatkan.',
            'Usahakan hadir setiap hari.',
            'Jangan terlalu banyak alpa, diharapkan selalu hadir ke sekolah',
            'Kehadiranmu sangat jarang sekali',
        ];
    }

    public function getDummyDeskripsiCatatan()
    {
        return [
            'Selalu berusaha untuk mematuhi tata tertib sekolah dan patuh terhadap Guru.',
            'Selalu berusaha untuk mandiri dan tepat waktu dalam mengerjakan tugas.',
            'Mempunyai kemampuan dan motivasi yang tinggi untuk menggunakan waktu secara efisien.',
            'Diharapkan merubah penampilannya menjadi lebih rapi, seperti tentang potong rambut dan cara berpakaian.',
            'Masih perlu memperbanyak teman bergaul dan teman diskusi, kurangi aktifitas menyendiri.',
            'Diharapkan dapat meningkatkan komitmennya untuk lebih serius saat mengerjakan tugas dan tidak mudah menyerah.',
        ];
    }

    public function getDummyDeskripsiRanking()
    {
        return [
            'Prestasinya sangat baik, perlu dipertahankan.',
            'Prestasi baik, perlu dipertahankan dan dtingkatkan.',
            'Prestasi cukup, perlu ditingkatkan belajar dan berdoa.',
            'Perlu ditingkatkan belajarnya, jangan lupa berdoa.',
            'Perlu dimaksimalkan belajarnya, usaha keras dan berdoa.',
            'Perlu usaha keras, maksimalkan belajarnya, lebih giat berdoa dan beribadah.',
        ];
    }

    public function getDummyDeskripsiFisik($jenis)
    {
        if ($jenis == '1') {
            return ['Baik', 'Kurang peka', 'Telinga perlu dibersihkan', ''];
        }
        return ['Tubuh sehat dan kuat', 'Mudah kecapekan', 'Kebersihan badan kurang terjaga', ''];
    }

    public function getKenaikanSiswa($id_kelas, $id_tp, $id_smt, $level = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama, b.nis, b.nisn, b.username, c.id_kelas, c.nama_kelas, c.level_id, d.naik');
        $this->db->from('kelas_siswa a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa', 'left');
        $this->db->join('master_kelas c', 'a.id_kelas=c.id_kelas', 'left');
        $this->db->join('rapor_naik d', 'a.id_siswa=d.id_siswa AND a.id_tp=d.id_tp AND a.id_smt=d.id_smt', 'left');

        if ($level !== null) {
            $this->db->where('c.level_id', $level);
        }
        if ($id_kelas !== null) {
            $this->db->where('a.id_kelas', $id_kelas);
        }

        $this->db->where('a.id_tp', $id_tp);
        $this->db->where('a.id_smt', $id_smt);
        return $this->db->get()->result();
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
        foreach ($result as $row) {
            $ret[$row->id_tp][$row->id_smt] = $row;
        }
        return $ret;
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
        $this->db->select('a.id_tp, a.id_smt, a.id_siswa, a.p_rata_rata as nhar, a.p_deskripsi, a.k_rata_rata,'
            . ' a.k_predikat, a.k_deskripsi, b.nilai as npts, c.nilai as npas, c.predikat');
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
        foreach ($result as $row) {
            $ret[$row->tp] = $row;
        }
        return $ret;
    }

    public function getDataKumpulanRapor($kelas = null, $tp = null, $smt = null)
    {
        $this->db->select('*');
        $this->db->from('buku_nilai a');
        $this->db->join('master_siswa b', 'a.id_siswa=b.id_siswa');

        if ($tp !== null) {
            $this->db->where('a.tp', $tp);
        }
        if ($smt !== null) {
            $this->db->where('a.smt', $smt);
        }
        if ($kelas !== null) {
            $this->db->where('a.kelas', $kelas);
        }

        $result = $this->db->get()->result();
        $ret    = [];
        foreach ($result as $row) {
            $ret[$row->id_siswa] = $row;
        }
        return $ret;
    }

    public function deleteNilaiRapor()
    {
        $tables = [
            'rapor_nilai_harian',
            'rapor_nilai_akhir',
            'rapor_naik',
            'rapor_nilai_pts',
            'rapor_prestasi',
            'rapor_catatan_wali',
            'rapor_fisik',
            'rapor_nilai_ekstra',
            'rapor_nilai_sikap',
        ];
        foreach ($tables as $table) {
            $this->db->empty_table($table);
        }
    }

    public function getAllNilaiRapor($ids_siswa = null)
    {
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.id_tp, a.id_smt, a.id_mapel, a.id_siswa, a.p_rata_rata, a.p_predikat, a.p_deskripsi,'
            . ' a.k_rata_rata, a.k_predikat, a.k_deskripsi,'
            . ' b.nilai as nilai_pas, b.akhir as nilai_rapor, b.predikat as rapor_predikat,'
            . ' c.*, d.*, e.nama, e.uid, f.naik,'
            . ' g.nilai as nilai_pts, g.predikat as pts_predikat,'
            . ' h.id_kelas, h.nama_kelas, h.level_id, i.nama_jurusan, k.nama_guru,'
            . ' l.ranking, l.deskripsi as rank_deskripsi, l.p1, l.p1_desk, l.p2, l.p2_desk, l.p3, l.p3_desk,'
            . ' m.nilai as absen, m.deskripsi as saran, n.kondisi, n.tinggi, n.berat, p.kode as mapel');
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

        if ($ids_siswa !== null) {
            $this->db->where_in('a.id_siswa', $ids_siswa);
        }

        return $this->db->get()->result();
    }

    public function getAllEkstra()
    {
        $this->db->select('*');
        $this->db->from('kelas_ekstra');
        $result = $this->db->get()->result();

        $ret = [];
        foreach ($result as $row) {
            $ret[$row->id_tp][$row->id_smt][$row->id_kelas] = unserialize($row->ekstra ?? '');
        }
        return $ret;
    }

    private function _buildNilaiGrouped($ids_siswa, $key_map)
    {
        if ($ids_siswa !== null) {
            $this->db->where_in(isset($key_map['table']) ? $key_map['table'] . '.id_siswa' : 'id_siswa', $ids_siswa);
        }
        $result = $this->db->get()->result();
        $ret    = [];
        foreach ($result as $res) {
            $path = [];
            foreach ($key_map['keys'] as $k) {
                $path[] = $res->$k;
            }
            $ref = &$ret;
            foreach ($path as $i => $seg) {
                if ($i === count($path) - 1) {
                    $ref[$seg] = $res;
                } else {
                    if (!isset($ref[$seg])) {
                        $ref[$seg] = [];
                    }
                    $ref = &$ref[$seg];
                }
            }
            unset($ref);
        }
        return $ret;
    }

    public function getAllNilaiEkstra($ids_siswa = null)
    {
        $this->db->select('a.*, b.nama_ekstra, b.kode_ekstra');
        $this->db->from('rapor_nilai_ekstra a');
        $this->db->join('master_ekstra b', 'a.id_ekstra=b.id_ekstra', 'left');

        if ($ids_siswa !== null) {
            $this->db->where_in('a.id_siswa', $ids_siswa);
        }

        $result = $this->db->get()->result();
        $ret    = [];
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_siswa][] = $res;
        }
        return $ret;
    }

    public function getAllNilaiSikap($ids_siswa = null)
    {
        $this->db->select('*');
        $this->db->from('rapor_nilai_sikap');

        if ($ids_siswa !== null) {
            $this->db->where_in('id_siswa', $ids_siswa);
        }

        $result = $this->db->get()->result();
        $ret    = [];
        foreach ($result as $res) {
            $ret[$res->id_tp][$res->id_smt][$res->id_siswa][$res->jenis] = $res;
        }
        return $ret;
    }

    public function getAllFisik($ids_siswa = null)
    {
        $this->db->select('id_tp, id_smt, id_siswa, kondisi, tinggi, berat');
        $this->db->from('rapor_fisik');

        if ($ids_siswa !== null) {
            $this->db->where_in('id_siswa', $ids_siswa);
        }

        $result = $this->db->get()->result();
        $ret    = [];
        foreach ($result as $res) {
            $ret[$res->id_siswa][$res->id_tp][$res->id_smt] = $res;
        }
        return $ret;
    }

    public function exists($uid, $tp, $smt, $kelas)
    {
        $this->db->where('uid', $uid)->where('tp', $tp)->where('smt', $smt)->where('kelas', $kelas);
        return $this->db->get('buku_nilai')->num_rows() > 0;
    }
}
