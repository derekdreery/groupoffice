
GO.{module}.writable{related_friendly_multiple}Store = new GO.data.JsonStore({
					    url: GO.settings.modules.{module}.url+ 'json.php',
					    baseParams: {
					    	auth_type:'write',
					    	task: '{related_friendly_multiple}'
					    	},
					    root: 'results',
					    id: 'id',
					    totalProperty:'total',
					    fields: [{STOREFIELDS}],
					    remoteSort: true
					});
					
GO.{module}.readable{related_friendly_multiple}Store = new GO.data.JsonStore({
					    url: GO.settings.modules.{module}.url+ 'json.php',
					    baseParams: {
					    	auth_type:'read',
					    	task: '{related_friendly_multiple}'
					    	},
					    root: 'results',
					    id: 'id',
					    totalProperty:'total',
					    fields: [{STOREFIELDS}],
					    remoteSort: true
					});