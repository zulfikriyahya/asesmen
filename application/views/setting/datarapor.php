<link rel="stylesheet" href="<?= base_url('assets/app/css/setting.css') ?>">
<style>
    .sidebar-siswa {
        background: var(--surface-1);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    .sidebar-siswa .sidebar-header {
        background: linear-gradient(90deg, rgba(6, 182, 212, 0.2), rgba(14, 165, 233, 0.1));
        border-bottom: 1px solid var(--glass-border);
        padding: 12px 16px;
    }

    .sidebar-siswa .sidebar-header h6 {
        font-family: 'Lexend', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--clr-accent);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }

    .sidebar-siswa .siswa-list {
        height: 400px;
        overflow-y: auto;
        padding: 6px 0;
    }

    .sidebar-siswa .siswa-list::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-siswa .siswa-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-siswa .siswa-list::-webkit-scrollbar-thumb {
        background: var(--clr-primary-dk);
        border-radius: 4px;
    }

    .preview-container {
        background: var(--surface-3);
        border-radius: var(--radius-md);
        padding: 16px;
        min-height: 300mm;
        overflow-y: auto;
        display: flex;
        justify-content: center;
    }

    .preview-action-bar {
        background: var(--surface-2);
        border-radius: var(--radius-sm);
        padding: 10px 12px;
        margin-bottom: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        border: 1px solid var(--glass-border);
    }

    .alert-restore {
        background: rgba(239, 68, 68, 0.08) !important;
        border-left: 3px solid var(--clr-danger) !important;
        border-radius: var(--radius-md) !important;
        padding: 14px 18px;
        color: #fca5a5 !important;
        font-family: 'Lexend', sans-serif;
        font-size: 0.82rem;
    }

    .alert-restore .text-danger {
        color: #f87171 !important;
    }

    .form-select-group label {
        font-family: 'Lexend', sans-serif;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-bottom: 5px;
    }
</style>

<div class="content-wrapper setting-page pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-6">
                    <h1><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- SIDEBAR KIRI -->
                <div class="col-md-3 mb-4">
                    <div class="sidebar-siswa mb-3">
                        <div class="sidebar-header">
                            <h6>Filter Data</h6>
                        </div>
                        <div class="p-3">
                            <div class="form-select-group mb-3">
                                <label>Tahun Pelajaran</label>
                                <select name="tahun" id="id-tahun" class="form-control form-control-sm">
                                    <?php foreach ($tahun as $key => $value) :
                                        $selected = isset($tahun_selected) && $key == $tahun_selected ? 'selected="selected"' : ''; ?>
                                        <option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-select-group mb-3">
                                <label>Semester</label>
                                <select name="semester" id="id-smt" class="form-control form-control-sm">
                                    <?php foreach ($semester as $key => $value) :
                                        $selected = isset($smt_selected) && $key == $smt_selected ? 'selected="selected"' : ''; ?>
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

                    <div class="sidebar-siswa">
                        <div class="sidebar-header">
                            <h6>Pilih Siswa</h6>
                        </div>
                        <div class="siswa-list">
                            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview">
                                <?php if (isset($siswas)) :
                                    $n = 1;
                                    foreach ($siswas as $siswa): ?>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                class="nav-link pt-1 pb-1 pl-2 text-sm siswa"
                                                onclick="preview(<?= $siswa->id_siswa ?>)">
                                                <?= $n . '. ' . $siswa->nama ?>
                                            </a>
                                        </li>
                                <?php $n++;
                                    endforeach;
                                endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- KONTEN KANAN -->
                <div class="col-md-9">
                    <div class="alert-restore mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-9">
                                <ul class="mb-0">
                                    <li><span class="text-danger">Opsi ini hanya boleh digunakan setelah guru wali kelas selesai mencetak semua rapor.</span></li>
                                    <li>Untuk mengumpulkan semua nilai siswa pada tahun pelajaran sebelumnya, klik tombol di sebelah kanan.</li>
                                    <li>Opsi ini akan menghapus semua nilai rapor dan memindahkannya ke halaman ini.</li>
                                </ul>
                            </div>
                            <div class="col-md-3 text-right">
                                <button class="btn btn-info btn-sm" onclick="restoreNilai()">
                                    <i class="fa fa-archive mr-1"></i>Pindahkan Nilai
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card my-shadow">
                        <div class="card-header bg-orange">
                            <div class="card-title">
                                <h6 class="text-bold"><?= $subjudul ?></h6>
                            </div>
                        </div>
                        <div class="card-body text-dark p-2">
                            <div class="preview-action-bar">
                                <button id="cetak-sampul" class="btn btn-primary btn-sm" onclick="cetakSampul()" disabled>
                                    <i class="fa fa-print mr-1"></i>Sampul
                                </button>
                                <button id="cetak-info" class="btn btn-primary btn-sm" onclick="cetakInfo()" disabled>
                                    <i class="fa fa-print mr-1"></i>Info
                                </button>
                                <button id="cetak-data" class="btn btn-primary btn-sm" onclick="cetakData()" disabled>
                                    <i class="fa fa-print mr-1"></i>Data Siswa
                                </button>
                                <button id="cetak-nilai" class="btn btn-primary btn-sm" onclick="cetakRapor()" disabled>
                                    <i class="fa fa-print mr-1"></i>Nilai
                                </button>
                                <button id="cetak-semua" class="btn btn-success btn-sm ml-auto" onclick="cetakSemua()" disabled>
                                    <i class="fa fa-print mr-1"></i>Semua Halaman
                                </button>
                            </div>

                            <div class="preview-container">
                                <div id="zoom" style="transform: scale(0.9); transform-origin: top center">
                                    <div id="print-preview">
                                        <div id="empty" style="display:flex;justify-content:center;align-items:center;width:210mm;height:297mm;color:var(--text-muted);font-family:'Lexend',sans-serif;font-size:0.9rem;">
                                            Silahkan pilih siswa
                                        </div>
                                        <div id="print-sampul" class="border my-shadow mb-3 d-none"
                                            style="display:flex;justify-content:center;background:white;width:210mm;height:297mm;padding:5mm 10mm">
                                            <div style="margin-top:80px;text-align:center">
                                                <img src="<?= base_url('assets/img/garuda_bw.png') ?>" style="width:80px">
                                                <br>
                                                <div style="text-align:center;font-family:'Arial';font-size:20pt;font-weight:bold;margin-top:16px">
                                                    <p style="margin-bottom:0">LAPORAN HASIL BELAJAR</p>
                                                    <?php
                                                    if ($setting->jenjang == '1') echo '<p style="margin-bottom:0">MADRASAH IBTIDAIYAH (MI)</p>';
                                                    elseif ($setting->jenjang == '2') echo '<p style="margin-bottom:0">MADRASAH TSANAWIYAH (MTS)</p>';
                                                    else echo '<p style="margin-bottom:0">MADRASAH ALIYAH (MA)</p>';
                                                    ?>
                                                    <p style="margin-bottom:0;font-size:24pt"><?= $setting->sekolah ?></p>
                                                </div>
                                                <div style="text-align:center;font-family:'Arial';font-size:12pt">
                                                    <p style="margin-bottom:0">NSM: <?= $setting->nss ?> | NPSN: <?= $setting->npsn ?></p>
                                                </div>
                                                <img src="<?= base_url() . $setting->logo_kiri ?>" style="width:200px;margin-top:30px">
                                                <div style="text-align:center;font-family:'Arial';font-size:14pt;margin-top:50px">
                                                    <p>NAMA PESERTA DIDIK</p>
                                                    <div style="display:flex;justify-content:center">
                                                        <table style="width:500px;border:1px solid black;border-collapse:collapse">
                                                            <tr>
                                                                <td id="nama-siswa" style="padding:10px;font-size:18pt;font-weight:bold"></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div style="text-align:center;font-family:'Arial';font-size:14pt;margin-top:20px">
                                                    <p>NIS / NISN</p>
                                                    <div style="display:flex;justify-content:center">
                                                        <table style="width:500px;border:1px solid black;border-collapse:collapse">
                                                            <tr>
                                                                <td id="nis-siswa" style="padding:4px"></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div style="text-align:center;font-family:'Arial';font-size:14pt;font-weight:bold;margin-top:80px">
                                                    <p>KEMENTRIAN AGAMA</p>
                                                    <p>REPUBLIK INDONESIA</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="print-info" class="border my-shadow mb-3 d-none" style="background:white;width:210mm;min-height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-data" class="border my-shadow mb-3 d-none" style="background:white;width:210mm;min-height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-sikap-nilai" class="border my-shadow mb-3 d-none pb-5" style="background:white;width:210mm;min-height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-deskripsi1" class="border my-shadow mb-3 d-none" style="background:white;width:210mm;min-height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-deskripsi2" class="border my-shadow mb-3 d-none" style="background:white;width:210mm;min-height:297mm;padding:5mm 10mm"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="overlay d-none" id="loading">
                            <div class="spinner-grow"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script src="<?= base_url() ?>/assets/app/js/print-area.js"></script>
<script>
    var siswaSelected = null;
    var thnSelected = '<?= isset($tahun_selected) ? $tahun_selected : '' ?>';
    var smtSelected = '<?= isset($smt_selected)   ? $smt_selected   : '' ?>';
    var klsSelected = '<?= isset($kelas_selected)  ? $kelas_selected  : '' ?>';

    var arrSiswa = JSON.parse(JSON.stringify(<?= isset($siswas) ? json_encode($siswas) : '[]' ?>));
    var tp = '<?= $tp_active->tahun ?>';
    var smt = '<?= $smt_active->smt ?>';
    var setting = JSON.parse(JSON.stringify(<?= json_encode($setting) ?>));

    let header_rapor = '',
        subHeader = '';
    if (setting.jenjang == '1') {
        header_rapor = 'MADRASAH IBTIDAIYAH';
        subHeader = '(MI)';
    } else if (setting.jenjang == '2') {
        header_rapor = 'MADRASAH TSANAWIYAH';
        subHeader = '(MTS)';
    } else {
        header_rapor = 'MADRASAH ALIYAH';
        subHeader = '(MA)';
    }

    var arrMapel = null;
    var z = 0.9;

    function handleNull(v) {
        return (v == null || v == '0' || v == '') ? '-' : v;
    }

    function handleGender(jk) {
        var k = handleNull(jk);
        if (k != '-') {
            if (k == 'L') k = 'Laki-laki';
            else if (k == 'P') k = 'Perempuan';
        }
        return k;
    }

    function handlePredikat(p) {
        return p == 'A' ? 'Sangat Baik' : p == 'B' ? 'Baik' : p == 'C' ? 'Cukup' : p == 'D' ? 'Kurang' : '';
    }

    function inArray(val, arr) {
        return $.inArray(val, arr) >= 0;
    }

    function handleTanggal(tgl) {
        var b = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if (handleNull(tgl) == '-') return '';
        var p = tgl.split('-');
        return p[2] + ' ' + b[Math.abs(p[1])] + ' ' + p[0];
    }

    function handleTitiMangsa(tgl) {
        var b = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if (handleNull(tgl) == '-') return '';
        var p = tgl.split('-');
        return p[0] + ' ' + (p[1] ? b[Math.abs(p[1])] : '') + (p[2] ? ' ' + p[2] : '');
    }

    function handleNisn(nis, nisn) {
        var s = '';
        if (handleNull(nis) != '-') s += handleNull(nis);
        if (handleNull(nisn) != '-') s += ' / ' + handleNull(nisn);
        return s;
    }

    function handleAlamat(almt, rt, rw, kel, kec, kab, prov) {
        var a = handleNull(almt);
        if (handleNull(rt) != '-' && handleNull(rw) != '-') a += ' RT/RW: ' + handleNull(rt) + '/' + handleNull(rw);
        if (handleNull(kel) != '-') a += ' Desa/Kel. ' + handleNull(kel);
        if (handleNull(kec) != '-') a += ' Kec. ' + handleNull(kec);
        if (handleNull(kab) != '-') a += ' Kota/Kab. ' + handleNull(kab);
        if (handleNull(prov) != '-') a += ' ' + handleNull(prov);
        return a;
    }

    function handleStatusKeluarga(v) {
        var l = ['', 'Anak Kandung', 'Anak Tiri', 'Anak Angkat'];
        return (v == null || v == '-' || v == '') ? '-' : l[v];
    }

    function ellipsisText(text) {
        if (text == null) return '';
        var sp = text.split(',');
        if (sp.length > 2) {
            var s2 = sp[1];
            if (s2.length >= 30) s2 = s2.substring(0, 30) + '...';
            return sp[0] + ',' + s2;
        }
        return text;
    }

    function createPageInfo() {
        var titles = ['Nama Madrasah', 'NPSN', 'NIS/NSS/NDS', 'Alamat', 'Kelurahan/Desa', 'Kecamatan', 'Kota/Kabupaten', 'Provinsi', 'Kode Pos', 'No. Telepon', 'Faksimili', 'Website', 'Email'];
        var vals = [handleNull(setting.sekolah), handleNull(setting.npsn), handleNull(setting.nss), handleNull(setting.alamat), handleNull(setting.desa), handleNull(setting.kecamatan), handleNull(setting.kota), handleNull(setting.provinsi), handleNull(setting.kode_pos), handleNull(setting.telp), handleNull(setting.fax), handleNull(setting.web), handleNull(setting.email)];
        var html = '<div style="padding:0.5cm;margin-top:100px"><div style="text-align:center;font-family:Arial;font-size:20pt;font-weight:bold"><p style="margin-bottom:0">LAPORAN HASIL BELAJAR</p><p style="margin-bottom:0">' + header_rapor + '</p><p style="margin-bottom:0">' + subHeader + '</p></div><br><div style="display:flex;justify-content:center;margin-top:300px;font-family:Tahoma;font-size:14pt"><table style="width:80%;border:0">';
        for (var i = 0; i < titles.length; i++) {
            html += '<tr><td style="width:30%' + (i == 0 ? ';font-weight:bold' : '') + '">' + titles[i] + '</td><td>:</td><td style="width:70%' + (i == 0 ? ';font-weight:bold' : '') + '">' + vals[i] + '</td></tr>';
        }
        return html + '</table></div></div>';
    }

    function createPageIdentitas(idSiswa) {
        var s = arrSiswa[idSiswa];
        var nos = ['1.', '2.', '3.', '4.', '5.', '6.', '7.', '8.', '9.', '10.', '11.', '', '', '12.', '', '', '', '', '', '', '', '', '13.', '', '', '', ''];
        var titles = ['Nama Lengkap Peserta Didik', 'NIS / NISN', 'Tempat Tanggal Lahir', 'Jenis Kelamin', 'Agama', 'Status dalam Keluarga', 'Anak ke', 'Alamat Peserta Didik', 'Nomor Telepon Rumah', 'Madrasah Asal', 'Diterima di Madrasah ini', 'a. Di kelas', 'b. Pada tanggal', 'Orang Tua', 'a. Nama Ayah', 'b. Pekerjaan', 'c. Nomor Telepon/HP', 'd. Alamat', 'e. Nama Ibu', 'f. Pekerjaan', 'g. Nomor Telepon/HP', 'h. Alamat', 'Wali', 'a. Nama Wali', 'b. Pekerjaan', 'c. Nomor Telpon/HP', 'd. Alamat'];
        var vals = [handleNull(s.nama), handleNisn(s.nis, s.nisn), handleNull(s.tempat_lahir) + ', ' + handleTanggal(s.tanggal_lahir), handleGender(s.jenis_kelamin), handleNull(s.agama), handleStatusKeluarga(s.status_keluarga), handleNull(s.anak_ke), handleAlamat(s.alamat, s.rt, s.rw, s.kelurahan, s.kecamatan, s.kabupaten, s.provinsi), handleNull(s.hp), handleNull(s.sekolah_asal), '', handleNull(s.kelas_awal), handleTanggal(s.tahun_masuk), '', handleNull(s.nama_ayah), handleNull(s.pekerjaan_ayah), handleNull(s.nohp_ayah), handleNull(s.alamat_syah), handleNull(s.nama_ibu), handleNull(s.pekerjaan_ibu), handleNull(s.nohp_ibu), handleNull(s.alamat_ibu), '', handleNull(s.nama_wali), handleNull(s.pekerjaan_wali), handleNull(s.nohp_wali), handleNull(s.alamat_wali)];
        var html = '<div style="padding:0.5cm"><div style="text-align:center;font-family:Arial;font-size:16pt;font-weight:bold"><p>IDENTITAS PESERTA DIDIK</p></div><br><div style="display:flex;justify-content:center;margin-top:20px;font-family:Arial;font-size:12pt"><table style="width:100%;border:0">';
        for (var i = 0; i < titles.length; i++) {
            var bold = i === 0 ? 'font-weight:bold;' : '';
            html += '<tr><td style="width:5%;vertical-align:top">' + nos[i] + '</td><td style="width:35%;vertical-align:top">' + titles[i] + '</td><td style="width:2%;vertical-align:top">:</td><td style="vertical-align:top;' + bold + '">' + vals[i] + '</td></tr>';
        }
        html += '</table></div></div>';
        html += '<table style="width:100%"><tr style="font-family:Tahoma;font-size:12pt"><td style="width:35%;padding-left:100px"><img src="' + base_url + 'assets/app/img/bg_frame.jpg"></td><td style="width:30%"></td><td style="width:35%">' + setting.kota + ', ' + handleTanggal(s.tahun_masuk) + '<br>Kepala Madrasah<br><br><br><br><u>' + setting.kepsek + '</u><br>Nip:</td></tr></table>';
        return html;
    }

    function createPageSikap(idSiswa) {
        var s = arrSiswa[idSiswa];
        var infoHtml = '<div style="padding:0 0.5cm 0.5cm 0.5cm"><p style="font-family:Tahoma;text-align:center;font-size:12pt"><b>PENCAPAIAN KOMPETENSI PESERTA DIDIK</b></p><hr><table style="width:100%;border:0"><tr style="font-family:Tahoma;font-size:9pt;vertical-align:top"><td style="width:20%">Nama</td><td>:</td><td style="width:40%"><b>' + s.nama + '</b></td><td style="width:20%">Kelas</td><td>:</td><td style="width:20%"><b>' + s.kelas + '</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td>No. Induk/NISN</td><td>:</td><td><b>' + handleNisn(s.nis, s.nisn) + '</b></td><td>Semester</td><td>:</td><td><b>' + smt + '</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td>Nama Madrasah</td><td>:</td><td><b>' + setting.sekolah + '</b></td><td>Tahun Pelajaran</td><td>:</td><td><b>' + tp + '</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td>Alamat</td><td>:</td><td colspan="3"><b>' + setting.alamat + ' ' + setting.kecamatan + ' ' + setting.kota + '</b></td></tr></table><hr><br>';

        var mkSikap = function(label, data) {
            return '<span style="font-family:Tahoma;font-size:10pt"><b>' + label + '</b></span><div style="margin-top:4px"><table style="width:100%;border:2px solid black;border-collapse:collapse"><tr style="font-family:Tahoma;font-size:9pt;text-align:center;background:#E6E7E9"><td style="width:20%;border:1px solid black"><b>Predikat</b></td><td style="border:1px solid black"><b>Deskripsi</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center"><b>' + handlePredikat(data.nilai) + '</b></td><td style="border:1px solid black;padding:6px">' + data.desk + '</td></tr></table></div><br>';
        };

        infoHtml += '<span style="font-family:Tahoma;font-size:10pt"><b>A. SIKAP</b></span><br>';
        infoHtml += mkSikap('1. Sikap Spiritual', siswaSelected.spritual);
        infoHtml += mkSikap('2. Sikap Sosial', siswaSelected.sosial);
        infoHtml += '</div>';

        var tableNilai = '<div style="padding:0.2cm 0.5cm 0 0.5cm"><span style="font-family:Tahoma;font-size:10pt"><b>B. PENGETAHUAN DAN KETERAMPILAN</b></span><br><table style="width:100%;border:2px solid black;border-collapse:collapse"><tr style="font-family:Tahoma;font-size:9pt;text-align:center;background:#E6E7E9"><td rowspan="2" style="width:5%;border:1px solid black"><b>NO</b></td><td rowspan="2" style="width:42%;border:1px solid black"><b>Mata Pelajaran</b></td><td rowspan="2" style="width:7%;border:1px solid black"><b>KKM</b></td><td colspan="2" style="border:1px solid black"><b>Pengetahuan</b></td><td colspan="2" style="border:1px solid black"><b>Keterampilan</b></td></tr><tr style="font-family:Tahoma;font-size:9pt;text-align:center;background:#E6E7E9"><td style="border:1px solid black"><b>Nilai</b></td><td style="border:1px solid black"><b>Predikat</b></td><td style="border:1px solid black"><b>Nilai</b></td><td style="border:1px solid black"><b>Predikat</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td colspan="7" style="border:1px solid black;padding:2px 8px"><b>Kelompok A (Umum)</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td rowspan="5" style="border:1px solid black;text-align:center;vertical-align:top;padding:6px">1</td><td style="border:1px solid black;padding:2px 6px"><b>Pendidikan Agama Islam</b></td>';
        for (var i = 0; i < 5; i++) tableNilai += '<td style="border:1px solid black"></td>';
        tableNilai += '</tr>';

        var no = 2,
            pos = 0,
            abjad = ['a', 'b', 'c', 'd'];
        var arrHph = s.hph,
            arrNilai = s.nilai_rapor,
            arrKKM = [];

        function getNilai(mapel, urutan, kelompok) {
            var kkm = '',
                pn = '',
                pp = '',
                kn = '',
                kp = '';
            $.each(arrNilai, function(i, n) {
                if (n.id_mapel == mapel.id_mapel) {
                    kkm = n.kkm == '' ? s.setting_rapor.kkm : n.kkm;
                    if (!inArray(kkm, arrKKM)) arrKKM.push(kkm);
                    pn = n.nilai == '0' ? '' : n.nilai;
                    pp = n.pred == '0' ? '' : n.pred;
                }
            });
            $.each(arrHph, function(i, h) {
                if (h.id_mapel == mapel.id_mapel) {
                    kn = h.k_nilai == '0' ? '' : h.k_nilai;
                    kp = h.k_pred == '0' ? '' : h.k_pred;
                }
            });
            return {
                kkm: kkm,
                pn: pn,
                pp: pp,
                kn: kn,
                kp: kp
            };
        }

        var mkRow = function(prefix, d) {
            return '<td style="border:1px solid black;padding:2px 6px">' + prefix + '</td><td style="border:1px solid black;text-align:center"><b>' + d.kkm + '</b></td><td style="border:1px solid black;text-align:center">' + d.pn + '</td><td style="border:1px solid black;text-align:center">' + d.pp + '</td><td style="border:1px solid black;text-align:center">' + d.kn + '</td><td style="border:1px solid black;text-align:center">' + d.kp + '</td></tr>';
        };

        $.each(arrMapel, function(k, m) {
            var d = getNilai(m);
            if (m.urutan == '1') {
                tableNilai += '<tr style="font-family:Tahoma;font-size:9pt">' + mkRow(abjad[pos] + '. ' + m.nama_mapel, d);
                pos++;
            } else if (m.urutan == '2') {
                tableNilai += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center">' + no + '</td>' + mkRow(m.nama_mapel, d);
                no++;
            }
        });

        tableNilai += '<tr style="font-family:Tahoma;font-size:9pt"><td colspan="7" style="border:1px solid black;padding:2px 8px"><b>Kelompok B (Umum)</b></td></tr>';
        $.each(arrMapel, function(k, m) {
            var d = getNilai(m);
            if (m.urutan == '3') {
                tableNilai += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center">' + no + '</td>' + mkRow(m.nama_mapel, d);
                no++;
            }
        });

        var totalMulok = 0;
        $.each(arrMapel, function(k, m) {
            if (m.kelompok == 'MULOK') totalMulok++;
        });
        if (totalMulok > 0) {
            tableNilai += '<tr style="font-family:Tahoma;font-size:9pt"><td rowspan="2" style="border:1px solid black;text-align:center;vertical-align:top;padding:6px">' + no + '</td><td colspan="6" style="border:1px solid black;vertical-align:top;padding:2px 6px"><b>Muatan Lokal *)</b></td></tr>';
            $.each(arrMapel, function(k, m) {
                var d = getNilai(m);
                if (m.kelompok == 'MULOK') {
                    tableNilai += '<tr style="font-family:Tahoma;font-size:9pt">' + mkRow(m.nama_mapel, d);
                }
            });
        }

        tableNilai += '</table></div>';
        return infoHtml + tableNilai;
    }

    function createPageDeskripsi(idSiswa) {
        var s = arrSiswa[idSiswa];
        var arrHph = s.hph;
        var html = '<div style="padding:0 0.5cm 0.5cm 0.5cm;margin-top:20px"><span style="font-family:Tahoma;font-size:10pt"><b>C. DESKRIPSI PENGETAHUAN DAN KETERAMPILAN</b></span><br><table style="width:100%;border:2px solid black;border-collapse:collapse;margin-top:6px"><tr style="font-family:Tahoma;font-size:9pt;text-align:center;background:#E6E7E9"><td style="width:5%;border:1px solid black"><b>NO</b></td><td style="width:20%;border:1px solid black"><b>Mata Pelajaran</b></td><td style="width:37%;border:1px solid black"><b>Pengetahuan</b></td><td style="width:38%;border:1px solid black"><b>Keterampilan</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td colspan="4" style="border:1px solid black;padding:6px"><b>Kelompok A (Umum)</b></td></tr><tr style="font-family:Tahoma;font-size:9pt"><td rowspan="5" style="border:1px solid black;text-align:center;vertical-align:top;padding:4px">1</td><td colspan="3" style="border:1px solid black;padding:4px">Pendidikan Agama Islam</td></tr>';

        var no = 2,
            pos = 0,
            abjad = ['a', 'b', 'c', 'd'];
        var mkDeskRow = function(prefix, pd, kd) {
            return '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;padding:4px">' + prefix + '</td><td style="border:1px solid black;padding:4px;font-size:8pt;line-height:1.3;text-align:justify">' + ellipsisText(pd) + '</td><td style="border:1px solid black;padding:4px;font-size:8pt;line-height:1.3;text-align:justify">' + ellipsisText(kd) + '</td></tr>';
        };

        $.each(arrMapel, function(k, m) {
            var pd = '',
                kd = '';
            $.each(arrHph, function(i, h) {
                if (h.id_mapel == m.id_mapel) {
                    kd = h.p_desk;
                    pd = h.k_desk;
                }
            });
            if (m.urutan == '1') {
                html += mkDeskRow(abjad[pos] + '. ' + m.nama_mapel, pd, kd);
                pos++;
            } else if (m.urutan == '2') {
                html += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center;padding:4px">' + no + '</td>' + mkDeskRow(m.nama_mapel, pd, kd).replace('<tr style="font-family:\'Tahoma\';font-size:9pt">', '');
                no++;
            }
        });

        html += '<tr style="font-family:Tahoma;font-size:9pt"><td colspan="4" style="border:1px solid black;padding:6px"><b>Kelompok B (Umum)</b></td></tr>';
        $.each(arrMapel, function(k, m) {
            var pd = '',
                kd = '';
            $.each(arrHph, function(i, h) {
                if (h.id_mapel == m.id_mapel) {
                    kd = h.p_desk;
                    pd = h.k_desk;
                }
            });
            if (m.urutan == '3') {
                html += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center">' + no + '</td><td style="border:1px solid black;padding:4px">' + m.nama_mapel + '</td><td style="border:1px solid black;padding:4px;font-size:8pt">' + ellipsisText(pd) + '</td><td style="border:1px solid black;padding:4px;font-size:8pt">' + ellipsisText(kd) + '</td></tr>';
                no++;
            }
        });

        var totalMulok = 0;
        $.each(arrMapel, function(k, m) {
            if (m.kelompok == 'MULOK') totalMulok++;
        });
        if (totalMulok > 0) {
            html += '<tr style="font-family:Tahoma;font-size:9pt"><td rowspan="2" style="border:1px solid black;text-align:center;vertical-align:top;padding:4px">' + no + '</td><td colspan="3" style="border:1px solid black;vertical-align:top;padding:4px"><b>Muatan Lokal *)</b></td></tr>';
            $.each(arrMapel, function(k, m) {
                var pd = '',
                    kd = '';
                $.each(arrHph, function(i, h) {
                    if (h.id_mapel == m.id_mapel) {
                        kd = h.p_desk;
                        pd = h.k_desk;
                    }
                });
                if (m.kelompok == 'MULOK') html += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;padding:4px">' + m.nama_mapel + '</td><td style="border:1px solid black;padding:4px;font-size:8pt">' + ellipsisText(pd) + '</td><td style="border:1px solid black;padding:4px;font-size:8pt">' + ellipsisText(kd) + '</td></tr>';
            });
        }
        html += '</table></div>';
        return html;
    }

    function buatTeksKenaikan(state, level) {
        var s1 = '',
            s2 = '',
            t1 = '<del>',
            t2 = '</del>';
        if (state == '0') {
            s1 = '<del>';
            s2 = '</del>';
            t1 = '';
            t2 = '';
        }
        var map = {
            '1': 'naik ke kelas II (dua)',
            'tinggal ke I': 'tinggal di kelas I (satu)',
            '2': 'naik ke kelas III (tiga)',
            '3': 'naik ke kelas IV (empat)',
            '4': 'naik ke kelas V (lima)',
            '5': 'naik ke kelas VI (enam)',
            '7': 'naik ke kelas VIII (delapan)',
            '8': 'naik ke kelas IX (sembilan)',
            '10': 'naik ke kelas XI (sebelas)',
            '11': 'naik ke kelas XII (duabelas)'
        };
        var lulus = ['6', '9', '12'];
        if ($.inArray(level, lulus) >= 0) return s1 + 'Lulus' + s2 + ' / ' + t1 + 'Tidak Lulus' + t2 + '<br>';
        return s1 + (map[level] || '') + s2 + '<br>' + t1 + 'tinggal di kelas' + t2 + '<br>';
    }

    function createPageekstra(idSiswa) {
        var s = arrSiswa[idSiswa];
        var html = '<div style="padding:0 0.5cm 0.5cm 0.5cm;margin-top:20px"><span style="font-family:Tahoma;font-size:10pt"><b>D. EKSTRAKURIKULER</b></span><br><table style="width:100%;border:2px solid black;border-collapse:collapse;margin-top:6px"><tr style="font-family:Tahoma;font-size:9pt;text-align:center;background:#E6E7E9"><td style="width:5%;border:1px solid black"><b>NO</b></td><td style="width:35%;border:1px solid black"><b>Kegiatan Ekstrakurikuler</b></td><td style="width:15%;border:1px solid black"><b>Nilai</b></td><td style="width:45%;border:1px solid black"><b>Keterangan</b></td></tr>';
        var no = 1;
        $.each(s.ekstra, function(k, v) {
            html += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center">' + no + '</td><td style="border:1px solid black;padding-left:4px">' + v.nama_ekstra + '</td><td style="border:1px solid black;text-align:center">' + v.pred + '</td><td style="border:1px solid black;padding-left:4px">' + v.desk + '</td></tr>';
            no++;
        });
        for (var i = no; i < 4; i++) {
            html += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center">' + no + '</td><td style="border:1px solid black"></td><td style="border:1px solid black"></td><td style="border:1px solid black"></td></tr>';
            no++;
        }
        html += '</table><br>';

        html += '<span style="font-family:Tahoma;font-size:10pt"><b>E. PRESTASI</b></span><br><table style="width:100%;border:2px solid black;border-collapse:collapse;margin-top:6px"><tr style="font-family:Tahoma;font-size:9pt;text-align:center;background:#E6E7E9"><td style="width:5%;border:1px solid black"><b>NO</b></td><td style="width:35%;border:1px solid black"><b>Jenis Kegiatan</b></td><td style="width:60%;border:1px solid black"><b>Deskripsi</b></td></tr>';
        no = 1;
        $.each(s.prestasi, function(p, v) {
            html += '<tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;text-align:center">' + no + '</td><td style="border:1px solid black">' + v.nilai + '</td><td style="border:1px solid black">' + v.desk + '</td></tr>';
            no++;
        });
        html += '</table><br>';

        var sRank = parseInt(s.rank.rank) > 5 ? '--' : s.rank.rank;
        var ssakit = s.absen.s == '' ? '0' : s.absen.s;
        var sizin = s.absen.i == '' ? '0' : s.absen.i;
        var salpa = s.absen.a == '' ? '0' : s.absen.a;

        html += '<span style="font-family:Tahoma;font-size:10pt"><b>G. CATATAN WALI KELAS</b></span><br><table style="width:100%;border:1px solid black;border-collapse:collapse;margin-top:6px"><tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;padding-left:6px"><table><tr><td style="vertical-align:top;width:12%">Ranking ke: </td><td>' + sRank + '. ' + s.rank.saran + '</td></tr><tr><td style="vertical-align:top">Saran-saran: </td><td>' + s.saran + '</td></tr></table></td></tr></table>';
        html += '<br><span style="font-family:Tahoma;font-size:10pt"><b>H. TANGGAPAN ORANG TUA/WALI</b></span><table style="width:100%;border:1px solid black;border-collapse:collapse;margin-top:6px"><tr><td style="height:40px;border:1px solid black"></td></tr></table>';
        html += '<br><span style="font-family:Tahoma;font-size:10pt"><b>I. KETIDAKHADIRAN</b></span><div style="display:flex;align-items:flex-start"><table style="width:45%;border:1px solid black;border-collapse:collapse;margin-top:6px"><tr style="font-family:Tahoma;font-size:9pt"><td style="width:70%;border:1px solid black;padding-left:6px">Sakit</td><td style="border:1px solid black;padding-left:6px">' + ssakit + ' hari</td></tr><tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;padding-left:6px">Izin</td><td style="border:1px solid black;padding-left:6px">' + sizin + ' hari</td></tr><tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;padding-left:6px">Tanpa Keterangan</td><td style="border:1px solid black;padding-left:6px">' + salpa + ' hari</td></tr></table>';

        if (s.smt == 'II (dua)') {
            html += '<div style="width:7%"></div><table style="width:48%;border:1px solid black;border-collapse:collapse;margin-top:6px"><tr style="font-family:Tahoma;font-size:9pt"><td style="border:1px solid black;padding-left:6px"><b>Keputusan:</b><br>Berdasarkan pencapaian kompetensi pada semester ke-1 dan ke-2, peserta didik ditetapkan:<br>' + buatTeksKenaikan(s.naik, s.level) + '<small>*) Coret yang tidak perlu.</small></td></tr></table>';
        }
        html += '</div>';

        html += '<br><table style="width:100%"><tr style="font-family:Tahoma;font-size:9pt"><td style="width:35%">Mengetahui:<br>Orang Tua/Wali<br><br><br><br><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td><td style="width:30%"></td><td style="width:35%">' + setting.kota + ', ' + handleTitiMangsa(s.setting_rapor.tgl_rapor_akhir) + '<br>Wali Kelas<br><br><br><br><u>' + s.wali_kelas + '</u><br>Nip:</td></tr></table>';
        html += '<div style="display:flex;justify-content:center"><table style="width:500px"><tr style="font-family:Tahoma;font-size:9pt"><td style="padding:4px 4px 4px 180px">Mengetahui,<br>Kepala Madrasah<br><br><br><br><br><u>' + setting.kepsek + '</u><br>Nip:</td></tr></table></div></div>';
        return html;
    }

    function preview(idSiswa) {
        siswaSelected = arrSiswa[idSiswa];
        arrMapel = siswaSelected.setting_mapel;
        $('#loading').removeClass('d-none');
        $('#nama-siswa').html('<b>' + siswaSelected.nama + '</b>');
        $('#nis-siswa').html(handleNisn(siswaSelected.nis, siswaSelected.nisn));
        $('#print-info').html(createPageInfo());
        $('#print-data').html(createPageIdentitas(idSiswa));
        $('#print-sikap-nilai').html(createPageSikap(idSiswa));
        $('#print-deskripsi1').html(createPageDeskripsi(idSiswa));
        $('#print-deskripsi2').html(createPageekstra(idSiswa));
        setTimeout(function() {
            $('#loading').addClass('d-none');
            $('#empty').addClass('d-none');
            $('#print-sampul, #print-info, #print-data, #print-sikap-nilai, #print-deskripsi1, #print-deskripsi2').removeClass('d-none');
            $('.btn').removeAttr('disabled');
        }, 500);
    }

    function cetakSampul() {
        $('#print-sampul').print(siswaSelected.nama);
    }

    function cetakInfo() {
        $('#print-info').print(siswaSelected.nama);
    }

    function cetakData() {
        $('#print-data').print(siswaSelected.nama);
    }

    function cetakRapor() {
        var div = '<div>' + $('#print-sikap-nilai').html() + '<div style="page-break-after:always"></div>' + $('#print-deskripsi1').html() + '<div style="page-break-after:always"></div>' + $('#print-deskripsi2').html() + '</div>';
        setTimeout(function() {
            $(div).print(siswaSelected.nama);
        }, 500);
    }

    function cetakSemua() {
        var div = '<div>' + $('#print-sampul').html() + '<div style="page-break-after:always"></div>' + $('#print-info').html() + '<div style="page-break-after:always"></div>' + $('#print-data').html() + '<div style="page-break-after:always"></div>' + $('#print-sikap-nilai').html() + '<div style="page-break-after:always"></div>' + $('#print-deskripsi1').html() + '<div style="page-break-after:always"></div>' + $('#print-deskripsi2').html() + '</div>';
        setTimeout(function() {
            $(div).print(siswaSelected.nama);
        }, 500);
    }

    function restoreNilai() {
        swal.fire({
            title: 'Mengumpulkan Nilai',
            text: 'Silahkan tunggu....',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
                swal.showLoading();
            }
        });
        $.ajax({
            url: base_url + 'bukurapor/restorenilai',
            type: 'GET',
            success: function(data) {
                swal.close();
            },
            error: function() {
                swal.fire({
                    title: 'ERROR',
                    text: 'Data Rapor gagal dipindahkan',
                    icon: 'error'
                });
            }
        });
    }

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
            if (t && s) window.location.href = base_url + 'bukurapor?tahun=' + t + '&semester=' + s + '&kelas=' + k;
        }
        opsiTahun.change(go);
        opsiSmt.change(go);
        opsiKelas.change(go);

        $('.siswa').click(function(e) {
            e.stopPropagation();
            e.preventDefault();
            e.stopImmediatePropagation();
            $('.siswa').removeClass('active');
            $(this).toggleClass('active');
        });
    });
</script>
