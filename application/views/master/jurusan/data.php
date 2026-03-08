<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="page-title"><b><?= $judul ?></b></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="glass-card mb-0">
                <div class="glass-card-header">
                    <h6 class="glass-card-title"><?= $subjudul ?></h6>
                    <div class="glass-card-tools">
                        <button type="button" onclick="window.location.reload()" class="btn-glass">
                            <i class="fa fa-sync"></i>
                            <span class="d-none d-sm-inline-block ml-1">Reload</span>
                        </button>
                        <button type="button" data-toggle="modal" data-target="#createJurusanModal" class="btn-glass btn-glass-primary">
                            <i class="fas fa-plus"></i>
                            <span class="d-none d-sm-inline-block ml-1">Tambah Data</span>
                        </button>
                    </div>
                </div>
                <div class="glass-card-body">
                    <div class="glass-alert glass-alert-info mb-3">
                        Abaikan halaman ini jika sekolah tidak ada jurusan (jenjang SMP/MTs atau SD/MI)
                    </div>
                    <?= form_open('', ['id' => 'bulk']) ?>
                    <div class="table-responsive">
                        <table id="jurusan" class="w-100 table table-sm table-hover glass-table">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle p-0" style="width:40px">
                                        <input type="checkbox" id="select_all">
                                    </th>
                                    <th class="text-center align-middle p-0" style="width:40px">No.</th>
                                    <th class="align-middle">Kode</th>
                                    <th class="align-middle">Jurusan</th>
                                    <th class="align-middle">Mapel Peminatan</th>
                                    <th class="text-center align-middle p-0" style="width:100px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                foreach ($jurusans as $row) :
                                    $badges = '';
                                    foreach (explode(',', $row->mapel_peminatan ?? '') as $mid) {
                                        if ($mid != '')
                                            $badges .= '<span class="badge-pill-glass">' . $jurusan_mapels[$row->id_jurusan][$mid] . '</span>';
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center">
                                            <input id="check<?= $row->id_jurusan ?>" name="checked[]" class="check"
                                                value="<?= $row->id_jurusan ?>" type="checkbox">
                                        </td>
                                        <td class="text-center"><?= $no ?></td>
                                        <td><?= $row->kode_jurusan ?></td>
                                        <td><?= $row->nama_jurusan ?></td>
                                        <td><?= $badges ?></td>
                                        <td class="text-center">
                                            <a class="btn-glass btn-glass-warning btn-xs editRecord"
                                                data-toggle="modal" data-target="#editJurusanModal"
                                                data-deletable="<?= $row->deletable ?>"
                                                data-mapel="<?= $row->mapel_peminatan ?>"
                                                data-id="<?= $row->id_jurusan ?>"
                                                data-nama="<?= $row->nama_jurusan ?>"
                                                data-kode="<?= $row->kode_jurusan ?>">
                                                <i class="fa fa-pencil-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php $no++;
                                endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<?= form_open('create', ['id' => 'create']) ?>
<div class="modal fade" id="createJurusanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content glass-modal">
            <div class="glass-modal-header">
                <h5 class="glass-modal-title">Tambah Jurusan</h5>
                <button type="button" class="glass-modal-close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="glass-modal-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label glass-label">Jurusan*</label>
                    <div class="col-md-9">
                        <input type="text" id="createnama" name="nama_jurusan" class="glass-input" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label glass-label">Kode*</label>
                    <div class="col-md-9">
                        <input type="text" id="createkode" name="kode_jurusan" class="glass-input" required>
                    </div>
                </div>
                <?php foreach ($kode_peminatan as $kode) : ?>
                    <?php if (isset($mapel_peminatan[$kode->kode_kel_mapel])) : ?>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label glass-label"><?= $kode->nama_kel_mapel ?></label>
                            <div class="col-md-9">
                                <?php if (count($mapel_peminatan) === 0) : ?>
                                    <select class="glass-input">
                                        <option value="0" selected disabled>Belum ada mapel <?= $kode->nama_kel_mapel ?></option>
                                    </select>
                                    <?php else :
                                    foreach ($mapel_peminatan as $k_mpl => $mapels) :
                                        if ($k_mpl === $kode->kode_kel_mapel) : ?>
                                            <select name="mapel[]" id="create_mapel_peminatan<?= $kode->kode_kel_mapel ?>"
                                                class="glass-input mapel_peminatan select2" multiple="">
                                                <?php foreach ($mapels as $kd_mpl => $mapel) : ?>
                                                    <option class="opt-mapel" value="<?= $kd_mpl ?>"><?= $mapel ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                <?php endif;
                                    endforeach;
                                endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="glass-modal-footer">
                <button type="button" class="btn-glass" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn-glass btn-glass-primary">
                    <i class="fa fa-plus"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<!-- Modal Edit -->
<?= form_open('update', ['id' => 'update']) ?>
<div class="modal fade" id="editJurusanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content glass-modal">
            <div class="glass-modal-header">
                <h5 class="glass-modal-title">Edit Jurusan</h5>
                <button type="button" class="glass-modal-close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="glass-modal-body">
                <div class="form-group row" id="formnama">
                    <label class="col-md-3 col-form-label glass-label">Jurusan*</label>
                    <div class="col-md-9">
                        <input type="text" id="namaEdit" name="nama_jurusan" class="glass-input" required>
                    </div>
                </div>
                <div class="form-group row" id="formkode">
                    <label class="col-md-3 col-form-label glass-label">Kode*</label>
                    <div class="col-md-9">
                        <input type="text" id="kodeEdit" name="kode_jurusan" class="glass-input" required>
                    </div>
                </div>
                <?php foreach ($kode_peminatan as $kode) : ?>
                    <?php if (isset($mapel_peminatan[$kode->kode_kel_mapel])) : ?>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label glass-label"><?= $kode->nama_kel_mapel ?></label>
                            <div class="col-md-9">
                                <?php if (count($mapel_peminatan) === 0) : ?>
                                    <select class="glass-input">
                                        <option value="0" selected disabled>Belum ada mapel <?= $kode->nama_kel_mapel ?></option>
                                    </select>
                                    <?php else :
                                    foreach ($mapel_peminatan as $k_mpl => $mapels) :
                                        if ($k_mpl === $kode->kode_kel_mapel) : ?>
                                            <select name="mapel[]" id="mapel_peminatan<?= $kode->kode_kel_mapel ?>"
                                                class="glass-input mapel_peminatan select2" multiple="">
                                                <!--
                                                <?php foreach ($mapels as $kd_mpl => $mapel) : ?>
                                                    <option class="opt-mapel" value="<?= $kd_mpl ?>"><?= $mapel ?></option>
                                                <?php endforeach; ?>
                                                -->
                                            </select>
                                <?php endif;
                                    endforeach;
                                endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="glass-modal-footer">
                <input type="hidden" id="editIdJurusan" name="id_jurusan">
                <button type="button" class="btn-glass" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn-glass btn-glass-primary">
                    <i class="fa fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<script>
    var mapels = JSON.parse('<?= json_encode($mapel_peminatan) ?>');
</script>
<script src="<?= base_url() ?>/assets/app/js/master/jurusan/crud.js"></script>
