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
		
		this.on('blur', function(input){			
			var number = GO.util.unlocalizeNumber(input.getValue());	
 			input.setValue(GO.util.numberFormat(number, this.decimals));
  	});
  	  	
  	this.on('focus',function(input){
  		input.focus(true);
  	});		
	}/*,
	
	setValue : function(v)
	{
		GO.form.NumberField.superclass.setValue.call(GO.util.numberFormat(v, this.decimals));
	}*/	
});

Ext.reg('numberfield', GO.form.NumberField);