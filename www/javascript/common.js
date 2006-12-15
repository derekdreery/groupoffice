function number_format(number, decimals, decimal_seperator, thousands_seperator)
{
	var internal_number = number.replace(thousands_seperator, "");
	internal_number = internal_number.replace(decimal_seperator, ".");
	var numberFloat = parseFloat(internal_number);
	numberFloat = numberFloat.toFixed(decimals);
	
	if(decimals>0)
	{
		var dotIndex = numberFloat.indexOf(".");	
		if(!dotIndex)
		{
			numberFloat = numberFloat+".";
			dotIndex = numberFloat.indexOf(".");	
		}
		
		var presentDecimals = numberFloat.length-dotIndex;
		
		for(i=presentDecimals;i<=decimals;i++)
		{
			numberFloat = numberFloat+"0";
		}
		var formattedNumber = decimal_seperator+numberFloat.substring(dotIndex+1);
	}else
	{
		
		var formattedNumber = "";
		var dotIndex = numberFloat.length;
	}
	

	

	var counter=0;
	for(i=dotIndex-1;i>=0;i--)
	{		
		if(counter==3 && numberFloat.substr(i,1)!='-')
		{
			formattedNumber= thousands_seperator+formattedNumber; 
			counter=0;
		}
		formattedNumber = numberFloat.substr(i,1)+formattedNumber;
		counter++;
		
	}	
	if(formattedNumber=='NaN')
	{
		formattedNumber = number_format('0', decimals, decimal_seperator, thousands_seperator);
	}
	return formattedNumber;
}

function executeOnEnter(evt, callbackfunction)
{
	evt=evt||false;
	
	if(evt.which)
	{
		var whichCode = evt.which;
	}else
	{		
		var whichCode = event.keyCode;
	}

	if (whichCode == 13)
	{
		return eval(callbackfunction);
	}
}

if (window.Event) //if Navigator 4.X
{
	document.captureEvents(Event.KEYPRESS)
}



function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}
	

function popup(url,width,height)
{
	if(width > 0 && height > 0)
	{
		var centered;
		x = (screen.availWidth - width) / 2;
		y = (screen.availHeight - height) / 2;
		centered =',width=' + width + ',height=' + height + ',left=' + x + ',top=' + y + ',scrollbars=yes,resizable=yes,status=yes';
	}else
	{
		centered = 'scrollbars=yes,resizable=yes,status=yes';
	}	
	var popup = window.open(url, '_blank', centered);
    	if (!popup.opener) popup.opener = self;
	popup.focus();
}

function openPopup(target, url,width,height, scrollbars)
{
	
	if(width > 0 && height > 0)
	{
		var centered;
		x = (screen.availWidth - width) / 2;
		y = (screen.availHeight - height) / 2;
		centered =',width=' + width + ',height=' + height + ',left=' + x + ',top=' + y + ',scrollbars='+scrollbars+',resizable=yes,status=yes';
	}else
	{
		centered = 'scrollbars='+scrollbars+',resizable=yes,status=yes';
	}	
	var popup = window.open(url, target, centered);
  	if (!popup.opener) popup.opener = self;
	popup.focus();
}


function confirm_action(url, message)
{
	if (confirm(message))
	{
		window.location=url
	}
}

function get_object(name)
{
	if (document.getElementById)
	{
		return document.getElementById(name);
 	}
 	else if (document.all)
	{
  		return document.all[name];
 	}
 	else if (document.layers)
	{
  		return document.layers[name];
	}
	return false;
}

function check_checkbox(id)
{
	if(check_box = get_object(id))
	{
		if (!check_box.disabled)
		{
			check_box.checked = !check_box.checked;
			if (check_box.onclick)
			{
				check_box.onclick();
			}
		}
	}
}

function select_radio(id)
{
	if(radio_but = get_object(id))
	{
		radio_but.checked = true;
		if (radio_but.onclick)
		{
			radio_but.onclick();
		}
	}
}


function getSelected(opt) 
{
	var selected = new Array();
	var index = 0;
	for (var i=0; i<opt.length;i++) 
	{
		if ((opt[i].selected) || (opt[i].checked)) 
		{
			index = selected.length;
			selected[index] = new Object;
			selected[index].value = opt[i].value;
			selected[index].index = i;
		}
	}
	return selected;
}


function copy_clip(copytext)
{

 	 if (window.clipboardData) 
   {
   
   // the IE-manier
   window.clipboardData.setData("Text", copytext);
   
   // waarschijnlijk niet de beste manier om Moz/NS te detecteren;
   // het is mij echter onbekend vanaf welke versie dit precies werkt:
   }
   else if (window.netscape) 
   { 
   
   // dit is belangrijk maar staat nergens duidelijk vermeld:
   // you have to sign the code to enable this, or see notes below 
   netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');
   
   // maak een interface naar het clipboard
   var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
   if (!clip) return;
   
   // maak een transferable
   var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
   if (!trans) return;
   
   // specificeer wat voor soort data we op willen halen; text in dit geval
   trans.addDataFlavor('text/unicode');
   
   // om de data uit de transferable te halen hebben we 2 nieuwe objecten nodig   om het in op te slaan
   var str = new Object();
   var len = new Object();
   
   var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
   
   var copytext=copytext;
   
   str.data=copytext;
   
   trans.setTransferData("text/unicode",str,copytext.length*2);
   
   var clipid=Components.interfaces.nsIClipboard;
   
   if (!clip) return false;
   
   clip.setData(trans,null,clipid.kGlobalClipboard);
   
   }
   return false;
}

function get_date(dateString, format, date_seperator)
{
	if(date_seperator == '/')
	{
		date_seperator = '\\/';
	}
	
	var datetimeArray = dateString.split(' ');
	var date = datetimeArray[0].replace(new RegExp(date_seperator,'g'), '-');
	var format = format.replace(new RegExp(date_seperator,'g'), '-');
	var	 dateArray = date.split('-');			
	var formatArray = format.split('-');
	
	var newDateArray = new Array();
				
	for(i=0;i<=2;i++)
	{
		newDateArray[formatArray[i]] = dateArray[i];
	}

	if(datetimeArray[1])
	{
		var timeArray = datetimeArray[1].split(':');
		var hour = timeArray[0];
		var min = timeArray[1];
	}else
	{
		var hour = 0;
		var min = 0;
	}
					//alert(newDateArray["Y"]+'-'+newDateArray["m"]+'-'+newDateArray["d"]+' '+hour+':'+min);
	var dateObj = new Date(newDateArray["Y"], newDateArray["m"]-1, newDateArray["d"], hour, min);
	
	return dateObj;	
}

function y2k(number) { return (number < 1000) ? number + 1900 : number; }

function isDate (day,month,year) {
// checks if date passed is valid
// will accept dates in following format:
// isDate(dd,mm,ccyy), or
// isDate(dd,mm) - which defaults to the current year, or
// isDate(dd) - which defaults to the current month and year.
// Note, if passed the month must be between 1 and 12, and the
// year in ccyy format.

    var today = new Date();
    year = ((!year) ? y2k(today.getYear()):year);
    month = ((!month) ? today.getMonth():month-1);
    if (!day) return false
    var test = new Date(year,month,day);
    if ( (y2k(test.getYear()) == year) &&
         (month == test.getMonth()) &&
         (day == test.getDate()) )
        return true;
    else
        return false
}


