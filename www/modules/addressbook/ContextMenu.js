GO.addressbook.ContextMenu = function(config){

	if(!config)
		config = {};

	if(!config.type)
		config.type='contact';

	config.items=[];

	if (GO.email) {
		this.actionCreateMail = new Ext.menu.Item({
			iconCls: 'btn-email',
			text:GO.addressbook.lang.createEmailSelected,
			cls: 'x-btn-text-icon',
			scope:this,
			handler: function()
			{
				var selected = this.getSelected();
				
				if(!GO.util.empty(selected[0].data.id)){
					
					if(config.type == 'contact')
						this.showCreateMailDialog({contact_id:selected[0].data.id});
					else
						this.showCreateMailDialog({company_id:selected[0].data.id});
				}else{
					this.showCreateMailDialog();
				}
			}
		});
		config.items.push(this.actionCreateMail);
	}
	
	this.actionBatchEdit = new Ext.menu.Item({
		iconCls: 'btn-settings',
		text: GO.lang.batchEdit,
		cls: 'x-btn-text-icon',
		scope:this,
		handler: function()
		{
			this.showBatchEditDialog();
		}
	});
	config.items.push(this.actionBatchEdit);
	
	GO.addressbook.ContextMenu.superclass.constructor.call(this,config);

}

Ext.extend(GO.addressbook.ContextMenu, Ext.menu.Menu, {
	model_name : '',
	selected  : [],
	grid : '',

	setSelected : function (grid, model_name) {
		this.selected = grid.selModel.getSelections();
		this.model_name=model_name;
		this.grid = grid;
	},

	getSelected : function () {
		if (typeof(this.selected)=='undefined')
			return [];
		else
			return this.selected;
	},

	showCreateMailDialog : function(config) {		
		if (GO.email) {
			
			if(!config)
				config = {};
			
			var emails = [];
			var selected = this.getSelected();
			for (var i = 0; i < selected.length; i++) {
				if (typeof(selected[i].data.email)=='string' && !GO.util.empty(selected[i].data.email))
					emails.push('"' + selected[i].data.name + '" <' + selected[i].data.email + '>');
			}

			if (emails.length>0)
				var str = emails.join(', ');
			else
				var str = '';

			Ext.apply(config, {
				account_id: GO.moduleManager.getPanel('email').account_id,
				values:{
					to: str
				}				
			});

			GO.email.showComposer(config);
//			GO.email.showComposer({
//				account_id: GO.moduleManager.getPanel('email').account_id,
//				values:{
//					to: str
//				}				
//			});
		}
	},
	
	showBatchEditDialog : function() {
		var ids = [];
		var selected = this.getSelected();
		for (var i = 0; i < selected.length; i++) {
			if (!GO.util.empty(selected[i].data.id))
				ids.push(selected[i].data.id);
		}
		
		GO.base.model.showBatchEditModelDialog(this.model_name, ids, this.grid, {
			sex:GO.addressbook.SexCombobox,
			company_id:GO.addressbook.SelectCompany
		},['uuid']);
	}
});