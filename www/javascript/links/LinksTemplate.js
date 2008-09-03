/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: LinksTemplate.js 2276 2008-07-04 12:22:20Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.linksTemplate = '<tpl if="links.length">'+
		'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
		//LINK DETAILS
		'<tr>'+
			'<td colspan="4" class="display-panel-heading">'+GO.lang.latestLinks+'</td>'+
		'</tr>'+
		
		'<tr>'+
			'<td width="16" class="display-panel-links-header">&nbsp;</td>'+
			'<td class="table_header_links">' + GO.lang['strName'] + '</td>'+
			'<td class="table_header_links">' + GO.lang['strType'] + '</td>'+
			'<td class="table_header_links">' + GO.lang['strMtime'] + '</td>'+
		'</tr>'+	
							
		'<tpl for="links">'+
			'<tr>'+
				'<td><div class="go-icon {iconCls}"></div></td>'+
				'<td><a href="#" onclick="{[this.openLink(values)]}">{name}</a></td>'+
				'<td>{type}</td>'+
				'<td>{mtime}</td>'+
			'</tr>'+
			'<tpl if="description.length">'+
				'<tr class="display-panel-link-description">'+
					'<td>&nbsp;</td>'+
					'<td colspan="3">{description}</td>'+
			'</tr>'+
			'</tpl>'+
		'</tpl>'+	
	'</tpl>';
	
GO.linksTemplateConfig = {
	
	openLink : function(values)
	{
		if(values.link_type=='folder')
		{
			return "GO.linkBrowser.show({link_id: "+values.parent_link_id+",link_type: "+values.parent_link_type+",folder_id: "+values.id+"});";
		}else
		{
			return "GO.linkHandlers["+values.link_type+"].call(this, "+values.id+");";
		}
	}
	
};

