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

	<!-- MODAL AGREGAR A PANTALLA DE INICIO -->
	<div class="modal fade" id="addToHomeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"><i class="fas fa-home"></i> Acceso rápido</h4>
				</div>
				<div class="modal-body" style="text-align:center;">
					<p style="font-size:16px; margin-bottom:20px;">¿Agregar <strong>Torneo BALTC</strong> a pantalla de inicio?</p>
					<p style="color:#666; font-size:13px; margin-bottom:20px;">Accede más rápido sin escribir la URL</p>
					<img src="https://www.baltc.net/wp-content/uploads/2016/09/cropped-favicon-1-192x192.png" style="width:80px; margin-bottom:15px;">
				</div>
				<div class="modal-footer" style="text-align:center;">
					<button type="button" class="btn btn-default" data-dismiss="modal">Ahora no</button>
					<button type="button" class="btn btn-success" id="btn-agregar-home">Agregar</button>
				</div>
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

	<!-- AGREGAR A PANTALLA DE INICIO (Chrome/Android) -->
	<script>
	$(function(){
		var deferredPrompt = null;
		var promptShown = localStorage.getItem('torneo-add-home-prompt-shown');

		// Capturar beforeinstallprompt (solo Chrome/Android)
		window.addEventListener('beforeinstallprompt', function(e) {
			e.preventDefault();
			deferredPrompt = e;

			// Mostrar modal solo si no lo ha visto antes
			if(!promptShown) {
				setTimeout(function() {
					$('#addToHomeModal').modal('show');
				}, 1500); // Esperar 1.5 segundos después de loguear
			}
		});

		// Botón Agregar
		$('#btn-agregar-home').on('click', function(){
			if(deferredPrompt) {
				deferredPrompt.prompt();
				deferredPrompt.userChoice.then(function(choiceResult) {
					if(choiceResult.outcome === 'accepted') {
						console.log('✓ Agregado a pantalla de inicio');
					}
					deferredPrompt = null;
					$('#addToHomeModal').modal('hide');
					localStorage.setItem('torneo-add-home-prompt-shown', 'true');
				});
			}
		});

		// Guardar que se mostró cuando dice "Ahora no"
		$('#addToHomeModal').on('hide.bs.modal', function(){
			localStorage.setItem('torneo-add-home-prompt-shown', 'true');
		});
	});
	</script>
</footer>
</html>