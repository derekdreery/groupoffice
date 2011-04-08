/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: TestsGrid.js 0000 2010-12-29 08:56:17 wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.sieve.TestsGrid = function(config){
	if(!config)
	{
		config = {};
	}
	config.autoScroll=true;
	config.height=180;
	config.style='margin: 5px;';
	config.border=true;
	var fields ={
		fields:['test','not','type','arg','arg1','arg2','text'],
		header: false,
		columns:[
//		{
//			header: GO.sieve.lang.test,
//			dataIndex: 'test'
//		},{
//			header: GO.sieve.lang.not,
//			dataIndex: 'not'
//		},{
//			header: GO.sieve.lang.type,
//			dataIndex: 'type'
//		},{
//			header: GO.sieve.lang.arg,
//			dataIndex: 'arg'
//		},{
//			header: GO.sieve.lang.arg1,
//			dataIndex: 'arg1'
//		},{
//			header: GO.sieve.lang.arg2,
//			dataIndex: 'arg2'
//		},
		{
			header:false,
			dataIndex:'text',
			renderer:function(value, metaData, record, rowIndex, colIndex, store){

				var txtToDisplay = '';

				switch(record.data.test)
				{
					case 'header':
						if(record.data.type == 'contains')
						{
							if(record.data.not)
							{
								if(record.data.arg1 == 'Subject')
									txtToDisplay = GO.sieve.lang.subjectcontainsnot+' '+record.data.arg2;
								else if(record.data.arg1 == 'From')
									txtToDisplay = GO.sieve.lang.fromcontainsnot+' '+record.data.arg2;
								else if(record.data.arg1 == 'To')
									txtToDisplay = GO.sieve.lang.tocontainsnot+' '+record.data.arg2;
								else
									txtToDisplay = GO.sieve.lang.custommailheader+" "+record.data.arg1+" "+GO.sieve.lang.notcontains+" "+record.data.arg2;
							}
							else
							{
								if(record.data.arg1 == 'Subject')
									txtToDisplay = GO.sieve.lang.subjectcontains+' '+record.data.arg2;
								else if(record.data.arg1 == 'From')
									txtToDisplay = GO.sieve.lang.fromcontains+' '+record.data.arg2;
								else if(record.data.arg1 == 'To')
									txtToDisplay = GO.sieve.lang.tocontains+' '+record.data.arg2;
								else if(record.data.arg1 == 'X-Spam-Flag')
									txtToDisplay = GO.sieve.lang.markedasspam;
								else
									txtToDisplay = GO.sieve.lang.custommailheader+" "+record.data.arg1+" "+GO.sieve.lang.contains+" "+record.data.arg2;
							}
						}
						else if(record.data.type == 'is')
						{
							if(record.data.not)
							{
								if(record.data.arg1 == 'Subject')
									txtToDisplay = GO.sieve.lang.subjectequalsnot+' '+record.data.arg2;
								else if(record.data.arg1 == 'From')
									txtToDisplay = GO.sieve.lang.fromequalsnot+' '+record.data.arg2;
								else if(record.data.arg1 == 'To')
									txtToDisplay = GO.sieve.lang.toequalsnot+' '+record.data.arg2;
								else
									txtToDisplay = GO.sieve.lang.custommailheader+" "+record.data.arg1+" "+GO.sieve.lang.notis +" "+record.data.arg2;
							}
							else
							{
								if(record.data.arg1 == 'Subject')
									txtToDisplay = GO.sieve.lang.subjectequals+' '+record.data.arg2;
								else if(record.data.arg1 == 'From')
									txtToDisplay = GO.sieve.lang.fromequals+' '+record.data.arg2;
								else if(record.data.arg1 == 'To')
									txtToDisplay = GO.sieve.lang.toequals+' '+record.data.arg2;
								else
									txtToDisplay = GO.sieve.lang.custommailheader+" "+record.data.arg1+" "+GO.sieve.lang.is +" "+record.data.arg2;
							}
						}
						break;

					case 'exists':
						if(record.data.not)
						{
							if(record.data.arg == 'Subject')
								txtToDisplay = GO.sieve.lang.subjectexistsnot;
							else if(record.data.arg == 'From')
								txtToDisplay = GO.sieve.lang.fromexistsnot;
							else if(record.data.arg == 'To')
								txtToDisplay = GO.sieve.lang.toexistsnot;
							else
								txtToDisplay = GO.sieve.lang.custommailheader+" "+record.data.arg+" "+GO.sieve.lang.notexists;
						}
						else
						{
							if(record.data.arg == 'Subject')
								txtToDisplay = GO.sieve.lang.subjectexists;
							else if(record.data.arg == 'From')
								txtToDisplay = GO.sieve.lang.fromexists;
							else if(record.data.arg == 'To')
								txtToDisplay = GO.sieve.lang.toexists;
							else
								txtToDisplay = GO.sieve.lang.custommailheader+" "+record.data.arg+" "+GO.sieve.lang.notexists;
						}
						break;

					case 'true':	
						txtToDisplay = 'Alle';
						break;

					case 'size':
						if(record.data.type == 'under')
							txtToDisplay = GO.sieve.lang.sizesmallerthan+' '+record.data.arg;
						else
							txtToDisplay = GO.sieve.lang.sizebiggerthan+' '+record.data.arg;
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
	    root: 'tests',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});
	enableDragDrop:true,
	config.enableDragDrop = true;
	config.ddGroup = 'SieveTestDD';
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
			handler: function(){this.deleteSelected();},
			scope: this
		}];

	GO.sieve.TestsGrid.superclass.constructor.call(this, config);

	this.on('render',function(){
	
		//enable row sorting
		var DDtarget = new Ext.dd.DropTarget(this.getView().mainBody,
		{
			ddGroup : 'SieveTestDD',
			copy:false,
			notifyDrop : this.onNotifyDrop.createDelegate(this)
		});
	}, this);
};

Ext.extend(GO.sieve.TestsGrid, GO.grid.GridPanel,{
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