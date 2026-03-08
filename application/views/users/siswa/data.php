{{FILE: users/siswa/data.php}}
<div class="content-wrapper bg-dark pt-4">
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
            <div class="card card-default my-shadow" style="position:relative;">

                <div class="card-header bg-orange with-border">
                    <h3 class="card-title text-bold"><?= $subjudul ?></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-action btn-success btn-sm"
                            data-action="aktifkan" data-toggle="tooltip" title="Aktifkan">
                            <i class="fa fa-users m-1"></i>
                            <span class="d-none d-sm-inline-block ml-1">Aktifkan Semua</span>
                        </button>
                        <button type="button" class="btn btn-action btn-danger btn-sm"
                            data-action="nonaktifkan" data-toggle="tooltip" title="Nonaktifkan">
                            <i class="fa fa-ban m-1"></i>
                            <span class="d-none d-sm-inline-block ml-1">Nonaktifkan Semua</span>
                        </button>
                    </div>
                </div>

                <div class="card-body text-dark">
                    <!-- Search & Length controls -->
                    <div class="row mb-2">
                        <div class="col-sm-12 col-md-6">
                            <label class="d-flex align-items-center gap-1">
                                Show
                                <select id="users_length" class="custom-select custom-select-sm form-control form-control-sm ml-1 mr-1" style="width:auto;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex justify-content-end align-items-center gap-1">
                                <button id="btn-clear" type="button" class="btn btn-sm btn-light" disabled
                                    data-toggle="tooltip" title="Hapus pencarian">
                                    <i class="fa fa-times"></i>
                                </button>
                                <input id="input-search" type="search" class="form-control form-control-sm"
                                    style="width:auto;" placeholder="Cari…" aria-controls="users">
                                <button id="btn-search" type="button" class="btn btn-sm btn-light" disabled
                                    onclick="applySearch()" data-toggle="tooltip" title="Cari">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mb-2">
                        <?= form_open('', ['id' => 'bulk']) ?>
                        <table id="users" class="w-100 table table-striped table-bordered table-hover">
                            <thead class="bg-maroon">
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

                <div class="overlay d-none" id="loading">
                    <div class="spinner-grow"></div>
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

        /* ── Table action buttons ── */
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
            var username = $(this).data('username'),
                nama = $(this).data('nama').replace("'", '');
            ajaxAction(base_url + 'usersiswa/deactivate/' + username + '/' + nama, loadSiswa);
        });

        $('#users').on('click', '.btn-reset', function() {
            var username = $(this).data('username'),
                nama = encodeURIComponent($(this).data('nama'));
            ajaxAction(base_url + 'usersiswa/reset_login/' + username + '/' + nama, loadSiswa);
        });

        $('.btn-action').on('click', function() {
            var action = $(this).data('action');
            var isAktif = action === 'aktifkan';
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
            error: function(xhr) {
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
                var kls = s.nama_kelas || '';
                var no = perPage * (currentPage - 1) + (idx + 1);
                html += `<tr>
        <td class="text-center align-middle">
          <input type="hidden" name="ids[]" value="${s.id_siswa}">${no}
        </td>
        <td class="align-middle">${s.nis}</td>
        <td>
          <div class="media d-flex align-items-center">
            <img class="avatar img-circle" src="${base_url + s.foto}" width="30" height="30" alt="">
            <div class="media-body ml-2">${s.nama}</div>
          </div>
        </td>
        <td class="align-middle">${kls}</td>
        <td class="align-middle">${s.username}</td>
        <td class="align-middle">${s.password}</td>
        <td class="text-center align-middle">
          <button type="button" class="btn btn-default btn-xs btn-reset"
                  ${s.reset == '0' ? 'disabled' : ''}
                  data-username="${s.username}" data-nama="${s.nama}"
                  data-toggle="tooltip" title="Reset Login">
            <i class="fa fa-sync text-xs mx-1"></i>
          </button>
        </td>
        <td class="text-center align-middle p-1">
          ${s.aktif == '0'
            ? `<span class="badge badge-danger">Nonaktif</span><br>
               <button type="button" class="btn btn-aktif btn-success btn-xs mt-1"
                       data-id="${s.id_siswa}" data-toggle="tooltip" title="Aktifkan">
                 <i class="fa fa-user-plus text-xs mx-1"></i>
               </button>`
            : `<span class="badge badge-success">Aktif</span><br>
               <button type="button" class="btn btn-nonaktif btn-danger btn-xs mt-1"
                       data-username="${s.username}" data-nama="${s.nama}"
                       data-toggle="tooltip" title="Nonaktifkan">
                 <i class="fa fa-ban text-xs mx-1"></i>
               </button>`
          }
        </td>
      </tr>`;
            });
        } else {
            html = '<tr><td colspan="8" class="text-center">Tidak ada data siswa</td></tr>';
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

