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

<div class="page dashboard-page">
	<div class="container" style="padding: 40px 20px;">
		<div style="max-width: 1000px; margin: 0 auto;">
			<h1 style="color: #fff; margin-bottom: 30px; text-align: center;">
				<i class="fas fa-table-tennis"></i> Dashboard Pádel
			</h1>

			<!-- Stats -->
			<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
				<div class="stat-card">
					<div class="stat-label">Total Inscriptos</div>
					<div class="stat-value"><?=$total_inscriptos?></div>
				</div>
				<div class="stat-card">
					<div class="stat-label">Caballeros</div>
					<div class="stat-value" style="color: #5a7a2e;"><?=$caballeros?> parejas</div>
				</div>
				<div class="stat-card">
					<div class="stat-label">Damas</div>
					<div class="stat-value" style="color: #d97706;"><?=$damas?> parejas</div>
				</div>
			</div>

			<!-- Parejas -->
			<div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(165,208,81,0.2); border-radius: 12px; padding: 20px;">
				<h2 style="color: #fff; margin-bottom: 20px;">Parejas Inscriptas</h2>

				<?php if (count($parejas) > 0): ?>
					<div style="overflow-x: auto;">
						<table style="width: 100%; border-collapse: collapse; color: #fff;">
							<thead>
								<tr style="border-bottom: 1px solid rgba(165,208,81,0.3);">
									<th style="padding: 12px; text-align: left; color: #a5d051;">Categoría</th>
									<th style="padding: 12px; text-align: left; color: #a5d051;">Jugador 1</th>
									<th style="padding: 12px; text-align: left; color: #a5d051;">Jugador 2</th>
									<th style="padding: 12px; text-align: left; color: #a5d051;">Fecha</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($parejas as $pareja): ?>
								<tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
									<td style="padding: 12px;">
										<span style="background: <?=($pareja->gender === 'M') ? 'rgba(90,122,46,0.3)' : 'rgba(217,118,6,0.3)'?>; padding: 4px 8px; border-radius: 4px;">
											<?=($pareja->gender === 'M') ? '♂ Caballeros' : '♀ Damas'?>
										</span>
									</td>
									<td style="padding: 12px; text-transform: capitalize;"><?=strtolower($pareja->player1)?></td>
									<td style="padding: 12px; text-transform: capitalize;"><?=strtolower($pareja->player2)?></td>
									<td style="padding: 12px; font-size: 13px; color: rgba(255,255,255,0.6);">
										<?=date('d/m/Y', strtotime($pareja->created_at))?>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div style="text-align: center; padding: 40px 20px; color: rgba(255,255,255,0.5);">
						<i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 10px;"></i>
						<p>No hay inscripciones aún</p>
					</div>
				<?php endif; ?>
			</div>

			<div style="text-align: center; margin-top: 30px;">
				<a href="<?=base_url('padel')?>" class="btn btn-primary" style="padding: 10px 20px;">
					<i class="fas fa-arrow-left"></i> Volver
				</a>
			</div>
		</div>
	</div>
</div>

<style>
.dashboard-page {
	background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
	color: #fff;
	min-height: calc(100vh - 110px);
	padding: 40px 20px;
}
.stat-card {
	background: rgba(0,0,0,0.4);
	border: 1px solid rgba(165,208,81,0.3);
	border-radius: 12px;
	padding: 24px;
	text-align: center;
	transition: all 0.3s ease;
}
.stat-card:hover {
	border-color: rgba(165,208,81,0.6);
	background: rgba(0,0,0,0.6);
}
.stat-label {
	color: rgba(255,255,255,0.6);
	font-size: 13px;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 10px;
}
.stat-value {
	font-size: 32px;
	font-weight: 800;
	color: #a5d051;
}
</style>
