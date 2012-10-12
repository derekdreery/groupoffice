/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: UserPriceGrid.js 12533 2012-01-06 16:19:21Z mdhart $
 * @copyright Copyright Intermesh
 * @author Michael de Hart <mdhart@intermesh.nl>
 */

GO.servermanager.UserPriceGrid = function(config){

	config = config || {};

	config.layout='fit';
	config.autoScroll=true;
	config.split=true;

	config.title=GO.servermanager.lang.users;
	config.store = new GO.data.JsonStore({
		url : GO.url('servermanager/price/userStore'),
		fields:['max_users','price_per_month']
	});

	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[	{
			header: GO.servermanager.lang.users,
			dataIndex: 'max_users',
			editor: new Ext.form.TextField({
				allowBlank: false
			})
		},{
			header: GO.servermanager.lang.price,
			dataIndex: 'price_per_month',
			editor: new GO.form.NumberField({
				allowBlank: false
			}),
			align:'right'
		}]
	});

	config.cm=columnModel;
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']
	});
	config.sm=new Ext.grid.RowSelectionModel( {singleSelect : true} );
	config.loadMask=true;
	config.clicksToEdit=1;

	var UserPrice = Ext.data.Record.create([
		{name: 'max_users',				type:'string'},
		{name: 'price_per_month',	type:'string'}
	]);


	config.tbar=[{
		iconCls: 'btn-add',
		text: GO.lang['cmdAdd'],
		cls: 'x-btn-text-icon',
		handler: function(){
			var e = new UserPrice({
				id: '0',
				max_users:'',
				price: GO.util.numberFormat("0")
			});
			this.stopEditing();
			this.store.insert(0, e);
			this.startEditing(0, 0);
		},
		scope: this
	},{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
		    if (typeof(this.selectedIndex)!='undefined') {
					if (this.store.getAt(this.selectedIndex).data.id!='0') {
						Ext.Ajax.request({
							url : GO.url('servermanager/price/userDelete'),
							params : {
									'id' : this.store.getAt(this.selectedIndex).data.id
							},
							callback:function(options, success, response){
								var result = Ext.decode(response.responseText);
								if (!success || !result.success) {
									if (result.responseText.feedback) {
										Ext.MessageBox.alert(GO.lang.strError,result.responseText.feedback);
									}
								}
							},
							scope:this
						});
					}
					this.store.removeAt(this.selectedIndex);
		    }
		},
		scope: this
	}];

	config.listeners={
		rowclick: function(sm,i,record) {
		    this.selectedIndex = i;
		},
		scope:this
	}

	GO.servermanager.UserPriceGrid.superclass.constructor.call(this, config);

};
Ext.extend(GO.servermanager.UserPriceGrid, Ext.grid.EditorGridPanel,{
	
	setCompanyId : function(company_id) {
	    this.store.baseParams.company_id = this.company_id = company_id;
	},
	
	getGridData : function(){

		var data = {};

		for (var i = 0; i < this.store.data.items.length;  i++)
		{
			var r = this.store.data.items[i].data;

			data[i]={};

			for(var key in r)
			{
				data[i][key]=r[key];
			}
		}

		return data;
	},
	setIds : function(ids)
	{
		for(var index in ids)
		{
			if(index!="remove")
			{
				this.store.getAt(index).set('id', ids[index]);
			}
		}
	},
	save : function(maskEl){
		var params = {rates:Ext.encode(this.getGridData())}
		
		if(this.store.getModifiedRecords().length>0 || this.deletedRecords){
			
			Ext.Ajax.request({
			    url : GO.url('servermanager/price/submitUsers'),
			    params:params,
			    callback:function(options, success, response){
						this.store.commitChanges();
						var result = Ext.decode(response.responseText);
						if(result.new_rates)
								this.setIds(result.new_rates);
						this.deletedRecords=false;
			    },
			    scope:this
			});
			
		}
	}
});
