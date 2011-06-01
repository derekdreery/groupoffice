GO.ErrorDialog = function(config) {
	config = config || {};

	Ext.apply(config, {
		width : 550,
		height : 300,
		autoHeight:true,
		closeAction : 'hide',
		plain : true,
		border : false,
		closable : true,
		title : GO.lang.strError,
		modal : false,
		items : [
				this.messagePanel = new Ext.FormPanel({
							region : 'center',
							cls : 'go-error-dialog',
							autoHeight:true,
							html : ''
						}), 
				this.detailPanel = new Ext.Panel({
					region : 'south',
					collapsible : true,
					collapsed:true,
					height : 150,
					title : GO.lang.errorDetails,
					titleCollapse:true,
					autoScroll:true,
					html:''
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

				this.detailPanel.collapse();

				this.messagePanel.body.update(error);
				
				if(GO.util.empty(details))
				{
					this.detailPanel.hide();
				}else
				{
					this.detailPanel.show();
					this.detailPanel.body.update('<pre>'+details+'</pre>');
				}

				GO.ErrorDialog.superclass.show.call(this);
			}
		});
GO.errorDialog = new GO.ErrorDialog();
