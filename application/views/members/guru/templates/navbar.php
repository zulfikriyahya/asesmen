<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark shadow">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        <!-- <li class="nav-item">
            <span class="nav-link text-dark">TP: <?= isset($tp_active) ? $tp_active->tahun : "Belum di set" ?> Smt: <?= isset($smt_active) ? $smt_active->nama_smt : "Belum di set" ?></span>
        </li> -->
    </ul>
    <!-- Right navbar links -->

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel border-0 d-flex mt-1">
            <div class="image">
                <img src="<?= $guru->foto == null ? base_url() . 'assets/img/user.png' : base_url() . $guru->foto; ?>"
                     class="img-square elevation-2" alt="User Image">
            </div>
            <div class="info">
            <div class="d-block text-light" style="line-height: 1 ">
                        <?= $guru->nama_guru == null ? 'Anonim' : $guru->nama_guru ?>
                        <br>
                        <small class="text-muted">
                            <?= $guru->nip == null ? 'Administrator' : $guru->nip ?>
                        </small>
                    </div>
            </div>
        </div>
        </li>
    </ul>

</nav>
