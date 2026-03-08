</div><!-- /.content-wrapper -->

<footer class="main-footer" style="
    background: rgba(15, 17, 23, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-top: 1px solid rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.45);
    font-family: 'Lexend', sans-serif;
    font-size: .78rem;
    font-weight: 400;
">
    <strong style="color:rgba(255,255,255,0.65);">ZEDAPPS SCHOOL</strong>
    &mdash; Version <?= APP_VERSION ?>
    <div class="float-right d-none d-sm-inline-block">
        &copy; 2018 &ndash; <?= date('Y') ?>
    </div>
</footer>

<aside class="control-sidebar control-sidebar-dark"></aside>
</div><!-- /.wrapper -->

<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>

<!-- DataTables -->
<script src="<?= base_url() ?>/assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/jszip/jszip.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- Misc -->
<script src="<?= base_url() ?>/assets/plugins/pace-progress/pace.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/sparklines/sparkline.js"></script>
<script src="<?= base_url() ?>/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/chart.js/Chart.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/chart.js/chartjs-plugin-labels.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/moment/moment-with-locales.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= base_url() ?>/assets/plugins/summernote/summernote-bs4.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/audio/summernote-audio.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/file/summernote-file.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/gallery/dist/summernote-gallery.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/summernote/plugin/math/summernote-math.js"></script>
<!-- UI Plugins -->
<script src="<?= base_url() ?>/assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/toastr/toastr.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/multiselect/js/jquery.multi-select.js"></script>
<script src="<?= base_url() ?>/assets/plugins/multiselect/js/jquery.quicksearch.js"></script>
<script src="<?= base_url() ?>/assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/assets/app/js/jquery.toast.min.js"></script>
<script src="<?= base_url() ?>/assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- AdminLTE -->
<script src="<?= base_url() ?>/assets/adminlte/dist/js/adminlte.js"></script>
<!-- Datetime -->
<script src="<?= base_url() ?>/assets/plugins/jquery-datetimepicker/jquery.datetimepicker.full.js"></script>
<script src="<?= base_url() ?>/assets/plugins/jquery-timeago/jquery.timeago.js"></script>
<!-- App -->
<script src="<?= base_url() ?>/assets/app/js/show.toast.js"></script>
<script src="<?= base_url() ?>/assets/app/js/jquery-thumbnail-cut.js"></script>

<script>
    $.fn.dataTableExt.oApi.fnPagingInfo = function(s) {
        return {
            iStart: s._iDisplayStart,
            iEnd: s.fnDisplayEnd(),
            iLength: s._iDisplayLength,
            iTotal: s.fnRecordsTotal(),
            iFilteredTotal: s.fnRecordsDisplay(),
            iPage: Math.ceil(s._iDisplayStart / s._iDisplayLength),
            iTotalPages: Math.ceil(s.fnRecordsDisplay() / s._iDisplayLength)
        };
    };

    function ajaxcsrf() {
        var csrf = {};
        csrf['<?= $this->security->get_csrf_token_name() ?>'] = '<?= $this->security->get_csrf_hash() ?>';
        $.ajaxSetup({
            data: csrf
        });
    }

    function reload_ajax() {
        table.ajax.reload();
    }

    (function initDestroyTimeOutPace() {
        var counter = 0;
        var tid = setInterval(function() {
            var txt = $('.pace-progress').attr('data-progress-text');
            if (typeof txt !== 'undefined' && Number(txt.replace('%', '')) === 99) counter++;
            if (counter > 50) {
                clearInterval(tid);
                Pace.stop();
            }
        }, 100);
    })();

    function logout() {
        swal.fire({
            title: 'Logout',
            text: 'Anda yakin ingin logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'Logout!'
        }).then(function(r) {
            if (r.value) location.href = base_url + 'logout';
        });
    }
</script>

</body>

</html>
