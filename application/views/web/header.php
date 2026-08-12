<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!doctype html>
<html class="no-js" lang="es">
<head>
	<!-- META TAGS -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="description" content="Inscripción al torneo interno de dobles del BALTC">
	<meta name="author" content="">
	<!-- ICON -->
	<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
	<link rel="manifest" href="site.webmanifest">
	<link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#ffc40d">
	<meta name="theme-color" content="#ffffff">
	<meta property="og:type" content= "website" />
	<meta property="og:title" content="<?=$titulo?> <?=TITULO?>" />
	<meta property="og:site_name" content="<?=$titulo?> <?=TITULO?>" />
	<meta property="og:url" content="<?=base_url()?>" />
	<meta property="og:description" content="📆 Programación: Cada pareja coordina días/horarios. Plazo: Semana de lunes a domingo. Semis y Finales programadas. 🎾 Pelotas provistas. 📲 Carga de resultados autogestionada.">
	<meta property="og:image" content="https://www.baltc.net/torneo/static/img/thumb-nuevo.jpeg">
	<link rel="icon" href="https://www.baltc.net/wp-content/uploads/2016/09/cropped-favicon-1-32x32.png" sizes="32x32" />
	<link rel="icon" href="https://www.baltc.net/wp-content/uploads/2016/09/cropped-favicon-1-192x192.png" sizes="192x192" />
	<link rel="apple-touch-icon-precomposed" href="https://www.baltc.net/wp-content/uploads/2016/09/cropped-favicon-1-180x180.png" />
	<meta name="msapplication-TileImage" content="https://www.baltc.net/wp-content/uploads/2016/09/cropped-favicon-1-270x270.png" />

	<!-- TITULO -->
	<title> <?=$titulo?> <?=TITULO?> </title>
	<!--
	 	HOJAS DE ESTILO
	-->
	<!-- GOOGLE FONTS -->
	<link href='<?=asset_url('fonts')?>/google-fonts/font.css' rel='stylesheet' type='text/css'>
	<!-- FLAT ICON FONTS -->
	<link href='<?=asset_url('fonts')?>/flat-icon/flaticon.css' rel='stylesheet' type='text/css'>
	<!-- BOOSTRAP CSS -->
	<link rel="stylesheet" media="all" href="<?=asset_url('vendor')?>/bootstrap/css/bootstrap.min.css">
	<!-- GRITTER (Notificaciones) -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.gritter/css/jquery.gritter.css" />
	<!-- FONT AWESOME (Iconos) -->
	<!--<link rel="stylesheet" href="<?=asset_url('fonts')?>/font-awesome-4/css/font-awesome.min.css">-->
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- Fontastic Custom icon font -->
    <link rel="stylesheet" href="<?=asset_url('css')?>/fontastic.css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?=asset_url('css')?>/style.default.css" id="theme-stylesheet">
	<!-- BOOTSTRAP DATETIMEPICKERS -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/bootstrap.datetimepicker/css/bootstrap-datetimepicker.min.css" />
	<!-- Select2 -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.select2/select2.css" />
	<!-- Data Tables -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.datatables/bootstrap-adapter/css/datatables.css" />
	<!--<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jszip-2.5.0/dt-1.10.16/b-1.4.2/b-flash-1.4.2/b-html5-1.4.2/datatables.min.css">-->
	<!-- CONTEXT MENU JQUERY -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.context/jquery.contextMenu.min.css" />
	<!-- Bootstrap timepicker -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.timepicker/jquery.timepicker.css" />
	<!-- Datepicker -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.datepicker/datepicker.min.css" />
	<!-- Typehead -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/bootstrap-typeahead/typeahead.css" />
	<!-- Nice Select -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.niceselect/nice-select.css" />
	<!-- SimpleBar -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.simplebar/simplebar.css" />
	<!-- AnimateCss -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/animate.css/animate.css" />
	<!-- ESTILO DE APLICACION -->
	<link rel="stylesheet" href="<?=asset_url('css')?>/style.css">
	<!-- Manifest para acceso directo -->
	<link rel="manifest" href="<?=base_url('manifest.json')?>">
	<meta name="theme-color" content="#a5d051">
	<!-- JQUERY 1.12.1 -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

	<!-- Datepicker -->
	<script src="<?=asset_url('vendor')?>/jquery.datepicker/datepicker.js"></script>
</head>

<body id="<?=$classname?>-page">
	<!-- LOADER -->
	<div class="wrapper" id="loading">
		<div class="spinner">
			<div class="spinner__item1"></div>
			<div class="spinner__item2"></div>
			<div class="spinner__item3"></div>
			<div class="spinner__item4"></div>
		</div>
	</div>