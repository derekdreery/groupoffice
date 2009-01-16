/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * Params:
 * 
 * linksStore: store to reload after items are linked
 * gridRecords: records from grid to link. They must have a link_id and link_type
 * fromLinks: array with link_id and link_type to link
 */
 
 /**
 * @class GO.dialog.SelectCompany
 * @extends Ext.Window
 * A window to select a number of User-Office user Users.
 * 
 * @cfg {Function} handler A function called when the Add or Ok button is clicked. The grid will be passed as argument.
 * @cfg {Object} scope The scope of the handler
 * 
 * @constructor
 * @param {Object} config The config object
 */
 
GO.addressbook.SelectCompanyDialog = function(config){
	
	Ext.apply(this, config);
	
	  
  this.searchField = new GO.form.SearchField({
		width:320
  });	
		
	this.grid = new GO.addressbook.CompaniesGrid({
		tbar: [
    GO.lang['strSearch']+': ', ' ', this.searchField
    ]});
    
  //dont filter on address lists when selecting
  delete this.grid.store.baseParams.enable_mailings_filter;
		
	this.searchField.store=this.grid.store;
	
	var focusSearchField = function(){
		this.searchField.focus(true);
	};
	
	
	
	
	GO.addressbook.SelectCompanyDialog.superclass.constructor.call(this, {
    layout: 'fit',
    focus: focusSearchField.createDelegate(this),
		modal:false,
		height:400,
		width:600,
		closeAction:'hide',
		title: GO.addressbook.lang['strSelectCompany'],
		items: this.grid,
		buttons: [
			{
				text: GO.lang['cmdOk'],
				handler: function (){
					this.callHandler(true);
				},
				scope:this
			},
			{
				text: GO.lang['cmdAdd'],
				handler: function (){
					this.callHandler(false);
				},
				scope:this
			},
			{
				text: GO.lang['cmdClose'],
				handler: function(){this.hide();},
				scope: this
			}
		]
    });
};

Ext.extend(GO.addressbook.SelectCompanyDialog, Ext.Window, {

	show : function(){
		if(!this.grid.store.loaded)
		{
			this.grid.store.load();
		}
		GO.addressbook.SelectCompanyDialog.superclass.show.call(this);
	},
	
	//private
	callHandler : function(hide){
		if(this.handler)
		{
			if(!this.scope)
			{
				this.scope=this;
			}
			
			var handler = this.handler.createDelegate(this.scope, [this.grid]);
			handler.call();
		}
		if(hide)
		{
			this.hide();
		}
	}	
	
});