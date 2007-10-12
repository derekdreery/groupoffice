Ext.ux.IFrameComponent = Ext.extend(Ext.BoxComponent, {
     onRender : function(ct, position){
          this.el = ct.createChild({tag: 'iframe', id: 'iframe-'+ this.id, frameBorder: 0, src: this.url});
     }
});

GroupOffice = function(){

	var tabs;

	return {

		init : function(){
			Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
			
			
			tabs = new Ext.TabPanel({
				//autoHeight: true,
		        //defaults:{autoHeight: true},		        
		        //height:500,
		        border:false,
                activeTab:0,
                tabPosition:'top'
		    });
			
			
			var viewport = new Ext.Viewport({
            layout:'border',
            items:[
                new Ext.BoxComponent({ // raw
                    region:'north',
                    el: 'header',
                    height:28,
                    titlebar:false
                }), {
                	region:'center',
                    titlebar: false,
                    layout:'fit',
                    items: [tabs]

                }]
            });
            
            
            this.removeLoadMask();
            
            
            this.loadModules();
            
            
			
			
			

		},
		
		loadModules : function()
		{
			var conn = new Ext.data.Connection();
			conn.request({
				url: BaseHref+'json.php',
				params: {task: 'modules'},
				callback: function(options, success, response)
				{
					
				
					
					if(!success)
					{				
						Ext.MessageBox.alert('Failed', response['errors']);
					}else
					{   
						
						var result = Ext.decode(response.responseText);
						
						if(!result.success)
						{
							switch(result.errors)
		        			{
		        				case 'UNAUTHORIZED':
		        					Ext.Msg.alert(GOlang['strUnauthorized'], GOlang['strUnauthorizedText']);
		        				break;
		        				
		        				case 'NOTLOGGEDIN':
		        					var loginDialog = new Ext.LoginDialog({
		        						callback: this.loadModules,
		        						scope: this
		        					});
		        					loginDialog.show();
		        				break;
		        			}
						}else
						{	
							for (var module in result.modules)
							{
								this.addCenterPanel(module, result.modules[module].humanName, result.modules[module].url);
							}
							
							tabs.items.map['notes'].show();
						}
					}
				},
				scope: this
			});
			
			
			
		},
		
		removeLoadMask : function()
		{
			var loading = Ext.get('loading');
			var mask = Ext.get('loading-mask');
			mask.setOpacity(.8);
			mask.shift({
				xy:loading.getXY(),
				width:loading.getWidth(),
				height:loading.getHeight(), 
				remove:true,
				duration:1,
				opacity:.3,
				easing:'bounceOut',
				callback : function(){
					loading.fadeOut({duration:.2,remove:true});
				}
			});
		},

		addCenterPanel : function(id, title, url)
		{

			var tab = new Ext.Panel({
		     id: id,
		     title: title,
		     closable:true,
		     // layout to fit child component
		     layout:'fit', 
		     iconCls: 'go-module-icon-'+id,
		     // add iframe as the child component
		     items: [ new Ext.ux.IFrameComponent({ 'id': id, 'url': url }) ]

		  
			});
			
			tabs.add(tab);


		}
	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);
