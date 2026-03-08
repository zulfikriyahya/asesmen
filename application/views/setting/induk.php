<link rel="stylesheet" href="<?= base_url('assets/app/css/setting.css') ?>">
<style>
    .sidebar-induk {
        background: var(--surface-1);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    .sidebar-induk .sidebar-header {
        background: linear-gradient(90deg, rgba(6, 182, 212, 0.2), rgba(14, 165, 233, 0.1));
        border-bottom: 1px solid var(--glass-border);
        padding: 12px 16px;
    }

    .sidebar-induk .sidebar-header h6 {
        font-family: 'Lexend', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--clr-accent);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0;
    }

    .sidebar-induk .search-wrap {
        padding: 10px 12px;
        border-bottom: 1px solid var(--glass-border);
    }

    .sidebar-induk .siswa-list {
        height: 1000px;
        overflow-y: auto;
        padding: 6px 0;
    }

    .sidebar-induk .siswa-list::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-induk .siswa-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-induk .siswa-list::-webkit-scrollbar-thumb {
        background: var(--clr-primary-dk);
        border-radius: 4px;
    }

    .preview-container {
        background: var(--surface-3);
        border-radius: var(--radius-md);
        padding: 16px;
        height: 300mm;
        overflow-y: auto;
        display: flex;
        justify-content: center;
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
                    <div class="sidebar-induk">
                        <div class="sidebar-header">
                            <h6>Cari Siswa</h6>
                        </div>
                        <div class="search-wrap">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <input type="text" class="form-control" id="cari-siswa" placeholder="Ketik nama...">
                            </div>
                        </div>
                        <div class="siswa-list">
                            <ul id="list-siswa" class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview">
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
                    <div class="card my-shadow">
                        <div class="card-header bg-orange">
                            <div class="card-title">
                                <h6 class="text-bold"><?= $subjudul ?></h6>
                            </div>
                            <div class="card-tools">
                                <button class="btn btn-warning btn-sm" onclick="editSiswa()" disabled>
                                    <i class="fa fa-edit mr-1"></i>Edit
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="cetakSemua()" disabled>
                                    <i class="fa fa-print mr-1"></i>Cetak
                                </button>
                            </div>
                        </div>
                        <div class="card-body text-dark p-2">
                            <div class="preview-container">
                                <div id="zoom" style="transform: scale(0.9); transform-origin: top center">
                                    <div id="print-preview">
                                        <div id="empty"
                                            style="display:flex;justify-content:center;align-items:center;width:210mm;height:297mm;color:var(--text-muted);font-family:'Lexend',sans-serif;font-size:0.9rem;">
                                            Silahkan pilih siswa
                                        </div>
                                        <div id="print-data-1" class="border my-shadow mb-3 d-none"
                                            style="background:white;width:210mm;height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-data-2" class="border my-shadow mb-3 d-none"
                                            style="background:white;width:210mm;height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-data-3" class="border my-shadow mb-3 d-none"
                                            style="background:white;width:210mm;height:297mm;padding:5mm 10mm"></div>
                                        <div id="print-nilai" class="border my-shadow mb-3 d-none pb-5"
                                            style="background:white;width:210mm;min-height:297mm;padding:5mm 10mm"></div>
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
    var klsSelected = '<?= isset($kelas_selected) ? $kelas_selected : '' ?>';
    var siswas = JSON.parse(JSON.stringify(<?= isset($siswas)   ? json_encode($siswas)   : '[]' ?>));
    var arrSiswa = JSON.parse(JSON.stringify(<?= isset($detail)   ? json_encode($detail)   : '[]' ?>));
    var setting = JSON.parse(JSON.stringify(<?= json_encode($setting) ?>));
    var test = JSON.parse(JSON.stringify(<?= isset($arr_test) ? json_encode($arr_test) : '[]' ?>));
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

    function handleNisn(nis, nisn) {
        var s = '';
        if (handleNull(nis) != '-') s += handleNull(nis);
        if (handleNull(nisn) != '-') s += ' / ' + handleNull(nisn);
        return s;
    }

    function handleTanggal(tgl) {
        var b = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        if (handleNull(tgl) == '-') return '';
        var p = tgl.split('-');
        return p[2] + ' ' + b[Math.abs(p[1])] + ' ' + p[0];
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

    function createPage1(idSiswa) {
        var s = arrSiswa[idSiswa];
        var html = '<p class="text-center text-dark mt-4" style="text-align:center;font-family:Arial;font-size:16pt;font-weight:bold">LEMBAR BUKU INDUK SISWA</p>';
        html += '<table style="margin-top:40px;font-size:11pt;font-weight:bold;font-family:Tahoma;width:100%;border:0">';
        html += '<tr><td style="width:40%">Nomor Induk Siswa</td><td>:</td><td>' + handleNull(s.nis) + '</td></tr>';
        html += '<tr><td>Nomor Induk Siswa Nasional</td><td>:</td><td>' + handleNull(s.nisn) + '</td></tr></table><br>';

        html += '<table style="font-family:Tahoma;width:100%;border:0;table-layout:fixed;line-height:1"><tr><td style="width:auto"><table style="font-family:Tahoma;border:0">';
        var nomor = 1;
        $.each(s.page1, function(abjad, header) {
            html += '<tr style="font-size:11pt;font-weight:bold"><td style="width:3%;border:0;padding-top:12px">' + abjad + '.</td><td colspan="5" style="padding-top:12px">' + header.title + '</td></tr>';
            var tableFisik = '';
            if (header.table != null) {
                tableFisik = '<td colspan="5"><table style="width:100%;border:1px solid black;border-collapse:collapse;font-size:10pt"><tr><td style="border:1px solid black;text-align:center">Tahun</td>';
                var cols = 0;
                $.each(header.table.tahun, function(k, t) {
                    cols++;
                    tableFisik += '<td style="border:1px solid black;text-align:center">' + t + '</td>';
                });
                var emptyRow = function(label) {
                    tableFisik += '</tr><tr><td style="border:1px solid black;text-align:center">' + label + '</td>';
                    for (var i = 0; i < cols; i++) tableFisik += '<td style="border:1px solid black;text-align:center"></td>';
                };
                emptyRow('Berat Badan');
                emptyRow('Tinggi Badan');
                emptyRow('Penyakit');
                emptyRow('Kelainan Jasmani');
                tableFisik += '</tr></table></td></tr>';
            }
            $.each(header.value, function(judul, isi) {
                if (isi != null && typeof isi === 'object') {
                    html += '<tr><td></td><td style="width:4%;border:0;vertical-align:top">' + nomor + '.</td><td colspan="2" style="vertical-align:top;width:30%">' + judul + '</td><td style="width:2%;border:0;vertical-align:top">:</td><td></td></tr>';
                    var ab = ['a', 'b', 'c', 'd', 'e', 'f'],
                        na = 0;
                    $.each(isi, function(sub, subisi) {
                        var v = sub == 'Tanggal' ? handleTanggal(subisi) : handleNull(subisi);
                        html += '<tr><td></td><td></td><td style="width:3%;border:0;vertical-align:top">' + ab[na] + '.</td><td style="vertical-align:top">' + sub + '</td><td style="vertical-align:top">: </td><td style="vertical-align:top">' + v + '</td></tr>';
                        na++;
                    });
                } else {
                    html += '<tr><td></td><td style="width:4%;border:0;vertical-align:top">' + nomor + '.</td><td colspan="2" style="vertical-align:top">' + judul + '</td><td style="vertical-align:top">: </td>';
                    if (isi == '[table]') html += '</tr><tr><td>' + tableFisik + '</td>';
                    else html += '<td style="vertical-align:top">' + handleNull(isi) + '</td>';
                    html += '</tr>';
                }
                nomor++;
            });
        });
        html += '</table></td><td style="width:30mm;vertical-align:top">';
        ['Foto ketika masuk<br>3 x 4', 'Foto ketika bersekolah<br>3 x 4', 'Foto ketika lulus<br>3 x 4'].forEach(function(label) {
            html += '<div style="display:block;width:30mm;height:35mm;border:1px solid #0a0a0a;text-align:center;padding-top:10px">' + label + '</div><div style="display:block;width:30mm;height:5cm"></div>';
        });
        html += '</td></tr></table>';
        return html;
    }

    function buildGenericPage(pageData) {
        var html = '<table style="font-family:Tahoma;width:100%;border:0;table-layout:fixed"><tr><td><table style="font-family:Tahoma;border:0">';
        var nomor = 1;
        $.each(pageData, function(abjad, header) {
            html += '<tr style="font-size:12pt;font-weight:bold"><td style="width:3%;border:0;padding-top:12px">' + abjad + '.</td><td colspan="5" style="padding-top:12px">' + header.title + '</td></tr>';
            $.each(header.value, function(judul, isi) {
                if (isi != null && typeof isi === 'object') {
                    html += '<tr><td></td><td style="width:4%;border:0;vertical-align:top">' + nomor + '.</td><td colspan="2" style="vertical-align:top;width:30%">' + judul + '</td><td style="width:2%;border:0;vertical-align:top">:</td><td></td></tr>';
                    var ab = ['a', 'b', 'c', 'd', 'e', 'f'],
                        na = 0;
                    $.each(isi, function(sub, subisi) {
                        var v = sub == 'Tanggal' ? handleTanggal(subisi) : handleNull(subisi);
                        html += '<tr><td></td><td></td><td style="width:3%;border:0;vertical-align:top">' + ab[na] + '.</td><td style="vertical-align:top">' + sub + '</td><td style="vertical-align:top">: </td><td style="vertical-align:top">' + v + '</td></tr>';
                        na++;
                    });
                } else {
                    html += '<tr><td></td><td style="width:4%;border:0;vertical-align:top">' + nomor + '.</td><td colspan="2" style="vertical-align:top">' + judul + '</td><td style="vertical-align:top">: </td>';
                    html += header.tahun != null ? '<td style="vertical-align:top">' + header.tahun.join('<br>') + '</td>' : '<td style="vertical-align:top">' + handleNull(isi) + '</td>';
                    html += '</tr>';
                }
                nomor++;
            });
        });
        html += '</table></td></tr></table>';
        return html;
    }

    function createPage2(idSiswa) {
        return buildGenericPage(arrSiswa[idSiswa].page2);
    }

    function createPage3(idSiswa) {
        return buildGenericPage(arrSiswa[idSiswa].page3);
    }

    function preview(idSiswa) {
        siswaSelected = arrSiswa[idSiswa];
        arrMapel = siswaSelected.setting_mapel;
        $('#loading').removeClass('d-none');
        $('#print-data-1').html(createPage1(idSiswa));
        $('#print-data-2').html(createPage2(idSiswa));
        $('#print-data-3').html(createPage3(idSiswa));
        setTimeout(function() {
            $('#loading').addClass('d-none');
            $('#empty').addClass('d-none');
            $('#print-data-1, #print-data-2, #print-data-3, #print-nilai').removeClass('d-none');
            $('.btn').removeAttr('disabled');
        }, 500);
    }

    function cetakSemua() {
        var div = '<div>' + $('#print-data-1').html() + '<div style="page-break-after:always"></div>' + $('#print-data-2').html() + '<div style="page-break-after:always"></div>' + $('#print-data-3').html() + '<div style="page-break-after:always"></div>' + $('#print-nilai').html() + '</div>';
        setTimeout(function() {
            $(div).print(siswaSelected.nama);
        }, 500);
    }

    $(document).ready(function() {
        $('input#cari-siswa').quicksearch('ul#list-siswa li');

        $('.siswa').click(function(e) {
            e.stopPropagation();
            e.preventDefault();
            e.stopImmediatePropagation();
            $('.siswa').removeClass('active');
            $(this).toggleClass('active');
        });
    });
</script>
