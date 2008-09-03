/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: SelectFee.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.projects.SelectFee = function(config){
	
	Ext.apply(this, config);
	
	if(!this.company_name)
	{
		this.company_name = '';
	}
	
	this.store = GO.projects.stores.readableFees; 
		
	GO.projects.SelectFee.superclass.constructor.call(this,{
		displayField: 'name',
		hiddenName:'fee_id',		
		valueField: 'id',
		triggerAction:'all',		
		mode:'local',
		editable: false,
		selectOnFocus:true,
	  forceSelection: true,
		typeAhead: true,
		fieldLabel: GO.projects.lang.fee
	});
	
}
Ext.extend(GO.projects.SelectFee, GO.form.ComboBox,{
	afterRender : function(){
		
		GO.projects.SelectFee.superclass.afterRender.call(this);
		
		this.store.load({
			callback:function(){
				this.selectFirst();
			},
			scope:this
		}, this);		
	}	
});