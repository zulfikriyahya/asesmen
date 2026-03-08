<style>
  @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&display=swap');

  :root {
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

  /* ── Override AdminLTE layout ── */
  * {
    font-family: 'Lexend', sans-serif !important;
    box-sizing: border-box;
  }

  .content-wrapper {
    background: linear-gradient(140deg, #080e1a 0%, #0d1526 55%, #0a1929 100%) !important;
    min-height: 100vh !important;
  }

  /* ── Page title ── */
  .content-header {
    padding: 1.75rem 1.5rem .5rem !important;
  }

  .content-header h1 {
    font-size: 1.4rem !important;
    font-weight: 700 !important;
    color: var(--text-1) !important;
    letter-spacing: -.01em;
    margin: 0 !important;
  }

  /* ── Glass card ── */
  .g-card {
    background: var(--glass-bg) !important;
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border: 1px solid var(--glass-border) !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow) !important;
    overflow: hidden;
    position: relative;
  }

  .g-card-header {
    background: linear-gradient(135deg, rgba(34, 211, 238, 0.12), rgba(59, 130, 246, 0.08)) !important;
    border-bottom: 1px solid var(--glass-border) !important;
    padding: .9rem 1.5rem !important;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    flex-wrap: wrap;
  }

  .g-card-header h3 {
    margin: 0 !important;
    font-size: .9rem !important;
    font-weight: 600 !important;
    color: var(--text-1) !important;
  }

  .g-card-body {
    padding: 1.5rem !important;
  }

  /* ── Buttons ── */
  .btn-g {
    display: inline-flex !important;
    align-items: center;
    gap: .4rem;
    padding: .4rem .9rem !important;
    border-radius: var(--radius-sm) !important;
    font-size: .8rem !important;
    font-weight: 500 !important;
    cursor: pointer;
    border: 1px solid var(--glass-border) !important;
    background: var(--glass-bg) !important;
    color: var(--text-1) !important;
    transition: background .2s, filter .2s;
    white-space: nowrap;
    box-shadow: none !important;
    line-height: 1.4;
  }

  .btn-g:hover,
  .btn-g:focus {
    background: var(--glass-hover) !important;
    color: #fff !important;
  }

  .btn-g.success {
    background: linear-gradient(135deg, #22c55e, #16a34a) !important;
    border-color: transparent !important;
    color: #fff !important;
  }

  .btn-g.success:hover {
    filter: brightness(1.1);
  }

  .btn-g.danger {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
    border-color: transparent !important;
    color: #fff !important;
  }

  .btn-g.danger:hover {
    filter: brightness(1.1);
  }

  /* ── DataTables override ── */
  div.dataTables_wrapper {
    color: var(--text-1) !important;
  }

  div.dataTables_wrapper .dataTables_length label,
  div.dataTables_wrapper .dataTables_filter label,
  div.dataTables_wrapper .dataTables_info,
  div.dataTables_wrapper .dataTables_paginate {
    color: var(--text-2) !important;
    font-size: .82rem !important;
  }

  div.dataTables_wrapper select,
  div.dataTables_wrapper input[type="search"] {
    background: rgba(255, 255, 255, 0.07) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: var(--radius-sm) !important;
    color: var(--text-1) !important;
    padding: .3rem .6rem !important;
    font-size: .8rem !important;
  }

  div.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: var(--glass-bg) !important;
    border: 1px solid var(--glass-border) !important;
    border-radius: var(--radius-sm) !important;
    color: var(--text-2) !important;
    font-size: .78rem !important;
    padding: .3rem .7rem !important;
    margin: 0 2px !important;
  }

  div.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--glass-hover) !important;
    color: #fff !important;
  }

  div.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, var(--accent), var(--accent2)) !important;
    border-color: transparent !important;
    color: #fff !important;
  }

  div.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: .35 !important;
    cursor: default;
  }

  table#users,
  table#users.dataTable {
    color: var(--text-1) !important;
    border-color: var(--glass-border) !important;
  }

  table#users thead th,
  table#users.dataTable thead th {
    background: rgba(34, 211, 238, 0.12) !important;
    color: var(--accent) !important;
    border-color: var(--glass-border) !important;
    font-size: .78rem !important;
    font-weight: 600 !important;
    letter-spacing: .03em;
    padding: .65rem 1rem !important;
    border-bottom: 2px solid rgba(34, 211, 238, 0.2) !important;
  }

  table#users thead th.sorting::after,
  table#users thead th.sorting_asc::after,
  table#users thead th.sorting_desc::after {
    color: rgba(34, 211, 238, 0.5) !important;
  }

  table#users tbody td {
    border-color: var(--glass-border) !important;
    font-size: .83rem !important;
    padding: .6rem 1rem !important;
    vertical-align: middle !important;
    background: transparent !important;
  }

  table#users tbody tr:nth-child(even) td {
    background: rgba(255, 255, 255, 0.02) !important;
  }

  table#users tbody tr:hover td {
    background: rgba(34, 211, 238, 0.05) !important;
  }

  table#users.dataTable tbody tr {
    background: transparent !important;
  }

  /* ── Overlay ── */
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

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h1><?= $judul ?></h1>
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

        <div class="g-card-body">
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
