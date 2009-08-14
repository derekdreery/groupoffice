/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 


GO.ModuleManager = Ext.extend(function(){	
	this.addEvents({
		'moduleReady' : true
	});	
	this.resumeEvents();
}, Ext.util.Observable,
{
	modules : {},
	modulePanels : {},
	panelConfigs : {},
	sortOrder : Array(),
	
	adminModulePanels : {},
	adminPanelConfigs : {},
	adminSortOrder : Array(),
	
	
	settingsPanels : {},
	settingsPanelConfigs : {},
	settingsSortOrder : Array(),
	
	readyFunctions : {},
	
	
	addSettingsPanel : function(panelID, panelClass, panelConfig)
	{		
		this.settingsPanels[panelID] = panelClass;
		this.settingsPanelConfigs[panelID] = panelConfig;
		this.settingsSortOrder.push(panelID);
	},
	
	getSettingsPanel : function(panelID)
	{
		if(this.settingsPanels[panelID])
			return new this.settingsPanels[panelID](this.settingsPanelConfigs[panelID]);
		else
			return false;				
	},
	
	getAllSettingsPanels : function(){
		
		var panels = [];
		
		for(var i=0;i<this.settingsSortOrder.length;i++)
		{
			panels.push(this.getSettingsPanel(this.settingsSortOrder[i]));	
		}
		return panels;
	},
	
	addModule : function(moduleName, panelClass, panelConfig)
	{		
		this.modules[moduleName]=true;
		if(panelClass)
		{
			panelConfig.moduleName = moduleName;
			panelConfig.id='go-module-panel-'+panelConfig.moduleName;
			
			this.modulePanels[moduleName] = panelClass;
			this.panelConfigs[moduleName] = panelConfig;
			this.sortOrder.push(moduleName);
		}
		this.onAddModule(moduleName);
		
	},
	
	onAddModule : function(moduleName)
	{
		if(this.readyFunctions[moduleName])
		{
			for(var i=0;i<this.readyFunctions[moduleName].length;i++)
			{
				var c = this.readyFunctions[moduleName][i];
				c.fn.call(c.fn.scope,moduleName,this);
			}
		}
	},
	
	onModuleReady : function(module, fn, scope)
	{
		scope=scope||window;

		if(!this.modules[module]){			
			this.readyFunctions[module] = this.readyFunctions[module] || [];			
			this.readyFunctions[module].push({
				fn:fn,
				scope: scope				
			});
		}else{
			fn.call(scope, module, this);
		}
	},
	
	/*addAdminModule : function(moduleName, panelClass, panelConfig)
	{
		panelConfig.moduleName = moduleName;
		
		this.modules[moduleName]=true;
		
		this.adminModulePanels[moduleName] = panelClass;
		this.adminPanelConfigs[moduleName] = panelConfig;
		this.adminSortOrder.push(moduleName);
		
		this.onAddModule(moduleName);
	},
	
	getAdminPanel : function(moduleName)
	{
		if(this.adminModulePanels[moduleName])
			return new this.adminModulePanels[moduleName](this.adminPanelConfigs[moduleName]);
		else
			return false;				
	},
	
	getAllAdminPanels : function(){
		
		var panels = [];
		
		for(var i=0;i<this.adminSortOrder.length;i++)
		{
			panels.push(this.getAdminPanel(this.adminSortOrder[i]));	
		}
		return panels;
	},
	
	getAllAdminPanelConfigs : function(){
		var configs = [];
		
		for(var i=0;i<this.adminSortOrder.length;i++)
		{
			configs.push(this.adminPanelConfigs[this.adminSortOrder[i]]);	
		}
		return configs;		
	},*/
	
	getPanel : function(moduleName)
	{
		if(this.modulePanels[moduleName])
			return new this.modulePanels[moduleName](this.panelConfigs[moduleName]);
		else
			return false;				
	},
	
	getAllPanels : function(){
		
		var panels = [];
		
		for(var i=0;i<this.sortOrder.length;i++)
		{
			panels.push(this.getPanel(this.sortOrder[i]));	
		}
		return panels;
	},

	getAllPanelConfigs : function(){
		var configs = [];

		for(var i=0;i<this.sortOrder.length;i++)
		{
			configs.push(this.panelConfigs[this.sortOrder[i]]);
		}
		return configs;
	}
				
});


GO.moduleManager = new GO.ModuleManager();