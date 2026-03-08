<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/users.css">

<div class="content-wrapper">
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
