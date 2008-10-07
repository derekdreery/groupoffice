/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: TaskPanel.js 2276 2008-07-04 12:22:20Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.tasks.TaskPanel = function(config)
{
	Ext.apply(this, config);

	this.autoScroll=true;
	//this.title=GO.tasks.lang.task;	
	
	this.newMenuButton = new GO.NewMenuButton();		
	
	this.tbar = [
		this.editButton = new Ext.Button({
			iconCls: 'btn-edit', 
			text: GO.lang['cmdEdit'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				if(!GO.tasks.taskDialog)
				{
					GO.tasks.taskDialog = new GO.tasks.TaskDialog();
				}
				GO.tasks.taskDialog.show(this.data.id);					
			}, 
			scope: this,
			disabled : true
		}),this.linkBrowseButton = new Ext.Button({
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			handler: function(){
				GO.linkBrowser.show({link_id: this.data.id,link_type: "12",folder_id: "0"});				
			},
			scope: this
		}),		
		this.newMenuButton
	];	
	
	GO.tasks.TaskPanel.superclass.constructor.call(this);		
}


Ext.extend(GO.tasks.TaskPanel, Ext.Panel,{
	
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
		
				
		template+='</div>';
		
		this.template = new Ext.XTemplate(template, config);
		
		GO.tasks.TaskPanel.superclass.initComponent.call(this);
	},
	
	loadTask : function(task_id)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url: GO.settings.modules.tasks.url+'json.php',
			params: {
				task: 'task_with_items',
				task_id: task_id
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
		this.data=data;
		this.editButton.setDisabled(!data.write_permission);
		this.linkBrowseButton.setDisabled(false);
		
		if(data.write_permission)
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:12,
				text: this.data.name,
				callback:function(){
					this.loadTask(this.data.id);				
				},
				scope:this
			});
		
		this.template.overwrite(this.body, data);	
	}
	
});			