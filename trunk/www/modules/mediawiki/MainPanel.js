GO.mediawiki.MainPanel = function(config){

	if(!config)
	{
		config = {};
	}

	config.tbar= new Ext.Toolbar({
		cls: 'go-head-tb',
		items:[new Ext.Button({
			iconCls: 'btn-refresh',
			cls: 'x-btn-text-icon',
			text: GO.lang['cmdRefresh'],
			handler:function(){
				GO.mediawiki.iFrameComponent.setUrl(GO.mediawiki.settings.externalUrl);
			},
			scope: this
		}),
		'-',
		this.settingsButton = new Ext.Button({
			iconCls: 'btn-settings',
			text: GO.lang.administration,
			cls: 'x-btn-text-icon',
			handler: function(){
				if(!this.settingsDialog)
				{
					this.settingsDialog = new GO.mediawiki.SettingsDialog();
				}
				this.settingsDialog.show();
			},
			scope: this
		})
		]
		});

	GO.mediawiki.iFrameComponent = new GO.panel.IFrameComponent({
		url: GO.mediawiki.settings.externalUrl
	});

	config.layout='fit';
	config.items = [GO.mediawiki.iFrameComponent];

	config.title = GO.mediawiki.settings.title;

	GO.mediawiki.MainPanel.superclass.constructor.call(this, config);

}

Ext.extend(GO.mediawiki.MainPanel, Ext.Panel,{

	beforeRender : function() {
		Ext.Ajax.request({
			url: GO.settings.modules.mediawiki.url + 'json.php',
			params: {
				task: 'load_settings'
			},
			scope: this,
			success: function(response,options) {
				var responseParams = Ext.decode(response.responseText);
				if (responseParams.success) {
					GO.mediawiki.settings.externalUrl = responseParams.data.external_url;
					GO.mediawiki.iFrameComponent.setUrl(GO.mediawiki.settings.external_url);
					GO.mediawiki.settings.title = responseParams.data.title;
					this.title = responseParams.title;
				} else {
					Ext.Msg.alert(GO.lang['strError'], responseParams.feedback);
				}
			}
		})
	}

});

GO.moduleManager.addModule('mediawiki', GO.mediawiki.MainPanel, {
	title : 'mediaWiki',
	iconCls : 'go-tab-icon-tasks'
});