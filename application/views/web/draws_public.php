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
		body {
			background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
			color: #fff;
			min-height: 100vh;
			padding: 20px 0;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}
		header {
			background: rgba(0,0,0,0.7);
			padding: 20px 0;
			margin-bottom: 30px;
			border-bottom: 3px solid #a5d051;
		}
		.logo { text-align: center; margin-bottom: 15px; }
		.logo img { max-width: 120px; height: auto; }
		h1 {
			text-align: center;
			color: #a5d051;
			margin-bottom: 30px;
			font-size: 2.5em;
			text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
			font-weight: bold;
			letter-spacing: 2px;
		}
		.draws-container {
			padding: 20px 0;
		}
		.draw-section {
			background: rgba(0,0,0,0.6);
			border: 2px solid #a5d051;
			border-radius: 10px;
			padding: 20px;
			margin-bottom: 30px;
		}
		.draw-header {
			background: linear-gradient(135deg, #a5d051 0%, #7cb342 100%);
			color: #000;
			padding: 15px 20px;
			margin: -20px -20px 20px -20px;
			font-size: 1.4em;
			font-weight: bold;
			border-radius: 8px 8px 0 0;
			display: flex;
			align-items: center;
			gap: 15px;
		}
		.draw-gender {
			background: rgba(0,0,0,0.3);
			padding: 5px 12px;
			border-radius: 5px;
			font-size: 0.85em;
		}
		.bracket-wrapper {
			overflow-x: auto;
			margin: 20px 0;
		}
		.draws-bracket {
			display: flex;
			gap: 20px;
			min-width: min-content;
			padding: 10px;
		}
		.draws-ronda {
			flex-shrink: 0;
			min-width: 180px;
		}
		.draws-ronda-titulo {
			background: rgba(165,208,81,0.2);
			color: #a5d051;
			padding: 8px;
			text-align: center;
			font-weight: bold;
			font-size: 0.9em;
			border-radius: 5px;
			margin-bottom: 10px;
			border: 1px solid rgba(165,208,81,0.4);
		}
		.draws-matches {
			display: flex;
			flex-direction: column;
			gap: 8px;
		}
		.draws-match {
			background: rgba(0,0,0,0.4);
			border: 1px solid rgba(165,208,81,0.3);
			border-radius: 5px;
			overflow: hidden;
		}
		.draws-match.jugado {
			border-color: rgba(165,208,81,0.6);
			background: rgba(165,208,81,0.1);
		}
		.draws-player {
			display: flex;
			align-items: center;
			padding: 8px 10px;
			font-size: 0.85em;
			border-bottom: 1px solid rgba(165,208,81,0.1);
			gap: 5px;
			min-height: 32px;
		}
		.draws-player:last-child {
			border-bottom: none;
		}
		.draws-player.ganador {
			color: #a5d051;
			font-weight: 800;
			background: rgba(165,208,81,0.1);
		}
		.draws-player.perdedor {
			color: rgba(255,255,255,0.3);
			text-decoration: line-through;
		}
		.draws-player.tbd {
			color: rgba(255,255,255,0.4);
			font-style: italic;
		}
		.draws-player-name {
			flex: 1;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.draws-score {
			font-size: 0.75em;
			color: #a5d051;
			font-weight: bold;
		}
		.empty-message {
			text-align: center;
			padding: 40px 20px;
			color: rgba(255,255,255,0.7);
		}
		footer {
			text-align: center;
			padding: 20px;
			color: rgba(255,255,255,0.6);
			border-top: 1px solid rgba(165,208,81,0.3);
			margin-top: 40px;
			font-size: 0.9em;
		}
		.loading {
			text-align: center;
			padding: 30px;
			color: #a5d051;
		}
	</style>
</head>
<body>
	<header>
		<div class="container">
			<div class="logo">
				<img src="<?=asset_url('img/logo.png')?>" alt="BALTC">
			</div>
		</div>
	</header>

	<div class="container">
		<h1><i class="fas fa-sitemap"></i> Draws</h1>

		<div id="draws-container" class="draws-container">
			<div class="empty-message"><i class="fas fa-spinner fa-spin"></i> Cargando draws...</div>
		</div>
	</div>

	<footer>
		<p>&copy; 2026 BALTC Torneo - Todos los derechos reservados</p>
	</footer>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(function(){
			var baseurl = '<?=base_url()?>';
			var RONDAS = ['1ra Ronda','2da Ronda','Cuartos de Final','Semifinal','Final'];

			$.ajax({
				url: baseurl + 'draws?public=1',
				dataType: 'json',
				success: function(data) {
					if(!data.draws || data.draws.length === 0) {
						$('#draws-container').html('<div class="empty-message"><i class="fas fa-inbox"></i><p>No hay draws sorteados</p></div>');
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
						html += '<i class="fas fa-spinner fa-spin"></i> Cargando bracket...';
						html += '</div>';
						html += '</div>';

						loadBracket(draw.category, draw.gender, '#draw-' + draw.category + '-' + draw.gender);
					});
					$('#draws-container').html(html);
				},
				error: function() {
					$('#draws-container').html('<div class="empty-message"><i class="fas fa-exclamation-circle"></i><p>Error al cargar los draws</p></div>');
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
							$(selector).html('<div class="empty-message">Sin datos disponibles</div>');
							return;
						}

						var partidos = res.partidos;
						var rondasUnicas = [];
						$.each(partidos, function(i, p) {
							if($.inArray(p.ronda, rondasUnicas) === -1) {
								rondasUnicas.push(p.ronda);
							}
						});

						var html = '<div class="bracket-wrapper"><div class="draws-bracket">';
						$.each(rondasUnicas, function(i, ronda) {
							var rondaPartidos = $.grep(partidos, function(p) { return p.ronda === ronda; });
							html += '<div class="draws-ronda">';
							html += '<div class="draws-ronda-titulo">' + ronda + '</div>';
							html += '<div class="draws-matches">';

							$.each(rondaPartidos, function(j, p) {
								var jugado = p.ganador_id != null;
								html += '<div class="draws-match' + (jugado ? ' jugado' : '') + '">';

								var j1 = p.jugador1 || 'BYE';
								var c1 = (!p.jugador1_id && jugado) ? 'ganador' : (jugado && p.ganador_id == p.jugador1_id ? 'ganador' : (jugado ? 'perdedor' : ''));
								html += '<div class="draws-player ' + (p.jugador1_id ? '' : 'tbd') + ' ' + c1 + '">';
								html += '<span class="draws-player-name">' + j1 + '</span>';
								if(p.score && p.score !== 'BYE') {
									html += '<span class="draws-score">' + p.score.split('-')[0] + '</span>';
								}
								html += '</div>';

								var j2 = p.jugador2 || 'BYE';
								var c2 = (!p.jugador2_id && jugado) ? 'ganador' : (jugado && p.ganador_id == p.jugador2_id ? 'ganador' : (jugado ? 'perdedor' : ''));
								html += '<div class="draws-player ' + (p.jugador2_id ? '' : 'tbd') + ' ' + c2 + '">';
								html += '<span class="draws-player-name">' + j2 + '</span>';
								if(p.score && p.score !== 'BYE') {
									html += '<span class="draws-score">' + p.score.split('-')[1] + '</span>';
								}
								html += '</div>';

								html += '</div>';
							});

							html += '</div></div>';
						});
						html += '</div></div>';

						$(selector).html(html);
					},
					error: function() {
						$(selector).html('<div class="empty-message">Error al cargar bracket</div>');
					}
				});
			}
		});
	</script>
</body>
</html>
