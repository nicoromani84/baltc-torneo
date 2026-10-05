<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="draws-container">
	<h2><i class="fas fa-sitemap"></i> Draws</h2>

	<!-- Categorías como cards -->
	<div class="categories-grid" id="categories-grid"></div>

	<div id="draws-content" style="display:none;">
		<div id="draw-title" class="draw-title"></div>
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

.draw-title {
	font-size: 18px;
	font-weight: bold;
	color: #a5d051;
	margin-bottom: 20px;
	padding-bottom: 10px;
	border-bottom: 2px solid rgba(165,208,81,0.3);
}

.draw-bracket {
	overflow-x: auto;
}

.bracket-row {
	display: flex;
	gap: 20px;
	min-width: max-content;
	padding-bottom: 20px;
}

.bracket-column {
	flex: 0 0 200px;
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.column-title {
	text-align: center;
	font-size: 11px;
	font-weight: bold;
	text-transform: uppercase;
	color: #a5d051;
	padding: 8px;
	background: rgba(165,208,81,0.1);
	border: 1px solid rgba(165,208,81,0.3);
	border-radius: 4px;
}

.match {
	background: rgba(0,0,0,0.3);
	border: 1.5px solid rgba(165,208,81,0.4);
	border-radius: 4px;
	overflow: hidden;
	backdrop-filter: blur(10px);
}

.match-player {
	padding: 8px 10px;
	border-bottom: 1px solid rgba(165,208,81,0.2);
	font-size: 12px;
	color: rgba(255,255,255,0.9);
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.match-player:last-child {
	border-bottom: none;
}

.match-player.winner {
	background: rgba(165,208,81,0.15);
	color: #a5d051;
	font-weight: bold;
}

.match-player.loser {
	color: rgba(255,255,255,0.4);
	text-decoration: line-through;
}

.match-player-name {
	flex: 1;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	text-transform: capitalize;
}

.match-seed {
	color: #a5d051;
	font-weight: bold;
	font-size: 10px;
	margin-right: 4px;
}

.match-score {
	text-align: center;
	font-size: 10px;
	color: rgba(255,255,255,0.5);
	padding: 4px 8px;
	background: rgba(0,0,0,0.2);
	border-top: 1px solid rgba(165,208,81,0.2);
}

.tbd {
	color: rgba(255,255,255,0.3);
	font-style: italic;
}
</style>

<script>
$(function() {
	var baseUrl = '<?php echo base_url(); ?>';
	var categories = <?php echo json_encode($categories); ?>;
	var currentCategory = null;

	// Renderizar categorías como cards
	function renderCategories() {
		var html = '';
		categories.forEach(function(cat) {
			html += '<div class="category-card" data-id="' + cat.id + '">' + cat.name + '</div>';
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
				if (!res.action || !res.partidos || res.partidos.length === 0) {
					$('#empty-message').text('No hay partidos en esta categoría');
					$('#draws-empty').show();
					$('#draws-content').hide();
					return;
				}

				$('#draws-empty').hide();
				renderBracket(res.partidos, res.sembrados || {});

				var catName = $('.category-card.active').text();
				$('#draw-title').text(catName + ' — Mixto');

				$('#draws-content').show();
			},
			error: function(xhr, status, err) {
				console.error('Error:', err, xhr.responseText);
				$('#empty-message').text('Error al cargar el draw');
				$('#draws-empty').show();
				$('#draws-content').hide();
			}
		});
	}

	function renderBracket(partidos, sembrados) {
		var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];
		sembrados = sembrados || {};

		// Detectar la primera ronda real del draw
		var rondaInicioIdx = 0;
		for(var i = 0; i < RONDAS.length; i++) {
			if(partidos.some(function(p){ return p.ronda === RONDAS[i]; })) {
				rondaInicioIdx = i;
				break;
			}
		}
		var primeraRonda = partidos.filter(function(p){ return p.ronda === RONDAS[rondaInicioIdx]; });
		var totalPrimera = primeraRonda.length;
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

		// Compactar las posiciones para que no queden gaps
		for(var ronda in byRonda) {
			var compacted = [];
			for(var i = 0; i < byRonda[ronda].length; i++) {
				if(byRonda[ronda][i] !== null) {
					compacted.push(byRonda[ronda][i]);
				}
			}
			if(compacted.length === 0) {
				compacted.push(null);
			}
			byRonda[ronda] = compacted;
		}

		// Mostrar la ronda inicial y la siguiente
		var rondasAMostrar = [RONDAS[rondaInicioIdx]];
		if(rondaInicioIdx + 1 < RONDAS.length) {
			rondasAMostrar.push(RONDAS[rondaInicioIdx + 1]);
		}

		var html = '<div class="bracket-row">';

		rondasAMostrar.forEach(function(ronda) {
			if(!byRonda[ronda]) return;

			html += '<div class="bracket-column">';
			html += '<div class="column-title">' + ronda + '</div>';

			byRonda[ronda].forEach(function(p) {
				if(!p) {
					html += '<div class="match">';
					html += '<div class="match-player tbd">Por definir</div>';
					html += '<div class="match-player tbd">Por definir</div>';
					html += '</div>';
					return;
				}

				var jugado = p.ganador_id != null;
				html += '<div class="match ' + (jugado ? 'jugado' : 'pendiente') + '">';

				// Jugador 1
				var esBYE1 = !p.jugador1_id || p.jugador1_id == 0;
				var c1 = (!esBYE1 && jugado) ? (p.ganador_id == p.jugador1_id ? 'winner' : 'loser') : '';
				var seed1 = !esBYE1 && sembrados[p.jugador1_id] ? sembrados[p.jugador1_id] : 0;
				html += '<div class="match-player ' + c1 + (esBYE1 ? ' tbd' : '') + '">';
				if(seed1) html += '<span class="match-seed">['+seed1+']</span>';
				html += '<span class="match-player-name">' + (p.jugador1 ? p.jugador1.toLowerCase() : 'BYE') + '</span>';
				if(!esBYE1 && p.ganador_id == p.jugador1_id) html += '<span style="color:#a5d051;font-weight:bold;margin-left:4px">✓</span>';
				html += '</div>';

				// Jugador 2
				var esBYE2 = !p.jugador2_id || p.jugador2_id == 0;
				var c2 = (!esBYE2 && jugado) ? (p.ganador_id == p.jugador2_id ? 'winner' : 'loser') : '';
				var seed2 = !esBYE2 && sembrados[p.jugador2_id] ? sembrados[p.jugador2_id] : 0;
				html += '<div class="match-player ' + c2 + (esBYE2 ? ' tbd' : '') + '">';
				if(seed2) html += '<span class="match-seed">['+seed2+']</span>';
				html += '<span class="match-player-name">' + (p.jugador2 ? p.jugador2.toLowerCase() : 'BYE') + '</span>';
				if(!esBYE2 && p.ganador_id == p.jugador2_id) html += '<span style="color:#a5d051;font-weight:bold;margin-left:4px">✓</span>';
				html += '</div>';

				if(p.score && p.score !== 'BYE') html += '<div class="match-score">' + p.score + '</div>';

				html += '</div>';
			});

			html += '</div>';
		});

		html += '</div>';
		$('#draw-bracket').html(html);
	}

	// Inicializar
	renderCategories();
	if(categories.length > 0) {
		selectCategory(categories[0].id);
	}
});
</script>
