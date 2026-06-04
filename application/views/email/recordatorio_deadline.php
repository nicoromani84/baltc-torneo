<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Recordatorio de partido</title>
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
			<h1 style="margin-top:0; color:#4c4480;">Recordatorio de partido pendiente</h1>
			<p style="margin:0 0 20px 0;">Hola <strong><?=ucwords(strtolower($nombre))?></strong>, te recordamos que tenés un partido pendiente en el Torneo Interno de Singles del BALTC.</p>

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
						<td style="padding:8px 0; color:#888; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; border-top:1px solid #dee2e6;">Rival</td>
						<td style="padding:8px 0; font-weight:700; color:#333; border-top:1px solid #dee2e6; text-transform:capitalize;"><?=ucwords(strtolower($rival))?></td>
					</tr>
					<tr>
						<td style="padding:8px 0; color:#888; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; border-top:1px solid #dee2e6;">Fecha límite</td>
						<td style="padding:8px 0; font-weight:800; color:#c0392b; font-size:15px; border-top:1px solid #dee2e6;"><?=$deadline?></td>
					</tr>
				</table>
			</div>

			<p style="color:#555; font-size:14px; background:#fff8e1; border-left:4px solid #f0ad4e; padding:12px 16px; border-radius:4px;">
				Tenés hasta esta fecha para jugarlo. Ponete en contacto con tu rival para coordinarlo antes del deadline.
			</p>

			<p style="color:#555; font-size:14px; background:#f0f4ff; border-left:4px solid #4a90d9; padding:12px 16px; border-radius:4px;">
				💡 Si tu partido se juega con luz artificial (a partir de las 18hs) coordiná la reserva de cancha con Secretaría con anticipación.
			</p>

			<p style="color:#666; font-size:14px;">Seguí el torneo en <a href="https://www.baltc.net/torneo" style="color:#7a9e3a;">baltc.net/torneo</a></p>
			<p style="color:#666; font-size:14px;">Muchas gracias,<br><strong>Secretaría BALTC</strong></p>
		</div>
	</div>
</body>
</html>
