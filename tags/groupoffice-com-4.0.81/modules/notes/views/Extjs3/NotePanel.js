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
	model_name : "GO_Notes_Model_Note",
	
	stateId : 'no-note-panel',

	//deprecated. tabbedformdialog refreshes active displaypanel automatically.
	editGoDialogId : 'note',
	
	editHandler : function(){
		GO.notes.showNoteDialog(this.model_id);		
	},	
		
	initComponent : function(){	
		
		this.loadUrl=('notes/note/display');
		
		this.id=Ext.id();
		
		this.template = 

				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">'+GO.notes.lang.note+': {name}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>ID:</td>'+
						'<td>{id}</td>'+
					'</tr>'+
					'<tr>'+
						'<tpl if="GO.util.empty(encrypted)">'+
							'<td colspan="2">{content}</td>'+
						'</tpl>'+
						'<tpl if="!GO.util.empty(encrypted)">'+
							'<td colspan="2"><div id="encryptedNoteDisplaySecure'+this.id+'"></div></td>'+
						'</tpl>'+
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
		
		if(GO.workflow)
			this.template +=GO.workflow.WorkflowTemplate;

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
	},
	
	afterLoad : function(result) {
		if(this.data.encrypted){
			if (!this.passwordPanel){
				this.passwordPanel = new Ext.Panel({			
					renderTo: 'encryptedNoteDisplaySecure'+this.id,
					layout: 'column',
					border: false,
					keys:[{
						key: Ext.EventObject.ENTER,
						fn : function() {
							this._loadWithPassword();
						},
						scope : this
					}],
					items: [
						this.passwordField = new Ext.form.TextField({
							name: 'password',
		//						emptyText: GO.lang['password']+' '+GO.lang['decryptContent'],
							inputType: 'password',
							width: '60%'
						}),
						this.passwordButton = new Ext.Button({
								text: GO.lang['decryptContent'],
								handler: function(){
									this._loadWithPassword();									
								},
								scope: this
							})
					]
				});
			}else
			{
				var el = Ext.get('encryptedNoteDisplaySecure'+this.id);
				//console.log(el);
				el.appendChild(this.passwordPanel.getEl());
			}
		}
	},
	
	_loadWithPassword : function() {
		
		var pass = this.passwordField.getValue();
		this.passwordField.setValue("");
		
		GO.request({
			url: 'notes/note/display',
			params: {
				'id' : this.model_id,
				'userInputPassword' : pass
			},
			success: function(options, response, result) {
				if (!GO.util.empty(result.feedback))
					Ext.MessageBox.alert('', result.feedback);
				if (GO.util.empty(result.data.encrypted)) {
					document.getElementById('encryptedNoteDisplaySecure'+this.id).innerHTML = result.data.content;
				}
			},
			scope: this
		});
	}
});			