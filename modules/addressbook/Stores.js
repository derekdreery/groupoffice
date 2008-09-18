/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: Stores.js 2656 2008-07-22 13:47:01Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.addressbook.readableAddressbooksStore = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+ 'json.php'	,
			baseParams: {
				'task':'addressbooks',
				'auth_type' : 'read'
				},
			root: 'results', 
			totalProperty: 'total', 
			id: 'id',
			fields: ['id','name','owner'],
			remoteSort: true
		});			
		
GO.addressbook.writableAddressbooksStore = new GO.data.JsonStore({
			url: GO.settings.modules.addressbook.url+ 'json.php'	,
			baseParams: {
				'task':'addressbooks',
				'auth_type' : 'write'
				},
			root: 'results', 
			totalProperty: 'total', 
			id: 'id',
			fields: ['id','name','owner', 'acl_read', 'acl_write'],
			remoteSort: true
		});


		
GO.addressbook.writableAddressbooksStore.on('load', function(){	
	GO.addressbook.writableAddressbooksStore.on('load', function(){
		GO.addressbook.readableAddressbooksStore.load();
	});
});

