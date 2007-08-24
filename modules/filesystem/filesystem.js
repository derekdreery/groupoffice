function activate_linking(goto_url)
{
	document.forms[0].goto_url.value=goto_url;
	document.forms[0].prop_task.value='activate_linking';
	document.forms[0].submit();
}


function item_click(check_box)
{
	var item = get_object(check_box.value);
	if (check_box.checked)
	{
		item.className = 'SelectedRow';
	}else
	{
		item.className = '';
	}
}

function invert_selection()
{
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
		{
			document.forms[0].elements[i].checked = !(document.forms[0].elements[i].checked);
			item_click(document.forms[0].elements[i]);
		}
	}
}

function cut_items(no_select)
{
	var count = 0;
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
			}
		}
	}

	if (count > 0)
	{
		document.forms[0].action = 'select_destination.php';
		document.forms[0].task.value = 'cut';
		document.forms[0].submit();
	}else
	{
		alert(no_select);
	}
}

function copy_items(no_select)
{
	var count = 0;
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].type == 'checkbox' && document.forms[0].elements[i].name != 'dummy')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
			}
		}
	}

	if (count > 0)
	{
		document.forms[0].action = 'select_destination.php';
		document.forms[0].task.value = 'copy';
		document.forms[0].submit();
	}else
	{
		alert(no_select);
	}
}

function paste_items()
{
	document.forms[0].task.value = 'paste';
	document.forms[0].submit();
}


function mail_files(no_select)
{
	var count = 0;
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].name == 'fs_list[selected][]')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
			}
		}
	}

	if(count > 0)
	{
		document.forms[0].task.value = 'mail_files';
		document.forms[0].submit();
	}else
	{
		alert(no_select);
	}
}

function change_location(dropbox)
{
	document.forms[0].share_path.value = dropbox.value;
	document.forms[0].elements["fv_view[path]"].value = dropbox.value;
	document.forms[0].submit();
}

function properties(no_multi_select)
{
	var count = 0;
	var path = new String;

	document.forms[0].return_to_path.value=document.forms[0].elements["fs_list[path]"].value;
	
	for (var i=0;i<document.forms[0].elements.length;i++)
	{
		if(document.forms[0].elements[i].name == 'fs_list[selected][]')
		{
			if (document.forms[0].elements[i].checked == true)
			{
				count++;
				path = document.forms[0].elements[i].value;
			}
		}
	}
	switch (count)
	{
		case 0:
			
			document.forms[0].task.value = "properties";
			document.forms[0].submit();
		break;

		case 1:
			document.forms[0].task.value = "properties";
			document.forms[0].elements["fs_list[path]"].value = path;
			document.forms[0].elements["fs_list[submitted]"].value = 'true';
			document.forms[0].submit();

		break;

		default:
			alert(no_multi_select);
		break;
	}
}
