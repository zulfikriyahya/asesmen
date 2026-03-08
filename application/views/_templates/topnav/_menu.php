<nav class="navbar navbar-static-top" style="
    background: rgba(12,14,20,0.88);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    box-shadow: 0 2px 20px rgba(0,0,0,0.4);
    font-family: 'Lexend', sans-serif;
">
    <div class="container">
        <div class="navbar-header">
            <a href="<?= base_url() ?>" class="navbar-brand" style="
                color: rgba(255,255,255,0.85);
                font-weight: 600;
                font-size: .95rem;
                letter-spacing: .02em;
            ">
                <i class="fa fa-laptop" style="color:#818cf8;"></i> <b>OL</b>TEST
            </a>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse"
                style="border-color:rgba(255,255,255,0.1); background:rgba(255,255,255,0.05);">
                <i class="fa fa-bars" style="color:rgba(255,255,255,0.6);"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#" style="color:rgba(255,255,255,0.6); font-size:.85rem;">
                        <?= $mhs->nama ?> &mdash; <?= $mhs->nama_kelas ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#" onclick="simpan_akhir()" style="
                        color: rgba(239,68,68,0.85);
                        font-size: .83rem;
                        font-weight: 500;
                    ">Selesai Ujian</a>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="
                        color: rgba(255,255,255,0.65);
                        font-size: .83rem;
                    ">
                        <?= htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8') ?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu" style="
                        background: rgba(15,17,23,0.96);
                        backdrop-filter: blur(12px);
                        border: 1px solid rgba(255,255,255,0.08);
                        border-radius: 10px;
                        min-width: 140px;
                    ">
                        <li>
                            <a href="<?= base_url('logout') ?>" style="
                                color: rgba(239,68,68,0.8);
                                font-size: .82rem;
                                padding: .5rem 1rem;
                                display: block;
                            ">Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
