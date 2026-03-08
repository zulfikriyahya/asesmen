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

  .table-wrap {
    padding: 1.5rem;
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
            <button type="button" onclick="reload_ajax()" class="btn-g">
              <i class="fa fa-sync"></i>
              <span class="d-none d-sm-inline">Reload</span>
            </button>
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

        <div class="table-wrap">
          <table id="users" class="w-100 table table-hover">
            <thead>
              <tr>
                <th class="text-center" style="width:40px">No.</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Password</th>
                <th>Jabatan</th>
                <th class="text-center">Reset Login</th>
                <th class="text-center">Aksi</th>
              </tr>
            </thead>
          </table>
        </div>

        <div class="overlay-g d-none" id="loading">
          <div class="spinner-grow text-info"></div>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
  var user_id = '<?= $user->id ?>';
</script>
<script src="<?= base_url() ?>/assets/app/js/users/guru/data.js"></script>
