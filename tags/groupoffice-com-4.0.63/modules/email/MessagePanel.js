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
	
	uid : 0,

	mailbox:  "",
	
	account_id: 0,

	initComponent : function(){
		
		GO.email.MessagePanel.superclass.initComponent.call(this);
		
		
		this.addEvents({
			attachmentClicked : true,
			linkClicked : true,
			emailClicked : true,
			load : true,
			reset : true
		});
		
		this.bodyId = Ext.id();
		this.attachmentsId = Ext.id();
		
		var templateStr = '<div class="message-header">'+
		'<table class="message-header-table">'+
		'<tr><td style="width:70px"><b>'+GO.email.lang.from+'</b></td>'+			
		'<td>: {from} &lt;<a class="normal-link" href="#" onclick="GO.email.showAddressMenu(event, \'{sender}\', \'{[this.addSlashes(values.from)]}\');">{sender}</a>&gt;</td></tr>'+
		'<tr><td><b>'+GO.email.lang.subject+'</b></td><td>: {subject}</td></tr>'+
		'<tr><td><b>'+GO.lang.strDate+'</b></td><td>: {date}</td></tr>'+
		//'<tr><td><b>'+GO.lang.strSize+'</b></td><td>: {size}</td></tr>'+
		'<tr><td><b>'+GO.email.lang.to+'</b></td><td>: '+
		'<tpl for="to">'+
		'{personal} <tpl if="email.length">&lt;<a class="normal-link" href="#" onclick="GO.email.showAddressMenu(event, \'{email}\', \'{[this.addSlashes(values.personal)]}\');">{email}</a>&gt;; </tpl>'+	
		'</tpl>'+
		'</td></tr>'+
		'<tpl if="cc.length">'+
		'<tr><td><b>'+GO.email.lang.cc+'</b></td><td>: '+
		'<tpl for="cc">'+
		'{personal} <tpl if="email.length">&lt;<a class="normal-link" href="#" onclick="GO.email.showAddressMenu(event, \'{email}\', \'{[this.addSlashes(values.personal)]}\');">{email}</a>&gt;; </tpl>'+	
		'</tpl>'+
		'</td></tr>'+
		'</tpl>'+
		'<tpl if="bcc.length">'+
		'<tr><td><b>'+GO.email.lang.bcc+'</b></td><td>: '+
		'<tpl for="bcc">'+
		'{personal} <tpl if="email.length">&lt;<a class="normal-link" href="#" onclick="GO.email.showAddressMenu(event, \'{email}\', \'{[this.addSlashes(values.name)]}\');">{email}</a>&gt;; </tpl>'+	
		'</tpl>'+
		'</td></tr>'+
		'</tpl>'+
		'</table>'+
		'<tpl if="attachments.length">'+
		'<table style="padding-top:5px;">'+
		'<tr><td><b>'+GO.email.lang.attachments+':</b></td></tr><tr><td id="'+this.attachmentsId+'">'+
		'<tpl for="attachments">'+
		'<a class="filetype-link filetype-{extension}" id="'+this.attachmentsId+'_{[xindex-1]}" href="#">{name} ({human_size})</a> '+
		'</tpl>'+
		'<tpl if="attachments.length&gt;1 && zip_of_attachments_url!=\'\'">'+
		'<a class="filetype-link filetype-zip" href="{zip_of_attachments_url}" target="_blank">'+GO.email.lang.downloadAllAsZip+'</a>'+
		'</tpl>'+
		'</td></tr>'+
		'</table>'+
		'</tpl>'+
		'<tpl if="blocked_images&gt;0">'+
		'<div class="go-warning-msg em-blocked">'+GO.email.lang.blocked+' <a id="em-unblock" href="#" class="normal-link">'+GO.email.lang.unblock+'</a></div>'+
		'</tpl>'+			
		'<tpl if="xssDetected">'+
		'<div class="go-warning-msg em-blocked"><a id="em-filterxss" href="#" class="normal-link">'+GO.email.lang.xssDetected+'</a></div>'+
		'</tpl>'+	
		'</div>'+
		'<tpl if="!GO.util.empty(values.iCalendar)">'+
		'<tpl if="iCalendar.feedback">'+
		'<div class="message-icalendar">'+
		'<span class="message-icalendar-icon go-model-icon-GO_Calendar_Model_Event"></span>'+
		'{[values.iCalendar.feedback]}'+
		'<span class="message-icalendar-actions">'+
		'<tpl if="iCalendar.invitation">'+
			'<tpl if="iCalendar.invitation.is_invitation">'+
			//'<tpl if="!iCalendar.invitation.event_id || iCalendar.invitation.event_declined == true">'+
				'<a class="normal-link" id="em-icalendar-accept-invitation" href="#">'+GO.email.lang.icalendarAcceptInvitation+'</a> '+
			//'</tpl>'+
				'<tpl if="iCalendar.invitation.event_declined == false">'+
				'<a class="normal-link" id="em-icalendar-decline-invitation" href="#">'+GO.email.lang.icalendarDeclineInvitation+'</a> '+
				'</tpl>'+
				'<a class="normal-link" id="em-icalendar-tentative-invitation" href="#">'+GO.email.lang.icalendarTentativeInvitation+'</a> '+
			'</tpl>'+
			'<tpl if="iCalendar.invitation.is_cancellation && iCalendar.invitation.event_id">'+
				'<a class="normal-link" id="em-icalendar-delete-event" href="#">'+GO.email.lang.icalendarDeleteEvent+'</a>'+
			'</tpl>'+
			'<tpl if="iCalendar.invitation.is_update">'+
				'<a class="normal-link" id="em-icalendar-update-event" href="#">'+GO.email.lang.icalendarUpdateEvent+'</a>'+
			'</tpl>'+
		'</tpl>'+
		'</span>'+
		'</div>'+
		'</tpl>'+
		'</tpl>'+
		'<div id="'+this.bodyId+'" class="message-body go-html-formatted">{htmlbody}</div>';
		
		this.template = new Ext.XTemplate(templateStr,{
			addSlashes : function(str)
			{
				str = GO.util.html_entity_decode(str, 'ENT_QUOTES');
				str = GO.util.add_slashes(str);
				return str;
			}

		});		
		this.template.compile();	
	},
	
	loadMessage : function(uid, mailbox, account_id, password)
	{		
		if(uid)
		{
			this.uid=uid;
			this.account_id=account_id;
			this.mailbox=mailbox;
			
			this.params = {
				uid: uid,
				mailbox: mailbox,
				account_id: account_id
			};
			if(password)
			{
				this.params.password=password;
			}
		}
				
		this.el.mask(GO.lang.waitMsgLoad);				
		GO.request({
			url: "email/message/view",
			params: this.params,
			scope: this,
			success: function(options, response, data)
			{							                          
//				if(this.updated)
//				{
//					data.iCalendar.feedback = GO.email.lang.icalendarEventUpdated;
//					this.updated = false;
//				}else
//				if(this.created)
//				{
//					data.iCalendar.feedback = GO.email.lang.icalendarEventCreated;
//					this.created = false;
//				}else
//				if(this.deleted)
//				{
//					data.iCalendar.feedback = GO.email.lang.icalendarEventDeleted;
//					this.deleted = false;
//				}else
//				if(this.declined)
//				{
//					data.iCalendar.feedback = GO.email.lang.icalendarInvitationDeclined;
//					this.declined = false;
//				}
				
				if(this.icalendarFeedback){
					data.iCalendar.feedback = this.icalendarFeedback;			
					delete this.icalendarFeedback;
				}
					
				data.mailbox=mailbox;

				if(data.askPassword)
				{
					if(!this.passwordDialog)
					{
						this.passwordDialog = new GO.dialog.PasswordDialog({
							title:GO.smime ? GO.smime.lang.enterPassword : GO.gnupg.lang.enterPassword,
							fn:function(button, password, passwordDialog){
								if(button=='cancel')
								{
									this.reset();
									this.el.unmask();
								}else
								{									
									this.loadMessage(passwordDialog.data.uid, passwordDialog.data.mailbox, passwordDialog.data.account_id, password);
								}
							},
							scope:this
						});							
					}
					this.passwordDialog.data={
						uid:this.uid,
						mailbox:this.mailbox,
						account_id:this.account_id
					};
					this.passwordDialog.show();
				}else
				{						
					this.setMessage(data);						
					this.el.unmask();
				}
					
				if(data.feedback)
				{
					GO.errorDialog.show(data.feedback);
				}	
                                        
				this.fireEvent('load', options, true, response, data, password);
			}				
			
		});
	},
	
	reset : function(){
		this.data=false;
		this.uid=0;
		
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
		
		this.fireEvent('reset', this);
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
		
		this.filterXssEl = Ext.get('em-filterxss');
		if(this.filterXssEl)
		{
			this.filterXssEl.on('click', function(){
				this.params.filterXSS='true';
				this.params.unblock='true';
				this.loadMessage();
			}, this);
		}

		var acceptInvitationEl = Ext.get('em-icalendar-accept-invitation');
		if(acceptInvitationEl)
		{
			acceptInvitationEl.on('click', function()
			{
				this.processInvitation(1);
			}, this);
		}
		var declineInvitationEl = Ext.get('em-icalendar-decline-invitation');
		if(declineInvitationEl)
		{
			declineInvitationEl.on('click', function()
			{
				this.processInvitation(2);
			}, this);
		}
		var tentativeInvitationEl = Ext.get('em-icalendar-tentative-invitation');
		if(tentativeInvitationEl)
		{
			tentativeInvitationEl.on('click', function()
			{
				this.processInvitation(3);
			}, this);
		}
		var icalDeleteEventEl = Ext.get('em-icalendar-delete-event');
		if(icalDeleteEventEl)
		{
			icalDeleteEventEl.on('click', function()
			{
				this.deleteEvent();
			}, this);
		}
		var icalUpdateEventEl = Ext.get('em-icalendar-update-event');
		if(icalUpdateEventEl)
		{
			icalUpdateEventEl.on('click', function()
			{
				//this.processResponse();
				this.processInvitation();
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
		
		
	},
	
	onAttachmentContextMenu : function (e, target){
		
		
		if(target.id.substr(0,this.attachmentsId.length)==this.attachmentsId)
		{			
			var attachment_no = target.id.substr(this.attachmentsId.length+1);
			
			e.preventDefault();
			var attachment = this.data.attachments[attachment_no];				
			this.attachmentContextMenu.showAt(e.getXY(), attachment);
		}
			
	},
	
	openAttachment :  function(e, target)
	{
		if(target.id.substr(0,this.attachmentsId.length)==this.attachmentsId)
		{
			var attachment_no = target.id.substr(this.attachmentsId.length+1);			
			
			var attachment = this.data.attachments[attachment_no];
			this.fireEvent('attachmentClicked', attachment, this);
		}
	},

	launchAddressContextMenu : function(e, href){
		var queryString = '';
		var email = '';
		var indexOf = href.indexOf('?');
		if(indexOf>-1)
		{
			email = href.substr(7, indexOf-7);
			queryString = href.substr(indexOf+1);
		}else
		{
			email = href.substr(7);
		}

		e.preventDefault();

		GO.email.addressContextMenu.showAt(e.getXY(), email, '', queryString);
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
				this.launchAddressContextMenu(e, href);
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
			
			var href=target.attributes['href'].value;
			
			if(href.substr(0,6)=='mailto')
			{
				this.launchAddressContextMenu(e, href);
			}else if(href.substr(0,3)=='go:')
			{
				e.preventDefault();
				
				var cmd = 'GO.mailFunctions.'+href.substr(3);
				eval(cmd); 
			}else
			{
				if (target.href && target.href.indexOf('#') != -1 && target.pathname == document.location.pathname){
				//internal link, do default
					
				}else
				{
					e.preventDefault();
					this.fireEvent('linkClicked', href);
				}
			}
		}		
	},

	cal_id:0,
	status_id:0,
	created:false,
	updated:false,
	deleted:false,
	declined:false,
	processInvitation : function(status_id)
	{
		this.status_id = status_id || 0;
			
		GO.request({
			url: 'calendar/event/acceptInvitation',
			params: {
				status: this.status_id,
				account_id: this.account_id,
				mailbox: this.mailbox,
				uid: this.uid
			},
			scope: this,
			success: function(options, response, data)
			{
				this.icalendarFeedback = data.feedback;				
					
				this.loadMessage();
			}
		});
	},
	
	deleteEvent : function()
	{		
		if(confirm(GO.email.lang.icalendarDeleteEventConfirm))
		{			
			GO.request({
				url: "calendar/event/delete",
				params: {
					id: this.data.iCalendar.invitation.event_id					
				},
				scope: this,
				success: function(options, response, result)
				{
					if(result.success)
					{
						this.deleted = true;
						this.loadMessage();
					}
				}
			});
		}
	}
});