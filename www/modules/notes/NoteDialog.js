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
 
GO.notes.NoteDialog = Ext.extend(GO.dialog.TabbedFormDialog , {
	
	customFieldType : 4,
	initComponent : function(){
		
		Ext.apply(this, {
			goDialogId:'note',
			title:GO.notes.lang.note,
			formControllerUrl: GO.url('notes/note')
		});
		
		GO.notes.NoteDialog.superclass.initComponent.call(this);	
	},
	afterLoad : function(action){
		this.selectCategory.setRemoteText(action.result.data.category_name);	
	},
	afterLoadNew : function(config){
		if(!config.category_id)
		{
			config.category_id=GO.notes.defaultCategory.id;
			config.category_name=GO.notes.defaultCategory.name;
		}
		this.selectCategory.setValue(config.category_id);
		if(config.category_name)
		{			
			this.selectCategory.setRemoteText(config.category_name);
		}		
	},
	
	buildForm : function () {
		
		this.selectLinkField = new GO.form.SelectLink({
			anchor:'100%'
		});

		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',
			layout:'form',
			items:[{
				xtype: 'textfield',
				name: 'name',
				width:300,
				anchor: '100%',
				maxLength: 100,
				allowBlank:false,
				fieldLabel: GO.lang.strName
			},this.selectCategory = new GO.form.ComboBox({
				fieldLabel: GO.notes.lang.category_id,
				hiddenName:'category_id',
				anchor:'100%',
				emptyText:GO.lang.strPleaseSelect,
				store: GO.notes.writableCategoriesStore,
				pageSize: parseInt(GO.settings.max_rows_list),
				valueField:'id',
				displayField:'name',
				mode: 'remote',
				triggerAction: 'all',
				editable: true,
				selectOnFocus:true,
				forceSelection: true,
				allowBlank: false
			}),
			this.selectLinkField,
			{
				xtype: 'textarea',
				name: 'content',
				anchor: '100% -80',
				hideLabel:true
			}]				
		});

		this.addPanel(this.propertiesPanel);
	}
});