<div class="content-wrapper bg-dark pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><b><?= $judul ?></b></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default my-shadow mb-0">
                <div class="card-header bg-orange">
                    <h6 class="card-title"><b><?= $subjudul ?></b></h6>
                    <div class="card-tools">
                        <button type="button" onclick="window.location.reload()" class="btn btn-sm btn-default"><i
                                    class="fa fa-sync"></i> <span class="d-none d-sm-inline-block ml-1">Reload</span>
                        </button>
                        <button type="button" data-toggle="modal" data-target="#createJurusanModal"
                                class="btn btn-sm btn-primary"><i
                                    class="fas fa-plus"></i><span
                                    class="d-none d-sm-inline-block ml-1">Tambah Data</span>
                        </button>
                    </div>
                </div>
                <div class="card-body text-dark">
                    <div class="alert alert-default-light border border-success align-content-center mb-3" role="alert">
                        Abaikan halaman ini jika sekolah tidak ada jurusan (jenjang SMP/MTs atau SD/MI)
                    </div>
                    <?= form_open('', array('id' => 'bulk')) ?>
                    <div class="table-responsive">
                        <table id="jurusan" class="w-100 table table-sm table-striped table-bordered table-hover">
                            <thead class="bg-maroon">
                            <tr>
                                <th height="40" class="text-center align-middle p-0" style="width: 40px">
                                    <input type="checkbox" id="select_all">
                                </th>
                                <th style="width: 40px" class="text-center align-middle p-0">No.</th>
                                <th class="align-middle">Kode</th>
                                <th class="align-middle">Jurusan</th>
                                <th class="align-middle">Mapel Peminatan</th>
                                <th class="text-center align-middle p-0" style="width: 100px"><span>Aksi</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $no = 1;
                            foreach ($jurusans as $row) :
                                $badges = '';
                                foreach (explode(',', $row->mapel_peminatan ?? '') as $mid) {
                                    if ($mid != '')
                                        $badges .= '<div class="badge badge-btn badge-success mr-1">' . $jurusan_mapels[$row->id_jurusan][$mid] . '</div>';
                                }
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <div>
                                            <input id="check<?= $row->id_jurusan ?>" name="checked[]" class="check"
                                                   value="<?= $row->id_jurusan ?>" type="checkbox">
                                        </div>
                                    </td>
                                    <td class="text-center"><?= $no ?></td>
                                    <td><?= $row->kode_jurusan ?></td>
                                    <td><?= $row->nama_jurusan ?></td>
                                    <td><?= $badges ?></td>
                                    <td>
                                        <div class="text-center">
                                            <a class="btn btn-xs btn-warning editRecord" data-toggle="modal"
                                               data-target="#editJurusanModal" data-deletable="<?= $row->deletable ?>"
                                               data-mapel="<?= $row->mapel_peminatan ?>"
                                               data-id='<?= $row->id_jurusan ?>'
                                               data-nama='<?= $row->nama_jurusan ?>'
                                               data-kode='<?= $row->kode_jurusan ?>'>
                                                <i class="fa fa-pencil-alt text-white"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php $no++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?= form_open('create', array('id' => 'create')) ?>
<div class="modal fade" id="createJurusanModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">Tambah Jurusan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Jurusan*</label>
                    <div class="col-md-9">
                        <input type="text" id="createnama" name="nama_jurusan" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">Kode*</label>
                    <div class="col-md-9">
                        <input type="text" id="createkode" name="kode_jurusan" class="form-control" required>
                    </div>
                </div>
                <?php foreach ($kode_peminatan as $kode) : ?>
                    <div class="form-group row">
                        <?php
                        if (isset($mapel_peminatan[$kode->kode_kel_mapel])) : ?>
                            <label class="col-md-3 col-form-label"><?= $kode->nama_kel_mapel ?></label>
                            <div class="col-md-9">
                                <?php if (count($mapel_peminatan) === 0) : ?>
                                    <select class="form-control">
                                        <option value="0" selected disabled>Belum ada mapel <?= $kode->nama_kel_mapel ?></option>
                                    </select>
                                <?php else:
                                    foreach ($mapel_peminatan as $k_mpl=>$mapels) :
                                        if ($k_mpl === $kode->kode_kel_mapel) : ?>
                                            <select name="mapel[]" id="create_mapel_peminatan<?= $kode->kode_kel_mapel ?>"
                                                    class="form-control mapel_peminatan select2" multiple="">
                                                <?php foreach ($mapels as $kd_mpl=>$mapel) :?>
                                                    <option class="opt-mapel" value="<?= $kd_mpl ?>"><?= $mapel ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; endforeach; endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
<?= form_close() ?>

<?= form_open('update', array('id' => 'update')) ?>
<div class="modal fade" id="editJurusanModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Jurusan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row" id="formnama">
                    <label class="col-md-3 col-form-label">Jurusan*</label>
                    <div class="col-md-9">
                        <input type="text" id="namaEdit" name="nama_jurusan" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row" id="formkode">
                    <label class="col-md-3 col-form-label">Kode*</label>
                    <div class="col-md-9">
                        <input type="text" id="kodeEdit" name="kode_jurusan" class="form-control" required>
                    </div>
                </div>
                <?php
                foreach ($kode_peminatan as $kode) : ?>
                    <div class="form-group row">
                        <?php if (isset($mapel_peminatan[$kode->kode_kel_mapel])) : ?>
                            <label class="col-md-3 col-form-label"><?= $kode->nama_kel_mapel ?></label>
                            <div class="col-md-9">
                                <?php if (count($mapel_peminatan) === 0) : ?>
                                    <select class="form-control">
                                        <option value="0" selected disabled>Belum ada mapel <?= $kode->nama_kel_mapel ?></option>
                                    </select>
                                <?php else:
                                    foreach ($mapel_peminatan as $k_mpl=>$mapels) :
                                        if ($k_mpl === $kode->kode_kel_mapel) : ?>
                                            <select name="mapel[]" id="mapel_peminatan<?= $kode->kode_kel_mapel ?>"
                                                    class="form-control mapel_peminatan select2" multiple="">
                                                <!--
                                                <?php foreach ($mapels as $kd_mpl=>$mapel) : ?>
                                                    <option class="opt-mapel" value="<?= $kd_mpl ?>"><?= $mapel ?></option>
                                                <?php endforeach; ?>
                                                -->
                                            </select>
                                        <?php endif; endforeach; endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="editIdJurusan" name="id_jurusan" class="form-control">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">
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
