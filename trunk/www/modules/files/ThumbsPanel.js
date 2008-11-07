GO.files.ThumbsPanel = Ext.extend(Ext.Panel, {
	store : false,
	initComponent : function(){
		
    var tpl = new Ext.XTemplate('<tpl for=".">',
            '<div class="fs-thumb-wrap" id="{name}">',
		    '<div class="fs-thumb"><img src="{thumb_url}" title="{name}"></div>',
		    '<span class="x-editable">{shortName}</span></div>',
        '</tpl>',
        '<div class="x-clear"></div>');
     
        
     this.items=[this.view = new Ext.DataView({
    //        store: this.store,
            tpl: tpl,
            autoHeight:true,
            multiSelect: true,
            overClass:'fs-view-over',
             selectedClass:'fs-view-selected',
            itemSelector:'div.fs-thumb-wrap',
            emptyText: 'No images to display',

            plugins: [
                new Ext.DataView.DragSelector()
                //new Ext.DataView.LabelEditor({dataIndex: 'name'})
            ],

            prepareData: function(data){
                data.shortName = Ext.util.Format.ellipsis(data.name, 20);
                return data;
            }
        })];
        
     this.autoScroll=true;
        
     GO.files.ThumbsPanel.superclass.initComponent.call(this);
		
	},
	
	onBeforeLoad : function(){		
    this.body.mask(GO.lang.waitMsgLoad);     
	},
	
	onStoreLoad : function(){		
    this.body.unmask();     
	},
	
	setStore : function(store){
		if(this.store)
		{
			this.store.un("beforeload", this.onBeforeLoad, this);
			this.store.un("load", this.onStoreLoad, this);
			this.store=false;
		}
		
		if(store)
		{
			this.store=store;
			this.store.on("befoelad", this.onBeforeLoad, this);
			this.store.on("load", this.onStoreLoad, this);
		}
		
		this.view.setStore(this.store);
		
	},
	/**
	 * Sends a delete request to the remote store. It will send the selected keys in json 
	 * format as a parameter. (delete_keys by default.)
	 * 
	 * @param {Object} options An object which may contain the following properties:<ul>
     * <li><b>deleteParam</b> : String (Optional)<p style="margin-left:1em">The name of the
     * parameter that will send to the store that holds the selected keys in JSON format.
     * Defaults to "delete_keys"</p>
     * </li>
	 * 
	 */
	deleteSelected : function(config){	  
		
		if(!config)
		{
			config=this.deleteConfig;
		}
		
		if(!config['deleteParam'])
		{
			config['deleteParam']='delete_keys';
		}
		
		var selectedRows = [];
		
		var records = this.view.getSelectedRecords();
		for(var i=0;i<records.length;i++)
		{
			selectedRows.push(records[i].data.path);
		}
		
		var params={}
		params[config.deleteParam]=Ext.encode(selectedRows);
		
		var deleteItemsConfig = {
			store:this.store,
			params: params,
			count: selectedRows.length	
		};
		
		if(config.callback)
		{
		  deleteItemsConfig['callback']=config.callback;		
		}
		if(config.success)
		{
		  deleteItemsConfig['success']=config.success;		
		}
		if(config.failure)
		{
		  deleteItemsConfig['failure']=config.failure;		
		}
		if(config.scope)
		{
		  deleteItemsConfig['scope']=config.scope;
		}
		
	
		GO.deleteItems(deleteItemsConfig);		
	}
	
});