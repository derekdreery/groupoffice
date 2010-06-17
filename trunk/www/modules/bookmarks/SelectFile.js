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
 * @author Twan Verhofstad
 */


GO.bookmarks.SelectFile = Ext.extend(Ext.form.TriggerField,{

	triggerClass : 'fs-form-file-select',
	filesFilter : '',
	root_folder_id : 0,
	files_folder_id : 0,
	dialog : false,

	onTriggerClick : function(){

		var thumb_id = this.dialog.formPanel.form.baseParams.id						// bookmark ID
		var thumbtitle = this.dialog.formPanel.form.items.items[1].value; // titel
		var thumbicon = this.dialog.formPanel.form.items.items[5].value;  // pad naar logo
		var pubicon = Ext.get('pubicon');
		if (!thumb_id) thumbtitle='Example';
		this.thumbsDialog = new GO.bookmarks.ThumbsDialog({
			thumb_id:thumb_id,
			iconfield:this,
			thumbtitle:thumbtitle,
			thumbicon:thumbicon,
			folder_id:this.root_folder_id,
			pubicon:pubicon
		});
		this.thumbsDialog.show(); //thumbsDialog, geen hide() maar close()    :(
	}

});

Ext.ComponentMgr.registerType('selectfile', GO.bookmarks.SelectFile);