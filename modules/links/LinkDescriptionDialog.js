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
GO.links.LinkDescriptionDialog = function(config){	
	if(!config)
	{
		config={};
	}
	this.buildForm();
	var focusFirstField = function(){
		this.formPanel.items.items[0].focus();
	};
	config.collapsible=true;
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=300;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.links.lang.linkDescription;					
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
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
	GO.links.LinkDescriptionDialog.superclass.constructor.call(this, config);
	this.addEvents({'save' : true});	
}
Ext.extend(GO.links.LinkDescriptionDialog, Ext.Window,{
	show : function (link_description_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		this.formPanel.form.reset();
		if(!link_description_id)
		{
			link_description_id=0;			
		}
		this.setLinkDescriptionId(link_description_id);
		if(this.link_description_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.links.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{					
					GO.links.LinkDescriptionDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
		}else 
		{
			GO.links.LinkDescriptionDialog.superclass.show.call(this);
		}
	},
	setLinkDescriptionId : function(link_description_id)
	{
		this.formPanel.form.baseParams['link_description_id']=link_description_id;
		this.link_description_id=link_description_id;
	},
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.links.url+'action.php',
			params: {'task' : 'save_link_description'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.link_description_id)
				{
					this.setLinkDescriptionId(action.result.link_description_id);
				}				
				this.fireEvent('save', this, this.link_description_id);				
				if(hide)
				{
					this.hide();	
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
	},
	buildForm : function () {
		this.formPanel = new Ext.FormPanel({
			waitMsgTarget:true,
			baseParams: {task: 'link_description'},			
			url: GO.settings.modules.links.url+'action.php',
			cls:'go-form-panel',			
			layout:'form',
			autoHeight:true,
			items:[{
				xtype: 'textfield',
			  name: 'description',
				anchor: '-20',
			  fieldLabel: GO.lang.strDescription
			}]
		});
	}
});
