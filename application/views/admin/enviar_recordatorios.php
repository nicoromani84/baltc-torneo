<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-info text-white">
			<h4 class="mb-0"><i class="fas fa-bell"></i> Enviar Recordatorios a Jugadores</h4>
		</div>
		<div class="card-body">
			<p class="text-muted mb-4">Selecciona un deadline y envía recordatorios a los jugadores que no han programado ni cargado resultado.</p>

			<div class="form-group">
				<label><strong>Seleccionar Deadline:</strong></label>
				<select id="deadline" class="form-control" style="max-width: 400px;">
					<option value="">Cargando deadlines...</option>
				</select>
			</div>

			<div id="partidos-container" style="display:none;" class="mt-4">
				<div class="alert alert-secondary mb-3">
					<i class="fas fa-info-circle"></i> <strong>Partidos sin programar y sin resultado cargado:</strong> Se muestran solo los partidos que aún no tienen fecha acordada Y no tienen resultado cargado.
				</div>
				<h5 class="mb-3">Partidos pendientes para este deadline</h5>
				<div class="table-responsive">
					<table class="table table-sm table-striped">
						<thead class="table-light">
							<tr>
								<th>Categoría</th>
								<th>Ronda</th>
								<th>Jugador 1</th>
								<th>Jugador 2</th>
								<th>Deadline</th>
							</tr>
						</thead>
						<tbody id="partidos-tabla">
						</tbody>
					</table>
				</div>

				<div class="alert alert-warning mt-3">
					<i class="fas fa-info-circle"></i> Se enviarán recordatorios a <strong id="total-jugadores">0</strong> jugadores (ambos de cada pareja)
				</div>

				<button id="btn-enviar" class="btn btn-success btn-lg">
					<i class="fas fa-paper-plane"></i> Enviar Recordatorios
				</button>
				<button id="btn-cancelar" class="btn btn-secondary btn-lg" style="display:none;">
					<i class="fas fa-times"></i> Cancelar
				</button>
			</div>

			<div id="resultado" class="mt-4" style="display:none;"></div>
		</div>
	</div>
</div>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';

	// Cargar deadlines al iniciar
	cargarDeadlines();

	function cargarDeadlines() {
		$.ajax({
			url: baseurl + 'admin/getDeadlinesPendientes',
			type: 'POST',
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action && res.deadlines.length > 0) {
					var html = '<option value="">Elige un deadline...</option>';
					res.deadlines.forEach(function(d) {
						html += '<option value="' + d.deadline + '">' + d.deadline_formato + ' (' + d.partidos + ' partidos)</option>';
					});
					$('#deadline').html(html);
				} else {
					$('#deadline').html('<option value="">No hay deadlines pendientes</option>');
				}
			}
		});
	}

	// Cuando cambia el deadline
	$('#deadline').on('change', function(){
		var deadline = $(this).val();
		if(!deadline) {
			$('#partidos-container').hide();
			$('#resultado').hide();
			return;
		}

		$.ajax({
			url: baseurl + 'admin/getPartidosPorDeadline',
			type: 'POST',
			data: { deadline: deadline },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action && res.partidos.length > 0) {
					var html = '';
					res.partidos.forEach(function(p) {
						html += '<tr>';
						html += '<td>' + p.categoria + '</td>';
						html += '<td>' + p.ronda + '</td>';
						html += '<td>' + (p.jugador1_nombres || 'N/A') + '</td>';
						html += '<td>' + (p.jugador2_nombres || 'N/A') + '</td>';
						html += '<td>' + new Date(p.deadline).toLocaleDateString() + '</td>';
						html += '</tr>';
					});
					$('#partidos-tabla').html(html);
					$('#total-jugadores').text(res.total * 2); // Ambos jugadores de cada pareja
					$('#partidos-container').show();
					$('#resultado').hide();
				} else {
					$('#partidos-container').hide();
					$('#resultado').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay partidos pendientes para este deadline</div>').show();
				}
			}
		});
	});

	// Enviar recordatorios
	$('#btn-enviar').on('click', function(){
		var deadline = $('#deadline').val();
		if(!deadline) {
			alert('Selecciona un deadline');
			return;
		}

		if(!confirm('¿Estás seguro? Se enviarán recordatorios a todos los jugadores de estos partidos.')) {
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');

		$.ajax({
			url: baseurl + 'admin/enviarRecordatoriosPorDeadline',
			type: 'POST',
			data: { deadline: deadline },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Recordatorios');

				if(res.action) {
					$('#resultado').html(
						'<div class="alert alert-success">' +
						'<i class="fas fa-check-circle"></i> <strong>¡Listo!</strong><br>' +
						res.msg +
						'</div>'
					).show();
					$('#partidos-container').hide();

					setTimeout(function(){
						location.reload();
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
				btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar Recordatorios');
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
