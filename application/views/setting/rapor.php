<?php
$satuan = [
    "1" => ["I ~ V", "VI"],
    "2" => ["VII-VIII", "IX"],
    "3" => ["X-XI", "XII"]
];
?>
<link rel="stylesheet" href="<?= base_url('assets/app/css/setting.css') ?>">

<div class="content-wrapper setting-page pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="text-bold"><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card my-shadow mb-4">
                <div class="card-header bg-orange">
                    <div class="card-title">
                        <h6 class="text-bold">Konfigurasi Rapor</h6>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div class="alert alert-default-info">
                        <strong>KKM</strong>
                        <ul>
                            <li>KKM Tunggal: mengatur semua mapel mempunyai KKM yang sama</li>
                            <li>Total BOBOT harus 100</li>
                            <li>Jangan lupa untuk menyimpan perubahan</li>
                        </ul>
                        <strong>Tampilkan NIP</strong>
                        <ul class="mb-0">
                            <li>Pilih <b>YA</b> jika NIP kepala sekolah / walikelas diisi NIP</li>
                            <li>Jika diisi NUPTK atau nomor lain, pilih <b>TIDAK</b></li>
                        </ul>
                    </div>
                    <hr>

                    <?= form_open('edit', array('id' => 'editsetting'), array('id_setting' => $rapor != null ? $rapor->id_setting : '')) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="setting-section-title">Tanggal Rapor</p>
                            <div class="form-group row">
                                <label class="col-5 col-form-label">Tgl Rapor PTS</label>
                                <div class="col-7">
                                    <input type="text" name="tgl_rapor_pts"
                                        value="<?= $rapor != null ? $rapor->tgl_rapor_pts : '' ?>"
                                        class="form-control tgl" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-5 col-form-label">Tgl Rapor Akhir Kls <?= $satuan[$setting->jenjang][0] ?></label>
                                <div class="col-7">
                                    <input type="text" name="tgl_rapor_akhir"
                                        value="<?= $rapor != null ? $rapor->tgl_rapor_akhir : '' ?>"
                                        class="form-control tgl" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-5 col-form-label">Tgl Rapor Akhir Kls <?= $satuan[$setting->jenjang][1] ?></label>
                                <div class="col-7">
                                    <input type="text" name="tgl_rapor_kelas_akhir"
                                        value="<?= $rapor != null ? $rapor->tgl_rapor_kelas_akhir : '' ?>"
                                        class="form-control tgl" autocomplete="off" required>
                                </div>
                            </div>

                            <p class="setting-section-title mt-3">Tampilan NIP</p>
                            <div class="form-group row">
                                <label class="col-5 col-form-label">NIP Kepala Sekolah</label>
                                <div class="col-7">
                                    <?php echo form_dropdown(
                                        'nip_kepsek',
                                        $kkm_drop,
                                        $rapor != null && isset($rapor->nip_kepsek) ? $rapor->nip_kepsek : '',
                                        'class="form-control" required'
                                    ); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-5 col-form-label">NIP Walikelas</label>
                                <div class="col-7">
                                    <?php echo form_dropdown(
                                        'nip_walikelas',
                                        $kkm_drop,
                                        $rapor != null && isset($rapor->nip_walikelas) ? $rapor->nip_walikelas : '',
                                        'class="form-control" required'
                                    ); ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1"></div>

                        <div class="col-md-5">
                            <p class="setting-section-title">KKM & Bobot Nilai</p>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">KKM Tunggal</label>
                                <div class="col-8">
                                    <?php echo form_dropdown(
                                        'kkm_tunggal',
                                        $kkm_drop,
                                        $rapor != null ? $rapor->kkm_tunggal : '',
                                        'id="tunggal" class="form-control" required'
                                    ); ?>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">KKM</label>
                                <div class="col-8">
                                    <input type="number" name="kkm"
                                        value="<?= $rapor != null ? $rapor->kkm : '' ?>"
                                        class="form-control kkm" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Bobot PH</label>
                                <div class="col-8">
                                    <input type="number" name="bobot_ph"
                                        value="<?= $rapor != null ? $rapor->bobot_ph : '' ?>"
                                        class="form-control kkm" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Bobot PTS</label>
                                <div class="col-8">
                                    <input type="number" name="bobot_pts"
                                        value="<?= $rapor != null ? $rapor->bobot_pts : '' ?>"
                                        class="form-control kkm" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-4 col-form-label">Bobot PAS</label>
                                <div class="col-8">
                                    <input type="number" name="bobot_pas"
                                        value="<?= $rapor != null ? $rapor->bobot_pas : '' ?>"
                                        class="form-control kkm" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-12 text-right mt-2">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-save mr-1"></i>Simpan
                            </button>
                        </div>
                    </div>
                    <?= form_close() ?>
                </div>
                <div class="overlay d-none" id="loading">
                    <div class="spinner-grow"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        function onChangeKkm() {
            var tunggal = $('#tunggal').val();
            if (tunggal == '1') $('.kkm').removeAttr('readonly');
            else $('.kkm').attr('readonly', 'true');
        }

        onChangeKkm();

        $.datetimepicker.setLocale('id');
        $('.tgl').datetimepicker({
            i18n: {
                id: {
                    months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    dayOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
                }
            },
            timepicker: false,
            scrollInput: false,
            scrollMonth: false,
            format: 'Y-m-d',
            disabledWeekDays: [0]
        }).change(function() {
            $(this).val(buat_tanggal_indonesia($(this).val()));
        });

        $('#editsetting').submit(function(e) {
            e.stopPropagation();
            e.preventDefault();
            e.stopImmediatePropagation();
            $('#loading').removeClass('d-none');
            $.ajax({
                url: base_url + 'rapor/saveraporadmin',
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    setTimeout(function() {
                        $('#loading').addClass('d-none');
                        response.status ? showSuccessToast('Sukses') : showDangerToast('Gagal');
                    }, 500);
                },
                error: function() {
                    $('#loading').addClass('d-none');
                    showDangerToast('Gagal disimpan');
                }
            });
        });

        $('#tunggal').on('change', function() {
            onChangeKkm();
        });
    });
</script>
