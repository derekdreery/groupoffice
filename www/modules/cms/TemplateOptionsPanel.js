GO.cms.TemplateOptionsPanel = Ext.extend(function(cfg) {

	var config = {
		layout : 'form',
		border : false,
		bodyStyle : 'padding:0px;',
		autoHeight : true
	};

	Ext.apply(config, cfg);

	GO.cms.TemplateOptionsPanel.superclass.constructor.call(this,
		config);

	this.addEvents({
		htmlTemplateSelected : true
	});
}, Ext.Panel, {

	templateConfig : false,
			
	options : [],
			
	loadConfig : function(config, optionValues, type, defaultTemplate) {
		var t, o;

		this.templateConfig = config;
		this.optionValues=optionValues;

		if (this.items) {
			this.items.each(function(formField) {
				var withLabel = formField.container
				.up('div.x-form-item');
				formField.destroy();
				if (withLabel)
					withLabel.remove();
			});
		}
				
				

		if (config.templates && config.templates.length) {
			var comboConfig = {
				fieldLabel : GO.cms.lang.insertTemplate,
				store : new Ext.data.SimpleStore({
					fields : ['name', 'html'],
					data : config.templates
				}),
				name : 'default_template',
				displayField : 'name',
				mode : 'local',
				triggerAction : 'all',
				editable : false,
				forceSelection : false,
				anchor : '-20'
			};

			if (!this.isFolder) {
				comboConfig.listeners = {
					select : function(combo, record, index) {
						this.fireEvent('htmlTemplateSelected', record);
						combo.reset();
					},
					scope : this
				};
			} else if (defaultTemplate) {
				comboConfig.value = defaultTemplate;
			}

			this.add(new GO.form.ComboBoxReset(comboConfig));
		}
				
				
				
		if (config.types && config.types.length) {
			var comboConfig = {
				fieldLabel : GO.lang.strType,
				store : new Ext.data.SimpleStore({
					fields : ['name', 'options'],
					data : config.types
				}),
				name : 'type',
				value: type,
				displayField : 'name',
				mode : 'local',
				triggerAction : 'all',
				editable : false,
				forceSelection : false,
				anchor : '-20',
				listeners : {
					select : function(combo, record, index) {
						this.addOptions(record.get('name'));
					},
					scope : this
				}
			};

			this.add(new GO.form.ComboBoxReset(comboConfig));
					
			if (this.isFolder) {
				this.add(new Ext.form.Checkbox({
					hideLabel : true,
					boxLabel : GO.cms.lang.applyRecursive,
					name : "recursive"
				}));
			}
		}
		this.addOptions(type);
	},
			
	removeOptions : function(){
		for(var i=0;i<this.options.length;i++)
		{
			var withLabel = this.options[i].container.up('div.x-form-item');
			this.options[i].destroy();
					
			if (withLabel)
				withLabel.remove();
		}
		this.options=[];
	},
			
	addOptions : function(type){
				
		this.removeOptions();
				
		var options = [];
				
		for(var i=0;i<this.templateConfig.types.length;i++)
		{
			if(this.templateConfig.types[i][0]==type)
			{
				options=this.templateConfig.types[i][1];
				break;
			}
		}
		var value;
		if (options.length) {
			for (var i = 0; i < options.length; i++) {
				o = options[i];
				if (o.type == 'select') {
					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: o.options[0][0];
									
					this.options.push(new GO.form.ComboBoxReset({
						fieldLabel : o.fieldLabel,
						hiddenName : o.name,
						store : new Ext.data.SimpleStore({
							fields : ['value', 'text'],
							data : o.options
						}),
						value : value,
						valueField : 'value',
						displayField : 'text',
						mode : 'local',
						triggerAction : 'all',
						editable : false,
						forceSelection : true,
						anchor : '-20'
					}));


				} else if(o.type=='file'){

					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: '';
			
					this.options.push(new GO.files.SelectFile({
						fieldLabel : o.fieldLabel,
						root_folder_id : this.ownerCt.ownerCt.ownerCt.root_folder_id,
						name : o.name,
						value : value,
						anchor : '-20',
						filesFilter : o.files_filter
					}));
							
				}else if(o.type=='checkbox'){
					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: '';
					this.options.push(new Ext.form.Checkbox({
						boxLabel : o.fieldLabel,
						hideLabel:true,
						name : o.name,
						checked : !GO.util.empty(value),
						anchor : '-20'
					}));
				}else if(o.type=='textarea'){
					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: '';
					this.options.push(new Ext.form.TextArea({
						fieldLabel : o.fieldLabel,
						name : o.name,
						value : value,
						anchor : '-20'
					}));
				}else if(o.type=='date'){
					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: '';
					this.options.push(new Ext.form.DateField({
						fieldLabel : o.fieldLabel,
						name : o.name,
						value : value,
						anchor : '-20'
					}));
				}else if(o.type=='combobox'){
					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: '';
					this.options.push(new Ext.form.ComboBox({
						fieldLabel : o.fieldLabel,
						name : o.name,
						//value : value,
						displayField:o.name,
						valueField: 'id',
						hiddenName:o.name,
						anchor : '-20',
						mode:'local',
						triggerAction:'all',
						store : new GO.data.JsonStore({
							url: GO.settings.modules.cms.url+ 'json.php',
							baseParams: {
								task: 'folder_files',
								folder_id: o.folder_id,
								name: o.name
							},
							root: 'results',
							id: 'id',
							totalProperty:'total',
							fields: ['id','folder_id','extension','size','ctime','mtime',o.name,'content','auto_meta','title','description','keywords','priority','hot_item','hot_item_text','template_item_id','acl','registered_comments','unregistered_comments'],
							remoteSort: true,
							autoLoad: true
						})
					}));
				}else {
					value = this.optionValues[o.name]
					? this.optionValues[o.name]
					: '';
					this.options.push(new Ext.form.TextField({
						fieldLabel : o.fieldLabel,
						name : o.name,
						value : value,
						anchor : '-20'
					}));
				}
				this.add(this.options[this.options.length-1]);
			}
		}
		this.doLayout();
	}


});