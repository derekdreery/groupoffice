/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: ActionGrid.js 0000 2010-12-16 09:46:17Z wsmits $
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
						{
							txtToDisplay = 'Kopieer naar '+record.data.target;
						}
						else
						{
							txtToDisplay = 'Verplaats naar '+record.data.target;
						}
						break;
					case 'redirect':
						if(record.data.copy)
						{
							txtToDisplay = 'Stuur kopie door naar '+record.data.target;
						}
						else
						{
							txtToDisplay = 'Stuur door naar '+record.data.target;
						}
						break;
					case 'vacation':
							txtToDisplay = 'Vakantie: Stuur bericht iedere '+record.data.days+ ' dag(en) en naar de adres(sen) '+record.data.addresses+ ' Bericht: '+record.data.reason;
						break;
					case 'reject':
						txtToDisplay = 'Weigeren met bericht: '+record.data.target;
						break;
					case 'discard':
						txtToDisplay = 'Verwijderen';
						break;
					case 'stop':
						txtToDisplay = 'Stop';
						break;
					default:
						txtToDisplay = 'Fout in weergeven van test';
						break;
				}
				
				return txtToDisplay;

			}
		}]
	};
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.sieve.url+ 'fileIO.php',
	    baseParams: {
	    	task: 'get_sieve_actions_json'
	    	},
	    root: 'actions',
	    id: 'id',
	    totalProperty:'total',
	    fields: fields.fields,
	    remoteSort: true
	});
	var columnModel =  new Ext.grid.ColumnModel({
		columns:fields.columns
	});

	config.cm=columnModel;
	//config.disabled=true;
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

	GO.sieve.SieveGrid.superclass.constructor.call(this, config);
};

Ext.extend(GO.sieve.ActionGrid, GO.grid.GridPanel,{

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