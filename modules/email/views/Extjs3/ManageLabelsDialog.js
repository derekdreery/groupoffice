GO.email.ManageLabelsDialog = Ext.extend(GO.dialog.TabbedFormDialog, {

    initComponent: function () {
        Ext.apply(this, {
            loadOnNewModel: false,
            title: GO.email.lang.labels,
            height: 560,
            enableOkButton: false,
            enableApplyButton: false
        });

        GO.email.ManageLabelsDialog.superclass.initComponent.call(this);

        this.on('hide', function () {
            if (this.labelsGrid.changed) {
                this.fireEvent('change');
                this.labelsGrid.changed = false;
            }
        }, this);

        this.addEvents({'change': true});
    },

    buildForm: function () {

        this.labelsGrid = new GO.email.ManageLabelsGrid();
        this.addPanel(this.labelsGrid);
    }
});