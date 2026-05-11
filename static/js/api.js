var api = {

	baseUrl: null,

	call : function( method, data, action, callback, noloading ) {
		var that = this;
		if (! noloading)
			showLoading();
		$.ajax({
			url: that.baseUrl+action,
			type: method,
			data: data,
			async: false,
			headers: { 'X-Auth-Token' : token },
			success: function(res) {
				hideLoading();
				if (typeof callback == 'function') {
					callback(res);
				}
			}
		});
	}


};

$.ajaxSetup({
    statusCode: {
      401: function(err){
      	showNotification('error', 'Debe volver a iniciar sesión. Redirigiendo...');
      	setTimeout(function(){
        	window.location.href = api.baseUrl;
      	}, 2000)
      }
    }
  });