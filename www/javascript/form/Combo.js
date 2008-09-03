/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: Combo.js 1857 2008-04-29 13:57:06Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.form.ComboBox = Ext.extend(Ext.form.ComboBox, {
	
	setRemoteText : function(text){
		this.setRawValue(text);
		this.lastSelectionText=text;
	},
	
	selectFirst : function(){		
		if(this.store.reader.jsonData.results.length>0)
		{
			this.setValue(this.store.reader.jsonData.results[0].id);
		}
	}
});