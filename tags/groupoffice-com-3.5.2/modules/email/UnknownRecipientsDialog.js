/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 		
GO.email.UnknownRecipientsDialog = Ext.extend(Ext.Window, {
	
	initComponent : function(){
		
		this.store = new GO.data.JsonStore({
			root: 'recipients',
			fields:['email','name', 'first_name', 'middle_name', 'last_name']
		});
		
		this.list = new GO.grid.SimpleSelectList({store: this.store});
		
		this.list.on('click', function(dataview, index){				
				var record = dataview.store.data.items[index];
				
				if(!GO.addressbook.contactDialog)
				{
					GO.addressbook.contactDialog = new GO.addressbook.ContactDialog();
				}
				
				var email = record.data.email;
				var tldi = email.lastIndexOf('.');
				if(tldi)
				{
					var tld = email.substring(tldi+1, email.length).toUpperCase();	
					if(GO.lang.countries[tld])
					{
						record.data.country=tld;
					}
				}
				
				GO.addressbook.showContactDialog();
				GO.addressbook.contactDialog.formPanel.form.setValues(record.data);
				
				this.store.remove(record);
				
				if(this.store.getCount()==0)
					this.hide();
		}, this);
		
		
		this.title= GO.email.lang.addUnknownRecipients;
		this.layout='fit';
		this.modal=false;
		this.height=400;			
		this.width=600;
		this.closable=true;
		this.closeAction='hide';	
		this.items= new Ext.Panel({
			autoScroll:true,
			items: [
				new Ext.Panel({
					border: false, 
					html: GO.email.lang.addUnknownRecipientsText
				}),			
				this.list
				],
			cls: 'go-form-panel'
		});
		
		GO.email.UnknownRecipientsDialog.superclass.initComponent.call(this);
	}
});