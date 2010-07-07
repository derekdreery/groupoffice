GO.wordpress.PublishPanel = function(config){
	config = config || {};

	config.title='Wordpress publish';
	config.layout='form';

	config.items=[
		{
			xtype:'checkbox',
			hideLabel:true,
			boxLabel:'Publish to Wordpress',
			name:'wp_publish'
		},{
			xtype:'textfield',
			fieldLabel:'Title',
			name:'wp_title',
			anchor:'100%'
		},{
			hideLabel:true,
			xtype:'htmleditor',
			anchor:'100% -50',
			name:'wp_content'
		}
	];

	GO.wordpress.PublishPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.wordpress.PublishPanel, Ext.Panel,{

});

GO.moduleManager.onModuleReady('addressbook',function(){

	Ext.override(GO.addressbook.ContactDialog,{
		initComponent : GO.addressbook.ContactDialog.prototype.initComponent.createInterceptor(function(){
			this.tabPanel.add(new GO.wordpress.PublishPanel());
		})
	});
});