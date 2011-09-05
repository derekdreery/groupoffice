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
	link_type : 6,

	loadParams : {task: 'file_with_items'},

	idParam : 'file_id',

	loadUrl : GO.settings.modules.files.url+'json.php',

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
			if(GO.settings.modules.gota && GO.settings.modules.gota.read_permission)
			{
				if(!deployJava.isWebStartInstalled('1.6.0'))
				{
					Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);
					window.location.href=GO.settings.modules.files.url+'download.php?mode=download&id='+this.link_id;
				}else
				{
					window.location.href=GO.settings.modules.gota.url+'jnlp.php?id='+this.link_id;
				}
			}else
			{
				window.location.href=GO.settings.modules.files.url+'download.php?mode=download&id='+this.link_id;
			}
		}
	},

	createTopToolbar : function(){
		var tbar = GO.files.FilePanel.superclass.createTopToolbar.call(this);

		tbar.splice(1,0,{
			iconCls: 'btn-settings',
			text: GO.lang.strProperties,
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.files.showFilePropertiesDialog(this.link_id+"");
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

	initTemplate : function(){
		this.template =

				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td>'+GO.lang.strLocation+':</td>'+
						'<td>{location}</td>'+
					'</tr>'+
					'<tr>'+
						'<td>'+GO.lang.strType+':</td>'+
						'<td colspan=>{type}</td>'+						
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
            '<td>{download_link}</td>'+
						'</tr>'+
	
					
          '</tpl>'+

//          '<tpl if="GO.util.empty(expire_time)"><tr>'+
//            '<td colspan="2">'+GO.files.lang.strDownloadInactive+'</td>'+
////            '<td>{expire_time} (<a href="{download_link}">'+GO.files.lang.strClickHere+'</a>)</td>'+
//          '</tpl>'+

					'<tpl if="!GO.util.empty(image_src)"><tr><td colspan="2">'+
						'<img style="cursor:pointer;" src="{image_src}" onclick="javascript:GO.files.openFile(new Ext.data.Record({\'name\':\'{image_name}\',\'path\':\'{image_path}\',\'extension\':\'{image_extension}\',\'download_path\':\'{download_path}\'}));" />'+
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

		GO.files.FilePanel.superclass.initTemplate.call(this);
	}
});