<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Draws - BALTC Torneo</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<style>
		* { margin: 0; padding: 0; box-sizing: border-box; }
		body {
			background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
			color: #fff;
			min-height: 100vh;
			padding: 20px 0;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}
		header {
			background: rgba(0,0,0,0.8);
			padding: 25px 0;
			margin-bottom: 40px;
			border-bottom: 3px solid #a5d051;
		}
		.logo { text-align: center; }
		.logo img { max-width: 140px; height: auto; }
		h1 {
			text-align: center;
			color: #a5d051;
			margin: 20px 0 10px 0;
			font-size: 2.8em;
			text-shadow: 2px 2px 8px rgba(0,0,0,0.9);
			font-weight: bold;
			letter-spacing: 3px;
		}
		.subtitle {
			text-align: center;
			color: rgba(255,255,255,0.6);
			font-size: 1.1em;
			margin-bottom: 30px;
		}
		.draw-section {
			background: rgba(0,0,0,0.7);
			border: 2px solid #a5d051;
			border-radius: 12px;
			padding: 30px;
			margin-bottom: 40px;
			box-shadow: 0 8px 32px rgba(165, 208, 81, 0.15);
		}
		.draw-header {
			background: linear-gradient(135deg, #a5d051 0%, #7cb342 100%);
			color: #000;
			padding: 18px 24px;
			margin: -30px -30px 25px -30px;
			font-size: 1.5em;
			font-weight: bold;
			border-radius: 10px 10px 0 0;
			display: flex;
			align-items: center;
			gap: 15px;
			box-shadow: 0 4px 15px rgba(165, 208, 81, 0.3);
		}
		.draw-gender {
			background: rgba(0,0,0,0.2);
			padding: 6px 14px;
			border-radius: 6px;
			font-size: 0.9em;
			font-weight: 600;
		}
		.bracket-container {
			overflow-x: auto;
			margin: 20px -30px -30px -30px;
			padding: 30px;
			background: rgba(0,0,0,0.5);
			border-radius: 0 0 10px 10px;
		}
		.bracket-svg {
			min-width: 100%;
			height: auto;
		}
		.match-box {
			background: linear-gradient(135deg, rgba(165,208,81,0.1) 0%, rgba(165,208,81,0.05) 100%);
			border: 1px solid rgba(165,208,81,0.4);
			border-radius: 6px;
			font-size: 13px;
			font-weight: 500;
			text-align: center;
		}
		.match-box.winner {
			border-color: #a5d051;
			background: rgba(165,208,81,0.15);
		}
		.player-entry {
			padding: 8px 12px;
			border-bottom: 1px solid rgba(165,208,81,0.2);
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.player-entry:last-child {
			border-bottom: none;
		}
		.player-entry.bye {
			color: rgba(255,255,255,0.4);
			font-style: italic;
		}
		.player-entry.winner {
			color: #a5d051;
			font-weight: bold;
		}
		.empty-message {
			text-align: center;
			padding: 50px 20px;
			color: rgba(255,255,255,0.7);
		}
		footer {
			text-align: center;
			padding: 30px 20px;
			color: rgba(255,255,255,0.5);
			border-top: 1px solid rgba(165,208,81,0.2);
			margin-top: 50px;
			font-size: 0.9em;
		}
		.loading {
			text-align: center;
			padding: 40px;
			color: #a5d051;
		}
		.container { max-width: 1400px; }
	</style>
</head>
<body>
	<header>
		<div class="logo">
			<img src="<?=asset_url('img/logo.png')?>" alt="BALTC">
			<h1><i class="fas fa-sitemap"></i> DRAWS</h1>
			<p class="subtitle">Cuadros de Sorteo</p>
		</div>
	</header>

	<div class="container">
		<div id="draws-container">
			<div class="empty-message"><i class="fas fa-spinner fa-spin"></i> Cargando draws...</div>
		</div>
	</div>

	<footer>
		<p>&copy; 2026 Buenos Aires Lawn Tennis Club - baltc.net/torneo</p>
	</footer>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(function(){
			var baseurl = '<?=base_url()?>';

			$.ajax({
				url: baseurl + 'draws?public=1',
				dataType: 'json',
				success: function(data) {
					if(!data.draws || data.draws.length === 0) {
						$('#draws-container').html('<div class="draw-section"><div class="empty-message"><i class="fas fa-inbox"></i><p style="margin-top: 15px;">No hay draws sorteados</p></div></div>');
						return;
					}

					var html = '';
					$.each(data.draws, function(i, draw) {
						var genderLabel = draw.gender === 'X' ? 'Mixto' : (draw.gender === 'M' ? 'Caballeros' : 'Damas');
						html += '<div class="draw-section">';
						html += '<div class="draw-header">';
						html += '<i class="fas fa-sitemap"></i> Categoría ' + draw.category;
						html += '<span class="draw-gender">' + genderLabel + '</span>';
						html += '</div>';
						html += '<div id="draw-' + draw.category + '-' + draw.gender + '" class="loading">';
						html += '<i class="fas fa-spinner fa-spin"></i> Renderizando bracket...';
						html += '</div>';
						html += '</div>';

						loadBracket(draw.category, draw.gender, '#draw-' + draw.category + '-' + draw.gender);
					});
					$('#draws-container').html(html);
				},
				error: function() {
					$('#draws-container').html('<div class="draw-section"><div class="empty-message"><i class="fas fa-exclamation-circle"></i><p>Error al cargar los draws</p></div></div>');
				}
			});

			function loadBracket(cat, gen, selector) {
				$.ajax({
					url: baseurl + 'draws/getData',
					type: 'POST',
					data: { category: cat, gender: gen },
					dataType: 'json',
					success: function(res) {
						if(!res.partidos || res.partidos.length === 0) {
							$(selector).html('<div class="bracket-container"><div style="text-align:center; padding:30px; color:rgba(255,255,255,0.5);">Sin datos disponibles</div></div>');
							return;
						}

						renderBracketPro(res.partidos, selector);
					},
					error: function() {
						$(selector).html('<div class="bracket-container"><div style="text-align:center; padding:30px; color:rgba(255,255,255,0.5);">Error al cargar bracket</div></div>');
					}
				});
			}

			function renderBracketPro(partidos, selector) {
				var rondas = {};
				$.each(partidos, function(i, p) {
					if(!rondas[p.ronda]) rondas[p.ronda] = [];
					rondas[p.ronda].push(p);
				});

				var rondaOrder = ['1ra Ronda', '2da Ronda', 'Cuartos de Final', 'Semifinal', 'Final'];
				var rondasPres = $.grep(rondaOrder, function(r) { return rondas[r]; });

				var matchHeight = 70;
				var matchWidth = 200;
				var colWidth = matchWidth + 100;
				var totalHeight = 800;
				var totalWidth = colWidth * rondasPres.length;

				var svg = '<svg class="bracket-svg" width="' + totalWidth + '" height="' + totalHeight + '" style="min-height: 800px;">';

				var positions = {};

				$.each(rondasPres, function(rondaIdx, ronda) {
					var x = rondaIdx * colWidth + 30;
					var matches = rondas[ronda];
					var spaceBetween = totalHeight / (matches.length + 1);

					$.each(matches, function(matchIdx, match) {
						var y = (matchIdx + 1) * spaceBetween - matchHeight / 2;
						var matchKey = ronda + '_' + matchIdx;
						positions[match.id] = { x: x + matchWidth, y: y + matchHeight / 2, ronda: ronda };

						var fillColor = match.ganador_id ? 'rgba(165,208,81,0.2)' : 'rgba(165,208,81,0.08)';
						svg += '<rect x="' + x + '" y="' + y + '" width="' + matchWidth + '" height="' + matchHeight + '" fill="' + fillColor + '" stroke="rgba(165,208,81,0.5)" stroke-width="1" rx="6"/>';

						var playerHeight = matchHeight / 2;
						var j1 = match.jugador1 ? match.jugador1.substring(0, 20) : 'BYE';
						var j2 = match.jugador2 ? match.jugador2.substring(0, 20) : 'BYE';
						var score1 = match.score && match.score.split('-')[0] ? match.score.split('-')[0] : '';
						var score2 = match.score && match.score.split('-')[1] ? match.score.split('-')[1] : '';

						svg += '<text x="' + (x + 8) + '" y="' + (y + playerHeight - 3) + '" font-size="12" fill="' + (match.ganador_id == match.jugador1_id ? '#a5d051' : '#fff') + '" font-weight="' + (match.ganador_id == match.jugador1_id ? 'bold' : 'normal') + '">' + j1 + (score1 ? ' ' + score1 : '') + '</text>';
						svg += '<text x="' + (x + 8) + '" y="' + (y + playerHeight * 2 - 3) + '" font-size="12" fill="' + (match.ganador_id == match.jugador2_id ? '#a5d051' : '#fff') + '" font-weight="' + (match.ganador_id == match.jugador2_id ? 'bold' : 'normal') + '">' + j2 + (score2 ? ' ' + score2 : '') + '</text>';
					});
				});

				svg += '</svg>';

				$(selector).html('<div class="bracket-container">' + svg + '</div>');
			}
		});
	</script>
</body>
</html>
