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
 
 /**
  * param user_name: the text, the initial text to show
  * param user_id: the initial user_id 
  */
 
 GO.form.SelectUser = function(config){	
	Ext.apply(this, config);
	
	this.store = new GO.data.JsonStore({
		url: GO.settings.modules.users.url+'non_admin_json.php',
		baseParams: {'task':'users'},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name','email','username'],
		remoteSort: true
	});
	this.store.setDefaultSort('name', 'asc');
	
	
	this.setRemoteValue(GO.settings.user_id, GO.settings.name);
	
	GO.form.SelectUser.superclass.constructor.call(this,{
		displayField: 'name',
		hiddenName:'user_id',
		value: GO.settings.user_id,
		valueField: 'id',
		triggerAction: 'all',
		selectOnFocus:true,
		forceSelection: true,
		pageSize: parseInt(GO.settings['max_rows_list'])
	});
}

Ext.extend(GO.form.SelectUser, GO.form.ComboBox,{
	setRemoteValue : function(user_id, name)
	{
		var UserRecord = Ext.data.Record.create([
	    {name: 'id'},
	    {name: 'name'}
    ]);
	  var loggedInUserRecord = new UserRecord({
	  		id: user_id,
	  		name: name
	  });
		this.store.add(loggedInUserRecord);
		
		this.setValue(user_id);
	}	
});

Ext.reg('selectuser', GO.form.SelectUser);