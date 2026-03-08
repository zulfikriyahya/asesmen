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

            <div class="row">
                <div class="col-xl-4 col-lg-5 mb-4">
                    <div class="glass-card h-100">
                        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem">
                            <h6 class="card-title mb-0">File Excel</h6>
                            <?php $url = 'uploads/import/format/format_siswa.xlsx'; ?>
                            <a href="<?= base_url() . $url ?>" class="btn btn-success-glass btn-sm" style="white-space:nowrap;flex-shrink:0">
                                <i class="fas fa-download mr-1"></i>Download Template
                            </a>
                        </div>
                        <div class="card-body" style="padding:1.5rem">
                            <?= form_open_multipart('', array('id' => 'formPreviewExcel')); ?>
                            <label style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#94a3b8;margin-bottom:0.6rem;display:block">Pilih file Excel</label>
                            <input type="file" id="input-file-events-excel" name="upload_file" class="dropify" />
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="glass-card h-100" style="display:flex;flex-direction:column">
                        <?= form_open('', array('id' => 'formUpload')); ?>
                        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem">
                            <h6 class="card-title mb-0">Preview</h6>
                            <button id="submit-excel" name="preview" type="submit" class="btn btn-cyan btn-sm" disabled="disabled" style="flex-shrink:0">
                                <i class="fas fa-cloud-upload-alt mr-1"></i>Upload
                            </button>
                        </div>
                        <?= form_close(); ?>
                        <div class="card-body table-responsive flex-grow-1" id="file-preview" style="padding:1.5rem;min-height:200px">
                            <table id="tableprev" class="mb-4 w-100"></table>
                            <p style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#64748b;margin:0">
                                Pastikan anda telah mengisi format yang telah disediakan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/FileSaver.min.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/excel/exceljs.js"></script>
<script type="text/javascript" src="<?= base_url() ?>/assets/app/js/excel/js-excel-template.min.js"></script>

<script>
    var typeImport = '<?= $tipe ?>';
    var dataSiswa;

    $(document).ready(function() {
        ajaxcsrf();

        var drEvente = $('#input-file-events-excel').dropify();

        drEvente.on('dropify.beforeClear', function(event, element) {
            return confirm("Hapus \"" + element.file.name + "\" ?");
        });

        drEvente.on('dropify.afterClear', function() {
            $('#submit-excel').attr('disabled', 'disabled');
            $('#tableprev').html('');
        });

        drEvente.on('dropify.errors', function() {
            showDangerToast("File rusak atau tidak didukung");
        });

        $('#input-file-events-excel').on('change', async function(e) {
            var files = e.target.files || [];
            if (!files.length) return;
            const jsonData = await getDataFromExcel(files[0]);
            createTable(jsonData[jsonData.sheets[0]], '#tableprev');
        });

        $('#formUpload').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var url = typeImport === "add" ? "datasiswa/do_import" : "datasiswa/updateall";

            swal.fire({
                text: "Silahkan tunggu....",
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });

            $.ajax({
                url: base_url + url,
                type: "POST",
                data: dataSiswa,
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
                        var arrNisnDup = [],
                            arrNisDup = [],
                            arrUserDup = [];
                        $.each(data.errors, function(idx, o) {
                            if (o.nisn !== "") arrNisnDup.push("<br />" + idx + ": " + o.nisn);
                            if (o.nis !== "") arrNisDup.push("<br />" + idx + ": " + o.nis);
                            if (o.username !== "") arrUserDup.push("<br />" + idx + ": " + o.username);
                        });
                        var err = arrNisnDup.length ? `<b>${arrNisnDup.join("' ")}</b>` : '';
                        err += arrNisDup.length ? `<br /><b>${arrNisDup.join("' ")}</b>` : '';
                        err += arrUserDup.length ? `<br /><b>${arrUserDup.join("' ")}</b>` : '';
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
        });
    });

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
        if (list.rows.length > 0) {
            $('#submit-excel').removeAttr('disabled');
            dataSiswa = new FormData($('#formUpload')[0]);
            list.rows.forEach(function(siswa, ind) {
                for (const key in siswa) {
                    if (key) dataSiswa.append('siswa[' + ind + '][' + key + ']', siswa[key]);
                }
            });
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
                if (k === 'label') {
                    header.append($('<th class="text-center align-middle"/>').html(row[k]));
                } else {
                    if ($.inArray(row[k], columns) === -1) columns.push(row[k]);
                }
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
</script>
