/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: DisplayPanel.tpl 2276 2008-07-04 12:22:20Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
GO.summary.AnnouncementPanel = function(config)
{
	Ext.apply(this, config);
	this.split=true;
	this.autoScroll=true;
	this.tbar = [
		this.editButton = new Ext.Button({
			iconCls: 'btn-edit', 
			text: GO.lang['cmdEdit'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				GO.summary.announcementDialog.show(this.data.id);
			}, 
			scope: this,
			disabled : true
		})
	];	
	GO.summary.AnnouncementPanel.superclass.constructor.call(this);		
}
Ext.extend(GO.summary.AnnouncementPanel, Ext.Panel,{
	initComponent : function(){
		var template = 
			'<div>'+
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">Information</td>'+
					'</tr>'+
									'<tpl if="user_id.length">'+
						'<tr>'+
							'<td>'+GO.lang.strOwner+':</td><td>{user_id}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="host.length">'+
						'<tr>'+
							'<td>'+GO.summary.lang.host+':</td><td>{host}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="ip.length">'+
						'<tr>'+
							'<td>'+GO.summary.lang.ip+':</td><td>{ip}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="link_id.length">'+
						'<tr>'+
							'<td>'+GO.summary.lang.linkId+':</td><td>{link_id}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="ctime.length">'+
						'<tr>'+
							'<td>'+GO.lang.strCtime+':</td><td>{ctime}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="mtime.length">'+
						'<tr>'+
							'<td>'+GO.lang.strMtime+':</td><td>{mtime}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="name.length">'+
						'<tr>'+
							'<td>'+GO.lang.strName+':</td><td>{name}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="expires.length">'+
						'<tr>'+
							'<td>'+GO.summary.lang.expires+':</td><td>{expires}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="upgrades.length">'+
						'<tr>'+
							'<td>'+GO.summary.lang.upgrades+':</td><td>{upgrades}</td>'+
						'</tr>'+
					'</tpl>'+

					'<tpl if="notified.length">'+
						'<tr>'+
							'<td>'+GO.summary.lang.notified+':</td><td>{notified}</td>'+
						'</tr>'+
					'</tpl>'+

				'</table>';																		
	  var config = {};
		template+='</div>';
		this.template = new Ext.XTemplate(template, config);
		GO.summary.AnnouncementPanel.superclass.initComponent.call(this);
	},
	loadAnnouncement : function(announcement_id)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url: GO.settings.modules.summary.url+'json.php',
			params: {
				task: 'announcement_with_items',
				announcement_id: announcement_id
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
		this.template.overwrite(this.body, data);	
	}
});			
