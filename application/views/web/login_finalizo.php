<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

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
							<p>La inscripción Finalizó</p>
							<!-- <form method="post" id="login" class="white">
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


<script type="text/javascript">
var loginurl = '<?=base_url('login')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';

$(function() {
	login.run();
});
</script> -->