<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if($inscripciones_cerradas): ?>
	<div class="page login-page">
		<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
			<div class="alert alert-info text-center" style="max-width: 600px; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
				<div style="font-size: 24px; margin-bottom: 20px;">
					<i class="fas fa-lock" style="color: #17a2b8; font-size: 48px; margin-bottom: 15px; display: block;"></i>
				</div>
				<h3 style="margin-bottom: 15px; color: #333;">Cerró la inscripción</h3>
				<p style="font-size: 16px; color: #555; margin-bottom: 20px;">
					En breve publicaremos los cuadros del torneo.
				</p>
				<p style="font-size: 14px; color: #888;">
					Gracias por participar en el Torneo Interno de Dobles del BALTC.
				</p>
			</div>
		</div>
	</div>
<?php else: ?>
	<div class="page login-page">
		<div class="container d-flex align-items-center">
			<div class="form-holder has-shadow">
				<div class="row">
					<!-- Logo & Information Panel-->
					<div class="col-lg-6">
						<div class="info d-flex align-items-center bg-white-alpha">
							<div class="content">
								<div class="logo">
									<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
								</div>
							</div>
						</div>
					</div>
					<!-- Form Panel    -->
					<div class="col-lg-6">
						<div class="form d-flex align-items-center">
							<div class="content">
								<p>Escribí tu DNI o N de Pasaporte</p>
								 <form method="post" id="login" class="white">
									<div class="form-group">
										<input id="login-dni" type="number" name="dni" required class="input-material" autocomplete="off" autofocus>
										<label for="login-dni" class="label-material active">DNI</label>
									</div>
									<button type="submit" class="btn btn-primary">Login</a>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>


<script type="text/javascript">
var loginurl = '<?=base_url('login')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';

$(function() {
	login.run();
});
</script> -->