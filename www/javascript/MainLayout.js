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

 
GO.MainLayout = function(){
	
	this.addEvents({
		'ready' : true
	});
	
	this.resumeEvents();
};

Ext.extend(GO.MainLayout, Ext.util.Observable, {

	ready : false,
	
	fullscreenPopup : false,
	
	onReady : function(fn, scope){		
		if(!this.ready){
			this.on('ready', fn, scope);
		}else{
			fn.call(scope, this);
		}
	},
	
	launchFullscreen : function(url)
	{
		this.fullscreenPopup = GO.util.popup({
			url: url,
			target: 'groupoffice'
		});	
	},	
	
	login : function(){
	
		this.fireReady();
		GO.loginDialog = new GO.dialog.LoginDialog({modal:false});
		this.createLoginCallback();
		GO.loginDialog.show();
		this.removeLoadMask();		
	},
	
	createLoginCallback : function(){
		
		GO.loginDialog.addCallback(function(){
				var url = GO.afterLoginUrl ? GO.afterLoginUrl : GO.settings.config.host;
				if(GO.loginDialog.fullscreenField.getValue() && window.name!='groupoffice')
				{
					this.launchFullscreen(url);
					GO.loginDialog.hideDialog=false;
					GO.loginDialog.on('callbackshandled', this.createLoginCallback, this);
				}else
				{
					document.location.href=url;
				}
			}, this);		
	},
	
	fireReady : function(){
		
		this.fireEvent('ready', this);
	 	this.ready=true;		
	},
	
	createTabPanel : function(items){
		this.tabPanel = new Ext.TabPanel({
        region:'center',
        titlebar: false,
        border:false,
        activeTab:'go-module-panel-'+GO.settings.start_module,
        tabPosition:'top',
        items: items,
        layoutOnTabChange:true
    	});
	},

	init : function(){  
          
   	this.fireReady();

		var items = GO.moduleManager.getAllPanels();
    
    if(items.length==0)
    {
    	items = new Ext.Panel({
    		id: 'go-module-panel-'+GO.settings.start_module,
    		region:'center',
    		border:false,
    		cls:'go-form-panel',
    		title: 'No modules',
    		html: '<h1>No modules available</h1>You have a valid Group-Office account but you don\'t have access to any of the modules. Please contact the administrator if you feel this is an error.'
    	});
    }
    
    this.createTabPanel(items);
    
   var topPanel = new Ext.Panel({
        region:'north',
        contentEl: 'mainNorthPanel',
        cls: 'go-top-panel',
        height:28,
        titlebar:false,
        border:false
      });		
		
		var viewport = new Ext.Viewport({
        layout:'border',
        border:false,
        items:[topPanel,this.tabPanel]
      });    
    
    var adminMenuLink = Ext.get("admin-menu-link");
    var adminModulePanels = GO.moduleManager.getAllAdminPanelConfigs();
		
		if(adminMenuLink && adminModulePanels.length>0)
		{
			adminMenuLink.setDisplayed(true);
			
			var adminMenu = new Ext.menu.Menu({
    	id: 'adminMenu'});    	
      
      for(var i=0;i<adminModulePanels.length;i++)
      {

					adminMenu.add({
						moduleName:adminModulePanels[i].moduleName,
						text:adminModulePanels[i].title,
						//tooltip: {text:GO.settings.modules[i].description, title:GO.settings.modules[i].humanName},
						iconCls: 'go-menu-icon-'+adminModulePanels[i].moduleName,
						handler: function(item, e){
							
							var panelId = 'go-module-panel-'+item.moduleName;
							
							if(!this.tabPanel.items.map[panelId])
							{								
								var panel = GO.moduleManager.getAdminPanel(item.moduleName);
								panel.id = panelId;
								this.tabPanel.add(panel);
							}else{
								var panel = this.tabPanel.items.map[panelId];
							}	
							panel.show();
						},
						scope: this
					});				
      }			
			
			adminMenuLink.on("click", function(){

				var x = adminMenuLink.getX();
				var y = topPanel.el.getY()+topPanel.el.getHeight();

				adminMenu.showAt([x,y]);													
			},
			this);
			
		}else
		{
			adminMenuLink.setDisplayed(false);
		}
		
		var configurationLink = Ext.get("configuration-link");
		
		if(configurationLink)
		{
			configurationLink.on("click", function(){
				
				if(!this.personalSettingsDialog)
				{
					this.personalSettingsDialog = new GO.PersonalSettingsDialog();						
				}
				
				this.personalSettingsDialog.show();
																
			},
			this);
		}
		
		var helpLink = Ext.get("help-link");
		
		if(helpLink)
		{
			var helpMenu = new Ext.menu.Menu({
    		id: 'helpMenu',
    		items:[{
	    			iconCls:'btn-help',
	    			text:GO.lang.strHelpContents,
	    			handler:function(){
	    				
	    				var win = window.open('http://www.group-office.com/wiki/');
	    				win.focus();
	    			},
	    			scope:this
	    			
	    		},{
	    			iconCls:'btn-report-bug',
	    			text:GO.lang.strReportBug,
	    			handler:function(){
	    				var win = window.open('https://sourceforge.net/tracker2/?func=add&group_id=76359&atid=547651');
	    				win.focus();			
	    			},
	    			scope:this
	    			
	    		},    		
	    		'-',{
    			iconCls:'btn-info',
    			text:GO.lang.strAbout,
    			handler:function(){
    				if(!this.aboutDialog)
    				{
    					this.aboutDialog = new GO.dialog.AboutDialog();
    				} 
    				this.aboutDialog.show();   				
    			},
    			scope:this    			
    		}]    		
    	});
			
			helpLink.on("click", function(){
				var x = helpLink.getX();
				var y = topPanel.el.getY()+topPanel.el.getHeight();

				helpMenu.showAt([x,y]);																
			},
			this);
		}
		
		
		var activeTab = this.tabPanel.getLayout().activeItem;
   
  	if(!activeTab)
   		this.tabPanel.setActiveTab(0);
   		
		this.removeLoadMask();		
		
		GO.checker = new GO.Checker();
		GO.checker.init.defer(2000,GO.checker);
		GO.checker.on('alert', function(data){   		
   		if(data.notification_area)
   		{
   			Ext.get('notification-area').update(data.notification_area);
   		}
   	}, this);
	},
	
	search :function(e)
	{
    var keynum;
    var input;
    input = Ext.get("search_query");

    if(window.event) // IE
    {
      keynum = e.keyCode
    }else if(e.which) // Netscape/Firefox/Opera
    {
			keynum = e.which
    }

    if(keynum==13)
    {
			this.addSearchPanel(input.getValue());
    }
    return true;
	},
	
	setAdminMenu : function()
	{
		var adminMenuLink = Ext.get("adminMenuLink");
		if(adminMenuLink)
		{
			
			adminMenuLink.on("click", function(){

				var x = adminMenuLink.getX();
				var y = top.el.getY()+top.el.getHeight();

				adminMenu.showAt([x,y]);													
			},
			this);
		}
	},
	
	removeLoadMask : function()
	{
		var loading = Ext.get('loading');
		var mask = Ext.get('loading-mask');
		mask.setOpacity(.8);
		mask.shift({
			xy:loading.getXY(),
			width:loading.getWidth(),
			height:loading.getHeight(), 
			remove:true,
			duration:1,
			opacity:.3,
			easing:'bounceOut',
			callback : function(){
				loading.fadeOut({duration:.2,remove:true});
			}
		});
	},
	
	addSearchPanel : function(query)
	{
		var searchPanel = new GO.grid.SearchPanel(
			{query: query}
		);
		this.tabPanel.add(searchPanel);
		searchPanel.show();
	},		
	
	showSearchRecord : function(recordData)
	{
	
		//check if a showSearchResult function exists in the module iframe
		if(window.frames['iframe-'+recordData['module']].showSearchResult)
		{
			tabs.items.map[recordData['module']].show();
			window.frames['iframe-'+recordData['module']].showSearchResult(recordData);
			
		}else{
		
			this.showPanel(recordData['module'], recordData['url']);
		}
	}
});

GO.mainLayout = new GO.MainLayout();