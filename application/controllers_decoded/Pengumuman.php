<?php

class Pengumuman extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
        }
        if (!(!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru'))) {
        }
        show_error('Hanya Administrator dan guru yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
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
        }
        $data = json_encode($data);
        $this->output->set_content_type('application/json')->set_output($data);
    }
    public function index()
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Pengumuman', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['gurus'] = $this->dropdown->getAllGuru();
        $kelas = $this->dropdown->getAllKeyKodeKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $kelas;
        $data['running_text'] = $this->dashboard->getRunningText();
        if ($this->ion_auth->is_admin()) {
        }
        $data['subjudul'] = 'Pengumuman Anda';
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $data['guru'] = $guru;
        $data['pengumumans'] = $this->post->getPostUser($guru->id_guru);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('pengumuman/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function kepada($kepada, $id_kepada = null)
    {
        $user = $this->ion_auth->user()->row();
        $data = ['user' => $user, 'judul' => 'Pengumuman', 'subjudul' => 'Semua Pengumuman', 'setting' => $this->dashboard->getSetting()];
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $data['tp'] = $this->dashboard->getTahun();
        $data['tp_active'] = $tp;
        $data['smt'] = $this->dashboard->getSemester();
        $data['smt_active'] = $smt;
        $data['gurus'] = $this->dropdown->getAllGuru();
        $kelas = $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt);
        $data['kelas'] = $kelas;
        $this->db->select('a.*, b.nama_guru, b.foto');
        $this->db->from('post a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $pengumumans = $this->db->get()->result();
        $comments = [];
        $balasan = [];
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
        $data['pengumumans'] = $pengumumans;
        $data['comments'] = $comments;
        $data['balasans'] = $balasan;
        if ($kepada === 'semua_guru') {
        }
        if ($kepada === 'semua_siswa') {
        }
        $data['kepada'] = urldecode($kepada);
        if ($this->ion_auth->is_admin()) {
        }
        $data['guru'] = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $this->load->view('members/guru/templates/header', $data);
        $this->load->view('pengumuman/data');
        $this->load->view('members/guru/templates/footer');
    }
    public function getPost()
    {
        $post = $this->post->getPostForUser(null);
        $this->output_json($post);
    }
    public function getComment($id_post, $page)
    {
        $perPage = 5;
        $offset = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_post', $id_post);
        $this->db->limit($perPage, $offset);
        $comment = $this->db->get()->result();
        $this->output_json($comment);
    }
    public function getReplies($id_comment, $page)
    {
        $perPage = 5;
        $offset = $page * $perPage;
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id_comment);
        $this->db->limit($perPage, $offset);
        $replies = $this->db->get()->result();
        $this->output_json($replies);
    }
    public function save()
    {
        $kepada = json_decode(json_encode($this->input->post('kepada[]', true)));
        $dari = $this->input->post('dari');
        $data = ['kepada' => serialize($kepada), 'dari' => $dari, 'dari_group' => $dari == '0' ? '1' : '2', 'text' => $this->input->post('text'), 'tanggal' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')];
        $insert = $this->db->replace('post', $data);
        $this->output_json($insert);
    }
    public function saveKomentar()
    {
        $dari = '0';
        $dari_group = 1;
        if ($this->ion_auth->is_admin()) {
        }
        $user = $this->ion_auth->user()->row();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $dari = $guru->id_guru;
        $dari_group = 2;
        $data = ['id_post' => $this->input->post('id_post'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $insert = $this->db->replace('post_comments', $data);
        $id = $this->db->insert_id();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa, (SELECT COUNT(post_reply.id_reply) FROM post_reply WHERE a.id_comment = post_reply.id_comment) AS jml');
        $this->db->from('post_comments a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_comment', $id);
        $comment = $this->db->get()->result();
        $this->output_json($comment);
    }
    public function saveBalasan()
    {
        $dari = '0';
        $dari_group = 1;
        if ($this->ion_auth->is_admin()) {
        }
        $user = $this->ion_auth->user()->row();
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
        $dari = $guru->id_guru;
        $dari_group = 2;
        $data = ['id_comment' => $this->input->post('id_comment'), 'dari' => $dari, 'dari_group' => $dari_group, 'text' => $this->input->post('text')];
        $insert = $this->db->replace('post_reply', $data);
        $id = $this->db->insert_id();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->db->select('a.*, b.nama_guru, b.foto, c.nama as nama_siswa, c.foto as foto_siswa');
        $this->db->from('post_reply a');
        $this->db->join('master_guru b', 'a.dari=b.id_guru', 'left');
        $this->db->join('master_siswa c', 'a.dari=c.id_siswa', 'left');
        $this->db->order_by('a.tanggal', 'desc');
        $this->db->where('a.id_reply', $id);
        $replies = $this->db->get()->result();
        $this->output_json($replies);
    }
    public function hapusPost($id_post)
    {
        $this->db->trans_start();
        $comments = $this->post->getIdComments($id_post);
        foreach ($comments as $comment) {
            $this->db->where('id_comment', $comment->id_comment);
            $deleted['balasan'] = $this->db->delete('post_reply');
        }
        $this->db->where('id_post', $id_post);
        if (!$this->db->delete('post_comments')) {
        }
        $this->db->where('id_post', $id_post);
        $deleted = $this->db->delete('post');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }
    public function hapusKomentar($id_comment)
    {
        $this->db->trans_start();
        $this->db->where('id_comment', $id_comment);
        $deleted['komentar'] = $this->db->delete('post_comments');
        $this->db->where('id_comment', $id_comment);
        $deleted['balasan'] = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }
    public function hapusBalasan($id_reply)
    {
        $this->db->trans_start();
        $this->db->where('id_reply', $id_reply);
        $deleted['balasan'] = $this->db->delete('post_reply');
        $this->db->trans_complete();
        $this->output_json($deleted);
    }
    public function getRunningText()
    {
        $data['running_text'] = $this->dashboard->getRunningText();
        $this->output_json($data);
    }
    public function saveRunningText()
    {
        $input = json_decode($this->input->post('text', true));
        $updates = [];
        foreach ($input as $d) {
            $data = ['id_text' => $d->id_text, 'text' => $d->text];
            $update = $this->db->replace('running_text', $data);
            array_push($updates, $update);
        }
        $data['status'] = $updates;
        $this->output_json($data);
    }
    public function hapusRunningText($id)
    {
        $this->db->where('id_text', $id);
        $deleted = $this->db->delete('running_text');
        $this->output_json($deleted);
    }
}