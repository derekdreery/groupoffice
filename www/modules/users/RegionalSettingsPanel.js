/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: RegionalSettingsPanel.js 2079 2008-06-10 15:04:01Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.users.RegionalSettingsPanel = function(config)
{
	if(!config)
	{
		config={};
	}
	
	this.autoScroll=true;
	
	

	

	/*  dateformat */
	var dateFormatData = new Ext.data.SimpleStore({
		fields: ['id', 'date_format'],		
		data : [
		['dmY', GO.users.lang.dmy],
		['mdY', GO.users.lang.mdy],
		['Ymd', GO.users.lang.jmd]
		]
	});

	/* dateseperator */
	var dateSeperatorData = new Ext.data.SimpleStore({
		fields: ['id', 'date_seperator'],
		data : [
		['-', '-'],
		['.', '.'],
		['/', '/']
		]
	});

	/* timeformat */
	var 	timeFormatData = new Ext.data.SimpleStore({
		fields: ['id', 'time_format'],		
		data : [
		['G:i', GO.users.lang.fullhourformat],
		['g:i a', GO.users.lang.halfhourformat]
		]
	});

	/* timeformat */
	var 	firstWeekdayData = new Ext.data.SimpleStore({
		fields: ['id', 'first_weekday'],		
		data : [
		['0', GO.users.lang.sunday],
		['1', GO.users.lang.monday]
		]
	});
	

var dateFormat = GO.settings.date_format.substring(0,1)+GO.settings.date_format.substring(2,3)+GO.settings.date_format.substring(4,5);
	
	config.border=false;
	config.hideLabel=true;
	config.title = GO.users.lang.regionalSettings;
	config.layout='form';
	config.defaults={anchor:'100%'};
	config.defaultType = 'textfield';
	config.cls='go-form-panel';
	config.labelWidth=190;
	config.items=[
		new Ext.form.ComboBox({
			fieldLabel: GO.users.lang['cmdFormLabelLanguage'],
			name: 'language_id',
			id: 'language_id',
			store:  new Ext.data.SimpleStore({
					fields: ['id', 'language'],
					data : GO.Languages
				}),
			displayField:'language',
			valueField: 'id',
			hiddenName:'language',
			mode:'local',
			triggerAction:'all',
			editable: false,
			selectOnFocus:true,
			forceSelection: true,
			value: GO.settings.language
		}),
		new Ext.form.ComboBox({
			fieldLabel: GO.users.lang.cmdFormLabelTimezone,
			name: 'timezone',
			store: new Ext.data.SimpleStore({
					fields: ['timezone'],
					data : GO.users.TimeZones
				}),
			displayField: 'timezone',
			mode: 'local',
			triggerAction: 'all',
			selectOnFocus: true,
			forceSelection: true,
			value: GO.settings.timezone
		}),
		new Ext.form.ComboBox({
			fieldLabel: GO.users.lang['cmdFormLabelDateFormat'],
			name: 'date_format',
			store: dateFormatData,
			displayField: 'date_format',
			value: dateFormat,
			valueField: 'id',
			hiddenName: 'date_format',
			mode: 'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus: true,
			forceSelection: true
		}),
		new Ext.form.ComboBox({
			fieldLabel: GO.users.lang['cmdFormLabelDateSeperator'],
			name: 'date_seperator_name',
			store: dateSeperatorData,
			displayField: 'date_seperator',			
			value: GO.settings.date_seperator,
			valueField: 'id',
			hiddenName: 'date_seperator',
			mode: 'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus: true,
			forceSelection: true
		}),
		new Ext.form.ComboBox({
			fieldLabel: GO.users.lang.timeFormat,
			name: 'time_format_name',
			store: timeFormatData,
			displayField: 'time_format',
			valueField: 'id',
			hiddenName: 'time_format',
			mode: 'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus: true,
			value: GO.settings.time_format,
			forceSelection: true
		}),
			
		new Ext.form.ComboBox({
			fieldLabel: GO.users.lang['cmdFormLabelFirstWeekday'],
			name: 'first_weekday_name',
			store: firstWeekdayData,
			displayField: 'first_weekday',
			valueField: 'id',
			hiddenName: 'first_weekday',
			mode: 'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus: true,
			forceSelection: true,
			value: GO.settings.first_weekday
		}),
		{
			xtype: 'textfield', 
			fieldLabel: GO.users.lang['cmdFormLabelThousandSeperator'], 
			name: 'thousands_seperator',
			value: GO.settings.thousands_seperator
		},
		{
			xtype: 'textfield', 
			fieldLabel: GO.users.lang['cmdFormLabelDecimalSeperator'], 
			name: 'decimal_seperator',
			value: GO.settings.decimal_seperator
		},
		{
			xtype: 'textfield', 
			fieldLabel: GO.users.lang['cmdFormLabelCurrency'], 
			name: 'currency',
			value: GO.settings.currency
		}
		];	
	
	GO.users.RegionalSettingsPanel.superclass.constructor.call(this, config);		
};


Ext.extend(GO.users.RegionalSettingsPanel, Ext.Panel,{
	

});			