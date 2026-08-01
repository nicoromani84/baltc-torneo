<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!doctype html>
<html class="no-js" lang="es">
<head>
	<!-- META TAGS -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="description" content="">
	<meta name="author" content="">
	<!-- ICON -->
	<link rel="shortcut icon" href="<?=asset_url('img')?>/favicon.png">
	<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon"> 
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
	<!-- GIJGO DATEPICKER -->
	<link rel="stylesheet" type="text/css" href="<?=asset_url('vendor')?>/jquery.gijgo/css/gijgo.min.css" />
	<!-- ESTILO DE APLICACION -->
	<link rel="stylesheet" href="<?=asset_url('css')?>/style.css">
	<!-- ADMIN MOBILE RESPONSIVE -->
	<link rel="stylesheet" href="<?=asset_url('css')?>/admin-mobile.css">
	<!-- JQUERY 1.12.1 -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	
	<!-- Datepicker -->
	<script src="<?=asset_url('vendor')?>/jquery.datepicker/datepicker.js"></script>
</head>

<body id="<?=$section?>-page">
	<!-- LOADER -->
	<div class="wrapper" id="loading">
		<div class="spinner">
			<div class="spinner__item1"></div>
			<div class="spinner__item2"></div>
			<div class="spinner__item3"></div>
			<div class="spinner__item4"></div>
		</div>
	</div>
<nav class="admin-navbar">
	<div class="admin-navbar-brand">
		<img src="<?=asset_url('img')?>/logo.png" alt="BALTC">
		<span>Admin Torneo</span>
	</div>
	<ul class="admin-navbar-links">
		<li class="<?=$section=='admin-dashboard'?'active':''?>">
			<a href="<?=base_url('admin')?>">
				<i class="fas fa-home"></i> Dashboard
			</a>
		</li>
		<li class="<?=$section=='admin-partidos'?'active':''?>">
			<a href="<?=base_url('admin/partidos')?>">
				<i class="fas fa-tennis-ball"></i> Partidos
			</a>
		</li>
		<?php if(empty($readonly)): ?>
		<li class="<?=$section=='admin-sorteo'?'active':''?>">
			<a href="<?=base_url('admin/sorteo')?>">
				<i class="fas fa-random"></i> Sorteo
			</a>
		</li>
		<?php endif; ?>
		<li class="<?=$section=='admin-draws'?'active':''?>">
			<a href="<?=base_url('admin/draws')?>">
				<i class="fas fa-sitemap"></i> Draws
			</a>
		</li>
		<li class="<?=$section=='admin-ranking'?'active':''?>">
			<a href="<?=base_url('admin/ranking')?>">
				<i class="fas fa-trophy"></i> Ranking
			</a>
		</li>
		<?php if(empty($readonly)): ?>
		<li class="<?=$section=='admin-mails'?'active':''?>">
			<a href="<?=base_url('admin/mails')?>">
				<i class="fas fa-envelope"></i> Mails
			</a>
		</li>
		<li class="<?=$section=='admin-mails-2ndchance'?'active':''?>">
			<a href="<?=base_url('admin/mails2ndchance')?>">
				<i class="fas fa-sync-alt"></i> 2nd Chance
			</a>
		</li>
		<?php endif; ?>
		<li class="<?=$section=='admin-loginlogs'?'active':''?>">
			<a href="<?=base_url('admin/loginLogs')?>">
				<i class="fas fa-history"></i> Logs
			</a>
		</li>
		<li class="<?=$section=='admin-partners'?'active':''?>">
			<a href="<?=base_url('admin/partners')?>">
				<i class="fas fa-users"></i> Partners
			</a>
		</li>
		<li class="admin-navbar-logout">
			<a href="<?=base_url('admin/logout')?>">
				<i class="fas fa-sign-out-alt"></i> Salir
			</a>
		</li>
	</ul>
	<button class="admin-navbar-toggle" id="admin-nav-toggle">
		<i class="fas fa-bars"></i>
	</button>
</nav>

<style>
.admin-navbar {
	display: flex;
	align-items: center;
	background: #1a1a2e;
	padding: 0 20px;
	height: 60px;
	position: sticky;
	top: 0;
	z-index: 1000;
	box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.admin-navbar-brand {
	display: flex;
	align-items: center;
	gap: 10px;
	margin-right: 30px;
	flex-shrink: 0;
}
.admin-navbar-brand img {
	height: 36px;
	width: auto;
	filter: brightness(0) invert(1);
}
.admin-navbar-brand span {
	color: #fff;
	font-weight: 700;
	font-size: 14px;
	text-transform: uppercase;
	letter-spacing: 1px;
	white-space: nowrap;
}
.admin-navbar-links {
	display: flex;
	list-style: none;
	margin: 0;
	padding: 0;
	flex: 1;
	gap: 4px;
}
.admin-navbar-links li a {
	display: flex;
	align-items: center;
	gap: 6px;
	color: rgba(255,255,255,0.7);
	text-decoration: none;
	padding: 8px 14px;
	border-radius: 6px;
	font-size: 13px;
	font-weight: 500;
	transition: all 0.2s;
	white-space: nowrap;
}
.admin-navbar-links li a:hover {
	background: rgba(255,255,255,0.1);
	color: #fff;
	text-decoration: none;
}
.admin-navbar-links li.active a {
	background: #a5d051;
	color: #1a1a2e;
	font-weight: 700;
}
.admin-navbar-logout {
	margin-left: auto;
}
.admin-navbar-logout a {
	color: rgba(255,100,100,0.8) !important;
}
.admin-navbar-logout a:hover {
	background: rgba(255,100,100,0.15) !important;
	color: #ff6464 !important;
}
.admin-navbar-toggle {
	display: none;
	background: none;
	border: none;
	color: #fff;
	font-size: 20px;
	cursor: pointer;
	margin-left: auto;
}
@media (max-width: 768px) {
	.admin-navbar { flex-wrap: wrap; height: auto; padding: 10px 15px; }
	.admin-navbar-toggle { display: block; }
	.admin-navbar-links {
		display: none;
		flex-direction: column;
		width: 100%;
		padding: 10px 0;
		gap: 2px;
	}
	.admin-navbar-links.open { display: flex; }
	.admin-navbar-links li { width: 100%; }
	.admin-navbar-links li a { padding: 10px 14px; }
	.admin-navbar-logout { margin-left: 0; }
}
</style>

<script>
$(function(){
	$('#admin-nav-toggle').on('click', function(){
		$('.admin-navbar-links').toggleClass('open');
	});
});
</script>
