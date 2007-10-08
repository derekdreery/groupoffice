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
		     // add iframe as the child component
		     items: [ new Ext.ux.IFrameComponent({ 'id': id, 'url': url }) ]

		  
			});
			
			tabs.add(tab);


		}
	};

}();
Ext.EventManager.onDocumentReady(GroupOffice.init, GroupOffice, true);
