GroupOffice = function(){
	var layout;
	var mainPanel;
	var navPanel;
	var innerLayout;
	
	return {

		init : function(){
			layout = new Ext.BorderLayout(document.body, {
				north: {
					split:false,
					initialSize: 25,
					titlebar: false
				},
				west: {
					split:true,
					initialSize: 200,
					minSize: 175,
					maxSize: 400,
					titlebar: false,
					collapsible: true,
					animate: true
				},
				center: {
					titlebar: false,
					autoScroll:true,
					closeOnTab: true
				}
			});

			layout.beginUpdate();
			layout.add('north', new Ext.ContentPanel('north', 'North'));

			innerLayout = new Ext.BorderLayout('west', {
				south: {
					split:true,
					initialSize: 250,
					minSize: 100,
					maxSize: 400,
					autoScroll:true,
					collapsible:true,
					titlebar: false,
					animate: true,
					cmargins: {top:2,bottom:0,right:0,left:0}
				},
				center: {
					autoScroll:true,
					titlebar:false
				}
			});
			// add the nested layout
			navLayout = new Ext.NestedLayoutPanel(innerLayout, 'Group-Office');
			layout.add('west', navLayout);

			innerLayout.beginUpdate();
			innerLayout.add('south', new Ext.ContentPanel('southwest'));
			
			navPanel = new Ext.ContentPanel('northwest');
			
			innerLayout.add('center', navPanel);

			// restore innerLayout state
			//innerLayout.restoreState();
			innerLayout.endUpdate(true);

			mainPanel = new Ext.ContentPanel('center');

			layout.add('center', mainPanel);


			layout.endUpdate();
			
			
			Ext.QuickTips.init();
			//Ext.QuickTips.register({title: 'Play', qtip: 'The summary displays relevant info', target: 'summary', autoHide:true});

		},
		
		setCenterUrl : function(url){
			mainPanel.load({
 			url: url});
		},
		
		setNavUrl : function(url){
			navPanel.load({
 			url: url});
		},
		
		getNavPanel : function(){
			return navPanel;
		}

	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);
