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
 * @class GO.form.SelectGroup
 * @extends GO.form.ComboBox
 *
 * Selects a Group-Office user group.
 * 
 * @constructor
 * Creates a new SelectGroup
 * @param {Object} config Configuration options
 */

 GO.form.SelectGroup = function(config){
	Ext.apply(this, config);

	this.store = new GO.data.JsonStore({
		url: GO.settings.modules.groups.url+'non_admin_json.php',
		baseParams: {'task':'groups'},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name','email','groupname'],
		remoteSort: true
	});
	this.store.setDefaultSort('name', 'asc');


	this.setRemoteValue(GO.settings.group_id, GO.settings.name);

	GO.form.SelectGroup.superclass.constructor.call(this,{
		displayField: 'name',
		hiddenName:'group_id',
		value: GO.settings.group_id,
		valueField: 'id',
		triggerAction: 'all',
		selectOnFocus:true,
		forceSelection: true,
		pageSize: parseInt(GO.settings['max_rows_list'])
	});
}

Ext.extend(GO.form.SelectGroup, GO.form.ComboBox,{
	setRemoteValue : function(group_id, name)
	{
		var GroupRecord = Ext.data.Record.create([
	    {name: 'id'},
	    {name: 'name'}
    ]);
	  var loggedInGroupRecord = new GroupRecord({
	  		id: group_id,
	  		name: name
	  });
		this.store.add(loggedInGroupRecord);

		this.setValue(group_id);
	}
});

Ext.reg('selectgroup', GO.form.SelectGroup);