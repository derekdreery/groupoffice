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
	
	this._createAttributesStore();
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
	_importBaseParams : '', // Predefined attributes to set. MUST be object e.g., {addressbook_id : 3, foo: 'bar'}. As an extra effect, model attributes that are set this way will not be imported in this use case.
	_moduleName : '', // e.g., addressbook
	_modelName : '', // e.g., contact
	_fileType : '', // e.g., CSV, VCard
	_excludedCustomFieldDataTypes : ['GO_Customfields_Customfieldtype_Heading','GO_Customfields_Customfieldtype_Function'], // Default setting. These are the custom field types that are excluded from import.
	_excludedAttributes : [], // fields named here are excluded from import.
	
	// Fields that are set while the dialog is being used.
	_colHeaders : {}, // This is a buffer associative array for all the cell values of the uploaded CSV file's first row.
	_attributesStore : null, // Also a buffer, an ArrayStore containing all the current model's attributes.
	_userSelectCSVMappings : {}, // An element of this object is, e.g., this._userSelectCSVMappings[33] = 'first_name';, which says that the 33rd column of the CSV goes to the t.first_name field of the models.
	
	_csvFieldDialog : null, // The second dialog in the use case.
	_inputIdPrefix : '', // Prefix for the input field id's in the _csvFieldDialog.
	
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
		this._importBaseParams[this._modelContainerIdName] = modelContainerId;
		GO.base.model.ImportDialog.superclass.show.call(this);
		this.fileSelector.clearQueue();
	},
	
	// Config MUST have parameters
	// 'excludedCustomFieldDataTypes', 'importBaseParams', 'controllerName' and 'fileType'
	_initDialog : function(config) {
		this._importBaseParams = config.importBaseParams;
		var controllerNameArr = config['controllerName'].split('_');
		this._moduleName = controllerNameArr[1].toLowerCase();
		this._modelName = controllerNameArr[3].toLowerCase();
		this._modelContainerIdName = config['modelContainerIdName'];
		this._fileType = config['fileType'];
		this._excludedAttributes = config['excludedAttributes'];
		for (var attrName in this._importBaseParams) {
			this._excludedAttributes.push(attrName);
		}
	},
	
	// Submit form to import the file.
	_submitForm : function() {
		if (!this._loadMask)
			this._loadMask = new Ext.LoadMask(Ext.getBody(), {msg: GO.addressbook.lang.importing+'...'});
		this._loadMask.show();

		this.formPanel.form.submit({
			url : GO.url(this._moduleName + '/' + this._modelName + '/import' + this._fileType),
			params : {
				attributeIndexMap : Ext.encode(this._userSelectCSVMappings),
				importBaseParams : Ext.encode(this._importBaseParams)
			},
			success : function( success, response, result ) {
				var errorsText = '';
				if (!GO.util.empty(response.result.summarylog)) {
					for (var i=0; i<response.result.summarylog.errors.length; i++) {
						if (i==0)
							errorsText = '<br />' + GO.lang.failedImportItems + ':<br />';
						errorsText = errorsText + GO.lang.item + ' ' + response.result.summarylog.errors[i].name + ': ' +
													response.result.summarylog.errors[i].message + '<br />';
					}
					//Ext.MessageBox.alert(GO.lang.strError,errorsText);
				}

				if (!response.result.success) {
					Ext.MessageBox.alert(GO.lang.strError,result.feedback);
				} else {
					if (response.result.totalCount){
						if(response.result.totalCount != response.result.successCount){
							GO.errorDialog.show(
								errorsText,
								GO.addressbook.lang['importSuccessCount']+' '+response.result.successCount+'/'+response.result.totalCount
							);
						} else {
							Ext.MessageBox.alert(
								'',
								GO.addressbook.lang['importSuccessCount']+' '+response.result.successCount+'/'+response.result.totalCount
								+ errorsText
							);
						}
					}else{
						Ext.MessageBox.alert(
							'',
							GO.addressbook.lang['importSuccess']
							+ errorsText
						);
					}
						
					this.hide();
					if (!GO.util.empty(this._csvFieldDialog))
						this._csvFieldDialog.close();
				}
				this._loadMask.hide();
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
				this._loadMask.hide();
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
//				this.formPanel.form.submit({
//					url: GO.url(this._moduleName + '/' + this._modelName + '/readCSVHeaders'),
//					success: function(form, action) {
//						
//					},
//					scope: this
//				})
				this.showImportDataSelectionWindow();
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
				text: GO.lang.cmdImport,
				width: '20%',
				disabled: this._fileType=='CSV',
				hidden: this._fileType=='CSV',
				handler: function(){
					this._submitForm();
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
		this.formPanel.form.submit({
			url: GO.url(this._moduleName + '/' + this._modelName + '/readCSVHeaders'),
			success: function(form, action) {
				this._buildCsvImportForm(action.result.results);
				this.el.mask();
				this._csvFieldDialog.show();
			},
			scope: this
		});
	},
	
	_createAttributesStore : function() {
		var data = [];
		data.push(['-','-','< < '+GO.lang.unused+' > >']);
		
		if (!(this._attributesStore)) {
			this._attributesStore = new Ext.data.ArrayStore({
				storeId: 'attributesStore',
				idIndex: 0,
				fields:['dbShortFieldName','dbFieldFullName','label']
			});
		}
		
		this._attributesStore.removeAll();
		
		GO.request({
			url: this._moduleName+'/'+this._modelName+'/attributes',
			params: {
				exclude_cf_datatypes: Ext.encode(this._excludedCustomFieldDataTypes),
				exclude_attributes: Ext.encode(this._excludedAttributes)
			},
			success: function(options, response, attributeResult)
			{
				for (var i=0; i<attributeResult.results.length; i++) {
					var nameArray = attributeResult.results[i]['name'].split('.');
					var nameOnly = nameArray[1];
					if (attributeResult.results[i]['gotype']=='customfield') {
						if (GO.customfields)
							data.push([nameOnly,attributeResult.results[i]['name'],attributeResult.results[i]['label']]);
					} else {
						data.push([nameOnly,attributeResult.results[i]['name'],attributeResult.results[i]['label']]);
					}
				}
				this._attributesStore.loadData(data);
			},
			scope:this
		});	
	},
	
	// Create the second dialog, should be done after every new uploaded file
	// in showImportDataSelectionWindow()
	_buildCsvImportForm : function(colHeaders) {

		this._colHeaders = colHeaders;
		
		if (!this.importFieldsFormPanel) {
			
			this._inputIdPrefix = this._moduleName+'_'+this._modelName+'_import_combo_';
			
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
		} else {
			// This destroys all the form's components for every new uploaded file.
			this.importFieldsFormPanel.removeAll(true);
		}
		
		// Create and add new fields for every column for every new uploaded file.
		for(var colNr=0; colNr<this._colHeaders.length; colNr++)
		{
			var combo =  new Ext.form.ComboBox({
				fieldLabel: this._colHeaders[colNr],
				id: this._inputIdPrefix+colNr,
				store: this._attributesStore,
				displayField:'label',
				valueField:	'dbShortFieldName',
				hiddenName: colNr,
				mode: 'local',
				triggerAction: 'all',
				editable:false
			});

			this.importFieldsFormPanel.add(combo);
		}
		
		/**
		 * This presets the comboboxes of the second dialog, such that any
		 * recognized column field in the form has the matching model attribute
		 * value in its combobox.
		 */
		for(var colNr=0; colNr<this._colHeaders.length; colNr++)
		{
			var colName = this._colHeaders[colNr];
			var matchingRecordId = this._attributesStore.findBy( function findByDisplayField(attributeRecord,id) {
				return !GO.util.empty(colName) && attributeRecord.data.dbShortFieldName.toLowerCase()==colName.toLowerCase();
			}, this);

			if (!GO.util.empty(this._attributesStore.getAt(matchingRecordId)))
				var presetMatchingValue = this._attributesStore.getAt(matchingRecordId).data.dbShortFieldName;
			else
				var presetMatchingValue = '-';

			var component = this.importFieldsFormPanel.getForm().findField(this._inputIdPrefix+colNr);

			component.setValue(presetMatchingValue);
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
					text: GO.lang['cmdImport'],
					handler: function() {
						this._rememberCSVmappings();
						this._submitForm();
						this.hide();
						this.el.unmask();
					},
					scope: this
				},{
					text: GO.lang['cmdCancel'],
					handler: function(){
						this._csvFieldDialog.close();
						this.hide();
						this.el.unmask();
					},
					scope: this
				}]
			});
		}
	},
	
	/**
	 * Last bit before the import paramaters are submitted: make ready the array
	 * this._userSelectCSVMappings as set by the user. That is basically an array
	 * whose keys are the column number in the uploaded CSV file (starting from 0),
	 * and whose values are the database field names such as used in the GO
	 * framework queries (e.g. in the case of contact import: t.address_no,
	 * companies.name)
	 */
	_rememberCSVmappings : function() {
		this._userSelectCSVMappings = {};
		Ext.each(this.importFieldsFormPanel.items.items,function(item,index,allItems){
			if (item.value!='-') {
				var colNr = item.id.replace(this._inputIdPrefix,"");
				this._userSelectCSVMappings[colNr] = item.value;
			}
		},this);
	}
	
});