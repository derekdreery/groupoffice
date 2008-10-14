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
 
GO.{module}.{friendly_single_ucfirst}Panel = function(config)
{
	Ext.apply(this, config);
	
	this.split=true;
	this.autoScroll=true;	
	
	<gotpl if="$link_type &gt; 0">
	this.newMenuButton = new GO.NewMenuButton();
	</gotpl>
		
	this.tbar = [
		this.editButton = new Ext.Button({
			iconCls: 'btn-edit', 
			text: GO.lang['cmdEdit'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				GO.{module}.{friendly_single}Dialog.show(this.data.id);
			}, 
			scope: this,
			disabled : true
		})<gotpl if="$link_type &gt; 0">,this.linkBrowseButton = new Ext.Button({
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			handler: function(){
				GO.linkBrowser.show({link_id: this.data.id,link_type: "{link_type}",folder_id: "0"});				
			},
			scope: this
		})</gotpl>];
	<gotpl if="$authenticate">if(GO.files)
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
	}</gotpl><gotpl if="$link_type &gt; 0">this.tbar.push(this.newMenuButton);</gotpl>	
	GO.{module}.{friendly_single_ucfirst}Panel.superclass.constructor.call(this);		
}


Ext.extend(GO.{module}.{friendly_single_ucfirst}Panel, Ext.Panel,{
	
	initComponent : function(){
	
		var template = 
			'<div>'+
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">Information</td>'+
					'</tr>'+

				{DISPLAYFIELDS}
									
				'</table>';																		
				<gotpl if="$link_type &gt; 0">
				template += GO.linksTemplate;
												
				if(GO.customfields)
				{
					template +=GO.customfields.displayPanelTemplate;
				}</gotpl>
	    	
	  var config = {};
		
		<gotpl if="$files">		
		if(GO.files)
		{
			Ext.apply(config, GO.files.filesTemplateConfig);
			template += GO.files.filesTemplate;
		}
		Ext.apply(config, GO.linksTemplateConfig);
		</gotpl>
				
		template+='</div>';
		
		this.template = new Ext.XTemplate(template, config);
		
		GO.{module}.{friendly_single_ucfirst}Panel.superclass.initComponent.call(this);
	},
	
	load{friendly_single_ucfirst} : function({friendly_single}_id)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url: GO.settings.modules.{module}.url+'json.php',
			params: {
				task: '{friendly_single}_with_items',
				{friendly_single}_id: {friendly_single}_id
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
		<gotpl if="$link_type &gt; 0">data.link_type={link_type};
		this.linkBrowseButton.setDisabled(false);</gotpl>
		
		<gotpl if="$link_type &gt; 0">if(data.write_permission)
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:{link_type},
				text: this.data.name,
				callback:function(){
					this.load{friendly_single_ucfirst}(this.data.id);				
				},
				scope:this
			});</gotpl>
		
		this.template.overwrite(this.body, data);	
	}
	
});			