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

GO.files.FilePanel = Ext.extend(GO.DisplayPanel,{
	model_name : "GO_Files_Model_File",

	noFileBrowser:true,

	stateId : 'fs-file-panel',

	/*
	 *Can be filled by other modules to display extra info
	 */
	extraTemplateProperties : '',

	editGoDialogId : 'file',

	editHandler : function(){

		//browsers don't like loading a json request and download dialog at the same time.'
		if(this.loading)
		{
			this.editHandler.defer(200, this);
		}else
		{				
			GO.files.editFile(this.model_id);
			
		}
	},

	createTopToolbar : function(){
		var tbar = GO.files.FilePanel.superclass.createTopToolbar.call(this);

		tbar.splice(1,0,{
			iconCls: 'btn-save',
			text: GO.lang.download,
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.files.downloadFile(this.model_id);
			},
			scope: this
		},{
			iconCls: 'btn-settings',
			text: GO.lang.strProperties,
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.files.showFilePropertiesDialog(this.model_id+"");
				//this.addSaveHandler(GO.files.filePropertiesDialog);
			},
			scope: this
		});

		return tbar;
	},

	reset : function(){
		GO.files.FilePanel.superclass.reset.call(this);
		this.setTitle('&nbsp;');
	},

	setData : function(data)
	{
		this.setTitle(data.name);
		GO.files.FilePanel.superclass.setData.call(this, data);
	},

	initComponent : function(){
		
		this.loadUrl=GO.url('files/file/display');
		
		this.template =

				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td width="120">'+GO.files.lang.path+':</td>'+
						'<td>{path}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.lang.strType+':</td>'+
						'<td colspan=><div class="go-grid-icon filetype filetype-{extension}">{type}</div></td>'+						
					'</tr>'+

					'<tr>'+
						'<td>'+GO.lang.strSize+':</td>'+
						'<td>{size}</td>'+
						
					'</tr>'+

					'<tr>'+
						'<td>'+GO.lang.strCtime+':</td>'+
						'<td>{ctime}</td>'+
						
					'</tr>'+

					'<tr>'+
						'<td>'+GO.lang.strMtime+':</td>'+
						'<td>{mtime}</td>'+						
					'</tr>'+

          '<tpl if="!GO.util.empty(expire_time)">'+
						'<tr>'+
							'<td colspan="2" class="display-panel-heading">'+GO.files.lang.strDownloadActive+'</td>'+
						'</tr>'+
						'<tr>'+
            '<td style="white-space:nowrap">'+GO.files.lang.downloadExpireTime+':</td>'+
            '<td>{expire_time}</td>'+
						'</tr>'+
						
						'<tr>'+
            '<td>'+GO.files.lang.downloadUrl+':</td>'+
            '<td><a href="{download_link}" target="_blank">{download_link}</a></td>'+
						'</tr>'+
	
					
          '</tpl>'+

					'<tpl if="!GO.util.empty(thumbnail_url)"><tr><td colspan="2">'+
						'<img src="{thumbnail_url}" />'+
					'</td></tr></tpl>'+

					this.extraTemplateProperties +

					/*'<tr>'+
						'<td>'+GO.lang.Atime+'</td>'+
						'<td>{atime}</td>'+
					'</tr>'+*/

					'<tpl if="!GO.util.empty(comment)">'+
						'<tr>'+
							'<td colspan="2" class="display-panel-heading">'+GO.files.lang.comments+'</td>'+
						'</tr>'+
						'<tr>'+
							'<td colspan="2">{comment}</td>'+
						'</tr>'+
					'</tpl>'+
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
		
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);

		if(GO.comments)
		{
			this.template += GO.comments.displayPanelTemplate;
		}

		GO.files.FilePanel.superclass.initComponent.call(this);
	}
});