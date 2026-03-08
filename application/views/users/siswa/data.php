<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

    :root {
        --bg-base: #080e1a;
        --bg-mid: #0d1526;
        --bg-top: #0a1929;
        --glass-bg: rgba(255, 255, 255, 0.04);
        --glass-hover: rgba(255, 255, 255, 0.08);
        --glass-border: rgba(99, 179, 237, 0.15);
        --accent: #22d3ee;
        --accent2: #3b82f6;
        --text-1: #e2f0ff;
        --text-2: #7eb8d4;
        --radius: 14px;
        --radius-sm: 8px;
        --shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    }

    *,
    *::before,
    *::after {
        font-family: 'Lexend', sans-serif !important;
        box-sizing: border-box;
    }

    .page-wrap {
        background: linear-gradient(140deg, var(--bg-base) 0%, var(--bg-mid) 55%, var(--bg-top) 100%);
        min-height: 100vh;
        padding: 2rem 0 3rem;
    }

    .page-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--text-1);
        letter-spacing: -.01em;
        margin: 0 0 1.75rem;
    }

    .g-card {
        background: var(--glass-bg);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        position: relative;
    }

    .g-card-header {
        background: linear-gradient(135deg, rgba(34, 211, 238, 0.12), rgba(59, 130, 246, 0.08));
        border-bottom: 1px solid var(--glass-border);
        padding: .9rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .g-card-header h3 {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .g-card-body {
        padding: 1.5rem;
    }

    .btn-g {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .4rem .9rem;
        border-radius: var(--radius-sm);
        font-size: .8rem;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        color: var(--text-1);
        transition: background .2s, filter .2s;
        white-space: nowrap;
    }

    .btn-g:hover {
        background: var(--glass-hover);
        color: #fff;
    }

    .btn-g.success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: transparent;
        color: #fff;
    }

    .btn-g.success:hover {
        filter: brightness(1.1);
    }

    .btn-g.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-color: transparent;
        color: #fff;
    }

    .btn-g.danger:hover {
        filter: brightness(1.1);
    }

    .btn-g:disabled {
        opacity: .4;
        cursor: not-allowed;
        filter: none;
    }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
        margin-bottom: 1.25rem;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .82rem;
        color: var(--text-2);
    }

    .toolbar-right {
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .f-select-sm {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-1);
        padding: .35rem .65rem;
        font-size: .8rem;
    }

    .f-search {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-1);
        padding: .38rem .75rem;
        font-size: .8rem;
        transition: border-color .2s, background .2s;
        width: 180px;
    }

    .f-search:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(34, 211, 238, 0.06);
    }

    .f-search::placeholder {
        color: var(--text-2);
        opacity: .55;
    }

    table#users {
        color: var(--text-1) !important;
    }

    table#users thead th {
        background: rgba(34, 211, 238, 0.12) !important;
        color: var(--accent) !important;
        border-color: var(--glass-border) !important;
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .03em;
        padding: .65rem 1rem !important;
    }

    table#users td {
        border-color: var(--glass-border) !important;
        font-size: .82rem;
        padding: .55rem 1rem !important;
        vertical-align: middle;
    }

    table#users tbody tr:hover {
        background: rgba(34, 211, 238, 0.05) !important;
    }

    .badge-on {
        background: rgba(34, 197, 94, 0.18);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        padding: .15rem .65rem;
        font-size: .72rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .badge-off {
        background: rgba(239, 68, 68, 0.18);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 20px;
        padding: .15rem .65rem;
        font-size: .72rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .overlay-g {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius);
        z-index: 10;
    }
</style>

<div class="page-wrap content-wrapper">
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
                })
                .then(function(r) {
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
