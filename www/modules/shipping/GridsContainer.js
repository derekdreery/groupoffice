GO.shipping.GridsContainer = function(config)
	{
		if(!config)
		{
			config = {};
		}

		//config.title = GO.shipping.lang.shipping;
		//config.plain = true;
		config.layout = 'card';
		config.layoutConfig = {
			deferredRender : true
		};
		config.activeItem = 0;
		//config.border = false;
		//config.closable = false;
		config.deferredRender = true;
		config.items = [
			GO.shipping.jobsGrid = new GO.shipping.JobsGrid({menu:config.menu}),
			GO.shipping.packagesGrid = new GO.shipping.PackagesGrid({menu:config.menu})
		];
/*
		var vp = new Ext.Viewport({
			layout : 'fit',
			items : GO.shipping.packagesGrid
		});
*/
		GO.shipping.GridsContainer.superclass.constructor.call(this, config);

	};


Ext.extend(GO.shipping.GridsContainer, Ext.Panel, {

	});