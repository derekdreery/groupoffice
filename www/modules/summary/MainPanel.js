/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 GO.summary.MainPanel = function(config)
 {
 	
 	if(!config)
 	{
 		config={};
 	}
 	
 	
 	this.activePortletsIds = Ext.state.Manager.get('active-portlets');
 	
 	if(!this.activePortletsIds)
 	{
 		this.activePortletsIds=['portlet-announcements', 'portlet-tasks', 'portlet-calendar','portlet-note'];
 	}
 	
 		
 	
 	
 	this.columns=[/*{
				columnWidth:.33,
	      style:'padding:10px 0 10px 10px',
	      border:false
	  	},*/
	  	{
				columnWidth:.5,
	      style:'padding:10px 0 10px 10px',
	      border:false
	  	},
	  	{
				columnWidth:.5,
	      style:'padding:10px 0 10px 10px',
	      border:false
	  	}];
	  	
	
	var portletsPerCol = Math.ceil(this.activePortletsIds.length/this.columns.length);
	  	
  
  
  
	for(var p=0;p<this.activePortletsIds.length;p++)
  {  	
  	if(GO.summary.portlets[this.activePortletsIds[p]])
  	{
	  	this.activePortlets.push(this.activePortletsIds[p]);
	  	var index = Math.ceil((p+1)/portletsPerCol)-1;  	
	  	var column = this.columns[index]; 
	  	
	  	if(!column.items)
	  	{
	  		column.items=[GO.summary.portlets[this.activePortletsIds[p]]];
	  	}else
	  	{
	  		column.items.push(GO.summary.portlets[this.activePortletsIds[p]]);
	  	}
  	}	
  }
   
  config.items=this.columns;
  
  for(var p in GO.summary.portlets)
  {
  	//if(!this.activePortletsIds.indexOf(p))
  	//{
  		this.availablePortlets.push(GO.summary.portlets[p]);
  	//}
  }
  
  if(!config.items)
  {
  	config.html = GO.summary.lang.noItems;
  }
  


  this.tbar=[{
  	text: GO.lang['cmdAdd'],
  	iconCls:'btn-add',
  	handler: this.showAvailablePortlets,
  	scope: this
  },{
  	text: 'Manage announcements',
  	iconCls:'btn-add',
  	handler: function(){
  		if(!this.manageAnnouncementsWindow)
  		{
  			
  			this.manageAnnouncementsWindow = new Ext.Window({
  				layout:'fit',
  				items:this.announcementsGrid =  new GO.summary.AnnouncementsGrid(),
  				width:700,
  				height:400,
  				title:GO.summary.lang.announcements,
  				closeAction:'hide',
  				buttons:[{
						text: GO.lang.cmdClose,
						handler: function(){this.manageAnnouncementsWindow.hide();},
						scope:this
					}],
					listeners:{
						show: function(){
							if(!this.announcementsGrid.store.loaded)
							{
								this.announcementsGrid.store.load();
							}							
						},
						scope:this
					}
  			});
  			
  			this.announcementsGrid.store.on('load',function(){
  				this.announcementsGrid.store.on('load',function(){
  					GO.summary.announcementsPanel.store.load();		
  				}, this);
  			}, this);
  			
  		}
  		
  		this.manageAnnouncementsWindow.show();
  	},
  	scope: this
  }]; 


  
	GO.summary.MainPanel.superclass.constructor.call(this,config);
	

};	

Ext.extend(GO.summary.MainPanel, GO.summary.Portal, {
	
	
	activePortlets : Array(),
	availablePortlets : Array(),
	
	
	showAvailablePortlets : function(){
		
		var data = {portlets: this.availablePortlets};
		
		var store = new Ext.data.JsonStore({		
				id: 'id',   
		    root: 'portlets',
		    fields: ['id', 'title', 'iconCls']
		});
		
		store.loadData(data);
		
		var tpl ='<tpl for=".">'+
			'<div class="go-item-wrap">{title}</div>'+
			'</tpl>';
		
		var list = new GO.grid.SimpleSelectList({store: store, tpl: tpl});
		
		list.on('click', function(dataview, index){
			var id = dataview.store.data.items[index].data.id;
			
			//.renderTo=this.firstColumn;
			//var portlet = new GO.summary.Portlet();
			
			var lastCount = 0;
			for(var c=0;c<this.columns.length;c++)
			{
				if(!this.columns[c].items || this.columns[c].items.length==0 || lastCount > this.columns[c].items.length)
				{
					break;
				}				
				lastCount = this.columns[c].items.length;
			}
			
			this.columns[c].add(GO.summary.portlets[id]);
			this.columns[c].doLayout();
			
			
			list.clearSelections();
			portletsWindow.close();
			
							
		}, this);
		
		var portletsWindow = new Ext.Window({
			title: GO.summary.lang.selectPortlet,
			layout:'fit',
			modal:false,
			height:400,
			width:600,
			closable:true,
			closeAction:'hide',	
			items: new Ext.Panel({
				items:list,
				cls: 'go-form-panel'
			})
		});
		
		portletsWindow.show();
		
	}
});

GO.moduleManager.addModule('summary', GO.summary.MainPanel, {
	title : GO.summary.lang.summary,
	iconCls : 'go-tab-icon-summary'
});
