/*GO.wordpress.PublishPanel = function(config){
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
				wordPastePlugin,
				hrPlugin,
				ioDentPlugin,
				rmFormatPlugin
			]
		}
	];

	GO.wordpress.PublishPanel.superclass.constructor.call(this, config);
}

Ext.extend(GO.wordpress.PublishPanel, Ext.Panel,{

});*/



GO.wordpress.edit = function(id, link_type){

	var panel = GO.mainLayout.openModule('wordpress');
	panel.items.item('iframe').el.dom.src=GO.settings.modules.wordpress.url+'redirect.php?link_id='+id+'&link_type='+link_type;
}

GO.wordpress.addPublishComponents = function(window, formPanel, link_type, id_name){
	window.editBtn = new Ext.Button({
		handler:function(){
			GO.wordpress.edit(window[id_name], link_type);
			window.collapse();
		},
		scope:window,
		text:GO.lang.cmdEdit
	});

	window.on('show', function(){
		window.editBtn.setDisabled(true);
		window.publishChanged=false;
	}, window);

	window.on('save', function(){
		if(window.publishCheck.getValue()){
			window.editBtn.setDisabled(false);
			if(window.publishChanged){
				GO.wordpress.edit(window[id_name], link_type);
				window.collapse();
				window.publishChanged=false;
			}
		}else
		{
			window.editBtn.setDisabled(true);
		}
	}, window);

	window.formPanel.on('actioncomplete', function(form, action){
		if(action.type=='load'){
			if(action.result.data.wp_publish)
				window.editBtn.setDisabled(false);
		}
	}, window);

	window.publishCheck = new Ext.form.Checkbox({
		hideLabel:true,
		boxLabel:GO.wordpress.lang.publishToWebsite,
		name:'wp_publish',
		listeners:{
			check:function(){
				window.publishChanged=true;
			},
			scope:window
		}
	});

	formPanel.add({
		xtype:'compositefield',
		hideLabel:true,
		items:[window.publishCheck,window.editBtn]
	});
}

GO.mainLayout.onReady(function(){
	if(GO.projects && GO.wordpress.mapping["5"]){
		Ext.override(GO.projects.ProjectDialog,{
			initComponent : GO.projects.ProjectDialog.prototype.initComponent.createInterceptor(function(){

				GO.wordpress.addPublishComponents(this, this.propertiesPanel.items.item('leftCol'), 5, 'project_id');
			})
		});
	}

	if(GO.addressbook && GO.wordpress.mapping["2"]){
		Ext.override(GO.addressbook.ContactDialog,{
			initComponent : GO.addressbook.ContactDialog.prototype.initComponent.createInterceptor(function(){
				//this.tabPanel.add(new GO.wordpress.PublishPanel());
				GO.wordpress.addPublishComponents(this, this.personalPanel.items.item('leftCol'), 2, 'contact_id');
				})
		});
	}
});