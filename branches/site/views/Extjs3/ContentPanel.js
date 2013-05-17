GO.site.ContentPanel = Ext.extend(Ext.form.FormPanel,{
	
	
	// Plugins for the editor
	editorImageInsertPlugin : false,
	editorTablePlugin : false,
	parentPanel : false,
	contentDialog : false,
	
	submitAction : 'update',
	setSiteId : function(siteId){
		this.form.baseParams.site_id = siteId;
	},
	load : function(contentId){
		this.setContentId(contentId);
		this.ownerCt.getLayout().setActiveItem(this);
		this.form.load({
			success:function(form, action){
				if(this.fileBrowseButton){
					this.fileBrowseButton.setId(action.result.data.site_id);
					this.editorImageInsertPlugin.setSiteId(action.result.data.site_id);
				}
			},
			scope:this
		});
	},
	create : function(siteId,parentId){
		this.setSiteId(siteId);
		this.setContentId(0, parentId);
		this.form.baseParams.parent_id = parentId;
		
		this.form.load({
			success:function(form, action){
				if(this.fileBrowseButton){
					this.fileBrowseButton.setId(action.result.data.site_id);
					this.editorImageInsertPlugin.setSiteId(action.result.data.site_id);
				}
				this.titleField.focus();
			},
			scope:this
		});
		
		
		
//		GO.request({
//			url: 'site/content/defaultSlug',
//			params: {
//				parentId: parentId
//			},
//			success: function(response, options, result){
//				this.parentSlug.setValue(result.defaultslug);
//			},
//			scope: this
//		});
		
		this.ownerCt.getLayout().setActiveItem(this);
	},
	setContentId : function(contentId, parentId){
		this.form.baseParams.id=contentId;
		this.reloadButton.setDisabled(!contentId);
		this.advancedButton.setDisabled(!contentId);
		
		delete this.form.baseParams.parent_id;
		
		if(!contentId){
			this.form.reset();
			this.submitAction = 'create';
		}else
		{
			this.submitAction = 'update';
		}		
	},
	
	constructor : function(config){
		config = config || {};
		
		config.id='site-content';
//		config.title = GO.site.lang.content;
		config.layout='form';
		config.border = false;
		config.url = GO.url('site/content/load');
		config.baseParams = {
			id:false
		}
		
		config.bodyStyle='padding:5px';
		config.labelWidth=60;
		
		this.reloadButton = new Ext.Button({
			iconCls: 'btn-refresh',
			itemId:'refresh',
			text: GO.site.lang.reload,
			cls: 'x-btn-text-icon'
		});

		this.reloadButton.on("click", function(){
		 // Reload the content
		 // TODO: check if there are changes in the content, then ask for saving first
		 this.form.load();
		},this);
		
		this.saveButton = new Ext.Button({
			iconCls: 'btn-save',
			itemId:'save',
			text: GO.site.lang.save,
			cls: 'x-btn-text-icon'
		});

		this.saveButton.on("click", function(){
		 // submit the content
		 this.form.submit({
				url:GO.url('site/content/'+this.submitAction),
				waitMsg:GO.lang['waitMsgSave'],
				success:function(form, action){
					this.setContentId(action.result.id);
					this.parentPanel.rebuildTree(true); // Rebuild the tree after submit
				},
				failure: function(form, action) {
					if(action.failureType == 'client')
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);
					else
						Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				},
				scope: this
			});
		},this);
		
		this.advancedButton = new Ext.Button({
			iconCls: 'btn-settings',
			itemId:'advanced',
			text: GO.site.lang.advanced,
			cls: 'x-btn-text-icon'
		});
		
		this.advancedButton.on("click", function(){
			this.showContentDialog(this.form.baseParams.id);
			
		},this);
		
		config.tbar=new Ext.Toolbar({
			hideBorders:true,
			items: [
				this.saveButton,
				this.reloadButton,
				this.advancedButton
			]
		});
		
		if (GO.files) {
			config.tbar.add(this.fileBrowseButton = new GO.files.FileBrowserButton({
				model_name:"GO_Site_Model_Site"
			}));
		}
		
		this.titleField = new Ext.form.TextField({
			name: 'title',
			width:300,
			maxLength: 255,
			allowBlank:false,
			fieldLabel: GO.site.lang.contentTitle
		});
		
		this.parentSlug = new Ext.form.TextField({
			name: 'parentslug',
			width:147,
			maxLength: 255,
			allowBlank:true,
			disabled:true
		});
		
		this.slugField = new Ext.form.TextField({
			name: 'slug',
			width:148,
			maxLength: 255,
			allowBlank:true,
			fieldLabel: GO.site.lang.contentSlug
		});
	
		this.completeSlug = new Ext.form.CompositeField({
			fieldLabel: GO.site.lang.contentSlug,
			items: [this.parentSlug,this.slugField]
		});
	
		this.titleField.on('change',function(field){
			this.slugField.setValue(this.formatSlug(field.getValue()));
		},this);
	
		
		this.editor = new GO.form.HtmlEditor({
			hideLabel:true,
			name: 'content',
			anchor: '100% -80',
			allowBlank:true,
			fieldLabel: GO.site.lang.contentContent,
			plugins:this.initHtmlEditorPlugins()
		});
				
		config.items = [
			this.titleField,
			this.completeSlug,
			this.editor
		];
		
		GO.site.ContentPanel.superclass.constructor.call(this, config);
		
		
	},
	showContentDialog : function(id){
		if(!this.contentDialog){
			this.contentDialog = new GO.site.ContentDialog();
			this.contentDialog.on('hide',function(){
				this.form.load();
			},this);
		}
		this.contentDialog.show(id);
	},
	formatSlug : function(slug){
		
		slug = slug.toLowerCase();
		slug = slug.replace(/[^a-z0-9]+/g, '-');
		slug = slug.replace(/^-|-$/g, '');		
		
		return slug;
	},
	initHtmlEditorPlugins : function(htmlEditorConfig) {		
		// optional image attachment
		this.editorImageInsertPlugin = new GO.site.HtmlEditorImageInsert();
		this.editorTablePlugin = new Ext.ux.form.HtmlEditor.Table();
			
		return [this.editorImageInsertPlugin,this.editorTablePlugin];
	}
});

