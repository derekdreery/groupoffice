/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: FoldersDialog.js 1651 2008-12-29 15:00:48Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.cms.FoldersDialog = function(config) {

	if (!config) config = {};

	Ext.apply(this, config);

	if(!config || !config.user_id) var user_id = 0 ;
	if(!config || !config.site_id) var site_id = 0 ;

	this.foldersTree = new Ext.tree.TreePanel({
				animate : true,
				border : false,
				autoScroll : true,
				height : 200,
				loader : new Ext.tree.TreeLoader({
							dataUrl : GO.settings.modules.cms.url
									+ 'json.php',
							baseParams : {
								task : 'tree-edit',
								user_id : user_id,
								site_id : site_id
							},
							preloadChildren : true,
							listeners : {
								beforeload : function() {
									this.body.mask(GO.lang.waitMsgLoad);
								},
								load : function() {
									this.body.unmask();
								},
								scope : this
							}
						})
			});

	// set the root node
	this.rootNode = new Ext.tree.AsyncTreeNode({
				text : GO.cms.lang.root,
				draggable : false,
				id : 'folder_root',
				folder_id : 0,
				expanded : false
			});
	this.foldersTree.setRootNode(this.rootNode);

	this.rootNode.on('load', function() {
				this.rootNode.select();

			}, this);

	this.foldersTree.on('checkchange', function(node, checked) {

				this.body.mask(GO.lang.waitMsgSave, 'x-mask-loading');

				var task = checked ? 'subscribe' : 'unsubscribe';

				Ext.Ajax.request({
							url : GO.settings.modules.cms.url + 'action.php',
							params : {
								task : task,
								user_id : GO.cms.user_id,
								folder_id : node.attributes.folder_id
							},
							callback : function(options, success, response) {
								if (!success) {
									Ext.MessageBox.alert(GO.lang.strError,
											response.result.feedback);
								}
								this.body.unmask();
							},
							scope : this
						});

			}, this);

	var treeEdit = new Ext.tree.TreeEditor(this.foldersTree, {
				ignoreNoChange : true
			});

	GO.cms.FoldersDialog.superclass.constructor.call(this, {
		layout : 'fit',
		modal : false,
		shadow : false,
		minWidth : 300,
		minHeight : 300,
		height : 400,
		width : 500,
		plain : true,
		closeAction : 'hide',
		title : GO.cms.lang.folders,

		items : this.foldersTree,

		buttons : [{
					text : GO.lang.cmdClose,
					handler : function() {
						this.hide();
					},
					scope : this
				}]
	});
}

Ext.extend(GO.cms.FoldersDialog, Ext.Window, {

			show : function(user_id,site_id) {

				this.render(Ext.getBody());

				this.site = site_id;
				this.foldersTree.loader.baseParams.user_id = GO.cms.user_id = user_id;
				this.foldersTree.loader.baseParams.site_id = site_id;

				if (!this.rootNode.isExpanded())
					this.rootNode.expand();
				else
					this.rootNode.reload();

				GO.cms.FoldersDialog.superclass.show.call(this);

			}
/*
			,getSubscribtionData : function() {
				var data = [];
				for (var i = 0; i < this.allFoldersStore.data.items.length; i++) {
					data[i] = {
						id : this.allFoldersStore.data.items[i].get('id'),
						subscribed : this.allFoldersStore.data.items[i]
								.get('subscribed'),
						name : this.allFoldersStore.data.items[i].get('name')
					};
				}
				return data;
			}
			*/
		});