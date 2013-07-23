GO.createModifyTemplate =
	'<div class="display-panel-heading "><div style="float:left;margin-left:3px;">'+GO.lang['createModify']+'</div></div>'+
//	'{[this.collapsibleSectionHeader(GO.lang.createModify, "createModify-"+values.panelId, "createModify")]}'+
	'<table>'+
		'<tr>'+
			'<td width="80px">'+GO.lang['strCtime']+':</td>'+'<td width="100px">{ctime}</td>'+
			'<td width="80px">'+GO.lang['strMtime']+':</td>'+'<td width="100px">{mtime}</td>'+
		'</tr><tr>'+
			'<td width="80px" valign="top" style="vertical-align:top;">'+GO.lang['createdBy']+':</td>'+'<td width="100px">{username}</td>'+
			'<td width="80px" valign="top" style="vertical-align:top;">'+GO.lang['mUser']+':</td>'+'<td width="100px">{musername}</tpl></td>'+
		'</tr>'+
	'</table>';