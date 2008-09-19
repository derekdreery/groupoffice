/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: SearchPanel.js 2948 2008-09-03 07:16:31Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.grid.SearchPanel = function(config){

	
	Ext.apply(this, config);
	
	
	if(!this.query)
	{
		this.query='%';
	}
	
	
	
  config['title']=GO.lang['strSearch']+': "'+Ext.util.Format.htmlEncode(this.query)+'"';
	config['closable']=true;
	config['iconCls']='go-search-icon-tab';
		
  GO.grid.SearchPanel.superclass.constructor.call(this, config);
  
  
  this.searchField.setValue(this.query);
  this.store.baseParams.query=this.query;
  
  
  this.store.on('load', function(){
  	this.setTitle(GO.lang['strSearch']+': "'+Ext.util.Format.htmlEncode(this.store.baseParams.query)+'"');
  	}, this);
	
}

Ext.extend(GO.grid.SearchPanel, GO.grid.LinksGrid, {
	
	rowDoulbleClicked : function(search_grid, rowClicked, e) {
			
		var selectionModel = this.getSelectionModel();
		var record = selectionModel.getSelected();
		
		if(GO.linkHandlers[record.data.link_type])
		{
			GO.linkHandlers[record.data.link_type].call(this, record.data.id, record);
		}else
		{
			Ext.Msg.alert(GO.lang['strError'], 'No handler definded for link type: '+record.data.link_type);
		}
	},
	
	afterRender : function()
	{
		GO.grid.SearchPanel.superclass.afterRender.call(this);
		this.addListener("rowdblclick", this.rowDoulbleClicked, this);
		this.store.load();
	}
	
	
});