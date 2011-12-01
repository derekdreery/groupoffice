GO.gnupg.KeysGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.gnupg.url+ 'json.php',
	    baseParams: {
	    	task: 'keys'	    	
	    	},
	    root: 'results',
	    id: 'fingerprint',
	    totalProperty:'total',
	    fields: ['fingerprint', 'id', 'uid', 'type']
	});
	
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header: 'id', 
			dataIndex: 'id'
		},{
			header: 'uid', 
			dataIndex: 'uid'
		},{
			header: 'type', 
			dataIndex: 'type'
		}
	]
	});
	
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{
		iconCls: 'btn-add',							
		text: GO.gnupg.lang.genKey,
		cls: 'x-btn-text-icon',
		handler: function(){				
    		if(!this.genKeyDialog)
    		{
    			this.genKeyDialog = new GO.gnupg.GenKeyDialog();
    			this.genKeyDialog.on('save', function(){this.store.reload();}, this);
    		}
    		this.genKeyDialog.show();
		},
		scope: this
	},{
		iconCls: 'btn-add',							
		text: GO.lang['cmdImport'],
		cls: 'x-btn-text-icon',
		handler: function(){				
   		if(!this.importKeyDialog)
  		{
  			this.importKeyDialog = new GO.gnupg.ImportKeyDialog();
  			this.importKeyDialog.on('save', function(){this.store.reload();}, this);
  		}
  		this.importKeyDialog.show();
		},
		scope: this
	},{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.deleteSelected();
		},
		scope: this
	},{
		iconCls:'btn-export',
		text:GO.lang.cmdExport,
		cls: 'x-btn-text-icon',
		handler: function(){
			
			var record = this.getSelectionModel().getSelected();
			
			if(!record)
			{
				alert( GO.lang['noItemSelected']);
			}else
			{
				document.location=GO.settings.modules.gnupg.url+'export.php?fingerprint='+record.get('fingerprint');
			}
		},
		scope: this
	
	},{
			iconCls: 'btn-email',
			text: GO.gnupg.lang.sendPublicKey,
			cls: 'x-btn-text-icon',
			handler: function(){
				var record = this.getSelectionModel().getSelected();
				
				if(!record)
				{
					alert( GO.lang['noItemSelected']);
				}else
				{
					GO.email.showComposer({
						loadUrl: GO.settings.modules.gnupg.url+'json.php',
						loadParams:{task:'send_key',fingerprint: record.get('fingerprint')}		
					});
				}
				
			},
			scope:this						
		},{
		iconCls: 'btn-refresh',
		text: GO.lang['cmdRefresh'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.store.load();
		},
		scope: this
	}/*,{
		iconCls:'btn-sign-key',
		text:GO.gnupg.lang.signKey,
		cls: 'x-btn-text-icon',
		handler: function(){
			
			var record = this.getSelectionModel().getSelected();
			
			if(!record)
			{
				alert( GO.lang['noItemSelected']);
			}else
			{
				if(!this.signKeyDialog)
    		{
    			this.signKeyDialog = new GO.gnupg.SignKeyDialog();
    			this.signKeyDialog.on('save', function(){this.store.reload();}, this);
    		}
    		this.signKeyDialog.show(record.get('uid'));
			}
		},
		scope: this
	
	}*/];
	
	GO.gnupg.KeysGrid.superclass.constructor.call(this, config);
};

Ext.extend(GO.gnupg.KeysGrid, GO.grid.GridPanel,{
	
	
});