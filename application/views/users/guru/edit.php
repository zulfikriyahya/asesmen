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
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .glass-card-header h6 {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .glass-card-body {
        padding: 1.25rem;
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
        min-width: 38%;
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

    .input-group-glass input:disabled {
        opacity: .4;
        cursor: not-allowed;
    }

    .btn-g {
        border-radius: var(--radius-sm);
        padding: .4rem .9rem;
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

    .btn-g.primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
    }

    .btn-g.primary:hover {
        filter: brightness(1.1);
    }

    .btn-g.success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
    }

    .btn-g.success:hover {
        filter: brightness(1.1);
    }

    .btn-g.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }

    .btn-g.danger:hover {
        filter: brightness(1.1);
    }

    .btn-g.ghost {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
    }

    .btn-g.ghost:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .btn-g.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }

    .btn-g.warning:hover {
        filter: brightness(1.1);
    }

    .btn-g:disabled {
        opacity: .4;
        cursor: not-allowed;
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
                        <div class="glass-card">
                            <div class="glass-card-header">
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
                            <div class="glass-card-body">
                                <?php
                                $fields = [
                                    ['label' => 'Nama Depan',    'name' => 'first_name', 'val' => is_null($users) ? '' : $users->first_name],
                                    ['label' => 'Nama Belakang', 'name' => 'last_name',  'val' => is_null($users) ? '' : $users->last_name],
                                    ['label' => 'Email',         'name' => 'email',      'val' => is_null($users) ? '' : $guru->email, 'type' => 'email'],
                                ];
                                foreach ($fields as $f): ?>
                                    <div class="input-group-glass">
                                        <span class="prepend"><?= $f['label'] ?></span>
                                        <input type="<?= $f['type'] ?? 'text' ?>" name="<?= $f['name'] ?>"
                                            value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                            <?= $disabled ?> required>
                                    </div>
                                <?php endforeach ?>
                                <div class="input-group-glass">
                                    <span class="prepend">Username</span>
                                    <input type="text" value="<?= $guru->username ?>" placeholder="Username" disabled>
                                </div>
                                <div class="input-group-glass" style="margin-bottom:0">
                                    <span class="prepend">Password</span>
                                    <input type="password" value="<?= $guru->password ?>" placeholder="Password" disabled>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <button type="submit" id="btn-level" class="btn-g primary" <?= $disabled ?>>Simpan</button>
                                </div>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="glass-card">
                            <div class="glass-card-header">
                                <h6>Ubah Password &amp; Username</h6>
                            </div>
                            <div class="glass-card-body">
                                <?php
                                $pwFields = [
                                    ['label' => 'Username',            'name' => 'username',    'type' => 'text',     'val' => is_null($users) ? '' : $users->username],
                                    ['label' => 'Password Lama',       'name' => 'old',         'type' => 'password', 'val' => ''],
                                    ['label' => 'Password Baru',       'name' => 'new',         'type' => 'password', 'val' => ''],
                                    ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'password', 'val' => ''],
                                ];
                                foreach ($pwFields as $i => $f): ?>
                                    <div class="input-group-glass" <?= $i === count($pwFields) - 1 ? 'style="margin-bottom:0"' : '' ?>>
                                        <span class="prepend"><?= $f['label'] ?></span>
                                        <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>"
                                            value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                            <?= $disabled ?> required>
                                    </div>
                                <?php endforeach ?>
                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="reset" class="btn-g ghost" <?= $disabled ?>>
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
