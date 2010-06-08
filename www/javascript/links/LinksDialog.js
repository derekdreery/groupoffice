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
			noOpenLinks:true,
			dontLoadOnRender:true
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
		listeners : {
			show:function(){
				this.grid.load();
			},
			scope:this
		},
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
			this.fromLinks.push({'link_id' : gridRecords[i].data['link_id'], 'link_type' : gridRecords[i].data['link_type']});
		}
	},
	setSingleLink : function(link_id, link_type)
	{
		this.fromLinks=[{"link_id":link_id,"link_type":link_type}];
	},

	selectFolder : function(toLinks){



		
	},
	
	linkItems : function()	{
		var selectionModel = this.grid.searchGrid.getSelectionModel();
		var records = selectionModel.getSelections();

		var tolinks = [];

		for (var i = 0;i<records.length;i++)
		{
			tolinks.push({'link_id' : records[i].data['id'], 'link_type' : records[i].data['link_type']});
		}

		if(tolinks.length==1){
			if(!this.selectFolderWindow){

				this.selectFolderTree = new GO.LinksTree();
				this.selectFolderTree.on('dblclick', function(node){
					var to_folder_id = parseInt(node.id.replace('lt-folder-',''));
					this.sendLinkRequest(tolinks, to_folder_id);
					this.selectFolderWindow.hide();
				}, this);

				this.selectFolderWindow = new GO.Window({
					layout:'fit',
					title:GO.lang.selectFolder,
					items:this.selectFolderTree,
					closeAction:'hide',
					width:400,
					height:400,
					modal:true,
					closable:true,
					buttons:[{
							text:GO.lang.cmdOk,
							handler:function(){

								var node = this.selectFolderTree.getSelectionModel().getSelectedNode();
								if(!node){
									alert(GO.lang.selectFolder);
								}

								var to_folder_id = parseInt(node.id.replace('lt-folder-',''));
								this.sendLinkRequest(tolinks, to_folder_id);
								this.selectFolderWindow.hide();
							},
							scope:this
					}]
				});
			}
			this.selectFolderWindow.show();

			this.selectFolderTree.loadLinks(tolinks[0]['link_id'], tolinks[0]['link_type'], function(rootNode){
				if(!rootNode.childNodes.length){
					this.selectFolderWindow.hide();
					this.sendLinkRequest(tolinks);
				}
			}, this);
			
		}else
		{
			this.sendLinkRequest(tolinks);
		}

		
	},

	sendLinkRequest : function(tolinks, to_folder_id){
		var to_folder_id = to_folder_id || 0;
		Ext.Ajax.request({
			url: BaseHref+'action.php',
			params: {
				task: 'link',
				fromLinks: Ext.encode(this.fromLinks),
				toLinks: Ext.encode(tolinks),
				description:this.grid.linkDescriptionField.getValue(),
				folder_id: this.folder_id,
				to_folder_id : to_folder_id
				},
			callback: function(options, success, response)
			{
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], response.result.errors);
				}else
				{
					this.fireEvent('link');
					this.grid.searchGrid.getSelectionModel().clearSelections();
					this.hide();
				}
			},
			scope: this
		});
	}
});


