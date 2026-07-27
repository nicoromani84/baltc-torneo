<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Inscripción confirmada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style type="text/css">
	@media screen {
	  @font-face {
	    font-family: 'Lato';
	    font-style: normal;
	    font-weight: 400;
	    src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
	  }
	  body { font-family: "Lato", "Lucida Grande", "Lucida Sans Unicode", Tahoma, Sans-Serif; }
	}
    </style>
</head>
<body style="padding:0;margin:0;background:#f4f4f4;">
	<div style="max-width:600px;margin:20px auto;border:1px solid #e9eaef;border-radius:8px;overflow:hidden;background:#fff;">

		<!-- HEADER -->
		<div style="padding:20px 24px;background:#ffffff;display:flex;align-items:center;border-bottom:3px solid #a5d051;">
			<img src="https://www.baltc.net/torneo/static/img/logo.png" alt="BALTC" style="height:50px;width:auto;" />
			<div style="margin-left:16px;">
				<div style="color:#1a1a2e;font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Buenos Aires Lawn Tennis Club</div>
				<div style="color:#555;font-size:11px;">Torneo Interno de Dobles</div>
			</div>
		</div>


		<!-- BODY -->
		<div style="padding:32px 24px;">
			<h1 style="margin:0 0 8px 0;color:#1a1a2e;font-size:24px;">¡Estás inscripto!</h1>
			<p style="color:#555;font-size:16px;margin:0 0 24px 0;">Hola <strong style="text-transform:capitalize;"><?=ucwords(strtolower($nombre))?></strong>, tu inscripción al Torneo Interno de Dobles del BALTC fue confirmada.</p>

			<div style="background:#f8f9fa;border-radius:8px;padding:20px;margin-bottom:24px;">
				<table style="width:100%;border-collapse:collapse;">
					<tr>
						<td style="padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #eee;">Categoría</td>
						<td style="padding:8px 0;font-weight:700;color:#333;border-bottom:1px solid #eee;"><?=$category?></td>
					</tr>
					<tr>
						<td style="padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;border-bottom:1px solid #eee;">Compañero</td>
						<td style="padding:8px 0;font-weight:700;color:#333;border-bottom:1px solid #eee;text-transform:capitalize;"><?=ucwords(strtolower($partner))?></td>
					</tr>
					<tr>
						<td style="padding:8px 0;color:#888;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;">Inicio del torneo</td>
						<td style="padding:8px 0;font-weight:700;color:#5a7a2e;">10 de agosto 2026</td>
					</tr>
				</table>
			</div>

			<div style="background:#fff8e1;border:1px solid #ffe082;border-radius:6px;padding:14px 16px;margin-bottom:24px;">
				<p style="margin:0;color:#7a6000;font-size:14px;">⚠️ Tu categoría puede quedar sujeta a revisión por parte de la organización.</p>
			</div>

			<p style="color:#555;font-size:14px;">Seguí los resultados y el draw en <a href="https://www.baltc.net/torneo" style="color:#5a7a2e;font-weight:700;">baltc.net/torneo</a></p>
			<p style="color:#555;font-size:14px;">Muchas gracias,<br><strong>Secretaría BALTC</strong></p>
		</div>

		<!-- FOOTER -->
		<div style="padding:16px 24px;background:#f4f4f4;border-top:1px solid #e9eaef;text-align:center;">
			<p style="margin:0;color:#aaa;font-size:12px;">Buenos Aires Lawn Tennis Club — <a href="https://www.baltc.net/torneo" style="color:#aaa;">baltc.net/torneo</a></p>
		</div>
	</div>
</body>
</html>
