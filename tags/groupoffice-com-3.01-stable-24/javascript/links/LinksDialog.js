/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
  
GO.dialog.LinksDialog = function(config){
	
	Ext.apply(this, config);
	
	this.grid = new GO.grid.SearchPanel({
			noTitle:true,
			noOpenLinks:true
		});

	this.grid.searchGrid.on('rowdblclick', this.linkItems, this);
		
	var focusSearch = function(){
		this.grid.searchField.focus(true);		
	};
	
	GO.dialog.LinksDialog.superclass.constructor.call(this, {
   	layout: 'fit',
   	focus: focusSearch.createDelegate(this),
		modal:false,
		minWidth:300,
		minHeight:300,
		height:500,
		width:700,
		border:false,
		plain:true,
		closeAction:'hide',
		title:GO.lang['strLinkItems'],
		items: this.grid,
		
		buttons: [
			{
				text: GO.lang['cmdOk'],
				handler: function(){							
					this.linkItems();
				},
				scope:this
			},
			{
				text: GO.lang['cmdClose'],
				handler: function(){this.hide();},
				scope: this
			}
		]
    });
    
   this.addEvents({'link' : true});
};

Ext.extend(GO.dialog.LinksDialog, Ext.Window, {
	
	folder_id : 0,
	
	setLinkRecords : function(gridRecords)
	{
		this.fromLinks = [];
		for (var i = 0;i<gridRecords.length;i++)
		{
			this.fromLinks.push({ 'link_id' : gridRecords[i].data['link_id'], 'link_type' : gridRecords[i].data['link_type'] });
		}
	},
	setSingleLink : function(link_id, link_type)
	{
		this.fromLinks=[{"link_id":link_id,"link_type":link_type}];
	},
	
	linkItems : function()	{
		var selectionModel = this.grid.searchGrid.getSelectionModel();
		var records = selectionModel.getSelections();

		var tolinks = [];

		for (var i = 0;i<records.length;i++)
		{
			tolinks.push({ 'link_id' : records[i].data['id'], 'link_type' : records[i].data['link_type'] });
		}

		Ext.Ajax.request({
			url: BaseHref+'action.php',
			params: {
				task: 'link', 
				fromLinks: Ext.encode(this.fromLinks), 
				toLinks: Ext.encode(tolinks),
				description:this.grid.linkDescriptionField.getValue(),
				folder_id: this.folder_id
				},
			callback: function(options, success, response)
			{
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], response.result.errors);
				}else
				{
					this.fireEvent('link');
					this.hide();
				}
			},
			scope: this
		});
	}
});


