GO.addressbook.ContactProfilePanel = function(config)
{
	Ext.apply(config);
	
	
	
	this.separator = ':';
	this.widthLeftColumn = 256;
	this.widthRightColumn = 253;
	this.widthNameField = 100;
	this.widthExtraField = 50;
	this.widthInputSeparator = 3;
	
	this.formFirstName = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strName'], 
		name: 'first_name', 
		id: 'first_name',
		allowBlank: false,
		width: this.widthLeftColumn - ( this.widthNameField + this.widthExtraField + (2*this.widthInputSeparator) )
	});
	
	this.formMiddleName = new Ext.form.TextField(
	{
		fieldLabel: '', 
		name: 'middle_name',
		labelSeparator: null,
		hideLabel: true,
		width: this.widthExtraField,
		style: 'margin-left: ' + this.widthInputSeparator + 'px;'
	});
	
	this.formLastName = new Ext.form.TextField(
	{
		fieldLabel: '', 
		name: 'last_name',
		labelSeparator: null,
		hideLabel: true,
		width: this.widthLeftColumn - ( this.widthNameField + this.widthExtraField + (2*this.widthInputSeparator) ),
		allowBlank: false,
		style: 'margin-left: ' + this.widthInputSeparator + 'px;'
	});
	
	this.formTitle = new Ext.form.TextField(
	{
		fieldLabel: GO.addressbook.lang['cmdFormLabelTitleInitials'], 
		name: 'title',
		labelSeparator: this.separator,
		width: 126
	});
	
	this.formInitials = new Ext.form.TextField(
	{
		fieldLabel: '', 
		name: 'initials',
		labelSeparator: null,
		hideLabel: true,
		width: 127,
		style: 'margin-left: ' + this.widthInputSeparator + 'px;'
	});
	
	this.formSexMale = new Ext.form.Radio({
		name: 'sex',
		fieldLabel: GO.lang['strSex'],
		labelSeparator: this.separator,
		boxLabel: GO.lang['strMale'],
		inputValue: 'M',
		dataIndex: 'sexM',
		width: this.widthLeftColumn	
	});
	
	this.formSexFemale = new Ext.form.Radio({
		name: 'sex',
		boxLabel: GO.lang['strFemale'],
		labelSeparator: null,
		hideLabel: true,
		inputValue: 'F',
		dataIndex: 'sexF',
		width: 100
	});
				
	this.formSalutation = new Ext.form.TextField(
	{
		fieldLabel: GO.addressbook.lang['cmdFormLabelSalutation'], 
		name: 'salutation',
		id: 'salutation',
		labelSeparator: this.separator,
		width: this.widthLeftColumn				
	});
	
	this.formBirthday = new Ext.form.DateField({
		fieldLabel: GO.lang['strBirthday'],
		name: 'birthday',
		labelSeparator: this.separator,
		format: GO.settings['date_format'],
		width: this.widthLeftColumn
	});						
	
	this.formEmail = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strEmail'], 
		name: 'email',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
	
	this.formEmail2 = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strEmail'] + ' 2', 
		name: 'email2',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
	
	this.formEmail3 = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strEmail'] + ' 3',
		name: 'email3',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});												
	
	this.formHomePhone = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strPhone'], 
		name: 'home_phone',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});	
	
	this.formFax = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strFax'], 
		name: 'fax',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});	
	
	this.formCellular = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strCellular'], 
		name: 'cellular',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});	
	
														
	
	this.formAddress = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddress'], 
		name: 'address',
		labelSeparator: this.separator,
		width: this.widthLeftColumn - (this.widthExtraField + this.widthInputSeparator )
	});
	
	this.formAddressNo = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddressNo'], 
		name: 'address_no',
		labelSeparator: null,
		hideLabel: true,
		width: this.widthExtraField,
		style: 'margin-left: ' + this.widthInputSeparator + 'px;'
	});				
	
	this.formPostal = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strZip'], 
		name: 'zip',
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});				

	this.formCity = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strCity'], 
		name: 'city',
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});				

	this.formState = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strState'], 
		name: 'state',
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	})
	
	this.formCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		name: 'country_text',
		hiddenName: 'country',
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});
	

	this.formCompany = new GO.addressbook.SelectCompany({
		fieldLabel: GO.lang['strCompany'], 
		name: 'company',
		hiddenName: 'company_id',
		labelSeparator: this.separator,
		width: this.widthRightColumn,
		emptyText: GO.addressbook.lang['cmdFormCompanyEmptyText'],
		addressbook_id: this.addressbook_id			
	});			
	

	
	
	this.formDepartment = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strDepartment'], 
		name: 'department',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});				

	this.formFunction = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strFunction'], 
		name: 'function',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});				

	this.formWorkPhone = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strWorkPhone'], 
		name: 'work_phone',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});				

	this.formWorkFax = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strWorkFax'], 
		name: 'work_fax',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});

	
	this.formAddressBooks = new GO.form.ComboBox({
		fieldLabel: GO.addressbook.lang['cmdFormLabelAddressBooks'],
		store: GO.addressbook.writableAddressbooksStore,
    displayField:'name',
    valueField: 'id',
    hiddenName:'addressbook_id',
    mode:'local',
    triggerAction:'all',
    editable: false,
		selectOnFocus:true,
    forceSelection: true,
    allowBlank: false,
    width: this.widthLeftColumn			
	});
	
	this.formAddressBooks.on('beforeselect', function(combo, record) 	
	{
		if(this.formCompany.getValue()>0)
		{
			if(confirm(GO.addressbook.lang.moveAll))
			{
				this.setAddressbookID(record.data.id);
				return true;
			}else
			{
				return false;
			}
		}
		
		
		
	}, this);
	
	/*this.formAddressBooks.on('load', function()
	{
		this.formAddressBooks.isLoaded = true;
		this.formAddressBooks.setValue(task.result.data.addressbook_id);
	}, 
	this);*/		
	

	this.formMiddleName.on('blur', this.setSalutation, this);
	this.formLastName.on('blur', this.setSalutation, this);
	this.formSexMale.on('check', this.setSalutation, this);
	this.formSexFemale.on('check', this.setSalutation, this);


	 
	this.addressbookFieldset = 
	{
  		xtype: 'fieldset',
  		title: GO.addressbook.lang['cmdFieldsetSelectAddressbook'],
  		autoHeight: true,
  		collapsed: false,
    	defaults: { border: false },
			items: [{
	    		defaults: { border: false, layout: 'table' },
				items: [{
					defaults: { border: false },
					items: [{
						layout: 'form', 
						items: this.formAddressBooks
					}]
				}]
			}]
	}
	
	this.personalFieldset = 
	{
		xtype: 'fieldset',
		title: GO.addressbook.lang['cmdFieldsetPersonalDetails'],
		autoHeight: true,
		collapsed: false,
  	defaults: { border: false, layout: 'table' },
		items: [{
			defaults: { border: false, layout: 'form' },
			items: [{
				items: [this.formFirstName]
			},{
				items: [this.formMiddleName]							
			},{
				items: [this.formLastName]
			}]
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formTitle]
			},{
				layout: 'form',
				items: [this.formInitials]							
			}]	
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formSexMale]
			},{
				layout: 'form',
				items: [this.formSexFemale]							
			}]				
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formSalutation]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formBirthday]
			}]					
		}]					
	}
	
	this.addressFieldset =
	{
		xtype: 'fieldset',
		title: GO.addressbook.lang['cmdFieldsetAddress'],
		autoHeight: true,
		collapsed: false,
  	defaults: { border: false, layout: 'table' },
		items: [{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formAddress]
			},{
				layout: 'form',
				items: [this.formAddressNo]							
			}]							
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formPostal]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formCity]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formState]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formCountry]
			}]
		}]						
	}
	
	this.contactFieldset = 
	{
  		xtype: 'fieldset',
  		title: GO.addressbook.lang['cmdFieldsetContact'],
  		autoHeight: true,
  		collapsed: false,
    	defaults: { border: false, layout: 'table' },
		items: [{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formEmail]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formEmail2]
			}]
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formEmail3]
			}]
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formHomePhone]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formFax]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formCellular]
			}]					
		},{
			defaults: { border: false },
			layoutConfig: { colums: 1},
			items: [{
				layout: 'form',
				items: [this.formWorkPhone]
			}]					
		},{
			defaults: { border: false },
			layoutConfig: { colums: 1},
			items: [{
				layout: 'form',
				items: [this.formWorkFax]
			}]
		}]					
	}
	this.workFieldset = 
	{
		xtype: 'fieldset',
		title: GO.addressbook.lang['cmdFieldsetWork'], 
		autoHeight: true,
		collapsed: false,
  	defaults: { border: false, layout: 'table' },
		items: [{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formCompany]
			}]					
		},{
			defaults: { border: false },
			items: [{
				layout: 'form',
				items: [this.formDepartment]
			}]					
		},{
		defaults: { border: false },
		items: [{
			layout: 'form',
						items: [this.formFunction]
					}]					
				}] 					
			}
 
		
	
	this.title= GO.addressbook.lang['cmdPanelContact'];
	//this.cls='go-form-panel';
	this.bodyStyle='padding:5px';
  this.layout= 'column';
  this.defaults= {border: false};
  this.items= [
    	{	 
    		columnWidth: .5,
    		autoScroll: true,
			items: [
				this.addressbookFieldset,
				this.personalFieldset,
				this.addressFieldset
			]				
    	},{
    		columnWidth: .5,
	    	style: 'margin-left: 5px;',
				items: [
					this.contactFieldset,
					this.workFieldset						
				]		
    	}
    ];	
	
	GO.addressbook.ContactProfilePanel.superclass.constructor.call(this);
}

Ext.extend(GO.addressbook.ContactProfilePanel, Ext.Panel,{
	setSalutation : function()
	{			
		var middleName = this.formMiddleName.getValue();	
		var lastName = this.formLastName.getValue();	
		var sexMale= this.formSexMale.getValue();	
		var sexFemale= this.formSexFemale.getValue();

		var empty = ' ';			
		var salutation = GO.addressbook.lang['cmdSalutation'];
		
		if (sexMale != '')
		{
			salutation += empty + GO.addressbook.lang['cmdSir'];
		} else if (sexFemale != '')
		{			
			salutation += empty + GO.addressbook.lang['cmdMadam'];			
		} else
		{
			salutation += empty + GO.addressbook.lang['cmdSir']+'/'+GO.addressbook.lang['cmdMadam'];
		}


		if (middleName != '')
		{
			salutation += empty + middleName;
		}
		
		if (lastName != '')
		{
			salutation += empty + lastName;
		}		
		
		this.formSalutation.setRawValue(salutation);
	},
	setAddressbookID : function(addressbook_id)
	{
		this.formAddressBooks.setValue(addressbook_id);		
		this.formCompany.store.baseParams['addressbook_id'] = addressbook_id;
	}
});