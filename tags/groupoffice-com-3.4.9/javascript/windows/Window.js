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
		if (this.height > screen.height || this.width > screen.width) {
			this.maximizable=true;
			this.minimizable=true;
			//this.maximized=false;
			this.fitContainer();
			//this.render();
		}
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