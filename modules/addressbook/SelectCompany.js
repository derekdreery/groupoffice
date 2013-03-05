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


GO.addressbook.SelectCompany = function(config){
	
	Ext.apply(this, config);
	
	if(!this.valueField)
		this.valueField='id';
		
	this.displayField='name_and_name2';

	if (typeof(config.fields)=='undefined') {
		config.fields = {
			columns:[],
			fields:['id', 'name_and_name2', 'post_address', 'post_address_no', 'post_zip', 'post_city', 'post_state', 'post_country', 'vat_no', 'crn','email','invoice_email','cf']
		};
	}

	if(!this.fieldLabel){
		this.fieldLabel=GO.addressbook.lang.company;
	}

	if(GO.customfields)
	{
		GO.customfields.addColumns("GO_Addressbook_Model_Company", config.fields);
	}

	if (typeof(config.store)=='undefined') {
		this.store = new GO.data.JsonStore({
			url: GO.url("addressbook/company/store"),
			baseParams: {				
				addressbook_id : this.addressbook_id,		
				noMultiSelectFilter:true
			},
			root: 'results',
			id: 'id',
			totalProperty:'total',
			fields: config.fields.fields,
			remoteSort: true
		});
	}
	
	

	this.store.setDefaultSort('name', 'asc');

	GO.addressbook.SelectCompany.superclass.constructor.call(this,{		
		triggerAction: 'all',
		selectOnFocus:true,
		pageSize: parseInt(GO.settings['max_rows_list'])
	});
	
}
Ext.extend(GO.addressbook.SelectCompany, GO.form.ComboBoxReset);

Ext.ComponentMgr.registerType('selectcompany', GO.addressbook.SelectCompany);