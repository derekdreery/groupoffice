		
GO.addressbook.SelectAddresslistWindow = Ext.extend(Ext.Window, {
	
	initComponent : function(){
		
		this.title=GO.addressbook.lang.selectMailingGroup;
		
		this.list = new GO.grid.SimpleSelectList({
				store: GO.addressbook.readableAddresslistsStore
			});
		
		this.list.on('click', function(dataview, index){			
				
				var addresslist_id = dataview.store.data.items[index].id;
			
				this.fireEvent("select", this, addresslist_id);
				this.list.clearSelections();
				this.hide();
				
		}, this);
		
		
		this.title= GO.addressbook.lang.selectAddresslist;
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
		
		GO.addressbook.SelectAddresslistWindow.superclass.initComponent.call(this);
		
		this.addEvents({"select":true});
	},
	
	show : function(){		
		if(!this.list.store.loaded)
		{
			this.list.store.load({
				callback:function(){
					this.show();
				},
				scope:this
			});
			return false;
		}
		
		GO.addressbook.SelectAddresslistWindow.superclass.show.call(this);
		
		if(this.list.store.getCount()==0)
		{
			this.panel.body.update(GO.addressbook.lang.noMailingGroups);
		}
	}
});
