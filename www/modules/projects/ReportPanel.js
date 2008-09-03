/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @version $Id: ReportPanel.js 2635 2008-07-18 11:00:31Z mschering $
 * @author Merijn Schering <mschering@intermesh.nl>
 */


 
GO.projects.ReportPanel = function(config){
	
	
	if(!config)
	{
		config={};
	}
	
	var applyButton = new Ext.Button({
		text: GO.lang['cmdApply'],
		handler: this.loadReport,
		scope:this
	});
	
	
	var textPanel = new Ext.Panel({
		border:false,
		cls: 'go-form-text-panel',
		html: GO.projects.lang.datefieldsInfo
	});
	
	
	this.groupBy = new Ext.form.ComboBox({
       	fieldLabel: GO.projects.lang.groupHoursBy,
        hiddenName:'group_by',
        store: new Ext.data.SimpleStore({
            fields: ['value', 'text'],
            data : [
            	['project_id', GO.projects.lang.projects],
            	['user_id', GO.lang.users],
            	['customer', GO.lang.customer]
            ]
            
        }),
        value:'project_id',
        valueField:'value',
        displayField:'text',
        mode: 'local',
        triggerAction: 'all',
        editable: false,
        selectOnFocus:true,
        forceSelection: true
    });	
    
  var now = new Date();
  var lastMonth = now.add(Date.MONTH, -1);
  var startOfLastMonth = lastMonth.getFirstDateOfMonth();
  var endOfLastMonth = lastMonth.getLastDateOfMonth();
  
  this.startDate = new Ext.form.DateField({    	
			name: 'start_date',
			format: GO.settings['date_format'],
			allowBlank:true,
			fieldLabel: GO.lang.strStart,
			value: startOfLastMonth.format(GO.settings.date_format)
			});
			
	this.endDate = new Ext.form.DateField({    	
			name: 'emd_date',
			format: GO.settings['date_format'],
			allowBlank:true,
			fieldLabel: GO.lang.strEnd,
			value: endOfLastMonth.format(GO.settings.date_format)
			});
	
	
	this.setReportPanel =new Ext.form.FormPanel({
		region:'north',
		height:180,
		title:GO.projects.lang.reports,
		cls:'go-form-panel',
		split:true,
		border:true,
		collapsible:true,
		items:[
			textPanel,
			this.groupBy,
			this.startDate,
			this.endDate,
			applyButton
		]
	});
	
	this.setReportPanel.on('save', function(){this.reportGrid.store.reload();}, this);
	
	this.reportGrid = new GO.projects.ReportGrid({
		region:'center',
		border:true
	});
			
	config.layout='border';
	config.items=[this.setReportPanel,this.reportGrid];
	
	GO.projects.ReportPanel.superclass.constructor.call(this, config);	
}

Ext.extend(GO.projects.ReportPanel, Ext.Panel,{
	loadReport : function(){
		this.reportGrid.store.baseParams.group_by=this.groupBy.getValue();
		
		var startDate = this.startDate.getValue();
		var endDate = this.endDate.getValue();		
		
		this.reportGrid.store.baseParams.start_date=startDate ? startDate.format(GO.settings.date_format) : '';
		this.reportGrid.store.baseParams.end_date=endDate ? endDate.format(GO.settings.date_format) : '';
		this.reportGrid.store.load();
	},
	
	afterRender : function(){
		
		this.loadReport();
		
		GO.projects.ReportPanel.superclass.afterRender.call(this);
	}
});