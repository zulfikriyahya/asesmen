<div class="content-wrapper">
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
