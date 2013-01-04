GO.notes.writableCategoriesStore = new GO.data.JsonStore({
	url: GO.settings.modules.notes.url+ 'json.php',
	baseParams: {
		auth_type:'write',
		task: 'categories'
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id', 'name', 'user_name'],
	remoteSort: true
});

GO.notes.writableAdminCategoriesStore = new GO.data.JsonStore({
	url: GO.settings.modules.notes.url+ 'json.php',
	baseParams: {
		auth_type:'write',
		task: 'categories'
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id', 'name', 'user_name'],
	remoteSort: true
});


GO.notes.readableCategoriesStore = new GO.data.JsonStore({
	url: GO.settings.modules.notes.url+ 'json.php',
	baseParams: {
		task: 'categories',
		auth_type: 'read',
		limit:GO.settings.config.nav_page_size
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id','user_name','acl_id','name','checked'],
	remoteSort: true
});
