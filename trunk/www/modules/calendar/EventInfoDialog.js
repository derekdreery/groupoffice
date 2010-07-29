GO.calendar.EventInfoDialog = function(config){

	if(!config)
	{
		config = {};
	}

	config.layout='fit';
	config.modal=false;
	config.border=false;
	config.width=400;
	config.autoHeight=true;
	config.title=GO.calendar.lang.eventInfo;
	config.resizable=false;
	config.plain=true;
	config.shadow=false,
	config.closeAction='hide';
	config.buttons=[{
		text:GO.lang['cmdClose'],
		handler: function()
		{
			this.hide()
		},
		scope: this
	}];

	config.items=[this.eventPanel = new GO.calendar.EventPanel({autoHeight:true,border:false})];

	GO.calendar.EventInfoDialog.superclass.constructor.call(this,config);

}

Ext.extend(GO.calendar.EventInfoDialog, Ext.Window, {

	show : function(event_id)
	{
		if(!this.rendered)
			this.render(Ext.getBody());

		this.eventPanel.load(event_id);

		GO.calendar.EventInfoDialog.superclass.show.call(this);
	}
});