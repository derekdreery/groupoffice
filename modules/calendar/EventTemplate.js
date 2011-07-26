GO.calendar.EventTemplate =
		'<tpl if="values.events && values.events.length">'+
		'{[this.collapsibleSectionHeader(GO.calendar.lang.forthcomingAppointments, "events-"+values.panelId, "events")]}'+
		
			'<table class="display-panel" cellpadding="0" cellspacing="0" border="0" id="events-{panelId}">'+
			'<tr>'+
				'<td class="table_header_links" width="16px;"></td>'+
				'<td class="table_header_links" width="10px;"></td>'+
				'<td class="table_header_links">' + GO.lang['strName'] + '</a></td>'+
				'<td class="table_header_links" width="110px">' + GO.calendar.lang.startsAt + '</td>'+
				'<td class="table_header_links" width="120px">' + GO.calendar.lang.calendar + '</td>'+
			'</tr>'+
			'<tpl if="!events.length">'+
				'<tr><td colspan="4">'+GO.lang.strNoItems+'</td></tr>'+
			'</tpl>'+
			'<tpl for="events">'+
				'<tr class="display-panel-link">'+
					'<td style="padding-right:0px !important;"><div class="display-panel-link-icon go-link-icon-1"></div></td>'+
					'<td style="padding-right:0px !important;padding-left:0px !important;"><div class="display-panel-has-links <tpl if="link_count&gt;1">has-links</tpl>"></div></td>'+
					'<td><a href="#" onclick="GO.linkHandlers[1].call(this, {id});">{name}</a></td>'+
					'<td>{start_time}</td>'+
					'<td>{calendar_name}</td>'+
				'</tr>'+
			'</tpl>'+
			'</table>'+
		'</tpl>';