/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: PageDialog.js 8376 2011-10-24 09:55:16Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
 
GO.sites.PageDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	customFieldType : "GO_Sites_Model_Page",
	
	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'page',
			title:GO.sites.lang.page,
			formControllerUrl: 'sites/pageBackend'
		});
		
		GO.sites.PageDialog.superclass.initComponent.call(this);	
	},
	
	buildForm : function () {
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',
			layout:'form',
			items:[{
	  		xtype: 'fieldset',
	  		title: GO.sites.lang.pageProperties,
	  		autoHeight: true,
	  		border: true,
	  		collapsed: false,
				items:[{
					xtype: 'textfield',
					name: 'name',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.sites.lang.pageName
				},{
					xtype: 'textfield',
					name: 'title',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.sites.lang.pageTitle
				},{
					xtype: 'textfield',
					name: 'path',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:true,
					fieldLabel: GO.sites.lang.pagePath
				},{
					xtype: 'textfield',
					name: 'template',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:true,
					fieldLabel: GO.sites.lang.pageTemplate
				},{
					xtype: 'textfield',
					name: 'controller',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.sites.lang.pageController
				},{
					xtype: 'textfield',
					name: 'controller_action',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.sites.lang.pageControllerAction
				},{
					xtype: 'xcheckbox',
					name:'login_required',
					fieldLabel: "Login required"
				}]
			}]
		});

		this.addPanel(this.propertiesPanel);
		
		this.contentPanel = new Ext.Panel({
			title:GO.sites.lang.pageContent,			
			cls:'go-form-panel',
			layout:'form',
			items:[{
	  		xtype: 'fieldset',
	  		title: GO.sites.lang.pagePageContent,
	  		autoHeight: true,
	  		border: true,
	  		collapsed: false,
				items:[
					new Ext.form.HtmlEditor({
						hideLabel:true,
						name: 'content',
						height: 230,
						anchor: '100%',
						allowBlank:true
					})
				]
			}]
		});

		this.addPanel(this.contentPanel);
		
		this.metaPanel = new Ext.Panel({
			title:GO.sites.lang.pageMetaData,			
			cls:'go-form-panel',
			layout:'form',
			items:[{
	  		xtype: 'fieldset',
	  		title: GO.sites.lang.pagePageMetaData,
	  		autoHeight: true,
	  		border: true,
	  		collapsed: false,
				items:[{
					xtype: 'textfield',
					name: 'description',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:true,
					fieldLabel: GO.sites.lang.pageDescription
				},{
					xtype: 'textfield',
					name: 'keywords',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:true,
					fieldLabel: GO.sites.lang.pageKeywords
				}]
			},{
	  		xtype: 'fieldset',
	  		title: GO.sites.lang.timeStamps,
	  		autoHeight: true,
	  		border: true,
	  		collapsed: false,
				items:[{
					xtype: 'plainfield',
					name: 'mtime',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.sites.lang.pageMtime
				},{
					xtype: 'plainfield',
					name: 'ctime',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.sites.lang.pageCtime
				}]
			}]
		});

		this.addPanel(this.metaPanel);
		
	},
	
	setSiteId : function(siteId){
		this.addBaseParam('site_id', siteId);
	}
});