function change_view(calendar_view, form_name, offset, day,month, year)
{
	document.forms[form_name].elements[calendar_view+'[offset]'].value=offset;
	goto_date(calendar_view, form_name, day, month, year);
}

function change_view_type(view_type, form_name)
{
	document.forms[form_name].elements['view_type'].value=view_type;
	document.forms[form_name].submit();
}
function goto_date(calendar_view, form_name,day, month, year)
{
  document.forms[form_name].elements[calendar_view+'[day]'].value = day;
  document.forms[form_name].elements[calendar_view+'[month]'].value = month;
  document.forms[form_name].elements[calendar_view+'[year]'].value = year;
  document.forms[form_name].submit();
}

function glow(tr)
{
	tr.className = 'SelectedRow';
}

function unglow(tr)
{
	tr.className = '';
}
