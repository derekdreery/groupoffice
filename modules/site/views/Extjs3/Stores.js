GO.site.multifileStore = new GO.data.JsonStore({
	url: GO.url('site/multifile/store'),		
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id','folder_id','name','locked_user_id','ctime','mtime','size','user_id','comment','extension','expire_time','random_code','thumb_url','thumbURL','order','model_id','field_id'],
	remoteSort: false,
	model:"GO_Files_Model_File"
});