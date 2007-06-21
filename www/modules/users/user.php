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
				<div id="inner_tab_<?php echo $uniqid; ?>" class="inner-tab">		
					<div id="form_<?php echo $uniqid; ?>"></div>		
				</div>
			</div>
			<div id="links_tab_<?php echo $uniqid; ?>" class="x-dlg-tab">
				<div id="linkstoolbar_<?php echo $uniqid; ?>"></div>
				<div id="links_grid_div_<?php echo $uniqid; ?>"></div>
			</div>
			<div id="access_<?php echo $uniqid; ?>" class="x-dlg-tab"></div>
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
	var reader;
	var layout;

	return {

		init : function(){



			Sexes = [
			['M', UsersLang['sexes']['M']],
			['F', UsersLang['sexes']['F']]
			];


			dialog = new Ext.LayoutDialog('userdialog_<?php echo $uniqid; ?>', {
				modal:true,
				shadow:false,
				resizable:false,
				width:700,
				height:550,
				center: {
					autoScroll:true,
					tabPosition: 'top',
					closeOnTab: true,
					alwaysShowTabs: true
				}

			});
			dialog.addKeyListener(27, this.destroyDialog, this);
			dialog.addButton({
				id: 'ok',
				text: GOlang['cmdOk'],
				handler: this.onButtonClick
			}, this.destroyDialog, this);
			dialog.addButton('Close', this.destroyDialog, this);


			layout = dialog.getLayout();
			layout.beginUpdate();

			reader = new Ext.data.JsonReader({
				root: 'user',
				id: 'id'
			},
			[
			'first_name',
			'middle_name',
			'last_name',
			'title',
			'inititals',
			'sex',
			'company',
			'birthday',
			'email',
			'home_phone',
			'fax',
			'cellular',
			'address',
			'address_no',
			'city',
			'zip',
			'state',
			'country_id',
			'id',
			'link_id'
			]
			);

			user_form = new Ext.form.Form({
				labelAlign: 'right',
				labelWidth: 80,
				waitMsgTarget: 'box-bd_<?php echo $uniqid; ?>',
				reader: reader
			});

			user_form.column({width:300, labelWidth:75}); // open column, without auto close

			user_form.fieldset(
			{legend:UsersLang['personalinfo']},
			new Ext.form.TextField({
				fieldLabel: UsersLang['first_name'],
				name: 'first_name',
				width:190,
				allowBlank:false
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['middle_name'],
				name: 'middle_name',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['last_name'],
				name: 'last_name',
				allowBlank:false,
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['title'],
				name: 'title',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['initials'],
				name: 'initials',
				width:190
			}),

			new Ext.form.ComboBox({
				fieldLabel: UsersLang['sex'],
				hiddenName:'sex',
				store: new Ext.data.SimpleStore({
					fields: ['abbr', 'sex'],
					data : Sexes // from states.js
				}),
				valueField: 'abbr',
				displayField:'sex',
				typeAhead: true,
				mode: 'local',
				triggerAction: 'all',
				emptyText:GOlang['strPleaseSelect'],
				selectOnFocus:true,
				width:190
			}),

			new Ext.form.TextField({
				fieldLabel: UsersLang['company'],
				name: 'company',
				width:190
			}),


			new Ext.form.DateField({
				fieldLabel: UsersLang['birthday'],
				name: 'birthday',
				width:190,
				format: GOsettings['date_format']
			})
			);




			user_form.fieldset(
			{legend:UsersLang['contactinfo']},

			new Ext.form.TextField({
				fieldLabel: UsersLang['email'],
				name: 'email',
				vtype:'email',
				allowBlank:false,
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['phone'],
				name: 'home_phone',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['fax'],
				name: 'fax',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['cellular'],
				name: 'cellular',
				width:190
			})
			);

			user_form.end();

			user_form.column(
			{width:300, style:'margin-left:10px', clear:true}
			);

			user_form.fieldset(
			{legend: UsersLang['address']},

			new Ext.form.TextField({
				fieldLabel: UsersLang['street'],
				name: 'address',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['address_no'],
				name: 'address_no',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['zip'],
				name: 'zip',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['city'],
				name: 'city',
				width:190
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['state'],
				name: 'state',
				width:190
			}),
			new Ext.form.ComboBox({
				fieldLabel: UsersLang['country'],
				hiddenName:'country_id',
				store: new Ext.data.SimpleStore({
					fields: ['id', 'name'],
					data : Countries
				}),
				displayField:'name',
				valueField: 'id',
				typeAhead: true,
				mode: 'local',
				triggerAction: 'all',
				emptyText:GOlang['strPleaseSelect'],
				selectOnFocus:true,
				width:190
			})
			);



			user_form.end();

			user_form.render('form_<?php echo $uniqid; ?>');




			var usertb = new Ext.Toolbar('toolbar_<?php echo $uniqid; ?>');

			usertb.addButton({
				id: 'save',
				icon: GOimages['save'],
				text: GOlang['cmdSave'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);

			usertb.addButton({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);



			userPanel = new Ext.ContentPanel('properties_<?php echo $uniqid; ?>',{
				title: '<?php echo $strProperties; ?>',
				toolbar: usertb,
				autoScroll:true
			});

			layout.add('center', userPanel);

			linksPanel = links.getGridPanel('<?php echo $uniqid; ?>');
			layout.add('center', linksPanel);
			linksPanel.on('activate',function() { links.loadLinks(reader.jsonData.user[0]['link_id']); });
			
			var permissionsPanel = new Ext.ContentPanel('access_<?php echo $uniqid; ?>',
			{				
				title: 'Access permissions',
				autoScroll:true
			});
			
			layout.add('center', permissionsPanel);
			permissionsPanel.on('activate', 
				function() { 
					permissionsPanel.load({
							scripts: true, 
							url: 'permissions.php',
							params: {
								user_id: reader.jsonData.user[0]['id'],
								uniqid: '<?php echo $uniqid; ?>'
							}
							
						});
				});

			
			layout.endUpdate();

		},
		getDialog : function()
		{
			return dialog;
		},
		destroyDialogButtons : function()
		{
			for (var i = 0;i<dialog.buttons.length;i++)
			{
				dialog.buttons[i].destroy();
			}
		},
		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':

				var fromlinks = [];
				fromlinks.push({ 'link_id' : reader.jsonData.user[0]['link_id'], 'link_type' : 8 });

				parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){links_ds.load()}});
				break;

				case 'unlink':

				var fromlinks = [];
				fromlinks.push({ 'link_id' : reader.jsonData.user[0]['link_id'], 'link_type' : 8 });


				var unlinks = [];

				var selectionModel = links_grid.getSelectionModel();
				var records = selectionModel.getSelections();

				for (var i = 0;i<records.length;i++)
				{
					unlinks.push(records[i].data['link_id']);
				}



				if(parent.GroupOffice.unlink(link_id, unlinks))
				{
					links_ds.load();
				}
				break;

				case 'ok':
				user_form.submit({
					url:'./action.php',
					params: {'task' : 'save','user_id' : reader.jsonData.user[0]['id']},

					success:function(form, action){
						//reload grid
						//users.getDataSource().reload();
					},

					failure: function(form, action) {
						Ext.MessageBox.alert('Failed', action.result.errors);
					}
				});
				dialog.destroy(true);
				break;

				case 'save':

				user_form.submit({
					url:'./action.php',
					params: {'task' : 'save','user_id' : reader.jsonData.user[0]['id']},
					waitMsg:'Saving...',
					success:function(form, action){
						//reload grid
						//users.getDataSource().reload();
					},

					failure: function(form, action) {
						Ext.MessageBox.alert('Error', action.result.errors);
					}
				});
				break;
			}
		},
		showDialog : function(user_id){
			layout.getRegion('center').showPanel('properties_<?php echo $uniqid; ?>');
			user_form.load({url: 'users_json.php?user_id='+user_id, waitMsg:'Loading...'});
			dialog.show();

		},
		destroyDialog : function(){
			/*if(dialog.isVisible()){
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

			Ext.DialogManager.unregister(dialog);*/
			dialog.hide();
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
