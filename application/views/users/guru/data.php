<link rel="stylesheet" href="<?= base_url() ?>assets/app/css/users.css">

<div class="content-wrapper">
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
