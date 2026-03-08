{{FILE: _templates/topnav/_menu.php}}
<nav class="navbar navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <a href="<?= base_url() ?>" class="navbar-brand">
                <i class="fa fa-laptop"></i> <b>OLT</b>EST
            </a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="#"><?= $mhs->nama ?> &mdash; <?= $mhs->nama_kelas ?></a></li>
            </ul>
        </div>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li><a href="#" onclick="simpan_akhir()">Selesai Ujian</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                        <?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') ?> <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="<?= base_url('logout') ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
