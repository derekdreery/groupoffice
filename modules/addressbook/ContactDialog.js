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
	Ext.apply(this, config);

	this.goDialogId = 'contact';
	

	this.personalPanel = new GO.addressbook.ContactProfilePanel();

	GO.addressbook.ContactPhoto = Ext.extend(Ext.BoxComponent, {
		autoEl : {
				tag: 'img',
				src:Ext.BLANK_IMAGE_URL
			},
	
		setPhotoSrc : function(contact_id)
		{
			var now = new Date();
			if (this.el)
				this.el.set({
					src: contact_id==0 ? Ext.BLANK_IMAGE_URL : GO.settings.modules.addressbook.url+'photo.php?contact_id='+contact_id+'&mtime='+now.format('U')
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
	      
	if(GO.mailings)
	{	
		items.push(new GO.mailings.SelectMailingsPanel());
	}
	
  
	if(GO.customfields && GO.customfields.types["2"])
	{
		for(var i=0;i<GO.customfields.types["2"].panels.length;i++)
		{
			items.push(GO.customfields.types["2"].panels[i]);
		}
	}

	this.formPanel = new Ext.FormPanel({
		waitMsgTarget:true,
		url: GO.settings.modules.addressbook.url+ 'json.php',
		baseParams: {},
		border: false,
		fileUpload : true,
		items: [
		this.tabPanel = new Ext.TabPanel({
			border: false,
			activeTab: 0,
			hideLabel: true,
			deferredRender: false,
			anchor:'100% 100%',
			items: items
		})
		]
	});
	
	
	//this.downloadDocumentButton = new Ext.Button();

	this.collapsible=true;
	this.layout= 'fit';
	this.modal=false;
	this.shadow= false;
	this.border= false;
	this.height= 570;
	
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
	
	
	GO.addressbook.ContactDialog.superclass.constructor.call(this);
	
	this.addEvents({
		'save':true
	});
}

Ext.extend(GO.addressbook.ContactDialog, GO.Window, {

	show : function(contact_id)
	{
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		if(GO.mailings && !GO.mailings.writableMailingsStore.loaded)
		{
			GO.mailings.writableMailingsStore.load({
				callback:function(){
					var values = GO.util.empty(contact_id) ? this.formPanel.form.getValues() : {};
					this.show(contact_id);
					delete values.addressbook_id;
					delete values.iso_address_format;
					delete values.salutation;
					this.formPanel.form.setValues(values);
				},
				scope:this
			});
		}else if(!GO.addressbook.writableAddressbooksStore.loaded)
		{
			GO.addressbook.writableAddressbooksStore.load(
			{
				callback:function(){
					var values = GO.util.empty(contact_id) ? this.formPanel.form.getValues() : {};
					this.show(contact_id);
					delete values.addressbook_id;
					delete values.iso_address_format;
					delete values.salutation;
					this.formPanel.form.setValues(values);
				},
				scope:this
			});
		}else
		{
			var tempAddressbookID = this.personalPanel.formAddressBooks.getValue();
			this.formPanel.form.reset();

			this.personalPanel.formAddressBooks.setValue(tempAddressbookID);
			//this.personalPanel.formAddressFormat.selectDefault();

			if(contact_id)
			{
				this.contact_id = contact_id;
			} else {
				this.contact_id = 0;
			}

			if(!GO.util.empty(GO.addressbook.defaultAddressbook) && this.personalPanel.formAddressBooks.store.getById(GO.addressbook.defaultAddressbook)){
				this.personalPanel.setAddressbookID(GO.addressbook.defaultAddressbook);
			}else if(tempAddressbookID>0 && this.personalPanel.formAddressBooks.store.getById(tempAddressbookID))
			{
				this.personalPanel.setAddressbookID(tempAddressbookID);
			}else
			{
				this.personalPanel.formAddressBooks.selectFirst();
				this.personalPanel.setAddressbookID(this.personalPanel.formAddressBooks.getValue());
			}
						
			if(this.contact_id > 0)
			{
				this.loadContact(contact_id);
			} else {
				this.setPhoto(0);
				GO.addressbook.ContactDialog.superclass.show.call(this);
			}
			var abRecord = this.personalPanel.formAddressBooks.store.getById(this.personalPanel.formAddressBooks.getValue());
			this.personalPanel.formAddressFormat.setValue(abRecord.get('default_iso_address_format'));
			//var sal = this.personalPanel.setSalutation();
			//this.personalPanel.formSalutation.setValue(sal);
			this.tabPanel.setActiveTab(0);			
		}

	},

	/*setAddressbookId : function(addressbook_id)
	{
		this.personalPanel.formAddressBooks.setValue(addressbook_id);
		this.personalPanel.formCompany.store.baseParams['addressbook_id'] = addressbook_id;			
		this.addressbook_id = addressbook_id;
	},*/
	
	loadContact : function(id)
	{
		this.formPanel.form.load({
			url: GO.settings.modules.addressbook.url+ 'json.php', 
			params: {
				contact_id: id,
				task: 'load_contact'
			},
			success: function(form, action) {
				
				if(!action.result.data.write_permission)
				{
					Ext.Msg.alert(GO.lang['strError'], GO.lang['strNoWritePermissions']);						
				}else
				{					
					this.personalPanel.setAddressbookID(action.result.data.addressbook_id);
					this.formPanel.form.findField('company_id').setRemoteText(action.result.data.company_name);
					if(!GO.util.empty(action.result.data.photo_src))
						this.setPhoto(id);
					
					GO.addressbook.ContactDialog.superclass.show.call(this);
				}
			},
			scope: this
		});
	},
	
	saveContact : function(hide)
	{		
		var company = this.personalPanel.formCompany.getRawValue();

		this.formPanel.form.submit({
			url:GO.settings.modules.addressbook.url+ 'action.php',
			waitMsg:GO.lang['waitMsgSave'],
			params:
			{
				task : 'save_contact',
				contact_id : this.contact_id,
				company: company,
				delete_photo : this.deleteImageCB.getValue()
			},
			success:function(form, action){
				if(action.result.contact_id)
				{
					this.contact_id = action.result.contact_id;
				}
				this.uploadFile.clearQueue();
				this.fireEvent('save', this, this.contact_id);
				if(!GO.util.empty(action.result.image))
					this.setPhoto(this.contact_id);
				else
					this.setPhoto(0);
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

	setPhoto : function(contact_id)
	{
		this.contactPhoto.setPhotoSrc(contact_id);
		this.deleteImageCB.setValue(false);
		this.deleteImageCB.setDisabled(contact_id=='');
	}
});
