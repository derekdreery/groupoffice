/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.addressbook.ContactDialog = function(config)
{
	config = config || {};

	this.goDialogId = 'contact';
	

	this.personalPanel = new GO.addressbook.ContactProfilePanel();

	GO.addressbook.ContactPhoto = Ext.extend(Ext.BoxComponent, {
		autoEl : {
				tag: 'img',
				src:Ext.BLANK_IMAGE_URL
			},
	
		setPhotoSrc : function(url)
		{
			var now = new Date();
			if (this.el)
				this.el.set({
					src: GO.util.empty(url) ? Ext.BLANK_IMAGE_URL : url+'&mtime='+now.format('U')
				});
			this.setVisible(true);
		}
	});

	this.contactPhoto = new GO.addressbook.ContactPhoto();

	this.deleteImageCB = new Ext.form.Checkbox({
		boxLabel: GO.addressbook.lang.deleteImage,
		labelSeparator: '',
		name: 'delete_image',
		allowBlank: true,
		hideLabel:true,
		disabled:true
	});

	this.uploadFile = new GO.form.UploadFile({
		inputName : 'image',
		max: 1
	})

	this.photoPanel = new Ext.Panel({
		title : GO.addressbook.lang.photo,
		layout: 'form',
		border:false,
		cls : 'go-form-panel',		
		autoScroll:true,
		items:[	this.uploadFile,
		this.contactPhoto
		,this.deleteImageCB
		]
	});

	this.commentPanel = new Ext.Panel({
		title: GO.addressbook.lang['cmdPanelComments'], 
		layout: 'fit',
		forceLayout:true,
		border:false,
		items: [ new Ext.form.TextArea({
			name: 'comment',
			fieldLabel: '',
			hideLabel: true,
			anchor:'100% 100%'
		})
		]
	});
	
	this.personalPanel.on('show', 
		function()
		{ 
			var firstName = Ext.get('first_name');					
			if (firstName)
			{
				firstName.focus();
			}
		}, this);
		
	this.commentPanel.on('show', function(){ 
		this.formPanel.form.findField('comment').focus();
	}, this);
	
	//var selectMailingsPanel = new GO.addressbook.SelectMailingsPanel();
	
	var items = [
	this.personalPanel,
	this.photoPanel,
	this.commentPanel];
	      
	items.push(new GO.addressbook.SelectAddresslistsPanel());
	
  
	if(GO.customfields && GO.customfields.types["GO_Addressbook_Model_Contact"])
	{
		for(var i=0;i<GO.customfields.types["GO_Addressbook_Model_Contact"].panels.length;i++)
		{
			items.push(GO.customfields.types["GO_Addressbook_Model_Contact"].panels[i]);
		}
	}

	this.formPanel = new Ext.FormPanel({
		waitMsgTarget:true,
		baseParams: {},
		border: false,
		fileUpload : true,
		items: [
		this.tabPanel = new Ext.TabPanel({
			border: false,
			activeTab: 0,
			hideLabel: true,
			deferredRender: false,
			enableTabScroll:true,
			anchor:'100% 100%',
			items: items
		})
		]
	});
	
	
	if(GO.settings.modules.users.read_permission){
		config.tbar=[{
				handler:function(){
					GO.users.showUserDialog();
					if(this.go_user_id){
						GO.users.userDialog.formPanel.load({
							url:GO.url('addressbook/contact/load'),
							params: {
								id : this.contact_id 
							},
							failure:function(form, action)
							{
								Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
							},
							scope: this				
						});
					}else
					{
						
					}
					//GO.users.userDialog.contactPanel.fillPersonalFields();
				},
				scope:this,
				text:GO.addressbook.lang.createUser
			}];
	}
	
	
	//this.downloadDocumentButton = new Ext.Button();

	this.collapsible=true;
	this.layout= 'fit';
	this.modal=false;
	this.shadow= false;
	this.border= false;
	this.height= 600;
	
	//autoHeight= true;
	this.width= 820;
	this.plain= true;
	this.closeAction= 'hide';
	//this.iconCls= 'btn-addressbook-contact';
	this.title= GO.addressbook.lang['cmdContactDialog'];
	this.items= this.formPanel;
	this.buttons= [
	{
		text: GO.lang['cmdOk'],
		handler:function(){
			this.saveContact(true);
		},
		scope: this
	},
	/*{
		text: GO.lang['cmdApply'],
		handler: function(){
			this.saveContact();
		},
		scope: this
	},*/
	{
		text: GO.lang['cmdClose'],
		handler: function()
		{
			this.hide();
		},
		scope: this
	}
	];
	
	var focusFirstField = function(){
		this.formPanel.form.findField('first_name').focus(true);
	};
	
	this.focus= focusFirstField.createDelegate(this);
	
	
	this.personalPanel.formAddressBooks.on({
					scope:this,
					change:function(sc, newValue, oldValue){
						var record = sc.store.getById(newValue);
						GO.customfields.disableTabs(this.tabPanel, record.data,'contactCustomfields');	
					}
				});
	
	
	GO.addressbook.ContactDialog.superclass.constructor.call(this, config);
	
	this.addEvents({
		'save':true
	});

//	if (GO.customfields) {
//		this.personalPanel.formAddressBooks.on('select',function(combo,record,index){
//			var allowed_cf_categories = record.data.allowed_cf_categories.split(',');
//			this.updateCfTabs(allowed_cf_categories);
//		},this);
//	}
}

Ext.extend(GO.addressbook.ContactDialog, GO.Window, {

	show : function(contact_id, config)
	{
		
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		if(!GO.addressbook.writableAddresslistsStore.loaded)
		{
			GO.addressbook.writableAddresslistsStore.load({
				callback:function(){
					//var values = GO.util.empty(contact_id) ? this.formPanel.form.getValues() : {};
					this.show(contact_id, config);
//					delete values.addressbook_id;
//					delete values.iso_address_format;
//					delete values.salutation;
//					this.formPanel.form.setValues(values);
				},
				scope:this
			});
		}else if(!GO.addressbook.writableAddressbooksStore.loaded)
		{
			GO.addressbook.writableAddressbooksStore.load(
			{
				callback:function(){
					//var values = GO.util.empty(contact_id) ? this.formPanel.form.getValues() : {};
					this.show(contact_id, config);
//					delete values.addressbook_id;
//					delete values.iso_address_format;
//					delete values.salutation;
//					this.formPanel.form.setValues(values);
				},
				scope:this
			});
		}else
		{
			var tempAddressbookID = this.personalPanel.formAddressBooks.getValue();
			this.formPanel.form.reset();

			this.personalPanel.formAddressBooks.setValue(tempAddressbookID);
	
			if(contact_id)
			{
				this.contact_id = contact_id;
			} else {
				this.contact_id = 0;
			}

			if(GO.addressbook.defaultAddressbook){

				var store = this.personalPanel.formAddressBooks.store;
				//add record to store if not loaded
				var r = store.getById(GO.addressbook.defaultAddressbook.id);
				if(!r)
				{					
					store.add(GO.addressbook.defaultAddressbook);
				}

				this.personalPanel.setAddressbookID(GO.addressbook.defaultAddressbook.id);
			}else if(tempAddressbookID>0 && this.personalPanel.formAddressBooks.store.getById(tempAddressbookID))
			{
				this.personalPanel.setAddressbookID(tempAddressbookID);
			}else
			{
				this.personalPanel.formAddressBooks.selectFirst();
				this.personalPanel.setAddressbookID(this.personalPanel.formAddressBooks.getValue());
			}
						
//			if(this.contact_id > 0)
//			{
				this.loadContact(this.contact_id, config);
//			} else {
//				this.setPhoto(0);
//				GO.addressbook.ContactDialog.superclass.show.call(this);
//			}
			//var abRecord = this.personalPanel.formAddressBooks.store.getById(this.personalPanel.formAddressBooks.getValue());
			this.tabPanel.setActiveTab(0);
		}

	},


	/*setAddressbookId : function(addressbook_id)
	{
		this.personalPanel.formAddressBooks.setValue(addressbook_id);
		this.personalPanel.formCompany.store.baseParams['addressbook_id'] = addressbook_id;			
		this.addressbook_id = addressbook_id;
	},*/
	
	loadContact : function(id, config)
	{
		this.formPanel.form.load({
			url:GO.url('addressbook/contact/load'),
			params:{
				id:id,
				addressbook_id:this.formPanel.form.findField('addressbook_id').getValue()
			},
			success: function(form, action) {
				
//				if(!action.result.data.write_permission)
//				{
//					Ext.Msg.alert(GO.lang['strError'], GO.lang['strNoWritePermissions']);						
//				}else
//				{		

					if(config && config.values)
						this.formPanel.form.setValues(config.values);

					this.personalPanel.setAddressbookID(action.result.data.addressbook_id);
					this.formPanel.form.findField('addressbook_id').setRemoteText(action.result.remoteComboTexts.addressbook_id);
					this.formPanel.form.findField('company_id').setRemoteText(action.result.remoteComboTexts.company_id);
					if(!GO.util.empty(action.result.data.photo_url))
						this.setPhoto(action.result.data.photo_url);

					if(GO.customfields)
						GO.customfields.disableTabs(this.tabPanel, action.result);	
					
					GO.addressbook.ContactDialog.superclass.show.call(this);
				//}
			},
			scope: this
		});
	},
	
	saveContact : function(hide)
	{		
		var company = this.personalPanel.formCompany.getRawValue();

		this.formPanel.form.submit({
			waitMsg:GO.lang['waitMsgSave'],
			url:GO.url('addressbook/contact/submit'),			
			params:
			{				
				id : this.contact_id,
				company: company
			},
			success:function(form, action){
				if(action.result.id)
				{
					this.contact_id = action.result.id;
				}
				this.uploadFile.clearQueue();
				this.fireEvent('save', this, this.contact_id);
				
				GO.dialog.TabbedFormDialog.prototype.refreshActiveDisplayPanels.call(this);
				
				//this.personalPanel.setContactID(this.contact_id);
				if(!GO.util.empty(action.result.photo_url))
					this.setPhoto(action.result.photo_url);
				else
					this.setPhoto("");
				if (hide)
				{
					this.hide();
				}
			},
			failure: function(form, action) {				
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
	},

	setPhoto : function(url)
	{
		this.contactPhoto.setPhotoSrc(url);
		this.deleteImageCB.setValue(false);
		this.deleteImageCB.setDisabled(url=='');
	}
});
