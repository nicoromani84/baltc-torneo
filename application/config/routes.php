<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'login';
$route['logout'] = 'login/logout';
$route['admin/logout'] = 'admin/logout';

// Menu principal
$route['menu'] = 'menu/index';

// Secciones del torneo
$route['resultados'] = 'menu/resultados';
$route['programacion'] = 'menu/programacion';
$route['draws'] = 'draws/index';
$route['draws/getData'] = 'draws/getData';

$route['admin/partidos'] = 'admin/partidos';
$route['admin/addPartido'] = 'admin/addPartido';
$route['admin/editPartido'] = 'admin/editPartido';
$route['admin/deletePartido'] = 'admin/deletePartido';

$route['admin/draws'] = 'admin/draws';
$route['admin/getDrawData'] = 'admin/getDrawData';
$route['admin/jugadores'] = 'admin/jugadores';
$route['admin/toggleInscripciones'] = 'admin/toggleInscripciones';
$route['admin/addJugador'] = 'admin/addJugador';
$route['admin/editJugador'] = 'admin/editJugador';
$route['admin/deleteJugador'] = 'admin/deleteJugador';
$route['admin/sorteo'] = 'admin/sorteo';
$route['admin/getInscriptosByCategory'] = 'admin/getInscriptosByCategory';
$route['admin/confirmarSorteo'] = 'admin/confirmarSorteo';

$route['mipartido'] = 'mipartido/index';
$route['mipartido/cargarResultado'] = 'mipartido/cargarResultado';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
