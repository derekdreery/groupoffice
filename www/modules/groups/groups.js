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
 * @author Boy Wijnmaalen <bwijnmaalen@intermesh.nl>
 */
 
 Ext.namespace('GO.groups')
 
 GO.groups.MainPanel = function(config)
 { 	
 	if(!config)
 	{
 		config={};
 	}
	
	this.storeAllGroups = new GO.data.JsonStore({
	    url: GO.url('groups/group/grid'),
	    baseParams: {task: 'groups',for_managing:1},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'name', 'user_id', 'user_name','acl_id','admin_only'],
	    remoteSort: true
	});			

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
        {header: GO.groups.lang.groups, dataIndex: 'name', width: 300},
        {header: GO.groups.lang.owner, dataIndex: 'user_name'},
				{header: GO.groups.lang.adminOnly, dataIndex: 'admin_only', renderer: function(value){return (value == 1)?GO.lang.cmdYes:GO.lang.cmdNo;}}
    ]
	});  
	
	this.searchField = new GO.form.SearchField({
		store: this.storeAllGroups,
		width:320
	});
		    	

	var tbar = new Ext.Toolbar({
		cls:'go-head-tb',
		items: [
		{
			iconCls: 'btn-add',
			text: GO.lang['cmdAdd'],
			cls: 'x-btn-text-icon',
			handler: function(){this.showGroupDialog(0);},
			scope: this,
			disabled: !GO.settings.modules.groups.write_permission
		},
		{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){this.deleteSelected();},
			scope: this,
			disabled: !GO.settings.modules.groups.write_permission
		},'-',GO.lang['strSearch'] + ':', this.searchField
		]});
      
  config.layout='fit';
  config.id='groups-grid-overview-groups';
  config.store=this.storeAllGroups;
  config.cm=columnModel;
  config.sm=new Ext.grid.RowSelectionModel({singleSelect: false});
  config.tbar=tbar;
  config.paging=true;
	config.noDelete= !GO.settings.modules.groups.write_permission;
  config.viewConfig={
  	autoFill:true,
  	forceFit:true
  };
  

	GO.groups.MainPanel.superclass.constructor.call(this,config);

};	

Ext.extend(GO.groups.MainPanel, GO.grid.GridPanel,{
		
		afterRender : function(){
			GO.groups.MainPanel.superclass.afterRender.call(this);
			
			this.on("rowdblclick",this.rowDoubleClick, this);
			
			this.storeAllGroups.load();
		},
		
		rowDoubleClick : function(grid)
		{
			this.showGroupDialog(grid.selModel.selections.items[0].data);
		},
		
		showGroupDialog : function(group)
		{
			if(!this.new_dialog)
			{
				this.userStore = new GO.data.JsonStore({
				    url: GO.url("groups/group/getUsers"),
				    baseParams: {id: group.id},
				    root: 'results',
				    id: 'id',
				    fields: ['id', 'user_id', 'name', 'username', 'email'],
				    remoteSort: true
				});			
	
				var columnModel =  new Ext.grid.ColumnModel({
					defaults:{
						sortable:true
					},
					columns:[
							{header: GO.lang.strName, dataIndex: 'name'},
              {header: GO.lang.strUsername, dataIndex: 'username'},
			        {header: GO.lang.strEmail, dataIndex: 'email'}	        			        
			    ]
				});
			  
					
			  this.userGrid = new GO.grid.GridPanel({
					title:GO.groups.lang.groupMembers,
			  	region:'center',
					paging:true,
		    	ds: this.userStore,
	        cm: columnModel,
	        sm: new Ext.grid.RowSelectionModel({singleSelect: false}),
		    	autoScroll:true,			    	  
	        border: false,
	        viewConfig:{forceFit:true, autoFill:true},
	        tbar:[
			    	{
			    		iconCls: 'btn-add', 
			    		text: GO.lang['cmdAdd'], 
			    		cls: 'x-btn-text-icon', 
			    		handler:function(){
						    if(!this.addUsersDialog)
								{
									this.addUsersDialog = new GO.dialog.SelectUsers({
										handler:function(allUserGrid)
										{
											if(allUserGrid.selModel.selections.keys.length>0)
											{
												this.userStore.baseParams['add_users']=Ext.encode(allUserGrid.selModel.selections.keys);
												this.userStore.reload();
												delete this.userStore.baseParams['add_users'];
											}
										},
										scope:this				
									});
								}
								this.addUsersDialog.show();
			    		}, 
			    		scope: this
			    	},
			    	{
			    		iconCls: 'btn-delete', 
			    		text: GO.lang['cmdDelete'], 
			    		cls: 'x-btn-text-icon', 
			    		handler: function(){
			    			this.userGrid.deleteSelected();
			    		},  
			    		scope: this
			    	}
		        ]
		    });
							
				this.newFormPanel = new Ext.FormPanel({
					region:'north',
					height:30,
					waitMsgTarget:true,
					labelWidth: 85,
					defaultType: 'textfield',
    			cls:'go-form-panel',
    			border: false,
    			items:[
    				{
    					fieldLabel: GO.lang.strName,
    					name: 'name',
    					anchor: '100%',
    					allowBlank: false
    				}
    			]
				});

				if(GO.settings.has_admin_permission) {
					var adminOnlyCheckBox = new Ext.form.Checkbox({
              name: 'admin_only',
							checked: group.admin_only,
							boxLabel: GO.groups.lang.adminOnlyLabel,
							hideLabel:true
          });
					this.newFormPanel.height = 80;
					this.newFormPanel.add(adminOnlyCheckBox);
				}
				
				var buttons = [
					{text: GO.lang['cmdOk'], handler: function(){this.saveNewGroup(true);}, scope: this},
					/*{text: GO.lang['cmdApply'], handler: function(){this.saveNewGroup();}, scope: this},*/
					{text: GO.lang['cmdClose'], handler: function(){this.new_dialog.hide();}, scope: this}
				];			
				
				var focusFirstField = function(){
					this.newFormPanel.items.items[0].focus();
				};

				this.firstTab = new Ext.Panel({
					layout: 'border',
					title : GO.lang.strProperties,
					autoScroll : true,					
					items : [this.newFormPanel,this.userGrid]
				});

				this.permissionsTab = new GO.grid.PermissionsPanel({
					title:GO.groups.lang.managePermissions,
					hideLevel:true
				});

				var items = [this.firstTab,this.permissionsTab];

				this.tabPanel = new Ext.TabPanel({
					activeTab : 0,
					deferredRender : false,
					border : false,
					items : items
				});
				
				this.new_dialog = new Ext.Window({
				//	layout: 'border',
					modal:false,
					shadow: false,
					height: 500,
					width: 500,
					layout:'fit',
					plain: true,
					closeAction: 'hide',
					title: GO.groups.lang.group,					
				//	items: [this.newFormPanel,this.userGrid],
					items: [this.tabPanel],
					buttons: buttons,
					focus: focusFirstField.createDelegate(this)
				});
			}
			this.tabPanel.setActiveTab(0);
			this.new_dialog.show();
			this.setGroup(group);
			
			
		},
		
		setGroup : function(group)
		{			
			this.group_id = this.userStore.baseParams['group_id']=group.id;

			var adminOnlyField = this.newFormPanel.form.findField('admin_only');
						
			if(this.group_id > 0)
			{
				this.userGrid.setDisabled(false);
				this.userStore.load({
					callback: function(){
						this.newFormPanel.form.findField('name').focus();
					},
					scope: this
				});
				if(group.name)
				{
					this.newFormPanel.form.findField('name').setValue(group.name);

					if(adminOnlyField)
						adminOnlyField.setValue(group.admin_only);
				}
				this.permissionsTab.setAcl(group.acl_id);
				
			}else
			{
				this.userGrid.setDisabled(true);
				this.userStore.loadData({"total":0,"results":[]});
				this.newFormPanel.form.findField('name').setRawValue('');
				if(adminOnlyField)
					adminOnlyField.setValue(0);
				
			}
			
		},
		
		saveNewGroup : function(hide)
		{			
			this.newFormPanel.form.submit({
				waitMsg:GO.lang.waitMsgSave,
				url:GO.settings.modules.groups.url+'action.php',
				params:
				{
					action : 'save_group',
					group_id : this.group_id
				},
				success:function(form, action){
					var group = {id: action.result.group_id, acl_id:action.result.acl_id};
					this.setGroup(group);
					this.storeAllGroups.reload();
					if (action.result.group_id)
					{
						this.permissionsTab.show();
					}else
					{
						this.new_dialog.hide();
					}					
				},
				failure: function(form, action) {					
					if(action.failureType != 'client')
					{					
						Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);			
					}else
					{
						Ext.MessageBox.alert(GO.lang['strError'], 'Errors in form');
					}
				},
				scope: this
			});
		}
});

GO.moduleManager.addModule('groups', GO.groups.MainPanel, {
		title : GO.groups.lang.groups,
		iconCls : 'go-tab-icon-groups',
		admin :true
});