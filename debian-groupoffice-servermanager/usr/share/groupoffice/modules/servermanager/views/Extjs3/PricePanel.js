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
	config.title = GO.servermanager.lang.pricing;
	config.layout='border';
//	config.defaults={
//		anchor:'100%'
//	};
	config.labelWidth=140;
	
	this.priceGrid = new GO.servermanager.UserPriceGrid({
		layout:'fit',
		region:'center'
	});
	
	config.items=[{
			xtype:'panel',
			region:'north',
			title:GO.servermanager.lang.space,
			height:200,
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
					xtype:'textfield',
					name:'mbs_included',
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
					text:'€',
					width:10
				},

				{
					xtype:'textfield',
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
	this.priceGrid
	,{
		region:'east',
		xtype:'panel',
		title:"Module prices",
		layout:'fit',
		width:300,
		html:'todo'
	}];
	
	GO.servermanager.PricePanel.superclass.constructor.call(this, config);	
}

Ext.extend(GO.servermanager.PricePanel, Ext.Panel,{
	
	});