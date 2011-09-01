/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 GO.NewMenuButton = Ext.extend(Ext.Button, {
	panel : false,
	initComponent : function(){
		
		this.menu = new Ext.menu.Menu({				
				items:GO.newMenuItems,
				panel:this.panel
			});
		this.text=GO.lang.cmdNew;
		this.iconCls='btn-add';			
		this.disabled=true;
		this.hidden=GO.newMenuItems.length==0;
			
		GO.NewMenuButton.superclass.initComponent.call(this);		
	},
	
	setLinkConfig : function(config){
		this.menu.linkConfig=config;		
		//this.menu.linkConfig.type_id=config.type+':'+config.id;
		
		if(!this.menu.linkConfig.scope)
		{
			this.menu.linkConfig.scope=this;
		}
		
		if(this.menu.linkConfig.callback)
		{
			this.menu.linkConfig.callback=this.menu.linkConfig.callback.createDelegate(this.menu.linkConfig.scope);
		}
		
		this.setDisabled(GO.util.empty(config.linkModelId));
	}	
	
});


 GO.NewMenuItem = Ext.extend(Ext.menu.Item, {
	initComponent : function(){

		this.menu = new Ext.menu.Menu({
				items:GO.newMenuItems
			});
		this.text=GO.lang.cmdNew;
		this.iconCls='btn-add';
		this.disabled=true;
		this.hidden=GO.newMenuItems.length==0;

		GO.NewMenuButton.superclass.initComponent.call(this);
	},

	setLinkConfig : function(config){
		this.menu.linkConfig=config;
		this.menu.linkConfig.modelNameAndId=config.linkModelName+':'+config.linkModelId;

		if(!this.menu.linkConfig.scope)
		{
			this.menu.linkConfig.scope=this;
		}

		if(this.menu.linkConfig.callback)
		{
			this.menu.linkConfig.callback=this.menu.linkConfig.callback.createDelegate(this.menu.linkConfig.scope);
		}

		this.setDisabled(GO.util.empty(config.linkModelId));
	}

});


GO.mainLayout.onReady(function(){
	GO.newMenuItems.unshift({
		text: GO.lang.link,
		iconCls: 'has-links',
		handler:function(item, e)
		{
			if(!this.linksDialog)
			{
				this.linksDialog = new GO.dialog.LinksDialog();
				this.linksDialog.on('link', function()
				{
					if(item.parentMenu.panel)
						item.parentMenu.panel.reload();
				});
			}

			this.linksDialog.setSingleLink(item.parentMenu.linkConfig.linkModelId, item.parentMenu.linkConfig.linkModelName);
			this.linksDialog.show();
		}
	});
});