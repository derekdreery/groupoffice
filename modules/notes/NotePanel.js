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
 
GO.notes.NotePanel = Ext.extend(GO.DisplayPanel,{
	link_type : 4,
	
	loadParams : {task: 'note_with_items'},
	
	idParam : 'note_id',
	
	loadUrl : GO.settings.modules.notes.url+'json.php',

	stateId : 'no-note-panel',

	editGoDialogId : 'note',
	
	editHandler : function(){
		GO.notes.showNoteDialog(this.link_id);		
	},	
		
	initComponent : function(){	
		this.template = 

				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">{name}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>ID:</td>'+
						'<td>{id}</td>'+
					'</tr>'+
					'<tr>'+
						'<td colspan="2">{content}</td>'+
					'</tr>'+									
				'</table>';																		
				
		if(GO.customfields)
		{
			this.template +=GO.customfields.displayPanelTemplate;
		}

		if(GO.tasks)
			this.template +=GO.tasks.TaskTemplate;

		if(GO.calendar)
			this.template += GO.calendar.EventTemplate;


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

		GO.notes.NotePanel.superclass.initComponent.call(this);
	}
});			