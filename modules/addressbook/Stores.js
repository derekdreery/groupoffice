/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.addressbook.readableAddressbooksStore = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+ 'json.php'	,
			baseParams: {
				'task':'addressbooks',
				'auth_type' : 'read',
				limit:parseInt(GO.settings['max_rows_list'])

				},
			root: 'results', 
			totalProperty: 'total', 
			id: 'id',
			fields: ['id','name','owner','checked'],
			remoteSort: true
		});			
		
GO.addressbook.writableAddressbooksStore = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+ 'json.php'	,
			baseParams: {
				'task':'addressbooks',
				'auth_type' : 'write',
				limit:parseInt(GO.settings['max_rows_list'])
				},
			root: 'results', 
			totalProperty: 'total', 
			id: 'id',
			fields: ['id','name','owner', 'acl_id','user_id','default_iso_address_format','default_salutation'],
			remoteSort: true
		});


		
GO.addressbook.writableAddressbooksStore.on('load', function(){	
	GO.addressbook.writableAddressbooksStore.on('load', function(){
		GO.addressbook.readableAddressbooksStore.load();
	}, this);
}, this, {single:true});

