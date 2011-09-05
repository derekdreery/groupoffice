GO.wordpress.AdminDialog = function(config){

	config = config || {};

	config.width=400;
	config.autoHeight=true;
	
	config.title=GO.lang.cmdSettings;

	/*var formItems=[];

	var supported=[2,5];
	//GO.linkTypes is defined in /default_scripts.inc.php
	for(var i=0;i<GO.linkTypes.length;i++){
		if(supported.indexOf(GO.linkTypes[i].id)>-1){
			formItems.push({
				xtype:'textfield',
				fieldLabel:GO.linkTypes[i].name,
				name: 'wp_category_'+GO.linkTypes[i].id,
				anchor:'100%'
			});
		}
	}

	this.linkTypesFieldset = new Ext.form.FieldSet({
		title:'Publish categories',
		autoHeight:true,
		items:formItems
	});*/

	this.formPanel = new Ext.FormPanel({
		waitMsgTarget:true,
		cls:'go-form-panel',		
		url:GO.settings.modules.wordpress.url+'action.php',		
		items:[{
			xtype:'textfield',
			fieldLabel:'Wordpress URL',
			name:'wp_url',
			anchor:'100%'
		}/*,this.linkTypesFieldset*/]
	});

	config.items=[this.formPanel];	

	config.listeners={
		show : function(){
			this.formPanel.form.load({
				url:GO.settings.modules.wordpress.url+'json.php',
				waitMsg:GO.lang.waitMsgLoad
			});
		},
		scope:this
	};

	config.buttons=[{
			text: GO.lang.cmdOk,
			handler: function(){
				this.formPanel.form.submit({
					waitMsg:GO.lang.waitMsgSave,
					success:function(){
						this.hide();
					},
					scope:this
				});
			},
			scope:this
		}];

	GO.wordpress.AdminDialog.superclass.constructor.call(this, config);
}

Ext.extend(GO.wordpress.AdminDialog, GO.Window);