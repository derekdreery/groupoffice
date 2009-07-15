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
 
GO.form.ComboBox = Ext.extend(Ext.form.ComboBox, {
	
	/*setRemoteText : function(text){
        if(this.rendered)
        {
            this.setRawValue(text);
        }
        this.lastSelectionText=text;
    },*/
	setRemoteText : function(text)
	{
		var r = this.findRecord(this.valueField, this.value);
		if(!r)
		{
			var comboRecord = Ext.data.Record.create([{
				name: this.valueField
			},{
				name: this.displayField
			}]);

			var recordData = {};
			recordData[this.valueField]=this.value;
			recordData[this.displayField]=text;

			var currentRecord = new comboRecord(recordData);
			this.store.add(currentRecord);
            
			this.setValue(this.value);
		}
	},

	initValue : function(){
		GO.form.ComboBox.superclass.initValue.call(this);
		this.setRawValue(this.lastSelectionText);
	},
	
	selectFirst : function(){
		if(this.store.getCount())
		{
			var records = this.store.getRange(0,1);
			this.setValue(records[0].get(this.valueField));
		}
	},
	
	clearLastSearch : function(){
		this.lastQuery=false;
		this.hasSearch=false;
	}
});

Ext.reg('combo', GO.form.ComboBox);