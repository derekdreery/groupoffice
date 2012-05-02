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
 * @author Twan Verhofstad
 */


GO.bookmarks.groupingStore = new Ext.data.GroupingStore({
		reader: new Ext.data.JsonReader({
			totalProperty: "total",
			root: "results",
			id: "id",
		fields: ['id','category_id','category_name','user_id','name','content','description','logo','open_extern','permissionLevel','public_icon','thumb','behave_as_module']
		}),
		baseParams: {
			task:'get_bookmarks'
		},
		proxy: new Ext.data.HttpProxy({
			url : GO.url('bookmarks/bookmark/store')
		}),
		groupField:'category_name',
		sortInfo: {
			field: 'name',
			direction: 'ASC'
		},
		remoteGroup:true,
		remoteSort:true
	});



// laat categorieen zien
GO.bookmarks.writableCategoriesStore = new GO.data.JsonStore({
	url : GO.url('bookmarks/category/store'),
	baseParams: {
		permissionLevel:GO.permissionLevels.write
	},
	root: 'results',
	id: 'id',
	totalProperty:'total',
	fields: ['id', 'name', 'user_name'],
	remoteSort: true
});

// comboCategoriesStore, zelfde als writableCategoriesStore PLUS extra veldje met 'show all'

GO.bookmarks.comboCategoriesStore = new GO.data.JsonStore({
	    url : GO.url('bookmarks/category/store'),
	    baseParams: {
	    	},
	    fields: ['id','user_name','acl_id','name'],
	    remoteSort: true
	});


// store voor public icons, bestandsnamen
	GO.bookmarks.thumbstore = new GO.data.JsonStore(
	{
			url: GO.url('bookmarks/bookmark/thumbs'),
		
//		url: GO.settings.modules.bookmarks.url+ 'json.php',
//		baseParams: {
//			task: 'thumbdir'
//		}	,
		fields: ['filename']
	});




	