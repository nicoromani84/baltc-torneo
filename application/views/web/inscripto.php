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
	<div id="inscripto-page" class="container">
		<div class="container">

			<div class="inscripto-card">
				<div class="inscripto-check">
					<i class="fas fa-check-circle"></i>
				</div>
				<h2 class="inscripto-titulo">¡Ya estás inscripto!</h2>
				<div class="inscripto-nombre"><?=strtolower($user->name)?></div>

				<div class="inscripto-detalle">
					<div class="inscripto-item">
						<i class="fas fa-star"></i>
						<span>Categoría <strong><?=$categoria?></strong></span>
					</div>
					<?php if(!empty($partner)): ?>
					<div class="inscripto-item">
						<i class="fas fa-user-friends"></i>
						<span>Compañero <strong style="text-transform:capitalize;"><?=strtolower($partner)?></strong></span>
					</div>
					<?php endif; ?>
					<div class="inscripto-item">
						<i class="fas fa-calendar-alt"></i>
						<span>Inicio: <strong>15 de Agosto 2026</strong></span>
					</div>
					<div class="inscripto-item inscripto-aviso">
						<i class="fas fa-info-circle"></i>
						<span>Tu categoría puede quedar sujeta a revisión por parte de la organización.</span>
					</div>
				</div>


			</div>

			<button class="btn btn-outline-light btn-sm inscripto-reglamento" data-toggle="modal" data-target="#modalReglamentoInscripto">
				<i class="fas fa-file-alt"></i> Ver reglamento
			</button>

		</div>
	</div>
</div>

<!-- MODAL REGLAMENTO -->
<div class="modal fade" id="modalReglamentoInscripto" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reglamento – Torneo Interno BALTC – Etapa: Dobles</h5>
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
                    <p>En la etapa de DOBLES no habrá second chance.</p>

                    <h5>3. Categorías</h5>
                    <p>El torneo contará con hasta cuatro categorías (sujeto a cantidad de participantes), definidas según el nivel de juego, donde:</p>
                    <ul>
                        <li>1ª: nivel más alto</li>
                        <li>2ª: nivel bueno</li>
                        <li>3ª: nivel intermedio</li>
                        <li>4°: nivel inicial</li>
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

                    <h5>6. Programación de partidos</h5>
                    <p>Una vez cerrada la inscripción, los participantes tendrán acceso a los cuadros actualizados del torneo accediendo al mismo link: <strong>baltc.net/torneo</strong> en la sección DRAWS.</p>
                    <p>Los días y horarios de juego serán coordinados directamente entre los jugadores hasta la instancia de cuartos inclusive.</p>
                    <p>Los partidos deberán jugarse dentro de cada semana, de lunes a domingo. Deberán respetarse los plazos límite establecidos por la organización para la disputa de cada instancia.</p>
                    <p>En caso de necesitar datos de contacto de otro jugador para coordinar el partido, los participantes podrán solicitarlos en la Secretaría.</p>
                    <p>Los partidos de semi-finales y finales serán programados por la organización.</p>

                    <h5>7. Uso de canchas</h5>
                    <p>Los participantes podrán jugar cualquier día y horario que quieran, respetando el reglamento interno del club.</p>
                    <p>Se recomienda a los que vayan a jugar después de las 18:00 reservar cancha previamente (con luz), sobre todo los días de semana (martes a viernes).</p>

                    <h5>8. Pelotas</h5>
                    <p>La organización proveerá las pelotas, siempre y cuando se juegue durante el horario de la Secretaría.</p>
                    <p>Los jugadores deberán pedir los tubos en la Secretaría y asegurarse de devolverlos al finalizar el partido. En caso que la Secretaría esté cerrada, deberán devolverlos al día siguiente, sin excepción.</p>

                    <h5>9. Resultados</h5>
                    <p>Los resultados deberán ser cargados por el participante GANADOR en la sección MI PARTIDO una vez finalizado el encuentro. Se recomienda hacerlo inmediatamente para evitar errores y facilitar la organización.</p>
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
                    <p>Ante cualquier inconveniente, por favor comunicarse con la organización vía whatsapp a los siguientes números:</p>
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
#inscripto-page {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	padding: 30px 0;
}
.inscripto-card {
	background: rgba(0,0,0,0.55);
	border: 1px solid rgba(165,208,81,0.35);
	border-radius: 20px;
	padding: 40px 30px;
	text-align: center;
	max-width: 420px;
	margin: 0 auto;
}
.inscripto-check {
	font-size: 70px;
	color: #a5d051;
	margin-bottom: 16px;
	animation: popIn 0.5s ease;
}
@keyframes popIn {
	0% { transform: scale(0); opacity: 0; }
	70% { transform: scale(1.15); }
	100% { transform: scale(1); opacity: 1; }
}
.inscripto-titulo {
	color: #fff;
	font-size: 26px;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 6px;
	text-shadow: 2px 2px 8px rgba(0,0,0,0.8);
}
.inscripto-nombre {
	color: #a5d051;
	font-size: 18px;
	font-weight: 700;
	text-transform: capitalize;
	margin-bottom: 24px;
}
.inscripto-detalle {
	background: rgba(255,255,255,0.05);
	border-radius: 12px;
	padding: 16px;
	margin-bottom: 24px;
	text-align: left;
}
.inscripto-item {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	padding: 8px 0;
	border-bottom: 1px solid rgba(255,255,255,0.07);
	color: rgba(255,255,255,0.85);
	font-size: 15px;
}
.inscripto-item:last-child { border-bottom: none; }
.inscripto-item i {
	color: #a5d051;
	font-size: 16px;
	margin-top: 2px;
	flex-shrink: 0;
}
.inscripto-item strong { color: #fff; }
.inscripto-aviso {
	color: rgba(255,220,80,0.85) !important;
	font-size: 13px !important;
}
.inscripto-aviso i { color: rgba(255,220,80,0.8) !important; }
.inscripto-reglamento {
	width: 100%;
	margin-top: 12px;
	border-color: rgba(255,255,255,0.3) !important;
	color: rgba(255,255,255,0.7) !important;
	font-size: 13px !important;
}
.inscripto-reglamento:hover {
	background: rgba(255,255,255,0.1) !important;
	color: #fff !important;
}
.inscripto-btn {
	width: 100%;
	padding: 14px;
	font-size: 16px;
	font-weight: 700;
	border-radius: 10px;
	letter-spacing: 0.5px;
}
@media (max-width: 575.98px) {
	.inscripto-card { padding: 30px 20px; }
	.inscripto-titulo { font-size: 22px; }
	.inscripto-check { font-size: 60px; }
}
</style>

<style>
#modalReglamentoInscripto {
    display: none;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 1050 !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    outline: 0 !important;
}
#modalReglamentoInscripto .modal-body {
    max-height: 60vh;
    overflow-y: auto;
}
#modalReglamentoInscripto .modal-content {
    max-height: 90vh;
    overflow: hidden;
}
</style>

<script type="text/javascript">
var baseurl = '<?=base_url()?>';
var token = '<?=$token?>';

$(function(){
    // Asegurar que el modal cierra correctamente
    $('#modalReglamentoInscripto [data-dismiss="modal"]').on('click', function(){
        $('#modalReglamentoInscripto').modal('hide');
    });
});
</script>
