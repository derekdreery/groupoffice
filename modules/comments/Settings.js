GO.comments.SettingsPanel = function(config) {
	if (!config) 
		config = {};

	config.autoScroll = true;
	config.border = false;
	config.hideLabel = true;
	config.title = GO.comments.lang.comments;
	config.hideMode = 'offsets';
	config.layout = 'form';
	config.bodyStyle = 'padding:5px';
	config.labelWidth=150;
	
	config.items=[
		{
			xtype:'fieldset',
			title:GO.comments.lang.readMore,
			autoHeight:true,
			items:[
				this.useReadmore = new Ext.ux.form.XCheckbox({
					boxLabel:GO.comments.lang.enableReadMore,
					hideLabel:true,
					checked:GO.comments.enableReadMore,
					name:'comments_enable_read_more'
				})
			]
		}
		
	];

	GO.comments.SettingsPanel.superclass.constructor.call(this, config);
};

Ext.extend(GO.comments.SettingsPanel, Ext.Panel, {
	onLoadSettings : function(action) {		
		this.useReadmore.setValue(action.result.data.comments_enable_read_more);
	}
});

GO.mainLayout.onReady(function() {
	GO.moduleManager.addSettingsPanel('comments',
		GO.comments.SettingsPanel);
});