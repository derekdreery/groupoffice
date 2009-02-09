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
		items : [this.messagePanel = new Ext.Panel({
							region : 'center',
							cls : 'go-error-dialog',
							autoHeight:true,
							html : ''
						}), this.detailPanel = new Ext.Panel({
					region : 'south',
					collapsible : true,
					collapsed : true,
					autoScroll : true,
					height : 150,
					title : GO.lang.errorDetails,
					//frame:true,
					titleCollapse:true,
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

				this.detailPanel.collapse();

				this.messagePanel.body.update(error);
				this.detailPanel.body.update(details);

				GO.ErrorDialog.superclass.show.call(this);
			}
		});
GO.errorDialog = new GO.ErrorDialog();
