Ext.ux.IFrameComponent = Ext.extend(Ext.BoxComponent, {
    onRender : function(ct, position){
        this.el = ct.createChild({tag: 'iframe', id: 'iframe-'+ this.id, frameBorder: 0, src: this.url});
    }
});


GO.tools.MainPanel = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	this.runPanel = new Ext.ux.IFrameComponent();
	this.runWindow = new Ext.Window({
		title:GO.tools.lang.scriptOutput,
		width:500,
		height:500,
		maximizable:true,
		closeAction:'hide',
		items:this.runPanel,
		layout:'fit'
	});

	this.cls='tools-panel';
	
		
	this.store = new GO.data.JsonStore({
	    url: GO.settings.modules.tools.url+ 'json.php',
	    baseParams: {
	    	task: 'scripts'  	
	    	},
	    root: 'results',
	    id: 'id',
	    totalProperty:'total',
	    fields: ['name', 'script'],
	    remoteSort: true
	});
	
	var scriptList = new GO.grid.SimpleSelectList({title: GO.tools.lang.scripts, store: this.store});
		
	scriptList.on('click', function(dataview, index){
				/*this.runWindow.show();
				this.runPanel.el.set({'src' : dataview.store.data.items[index].data.script});*/
				window.open(dataview.store.data.items[index].data.script);				
		}, this);
		
	this.items=[
	new Ext.Panel({html: GO.tools.lang.description}),
	new GO.form.HtmlComponent({html:'<br />'}),
	scriptList
	];
	
	GO.tools.MainPanel.superclass.constructor.call(this, config);
	
}
 
Ext.extend(GO.tools.MainPanel, Ext.Panel,{
	afterRender: function(){
		this.store.load();
		GO.tools.MainPanel.superclass.afterRender.call(this);
	}	
});


/*
 * This will add the module to the main tabpanel filled with all the modules
 */
 
GO.moduleManager.addModule('tools', GO.tools.MainPanel, {
	title : GO.tools.lang.tools,
	iconCls : 'go-tab-icon-tools',
	admin :true
});
