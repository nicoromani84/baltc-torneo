<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container mt-5">
	<div class="card">
		<div class="card-header bg-info text-white">
			<h4 class="mb-0"><i class="fas fa-search"></i> Buscar Partido</h4>
		</div>
		<div class="card-body">
			<form id="buscarForm">
				<div class="form-group">
					<label>Ronda:</label>
					<input type="text" id="ronda" class="form-control" placeholder="ej: Grupo B" required>
				</div>
				<div class="form-group">
					<label>Fecha (YYYY-MM-DD):</label>
					<input type="text" id="fecha" class="form-control" placeholder="ej: 2026-08-26" required>
				</div>
				<div class="form-group">
					<label>Deadline (YYYY-MM-DD):</label>
					<input type="text" id="deadline" class="form-control" placeholder="ej: 2026-09-01" required>
				</div>
				<button type="button" class="btn btn-primary" onclick="buscarPartido()">
					<i class="fas fa-search"></i> Buscar
				</button>
			</form>

			<div id="resultado" style="margin-top: 30px; display: none;">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead class="table-light">
							<tr>
								<th>ID</th>
								<th>Ronda</th>
								<th>Fecha/Hora</th>
								<th>Deadline</th>
								<th>Jugador 1</th>
								<th>Jugador 2</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody id="tablaPartidos"></tbody>
					</table>
				</div>
			</div>

			<div id="sin-resultados" class="alert alert-info" style="display: none; margin-top: 20px;">
				No se encontraron partidos con esos datos
			</div>
		</div>
	</div>
</div>

<script>
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';

function buscarPartido() {
	var ronda = document.getElementById('ronda').value;
	var fecha = document.getElementById('fecha').value;
	var deadline = document.getElementById('deadline').value;

	if(!ronda || !fecha || !deadline) {
		alert('Completá todos los campos');
		return;
	}

	fetch(baseurl + 'admin/buscarPartido', {
		method: 'POST',
		headers: {
			'X-Auth-Token': token,
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: 'ronda=' + encodeURIComponent(ronda) + '&fecha=' + encodeURIComponent(fecha) + '&deadline=' + encodeURIComponent(deadline)
	})
	.then(r => r.json())
	.then(data => {
		if(data.action && data.partidos.length > 0) {
			var html = '';
			data.partidos.forEach(function(p) {
				html += '<tr>';
				html += '<td><strong>' + p.id + '</strong></td>';
				html += '<td>' + p.ronda + '</td>';
				html += '<td>' + (p.fecha || '-') + ' ' + (p.hora || '') + '</td>';
				html += '<td>' + p.deadline + '</td>';
				html += '<td>' + (p.j1 || '-') + '</td>';
				html += '<td>' + (p.j2 || '-') + '</td>';
				html += '<td><button class="btn btn-sm btn-success" onclick="actualizarDeadline(' + p.id + ')">Cambiar Deadline</button></td>';
				html += '</tr>';
			});
			document.getElementById('tablaPartidos').innerHTML = html;
			document.getElementById('resultado').style.display = 'block';
			document.getElementById('sin-resultados').style.display = 'none';
		} else {
			document.getElementById('resultado').style.display = 'none';
			document.getElementById('sin-resultados').style.display = 'block';
		}
	})
	.catch(err => console.error('Error:', err));
}

function actualizarDeadline(partidoId) {
	var nuevoDeadline = prompt('Ingresá el nuevo deadline (YYYY-MM-DD):', '2026-09-02');
	if(!nuevoDeadline) return;

	fetch(baseurl + 'admin/actualizarDeadlinePartido', {
		method: 'POST',
		headers: {
			'X-Auth-Token': token,
			'Content-Type': 'application/x-www-form-urlencoded'
		},
		body: 'partido_id=' + partidoId + '&deadline=' + encodeURIComponent(nuevoDeadline)
	})
	.then(r => r.json())
	.then(data => {
		if(data.action) {
			alert(data.msg);
			buscarPartido(); // Recargar
		} else {
			alert('Error: ' + data.msg);
		}
	})
	.catch(err => alert('Error: ' + err));
}
</script>
