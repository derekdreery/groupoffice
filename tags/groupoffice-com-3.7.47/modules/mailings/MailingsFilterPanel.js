/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.mailings.MailingsFilterPanel = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	config.autoScroll=true;
	config.title= GO.mailings.lang.filterMailings;
	
	var checkColumn = new GO.grid.CheckColumn({
		header: '&nbsp;',
		dataIndex: 'checked',
		width: 30
	});
	
	
	Ext.apply(config,{
		cls:'go-grid3-hide-headers',
		disableSelection:true,
		border:true,
		loadMask:true,
		store: GO.mailings.readableMailingsStore,		
		columns: [
				checkColumn,
				{
					header: GO.lang.strName, 
					dataIndex: 'name',
					id:'name'
				}				
			],
		plugins: [checkColumn],
		autoExpandColumn:'name',
		viewConfig: {emptyText:GO.mailings.lang.noAddressLists}
	});	

	config.layout= 'fit';

	
	var applyButton = new Ext.Button({
		text:GO.lang.cmdApply,
		handler:function(){			
			var mailings = [];
			
			for (var i = 0; i < GO.mailings.readableMailingsStore.data.items.length;  i++)
			{
				var checked = GO.mailings.readableMailingsStore.data.items[i].get('checked');
				if(checked=="1")
				{
					mailings.push(GO.mailings.readableMailingsStore.data.items[i].get('id'));	
				}				
			}
			
			this.fireEvent('change', this, mailings);
			
			GO.mailings.readableMailingsStore.commitChanges();			
		},
		scope: this
	});    
	
	var resetButton = new Ext.Button({
		text:GO.lang.cmdReset,
		handler:function(){			
			
			var mailings = [];
			for (var i = 0; i < GO.mailings.readableMailingsStore.data.items.length;  i++)
			{
				var checked = GO.mailings.readableMailingsStore.data.items[i].set('checked', '0');								
			}
						
			this.fireEvent('change', this, mailings);			
			GO.mailings.readableMailingsStore.commitChanges();		
		},
		scope: this
	});    
	
	
	config.buttons=[applyButton,resetButton];
	config.buttonAlign='left';

	GO.mailings.MailingsFilterPanel.superclass.constructor.call(this, config);	
	this.addEvents({change : true});
}

Ext.extend(GO.mailings.MailingsFilterPanel, GO.grid.GridPanel,{
	afterRender : function(){
		
		
		
		GO.mailings.readableMailingsStore.load();
		
		GO.mailings.MailingsFilterPanel.superclass.afterRender.call(this);
	}
});