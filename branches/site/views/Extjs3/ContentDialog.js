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
 
GO.site.ContentDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	customFieldType : "GO_Site_Model_Content",
	
	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'content',
			title:GO.site.lang.content,
			formControllerUrl: 'site/content',
			height:600,
			width:900
		});
		
		GO.site.ContentDialog.superclass.initComponent.call(this);	
	},
	
	buildForm : function () {
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',
			layout:'form',
			items:[{
	  		xtype: 'fieldset',
	  		title: GO.lang.strProperties,
	  		autoHeight: true,
	  		border: true,
	  		collapsed: false,
				items:[{
					xtype: 'textfield',
					name: 'title',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.site.lang.contentTitle
				},{
					xtype: 'textfield',
					name: 'slug',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:false,
					fieldLabel: GO.site.lang.contentSlug
				}]
			},{
	  		xtype: 'fieldset',
	  		title: GO.site.lang.metaData,
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
					fieldLabel: GO.site.lang.contentMeta_description
				},{
					xtype: 'textfield',
					name: 'keywords',
					width:300,
					anchor: '100%',
					maxLength: 100,
					allowBlank:true,
					fieldLabel: GO.site.lang.contentMeta_keywords
				}]
			}]
		});

		this.addPanel(this.propertiesPanel);
		
		this.contentPanel = new Ext.Panel({
			title:GO.site.lang.contentContent,			
			layout:'form',
			items:[
					new GO.form.HtmlEditor({
						hideLabel:true,
						name: 'content',
						anchor: '100% 100%',
						allowBlank:true
					})
				]
	
		});

		this.addPanel(this.contentPanel);
		
	},
	
	setSiteId : function(siteId){
		this.addBaseParam('site_id', siteId);
	}
});