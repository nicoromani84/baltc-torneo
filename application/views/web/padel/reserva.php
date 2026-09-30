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
	<div id="padel-reservar" class="container">
		<div class="container">
			<h1 class="d-none d-sm-block">Hola <strong><?=$user->name?></strong><br>Inscribite en Pádel <?=$category?></h1>
			<h1 class="d-sm-none">Hola <strong><?=$user->name?></strong><br>Pádel <?=$category?></h1>
			<p>Comienzo: 6 de Octubre 2026</p>
			<div class="form">
				<form class="form-inline">
					<div class="form-group xs-fullwidth" id="partner-search-group">
						<div class="input-group">
							<div class="input-group-prepend"><span class="input-group-text"><i class="icon fas fa-users"></i></span></div>
							<input type="search" name="partner" autocomplete="off" placeholder="Elegí tu pareja" class="typeahead form-control" id="partner-input">
							<div class="input-group-append"><span class="input-group-text"><i class="fas fa-search"></i></span></div>
						</div>
						<small id="partner-info" style="display:none;color:#666;margin-top:5px;">Buscando parejas disponibles...</small>
					</div>
					<div class="mobile-suggestions simplebar"></div>
					<div class="form-group xs-fullwidth acepto-reglamento-group">
						<div class="form-check">
							<input type="checkbox" class="form-check-input" id="acepto_reglamento">
							<label class="form-check-label" for="acepto_reglamento">Acepto el reglamento de pádel</label>
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

<style>
#padel-reservar {
	min-height: calc(100vh - 110px);
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	padding: 30px 0;
}
.reserve-page {
	background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
	color: #fff;
}
</style>

<script type="text/javascript">
var padelurl = '<?=base_url('padel')?>';
var token = '<?=$token?>';
var baseurl = '<?=base_url()?>';
var userGender = '<?=$user->gender?>';

$(function() {
	padel.run();
});
</script>

<script type="text/javascript">
var padel = {
	run: function() {
		this.reserva.triggers();
	},
	reserva: {
		vars: {
			names: [],
			data: []
		},
		triggers: function() {
			var that = this;

			// Obtener parejas del mismo género
			that.getPartners(function(res) {
				if(res.action) {
					that.vars.data = res.data.map(function(a) {
						return {id: a.id, name: a.name.toLowerCase()};
					});

					that.vars.names = res.data.map(function(a) {
						return a.name.toLowerCase();
					});

					that.initTypeahead();
				}
			});

			jQuery.validator.addMethod("validName", function(value, element) {
				return that.vars.names.indexOf(value) >= 0;
			});

			$('#padel-reservar form').validate({
				ignore: [],
				errorElement: "div",
				errorClass: 'is-invalid',
				validClass: 'is-valid',
				onkeyup: false,
				onclick: false,
				onfocusout: false,
				rules: {
					partner: {
						required: true,
						validName: true
					}
				},
				messages: {
					partner: {
						required: 'Debes ingresar tu pareja',
						validName: 'La pareja ingresada no existe'
					}
				},
				errorPlacement: function(error, element) {
					error.addClass("invalid-feedback");
					error.insertAfter(element);
				},
				submitHandler: function(form) {
					var formdata = that.serializedToObj($('.form form').serializeArray());
					formdata.partner = that.getPartnerId(formdata.partner);

					if(typeof formdata.partner == 'undefined' || formdata.partner == '') {
						showNotification('error', 'Debes ingresar tu compañero.');
						return false;
					}

					if(!$('#acepto_reglamento').is(':checked')) {
						showNotification('error', 'Debés aceptar el reglamento.');
						return false;
					}

					$(form).find('button[type="submit"]').prop('disabled', true).addClass('loading');

					setTimeout(function() {
						$.ajax({
							url: padelurl + '/add',
							type: "POST",
							data: formdata,
							headers: {'X-Auth-Token': token},
							success: function(res) {
								if(res.action) {
									window.location.href = padelurl + '/inscripto';
								} else {
									if(typeof res.msg !== 'undefined') {
										showNotification('error', res.msg);
									} else {
										showNotification('error', 'Error al grabar la inscripción');
									}
									$(form).find('button[type="submit"]').prop('disabled', false).removeClass('loading');
								}
							}
						});
					}, 1000);
				}
			});

			$('.typeahead').bind('typeahead:render', function(ev, suggestion) {
				new SimpleBar($('.simplebar')[0]);
			});
		},

		getPartnerId: function(name) {
			var that = this;
			return padel.reserva.vars.data[padel.reserva.vars.names.indexOf(name)].id;
		},

		serializedToObj: function(array) {
			var data = {};
			$(array).each(function(index, obj) {
				data[obj.name] = obj.value;
			});
			return data;
		},

		substringMatcher: function(strs) {
			return function findMatches(q, cb) {
				var matches, substringRegex;
				matches = [];
				substrRegex = new RegExp(q, 'i');
				$.each(strs, function(i, str) {
					if (substrRegex.test(str)) {
						matches.push(str);
					}
				});
				cb(matches);
			};
		},

		getPartners: function(callback) {
			var that = this;
			$.ajax({
				url: baseurl + '/padel/partners/getMismoGenero',
				type: "GET",
				data: {},
				headers: {'X-Auth-Token': token},
				success: function(res) {
					if (typeof callback == 'function') {
						callback(res);
					}
				}
			});
		},

		initTypeahead: function() {
			var that = this;
			$('.typeahead').typeahead('destroy');

			$('.typeahead').typeahead({
				hint: true,
				highlight: true,
				minLength: 1,
				menu: isMobile().any() ? $('.mobile-suggestions') : '',
				classNames: {menu: 'tt-menu simplebar'}
			}, {
				name: 'states',
				source: that.substringMatcher(that.vars.names),
				limit: 10000
			});
		}
	}
};
</script>
