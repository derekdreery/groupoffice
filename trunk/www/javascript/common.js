/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
Ext.namespace('GO.util');

GO.mailTo = function(email){
	
	if(GO.email && GO.settings.modules.email.read_permission)
	{
		return '<a href="javascript:GO.email.Composer.show({values : {to: \''+email+'\'}});">'+email+'</a>';
	}else
	{
		return '<a href="mailto:'+email+'">'+email+'</a>';
	}	
}

GO.playAlarm = function(){
	
	var flashMovie= GO.util.getFlashMovieObject("alarmSound");
	flashMovie.Play();	
}

GO.util.nl2br = function (v)
{
	return v.replace(/([^>])\n/g, '$1<br />\n');
}

GO.util.clone = function(o) {
    if('object' !== typeof o) {
        return o;
    }
    var c = 'function' === typeof o.pop ? [] : {};
    var p, v;
    for(p in o) {
        v = o[p];
        if('object' === typeof v) {
            c[p] = GO.util.clone(v);
        }
        else {
            c[p] = v;
        }
    }
    return c;
}  
/**
 * Handles default error messages from the Group-Office server. It checks for the 
 * precense of UNAUTHORIZED or NOTLOGGEDIN as error message. It will present a 
 * login dialog if the user needs to login
 * 
 * @param {Object} json JSON object returned from the GO server. 
 * @param (Function} callback Callback function to call after successful login
 * @param {Object} scope	Scope the function to this object
 * 
 * @returns {Boolean} True if no errors have been returned.
 */
 
GO.jsonAuthHandler = function(json, callback, scope)
{
	if(json.authError)
	{
		switch(json.authError)
		{
			case 'UNAUTHORIZED':
				alert(GO.lang['strUnauthorizedText']);
				return false;
			break;
			
			case 'NOTLOGGEDIN':			
				
				if(callback)
				{
					GO.loginDialog.addCallback(callback, scope);
				}
							
				GO.loginDialog.show();
				return false;
			break;
		}
	}
	return true;
}



//url, params, count, callback, success, failure, scope ( success & failure are callbacks)
//store. If you pass a store it will automatically reload it with the params
//it will reload with a callback that will check for deleteSuccess in the json reponse. If it
//failed it will display deleteFeedback
GO.deleteItems = function(config)
{	
	switch(config.count)
	{
		case 0:
			alert( GO.lang['noItemSelected']);
			return false;
		break;
		
		case 1:
			var strConfirm = GO.lang['strDeleteSelectedItem'];
		break;
		
		default:
			var t = new Ext.Template(
		    	GO.lang['strDeleteSelectedItems']
			);
			var strConfirm = t.applyTemplate({'count': config.count});						
		break;						
	}

	if(confirm(strConfirm)){
		if(config.store)
		{
			//add the parameters
			for(var param in config.params)
			{
				config.store.baseParams[param]=config.params[param];
			}
			config.store.reload({
				//params: config.params,
				callback: function(){
					if(!this.reader.jsonData.deleteSuccess)
					{
						if(config.failure)
						{
							callback = config.failure.createDelegate(config.scope);
							callback.call();
						}
						alert( this.reader.jsonData.deleteFeedback);
					}else
					{
						if(config.success)
						{
							callback = config.success.createDelegate(config.scope);
							callback.call();
						}
					}
					
					if(config.callback)
					{
						callback = config.callback.createDelegate(config.scope);
						callback.call();
					}	
				}
			}
			);
			
			//remove the delete params
			for(var param in config.params)
			{					
				delete config.store.baseParams[param];					
			}
			
			
		}else
		{

			Ext.Ajax.request({
				url: config.url,
				params: config.params,
				callback: function(options, success, response)
				{

					if(!success)
					{
						alert( GO.lang['strRequestError']);
					}else
					{
						
						
						var responseParams = Ext.decode(response.responseText);
						if(!responseParams.success)
						{
							if(config.failure)
							{
								callback = config.failure.createDelegate(config.scope);
								callback.call(this, responseParams);
							}
							alert( responseParams.feedback);
						}else
						{
							if(config.success)
							{
								callback = config.success.createDelegate(config.scope);
								callback.call(this, responseParams);
							}
						}
						
						if(config.callback)
						{
							callback = config.callback.createDelegate(config.scope);
							callback.call(this, responseParams);
						}
					}
				}				
			});
		}	
	}
	
}

GO.util.getFlashMovieObject = function(movieName)
{
  if (window.document[movieName]) 
  {
      return window.document[movieName];
  }
  if (navigator.appName.indexOf("Microsoft Internet")==-1)
  {
    if (document.embeds && document.embeds[movieName])
      return document.embeds[movieName]; 
  }
  else // if (navigator.appName.indexOf("Microsoft Internet")!=-1)
  {
    return document.getElementById(movieName);
  }
}


GO.util.unlocalizeNumber = function (number, decimal_seperator, thousands_seperator)
{
	
	if(!decimal_seperator)
	{
		decimal_seperator=GO.settings.decimal_seperator;
	}
	
	if(!thousands_seperator)
	{
		thousands_seperator=GO.settings.thousands_seperator;
	}
	
	number = number+"";
	
	number = number.replace(thousands_seperator, "");
	return number.replace(decimal_seperator, ".");	
}



GO.util.numberFormat = function (number, decimals, decimal_seperator, thousands_seperator)
{
	if(!decimals)
	{
		decimals=2;
	}
	
	if(!decimal_seperator)
	{
		decimal_seperator=GO.settings.decimal_seperator;
	}
	
	if(!thousands_seperator)
	{
		thousands_seperator=GO.settings.thousands_seperator;
	}

	if(number=='')
	{
		return '';
	}
	
/*	if(localized)
	{
		var internal_number = number.replace(thousands_seperator, "");
		internal_number = internal_number.replace(decimal_seperator, ".");
	}else
	{
		var internal_number=number;
	}*/
	
	var numberFloat = parseFloat(number);
	
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
		
		var dec = decimals;
		while(formattedNumber.substring(formattedNumber.length-1)=='0' && dec>decimals)
		{
			dec--;
			formattedNumber = formattedNumber.substring(0,formattedNumber.length-1);
		}
		
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
	if(formattedNumber==',NaN')
	{
		formattedNumber = GO.util.numberFormat('0', decimals, decimal_seperator, thousands_seperator);
	}
	return formattedNumber;
}

GO.util.popup = function (config)
{
	if(!config.width)
	{
		config.width = screen.availWidth;
		config.height = screen.availHeight;
	}
	if(!config.target)
	{
		config.target='_blank';
	}

	var centered;
	x = (screen.availWidth - config.width) / 2;
	y = (screen.availHeight - config.height) / 2;
	centered =',width=' + config.width + ',height=' + config.height + ',left=' + x + ',top=' + y + ',scrollbars=yes,resizable=yes,status=no';

	var popup = window.open(config.url, config.target, centered);
	
	if(!popup)
	{
		alert(GO.lang.popupBlocker);
		return false;
	}
	
  if (!popup.opener) popup.opener = self;
  
	popup.focus();
	
	return popup;
}

