<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<header>
	<div class="container">
		<div class="logo">
			<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
		</div>
		<ul class="buttons">
			<li>
				<a href="<?=base_url('/logout')?>">
					<span class="icon-logout"><i class="fas fa-sign-out-alt"></i></span>
				</a>
			</li>
		</ul>
	</div>
</header>
<div class="page reserve-page">
	<div id="reservar" class="container">
		<div class="container">
			<h1 class="d-none d-sm-block">Hola <strong><?=$user->name?></strong><br>Torneo Interno de Dobles Nocturno</h1>
			<h1 class="d-sm-none">Hola <strong><?=$user->name?></strong>Inscribite en el Torneo Interno de Singles </h1>
			<p>A partir del 19 de Mayo 2026</p>
			<div class="form">
				<form class="form-inline">
					<div class="form-group xs-fullwidth">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-star"></i></span></div>
							<select name="category" id="category" class="mr3 form-control niceselect">
								<option disabled selected value="">Elegir categoría</option>
								<?php foreach($categories as $category){ ?>
								<option value="<?=$category->id?>"><?=$category->name?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group xs-fullwidth" style="display:none">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-user-friends"></i></span></div>
							<input type="search" name="partner" autocomplete="off" placeholder="Elegí tu compañero" class="typeahead form-control">
							<i class="fas fa-search"></i>
						</div>
					</div>
					<div class="mobile-suggestions simplebar"></div>
					<div class="form-group to-right">
						<button type="submit" class="btn btn-primary"><span>Anotarse</span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var reservaurl = '<?=base_url('reserva')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';

$(function() {
	reserva.run();
});
</script>