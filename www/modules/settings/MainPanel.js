GO.settingsmodule.MainPanel = function(config){

	if(!config)
	{
		config = {};
	}
	config.labelWidth=150;
	config.border=false;
	config.padding= 10;
	config.url= GO.settings.modules.settings.url+'data.php',
	config.baseParams={
				task:'load_settings',
				save: false
			};

	config.tbar=new Ext.Toolbar({
		cls:'go-head-tb',
		items: [{
		iconCls: 'btn-save',
		text: GO.lang.cmdSave,
		cls: 'x-btn-text-icon',
		handler: function()
		{
			this.el.mask(GO.lang.waitMsgLoad);				
			this.form.baseParams.save = true;
			this.form.submit({
				success: function(form,action){this.el.unmask();},
				failure: function(form,action){this.el.unmask();},
				scope: this
			});
		},
		scope: this
	},{
		iconCls: 'btn-delete',
		text: GO.lang.cmdCancel,
		cls: 'x-btn-text-icon',
		handler: function()
		{
			this.form.baseParams.save = false;
			this.form.load();
		},
		scope: this
	}]
	});

	GO.settingsmodule.MainPanel.superclass.constructor.call(this, config);
};

Ext.extend(GO.settingsmodule.MainPanel, Ext.FormPanel, {
	afterRender : function()
	{
		GO.settingsmodule.MainPanel.superclass.afterRender.call(this);
		this.form.load();
		this.form.timeout=360;
	}
});

GO.moduleManager.addModule('settings', GO.settingsmodule.MainPanel, {
	title : GO.settingsmodule.lang.title,
	iconCls : 'go-tab-icon-settings',
	admin :true
});