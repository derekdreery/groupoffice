GO.email.SettingsPanel = function(config) {
	if (!config) {
		config = {};
	}


	config.autoScroll = true;
	config.border = false;
	config.hideLabel = true;
	config.title = GO.lang.strEmail;
	config.hideMode = 'offsets';
	config.layout = 'form';
	config.bodyStyle = 'padding:5px';
	
	config.items=[{
		xtype:'fieldset',
		title:GO.email.defaultProgram,
		autoHeight:true,		
			html:GO.email.defaultProgramInstructions
			
	}];


	GO.calendar.SettingsPanel.superclass.constructor.call(this, config);
};

Ext.extend(GO.email.SettingsPanel, Ext.Panel, {
	onLoadSettings : function(action) {

	},

	onSaveSettings : function() {
	}

});

GO.mainLayout.onReady(function() {
			GO.moduleManager.addSettingsPanel('email',
					GO.email.SettingsPanel);
		});