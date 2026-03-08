<?php

defined('BASEPATH') or exit('No direct script access allowed');
class HasilUjian extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->ion_auth->logged_in()) {
        }
        redirect('auth');
        $this->load->library(['datatables']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Ujian_model', 'ujian');
        $this->user = $this->ion_auth->user()->row();
    }
    public function output_json($data, $encode = true)
    {
        if (!$encode) {
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function data()
    {
        $nip_guru = null;
        if (!$this->ion_auth->in_group('guru')) {
        }
        $nip_guru = $this->user->username;
        $this->output_json($this->ujian->getHasilUjian($nip_guru), false);
    }
    public function NilaiMhs($id)
    {
        $this->output_json($this->ujian->HslUjianById($id, true), false);
    }
    public function index()
    {
        $data = ['user' => $this->user, 'judul' => 'Ujian', 'subjudul' => 'Hasil Ujian'];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $nilai = $this->ujian->bandingNilai($id);
        $data = ['user' => $this->user, 'judul' => 'Ujian', 'subjudul' => 'Detail Hasil Ujian', 'ujian' => $ujian, 'nilai' => $nilai];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/detail_hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }
    public function cetak($id)
    {
        $mhs = $this->ujian->getIdMahasiswa($this->user->username);
        $hasil = $this->ujian->HslUjian($id, $mhs->id_siswa)->row();
        $ujian = $this->ujian->getUjianById($id);
        $data = ['ujian' => $ujian, 'hasil' => $hasil, 'mhs' => $mhs];
        $this->load->view('ujian/cetak', $data);
    }
    public function cetak_detail($id)
    {
        $ujian = $this->ujian->getUjianById($id);
        $nilai = $this->ujian->bandingNilai($id);
        $hasil = $this->ujian->HslUjianById($id)->result();
        $data = ['ujian' => $ujian, 'nilai' => $nilai, 'hasil' => $hasil];
        $this->load->view('ujian/cetak_detail', $data);
    }
}