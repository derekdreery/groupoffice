/**
 * @copyright Copyright Intermesh 2007
 * @author Merijn Schering <mschering@intermesh.nl>
 * 
 * This file is part of Group-Office.
 * 
 * Group-Office is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 * 
 * See file /LICENSE.GPL
 */
 
Note = function(){

	var win;
	var formPanel;
	var layout;
	var linksPanel;
	var linksDialog;
	var loaded_link_id=0;
	var nameField;


	return {
		showDialog : function (note_id)
		{
			if(!win){

				nameField = new Ext.form.TextField({
						id:'name',
						fieldLabel: GOlang['strName'],
						name: 'name',
						allowBlank:false,
						style:'width:92%;'
					});
				
				formPanel = new Ext.form.FormPanel({
					title: GOlang['strProperties'],
					labelWidth: 75, // label settings here cascade unless overridden
					defaultType: 'textfield',
        			bodyStyle:'padding:5px;',
					reader: new Ext.data.JsonReader({
						root: 'note',
						id: 'id'
					}, [
					{name: 'name'},
					{name: 'link_id'},
					{name: 'content'}
					]),
					
					items: [nameField,{
						id: 'content',
						fieldLabel: GOlang['strText'],
						name: 'content',
						xtype: 'textarea',
						style:'width:92%;height:200px;'
						
					}					
					]/*,
					tbar: [{
						id: 'link',
						icon: GOimages['link'],
						text: GOlang['cmdLink'],
						cls: 'x-btn-text-icon',
						handler: this.onButtonClick,
						scope: this
					}]*/
				});
				
				formPanel.form.on('actioncomplete', function(form, action){
					if(action.type=='load')
					{
						linksPanel.loadLinks(action.result.data['link_id'], 4);
						loaded_link_id=action.result.data['link_id'];
					}
				});
				
				linksPanel = new Ext.grid.LinksPanel();
				
				var tabs = new Ext.TabPanel({
			        activeTab: 0,
			        frame:true,
			        defaults:{autoHeight: true},
			        items:[
			           formPanel,
			           linksPanel
			        ]
			    });
			
			
				win = new Ext.Window( {
					el: 'notedialog',
					layout: 'fit',
					modal:true,
					shadow:false,
					minWidth:300,
					minHeight:300,
					height:400,
					width:600,
					plain:true,
					closeAction:'hide',

        			
					items: [
						tabs
					],
					
					buttons: [
						{
							id: 'ok',
							text: GOlang['cmdOk'],
							handler: function(){
								formPanel.form.submit({
								url:'./action.php',
								params: {'task' : 'save','note_id' : loaded_note_id},
			
								success:function(form, action){
									//reload grid
									Notes.getDataSource().reload();
								},
			
								failure: function(form, action) {
									Ext.MessageBox.alert(GOlang['strError'], action.result.errors);
								}
							});
							win.hide();
							},
							scope:this
						},
						{
							id: 'close',
							text: GOlang['cmdClose'],
							handler: function(){win.hide();},
							scope: this
						}
					]
				});
			}
			
			
			loaded_note_id=note_id;
			if(note_id>0)
			{
				formPanel.form.load({url: 'notes_json.php?note_id='+note_id, waitMsg:GOlang['waitMsgLoad']});
			}else
			{
				formPanel.form.reset();
				linksPanel.setDisabled(true);
			}

			nameField.focus.defer(1000, nameField, [true]);
			
			
			win.show();
		},

		rowDoulbleClicked : function(search_grid, rowClicked, e) {

			var selectionModel = links_grid.getSelectionModel();
			var record = selectionModel.getSelected();

			//parent.Ext.get('dialog').load({url: record.data['url'], scripts: true });
			parent.GroupOffice.showDialog({url: record.data['url'], scripts: true });
		},
		onButtonClick : function(btn){
			switch(btn.id)
			{
				case 'link':

					if(!linksDialog)
					{
						linksDialog= new Ext.LinksDialog();
					}
					linksDialog.setSingleLink(loaded_link_id, 4);
					linksDialog.show();
				break;

				case 'unlink':

				var unlinks = [];

				var selectionModel = links_grid.getSelectionModel();
				var records = selectionModel.getSelections();

				for (var i = 0;i<records.length;i++)
				{
					unlinks.push(records[i].data['link_id']);
				}



				if(parent.GroupOffice.unlink(note_form.reader.jsonData.note[0].link_id, unlinks))
				{
					links_ds.load();
				}
				break;

		
			}
		}
	}
}();

