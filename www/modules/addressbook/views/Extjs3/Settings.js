GO.mainLayout.onReady(function(){
	
	if(GO.addressbook && GO.settings.show_addresslist_tab == "1")
		GO.moduleManager.addSettingsPanel('addresslists', GO.addressbook.AddresslistsSettingsPanel,{},4);
	
	
	if(GO.customfields && GO.customfields.types["GO_Addressbook_Model_Contact"])
	{
		for(var i=0;i<GO.customfields.types["GO_Addressbook_Model_Contact"].panels.length;i++)
		{
			var id = '';
			id = GO.customfields.types["GO_Addressbook_Model_Contact"].panels[i].category_id;

			if(GO.settings.show_contact_cf_tabs[id] && GO.settings.show_contact_cf_tabs[id] == true)
				GO.moduleManager.addSettingsPanel('contact_cf_panel_'+i,GO.customfields.CustomFormPanel, GO.customfields.types["GO_Addressbook_Model_Contact"].panels[i],i+5);
		}
	}	
});