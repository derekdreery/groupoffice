GO.addressbook.AddressbookDialog = Ext.extend(GO.dialog.TabbedFormDialog, {

	initComponent : function(){
		
		Ext.apply(this, {
			titleField:'name',
			title:GO.addressbook.lang.addressbook,
			formControllerUrl: 'addressbook/addressbook',
			width:700,
			height:440
			//fileUpload:true
		});
		
		GO.addressbook.AddressbookDialog.superclass.initComponent.call(this);	
	},
	beforeSubmit : function(params) {		
		this.formPanel.baseParams.importBaseParams = Ext.encode({'addressbook_id':this.remoteModelId});
		
		GO.addressbook.AddressbookDialog.superclass.beforeSubmit.call(this);	
	},
	buildForm : function(){
		
		this.propertiesPanel = new Ext.Panel({
			title:GO.addressbook.lang.cmdPanelProperties,
			layout: 'form',
			labelWidth: 140,
			defaultType: 'textfield',
			border: false,
			bodyStyle:'padding:5px',
			defaults: {anchor:'100%'},
			//cls:'go-form-panel',
			items:[
			{
				fieldLabel: GO.lang['strName'],
				name: 'name',
				allowBlank: false
			},
			this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				disabled : !GO.settings.has_admin_permission,
				allowBlank: false
			}),{
				xtype:'panel',
				border:false,
				layout:'column',
				items:[{
					border:false,
					layout:'form',
					columnWidth:.9,
					items:{
						xtype:'textfield',
						fieldLabel: GO.addressbook.lang['defaultSalutation'],
						name: 'default_salutation',
						allowBlank: false,
						anchor:'99%',
						value:GO.addressbook.lang.defaultSalutationExpression
					}
				},{
					columnWidth:.1,
					border:false,
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
		
		this.addPanel(this.propertiesPanel);
		
		this.importDialogs = {};
		
		this.addPanel(this.importPanel = new Ext.Panel({
			title:GO.lang.cmdImport,
			layout: 'form',
			items: [],
			defaults: {anchor:'100%'},
			border: false,
			labelWidth: 150,
			toolbars: [],
			cls:'go-form-panel',
			items: [
				this.fileTypeCB = new GO.form.ComboBox({
					hiddenName: 'fileType',
					fieldLabel: GO.addressbook.lang.cmdFormLabelFileType,
					store: new Ext.data.ArrayStore({
						storeId: 'fileTypeStore',
						idIndex: 0,
						fields:['value','label'],
						data: [
							['CSV','CSV (Comma Separated Values)'],
							['VCard','VCF (vCard)']
						]
					}),
					valueField:'value',
					displayField:'label',
					mode:'local',
					allowBlank: false,
					triggerAction: 'all',
					value: 'CSV'
				}), this.controllerNameCB = new GO.form.ComboBox({
					hiddenName: 'controller',
					fieldLabel: GO.lang.cmdImport,
					store: new Ext.data.ArrayStore({
						storeId: 'controllersStore',
						idIndex: 0,
						fields:['value','label'],
						data: [
							['GO_Addressbook_Controller_Contact',GO.addressbook.lang.contacts],
							['GO_Addressbook_Controller_Company',GO.addressbook.lang.companies]
						]
					}),
					valueField:'value',
					displayField:'label',
					mode:'local',
					allowBlank: false,
					triggerAction: 'all',
					value: 'GO_Addressbook_Controller_Contact'
				}),new Ext.Panel({
					layout: 'form',
					border: false,
					items: [
						new Ext.Button({
							text: GO.lang.cmdContinue,
							width: '20%',
							handler: function(){
								var controllerName = this.controllerNameCB.getValue();		
								var fileType = this.fileTypeCB.getValue();
								if (!GO.util.empty(controllerName) && !GO.util.empty(fileType)) {
									if ( !this.importDialogs[fileType] )
										this.importDialogs[fileType] = {};
									if ( !this.importDialogs[fileType][controllerName] ) {
											this.importDialogs[fileType][controllerName] = new GO.base.model.ImportDialog({
												importBaseParams : { addressbook_id : this.remoteModelId },
												controllerName : controllerName,
												fileType: fileType,
												excludedAttributes : ['ctime','mtime','user_id', 'contact_name','link_id','files_folder_id']
											});
										}
									this.importDialogs[fileType][controllerName].show(this.remoteModelId);
								}
							},
							scope: this
						})
					]
				})
			]
		}));
		
		this.fileTypeCB.on('select',function(combo,record,index){
			if (record.id=='VCard')
				this.controllerNameCB.setValue('GO_Addressbook_Controller_Company');
			this.controllerNameCB.setVisible(record.id=='CSV');
		},this);
		
//		this.addPanel( this.importPanel = new GO.base.model.ImportPanel({
//			filetypes:[
//				['csv','CSV (Comma Separated Values)'],
//				['vcf','VCF (vCard)']
//			],
//			controllers:[
//				['GO_Addressbook_Controller_Contact',GO.addressbook.lang.contacts],
//				['GO_Addressbook_Controller_Company',GO.addressbook.lang.companies]
//			],
//			importBaseParams:[
//				{'addressbook_id':this.remoteModelId}
//			]
//		}));
		
		this.addPermissionsPanel(new GO.grid.PermissionsPanel());
		
		if(GO.customfields){
			this.disableContactsCategoriesPanel = new GO.customfields.DisableCategoriesPanel({
				title:GO.addressbook.lang.contactCustomFields
			});
			this.addPanel(this.disableContactsCategoriesPanel);
			
			this.disableCompaniesCategoriesPanel = new GO.customfields.DisableCategoriesPanel({
				title:GO.addressbook.lang.companyCustomFields
			});
			this.addPanel(this.disableCompaniesCategoriesPanel);
		}
	},
	
	afterLoad : function(remoteModelId, config, action){
		if(GO.customfields){
			this.disableContactsCategoriesPanel.setModel(remoteModelId, "GO_Addressbook_Model_Contact");
			this.disableCompaniesCategoriesPanel.setModel(remoteModelId, "GO_Addressbook_Model_Company");
		}
	}
});
