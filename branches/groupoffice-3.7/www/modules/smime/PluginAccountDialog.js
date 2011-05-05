/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: Overrides.js 0000 2010-12-29 08:59:17 wsmits $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.AccountDialog, {
            initComponent : GO.email.AccountDialog.prototype.initComponent.createInterceptor(function(){
							
								this.propertiesPanel.fileUpload=true;
								this.propertiesPanel.bodyCfg.enctype="multipart/form-data";
								
								this.smimePanel=new Ext.Panel({
									cls:'go-form-panel',
									title:'SMIME setttings',
									items:[
										this.deleteCert = new Ext.form.Checkbox({
											boxLabel:"Delete certificate",
											labelSeparator: '',
											name: 'delete_cert',
											allowBlank: true,
											hideLabel:true,
											disabled:true
										}),
										this.uploadFile = new GO.form.UploadFile({
											addText:'Select new PKSC12 Certificate',
											inputName : 'cert',
											max: 1
										}),{
											xtype:'checkbox',
											hideLabel:true,
											boxLabel:'Always sign messages',
											name:'always_sign'
										}
									]
								});
							
                this.tabPanel.add(this.smimePanel);
								
								
								
								
								this.propertiesPanel.form.on("actioncomplete", function(form, action){													
									if(action.type=='submit'){
										this.uploadFile.clearQueue();
										this.deleteCert.setDisabled(!action.result.cert);
									}else
									{
										this.deleteCert.setDisabled(!action.result.data.cert);
									}
								}, this);
            })
        })
});
        