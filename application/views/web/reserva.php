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
			<h1 class="d-none d-sm-block">Hola <strong><?=$user->name?></strong><br>Inscribite en el Torneo Interno de Singles</h1>
			<h1 class="d-sm-none">Hola <strong><?=$user->name?></strong><br>Inscribite en el Torneo Interno de Singles</h1>
			<p>A partir del 19 de Mayo 2026</p>
			<div class="form">
				<form class="form-inline">
					<div class="form-group xs-fullwidth">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-star"></i></span></div>
							<select name="category" id="category" class="mr3 form-control niceselect">
								<option disabled selected value="">Elegir categoría</option>
								<?php foreach($categories as $category){ ?>
								<option value="<?=$category->id?>"><?=$category->name?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group xs-fullwidth" style="display:none">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-user-friends"></i></span></div>
							<input type="search" name="partner" autocomplete="off" placeholder="Elegí tu compañero" class="typeahead form-control">
							<i class="fas fa-search"></i>
						</div>
					</div>
					<div class="mobile-suggestions simplebar"></div>
					<div class="form-group xs-fullwidth acepto-reglamento-group">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="acepto_reglamento">
							<label class="form-check-label" for="acepto_reglamento">Acepto el <a href="#" data-toggle="modal" data-target="#modalReglamento"> reglamento del torneo</a></label>
						</div>
					</div>
					<div class="form-group xs-fullwidth" style="margin-top:5px">
						<button type="submit" class="btn btn-primary" style="width:100%"><span>Anotarse</span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<!-- MODAL REGLAMENTO -->
<div class="modal fade" id="modalReglamento" tabindex="-1" role="dialog" aria-labelledby="modalReglamentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReglamentoLabel">Reglamento del Torneo Interno de Singles</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="reglamento">
                    <h5>1. Organización</h5>
                    <p>El torneo es organizado por el Buenos Aires Lawn Tennis Club (BALTC). La Comisión Deportiva tendrá a su cargo la supervisión general y la resolución de cualquier situación no prevista en el presente reglamento.</p>
                    <p>El torneo se desarrollará en tres etapas:</p>
                    <ul>
                        <li>Singles: mayo, junio y julio</li>
                        <li>Dobles (Damas y Caballeros): julio, agosto y septiembre</li>
                        <li>Dobles Mixto: septiembre, octubre y noviembre</li>
                    </ul>

                    <h5>2. Inscripción y arancel</h5>
                    <p>Los participantes podrán inscribirse en una o más etapas. El arancel de $20.000 será por etapa y se debitará junto con la liquidación de la cuota mensual.</p>

                    <h5>3. Categorías</h5>
                    <p>El torneo contará en principio con tres categorías (sujeto a cantidad de participantes), definidas según el nivel de juego:</p>
                    <ul>
                        <li>1ª: nivel más alto</li>
                        <li>2ª: nivel intermedio</li>
                        <li>3ª: nivel inicial</li>
                    </ul>
                    <p>La organización se reserva el derecho de reubicar a cualquier jugador en la categoría que considere más adecuada a su nivel real.</p>

                    <h5>4. Participantes</h5>
                    <p>Podrán participar todos los socios activos del club que se encuentren al día con sus obligaciones sociales. Los menores podrán participar siempre que tengan más de 12 años y cuenten con el nivel requerido de juego. Cada jugador podrá inscribirse en una sola categoría.</p>

                    <h5>5. Formato de juego</h5>
                    <p>Los partidos se disputarán al mejor de tres (3) sets:</p>
                    <ul>
                        <li>Los dos primeros sets con tie-break</li>
                        <li>En caso de tercer set, se jugará un match tie-break a diez puntos, con diferencia mínima de dos</li>
                    </ul>
                    <p>El torneo garantizará un mínimo de dos (2) partidos a cada jugador inscripto.</p>

                    <h5>6. Programación de partidos</h5>
                    <p>Una vez cerrada la inscripción, los participantes tendrán acceso a los cuadros actualizados en <strong>baltc.net/torneo</strong> en la sección DRAWS.</p>
                    <p>Los días y horarios de juego serán coordinados directamente entre los jugadores hasta la instancia de cuartos inclusive. Los partidos deberán jugarse dentro de cada semana, de lunes a domingo, respetando los plazos límite establecidos por la organización.</p>
                    <p>En caso de necesitar datos de contacto de otro jugador, los participantes podrán solicitarlos en la Secretaría.</p>
                    <p>Los partidos de semi-finales y finales serán programados por la organización.</p>

                    <h5>7. Uso de canchas</h5>
                    <p>Los jugadores deberán respetar el reglamento interno del club en todos los días y horarios, excepto en los casos que se detallan a continuación.</p>
                    <p>Se establecerá prioridad para la disputa de partidos del torneo en la <strong>cancha 14</strong> en los siguientes días y horarios:</p>
                    <ul>
                        <li>Días de semana: a partir de las 17:00</li>
                        <li>Sábados: a partir de las 12:00</li>
                        <li>Domingos y feriados: durante todo el día</li>
                    </ul>
                    <p>Los jugadores deberán anotarse en la grilla de la cartelera del club en el momento previo al uso de la cancha (no con antelación). Esta prioridad será válida exclusivamente para partidos del torneo.</p>

                    <h5>8. Pelotas</h5>
                    <p>Las pelotas deberán ser provistas por los jugadores hasta la instancia de cuartos. A partir de semis, será la organización quien brinde las pelotas.</p>

                    <h5>9. Código de conducta</h5>
                    <p>Se espera de todos los participantes un comportamiento deportivo y respetuoso. Cualquier conducta antideportiva, así como agresiones verbales o físicas, podrá ser sancionada con la descalificación inmediata del torneo, sin perjuicio de las medidas disciplinarias que el club considere pertinentes.</p>

                    <h5>10. Arbitraje</h5>
                    <p>Los partidos se jugarán sin árbitro. Los jugadores deberán resolver las situaciones dudosas de manera deportiva. En caso de desacuerdos irresolubles, podrá solicitarse la intervención de un representante de la organización.</p>

                    <h5>11. Resultados</h5>
                    <p>Los resultados deberán ser cargados por el participante <strong>GANADOR</strong> en la sección MI PARTIDO. Ante cualquier inconveniente, contactar a la organización vía WhatsApp al <strong>+54 9 11 36 21 00 03</strong>.</p>

                    <h5>12. Walkover (no presentación)</h5>
                    <p>En caso de no presentación de alguno de los jugadores sin aviso previo, la organización podrá dar el partido por perdido (walkover).</p>

                    <h5>13. Premios</h5>
                    <p>Los ganadores y finalistas de cada categoría recibirán premios y distinciones, que serán anunciados oportunamente.</p>

                    <h5>14. Cierre de inscripción y bajas</h5>
                    <p>Las inscripciones cerrarán el <strong>domingo 17 de mayo a las 21:00</strong>, a fin de poder realizar el sorteo y confeccionar los cuadros con la debida antelación. Cualquier baja deberá ser comunicada antes de ese plazo.</p>
                    <p>Una vez disputado el primer partido, no se aceptarán bajas del torneo y se considerará walkover.</p>

                    <h5>15. Aceptación del reglamento</h5>
                    <p>La inscripción al torneo implica la aceptación plena e incondicional del presente reglamento. Cualquier situación no contemplada será resuelta por la Comisión Deportiva, cuya decisión será inapelable.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="$('#acepto_reglamento').prop('checked', true)">Acepto el reglamento</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
#modalReglamento .modal-dialog-scrollable .modal-body {
    overflow-y: auto !important;
    max-height: 60vh !important;
}
#modalReglamento .modal-content {
    max-height: 90vh !important;
}
</style>

<script type="text/javascript">
var reservaurl = '<?=base_url('reserva')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';

$(function() {
	reserva.run();
});
</script>