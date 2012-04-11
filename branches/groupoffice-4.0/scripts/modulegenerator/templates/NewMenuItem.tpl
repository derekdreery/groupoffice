GO.newMenuItems.push({
	text: GO.{module}.lang.{friendly_single_js},
	iconCls: 'go-link-icon-{link_type}',
	handler:function(item, e){		
		GO.{module}.{friendly_single_js}Dialog.show(0, {
			link_config: item.parentMenu.link_config			
		});
	}
});
