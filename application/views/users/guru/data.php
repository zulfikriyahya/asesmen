{{FILE: users/guru/data.php}}
<div class="content-wrapper bg-dark pt-4">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="text-bold"><?= $judul ?></h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card card-default my-shadow" style="position:relative;">

        <div class="card-header bg-orange with-border">
          <h3 class="card-title text-bold"><?= $subjudul ?></h3>
          <div class="card-tools">
            <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-default">
              <i class="fa fa-sync"></i>
              <span class="d-none d-sm-inline-block ml-1">Reload</span>
            </button>
            <button type="button" class="btn btn-action btn-success btn-sm"
                    data-action="aktifkan" data-toggle="tooltip" title="Aktifkan">
              <i class="fa fa-users m-1"></i>
              <span class="d-none d-sm-inline-block ml-1">Aktifkan Semua</span>
            </button>
            <button type="button" class="btn btn-action btn-danger btn-sm"
                    data-action="nonaktifkan" data-toggle="tooltip" title="Nonaktifkan">
              <i class="fa fa-ban m-1"></i>
              <span class="d-none d-sm-inline-block ml-1">Nonaktifkan Semua</span>
            </button>
          </div>
        </div>

        <div class="card-body text-dark">
          <div class="table-responsive">
            <table id="users" class="w-100 table table-striped table-bordered table-hover">
              <thead class="bg-maroon">
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
        </div>

        <div class="overlay d-none" id="loading">
          <div class="spinner-grow"></div>
        </div>

      </div>
    </div>
  </section>
</div>

<script>var user_id = '<?= $user->id ?>';</script>
<script src="<?= base_url() ?>/assets/app/js/users/guru/data.js"></script>

