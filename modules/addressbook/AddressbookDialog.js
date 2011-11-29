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


GO.addressbook.AddressbookDialog = function(config)
{
	if(!config)
	{
		config={};
	}
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	config.layout= 'fit';
	config.modal= false;
	config.shadow= false;
	config.border= false;
	config.height= 450;
	config.width= 800;
	config.closeAction= 'hide';
	config.title= GO.addressbook.lang.addressbook;
	config.items= this.tabPanel;
	this.buttons=[
	{
		text: GO.addressbook.lang['cmdUpload'],
		handler: function(){
			this.uploadFile();
			
		},
		hidden: true,
		scope: this
	},
	{
		text: GO.addressbook.lang['cmdExport'],
		handler: function(){
			this.exportData();
		},
		hidden: true,
		scope: this
	},
	{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.saveAddressbook(true);
		},
		scope: this
	},
	{
		text: GO.lang['cmdApply'],
		handler: function(){
			this.saveAddressbook();
		},
		scope: this
	},
	{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope: this
	}
	];
			
	GO.addressbook.AddressbookDialog.superclass.constructor.call(this, config);

	
}

Ext.extend(GO.addressbook.AddressbookDialog, GO.Window,{
	

	buildForm : function(id)
	{

		this.propertiesPanel = new Ext.FormPanel({
			waitMsgTarget:true,
			title: GO.addressbook.lang['cmdPanelProperties'],
			labelWidth: 140,
			defaultType: 'textfield',
			border: false,
			bodyStyle:'padding:5px',
			items:[
			{
				fieldLabel: GO.lang['strName'],
				name: 'name',
				allowBlank: false,
				anchor:'100%'
			},
			this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				disabled : !GO.settings.has_admin_permission,
				allowBlank: false,
				anchor:'100%'
			}),
			this.formAddressFormat = new GO.form.SelectAddressFormat({
				fieldLabel: GO.addressbook.lang['defaultAddressFormat'],
				name: 'default_iso_address_format',
				displayField: 'country_name',
				hiddenName:'default_iso_address_format',
				allowBlank:false,
				anchor:'100%',
				value:GO.settings.country
			}), {
				xtype:'panel',
				border:false,
				layout:'column',
				items:[{
					border:false,
					layout:'form',
					columnWidth:.8,
					items:{
						xtype:'textfield',
						fieldLabel: GO.addressbook.lang['defaultSalutation'],
						name: 'default_salutation',
						allowBlank: false,
						anchor:'100%',
						value:GO.addressbook.lang.defaultSalutationExpression
					}
				},{
					columnWidth:.2,
					border:false,
					bodyStyle:'padding-left:5px',
					items:{
						xtype:'button',
						handler:function(){this.propertiesPanel.form.findField('default_salutation').setValue(GO.addressbook.lang.defaultSalutationExpression);},
						scope:this,
						text:GO.lang.cmdReset
					}
				}]
			},
			{
				xtype:'fieldset',
				title:GO.addressbook.lang.explanationVariables,
				border:true,
				layout:'column',
				autoHeight:true,
				items:[{
					border:false,
					columnWidth:.2,
					html:	'['+GO.addressbook.lang.cmdSir+'/'+GO.addressbook.lang.cmdMadam+']<br />'+
							'{title}<br />'+
							'{initials}<br />'+
							'{first_name}<br />'+
							'{middle_name}<br />'+
							'{last_name}'
				},{
					columnWidth:.8,
					border:false,
					html:	GO.addressbook.lang.explanationSex+
							'<br />'+GO.lang.strTitle+
							'<br />'+GO.lang.strInitials+
							'<br />'+GO.lang.strFirstName+
							'<br />'+GO.lang.strMiddleName+
							'<br />'+GO.lang.strLastName
				}]
			}
			]
		});
		
		this.importfileTypeCombo = new Ext.form.ComboBox({
			fieldLabel: GO.addressbook.lang['cmdFormLabelFileType'],
			value: 'csv',
			store: new Ext.data.SimpleStore({
				fields: ['value', 'text'],
				data :
				[
				['csv','CSV (Comma Separated Values)'],
				['vcf','VCF (vCard)']
				]
			}),
			displayField:'text',
			valueField: 'value',
			hiddenName:'import_filetype',
			mode:'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus:true,
			forceSelection: true
		});
		this.importfileTypeCombo.on('select',
			function()
			{
				switch(this.importfileTypeCombo.getValue())
				{
					case 'vcf':
						this.importContactsCompaniesCombo.setDisabled(true);
						this.importValueSeperator.setDisabled(true);
						this.importValueIncluded.setDisabled(true);
						break;
					default:
						this.importContactsCompaniesCombo.setDisabled(false);
						this.importValueSeperator.setDisabled(false);
						this.importValueIncluded.setDisabled(false);								
						break;
				}
			}
			,this
			);
		
		this.importfileInput = new Ext.form.TextField({
			autoCreate: {
				tag: "input",
				type: "file",
				size: "25",
				autocomplete: "off"
			},
			fieldLabel : GO.lang.strSelectFile,
			name: 'import_file',
			inputType: 'file',
			cls: 'x-form-file x-form-field'
		});
		
		this.exportfileTypeCombo = new Ext.form.ComboBox({
			fieldLabel: GO.addressbook.lang['cmdFormLabelFileType'],
			value:'csv',
			store: new Ext.data.SimpleStore({
				fields: ['value', 'text'],
				data : [
				['csv','CSV (Comma Separated Values)'],
				['vcf','VCF (vCard)']
				]
			}),
			displayField:'text',
			valueField: 'value',
			hiddenName:'export_filetype',
			mode:'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus:true,
			forceSelection: true
		});
		this.exportfileTypeCombo.on('select',
			function()
			{
				switch(this.exportfileTypeCombo.getValue())
				{
					case 'vcf':
						this.exportContactsCompaniesCombo.setDisabled(true);
						this.exportValueSeperator.setDisabled(true);
						this.exportValueIncluded.setDisabled(true);
						this.exportLinesEnded.setDisabled(true);
						break;
					default:
						this.exportContactsCompaniesCombo.setDisabled(false);
						this.exportValueSeperator.setDisabled(false);
						this.exportValueIncluded.setDisabled(false);
						this.exportLinesEnded.setDisabled(false);							
						break;
				}
			}
			,this
			);
		
		this.importContactsCompaniesCombo = new Ext.form.ComboBox({
			fieldLabel: GO.addressbook.lang['cmdImport'],
			value:'contacts',
			store: new Ext.data.SimpleStore({
				fields: ['value', 'text'],
				data : [
				['contacts',GO.addressbook.lang.contacts],
				['companies',GO.addressbook.lang.companies]
				]
			}),
			displayField:'text',
			valueField: 'value',
			hiddenName:'import_type',
			mode:'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus:true,
			forceSelection: true
		});						
		
		this.exportContactsCompaniesCombo = new Ext.form.ComboBox({
			fieldLabel: GO.addressbook.lang['cmdExport'],
			value:'contacts',
			store: new Ext.data.SimpleStore({
				fields: ['value', 'text'],
				data : [
				['contacts',GO.addressbook.lang.contacts],
				['companies',GO.addressbook.lang.companies]
				]
			}),
			displayField:'text',
			valueField: 'value',
			hiddenName:'export_type',
			mode:'local',
			triggerAction:'all',
			editable: false,
			selectOnFocus:true,
			forceSelection: true
		});			
		
		this.addressbookImportPanel = new Ext.FormPanel({
			waitMsgTarget:true,
			title: GO.addressbook.lang['cmdImport'],
			labelWidth: 150,
			defaultType: 'textfield',
			fileUpload: true,
			border: false,
			defaults: { 
				anchor:'100%'
			},
			cls:'go-form-panel',
			waitMsgTarget:true,
			items:[
			this.importfileTypeCombo,
			this.importfileInput,
			this.importContactsCompaniesCombo,
			this.importValueSeperator = new Ext.form.TextField(
			{
				fieldLabel: GO.addressbook.lang['cmdFormLabelValueSeperated'],
				name: 'separator',
				anchor: '',
				width: 100,
				value: ',',
				allowBlank: false
			}),
			this.importValueIncluded = new Ext.form.TextField(
			{
				fieldLabel: GO.addressbook.lang['cmdFormLabelValueIncluded'],
				name: 'quote',
				anchor: '',
				width: 100,
				value: '"',
				allowBlank: false
			})
			]
			
		});
		
		this.addressbookExportPanel = new Ext.FormPanel({
			onSubmit: Ext.emptyFn,
			submit: function() {
				this.getEl().dom.submit();
			},				
			title: GO.addressbook.lang.cmdExport,
			labelWidth: 150,
			defaultType: 'textfield',
			border: false,
			defaults: {
				anchor:'100%',
				allowBlank: false
			},
			cls:'go-form-panel',
			waitMsgTarget:true,
			items:[
			this.exportfileTypeCombo,
			this.exportContactsCompaniesCombo,
			this.exportValueSeperator = new Ext.form.TextField(
			{
				fieldLabel: GO.addressbook.lang['cmdFormLabelValueSeperated'],
				name: 'separator',
				anchor: '',
				width: 100,
				value: ',',
				allowBlank: false
			}),
			this.exportValueIncluded = new Ext.form.TextField(
			{
				fieldLabel: GO.addressbook.lang['cmdFormLabelValueIncluded'],
				name: 'quote',
				anchor: '',
				width: 100,
				value: '"',
				allowBlank: false
			}),
			this.exportLinesEnded = new Ext.form.TextField(
			{
				fieldLabel: GO.addressbook.lang['cmdFormLabelLinesEnded'],
				name: 'crlf',
				anchor: '',
				width: 100,
				value: '\\r\\n',
				allowBlank: false
			})
			]
		});
		
		this.tabPanel = new Ext.TabPanel({
			activeTab: 0,
			deferredRender:false,
			border:false,
			items:[
			this.propertiesPanel,
			this.addressbookImportPanel,
			this.addressbookExportPanel,
			this.readPermissionsTab = new GO.grid.PermissionsPanel()
			]
		});
		
		
		this.propertiesPanel.on('show', this.syncButtons,	this);			
		this.addressbookImportPanel.on('show', this.syncButtons, this);			
		this.addressbookExportPanel.on('show', this.syncButtons, this);				
		this.readPermissionsTab.on('show', this.syncButtons, this);			
	
	},
	
	syncButtons : function(){
		
		this.buttons[0].setVisible(this.addressbookImportPanel.isVisible());
		this.buttons[1].setVisible(this.addressbookExportPanel.isVisible());
		this.buttons[2].setVisible(this.propertiesPanel.isVisible());
		this.buttons[3].setVisible(this.propertiesPanel.isVisible());
		
	},
	
	show : function(record)
	{		
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		if(record)
		{
			this.record=record;
			this.addressbook_id=record.data.id;
		}else
		{
			this.addressbook_id=0;
		}
		
		this.tabPanel.setActiveTab(0);
		
		if (this.addressbook_id == 0)
		{	
			this.propertiesPanel.form.reset();
			this.addressbookImportPanel.setDisabled(true);
			this.addressbookExportPanel.setDisabled(true);
			this.readPermissionsTab.setAcl(0);
	
		} else {			
			//this.propertiesPanel.form.findField('name').setValue(this.record.data.name);
			//this.selectUser.setValue(this.record.data.user_id);
			this.propertiesPanel.form.setValues(this.record.data);
			this.selectUser.setRemoteText(this.record.data.owner);
			
			this.addressbookImportPanel.setDisabled(false);
			this.addressbookExportPanel.setDisabled(false);
			
			this.readPermissionsTab.setAcl(this.record.data.acl_id);
		}
		
		
		GO.addressbook.AddressbookDialog.superclass.show.call(this);
		
		this.propertiesPanel.form.clearInvalid();
		this.syncButtons();
	},
	
	
	importDataSelectionWindow: function()
	{
		switch(this.addressbookImportPanel.form.items.items[2].getValue())
		{
			case 'contacts':	
				var type="2";
				this.defaultCSVField = {
					'title':  GO.lang['strTitle'],
					'first_name': GO.lang['strFirstName'],
					'middle_name': GO.lang['strMiddleName'],
					'last_name': GO.lang['strLastName'],
					'initials': GO.lang['strInitials'],
					'sex': GO.lang['strSex'],
					'birthday': GO.lang['strBirthday'],
					'address': GO.lang['strAddress'],
					'address_no': GO.lang['strAddressNo'],
					'zip': GO.lang['strZip'],
					'city': GO.lang['strCity'],
					'state': GO.lang['strState'],
					'country': GO.lang['strCountry'],
					'email': GO.lang['strEmail'],
					'email2': GO.lang['strEmail'] + ' 2',
					'email3': GO.lang['strEmail'] + ' 3',
					'home_phone': GO.lang['strPhone'],
					'fax': GO.lang['strFax'],
					'work_phone': GO.lang['strWorkPhone'],
					'work_fax': GO.lang['strWorkFax'],
					'cellular': GO.lang['strCellular'],
					'company_name': GO.lang['strCompany'],
					'company_name2': GO.lang['strCompany']+' 2',
					'department': GO.lang['strDepartment'],
					'function': GO.lang['strFunction'],
					'salutation': GO.lang['strSalutation'],
					'comment': GO.lang['strComment']
				};
				
				break;
			case 'companies':
				var type="3";
				this.defaultCSVField = {
					'name':  GO.lang['strName'], 
					'name2':  GO.lang['strName']+' 2', 
					'email':  GO.lang['strEmail'],
					'phone': GO.lang['strPhone'],
					'fax': GO.lang['strFax'],
					'country': GO.lang['strCountry'],
					'state': GO.lang['strState'],
					'city': GO.lang['strCity'],
					'zip': GO.lang['strZip'],
					'address': GO.lang['strAddress'],
					'address_no': GO.lang['strAddressNo'],
					'post_country': GO.lang['strPostCountry'],
					'post_state': GO.lang['strPostState'],
					'post_city': GO.lang['strPostCity'],
					'post_zip': GO.lang['strPostZip'],
					'post_address': GO.lang['strPostAddress'],
					'post_address_no': GO.lang['strPostAddressNo'],
					'homepage': GO.lang['strHomepage'],
					'bank_no': GO.addressbook.lang['cmdFormLabelBankNo'],
					'vat_no': GO.addressbook.lang['cmdFormLabelVatNo']
				};					
				break;
		}
		
		if(GO.customfields && GO.customfields.types[type] && GO.customfields.types[type].panels)
		{
			for(var p=0;p<GO.customfields.types[type].panels.length;p++)
			{
				var fields = GO.customfields.types[type].panels[p].customfields;

				for(var f=0;f<fields.length;f++)
				{
					if(fields[f].datatype!='heading' && fields[f].datatype!='function')
					{
						this.defaultCSVField[fields[f].dataname]=fields[f].name;
					}
				}
			}
		}


		/*this.defaultCSVField_keys = Array();
		for (var key in this.defaultCSVFieldContacts)
		{
			this.defaultCSVField_keys.push(key);
		}*/
		
		this.addressbookImportData = new Ext.form.FormPanel({
			waitMsgTarget:true,
			
			//id: 'addressbook-default-import-data-window',
			labelWidth: 125,
			border: false,
			defaults: { 
				anchor:'-20'
			},
			cls: 'go-form-panel',
			autoHeight:true
		});

		this.addressbookImportData.form.timeout=300;
		
		this.csvFieldStore = new Ext.data.JsonStore({
			fields: ['id', 'name'],
			root: 'list_keys',
			id: 'id'
		});
          
		this.csvFieldStore.on('load',
			function()
			{
				var combos = this.addressbookImportData.items.items;

				for(var i = 0; i < combos.length; i++)
				{
					var setDefault = true;
					for(var j = 0; j < combos[i].store.data.items.length; j++)
					{
						var csvField = combos[i].store.data.items[j].data.name;
						var fieldLabel = combos[i].fieldLabel;
						if(csvField == fieldLabel)
						{
							combos[i].setValue(combos[i].store.data.items[j].data.id);
							setDefault = false;
						}
					}
    			
					if (setDefault)
					{
						combos[i].setValue(combos[i].store.data.items[0].data.id);
					}
				}
			}
			, this);
		
		for(var key in this.defaultCSVField)
		{
			var combo =  new Ext.form.ComboBox({
				fieldLabel: this.defaultCSVField[key],
				id:  'export_combo_'+key,
				store: this.csvFieldStore,
				displayField:'name',
				valueField:	'id',
				hiddenName: key,
				mode: 'local',
				triggerAction: 'all',
				editable:false
			});
			
			this.addressbookImportData.add(combo);
		}
		
		var buttons = [
		{
			text: GO.addressbook.lang['cmdImport'],
			handler: this.importData,
			scope: this
		},

		{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.csvFieldDialog.close();
			},
			scope: this
		}
		];
		
		this.csvFieldDialog = new Ext.Window({
			autoScroll:true,
			height: 400,
			width: 400,
			modal:true,
			title: GO.addressbook.lang.matchFields,
			items: [
			this.addressbookImportData
			],
			buttons: buttons			
		});
		
		this.csvFieldDialog.show();			
	},		
	
	saveAddressbook : function(hide)
	{
		this.propertiesPanel.form.submit({
			waitMsg:GO.lang.waitMsgSave,
			url:GO.settings.modules.addressbook.url+ 'action.php',
			params:
			{
				task : 'save_addressbook',
				addressbook_id : this.addressbook_id
			},
			success:function(form, action){
				
				GO.addressbook.writableAddressbooksStore.reload();
				
				if(action.result.addressbook_id)
				{
					this.addressbook_id = action.result.addressbook_id;
					
					this.addressbookImportPanel.setDisabled(false);
					this.addressbookExportPanel.setDisabled(false);
					this.readPermissionsTab.setAcl(action.result.acl_id);
				}
				
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
	
	uploadFile : function()
	{
		this.addressbookImportPanel.form.submit({
			waitMsg: GO.lang.waitMsgUpload,
			url:GO.settings.modules.addressbook.url+ 'action.php',
			params:
			{
				task : 'upload',
				addressbook_id : this.addressbook_id
			},
			success:function(form, action){
				if(this.addressbookImportPanel.form.items.items[0].getValue() == 'csv')
				{
					this.importDataSelectionWindow();
					this.csvFieldStore.loadData(action.result);
				}else
				{
					//this.csvFieldDialog.close();
					
					Ext.MessageBox.alert(GO.lang.strSuccess, GO.addressbook.lang.importSuccess);
					this.refreshAb();
				}
			},
			failure: function(form, action) {
				
				if(action.failureType != 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
	},

	refreshAb : function(){
		var ab = GO.mainLayout.getModulePanel('addressbook');
		if(ab.tabPanel.getActiveTab().id=='ab-contacts-grid')
			ab.contactsGrid.store.reload();
		else
			ab.companiesGrid.store.reload();

	},

	importData : function()
	{
		this.addressbookImportData.form.submit({
			url:GO.settings.modules.addressbook.url+ 'action.php',
			waitMsg:GO.lang['waitMsgSave'],
			params:
			{
				task : 'import',
				addressbook_id : this.addressbook_id,
				separator: this.addressbookImportPanel.form.findField('separator').getValue(),
				quote: this.addressbookImportPanel.form.findField('quote').getValue(),
				import_type: this.addressbookImportPanel.form.items.items[2].getValue(),
				import_filetype: this.addressbookImportPanel.form.items.items[0].getValue()
			},
			success:function(form, action){
				if(this.csvFieldDialog)
					this.csvFieldDialog.close();
					
				Ext.MessageBox.alert(GO.lang.strSuccess, GO.addressbook.lang.importSuccess);
				this.refreshAb();
			},
			failure: function(form, action) {					
				if(action.failureType != 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);			
				}
			},
			scope: this
		});			
	},
	
	exportData : function()
	{
		this.addressbookExportPanel.form.el.set({
			action:GO.settings.modules.addressbook.url+'export.php?addressbook_id='+this.addressbook_id
			});
		this.addressbookExportPanel.form.submit();		
	}					
});	
