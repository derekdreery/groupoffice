GO.moduleManager.onModuleReady('calendar',function(){
	Ext.override(GO.calendar.SettingsPanel, {
		initComponent : GO.calendar.SettingsPanel.prototype.initComponent.createInterceptor(function(){
			this.freeBusyWindow = new GO.Window({
				height:500,
				width:500,
				layout:'fit',
				title:GO.freebusypermission.lang.freebusyPermissions,
				closable:true,
				closeAction:'hide',
				buttons:[{
						text:GO.lang.cmdClose,
						handler:function(){
							this.freeBusyWindow.hide();
						},
						scope:this
				}],
				items:[this.freebusyPermissionsPanel = new GO.grid.PermissionsPanel({hideLevel:true})]
			})

			this.freebusyPermissionsPanel.border=true;

			this.items.push({
				xtype:'button',
				handler:function(){
					this.freeBusyWindow.show();
					this.freebusyPermissionsPanel.loadAcl();
				},
				scope:this,
				text:GO.freebusypermission.lang.freebusyPermissions
			});			
		}),
		
		onLoadSettings : GO.calendar.SettingsPanel.prototype.onLoadSettings.createSequence(function(action){
			this.freebusyPermissionsPanel.setAcl(action.result.data.freebusypermissions_acl_id);
		})
		
	});
});