GO.mailings.MailingStatusWindow = function(config){

	config = config || {};

	config.title=GO.mailings.lang.sentMailings;
	config.id='ml-sent-mailings';

	config.width=770;
	config.height=500;

	config.layout='fit';

	config.items=this.sentMailingsGrid = new GO.mailings.SentMailingsGrid();

	config.tbar = [{
				iconCls: 'ml-btn-mailings',
				text: GO.addressbook.lang.sendMailing,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!this.selectMailingGroupWindow)
					{
						this.selectMailingGroupWindow=new GO.mailings.SelectMailingGroupWindow();
						this.selectMailingGroupWindow.on("select", function(win, mailing_group_id){
							var composer = GO.email.showComposer({mailing_group_id:mailing_group_id});
							composer.on('hide', function(){
								this.sentMailingsGrid.store.load();
							}, this, {single:true});
						}, this);
					}
					this.selectMailingGroupWindow.show();
				},
				scope: this
			}];
	

	GO.mailings.MailingStatusWindow.superclass.constructor.call(this, config);
}

Ext.extend(GO.mailings.MailingStatusWindow, GO.Window);