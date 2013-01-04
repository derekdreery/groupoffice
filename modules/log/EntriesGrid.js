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
GO.log.EntriesGrid = function(config) {
	if (!config) {
		config = {};
	}
	
	config.title = GO.log.lang.log;
	config.layout = 'fit';
	config.autoScroll = true;
	config.split = true;
	config.store = new GO.data.JsonStore({
				url : GO.settings.modules.log.url + 'json.php',
				baseParams : {
					task : 'entries'
				},
				root : 'results',
				id : 'id',
				totalProperty : 'total',
				fields : ['id', 'link_id', 'link_type', 'time', 'user_name',
						'text'],
				remoteSort : true
			});
	config.paging = true;
	var columnModel = new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[{
				header : GO.log.lang.time,
				dataIndex : 'time'
			}, {
				header : GO.lang.strDescription,
				dataIndex : 'text'
			},{
				header : GO.lang.strUser,
				dataIndex : 'user_name',
				sortable : false
			}, {
				header : GO.lang.strType,
				dataIndex : 'link_type'
			}

	]
	});
	
	config.cm = columnModel;
	config.view = new Ext.grid.GridView({
				autoFill : true,
				forceFit : true,
				emptyText : GO.lang['strNoItems']
			});
	config.sm = new Ext.grid.RowSelectionModel();
	config.loadMask = true;

	this.searchField = new GO.form.SearchField({
			store : config.store,
			width : 320
		});
		
	config.tbar=[GO.lang['strSearch'] + ':', this.searchField,'-',{
			iconCls: 'btn-export', 
			text: GO.lang.cmdExport, 
			cls: 'x-btn-text-icon', 
			handler:function(){				
				var config = {};			
				config.colModel = this.getColumnModel();				
				config.title = this.ownerCt.title;
				
				var query = this.searchField.getValue();
				if(!GO.util.empty(query))
				{
					config.subtitle= GO.lang.searchQuery+': '+query;
				}else
				{
					config.subtitle='';
				}
				
				if(!this.exportDialog)
				{
					this.exportDialog = new GO.ExportQueryDialog({
						query:'log'
					});
				}			
				this.exportDialog.show(config);
			},  
			scope: this		
		}];
			
	GO.log.EntriesGrid.superclass.constructor.call(this, config);
};
Ext.extend(GO.log.EntriesGrid, GO.grid.GridPanel, {
			loaded : false,
			afterRender : function() {
				GO.log.EntriesGrid.superclass.afterRender.call(this);
				if (this.isVisible()) {
					this.onGridShow();
				}
			},
			onGridShow : function() {
				if (!this.loaded && this.rendered) {
					this.store.load();
					this.loaded = true;
				}
			}
		});
