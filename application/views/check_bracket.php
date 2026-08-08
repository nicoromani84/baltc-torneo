<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Check Bracket</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
	<style>
		body { padding: 20px; background: #f5f5f5; }
		.container { background: white; padding: 20px; border-radius: 8px; max-width: 1000px; }
		h2 { color: #2d6a00; margin-top: 30px; margin-bottom: 15px; border-bottom: 2px solid #a5d051; padding-bottom: 10px; }
		table { font-size: 12px; margin-bottom: 20px; }
		.bye { background: #fff3cd; }
		.con-ganador { background: #d4edda; }
	</style>
</head>
<body>
	<div class="container">
		<h1>📊 Check Bracket Status</h1>

		<h2>✅ Últimos resultados cargados (10)</h2>
		<table class="table table-bordered table-sm">
			<thead class="thead-dark">
				<tr><th>ID</th><th>Categoría</th><th>Ronda</th><th>BP</th><th>J1</th><th>J2</th><th>Ganador</th><th>Score</th></tr>
			</thead>
			<tbody>
				<?php foreach($ultimosResultados as $r): ?>
					<tr class="con-ganador">
						<td><?=$r->id?></td>
						<td><?=$r->categoria?></td>
						<td><?=$r->ronda?></td>
						<td><?=$r->bracket_pos?></td>
						<td><?=$r->jugador1_id?></td>
						<td><?=$r->jugador2_id?></td>
						<td><strong><?=$r->ganador_id?></strong></td>
						<td><?=$r->score?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2>🏆 Cuartos de Final (1ra)</h2>
		<table class="table table-bordered table-sm">
			<thead class="thead-dark">
				<tr><th>ID</th><th>BP</th><th>J1</th><th>J2</th><th>Ganador</th><th>Score</th></tr>
			</thead>
			<tbody>
				<?php foreach($cuartos as $p): ?>
					<tr class="<?= $p->score === 'BYE' ? 'bye' : (!empty($p->ganador_id) ? 'con-ganador' : '') ?>">
						<td><?=$p->id?></td>
						<td><?=$p->bracket_pos?></td>
						<td><?=$p->jugador1_id?></td>
						<td><?=$p->jugador2_id?> <?= $p->jugador2_id === null ? '<span class="badge badge-warning">BYE</span>' : '' ?></td>
						<td><?=$p->ganador_id?></td>
						<td><?=$p->score?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2>🥊 Semifinal (deberían estar aquí)</h2>
		<table class="table table-bordered table-sm">
			<thead class="thead-dark">
				<tr><th>ID</th><th>BP</th><th>J1</th><th>J2</th><th>Ganador</th><th>Score</th></tr>
			</thead>
			<tbody>
				<?php if(empty($semifinal)): ?>
					<tr><td colspan="6" class="text-danger"><strong>❌ No hay partidos</strong></td></tr>
				<?php else: ?>
					<?php foreach($semifinal as $p): ?>
						<tr class="<?= !empty($p->ganador_id) ? 'con-ganador' : '' ?>">
							<td><?=$p->id?></td>
							<td><?=$p->bracket_pos?></td>
							<td><?=$p->jugador1_id?></td>
							<td><?=$p->jugador2_id?></td>
							<td><?=$p->ganador_id?></td>
							<td><?=$p->score?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<?php if(!empty($debug)): ?>
			<h2>🔍 Debug Info</h2>
			<div class="alert alert-info">
				<?php foreach($debug as $line): ?>
					<div><?=$line?></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</body>
</html>
