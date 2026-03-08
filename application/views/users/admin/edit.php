{{FILE: users/admin/edit.php}}
<div class="content-wrapper bg-dark pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2 align-items-center">
                <div class="col-6">
                    <h1 class="text-bold"><?= $judul ?></h1>
                </div>
                <div class="col-6 text-right">
                    <a href="<?= base_url('useradmin') ?>" class="btn btn-sm btn-danger">
                        <i class="fas fa-arrow-circle-left"></i>
                        <span class="d-none d-sm-inline-block ml-1">Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <?php if ($this->ion_auth->is_admin()): ?>
                    <!-- ── Info User ── -->
                    <div class="col-md-6 mb-3">
                        <?= form_open('users/edit_info', ['id' => 'user_info'], ['id' => $users->id]) ?>
                        <div class="card card-info my-shadow">
                            <div class="card-header bg-orange">
                                <h3 class="card-title text-bold"><?= $subjudul ?></h3>
                            </div>
                            <div class="card-body text-dark pb-0">
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
                            <div class="card-footer text-dark">
                                <button type="submit" id="btn-info" class="btn btn-info float-right">Simpan</button>
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>
                <?php endif ?>

                <?php if ($user->id === $users->id || $this->ion_auth->is_admin()): ?>
                    <!-- ── Foto Profile ── -->
                    <div class="col-md-6 mb-3">
                        <div class="card card-primary my-shadow">
                            <div class="card-header bg-orange">
                                <h3 class="card-title text-bold">Foto Profile</h3>
                            </div>
                            <div class="card-body text-dark">
                                <div class="row">
                                    <div class="col-5">
                                        <?= form_open_multipart('', ['id' => 'set-foto']) ?>
                                        <div class="form-group">
                                            <label>Foto</label>
                                            <input type="file" id="foto" name="foto" class="dropify"
                                                data-max-file-size-preview="2M"
                                                data-allowed-file-extensions="jpg jpeg png"
                                                data-default-file="<?= base_url() . $profile->foto ?>">
                                        </div>
                                        <?= form_close() ?>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group">
                                            <label>Nama Lengkap</label>
                                            <input type="text" id="nama-lengkap" class="form-control"
                                                placeholder="Nama Lengkap" value="<?= $profile->nama_lengkap ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Jabatan</label>
                                            <input type="text" id="jabatan" class="form-control"
                                                placeholder="Jabatan" value="<?= $profile->jabatan ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-dark">
                                <button onclick="simpanProfile()" id="simpan" class="btn btn-info float-right">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <!-- ── Ubah Password ── -->
                    <div class="col-md-6 mb-3">
                        <?= form_open('useradmin/change_password', ['id' => 'change_password'], ['id' => $users->id]) ?>
                        <div class="card card-warning my-shadow">
                            <div class="card-header bg-orange">
                                <h3 class="card-title text-bold">Ubah Password</h3>
                            </div>
                            <div class="card-body text-dark pb-0">
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
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="card-footer text-dark d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-default mr-2">
                                    <i class="fa fa-rotate-left"></i> Reset
                                </button>
                                <button type="submit" id="btn-pass" class="btn btn-info">Simpan</button>
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

        var forms = [{
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
        ];

        forms.forEach(function(f) {
            $('form#' + f.id).on('submit', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                $(f.btn).attr('disabled', true).text('Process…');
                submitajax($(this).attr('action'), $(this).serialize(), f.msg);
            });
        });

        $('form input, form select').on('change', function() {
            $(this).closest('.form-group').removeClass('has-error');
            $(this).nextAll('.help-block').eq(0).text('');
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
                    replace: 'Seret atau klik untuk mengganti logo',
                    remove: 'Hapus',
                    error: 'Ooops, ada kesalahan!!'
                },
                error: {
                    fileSize: 'File terlalu besar (maks {{ value }}).',
                    imageFormat: 'Format tidak diizinkan ({{ value }} saja).'
                }
            });

            drEvent.on('dropify.afterClear', function(e, el) {
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
                var input = this;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#prev-logo-kanan').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
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
