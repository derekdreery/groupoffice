GO.customfields.dataTypes={
	GO_Customfields_Customfieldtype_User:{
		label : GO.lang.strUser,
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			delete f.name;

			f=Ext.apply(f, {
				xtype: 'selectuser',
				idValuePair:true,
				startBlank:true,
				forceSelection:true,
				hiddenName:customfield.dataname,
				anchor:'-20',
				valueField:'cf'
			});

			return f;
		}
    },
	GO_Customfields_Customfieldtype_Datetime:{
		label:'Date time',
		getFormField : function(customfield, config){			
			
			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			delete f.anchor;

			return Ext.apply(f, {				
				xtype:'datetime',
				width : 300
//				timeFormat: GO.settings['time_format'],
//				dateFormat:GO.settings['date_format'],
//				hiddenFormat: GO.settings['date_format']+" "+GO.settings['time_format']
			});
		}
	},

	GO_Customfields_Customfieldtype_Date : {
		label:'Date',
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			delete f.anchor;

			return Ext.apply(f, {				
				xtype:'datefield',
				format: GO.settings['date_format'],
				width : 100
			});
		}
	},

	GO_Customfields_Customfieldtype_Number : {
		label : 'Number',
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			return Ext.apply(f, {
				xtype:'numberfield',
				decimals: customfield.number_decimals
			});
		}
	},
	GO_Customfields_Customfieldtype_Checkbox :{
		label: 'Checkbox',
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			delete f.fieldLabel;

			return Ext.apply(f, {
				xtype:'xcheckbox',
				boxLabel: customfield.name,
				hideLabel: true
			});
		}
	},
	GO_Customfields_Customfieldtype_BinaryCombobox :{
		label: 'Binary Combobox',
		getFormField : function(customfield, config){

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			delete f.fieldLabel;

			return Ext.apply(f, {
				xtype:'combo',
				fieldLabel: customfield.name,
				hiddenName: customfield.dataname,
				store: new Ext.data.ArrayStore({
						storeId: 'binaryStore',
						idIndex: 0,
						fields:['value','label'],
						data: [
							['0',GO.lang['cmdNo']],
							['1',GO.lang['cmdYes']]
						]
					}),
				valueField:'value',
				displayField:'label',
				mode:'local',
				allowBlank: false,
				triggerAction: 'all'
			});
		}
	},
	GO_Customfields_Customfieldtype_Textarea : {
		label : 'Textarea',
		getFormField : function(customfield, config){
			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

                        if(!customfield.height)
                        {
                               customfield.height = 40;
                        }
                        
			return Ext.apply(f, {
				xtype:'textarea',
                                height:parseInt(customfield.height)
			}, config);
		}
	},
	GO_Customfields_Customfieldtype_Html : {
		label : 'HTML',
		getFormField : function(customfield, config){
			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			return Ext.apply(f, {
				xtype:'xhtmleditor',
				height:200
			}, config);
		}
	},
	GO_Customfields_Customfieldtype_Select : {
		label : 'Select',
		getFormField : function(customfield, config){
			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);
			var store = new GO.data.JsonStore({
				//url: GO.settings.modules.customfields.url+'json.php',
				url:GO.url('customfields/field/selectOptions'),
				baseParams: {
					//'task': 'field_options',
					'field_id' : customfield.customfield_id//customfield.id.replace("col_","")
				},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','text'],
				remoteSort:true
			});

			if(GO.util.empty(customfield.multiselect)){
				return Ext.apply(f, {
					xtype:'combo',
					store: store,
					valueField:'text',
					displayField:'text',
					mode: 'remote',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true
				}, config);
			}else
			{
				return Ext.apply(f, {
					max:parseInt(customfield.max),
					allowAddNewData:true, //otherwise every value will be looked up at the server. We don't want that.
					xtype:'superboxselect',
					resizable: true,
					store: store,
					mode: 'remote',
					displayField: 'text',
					displayFieldTpl: '{text}',
					valueField: 'text',
					forceSelection : true,
					valueDelimiter:'|',
					hiddenName:customfield.dataname,
					anchor:'-20',
					allowBlank: GO.util.empty(customfield.required),
					queryDelay: 0,
					triggerAction: 'all'
				});
			}
		}
	},
	/*
	 * A GO_Customfields_Customfieldtype_Treeselect consists of one master and one or more slave comboboxes.
	 * The slave is loaded with data depending on the selection of it's parent.
	 * The last slave can be a multiselect combo (superboxselect).
	 */
	GO_Customfields_Customfieldtype_Treeselect : {
		label : 'Tree select',
		getFormField : function(customfield, config){

			//store the slaves of this GO_Customfields_Customfieldtype_Treeselect in an array
			if(!GO.customfields.slaves)
				GO.customfields.slaves={};

			var treemaster_field_id = !GO.util.empty(customfield.treemaster_field_id) ? customfield.treemaster_field_id : customfield.id;

			if(!GO.customfields.slaves[treemaster_field_id])
				GO.customfields.slaves[treemaster_field_id]={};

			GO.customfields.slaves[treemaster_field_id][parseInt(customfield.nesting_level)]=customfield.dataname;

			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			var isMaster = customfield.nesting_level==0;

			var store = new GO.data.JsonStore({
				//url: GO.settings.modules.customfields.url+'json.php',
				url: GO.url('customfields/fieldTreeSelectOption/store'),
				baseParams: {
					//'task': 'tree_select_options_combo',
					'field_id' : treemaster_field_id,
					parent_id: (isMaster) ? 0 : -1
				},
				root: 'results',
				totalProperty: 'total',
				id: 'id',
				fields:['id','name','name_with_id'],
				remoteSort:true
			});

			delete f.name;

			if(GO.util.empty(customfield.multiselect)){
				return Ext.apply(f, {
					treemaster_field_id:treemaster_field_id,
					nesting_level:parseInt(customfield.nesting_level),
					xtype:'combo',
					store: store,
					hiddenName:customfield.dataname,
					valueField:'name_with_id',
					displayField:'name',
					mode: 'remote',
					triggerAction: 'all',
					editable: true,
					selectOnFocus:true,
					forceSelection:true,
					listeners:{
						scope:this,
						select:function(combo, record, index){
							var nextNestingLevel=combo.nesting_level+1;
							var formPanel = combo.findParentByType('form');
							while(GO.customfields.slaves[combo.treemaster_field_id][nextNestingLevel]){								
								
								var field = formPanel.form.findField(GO.customfields.slaves[combo.treemaster_field_id][nextNestingLevel]);
								if(!field)
									field = formPanel.form.findField(GO.customfields.slaves[combo.treemaster_field_id][nextNestingLevel]+'[]');

								if(nextNestingLevel==combo.nesting_level+1) //is first upcoming slave
									field.store.baseParams.parent_id=record.id;
								else
									field.store.baseParams.parent_id = -1;
								field.lastQuery = null;
								field.clearValue();
								
								nextNestingLevel++;
							}							
						},
						render:function(combo){
							//var formPanel = combo.findParentByType("form");
							//
							var formPanel = combo.findParentBy(function(p){
								if(p.form)
									return true;
							});
							//add listener to form to set the correct form values and store parameters
							if(!GO.util.empty(formPanel) && !formPanel.GO_Customfields_Customfieldtype_TreeselectListenerAdded){
								formPanel.GO_Customfields_Customfieldtype_TreeselectListenerAdded=true;

								formPanel.on('actioncomplete', function(form, action){
									if(action.type=='load'){
										form.items.each(function(field){
											//check if this field is a tree select
											if(field.treemaster_field_id){

												var nextField=false;
												var nextNestingLevel=field.nesting_level+1;
												if(GO.customfields.slaves[field.treemaster_field_id][nextNestingLevel]){

													nextField = formPanel.form.findField(GO.customfields.slaves[field.treemaster_field_id][nextNestingLevel]);
													if(!nextField)
														nextField = formPanel.form.findField(GO.customfields.slaves[field.treemaster_field_id][nextNestingLevel]+'[]');
												}
												var v = field.getValue();

												if(v){
													if(!field.valueDelimiter){
														//normal combo

														v=v.split(':');
														if(v.length>1){

															if(nextField)
																nextField.store.baseParams.parent_id=v[0];
															field.setRawValue(v[1]);
														}
													}
												}else
												{
													//empty value
													if(field.nesting_level==0) // is master												
														field.store.baseParams.parent_id=0;
													else
														field.store.baseParams.parent_id= -1;
													field.clearValue();
												}
												field.lastQuery = null;
												
											}
											
										});
									}
								});
							}
						}
					}
				}, config);
			}else
			{
				//if (combo.nesting_level!=0)
					store.baseParams.parent_id=-1;
				//only the last slave can be a multiselect combo
				return Ext.apply(f, {
					allowAddNewData:true,
					//itemId:customfield.dataname,
					max:parseInt(customfield.max),
					treemaster_field_id:treemaster_field_id,
					nesting_level:parseInt(customfield.nesting_level),
					xtype:'superboxselect',
					resizable: true,
					store: store,
					mode: 'remote',
					displayField:'name',
					displayFieldTpl: '{name}',
					valueField: 'name_with_id',
					forceSelection : true,
					valueDelimiter:'|',
					hiddenName:customfield.dataname,
					anchor:'-20',
					allowBlank: GO.util.empty(customfield.required),
					queryDelay: 0,
					triggerAction: 'all'
				}, config);
			}

		}
	},
	GO_Customfields_Customfieldtype_TreeselectSlave : {
		label:'Tree select slave',
		getFormField : function(customfield, config){
			return GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Treeselect.getFormField(customfield, config);
		}
	},
	GO_Customfields_Customfieldtype_Heading : {
		label : 'Heading',
		getFormField : function(customfield, config){
			return new GO.form.HtmlComponent(Ext.apply({
				html: "<h1 class=\"cf-heading\">"+customfield.name+"</h1>"
			}, config));
		}
	},
	GO_Customfields_Customfieldtype_Function : {
		label : 'Function',
		getFormField : function(customfield, config){
			return new Ext.form.Hidden(Ext.apply({
				name: customfield.dataname
			}, config));
//			return false;
		}
	},
	GO_Customfields_Customfieldtype_Text: {
		label : 'Text',
		getFormField : function(customfield, config){

			config = config || {};

			if(!GO.util.empty(customfield.validation_regex)){
				
				if(!GO.util.empty(customfield.validation_modifiers))
					config.regex=new RegExp(customfield.validation_regex, customfield.validation_modifiers);
				else
					config.regex=new RegExp(customfield.validation_regex);
			}

			if(!GO.util.empty(customfield.helptext))
				config.plugins=new Ext.ux.FieldHelp(customfield.helptext);

			var fieldLabel = customfield.name;
			if(!GO.util.empty(customfield.required))
				fieldLabel+='*';

			return Ext.apply({
				xtype:'textfield',
				name: customfield.dataname,
				fieldLabel: fieldLabel,
				anchor:'-20',
				allowBlank: GO.util.empty(customfield.required)
			}, config);
		}
	},
	GO_Customfields_Customfieldtype_EncryptedText: {
		label : 'Encrypted text',
		getFormField : function(customfield, config) {
			config = config || {};

			if(!GO.util.empty(customfield.validation_regex))
				config.regex=new RegExp(customfield.validation_regex);

			if(!GO.util.empty(customfield.helptext))
				config.plugins=new Ext.ux.FieldHelp(customfield.helptext);

			var fieldLabel = customfield.name;
			if(!GO.util.empty(customfield.required))
				fieldLabel+='*';

			return Ext.apply({
				xtype:'textfield',
				name: customfield.dataname,
				fieldLabel: fieldLabel,
				anchor:'-20',
				allowBlank: GO.util.empty(customfield.required)
			}, config);
		}
	},
	GO_Customfields_Customfieldtype_Infotext: {
		label : 'Info text',
		getFormField : function(customfield, config){

			config = config || {};

			return Ext.apply({
				xtype:'htmlcomponent',
				html: customfield.name,
				style:'font-size:12px;margin-bottom:15px;'
			}, config);
		}
	},
	GO_Customfields_Customfieldtype_Yesno : {
		label : 'Yes No Field',
		getFormField : function(customfield, config){
			var f = GO.customfields.dataTypes.GO_Customfields_Customfieldtype_Text.getFormField(customfield, config);

			var store = new Ext.data.SimpleStore({
				id: 'id',
				fields:['id','text'],
				data: [['0',GO.customfields.lang.undef],
							['1',GO.lang.cmdYes],
							['-1',GO.lang.cmdNo]],
				remoteSort:false
			});

			delete f.name;

			return Ext.apply(f, {
				xtype:'combo',
				store: store,
				valueField:'id',
				displayField:'text',
				hiddenName:customfield.dataname,
				mode: 'local',
				editable: false,
				triggerAction : 'all',
				selectOnFocus:true,
				forceSelection:true
			}, config);
		}
	}
};
