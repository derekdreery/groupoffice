GO.email.ManageLabelsGrid = Ext.extend(GO.grid.GridPanel, {
    changed: false,

    initComponent: function () {

        Ext.apply(this, {
            standardTbar: true,
            store: GO.email.writableLabelsStore,
            border: false,
            paging: true,
            view: new Ext.grid.GridView({
                autoFill: true,
                forceFit: true,
                emptyText: GO.lang['strNoItems']
            }),
            cm: new Ext.grid.ColumnModel({
                defaults: {
                    sortable: true
                },
                columns: [
                    {
                        header: GO.lang.strName,
                        dataIndex: 'name'
                    },
                    {
                        header: GO.lang.color,
                        dataIndex: 'color',
                        renderer: function (value, metaData, record) {
                            return '<div style="display:inline-block; width:38px; height:14px; background-color:#' + value + '; margin-right:4px;"></div>';
                        }
                    }
                ]
            })
        });

        GO.email.ManageLabelsGrid.superclass.initComponent.call(this);

        GO.email.writableLabelsStore.load();
    },

    dblClick: function (grid, record) {
        this.showLabelDialog(record.id);
    },

    btnAdd: function () {
        this.showLabelDialog();
    },

    showLabelDialog: function (id) {
        if (!this.labelDialog) {
            this.labelDialog = new GO.email.LabelDialog();

            this.labelDialog.on('save', function () {
                this.store.load();
                this.changed = true;
            }, this);
        }
        this.labelDialog.show(id);
    },

    deleteSelected: function () {
        GO.email.ManageLabelsGrid.superclass.deleteSelected.call(this);
        this.changed = true;
    }
});