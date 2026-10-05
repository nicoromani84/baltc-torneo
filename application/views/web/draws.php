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
<div class="draws-container">
	<h2><i class="fas fa-sitemap"></i> Draws</h2>

	<!-- Categorías como cards -->
	<div class="categories-grid" id="categories-grid"></div>

	<div id="draws-content" style="display:none;">
		<div id="draw-title" class="draw-title"></div>

		<!-- Navegación entre rondas -->
		<div id="draw-nav" class="draw-nav" style="display:none">
			<button class="btn btn-sm btn-outline" id="btn-prev-ronda"><i class="fas fa-chevron-left"></i></button>
			<span id="draws-ronda-label" class="draws-ronda-label"></span>
			<button class="btn btn-sm btn-outline" id="btn-next-ronda"><i class="fas fa-chevron-right"></i></button>
		</div>

		<div id="draw-bracket" class="draw-bracket"></div>
	</div>

	<div id="draws-empty" style="display:none;" class="alert alert-info">
		<i class="fas fa-sitemap"></i> <span id="empty-message">Seleccioná una categoría</span>
	</div>
</div>

<style>
.draws-container {
	padding: 20px;
}

.categories-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 12px;
	margin-bottom: 30px;
}

.category-card {
	background: rgba(165,208,81,0.1);
	border: 2px solid rgba(165,208,81,0.3);
	border-radius: 8px;
	padding: 16px;
	cursor: pointer;
	transition: all 0.3s;
	text-align: center;
	font-weight: 600;
	color: rgba(255,255,255,0.8);
}

.category-card:hover {
	background: rgba(165,208,81,0.2);
	border-color: rgba(165,208,81,0.6);
	transform: translateY(-2px);
}

.category-card.active {
	background: #a5d051;
	border-color: #a5d051;
	color: #1a1a2e;
}

.category-card.my-category {
	border-color: #a5d051;
	background: rgba(165,208,81,0.15);
}

.my-category-badge {
	display: inline-block;
	margin-left: 6px;
	font-size: 14px;
}

.draw-title {
	font-size: 18px;
	font-weight: bold;
	color: #a5d051;
	margin-bottom: 15px;
	padding-bottom: 10px;
	border-bottom: 2px solid rgba(165,208,81,0.3);
	text-align: center;
}

.draw-nav {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 15px;
	margin-bottom: 20px;
}

.draws-ronda-label {
	color: #fff;
	font-weight: 700;
	font-size: 14px;
	min-width: 180px;
	text-align: center;
}

.draw-nav .btn {
	background: rgba(165,208,81,0.1);
	border: 1px solid rgba(165,208,81,0.3);
	color: #a5d051;
	border-radius: 6px;
	padding: 6px 12px;
	transition: all 0.2s;
}

.draw-nav .btn:hover:not(:disabled) {
	background: rgba(165,208,81,0.2);
	border-color: #a5d051;
}

.draw-nav .btn:disabled {
	opacity: 0.3;
	cursor: not-allowed;
}

.draw-bracket {
	overflow-x: auto;
	padding-bottom: 20px;
}

.bracket-row {
	display: flex;
	gap: 0;
	min-width: max-content;
}

.bracket-column {
	flex: 0 0 200px;
	display: flex;
	flex-direction: column;
	min-width: 160px;
}

.column-title {
	text-align: center;
	font-size: 11px;
	font-weight: bold;
	text-transform: uppercase;
	color: rgba(255,255,255,0.6);
	padding: 8px 6px;
	background: rgba(0,0,0,0.3);
	border: 1px solid rgba(165,208,81,0.2);
	margin: 0 4px;
}

.bracket-column.activa .column-title {
	color: #a5d051;
	background: rgba(165,208,81,0.1);
	border-color: rgba(165,208,81,0.4);
}

.bracket-column.activa .column-title::after {
	content: " EN JUEGO";
	font-size: 9px;
	background: rgba(165,208,81,0.2);
	color: #a5d051;
	padding: 1px 6px;
	border-radius: 8px;
	margin-left: 4px;
}

.matches {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	flex: 1;
	padding: 8px 4px;
	gap: 8px;
}

.match {
	background: rgba(0,0,0,0.3);
	border: 1px solid rgba(255,255,255,0.1);
	border-radius: 8px;
	overflow: hidden;
}

.match.jugado {
	border-color: rgba(165,208,81,0.4);
}

.match-player {
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

.match-player:last-child {
	border-bottom: none;
}

.match-player.ganador {
	color: #a5d051;
	font-weight: 800;
}

.match-player.perdedor {
	color: rgba(255,255,255,0.3);
	text-decoration: line-through;
	font-weight: 400;
}

.match-player.tbd {
	color: rgba(255,255,255,0.3);
	font-style: italic;
	font-weight: 400;
}

.match-player-name {
	flex: 1;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}

.match-seed {
	color: #a5d051;
	font-weight: 800;
	font-size: 10px;
	margin-right: 3px;
}

.match-check {
	font-size: 11px;
	color: #a5d051;
}

.match-score {
	text-align: center;
	font-size: 11px;
	color: #a5d051;
	padding: 3px 8px;
	background: rgba(165,208,81,0.1);
	font-weight: 700;
}

.connector {
	display: flex;
	flex-direction: column;
	justify-content: space-around;
	width: 16px;
	padding: 8px 0;
}

.connector-line {
	flex: 1;
	border-right: 1px solid rgba(165,208,81,0.3);
	margin: 2px 0;
}
</style>

<script>
$(function() {
	var baseUrl = '<?php echo base_url(); ?>';
	var categories = <?php echo json_encode($categories); ?>;
	var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];

	var currentCategory = null;
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

	function renderCategories() {
		var miCat = '<?php echo $mi_category; ?>';
		var html = '';

		// Ordenar: mi categoría primero
		var categoriesOrdenadas = [];
		if(miCat) {
			var myCat = categories.find(function(c) { return c.id == miCat; });
			if(myCat) categoriesOrdenadas.push(myCat);
		}
		categories.forEach(function(cat) {
			if(!miCat || cat.id != miCat) categoriesOrdenadas.push(cat);
		});

		categoriesOrdenadas.forEach(function(cat) {
			var isMyCat = miCat && cat.id == miCat;
			html += '<div class="category-card' + (isMyCat ? ' my-category' : '') + '" data-id="' + cat.id + '">';
			html += cat.name;
			if(isMyCat) html += ' <span class="my-category-badge">⭐</span>';
			html += '</div>';
		});
		$('#categories-grid').html(html);

		$(document).on('click', '.category-card', function() {
			var catId = $(this).data('id');
			selectCategory(catId);
		});
	}

	function selectCategory(catId) {
		$('.category-card').removeClass('active');
		$('.category-card[data-id="' + catId + '"]').addClass('active');
		currentCategory = catId;
		loadDraw(catId);
	}

	function loadDraw(catId) {
		$.ajax({
			url: baseUrl + 'draws/getData',
			type: 'POST',
			dataType: 'json',
			data: {
				category: catId,
				gender: 'X'
			},
			success: function(res) {
				$('#draw-bracket').html('');
				if (!res.action || !res.partidos || res.partidos.length === 0) {
					$('#empty-message').text('No hay partidos en esta categoría');
					$('#draws-empty').show();
					$('#draws-content').hide();
					return;
				}

				$('#draws-empty').hide();
				todosPartidos = res.partidos;
				sembradosActivos = res.sembrados || {};

				// Calcular primera ronda real
				rondaInicioIdx = getRondaInicio();

				// Determinar ronda activa: primera con pendientes, o última completada
				rondaActivaIdx = rondaInicioIdx;
				for(var i = rondaInicioIdx; i < RONDAS.length; i++) {
					var rp = todosPartidos.filter(function(p){ return p.ronda === RONDAS[i]; });
					if(rp.length > 0) {
						var pend = rp.filter(function(p){ return !p.ganador_id; });
						if(pend.length > 0) { rondaActivaIdx = i; break; }
						rondaActivaIdx = i;
					}
				}

				var catName = $('.category-card.active').text();
				$('#draw-title').text(catName + ' — Mixto');

				renderBracket();
				$('#draws-content').show();
			},
			error: function(xhr, status, err) {
				console.error('Error:', err);
				$('#empty-message').text('Error al cargar el draw');
				$('#draws-empty').show();
				$('#draws-content').hide();
			}
		});
	}

	function renderBracket() {
		var rondasMostrar = [RONDAS[rondaActivaIdx]];
		if(RONDAS[rondaActivaIdx + 1]) rondasMostrar.push(RONDAS[rondaActivaIdx + 1]);

		$('#btn-prev-ronda').prop('disabled', rondaActivaIdx <= rondaInicioIdx);
		$('#btn-next-ronda').prop('disabled', rondaActivaIdx >= RONDAS.length - 1);
		$('#draws-ronda-label').text(RONDAS[rondaActivaIdx] + (RONDAS[rondaActivaIdx+1] ? ' + ' + RONDAS[rondaActivaIdx+1] : ''));
		$('#draw-nav').show();

		// Slots de la primera ronda real
		var primera = todosPartidos.filter(function(p){ return p.ronda === RONDAS[rondaInicioIdx]; });
		var totalPrimera = primera.length;

		var html = '<div class="bracket-row">';
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

			html += '<div class="bracket-column' + (esActiva ? ' activa' : '') + '">';
			html += '<div class="column-title">' + ronda + '</div>';
			html += '<div class="matches">';

			slots.forEach(function(p) {
				if(!p) {
					html += '<div class="match">';
					html += '<div class="match-player tbd"><span class="match-player-name">por definir</span></div>';
					html += '<div class="match-player tbd"><span class="match-player-name">por definir</span></div>';
					html += '</div>';
					return;
				}

				var jugado = p.ganador_id != null;
				html += '<div class="match' + (jugado ? ' jugado' : '') + '">';

				// Jugador 1
				var esBYE1 = !p.jugador1_id || p.jugador1_id == 0;
				var c1 = (!esBYE1 && jugado) ? (p.ganador_id == p.jugador1_id ? 'ganador' : 'perdedor') : '';
				var seed1 = !esBYE1 && sembradosActivos[p.jugador1_id] ? sembradosActivos[p.jugador1_id] : 0;
				html += '<div class="match-player ' + c1 + (esBYE1 ? ' tbd' : '') + '">';
				if(seed1) html += '<span class="match-seed">['+seed1+']</span>';
				html += '<span class="match-player-name">' + (p.jugador1 ? p.jugador1.toLowerCase() : 'BYE') + '</span>';
				if(!esBYE1 && p.ganador_id == p.jugador1_id) html += '<span class="match-check"><i class="fas fa-check"></i></span>';
				html += '</div>';

				// Jugador 2
				var esBYE2 = !p.jugador2_id || p.jugador2_id == 0;
				var c2 = (!esBYE2 && jugado) ? (p.ganador_id == p.jugador2_id ? 'ganador' : 'perdedor') : '';
				var seed2 = !esBYE2 && sembradosActivos[p.jugador2_id] ? sembradosActivos[p.jugador2_id] : 0;
				html += '<div class="match-player ' + c2 + (esBYE2 ? ' tbd' : '') + '">';
				if(seed2) html += '<span class="match-seed">['+seed2+']</span>';
				html += '<span class="match-player-name">' + (p.jugador2 ? p.jugador2.toLowerCase() : 'BYE') + '</span>';
				if(!esBYE2 && p.ganador_id == p.jugador2_id) html += '<span class="match-check"><i class="fas fa-check"></i></span>';
				html += '</div>';

				if(p.score && p.score !== 'BYE') html += '<div class="match-score">' + p.score + '</div>';

				html += '</div>';
			});

			html += '</div></div>';

			// Conector
			if(idx < rondasMostrar.length - 1) {
				html += '<div class="connector">';
				for(var i = 0; i < slots.length; i++) html += '<div class="connector-line"></div>';
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

	renderCategories();
	var miCat = '<?php echo $mi_category; ?>';
	if(miCat && miCat !== '') {
		selectCategory(parseInt(miCat));
	} else if(categories.length > 0) {
		selectCategory(categories[0].id);
	}
});
</script>
</div>
</div>
