<?php
$satuan = ["1" => ["SD", "MI"], "2" => ["SMP", "MTS"], "3" => ["SMA", "MA", "SMK"]];
?>
<link rel="stylesheet" href="<?= base_url('assets/app/css/setting.css') ?>">

<div class="content-wrapper setting-page pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-bold">Profile Madrasah</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card my-shadow">
                <div class="card-header bg-orange">
                    <div class="card-title">
                        <h6 class="text-bold">Pengaturan Profil</h6>
                    </div>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="submitSetting()">
                            <i class="fa fa-save mr-1"></i>Simpan
                        </button>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <?= form_open('', array('id' => 'savesetting')) ?>

                    <p class="setting-section-title">Identitas Aplikasi & Sekolah</p>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label>Nama Aplikasi *</label>
                            <input type="text" name="nama_aplikasi" class="form-control required"
                                value="<?= $setting->nama_aplikasi ?>">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label>Nama Sekolah *</label>
                            <input type="text" name="nama_sekolah" class="form-control required"
                                value="<?= $setting->sekolah ?>" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>NSS / NSM</label>
                            <input type="number" name="nss" class="form-control" value="<?= $setting->nss ?>">
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>NPSN</label>
                            <input type="number" name="npsn" class="form-control" value="<?= $setting->npsn ?>">
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>Jenjang *</label>
                            <select id="jenjang" class="form-control required" name="jenjang" required>
                                <option value="" disabled>Pilih Jenjang</option>
                                <?php
                                $jenjang = ["SD/MI", "SMP/MTS", "SMA/MA/SMK"];
                                for ($i = 0; $i < 3; $i++) {
                                    $arrJenjang[$i + 1] = $jenjang[$i];
                                }
                                foreach ($arrJenjang as $key => $val) :
                                    $selected = $setting->jenjang == $key ? 'selected' : '';
                                ?>
                                    <option value="<?= $key ?>" <?= $selected ?>><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>Satuan Pendidikan *</label>
                            <select id="satuan-pend" class="form-control required" name="satuan_pendidikan" required>
                                <option value="0" disabled>Satuan Pendidikan</option>
                                <?php
                                $satuan_selected = $satuan[$setting->jenjang];
                                for ($i = 0; $i < count($satuan_selected); $i++) {
                                    $arrSatuan[$i + 1] = $satuan_selected[$i];
                                }
                                foreach ($arrSatuan as $keys => $vals) :
                                    $selecteds = $setting->satuan_pendidikan == $keys ? 'selected' : '';
                                ?>
                                    <option value="<?= $keys ?>" <?= $selecteds ?>><?= $vals ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <p class="setting-section-title">Alamat</p>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label>Alamat *</label>
                            <textarea class="form-control required" name="alamat" rows="5"
                                required><?= $setting->alamat ?></textarea>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label>Desa / Kelurahan *</label>
                                    <input type="text" name="desa" class="form-control required"
                                        value="<?= $setting->desa ?>" required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label>Kecamatan *</label>
                                    <input type="text" name="kec" class="form-control required"
                                        value="<?= $setting->kecamatan ?>" required>
                                </div>
                                <div class="col-md-5 mb-4">
                                    <label>Kabupaten / Kota *</label>
                                    <input type="text" name="kota" class="form-control required"
                                        value="<?= $setting->kota ?>" required>
                                </div>
                                <div class="col-md-2 mb-4">
                                    <label>Kode Pos</label>
                                    <input type="number" name="kode_pos" class="form-control"
                                        value="<?= $setting->kode_pos ?>">
                                </div>
                                <div class="col-md-5 mb-4">
                                    <label>Provinsi *</label>
                                    <input type="text" name="provinsi" class="form-control required"
                                        value="<?= $setting->provinsi ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="setting-section-title">Kontak</p>
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <label>Faksimili</label>
                            <input type="text" name="fax" class="form-control" value="<?= $setting->fax ?>">
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>Website</label>
                            <input type="text" name="web" class="form-control" value="<?= $setting->web ?>">
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>Email</label>
                            <input type="text" name="email" class="form-control" value="<?= $setting->email ?>">
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>Nomor Telepon</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">+62</span>
                                </div>
                                <input type="number" name="tlp" class="form-control" value="<?= $setting->telp ?>">
                            </div>
                        </div>
                    </div>

                    <p class="setting-section-title">Kepala Sekolah</p>
                    <div class="row">
                        <div class="col-md-5 mb-4">
                            <label>Nama Kepala Sekolah *</label>
                            <input type="text" name="kepsek" class="form-control required"
                                value="<?= $setting->kepsek ?>" required>
                        </div>
                        <div class="col-md-3 mb-4">
                            <label>NIP</label>
                            <input type="number" name="nip" class="form-control" value="<?= $setting->nip ?>">
                        </div>
                    </div>

                    <?= form_close() ?>

                    <p class="setting-section-title">Aset Visual</p>
                    <div class="row">
                        <div class="col-md-4">
                            <?= form_open_multipart('', array('id' => 'set-tandatangan')) ?>
                            <div class="form-group pb-2">
                                <label>Tanda Tangan</label>
                                <input type="file" id="tanda-tangan" name="logo" class="dropify"
                                    data-max-file-size-preview="2M"
                                    data-allowed-file-extensions="jpg jpeg png"
                                    data-default-file="<?= base_url() . $setting->tanda_tangan ?>" />
                            </div>
                            <?= form_close() ?>
                        </div>
                        <div class="col-md-4">
                            <?= form_open_multipart('', array('id' => 'set-logo-kiri')) ?>
                            <div class="form-group pb-2">
                                <label>Logo Kiri / Logo Aplikasi</label>
                                <input type="file" id="logo-kiri" name="logo" class="dropify"
                                    data-max-file-size-preview="2M"
                                    data-allowed-file-extensions="jpg jpeg png"
                                    data-default-file="<?= base_url() . $setting->logo_kiri ?>" />
                            </div>
                            <?= form_close() ?>
                        </div>
                        <div class="col-md-4">
                            <?= form_open_multipart('', array('id' => 'set-logo-kanan')) ?>
                            <div class="form-group pb-2">
                                <label>Logo Kanan</label>
                                <input type="file" id="logo-kanan" name="logo" class="dropify"
                                    data-max-file-size-preview="2M"
                                    data-allowed-file-extensions="jpg jpeg png"
                                    data-default-file="<?= base_url() . $setting->logo_kanan ?>" />
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
    var logoKanan = '<?= base_url() . $setting->logo_kanan ?>';
    var logoKiri = '<?= base_url() . $setting->logo_kiri ?>';
    var tandatangan = '<?= base_url() . $setting->tanda_tangan ?>';
    var satuanPend = JSON.parse(JSON.stringify(<?= json_encode($satuan) ?>));

    function submitSetting() {
        $('#savesetting').submit();
    }

    $(document).ready(function() {
        ajaxcsrf();

        var drEvent = $('.dropify').dropify({
            messages: {
                'default': 'Seret logo kesini atau klik',
                'replace': 'Seret atau klik untuk mengganti logo',
                'remove': 'Hapus',
                'error': 'Ooops, ada kesalahan!!'
            }
        });

        drEvent.on('dropify.afterClear', function(event, element) {
            deleteImage($(event.currentTarget).data('default-file'));
            if (element.element.id === 'logo-kanan') logoKanan = '';
            else if (element.element.id === 'logo-kiri') logoKiri = '';
            else if (element.element.id === 'tanda-tangan') tandatangan = '';
        });

        $('#jenjang').change(function() {
            var htmlOptions = '<option value="0" disabled="">Satuan Pendidikan</option>\n';
            var satSelected = satuanPend[$(this).val()];
            for (let i = 0; i < satSelected.length; i++) {
                htmlOptions += '<option value="' + (i + 1) + '">' + satSelected[i] + '</option>';
            }
            $('#satuan-pend').html(htmlOptions);
        });

        $('#savesetting').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            var hasInput = true;
            $('.required').each(function() {
                if ($(this).val() === '') {
                    hasInput = false;
                    return false;
                }
            });

            if (!hasInput) {
                Swal.fire({
                    title: 'ERROR',
                    text: 'Isi semua pilihan yang bertanda bintang (*)',
                    icon: 'error'
                });
            } else {
                swal.fire({
                    text: 'Silahkan tunggu....',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
                $.ajax({
                    url: base_url + 'settings/savesetting',
                    type: 'POST',
                    data: $(this).serialize() + '&logo_kanan=' + logoKanan + '&logo_kiri=' + logoKiri + '&tanda_tangan=' + tandatangan,
                    success: function(response) {
                        swal.fire({
                                title: 'Sukses',
                                html: 'Berhasil menyimpan pengaturan',
                                icon: 'success',
                                confirmButtonColor: '#06b6d4'
                            })
                            .then(r => {
                                if (r.value) window.location.href = base_url + 'settings';
                            });
                    },
                    error: function(xhr) {
                        const err = JSON.parse(xhr.responseText);
                        swal.fire({
                            title: 'Error',
                            text: err.Message,
                            icon: 'error'
                        });
                    }
                });
            }
        });

        $("#logo-kanan").change(function() {
            handleFileChange(this, '#prev-logo-kanan', '#set-logo-kanan', 'logo_kanan');
        });
        $("#logo-kiri").change(function() {
            handleFileChange(this, '#prev-logo-kiri', '#set-logo-kiri', 'logo_kiri');
        });
        $("#tanda-tangan").change(function() {
            handleFileChange(this, null, '#set-tandatangan', 'tandatangan');
        });

        function handleFileChange(input, previewSel, formSel, type) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    if (previewSel) $(previewSel).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
                uploadAttach(base_url + 'settings/uploadfile/' + type, new FormData($(formSel)[0]));
            }
        }

        function uploadAttach(action, data) {
            $.ajax({
                type: 'POST',
                enctype: 'multipart/form-data',
                url: action,
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(data) {
                    if (data.src.includes('kanan')) logoKanan = data.src;
                    else if (data.src.includes('kiri')) logoKiri = data.src;
                    else if (data.src.includes('tanda')) tandatangan = data.src;
                },
                error: function(e) {
                    console.log('error', e.responseText);
                }
            });
        }

        function deleteImage(src) {
            $.ajax({
                data: {
                    src: src
                },
                type: 'POST',
                url: base_url + 'settings/deletefile',
                cache: false
            });
        }
    });
</script>
