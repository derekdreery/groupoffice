GO.email.writableLabelsStore = new GO.data.JsonStore({
    url: GO.url('email/label/store'),
    baseParams: {
        permissionLevel: GO.permissionLevels.write
    },
    fields: ['id', 'name', 'flag', 'color', 'default']
});
