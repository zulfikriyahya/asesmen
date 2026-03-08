<link rel="stylesheet" href="<?= base_url('assets/app/css/setting.css') ?>">

<div class="content-wrapper setting-page pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-6">
                    <h1><?= $judul ?></h1>
                </div>
                <div class="col-6">
                    <a href="<?= base_url('bukuinduk') ?>" class="btn btn-sm btn-danger float-right">
                        <i class="fas fa-arrow-circle-left mr-1"></i>
                        <span class="d-none d-sm-inline-block">Kembali</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card my-shadow mb-4">
                <div class="card-header bg-orange">
                    <div class="card-title">
                        <h6 class="text-bold"><?= $subjudul ?></h6>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div class="alert alert-default-info">
                        Fitur ini digunakan untuk menyalin semua siswa TP: <strong><?= $tp_active->tahun ?></strong> dari semester 1 ke semester 2.
                        <ul class="mt-2 mb-0">
                            <li>Pilih kelas dari semester I</li>
                            <li>Klik SIMPAN untuk menyalin semua data kelas</li>
                        </ul>
                        <div class="mt-2">Untuk menyalin siswa antar Tahun Pelajaran, gunakan halaman <strong>KENAIKAN KELAS</strong>.</div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-select-group mb-3">
                                <label>Tahun Pelajaran</label>
                                <select name="tahun" id="id-tahun" class="form-control form-control-sm">
                                    <?php foreach ($tahun as $key => $value) :
                                        $selected = $key == $tahun_selected ? 'selected="selected"' : ''; ?>
                                        <option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-select-group mb-3">
                                <label>Semester</label>
                                <select name="semester" id="id-smt" class="form-control form-control-sm">
                                    <?php foreach ($semester as $key => $value) :
                                        $selected = $key == $smt_selected ? 'selected="selected"' : ''; ?>
                                        <option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-select-group">
                                <label>Kelas</label>
                                <?php echo form_dropdown(
                                    'kelas',
                                    $kelases,
                                    isset($kelas_selected) ? $kelas_selected : null,
                                    'id="id-kelas" class="form-control form-control-sm"'
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var thnSelected = '<?= isset($tahun_selected) ? $tahun_selected : '' ?>';
    var smtSelected = '<?= isset($smt_selected)   ? $smt_selected   : '' ?>';
    var klsSelected = '<?= isset($kelas_selected)  ? $kelas_selected  : '' ?>';

    $(document).ready(function() {
        var opsiTahun = $('#id-tahun'),
            opsiSmt = $('#id-smt'),
            opsiKelas = $('#id-kelas');

        if (thnSelected == '') opsiTahun.prepend("<option value='0' selected disabled>Pilih Tahun Pelajaran</option>");
        if (smtSelected == '') opsiSmt.prepend("<option value='0' selected disabled>Pilih Semester</option>");
        if (klsSelected == '') opsiKelas.prepend("<option value='0' selected disabled>Pilih Kelas</option>");

        function go() {
            var t = opsiTahun.val(),
                s = opsiSmt.val(),
                k = opsiKelas.val();
            if (t && s) window.location.href = base_url + 'bukuinduk?backupnilai=' + t + '&semester=' + s + '&kelas=' + k;
        }
        opsiTahun.change(go);
        opsiSmt.change(go);
        opsiKelas.change(go);
    });
</script>
