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
                    this.messageBodyEl.down(".message-header").createChild({
                        tag:'a',
                        target:'_blank',
                        href:GO.settings.modules.smime.url+'verify.php?uid='+data.uid+'&account_id='+data.account_id+'&mailbox='+encodeURIComponent(data.mailbox),
                        html:"verify"
                    });
                })
            })
        })
});
        