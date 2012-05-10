/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
  * @author WilmarVB <wilmar@intermesh.nl>
 */

GO.servermanager.ManageDialog = Ext.extend(GO.dialog.TabbedFormDialog,{

	enableOkButton : false,
	enableApplyButton : false,

	initComponent : function() {
		Ext.apply(this, {
			title: GO.lang.administration,
			formControllerUrl: 'servermanager/automaticEmail', // change this if new panels are added
			width:700,
			height:440
			//fileUpload:true
		});
		GO.servermanager.ManageDialog.superclass.initComponent.call(this);
	},

	buildForm : function() {
		this.autoEmailGrid = new GO.grid.GridPanel({
			title: GO.servermanager.lang.autoEmail,
			layout: 'fit',
			store: new GO.data.JsonStore({
				url: GO.url("servermanager/automaticEmail/store"),
				root: 'results',
				id: 'id',
				totalProperty:'total',
				fields: ['id','name','days','active'],
				remoteSort: true
			}),
			paging: true,
			cm: new Ext.grid.ColumnModel({
				defaults:{
					sortable:true
				},
				columns:[{
					dataIndex: 'id',
					hidden: true
				}, {
					header: GO.lang.strName, 
					dataIndex: 'name',
					sortable: true
				}, {
					header: GO.servermanager.lang.days, 
					dataIndex: 'days',
					sortable: true
				}, {
					header: GO.servermanager.lang.enabled,
					dataIndex: 'active',
					sortable: true,
					width: 20
				}]
			}),
			view: new Ext.grid.GridView({
				autoFill: true,
				forceFit: true,
				emptyText: GO.lang['strNoItems']
			}),
			sm : new Ext.grid.RowSelectionModel(),
			loadMask : true,
			tbar : [{
				iconCls: 'btn-add',
				text: GO.lang['cmdAdd'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.showAutoEmailDialog(0);
				},
				scope: this
			},{
				iconCls: 'btn-delete',
				text: GO.lang['cmdDelete'],
				cls: 'x-btn-text-icon',
				handler: function(){
					this.autoEmailGrid.deleteSelected();
				},
				scope: this
			}]
		});
		
		this.autoEmailGrid.on('rowdblclick', function(grid,rowIndex,e){
			this.showAutoEmailDialog(grid.store.getAt(rowIndex).data.id);
		},this);
		
		this.addPanel(this.autoEmailGrid);
		
	},

	afterLoad : function(remoteModelId, config, action) {
		this.autoEmailGrid.store.load();
		GO.servermanager.ManageDialog.superclass.afterLoad(this,remoteModelId,config,action);
	},

	showAutoEmailDialog : function(remoteModelId) {
		if (!this.autoEmailDialog) {
			this.autoEmailDialog = new GO.servermanager.AutoEmailDialog();
			this.autoEmailDialog.on('save',function(){
				this.autoEmailGrid.store.load();
			},this);
		}
		this.autoEmailDialog.show(remoteModelId);
	}

});	
