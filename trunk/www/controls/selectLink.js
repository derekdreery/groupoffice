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
 
Ext.form.selectLink = function(config){
	
	Ext.apply(this, config);
	
	
	this.store = new Ext.data.Store({
		
				proxy: new Ext.data.HttpProxy({
					url: BaseHref+'links_json.php'
				}),
		
				baseParams: {"query": ''},
		
				reader: new Ext.data.JsonReader({
					root: 'results',
					totalProperty: 'total',
					id: 'link_id'
				}, [
				{name: 'link_id'},
				{name: 'link_type'},
				{name: 'type_name'}
				]),
		
				// turn on remote sorting
				remoteSort: true
			});
			
	this.displayField='type_name';
			
	Ext.form.selectLink.superclass.constructor.call(this);
	
}

Ext.extend(Ext.form.selectLink, Ext.form.ComboBox);