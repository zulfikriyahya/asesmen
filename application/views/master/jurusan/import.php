<div class="content-wrapper pt-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-6">
                    <h1 class="page-title"><?= $judul ?></h1>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <a href="<?= base_url('datajurusan') ?>" class="btn-glass btn-glass-danger">
                        <i class="fas fa-arrow-circle-left"></i>
                        <span class="d-none d-sm-inline-block ml-1">Batal</span>
                    </a>
                </div>
            </div>
        </div>
    </section>


    <section class="content">
        <div class="container-fluid">

            <div class="glass-alert glass-alert-warning mb-4">
                <strong>Catatan!</strong> Untuk import data dari file excel, silahkan download templatenya terlebih dahulu.
            </div>

            <div class="row">
                <div class="col-xl-4 col-lg-5">

                    <div class="glass-card mb-4">
                        <?= form_open_multipart('', ['id' => 'formPreviewExcel']) ?>
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">File Excel</h6>
                            <a href="<?= base_url('uploads/import/format/format_jurusan.xlsx') ?>"
                                class="btn-glass btn-glass-success">
                                <i class="fas fa-download"></i>
                                <span class="ml-1">Download Template</span>
                            </a>
                        </div>
                        <div class="glass-card-body excel">
                            <div class="form-group">
                                <label class="glass-label">Pilih file excel</label>
                                <input type="file" id="input-file-events-excel" name="upload_file" class="dropify">
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>

                    <div class="glass-card mb-4">
                        <?= form_open_multipart('', ['id' => 'formPreviewWord']) ?>
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">File Word</h6>
                            <a href="<?= base_url('uploads/import/format/format_jurusan.docx') ?>"
                                class="btn-glass btn-glass-success">
                                <i class="fas fa-download"></i>
                                <span class="ml-1">Download Template</span>
                            </a>
                        </div>
                        <div class="glass-card-body word">
                            <div class="form-group">
                                <label class="glass-label">Pilih file word</label>
                                <input type="file" id="input-file-events-word" name="upload_file" class="dropify">
                            </div>
                        </div>
                        <?= form_close() ?>
                    </div>

                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="glass-card mb-4">
                        <?= form_open('', ['id' => 'formUpload']) ?>
                        <div class="glass-card-header">
                            <h6 class="glass-card-title">Preview</h6>
                            <input type="hidden" name="jurusan" id="formInput">
                            <button name="preview" type="submit" class="btn-glass btn-glass-primary">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="ml-1">Upload</span>
                            </button>
                        </div>
                        <?= form_close() ?>
                        <div class="glass-card-body" id="file-preview">
                            <span class="text-muted" style="font-size:.82rem;">
                                Pastikan anda telah mengisi format yang telah disediakan.
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script src="<?= base_url() ?>/assets/app/js/jquery.htmlparser.min.js"></script>
<script src="<?= base_url() ?>/assets/app/js/master/jurusan/import.js"></script>
