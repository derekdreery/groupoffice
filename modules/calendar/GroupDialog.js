GO.calendar.GroupDialog = function(config) {

	if (!config) {
		config = {};
	}
    
	this.buildForm();
	var focusFirstField = function() {
		this.propertiesPanel.items.items[0].focus();
	};
    
	config.collapsible = true;
	config.maximizable = true;
	config.layout = 'fit';
	config.modal = false;
	config.resizable = false;
	config.width = 500;
	config.height = 450;
	config.closeAction = 'hide';
	config.title = GO.calendar.lang.resource_group;
	config.items = this.formPanel;
	config.focus = focusFirstField.createDelegate(this);
	config.buttons = [{
		text : GO.lang['cmdOk'],
		handler : function() {
			this.submitForm(true);
		},
		scope : this
	}, {
		text : GO.lang['cmdApply'],
		handler : function() {
			this.submitForm();
		},
		scope : this
	}, {
		text : GO.lang['cmdClose'],
		handler : function() {
			this.hide();
		},
		scope : this
	}];

	GO.calendar.GroupDialog.superclass.constructor.call(this, config);
	this.addEvents({
		'save' : true
	});
    
};

Ext.extend(GO.calendar.GroupDialog, GO.Window, {
	show : function(group_id, config)
	{
		if (!this.rendered)
		{
			this.render(Ext.getBody());
		}
		this.formPanel.form.reset();
		this.tabPanel.setActiveTab(0);
		if (!group_id)
		{
			group_id = 0;
		}
		this.setGroupId(group_id);
        
		if (this.group_id > 0)
		{
			this.formPanel.load({
				url : GO.settings.modules.calendar.url + 'json.php',
				waitMsg : GO.lang['waitMsgLoad'],
				success : function(form, action)
				{
					this.groupAdminsPanel.setGroupId(action.result.data.id);
					this.selectUser.setRemoteText(action.result.data.user_name);

					if(this.group_id == 1)
					{
						this.tabPanel.hideTabStripItem('permissions-panel');
						this.selectUser.setDisabled(true);
						this.setTitle(GO.calendar.lang.calendar_group);
					}else
					{
						this.tabPanel.unhideTabStripItem('permissions-panel');
						this.selectUser.setDisabled(false);
						this.setTitle(GO.calendar.lang.resource_group);
					}

					GO.calendar.GroupDialog.superclass.show.call(this);
				},
				failure : function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope : this
			});            
		} else
{			
			this.groupAdminsPanel.setGroupId(0);
			GO.calendar.GroupDialog.superclass.show.call(this);
		}
	},
	setGroupId : function(group_id)
	{
		this.formPanel.form.baseParams['group_id'] = group_id;
		this.group_id = group_id;
	},
	submitForm : function(hide)
	{
		this.formPanel.form.submit({
			url : GO.settings.modules.calendar.url + 'action.php',
			params : {
				'task' : 'save_group'
			},
			waitMsg : GO.lang['waitMsgSave'],
			success : function(form, action)
			{
				if (action.result.group_id)
				{
					this.groupAdminsPanel.setGroupId(action.result.group_id);
					this.setGroupId(action.result.group_id);
				}
		
				var fields = (this.group_id == 1) ? action.result.fields : false;
				
				this.fireEvent('save', this, this.group_id, fields);
				
				if (hide)
				{
					this.hide();
				}
			},
			failure : function(form, action)
			{
				if (action.failureType == 'client')
				{
					Ext.MessageBox.alert(GO.lang['strError'],
						GO.lang['strErrorsInForm']);
				} else
{
					Ext.MessageBox.alert(GO.lang['strError'],
						action.result.feedback);
				}
			},
			scope : this
		});
	},
	buildForm : function()
	{
		this.propertiesPanel = new Ext.Panel({
			title : GO.lang['strProperties'],
			cls : 'go-form-panel',
			layout : 'form',
			autoScroll : true,
			items : [this.selectUser = new GO.form.SelectUser({
				fieldLabel : GO.lang['strUser'],
				disabled : !GO.settings.modules['calendar']['write_permission'],
				value : GO.settings.user_id,
				anchor : '100%'
			}), {
				xtype : 'textfield',
				name : 'name',
				anchor : '100%',
				fieldLabel : GO.lang.strName
			},{
                                xtype:'checkbox',
                                name:'show_not_as_busy',
                                hideLabel: true,
                                boxLabel:GO.calendar.lang.showNotBusy
                        }]
		});

		if(GO.customfields && GO.customfields.types["1"])
		{
			if(GO.customfields.types["1"].panels.length > 0)
			{
				var cfFieldset = new Ext.form.FieldSet({
					autoHeight:true,
					title:GO.customfields.lang.customfields
				});
				for(var i=0;i<GO.customfields.types["1"].panels.length;i++)
				{
					cfFieldset.add({
						xtype:'checkbox',
						name:'fields[cf_category_'+GO.customfields.types["1"].panels[i].category_id+']',
						hideLabel: true,
						boxLabel:GO.customfields.types["1"].panels[i].title
					});
				}
				this.propertiesPanel.add(cfFieldset);
			}
		}
	
		var items = [this.propertiesPanel];

		this.groupAdminsPanel = new GO.calendar.GroupAdminsPanel({
			id:'permissions-panel'
		});

		items.push(this.groupAdminsPanel);
        
		this.tabPanel = new Ext.TabPanel({
			activeTab : 0,
			deferredRender : false,
			border : false,
			items : items,
			anchor : '100% 100%'
		});
        
		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget : true,
			url : GO.settings.modules.calendar.url + 'action.php',
			border : false,
			baseParams : {
				task : 'group'
			},
			items : this.tabPanel
		});        
	}
    
});
