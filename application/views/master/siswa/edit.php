<?php
$fotoSiswa = $siswa->foto;
if (!file_exists(FCPATH . $siswa->foto)) {
    $fotoSiswa = str_replace('profiles', 'foto_siswa', $siswa->foto);
}
?>
<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/siswa.css">

<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1><?= $judul ?></h1>
                <button onclick="window.history.back();" type="button" class="btn btn-danger-glass btn-sm">
                    <i class="fas fa-arrow-circle-left mr-1"></i>Kembali
                </button>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?= $this->session->flashdata('updatesiswa') ?>
            <div class="row">
                <!-- Kolom kiri: profil -->
                <div class="col-md-4 mb-4">
                    <div class="glass-card">
                        <div class="card-header">
                            <h6 class="card-title">Detail Data Siswa</h6>
                        </div>
                        <div class="card-body">
                            <div class="user-profile-inner">
                                <?php if (!file_exists(FCPATH . $fotoSiswa) || $fotoSiswa == ""): ?>
                                    <img src="<?= base_url() ?>/assets/img/<?= $siswa->jenis_kelamin == 'L' ? 'siswa-l' : 'siswa-p' ?>.png"
                                        class="profile-avatar" alt="avatar">
                                <?php else: ?>
                                    <img src="<?= base_url($fotoSiswa) ?>" class="profile-avatar" alt="avatar">
                                <?php endif; ?>

                                <h4><?= $siswa->nama ?></h4>

                                <div class="row" style="gap:0;margin:0">
                                    <div class="col-6 pr-1">
                                        <button type="button" data-toggle="modal" data-target="#editFotoModal"
                                            class="btn btn-cyan btn-sm btn-block">
                                            <i class="fas fa-image mr-1"></i>Ganti Foto
                                        </button>
                                    </div>
                                    <div class="col-6 pl-1">
                                        <button type="button" class="btn btn-danger-glass btn-sm btn-block"
                                            onclick="deleteImage(true)">
                                            <i class="fa fa-trash mr-1"></i>Hapus Foto
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2" style="margin:0">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-warning-glass btn-sm btn-block"
                                            data-toggle="modal" data-target="#editLoginModal">
                                            <i class="fa fa-pencil mr-1"></i>Edit Username / Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom kanan: form edit -->
                <div class="col-md-8 mb-4">
                    <?= form_open('datasiswa/updatedata', array('id' => 'siswa'), array('method' => 'edit', 'id_siswa' => $siswa->id_siswa)) ?>
                    <div class="glass-card">
                        <div class="card-header">
                            <ul class="nav nav-pills nav-pills-glass flex-wrap" style="gap:0.25rem">
                                <li class="nav-item"><a class="nav-link active" href="#datasiswa" data-toggle="tab">Siswa</a></li>
                                <li class="nav-item"><a class="nav-link" href="#biosiswa" data-toggle="tab">Detail</a></li>
                                <li class="nav-item"><a class="nav-link" href="#ortusiswa" data-toggle="tab">Keluarga</a></li>
                                <li class="nav-item"><a class="nav-link" href="#walisiswa" data-toggle="tab">Wali</a></li>
                            </ul>
                            <div class="d-flex" style="gap:0.4rem">
                                <button type="reset" class="btn btn-warning-glass btn-sm">
                                    <i class="fa fa-sync mr-1"></i>Reset
                                </button>
                                <button type="submit" id="submit" class="btn btn-success-glass btn-sm">
                                    <i class="fas fa-save mr-1"></i>Simpan
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">

                                <!-- Tab: Siswa -->
                                <div class="tab-pane active" id="datasiswa">
                                    <?php foreach ($input_data as $data):
                                        $req = ($data->name == 'nisn' || $data->name == 'sekolah_asal') ? '' : ' required';
                                        if ($data->name == 'jenis_kelamin'): ?>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label"><?= $data->label ?></label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text input-group-text-glass"><i class="<?= $data->icon ?>"></i></span>
                                                        </div>
                                                        <select class="form-control form-control-glass" name="jenis_kelamin" required>
                                                            <option value="" disabled>Pilih Jenis Kelamin</option>
                                                            <?php foreach (["L" => "Laki-laki", "P" => "Perempuan"] as $k => $v): ?>
                                                                <option value="<?= $k ?>" <?= $k == $data->value ? 'selected' : '' ?>><?= $v ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif ($data->name == 'status'): ?>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label"><?= $data->label ?></label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text input-group-text-glass"><i class="<?= $data->icon ?>"></i></span>
                                                        </div>
                                                        <select class="form-control form-control-glass" name="status" required>
                                                            <?php foreach (["Pilih Status", "AKTIF", "LULUS", "PINDAH", "KELUAR"] as $k => $v): ?>
                                                                <option value="<?= $k ?>" <?= $k == $data->value ? 'selected' : '' ?>><?= $v ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif ($data->name == 'kelas_awal'): ?>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label"><?= $data->label ?></label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text input-group-text-glass"><i class="<?= $data->icon ?>"></i></span>
                                                        </div>
                                                        <select class="form-control form-control-glass" name="kelas_awal" required>
                                                            <option value="" disabled>Kelas Awal</option>
                                                            <?php
                                                            if ($setting->jenjang == 1)       $opsis = ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'];
                                                            elseif ($setting->jenjang == 2)   $opsis = ['7' => '7', '8' => '8', '9' => '9'];
                                                            else                              $opsis = ['10' => '10', '11' => '11', '12' => '12'];
                                                            foreach ($opsis as $key => $kelas): ?>
                                                                <option value="<?= $key ?>" <?= $key == $data->value ? 'selected' : '' ?>><?= $kelas ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php elseif ($data->name == 'agama'): ?>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label"><?= $data->label ?></label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text input-group-text-glass"><i class="<?= $data->icon ?>"></i></span>
                                                        </div>
                                                        <select class="form-control form-control-glass" id="agama" name="agama" required>
                                                            <option value="0">Pilih Agama</option>
                                                            <?php foreach (["Islam", "Kristen", "Katolik", "Kristen Protestan", "Hindu", "Budha", "Konghucu", "lainnya"] as $ag): ?>
                                                                <option value="<?= $ag ?>" <?= $ag == $data->value ? 'selected' : '' ?>><?= $ag ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="form-group row">
                                                <label class="col-md-4 col-form-label"><?= $data->label ?></label>
                                                <div class="col-md-8">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text input-group-text-glass"><i class="<?= $data->icon ?>"></i></span>
                                                        </div>
                                                        <input value="<?= trim($data->value ?? '') ?>"
                                                            id="<?= $data->name ?>"
                                                            type="<?= $data->type ?>"
                                                            class="form-control form-control-glass <?= $data->class ?>"
                                                            name="<?= $data->name ?>"
                                                            placeholder="<?= $data->label ?>"
                                                            autocomplete="off" <?= $req ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tab: Detail -->
                                <div class="tab-pane" id="biosiswa">
                                    <?php foreach ($input_bio as $bio): ?>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label"><?= $bio->label ?></label>
                                            <div class="col-md-8">
                                                <?php if ($bio->name == 'agama'): ?>
                                                    <select class="form-control form-control-glass" id="agama_bio" name="agama">
                                                        <option value="">Pilih Agama</option>
                                                        <?php foreach (["Islam", "Kristen", "Katolik", "Kristen Protestan", "Hindu", "Budha", "Konghucu", "lainnya"] as $ag): ?>
                                                            <option value="<?= $ag ?>" <?= $ag == $bio->value ? 'selected' : '' ?>><?= $ag ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input value="<?= trim($bio->value ?? '') ?>"
                                                        id="<?= $bio->name ?>"
                                                        type="<?= $bio->type ?>"
                                                        class="form-control form-control-glass <?= $bio->class ?>"
                                                        name="<?= $bio->name ?>"
                                                        placeholder="<?= $bio->label ?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tab: Keluarga -->
                                <div class="tab-pane" id="ortusiswa">
                                    <?php foreach ($input_ortu as $ortu): ?>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label"><?= $ortu->label ?></label>
                                            <div class="col-md-8">
                                                <?php if ($ortu->name == 'status_keluarga'): ?>
                                                    <select class="form-control form-control-glass" id="<?= $ortu->name ?>" name="<?= $ortu->name ?>">
                                                        <?php foreach (["Pilih Status Kelurga", "Anak Kandung", "Anak Tiri", "Anak Angkat"] as $ks => $stt): ?>
                                                            <option value="<?= $ks ?>" <?= $ks == $ortu->value ? 'selected' : '' ?>><?= $stt ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php else: ?>
                                                    <input value="<?= trim($ortu->value ?? '') ?>"
                                                        id="<?= $ortu->name ?>"
                                                        type="<?= $ortu->type ?>"
                                                        class="form-control form-control-glass"
                                                        name="<?= $ortu->name ?>"
                                                        placeholder="<?= $ortu->label ?>">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Tab: Wali -->
                                <div class="tab-pane" id="walisiswa">
                                    <?php foreach ($input_wali as $wali): ?>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label"><?= $wali->label ?></label>
                                            <div class="col-md-8">
                                                <input value="<?= trim($wali->value ?? '') ?>"
                                                    id="<?= $wali->name ?>"
                                                    type="<?= $wali->type ?>"
                                                    class="form-control form-control-glass"
                                                    name="<?= $wali->name ?>"
                                                    placeholder="<?= $wali->label ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal: Edit Foto -->
<div class="modal fade" id="editFotoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Foto</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?= form_open_multipart('', array('id' => 'set-foto-profile')) ?>
                <label style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#94a3b8">Foto Profil</label>
                <input type="file" id="foto-profile" name="foto" class="dropify"
                    data-max-file-size-preview="2M"
                    data-allowed-file-extensions="jpg jpeg png"
                    data-default-file="<?= base_url() . $fotoSiswa ?>" />
                <?= form_close() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success-glass btn-sm" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Login -->
<?= form_open('', array('id' => 'updatelogin'), array('id_siswa' => $siswa->id_siswa)) ?>
<div class="modal fade" id="editLoginModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Username / Password</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?php
                $loginFields = [
                    ['label' => 'Username',          'name' => 'username',    'type' => 'text',     'value' => $siswa->username,  'readonly' => false],
                    ['label' => 'Password Lama',     'name' => 'old',         'type' => 'text',     'value' => $siswa->password,  'readonly' => true],
                    ['label' => 'Password Baru',     'name' => 'new',         'type' => 'text',     'value' => '',                'readonly' => false],
                    ['label' => 'Konfirmasi Password', 'name' => 'new_confirm', 'type' => 'text',     'value' => '',                'readonly' => false],
                ];
                foreach ($loginFields as $f): ?>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text input-group-text-glass" style="min-width:140px"><?= $f['label'] ?></span>
                        </div>
                        <input type="<?= $f['type'] ?>" class="form-control form-control-glass"
                            name="<?= $f['name'] ?>" value="<?= $f['value'] ?>"
                            placeholder="<?= $f['label'] ?>"
                            <?= $f['readonly'] ? 'readonly' : '' ?>>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning-glass btn-sm">Ganti Password</button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<script>
    var fotoProfile = '';
    var idSiswa = '<?= $siswa->id_siswa ?>';
    var src = '<?= $fotoSiswa ?>';

    $(document).ready(function() {
        ajaxcsrf();

        $('.tahun').datetimepicker({
            icons: {
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            },
            timepicker: false,
            scrollInput: false,
            scrollMonth: false,
            format: 'Y-m-d',
            disabledWeekDays: [0],
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        });

        var drEvent = $('.dropify').dropify({
            messages: {
                'default': 'Seret foto kesini atau klik',
                'replace': 'Seret atau klik untuk mengganti foto',
                'remove': 'Hapus',
                'error': 'Ada kesalahan.'
            }
        });

        drEvent.on('dropify.afterClear', function(event) {
            src = $(event.currentTarget).data('default-file');
            deleteImage(false);
            fotoProfile = '';
        });

        drEvent.on('dropify.errors', function() {
            showDangerToast("File rusak atau tidak didukung");
        });

        $('#editFotoModal').on('hidden.bs.modal', function() {
            window.location.reload();
        });

        $('form#siswa').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var btn = $('#submit');
            btn.attr('disabled', 'disabled');

            swal.fire({
                text: "Silahkan tunggu....",
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });

            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: 'POST',
                success: function(data) {
                    btn.removeAttr('disabled');
                    if (data.insert) {
                        swal.fire({
                                title: "Sukses",
                                text: data.text,
                                icon: "success"
                            })
                            .then(r => {
                                if (r.value) window.location.reload(true);
                            });
                    } else {
                        swal.fire({
                            title: "Error",
                            text: data.text,
                            icon: "error"
                        });
                    }
                },
                error: function() {
                    swal.fire({
                        title: "ERROR",
                        text: "Data tidak tersimpan",
                        icon: "error"
                    });
                }
            });
        });

        $('#updatelogin').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var dataPost = $(this).serialize();

            $('#editLoginModal').modal('hide').data('bs.modal', null);
            swal.fire({
                text: "Silahkan tunggu....",
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });

            $.ajax({
                url: base_url + "datasiswa/editlogin",
                type: "POST",
                dataType: "JSON",
                data: dataPost,
                success: function(data) {
                    if (data.status) {
                        swal.fire({
                                title: "Sukses",
                                html: data.text,
                                icon: "success"
                            })
                            .then(r => {
                                if (r.value) window.location.reload();
                            });
                    } else {
                        var html = '<ul>';
                        ['username', 'old', 'new', 'new_confirm'].forEach(function(k) {
                            if (data.errors[k]) html += '<li>' + data.errors[k] + '</li>';
                        });
                        html += '</ul>';
                        swal.fire({
                            title: "ERROR",
                            html: html,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    swal.fire({
                        title: "Error",
                        text: JSON.parse(xhr.responseText).Message,
                        icon: "error"
                    });
                }
            });
        });

        function uploadAttach(action, data) {
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: action,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(data) {
                    fotoProfile = data.src;
                },
                error: function() {
                    showDangerToast("File tidak terbaca");
                }
            });
        }

        $("#foto-profile").change(function() {
            var input = $(this)[0];
            if (input.files && input.files[0]) {
                var form = new FormData($('#set-foto-profile')[0]);
                uploadAttach(base_url + 'datasiswa/uploadfile/' + idSiswa, form);
            }
        });
    });

    function deleteImage(fromBtn) {
        $.ajax({
            data: {
                src: src
            },
            type: "POST",
            url: base_url + "datasiswa/deletefile/" + idSiswa,
            cache: false,
            success: function() {
                if (fromBtn) window.location.reload();
            }
        });
    }
</script>
