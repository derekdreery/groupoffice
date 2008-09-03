GO.addressbook.CompanyProfilePanel = function(config)
{
	Ext.apply(config);
	
	
	this.separator = ':';
	this.widthLeftColumn = 250;
	this.widthRightColumn = 250;
	this.widthAddressNo = 50;
	this.widthInputSeparator = 3;
	
	
	
	
	/*
	 * 
	 * 		VISITORS ADDRESS
	 * 
	 */
	 
	this.formAddress = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddress'], 
		name: 'address', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: (this.widthRightColumn - (this.widthAddressNo + this.widthInputSeparator))
	});
					
	this.formAddressNo = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddressNo'], 
		name: 'address_no',
		labelSeparator: null,
		hideLabel: true,
		width: this.widthAddressNo,
		style: 'margin-left: ' + this.widthInputSeparator + 'px;'
	});
					
	this.formZip = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strZip'], 
		name: 'zip', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
					
	this.formCity = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strCity'], 
		name: 'city', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
					
	this.formState = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strState'], 
		name: 'state', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
	
	/*
	this.formCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		id: 'countryComboCompany',
		hiddenName: 'country',
		width: this.widthRightColumn
	});
	*/
	
	this.formCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		name: 'country_text',
		hiddenName: 'country',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});				
	
	/*
	 * 
	 * 		POST ADDRESS
	 * 
	 */
	 
	this.formPostAddress = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddress'], 
		name: 'post_address', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: (this.widthRightColumn - (this.widthAddressNo + this.widthInputSeparator))
	});
					
	this.formPostAddressNo = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddressNo'], 
		name: 'post_address_no', 
		labelSeparator: null,
		hideLabel: true,
		width: this.widthAddressNo,
		style: 'margin-left: ' + this.widthInputSeparator + 'px;'
	});
					
	this.formPostZip = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strZip'], 
		name: 'post_zip', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
					
	this.formPostCity = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strCity'], 
		name: 'post_city', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
					
	this.formPostState = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strState'], 
		name: 'post_state', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});
	
	/*
	this.formPostCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		id: 'postCountryComboCompany',
		hiddenName: 'post_country',
		width: this.widthRightColumn
	});
	*/
	
	this.formPostCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		name:'post_country_text',
		hiddenName: 'post_country',
		labelSeparator: this.separator,
		width: this.widthRightColumn
	});														
	
	/*
	 * 
	 * 		NAME,EMAIL,HOMEPAGE,ETC
	 * 
	 */								
					 
	this.formName = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strName'], 
		name: 'name',
		id: 'companyName',
		allowBlank: false,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});
		
	this.formPhone = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strPhone'], 
		name: 'phone', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});

	this.formFax = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strFax'], 
		name: 'fax', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});
	
	this.formEmail = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strEmail'], 
		name: 'email', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});				
		
	this.formHomepage = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strHomepage'],
		name: 'homepage', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});	
	
	this.formBankNo = new Ext.form.TextField(
	{
		fieldLabel: GO.addressbook.lang['cmdFormLabelBankNo'],
		name: 'bank_no', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});	
	
	this.formVatNo = new Ext.form.TextField(
	{
		fieldLabel: GO.addressbook.lang['cmdFormLabelVatNo'],
		name: 'vat_no', 
		allowBlank: true,
		labelSeparator: this.separator,
		width: this.widthLeftColumn
	});
	
	/*
	 * 
	 * 		ADDRESSBOOK
	 * 
	 */					
	
	this.formAddressBooks = new GO.form.ComboBox({
		fieldLabel: GO.addressbook.lang['cmdFormLabelAddressBooks'],
		id: 'addressbookCombo_2',
		store: GO.addressbook.writableAddressbooksStore,
    displayField:'name',
    valueField: 'id',
    hiddenName:'addressbook_id',
    mode:'local',
    triggerAction:'all',
    editable: false,
		selectOnFocus:true,
    forceSelection: true,
    width: this.widthRightColumn			
	});
	this.formAddressBooks.on('beforeselect', function(combo, record) 	
	{
		if(this.company_id>0)
		{
			return confirm(GO.addressbook.lang.moveAll);
		}
	}, this);	

	
	this.title=GO.addressbook.lang['cmdPanelCompany'];
				
	this.bodyStyle='padding: 5px 5px 5px 5px'; 
	this.layout='column';
	this.defaults={border: false};
	this.items=[
		{	 
			columnWidth: .5,
	  	defaults: { border: false },
			items: [{
	  		xtype: 'fieldset',
	  		title: GO.addressbook.lang['cmdFieldsetSelectAddressbook'],
	  		autoHeight: true,
	  		border: true,
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
			},{			    		
	  		xtype: 'fieldset',
	  		title: GO.addressbook.lang['cmdFieldsetCompanyDetails'],
	  		autoHeight: true,
	  		collapsed: false,
	  		border: true,
	    	defaults: { border: false, layout: 'table' },
				items: [{
					defaults: { border: false },
					items: [{
						layout: 'form', 
						items: [this.formName]
					}]
				},{
					defaults: { border: false },
					items: [{
						layout: 'form', 
						items: [this.formPhone]
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
						items: [this.formEmail]
					}]
				},{
					defaults: { border: false },
					items: [{
						layout: 'form', 
						items: [this.formHomepage]
					}]
				},{
					defaults: { border: false },
					items: [{
						layout: 'form', 
						items: [this.formBankNo]
					}]
				},{
					defaults: { border: false },
					items: [{
						layout: 'form', 
						items: [this.formVatNo]
					}]
				}]
			}]
	  	},{
	  		columnWidth: .5,
	    	defaults: { border: false },
	    	style: 'margin-left: 5px;',
				items: [{			    		
	    		xtype: 'fieldset',
	    		title: GO.addressbook.lang['cmdFieldsetVisitAddress'],
	    		autoHeight: true,
	    		collapsed: false,
	    		border: true,
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
							items: [this.formZip]
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
				},{			    		
	    		xtype: 'fieldset',
	    		title: GO.addressbook.lang['cmdFieldsetPostAddress'], 
	    		autoHeight: true,
	    		collapsed: false,
	    		border: true,
		    	defaults: { border: false, layout: 'table' },
					items: [{
						defaults: { border: false },
						items: [{
							layout: 'form', 
							items: [this.formPostAddress]
						},{
							layout: 'form', 
							items: [this.formPostAddressNo]									
						}]
					},{
						defaults: { border: false },
						items: [{
							layout: 'form', 
							items: [this.formPostZip]
						}]
					},{
						defaults: { border: false },
						items: [{
							layout: 'form', 
							items: [this.formPostCity]
						}]
					},{
						defaults: { border: false },
						items: [{
							layout: 'form', 
							items: [this.formPostState]
						}]
					},{
						defaults: { border: false },
						items: [{
							layout: 'form', 
							items: [this.formPostCountry]
						}]
					}]
			}]	    		
		}];


	GO.addressbook.CompanyProfilePanel.superclass.constructor.call(this);
}

Ext.extend(GO.addressbook.CompanyProfilePanel, Ext.Panel,{
	setCompanyId : function(company_id)
	{
		this.company_id=company_id;
	}
});