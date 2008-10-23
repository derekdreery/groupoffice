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


Ext.Window.override({
	 shadow : false,
	 constrainHeader : true
});


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


Ext.MessageBox.buttonText.yes = GO.lang['cmdYes'];
Ext.MessageBox.buttonText.no = GO.lang['cmdNo'];
Ext.MessageBox.buttonText.ok = GO.lang['cmdOk'];
Ext.MessageBox.buttonText.cancel = GO.lang['cmdCancel'];