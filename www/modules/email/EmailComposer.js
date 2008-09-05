/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.php 2954 2008-09-03 11:35:34Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.email.EmailComposer = function(config){
	Ext.apply(config);
	
	this.optionsMenu = new Ext.menu.Menu({
        id: 'optionsMenu',
        items: [
           this.notifyCheck = new Ext.menu.CheckItem({
               text: GO.email.lang.notification,
               checked: false,
               checkHandler:function(check, checked){	           	
	           	this.sendParams['notification']=checked? 'true' : 'false';
	           },
	           scope: this
           }),
           '-',
           '<b class="menu-title">'+GO.email.lang.priority+'</b>', 
           {      
	           text: GO.email.lang.high,
	           checked: false,
	           group: 'priority',
	           checkHandler:function(){	           	
	           	this.sendParams['priority']='1';
	           },
	           scope: this
	       }, this.normalPriorityCheck = new Ext.menu.CheckItem({
	           text: GO.email.lang.normal,
	           checked: true,
	           group: 'priority',
	           checkHandler:function(){	           	
	           	this.sendParams['priority']='3';
	           },
	           scope: this
	       }), {
	           text: GO.email.lang.low,
	           checked: false,
	           group: 'priority',
	           checkHandler:function(){	           	
	           	this.sendParams['priority']='5';
	           },
	           scope: this
	       }
        ]
    });
	
	
	this.showMenu = new Ext.menu.Menu({
    id: 'showMenu',
    items: [
			this.formFieldCheck = new Ext.menu.CheckItem({
				id: 'fromFieldCheck',
		    text: GO.email.lang.sender,
		    checked: true,
		    checkHandler: this.onShowFieldCheck,
		    scope: this
			}),
			this.ccFieldCheck = new Ext.menu.CheckItem({
				id: 'ccFieldCheck',
		    text: GO.email.lang.ccField,
		    checked: false,
		    checkHandler: this.onShowFieldCheck,
		    scope: this
			}),
			this.bccFieldCheck = new Ext.menu.CheckItem({
				id: 'bccFieldCheck',
		    text: GO.email.lang.bccField,
		    checked: false,
		    checkHandler: this.onShowFieldCheck,
		    scope: this
			})

     ]
    });
	
	
	var imageInsertPlugin = new GO.plugins.HtmlEditorImageInsert();
        
  imageInsertPlugin.on('insert', function(plugin){
    			this.inline_attachments.push({
    				tmp_file: plugin.selectedPath,
    				url: plugin.selectedUrl
    			});
  		}, this);
  		
 	this.selectLinkField = new GO.form.SelectLink( 	
	{
		anchor:'100%'      		
	});


	
	this.formPanel = new Ext.form.FormPanel({
		baseCls: 'x-plain',
        labelWidth: 100,
        url:'save-form.php',
        defaultType: 'textfield',
        items: [
        this.fromCombo = new Ext.form.ComboBox({
	        store: new Ext.data.JsonStore({
						url: BaseHref+'modules/email/json.php',		
						baseParams: {
							"task": 'accounts',
							personal_only: true
						},
						fields: ['id', 'email'],
						root: 'results',
						totalProperty: 'total',
						id: 'id'
					}),
					fieldLabel: GO.email.lang.from,
					name: 'account_name',
					anchor: '100%',
					displayField:'email',
					valueField:'id',
					hiddenName:'account_id',
					forceSelection: true,
					triggerAction: 'all',
					mode: 'local'   
        }),        
        
        this.toCombo = new GO.form.ComboBoxMulti({
        	sep:',',
          fieldLabel: GO.email.lang.sendTo,
          name: 'to',
          anchor:'100%',  // anchor width by percentage          
          height:50,
          store: new Ext.data.JsonStore({
          	 url:BaseHref+'json.php',
          	 baseParams:{task: "email"},
             fields: ['full_email'],
             root:'persons'
           }),
          displayField:'full_email'            
        }),     
        
        this.ccCombo = new GO.form.ComboBoxMulti({
        	sep:',',
        	id: 'cc',
            fieldLabel: GO.email.lang.cc,
            name: 'cc',
            anchor:'100%',  // anchor width by percentage
            height:50,
            store: new Ext.data.JsonStore({
            	 url:BaseHref+'json.php',
            	 baseParams:{task: "email"},
               fields: ['full_email'],
               root:'persons'
             }),
            displayField:'full_email', //I'm interested in technology 'name'
            hideTrigger: true,
            minChars: 2,
            triggerAction: 'all',
            selectOnFocus: false
            
        }),
        
        
        
        this.bccCombo = new GO.form.ComboBoxMulti({
        	sep:',',
        	id: 'bcc',
            fieldLabel: GO.email.lang.bcc,
            name: 'bcc',
            anchor:'100%',  // anchor width by percentage
            height:50,
            store: new Ext.data.JsonStore({
            	 url:BaseHref+'json.php',
            	 baseParams:{task: "email"},
                 fields: ['full_email'],
                 root:'persons'
             }),
            displayField:'full_email', //I'm interested in technology 'name'
            hideTrigger: true,
            minChars: 2,
            triggerAction: 'all',
            selectOnFocus: false
            
        }),
        this.selectLinkField,
        this.subjectField = new Ext.form.TextField({
            fieldLabel: GO.email.lang.subject,
            name: 'subject',
            anchor: '100%'
        }), this.htmlEditor = new Ext.form.HtmlEditor({
            hideLabel: true,
            name: 'body',
            anchor: '100% -130',  // anchor width by percentage and height by raw adjustment
            plugins: imageInsertPlugin
        })]
    });
    
    
    	
    
    
    //store for attachments needs to be created here because a forward action might attachments
    this.attachmentsStore =new Ext.data.JsonStore({
    		url: GO.settings.modules.email.url+'json.php',
    		baseParams: {task:'attachments'},
    		root:'results',
		    fields: ['tmp_name', 'name', 'size', 'type'],	
		    id: 'tmp_name'
		});
		
		this.attachmentsStore.on('remove', this.updateAttachmentsButton, this);
		this.attachmentsStore.on('load', this.updateAttachmentsButton, this);
		
	
	if(GO.mailings)
	{
		this.templatesStore = new GO.data.JsonStore({
				url: GO.settings.modules.mailings.url+'json.php',
				baseParams: {'task': 'authorized_templates'},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','name'],
				remoteSort:true
			});
		
		this.templatesList = new GO.email.TemplatesList({store: this.templatesStore});	
		
		this.templatesList.on('click', function(dataview, index){
			
			this.showConfig.template_id= index>0 ? dataview.store.data.items[index-1].id : 0;
			this.show(this.showConfig);
			this.templatesWindow.hide();
			this.templatesList.clearSelections();
							
		}, this);
		
		this.templatesWindow = new Ext.Window({
				title: GO.email.lang.selectTemplate,
				layout:'fit',
				modal:false,
				height:400,			
				width:600,
				closable:true,
				closeAction:'hide',	
				items: new Ext.Panel({
					autoScroll:true,
					items:this.templatesList,
					cls: 'go-form-panel'
				})
			});
	}
	
	var tbar = [
	    {
				text: GO.email.lang.send,
	      iconCls: 'btn-send',
				handler: this.sendMail,
				scope:this
	    },
			{
        text: GO.email.lang.extraOptions,
        iconCls: 'btn-settings',
        menu: this.optionsMenu  // assign menu by instance
      },
			this.showMenuButton = new Ext.Button({
        text:GO.email.lang.show,
        iconCls: 'btn-show',
        menu: this.showMenu  // assign menu by instance
      }),
      this.attachmentsButton = new Ext.Button({
				text: GO.email.lang.attachments,
				iconCls: 'btn-attach',
				handler: this.showAttachmentsDialog,
				scope:this
      })
		];
		
	if(GO.addressbook)
	{
		tbar.push(
      {
      	text: GO.addressbook.lang.addressbook,
      	iconCls: 'btn-addressbook',
      	handler:function(){
      		if(!this.addressbookDialog)
      		{
      			this.addressbookDialog = new GO.email.AddressbookDialog();
      			this.addressbookDialog.on('addrecipients', function(fieldName, selections){
      				var field = this.formPanel.form.findField(fieldName);
      				
      				var emails = [];
      				
      				for(var i=0;i<selections.length;i++)
      				{
      					emails.push('"'+selections[i].data.name+'" <'+selections[i].data.email+'>');
      				}
      				
      				var currentVal = field.getValue();
      				if(currentVal!='')
      					currentVal+=',';
      				
      				currentVal += emails.join(',');
      				
      				field.setValue(currentVal);
      				
      				if(fieldName=='cc')
      				{
      					this.ccFieldCheck.setChecked(true);
      				}else if(fieldName=='bcc')
      				{
      					this.bccFieldCheck.setChecked(true);
      				}
      				
      			}, this);
      		}
      		
      		this.addressbookDialog.show();
      	},
      	scope: this      	
      });
	}
	
	var focusFn = function(){this.toCombo.focus();};
	
	GO.email.EmailComposer.superclass.constructor.call(this, {
  	title: GO.email.lang.composeEmail,
    width: 700,
    height:500,
    minWidth: 300,
    minHeight: 200,
    layout: 'fit',
		maximizable:true,
    plain:true,
    closeAction:'hide',
    bodyStyle:'padding:5px;',
    buttonAlign:'center',
		focus: focusFn.createDelegate(this),
		tbar:tbar,
		items: this.formPanel	
    });

		
    
    this.addEvents({'send' : true});
};


Ext.extend(GO.email.EmailComposer, Ext.Window, {
	
	afterRender: function(){
		GO.email.EmailComposer.superclass.afterRender.call(this);	
		
		this.on('resize', this.setEditorHeight, this);
	},
	
	toComboVisible : true,
	
	updateAttachmentsButton : function(){
		
		var text = this.attachmentsStore.getCount()>0 ? GO.email.lang.attachments+' ('+this.attachmentsStore.getCount()+')' : GO.email.lang.attachments;
		
		this.attachmentsButton.setText(text);
	},
	
	reset : function(){
		
		this.sendParams={'task' : 'sendmail', notification: 'false', priority: '3', inline_attachments: {}};
		this.inline_attachments=Array();
		this.formPanel.form.reset();
		
		if(this.defaultAcccountId)
		{
			this.fromCombo.setValue(this.defaultAcccountId);
		}
		
		this.notifyCheck.setChecked(false);
		this.normalPriorityCheck.setChecked(true);
		
		if(this.attachmentsGrid){
			this.attachmentsGrid.store.loadData({results:[]});
		}
	},

	show : function(config){		
	    
	  
		if(!this.rendered)
		{
			
			
			this.fromCombo.store.on('load', function(){
	  			var records = this.fromCombo.store.getRange(); 
	  			if(records.length)
	  			{
		    		if(!config.account_id)
		    		{
		    			config.account_id = records[0].data.id;
		    		}
		    		
		    		this.render(Ext.getBody());
		    		
		    		this.show(config);
		    		Ext.getBody().unmask();
	  			}else
	  			{
	  				Ext.getBody().unmask();
	  				Ext.Msg.alert(GO.email.lang.noAccountTitle, GO.email.lang.noAccount);
	  			}
	    	}, this, {single: true});
	    	
	    Ext.getBody().mask(GO.lang.waitMsgLoad);
	    
			if(!GO.mailings)
			{
				config.template_id=0;
				this.fromCombo.store.load();
			}else
			{    	
				
				this.templatesStore.load({					
					callback: function(){						
						this.fromCombo.store.load();							
					},
					scope:this
				});
			}			
			
		}else if(config.template_id == undefined && this.templatesStore && this.templatesStore.getTotalCount()>1)
    {
    	this.showConfig=config;
    	this.templatesWindow.show();
    }else
    {
    	
    	
    	
    	
			this.toComboVisible=true;
			this.showMenuButton.setDisabled(false);
			this.toCombo.getEl().up('.x-form-item').setDisplayed(true);
			this.sendURL = GO.settings.modules.email.url+'action.php';
			
    	
    	if(config.template_id == undefined  && this.templatesStore && this.templatesStore.getTotalCount()==1)
    	{
    		config.template_id=this.templatesStore.data.items[0].get('id');
    	}
				
			this.attachmentsStore.removeAll();
			this.inline_attachments=[];	
			this.reset();
			if(config.account_id)
			{
				this.fromCombo.setValue(config.account_id);
			}else
			{
				this.fromCombo.setValue(this.fromCombo.store.data.items[0].id);
			}

	 		if(config.values)
			{
				this.formPanel.form.setValues(config.values);
			}
			
			GO.email.EmailComposer.superclass.show.call(this);

			if(config.uid || config.template_id || config.loadUrl)
			{
				if(!config.task)
				{
					config.task='template';
				}
				
				var params = config.loadParams ? config.loadParams : {
					uid : config.uid,
					account_id : this.fromCombo.getValue(),
					task : config.task,
					mailbox : config.mailbox,
					template_id : config.template_id			
				};
				
				if(config.template_id>0)
				{
					params.to=this.toCombo.getValue();
				}
				
				var url = config.loadUrl ?  config.loadUrl : GO.settings.modules.email.url+'json.php';
				
				
				
				//for mailings plugin				
				if(config.mailing_group_id>0)
				{
					this.sendURL = GO.settings.modules.mailings.url+'action.php';
					
					this.toComboVisible=false;
					this.showMenuButton.setDisabled(true);
					this.toCombo.getEl().up('.x-form-item').setDisplayed(false);
					this.ccCombo.getEl().up('.x-form-item').setDisplayed(false);
		 			this.bccCombo.getEl().up('.x-form-item').setDisplayed(false);
		 		
					
					this.sendParams.mailing_group_id=config.mailing_group_id;
					
					//so that template loading won't replace fields
					params.mailing_group_id=config.mailing_group_id;
				}
				
				
				
				
				
				this.formPanel.form.load({
					url: url, 
					params: params,
					waitMsg:GO.lang.waitMsgLoad,
					success: function(form, action) {		   
						
						this.sendParams['reply_uid']=config.uid;
						this.sendParams['reply_mailbox']=config.mailbox;
						     
						if(action.result.data.inline_attachments)
						{		
							this.inline_attachments = action.result.data.inline_attachments;
						}
						if(action.result.data.attachments)
						{
							this.attachmentsStore.loadData({results: action.result.data.attachments}, true);
						}
						
						if(action.result.replace_personal_fields)
						{
							this.sendParams['replace_personal_fields']='1';
						}
						
						if(action.result.data.cc)
						{
							this.ccFieldCheck.setChecked(true);
						}else
						{
							this.ccFieldCheck.setChecked(false);
						}
						
						if(this.toCombo.getValue()=='')
						{
							this.toCombo.focus();
						}else
						{
							this.htmlEditor.focus();
						}
			    },
			    scope: this
				});
				
			}
	
	
	    
	      
	     //somehow on render fails???
	    if(!this.showed)
	    {
	    	this.showed=true;
				this.ccCombo.getEl().up('.x-form-item').setDisplayed(false);
		 		this.bccCombo.getEl().up('.x-form-item').setDisplayed(false);
	    }
	 		    
	 		//this.setEditorHeight();

			}
   		
    }, 
    
    showAttachmentsDialog : function(){
    	
    	if(!this.attachmentsDialog)
    	{
    		
    		
    		var tbar = [];
    		
    		tbar.push({
				        	id: 'add-local', 
				        	iconCls: 'btn-add',
				        	text: GO.email.lang.attachFilesPC, 
				        	cls: 'x-btn-text-icon', 
				        	handler: function(){
				        		
				        		this.uploadDialog.show();
				        	}, 
				        	scope: this
				        });
				        
				if(GO.files)
				{
					tbar.push({
				        	id: 'add-remote', 
				        	iconCls: 'btn-add',
				        	text: GO.email.lang.attachFilesGO, 
				        	cls: 'x-btn-text-icon', 
				        	handler: function(){
				        		if(!this.fileBrowser)
				        		{
				        			this.fileBrowser = new GO.files.FileBrowser({
				        				border:false,
				        				fileClickHandler: this.addRemoteFiles,
				        				scope: this
				        			});
				        			
				        			this.fileBrowserWindow = new Ext.Window({
				        				
				        				title: GO.lang.strSelectFiles,
				        				height:400,
				        				width:600,
				        				layout:'fit',
				        				border:false,
				        				closeAction:'hide',
				        				items: this.fileBrowser,
				        				buttons:[
				        					{
				        						text: GO.lang.cmdOk,				        						
				        						handler: this.addRemoteFiles, 
				        						scope: this 
				        					},{
				        						text: GO.lang.cmdClose,				        						
				        						handler: function(){
				        							this.fileBrowserWindow.hide();
				        						},
				        						scope:this
				        					}
				        					
				        				]
				        								        				
				        			});
				        		}
				        		
				        		this.fileBrowserWindow.show();
				        		
				        	}, 
				        	scope: this
				        });
				}
				
				tbar.push({
				    		id: 'delete', 
				    		iconCls: 'btn-delete',
				    		text: GO.lang.cmdDelete, 
				    		cls: 'x-btn-text-icon', 
				    		handler: function(){
				    			
				    			var rows = this.attachmentsGrid.selModel.getSelections();
								for(var i=0;i<rows.length;i++)
									this.attachmentsStore.remove(rows[i]);
				    			
				    		},  
				    		scope: this
				    	});
  			
    		
    		
    		this.attachmentsGrid = new Ext.grid.GridPanel({
			    	id: 'groups-grid-overview-users',
			    	store: this.attachmentsStore,
			        columns:[
			        	{id:'name',header: GO.lang.strName, dataIndex: 'name'},
			        	{id:'size',header: GO.lang.strSize, dataIndex: 'size'},
			        	{id:'type',header: GO.lang.strType, dataIndex: 'type'}
			        	],
			        
			        sm: new Ext.grid.RowSelectionModel({singleSelect: false}),
			        view: new Ext.grid.GridView({
								forceFit: true,
								autoFill: true
							}),
			        tbar: tbar        
			    });
    		

    		this.attachmentsDialog = new Ext.Window({
					title: GO.email.lang.attachments,
					layout:'fit',
					modal:false,
					closeAction:'hide',
					minWidth:300,
					minHeight:300,
					height:400,
					width:600,		
					items: this.attachmentsGrid,
					buttons:[
						{
							text:GO.lang.cmdClose,
							handler: function(){this.attachmentsDialog.hide()}, 
							scope: this
						}]
				});
				
				
			var uploadFile = new GO.form.UploadFile({
	    			inputName : 'attachments',
	    			addText: GO.lang.smallUpload
	    		});

    		this.upForm = new Ext.form.FormPanel({
    			fileUpload:true,
    			items: [uploadFile,
    				 new Ext.Button({
	    				text:GO.lang.largeUpload,
	    				handler: function(){
	    					if(!deployJava.isWebStartInstalled('1.6.0'))
								{		
	    						Ext.MessageBox.alert(GO.lang.strError, GO.lang.noJava);
								}else
								{    					
		    					var local_path = this.local_path ? 'true' : false;
		    					
		    					GO.util.popup(GO.settings.modules.email.url+'jupload/index.php', '640','500');
		    					this.uploadDialog.hide();
		    					//for refreshing by popup
		    					GO.attachmentsStore = this.attachmentsStore;
								}
	    				},
	    				scope:this
	    			})],
    			cls: 'go-form-panel'
    		});
				
			this.uploadDialog = new Ext.Window({
					title: GO.email.lang.uploadAttachments,
					layout:'fit',					
					modal:false,
					height:300,
					width:300,		
					items: this.upForm,
					buttons:[
						{
							text:GO.email.lang.startTransfer,
							handler: function(){		
								
								this.upForm.container.mask(GO.lang.waitMsgUpload,'x-mask-loading');
														
								this.upForm.form.submit({
									url:GO.settings.modules.email.url+'action.php',
									params: {task: 'attach_file'},
									success:function(form, action){
										
										this.attachmentsStore.loadData({'results' : action.result.files}, true);
										this.upForm.container.unmask();
										uploadFile.clearQueue();
										
										this.uploadDialog.hide();
										
									},
									scope: this
								});
							}, 
							scope: this
						},
						{
							text:GO.lang.cmdClose,
							handler: function(){this.uploadDialog.hide()}, 
							scope: this
						}]
				});
    	}
    	this.attachmentsDialog.show();
    },
    
    addRemoteFiles : function(){
				        							
			var AttachmentRecord = Ext.data.Record.create([
			    {name: 'tmp_name'},
			    {name: 'name'},
			    {name: 'type'},
			    {name: 'size'}
			]);
			
			var selections = this.fileBrowser.gridPanel.selModel.selections.items;
			
			for(var i=0;i<selections.length;i++)
			{
				var newRecord = new AttachmentRecord({
						id:  selections[i].data.path,
				    tmp_name: selections[i].data.path,
				    name: selections[i].data.name,
				    type: selections[i].data.type,
				    size: selections[i].data.size
				});
				newRecord.id=selections[i].data.path;
				this.attachmentsStore.add(newRecord);
			}
			this.updateAttachmentsButton();
			this.fileBrowserWindow.hide();
				
		},
    
    sendMail : function(){
    	
    	if(this.attachmentsStore && this.attachmentsStore.data.keys.length)
    	{
    		this.sendParams['attachments']=Ext.encode(this.attachmentsStore.data.keys);    		
    	}
    	
    	this.sendParams['inline_attachments']=Ext.encode(this.inline_attachments);
    	
		this.formPanel.form.submit({
			
			
			
			url:this.sendURL,
			params: this.sendParams,
			waitMsg:GO.lang.waitMsgSave,
			success:function(form, action){
				
				if(action.result.account_id)
				{
					this.account_id=action.result.account_id;
				}
				
				if(this.callback)
				{
					if(!this.scope)
					{
						this.scope=this;
					}
					
					var callback = this.callback.createDelegate(this.scope);
					callback.call();
				}
				//this.reset();
				
				if(GO.addressbook && action.result.unknown_recipients && action.result.unknown_recipients.length)
				{					
					if(!GO.email.unknownRecipientsDialog)
						GO.email.unknownRecipientsDialog = new GO.email.UnknownRecipientsDialog();					
					
					GO.email.unknownRecipientsDialog.store.loadData({recipients: action.result.unknown_recipients});
					
					GO.email.unknownRecipientsDialog.show();					
				}
				
				this.fireEvent('send', this);
				this.hide();					
			},
	
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang.strError, action.result.feedback);
			},
			scope:this
			
		});
	},
		
	
    

    onShowFieldCheck : function(check, checked)
    {
    	switch(check.id)
    	{
    		case 'fromFieldCheck':
    			this.fromCombo.getEl().up('.x-form-item').setDisplayed(checked);
    		break;
    		
    		case 'ccFieldCheck':
    			this.ccCombo.getEl().up('.x-form-item').setDisplayed(checked);
    		break;
    		
    		case 'bccFieldCheck':
    			this.bccCombo.getEl().up('.x-form-item').setDisplayed(checked);
    		break;
    	}
    	//this.doLayout();
    	this.setEditorHeight();
    },
    
    setEditorHeight : function()
    {
    	var height=this.subjectField.el.getHeight()+this.selectLinkField.el.getHeight();
    	//this.tbar.getHeight()
    	if(this.toComboVisible)
    	{
    		height+=this.toCombo.el.getHeight()+3;
    	}
    	
    	for(var i=0;i<this.showMenu.items.items.length;i++)
    	{
    		if(this.showMenu.items.items[i].checked)
    		{
    			switch(this.showMenu.items.items[i].id)
    			{
    				case 'fromFieldCheck':
    					height+=this.fromCombo.el.getHeight()+3;
    				break;
    				
    				default:
    					height+=this.toCombo.el.getHeight()+3;
    				break;
    			}
    		}
    	}

    	var innerHeight = this.getInnerHeight();
    	var newHeight = innerHeight-height;

    	this.htmlEditor.setHeight(newHeight);
    	this.htmlEditor.setWidth(this.getInnerWidth()-10);
    	this.htmlEditor.syncSize();    
    }
});



GO.email.TemplatesList = function(config){
	
	Ext.apply(config);
	var tpl = new Ext.XTemplate( 
		'<div id="template-0" class="go-item-wrap">No template</div>',
		'<tpl for=".">',
		'<div id="template-{id}" class="go-item-wrap">{name}</div>',
		'</tpl>'
	);
	
	GO.email.TemplatesList.superclass.constructor.call(this, {
		store: config.store,
    tpl: tpl,
    singleSelect:true,
    autoHeight:true,
    overClass:'go-view-over',
    itemSelector:'div.go-item-wrap',
    selectedClass: 'go-view-selected'
	});	
}

Ext.extend(GO.email.TemplatesList,Ext.DataView, {
   onRender : function(ct, position){
			this.el = ct.createChild({
  	  	tag: 'div', 
       	cls:'go-select-list'
      });
      
      GO.email.TemplatesList.superclass.onRender.apply(this, arguments);
   }

});
