/**
 * This script is made up of two dialogs: one ImportDialog, and one dialog
 * to enable the user to map CSV columns to model fields.
 * @author WilmarVB <wilmar@intermesh.nl>
 */

GO.base.model.ImportDialog = function(config) {
	
	this._initDialog(config); // config MUST have parameters 'controllerName' and 'fileType'
	this._buildForm();
	
	config.title = GO.lang.cmdImport;
	config.layout = 'form';
	config.defaults = {anchor:'100%'};
	config.border = false;
	config.labelWidth = 150;
	config.toolbars = [];
	config.cls = 'go-form-panel';
	config.width = 400;
	config.items = [
		this.formPanel
	];
	
	GO.base.model.ImportDialog.superclass.constructor.call(this,config);
	
}

Ext.extend( GO.base.model.ImportDialog, GO.Window, {
	
	/****************************************************************************
	 ****************************************************************************
	 *
	 * Internal Fields
	 *
	 ****************************************************************************
	 *****************************************************************************
	 */
	
	// Fields that MUST be initiated at construction by passing:
	// 'excludedCustomFieldDataTypes', 'modelContainerIdName', 'controllerName' and 'fileType'
	// in the constructor config parameter.
	_importBaseParams : '', // Predefined attributes to set. MUST be object e.g., {addressbook_id : 3}. As an extra effect, these predefined attributes will not be imported.
	_moduleName : '', // e.g., addressbook
	_modelName : '', // e.g., contact
	_fileType : '', // e.g., csv
	_excludedCustomFieldDataTypes : ['GO_Customfields_Customfieldtype_Heading','GO_Customfields_Customfieldtype_Function'], // Default setting. These are the custom field types that are excluded from import.
	_excludedAttributes : [], // fields named here are excluded from import.
	
	// Fields that are set while the dialog is being used.
	_modelAttributes : {}, // All the model attributes used for CSV import.
	_csvHeaderStore : null, // ArrayStore containing all the CSV headers.
	_userSelectCSVMappings : {}, // An element of this object is, e.g., this._userSelectCSVMappings[33] = 't.first_name';, which says that the 33rd column of the CSV goes to the t.first_name field of the models.
	
	_csvFieldDialog : null, // The second dialog in the use case.
	
	/****************************************************************************
	 ****************************************************************************
	 *
	 * Methods for the first dialog.
	 *
	 ****************************************************************************
	 *****************************************************************************
	 */
	
	show : function(modelContainerId) {
		this.modelContainerIdField.setValue(modelContainerId);
		GO.base.model.ImportDialog.superclass.show.call(this);
	},
	
	// Config MUST have parameters
	// 'excludedCustomFieldDataTypes', 'importBaseParams', 'controllerName' and 'fileType'
	_initDialog : function(config) {
		this._importBaseParams = config.importBaseParams;
		var controllerNameArr = config['controllerName'].split('_');
		this._moduleName = controllerNameArr[1].toLowerCase();
		this._modelName = controllerNameArr[3].toLowerCase();
		this._fileType = config['fileType'];
		this._excludedAttributes = config['excludedAttributes'];
		for (var attrName in this._importBaseParams) {
			this._excludedAttributes.push(attrName);
		}
	},
	
	// Submit form to import the file.
	_submitForm : function(hide) {
		this.formPanel.form.submit({
			url : GO.url(this._moduleName + '/' + this._modelName + '/import' + this._fileType),
			params : {
				attributeIndexMap : Ext.encode(this._userSelectCSVMappings),
				importBaseParams : Ext.encode(this._importBaseParams)
			},
			success : function( success, response, result ) {
				if (!response.result.success) {
					Ext.MessageBox.alert(GO.lang.strError,result.feedback);
				} else {
					Ext.MessageBox.alert(GO.lang.strSuccess,GO.addressbook.lang['importSuccess']);
				}
			},
			failure : function ( form, action ) {
				if (!GO.util.empty(action.result.summarylog)) {
					var messageText = '';
					for (var i=0; i<action.result.summarylog.errors.length; i++)
						messageText = messageText + action.result.summarylog.errors[i].message + '<br />';
					Ext.MessageBox.alert(GO.lang.strError,messageText);
				} else if (!GO.util.empty(action.result.feedback)) {
					Ext.MessageBox.alert(GO.lang.strError,action.result.feedback);
				}
			},
			scope: this
		});
	},
	
	// Build form in constructor.
	_buildForm : function() {

		this.txtDelimiter = new Ext.form.TextField({
			name: 'delimiter',
			fieldLabel: GO.addressbook.lang.cmdFormLabelValueSeperated,
			allowBlank: false,
			value: GO.settings.list_separator,
			disabled: this._fileType!='CSV',
			hidden: this._fileType!='CSV'
		});
		
		this.txtEnclosure = new Ext.form.TextField({
			name: 'enclosure',
			fieldLabel: GO.addressbook.lang.cmdFormLabelValueIncluded,
			allowBlank: false,
			value: GO.settings.text_separator,
			disabled: this._fileType!='CSV',
			hidden: this._fileType!='CSV'
		});
		
		this.fileSelector = new GO.form.UploadFile({
			inputName: 'files',
			fieldLabel: GO.lang.upload,
			max:1
		});
		
		if (this._fileType=='CSV')
			this.fileSelector.on('fileAdded',function(file){
				this.formPanel.form.submit({
					url: GO.url(this._moduleName + '/' + this._modelName + '/readCSVHeaders'),
					success: function(form, action) {
						this._createCSVHeaderStore(action.result.results);
						this.showImportDataSelectionWindow();
					},
					scope: this
				})
			},this);
		
		this.fileTypeField = new Ext.form.TextField({
			hidden: true,
			name: 'fileType',
			value: this._fileType
		});
		
		this.modelContainerIdField = new Ext.form.TextField({
			hidden: true,
			name: this._modelContainerIdName
		});
		
		this.formPanel = new Ext.form.FormPanel({
			fileUpload : true,
			items: [
				this.txtDelimiter,
				this.txtEnclosure,
				this.fileSelector,
				this.fileTypeField,
				this.modelContainerIdField
			],
			buttons: [{
				text: GO.lang.cmdOk,
				width: '20%',
				handler: function(){
					this._submitForm(true);
				},
				scope: this
			},{
				text: GO.lang.cmdApply,
				width: '20%',
				handler: function(){
					this._submitForm(false);
				},
				scope: this
			},{
				text: GO.lang.cmdClose,
				width: '20%',
				handler: function(){
					this.hide();
				},
				scope: this
			}]
		});
		
	},
	
	/****************************************************************************
	 ****************************************************************************
	 *
	 * Methods for the second dialog.
	 *
	 ****************************************************************************
	 *****************************************************************************
	 */
	
	showImportDataSelectionWindow: function()
	{
		GO.request({
			url: this._moduleName+'/'+this._modelName+'/attributes',
			params: {
				exclude_cf_datatypes: Ext.encode(this._excludedCustomFieldDataTypes),
				exclude_attributes: Ext.encode(this._excludedAttributes)
			},
			success: function(options, response, result)
			{
				this._onAttributesLoaded(result.results);
			},
			scope:this
		});		
	},
	
	_createCSVHeaderStore : function(headersArray) {
		var data = [];
		data.push([-1,'---']);
		for (var colNr=0; colNr<headersArray.length; colNr++) {
			data.push([colNr,headersArray[colNr]]);
		}
		
		if (!(this._csvHeaderStore)) {
			this._csvHeaderStore = new Ext.data.ArrayStore({
				storeId: 'csvHeaderStore',
				idIndex: 0,
				fields:['colNr','headerString']
			});
		}
		
		this._csvHeaderStore.removeAll();
		this._csvHeaderStore.loadData(data);
		
	},
	
	// When, in case of an imported CSV, the model attributes are loaded, open up the second dialog
	_onAttributesLoaded : function(attributes) {

		this._modelAttributes = {};
		
		for (var i=0; i<attributes.length; i++) {
			if (attributes[i].gotype=='customfield') {
				if (GO.customfields)
					this._modelAttributes[attributes[i].name] = attributes[i].label;
			} else {
				this._modelAttributes[attributes[i].name] = attributes[i].label;
			}
		}
		
		if (!this.importFieldsFormPanel) {
			this.importFieldsFormPanel = new Ext.form.FormPanel({
				waitMsgTarget:true,

				//id: 'addressbook-default-import-data-window',
				labelWidth: 125,
				border: false,
				defaults: { 
					anchor:'-20'
				},
				cls: 'go-form-panel',
				autoHeight:true
			});

			this.importFieldsFormPanel.form.timeout=300;
		
			for(var key in this._modelAttributes)
			{
				var combo =  new Ext.form.ComboBox({
					fieldLabel: this._modelAttributes[key],
					id: this._moduleName+'_'+this._modelName+'_import_combo_'+key,
					store: this._csvHeaderStore,
					displayField:'headerString',
					valueField:	'colNr',
					hiddenName: key,
					mode: 'local',
					triggerAction: 'all',
					editable:false
				});

				this.importFieldsFormPanel.add(combo);
			}
		} else {
			this.importFieldsFormPanel.getForm().reset();
		}
		
		for(var key in this._modelAttributes)
		{
			var keyArray = key.split('.');
			var matchingRecordId = this._csvHeaderStore.findBy( function findByDisplayField(record,id) {
				if (!GO.util.empty(keyArray[1]) && record.data.headerString.toLowerCase()==keyArray[1].toLowerCase())
					return true;
				if (record.data.headerString.toLowerCase()==key.toLowerCase())
					return true;
				if (record.data.headerString.toLowerCase()==keyArray[0].toLowerCase())
					return true;
				return false;
			}, this);

			var matchingRecord = this._csvHeaderStore.getAt(matchingRecordId);

			if (!GO.util.empty(matchingRecord))
				var colNr = matchingRecord.data.colNr;
			else
				var colNr = null;

			var component = this.importFieldsFormPanel.getForm().findField(this._moduleName+'_'+this._modelName+'_import_combo_'+key);

			component.setValue(colNr);
		}

		if (!this._csvFieldDialog) {
			this._csvFieldDialog = new GO.Window({
				autoScroll:true,
				height: 400,
				width: 400,
				modal:true,
				title: GO.addressbook.lang.matchFields,
				items: [
				this.importFieldsFormPanel
				],
				buttons: [{
					text: GO.lang['cmdOk'],
					handler: function() {
						this._rememberCSVmappings();
						this._csvFieldDialog.close();
					},
					scope: this
				},{
					text: GO.lang['cmdClose'],
					handler: function(){
						this._csvFieldDialog.close();
					},
					scope: this
				}]
			});
		}
		
		this._csvFieldDialog.show();			
	},
	
	_rememberCSVmappings : function() {
		this._userSelectCSVMappings = {};
		Ext.each(this.importFieldsFormPanel.items.items,function(item,index,allItems){
			if (typeof(item.value)=='number') {
				var idArray = (item.id).substring(13).split('.');
				this._userSelectCSVMappings[item.value] = idArray[1];
			}
		},this);
	}
	
});