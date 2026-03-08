<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

    :root {
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        --accent: #f97316;
        --text-primary: #f1f5f9;
        --text-muted: #94a3b8;
        --radius: 14px;
        --radius-sm: 8px;
    }

    * {
        font-family: 'Lexend', sans-serif !important;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--glass-shadow);
        overflow: hidden;
    }

    .glass-card-header {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.25), rgba(249, 115, 22, 0.06));
        border-bottom: 1px solid var(--glass-border);
        padding: .85rem 1.25rem;
    }

    .glass-card-header h3 {
        margin: 0;
        font-size: .95rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .glass-card-body {
        padding: 1.25rem;
    }

    .profile-box {
        text-align: center;
        padding: 1.5rem 1rem;
    }

    .profile-box img {
        border: 3px solid var(--glass-border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }

    .profile-box h4 {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: .2rem;
    }

    .profile-box small {
        color: var(--text-muted);
        font-size: .8rem;
    }

    .input-group-glass {
        display: flex;
        margin-bottom: .85rem;
        border-radius: var(--radius-sm);
        overflow: hidden;
        border: 1px solid var(--glass-border);
    }

    .input-group-glass .prepend {
        background: rgba(255, 255, 255, 0.08);
        border-right: 1px solid var(--glass-border);
        padding: .5rem .85rem;
        font-size: .8rem;
        color: var(--text-muted);
        white-space: nowrap;
        display: flex;
        align-items: center;
        min-width: 40%;
    }

    .input-group-glass input {
        flex: 1;
        background: rgba(255, 255, 255, 0.05);
        border: none;
        color: var(--text-primary);
        padding: .5rem .8rem;
        font-size: .84rem;
        font-family: 'Lexend', sans-serif;
    }

    .input-group-glass input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.09);
    }

    .input-group-glass input::placeholder {
        color: var(--text-muted);
    }

    .btn-g {
        border-radius: var(--radius-sm);
        padding: .45rem 1rem;
        font-size: .82rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-family: 'Lexend', sans-serif;
    }

    .btn-g.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }

    .btn-g.warning:hover {
        filter: brightness(1.1);
    }

    .btn-g.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }

    .btn-g.danger:hover {
        filter: brightness(1.1);
    }
</style>

<div class="content-wrapper pt-4" style="background: linear-gradient(135deg, #0f0f19 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3 align-items-center">
                <div class="col-6">
                    <h1 style="font-weight:700;color:#f1f5f9;font-size:1.5rem;"><?= $judul ?></h1>
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
                    <div class="glass-card">
                        <div class="glass-card-header">
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
                            <img src="<?= $src ?>" class="img-circle mt-2" alt="Foto Siswa"
                                style="width:80px;height:80px;object-fit:cover;">
                            <h4 class="mt-3 mb-0"><?= $siswa->nama ?></h4>
                            <small>Kelas <?= $siswa->nama_kelas ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 mb-4">
                    <div class="glass-card">
                        <div class="glass-card-header">
                            <h3>Ubah Password &amp; Username</h3>
                        </div>
                        <div class="glass-card-body">
                            <?= $this->session->flashdata('editsiswa') ?>
                            <?= form_open('usersiswa/update', ['id' => 'editsiswa'], ['method' => 'edit', 'id_siswa' => $siswa->id_siswa]) ?>
                            <?php
                            $pwFields = [
                                ['label' => 'Username',            'name' => 'username',    'type' => 'text', 'val' => $siswa->username],
                                ['label' => 'Password Lama',       'name' => 'old',         'type' => 'text', 'val' => $siswa->password],
                                ['label' => 'Password Baru',       'name' => 'new',         'type' => 'text', 'val' => ''],
                                ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'text', 'val' => ''],
                            ];
                            foreach ($pwFields as $i => $f): ?>
                                <div class="input-group-glass" <?= $i === count($pwFields) - 1 ? 'style="margin-bottom:0"' : '' ?>>
                                    <span class="prepend"><?= $f['label'] ?></span>
                                    <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>"
                                        value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                        <?= in_array($f['name'], ['username', 'old', 'new_confirm']) ? 'required' : '' ?>>
                                </div>
                            <?php endforeach ?>
                            <div class="d-flex justify-content-end mt-3">
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
