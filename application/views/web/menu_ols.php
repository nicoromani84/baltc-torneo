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

<div class="page menu-page">
	<div id="menu-torneo" class="container">
		<div class="container">
			<h1>Hola <strong><?=$user->name?></strong><br>Torneo Interno de Singles</h1>
			<p>¿Qué querés ver?</p>
			<div class="menu-grid">

				<a href="<?=base_url('resultados')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-trophy"></i>
					</div>
					<div class="menu-card-label">Resultados</div>
				</a>

				<a href="<?=base_url('programacion')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-calendar-alt"></i>
					</div>
					<div class="menu-card-label">Programación</div>
				</a>

				<a href="<?=base_url('draws')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-sitemap"></i>
					</div>
					<div class="menu-card-label">Draws</div>
				</a>

			</div>
		</div>
	</div>
</div>

<style>
#menu-page {
	background: url('<?=asset_url('img')?>/bg.jpg') no-repeat center center;
	background-size: cover;
}
.menu-page {
	display: table;
	width: 100%;
	height: 100%;
	min-height: calc(100vh - 110px);
}
#menu-torneo {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: middle;
	height: 100%;
}
#menu-torneo h1 {
	text-align: center;
	color: #fff;
	margin: 0 0 10px 0;
	text-transform: uppercase;
	font-weight: 700;
	font-size: 38px;
	text-shadow: 4px 4px 12px rgba(0,0,0,0.9);
}
#menu-torneo h1 strong {
	color: #a5d051;
}
#menu-torneo > .container > p {
	font-weight: 400;
	margin: 0 0 35px 0;
	font-size: 20px;
	text-shadow: 2px 2px 6px rgba(0,0,0,0.8);
	color: #fff;
	text-align: center;
}
.menu-grid {
	display: flex;
	justify-content: center;
	gap: 20px;
	flex-wrap: wrap;
}
.menu-card {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	background: rgba(0, 0, 0, 0.45);
	border: 2px solid rgba(165, 208, 81, 0.4);
	border-radius: 16px;
	width: 160px;
	height: 160px;
	text-decoration: none;
	transition: all 0.3s ease;
	cursor: pointer;
}
.menu-card:hover {
	background: rgba(165, 208, 81, 0.25);
	border-color: #a5d051;
	transform: translateY(-4px);
	box-shadow: 0 10px 30px rgba(0,0,0,0.4);
	text-decoration: none;
}
.menu-card-icon {
	font-size: 48px;
	color: #a5d051;
	margin-bottom: 14px;
	transition: all 0.3s ease;
}
.menu-card:hover .menu-card-icon {
	color: #fff;
}
.menu-card-label {
	font-size: 15px;
	font-weight: 700;
	color: #fff;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
}

@media (max-width: 575.98px) {
	#menu-torneo h1 { font-size: 24px; }
	#menu-torneo h1 strong { display: block; }
	.menu-grid { gap: 15px; }
	.menu-card { width: 140px; height: 140px; }
	.menu-card-icon { font-size: 40px; }
	.menu-card-label { font-size: 13px; }
}
</style>

<script type="text/javascript">
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';
</script>
