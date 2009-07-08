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
	
	Ext.apply(this, config);
		
	this.displayField='name';

	
	this.store = new GO.data.JsonStore({
	    url: GO.settings.modules.addressbook.url+ 'json.php',
	    baseParams: {
	    	task: 'contacts',
				'addressbook_id' : this.addressbook_id
				},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['id', 'name', 'salutation', 'email'],
	    remoteSort: true
	});
	
	this.store.setDefaultSort('name', 'asc');

	GO.addressbook.SelectContact.superclass.constructor.call(this,{
		valueField: 'id',
		triggerAction: 'all',
		selectOnFocus:true,
		pageSize: parseInt(GO.settings['max_rows_list'])
	});
	
}
Ext.extend(GO.addressbook.SelectContact, GO.form.ComboBoxReset);
