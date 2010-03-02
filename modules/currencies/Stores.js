GO.currencies.currenciesStore = new GO.data.JsonStore({
	    url: GO.settings.modules.currencies.url+ 'json.php',
	    baseParams: {
	    	task: 'currencies'
	    	},
	    root: 'results',
	    id: 'code',
	    totalProperty:'total',
	    fields: ['code','symbol','value'],
	    remoteSort: true
	});