//GO.mainLayout.onReady(function(){
//	
//	GO.moduleManager.addSettingsPanel('addresslists', GO.addressbook.AddresslistsSettingsPanel,{},4);
//	
//	if(GO.customfields && GO.customfields.types["GO_Addressbook_Model_Contact"])
//	{
//		for(var i=0;i<GO.customfields.types["GO_Addressbook_Model_Contact"].panels.length;i++)
//		{
//			GO.moduleManager.addSettingsPanel('contact_cf_panel_'+i,GO.customfields.CustomFormPanel, GO.customfields.types["GO_Addressbook_Model_Contact"].panels[i],i+5);
//		}
//	}	
//});