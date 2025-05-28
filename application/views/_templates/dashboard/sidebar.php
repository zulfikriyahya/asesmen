<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-orange my-shadow">
    <!-- Brand Logo -->
    <a href="<?= base_url(); ?>" class="brand-link bg-dark">
        <?php $logo_app = $setting->logo_kiri == null ? base_url() . 'assets/img/favicon.png' : base_url() . $setting->logo_kiri; ?>
        <img src="<?= $logo_app ?>" alt="App Logo" class="brand-image"
            style="opacity: .9">
        <span class="brand-text font-weight-light">
            <h5><b><?= $setting->nama_aplikasi ?></b></h5>
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-3 mb-0">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent"
                id="tree-menus" data-widget="treeview" role="menu" data-accordion="false">
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<script>
    const page = '<?= $this->uri->segment(1) ?>';
    const pageact = '<?= $this->uri->segment(2); ?>';
    const menus = [
        // DASHBOARD
        {
            'header': "<marquee><center>Selamat datang di <strong>ZEDAPPS SCHOOL.</strong></marquee>",
            // 'cbt': '1',
            'menu': []
        },
        // MASTER
        {
            'header': '<b>MASTER</b>',
            'cbt': '1',
            'menu': [
                // MASTER DATA
                {
                    'name': 'Master Data',
                    'icon': 'fas fa-server',
                    'cbt': '1',
                    'submenu': [
                        // TAHUN PELAJARAN
                        {
                            'name': "Tahun Pelajaran",
                            'link': "datatahun",
                            'icon': 'far fa-calendar-check'
                        },
                        // MATA PELAJARAN
                        {
                            'name': "Mata Pelajaran",
                            'link': "datamapel",
                            'icon': 'fa fa-book'
                        },
                        // JURUSAN
                        {
                            'name': "Jurusan",
                            'link': "datajurusan",
                            'icon': 'fa fa-flask'
                        },
                        // KELAS / ROMBEL
                        {
                            'name': "Kelas / Rombel",
                            'link': "datakelas",
                            'icon': 'fa fa-school'
                        },
                        // EKSTRAKURIKULER
                        {
                            'name': "Ekstrakurikuler",
                            'link': "dataekstra",
                            'icon': 'fa fa-chess'
                        },
                        // GURU
                        {
                            'name': "Guru",
                            'link': "dataguru",
                            'icon': 'fa fa-chalkboard-teacher'
                        },
                        // SISWA
                        {
                            'name': "Siswa",
                            'link': "datasiswa",
                            'icon': 'fa fa-users'
                        },
                        // ALUMNI
                        {
                            'name': 'Alumni',
                            'link': 'dataalumni',
                            'icon': 'fa fa-graduation-cap'
                        },
                    ]
                },
            ]
        },
        // E-LEARNING
        {
            'header': '<b>E-LEARNING</b>',
            'cbt': '1',
            'menu': [
                // DATA ELEARNING
                {
                    'name': 'Data E-Learning',
                    'icon': 'fas fa-chalkboard',
                    'cbt': '1',
                    'submenu': [
                        // MATERI
                        {
                            'name': "Materi",
                            'link': "kelasmateri/materi",
                            'icon': 'fa fa-pencil-ruler'
                        },
                        // TUGAS
                        {
                            'name': "Tugas",
                            'link': "kelasmateri/tugas",
                            'icon': 'fa fa-drafting-compass'
                        },
                        // JADWAL PELAJARAN
                        {
                            'name': "Jadwal Pelajaran",
                            'link': "kelasjadwal",
                            'icon': 'fa fa-calendar-alt'
                        },
                        // JADWAL MATERI / TUGAS
                        {
                            'name': "Jadwal Materi / Tugas",
                            'link': "kelasmaterijadwal",
                            'icon': 'fa fa-calendar-check-o'
                        },
                    ]
                },
                // PELAKSANAAN E-LEARNING
                {
                    'name': 'Pelaksanaan E-Learning',
                    'icon': 'fas fa-microscope',
                    'cbt': '1',
                    'submenu': [
                        // KEHADIRAN HARIAN
                        {
                            'name': 'Kehadiran Harian',
                            'link': "kelasabsensiharian",
                            'icon': 'fa fa-user-check'
                        },
                        // KEHADIRAN BULANAN
                        {
                            'name': 'Kehadiran Bulanan',
                            'link': "kelasabsensibulanan",
                            'icon': 'fa fa-tasks'
                        },
                        // NILAI HARIAN
                        {
                            'name': 'Nilai Harian',
                            'link': "kelasstatus",
                            'icon': 'far fa-clipboard'
                        },
                        // REKAP NILAI
                        {
                            'name': 'Rekap Nilai',
                            'link': "kelasnilai",
                            'icon': 'fa fa-file'
                        },
                    ]
                },
            ]
        },
        // CBT
        {
            'header': '<b>CBT</b>',
            'cbt': '1',
            'menu': [
                // DATA CBT
                {
                    'name': 'Data CBT',
                    'icon': 'fa fa-user-graduate',
                    'cbt': '1',
                    'submenu': [
                        // JENIS CBT
                        {
                            'name': "Jenis CBT",
                            'link': "cbtjenis",
                            'icon': 'fa fa-project-diagram'
                        },
                        // SESI
                        {
                            'name': "Sesi",
                            'link': "cbtsesi",
                            'icon': 'far fa-clock'
                        },
                        // RUANG
                        {
                            'name': "Ruang",
                            'link': "cbtruang",
                            'icon': 'fa fa-door-open'
                        },
                        // ATUR RUANG / SESI
                        {
                            'name': "Atur Ruang / Sesi",
                            'link': "cbtsesisiswa",
                            'icon': 'fa fa-user-clock'
                        },
                        // JADWAL
                        {
                            'name': "Jadwal",
                            'link': "cbtjadwal",
                            'icon': 'far fa-calendar-alt'
                        },
                        // ALOKASI WAKTU
                        {
                            'name': "Alokasi Waktu",
                            'link': "cbtalokasi",
                            'icon': 'fa fa-clock-o'
                        },
                        // PENGAWAS
                        {
                            'name': "Pengawas",
                            'link': "cbtpengawas",
                            'icon': 'fa fa-briefcase'
                        },
                        // NOMOR PESERTA
                        {
                            'name': "Nomor Peserta",
                            'link': "cbtnomorpeserta",
                            'icon': 'far fa-id-card'
                        },
                        // BANK SOAL
                        {
                            'name': "Bank Soal",
                            'link': "cbtbanksoal",
                            'icon': 'far fa-folder-open'
                        },
                    ]
                },
                // PELAKSANAAN CBT
                {
                    'name': 'Pelaksanaan CBT',
                    'icon': 'fas fa-graduation-cap',
                    'cbt': '1',
                    'submenu': [
                        // CETAK
                        {
                            'name': 'Cetak',
                            'link': "cbtcetak",
                            'icon': 'fa fa-print'
                        },
                        // TOKEN
                        {
                            'name': "Token",
                            'link': "cbttoken",
                            'icon': 'fa fa-key'
                        },
                        // STATUS CBT
                        {
                            'name': 'Status CBT',
                            'link': "cbtstatus",
                            'icon': 'fa fa-user-clock'
                        },
                        {
                            'name': 'Hasil CBT',
                            'link': "cbtnilai",
                            'icon': 'fa fa-file-alt'
                        },
                        {
                            'name': 'Analisis Soal',
                            'link': "cbtanalisis",
                            'icon': 'fa fa-chart-line'
                        },
                        {
                            'name': 'Rekap Nilai',
                            'link': "cbtrekap",
                            'icon': 'fas fa-file'
                        },
                    ]
                },
            ]
        },
        // RAPOR
        {
            'header': '<b>RAPOR</b>',
            'cbt': '1',
            'menu': [
                // SETTING RAPOR
                {
                    'name': 'Setting Rapor',
                    'link': 'rapor',
                    'icon': 'fas fa-book',
                    'cbt': '1'
                },
                // CETAK RAPOR
                {
                    'name': 'Cetak Rapor',
                    'link': 'bukurapor',
                    'icon': 'fas fa-print',
                    'cbt': '1'
                },
            ]
        },
        // PENGATURAN
        {
            'header': '<b>PENGATURAN</b>',
            'cbt': '1',
            'menu': [
                // PROFIL MADRASAH
                {
                    'name': 'Profil Madrasah',
                    'link': 'settings',
                    'icon': 'fas fa-university',
                    'cbt': '1',
                },
                // PENGUMUMAN
                {
                    'name': 'Pengumuman',
                    'link': 'pengumuman',
                    'icon': 'fas fa-bullhorn',
                    'cbt': '1'
                },
                // PENGGUNA
                {
                    'name': 'Pengguna',
                    'icon': 'fa fa-users-cog',
                    'cbt': '1',
                    'submenu': [
                        // ADMINISTRATOR
                        {
                            'name': 'Administrator',
                            'link': "useradmin",
                            'icon': 'fas fa-user-secret'
                        },
                        // GURU
                        {
                            'name': 'Guru',
                            'link': "userguru",
                            'icon': 'fas fa-user-tie'
                        },
                        // SISWA
                        {
                            'name': 'Siswa',
                            'link': "usersiswa",
                            'icon': 'fas fa-users'
                        },
                    ]
                },
                // DATABASE
                {
                    'name': 'Database',
                    'icon': 'fa fa-database',
                    'cbt': '1',
                    'submenu': [
                        // MANAJEMEN DATA
                        {
                            'name': 'Manajemen Data',
                            'link': "dbclear",
                            'icon': 'fas fa-cog'
                        },
                        // BACKUP DATA
                        {
                            'name': 'Backup Data',
                            'link': "dbmanager",
                            'icon': 'fas fa-archive'
                        },
                    ]
                },

            ]
        },
        // LOGOUT
        {
            'name': '<strong class="text-danger">L O G O U T</strong>',
            'icon': 'fas fa-sign-out-alt',
            'cbt': '1'
        },
    ];

    const isLogin = localStorage.getItem('garudaCBT.login')
    const isCbtMode = isLogin ? isLogin === '1' : false
    let htmlMenu = '';
    menus.forEach(function(header) {
        if (isCbtMode && header.cbt === '0') {
            return
        }
        if (header.header) {
            htmlMenu += `<li class="nav-header">${header.header}</li>`;
            header.menu.forEach(function(menu) {
                if (isCbtMode && menu.cbt === '0') {
                    return
                }
                if (menu.submenu) {
                    var subs = menu.submenu.map(function(item) {
                        if (item['link'].includes('/')) {
                            return item['link'].split('/')[1]
                        } else return item['link'];
                    });
                    htmlMenu += `<li class="nav-item has-treeview ${subs.includes(pageact) || subs.includes(page) ? "menu-open" : ""}">
                <a href="#" class="nav-link ${subs.includes(pageact) || subs.includes(page) ? "active" : ""}">
                    <i class="nav-icon ${menu.icon}"></i>
                    <p>${menu.name}<i class="fas fa-angle-left right"></i></p>
                </a><ul class="nav nav-treeview">`;
                    menu.submenu.forEach(function(sub) {
                        htmlMenu += `<li class="nav-item">
                        <a href="${base_url + sub.link}"
                           class="nav-link ${page+'/'+pageact === sub.link || page === sub.link ? "active" : ""}">
                            <i class="${sub.icon} nav-icon"></i>
                            <p>${sub.name}</p>
                        </a>
                    </li>`;
                    })
                    htmlMenu += `</ul></li>`;
                } else {
                    htmlMenu += `<li class="nav-item"><a href="${base_url + menu.link}"
                   class="nav-link ${page === menu.link ? "active" : ""}">
                    <i class="nav-icon ${menu.icon}"></i>
                    <p>${menu.name}</p>
                </a></li>`
                }
            })
        } else {
            htmlMenu += `<hr /><li class="nav-item">
                <a href="#" onclick="logout()" class="nav-link">
                    <i class="${header.icon} nav-icon"></i>
                    <p>${header.name}</p>
                </a>
            </li>`;
        }
    })
    $('#tree-menus').html(htmlMenu)
</script>