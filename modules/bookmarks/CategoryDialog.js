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
 * @author Twan Verhofstad
 */


GO.bookmarks.CategoryDialog = function(config){
	
	
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
	config.width=500;
	config.height=400;
	config.closeAction='hide';
	config.title= GO.bookmarks.lang.category;
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

	
	GO.bookmarks.CategoryDialog.superclass.constructor.call(this, config);
	
	this.addEvents({
		'save' : true
	});
}

Ext.extend(GO.bookmarks.CategoryDialog, Ext.Window,{
	
	show : function (category_id) {

		if(!this.rendered)
			this.render(Ext.getBody());

		
		this.tabPanel.setActiveTab(0);
		
		
		
		if(!category_id)
		{
			category_id=0;			
		}
			
		this.setCategoryId(category_id);

	//	console.log(category_id);

		if(this.category_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.bookmarks.url+'json.php',
				'task' : 'category',

				success:function(form, action)
				{					
					//	this.setWritePermission(action.result.data.write_permission);
					this.readPermissionsTab.setAcl(action.result.data.acl_id);
					
					this.selectUser.setRemoteText(action.result.data.user_name);
					
					GO.bookmarks.CategoryDialog.superclass.show.call(this);
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
			this.setWritePermission(true);
			
			GO.bookmarks.CategoryDialog.superclass.show.call(this);
		}
	},
	
	
	setWritePermission : function(writePermission)
	{
		this.buttons[0].setDisabled(!writePermission);
		this.buttons[1].setDisabled(!writePermission);
	},

	setCategoryId : function(category_id)
	{
		this.formPanel.form.baseParams['category_id']=category_id;
		this.category_id=category_id;
		
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.bookmarks.url+'action.php',
			params: {
				'task' : 'save_category'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){				
				this.fireEvent('save', this);				
				if(hide)
				{
					this.hide();	
				}else
				{				
					if(action.result.category_id)					
						this.setCategoryId(action.result.category_id);

					if(typeof(action.result.acl_id)!='undefined')
						this.readPermissionsTab.setAcl(action.result.acl_id);
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

		var items=[this.selectUser = new GO.form.SelectUser({
			fieldLabel: GO.lang['strUser'],
			disabled: !GO.settings.modules['bookmarks']['write_permission'],
			value: GO.settings.user_id,
			anchor: '100%'
		}),{
			xtype: 'textfield',
			name: 'name',
			anchor: '100%',
			allowBlank:false,
			fieldLabel: GO.lang.strName
		}
		]

		if (GO.settings.modules.bookmarks.write_permission) items.push({ // niet 1 maar 'admin-rights''
			name: 'public',
			xtype: 'checkbox',
			boxLabel: GO.bookmarks.lang.sharedCategory,
			hideLabel:true
		});

		this.propertiesPanel = new Ext.Panel({
			url: GO.settings.modules.bookmarks.url+'json.php',
			border: false,
			baseParams: {
				task: 'category'
			},
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',
			waitMsgTarget:true,
			layout:'form',
			autoScroll:true,			
			items: items				
		});

		var items  = [this.propertiesPanel];
		
		this.readPermissionsTab = new GO.grid.PermissionsPanel({
			});

		items.push(this.readPermissionsTab);
 
		this.tabPanel = new Ext.TabPanel({
			activeTab: 0,
			deferredRender: false,
			border: false,
			items: items,
			anchor: '100% 100%'
		}) ;    
    
		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,
			url: GO.settings.modules.bookmarks.url+'json.php',
			border: false,
			baseParams: {
				task: 'category'
			},
			items:this.tabPanel				
		});
	}
});