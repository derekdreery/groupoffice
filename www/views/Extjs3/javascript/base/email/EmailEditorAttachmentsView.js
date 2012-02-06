GO.base.email.EmailEditorAttachmentsView = function(config){
		config=config||{};
		config.store = new GO.data.JsonStore({
			url:GO.url('core/pluploads'),
			fields : ['tmp_file', 'name', 'size', 'type', 'extension', 'human_size','from_file_storage'],
			id : 'tmp_file'
		});
		
		config.store.on('load', function(){
			if(this.store.data.length)	
				this.show();
			else
				this.hide();
			
			this.fireEvent('attachmentschanged', this);
		}, this);
		
		Ext.apply(config, {
			tpl: new Ext.XTemplate(
				GO.email.lang.attachments+':'+
				'<div style="overflow-x:hidden" id="'+this.attachmentsId+'" tabindex="0" class="em-attachments-container" >'+
				'<tpl for=".">',				
				'<span class="filetype-link filetype-{extension} attachment-wrap x-unselectable" unselectable="on" style="float:left" id="'+'{tmp_name}'+'">{name} ({human_size})</span>'+
				'</tpl>'+
				'</div>',
				'<div class="x-clear"></div>'
				),
			multiSelect:true,
			autoHeight:true,
			autoScroll:true,
			overClass:'x-view-over',
			hidden:true,
			itemSelector:'span.attachment-wrap'			
		});		
		
		GO.base.email.EmailEditorAttachmentsView.superclass.constructor.call(this, config);
		
		this.addEvents({attachmentschanged:true});
		
		this.on('contextmenu',this.onAttachmentContextMenu, this);
		this.on('dblclick',this.onAttachmentDblClick, this);
		this.on('render',function(){
					this.getEl().tabIndex=0;
					var map = new Ext.KeyMap(this.getEl(),{
						key: Ext.EventObject.DELETE,
						fn: function(key, e){
							this.removeSelectedAttachments();
						},
						scope:this
					});
				}, this);
	}
Ext.extend(GO.base.email.EmailEditorAttachmentsView, Ext.DataView, {
	
	afterUpload : function(loadParams){
		var params = {add:true, params:loadParams};
		this.store.load(params);
	},
	removeSelectedAttachments : function(){
		var records = this.getSelectedRecords();
		for(var i=0;i<records.length;i++)
		{
			this.store.remove(records[i]);
		}
		this.setVisible(this.store.data.length);
		this.fireEvent('attachmentschanged', this);
		
	},
	onAttachmentDblClick : function(view, index, node, e){
		
		var record = this.store.getAt(index);	
		if(record.data.from_file_storage){
			window.open(GO.url("files/file/download",{path:record.data.tmp_file}));
		}else
		{
			window.open(GO.url("core/downloadTempFile",{path:record.data.tmp_file}));
		}		
	},
	
	onAttachmentContextMenu : function(dv, index, node, e)
	{
		if(!this.menu)
		{
			this.menu = new Ext.menu.Menu({
				items: [
				{
					iconCls:'btn-delete',
					text:GO.lang.cmdDelete,
					scope:this,
					handler: function()
					{
						this.removeSelectedAttachments();
					}
				}]
			});
		}

		if(!this.isSelected(node))
		{
			this.select(node);
		}		

		e.preventDefault();
		this.menu.showAt(e.getXY());		
	}
});