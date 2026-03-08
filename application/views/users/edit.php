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
        margin-bottom: 1.25rem;
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

    .g-card-footer {
        border-top: 1px solid var(--glass-border);
        padding: .9rem 1.5rem;
        background: rgba(0, 0, 0, 0.15);
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }

    .f-group {
        margin-bottom: 1.1rem;
    }

    .f-group.last {
        margin-bottom: 0;
    }

    .f-group label {
        display: block;
        font-size: .76rem;
        font-weight: 500;
        color: var(--text-2);
        margin-bottom: .35rem;
    }

    .f-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-1);
        padding: .5rem .85rem;
        font-size: .84rem;
        transition: border-color .2s, background .2s;
    }

    .f-input:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(34, 211, 238, 0.06);
    }

    .f-input::placeholder {
        color: var(--text-2);
        opacity: .6;
    }

    .f-select {
        width: 100% !important;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-1);
        padding: .5rem .85rem;
        font-size: .84rem;
    }

    .help-block {
        font-size: .72rem;
        color: #f87171;
        margin-top: .2rem;
        display: block;
    }

    .radio-label {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .84rem;
        color: var(--text-1);
        cursor: pointer;
        margin-right: 1.25rem;
    }

    .radio-label input[type="radio"] {
        accent-color: var(--accent);
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
</style>

<div class="page-wrap content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-sm-6">
                    <h1 class="page-title">Edit User</h1>
                </div>
                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-sm-6 text-right">
                        <a href="<?= base_url('users') ?>" class="btn-g">
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
                        <div class="g-card">
                            <div class="g-card-header">
                                <h3>Data User</h3>
                            </div>
                            <div class="g-card-body">
                                <div class="f-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="f-input" value="<?= $users->username ?>">
                                    <small class="help-block"></small>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="f-group">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="f-input" value="<?= $users->first_name ?>">
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="f-group">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="f-input" value="<?= $users->last_name ?>">
                                            <small class="help-block"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="f-group last">
                                    <label>Email</label>
                                    <input type="email" name="email" class="f-input" value="<?= $users->email ?>">
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="g-card-footer">
                                <button type="submit" id="btn-info" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id !== $users->id): ?>
                    <div class="col-sm-4 mb-4">
                        <?= form_open('users/edit_level', ['id' => 'user_level'], ['id' => $users->id]) ?>
                        <div class="g-card">
                            <div class="g-card-header">
                                <h3>Level</h3>
                            </div>
                            <div class="g-card-body">
                                <div class="f-group last">
                                    <label>Level User</label>
                                    <select id="level" name="level" class="f-select select2">
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
                            <div class="g-card-footer">
                                <button type="submit" id="btn-level" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>

                        <?= form_open('users/edit_status', ['id' => 'user_status'], ['id' => $users->id]) ?>
                        <div class="g-card">
                            <div class="g-card-header">
                                <h3>Status</h3>
                            </div>
                            <div class="g-card-body">
                                <div class="f-group last">
                                    <label class="radio-label">
                                        <input type="radio" name="status" value="1" <?= $users->active === '1' ? 'checked' : '' ?>> Aktif
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="status" value="0" <?= $users->active === '0' ? 'checked' : '' ?>> Tidak Aktif
                                    </label>
                                    <small class="help-block d-block mt-1"></small>
                                </div>
                            </div>
                            <div class="g-card-footer">
                                <button type="submit" id="btn-status" class="btn-g success">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id === $users->id): ?>
                    <div class="col-sm-4 mb-4">
                        <?= form_open('users/change_password', ['id' => 'change_password'], ['id' => $users->id]) ?>
                        <div class="g-card">
                            <div class="g-card-header">
                                <h3>Ubah Password</h3>
                            </div>
                            <div class="g-card-body">
                                <?php foreach (
                                    [
                                        ['label' => 'Password Lama',       'name' => 'old'],
                                        ['label' => 'Password Baru',       'name' => 'new'],
                                        ['label' => 'Konfirmasi Password', 'name' => 'new_confirm'],
                                    ] as $i => $f
                                ): ?>
                                    <div class="f-group <?= $i === 2 ? 'last' : '' ?>">
                                        <label><?= $f['label'] ?></label>
                                        <input type="password" name="<?= $f['name'] ?>" class="f-input" placeholder="<?= $f['label'] ?>">
                                        <small class="help-block"></small>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="g-card-footer">
                                <button type="reset" class="btn-g">
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
