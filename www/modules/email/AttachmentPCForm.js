GO.email.AttachmentPCForm = function(config)
{
	if (!config) {
		config = {};
	}


	this.uploadFile = new GO.form.UploadFile({
		addText:GO.email.lang.attachFilesPC,
		cls:'email-upload-pc',
		inputName:'attachments',
		createNoRows:true
	}),
	this.uploadFile.on('fileAdded',function(e, input)
	{
		this.uploadHandler();
	},this)
	
	config.border=false;
	config.fileUpload=true;
	config.autoScroll=true;
	
	config.items=[this.uploadFile];

	GO.email.AttachmentPCForm.superclass.constructor.call(this, config);

	this.addEvents({
		'upload' : true
	});
	
}
Ext.extend(GO.email.AttachmentPCForm, Ext.form.FormPanel, {

	uploadHandler : function(){

		this.form.submit({
			url:GO.settings.modules.email.url+'action.php',
			success:function(form, action){
				this.uploadFile.clearQueue();
				
				var file = (action.result.files) ? action.result.files[0] : action.result.file;
				
				this.fireEvent('upload', this, file);
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
