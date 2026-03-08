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

    .glass-card-footer {
        border-top: 1px solid var(--glass-border);
        padding: .85rem 1.25rem;
        background: rgba(0, 0, 0, 0.1);
    }

    .form-group-g {
        margin-bottom: 1rem;
    }

    .form-group-g label {
        font-size: .78rem;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: .3rem;
        display: block;
    }

    .input-g {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        padding: .5rem .8rem;
        width: 100%;
        font-size: .84rem;
        transition: border-color .2s, background .2s;
    }

    .input-g:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.1);
    }

    .input-g::placeholder {
        color: var(--text-muted);
    }

    .select-g {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        padding: .5rem .8rem;
        width: 100%;
        font-size: .84rem;
    }

    .help-block {
        font-size: .75rem;
        color: #ef4444;
    }

    .radio-label {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .84rem;
        color: var(--text-primary);
        cursor: pointer;
        margin-right: 1rem;
    }

    .radio-label input[type="radio"] {
        accent-color: var(--accent);
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

    .btn-g.ghost {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
    }

    .btn-g.ghost:hover {
        background: rgba(255, 255, 255, 0.1);
    }

    .btn-g.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }

    .btn-g.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }

    .btn-g.warning:hover {
        filter: brightness(1.1);
    }
</style>

<div class="content-wrapper pt-4" style="background: linear-gradient(135deg, #0f0f19 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3 align-items-center">
                <div class="col-sm-6">
                    <h1 style="font-weight:700;color:#f1f5f9;font-size:1.5rem;">Edit User</h1>
                </div>
                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-sm-6 text-right">
                        <a href="<?= base_url('users') ?>" class="btn-g ghost">
                            <i class="fa fa-arrow-left"></i> Batal
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-sm-4 mb-4">
                        <?= form_open('users/edit_info', ['id' => 'user_info'], ['id' => $users->id]) ?>
                        <div class="glass-card">
                            <div class="glass-card-header">
                                <h3>Data User</h3>
                            </div>
                            <div class="glass-card-body">
                                <div class="form-group-g">
                                    <label>Username</label>
                                    <input type="text" name="username" class="input-g" value="<?= $users->username ?>">
                                    <small class="help-block"></small>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group-g">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="input-g" value="<?= $users->first_name ?>">
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group-g">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="input-g" value="<?= $users->last_name ?>">
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group-g mb-0">
                                    <label>Email</label>
                                    <input type="email" name="email" class="input-g" value="<?= $users->email ?>">
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="glass-card-footer d-flex justify-content-end">
                                <button type="submit" id="btn-info" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id !== $users->id): ?>
                    <div class="col-sm-4 mb-4">
                        <?= form_open('users/edit_level', ['id' => 'user_level'], ['id' => $users->id]) ?>
                        <div class="glass-card mb-4">
                            <div class="glass-card-header">
                                <h3>Level</h3>
                            </div>
                            <div class="glass-card-body">
                                <div class="form-group-g mb-0">
                                    <label>Level User</label>
                                    <select id="level" name="level" class="select-g select2" style="width:100%!important">
                                        <option value="">Pilih Level</option>
                                        <?php foreach ($groups as $row): ?>
                                            <option value="<?= $row->id ?>" <?= $level->id === $row->id ? 'selected' : '' ?>>
                                                <?= $row->name ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="glass-card-footer d-flex justify-content-end">
                                <button type="submit" id="btn-level" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>

                        <?= form_open('users/edit_status', ['id' => 'user_status'], ['id' => $users->id]) ?>
                        <div class="glass-card">
                            <div class="glass-card-header">
                                <h3>Status</h3>
                            </div>
                            <div class="glass-card-body">
                                <div class="form-group-g mb-0">
                                    <label class="radio-label">
                                        <input type="radio" name="status" value="1" <?= $users->active === '1' ? 'checked' : '' ?>> Aktif
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="status" value="0" <?= $users->active === '0' ? 'checked' : '' ?>> Tidak Aktif
                                    </label>
                                    <small class="help-block d-block"></small>
                                </div>
                            </div>
                            <div class="glass-card-footer d-flex justify-content-end">
                                <button type="submit" id="btn-status" class="btn-g success">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id === $users->id): ?>
                    <div class="col-sm-4 mb-4">
                        <?= form_open('users/change_password', ['id' => 'change_password'], ['id' => $users->id]) ?>
                        <div class="glass-card">
                            <div class="glass-card-header">
                                <h3>Ubah Password</h3>
                            </div>
                            <div class="glass-card-body">
                                <?php foreach (
                                    [
                                        ['label' => 'Password Lama',       'name' => 'old'],
                                        ['label' => 'Password Baru',       'name' => 'new'],
                                        ['label' => 'Konfirmasi Password', 'name' => 'new_confirm'],
                                    ] as $f
                                ): ?>
                                    <div class="form-group-g <?= $f['name'] === 'new_confirm' ? 'mb-0' : '' ?>">
                                        <label><?= $f['label'] ?></label>
                                        <input type="password" name="<?= $f['name'] ?>" class="input-g"
                                            placeholder="<?= $f['label'] ?>">
                                        <small class="help-block"></small>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="glass-card-footer d-flex justify-content-end gap-2">
                                <button type="reset" class="btn-g ghost">
                                    <i class="fa fa-rotate-left"></i> Reset
                                </button>
                                <button type="submit" id="btn-pass" class="btn-g warning">Ganti Password</button>
                            </div>
                        </div>
                    </div>
                <?php endif ?>

            </div>
        </div>
    </section>
</div>

<script src="<?= base_url() ?>assets/dist/js/app/users/edit.js"></script>

<?php if ($user->id === $users->id): ?>
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
