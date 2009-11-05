/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id: SelectCalendar.js 2787 2009-07-07 15:45:25Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.calendar.SelectCalendar = function(config){

	config = config || {};

	if(!config.hiddenName)
		config.hiddenName='calendar_id';

	if(!config.fieldLabel)
	{
		config.fieldLabel=GO.calendar.lang.calendar;
	}

	Ext.apply(this, config);


	this.store = new GO.data.JsonStore({
		url: GO.settings.modules.calendar.url+'json.php',
		baseParams: {'task': 'writable_calendars'},
		root: 'results',
		totalProperty: 'total',
		id: 'id',
		fields:['id','name','user_name'],
		remoteSort:true
	});

	GO.calendar.SelectCalendar.superclass.constructor.call(this,{
		displayField: 'name',
		valueField: 'id',
		triggerAction:'all',
		mode:'remote',
		editable: true,
		selectOnFocus:true,
		forceSelection: true,
		typeAhead: true,
		emptyText:GO.lang.strPleaseSelect,
		pageSize: parseInt(GO.settings.max_rows_list)
	});

}
Ext.extend(GO.calendar.SelectCalendar, GO.form.ComboBox, {

});


