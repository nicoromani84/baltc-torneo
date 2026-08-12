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

	<!-- BANNER AGREGAR A PANTALLA DE INICIO -->
	<div id="install-banner" style="display:none; position:fixed; bottom:0; left:0; right:0; background:#1a1a2e; border-top:2px solid #a5d051; padding:12px 20px; box-shadow:0 -2px 10px rgba(0,0,0,0.3); z-index:999; font-family:Arial,sans-serif;">
		<div style="display:flex; align-items:center; justify-content:space-between; max-width:1200px; margin:0 auto;">
			<div style="display:flex; align-items:center; gap:12px; flex:1;">
				<img src="https://www.baltc.net/wp-content/uploads/2016/09/cropped-favicon-1-32x32.png" style="width:32px; height:32px;">
				<span style="color:#fff; font-size:14px;">Agregar "Torneo BALTC" a pantalla de inicio</span>
			</div>
			<div style="display:flex; gap:8px;">
				<button id="install-btn" style="background:#a5d051; color:#1a1a2e; border:none; padding:8px 20px; border-radius:4px; font-weight:bold; font-size:13px; cursor:pointer; white-space:nowrap;">Agregar</button>
				<button id="close-banner" style="background:transparent; border:none; color:rgba(255,255,255,0.6); cursor:pointer; font-size:20px; padding:0 10px;">×</button>
			</div>
		</div>
	</div>

</body>

<footer>
	<!-- JAVASCRIPT APLICACION -->
	<script src="<?=asset_url('js')?>/<?=$classname?>.js"></script>
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
	<!-- DATETIMEPICKER -->
	<script type="text/javascript" src="<?=asset_url('vendor')?>/bootstrap.datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
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
	<!-- MULTIMODAL -->
	<script type="text/javascript" src="<?=asset_url('js')?>/multimodal.js"></script>
	<!-- BOOTSTRAP CORE -->
	<script src="<?=asset_url('vendor')?>/bootstrap/js/bootstrap.min.js"></script>

	<!-- BANNER AGREGAR A PANTALLA DE INICIO -->
	<script>
	$(function(){
		var deferredPrompt = null;
		var bannerShown = localStorage.getItem('torneo-install-banner-shown');

		// Capturar beforeinstallprompt
		window.addEventListener('beforeinstallprompt', function(e) {
			e.preventDefault();
			deferredPrompt = e;

			// Mostrar banner solo si no lo ha visto antes
			if(!bannerShown) {
				setTimeout(function() {
					$('#install-banner').show();
				}, 2000);
			}
		});

		// Botón agregar
		$('#install-btn').on('click', function(){
			if(deferredPrompt) {
				deferredPrompt.prompt();
				deferredPrompt.userChoice.then(function(choice) {
					deferredPrompt = null;
					$('#install-banner').fadeOut();
					localStorage.setItem('torneo-install-banner-shown', 'true');
				});
			}
		});

		// Cerrar banner
		$('#close-banner').on('click', function(){
			$('#install-banner').fadeOut();
			localStorage.setItem('torneo-install-banner-shown', 'true');
		});
	});
	</script>
</footer>
</html>