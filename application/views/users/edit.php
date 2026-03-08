{{FILE: users/edit.php}}
<div class="content-wrapper bg-dark pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-sm-6">
                    <h1 class="text-bold">Edit User</h1>
                </div>
                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-sm-6 text-right">
                        <a href="<?= base_url('users') ?>" class="btn btn-sm btn-default">
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
                    <!-- ── Data User ── -->
                    <div class="col-sm-4 mb-3">
                        <?= form_open('users/edit_info', ['id' => 'user_info'], ['id' => $users->id]) ?>
                        <div class="card card-info my-shadow">
                            <div class="card-header with-border">
                                <h3 class="card-title">Data User</h3>
                            </div>
                            <div class="card-body pb-0">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= $users->username ?>">
                                    <small class="help-block"></small>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label>First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="<?= $users->first_name ?>">
                                        <small class="help-block"></small>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label>Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="<?= $users->last_name ?>">
                                        <small class="help-block"></small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= $users->email ?>">
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="btn-info" class="btn btn-info">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id !== $users->id): ?>
                    <!-- ── Level & Status ── -->
                    <div class="col-sm-4 mb-3">
                        <?= form_open('users/edit_level', ['id' => 'user_level'], ['id' => $users->id]) ?>
                        <div class="card card-primary my-shadow">
                            <div class="card-header with-border">
                                <h3 class="card-title">Level</h3>
                            </div>
                            <div class="card-body pb-0">
                                <div class="form-group">
                                    <label>Level User</label>
                                    <select id="level" name="level" class="form-control select2" style="width:100%!important">
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
                            <div class="card-footer">
                                <button type="submit" id="btn-level" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>

                        <?= form_open('users/edit_status', ['id' => 'user_status'], ['id' => $users->id]) ?>
                        <div class="card card-success my-shadow mt-3">
                            <div class="card-header with-border">
                                <h3 class="card-title">Status</h3>
                            </div>
                            <div class="card-body pb-0">
                                <div class="form-group">
                                    <label class="mr-3">
                                        <input type="radio" name="status" value="1" <?= $users->active === '1' ? 'checked' : '' ?>>
                                        Aktif
                                    </label>
                                    <label>
                                        <input type="radio" name="status" value="0" <?= $users->active === '0' ? 'checked' : '' ?>>
                                        Tidak Aktif
                                    </label>
                                    <small class="help-block"></small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" id="btn-status" class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id === $users->id): ?>
                    <!-- ── Ubah Password ── -->
                    <div class="col-sm-4 mb-3">
                        <?= form_open('users/change_password', ['id' => 'change_password'], ['id' => $users->id]) ?>
                        <div class="card card-warning my-shadow">
                            <div class="card-header with-border">
                                <h3 class="card-title">Ubah Password</h3>
                            </div>
                            <div class="card-body pb-0">
                                <?php foreach (
                                    [
                                        ['label' => 'Password Lama',        'name' => 'old'],
                                        ['label' => 'Password Baru',         'name' => 'new'],
                                        ['label' => 'Konfirmasi Password',   'name' => 'new_confirm'],
                                    ] as $f
                                ): ?>
                                    <div class="form-group">
                                        <label><?= $f['label'] ?></label>
                                        <input type="password" name="<?= $f['name'] ?>" class="form-control"
                                            placeholder="<?= $f['label'] ?>">
                                        <small class="help-block"></small>
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="card-footer d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-default mr-2">
                                    <i class="fa fa-rotate-left"></i> Reset
                                </button>
                                <button type="submit" id="btn-pass" class="btn btn-warning">Ganti Password</button>
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
