<?php
$foto    = $profile->foto == null
    ? base_url() . 'assets/img/user.png'
    : base_url() . $profile->foto;
$nama    = $profile->nama_lengkap ?: 'Nama Admin';
$jabatan = $profile->jabatan      ?: 'Administrator';
?>

<nav class="main-header navbar navbar-expand navbar-dark" style="
    background: rgba(15, 17, 23, 0.75);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    box-shadow: 0 2px 20px rgba(0,0,0,0.4);
">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"
                style="color:rgba(255,255,255,0.7); transition: color .2s;">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <div class="d-flex align-items-center" style="gap:.75rem; padding:.25rem .75rem;">
                <img src="<?= $foto ?>" alt="User"
                    style="width:34px;height:34px;object-fit:cover;border-radius:50%;
                            border:2px solid rgba(99,102,241,0.5);
                            box-shadow:0 0 0 3px rgba(99,102,241,0.15);">
                <div style="line-height:1.25;">
                    <span style="display:block;font-size:.82rem;font-weight:600;color:rgba(255,255,255,0.9);">
                        <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <span style="display:block;font-size:.72rem;font-weight:400;color:rgba(255,255,255,0.4);">
                        <?= htmlspecialchars($jabatan, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </div>
        </li>
    </ul>
</nav>
