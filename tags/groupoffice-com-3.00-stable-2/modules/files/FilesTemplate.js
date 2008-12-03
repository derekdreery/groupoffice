GO.files.filesTemplate = '<tpl if="files.length">'+

		'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
		//LINK DETAILS
		'<tr>'+
			'<td colspan="4" class="display-panel-heading">'+GO.files.lang.files+'</td>'+
		'</tr>'+
		
		'<tr>'+							
			'<td class="table_header_links">' + GO.lang['strName'] + '</a></td>'+							
			'<td class="table_header_links">' + GO.lang['strMtime'] + '</td>'+
		'</tr>'+	
							
		'<tpl for="files">'+
			'<tr>'+
				'<tpl if="values.extension==\'folder\'">'+										
					'<td><a href="#" onclick="GO.files.openFolder(\'{[this.getPath(values.path)]}\');">{grid_display}</a></td>'+
				'</tpl>'+							
				'<tpl if="values.extension!=\'folder\'">'+										
					'<td><a href="#" onclick="GO.files.openFile(\'{[this.getPath(values.path)]}\');">{grid_display}</a></td>'+
				'</tpl>'+
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