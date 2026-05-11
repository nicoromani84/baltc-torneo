<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="page login-page">
	<div class="container d-flex align-items-center">
		<div class="form-holder has-shadow">
			<div class="form">
				<div class="content">
					<div class="logo">
						<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
					</div>
					<form method="post" id="login">
						<div class="form-group">
							<input id="login-username" type="text" name="username" required class="input-material" autocomplete="off">
							<label for="login-username" class="label-material">Usuario</label>
						</div>
						<div class="form-group">
							<input id="login-password" type="password" name="password" required class="input-material" autocomplete="off">
							<label for="login-password" class="label-material">Contraseña</label>
						</div>
						<button type="submit" class="btn btn-primary">Login</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
var loginurl = '<?=base_url('/admin')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';

$(function() {
	admin.login.run();
});
</script>