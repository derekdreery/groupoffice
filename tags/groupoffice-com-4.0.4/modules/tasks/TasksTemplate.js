GO.tasks.TaskTemplate =
		'<tpl if="values.tasks && values.tasks.length">'+
		'{[this.collapsibleSectionHeader(GO.tasks.lang.tasks, "tasks-"+values.panelId, "tasks")]}'+
		
			'<table class="display-panel" cellpadding="0" cellspacing="0" border="0" id="tasks-{panelId}">'+
			'<tr>'+
				'<td class="table_header_links" width="16px;"></td>'+
				'<td class="table_header_links" width="10px;"></td>'+
				'<td class="table_header_links">' + GO.lang['strName'] + '</a></td>'+
				'<td class="table_header_links" width="110px">' + GO.tasks.lang.status + '</td>'+
				'<td class="table_header_links" width="110px">' + GO.tasks.lang.dueDate + '</td>'+
				'<td class="table_header_links" width="120px">' + GO.tasks.lang.tasklist + '</td>'+
			'</tr>'+
			'<tpl if="!tasks.length">'+
				'<tr><td colspan="4">'+GO.lang.strNoItems+'</td></tr>'+
			'</tpl>'+
			'<tpl for="tasks">'+
				'<tr class="display-panel-link">'+
					'<td style="padding-right:0px !important;"><div class="display-panel-link-icon go-model-icon-GO_Tasks_Model_Task"></div></td>'+					
					'<td style="padding-right:0px !important;padding-left:0px !important;"><div class="display-panel-has-links <tpl if="link_count&gt;1">has-links</tpl>"></div></td>'+
					'<td><a href="#" onclick="GO.linkHandlers[\'GO_Tasks_Model_Task\'].call(this, {id});" <tpl if="completion_time!=\'\'">class="tasks-completed"</tpl><tpl if="late!=\'\'">class="tasks-late"</tpl>>{name}</a></td>'+
					'<td>{status}</td>'+
					'<td>{due_time}</td>'+
					'<td>{tasklist_name}</td>'+
				'</tr>'+	
//				'<tpl if="description">'+
//				'<tr><td colspan="99">{description}</td></tr>'+
//				'</tpl>'+
			'</tpl>'+
			'</table>'+
		'</tpl>';