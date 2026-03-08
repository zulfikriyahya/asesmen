<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/users.css">

<div class="content-wrapper">
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
                        <?= form_close() ?>
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
