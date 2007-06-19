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

$user = $GO_USERS->get_user($user_id);

?>

<div id="userdialog__<?php echo $uniqid; ?>">
	<div class="x-dlg-hd"><?php echo $strUser; ?></div>	
	    <div class="x-dlg-bd">	   
		    <div id="properties_<?php echo $uniqid; ?>" class="x-dlg-tab">
			 <div id="toolbar_<?php echo $uniqid; ?>"></div>
				<div id="inner_tab_<?php echo $uniqid; ?>" class="inner-tab">		
					<div id="form_<?php echo $uniqid; ?>">
					</div>		
				</div>
			</div>
			<div id="links_tab_<?php echo $uniqid; ?>" class="x-dlg-tab">
			<div id="linkstoolbar_<?php echo $uniqid; ?>"></div>
			<div id="links_grid_div_<?php echo $uniqid; ?>">
			</div>
			</div>
	    </div>
	</div>
</div>
<script type="text/javascript">

Countries = [
<?php

$GO_USERS->get_countries();
while($GO_USERS->next_record())
{
	echo '['.$GO_USERS->f('id').',"'.$GO_USERS->f('name').'"],';
}

?>
];


user = function(){

	var linksPanel;
	var dialog;
	var links_grid;
	var links_ds;
	var links_loaded;
	var user_form;

	return {

		init : function(){



			Sexes = [
			['M', UsersLang['sexes']['M']],
			['F', UsersLang['sexes']['F']]
			];


			dialog = new Ext.LayoutDialog('userdialog__<?php echo $uniqid; ?>', {
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


			var layout = dialog.getLayout();
			layout.beginUpdate();



			user_form = new Ext.form.Form({
				labelAlign: 'right',
				labelWidth: 80
			});

			user_form.column({width:300, labelWidth:75}); // open column, without auto close

			user_form.fieldset(
			{legend:UsersLang['personalinfo']},
			new Ext.form.TextField({
				fieldLabel: UsersLang['first_name'],
				name: 'first_name',
				width:190,
				allowBlank:false,
				value: '<?php echo addslashes($user['first_name']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['middle_name'],
				name: 'middle_name',
				width:190,
				value: '<?php echo addslashes($user['middle_name']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['last_name'],
				name: 'last_name',
				allowBlank:false,
				width:190,
				value: '<?php echo addslashes($user['last_name']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['title'],
				name: 'title',
				width:190,
				value: '<?php echo addslashes($user['title']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['initials'],
				name: 'initials',
				width:190,
				value: '<?php echo addslashes($user['initials']); ?>'
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
				width:190,
				value: '<?php echo addslashes($user['sex']); ?>'
			}),

			new Ext.form.TextField({
				fieldLabel: UsersLang['company'],
				name: 'company',
				width:190,
				value: '<?php echo addslashes($user['company']); ?>'
			}),

			
			new Ext.form.DateField({
				fieldLabel: UsersLang['birthday'],
				name: 'birthday',
				width:190,				
				value: '<?php echo addslashes(db_date_to_date($user['birthday'])); ?>',
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
				width:190,
				value: '<?php echo addslashes($user['email']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['phone'],
				name: 'home_phone',
				width:190,
				value: '<?php echo addslashes($user['home_phone']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['fax'],
				name: 'fax',
				width:190,
				value: '<?php echo addslashes($user['fax']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['cellular'],
				name: 'cellular',
				width:190,
				value: '<?php echo addslashes($user['cellular']); ?>'
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
				width:190,
				value: '<?php echo addslashes($user['address']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['address_no'],
				name: 'address_no',
				width:190,
				value: '<?php echo addslashes($user['address_no']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['zip'],
				name: 'zip',
				width:190,
				value: '<?php echo addslashes($user['zip']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['city'],
				name: 'city',
				width:190,
				value: '<?php echo addslashes($user['city']); ?>'
			}),
			new Ext.form.TextField({
				fieldLabel: UsersLang['state'],
				name: 'state',
				width:190,
				value: '<?php echo addslashes($user['state']); ?>'
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
				width:190,
				value: '<?php echo addslashes($user['country_id']); ?>'
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
				//toolbar: usertb,
				autoScroll:true,
			});

			layout.add('center', userPanel);




			var linkstb = new Ext.Toolbar('linkstoolbar_<?php echo $uniqid; ?>');


			linkstb.addButton({
				id: 'link',
				icon: GOimages['link'],
				text: GOlang['cmdLink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);

			linkstb.addButton({
				id: 'unlink',
				icon: GOimages['unlink'],
				text: GOlang['cmdUnlink'],
				cls: 'x-btn-text-icon',
				handler: this.onButtonClick
			}
			);

			links_ds = new Ext.data.Store({

				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'links_json.php?link_id=<?php echo $user['link_id']; ?>'
				}),

				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'icon'},
				{name: 'link_id'},
				{name: 'name'},
				{name: 'type'},
				{name: 'url'},
				{name: 'mtime'}
				]),

				// turn on remote sorting
				remoteSort: true
			});
			links_ds.setDefaultSort('mtime', 'desc');


			function IconRenderer(src){
				return '<img src=\"' + src +' \" />';
			}

			// the column model has information about grid columns
			// dataIndex maps the column to the specific data field in
			// the data store
			var links_cm = new Ext.grid.ColumnModel([
			{
				header:"",
				width:28,
				dataIndex: 'icon',
				renderer: IconRenderer
			},{
				header: GOlang['strName'],
				dataIndex: 'name',
				css: 'white-space:normal;'
			},{
				header: GOlang['strType'],
				dataIndex: 'type'
			},{
				header: GOlang['strMtime'],
				dataIndex: 'mtime'
			}]);

			// by default columns are sortable
			links_cm.defaultSortable = true;

			// create the editor grid
			links_grid = new Ext.grid.Grid('links_grid_div_<?php echo $uniqid; ?>', {
				ds: links_ds,
				cm: links_cm,
				selModel: new Ext.grid.RowSelectionModel(),
				enableColLock:false,
				loadMask: true,
				displayInfo: true,
				displayMsg: GOlang['displayingItems'],
				emptyMsg: GOlang['strNoItems']

			});

			//grid.addListener("rowclick", this.rowClicked, this);
			links_grid.addListener("rowdblclick", this.rowDoulbleClicked, this);


			linksPanel = new Ext.GridPanel(links_grid, { title: 'Links', toolbar: linkstb});
			layout.add('center', linksPanel);

			linksPanel.on('activate',this.loadLinks);

			layout.getRegion('center').showPanel('properties_<?php echo $uniqid; ?>');

			layout.endUpdate();




			dialog.show();


		},
		loadLinks : function()
		{
			if(!links_loaded)
			{
				links_loaded=true;
				links_ds.load();
				links_grid.render();
			}
		},
		rowDoulbleClicked : function(search_grid, rowClicked, e) {

			var selectionModel = links_grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//parent.Ext.get('dialog').load({url: record.data['url'], scripts: true });
			parent.GroupOffice.showDialog({url: record.data['url'], scripts: true });
		},
		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':

				var fromlinks = [];
				fromlinks.push({ 'link_id' : <?php echo $user['link_id']; ?>, 'link_type' : 8 });

				parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){links_ds.load()}});
				break;

				case 'unlink':

				var fromlinks = [];
				fromlinks.push({ 'link_id' : <?php echo $user['link_id']; ?>, 'link_type' : 8 });


				var unlinks = [];

				var selectionModel = links_grid.getSelectionModel();
				var records = selectionModel.getSelections();

				for (var i = 0;i<records.length;i++)
				{
					unlinks.push(records[i].data['link_id']);
				}



				if(parent.GroupOffice.unlink(<?php echo $user['link_id']; ?>, unlinks))
				{
					links_ds.load();
				}
				break;

				case 'ok':
				user_form.submit({
					url:'./action.php',
					params: {'task' : 'save','user_id' : <?php echo $user['id']; ?>},

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
					params: {'task' : 'save','user_id' : <?php echo $user['id']; ?>},
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
