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
		'ready' : true,
		'render' : true,
		'linksDeleted' : true,
		'focus' : true,
		'blur' : true,
		'login' : true
	});
	
	this.resumeEvents();
};

Ext.extend(GO.MainLayout, Ext.util.Observable, {

	ready : false,
	
	fullscreenPopup : false,

	state : false,

	stateSaveScheduled : false,

	rendered:false,
	
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

		new Ext.Viewport();
		
		GO.loginDialog = new GO.dialog.LoginDialog({modal:false});

		//this.fireReady();
		this.createLoginCallback();

		this.removeLoadMask();

		GO.loginDialog.show();

		if(window.console && window.console.firebug)
		{
			this.msg(GO.lang.firebugDetected, GO.lang.firebugWarning, 4, 400);
		}
		if(GO.settings.config.debug)
		{
			this.msg(GO.lang.debugModeDetected, GO.lang.debugModeWarning, 4, 400);
		}

		this.fireEvent('login', this);
	},

	saveState : function(){
		Ext.state.Manager.getProvider().set('open-modules', this.getOpenModules());
	},

	logout : function(first){
		
		if(Ext.Ajax.isLoading())
		{
			if(first){
				Ext.getBody().mask(GO.lang.waitMsgLoad);
			}
			this.logout.defer(500, this, [true]);
		}else
		{
			document.location=GO.settings.config.host+"index.php?task=logout";
		}
	},
	
	createLoginCallback : function(){
		
		GO.loginDialog.addCallback(function(){
				var url = GO.afterLoginUrl ? GO.afterLoginUrl : GO.settings.config.host;
				if(GO.loginDialog.fullscreenField.getValue() && window.name!='groupoffice')
				{
					url = GO.util.addParamToUrl(url, 'fullscreen_loaded','true');

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

	getOpenModules : function(){
		var openModules=[];
		this.tabPanel.items.each(function(p){
				var tabEl = this.tabPanel.getTabEl(p);

				if(tabEl.style.display != 'none'){
					openModules.push(p.moduleName);
				}
			}, this);

		return openModules;

	},
	
	createTabPanel : function(items){
		this.tabPanel = new Ext.TabPanel({
        region:'center',
        titlebar: false,
        border:false,
        activeTab:'go-module-panel-'+GO.settings.start_module,
        tabPosition:'top',				
        items: items/*,
        layoutOnTabChange:true*/
    	});

		this.tabPanel.on('contextmenu',function(tp, panel, e){

			if(panel.closable){
				return false;
			}

			var openModules =this.getOpenModules();
			
			//don't hide last tab
			if(openModules.length>1){

				tp.hideTabStripItem(panel);
				panel.hide();

				//var menuItem = this.startMenu.items.item('go-start-menu-'+panel.moduleName);
				//menuItem.show();

				if(panel == tp.activeTab){
						var next = tp.stack.next();
						if(next){
								tp.setActiveTab(next);
						}else if(tp.items.getCount() > 0){
								tp.setActiveTab(0);
						}else{
								tp.activeTab = null;
						}
				}
				//this.refreshMenu();
				this.saveState();
			}
		},this);

		
	},

	/*refreshMenu : function(){
		var visible=0;
		var above=0;
		var beneath=0;
		var adminMenuEl=false;

		this.startMenu.items.each(function(i){
			if(i.id!='go-start-menu-admin-menu'){
				if(!i.hidden){
					visible++;

					if(!adminMenuEl){
						above++;
					}else
					{
						beneath++;
					}
				}
			}else
			{
				adminMenuEl=i;
			}
		}, this);

		if(!above || !beneath){
			adminMenuEl.hide();
		}else
		{
			adminMenuEl.show();
		}

		this.startMenuLink.setDisplayed(visible>0);
	},*/
	
	getModulePanel : function(moduleName){
		var panelId = 'go-module-panel-'+moduleName;
							
		if(this.tabPanel.items.map[panelId])
		{								
			return this.tabPanel.items.map[panelId];
		}else
		{
			return false;
		}
	},

	//overridable
	beforeRender : function(){

	},



	init : function(){

		window.name='groupoffice';
    
		GO.loginDialog = new GO.dialog.LoginDialog({modal:true});

		GO.checker = new GO.Checker();

   	this.fireReady();

		Ext.QuickTips.init();
		Ext.apply(Ext.QuickTips.getQuickTip(), {
				dismissDelay:0,
				maxWidth:500
		});

		var allPanels = GO.moduleManager.getAllPanelConfigs();

		var items=[];

		this.startMenu = new Ext.menu.Menu({id: 'startMenu'});
    
    if(allPanels.length==0)
    {
    	items = new Ext.Panel({
    		id: 'go-module-panel-'+GO.settings.start_module,
    		region:'center',
    		border:false,
    		cls:'go-form-panel',
    		title: 'No modules',
    		html: '<h1>No modules available</h1>You have a valid account but you don\'t have access to any of the modules. Please contact the administrator if you feel this is an error.'
    	});
    }

		var adminMenuItems=[];
		var menuItemConfig;

		this.state = Ext.state.Manager.get('open-modules');

		
		for(var i=0;i<allPanels.length;i++){

			if(this.state && this.state.indexOf(allPanels[i].moduleName)>-1)
				items.push(GO.moduleManager.getPanel(allPanels[i].moduleName));
			
			menuItemConfig = {
					id:'go-start-menu-'+allPanels[i].moduleName,
					moduleName:allPanels[i].moduleName,
					text:allPanels[i].title,
					iconCls: 'go-menu-icon-'+allPanels[i].moduleName,
					handler: function(item, e){
						this.openModule(item.moduleName);
					},
					scope: this
				};

			if(!allPanels[i].admin){
				if(!this.state)
					items.push(GO.moduleManager.getPanel(allPanels[i].moduleName));
				
				this.startMenu.add(menuItemConfig);
			}else
			{
				adminMenuItems.push(menuItemConfig);
			}
		}
		
		if(adminMenuItems.length){

			this.startMenu.add(new Ext.menu.TextItem({id:'go-start-menu-admin-menu', text:'<div class="menu-title">'+GO.lang.adminMenu+'</div>'}));

			for(var i=0;i<adminMenuItems.length;i++){
				this.startMenu.add(adminMenuItems[i]);
			}
		}
    
    this.createTabPanel(items);

		this.beforeRender();
    
   var topPanel = new Ext.Panel({
        region:'north',
        contentEl: 'mainNorthPanel',
        cls: 'go-top-panel',
        height:28,
        titlebar:false,
        border:false
      });		
		
		GO.viewport = new Ext.Viewport({
        layout:'border',
        border:false,
        items:[topPanel,this.tabPanel]
      });    
    
    this.startMenuLink = Ext.get("start-menu-link");
			
		this.startMenuLink.on("click", function(){

			var x = this.startMenuLink.getX();
			var y = topPanel.el.getY()+topPanel.el.getHeight();

			this.startMenu.showAt([x,y]);
		},
		this);


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
	    				var win = window.open(GO.settings.config.help_link);
	    				win.focus();
	    			},
	    			scope:this	    			
	    		},{
	    			iconCls:'btn-forum',
	    			text:GO.lang.strCommunityForum,
	    			handler:function(){	    				
	    				var win = window.open('http://www.group-office.com/forum/');
	    				win.focus();
	    			},
	    			scope:this
	    			
	    		},'-',{
	    			iconCls: 'btn-support',
	    			text: GO.lang.contactSupportDesk,
	    			handler: function(){
	    				GO.supportLink=GO.settings.config.webmaster_email;
	    				if(Ext.form.VTypes.email(GO.supportLink))
	    				{
	    					if(GO.email && GO.settings.modules.email.read_permission)
	    					{
	    						GO.email.showComposer({
										values : {to: GO.supportLink}							
									});
	    					}else
	    					{
	    						document.location='mailto:'+GO.supportLink;
	    					}
	    				}else
	    				{
	    					window.open(GO.supportLink);
	    				}
	    			},
	    			scope:this
	    		
	    		},{
	    			iconCls:'btn-report-bug',
	    			text:GO.lang.strReportBug,
	    			handler:function(){
	    				var win = window.open('http://sourceforge.net/tracker/?group_id=76359&atid=547651');
	    				win.focus();			
	    			},
	    			scope:this
	    			
	    		},    		
	    		'-',{
    			iconCls:'btn-info',
    			text:GO.lang.strAbout.replace('{product_name}', GO.settings.config.product_name),
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
		
		
		GO.checker.init.defer(2000,GO.checker);
		GO.checker.on('alert', function(data){   		
   		if(data.notification_area)
   		{
   			Ext.get('notification-area').update(data.notification_area);
   		}
   	}, this);   	

		if (GO.settings.modules.search && GO.settings.modules.search.read_permission) {
			var searchField = new Ext.form.TextField({
				name:'search_query',
				enableKeyEvents:true,
				emptyText:GO.lang.strSearch+'...',
				listeners:{
					scope:this,
					keypress:function(field, e){
						if(e.getKey()==Ext.EventObject.ENTER){
							//this.addSearchPanel(field.getValue());
							if(!this.searchPanel){
								this.searchPanel = new GO.grid.SearchPanel(
									{
										query: field.getValue(),
										id:'go-search-panel'
									}
								);
								this.tabPanel.add(this.searchPanel);
							}else
							{
								this.searchPanel.query=field.getValue();
								this.searchPanel.load();
							}
							this.tabPanel.unhideTabStripItem(this.searchPanel);
							this.searchPanel.show();
						}
					},
					blur:function(field){
						field.reset();
					}
				},
				renderTo:"search_query"
			});
		}

		/*for(var i=0;i<items.length;i++){
			var menuItem = this.startMenu.items.item('go-start-menu-'+items[i].moduleName);
			menuItem.hide();
		}
		this.refreshMenu();*/

		Ext.QuickTips.register({
			text:GO.lang.rightClickToClose,
			title:GO.lang.closeApps,
			target:this.startMenuLink
		});


		this.rendered=true;
		this.fireEvent('render');

   	
   	this.removeLoadMask();
	},

	initModule : function(moduleName){

		var panelId = 'go-module-panel-'+moduleName;
		var panel;

		if(!this.tabPanel.items.map[panelId])
		{
			panel = GO.moduleManager.getPanel(moduleName);
                        if(panel)
                        {
                                panel.id = panelId;
                                this.tabPanel.add(panel);

                                /*if(!this.hintShown)
                                {
                                        this.msg(GO.lang.closeApps, GO.lang.rightClickToClose);
                                        this.hintShown=true;
                                }*/

                                this.saveState();
                        }else
                        {
                                return false;
                        }

		}else{
			panel = this.tabPanel.items.map[panelId];
			this.tabPanel.unhideTabStripItem(panel);
		}	

		return panel;
	},

	openModule : function(moduleName){

		var panel = this.initModule(moduleName);
                if(panel)
                {
                        panel.show();
                        return panel;
                }else
                {
                        return false;
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
	
	createBox : function (t, s, width){

			if(!width){
				width=400;
			}
			return ['<div class="msg" style="width:'+width+'px;position:relative;margin:auto">',
							'<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
							'<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3>', t, '</h3>', s, '</div></div></div>',
							'<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
							'</div>'].join('');
	},

  msg : function(title, format, time, width){

			if(!time){
				time=4;
			}
		
			if(!this.msgCt){
					this.msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
			}

			if (this.tabPanel){
				this.msgCt.alignTo(this.tabPanel.el, 'tr-tr');
			}else
			{
				this.msgCt.alignTo(Ext.getBody(), 't-t', [0,20]);
			}
			var s = String.format.apply(String, Array.prototype.slice.call(arguments, 1));
			var m = Ext.DomHelper.append(this.msgCt, {html:this.createBox(title, s, width)}, true);
			m.slideIn('t').pause(time).ghost("t", {remove:true});
	},
	onLinksDeletedHandler : function(link_types, modulePanel, store){
		if(link_types){
			for(var i=0;i<link_types.length;i++){
				if(store.getById(link_types[i])){
					modulePanel.on('show',function(){
						store.reload();
					}, this, {single:true});
				}
			}
		}
	}

	/*,
	
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
	}*/
});

GO.mainLayout = new GO.MainLayout();