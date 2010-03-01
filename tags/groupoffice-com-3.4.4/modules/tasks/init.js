/**
 * @copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

Tasks = function(){
	return {

		init : function(){
			
			var mainPanel = new GO.tasks.MainPanel();

			
			var viewport = new Ext.Viewport({
				layout:'fit',				
				items: mainPanel
			});
			
		}
	}
}();
GO.mainLayout.onReady(Tasks.init, Tasks, true);