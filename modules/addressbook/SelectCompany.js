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
		
	this.displayField='name_and_name2';

	if (typeof(config.fields)=='undefined') {
		config.fields = {
			columns:[],
			fields:['id', 'name_and_name2', 'post_address', 'post_address_no', 'post_zip', 'post_city', 'post_state', 'post_country', 'vat_no', 'email']
		};
	}

	if(!this.fieldLabel){
		this.fieldLabel=GO.addressbook.lang.company;
	}

	if(GO.customfields)
	{
		GO.customfields.addColumns(3, config.fields);
	}

	if (typeof(config.store)=='undefined') {
		this.store = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+ 'json.php',
			baseParams: {
				task: 'companies',
				'addressbook_id' : this.addressbook_id,
				no_addressbooks_filter:'1'
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
		valueField: 'id',
		triggerAction: 'all',
		selectOnFocus:true,
		pageSize: parseInt(GO.settings['max_rows_list'])
	});
	
}
Ext.extend(GO.addressbook.SelectCompany, GO.form.ComboBoxReset);
