{{FILE: users/data.php}}
<?php /* Halaman ini adalah placeholder/legacy — konten chart belum diimplementasikan */ ?>
<div class="content-wrapper bg-dark pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="text-bold">Manajemen Pengguna</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default my-shadow">

                <div class="card-header bg-orange with-border">
                    <h3 class="card-title text-bold">Master <?= $subjudul ?></h3>
                    <div class="card-tools">
                        <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-default">
                            <i class="fa fa-sync"></i>
                            <span class="d-none d-sm-inline-block ml-1">Reload</span>
                        </button>
                    </div>
                </div>

                <div class="card-body text-dark">
                    <div class="mb-2">
                        <label class="d-inline-flex align-items-center gap-1">
                            <input type="checkbox" id="show_me"> Tampilkan saya
                        </label>
                    </div>
                </div>

                <div class="table-responsive px-4 pb-3" style="border:0">
                    <table id="users" class="w-100 table table-striped table-bordered table-hover">
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

