<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="draws-container">
	<h2><i class="fas fa-sitemap"></i> Draws</h2>

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
						<option value="X">Mixto</option>
					</select>
				</div>
				<div class="form-group col-md-5 mb-0">
					<button class="btn btn-primary" id="btn-cargar-draw">
						<i class="fas fa-eye"></i> Cargar Draw
					</button>
				</div>
			</div>
		</div>
	</div>

	<div id="draws-content" style="display:none;">
		<div id="draw-title" class="draw-title"></div>
		<div id="draw-bracket" class="draw-bracket"></div>
	</div>

	<div id="draws-empty" style="display:none;" class="alert alert-info">
		<i class="fas fa-sitemap"></i> <span id="empty-message">Seleccioná categoría y género para ver el draw</span>
	</div>
</div>

<style>
.draws-container {
	padding: 20px;
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
	var token = '<?php echo $token; ?>';

	$('#btn-cargar-draw').on('click', function() {
		var cat = $('#draws-category').val();
		var gen = $('#draws-gender').val();

		if (!cat || !gen) {
			$('#empty-message').text('Seleccioná categoría y género');
			$('#draws-empty').show();
			$('#draws-content').hide();
			return;
		}

		$.ajax({
			url: baseUrl + 'draws/getData',
			type: 'POST',
			dataType: 'json',
			data: {
				category: cat,
				gender: gen
			},
			success: function(res) {
				console.log('Draw data:', res);

				if (!res.action || !res.partidos || res.partidos.length === 0) {
					$('#empty-message').text('No hay partidos en este draw');
					$('#draws-empty').show();
					$('#draws-content').hide();
					return;
				}

				$('#draws-empty').hide();
				renderBracket(res.partidos, res.sembrados || {});

				var catName = $('#draws-category option:selected').text();
				var genName = gen === 'M' ? 'Caballeros' : (gen === 'F' ? 'Damas' : 'Mixto');
				$('#draw-title').text(catName + ' — ' + genName);

				$('#draws-content').show();
			},
			error: function(xhr, status, err) {
				console.error('Error:', err, xhr.responseText);
				$('#empty-message').text('Error al cargar el draw');
				$('#draws-empty').show();
				$('#draws-content').hide();
			}
		});
	});

	function renderBracket(partidos, sembrados) {
		const RONDAS = ['1ra Ronda', '2da Ronda', 'Cuartos de Final', 'Semifinal', 'Final'];

		var rondaInicioIdx = 0;
		for (var i = 0; i < RONDAS.length; i++) {
			if (partidos.some(p => p.ronda === RONDAS[i])) {
				rondaInicioIdx = i;
				break;
			}
		}

		var primeraRonda = partidos.filter(p => p.ronda === RONDAS[rondaInicioIdx]);
		var totalPrimera = primeraRonda.length;
		var rondasCount = RONDAS.length - rondaInicioIdx;

		var byRonda = {};
		for (var r = 0; r < rondasCount; r++) {
			var rondaNombre = RONDAS[rondaInicioIdx + r];
			if (!rondaNombre) break;
			var slotCount = Math.max(1, totalPrimera / Math.pow(2, r));
			byRonda[rondaNombre] = new Array(Math.ceil(slotCount)).fill(null);
		}

		partidos.forEach(p => {
			if (byRonda[p.ronda] !== undefined) {
				var pos = p.bracket_pos !== null && p.bracket_pos !== undefined ? parseInt(p.bracket_pos) : 0;
				if (pos < byRonda[p.ronda].length) {
					byRonda[p.ronda][pos] = p;
				}
			}
		});

		var rondasOrden = Object.keys(byRonda);
		var html = '<div class="bracket-row">';

		rondasOrden.forEach(function(ronda) {
			html += '<div class="bracket-column">';
			html += '<div class="column-title">' + ronda + '</div>';

			byRonda[ronda].forEach(function(p) {
				if (!p) {
					html += '<div class="match">';
					html += '<div class="match-player tbd">Por definir</div>';
					html += '<div class="match-player tbd">Por definir</div>';
					html += '</div>';
					return;
				}

				html += '<div class="match">';

				var j1Seed = sembrados[p.jugador1_id] ? '<span class="match-seed">[' + sembrados[p.jugador1_id] + ']</span>' : '';
				var j1Class = p.ganador_id === p.jugador1_id ? 'winner' : (p.ganador_id ? 'loser' : '');
				var j1Name = p.jugador1 ? p.jugador1.toLowerCase() : 'BYE';
				html += '<div class="match-player ' + j1Class + '">' + j1Seed + '<span class="match-player-name">' + j1Name + '</span></div>';

				var j2Seed = sembrados[p.jugador2_id] ? '<span class="match-seed">[' + sembrados[p.jugador2_id] + ']</span>' : '';
				var j2Class = p.ganador_id === p.jugador2_id ? 'winner' : (p.ganador_id ? 'loser' : '');
				var j2Name = p.jugador2 ? p.jugador2.toLowerCase() : 'BYE';
				html += '<div class="match-player ' + j2Class + '">' + j2Seed + '<span class="match-player-name">' + j2Name + '</span></div>';

				if (p.score && p.score !== 'BYE') {
					html += '<div class="match-score">' + p.score + '</div>';
				}

				html += '</div>';
			});

			html += '</div>';
		});

		html += '</div>';
		$('#draw-bracket').html(html);
	}
});
</script>
