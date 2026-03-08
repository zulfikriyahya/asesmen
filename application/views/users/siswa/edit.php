<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-6">
                    <h1 class="page-title"><?= $judul ?></h1>
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
                    <div class="g-card">
                        <div class="g-card-header">
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
                            <img src="<?= $src ?>" alt="Foto Siswa" style="width:80px;height:80px;object-fit:cover;">
                            <h4><?= $siswa->nama ?></h4>
                            <small>Kelas <?= $siswa->nama_kelas ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-md-9 mb-4">
                    <div class="g-card">
                        <div class="g-card-header">
                            <h3>Ubah Password &amp; Username</h3>
                        </div>
                        <div class="g-card-body">
                            <?= $this->session->flashdata('editsiswa') ?>
                            <?= form_open('usersiswa/update', ['id' => 'editsiswa'], ['method' => 'edit', 'id_siswa' => $siswa->id_siswa]) ?>
                            <?php
                            $pwFields = [
                                ['label' => 'Username',            'name' => 'username',    'type' => 'text', 'val' => $siswa->username],
                                ['label' => 'Password Lama',       'name' => 'old',         'type' => 'text', 'val' => $siswa->password],
                                ['label' => 'Password Baru',       'name' => 'new',         'type' => 'text', 'val' => ''],
                                ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'text', 'val' => ''],
                            ];
                            foreach ($pwFields as $f): ?>
                                <div class="ig">
                                    <span class="ig-label"><?= $f['label'] ?></span>
                                    <input type="<?= $f['type'] ?>" name="<?= $f['name'] ?>"
                                        value="<?= $f['val'] ?>" placeholder="<?= $f['label'] ?>"
                                        <?= in_array($f['name'], ['username', 'old', 'new_confirm']) ? 'required' : '' ?>>
                                </div>
                            <?php endforeach ?>
                            <div class="action-row">
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
