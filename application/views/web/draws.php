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
	<div id="draws-page" class="container">
		<div class="container">
			<h1>Draws</h1>
			<p class="draws-subtitle">Seleccioná categoría y género para ver los cuadros</p>

			<!-- TABS TORNEOS - SOLO DOBLES -->
			<div class="draws-tournament-tabs">
				<button class="draws-tab active" data-tournament="singles">Singles</button>
				<button class="draws-tab" data-tournament="doubles">Dobles</button>
			</div>

			<!-- SELECTOR -->
			<div class="draws-selector">
				<div class="draws-select-wrap">
					<select id="draws-category" class="draws-select">
						<option value="">Categoría...</option>
						<?php foreach($categories as $c): ?>
						<option value="<?=$c->id?>" <?=($mi_category && $c->id==$mi_category)?'selected':''?>><?=$c->name?></option>
						<?php endforeach; ?>
					</select>
					<i class="fas fa-chevron-down draws-select-arrow"></i>
				</div>
				<div class="draws-select-wrap">
					<select id="draws-gender" class="draws-select">
						<option value="">Género...</option>
						<option value="M" <?=($mi_gender=='M')?'selected':''?>>Caballeros</option>
						<option value="F" <?=($mi_gender=='F')?'selected':''?>>Damas</option>
					</select>
					<i class="fas fa-chevron-down draws-select-arrow"></i>
				</div>
			</div>
			<div id="draw-resultado" style="display:none">
				<div id="draw-titulo" class="draws-titulo">
					<span id="mi-cat-badge" style="display:none" class="mi-cat-badge">⭐ Mi categoría</span>
				</div>
				<div id="draw-nav" class="draws-nav" style="display:none">
					<button class="btn btn-sm btn-outline draws-nav-btn" id="btn-prev-ronda"><i class="fas fa-chevron-left"></i></button>
					<span id="draws-ronda-label" class="draws-nav-label"></span>
					<button class="btn btn-sm btn-outline draws-nav-btn" id="btn-next-ronda"><i class="fas fa-chevron-right"></i></button>
				</div>
				<div id="draw-bracket" class="draws-bracket-wrap"></div>
			</div>
			<div id="draw-vacio" style="display:none" class="resultado-vacio">
				<i class="fas fa-sitemap"></i>
				<p>No hay draw disponible para esta selección.</p>
			</div>
		</div>
	</div>
</div>
<style>
#draws-page {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: top;
	height: 100%;
	padding-top: 30px;
	padding-bottom: 30px;
}
#draws-page h1 {
	text-align: center;
	color: #fff;
	text-transform: uppercase;
	font-weight: 700;
	font-size: 38px;
	text-shadow: 4px 4px 12px rgba(0,0,0,0.9);
	margin: 0;
}
#draws-page > .container > p {
	color: #fff;
	text-align: center;
	font-size: 18px;
	text-shadow: 2px 2px 6px rgba(0,0,0,0.8);
	margin: 5px 0 20px 0;
}
.draws-hint strong { color: rgba(255,255,255,0.7); }
.mi-cat-badge {
	display: inline-block;
	font-size: 11px;
	background: rgba(165,208,81,0.2);
	color: #a5d051;
	border: 1px solid rgba(165,208,81,0.4);
	padding: 2px 10px;
	border-radius: 10px;
	font-weight: 700;
	margin-left: 8px;
	vertical-align: middle;
	letter-spacing: 0.3px;
}
.draws-subtitle {
	text-align: center;
	color: rgba(255,255,255,0.55);
	font-size: 14px;
	margin: 5px 0 20px 0;
	text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
}
.draws-tournament-tabs {
	display: flex;
	justify-content: center;
	gap: 10px;
	margin-bottom: 20px;
}
.draws-tab {
	background: rgba(0,0,0,0.5);
	border: 1px solid rgba(165,208,81,0.4);
	color: rgba(255,255,255,0.7);
	border-radius: 6px;
	padding: 10px 20px;
	font-size: 14px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}
.draws-tab:hover {
	background: rgba(165,208,81,0.1);
	border-color: #a5d051;
	color: #a5d051;
}
.draws-tab.active {
	background: rgba(165,208,81,0.2);
	border-color: #a5d051;
	color: #a5d051;
}
.draws-selector {
	display: flex;
	gap: 10px;
	justify-content: center;
	flex-wrap: wrap;
	margin-bottom: 20px;
}
.draws-select-wrap {
	position: relative;
	display: inline-flex;
	align-items: center;
	flex: 1;
	min-width: 140px;
}
.draws-select-arrow {
	position: absolute;
	right: 10px;
	top: 50%;
	transform: translateY(-50%);
	color: #a5d051;
	font-size: 10px;
	pointer-events: none;
}
.draws-select {
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none;
	background: rgba(0,0,0,0.5);
	border: 1px solid rgba(165,208,81,0.4);
	color: #fff;
	border-radius: 6px;
	padding: 8px 32px 8px 14px;
	font-size: 14px;
	min-width: 140px;
	width: 100%;
}
.draws-select option { background: #222; color: #fff; }
.draws-titulo {
	text-align: center;
	color: #a5d051;
	font-weight: 700;
	font-size: 16px;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 15px;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-wrap: wrap;
	gap: 6px;
}
/* NAV RONDAS */
.draws-nav {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 15px;
	margin-bottom: 15px;
}
.draws-nav-btn {
	background: rgba(255,255,255,0.1);
	border: 1px solid rgba(255,255,255,0.2);
	color: #fff;
	border-radius: 6px;
	padding: 5px 12px;
}
.draws-nav-btn:hover { background: rgba(165,208,81,0.2); border-color: #a5d051; }
.draws-nav-btn:disabled { opacity: 0.3; cursor: default; }
.draws-nav-label {
	color: #fff;
	font-weight: 700;
	font-size: 14px;
	min-width: 160px;
	text-align: center;
}
/* BRACKET */
.draws-bracket-wrap { overflow-x: auto; padding-bottom: 10px; }
.draws-bracket { display: flex; gap: 0; min-width: max-content; }
.draws-ronda { display: flex; flex-direction: column; min-width: 160px; max-width: 200px; }
.draws-ronda-titulo {
	text-align: center;
	font-size: 11px;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: rgba(255,255,255,0.7);
	padding: 8px 6px;
	background: rgba(0,0,0,0.6);
	border: 1px solid rgba(255,255,255,0.2);
	margin: 0 4px;
	border-radius: 4px;
}
.draws-ronda.activa .draws-ronda-titulo {
	color: #a5d051;
	background: rgba(165,208,81,0.1);
	border-color: rgba(165,208,81,0.3);
}
.draws-matches {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	flex: 1;
	padding: 8px 4px;
	gap: 8px;
}
.draws-match {
	background: rgba(0,0,0,0.75);
	border: 1.5px solid rgba(165,208,81,0.5);
	border-radius: 8px;
	overflow: hidden;
	box-shadow: 0 2px 8px rgba(0,0,0,0.4);
}
.draws-match.jugado { border-color: rgba(165,208,81,0.4); }
.draws-player {
	display: flex;
	align-items: center;
	padding: 9px 10px;
	font-size: 13px;
	border-bottom: 1px solid rgba(255,255,255,0.07);
	gap: 6px;
	min-height: 36px;
	color: rgba(255,255,255,0.85);
	text-transform: capitalize;
	font-weight: 600;
}
.draws-player:last-child { border-bottom: none; }
.draws-player.ganador { color: #a5d051; font-weight: 800; }
.draws-player.perdedor { color: rgba(255,255,255,0.3); text-decoration: line-through; font-weight: 400; }
.draws-player.tbd { color: rgba(255,255,255,0.3); font-style: italic; font-weight: 400; }
.draws-player-name { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-style: italic; }
.draws-check { font-size: 11px; color: #a5d051; }
.draws-score {
	text-align: center;
	font-size: 11px;
	color: #a5d051;
	padding: 3px 8px;
	background: rgba(165,208,81,0.1);
	font-weight: 700;
}
.draws-connector {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	width: 16px;
	padding: 8px 0;
}
.draws-connector-line {
	flex: 1;
	border-right: 1px solid rgba(165,208,81,0.3);
	margin: 2px 0;
}
.draws-player.yo-player {
	background: rgba(165,208,81,0.12);
	border-left: 2px solid #a5d051;
	color: #fff !important;
	font-weight: 800 !important;
}
.resultado-vacio { text-align:center; padding:40px 0; color:rgba(255,255,255,0.5); }
.resultado-vacio i { font-size:50px; color:#a5d051; margin-bottom:15px; display:block; }
.resultado-vacio p { font-size:18px; }
@media (max-width: 767px) {
	#draws-page { padding-top: 16px; padding-bottom: 16px; }
	#draws-page h1 { font-size: 22px; }
	#draws-page > .container { padding-left: 12px; padding-right: 12px; }
	.draws-subtitle { font-size: 13px; }
	.draws-selector { gap: 8px; }
	.draws-select { font-size: 13px; padding: 7px 28px 7px 10px; }

	/* Bracket en mobile: scroll horizontal contenido */
	.draws-bracket-wrap {
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
		padding-bottom: 16px;
		margin-left: -12px;
		margin-right: -12px;
		padding-left: 12px;
		padding-right: 12px;
	}
	.draws-bracket {
		display: flex;
		flex-wrap: nowrap;
		gap: 0;
		min-width: max-content;
	}
	.draws-ronda {
		min-width: 150px;
		max-width: 170px;
	}
	.draws-ronda-titulo {
		font-size: 10px;
		padding: 6px 4px;
	}
	.draws-player {
		font-size: 11px;
		padding: 7px 8px;
		min-height: 32px;
	}
	.draws-player-name {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: 110px;
	}
	.draws-matches { gap: 6px; padding: 6px 2px; }
	.draws-match { border-radius: 6px; }
	.draws-connector { width: 10px; }

	/* Nav rondas */
	.draws-nav { gap: 10px; margin-bottom: 10px; }
	.draws-nav-label { font-size: 12px; min-width: 130px; }
	.draws-nav-btn { padding: 4px 10px; font-size: 12px; }

	/* Título */
	.draws-titulo { font-size: 13px; letter-spacing: 0.5px; }
}
</style>
<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];
	var todosPartidos = [];
	var sembradosActivos = {};
	var rondaActivaIdx = 0;
	var rondaInicioIdx = 0;

	function getRondaInicio() {
		for(var i = 0; i < RONDAS.length; i++) {
			if(todosPartidos.some(function(p){ return p.ronda === RONDAS[i]; })) return i;
		}
		return 0;
	}

	function cargarDraw(cat, gen) {
		if(!cat || !gen) return;
		$.ajax({
			url: baseurl + 'draws/getData',
			type: 'POST',
			data: { category: cat, gender: gen },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				$('#draw-bracket').html('');
				if(!res.action || !res.partidos.length) {
					$('#draw-resultado').hide();
					$('#draw-vacio').show();
					return;
				}
				$('#draw-vacio').hide();
				todosPartidos = res.partidos;
				sembradosActivos = res.sembrados || {};
				var catNombre = $('#draws-category option:selected').text();
				var genNombre = gen == 'M' ? 'Caballeros' : 'Damas';
				$('#draw-titulo').html('Categoría ' + catNombre + ' — ' + genNombre + '<span id="mi-cat-badge" style="display:none" class="mi-cat-badge">⭐ Mi categoría</span>');
				if(miCat && miGen && cat == miCat && gen == miGen) {
					$('#mi-cat-badge').show();
				}
				if(res.is_groups) {
					renderGroups(res.groups);
				} else {
					rondaInicioIdx = getRondaInicio();
					rondaActivaIdx = rondaInicioIdx;
					for(var i = rondaInicioIdx; i < RONDAS.length; i++) {
						var rp = todosPartidos.filter(function(p){ return p.ronda === RONDAS[i]; });
						if(rp.length > 0) {
							var pend = rp.filter(function(p){ return !p.ganador_id; });
							if(pend.length > 0) { rondaActivaIdx = i; break; }
							rondaActivaIdx = i;
						}
					}
					renderBracket();
				}
				$('#draw-resultado').show();
			}
		});
	}

	function formatPlayerNamesShort(fullNames) {
		if(!fullNames) return 'BYE';
		var names = fullNames.split(' / ');
		return names.map(function(n) {
			var apellido = n.split(',')[0].trim();
			return apellido.charAt(0).toUpperCase() + apellido.slice(1).toLowerCase();
		}).join('-');
	}

	function renderGroups(groups) {
		$('#draw-nav').hide();
		var html = '';
		for(var grupoNombre in groups) {
			var standings = groups[grupoNombre];
			html += '<div style="margin-bottom: 20px; background: rgba(0,0,0,0.6); padding: 12px; border-radius: 8px; border: 1px solid rgba(165,208,81,0.3);">';
			html += '<h5 style="margin-bottom: 10px; color: #a5d051; text-transform: uppercase; letter-spacing: 0.5px; font-size: 12px;">' + grupoNombre + '</h5>';
			html += '<table class="table table-sm" style="max-width: 350px; margin-bottom: 0; background: rgba(0,0,0,0.5); border-collapse: collapse; font-size: 12px;">';
			html += '<thead><tr style="background: rgba(0,0,0,0.7); border-bottom: 2px solid rgba(165,208,81,0.4);"><th style="color: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.2); font-weight: 700; width: 25px;">Pos</th><th style="color: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.2); font-weight: 700;">Apellido</th><th style="color: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.2); font-weight: 700; width: 30px;">PJ</th><th style="color: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.2); font-weight: 700; width: 30px;">PG</th><th style="color: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.2); font-weight: 700; width: 30px;">Pts</th></tr></thead>';
			html += '<tbody>';
			for(var i = 0; i < standings.length; i++) {
				var s = standings[i];
				var apellido = s.nombre.split(' / ').map(function(n) { return n.split(',')[0]; }).join(' / ');
				html += '<tr style="border-bottom: 1px solid rgba(165,208,81,0.15); background: ' + (i % 2 === 0 ? 'rgba(0,0,0,0.3)' : 'rgba(0,0,0,0.5)') + ';">';
				html += '<td style="font-weight: bold; color: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.1); text-align: center;">' + (i+1) + '</td>';
				html += '<td style="color: rgba(255,255,255,0.9); padding: 6px; border: 1px solid rgba(165,208,81,0.1); text-transform: capitalize; font-size: 11px;">' + apellido.toLowerCase() + '</td>';
				html += '<td style="color: rgba(255,255,255,0.8); padding: 6px; border: 1px solid rgba(165,208,81,0.1); text-align: center;">' + s.pj + '</td>';
				html += '<td style="color: rgba(255,255,255,0.8); padding: 6px; border: 1px solid rgba(165,208,81,0.1); text-align: center;">' + s.pg + '</td>';
				html += '<td style="font-weight: bold; color: #1a1a2e; background: #a5d051; padding: 6px; border: 1px solid rgba(165,208,81,0.5); text-align: center;">' + s.pts + '</td>';
				html += '</tr>';
			}
			html += '</tbody></table>';
			html += '</div>';
		}
		$('#draw-bracket').html(html);
	}

	function renderBracket() {
		var rondasMostrar = [RONDAS[rondaActivaIdx]];
		if(RONDAS[rondaActivaIdx + 1]) rondasMostrar.push(RONDAS[rondaActivaIdx + 1]);

		$('#btn-prev-ronda').prop('disabled', rondaActivaIdx <= rondaInicioIdx);
		$('#btn-next-ronda').prop('disabled', rondaActivaIdx >= RONDAS.length - 1);
		$('#draws-ronda-label').text(RONDAS[rondaActivaIdx] + (RONDAS[rondaActivaIdx+1] ? ' + ' + RONDAS[rondaActivaIdx+1] : ''));
		$('#draw-nav').show();

		var primera = todosPartidos.filter(function(p){ return p.ronda === RONDAS[rondaInicioIdx]; });
		var totalPrimera = primera.length;

		var html = '<div class="draws-bracket">';
		rondasMostrar.forEach(function(ronda, idx) {
			var esActiva = idx === 0;
			var rondaIdx = RONDAS.indexOf(ronda);
			var rondaIdxRelativo = rondaIdx - rondaInicioIdx;
			var slotCount = Math.max(1, totalPrimera / Math.pow(2, rondaIdxRelativo));
			var slots = new Array(Math.ceil(slotCount)).fill(null);
			todosPartidos.forEach(function(p) {
				if(p.ronda === ronda) {
					var pos = p.bracket_pos !== null && p.bracket_pos !== undefined ? parseInt(p.bracket_pos) : 0;
					if(pos < slots.length) slots[pos] = p;
				}
			});
			html += '<div class="draws-ronda' + (esActiva ? ' activa' : '') + '">';
			html += '<div class="draws-ronda-titulo">' + ronda + (esActiva ? ' <span style="font-size:9px;background:rgba(165,208,81,0.2);color:#a5d051;padding:1px 6px;border-radius:8px;margin-left:4px">EN JUEGO</span>' : '') + '</div>';
			html += '<div class="draws-matches">';
			slots.forEach(function(p) {
				if(!p) {
					html += '<div class="draws-match">';
					html += '<div class="draws-player tbd"><span class="draws-player-name">por definir</span></div>';
					html += '<div class="draws-player tbd"><span class="draws-player-name">por definir</span></div>';
					html += '</div>';
					return;
				}
				var esBYE = p.score === 'BYE';
				var jugado = p.ganador_id != null;
				html += '<div class="draws-match' + (jugado ? ' jugado' : '') + '">';
				var esBYE1 = !p.jugador1_id || p.jugador1_id == 0;
				var c1 = (!esBYE1 && jugado) ? (p.ganador_id == p.jugador1_id ? 'ganador' : 'perdedor') : '';
				var seed1 = !esBYE1 && sembradosActivos[p.jugador1_id] ? sembradosActivos[p.jugador1_id] : 0;
				var esYo1 = !esBYE1 && miPartnerId && p.jugador1_id == miPartnerId;
				html += '<div class="draws-player ' + c1 + (esBYE1 ? ' tbd' : '') + (esYo1 ? ' yo-player' : '') + '">';
				if(seed1) html += '<span style="color:#a5d051;font-weight:800;font-size:10px;margin-right:3px">['+seed1+']</span>';
				html += '<span class="draws-player-name">' + formatPlayerNamesShort(p.jugador1) + '</span>';
				if(!esBYE1 && p.ganador_id == p.jugador1_id) html += '<span class="draws-check"><i class="fas fa-check"></i></span>';
				html += '</div>';
				var esBYE2 = !p.jugador2_id || p.jugador2_id == 0;
				var c2 = (!esBYE2 && jugado) ? (p.ganador_id == p.jugador2_id ? 'ganador' : 'perdedor') : '';
				var seed2 = !esBYE2 && sembradosActivos[p.jugador2_id] ? sembradosActivos[p.jugador2_id] : 0;
				var esYo2 = !esBYE2 && miPartnerId && p.jugador2_id == miPartnerId;
				html += '<div class="draws-player ' + c2 + (esBYE2 ? ' tbd' : '') + (esYo2 ? ' yo-player' : '') + '">';
				if(seed2) html += '<span style="color:#a5d051;font-weight:800;font-size:10px;margin-right:3px">['+seed2+']</span>';
				html += '<span class="draws-player-name">' + formatPlayerNamesShort(p.jugador2) + '</span>';
				if(!esBYE2 && p.ganador_id == p.jugador2_id) html += '<span class="draws-check"><i class="fas fa-check"></i></span>';
				html += '</div>';
				if(p.score && p.score !== 'BYE') html += '<div class="draws-score">' + p.score + '</div>';
				html += '</div>';
			});
			html += '</div></div>';
			if(idx < rondasMostrar.length - 1) {
				html += '<div class="draws-connector">';
				for(var i = 0; i < slots.length; i++) html += '<div class="draws-connector-line"></div>';
				html += '</div>';
			}
		});
		html += '</div>';
		$('#draw-bracket').html(html);
	}

	$('#btn-prev-ronda').on('click', function(){
		if(rondaActivaIdx > rondaInicioIdx) { rondaActivaIdx--; renderBracket(); }
	});
	$('#btn-next-ronda').on('click', function(){
		if(rondaActivaIdx < RONDAS.length - 1) { rondaActivaIdx++; renderBracket(); }
	});

	var miCat = '<?=$mi_category?>';
	var miGen = '<?=$mi_gender?>';
	var miPartnerId = '<?=$mi_partner_id?>';
	var disponibles = {};
	var currentTournament = 'doubles';

	// Mostrar solo tab de dobles
	$('.draws-tab[data-tournament="singles"]').hide();
	$('.draws-tab[data-tournament="doubles"]').addClass('active');

	function cargarDrawsDisponibles() {
		$.ajax({
			url: baseurl + 'draws/getDrawsDisponibles',
			type: 'POST',
			data: { tournament_type: currentTournament },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.draws) {
					res.draws.forEach(function(d){ disponibles[d.category+'_'+d.gender] = true; });
				}
				var initialGen = (miGen && miGen !== '') ? miGen : 'M';
				$('#draws-gender').val(initialGen);
				var toLoad = null;
				// Prioridad 1: Siempre mostrar la categoría del usuario si existe
				if(miCat !== '' && miGen !== '') {
					toLoad = { cat: miCat, gen: miGen };
				} else if(res.draws && res.draws.length) {
					// Prioridad 2: Si no tiene categoría, mostrar la primera disponible
					var fallback = (miGen && miGen !== '') ? res.draws.find(function(d){ return d.gender === miGen; }) : null;
					if(!fallback) fallback = res.draws[0];
					if(fallback) {
						toLoad = { cat: fallback.category, gen: fallback.gender };
						$('#draws-gender').val(toLoad.gen);
					}
				}
				if(toLoad) {
					$('#draws-category').val(toLoad.cat);
					cargarDraw(toLoad.cat, toLoad.gen);
				}
			}
		});
	}

	// Auto-cargar mi categoría si existe
	if(miCat !== '') {
		var genToUse = (miGen !== '' && miGen !== null) ? miGen : 'M';
		$('#draws-category').val(miCat);
		$('#draws-gender').val(genToUse);
		cargarDraw(miCat, genToUse);
	} else {
		cargarDrawsDisponibles();
	}

	$('#draws-category, #draws-gender').on('change', function(){
		var cat = $('#draws-category').val();
		var gen = $('#draws-gender').val();
		if(cat && gen) cargarDraw(cat, gen);
	});
});
</script>
