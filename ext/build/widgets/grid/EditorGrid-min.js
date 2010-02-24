/*
 * Ext JS Library 2.3.0
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.grid.EditorGridPanel=Ext.extend(Ext.grid.GridPanel,{clicksToEdit:2,isEditor:true,detectEdit:false,autoEncode:false,trackMouseOver:false,initComponent:function(){Ext.grid.EditorGridPanel.superclass.initComponent.call(this);if(!this.selModel){this.selModel=new Ext.grid.CellSelectionModel();}
this.activeEditor=null;this.addEvents("beforeedit","afteredit","validateedit");},initEvents:function(){Ext.grid.EditorGridPanel.superclass.initEvents.call(this);this.on("bodyscroll",this.stopEditing,this,[true]);this.on("columnresize",this.stopEditing,this,[true]);if(this.clicksToEdit==1){this.on("cellclick",this.onCellDblClick,this);}else{if(this.clicksToEdit=='auto'&&this.view.mainBody){this.view.mainBody.on("mousedown",this.onAutoEditClick,this);}
this.on("celldblclick",this.onCellDblClick,this);}},onCellDblClick:function(g,row,col){this.startEditing(row,col);},onAutoEditClick:function(e,t){if(e.button!==0){return;}
var row=this.view.findRowIndex(t);var col=this.view.findCellIndex(t);if(row!==false&&col!==false){this.stopEditing();if(this.selModel.getSelectedCell){var sc=this.selModel.getSelectedCell();if(sc&&sc[0]===row&&sc[1]===col){this.startEditing(row,col);}}else{if(this.selModel.isSelected(row)){this.startEditing(row,col);}}}},onEditComplete:function(ed,value,startValue){this.editing=false;this.activeEditor=null;ed.un("specialkey",this.selModel.onEditorKey,this.selModel);var r=ed.record;var field=this.colModel.getDataIndex(ed.col);value=this.postEditValue(value,startValue,r,field);if(String(value)!==String(startValue)){var e={grid:this,record:r,field:field,originalValue:startValue,value:value,row:ed.row,column:ed.col,cancel:false};if(this.fireEvent("validateedit",e)!==false&&!e.cancel){r.set(field,e.value);delete e.cancel;this.fireEvent("afteredit",e);}}
this.view.focusCell(ed.row,ed.col);},startEditing:function(row,col){this.stopEditing();if(this.colModel.isCellEditable(col,row)){this.view.ensureVisible(row,col,true);var r=this.store.getAt(row);var field=this.colModel.getDataIndex(col);var e={grid:this,record:r,field:field,value:r.data[field],row:row,column:col,cancel:false};if(this.fireEvent("beforeedit",e)!==false&&!e.cancel){this.editing=true;var ed=this.colModel.getCellEditor(col,row);if(!ed.rendered){ed.render(this.view.getEditorParent(ed));}
(function(){ed.row=row;ed.col=col;ed.record=r;ed.on("complete",this.onEditComplete,this,{single:true});ed.on("specialkey",this.selModel.onEditorKey,this.selModel);this.activeEditor=ed;var v=this.preEditValue(r,field);ed.startEdit(this.view.getCell(row,col).firstChild,v===undefined?'':v);}).defer(50,this);}}},preEditValue:function(r,field){var value=r.data[field];return this.autoEncode&&typeof value=='string'?Ext.util.Format.htmlDecode(value):value;},postEditValue:function(value,originalValue,r,field){return this.autoEncode&&typeof value=='string'?Ext.util.Format.htmlEncode(value):value;},stopEditing:function(cancel){if(this.activeEditor){this.activeEditor[cancel===true?'cancelEdit':'completeEdit']();}
this.activeEditor=null;}});Ext.reg('editorgrid',Ext.grid.EditorGridPanel);