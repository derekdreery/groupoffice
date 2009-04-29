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
GO.summary.portlets=[];

GO.mainLayout.onReady(function(){
	var feedGrid = new GO.portlets.rssFeedPortlet();
	
	GO.summary.portlets['portlet-rss-reader']=new GO.summary.Portlet({
		id: 'portlet-rss-reader',
		//iconCls: 'rss-icon',
	 	title: GO.summary.lang.hotTopics,
		layout:'fit',
		tools: [{
					id: 'gear',
	        handler: function(){
	          
						Ext.Msg.prompt(GO.lang.url, GO.summary.lang.enterRssFeed, function(btn, text){
							if (btn == 'ok'){
								
								feedGrid.loadFeed(text);
								
								Ext.Ajax.request({
									url: GO.settings.modules.summary.url+'action.php',
									params: {
										'task':'save_rss_url',
										'url' : text
									},
									waitMsg: GO.lang['waitMsgSave'],
									waitMsgTarget: 'portlet-rss-reader'		
								});
							}
						});
	            
	            
	        }
	    },{
	        id:'close',
	        handler: function(e, target, panel){
	            panel.removePortlet();
	        }
	    }],
		items: feedGrid,
		height:300
	});
	
	feedGrid.on('render',function(){
		Ext.Ajax.request({
			url: GO.settings.modules.summary.url+'json.php',
			params: {
				'task':'feed'
			},
			waitMsg: GO.lang['waitMsgLoad'],
			waitMsgTarget: 'portlet-rss-reader',
			callback: function(options, success, response){
					if(!success)
					{
						Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
					}else
					{
						var responseParams = Ext.decode(response.responseText);
						
						if(responseParams.data.url && responseParams.data.url!='')
						{
							feedGrid.loadFeed(responseParams.data.url);
						}else
						{
							feedGrid.loadFeed('http://newsrss.bbc.co.uk/rss/newsonline_world_edition/front_page/rss.xml');
						}
					}
			}		
		});
	});
	
	
	
	
	
	
	/* start note portlet */
	
	
	var noteInput = new Ext.form.TextArea({
		hideLabel: true,
		name: 'text',
		anchor: '100% 100%'
		
	});
	
	noteInput.on('change', function(){
		notePanel.form.submit({
			url: GO.settings.modules.summary.url+'action.php',
			params: {'task':'save_note'},
			waitMsg: GO.lang['waitMsgSave']			
		});
	});
	
	var notePanel = new Ext.form.FormPanel({
		items: noteInput,
		waitMsgTarget: true
	});
	
	notePanel.on('render', function(){
		notePanel.load({
			url: GO.settings.modules.summary.url+'json.php',
			params:{task:'note'},
			waitMsg: GO.lang['waitMsgLoad']
		});				
	});
	
	
	
	
	
	
	GO.summary.portlets['portlet-note']=new GO.summary.Portlet({
		id: 'portlet-note',
		//iconCls: 'note-icon',
	 	title: GO.summary.lang.notes,
		layout:'fit',
		tools: [{
	        id:'close',
	        handler: function(e, target, panel){
	            panel.removePortlet();
	        }
	    }],
		items: notePanel,
		height:300
	});
		
	
	
	GO.summary.announcementsPanel = new GO.summary.AnnouncementsViewGrid();
	GO.summary.announcementsPanel.store.on('load', function(){
		if(GO.summary.announcementsPanel.store.getCount())
		{
			if(!GO.summary.portlets['portlet-announcements'].isVisible())
			{
				GO.summary.portlets['portlet-announcements'].show();
				GO.summary.portlets['portlet-announcements'].doLayout();
			}
		}else
		{
			GO.summary.portlets['portlet-announcements'].hide();
		}
			
	},this);
	
	GO.summary.portlets['portlet-announcements']=new GO.summary.Portlet({
		id: 'portlet-announcements',
	 	title: GO.summary.lang.announcements,
		layout:'fit',
		items: GO.summary.announcementsPanel,
		autoHeight:true
	});
	
	GO.summary.portlets['portlet-announcements'].hide();

});