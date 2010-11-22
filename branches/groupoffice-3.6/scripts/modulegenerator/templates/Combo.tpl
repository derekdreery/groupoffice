{
				xtype: 'combo',
       	fieldLabel: {HEADER},
        hiddenName:'{DATAINDEX}',
        anchor:'-20',
        store: GO.{module}.writable{related_friendly_multiple_ucfirst}Store,
        valueField:'id',
        displayField:'name',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        selectOnFocus:true,
        forceSelection: true
    }