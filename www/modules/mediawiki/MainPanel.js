Ext.namespace('GO.mediawiki');

GO.mediawiki.MainPanel = function(config){

	if(!config)
	{
		config = {};
	}

	this.buildForm();

	config.tbar= [new Ext.Button({
			iconCls: 'btn-save',
			cls: 'x-btn-text-icon',
			text: GO.lang['cmdOk'],
			handler:function(){
				Ext.Ajax.request({
					url: GO.settings.modules.mediawiki.url + 'json.php',
					params: {
						task: 'login'
					},
					scope: this
				})
			},
			scope: this
		})];

	config.items = [new GO.panel.SecureIFrameComponent({
			//url:'http://localhost/mediawiki/index.php?wpName=testpersoon',
			//width: '100%',
			//height: '100%'
		})];

	GO.mediawiki.MainPanel.superclass.constructor.call(this, config);

}

Ext.extend(GO.mediawiki.MainPanel, Ext.Panel,{
	afterRender : function()
	{
		GO.mediawiki.MainPanel.superclass.afterRender.call(this);
	},

	buildForm : function() {
		
	}
});

GO.moduleManager.addModule('mediawiki', GO.mediawiki.MainPanel, {
	title : 'mediaWiki',
	iconCls : 'go-tab-icon-tasks'
});