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

GO.email.EmailComposer = function(config) {
	Ext.apply(config);

	this.cls='em-composer';
	
	var priorityGroup = Ext.id();
	
	var optionsMenuItems = [
	this.notifyCheck = new Ext.menu.CheckItem({
		text : GO.email.lang.notification,
		checked : false,
		checkHandler : function(check, checked) {
			this.sendParams['notification'] = checked
			? 'true'
			: 'false';
		},
		scope : this
	}),
	'-',
	'<div class="menu-title">'
	+ GO.email.lang.priority + '</div>', {
		text : GO.email.lang.high,
		checked : false,
		group : priorityGroup,
		checkHandler : function() {
			this.sendParams['priority'] = '1';
		},
		scope : this
	}, this.normalPriorityCheck = new Ext.menu.CheckItem({
		text : GO.email.lang.normal,
		checked : true,
		group : priorityGroup,
		checkHandler : function() {
			this.sendParams['priority'] = '3';
		},
		scope : this
	}), {
		text : GO.email.lang.low,
		checked : false,
		group : priorityGroup,
		checkHandler : function() {
			this.sendParams['priority'] = '5';
		},
		scope : this
	},'-',this.htmlCheck = new Ext.menu.CheckItem({
		text:GO.email.lang.htmlMarkup,
		checked:GO.email.useHtmlMarkup,
		listeners : {
			checkchange: function(check, checked) {
								 	
				if(this.bodyContentAtWindowOpen==this.editor.getValue() || confirm(GO.email.lang.confirmLostChanges))
				{
					this.setContentTypeHtml(checked);
					/**
					 * reload dialog for text or html
					 */
					this.showConfig.keepEditingMode=true;
					var v = this.formPanel.form.getValues();
					delete v.body;
					delete v.textbody;
					
					if(!this.showConfig.values) this.showConfig.values={};
					Ext.apply(this.showConfig.values, v);

					
					delete this.showConfig.move;
					this.show(this.showConfig);
				}else
				{
					check.setChecked(!checked, true);
				}
			},
			scope:this
		}
	})];
						
	if(GO.gnupg)
	{
		optionsMenuItems.push('-');
			
		optionsMenuItems.push(this.encryptCheck = new Ext.menu.CheckItem({
			text:GO.gnupg.lang.encryptMessage,
			checked: false,
			listeners : {
				checkchange: function(check, checked) {
					if(this.formPanel.baseParams.content_type=='html')
					{
						if(!confirm(GO.gnupg.lang.confirmChangeToText))
						{
							check.setChecked(!checked, true);
							return false;
						}else
						{
							this.setContentTypeHtml(false);
							this.htmlCheck.setChecked(false, true);
							this.showConfig.keepEditingMode=true;
							this.show(this.showConfig);
						}
					}
						
					this.htmlCheck.setDisabled(checked);
						
					this.sendParams['encrypt'] = checked
					? '1'
					: '0';
								
					return true;
				},
				scope:this
			}
		}));
	}

	this.optionsMenu = new Ext.menu.Menu({
		items : optionsMenuItems
	});

	this.showMenu = new Ext.menu.Menu({
				
		items : [this.formFieldCheck = new Ext.menu.CheckItem({
			text : GO.email.lang.sender,
			checked : true,
			checkHandler : this.onShowFieldCheck,
			scope : this
		}),
		this.ccFieldCheck = new Ext.menu.CheckItem({
			text : GO.email.lang.ccField,
			checked : false,
			checkHandler : this.onShowFieldCheck,
			scope : this
		}),
		this.bccFieldCheck = new Ext.menu.CheckItem({
			text : GO.email.lang.bccField,
			checked : false,
			checkHandler : this.onShowFieldCheck,
			scope : this
		})
		]
	});


	var uploadItems = [];
	
	var version = deconcept.SWFObjectUtil.getPlayerVersion();
	if(!GO.settings.config.disable_flash_upload && version.major > 0)
	{
		uploadItems.push({
			iconCls:'btn-computer',
			text : GO.email.lang.attachFilesPC,
			handler : function()
			{
				if(!this.uploadFlashDialog)
				{
					var max = Math.floor(GO.settings.config.max_attachment_size/1048576)+'MB';

					this.uploadFlashDialog = new GO.UploadFlashDialog({
						uploadPanel: new Ext.ux.SwfUploadPanel({
							post_params : {
								"task" : 'upload_attachment'
							},
							upload_url : GO.settings.modules.email.url+ 'action.php',
							labelWidth: 110,

							file_size_limit:max,
							single_file_select: false, // Set to true if you only want to select one file from the FileDialog.
							confirm_delete: false, // This will prompt for removing files from queue.
							remove_completed: false // Remove file from grid after uploaded.
						}),
						title:GO.email.lang.attachments
					});

					this.uploadFlashDialog.on('fileUploadSuccess', function(obj, file, data)
					{
						this.attachmentsStore.loadData({
							'results' : data.file
						}, true);
					},this)
				}

				this.uploadFlashDialog.show();
			},
			scope:this
		})
	}else
	{
		this.uploadForm = new GO.UploadPCForm({
			waitMsgTarget:this.getId(),
			baseParams:{
				task:'attach_file'
			},
			addText: GO.email.lang.attachFilesPC,
			url:GO.settings.modules.email.url+'action.php'
		});
		this.uploadForm.on('upload', function(e, file)
		{
			this.attachmentsStore.loadData({
				'results' : file
			}, true);

			this.attachmentMenu.hide();

		},this);

		uploadItems.push(this.uploadForm);
	}
	
	if(GO.files)
	{
		uploadItems.push({
			iconCls:'btn-groupoffice',
			text : GO.email.lang.attachFilesGO.replace('{product_name}', GO.settings.config.product_name),
			handler : function()
			{
				if(GO.files)
				{
					GO.files.createSelectFileBrowser();

					GO.selectFileBrowser.setFileClickHandler(this.addRemoteFiles, this);

					GO.selectFileBrowser.setFilesFilter('');
					GO.selectFileBrowser.setRootID(0,0);
					GO.selectFileBrowserWindow.show();
				}
			},
			scope : this
		});
	}

	uploadItems.push('-');
	uploadItems.push({
		text : GO.email.lang.attachFilesPC+' (Java)',
		handler : function() {

			if (!deployJava.isWebStartInstalled('1.5.0')) {
				Ext.MessageBox.alert(GO.lang.strError,
					GO.lang.noJava);
			} else {

				//for updating attachments
				GO.attachmentsStore = this.attachmentsStore;

				GO.util.popup({
					url:
					GO.settings.modules.email.url+'jupload/index.php',
					width : 660,
					height: 500,
					target:
					'jupload'
				});
			}
		},
		scope : this
	});
	
	this.attachmentMenu = new Ext.menu.Menu(
	{
		items:uploadItems
	});
	

	var imageInsertPlugin = new GO.plugins.HtmlEditorImageInsert();
	
	imageInsertPlugin.on('insert', function(plugin, path, url,temp,id) {
		this.inline_attachments.push({
			tmp_file : id || path,
			url : url,
			temp:temp
		});
	}, this);


	var spellcheckInsertPlugin = new GO.plugins.HtmlEditorSpellCheck(this);
	var wordPastePlugin = new Ext.ux.form.HtmlEditor.Word();
	//var dividePlugin = new Ext.ux.form.HtmlEditor.Divider();
	//var tablePlugin = new Ext.ux.form.HtmlEditor.Table();
	var hrPlugin = new Ext.ux.form.HtmlEditor.HR();
	var ioDentPlugin = new Ext.ux.form.HtmlEditor.IndentOutdent();
	var ssScriptPlugin = new Ext.ux.form.HtmlEditor.SubSuperScript();
	var rmFormatPlugin = new Ext.ux.form.HtmlEditor.RemoveFormat();

	var items = [
	this.fromCombo = new Ext.form.ComboBox({
		store : GO.email.aliasesStore,
		fieldLabel : GO.email.lang.from,
		name : 'alias_name',
		anchor : '100%',
		displayField : 'name',
		valueField : 'id',
		hiddenName : 'alias_id',
		forceSelection : true,
		triggerAction : 'all',
		mode : 'local',
		tpl: '<tpl for="."><div class="x-combo-list-item">{name:htmlEncode}</div></tpl>',
		listeners:{
			beforeselect: function(cb, newAccountRecord){
				var oldAccountRecord = cb.store.getById(cb.getValue());
											
				var oldSig = oldAccountRecord.get(this.formPanel.baseParams.content_type+"_signature");
				var newSig = newAccountRecord.get(this.formPanel.baseParams.content_type+"_signature");

				var editorValue = this.editor.getValue();

				/*
				 *GO returns <br /> but the browse turns this into <br> so replace those
				 */
				if(this.formPanel.baseParams.content_type=='html'){
					editorValue = editorValue.replace(/<br>/g, '<br />');
				}
				if(GO.util.empty(oldSig))
				{
					this.addSignature(newAccountRecord);
				}else
				{
					this.editor.setValue(editorValue.replace(oldSig,newSig));
				}
			},
			scope:this
		}
	}),

	/*	this.toCombo = new Ext.ux.form.SuperBoxSelect({
		allowAddNewData:true,
		fieldLabel : GO.email.lang.sendTo,
		resizable: true,
		store: new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email','info'],
			root : 'persons'
		}),
		mode: 'remote',
		valueField : 'full_email',
		displayField : 'info',
		displayFieldTpl: '{full_email}',
		forceSelection : true,
		valueDelimiter:',',
		anchor:'100%',
		queryDelay: 0,
		triggerAction: 'all',
		name:'to[]',
		listeners: {
			removeitem : function(){
				this.setEditorHeight();
			},
			additem : function(){
				this.setEditorHeight();
			},
			scope:this
		}
	}),
	this.ccCombo = new Ext.ux.form.SuperBoxSelect({
		allowAddNewData:true,
		fieldLabel : GO.email.lang.cc,
		resizable: true,
		store: new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email','info'],
			root : 'persons'
		}),
		mode: 'remote',
		valueField : 'full_email',
		displayField : 'info',
		displayFieldTpl: '{full_email}',
		forceSelection : true,
		valueDelimiter:',',
		anchor:'100%',
		queryDelay: 0,
		triggerAction: 'all',
		name:'cc[]',
		listeners: {
			removeitem : function(){
				this.setEditorHeight();
			},
			additem : function(){
				this.setEditorHeight();
			},
			scope:this
		}
	}),
	this.bccCombo = new Ext.ux.form.SuperBoxSelect({
		allowAddNewData:true,
		fieldLabel : GO.email.lang.bcc,
		resizable: true,
		store: new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email','info'],
			root : 'persons'
		}),
		mode: 'remote',
		valueField : 'full_email',
		displayField : 'info',
		displayFieldTpl: '{full_email}',
		forceSelection : true,
		valueDelimiter:',',
		anchor:'100%',
		queryDelay: 0,
		triggerAction: 'all',
		name:'bcc[]',
		listeners: {
			removeitem : function(){
				this.setEditorHeight();
			},
			additem : function(){
				this.setEditorHeight();
			},
			scope:this
		}
	}),*/


	this.toCombo = new GO.form.ComboBoxMulti({
		sep : ',',
		fieldLabel : GO.email.lang.sendTo,
		name : 'to',
		anchor : '100%',
		height : 50,
		store : new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email','info'],
			root : 'persons'
		}),
		valueField : 'full_email',
		displayField : 'info'
	}),

	this.ccCombo = new GO.form.ComboBoxMulti({
		sep : ',',
		fieldLabel : GO.email.lang.cc,
		name : 'cc',
		anchor : '100%',
		height : 50,
		store : new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email'],
			root : 'persons'
		}),
		displayField : 'full_email',
		hideTrigger : true,
		minChars : 2,
		triggerAction : 'all',
		selectOnFocus : false

	}),

	this.bccCombo = new GO.form.ComboBoxMulti({
		sep : ',',
		fieldLabel : GO.email.lang.bcc,
		name : 'bcc',
		anchor : '100%',
		height : 50,
		store : new Ext.data.JsonStore({
			url : BaseHref + 'json.php',
			baseParams : {
				task : "email"
			},
			fields : ['full_email'],
			root : 'persons'
		}),
		displayField : 'full_email',
		hideTrigger : true,
		minChars : 2,
		triggerAction : 'all',
		selectOnFocus : false

	})];
								
	var anchor = -113;
						
	if(GO.settings.modules.savemailas && GO.settings.modules.savemailas.read_permission)
	{
		if (!this.selectLinkField) {
			this.selectLinkField = new GO.form.SelectLink({
				anchor : '100%'
			});					
			anchor+=26;
			items.push(this.selectLinkField);
		}
	}

	try {
		if(config && config.links)
		{
			if (!this.selectLinkField) {
				this.selectLinkField = new GO.form.SelectLink({
					anchor : '100%'
				});
				anchor+=26;
				items.push(this.selectLinkField);
			}
		}
	} catch(e) {}

	items.push(this.subjectField = new Ext.form.TextField({
		fieldLabel : GO.email.lang.subject,
		name : 'subject',
		anchor : '100%'
	}));

	var plugins = [
	imageInsertPlugin];
	
	if(GO.email.pspellSupport)
		plugins.push(spellcheckInsertPlugin);

	plugins.push(
		wordPastePlugin,
		hrPlugin,
		ioDentPlugin,
		rmFormatPlugin,
		ssScriptPlugin
		);

	items.push(this.htmlEditor = new Ext.form.HtmlEditor({
		hideLabel : true,
		name : 'body',
		style: 'font: 12px Arial, Helvetica, sans-serif;',
		anchor : '100% '+anchor,
		plugins : plugins,
		defaultFont:'arial',
		listeners:{
			activate:function(){

				var doc = this.htmlEditor.getDoc();				
				if (Ext.isGecko){					
					Ext.EventManager.on(doc, {
						keypress: this.fireSubmit,
						scope: this
					});
				}
				if (Ext.isIE || Ext.isWebKit || Ext.isOpera) {
					Ext.EventManager.on(doc, 'keydown', this.fireSubmit,
						this);
				}
			},
			scope:this
		},

		
		onFirstFocus : function(){
			this.activated = true;
			this.disableItems(this.readOnly);
			if(Ext.isGecko){ // prevent silly gecko errors
				/*this.win.focus();
            var s = this.win.getSelection();
            if(!s.focusNode || s.focusNode.nodeType != 3){
                var r = s.getRangeAt(0);
                r.selectNodeContents(this.getEditorBody());
                r.collapse(true);
                this.deferFocus();
            }*/
				try{
					this.execCmd('useCSS', true);
					this.execCmd('styleWithCSS', false);
				}catch(e){}
			}
			this.fireEvent('activate', this);
		},
		createToolbar : Ext.form.HtmlEditor.prototype.createToolbar.createSequence(function(editor){
			this.tb.enableOverflow=true;
		}),

		getDocMarkup : function(){
			var h = Ext.fly(this.iframe).getHeight() - this.iframePad * 2;
			return String.format('<html><head><style type="text/css">body{border: 0; margin: 0; padding: {0}px; height: {1}px; cursor: text}body p{margin:0px;}</style></head><body></body></html>', this.iframePad, h);
		},
		fixKeys : function(){ // load time branching for fastest keydown performance
			if(Ext.isIE){
				return function(e){
					var k = e.getKey(),
					doc = this.getDoc(),
					r;
					if(k == e.TAB){
						e.stopEvent();
						r = doc.selection.createRange();
						if(r){
							r.collapse(true);
							r.pasteHTML('&nbsp;&nbsp;&nbsp;&nbsp;');
							this.deferFocus();
						}
					}else if(k == e.ENTER){
				//                    r = doc.selection.createRange();
				//                    if(r){
				//                        var target = r.parentElement();
				//                        if(!target || target.tagName.toLowerCase() != 'li'){
				//                            e.stopEvent();
				//                            r.pasteHTML('<br />');
				//                            r.collapse(false);
				//                            r.select();
				//                        }
				//                    }
				}
				};
			}else if(Ext.isOpera){
				return function(e){
					var k = e.getKey();
					if(k == e.TAB){
						e.stopEvent();
						this.win.focus();
						this.execCmd('InsertHTML','&nbsp;&nbsp;&nbsp;&nbsp;');
						this.deferFocus();
					}
				};
			}else if(Ext.isWebKit){ 
            return function(e){
                var k = e.getKey();
                if(k == e.TAB){
                    e.stopEvent();
                    this.execCmd('InsertText','\t');
                    this.deferFocus();
                }else if(k == e.ENTER){
                    e.stopEvent();
                    var doc = this.getDoc();
                    if (doc.queryCommandState('insertorderedlist') ||
                        doc.queryCommandState('insertunorderedlist')) {
                      this.execCmd('InsertHTML', '</li><br /><li>');
                   } else {
                      this.execCmd('InsertHtml','<br />&nbsp;');
											this.execCmd('delete');
                   }
                    this.deferFocus();
                }
             };
			}
		}(),
		updateToolbar: function(){

			/*
				 * I override the default function here to increase performance.
				 * ExtJS syncs value every 100ms while typing. This is slow with large
				 * html documents. I manually call syncvalue when the message is sent
				 * so it's certain the right content is submitted.
				 */

			if(this.readOnly){
				return;
			}

			if(!this.activated){
				this.onFirstFocus();
				return;
			}

			var btns = this.tb.items.map,
			doc = this.getDoc();

			if(this.enableFont && !Ext.isSafari2){
				var name = (doc.queryCommandValue('FontName')||this.defaultFont).toLowerCase();
				if(name != this.fontSelect.dom.value){
					this.fontSelect.dom.value = name;
				}
			}
			if(this.enableFormat){
				btns.bold.toggle(doc.queryCommandState('bold'));
				btns.italic.toggle(doc.queryCommandState('italic'));
				btns.underline.toggle(doc.queryCommandState('underline'));
			}
			if(this.enableAlignments){
				btns.justifyleft.toggle(doc.queryCommandState('justifyleft'));
				btns.justifycenter.toggle(doc.queryCommandState('justifycenter'));
				btns.justifyright.toggle(doc.queryCommandState('justifyright'));
			}
			if(!Ext.isSafari2 && this.enableLists){
				btns.insertorderedlist.toggle(doc.queryCommandState('insertorderedlist'));
				btns.insertunorderedlist.toggle(doc.queryCommandState('insertunorderedlist'));
			}

			Ext.menu.MenuMgr.hideAll();

			//This property is set in javascript/focus.js. When the mouse goes into
			//the editor iframe it thinks it has lost the focus.
			GO.hasFocus=true;

		//this.syncValue();
		}
	}));


	items.push(this.textEditor = new Ext.form.TextArea({
		name: 'textbody',
		anchor : '100% '+anchor,
		hideLabel : true,
		cls:'em-plaintext-body-field'
	}));


	this.attachmentsId = Ext.id();

	// store for attachments needs to be created here because a forward action
	// might attachments
	this.attachmentsStore = new Ext.data.JsonStore({
		url : GO.settings.modules.email.url + 'json.php',
		baseParams : {
			task : 'attachments'
		},
		root : 'results',
		fields : ['tmp_name', 'name', 'size', 'type', 'extension', 'human_size', 'last_dir'],
		id : 'tmp_name'
	});


	this.attachmentsStore.on('remove', function()
	{
		
		}, this);

	this.attachmentsStore.on('load', function()
	{
		if(!this.attachmentsView.isVisible() && this.attachmentsStore.data.length)
		{
			this.attachmentsView.show();
		//this.attachmentsEl = Ext.get(this.attachmentsId);
		//this.attachmentsEl.on('contextmenu', this.onAttachmentContextMenu, this);
		}
		
		this.setEditorHeight();
	}, this);

	this.attachmentsView = new Ext.DataView({
		store:this.attachmentsStore,
		tpl: new Ext.XTemplate(
			GO.email.lang.attachments+':'+
			'<div style="overflow-x:hidden" id="'+this.attachmentsId+'" tabindex="0" class="em-attachments-container" >'+
			'<tpl for=".">',
			
			'<span class="filetype-link filetype-{extension} attachment-wrap x-unselectable" unselectable="on" style="float:left" id="'+'{tmp_name}'+'">{name} ({human_size})</span>'+
			'</tpl>'+
			'</div>',
			'<div class="x-clear"></div>'
			),
		multiSelect:true,
		autoHeight:true,
		autoScroll:true,
		overClass:'x-view-over',
		hidden:true,
		itemSelector:'span.attachment-wrap',
		
		listeners:{
			contextmenu:this.onAttachmentContextMenu,
			dblclick:this.downloadTemporaryAttachment,
			scope:this,
			render:function(){
				this.attachmentsView.getEl().tabIndex=0;
				var map = new Ext.KeyMap(this.attachmentsView.getEl(),{
					key: Ext.EventObject.DELETE,
					fn: function(key, e){
						this.removeSelectedAttachments();
					},
					scope:this
				});
			}
		}
	})
	
	items.push(this.attachmentsView);


	this.formPanel = new Ext.form.FormPanel({
		border : false,
		labelWidth : 100,
		waitMsgTarget : true,
		baseParams: {
			content_type:'html'
		},
		cls : 'go-form-panel',
		defaultType : 'textfield',
		items : items,
		keys:[{
			key: Ext.EventObject.ENTER,
			ctrl:true,
			fn: function(key, e){
				this.sendMail(false,false);
			},
			scope:this
		}]
	});

	//Set a long timeout for large attachments
	this.formPanel.form.timeout=3000;

	//the html markup from a signature changes when the editor is initialized. The initialize event fires too soon.
	//The first push event does the trick of changing the html.
	this.htmlEditor.on('push', function(){
		this.bodyContentAtWindowOpen=this.htmlEditor.getValue();
	}, this, {
		single:true
	});

	
		

	if (GO.mailings) {
		this.templatesStore = new GO.data.JsonStore({
			url : GO.settings.modules.mailings.url + 'json.php',
			baseParams : {
				'task' : 'authorized_templates'
			},
			root : 'results',
			totalProperty : 'total',
			id : 'id',
			fields : ['id', 'name', 'group', 'text','template_id','checked'],
			remoteSort : true
		});
	}

	var tbar = [this.sendButton = new Ext.Button({
		text : GO.email.lang.send,
		iconCls : 'btn-send',
		handler : function() {
			this.sendMail();
		},
		scope : this
	}), this.saveButton = new Ext.Button({
		iconCls : 'btn-save',
		text : GO.lang.cmdSave,
		handler : function() {
			this.sendMail(true);
		},
		scope : this
	}), {
		text : GO.email.lang.extraOptions,
		iconCls : 'btn-settings',
		menu : this.optionsMenu
	// assign menu by instance
	}	, this.showMenuButton = new Ext.Button({
		text : GO.email.lang.show,
		iconCls : 'btn-show',
		menu : this.showMenu
	// assign menu by instance
	})];


	this.attachmentsButton = new Ext.Button({
		text : GO.email.lang.attachments,
		iconCls : 'btn-attach',
		menu : this.attachmentMenu
	});

	tbar.push(this.attachmentsButton);


	if (GO.addressbook) {
		tbar.push({
			text : GO.addressbook.lang.addressbook,
			iconCls : 'btn-addressbook',
			handler : function() {
				if (!this.addressbookDialog) {
					this.addressbookDialog = new GO.email.AddressbookDialog();
					this.addressbookDialog.on('addrecipients',
						function(fieldName, selections) {
							this.addRecipients(fieldName,selections);
						}, this);
				}

				this.addressbookDialog.show();
			},
			scope : this
		});
	}

	if(GO.mailings){
		tbar.push(this.templatesBtn = new Ext.Button({

			iconCls:'ml-btn-mailings',
			text:GO.mailings.lang.emailTemplate,
			menu:this.templatesMenu = new GO.menu.JsonMenu({
				store:this.templatesStore,
				listeners:{
					scope:this,
					itemclick : function(item, e ) {
						if(item.template_id=='default'){
							this.templatesStore.baseParams.default_template_id=this.showConfig.template_id;
							this.templatesStore.load();
							delete this.templatesStore.baseParams.default_template_id;
						}else if(this.bodyContentAtWindowOpen==this.editor.getValue() || confirm(GO.email.lang.confirmLostChanges))
						{
							this.showConfig.template_id=item.template_id;
							this.showConfig.keepEditingMode=true;
							this.show(this.showConfig);
						}else
						{
							return false;							
						}
					}
				}
			})
		}));
	}

	var focusFn = function() {
		this.toCombo.focus();
	};

	GO.email.EmailComposer.superclass.constructor.call(this, {
		title : GO.email.lang.composeEmail,
		width : 750,
		height : 500,
		minWidth : 300,
		minHeight : 200,
		layout : 'fit',
		maximizable : true,
		collapsible : true,
		animCollapse : false,
		//plain : true,
		closeAction : 'hide',
		buttonAlign : 'center',
		focus : focusFn.createDelegate(this),
		tbar : tbar,
		items : this.formPanel
	});

	this.addEvents({
		//		attachmentDblClicked : true,
		zipOfAttachmentsDblClicked : true,
		'send' : true,
		'reset' : true,
		afterShowAndLoad:true,
		beforesendmail:true
	});
};

Ext.extend(GO.email.EmailComposer, GO.Window, {

	stateId : 'email-composer',
	
	showConfig : {},

	autoSaveTask : {},
	
	lastAutoSave : false,
	
	bodyContentAtWindowOpen : false,

	removeSelectedAttachments : function(){
		var records = this.attachmentsView.getSelectedRecords();
		for(var i=0;i<records.length;i++)
		{
			this.attachmentsStore.remove(records[i]);
		}
		this.attachmentsView.setVisible(this.attachmentsStore.data.length);
		this.setEditorHeight();
	},

	/*
	 *handles ctrl+enter from html editor
	 */
	fireSubmit : function(e) {
		if (e.ctrlKey && Ext.EventObject.ENTER == e.getKey()) {
			//e.stopEvent();
			this.sendMail(false, false);
		}
	},


	isHTML : function(){
		if (this.formPanel.baseParams.content_type == 'html'){
			return true;
		}else{
			return false;
		}
	},	
	
	setContentTypeHtml : function(checked){
		this.formPanel.baseParams.content_type = checked
		? 'html'
		: 'plain';
					
		//this.htmlCheck.setChecked(checked, true);

		this.htmlEditor.getEl().up('.x-form-item').setDisplayed(checked);
		this.textEditor.getEl().up('.x-form-item').setDisplayed(!checked);

		this.editor = checked ? this.htmlEditor : this.textEditor;
	},
	
	autoSave : function(){
		if(GO.util.empty(this.sendParams.mailing_group_id) && this.lastAutoSave && this.lastAutoSave!=this.editor.getValue())
		{
			this.sendMail(true,true);
		}
		this.lastAutoSave=this.editor.getValue();
	},
	
	startAutoSave : function(){
		this.lastAutoSave=false;
		Ext.TaskMgr.start(this.autoSaveTask);
	},
	
	stopAutoSave : function(){
		Ext.TaskMgr.stop(this.autoSaveTask);
	},
	
	afterRender : function() {
		GO.email.EmailComposer.superclass.afterRender.call(this);

		this.autoSaveTask={
			run: this.autoSave,
			scope:this,
			interval:120000
		//interval:5000
		};
		
		this.on('hide', this.stopAutoSave, this);

		this.on('resize', function()
		{
			this.setEditorHeight();
		})
	/*this[this.collapseEl].hideMode='offsets';

		this.on('beforecollapse', function(){
			console.log(this[this.collapseEl]);
			this[this.collapseEl].hideMode='offsets';
		}, this);		*/
	},

	toComboVisible : true,

	reset : function(keepAttachmentsAndOptions) {
		if(!keepAttachmentsAndOptions){
			this.sendParams = {
				'task' : 'sendmail',
				inline_attachments : {},
				inline_temp_attachments : {},
				notification : 'false',
				priority : '3',
				draft_uid : 0
			};

			this.showCC((GO.email.showCCfield == '1') ? true : false);
			this.showBCC((GO.email.showBCCfield == '1') ? true : false);			
			this.ccFieldCheck.setChecked((GO.email.showCCfield == '1') ? true : false);
			this.bccFieldCheck.setChecked((GO.email.showBCCfield == '1') ? true : false);
			
			if (this.defaultAcccountId) {
				this.fromCombo.setValue(this.defaultAcccountId);
			}
			this.notifyCheck.setChecked(false);
			this.normalPriorityCheck.setChecked(true);
			this.attachmentsStore.removeAll();
			this.setEditorHeight();
		}else
		{
			//keep options when switching from text <> html
			this.sendParams={
				'task' : 'sendmail',
				inline_attachments : {},
				inline_temp_attachments : {},
				notification : this.sendParams.notification,
				priority : this.sendParams.priority,
				draft_uid : this.sendParams.draft_uid,
				reply_uid : this.sendParams.reply_uid,
				reply_mailbox : this.sendParams.reply_mailbox,
				in_reply_to : this.sendParams.in_reply_to,
				forward_uid : this.sendParams.forward_uid,
				forward_mailbox : this.sendParams.forward_mailbox
			};
			
		}
		this.inline_attachments = [];
		this.inline_temp_attachments = [],
		this.formPanel.form.reset();
		this.htmlEditor.SpellCheck = false;
		
		this.fireEvent("reset", this);
	},

	showCC : function(show){
		this.ccCombo.getEl().up('.x-form-item').setDisplayed(show);
		if(show)
		{
			this.ccCombo.onResize();
		}		
	},
	
	showBCC : function(show){
		this.bccCombo.getEl().up('.x-form-item').setDisplayed(show);		
		if(show)
		{
			this.bccCombo.onResize();
		}
	},

	addRecipients : function(fieldName,selections) {
		var field = this.formPanel.form.findField(fieldName);

		var currentVal = field.getValue();
		if (currentVal != '' && currentVal.substring(currentVal.length-1,currentVal.length) != ',' && currentVal.substring(currentVal.length-2,currentVal.length-1)!=',')
			currentVal += ', ';

		currentVal += selections;

		field.setValue(currentVal);

		if (fieldName == 'cc') {
			this.ccFieldCheck.setChecked(true);
		} else if (fieldName == 'bcc') {
			this.bccFieldCheck.setChecked(true);
		}
	},
//
//	setRecipients : function(fieldName,selections) {
//		var field = this.formPanel.form.findField(fieldName);
//		field.setValue(selections);
//		field.store.load();
//	},

	show : function(config) {

		//TODO enable after testing
		//Ext.getBody().mask(GO.lang.waitMsgLoad);

		delete this.link_config;

		this.showConfig=config;
		
		if (!this.rendered) {
				
			Ext.Ajax.request({
				url: GO.settings.modules.email.url+'json.php',
				params:{
					task:'init_composer'
				},
				callback: function(options, success, response)
				{

					if(!success)
					{
						alert( GO.lang['strRequestError']);
					}else
					{
						var jsonData = Ext.decode(response.responseText);


						this.fromCombo.store.loadData(jsonData.aliases);

						if(this.templatesStore)
							this.templatesStore.loadData(jsonData.templates);

						var records = this.fromCombo.store.getRange();
						if (records.length) {
							if (!config.account_id) {
								this.showConfig.account_id = records[0].data.account_id;
							}

							this.render(Ext.getBody());
							this.show(this.showConfig);

							return;

						} else {
							Ext.getBody().unmask();
							Ext.Msg.alert(GO.email.lang.noAccountTitle,
								GO.email.lang.noAccount);
						}


					}
				},
				scope:this
			});
			
			this.htmlEditor.SpellCheck = false;
		/*} else if (config.template_id == undefined && this.templatesStore
			&& this.templatesStore.getTotalCount() > 1) {
			//this.showConfig = config;
			Ext.getBody().unmask();
			this.templatesWindow.show();*/
		} else {

			/*if (config.template_id == undefined && this.templatesStore
				&& this.templatesStore.getTotalCount() == 1) {
				config.template_id = this.templatesStore.data.items[0]
				.get('id');
			}*/

			if (typeof(config.template_id) == 'undefined' && this.templatesStore){
				var templateRecordIndex = this.templatesStore.findBy(function(record,id){
					return record.get('checked');
				});

				if(templateRecordIndex>-1)
					config.template_id=this.templatesStore.getAt(templateRecordIndex).get('template_id');
			}

			//check the right template menu item.
			if(this.templatesStore && config.template_id && this.templatesMenu.items){
				var item = this.templatesMenu.items.find(function(item){
					return item.template_id==config.template_id;
				});
				item.setChecked(true);
			}

			
			//this.inline_attachments = [];

			//keep attachments when switchting from text <> html
			this.reset(config.keepEditingMode);

			
			//save the mail to a file location
			if(config.saveToPath){
				this.sendParams.save_to_path=config.saveToPath;
				this.sendButton.hide();
			}else
			{
				this.sendButton.show();
			}


			var index=-1;
			if (config.account_id) {
				index = this.fromCombo.store.findBy(function(record, id){
					return record.get('account_id')==config.account_id;
				});
			}

			//find by e-mail
			if(config.from){
				index = this.fromCombo.store.findBy(function(record, id){
					return record.get('email')==config.from;
				});
			}
			if(index==-1)
			{
				index=0;
			}
			this.fromCombo.setValue(this.fromCombo.store.data.items[index].id);

			if (config.values) {
				this.formPanel.form.setValues(config.values);
			}

			//this will be true when swithing from html to text or vice versa
			if(!config.keepEditingMode)
			{
				//remove attachments if not switching edit mode
				//this.attachmentsStore.removeAll();
				
				this.setContentTypeHtml(GO.email.useHtmlMarkup);
				this.htmlCheck.setChecked(GO.email.useHtmlMarkup, true);
				if(this.encryptCheck)
					this.encryptCheck.setChecked(false, true);
			}			

			this.toComboVisible = true;
			this.showMenuButton.setDisabled(false);
			this.toCombo.getEl().up('.x-form-item').setDisplayed(true);
			this.sendURL = GO.settings.modules.email.url + 'action.php';
			this.saveButton.setDisabled(false);
		
			this.notifyCheck.setChecked(GO.email.alwaysRequestNotification);
			
			if(config.move)
			{
				var pos = this.getPosition();
				this.setPagePosition(pos[0]+config.move, pos[1]+config.move);
			}
			
			
			// for mailings plugin
			if (config.mailing_group_id > 0) {
				this.sendURL = GO.settings.modules.mailings.url
				+ 'action.php';

				this.toComboVisible = false;
				this.showMenuButton.setDisabled(true);
				this.toCombo.getEl().up('.x-form-item').setDisplayed(false);
				this.showCC(false);
				this.showBCC(false);

				this.sendParams.mailing_group_id = config.mailing_group_id;

				this.saveButton.setDisabled(true);
			}else
			{
				this.ccFieldCheck.setChecked(GO.email.showCCfield == '1');
				this.bccFieldCheck.setChecked(GO.email.showBCCfield == '1');
			/*if(GO.email.showCCfield == '1')
				{
					this.showCC(true);
				}
				if(GO.email.showBCCfield == '1')
				{
					this.showBCC(true);
				}*/
			}

			if (config.uid || config.template_id || config.loadUrl) {
				if (!config.task) {
					config.task = 'template';
				}
				
				if(config.task=='opendraft')
					this.sendParams.draft_uid = config.uid;
				
				var fromRecord = this.fromCombo.store.getById(this.fromCombo.getValue());

				var params = config.loadParams ? config.loadParams : {
					uid : config.uid,
					account_id : fromRecord.get('account_id'),
					task : config.task,
					mailbox : config.mailbox
				};

				//for directly loading a contact in a template
				if(config.contact_id)
					params.contact_id=config.contact_id;
				
				if (config.mailing_group_id > 0) {
					// so that template loading won't replace fields
					params.mailing_group_id = config.mailing_group_id;
				}

				//if (config.template_id>0) {
				params.template_id=config.template_id;
				params.to = this.toCombo.getValue();
				//}

				var url = config.loadUrl
				? config.loadUrl
				: GO.settings.modules.email.url + 'json.php';

				//sometimes this is somehow copied from the baseparams
				delete params.content_type;

				if (typeof(config.values)!='undefined' && typeof(config.values.body)!='undefined')
					params.body = config.values.body;

				this.formPanel.form.load({
					url : url,
					params : params,
					waitMsg : GO.lang.waitMsgLoad,
					failure:function(form, action)
					{
						Ext.getBody().unmask();
						Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
					},
					success : function(form, action) {

						if (config.task == 'reply'
							|| config.task == 'reply_all') {
							this.sendParams['reply_uid'] = config.uid;
							this.sendParams['reply_mailbox'] = config.mailbox;
							this.sendParams['in_reply_to']=action.result.data.in_reply_to;
						}else if (config.task == 'forward'){
							this.sendParams['forward_uid'] = config.uid;
							this.sendParams['forward_mailbox'] = config.mailbox;
						}



						if (action.result.data.inline_attachments) {
							this.inline_attachments = action.result.data.inline_attachments;
						}

						if (!config.keepEditingMode && action.result.data.attachments) {
							this.attachmentsStore.loadData({
								results : action.result.data.attachments
							}, true);
						}

						this.afterShowAndLoad(params.task!='opendraft', config);
					},
					scope : this
				});

			}else
			{
				this.afterShowAndLoad(true, config);
			}
			if (config.link_config) {
				this.link_config = config.link_config;
				if (config.link_config.type_id && typeof(this.selectLinkField)!='undefined') {
					this.selectLinkField.setValue(config.link_config.type_id);
					this.selectLinkField.setRemoteText(config.link_config.text);
				}
			}
		}
	},

	insertDefaultFont : function(){
		var font = this.htmlEditor.fontSelect.dom.value;
		var v = this.htmlEditor.getValue();
		if(v.toLowerCase().substring(0,5)!='<font'){
			if(v=='')
				v='<br />';
			
			v='<font face="'+font+'">'+v+'</font>'
		}

		this.htmlEditor.setValue(v);		
	},
	
	afterShowAndLoad : function(addSignature, config){
		if(addSignature)
		{
			this.addSignature();
		}
		
		if(this.formPanel.baseParams.content_type=='plain')
		{
			//set cursor at top
			this.editor.selectText(0,0);
		}else
		{
			//if(this.htmlEditor.activated){
			this.insertDefaultFont();
		/*}else
			{
				this.htmlEditor.on('activate', this.insertDefaultFont, this);
			}*/
		}

		this.setEditorHeight();
		this.startAutoSave();
		this.bodyContentAtWindowOpen=this.editor.getValue();

		var oldShowCC=GO.email.showCCfield;
		var oldShowBCC=GO.email.showBCCfield;
		this.ccFieldCheck.setChecked(GO.email.showCCfield == '1' || this.ccCombo.getValue()!='');
		this.bccFieldCheck.setChecked(GO.email.showBCCfield == '1' || this.bccCombo.getValue()!='');
	
		//we only want to send these settings when a user manually enables the cc field.
		//when this is done automatically by loading the data we don't want to save that.
		delete this.sendParams['email_show_cc'];
		delete this.sendParams['email_show_bcc'];
		GO.email.showCCfield=oldShowCC;
		GO.email.showBCCfield=oldShowBCC;

		if(config.afterLoad)
		{
			if(!config.scope)
				config.scope=this;
			config.afterLoad.call(config.scope);
		}

		Ext.getBody().unmask();
		GO.email.EmailComposer.superclass.show.call(this);


		if (this.toCombo.getValue() == '') {
			this.toCombo.focus();
		} else {
			this.editor.focus();
		}
		
		this.fireEvent('afterShowAndLoad',this);
	},
	
	addSignature : function(accountRecord){
		accountRecord = accountRecord || this.fromCombo.store.getById(this.fromCombo.getValue());
			
		var sig = accountRecord.get(this.formPanel.baseParams.content_type+"_signature");
		
		if(!GO.util.empty(sig))
		{
			if(this.formPanel.baseParams.content_type=='plain')
			{
				sig = "\n"+sig+"\n";
			}else
			{
				sig = '<br /><div id="EmailSignature">'+sig+'</div><br />';
			}
		}
		
		this.editor.setValue(sig+this.editor.getValue());
	},

	showAttachmentsDialog : function() {
		if (!this.attachmentsDialog) {
			var tbar = [];

			tbar.push({
				// id : 'add-local',
				iconCls : 'btn-add',
				text : GO.email.lang.attachFilesPC,
				cls : 'x-btn-text-icon',
				handler : function() {

					this.uploadDialog.show();
				},
				scope : this
			});

			if (GO.files) {
				tbar.push({
					// id : 'add-remote',
					iconCls : 'btn-add',
					text : GO.email.lang.attachFilesGO.replace('{product_name}', GO.settings.config.product_name),
					cls : 'x-btn-text-icon',
					handler : function() {

						GO.files.createSelectFileBrowser();

						GO.selectFileBrowser.setFileClickHandler(this.addRemoteFiles, this);

						GO.selectFileBrowser.setFilesFilter('');
						GO.selectFileBrowser.setRootID(0,0);
						GO.selectFileBrowserWindow.show();

					/*if (!this.fileBrowser) {
							this.fileBrowser = new GO.files.FileBrowser({
								border : false,
								fileClickHandler : this.addRemoteFiles,
								filePanelCollapsed:true,
								scope : this
							});

							this.fileBrowserWindow = new Ext.Window({
								title : GO.lang.strSelectFiles,
								height : 450,
								width : 750,
								layout : 'fit',
								border : false,
								closeAction : 'hide',
								items : this.fileBrowser,
								buttons : [{
									text : GO.lang.cmdOk,
									handler : this.addRemoteFiles,
									scope : this
								}, {
									text : GO.lang.cmdClose,
									handler : function() {
										this.fileBrowserWindow
										.hide();
									},
									scope : this
								}]
							});
						}
						this.fileBrowserWindow.show();*/
					},
					scope : this
				});
			}

			tbar.push({
				// id : 'delete',
				iconCls : 'btn-delete',
				text : GO.lang.cmdDelete,
				cls : 'x-btn-text-icon',
				handler : function() {

					var rows = this.attachmentsGrid.selModel
					.getSelections();
					for (var i = 0; i < rows.length; i++)
						this.attachmentsStore.remove(rows[i]);

				},
				scope : this
			});

			this.attachmentsGrid = new GO.grid.GridPanel({
				// id : 'groups-grid-overview-users',
				store : this.attachmentsStore,
				loadMask:true,
				columns : [{
					header : GO.lang.strName,
					dataIndex : 'name'
				}, {
					header : GO.lang.strSize,
					dataIndex : 'size',
					renderer: function(v){
						return  Ext.util.Format.fileSize(v);
					}
				}, {
					header : GO.lang.strType,
					dataIndex : 'type'
				}],

				sm : new Ext.grid.RowSelectionModel({
					singleSelect : false
				}),
				view : new Ext.grid.GridView({
					forceFit : true,
					autoFill : true
				}),
				tbar : tbar
			});

			this.attachmentsDialog = new Ext.Window({
				title : GO.email.lang.attachments,
				layout : 'fit',
				modal : false,
				closeAction : 'hide',
				minWidth : 300,
				minHeight : 300,
				height : 400,
				width : 600,
				items : this.attachmentsGrid,
				buttons : [{
					text : GO.lang.cmdClose,
					handler : function() {
						this.attachmentsDialog.hide()
					},
					scope : this
				}]
			});

			var uploadFile = new GO.form.UploadFile({
				inputName : 'attachments',
				addText : GO.lang.smallUpload
			});

			this.upForm = new Ext.form.FormPanel({
				fileUpload : true,
				waitMsgTarget : true,
				items : [uploadFile, new Ext.Button({
					text : GO.lang.largeUpload,
					handler : function() {
						if (!deployJava.isWebStartInstalled('1.5.0')) {
							Ext.MessageBox.alert(GO.lang.strError,
								GO.lang.noJava);
						} else {

							/*
									 * crashes firefox in ubuntu GO.util.popup({
									 * url : GO.settings.modules.email.url +
									 * 'jupload/index.php', width : 640, height :
									 * 500 });
									 */
							window.open(GO.settings.modules.email.url+'jupload/index.php');

							this.uploadDialog.hide();
							// for refreshing by popup
							GO.attachmentsStore = this.attachmentsStore;
						}
					},
					scope : this
				})],
				cls : 'go-form-panel'
			});

			this.uploadDialog = new Ext.Window({
				title : GO.email.lang.uploadAttachments,
				layout : 'fit',
				modal : true,
				height : 300,
				width : 300,
				items : this.upForm,
				closeAction:'hide',
				buttons : [{
					text : GO.email.lang.startTransfer,
					handler : function() {
						this.upForm.form.submit({
							waitMsg : GO.lang.waitMsgUpload,
							url : GO.settings.modules.email.url
							+ 'action.php',
							params : {
								task : 'attach_file'
							},
							success : function(form, action) {

								this.attachmentsStore.loadData({
									'results' : action.result.files
								}, true);

								uploadFile.clearQueue();

								this.uploadDialog.hide();

							},
							scope : this
						});
					},
					scope : this
				}, {
					text : GO.lang.cmdClose,
					handler : function() {
						this.uploadDialog.hide()
					},
					scope : this
				}]
			});
		}
		this.attachmentsDialog.show();
	},

	addRemoteFiles : function() {

		var AttachmentRecord = Ext.data.Record.create([{
			name : 'tmp_name'
		},{
			name : 'id'
		}, {
			name : 'name'
		}, {
			name : 'type'
		}, {
			name : 'size'
		}, {
			name : 'extension'
		}, {
			name : 'human_size'
		}]);

		var selections = GO.selectFileBrowser.getSelectedGridRecords();

		for (var i = 0; i < selections.length; i++)
		{			
			var newRecord = new AttachmentRecord({
				id : selections[i].data.id,
				tmp_name : selections[i].data.id,
				name : selections[i].data.name,
				type : selections[i].data.type,
				size : selections[i].data.size,
				extension : selections[i].data.extension,
				human_size : (selections[i].data.size=='-') ? selections[i].data.size : Ext.util.Format.fileSize(selections[i].data.size)
			});
			newRecord.id = selections[i].data.path;
			this.attachmentsStore.add(newRecord);
		}

		if(!this.attachmentsView.isVisible() && this.attachmentsStore.data.length)
		{
			this.attachmentsView.show();
			this.attachmentsEl = Ext.get(this.attachmentsId);
			this.attachmentsEl.on('contextmenu', this.onAttachmentContextMenu, this);
		//			this.attachmentsEl.on('dblclick', this.openAttachment, this);
		}

		this.setEditorHeight();
		
		GO.selectFileBrowserWindow.hide();

	},

	//	openAttachment :  function(e, item_id, target)
	//	{
	//		if(target.id.substr(0,this.attachmentsId.length)==this.attachmentsId)
	//		{
	//			var attachment_no = target.id.substr(this.attachmentsId.length+1);
	//
	//			if(attachment_no=='zipofall')
	//			{
	//				this.fireEvent('zipOfAttachmentsDblClicked');
	//			}else
	//			{
	//				var attachment = this.data.attachments[attachment_no];
	//				this.fireEvent('attachmentDblClicked', attachment, this);
	//			}
	//		}
	//	},

	HandleResult : function (btn){
		if (btn == 'yes'){
			this.htmlEditor.SpellCheck = true;
			this.sendMail();
		}else{
			this.editor.plugins[1].spellcheck();
		}
	},

	submitForm : function(hide){
		this.sendMail(false, false);
	},

	sendMail : function(draft, autoSave) {
		//prevent double send with ctrl+enter
		if(this.sendButton.disabled){
			return false;
		}		

		/*if (this.isHTML() && this.htmlEditor.SpellCheck == false && !draft){
			//Ask if they want to run a spell check
			Ext.MessageBox.confirm(GO.lang.strConfirm, GO.lang.spellcheckAsk, function (btn){self.HandleResult(btn,self);});
			return false;
		}*/
		
		
		if(!draft && !autoSave && !this.fireEvent('beforesendmail', this))
			return false;
		

		if (this.uploadDialog && this.uploadDialog.isVisible()) {

			if(autoSave)
				return false;

			alert(GO.email.lang.closeUploadDialog);
			this.attachmentsDialog.show();
			this.uploadDialog.show();
			return false;
		}

		this.saveButton.setDisabled(true);
		this.sendButton.setDisabled(true);

		if (autoSave || this.subjectField.getValue() != ''
			|| confirm(GO.email.lang.confirmEmptySubject)) {
			if (this.attachmentsDialog && this.attachmentsDialog.isVisible()) {
				this.attachmentsDialog.hide();
			}

			if (this.attachmentsStore && this.attachmentsStore.data.keys.length) {
				
				var attachments = [];
				
				var records = this.attachmentsStore.getRange();
				for(var i=0;i<records.length;i++)
				{
					attachments.push(Ext.util.Format.htmlDecode(records[i].get('tmp_name')));
				}
				
				this.sendParams['attachments'] = Ext.encode(attachments);
			}

			this.sendParams['inline_attachments'] = Ext
			.encode(this.inline_attachments);

			this.sendParams['inline_temp_attachments'] = Ext
			.encode(this.inline_temp_attachments);

			this.sendParams.draft = draft;

			// extra sync to make sure all is in there.
			this.htmlEditor.syncValue();

			var waitMsg=null;
			if(!autoSave){
				waitMsg = draft ? GO.lang.waitMsgSave : GO.email.lang.sending;
			}
			
			if(!autoSave && !draft){
				this.stopAutoSave();
			}

			this.formPanel.form.submit({
				url : this.sendURL,
				params : this.sendParams,
				waitMsg : waitMsg,
				waitMsgTarget : autoSave ? null : this.formPanel.body,
				success : function(form, action) {
					
					this.saveButton.setDisabled(false);
					this.sendButton.setDisabled(false);
					
					if (action.result.account_id) {
						this.account_id = action.result.account_id;
					}

					if(!draft)
					{
						if (this.callback) {
							if (!this.scope) {
								this.scope = this;
							}
	
							var callback = this.callback.createDelegate(this.scope);
							callback.call();
						}
						// this.reset();
	
						if (GO.addressbook && action.result.unknown_recipients
							&& action.result.unknown_recipients.length) {
							if (!GO.email.unknownRecipientsDialog)
								GO.email.unknownRecipientsDialog = new GO.email.UnknownRecipientsDialog();
	
							GO.email.unknownRecipientsDialog.store.loadData({
								recipients : action.result.unknown_recipients
							});
	
							GO.email.unknownRecipientsDialog.show();
						}

						if (this.link_config && this.link_config.callback) {
							this.link_config.callback.call(this);
						}
	
						this.fireEvent('send', this);
					
						this.hide();
					}else
					{
						this.sendParams.draft_uid = action.result.draft_uid;
						
						this.fireEvent('save', this);
					}
				},

				failure : function(form, action) {
					if(!autoSave)
					{
						var fb = action.result && action.result.feedback ? action.result.feedback : GO.lang.strRequestError;
						Ext.MessageBox.alert(GO.lang.strError, fb);
					}
					this.saveButton.setDisabled(false);
					this.sendButton.setDisabled(false);
				},
				scope : this

			});
		} else {
			this.subjectField.focus();
			this.saveButton.setDisabled(false);
			this.sendButton.setDisabled(false);
		}
	},

	onShowFieldCheck : function(check, checked) {
		
		switch (check.id) {
			case this.formFieldCheck.id :
				this.fromCombo.getEl().up('.x-form-item').setDisplayed(checked);
				break;

			case this.ccFieldCheck.id :
				GO.email.showCCfield = (checked) ? '1' : '0';
				this.sendParams['email_show_cc'] = GO.email.showCCfield;
				this.showCC(checked);				
				break;

			case this.bccFieldCheck.id :
				GO.email.showBCCfield = (checked) ? '1' : '0';
				this.sendParams['email_show_bcc'] = GO.email.showBCCfield;
				this.showBCC(checked);
				break;
		}
		this.setEditorHeight();
	},

	setEditorHeight : function() {

		var subjectEl = this.subjectField.getEl().up('.x-form-item');
		var height = subjectEl.getHeight()+subjectEl.getMargins('tb');

		var attachmentsEl = this.attachmentsView.getEl();
		attachmentsEl.setHeight("auto");
		var attachmentsElHeight = attachmentsEl.getHeight();
		
		if(attachmentsElHeight > 89)
		{
			attachmentsElHeight = 89;
			this.attachmentsView.getEl().setHeight(attachmentsElHeight);
		}			
		height += attachmentsElHeight+attachmentsEl.getMargins('tb');
		
		if(GO.settings.modules.savemailas && GO.settings.modules.savemailas.read_permission)
		{
			var slEl = this.selectLinkField.getEl().up('.x-form-item');
			height += slEl.getHeight()+slEl.getMargins('tb');
		}
	
		if (this.toComboVisible) {
			var toEl = this.toCombo.getEl().up('.x-form-item');
			height += toEl.getHeight()+toEl.getMargins('tb');
		}

		var el;
		for (var i = 0; i < this.showMenu.items.items.length; i++) {
			if (this.showMenu.items.items[i].checked) {
				if(i==0)
				{
					el=this.fromCombo.getEl().up('.x-form-item');
				}else
				{
					el=this.toCombo.getEl().up('.x-form-item');
				}
				height += el.getHeight()+el.getMargins('tb');
			}
		}
		height+=4;
		
		var newAnchor = "100% -"+height;

		//reset anchor and delete cached anchorSpec
		this.htmlEditor.anchor=newAnchor;
		delete this.htmlEditor.anchorSpec;
		this.textEditor.anchor=newAnchor;
		delete this.textEditor.anchorSpec;
		
		this.htmlEditor.syncSize();
		this.formPanel.doLayout();
	},

	downloadTemporaryAttachment : function(dv, index, node, e ) {

		var record = dv.store.getAt(index);
		
			
		if(!record.data.id){
			Ext.Ajax.request({
					url: GO.settings.modules.email.url+'json.php',
					params:{
						task: 'create_download_hash',
						filename: record.data.name
					},
					callback: function(options, success, response)
					{
						if(!success)
						{
							alert( GO.lang['strRequestError']);
						}else
						{
							var jsonData = Ext.decode(response.responseText);
							var code = jsonData.code;
							document.location=GO.settings.modules.email.url+'download_file.php?last_dir='+record.data.last_dir+'&filename='+encodeURIComponent(record.data.name)+'&code='+code;
						}
					},
					scope:this
				});
		}else
		{
			GO.files.openFile(record);
		}
	},

	onAttachmentContextMenu : function(dv, index, node, e)
	{
		if(!this.menu)
		{
			this.menu = new Ext.menu.Menu({
				id:'email-attachmentsgrid-ctx',
				items: [
				{
					text:GO.lang.cmdDelete,
					scope:this,
					handler: function()
					{
						this.removeSelectedAttachments();
					}
				}]
			});
		}

		if(!this.attachmentsView.isSelected(node))
		{
			this.attachmentsView.select(node);
		}		

		e.preventDefault();
		this.menu.showAt(e.getXY());		
	}
});

GO.email.TemplatesList = function(config) {

	Ext.apply(config);
	var tpl = new Ext.XTemplate(
		'<div id="template-0" class="go-item-wrap">'+GO.mailings.lang.noTemplate+'</div>',
		'<tpl for=".">',
		'<div id="template-{id}" class="go-item-wrap"">{name}</div>',
		'<tpl if="!GO.util.empty(default_template)"><div class="ml-template-default-spacer"></div></tpl>',
		'</tpl>');

	GO.email.TemplatesList.superclass.constructor.call(this, {
		store : config.store,
		tpl : tpl,
		singleSelect : true,
		autoHeight : true,
		overClass : 'go-view-over',
		itemSelector : 'div.go-item-wrap',
		selectedClass : 'go-view-selected'
	});
}

Ext.extend(GO.email.TemplatesList, Ext.DataView, {
	onRender : function(ct, position) {
		this.el = ct.createChild({
			tag : 'div',
			cls : 'go-select-list'
		});

		GO.email.TemplatesList.superclass.onRender.apply(this,
			arguments);
	}

});
