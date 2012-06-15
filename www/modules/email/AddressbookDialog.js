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
 * 
 * 
 * Params:
 * 
 * linksStore: store to reload after items are linked gridRecords: records from
 * grid to link. They must have a link_id and link_type fromLinks: array with
 * link_id and link_type to link
 */

/**
 * @class GO.dialog.SelectEmail
 * @extends Ext.Window A window to select a number of User-Office user Users.
 * 
 * @cfg {Function} handler A function called when the Add or Ok button is
 *      clicked. The grid will be passed as argument.
 * @cfg {Object} scope The scope of the handler
 * 
 * @constructor
 * @param {Object}
 *            config The config object
 */

GO.email.AddressbookDialog = function(config) {

	Ext.apply(this, config);

	var items = Array();
	
	if (GO.addressbook) {
		this.contactsGrid = new GO.email.ContactsGrid({
			title:GO.addressbook.lang.contacts
		});

		this.contactsGrid.on('show', function() {
			this.contactsGrid.store.load();
		}, this);

		this.companiesStore = new GO.data.JsonStore({
			url : GO.url("addressbook/company/store"),
			baseParams : {
				//task : 'companies',
				require_email:true				
			},
//			root : 'results',
//			id : 'id',
//			totalProperty : 'total',
			fields : ['id', 'name', 'city', 'email', 'phone',
			'homepage', 'address', 'zip'],
			remoteSort : true
		});

		this.companySearchField = new GO.form.SearchField({
			store : this.companiesStore,
			width : 320
		});

		this.companyGrid = new GO.grid.GridPanel({
			title : GO.addressbook.lang.companies,
			paging : true,
			border : false,
			store : this.companiesStore,
			view : new Ext.grid.GridView({
				autoFill : true,
				forceFit : true
			}),
			columns : [{
				header : GO.lang['strName'],
				dataIndex : 'name',
				css : 'white-space:normal;',
				sortable : true
			}, {
				header : GO.lang['strEmail'],
				dataIndex : 'email',
				css : 'white-space:normal;',
				sortable : true
			}],
			sm : new Ext.grid.RowSelectionModel(),
			tbar : [GO.lang['strSearch'] + ': ', ' ',
			this.companySearchField]
		});

		this.companyGrid.on('show', function() {
			this.companiesStore.load();
		}, this);

		items.push(this.contactsGrid);
		items.push(this.companyGrid);

	}

	if (GO.addressbook) {
		this.mailingsGrid = new GO.grid.GridPanel({
			title : GO.addressbook.lang.cmdPanelMailings,
			paging : false,
			border : false,
			store : GO.addressbook.readableAddresslistsStore,
			view : new Ext.grid.GridView({
				autoFill : true,
				forceFit : true
			}),
			columns : [{
				header : GO.lang['strName'],
				dataIndex : 'name',
				css : 'white-space:normal;',
				sortable : true
			}],
			sm : new Ext.grid.RowSelectionModel()
		});
		this.mailingsGrid.on('show', function() {
			if(!GO.addressbook.readableAddresslistsStore.loaded)
				GO.addressbook.readableAddresslistsStore.load();
		}, this);

		items.push(this.mailingsGrid);
	}
	
	/*
	 * this.usersGrid.on('afterRender', function(){
	 * if(this.usersGrid.isVisible()) { this.onShow(); } }, this);
	 */

	this.userGroupsStore = new GO.data.JsonStore({
		url : GO.url('core/groups'),
		baseParams : {
			for_mail : 1
		},
		id : 'id',
		root : 'results',
		fields: ['id', 'name', 'user_id', 'user_name'],
		totalProperty : 'total',
		remoteSort : true
	});

	this.userGroupsGrid = new GO.grid.GridPanel({
		title : GO.email.lang.groups,
		paging : true,
		border : false,
		store : this.userGroupsStore,
		view : new Ext.grid.GridView({
			autoFill : true,
			forceFit : true
		}),
		columns : [{
			header : GO.lang['strName'],
			dataIndex : 'name',
			css : 'white-space:normal;',
			sortable : true
		}, {
			header : GO.lang['strOwner'],
			dataIndex : 'user_name',
			css : 'white-space:normal;',
			sortable : true
		}],
		sm : new Ext.grid.RowSelectionModel()
	});

	this.userGroupsGrid.on('show', function() {
		this.userGroupsStore.load();
	}, this);
	
	items.push(this.userGroupsGrid);

	

	this.tabPanel = new Ext.TabPanel({
		activeTab : 0,
		items : items,
		border : false
	});

	GO.email.AddressbookDialog.superclass.constructor.call(this, {
		layout : 'fit',
		modal : false,
		height : 400,
		width : 600,
		closeAction : 'hide',
		title : GO.addressbook.lang.addressbook,
		items : this.tabPanel,
		buttons : [{
			text : GO.email.lang.addToRecipients,
			handler : function() {
				this.addRecipients('to');
			},
			scope : this
		}, {
			text : GO.email.lang.addToCC,
			handler : function() {
				this.addRecipients('cc');
			},
			scope : this
		}, {
			text : GO.email.lang.addToBCC,
			handler : function() {
				this.addRecipients('bcc');
			},
			scope : this
		}, {
			text : GO.lang['cmdClose'],
			handler : function() {
				this.hide();
			},
			scope : this
		}]
	});

	this.addEvents({
		addrecipients : true
	});
};

Ext.extend(GO.email.AddressbookDialog, Ext.Window, {
	addRecipients : function(field) {
		var str="";
		var activeGrid = this.tabPanel.getLayout().activeItem;
		var selections = activeGrid.selModel.getSelections();
				
		if (this.mailingsGrid && activeGrid == this.mailingsGrid) {
					
			var addresslists = [];
					
			for(var i=0;i<selections.length;i++)
			{
				addresslists.push(selections[i].data.id);
			}					

			GO.request({
				maskEl: this.getEl(),
				url: "addressbook/addresslist/getRecipientsAsString",
				params: {					
					addresslists: Ext.encode(addresslists)
				},
				success: function(options, response, result)
				{					
					this.fireEvent('addrecipients', field, result.recipients);
				},
				scope:this
			});

		}else
		if(activeGrid == this.userGroupsGrid)
		{
			var user_groups = [];

			for(var i=0;i<selections.length;i++)
			{
				user_groups.push(selections[i].data.id);
			}

			this.el.mask(GO.lang.waitMsgLoad);
			GO.request({
				url: "groups/group/getRecipientsAsString",
				params: {
					groups: Ext.encode(user_groups)
				},
				success: function(options, response, result)
				{
					this.fireEvent('addrecipients', field, result.recipients);
					this.el.unmask();
				},
				scope:this
			});
		}else
		/*if(activeGrid == this.addressbooksGrid)
		{
			var addressbooks = [];

			for(var i=0;i<selections.length;i++)
			{
				addressbooks.push(selections[i].data.id);
			}

			this.el.mask(GO.lang.waitMsgLoad);
			Ext.Ajax.request({
				url: GO.settings.modules.addressbook.url+'json.php',
				params: {
					task:'addressbooks_string',
					addressbooks: addressbooks.join(',')
				},
				callback: function(options, success, response)
				{
					str = response.responseText;
					this.fireEvent('addrecipients', field, str);
					this.el.unmask();
				},
				scope:this
			});
		}
		else*/
		{
			var emails = [];

			for (var i = 0; i < selections.length; i++) {
				emails.push('"' + selections[i].data.name + '" <'
					+ selections[i].data.email + '>');
			}
					
			str=emails.join(', ');
			this.fireEvent('addrecipients', field, str);
		}
				
	}
});
