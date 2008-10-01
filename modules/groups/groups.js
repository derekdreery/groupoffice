/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: groups.js 2943 2008-09-02 13:11:05Z mschering $
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
 
	//Ext.QuickTips.init();
	//Ext.form.Field.prototype.msgTarget = 'side';
	
	
	this.storeAllGroups = new GO.data.JsonStore({
	    url: GO.settings.modules.groups.url+'json.php',
	    baseParams: {action: 'groups'},
	    root: 'results',
	    id: 'id',
	    fields: ['id', 'name', 'user_id', 'user_name'],
	    remoteSort: true
	});			
	
	//this.storeAllGroups.setDefaultSort('name', 'ASC');


	var columnModel =  new Ext.grid.ColumnModel([
        {id:'name',header: GO.groups.lang.groups, dataIndex: 'name', width: 300},
        {id:'user_name',header: GO.groups.lang.owner, dataIndex: 'user_name'}	        			        
    ]);	
  columnModel.defaultSortable = true;
    
	var tbar = new Ext.Toolbar({		
			cls:'go-head-tb',
			items: [
    	{iconCls: 'btn-add', text: GO.lang['cmdAdd'], cls: 'x-btn-text-icon', handler: function(){this.showGroupDialog(0);}, scope: this},
    	{iconCls: 'btn-delete', text: GO.lang['cmdDelete'], cls: 'x-btn-text-icon', handler: function(){this.deleteSelected();},  scope: this}
      ]});
      
  config.layout='fit';
  config.id='groups-grid-overview-groups';
  config.store=this.storeAllGroups;
  config.cm=columnModel;
  config.sm=new Ext.grid.RowSelectionModel({singleSelect: false});
  config.tbar=tbar;
  config.paging=true;
  config.autoExpandColumn='user_name';

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
			//this.groupName = this.grid.selModel.selections.items[0].data.name;
			this.showGroupDialog(grid.selModel.selections.items[0].data);
			//alert(this.grid.selModel.selections.items[0].data.name);
		},
		

		
		showGroupButtonHandler : function (btn)
		{
			this.newFormPanel.container.mask(GO.lang['waitMsgSave'],'x-mask-loading');
			
			switch(btn.id)
			{
				case 'ok':
					this.saveNewGroup(true);
				break;
				case 'apply':
					this.saveNewGroup();
				break;
			}
		},	
		
		addUserButtonHandler : function (btn)
		{
			switch(btn.id)
			{
				case 'add':		
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
				break;
				case 'delete':
					//TODO nieuwe standard delete functie gebruiken
					GO.deleteItems({
						url: GO.settings.modules.groups.url+'action.php',  
						params: {action: 'delete_user_from_group', delete_users: Ext.encode(this.userGrid.selModel.selections.keys), group_id: this.group_id}, 
						count: this.userGrid.selModel.selections.keys.length,
						callback: function(options, success, response){						
							this.userStore.reload();
						}, 
						scope: this
					});				
				break;
			}
		},
		

		deleteGroup : function()
		{
			GO.deleteItems({
				url: GO.settings.modules.groups.url+'action.php',  
				params: {action: 'delete', groups: Ext.encode(this.selModel.selections.keys)}, 
				count: this.selModel.selections.keys.length,
				callback: function(options, success, response){						
					this.storeAllGroups.reload();
				}, 
				scope: this});
		},
		
		showGroupDialog : function(group)
		{
			if(!this.new_dialog)
			{
				this.userStore = new GO.data.JsonStore({
				    url: GO.settings.modules.groups.url+'json.php',
				    baseParams: {action: 'users_in_group'},
				    root: 'results',
				    id: 'id',
				    fields: ['id', 'user_id', 'name', 'email'],
				    remoteSort: true
				});			
				
				//this.userStore.setDefaultSort('name', 'ASC');
			
	
				var columnModel =  new Ext.grid.ColumnModel([
			        {id:'name',header: GO.lang.strUsername, dataIndex: 'name'},
			        {id:'email',header: GO.lang.strEmail, dataIndex: 'email', width: 150}	        			        
			    ]);	
			    columnModel.defaultSortable = true;				
				
				var tbar = [
			    	{id: 'add', iconCls: 'btn-add', text: GO.lang['cmdAdd'], cls: 'x-btn-text-icon', handler: this.addUserButtonHandler, scope: this},
			    	{id: 'delete', iconCls: 'btn-delete', text: GO.lang['cmdDelete'], cls: 'x-btn-text-icon', handler: this.addUserButtonHandler,  scope: this}
		        ];
		        
		        var bbar = new Ext.PagingToolbar({
		            store: this.userStore,
		            pageSize: 20,
		            displayInfo: true,
		            displayMsg: GO.lang['displayingItems'],
		            emptyMsg: GO.lang['strNoItems']
		        });				
				
				var selectionModel = new Ext.grid.RowSelectionModel({singleSelect: false});
				var autoScroll = true;
				var trackMouseOver = false;
				var plugins;
				var autoExpandColumn = 'name';
				var autoExpandMax = 2500;
				var enableDragDrop = false;
				var collapsible = false;		   
		 			
			    this.userGrid = new Ext.grid.GridPanel({
			    	id: 'groups-grid-overview-users',
			    	ds: this.userStore,
			        cm: columnModel,
			        sm: selectionModel,
			        height: 300,
			    	autoScroll: (autoScroll) ? autoScroll : false,
			    	trackMouseOver: (trackMouseOver) ? trackMouseOver : true,  
			        plugins: (plugins) ? plugins : null,
			        tbar: (tbar) ? tbar : null,
			        bbar: (bbar) ? bbar : null,
			        autoExpandColumn: (autoExpandColumn) ? autoExpandColumn : null,
			        autoExpandMax: (autoExpandMax) ? autoExpandMax : null,		        
			        enableDragDrop: (enableDragDrop) ? enableDragDrop : false,
			        collapsible: (collapsible) ? collapsible : false,
			        border: true
			        
			    });
							
				this.newFormPanel = new Ext.FormPanel({
					id: 'groups-formpanel-new-group',
					labelWidth: 85,
					defaultType: 'textfield',
	    			bodyStyle: 'padding: 5px 5px 5px 5px;',
	    			border: true,
	    			items:[
	    				{
	    					id: 'name', 
	    					fieldLabel: GO.groups.lang.lblNew, 
	    					name: 'name',
	    					width: '88%',
	    					allowBlank: false
	    				}		
	    			]
				});			
				
				var buttons = [
					{id: 'ok', text: GO.lang['cmdOk'], handler: this.showGroupButtonHandler, scope: this },
					{id: 'apply', text: GO.lang['cmdApply'], handler: this.showGroupButtonHandler, scope: this },
					{id: 'close', text: GO.lang['cmdClose'], handler: function(){this.new_dialog.hide();}, scope: this }
				];			
				
				this.new_dialog = new Ext.Window({
					id: 'groups-window-new-group',
					renderTo: document.body,
					layout: 'form',
					modal:false,
					shadow: false,
					autoHeight: true,
					width: 500,
					plain: true,
					closeAction: 'hide',
					title: GO.groups.lang.newGroupName,
					enableTabScroll: true,
					items: [this.newFormPanel,this.userGrid],
					buttons: buttons,
					focus: function(){
			 		    Ext.get('name').focus();
					}
				});
				
				var map = new Ext.KeyMap("groups-window-new-group", {
				    key: 13,
				    fn: this.saveNewGroup,
				    scope: this
				});
			}
			
			this.setGroup(group);
			this.new_dialog.show();
		},
		
		setGroup : function(group)
		{			
			this.group_id = this.userStore.baseParams['group_id']=group.id;
			
			
			if(this.group_id > 0)
			{
				this.userGrid.setDisabled(false);
				this.userStore.load({
					callback: function(){
						this.newFormPanel.form.findField('name').focus();
					},
					scope: this
				});
				if(group && group.name)
				{
					this.newFormPanel.form.findField('name').setValue(group.name);
				}
			}else
			{
				this.userGrid.setDisabled(true);
				this.userStore.loadData({"total":0,"results":[]});
				this.newFormPanel.form.findField('name').setRawValue('');
				
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
					this.newFormPanel.container.unmask();
					
					var group = {id: action.result.group_id};

					this.setGroup(group);

					this.storeAllGroups.reload();

					if (hide)
					{
						this.new_dialog.hide();
					}
					
					
				},
				failure: function(form, action) {					
					this.newFormPanel.container.unmask();
					
					if(action.failureType != 'client')
					{					
						Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);			
					}
				},
				scope: this
			});
		}
});



GO.moduleManager.addAdminModule('groups', GO.groups.MainPanel, {
		title : GO.groups.lang.groups,
		iconCls : 'go-tab-icon-groups',
		closable:true
});
