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

?>

<div class="x-dlg-hd">Note</div>

    <div class="x-dlg-bd">
	    <div id="properties" class="x-dlg-tab">
		<div id="toolbar"></div>
			<div id="inner-tab" class="inner-tab">		
				<div id="form"></div>		
			</div>
		</div>
		<div id="links" class="x-dlg-tab">
		<div class="inner-tab">
		</div>
		</div>
    </div>
</div>


<script type="text/javascript">

dialog = new Ext.LayoutDialog("dialog", {
	modal:true,
	shadow:true,
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
dialog.addButton('Submit', dialog.hide, dialog);
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
	allowBlank:false,
	style:'width:400px'
}),

new Ext.form.TextArea({
	fieldLabel: 'Text',
	name: 'content',
	style:'width:400px;height:200px'
})

);

note_form.render('form');


var notetb = new Ext.Toolbar('toolbar');

save_button =notetb.addButton({
	id: 'save',
	icon: GOimages['save'],
	text: GOlang['cmdSave'],
	cls: 'x-btn-text-icon',
	handler: this.onButtonClick
}
);



notePanel = new Ext.ContentPanel('properties',{
	title: NotesLang['note'],
	toolbar: notetb,
	resizeEl: 'inner-tab',
	autoScroll:true,
	fitToFrame:true
});

layout.add('center', notePanel);


linksPanel = new Ext.ContentPanel('links', { title: 'Links'});



layout.add('center', linksPanel);
layout.getRegion('center').showPanel('properties');

layout.endUpdate();

dialog.show();

</script>
