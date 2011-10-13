/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.calendar.ViewDialog = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	var checkCol = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'selected',
		width: 55
	});
	 
	this.calendarsStore = new Ext.data.GroupingStore({
		baseParams: {
			'task': 'view_calendars',
			view_id: this.view_id
		},
		reader: new Ext.data.JsonReader({
			root: 'results',
			id: 'id',
			totalProperty: 'total',
			fields:['id','name','user_name','selected', 'group_name']
		}),
		proxy: new Ext.data.HttpProxy({
			url: GO.settings.modules.calendar.url+'json.php'
		}),
		sortInfo:{
			field: 'name',
			direction: "ASC"
		},
		groupField:'group_name',
		remoteSort:true
	});	 
	 
	this.calendarsGrid = new GO.grid.GridPanel( {
		region:'center',
		layout:'fit',
		paging:false,
		border:false,
		split:true,
		plugins:checkCol,
		store: this.calendarsStore,
		columns:[
		checkCol,
		{
			header:GO.lang.strName,
			dataIndex: 'name'
		},{
			header:GO.lang.strOwner,
			dataIndex: 'user_name'
		},{
      header:GO.calendar.lang.group,
			dataIndex: 'group_name',
			id:'group_name',
			hidden:true
		}],
		view:new Ext.grid.GroupingView({
        autoFill: true,
        forceFit:true,
        groupTextTpl: '{text}',
        emptyText: GO.lang['strNoItems']
    }),
		sm: new Ext.grid.RowSelectionModel(),
		loadMask: true
	});


	this.propertiesTab = new Ext.Panel({
		title:GO.lang.strProperties,
		layout:'border',
		//anchor: '100% 100%',
		items: [new Ext.Panel({
			layout:'form',
			region:'north',
			height:110,
			defaultType: 'textfield',
			defaults: {
				anchor: '100%'
			},
			cls:'go-form-panel',
			waitMsgTarget:true,
			labelWidth: 75,
			border:false,
			items: [
			this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang.strUser,
				disabled: !GO.settings.modules['calendar']['write_permission']
			}),
			{
				fieldLabel: GO.lang.strName,
				name: 'name',
				allowBlank:false		
			},this.merge = new Ext.form.Checkbox({
				name:'merge',
				boxLabel: GO.calendar.lang.merge,
				hideLabel : true
			}),GO.calendar.ownColor = new Ext.form.Checkbox({
				name:'owncolor',
				boxLabel: GO.calendar.lang.ownColor,
				hideLabel : true,
				disabled : true,
				checked : true
			})
			]
		}),
		this.calendarsGrid]

	});

	this.readPermissionsTab = new GO.grid.PermissionsPanel({
		
		});



	this.formPanel = new Ext.form.FormPanel({
		url: GO.settings.modules.calendar.url+'action.php',
		//labelWidth: 75, // label settings here cascade unless overridden
		defaultType: 'textfield',
		waitMsgTarget:true,
		border:false,
		items:[{
			hideLabel:true,
			deferredRender:false,
			xtype:'tabpanel',
			activeTab: 0,
			border:false,
			anchor: '100% 100%',
			items:[
			this.propertiesTab,
			this.readPermissionsTab
			]
		}]
	});
	
	
	GO.calendar.ViewDialog.superclass.constructor.call(this,{
		title: GO.calendar.lang.view,
		layout:'fit',
		modal:false,
		height:500,
		width:400,
		closeAction:'hide',
		items: this.formPanel,
		buttons:[
		{
			text:GO.lang.cmdOk,
			handler: function(){
				this.save(true)
			},
			scope: this
		},
		{
			text:GO.lang.cmdApply,
			handler: function(){
				this.save(false)
			},
			scope: this
		},

		{
			text:GO.lang.cmdClose,
			handler: function(){
				this.hide()
			},
			scope: this
		}
		]
	});

	this.merge.on('check',function(checkbox,value) {
		if (value) {
			GO.calendar.ownColor.setDisabled(false);
		} else {
			GO.calendar.ownColor.setDisabled(true);
		}
		});

}

Ext.extend(GO.calendar.ViewDialog, GO.Window, {
	
	initComponent : function(){
		
		this.addEvents({
			'save' : true
		});
		
		GO.calendar.ViewDialog.superclass.initComponent.call(this);
		
		
	},
				
	show : function (view_id){
		if(!this.rendered)
			this.render(Ext.getBody());
					
		if(view_id > 0)
		{
			if(view_id!=this.view_id)
			{
				this.loadView(view_id);
			}else
			{
				GO.calendar.ViewDialog.superclass.show.call(this);
			}
		}else
		{
			this.view_id=0;
			this.formPanel.form.reset();
			this.propertiesTab.show();

			this.readPermissionsTab.setDisabled(true);
			
			
			this.calendarsStore.baseParams['view_id']=0;
			this.calendarsStore.reload();

			//this.selectUser.setValue(GO.settings['user_id']);
			//this.selectUser.setRawValue(GO.settings['name']);

			GO.calendar.ViewDialog.superclass.show.call(this);
		}
	},
	loadView : function(view_id)
	{
		this.formPanel.form.load({
			url: GO.settings.modules.calendar.url+'json.php',
			params: {
				view_id:view_id,
				task: 'view'
			},
			waitMsg:GO.lang.waitMsgLoad,
			success: function(form, action) {
				this.view_id=view_id;
				this.selectUser.setRawValue(action.result.data.user_name);
				this.readPermissionsTab.setAcl(action.result.data.acl_id);

				
				this.calendarsStore.baseParams['view_id']=view_id;
				this.calendarsStore.reload();
				
				GO.calendar.ViewDialog.superclass.show.call(this);
			},
			failure:function(form, action)
			{
				Ext.Msg.alert(GO.lang.strError, action.result.feedback)
			},
			scope: this
		});
	},
	save : function(hide)
	{
		var calendars=[];
			
		for (var i = 0; i < this.calendarsStore.data.items.length;  i++)
		{
			if(this.calendarsStore.data.items[i].get('selected')=='1')
			{
				calendars.push(this.calendarsStore.data.items[i].get('id'));
			}
		}
		
		this.formPanel.form.submit({
				
			url:GO.settings.modules.calendar.url+'action.php',
			params: {
				'task' : 'save_view',
				'view_id': this.view_id,
				'merge' : this.merge,
				'view_calendars' : Ext.encode(calendars)
			},
			waitMsg:GO.lang.waitMsgSave,
			success:function(form, action){
								
										
				if(action.result.view_id)
				{
					this.view_id=action.result.view_id;
					this.readPermissionsTab.setAcl(action.result.acl_id);

				//this.loadAccount(this.view_id);
				}
				
				this.fireEvent('save');
				
				if(hide)
				{
					this.hide();
				}
					
					
			},

			failure: function(form, action) {
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang.strErrorsInForm;
				}else
				{
					error = action.result.feedback;
				}
					
				Ext.MessageBox.alert(GO.lang.strError, error);
			},
			scope:this

		});
			
	}
});
