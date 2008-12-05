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
 
//override Ext functions here
/* bug in 2.2 */
Ext.form.TriggerField.override({
    afterRender : function(){
        Ext.form.TriggerField.superclass.afterRender.call(this);
        var y;
        if(Ext.isIE && !this.hideTrigger && this.el.getY() != (y = this.trigger.getY())){
            this.el.position();
            this.el.setY(y);
        }
    }
});

/**
 * Keep window in viewport and no shadows by default for IE performance
 */

Ext.Window.override({
	 shadow : false,
	 constrainHeader : true
});

/**
 * For editor grid in scrolling view
 */

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

/**
 * Localization
 */
Ext.MessageBox.buttonText.yes = GO.lang['cmdYes'];
Ext.MessageBox.buttonText.no = GO.lang['cmdNo'];
Ext.MessageBox.buttonText.ok = GO.lang['cmdOk'];
Ext.MessageBox.buttonText.cancel = GO.lang['cmdCancel'];


/**
 * Fix for loosing pasted value in HTML editor
 */
Ext.override(Ext.form.HtmlEditor, {
	getValue : function() {
		this.syncValue();
		return Ext.form.HtmlEditor.superclass.getValue.call(this);
	}
});

Ext.override(Ext.DatePicker, {
	startDay: parseInt(GO.settings.first_weekday)
});



/*
 * Fix for hover in panels that stays on when you mouseout on scrollbar
 */
 
Ext.override(Ext.Element, {
	findParent : function(simpleSelector, maxDepth, returnEl){
		var p = this.dom, b = document.body, depth = 0, dq = Ext.DomQuery, stopEl;
		maxDepth = maxDepth || 50;
		if(typeof maxDepth != "number"){
		stopEl = Ext.getDom(maxDepth);
		maxDepth = 10;
	}
	try {
		while(p && p.nodeType == 1 && depth < maxDepth && p != b && p != stopEl){
			if(dq.is(p, simpleSelector)){
				return returnEl ? Ext.get(p) : p;
			}
			depth++;
			p = p.parentNode;
		}
	} catch(e) {};
	return null;
}
});
Ext.override(Ext.grid.GridView, {
	renderUI : function(){
		var header = this.renderHeaders();
		var body = this.templates.body.apply({rows:''});
		var html = this.templates.master.apply({
			body: body,
			header: header
		});
		var g = this.grid;
		g.getGridEl().dom.innerHTML = html;
		this.initElements();
		Ext.fly(this.innerHd).on("click", this.handleHdDown, this);
		this.mainHd.on("mouseover", this.handleHdOver, this);
		this.mainHd.on("mouseout", this.handleHdOut, this);
		this.mainHd.on("mousemove", this.handleHdMove, this);
		this.scroller.on('scroll', this.syncScroll,  this);
		if(g.enableColumnResize !== false){
			this.splitone = new Ext.grid.GridView.SplitDragZone(g, this.mainHd.dom);
		}
		if(g.enableColumnMove){
			this.columnDrag = new Ext.grid.GridView.ColumnDragZone(g, this.innerHd);
			this.columnDrop = new Ext.grid.HeaderDropZone(g, this.mainHd.dom);
		}
		if(g.enableHdMenu !== false){
			if(g.enableColumnHide !== false){
				this.colMenu = new Ext.menu.Menu({id:g.id + "-hcols-menu"});
				this.colMenu.on("beforeshow", this.beforeColMenuShow, this);
				this.colMenu.on("itemclick", this.handleHdMenuClick, this);
			}
			this.hmenu = new Ext.menu.Menu({id: g.id + "-hctx"});
			this.hmenu.add(
				{id:"asc", text: this.sortAscText, cls: "xg-hmenu-sort-asc"},
				{id:"desc", text: this.sortDescText, cls: "xg-hmenu-sort-desc"}
			);
			if(g.enableColumnHide !== false){
				this.hmenu.add('-',
					{id:"columns", text: this.columnsText, menu: this.colMenu, iconCls: 'x-cols-icon'}
				);
			}
			this.hmenu.on("itemclick", this.handleHdMenuClick, this);
		}
		if(g.enableDragDrop || g.enableDrag){
			this.dragZone = new Ext.grid.GridDragZone(g, {
				ddGroup : g.ddGroup || 'GridDD'
			});
		}
		this.updateHeaderSortState();
		if(this.grid.trackMouseOver){
			this.mainBody.on("mouseover", this.onRowOver, this);
			this.mainBody.on("mouseout", this.onRowOut, this);
		}
	},
	initUI : function(grid){
		grid.on("headerclick", this.onHeaderClick, this);
//		if(grid.trackMouseOver){
//			grid.on("mouseover", this.onRowOver, this);
//			grid.on("mouseout", this.onRowOut, this);
//		}
	}
});

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
