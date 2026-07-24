<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="sorteo-admin">
	<div class="partidos-header">
		<h2><i class="fas fa-random"></i> Sorteo de Cuadros</h2>
	</div>
	<!-- PASO 1: SELECTOR -->
	<div class="sorteo-card mb-4">
		<div class="sorteo-card-header">
			<span class="sorteo-step">1</span> Seleccionar torneo, categoría y género
		</div>
		<div class="sorteo-card-body">
			<div class="form-row align-items-end">
				<div class="form-group col-md-2 mb-0">
					<label>Torneo</label>
					<select id="sorteo-tournament" class="form-control">
						<option value="singles">Singles</option>
						<option value="doubles">Dobles</option>
					</select>
				</div>
				<div class="form-group col-md-3 mb-0">
					<label>Categoría</label>
					<select id="sorteo-category" class="form-control">
						<option value="">Elegir...</option>
						<?php foreach($categories as $c): ?>
						<option value="<?=$c->id?>"><?=$c->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-md-3 mb-0">
					<label>Género</label>
					<select id="sorteo-gender" class="form-control">
						<option value="">Elegir...</option>
						<option value="M">Caballeros</option>
						<option value="F">Damas</option>
					</select>
				</div>
				<div class="form-group col-md-3 mb-0">
					<button class="btn btn-primary" id="btn-cargar">
						<i class="fas fa-users"></i> Cargar jugadores
					</button>
				</div>
			</div>
		</div>
	</div>
	<!-- PASO 2: ORDENAR POR SIEMBRA -->
	<div id="paso2" style="display:none">
		<div class="sorteo-card mb-4">
			<div class="sorteo-card-header">
				<span class="sorteo-step">2</span> Ordenar por siembra
				<small class="text-muted ml-2" id="info-sembrados"></small>
			</div>
			<div class="sorteo-card-body">
				<div class="alert alert-info mb-3">
					<i class="fas fa-info-circle"></i>
					Arrastrá los jugadores para ordenarlos. Los primeros serán los <strong>sembrados</strong> (resaltados en verde) y se ubicarán en posiciones fijas del cuadro. El resto se sortea al azar.
				</div>
				<div class="row">
					<div class="col-md-5">
						<ul id="sorteo-lista" class="sorteo-jugadores list-unstyled mb-3"></ul>
					</div>
					<div class="col-md-7">
						<!-- Botones siempre visibles a la derecha -->
						<div id="sorteo-acciones" style="margin-bottom:16px;">
							<button class="btn btn-warning btn-lg" id="btn-sortear" style="width:100%;margin-bottom:8px;">
								<i class="fas fa-random"></i> Sortear
							</button>
							<div id="btns-confirmacion" style="display:none;gap:8px;display:none;">
								<button class="btn btn-success btn-lg" id="btn-confirmar" style="width:100%;margin-bottom:8px;">
									<i class="fas fa-check"></i> Confirmar sorteo
								</button>
								<button class="btn btn-secondary" id="btn-volver" style="width:100%;">
									<i class="fas fa-redo"></i> Volver a sortear
								</button>
							</div>
						</div>
						<div id="bracket-col" style="display:none">
							<h6 class="sorteo-subtitulo">Vista previa del cuadro</h6>
							<div id="sorteo-bracket"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style>
.sorteo-admin { padding: 20px; }
.partidos-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.partidos-header h2 { margin:0; font-size:22px; }
.sorteo-card { border:1px solid #dee2e6; border-radius:8px; overflow:hidden; }
.sorteo-card-header {
	background: #343a40; color: #fff;
	padding: 12px 16px; font-weight:700;
	font-size: 14px; display:flex; align-items:center; gap:10px;
}
.sorteo-card-body { padding: 20px; }
.sorteo-step {
	width:26px; height:26px; background:#a5d051; color:#1a1a2e;
	border-radius:50%; display:inline-flex; align-items:center; justify-content:center;
	font-size:13px; font-weight:900; flex-shrink:0;
}
.sorteo-subtitulo { font-weight:700; text-transform:uppercase; font-size:12px; letter-spacing:0.5px; color:#495057; margin-bottom:10px; }
.sorteo-jugadores { border:1px solid #dee2e6; border-radius:8px; overflow:hidden; max-height:500px; overflow-y:auto; }
.sorteo-item {
	display:flex; align-items:center; padding:9px 12px;
	background:#fff; border-bottom:1px solid #dee2e6;
	cursor:grab; user-select:none; transition:background 0.15s;
}
.sorteo-item:last-child { border-bottom:none; }
.sorteo-item:hover { background:#f8f9fa; }
.sorteo-item.sembrado { background:#f0fae0; border-left:3px solid #a5d051; }
.sorteo-item.sortable-ghost { opacity:0.5; background:#e8f4fd; }
.sorteo-pos {
	width:24px; height:24px; background:#6c757d; color:#fff; border-radius:50%;
	display:flex; align-items:center; justify-content:center;
	font-size:11px; font-weight:700; margin-right:10px; flex-shrink:0;
}
.sorteo-item.sembrado .sorteo-pos { background:#a5d051; color:#1a1a2e; }
.sorteo-nombre { font-size:13px; text-transform:capitalize; flex:1; }
.sorteo-seed-badge {
	font-size:10px; font-weight:700; color:#a5d051;
	background:rgba(165,208,81,0.15); padding:2px 6px; border-radius:4px;
	margin-right:6px;
}
.sorteo-handle { color:#ced4da; font-size:14px; cursor:grab; }
/* Bracket */
.bracket-wrap { overflow-x:auto; padding-bottom:8px; }
.bracket { display:flex; gap:0; min-width:max-content; }
.bracket-ronda { display:flex; flex-direction:column; min-width:150px; }
.bracket-ronda-titulo {
	text-align:center; font-size:10px; font-weight:700; text-transform:uppercase;
	color:#6c757d; letter-spacing:0.4px; padding:6px; background:#f8f9fa;
	border:1px solid #dee2e6; border-bottom:none; margin:0 3px;
}
.bracket-matches { display:flex; flex-direction:column; justify-content:space-around; flex:1; padding:6px 3px; gap:6px; }
.bracket-match { background:#fff; border:1px solid #dee2e6; border-radius:5px; overflow:hidden; }
.bracket-match.sembrado-match { border-color:#a5d051; }
.bracket-player { padding:5px 8px; font-size:11px; text-transform:capitalize; border-bottom:1px solid #f0f0f0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.bracket-player:last-child { border-bottom:none; }
.bracket-player.tbd { color:#adb5bd; font-style:italic; }
.bracket-player.sembrado-player { color:#2d6a00; font-weight:700; }
.bracket-connector { display:flex; flex-direction:column; justify-content:space-around; width:14px; padding:6px 0; }
.bracket-connector-line { flex:1; border-right:1px solid #dee2e6; margin:1px 0; }
/* Resumen */
.resumen-grupo { margin-bottom:12px; }
.resumen-titulo { font-weight:700; font-size:13px; color:#495057; margin-bottom:6px; }
.resumen-partido { display:flex; align-items:center; gap:8px; padding:5px 0; border-bottom:1px solid #f0f0f0; font-size:13px; }
.resumen-partido:last-child { border-bottom:none; }
.resumen-ronda { font-size:10px; color:#6c757d; width:80px; flex-shrink:0; }
@media (max-width: 768px) {
	.form-row { align-items: flex-start !important; }
	.form-row .form-group label { margin-bottom: 4px; display: block; }
	#btn-cargar { margin-top: 32px; }
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var preselCat = '<?=isset($default_cat) ? $default_cat : ''?>';
	var preselGen = '<?=isset($default_gen) ? $default_gen : ''?>';
	if(preselCat) $('#sorteo-category').val(preselCat);
	if(preselGen) $('#sorteo-gender').val(preselGen);
	var jugadores = [];
	var categoryId = null;
	var gender = null;
	var tournament_type = 'singles';
	var numSembrados = 0;
	var bracketFinal = [];
	var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];
	function calcSembrados(n) {
		if(n <= 4)  return 2;
		if(n <= 8)  return 2;
		if(n <= 16) return 4;
		return 8;
	}
	function posicionesSembrados(size, nSembrados) {
		var e = Math.max(1, Math.floor(size / 8));
		var pos = {};
		if(nSembrados >= 1) pos[0] = 0;
		if(nSembrados >= 2) pos[1] = size - 2;
		if(nSembrados >= 4) {
			var ops34 = [3*e, 4*e];
			var s34 = Math.random() < 0.5;
			pos[2] = s34 ? ops34[0] : ops34[1];
			pos[3] = s34 ? ops34[1] : ops34[0];
		}
		if(nSembrados >= 8) {
			var libres = [e, 2*e, 5*e, 6*e];
			for(var i = libres.length-1; i > 0; i--) {
				var j = Math.floor(Math.random()*(i+1));
				var t = libres[i]; libres[i] = libres[j]; libres[j] = t;
			}
			pos[4] = libres[0];
			pos[5] = libres[1];
			pos[6] = libres[2];
			pos[7] = libres[3];
		}
		var validas = {};
		for(var k in pos) { if(pos[k] < size) validas[k] = pos[k]; }
		return validas;
	}
	$('#sorteo-tournament').on('change', function(){
		tournament_type = $(this).val();
		$('#paso2').hide();
		$('#bracket-col').hide();
		$('#btns-confirmacion').hide();
		$('#btn-sortear').show();
		bracketFinal = null;
		jugadores = [];
	});
	$('#sorteo-category, #sorteo-gender').on('change', function(){
		$('#paso2').hide();
		$('#bracket-col').hide();
		$('#btns-confirmacion').hide();
		$('#btn-sortear').show();
		bracketFinal = null;
		jugadores = [];
	});
	$('#btn-cargar').on('click', function(){
		categoryId = $('#sorteo-category').val();
		gender = $('#sorteo-gender').val();
		if(!categoryId || !gender) { alert('Seleccioná categoría y género.'); return; }
		$.ajax({
			url: baseurl + 'admin/getInscriptosByCategory',
			type: 'POST',
			data: { category: categoryId, gender: gender, tournament_type: tournament_type },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(!res.action || !res.jugadores.length) {
					alert('No hay inscriptos en esta categoría y género.');
					return;
				}
				jugadores = res.jugadores;
				numSembrados = calcSembrados(jugadores.length);
				$('#info-sembrados').text('(' + jugadores.length + ' jugadores — ' + numSembrados + ' sembrados)');
				renderLista();
				renderBracket();
				$('#paso2').slideDown();
			}
		});
	});
	$('#btn-sortear').on('click', function(){
		var sembrados = jugadores.slice(0, numSembrados);
		var resto = jugadores.slice(numSembrados);
		for(var i = resto.length-1; i > 0; i--) {
			var j = Math.floor(Math.random()*(i+1));
			var t = resto[i]; resto[i] = resto[j]; resto[j] = t;
		}
		jugadores = sembrados.concat(resto);
		renderLista();
		armarBracket();
	});
	function renderLista() {
		var html = '';
		for(var i = 0; i < jugadores.length; i++) {
			var esSembrado = i < numSembrados;
			html += '<li class="sorteo-item' + (esSembrado?' sembrado':'') + '" data-id="' + jugadores[i].jugador_id + '">';
			html += '<div class="sorteo-pos">' + (i+1) + '</div>';
			if(esSembrado) html += '<span class="sorteo-seed-badge">[' + (i+1) + ']</span>';
			html += '<div class="sorteo-nombre">' + jugadores[i].jugador.toLowerCase() + '</div>';
			html += '<div class="sorteo-handle"><i class="fas fa-grip-vertical"></i></div>';
			html += '</li>';
		}
		$('#sorteo-lista').html(html);
		if(window._sortable) window._sortable.destroy();
		window._sortable = new Sortable(document.getElementById('sorteo-lista'), {
			animation: 150,
			ghostClass: 'sortable-ghost',
			onEnd: function() {
				var nuevos = [];
				$('#sorteo-lista .sorteo-item').each(function(i, el){
					$(el).find('.sorteo-pos').text(i+1);
					var esSembrado = i < numSembrados;
					$(el).toggleClass('sembrado', esSembrado);
					$(el).find('.sorteo-seed-badge').remove();
					if(esSembrado) $(el).find('.sorteo-nombre').before('<span class="sorteo-seed-badge">[' + (i+1) + ']</span>');
					var id = $(el).data('id');
					var j = jugadores.find(function(x){ return x.jugador_id == id; });
					if(j) nuevos.push(j);
				});
				jugadores = nuevos;
			}
		});
	}
	function armarBracket() {
		var n = jugadores.length;
		var size = 1;
		while(size < n) size *= 2;
		var slots = new Array(size).fill(null);
		var posMap = posicionesSembrados(size, numSembrados);
		var sembradosConPos = 0;
		for(var s = 0; s < numSembrados; s++) {
			if(posMap[s] !== undefined) sembradosConPos++;
		}
		for(var s = 0; s < sembradosConPos && s < jugadores.length; s++) {
			if(posMap[s] !== undefined) slots[posMap[s]] = jugadores[s];
		}
		var nByes = size - n;
		var noSembrados = jugadores.slice(sembradosConPos);
		var slotsConBye = {};
		var byesUsados = 0;
		for(var s = 0; s < sembradosConPos && byesUsados < nByes; s++) {
			var spos = posMap[s];
			var companion = spos % 2 === 0 ? spos + 1 : spos - 1;
			if(!slotsConBye[companion] && !slots[companion]) {
				slotsConBye[companion] = true;
				byesUsados++;
			}
		}
		var vacios = [];
		for(var i = 0; i < size; i++) {
			if(!slots[i] && !slotsConBye[i]) vacios.push(i);
		}
		var byesRestantes = nByes - byesUsados;
		for(var i = vacios.length-1; i > 0; i--) {
			var j = Math.floor(Math.random()*(i+1));
			var t = vacios[i]; vacios[i] = vacios[j]; vacios[j] = t;
		}
		for(var i = 0; i < byesRestantes; i++) {
			if(vacios[i] !== undefined) slotsConBye[vacios[i]] = true;
		}
		var slotsJugadores = [];
		for(var i = 0; i < size; i++) {
			if(!slots[i] && !slotsConBye[i]) slotsJugadores.push(i);
		}
		for(var i = slotsJugadores.length-1; i > 0; i--) {
			var j = Math.floor(Math.random()*(i+1));
			var t = slotsJugadores[i]; slotsJugadores[i] = slotsJugadores[j]; slotsJugadores[j] = t;
		}
		for(var i = 0; i < noSembrados.length; i++) {
			if(slotsJugadores[i] !== undefined) slots[slotsJugadores[i]] = noSembrados[i];
		}
		bracketFinal = slots;
		$('#bracket-col').show();
		renderBracketFromSlots(slots, size);
		renderResumen(slots, size);
		$('#btns-confirmacion').show();
		$('#btn-sortear').hide();
	}
	function renderBracket() {
		var n = jugadores.length;
		var size = 1;
		while(size < n) size *= 2;
		var slots = jugadores.slice();
		while(slots.length < size) slots.push(null);
		renderBracketFromSlots(slots, size);
	}
	function getRondaInicio(size) {
		var map = {2:4, 4:3, 8:2, 16:1, 32:0};
		return map[size] !== undefined ? map[size] : 0;
	}
	function renderBracketFromSlots(slots, size) {
		var rondasCount = Math.log2(size);
		var rondaInicio = getRondaInicio(size);
		var rondas = [];
		var matches = [];
		for(var i = 0; i < slots.length; i+=2) matches.push([slots[i], slots[i+1]]);
		rondas.push({ titulo: RONDAS[rondaInicio], matches: matches });
		var mc = matches.length / 2;
		for(var r = 1; r < rondasCount; r++) {
			var tbd = [];
			for(var m = 0; m < mc; m++) tbd.push([null, null]);
			rondas.push({ titulo: RONDAS[rondaInicio + r] || 'Ronda '+(r+1), matches: tbd });
			mc = Math.max(1, mc/2);
		}
		var html = '<div class="bracket-wrap"><div class="bracket">';
		rondas.forEach(function(ronda, ri) {
			html += '<div class="bracket-ronda"><div class="bracket-ronda-titulo">' + ronda.titulo + '</div><div class="bracket-matches">';
			ronda.matches.forEach(function(p, mi) {
				var p1 = p[0], p2 = p[1];
				if(ri === 0 && !p1 && !p2) return;
				var isSembrado1 = p1 && jugadores.indexOf(p1) < numSembrados;
				var isSembrado2 = p2 && jugadores.indexOf(p2) < numSembrados;
				var matchClass = (isSembrado1 || isSembrado2) && ri == 0 ? ' sembrado-match' : '';
				html += '<div class="bracket-match' + matchClass + '">';
				var p1Empty = ri === 0 && !p1 && p2 ? 'BYE' : 'TBD';
				var p2Empty = ri === 0 && !p2 && p1 ? 'BYE' : 'TBD';
				html += '<div class="bracket-player' + (p1 ? (isSembrado1&&ri==0?' sembrado-player':'') : ' tbd') + '">';
				if(p1 && isSembrado1 && ri==0) html += '[' + (jugadores.indexOf(p1)+1) + '] ';
				html += (p1 ? p1.jugador.toLowerCase() : p1Empty) + '</div>';
				html += '<div class="bracket-player' + (p2 ? (isSembrado2&&ri==0?' sembrado-player':'') : ' tbd') + '">';
				if(p2 && isSembrado2 && ri==0) html += '[' + (jugadores.indexOf(p2)+1) + '] ';
				html += (p2 ? p2.jugador.toLowerCase() : p2Empty) + '</div>';
				html += '</div>';
			});
			html += '</div></div>';
			if(ri < rondas.length-1) {
				html += '<div class="bracket-connector">';
				for(var i=0; i<ronda.matches.length; i++) html += '<div class="bracket-connector-line"></div>';
				html += '</div>';
			}
		});
		html += '</div></div>';
		$('#sorteo-bracket').html(html);
	}
	function renderResumen(slots, size) {
		var rondaInicio = getRondaInicio(size);
		var rondaNombre = RONDAS[rondaInicio];
		var html = '<div class="resumen-grupo"><div class="resumen-titulo">Partidos de ' + rondaNombre + '</div>';
		for(var i = 0; i < slots.length; i+=2) {
			var p1 = slots[i], p2 = slots[i+1];
			if(!p1 && !p2) continue;
			var idx1 = p1 ? jugadores.indexOf(p1) : -1;
			var idx2 = p2 ? jugadores.indexOf(p2) : -1;
			html += '<div class="resumen-partido">';
			html += '<span class="resumen-ronda">' + rondaNombre + '</span>';
			html += '<span>' + (p1 ? (idx1 < numSembrados ? '[' + (idx1+1) + '] ' : '') + p1.jugador.toLowerCase() : 'BYE') + '</span>';
			html += '<span style="color:#a5d051; margin:0 8px">vs</span>';
			html += '<span>' + (p2 ? (idx2 < numSembrados ? '[' + (idx2+1) + '] ' : '') + p2.jugador.toLowerCase() : 'BYE') + '</span>';
			html += '</div>';
		}
		html += '</div>';
		$('#resumen-sorteo').html(html);
	}
	$('#btn-volver').on('click', function(){
		$('#bracket-col').hide();
		$('#btns-confirmacion').hide();
		$('#btn-sortear').show();
		bracketFinal = null;
	});
	$('#btn-confirmar').on('click', function(){
		var catNombre = $('#sorteo-category option:selected').text();
		var genNombre = gender == 'M' ? 'Caballeros' : 'Damas';
		if(!confirm('¿Confirmás el sorteo de ' + catNombre + ' ' + genNombre + '? Si ya hay partidos para esta categoría y género serán eliminados.')) return;
		var idsOrdenados = bracketFinal.map(function(j){ return j ? j.jugador_id : null; });
		var sembradosData = [];
		for(var s = 0; s < numSembrados; s++) {
			sembradosData.push(jugadores[s] ? jugadores[s].jugador_id : null);
		}
		$.ajax({
			url: baseurl + 'admin/confirmarSorteo',
			type: 'POST',
			data: {
				category: categoryId,
				gender: gender,
				tournament_type: tournament_type,
				jugadores: JSON.stringify(idsOrdenados),
				sembrados: JSON.stringify(sembradosData)
			},
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(res.action) {
					alert('¡Sorteo confirmado!');
					location.href = baseurl + 'admin/partidos?cat=' + categoryId + '&gen=' + gender + '#acordeon-cat-' + categoryId + '-' + gender;
				} else {
					alert('Error: ' + (res.msg || 'Error desconocido'));
				}
			}
		});
	});
});
</script>
