/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: NoteDialog.js 2507 2008-07-14 14:05:13Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.notes.NoteDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	//config.iconCls='go-link-icon-4';
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.width=700;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.notes.lang.note;					
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

	
	GO.notes.NoteDialog.superclass.constructor.call(this, config);
	
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.notes.NoteDialog, Ext.Window,{
	
	show : function (note_id, config) {

		if(!this.rendered)
			this.render(Ext.getBody());
		
		this.tabPanel.setActiveTab(0);
		
		
		
		if(!note_id)
		{
			note_id=0;			
		}
			
		this.setNoteId(note_id);
		
		if(this.note_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.notes.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					
					if(GO.files)
					{
						this.fileBrowser.setRootPath(action.result.data.files_path);
						this.fileBrowser.setDisabled(false);
					}					

					this.selectCategory.setRemoteText(action.result.data.category_name);
					this.selectUser.setRemoteText(action.result.data.user_name);
				
					GO.notes.NoteDialog.superclass.show.call(this);
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

			if(GO.files)
			{
				this.fileBrowser.setDisabled(true);
			}

			GO.notes.NoteDialog.superclass.show.call(this);
		}
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(config && config.link_config)
		{
			this.link_config=config.link_config;
			if(config.link_config.type_id)
			{
				this.selectLinkField.setValue(config.link_config.type_id);
				this.selectLinkField.setRemoteText(config.link_config.text);
			}
		}else
		{
			delete this.link_config;
		}
	},

	setNoteId : function(note_id)
	{
		this.formPanel.form.baseParams['note_id']=note_id;
		this.note_id=note_id;
		
		this.linksPanel.loadLinks(note_id, 4);
		
		this.selectLinkField.container.up('div.x-form-item').setDisplayed(note_id==0);
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.notes.url+'action.php',
			params: {'task' : 'save_note'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();	
				}else
				{
				
					if(action.result.note_id)
					{
						this.setNoteId(action.result.note_id);

						if(GO.files && action.result.files_path)
						{
							this.fileBrowser.setRootPath(action.result.files_path);
							this.fileBrowser.setDisabled(false);
						}			
					}
				}
				
				if(this.link_config && this.link_config.callback)
				{					
					this.link_config.callback.call(this);					
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
		
		this.selectLinkField = new GO.form.SelectLink({
			anchor:'-20'
		});

		this.propertiesPanel = new Ext.Panel({
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {task: 'note'},			
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',			
			layout:'form',
			autoScroll:true,
			items:[this.selectLinkField,
				this.selectCategory = new GO.form.ComboBox({
	       	fieldLabel: GO.notes.lang.category_id,
	        hiddenName:'category_id',
	        anchor:'-20',
	        emptyText:GO.lang.strPleaseSelect,
	        store: new GO.data.JsonStore({
					    url: GO.settings.modules.notes.url+ 'json.php',
					    baseParams: {
					    	auth_type:'write',
					    	task: 'categories'
				    	},
			    root: 'results',
			    id: 'id',
			    totalProperty:'total',
			    fields: ['id', 'name', 'user_name'],
			    remoteSort: true
				}),
				pageSize: parseInt(GO.settings.max_rows_list),
        valueField:'id',
        displayField:'name',
        mode: 'remote',
        triggerAction: 'all',
        editable: true,
        selectOnFocus:true,
        forceSelection: true,
        allowBlank: false
    }),
    this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				disabled: !GO.settings.modules['notes']['write_permission'],
				value: GO.settings.user_id,
				anchor: '-20'
			}),{
				xtype: 'textfield',
			  name: 'name',
			  width:300,
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.lang.strName
			},{
				xtype: 'textarea',
			  name: 'content',
				anchor: '-20 -110',
			  allowBlank:true,
			  fieldLabel: GO.notes.lang.content
			}]				
		});

		var items  = [this.propertiesPanel];
		
    
      	
    if(GO.files)
		{
			this.fileBrowser = new GO.files.FileBrowser({
				title: GO.lang.strFiles, 
				treeRootVisible:true, 
				treeCollapsed:true,
				disabled:true
				});
			items.push(this.fileBrowser);
		}
		
    
		
		
		//Create the standard GO linkspanel
		this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
		items.push(this.linksPanel);
		if(GO.customfields && GO.customfields.types["4"])
		{
			for(var i=0;i<GO.customfields.types["4"].panels.length;i++)
			{			  	
				items.push(GO.customfields.types["4"].panels[i]);
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
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {task: 'note'},				
			items:this.tabPanel				
		});
    
    
	}
});