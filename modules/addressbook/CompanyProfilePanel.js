GO.addressbook.CompanyProfilePanel = function(config)
{
	Ext.apply(config);
	
	
	this.formAddress = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddress'], 
		name: 'address'
	});
					
	this.formAddressNo = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddressNo'], 
		name: 'address_no'		
	});
					
	this.formZip = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strZip'], 
		name: 'zip'
	});
					
	this.formCity = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strCity'], 
		name: 'city'
	});
					
	this.formState = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strState'], 
		name: 'state'
	});
	
	
	this.formCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		name: 'country_text',
		hiddenName: 'country'
	});				
	
	/*
	 * 
	 * 		POST ADDRESS
	 * 
	 */
	 
	this.formPostAddress = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddress'], 
		name: 'post_address'
	});
					
	this.formPostAddressNo = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strAddressNo'], 
		name: 'post_address_no'
	});
					
	this.formPostZip = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strZip'], 
		name: 'post_zip'
	});
					
	this.formPostCity = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strCity'], 
		name: 'post_city'
	});
					
	this.formPostState = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strState'], 
		name: 'post_state'
	});
	
	this.formPostCountry = new GO.form.SelectCountry({
		fieldLabel: GO.lang['strCountry'],
		name:'post_country_text',
		hiddenName: 'post_country'
	});														
	
					 
	this.formName = new Ext.form.TextField(
	{
		id:'companyName',
		fieldLabel: GO.lang['strName'], 
		name: 'name'
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
		name: 'fax'
	});
	
	this.formEmail = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strEmail'], 
		name: 'email'
	});				
		
	this.formHomepage = new Ext.form.TextField(
	{
		fieldLabel: GO.lang['strHomepage'],
		name: 'homepage'
	});	
	
	this.formBankNo = new Ext.form.TextField(
	{
		fieldLabel: GO.addressbook.lang['cmdFormLabelBankNo'],
		name: 'bank_no'
	});	
	
	this.formVatNo = new Ext.form.TextField(
	{
		fieldLabel: GO.addressbook.lang['cmdFormLabelVatNo'],
		name: 'vat_no'
	});
	
	/*
	 * 
	 * 		ADDRESSBOOK
	 * 
	 */					
	
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
    forceSelection: true    			
	});
	this.formAddressBooks.on('beforeselect', function(combo, record) 	
	{
		if(this.company_id>0)
		{
			return confirm(GO.addressbook.lang.moveAll);
		}
	}, this);	

	
	this.title=GO.addressbook.lang['cmdPanelCompany'];
				
	this.labelWidth=120;
	this.bodyStyle='padding: 5px'; 
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
	    	defaults: { anchor:'100%' },
				items:this.formAddressBooks
						
			},{			    		
	  		xtype: 'fieldset',
	  		title: GO.addressbook.lang['cmdFieldsetCompanyDetails'],
	  		autoHeight: true,
	  		collapsed: false,
	  		border: true,
	    	defaults: { border: false, anchor: '100%' },
				items: [this.formName,this.formPhone,this.formFax,this.formEmail,this.formHomepage,this.formBankNo,this.formVatNo]
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
	    	defaults: { border: false, anchor: '100%' },
				items: [this.formAddress,this.formAddressNo,this.formZip,this.formCity,this.formState,this.formCountry]
			},{			    		
    		xtype: 'fieldset',
    		title: GO.addressbook.lang['cmdFieldsetPostAddress'], 
    		autoHeight: true,
    		collapsed: false,
    		border: true,
	    	defaults: { border: false, anchor:'100%' },
				items: [this.formPostAddress,this.formPostAddressNo,this.formPostZip,this.formPostCity,this.formPostState,this.formPostCountry]  		
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