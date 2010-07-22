GO.ExportQueryDialog = Ext.extend(Ext.Window, {

	/*
	 * Pass extra type radio buttons with this config option
	 */
	customTypes : [],

	initComponent : function() {		

		this.formPanelItems[0].items = [{
			boxLabel : 'CSV',
			name : 'type',
			inputValue : 'csv_export_query',
			checked : true
		}, {
			boxLabel : 'PDF',
			name : 'type',
			inputValue : 'pdf_export_query'
		}, {
			boxLabel : GO.lang.toScreen,
			name : 'type',
			inputValue : 'html_export_query'
		}];

		if(this.query && GO.customexports[this.query]){
			for(var i=0;i<GO.customexports[this.query].length;i++){
				this.formPanelItems[0].items.push({
					boxLabel : GO.customexports[this.query][i].name,
					name : 'type',
					inputValue : GO.customexports[this.query][i].cls
				});
			}
		}

		for(var i=0,max=this.customTypes.length;i<max;i++)
			this.formPanelItems[0].items.push(this.customTypes[i]);


		if(!this.title)
			this.title = GO.lang.cmdExport;
		
		Ext.apply(this, {
			
			items : this.formPanel = new Ext.FormPanel({
						items : this.formPanelItems,
						bodyStyle : 'padding:5px'
					}),
			autoHeight : true,
			closeAction : 'hide',
			closeable : true,
			height : 400,
			width : 400,
			buttons : [{
						text : GO.lang.strEmail,
						handler : function() {
							this.hide();

							this.beforeRequest();
							GO.email.showComposer({
										loadUrl : BaseHref + 'json.php',
										loadParams : this.loadParams
									});
						},
						scope : this
					}, {
						text : GO.lang.download,
						handler : function() {

							this.beforeRequest();

							var downloadUrl = '';
							for (var name in this.loadParams) {

								if (downloadUrl == '') {
									downloadUrl = BaseHref
											+ 'export_query.php?';
								} else {
									downloadUrl += '&';
								}

								downloadUrl += name
										+ '='
										+ encodeURIComponent(this.loadParams[name]);
							}
							window.open(downloadUrl);
							this.hide();
						},
						scope : this
					}, {
						text : GO.lang['cmdClose'],
						handler : function() {
							this.hide();
						},
						scope : this
					}]
		});

		GO.ExportQueryDialog.superclass.initComponent.call(this);
	},

	loadParams : {},
	downloadUrl : '',
	showAllFields:false,

	formPanelItems : [{
				autoHeight : true,
				xtype : 'radiogroup',
				fieldLabel : GO.lang.strType,
				columns:2,
				items:[]
			},{
				xtype:'checkbox',
				name:'export_hidden',
				hideLabel:true,
				boxLabel:GO.lang.exportHiddenColumns
			}],

	show : function(config) {

		GO.ExportQueryDialog.superclass.show.call(this);

		var config = config || {};

		Ext.apply(this, config);

	},

	beforeRequest : function() {
		var columns = [];

		var exportHidden = (this.showAllFields) ? true : this.formPanel.form.findField('export_hidden').getValue();

		if (this.colModel) {
			for (var i = 0; i < this.colModel.getColumnCount(); i++) {
				var c = this.colModel.config[i];
				if ((exportHidden || !c.hidden) && !c.hideInExport)
					columns.push(c.dataIndex + ':' + c.header);
			}
		}

		if (GO.util.empty(this.title))
			this.title = this.query

		Ext.apply(this.loadParams, {
			task : 'email_export_query',
			query : this.query,
			columns : columns.join(','),
			title : this.title
		});

		if (this.subtitle) {
			this.loadParams.subtitle = this.subtitle;
		}

		if (this.text) {
			this.loadParams.text = this.text;
		}

		var values = this.formPanel.form.getValues();
		Ext.apply(this.loadParams, values);
	}
});