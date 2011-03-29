/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: ActionGrid.js 0000 2010-12-16 08:57:17 wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.sieve.ActionGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.height=180;
	config.style='margin: 5px;';
	config.border=true;
	config.cls = 'go-grid3-hide-headers';
	var fields ={
		fields:['type','copy','target','days','addresses','reason','text'],
		header: false,
		columns:[
//		{
//			header: GO.sieve.lang.type,
//			dataIndex: 'type'
//		},{
//			header: GO.sieve.lang.copy,
//			dataIndex: 'copy'
//		},{
//			header: GO.sieve.lang.target,
//			dataIndex: 'target'
//		},{
//			header: GO.sieve.lang.days,
//			dataIndex: 'days'
//		},{
//			header: GO.sieve.lang.addresses,
//			dataIndex: 'addresses'
//		},{
//			header: GO.sieve.lang.reason,
//			dataIndex: 'reason'
//		},
		{
			header:false,
			dataIndex:'text',
			renderer:function(value, metaData, record, rowIndex, colIndex, store){
				
				var txtToDisplay = '';

				switch(record.data.type)
				{
					case 'fileinto':
						if(record.data.copy)
							txtToDisplay = GO.sieve.lang.copyto+' '+record.data.target;
						else
							txtToDisplay = GO.sieve.lang.fileinto+' '+record.data.target;
						break;

					case 'redirect':
						if(record.data.copy)
							txtToDisplay = GO.sieve.lang.sendcopyto+' '+record.data.target;
						else
							txtToDisplay = GO.sieve.lang.forwardto+' '+record.data.target;
						break;

					case 'vacation':
							txtToDisplay = GO.sieve.lang.vacsendevery+' '+record.data.days+' '+GO.sieve.lang.vacdaystoadresses+' '+record.data.addresses+' '+GO.sieve.lang.vacationmessage+' '+record.data.reason;
						break;

					case 'reject':
							txtToDisplay = GO.sieve.lang.refusewithmesssage+' '+record.data.target;
						break;

					case 'discard':
							txtToDisplay = GO.sieve.lang.discard;
						break;

					case 'stop':
							txtToDisplay = GO.sieve.lang.stop;
						break;
						
					default:
							txtToDisplay = GO.sieve.lang.errorshowtext;
						break;
				}
				return txtToDisplay;
			}
		}
	]};

	var columnModel =  new Ext.grid.ColumnModel({
		columns:fields.columns
	});

	config.store = new GO.data.JsonStore({
	    root: 'actions',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});
	config.enableDragDrop = true;
	config.ddGroup = 'SieveActionDD';
	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	config.tbar=[{
			iconCls: 'btn-delete',
			text: GO.lang['cmdDelete'],
			cls: 'x-btn-text-icon',
			handler: function(){ this.deleteSelected();},
				scope: this
		}];

	GO.sieve.SieveGrid.superclass.constructor.call(this, config);

	this.on('render',function(){
		//enable row sorting
		var DDtarget = new Ext.dd.DropTarget(this.getView().mainBody,
		{
			ddGroup : 'SieveActionDD',
			copy:false,
			notifyDrop : this.onNotifyDrop.createDelegate(this)
		});
	}, this);
};

Ext.extend(GO.sieve.ActionGrid, GO.grid.GridPanel,{
	deleteSelected : function(){this.store.remove(this.getSelectionModel().getSelections());},

	onNotifyDrop : function(dd, e, data)
	{
		var rows=this.selModel.getSelections();
		var dragData = dd.getDragData(e);
		var cindex=dragData.rowIndex;
		if(cindex=='undefined')
		{
			cindex=this.store.data.length-1;
		}

		for(i = 0; i < rows.length; i++)
		{
			var rowData=this.store.getById(rows[i].id);

			if(!this.copy){
				this.store.remove(this.store.getById(rows[i].id));
			}

			this.store.insert(cindex,rowData);
		}

		//save sort order
		var filters = {};

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			filters[this.store.data.items[i].get('id')] = i;
		}
	}
});