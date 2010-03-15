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
		this.menu.link_config=config;		
		this.menu.link_config.type_id=config.type+':'+config.id;
		
		if(!this.menu.link_config.scope)
		{
			this.menu.link_config.scope=this;
		}
		
		if(this.menu.link_config.callback)
		{
			this.menu.link_config.callback=this.menu.link_config.callback.createDelegate(this.menu.link_config.scope);
		}
		
		this.setDisabled(false);
	}	
	
});