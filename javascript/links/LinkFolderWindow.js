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

 
GO.LinkFolderWindow = function(config){
	if(!config)
	{
		config={};
	}
	var focusName = function(){
		this.newFolderNameField.focus(true);		
	};
		
	this.newFolderNameField = new Ext.form.TextField({	             	
        fieldLabel: GO.lang['strName'],
        name: 'name',
        value: GO.lang['newFolder'],
        allowBlank:false,
        anchor:'100%'   
    });
	this.formPanel = new Ext.form.FormPanel({
			defaultType: 'textfield',
			labelWidth:75,
			cls:'go-form-panel',
			waitMsgTarget:true,
			items:this.newFolderNameField,
			autoHeight:true	,
			baseParams:{task : 'link_folder', folder_id : 0}
		});
	
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=400;
	config.autHeight=true;
	config.closeAction='hide';
	config.title= GO.lang['newFolder'];
	config.items= this.formPanel;
	config.focus= focusName.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm(true);
			},
			scope: this
		},{
			text: GO.lang['cmdApply'],
			handler: function(){
				this.submitForm();
			},
			scope:this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}					
	];
	
	GO.LinkFolderWindow.superclass.constructor.call(this, config);
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.LinkFolderWindow, Ext.Window,{

	
	show : function (config) {
		
		if(!this.rendered)
			this.render(Ext.getBody());
		
		if(!config.folder_id)
		{
			config.folder_id=0
		}
		this.link_id=config.link_id ? config.link_id : 0;
		this.link_type=config.link_type ? config.link_type : 0;
		this.parent_id=config.parent_id ? config.parent_id : 0;
			
		this.setLinkFolderId(config.folder_id);
		
		if(this.folder_id>0)
		{
			this.formPanel.load({
				url : BaseHref+'json.php',
				
				success:function(form, action)
				{		
					this.setLinkFolderId (action.result.data.id);
					this.link_id=action.result.data.link_id;
					this.link_type=action.result.data.link_type;
					this.parent_id=action.result.data.parent_id;
					GO.LinkFolderWindow.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else 
		{
			
			this.formPanel.form.reset();
			
			GO.LinkFolderWindow.superclass.show.call(this);
		}
	},
	
	setLinkFolderId : function(folder_id)
	{
		if(!folder_id)
		{
			folder_id = 0;
		} 
		this.formPanel.form.baseParams['folder_id']=folder_id;
		this.folder_id=folder_id;		
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:BaseHref+'action.php',
			params: {
				'task' : 'save_link_folder',
				link_id : this.link_id,
				link_type : this.link_type,
				parent_id : this.parent_id
				},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();	
				}else
				{				
					if(action.result.folder_id)
					{
						this.setLinkFolderId(action.result.folder_id);
					}
				}					
			},		
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
		
	}
});