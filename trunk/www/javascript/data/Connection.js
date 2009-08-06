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
 
/**
 * @class GO.data.Connection
 * @extends Ext.data.Connection
 * 
 * This class extends the default Ext connection and it will handle Group-Office authentication 
 * automatically.
 * 
 * The class encapsulates a connection to the page's originating domain, allowing requests to be made
 * either to a configured URL, or to a URL specified at request time.<br><br>
 * <p>
 * Requests made by this class are asynchronous, and will return immediately. No data from
 * the server will be available to the statement immediately following the {@link #request} call.
 * To process returned data, use a callback in the request options object, or an event listener.</p><br>
 * <p>
 * Note: If you are doing a file upload, you will not get a normal response object sent back to
 * your callback or event handler.  Since the upload is handled via in IFRAME, there is no XMLHttpRequest.
 * The response object is created using the innerHTML of the IFRAME's document as the responseText
 * property and, if present, the IFRAME's XML document as the responseXML property.</p><br>
 * This means that a valid XML or HTML document must be returned. If JSON data is required, it is suggested
 * that it be placed either inside a &lt;textarea> in an HTML document and retrieved from the responseText
 * using a regex, or inside a CDATA section in an XML document and retrieved from the responseXML using
 * standard DOM methods.
 * @constructor
 * @param {Object} config a configuration object.
 */
GO.data.Connection = Ext.extend(Ext.data.Connection, {
	
	 timeout : 60000,
	/**
     * Sends an HTTP request to a remote server.<p>
     * <b>Important:<b> Ajax server requests are asynchronous, and this call will
     * return before the response has been recieved. Process any returned data
     * in a callback function.
     * @param {Object} options An object which may contain the following properties:<ul>
     * <li><b>url</b> : String (Optional)<p style="margin-left:1em">The URL to
     * which to send the request. Defaults to configured URL</p></li>
     * <li><b>params</b> : Object/String/Function (Optional)<p style="margin-left:1em">
     * An object containing properties which are used as parameters to the
     * request, a url encoded string or a function to call to get either.</p></li>
     * <li><b>method</b> {String} (Optional) The HTTP method to use for the request. Defaults to the configured method, or
     * if no method was configured, "GET" if no parameters are being sent, and "POST" if parameters are being sent.</li>
     * <li><b>callback</b> : Function} (Optional)<p style="margin-left:1em">The
     * function to be called upon receipt of the HTTP response. The callback is
     * called regardless of success or failure and is passed the following
     * parameters:<ul>
     * <li><b>options</b> : Object<p style="margin-left:1em">The parameter to the request call.</p></li>
     * <li><b>success</b> : Boolean<p style="margin-left:1em">True if the request succeeded.</p></li>
     * <li><b>response</b> : Object<p style="margin-left:1em">The XMLHttpRequest object containing the response data. See http://www.w3.org/TR/XMLHttpRequest/ for details about accessing elements of the response.</p></li>
     * </ul></p></li>
     * <li><b>success</b> : Function (Optional)<p style="margin-left:1em">The function
     * to be called upon success of the request. The callback is passed the following
     * parameters:<ul>
     * <li><b>response</b> : Object<p style="margin-left:1em">The XMLHttpRequest object containing the response data.</p></li>
     * <li><b>options</b> : Object<p style="margin-left:1em">The parameter to the request call.</p></li>
     * </ul></p></li>
     * <li><b>failure</b> : Function (Optional)<p style="margin-left:1em">The function
     * to be called upon failure of the request. The callback is passed the
     * following parameters:<ul>
     * <li><b>response</b> : Object<p style="margin-left:1em">The XMLHttpRequest object containing the response data.</p></li>
     * <li><b>options</b> : Object<p style="margin-left:1em">The parameter to the request call.</p></li>
     * </ul></p></li>
     * <li><b>scope</b> : Object (Optional)<p style="margin-left:1em">The scope in
     * which to execute the callbacks: The "this" object for the callback function.
     * Defaults to the browser window.</p></li>
     * <li><b>form</b> : Object/String (Optional)<p style="margin-left:1em">A form
     * object or id to pull parameters from.</p></li>
     * <li><b>isUpload</b> : Boolean (Optional)<p style="margin-left:1em">True if
     * the form object is a file upload (will usually be automatically detected).</p></li>
     * <li><b>headers</b> : Object (Optional)<p style="margin-left:1em">Request
     * headers to set for the request.</p></li>
     * <li><b>xmlData</b> : Object (Optional)<p style="margin-left:1em">XML document
     * to use for the post. Note: This will be used instead of params for the post
     * data. Any params will be appended to the URL.</p></li>
     * <li><b>jsonData</b> : Object/String (Optional)<p style="margin-left:1em">JSON
     * data to use as the post. Note: This will be used instead of params for the post
     * data. Any params will be appended to the URL.</p></li>
     * <li><b>disableCaching</b> : Boolean (Optional)<p style="margin-left:1em">True
     * to add a unique cache-buster param to GET requests.</p></li>
     * </ul>
     * @return {Number} transactionId The id of the server transaction. This may be used
     * to cancel the request.
     */
     
	request : function(o){
		
		if(!GO.checkerIcon)
		{
			GO.checkerIcon = Ext.get("checker-icon");			
		}
		if(GO.checkerIcon)
		{
			GO.checkerIcon.setDisplayed(true);	
			
			//this slowed down IE enormously :(		
			//Ext.getBody().addClass('go-wait');			
		}
		
		if(o.callback)
		{
			o.originalCallback = o.callback;
		}
			
		o.loginCallback = function(){
			var cbOptions=o;
			cbOptions['callback']=o.originalCallback;			
			this.request(cbOptions);
		};
		o.loginCallbackScope=this;
		o.callback = this.authHandler;
	
		GO.data.Connection.superclass.request.call (this, o);
	},
	
	
	/**
	 * Useful in a connection callback function.
	 * Handles default error messages from the Group-Office server. It checks for the 
	 * precense of UNAUTHORIZED or NOTLOGGEDIN as error message. It will present a 
	 * login dialog if the user needs to login
	 * 
	 * @param {Boolean} success True if the request was sent successful 
	 * @param (Function} callback Callback function to call after successful login
	 * @param {Object} scope	Scope the function to this object
	 * 
	 * @returns {Boolean} True if no errors have been returned.
	 */
		
	authHandler : function(options, success, response)
	{

		if(GO.checkerIcon)
		{
			GO.checkerIcon.setDisplayed(false);			
			//this slowed down IE enormously :(
			//Ext.getBody().removeClass('go-wait');
		}
		
		if(!success)
		{
			//Ext.Msg.alert(GO.lang['strError'], String.format(GO.lang['strRequestError'], response.status));			
			Ext.callback(options.originalCallback, options.scope, [options, success, response]);
		}else
		{
			if(response.responseText.substr(0,1)=='{')
			{
				var data = Ext.decode(response.responseText);
			}else
			{
				var data = {};
			}
			if(!GO.util.empty(data.authError))
			{
				switch(data.authError)
				{
					case 'UNAUTHORIZED':
						Ext.Msg.alert(GO.lang['strUnauthorized'], GO.lang['strUnauthorizedText']);
					break;
					
					case 'NOTLOGGEDIN':						
						GO.loginDialog.addCallback(options.loginCallback, options.loginCallbackScope);
						GO.loginDialog.show();						
					break;
				}
				return false;
				
			}else
			{
				Ext.callback(options.originalCallback, options.scope, [options, success, response]);
			}
		}
	}	
});


/**
 * @class Ext.Ajax
 * @extends Ext.data.Connection
 * Global Ajax request class.  Provides a simple way to make Ajax requests with maximum flexibility.  Example usage:
 * <pre><code>
// Basic request
Ext.Ajax.request({
   url: 'foo.php',
   success: someFn,
   failure: otherFn,
   headers: {
       'my-header': 'foo'
   },
   params: { foo: 'bar' }
});

// Simple ajax form submission
Ext.Ajax.request({
    form: 'some-form',
    params: 'foo=bar'
});

// Default headers to pass in every request
Ext.Ajax.defaultHeaders = {
    'Powered-By': 'Ext'
};

// Global Ajax events can be handled on every request!
Ext.Ajax.on('beforerequest', this.showSpinner, this);
</code></pre>
 * @singleton
 */
Ext.Ajax = new GO.data.Connection({
    /**
     * @cfg {String} url @hide
     */
    /**
     * @cfg {Object} extraParams @hide
     */
    /**
     * @cfg {Object} defaultHeaders @hide
     */
    /**
     * @cfg {String} method (Optional) @hide
     */
    /**
     * @cfg {Number} timeout (Optional) @hide
     */
    /**
     * @cfg {Boolean} autoAbort (Optional) @hide
     */

    /**
     * @cfg {Boolean} disableCaching (Optional) @hide
     */

    /**
     * @property  disableCaching
     * True to add a unique cache-buster param to GET requests. (defaults to true)
     * @type Boolean
     */
    /**
     * @property  url
     * The default URL to be used for requests to the server. (defaults to undefined)
     * @type String
     */
    /**
     * @property  extraParams
     * An object containing properties which are used as
     * extra parameters to each request made by this object. (defaults to undefined)
     * @type Object
     */
    /**
     * @property  defaultHeaders
     * An object containing request headers which are added to each request made by this object. (defaults to undefined)
     * @type Object
     */
    /**
     * @property  method
     * The default HTTP method to be used for requests. (defaults to undefined; if not set but parms are present will use POST, otherwise GET)
     * @type String
     */
    /**
     * @property  timeout
     * The timeout in milliseconds to be used for requests. (defaults to 30000)
     * @type Number
     */

    /**
     * @property  autoAbort
     * Whether a new request should abort any pending requests. (defaults to false)
     * @type Boolean
     */
    autoAbort : false,

    /**
     * Serialize the passed form into a url encoded string
     * @param {String/HTMLElement} form
     * @return {String}
     */
    serializeForm : function(form){
        return Ext.lib.Ajax.serializeForm(form);
    }
});