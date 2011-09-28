/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: ExportDialog.js 7982 2011-08-29 14:38:37Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.ExportDialog = Ext.extend(GO.Window , {
	
	documentTitle : '',
	name : '',
	url : '',

	initComponent : function(){

		this.hiddenDocumentTitle = new Ext.form.Hidden({
			name:'documentTitle'
		});
		this.hiddenName = new Ext.form.Hidden({
			name:'name'
		});
		this.hiddenUrl = new Ext.form.Hidden({
			name:'url'
		});
	
		this.radioGroup = new Ext.form.RadioGroup({
			fieldLabel : 'Type',
			name       : 'exportFormat',
			columns: 1,
			items: []
		});
		
		this.includeHidden = new Ext.form.Checkbox({
			fieldLabel : 'Export hidden columns too',
			name       : 'includeHidden'
		});
		
		this.exportOrientation = new Ext.form.ComboBox({
			fieldLabel : 'Orientation',
			name: 'exportOrientation',
			mode: 'local',
			editable:false,
			triggerAction:'all',
			width: 120,
			value:"H",
			store: new Ext.data.ArrayStore({
				id:"id",
				fields: [
						'id',
						'displayText'
				],
				data: [['H', 'Horizontaal'], ['V', 'Verticaal']]
			}),
			valueField: 'id',
			displayField: 'displayText'
		});
		
		this.formPanel = new Ext.form.FormPanel({
			url:GO.url(this.url),
			standardSubmit:true,
			waitMsgTarget:true,			
			border: false,
			margin: 10,
			padding: 10,
			labelWidth: 160,
			items: [
				this.radioGroup,
				this.includeHidden,
				this.exportOrientation,
				this.hiddenDocumentTitle,
				this.hiddenName,
				this.hiddenUrl
			]
		});
		
		
		Ext.apply(this, {
			goDialogId:'export',
			title:'ExportDialog',
			autoHeight:true,
			width:400,
			items: [this.formPanel],
			buttons:[
			{ 
				text: GO.lang['cmdClose'], 
				handler: function(){ 
					this.hide(); 
				}, 
				scope: this 
			},{ 
				text: GO.lang['cmdExport'], 
				handler: function(){ 
					this.submitForm(true);
				}, 
				scope: this 
			}]
		});		
				
		GO.ExportDialog.superclass.initComponent.call(this);	
	},
	
	show : function () {
					
		if(!this.rendered){
			
			this.hiddenDocumentTitle.setValue(this.documentTitle);
			this.hiddenName.setValue(this.name);
			this.hiddenUrl.setValue(this.url);
			
			
				// Get the available export types for the form
			GO.request({
				url: 'export/types',
				params:{
					model: this.modelClassName
				},
				success: function(response, options, result)
				{
					var name;
					for(var clsName in result.outputTypes) {
						name = result.outputTypes[clsName];
						this.createExportTypeRadio(name, clsName);
					}
					GO.ExportDialog.superclass.show.call(this);	
				},
				scope:this
			});
		}else
		{
			GO.ExportDialog.superclass.show.call(this);
		}
		
	},	
	createExportTypeRadio : function(name,clsName) {
		var radioButton = new Ext.form.Radio({
			  fieldLabel : "",
        boxLabel   : name,
        name       : 'type',
        inputValue : clsName,
				value : clsName
		});
		
		this.radioGroup.items.push(radioButton);		
	},
	submitForm : function(hide) {
		this.formPanel.form.getEl().dom.target='_blank';
		this.formPanel.form.el.dom.target='_blank';
		this.formPanel.form.submit(
		{
			url:GO.url(this.url),
			params: {
				'name': this.name,
				'documentTitle' : this.documentTitle	
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action) {		
				console.log("SUCCESSFULL");
//				if(hide)
//					this.hide();	
			},		
			failure: function(form, action) {
				if(action.failureType == 'client')			
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
			 else
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});
		
		if(hide)
			this.hide();	
	}	
});