GO.email.LinkedMessagePanel = Ext.extend(GO.email.MessagePanel,{
	initComponent : function(){
		this.tbar=[{
					iconCls: 'btn-print',
					text: GO.lang.cmdPrint,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.body.print();
					},
					scope: this
				},
				'-',
				{
					iconCls: 'btn-reply',
					text: GO.email.lang.reply,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.remoteMessage.task='reply';
						GO.email.showComposer({
							loadUrl : GO.settings.modules.email.url + 'json.php',
							loadParams : this.remoteMessage
						});
					},
					scope: this
				},
				{
					iconCls: 'btn-reply-all',
					text: GO.email.lang.replyAll,
					cls: 'x-btn-text-icon',
					handler: function(){
						this.remoteMessage.task='reply_all';
						GO.email.showComposer({
							loadUrl : GO.settings.modules.email.url + 'json.php',
							loadParams : this.remoteMessage
						});
					},
					scope: this
				},
				{
					iconCls: 'btn-forward',
					text: GO.email.lang.forward,
					cls: 'x-btn-text-icon',
					handler: function(){

						this.remoteMessage.task='forward';

						GO.email.showComposer({
							loadUrl : GO.settings.modules.email.url + 'json.php',
							loadParams : this.remoteMessage
						});
					},
					scope: this
				},{
					iconCls: 'btn-edit',
					text: GO.lang.cmdEdit,
					handler: function(){
						this.remoteMessage.task='opendraft';
						GO.email.showComposer({
							loadUrl : GO.settings.modules.email.url + 'json.php',
							loadParams : this.remoteMessage,
							saveToPath:this.remoteMessage.path
						});

						//this.ownerCt.hide();
					},
					scope: this
				}];

		GO.email.LinkedMessagePanel.superclass.initComponent.call(this);
	},
	border:false,
	autoScroll:true,
	editHandler : function(){
		//needed because it needs to be compatible with javascript/DisplayPanel.js
	},
	load : function(id){

		this.el.mask(GO.lang.strWaitMsgLoad);

		if(!this.remoteMessage)
			this.remoteMessage={};

		if(id)
			this.messageId=id;
		
		this.remoteMessage.id=this.messageId;

		var url = '';
		if(this.remoteMessage.account_id)
		{
			this.remoteMessage.task='message_attachment';
			url = GO.settings.modules.email.url+'json.php';
		}else
		{
			this.remoteMessage.task='linked_message';
			url = GO.settings.modules.mailings.url+'json.php';
		}


		Ext.Ajax.request({
			url: url,
			params: this.remoteMessage,
			scope: this,
			callback: function(options, success, response)
			{
				var data = Ext.decode(response.responseText);
				this.setMessage(data);
				this.el.unmask();
			}
		});
	},
	listeners:{
		scope:this,
		linkClicked: function(href){
			var win = window.open(href);
			win.focus();
		},
		attachmentClicked: function(attachment, panel){
			if(attachment.type=='message')
			{
				this.remoteMessage.part_number=attachment.number+".0";
				GO.linkHandlers[9].call(panel, panel.messageId, panel.remoteMessage);
			}else
			{
				if(panel.data.path)
				{
					document.location.href=GO.settings.modules.email.url+
					'mimepart.php?path='+
					encodeURIComponent(panel.data.path)+'&part_imap_id='+attachment.imap_id;
				}else
				{
					document.location.href=GO.settings.modules.email.url+
					'mimepart.php?uid='+panel.remoteMessage.uid+'' +
					'&account_id='+panel.remoteMessage.account_id+'' +
					'&encoding='+panel.remoteMessage.encoding+'' +
					'&mailbox='+encodeURIComponent(panel.remoteMessage.mailbox)+'' +
					'&imap_id='+panel.remoteMessage.imap_id+'' +
					'&part_imap_id='+attachment.imap_id;
				}
			}
		}
	}

});

