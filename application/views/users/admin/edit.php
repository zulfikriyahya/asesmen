<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

    :root {
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        --accent: #f97316;
        --surface: rgba(15, 15, 25, 0.85);
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
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.25), rgba(249, 115, 22, 0.08));
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
        background: rgba(0, 0, 0, 0.15);
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
        font-size: .85rem;
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

    .help-block {
        font-size: .75rem;
        color: #ef4444;
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

    .btn-g.accent {
        background: linear-gradient(135deg, var(--accent), #ea6c0a);
        color: #fff;
    }

    .btn-g.accent:hover {
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

    .btn-g.danger:hover {
        filter: brightness(1.1);
    }

    .btn-g.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff;
    }

    .btn-g:disabled {
        opacity: .5;
        cursor: not-allowed;
    }
</style>

<div class="content-wrapper pt-4" style="background: linear-gradient(135deg, #0f0f19 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3 align-items-center">
                <div class="col-6">
                    <h1 style="font-weight:700;color:#f1f5f9;font-size:1.5rem;"><?= $judul ?></h1>
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
                        <div class="glass-card">
                            <div class="glass-card-header">
                                <h3><?= $subjudul ?></h3>
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

                <?php if ($user->id === $users->id || $this->ion_auth->is_admin()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="glass-card">
                            <div class="glass-card-header">
                                <h3>Foto Profile</h3>
                            </div>
                            <div class="glass-card-body">
                                <div class="row">
                                    <div class="col-5">
                                        <?= form_open_multipart('', ['id' => 'set-foto']) ?>
                                        <div class="form-group-g">
                                            <label>Foto</label>
                                            <input type="file" id="foto" name="foto" class="dropify"
                                                data-max-file-size-preview="2M"
                                                data-allowed-file-extensions="jpg jpeg png"
                                                data-default-file="<?= base_url() . $profile->foto ?>">
                                        </div>
                                        <?= form_close() ?>
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group-g">
                                            <label>Nama Lengkap</label>
                                            <input type="text" id="nama-lengkap" class="input-g"
                                                placeholder="Nama Lengkap" value="<?= $profile->nama_lengkap ?>">
                                        </div>
                                        <div class="form-group-g mb-0">
                                            <label>Jabatan</label>
                                            <input type="text" id="jabatan" class="input-g"
                                                placeholder="Jabatan" value="<?= $profile->jabatan ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="glass-card-footer d-flex justify-content-end">
                                <button onclick="simpanProfile()" id="simpan" class="btn-g primary">Simpan</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <?= form_open('useradmin/change_password', ['id' => 'change_password'], ['id' => $users->id]) ?>
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
                                    </div>
                                <?php endforeach ?>
                            </div>
                            <div class="glass-card-footer d-flex justify-content-end gap-2">
                                <button type="reset" class="btn-g ghost">
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
            $(this).closest('.form-group-g').find('.help-block').text('');
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
