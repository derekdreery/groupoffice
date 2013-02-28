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
GO.files.UploadDialog = function(config) {
	if (!config) {
		config = {};
	}

	this.uploadFile = new GO.form.UploadFile({
				inputName : 'attachments',
				addText : GO.lang.smallUpload
			});

	var items = [this.uploadFile, new Ext.Button({
			text : GO.lang.largeUpload,
			handler : function() {
				if (!deployJava.isWebStartInstalled('1.5.0')) {
					Ext.MessageBox.alert(GO.lang.strError,
							GO.lang.noJava);
				} else {
					/*
					 * var p = GO.util.popup({ url:
					 * GO.settings.modules.files.url+'jupload/index.php?id='+encodeURIComponent(this.folder_id),
					 * width : 640, height: 500, target:
					 * 'jupload' });
					 */

					window
							.open(GO.settings.modules.files.url
									+ 'jupload/index.php?id='
									+ this.folder_id);

					this.hide();
				}
			},
			scope : this
		}),{
			border:false,
			html:GO.files.lang.uploadProperties,
			bodyStyle:'padding:20px 0px 10px'
		},{
			xtype:'textarea',
			name:'comments',
			fieldLabel:GO.files.lang.comments,
			anchor:'100%',
			height:100
	}];


	
	if(GO.customfields && GO.customfields.types["6"])
	{
		var cfFS, formField;
  	for(var i=0;i<GO.customfields.types["6"].panels.length;i++)
  	{
			var cfPanel = GO.customfields.types["6"].panels[i];

			cfFS = {
				xtype:'fieldset',
				title:cfPanel.title,
				items:[],
				autoHeight:true
			};
			for(var i=0;i<cfPanel.customfields.length;i++)
			{
				formField = GO.customfields.getFormField(cfPanel.customfields[i]);
				cfFS.items.push(formField);
			}

			items.push(cfFS);
  	}
	}


	this.upForm = new Ext.form.FormPanel({
		fileUpload : true,
		border:false,
		waitMsgTarget : true,
		autoHeight:true,
		baseParams: {
			task: 'upload'
		},
		items : items,
		cls : 'go-form-panel'
	});

	config.collapsible = false;
	config.maximizable = false;
	config.modal = true;
	config.layout='fit';
	config.resizable = false;
	config.width = 500;
	config.items = {
		autoScroll:true,
		border:false,
		items:[this.upForm]
	}
	config.height=400;
	config.autoScroll=true;
	config.closeAction = 'hide';
	config.title = GO.lang.uploadFiles;
	config.buttons = [{
				text : GO.lang['cmdOk'],
				handler : this.uploadHandler,
				scope : this
			}, {
				text : GO.lang['cmdClose'],
				handler : function() {
					this.hide();
				},
				scope : this
			}];

	GO.files.UploadDialog.superclass.constructor.call(this, config);

	this.addEvents({
				upload : true
			});
}
Ext.extend(GO.files.UploadDialog, Ext.Window, {
	show : function(folder_id) {
		if (!this.rendered) {
			this.render(Ext.getBody());
		}

		this.upForm.form.reset();
		
		this.folder_id=folder_id;
		GO.files.UploadDialog.superclass.show.call(this);
	},
	uploadHandler : function(){
		this.upForm.form.submit({
			url:GO.settings.modules.files.url+'action.php',
			waitMsg : GO.lang.waitMsgUpload,
			success:function(form, action){
				this.uploadFile.clearQueue();						
				this.hide();
				
				this.fireEvent('upload', action);
				
			},
			failure:function(form, action)
			{
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang['strErrorsInForm'];
				}else
				{
					error = action.result.feedback;
				}
				
				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope: this
		});			
	}
});
