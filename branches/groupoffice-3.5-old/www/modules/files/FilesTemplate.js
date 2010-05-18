GO.files.filesTemplate = '<tpl if="files.length">'+

		'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
		//LINK DETAILS
		'<tr>'+
			'<td colspan="4" class="display-panel-heading">'+GO.files.lang.files+'</td>'+
		'</tr>'+
		
		'<tr>'+							
			'<td class="table_header_links" style="width:100%">' + GO.lang['strName'] + '</td>'+							
			'<td class="table_header_links" style="white-space:nowrap">' + GO.lang['strMtime'] + '</td>'+
		'</tr>'+	
							
		'<tpl for="files">'+
			'<tr>'+											
				'<td><a class="go-grid-icon filetype filetype-{extension}" href="#files_{[xindex-1]}">{name}</a></td>'+
				'<td style="white-space:nowrap">{mtime}</td>'+
			'</tr>'+
		'</tpl>'+
	
'</tpl>';
GO.files.filesTemplateConfig={
	getPath : function(path)
	{
		return path.replace(/\'/g,'\\\'');
	}
};