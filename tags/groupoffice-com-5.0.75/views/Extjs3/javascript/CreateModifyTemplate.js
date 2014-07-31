GO.createModifyTemplate =
	'<div class="display-panel-heading ">'+GO.lang['createModify']+'</div>'+
//	'{[this.collapsibleSectionHeader(GO.lang.createModify, "createModify-"+values.panelId, "createModify")]}'+
	'<table>'+
		'<tr>'+
			'<td width="120">'+GO.lang['strCtime']+':</td>'+'<td style="padding-right:2px">{ctime}</td>'+
			'<td width="80">'+GO.lang['strMtime']+':</td>'+'<td>{mtime}</td>'+
		'</tr><tr>'+
			'<td width="120" style="vertical-align:top;">'+GO.lang['createdBy']+':</td>'+'<td style="padding-right:2px">{username}</td>'+
			'<td width="120" style="vertical-align:top;">'+GO.lang['mUser']+':</td>'+'<td>{musername}</td>'+
		'</tr>'+
	'</table>';