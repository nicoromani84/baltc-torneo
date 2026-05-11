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
</style>

<script>
$(function(){
	var baseurl = '<?=base_url()?>';
	var token = '<?=$token?>';
	var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];

	// Click en pestañas
	$(document).on('click', '.draws-tab', function(){
		$('.draws-tab').removeClass('active');
		$(this).addClass('active');
		var cat = $(this).data('cat');
		var gen = $(this).data('gen');
		$('#draws-category').val(cat);
		$('#draws-gender').val(gen);
		$('#btn-ver-draw').trigger('click');
	});

	// Cargar pestañas dinámicamente — solo las que tienen draw
	function cargarTabs() {
		$.ajax({
			url: baseurl + 'admin/getDrawsDisponibles',
			type: 'POST',
			headers: { 'X-Auth-Token': token },
			success: function(res) {
				var $cont = $('#draws-tabs-container');
				$cont.html('');
				if(!res.draws || !res.draws.length) {
					$cont.html('<span class="text-muted small">No hay draws sorteados aún.</span>');
					return;
				}
				res.draws.forEach(function(d) {
					var icono = d.gender == 'M' ? 'fa-male' : 'fa-female';
					var label = d.gender == 'M' ? 'Cab.' : 'Dam.';
					$cont.append('<button class="draws-tab" data-cat="'+d.category+'" data-gen="'+d.gender+'">'
						+ '<i class="fas '+icono+'"></i> '+d.categoria+' '+label
						+ '</button>');
				});
				// Auto-cargar el primero
				setTimeout(function(){
					$('.draws-tab').first().trigger('click');
				}, 100);
			}
		});
	}
	cargarTabs();

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
				renderDraw(res.partidos, res.sembrados || {});
				$('#draw-container').show();
			}
		});
	});

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
	}

	// PDF DOWNLOAD
	$('#btn-pdf').on('click', function(){
		var cat = $('#draws-category').val();
		var gen = $('#draws-gender').val();
		var catNombre = $('#draws-category option:selected').text();
		var genNombre = gen == 'M' ? 'Caballeros' : 'Damas';

		// Use html2canvas + jsPDF
		// Capturar el bracket interno completo (no el wrapper con overflow)
		var wrapper = document.getElementById('draw-bracket');
		var element = wrapper.querySelector('.draw-bracket') || wrapper;
		var fullWidth = element.scrollWidth;
		var fullHeight = element.scrollHeight;
		html2canvas(element, { 
			scale: 1.5, 
			backgroundColor: '#ffffff', 
			useCORS: true,
			scrollX: 0,
			scrollY: 0,
			width: fullWidth,
			height: fullHeight,
			windowWidth: fullWidth + 100
		}).then(function(canvas) {
			var imgData = canvas.toDataURL('image/png');
			var pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
			var pageW = pdf.internal.pageSize.getWidth();
			var pageH = pdf.internal.pageSize.getHeight();

			// Header background
			pdf.setFillColor(255, 255, 255);
			pdf.rect(0, 0, pageW, 40, 'F');

			// Logo
			var logoImg = new Image();
			logoImg.crossOrigin = 'anonymous';
			logoImg.src = 'https://www.baltc.net/torneo/static/img/logo.png';
			logoImg.onload = function() {
				// Logo - mantener proporcion
				var logoAspect = logoImg.naturalWidth / logoImg.naturalHeight;
				var logoH = 30;
				var logoW = logoH * logoAspect;
				pdf.addImage(logoImg, 'PNG', 6, 5, logoW, logoH);

				// Title
				pdf.setTextColor(26, 26, 46);
				pdf.setFontSize(18);
				pdf.setFont('helvetica', 'bold');
				pdf.text('TORNEO INTERNO DE SINGLES — BALTC', logoW + 12, 18);

				// Subtitle
				pdf.setFontSize(11);
				pdf.setFont('helvetica', 'normal');
				pdf.setTextColor(80, 80, 80);
				pdf.text('Categoría ' + catNombre + ' — ' + genNombre + '   |   Inicio: 19 de Mayo 2026', logoW + 12, 30);

				// Green accent line
				pdf.setDrawColor(165, 208, 81);
				pdf.setLineWidth(1.5);
				pdf.line(0, 40, pageW, 40);

				// Bracket image — fit entirely within page
				var maxW = pageW - 20;
				var maxH = pageH - 52;
				var ratio = canvas.width / canvas.height;
				var imgW = maxW;
				var imgH = imgW / ratio;
				if(imgH > maxH) {
					imgH = maxH;
					imgW = imgH * ratio;
				}
				var xOffset = (pageW - imgW) / 2;
				pdf.addImage(imgData, 'PNG', xOffset, 44, imgW, imgH);

				// Footer
				pdf.setFontSize(8);
				pdf.setTextColor(150, 150, 150);
				pdf.text('Buenos Aires Lawn Tennis Club — baltc.net/torneo', pageW/2, pageH - 5, { align: 'center' });

				pdf.save('Draw_' + catNombre + '_' + genNombre + '_BALTC.pdf');
			};
		});
	});
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
// Make jsPDF available
window.jsPDF = window.jspdf.jsPDF;
</script>
