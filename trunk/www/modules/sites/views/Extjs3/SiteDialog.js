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
	//id 	name 	user_id 	mtime 	ctime 	domain 	template 	login_path 	ssl 	mod_rewrite 	mod_rewrite_base_path 	lost_password_path 	reset_password_path 	register_path 	logout_path 	register_user_groups 
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
				name: 'lost_password_path',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.sites.lang.siteLostPasswordPath
			}]
		});

		this.addPanel(this.propertiesPanel);
	}
});