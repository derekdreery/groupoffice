<?php
/**
 * @copyright Copyright Intermesh 2007
 * @version $Revision: 1.47 $ $Date: 2006/11/21 16:25:40 $
 * 
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
      
 * @package users
 * @category users
 */
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('users');
require_once($GO_LANGUAGE->get_language_file('users'));

$user_id=isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;

$uniqid=uniqid();

?>

<div id="userdialog__<?php echo $uniqid; ?>" style="width:100%" height="100%">
	<div class="x-dlg-hd">Note</div>	
	    <div class="x-dlg-bd">	   
		    <iframe style="width:100%" height="100%" frameBorder="0" src="<?php echo $GO_MODULES->modules['users']['url'].'user_wrapped.php?user_id='.$user_id; ?>"></iframe>
	    </div>
	</div>
</div>
<script type="text/javascript">



user = function(){

	var linksPanel;
	var dialog;
	var links_grid;
	var links_ds;
	var links_loaded;

	return {

		init : function(){




			dialog = new Ext.BasicDialog('userdialog__<?php echo $uniqid; ?>', {
				modal:true,
				shadow:false,
				resizable:false

			});
			dialog.addKeyListener(27, this.destroyDialog, this);
			dialog.addButton({
				id: 'ok',
				text: GOlang['cmdOk'],
				handler: this.onButtonClick
			}, this.destroyDialog, this);
			dialog.addButton('Close', this.destroyDialog, this);

			dialog.show();


		},
		destroyDialog : function(){
			if(dialog.isVisible()){
				dialog.animateTarget = null;
				dialog.hide();
			}
			Ext.EventManager.removeResizeListener(dialog.adjustViewport, dialog);
			if(dialog.tabs){
				dialog.tabs.destroy(removeEl);
			}
			Ext.destroy(
			dialog.shim,
			dialog.proxy,
			dialog.close,
			dialog.mask
			);
			if(dialog.dd){
				dialog.dd.unreg();
			}
			if(dialog.buttons){
				for(var i = 0, len = dialog.buttons.length; i < len; i++){
					dialog.buttons[i].destroy();
				}
			}
			dialog.el.removeAllListeners();

			dialog.el.update("");
			dialog.el.remove();

			Ext.DialogManager.unregister(dialog);

		}		
	}
}();

user.init();
</script>
