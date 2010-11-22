GO.email.MessageDialog = function(config){	
	
	if(!config)
	{
		config={};
	}

	this.messagePanel = new GO.email.MessagePanel({
		autoScroll:true
	});
	
	config.layout='fit';
	config.title=GO.email.lang.message;
	config.stateId='email-message-dialog';
	config.maximizable=true;
	config.modal=false;
	config.width=500;
	config.height=400;
	config.resizable=true;
	config.minizable=true;
	config.closeAction='hide';	
	config.items=this.messagePanel;
	config.buttons=[{	
		text: GO.lang['cmdClose'],
		handler: function()
		{
			this.hide();
		},
		scope:this
	}];
	
	GO.email.MessageDialog.superclass.constructor.call(this, config);
}

Ext.extend(GO.email.MessageDialog, Ext.Window,{
		
	show : function(uid, mailbox, account_id)
	{
		if(!this.rendered)
			this.render(Ext.getBody());

		this.messagePanel.loadMessage(uid, mailbox, account_id);
				
		GO.email.MessageDialog.superclass.show.call(this);
	}
});