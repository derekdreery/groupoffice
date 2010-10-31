GO.UploadPCForm = function(config)
{
	if (!config) {
		config = {};
	}

	config.iconCls='btn-computer go-upload-pc-form';

	if(!config.addText)
	{
		config.addText = GO.email.lang.attachFilesPC;
	}
	
	this.uploadFile = new GO.form.UploadFile({
		addText:config.addText,
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

	GO.UploadPCForm.superclass.constructor.call(this, config);

	this.addEvents({
		'upload' : true
	});
	
}
Ext.extend(GO.UploadPCForm, Ext.form.FormPanel, {

	uploadHandler : function(){

		this.form.submit({			
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
