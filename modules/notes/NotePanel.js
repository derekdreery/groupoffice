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
 
GO.notes.NotePanel = function(config)
{
	Ext.apply(this, config);
	
	this.split=true;
	this.autoScroll=true;
	this.title=GO.notes.lang.note;	
	this.newMenuButton = new GO.NewMenuButton();		
	
	this.tbar = [
		this.editButton = new Ext.Button({
			iconCls: 'btn-edit', 
			text: GO.lang['cmdEdit'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				if(!GO.notes.noteDialog)
				{
					GO.notes.noteDialog = new GO.notes.NoteDialog();
				}
				GO.notes.noteDialog.show(this.data.id);					
			}, 
			scope: this,
			disabled : true
		}),this.linkBrowseButton = new Ext.Button({
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			handler: function(){
				GO.linkBrowser.show({link_id: this.data.id,link_type: "4",folder_id: "0"});				
			},
			disabled : true,
			scope: this
		})];
		
	if(GO.files)
	{
		this.tbar.push(this.fileBrowseButton = new Ext.Button({
			iconCls: 'go-menu-icon-files', 
			cls: 'x-btn-text-icon', 
			text: GO.files.lang.files,
			handler: function(){
				GO.files.openFolder(this.data.files_path);				
			},
			scope: this,
			disabled: true
		}));
	}
	this.tbar.push(this.newMenuButton);	
	
	GO.notes.NotePanel.superclass.constructor.call(this);		
}


Ext.extend(GO.notes.NotePanel, Ext.Panel,{	
	initComponent : function(){	
		var template = 
			'<div>'+
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">{name}</td>'+
					'</tr>'+

					'<tpl if="content.length">'+
						'<tr>'+
							'<td colspan="2">{content}</td>'+
						'</tr>'+
					'</tpl>'+
									
				'</table>';																		
				
				template += GO.linksTemplate;
												
				if(GO.customfields)
				{
					template +=GO.customfields.displayPanelTemplate;
				}
				
				
	    	
	  var config = {};		
				
		if(GO.files)
		{
			Ext.apply(config, GO.files.filesTemplateConfig);
			template += GO.files.filesTemplate;
		}
		Ext.apply(config, GO.linksTemplateConfig);
		
		if(GO.comments)
		{
			template += GO.comments.displayPanelTemplate;
		}		
				
		template+='</div>';
		
		this.template = new Ext.XTemplate(template, config);
		
		GO.notes.NotePanel.superclass.initComponent.call(this);
	},
	
	loadNote : function(note_id)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url: GO.settings.modules.notes.url+'json.php',
			params: {
				task: 'note_with_items',
				note_id: note_id
			},
			callback: function(options, success, response)
			{
				this.body.unmask();
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{
					var responseParams = Ext.decode(response.responseText);
					this.setData(responseParams.data);
				}				
			},
			scope: this			
		});
		
	},
	
	setData : function(data)
	{
		data.link_type=4;
		this.data=data;
		this.editButton.setDisabled(!data.write_permission);
		this.linkBrowseButton.setDisabled(false);
		if(GO.files)
		{
			this.fileBrowseButton.setDisabled(false);
		}
		
		if(data.write_permission)
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:4,
				text: this.data.name,
				callback:function(){
					this.loadNote(this.data.id);				
				},
				scope:this
			});
		
		this.template.overwrite(this.body, data);	
	}	
});			