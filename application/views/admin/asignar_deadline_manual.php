<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-warning text-dark">
			<h4 class="mb-0"><i class="fas fa-calendar"></i> Asignar Deadline Manual (Sin Emails)</h4>
		</div>
		<div class="card-body">
			<p class="text-muted mb-4">Asigna un deadline a los partidos de una categoría sin enviar correos. Los jugadores verán el deadline en "Mi Partido" y admin verá en "Partidos del Admin".</p>

			<form id="form-deadline">
				<div class="form-row">
					<div class="form-group col-md-3">
						<label><strong>Categoría *</strong></label>
						<select id="categoria" class="form-control" required>
							<option value="">Elegir...</option>
							<?php foreach($categories as $c): ?>
							<option value="<?=$c->id?>"><?=$c->name?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<label><strong>Género *</strong></label>
						<select id="genero" class="form-control" required>
							<option value="">Elegir...</option>
							<option value="M">Caballeros</option>
							<option value="F">Damas</option>
						</select>
					</div>
					<div class="form-group col-md-3">
						<label>Ronda (Opcional)</label>
						<select id="ronda" class="form-control">
							<option value="">Todas las rondas</option>
							<option value="Grupo A">Grupo A</option>
							<option value="Grupo B">Grupo B</option>
							<option value="Grupo C">Grupo C</option>
							<option value="Grupo D">Grupo D</option>
							<option value="1ra Ronda">1ra Ronda</option>
							<option value="2da Ronda">2da Ronda</option>
							<option value="Cuartos de Final">Cuartos de Final</option>
							<option value="Semifinal">Semifinal</option>
							<option value="Final">Final</option>
						</select>
					</div>
					<div class="form-group col-md-4">
						<label><strong>Deadline *</strong></label>
						<input type="date" id="deadline" class="form-control" required>
					</div>
				</div>

				<div class="alert alert-info mt-3">
					<i class="fas fa-info-circle"></i> <strong>Nota:</strong> Se actualizarán todos los partidos pendientes (sin resultado) que coincidan con los criterios. NO se enviarán correos.
				</div>

				<div class="mt-3">
					<button type="submit" class="btn btn-warning btn-lg">
						<i class="fas fa-check"></i> Asignar Deadline
					</button>
					<a href="<?=base_url('admin/draws')?>" class="btn btn-secondary btn-lg">
						<i class="fas fa-times"></i> Cancelar
					</a>
				</div>
			</form>

			<div id="resultado" class="mt-4" style="display:none;"></div>
		</div>
	</div>
</div>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';

	$('#form-deadline').on('submit', function(e){
		e.preventDefault();

		var cat = $('#categoria').val();
		var gen = $('#genero').val();
		var ronda = $('#ronda').val();
		var deadline = $('#deadline').val();

		if(!cat || !gen || !deadline) {
			alert('Completa todos los campos requeridos');
			return;
		}

		var btn = $(this).find('button[type=submit]');
		btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Asignando...');

		$.ajax({
			url: baseurl + 'admin/guardarDeadlineManual',
			type: 'POST',
			data: {
				category: cat,
				gender: gen,
				ronda: ronda,
				deadline: deadline
			},
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				btn.prop('disabled', false).html('<i class="fas fa-check"></i> Asignar Deadline');

				if(res.action) {
					$('#resultado').html(
						'<div class="alert alert-success">' +
						'<i class="fas fa-check-circle"></i> <strong>¡Listo!</strong><br>' +
						'Se actualizaron <strong>' + res.affected + ' partidos</strong> con el deadline ' + deadline +
						'</div>'
					).show();

					// Limpiar formulario después de 2 segundos
					setTimeout(function(){
						$('#form-deadline')[0].reset();
						$('#resultado').fadeOut();
					}, 2000);
				} else {
					$('#resultado').html(
						'<div class="alert alert-danger">' +
						'<i class="fas fa-times-circle"></i> Error: ' + (res.msg || 'Error desconocido') +
						'</div>'
					).show();
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fas fa-check"></i> Asignar Deadline');
				$('#resultado').html(
					'<div class="alert alert-danger">' +
					'<i class="fas fa-times-circle"></i> Error de conexión' +
					'</div>'
				).show();
			}
		});
	});
});
</script>
