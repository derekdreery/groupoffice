/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @since Group-Office 1.0
 */


GO.email.MessagePanel = Ext.extend(Ext.Panel, {
	
	
	initComponent : function(){
		
		GO.email.MessagePanel.superclass.initComponent.call(this);
		
		
		this.addEvents({
			attachmentClicked : true,
			zipOfAttachmentsClicked : true,
			linkClicked : true,
			emailClicked : true,
			load : true
		});
		
		this.bodyId = Ext.id();
		this.attachmentsId = Ext.id();
		
		var templateStr = '<div class="message-header">'+
			'<table class="message-header-table">'+
			'<tr><td style="width:70px"><b>'+GO.email.lang.from+'</b></td>'+			
			'<td>: {from} &lt;<a class="normal-link" href="#" oncontextmenu="GO.email.showAddressMenu(event, \'{sender}\', \'{from}\');">{sender}</a>&gt;</td></tr>'+
			'<tr><td><b>'+GO.email.lang.subject+'</b></td><td>: {subject}</td></tr>'+
			'<tr><td><b>'+GO.lang.strDate+'</b></td><td>: {date}</td></tr>'+
			//'<tr><td><b>'+GO.lang.strSize+'</b></td><td>: {size}</td></tr>'+
			'<tr><td><b>'+GO.email.lang.to+'</b></td><td>: '+
			'<tpl for="to">'+
			'{name} <tpl if="email.length">&lt;<a class="normal-link" href="#" oncontextmenu="GO.email.showAddressMenu(event, \'{email}\', \'{name}\');">{email}</a>&gt;; </tpl>'+	
			'</tpl>'+
			'</td></tr>'+
			'<tpl if="cc.length">'+
				'<tr><td><b>'+GO.email.lang.cc+'</b></td><td>: '+
				'<tpl for="cc">'+
				'{name} <tpl if="email.length">&lt;<a class="normal-link" href="#" oncontextmenu="GO.email.showAddressMenu(event, \'{email}\', \'{name}\');">{email}</a>&gt;; </tpl>'+	
				'</tpl>'+
				'</td></tr>'+
			'</tpl>'+
			'<tpl if="bcc.length">'+
				'<tr><td><b>'+GO.email.lang.bcc+'</b></td><td>: '+
				'<tpl for="bcc">'+
				'{name} <tpl if="email.length">&lt;<a class="normal-link" href="#" oncontextmenu="GO.email.showAddressMenu(event, \'{email}\', \'{name}\');">{email}</a>&gt;; </tpl>'+	
				'</tpl>'+
				'</td></tr>'+
			'</tpl>'+
			'</table>'+
			'<tpl if="attachments.length">'+
				'<table style="padding-top:5px;">'+
				'<tr><td><b>'+GO.email.lang.attachments+':</b></td></tr><tr><td id="'+this.attachmentsId+'">'+
					'<tpl for="attachments">'+
					'<a class="filetype-link filetype-{extension}" id="'+this.attachmentsId+'_{index}" href="#">{name} ({human_size})</a> '+
					'</tpl>'+
					'<tpl if="attachments.length&gt;1">'+
						'<a class="filetype-link filetype-zip" id="'+this.attachmentsId+'_zipofall" href="#">'+GO.email.lang.downloadAllAsZip+'</a>'+
					'</tpl>'+
				'</td></tr>'+
				'</table>'+
			'</tpl>'+
			'<tpl if="blocked_images&gt;0">'+
			'<div class="go-warning-msg em-blocked">'+GO.email.lang.blocked+' <a id="em-unblock" href="#" class="normal-link">'+GO.email.lang.unblock+'</a></div>'+
			'</tpl>'+
			'</div>'+
			'<div id="'+this.bodyId+'" class="message-body">{body}</div>';
		
		this.template = new Ext.XTemplate(templateStr,{
			
					
		});		
		this.template.compile();	
	},
	
	loadMessage : function(uid, mailbox, account_id)
	{
		if(uid)
		{
			this.params = {
					uid: uid,
					mailbox: mailbox,
					account_id: account_id,
					task:'message'
				};
		}
				
		this.el.mask(GO.lang.waitMsgLoad);				
		Ext.Ajax.request({
			url: GO.settings.modules.email.url+'json.php',
			params: this.params,
			scope: this,
			callback: function(options, success, response)
			{					
				this.fireEvent('load', options, success, response);
				
				if(success)					
				{
					var data = Ext.decode(response.responseText);						
					this.setMessage(data);						
					this.el.unmask();
				}				
			}
		});
	},
	
	reset : function(){
		this.data=false;
		
		if(this.messageBodyEl)
		{
			this.messageBodyEl.removeAllListeners();
		}
		if(this.attachmentsEl)
		{
			this.attachmentsEl.removeAllListeners();
		}
		
		if(this.unblockEl)
		{
			this.unblockEl.removeAllListeners();
		}
		
		this.body.update('');
	},
	
	setMessage : function(data)
	{		
		
		this.data = data;
		
		//remove old listeners
		if(this.messageBodyEl)
		{
			this.messageBodyEl.removeAllListeners();
		}
		if(this.attachmentsEl)
		{
			this.attachmentsEl.removeAllListeners();
		}
		
		if(this.unblockEl)
		{
			this.unblockEl.removeAllListeners();
		}
		
		this.template.overwrite(this.body, data);		
		
		
		this.unblockEl = Ext.get('em-unblock');
		if(this.unblockEl)
		{
			this.unblockEl.on('click', function(){
				this.params.unblock='true';
				this.loadMessage();
			}, this);
		}
		
		this.messageBodyEl = Ext.get(this.bodyId);		
		this.messageBodyEl.on('click', this.onMessageBodyClick, this);
		this.messageBodyEl.on('contextmenu', this.onMessageBodyContextMenu, this);
		
		if(data.attachments.length)
		{
			this.attachmentsEl = Ext.get(this.attachmentsId);			
			this.attachmentsEl.on('click', this.openAttachment, this);
			
			if(this.attachmentContextMenu)
			{			
				this.attachmentsEl.on('contextmenu', this.onAttachmentContextMenu, this);
			}
		}
		
		this.body.scrollTo('top',0);
		
		if(this.data['new'] && this.data.notification)
		{
			if(confirm(GO.email.lang.sendNotification.replace('%s', this.data.notification)))
			{
				var params = {
					task:'notification',
					account_id: this.data.account_id,
					notification_to: this.data.notification,
					subject: this.data.subject
				}
				
				Ext.Ajax.request({
					url: GO.settings.modules.email.url+'action.php',
					params: params
				});
			}
		}
	},
	
	onAttachmentContextMenu : function (e, target){
		
		
		if(target.id.substr(0,this.attachmentsId.length)==this.attachmentsId)
		{			
			var attachment_no = target.id.substr(this.attachmentsId.length+1);
			
			if(attachment_no=='zipofall')
			{
				//this.fireEvent('zipOfAttachmentsClicked');				
			}else
			{
				e.preventDefault();
				var attachment = this.data.attachments[attachment_no];				
				this.attachmentContextMenu.showAt(e.getXY(), attachment);
			} 
		}
			
	},
	
	openAttachment :  function(e, target)
	{
		//e.preventDefault();
		//alert(target.id);
		if(target.id.substr(0,this.attachmentsId.length)==this.attachmentsId)
		{			
			var attachment_no = target.id.substr(this.attachmentsId.length+1);
			
			if(attachment_no=='zipofall')
			{
				this.fireEvent('zipOfAttachmentsClicked');				
			}else
			{
				var attachment = this.data.attachments[attachment_no];				
				this.fireEvent('attachmentClicked', attachment, this);
			} 
		}
	},
	
	onMessageBodyContextMenu :  function(e, target){
		
		if(target.tagName!='A')
		{
			target = Ext.get(target).findParent('A', 10);
			if(!target)
				return false;
		}
		
		if(target.tagName=='A')
		{
			var href=target.attributes['href'].value;
			
			if(href.substr(0,6)=='mailto')
			{
				var indexOf = href.indexOf('?');
				if(indexOf>0)
				{
					var email = href.substr(7, indexOf-8);
				}else
				{
					var email = href.substr(7);
				}				

				e.preventDefault();
				
				GO.email.addressContextMenu.showAt(e.getXY(), email);
			}else
			{
				
			}
		}		
	},
	
	onMessageBodyClick :  function(e, target){
		if(target.tagName!='A')
		{
			target = Ext.get(target).findParent('A', 10);
			if(!target)
				return false;
		}
		
		if(target.tagName=='A')
		{
			e.preventDefault();
			var href=target.attributes['href'].value;
			
			if(href.substr(0,6)=='mailto')
			{
				var indexOf = href.indexOf('?');
				if(indexOf>0)
				{
					var email = href.substr(7, indexOf-8);
				}else
				{
					var email = href.substr(7);
				}						
				this.fireEvent('emailClicked', email);			
			}else
			{				
				this.fireEvent('linkClicked', href);
			}
		}		
	}
});