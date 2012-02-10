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
						GO.email.showComposer({
							task:'reply',
							loadParams : {
								path:this.data.path
							}
						});
					},
					scope: this
				},
				{
					iconCls: 'btn-reply-all',
					text: GO.email.lang.replyAll,
					cls: 'x-btn-text-icon',
					handler: function(){
						GO.email.showComposer({
							task:'reply_all',
							loadParams : {
								path:this.data.path
							}
						});
					},
					scope: this
				},
				{
					iconCls: 'btn-forward',
					text: GO.email.lang.forward,
					cls: 'x-btn-text-icon',
					handler: function(){						
						GO.email.showComposer({
							task:'forward',
							loadParams : {
								path:this.data.path
							}
						});
					},
					scope: this
				},{
					iconCls: 'btn-edit',
					text: GO.lang.cmdEdit,
					handler: function(){
						var composer = GO.email.showComposer({
							task:'opendraft',
							loadParams : {
								path:this.data.path
							},
							saveToPath:this.data.path
						});
						
						composer.on('hide', this.reload, this, {single:true});
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
	loadUrl: '',
	reload : function (){
		this.load(this.lastId, this.lastConfig);	
	},
	load : function(id, config){

	 config = config || {};
	 
	 this.lastConfig=config;
	 this.lastId=id;
	 
		this.el.mask(GO.lang.strWaitMsgLoad);

		if(!this.remoteMessage)
			this.remoteMessage={};


		this.messageId=id;		
		this.remoteMessage.id=this.messageId;

		this.loadUrl = '';
		switch(config.action){
			
			case 'path':
				this.loadUrl=GO.url("savemailas/linkedEmail/loadPath");
			break;
			
			case 'attachment':
				this.loadUrl = GO.url("email/message/messageAttachment");
				break;
				
			case 'file':
				this.loadUrl=GO.url("savemailas/linkedEmail/loadFile");
				break;
				
			default:
				this.loadUrl=GO.url("savemailas/linkedEmail/loadLink");
				
				break;
			
		}

		Ext.Ajax.request({
			url: this.loadUrl,
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
				// TODO: Replace GO.linkHandlers[9] index with model name of table
				// em_links, when that is created.
				GO.email.showMessageAttachment(0, panel.remoteMessage);
			}else
			{
				window.open(attachment.url);
				
//				if(panel.data.path)
//				{
//					document.location.href=GO.settings.modules.email.url+
//					'mimepart.php?path='+
//					encodeURIComponent(panel.data.path)+'&part_imap_id='+attachment.imap_id;
//				}else
//				{
//					document.location.href=GO.settings.modules.email.url+
//					'mimepart.php?uid='+panel.uid+'' +
//					'&account_id='+panel.remoteMessage.account_id+'' +
//					'&encoding='+panel.remoteMessage.encoding+'' +
//					'&mailbox='+encodeURIComponent(panel.remoteMessage.mailbox)+'' +
//					'&imap_id='+panel.remoteMessage.imap_id+'' +
//					'&part_imap_id='+attachment.imap_id;
//				}
			}
		}
	}

});

