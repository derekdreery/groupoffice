GO.calendar.PermissionsDialog = function(config) {

	if (!config) {
		config = {};
	}
    
	config.layout='border';
	config.modal=false;
	config.resizable=true;
	config.maximizable=true;
	config.width=500;
	config.height=450;
	config.closeAction='hide';
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
			this.beforeHide();
		},
		scope : this
	}];

	this.permissionsGrid = new GO.grid.MultiSelectGrid({
		allowNoSelection:true,
		donotcommitRecords:true,
		region:'center',
		loadMask:true,
		store:new GO.data.JsonStore({
			url: GO.settings.modules.calendar.url+'json.php',
			baseParams: {
				'task': 'permissions'
			},
			root: 'results',
			totalProperty: 'total',
			id: 'id',
			fields:['id','name','user_name','checked'],
			remoteSort:true
		})		
	});
	
	this.formPanel = new Ext.form.FormPanel({
		region:'north',
		height:75,
		defaultType: 'textfield',
		defaults: {
			anchor: '100%'
		},
		cls:'go-form-panel',
		url: GO.settings.modules.calendar.url+'json.php',
		waitMsgTarget:true,
		labelWidth:110,
		border:false,
		items:[
			this.selectGroup = new GO.form.ComboBox({
				hiddenName:'group_id',
				fieldLabel:GO.lang.userGroup,
				store:new GO.data.JsonStore({
					url: GO.settings.modules.groups.url+'non_admin_json.php',
					baseParams: {'task':'groups_all'},
					root: 'results',
					totalProperty: 'total',
					id: 'id',
					fields:['id','name','email','groupname'],
					remoteSort: true
				}),
				displayField: 'name',
				valueField: 'id',
				triggerAction: 'all',
				selectOnFocus:true,
				forceSelection: true,
				pageSize: parseInt(GO.settings['max_rows_list']),
				mode:'local',				
				editable:false,
				disabled:!GO.settings.modules['calendar']['write_permission']
			}),
			this.selectLevel = new GO.form.ComboBox({
				hiddenName:'acl_id',
				fieldLabel:GO.lang.strPermissions,
				store:new Ext.data.SimpleStore({
					id:0,
					fields : ['value', 'text'],
					data : [
						[1, GO.lang.permissionRead],
						[2, GO.lang.permissionWrite],
						[3, GO.lang.permissionDelete],
						[4, GO.lang.permissionManage]
					]
				}),
				valueField:'value',				
				displayField:'text',
				mode:'local',
				triggerAction:'all',
				editable:false,
				selectOnFocus:true,
				forceSelection:true
			})
		]
	});

	config.items=[this.formPanel, this.permissionsGrid];

	GO.calendar.PermissionsDialog.superclass.constructor.call(this, config);
	this.addEvents({
		'save' : true
	});


	this.permissionsGrid.on('change', function(grid, calendars, records)
	{
		this.calendars = calendars;
	}, this);

	this.permissionsGrid.store.on('load', function()
	{
		this.calendars = this.checked_calendars = this.permissionsGrid.store.reader.jsonData.checked_calendars;
	},this);

	this.selectGroup.on('select', function(combo, record){	
		this.permissionsGrid.store.baseParams.group_id = record.id;
		this.permissionsGrid.store.reload();
	},this)

	this.selectLevel.on('select', function(combo, record){
		this.permissionsGrid.store.baseParams.level_id = record.id;
		this.permissionsGrid.store.reload();
	},this)
    
};

Ext.extend(GO.calendar.PermissionsDialog, GO.Window, {

	group_id:0,	
	afterRender : function()
	{			   
		GO.calendar.PermissionsDialog.superclass.afterRender.call(this);
	},
	show : function(resources)
	{
		if (!this.rendered)
		{
			this.render(Ext.getBody());
		}

		if(!this.selectGroup.store.loaded){
			this.selectGroup.store.load({
				callback:function(){										
					this.show(resources);
				},scope:this
			});
			return false;
		}

		var group_id = this.selectGroup.store.data.items[0] ? this.selectGroup.store.data.items[0].id : "";
		
		if(!group_id){
			alert("You don't have permission to edit groups");			
		}else
		{
			this.resources = resources;

			this.selectGroup.setValue(group_id);
			this.permissionsGrid.store.baseParams.group_id = group_id;

			this.selectLevel.setValue(1);
			this.permissionsGrid.store.baseParams.level_id = 1;

			var title = (resources) ? GO.calendar.lang.resourcesPermissions : GO.calendar.lang.calendarsPermissions;
			this.setTitle(title);

			this.permissionsGrid.store.baseParams.resources = resources;


			this.permissionsGrid.store.reload();
			GO.calendar.GroupDialog.superclass.show.call(this);
		}		
	},
	
	submitForm : function(hide)
	{
		this.formPanel.form.submit({

			url:GO.settings.modules.calendar.url+'action.php',
			params: {
				'task':'save_permissions',								
				'calendars' : Ext.encode(this.calendars),
				'resources' : this.resources
			},
			waitMsg:GO.lang.waitMsgSave,
			success:function(form, action){				

				this.fireEvent('save');
				
				this.permissionsGrid.store.reload();

				if(hide)
				{
					this.hide();
				}
			},
			failure: function(form, action) {				
			},
			scope:this
		});
	},

	beforeHide : function()
	{
		var modified = false;		
		if(this.calendars.length != this.checked_calendars.length)
		{
			modified = true;
		}else
		{
			this.checked_calendars.sort();
			this.calendars.sort();			
			if(this.calendars.toString() != this.checked_calendars.toString())
			{
				modified = true;
			}
		}

                if(!modified || confirm(GO.lang.changesWillBeLost))
		{
			this.hide();		
		}		
	}
    
});
