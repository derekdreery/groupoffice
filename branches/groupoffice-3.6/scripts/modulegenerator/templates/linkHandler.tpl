GO.linkHandlers[{link_type}]=function(id){
	//	GO.{module}.{friendly_single_js}Dialog.show(id);
	
	var {friendly_single_js}Panel = new GO.{module}.{friendly_single_ucfirst}Panel();
	var linkWindow = new GO.LinkViewWindow({
		title: GO.billing.lang.{friendly_single_js},
		items: {friendly_single_js}Panel
	});
	 {friendly_single_js}Panel.load(id);
	linkWindow.show();
}

