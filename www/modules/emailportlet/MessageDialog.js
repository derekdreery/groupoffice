GO.emailportlet.MessageDialog = function(config){	
	
	if(!config)
	{
		config={};
	}

	this.messagePanel = new GO.email.MessagePanel({
		autoScroll:true
	});
	
	config.layout='fit';
	config.title=GO.email.lang.message;
	config.maximizable=true;
	config.modal=false;
	config.width=500;
	config.height=400;
	config.resizable=false;	
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
	
	GO.emailportlet.MessageDialog.superclass.constructor.call(this, config);
}

Ext.extend(GO.emailportlet.MessageDialog, Ext.Window,{
		
	show : function(uid, mailbox, account_id)
	{
		if(!this.rendered)
			this.render(Ext.getBody());

		this.messagePanel.loadMessage(uid, mailbox, account_id);
		if(this.type_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.tickets.url+'json.php',
				
				success:function(form, action)
				{
					this.setWritePermission(action.result.data.write_permission);					
					this.readPermissionsTab.setAcl(action.result.data.acl_id);
					
					this.selectUser.setRemoteText(action.result.data.user_name);
					
					GO.tickets.TypeDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
			});
		}else
		{
			GO.emailportlet.MessageDialog.superclass.show.call(this);
		}
	}
});