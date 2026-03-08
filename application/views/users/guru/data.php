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

        <div class="table-responsive px-4 py-3">
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
          <div class="spinner-grow text-warning"></div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  var user_id = '<?= $user->id ?>';
</script>
<script src="<?= base_url() ?>/assets/app/js/users/guru/data.js"></script>
