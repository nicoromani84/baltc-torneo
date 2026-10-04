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
		}
		header {
			background: rgba(0,0,0,0.5);
			padding: 20px 0;
			margin-bottom: 30px;
			border-bottom: 2px solid #a5d051;
		}
		.logo { text-align: center; margin-bottom: 20px; }
		.logo img { max-width: 150px; height: auto; }
		h1 {
			text-align: center;
			color: #a5d051;
			margin-bottom: 30px;
			font-size: 3em;
			text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
			font-weight: bold;
			letter-spacing: 2px;
		}
		.draws-container {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
			gap: 20px;
			padding: 20px;
		}
		.draw-card {
			background: rgba(0,0,0,0.6);
			border: 2px solid #a5d051;
			border-radius: 10px;
			padding: 20px;
			overflow: hidden;
			transition: transform 0.3s, box-shadow 0.3s;
		}
		.draw-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 10px 30px rgba(165, 208, 81, 0.3);
		}
		.draw-header {
			background: linear-gradient(135deg, #a5d051 0%, #7cb342 100%);
			color: #000;
			padding: 15px;
			margin: -20px -20px 15px -20px;
			font-size: 1.3em;
			font-weight: bold;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.draw-gender {
			background: rgba(0,0,0,0.3);
			padding: 5px 10px;
			border-radius: 5px;
			font-size: 0.9em;
		}
		.match-row {
			display: flex;
			align-items: center;
			padding: 10px 0;
			border-bottom: 1px solid rgba(165,208,81,0.2);
		}
		.match-row:last-child {
			border-bottom: none;
		}
		.player { flex: 1; }
		.player-1 { text-align: left; }
		.player-2 { text-align: right; }
		.vs {
			text-align: center;
			color: #a5d051;
			font-weight: bold;
			width: 60px;
			font-size: 0.9em;
		}
		.loading {
			text-align: center;
			padding: 20px;
			color: #a5d051;
		}
		.empty-message {
			text-align: center;
			padding: 40px 20px;
			color: rgba(255,255,255,0.7);
			font-size: 1.2em;
		}
		footer {
			text-align: center;
			padding: 20px;
			color: rgba(255,255,255,0.7);
			border-top: 1px solid rgba(165,208,81,0.3);
			margin-top: 40px;
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
			<div class="empty-message">Cargando draws...</div>
		</div>
	</div>

	<footer>
		<p>&copy; 2026 BALTC Torneo - Todos los derechos reservados</p>
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
						$('#draws-container').html('<div class="empty-message"><i class="fas fa-inbox"></i><p>No hay draws sorteados</p></div>');
						return;
					}

					var html = '';
					$.each(data.draws, function(i, draw) {
						var genderLabel = draw.gender === 'X' ? 'Mixto' : (draw.gender === 'M' ? 'Caballeros' : 'Damas');
						html += '<div class="draw-card">';
						html += '<div class="draw-header">';
						html += '<i class="fas fa-sitemap"></i> Categoría ' + draw.category;
						html += '<span class="draw-gender">' + genderLabel + '</span>';
						html += '</div>';
						html += '<div id="draw-' + draw.category + '-' + draw.gender + '" class="loading">';
						html += '<i class="fas fa-spinner fa-spin"></i> Cargando...';
						html += '</div>';
						html += '</div>';

						loadDrawMatches(draw.category, draw.gender, '#draw-' + draw.category + '-' + draw.gender);
					});
					$('#draws-container').html(html);
				},
				error: function() {
					$('#draws-container').html('<div class="empty-message"><i class="fas fa-exclamation-circle"></i><p>Error al cargar los draws</p></div>');
				}
			});

			function loadDrawMatches(cat, gen, selector) {
				$.ajax({
					url: baseurl + 'draws/getData',
					type: 'POST',
					data: { category: cat, gender: gen },
					dataType: 'json',
					success: function(res) {
						if(!res.partidos || res.partidos.length === 0) {
							$(selector).html('<div class="empty-message">Sin datos</div>');
							return;
						}

						var html = '';
						$.each(res.partidos, function(i, p) {
							var j1 = p.jugador1 || 'BYE';
							var j2 = p.jugador2 || 'BYE';
							var score = p.score || 'vs';
							html += '<div class="match-row">';
							html += '<div class="player player-1">' + j1 + '</div>';
							html += '<div class="vs">' + score + '</div>';
							html += '<div class="player player-2">' + j2 + '</div>';
							html += '</div>';
						});
						$(selector).html(html);
					},
					error: function() {
						$(selector).html('<div class="empty-message">Error al cargar</div>');
					}
				});
			}
		});
	</script>
</body>
</html>
