<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/siswa.css">

<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1><?= $judul ?></h1>
                <a href="<?= base_url('datamapel') ?>" class="btn btn-danger-glass btn-sm" style="position:fixed;top:1rem;right:1.5rem;z-index:999">
                    <i class="fas fa-arrow-circle-left mr-1"></i>Batal
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="alert-glass mb-4">
                <strong>Catatan!</strong> Untuk import data dari file Excel, silahkan download templatenya terlebih dahulu.
            </div>

            <div class="row mb-4">
                <div class="col-sm-6 mb-3">
                    <div class="glass-card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">File Excel</h6>
                            <div class="card-tools">
                                <a href="<?= base_url('uploads/import/format/format_mapel.xlsx') ?>" class="btn btn-success-glass btn-sm">
                                    <i class="fas fa-download mr-1"></i>Download Template
                                </a>
                            </div>
                        </div>
                        <div class="card-body" style="padding:1.5rem">
                            <?= form_open_multipart('', array('id' => 'formPreviewExcel')); ?>
                            <label style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#94a3b8;margin-bottom:0.6rem;display:block">Pilih file Excel</label>
                            <input type="file" id="input-file-events-excel" name="upload_file" class="dropify" />
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="glass-card h-100">
                        <div class="card-header">
                            <h6 class="card-title mb-0">File Word</h6>
                            <div class="card-tools">
                                <a href="<?= base_url('uploads/import/format/format_mapel.docx') ?>" class="btn btn-success-glass btn-sm">
                                    <i class="fas fa-download mr-1"></i>Download Template
                                </a>
                            </div>
                        </div>
                        <div class="card-body" style="padding:1.5rem">
                            <?= form_open_multipart('', array('id' => 'formPreviewWord')); ?>
                            <label style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#94a3b8;margin-bottom:0.6rem;display:block">Pilih file Word</label>
                            <input type="file" id="input-file-events-word" name="upload_file" class="dropify" />
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card mb-4">
                <?= form_open('', array('id' => 'formUpload')); ?>
                <div class="card-header">
                    <h6 class="card-title mb-0">Preview</h6>
                    <div class="card-tools">
                        <button name="preview" type="submit" class="btn btn-cyan btn-sm">
                            <i class="fas fa-cloud-upload-alt mr-1"></i>Upload
                        </button>
                    </div>
                </div>
                <?= form_close(); ?>
                <div class="card-body" id="file-preview" style="padding:1.5rem;min-height:120px">
                    <p style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#64748b;margin:0">
                        Pastikan anda telah mengisi format yang telah disediakan.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?= base_url() ?>/assets/app/js/mammoth.browser.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/FileSaver.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/excel/exceljs.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/excel/js-excel-template.min.js"></script>
<script src="<?= base_url() ?>/assets/app/js/jquery.htmlparser.min.js"></script>

<script>
    let formDataMapel;

    $(document).ready(function() {
        var drEvente = $('.dropify').dropify();

        drEvente.on('dropify.beforeClear', function(event, element) {
            return confirm("Hapus \"" + element.file.name + "\" ?");
        });

        drEvente.on('dropify.afterClear', function() {
            formDataMapel = null;
            $('#file-preview').html('<p style="font-family:\'Lexend\',sans-serif;font-size:0.82rem;color:#64748b;margin:0">Pastikan anda telah mengisi format yang telah disediakan.</p>');
        });

        drEvente.on('dropify.errors', function() {
            showDangerToast("File rusak atau tidak didukung");
        });

        $('#input-file-events-excel').on('change', async function(e) {
            var files = e.target.files || [];
            if (!files.length) return;
            formDataMapel = null;
            $('#file-preview').html('<p style="font-family:\'Lexend\',sans-serif;font-size:0.82rem;color:#64748b;margin:0">Memuat data...</p>');
            const jsonData = await getDataFromExcel(files[0]);
            let tbl = $('<table class="mb-4 w-100"></table>');
            createTable(jsonData[jsonData.sheets[0]], tbl);
        });

        $('#input-file-events-word').on('change', async function(e) {
            var files = e.target.files || [];
            if (!files.length) return;
            formDataMapel = null;
            $('#file-preview').html('<p style="font-family:\'Lexend\',sans-serif;font-size:0.82rem;color:#64748b;margin:0">Memuat file...</p>');
            parseWordDocxFile(files[0]);
        });

        $('#formUpload').submit('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $.ajax({
                url: base_url + "datamapel/do_import",
                type: "POST",
                processData: false,
                contentType: false,
                data: formDataMapel,
                success: function() {
                    window.history.back();
                },
                error: function() {
                    showDangerToast("File tidak terbaca");
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
        var options = {
            styleMap: ["u => u", "strike => del"]
        };

        reader.onloadend = function() {
            mammoth.convertToHtml({
                arrayBuffer: reader.result
            }, options).then(function(resultObject) {
                var showDiv = $('#file-preview');
                showDiv.html(resultObject.value);
                setTimeout(function() {
                    formDataMapel = new FormData($('#formUpload')[0]);
                    showDiv.children().not("table").remove();
                    showDiv.children('table').each(function() {
                        $(this).addClass('table-glass w-100');
                        var $trs = $(this).find('tr');
                        $trs.splice(0, 1);
                        $trs.each(function(index, tr) {
                            if ($(tr).parent().closest('td').length === 0) {
                                var namaMapel = $(this).find("td:eq(1)").text().trim();
                                var kodeMapel = $(this).find("td:eq(2)").text().trim();
                                if (namaMapel && kodeMapel) {
                                    formDataMapel.append('mapel[' + index + '][nama_mapel]', namaMapel);
                                    formDataMapel.append('mapel[' + index + '][kode]', kodeMapel);
                                } else {
                                    $(this).remove();
                                }
                            }
                        });
                    });
                    swal.close();
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
                row.append(c === 3 || c > 8 ? $('<td/>').html(val) : $('<td class="text-center"/>').html(val));
            }
            $(selector).append(row);
        }
        $('#file-preview').prepend(selector);

        if (list.rows.length > 0) {
            formDataMapel = new FormData($('#formUpload')[0]);
            list.rows.forEach(function(mapel, ind) {
                for (const key in mapel) {
                    if (key === '2') formDataMapel.append('mapel[' + ind + '][nama_mapel]', mapel[key]);
                    else if (key === '3') formDataMapel.append('mapel[' + ind + '][kode]', mapel[key]);
                }
            });
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
                                        if (i === 3) val = val.toString().replace("'", "");
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
</script>
