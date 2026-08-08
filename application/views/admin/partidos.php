<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="partidos-admin">

	<div class="partidos-header">
		<h2><i class="fas fa-tennis-ball"></i> Gestión de Partidos</h2>
		<?php if(empty($readonly)): ?>
		<div style="display:flex;gap:8px;">
			<button class="btn btn-warning" id="btn-recordatorio">
				<i class="fas fa-calendar"></i> Programar masivamente
			</button>
		</div>
		<?php endif; ?>
	</div>

	<!-- MODAL RECORDATORIO -->
	<div id="modal-recordatorio" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
		<div style="background:#fff; border-radius:8px; padding:30px; max-width:500px; width:90%; max-height:90vh; overflow-y:auto;">
			<h5 style="margin-top:0;"><i class="fas fa-calendar"></i> Enviar programación masiva</h5>

			<!-- PASO 1: Categoría + Género -->
			<div class="form-row">
				<div class="form-group col-md-6">
					<label><span style="background:#a5d051;color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:5px">1</span> Categoría</label>
					<select id="rec-category" class="form-control">
						<option value="">Todas</option>
						<?php foreach($categories as $c): ?>
						<option value="<?=$c->id?>"><?=$c->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label><span style="background:#a5d051;color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:5px">2</span> Género</label>
					<select id="rec-gender" class="form-control">
						<option value="">Todos</option>
						<option value="M">Caballeros</option>
						<option value="F">Damas</option>
					</select>
				</div>
			</div>

			<!-- PASO 2: Ronda (dinámica) -->
			<div class="form-group" id="rec-ronda-wrap" style="display:none">
				<label><span style="background:#a5d051;color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:5px">3</span> Ronda</label>
				<select id="rec-ronda" class="form-control">
					<option value="">Cargando...</option>
				</select>
				<small id="rec-ronda-hint" class="text-muted"></small>
			</div>

			<!-- PASO 3: Fecha deadline (dinámica) -->
			<div class="form-group" id="rec-deadline-wrap" style="display:none">
				<label><span style="background:#a5d051;color:#fff;border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;margin-right:5px">4</span> Fecha límite para jugar</label>
				<input type="date" id="rec-deadline" class="form-control">
			</div>

			<div id="rec-preview" style="display:none; background:#f8f9fa; border-radius:6px; padding:14px; margin-bottom:16px; font-size:13px; color:#495057; border-left:4px solid #a5d051;">
				<div id="rec-preview-text" style="margin-bottom:8px; font-weight:600;"></div>
				<div id="rec-partidos-list"></div>
			</div>
			<div style="display:flex; gap:8px; justify-content:flex-end; margin-top:10px;">
				<button class="btn btn-secondary" id="btn-rec-cancelar">Cancelar</button>
				<button class="btn btn-success" id="btn-rec-enviar" style="display:none"><i class="fas fa-paper-plane"></i> Enviar <span id="rec-count"></span></button>
			</div>
		</div>
	</div>

	<!-- FORM NUEVO/EDITAR PARTIDO -->
	<div id="form-partido" style="display:none">
		<div class="partido-form-card">
			<h4 id="form-titulo">Nuevo Partido</h4>
			<input type="hidden" id="partido-id">

			<div class="form-row">
				<div class="form-group col-md-3">
					<label>Categoría</label>
					<select id="partido-category" class="form-control">
						<option value="">Seleccionar...</option>
						<?php foreach($categories as $c): ?>
						<option value="<?=$c->id?>"><?=$c->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-md-3">
					<label>Género</label>
					<select id="partido-gender" class="form-control">
						<option value="">Seleccionar...</option>
						<option value="M">Caballeros</option>
						<option value="F">Damas</option>
					</select>
				</div>
				<div class="form-group col-md-3">
					<label>Ronda</label>
					<select id="partido-ronda" class="form-control">
						<option value="">Seleccionar...</option>
						<option value="1ra Ronda">1ra Ronda</option>
						<option value="2da Ronda">2da Ronda</option>
						<option value="Cuartos de Final">Cuartos de Final</option>
						<option value="Semifinal">Semifinal</option>
						<option value="Final">Final</option>
					</select>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-5">
					<label>Jugador 1</label>
					<select id="partido-j1" class="form-control">
						<option value="">Seleccionar...</option>
						<?php foreach($inscriptos as $u): ?>
						<option value="<?=$u->jugador_id?>" data-gender="<?=$u->gender?>"><?=strtolower($u->jugador)?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-md-2 text-center" style="padding-top:32px">
					<strong>VS</strong>
				</div>
				<div class="form-group col-md-5">
					<label>Jugador 2</label>
					<select id="partido-j2" class="form-control">
						<option value="">Seleccionar...</option>
						<?php foreach($inscriptos as $u): ?>
						<option value="<?=$u->jugador_id?>" data-gender="<?=$u->gender?>"><?=strtolower($u->jugador)?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-md-3">
					<label>Fecha</label>
					<input type="date" id="partido-fecha" class="form-control">
				</div>
				<div class="form-group col-md-3">
					<label>Hora</label>
					<input type="time" id="partido-hora" class="form-control">
				</div>
			</div>
			<div class="form-row">
				<div class="form-group col-md-6">
					<label>Score</label>
					<div class="score-row">
						<div class="score-par">
							<input type="number" class="score-n score-n1" id="s1a" min="0" max="9" placeholder="0">
							<span class="score-dash">-</span>
							<input type="number" class="score-n score-n1" id="s1b" min="0" max="9" placeholder="0">
						</div>
						<div class="score-par">
							<input type="number" class="score-n score-n1" id="s2a" min="0" max="9" placeholder="0">
							<span class="score-dash">-</span>
							<input type="number" class="score-n score-n1" id="s2b" min="0" max="9" placeholder="0">
						</div>
						<div class="score-par">
							<input type="number" class="score-n score-n2" id="s3a" min="0" max="99" placeholder="–">
							<span class="score-dash">-</span>
							<input type="number" class="score-n score-n2" id="s3b" min="0" max="99" placeholder="–">
						</div>
					</div>
					<input type="hidden" id="partido-score">
				</div>
				<div class="form-group col-md-4">
					<label>Ganador</label>
					<div id="ganador-warning" style="display:none" class="alert alert-warning py-1 px-2 mb-1" style="font-size:12px">
						<i class="fas fa-exclamation-triangle"></i> Asigná un ganador antes de guardar
					</div>
					<select id="partido-ganador" class="form-control">
						<option value="">Sin resultado aún</option>
					</select>
				</div>
			</div>

			<div class="form-group">
				<button class="btn btn-primary" id="btn-guardar-partido">Guardar</button>
				<button class="btn btn-danger" id="btn-wo-partido" type="button">W.O.</button>
				<button class="btn btn-secondary" id="btn-cancelar-partido">Cancelar</button>
			</div>
		</div>
	</div>

	<!-- BUSCADOR -->
	<div class="mb-3">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fas fa-search"></i></span>
			</div>
			<input type="text" id="buscar-partido" class="form-control" placeholder="Buscar jugador en todos los partidos...">
			<div class="input-group-append">
				<button class="btn btn-outline-secondary" id="btn-limpiar-busqueda" style="display:none">
					<i class="fas fa-times"></i> Limpiar
				</button>
			</div>
		</div>
		<div id="busqueda-resultados" style="display:none" class="mt-2">
			<small class="text-muted" id="busqueda-count"></small>
		</div>
	</div>

	<!-- ACCORDEONS POR CATEGORIA + GENERO -->
	<div id="tabla-partidos">
		<?php if(empty($matches)): ?>
		<div class="text-center text-muted" style="padding:40px">
			<i class="fas fa-tennis-ball fa-3x" style="margin-bottom:15px; display:block"></i>
			No hay partidos cargados aún.
		</div>
		<?php else: ?>
		<?php
			// Agrupar por categoria + gender
			$grupos = array();
			foreach($matches as $m) {
				$key = $m->categoria . '||' . ($m->gender ?: '?');
				if(!isset($grupos[$key])) $grupos[$key] = array();
				$grupos[$key][] = $m;
			}
			$acordeon_i = 0;
			foreach($grupos as $key => $partidos):
				list($cat, $gen) = explode('||', $key);
				$genLabel = $gen == 'M' ? 'Caballeros' : ($gen == 'F' ? 'Damas' : '');
				$icono = $gen == 'M' ? 'fa-male' : 'fa-female';
				$color = $gen == 'M' ? '#1a6fad' : '#ad1a6f';
				$acordeon_id = 'acordeon-' . $acordeon_i++;
				$cat_id = $partidos[0]->category; // ID numérico de la categoría
		?>
		<div class="partido-acordeon mb-3" data-cat="<?=$cat_id?>" data-gen="<?=$gen?>" id="acordeon-cat-<?=$cat_id?>-<?=$gen?>">
			<div class="partido-acordeon-header" data-toggle="collapse" data-target="#<?=$acordeon_id?>" data-cat="<?=$cat_id?>" data-gen="<?=$gen?>" style="border-left: 4px solid <?=$color?>">
				<span>
					<i class="fas <?=$icono?>" style="color:<?=$color?>; margin-right:8px"></i>
					<strong><?=$cat?></strong> — <?=$genLabel?>
				</span>
				<span class="badge badge-secondary"><?=count($partidos)?> partidos</span>
				<i class="fas fa-chevron-down acordeon-arrow"></i>
			</div>
			<div id="<?=$acordeon_id?>" class="collapse show">
				<table class="table table-hover mb-0">
					<thead>
						<tr>
							<th>Ronda</th>
							<th>Fecha</th>
							<th>Jugador 1</th>
							<th>Score</th>
							<th>Jugador 2</th>
							<th>Ganador</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($partidos as $m): ?>
					<tr class="partido-row" data-j1="<?=strtolower($m->jugador1)?>" data-j2="<?=strtolower($m->jugador2)?>">
						<td><span class="badge badge-light"><?=$m->ronda?></span></td>
						<td>
							<?php if(!empty($m->fecha) && $m->fecha !== '0000-00-00'): ?>
							<small><?=date('d/m', strtotime($m->fecha))?></small><br>
							<small class="text-muted"><?=!empty($m->hora) ? substr($m->hora,0,5) : ''?></small>
							<?php else: ?><small class="text-muted">—</small><?php endif; ?>
							<?php if(!empty($m->deadline) && !$m->ganador_id): ?>
							<br><span style="display:inline-block;background:#fff3cd;color:#856404;border:1px solid #ffc107;border-radius:4px;font-size:10px;font-weight:600;padding:1px 6px;margin-top:3px;">
								<i class="fas fa-clock"></i> <?=date('d/m', strtotime($m->deadline))?>
							</span>
							<?php endif; ?>
						</td>
						<td class="<?=$m->ganador_id==$m->jugador1_id?'text-success font-weight-bold':''?>">
							<?=strtolower($m->jugador1)?>
						</td>
						<td><strong><?=$m->score ?: '—'?></strong></td>
						<td class="<?=$m->ganador_id==$m->jugador2_id?'text-success font-weight-bold':''?>">
							<?=strtolower($m->jugador2)?>
						</td>
						<td>
							<?=$m->ganador
								? '<span class="text-success"><i class="fas fa-check-circle"></i> '.strtolower($m->ganador).'</span>'
								: '<span class="text-muted">pendiente</span>'?>
						</td>
						<td nowrap>
							<button class="btn btn-sm btn-warning btn-editar"
								data-id="<?=$m->id?>"
								data-category="<?=$m->category?>"
								data-gender="<?=$m->gender?>"
								data-ronda="<?=$m->ronda?>"
								data-j1="<?=$m->jugador1_id?>"
								data-j2="<?=$m->jugador2_id?>"
								data-score="<?=$m->score?>"
								data-ganador="<?=$m->ganador_id?>"
								data-fecha="<?=$m->fecha?>"
								data-hora="<?=$m->hora?>">
								<i class="fas fa-edit"></i>
							</button>
							<button class="btn btn-sm btn-danger btn-borrar" data-id="<?=$m->id?>">
								<i class="fas fa-trash"></i>
							</button>
						</td>
					</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>

</div>

<style>
.partidos-admin { padding: 20px; }
.partidos-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.partidos-header h2 { margin:0; font-size:22px; }
.partido-form-card { background:#f8f9fa; border-radius:8px; padding:20px; margin-bottom:25px; border:1px solid #dee2e6; }
.partido-form-card h4 { margin-bottom:20px; }
.partido-acordeon { border:1px solid #dee2e6; border-radius:8px; overflow:hidden; }
.partido-acordeon-header {
	display: flex; align-items: center; gap: 10px;
	background: #f8f9fa; padding: 12px 16px;
	cursor: pointer; user-select: none;
	font-size: 14px;
}
.partido-acordeon-header:hover { background: #e9ecef; }
.partido-acordeon-header span:first-child { flex: 1; }
.acordeon-arrow { margin-left: 8px; transition: transform 0.2s; color: #6c757d; }
.partido-acordeon-header[aria-expanded="false"] .acordeon-arrow,
.partido-acordeon-header.collapsed .acordeon-arrow { transform: rotate(-90deg); }
.partido-acordeon .table { margin:0; }
.partido-acordeon .table thead th { background: #f1f3f5; font-size:12px; text-transform:uppercase; letter-spacing:0.4px; }
.score-row { display:flex; align-items:center; gap:10px; flex-wrap:nowrap; }
.score-par { display:flex; align-items:center; gap:4px; background:#f8f9fa; border:1px solid #dee2e6; border-radius:6px; padding:4px 8px; }
.score-n { text-align:center; font-weight:700; font-size:15px; border:none; background:transparent; outline:none; -moz-appearance:textfield; padding:2px 0; }
.score-n1 { width:28px; }
.score-n2 { width:38px; }
.score-n::-webkit-outer-spin-button, .score-n::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
.score-dash { font-weight:700; font-size:16px; color:#6c757d; }
.score-par:focus-within { border-color:#a5d051; background:#f0fae0; }
</style>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var defaultCat = '<?=isset($default_cat) ? $default_cat : ''?>';
	var defaultGen = '<?=isset($default_gen) ? $default_gen : ''?>';

	// Si viene de confirmar sorteo, scroll al grupo correspondiente
	if(defaultCat && defaultGen) {
		var $acord = $('#acordeon-cat-'+defaultCat+'-'+defaultGen);
		if($acord.length) {
			$acord.find('.collapse').addClass('show');
			$('html,body').scrollTop($acord.offset().top - 20);
		}
	}

	// Flecha del acordeon
	$('.partido-acordeon-header').on('click', function(){
		$(this).toggleClass('collapsed');
	});

	// Filtrar jugadores por género
	function filtrarJugadores(gender) {
		$('#partido-j1 option, #partido-j2 option').each(function(){
			var og = $(this).data('gender');
			if(!og || !gender || og == gender) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		$('#partido-j1, #partido-j2').val('');
		$('#partido-ganador').html('<option value="">Sin resultado aún</option>');
	}

	$('#partido-gender').on('change', function(){
		filtrarJugadores($(this).val());
	});

	function updateGanadorSelect() {
		var j1id = $('#partido-j1').val();
		var j1name = $('#partido-j1 option:selected').text();
		var j2id = $('#partido-j2').val();
		var j2name = $('#partido-j2 option:selected').text();
		var ganadorVal = $('#partido-ganador').val();
		$('#partido-ganador').html('<option value="">Sin resultado aún</option>');
		if(j1id) $('#partido-ganador').append('<option value="'+j1id+'">'+j1name+'</option>');
		if(j2id) $('#partido-ganador').append('<option value="'+j2id+'">'+j2name+'</option>');
		if(ganadorVal) $('#partido-ganador').val(ganadorVal);
	}

	$('#partido-j1, #partido-j2').on('change', updateGanadorSelect);
	$(document).on('change', '#partido-ganador', function(){
		if($(this).val()) { $('#ganador-warning').hide(); $(this).removeClass('border-warning'); }
	});

	// SCORE: 3 pares fijos
	var scoreOrder = ['s1a','s1b','s2a','s2b','s3a','s3b'];

	function buildScore() {
		var sets = [];
		var pairs = [['s1a','s1b'],['s2a','s2b'],['s3a','s3b']];
		pairs.forEach(function(p) {
			var a = $('#'+p[0]).val();
			var b = $('#'+p[1]).val();
			if(a !== '' && b !== '') sets.push(a + '-' + b);
		});
		$('#partido-score').val(sets.join(', '));
	}

	function parseScoreToFields(score) {
		// Clear all
		scoreOrder.forEach(function(id){ $('#'+id).val(''); });
		if(!score) return;
		var sets = score.split(',').map(function(s){ return s.trim(); });
		sets.forEach(function(set, i) {
			var parts = set.split('-');
			if(i === 0) { $('#s1a').val(parts[0]||''); $('#s1b').val(parts[1]||''); }
			if(i === 1) { $('#s2a').val(parts[0]||''); $('#s2b').val(parts[1]||''); }
			if(i === 2) { $('#s3a').val(parts[0]||''); $('#s3b').val(parts[1]||''); }
		});
	}

	// Backspace: si el campo está vacío, ir al anterior
	$(document).on('keydown', '.score-n', function(e){
		if(e.key === 'Backspace' && $(this).val() === '') {
			var idx = scoreOrder.indexOf($(this).attr('id'));
			if(idx > 0) {
				var $prev = $('#' + scoreOrder[idx-1]);
				$prev.val('').focus().select();
				buildScore();
			}
			e.preventDefault();
		}
	});

	// Auto-avanzar: 1 dígito para sets 1&2, 2 dígitos para tie-break
	$(document).on('input', '.score-n', function(){
		var $inp = $(this);
		var isTiebreak = $inp.hasClass('score-n2');
		var maxLen = isTiebreak ? 2 : 1;
		var val = $inp.val().replace(/[^0-9]/g,'');
		if(val.length > maxLen) val = val.slice(0, maxLen);
		$inp.val(val);
		if(val.length === maxLen) {
			var idx = scoreOrder.indexOf($inp.attr('id'));
			if(idx < scoreOrder.length - 1) {
				$('#' + scoreOrder[idx+1]).focus().select();
			}
		}
		buildScore();
	});

	$(document).on('change', '.score-n', buildScore);

	$('#btn-cancelar-partido').on('click', function(){
		$('#form-partido').slideUp();
	});

	$('#btn-wo-partido').on('click', function(){
		var id = $('#partido-id').val();
		var ganador = $('#partido-ganador').val();
		var ganadorNombre = $('#partido-ganador option:selected').text();
		if(!id) { alert('Primero seleccioná el partido a editar.'); return; }
		if(!ganador) {
			$('#ganador-warning').show();
			$('#partido-ganador').addClass('border-warning').focus();
			return;
		}
		if(!confirm('¿Confirmás W.O. a favor de "' + ganadorNombre + '"?\n\nEsto guardará el resultado como W.O.')) return;
		$.ajax({
			url: baseurl + 'admin/editPartido',
			type: 'POST',
			data: {
				id: id,
				category: $('#partido-category').val(),
				gender: $('#partido-gender').val(),
				ronda: $('#partido-ronda').val(),
				jugador1_id: $('#partido-j1').val(),
				jugador2_id: $('#partido-j2').val(),
				score: 'W.O.',
				ganador_id: ganador,
				fecha: $('#partido-fecha').val(),
				hora: $('#partido-hora').val()
			},
			headers: { 'X-Auth-Token': token },
			success: function(res){
				if(res.action) { location.reload(); }
				else { alert(res.msg || 'Error al guardar.'); }
			}
		});
	});

	$(document).on('click', '.btn-editar', function(){
		var d = $(this).data();
		$('#partido-id').val(d.id);
		$('#form-titulo').text('Editar Partido');
		$('#partido-category').val(d.category);
		$('#partido-gender').val(d.gender);
		filtrarJugadores(d.gender);
		$('#partido-ronda').val(d.ronda);
		$('#partido-j1').val(d.j1);
		$('#partido-j2').val(d.j2);
		parseScoreToFields(d.score);
		$('#partido-fecha').val(d.fecha || '');
		$('#partido-hora').val(d.hora ? d.hora.substring(0,5) : '');
		updateGanadorSelect();
		$('#partido-ganador').val(d.ganador);
		$('#form-partido').slideDown();
		$('html,body').animate({scrollTop: $('#form-partido').offset().top - 80}, 300);
	});

	$('#btn-guardar-partido').on('click', function(){
		var id = $('#partido-id').val();
		var url = id ? baseurl + 'admin/editPartido' : baseurl + 'admin/addPartido';
		buildScore();
		// Validar ganador si hay score
		var score = $('#partido-score').val();
		var ganador = $('#partido-ganador').val();
		if(score && !ganador) {
			$('#ganador-warning').show();
			$('#partido-ganador').addClass('border-warning').focus();
			return;
		}
		$('#ganador-warning').hide();
		$('#partido-ganador').removeClass('border-warning');
		$.ajax({
			url: url,
			type: 'POST',
			data: {
				id: id,
				category: $('#partido-category').val(),
				gender: $('#partido-gender').val(),
				ronda: $('#partido-ronda').val(),
				jugador1_id: $('#partido-j1').val(),
				jugador2_id: $('#partido-j2').val(),
				score: $('#partido-score').val(),
				ganador_id: $('#partido-ganador').val(),
				fecha: $('#partido-fecha').val(),
				hora: $('#partido-hora').val()
			},
			headers: { 'X-Auth-Token': token },
			success: function(res){
				if(res.action) { location.reload(); }
				else { alert(res.msg || 'Error al guardar.'); }
			}
		});
	});

	// BUSCADOR DE PARTIDOS
	$('#buscar-partido').on('input', function(){
		var q = $(this).val().toLowerCase().trim();
		if(!q) {
			$('.partido-row').show();
			$('.partido-acordeon').show();
			$('#busqueda-resultados').hide();
			$('#btn-limpiar-busqueda').hide();
			return;
		}
		$('#btn-limpiar-busqueda').show();
		var found = 0;
		$('.partido-acordeon').each(function(){
			var acordeon = $(this);
			var visible = 0;
			acordeon.find('.partido-row').each(function(){
				var j1 = $(this).data('j1') || '';
				var j2 = $(this).data('j2') || '';
				if(j1.indexOf(q) >= 0 || j2.indexOf(q) >= 0) {
					$(this).show();
					visible++;
					found++;
				} else {
					$(this).hide();
				}
			});
			// Expandir acordeon si tiene resultados
			if(visible > 0) {
				acordeon.show();
				acordeon.find('.collapse').addClass('show');
			} else {
				acordeon.hide();
			}
		});
		$('#busqueda-count').text(found + ' partido(s) encontrado(s)');
		$('#busqueda-resultados').show();
	});

	$('#btn-limpiar-busqueda').on('click', function(){
		$('#buscar-partido').val('').trigger('input');
	});

	// ── RECORDATORIO DEADLINE ──────────────────────────────
	// ── RECORDATORIO DEADLINE ──────────────────────────────
	function recCargarRondas() {
		var cat = $('#rec-category').val();
		var gen = $('#rec-gender').val();
		$('#rec-ronda-wrap').hide();
		$('#rec-deadline-wrap').hide();
		$('#rec-preview').hide();
		$('#btn-rec-enviar').hide();
		$.ajax({
			url: baseurl + 'admin/previewRecordatorio',
			type: 'POST',
			data: { category: cat, gender: gen, ronda: '' },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				var $sel = $('#rec-ronda');
				$sel.html('<option value="">Todas las rondas pendientes (' + res.partidos + ' partidos)</option>');
				if(res.rondas && res.rondas.length) {
					$.each(res.rondas, function(i, r) {
						$sel.append('<option value="'+r.ronda+'">'+r.ronda+' ('+r.pendientes+' pendientes)</option>');
					});
					$('#rec-ronda-hint').text('');
				} else {
					$('#rec-ronda-hint').text('No hay partidos pendientes con estos filtros.');
				}
				$('#rec-ronda-wrap').show();
				// Auto-seleccionar "Todas las rondas pendientes" y mostrar fecha
				$sel.val('').trigger('change');
			}
		});
	}
	function recActualizarPreview() {
		var cat = $('#rec-category').val();
		var gen = $('#rec-gender').val();
		var ronda = $('#rec-ronda').val();
		var deadline = $('#rec-deadline').val();
		$('#rec-preview').hide();
		$('#btn-rec-enviar').hide();
		if(!deadline) return;
		$.ajax({
			url: baseurl + 'admin/previewRecordatorio',
			type: 'POST',
			data: { category: cat, gender: gen, ronda: ronda },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				function apellido(n) {
					if(!n) return '?';
					// Si contiene " / ", es una pareja: mostrar ambos apellidos
					if(n.indexOf(' / ') >= 0) {
						var nombres = n.split(' / ');
						return nombres.map(function(nom) {
							return nom.split(',')[0].trim().toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
						}).join('-');
					}
					// Si no, es un jugador individual
					return n.split(',')[0].trim().toLowerCase().replace(/\b\w/g,function(l){return l.toUpperCase();});
				}
				if(res.total === 0) {
					$('#rec-preview-text').html('<i class="fas fa-exclamation-circle" style="color:#e74c3c"></i> No hay partidos pendientes con esos filtros.');
					$('#rec-partidos-list').html('');
					$('#btn-rec-enviar').hide();
				} else {
					$('#rec-preview-text').html('<i class="fas fa-paper-plane" style="color:#a5d051"></i> <strong>' + res.partidos + ' partido(s)</strong> — <strong>' + res.total + ' jugador(es)</strong> recibirán el mail:');
					$('#rec-count').text('(' + res.total + ')');
					var html = '';
					if(res.partidos_lista && res.partidos_lista.length) {
						res.partidos_lista.forEach(function(p) {
							html += '<div style="padding:3px 0; border-bottom:1px solid #e9ecef; font-size:12px;">'
								+ '<span style="font-weight:700; color:#2d6a00;">' + apellido(p.j1) + '</span>'
								+ ' <span style="color:#aaa; font-size:10px;">vs</span> '
								+ '<span style="font-weight:700; color:#2d6a00;">' + apellido(p.j2) + '</span>'
								+ '</div>';
						});
					}
					$('#rec-partidos-list').html(html);
					$('#btn-rec-enviar').show();
				}
				$('#rec-preview').show();
			}
		});
	}
	$('#btn-recordatorio').on('click', function(){
		$('#rec-preview').hide();
		$('#btn-rec-enviar').hide();
		$('#rec-deadline').val('');
		$('#rec-category, #rec-gender').val('');
		$('#rec-ronda-wrap, #rec-deadline-wrap').hide();
		$('#modal-recordatorio').css('display','flex');
	});
	$('#btn-rec-cancelar').on('click', function(){
		$('#modal-recordatorio').hide();
	});
	$('#modal-recordatorio').on('click', function(e){
		if($(e.target).is('#modal-recordatorio')) $(this).hide();
	});
	$('#rec-category, #rec-gender').on('change', function(){
		recCargarRondas();
	});
	$('#rec-ronda').on('change', function(){
		$('#rec-deadline-wrap').show();
		recActualizarPreview();
	});
	$('#rec-deadline').on('change', function(){
		recActualizarPreview();
	});
	$('#btn-rec-enviar').on('click', function(){
		var deadline = $('#rec-deadline').val();
		if(!deadline){ alert('Seleccioná una fecha límite.'); return; }
		$(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
		$.ajax({
			url: baseurl + 'admin/enviarRecordatorio',
			type: 'POST',
			data: {
				deadline: deadline,
				category: $('#rec-category').val(),
				gender: $('#rec-gender').val(),
				ronda: $('#rec-ronda').val()
			},
			headers: { 'X-Auth-Token': token },
			success: function(res){
				$('#modal-recordatorio').hide();
				$('#btn-rec-enviar').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Enviar <span id="rec-count"></span>');
				if(res.action) alert('✅ Recordatorios enviados a ' + res.enviados + ' jugador(es).');
				else alert('Error al enviar: ' + (res.msg || 'desconocido'));
			}
		});
	});

	$(document).on('click', '.btn-borrar', function(){
		if(!confirm('¿Seguro que querés borrar este partido?')) return;
		var id = $(this).data('id');
		$.ajax({
			url: baseurl + 'admin/deletePartido',
			type: 'POST',
			data: { id: id },
			headers: { 'X-Auth-Token': token },
			success: function(res){ if(res.action) location.reload(); }
		});
	});
});
</script>
