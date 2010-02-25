/*
 * Ext JS Library 2.3.0
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.grid.GroupingView=Ext.extend(Ext.grid.GridView,{hideGroupedColumn:false,showGroupName:true,startCollapsed:false,enableGrouping:true,enableGroupingMenu:true,enableNoGroups:true,emptyGroupText:'(None)',ignoreAdd:false,groupTextTpl:'{text}',gidSeed:1000,initTemplates:function(){Ext.grid.GroupingView.superclass.initTemplates.call(this);this.state={};var sm=this.grid.getSelectionModel();sm.on(sm.selectRow?'beforerowselect':'beforecellselect',this.onBeforeRowSelect,this);if(!this.startGroup){this.startGroup=new Ext.XTemplate('<div id="{groupId}" class="x-grid-group {cls}">','<div id="{groupId}-hd" class="x-grid-group-hd" style="{style}"><div>',this.groupTextTpl,'</div></div>','<div id="{groupId}-bd" class="x-grid-group-body">');}
this.startGroup.compile();this.endGroup='</div></div>';},findGroup:function(el){return Ext.fly(el).up('.x-grid-group',this.mainBody.dom);},getGroups:function(){return this.hasRows()?this.mainBody.dom.childNodes:[];},onAdd:function(){if(this.enableGrouping&&!this.ignoreAdd){var ss=this.getScrollState();this.refresh();this.restoreScroll(ss);}else if(!this.enableGrouping){Ext.grid.GroupingView.superclass.onAdd.apply(this,arguments);}},onRemove:function(ds,record,index,isUpdate){Ext.grid.GroupingView.superclass.onRemove.apply(this,arguments);var g=document.getElementById(record._groupId);if(g&&g.childNodes[1].childNodes.length<1){Ext.removeNode(g);}
this.applyEmptyText();},refreshRow:function(record){if(this.ds.getCount()==1){this.refresh();}else{this.isUpdating=true;Ext.grid.GroupingView.superclass.refreshRow.apply(this,arguments);this.isUpdating=false;}},beforeMenuShow:function(){var field=this.getGroupField();var g=this.hmenu.items.get('groupBy');if(g){g.setDisabled(this.cm.config[this.hdCtxIndex].groupable===false);}
var s=this.hmenu.items.get('showGroups');if(s){s.setDisabled(!field&&this.cm.config[this.hdCtxIndex].groupable===false);s.setChecked(!!field,true);}},renderUI:function(){Ext.grid.GroupingView.superclass.renderUI.call(this);this.mainBody.on('mousedown',this.interceptMouse,this);if(this.enableGroupingMenu&&this.hmenu){this.hmenu.add('-',{id:'groupBy',text:this.groupByText,handler:this.onGroupByClick,scope:this,iconCls:'x-group-by-icon'});if(this.enableNoGroups){this.hmenu.add({id:'showGroups',text:this.showGroupsText,checked:true,checkHandler:this.onShowGroupsClick,scope:this});}
this.hmenu.on('beforeshow',this.beforeMenuShow,this);}},onGroupByClick:function(){this.grid.store.groupBy(this.cm.getDataIndex(this.hdCtxIndex));this.beforeMenuShow();},onShowGroupsClick:function(mi,checked){if(checked){this.onGroupByClick();}else{this.grid.store.clearGrouping();}},toggleGroup:function(group,expanded){this.grid.stopEditing(true);group=Ext.getDom(group);var gel=Ext.fly(group);expanded=expanded!==undefined?expanded:gel.hasClass('x-grid-group-collapsed');this.state[gel.dom.id]=expanded;gel[expanded?'removeClass':'addClass']('x-grid-group-collapsed');},toggleAllGroups:function(expanded){var groups=this.getGroups();for(var i=0,len=groups.length;i<len;i++){this.toggleGroup(groups[i],expanded);}},expandAllGroups:function(){this.toggleAllGroups(true);},collapseAllGroups:function(){this.toggleAllGroups(false);},interceptMouse:function(e){var hd=e.getTarget('.x-grid-group-hd',this.mainBody);if(hd){e.stopEvent();this.toggleGroup(hd.parentNode);}},getGroup:function(v,r,groupRenderer,rowIndex,colIndex,ds){var g=groupRenderer?groupRenderer(v,{},r,rowIndex,colIndex,ds):String(v);if(g===''){g=this.cm.config[colIndex].emptyGroupText||this.emptyGroupText;}
return g;},getGroupField:function(){return this.grid.store.getGroupState();},afterRender:function(){Ext.grid.GroupingView.superclass.afterRender.call(this);if(this.grid.deferRowRender){this.updateGroupWidths();}},renderRows:function(){var groupField=this.getGroupField();var eg=!!groupField;if(this.hideGroupedColumn){var colIndex=this.cm.findColumnIndex(groupField);if(!eg&&this.lastGroupField!==undefined){this.mainBody.update('');this.cm.setHidden(this.cm.findColumnIndex(this.lastGroupField),false);delete this.lastGroupField;}else if(eg&&this.lastGroupField===undefined){this.lastGroupField=groupField;this.cm.setHidden(colIndex,true);}else if(eg&&this.lastGroupField!==undefined&&groupField!==this.lastGroupField){this.mainBody.update('');var oldIndex=this.cm.findColumnIndex(this.lastGroupField);this.cm.setHidden(oldIndex,false);this.lastGroupField=groupField;this.cm.setHidden(colIndex,true);}}
return Ext.grid.GroupingView.superclass.renderRows.apply(this,arguments);},doRender:function(cs,rs,ds,startRow,colCount,stripe){if(rs.length<1){return'';}
var groupField=this.getGroupField();var colIndex=this.cm.findColumnIndex(groupField);this.enableGrouping=!!groupField;if(!this.enableGrouping||this.isUpdating){return Ext.grid.GroupingView.superclass.doRender.apply(this,arguments);}
var gstyle='width:'+this.getTotalWidth()+';';var gidPrefix=this.grid.getGridEl().id;var cfg=this.cm.config[colIndex];var groupRenderer=cfg.groupRenderer||cfg.renderer;var prefix=this.showGroupName?(cfg.groupName||cfg.header)+': ':'';var groups=[],curGroup,i,len,gid;for(i=0,len=rs.length;i<len;i++){var rowIndex=startRow+i;var r=rs[i],gvalue=r.data[groupField],g=this.getGroup(gvalue,r,groupRenderer,rowIndex,colIndex,ds);if(!curGroup||curGroup.group!=g){gid=gidPrefix+'-gp-'+groupField+'-'+Ext.util.Format.htmlEncode(g);var isCollapsed=typeof this.state[gid]!=='undefined'?!this.state[gid]:this.startCollapsed;var gcls=isCollapsed?'x-grid-group-collapsed':'';curGroup={group:g,gvalue:gvalue,text:prefix+g,groupId:gid,startRow:rowIndex,rs:[r],cls:gcls,style:gstyle};groups.push(curGroup);}else{curGroup.rs.push(r);}
r._groupId=gid;}
var buf=[];for(i=0,len=groups.length;i<len;i++){var g=groups[i];this.doGroupStart(buf,g,cs,ds,colCount);buf[buf.length]=Ext.grid.GroupingView.superclass.doRender.call(this,cs,g.rs,ds,g.startRow,colCount,stripe);this.doGroupEnd(buf,g,cs,ds,colCount);}
return buf.join('');},getGroupId:function(value){var gidPrefix=this.grid.getGridEl().id;var groupField=this.getGroupField();var colIndex=this.cm.findColumnIndex(groupField);var cfg=this.cm.config[colIndex];var groupRenderer=cfg.groupRenderer||cfg.renderer;var gtext=this.getGroup(value,{data:{}},groupRenderer,0,colIndex,this.ds);return gidPrefix+'-gp-'+groupField+'-'+Ext.util.Format.htmlEncode(value);},doGroupStart:function(buf,g,cs,ds,colCount){buf[buf.length]=this.startGroup.apply(g);},doGroupEnd:function(buf,g,cs,ds,colCount){buf[buf.length]=this.endGroup;},getRows:function(){if(!this.enableGrouping){return Ext.grid.GroupingView.superclass.getRows.call(this);}
var r=[];var g,gs=this.getGroups();for(var i=0,len=gs.length;i<len;i++){g=gs[i].childNodes[1].childNodes;for(var j=0,jlen=g.length;j<jlen;j++){r[r.length]=g[j];}}
return r;},updateGroupWidths:function(){if(!this.enableGrouping||!this.hasRows()){return;}
var tw=Math.max(this.cm.getTotalWidth(),this.el.dom.offsetWidth-this.scrollOffset)+'px';var gs=this.getGroups();for(var i=0,len=gs.length;i<len;i++){gs[i].firstChild.style.width=tw;}},onColumnWidthUpdated:function(col,w,tw){Ext.grid.GroupingView.superclass.onColumnWidthUpdated.call(this,col,w,tw);this.updateGroupWidths();},onAllColumnWidthsUpdated:function(ws,tw){Ext.grid.GroupingView.superclass.onAllColumnWidthsUpdated.call(this,ws,tw);this.updateGroupWidths();},onColumnHiddenUpdated:function(col,hidden,tw){Ext.grid.GroupingView.superclass.onColumnHiddenUpdated.call(this,col,hidden,tw);this.updateGroupWidths();},onLayout:function(){this.updateGroupWidths();},onBeforeRowSelect:function(sm,rowIndex){if(!this.enableGrouping){return;}
var row=this.getRow(rowIndex);if(row&&!row.offsetParent){var g=this.findGroup(row);this.toggleGroup(g,true);}},groupByText:'Group By This Field',showGroupsText:'Show in Groups'});Ext.grid.GroupingView.GROUP_ID=1000;