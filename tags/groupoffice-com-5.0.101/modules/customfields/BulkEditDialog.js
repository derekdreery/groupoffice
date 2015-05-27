/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

 
GO.customfields.BulkEditDialog = function(config){
	
	if(!config)
	{
		config={};
	}

	this.buildForm();

	config.maximizable=true;
	config.layout='fit';
	config.modal=true;
	config.resizable=false;
	config.width=700;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.customfields.lang.bulkEdit;
	config.items= [
		this.formPanel
	];
	config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.submitForm();
		},
		scope:this
	},{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}];
	

	
	GO.customfields.BulkEditDialog.superclass.constructor.call(this, config);
	
	this.addEvents({'change':true});
	
}

Ext.extend(GO.customfields.BulkEditDialog, Ext.Window,{
	
	show : function (selectedFiles) {
		if(!this.rendered)
			this.render(Ext.getBody());

		this.setSelectedFileIds(selectedFiles);

		GO.customfields.FieldDialog.superclass.show.call(this);
	},

	setSelectedFileIds : function(selectedFiles) {
		if(typeof(selectedFiles)=='undefined')
			selectedFiles = new Array();
		this.selected_file_ids = new Array();
		for (var i=0; i<selectedFiles.length; i++)
			this.selected_file_ids.push(selectedFiles[i].id);
	},

	submitForm : function() {
		this.formPanel.form.submit({
			url:GO.settings.modules.customfields.url+'action.php',
			params: {
				'task' : 'bulk_edit',
				'selected_file_ids' : Ext.encode(this.selected_file_ids)
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				this.hide();
				Ext.Msg.alert(GO.customfields.lang.success, GO.customfields.lang.appliedToSelection);
			},
			failure: function(form, action) {
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang['strErrorsInForm'];
				}else
				{
					error = action.result.feedback;
				}



				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope:this

		});
	},

	buildForm : function()
	{
		var checkBox,formField;
		var tabPanelItems = new Array();
		for (var i=0; i<GO.customfields.file_categories.length; i++) {
			tabPanelItems[GO.customfields.file_categories[i].id.toString()] = new Ext.Panel({
				title: GO.customfields.file_categories[i].name,
				items: [{
					xtype: 'hidden',
					name: 'category_id',
					value: GO.customfields.file_categories[i].id
				}],
				bodyStyle: 'padding:5px;',
				labelWidth: 70,
				layout: 'form'
			});
			tabPanelItems[GO.customfields.file_categories[i].id.toString()].add(new Ext.form.Label({
				text: GO.customfields.lang.applyToSelectionInstructions,
				width: '95%'
			}));
		}
		
		for(var i=0;i<GO.customfields.types["6"].panels.length;i++)
		{
			var cfPanel = GO.customfields.types["6"].panels[i];

			for(var n=0;n<cfPanel.customfields.length;n++)
			{
				formField = GO.customfields.getFormField(cfPanel.customfields[n]);
				formField.flex=12;
				delete formField.width;
				delete formField.anchor;

				if (formField.xtype=='xcheckbox') formField.hideLabel = true;
				//if (formField.xtype=='html') formField.width = '300';

				checkBox = new Ext.form.Checkbox({
					flex:1,
					checked: false,
					name: 'col_'+cfPanel.customfields[n].id.toString()+'_checked'
				});
				if (typeof(formField)!='undefined')
					tabPanelItems[cfPanel.category_id.toString()].add(new Ext.form.CompositeField({
						anchor:'-20',
						items: [formField,checkBox]
					}));
			}

		}

		this.tabPanels = new Array();

		for (var i=0; i<tabPanelItems.length; i++) {
			if (typeof(tabPanelItems[i])!='undefined')
				this.tabPanels.push(tabPanelItems[i]);
		}

		this.tabPanel = new Ext.TabPanel({
			activeTab: 0,
			deferredRender: false,
			border: false,
			items: this.tabPanels,
			anchor: '100% 100%'
		});

		this.formPanel = new Ext.form.FormPanel({
			waitMsgTarget:true,
			url: GO.settings.modules.notes.url+'action.php',
			border: false,
			baseParams: {
				task: 'bulk_edit'
			},
			items:this.tabPanel
		});
	}	
});