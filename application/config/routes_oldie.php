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
$route['draws'] = 'menu/draws';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
