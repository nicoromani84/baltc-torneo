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
			<h1 class="d-none d-sm-block">¡Anotate en el<br>Torneo de Inauguración de Pádel!</h1>
			<h1 class="d-sm-none">¡Anotate en el Torneo<br>de Inauguración de Pádel!</h1>
			<p class="padel-fecha"><i class="fas fa-calendar-alt"></i> 10 de Octubre • 17hs</p>
			<div class="form">
				<form class="form-inline">
					<div class="form-group xs-fullwidth">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-star"></i></span></div>
							<select name="category" id="category" class="mr3 form-control niceselect">
								<option disabled selected value="">Elegir categoría</option>
								<?php foreach($categories as $category){ ?>
								<option value="<?=$category->id?>" data-gender="<?=$category->gender?>"><?=($category->gender === 'X' ? '♂♀ ' : '')?><?=$category->name?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group xs-fullwidth" id="partner-search-group">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-users"></i></span></div>
							<input type="search" name="partner" autocomplete="off" placeholder="Elegí tu pareja" class="typeahead form-control" id="partner-input">
							<div class="input-group-append"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
						</div>
						<small id="mixed-partner-info" style="display:none;color:#666;margin-top:5px;">Buscando parejas disponibles...</small>
					</div>
					<div class="mobile-suggestions simplebar"></div>
					<div class="form-group xs-fullwidth acepto-reglamento-group">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="acepto_reglamento">
							<label class="form-check-label" for="acepto_reglamento">Acepto el <a href="#" data-toggle="modal" data-target="#modalReglamento"> reglamento de pádel</a></label>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReglamentoLabel">Reglamento Pádel – BALTC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="reglamento">
                    <h5>1. Organización</h5>
                    <p>El torneo de pádel es organizado por el Buenos Aires Lawn Tennis Club (BALTC). La Subcomisión de Tenis tendrá a su cargo la supervisión general y la resolución de cualquier situación no prevista en el presente reglamento.</p>

                    <h5>2. Inscripción</h5>
                    <p>Los participantes podrán inscribirse en pádel indicando a su pareja del mismo género.</p>
                    <p>El arancel será determinado por la organización y se debitará junto con la liquidación de la cuota mensual.</p>

                    <h5>3. Categorías</h5>
                    <p>El torneo contará con categorías de Caballeros y Damas, sujeto a la cantidad de participantes.</p>

                    <h5>4. Participantes</h5>
                    <p>Podrán participar todos los socios activos del club que se encuentren al día con sus obligaciones sociales.</p>
                    <p>Los menores podrán participar siempre que tengan más de 12 años y cuenten con el nivel requerido de juego.</p>

                    <h5>5. Formato de juego</h5>
                    <p>Los partidos se disputarán al mejor de tres (3) sets con tie-break en cada set.</p>

                    <h5>6. Programación de partidos</h5>
                    <p>Los días y horarios de juego serán coordinados directamente entre los jugadores.</p>
                    <p>Los partidos deberán jugarse dentro de los plazos establecidos por la organización.</p>

                    <h5>7. Uso de canchas</h5>
                    <p>Los participantes podrán jugar cualquier día y horario que quieran, respetando el reglamento interno del club.</p>
                    <p>Se recomienda reservar cancha previamente, sobre todo en horarios de mayor concurrencia.</p>

                    <h5>8. Pelotas</h5>
                    <p>Los jugadores deberán proveer sus propias pelotas de pádel.</p>

                    <h5>9. Resultados</h5>
                    <p>Los resultados deberán ser cargados por el ganador en el sistema una vez finalizado el encuentro.</p>

                    <h5>10. Código de conducta</h5>
                    <p>Se espera de todos los participantes un comportamiento deportivo y respetuoso.</p>
                    <p>Cualquier conducta antideportiva podrá ser sancionada con la descalificación del torneo.</p>

                    <h5>11. Contacto</h5>
                    <p>Ante cualquier inconveniente, comunicarse con la Secretaría del club.</p>
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
#modalReglamento {
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
#modalReglamento .modal-body {
    max-height: 60vh;
    overflow-y: auto;
}
#modalReglamento .modal-content {
    max-height: 90vh;
    overflow: hidden;
}
/* Colores de Pádel */
h1 {
    color: #ff6b35 !important;
    font-weight: 900 !important;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
}
.padel-fecha {
    color: #ffd700 !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    margin-bottom: 24px !important;
}
.padel-fecha i {
    color: #ff6b35;
    margin-right: 8px;
}
.form-control, .niceselect {
    border-color: #ff6b35 !important;
    background: rgba(255, 107, 53, 0.05) !important;
    color: #333 !important;
}
.form-control:focus, .niceselect:focus {
    border-color: #ffd700 !important;
    box-shadow: 0 0 8px rgba(255, 107, 53, 0.3) !important;
}
.btn-primary {
    background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%) !important;
    border-color: #ff6b35 !important;
    font-weight: 700 !important;
    font-size: 16px !important;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #ff5722 0%, #ff6b35 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(255, 107, 53, 0.4);
}
.input-group-text {
    background: #ff6b35 !important;
    color: white !important;
    border-color: #ff6b35 !important;
}
.form-check-label a {
    color: #ff6b35 !important;
    text-decoration: underline;
}
.form-check-label a:hover {
    color: #ffd700 !important;
}
.acepto-reglamento-group {
    margin-top: 16px;
}
</style>

<script type="text/javascript">
var reservaurl = '<?=base_url('padel')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';
var userGender = '<?=$user->gender?>';

$(function() {
	reserva.run();
});
</script>
