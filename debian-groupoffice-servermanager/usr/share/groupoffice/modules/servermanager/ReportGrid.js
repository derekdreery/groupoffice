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
 
GO.servermanager.ReportGrid = function(config){
	
	if(!config)
	{
		config = {};
	}
	
	config.title = GO.servermanager.lang.report;
	config.layout='fit';
	config.autoScroll=true;
	config.split=true;
	config.store = new GO.data.JsonStore({
	    url: GO.settings.modules.servermanager.url+ 'json.php',
	    baseParams: {
	    	task: 'report'
	    	},
	    totalProperty:'total',
	    root: 'results',
	    id: 'name',
	    fields: ['name','count_users','max_users', 'install_time','lastlogin','total_logins','database_usage','file_storage_usage','mailbox_usage','total_usage', 'comment', 'ctime', 'mailbox_domains', 'features', 'billing', 'professional'],
	    remoteSort: true
	});
	
	config.paging=true;
	var columnModel =  new Ext.grid.ColumnModel([
	   		{
			header: GO.lang.strName, 
			dataIndex: 'name'
		},	{
			header: GO.lang.strCtime, 
			dataIndex: 'ctime'
		},		{
			header: GO.servermanager.lang.maxUsers,
			dataIndex: 'max_users',
			align:'right'
		},		{
			header: GO.servermanager.lang.countUsers, 
			dataIndex: 'count_users',
			align:'right'
		},		{
			header: GO.servermanager.lang.installTime, 
			dataIndex: 'install_time'
		},	{
			header: GO.servermanager.lang.lastlogin, 
			dataIndex: 'lastlogin'
		},		{
			header: GO.servermanager.lang.totalLogins, 
			dataIndex: 'total_logins',
			align:'right'
		},		{
			header: GO.servermanager.lang.databaseUsage, 
			dataIndex: 'database_usage',
			align:'right'
		},
		{
			header: GO.servermanager.lang.fileStorageUsage, 
			dataIndex: 'file_storage_usage',
			align:'right'
		},
		{
			header: GO.servermanager.lang.mailboxUsage, 
			dataIndex: 'mailbox_usage',
			align:'right'
		},
		{
			header: GO.servermanager.lang.totalUsage, 
			dataIndex: 'total_usage',
			align:'right'
		},{
			header: GO.servermanager.lang.strComment, 
			dataIndex: 'comment'
		},{
			header: GO.servermanager.lang.mailDomains, 
			dataIndex: 'mailbox_domains'
		},{
			header: GO.servermanager.lang.billing, 
			dataIndex: 'billing',
			renderer:function(v){
				return v=="1" ? GO.lang.cmdYes : GO.lang.cmdNo;
			}
		},{
			header: 'Professional',
			dataIndex: 'professional',
			renderer:function(v){
				return v=="1" ? GO.lang.cmdYes : GO.lang.cmdNo;
			}
		}/*,{
			header: GO.servermanager.lang.features, 
			dataIndex: 'features'
		}*/
	]);
	columnModel.defaultSortable = true;
	config.cm=columnModel;
	
	config.view=new Ext.grid.GridView({
		autoFill: true,
		forceFit: true,
		emptyText: GO.lang['strNoItems']		
	});
	config.sm=new Ext.grid.RowSelectionModel();
	config.loadMask=true;
	
	GO.servermanager.ReportGrid.superclass.constructor.call(this, config);
};

Ext.extend(GO.servermanager.ReportGrid, GO.grid.GridPanel,{
	afterRender : function(){
		GO.servermanager.ReportGrid.superclass.afterRender.call(this);		
		this.store.load();
	}
});