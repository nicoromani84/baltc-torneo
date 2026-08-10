<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="draws-admin">

	<div class="partidos-header">
		<h2><i class="fas fa-sitemap"></i> Draw del Torneo</h2>
		<button class="btn btn-danger" id="btn-pdf" style="display:none">
			<i class="fas fa-file-pdf"></i> Bajar PDF
		</button>
	</div>

	<!-- SELECTOR CATEGORIA -->
	<div class="card mb-4">
		<div class="card-body">
			<div class="form-row align-items-end">
				<div class="form-group col-md-4 mb-0">
					<label>Categoría</label>
					<select id="draws-category" class="form-control">
						<option value="">Elegir...</option>
						<?php foreach($categories as $c): ?>
						<option value="<?=$c->id?>"><?=$c->name?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group col-md-3 mb-0">
					<label>Género</label>
					<select id="draws-gender" class="form-control">
						<option value="">Elegir...</option>
						<option value="M">Caballeros</option>
						<option value="F">Damas</option>
					</select>
				</div>
				<div class="form-group col-md-3 mb-0">
					<button class="btn btn-primary" id="btn-ver-draw">
						<i class="fas fa-eye"></i> Ver Draw
					</button>
				</div>
				<div class="form-group col-md-2 mb-0">
					<button class="btn btn-info" id="btn-crear-grupos">
						<i class="fas fa-users"></i> Grupos
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- PESTAÑAS RÁPIDAS - solo categorías con draw sorteado -->
	<div class="draws-tabs mb-3" id="draws-tabs-container">
		<span class="text-muted small">Cargando...</span>
	</div>

	<div id="draw-titulo-activo" class="draws-titulo-activo" style="display:none"></div>

	<div id="draw-container" style="display:none">
		<div id="draw-bracket" class="draw-bracket-wrap"></div>
	</div>

	<div id="draw-vacio" style="display:none" class="text-center text-muted" style="padding:40px">
		<i class="fas fa-sitemap fa-3x" style="margin-bottom:15px; display:block; color:#dee2e6"></i>
		<p>No hay partidos para esta categoría todavía.</p>
	</div>

</div>

<style>
.draws-admin { padding: 20px; }
.draws-tabs { display:flex; flex-wrap:wrap; gap:6px; }
.draws-tab {
	background: #f8f9fa;
	border: 1px solid #dee2e6;
	border-radius: 6px;
	padding: 6px 14px;
	font-size: 13px;
	font-weight: 600;
	color: #495057;
	cursor: pointer;
	transition: all 0.2s;
}
.draws-tab:hover { background: #e9ecef; border-color: #adb5bd; }
.draws-tab.active {
	background: #a5d051;
	border-color: #a5d051;
	color: #1a1a2e;
}
.draws-tab .fa-male { color: #1a6fad; }
.draws-tab .fa-female { color: #ad1a6f; }
.draws-tab.active .fa-male,
.draws-tab.active .fa-female { color: #1a1a2e; }
.draws-tab-pending {
	background: #fff;
	border: 1px dashed #adb5bd;
	color: #adb5bd;
	opacity: 0.75;
}
.draws-tab-pending:hover { opacity: 1; border-color: #f0ad4e; color: #856404; background: #fff8e1; }
.draws-tab-pending.active { background: #fff8e1; border-color: #f0ad4e; color: #856404; opacity:1; }
.draws-titulo-activo {
	font-weight: 700;
	font-size: 15px;
	color: #495057;
	margin-bottom: 12px;
	padding: 8px 12px;
	background: #f8f9fa;
	border-left: 4px solid #a5d051;
	border-radius: 4px;
}
.partidos-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.partidos-header h2 { margin:0; font-size:22px; }

.draw-bracket-wrap { overflow-x: auto; padding-bottom: 20px; }
.draw-bracket { display: flex; gap: 0; min-width: max-content; }

.draw-ronda {
	display: flex;
	flex-direction: column;
	min-width: 200px;
}
.draw-ronda-titulo {
	text-align: center;
	font-size: 11px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: #6c757d;
	padding: 8px;
	background: #f8f9fa;
	border: 1px solid #dee2e6;
	border-bottom: none;
	margin: 0 4px;
}
.draw-matches {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	flex: 1;
	padding: 8px 4px;
	gap: 8px;
}

.draw-match {
	background: #fff;
	border: 1px solid #dee2e6;
	border-radius: 6px;
	overflow: hidden;
	position: relative;
	box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.draw-match.pendiente { border-color: #dee2e6; }
.draw-match.jugado { border-color: #a5d051; }

.draw-player {
	display: flex;
	align-items: center;
	padding: 7px 10px;
	font-size: 12px;
	border-bottom: 1px solid #f0f0f0;
	gap: 6px;
	min-height: 34px;
}
.draw-player:last-child { border-bottom: none; }
.draw-player.ganador { font-weight: 700; color: #2d6a00; background: #f0fae0; }
.draw-player.perdedor { color: #adb5bd; text-decoration: line-through; }
.draw-player.tbd { color: #ced4da; font-style: italic; }
.draw-player-name { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-transform: capitalize; }
.draw-player-check { color: #a5d051; font-size: 11px; }

.draw-score {
	text-align: center;
	font-size: 10px;
	color: #6c757d;
	padding: 2px 8px;
	background: #f8f9fa;
	border-top: 1px solid #f0f0f0;
	font-weight: 600;
}

.draw-connector {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	width: 20px;
	padding: 8px 0;
}
.draw-connector-line {
	flex: 1;
	border-right: 2px solid #dee2e6;
	margin: 2px 0;
}
@media (max-width: 768px) {
	#btn-pdf { display: none !important; }
}
</style>

<script>
console.log('admin/draws.php script loaded');
$(function(){
	console.log('jQuery ready - baseurl:', '<?=base_url()?>');
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];
	var currentPartidos = [], currentSembrados = {};

	// Click en pestañas
	$(document).on('click', '.draws-tab', function(){
		$('.draws-tab').removeClass('active');
		$(this).addClass('active');
		var cat = $(this).data('cat');
		var gen = $(this).data('gen');
		var tiene = $(this).data('tiene');
		$('#draws-category').val(cat);
		$('#draws-gender').val(gen);
		if(!tiene) {
			$('#draw-container').hide();
			$('#draw-titulo-activo').hide();
			$('#btn-pdf').hide();
			$('#draw-vacio').html(
				'<i class="fas fa-sitemap fa-3x" style="margin-bottom:15px;display:block;color:#dee2e6"></i>'
				+ '<p>Esta categoría aún no fue sorteada.</p>'
				+ '<div class="mt-3">'
				+ '<a href="'+baseurl+'admin/sorteo?cat='+cat+'&gen='+gen+'" class="btn btn-warning mt-2">'
				+ '<i class="fas fa-random"></i> Sortear automático</a>'
				+ ' '
				+ '<a href="'+baseurl+'admin/crearGruposManual?cat='+cat+'&gen='+gen+'" class="btn btn-info mt-2">'
				+ '<i class="fas fa-users"></i> Crear grupos manualmente</a>'
				+ '</div>'
			).show();
			return;
		}
		$('#btn-ver-draw').trigger('click');
	});

	// Cargar pestañas: todas las categorías, con indicador si tienen draw o no
	var categorias = <?=json_encode($categories)?>;
	function cargarTabs() {
		$.ajax({
			url: baseurl + 'admin/getDrawsDisponibles',
			type: 'POST',
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				console.log('getDrawsDisponibles OK:', res);
				var disponibles = {};
				if(res.draws) res.draws.forEach(function(d){ disponibles[d.category+'_'+d.gender] = true; });
				var $cont = $('#draws-tabs-container');
				$cont.html('');
				categorias.forEach(function(c) {
					['M','F'].forEach(function(gen) {
						var icono = gen == 'M' ? 'fa-male' : 'fa-female';
						var label = gen == 'M' ? 'Cab.' : 'Dam.';
						var tiene = disponibles[c.id+'_'+gen];
						var cls = tiene ? 'draws-tab' : 'draws-tab draws-tab-pending';
						var title = tiene ? '' : ' title="Sin sortear"';
						$cont.append('<button class="'+cls+'" data-cat="'+c.id+'" data-gen="'+gen+'" data-tiene="'+(tiene?1:0)+'"'+title+'>'
							+ '<i class="fas '+icono+'"></i> '+c.name+' '+label
							+ (tiene ? '' : ' <i class="fas fa-exclamation-circle" style="font-size:10px;opacity:0.6;margin-left:3px"></i>')
							+ '</button>');
					});
				});
				// Auto-cargar el primero con draw
				setTimeout(function(){
					var $primero = $('.draws-tab[data-tiene="1"]').first();
					if($primero.length) $primero.trigger('click');
				}, 100);
			},
			error: function(xhr, status, err) {
				console.error('ERROR en getDrawsDisponibles:', status, err, xhr.responseText);
			}
		});
	}
	cargarTabs();

	$('#btn-crear-grupos').on('click', function(){
		var cat = $('#draws-category').val();
		var gen = $('#draws-gender').val();
		if(!cat || !gen) { alert('Seleccioná categoría y género.'); return; }
		location.href = baseurl + 'admin/crearGruposManual?cat=' + cat + '&gen=' + gen;
	});

	$('#btn-ver-draw').on('click', function(){
		var cat = $('#draws-category').val();
		var gen = $('#draws-gender').val();
		if(!cat || !gen) { alert('Seleccioná categoría y género.'); return; }

		$.ajax({
			url: baseurl + 'admin/getDrawData',
			type: 'POST',
			data: { category: cat, gender: gen },
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				if(!res.action || !res.partidos.length) {
					$('#draw-container').hide();
					$('#draw-vacio').show();
					return;
				}
				$('#draw-vacio').hide();
				// Resaltar pestaña activa
				var cat = $('#draws-category').val();
				var gen = $('#draws-gender').val();
				$('.draws-tab').removeClass('active');
				$('.draws-tab[data-cat="'+cat+'"][data-gen="'+gen+'"]').addClass('active');
				// Mostrar título
				var catNombre = $('#draws-category option:selected').text();
				var genNombre = gen == 'M' ? 'Caballeros' : 'Damas';
				$('#draw-titulo-activo').text('Draw ' + catNombre + ' — ' + genNombre).show();
				$('#btn-pdf').show();
				currentPartidos = res.partidos;
				currentSembrados = res.sembrados || {};
				if(res.is_groups) {
					renderGroups(res.groups);
				} else {
					renderDraw(res.partidos, res.sembrados || {});
				}
				$('#draw-container').show();
			}
		});
	});

	function renderGroups(groups) {
		var html = '';
		for(var grupoNombre in groups) {
			var standings = groups[grupoNombre];
			html += '<div style="margin-bottom: 30px;">';
			html += '<h5 style="margin-bottom: 15px;">' + grupoNombre + '</h5>';
			html += '<table class="table table-sm table-bordered" style="max-width: 600px;">';
			html += '<thead class="table-light"><tr><th>Pos</th><th>Jugador</th><th>PJ</th><th>PG</th><th>PP</th><th>DS</th><th>DG</th><th>Pts</th></tr></thead>';
			html += '<tbody>';
			for(var i = 0; i < standings.length; i++) {
				var s = standings[i];
				html += '<tr>';
				html += '<td style="font-weight: bold;">' + (i+1) + '</td>';
				html += '<td>' + s.nombre.toLowerCase() + '</td>';
				html += '<td>' + s.pj + '</td>';
				html += '<td>' + s.pg + '</td>';
				html += '<td>' + s.pp + '</td>';
				html += '<td>' + (s.dg >= 0 ? '+' : '') + s.dg + '</td>';
				html += '<td>' + (s.dgg >= 0 ? '+' : '') + s.dgg + '</td>';
				html += '<td style="font-weight: bold; background: #f0fae0;">' + s.pts + '</td>';
				html += '</tr>';
			}
			html += '</tbody></table>';
			html += '</div>';
		}
		$('#draw-bracket').html(html);
	}

	function renderDraw(partidos, sembrados) {
		sembrados = sembrados || {};

		// Detectar la primera ronda real del draw (no asumir '1ra Ronda')
		var rondaInicioIdx = 0;
		for(var i = 0; i < RONDAS.length; i++) {
			if(partidos.some(function(p){ return p.ronda === RONDAS[i]; })) {
				rondaInicioIdx = i;
				break;
			}
		}
		var primeraRonda = partidos.filter(function(p){ return p.ronda === RONDAS[rondaInicioIdx]; });
		var totalPrimera = primeraRonda.length;
		// Cuántas rondas hay desde la primera real hasta el final
		var rondasCount = RONDAS.length - rondaInicioIdx;

		// Crear slots para cada ronda
		var byRonda = {};
		for(var r = 0; r < rondasCount; r++) {
			var rondaNombre = RONDAS[rondaInicioIdx + r];
			if(!rondaNombre) break;
			var slotCount = Math.max(1, totalPrimera / Math.pow(2, r));
			byRonda[rondaNombre] = new Array(Math.ceil(slotCount)).fill(null);
		}

		// Ubicar partidos en su posición correcta
		partidos.forEach(function(p) {
			if(byRonda[p.ronda] !== undefined) {
				var pos = p.bracket_pos !== null && p.bracket_pos !== undefined ? parseInt(p.bracket_pos) : 0;
				if(pos < byRonda[p.ronda].length) {
					byRonda[p.ronda][pos] = p;
				}
			}
		});

		// Solo incluir rondas que tienen slots definidos
		var rondasOrden = Object.keys(byRonda);

		var html = '<div class="draw-bracket">';

		rondasOrden.forEach(function(ronda, idx) {
			html += '<div class="draw-ronda">';
			html += '<div class="draw-ronda-titulo">' + ronda + '</div>';
			html += '<div class="draw-matches">';

			byRonda[ronda].forEach(function(p) {
				if(!p) {
					// TBD match
					html += '<div class="draw-match pendiente">';
					html += '<div class="draw-player tbd"><span class="draw-player-name">Por definir</span></div>';
					html += '<div class="draw-player tbd"><span class="draw-player-name">Por definir</span></div>';
					html += '</div>';
					return;
				}

				var esBYE = p.score === 'BYE';
				var jugado = p.ganador_id != null;
				html += '<div class="draw-match ' + (jugado ? 'jugado' : 'pendiente') + '">';

				// Jugador 1
				var esBYE1 = !p.jugador1_id || p.jugador1_id == 0;
				var c1 = (!esBYE1 && jugado) ? (p.ganador_id == p.jugador1_id ? 'ganador' : 'perdedor') : '';
				var seed1 = !esBYE1 && sembrados[p.jugador1_id] ? sembrados[p.jugador1_id] : 0;
				html += '<div class="draw-player ' + c1 + (esBYE1 ? ' tbd' : '') + '">';
				if(seed1) html += '<span style="color:#a5d051;font-weight:800;font-size:10px;margin-right:3px">['+seed1+']</span>';
				html += '<span class="draw-player-name">' + (p.jugador1 ? p.jugador1.toLowerCase() : 'BYE') + '</span>';
				if(!esBYE1 && p.ganador_id == p.jugador1_id) html += '<span class="draw-player-check"><i class="fas fa-check"></i></span>';
				html += '</div>';

				// Jugador 2
				var esBYE2 = !p.jugador2_id || p.jugador2_id == 0;
				var c2 = (!esBYE2 && jugado) ? (p.ganador_id == p.jugador2_id ? 'ganador' : 'perdedor') : '';
				var seed2 = !esBYE2 && sembrados[p.jugador2_id] ? sembrados[p.jugador2_id] : 0;
				html += '<div class="draw-player ' + c2 + (esBYE2 ? ' tbd' : '') + '">';
				if(seed2) html += '<span style="color:#a5d051;font-weight:800;font-size:10px;margin-right:3px">['+seed2+']</span>';
				html += '<span class="draw-player-name">' + (p.jugador2 ? p.jugador2.toLowerCase() : 'BYE') + '</span>';
				if(!esBYE2 && p.ganador_id == p.jugador2_id) html += '<span class="draw-player-check"><i class="fas fa-check"></i></span>';
				html += '</div>';

				if(p.score && p.score !== 'BYE') html += '<div class="draw-score">' + p.score + '</div>';

				html += '</div>';
			});

			html += '</div></div>';

			// Conector entre rondas
			if(idx < rondasOrden.length - 1) {
				html += '<div class="draw-connector">';
				var count = byRonda[ronda].length;
				for(var i = 0; i < count; i++) {
					html += '<div class="draw-connector-line"></div>';
				}
				html += '</div>';
			}
		});

		html += '</div>';
		$('#draw-bracket').html(html);

		// Inicializar drag & drop
		setTimeout(function() { initDragDropBracket(partidos); }, 100);
	}

	// Genera HTML del bracket clásico (izquierda→derecha) con inline styles para PDF
	function renderBracketForPDF(partidos, sembrados) {
		sembrados = sembrados || {};

		// Primera letra de cada palabra en mayúscula
		function tc(str) {
			if(!str) return 'BYE';
			return str.toLowerCase().replace(/\b\w/g, function(l){ return l.toUpperCase(); });
		}

		var rondaInicioIdx = 0;
		for(var i = 0; i < RONDAS.length; i++) {
			if(partidos.some(function(p){ return p.ronda === RONDAS[i]; })) { rondaInicioIdx = i; break; }
		}
		var primeraRonda = RONDAS[rondaInicioIdx];
		var totalPrimera = partidos.filter(function(p){ return p.ronda === primeraRonda; }).length;
		var rondasCount = RONDAS.length - rondaInicioIdx;

		var byRonda = {};
		for(var r = 0; r < rondasCount; r++) {
			var rn = RONDAS[rondaInicioIdx + r];
			byRonda[rn] = new Array(Math.ceil(Math.max(1, totalPrimera / Math.pow(2, r)))).fill(null);
		}
		partidos.forEach(function(p) {
			if(byRonda[p.ronda] !== undefined) {
				var pos = (p.bracket_pos !== null && p.bracket_pos !== undefined) ? parseInt(p.bracket_pos) : 0;
				if(pos < byRonda[p.ronda].length) byRonda[p.ronda][pos] = p;
			}
		});

		var rondasOrden = Object.keys(byRonda);
		var numRondas = rondasOrden.length;
		var containerW = 720;
		var connW = 14;
		// Primera columna 1.8x más ancha que las siguientes
		var totalConns = connW * (numRondas - 1);
		var unitW = Math.floor((containerW - totalConns) / (numRondas + 0.8));
		var firstColW = Math.floor(unitW * 1.8);

		function colWidth(idx) { return idx === 0 ? firstColW : unitW; }
		function colFs(idx)    { return idx === 0 ? 11 : 10; }

		var html = '<div style="display:flex;align-items:stretch;background:#fff;font-family:Arial,Helvetica,sans-serif;width:' + containerW + 'px;box-sizing:border-box;">';

		rondasOrden.forEach(function(ronda, idx) {
			var slots = byRonda[ronda];
			var cw = colWidth(idx);
			var fs = colFs(idx);
			html += '<div style="width:' + cw + 'px;flex-shrink:0;display:flex;flex-direction:column;">';
			html += '<div style="text-align:center;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;color:#6c757d;padding:5px 4px;background:#f1f3f5;border:1px solid #dee2e6;border-bottom:none;margin:0 3px;">' + ronda + '</div>';
			html += '<div style="display:flex;flex-direction:column;justify-content:space-around;flex:1;padding:5px 3px;gap:5px;">';

			slots.forEach(function(p) {
				if(!p) {
					html += '<div style="border:1px solid #dee2e6;border-radius:4px;overflow:hidden;">';
					html += '<div style="padding:4px 7px;font-size:' + fs + 'px;color:#ced4da;font-style:italic;min-height:26px;display:flex;align-items:center;">Por definir</div>';
					html += '<div style="padding:4px 7px;font-size:' + fs + 'px;color:#ced4da;font-style:italic;border-top:1px solid #f0f0f0;min-height:26px;display:flex;align-items:center;">Por definir</div>';
					html += '</div>';
					return;
				}
				var jugado = p.ganador_id != null;
				html += '<div style="border:1px solid ' + (jugado ? '#a5d051' : '#dee2e6') + ';border-radius:4px;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,0.05);">';
				[[p.jugador1_id, p.jugador1, p.ganador_id == p.jugador1_id],
				 [p.jugador2_id, p.jugador2, p.ganador_id == p.jugador2_id]].forEach(function(pl, pi) {
					var pid = pl[0], pname = pl[1], isWin = jugado && pl[2];
					var bg = isWin ? '#f0fae0' : '#fff';
					var col = !jugado ? '#333' : (isWin ? '#2d6a00' : '#adb5bd');
					var dec = jugado && !isWin ? 'line-through' : 'none';
					var fw = isWin ? '700' : 'normal';
					var seed = sembrados[pid] ? '<span style="color:#a5d051;font-weight:800;font-size:8px;margin-right:2px">[' + sembrados[pid] + ']</span>' : '';
					var name = tc(pname);
					var bt = pi === 1 ? 'border-top:1px solid #f0f0f0;' : '';
					html += '<div style="padding:4px 7px;font-size:' + fs + 'px;background:' + bg + ';color:' + col + ';text-decoration:' + dec + ';font-weight:' + fw + ';min-height:26px;display:flex;align-items:center;' + bt + 'white-space:nowrap;overflow:hidden;">';
					html += seed + '<span style="overflow:hidden;text-overflow:ellipsis;text-decoration:' + dec + ';">' + name + '</span></div>';
				});
				if(p.score && p.score !== 'BYE') {
					html += '<div style="text-align:center;font-size:8px;color:#6c757d;background:#f8f9fa;padding:2px 5px;border-top:1px solid #f0f0f0;font-weight:600;">' + p.score + '</div>';
				}
				html += '</div>';
			});

			html += '</div></div>';

			// Conector entre rondas
			if(idx < rondasOrden.length - 1) {
				var cnt = slots.length;
				html += '<div style="width:' + connW + 'px;flex-shrink:0;display:flex;flex-direction:column;justify-content:space-around;padding:5px 0;">';
				for(var ci = 0; ci < cnt; ci++) {
					html += '<div style="flex:1;border-right:1px solid #dee2e6;margin:1px 0;"></div>';
				}
				html += '</div>';
			}
		});

		html += '</div>';
		return html;
	}

	// PDF DOWNLOAD — A4 portrait, alta calidad, bracket clásico izquierda→derecha
	$('#btn-pdf').on('click', function(){
		var cat = $('#draws-category').val();
		var gen = $('#draws-gender').val();
		var catNombre = $('#draws-category option:selected').text();
		var genNombre = gen == 'M' ? 'Caballeros' : 'Damas';

		if(!currentPartidos || !currentPartidos.length) { alert('No hay draw cargado.'); return; }

		var $tmp = $('<div>').css({ position: 'fixed', left: '-9999px', top: 0, zIndex: -1, background: '#fff' }).appendTo('body');
		$tmp.html(renderBracketForPDF(currentPartidos, currentSembrados));

		html2canvas($tmp[0].firstElementChild, {
			scale: 3,
			backgroundColor: '#ffffff',
			useCORS: true,
			logging: false
		}).then(function(canvas) {
			$tmp.remove();
			var imgData = canvas.toDataURL('image/png');
			var pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
			var pageW = pdf.internal.pageSize.getWidth();
			var pageH = pdf.internal.pageSize.getHeight();

			var logoImg = new Image();
			logoImg.crossOrigin = 'anonymous';
			logoImg.src = 'https://www.baltc.net/torneo/static/img/logo.png';

			var finalizarPDF = function(conLogo) {
				var headerH = 32;
				if(conLogo) {
					var la = logoImg.naturalWidth / logoImg.naturalHeight;
					var lh = 20, lw = lh * la;
					pdf.addImage(logoImg, 'PNG', 6, 5, lw, lh);
					pdf.setTextColor(26, 26, 46);
					pdf.setFontSize(13);
					pdf.setFont('helvetica', 'bold');
					pdf.text('TORNEO INTERNO DE SINGLES — BALTC', lw + 10, 13);
					pdf.setFontSize(9);
					pdf.setFont('helvetica', 'normal');
					pdf.setTextColor(80, 80, 80);
					pdf.text('Categoría ' + catNombre + ' — ' + genNombre, lw + 10, 22);
				} else {
					pdf.setFontSize(13);
					pdf.setFont('helvetica', 'bold');
					pdf.setTextColor(26, 26, 46);
					pdf.text('TORNEO INTERNO BALTC — ' + catNombre + ' ' + genNombre, pageW/2, 16, { align: 'center' });
				}
				pdf.setDrawColor(165, 208, 81);
				pdf.setLineWidth(1.2);
				pdf.line(0, headerH, pageW, headerH);

				var availW = pageW - 16;
				var availH = pageH - headerH - 14;
				var ratio = canvas.width / canvas.height;
				var imgW = availW, imgH = imgW / ratio;
				if(imgH > availH) { imgH = availH; imgW = imgH * ratio; }
				pdf.addImage(imgData, 'PNG', (pageW - imgW) / 2, headerH + 4, imgW, imgH);

				pdf.setFontSize(7);
				pdf.setTextColor(150);
				pdf.text('Buenos Aires Lawn Tennis Club — baltc.net/torneo', pageW/2, pageH - 4, { align: 'center' });
				pdf.save('Draw_' + catNombre + '_' + genNombre + '_BALTC.pdf');
			};

			logoImg.onload = function() { finalizarPDF(true); };
			logoImg.onerror = function() { finalizarPDF(false); };
		});
	});
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
// Make jsPDF available
window.jsPDF = window.jspdf.jsPDF;

// ========== DRAG & DROP BRACKETS ==========
var sortables = {};

function initDragDropBracket(partidos) {
	// Destruir sortables anteriores
	Object.keys(sortables).forEach(function(key) {
		if(sortables[key]) sortables[key].destroy();
	});
	sortables = {};

	// Agregar data-match-id a cada match
	document.querySelectorAll('.draw-match').forEach(function(match, idx) {
		if(partidos && partidos[idx]) {
			match.dataset.matchId = partidos[idx].id;
		}
	});

	// Inicializar SortableJS en cada ronda
	document.querySelectorAll('.draw-matches').forEach(function(container) {
		var rondaDiv = container.closest('.draw-ronda');
		var rondaTitulo = rondaDiv ? rondaDiv.querySelector('.draw-ronda-titulo').textContent.trim() : 'Unknown';
		var category = $('#draws-category').val();
		var gender = $('#draws-gender').val();

		sortables[rondaTitulo] = Sortable.create(container, {
			animation: 150,
			ghostClass: 'sortable-ghost',
			onEnd: function(evt) {
				updateBracketPositions(category, gender, rondaTitulo, container);
			}
		});
	});
}

function updateBracketPositions(category, gender, ronda, container) {
	var positions = [];
	var matches = container.querySelectorAll('.draw-match');

	matches.forEach(function(match, idx) {
		var matchId = match.dataset.matchId;
		if(matchId) {
			positions.push({
				match_id: parseInt(matchId),
				bracket_pos: idx
			});
		}
	});

	if(positions.length === 0) return;

	$.ajax({
		url: baseurl + 'admin/updateBracketPositions',
		type: 'POST',
		data: {
			category: category,
			gender: gender,
			ronda: ronda,
			positions: JSON.stringify(positions)
		},
		headers: { 'X-Auth-Token': token },
		success: function(res) {
			if(res.action) {
				loadDrawData();
			} else {
				console.log('Error:', res.msg);
			}
		},
		error: function(e) {
			console.log('Error de conexión', e);
		}
	});
}

// Estilos para drag & drop
var style = document.createElement('style');
style.textContent = `
	.draw-matches {
		cursor: grab;
	}
	.draw-match {
		cursor: grab;
		transition: opacity 0.2s;
		border-radius: 4px;
	}
	.draw-match:active {
		cursor: grabbing;
	}
	.sortable-ghost {
		opacity: 0.4;
		background: rgba(165, 208, 81, 0.1);
	}
`;
document.head.appendChild(style);
</script>
