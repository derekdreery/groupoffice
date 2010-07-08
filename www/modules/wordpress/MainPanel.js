Ext.namespace('GO.wordpress');

GO.moduleManager.addModule('wordpress', Ext.Panel, {
	title : 'Wordpress',
	iconCls : 'go-tab-icon-forum',
	layout:'fit',
	items:[new GO.panel.IFrameComponent({
			url:GO.settings.modules.wordpress.url+'redirect.php'
	})],
	border:false,
	tbar:[{
		iconCls:'btn-settings',
		text:GO.lang.cmdSettings,
		handler:function(){
			if(!this.adminDialog){
				this.adminDialog = new GO.wordpress.AdminDialog();
			}
			this.adminDialog.show();
		}
	}]
});
