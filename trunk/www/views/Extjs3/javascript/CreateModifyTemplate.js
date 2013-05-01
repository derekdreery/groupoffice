GO.createModifyTemplate =
	'{[this.collapsibleSectionHeader(GO.lang.createModify, "createModify-"+values.panelId, "createModify")]}'+
	'<table>'+
		'<tr>'+
			'<td width="80px">'+GO.lang['strCtime']+':</td>'+'<td width="100px">{ctime}</td>'+
			'<td width="80px">'+GO.lang['strMtime']+':</td>'+'<td width="100px">{mtime}</td>'+
		'</tr><tr>'+
			'<td width="80px">'+GO.lang['createdBy']+':</td>'+'<td width="100px">{username}</td>'+
			'<td width="80px">'+GO.lang['mUser']+':</td>'+'<td width="100px">'+
					'<tpl if="muser_id">{musername}</tpl>'+
			'</td>'+
		'</tr>'+
	'</table>';