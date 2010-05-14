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
		html:GO.email.defaultProgramInstructions.replace('{url}', GO.settings.modules.email.url+'register_email.php').replace('{product_name}', GO.settings.config.product_name)
	},this.useHtml = new Ext.form.Checkbox({
		boxLabel:GO.email.lang.htmlMarkup,
		hideLabel:true,
		checked:GO.email.useHtmlMarkup,
		name:'use_html_markup'
	})];


	GO.email.SettingsPanel.superclass.constructor.call(this, config);
};

Ext.extend(GO.email.SettingsPanel, Ext.Panel, {
	onLoadSettings : function(action) {

	},

	onSaveSettings : function() {
		GO.email.useHtmlMarkup=this.useHtml.getValue();
	}

});

GO.mainLayout.onReady(function() {
			GO.moduleManager.addSettingsPanel('email',
					GO.email.SettingsPanel);
		});