/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: PlainField.js 1857 2008-04-29 13:57:06Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

/**
 * @class GO.form.PlainField
 * @extends Ext.Component
 * Base class to easily display simple text in the form layout.
 * @constructor
 * Creates a new PlainField Field 
 * @param {Object} config Configuration options
 */


GO.form.PlainField = Ext.extend(Ext.form.Field, {


	// private
	defaultAutoCreate: {
		tag: 'div',
		cls: 'x-form-plainfield'
	},

	// private
	initComponent: function() {
		GO.form.PlainField.superclass.initComponent.call(this);
		this.addEvents(
			/**
			 * @event load
			 * Fires when the content is loaded into the field
			 * @param {GO.form.PlainField} this
			 * @param {Object} file
			 */
			'load'
		);
	},
	
	getName: function(){
    return this.name;
  },

	// private
  initValue : function(){
      if(this.value !== undefined){
          this.setValue(this.value);
      }else if(this.el.dom.innerHTML.length > 0){
          this.setValue(this.el.dom.value);
      }
  },
	
	setValue : function(v){
		this.value = v;
    if(this.rendered){
        this.el.update(v);
    }
  }

});
Ext.reg('plainfield', GO.form.PlainField);