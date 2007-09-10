//to capture shift keys in navigator 4.x
if (window.Event) //if Navigator 4.X
{
	document.captureEvents(Event.KEYPRESS)
}
			
//Firefox does this with CSS in the table
//No text selection
//With IE4+ the following works:
if (document.all)
{
  //document.onselectstart = function () { return false; };
}
function table_sort(form_id, table_id, sort_index, sort_ascending){
	document.forms[form_id].elements[table_id+"[sort_index]"].value=sort_index;
	document.forms[form_id].elements[table_id+"[sort_ascending]"].value=sort_ascending;
	document.forms[form_id].submit();
}
function table_change_page(form_id, table_id, start, offset){
	document.forms[form_id].elements[table_id+"[offset]"].value=offset;
	document.forms[form_id].elements[table_id+"[start]"].value=start;
	document.forms[form_id].submit();
}	

function table_confirm_delete(form_id, table_id, strNoItemSelected, strSelectedItem, strSelectedItems, strDeletePrefix, strDeleteSuffix)
{
	var count = 0;

	for (var i=0;i<document.forms[form_id].elements.length;i++)
	{
		if(document.forms[form_id].elements[i].name == table_id+"[selected][]" 
				&& document.forms[form_id].elements[i].checked == true)
		{
			count++;
		}
	}
	switch (count)
	{
		case 0:
			alert(strNoItemSelected);
		break;

		case 1:
			if (confirm(strDeletePrefix+' '+strSelectedItem+' '+strDeleteSuffix))
			{
				document.forms[form_id].elements[table_id+"[task]"].value="delete";
				document.forms[form_id].submit();
			}
		break;

		default:
			if (confirm(strDeletePrefix+' '+count+' '+strSelectedItems+' '+strDeleteSuffix))
			{
				document.forms[form_id].elements[table_id+"[task]"].value="delete";
				document.forms[form_id].submit();
			}
		break;
	}
}

function table_delete(form_id, table_id)
{
	document.forms[form_id].elements[table_id+"[task]"].value="delete";
	document.forms[form_id].submit();
}

function table_count_selected(form_id, table_id)
{
	var count = 0;

	for (var i=0;i<document.forms[form_id].elements.length;i++)
	{
		if(document.forms[form_id].elements[i].name == table_id+"[selected][]" 
				&& document.forms[form_id].elements[i].checked == true)
		{
			count++;
		}
	}
	return count;
}

function table_confirm_unlink(form_id, table_id, strNoItemSelected, strSelectedItem, strSelectedItems, strUnlinkPrefix, strUnlinkSuffix)
{
	var count = 0;

	for (var i=0;i<document.forms[form_id].elements.length;i++)
	{
		if(document.forms[form_id].elements[i].name == table_id+"[selected][]" 
				&& document.forms[form_id].elements[i].checked == true)
		{
			count++;
		}
	}
	switch (count)
	{
		case 0:
			alert(strNoItemSelected);
		break;

		case 1:
			if (confirm(strUnlinkPrefix+' '+strSelectedItem+' '+strUnlinkSuffix))
			{
				document.forms[form_id].elements[table_id+"[task]"].value="unlink";
				document.forms[form_id].submit();
			}
		break;

		default:
			if (confirm(strUnlinkPrefix+' '+count+' '+strSelectedItems+' '+strUnlinkSuffix))
			{
				document.forms[form_id].elements[table_id+"[task]"].value="unlink";
				document.forms[form_id].submit();
			}
		break;
	}
}

var oldclasses = new Array();


function table_update_class(checkbox, row_id)
{
	var table_row = get_object(row_id);
	if (checkbox.checked)
	{
		oldclasses[row_id] = table_row.className;
		table_row.className = "SelectedRow";
	}else
	{
		table_row.className = oldclasses[row_id];
	}
}

function table_clear_old_class(row_id)
{
	oldclasses[row_id]="";
}

function table_select_all(form_id, table_id, check)
{
	for (var i=0;i<document.forms[form_id].elements.length;i++)
	{
		if(document.forms[form_id].elements[i].name == table_id+"[selected][]" && document.forms[form_id].elements[i].checked!=check)
		{
			document.forms[form_id].elements[i].checked = check;
			document.forms[form_id].elements[i].onclick();
		}
	}
}

function table_clear_selection(form_id, table_id)
{
	for (var i=0;i<document.forms[form_id].elements.length;i++)
	{
		if(document.forms[form_id].elements[i].id.substring(0,5) == "dtcb_" && document.forms[form_id].elements[i].checked)
		{		
			document.forms[form_id].elements[i].checked = false;
			document.forms[form_id].elements[i].onclick();
		}
	}
}

var last_selected = "";


function table_select(evt, form_id, table_id, row_id, multiselect, select_without_ctrl)
{		
	evt=evt||false;
	
	if(select_without_ctrl)
	{
		var ctrlPressed = true;
	}else
	{
		if (navigator.userAgent.toLowerCase().indexOf('mac')>=0) {
			var ctrlPressed = (evt && evt.altKey);
		} else {
			var ctrlPressed = (evt && evt.ctrlKey);
		}
	}
	var shiftPressed = (evt && evt.shiftKey);
	
	if((!ctrlPressed && !shiftPressed) || !multiselect)
	{
		table_clear_selection(form_id, table_id);
	}
	
	var start_selection = false;	

	if(shiftPressed && last_selected != "" && multiselect)
	{
		//undo last selection
		if(last_selected != start_point)
		{
			for (var i=0;i<document.forms[form_id].elements.length;i++)
			{
				if(document.forms[form_id].elements[i].type == "checkbox" && 
						document.forms[form_id].elements[i].name== table_id+"[selected][]")
				{
					
					if(start_selection)
					{			
						if(document.forms[form_id].elements[i].id != start_point)
						{
							document.forms[form_id].elements[i].checked = !document.forms[form_id].elements[i].checked;
							document.forms[form_id].elements[i].onclick();
						}
						
						if(document.forms[form_id].elements[i].id == last_selected || 
								document.forms[form_id].elements[i].id ==start_point)
						{
							start_selection = false;
							break;					
						}
					}else
					{
						if(document.forms[form_id].elements[i].id == last_selected || 
								document.forms[form_id].elements[i].id ==start_point)
						{
							start_selection = true;
							if(document.forms[form_id].elements[i].id != start_point)
							{
								document.forms[form_id].elements[i].checked = !document.forms[form_id].elements[i].checked;
								document.forms[form_id].elements[i].onclick();
							}
						}
					}	
				}
			}
		}
		for (var i=0;i<document.forms[form_id].elements.length;i++)
		{
			if(document.forms[form_id].elements[i].type == "checkbox" &&	
				document.forms[form_id].elements[i].name== table_id+"[selected][]")
			{
				if(start_selection)
				{			
					if(document.forms[form_id].elements[i].id != start_point)
					{
						document.forms[form_id].elements[i].checked = !document.forms[form_id].elements[i].checked;
						document.forms[form_id].elements[i].onclick();
					}
					
					if(document.forms[form_id].elements[i].id == "dtcb_"+row_id || document.forms[form_id].elements[i].id == start_point)
					{
						start_selection = false;
						break;					
					}
				}else
				{
					if(document.forms[form_id].elements[i].id == "dtcb_"+row_id || document.forms[form_id].elements[i].id == start_point)
					{
						start_selection = true;
						if(document.forms[form_id].elements[i].id != start_point)
						{
							document.forms[form_id].elements[i].checked = !document.forms[form_id].elements[i].checked;
							document.forms[form_id].elements[i].onclick();
						}
					}
				}				
			}
		}
	}else
	{
		var checkbox = get_object("dtcb_"+row_id);
		checkbox.checked=!checkbox.checked;
		checkbox.onclick();
		start_point = "dtcb_"+row_id;
	}	
	last_selected = "dtcb_"+row_id;
}
function table_select_single(form_id, table_id, row_id)
{
	table_clear_selection(form_id, table_id);
	
	var checkbox = get_object("dtcb_"+row_id);
	if(checkbox)
	{
		checkbox.checked=!checkbox.checked;
		checkbox.onclick();
		start_point = "dtcb_"+row_id;						
		last_selected = "dtcb_"+row_id;
	}
}


var old_glow_classes= new Array();
function table_glow_row(row)
{
	old_glow_classes[row.id] =row.className;
  row.className = 'SelectedRow';
}

function table_unglow_row(row)
{
  row.className = old_glow_classes[row.id];
}
