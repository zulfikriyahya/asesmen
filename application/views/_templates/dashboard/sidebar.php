<?php
$logo_app = $setting->logo_kiri == null
    ? base_url() . 'assets/img/favicon.png'
    : base_url() . $setting->logo_kiri;
?>

<aside class="main-sidebar sidebar-dark-orange my-shadow">

    <!-- Brand -->
    <a href="<?= base_url() ?>" class="brand-link bg-dark d-flex align-items-center" style="gap:.6rem;padding:.75rem 1rem;">
        <img src="<?= $logo_app ?>" alt="App Logo" class="brand-image" style="opacity:.9;width:32px;height:32px;object-fit:contain;margin:0;">
        <span class="brand-text">
            <strong style="font-size:.95rem;"><?= htmlspecialchars($setting->nama_aplikasi, ENT_QUOTES, 'UTF-8') ?></strong>
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-3 mb-0">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent"
                id="tree-menus" data-widget="treeview" role="menu" data-accordion="false">
            </ul>
        </nav>
    </div>

</aside>

<script>
    const page = '<?= $this->uri->segment(1) ?>';
    const pageact = '<?= $this->uri->segment(2) ?>';

    const menus = [
        // DASHBOARD
        {
            header: "<marquee><center>Selamat datang di <strong>ZEDAPPS SCHOOL.</strong></center></marquee>",
            menu: []
        },
        // MASTER
        {
            header: '<b>MASTER</b>',
            cbt: '1',
            menu: [{
                name: 'Master Data',
                icon: 'fas fa-server',
                cbt: '1',
                submenu: [{
                        name: 'Tahun Pelajaran',
                        link: 'datatahun',
                        icon: 'far fa-calendar-check'
                    },
                    {
                        name: 'Mata Pelajaran',
                        link: 'datamapel',
                        icon: 'fa fa-book'
                    },
                    {
                        name: 'Jurusan',
                        link: 'datajurusan',
                        icon: 'fa fa-flask'
                    },
                    {
                        name: 'Kelas / Rombel',
                        link: 'datakelas',
                        icon: 'fa fa-school'
                    },
                    {
                        name: 'Guru',
                        link: 'dataguru',
                        icon: 'fa fa-chalkboard-teacher'
                    },
                    {
                        name: 'Siswa',
                        link: 'datasiswa',
                        icon: 'fa fa-users'
                    },
                ]
            }, ]
        },
        // E-LEARNING
        {
            header: '<b>E-LEARNING</b>',
            cbt: '0',
            menu: [{
                    name: 'Data E-Learning',
                    icon: 'fas fa-chalkboard',
                    cbt: '1',
                    submenu: [{
                            name: 'Materi',
                            link: 'kelasmateri/materi',
                            icon: 'fa fa-pencil-ruler'
                        },
                        {
                            name: 'Tugas',
                            link: 'kelasmateri/tugas',
                            icon: 'fa fa-drafting-compass'
                        },
                        {
                            name: 'Jadwal Pelajaran',
                            link: 'kelasjadwal',
                            icon: 'fa fa-calendar-alt'
                        },
                        {
                            name: 'Jadwal Materi / Tugas',
                            link: 'kelasmaterijadwal',
                            icon: 'fa fa-calendar-check-o'
                        },
                    ]
                },
                {
                    name: 'Pelaksanaan E-Learning',
                    icon: 'fas fa-microscope',
                    cbt: '0',
                    submenu: [{
                            name: 'Kehadiran Harian',
                            link: 'kelasabsensiharian',
                            icon: 'fa fa-user-check'
                        },
                        {
                            name: 'Kehadiran Bulanan',
                            link: 'kelasabsensibulanan',
                            icon: 'fa fa-tasks'
                        },
                        {
                            name: 'Nilai Harian',
                            link: 'kelasstatus',
                            icon: 'far fa-clipboard'
                        },
                        {
                            name: 'Rekap Nilai',
                            link: 'kelasnilai',
                            icon: 'fa fa-file'
                        },
                    ]
                },
            ]
        },
        // CBT
        {
            header: '<b>CBT</b>',
            cbt: '1',
            menu: [{
                    name: 'Data CBT',
                    icon: 'fa fa-user-graduate',
                    cbt: '1',
                    submenu: [{
                            name: 'Jenis CBT',
                            link: 'cbtjenis',
                            icon: 'fa fa-project-diagram'
                        },
                        {
                            name: 'Sesi',
                            link: 'cbtsesi',
                            icon: 'far fa-clock'
                        },
                        {
                            name: 'Ruang',
                            link: 'cbtruang',
                            icon: 'fa fa-door-open'
                        },
                        {
                            name: 'Atur Ruang/Sesi',
                            link: 'cbtsesisiswa',
                            icon: 'fa fa-user-clock'
                        },
                        {
                            name: 'Jadwal',
                            link: 'cbtjadwal',
                            icon: 'far fa-calendar-alt'
                        },
                        {
                            name: 'Alokasi Waktu',
                            link: 'cbtalokasi',
                            icon: 'fa fa-clock-o'
                        },
                        {
                            name: 'Pengawas',
                            link: 'cbtpengawas',
                            icon: 'fa fa-briefcase'
                        },
                        {
                            name: 'Nomor Peserta',
                            link: 'cbtnomorpeserta',
                            icon: 'far fa-id-card'
                        },
                        {
                            name: 'Bank Soal',
                            link: 'cbtbanksoal',
                            icon: 'far fa-folder-open'
                        },
                    ]
                },
                {
                    name: 'Pelaksanaan CBT',
                    icon: 'fas fa-graduation-cap',
                    cbt: '1',
                    submenu: [{
                            name: 'Cetak',
                            link: 'cbtcetak',
                            icon: 'fa fa-print'
                        },
                        {
                            name: 'Token',
                            link: 'cbttoken',
                            icon: 'fa fa-key'
                        },
                        {
                            name: 'Status CBT',
                            link: 'cbtstatus',
                            icon: 'fa fa-user-clock'
                        },
                        {
                            name: 'Hasil CBT',
                            link: 'cbtnilai',
                            icon: 'fa fa-file-alt'
                        },
                        {
                            name: 'Analisis Soal',
                            link: 'cbtanalisis',
                            icon: 'fa fa-chart-line'
                        },
                        {
                            name: 'Rekap Nilai',
                            link: 'cbtrekap',
                            icon: 'fas fa-file'
                        },
                    ]
                },
            ]
        },
        // RAPOR
        {
            header: '<b>RAPOR</b>',
            cbt: '0',
            menu: [{
                    name: 'Setting Rapor',
                    link: 'rapor',
                    icon: 'fas fa-book',
                    cbt: '1'
                },
                {
                    name: 'Cetak Rapor',
                    link: 'bukurapor',
                    icon: 'fas fa-print',
                    cbt: '1'
                },
            ]
        },
        // PENGATURAN
        {
            header: '<b>PENGATURAN</b>',
            cbt: '1',
            menu: [{
                    name: 'Profil Madrasah',
                    link: 'settings',
                    icon: 'fas fa-university',
                    cbt: '1'
                },
                {
                    name: 'Pengumuman',
                    link: 'pengumuman',
                    icon: 'fas fa-bullhorn',
                    cbt: '1'
                },
                {
                    name: 'Pengguna',
                    icon: 'fa fa-users-cog',
                    cbt: '1',
                    submenu: [{
                            name: 'Administrator',
                            link: 'useradmin',
                            icon: 'fas fa-user-secret'
                        },
                        {
                            name: 'Guru',
                            link: 'userguru',
                            icon: 'fas fa-user-tie'
                        },
                        {
                            name: 'Siswa',
                            link: 'usersiswa',
                            icon: 'fas fa-users'
                        },
                    ]
                },
                {
                    name: 'Database',
                    icon: 'fa fa-database',
                    cbt: '0',
                    submenu: [{
                            name: 'Manajemen Data',
                            link: 'dbclear',
                            icon: 'fas fa-cog'
                        },
                        {
                            name: 'Backup Data',
                            link: 'dbmanager',
                            icon: 'fas fa-archive'
                        },
                    ]
                },
            ]
        },
        // LOGOUT
        {
            name: '<strong class="text-danger">L O G O U T</strong>',
            icon: 'fas fa-sign-out-alt',
            cbt: '1'
        },
    ];

    (function buildMenu() {
        const isCbtMode = localStorage.getItem('garudaCBT.login') === '1';
        let html = '';

        menus.forEach(function(section) {
            if (isCbtMode && section.cbt === '0') return;

            if (section.header) {
                html += `<li class="nav-header">${section.header}</li>`;

                section.menu.forEach(function(menu) {
                    if (isCbtMode && menu.cbt === '0') return;

                    if (menu.submenu) {
                        var slugs = menu.submenu.map(function(s) {
                            return s.link.includes('/') ? s.link.split('/')[1] : s.link;
                        });
                        var isOpen = slugs.includes(pageact) || slugs.includes(page);

                        html += `<li class="nav-item has-treeview ${isOpen ? 'menu-open' : ''}">
            <a href="#" class="nav-link ${isOpen ? 'active' : ''}">
              <i class="nav-icon ${menu.icon}"></i>
              <p>${menu.name}<i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">`;

                        menu.submenu.forEach(function(sub) {
                            var isActive = page + '/' + pageact === sub.link || page === sub.link;
                            html += `<li class="nav-item">
              <a href="${base_url + sub.link}" class="nav-link ${isActive ? 'active' : ''}">
                <i class="${sub.icon} nav-icon"></i>
                <p>${sub.name}</p>
              </a>
            </li>`;
                        });

                        html += `</ul></li>`;
                    } else {
                        html += `<li class="nav-item">
            <a href="${base_url + menu.link}" class="nav-link ${page === menu.link ? 'active' : ''}">
              <i class="nav-icon ${menu.icon}"></i>
              <p>${menu.name}</p>
            </a>
          </li>`;
                    }
                });
            } else {
                // Logout
                html += `<hr/>
        <li class="nav-item">
          <a href="#" onclick="logout()" class="nav-link">
            <i class="${section.icon} nav-icon"></i>
            <p>${section.name}</p>
          </a>
        </li>`;
            }
        });

        $('#tree-menus').html(html);
    })();
</script>
