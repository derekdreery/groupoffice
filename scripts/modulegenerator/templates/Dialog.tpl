/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: Dialog.tpl 2276 2008-07-04 12:22:20Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */

 
GO.{module}.{friendly_single_ucfirst}Dialog = function(config){	
	if(!config)
	{
		config={};
	}
	
	this.buildForm();
	
	var focusFirstField = function(){
		this.propertiesPanel.items.items[0].focus();
	};
	
	config.collapsible=true;
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=700;
	config.height=500;
	config.closeAction='hide';
	config.title= GO.{module}.lang.{friendly_single_js};					
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm(true);
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
	
	GO.{module}.{friendly_single_ucfirst}Dialog.superclass.constructor.call(this, config);

	this.addEvents({'save' : true});	
}

Ext.extend(GO.{module}.{friendly_single_ucfirst}Dialog, Ext.Window,{
	
	show : function ({friendly_single}_id, config) {

		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		
		<gotpl if="$link_type &gt; 0">
		delete this.link_config;
		</gotpl>
		this.formPanel.form.reset();
		
		this.tabPanel.setActiveTab(0);
		
		if(!{friendly_single}_id)
		{
			{friendly_single}_id=0;			
		}
			
		this.set{friendly_single_ucfirst}Id({friendly_single}_id);
		
		if(this.{friendly_single}_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.{module}.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{					
					<gotpl if="$authenticate">
					this.setWritePermission(action.result.data.write_permission);					
					this.readPermissionsTab.setAcl(action.result.data.acl_read);
					this.writePermissionsTab.setAcl(action.result.data.acl_write);
					</gotpl>	
					<gotpl if="$user_id">
					this.selectUser.setRemoteText(action.result.data.user_name);
					</gotpl>			
					
					GO.{module}.{friendly_single_ucfirst}Dialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this				
			});
		}else 
		{
			<gotpl if="$authenticate">
			this.setWritePermission(true);
			this.readPermissionsTab.setAcl(0);
			this.writePermissionsTab.setAcl(0);
			</gotpl>
			GO.{module}.{friendly_single_ucfirst}Dialog.superclass.show.call(this);
		}
		
		<gotpl if="$link_type &gt; 0">
		//if the newMenuButton from another passed a linkTypeId then set this value in the select link field
		if(config && config.link_config)
		{
			this.link_config=config.link_config;
			if(config.link_config.type_id)
			{
				this.selectLinkField.setValue(config.link_config.type_id);
				this.selectLinkField.setRemoteText(config.link_config.text);
			}
		}
		</gotpl>
	},
	
	<gotpl if="$authenticate">
	setWritePermission : function(writePermission)
	{
		this.buttons[0].setDisabled(!writePermission);
		this.buttons[1].setDisabled(!writePermission);
		</gotpl>
		<gotpl if="$authenticate">
	},
	</gotpl>
	set{friendly_single_ucfirst}Id : function({friendly_single}_id)
	{
		this.formPanel.form.baseParams['{friendly_single}_id']={friendly_single}_id;
		this.{friendly_single}_id={friendly_single}_id;
		<gotpl if="$link_type&gt;0">
		this.selectLinkField.container.up('div.x-form-item').setDisplayed({friendly_single}_id==0);
		</gotpl>
	},
	
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.{module}.url+'action.php',
			params: {'task' : 'save_{friendly_single}'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
			
				if(action.result.{friendly_single}_id)
				{
					this.set{friendly_single_ucfirst}Id(action.result.{friendly_single}_id);
					<gotpl if="$authenticate">
					this.readPermissionsTab.setAcl(action.result.acl_read);
					this.writePermissionsTab.setAcl(action.result.acl_write);
					</gotpl>					
				}				
								
				this.fireEvent('save', this, this.{friendly_single}_id);				
				if(hide)
				{
					this.hide();	
				}
				<gotpl if="$link_type&gt;0">
				if(this.link_config && this.link_config.callback)
				{					
					this.link_config.callback.call(this);					
				}</gotpl>									
			},		
			failure: function(form, action) {
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
	
	buildForm : function () {
		<gotpl if="$link_type &gt; 0">
		this.selectLinkField = new GO.form.SelectLink();
		</gotpl>		
		this.propertiesPanel = new Ext.Panel({
			title:GO.lang['strProperties'],			
			cls:'go-form-panel',			
			layout:'form',
			autoScroll:true,
			items:[<gotpl if="$link_type &gt; 0">this.selectLinkField,</gotpl>{FORMFIELDS}]
		});

		var items  = [this.propertiesPanel];		
    
		<gotpl if="$link_type &gt; 0">
		if(GO.customfields && GO.customfields.types["{link_type}"])
		{
			for(var i=0;i<GO.customfields.types["{link_type}"].panels.length;i++)
			{			  	
				items.push(GO.customfields.types["{link_type}"].panels[i]);
			}
		}
		</gotpl>		
		<gotpl if="$authenticate">
    this.readPermissionsTab = new GO.grid.PermissionsPanel({
			
		});
	
		this.writePermissionsTab = new GO.grid.PermissionsPanel({
			title: GO.lang['strWritePermissions']
		});
    
    items.push(this.readPermissionsTab);
    items.push(this.writePermissionsTab);
		</gotpl>
 
    this.tabPanel = new Ext.TabPanel({
      activeTab: 0,      
      deferredRender: false,
    	border: false,
      items: items,
      anchor: '100% 100%'
    });   
    
    this.formPanel = new Ext.form.FormPanel({
	    waitMsgTarget:true,
			url: GO.settings.modules.{module}.url+'action.php',
			border: false,
			baseParams: {task: '{friendly_single}'},				
			items:this.tabPanel				
		});   
	}
});