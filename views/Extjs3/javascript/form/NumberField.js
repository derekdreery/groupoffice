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
     * @cfg {String} minText Error text to display if the minimum value validation fails (defaults to "The minimum value for this field is {minValue}")
     */
	minText : GO.lang.numMinValue,
    
	/**
     * @cfg {String} maxText Error text to display if the maximum value validation fails (defaults to "The maximum value for this field is {maxValue}")
     */
	maxText : GO.lang.numMaxValue,
	
	/**
		* @cfg {Number} minValue The minimum allowed value (defaults to Number.NEGATIVE_INFINITY)
		*/
	minValue : Number.NEGATIVE_INFINITY,

	/**
		* @cfg {Number} maxValue The maximum allowed value (defaults to Number.MAX_VALUE)
		*/
	maxValue : Number.MAX_VALUE,
		
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
	},
	
	
	
	
	/**
     * Runs all of NumberFields validations and returns an array of any errors. Note that this first
     * runs TextField's validations, so the returned array is an amalgamation of all field errors.
     * The additional validations run test that the value is a number, and that it is within the
     * configured min and max values.
     * @param {Mixed} value The value to get errors for (defaults to the current field value)
     * @return {Array} All validation errors for this field
     */
	getErrors: function(value) {
		var errors = GO.form.NumberField.superclass.getErrors.apply(this, arguments);
        
		value = Ext.isDefined(value) ? value : GO.util.unlocalizeNumber(this.getRawValue());
				
		if (value.length < 1) { // if it's blank and textfield didn't flag it then it's valid
			return errors;
		}
        
         
		if (value < this.minValue) {
			errors.push(String.format(this.minText, this.minValue));
		}
        
		if (value > this.maxValue) {
			errors.push(String.format(this.maxText, this.maxValue));
		}
        
		return errors;
	}
});

Ext.reg('numberfield', GO.form.NumberField);