		
GO.addressbook.SelectAddressbookWindow = Ext.extend(Ext.Window, {
	
	initComponent : function(){
		this.addEvents({'select' : true});	
		
		
		this.title=GO.addressbook.lang.selectAddressbook;
		
		this.list = new GO.grid.SimpleSelectList({
				store: GO.addressbook.readableAddressbooksStore 
			});
		
		this.list.on('click', function(dataview, index){			
				
				var addressbook_id = dataview.store.data.items[index].id;
			
				this.fireEvent('select', addressbook_id);			
				this.hide();
				
		}, this);
		
		
		this.title= GO.addressbook.lang.selectAddressbook;
		this.layout='fit';
		this.modal=false;
		this.height=400;			
		this.width=400;
		this.closable=true;
		this.closeAction='hide';	
		this.items= this.panel = new Ext.Panel({
			autoScroll:true,
			items: this.list,
			cls: 'go-form-panel'
		});
		this.buttons=[{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}];
		
		GO.addressbook.SelectAddressbookWindow.superclass.initComponent.call(this);
		
	}
});