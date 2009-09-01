GO.comments.displayPanelTemplate = 
	'<tpl if="comments.length">'+
			'<table cellpadding="0" cellspacing="0" border="0" class="display-panel">'+			
				'<tr>'+
					'<td class="display-panel-heading" colspan="2">'+GO.comments.lang.fiveLatestComments+' (<a href="#" onclick="GO.comments.browseComments({id}, {link_type});" class="normal-link">'+GO.comments.lang.browseComments+'</a>)</td>'+
				'</tr>'+
				'<tpl for="comments">'+					
					'<tr>'+
						'<td><i>{user_name}</i></td>'+										
						'<td style="text-align:right"><b>{ctime}</b></td>'+
					'</tr>'+
					'<tr>'+
						'<td colspan="2" style="padding-left:5px">{comments}<hr /></td>'+
					'</tr>'+
				'</tpl>'+
			'</table>'+
	'</tpl>';