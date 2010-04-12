GO.Window = function(config)
{
	if(!config)
	{
		config={};
	}

	config.maximizable=true;
	config.minimizable=true;

	if(!config.keys)
	{
		config.keys=[];
	}
	
	GO.Window.superclass.constructor.call(this, config);
/*
	this.on("show",function(window){
		console.log(window);
	});
*/
}

GO.Window = Ext.extend(Ext.Window,{

	temporaryListeners : [],
	
	addListenerTillHide : function(eventName, fn, scope){
		this.on(eventName, fn, scope);		
		this.temporaryListeners.push({eventName:eventName,fn:fn,scope:scope});
	},

	beforeShow : function() {
		GO.Window.superclass.beforeShow.call(this);

		var vpH=GO.viewport.getEl().getHeight();
		var vpW=GO.viewport.getEl().getWidth();
		var center=false;

		if (this.height > vpH){
			this.setHeight(vpH);
		}
		if(this.width > vpW) {
			this.setWidth(vpW);
		}
		if(center)
			this.center();
	},

	hide : function(){
		
		for(var i=0;i<this.temporaryListeners.length;i++)
		{
			this.un(this.temporaryListeners[i].eventName, this.temporaryListeners[i].fn, this.temporaryListeners[i].scope);
		}
		this.temporaryListeners=[];
		GO.Window.superclass.hide.call(this);
	}
});