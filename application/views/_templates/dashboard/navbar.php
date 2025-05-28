<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark shadow">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="user-panel border-0 d-flex mt-1">
                <div class="image">
                    <img src="<?= $profile->foto == null ? base_url() . 'assets/img/user.png' : base_url() . $profile->foto; ?>" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <div class="d-block text-light" style="line-height: 1 ">
                        <?= $profile->nama_lengkap == null ? 'Nama Admin' : $profile->nama_lengkap ?>
                        <br>
                        <small class="text-muted">
                            <?= $profile->jabatan == null ? 'Administrator' : $profile->jabatan ?>
                        </small>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>