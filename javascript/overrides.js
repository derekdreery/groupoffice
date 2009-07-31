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
 * Scroll menu when higher then the screen is
 *
 **/
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


/**
* for Ubuntu new wave theme
*/

Ext.override(Ext.grid.GridView, {scrollOffset:20});



/* for IE8 menu's
 *
 * Probably not needed in Extjs 3.x
*/
if(Ext.version!='3.0'){
	Ext.override(Ext.menu.Menu, {
			autoWidth : function(){
					var el = this.el, ul = this.ul;
					if(!el){
							return;
					}
					var w = this.width;
					if(w){
							el.setWidth(w);
					}else if(Ext.isIE && !Ext.isIE8){
							el.setWidth(this.minWidth);
							var t = el.dom.offsetWidth; // force recalc
							el.setWidth(ul.getWidth()+el.getFrameWidth("lr"));
					}
			}
	});
}


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

 
//override Ext functions here
/* bug in 2.2 
Ext.form.TriggerField.override({
    afterRender : function(){
        Ext.form.TriggerField.superclass.afterRender.call(this);
        var y;
        if(Ext.isIE && !this.hideTrigger && this.el.getY() != (y = this.trigger.getY())){
            this.el.position();
            this.el.setY(y);
        }
    }
});*/

/**
 * Keep window in viewport and no shadows by default for IE performance
 */

Ext.Window.override({
	 //shadow : false,
	 constrainHeader : true
});

/**
 * For editor grid in scrolling view (Billing module items tab in order dialog)

Not needed in Extjs 3
*/

if(Ext.version!='3.0'){
	Ext.override(Ext.Editor, {
		doAutoSize : function(){
			if(this.autoSize){
				var sz = this.boundEl.getSize(), fs = this.field.getSize();
				switch(this.autoSize){
					case "width":
						this.setSize(sz.width, fs.height);
						break;
					case "height":
						this.setSize(fs.width, sz.height);
						break;
					case "none":
						this.setSize(fs.width, fs.height);
						break;
					default:
						this.setSize(sz.width,  sz.height);
				}
			}
		}
	});
}

/**
 * Localization
 */
Ext.MessageBox.buttonText.yes = GO.lang['cmdYes'];
Ext.MessageBox.buttonText.no = GO.lang['cmdNo'];
Ext.MessageBox.buttonText.ok = GO.lang['cmdOk'];
Ext.MessageBox.buttonText.cancel = GO.lang['cmdCancel'];


/**
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
 * Fix for hover in panels that stays on when you mouseout on scrollbar
 * 
 * https://extjs.com/forum/showthread.php?p=283708#post283708
 *
 * not necessary in Extjs 3
 * 
*/
if(Ext.version!='3.0'){

	Ext.override(Ext.tree.TreeEventModel, {
		initEvents : function(){
			var el = this.tree.getTreeEl();
			el.on('click', this.delegateClick, this);
			if(this.tree.trackMouseOver !== false){
				var innerCt = Ext.fly(el.dom.firstChild);
				innerCt.on('mouseover', this.delegateOver, this);
				innerCt.on('mouseout', this.delegateOut, this);
			}
			el.on('dblclick', this.delegateDblClick, this);
			el.on('contextmenu', this.delegateContextMenu, this);
		}
	});
}

/*
 * End of fix for hover in panels that stays on when you mouseout on scrollbar
 */


/**
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

/**
*End of print elements
*/

/**
 * Width and height not restored in grid
 * 
 * http://extjs.com/forum/showthread.php?t=55086

Not needed in 3.0


if(Ext.version!='3.0'){

	Ext.override(Ext.grid.GridPanel,{
	 applyState : function(state){
					var cm = this.colModel;
					var cs = state.columns;
					if(cs){
							for(var i = 0, len = cs.length; i < len; i++){
									var s = cs[i];
									var c = cm.getColumnById(s.id);
									if(c){
											c.hidden = s.hidden;
											c.width = s.width;
											var oldIndex = cm.getIndexById(s.id);
											if(oldIndex != i){
													cm.moveColumn(oldIndex, i);
											}
									}
							}
					}
					if(state.sort){
							this.store[this.store.remoteSort ? 'setDefaultSort' : 'sort'](state.sort.field, state.sort.direction);
					}
					Ext.apply(this, state);
			}
	});
}	*/
/**
 * End Width and height not restored in grid
 */





/**
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


/**
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

if(Ext.version=='3.0'){
	Ext.override(Ext.Panel,{
		forceLayout:true
	});
}