/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */


GO.mailings.TemplatesGrid = function(config)
{
	if(!config)
	{
		config = {};
	}
	
	
	config.sm= new Ext.grid.RowSelectionModel({
		singleSelect:false
	});
	config.title= GO.mailings.lang['cmdPanelTemplate'];
	config.tbar= [
	{
		iconCls: 'btn-add',
		text: GO.mailings.lang['cmdAddEmailTemplate'],
		cls: 'x-btn-text-icon',
		handler: function(){

			this.showEmailTemplateDialog();
		},
		scope: this
	},
	{
		iconCls: 'btn-add',
		text: GO.mailings.lang.addDocumentTemplate,
		cls: 'x-btn-text-icon',
		handler: function(){
			this.showOOTemplateDialog();
		},
		scope: this
	},
	{
		iconCls: 'btn-delete',
		text: GO.lang['cmdDelete'],
		cls: 'x-btn-text-icon',
		handler: function(){
			this.deleteSelected();
		},
		scope: this
	}
	];
	
	var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:[
		{
			header: GO.lang['strName'],
			dataIndex: 'name'
		},
		{
			header: GO.lang.strOwner,
			dataIndex: 'owner' ,
			width: 300,
			sortable: false
		},
		{
			header: GO.mailings.lang['cmdType'],
			dataIndex: 'type' ,
			renderer: this.typeRenderer.createDelegate(this),
			width: 100
		}
		]
	});


	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang.strNoItems
	});
	config.cm= columnModel;
	config.border= false;
	config.paging= true;
	config.layout= 'fit';

	config.deleteConfig= {
		callback: function(){
			GO.mailings.ooTemplatesStore.reload();
		},
		scope: this
	};
	
	config.store = new GO.data.JsonStore({
		url: GO.settings.modules.mailings.url+ 'json.php',
		baseParams: {
			task: 'writable_templates'
		},
		root: 'results',
		id: 'id',
		fields: ['id', 'user_id', 'owner', 'name', 'type', 'acl_id','extension'],
		remoteSort: true
	});
	config.store.setDefaultSort('name', 'ASC');
	config.store.on('load', function(){
		if(GO.documenttemplates)
			GO.documenttemplates.ooTemplatesStore.load();
	}, this);
	
	GO.mailings.TemplatesGrid.superclass.constructor.call(this, config);
	
	this.on('rowdblclick', function(grid, rowIndex){
		var record = grid.getStore().getAt(rowIndex);
		
		if(record.data.type=='0')
		{
			this.showEmailTemplateDialog(record.data.id);
		}else
		{
			this.showOOTemplateDialog(record.data.id);
		}		
	}, this);	
}

Ext.extend(GO.mailings.TemplatesGrid, GO.grid.GridPanel,{
	templateType : {
		'0' : 'E-mail',
		'1' : GO.mailings.lang.documentTemplate
	},

	showOOTemplateDialog : function(template_id){

		if(!GO.documenttemplates){
			alert('You need to install the document templates module');
			return false;
		}

		if(!this.ooTemplateDialog){
			this.ooTemplateDialog = new GO.documenttemplates.OOTemplateDialog();
			this.ooTemplateDialog.on('save', function(){
				this.store.load();
			}, this);
		}

		this.ooTemplateDialog.show(template_id);
	},

	showEmailTemplateDialog : function(template_id){
		if(!this.emailTemplateDialog){
			this.emailTemplateDialog = new GO.mailings.EmailTemplateDialog();
			this.emailTemplateDialog.on('save', function(){
				this.store.load();
			}, this);
		}
		this.emailTemplateDialog.show(template_id);
	},

	typeRenderer : function(val, meta, record)
	{
		var type = this.templateType[val];
		
		if(val=='1'){
			type+=' ('+record.get('extension')+')';
		}

		return type;
	},
	
	afterRender : function()
	{
		GO.mailings.TemplatesGrid.superclass.afterRender.call(this);
		
		if(this.isVisible())
		{
			if(!this.store.loaded)
			{
				this.store.load();
			}
		}

	},
	
	onShow : function(){
		GO.mailings.TemplatesGrid.superclass.onShow.call(this);
		if(!this.store.loaded)
		{
			this.store.load();
		}
	}

});
