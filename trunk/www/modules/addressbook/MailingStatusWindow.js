GO.addressbook.MailingStatusWindow = function(config){

	config = config || {};

	config.title=GO.addressbook.lang.sentMailings;
	config.id='ml-sent-mailings';

	config.width=770;
	config.height=500;

	config.layout='fit';

	config.items=this.sentMailingsGrid = new GO.addressbook.SentMailingsGrid();

	config.tbar = [{
				iconCls: 'ml-btn-mailings',
				text: GO.addressbook.lang.sendMailing,
				cls: 'x-btn-text-icon',
				handler: function(){
					if(!this.selectAddresslistWindow)
					{
						this.selectAddresslistWindow=new GO.addressbook.SelectAddresslistWindow();
						this.selectAddresslistWindow.on("select", function(win, addresslist_id){
							var composer = GO.email.showComposer({addresslist_id:addresslist_id});
							composer.on('hide', function(){
								this.sentMailingsGrid.store.load();
							}, this, {single:true});
						}, this);
					}
					this.selectAddresslistWindow.show();
				},
				scope: this
			}];
	

	GO.addressbook.MailingStatusWindow.superclass.constructor.call(this, config);
}

Ext.extend(GO.addressbook.MailingStatusWindow, GO.Window);