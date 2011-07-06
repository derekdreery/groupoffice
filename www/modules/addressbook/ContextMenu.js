GO.addressbook.ContextMenu = function(config){

	if(!config)
	{
		config = {};
	}

	config.items=[];

	if (GO.email) {
		this.actionCreateMail = new Ext.menu.Item({
			iconCls: 'btn-email',
			text:GO.addressbook.lang.createEmailSelected,
			cls: 'x-btn-text-icon',
			scope:this,
			handler: function()
			{
				this.showCreateMailDialog();
			}
		});
		config.items.push(this.actionCreateMail);
	}

	GO.addressbook.ContextMenu.superclass.constructor.call(this,config);

}

Ext.extend(GO.addressbook.ContextMenu, Ext.menu.Menu, {

	setSelected : function (selected) {
		this.selected = selected;
	},

	getSelected : function () {
		if (typeof(this.selected)=='undefined')
			return [];
		else
			return this.selected;
	},

	showCreateMailDialog : function() {
		if (GO.email) {
			var emails = [];
			var selected = this.getSelected();
			for (var i = 0; i < selected.length; i++) {
				if (typeof(selected[i].data.email)=='string')
					emails.push('"' + selected[i].data.name + '" <' + selected[i].data.email + '>');
			}

			if (emails.length>0)
				var str = emails.join(', ');
			else
				var str = '';

			var composer = GO.email.showComposer({
				account_id: GO.moduleManager.getPanel('email').account_id
			});

			composer.setRecipients('to',str);
		}
	}
});