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
 		
GO.email.UnknownRecipientsDialog = Ext.extend(Ext.Window, {
	
	initComponent : function(){
		
		this.store = new GO.data.JsonStore({
			root: 'recipients',
			fields:['email','name', 'first_name', 'middle_name', 'last_name']
		});


		var action = new Ext.ux.grid.RowActions({
			header:'',
			hideMode:'display',
			keepSelection:true,
			actions:[{
				iconCls:'btn-add',
				qtip:GO.lang.cmdAdd
			},{
				iconCls:'btn-edit',
				qtip:GO.lang.cmdEdit
			}],
			width: 50
		});

		action.on({
			action:function(grid, record, action, row, col) {

				var email = record.data.email;
				var tldi = email.lastIndexOf('.');
				if(tldi)
				{
					var tld = email.substring(tldi+1, email.length).toUpperCase();
					if(GO.lang.countries[tld])
					{
						record.data.country=tld;
					}
				}
	
				if(action == 'btn-add')
				{
					this.addContactToAddresslistAtSaveContactEvent = true;
					GO.addressbook.showContactDialog(0, {
						values: record.data,
						addresslistIds: new Array(this.addresslistId),
						first_name: record.data.first_name,
						middle_name: record.data.middle_name,
						last_name: record.data.last_name
					});
					//GO.addressbook.contactDialog.formPanel.form.setValues(record.data);
				}else
				{
					if(!GO.email.findContactDialog)
					{
						GO.email.findContactDialog = new GO.email.FindContactDialog();
						
						if (!GO.util.empty(this.addresslistId)) {
								GO.email.findContactDialog.on('email_merged', function(contactId) {
								if (this.addresslistId>0) {
									this.addToAddressbook(contactId,this.addresslistId);
								} else {
									alert(GO.addressbook.lang.addresslistIdMustBePositive);
								}
								}, this);
						}
					}
		     
					GO.email.findContactDialog.show(record.data);
				}

				var store = grid.getStore();
				store.remove(record);

				if(store.getCount()==0)
				{
					this.hide();
				}
			},
			scope:this
		});
				
		this.grid = new GO.grid.GridPanel({
			store: this.store,
			plugins:action,
			border:false,
			region:'center',
			loadMask:true,
			columns : [{
				header : GO.lang.strName,
				dataIndex : 'name'
			}, {
				header : GO.lang.strEmail,
				dataIndex : 'email'
			},
			action],
			sm : new Ext.grid.RowSelectionModel({
				singleSelect : false
			}),
			view : new Ext.grid.GridView({
				forceFit : true,
				autoFill : true
			})
		});

		var items = [
			this.descriptionTextPanel = new Ext.Panel({
				border: false,
				region:'north',
				html: this.descriptionText  ? this.descriptionText : GO.email.lang.addUnknownRecipientsText,
				cls:'go-form-panel'
			}),
			this.grid
		];
		
		if (GO.util.empty(this.disableSkipUnknownCheckbox)) {
			items.push(new Ext.Panel({
				border: false,
				items: this.skipUnknownRecipients = new Ext.form.Checkbox({
					boxLabel:GO.email.lang.skipUnknownRecipientsAction,
					hideLabel:true,
					checked:false,
					name:'skip_unknown_recipients',
					listeners : {
						check : function(field, checked)
						{
							GO.email.skipUnknownRecipients = checked;

							GO.request({
								url: "core/saveSetting",
								params: {
									value: checked ? 1 : 0,
									name:"email_skip_unknown_recipients",
									user_id:GO.settings.user_id
								},				
								scope: this
							});
						},
						scope : this
					}
				}),
				region:'south',
				autoHeight:true,
				cls:'go-form-panel'
			}))
		}
		
		this.title= !GO.util.empty(this.title) ? this.title : GO.email.lang.addUnknownRecipients;
		this.layout='fit';
		this.modal=false;
		this.height=400;
		this.width=600;
		this.closable=true;
		this.closeAction='hide';
		this.items= new Ext.Panel({
			autoScroll:true,
			layout:'border',
			items: items
		});	
		
		GO.email.UnknownRecipientsDialog.superclass.initComponent.call(this);
		
	},
	
	addToAddressbook : function(contactId,addresslistId) {
		Ext.Ajax.request({
			url: GO.url('addressbook/addresslist/addContactsToAddresslist'),
			params: {
				contactIds: Ext.encode(new Array(contactId)),
				addresslistId : addresslistId
			}
//			,
//			callback: function(options, success, response)
//			{
//
//			}
		});
	}
	
});
