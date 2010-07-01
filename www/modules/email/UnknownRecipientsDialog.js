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


	var action = new Ext.ux.grid.RowActions({
	    header:'',
	    hideMode:'display',
	    keepSelection:true,
	    actions:[{
		iconCls:'btn-add',
		qtip:GO.lang.cmdAdd
	    },{
		iconCls:'btn-edit',
		qtip:GO.lang.cmdEdit
	    }],
	    width: 50
	});

	action.on({
	    action:function(grid, record, action, row, col) {

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
	
		if(action == 'btn-add')
		{		  
		    GO.addressbook.showContactDialog();
		    GO.addressbook.contactDialog.formPanel.form.setValues(record.data);
		}else
		{
		     if(!GO.email.findContactDialog)
		     {
			 GO.email.findContactDialog = new GO.email.FindContactDialog();
		     }
		     
		     GO.email.findContactDialog.show(record.data);
		}

		var store = grid.getStore();
		store.remove(record);

		if(store.getCount()==0)
		{
		    this.hide();
		}
	    },scope:this
	});
				
	this.grid = new GO.grid.GridPanel({
	    store: this.store,
	    autoHeight:true,
	    plugins:action,
	    border:false,
	    loadMask:true,
	    columns : [{
		header : GO.lang.strName,
		dataIndex : 'name'
	    }, {
		header : GO.lang.strEmail,
		dataIndex : 'email'
	    },
	    action],
	    sm : new Ext.grid.RowSelectionModel({
		singleSelect : false
	    }),
	    view : new Ext.grid.GridView({
		forceFit : true,
		autoFill : true
	    })
	});

		
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
		html: GO.email.lang.addUnknownRecipientsText,
		cls:'go-form-panel'
	    }),
	    this.grid
	    ]
	});
		
	GO.email.UnknownRecipientsDialog.superclass.initComponent.call(this);
    }
});
