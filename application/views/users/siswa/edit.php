<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

    :root {
        --bg-base: #080e1a;
        --bg-mid: #0d1526;
        --bg-top: #0a1929;
        --glass-bg: rgba(255, 255, 255, 0.04);
        --glass-hover: rgba(255, 255, 255, 0.08);
        --glass-border: rgba(99, 179, 237, 0.15);
        --accent: #22d3ee;
        --accent2: #3b82f6;
        --text-1: #e2f0ff;
        --text-2: #7eb8d4;
        --radius: 14px;
        --radius-sm: 8px;
        --shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    }

    *,
    *::before,
    *::after {
        font-family: 'Lexend', sans-serif !important;
        box-sizing: border-box;
    }

    .page-wrap {
        background: linear-gradient(140deg, var(--bg-base) 0%, var(--bg-mid) 55%, var(--bg-top) 100%);
        min-height: 100vh;
        padding: 2rem 0 3rem;
    }

    .page-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--text-1);
        letter-spacing: -.01em;
        margin: 0;
    }

    .g-card {
        background: var(--glass-bg);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .g-card-header {
        background: linear-gradient(135deg, rgba(34, 211, 238, 0.12), rgba(59, 130, 246, 0.08));
        border-bottom: 1px solid var(--glass-border);
        padding: .9rem 1.5rem;
    }

    .g-card-header h3 {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .g-card-body {
        padding: 1.5rem;
    }

    .profile-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2rem 1.5rem;
        gap: .6rem;
    }

    .profile-box img {
        border: 3px solid rgba(34, 211, 238, 0.3);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4), 0 0 20px rgba(34, 211, 238, 0.1);
        border-radius: 50%;
    }

    .profile-box h4 {
        color: var(--text-1);
        font-weight: 600;
        font-size: .95rem;
        margin: .35rem 0 0;
        text-align: center;
    }

    .profile-box small {
        color: var(--text-2);
        font-size: .78rem;
    }

    .ig {
        display: flex;
        border-radius: var(--radius-sm);
        overflow: hidden;
        border: 1px solid var(--glass-border);
        margin-bottom: .85rem;
    }

    .ig:last-child {
        margin-bottom: 0;
    }

    .ig-label {
        background: rgba(34, 211, 238, 0.07);
        border-right: 1px solid var(--glass-border);
        padding: .52rem 1rem;
        font-size: .78rem;
        color: var(--text-2);
        white-space: nowrap;
        display: flex;
        align-items: center;
        min-width: 42%;
    }

    .ig input {
        flex: 1;
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: var(--text-1);
        padding: .52rem .85rem;
        font-size: .83rem;
    }

    .ig input:focus {
        outline: none;
        background: rgba(34, 211, 238, 0.06);
    }

    .ig input::placeholder {
        color: var(--text-2);
        opacity: .55;
    }

    .btn-g {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .42rem .95rem;
        border-radius: var(--radius-sm);
        font-size: .8rem;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        color: var(--text-1);
        transition: background .2s, filter .2s;
        white-space: nowrap;
    }

    .btn-g:hover {
        background: var(--glass-hover);
        color: #fff;
    }

    .btn-g.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
    }

    .btn-g.danger:hover {
        filter: brightness(1.1);
    }

    .btn-g.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-color: transparent;
        color: #fff;
    }

    .btn-g.warning:hover {
        filter: brightness(1.1);
    }

    .action-row {
        display: flex;
        justify-content: flex-end;
        margin-top: 1.25rem;
    }
</style>

<div class="page-wrap content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-6">
                    <h1 class="page-title"><?= $judul ?></h1>
                </div>
                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-6 text-right">
                        <a href="<?= base_url('usersiswa') ?>" class="btn-g danger">
                            <i class="fas fa-arrow-circle-left"></i>
                            <span class="d-none d-sm-inline">Kembali</span>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-3 mb-4">
                    <div class="g-card">
                        <div class="g-card-header">
                            <h3>Detail Data Siswa</h3>
                        </div>
                        <div class="profile-box">
                            <?php
                            if ($siswa->foto === 'assets/img/siswa.png') {
                                $img = $siswa->jenis_kelamin === 'L' ? 'siswa-l.png' : 'siswa-p.png';
                                $src = base_url() . 'assets/img/' . $img;
                            } else {
                                $src = base_url() . 'assets/img/' . $siswa->foto;
                            }
                            ?>
                            <img src="<?= $src ?>" alt="Foto Siswa" style="width:80px;height:80px;object-fit:cover;">
                            <h4><?= $siswa->nama ?></h4>
                            <small>Kelas <?= $siswa->nama_kelas ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 mb-4">
                    <div class="g-card">
                        <div class="g-card-header">
                            <h3>Ubah Password &amp; Username</h3>
                        </div>
                        <div class="g-card-body">
                            <?= $this->session->flashdata('editsiswa') ?>
                            <?= form_open('usersiswa/update', ['id' => 'editsiswa'], ['method' => 'edit', 'id_siswa' => $siswa->id_siswa]) ?>
                            <?php
                            $pwFields = [
                                ['label' => 'Username',            'name' => 'username',    'type' => 'text', 'val' => $siswa->username],
                                ['label' => 'Password Lama',       'name' => 'old',         'type' => 'text', 'val' => $siswa->password],
                                ['label' => 'Password Baru',       'name' => 'new',         'type' => 'text', 'val' => ''],
                                ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'text', 'val' => ''],
                            ];
                            foreach ($pwFields as $f): ?>
                                <div class="ig">
                                    <span class="ig-label"><?= $f['label'] ?></span>
                                    <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>"
                                        value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                        <?= in_array($f['name'], ['username', 'old', 'new_confirm']) ? 'required' : '' ?>>
                                </div>
                            <?php endforeach ?>
                            <div class="action-row">
                                <button type="submit" id="btn-pass" class="btn-g warning">Ganti Password</button>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
    $(function() {
        $('#flashdata').fadeTo(10000, 500).slideUp(500, function() {
            $(this).slideUp(500);
        });
    });
</script>
