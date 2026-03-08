<?php

defined('BASEPATH') or exit('No direct script access allowed');

class HasilUjian extends CI_Controller
{
    protected $user;

    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        $this->load->library(['datatables']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Ujian_model', 'ujian');
        $this->user = $this->ion_auth->user()->row();
    }

    public function output_json($data, $encode = true)
    {
        if (!$encode) {
            $this->output->set_content_type('application/json')->set_output($data);
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode($data));
        }
    }

    public function data()
    {
        $nip_guru = $this->ion_auth->in_group('guru') ? $this->user->username : null;
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
        $data = [
            'user'    => $this->user,
            'judul'   => 'Ujian',
            'subjudul' => 'Detail Hasil Ujian',
            'ujian'   => $this->ujian->getUjianById($id),
            'nilai'   => $this->ujian->bandingNilai($id),
        ];
        $this->load->view('_templates/dashboard/_header.php', $data);
        $this->load->view('ujian/detail_hasil');
        $this->load->view('_templates/dashboard/_footer.php');
    }

    public function cetak($id)
    {
        $mhs   = $this->ujian->getIdMahasiswa($this->user->username);
        $hasil = $this->ujian->HslUjian($id, $mhs->id_siswa)->row();
        $this->load->view('ujian/cetak', ['ujian' => $this->ujian->getUjianById($id), 'hasil' => $hasil, 'mhs' => $mhs]);
    }

    public function cetak_detail($id)
    {
        $this->load->view('ujian/cetak_detail', [
            'ujian'  => $this->ujian->getUjianById($id),
            'nilai'  => $this->ujian->bandingNilai($id),
            'hasil'  => $this->ujian->HslUjianById($id)->result(),
        ]);
    }
}
