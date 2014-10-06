GO.backupmanager.PublishDialog = function(config){
	
    if(!config)
    {
        config={};
    }
	
    this.buildForm();
	
    var focusFirstField = function(){
        this.items.items[0].focus();
    };
	
    config.layout='fit';
    config.title=GO.backupmanager.lang.publishkey;
    config.modal=true;
    config.border=false;
    config.width=400;
    config.autoHeight=true;
    config.resizable=false;
    config.plain=true;
    config.shadow=false,
    config.closeAction='hide';
    config.items=this.formPanel;
    config.focus=focusFirstField.createDelegate(this);
    config.buttons=[{
        text: GO.lang['cmdOk'],
        handler: function()
        {
            this.submitForm(true);
        },
        scope: this
    },{
        text: GO.lang['cmdClose'],
        handler: function()
        {
            this.hide();
        },
        scope:this
    }];
	
    GO.backupmanager.PublishDialog.superclass.constructor.call(this, config);
	
    this.addEvents({
        'save' : true
    });
}

Ext.extend(GO.backupmanager.PublishDialog, Ext.Window,{
		
    show : function(values)
    {
        if(!this.rendered)
            this.render(Ext.getBody());

        this.formPanel.getForm().reset();
        
        this.formPanel.getForm().setValues(values);

        this.formPanel.baseParams.rmachine = values.rmachine;
        this.formPanel.baseParams.rport = values.rport;
        this.formPanel.baseParams.ruser = values.ruser;
				this.formPanel.baseParams.rtarget = values.rtarget;
        
        GO.backupmanager.PublishDialog.superclass.show.call(this);
    },    
    submitForm : function(hide)
    {
        this.formPanel.form.submit(
        {
            url:GO.settings.modules.backupmanager.url+'action.php',
            params: {
                task : 'scp_key'
            },
            waitMsg:GO.lang['waitMsgSave'],
            success:function(form, action)
            {			
                if(hide)
                {
                    this.hide();                 
                }

								this.fireEvent('publish');

                Ext.MessageBox.alert(GO.lang['strSuccess'], GO.backupmanager.lang.publishSuccess);
            },
            failure: function(form, action)
            {
                if(action.failureType == 'client')
                {
                    Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);
                } else {
                    Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
                }
            },
            scope: this
        });
    },
    buildForm : function ()
    {			  
        this.formPanel = new Ext.form.FormPanel({
            cls:'go-form-panel',
            anchor:'100% 100%',
            bodyStyle:'padding:5px',
            waitMsgTarget:true,
            autoHeight:true,
            url:GO.settings.modules.backupmanager.url+'action.php',
            border:false,
            baseParams:{
                task:'type'
            },
            defaults:{anchor: '95%'},
            defaultType:'textfield',
            labelWidth:100,
            items:[{
                fieldLabel:GO.backupmanager.lang.rmachine,
                name:'rmachine',
                allowBlank:false,
                disabled:true
            },{
                fieldLabel:GO.backupmanager.lang.rport,
                name:'rport',
                allowBlank:false,
                disabled:true
            },{
                fieldLabel:GO.lang['strUsername'],
                name:'ruser',
                allowBlank:false,
                disabled:true
            },{
                fieldLabel:GO.backupmanager.lang.rpassword,
                name:'rpassword',
                allowBlank:false,
                inputType:'password'
            }]
        });
    }
});