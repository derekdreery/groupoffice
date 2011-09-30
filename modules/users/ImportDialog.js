GO.users.ImportDialog = Ext.extend(Ext.Window, {
	
	initComponent : function(){
		
		this.title=GO.lang.cmdImport;
		
		this.width=500;
		this.autoHeight=true;
		
		this.closeAction='hide';
		
		this.uploadFile = new GO.form.UploadFile({
			inputName : 'importfile',
			max:1  				
		});				
		
		this.upForm = new Ext.form.FormPanel({
			fileUpload:true,
			waitMsgTarget:true,
			items: [new GO.form.HtmlComponent({
				html: GO.users.lang.importText+'<br /><br />'
			}),
			this.uploadFile],
			cls: 'go-form-panel'
		});
		
		
		
		this.items=[
		
		this.upForm];
		
		this.buttons=[
		{
			text:GO.lang.cmdOk,
			handler: this.uploadHandler, 
			scope: this
		},
		{
			text:GO.lang['cmdClose'],
			handler: function(){this.hide()}, 
			scope: this
		},{
			text:GO.users.lang.downloadSampleCSV,
			handler: function(){
				window.open(GO.settings.modules.users.url+'download_importsample.php');
			},
			scope:this			
		}];
		
		this.addEvents({'import': true});
		
		GO.users.ImportDialog.superclass.initComponent.call(this);
	},
	uploadHandler : function(){
		this.upForm.form.submit({
			waitMsg:GO.lang.waitMsgUpload,
			url:GO.settings.modules.users.url+'action.php',
			params: {
			  task: 'import'			  
			},
			success:function(form, action){
				this.uploadFile.clearQueue();						
				this.hide();
				
				this.fireEvent('import');
				
				var fb = action.result.feedback.replace(/BR/g,'<br />');
				
				Ext.MessageBox.alert(GO.lang.strSuccess, fb);
			},
			failure: function(form, action) {	
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					
					var fb = action.result.feedback.replace(/BR/g,'<br />');
					
					Ext.MessageBox.alert(GO.lang['strError'], fb);
				}
			},
			scope: this
		});			
	}
});

