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

    .btn-g:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .checkbox-glass {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        font-size: .84rem;
        color: var(--text-muted);
        cursor: pointer;
    }

    .checkbox-glass input[type="checkbox"] {
        accent-color: var(--accent);
        width: 14px;
        height: 14px;
        cursor: pointer;
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
                    <h1 style="font-weight:700;color:#f1f5f9;font-size:1.5rem;">Manajemen Pengguna</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="glass-card">
                <div class="glass-header">
                    <h3>Master <?= $subjudul ?></h3>
                    <button type="button" onclick="reload_ajax()" class="btn-g">
                        <i class="fa fa-sync"></i>
                        <span class="d-none d-sm-inline">Reload</span>
                    </button>
                </div>

                <div class="px-4 pt-3 pb-2">
                    <label class="checkbox-glass">
                        <input type="checkbox" id="show_me"> Tampilkan saya
                    </label>
                </div>

                <div class="table-responsive px-4 pb-4">
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
                        <tfoot>
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
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    var user_id = '<?= $user->id ?>';
</script>
<script src="<?= base_url() ?>assets/dist/js/app/users/data.js"></script>
