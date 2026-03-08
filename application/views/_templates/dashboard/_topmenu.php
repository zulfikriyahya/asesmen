<?php /* Legacy topnav — tidak digunakan di template aktif, dipertahankan apa adanya */ ?>
<a href="<?= base_url('dashboard') ?>" class="logo">
    <span class="logo-mini"><b>CBT</b></span>
    <span class="logo-lg"><b>C</b>omputer <b>B</b>ased <b>T</b>est</span>
</a>

<nav class="navbar navbar-static-top" role="navigation" style="
    background: rgba(12,14,20,0.85);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    font-family: 'Lexend', sans-serif;
">
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:rgba(255,255,255,0.75);">
                    <img src="<?= base_url() ?>assets/dist/img/user1.png" class="user-image" alt="User Image"
                        style="border:2px solid rgba(99,102,241,0.4);border-radius:50%;">
                    <span class="hidden-xs">Hi, <?= $user->first_name ?></span>
                </a>
                <ul class="dropdown-menu" style="
                    background: rgba(15,17,23,0.95);
                    backdrop-filter: blur(12px);
                    border: 1px solid rgba(255,255,255,0.08);
                    border-radius: 10px;
                ">
                    <li class="user-header" style="background: rgba(99,102,241,0.1);">
                        <img src="<?= base_url() ?>assets/dist/img/user1.png" class="img-circle" alt="User Image"
                            style="border:2px solid rgba(99,102,241,0.4);">
                        <p style="color:rgba(255,255,255,0.8);">
                            <?= $user->first_name . ' ' . $user->last_name ?>
                            <small style="color:rgba(255,255,255,0.4);">
                                Anggota sejak <?= date('M, Y', $user->created_on) ?>
                            </small>
                        </p>
                    </li>
                    <li class="user-footer" style="background: rgba(255,255,255,0.02); padding:.75rem 1rem; display:flex; justify-content:space-between;">
                        <a href="<?= base_url() ?>users/edit/<?= $user->id ?>" style="
                            padding:.35rem .85rem; border-radius:6px;
                            background:rgba(99,102,241,0.15); color:rgba(255,255,255,0.7);
                            border:1px solid rgba(99,102,241,0.3); font-size:.8rem; text-decoration:none;
                        ">
                            <?= $this->ion_auth->is_admin() ? 'Edit Profile' : 'Ganti Password' ?>
                        </a>
                        <a href="#" id="logout" style="
                            padding:.35rem .85rem; border-radius:6px;
                            background:rgba(239,68,68,0.12); color:rgba(239,68,68,0.8);
                            border:1px solid rgba(239,68,68,0.25); font-size:.8rem; text-decoration:none;
                        ">Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
