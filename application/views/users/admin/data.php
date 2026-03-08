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
        --accent-dim: rgba(34, 211, 238, 0.15);
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

    /* ── Layout ── */
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

    /* ── Glass card ── */
    .g-card {
        background: var(--glass-bg);
        backdrop-filter: blur(18px);
        -webkit-backdrop-filter: blur(18px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
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

    .g-card-header h6 {
        margin: 0;
        font-size: .9rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .g-card-body {
        padding: 1.5rem;
    }

    /* ── Buttons ── */
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
        transition: background .2s, border-color .2s, filter .2s;
        white-space: nowrap;
    }

    .btn-g:hover {
        background: var(--glass-hover);
        border-color: rgba(99, 179, 237, .35);
        color: #fff;
    }

    .btn-g.accent {
        background: linear-gradient(135deg, var(--accent), var(--accent2));
        border-color: transparent;
        color: #fff;
    }

    .btn-g.accent:hover {
        filter: brightness(1.1);
    }

    /* ── Table ── */
    .table-wrap {
        padding: 0 1.5rem 1.5rem;
    }

    table#users {
        color: var(--text-1) !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
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
        font-size: .83rem;
        padding: .6rem 1rem !important;
        vertical-align: middle;
    }

    table#users tbody tr {
        transition: background .15s;
    }

    table#users tbody tr:hover {
        background: rgba(34, 211, 238, 0.05) !important;
    }

    /* ── Modal ── */
    .g-modal .modal-content {
        background: rgba(8, 14, 26, 0.96);
        backdrop-filter: blur(24px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        color: var(--text-1);
    }

    .g-modal .modal-header {
        background: linear-gradient(135deg, rgba(34, 211, 238, 0.1), rgba(59, 130, 246, 0.06));
        border-bottom: 1px solid var(--glass-border);
        padding: 1rem 1.5rem;
    }

    .g-modal .modal-title {
        font-size: .95rem;
        font-weight: 600;
        color: var(--text-1);
    }

    .g-modal .modal-body {
        padding: 1.5rem;
    }

    .g-modal .modal-footer {
        border-top: 1px solid var(--glass-border);
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
    }

    .g-modal .close {
        color: var(--text-2);
        opacity: 1;
        font-size: 1.3rem;
        line-height: 1;
    }

    .g-modal .close:hover {
        color: #fff;
    }

    /* ── Form fields ── */
    .f-group {
        margin-bottom: 1rem;
    }

    .f-group:last-child {
        margin-bottom: 0;
    }

    .f-group label {
        display: block;
        font-size: .76rem;
        font-weight: 500;
        color: var(--text-2);
        margin-bottom: .35rem;
    }

    .f-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-1);
        padding: .5rem .85rem;
        font-size: .84rem;
        transition: border-color .2s, background .2s;
    }

    .f-input:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(34, 211, 238, 0.06);
    }

    .f-input::placeholder {
        color: var(--text-2);
        opacity: .6;
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
                    <h6><?= $subjudul ?></h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" onclick="reload_ajax()" class="btn-g">
                            <i class="fa fa-sync"></i>
                            <span class="d-none d-sm-inline">Reload</span>
                        </button>
                        <button type="button" data-toggle="modal" data-target="#createAdminModal" class="btn-g accent">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline">Tambah Admin</span>
                        </button>
                    </div>
                </div>

                <div class="table-wrap">
                    <table id="users" class="w-100 table table-hover">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>Created On</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
        </div>
    </section>
</div>

<?= form_open('create', ['id' => 'create']) ?>
<div class="modal fade g-modal" id="createAdminModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $fields = [
                    ['label' => 'First Name',       'name' => 'first_name',       'type' => 'input'],
                    ['label' => 'Last Name',         'name' => 'last_name',        'type' => 'input'],
                    ['label' => 'Email',             'name' => 'email',            'type' => 'input'],
                    ['label' => 'Username',          'name' => 'username',         'type' => 'input'],
                    ['label' => 'Password',          'name' => 'password',         'type' => 'password'],
                    ['label' => 'Confirm Password',  'name' => 'confirm_password', 'type' => 'password'],
                ];
                foreach ($fields as $f):
                    echo '<div class="f-group">';
                    echo '<label for="' . $f['name'] . '">' . $f['label'] . '</label>';
                    echo form_error($f['name']);
                    if ($f['type'] === 'password') {
                        echo form_password(['name' => $f['name'], 'class' => 'f-input'], '', 'required');
                    } else {
                        echo form_input(['name' => $f['name'], 'class' => 'f-input'], set_value($f['name']), 'required');
                    }
                    echo '</div>';
                endforeach;
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-g" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn-g accent">
                    <i class="fa fa-plus"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<script>
    var user_id = '<?= $user->id ?>';
</script>
<script src="<?= base_url() ?>/assets/app/js/users/admin/data.js"></script>
