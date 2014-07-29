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
 
GO.postfixadmin.AliasDialog = Ext.extend(GO.dialog.TabbedFormDialog,{
	
	initComponent : function(){
		Ext.apply(this, {
			titleField:'address',
			title: GO.postfixadmin.lang.alias,
			formControllerUrl: 'postfixadmin/alias',
			width:700,
			height:500
		});
		this.addEvents({'save' : true});	
		GO.postfixadmin.AliasDialog.superclass.initComponent.call(this);
	},
		
	buildForm : function () {
		
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',waitMsgTarget:true,			
			layout:'form',
			autoScroll:true,
			items:[{
				xtype: 'textfield',
				name: 'domain_id',
				hidden: true
			},{
				xtype: 'textfield',
			  name: 'address',
				anchor: '-20',
			  allowBlank:false,
			  fieldLabel: GO.postfixadmin.lang.address
			},{
				xtype: 'textarea',
			  name: 'goto',
				anchor: '-20',
			  allowBlank:true,
			  fieldLabel: GO.postfixadmin.lang.goto_address,
				plugins:[new Ext.ux.FieldHelp(GO.postfixadmin.lang.aliasHelp)]
			},{
				xtype: 'xcheckbox',
			  name: 'active',
				anchor: '-20',
			  allowBlank:false,
			  boxLabel: GO.postfixadmin.lang.active,
			  hideLabel: true
			}]
		});
		
		this.addPanel(this.propertiesPanel);	
	}
});