/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: HtmlEditorImageInsert.js 10290 2012-05-02 08:08:30Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.site.HtmlEditorImageInsert = function(config) {
    
	config = config || {};
    
	Ext.apply(this, config);
    
	this.init = function(htmlEditor) {
		this.editor = htmlEditor;
		this.editor.on('render', this.onRender, this);
	};
	this.imageEditorDialog = false,
	this.filesFilter='jpg,png,gif,jpeg,bmp';
	this.addEvents({
		'insert' : true
	});
};

Ext.extend(GO.site.HtmlEditorImageInsert, Ext.util.Observable, {

	root_folder_id : 0,
	model_name: "GO_Site_Model_Site",
	folder_id : 0,
	
	isTempFile : true,
	
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
		};
		element.overflowText=GO.lang.insertImage;
		
		this.uploadForm = new GO.UploadPCForm({
//			url: GO.url('core/upload'),
			addText: GO.site.lang.attachFilesPC
		});

		this.uploadForm.on('upload', function(e, files)
		{
			this.selectTempImage(files[0]);
		},this);

		var menuItems = [
		this.uploadForm
		];

		if(GO.files){
			menuItems.push({
				iconCls:'btn-groupoffice',
				text : GO.site.lang.attachFilesGO.replace('{product_name}', GO.settings.config.product_name),
				handler : function()
				{
					this.showFileBrowser();
				},
				scope : this
			});
		}

		this.menu = element.menu = new Ext.menu.Menu({
			items:menuItems
		});
		
		
		this.editor.tb.add(element);
	},
	
	showFileBrowser : function (){
		GO.request({
			url:'files/folder/checkModelFolder',
			params:{								
				mustExist:true,
				model:this.model_name,
				id:this.id
			},
			success:function(response, options, result){														
				GO.files.createSelectFileBrowser();
				GO.selectFileBrowser.setFileClickHandler(this.selectImage, this);
				GO.selectFileBrowser.setFilesFilter(this.filesFilter);
				GO.selectFileBrowser.setRootID(result.files_folder_id, result.files_folder_id);
				GO.selectFileBrowserWindow.show();
				GO.selectFileBrowserWindow.show.defer(200, GO.selectFileBrowserWindow);
			},
			scope:this
		});
	},

	selectImage : function(r){	
		
		this.selectedRecord = r;
		this.selectedPath = r.data.path;
	
		this.showImageEditor(r.data.id,this.selectedPath);
		GO.selectFileBrowserWindow.hide();
	},
	setSiteId : function(site_id){
		this.id = site_id;
	},
	showImageEditor : function(id,path){
		
		var selectedUrl = GO.url("files/file/download",{id:id});
		
		if(!this.imageEditorDialog){
			this.imageEditorDialog = new GO.site.HtmlEditorImageDialog();
			
			this.imageEditorDialog.on('hide', function(){
			//var html = '<site:img id="'+r.data.id+'" path="'+this.selectedPath+'" lightbox="1"><img src="'+this.selectedUrl+'" /></site:img>';
			var html = this.imageEditorDialog.getTag();
			
			if(html){
				this.editor.focus();
				this.editor.insertAtCursor(html);
			}
		},this);
			
		}
		
		var dialogconfig = [];

		dialogconfig.id = id;
		dialogconfig.path = path;
		dialogconfig.url = selectedUrl;
		
		this.imageEditorDialog.show(dialogconfig);
	}
});