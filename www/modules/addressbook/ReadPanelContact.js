GO.addressbook.ContactReadPanel = function(config)
{
	if(!config)
	{
		config={};
	}
	config.width=450;
	config.split=true;
	config.autoScroll=true;
	config.layout='fit';
	
	Ext.apply(this, config);
	
	this.newMenuButton = new GO.NewMenuButton();	
	
	if(GO.mailings)
	{	
		this.newOODoc = new GO.mailings.NewOODocumentMenuItem();
		this.newMenuButton.menu.add(this.newOODoc);
		
		
		GO.mailings.ooTemplatesStore.on('load', function(){
			this.newOODoc.setDisabled(GO.mailings.ooTemplatesStore.getCount() == 0);
		}, this);
	}
	
	
	this.tbar = [
		this.editButton = new Ext.Button({
			iconCls: 'btn-edit', 
			text: GO.lang['cmdEdit'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				GO.addressbook.contactDialog.show(this.data.id);
			}, 
			scope: this,
			disabled : true
		}),{
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			handler: function(){
				GO.linkBrowser.show({link_id: this.data.id,link_type: "2",folder_id: "0"});				
			},
			scope: this
		},
		this.newMenuButton
	];	
	
	
	GO.addressbook.ContactReadPanel.superclass.constructor.call(this);		
}


Ext.extend(GO.addressbook.ContactReadPanel, Ext.Panel,{
	
	initComponent : function(){
		
		
		var template = 
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">' + GO.addressbook.lang['cmdContactDetailsFor'] + ' <b>{full_name}</b></td>'+
					'</tr>'+
					
					'<tr>'+
						
						// PERSONAL DETAILS+ 1e KOLOM
						'<td valign="top">'+
							'<table cellpadding="0" cellspacing="0" border="0">'+
								//NAME
								'<tr>'+
									'<td>' +
									'<tpl if="title.length">'+
									'{title} '+
									'</tpl>'+
									'{full_name}'+
								
									//ADDRESS															
									'<tpl if="address.length || address_no.length">'+
										'<br />{[this.GoogleMapsCityStreet(values)]}'+															
									'</tpl>'+
									
									//ZIP							
									'<tpl if="zip.length || city.length">'+
										'<br />{zip} {city}'+			
									'</tpl>'+
									
									//STATE							
									'<tpl if="state.length">'+
										'<br />{state}'+						
									'</tpl>'+
									
									//COUNTRY							
									'<tpl if="country.length">'+
										'<br />{country}'+						
									'</tpl>'+
									'</td>'+
								'</tr>'+					
							'</table>'+
						'</td>'+
						
						// PERSONAL DETAILS+ 2e KOLOM
						'<td valign="top" class="contactCompanyDetailsPanelKolom">'+
							'<table cellpadding="0" cellspacing="0" border="0">'+
								
								//INITIALS
								'<tpl if="initials.length">'+
									'<tr>'+
										'<td>' + GO.lang['strInitials'] + ':</td><td> {initials}</td>'+
									'</tr>'+						
								'</tpl>'+
	
								//BIRTHDAY							
								'<tpl if="birthday.length">'+
									'<tr>'+
										'<td>' + GO.lang['strBirthday'] + ':</td><td> {birthday}</td>'+
									'</tr>'+						
								'</tpl>'+
							'</table>'+
						'</td>'+
					'</tr>'+

					
				'<tpl if="this.isContactFieldset(values)">'+
					
						//CONTACT DETAILS
						'<tr>'+
							'<td colspan="2" class="display-panel-heading">' + GO.addressbook.lang['cmdFieldsetContact'] + '</td>'+
						'</tr>'+
						
						'<tr>'+
							// CONTACT DETAILS+ 1e KOLOM
							'<td valign="top" class="contactCompanyDetailsPanelKolom60">'+
								'<table cellpadding="0" cellspacing="0" border="0">'+
									
									//EMAIL							
									'<tpl if="email.length">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ':</td><td><a href="#" onclick="GO.email.Composer.show({values : {to: \'{email}\'}})">{email}</a></td>'+
										'</tr>'+						
									'</tpl>'+
		
									//EMAIL2							
									'<tpl if="email2.length">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ' 2:</td><td><a href="#" onclick="GO.email.Composer.show({values : {to: \'{email2}\'}})">{email2}</a></td>'+
										'</tr>'+						
									'</tpl>'+
		
									//EMAIL3							
									'<tpl if="email3.length">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ' 3:</td><td><a href="#" onclick="GO.email.Composer.show({values : {to: \'{email3}\'}})">{email3}</a></td>'+
										'</tr>'+						
									'</tpl>'+
									
									'<tpl if="this.isPhoneFieldset(values)">'+
										'<tr><td colspan="2">&nbsp;</td></tr>'+
										
										//PHONE							
										'<tpl if="home_phone.length">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strPhone'] + ':</td><td><a href="callto:{home_phone}+type=phone">{home_phone}</a></td>'+
											'</tr>'+						
										'</tpl>'+

										//CELLULAR							
										'<tpl if="cellular.length">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strCellular'] + ':</td><td><a href="callto:{cellular}+type=phone">{cellular}</a></td>'+
											'</tr>'+						
										'</tpl>'+
													
										//FAX							
										'<tpl if="fax.length">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strFax'] + ':</td><td>{fax}</td>'+
											'</tr>'+						
										'</tpl>'+
										
									'</tpl>'+ //end this.isPhoneFieldset()
								'</table>'+
							'</td>'+
							
							'<tpl if="this.isWorkPhoneFieldset(values)">'+
							
								// CONTACT DETAILS+ 2e KOLOM
								'<td valign="top" class="contactCompanyDetailsPanelKolom40">'+
									'<table cellpadding="0" cellspacing="0" border="0">'+
										
										//PHONE WORK							
										'<tpl if="work_phone.length">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strWorkPhone'] + ':</td><td><a href="callto:{work_phone}+type=phone">{work_phone}</a></td>'+
											'</tr>'+						
										'</tpl>'+
			
										//FAX WORK							
										'<tpl if="work_fax.length">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strWorkFax'] + ':</td><td>{work_fax}</td>'+
											'</tr>'+						
										'</tpl>'+
										
									'</table>'+							
								'</td>'+
							
							'</tpl>'+ //end this.isPhoneFieldset()
							
						'</tr>'+

				'</tpl>'+
				
					
				'<tpl if="this.isWorkFieldset(values)">'+


						//WORK DETAILS
						'<tr>'+
							'<td colspan="2" class="display-panel-heading">' + GO.addressbook.lang['cmdFieldsetWork'] + '</td>'+
						'</tr>'+
						
						'<tr>'+
							// CONTACT DETAILS+ 1e KOLOM
							'<td colspan="2" valign="top" class="contactCompanyDetailsPanelKolom60">'+
								'<table cellpadding="0" cellspacing="0" border="0">'+
								
									//COMPANY							
									'<tpl if="company_name.length">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strCompany'] + ':</td><td><a href="#" onclick="GO.linkHandlers[3].call(this,{company_id});">{company_name}</a></td>'+
										'</tr>'+						
									'</tpl>'+

									//FUNCTION							
									'<tpl if="values[\'function\'].length">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strFunction'] + ':</td><td>{function}</td>'+
										'</tr>'+						
									'</tpl>'+

									//DEPARTMENT							
									'<tpl if="department.length">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strDepartment'] + ':</td><td>{department}</td>'+
										'</tr>'+						
									'</tpl>'+																	

								'</table>'+							
							'</td>'+							
						'</tr>'+
				
				'</tpl>'+
				
				'<tpl if="values[\'comment\'].length">'+

				//WORK DETAILS
				'<tr>'+
					'<td class="display-panel-heading" colspan="2">' + GO.addressbook.lang['cmdFormLabelComment'] + '</td>'+
				'</tr>'+
				'<tr>'+
					'<td colspan="2">{comment}</td>'+
				'</tr>'+
		
				'</tpl>'+
				'</table>'+
				GO.linksTemplate;
				
				if(GO.customfields)
				{
					template +=GO.customfields.displayPanelTemplate;
				}
				
			
		var config = {
			isContactFieldset: function(values){
				if(values['email'].length ||
					values['email2'].length ||
					values['email3'].length ||
					values['home_phone'].length ||
					values['fax'].length ||
					values['cellular'].length ||
					values['work_phone'].length ||
					values['work_fax'].length	)
				{
					return true;
				} else {
					return false;
				}
			},			
		isPhoneFieldset : function(values)
			{
				if(values['home_phone'].length ||
					values['fax'].length ||
					values['cellular'].length )
				{
					return true;
				} else {
					return false;
				}
			},
			isWorkPhoneFieldset : function(values)
			{
				if(values['work_phone'].length ||
					values['work_fax'].length )
				{
					return true;
				} else {
					return false;
				}
			},
			isWorkFieldset : function(values)
			{
				if(values['company_name'].length ||
					values['function'].length ||
					values['department'].length )
				{
					return true;
				} else {
					return false;
				}
			},
			GoogleMapsCityStreet : function(values)
			{
				var google_url = 'http://maps.google.com/maps?q=';
				
				if(values['address'].length && values['city'].length)
				{
					if(values['address_no'].length)
					{
						return '<a href="' + google_url + values['address'] + '+' + values['address_no'] + '+' + values['city'] + '" target="_blank" >' + values['address'] + ' ' + values['address_no'] + '</a>';	
					} else {
						return '<a href="' + google_url + values['address'] + '+' + values['city'] + '" target="_blank" >' + values['address'] + '</a>';						
					}
				} else {
					return values['address'] + ' ' + values['address_no'];
				}
			}
		};		
		
		Ext.apply(config, GO.linksTemplateConfig);
		
		
		if(GO.files)
		{
			Ext.apply(config, GO.files.filesTemplateConfig);
			template += GO.files.filesTemplate;
		}
		
		this.template = new Ext.XTemplate(template, config);
		
		GO.addressbook.ContactReadPanel.superclass.initComponent.call(this);
	},
	
	
	loadContact : function(contact_id)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url: GO.settings.modules.addressbook.url+'json.php',
			params: {
				contact_id: contact_id,
				task: 'load_contact_with_items'
			},
			scope: this,
			callback: function(options, success, response)
			{		
				this.body.unmask();
				
				var data = Ext.decode(response.responseText);
				this.setData(data.data);
			}
		});
		
	},
	
	reload : function()
	{
		if(this.data)
			this.loadContact(this.data.id);
	},
	
	setData : function(data)
	{
		this.data=data;
		this.editButton.setDisabled(!data.write_permission);
		this.template.overwrite(this.body, data);	
		
		if(data.write_permission)
		{
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:2,
				text: this.data.first_name+' '+this.data.last_name,
				callback:function(){
					this.loadContact(this.data.id);				
				},
				scope:this
			});
		}
	}
	
});			