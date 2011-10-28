
GO.moduleManager.addModule('mailings');

GO.mainLayout.onReady(function(){
	if(!GO.addressbook){
		alert('The mailings module is installed but depends on the not available addressbook module. The administrator should correct this.');
	}
});