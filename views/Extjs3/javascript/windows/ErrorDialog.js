GO.ErrorDialog = function(config) {
	config = config || {};

	Ext.apply(config, {
		width : 550,
		height : 300,
		closeAction : 'hide',
		plain : true,
		border : false,
		closable : true,
		title : GO.lang.strError,
		modal : false, 
		
		layout:'fit',
		items : [
		this.messagePanel = new Ext.Panel({							
			cls : 'go-error-dialog',		
			autoScroll:true,
			html : ''
		})],
		buttons : [{
			text : GO.lang.cmdClose,
			handler : function() {
				this.hide();
			},
			scope : this
		}]
	});

	GO.ErrorDialog.superclass.constructor.call(this, config);
}

Ext.extend(GO.ErrorDialog, GO.Window, {

	show : function(error, details) {

		if (!this.rendered)
			this.render(Ext.getBody());

		//				this.detailPanel.collapse();
				
		if(!error)
			error = "No error message given";
		
		if(details)
			error += "<br /><br />"+details;

		this.messagePanel.body.update(error);
				
		//				if(GO.util.empty(details))
		//				{
		//					this.detailPanel.hide();
		//				}else
		//				{
		//					this.detailPanel.show();
		//					this.detailPanel.body.update('<pre>'+details+'</pre>');
		//				}

		GO.ErrorDialog.superclass.show.call(this);
	}
});
GO.errorDialog = new GO.ErrorDialog();
