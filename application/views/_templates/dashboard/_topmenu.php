{{FILE: _templates/dashboard/_topmenu.php}}
<?php /* Legacy topnav — tidak digunakan di template aktif, dipertahankan apa adanya */ ?>
<a href="<?= base_url('dashboard') ?>" class="logo">
    <span class="logo-mini"><b>CBT</b></span>
    <span class="logo-lg"><b>C</b>omputer <b>B</b>ased <b>T</b>est</span>
</a>

<nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?= base_url() ?>assets/dist/img/user1.png" class="user-image" alt="User Image">
                    <span class="hidden-xs">Hi, <?= $user->first_name ?></span>
                </a>
                <ul class="dropdown-menu">
                    <li class="user-header">
                        <img src="<?= base_url() ?>assets/dist/img/user1.png" class="img-circle" alt="User Image">
                        <p>
                            <?= $user->first_name . ' ' . $user->last_name ?>
                            <small>Anggota sejak <?= date('M, Y', $user->created_on) ?></small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="<?= base_url() ?>users/edit/<?= $user->id ?>" class="btn btn-default btn-flat">
                                <?= $this->ion_auth->is_admin() ? 'Edit Profile' : 'Ganti Password' ?>
                            </a>
                        </div>
                        <div class="pull-right">
                            <a href="#" id="logout" class="btn btn-default btn-flat">Logout</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

