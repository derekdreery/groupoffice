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
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
 
GO.grid.CheckColumn = function(config){
    Ext.apply(this, config);
    if(!this.id){
        this.id = Ext.id();
    }
    this.renderer = this.renderer.createDelegate(this);
    
    this.addEvents({'change' : true});
    
    GO.grid.CheckColumn.superclass.constructor.call(this);
};

Ext.extend(GO.grid.CheckColumn, Ext.util.Observable, {

		disabled_field : 'disabled',
	
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
            
            var disabled = record.get(this.disabled_field);
            
            if (!disabled)
            {
           		var newValue = record.data[this.dataIndex]=='1' ? '0' : '1';
           		record.set(this.dataIndex, newValue);
           		
           		this.fireEvent('change', record, newValue);
            }
        }
    },

    renderer : function(v, p, record){
        p.css += ' x-grid3-check-col-td';
        
        var disabled = record.get(this.disabled_field);

        if (v == '1')
        {
        	if (disabled)
        	{
        		var on = '-on x-item-disabled';
        	} else {
        		var on = '-on';
        	}
        } else {
        	if (disabled)
        	{
        		var on = ' x-item-disabled';
        	} else {
        		var on = '';
        	}
        }
        
        return '<div class="x-grid3-check-col'+ on +' x-grid3-cc-'+this.id+'">&#160;</div>';
    }
});