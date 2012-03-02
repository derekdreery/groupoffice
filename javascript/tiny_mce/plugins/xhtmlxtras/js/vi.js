/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: en.js 7708 2011-07-06 14:13:04Z wilmar1980 $
 * @author Dat Pham <datpx@fab.vn> +84907382345
 */

function init() {
	SXE.initElementDialog('del');
	if (SXE.currentAction == "update") {
		setFormValue('datetime', tinyMCEPopup.editor.dom.getAttrib(SXE.updateElement, 'datetime'));
		setFormValue('cite', tinyMCEPopup.editor.dom.getAttrib(SXE.updateElement, 'cite'));
		SXE.showRemoveButton();
	}
}

function setElementAttribs(elm) {
	setAllCommonAttribs(elm);
	setAttrib(elm, 'datetime');
	setAttrib(elm, 'cite');
}

function insertDel() {
	var elm = tinyMCEPopup.editor.dom.getParent(SXE.focusElement, 'DEL');

	tinyMCEPopup.execCommand('mceBeginUndoLevel');
	if (elm == null) {
		var s = SXE.inst.selection.getContent();
		if(s.length > 0) {
			insertInlineElement('del');
			var elementArray = tinymce.grep(SXE.inst.dom.select('del'), function(n) {return n.id == '#sxe_temp_del#';});
			for (var i=0; i<elementArray.length; i++) {
				var elm = elementArray[i];
				setElementAttribs(elm);
			}
		}
	} else {
		setElementAttribs(elm);
	}
	tinyMCEPopup.editor.nodeChanged();
	tinyMCEPopup.execCommand('mceEndUndoLevel');
	tinyMCEPopup.close();
}

function insertInlineElement(en) {
	var ed = tinyMCEPopup.editor, dom = ed.dom;

	ed.getDoc().execCommand('FontName', false, 'mceinline');
	tinymce.each(dom.select(tinymce.isWebKit ? 'span' : 'font'), function(n) {
		if (n.style.fontFamily == 'mceinline' || n.face == 'mceinline')
			dom.replace(dom.create(en), n, 1);
	});
}

function removeDel() {
	SXE.removeElement('del');
	tinyMCEPopup.close();
}

tinyMCEPopup.onInit.add(init);
