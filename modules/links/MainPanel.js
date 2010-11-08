GO.newMenuItems.push({
	text: GO.links.lang.link,
	iconCls: 'has-links',
	handler:function(item, e)
	{
		if(!this.linksDialog)
		{					
			this.linksDialog = new GO.dialog.LinksDialog();
			this.linksDialog.on('link', function()
			{
				item.parentMenu.panel.reload();
			});
		}

		this.linksDialog.setSingleLink(item.parentMenu.link_config.id, item.parentMenu.link_config.type);		
		this.linksDialog.show();
	}
});
