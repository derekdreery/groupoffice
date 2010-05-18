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
 
GO.linksTemplate = '<tpl if="links.length">'+
		'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
		//LINK DETAILS
		'<tr>'+
			'<td colspan="4" class="display-panel-heading">'+GO.lang.latestLinks+'</td>'+
		'</tr>'+
		
		'<tr>'+
			'<td style="width:16px" class="display-panel-links-header">&nbsp;</td>'+
			'<td style="width: 100%" class="table_header_links">' + GO.lang['strName'] + '</td>'+
			/*'<td class="table_header_links">' + GO.lang['strType'] + '</td>'+*/
			'<td class="table_header_links" style="white-space:nowrap">' + GO.lang['strMtime'] + '</td>'+
		'</tr>'+	
							
		'<tpl for="links">'+
			'<tr class="display-panel-link">'+
				'<td><div class="go-icon {iconCls}" ext:qtip="{type}"></div></td>'+
				'<td><a href="#link_{[xindex-1]}">{name}</a><tpl if="link_description.length"><br />{link_description}</tpl></td>'+
				'<td style="white-space:nowrap">{mtime}</td>'+
			'</tr>'+
			'<tpl if="description.length">'+
				'<tr class="display-panel-link-description">'+
					'<td>&nbsp;</td>'+
					'<td colspan="3">{description}</td>'+
			'</tr>'+
			'</tpl>'+
		'</tpl>'+	
	'</tpl>';
	
GO.linksTemplateConfig = {};

