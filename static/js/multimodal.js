var multimodal = {
	
	//MODAL
	obj: $('#multimodal'),

	//FUNCTIONS
	show : function() { this.obj.modal('show') },
	hide : function() { var that = this; console.log(that.confirmHide); if(that.confirmHide == true) { if(confirm('¿Seguro desea cerrar esta ventana?')) { that.obj.modal('hide') } } else { that.obj.modal('hide') } },
	toggle : function() { this.obj.modal('toggle') },
	reset : function () { this.resetHeader(); this.resetBody(); this.resetFooter(); },
	callback : null,
	
	// RETURN MODAL PARTS METHODS
	outModal : function () { return this.obj; },
	outHeader : function () { return this.obj.find('div.modal-header'); },
	outBody : function () { return this.obj.find('div.modal-body'); },
	outFooter : function () { return this.obj.find('div.modal-footer'); },

	//SET METHODS
	setSize: function(c) { this.obj.find('div.modal-dialog').removeClass().addClass('modal-dialog '+c); },
	setHeader : function(c) { this.obj.find('div.modal-header').html( c + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>' ); this.setSize('modal-lg'); },
	setDefaultHeader : function() { this.outModal().removeClass().addClass('modal fade') },
	setHeaderStyle : function(t) { this.setDefaultHeader(); this.obj.addClass(t) },
	setBody : function(c) { this.obj.find('div.modal-body').html( c ); },
	setFooter : function(c) { this.obj.find('div.modal-footer').html( c ); },
	setCloseFooter: function() { this.obj.find('div.modal-footer').html( '<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>' ); },
	setCallbackFooter: function(btn) { this.obj.find('div.modal-footer').html( '<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>'+btn ); },

	//RESET METHODS
	resetHeader : function() { this.obj.find('div.modal-header').html('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'); },
	resetBody : function() { this.obj.find('div.modal-body').html(''); },
	resetFooter : function() { this.obj.find('div.modal-footer').html(''); },

};