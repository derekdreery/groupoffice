GroupOffice = function(){
	var layout;
	
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
				titlebar: true,
				collapsible: true,
				animate: true
			},
			center: {
				titlebar: true,
				autoScroll:true,
				closeOnTab: true
			}
		});

		layout.beginUpdate();
		layout.add('north', new Ext.ContentPanel('north', 'North'));
		
		 var innerLayout = new Ext.BorderLayout('west', {
                south: {
                    split:true,
                    initialSize: 250,
                    minSize: 100,
                    maxSize: 400,
                    autoScroll:false,
                    collapsible:true,
                    titlebar: true,
                    animate: true,
                    cmargins: {top:2,bottom:0,right:0,left:0}
                },
                center: {
                    autoScroll:false,
                    titlebar:false
                }
            });
            // add the nested layout
            navPanel = new Ext.NestedLayoutPanel(innerLayout, 'View Feed');
            layout.add('west', navPanel);
            
            innerLayout.beginUpdate();
            innerLayout.add('south', new Ext.ContentPanel('southwest', {title: "Main menu"}));
            innerLayout.add('center', new Ext.ContentPanel('northwest', {title: "Module menu"}));
            
            // restore innerLayout state
            innerLayout.restoreState();
            innerLayout.endUpdate(true);
		
		
		layout.add('center', new Ext.ContentPanel('center', {title: 'Close Me', closable: true}));
		

		layout.endUpdate();
		}


	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);