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
 
GO.tasks.TaskPanel = Ext.extend(GO.DisplayPanel,{
	link_type : 12,
	
	loadParams : {task: 'task_with_items'},
	
	idParam : 'task_id',
	
	loadUrl : GO.settings.modules.tasks.url+'json.php',
	
	editHandler : function(){
		this.addSaveHandler(GO.tasks.taskDialog);
		GO.tasks.taskDialog.show({task_id: this.data.id});
	},	
	
	initComponent : function(){
	
		this.template = 
			'<div>'+
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">{name}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.tasks.lang.startsAt+':</td>'+
						'<td>{start_date}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.tasks.lang.dueAt+':</td>'+
						'<td>{due_date}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.lang.strStatus+':</td>'+
						'<td>{status_text}</td>'+
					'</tr>'+
					'<tpl if="this.notEmpty(description)">'+
						'<tr>'+
							'<td colspan="2" class="display-panel-heading">'+GO.lang.strDescription+'</td>'+
						'</tr>'+
						'<tr>'+
							'<td colspan="2">{description}</td>'+
						'</tr>'+
					'</tpl>'+
									
				'</table>';																		
				
		this.template += GO.linksTemplate;
												
				
		if(GO.files)
		{
			Ext.apply(this.templateConfig, GO.files.filesTemplateConfig);
			this.template += GO.files.filesTemplate;
		}
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);
		
		
		if(GO.comments)
		{
			this.template += GO.comments.displayPanelTemplate;
		}
		
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
	}	
});			