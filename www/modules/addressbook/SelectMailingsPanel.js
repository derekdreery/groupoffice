GO.addressbook.SelectAddresslistPanel = Ext.extend(Ext.Panel, {
	
	addresslistElements : [],

	hideAllowCheck : false,
	
	initComponent : function(){
		
		this.title=GO.addressbook.lang.cmdPanelAddresslist;
		this.cls='go-form-panel';
		this.autoScroll=true;
		
		this.items=[];

		if(!this.hideAllowCheck){
			this.items.push(new Ext.form.Checkbox({
					boxLabel: GO.addressbook.lang.sendingEmailAllowed,
					labelSeparator: '',
					name: 'email_allowed',
					autoCreate:  { tag: "input", type: "checkbox", autocomplete: "off", value: '1' },
					checked: true
				}));
		}

		this.items.push(new GO.form.HtmlComponent({html:'<br /><h1>'+GO.addressbook.lang.enabledMailingGroups+'</h1>'}));

		

		GO.addressbook.SelectAddresslistPanel.superclass.initComponent.call(this);

	},
	
	removeComponents : function(){
			var f = this.ownerCt.ownerCt.form;
			for(var i=0;i<this.addresslistElements.length;i++)
			{
				f.remove(this.addresslistElements[i]);
				this.remove(this.addresslistElements[i], true);
			}
			this.addresslistElements=[];
		},
	
	loadComponents : function(){
		
		this.removeComponents();

		var f = this.ownerCt.ownerCt.form;
		
		for(var i=0;i<GO.addressbook.writableAddresslistStore.data.items.length;i++)
		{
			var record = GO.addressbook.writableAddresslistStore.data.items[i];
			
			this.addresslistElements.push(new Ext.form.Checkbox({
				boxLabel: record.data.name,
				labelSeparator: '',
				name: 'addresslists[]',
				autoCreate:  { tag: "input", type: "checkbox", autocomplete: "off", value: record.data.id },
				value:false
			}));
			
			this.add(this.addresslistElements[i]);
			f.add(this.addresslistElements[i]);
		}
		this.doLayout();
		
	},
	afterRender : function(){
		GO.addressbook.SelectAddresslistPanel.superclass.afterRender.call(this);

		if(GO.addressbook.writableAddresslistStore.loaded){
			this.loadComponents();
		}else
		{
			this.disabled=true;
		}

		GO.addressbook.writableAddresslistStore.on('load', function(){
			this.loadComponents();
			this.setDisabled(false);
		}, this);
	}
});

