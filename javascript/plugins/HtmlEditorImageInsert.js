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
    
    this.addEvents({'insert' : true});
}

Ext.extend(GO.plugins.HtmlEditorImageInsert, Ext.util.Observable, {
/*	imageInsert : function(){
		if(!this.propertiesWindow)
  	{
			this.formPanel = new Ext.form.FormPanel({
				border:false,
				autoHeight: true,
				labelWidth: 70,
				defaultType: 'textfield',
				cls:'go-form-panel',
				items:[this.urlField = new Ext.form.TextField({
					name:'url',
					fieldLabel:'URL',
					anchor:'100%'
				}),
				new Ext.Button({
					text: 'Select',				        						
					handler: this.showFileBrowser, 
					scope: this 
				})]
			});
			
			this.propertiesWindow = new Ext.Window({
				
				title: 'Select files',
				autoHeight: true,
				width:400,
				layout:'fit',
				border:false,
				closeAction:'hide',
				items: this.formPanel,
				buttons:[
					{
						text: GO.lang['cmdOk'],				        						
						handler: function(){
							
							var html = '<img src="'+this.urlField.getValue()+'" border="0" />';
							
							this.fireEvent('insert', this);
							
							this.editor.relayCmd('inserthtml', html);
							this.propertiesWindow.hide();
						}, 
						scope: this 
					},{
						text: GO.lang['cmdClose'],				        						
						handler: function(){
							this.propertiesWindow.hide();
						},
						scope:this
					}
					
				]
								        				
			});
		}		

  	this.propertiesWindow.show.defer(200, this.propertiesWindow);
  	
		
	},*/
	onRender :  function() {
	  if (!Ext.isSafari) {
        this.editor.tb.add({
            itemId : 'htmlEditorImage',
            cls : 'x-btn-icon go-edit-insertimage',
            enableToggle: false,
            scope: this,
            handler:function(){ this.showFileBrowser(); },
            clickEvent:'mousedown',
            tabIndex:-1,
            tooltip:{title:GO.lang.image, text:GO.lang.insertImage}
        });
    }
	},
	
	showFileBrowser : function (){
		
		if(!GO.files)
		{
			alert(GO.lang.noFilesModule);
			return false;
		}
		
		if(!this.fileBrowser)
		{
			this.fileBrowser = new GO.files.FileBrowser({
				border:false,
				fileClickHandler: this.selectImage,
				filesFilter:'jpg,png,gif,jpeg,bmp',
				scope: this
			});
			
			this.fileBrowserWindow = new Ext.Window({
				
				title: 'Select files',
				height:480,
				width:680,
				layout:'fit',
				border:false,
				closeAction:'hide',
				items: this.fileBrowser,
				buttons:[
					{
						text: GO.lang['cmdOk'],				        						
						handler: this.selectImage, 
						scope: this 
					},{
						text: GO.lang['cmdClose'],				        						
						handler: function(){
							this.fileBrowserWindow.hide();
						},
						scope:this
					}
					
				]
								        				
			});
		}		
		this.fileBrowserWindow.show.defer(200, this.fileBrowserWindow);
	},
	
	selectImage : function(){
		
		var records = this.fileBrowser.getSelectedGridRecords();
		
		if(records.length)
		{
			this.selectedPath = records[0].data.path;
			this.selectedUrl = GO.settings.modules.files.url+'download.php?path='+encodeURIComponent(this.selectedPath);
				
			var html = '<img src="'+this.selectedUrl+'" border="0" />';
								
			this.fireEvent('insert', this);
			
			this.editor.insertAtCursor(html);
		}	
		this.fileBrowserWindow.hide();
	}
	
});