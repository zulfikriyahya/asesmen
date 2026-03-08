<div class="content-wrapper">

    <!-- ── INFO BOX ── -->
    <section class="content-header p-0">
        <div class="container-fluid px-0 pt-4 pb-0">
            <div class="row mx-2">
                <?php foreach ($info_box as $info): ?>
                    <div class="col-md-2 col-4 mb-2">
                        <a href="<?= base_url() . $info->url ?>" class="info-box-link">
                            <div class="glass-stat-box">
                                <div class="glass-stat-icon">
                                    <i class="fa fa-<?= $info->icon ?>"></i>
                                </div>
                                <div class="glass-stat-body">
                                    <span class="glass-stat-label"><?= $info->title ?></span>
                                    <span class="glass-stat-value"><?= $info->total ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <!-- ── MAIN CONTENT ── -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <!-- LEFT COLUMN -->
                <div class="col-md-8">

                    <!-- PENILAIAN -->
                    <div class="glass-card mb-3">
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">PENILAIAN</h6>
                        </div>
                        <div class="glass-card-body">

                            <div class="row">
                                <?php foreach ($ujian_box as $info): ?>
                                    <div class="col-md-4 col-6 mb-2">
                                        <a href="<?= base_url() . $info->url ?>" class="info-box-link">
                                            <div class="glass-info-box">
                                                <span class="glass-info-label"><?= $info->title ?></span>
                                                <span class="glass-info-value"><?= $info->total ?></span>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach ?>

                                <div class="col-md-8 col-12 mb-2">
                                    <a href="<?= base_url('cbttoken') ?>" class="info-box-link">
                                        <div class="glass-info-box glass-info-box--token">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="glass-info-label">Token</span>
                                                <small class="d-none text-muted" id="interval">-- : -- : --</small>
                                            </div>
                                            <span class="glass-info-value" id="token-view">
                                                <?= $token->token ?? '- - - - - -' ?>
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <hr class="glass-divider">

                            <!-- Jadwal penilaian hari ini -->
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h6 class="glass-section-title">PENILAIAN HARI INI</h6>
                                </div>
                                <div class="col-12 table-responsive">
                                    <?php
                                    $no = 1;
                                    $jadwal_ujian = $jadwals_ujian[date('Y-m-d')] ?? [];
                                    if (count($jadwal_ujian) > 0): ?>

                                        <table id="tbl-penilaian" class="w-100 glass-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center align-middle">No</th>
                                                    <th class="text-center align-middle">Ruang</th>
                                                    <th class="text-center align-middle">Sesi</th>
                                                    <th class="text-center align-middle">Mata Pelajaran</th>
                                                    <th class="text-center align-middle">Pengawas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ruangs as $ruang => $sesis):
                                                    foreach ($sesis as $sesi):
                                                        foreach ($jadwal_ujian as $jadwal):

                                                            $id_guru = isset($pengawas[$jadwal[0]->id_jadwal][$ruang][$sesi->sesi_id])
                                                                ? explode(',', $pengawas[$jadwal[0]->id_jadwal][$ruang][$sesi->sesi_id]->id_guru ?? '')
                                                                : [];

                                                            $badge_kelas   = '';
                                                            $total_peserta = 0;

                                                            foreach ($jadwal as $jdw) {
                                                                foreach ($jdw->bank_kelas as $bank_kelas) {
                                                                    foreach ($jdw->peserta as $peserta) {
                                                                        $cnt = isset($peserta[$ruang][$sesi->sesi_id])
                                                                            ? count($peserta[$ruang][$sesi->sesi_id]) : 0;
                                                                        if ($bank_kelas['kelas_id'] != null && $cnt > 0) {
                                                                            $total_peserta += $cnt;
                                                                            $nama_kls = $kelases[$bank_kelas['kelas_id']] ?? '- -';
                                                                            $badge_kelas .= '<span class="badge-pill-glass">' . $nama_kls . ' ' . $cnt . ' siswa</span>';
                                                                        }
                                                                    }
                                                                }
                                                            }

                                                            if ($total_peserta > 0): ?>
                                                                <tr>
                                                                    <td class="text-center align-middle"><?= $no ?></td>
                                                                    <td class="text-center align-middle"><?= $sesi->nama_ruang ?></td>
                                                                    <td class="text-center align-middle"><?= $sesi->nama_sesi ?></td>
                                                                    <td class="text-center align-middle"><?= $jadwal[0]->kode ?></td>
                                                                    <td class="align-middle crop-text-table">
                                                                        <?php foreach ($id_guru as $ig):
                                                                            echo isset($gurus[$ig]) ? '<p class="p-0 m-0">' . $gurus[$ig] . '</p>' : '';
                                                                        endforeach ?>
                                                                    </td>
                                                                </tr>
                                                <?php endif;
                                                        endforeach;
                                                    endforeach;
                                                    $no++;
                                                endforeach ?>
                                            </tbody>
                                        </table>

                                    <?php else: ?>
                                        <div class="glass-empty-state">Tidak ada jadwal penilaian hari ini.</div>
                                    <?php endif ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- INFO / PENGUMUMAN -->
                    <div class="glass-card mb-3">
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">INFO / PENGUMUMAN</h6>
                        </div>
                        <div class="glass-card-body">
                            <div class="konten-pengumuman">
                                <div id="pengumuman"></div>
                                <p id="loading-post" class="text-center d-none">
                                    <br><i class="fa fa-spin fa-circle-o-notch"></i> Loading…
                                </p>
                                <div id="loadmore-post" onclick="getPosts()"
                                    class="text-center mt-4 loadmore d-none">
                                    <button class="btn-glass">Muat Pengumuman lainnya…</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!-- /LEFT COLUMN -->

                <!-- RIGHT COLUMN -->
                <div class="col-md-4">

                    <!-- JADWAL HARI INI -->
                    <div class="glass-card mb-3">
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">JADWAL HARI INI</h6>
                            <div class="glass-card-tools">
                                <a href="<?= base_url('kelasjadwal') ?>" class="btn-glass btn-xs">
                                    <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="glass-card-body p-0">

                            <!-- Tab pills -->
                            <div class="glass-tab-header">
                                <ul class="nav nav-pills glass-nav-pills p-2 flex-wrap">
                                    <?php $no = 1;
                                    foreach ($kelases as $ky => $kelas):
                                        $active = $no == 1 ? 'active' : ''; ?>
                                        <li class="nav-item">
                                            <a class="nav-link glass-nav-link <?= $active ?>"
                                                href="#tab_<?= $ky ?>" data-toggle="tab">
                                                <?= $kelas ?>
                                            </a>
                                        </li>
                                    <?php $no++;
                                    endforeach ?>
                                </ul>
                            </div>

                            <?php if (count($jadwals) > 0 && count($kbms) > 0): ?>
                                <div class="tab-content">
                                    <?php $no = 1;
                                    foreach ($kelases as $ky => $kelas):
                                        $arrIst = [];
                                        $arrDur = [];
                                        if (isset($kbms[$ky]->istirahat)) {
                                            foreach ($kbms[$ky]->istirahat as $ist) {
                                                $arrIst[]            = $ist['ist'];
                                                $arrDur[$ist['ist']] = $ist['dur'];
                                            }
                                        }
                                        $active = $no == 1 ? 'active' : ''; ?>

                                        <div class="tab-pane <?= $active ?>" id="tab_<?= $ky ?>">
                                            <?php if (isset($kbms[$ky])): ?>
                                                <div class="table-responsive">
                                                    <table class="w-100 glass-table glass-table--compact">
                                                        <tbody>
                                                            <?php
                                                            $jamMulai  = new DateTime($kbms[$ky]->kbm_jam_mulai);
                                                            $jamSampai = new DateTime($kbms[$ky]->kbm_jam_mulai);

                                                            for ($i = 0; $i < $kbms[$ky]->kbm_jml_mapel_hari; $i++):
                                                                $jamke = $i + 1;

                                                                if (in_array($jamke, $arrIst)):
                                                                    $dur = $arrDur[$jamke];
                                                                    $jamSampai->add(new DateInterval('PT' . $dur . 'M')); ?>
                                                                    <tr class="jam glass-row-break" data-jamke="<?= $jamke ?>">
                                                                        <td class="align-middle" style="width:120px">
                                                                            <?= $jamMulai->format('H:i') ?> &ndash; <?= $jamSampai->format('H:i') ?>
                                                                        </td>
                                                                        <td class="align-middle" style="color:rgba(255,255,255,0.3);font-style:italic;">
                                                                            Istirahat
                                                                        </td>
                                                                    </tr>
                                                                <?php $jamMulai->add(new DateInterval('PT' . $dur . 'M'));

                                                                else:
                                                                    $dur = $kbms[$ky]->kbm_jam_pel;
                                                                    $jamSampai->add(new DateInterval('PT' . $dur . 'M')); ?>
                                                                    <tr class="jam" data-jamke="<?= $jamke ?>">
                                                                        <td class="align-middle" style="width:120px">
                                                                            <?= $jamMulai->format('H:i') ?> &ndash; <?= $jamSampai->format('H:i') ?>
                                                                        </td>
                                                                        <td class="align-middle">
                                                                            <?= isset($jadwals[$ky][$jamke]) && $jadwals[$ky][$jamke]->kode != null
                                                                                ? $jadwals[$ky][$jamke]->kode : '--' ?>
                                                                        </td>
                                                                    </tr>
                                                            <?php $jamMulai->add(new DateInterval('PT' . $dur . 'M'));
                                                                endif;
                                                            endfor ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <div class="glass-empty-state">
                                                    Jadwal untuk kelas <b><?= $kelas ?></b> belum dibuat.
                                                </div>
                                            <?php endif ?>
                                        </div>

                                    <?php $no++;
                                    endforeach ?>
                                </div>

                            <?php else: ?>
                                <div class="glass-empty-state">Tidak ada jadwal hari ini.</div>
                            <?php endif ?>

                        </div>
                    </div>

                    <!-- AKTIVITAS -->
                    <div class="glass-card mb-3">
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">AKTIVITAS</h6>
                            <div class="glass-card-tools">
                                <button type="button" onclick="hapusLogAktivitas()" class="btn-glass btn-glass-danger btn-xs">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="glass-card-body">
                            <div id="log-list"></div>
                        </div>
                    </div>

                </div><!-- /RIGHT COLUMN -->

            </div>
        </div>
    </section>

</div><!-- /.content-wrapper -->


<!-- MODAL: KOMENTAR -->
<div class="modal fade" id="komentarModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tulis Komentar</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="img-fluid img-circle img-sm" src="<?= base_url('assets/img/siswa.png') ?>" alt="">
                <div class="img-push mt-2">
                    <?= form_open('create', ['id' => 'komentar']) ?>
                    <input type="hidden" id="id-post" name="id_post" value="">
                    <div class="input-group">
                        <input type="text" name="text" placeholder="Tulis komentar…"
                            class="form-control form-control-sm" required>
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-success btn-sm">Komentari</button>
                        </span>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-glass" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>


<!-- MODAL: BALASAN -->
<div class="modal fade" id="balasanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tulis Balasan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img class="img-fluid img-circle img-sm" src="<?= base_url('assets/img/siswa.png') ?>" alt="">
                <div class="img-push mt-2">
                    <?= form_open('create', ['id' => 'balasan']) ?>
                    <input type="hidden" id="id-comment" name="id_comment" value="">
                    <div class="input-group">
                        <input type="text" name="text" placeholder="Tulis balasan…"
                            class="form-control form-control-sm" required>
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-success btn-sm">Balas</button>
                        </span>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-glass" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>


<script>
    adaJadwalUjian = '<?= count($ada_ujian) ?>';
    localStorage.setItem('ada_jadwal_ujian', adaJadwalUjian);
</script>
<script src="<?= base_url() ?>/assets/app/js/jquery.rowspanizer.js"></script>
<script src="<?= base_url() ?>/assets/app/js/dashboard.js"></script>
