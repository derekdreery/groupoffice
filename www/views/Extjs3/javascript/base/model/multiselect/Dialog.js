GO.base.model.multiselect.dialog = function(config){
	
	
	this.multiselectPanel = new GO.base.model.multiselect.panel({
			url:config.url,
			cm:config.cm,
			fields:config.fields,
			model_id:config.model_id
		});
		
	delete config.url;
	delete config.cm;
	delete config.fields;
	delete config.model_id;
	
	
	Ext.apply(this, config);
	

	GO.base.model.multiselect.dialog.superclass.constructor.call(this, {
		layout: 'fit',
		modal:false,
		height:400,
		width:600,
		closeAction:'hide',
		title:config.title,
		items: this.multiselectPanel,
		buttons: [
		{
			text: GO.lang['cmdOk'],
			handler: function (){
				this.multiselectPanel.callHandler(true);
			},
			scope:this
		},
		{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope: this
		}]
	});
};

Ext.extend(GO.base.model.multiselect.dialog, GO.Window, {

});