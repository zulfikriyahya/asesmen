{{FILE: users/guru/edit.php}}
<div class="content-wrapper bg-dark pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-6">
                    <h1><?= $judul ?></h1>
                </div>
                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-6 text-right">
                        <a href="<?= base_url('userguru') ?>" class="btn btn-sm btn-danger">
                            <i class="fas fa-arrow-circle-left"></i>
                            <span class="d-none d-sm-inline-block ml-1">Kembali</span>
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

                    <!-- ── Login Info ── -->
                    <div class="col-md-6 mb-3">
                        <div class="card my-shadow">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <?php if (is_null($users)): ?>
                                    <h6 class="card-title mb-0"><?= $guru->nama_guru ?> belum aktif</h6>
                                    <button type="button" id="btn-aktif" class="btn btn-success btn-sm">Aktifkan</button>
                                <?php else: ?>
                                    <h6 class="card-title mb-0">Edit Login <?= $guru->nama_guru ?></h6>
                                    <?php if ($this->ion_auth->is_admin()): ?>
                                        <button type="button" id="btn-nonaktif" class="btn btn-danger btn-sm">Nonaktifkan</button>
                                    <?php endif ?>
                                <?php endif ?>
                            </div>
                            <?= form_open('userguru/editlogin', ['id' => 'change_password'], ['id' => $guru->id]) ?>
                            <div class="card-body">
                                <?php
                                $disabled = is_null($users) ? 'disabled' : '';
                                $fields = [
                                    ['label' => 'Nama Depan',    'name' => 'first_name', 'val' => is_null($users) ? '' : $users->first_name],
                                    ['label' => 'Nama Belakang', 'name' => 'last_name',  'val' => is_null($users) ? '' : $users->last_name],
                                    ['label' => 'Email',         'name' => 'email',      'val' => is_null($users) ? '' : $guru->email, 'type' => 'email'],
                                ];
                                foreach ($fields as $f):
                                ?>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend w-40">
                                            <span class="input-group-text"><?= $f['label'] ?></span>
                                        </div>
                                        <input type="<?= $f['type'] ?? 'text' ?>" name="<?= $f['name'] ?>"
                                            class="form-control" value="<?= $f['val'] ?>"
                                            placeholder="<?= $f['label'] ?>" <?= $disabled ?> required>
                                    </div>
                                <?php endforeach ?>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-40">
                                        <span class="input-group-text">Username</span>
                                    </div>
                                    <input type="text" class="form-control" value="<?= $guru->username ?>" placeholder="Username" disabled>
                                </div>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-40">
                                        <span class="input-group-text">Password</span>
                                    </div>
                                    <input type="password" class="form-control" value="<?= $guru->password ?>" placeholder="Password" disabled>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" id="btn-level" class="btn btn-primary" <?= $disabled ?>>Simpan</button>
                                </div>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>

                    <!-- ── Ubah Password ── -->
                    <div class="col-md-6 mb-3">
                        <div class="card card-warning my-shadow">
                            <div class="card-header with-border">
                                <h3 class="card-title">Ubah Password &amp; Username</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                $pwFields = [
                                    ['label' => 'Username',            'name' => 'username',    'type' => 'text',     'val' => is_null($users) ? '' : $users->username],
                                    ['label' => 'Password Lama',       'name' => 'old',         'type' => 'password', 'val' => ''],
                                    ['label' => 'Password Baru',       'name' => 'new',         'type' => 'password', 'val' => ''],
                                    ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'password', 'val' => ''],
                                ];
                                foreach ($pwFields as $f):
                                ?>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend w-40">
                                            <span class="input-group-text"><?= $f['label'] ?></span>
                                        </div>
                                        <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>" class="form-control"
                                            value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                            <?= $disabled ?> required>
                                    </div>
                                <?php endforeach ?>
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-default mr-2" <?= $disabled ?>>
                                        <i class="fa fa-rotate-left"></i> Reset
                                    </button>
                                    <button type="submit" id="btn-pass" class="btn btn-warning" <?= $disabled ?>>
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

