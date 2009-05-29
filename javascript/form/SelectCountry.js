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
  
GO.form.SelectCountry = function(config){
	
	var countries = [];
	
	for(var c in GO.lang.countries)
	{
		countries.push([c, GO.lang.countries[c]]);
	}
		
	Ext.apply(this, config);

	GO.form.SelectCountry.superclass.constructor.call(this,{
   store: new Ext.data.SimpleStore({
        fields: ['iso', 'name'],
        data : countries        
    }),
		valueField: 'iso',
		displayField: 'name',
		triggerAction: 'all',
		editable: true,
		mode:'local',
		selectOnFocus:true,
		forceSelection: true,
		emptyText: GO.lang.strNoCountrySelected
	});

}
 
Ext.extend(GO.form.SelectCountry, Ext.form.ComboBox);