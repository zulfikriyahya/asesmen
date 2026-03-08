<?php
$foto = $profile->foto == null
    ? base_url() . 'assets/img/user.png'
    : base_url() . $profile->foto;
$nama    = $profile->nama_lengkap ?: 'Nama Admin';
$jabatan = $profile->jabatan      ?: 'Administrator';
?>

<nav class="main-header navbar navbar-expand navbar-dark shadow">

    <!-- Left: sidebar toggle -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <!-- Right: user info -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="user-panel border-0 d-flex align-items-center mt-1" style="gap:.6rem;">
                <div class="image flex-shrink-0">
                    <img src="<?= $foto ?>" class="img-circle elevation-2" alt="User Image"
                        style="width:32px;height:32px;object-fit:cover;">
                </div>
                <div class="info" style="line-height:1.2;">
                    <span class="d-block text-light" style="font-size:.85rem;font-weight:600;">
                        <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <small class="text-muted" style="font-size:.75rem;">
                        <?= htmlspecialchars($jabatan, ENT_QUOTES, 'UTF-8') ?>
                    </small>
                </div>
            </div>
        </li>
    </ul>

</nav>
