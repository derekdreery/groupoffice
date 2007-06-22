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

//$user_id=isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;

$uniqid=uniqid();

//$user = $GO_USERS->get_user($user_id);

?>

<div id="userdialog_<?php echo $uniqid; ?>">
	<div class="x-dlg-hd"><?php echo $strUser; ?></div>	
	    <div id="box-bd_<?php echo $uniqid; ?>" class="x-dlg-bd">	   
		    <div id="properties_<?php echo $uniqid; ?>" class="x-dlg-tab">
		    <div id="toolbar_<?php echo $uniqid; ?>"></div>
			<div id="profileContent"></div>
		    </div>			 
			<div id="links_tab_<?php echo $uniqid; ?>" class="x-dlg-tab">
				<div id="linkstoolbar_<?php echo $uniqid; ?>"></div>
				<div id="links_grid_div_<?php echo $uniqid; ?>"></div>
			</div>
			<div id="access_<?php echo $uniqid; ?>" class="x-dlg-tab"></div>
			<div id="lookandfeel_<?php echo $uniqid; ?>" class="x-dlg-tab"></div>
			<div id="regional_<?php echo $uniqid; ?>" class="x-dlg-tab"></div>
	    </div>
	</div>
</div>
<script type="text/javascript">

Countries = [
<?php

$countries=array();
$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	$countries[] = '['.$GO_USERS->f('id').',"'.$GO_USERS->f('name').'"]';
}
echo implode(',',$countries);
?>
];


user = function(){

	var linksPanel;
	var dialog;

	var user_form;

	var layout;

	var loaded_user_id=0;
	var loaded_link_id=0;
	var linkButton;

	return {

		init : function(){

			dialog = new Ext.LayoutDialog('userdialog_<?php echo $uniqid; ?>', {
				modal:true,
				shadow:false,
				resizable:true,
				proxyDrag: true,
				width:700,
				height:550,
				center: {
					autoScroll:true,
					tabPosition: 'top',
					closeOnTab: true,
					alwaysShowTabs: true
				}

			});
			dialog.addKeyListener(27, dialog.hide, this);


			layout = dialog.getLayout();
			
			

		},
		createTabs : function()
		{
			layout.beginUpdate();

			if(!layout.findPanel('properties_<?php echo $uniqid; ?>'))
			{
				var usertb = new Ext.Toolbar('toolbar_<?php echo $uniqid; ?>');

				linkButton = usertb.addButton({
					id: 'link',
					icon: GOimages['link'],
					text: GOlang['cmdLink'],
					cls: 'x-btn-text-icon',
					handler: function(){
						var fromlinks = [];
						fromlinks.push({ 'link_id' : loaded_link_id, 'link_type' : 8 });

						parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){links_ds.load()}});

					}
				}
				);
				linkButton.disable();


				userPanel = new Ext.ContentPanel('properties_<?php echo $uniqid; ?>',{
					title: '<?php echo $strProperties; ?>',
					autoScroll:true,
					toolbar: usertb,
					resizeEl: 'profileContent',
					fitToFrame:true
				});

				layout.add('center', userPanel);
				userPanel.on('activate',
				function() {
					userPanel.resizeEl.load({
						scripts: true,
						url: 'profile.php',						
						params: {
							user_id: loaded_user_id,
							uniqid: '<?php echo $uniqid; ?>'
						}

					});
				});
				
			}

			if(loaded_user_id>0 && !layout.findPanel('access_<?php echo $uniqid; ?>'))
			{

				linksPanel = links.getGridPanel('<?php echo $uniqid; ?>');
				layout.add('center', linksPanel);
				linksPanel.on('activate', function() {

					user.destroyDialogButtons();
					var dialog = user.getDialog();

					dialog.addButton('Close', dialog.hide, dialog);
				});


				linksPanel.on('activate',function() {

					links.loadLinks(loaded_link_id);

				});

				var permissionsPanel = new Ext.ContentPanel('access_<?php echo $uniqid; ?>',
				{
					title: 'Permissions',
					autoScroll:true
				});

				layout.add('center', permissionsPanel);
				permissionsPanel.on('activate',
				function() {

					permissionsPanel.load({
						scripts: true,
						url: 'permissions.php',
						params: {
							user_id: loaded_user_id,
							uniqid: '<?php echo $uniqid; ?>'
						}

					});

				});

				var lookAndFeelPanel = new Ext.ContentPanel('lookandfeel_<?php echo $uniqid; ?>',
				{
					title: 'Look and feel',
					autoScroll:true
				});

				layout.add('center', lookAndFeelPanel);
				lookAndFeelPanel.on('activate',
				function() {
					lookAndFeelPanel.load({
						scripts: true,
						url: 'look_and_feel.php',
						params: {
							user_id: loaded_user_id,
							uniqid: '<?php echo $uniqid; ?>'
						}

					});
				});

				var regionalPanel = new Ext.ContentPanel('regional_<?php echo $uniqid; ?>',
				{
					title: 'Regional settings',
					autoScroll:true
				});

				layout.add('center', regionalPanel);
				regionalPanel.on('activate',
				function() {
					regionalPanel.load({
						scripts: true,
						url: 'regional.php',
						params: {
							user_id: loaded_user_id,
							uniqid: '<?php echo $uniqid; ?>'
						}

					});
				});
				linkButton.enable();
			}
			
			layout.getRegion('center').showPanel('properties_<?php echo $uniqid; ?>');

			layout.endUpdate();
		},
		removePanels : function()
		{
			var region = layout.getRegion('center');
			
			var panels = [];
			for (var i = 1;i<region.panels.items.length;i++)
			{				
				panels.push(region.panels.items[i].getId());
			}
			for (var i = 0;i<panels.length;i++)
			{				
				region.remove(panels[i]);
			}
			linkButton.disable();
			
		},
		getDialog : function()
		{
			return dialog;
		},
		destroyDialogButtons : function()
		{
			if(typeof(dialog.buttons) != 'undefined')
			{
				for (var i = 0;i<dialog.buttons.length;i++)
				{
					dialog.buttons[i].destroy();
				}
			}
		},
		setUserID : function(user_id)
		{
			if(loaded_user_id>0 && user_id!=loaded_user_id)
			{
				if(user_id==0)
				{
					this.removePanels();
				}				
			}
			
			
			
			loaded_user_id=user_id;
			
			this.createTabs();
			
			if(loaded_user_id==0 && user_id==0)
			{
				userPanel.resizeEl.load({
					scripts: true,
					url: 'profile.php',						
					params: {
						user_id: user_id,
						uniqid: '<?php echo $uniqid; ?>'
					}

				});
			}
		},

		showDialog : function(user_id, link_id){
			
			
			
			this.setUserID(user_id);
			
			//user_form.load({url: 'users_json.php?user_id='+user_id, waitMsg:'Loading...'});
			dialog.show();

		},
		setLinkID : function(link_id)
		{
			loaded_link_id=link_id;
		}
	}
}();

user.init();
<?php
if(isset($_REQUEST['user_id']))
{
	echo 'user.showDialog('.$_REQUEST['user_id'].');';
}
?>
</script>
