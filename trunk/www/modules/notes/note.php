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
	$note['name']='New note';
	$note['user_id']=$GO_SECURITY->user_id;
	$note_id=$notes->add_note($note);
}

$note = $notes->get_note($note_id);


?>
<div id="notedialog">
	<div class="x-dlg-hd">Note</div>
	
	    <div class="x-dlg-bd">
	   
		    <div id="properties" class="x-dlg-tab">
			 <div id="toolbar"></div>
				<div id="inner-tab" class="inner-tab">		
					<div id="form"></div>		
				</div>
			</div>
			<div id="links" class="x-dlg-tab">
			<div id="linkstoolbar"></div>
			<div class="inner-tab" id="linkGridsDiv">
			</div>
			</div>
	    </div>
	</div>
</div>

<script type="text/javascript">



Note = function(){
	
	var linksPanel;
	var dialog;
	var linkGrids;
	
	return {

		init : function(){

			if(!dialog){
				dialog = new Ext.LayoutDialog("notedialog", {
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
				dialog.addKeyListener(27, dialog.hide, dialog);
				dialog.addButton({
					id: 'ok',
					text: GOlang['cmdOk'],					
					handler: this.onButtonClick
				}, dialog.hide, dialog);
				dialog.addButton('Close', dialog.hide, dialog);
			
				var layout = dialog.getLayout();
				layout.beginUpdate();
				
				
				
				
				
			
				note_form = new Ext.form.Form({
					labelWidth: 75, // label settings here cascade unless overridden
					url:'save-form.php',
			
					reader: new Ext.data.JsonReader({
						root: 'note',
						id: 'id'
					}, [
					{name: 'name', mapping: 'name'},
					{name: 'content', mapping: 'content'}
					])
				});
				note_form.add(
				new Ext.form.TextField({
					fieldLabel: 'Name',
					name: 'name',
					value: "<?php echo addslashes($note['name']); ?>",
					allowBlank:false,
					style:'width:100%'
				}),
			
				new Ext.form.TextArea({
					fieldLabel: 'Text',
					name: 'content',
					value: "<?php echo addslashes($note['content']); ?>",
					style:'width:100%;height:200px'
				})
			
				);
			
				note_form.render('form');
			
			
				var notetb = new Ext.Toolbar('toolbar');
			
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
			
			
			
				notePanel = new Ext.ContentPanel('properties',{
					title: '<?php echo $no_note; ?>',
					//toolbar: notetb,
					autoScroll:true,
				});
			
				layout.add('center', notePanel);
				
				
				
				
				var linkstb = new Ext.Toolbar('linkstoolbar');
			
				
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
				
				
				
				linksPanel = new Ext.ContentPanel('links', { title: 'Links', toolbar: linkstb});
				
				//linksPanel.setUrl({ url: '../../links.php?link_id=<?php echo $note['link_id']; ?>', scripts: true});	
				layout.add('center', linksPanel);
				
				linksPanel.on('activate', this.loadLinks);
				
				
				layout.getRegion('center').showPanel('properties');
			
				layout.endUpdate();
			}
			dialog.show();
		},
		loadLinks : function()
		{
			Ext.get('linkGridsDiv').load({ url: '<?php echo $GO_CONFIG->host; ?>links.php?link_id=<?php echo $note['link_id']; ?>', scripts: true});
		},
		setLinkGrids : function (passedLinkGrids)
		{
			linkGrids=passedLinkGrids;
			
		},
		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':
					
					var fromlinks = [];
					fromlinks.push({ 'link_id' : <?php echo $note['link_id']; ?>, 'link_type' : 4 });				
					
					parent.GroupOffice.showLinks({ 'fromlinks': fromlinks});
				break;
				
				case 'unlink':
					
					var fromlinks = [];
					fromlinks.push({ 'link_id' : <?php echo $note['link_id']; ?>, 'link_type' : 4 });	
					
				
					var unlinks = [];
					
					for (var i = 0;i<linkGrids.length;i++)
					{
						var selectionModel = linkGrids[i].getSelectionModel();
						var records = selectionModel.getSelections();	
						
						for (var i = 0;i<records.length;i++)
						{
							unlinks.push(records[i].data['link_id']);
						}
						
					}			
					
					if(parent.GroupOffice.unlink(<?php echo $note['link_id']; ?>, unlinks))
					{
						this.loadLinks();
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
					dialog.hide();
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
