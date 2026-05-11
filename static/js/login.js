var login = {
	//COMPORTAMIENTOS GENERALES
	run : function() {
		var that = this;

		$(document).on('focus', '#login-dni', function(){
			$('html, body').animate({ scrollTop: $(this).offset().top }, 0);
		})

		$('#login').validate({
            errorElement: "div",
            errorClass: 'is-invalid',
            validClass: 'is-valid',
            onkeyup: false,
            onclick: false,
            onfocusout: false,
            rules: {
				dni: {
					required: true,
					remote: loginurl + '/validate'
				}
			},
			messages: {
				dni: {
					required: 'Debes completar tu DNI o Pasaport',
					remote: 'Lo sentimos, no se encuentra tu DNI o Pasaporte registrado, comunicate al 4772-0983.'
				}
			},
            errorPlacement: function (error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
            },
            submitHandler: function(form) {
            	that.do( function(res) {
				if (res.action) {
					showNotification('success', 'Sesión iniciada correctamente.')
					setTimeout(function(){window.location.reload();}, 1500);
				} else {
					showNotification('error', res.msg)
				}
			} );
            }
        });

	},

	//Process Login
	do: function(callback) {
		var that = this;
		$.ajax({
			url: loginurl + '/proccess',
			type: "POST",
			data: $('#login').serialize(),
			headers: { 'X-Auth-Token' : token },
			success: function(res) {
				if (typeof callback == 'function') {
					callback(res);
				}
			}
		});
	}
};