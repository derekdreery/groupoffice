/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: ProjectDialog.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.projects.ProjectDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	//Create the standard GO linkspanel
	this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
	
	if(GO.files)
	{
		this.fileBrowser = new GO.files.FileBrowser({
			title: GO.lang.strFiles, 
			treeRootVisible:true, 
			treeCollapsed:true,
			disabled:true
			});
	}
	this.buildForm();
	
	var focusName = function(){
		this.nameField.focus();		
	};
	
	
		
	
	
	config.iconCls='go-module-icon-projects';
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=700;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.projects.lang.project;					
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

	
	GO.projects.ProjectDialog.superclass.constructor.call(this, config);
	
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.projects.ProjectDialog, Ext.Window,{

	
	show : function (config) {
		
		//this.maximize();
		
		if(!this.rendered)
			this.render(Ext.getBody());
		

		
		if(!config)
		{
			config={};
		}
		
		this.propertiesPanel.show();
		
		
		
		if(!config.project_id)
		{
			config.project_id=0;			
		}
			
		this.setProjectId(config.project_id);
		
		if(config.project_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.projects.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					this.setValues(config.values);
					if(GO.files)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}					
					this.setWritePermission(action.result.data.write_permission);
					
					this.readPermissionsTab.setAcl(action.result.data.acl_read);
					this.writePermissionsTab.setAcl(action.result.data.acl_write);					
					this.bookPermissionsTab.setAcl(action.result.data.acl_book);
					
					GO.projects.ProjectDialog.superclass.show.call(this);
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
			this.linksPanel.setDisabled(true);			
			
			this.setWritePermission(true);
			
			if(GO.files)
			{
				this.fileBrowser.setDisabled(true);
			}
			
			
			this.setValues(config.values);
			GO.projects.ProjectDialog.superclass.show.call(this);
		}
	},
	
	setWritePermission : function(writePermission)
	{
		this.buttons[0].setDisabled(!writePermission);
		this.buttons[1].setDisabled(!writePermission);
		this.linksPanel.setWritePermission(writePermission);
	},
	
	setValues : function(values)
	{
		if(values)
		{
			for(var key in values)
			{
				var field = this.formPanel.form.findField(key);
				if(field)
				{
					field.setValue(values[key]);
				}
			}
		}
		
	},
	setProjectId : function(project_id)
	{
		this.formPanel.form.baseParams['project_id']=project_id;
		this.project_id=project_id;
		this.linksPanel.loadLinks(project_id, 5);
		//this.hoursPanel.setProjectId(project_id);
		this.msPanel.setProjectId(project_id);
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.projects.url+'action.php',
			params: {'task' : 'save_project'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();	
				}else
				{
				
					if(action.result.project_id)
					{
						this.setProjectId(action.result.project_id);
						
						if(GO.files && action.result.files_path)
						{
							this.fileBrowser.setRootPath(action.result.files_path);
							this.fileBrowser.setDisabled(false);
						}					
						
						this.readPermissionsTab.setAcl(action.result.acl_read);
						this.writePermissionsTab.setAcl(action.result.acl_write);					
						this.bookPermissionsTab.setAcl(action.result.acl_book);
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
		
		
		items = [
		this.nameField = new Ext.form.TextField({
        name: 'name',
      	anchor: '100%',
        allowBlank:false,
        fieldLabel: GO.lang.strSubject
  	}) ];

    //var selectLinkField = new GO.form.SelectLink();
    
    if(GO.addressbook)
    {
    	var customerField = new GO.addressbook.SelectCompany({
    		anchor:'100%',
    		fieldLabel: GO.lang.customer,
    		name: 'customer'
    	});
    	items.push(customerField);
    }

    var description = new Ext.form.TextArea({        
        name: 'description',
      	anchor: '100%',
      	height:100,
        allowBlank:true,
        fieldLabel: GO.lang.strDescription
    	});
    	   	
    items.push(description);
		    
		var archiveCB = new Ext.form.Checkbox({
			boxLabel:GO.projects.lang.archiveProject,		  
		  name:'archived',
		  checked:false,
		  width:'auto',
		  labelSeparator: '',
			hideLabel: true
		});
    items.push(archiveCB);

		this.propertiesPanel = new Ext.Panel({
			url: GO.settings.modules.projects.url+'action.php',
			border: false,
			baseParams: {task: 'project'},			
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',			
			layout:'form',
			autoScroll:true,
			items:items
				
		});
		
		/*this.hoursPanel = new GO.projects.HoursGrid({
			title: 'Time',
			layout: 'fit'
		});*/
		
		this.msPanel = new GO.projects.MSGrid({title: GO.projects.lang.milestones});
		
		this.readPermissionsTab = new GO.grid.PermissionsPanel({
			title: GO.lang['strReadPermissions']
		});
	
		this.writePermissionsTab = new GO.grid.PermissionsPanel({
			title: GO.lang['strWritePermissions']
		});
		
		this.bookPermissionsTab = new GO.grid.PermissionsPanel({
			title: GO.projects.lang.bookPermission
		});
		
		var items  = [
      	this.propertiesPanel,
      	this.msPanel,
      	//this.hoursPanel,
      	this.linksPanel];
      	
    if(GO.files)
		{
			items.push(this.fileBrowser);
		}
      	
    items.push(this.readPermissionsTab);
    items.push(this.writePermissionsTab);
    items.push(this.bookPermissionsTab);
      
		
		
		if(GO.customfields && GO.customfields.types["5"])
		{
			for(var i=0;i<GO.customfields.types["5"].panels.length;i++)
			{			  	
				items.push(GO.customfields.types["5"].panels[i]);
			}
		}
 
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
    	border: false,
      items: items,
      anchor: '100% 100%'
    }) ;    
    
    
    this.formPanel = new Ext.form.FormPanel({
			url: GO.settings.modules.projects.url+'action.php',
			border: false,
			baseParams: {task: 'project'},				
			items:this.tabPanel				
		});
    
    
	}
});