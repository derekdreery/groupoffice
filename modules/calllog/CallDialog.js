GO.calllog.CallDialog = function(config){

	if(!config)
	{
		config = {};
	}

	this.buildForm();

	var focusFirstField = function(){
		//this.formPanel.items.items[0].focus();
	};
    
	config.layout='fit';
	config.title=GO.calllog.lang.call;
	config.modal=false;
	config.border=false;
	config.width=800;
	config.autoHeight=true;
	config.resizable=false;
	config.plain=true;
	config.shadow=false,
	config.closeAction='hide';
	config.items=this.formPanel;
	config.focus=focusFirstField.createDelegate(this);
	config.buttons=[{
		text:GO.lang['cmdOk'],
		handler: function()
		{
			this.submitForm(true)
		},
		scope: this
	},/*{
		text:GO.lang['cmdApply'],
		handler: function()
		{
			this.submitForm(false)
		},
		scope: this
	},*/{
		text:GO.lang['cmdClose'],
		handler: function()
		{
			this.hide()
		},
		scope: this
	}];
		
	GO.calllog.CallDialog.superclass.constructor.call(this,config);
	
	this.addEvents({'save' : true});
}

Ext.extend(GO.calllog.CallDialog, Ext.Window, {

	call_id:0,
	show : function (record)
	{		
		if(!this.rendered)
			this.render(Ext.getBody());
		
		this.call_id = (record) ? record.id : 0;		
		if(!record)
		{
			record = {};
			this.formPanel.form.reset();

			var date = new Date();
			var i = parseInt(date.format("i"));

			if (i > 45) {
				i = '45';
			} else if (i > 30) {
				i = '30';
			} else if (i > 15) {
				i = '15';
			} else {
				i = '00';
			}

			record['date'] = new Date();
			record['time'] = date.format(GO.settings.time_format);
		}
		
		this.formPanel.form.setValues(record);

		GO.calllog.CallDialog.superclass.show.call(this);
	},

	submitForm : function(hide)
	{
		this.formPanel.form.submit(
		{		
			url:GO.settings.modules.calllog.url+'json.php',
			params: {
				task:'save_call',
				id:this.call_id
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action)
			{
				if(action.result.id)
				{
					this.call_id=action.result.id;
				}
			
				this.fireEvent('save');
				
				if(hide)
				{
					this.hide();
				}
			},
			failure: function(form, action) 
			{
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
	
	buildForm : function () 
	{

		this.dateField = new Ext.form.DateField({
			name : 'date',
			width : 100,
			format : GO.settings['date_format'],
			fieldLabel: GO.lang.strDate,
			allowBlank : false
		});
		
		this.timeField = new Ext.form.TimeField({
			increment: 15,
			format:GO.settings.time_format,
			name:'time',
			width:80,
			fieldLabel: GO.lang.strTime,
			autoSelect :true
		});				

		var fieldset = new Ext.form.FieldSet({
			title:GO.lang.strProperties,
			cls:'go-form-panel',
			anchor:'100% 100%',			
			defaults:{
				anchor:'-20',
				labelWidth:140
			},
			defaultType:'textfield',
			collapsed:false,
			items:[this.dateField,this.timeField,
			{
				fieldLabel: GO.lang.strName,
				name: 'name',
				allowBlank:false
			},{
				fieldLabel: GO.lang.strCompany,
				name: 'company'
			},{
				fieldLabel: GO.lang.strPhone,
				name: 'phone'
			},{
				fieldLabel: GO.lang.strEmail,
				name: 'email',
				vtype:'emailAddress'
			},{
				fieldLabel: GO.lang.strDescription,
				name: 'description',
				xtype:'textarea'
			}]
		});

		var items_left_col=[];
		var items_right_col=[];
		items_left_col.push(fieldset);

		if(GO.customfields && GO.customfields.types["18"])
		{			
			for(var i=0;i<GO.customfields.types["18"].panels.length;i++)
			{
				var cat = GO.customfields.types["18"].panels[i];
				var cf_items=[];
							
				for(var j=0; j<cat.customfields.length; j++)
				{
					cf_items.push(GO.customfields.getFormField(cat.customfields[j]));
				}
				
				fieldset = new Ext.form.FieldSet({
					title:cat.title,
					cls:'go-form-panel',
					anchor:'100% 100%',
					defaults:{
						anchor:'100%',
						labelWidth:140
					},
					defaultType:'textfield',
					collapsed:false,					
					items:cf_items
				});

				if(i%2 || i == 0)
				{
					items_right_col.push(fieldset);
				}else
				{
					items_left_col.push(fieldset);
				}				
			}
		}
		
		this.formPanel = new Ext.FormPanel({
			cls:'go-form-panel',
			anchor:'100% 100%',
			bodyStyle:'padding:10px',
			defaults:{
				anchor: '100%',
				border:false
			},
			autoHeight:true,
			waitMsgTarget:true,			
			layout:'column',
			items: [
			{
				columnWidth:.5,
				items:items_left_col
			},{
				columnWidth:.5,
				items:items_right_col
			}]
		});
	}	
});