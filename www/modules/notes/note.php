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
      
 * @package Notes
 * @category Notes
 */
require_once("../../Group-Office.php");

$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('notes');
require_once($GO_LANGUAGE->get_language_file('notes'));

load_basic_controls();

require_once($GO_MODULES->class_path."notes.class.inc");
$notes = new notes();

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';
$note_id = isset($_REQUEST['note_id']) ? $_REQUEST['note_id'] : 0;

if($note_id==0)
{
	$note['name']=$strNewNote;
	$note['user_id']=$GO_SECURITY->user_id;
	$note_id=$notes->add_note($note);
}

$note = $notes->get_note($note_id);


$uniqid=uniqid();

?>
<div id="notedialog__<?php echo $uniqid; ?>">
	<div class="x-dlg-hd">Note</div>	
	    <div class="x-dlg-bd">	   
		    <div id="properties_<?php echo $uniqid; ?>" class="x-dlg-tab">
			 <div id="toolbar_<?php echo $uniqid; ?>"></div>
				<div id="inner_tab_<?php echo $uniqid; ?>" class="inner-tab">		
					<div id="form_<?php echo $uniqid; ?>"></div>		
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



Note = function(){

	var linksPanel;
	var dialog;
	var links_grid;
	var links_ds;
	var links_loaded;

	return {

		init : function(){

			if(!dialog){
				dialog = new Ext.LayoutDialog("notedialog__<?php echo $uniqid; ?>", {
					modal:true,
					shadow:false,
					minWidth:300,
					minHeight:300,
					height:400,
					width:600,
					proxyDrag: true,
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






				note_form = new Ext.form.Form({
					labelWidth: 75, // label settings here cascade unless overridden


					reader: new Ext.data.JsonReader({
						root: 'note',
						id: 'id'
					}, [
					{name: 'name'},
					{name: 'content'}
					])
				});

				var name_field = new Ext.form.TextField({
					fieldLabel: GOlang['strName'],
					name: 'name',
					value: "<?php echo addslashes($note['name']); ?>",
					allowBlank:false,
					style:'width:100%'
				});


				note_form.add(name_field
				,

				new Ext.form.TextArea({
					fieldLabel: GOlang['strText'],
					name: 'content',
					value: "<?php echo addslashes($note['content']); ?>",
					style:'width:100%;height:200px'
				})

				);

				note_form.render('form_<?php echo $uniqid; ?>');


				var notetb = new Ext.Toolbar('toolbar_<?php echo $uniqid; ?>');

				notetb.addButton({
					id: 'save',
					icon: GOimages['save'],
					text: GOlang['cmdSave'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick
				}
				);

				notetb.addButton({
					id: 'link',
					icon: GOimages['link'],
					text: GOlang['cmdLink'],
					cls: 'x-btn-text-icon',
					handler: this.onButtonClick
				}
				);



				notePanel = new Ext.ContentPanel('properties_<?php echo $uniqid; ?>',{
					title: '<?php echo $no_note; ?>',
					//toolbar: notetb,
					autoScroll:true,
				});

				layout.add('center', notePanel);




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
						url: BaseHref+'links_json.php?link_id=<?php echo $note['link_id']; ?>'
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
			}
			dialog.show();

			name_field.focus(true);
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
				fromlinks.push({ 'link_id' : <?php echo $note['link_id']; ?>, 'link_type' : 4 });

				parent.GroupOffice.showLinks({ 'fromlinks': fromlinks, 'callback': function(){links_ds.load()}});
				break;

				case 'unlink':

				var fromlinks = [];
				fromlinks.push({ 'link_id' : <?php echo $note['link_id']; ?>, 'link_type' : 4 });


				var unlinks = [];

				var selectionModel = links_grid.getSelectionModel();
				var records = selectionModel.getSelections();

				for (var i = 0;i<records.length;i++)
				{
					unlinks.push(records[i].data['link_id']);
				}



				if(parent.GroupOffice.unlink(<?php echo $note['link_id']; ?>, unlinks))
				{
					links_ds.load();
				}
				break;

				case 'ok':
				note_form.submit({
					url:'./action.php',
					params: {'task' : 'save','note_id' : <?php echo $note['id']; ?>},

					success:function(form, action){
						//reload grid
						Notes.getDataSource().reload();
					},

					failure: function(form, action) {
						Ext.MessageBox.alert('Failed', action.result.errors);
					}
				});
				dialog.destroy(true);
				break;

				case 'save':

				note_form.submit({
					url:'./action.php',
					params: {'task' : 'save','note_id' : <?php echo $note['id']; ?>},

					success:function(form, action){
						//reload grid
						Notes.getDataSource().reload();
					},

					failure: function(form, action) {
						Ext.MessageBox.alert('Failed', action.result.errors);
					}
				});
				break;
			}
		}
	}
}();

Note.init();
//note_form.load({url: 'notes_json.php?note_id=<?php echo $note_id; ?>', waitMsg:'Loading...'});
</script>
