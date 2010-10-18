GO.tasks.TaskTemplate =
		'{[this.collapsibleSectionHeader(GO.tasks.lang.incompleteTasks, "tasks-"+values.panelId, "tasks")]}'+
		'<tpl if="values.tasks">'+
			'<table class="display-panel" cellpadding="0" cellspacing="0" border="0" id="tasks-{panelId}">'+
			'<tr>'+
				'<td class="table_header_links">' + GO.lang['strName'] + '</a></td>'+
				'<td class="table_header_links">' + GO.tasks.lang.dueDate + '</td>'+
				'<td class="table_header_links">' + GO.tasks.lang.tasklist + '</td>'+
			'</tr>'+
			'<tpl if="!tasks.length">'+
				'<tr><td colspan="4">'+GO.lang.strNoItems+'</td></tr>'+
			'</tpl>'+
			'<tpl for="tasks">'+
				'<tr class="display-panel-link">'+
					'<td><a href="#" onclick="GO.linkHandlers[12].call(this, {id});" <tpl if="completion_time!=\'\'">class="tasks-completed"</tpl>>{name}</a></td>'+
					'<td>{due_time}</td>'+
					'<td>{tasklist_name}</td>'+
				'</tr>'+				
			'</tpl>'+
			'</table>'+
		'</tpl>';