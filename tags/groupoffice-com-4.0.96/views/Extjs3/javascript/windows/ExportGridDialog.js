/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: ExportGridDialog.js 7982 2011-08-29 14:38:37Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.ExportGridDialog = Ext.extend(GO.Window , {
	
	documentTitle : '',
	name : '',
	url : '',
	colModel : '',	
	exportClassPath : "",

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
		this.hiddenColumns = new Ext.form.Hidden({
			name:'columns'
		});
		this.hiddenHeaders = new Ext.form.Hidden({
			name:'headers'
		});
	
		this.radioGroup = new Ext.form.RadioGroup({
			fieldLabel : 'Type',
			name       : 'exportFormat',
			columns: 1,
			items: []
		});
		
		this.radioGroup.on('change', function(){
			this.checkOrientation(this.radioGroup.getValue());
		}, this);
		
		this.includeHidden = new Ext.form.Checkbox({
			fieldLabel : GO.lang.exportIncludeHidden,
			name       : 'includeHidden'
		});
		
		this.humanHeaders = new Ext.form.Checkbox({
			fieldLabel : GO.lang.exportHumanHeaders,
			name       : 'humanHeaders',
			checked		 : true
		});
		
		this.includeHeaders = new Ext.form.Checkbox({
			fieldLabel  : GO.lang.exportIncludeHeaders,
			name				: 'includeHeaders'
		});
		
		this.exportOrientation = new Ext.form.ComboBox({
			fieldLabel : GO.lang.exportOrientation,
			hiddenName: 'exportOrientation',
			name: 'exportOrientation',
			mode: 'local',
			editable:false,
			triggerAction:'all',
			lazyRender:true,
			width: 120,
			value:"V",
			store: new Ext.data.SimpleStore({
				fields: [
						'myId',
						'displayText'
				],
				data: [['H', GO.lang.landscape], ['V', GO.lang.portrait]]
			}),
			valueField: 'myId',
			displayField: 'displayText'
		});
		
		this.includeHeaders.setValue(true);
		
		this.hiddenParamsField = new Ext.form.Hidden({
			name:'params'
		});
		
		this.formPanel = new Ext.form.FormPanel({
			url:GO.url(this.url),
			standardSubmit:true,
			waitMsgTarget:true,			
			border: false,
			margin: 10,
			baseParams:{},
			padding: 10,
			labelWidth: 160,
			autoHeight:true,
			items: [
				this.radioGroup,
				this.includeHidden,
				this.includeHeaders,
				this.humanHeaders,
				this.exportOrientation,
				this.hiddenDocumentTitle,
				this.hiddenName,
				this.hiddenUrl,
				this.hiddenColumns,
				this.hiddenHeaders,
				this.hiddenParamsField
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
				text: GO.lang['cmdExport'], 
				handler: function(){ 
					this.submitForm(true);
				}, 
				scope: this 
			},{ 
				text: GO.lang['cmdClose'], 
				handler: function(){ 
					this.hide(); 
				}, 
				scope: this 
			}]
		});		
				
		GO.ExportGridDialog.superclass.initComponent.call(this);	
	},
	
	show : function () {
		
		this.hiddenParamsField.setValue(Ext.encode(this.params));
		this.hiddenDocumentTitle.setValue(this.documentTitle);
		this.hiddenName.setValue(this.name);
		this.hiddenUrl.setValue(this.url);
		
	
		if(!this.rendered){
				// Get the available export types for the form
			GO.request({
				url: 'export/types',
				params:{
					exportClassPath:this.exportClassPath
				},
				success: function(response, options, result)
				{
					var name;
					var useOrientation;
					var checked=true;
					for(var clsName in result.outputTypes) {
						name = result.outputTypes[clsName].name;
						useOrientation = result.outputTypes[clsName].useOrientation;
						this.createExportTypeRadio(name, clsName, checked, useOrientation);
						checked=false;
					}
					GO.ExportGridDialog.superclass.show.call(this);	
				},
				scope:this
			});
		}else
		{
			GO.ExportGridDialog.superclass.show.call(this);
		}		
	},	
	createExportTypeRadio : function(name,clsName, checked, useOrientation) {
		var radioButton = new Ext.form.Radio({
			  fieldLabel : "",
        boxLabel   : name,
        name       : 'type',
        inputValue : clsName,
				value : clsName,
				checked: checked,
				orientation: useOrientation
		});
		
		this.radioGroup.items.push(radioButton);		
		if(checked && !useOrientation)
			this.exportOrientation.hide();
	},
	checkOrientation : function(selectedRadio){

		if(!selectedRadio.orientation)
			this.exportOrientation.hide();
		else
			this.exportOrientation.show();
		
		this.syncShadow();
		
	},
	addFormElement : function(elementToAdd){
		this.formPanel.add(elementToAdd);
	},
	insertFormElement : function(targetIndex, elementToAdd){
		this.formPanel.insert(targetIndex, elementToAdd);
	},
	
	submitForm : function(hide) {
		this.formPanel.form.getEl().dom.target='_blank';
		this.formPanel.form.el.dom.target='_blank';
		
		// Get the columns that needs to be exported from the grid.
		var columns = [];
		var headers = [];
			
		var exportHidden = this.includeHidden.getValue();

		if (this.colModel) {
			for (var i = 0; i < this.colModel.getColumnCount(); i++) {
				var c = this.colModel.config[i];

				if ((exportHidden || !c.hidden) && !c.hideInExport)
					columns.push(c.dataIndex);
					headers.push(c.header);
			}
		}
		
		this.hiddenColumns.setValue(columns.join(','));
		this.hiddenHeaders.setValue(headers.join(','));

		this.formPanel.form.submit(
		{
			url:GO.url(this.url),
			params: {
				'name': this.name,
				'documentTitle' : this.documentTitle	
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action) {		
				//console.log("SUCCESSFULL");
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