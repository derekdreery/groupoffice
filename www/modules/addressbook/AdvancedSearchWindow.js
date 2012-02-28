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
 */

GO.addressbook.AdvancedSearchWindow = function(config){

	config = config || {};

	config.title = GO.addressbook.lang.advancedSearch;
	//config.closable=true;
//	config.width=400;
//	config.height=600;
	config.border=false;
	config.collapsible=true;
	config.layout='card';
		config.layoutConfig={
			deferredRender:false,
			layoutOnCardChange:true
		};
	config.modal=false;
	config.resizable=true;
	config.width=850;
	config.height=350;
	config.closeAction='hide';
	config.items = [{
			layout:'border',
			items:[this._contactsQueryPanel = new GO.query.QueryPanel({
					region:'center',
					modelName:'GO_Addressbook_Model_Contact',
					modelAttributesUrl:GO.url('addressbook/contact/attributes')
				}), this._contactsQueriesGrid = new GO.query.SavedQueryGrid({
					region: 'west',
					width:200,
					split:true,
					queryPanel: this._contactsQueryPanel,
					modelName:'GO_Addressbook_Model_Contact'
				})]
		},{
			layout:'border',
			items:[this._companiesQueryPanel = new GO.query.QueryPanel({			
					region:'center',
					layout:'fit',
					modelName:'GO_Addressbook_Model_Company',
					modelAttributesUrl:GO.url('addressbook/company/attributes')
				}), this._companiesQueriesGrid = new GO.query.SavedQueryGrid({
					region: 'west',
					width:200,
					split:true,
					queryPanel: this._companiesQueryPanel,
					modelName:'GO_Addressbook_Model_Company'
				})
			]
		}];
		
		this._contactsQueriesGrid.on('rowdblclick',function(grid,rowId,e){
			var record = grid.store.getAt(rowId);
			this.queryId = record.data.id;
			this._contactsQueryPanel.setCriteriaStore(record);
			this._contactsQueryPanel.titleField.setValue('<b>'+record.data.name+'</b>');
		},this);
		
		this._companiesQueriesGrid.on('rowdblclick',function(grid,rowId,e){
			var record = grid.store.getAt(rowId);
			this.queryId = record.data.id;
			this._companiesQueryPanel.setCriteriaStore(record);
			this._companiesQueryPanel.titleField.setValue('<b>'+record.data.name+'</b>');
		},this);
		
		config.buttons=[{
			text: GO.lang['cmdSave'],
			handler: function(){
				this.showSavedQueryDialog(this.queryId,this._getModelName());
			},
			scope: this
		},{
			text: GO.lang.executeQuery,
			handler: function(){
				this.search();
			},
			scope: this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}];

	GO.addressbook.AdvancedSearchWindow.superclass.constructor.call(this,config);

}

Ext.extend(GO.addressbook.AdvancedSearchWindow, GO.Window, {
	
	queryId : 0,
	
	/*
	 * Sets whether, during the time of use of this window, the data type is
	 * 'contact' or 'company', and apply the ensuing changes to this window.
	 * Made to be called from this.show(), but external calls also possible.
	 */
	updateDataType : function(type,masterPanel) {
		if (type!='companies' && type!='contacts')
			Ext.MessageBox.alert(GO.lang.strWarning,"AdvancedSearchWindow.updateDataType() parameter must be either 'contacts' or 'companies'.");
		
		if (type=='contacts')
			this.getLayout().setActiveItem(0);
		else
			this.getLayout().setActiveItem(1);

		this._datatype = type;
	
		if (this._datatype=='contacts') {
			this.externalTargetGrid = masterPanel.contactsGrid;
		} else {
			this.externalTargetGrid = masterPanel.companiesGrid;
		}
	},

	getDatatype : function() {
		if (typeof(this._datatype)=='undefined')
			return false;		
		return this._datatype;
	},
	
	_getModelName : function() {
		switch (this.getDatatype()) {
			case 'contacts':
				return 'GO_Addressbook_Model_Contact';
				break;
			case 'companies':
				return 'GO_Addressbook_Model_Company';
				break;
			default:
				return false;
				break;
		}
	},
	
	show : function(config) {
		GO.addressbook.AdvancedSearchWindow.superclass.show.call(this,config);
		this.updateDataType(config.dataType,config.masterPanel);
	},
	
	search : function(){
		//checkbox values are only returned when ticked
		delete this.externalTargetGrid.store.baseParams.search_current_folder;
		
		if (this.getDatatype()=='contacts')
			this.externalTargetGrid.store.baseParams['advancedQueryData'] = Ext.encode(this._contactsQueryPanel.getGridData());
		else
			this.externalTargetGrid.store.baseParams['advancedQueryData'] = Ext.encode(this._companiesQueryPanel.getGridData());
		
		this.externalTargetGrid.store.load();
		this.externalTargetGrid.setDisabled(false);
		this.fireEvent('ok', this);
	},
	
	reset : function(){
		this.externalTargetGrid.store.removeAll();
		this.externalTargetGrid.setDisabled(true);
//		this.setTitle(GO.filesearch.lang.filesearch);
		this.externalTargetGrid.exportTitle=GO.lang.strSearch;
	},
	
	showSavedQueryDialog : function(modelId,modelName) {
		if (GO.util.empty(GO.query.savedQueryDialog))
			GO.query.savedQueryDialog = new GO.query.SavedQueryDialog();
		
		GO.query.savedQueryDialog.show(
			modelId, {
				'model_name' : modelName
			}
		);
	}
	
});