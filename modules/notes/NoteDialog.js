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
 
GO.notes.NoteDialog = function(config){	
	if(!config)
	{
		config={};
	}

	config.goDialogId='note';
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	//config.iconCls='go-link-icon-4';
	config.collapsible=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.maximizable=true;
	config.width=600;
	config.height=400;
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
	
	this.addEvents({
		'save' : true
	});
}

Ext.extend(GO.notes.NoteDialog, GO.Window,{
	
	show : function (note_id, config) {

		config = config || {};
		
		//tmpfiles on the server ({name:'Name',tmp_file:/tmp/name.ext} will be attached)
		this.formPanel.baseParams.tmp_files = config.tmp_files ? Ext.encode(config.tmp_files) : '';
				
		if(!this.rendered)
			this.render(Ext.getBody());
		
		if(!note_id)
		{
			note_id=0;			
		}
		
		delete this.link_config;
		this.formPanel.form.reset();	
		
		this.propertiesPanel.show();
			
		this.setNoteId(note_id);
		
		if(this.note_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.notes.url+'json.php',
				success:function(form, action)
				{
					this.selectCategory.setRemoteText(action.result.data.category_name);	
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
			if(!config.category_id)
			{
				config.category_id=GO.notes.defaultCategory.id;
				config.category_name=GO.notes.defaultCategory.name;
			}
			this.selectCategory.setValue(config.category_id);
			if(config.category_name)
			{			
				this.selectCategory.setRemoteText(config.category_name);
			}
			
			this.formPanel.form.setValues(config.values);
			
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
		}
	},

	setNoteId : function(note_id)
	{
		this.formPanel.form.baseParams['note_id']=note_id;
		this.note_id=note_id;
		
		this.selectLinkField.container.up('div.x-form-item').setDisplayed(note_id==0);
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.notes.url+'action.php',
			params: {
				'task' : 'save_note'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				if(action.result.note_id)
				{
					this.setNoteId(action.result.note_id);	
				}
				
				if(hide)
				{
					this.hide();	
				}
				
				this.fireEvent('save', this, this.note_id);
				
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
			anchor:'100%'
		});

		this.propertiesPanel = new Ext.Panel({
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {
				task: 'note'
			},
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',
			waitMsgTarget:true,
			layout:'form',
			items:[{
				xtype: 'textfield',
				name: 'name',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.lang.strName
			},this.selectCategory = new GO.form.ComboBox({
				fieldLabel: GO.notes.lang.category_id,
				hiddenName:'category_id',
				anchor:'100%',
				emptyText:GO.lang.strPleaseSelect,
				store: GO.notes.writableCategoriesStore,
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
			this.selectLinkField,
			{
				xtype: 'textarea',
				name: 'content',
				anchor: '100% -80',
				hideLabel:true
			}]				
		});

		var items  = [this.propertiesPanel];		
		
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
		});
    
		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {
				task: 'note'
			},
			items:this.tabPanel				
		});    
	}
});