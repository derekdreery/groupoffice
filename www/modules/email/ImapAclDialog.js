GO.email.ImapAclDialog = Ext.extend(GO.Window, {

	initComponent : function(){

		this.grid = new GO.grid.GridPanel({
			store:new GO.data.JsonStore({
				url: GO.settings.modules.email.url+'json.php',
				baseParams: {
					"task": 'getacl',
					mailbox:"",
					account_id:0
				},
				root: 'results',
				id: 'identifier',
				fields:['identifier','permissions']
			}),
			cm: new Ext.grid.ColumnModel({
				defaults:{
					sortable:false
				},
				columns:[
				{
					header:GO.lang.strUser,
					dataIndex: 'identifier'
				},{
					header: GO.lang.strPermissions,
					dataIndex: 'permissions'
				}]
			}),
			view: new Ext.grid.GridView({
				autoFill: true,
				forceFit: true,
				emptyText: GO.lang['strNoItems']
			}),
			loadMask:true,
			tbar: [{
				iconCls: 'btn-add',
				text: GO.lang.cmdAdd,
				cls: 'x-btn-text-icon',
				handler: function(){

				},
				scope: this
			},{
				iconCls: 'btn-delete',
				text: GO.lang.cmdDelete,
				cls: 'x-btn-text-icon',
				handler: function(){
					this.grid.deleteSelected();
				},
				scope:this
			}]
		});

		Ext.apply(this, {
			width:500,
			height:400,
			title:GO.email.lang.shareFolder,
			layout:'fit',
			items:[this.grid]
		});
		GO.email.ImapAclDialog.superclass.initComponent.call(this);
	},

	setParams : function(account_id, mailbox){
		this.grid.store.baseParams.account_id=account_id;
		this.grid.store.baseParams.mailbox=mailbox;
		this.grid.store.load();
	},

	userDialog : function(identifier){

		if(!this.userDialog){
			this.userDialog = new GO.email.ImapAclUserDialog();
		}

	}

});