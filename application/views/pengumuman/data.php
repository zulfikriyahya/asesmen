<style>
    .pg-card {
        background: var(--glass-bg);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .pg-card-header {
        background: linear-gradient(135deg, rgba(34, 211, 238, 0.12), rgba(59, 130, 246, 0.08));
        border-bottom: 1px solid var(--glass-border);
        padding: .85rem 1.25rem;
        font-size: .85rem;
        font-weight: 600;
        color: var(--accent);
        letter-spacing: .02em;
    }

    .pg-card-body {
        padding: 1.25rem;
    }

    /* ── Running text table ── */
    #tb-text {
        width: 100%;
        border-collapse: collapse;
    }

    #tb-text td {
        border: 1px solid var(--glass-border);
        padding: .45rem .75rem;
        font-size: .82rem;
        color: var(--text-1);
        background: transparent;
    }

    #tb-text td.editable {
        background: rgba(255, 255, 255, 0.04);
        outline: none;
    }

    #tb-text td.editable:focus {
        background: rgba(34, 211, 238, 0.06);
        border-color: var(--accent);
    }

    /* ── Summernote dark override ── */
    .note-editor.note-frame {
        border: 1px solid var(--glass-border) !important;
        border-radius: var(--radius-sm) !important;
        background: rgba(255, 255, 255, 0.04) !important;
    }

    .note-toolbar {
        background: rgba(34, 211, 238, 0.08) !important;
        border-bottom: 1px solid var(--glass-border) !important;
    }

    .note-editable {
        background: rgba(255, 255, 255, 0.03) !important;
        color: var(--text-1) !important;
        min-height: 120px;
    }

    /* ── Select2 kepada ── */
    .select2-container .select2-selection--multiple {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid var(--glass-border) !important;
        border-radius: var(--radius-sm) !important;
        min-height: 38px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: rgba(34, 211, 238, 0.18) !important;
        border-color: var(--glass-border) !important;
        color: var(--text-1) !important;
        border-radius: 4px;
        font-size: .78rem;
    }

    /* ── Post card ── */
    .post-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        margin-bottom: .85rem;
        overflow: hidden;
    }

    .post-body {
        padding: 1rem 1.25rem;
    }

    .post-avatar-admin {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .85rem;
        color: #fff;
        flex-shrink: 0;
    }

    .post-avatar img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(34, 211, 238, 0.25);
    }

    .post-author {
        font-size: .83rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .post-time {
        font-size: .73rem;
        color: var(--text-2);
    }

    .post-text {
        font-size: .84rem;
        color: var(--text-1);
        margin-top: .65rem;
        line-height: 1.6;
    }

    .post-actions {
        display: flex;
        gap: .35rem;
        margin-top: .75rem;
        flex-wrap: wrap;
    }

    .btn-post {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .28rem .75rem;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 500;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        color: var(--text-2);
        cursor: pointer;
        transition: background .2s, color .2s;
    }

    .btn-post:hover {
        background: var(--glass-hover);
        color: var(--text-1);
    }

    .btn-post.danger:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.3);
    }

    /* ── Comment ── */
    .comment-wrap {
        border-top: 1px solid var(--glass-border);
        padding: .85rem 1.25rem;
        background: rgba(0, 0, 0, 0.12);
    }

    .comment-item {
        display: flex;
        gap: .75rem;
        margin-bottom: .75rem;
    }

    .comment-item:last-child {
        margin-bottom: 0;
    }

    .c-avatar-admin {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .75rem;
        color: #fff;
        flex-shrink: 0;
    }

    .c-avatar img {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid rgba(34, 211, 238, 0.2);
        flex-shrink: 0;
    }

    .c-bubble {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 0 var(--radius-sm) var(--radius-sm) var(--radius-sm);
        padding: .45rem .85rem;
        font-size: .8rem;
        color: var(--text-1);
    }

    .c-bubble b {
        color: var(--accent);
        font-size: .75rem;
        display: block;
        margin-bottom: .15rem;
    }

    .c-meta {
        font-size: .72rem;
        color: var(--text-2);
        margin-top: .25rem;
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .c-meta span {
        cursor: pointer;
    }

    .c-meta span:hover {
        color: var(--text-1);
    }

    /* ── Reply bubble ── */
    .r-bubble {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        border-radius: 0 var(--radius-sm) var(--radius-sm) var(--radius-sm);
        padding: .4rem .75rem;
        font-size: .78rem;
        color: var(--text-1);
    }

    .r-bubble b {
        color: var(--accent2);
        font-size: .72rem;
        display: block;
        margin-bottom: .1rem;
    }

    /* ── Load more ── */
    .loadmore-btn {
        display: block;
        text-align: center;
        padding: .45rem;
        margin-top: .5rem;
        border: 1px dashed var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-2);
        font-size: .78rem;
        cursor: pointer;
        transition: background .2s;
    }

    .loadmore-btn:hover {
        background: var(--glass-hover);
        color: var(--text-1);
    }

    /* ── Modal override ── */
    .pg-modal .modal-content {
        background: rgba(8, 14, 26, 0.97);
        backdrop-filter: blur(24px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        color: var(--text-1);
    }

    .pg-modal .modal-header {
        background: linear-gradient(135deg, rgba(34, 211, 238, 0.1), rgba(59, 130, 246, 0.06));
        border-bottom: 1px solid var(--glass-border);
        padding: .9rem 1.25rem;
    }

    .pg-modal .modal-title {
        font-size: .9rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .pg-modal .close {
        color: var(--text-2);
        opacity: 1;
    }

    .pg-modal .close:hover {
        color: #fff;
    }

    .pg-modal .modal-body {
        padding: 1.25rem;
    }

    .pg-modal .modal-footer {
        border-top: 1px solid var(--glass-border);
        padding: .85rem 1.25rem;
    }

    .pg-modal input.form-control {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid var(--glass-border) !important;
        border-radius: var(--radius-sm) !important;
        color: var(--text-1) !important;
        font-size: .83rem;
    }

    .pg-modal input.form-control:focus {
        border-color: var(--accent) !important;
        background: rgba(34, 211, 238, 0.06) !important;
        box-shadow: none !important;
    }

    .kepada-label {
        font-size: .76rem;
        font-weight: 500;
        color: var(--text-2);
        margin-bottom: .35rem;
        display: block;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="page-title"><?= $judul ?></h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <?php if ($this->ion_auth->is_admin()): ?>
                    <div class="col-12 col-md-4 mb-4">
                        <div class="pg-card">
                            <div class="pg-card-header">Running Text</div>
                            <div class="pg-card-body">
                                <p style="font-size:.78rem;color:var(--text-2);margin-bottom:.75rem;">
                                    Running text akan muncul di bagian atas footer siswa.
                                </p>
                                <?= form_open('', ['id' => 'formrunningtext']) ?>
                                <table id="tb-text" class="mb-3">
                                    <?php for ($i = 0; $i < 5; $i++):
                                        $text = isset($running_text[$i]) ? $running_text[$i]->text : ''; ?>
                                        <tr>
                                            <td width="28" class="text-center" style="color:var(--text-2);font-size:.75rem;"><?= $i + 1 ?></td>
                                            <td class="editable"><?= $text ?></td>
                                        </tr>
                                    <?php endfor; ?>
                                </table>
                                <div class="text-right">
                                    <button type="submit" class="btn-g primary">
                                        <i class="fa fa-save"></i> Simpan
                                    </button>
                                </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-12 <?= $this->ion_auth->is_admin() ? 'col-md-8' : '' ?> mb-4">
                    <div class="pg-card">
                        <div class="pg-card-header">Info / Pengumuman</div>
                        <div class="pg-card-body">
                            <?= form_open('', ['id' => 'formpengumuman']) ?>
                            <div class="f-group">
                                <label class="kepada-label">Kepada</label>
                                <select id="opsi-kepada" name="kepada[]" class="select2 form-control" multiple required>
                                    <option value="guru">Semua Guru</option>
                                    <option value="siswa">Semua Siswa</option>
                                    <?php foreach ($kelas as $key => $value): ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="f-group last">
                                <textarea class="w-100" id="text-pengumuman" name="text" required></textarea>
                            </div>
                            <?php if ($this->ion_auth->is_admin()): ?>
                                <input type="hidden" name="dari" value="0">
                            <?php else: ?>
                                <input type="hidden" name="dari" value="<?= $guru->id_guru ?>">
                            <?php endif; ?>
                            <div class="text-right mt-3">
                                <button type="submit" class="btn-g primary">
                                    <i class="fa fa-save"></i> Simpan
                                </button>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="pg-card">
                        <div class="pg-card-header"><?= $subjudul ?></div>
                        <div class="pg-card-body">
                            <?php foreach ($pengumumans as $p): ?>
                                <div class="post-card">
                                    <div class="post-body">
                                        <div class="d-flex align-items-center" style="gap:.75rem;">
                                            <?php if ($p->dari == '0'): ?>
                                                <div class="post-avatar-admin">A</div>
                                                <div>
                                                    <div class="post-author">Admin</div>
                                                    <div class="post-time"><?= buat_tanggal(date('D, d M Y H:i', strtotime($p->tanggal))) ?></div>
                                                </div>
                                            <?php else: ?>
                                                <div class="post-avatar">
                                                    <img src="<?= $p->foto ? base_url() . $p->foto : base_url('assets/img/siswa.png') ?>" alt="">
                                                </div>
                                                <div>
                                                    <div class="post-author"><?= $p->nama_guru ?></div>
                                                    <div class="post-time"><?= $p->tanggal ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-text"><?= $p->text ?></div>
                                        <div class="post-actions" id="parent<?= $p->id_post ?>">
                                            <button type="button" class="btn-post btn-toggle"
                                                data-id="<?= $p->id_post ?>" data-toggle="modal" data-target="#komentarModal">
                                                <i class="fas fa-reply"></i> Komentar
                                            </button>
                                            <button type="button" id="trigger<?= $p->id_post ?>"
                                                class="btn-post action-collapse"
                                                data-toggle="collapse"
                                                href="#collapse-<?= $p->id_post ?>">
                                                <i class="fa fa-comments"></i> <?= $p->jml ?> komentar
                                            </button>
                                            <button type="button" class="btn-post danger"
                                                onclick="hapusPost(<?= $p->id_post ?>)">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <div id="collapse-<?= $p->id_post ?>" class="collapse toggle-comment comment-wrap"
                                        data-id="<?= $p->id_post ?>">
                                        <div id="konten<?= $p->id_post ?>"></div>
                                        <div id="loading<?= $p->id_post ?>" class="text-center d-none py-2">
                                            <div class="spinner-grow spinner-grow-sm text-info"></div>
                                        </div>
                                        <?php if ($p->jml == '0'): ?>
                                            <p style="font-size:.78rem;color:var(--text-2);text-align:center;margin:.5rem 0 0;">Belum ada komentar</p>
                                        <?php else: ?>
                                            <div id="loadmore<?= $p->id_post ?>"
                                                onclick="getComments(<?= $p->id_post ?>)"
                                                class="loadmore-btn">
                                                Muat komentar lainnya…
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<!-- Modal Komentar -->
<div class="modal fade pg-modal" id="komentarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tulis Komentar</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?= form_open('create', ['id' => 'komentar']) ?>
                <input type="hidden" id="id-post" name="id_post" value="">
                <div class="input-group">
                    <input type="text" name="text" placeholder="Tulis komentar…" class="form-control" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn-g success" style="border-radius:0 var(--radius-sm) var(--radius-sm) 0;">Kirim</button>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-g" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Balasan -->
<div class="modal fade pg-modal" id="balasanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tulis Balasan</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <?= form_open('create', ['id' => 'balasan']) ?>
                <input type="hidden" id="id-comment" name="id_comment" value="">
                <div class="input-group">
                    <input type="text" name="text" placeholder="Tulis balasan…" class="form-control" required>
                    <div class="input-group-append">
                        <button type="submit" class="btn-g success" style="border-radius:0 var(--radius-sm) var(--radius-sm) 0;">Kirim</button>
                    </div>
                </div>
                <?= form_close() ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-g" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    var guru = '<?= isset($guru) ? $guru->id_guru : "" ?>';

    function createTime(d) {
        var date = new Date(d),
            h = date.getHours(),
            m = date.getMinutes();
        var sH = h < 10 ? '0' + h : '' + h,
            sM = m < 10 ? '0' + m : '' + m;
        var hari = daysdifference(d);
        if (hari === 0) return sH + ':' + sM;
        if (hari === 1) return 'kemarin ' + sH + ':' + sM;
        return jQuery.timeago(d) + ', ' + sH + ':' + sM;
    }

    function daysdifference(last) {
        return Math.round(Math.abs((new Date(last).getTime() - new Date().getTime()) / (1000 * 3600 * 24)));
    }

    function avatarHtml(v, size) {
        var s = size === 'sm' ? 34 : 40;
        var cls = size === 'sm' ? 'c-avatar-admin' : 'post-avatar-admin';
        if (v.dari == '0') return '<div class="' + cls + '" style="width:' + s + 'px;height:' + s + 'px;">A</div>';
        var foto = v.dari_group == '2' ?
            (v.foto ? base_url + v.foto : base_url + 'assets/img/siswa.png') :
            (v.foto_siswa ? base_url + v.foto_siswa : base_url + 'assets/img/siswa.png');
        return '<img src="' + foto + '" style="width:' + s + 'px;height:' + s + 'px;border-radius:50%;object-fit:cover;border:1px solid rgba(34,211,238,0.25);" alt="">';
    }

    function addComments(id, comments, append) {
        var html = '';
        $.each(comments, function(i, v) {
            var dari = v.dari_group === '1' ? 'Admin' : (v.dari_group === '2' ? v.nama_guru : v.nama_siswa);
            html += '<div class="comment-item" id="parent-reply' + v.id_comment + '">' +
                avatarHtml(v, 'sm') +
                '<div class="w-100">' +
                '  <div class="c-bubble"><b>' + dari + '</b>' + v.text + '</div>' +
                '  <div class="c-meta">' +
                '    <span>' + createTime(v.tanggal) + '</span>' +
                '    <span id="trigger-reply' + v.id_comment + '" class="action-collapse" data-toggle="collapse" href="#collapse-reply' + v.id_comment + '">' + v.jml + ' balasan</span>' +
                '    <span class="btn-toggle-reply" data-id="' + v.id_comment + '" data-toggle="modal" data-target="#balasanModal"><i class="fas fa-reply"></i> Balas</span>' +
                '    <span onclick="hapusKomentar(' + v.id_comment + ')" style="color:#f87171;"><i class="fa fa-trash"></i> Hapus</span>' +
                '  </div>' +
                '  <div id="collapse-reply' + v.id_comment + '" class="collapse toggle-reply mt-2" data-id="' + v.id_comment + '" style="padding-left:.5rem;">' +
                (v.jml != '0' ? '<div id="konten-reply' + v.id_comment + '"></div><div id="loadmore-reply' + v.id_comment + '" onclick="getReplies(' + v.id_comment + ')" class="loadmore-btn">Muat balasan…</div>' : '') +
                '    <div id="loading-reply' + v.id_comment + '" class="text-center d-none"><div class="spinner-grow spinner-grow-sm text-info"></div></div>' +
                '  </div>' +
                '</div></div>';
        });

        append ? $('#konten' + id).append(html) : $('#konten' + id).prepend(html);

        $('.toggle-reply').on('shown.bs.collapse', function() {
            var rid = $(this).data('id');
            if ($('#konten-reply' + rid).find('.comment-item').length === 0) $('#loadmore-reply' + rid).click();
        });
    }

    function addReplies(id, replies, append) {
        var html = '';
        $.each(replies, function(i, v) {
            if ($('.media' + v.id_reply).length) return;
            var dari = v.dari_group == '1' ? 'Admin' : (v.dari_group == '2' ? v.nama_guru : v.nama_siswa);
            html += '<div class="comment-item media' + v.id_reply + '">' +
                avatarHtml(v, 'xs') +
                '<div class="w-100">' +
                '  <div class="r-bubble"><b>' + dari + '</b>' + v.text + '</div>' +
                '  <div class="c-meta">' +
                '    <span>' + createTime(v.tanggal) + '</span>' +
                '    <span onclick="hapusReply(' + v.id_reply + ')" style="color:#f87171;"><i class="fa fa-trash"></i> Hapus</span>' +
                '  </div>' +
                '</div></div>';
        });
        append ? $('#konten-reply' + id).append(html) : $('#konten-reply' + id).prepend(html);
    }

    function getComments(id) {
        $('#loading' + id).removeClass('d-none');
        $('#loadmore' + id).addClass('d-none');
        var $count = $('#loadmore' + id),
            page = $count.data('count') || 0;
        setTimeout(function() {
            $.ajax({
                url: base_url + 'pengumuman/getcomment/' + id + '/' + page,
                type: 'GET',
                success: function(r) {
                    page += 1;
                    $count.data('count', page);
                    if (r.length === 5) $('#loadmore' + id).removeClass('d-none');
                    $('#loading' + id).addClass('d-none');
                    addComments(id, r, true);
                }
            });
        }, 500);
    }

    function getReplies(id) {
        $('#loading-reply' + id).removeClass('d-none');
        $('#loadmore-reply' + id).addClass('d-none');
        var $count = $('#loadmore-reply' + id),
            page = $count.data('count') || 0;
        setTimeout(function() {
            $.ajax({
                url: base_url + 'pengumuman/getreplies/' + id + '/' + page,
                type: 'GET',
                success: function(r) {
                    page += 1;
                    $count.data('count', page);
                    if (r.length === 5) $('#loadmore-reply' + id).removeClass('d-none');
                    $('#loading-reply' + id).addClass('d-none');
                    addReplies(id, r, true);
                }
            });
        }, 500);
    }

    function hapusPost(id) {
        swal.fire({
                title: 'Hapus Pengumuman',
                text: 'Pengumuman ini akan dihapus',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'HAPUS'
            })
            .then(function(r) {
                if (!r.value) return;
                swal.fire({
                    text: 'Silahkan tunggu…',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => swal.showLoading()
                });
                $.ajax({
                    url: base_url + 'pengumuman/hapuspost/' + id,
                    type: 'GET',
                    success: function(data) {
                        data ? swal.fire({
                                title: 'Sukses',
                                text: 'Pengumuman dihapus',
                                icon: 'success'
                            }).then(r => {
                                if (r.value) location.reload();
                            }) :
                            swal.fire({
                                title: 'ERROR',
                                text: 'Pengumuman tidak dihapus',
                                icon: 'error'
                            });
                    }
                });
            });
    }

    function hapusKomentar(id) {
        swal.fire({
                title: 'Hapus Komentar',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'HAPUS'
            })
            .then(function(r) {
                if (!r.value) return;
                $.ajax({
                    url: base_url + 'pengumuman/hapuskomentar/' + id,
                    type: 'GET',
                    success: function(data) {
                        swal.fire({
                            title: data ? 'Sukses' : 'ERROR',
                            text: data ? 'Komentar dihapus' : 'Komentar tidak dihapus',
                            icon: data ? 'success' : 'error'
                        });
                    }
                });
            });
    }

    function hapusReply(id) {
        swal.fire({
                title: 'Hapus Balasan',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'HAPUS'
            })
            .then(function(r) {
                if (!r.value) return;
                $.ajax({
                    url: base_url + 'pengumuman/hapusbalasan/' + id,
                    type: 'GET',
                    success: function(data) {
                        swal.fire({
                            title: data ? 'Sukses' : 'ERROR',
                            text: data ? 'Balasan dihapus' : 'Balasan tidak dihapus',
                            icon: data ? 'success' : 'error'
                        });
                    }
                });
            });
    }

    $(function() {
        $('.editable').attr('contentEditable', true);
        $('.select2').select2();

        $('#text-pengumuman').summernote({
            placeholder: 'Tulis Pengumuman',
            tabsize: 2,
            minHeight: 100,
        });

        $('.toggle-comment').on('shown.bs.collapse', function() {
            var id = $(this).data('id');
            if ($(this).find('.comment-item').length === 0) $('#loadmore' + id).click();
        });

        $('#formpengumuman').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            swal.fire({
                text: 'Silahkan tunggu…',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });
            $.ajax({
                url: base_url + 'pengumuman/save',
                type: 'POST',
                dataType: 'JSON',
                data: $(this).serialize(),
                success: function(data) {
                    data ? swal.fire({
                            title: 'Sukses',
                            text: 'Pengumuman berhasil disimpan',
                            icon: 'success'
                        }).then(r => {
                            if (r.value) location.href = base_url + 'pengumuman';
                        }) :
                        swal.fire({
                            title: 'ERROR',
                            text: 'Pengumuman tidak tersimpan',
                            icon: 'error'
                        });
                },
                error: function(xhr) {
                    swal.fire({
                        title: 'Error',
                        text: JSON.parse(xhr.responseText).Message,
                        icon: 'error'
                    });
                }
            });
        });

        $('#komentarModal').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');
            $('#id-post').val(id);
            if (!$('#collapse-' + id).hasClass('show')) $('#trigger' + id).click();
        });

        $('#balasanModal').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');
            $('#id-comment').val(id);
            if (!$('#collapse-reply' + id).hasClass('show')) $('#trigger-reply' + id).click();
        });

        $('#komentar').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var id = $(this).find('input[name=id_post]').val();
            $.ajax({
                url: base_url + 'pengumuman/savekomentar',
                data: $(this).serialize(),
                method: 'POST',
                dataType: 'JSON',
                success: function(r) {
                    $('#komentarModal').modal('hide');
                    addComments(id, r, false);
                },
                error: function() {
                    $('#komentarModal').modal('hide');
                    showDangerToast('Error, komentar tidak terkirim');
                }
            });
        });

        $('#balasan').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var id = $(this).find('input[name=id_comment]').val();
            $.ajax({
                url: base_url + 'pengumuman/savebalasan',
                data: $(this).serialize(),
                method: 'POST',
                dataType: 'JSON',
                success: function(r) {
                    $('#balasanModal').modal('hide');
                    addReplies(id, r, false);
                },
                error: function() {
                    $('#balasanModal').modal('hide');
                    showDangerToast('Error, balasan tidak terkirim');
                }
            });
        });

        $('#formrunningtext').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var no = 1,
                jsonObj = [];
            $('#tb-text tr').each(function() {
                jsonObj.push({
                    id_text: no,
                    text: $(this).find('.editable').text()
                });
                no++;
            });
            $.ajax({
                url: base_url + 'pengumuman/saverunningtext',
                type: 'POST',
                data: $(this).serialize() + '&text=' + JSON.stringify(jsonObj),
                success: function(r) {
                    r.status[0] ? location.reload() : showDangerToast('Tidak bisa menyimpan');
                },
                error: function() {
                    showDangerToast('Error, tidak bisa menyimpan');
                }
            });
        });
    });
</script>
