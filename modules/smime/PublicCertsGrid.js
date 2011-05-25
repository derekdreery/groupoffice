GO.smime.PublicCertsGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.smime.url+ 'json.php',
		baseParams: {
			task: 'public_certs'	    	
		},
		root: 'results',
		id: 'id',
		totalProperty:'total',
		fields: ['id','email'],
		autoLoad:true
	});
	
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header: 'email', 
			dataIndex:  'email'
		}
		]
	});
	
	config.cm=columnModel;
	config.paging=true;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	config.tbar=[{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.deleteSelected();
		},
		scope: this
	}];
	config.listeners={
		scope:this,
		rowdblclick:function(grid, rowIndex){
			var record = grid.getStore().getAt(rowIndex);
			
			if(!this.certWin){
				this.certWin = new GO.Window({
					title:GO.smime.lang.smimeCert,
					width:500,
					height:300,
					closeAction:'hide',
					layout:'fit',
					items:[this.certPanel = new Ext.Panel({
						bodyStyle:'padding:10px'
					})]
				});
			}
												
			this.certWin.show();
												
			this.certPanel.load(GO.settings.modules.smime.url+'verify.php?cert_id='+record.id);
		}
	}
	
	GO.smime.PublicCertsGrid.superclass.constructor.call(this, config);
};

Ext.extend(GO.smime.PublicCertsGrid, GO.grid.GridPanel,{
	
	
	});


GO.smime.PublicCertsWindow = Ext.extend(GO.Window, {
	initComponent : function(){
		
		this.title=GO.smime.lang.pubCerts;
		this.width=400;
		this.height=400;
		this.layout='fit';
		this.items=new GO.smime.PublicCertsGrid();
		this.closeAction='hide';
		
		this.buttons=[{
			text:GO.lang.cmdClose,
			handler:function(){
				this.hide();
			},
			scope:this
		}]
		
		GO.smime.PublicCertsWindow.superclass.initComponent.call(this);
	}
});


GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.EmailClient, {
		initComponent : GO.email.EmailClient.prototype.initComponent.createSequence(function(){
			this.settingsMenu.add({
				iconCls:'btn-pub-certs',
				text:GO.smime.lang.pubCerts,
				handler:function(){
					if(!this.pubCertsWin)
						this.pubCertsWin = new GO.smime.PublicCertsWindow ();
					
					this.pubCertsWin.show();
				}
			})
		})
	})
});
