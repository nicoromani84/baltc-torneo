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
$route['draws/getDrawsDisponibles'] = 'draws/getDrawsDisponibles';

$route['admin/partidos'] = 'admin/partidos';
$route['admin/addPartido'] = 'admin/addPartido';
$route['admin/editPartido'] = 'admin/editPartido';
$route['admin/deletePartido'] = 'admin/deletePartido';

$route['admin/draws'] = 'admin/draws';
$route['admin/getDrawData'] = 'admin/getDrawData';
$route['admin/singles'] = 'admin/singles';
$route['admin/loginLogs'] = 'admin/loginLogs';
$route['admin/partners'] = 'admin/partners';
$route['admin/getPartnersForPairing'] = 'admin/getPartnersForPairing';
$route['admin/addPairing'] = 'admin/addPairing';
$route['admin/addPartnerQuick'] = 'admin/addPartnerQuick';
$route['admin/editPartner'] = 'admin/editPartner';
$route['admin/editParejaCategory'] = 'admin/editParejaCategory';
$route['admin/deletePartner'] = 'admin/deletePartner';
$route['admin/jugadores'] = 'admin/jugadores';
$route['admin/toggleInscripciones'] = 'admin/toggleInscripciones';
$route['admin/addJugador'] = 'admin/addJugador';
$route['admin/editJugador'] = 'admin/editJugador';
$route['admin/deleteJugador'] = 'admin/deleteJugador';
$route['admin/sorteo'] = 'admin/sorteo';
$route['admin/getInscriptosByCategory'] = 'admin/getInscriptosByCategory';
$route['admin/confirmarSorteo'] = 'admin/confirmarSorteo';
$route['admin/crearGruposManual'] = 'admin/crearGruposManual';
$route['admin/guardarGrupos'] = 'admin/guardarGrupos';
$route['admin/asignarDeadlineManual'] = 'admin/asignarDeadlineManual';
$route['admin/guardarDeadlineManual'] = 'admin/guardarDeadlineManual';
$route['admin/enviarRecordatorios'] = 'admin/enviarRecordatorios';
$route['admin/getDeadlinesPendientes'] = 'admin/getDeadlinesPendientes';
$route['admin/getPartidosPorDeadline'] = 'admin/getPartidosPorDeadline';
$route['admin/enviarRecordatoriosPorDeadline'] = 'admin/enviarRecordatoriosPorDeadline';
$route['admin/listarDestinatariosDeadline'] = 'admin/listarDestinatariosDeadline';
$route['admin/enviarEmailPrueba'] = 'admin/enviarEmailPrueba';
$route['admin/debugDeadlines'] = 'admin/debugDeadlines';
$route['admin/verPartidosProgramados'] = 'admin/verPartidosProgramados';
$route['admin/previewRecordatorios'] = 'admin/previewRecordatorios';

$route['admin/mails'] = 'admin/mails';
$route['admin/mails2ndchance'] = 'admin/mails2ndchance';
$route['admin/getDestinatariosNotificacion'] = 'admin/getDestinatariosNotificacion';
$route['admin/enviarNotificacion'] = 'admin/enviarNotificacion';
$route['admin/verPendientes2ndChance'] = 'admin/verPendientes2ndChance';
$route['admin/guardarPendientes2ndChance'] = 'admin/guardarPendientes2ndChance';
$route['admin/enviar2ndChance'] = 'admin/enviar2ndChance';
$route['admin/getEmailTemplates'] = 'admin/getEmailTemplates';
$route['admin/previewEmailTemplate'] = 'admin/previewEmailTemplate';
$route['admin/preview2ndChance'] = 'admin/preview2ndChance';
$route['admin/enviarInvitacion2ndChance'] = 'admin/enviarInvitacion2ndChance';
$route['admin/get2ndChanceInscriptos'] = 'admin/get2ndChanceInscriptos';
$route['admin/preview2ndChanceEmail'] = 'admin/preview2ndChanceEmail';
$route['admin/enviarInvitacion2ndChanceIndividual'] = 'admin/enviarInvitacion2ndChanceIndividual';
$route['admin/ranking'] = 'admin/ranking';
$route['admin/getRankingData'] = 'admin/getRankingData';
$route['admin/descargarRankingPDF'] = 'admin/descargarRankingPDF';
$route['admin/syncUsersToPartners'] = 'admin/syncUsersToPartners';

$route['invitacion/aceptar2ndchance'] = 'invitacion/aceptar2ndchance';

$route['mipartido'] = 'mipartido/index';
$route['mipartido/cargarResultado'] = 'mipartido/cargarResultado';
$route['mipartido/guardarFechaAcordada'] = 'mipartido/guardarFechaAcordada';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
