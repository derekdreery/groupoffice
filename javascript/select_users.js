function search_users(form_name, id)
{
	document.forms[form_name].elements[id+"[task]"].value="search";
	document.forms[form_name].submit();
}

function add_users(form_name, id)
{
	document.forms[form_name].elements[id+"[task]"].value="add";
	document.forms[form_name].submit();
}

function delete_users(form_name, id)
{
	document.forms[form_name].elements[id+"[task]"].value="delete";
	document.forms[form_name].submit();
}

function return_to(form_name, id)
{
	document.forms[form_name].elements[id+"[task]"].value="";
	document.forms[form_name].submit();
}
