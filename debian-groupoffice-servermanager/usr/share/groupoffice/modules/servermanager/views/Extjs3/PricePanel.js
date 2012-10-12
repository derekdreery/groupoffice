/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: PricePanel.js 10380 2012-05-24 09:38:49Z mdhart $
 * @copyright Copyright Intermesh
 * @author Michael de Hart <mdhart@intermesh.nl>
 */
 
GO.servermanager.PricePanel = function(config)
{
	config = config || {};

	config.border=true;
	config.hideLabel=true;
	config.title = GO.servermanager.lang.userPricing;
	config.layout='border';
//	config.defaults={
//		anchor:'100%'
//	};
	config.labelWidth=140;
	
	config.items=[{
			xtype:'panel',
			region:'north',
			title:GO.servermanager.lang.space,
			height:80,
			bodyStyle: 'padding:5px',
			items:[
			{
				xtype: 'compositefield',
				labelWidth: 70,
				fieldLabel: GO.servermanager.lang.mbsIncluded,
				items: [
				{
					xtype:'label',
					text:' ',
					width:10
				},

				{
					xtype:'numberfield',
					name:'mbs_included',
					value: GO.settings.servermanager_mbs_included,
					decimals:0,
					width:100
				},

				{
					xtype:'label',
					text: 'MB '+GO.servermanager.lang.perUser
					}
				]
			},
			{
				xtype: 'compositefield',
				labelWidth: 70,
				fieldLabel: GO.servermanager.lang.extraMbs,
				items: [
				{
					xtype:'label',
					text: GO.settings.currency ,
					width:10
				},
				{
					xtype:'numberfield',
					name:'extra_mbs',
					width:100
				},
				{
					xtype:'label',
					text:GO.servermanager.lang.perMonth+'/GB'
				}
				]
			}
			]
		},
	this.userPriceGrid= new GO.servermanager.UserPriceGrid({
		region:'center'
	}),
	this.modulePriceGrid= new GO.servermanager.ModulePriceGrid({
		region:'east',
		title:"Module prices",
		width:300
	})];
	
	GO.servermanager.PricePanel.superclass.constructor.call(this, config);	
}

Ext.extend(GO.servermanager.PricePanel, Ext.Panel,{
	
	afterRender: function(){
		
		GO.servermanager.PricePanel.superclass.afterRender.call(this);
		
		var requests = {
			moduleprices:{r:"servermanager/price/moduleStore"},				
			userprices:{r:"servermanager/price/userStore"}
			//space: {r:"servermanager/price/options"}
		}

		GO.request({

			url: "core/multiRequest",
			params:{
				requests:Ext.encode(requests)
			},
			success: function(options, response, result)
			{
				this.userPriceGrid.store.loadData(result.userprices);
				this.modulePriceGrid.store.loadData(result.moduleprices);
				
				/*GO.tasks.categoriesStore.loadData(result.categories);
				this.taskListsStore.loadData(result.tasklists);				
				if (!GO.util.empty(result.tasks))
					this.gridPanel.store.loadData(result.tasks);*/
			},
			scope:this
		});    
		
	}
	
	});