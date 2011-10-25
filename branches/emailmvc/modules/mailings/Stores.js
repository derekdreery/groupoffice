GO.mailings.writableMailingsStore = new GO.data.JsonStore({
    url: GO.url("addressbook/addresslist/store"),
    baseParams: {
        permissionLevel: GO.permissionLevels.write
    },
    fields: ['id', 'name', 'owner','acl_id'],
    remoteSort: true
});
		
GO.mailings.writableMailingsStore.on('load', function(){
	GO.mailings.writableMailingsStore.on('load', function(){
    GO.mailings.readableMailingsStore.load();
	}, this);
}, this, {single:true});
		
GO.mailings.readableMailingsStore = new GO.data.JsonStore({
    url: GO.url("addressbook/addresslist/store"),
    baseParams: {
        permissionLevel: GO.permissionLevels.read
    },
    fields: ['id', 'name', 'owner','acl_id', 'checked'],
    remoteSort: true
});
		
GO.moduleManager.addModule('mailings');

GO.mainLayout.onReady(function(){
	if(!GO.addressbook){
		alert('The mailings module is installed but depends on the not available addressbook module. The administrator should correct this.');
	}
});