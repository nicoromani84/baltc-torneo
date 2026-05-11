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
			<h1 class="d-none d-sm-block">Hola <strong><?=$user->name?></strong><br>Torneo Interno de Singles</h1>
			<h1 class="d-sm-none">Hola <strong><?=$user->name?></strong><br>Torneo Interno de Singles</h1>
			<p>¿Qué querés ver?</p>
			<div class="menu-grid">

				<a href="<?=base_url('resultados')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-trophy"></i>
					</div>
					<div class="menu-card-label">Partidos<br><span style="font-size:10px;opacity:0.7;font-weight:400;text-transform:none;letter-spacing:0">y resultados</span></div>
				</a>


				<a href="<?=base_url('draws')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-sitemap"></i>
					</div>
					<div class="menu-card-label">Draws</div>
				</a>

				<a href="<?=base_url('mipartido')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-edit"></i>
					</div>
					<div class="menu-card-label">Mi Partido</div>
				</a>

			</div>
		</div>
	</div>
</div>

<style>
.menu-grid {
	display: flex;
	justify-content: center;
	gap: 20px;
	flex-wrap: wrap;
	width: 100%;
	padding: 10px;
	background: rgba(0,0,0,.4);
	border-radius: 5px;
}
.menu-card {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	border: 2px solid rgba(165, 208, 81, 0.5);
	border-radius: 12px;
	width: 150px;
	height: 130px;
	text-decoration: none;
	transition: all 0.3s ease;
	flex: 1;
	max-width: 160px;
}
.menu-card:hover, .menu-card:active {
	background: rgba(165, 208, 81, 0.2);
	border-color: #a5d051;
	text-decoration: none;
}
.menu-card-icon {
	font-size: 40px;
	color: #a5d051;
	margin-bottom: 10px;
}
.menu-card-label {
	font-size: 13px;
	font-weight: 700;
	color: #fff;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	text-align: center;
}

@media (max-width: 575.98px) {
	.menu-grid { gap: 6px; flex-wrap: nowrap; padding: 8px; }
	.menu-card { height: 95px; max-width: none; min-width: 0; flex: 1 1 0; width: 0; }
	.menu-card-icon { font-size: 26px; margin-bottom: 5px; }
	.menu-card-label { font-size: 9px; letter-spacing: 0; padding: 0 2px; }
}
</style>

<script type="text/javascript">
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';
</script>
