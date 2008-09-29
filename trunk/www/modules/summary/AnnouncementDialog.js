/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: Dialog.tpl 2276 2008-07-04 12:22:20Z mschering $
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
		this.propertiesPanel.items.items[0].focus();
	};
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
		this.tabPanel.setActiveTab(0);
		if(!announcement_id)
		{
			announcement_id=0;			
		}
		this.setAnnouncementId(announcement_id);
		if(this.announcement_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.summary.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					this.selectUser.setRemoteText(action.result.data.user_name);
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
		this.formPanel.form.baseParams['announcement_id']=announcement_id;
		this.announcement_id=announcement_id;
	},
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.summary.url+'action.php',
			params: {'task' : 'save_announcement'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				this.fireEvent('save', this);
				if(hide)
				{
					this.hide();	
				}else
				{
					if(action.result.announcement_id)
					{
						this.setAnnouncementId(action.result.announcement_id);
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
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',			
			layout:'form',
			autoScroll:true,
			items:[this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				disabled: !GO.settings.modules['summary']['write_permission'],
				value: GO.settings.user_id,
				anchor: '-20'
			})
,{
				xtype: 'textfield',
			  name: 'host',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.summary.lang.host
			}
,{
				xtype: 'textfield',
			  name: 'ip',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.summary.lang.ip
			}
,{
				xtype: 'textfield',
			  name: 'link_id',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.summary.lang.linkId
			}
,{
				xtype: 'textfield',
			  name: 'name',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.lang.strName
			}
,{
				xtype: 'textfield',
			  name: 'expires',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.summary.lang.expires
			}
,{
				xtype: 'textfield',
			  name: 'upgrades',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.summary.lang.upgrades
			}
,{
				xtype: 'textfield',
			  name: 'notified',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.summary.lang.notified
			}
]
		});
		var items  = [this.propertiesPanel];
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
    	border: false,
      items: items,
      anchor: '100% 100%'
    }) ;    
    this.formPanel = new Ext.form.FormPanel({
	    waitMsgTarget:true,
			url: GO.settings.modules.summary.url+'action.php',
			border: false,
			baseParams: {task: 'announcement'},				
			items:this.tabPanel				
		});
	}
});
