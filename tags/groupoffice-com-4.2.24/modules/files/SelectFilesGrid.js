GO.files.SelectFilesGrid = function(config){

	if(!config)
	{
		config = {};
	}

	config.layout='fit';
	config.autoScroll=true;
	config.split=true;       


        this.checkColumn = new GO.grid.CheckColumn({
                header: '&nbsp;',
		dataIndex: 'checked',
                width: 20,
                sortable:false
        });

        this.checkColumn.on('change', function(record, checked)
        {
                this.changed=true;
        }, this);


        config.plugins=this.checkColumn;

        var fields ={
		fields:['type_id', 'id','name','type', 'size', 'mtime', 'extension', 'timestamp', 'thumb_url','path','acl_id'],
                columns: [this.checkColumn,{
                    id:'name',
                    header:GO.lang['strName'],
                    dataIndex: 'name',
                    renderer:function(v, meta, r){
                            var cls = r.get('acl_id')>0 ? 'folder-shared' : 'filetype filetype-'+r.get('extension');
                            return '<div class="go-grid-icon '+cls+'">'+v+'</div>';
                    }
            },{
                    id:'type',
                    header:GO.lang.strType,
                    dataIndex: 'type',
                    sortable:true,
                    hidden:true,
                    width:100
            },{
                    id:'size',
                    header:GO.lang.strSize,
                    dataIndex: 'size',
                    renderer: function(v){
                            return  v=='-' ? v : Ext.util.Format.fileSize(v);
                    },
                    hidden:true,
                    width:100
            },{
                    id:'mtime',
                    header:GO.lang.strMtime,
                    dataIndex: 'mtime',
                    width:120
            }]
        }

        config.store = new GO.data.JsonStore({
		url: GO.settings.modules.files.url+'json.php',
		baseParams: {
			'task': 'grid',
                        'show_files_only':1
		},
		root: 'results',
		totalProperty: 'total',
		id: 'type_id',
		fields:fields.fields,
		remoteSort:true
	});
	config.paging=true;


        var columnModel =  new Ext.grid.ColumnModel({
		defaults:{
			sortable:true
		},
		columns:fields.columns
	});

	config.cm=columnModel;
	config.autoExpandColumn='name';
	config.view=new Ext.grid.GridView({
		emptyText: GO.lang['strNoItems']
	}),
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
        config.clicksToEdit=1;


	GO.files.SelectFilesGrid.superclass.constructor.call(this, config);

        this.store.on('beforeload',function()
        {
                this.getModifiedRecords();

        }, this);

        this.store.on('load', function()
        {
                for(var i=0; i<this.store.data.items.length; i++)
                {
                        var record = this.store.data.items[i];

                        if(this.fileSelected(record.id))
                        {
                                record.set('checked', '1');
                        }
                }
        }, this)

        this.on('afteredit', this.afterEdit, this);
	this.on('beforeedit', this.beforeEdit, this);

	this.addEvents({
		'afternoedit' : true
	});

	this.on('afternoedit', this.afterNoEdit, this);

};

Ext.extend(GO.files.SelectFilesGrid, GO.grid.EditorGridPanel,{

        changed : false,
        selectedFiles: [],

        selectFile: function(id)
        {
                var type = id.substr(0, 1);
                if(type == 'f')
                {
                        this.selectedFiles.push(id);
                }                               
        },
        deselectFile: function(id)
        {
                for(var i=0; i<this.selectedFiles.length;i++ )
                {
                        if(this.selectedFiles[i]==id)
                        {
                                this.selectedFiles.splice(i,1);
                        }
                }
        },
        fileSelected: function(id)
        {
                for(var i=0; i<this.selectedFiles.length; i++)
                {
                        if(this.selectedFiles[i] == id)
                        {
                                return true;
                        }
                }

                return false;
        },        
        getModifiedRecords: function()
        {
                var records = this.store.getModifiedRecords();
                for(var i=0; i<records.length; i++)
                {
                        var record = records[i];
                        
                        if(record.data.checked == '1')
                        {                                
                                if(!this.fileSelected(record.id))
                                {
                                        this.selectFile(record.id);
                                }
                        }else
                        {
                            this.deselectFile(record.id);
                        }
                }
        },
        getSelectedFiles: function(check_latest)
        {
                if(check_latest)
                {
                        this.getModifiedRecords();
                }

                return this.selectedFiles;
        },
        removeSelectedFiles: function()
        {
                this.selectedFiles = [];

                this.store.rejectChanges();
        },

        selectAllFiles: function(folder_id)
        {
                Ext.Ajax.request({
                    url:GO.settings.modules.files.url+'json.php',
                    params:{
                        task:'get_all_file_ids',
                        folder_id: folder_id
                    },
                    callback:function(options, success, response){

                        var data = Ext.decode(response.responseText);
                        if(data.success && data.results)
                        {
                                this.selectedFiles = data.results;
                                this.store.reload();
                        }
                    },
                    scope:this
                });
        },

	iconRenderer : function(src,cell,record){
		return '<div class="' + record.data.iconCls +'"></div>';
	},

	/*
	 * Overide ext method because there's no way to capture afteredit when there's no change.
	 * We need this because we format /unformat numbers before and after edit.
	 */
	onEditComplete : function(ed, value, startValue){

		GO.billing.DeliveriesGrid.superclass.onEditComplete.call(this, ed, value, startValue);

		if(startValue != 'undefined' && String(value) === String(startValue)){
			var r = ed.record;
			var field = this.colModel.getDataIndex(ed.col);
			value = this.postEditValue(value, startValue, r, field);

			var e = {
				grid: this,
				record: r,
				field: field,
				originalValue: startValue,
				value: value,
				row: ed.row,
				column: ed.col,
				cancel:false
			};
			this.fireEvent('afternoedit', e);
		}

	},

        afterNoEdit : function (e)
	{
		e.record.set(e.field, this.currentOriginalValue);
	},

        afterEdit : function (e)
	{
		this.changed=true;

                e.record.set(e.field, GO.util.unlocalizeNumber(e.value));

		var r = e.record.data;
	},

	beforeEdit : function(e)
	{
                var type = e.record.id.substr(0, 1);
                if(type == 'f')
                {
                        return false;
                }else
                {
                        var colId = this.colModel.getColumnId(e.column);

                        var col = this.colModel.getColumnById(colId);

                        this.currentOriginalValue=e.value;
                        if(col && col.editor && col.editor.decimals)
                        {
                                e.record.set(e.field, GO.util.numberFormat(e.value));
                        }
                }
	},

	numberRenderer : function(v)
	{
		//v = GO.util.unlocalizeNumber(v);
		return GO.util.numberFormat(v);
	}

});