<?php
$readAdminNama  = $data->nama_admin === '' ? '' : 'readonly';
$readAdminUser  = $data->user_admin  === '' ? '' : 'readonly';
$readAdminPass  = $data->pass_admin  === '' ? '' : 'readonly';
$readAppNama    = $data->aplikasi    === '' ? '' : 'readonly';
$readSklNama    = $data->sekolah     === '' ? '' : 'readonly';
$readSklKepsek  = $data->kepsek      === '' ? '' : 'readonly';
$readSklJenjang = $data->jenjang     === '' ? '' : 'readonly';
$readSklSatuan  = $data->satuan      === '' ? '' : 'readonly';
$readSklAlamat  = $data->alamat      === '' ? '' : 'readonly';
$readSklDesa    = $data->desa        === '' ? '' : 'readonly';
$readSklKec     = $data->kec         === '' ? '' : 'readonly';
$readSklKota    = $data->kota        === '' ? '' : 'readonly';
$readSklProv    = $data->prov        === '' ? '' : 'readonly';

$cp = $data->current_page;
?>

<div class="install-wrap">
    <div class="install-split">

        <!-- Brand -->
        <div class="install-brand">
            <img src="<?= base_url() ?>assets/img/favicon.png" alt="Logo">
            <h1><b>ASESMEN</b> MADRASAH</h1>
            <p>Ikuti langkah instalasi di sebelah kanan untuk menyelesaikan pengaturan aplikasi.</p>
        </div>

        <!-- Card -->
        <div class="install-card" style="position:relative;">
            <div class="install-card-header">
                <h3>Langkah Instalasi</h3>
            </div>
            <div class="install-card-body">

                <!-- Step Indicator -->
                <div class="step-indicator">
                    <?php
                    $steps = ['Database', 'Administrator', 'Instansi', 'Selesai'];
                    foreach ($steps as $i => $label):
                        $n = $i + 1;
                        $cls = '';
                        if ($n < $cp)       $cls = 'completed';
                        elseif ($n === $cp) $cls = 'current';
                    ?>
                        <div class="step-item <?= $cls ?>">
                            <div class="step-dot">
                                <?= $n < $cp ? '<i class="fas fa-check" style="font-size:.6rem"></i>' : $n ?>
                            </div>
                            <span class="step-label"><?= $label ?></span>
                        </div>
                    <?php endforeach ?>
                </div>

                <?= form_open('', ['id' => 'installapp']) ?>

                <!-- Step 1: Database -->
                <div class="step-content <?= $cp == 1 ? 'active' : '' ?>" data-step="1">
                    <div class="install-form-grid">
                        <div class="install-field">
                            <label>Hostname</label>
                            <input type="text" name="hostname" value="<?= $data->hostname ?>" placeholder="localhost" readonly>
                        </div>
                        <div class="install-field">
                            <label>Host Username</label>
                            <input type="text" name="hostuser" value="<?= $data->username ?>" placeholder="Host Username" readonly>
                        </div>
                        <div class="install-field">
                            <label>Host Password</label>
                            <input type="text" name="hostpass" value="<?= $data->password ?>" placeholder="Kosongkan jika tidak ada" readonly>
                            <small>Kosongkan jika tidak menggunakan password.</small>
                        </div>
                        <div class="install-field">
                            <label>Database Name</label>
                            <input type="text" name="database" value="<?= $data->database ?>" placeholder="Database Name" readonly>
                            <small>Jangan gunakan spasi.</small>
                        </div>
                    </div>
                    <hr class="install-divider">
                    <div class="install-actions">
                        <button type="button" id="next1" class="btn-install btn-install-primary">
                            Selanjutnya <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Administrator -->
                <div class="step-content <?= $cp == 2 ? 'active' : '' ?>" data-step="2">
                    <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9a9fa8;margin-bottom:1rem;">
                        Registrasi Administrator
                    </p>
                    <div class="install-form-grid">
                        <div class="install-field">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" id="input-nama-adm"
                                value="<?= $data->nama_admin ?>" class="adm" required <?= $readAdminNama ?>>
                        </div>
                        <div class="install-field">
                            <label>Username</label>
                            <input type="text" name="username" id="input-user"
                                class="adm" value="<?= $data->user_admin ?>" required <?= $readAdminUser ?>>
                        </div>
                        <div class="install-field">
                            <label>Password</label>
                            <input type="text" name="password" id="input-pass"
                                class="adm" required <?= $readAdminPass ?>>
                            <small>Minimal 6 karakter.</small>
                        </div>
                        <div class="install-field">
                            <label>Ulangi Password</label>
                            <input type="text" id="input-rep-pass" class="adm" required <?= $readAdminPass ?>>
                        </div>
                    </div>
                    <hr class="install-divider">
                    <div class="install-actions">
                        <button type="button" id="prev2" class="btn-install btn-install-back mr-auto">
                            <i class="fas fa-arrow-left"></i> Sebelumnya
                        </button>
                        <button type="button" id="next2" class="btn-install btn-install-primary">
                            Selanjutnya <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Instansi -->
                <div class="step-content <?= $cp == 3 ? 'active' : '' ?>" data-step="3">
                    <div class="install-form-grid">
                        <div class="install-field">
                            <label>Nama Aplikasi</label>
                            <input type="text" id="input-nama-app" name="nama_aplikasi"
                                class="app" value="<?= $data->aplikasi ?>" required <?= $readAppNama ?>>
                        </div>
                        <div class="install-field">
                            <label>Nama Sekolah</label>
                            <input type="text" id="input-nama-skl" name="nama_sekolah"
                                class="app" value="<?= $data->sekolah ?>" required <?= $readSklNama ?>>
                        </div>
                        <div class="install-field">
                            <label>Kepala Sekolah</label>
                            <input type="text" id="input-nama-kepsek" name="kepsek"
                                class="app" value="<?= $data->kepsek ?>" required <?= $readSklKepsek ?>>
                        </div>
                        <div class="install-field" style="grid-column: 1 / -1">
                            <label>Alamat</label>
                            <input type="text" id="input-alamat" name="alamat"
                                class="app" value="<?= $data->alamat ?>" required <?= $readSklAlamat ?>>
                        </div>
                        <div class="install-field">
                            <label>Desa / Kelurahan</label>
                            <input type="text" id="input-desa" name="desa"
                                class="app" value="<?= $data->desa ?>" required <?= $readSklDesa ?>>
                        </div>
                        <div class="install-field">
                            <label>Kecamatan</label>
                            <input type="text" id="input-kec" name="kec"
                                class="app" value="<?= $data->kec ?>" required <?= $readSklKec ?>>
                        </div>
                        <div class="install-field">
                            <label>Kabupaten / Kota</label>
                            <input type="text" id="input-kota" name="kota"
                                class="app" value="<?= $data->kota ?>" required <?= $readSklKota ?>>
                        </div>
                        <div class="install-field">
                            <label>Provinsi</label>
                            <input type="text" id="input-prov" name="prov"
                                class="app" value="<?= $data->prov ?>" <?= $readSklProv ?>>
                        </div>
                        <div class="install-field">
                            <label>Jenjang</label>
                            <select id="input-jenjang" name="jenjang" class="app" required <?= $readSklJenjang ?>>
                                <option value="" disabled selected>Pilih Jenjang</option>
                                <option value="1">SD / MI</option>
                                <option value="2">SMP / MTS</option>
                                <option value="3">SMA / MA / SMK</option>
                            </select>
                        </div>
                        <div class="install-field">
                            <label>Satuan Pendidikan</label>
                            <select id="input-satuan" name="satuan" class="app" required <?= $readSklSatuan ?>>
                                <option value="" disabled>Satuan Pendidikan</option>
                            </select>
                        </div>
                    </div>
                    <hr class="install-divider">
                    <div class="install-actions">
                        <button type="button" id="prev3" class="btn-install btn-install-back mr-auto">
                            <i class="fas fa-arrow-left"></i> Sebelumnya
                        </button>
                        <button type="button" id="next3" class="btn-install btn-install-primary">
                            Selanjutnya <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Konfirmasi -->
                <div class="step-content <?= $cp == 4 ? 'active' : '' ?>" data-step="4">
                    <p style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#9a9fa8;margin-bottom:1rem;">
                        Konfirmasi Data
                    </p>
                    <table class="summary-table">
                        <tbody>
                            <tr>
                                <td>Database</td>
                                <td id="text-db">—</td>
                            </tr>
                            <tr>
                                <td>Nama Aplikasi</td>
                                <td id="text-app">—</td>
                            </tr>
                            <tr>
                                <td>Administrator</td>
                                <td id="text-adm">—</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td id="text-usr">—</td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td id="text-pass">—</td>
                            </tr>
                            <tr>
                                <td>Nama Sekolah</td>
                                <td id="text-skl">—</td>
                            </tr>
                            <tr>
                                <td>Kepala Sekolah</td>
                                <td id="text-kep">—</td>
                            </tr>
                            <tr>
                                <td>Jenjang</td>
                                <td id="text-jen">—</td>
                            </tr>
                            <tr>
                                <td>Satuan Pendidikan</td>
                                <td id="text-satuan">—</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td id="text-alm">—</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr class="install-divider">
                    <div class="install-actions">
                        <button type="button" id="prev4" class="btn-install btn-install-back mr-auto">
                            <i class="fas fa-arrow-left"></i> Sebelumnya
                        </button>
                        <button type="button" id="next4" class="btn-install btn-install-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>

                <?= form_close() ?>

            </div>

            <!-- Loading overlay -->
            <div class="overlay d-none loading">
                <div class="spinner-ring"></div>
            </div>
        </div>

    </div>
</div>

<script>
    var dataInstall = <?= json_encode($data) ?>;
    var currentPage = dataInstall.current_page;
    var satuanPend = [
        [],
        ['SD', 'MI'],
        ['SMP', 'MTS'],
        ['SMA', 'MA', 'SMK']
    ];

    $(function() {
        $('#next1').click(function() {
            currentPage++;
            switchPage(currentPage, false);
        });

        $('#next2').click(function() {
            var adminAda = dataInstall.nama_admin !== '' && dataInstall.user_admin !== '';
            if (adminAda) {
                currentPage++;
                switchPage(currentPage, false);
                return;
            }

            var hasInput = true;
            $('.adm').each(function() {
                if (!$(this).val()) {
                    hasInput = false;
                    return false;
                }
            });

            if (!hasInput) {
                return Swal.fire({
                    title: 'ERROR',
                    text: 'Semua harus diisi, jangan ada yang kosong',
                    icon: 'error'
                });
            }
            if ($('#input-pass').val() !== $('#input-rep-pass').val()) {
                return Swal.fire({
                    title: 'ERROR',
                    text: 'Password tidak sama',
                    icon: 'error'
                });
            }
            if ($('#input-pass').val().length < 6) {
                return Swal.fire({
                    title: 'ERROR',
                    text: 'Password kurang dari 6 karakter',
                    icon: 'error'
                });
            }
            markStep(2);
            currentPage++;
            switchPage(currentPage, false);
        });

        $('#prev2').click(function() {
            currentPage--;
            switchPage(currentPage, true);
        });

        $('#next3').click(function() {
            var settingAda = dataInstall.aplikasi && dataInstall.sekolah && dataInstall.kepsek &&
                dataInstall.jenjang && dataInstall.alamat && dataInstall.desa &&
                dataInstall.kec && dataInstall.kota;
            if (settingAda) {
                currentPage++;
                switchPage(currentPage, false);
                return;
            }

            var hasInput = true;
            $('.app').each(function() {
                if (!$(this).val()) {
                    hasInput = false;
                    return false;
                }
            });

            if (!hasInput) {
                return Swal.fire({
                    title: 'ERROR',
                    text: 'Isi semua pilihan yang bertanda bintang (*)',
                    icon: 'error'
                });
            }
            markStep(3);
            currentPage++;
            switchPage(currentPage, false);
        });

        $('#prev3').click(function() {
            currentPage--;
            switchPage(currentPage, true);
        });
        $('#prev4').click(function() {
            currentPage--;
            switchPage(currentPage, true);
        });
        $('#next4').click(function() {
            $('#installapp').submit();
        });

        $('#input-jenjang').on('change', function() {
            var opts = '<option value="" disabled>Satuan Pendidikan</option>';
            satuanPend[$(this).val()].forEach(function(v, i) {
                opts += '<option value="' + (i + 1) + '">' + v + '</option>';
            });
            $('#input-satuan').html(opts);
        });

        $('#installapp').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $('.loading').removeClass('d-none');

            swal.fire({
                title: 'Menyimpan instalasi',
                text: 'Silahkan tunggu....',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });

            $.ajax({
                url: base_url + 'install/createapp',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('.loading').addClass('d-none');
                    var ok = response.admin && response.insert;
                    swal.fire({
                        title: ok ? 'Sukses' : 'Gagal!',
                        html: ok ? 'Aplikasi berhasil diinstall' : 'Gagal menyimpan data aplikasi',
                        icon: ok ? 'success' : 'error',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(r => {
                        if (r.value && ok) window.location.href = base_url;
                    });
                },
                error: function(xhr) {
                    $('.loading').addClass('d-none');
                    swal.fire({
                        title: 'ERROR',
                        html: 'Gagal menyimpan data',
                        icon: 'error'
                    });
                    console.log(xhr.responseText);
                }
            });
        });
    });

    function markStep(n) {
        $('.step-item:nth-child(' + n + ')').addClass('completed').removeClass('current');
    }

    function switchPage(page, back) {
        // Update indicator
        $('.step-item').each(function(i) {
            var n = i + 1;
            $(this).removeClass('current completed');
            if (n < page) $(this).addClass('completed');
            if (n === page) $(this).addClass('current');
        });

        if (back) $('.step-item:nth-child(' + (page + 1) + ')').removeClass('completed');

        // Switch content
        $('.step-content').removeClass('active');
        $('.step-content[data-step="' + page + '"]').addClass('active');

        if (page === 4) {
            var alm = [$('#input-alamat').val(), $('#input-desa').val(),
                    $('#input-kec').val(), $('#input-kota').val(), $('#input-prov').val()
                ]
                .filter(Boolean).join(', ');

            $('#text-db').text($('#input-nama-db').val() || dataInstall.database);
            $('#text-app').text($('#input-nama-app').val());
            $('#text-adm').text($('#input-nama-adm').val());
            $('#text-usr').text($('#input-user').val());
            $('#text-pass').text($('#input-pass').val() || '••••••');
            $('#text-skl').text($('#input-nama-skl').val());
            $('#text-kep').text($('#input-nama-kepsek').val());
            $('#text-jen').text($('#input-jenjang option:selected').text());
            $('#text-satuan').text($('#input-satuan option:selected').text());
            $('#text-alm').text(alm);
        }
    }
</script>
