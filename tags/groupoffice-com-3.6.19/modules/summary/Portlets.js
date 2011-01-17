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
	var rssTabPanel = new Ext.TabPanel({});
	
	GO.summary.portlets['portlet-rss-reader']=new GO.summary.Portlet({
		id: 'portlet-rss-reader',
		//iconCls: 'rss-icon',
		title: GO.summary.lang.hotTopics,
		layout:'fit',
		tools: [{
			id: 'gear',
			handler: function(){
				if(!this.manageWebFeedsWindow)
				{
					this.manageWebFeedsWindow = new Ext.Window({
						layout:'fit',
						items:this.WebFeedsGrid =  new GO.summary.WebFeedsGrid(),
						width:700,
						height:400,
						title:GO.summary.lang.rssFeeds,
						closeAction:'hide',
						buttons:[{
							text: GO.lang.cmdSave,
							handler: function(){

								if(!this.WebFeedsGrid.isValid(true)){
									alert(GO.lang['strErrorsInForm']);
									return false;
								}
								var params={
									'task' : 'save_feeds'
								};
								if(this.WebFeedsGrid.store.loaded){
									params['feeds']=Ext.encode(this.WebFeedsGrid.getGridData());
								}
								Ext.Ajax.request({
									url: GO.settings.modules.summary.url+'action.php',
									params: params,
									callback: function(options, success, response){
										if(!success)
										{
											Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
										}else
										{
											var responseParams = Ext.decode(response.responseText);
											this.WebFeedsGrid.store.reload();
											this.manageWebFeedsWindow.hide();
											rssTabPanel.items.each(function(p){ // Walk through tabs
												if(responseParams.data[p.feedId]==undefined) // Deleted feed
													rssTabPanel.remove(p);
												else // Feed already exists
												{
													var r = responseParams.data[p.feedId];

													if(p.feed != r.url || parseInt(p.getView().showPreview) != parseInt(r.summary))
														p.loadFeed(r.url, parseInt(r.summary));
													if(p.title != r.title)
														p.setTitle(r.title);
													delete responseParams.data[p.feedId]; //Remove id (don't create it again)
												}
											}, this);
											for(var i in responseParams.data) //For each new id
											{
												if(i != 'remove')
												{
													rssTabPanel.add(new GO.portlets.rssFeedPortlet({
														feedId: responseParams.data[i].id,
														feed: responseParams.data[i].url,
														title: responseParams.data[i].title,
														showPreview:parseInt(responseParams.data[i].summary),
														closable:false
													}));
												}
											}
											if(rssTabPanel.items.length == 0)
											{
												rssTabPanel.add(new Ext.Panel({
													title: '<br />',
													html: '<br />'+GO.summary.lang.noRssFeeds,
													cls: 'go-form-panel'
												}));
												rssTabPanel.setActiveTab(0);
											}
											if(rssTabPanel.getActiveTab() == null)
												rssTabPanel.setActiveTab(0)
										}
									},
									scope:this
								});
							},
							scope: this
						}],
						listeners:{
							show: function(){
								if(!this.WebFeedsGrid.store.loaded)
								{
									this.WebFeedsGrid.store.load();
								}
							},
							scope:this
						}
					});
				}
				this.manageWebFeedsWindow.show();     
			}
		},{
			id:'close',
			handler: function(e, target, panel){
				panel.removePortlet();
			}
		}],
		items: rssTabPanel,
		height:300
	});

	GO.summary.portlets['portlet-rss-reader'].on('render',function(){
		Ext.Ajax.request({
			url: GO.settings.modules.summary.url+'json.php',
			params: {
				'task':'rss_tabs'
			},
			waitMsg: GO.lang['waitMsgLoad'],
			waitMsgTarget: 'portlet-rss-reader',
			scope:this,
			callback: function(options, success, response){
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{
					var rssTabPanels = Ext.decode(response.responseText);
					if(rssTabPanels.data.length == 0)
					{
						rssTabPanel.add(new Ext.Panel({
							title: '<br />',
							html: '<br />'+GO.summary.lang.noRssFeeds,
							cls: 'go-form-panel'
						}));
						rssTabPanel.setActiveTab(0);
					}
					else
					{
						for(var i=0;i<rssTabPanels.data.length;i++){
							rssTabPanel.add(new GO.portlets.rssFeedPortlet({
								feedId: rssTabPanels.data[i].id,
								feed: rssTabPanels.data[i].url,
								title: rssTabPanels.data[i].title,
								showPreview:parseInt(rssTabPanels.data[i].summary),
								closable:false
							}));
							rssTabPanel.setActiveTab(0);
						};
					}
				}
				this.doLayout();
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
			params: {
				'task':'save_note'
			},
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
			params:{
				task:'note'
			},
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
		autoHeight:true,
		hideMode:'offsets'
	});
	
	GO.summary.portlets['portlet-announcements'].hide();

});