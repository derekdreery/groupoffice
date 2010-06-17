/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Twan Verhofstad
 */

GO.bookmarks.BookmarksDialog = function(config){
	
	if(!config)
	{
		config={};
	}

	this.buildForm(config);
	
	config.maximizable=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=500;
	config.height=350;
	config.closeAction='hide';
	config.items= this.formPanel;
	config.title=GO.bookmarks.lang.bookmark;
	config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function(){
			this.submitForm(true);
		},
		scope: this
	},{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.hide();
		},
		scope:this
	}];

	GO.bookmarks.BookmarksDialog.superclass.constructor.call(this, config);
	
	this.addEvents({
		'save' : true
	});
}

Ext.extend(GO.bookmarks.BookmarksDialog, Ext.Window,{

	show : function (config) {		

		if(!this.selectCategory.store.loaded){
			return this.selectCategory.store.load({
				callback:function(){
					this.show(config);
				},
				scope:this
			});			
		}
		
		GO.bookmarks.BookmarksDialog.superclass.show.call(this);

		var logo='icons/bookmark.png';
		var pub='1';
  
		if (config.edit==1) // edit bookmark
		{
			// vul form met gegevens van aangeklikte bookmark
			this.formPanel.form.setValues(config.record);
			//this.selectCategory.setRemoteText(config.record.category_name);
			this.formPanel.baseParams.id=config.record.id;
			// thumb voorbeeld

			logo = config.record.logo;
			pub = config.record.public_icon;
			
		}
		else // add bookmark
		{
			// lege velden in form
			this.formPanel.baseParams.id=0;
			this.formPanel.form.reset();
			this.selectCategory.selectFirst();
			// leeg voorbeeld
			new Ext.XTemplate('').overwrite(Ext.get('thumbX'));
		}

		new Ext.XTemplate('<div class="thumb-wrap" >'+
				'<div class="thumb" no-repeat center center;">'+
				'<div class="thumb-name" style="background-image:url('+BaseHref+'modules/bookmarks/bmthumb.php?src='+logo+'&h=32&w=32&pub='+pub+')"><h1>'+GO.bookmarks.lang.title+'</h1>'+GO.bookmarks.lang.description+'</div></div>'
				+'</div>').overwrite(Ext.get('thumbX'));
	},
	

	submitForm : function(hide){
		
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.bookmarks.url+'action.php',
			params: {
				'task' : 'save_bookmark'
			},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				if(action.result.bookmark_id){
					this.formPanel.baseParams['id']=action.result.bookmark_id;
				}
				if(hide)
				{
					this.hide();
				}
				this.fireEvent('save', this);
			},
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
	},



	buildForm : function (config) {
	
		this.bookmarkPanel = new Ext.Panel({
			layout:'form',
			border: false,
			cls:'go-form-panel',
			waitMsgTarget:true,
			items: [ // de invoervelden
			this.selectCategory = new GO.form.ComboBox({
				fieldLabel: GO.bookmarks.lang.category,
				hiddenName:'category_id',
				anchor:'100%',
				store: GO.bookmarks.writableCategoriesStore,
				displayField:'name',
				valueField:'id',
				triggerAction: 'all',
				editable: false,
				allowBlank: false,
				selectOnFocus:true,
				forceSelection: true,
				mode:'local'
			}),{
				name: 'name',
				xtype: 'textfield',
				fieldLabel: GO.bookmarks.lang.title, 
				anchor: '100%',
				allowBlank: false
			},{
				name: 'content',
				xtype: 'textfield',
				fieldLabel: 'URL',
				anchor: '100%',
				vtype: 'url',
				value:'http://',
				allowBlank: false
			},{
				name: 'open_extern',
				xtype: 'checkbox',
				boxLabel: GO.bookmarks.lang.extern,
				hideLabel:true,
				anchor: '100%',
				checked:true
			},
			{
				name: 'description',
				xtype: 'textarea',
				fieldLabel: GO.bookmarks.lang.description,
				anchor: '100%',
				height:65
			},
			this.selectFile = new GO.bookmarks.SelectFile({
				fieldLabel: GO.bookmarks.lang.logo, 
				name: 'logo',
				anchor: '100%',
				value:'icons/bookmark.png',
				root_folder_id: GO.bookmarks.iconsFolderId,
				dialog: this
			}),
			{
				id: 'pubicon',					// database veld :(  of logo public of ge-upload is
				name: 'public_icon',
				xtype: 'hidden',
				hidden: true
			},

			this.thumbexample = new Ext.Component({
				style: {
					marginLeft: '100px'
				},
				id: 'thumbX',
				autoEl:{
					cls: 'thumbnails',
					html:	''
				}
			})]
		});
		this.items = [this.bookmarkPanel];
		this.formPanel = new Ext.form.FormPanel({
			baseParams : {
				id: 0
			},
			items: this.items
		});
	}
});