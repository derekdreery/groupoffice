GO.Window = Ext.extend(Ext.Window,{

	temporaryListeners : [],
	
	addListenerTillHide : function(eventName, fn, scope){
		this.on(eventName, fn, scope);		
		this.temporaryListeners.push({eventName:eventName,fn:fn,scope:scope});
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