GO.moduleManager.addModule('wordpress', GO.panel.IFrameComponent, {
	title : 'Wordpress',
	iconCls : 'go-tab-icon-forum',
	url:GO.settings.modules.wordpress.url+'redirect.php',
	border:false
});
