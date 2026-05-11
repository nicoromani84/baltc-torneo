<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

	<!-- MODAL CAMBIAR CONTRASEÑA -->
	<!-- Modal -->
	<div class="modal fade" id="multimodal" tabindex="-1" role="dialog" data-backdrop="static">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer"></div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<!-- MODAL CAMBIAR CONTRASEÑA -->
	<!-- Modal -->
	<div class="modal fade" id="cambiarpassword" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="text-center">
						<div class="i-circle primary"><i class="fa fa-check"></i></div>
						<h4>Awesome!</h4>
						<p>Changes has been saved successfully!</p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">Proceed</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

</body>

<footer>
	<!-- JAVASCRIPT APLICACION -->
	<script src="<?=asset_url('js')?>/<?=$this->router->fetch_class()?>.js"></script>
	<!-- Main File -->
    <script src="<?=asset_url('js')?>/front.js"></script>
	<!-- JAVASCRIPT XMLHTTPREQUEST -->
	<script src="<?=asset_url('js')?>/api.js"></script>
	<!-- SET API URL -->
    <script type="text/javascript">api.baseUrl = '<?=base_url()?>';</script>
    <!-- JQUERY UI -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.ui/jquery-ui.min.js"></script>
	<script type="text/javascript" src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
	<!-- POPPER -->
    <script type="text/javascript" src="<?=asset_url('vendor')?>/popper.js/umd/popper.min.js"> </script>
    <!-- JQUERY COOKIE -->
    <script src="<?=asset_url('vendor')?>/jquery.cookie/jquery.cookie.js"> </script>
    <!-- JQUERY VALIDATE -->
    <script src="<?=asset_url('vendor')?>/jquery-validation/jquery.validate.js"></script>
	<!-- GRITTER (Notificaciones) -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.gritter/js/jquery.gritter.js"></script>
	<!-- SELECT2 -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.select2/select2.min.js"></script>
	<!-- DATA TABLES -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.datatables/jquery.datatables.min.js"></script>
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.datatables/bootstrap-adapter/js/datatables.js"></script>
	<!-- TYPEHEAD (AUTOCOMPLETE) -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/bootstrap-typeahead/typeahead.min.js"></script>
 	<script type="text/javascript" src="<?=asset_url('vendor')?>/bootstrap-typeahead/bloodhound.min.js"></script>
	<!-- CONTEXT MENU JQUERY -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.context/jquery.contextMenu.min.js"></script>
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.context/jquery.ui.position.min.js"></script>
	<!-- Mustache -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/mustache/mustache.min.js"></script>
	<!-- Jquery Timepicker -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.timepicker/jquery.timepicker.js"></script>
	<!-- Jquery Nice Select -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.niceselect/jquery.nice-select.min.js"></script>
	<!-- Jquery SimpleBar -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.simplebar/simplebar.js"></script>
	<!-- Jquery MustacheJs -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.mustache/mustache.min.js"></script>
	<!-- Jquery MomentJs -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.momentjs/moment.min.js"></script>
	<!-- Jquery GIJGO DATEPICKER -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/jquery.gijgo/js/gijgo.min.js"></script>
	<!-- MULTIMODAL -->
	<script type="text/javascript" src="<?=asset_url('js')?>/multimodal.js"></script>
	<!-- BOOTSTRAP CORE -->
	<script src="<?=asset_url('vendor')?>/bootstrap/js/bootstrap.min.js"></script>
</footer>
</html>