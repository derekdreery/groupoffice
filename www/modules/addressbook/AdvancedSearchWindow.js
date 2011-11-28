/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

/**
 * Wilmar: adapted from Merijn's modules/filesearch/MainPanel.js
 */

GO.addressbook.AdvancedSearchWindow = function(config){

	config = config || {};

	this.buildForm();

	config.title = GO.addressbook.lang.advancedSearch;
	//config.closable=true;
//	config.width=400;
//	config.height=600;
	config.border=false;
	config.collapsible=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=true;
	config.width=600;
	config.height=350;
	config.closeAction='hide';
	config.items = [this.formPanel];
	config.buttons=[{
		text: GO.addressbook.lang.executeQuery,
		handler: function(){
			this.search();
//			this.hide();
		},
		scope: this
	},{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}
	];

	GO.addressbook.AdvancedSearchWindow.superclass.constructor.call(this,config);

}

Ext.extend(GO.addressbook.AdvancedSearchWindow, Ext.Window, {
	
	/*
	 * Sets whether, during the time of use of this window, the data type is
	 * 'contact' or 'company', and apply the ensuing changes to this window.
	 * Made to be called from this.show(), but external calls also possible.
	 */
	updateDataType : function(type,masterPanel) {
		if (type!='companies' && type!='contacts')
			Ext.MessageBox.alert(GO.lang.strWarning,"AdvancedSearchWindow.updateDataType() parameter must be either 'contacts' or 'companies'.");
		
		this._dataType = type;
		this.queryPanels.items.itemAt(0).setDisabled(type!='contacts'); // item 0 is the contactsQueryPanel
		this.queryPanels.items.itemAt(0).setVisible(type=='contacts')
		this.queryPanels.items.itemAt(1).setDisabled(type!='companies'); // item 1 is the companiesQueryPanel
		this.queryPanels.items.itemAt(1).setVisible(type=='companies');
		
		if (this._dataType=='contacts') {
			this.externalTargetGrid = masterPanel.contactsGrid;
			this.activeQueryPanel = this.queryPanels.items.itemAt(0);
		} else {
			this.externalTargetGrid = masterPanel.companiesGrid;
			this.activeQueryPanel = this.queryPanels.items.itemAt(1);
		}
	},

	getDatatype : function() {
		if (typeof(this._datatype)=='undefined')
			return false;
		return this._datatype;
	},
	
	_getModelName : function() {
		switch (this.getDataType) {
			case 'contact':
				return 'GO_Addressbook_Model_Contact';
				break;
			case 'company':
				return 'GO_Addressbook_Model_Company';
				break;
			default:
				return false;
				break;
		}
	},
	
	buildForm : function() {
		this.queryPanels = new Ext.Panel({
			border: false,
			width: '100%',
			height: '100%',
			items: [
				this._contactsQueryPanel = new GO.query.QueryPanel({
					region:'center',
					modelName:'GO_Addressbook_Model_Contact',
					modelAttributesUrl:GO.url('addressbook/contact/attributes'),
					disabled: true,
					width: 600,
					height: 350
				}),
				this._companiesQueryPanel = new GO.query.QueryPanel({
					region:'center',
					modelName:'GO_Addressbook_Model_Company',
					modelAttributesUrl:GO.url('addressbook/company/attributes'),
					disabled: true,
					width: 600,
					height: 350
				})
			]
		});
		
		this.formPanel = new Ext.form.FormPanel({
			split:true,
			width: '100%',
			height: '100%',
			border: false,
			items: [{
				layout:'form',
//				title: GO.adressbook.lang.advancedSearch,
				items:[this.queryPanels]
				}
			]
		})
	},
	
	show : function(config) {
		this.updateDataType(config.dataType,config.masterPanel);
		GO.addressbook.AdvancedSearchWindow.superclass.show.call(this,config);
	},
	
	search : function(){
		//checkbox values are only returned when ticked
		delete this.externalTargetGrid.store.baseParams.search_current_folder;
		this.externalTargetGrid.store.baseParams['advancedQueryData'] = Ext.encode(this.activeQueryPanel.getGridData());
		this.externalTargetGrid.store.load();
		this.externalTargetGrid.setDisabled(false);
		this.fireEvent('ok', this);
	},
	
	reset : function(){
		this.externalTargetGrid.setDocumentBundle(false);
		this.externalTargetGrid.store.removeAll();
		this.externalTargetGrid.setDisabled(true);
//		this.setTitle(GO.filesearch.lang.filesearch);
		this.externalTargetGrid.exportTitle=GO.lang.strSearch;
	}
	
});