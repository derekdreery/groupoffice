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
 * @class GO.form.NumberField
 * @extends Ext.form.TextField
 * Numeric text field that provides automatic number formatting by using the
 * Group-Office personal settings
 * @constructor
 * Creates a new NumberField
 * @param {Object} config Configuration options
 */
GO.form.NumberField = Ext.extend(Ext.form.TextField, {
	/**
	 * @cfg {Number} decimals The maximum precision to display after the decimal separator (defaults to 2)
	 */
	decimals : 2,
	initComponent : function(){
		GO.form.NumberField.superclass.initComponent.call(this);
		
		this.style="text-align:right";
		
		this.on('focus',function(input){
			this.fixPrecision();
			input.focus(true);
		});
	}
	,
	beforeBlur : function(){
		this.fixPrecision();
	},
	fixPrecision : function(){
		var number = GO.util.unlocalizeNumber(this.getValue());
		this.setValue(GO.util.numberFormat(number, this.decimals));
	}
});

Ext.reg('numberfield', GO.form.NumberField);