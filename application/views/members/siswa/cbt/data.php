<?php
$arrGuru = [];
foreach ($guru as $g) {
    $arrGuru[$g->id_guru] = $g->nama_guru;
}

$jam_pertama = null;
$jadwal_selesai = [];
?>
<div class="content-wrapper bg-dark" style="margin-top: -1px;">
    <div class="sticky">
    </div>
    <section class="content overlap pt-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <?php
                    $cbt_setting = [];
                    $this->load->view('members/siswa/templates/top'); ?>
                </div>
                <div class="col-12">
                    <div class="card my-shadow">
                        <div class="card-header bg-info">
                            <div class="text-center text-bold">INFO ASSESMEN</div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    //echo '<pre>';
                                    //var_dump($elapsed);
                                    //var_dump(strtotime($durasiMulai->format('H:i')));
                                    //echo '</pre>';
                                    if ($cbt_info == null) : ?>
                                        <div class="alert alert-default-warning">
                                            <div class="text-center">Tidak ada jadwal penilaian</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="card border">
                                            <div class="card-header border-bottom-0">
                                                <h4 class="card-title mt-1 text-wrap text-bold"><?= $siswa->nama ?></h4>
                                            </div>
                                            <div class="card-body text-dark pt-0">
                                                <ul class="list-group list-group-unbordered">
                                                    <?php
                                                    $arrTitle = ['No. Peserta', 'Ruang', 'Sesi', 'Dari', 'Sampai'];
                                                    $arrSub = [$cbt_info->no_peserta->nomor_peserta ?? '', $cbt_info->nama_ruang ?? '', $cbt_info->nama_sesi ?? '', substr($cbt_info->waktu_mulai, 0, -3), substr($cbt_info->waktu_akhir, 0, -3)];
                                                    foreach ($arrTitle as $key => $title) :
                                                        if ($arrSub[$key] == null) array_push($cbt_setting, $title)
                                                    ?>
                                                        <li class="list-group-item p-1">
                                                            <?= $title ?>
                                                            <span class="float-right"><b><?= $arrSub[$key] ?></b></span>
                                                        </li>
                                                    <?php
                                                    endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-8">
                                    <div class="alert alert-default-danger">
                                        <div class="text-center"><b>** INFORMASI PENTING **</b></div>
                                        Selama melaksanakan Assesmen <b>Siswa DILARANG:</b>
                                        <ul>
                                            <li>
                                                Meninggalkan ruang ujian tanpa izin pengawas
                                            </li>
                                            <li>
                                                Logout/Keluar dari aplikasi tanpa izin dari pengawas
                                            </li>
                                            <li>
                                                Saling memberitahukan jawaban sesama peserta
                                            </li>
                                            <li>
                                                Membawa makanan dan minuman
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card my-shadow">
                        <div class="card-header bg-info">
                            <div class="text-center text-bold">
                                JADWAL ASSESMEN<br /><small><?= buat_tanggal(date('D, d M Y')) ?></small>
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="row" id="jadwal-content">
                                <?php
                                if ($cbt_info == null || count($cbt_setting) > 0) : ?>
                                    <div class="col-12 alert alert-default-warning">
                                        <div class="text-center">Tidak ada jadwal assesmen.</div>
                                    </div>
                                    <?php else:
                                    $jamSesi = $cbt_info == null ? '0' : (isset($cbt_info->sesi_id) ? $cbt_info->sesi_id : $cbt_info->id_sesi);
                                    if (isset($cbt_jadwal[date('Y-m-d')]) && count($cbt_jadwal[date('Y-m-d')]) > 0) :
                                        foreach ($cbt_jadwal[date('Y-m-d')] as $key => $jadwal) :
                                            $kk = unserialize($jadwal->bank_kelas ?? '');
                                            $arrKelasCbt = [];
                                            foreach ($kk as $k) {
                                                array_push($arrKelasCbt, $k['kelas_id']);
                                            }

                                            $startDay = strtotime($jadwal->tgl_mulai);
                                            $endDay = strtotime($jadwal->tgl_selesai);
                                            $today = strtotime(date('Y-m-d'));

                                            //echo 'skrg='.$today . ' start=' . $startDay . ' end=' . $endDay;

                                            $hariMulai = new DateTime($jadwal->tgl_mulai);
                                            $hariSampai = new DateTime($jadwal->tgl_selesai);

                                            $sesiMulai = new DateTime($sesi[$jamSesi]['mulai']);
                                            $sesiSampai = new DateTime($sesi[$jamSesi]['akhir']);
                                            $now = strtotime(date('H:i'));

                                            $durasi = $elapsed[$jadwal->id_jadwal];
                                            $jadwal_selesai[$jadwal->tgl_mulai][$jadwal->jam_ke] = $durasi != null
                                                ? $durasi->status == '2'
                                                : false;

                                            if ($durasi != null) {
                                                $selesai = $durasi->selesai != null;
                                                $lanjutkan = $durasi->lama_ujian != null;
                                                $reset = $durasi->reset;
                                                if ($lanjutkan != null && !$selesai) $bg = 'bg-gradient-warning';
                                                elseif ($selesai) $bg = 'bg-gradient-success';
                                                else {
                                                    $bg = 'bg-gradient-danger';
                                                }
                                            } else {
                                                $selesai = false;
                                                $lanjutkan = false;
                                                $reset = 0;
                                                $bg = 'bg-gradient-danger';
                                            }
                                            $jam_ke = $jadwal->jam_ke == '0' ? '1' : $jadwal->jam_ke;
                                    ?>
                                            <div class="jadwal-cbt col-md-6 col-lg-4">
                                                <div class="card border">
                                                    <div class="card-header">
                                                        <div class="card-title">
                                                            <b>Jam ke: <?= $jam_ke ?></b>
                                                        </div>
                                                        <div class="card-tools">
                                                            <b><i class="fa fa-clock-o text-gray mr-1"></i><?= $jadwal->durasi_ujian ?>
                                                                mnt</b>
                                                        </div>
                                                    </div>
                                                    <div class="card-body text-dark p-0">
                                                        <div class="small-box <?= $bg ?> mb-0">
                                                            <div class="inner">
                                                                <h6 class="crop-text-1">
                                                                    <b><?= $jadwal->nama_mapel ?></b>
                                                                </h6>
                                                                <h5><?= $jadwal->nama_jenis ?></h5>
                                                            </div>
                                                            <div class="icon">
                                                                <i class="fas fa-book-open"></i>
                                                            </div>
                                                            <hr style="margin-top:0; margin-bottom: 0">
                                                            <?php
                                                            if (!$lanjutkan && $reset == 0 && !$selesai) : ?>
                                                                <?php if ($today < $startDay) : ?>
                                                                    <div id="<?= $jadwal->id_jadwal ?>"
                                                                        class="status small-box-footer p-2"
                                                                        data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                        data-jamke="<?= $jadwal->jam_ke ?>">
                                                                        <b>Belum Mulai</b>
                                                                    </div>
                                                                <?php elseif ($today > $endDay) : ?>
                                                                    <div id="<?= $jadwal->id_jadwal ?>"
                                                                        class="status small-box-footer p-2"
                                                                        data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                        data-jamke="<?= $jadwal->jam_ke ?>">
                                                                        <b>Sudah Berakhir</b>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <?php if ($now < strtotime($sesiMulai->format('H:i'))) : ?>
                                                                        <div id="<?= $jadwal->id_jadwal ?>"
                                                                            class="status small-box-footer p-2"
                                                                            data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                            data-jamke="<?= $jadwal->jam_ke ?>">
                                                                            <b><?= strtoupper($cbt_info->nama_sesi ?? '') ?>
                                                                                Belum Mulai</b>
                                                                        </div>
                                                                    <?php elseif ($now > strtotime($sesiSampai->format('H:i'))) : ?>
                                                                        <div id="<?= $jadwal->id_jadwal ?>"
                                                                            class="status small-box-footer p-2"
                                                                            data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                            data-jamke="<?= $jadwal->jam_ke ?>">
                                                                            <b><?= strtoupper($cbt_info->nama_sesi ?? '') ?>
                                                                                Sudah Berakhir</b>
                                                                        </div>
                                                                    <?php else : ?>
                                                                        <?php if (isset($jadwal_selesai[$jadwal->tgl_mulai][$jadwal->jam_ke - 1]) && $jadwal_selesai[$jadwal->tgl_mulai][$jadwal->jam_ke - 1] == false) : ?>
                                                                            <button id="<?= $jadwal->id_jadwal ?>"
                                                                                class="btn-block btn status text-white small-box-footer p-2 btn-disabled"
                                                                                disabled>
                                                                                <b>Menunggu</b>
                                                                            </button>
                                                                        <?php else : ?>
                                                                            <button id="<?= $jadwal->id_jadwal ?>"
                                                                                onclick="location.href='<?= base_url('siswa/konfirmasi/' . $jadwal->id_jadwal) ?>'"
                                                                                class="btn btn-block status text-white small-box-footer p-2"
                                                                                data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                                data-jamke="<?= $jadwal->jam_ke ?>">
                                                                                <b>Kerjakan</b><i
                                                                                    class="fas fa-arrow-circle-right ml-3"></i>
                                                                            </button>
                                                                <?php endif;
                                                                    endif;
                                                                endif; ?>
                                                            <?php elseif ($lanjutkan && !$selesai) : ?>
                                                                <button id="<?= $jadwal->id_jadwal ?>"
                                                                    class="btn-block btn status small-box-footer p-2 text-white"
                                                                    onclick="location.href='<?= base_url('siswa/konfirmasi/' . $jadwal->id_jadwal) ?>'"
                                                                    data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                    data-jamke="<?= $jadwal->jam_ke ?>">
                                                                    <b>LANJUTKAN</b><i
                                                                        class="fas fa-arrow-circle-right ml-3"></i>
                                                                </button>
                                                            <?php else : ?>
                                                                <div id="<?= $jadwal->id_jadwal ?>"
                                                                    class="btn status small-box-footer p-2"
                                                                    data-tgl="<?= $jadwal->tgl_mulai ?>"
                                                                    data-jamke="<?= $jadwal->jam_ke ?>">
                                                                    <b>SUDAH SELESAI</b>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        endforeach;
                                    else: ?>
                                        <div class="col-12 alert alert-default-warning">
                                            <div class="text-center">Tidak ada jadwal assesmen hari ini.</div>
                                        </div>
                                <?php
                                    endif;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card my-shadow">
                        <div class="card-header bg-info">
                            <div class="text-center text-bold">
                                JADWAL ASSESMEN SEBELUMNYA
                            </div>
                        </div>
                        <div class="card-body text-dark">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <?php
                                    foreach ($cbt_jadwal as $tgl => $jadwals) :
                                        if ($tgl != date('Y-m-d')) : ?>
                                            <tr class="bg-info">
                                                <td colspan="4" class="tgl-ujian"
                                                    data-tgl="<?= $tgl ?>">
                                                    <?= buat_tanggal(date('D, d M Y', strtotime($tgl))) ?>
                                                </td>
                                            </tr>
                                            <tr class="bg-info">
                                                <th class="text-center align-middle">Jam ke</th>
                                                <th class="text-center align-middle">Mapel</th>
                                                <th class="text-center align-middle">Jenis Penilaian</th>
                                                <th class="align-middle">Status</th>
                                            </tr>

                                            <?php
                                            foreach ($jadwals as $key => $jadwal) :
                                                $jam_ke = $jadwal->jam_ke == '0' ? '1' : $jadwal->jam_ke;
                                                $kk = unserialize($jadwal->bank_kelas ?? '');
                                                $arrKelasCbt = [];
                                                foreach ($kk as $k) {
                                                    array_push($arrKelasCbt, $k['kelas_id']);
                                                }

                                                $startDay = strtotime($jadwal->tgl_mulai);
                                                $endDay = strtotime($jadwal->tgl_selesai);
                                                $today = strtotime(date('Y-m-d'));

                                                $hariMulai = new DateTime($jadwal->tgl_mulai);
                                                $hariSampai = new DateTime($jadwal->tgl_selesai);

                                                $sesiMulai = new DateTime($sesi[$jamSesi]['mulai']);
                                                $sesiSampai = new DateTime($sesi[$jamSesi]['akhir']);
                                                $now = strtotime(date('H:i'));

                                                $durasi = $elapsed[$jadwal->id_jadwal];
                                                $jadwal_selesai[$jadwal->tgl_mulai][$jadwal->jam_ke] = $durasi != null
                                                    ? $durasi->status == '2'
                                                    : false;

                                                if ($durasi != null) {
                                                    $selesai = $durasi->selesai != null;
                                                    $lanjutkan = $durasi->lama_ujian != null;
                                                    $reset = $durasi->reset;
                                                    if ($lanjutkan != null && !$selesai) $bg = 'btn-warning';
                                                    elseif ($selesai) $bg = 'btn-secondary';
                                                    else {
                                                        $bg = 'btn-danger';
                                                    }
                                                } else {
                                                    $selesai = false;
                                                    $lanjutkan = false;
                                                    $reset = 0;
                                                    $bg = 'btn-primary';
                                                }

                                                $status = '';
                                                if (!$lanjutkan && $reset == 0 && !$selesai) {
                                                    if ($today < $startDay) {
                                                        $status = '<button id="' . $jadwal->id_jadwal . '" class="status-table btn btn-disabled ' . $bg . '"'
                                                            . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                            . ' <b>Belum Mulai</b></button>';
                                                    } elseif ($today > $endDay) {
                                                        $status = '<button id="' . $jadwal->id_jadwal . '" class="status-table btn btn-disabled ' . $bg . '"'
                                                            . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                            . ' <b>Sudah Berakhir</b></button>';
                                                    } else {
                                                        if ($now < strtotime($sesiMulai->format('H:i'))) {
                                                            $status = '<button id="' . $jadwal->id_jadwal . '" class="status-table btn btn-disabled ' . $bg . '"'
                                                                . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                                . ' <b>' . strtoupper($cbt_info->nama_sesi ?? '') . ' Belum Mulai</b></button>';
                                                        } elseif ($now > strtotime($sesiSampai->format('H:i'))) {
                                                            $status = '<button id="' . $jadwal->id_jadwal . '" class="status-table btn btn-disabled ' . $bg . '"'
                                                                . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                                . '<b>' . strtoupper($cbt_info->nama_sesi ?? '') . ' Sudah Berakhir</b></button>';
                                                        } else {
                                                            if (isset($jadwal_selesai[$jadwal->tgl_mulai][$jadwal->jam_ke - 1]) && $jadwal_selesai[$jadwal->tgl_mulai][$jadwal->jam_ke - 1] == false) {
                                                                $status = '<button id="' . $jadwal->id_jadwal . '"'
                                                                    . ' class="status-table btn btn-disabled ' . $bg . '" disabled>'
                                                                    . ' <b>Menunggu</b></button>';
                                                            } else {
                                                                $status = '<button id="' . $jadwal->id_jadwal . '"'
                                                                    . ' onclick="location.href=\'' . base_url() . 'siswa/konfirmasi/' . $jadwal->id_jadwal . '\'"'
                                                                    . ' class="status-table btn ' . $bg . '"'
                                                                    . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                                    . ' <b>Kerjakan</b></button>';
                                                            }
                                                        }
                                                    }
                                                } elseif ($lanjutkan && !$selesai) {
                                                    $status = '<button id="' . $jadwal->id_jadwal . '" class="status-table btn ' . $bg . '"'
                                                        . ' onclick="location.href=\'' . base_url() . 'siswa/konfirmasi/' . $jadwal->id_jadwal . '\'"'
                                                        . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                        . ' <b>Lanjutkan</b></button>';
                                                } else {
                                                    $status = '<button id="' . $jadwal->id_jadwal . '" class="status-table btn btn-disabled ' . $bg . '"'
                                                        . ' data-tgl="' . $jadwal->tgl_mulai . '" data-jamke="' . $jadwal->jam_ke . '">'
                                                        . ' <b>Sudah Selesai</b></button>';
                                                }
                                            ?>

                                                <tr>
                                                    <td class="text-center"><?= $jam_ke ?>
                                                        <br><?= $jadwal->durasi_ujian ?> mnt
                                                    </td>
                                                    <td class="text-center"><?= $jadwal->nama_mapel ?><br>
                                                        <!-- <small class="d-block d-md-none"><?= $jadwal->nama_jenis ?></small> -->
                                                    </td>
                                                    <td class="text-center"><?= $jadwal->nama_jenis ?></td>
                                                    <td><?= $status ?></td>
                                                </tr>
                                    <?php
                                            endforeach;
                                        endif;
                                    endforeach; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>