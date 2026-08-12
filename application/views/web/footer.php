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

	<!-- MODAL ACCESO DIRECTO -->
	<div class="modal fade" id="instalarAppModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><i class="fas fa-home"></i> Agregar acceso directo</h4>
				</div>
				<div class="modal-body">
					<div style="text-align: center; padding: 10px 0;">
						<p style="font-size: 16px; font-weight: bold; margin-bottom: 15px;">📱 Torneo BALTC</p>
						<p style="color: #666; margin-bottom: 20px;">Agrega un acceso directo a tu pantalla de inicio para abrir rápidamente sin escribir la URL.</p>

						<div id="android-install" style="display: none;">
							<button id="btn-instalar-app" class="btn btn-success btn-block" style="padding: 12px; font-size: 16px; font-weight: bold;">
								<i class="fas fa-download"></i> Agregar a pantalla de inicio
							</button>
							<p style="color: #999; font-size: 12px; margin-top: 10px;">El ícono aparecerá en tu home screen</p>
						</div>

						<div id="ios-install" style="display: none;">
							<div style="text-align: left; background: #f5f5f5; padding: 15px; border-radius: 8px;">
								<p style="font-weight: bold; margin-bottom: 10px;">Instrucciones para iOS:</p>
								<ol style="margin: 0; padding-left: 20px; font-size: 13px;">
									<li>Tap el botón <strong>Compartir</strong> (↑ arriba)</li>
									<li>Scroll down y tap <strong>"Add to Home Screen"</strong></li>
									<li>Tap "Add" en la esquina superior derecha</li>
								</ol>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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

	<!-- ACCESO DIRECTO SHORTCUT -->
	<script>
	$(function(){
		var deferredPrompt = null;
		var userHasSeenInstallPrompt = localStorage.getItem('torneo-install-prompt-shown');

		// Capturar el evento beforeinstallprompt (Android/Chrome)
		window.addEventListener('beforeinstallprompt', function(e) {
			// Prevenir que el navegador muestre su propio prompt
			e.preventDefault();
			deferredPrompt = e;

			// Mostrar nuestro modal solo si es la primera vez en esta sesión
			if(!userHasSeenInstallPrompt) {
				setTimeout(function() {
					$('#android-install').show();
					$('#instalarAppModal').modal('show');
					localStorage.setItem('torneo-install-prompt-shown', 'true');
				}, 1000); // Esperar 1 segundo después de cargar
			}
		});

		// Detectar iOS
		var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
		if(isIOS && !userHasSeenInstallPrompt) {
			setTimeout(function() {
				$('#ios-install').show();
				$('#instalarAppModal').modal('show');
				localStorage.setItem('torneo-install-prompt-shown', 'true');
			}, 1000);
		}

		// Botón Instalar
		$('#btn-instalar-app').on('click', function(){
			if(deferredPrompt) {
				deferredPrompt.prompt();
				deferredPrompt.userChoice.then(function(choiceResult) {
					if(choiceResult.outcome === 'accepted') {
						console.log('Usuario instaló la app');
					}
					deferredPrompt = null;
					$('#instalarAppModal').modal('hide');
				});
			}
		});
	});
	</script>
</footer>
</html>