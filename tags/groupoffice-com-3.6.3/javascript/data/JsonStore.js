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
 * @class GO.data.JsonStore
 * @extends Ext.data.JsonStore
 * 
 * Extends the Ext JsonStore class to handle Group-Office authentication automatically. <br/>
<pre><code>
var store = new GO.data.JsonStore({
    url: 'get-images.php',
    root: 'images',
    fields: ['name', 'url', {name:'size', type: 'float'}, {name:'lastmod', type:'date'}]
});
</code></pre>
 * This would consume a returned object of the form:
<pre><code>
{
    images: [
        {name: 'Image one', url:'/GetImage.php?id=1', size:46.5, lastmod: new Date(2007, 10, 29)},
        {name: 'Image Two', url:'/GetImage.php?id=2', size:43.2, lastmod: new Date(2007, 10, 30)}
    ]
}
</code></pre>
 * An object literal of this form could also be used as the {@link #data} config option.
 * <b>Note: Although they are not listed, this class inherits all of the config options of Store,
 * JsonReader.</b>
 * @cfg {String} url  The URL from which to load data through an HttpProxy. Either this
 * option, or the {@link #data} option must be specified.
 * @cfg {Object} data  A data object readable this object's JsonReader. Either this
 * option, or the {@link #url} option must be specified.
 * @cfg {Array} fields  Either an Array of field definition objects as passed to
 * {@link Ext.data.Record#create}, or a Record constructor object created using {@link Ext.data.Record#create}.
 * @constructor
 * @param {Object} config
 */

GO.data.JsonStore = function(config) {

	GO.data.JsonStore.superclass.constructor.call (this, config);
	
	this.on('load', function(){
		this.loaded=true;
	}, this);
	
	this.on('loadexception',	
		function(proxy, store, response, e){

			if(response.status==0)
			{
				//silently ignore because auto refreshing jobs often get here somehow??
				//GO.errorDialog.show(GO.lang.strRequestError, "");
			}else if(!this.reader.jsonData || GO.jsonAuthHandler(this.reader.jsonData, this.load, this))
			{
				switch(response.responseText.trim())
				{
					case 'NOTLOGGEDIN':
						document.location=BaseHref;
					break;

					case 'UNAUTHORIZED':
						Ext.Msg.alert(GO.lang['strUnauthorized'], GO.lang['strUnauthorizedText']);
					break;

					default:
						var msg = GO.lang.serverError;

						if(this.reader.jsonData && this.reader.jsonData.feedback)
						{
							msg += '<br /><br />'+this.reader.jsonData.feedback;
						}

						msg += '<br /><br />JsonStore load exception occurred';
						GO.errorDialog.show(msg, response.responseText);
						break;
				}					
			}
		}
		,this);
};

Ext.extend(GO.data.JsonStore, Ext.data.JsonStore, {
	loaded : false	
});
	