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
			<p>Torneo Interno de Singles</p>

			<?php if(empty($partidos)): ?>
			<div class="resultado-vacio">
				<i class="fas fa-check-circle"></i>
				<p>No tenés partidos pendientes por cargar.</p>
			</div>
			<?php else: ?>

			<?php foreach($partidos as $p): ?>
			<div class="mipartido-card">
				<div class="mipartido-ronda"><?=$p->ronda?> — <?=$p->categoria?></div>
				<div class="mipartido-vs">
					<div class="mipartido-jugador yo">
						<div class="mipartido-nombre"><?=strtolower($p->yo)?></div>
						<small>vos</small>
					</div>
					<div class="mipartido-separador">vs</div>
					<div class="mipartido-jugador rival">
						<div class="mipartido-nombre"><?=strtolower($p->rival)?></div>
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
	background: rgba(0,0,0,0.5);
	border: 1px solid rgba(165,208,81,0.3);
	border-radius: 12px;
	padding: 20px;
	margin-bottom: 15px;
}
.mipartido-ronda {
	font-size: 11px;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: #a5d051;
	font-weight: 700;
	margin-bottom: 15px;
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
});
</script>
