<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/users.css">

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row align-items-center mb-4">
                <div class="col-6">
                    <h1 class="page-title"><?= $judul ?></h1>
                </div>
                <div class="col-6 text-right">
                    <a href="<?= base_url('useradmin') ?>" class="btn-g danger">
                        <i class="fas fa-arrow-circle-left"></i>
                        <span class="d-none d-sm-inline">Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-md-6 mb-4">
                        <?= form_open('users/edit_info', ['id' => 'user_info'], ['id' => $users->id]) ?>
                        <div class="g-card">
                            <div class="g-card-header">
                                <h3><?= $subjudul ?></h3>
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

                <?php if ($user->id === $users->id || $this->ion_auth->is_admin()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="g-card">
                            <div class="g-card-header">
                                <h3>Foto Profile</h3>
                            </div>
                            <div class="g-card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <?= form_open_multipart('', ['id' => 'set-foto']) ?>
                                        <div class="f-group last">
                                            <label>Foto</label>
                                            <input type="file" id="foto" name="foto" class="dropify"
                                                data-max-file-size-preview="2M"
                                                data-allowed-file-extensions="jpg jpeg png"
                                                data-default-file="<?= base_url() . $profile->foto ?>">
                                        </div>
                                        <?= form_close() ?>
                                    </div>
                                    <div class="col-7">
                                        <div class="f-group">
                                            <label>Nama Lengkap</label>
                                            <input type="text" id="nama-lengkap" class="f-input" placeholder="Nama Lengkap" value="<?= $profile->nama_lengkap ?>">
                                        </div>
                                        <div class="f-group last">
                                            <label>Jabatan</label>
                                            <input type="text" id="jabatan" class="f-input" placeholder="Jabatan" value="<?= $profile->jabatan ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="g-card-footer">
                                <button onclick="simpanProfile()" id="simpan" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <?= form_open('useradmin/change_password', ['id' => 'change_password'], ['id' => $users->id]) ?>
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
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="g-card-footer">
                                <button type="reset" class="btn-g">
                                    <i class="fa fa-rotate-left"></i> Reset
                                </button>
                                <button type="submit" id="btn-pass" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

            </div>
        </div>
    </section>
</div>

<script>
    $(function() {
        function submitajax(url, data, msg) {
            swal.fire({
                text: 'Silahkan tunggu…',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });
            $.ajax({
                url,
                data,
                type: 'POST',
                success: function(r) {
                    if (r.status) {
                        swal.fire({
                                title: 'Sukses',
                                text: msg,
                                icon: 'success',
                                confirmButtonColor: '#3085d6'
                            })
                            .then(res => {
                                if (res.value) location.href = base_url + 'logout';
                            });
                    } else {
                        var txt = r.errors ? 'Gagal edit admin' : r.msg ? 'Password lama tidak benar' : 'Terjadi kesalahan';
                        swal.fire({
                            title: 'Gagal',
                            text: txt,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    swal.fire({
                        title: 'Error',
                        text: JSON.parse(xhr.responseText).Message,
                        icon: 'error'
                    });
                }
            });
        }

        [{
                id: 'change_password',
                btn: '#btn-pass',
                msg: 'Password anda berhasil diganti'
            },
            {
                id: 'user_info',
                btn: '#btn-info',
                msg: 'Informasi user berhasil diupdate'
            },
            {
                id: 'user_level',
                btn: '#btn-level',
                msg: 'Level user berhasil diupdate'
            },
            {
                id: 'user_status',
                btn: '#btn-status',
                msg: 'Status user berhasil diupdate'
            },
        ].forEach(function(f) {
            $('form#' + f.id).on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $(f.btn).attr('disabled', true).text('Process…');
                submitajax($(this).attr('action'), $(this).serialize(), f.msg);
            });
        });

        $('form input, form select').on('change', function() {
            $(this).closest('.f-group').find('.help-block').text('');
        });
    });
</script>

<?php if ($user->id === $users->id): ?>
    <script>
        var idUser = '<?= $user->id ?>';
        var fprofil = '<?= base_url() . $profile->foto ?>';

        $(function() {
            ajaxcsrf();

            var drEvent = $('.dropify').dropify({
                messages: {
                    default: 'Seret logo kesini atau klik',
                    replace: 'Seret atau klik untuk mengganti',
                    remove: 'Hapus',
                    error: 'Ada kesalahan!'
                },
                error: {
                    fileSize: 'File terlalu besar (maks {{ value }}).',
                    imageFormat: 'Format tidak diizinkan ({{ value }} saja).'
                }
            });

            drEvent.on('dropify.afterClear', function(e) {
                deleteImage($(e.currentTarget).data('default-file'));
            });
            drEvent.on('dropify.errors', function() {
                $.toast({
                    heading: 'Error',
                    text: 'File rusak',
                    icon: 'warning',
                    showHideTransition: 'fade',
                    hideAfter: 5000,
                    position: 'top-right'
                });
            });

            $('#foto').on('change', function() {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#prev-logo-kanan').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                    uploadAttach(base_url + 'useradmin/uploadfile/' + idUser, new FormData($('#set-foto')[0]));
                }
            });

            function uploadAttach(action, data) {
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function(d) {
                        fprofil = d.src;
                    },
                    error: function(xhr) {
                        swal.fire({
                            title: 'Error',
                            text: JSON.parse(xhr.responseText).Message,
                            icon: 'error'
                        });
                    }
                });
            }

            function deleteImage(src) {
                $.ajax({
                    data: {
                        src
                    },
                    type: 'POST',
                    url: base_url + 'useradmin/deletefile',
                    cache: false,
                    success: function() {
                        fprofil = '';
                    }
                });
            }
        });

        function simpanProfile() {
            swal.fire({
                text: 'Silahkan tunggu…',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });
            $.ajax({
                data: {
                    foto: fprofil,
                    nama_lengkap: $('#nama-lengkap').val(),
                    jabatan: $('#jabatan').val()
                },
                type: 'POST',
                url: base_url + 'useradmin/saveprofile',
                success: function() {
                    swal.fire({
                        title: 'Sukses',
                        text: 'Profile berhasil disimpan',
                        icon: 'success'
                    });
                },
                error: function(xhr) {
                    swal.fire({
                        title: 'Error',
                        text: JSON.parse(xhr.responseText).Message,
                        icon: 'error'
                    });
                }
            });
        }
    </script>
<?php endif ?>
