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
 
//override Ext functions here
/* bug in 2.2 */
Ext.form.TriggerField.override({
    afterRender : function(){
        Ext.form.TriggerField.superclass.afterRender.call(this);
        var y;
        if(Ext.isIE && !this.hideTrigger && this.el.getY() != (y = this.trigger.getY())){
            this.el.position();
            this.el.setY(y);
        }
    }
});

/**
 * Keep window in viewport and no shadows by default for IE performance
 */

Ext.Window.override({
	 shadow : false,
	 constrainHeader : true
});

/**
 * For editor grid in scrolling view
 */

Ext.override(Ext.Editor, {
	doAutoSize : function(){
		if(this.autoSize){
			var sz = this.boundEl.getSize(), fs = this.field.getSize();
			switch(this.autoSize){
				case "width":
					this.setSize(sz.width, fs.height);
					break;
				case "height":
					this.setSize(fs.width, sz.height);
					break;
				case "none":
					this.setSize(fs.width, fs.height);
					break;
				default:
					this.setSize(sz.width,  sz.height);
			}
		}
	}
});

/**
 * Localization
 */
Ext.MessageBox.buttonText.yes = GO.lang['cmdYes'];
Ext.MessageBox.buttonText.no = GO.lang['cmdNo'];
Ext.MessageBox.buttonText.ok = GO.lang['cmdOk'];
Ext.MessageBox.buttonText.cancel = GO.lang['cmdCancel'];


/**
 * Fix for loosing pasted value in HTML editor
 */
Ext.override(Ext.form.HtmlEditor, {
	getValue : function() {
		this.syncValue();
		return Ext.form.HtmlEditor.superclass.getValue.call(this);
	}
});

Ext.override(Ext.DatePicker, {
	startDay: parseInt(GO.settings.first_weekday)
});

