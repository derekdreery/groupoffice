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
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.moduleManager.onModuleReady('email',function(){
	Ext.override(GO.email.MessagePanel, {
            initComponent : GO.email.MessagePanel.prototype.initComponent.createSequence(function(){
                this.on('load',function(options, success, response, data){
									if(data.smime_signed){
										var el = this.body.down(".message-header").createChild({													
													html:"This message is signed. Click here to verify the signature.",
													style:"cursor:pointer;text-decoration:underline"
											});
											
											el.on('click', function(){
												if(!this.certWin){
													this.certWin = new GO.Window({
														title:'SMIME Certificate',
														width:500,
														height:300,
														closeAction:'hide',
														layout:'fit',
														items:[this.certPanel = new Ext.Panel({bodyStyle:'padding:10px'})]
													});
												}
												
												this.certWin.show();
												this.certPanel.load(GO.settings.modules.smime.url+'verify.php?uid='+													
													this.uid+'&account_id='+this.account_id+'&mailbox='+encodeURIComponent(this.mailbox));
											}, this)
									}
								})
            })
        })
});
        