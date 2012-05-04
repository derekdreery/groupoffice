/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

 
GO.addressbook.EmailTemplateDialog = function(config){
	
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
	config.resizable=true;
	config.width=760;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.addressbook.lang.emailTemplate;					
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
	}];

	
	GO.addressbook.EmailTemplateDialog.superclass.constructor.call(this, config);
	
	
	this.addEvents({
		'save' : true
	});
}

Ext.extend(GO.addressbook.EmailTemplateDialog, Ext.Window,{
//
//	inline_attachments : [],
	
	show : function (email_template_id) {
		
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}

		this.tabPanel.setActiveTab(0);

		if(!email_template_id)
		{
			email_template_id=0;			
		}
			
		this.setEmailTemplateId(email_template_id);
		
		if(this.email_template_id>0)
		{
			this.formPanel.load({
				url: GO.url('addressbook/template/load'),
				
				success:function(form, action)
				{
					this.readPermissionsTab.setAcl(action.result.data.acl_id);
										
					GO.addressbook.EmailTemplateDialog.superclass.show.call(this);
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
			this.htmlEditPanel.reset();
			this.readPermissionsTab.setAcl(0);

			GO.addressbook.EmailTemplateDialog.superclass.show.call(this);
		}
	},
	
	

	setEmailTemplateId : function(email_template_id)
	{
		this.formPanel.form.baseParams['id']=email_template_id;
		this.email_template_id=email_template_id;		
	},
	
	submitForm : function(hide){

		//won't toggle if not done twice...
//		this.htmlEditPanel.getHtmlEditor().toggleSourceEdit(false);
//		this.htmlEditPanel.getHtmlEditor().toggleSourceEdit(false);

		this.formPanel.form.submit(
		{
			url: GO.url('addressbook/template/submit'),
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				this.fireEvent('save', this);

				if(hide)
				{
					this.hide();	
				}else
				{
					if(action.result.id)
					{
						this.setEmailTemplateId(action.result.id);						
						this.readPermissionsTab.setAcl(action.result.acl_id);
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
		
//		var imageInsertPlugin = new GO.plugins.HtmlEditorImageInsert();
//		imageInsertPlugin.on('insert', function(plugin, path, url,temp,id) {
//
//
//			var ia = {
//				tmp_file : path,
//				url : url,
//				temp:temp
//			};
//
//		this.inline_attachments.push(ia);
//		}, this);
		
		var autodata = [			
		['{date}',GO.lang['strDate']],
		['{salutation}',GO.lang['strSalutation']],
		['{first_name}',GO.lang['strFirstName']],
		['{middle_name}',GO.lang['strMiddleName']],
		['{last_name}',GO.lang['strLastName']],
		['{initials}',GO.lang['strInitials']],
		['{title}',GO.lang['strTitle']],
		['{email}',GO.lang['strEmail']],
		['{home_phone}',GO.lang['strPhone']],
		['{fax}',GO.lang['strFax']],
		['{cellular}',GO.lang['strCellular']],
		['{address}',GO.lang['strAddress']],
		['{address_no}',GO.lang['strAddressNo']],
		['{zip}',GO.lang['strZip']],
		['{city}',GO.lang['strCity']],
		['{state}',GO.lang['strState']],
		['{country}',GO.lang['strCountry']],
		['{company}',GO.lang['strCompany']],
		['{department}',GO.lang['strDepartment']],
		['{function}',GO.lang['strFunction']],
		['{work_phone}',GO.lang['strWorkPhone']],
		['{work_fax}',GO.lang['strWorkFax']],
		['{work_address}',GO.lang['strWorkAddress']],
		['{work_address_no}',GO.lang.strWorkAddressNo],
		['{work_city}',GO.lang['strWorkCity']],
		['{work_zip}',GO.lang['strWorkZip']],
		['{work_state}',GO.lang['strWorkState']],
		['{work_country}',GO.lang['strWorkCountry']],
		['{work_post_address}',GO.lang['strPostAddress']],
		['{work_post_address_no}',GO.lang['strPostAddressNo']],
		['{work_post_city}',GO.lang['strPostCity']],
		['{work_post_zip}',GO.lang['strPostZip']],
		['{work_post_state}',GO.lang['strPostState']],
		['{work_post_country}',GO.lang['strPostCountry']],
		['{homepage}',GO.lang['strHomepage']],
		['{user:name}',GO.lang.strName+' ('+GO.lang.strUser+')'],
		['{user:first_name}',GO.lang['strFirstName']+' ('+GO.lang.strUser+')'],
		['{user:middle_name}',GO.lang['strMiddleName']+' ('+GO.lang.strUser+')'],
		['{user:last_name}',GO.lang['strLastName']+' ('+GO.lang.strUser+')'],
		['{user:initials}',GO.lang['strInitials']+' ('+GO.lang.strUser+')'],
		['{user:title}',GO.lang['strTitle']+' ('+GO.lang.strUser+')'],
		['{user:email}',GO.lang['strEmail']+' ('+GO.lang.strUser+')'],
		['{user:home_phone}',GO.lang['strPhone']+' ('+GO.lang.strUser+')'],
		['{user:fax}',GO.lang['strFax']+' ('+GO.lang.strUser+')'],
		['{user:cellular}',GO.lang['strCellular']+' ('+GO.lang.strUser+')'],
		['{user:address}',GO.lang['strAddress']+' ('+GO.lang.strUser+')'],
		['{user:address_no}',GO.lang['strAddressNo']+' ('+GO.lang.strUser+')'],
		['{user:zip}',GO.lang['strZip']+' ('+GO.lang.strUser+')'],
		['{user:city}',GO.lang['strCity']+' ('+GO.lang.strUser+')'],
		['{user:state}',GO.lang['strState']+' ('+GO.lang.strUser+')'],
		['{user:country}',GO.lang['strCountry']+' ('+GO.lang.strUser+')'],
		['{user:name}',GO.lang['strCompany']+' ('+GO.lang.strUser+')'],
		['{user:department}',GO.lang['strDepartment']+' ('+GO.lang.strUser+')'],
		['{user:function}',GO.lang['strFunction']+' ('+GO.lang.strUser+')'],
		['{usercompany:phone}',GO.lang['strWorkPhone']+' ('+GO.lang.strUser+')'],
		['{usercompany:fax}',GO.lang['strWorkFax']+' ('+GO.lang.strUser+')'],
		['{usercompany:address}',GO.lang['strWorkAddress']+' ('+GO.lang.strUser+')'],
		['{usercompany:address_no}',GO.lang['strWorkAddressNo']+' ('+GO.lang.strUser+')'],
		['{usercompany:city}',GO.lang['strWorkCity']+' ('+GO.lang.strUser+')'],
		['{usercompany:zip}',GO.lang['strWorkZip']+' ('+GO.lang.strUser+')'],
		['{usercompany:state}',GO.lang['strWorkState']+' ('+GO.lang.strUser+')'],
		['{usercompany:country}',GO.lang['strWorkCountry']+' ('+GO.lang.strUser+')'],
		['{user:homepage}',GO.lang.strHomepage+' ('+GO.lang.strUser+')'],
		['{unsubscribe_link}',GO.addressbook.lang.unsubscribeLink],
		['%unsubscribe_href%',GO.addressbook.lang.unsubscribeHref]
		];
   	
		var items = [new Ext.Panel({
			title:GO.addressbook.lang.autoData ,
			autoScroll:true,
			items:new GO.grid.SimpleSelectList({
				store:  new Ext.data.SimpleStore({
					fields: ['value', 'name'],
					data : autodata
				}),
				listeners:{
					scope:this,
					click:function(dataview, index){
						
						this.htmlEditPanel.getHtmlEditor().insertAtCursor(dataview.store.data.items[index].data.value);
						this.htmlEditPanel.getHtmlEditor().deferFocus();
						dataview.clearSelections();
					}
				}
			})
		})];

		if(GO.customfields){
			autodata=[];
//			if(GO.customfields.types["2"] && GO.customfields.types["2"].panels.length)
//			{
//				for(var i=0;i<GO.customfields.types["2"].panels.length;i++)
//				{
//					var p = GO.customfields.types["2"].panels[i];
//					for(var c=0;c<p.customfields.length;c++){
//						autodata.push(['{'+p.customfields[c].dataname+'}',p.customfields[c].name]);
//					}
//				}
//			}
			if(autodata.length){
				items.push(new Ext.Panel({
					autoScroll:true,
					title:GO.addressbook.lang.customContactFields,

					items:new GO.grid.SimpleSelectList({
						store:  new Ext.data.SimpleStore({
							fields: ['value', 'name'],
							data : autodata
						}),
						listeners:{
							scope:this,
							click:function(dataview, index){
								this.htmlEditPanel.getHtmlEditor().insertAtCursor(dataview.store.data.items[index].data.value);
								this.htmlEditPanel.getHtmlEditor().deferFocus();
								dataview.clearSelections();
							}
						}
					})
				}));
			}

			autodata=[];
//			if(GO.customfields.types["GO_B"] && GO.customfields.types["3"].panels.length)
//			{
//				for(var i=0;i<GO.customfields.types["3"].panels.length;i++)
//				{
//					var p = GO.customfields.types["3"].panels[i];
//					for(var c=0;c<p.customfields.length;c++){
//						autodata.push(['{'+p.customfields[c].dataname+'}',p.customfields[c].name]);
//					}
//				}
//			}
			if(autodata.length){
				items.push(new Ext.Panel({
					autoScroll:true,
					title:GO.addressbook.lang.customCompanyFields,
					items:new GO.grid.SimpleSelectList({
						store:  new Ext.data.SimpleStore({
							fields: ['value', 'name'],
							data : autodata
						}),
						listeners:{
							scope:this,
							click:function(dataview, index){
								this.htmlEditPanel.getHtmlEditor().insertAtCursor(dataview.store.data.items[index].data.value);
								this.htmlEditPanel.getHtmlEditor().deferFocus();
								dataview.clearSelections();
							}
						}
					})
				}));
			}
			
			
			
			autodata=[];
//			if(GO.customfields.types["8"] && GO.customfields.types["8"].panels.length)
//			{
//				for(var i=0;i<GO.customfields.types["8"].panels.length;i++)
//				{
//					var p = GO.customfields.types["8"].panels[i];
//					for(var c=0;c<p.customfields.length;c++){
//						autodata.push(['{user:'+p.customfields[c].dataname+'}',p.customfields[c].name]);
//					}
//				}
//			}
			if(autodata.length){
				items.push(new Ext.Panel({
					autoScroll:true,
					title:GO.addressbook.lang.customUserFields,
					items:new GO.grid.SimpleSelectList({
						store:  new Ext.data.SimpleStore({
							fields: ['value', 'name'],
							data : autodata
						}),
						listeners:{
							scope:this,
							click:function(dataview, index){
								this.htmlEditPanel.getHtmlEditor().insertAtCursor(dataview.store.data.items[index].data.value);
								this.htmlEditPanel.getHtmlEditor().deferFocus();
								dataview.clearSelections();
							}
						}
					})
				}));
			}
		}

		this.autoDataPanel = new Ext.Panel({
			region:'east',
			layout:'accordion',
			border:false,
			autoScroll:true,
			width: 180,
			split:true,
			resizable:true,
			items:items
		});
		

		this.propertiesPanel = new Ext.Panel({
			region:'center',
			border: false,
			layout:'border',
			items:[{
				region:'north',
				height:60,
				layout:'form',
				border: false,
				cls:'go-form-panel',			
				items:[{
					xtype: 'textfield',
					name: 'name',
					anchor: '100%',
					allowBlank:false,
					fieldLabel: GO.lang.strName
				}]
			},
			this.htmlEditPanel = new GO.base.email.EmailEditorPanel({
				region:'center'
			})]
				
		});

		//{text:'Toggle HTML',handler:function(){this.htmlEditPanel.setContentTypeHtml(this.htmlEditPanel.getContentType()!='html')}, scope:this}
		var borderLayoutPanel = new Ext.Panel({
			layout:'border',
			title:GO.lang['strProperties'],	
			tbar:[this.htmlEditPanel.getAttachmentsButton()],
			items: [this.propertiesPanel, this.autoDataPanel]			
		});
		

		var items  = [borderLayoutPanel];
		
		this.readPermissionsTab = new GO.grid.PermissionsPanel({
			
			});

		items.push(this.readPermissionsTab);
 
		this.tabPanel = new Ext.TabPanel({
			activeTab: 0,
			deferredRender: false,
			border: false,
			items: items,
			anchor: '100% 100%'
		}) ;
    
    
		this.formPanel = new Ext.form.FormPanel({
			border: false,
			baseParams: {
				task: 'email_template'
			},
			waitMsgTarget:true,			
			items:this.tabPanel				
		});
    
    
	}
});