Ext.namespace('GO.wordpress');

var wpIframe;
var wpConfig = {
	title : GO.wordpress.lang.wordpress,
	iconCls : 'go-tab-icon-wordpress',
	layout:'fit',
	items:[wpIframe = new GO.panel.IFrameComponent({
			url:GO.settings.modules.wordpress.url+'redirect.php'
	})],
	border:false,
	tbar:[{
			iconCls:'go-module-icon-wordpress',
			text:GO.wordpress.lang.wordpressAdmin,
			handler:function(){
				wpIframe.el.dom.contentDocument.location=GO.settings.modules.wordpress.url+'redirect.php';
			},
			scope:this
	}]
};

if(GO.settings.has_admin_permission){
	wpConfig.tbar.push('-',{
		iconCls:'btn-settings',
		text:GO.lang.cmdSettings,
		handler:function(){
			if(!this.adminDialog){
				this.adminDialog = new GO.wordpress.AdminDialog();
			}
			this.adminDialog.show();
		}
	});
}

GO.moduleManager.addModule('wordpress', Ext.Panel, wpConfig);
