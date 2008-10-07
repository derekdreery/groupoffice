/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.MainLayout.override({
  createTabPanel : function(items){
		this.tabPanel = new Ext.TabPanel({
      region:'center',
      titlebar: false,
      border:false,
      activeTab:'go-module-panel-'+GO.settings.start_module,
      tabPosition:'top',
      baseCls: 'go-moduletabs',
      items: items,
      layoutOnTabChange:true
  	});
	}
});
 