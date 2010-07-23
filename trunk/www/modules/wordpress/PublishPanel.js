GO.wordpress.PublishPanel = function(config){
	config = config || {};

	config.title=GO.wordpress.lang.websitePublish;
	config.layout='form';

	var spellcheckInsertPlugin = new GO.plugins.HtmlEditorSpellCheck(this);
	var wordPastePlugin = new Ext.ux.form.HtmlEditor.Word();
	//var dividePlugin = new Ext.ux.form.HtmlEditor.Divider();
	//var tablePlugin = new Ext.ux.form.HtmlEditor.Table();
	var hrPlugin = new Ext.ux.form.HtmlEditor.HR();
	var ioDentPlugin = new Ext.ux.form.HtmlEditor.IndentOutdent();
	//var ssScriptPlugin = new Ext.ux.form.HtmlEditor.SubSuperScript();
	var rmFormatPlugin = new Ext.ux.form.HtmlEditor.RemoveFormat();

	config.items=[
		{
			xtype:'checkbox',
			hideLabel:true,
			boxLabel:'Publish to Wordpress',
			name:'wp_publish'
		},{
			hideLabel:true,
			xtype:'htmleditor',
			anchor:'100% -50',
			name:'wp_content',
			plugins: [
				spellcheckInsertPlugin,
				wordPastePlugin,// evil! makes it very slow because of a lot of getvalue calls.
				hrPlugin,
				ioDentPlugin,
				rmFormatPlugin
			]
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

GO.moduleManager.onModuleReady('projects',function(){

	Ext.override(GO.projects.ProjectDialog,{
		initComponent : GO.projects.ProjectDialog.prototype.initComponent.createInterceptor(function(){
			this.tabPanel.add(new GO.wordpress.PublishPanel());
		})
	});
});