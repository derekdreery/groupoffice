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


GO.addressbook.SelectContact = function(config){

	if(!config.displayField)
		config.displayField='name';
	
	if(!config.valueField)
		config.valueField='id';


	var fields = {fields: ['id', 'cf', 'name', 'salutation', 'email', 'first_name', 'middle_name','last_name', 'home_phone', 'work_phone', 'cellular', 'company_id','company_name','address','address_no','zip','city','state','country'], columns:[]};
	if(GO.customfields)
	{
		GO.customfields.addColumns("GO_Addressbook_Model_Contact", fields);
	}
	
	config.store = new GO.data.JsonStore({
	    url: GO.url("addressbook/contact/store"),
	    baseParams: {	    	
				addressbook_id : config.addressbook_id,
				noMultiSelectFilter:true
			},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',	    
      fields: fields.fields,
	    remoteSort: true
	});
	
	config.store.setDefaultSort('name', 'asc');

	config.triggerAction='all';
	config.selectOnFocus=true;
	config.pageSize=parseInt(GO.settings['max_rows_list']);

	GO.addressbook.SelectContact.superclass.constructor.call(this,config);
	
}
Ext.extend(GO.addressbook.SelectContact, GO.form.ComboBoxReset);

Ext.ComponentMgr.registerType('selectcontact', GO.addressbook.SelectContact);