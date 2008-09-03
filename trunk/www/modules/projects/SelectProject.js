/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: SelectProject.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */

GO.projects.SelectProject = function(config){
	
	Ext.apply(this, config);
	
	if(!this.company_name)
	{
		this.company_name = '';
	}
	
	this.store = GO.projects.stores.bookableProjects;	

	GO.projects.SelectProject.superclass.constructor.call(this,{
		displayField: 'name',
		hiddenName:'project_id',		
		valueField: 'id',
		triggerAction:'all',		
		mode:'local',
		editable: false,
		selectOnFocus:true,
	  forceSelection: true,
		typeAhead: true,
		fieldLabel: GO.projects.lang.project
	});
	
}
Ext.extend(GO.projects.SelectProject, GO.form.ComboBox,{
	afterRender : function(){
		
		GO.projects.SelectProject.superclass.afterRender.call(this);
		
		this.store.load({
			callback:function(){
				this.selectFirst();
			},
			scope:this
		}, this);		
	}	
});