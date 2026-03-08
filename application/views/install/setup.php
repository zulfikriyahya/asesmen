<div class="install-wrap">
    <div class="install-split">

        <!-- Brand -->
        <div class="install-brand">
            <img src="<?= base_url() ?>/assets/img/garuda_white.svg" alt="Logo">
            <h1><b>G</b>abungan <b>A</b>plikasi <b>R</b>apor,<br><b>U</b>jian dan e-learning</h1>
            <p>Selamat datang di installer ASESMEN MADRASAH CBT.<br>Isi konfigurasi database untuk memulai.</p>
        </div>

        <!-- Card -->
        <div class="install-card">
            <div class="install-card-header">
                <h3>Konfigurasi Database</h3>
            </div>
            <div class="install-card-body">

                <?= form_open('create', ['id' => 'create']) ?>

                <div class="install-form-grid">
                    <div class="install-field" style="grid-column: 1 / -1">
                        <label>Host Name</label>
                        <input type="text" name="hostname" placeholder="localhost" required>
                    </div>
                    <div class="install-field">
                        <label>Host Username</label>
                        <input type="text" name="hostuser" placeholder="root" required>
                    </div>
                    <div class="install-field">
                        <label>Host Password</label>
                        <input type="text" name="hostpass" placeholder="Kosongkan jika tidak ada">
                        <small>Kosongkan jika tidak menggunakan password.</small>
                    </div>
                    <div class="install-field" style="grid-column: 1 / -1">
                        <label>Nama Database</label>
                        <input type="text" name="database" placeholder="nama_database" required>
                        <small>Jangan gunakan spasi.</small>
                    </div>
                </div>

                <div class="install-actions">
                    <button type="submit" id="install-db" class="btn-install btn-install-primary">
                        <i class="fas fa-database"></i> Install / Update
                    </button>
                </div>

                <?= form_close() ?>

            </div>
        </div>

    </div>

    <!-- Info Panel -->
    <div style="width:100%; max-width:960px; margin-top:1.5rem;">
        <div class="install-info">
            <h5>A. Update Aplikasi</h5>
            <ul>
                <li>Isi kolom di atas sesuai pengaturan localhost/MySQL dan nama database yang sudah ada, lalu klik <b>Install / Update</b>.</li>
            </ul>

            <h5>B. Install Otomatis</h5>
            <ul>
                <li>Isi kolom di atas, sesuaikan dengan pengaturan localhost/MySQL, isi nama database, lalu klik <b>Install / Update</b>.</li>
                <li>Lanjutkan proses instalasi di halaman selanjutnya.</li>
            </ul>

            <h5>C. Install Manual</h5>
            <ol>
                <li>Buat database baru di <b>phpMyAdmin</b>.</li>
                <li>IMPORT file database di dalam folder <code>/assets/app/db/master.sql</code>.</li>
                <li>Buka file <b>database.php</b> di dalam folder <code>/application/config/</code>.</li>
                <li>
                    Ganti baris berikut:
                    <pre>'hostname' => '',
'username' => '',
'password' => '',
'database' => '',</pre>
                    Menjadi:
                    <pre>'hostname' => 'localhost',
'username' => '',        // laragon: root | xampp: kosong
'password' => '',        // default: kosong
'database' => 'nama_database',</pre>
                </li>
                <li>Refresh halaman ini.</li>
            </ol>
        </div>
    </div>

</div>

<script>
    $(function() {
        console.log('<?= $res ?>', '<?= $msg ?>');

        $('#create').on('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            swal.fire({
                title: 'Checking database',
                text: 'Silahkan tunggu....',
                allowEscapeKey: false,
                allowOutsideClick: false,
                onOpen: () => swal.showLoading()
            });

            $.ajax({
                url: base_url + 'install/checkdatabase',
                method: 'POST',
                data: $(this).serialize() + '&page=0',
                success: function() {
                    swal.fire({
                        title: 'Sukses',
                        html: 'Database berhasil diinstall',
                        icon: 'success',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(r => {
                        if (r.value) window.location.reload();
                    });
                },
                error: function(xhr) {
                    swal.fire({
                        title: 'ERROR',
                        html: 'Gagal inisialisasi database',
                        icon: 'error'
                    });
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>
