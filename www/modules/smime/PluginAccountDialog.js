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
									title:GO.smime.lang.settings,
									disabled:true,
									items:[
										this.deleteCert = new Ext.form.Checkbox({
											boxLabel:GO.smime.lang.deleteCert,
											labelSeparator: '',
											name: 'delete_cert',
											allowBlank: true,
											hideLabel:true,
											disabled:true
										}),
										this.uploadFile = new GO.form.UploadFile({
											addText:GO.smime.lang.selectPkcs12Cert,
											inputName : 'cert',
											max: 1
										}),{
											xtype:'checkbox',
											hideLabel:true,
											boxLabel:GO.smime.lang.alwaysSign,
											name:'always_sign'
										}
									]
								});
							
                this.tabPanel.add(this.smimePanel);
								
								
								this.on('show', function(){
									this.smimePanel.setDisabled(true);
								}, this)
								
								this.propertiesPanel.form.on("actioncomplete", function(form, action){													
									if(action.type=='submit'){
										this.uploadFile.clearQueue();
										this.deleteCert.setDisabled(!action.result.cert);
									}else
									{
										this.smimePanel.setDisabled(false);
										this.deleteCert.setDisabled(!action.result.data.cert);
									}
								}, this);
            })
        })
});
        