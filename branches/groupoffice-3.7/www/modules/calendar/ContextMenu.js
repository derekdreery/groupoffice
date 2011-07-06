GO.calendar.ContextMenu = function(config){

	if(!config)
	{
		config = {};
	}

	config.items=[
	{
		iconCls: 'btn-properties',
		text:GO.calendar.lang.showInfo,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.showEventInfoDialog();
		}
	},new Ext.menu.Separator(),
	this.actionCopy = new Ext.menu.Item({
		iconCls: 'btn-copy',
		text: GO.lang.copy,
		cls: 'x-btn-text-icon',
		scope:this,		
		disabled:true,
		handler: function()
		{
			this.showSelectDateDialog(true, false);
		}
	}),
	this.actionCut = new Ext.menu.Item({
		iconCls: 'btn-cut',
		text: GO.calendar.lang.move,
		cls: 'x-btn-text-icon',
		scope:this,
		disabled:true,
		handler: function()
		{
			if(this.event.repeats)
			{
				this.menuHandler();
			}else
			{
				this.showSelectDateDialog(false, false);
			}
		}
	}),new Ext.menu.Separator(),
	this.actionDelete = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		scope:this,
		disabled:true,
		handler: function()
		{
			this.fireEvent("deleteEvent", this);
		}
	}),'-',
	this.newMenuItem = new GO.NewMenuItem()
	]

	if (GO.email) {
		this.actionCreateMail = new Ext.menu.Item({
			iconCls: 'btn-email',
			text:GO.calendar.lang.sendEmailParticipants,
			cls: 'x-btn-text-icon',
			scope:this,
			handler: function()
			{
				this.showCreateMailDialog();
			}
		});
		config.items.splice(1,0,this.actionCreateMail);
	}

	if(GO.timeregistration)
	{
		this.actionAddTimeRegistration = new Ext.menu.Item({
			text: GO.calendar.lang.addTimeRegistration,
			iconCls: 'go-menu-icon-timeregistration',
			cls: 'x-btn-text-icon',
			scope:this,
			handler: function()
			{
				this.showAddTimeRegistrationDialog();
			}
		});

		config.items.splice(1,0,this.actionAddTimeRegistration);
	}

/*
	this.selectDateDialog = new GO.calendar.SelectDateDialog();
	this.selectDateDialog.on('updateEvent', function(event, isCopy, repeats, offset, new_event_id)
	{
		this.fireEvent('updateEvent', this, event, isCopy, repeats, offset, new_event_id);
	}, this);			
	*/
	GO.calendar.ContextMenu.superclass.constructor.call(this,config);

	this.addEvents({
		'updateEvent' : true
	});

}

Ext.extend(GO.calendar.ContextMenu, Ext.menu.Menu, {

	event:null,
	view_id: 0,
	
	setEvent : function(event, view_id)
	{
		this.event = event;
		
		this.view_id = (view_id) ? view_id : 0;

		this.actionCopy.setDisabled(this.event.read_only);
		this.actionCut.setDisabled(this.event.read_only);
		this.actionDelete.setDisabled(this.event.read_only);

		if (GO.email)
			this.actionCreateMail.setDisabled(event.participant_ids=='');

		this.newMenuItem.setLinkConfig({
			type:1,
			id:event.event_id
		});
	},
	setParticipants : function(participant_ids) {
		this.participant_ids = participant_ids;
	},
	showCreateMailDialog : function() {
		if (GO.email) {
			Ext.Ajax.request({
				url: GO.settings.modules.calendar.url + 'json.php',
				params : {
					'task' : 'user_email_addresses',
					'participant_ids' : this.participant_ids
				},
				success : function(response,options) {
					var responseText = Ext.decode(response.responseText);
					var users = responseText.results;

					var emails = [];
					for (var i = 0; i < users.length; i++) {
						emails.push('"' + users[i].name + '" <'
							+ users[i].email + '>');
					}

					if (emails.length>0)
						var str = emails.join(', ');
					else
						var str = '';

					var composer = GO.email.showComposer({
						account_id: GO.moduleManager.getPanel('email').account_id
					});

					composer.addRecipients('to',str);
//					var field = composer.formPanel.form.findField('to');
//					field.setValue(str);
//					console.log(composer.formPanel.form.findField('to').getValue());
//					console.log(field.getValue());
//					console.log(GO.email.composers.length);
//					composer.formPanel.doLayout();
				},
				scope : this
			});
		}
	},
	showAddTimeRegistrationDialog : function()
	{
		if(!this.addTimeRegistrationDialog)
		{
			this.addTimeRegistrationDialog = new GO.timeregistration.addTimeRegistrationDialog();
		}
		this.addTimeRegistrationDialog.show(this.event);
	},
	showSelectDateDialog : function(isCopy, repeat)
	{
		if(!this.selectDateDialog)
		{
			this.selectDateDialog = new GO.calendar.SelectDateDialog();


			this.selectDateDialog.on('updateEvent', function(obj, new_event_id, is_visible)
			{
				this.fireEvent('updateEvent', obj, new_event_id, is_visible);
			}, this);
		}
	
		this.selectDateDialog.show(this.event, isCopy, repeat, this.view_id);
	},
	showEventInfoDialog : function()
	{
		GO.linkHandlers[1].call(this, this.event.event_id);
	},
	menuHandler : function()
	{
		if(!this.menuRecurrenceDialog)
		{
			this.menuRecurrenceDialog = new GO.calendar.RecurrenceDialog();

			this.menuRecurrenceDialog.on('single', function()
			{
				this.showSelectDateDialog(false, false);
				this.menuRecurrenceDialog.hide();
			},this)

			this.menuRecurrenceDialog.on('entire', function()
			{
				this.showSelectDateDialog(false, true);
				this.menuRecurrenceDialog.hide();
			},this)

			this.menuRecurrenceDialog.on('cancel', function()
			{
				this.menuRecurrenceDialog.hide();
			},this)
		}
		this.menuRecurrenceDialog.show();
	}
});