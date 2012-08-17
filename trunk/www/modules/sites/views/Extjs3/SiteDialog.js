/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: SiteDialog.js 8376 2011-10-24 09:55:16Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */
 
GO.sites.SiteDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	customFieldType : "GO_Sites_Model_Site",

	initComponent : function() {
		Ext.apply(this, {
			goDialogId:'site',
			title:GO.sites.lang.siteProperties,
			formControllerUrl: 'sites/siteBackend',
			height:550
		});
		
		GO.sites.SiteDialog.superclass.initComponent.call(this);
	},
	buildForm : function () {
		
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang.strProperties,
			cls:'go-form-panel',
			layout:'form',
			labelWidth: 170,
			items:[{
	  		xtype: 'fieldset',
	  		title: GO.sites.lang.siteProperties,
	  		autoHeight: true,
	  		border: true,
	  		collapsed: false,
				items:[
					{
						xtype: 'hidden',
						name: 'site_id',
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteId
					},{
						xtype: 'textfield',
						name: 'name',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteName
					},{
						xtype: 'textfield',
						name: 'domain',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteDomain
					},{
						xtype: 'textfield',
						name: 'template',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteTemplate
					},{
						xtype: 'textfield',
						name: 'login_path',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteLoginPath
					},{
						xtype: 'textfield',
						name: 'register_user_groups',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:true,
						fieldLabel: GO.sites.lang.siteRegisterUserGroups
					},{
						xtype: 'textfield',
						name: 'language',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:true,
						fieldLabel: GO.sites.lang.language
					},{
						xtype: 'xcheckbox',
						name: 'ssl',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteSsl
					},{
						xtype: 'xcheckbox',
						name: 'mod_rewrite',
						width:300,
						anchor: '100%',
						maxLength: 100,
						allowBlank:false,
						fieldLabel: GO.sites.lang.siteModRewrite
					}]		
			}]
		});

		this.addPanel(this.propertiesPanel);
	}
});