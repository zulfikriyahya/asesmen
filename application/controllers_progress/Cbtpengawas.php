<?php

class Cbtpengawas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth');
        }
        if (!$this->ion_auth->is_admin()) {
            show_error('Hanya Administrator yang diberi hak untuk mengakses halaman ini, <a href="' . base_url('dashboard') . '">Kembali ke menu awal</a>', 403, 'Akses Terlarang');
        }
        $this->load->library(['datatables', 'form_validation']);
        $this->load->model('Master_model', 'master');
        $this->load->model('Dashboard_model', 'dashboard');
        $this->load->model('Cbt_model', 'cbt');
        $this->load->model('Dropdown_model', 'dropdown');
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
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();

        $id_jenis = $this->cbt->getDistinctJenisJadwal($tp->id_tp, $smt->id_smt);
        $ids = [];
        foreach ($id_jenis as $jenis) {
            $ids[] = $jenis->id_jenis;
        }

        $jenis_selected = $this->input->get('jenis', true);
        $tglJadwals = [];
        if ($jenis_selected !== null) {
            $tglJadwals = $this->cbt->getAllJadwalByJenis($jenis_selected, $tp->id_tp, $smt->id_smt);
            foreach ($tglJadwals as $tgl => $jadwalss) {
                foreach ($jadwalss as $mpl => $jadwals) {
                    foreach ($jadwals as $jadwal) {
                        $jadwal->bank_kelas = unserialize($jadwal->bank_kelas ?? '');
                        foreach ($jadwal->bank_kelas as $kb) {
                            if ($kb['kelas_id'] != '') {
                                $jadwal->peserta[] = $this->cbt->getKelasUjian($kb['kelas_id']);
                            }
                        }
                    }
                }
            }
        }

        $data = [
            'user'           => $user,
            'judul'          => 'Atur Pengawas',
            'subjudul'       => 'Pengawas Ujian/Ulangan',
            'setting'        => $this->dashboard->getSetting(),
            'profile'        => $this->dashboard->getProfileAdmin($user->id),
            'tp'             => $this->dashboard->getTahun(),
            'tp_active'      => $tp,
            'smt'            => $this->dashboard->getSemester(),
            'smt_active'     => $smt,
            'kelas'          => $this->dropdown->getAllKelas($tp->id_tp, $smt->id_smt),
            'gurus'          => $this->dropdown->getAllGuru(),
            'jenis'          => count($ids) > 0 ? $ids : ['' => 'belum ada jadwal ujian'],
            'jenis_selected' => $jenis_selected,
            'tgl_jadwals'    => $tglJadwals,
            'ruang'          => $this->dropdown->getAllRuang(),
            'sesi'           => $this->dropdown->getAllSesi(),
            'ruang_sesi'     => $this->cbt->getRuangSesi($tp->id_tp, $smt->id_smt),
            'ruangs'         => $this->cbt->getDistinctRuang($tp->id_tp, $smt->id_smt, []),
            'pengawas'       => $this->cbt->getAllPengawas($tp->id_tp, $smt->id_smt),
        ];

        $this->load->view('_templates/dashboard/_header', $data);
        $this->load->view('cbt/pengawas/data');
        $this->load->view('_templates/dashboard/_footer');
    }

    public function savePengawas()
    {
        $input = json_decode($this->input->post('data', true));
        $tp = $this->dashboard->getTahunActive();
        $smt = $this->dashboard->getSemesterActive();
        $updated = 0;
        foreach ($input as $d) {
            $id_pengawas = $tp->id_tp . $smt->id_smt . $d->jadwal . $d->ruang . $d->sesi;
            $dataInsert = [
                'id_pengawas' => $id_pengawas,
                'id_jadwal'   => $d->jadwal,
                'id_tp'       => $tp->id_tp,
                'id_smt'      => $smt->id_smt,
                'id_ruang'    => $d->ruang,
                'id_sesi'     => $d->sesi,
                'id_guru'     => implode(',', $d->guru),
            ];
            if ($this->db->replace('cbt_pengawas', $dataInsert)) {
                $updated++;
            }
        }
        $this->output_json(['error' => '--', 'status' => $updated]);
    }
}
