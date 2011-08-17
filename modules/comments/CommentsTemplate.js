GO.comments.displayPanelTemplate =
	//'{[this.collapsibleSectionHeader(GO.comments.lang.recentComments+\' (<a href="#" onclick="GO.comments.browseComments(\'+values.id+\', \'+values.link_type+\');" class="normal-link">\'+GO.comments.lang.browseComments+\'</a>)\', "comments-"+values.panelId, "comments")]}'+
'<tpl if="values.comments && values.comments.length">'+
'{[this.collapsibleSectionHeader(GO.comments.lang.recentComments, "comments-"+values.panelId, "comments")]}'+
	
			'<table cellpadding="0" cellspacing="0" border="0" class="display-panel" id="comments-{panelId}">'+
				'<tpl if="!comments.length">'+
					'<tr><td colspan="3">'+GO.lang.strNoItems+'</td></tr>'+
				'</tpl>'+
				'<tpl for="comments">'+					
					'<tr>'+
						'<td><i>{user_name}</i></td>'+										
						'<td style="text-align:right"><b>{ctime}</b></td>'+
					'</tr>'+
					'<tr>'+
						'<td colspan="2" style="padding-left:5px">{comments}<hr /></td>'+
					'</tr>'+
				'</tpl>'+

				'<tr><td colspan="4"><a class="display-panel-browse" href="#" onclick="GO.comments.browseComments({id}, {link_type});">'+GO.lang.browse+'</a></td></tr>'+

			'</table>'+
	'</tpl>';