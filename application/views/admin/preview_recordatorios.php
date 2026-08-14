<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>
#preview-recordatorios {
	padding: 20px;
	background: #1a1a2e;
	color: #fff;
}

.recordatorio-header {
	font-size: 24px;
	font-weight: 700;
	margin-bottom: 20px;
	color: #a5d051;
}

.filter-section {
	background: rgba(255,255,255,0.05);
	padding: 15px;
	border-radius: 8px;
	margin-bottom: 20px;
	border: 1px solid rgba(165,208,81,0.2);
}

.filter-section label {
	display: block;
	margin-bottom: 8px;
	font-weight: 600;
	font-size: 14px;
}

.filter-section input {
	width: 100%;
	max-width: 300px;
	padding: 8px 12px;
	background: rgba(255,255,255,0.1);
	border: 1px solid #a5d051;
	border-radius: 6px;
	color: #fff;
	font-size: 14px;
}

.filter-section button {
	margin-top: 10px;
	padding: 8px 20px;
	background: #a5d051;
	color: #1a1a2e;
	border: none;
	border-radius: 6px;
	font-weight: 700;
	cursor: pointer;
}

.filter-section button:hover {
	background: #b8e66b;
}

.resultado-vacio {
	text-align: center;
	padding: 40px;
	color: rgba(255,255,255,0.5);
}

.destinatarios-list {
	display: grid;
	gap: 15px;
}

.destinatario-card {
	background: rgba(255,255,255,0.05);
	border: 1px solid rgba(165,208,81,0.3);
	border-radius: 8px;
	padding: 15px;
	border-left: 4px solid #a5d051;
}

.destinatario-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 10px;
	font-weight: 600;
}

.destinatario-nombre {
	color: #a5d051;
	font-size: 15px;
}

.destinatario-email {
	color: rgba(255,255,255,0.6);
	font-size: 12px;
	font-family: monospace;
}

.destinatario-detalles {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 10px;
	margin-top: 10px;
	font-size: 13px;
}

.destinatario-detalles div {
	padding: 8px;
	background: rgba(0,0,0,0.3);
	border-radius: 4px;
}

.destinatario-label {
	color: rgba(255,255,255,0.5);
	font-weight: 600;
	font-size: 11px;
	text-transform: uppercase;
}

.destinatario-valor {
	color: #fff;
	margin-top: 4px;
}

.stats {
	background: rgba(165,208,81,0.1);
	border: 1px solid rgba(165,208,81,0.3);
	padding: 15px;
	border-radius: 8px;
	margin-bottom: 20px;
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
	gap: 15px;
}

.stat-item {
	text-align: center;
}

.stat-number {
	font-size: 28px;
	font-weight: 700;
	color: #a5d051;
}

.stat-label {
	font-size: 12px;
	color: rgba(255,255,255,0.6);
	margin-top: 5px;
}

.loading {
	text-align: center;
	padding: 40px;
	color: #a5d051;
}
</style>

<div id="preview-recordatorios">
	<div class="recordatorio-header">
		📧 Vista Previa de Recordatorios
	</div>

	<div class="filter-section">
		<label>Deadline (formato YYYY-MM-DD):</label>
		<input type="text" id="deadline-input" placeholder="2026-08-18" value="2026-08-18">
		<button onclick="cargarRecordatorios()">🔍 Buscar Destinatarios</button>
	</div>

	<div id="stats" class="stats" style="display:none;">
		<div class="stat-item">
			<div class="stat-number" id="total-count">0</div>
			<div class="stat-label">Destinatarios</div>
		</div>
		<div class="stat-item">
			<div class="stat-number" id="partidos-count">0</div>
			<div class="stat-label">Partidos</div>
		</div>
	</div>

	<div id="resultado" class="resultado-vacio">
		Ingresá una fecha y hacé click en "Buscar"
	</div>
</div>

<script>
function cargarRecordatorios() {
	var deadline = $('#deadline-input').val();
	if(!deadline) {
		alert('Ingresá una fecha');
		return;
	}

	$('#resultado').html('<div class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');

	$.ajax({
		url: '<?=base_url()?>admin/listarDestinatariosDeadline',
		type: 'POST',
		data: { deadline: deadline },
		headers: { 'X-Auth-Token': '<?=$token?>' },
		dataType: 'json',
		success: function(res) {
			if(res.action && res.destinatarios) {
				mostrarDestinatairos(res.destinatarios, deadline);
			} else {
				$('#resultado').html('<div class="resultado-vacio">No hay recordatorios para esta fecha</div>');
			}
		},
		error: function(xhr, status, err) {
			$('#resultado').html('<div class="resultado-vacio">Error: ' + err + '</div>');
		}
	});
}

function mostrarDestinatairos(destinatarios, deadline) {
	if(!destinatarios || destinatarios.length === 0) {
		$('#resultado').html('<div class="resultado-vacio">No hay partidos pendientes para esta fecha</div>');
		$('#stats').hide();
		return;
	}

	var partidos_unicos = {};
	destinatarios.forEach(function(d) {
		partidos_unicos[d.partido_id] = true;
	});

	$('#total-count').text(destinatarios.length);
	$('#partidos-count').text(Object.keys(partidos_unicos).length);
	$('#stats').show();

	var html = '<div class="destinatarios-list">';
	destinatarios.forEach(function(d, idx) {
		html += '<div class="destinatario-card">';
		html += '<div class="destinatario-header">';
		html += '<div class="destinatario-nombre">' + (idx+1) + '. ' + d.nombre + '</div>';
		html += '</div>';
		html += '<div class="destinatario-email">' + d.email + '</div>';
		html += '<div class="destinatario-detalles">';
		html += '<div><div class="destinatario-label">Categoría</div><div class="destinatario-valor">' + d.categoria + '</div></div>';
		html += '<div><div class="destinatario-label">Ronda</div><div class="destinatario-valor">' + d.ronda + '</div></div>';
		html += '<div><div class="destinatario-label">Mi Pareja</div><div class="destinatario-valor">' + d.pareja + '</div></div>';
		html += '<div><div class="destinatario-label">Rival</div><div class="destinatario-valor">' + d.rival + '</div></div>';
		html += '</div>';
		html += '</div>';
	});
	html += '</div>';

	$('#resultado').html(html);
}

// Auto-cargar al abrir
$(function() {
	cargarRecordatorios();
});
</script>
