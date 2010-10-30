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

	this.filesFilter='jpg,png,gif,jpeg,bmp';
	this.addEvents({
		'insert' : true
	});
}

Ext.extend(GO.plugins.HtmlEditorImageInsert, Ext.util.Observable, {


	root_folder_id : 0,
	folder_id : 0,
	onRender :  function() {

		var element={};

		element.itemId='htmlEditorImage';
		element.cls='x-btn-icon go-edit-insertimage';
		element.enableToggle=false;
		element.scope=this;
		element.clickEvent='mousedown';
		element.tabIndex=-1;
		element.tooltip={
			title:GO.lang.image,
			text:GO.lang.insertImage
		}
		element.overflowText=GO.lang.insertImage;
		
		if(GO.files)
		{					
			this.uploadForm = new GO.files.UploadPCForm({
				baseParams:{
					task:'upload_attachment'
				},
				url:GO.settings.modules.email.url+'action.php'
			});

			this.uploadForm.on('upload', function(e, file)
			{
				this.selectTempImage(file.name);
			},this);
			
			this.menu = element.menu = new Ext.menu.Menu({
				items:[
				this.uploadForm,
				{
					text : GO.email.lang.attachFilesGO.replace('{product_name}', GO.settings.config.product_name),
					handler : function()
					{
						this.showFileBrowser();
					},
					scope : this
				}]
			})
		}else
		{
			element.handler=function(){
				this.showFileBrowser();
			}
		}
		
		this.editor.tb.add(element);
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

	selectTempImage : function(name)
	{
		this.selectedUrl = GO.settings.modules.files.url+'download_temp_file.php?name='+name;
		this.name = name;		

		var html = '<img src="'+this.selectedUrl+'" border="0" />';

		this.fireEvent('insert_temp', this);
		
		this.menu.hide();

		this.editor.focus();
		this.editor.insertAtCursor(html);		
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