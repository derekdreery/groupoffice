GO.addressbook.ContactReadPanel = Ext.extend(GO.DisplayPanel,{
	
	link_type : 2,
	
	loadParams : {task: 'load_contact_with_items'},
	
	idParam : 'contact_id',
	
	loadUrl : GO.settings.modules.addressbook.url+'json.php',
	
	editHandler : function(){
		GO.addressbook.contactDialog.show(this.id);
		this.addSaveHandler(GO.addressbook.contactDialog);
	},	
	
	initComponent : function(){
		
		
		this.template = 
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
										'<tpl if="!GO.util.empty(title)">'+
											'{title} '+
										'</tpl>'+
										'{full_name}'+
										'<br />'+
										'<tpl if="!GO.util.empty(google_maps_link)">'+
											'<a href="{google_maps_link}" target="_blank">'+
										'</tpl>'+
										'{formatted_address}'+
										'<tpl if="!GO.util.empty(google_maps_link)">'+
											'</a>'+
										'</tpl>'+
									'</td>'+
								'</tr>'+					
							'</table>'+
						'</td>'+
						
						// PERSONAL DETAILS+ 2e KOLOM
						'<td valign="top" class="contactCompanyDetailsPanelKolom">'+
							'<table cellpadding="0" cellspacing="0" border="0">'+
								
								//INITIALS
								'<tpl if="!GO.util.empty(initials)">'+
									'<tr>'+
										'<td>' + GO.lang['strInitials'] + ':</td><td> {initials}</td>'+
									'</tr>'+						
								'</tpl>'+
	
								//BIRTHDAY							
								'<tpl if="!GO.util.empty(birthday)">'+
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
									'<tpl if="!GO.util.empty(email)">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ':</td><td>{[this.mailTo(values.email, values.full_name)]}</td>'+
										'</tr>'+						
									'</tpl>'+
		
									//EMAIL2							
									'<tpl if="!GO.util.empty(email2)">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ' 2:</td><td>{[this.mailTo(values.email2, values.full_name)]}</td>'+
										'</tr>'+						
									'</tpl>'+
		
									//EMAIL3							
									'<tpl if="!GO.util.empty(email3)">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ' 3:</td><td>{[this.mailTo(values.email3, values.full_name)]}</td>'+
										'</tr>'+						
									'</tpl>'+
									
									'<tpl if="this.isPhoneFieldset(values)">'+
										'<tr><td colspan="2">&nbsp;</td></tr>'+
										
										//PHONE							
										'<tpl if="!GO.util.empty(home_phone)">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strPhone'] + ':</td><td><a href="callto:{home_phone}+type=phone">{home_phone}</a></td>'+
											'</tr>'+						
										'</tpl>'+

										//CELLULAR							
										'<tpl if="!GO.util.empty(cellular)">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strCellular'] + ':</td><td><a href="callto:{cellular}+type=phone">{cellular}</a></td>'+
											'</tr>'+						
										'</tpl>'+
													
										//FAX							
										'<tpl if="!GO.util.empty(fax)">'+
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
										'<tpl if="!GO.util.empty(work_phone)">'+
											'<tr>'+
												'<td class="contactCompanyLabelWidth">' + GO.lang['strWorkPhone'] + ':</td><td><a href="callto:{work_phone}+type=phone">{work_phone}</a></td>'+
											'</tr>'+						
										'</tpl>'+
			
										//FAX WORK							
										'<tpl if="!GO.util.empty(work_fax)">'+
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
									'<tpl if="!GO.util.empty(company_name)">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strCompany'] + ':</td><td><a href="#" onclick="GO.linkHandlers[3].call(this,{company_id});">{company_name}</a></td>'+
										'</tr>'+						
									'</tpl>'+

									//FUNCTION							
									'<tpl if="!GO.util.empty(values[\'function\'])">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strFunction'] + ':</td><td>{function}</td>'+
										'</tr>'+						
									'</tpl>'+

									//DEPARTMENT							
									'<tpl if="!GO.util.empty(department)">'+
										'<tr>'+
											'<td class="contactCompanyLabelWidth">' + GO.lang['strDepartment'] + ':</td><td>{department}</td>'+
										'</tr>'+						
									'</tpl>'+																	

								'</table>'+							
							'</td>'+							
						'</tr>'+
				
				'</tpl>'+
				
				'<tpl if="!GO.util.empty(values[\'comment\'])">'+

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
					this.template +=GO.customfields.displayPanelTemplate;
				}
				
			
		Ext.apply(this.templateConfig, {
			addSlashes : function(str)
			{
				str = GO.util.html_entity_decode(str, 'ENT_QUOTES');
				str = GO.util.add_slashes(str);
				return str;
			},
			mailTo : function(email, name) {
			
				if(GO.email && GO.settings.modules.email.read_permission)
				{
					return '<a href="#" onclick="GO.email.showAddressMenu(event, \''+this.addSlashes(email)+'\',\''+this.addSlashes(name)+'\');">'+email+'</a>';
				}else
				{
					return '<a href="mailto:'+email+'">'+email+'</a>';
				}
			},
			
			isContactFieldset: function(values){
				if(!GO.util.empty(values['email']) ||
					!GO.util.empty(values['email2']) ||
					!GO.util.empty(values['email3']) ||
					!GO.util.empty(values['home_phone']) ||
					!GO.util.empty(values['fax']) ||
					!GO.util.empty(values['cellular']) ||
					!GO.util.empty(values['work_phone']) ||
					!GO.util.empty(values['work_fax'])	)
				{
					return true;
				} else {
					return false;
				}
			},			
		isPhoneFieldset : function(values)
			{
				if(!GO.util.empty(values['home_phone']) ||
					!GO.util.empty(values['fax']) ||
					!GO.util.empty(values['cellular']) )
				{
					return true;
				} else {
					return false;
				}
			},
			isWorkPhoneFieldset : function(values)
			{
				if(!GO.util.empty(values['work_phone']) ||
					!GO.util.empty(values['work_fax']) )
				{
					return true;
				} else {
					return false;
				}
			},
			isWorkFieldset : function(values)
			{
				if(!GO.util.empty(values['company_name']) ||
					!GO.util.empty(values['function']) ||
					!GO.util.empty(values['department']))
				{
					return true;
				} else {
					return false;
				}
			},
			GoogleMapsCityStreet : function(values)
			{
				var google_url = 'http://maps.google.com/maps?q=';
				
				if(!GO.util.empty(values['address']) && !GO.util.empty(values['city']))
				{
					if(!GO.util.empty(values['address_no']))
					{
						return '<a href="' + google_url + values['address'] + '+' + values['address_no'] + '+' + values['city'] + '" target="_blank" >' + values['address'] + ' ' + values['address_no'] + '</a>';	
					} else {
						return '<a href="' + google_url + values['address'] + '+' + values['city'] + '" target="_blank" >' + values['address'] + '</a>';						
					}
				} else {
					return values['address'] + ' ' + values['address_no'];
				}
			}
		});		
		
		Ext.apply(this.templateConfig, GO.linksTemplateConfig);
		
		
		if(GO.files)
		{
			Ext.apply(this.templateConfig, GO.files.filesTemplateConfig);
			this.template += GO.files.filesTemplate;
		}
		
		if(GO.comments)
		{
			this.template += GO.comments.displayPanelTemplate;
		}
		
		
		GO.addressbook.ContactReadPanel.superclass.initComponent.call(this);
		
		
		if(GO.mailings)
		{	
			this.newOODoc = new GO.mailings.NewOODocumentMenuItem();
			this.newOODoc.on('create', function(){this.reload();}, this);
			
			this.newMenuButton.menu.add(this.newOODoc);		
			
			GO.mailings.ooTemplatesStore.on('load', function(){
				this.newOODoc.setDisabled(GO.mailings.ooTemplatesStore.getCount() == 0);
			}, this);
		}
		
		if(GO.tasks)
		{
			this.scheduleCallItem = new GO.tasks.ScheduleCallMenuItem();
			this.newMenuButton.menu.add(this.scheduleCallItem);
		}
	},
	getLinkName : function(){
		return this.data.full_name;
	},
	setData : function(data)
	{
		GO.addressbook.ContactReadPanel.superclass.setData.call(this, data);
		
		if(GO.mailings && !GO.mailings.ooTemplatesStore.loaded)
					GO.mailings.ooTemplatesStore.load();
				
		if(data.write_permission)
		{
			if(this.scheduleCallItem)
			{				
				var name = this.data.full_name;
				
				if(this.data.work_phone!='')
				{
					name += ' ('+this.data.work_phone+')';
				}else if(this.data.cellular!='')
				{
					name += ' ('+this.data.cellular+')';
				}else if(this.data.home_phone!='')
				{
					name += ' ('+this.data.home_phone+')';
				}
				
				this.scheduleCallItem.setLinkConfig({
					name: name,
					links:[{link_id: this.data.id, link_type:2}],
					callback:this.reload,
					scope: this
				});
			}
		}
	}	
});			