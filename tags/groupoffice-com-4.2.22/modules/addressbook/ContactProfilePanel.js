GO.addressbook.ContactProfilePanel = function(config)
	{
		config = config || {};
		Ext.apply(config);

		if(!config.forUser){		
			this.formFirstName = new Ext.form.TextField(
			{
				fieldLabel: GO.lang['strFirstName'],
				name: 'first_name',
				panel: this,
				validateValue: function(val) {
					var bool = (val!='' || this.panel.formLastName.getValue()!='');
					if(!bool)
					{
						this.markInvalid(this.blankText);
					}else
					{
						this.panel.formLastName.clearInvalid();
					}
					return bool;
				}
			});

			this.formMiddleName = new Ext.form.TextField(
			{
				fieldLabel: GO.lang.strMiddleName,
				name: 'middle_name'
			});

			this.formLastName = new Ext.form.TextField(
			{
				fieldLabel: GO.lang.strLastName,
				name: 'last_name',
				panel: this,
				validateValue: function(val) {
					var bool = (val!='' || this.panel.formFirstName.getValue()!='');
					if(!bool)
					{
						this.markInvalid(this.blankText);
					}else
					{
						this.panel.formFirstName.clearInvalid();
					}
					return bool;
				}
			});
		}
	
		this.formTitle = new Ext.form.TextField(
		{
			fieldLabel: GO.lang.strTitle,
			name: 'title'
		});
		
		this.formAfternameTitle = new Ext.form.TextField(
		{
			fieldLabel: GO.lang.strSuffix,
			name: 'suffix'
		});
	
		this.formInitials = new Ext.form.TextField(
		{
			fieldLabel: GO.lang.strInitials,
			name: 'initials'
		});
	
		this.sexCombo = new GO.form.ComboBox({
			fieldLabel: GO.lang.strSex,
			hiddenName:'sex',
			store: new Ext.data.SimpleStore({
				fields: ['value', 'text'],
				data : [
				['M', GO.lang['strMale']],
				['F', GO.lang['strFemale']]
				]
        
			}),
			value:'M',
			valueField:'value',
			displayField:'text',
			mode: 'local',
			triggerAction: 'all',
			editable: false,
			selectOnFocus:true,
			forceSelection: true
		});

		this.formSalutation = new Ext.form.TextField(
		{
			fieldLabel: GO.addressbook.lang['cmdFormLabelSalutation'],
			name: 'salutation'
		});
	
		this.formBirthday = new Ext.form.DateField({
			fieldLabel: GO.lang['strBirthday'],
			name: 'birthday',
			format: GO.settings['date_format']
		});
	
		if(!config.forUser){
			this.formEmail = new Ext.form.TextField(
			{
				fieldLabel: GO.lang['strEmail'],
				name: 'email',
				vtype:'emailAddress'

			});
		}
	
		this.formEmail2 = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strEmail'] + ' 2',
			name: 'email2',
			vtype:'emailAddress'
		});
	
		this.formEmail3 = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strEmail'] + ' 3',
			name: 'email3',
			vtype:'emailAddress'
		});
	
		this.formHomePhone = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strPhone'],
			name: 'home_phone'
		});
	
		this.formFax = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strFax'],
			name: 'fax'
		});
	
		this.formCellular = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strCellular'],
			name: 'cellular'
		});
		
		this.formCellular2 = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['cellular2'],
			name: 'cellular2'
		});
		
		this.formHomepage = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strHomepage'],
			name: 'homepage'
		});
	
														
	
		this.formAddress = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strAddress'],
			name: 'address'
		});
	
		this.formAddressNo = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strAddressNo'],
			name: 'address_no'
		});
	
		this.formPostal = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strZip'],
			name: 'zip'
		});

		this.formCity = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strCity'],
			name: 'city'
		});

		this.formState = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strState'],
			name: 'state'
		});
	
		this.formCountry = new GO.form.SelectCountry({
			fieldLabel: GO.lang['strCountry'],
			name: 'country_text',
			hiddenName: 'country'
		});



		this.formCompany = new GO.addressbook.SelectCompany({
			fieldLabel: GO.lang['strCompany'],
			name: 'company',
			hiddenName: 'company_id',
			emptyText: GO.addressbook.lang['cmdFormCompanyEmptyText'],
			addressbook_id: this.addressbook_id
		});
	
		this.formDepartment = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strDepartment'],
			name: 'department'
		});

		this.formFunction = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strFunction'],
			name: 'function'
		});

		this.formWorkPhone = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strWorkPhone'],
			name: 'work_phone'
		});	

		this.formWorkFax = new Ext.form.TextField(
		{
			fieldLabel: GO.lang['strWorkFax'],
			name: 'work_fax'
		});


		this.formAddressBooks = new GO.addressbook.SelectAddressbook({
			fieldLabel: GO.addressbook.lang['cmdFormLabelAddressBooks'],
			store: GO.addressbook.writableAddressbooksStore,			
			selectOnFocus:true,
			forceSelection: true,
			allowBlank: false,
			anchor:'100%'
		});
		
		if(!config.forUser){
			this.formAddressBooks.on('beforeselect', function(combo, record)
			{
				if(this.formCompany.getValue()==0 || confirm(GO.addressbook.lang.moveAll))
				{
					this.setAddressbookID(record.data.id);
					this.setSalutation();
					return true;
				}else
				{
					return false;
				}
			}, this);
		

			this.formAddressBooks.on('select', function(){
				this.setSalutation(true)
			}, this);

			this.formFirstName.on('blur', function(){
				this.setSalutation(false)
			}, this);
			this.formMiddleName.on('blur', function(){
				this.setSalutation(false)
			}, this);
			this.formLastName.on('blur', function(){
				this.setSalutation(false)
			}, this);
				
			this.formFirstName.on('change', function(){
				this.setSalutation(true)
			}, this);
			this.formMiddleName.on('change', function(){
				this.setSalutation(true)
			}, this);
			this.formLastName.on('change', function(){
				this.setSalutation(true)
			}, this);
		}
		this.formInitials.on('blur', function(){
			this.setSalutation(false)
		}, this);
		this.formTitle.on('blur', function(){
			this.setSalutation(false)
		}, this);
		this.sexCombo.on('change', function(){
			this.setSalutation(true)
		}, this);

		
		this.formInitials.on('change', function(){
			this.setSalutation(true)
		}, this);
		this.formTitle.on('change', function(){
			this.setSalutation(true)
		}, this);

		this.addressbookFieldset = new Ext.form.FieldSet(
		{
			xtype: 'fieldset',
			title: GO.addressbook.lang['cmdFieldsetSelectAddressbook'],
			autoHeight: true,
			collapsed: false,
			items: this.formAddressBooks
		});
		
	
		this.personalFieldset = new Ext.form.FieldSet(
		{
			xtype: 'fieldset',
			title: GO.addressbook.lang['cmdFieldsetPersonalDetails'],
			autoHeight: true,
			collapsed: false,
			defaults: {
				border: false,
				anchor:'100%'
			}
		});
		
		if(!config.forUser){
			this.personalFieldset.add([
				this.formFirstName,this.formMiddleName,this.formLastName
				]);	
		}
		
		this.personalFieldset.add([
			this.formTitle,this.formInitials,this.formAfternameTitle,this.sexCombo,
			this.formSalutation,
			this.formBirthday							
			]);
	
		this.addressFieldset = new Ext.form.FieldSet(
		{
			xtype: 'fieldset',
			title: GO.addressbook.lang['cmdFieldsetAddress'],
			autoHeight: true,
			collapsed: false,
			defaults: {
				border: false,
				anchor:'100%'
			},
			items: [this.formAddress,this.formAddressNo,this.formPostal,this.formCity,this.formState,this.formCountry]
		});
	
		this.contactFieldset =new Ext.form.FieldSet(
		{
			xtype: 'fieldset',
			title: GO.addressbook.lang['cmdFieldsetContact'],
			autoHeight: true,
			collapsed: false,
			defaults: {
				border: false,
				anchor:'100%'
			}
		});
		
		
		if(!config.forUser){
			this.contactFieldset.add(this.formEmail);	
		}
		
		this.contactFieldset.add([this.formEmail2,this.formEmail3,this.formHomePhone,this.formFax,this.formCellular,this.formCellular2,this.formWorkPhone,this.formWorkFax,this.formHomepage]);
		
		
		this.workFieldset = new Ext.form.FieldSet(
		{
			xtype: 'fieldset',
			title: GO.addressbook.lang['cmdFieldsetWork'],
			autoHeight: true,
			collapsed: false,
			defaults: {
				border: false,
				anchor:'100%'
			},
			items: [this.formCompany,this.formDepartment,this.formFunction]
		});
 
			var leftColItems = [];
		
		//if(!config.forUser)
			leftColItems.push(this.addressbookFieldset);
		
		leftColItems.push(this.personalFieldset,this.workFieldset);
		
	
		this.title= GO.addressbook.lang['cmdPanelContact'];
		//this.cls='go-form-panel';
		this.autoScroll=true;
		this.bodyStyle='padding:5px';
		this.layout= 'column';
		this.labelWidth=125;
		this.defaults= {
			border: false
		};
		this.items= [
		{
			itemId:'leftCol',
			columnWidth: .5,
			items: leftColItems			
		},{
			itemId:'rightCol',
			columnWidth: .5,
			style: 'margin-left: 5px;',
			items: [
			this.contactFieldset,
			this.addressFieldset
			]
		}
		];
	
		GO.addressbook.ContactProfilePanel.superclass.constructor.call(this);
	}

Ext.extend(GO.addressbook.ContactProfilePanel, Ext.Panel,{
	setSalutation : function(overwrite)
	{
		if(overwrite || this.formSalutation.getValue()==''){
			var firstName = this.formFirstName.getValue();
			var middleName = this.formMiddleName.getValue();
			var lastName = this.formLastName.getValue();
			var initials = this.formInitials.getValue();
			var title = this.formTitle.getValue();
			var record = this.formAddressBooks.store.getById(this.formAddressBooks.getValue());
			var sal = record.get('default_salutation');

			var sex = sal.slice(sal.indexOf('[')+1, sal.indexOf(']'));
			var sex_split = sex.split('/');
			var gender = (this.sexCombo.getValue() == 'M')? sex_split[0] : sex_split[1];

			sal = sal.replace('['+sex+']', gender);
			sal = sal.replace('{first_name}', firstName);
			sal = sal.replace('{middle_name}', middleName);
			sal = sal.replace('{last_name}', lastName);
			sal = sal.replace('{initials}', initials);
			sal = sal.replace('{title}', title);
			sal = sal.replace(/\s+/g, ' ');

			this.formSalutation.setValue(sal);
		}
	},
	setAddressbookID : function(addressbook_id)
	{
		this.formAddressBooks.setValue(addressbook_id);		
		this.formCompany.store.baseParams['addressbook_id'] = addressbook_id;
		this.formCompany.clearLastSearch();

//		if (GO.customfields) {
//			var allowed_cf_categories = this.formAddressBooks.store.getById(addressbook_id).data.allowed_cf_categories.split(',');
//			GO.addressbook.contactDialog.updateCfTabs(allowed_cf_categories);
//		}
	},
	setValues : function(record)
	{
		this.formFirstName.setValue(record.name);
		this.formEmail.setValue(record.email);
		this.formHomePhone.setValue(record.phone);
		this.formCompany.setValue(record.company);
	}

});