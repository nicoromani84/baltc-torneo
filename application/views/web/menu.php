<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<header>
	<div class="container">
		<div class="logo">
			<img src="<?=asset_url('img')?>/logo.png" alt="Logo">
		</div>
		<ul class="buttons">
			<li>
				<a href="<?=base_url('/logout')?>">
					<span class="icon-logout"><i class="fas fa-sign-out-alt"></i></span>
				</a>
			</li>
		</ul>
	</div>
</header>

<div class="page reserve-page">
	<div id="reservar" class="container">
		<div class="container">
			<h1 class="d-none d-sm-block">Hola <strong><?=$user->name?></strong><br>Torneo Interno de Singles</h1>
			<h1 class="d-sm-none">Hola <strong><?=$user->name?></strong><br>Torneo Interno de Singles</h1>
			<p>¿Qué querés ver?</p>
			<div class="menu-grid">

				<a href="<?=base_url('resultados')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-trophy"></i>
					</div>
					<div class="menu-card-label">Partidos<br><span style="font-size:10px;opacity:0.7;font-weight:400;text-transform:none;letter-spacing:0">y resultados</span></div>
				</a>


				<a href="<?=base_url('draws')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-sitemap"></i>
					</div>
					<div class="menu-card-label">Draws</div>
				</a>

				<a href="<?=base_url('mipartido')?>" class="menu-card">
					<div class="menu-card-icon">
						<i class="fas fa-edit"></i>
					</div>
					<div class="menu-card-label">Mi Partido</div>
				</a>

				<a href="#" class="menu-card" data-toggle="modal" data-target="#modalReglamento">
					<div class="menu-card-icon">
						<i class="fas fa-file-alt"></i>
					</div>
					<div class="menu-card-label">Reglamento</div>
				</a>

			</div>
		</div>
	</div>
</div>

<style>
.menu-grid {
	display: flex;
	justify-content: center;
	gap: 20px;
	flex-wrap: wrap;
	width: 100%;
	padding: 10px;
	background: rgba(0,0,0,.4);
	border-radius: 5px;
}
.menu-card {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	border: 2px solid rgba(165, 208, 81, 0.5);
	border-radius: 12px;
	width: 150px;
	height: 130px;
	text-decoration: none;
	transition: all 0.3s ease;
	flex: 1;
	max-width: 160px;
}
.menu-card:hover, .menu-card:active {
	background: rgba(165, 208, 81, 0.2);
	border-color: #a5d051;
	text-decoration: none;
}
.menu-card-icon {
	font-size: 40px;
	color: #a5d051;
	margin-bottom: 10px;
}
.menu-card-label {
	font-size: 13px;
	font-weight: 700;
	color: #fff;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	text-align: center;
}

@media (max-width: 575.98px) {
	.menu-grid { gap: 6px; flex-wrap: nowrap; padding: 8px; }
	.menu-card { height: 95px; max-width: none; min-width: 0; flex: 1 1 0; width: 0; }
	.menu-card-icon { font-size: 26px; margin-bottom: 5px; }
	.menu-card-label { font-size: 9px; letter-spacing: 0; padding: 0 2px; }
}
</style>

<script type="text/javascript">
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';
</script>

<!-- MODAL REGLAMENTO -->
<div class="modal fade" id="modalReglamento" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reglamento del Torneo Interno de Singles</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="reglamento">
                    <h5>1. Organización</h5>
                    <p>El torneo es organizado por el Buenos Aires Lawn Tennis Club (BALTC). La Subcomisión de Tenis tendrá a su cargo la supervisión general y la resolución de cualquier situación no prevista en el presente reglamento.</p>
                    <p>El torneo se desarrollará en tres etapas:</p>
                    <ul>
                        <li>Singles: mayo, junio y julio</li>
                        <li>Dobles (Damas y Caballeros): julio, agosto y septiembre</li>
                        <li>Dobles Mixto: septiembre, octubre y noviembre</li>
                    </ul>

                    <h5>2. Inscripción y arancel</h5>
                    <p>Los participantes podrán inscribirse en una o más etapas.</p>
                    <p>El arancel de $20.000 será por etapa y se debitará junto con la liquidación de la cuota mensual.</p>

                    <h5>3. Categorías</h5>
                    <p>El torneo contará en principio con tres categorías (sujeto a cantidad de participantes), definidas según el nivel de juego:</p>
                    <ul>
                        <li>1ª: nivel más alto</li>
                        <li>2ª: nivel intermedio</li>
                        <li>3ª: nivel inicial</li>
                    </ul>
                    <p>La organización se reserva el derecho de reubicar a cualquier jugador en la categoría que considere más adecuada a su nivel real.</p>

                    <h5>4. Participantes</h5>
                    <p>Podrán participar todos los socios activos del club que se encuentren al día con sus obligaciones sociales.</p>
                    <p>Los menores podrán participar siempre que tengan más de 12 años y cuenten con el nivel requerido de juego.</p>
                    <p>Cada jugador podrá inscribirse en una sola categoría.</p>

                    <h5>5. Formato de juego</h5>
                    <p>Los partidos se disputarán al mejor de tres (3) sets:</p>
                    <ul>
                        <li>Los dos primeros sets con tie-break</li>
                        <li>En caso de tercer set, se jugará un match tie-break a diez puntos, con diferencia mínima de dos</li>
                    </ul>
                    <p>El torneo garantizará un mínimo de dos (2) partidos a cada jugador inscripto.</p>

                    <h5>6. Programación de partidos</h5>
                    <p>Una vez cerrada la inscripción, los participantes tendrán acceso a los cuadros actualizados del torneo accediendo al mismo link: <strong>baltc.net/torneo</strong> en la sección DRAWS.</p>
                    <p>Los días y horarios de juego serán coordinados directamente entre los jugadores hasta la instancia de cuartos inclusive.</p>
                    <p>Los partidos deberán jugarse dentro de cada semana, de lunes a domingo. Deberán respetarse los plazos límite establecidos por la organización para la disputa de cada instancia.</p>
                    <p>En caso de necesitar datos de contacto de otro jugador para coordinar el partido, los participantes podrán solicitarlos en la Secretaría.</p>
                    <p>Los partidos de semi-finales y finales serán programados por la organización.</p>

                    <h5>7. Uso de canchas</h5>
                    <p>Los participantes podrán jugar cualquier día y horario que quieran, respetando el reglamento interno del club.</p>
                    <p>Se recomienda a los que vayan a jugar después de las 18:00 reservar cancha previamente (con luz), sobre todo los días de semana (martes a viernes).</p>
                    <p>Se recuerda también que en la <strong>cancha 9</strong> hay prioridad todos los días para jugar singles (salvo en el horario de escuela).</p>
                    <p>Se establecerá una prioridad adicional para la disputa de partidos del torneo en la <strong>cancha 14</strong> durante los fines de semana en los siguientes horarios:</p>
                    <ul>
                        <li>Sábados: de 12:00 a 18:00</li>
                        <li>Domingos y feriados: hasta las 18:00</li>
                    </ul>
                    <p>Esta prioridad será válida exclusivamente para la disputa de partidos correspondientes al torneo.</p>

                    <h5>8. Pelotas</h5>
                    <p>La organización proveerá las pelotas, siempre y cuando se juegue durante el horario de la Secretaría.</p>
                    <p>Los jugadores deberán pedir los tubos en la Secretaría y asegurarse de devolverlos al finalizar el partido. En caso que la Secretaría esté cerrada, deberán devolverlos al día siguiente, sin excepción.</p>

                    <h5>9. Resultados</h5>
                    <p>Los resultados deberán ser cargados por el participante <strong>GANADOR</strong> en la sección MI PARTIDO una vez finalizado el encuentro. Se recomienda hacerlo inmediatamente para evitar errores y facilitar la organización.</p>
                    <p>En caso que no sea posible, por favor hacerlo antes de la fecha límite provista por la organización.</p>

                    <h5>10. Código de conducta</h5>
                    <p>Se espera de todos los participantes un comportamiento deportivo y respetuoso.</p>
                    <p>Cualquier conducta antideportiva, así como agresiones verbales o físicas, podrá ser sancionada con la descalificación inmediata del torneo, sin perjuicio de las medidas disciplinarias que el club considere pertinentes.</p>

                    <h5>11. Arbitraje</h5>
                    <p>Los partidos se jugarán sin árbitro.</p>
                    <p>Los jugadores deberán resolver las situaciones dudosas de manera deportiva. En caso de desacuerdos irresolubles, podrá solicitarse la intervención de un representante de la organización.</p>

                    <h5>12. Walkover (no presentación)</h5>
                    <p>En caso de no presentación de alguno de los jugadores sin aviso previo, la organización podrá dar el partido por perdido (walkover).</p>

                    <h5>13. Premios</h5>
                    <p>Los ganadores y finalistas de cada categoría recibirán premios y distinciones, que serán anunciados oportunamente.</p>

                    <h5>14. Aceptación del reglamento</h5>
                    <p>La inscripción al torneo implica la aceptación plena e incondicional del presente reglamento.</p>
                    <p>Cualquier situación no contemplada será resuelta por la Comisión Deportiva, cuya decisión será inapelable.</p>

                    <h5>15. Datos de Contacto</h5>
                    <p>Ante cualquier inconveniente, por favor comunicarse con la organización vía WhatsApp a los siguientes números:</p>
                    <ul>
                        <li>Juliana Piumatti: +54 9 11 3621 0003</li>
                        <li>Mailen Auroux: +54 9 11 6820 8130</li>
                        <li>Bianca Cacciola: +54 9 11 5578 5858</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
#modalReglamento .modal-body {
    max-height: 60vh !important;
    overflow-y: auto !important;
}
#modalReglamento .modal-content {
    max-height: 90vh !important;
}
</style>
