GO.moduleManager.addModule('phpbb3', GO.panel.IFrameComponent, {
	title : GO.phpbb3.lang.forum,
	iconCls : 'go-tab-icon-forum',
	url:GO.settings.modules.phpbb3.url+'redirect.php',
	border:false
});
