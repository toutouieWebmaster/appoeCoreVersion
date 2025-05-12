</div><!-- END DIV PANEL -->
</div><!-- END DIV COL -->
</div><!-- END DIV ROW -->
</div><!-- END DIV CONTAINER -->
</div><!-- END DIV CONTENT-AREA -->
</div><!-- END DIV WRAPPER -->
<div id="loadMediaLibrary"></div>
<div id="mediaDetails" data-file-id="">Aucun fichier sélectionné</div>
</div><!-- END DIV SITE -->
<!-- MODAL INFO -->
<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="modalTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL INFO END -->

<?php use App\Flash;

includePluginsFilesForAppInFooter();
Flash::constructAndDisplay(); ?>
<div id="overlay">
    <div id="overlayContent" class="overlayContent"></div>
</div>
<script type="text/javascript" src="<?= WEB_TEMPLATE_URL; ?>plugins/bootstrap/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="<?= WEB_TEMPLATE_URL; ?>plugins/js/jquery.slimscroll.min.js"></script>
<script type="text/javascript" src="<?= WEB_TEMPLATE_URL; ?>plugins/waves/waves.min.js"></script>
<script type="text/javascript" src="<?= WEB_LIB_URL; ?>js/appoEditor/appoEditor.js"></script>
<script type="text/javascript" src="<?= WEB_LIB_URL; ?>js/datatable/dataTables.min.js"></script>
<script type="text/javascript" src="<?= WEB_LIB_URL; ?>js/datatable/bootstrap4.min.js"></script>
<?php includePluginsJs(true); ?>
</body>
</html>