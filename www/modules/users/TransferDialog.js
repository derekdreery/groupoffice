GO.users.TransferDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	initComponent : function(){


		Ext.apply(this, {
			loadOnNewModel: false,
			goDialogId:'usertransferdialog',
			title: 'Transfer Userdata',
			formControllerUrl: 'users/user',
			createAction : 'transfer',
			layout: 'fit',
			height:230,
			//enableOkButton: GO.fixdossiers.isManager,
			enableApplyButton: false,
			width:400,
			jsonPost: true
		});
		
		GO.users.TransferDialog.superclass.initComponent.call(this);	
	},
	
	show : function () {
		this.userStore.load();
		GO.users.TransferDialog.superclass.show.call(this);
	},
	  
	buildForm : function () {
		this.transferPanel = this.buildTransferPanel();
		this.addPanel(this.transferPanel);
	},
	
	buildTransferPanel : function() {
		this.userStore = new GO.data.JsonStore({
			url: GO.url('users/user/store'),
			id: 'id',
			totalProperty: 'total',
			root: 'results',
			fields: ['id', 'username', 'name'],
			remoteSort: true
		});
		
		return new Ext.Panel({
			title:'Select users',			
			cls:'go-form-panel',
			layout:'form',
			width: '100%',
			items:[
				{
					xtype:'displayfield',
					html: 'Select 2 user accounts to transfer data from one account to the other'
				},
				{
					xtype: 'combo',
					editable:false,
					hiddenName: 'transfer.id_from',
					fieldLabel: 'From',
					mode: 'remote',
					triggerAction: 'all',
					store: this.userStore,
					displayField:'name',
					valueField: 'id'
				},{
					xtype: 'combo',
					editable:false,
					hiddenName: 'transfer.id_to',
					fieldLabel: 'To',
					mode: 'remote',
					triggerAction: 'all',
					store: this.userStore,
					displayField:'name',
					valueField: 'id'
				}
			]				
		});
	}
	
});