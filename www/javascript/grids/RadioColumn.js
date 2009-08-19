GO.grid.RadioColumn = function(config){
    Ext.apply(this, config);
    if(!this.id){
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
};

GO.grid.RadioColumn.prototype = {
    init : function(grid){
        this.grid = grid;
        this.grid.on('render', function(){
            var view = this.grid.getView();
            view.mainBody.on('mousedown', this.onMouseDown, this);
        }, this);
    },

    onMouseDown : function(e, t){
        if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
            e.stopEvent();
			var index = this.grid.getView().findRowIndex(t);
			var record = this.grid.store.getAt(index);
			var disabled = this.isDisabled(record);

            if (!disabled)
            {
				if(record.get(this.dataIndex)) {
					return;
				}

				for(var i = 0;i < this.grid.store.getCount(); ++i) {
						var rec = this.grid.store.getAt(i);
						if(rec.get(this.dataIndex)) {
							rec.set(this.dataIndex, false);
						}

				}
				record.set(this.dataIndex, true);
			}

        }
    },

	isDisabled : function(record){
		return false;
	},

    renderer : function(v, p, record){
		p.css += ' x-grid3-radio-col-td';
        var disabled = this.isDisabled(record);
		var on;
        if (!GO.util.empty(v))
        {
        	if (disabled)
        	{				
        		on = '-on x-item-disabled';
        	} else {
        		on = '-on';
        	}
        } else {
        	if (disabled)
        	{
        		on = ' x-item-disabled';
        	} else {
        		on = '';
        	}
        }

        return '<div class="x-grid3-radio-col'+ on +' x-grid3-cc-'+this.id+'">&#160;</div>';
    }
};