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
 * @author Danny Wijffelaars <dwijffelaars@intermesh.nl>
 */

 /**
  * param group_name: the text, the initial text to show
  * param group_id: the initial group_id
  */

GO.addressFormatStore = new GO.data.JsonStore({
		url: BaseHref+'json.php',
		baseParams: {'task':'select_address_format'},
		root: 'results',
		totalProperty: 'total',
		id: 'iso',
		fields:['iso','address_format_id','country_name'],
		remoteSort: true
	});
GO.addressFormatStore.setDefaultSort('country_name', 'ASC');

 GO.form.SelectAddressFormat = function(config){
	Ext.apply(this, config);

	this.store = GO.addressFormatStore;


	//this.setRemoteValue(GO.settings.group_id, GO.settings.name);

	GO.form.SelectAddressFormat.superclass.constructor.call(this,{
		valueField: 'iso',
		triggerAction: 'all',
		selectOnFocus:true,
		forceSelection:true,
		editable:true,
		mode:'local',
		lazyInit:false
	});
}

Ext.extend(GO.form.SelectAddressFormat, GO.form.ComboBox,{
	afterRender : function(){

		if(!this.store.loaded)
			this.store.load({
				callback : function(){
					var v = this.getValue();
					if(v){						
						this.setValue(v);
					}
				},
				scope:this
			});

		GO.form.SelectAddressFormat.superclass.afterRender.call(this);

	}


});