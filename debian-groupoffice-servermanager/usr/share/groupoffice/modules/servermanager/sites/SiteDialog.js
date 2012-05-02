GO.moduleManager.onModuleReady('sites',function(){

	
	Ext.override(GO.sites.SiteDialog, {
		buildForm : GO.sites.SiteDialog.prototype.buildForm.createSequence(function(){
			
			this.createDefaultServermanagerPagesButton = new Ext.Button({
				iconCls: 'btn-add',
				itemId:'createServermanagerPages',
				text: GO.sites.lang.createDefaultPages,
				cls: 'x-btn-text-icon'
			});

			this.createDefaultServermanagerPagesButton.on("click", function(){
				Ext.MessageBox.confirm(GO.sites.lang.createDefaultPages, GO.sites.lang.reallyCreateDefaultPages, function(btn){
					if(btn == 'yes'){
						GO.request({
							url: 'servermanager/siteModule/CreateDefaultPages',
							params: {
								site_id: this.remoteModelId
							},
							success: function(response, options, results){
								GO.mainLayout.getModulePanel('sites').rebuildTree();
							},
							scope: this
						});
					}
				}, this);
			},this);
			
			this.sitesServermanagerPanel = new Ext.Panel({
				title:GO.servermanager.lang.servermanager,			
				cls:'go-form-panel',
				layout:'form',
				items:[{
					xtype: 'fieldset',
					title: GO.sites.lang.createDefaultPagesTitle,
					autoHeight: true,
					border: true,
					collapsed: false,
					items:this.createDefaultServermanagerPagesButton
				}]
			});

			this.addPanel(this.sitesServermanagerPanel);	
		})
	});
});
