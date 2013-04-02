/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: SelectSettingPanels.js 12502 2012-11-02 16:26:05Z wsmits $
 * @copyright Copyright Intermesh
 * @author Wesley Smits <wsmits@intermesh.nl>
 */

GO.users.SelectSettingPanels = Ext.extend(Ext.Panel,{

	tabElements : [],

	initComponent : function(){
		
		this.autoScroll=true;
		this.border=false;
		this.hideLabel=true;
		this.title = GO.users.lang.enabledSettingtabs;
		this.layout='form';
		this.cls='go-form-panel';
		this.labelWidth=50;
		
		this.items = [];
		
		this.items.push(new GO.form.HtmlComponent({html:'<br /><h1>'+'Show the addresslist panel'+'</h1>'})); // TODO: in language file
		
		// This item is saved in the GO_settings table as "globalsettings_show_tab_addresslist".
		this.items.push({
			xtype:'xcheckbox',
			boxLabel: 'Addresslist panel', // TODO: in language file
			name: 'globalsettings_show_tab_addresslist',
			hideLabel:true,
			checked: false
			});		
		
		this.items.push(new GO.form.HtmlComponent({html:'<br /><h1>'+'Enabled custom field tabs'+'</h1>'})); // TODO: in language file
		
		GO.users.SelectSettingPanels.superclass.initComponent.call(this);
	},
	
	removeComponents : function(){
			var f = this.ownerCt.ownerCt.form;
			for(var i=0;i<this.tabElements.length;i++)
			{
				f.remove(this.tabElements[i]);
				this.remove(this.tabElements[i], true);
			}
			this.tabElements=[];
		},
	
	loadComponents : function(){
		
		this.removeComponents();

		var f = this.ownerCt.ownerCt.form;
		
		for(var i=0;i<GO.users.contactCustomFieldsCategoriesStore.data.items.length;i++)
		{
			var record = GO.users.contactCustomFieldsCategoriesStore.data.items[i];
			
			this.tabElements.push(new Ext.form.Checkbox({
				boxLabel: record.data.name,
				labelSeparator: '',
				name: 'tab_cf_cat_'+record.data.id,
				autoCreate:  { tag: "input", type: "checkbox", autocomplete: "off", value: record.data.id },
				value:false,
				hideLabel:true
			}));
			
			this.add(this.tabElements[i]);
			f.add(this.tabElements[i]);
		}
		this.doLayout();
		
	},
	afterRender : function(){
		GO.users.SelectSettingPanels.superclass.afterRender.call(this);

		if(GO.users.contactCustomFieldsCategoriesStore.loaded){
			this.loadComponents();
		} else {
			GO.users.contactCustomFieldsCategoriesStore.load();
		}	
		GO.users.contactCustomFieldsCategoriesStore.on('load', function(){
			this.loadComponents();
		}, this);
	}	
});			