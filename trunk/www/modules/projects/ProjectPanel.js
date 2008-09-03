GO.projects.ProjectPanel = function(config)
{
	Ext.apply(this, config);
	
	this.autoScroll=true;
	

	this.newMenuButton = new GO.NewMenuButton();
	
	
	this.tbar = [
		this.editButton = new Ext.Button({
			iconCls: 'btn-edit', 
			text: GO.lang['cmdEdit'], 
			cls: 'x-btn-text-icon', 
			handler: function(){
				GO.projects.projectDialog.show({ project_id: this.data.id});
			}, 
			scope: this,
			disabled : true
		}),{
			iconCls: 'btn-link', 
			cls: 'x-btn-text-icon', 
			text: GO.lang.cmdBrowseLinks,
			handler: function(){
				GO.linkBrowser.show({link_id: this.data.id,link_type: "5",folder_id: "0"});				
			},
			scope: this
		},		
		this.newMenuButton
	];	
	
	
	GO.projects.ProjectPanel.superclass.constructor.call(this);		
}


Ext.extend(GO.projects.ProjectPanel, Ext.Panel,{
	
	afterRender : function(){
		GO.projects.ProjectPanel.superclass.afterRender.call(this);
		
		this.body.on('click', this.onBodyClick, this);		
	},
	
	initComponent : function(){
		

		
		
		var template = 
			'<div>'+
				'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
					'<tr>'+
						'<td colspan="2" class="display-panel-heading">'+GO.projects.lang.informationAbout+' <b>{name}</b></td>'+
					'</tr>'+
					'<tr>'+
						'<td colspan="2" class="display-panel-description">{description}</td>'+
					'</tr>'+
					
					'<tpl if="customer.length">'+
						'<tr>'+
							'<td>'+GO.lang.customer+':</td><td>{customer}</b></td>'+
						'</tr>'+
					'</tpl>'+
					
				'</table>'+																
				
				
				'<tpl if="milestones.length">'+

						'<table class="display-panel" cellpadding="0" cellspacing="0" border="0">'+
						//LINK DETAILS
						'<tr>'+
							'<td colspan="4" class="display-panel-heading">'+GO.projects.lang.milestones+'</td>'+
						'</tr>'+
						
						'<tr>'+							
							'<td class="table_header_links" style="width:16px">&nbsp;</td>'+
							'<td class="table_header_links">' + GO.lang['strName'] + '</td>'+
							'<td class="table_header_links">'+GO.projects.lang.assignedTo+'</td>'+
							'<td class="table_header_links">'+GO.projects.lang.due+'</td>'+
						'</tr>'+	
											
						'<tpl for="milestones">'+
							'<tr id="pm-milestone-row-{id}" class="{[this.getClass(values)]}">'+
								'<td style="width:16px"><input id="pm-milestone-cb-{id}" type="checkbox" {[ values.completed ? "checked" : "" ]} /></td>'+
								'<td><div class="projects-milestone">{name}</div></td>'+
								'<td>{user_name}</td>'+
								'<td>{due_time}</td>'+								
							'</tr>'+
							'<tpl if="description.length">'+
								'<tr><td></td><td colspan="3" class="project-item-description">{description}</td></tr>'+
							'</tpl>'+
						
							
							
						'</tpl>'+
					
	    	'</tpl>'+
				GO.linksTemplate;
				
				
				if(GO.customfields)
				{
					template +=GO.customfields.displayPanelTemplate;
				}
	    	
	   var config = {	    		 
					getClass: function(values){
						
						var cls = '';
						
						var now = new Date();											
						var date = Date.parseDate(values.due_time, GO.settings.date_format);
						
						
						if(date<now)
						{
							cls = 'projects-late ';
						}
						
						if(values.completed)
						{
							cls += 'projects-completed';
						}
						return cls;
					}			  
				};
				
		if(GO.files)
		{
			Ext.apply(config, GO.files.filesTemplateConfig);
			template += GO.files.filesTemplate;
		}
		
		Ext.apply(config, GO.linksTemplateConfig);
				
		template+='</div>';
		
		this.template = new Ext.XTemplate(template, config);
	
		
		this.addEvents({'milestonecheck':true});
		
		GO.projects.ProjectPanel.superclass.initComponent.call(this);
	},
	
	loadProject : function(project_id)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		Ext.Ajax.request({
			url: GO.settings.modules.projects.url+'json.php',
			params: {
				task: 'project_with_items',
				project_id: project_id
			},
			callback: function(options, success, response)
			{
				this.body.unmask();
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{
					var responseParams = Ext.decode(response.responseText);
					this.setData(responseParams.data);
				}				
			},
			scope: this			
		});
		
	},
	
	onBodyClick :  function(e, target){
		
		
		
		
		if(target.tagName=='INPUT')
		{
			e.preventDefault();
						
			var id = target.id;
			var milestone_id = id.replace('pm-milestone-cb-','');
			
			var row = Ext.get(id.replace('cb', 'row'));
			
			Ext.Ajax.request({
				url: GO.settings.modules.projects.url+'action.php',
				params: {
					task: 'check_milestone',
					milestone_id: milestone_id,
					checked: target.checked
				},
				callback: function(options, success, response)
				{
					try
					{
						if(!success)
						{
							throw GO.lang['strRequestError']; 
						}
						var responseParams = Ext.decode(response.responseText);						
						
						if(!responseParams.success)
						{
							throw responseParams.feedback;
						}
						
						if(!target.checked)
						{				
							row.addClass('projects-completed');
							target.checked=true;
						}else
						{
							row.removeClass('projects-completed');
							target.checked=false;
						}
						
						this.fireEvent('milestonecheck', milestone_id, target.checked);
					}
					catch(error)
					{
						Ext.MessageBox.alert(GO.lang['strError'], error);
					}			
				},
				scope: this			
			});
				
			
			
			
				
		}
	},
	
	setData : function(data)
	{
		this.data=data;
		this.editButton.setDisabled(!data.write_permission);
		
		if(data.write_permission)
			this.newMenuButton.setLinkConfig({
				id:this.data.id,
				type:5,
				text: this.data.name,
				callback:function(){
					this.loadProject(this.data.id);				
				},
				scope:this
			});
		
		
		this.template.overwrite(this.body, data);	
	}
	
});			