/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: SecureIframeComponent.js
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */
 
 
GO.panel.SecureIFrameComponent = Ext.extend(Ext.BoxComponent, {
     onRender : function(ct, position){
     	
     		if(!this.name)
     		{
     			this.name='';
     		}

				Ext.Ajax.request({
					url: GO.settings.modules.mediawiki.url+'json.php',
					params: {task:'login'},
					callback: function(options,success,response) {
						var responseParams = Ext.decode(response.responseText);
						if (success) {
							this.el = ct.createChild({tag: 'iframe', id: this.id, frameBorder: 0, src: responseParams.url, name:this.name, width:'100%', height: '100%'});
						} else {
							Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
						}
					},
					scope: this
				})
     },
     
     setUrl : function(url){
     	this.el.set({src:url});
     }
});