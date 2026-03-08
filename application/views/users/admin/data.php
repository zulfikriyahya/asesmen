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
                    <h6><?= $subjudul ?></h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" onclick="reload_ajax()" class="btn-g">
                            <i class="fa fa-sync"></i>
                            <span class="d-none d-sm-inline">Reload</span>
                        </button>
                        <button type="button" data-toggle="modal" data-target="#createAdminModal" class="btn-g accent">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline">Tambah Admin</span>
                        </button>
                    </div>
                </div>

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
                    </table>
                </div>

            </div>
        </div>
    </section>
</div>

<?= form_open('create', ['id' => 'create']) ?>
<div class="modal fade g-modal" id="createAdminModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
                    ['label' => 'First Name',       'name' => 'first_name',       'type' => 'input'],
                    ['label' => 'Last Name',         'name' => 'last_name',        'type' => 'input'],
                    ['label' => 'Email',             'name' => 'email',            'type' => 'input'],
                    ['label' => 'Username',          'name' => 'username',         'type' => 'input'],
                    ['label' => 'Password',          'name' => 'password',         'type' => 'password'],
                    ['label' => 'Confirm Password',  'name' => 'confirm_password', 'type' => 'password'],
                ];
                foreach ($fields as $f):
                    echo '<div class="f-group">';
                    echo '<label for="' . $f['name'] . '">' . $f['label'] . '</label>';
                    echo form_error($f['name']);
                    if ($f['type'] === 'password') {
                        echo form_password(['name' => $f['name'], 'class' => 'f-input'], '', 'required');
                    } else {
                        echo form_input(['name' => $f['name'], 'class' => 'f-input'], set_value($f['name']), 'required');
                    }
                    echo '</div>';
                endforeach;
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-g" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn-g accent">
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
