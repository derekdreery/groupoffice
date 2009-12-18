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

/*Ext.override(Ext.form.TextField, {
	stripCharsRe: /(^\s+|\s+$)/g
});*/

/*
 * Scroll menu when higher then the screen is
 *
 */

Ext.override(Ext.menu.Menu, {
    showAt : function(xy, parentMenu, /* private: */_e){
        this.parentMenu = parentMenu;
        if(!this.el){
            this.render();
        }
        if(_e !== false){
            this.fireEvent("beforeshow", this);
            xy = this.el.adjustForConstraints(xy);
        }
        this.el.setXY(xy);

				//this.el.applyStyles('height: auto;');

        // get max height from body height minus y cordinate from this.el
        var maxHeight = Ext.getBody().getHeight() - xy[1];
        // store orig element height
        if (!this.el.origHeight) {
            this.el.origHeight = this.el.getHeight();
        }
        // if orig height bigger than max height
        if (this.el.origHeight > maxHeight) {
            // set element with max height and apply scrollbar
            this.el.setHeight(maxHeight);
            this.el.applyStyles('overflow-y: auto;');
        } else {
            // set the orig height
            //this.el.setHeight(this.el.origHeight);
        }

        this.el.show();
        this.hidden = false;
        this.focus();
        this.fireEvent("show", this);
    }
});


/*
* for Ubuntu new wave theme
*/

Ext.override(Ext.grid.GridView, {scrollOffset:20});


/* password vtype */ 
 Ext.apply(Ext.form.VTypes, {    
    password : function(val, field) {
        if (field.initialPassField) {
            var pwd = Ext.getCmp(field.initialPassField);
            return (val == pwd.getValue());
        }
        return true;
    },
    passwordText : GO.lang.passwordMatchError
});

 
/**
 * Keep window in viewport and no shadows by default for IE performance
 */

Ext.Window.override({
	 //shadow : false,
	 constrainHeader : true,
	 animCollapse : false
});

/*
 * Localization
 */
Ext.MessageBox.buttonText.yes = GO.lang['cmdYes'];
Ext.MessageBox.buttonText.no = GO.lang['cmdNo'];
Ext.MessageBox.buttonText.ok = GO.lang['cmdOk'];
Ext.MessageBox.buttonText.cancel = GO.lang['cmdCancel'];


/*
 * Fix for loosing pasted value in HTML editor

Ext.override(Ext.form.HtmlEditor, {
	getValue : function() {
		this.syncValue();
		return Ext.form.HtmlEditor.superclass.getValue.call(this);
	}
}); */

Ext.override(Ext.DatePicker, {
	startDay: parseInt(GO.settings.first_weekday)
});


/*
* Print elements
*/
Ext.override(Ext.Element, {
    /**
     * @cfg {string} printCSS The file path of a CSS file for printout.
     */
    printCSS: ''
    /**
     * @cfg {Boolean} printStyle Copy the style attribute of this element to the print iframe.
     */
    , printStyle: false
    /**
     * @property {string} printTitle Page Title for printout. 
     */
    , printTitle: document.title

    /**    
     * Prints this element.
     * 
     * @param config {object} (optional)
     */
    , print: function(config) {
        Ext.apply(this, config);
        
        var el = Ext.get(this.id).dom;
        var c = document.getElementById('printcontainer');
        var iFrame = document.getElementById('printframe');
        
        var strTemplate = '<HTML><HEAD>{0}<TITLE>{1}</TITLE></HEAD><BODY onload="{2}" style="background-color:white;">{3}</BODY></HTML>';
        var strAttr = '';
        var strFormat;
        var strHTML;
        
        //Get rid of the old crap so we don't copy it
        //to our iframe
        if (iFrame != null) c.removeChild(iFrame);
        if (c != null) el.removeChild(c);
        
        //Copy attributes from this element.
        for (var i = 0; i < el.attributes.length; i++) {
            if (Ext.isEmpty(el.attributes[i].value) || el.attributes[i].value.toLowerCase() != 'null') {
                strFormat = Ext.isEmpty(el.attributes[i].value)? '{0}="true" ': '{0}="{1}" ';
                if (this.printStyle? this.printStyle: el.attributes[i].name.toLowerCase() != 'style')
                    strAttr += String.format(strFormat, el.attributes[i].name, el.attributes[i].value);
            }
        }
        
        for(var i=0;i<document.styleSheets.length;i++)
        {
        	this.printCSS+='<link rel="stylesheet" type="text/css" href="'+document.styleSheets[i].href+'"/>';
        }

				this.printCSS+='<style>body{overflow:visible !important;}</style>';
        
        //Build our HTML document for the iframe
        strHTML = String.format(
                strTemplate
                , Ext.isEmpty(this.printCSS)? '#': this.printCSS
                , this.printTitle
                , Ext.isIE? 'document.execCommand(\'print\');': 'window.print();'
                , el.innerHTML
        );
        
        var popup = window.open('about:blank');
        if (!popup.opener) popup.opener = self
				popup.document.write(strHTML);
				popup.document.close();
				popup.focus();
    }
});

Ext.override(Ext.Component, {
    printEl: function(config) {
        this.el.print(Ext.isEmpty(config)? this.initialConfig: config);
    }
    , printBody: function(config) {
        this.body.print(Ext.isEmpty(config)? this.initialConfig: config);
    }
}); 


Ext.override(Ext.data.Connection, {

	doFormUpload : function(o, ps, url){
		var id = Ext.id();
        var frame = document.createElement('iframe');
        frame.id = id;
        frame.name = id;
        frame.className = 'x-hidden';

        document.body.appendChild(frame);

        //Reset the Frame to neutral domain
	    Ext.fly(frame).set({
		    src : Ext.SSL_SECURE_URL
        });

        if(Ext.isIE){
           document.frames[id].name = id;
        }

        var form = Ext.getDom(o.form),
            buf = {
                target: form.target,
                method: form.method,
                encoding: form.encoding,
                enctype: form.enctype,
                action: form.action
            };
        form.target = id;
        form.method = 'POST';
        form.enctype = form.encoding = 'multipart/form-data';
        if(url){
            form.action = url;
        }

        var hiddens, hd;
        if(ps){ // add dynamic params
            hiddens = [];
            ps = Ext.urlDecode(ps, false);
            for(var k in ps){
                if(ps.hasOwnProperty(k)){
                    hd = document.createElement('input');
                    hd.type = 'hidden';
                    hd.name = k;
                    hd.value = ps[k];
                    form.appendChild(hd);
                    hiddens.push(hd);
                }
            }
        }

        function cb(){
            var r = {  // bogus response object
                responseText : '',
                responseXML : null
            };

            r.argument = o ? o.argument : null;

            try { //
                var doc;
                if(Ext.isIE){
                    doc = frame.contentWindow.document;
                }else {
                    doc = (frame.contentDocument || window.frames[id].document);
                }
                if(doc && doc.body){
                    r.responseText = doc.body.innerHTML;
                }
                if(doc && doc.XMLDocument){
                    r.responseXML = doc.XMLDocument;
                }else {
                    r.responseXML = doc;
                }
            }
            catch(e) {
                // ignore
            }

            Ext.EventManager.removeListener(frame, 'load', cb, this);

            this.fireEvent("requestcomplete", this, r, o);

            Ext.callback(o.success, o.scope, [r, o]);
            Ext.callback(o.callback, o.scope, [o, true, r]);

            setTimeout(function(){Ext.removeNode(frame);}, 100);
        }

        Ext.EventManager.on(frame, 'load', cb, this);
        form.submit();

        form.target = buf.target;
        form.method = buf.method;
        form.enctype = buf.enctype;
        form.encoding = buf.encoding;
        form.action = buf.action;

        if(hiddens){ // remove dynamic params
            for(var i = 0, len = hiddens.length; i < len; i++){
                Ext.removeNode(hiddens[i]);
            }
        }

	}
});


/*
 * Catch JSON parsing errors and show error dialog
 * @type 
 */
Ext.decode = Ext.util.JSON.decode = function(json){
	try{
	 return eval("(" + json + ')');
	}
	catch (e)
	{
		GO.errorDialog.show(GO.lang.serverError, json);
	}
};


/*
 * Don't position tooltip outside the screen


Ext.override(Ext.ToolTip,{

	adjustPosition : function(x, y){
    // keep the position from being under the mouse
    var ay = this.targetXY[1], h = this.getSize().height;
    if(this.constrainPosition && y <= ay && (y+h) >= ay){
        y = ay-h-5;
    }
    
    var body = Ext.getBody();
    var bodyHeight = body.getHeight();
    var tipSize = this.getSize();
    
    if(y+tipSize.height>bodyHeight)
    {
    	y=bodyHeight-tipSize.height-5;
    }
    
    if(y<0)
    {
    	y=5;
    }
    
    return {x : x, y: y};
  }    
}); */


/*
 * Fix for "Permission denied to access property 'dom' from a non-chrome context"
 *
 * http://extjs.com/forum/showthread.php?p=366510#post366510
 

Ext.lib.Event.resolveTextNode = Ext.isGecko ? function(node){
	if(!node){
		return;
	}
	var s = HTMLElement.prototype.toString.call(node);
	if(s == '[xpconnect wrapped native prototype]' || s == '[object XULElement]'){
		return;
	}
	return node.nodeType == 3 ? node.parentNode : node;
} : function(node){
	return node && node.nodeType == 3 ? node.parentNode : node;
};

* end fix
 */
