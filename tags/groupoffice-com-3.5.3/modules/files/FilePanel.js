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

	/*
	 *Can be filled by other modules to display extra info
	 */
	extraTemplateProperties : '',

	editHandler : function(){
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
	},

	createTopToolbar : function(){
		var tbar = GO.files.FilePanel.superclass.createTopToolbar.call(this);

		tbar.splice(1,0,{
			iconCls: 'btn-settings',
			text: GO.lang.strProperties,
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.files.showFilePropertiesDialog(this.link_id+"");
				this.addSaveHandler(GO.files.filePropertiesDialog);
			},
			scope: this
		});

		return tbar;
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
						'<td>{type}</td>'+
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

		this.template += GO.linksTemplate;

		if(GO.customfields)
		{
			this.template +=GO.customfields.displayPanelTemplate;
		}
		
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);

		if(GO.comments)
		{
			this.template += GO.comments.displayPanelTemplate;
		}

		GO.files.FilePanel.superclass.initTemplate.call(this);
	}
});