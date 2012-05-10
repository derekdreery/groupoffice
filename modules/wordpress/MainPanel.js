Ext.namespace('GO.wordpress');

var wpIframe;
var wpConfig = {
	title : GO.wordpress.lang.wordpress,
	iconCls : 'go-tab-icon-wordpress',
	layout:'fit',
	items:[wpIframe = new GO.panel.IFrameComponent({
			itemId:'iframe',
			url:GO.settings.modules.wordpress.url+'redirect.php'
	})],
	border:false
};

var tbarItems = [{
			iconCls:'btn-settings',
			text:GO.wordpress.lang.wordpressAdmin,
			handler:function(){
				wpIframe.el.dom.src=GO.settings.modules.wordpress.url+'redirect.php';
			},
			scope:this
	}];

if(GO.settings.has_admin_permission){
	tbarItems.push('-',{
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

wpConfig.tbar= new Ext.Toolbar({
		cls:'go-head-tb',
		items:tbarItems
	});

GO.moduleManager.addModule('wordpress', Ext.Panel, wpConfig);
