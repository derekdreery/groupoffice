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

GO.form.ComboBoxReset = Ext.extend(GO.form.ComboBox, {
			initComponent : Ext.form.TwinTriggerField.prototype.initComponent,
			getTrigger : Ext.form.TwinTriggerField.prototype.getTrigger,
			initTrigger : Ext.form.TwinTriggerField.prototype.initTrigger,
			trigger1Class : 'x-form-clear-trigger',
			hideTrigger1 : true,
			reset : Ext.form.Field.prototype.reset.createSequence(function() {
					this.triggers[0].hide();
			}),
			onViewClick : Ext.form.ComboBox.prototype.onViewClick.createSequence(function() {
								this.triggers[0].show();
							}),
			onTrigger2Click : function() {
				this.onTriggerClick();
			},
			onTrigger1Click : function() {
				this.clearValue();
				this.triggers[0].hide();
				this.fireEvent('clear', this);
			},
			setValue : function(v){
				GO.form.ComboBoxReset.superclass.setValue.call(this, v);
				if(v!='')
				{					
					this.triggers[0].show();
				}
			},afterRender:function(){
				GO.form.ComboBoxReset.superclass.afterRender.call(this);
				if(Ext.isIE8)this.el.setTop(1);
			}
			
		});

Ext.reg('comboboxreset', GO.form.ComboBoxReset);