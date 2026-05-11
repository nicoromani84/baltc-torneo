<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Resultado del partido</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
	@media screen {
	  @font-face {
	    font-family: 'Lato';
	    font-style: normal;
	    font-weight: 400;
	    src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
	  }
	  body {
	    font-family: "Lato", "Lucida Grande", "Lucida Sans Unicode", Tahoma, Sans-Serif;
	  }
	}
    </style>
</head>
<body style="padding:0;margin:0;">
	<div style="border:1px solid #e9eaef; border-radius:4px; max-width:600px; margin:0 auto;">
		<div style="padding:20px; background:#e9eaef;">
			<img src="https://www.baltc.net/torneo/static/img/logo.png" alt="Logo" style="display:block; width:210px;" />
		</div>
		<div style="padding:30px 20px;">
			<h1 style="margin-top:0; color:#4c4480;">¡Ganaste tu partido!</h1>
			<p style="margin:0 0 20px 0;">Hola <strong><?=ucwords(strtolower($nombre))?></strong>, te confirmamos el resultado de tu partido en el Torneo Interno de Singles del BALTC.</p>

			<div style="background:#f8f9fa; border-radius:8px; padding:20px; margin-bottom:20px;">
				<table style="width:100%; border-collapse:collapse;">
					<tr>
						<td style="padding:8px 0; color:#888; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Categoría</td>
						<td style="padding:8px 0; font-weight:700; color:#333;"><?=$categoria?></td>
					</tr>
					<tr>
						<td style="padding:8px 0; color:#888; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Ronda</td>
						<td style="padding:8px 0; font-weight:700; color:#333;"><?=$ronda?></td>
					</tr>
					<tr>
						<td style="padding:8px 0; color:#888; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; border-top:1px solid #dee2e6;">Resultado</td>
						<td style="padding:8px 0; border-top:1px solid #dee2e6;">
							<span style="font-weight:800; color:#5a7a2e; font-size:16px; text-transform:capitalize;"><?=ucwords(strtolower($ganador))?></span>
							<span style="color:#888; margin:0 6px;">def.</span>
							<span style="color:#888; text-decoration:line-through; text-transform:capitalize;"><?=ucwords(strtolower($perdedor))?></span>
						</td>
					</tr>
					<?php if(!empty($score)): ?>
					<tr>
						<td style="padding:8px 0; color:#888; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Score</td>
						<td style="padding:8px 0; font-weight:700; color:#5a7a2e; font-size:16px;"><?=$score?></td>
					</tr>
					<?php endif; ?>
				</table>
			</div>

			<p style="color:#666; font-size:14px;">Seguí atento la sección <strong>Partidos</strong> para conocer tu próximo rival.</p>
			<p style="color:#666; font-size:14px;">Seguí el torneo en <a href="https://www.baltc.net/torneo" style="color:#7a9e3a;">baltc.net/torneo</a></p>
			<p style="color:#666; font-size:14px;">Muchas gracias,<br><strong>Secretaría BALTC</strong></p>
		</div>
	</div>
</body>
</html>
