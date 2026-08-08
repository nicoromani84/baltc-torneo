<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Debug Bracket</title>
	<style>
		body { font-family: monospace; padding: 20px; background: #f5f5f5; }
		h2 { color: #333; border-bottom: 2px solid #a5d051; padding-bottom: 5px; }
		table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
		td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
		th { background: #a5d051; color: white; font-weight: bold; }
		tr:hover { background: #f9f9f9; }
		.empty { color: #999; font-style: italic; }
	</style>
</head>
<body>
	<h1>🔍 Debug Bracket</h1>

	<h2>1. Categorías con "1ra"</h2>
	<?php if(!empty($categorias)): ?>
		<table>
			<tr><th>ID</th><th>Name</th></tr>
			<?php foreach($categorias as $c): ?>
				<tr><td><?=$c->id?></td><td><?=$c->name?></td></tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p class="empty">Vacío</p>
	<?php endif; ?>

	<h2>2. Todos los Cuartos de Final</h2>
	<?php if(!empty($cuartos_final)): ?>
		<table>
			<tr><th>ID</th><th>J1</th><th>J2</th><th>Categoría</th><th>Gender</th></tr>
			<?php foreach($cuartos_final as $m): ?>
				<tr>
					<td><?=$m->id?></td>
					<td><?=$m->jugador1_id?></td>
					<td><?=$m->jugador2_id?></td>
					<td><?=$m->name?></td>
					<td><?=$m->gender?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p class="empty">Vacío</p>
	<?php endif; ?>

	<h2>3. Todos los partidos con Pierini</h2>
	<?php if(!empty($pierini)): ?>
		<table>
			<tr><th>ID</th><th>J1</th><th>J2</th><th>Ronda</th><th>Categoría</th></tr>
			<?php foreach($pierini as $m): ?>
				<tr <?php if($m->jugador1_id == $m->jugador2_id) echo 'style="background: #ffcccc;"'; ?>>
					<td><?=$m->id?></td>
					<td><?=$m->jugador1_id?></td>
					<td><?=$m->jugador2_id?></td>
					<td><?=$m->ronda?></td>
					<td><?=$m->name?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p class="empty">Vacío</p>
	<?php endif; ?>

	<h2>4. Partners en cada Reservation (Dupcheck)</h2>
	<?php if(!empty($dupcheck)): ?>
		<table>
			<tr><th>Reservation ID</th><th>Partners</th></tr>
			<?php foreach($dupcheck as $r): ?>
				<tr>
					<td><?=$r->id?></td>
					<td><?=$r->partners ?: '<em style="color:#999">Vacío</em>'?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p class="empty">Vacío</p>
	<?php endif; ?>

	<p style="margin-top: 30px; color: #666; font-size: 12px;">
		<strong>Nota:</strong> Si ves alguna fila resaltada en rojo, significa que jugador1_id = jugador2_id (mismo equipo en ambos lados).
	</p>
</body>
</html>
