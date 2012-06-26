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
GO.summary.AnnouncementDialog = function(config){
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
	config.width=700;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.summary.lang.announcement;					
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
	GO.summary.AnnouncementDialog.superclass.constructor.call(this, config);
	this.addEvents({'save' : true});	
}
Ext.extend(GO.summary.AnnouncementDialog, Ext.Window,{
	show : function (announcement_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}

		if(!announcement_id)
		{
			announcement_id=0;			
		}
		this.setAnnouncementId(announcement_id);
		if(this.announcement_id>0)
		{
			this.formPanel.load({
				url : GO.url('summary/announcement/load'),
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					GO.summary.AnnouncementDialog.superclass.show.call(this);
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
			GO.summary.AnnouncementDialog.superclass.show.call(this);
		}
	},
	setAnnouncementId : function(announcement_id)
	{
		this.formPanel.form.baseParams['id']=announcement_id;
		this.announcement_id=announcement_id;
	},
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url: GO.url('summary/announcement/submit'),
			params: {},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				this.fireEvent('save', this);
				if(hide)
				{
					this.hide();	
				}else
				{
					if(action.result.id)
					{
						this.setAnnouncementId(action.result.id);
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
	},
	buildForm : function () {
    this.formPanel = new Ext.form.FormPanel({
	    waitMsgTarget:true,
			url: GO.url('summary/announcement/submit'),
			border: false,
			baseParams: {},				
			cls:'go-form-panel',			
			items:[{
				xtype: 'datefield',
			  name: 'due_time',
				minValue:new Date(),
				anchor: '-20',
				format: GO.settings.date_format,
			  fieldLabel: GO.summary.lang.dueTime
			},{
				xtype: 'textfield',
			  name: 'title',
				anchor: '-20',
			  fieldLabel: GO.summary.lang.title
			},{
				xtype: 'htmleditor',
			  name: 'content',
				anchor: '-20 -60',
			  hideLabel:true
			}]		
		});
	}
});
