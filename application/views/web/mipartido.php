<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<header>
	<div class="container">
		<div class="logo">
			<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
		</div>
		<ul class="buttons">
			<li>
				<a href="<?=base_url('menu')?>">
					<span class="icon-logout"><i class="fas fa-arrow-left"></i></span>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="page reserve-page">
	<div id="mipartido-page" class="container">
		<div class="container">
			<h1>Mi Partido</h1>
			<p>Torneo Interno de Dobles</p>

			<?php if(empty($partidos)): ?>
			<div class="resultado-vacio">
				<i class="fas fa-check-circle"></i>
				<p>No tenés partidos pendientes por cargar.</p>
			</div>
			<?php else: ?>

			<?php foreach($partidos as $p): ?>
			<div class="mipartido-card">
				<div class="mipartido-ronda"><?=$p->ronda?> — <?=$p->categoria?></div>
				<?php if(!empty($p->deadline)): ?>
				<div class="mipartido-deadline"><i class="fas fa-clock"></i> Jugarlo antes del <strong><?=date('d/m/Y', strtotime($p->deadline))?></strong></div>
				<div class="mipartido-fecha-wrap" id="fecha-wrap-<?=$p->id?>">
					<?php if(!empty($p->fecha)): ?>
					<div class="mipartido-fecha-acordada" id="fecha-acordada-<?=$p->id?>">
						<i class="fas fa-calendar-check"></i>
						<span>Acordado: <strong><?=date('d/m/Y', strtotime($p->fecha))?><?=!empty($p->hora) ? ' a las ' . date('H:i', strtotime($p->hora)) : ''?></strong></span>
						<button class="mipartido-fecha-edit" data-id="<?=$p->id?>"><i class="fas fa-pencil-alt"></i></button>
					</div>
					<?php else: ?>
					<button class="mipartido-fecha-trigger" data-id="<?=$p->id?>">
						<i class="fas fa-calendar-plus"></i> ¿Ya coordinaste con tu rival? Agrega la fecha acá
					</button>
					<?php endif; ?>
					<div class="mipartido-fecha-form" id="fecha-form-<?=$p->id?>" style="display:none"
						data-deadline="<?=$p->deadline?>"
						data-id="<?=$p->id?>">
						<div class="mipartido-fecha-hint"><i class="fas fa-pencil-alt"></i> Tocá los campos para cambiar fecha u hora</div>
						<div class="mipartido-fecha-inputs">
							<input type="date" class="fecha-input-date" max="<?=$p->deadline?>">
							<input type="time" class="fecha-input-time" value="10:00">
						</div>
						<div class="mipartido-fecha-confirm" id="fecha-confirm-<?=$p->id?>" style="display:none">
							<span class="fecha-confirm-label"></span>
							<div class="mipartido-fecha-btns">
								<button class="btn-fecha-ok"><i class="fas fa-check"></i> Confirmar</button>
								<button class="btn-fecha-cancel"><i class="fas fa-times"></i></button>
							</div>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="mipartido-vs">
					<div class="mipartido-jugador yo">
						<div class="mipartido-nombre"><?=strtolower($p->yo)?></div>
						<small>vos</small>
					</div>
					<div class="mipartido-separador">vs</div>
					<div class="mipartido-jugador rival">
						<div class="mipartido-nombre">
							<?php if(!empty($p->rival_wa)):
								$partes = explode(',', $p->yo);
								$apellido = ucfirst(strtolower(trim($partes[0])));
								$nombre   = isset($partes[1]) ? ucfirst(strtolower(trim($partes[1]))) : '';
								$nombre_friendly = $nombre ? $nombre . ' ' . $apellido : $apellido;
								$wa_msg = 'Hola soy ' . $nombre_friendly . ', cuando podes jugar el partido del torneo interno?';
								$wa_phone = preg_replace('/[^0-9]/', '', $p->rival_wa);
								$wa_url = 'https://wa.me/' . $wa_phone . '?text=' . rawurlencode($wa_msg);
							?>
							<a href="<?=$wa_url?>" class="rival-wa-nombre" title="Escribirle por WhatsApp">
								<?=strtolower($p->rival)?> <i class="fab fa-whatsapp rival-wa-icon"></i>
							</a>
							<?php else: ?>
							<?=strtolower($p->rival)?>
							<?php endif; ?>
						</div>
						<small>rival</small>
					</div>
				</div>

				<div class="mipartido-form" id="form-<?=$p->id?>">
					<p class="mipartido-instruccion">¿Ganaste vos este partido?</p>

					<div class="mipartido-score-wrap">
						<label>Score del partido</label>
						<div class="score-row">
							<div class="score-par">
								<input type="number" class="score-n score-n1" data-partido="<?=$p->id?>" data-set="s1a" min="0" max="9" placeholder="0">
								<span class="score-dash">-</span>
								<input type="number" class="score-n score-n1" data-partido="<?=$p->id?>" data-set="s1b" min="0" max="9" placeholder="0">
							</div>
							<div class="score-par">
								<input type="number" class="score-n score-n1" data-partido="<?=$p->id?>" data-set="s2a" min="0" max="9" placeholder="0">
								<span class="score-dash">-</span>
								<input type="number" class="score-n score-n1" data-partido="<?=$p->id?>" data-set="s2b" min="0" max="9" placeholder="0">
							</div>
							<div class="score-par">
								<input type="number" class="score-n score-n2" data-partido="<?=$p->id?>" data-set="s3a" min="0" max="99" placeholder="–">
								<span class="score-dash">-</span>
								<input type="number" class="score-n score-n2" data-partido="<?=$p->id?>" data-set="s3b" min="0" max="99" placeholder="–">
							</div>
						</div>
					</div>

					<button class="btn btn-primary btn-cargar-resultado" 
						data-id="<?=$p->id?>"
						data-ganador="<?=$p->yo_id?>">
						<i class="fas fa-check"></i> Confirmar — Gané yo
					</button>
					<button class="btn btn-wo btn-cargar-wo"
						data-id="<?=$p->id?>"
						data-ganador="<?=$p->yo_id?>">
						<i class="fas fa-times-circle"></i> Gané por W.O.
					</button>
				</div>
			</div>
			<?php endforeach; ?>

			<?php endif; ?>
		</div>
	</div>
</div>

<style>
#mipartido-page {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: top;
	height: 100%;
	padding-top: 30px;
	padding-bottom: 30px;
}
#mipartido-page h1 {
	text-align: center;
	color: #fff;
	text-transform: uppercase;
	font-weight: 700;
	font-size: 38px;
	text-shadow: 4px 4px 12px rgba(0,0,0,0.9);
	margin: 0;
}
#mipartido-page > .container > p {
	color: #fff;
	text-align: center;
	font-size: 18px;
	text-shadow: 2px 2px 6px rgba(0,0,0,0.8);
	margin: 5px 0 25px 0;
}
.mipartido-card {
	background: rgba(0,0,0,0.75);
	border: 2px solid rgba(165,208,81,0.6);
	border-radius: 12px;
	padding: 20px;
	margin-bottom: 15px;
	box-shadow: 0 4px 15px rgba(0,0,0,0.5), inset 0 0 20px rgba(165,208,81,0.05);
}
.mipartido-ronda {
	font-size: 11px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: #a5d051;
	font-weight: 700;
	margin-bottom: 6px;
}
.mipartido-deadline {
	font-size: 12px;
	color: rgba(255,200,80,0.9);
	margin-bottom: 8px;
}
.mipartido-deadline i { margin-right: 4px; }
.mipartido-fecha-wrap { margin-bottom: 14px; }
.mipartido-fecha-trigger {
	background: none;
	border: 1px dashed rgba(165,208,81,0.4);
	color: rgba(165,208,81,0.8);
	border-radius: 8px;
	padding: 8px 14px;
	font-size: 12px;
	width: 100%;
	cursor: pointer;
	text-align: left;
	transition: all 0.2s;
}
.mipartido-fecha-trigger:hover { border-color: #a5d051; color: #a5d051; background: rgba(165,208,81,0.07); }
.mipartido-fecha-trigger i { margin-right: 6px; }
.mipartido-fecha-acordada {
	display: flex;
	align-items: center;
	gap: 8px;
	background: rgba(165,208,81,0.1);
	border: 1px solid rgba(165,208,81,0.3);
	border-radius: 8px;
	padding: 8px 12px;
	font-size: 13px;
	color: rgba(255,255,255,0.85);
}
.mipartido-fecha-acordada i { color: #a5d051; }
.mipartido-fecha-acordada span { flex: 1; }
.mipartido-fecha-edit {
	background: none;
	border: none;
	color: rgba(255,255,255,0.4);
	cursor: pointer;
	padding: 2px 4px;
	font-size: 11px;
}
.mipartido-fecha-edit:hover { color: #a5d051; }
.mipartido-fecha-form { margin-top: 6px; }
.mipartido-fecha-hint {
	font-size: 11px;
	color: rgba(255,255,255,0.45);
	margin-bottom: 6px;
	letter-spacing: 0.2px;
}
.mipartido-fecha-hint i { margin-right: 4px; color: rgba(165,208,81,0.6); }
.mipartido-fecha-inputs {
	display: flex;
	gap: 8px;
	margin-bottom: 8px;
}
.fecha-input-date, .fecha-input-time {
	flex: 1;
	background: rgba(255,255,255,0.08);
	border: 1.5px solid rgba(165,208,81,0.6);
	border-radius: 6px;
	color: #fff;
	padding: 9px 10px;
	font-size: 13px;
	cursor: pointer;
}
.fecha-input-date:focus, .fecha-input-time:focus {
	outline: none;
	border-color: #a5d051;
	background: rgba(165,208,81,0.1);
}
.mipartido-fecha-confirm {
	background: rgba(0,0,0,0.3);
	border: 1px solid rgba(165,208,81,0.3);
	border-radius: 8px;
	padding: 10px 12px;
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 10px;
}
.fecha-confirm-label { font-size: 13px; color: rgba(255,255,255,0.85); flex: 1; }
.mipartido-fecha-btns { display: flex; gap: 8px; }
.btn-fecha-ok {
	background: #a5d051;
	color: #1a1a2e;
	border: none;
	border-radius: 6px;
	padding: 6px 14px;
	font-size: 12px;
	font-weight: 700;
	cursor: pointer;
}
.btn-fecha-cancel {
	background: rgba(255,255,255,0.1);
	color: rgba(255,255,255,0.6);
	border: none;
	border-radius: 6px;
	padding: 6px 10px;
	font-size: 12px;
	cursor: pointer;
}
.mipartido-vs {
	display: flex;
	align-items: center;
	gap: 15px;
	margin-bottom: 20px;
}
.mipartido-jugador {
	flex: 1;
	text-align: center;
}
.mipartido-jugador.yo { text-align: right; }
.mipartido-jugador.rival { text-align: left; }
.mipartido-nombre {
	font-size: 16px;
	font-weight: 700;
	color: #fff;
	text-transform: capitalize;
}
.mipartido-jugador small { color: rgba(255,255,255,0.4); font-size: 11px; }
.mipartido-jugador.yo .mipartido-nombre { color: #a5d051; }
.rival-wa-nombre { color: #fff; text-decoration: none; }
.rival-wa-nombre:hover { color: #25D366; }
.rival-wa-icon { color: #25D366; margin-left: 4px; font-size: 20px; vertical-align: middle; }
.mipartido-separador {
	font-size: 13px;
	color: rgba(255,255,255,0.4);
	font-weight: 700;
	flex-shrink: 0;
}
.mipartido-instruccion {
	color: rgba(255,255,255,0.7);
	font-size: 14px;
	margin-bottom: 15px;
}
.mipartido-score-wrap { margin-bottom: 15px; }
.mipartido-score-wrap label {
	display: block;
	color: rgba(255,255,255,0.6);
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	margin-bottom: 8px;
}
.score-row { display:flex; align-items:center; gap:10px; flex-wrap:nowrap; }
.score-par {
	display:flex; align-items:center; gap:4px;
	background: rgba(255,255,255,0.1);
	border: 1px solid rgba(255,255,255,0.2);
	border-radius:6px; padding:6px 10px;
}
.score-par:focus-within { border-color:#a5d051; background:rgba(165,208,81,0.1); }
.score-n {
	text-align:center; font-weight:700; font-size:16px;
	border:none; background:transparent; outline:none;
	-moz-appearance:textfield; padding:2px 0; color:#fff;
}
.score-n::placeholder { color: rgba(255,255,255,0.3); }
.score-n::-webkit-outer-spin-button, .score-n::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
.score-n1 { width:28px; }
.score-n2 { width:38px; }
.score-dash { font-weight:700; font-size:16px; color:rgba(255,255,255,0.5); }
.btn-cargar-resultado { width: 100%; padding: 12px; font-size: 15px; margin-bottom: 8px; }
.btn-wo {
	width: 100%; padding: 10px; font-size: 13px;
	background: transparent; border: 1px solid rgba(255,100,100,0.4);
	color: rgba(255,100,100,0.8); border-radius: 6px; cursor: pointer;
}
.btn-wo:hover { background: rgba(255,100,100,0.1); border-color: rgba(255,100,100,0.7); color: #ff6464; }
.resultado-vacio {
	text-align: center; padding: 40px 0;
	color: rgba(255,255,255,0.5);
}
.resultado-vacio i { font-size: 50px; color: #a5d051; margin-bottom: 15px; display: block; }
.resultado-vacio p { font-size: 18px; }
@media (max-width: 575.98px) {
	#mipartido-page h1 { font-size: 26px; }
	.mipartido-nombre { font-size: 14px; }
}
</style>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var scoreOrder = ['s1a','s1b','s2a','s2b','s3a','s3b'];

	// Backspace: si campo vacío, ir al anterior
	$(document).on('keydown', '.score-n', function(e){
		if(e.key === 'Backspace' && $(this).val() === '') {
			var setKey = $(this).data('set');
			var partido = $(this).data('partido');
			var idx = scoreOrder.indexOf(setKey);
			if(idx > 0) {
				var $prev = $('[data-partido="'+partido+'"][data-set="'+scoreOrder[idx-1]+'"]');
				$prev.val('').focus().select();
			}
			e.preventDefault();
		}
	});

	// Auto-avanzar entre campos de score
	$(document).on('input', '.score-n', function(){
		var $inp = $(this);
		var isTiebreak = $inp.hasClass('score-n2');
		var maxLen = isTiebreak ? 2 : 1;
		var val = $inp.val().replace(/[^0-9]/g,'');
		if(val.length > maxLen) val = val.slice(0, maxLen);
		$inp.val(val);
		if(val.length === maxLen) {
			var setKey = $inp.data('set');
			var idx = scoreOrder.indexOf(setKey);
			var partido = $inp.data('partido');
			if(idx < scoreOrder.length - 1) {
				$('[data-partido="'+partido+'"][data-set="'+scoreOrder[idx+1]+'"]').focus().select();
			}
		}
	});

	function buildScoreForPartido(partidoId) {
		var sets = [];
		var pairs = [['s1a','s1b'],['s2a','s2b'],['s3a','s3b']];
		pairs.forEach(function(p) {
			var a = $('[data-partido="'+partidoId+'"][data-set="'+p[0]+'"]').val();
			var b = $('[data-partido="'+partidoId+'"][data-set="'+p[1]+'"]').val();
			if(a !== '' && b !== '') sets.push(a + '-' + b);
		});
		return sets.join(', ');
	}

	$(document).on('click', '.btn-cargar-wo', function(){
		var $btn = $(this);
		var id = $btn.data('id');
		var ganadorId = $btn.data('ganador');
		if(!confirm('¿Confirmás que ganaste este partido por W.O.?')) return;
		$btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
		$.ajax({
			url: baseurl + 'mipartido/cargarResultado',
			type: 'POST',
			data: { id: id, ganador_id: ganadorId, score: 'W.O.' },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action) {
					$('#form-' + id).html('<div style="color:#a5d051; text-align:center; padding:10px"><i class="fas fa-check-circle fa-2x"></i><p style="margin-top:8px">Resultado cargado. ¡Gracias!</p></div>');
				} else {
					alert(res.msg || 'Error al guardar.');
					$btn.prop('disabled', false).html('<i class="fas fa-times-circle"></i> Gané por W.O.');
				}
			}
		});
	});

	$(document).on('click', '.btn-cargar-resultado', function(){
		var $btn = $(this);
		var id = $btn.data('id');
		var ganadorId = $btn.data('ganador');
		var score = buildScoreForPartido(id);

		if(!score) {
			alert('Ingresá el score del partido antes de confirmar.');
			return;
		}
		if(!confirm('¿Confirmás que ganaste este partido con score ' + score + '?')) return;

		$btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

		$.ajax({
			url: baseurl + 'mipartido/cargarResultado',
			type: 'POST',
			data: { id: id, ganador_id: ganadorId, score: score },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action) {
					$('#form-' + id).html('<div style="color:#a5d051; text-align:center; padding:10px"><i class="fas fa-check-circle fa-2x"></i><p style="margin-top:8px">Resultado cargado. ¡Gracias!</p></div>');
				} else {
					alert(res.msg || 'Error al guardar.');
					$btn.prop('disabled', false).html('<i class="fas fa-check"></i> Confirmar — Gané yo');
				}
			}
		});
	});

	// Fecha acordada
	var MESES = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

	function formatFechaLabel(fecha, hora) {
		var d = new Date(fecha + 'T00:00:00');
		var label = d.getDate() + ' de ' + MESES[d.getMonth()];
		if(hora) label += ' a las ' + hora.substr(0,5);
		return label;
	}

	function abrirFechaForm(id) {
		$('#fecha-form-' + id).slideDown(150);
		var $f = $('#fecha-form-' + id);
		var deadline = $f.data('deadline');
		var hoy = new Date();
		var hoyStr = hoy.getFullYear() + '-' + String(hoy.getMonth()+1).padStart(2,'0') + '-' + String(hoy.getDate()).padStart(2,'0');
		$f.find('.fecha-input-date').attr('max', deadline).val(hoyStr);
		$f.find('.fecha-input-time').val('10:00');
		var label = formatFechaLabel(hoyStr, '10:00');
		$('#fecha-confirm-' + id).find('.fecha-confirm-label').text('Confirmar: ' + label);
		$('#fecha-confirm-' + id).show();
	}

	$(document).on('click', '.mipartido-fecha-trigger, .mipartido-fecha-edit', function(){
		var id = $(this).data('id');
		$('.mipartido-fecha-trigger[data-id="'+id+'"]').hide();
		$('.mipartido-fecha-acordada[id="fecha-acordada-'+id+'"]').hide();
		abrirFechaForm(id);
	});

	$(document).on('change', '.fecha-input-date, .fecha-input-time', function(){
		var $form = $(this).closest('.mipartido-fecha-form');
		var id = $form.data('id');
		var fecha = $form.find('.fecha-input-date').val();
		var hora  = $form.find('.fecha-input-time').val();
		if(!fecha) { $('#fecha-confirm-' + id).hide(); return; }
		var label = formatFechaLabel(fecha, hora);
		$('#fecha-confirm-' + id).find('.fecha-confirm-label').text('Confirmar: ' + label);
		$('#fecha-confirm-' + id).slideDown(150);
	});

	$(document).on('click', '.btn-fecha-cancel', function(){
		var $form = $(this).closest('.mipartido-fecha-form');
		var id = $form.data('id');
		$form.slideUp(150);
		$('#fecha-confirm-' + id).hide();
		if($('#fecha-acordada-' + id).length) {
			$('#fecha-acordada-' + id).show();
		} else {
			$('.mipartido-fecha-trigger[data-id="'+id+'"]').show();
		}
	});

	$(document).on('click', '.btn-fecha-ok', function(){
		var $btn = $(this);
		var $form = $btn.closest('.mipartido-fecha-form');
		var id    = $form.data('id');
		var fecha = $form.find('.fecha-input-date').val();
		var hora  = $form.find('.fecha-input-time').val();
		if(!fecha) return;
		$btn.prop('disabled', true);
		$.ajax({
			url: baseurl + 'mipartido/guardarFechaAcordada',
			type: 'POST',
			data: { id: id, fecha: fecha, hora: hora },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				$btn.prop('disabled', false);
				if(res.action) {
					$form.slideUp(150);
					var label = formatFechaLabel(fecha, hora);
					var $wrap = $('#fecha-acordada-' + id);
					if($wrap.length) {
						$wrap.find('strong').text(label.replace(' a las ', ' a las '));
						$wrap.show();
					} else {
						$('.mipartido-fecha-trigger[data-id="'+id+'"]').replaceWith(
							'<div class="mipartido-fecha-acordada" id="fecha-acordada-'+id+'">'
							+ '<i class="fas fa-calendar-check"></i>'
							+ '<span>Acordado: <strong>'+label+'</strong></span>'
							+ '<button class="mipartido-fecha-edit" data-id="'+id+'"><i class="fas fa-pencil-alt"></i></button>'
							+ '</div>'
						);
					}
				} else {
					alert(res.msg || 'Error al guardar.');
				}
			}
		});
	});
});
</script>
