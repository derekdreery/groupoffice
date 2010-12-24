/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: TestsGrid.js 0000 2010-12-16 09:38:17Z wsmits $
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

				var txtToDispxlay = '';

				switch(record.data.test)
				{
					case 'header':
						if(record.data.type == 'contains')
						{
							if(record.data.not)
							{
								if(record.data.arg1 == 'Subject')
								{
									txtToDisplay = 'Onderwerp bevat geen '+record.data.arg2;
								}
								else if(record.data.arg1 == 'From')
								{
									txtToDisplay = 'Afzender bevat geen '+record.data.arg2;
								}
								else if(record.data.arg1 == 'To')
								{
									txtToDisplay = 'Ontvanger bevat geen '+record.data.arg2;
								}
							}
							else
							{
								if(record.data.arg1 == 'Subject')
								{
									txtToDisplay = 'Onderwerp bevat '+record.data.arg2;
								}
								else if(record.data.arg1 == 'From')
								{
									txtToDisplay = 'Afzender bevat '+record.data.arg2;
								}
								else if(record.data.arg1 == 'To')
								{
									txtToDisplay = 'Ontvanger bevat '+record.data.arg2;
								}
							}
						}
						else if(record.data.type == 'is')
						{
							if(record.data.not)
							{
								if(record.data.arg1 == 'Subject')
								{
									txtToDisplay = 'Onderwerp is niet gelijk aan '+record.data.arg2;
								}
								else if(record.data.arg1 == 'From')
								{
									txtToDisplay = 'Afzender is niet gelijk aan '+record.data.arg2;
								}
								else if(record.data.arg1 == 'To')
								{
									txtToDisplay = 'Ontvanger is niet gelijk aan '+record.data.arg2;
								}
							}
							else
							{
								if(record.data.arg1 == 'Subject')
								{
									txtToDisplay = 'Onderwerp is gelijk aan '+record.data.arg2;
								}
								else if(record.data.arg1 == 'From')
								{
									txtToDisplay = 'Afzender is gelijk aan '+record.data.arg2;
								}
								else if(record.data.arg1 == 'To')
								{
									txtToDisplay = 'Ontvanger is gelijk aan '+record.data.arg2;
								}
							}
						}
						break;
					case 'exists':
						if(record.data.not)
						{
							if(record.data.arg == 'Subject')
							{
								txtToDisplay = 'Onderwerp bestaat niet';
							}
							else if(record.data.arg == 'From')
							{
								txtToDisplay = 'Afzender bestaat niet';
							}
							else if(record.data.arg == 'To')
							{
								txtToDisplay = 'Ontvanger bestaat niet';
							}
						}
						else
						{
							if(record.data.arg == 'Subject')
							{
								txtToDisplay = 'Onderwerp bestaat';
							}
							else if(record.data.arg == 'From')
							{
								txtToDisplay = 'Afzender bestaat';
							}
							else if(record.data.arg == 'To')
							{
								txtToDisplay = 'Ontvanger bestaat';
							}
						}
						break;
					case 'true':	
						txtToDisplay = 'Alle';
						break;
					case 'size':
						if(record.data.type == 'under')
						{
							txtToDisplay = 'Grootte is kleiner dan '+record.data.arg;
						}
						else
						{
							txtToDisplay = 'Grootte is groter dan '+record.data.arg;
						}
						break;
					default:
						txtToDisplay = 'Fout in weergeven van test';
						break;
				}

				return txtToDisplay;
			}
		}

	]
	};
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.sieve.url+ 'fileIO.php',
	    baseParams: {
	    	task: 'get_sieve_tests_json'
	    	},
	    root: 'tests',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});
	var columnModel =  new Ext.grid.ColumnModel({
		columns:fields.columns
	});
	config.cls = 'go-grid3-hide-headers';
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
			handler: function(){
				this.store.remove(this.getSelectionModel().getSelections());
			},
			scope: this
		}];

	GO.sieve.TestsGrid.superclass.constructor.call(this, config);
};

Ext.extend(GO.sieve.TestsGrid, GO.grid.GridPanel,{

	setAccountId : function(account_id){
		this.store.baseParams.account_id = account_id;
	},
	setScriptName : function(name){
		this.store.baseParams.script_name = name;
	},
	setScriptIndex : function(index){
		this.store.baseParams.script_index = index;
	}
});