<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/users.css">

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="page-title"><?= $judul ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="g-card">

                <div class="g-card-header">
                    <h3><?= $subjudul ?></h3>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn-g success btn-action"
                            data-action="aktifkan" data-toggle="tooltip" title="Aktifkan">
                            <i class="fa fa-users"></i>
                            <span class="d-none d-sm-inline">Aktifkan Semua</span>
                        </button>
                        <button type="button" class="btn-g danger btn-action"
                            data-action="nonaktifkan" data-toggle="tooltip" title="Nonaktifkan">
                            <i class="fa fa-ban"></i>
                            <span class="d-none d-sm-inline">Nonaktifkan Semua</span>
                        </button>
                    </div>
                </div>

                <div class="g-card-body">
                    <div class="toolbar">
                        <div class="toolbar-left">
                            Show
                            <select id="users_length" class="f-select-sm">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            entries
                        </div>
                        <div class="toolbar-right">
                            <button id="btn-clear" type="button" class="btn-g" disabled
                                data-toggle="tooltip" title="Hapus pencarian">
                                <i class="fa fa-times"></i>
                            </button>
                            <input id="input-search" type="search" class="f-search"
                                placeholder="Cari…" aria-controls="users">
                            <button id="btn-search" type="button" class="btn-g" disabled
                                onclick="applySearch()" data-toggle="tooltip" title="Cari">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <?= form_open('', ['id' => 'bulk']) ?>
                        <table id="users" class="w-100 table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:40px">No.</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th class="text-center">Reset Login</th>
                                    <th class="text-center">Status / Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                        <?= form_close() ?>
                    </div>

                    <div class="d-flex justify-content-end">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination"></ul>
                        </nav>
                    </div>
                </div>

                <div class="overlay-g d-none" id="loading">
                    <div class="spinner-grow text-info"></div>
                </div>

            </div>
        </div>
    </section>
</div>

<?= form_open('', ['id' => 'pager']) ?>
<input type="hidden" id="pager-page" name="page" value="1">
<input type="hidden" id="pager-limit" name="limit" value="10">
<?= form_close() ?>

<script src="<?= base_url() ?>/assets/app/js/jquery.twbsPagination.js"></script>
<script>
    let currentPage = 1,
        perPage = 10,
        $pagination, defaultOpts, query;

    $(function() {
        ajaxcsrf();

        $pagination = $('#pagination');
        defaultOpts = {
            visiblePages: 5,
            initiateStartPageClick: false,
            onPageClick: function(e, page) {
                currentPage = page;
                loadSiswa();
            }
        };
        $pagination.twbsPagination(defaultOpts);

        $('#users_length').on('change', function() {
            $('#pager-limit').val($(this).val());
            perPage = $(this).val();
            currentPage = 1;
            loadSiswa();
        });

        $('#input-search').on('change keyup', function() {
            var val = $(this).val();
            query = val === '' ? null : val;
            $('#btn-clear, #btn-search').attr('disabled', query == null);
        });

        $('#btn-clear').on('click', function() {
            query = null;
            currentPage = 1;
            $('#btn-clear, #btn-search').attr('disabled', true);
            $('#input-search').val('');
            loadSiswa();
        });

        function ajaxAction(url, onSuccess) {
            $('#loading').removeClass('d-none');
            $.ajax({
                url,
                type: 'GET',
                success: function(r) {
                    $('#loading').addClass('d-none');
                    if (r.msg) {
                        swal.fire({
                            title: r.status ? 'Sukses' : 'Error',
                            text: decodeURIComponent(r.msg),
                            icon: r.status ? 'success' : 'error'
                        });
                        if (r.status && onSuccess) onSuccess();
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Gagal',
                        html: xhr.responseText,
                        icon: 'error'
                    });
                }
            });
        }

        $('#users').on('click', '.btn-aktif', function() {
            ajaxAction(base_url + 'usersiswa/activate/' + $(this).data('id'), loadSiswa);
        });
        $('#users').on('click', '.btn-nonaktif', function() {
            ajaxAction(base_url + 'usersiswa/deactivate/' + $(this).data('username') + '/' + $(this).data('nama').replace("'", ''), loadSiswa);
        });
        $('#users').on('click', '.btn-reset', function() {
            ajaxAction(base_url + 'usersiswa/reset_login/' + $(this).data('username') + '/' + encodeURIComponent($(this).data('nama')), loadSiswa);
        });

        $('.btn-action').on('click', function() {
            var action = $(this).data('action'),
                isAktif = action === 'aktifkan';
            swal.fire({
                title: isAktif ? 'Aktifkan semua siswa' : 'Nonaktifkan semua siswa',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Lanjutkan'
            }).then(function(r) {
                if (!r.value) return;
                $('#loading').removeClass('d-none');
                swal.fire({
                    title: isAktif ? 'Mengaktifkan…' : 'Menonaktifkan…',
                    text: 'Silahkan tunggu…',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => swal.showLoading()
                });
                $.ajax({
                    url: base_url + (isAktif ? 'usersiswa/aktifkansemua' : 'usersiswa/nonaktifkansemua'),
                    type: 'GET',
                    success: function(res) {
                        $('#loading').addClass('d-none');
                        swal.fire({
                            title: res.status ? 'Sukses' : 'Gagal',
                            text: res.msg,
                            icon: res.status ? 'success' : 'error'
                        }).then(loadSiswa);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal',
                            html: xhr.responseText,
                            icon: 'error'
                        });
                    }
                });
            });
        });

        loadSiswa();
    });

    function loadSiswa() {
        $('#pager-page').val(currentPage);
        $('#loading').removeClass('d-none');
        var cari = query != null ? '&search=' + query : '';
        $.ajax({
            url: base_url + 'usersiswa/list',
            type: 'POST',
            data: $('#pager').serialize() + cari,
            success: function(data) {
                $('#loading').addClass('d-none');
                $('#input-search').val(data.search);
                if (data.pages > 0) {
                    $pagination.removeClass('d-none').twbsPagination('destroy')
                        .twbsPagination($.extend({}, defaultOpts, {
                            startPage: currentPage,
                            totalPages: data.pages
                        }));
                } else {
                    $pagination.addClass('d-none');
                }
                previewData(data);
            },
            error: function() {
                $('#loading').addClass('d-none');
                swal.fire({
                    title: 'ERROR',
                    text: 'Ada kesalahan',
                    icon: 'error'
                });
            }
        });
    }

    function previewData(data) {
        var html = '';
        if (data.lists.length > 0) {
            $.each(data.lists, function(idx, s) {
                var no = perPage * (currentPage - 1) + (idx + 1);
                html += `<tr>
                    <td class="text-center"><input type="hidden" name="ids[]" value="${s.id_siswa}">${no}</td>
                    <td>${s.nis}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img class="avatar img-circle" src="${base_url + s.foto}" width="28" height="28" style="object-fit:cover;" alt="">
                            <span>${s.nama}</span>
                        </div>
                    </td>
                    <td>${s.nama_kelas || ''}</td>
                    <td>${s.username}</td>
                    <td>${s.password}</td>
                    <td class="text-center">
                        <button type="button" class="btn-g btn-reset" ${s.reset == '0' ? 'disabled' : ''}
                                data-username="${s.username}" data-nama="${s.nama}"
                                data-toggle="tooltip" title="Reset Login">
                            <i class="fa fa-sync" style="font-size:.72rem;"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        ${s.aktif == '0'
                            ? `<span class="badge-off">Nonaktif</span><br>
                               <button type="button" class="btn-g success btn-aktif mt-1" data-id="${s.id_siswa}" title="Aktifkan">
                                 <i class="fa fa-user-plus" style="font-size:.72rem;"></i>
                               </button>`
                            : `<span class="badge-on">Aktif</span><br>
                               <button type="button" class="btn-g danger btn-nonaktif mt-1"
                                       data-username="${s.username}" data-nama="${s.nama}" title="Nonaktifkan">
                                 <i class="fa fa-ban" style="font-size:.72rem;"></i>
                               </button>`
                        }
                    </td>
                </tr>`;
            });
        } else {
            html = '<tr><td colspan="8" class="text-center" style="padding:2rem;color:#7eb8d4;">Tidak ada data siswa</td></tr>';
        }
        $('#table-body').html(html);
        $('.avatar').on('error', function() {
            var src = $(this).attr('src').replace('profiles', 'foto_siswa');
            $(this).attr('src', src).on('error', function() {
                $(this).attr('src', base_url + 'assets/img/siswa.png');
            });
        });
    }

    function applySearch() {
        query = $('#input-search').val();
        currentPage = 1;
        loadSiswa();
    }
</script>
