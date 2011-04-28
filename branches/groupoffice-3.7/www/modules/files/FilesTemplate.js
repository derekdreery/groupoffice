GO.files.filesTemplate =

		'<tpl if="values.files && values.files.length">'+
		
		'{[this.collapsibleSectionHeader(GO.files.lang.files, "files-"+values.panelId, "files")]}'+

		
		'<table class="display-panel" cellpadding="0" cellspacing="0" border="0" id="files-{panelId}">'+		
		'<tr>'+							
			'<td class="table_header_links" style="width:100%">' + GO.lang['strName'] + '</td>'+							
			'<td class="table_header_links" style="white-space:nowrap">' + GO.lang['strMtime'] + '</td>'+
			'<td class="table_header_links">&nbsp;</td>'+
		'</tr>'+
		'<tpl if="!files.length">'+
			'<tr><td colspan="4">'+GO.lang.strNoItems+'</td></tr>'+
		'</tpl>'+
		'<tpl for="files">'+
			'<tr>'+											
				'<td><a class="go-grid-icon filetype filetype-{extension}" href="#" onclick="'+

				'<tpl if="extension!=\'folder\'">'+
				'GO.linkHandlers[6].call(this, {id});'+
				'</tpl>'+
				'<tpl if="extension==\'folder\'">'+
				'GO.linkHandlers[17].call(this, {id});'+
				//'GO.files.openFolder({[this.panel.data.files_folder_id]}, {id});'+
				'</tpl>'+

				'">{name}</a></td>'+

				'<td style="white-space:nowrap">{mtime}</td>'+

				'<tpl if="extension!=\'folder\'">'+
				'<td style="white-space:nowrap"><a style="display:block" class="go-icon btn-edit" href="#files_{[xindex-1]}">&nbsp;</a></td>'+
				'</tpl>'+
				'<tpl if="extension==\'folder\'">'+
				'<td style="white-space:nowrap"><a style="display:block" class="go-icon btn-files" href="#files_{[xindex-1]}">&nbsp;</a></td>'+
				'</tpl>'+
			'</tr>'+
		'</tpl>'+
		
		'<tr><td colspan="4"><a class="display-panel-browse" href="#browsefiles">'+GO.lang.browse+'</a></td></tr>'+

		'</table>'+
	
'</tpl>';
GO.files.filesTemplateConfig={
	getPath : function(path)
	{
		return path.replace(/\'/g,'\\\'');
	}
};