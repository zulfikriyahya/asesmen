<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/siswa.css">

<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="glass-card mb-0" style="position:relative">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.5rem">
                    <h6 class="card-title mb-0"><?= $subjudul ?></h6>
                    <div style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap">
                        <button type="button" data-toggle="modal" data-target="#createSiswaModal" class="btn btn-cyan btn-sm">
                            <i class="fas fa-plus mr-1"></i>Tambah Siswa
                        </button>
                        <a href="<?= base_url('datasiswa/add') ?>" class="btn btn-glass btn-sm">
                            <i class="fas fa-upload mr-1"></i>Import
                        </a>
                        <a href="<?= base_url('datasiswa/update') ?>" class="btn btn-success-glass btn-sm">
                            <i class="fas fa-database mr-1"></i>Update Data
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3" style="gap:0.5rem">
                        <div class="d-flex align-items-center" style="gap:0.5rem">
                            <label class="mb-0" style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#94a3b8;white-space:nowrap">Show</label>
                            <select id="users_length" class="custom-select-glass" style="width:80px">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center" style="gap:0.4rem;flex:1;max-width:360px">
                            <input id="input-search" type="search" class="form-control form-control-glass" placeholder="Cari siswa..." aria-controls="users">
                            <button id="btn-search" type="button" class="btn btn-cyan btn-sm flex-shrink-0" onclick="applySearch()" disabled="disabled">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap mb-3" style="gap:0.5rem">
                        <div class="dropdown dropdown-glass">
                            <button id="dropdown-btn" class="btn btn-danger-glass dropdown-toggle btn-sm" type="button" data-toggle="dropdown" aria-expanded="false" disabled="disabled">
                                Aksi
                            </button>
                            <div id="dropdown-action" class="dropdown-menu">
                                <a class="dropdown-item" id="pindah" href="#">Set sebagai PINDAH</a>
                                <a class="dropdown-item" id="keluar" href="#">Set sebagai KELUAR</a>
                                <a class="dropdown-item" id="hapus" href="#">HAPUS</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap:0.5rem">
                            <label class="mb-0" style="font-family:'Lexend',sans-serif;font-size:0.82rem;color:#94a3b8;white-space:nowrap">Filter</label>
                            <select id="users-filter" class="custom-select-glass" style="width:130px">
                                <option value="1">Aktif</option>
                                <option value="5">Tanpa Kelas</option>
                                <option value="3">Pindah</option>
                                <option value="4">Keluar</option>
                            </select>
                        </div>
                    </div>

                    <?= form_open('datasiswa/delete', array('id' => 'bulk')); ?>
                    <div class="table-responsive">
                        <table id="table-siswa" class="table-glass w-100">
                            <thead>
                                <tr>
                                    <th style="width:40px"><input class="select_all" type="checkbox"></th>
                                    <th style="width:40px">No.</th>
                                    <th>Nama & Kelas</th>
                                    <th>NIS & NISN</th>
                                    <th style="width:80px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </div>
                    <?= form_close() ?>

                    <div class="mt-3 d-flex justify-content-end">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination"></ul>
                        </nav>
                    </div>
                </div>

                <div class="overlay-glass d-none" id="loading">
                    <div class="spinner-cyan"></div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= form_open('', array('id' => 'formsiswa')); ?>
<div class="modal fade" id="createSiswaModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-glass" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="nama_siswa">Nama Siswa :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="nama_siswa" type="text" class="form-control form-control-glass" name="nama_siswa" placeholder="Nama Siswa" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="nis">NIS :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input type="number" id="nis" class="form-control form-control-glass" name="nis" placeholder="NIS" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="nisn">NISN :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input type="number" id="nisn" class="form-control form-control-glass" name="nisn" placeholder="NISN" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="jenis_kelamin">Jenis Kelamin :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-venus-mars"></i></span>
                            </div>
                            <select class="form-control form-control-glass" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="kelas_awal">Kelas Awal :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-graduation-cap"></i></span>
                            </div>
                            <?php
                            if ($setting->jenjang == 1) {
                                $opsis['1'] = '1';
                                $opsis['2'] = '2';
                                $opsis['3'] = '3';
                                $opsis['4'] = '4';
                                $opsis['5'] = '5';
                                $opsis['6'] = '6';
                            } elseif ($setting->jenjang == 2) {
                                $opsis['7'] = '7';
                                $opsis['8'] = '8';
                                $opsis['9'] = '9';
                            } else {
                                $opsis['10'] = '10';
                                $opsis['11'] = '11';
                                $opsis['12'] = '12';
                            };
                            ?>
                            <select class="form-control form-control-glass" id="kelas_awal" name="kelas_awal">
                                <option value="">Pilih Kelas Awal</option>
                                <?php foreach ($opsis as $kelas): ?>
                                    <option value="<?= $kelas ?>"><?= $kelas ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="tahun_masuk">Tanggal Diterima :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" name="tahun_masuk" id="tahunmasuk" class="form-control form-control-glass" autocomplete="off" placeholder="Tgl/Tahun Masuk" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="username">Username :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-user"></i></span>
                            </div>
                            <input id="username" type="text" class="form-control form-control-glass" name="username" placeholder="Username" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-offset-4">
                        <label for="password">Password :</label>
                    </div>
                    <div class="col-md-8 col-sm-offset-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text input-group-text-glass"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input id="password" class="form-control form-control-glass" name="password" placeholder="Password" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-glass btn-sm" data-dismiss="modal">Batal</button>
                <button type="reset" class="btn btn-warning-glass btn-sm"><i class="fa fa-sync mr-1"></i>Reset</button>
                <button type="submit" class="btn btn-cyan btn-sm"><i class="fa fa-plus mr-1"></i>Simpan</button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<?= form_open('', array('id' => 'pager')); ?>
<input type="hidden" id="pager-page" name="page" value="1">
<input type="hidden" id="pager-limit" name="limit" value="10">
<?= form_close() ?>

<script src="<?= base_url() ?>/assets/app/js/jquery.twbsPagination.js" type="text/javascript"></script>
<script>
    let currentPage = 1;
    let perPage = 10;
    let $pagination, defaultOpts, query, actionBulk;

    $(document).ready(function() {
        ajaxcsrf();
        $pagination = $('#pagination');
        defaultOpts = {
            visiblePages: 5,
            initiateStartPageClick: false,
            onPageClick: function(event, page) {
                currentPage = page;
                loadSiswa();
            }
        };
        $pagination.twbsPagination(defaultOpts);

        $('#users_length').change(function() {
            $('#pager-limit').val($(this).val());
            perPage = $(this).val();
            currentPage = 1;
            loadSiswa();
        });

        $('#users-filter').change(function() {
            currentPage = 1;
            loadSiswa();
        });

        $('#input-search').on('change keyup', function() {
            var val = $(this).val();
            query = val === "" ? null : val;
            $('#btn-search').attr('disabled', query == null);
        });

        $(".select_all").on("click", function() {
            var checked = this.checked;
            $(".check").each(function() {
                this.checked = checked;
            });
            $('#dropdown-btn').attr('disabled', !checked);
        });

        $("#table-siswa tbody").on("click", "tr .check", function() {
            var total = $("#table-siswa tbody tr .check").length;
            var checked = $("#table-siswa tbody tr .check:checked").length;
            $(".select_all").prop("checked", total === checked);
            $('#dropdown-btn').attr('disabled', checked === 0);
        });

        $("#bulk").on("submit", function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $.ajax({
                url: $(this).attr("action"),
                data: $(this).serialize() + '&aksi=' + actionBulk,
                type: "POST",
                success: function(respon) {
                    if (respon.status) {
                        $(".select_all").prop("checked", false);
                        $('#dropdown-btn').attr('disabled', true);
                        swal.fire({
                            title: "Berhasil",
                            text: respon.total + " data berhasil diproses",
                            icon: "success"
                        });
                        loadSiswa();
                    } else {
                        swal.fire({
                            title: "Gagal",
                            text: "Tidak ada data yang dipilih",
                            icon: "error"
                        });
                    }
                },
                error: function() {
                    swal.fire({
                        title: "Gagal",
                        text: "Ada data yang sedang digunakan",
                        icon: "error"
                    });
                }
            });
        });

        $('#tahunmasuk').datetimepicker({
            icons: {
                next: 'fa fa-angle-right',
                previous: 'fa fa-angle-left'
            },
            timepicker: false,
            format: 'Y-m-d',
            disabledWeekDays: [0],
            widgetPositioning: {
                horizontal: 'left',
                vertical: 'bottom'
            }
        });

        $('#formsiswa').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $.ajax({
                url: base_url + "datasiswa/create",
                data: $(this).serialize(),
                dataType: "JSON",
                type: 'POST',
                success: function(response) {
                    $('#createSiswaModal').modal('hide').data('bs.modal', null);
                    if (response.insert) {
                        showSuccessToast(response.text);
                        loadSiswa();
                    } else {
                        showDangerToast(response.text);
                    }
                },
                error: function() {
                    showDangerToast("Gagal disimpan");
                }
            });
        });

        $("#dropdown-action a").click(function() {
            actionBulk = $(this).attr('id');
            if (actionBulk === "pindah") bulk_pindah();
            else if (actionBulk === "keluar") bulk_keluar();
            else if (actionBulk === "hapus") bulk_delete();
        });

        loadSiswa();
    });

    function loadSiswa() {
        $(".select_all").prop("checked", false);
        $('#pager-page').val(currentPage);
        $('#loading').removeClass('d-none');
        var dataPost = $('#pager').serialize() +
            (query != null ? '&search=' + query : '') +
            '&filter=' + $('#users-filter').val();

        $.ajax({
            url: base_url + 'datasiswa/list',
            data: dataPost,
            type: 'POST',
            success: function(data) {
                $('#loading').addClass('d-none');
                $('#input-search').val(data.search);
                if (data.pages > 0) {
                    $pagination.removeClass('d-none').twbsPagination('destroy').twbsPagination(
                        $.extend({}, defaultOpts, {
                            startPage: currentPage,
                            totalPages: data.pages
                        })
                    );
                } else {
                    $pagination.addClass('d-none');
                }
                previewData(data);
            },
            error: function(xhr) {
                $('#loading').addClass('d-none');
                swal.fire({
                    title: "ERROR",
                    text: "Ada kesalahan",
                    icon: "error"
                });
            }
        });
    }

    function previewData(data) {
        $('#input-search').val(data.search);
        var html = '';
        if (data.lists.length > 0) {
            $.each(data.lists, function(idx, siswa) {
                var kls = siswa.nama_kelas ? '<span class="badge-cyan ml-1">' + siswa.nama_kelas + '</span>' : '';
                var status = siswa.aktif == "0" ?
                    '<span class="badge-danger-glass ml-1">Nonaktif</span>' :
                    '<span class="badge-success-glass ml-1">Aktif</span>';
                var jk = '<span class="badge-light-glass ml-1">' + siswa.jenis_kelamin + '</span>';
                html += '<tr>' +
                    '<td class="text-center"><input name="checked[]" class="check" value="' + siswa.id_siswa + '" type="checkbox"></td>' +
                    '<td class="text-center">' + Number((perPage * (currentPage - 1)) + (idx + 1)) + '</td>' +
                    '<td>' +
                    '   <div class="d-flex align-items-center" style="gap:0.6rem">' +
                    '       <img class="avatar-circle avatar" src="' + base_url + siswa.foto + '" alt="foto">' +
                    '       <div class="siswa-name">' + siswa.nama + '<br>' + kls + jk + status + '</div>' +
                    '   </div>' +
                    '</td>' +
                    '<td>' +
                    '   <span class="badge-light-glass d-block mb-1">' + siswa.nis + '</span>' +
                    '   <span class="badge-light-glass d-block">' + siswa.nisn + '</span>' +
                    '</td>' +
                    '<td class="text-center">' +
                    '   <a class="btn btn-warning-glass btn-sm" href="' + base_url + 'datasiswa/edit/' + siswa.id_siswa + '">' +
                    '       <i class="fa fa-pencil-alt"></i> Edit' +
                    '   </a>' +
                    '</td>' +
                    '</tr>';
            });
        } else {
            html = '<tr><td colspan="5" class="text-center">Belum ada data</td></tr>';
        }
        $('#table-body').html(html);
        $('.avatar').each(function() {
            $(this).on("error", function() {
                var src = $(this).attr('src').replace('profiles', 'foto_siswa');
                $(this).attr("src", src).on("error", function() {
                    $(this).attr("src", base_url + 'assets/img/siswa.png');
                });
            });
        });
    }

    function applySearch() {
        query = $('#input-search').val();
        currentPage = 1;
        loadSiswa();
    }

    function confirmBulk(text, label) {
        if (!$("#table-siswa tbody tr .check:checked").length) {
            return swal.fire({
                title: "Gagal",
                text: "Tidak ada data yang dipilih",
                icon: "error"
            });
        }
        swal.fire({
            title: "Anda yakin?",
            text: text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0891b2",
            cancelButtonColor: "#d33",
            confirmButtonText: label
        }).then(result => {
            if (result.value) $("#bulk").submit();
        });
    }

    function bulk_delete() {
        confirmBulk("Data terpilih akan dihapus!", "Hapus!");
    }

    function bulk_pindah() {
        confirmBulk("Data terpilih akan diset sebagai siswa PINDAH", "YA!");
    }

    function bulk_keluar() {
        confirmBulk("Data terpilih akan diset sebagai siswa KELUAR", "YA!");
    }
</script>
