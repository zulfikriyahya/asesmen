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
        transition: background .2s, border-color .2s;
        white-space: nowrap;
    }

    .btn-g:hover {
        background: var(--glass-hover);
        border-color: rgba(99, 179, 237, .35);
        color: #fff;
    }

    .check-label {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .82rem;
        color: var(--text-2);
        cursor: pointer;
        padding: 1rem 1.5rem;
    }

    .check-label input[type="checkbox"] {
        accent-color: var(--accent);
        width: 14px;
        height: 14px;
        cursor: pointer;
    }

    .table-wrap {
        padding: 0 1.5rem 1.5rem;
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
        font-size: .83rem;
        padding: .6rem 1rem !important;
        vertical-align: middle;
    }

    table#users tbody tr:hover {
        background: rgba(34, 211, 238, 0.05) !important;
    }

    table#users tfoot th {
        background: rgba(34, 211, 238, 0.06) !important;
        color: var(--text-2) !important;
        border-color: var(--glass-border) !important;
        font-size: .78rem;
        padding: .6rem 1rem !important;
    }
</style>

<div class="page-wrap content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="page-title">Manajemen Pengguna</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="g-card">

                <div class="g-card-header">
                    <h3>Master <?= $subjudul ?></h3>
                    <button type="button" onclick="reload_ajax()" class="btn-g">
                        <i class="fa fa-sync"></i>
                        <span class="d-none d-sm-inline">Reload</span>
                    </button>
                </div>

                <label class="check-label">
                    <input type="checkbox" id="show_me"> Tampilkan saya
                </label>

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
