<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Test: Cargar Resultado</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
	<style>
		body { padding: 30px; background: #f5f5f5; }
		.container { max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
		h2 { color: #2d6a00; margin-bottom: 30px; }
		.form-group label { font-weight: bold; color: #333; }
		.btn { background: #a5d051; border: none; color: white; font-weight: bold; }
		.btn:hover { background: #8fb840; }
		.alert { margin-top: 20px; }
	</style>
</head>
<body>
	<div class="container">
		<h2>🧪 Test: Cargar Resultado</h2>

		<form id="form-test">
			<div class="form-group">
				<label>Partido</label>
				<select class="form-control" id="partido_select" required>
					<option value="">Seleccionar partido...</option>
					<?php foreach($partidos as $p): ?>
						<option value="<?=$p->id?>" data-j1="<?=$p->jugador1_id?>" data-j2="<?=$p->jugador2_id?>" data-j1-name="<?=htmlspecialchars($p->j1_name)?>" data-j2-name="<?=htmlspecialchars($p->j2_name)?>">
							<?=$p->categoria?> - <?=$p->ronda?> - <?=$p->j1_name?> vs <?=$p->j2_name?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<input type="hidden" id="partido_id">

			<div class="form-group">
				<label>Ganador</label>
				<select class="form-control" id="ganador_id" required disabled>
					<option value="">Seleccionar ganador...</option>
				</select>
			</div>

			<div class="form-group">
				<label>Score</label>
				<input type="text" class="form-control" id="score" value="6-4, 6-3" placeholder="6-4, 6-3" required>
			</div>

			<div class="form-group">
				<label>Email de Prueba</label>
				<input type="email" class="form-control" id="test_email" value="nicolas.romani09@gmail.com" required>
			</div>

			<button type="submit" class="btn btn-lg btn-block">Cargar Resultado</button>
		</form>

		<button class="btn btn-warning btn-lg btn-block" id="btn-revertir" style="margin-top: 10px;">Revertir Resultado</button>

		<div id="resultado"></div>

		<hr>
		<h3>🔍 Debug: Todos los partidos (1ra Ronda + Cuartos)</h3>
		<table class="table table-sm table-bordered">
			<thead class="thead-dark"><tr><th>ID</th><th>Ronda</th><th>J1</th><th>J2</th><th>Score</th><th>Ganador</th></tr></thead>
			<tbody>
				<?php foreach($todos as $p): ?>
					<tr class="<?= empty($p->ganador_id) && empty($p->score) ? 'table-success' : 'table-danger' ?>">
						<td><?=$p->id?></td>
						<td><?=$p->ronda?></td>
						<td><?=$p->jugador1_id?></td>
						<td><?=$p->jugador2_id?></td>
						<td><?=$p->score?></td>
						<td><?=$p->ganador_id?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(function(){
			// Cuando selecciona un partido, actualizar opciones de ganador
			$('#partido_select').on('change', function(){
				var $opt = $(this).find(':selected');
				var partido_id = $opt.val();
				var j1 = $opt.data('j1');
				var j2 = $opt.data('j2');
				var j1_name = $opt.data('j1-name');
				var j2_name = $opt.data('j2-name');

				$('#partido_id').val(partido_id);
				var html = '<option value="">Seleccionar ganador...</option>';
				if(j1) html += '<option value="' + j1 + '">' + j1_name + '</option>';
				if(j2) html += '<option value="' + j2 + '">' + j2_name + '</option>';
				$('#ganador_id').html(html).prop('disabled', !partido_id);
			});

			$('#form-test').on('submit', function(e){
				e.preventDefault();
				var btn = $(this).find('button[type=submit]');
				btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cargando...');

				$.ajax({
					url: '<?=base_url('admin/testCargarResultado')?>',
					type: 'POST',
					data: {
						id: $('#partido_id').val(),
						ganador_id: $('#ganador_id').val(),
						score: $('#score').val(),
						test_email: $('#test_email').val()
					},
					headers: { 'X-Auth-Token': '<?=$token?>' },
					success: function(res){
						var html = '<div class="alert ' + (res.action ? 'alert-success' : 'alert-danger') + '">';
						html += '<strong>' + (res.action ? '✅ Éxito' : '❌ Error') + '</strong><br>';
						html += res.msg || 'Operación completada';
						html += '</div>';
						$('#resultado').html(html);
						btn.prop('disabled', false).html('Cargar Resultado');
					},
					error: function(){
						$('#resultado').html('<div class="alert alert-danger"><strong>Error</strong><br>No se pudo conectar al servidor</div>');
						btn.prop('disabled', false).html('Cargar Resultado');
					}
				});
			});

			$('#btn-revertir').on('click', function(){
				if(!confirm('¿Revertir resultado del partido?')) return;
				var btn = $(this);
				btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Revirtiendo...');

				$.ajax({
					url: '<?=base_url('admin/revertirResultado')?>',
					type: 'POST',
					data: { id: $('#partido_id').val() },
					headers: { 'X-Auth-Token': '<?=$token?>' },
					success: function(res){
						var html = '<div class="alert ' + (res.action ? 'alert-info' : 'alert-danger') + '">';
						html += '<strong>' + (res.action ? '↩️ Revertido' : '❌ Error') + '</strong><br>';
						html += res.msg || 'Operación completada';
						html += '</div>';
						$('#resultado').html(html);
						btn.prop('disabled', false).html('Revertir Resultado');
					},
					error: function(){
						$('#resultado').html('<div class="alert alert-danger"><strong>Error</strong><br>No se pudo conectar al servidor</div>');
						btn.prop('disabled', false).html('Revertir Resultado');
					}
				});
			});
		});
	</script>
</body>
</html>
