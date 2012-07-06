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

GO.files.FilesContextMenu = function(config)
{
	if(!config)
	{
		config = {};
	}
	config['shadow']='frame';
	config['minWidth']=180;

	this.downloadButton = new Ext.menu.Item({
		iconCls: 'btn-save',
		text: GO.lang.download,
		cls: 'x-btn-text-icon',
		handler: function(){
			window.open(GO.url("files/file/download",{
				id:this.records[0].data.id,
				inline:false
				}));
		},
		scope: this
	});



	/*this.pasteButton = new Ext.menu.Item({
					iconCls: 'btn-paste',
					text: 'Paste',
					cls: 'x-btn-text-icon',
					handler: function(){
						this.fireEvent('paste', this);
					},
					scope: this
				});*/

	this.deleteButton = new Ext.menu.Item({
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.fireEvent('delete', this, this.records, this.clickedAt);
		},
		scope: this
	});

	this.cutButton= new Ext.menu.Item({
		iconCls: 'btn-cut',
		text: GO.lang.cut,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.fireEvent('cut', this, this.records, this.clickedAt);
		},
		scope: this
	});
	this.copyButton = new Ext.menu.Item({
		iconCls: 'btn-copy',
		text: GO.lang.copy,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.fireEvent('copy', this, this.records, this.clickedAt);
		},
		scope: this
	});


	this.lockButton = new Ext.menu.Item({
		iconCls: 'btn-lock',
		text: GO.files.lang.lock,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function(){
			GO.request({
				url:'files/file/submit',
				params:{
					id:this.records[0].data.id,
					locked_user_id:GO.settings.user_id
				},
				success:function(action, response, result){

					var filesModulePanel = GO.mainLayout.getModulePanel('files');
					if(filesModulePanel && filesModulePanel.folder_id==this.records[0].data.folder_id) {
						filesModulePanel.getActiveGridStore().load();
						filesModulePanel.folderPanel.setVisible(false);
						filesModulePanel.filePanel.setVisible(true);
						filesModulePanel.filePanel.load(this.records[0].json.id,true);
					}

					if (!GO.util.empty(GO.files.fileBrowser))
						GO.files.fileBrowser.gridStore.load();
					if (!GO.util.empty(GO.selectFileBrowser))
						GO.selectFileBrowser.gridStore.load();
				},
				scope:this
			})
		}
	})

	this.unlockButton = new Ext.menu.Item({
		iconCls: 'btn-unlock',
		text: GO.files.lang.unlock,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function(){
			GO.request({
				url:'files/file/submit',
				params:{
					id:this.records[0].data.id,
					locked_user_id:0
				},
				success:function(action, response, result){
					var filesModulePanel = GO.mainLayout.getModulePanel('files');
					if(filesModulePanel && filesModulePanel.folder_id==this.records[0].data.folder_id) {
						filesModulePanel.getActiveGridStore().load();
						filesModulePanel.folderPanel.setVisible(false);
						filesModulePanel.filePanel.setVisible(true);
						filesModulePanel.filePanel.load(this.records[0].json.id,true);
					}
					if (!GO.util.empty(GO.files.fileBrowser))
						GO.files.fileBrowser.gridStore.load();
					if (!GO.util.empty(GO.selectFileBrowser))
						GO.selectFileBrowser.gridStore.load();
				},
				scope:this
			})
		}
	})

	this.compressButton = new Ext.menu.Item({
		iconCls: 'btn-compress',
		text: GO.lang.compress,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.fireEvent('compress', this, this.records, this.clickedAt);
		},
		scope: this
	});
	this.decompressButton = new Ext.menu.Item({
		iconCls: 'btn-decompress',
		text: GO.lang.decompress,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.fireEvent('decompress', this, this.records, this.clickedAt);
		},
		scope: this
	});

	config['items']=[this.downloadButton];

	if(GO.settings.modules.gota && GO.settings.modules.gota.read_permission)
	{
		this.gotaButton = new Ext.menu.Item({
			iconCls: 'btn-edit',
			text: GO.gota.lang.editLocally,
			cls: 'x-btn-text-icon',
			handler: function(){
				GO.files.editFile(this.records[0].data.id);
			},
			scope: this
		});
		config['items'].push(this.gotaButton);
	}
	config['items'].push(this.lockButton);
	config['items'].push(this.unlockButton);


	config['items'].push({
		iconCls: 'btn-properties',
		text: GO.lang['strProperties'],
		handler: function(){
			this.fireEvent('properties', this, this.records);
		},
		scope:this
	});

	config['items'].push(new Ext.menu.Separator());
	config['items'].push(this.cutButton);
	config['items'].push(this.copyButton);
	//this.pasteButton,
	config['items'].push(new Ext.menu.Separator());
	config['items'].push(this.deleteButton);
	config['items'].push(this.compressSeparator = new Ext.menu.Separator());
	config['items'].push(this.compressButton);
	config['items'].push(this.decompressButton);

	this.createDownloadLinkButton = new Ext.menu.Item({
		iconCls: 'btn-save',
		text: GO.files.lang.createDownloadLink,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.fireEvent('download_link', this, this.records, this.clickedAt, false);
		},
		scope: this
	});
	config['items'].push(this.createDownloadLinkButton);

	if(GO.settings.modules.email) {
		this.downloadLinkButton = new Ext.menu.Item({
			iconCls: 'btn-email',
			text: GO.files.lang.emailDownloadLink,
			cls: 'x-btn-text-icon',
			handler: function(){
				this.fireEvent('download_link', this, this.records, this.clickedAt, true);
			},
			scope: this
		});
		config['items'].push(this.downloadLinkButton);

		this.emailFilesButton = new Ext.menu.Item({
			iconCls: 'em-btn-email-files',
			text: GO.email.lang.emailFiles,
			cls: 'x-btn-text-icon',
			handler: function(){
				this.fireEvent('email_files', this, this.records);
			},
			scope: this
		});
		config['items'].push(this.emailFilesButton);
	}

	GO.files.FilesContextMenu.superclass.constructor.call(this, config);

	this.addEvents({

		'properties' : true,
		'paste' : true,
		'cut' : true,
		'copy' : true,
		'delete' : true,
		'compress' : true,
		'decompress' : true,
		'download_link' : true,
		'email_files' : true

	});

}

Ext.extend(GO.files.FilesContextMenu, Ext.menu.Menu,{
	/*tree or grid */

	clickedAt : 'grid',

	records : [],

	showAt : function(xy, records, clickedAt)
	{
		if(clickedAt)
			this.clickedAt = clickedAt;

		var extension = '';
		this.records = records;
		if(records.length=='1')
		{
			extension = records[0].data.extension;

			switch(extension)
			{
				case 'zip':
				case 'tar':
				case 'tgz':
				case 'gz':
					this.downloadButton.show();
					if(this.gotaButton)
						this.gotaButton.show();
					this.decompressButton.show();
					this.compressButton.hide();
					this.downloadLinkButton.show();
					break;

				case '':
					if (records[0].data.type=='Map') {
						this.downloadButton.hide();
						if(this.gotaButton)
							this.gotaButton.hide();
						this.decompressButton.hide();
						this.compressButton.show();
						this.downloadLinkButton.hide();
					} else {
						this.downloadButton.show();
						if(this.gotaButton)
							this.gotaButton.show();
						this.decompressButton.show();
						this.compressButton.hide();
						this.downloadLinkButton.show();
					}
					break;
				case 'folder':
					this.lockButton.hide();
					this.unlockButton.setVisible(false);
					this.downloadButton.hide();
					if(this.gotaButton)
						this.gotaButton.hide();
					this.decompressButton.hide();
					clickedAt == 'tree' ? this.compressButton.hide() : this.compressButton.show();
					this.downloadLinkButton.hide();

					break;

				default:
					this.lockButton.show();
					if(this.gotaButton)
						this.gotaButton.show();

					this.lockButton.setDisabled(this.records[0].data.locked_user_id>0);
					this.unlockButton.setVisible(this.records[0].data.locked_user_id>0);
					this.unlockButton.setDisabled(!this.records[0].data.unlock_allowed);

					if(this.gotaButton)
						this.gotaButton.setDisabled(this.records[0].data.permission_level<GO.permissionLevels.write || (this.records[0].data.locked_user_id>0 &&this.records[0].data.locked_user_id!=GO.settings.user_id));

					this.downloadButton.show();
					clickedAt == 'tree' ? this.compressButton.hide() : this.compressButton.show();
					this.decompressButton.hide();
					this.downloadLinkButton.show();
					break;
			}
		}else
		{
			clickedAt == 'tree' ? this.compressButton.hide() : this.compressButton.show();
			this.decompressButton.hide();
			this.downloadButton.hide();

			if(this.gotaButton)
				this.gotaButton.hide();
		}

		GO.files.FilesContextMenu.superclass.showAt.call(this, xy);
	}
});
