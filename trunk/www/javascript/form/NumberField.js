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
 
GO.form.NumberField = Ext.extend(Ext.form.TextField, {
	decimals : 2,
	initComponent : function(){
		GO.form.NumberField.superclass.initComponent.call(this);
		
		this.style="text-align:right";
		
		this.on('focus',function(input){
			this.setValue(this.getValue());
			input.focus(true);
		});
	},
	getValue : function(){
		return GO.util.unlocalizeNumber(Ext.form.NumberField.superclass.getValue.call(this));
	},

	setValue : function(v){
		Ext.form.NumberField.superclass.setValue.call(this, GO.util.numberFormat(v,this.decimals));
	},
	beforeBlur : function(){
		this.setValue(this.getValue());
	}
});

Ext.reg('numberfield', GO.form.NumberField);