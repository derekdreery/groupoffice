GO.mainLayout.onReady(function(){
	
	Ext.getBody().addKeyListener({ctrl:true, key:Ext.EventObject.F7}, function(e){
	
		
		if(!GO.debugWindow){
			GO.debugWindow =  new GO.DebugWindow();
		}
		
		GO.debugWindow.show();
		
	});
});

GO.DebugWindow = Ext.extend(GO.Window, {
	initComponent : function(){
		
		this.taskConfig={
			run: this.loadLog,
			scope:this,
			interval:1000
		};
		
		Ext.apply(this, {
			title:"Debug",
			maximizable:true,
			collapsible:true,
			width:600,
			height:1200,
			layout:'fit',
			items:{
				xtype:'tabpanel',
				items:[
					this.outputPanel = new GO.LogPanel({title:'Log'}),
					this.errorPanel = new GO.LogPanel({title:'Errors'}),
					this.infoPanel = new Ext.Panel({title:'Info',autoScroll:true, listeners:{show:this.loadInfo, scope:this}}),
				],
				activeTab:0
			},
			listeners:{
				show:function(){
					this.scrolledToBottom=false;
					Ext.TaskMgr.start(this.taskConfig);
					
					this.alignTo(Ext.getBody(),'tr-tr');
				},
				hide:function(){
					Ext.TaskMgr.stop(this.taskConfig);
				},
				scope:this
//        deactivate: function(self) {
//            self.toFront();
//
//        },
//        delay: 1
			}
		});
		
		GO.DebugWindow.superclass.initComponent.call(this);
	},
	
	loadInfo : function(){
		GO.request({
			url:'core/info',
			success:function(response, options, result){
							
				this.infoPanel.update(result.info);
		
			},
			scope:this
		});
	},
	loadLog : function(){
		GO.request({
			url:'core/debug',
			success:function(response, options, result){
				
				
				this.outputPanel.setLog(result.debugLog);
				this.errorPanel.setLog(result.errorLog);
				
	
			},
			fail:function(){
				Ext.TaskMgr.stop(this.taskConfig);
				this.hide();
			},
			scope:this
		});
	}
});


GO.LogPanel = Ext.extend(Ext.Panel,{
	autoScroll:true,
	setLog : function(str){
		var d = this.body.dom;
				
		var isAtBottom = d.scrollTop >= d.scrollHeight - d.offsetHeight;

		this.update(str);

		//scroll to bottom
		if(!this.scrolledToBottom || isAtBottom){

			d.scrollTop = d.scrollHeight - d.offsetHeight;

			this.scrolledToBottom=true;
		}
	}
})