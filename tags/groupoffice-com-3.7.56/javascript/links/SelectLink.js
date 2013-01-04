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
 
GO.form.SelectLink = function(config){
	
	config = config || {};
	
	config.store = new GO.data.JsonStore({				
		url: BaseHref+'json.php',
		baseParams: {
			query: '',
			task:'links'
		},
		root: 'results',
		totalProperty: 'total',
		fields:['link_id','link_type','link_and_type', 'type_name'],

		remoteSort: true
				
	});

	config.forceSelection=true;
	config.displayField='type_name';
	config.valueField='link_and_type',
	config.hiddenName='link';
	config.triggerAction='all';
	config.width=400;
	config.selectOnFocus=false;
	config.fieldLabel=GO.lang.cmdLink;
	config.pageSize=20;//parseInt(GO.settings['max_rows_list']);
	GO.form.SelectLink.superclass.constructor.call(this, config);
	
}

Ext.extend(GO.form.SelectLink, GO.form.ComboBoxReset,{
	onTriggerClick : function(){

		if(!GO.selectLinkDialog){
			GO.selectLinkDialog = new GO.dialog.LinksDialog({
				singleSelect:true,
				selectLinkField:this,
				linkItems : function()	{
					var selectionModel = this.grid.searchGrid.getSelectionModel();
					var record = selectionModel.getSelected();

					this.selectLinkField.setValue(record.get('link_and_type'));
					this.selectLinkField.setRemoteText(record.get('type_name'));
					this.hide();
				}
			});
		}
		GO.selectLinkDialog.show();
		
	}
});