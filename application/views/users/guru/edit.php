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
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .g-card-header h6 {
        margin: 0;
        font-size: .88rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .g-card-body {
        padding: 1.5rem;
    }

    /* ── Input group row ── */
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

    .ig input:disabled {
        opacity: .35;
        cursor: not-allowed;
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

    .btn-g.primary {
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border-color: transparent;
        color: #fff;
    }

    .btn-g.primary:hover {
        filter: brightness(1.1);
    }

    .btn-g.success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: transparent;
        color: #fff;
    }

    .btn-g.success:hover {
        filter: brightness(1.1);
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

    .btn-g:disabled {
        opacity: .4;
        cursor: not-allowed;
        filter: none;
    }

    .action-row {
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
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
                        <a href="<?= base_url('userguru') ?>" class="btn-g danger">
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

                <?php if ($user->username === $guru->username || $this->ion_auth->is_admin()): ?>
                    <?php $disabled = is_null($users) ? 'disabled' : ''; ?>

                    <div class="col-md-6 mb-4">
                        <div class="g-card">
                            <div class="g-card-header">
                                <?php if (is_null($users)): ?>
                                    <h6><?= $guru->nama_guru ?> belum aktif</h6>
                                    <button type="button" id="btn-aktif" class="btn-g success">Aktifkan</button>
                                <?php else: ?>
                                    <h6>Edit Login <?= $guru->nama_guru ?></h6>
                                    <?php if ($this->ion_auth->is_admin()): ?>
                                        <button type="button" id="btn-nonaktif" class="btn-g danger">Nonaktifkan</button>
                                    <?php endif ?>
                                <?php endif ?>
                            </div>
                            <?= form_open('userguru/editlogin', ['id' => 'change_password'], ['id' => $guru->id]) ?>
                            <div class="g-card-body">
                                <?php
                                $fields = [
                                    ['label' => 'Nama Depan',    'name' => 'first_name', 'val' => is_null($users) ? '' : $users->first_name],
                                    ['label' => 'Nama Belakang', 'name' => 'last_name',  'val' => is_null($users) ? '' : $users->last_name],
                                    ['label' => 'Email',         'name' => 'email',      'val' => is_null($users) ? '' : $guru->email, 'type' => 'email'],
                                ];
                                foreach ($fields as $f): ?>
                                    <div class="ig">
                                        <span class="ig-label"><?= $f['label'] ?></span>
                                        <input type="<?= $f['type'] ?? 'text' ?>" name="<?= $f['name'] ?>"
                                            value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                            <?= $disabled ?> required>
                                    </div>
                                <?php endforeach ?>
                                <div class="ig">
                                    <span class="ig-label">Username</span>
                                    <input type="text" value="<?= $guru->username ?>" placeholder="Username" disabled>
                                </div>
                                <div class="ig">
                                    <span class="ig-label">Password</span>
                                    <input type="password" value="<?= $guru->password ?>" placeholder="Password" disabled>
                                </div>
                                <div class="action-row">
                                    <button type="submit" id="btn-level" class="btn-g primary" <?= $disabled ?>>Simpan</button>
                                </div>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="g-card">
                            <div class="g-card-header">
                                <h6>Ubah Password &amp; Username</h6>
                            </div>
                            <div class="g-card-body">
                                <?php
                                $pwFields = [
                                    ['label' => 'Username',            'name' => 'username',    'type' => 'text',     'val' => is_null($users) ? '' : $users->username],
                                    ['label' => 'Password Lama',       'name' => 'old',         'type' => 'password', 'val' => ''],
                                    ['label' => 'Password Baru',       'name' => 'new',         'type' => 'password', 'val' => ''],
                                    ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'password', 'val' => ''],
                                ];
                                foreach ($pwFields as $f): ?>
                                    <div class="ig">
                                        <span class="ig-label"><?= $f['label'] ?></span>
                                        <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>"
                                            value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                            <?= $disabled ?> required>
                                    </div>
                                <?php endforeach ?>
                                <div class="action-row">
                                    <button type="reset" class="btn-g" <?= $disabled ?>>
                                        <i class="fa fa-rotate-left"></i> Reset
                                    </button>
                                    <button type="submit" id="btn-pass" class="btn-g warning" <?= $disabled ?>>
                                        Ganti Password
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endif ?>
            </div>
        </div>
    </section>
</div>

<script>
    var guru_id = '<?= $guru->id_guru ?>';
</script>
<script src="<?= base_url() ?>/assets/app/js/users/guru/edit.js"></script>

<?php if ($user->id === $guru->id_guru): ?>
    <script>
        $(function() {
            $('form#change_password').on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var btn = $('#btn-pass');
                btn.attr('disabled', true).text('Process…');
                submitajax($(this).attr('action'), $(this).serialize(), 'Password anda berhasil diganti', btn);
            });
        });
    </script>
<?php endif ?>
