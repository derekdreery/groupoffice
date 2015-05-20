GO.dropbox.SettingsPanel = function(config) {
	if (!config) 
		config = {};

	config.title = GO.dropbox.lang.name;
	config.bodyStyle = 'padding:5px';
	
	config.items=[{
			xtype:'htmlcomponent',
			html:GO.dropbox.lang.instructions
	},new Ext.Button({
			style:'margin-top:20px',
			text:GO.dropbox.lang.connect,
			handler:function(){
				window.open(GO.url('dropbox/auth/start'));
			},
			scope:this
	}),new Ext.Button({
			style:'margin-top:20px',
			text:GO.dropbox.lang.disconnect,
			handler:function(){
				GO.request({
					url:'dropbox/auth/disconnect',
					success:function(){
						Ext.Msg.alert(GO.lang.strSuccess, GO.dropbox.lang.disconnectedSuccessfully);
					}
				});
			},
			scope:this
	})];

	GO.dropbox.SettingsPanel.superclass.constructor.call(this, config);
};

Ext.extend(GO.dropbox.SettingsPanel, Ext.Panel, {
	onLoadSettings : function(action) {		
	
	}
});

GO.mainLayout.onReady(function() {
	GO.moduleManager.addSettingsPanel('dropbox',
		GO.dropbox.SettingsPanel);
});