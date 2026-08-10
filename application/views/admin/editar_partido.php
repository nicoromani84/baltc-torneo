<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-4">
	<div class="card">
		<div class="card-header bg-primary text-white">
			<h4 class="mb-0"><i class="fas fa-edit"></i> Editar Partido Específico</h4>
		</div>
		<div class="card-body">

			<!-- SELECTOR CATEGORIA/GENERO/RONDA -->
			<div class="form-row mb-4">
				<div class="form-group col-md-3">
					<label><strong>Categoría</strong></label>
					<select id="categoria" class="form-control">
						<option value="">Elegir...</option>
						<?php foreach($categories as $c): ?>
						<option value="<?=$c->id?>"><?=$c->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-md-2">
					<label><strong>Género</strong></label>
					<select id="genero" class="form-control">
						<option value="">Elegir...</option>
						<option value="M">Caballeros</option>
						<option value="F">Damas</option>
					</select>
				</div>
				<div class="form-group col-md-3">
					<label><strong>Ronda</strong></label>
					<select id="ronda" class="form-control">
						<option value="">Elegir...</option>
					</select>
				</div>
				<div class="form-group col-md-4">
					<label>&nbsp;</label>
					<button id="btn-cargar-partidos" class="btn btn-primary btn-block">
						<i class="fas fa-list"></i> Cargar Partidos
					</button>
				</div>
			</div>

			<!-- LISTA DE PARTIDOS -->
			<div id="partidos-container" style="display:none">
				<h5 class="mb-3">Partidos</h5>
				<div class="table-responsive">
					<table class="table table-sm table-striped">
						<thead class="table-light">
							<tr>
								<th>ID</th>
								<th>Jugador 1</th>
								<th>Jugador 2</th>
								<th>Score</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody id="partidos-tabla">
						</tbody>
					</table>
				</div>
			</div>

			<!-- FORMULARIO EDICIÓN -->
			<div id="editar-container" style="display:none" class="mt-4 p-4 border rounded" style="background:#f8f9fa">
				<h5 class="mb-4"><i class="fas fa-pencil-alt"></i> Editar Partido #<span id="partido-id-display"></span></h5>

				<div class="form-row mb-3">
					<div class="form-group col-md-6">
						<label><strong>Jugador 1</strong></label>
						<select id="jugador1" class="form-control">
							<option value="">Elegir...</option>
						</select>
					</div>
					<div class="form-group col-md-6">
						<label><strong>Jugador 2</strong></label>
						<select id="jugador2" class="form-control">
							<option value="">Elegir...</option>
						</select>
					</div>
				</div>

				<div class="form-row">
					<div class="form-group col">
						<button id="btn-guardar-cambios" class="btn btn-success">
							<i class="fas fa-check"></i> Guardar Cambios
						</button>
						<button id="btn-cancelar" class="btn btn-secondary">
							<i class="fas fa-times"></i> Cancelar
						</button>
					</div>
				</div>
			</div>

			<div id="mensaje" class="mt-3"></div>

		</div>
	</div>
</div>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var currentPartidoId = null;

	// Cargar rondas cuando cambia categoria/genero
	$('#categoria, #genero').on('change', function() {
		var cat = $('#categoria').val();
		var gen = $('#genero').val();

		if(!cat || !gen) {
			$('#ronda').html('<option value="">Elegir...</option>');
			return;
		}

		$.ajax({
			url: baseurl + 'admin/getRondasByCategory',
			type: 'POST',
			data: { category: cat, gender: gen },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				var html = '<option value="">Elegir...</option>';
				if(res.rondas) {
					res.rondas.forEach(function(r) {
						html += '<option value="' + r + '">' + r + '</option>';
					});
				}
				$('#ronda').html(html);
			}
		});
	});

	// Cargar partidos
	$('#btn-cargar-partidos').on('click', function() {
		var cat = $('#categoria').val();
		var gen = $('#genero').val();
		var ronda = $('#ronda').val();

		if(!cat || !gen || !ronda) {
			alert('Selecciona categoría, género y ronda');
			return;
		}

		$.ajax({
			url: baseurl + 'admin/getPartidosByRonda',
			type: 'POST',
			data: { category: cat, gender: gen, ronda: ronda },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.partidos && res.partidos.length > 0) {
					var html = '';
					res.partidos.forEach(function(p) {
						html += '<tr>';
						html += '<td><strong>' + p.id + '</strong></td>';
						html += '<td>' + (p.jugador1 || 'Por definir') + '</td>';
						html += '<td>' + (p.jugador2 || 'Por definir') + '</td>';
						html += '<td>' + (p.score || '-') + '</td>';
						html += '<td><button class="btn btn-sm btn-warning editar-partido" data-id="' + p.id + '" data-cat="' + cat + '" data-gen="' + gen + '" data-ronda="' + ronda + '">';
						html += '<i class="fas fa-edit"></i> Editar</button></td>';
						html += '</tr>';
					});
					$('#partidos-tabla').html(html);
					$('#partidos-container').show();
				} else {
					alert('No hay partidos en esta ronda');
					$('#partidos-container').hide();
				}
			}
		});
	});

	// Editar partido
	$(document).on('click', '.editar-partido', function() {
		currentPartidoId = $(this).data('id');
		var cat = $(this).data('cat');
		var gen = $(this).data('gen');
		var ronda = $(this).data('ronda');

		$('#partido-id-display').text(currentPartidoId);

		// Cargar jugadores disponibles
		$.ajax({
			url: baseurl + 'admin/getJugadoresByCategory',
			type: 'POST',
			data: { category: cat, gender: gen },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				var html = '<option value="">Elegir...</option>';
				if(res.jugadores) {
					res.jugadores.forEach(function(j) {
						html += '<option value="' + j.id + '">' + j.nombre + '</option>';
					});
				}
				$('#jugador1').html(html);
				$('#jugador2').html(html);

				$('#editar-container').show();
				$('html, body').animate({ scrollTop: $('#editar-container').offset().top - 100 }, 500);
			}
		});
	});

	// Guardar cambios
	$('#btn-guardar-cambios').on('click', function() {
		var j1 = $('#jugador1').val();
		var j2 = $('#jugador2').val();

		if(!j1 || !j2) {
			alert('Selecciona ambos jugadores');
			return;
		}

		$.ajax({
			url: baseurl + 'admin/actualizarPartido',
			type: 'POST',
			data: {
				partido_id: currentPartidoId,
				jugador1_id: j1,
				jugador2_id: j2
			},
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action) {
					$('#mensaje').html('<div class="alert alert-success"><i class="fas fa-check-circle"></i> Partido actualizado correctamente</div>');
					setTimeout(function() {
						$('#btn-cancelar').trigger('click');
						$('#btn-cargar-partidos').trigger('click');
					}, 1000);
				} else {
					$('#mensaje').html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Error: ' + (res.msg || 'Error desconocido') + '</div>');
				}
			}
		});
	});

	// Cancelar
	$('#btn-cancelar').on('click', function() {
		$('#editar-container').hide();
		$('#mensaje').html('');
		currentPartidoId = null;
	});
});
</script>
