GO.calendar.EventTemplate =
		'{[this.collapsibleSectionHeader(GO.calendar.lang.forthcomingAppointments, "events-"+values.panelId, "events")]}'+
		'<tpl if="values.events">'+
			'<table class="display-panel" cellpadding="0" cellspacing="0" border="0" id="events-{panelId}">'+
			'<tr>'+
				'<td class="table_header_links" width="40%">' + GO.lang['strName'] + '</a></td>'+
				'<td class="table_header_links" width="20%">' + GO.calendar.lang.startsAt + '</td>'+
				'<td class="table_header_links" width="40%">' + GO.calendar.lang.calendar + '</td>'+
			'</tr>'+
			'<tpl if="!events.length">'+
				'<tr><td colspan="4">'+GO.lang.strNoItems+'</td></tr>'+
			'</tpl>'+
			'<tpl for="events">'+
				'<tr class="display-panel-link">'+
					'<td><a href="#" onclick="GO.linkHandlers[1].call(this, {id});">{name}</a></td>'+
					'<td>{start_time}</td>'+
					'<td>{calendar_name}</td>'+
				'</tr>'+
			'</tpl>'+
			'</table>'+
		'</tpl>';