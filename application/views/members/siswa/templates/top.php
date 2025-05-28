<div class="info-box bg-transparent shadow-none">
    <img class="avatar" src="<?= base_url($siswa->foto) ?>" width="90" height="90">
    <div class="info-box-content">
        <h6 class="info-box-text text-white text-wrap"><b><?= $siswa->nama ?></b></h6>
        <span class="info-box-text text-white mb-1"><?= $siswa->nis ?></span>
        <span class="info-box-text text-white text-bold mb-1"><?= $siswa->nama_kelas ?></span>
    </div>
</div>

<script>
    $(`.avatar`).each(function() {
        $(this).on("error", function() {
            var src = $(this).attr('src').replace('profiles', 'foto_siswa');
            $(this).attr("src", src);
            $(this).on("error", function() {
                $(this).attr("src", base_url + 'assets/img/siswa.png');
            });

        });
    });
</script>