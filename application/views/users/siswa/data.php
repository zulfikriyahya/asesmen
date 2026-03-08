<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

    :root {
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        --accent: #f97316;
        --text-primary: #f1f5f9;
        --text-muted: #94a3b8;
        --radius: 14px;
        --radius-sm: 8px;
    }

    * {
        font-family: 'Lexend', sans-serif !important;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--glass-shadow);
        overflow: hidden;
        position: relative;
    }

    .glass-header {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.3), rgba(249, 115, 22, 0.08));
        border-bottom: 1px solid var(--glass-border);
        padding: .85rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .glass-header h3 {
        margin: 0;
        font-size: .95rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .btn-g {
        border-radius: var(--radius-sm);
        padding: .35rem .75rem;
        font-size: .8rem;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        color: var(--text-primary);
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-family: 'Lexend', sans-serif;
    }

    .btn-g:hover {
        background: rgba(255, 255, 255, 0.1);
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
    }

    .select-g {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        padding: .3rem .6rem;
        font-size: .82rem;
        font-family: 'Lexend', sans-serif;
    }

    .input-search-g {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        padding: .4rem .75rem;
        font-size: .82rem;
        font-family: 'Lexend', sans-serif;
        transition: border-color .2s;
    }

    .input-search-g:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.1);
    }

    .input-search-g::placeholder {
        color: var(--text-muted);
    }

    table#users {
        color: var(--text-primary) !important;
    }

    table#users thead th {
        background: rgba(249, 115, 22, 0.2) !important;
        color: var(--text-primary) !important;
        border-color: var(--glass-border) !important;
        font-weight: 500;
        font-size: .82rem;
    }

    table#users td,
    table#users th {
        border-color: var(--glass-border) !important;
        font-size: .83rem;
        vertical-align: middle;
    }

    table#users tbody tr:hover {
        background: rgba(255, 255, 255, 0.04) !important;
    }

    .overlay-g {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius);
        z-index: 10;
    }

    .label-g-success {
        background: rgba(34, 197, 94, 0.2);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 20px;
        padding: .15rem .6rem;
        font-size: .75rem;
        font-weight: 500;
    }

    .label-g-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 20px;
        padding: .15rem .6rem;
        font-size: .75rem;
        font-weight: 500;
    }
</style>

<div class="content-wrapper pt-4" style="background: linear-gradient(135deg, #0f0f19 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 style="font-weight:700;color:#f1f5f9;font-size:1.5rem;"><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="glass-card">
                <div class="glass-header">
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

                <div class="px-4 pt-3 pb-2">
                    <div class="row align-items-center mb-3">
                        <div class="col-sm-12 col-md-6 mb-2 mb-md-0">
                            <label style="color:var(--text-muted);font-size:.82rem;display:inline-flex;align-items:center;gap:.5rem;">
                                Show
                                <select id="users_length" class="select-g">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                entries
                            </label>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <div class="d-flex justify-content-md-end align-items-center gap-2">
                                <button id="btn-clear" type="button" class="btn-g" disabled
                                    data-toggle="tooltip" title="Hapus pencarian">
                                    <i class="fa fa-times"></i>
                                </button>
                                <input id="input-search" type="search" class="input-search-g"
                                    placeholder="Cari…" aria-controls="users">
                                <button id="btn-search" type="button" class="btn-g" disabled
                                    onclick="applySearch()" data-toggle="tooltip" title="Cari">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
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

                    <div class="d-flex justify-content-end pb-2">
                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination"></ul>
                        </nav>
                    </div>
                </div>

                <div class="overlay-g d-none" id="loading">
                    <div class="spinner-grow text-warning"></div>
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
                            <img class="avatar img-circle" src="${base_url + s.foto}" width="30" height="30" style="object-fit:cover;" alt="">
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
                            <i class="fa fa-sync" style="font-size:.75rem;"></i>
                        </button>
                    </td>
                    <td class="text-center">
                        ${s.aktif == '0'
                            ? `<span class="label-g-danger d-block mb-1">Nonaktif</span>
                               <button type="button" class="btn-g success btn-aktif" data-id="${s.id_siswa}" title="Aktifkan">
                                 <i class="fa fa-user-plus" style="font-size:.75rem;"></i>
                               </button>`
                            : `<span class="label-g-success d-block mb-1">Aktif</span>
                               <button type="button" class="btn-g danger btn-nonaktif"
                                       data-username="${s.username}" data-nama="${s.nama}" title="Nonaktifkan">
                                 <i class="fa fa-ban" style="font-size:.75rem;"></i>
                               </button>`
                        }
                    </td>
                </tr>`;
            });
        } else {
            html = '<tr><td colspan="8" class="text-center" style="color:var(--text-muted);padding:2rem;">Tidak ada data siswa</td></tr>';
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
