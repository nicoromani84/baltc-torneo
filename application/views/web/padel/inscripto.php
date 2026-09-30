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
	<div id="inscripto-page" class="container">
		<div class="container">
			<div class="inscripto-card">
				<div class="inscripto-check">
					<i class="fas fa-check-circle"></i>
				</div>
				<h2 class="inscripto-titulo">¡Anotado en Pádel!</h2>
				<div class="inscripto-nombre"><?=strtolower($user->name)?></div>

				<div class="inscripto-detalle">
					<div class="inscripto-item">
						<i class="fas fa-star"></i>
						<span>Categoría <strong><?=isset($categoria) ? $categoria : 'N/A'?></strong></span>
					</div>
					<?php if(isset($partner_name)): ?>
					<div class="inscripto-item">
						<i class="fas fa-user-friends"></i>
						<span>Compañero <strong style="text-transform:capitalize;"><?=$partner_name?></strong></span>
					</div>
					<?php endif; ?>
					<div class="inscripto-item">
						<i class="fas fa-calendar-alt"></i>
						<span>Inauguración: <strong>10 de Octubre • 17hs</strong></span>
					</div>
				</div>

				<a href="<?=base_url('padel/dashboard')?>" class="btn btn-outline-light btn-sm inscripto-reglamento" style="margin-top: 12px;">
					<i class="fas fa-chart-bar"></i> Ver Dashboard
				</a>
			</div>
		</div>
	</div>
</div>

<style>
#inscripto-page {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	padding: 30px 0;
}
.inscripto-card {
	background: rgba(0,0,0,0.55);
	border: 2px solid #ff6b35;
	border-radius: 20px;
	padding: 40px 30px;
	text-align: center;
	max-width: 420px;
	margin: 0 auto;
	box-shadow: 0 0 20px rgba(255, 107, 53, 0.2);
}
.inscripto-check {
	font-size: 70px;
	color: #ff6b35;
	margin-bottom: 16px;
	animation: popIn 0.5s ease;
}
@keyframes popIn {
	0% { transform: scale(0); opacity: 0; }
	70% { transform: scale(1.15); }
	100% { transform: scale(1); opacity: 1; }
}
.inscripto-titulo {
	color: #fff;
	font-size: 26px;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 6px;
	text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
}
.inscripto-nombre {
	color: #ffd700;
	font-size: 18px;
	font-weight: 700;
	text-transform: capitalize;
	margin-bottom: 24px;
}
.inscripto-detalle {
	background: rgba(255,255,255,0.05);
	border-radius: 12px;
	padding: 16px;
	margin-bottom: 24px;
	text-align: left;
}
.inscripto-item {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	padding: 8px 0;
	border-bottom: 1px solid rgba(255,255,255,0.07);
	color: rgba(255,255,255,0.85);
	font-size: 15px;
}
.inscripto-item:last-child { border-bottom: none; }
.inscripto-item i {
	color: #ff6b35;
	font-size: 16px;
	margin-top: 2px;
	flex-shrink: 0;
}
.inscripto-item strong { color: #fff; }
.inscripto-reglamento {
	width: 100%;
	border-color: #ff6b35 !important;
	color: #ff6b35 !important;
	font-size: 13px !important;
	background: transparent !important;
}
.inscripto-reglamento:hover {
	background: rgba(255, 107, 53, 0.1) !important;
	color: #ffd700 !important;
	border-color: #ffd700 !important;
}
@media (max-width: 575.98px) {
	.inscripto-card { padding: 30px 20px; }
	.inscripto-titulo { font-size: 22px; }
	.inscripto-check { font-size: 60px; }
}
</style>
