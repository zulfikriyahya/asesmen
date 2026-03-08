<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pengumuman extends CI_Controller
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
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Kelas_model', 'kelas');
        $this->load->model('Dropdown_model', 'dropdown');
        $this->load->model('Post_model', 'post');
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

    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $tp   = $this->master->getTahunActive();
        $smt  = $this->master->getSemesterActive();

        $data = [
            'user'         => $user,
            'judul'        => 'Pengumuman',
            'setting'      => $this->dashboard->getSetting(),
            'tp'           => $this->dashboard->getTahun(),
            'tp_active'    => $tp,
            'smt'          => $this->dashboard->getSemester(),
            'smt_active'   => $smt,
            'gurus'        => $this->dropdown->getAllGuru(),
            'kelas'        => $this->dropdown->getAllKeyKodeKelas($tp->id_tp, $smt->id_smt),
            'running_text' => $this->dashboard->getRunningText(),
        ];

        if ($this->ion_auth->is_admin()) {
            $data['subjudul']     = 'Semua Pengumuman';
            $data['profile']      = $this->dashboard->getProfileAdmin($user->id);
            $data['pengumumans']  = $this->post->getPostUser(0);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('pengumuman/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru                = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['subjudul']    = 'Pengumuman Anda';
            $data['guru']        = $guru;
            $data['pengumumans'] = $this->post->getPostUser($guru->id_guru);
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('pengumuman/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function kepada($kepada, $id_kepada = null)
    {
        $user = $this->ion_auth->user()->row();
        $tp   = $this->master->getTahunActive();
        $smt  = $this->master->getSemesterActive();

        $this->db->select('a.*, b.nama_guru, b.foto');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $pengumumans = $this->db->get()->result();

        $comments = [];
        $balasan  = [];

        foreach ($pengumumans as $pengumuman) {
            $this->db->select('a.*, b.nama_guru, b.foto');
            $this->db->from('post_comments a');
            $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
            $this->db->order_by('a.tanggal', 'desc');
            $this->db->where('a.id_post', $pengumuman->id_post);
            $comment = $this->db->get()->result();

            foreach ($comment as $comm) {
                $this->db->select('a.*, b.nama_guru, b.foto');
                $this->db->from('post_reply a');
                $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
                $this->db->order_by('a.tanggal', 'desc');
                $this->db->where('a.id_comment', $comm->id_comment);
                $balasan[$pengumuman->id_post][$comm->id_comment] = $this->db->get()->result();
            }

            $comments[$pengumuman->id_post] = $comment;
        }

        if ($kepada === 'semua_guru' || $kepada === 'semua_siswa') {
            $label_kepada = $kepada === 'semua_guru' ? 'Semua Guru' : 'Semua Siswa';
        } else {
            $label_kepada = urldecode($kepada);
        }

        $data = [
            'user'        => $user,
            'judul'       => 'Pengumuman',
            'subjudul'    => 'Semua Pengumuman',
            'setting'     => $this->dashboard->getSetting(),
            'tp'          => $this->dashboard->getTahun(),
            'tp_active'   => $tp,
            'smt'         => $this->dashboard->getSemester(),
            'smt_active'  => $smt,
            'gurus'       => $this->dropdown->getAllGuru(),
            'kelas'       => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'pengumumans' => $pengumumans,
            'comments'    => $comments,
            'balasans'    => $balasan,
            'kepada'      => $label_kepada,
            'guru'        => $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt),
        ];

        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('pengumuman/data');
        $this->load->view('members/guru/templates/footer');
    }

    public function getPost()
    {
        $this->output_json($this->post->getPostForUser(null));
    }

    public function getComment($id_post, $page)
    {
        $perPage = 5;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_post', $id_post);
        $this->db->limit($perPage, $page * $perPage);
        $this->output_json($this->db->get()->result());
    }

    public function getReplies($id_comment, $page)
    {
        $perPage = 5;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id_comment);
        $this->db->limit($perPage, $page * $perPage);
        $this->output_json($this->db->get()->result());
    }

    public function save()
    {
        $kepada = json_decode(json_encode($this->input->post('kepada[]', true)));
        $dari   = $this->input->post('dari');

        $insert = $this->db->replace('post', [
            'kepada'      => serialize($kepada),
            'dari'        => $dari,
            'dari_group'  => $dari == '0' ? '1' : '2',
            'text'        => $this->input->post('text'),
            'tanggal'     => date('Y-m-d H:i:s'),
            'updated'     => date('Y-m-d H:i:s'),
        ]);

        $this->output_json($insert);
    }

    public function saveKomentar()
    {
        if ($this->ion_auth->is_admin()) {
            $dari       = '0';
            $dari_group = 1;
        } else {
            $user       = $this->ion_auth->user()->row();
            $tp         = $this->master->getTahunActive();
            $smt        = $this->master->getSemesterActive();
            $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $dari       = $guru->id_guru;
            $dari_group = 2;
        }

        $this->db->replace('post_comments', ['id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')]);
        $id = $this->db->insert_id();

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id);
        $this->output_json($this->db->get()->result());
    }

    public function saveBalasan()
    {
        if ($this->ion_auth->is_admin()) {
            $dari       = '0';
            $dari_group = 1;
        } else {
            $user       = $this->ion_auth->user()->row();
            $tp         = $this->master->getTahunActive();
            $smt        = $this->master->getSemesterActive();
            $guru       = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $dari       = $guru->id_guru;
            $dari_group = 2;
        }

        $this->db->replace('post_reply', ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')]);
        $id = $this->db->insert_id();

        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_reply', $id);
        $this->output_json($this->db->get()->result());
    }

    public function hapusPost($id_post)
    {
        $this->db->trans_start();
        $comments = $this->post->getIdComments($id_post);

        foreach ($comments as $comment) {
            $this->db->where('id_comment', $comment->id_comment);
            $this->db->delete('post_reply');
        }

        $this->db->where('id_post', $id_post);
        $this->db->delete('post_comments');
        $this->db->where('id_post', $id_post);
        $deleted = $this->db->delete('post');
        $this->db->trans_complete();

        $this->output_json($deleted);
    }

    public function hapusKomentar($id_comment)
    {
        $this->db->trans_start();
        $this->db->where('id_comment', $id_comment);
        $this->db->delete('post_comments');
        $this->db->where('id_comment', $id_comment);
        $deleted = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }

    public function hapusBalasan($id_reply)
    {
        $this->db->trans_start();
        $this->db->where('id_reply', $id_reply);
        $deleted = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }

    public function getRunningText()
    {
        $this->output_json(['running_text' => $this->dashboard->getRunningText()]);
    }

    public function saveRunningText()
    {
        $input   = json_decode($this->input->post('text', true));
        $updates = [];

        foreach ($input as $d) {
            $updates[] = $this->db->replace('running_text', ['id_text' => $d->id_text, 'text' => $d->text]);
        }

        $this->output_json(['status' => $updates]);
    }

    public function hapusRunningText($id)
    {
        $this->db->where('id_text', $id);
        $this->output_json($this->db->delete('running_text'));
    }
}
