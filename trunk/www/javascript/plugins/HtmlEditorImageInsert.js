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

GO.plugins.HtmlEditorImageInsert = function(config) {
    
	config = config || {};
    
	Ext.apply(this, config);
    
	this.init = function(htmlEditor) {
		this.editor = htmlEditor;
		this.editor.on('render', this.onRender, this);
	};
    
	this.addEvents({
		'insert' : true
	});
}

Ext.extend(GO.plugins.HtmlEditorImageInsert, Ext.util.Observable, {


	root_folder_id : 0,
	folder_id : 0,
	onRender :  function() {
			this.editor.tb.add({
				itemId : 'htmlEditorImage',
				cls : 'x-btn-icon go-edit-insertimage',
				enableToggle: false,
				scope: this,
				handler:function(){
					this.showFileBrowser();
				},
				clickEvent:'mousedown',
				tabIndex:-1,
				tooltip:{
					title:GO.lang.image,
					text:GO.lang.insertImage
					}
			});
	},
	
	showFileBrowser : function (){
		
		if(!GO.files)
		{
			alert(GO.lang.noFilesModule);
			return false;
		}

		GO.files.createSelectFileBrowser();

		GO.selectFileBrowser.setFileClickHandler(this.selectImage, this);

		GO.selectFileBrowser.setFilesFilter(this.filesFilter);
		GO.selectFileBrowser.setRootID(this.root_folder_id, this.files_folder_id);
		GO.selectFileBrowserWindow.show();

		GO.selectFileBrowserWindow.show.defer(200, GO.selectFileBrowserWindow);
	},
	
	selectImage : function(r){	

		this.selectedRecord = r;
		this.selectedPath = r.data.path;
		this.selectedUrl = GO.settings.modules.files.url+'download.php?id='+this.selectedRecord.get('id');
				
		var html = '<img src="'+this.selectedUrl+'" border="0" />';
								
		this.fireEvent('insert', this);

		this.editor.focus();
			
		this.editor.insertAtCursor(html);
		
		GO.selectFileBrowserWindow.hide();
	}
	
});