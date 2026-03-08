<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Cbttoken extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin() && !$this->ion_auth->in_group('guru')) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Log_model', 'logging');
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
        $tp = $this->master->getTahunActive();
        $smt = $this->master->getSemesterActive();
        $token = $this->cbt->getToken();
        $default = json_decode(json_encode(['token' => '', 'auto' => '0', 'jarak' => '1', 'elapsed' => '00:00:00']));
        $data = [
            'user'      => $user,
            'judul'     => 'Token Ujian',
            'subjudul'  => 'Token',
            'setting'   => $this->dashboard->getSetting(),
            'tp'        => $this->dashboard->getTahun(),
            'tp_active' => $tp,
            'smt'       => $this->dashboard->getSemester(),
            'smt_active' => $smt,
            'token'     => $token !== null ? $token : $default,
        ];
        if ($this->ion_auth->is_admin()) {
            $data['profile'] = $this->dashboard->getProfileAdmin($user->id);
            $this->load->view('_templates/dashboard/_header', $data);
            $this->load->view('cbt/token/data');
            $this->load->view('_templates/dashboard/_footer');
        } else {
            $guru = $this->dashboard->getDataGuruByUserId($user->id, $tp->id_tp, $smt->id_smt);
            $data['guru'] = $guru;
            $this->load->view('members/guru/templates/header', $data);
            $this->load->view('members/guru/cbt/token/data');
            $this->load->view('members/guru/templates/footer');
        }
    }

    public function generateToken()
    {
        $post_token = json_decode($this->input->get('data'));
        $force = $this->input->get('force');
        $token = $this->cbt->getToken();
        $updated = date('Y-m-d H:i:s');
        $new = null;
        if ($force == '1') {
            $new = $this->createNewToken();
        } else {
            $mulai = new DateTime($token->updated);
            $diff = $mulai->diff(new DateTime());
            $total_minutes = $diff->days * 24 * 60 + $diff->h * 60 + $diff->i;
            if ($total_minutes >= $post_token->jarak) {
                $new = $this->createNewToken();
            }
        }
        if ($new !== null) {
            $post_token->token = $new;
            $post_token->updated = $updated;
            $this->cbt->saveToken($post_token);
        }
        $token = $this->cbt->getToken();
        $token->now = $updated;
        $this->output_json($token);
    }

    public function loadToken()
    {
        $dataflds = $this->db->field_data('cbt_token');
        foreach ($dataflds as $fld) {
            if ($fld->name == 'updated' && $fld->type != 'varchar') {
                $field = ['updated' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => '']];
                $this->dbforge->modify_column('cbt_token', $field);
            }
        }
        $token = $this->cbt->getToken();
        if ($token === null) {
            $this->output_json(['token' => '', 'auto' => '0', 'elapsed' => '00:00:00']);
        } else {
            $token->now = date('Y-m-d H:i:s');
            $this->output_json($token);
        }
    }

    private function createNewToken()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($chars);
        $token = '';
        for ($i = 0; $i < 6; $i++) {
            $token .= $chars[mt_rand(0, $len - 1)];
        }
        return $token;
    }
}
