EventDialog = function(){
	
	var dialog;

	return{
		
		init : function () {
			
			if(!dialog)
			{
				dialog = new Ext.LayoutDialog('eventDialogDiv', {
						modal:true,
						shadow:false,
						resizable:false,
						proxyDrag: true,
						width:600,
						height:250,
						collapsible:false,
						shim:false,
						center: {
							autoScroll:true,
							tabPosition: 'top',
							closeOnTab: true,
							alwaysShowTabs: false
						}
		
					});
					dialog.addKeyListener(27, dialog.hide, dialog);
		
		
					layout = dialog.getLayout();
		
		
					layout.beginUpdate();
				
					
					layout.add('center', new Ext.ContentPanel('eventPropertiesDiv',{
						title: GOlang['strProperties'],
						autoScroll:true					
					}));
		
					layout.endUpdate();
					
				dialog.show();
			
			}
		}	
	
	}
}