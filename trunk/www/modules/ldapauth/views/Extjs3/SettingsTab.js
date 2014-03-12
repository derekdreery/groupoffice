/**
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Michael de Hart <mdhart@intermesh.nl>
 */
GO.ldapauth.SettingsTab = Ext.extend(Ext.Panel, {
	
	extraPanels: [],
	
	initComponent: function() {

		this.formPanel = new Ext.Panel({
			layout : 'form',
			labelWidth: 140
		});
		Ext.apply(this, {
			autoScroll : true,
			cls: 'go-form-panel',
			title : 'LDAP', //The first Tab
			items : this.formPanel
		});

		GO.ldapauth.SettingsTab.superclass.initComponent.call(this);
	},
	onLoadSettings: function(action) {
		//when settings are loaded
		if(!action.result.data.ldap_fields || action.result.data.ldap_fields.length===0 ){
			this.setDisabled(true);
		}
		else if(action.result.data.ldap_fields) {
			this.setDisabled(false);
			var fields = action.result.data.ldap_fields;
			//clear panel and add new services with there respective values
			
			var currentPanel = this.formPanel;
			for(var i in fields) {
				var fieldType = fields[i][0],
					field = {};
				field.name = fields[i][1] || '';
				var value = action.result.data[field.name.toLowerCase().replace(/\[\]/,"")] || null;
				if(!fieldType)
					continue;
				
				//Add new tab
				if(fieldType=='tab') {
					var name = fields[i]['text'] || 'NO text';
					if(i==0){
						currentPanel = this.formPanel;
						this.setTitle(name);
					} else {
						if(!this.extraPanels[name]) {
							this.extraPanels[name] = currentPanel = this.formPanel=new Ext.Panel({
								id: name,
								title: name,
								autoScroll : true,
								layout : 'form',
								cls: 'go-form-panel',
								labelWidth: 140
							});
						} else
							currentPanel = this.extraPanels[name];
						
						GO.mainLayout.personalSettingsDialog._tabPanel.add(currentPanel);
						GO.mainLayout.personalSettingsDialog._tabPanel.doLayout();
						//GO.moduleManager.addSettingsPanel(field.name, this.formPanel);
					}
					currentPanel.removeAll();
					continue;
				}
				
				switch(fieldType) {
					case 'text': 
						field.xtype='textfield';
						field.fieldLabel = fields[i]['label'] || '';
						field.value = value;
						field.anchor ='100%';
						break;
					case 'textarea': 
						field.xtype='textarea'; 
						field.height=100;
						field.fieldLabel = fields[i]['label'] || '';
						field.value = value;
						field.anchor ='100%';
						break;
					case 'checkbox': 
						
						if(typeof(fields[i]['onValue'])=='undefined')
							fields[i]['onValue']='true';
						
						if(typeof(fields[i]['offValue'])=='undefined')
							fields[i]['onValue']='false';
						
						if(!Ext.isArray(value)){
							value = [value];
						}

						
						field.xtype='xcheckbox'; 
						field.boxLabel = fields[i]['label'] || '';
						field.submitOnValue=fields[i]['onValue'];
						field.submitOffValue=fields[i]['offValue'];

						
						if(!GO.util.empty(field.submitOnValue)){
							field.checked = value && value.indexOf(field.submitOnValue)!==-1 ? true : false;
						}else if (!GO.util.empty(field.submitOffValue)){
							field.checked = value && value.indexOf(field.submitOffValue)===-1 ? true : false;
						}
	
						break;
					case 'list': 
						field.xtype='listfield'; 
						field.fieldLabel = fields[i]['label'] || '';
						field.value = value;
						field.anchor ='100%';
						break;
					case 'heading': 
						field.xtype='displayfield';
						field.html = fields[i]['text'] || '';
						field.hideLabel=true;
						field.html = '<b>'+fields[i]['text']+'</b><hr style="margin: 0px 0 10px 0;">' || '';
						break;
					case 'display': 
						field.xtype='displayfield';
						field.html = fields[i]['text'] || '';
						break;
				}
				//field.text = fields[i]['text'] || '';
				
				//console.log(field);
				currentPanel.add(field);
				currentPanel.doLayout();
			}
			//this.serviceFieldset.items = this.serviceFields;
			
		}
		
	},
	onSaveSettings: function() {
		//when settings are saved
		
	}
});

GO.mainLayout.onReady(function() {
	GO.moduleManager.addSettingsPanel('ldapauth', GO.ldapauth.SettingsTab);
});