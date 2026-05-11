<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Confirmation Email</title>
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
	</style>
</head>
<body style="padding: 0px; margin: 0px;">
	<div style="border: 1px solid #e9eaef; border-radius: 4px;">
		<div style="padding: 20px; background: #e9eaef;"><img src="https://www.baltc.net/reservas/static/img/logo.png" alt="Logo" style="display: block; width: 210px;" /></div>
		<div style="padding: 20px">
			<h1 style="margin-top: 0px; color: #4c4480">Reserva desde el BALTC</h1>
		  	<p style="margin: 0">Hola <?=$club?>, tenes reserva <?=$cuando?> de <?=$desde?> a <?=$hasta?>hs.</p>
		  	<p>Jugadores: <span style="text-transform: capitalize;"><?=$partner?></span>.</p>
			<p style="margin: 0">Se anuncian en recepción.</p>
		</div>
	</div>
</body>
</html>