/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: HoursDialog.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.projects.HoursDialog = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	
	this.buildForm();
	
	var focusName = function(){
		this.unitsField.focus();		
	};
	
	
		
	
	

	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=400;
	config.autoHeight=true;
	config.closeAction='hide';
	config.title= GO.lang.strHours;					
	config.items= this.formPanel;
	config.focus= focusName.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm();
				this.hide();
			},
			scope: this
		},{
			text: GO.lang['cmdApply'],
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
		}					
	];

	
	GO.projects.HoursDialog.superclass.constructor.call(this, config);
	
	this.render(Ext.getBody());
	
	this.addEvents({'save' : true});	
}

Ext.extend(GO.projects.HoursDialog, Ext.Window,{

	
	show : function (config) {
		
		//this.maximize();
		
		if(!config)
		{
			config={};
		}
		
		
		if(!config.hours_id)
		{
			config.hours_id=0;			
		}
			
		this.setHoursId(config.hours_id);
		
		if(config.hours_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.projects.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					this.setValues(config.values);
					GO.projects.HoursDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
				
			});
		}else 
		{
			
			this.formPanel.form.reset();			
			this.setValues(config.values);
			this.selectProject.selectFirst();
			this.selectFee.selectFirst();
			GO.projects.HoursDialog.superclass.show.call(this);
		}
	},
	
	setWritePermission : function(writePermission)
	{
		this.buttons[0].setDisabled(!writePermission);
		this.buttons[1].setDisabled(!writePermission);
	},
	
	setValues : function(values)
	{
		if(values)
		{
			for(var key in values)
			{
				var field = this.formPanel.form.findField(key);
				if(field)
				{
					field.setValue(values[key]);
				}
			}
		}
		
	},
	setHoursId : function(hours_id)
	{
		this.formPanel.form.baseParams['hours_id']=hours_id;
		this.hours_id=hours_id;	
	},
	
	submitForm : function(){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.projects.url+'action.php',
			params: {'task' : 'save_hours'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				
				if(action.result.hours_id)
				{
					this.setHoursId(action.result.hours_id);					
				}
				
				this.fireEvent('save', this);					
			},		
			failure: function(form, action) {
				Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
			},
			scope: this
		});
		
	},
	
	
	buildForm : function () {
		

		this.unitsField = new GO.form.NumberField({
        name: 'units',
      	width: 40,
        allowBlank:false,
        fieldLabel: GO.projects.lang.units,
        value: GO.util.numberFormat("0",2,GO.settings.decimal_seperator, GO.settings.thousands_seperator)
  	});  	      		

    var comments = new Ext.form.TextArea({
        name: 'comments',
      	anchor:'100%',
      	height:100,
        allowBlank:true,
        fieldLabel: GO.lang.strDescription
    	});
    	
    var now = new Date();
    var date = new Ext.form.DateField({    	
				name: 'date',
				width:100,
				format: GO.settings['date_format'],
				allowBlank:false,
				fieldLabel: GO.lang['strDate'],
				value: now.format(GO.settings.date_format)
				});
    	
    
    this.selectProject = new GO.projects.SelectProject();
    this.selectFee = new GO.projects.SelectFee();
		
    this.formPanel = new Ext.form.FormPanel(
		{
			url: GO.settings.modules.projects.url+'action.php',
			border: false,
			cls:'go-form-panel',
			baseParams: {task: 'hours'},
			autoHeight:true,
      items: [
      	this.unitsField,
      	date,
      	this.selectProject,
      	this.selectFee,
      	comments
      ]
		});
	}
});