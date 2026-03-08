<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

    :root {
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        --accent: #f97316;
        --accent-hover: #ea6c0a;
        --surface: rgba(15, 15, 25, 0.85);
        --text-primary: #f1f5f9;
        --text-muted: #94a3b8;
        --radius: 14px;
        --radius-sm: 8px;
    }

    * {
        font-family: 'Lexend', sans-serif;
        box-sizing: border-box;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        box-shadow: var(--glass-shadow);
    }

    .glass-header {
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.3), rgba(234, 108, 10, 0.15));
        border-bottom: 1px solid var(--glass-border);
        border-radius: var(--radius) var(--radius) 0 0;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .glass-header h6 {
        margin: 0;
        color: var(--text-primary);
        font-weight: 600;
        font-size: .95rem;
    }

    .btn-glass {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
        border-radius: var(--radius-sm);
        padding: .35rem .75rem;
        font-size: .8rem;
        font-family: 'Lexend', sans-serif;
        font-weight: 500;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }

    .btn-glass:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .btn-glass.accent {
        background: linear-gradient(135deg, var(--accent), var(--accent-hover));
        border-color: transparent;
    }

    .btn-glass.accent:hover {
        filter: brightness(1.1);
    }

    .modal-glass .modal-content {
        background: rgba(15, 15, 25, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius);
        color: var(--text-primary);
    }

    .modal-glass .modal-header {
        border-bottom: 1px solid var(--glass-border);
        padding: 1rem 1.25rem;
    }

    .modal-glass .modal-footer {
        border-top: 1px solid var(--glass-border);
    }

    .modal-glass .modal-title {
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-primary);
    }

    .modal-glass .close {
        color: var(--text-muted);
        opacity: 1;
    }

    .modal-glass .close:hover {
        color: #fff;
    }

    .form-group-glass {
        margin-bottom: .85rem;
    }

    .form-group-glass label {
        font-size: .8rem;
        font-weight: 500;
        color: var(--text-muted);
        margin-bottom: .3rem;
        display: block;
    }

    .form-control-glass {
        background: rgba(255, 255, 255, 0.07);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        padding: .5rem .75rem;
        font-family: 'Lexend', sans-serif;
        font-size: .85rem;
        width: 100%;
        transition: border-color .2s;
    }

    .form-control-glass:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.1);
    }

    .form-control-glass::placeholder {
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
        font-size: .84rem;
    }

    table#users tbody tr:hover {
        background: rgba(255, 255, 255, 0.04) !important;
    }
</style>

<div class="content-wrapper pt-4" style="background: linear-gradient(135deg, #0f0f19 0%, #1a1a2e 50%, #16213e 100%); min-height: 100vh;">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 style="font-family:'Lexend',sans-serif;font-weight:700;color:#f1f5f9;font-size:1.5rem;"><?= $judul ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="glass-card">
                <div class="glass-header">
                    <h6><?= $subjudul ?></h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" onclick="reload_ajax()" class="btn-glass">
                            <i class="fa fa-sync"></i>
                            <span class="d-none d-sm-inline">Reload</span>
                        </button>
                        <button type="button" data-toggle="modal" data-target="#createAdminModal" class="btn-glass accent">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline">Tambah Admin</span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive px-4 py-3">
                    <table id="users" class="w-100 table table-hover" style="border-collapse:separate;border-spacing:0;">
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
<div class="modal fade modal-glass" id="createAdminModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body px-4">
                <?php
                $fields = [
                    ['label' => 'First Name',        'name' => 'first_name',       'type' => 'input'],
                    ['label' => 'Last Name',          'name' => 'last_name',        'type' => 'input'],
                    ['label' => 'Email',              'name' => 'email',            'type' => 'input'],
                    ['label' => 'Username',           'name' => 'username',         'type' => 'input'],
                    ['label' => 'Password',           'name' => 'password',         'type' => 'password'],
                    ['label' => 'Confirm Password',   'name' => 'confirm_password', 'type' => 'password'],
                ];
                foreach ($fields as $f):
                    echo '<div class="form-group-glass">';
                    echo '<label for="' . $f['name'] . '">' . $f['label'] . '</label>';
                    echo form_error($f['name']);
                    if ($f['type'] === 'password') {
                        echo form_password(['name' => $f['name'], 'class' => 'form-control-glass'], '', 'required');
                    } else {
                        echo form_input(['name' => $f['name'], 'class' => 'form-control-glass'], set_value($f['name']), 'required');
                    }
                    echo '</div>';
                endforeach;
                ?>
            </div>
            <div class="modal-footer px-4 pb-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn-glass" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn-glass accent">
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
