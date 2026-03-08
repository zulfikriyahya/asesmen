{{FILE: users/siswa/edit.php}}
<div class="content-wrapper bg-dark pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-6">
                    <h1><?= $judul ?></h1>
                </div>
                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-6 text-right">
                        <a href="<?= base_url('usersiswa') ?>" class="btn btn-sm btn-danger">
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

                <!-- ── Detail Siswa ── -->
                <div class="col-md-3 mb-3">
                    <div class="card card-info my-shadow">
                        <div class="card-header with-border">
                            <h3 class="card-title">Detail Data Siswa</h3>
                        </div>
                        <div class="card-body text-center">
                            <?php
                            if ($siswa->foto === 'assets/img/siswa.png') {
                                $img = $siswa->jenis_kelamin === 'L' ? 'siswa-l.png' : 'siswa-p.png';
                                $src = base_url() . 'assets/img/' . $img;
                            } else {
                                $src = base_url() . 'assets/img/' . $siswa->foto;
                            }
                            ?>
                            <img src="<?= $src ?>" class="img-circle profile-avatar mt-2" alt="Foto Siswa"
                                style="width:80px;height:80px;object-fit:cover;">
                            <h4 class="mt-3 mb-0"><?= $siswa->nama ?></h4>
                            <small class="text-muted">Kelas <?= $siswa->nama_kelas ?></small>
                        </div>
                    </div>
                </div>

                <!-- ── Ubah Password ── -->
                <div class="col-md-9 mb-3">
                    <div class="card card-warning my-shadow">
                        <div class="card-header with-border">
                            <h3 class="card-title">Ubah Password &amp; Username</h3>
                        </div>
                        <div class="card-body">
                            <?= $this->session->flashdata('editsiswa') ?>
                            <?= form_open('usersiswa/update', ['id' => 'editsiswa'], ['method' => 'edit', 'id_siswa' => $siswa->id_siswa]) ?>

                            <?php
                            $pwFields = [
                                ['label' => 'Username',            'name' => 'username',    'type' => 'text',     'val' => $siswa->username],
                                ['label' => 'Password Lama',       'name' => 'old',         'type' => 'text',     'val' => $siswa->password],
                                ['label' => 'Password Baru',       'name' => 'new',         'type' => 'text',     'val' => ''],
                                ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'text',     'val' => ''],
                            ];
                            foreach ($pwFields as $f): ?>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend w-40">
                                        <span class="input-group-text"><?= $f['label'] ?></span>
                                    </div>
                                    <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>" class="form-control"
                                        value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                        <?= in_array($f['name'], ['username', 'old', 'new_confirm']) ? 'required' : '' ?>>
                                </div>
                            <?php endforeach ?>

                            <div class="d-flex justify-content-end">
                                <button type="submit" id="btn-pass" class="btn btn-warning">Ganti Password</button>
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
