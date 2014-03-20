GO.site.ContentPanel = Ext.extend(Ext.form.FormPanel, {
	// Plugins for the editor
	editorLinkInsertPlugin: false,
	editorImageInsertPlugin: false,
	editorTablePlugin: false,
	parentPanel: false,
	contentDialog: false,
	submitAction: 'update',
	setSiteId: function(siteId) {
		this.form.baseParams.site_id = siteId;

		if (this.fileBrowseButton) {
			this.fileBrowseButton.setId(siteId);
//					this.editorImageInsertPlugin.setSiteId(action.result.data.site_id);
//					this.editorLinkInsertPlugin.setSiteId(action.result.data.site_id);
		}
	},
	load: function(contentId) {
		this.setContentId(contentId);
		this.ownerCt.getLayout().setActiveItem(this);
		this.form.load({
			method: 'GET',
			url: GO.url('site/content/update'),
			success: function(form, action) {


				this.setSiteId(action.result.data.site_id);
			},
			scope: this
		});
	},
	create: function(siteId, parentId) {
		this.setSiteId(siteId);
		this.setContentId(0, parentId);
		this.form.baseParams.parent_id = parentId;

		this.form.load({
			method: 'GET',
			url: GO.url('site/content/create'),
			success: function(form, action) {
				this.titleField.focus();
			},
			scope: this
		});


		this.ownerCt.getLayout().setActiveItem(this);
	},
	setContentId: function(contentId, parentId) {
		this.form.baseParams.id = contentId;
		this.advancedButton.setDisabled(!contentId);

		delete this.form.baseParams.parent_id;

		if (!contentId) {
			this.form.reset();
			this.submitAction = 'create';
		} else
		{
			this.submitAction = 'update';
		}
	},
	constructor: function(config) {
		config = config || {};

		config.id = 'site-content';
//		config.title = GO.site.lang.content;
		config.layout = 'form';
		config.border = false;
		config.url = GO.url('site/content/update');
		config.baseParams = {
			id: false
		}

//		config.bodyStyle='padding:5px';
		config.labelWidth = 60;


		this.saveButton = new Ext.Button({
			iconCls: 'btn-save',
			itemId: 'save',
			text: GO.site.lang.save,
			cls: 'x-btn-text-icon'
		});

		this.saveButton.on("click", function() {
			// submit the content
			this.form.submit({
				url: GO.url('site/content/' + this.submitAction),
				waitMsg: GO.lang['waitMsgSave'],
				success: function(form, action) {
					this.setContentId(action.result.id);
					this.parentPanel.rebuildTree(true); // Rebuild the tree after submit
				},
				failure: function(form, action) {
					if (action.failureType == 'client')
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);
					else
						Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				},
				scope: this
			});
		}, this);

		this.advancedButton = new Ext.Button({
			iconCls: 'btn-settings',
			itemId: 'advanced',
			text: GO.site.lang.advanced,
			cls: 'x-btn-text-icon'
		});

		this.advancedButton.on("click", function() {
			this.showContentDialog(this.form.baseParams.id);

		}, this);

		config.tbar = new Ext.Toolbar({
//			hideBorders:true,
			style: 'margin-bottom:10px;',
			items: [
				this.saveButton,
				this.advancedButton
			]
		});

		if (GO.files) {
			config.tbar.add(this.fileBrowseButton = new GO.files.FileBrowserButton({
				model_name: "GO\\Site\\Model\\Site"
			}));
		}

		this.titleField = new Ext.form.TextField({
			name: 'title',
			width: 300,
			maxLength: 255,
			allowBlank: false,
			fieldLabel: GO.site.lang.contentTitle
		});

		this.parentSlug = new Ext.form.TextField({
			name: 'parentslug',
			width: 147,
			maxLength: 255,
			allowBlank: true,
			disabled: true
		});

		this.slugField = new Ext.form.TextField({
			name: 'baseslug',
			width: 148,
			maxLength: 255,
			allowBlank: true,
			fieldLabel: GO.site.lang.contentSlug
		});

		this.completeSlug = new Ext.form.CompositeField({
			fieldLabel: GO.site.lang.contentSlug,
			items: [this.parentSlug, this.slugField]
		});

		this.titleField.on('change', function(field) {
			this.slugField.setValue(this.formatSlug(field.getValue()));
		}, this);

//		
//		this.editor = new GO.form.HtmlEditor({
//			hideLabel:true,
//			name: 'content',
//			anchor: '100% -80',
//			allowBlank:true,
//			enableLinks:false,
//			fieldLabel: GO.site.lang.contentContent,
//			plugins:this.initHtmlEditorPlugins()
//		});

		this.editor = new Ext.form.TextArea({
			hideLabel: true,
			style: 'font-family: "Lucida Console", Monaco, monospace;padding:10px;line-height:16px;',
			name: 'content',
			anchor: '100% -80',
			allowBlank: true,
			fieldLabel: GO.site.lang.contentContent,
			listeners: {
				render: function() {
					this.editor.getEl().on('paste', this.handlePaste, this);

					var editor = this.editor;

					var contentDD = new Ext.dd.DropTarget(this.editor.getEl(), {
						// must be same as for tree
						ddGroup: 'site-tree',
						notifyDrop: function(dd, e, node) {
							
							console.log(node);
							
							if(node.node && node.node.attributes.slug){
								//dragged from content tree
								var tag = '{site:link slug="' + node.node.attributes.slug + '"}' + node.node.text + '{/site:link}';

								editor.insertAtCursor(tag);
							
							}else{
								//dragged from file browser
								
								if(node.grid){
									
									var record = node.selections[0].data;
									
									if(record.extension=='folder'){
										return false;
									}
									
									var pos = record.path.indexOf('files/');
									
									var tag = '{site:link path="' + record.path.substring(pos,record.path.length)+'"}' +record.name + '{/site:link}';
									
									editor.insertAtCursor(tag);
								}
								
								
							}
							return true;
							
						}
					});
					
					contentDD.addToGroup('FilesDD');

				},
				scope: this
			}
		});



		config.items = [
			this.titleField,
			this.completeSlug,
			this.editor
		];
		GO.site.ContentPanel.superclass.constructor.call(this, config);
	},
	handlePaste: function(e) {

		var bE = e.browserEvent;

		for (var i = 0; i < bE.clipboardData.items.length; i++) {
			var item = bE.clipboardData.items[i];
			if (item.kind === "file") {
				this.uploadFile(item.getAsFile());
			}
		}
	},
	uploadFile: function(file) {
		var xhr = new XMLHttpRequest();

		xhr.upload.onprogress = function(e) {
			var percentComplete = (e.loaded / e.total) * 100;
			console.log("Uploaded: " + percentComplete + "%");
		};

		xhr.onload = function() {
			if (xhr.status == 200) {
				alert("Sucess! Upload completed");
			} else {
				alert("Error! Upload failed");
			}
		};

		xhr.onerror = function() {
			alert("Error! Upload failed. Can not connect to server.");
		};

		var self = this;

		xhr.onreadystatechange = function()
		{
			if (xhr.readyState == 4 && xhr.status == 200)
			{
				var result = Ext.decode(xhr.responseText);

				var tag;

				if (result.isImage) {
					tag = "{site:thumb path=\"" + result.path + "\" lw=\"300\" ph=\"300\"}";
				} else
				{
					tag = "{site:link path=\"" + result.path + "\"}" + result.path + "{/site:link}";
				}

				self.editor.insertAtCursor(tag);
			}
		}

		var filename = prompt("Please enter the file name", this.slugField.getValue());

		xhr.open("POST", GO.url('site/content/paste', {
			site_id: this.form.baseParams.site_id,
			filename: filename,
			filetype: file.type
		}));

		var formData = new FormData();
		formData.append("pastedFile", file);

		xhr.send(formData);
	},
	showContentDialog: function(id) {
		if (!this.contentDialog) {
			this.contentDialog = new GO.site.ContentDialog();
			this.contentDialog.on('hide', function() {
				this.form.load();
			}, this);
		}
		this.contentDialog.show(id);
	},
	formatSlug: function(slug) {

		slug = slug.toLowerCase();
		slug = slug.replace(/[^a-z0-9]+/g, '-');
		slug = slug.replace(/^-|-$/g, '');

		return slug;
	}
//	initHtmlEditorPlugins : function(htmlEditorConfig) {		
//		// insertLink plugin
//		this.editorLinkInsertPlugin = new GO.site.HtmlEditorLinkInsert({toolbarPosition : 17,toolbarSeparatorAfter:true});
//		
//		// optional image attachment
//		this.editorImageInsertPlugin = new GO.site.HtmlEditorImageInsert({toolbarPosition : 19,toolbarSeparatorAfter:true});
//		this.editorTablePlugin = new Ext.ux.form.HtmlEditor.Table();
//			
//		return [this.editorLinkInsertPlugin,this.editorImageInsertPlugin,this.editorTablePlugin, new Ext.ux.form.HtmlEditor.HeadingMenu()];
//	}
});

