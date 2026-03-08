{{FILE: users/admin/data.php}}
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

    <section class="content text-dark">
        <div class="container-fluid">
            <div class="card card-default my-shadow">

                <div class="card-header bg-orange with-border">
                    <h6 class="card-title text-bold"><?= $subjudul ?></h6>
                    <div class="card-tools">
                        <button type="button" onclick="reload_ajax()" class="btn btn-sm btn-default">
                            <i class="fa fa-sync"></i>
                            <span class="d-none d-sm-inline-block ml-1">Reload</span>
                        </button>
                        <button type="button" data-toggle="modal" data-target="#createAdminModal"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline-block ml-1">Tambah Admin</span>
                        </button>
                    </div>
                </div>

                <div class="card-body text-dark"></div>

                <div class="table-responsive px-4 pb-3" style="border:0">
                    <table id="users" class="w-100 table table-striped table-bordered table-hover">
                        <thead class="bg-maroon">
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
<div class="modal fade" id="createAdminModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                $fields = [
                    ['label' => 'First name',        'name' => 'first_name',       'type' => 'input'],
                    ['label' => 'Last name',         'name' => 'last_name',        'type' => 'input'],
                    ['label' => 'Email',             'name' => 'email',            'type' => 'input'],
                    ['label' => 'Username',          'name' => 'username',         'type' => 'input'],
                    ['label' => 'Password',          'name' => 'password',         'type' => 'password'],
                    ['label' => 'Confirm password',  'name' => 'confirm_password', 'type' => 'password'],
                ];
                foreach ($fields as $f):
                    echo '<div class="form-group row">';
                    echo form_label($f['label'] . ':', $f['name'], ['class' => 'col-md-4 col-form-label']);
                    echo form_error($f['name']);
                    if ($f['type'] === 'password') {
                        echo form_password(['name' => $f['name'], 'class' => 'col-md-7 form-control'], '', 'required');
                    } else {
                        echo form_input(['name' => $f['name'], 'class' => 'col-md-7 form-control'], set_value($f['name']), 'required');
                    }
                    echo '</div>';
                endforeach;
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">
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
