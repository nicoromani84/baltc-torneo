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
				<label>ID Partido</label>
				<input type="number" class="form-control" id="partido_id" value="1667" required>
				<small class="text-muted">Loketek/Sacerdote vs Polla/Pierini (Cuartos de Final, 1ra)</small>
			</div>

			<div class="form-group">
				<label>Ganador (Reservation ID)</label>
				<select class="form-control" id="ganador_id" required>
					<option value="">Seleccionar...</option>
					<option value="238">238 - Loketek, Sebastian / Sacerdote, Diego Raul</option>
					<option value="233">233 - Polla, Damian / Pierini, Alfredo Santiago</option>
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

		<div id="resultado"></div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(function(){
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
		});
	</script>
</body>
</html>
