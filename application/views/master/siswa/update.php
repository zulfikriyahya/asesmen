<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/siswa.css">

<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-sm-flex justify-content-between align-items-center mb-3">
                <h1><?= $subjudul ?></h1>
                <a href="<?= base_url('datasiswa') ?>" class="btn btn-danger-glass btn-sm">
                    <i class="fas fa-arrow-circle-left mr-1"></i>Kembali
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="alert-glass mb-4">
                <strong>Catatan!</strong> Untuk import data dari file Excel, silahkan download templatenya terlebih dahulu.
            </div>

            <!-- Download Data Siswa -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Download Data Siswa</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (count($kelas) > 0): ?>
                            <?php foreach ($kelas as $id => $kls): ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                                    <div onclick="download(this)" data-kelas="<?= $kls ?>" data-id="<?= $id ?>"
                                        class="btn-download-kelas">
                                        <i class="fas fa-download mb-1 d-block"></i><?= $kls ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert-glass">Belum ada data siswa dan kelas.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Download Foto Siswa -->
            <div class="glass-card mb-4">
                <div class="card-header">
                    <h6 class="card-title">Download Foto Siswa</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (count($kelas) > 0): ?>
                            <?php foreach ($kelas as $id => $kls): ?>
                                <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                                    <div onclick="downloadWord(this)" data-kelas="<?= $kls ?>" data-id="<?= $id ?>"
                                        class="btn-download-kelas">
                                        <i class="fas fa-download mb-1 d-block"></i><?= $kls ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert-glass">Belum ada data siswa dan kelas.</div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Hidden export table -->
            <div class="d-none" id="download-tbl">
                <table style="width:100%;font-size:11pt;line-height:1.3;border:1px solid black;border-collapse:collapse;border-spacing:0;page-break-after:always">
                    <thead>
                        <tr id="header-table" data-height="60" style="background-color:lightgrey"></tr>
                    </thead>
                    <tbody id="body-table"></tbody>
                </table>
            </div>

            <!-- Upload Panel -->
            <div class="row mb-4">
                <div class="col-sm-6 mb-3">
                    <div class="glass-card h-100">
                        <div class="card-header">
                            <h6 class="card-title">Update Data (Excel)</h6>
                        </div>
                        <div class="card-body">
                            <?= form_open_multipart('', array('id' => 'formPreviewExcel')); ?>
                            <input accept=".xlsx" type="file" id="input-file-events-excel" name="upload_file" class="dropify" />
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="glass-card h-100">
                        <div class="card-header">
                            <h6 class="card-title">Update Foto (Word)</h6>
                        </div>
                        <div class="card-body">
                            <?= form_open_multipart('', array('id' => 'formPreviewWord')); ?>
                            <input accept=".docx" type="file" id="input-file-events-word" name="upload_file" class="dropify" />
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview & Upload -->
            <div class="glass-card mb-4">
                <?= form_open('', array('id' => 'formUpload')); ?>
                <div class="card-header">
                    <h6 class="card-title">Preview</h6>
                    <button id="submit-excel" name="preview" type="submit" class="btn btn-cyan btn-sm" disabled="disabled">
                        <i class="fas fa-cloud-upload-alt mr-1"></i>Upload
                    </button>
                </div>
                <?= form_close(); ?>
                <div class="card-body table-responsive" id="file-preview">
                    <span style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#64748b">
                        Pastikan anda telah mengisi format yang telah disediakan.
                    </span>
                </div>
            </div>

        </div>
    </section>
</div>

<script src="<?= base_url() ?>/assets/app/js/mammoth.browser.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/FileSaver.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/excel/exceljs.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/excel/js-excel-template.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/html-docx.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/convert-area.js"></script>

<script>
    var typeImport = '<?= $tipe ?>';
    var formDataSiswa, isUploadType;

    $(document).ready(function() {
        ajaxcsrf();

        var drEvente = $('.dropify').dropify();

        drEvente.on('dropify.beforeClear', function(event, element) {
            return confirm("Hapus \"" + element.file.name + "\" ?");
        });

        drEvente.on('dropify.afterClear', function() {
            $('#submit-excel').attr('disabled', 'disabled');
            formDataSiswa = null;
        });

        drEvente.on('dropify.errors', function() {
            showDangerToast("File rusak atau tidak didukung");
        });

        $('#input-file-events-excel').on('change', async function(e) {
            var files = e.target.files || [];
            if (!files.length) return;
            $('#file-preview').html('<span style="font-family:\'Lexend\',sans-serif;font-size:0.82rem;color:#64748b">Memuat data...</span>');
            $('#submit-excel').attr('disabled', 'disabled');
            formDataSiswa = null;
            isUploadType = null;
            const jsonData = await getDataFromExcel(files[0]);
            let tbl = $('<table class="mb-4 w-100"></table>');
            createTable(jsonData[jsonData.sheets[0]], tbl);
        });

        $('#input-file-events-word').on('change', async function(e) {
            var files = e.target.files || [];
            if (!files.length) return;
            $('#file-preview').html('<span style="font-family:\'Lexend\',sans-serif;font-size:0.82rem;color:#64748b">Memuat file...</span>');
            $('#submit-excel').attr('disabled', 'disabled');
            formDataSiswa = null;
            isUploadType = null;
            parseWordDocxFile(files[0]);
        });

        $('#formUpload').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (!formDataSiswa || !isUploadType) return;
            var url = isUploadType === 'excel' ? "datasiswa/updateall" : "datasiswa/update_foto";

            swal.fire({
                text: "Silahkan tunggu....",
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });

            $.ajax({
                url: base_url + url,
                type: "POST",
                data: formDataSiswa,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function(data) {
                    if (data.status) {
                        swal.fire({
                                title: "Berhasil",
                                text: "Data siswa berhasil diimpor",
                                icon: "success"
                            })
                            .then(r => {
                                if (r.value) window.history.back();
                            });
                    } else {
                        var arrN = [],
                            arrNis = [],
                            arrU = [];
                        $.each(data.errors, function(idx, o) {
                            if (o.nisn !== "") arrN.push("<br />" + idx + ": " + o.nisn);
                            if (o.nis !== "") arrNis.push("<br />" + idx + ": " + o.nis);
                            if (o.username !== "") arrU.push("<br />" + idx + ": " + o.username);
                        });
                        var err = arrN.length ? `<b>${arrN.join("' ")}</b>` : '';
                        err += arrNis.length ? `<br /><b>${arrNis.join("' ")}</b>` : '';
                        err += arrU.length ? `<br /><b>${arrU.join("' ")}</b>` : '';
                        swal.fire({
                            title: "Gagal",
                            html: 'Cek kembali siswa berikut:<br />' + err,
                            icon: "error"
                        });
                    }
                },
                error: function(xhr) {
                    $('#file-preview').html(xhr.responseText);
                    swal.fire({
                        title: "Gagal",
                        html: 'Gagal menyimpan data',
                        icon: "error"
                    });
                }
            });
            return false;
        });
    });

    function parseWordDocxFile(file) {
        swal.fire({
            title: "Memuat file",
            text: "Silahkan tunggu....",
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => swal.showLoading()
        });
        var reader = new FileReader();
        reader.onloadend = function() {
            mammoth.convertToHtml({
                arrayBuffer: reader.result
            }).then(function(resultObject) {
                var showDiv = $('#file-preview');
                showDiv.html(resultObject.value);
                setTimeout(function() {
                    formDataSiswa = new FormData($('#formUpload')[0]);
                    showDiv.children().not("table").remove();
                    showDiv.children('table').each(function() {
                        $(this).addClass('table table-bordered w-100');
                        var $trs = $(this).find('tr');
                        $trs.splice(0, 1);
                        $trs.each(function(index, tr) {
                            if ($(tr).parent().closest('td').length === 0) {
                                var id = $(this).find("td:eq(1)").text().trim();
                                var nis = $(this).find("td:eq(2)").text().trim();
                                var nama = $(this).find("td:eq(3)").text().trim();
                                var foto = $(this).find("td:eq(5)").find('img');
                                foto.each(function() {
                                    $(this).attr('width', '100');
                                });
                                if (id && nis) {
                                    formDataSiswa.append('siswa[' + index + '][id]', id);
                                    formDataSiswa.append('siswa[' + index + '][nis]', nis);
                                    formDataSiswa.append('siswa[' + index + '][nama]', nama);
                                    if (foto.length > 0) {
                                        var ftsiswa = $(foto[0]).attr('src');
                                        var ext = ftsiswa.substring("data:image/".length, ftsiswa.indexOf(";base64"));
                                        var base64 = ftsiswa.split(';base64')[1];
                                        formDataSiswa.append('siswa[' + index + '][foto]', base64);
                                        formDataSiswa.append('siswa[' + index + '][ext]', ext);
                                    }
                                    $('#submit-excel').removeAttr('disabled');
                                } else {
                                    $(this).remove();
                                }
                            }
                        });
                    });
                    swal.close();
                    isUploadType = 'word';
                }, 500);
            });
        };
        reader.readAsArrayBuffer(file);
    }

    function createTable(list, selector) {
        let cols = Headers(list.header, selector);
        let len = Math.min(list.rows.length, 50);
        for (let i = 0; i < len; i++) {
            let row = $('<tr/>');
            for (let c = 0; c < cols.length; c++) {
                let val = list.rows[i][cols[c]] || '';
                row.append(c === 3 || c > 8 ?
                    $('<td/>').html(val) :
                    $('<td class="text-center"/>').html(val));
            }
            $(selector).append(row);
        }
        $('#file-preview').prepend(selector);
        if (list.rows.length > 0) {
            $('#submit-excel').removeAttr('disabled');
            formDataSiswa = new FormData($('#formUpload')[0]);
            list.rows.forEach(function(siswa, ind) {
                for (const key in siswa) {
                    if (key) formDataSiswa.append('siswa[' + ind + '][' + key + ']', siswa[key]);
                }
            });
            isUploadType = 'excel';
        } else {
            $('#submit-excel').attr('disabled', 'disabled');
        }
    }

    function Headers(list, selector) {
        let columns = [];
        let header = $('<tr/>');
        for (let i = 0; i < list.length; i++) {
            let row = list[i];
            for (let k in row) {
                if (k === 'label') header.append($('<th class="text-center align-middle"/>').html(row[k]));
                else if ($.inArray(row[k], columns) === -1) columns.push(row[k]);
            }
        }
        $(selector).append(header);
        return columns;
    }

    function getDataFromExcel(file) {
        return new Promise((resolve, reject) => {
            const wb = new ExcelJS.Workbook();
            const reader = new FileReader();
            reader.onload = async () => {
                try {
                    wb.xlsx.load(reader.result).then(workbook => {
                        let dataFiles = {};
                        workbook.eachSheet((sheet) => {
                            if (!dataFiles['sheets']) dataFiles['sheets'] = [];
                            dataFiles['sheets'].push(sheet.name);
                            let cols = {
                                name: sheet.name,
                                header: [],
                                rows: []
                            };
                            sheet.eachRow({
                                includeEmpty: true
                            }, (row, rowIndex) => {
                                let obj = {};
                                for (let i = 0; i < row.values.length; i++) {
                                    if (rowIndex === 1) {
                                        if (row.values[i]) {
                                            let val = isRichValue(row.values[i]) ? richToString(row.values[i]) : (row.values[i] || 'val-' + i);
                                            if (val && val.includes('|')) val = val.split('|')[0];
                                            cols.header.push({
                                                label: val,
                                                value: i
                                            });
                                        }
                                    } else {
                                        let val = isRichValue(row.values[i]) ? richToString(row.values[i]) : (row.values[i] || '');
                                        if ([2, 3, 14, 19, 20, 30, 36, 42].includes(i)) val = val.toString().replace("'", "");
                                        obj[i] = val;
                                    }
                                }
                                cols.rows.push(obj);
                            });
                            cols.rows = removeEmptyObjects(cols.rows);
                            dataFiles[sheet.name] = cols;
                        });
                        resolve(dataFiles);
                    });
                } catch (err) {
                    reject(err);
                }
            };
            reader.onerror = (e) => reject(e);
            reader.readAsArrayBuffer(file);
        });
    }

    function removeEmptyObjects(arr) {
        return arr.filter(el => {
            delete el.undefined;
            return Object.keys(el).length !== 0;
        });
    }

    function isRichValue(v) {
        return Boolean(v && Array.isArray(v.richText));
    }

    function richToString(r) {
        return r.richText.map(({
            text
        }) => text).join('|');
    }

    async function downloadTemplate(kls, res) {
        const response = await fetch(base_url + 'uploads/import/format/format_update_siswa.xlsx');
        const arrayBuffer = await response.arrayBuffer();
        const excelTemplate = await JsExcelTemplate.fromArrayBuffer(arrayBuffer);
        excelTemplate.set("siswa", res.siswa);
        const blob = await excelTemplate.toBlob();
        saveAs(blob, `Update Data Siswa ${kls}.xlsx`);
    }

    function download(btn) {
        var idKls = $(btn).data('id'),
            kls = $(btn).data('kelas');
        $.ajax({
            url: base_url + "datasiswa/downloaddata/" + idKls,
            method: "GET",
            success: function(result) {
                downloadTemplate(kls, result);
            },
            error: function(xhr) {
                swal.fire({
                    title: "Error",
                    text: JSON.parse(xhr.responseText).Message,
                    icon: "error"
                });
            }
        });
    }

    function downloadWord(btn) {
        var idKls = $(btn).data('id'),
            kls = $(btn).data('kelas');
        $.ajax({
            url: base_url + "datasiswa/downloaddata/" + idKls,
            method: "GET",
            success: function(result) {
                if (result.siswa) prosesResult(result.siswa, kls);
            },
            error: function(xhr) {
                swal.fire({
                    title: "Error",
                    text: JSON.parse(xhr.responseText).Message,
                    icon: "error"
                });
            }
        });
    }

    function prosesResult(dataSiswa, kelas) {
        swal.fire({
            text: "Menyiapkan data foto kelas " + kelas + " ....",
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => swal.showLoading()
        });

        var headerTbl = [{
                title: "No.",
                key: 'number',
                isCenter: true,
                width: '60px'
            },
            {
                title: "ID",
                key: 'id_siswa',
                isCenter: true,
                width: '60px'
            },
            {
                title: "NIS",
                key: 'nis',
                isCenter: true,
                width: 'auto'
            },
            {
                title: "NAMA SISWA",
                key: 'nama',
                isCenter: false,
                width: 'auto'
            },
            {
                title: "JENIS KELAMIN (L/P)",
                key: 'jenis_kelamin',
                isCenter: true,
                width: 'auto'
            },
            {
                title: "FOTO",
                key: 'foto',
                isCenter: true,
                width: '100px'
            },
        ];

        var thead = '',
            tbody = '';
        headerTbl.forEach(function(h) {
            thead += '<th style="width:' + h.width + ';border:1px solid black;border-collapse:collapse;text-align:center;font-weight:bold;">' +
                '<span style="margin:0;padding:4px">' + h.title + '</span></th>';
        });
        dataSiswa.forEach(function(siswa, ind) {
            tbody += '<tr>';
            headerTbl.forEach(function(h) {
                if (h.key === 'number') {
                    tbody += '<td style="border:1px solid black;text-align:center;padding:4px">' + (ind + 1) + '</td>';
                } else if (h.key === 'foto') {
                    tbody += '<td id="avatar-' + siswa.nis + '" style="border:1px solid black;padding:4px">' +
                        '<img class="avatar" src="' + base_url + siswa.foto + '" alt="foto" style="max-width:100px;height:auto"></td>';
                } else {
                    var center = h.isCenter ? 'text-align:center;' : '';
                    tbody += '<td style="border:1px solid black;padding:4px;' + center + '">' + siswa[h.key] + '</td>';
                }
            });
            tbody += '</tr>';
        });

        $('#header-table').html(thead);
        $('#body-table').html(tbody);

        var count = 0;
        $('.avatar').each(async function(i, v) {
            var loaded = () => new Promise((resolve) => {
                $(v).on("error", function() {
                        $(this).remove();
                        resolve({
                            pos: i,
                            error: true
                        });
                    })
                    .on('load', function() {
                        resolve({
                            pos: i,
                            error: false
                        });
                    });
            });
            await loaded();
            count++;
            if (count === dataSiswa.length) saveWord(kelas);
        });
    }

    function saveWord(kelas) {
        swal.close();
        var contentDocument = $('#download-tbl').convertToHtmlFile('detail', '');
        var content = '<!DOCTYPE html>' + contentDocument.documentElement.outerHTML;
        var converted = htmlDocx.asBlob(content, {
            size: 'A4',
            margins: {
                top: 700,
                bottom: 700,
                left: 1000,
                right: 1000
            }
        });
        saveAs(converted, `Foto Siswa Kelas ${kelas}.docx`);
    }
</script>
