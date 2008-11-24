GO.addressbook.CompanyReadPanel = Ext.extend(GO.DisplayPanel,{
	
	link_type : 3,
	
	loadParams : {task: 'load_company_with_items'},
	
	idParam : 'company_id',
	
	loadUrl : GO.settings.modules.addressbook.url+'json.php',
	
	editHandler : function(){
		GO.addressbook.companyDialog.show(this.data.id);
		this.addSaveHandler(GO.addressbook.companyDialog);
	},	
	
	initComponent : function(){
 
			this.template = '<div>'+
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<tpl if="this.isCompanySecondColumn(values)">'+
							'<td colspan="2" valign="top" class="display-panel-heading">'+
						'</tpl>'+

						'<tpl if="this.isCompanySecondColumn(values) == false">'+
							'<td valign="top" class="display-panel-heading">'+
						'</tpl>'+
						
							GO.addressbook.lang['cmdCompanyDetailsFor'] + ' <b>{name}</b>'+
						'</td>'+
					'</tr>'+
					
					'<tr>'+	
						// COMPANY DETAILS+ 1e KOLOM
						'<tpl if="this.isCompanySecondColumn(values)">'+
							'<tpl if="this.isBankVat(values)">'+
								'<td valign="top" class="contactCompanyDetailsPanelKolom">'+
							'</tpl>'+
							
							'<tpl if="this.isBankVat(values) == false">'+
								'<td colspan="2" valign="top" class="contactCompanyDetailsPanelKolom100">'+
							'</tpl>'+							
						'</tpl>'+
						
						'<tpl if="this.isCompanySecondColumn(values) == false">'+
							'<td valign="top" class="contactCompanyDetailsPanelKolom100">'+
						'</tpl>'+
																		
							'<table cellpadding="0" cellspacing="0" border="0">'+						
								
								//PHONE							
								'<tpl if="phone.length">'+
									'<tr>'+
										'<td class="contactCompanyLabelWidth">' + GO.lang['strPhone'] + ':</td><td><a href="callto:{phone}+type=phone">{phone}</a></td>'+
									'</tr>'+						
								'</tpl>'+

								//FAX							
								'<tpl if="fax.length">'+
									'<tr>'+
										'<td class="contactCompanyLabelWidth">' + GO.lang['strFax'] + ':</td><td>{fax}</td>'+
									'</tr>'+						
								'</tpl>'+								
								
								//EMAIL							
								'<tpl if="email.length">'+
									'<tr>'+
										'<td class="contactCompanyLabelWidth">' + GO.lang['strEmail'] + ':</td><td><a href="#" onclick="GO.email.Composer.show({values : {to: \'{email}\'}})">{email}</a></td>'+
									'</tr>'+						
								'</tpl>'+		
								
								// LEGE REGEL
								'<tr><td colspan="2">&nbsp;</td></tr>'+																
											
								//HOMEPAGE							
								'<tpl if="homepage.length">'+
									'<tr>'+
										'<td class="contactCompanyLabelWidth">' + GO.lang['strHomepage'] + ':</td><td>&nbsp;<a href="{homepage}" target="_blank">{homepage}</a></td>'+
									'</tr>'+						
								'</tpl>'+																			
							'</table>'+
						'</td>'+
						
						'<tpl if="this.isBankVat(values)">'+
							// COMPANY DETAILS+ 2e KOLOM
							'<td valign="top" class="contactCompanyDetailsPanelKolom">'+
								'<table cellpadding="0" cellspacing="0" border="0">'+												
									
									//BANK_NO
									'<tpl if="bank_no.length">'+
										'<tr>'+
											'<td>' + GO.addressbook.lang['cmdFormLabelBankNo'] + ':</td><td>&nbsp;{bank_no}</td>'+
										'</tr>'+						
									'</tpl>'+
		
									//VAT_NO							
									'<tpl if="vat_no.length">'+
										'<tr>'+
											'<td>' + GO.addressbook.lang['cmdFormLabelVatNo'] + ':</td><td> {vat_no}</td>'+
										'</tr>'+						
									'</tpl>'+
								'</table>'+
							'</td>'+
						'</tpl>'+					
					'</tr>'+
					
					
					// CONTACT DETAILS+ 1e KOLOM
					'<tpl if="this.isAddress(values)">'+					
						'<tr>'+
							'<tpl if="this.isCompanySecondColumn(values)">'+
								'<td colspan="2" valign="top" class="display-panel-heading">'+
							'</tpl>'+
	
							'<tpl if="this.isCompanySecondColumn(values) == false">'+
								'<td valign="top" class="display-panel-heading">'+
							'</tpl>'+
							
							GO.addressbook.lang['cmdFieldsetContact']+
							'</td>'+
						'</tr>'+

						'<tr>'+
							'<tpl if="this.isAddressVisit(values)">'+
							
								'<tpl if="this.isCompanySecondColumn(values)">'+
									'<tpl if="this.isAddressPost(values)">'+
										'<td valign="top" class="contactCompanyDetailsPanelKolom">'+
									'</tpl>'+
									
									'<tpl if="this.isAddressPost(values) == false">'+
										'<td colspan="2" valign="top" class="contactCompanyDetailsPanelKolom100">'+
									'</tpl>'+							
								'</tpl>'+
								
								'<tpl if="this.isCompanySecondColumn(values) == false">'+
									'<td valign="top" class="contactCompanyDetailsPanelKolom100">'+
								'</tpl>'+
							
									'<table cellpadding="0" cellspacing="0" border="0">'+
										
										'<tr>'+
											'<td colspan="2" class="readPanelSubHeading">' + GO.addressbook.lang['cmdFieldsetVisitAddress'] + '</td>'+
										'</tr>'+
										
										// LEGE REGEL													
										'<tr>'+
											'<td>'+
										//ADDRESS															
										'<tpl if="address.length || address_no.length">'+
											'{[this.GoogleMapsCityStreet(values)]}'+				
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

									'</table>'+
								'</td>'+		
							'</tpl>'+
							
							// CONTACT DETAILS+ 2e KOLOM
							'<tpl if="this.isAddressPost(values)">'+
								'<tpl if="this.isAddressVisit(values)">'+
									'<td valign="top" class="contactCompanyDetailsPanelKolom">'+
								'</tpl>'+				
								
								'<tpl if="this.isAddressVisit(values) == false">'+
									'<td colspan="2" valign="top" class="contactCompanyDetailsPanelKolom100">'+
								'</tpl>'+
									
									'<table cellpadding="0" cellspacing="0" border="0">'+
										
										'<tr>'+
											'<td colspan="2" class="readPanelSubHeading">' + GO.addressbook.lang['cmdFieldsetPostAddress'] + '</td>'+
										'</tr>'+											
										
										// LEGE REGEL
										'<tr>'+
											'<td>'+							
										
										//ADDRESS															
										'<tpl if="post_address.length || post_address_no.length">'+
											'{post_address} {post_address_no}'+
										'</tpl>'+
										
										//ZIP							
										'<tpl if="post_zip.length || post_city.length">'+
											'<br />{post_zip} {post_city}'+
										'</tpl>'+
										
										//STATE							
										'<tpl if="post_state.length">'+
											'<br />{post_state}'+
										'</tpl>'+
										
										//COUNTRY							
										'<tpl if="post_country.length">'+
											'<br />{post_country}'+				
										'</tpl>'+

									'</table>'+
								'</td>'+														
							'</tpl>'+							
						'</tr>'+
					'</tpl>'+		
					
					'</table>'+		

					'<tpl if="comment.length">'+						
						'<table cellpadding="0" cellspacing="0" border="0" class="display-panel">'+
						'<tr>'+
							'<td class="display-panel-heading">' + GO.addressbook.lang['cmdFormLabelComment'] + '</td>'+
						'</tr>'+
						'<tr>'+
							'<td>{comment}</td>'+
						'</tr>'+
						'</table>'+
					'</tpl>'+		
					
					
					'<tpl if="employees.length">'+
						'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
						//LINK DETAILS
						'<tr>'+
							'<td colspan="4" class="display-panel-heading">'+GO.addressbook.lang.cmdPanelEmployees+'</td>'+
						'</tr>'+
						
						'<tr>'+
							'<td width="16" class="display-panel-links-header">&nbsp;</td>'+
							'<td class="table_header_links">' + GO.lang['strName'] + '</td>'+
							'<td class="table_header_links">' + GO.lang['strEmail'] + '</td>'+							
						'</tr>'+	
											
						'<tpl for="employees">'+
							'<tr>'+
								'<td><div class="go-icon go-link-icon-2"></div></td>'+
								'<td><a href="#" onclick="GO.linkHandlers[2].call(this, {id});">{name}</a></td>'+
								'<td><a href="#" onclick="GO.email.Composer.show({values : {to: \'{email}\'}})">{email}</a></td>'+
							'</tr>'+							
						'</tpl>'+	
					'</tpl>'+
								
			GO.linksTemplate;
			
		if(GO.customfields)
		{
			this.template +=GO.customfields.displayPanelTemplate;
		}
	    	
	  this.templateConfig =	{
			isCompanySecondColumn : function(values)
			{
				if(
					this.isBankVat(values) ||
					this.isAddressPost(values)
				)
				{
					return true;
				} else {
					return false;
				}
			},
			isBankVat : function(values)
			{
				if(
					values['bank_no'].length ||
					values['vat_no'].length 				
				)
				{
					return true;
				} else {
					return false;
				}
			},	
			isAddress : function(values)
			{
				if(
					values['address'].length ||
					values['address_no'].length ||
					values['zip'].length ||
					values['city'].length ||
					values['state'].length ||
					values['country'].length ||
					values['post_address'].length ||
					values['post_address_no'].length ||
					values['post_zip'].length ||
					values['post_city'].length ||
					values['post_state'].length ||
					values['post_country'].length
				)
				{
					return true;
				} else {
					return false;
				}
			},	
			isAddressVisit : function(values)
			{
				if(
					values['address'].length ||
					values['address_no'].length ||
					values['zip'].length ||
					values['city'].length ||
					values['state'].length ||
					values['country'].length
				)
				{
					return true;
				} else {
					return false;
				}
			},
			isAddressPost : function(values)
			{
				if(
					values['post_address'].length ||
					values['post_address_no'].length ||
					values['post_zip'].length ||
					values['post_city'].length ||
					values['post_state'].length ||
					values['post_country'].length					
				)
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
				
		this.template+='</div>';		
			
		GO.addressbook.CompanyReadPanel.superclass.initComponent.call(this);
		
		if(GO.mailings)
		{			
			this.newOODoc = new GO.mailings.NewOODocumentMenuItem();
			this.newMenuButton.menu.add(this.newOODoc);		
			
			GO.mailings.ooTemplatesStore.on('load', function(){
				this.newOODoc.setDisabled(GO.mailings.ooTemplatesStore.getCount() == 0);
			}, this);
		}
	}	
});