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
	
	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'site',
			title:GO.sites.lang.site,
			formControllerUrl: 'sites/siteBackend'
		});
		
		GO.sites.SiteDialog.superclass.initComponent.call(this);	
	},
//	ssl 	mod_rewrite:)

	buildForm : function () {
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',
			layout:'form',
			labelWidth: 200,
			items:[{
				xtype: 'textfield',
				name: 'name',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteName
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
				name: 'user_id',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteUserId
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
				name: 'lost_password_path',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteLostPasswordPath
			},{
				xtype: 'textfield',
				name: 'reset_password_path',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteResetPasswordPath
			},{
				xtype: 'textfield',
				name: 'logout_path',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteLogoutPath
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
				name: 'register_path',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteRegisterPath
			},{
				xtype: 'textfield',
				name: 'register_user_groups',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteRegisterUserGroups
			},{
				xtype: 'textfield',
				name: 'mod_rewrite_base_path',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteModRewriteBasePath
			}]
		});

		this.addPanel(this.propertiesPanel);
	}
});