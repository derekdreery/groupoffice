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
  * param group_name: the text, the initial text to show
  * param group_id: the initial group_id
  */


 GO.currencies.SelectCurrency = function(config){
	Ext.apply(this, config);

	this.store = GO.currencies.currenciesStore;

	GO.currencies.SelectCurrency.superclass.constructor.call(this,{
		valueField: 'code',
		displayField:'code',
		width:50,
		triggerAction: 'all',
		selectOnFocus:true,
		forceSelection:true,
		editable:false,
		mode:'local',
		lazyInit:false
	});
}

Ext.extend(GO.currencies.SelectCurrency, GO.form.ComboBox,{
	afterRender : function(){
		
		if(!this.store.loaded){
			this.store.load({
				callback : function(){
					var v = this.getValue();
					if(v){
						this.setValue(v);
					}
				},
				scope:this
			});
		}else
		{
			this.selectFirst();
		}


		GO.currencies.SelectCurrency.superclass.afterRender.call(this);
	}
});