/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: AccountDialog.js 2938 2008-09-01 20:39:51Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.email.AccountDialog = function(config){
	Ext.apply(this, config);
	
	
	var typeField;
	var sslCb;
	
	
	function formatBoolean(value){
        return value=="1" ? GO.lang.cmdYes : GO.lang.cmdNo;  
    };

    // the column model has information about grid columns
    // dataIndex maps the column to the specific data field in
    // the data store (created below)
    var cm = new Ext.grid.ColumnModel([{
           header: GO.email.lang.field,
           dataIndex: 'field'				          	           
        },{
           header: GO.email.lang.contains,
           dataIndex: 'keyword'
        },{
           header: GO.email.lang.moveToFolder,
           dataIndex: 'folder'
        },{
           header: GO.email.lang.markAsRead,
           dataIndex: 'mark_as_read',
           renderer: formatBoolean
        }]);

    // by default columns are sortable
    cm.defaultSortable = false;

    // create the Data Store
    this.filtersDS = new GO.data.JsonStore({

		url: GO.settings.modules.email.url+'json.php',							
		baseParams: {
			type: 'filters',
			account_id: this.account_id
		},
		root: 'results',
		id: 'id',
		fields: ['id', 'field','keyword','folder','mark_as_read'],
		remoteSort: false
	});

  // create the editor grid
  this.filtersTab = new GO.grid.GridPanel({
  	title: GO.email.lang.filters,
		layout:'fit',
		border:true,
    ds: this.filtersDS,
    cm: cm,
    view: new Ext.grid.GridView({autoFill:true, forceFit:true}),
    sm: new Ext.grid.RowSelectionModel(),
    tbar: [{
			iconCls: 'btn-add',
			text: GO.lang.cmdAdd,
			cls: 'x-btn-text-icon',
			handler: function(){
				filter.showDialog(0, this.account_id, this.filtersDS);	
			},
			scope: this
		},{				
			iconCls: 'btn-delete',
			text: GO.lang.cmdDelete,
			cls: 'x-btn-text-icon',
			handler: function(){
				this.filtersTab.deleteSelected();
			},
			scope: this
		}]

  });

	this.filtersTab.on('rowdblclick', function(){
		var selectionModel = this.filtersTab.getSelectionModel();
		var record = selectionModel.getSelected();
			filter.showDialog(record.data.id, this.account_id, this.filtersDS);
		}, this);
	
	this.filtersTab.on('show', function(){
		// trigger the data store load
		
		if(this.filtersDS.baseParams['account_id']!=this.account_id)
		{
			this.filtersDS.baseParams={
					task: 'filters', account_id: this.account_id
				};
		    this.filtersDS.load();
		}
	}, this);
	

	var incomingTab = {
        title: GO.email.lang.incomingMail,
        layout:'form',
        defaults: {anchor: '100%'},
        defaultType: 'textfield',
        autoHeight:true,
        cls:'go-form-panel',waitMsgTarget:true,
				labelWidth: 120,
        items: [
        	typeField = new Ext.form.ComboBox({
               	fieldLabel: GO.email.lang.type,
                hiddenName:'type',
                store: new Ext.data.SimpleStore({
                    fields: ['value', 'text'],
                    data : [
                    	['imap', 'IMAP'],
                    	['pop3', 'POP-3']
                    ]
                    
                }),
                value:'imap',
                valueField:'value',
                displayField:'text',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                selectOnFocus:true,
                forceSelection: true
            }),
            new Ext.form.TextField({		                	
                fieldLabel: GO.email.lang.host,
                name: 'host',
                allowBlank:false			   
            }),
            new Ext.form.TextField({		                	
                fieldLabel: GO.lang.strUsername,
                name: 'username',
                allowBlank:false			   
            }),
            new Ext.form.TextField({		                	
                fieldLabel: GO.lang.strPassword,
                name: 'password',
                inputType:'password',
                allowBlank:false			   
            }),{
	            xtype:'fieldset',
	            title: GO.email.lang.advanced,
	            collapsible: true,
	            collapsed:true,
	            autoHeight:true,
	            autoWidth:true,
	            //defaults: {anchor: '100%'},
	            defaultType: 'textfield',
	            labelWidth:75,
	            labelAlign: 'left',
	            

	            items:[
		             sslCb = new Ext.form.Checkbox({
	                    boxLabel: GO.email.lang.ssl,
	                    name: 'ssl',
	                    checked: false,
	                    hideLabel:true
	                }),new Ext.form.Checkbox({
	                    boxLabel: GO.email.lang.tls,
	                    name: 'tls',
	                    checked: false,
	                    hideLabel:true
	                }),new Ext.form.Checkbox({
	                    boxLabel: GO.email.lang.novalidateCert,
	                    name: 'novalidate_cert',
	                    checked: false,
	                    hideLabel:true
	                }),new Ext.form.TextField({		                	
		                fieldLabel: GO.email.lang.port,
		                name: 'port',
		                value: '143',
		                allowBlank:false			   
		            }),new Ext.form.TextField({		                	
		                fieldLabel: GO.email.lang.rootMailbox,
		                name: 'mbroot'		   
		            })
	              ]
            }        
        ]		
	};
	
	//end incomming tab
	
	var propertiesTab = {
        title:GO.lang.strProperties,
        layout:'form',
        anchor: '100% 100%',
        defaults: {anchor: '100%'},
        defaultType: 'textfield',
        autoHeight:true,
        cls:'go-form-panel',
        labelWidth: 100,        
        items: [
					this.selectUser = new GO.form.SelectUser({
						fieldLabel: GO.lang.strUser,
						disabled: !GO.settings.modules['email']['write_permission']
					}),				
					{
              fieldLabel: GO.lang.strName,
              name: 'name',
              allowBlank:false
 
          },
          {
              fieldLabel: GO.lang.strEmail,
              name: 'email',
              allowBlank:false
          }
		]	            
	};
	
	
	
	var outgoingTab = {
        title: GO.email.lang.outgoingMail,
        layout:'form',
        defaults: {anchor: '100%'},
        defaultType: 'textfield',
        autoHeight:true,
        cls:'go-form-panel',
				labelWidth: 120,
        items: [        	
            new Ext.form.TextField({		                	
                fieldLabel: GO.email.lang.host,
                name: 'smtp_host',
                allowBlank:false			   
            }),            
		        this.encryptionField = new Ext.form.ComboBox({
	             	fieldLabel: GO.email.lang.encryption,
                hiddenName:'smtp_encryption',
                store: new Ext.data.SimpleStore({
                    fields: ['value', 'text'],
                    data : [
                    	['8', GO.email.lang.noEncryption],
                    	['2', 'TLS'],
                    	['4', 'SSL']
                    ]
                    
                }),
                value:'8',
                valueField:'value',
                displayField:'text',
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                selectOnFocus:true,
                forceSelection: true
            }),
            new Ext.form.TextField({		                	
		                fieldLabel: GO.email.lang.port,
		                name: 'smtp_port',
		                value: '25',
		                allowBlank:false			   
		        }),
            new Ext.form.TextField({		                	
                fieldLabel: GO.lang.strUsername,
                name: 'smtp_username'		   
            }),
            new Ext.form.TextField({		                	
                fieldLabel: GO.lang.strPassword,
                name: 'smtp_password',
                inputType:'password'		   
            })]		
	};
	
	
	
	this.subscribedFoldersStore = new Ext.data.JsonStore({

		url: GO.settings.modules.email.url+'json.php',
		baseParams: {task:'subscribed_folders', account_id:0},
		root: 'data',
		fields: ['id','name']
	});
	
	
		
	
	this.foldersTab =new Ext.Panel({
		title: GO.email.lang.folders,
    autoHeight:true,       		
		layout:'form',
  	cls:'go-form-panel',
  	defaults: {anchor: '100%'},
  	defaultType: 'textfield',
  	labelWidth: 150,
  	tbar: [{
			iconCls: 'btn-add',
			text: GO.email.lang.manageFolders,
			cls: 'x-btn-text-icon',
			scope: this,
    		handler:function(){
    			
    			if(!this.foldersDialog)
    			{
    				this.foldersDialog = new GO.email.FoldersDialog();
    			}
    			this.foldersDialog.show(this.account_id);
    			
    		}
  		}],
 
    items:[new Ext.form.ComboBox({
           	fieldLabel: GO.email.lang.sendItemsFolder,
            hiddenName:'sent',
            store: this.subscribedFoldersStore,
            valueField:'name',
            displayField:'name',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            editable: false,
            selectOnFocus:true,
            forceSelection: true
        }),
        new Ext.form.ComboBox({
           	fieldLabel: GO.email.lang.trashFolder,
            hiddenName:'trash',
            store: this.subscribedFoldersStore,
            valueField:'name',
            displayField:'name',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            editable: false,
            selectOnFocus:true,
            forceSelection: true
        }),
        new Ext.form.ComboBox({
           	fieldLabel: GO.email.lang.draftsFolder,
            hiddenName:'drafts',
            store: this.subscribedFoldersStore,
            valueField:'name',
            displayField:'name',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            editable: false,
            selectOnFocus:true,
            forceSelection: true
        })]
   	
			
	});
	
	
	this.vacationPanel = new Ext.Panel({
			disabled:true,
			title:GO.email.lang.vacation,			
			cls:'go-form-panel',			
			layout:'form',
			autoScroll:true,
			items:[
			{
				xtype: 'checkbox',
			  name: 'vacation_active',
				anchor: '-20',
			  boxLabel: GO.email.lang.vacationActive,
			  hideLabel: true
			  
			},{
				xtype: 'textfield',
			  name: 'vacation_subject',
				anchor: '-20',
			  fieldLabel: GO.email.lang.vacationSubject
			},{
				xtype: 'textarea',
			  name: 'vacation_body',
				anchor: '-20',
			  fieldLabel: GO.email.lang.vacationBody
			}]
				
		});
	

	

	
	this.propertiesPanel = new Ext.form.FormPanel({
			url: GO.settings.modules.email.url+'action.php',
			//labelWidth: 75, // label settings here cascade unless overridden
			defaultType: 'textfield',
			waitMsgTarget:true,
			labelWidth:120,
			border:false,
			items:[this.tabPanel = new Ext.TabPanel({
				hideLabel:true,
				deferredRender:false,	
      	activeTab: 0,
      	border:false,
      	anchor: '100% 100%',
      	items:[
      	propertiesTab,
      	incomingTab,
      	outgoingTab,
      	this.foldersTab,
      	this.filtersTab,
      	this.vacationPanel
				]
       })]
	

		});
		
	typeField.on('select', function(combo, record, index){
		
		var value = index==1 ? '110' : '143';
		
		this.propertiesPanel.form.findField('port').setValue(value);		
	},
	this);
	
	this.encryptionField.on('select', function(combo, record, index){		
		var value = record.data.value==8 ? '25' : '465';		
		this.propertiesPanel.form.findField('smtp_port').setValue(value);		
	},
	this);
	
	sslCb.on('check', function(checkbox, checked){
		if(typeField.getValue()=='imap')
		{
			this.propertiesPanel.form.findField('port').setValue(993);
		}else
		{
			this.propertiesPanel.form.findField('port').setValue(995);
		}
	},this)
	
	
	this.selectUser.on('select', function(combo, record, index){
		this.propertiesPanel.form.findField('email').setValue(record.data.email);
		this.propertiesPanel.form.findField('username').setValue(record.data.username);
		this.propertiesPanel.form.findField('name').setValue(record.data.name);
	},
	this);

	
	
	GO.email.AccountDialog.superclass.constructor.call(this, {
    layout: 'fit',
		modal:false,
		height:400,
		width:650,
		plain:true,
		closeAction:'hide',
		title:GO.email.lang.account,
		
		items: this.propertiesPanel,
		
		
		buttons: [
			{
			
				text: GO.lang.cmdOk,
				handler: function(){							
					this.save(true);
				},
				scope:this
			},
			{
			
				text: GO.lang.cmdApply,
				handler: function(){			
					this.save(false);						
				},
				scope:this
			},
			{
			
				text: GO.lang.cmdClose,
				handler: function(){this.hide();},
				scope: this
			}
		]
    });

	this.addEvents({'save' : true});
	
}

Ext.extend(GO.email.AccountDialog, Ext.Window, {
	
	
	save : function(hide)
	{
		this.propertiesPanel.form.submit({
						
			url:GO.settings.modules.email.url+'action.php',
			params: {'task' : 'save_account_properties', 'account_id': this.account_id},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				if(action.result.account_id)
				{
					this.account_id=action.result.account_id;
					//this.foldersTab.setDisabled(false);
					this.loadAccount(this.account_id);
				}
				
				this.fireEvent('save', this);
				
				if(hide)
				{
					this.hide();
				}
				
				
			},
	
			failure: function(form, action) {
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang.strErrorsInForm;
				}else
				{
					error = action.result.feedback;
				}
				
				Ext.MessageBox.alert(GO.lang.strError, error);
			},
			scope:this
			
		});
		
	},
	show : function(account_id){
		GO.email.AccountDialog.superclass.show.call(this);
		
		this.tabPanel.setActiveTab(0);
		
		if(account_id)
		{
			this.loadAccount(account_id);
			this.subscribedFoldersStore.baseParams.account_id=account_id;
			this.subscribedFoldersStore.load();
		}else
		{
			this.propertiesPanel.form.reset();
			this.account_id=0;
			this.foldersTab.setDisabled(true);
			this.filtersTab.setDisabled(true);
			this.vacationPanel.setDisabled(true);
			//default values
			
			//this.selectUser.setValue(GO.settings['user_id']);
			//this.selectUser.setRawValue(GO.settings['name']);
		//	this.selectUser.lastSelectionText=GO.settings['name'];
			
			this.propertiesPanel.form.findField('name').setValue(GO.settings['name']);
			this.propertiesPanel.form.findField('email').setValue(GO.settings['email']);
			this.propertiesPanel.form.findField('username').setValue(GO.settings['username']);
			
 		}
	},
	
	loadAccount : function(account_id)
	{
		this.propertiesPanel.form.load({
			url: GO.settings.modules.email.url+'json.php', 
			params: {account_id:account_id, task: 'account'},
			waitMsg:GO.lang.waitMsgLoad,
			success: function(form, action) {
		    this.account_id=account_id;
				this.selectUser.setRemoteValue(action.result.data.user_id, action.result.data.user_name);
				
				this.foldersTab.setDisabled(false);
				this.filtersTab.setDisabled(false);
				this.vacationPanel.setDisabled(typeof(action.result.data.vacation_subject)=='undefined');
		    },
		    scope: this
		});
	}
});



filter = function(){


	return {

		showDialog : function(filter_id, account_id, ds){
		
			
			
			
			
			if(!this.win)
			{
				
				var subscribedFoldersStore = new Ext.data.JsonStore({

					url: GO.settings.modules.email.url+'json.php',
					baseParams: {task:'subscribed_folders', hideInbox: 'true',  account_id:account_id},
					root: 'data',
					fields: ['id','name']
				});
				
				
				this.formPanel = new Ext.form.FormPanel({
			        layout:'form',
			        defaults: {anchor: '100%'},
			        defaultType: 'textfield',
			        labelWidth:125,
			        autoHeight:true,
			        cls:'go-form-panel',waitMsgTarget:true,
			        items: [
			        	new Ext.form.ComboBox({
			               	fieldLabel: GO.email.lang.field,
			                hiddenName:'field',
			                store: new Ext.data.SimpleStore({
			                    fields: ['value', 'text'],
			                    data : [
			                    	['sender', GO.email.lang.sender],
			                    	['subject', GO.email.lang.subject],
			                    	['to', GO.email.lang.sendTo],
			                    	['cc', GO.email.lang.ccField]
			                    ]
			                    
			                }),
			                value:'sender',
			                valueField:'value',
			                displayField:'text',
			                typeAhead: true,
			                mode: 'local',
			                triggerAction: 'all',
			                editable: false,
			                selectOnFocus:true,
			                forceSelection: true
			            }),
			        	{
			                fieldLabel: GO.email.lang.keyword,
			                name: 'keyword',
			                allowBlank:false
			   
			            },
						new Ext.form.ComboBox({
			               	fieldLabel: GO.email.lang.moveToFolder,
			                hiddenName:'folder',
			                store: subscribedFoldersStore,
			                valueField:'name',
			                displayField:'name',
			                typeAhead: true,
			                mode: 'remote',
			                triggerAction: 'all',
			                editable: false,
			                selectOnFocus:true,
			                forceSelection: true,
			                allowBlank: false
			            }),	
			            new Ext.form.Checkbox({
		                    boxLabel: GO.email.lang.markAsRead,
		                    name: 'mark_as_read',
		                    checked: false,
		                    hideLabel:true
		                })						
					]	
					}       
				
				);
				
				
				this.win = new Ext.Window({
					title: GO.email.lang.filter,
					layout: 'fit',
					modal:false,
					shadow:false,
					autoHeight:true,
					width:400,
					plain:false,
					closeAction:'hide',
					items:this.formPanel,
					buttons:[
					{
						id: 'ok',
						text: GO.lang.cmdOk,
						handler: function(){
			
							this.formPanel.form.submit({								
								url:GO.settings.modules.email.url+'action.php',
								params: {
									'task' : 'save_filter', 
									'filter_id': this.filter_id,
									'account_id': account_id
									},
								waitMsg:GO.lang.waitMsgSave,
								success:function(form, action){
								
									if(action.result.filter_id)
									{
										this.filter_id=action.result.filter_id;
									}			
									ds.reload();	
													
									this.win.hide();
																		
								},						
								failure: function(form, action) {
									var error = '';
									if(action.failureType=='client')
									{
										error = GO.lang.strErrorsInForm;
									}else
									{
										error = action.result.feedback;
									}
									
									Ext.MessageBox.alert(GO.lang.strError, error);
								},
								scope:this								
							});														
						},
						scope:this
					},					
					{
						id: 'close',
						text: GO.lang.cmdClose,
						handler: function(){this.win.hide();},
						scope: this
					}					
					]
				});
			}
						
			
			if(this.filter_id!=filter_id)
			{
				this.filter_id=filter_id;
				
				if(this.filter_id>0)
				{
					this.formPanel.load( {
						url: GO.settings.modules.email.url+'json.php',
						params: {
							filter_id: filter_id,
							task: 'filter'
						}	
					});
				}else
				{
					this.formPanel.form.reset();
				}
			}	
			
			this.win.show();
		}			
	}
}();
