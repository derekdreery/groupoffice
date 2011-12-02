GO.addressbook.AddressbookDialog = Ext.extend(GO.dialog.TabbedFormDialog, {

	initComponent : function(){
		
		Ext.apply(this, {
			title:GO.addressbook.lang.addressbook,
			formControllerUrl: 'addressbook/addressbook',
			width:700
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
		
		this.addPanel( this.importPanel = new GO.base.model.ImportPanel({
			filetypes:[
				['csv','CSV (Comma Separated Values)'],
				['vcf','VCF (vCard)']
			],
			controllers:[
				['GO_Addressbook_Controller_Contact','Contacten'],
				['GO_Addressbook_Controller_Company','Bedrijven']
			],
			importBaseParams:[
				{'addressbook_id':this.remoteModelId}
			]
		}));
		
		this.addPermissionsPanel(new GO.grid.PermissionsPanel());
		
		if(GO.customfields){
			this.disableContactsCategoriesPanel = new GO.customfields.DisableCategoriesPanel({
				title:'Contacts custom fields'
			});
			this.addPanel(this.disableContactsCategoriesPanel);
			
			this.disableCompaniesCategoriesPanel = new GO.customfields.DisableCategoriesPanel({
				title:'Company custom fields'
			});
			this.addPanel(this.disableCompaniesCategoriesPanel);
		}
	}
});
