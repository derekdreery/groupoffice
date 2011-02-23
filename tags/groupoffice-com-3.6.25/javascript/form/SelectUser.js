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
 * @class GO.form.SelectUser
 * @extends GO.form.ComboBox
 *
 * Selects a Group-Office user.
 *
 * @constructor
 * Creates a new SelectUser
 * @param {Object} config Configuration options
 */
GO.form.SelectUser = function(config){

	config = config || {};

	if(typeof(config.allowBlank)=='undefined')
		config.allowBlank=false;

	Ext.apply(this, config);
	
	this.store = new GO.data.JsonStore({
		url: GO.settings.modules.users.url+'non_admin_json.php',
		baseParams: {
			'task':'users'
		},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name','email','username','company','first_name', 'middle_name', 'last_name', 'address', 'address_no', 'zip', 'city', 'state', 'country','cf'],
		remoteSort: true
	});
	this.store.setDefaultSort('name', 'asc');

	if(!this.hiddenName)
		this.hiddenName='user_id';

	if(!config.startBlank){
		this.setRemoteValue(GO.settings.user_id, GO.settings.name);
		this.value=GO.settings.user_id;
	}

	if(!this.valueField)
		this.valueField='id';
	
	GO.form.SelectUser.superclass.constructor.call(this,{
		displayField: 'name',		
		triggerAction: 'all',
		selectOnFocus:true,
		forceSelection: true,
		pageSize: parseInt(GO.settings['max_rows_list'])
	});
}

Ext.extend(GO.form.SelectUser, GO.form.ComboBoxReset,{
	setRemoteValue : function(user_id, name)
	{
		var r = this.findRecord('id', user_id);
		if(!r)
		{
			var UserRecord = Ext.data.Record.create([
			{
				name: 'id'
			},{
				name: 'name'
			}
			]);
			var loggedInUserRecord = new UserRecord({
				id: user_id,
				name: name
			});
			this.store.add(loggedInUserRecord);
		}
		
		this.setValue(user_id);
	}	
});

Ext.reg('selectuser', GO.form.SelectUser);