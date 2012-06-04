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
 
GO.servermanager.InstallationDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=750;
	config.height=550;
	config.closeAction='hide';
	config.title= GO.servermanager.lang.installation;					
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm(true);
			},
			scope: this
		},{
			text: GO.lang['cmdApply'],
			handler: function(){
				this.submitForm();
			},
			scope:this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}					
	];
	
	GO.servermanager.InstallationDialog.superclass.constructor.call(this, config);
	this.addEvents({'save' : true});	
}
Ext.extend(GO.servermanager.InstallationDialog, GO.Window,{
	
	show : function (installation_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		this.tabPanel.setActiveTab(0);
		
		
		
		if(!installation_id)
		{
			installation_id=0;			
		}
			
		this.setInstallationId(installation_id);
		
		if(this.installation_id>0)
		{
			this.formPanel.load({
				url : GO.url("servermanager/installation/load"),
				waitMsg:GO.lang.waitMsgLoad,
				success:function(form, action)
				{					
					GO.servermanager.InstallationDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else 
		{
			
			this.formPanel.form.reset();
			
			GO.servermanager.InstallationDialog.superclass.show.call(this);
		}
		
		
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(config && config.link_config)
		{
			this.link_config=config.link_config;
			if(config.link_config.type_id)
			{
				
			}
		}else
		{
			delete this.link_config;
		}
		
	},
	
	setInstallationId : function(installation_id)
	{
		this.formPanel.form.baseParams['id']=installation_id;
		this.installation_id=installation_id;

		if(this.modulesGrid.store.baseParams.installation_id!=installation_id){
			this.modulesGrid.store.baseParams.installation_id=installation_id;
			this.modulesGrid.store.removeAll();
			this.modulesGrid.store.loaded=false;
		}
		
		this.formPanel.form.findField('admin_password1').allowBlank=installation_id>0;
		this.formPanel.form.findField('admin_password2').allowBlank=installation_id>0;

		
		this.formPanel.form.findField('enabled').setDisabled(installation_id==0);
		
		this.formPanel.form.findField('name').setDisabled(installation_id>0);
	//	this.configPanel.setDisabled(installation_id==0);
		//this.linksPanel.loadLinks(installation_id, 13);

	},
	
	submitForm : function(hide){

		var params =  {};
		if(this.modulesGrid.store.loaded)
		{
			params.modules=Ext.encode(this.modulesGrid.getSelected());
		}

		this.formPanel.form.submit(
		{
			url:GO.url("servermanager/installation/submit"),
			params:params,
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);

				if(this.modulesGrid.loaded)
				{
					this.modulesGrid.store.commitChanges();
				}
				
				if(hide)
				{
					this.hide();	
				}else
				{				
					if(action.result.id)
					{
						this.setInstallationId(action.result.id);
					}
				}	
			},		
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
		
	},
	
	
	buildForm : function () {
		/*  dateformat */
		var dateFormatData = new Ext.data.SimpleStore({
			fields: ['id', 'date_format'],		
			data : [
			['dmY', GO.users.lang.dmy],
			['mdY', GO.users.lang.mdy],
			['Ymd', GO.users.lang.jmd]
			]
		});
	
		/* dateseparator */
		var dateSeperatorData = new Ext.data.SimpleStore({
			fields: ['id', 'date_separator'],
			data : [
			['-', '-'],
			['.', '.'],
			['/', '/']
			]
		});
	
		/* timeformat */
		var 	timeFormatData = new Ext.data.SimpleStore({
			fields: ['id', 'time_format'],		
			data : [
			['G:i', GO.users.lang.fullhourformat],
			['g:i a', GO.users.lang.halfhourformat]
			]
		});
	
		/* timeformat */
		var 	firstWeekdayData = new Ext.data.SimpleStore({
			fields: ['id', 'first_weekday'],		
			data : [
			['0', GO.users.lang.sunday],
			['1', GO.users.lang.monday]
			]
		});
		
		
		
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],					
			layout:'column',
			autoScroll:true,
			items:[{
					cls:'go-form-panel',waitMsgTarget:true,
					layout:'form',
					labelWidth:140,
					border:false,
					columnWidth:.5,
					items:[{
					xtype: 'checkbox',
				  name: 'enabled',
					anchor: '-20',
				 	hideLabel:true,
				  boxLabel: GO.servermanager.lang.enabled,
				  checked:true,
					disabled:true
				},new Ext.form.ComboBox({
						fieldLabel: GO.servermanager.lang.status,
						hiddenName:'status',
						store: new Ext.data.SimpleStore({
								fields: ['value', 'text'],
								data : [
									['trial', '30 day trial'],
									['warntrial1', 'First warning, 20 days until deletion'],
									['warntrial2', 'Second warning, 10 days until deletion'],
									['ignore', 'Never remove installation']
								]

						}),
						value:'ignore',
						valueField:'value',
						displayField:'text',
						typeAhead: true,
						mode: 'local',
						triggerAction: 'all',
						editable: false,
						selectOnFocus:true,
						forceSelection: true,
						anchor: '-20'
				}),{
					xtype: 'textfield',
				  name: 'name',
					anchor: '-20',
				  allowBlank:false,
				  fieldLabel: GO.servermanager.lang.domain
				},{
					xtype: 'textfield',
				  name: 'admin_password1',
					anchor: '-20',
					inputType: 'password',
				  fieldLabel: GO.servermanager.lang.adminPassword
				},{
					xtype: 'textfield',
				  name: 'admin_password2',
					anchor: '-20',
					inputType: 'password',
				  fieldLabel: GO.servermanager.lang.confirmAdminPassword
				},{
					xtype: 'textfield',
				  name: 'webmaster_email',
					anchor: '-20',
				  allowBlank:false,
				  fieldLabel: GO.servermanager.lang.webmasterEmail,
				  value:GO.settings.email
				},{
					xtype: 'textfield',
				  name: 'title',
					anchor: '-20',
				  allowBlank:false,
				  fieldLabel: GO.servermanager.lang.title,
				  value: GO.settings.config.title
				},{
					xtype: 'combo',
				  name: 'theme',
					store:  new GO.data.JsonStore({
						url: GO.settings.modules.users.url+'non_admin_json.php',
						baseParams: {'task':'themes'},
						root: 'results',
						totalProperty: 'total',
						fields:['theme'],
						remoteSort: true

					}),
					displayField:'theme',
					valueField: 'theme',
					mode:'remote',
					triggerAction:'all',
					editable: false,
					selectOnFocus:true,
					forceSelection: true,
					anchor: '-20',
				  allowBlank:false,
				  fieldLabel: GO.servermanager.lang.theme,
				  value:GO.settings.config.theme
				},{
					xtype: 'checkbox',
				  name: 'allow_themes',
					anchor: '-20',
				 	hideLabel:true,
				  boxLabel: GO.servermanager.lang.allowThemes,
				  checked:false
				},{
					xtype: 'checkbox',
				  name: 'allow_password_change',
					anchor: '-20',
				  hideLabel:true,
				  boxLabel: GO.servermanager.lang.allowPasswordChange,
				  checked:true
				}/*,{
					xtype: 'checkbox',
				  name: 'allow_registration',
					anchor: '-20',
				  hideLabel:true,
				  boxLabel: GO.servermanager.lang.allowRegistration
				},{
					xtype: 'checkbox',
				  name: 'allow_duplicate_email',
					anchor: '-20',
				  hideLabel:true,
				  boxLabel: GO.servermanager.lang.allowDuplicateEmail
				},{
					xtype: 'checkbox',
				  name: 'auto_activate_accounts',
					anchor: '-20',
				  hideLabel:true,
				  boxLabel: GO.servermanager.lang.autoActivateAccounts
				},{
					xtype: 'checkbox',
				  name: 'notify_admin_of_registration',
					anchor: '-20',
				  hideLabel:true,
				  boxLabel: GO.servermanager.lang.notifyAdminOfRegistration,
				  checked:true
				}*/]
			},{
				cls:'go-form-panel',waitMsgTarget:true,
				layout:'form',
				columnWidth:.5,
				labelWidth:140,
				border:false,
				items:[new GO.form.SelectCountry({
						fieldLabel: GO.lang['strCountry'],
						hiddenName: 'default_country',
						anchor: '-20',
						value: GO.settings.country
					}),new Ext.form.ComboBox({
						anchor: '-20',
						fieldLabel: GO.users.lang['cmdFormLabelLanguage'],
						name: 'language',
						store:  new Ext.data.SimpleStore({
								fields: ['id', 'language'],
								data : GO.Languages
							}),
						displayField:'language',
						valueField: 'id',
						hiddenName:'language',
						mode:'local',
						triggerAction:'all',
						selectOnFocus:true,
						forceSelection: true,
						value: GO.settings.language
					}),new Ext.form.ComboBox({
						anchor: '-20',
						fieldLabel: GO.users.lang.cmdFormLabelTimezone,
						name: 'default_timezone',
						store: new Ext.data.SimpleStore({
								fields: ['timezone'],
								data : GO.users.TimeZones
							}),
						displayField: 'timezone',
						mode: 'local',
						triggerAction: 'all',
						selectOnFocus: true,
						forceSelection: true,
						value: GO.settings.timezone
					}),{
						anchor: '-20',
						xtype: 'textfield',
					  name: 'default_currency',						
					  allowBlank:false,
					  fieldLabel: GO.servermanager.lang.defaultCurrency,
					  value: GO.settings.currency
					},
					new Ext.form.ComboBox({
							anchor: '-20',
							fieldLabel: GO.users.lang['cmdFormLabelDateFormat'],
							name: 'date_format',
							store: dateFormatData,
							displayField: 'date_format',
							value: GO.settings.date_format.replace(new RegExp(GO.settings.date_separator, "g"), ""),
							valueField: 'id',
							hiddenName: 'default_date_format',
							mode: 'local',
							triggerAction: 'all',
							editable: false,
							selectOnFocus: true,
							forceSelection: true
						}),
					new Ext.form.ComboBox({
						anchor: '-20',
						fieldLabel: GO.users.lang['cmdFormLabelDateSeperator'],
						name: 'date_separator_name',
						store: dateSeperatorData,
						displayField: 'date_separator',			
						value: GO.settings.date_separator,
						valueField: 'id',
						hiddenName: 'default_date_separator',
						mode: 'local',
						triggerAction: 'all',
						editable: false,
						selectOnFocus: true,
						forceSelection: true
					}),
					new Ext.form.ComboBox({
						fieldLabel: GO.users.lang.timeFormat,
						name: 'time_format_name',
						store: timeFormatData,
						displayField: 'time_format',
						valueField: 'id',
						hiddenName: 'default_time_format',
						mode: 'local',
						triggerAction: 'all',
						editable: false,
						selectOnFocus: true,
						value: GO.settings.time_format,
						anchor: '-20',
						forceSelection: true						
					}),
						
					new Ext.form.ComboBox({
						fieldLabel: GO.users.lang['cmdFormLabelFirstWeekday'],
						store: firstWeekdayData,
						displayField: 'first_weekday',
						valueField: 'id',
						hiddenName: 'first_weekday',
						mode: 'local',
						triggerAction: 'all',
						editable: false,
						selectOnFocus: true,
						forceSelection: true,
						anchor: '-20',
						value: GO.settings.first_weekday
					}),
						
						{
						xtype: 'textfield',
					  name: 'default_decimal_separator',
						anchor: '-20',
					  allowBlank:false,
					  fieldLabel: GO.servermanager.lang.defaultDecimalSeperator,
					  value: GO.settings.decimal_separator
					},{
						xtype: 'textfield',
					  name: 'default_thousands_separator',
						anchor: '-20',
					  allowBlank:false,
					  fieldLabel: GO.servermanager.lang.defaultThousandsSeperator,
					  value: GO.settings.thousands_separator
					}]
			},{
				columnWidth:1,
				layout:'form',
				cls:'go-form-panel',waitMsgTarget:true,
				labelWidth:160,
				border:false,
				items:[{
						xtype: 'textfield',
					  name: 'restrict_smtp_hosts',
						anchor: '-20',					  
					  fieldLabel: GO.servermanager.lang.restrictSmtpHosts
					},{
						xtype: 'textfield',
					  name: 'serverclient_domains',
						anchor: '-20',					  
					  fieldLabel: GO.servermanager.lang.mailDomains
						},new GO.form.NumberField({
						decimals:0,
					  name: 'max_users',
						anchor: '-20',
					  allowBlank:false,
					  fieldLabel: GO.servermanager.lang.maxUsers
					}),new GO.form.NumberField({
					  name: 'quota',
						anchor: '-20',					  
					  fieldLabel: GO.servermanager.lang.quota,
					  value:1
					})]
			}]
				
		});


		//this.modulesGrid = new GO.servermanager.ModulesGrid();

		this.modulesGrid = new GO.grid.MultiSelectGrid({
			id:'sm-modules',
			title:GO.servermanager.lang.availableModules,
			loadMask:true,
			allowNoSelection:true,
			store:new GO.data.JsonStore({
				url: GO.url("servermanager/installation/modules"),
				baseParams: {
					installation_id:0
				},
				fields: ['id','name','installed','checked', "usercount"]
			}),
			showHeaders:true,
			noSingleSelect:true,
			extraColumns:[{
					header:'Users',
					dataIndex:'usercount',
					width:60
			}]
		});
		
		this.modulesGrid.on('show',function(){			
				this.modulesGrid.store.load();
		},this);

		var items  = [this.propertiesPanel, this.modulesGrid];
		
    
    
    
		
		//Create the standard GO linkspanel
		/*this.linksPanel = new GO.grid.LinksPanel({title: GO.lang['strLinks']});
		items.push(this.linksPanel);
		if(GO.customfields && GO.customfields.types["13"])
		{
			for(var i=0;i<GO.customfields.types["13"].panels.length;i++)
			{			  	
				items.push(GO.customfields.types["13"].panels[i]);
			}
		}*/
		
		
		/*this.configPanel = new Ext.Panel({
			title: GO.servermanager.lang.configEditor, 
			layout: 'fit',
			border:false,
			disabled:true,
			items: [ new Ext.form.TextArea({
				name: 'config',
				fieldLabel: '',
				hideLabel: true,
				anchor:'100% 100%'
			})
			]
		});
		items.push(this.configPanel);*/
		
 
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
    	border: false,
      items: items,
      anchor: '100% 100%'
    }) ;    
    
    
    this.formPanel = new Ext.form.FormPanel({
    	waitMsgTarget:true,
			url: GO.settings.modules.servermanager.url+'action.php',
			border: false,
			baseParams: {},				
			items:this.tabPanel				
		});
    
    
	}
});