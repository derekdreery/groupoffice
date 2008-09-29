/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: MainLayout.js 2948 2008-09-03 07:16:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
 /*
  * Example on how to override theme layout 
  * 
  * The main file is at javascript/MainLayout.js
  */

 GO.MainLayout.override({
    fireReady : function(){			
			this.fireEvent('ready', this);
		 	this.ready=true;	
		}
});
 