GO.site.multifileStore = new GO.data.JsonStore({
	url: GO.url('site/multifile/store'),		
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id','folder_id','name','locked_user_id','ctime','mtime','size','user_id','comment','extension','expire_time','random_code','thumb_url','thumbURL','order','model_id','field_id'],
	remoteSort: false,
	model:"GO_Files_Model_File"
});

GO.site.availableMenuParentsStore = new GO.data.JsonStore({
	url: GO.url('site/menuItem/parentStore'),
	baseParams: {
		id:0,
		site_id:0
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id','label'],
	remoteSort: false,
	model:"GO_Site_Model_MenuItem"
});

GO.site.availableMenuContentsStore = new GO.data.JsonStore({
	url: GO.url('site/menu/contentStore'),
	baseParams: {
		menu_id:0
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id','title'],
	remoteSort: false,
	model:"GO_Site_Model_Content"
});

GO.site.linkTargetStore = new Ext.data.ArrayStore({
	storeId: 'linktargets',
	idIndex: 0,
	fields:['value','label'],
	data: [
		['_blank','_BLANK'],
		['_self','_SELF'],
		['_parent','_PARENT'],
		['_top','_TOP']
	]
});