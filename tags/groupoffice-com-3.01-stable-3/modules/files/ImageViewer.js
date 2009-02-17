GO.files.ImageViewer = Ext.extend(Ext.Window, {
	
	originalImgSize : false,
	
	viewerImages : Array(),
	
	currentImgIndex : 0,
	
	initComponent : function(){
		
		this.border=false;
		this.plain=true;
		this.maximizable=true;
		this.width=640;
		this.height=Ext.getBody().getHeight()-80;
		this.bodyStyle='text-align:center;vertical-align:middle';
		this.title='Image viewer';		
		
		this.tbar=[this.previousButton = new Ext.Button({
			iconCls: 'btn-left-arrow',
			text:GO.lang.cmdPrevious,
			handler: function(){
				this.loadImage(this.currentImgIndex-1);
			},
			scope:this
		}),this.nextButton = new Ext.Button({
			iconCls: 'btn-right-arrow',
			text:GO.lang.cmdNext,
			handler: function(){
				this.loadImage(this.currentImgIndex+1);
			},
			scope:this
		}),
		'-',
		{
			iconCls: 'btn-save',
			text: GO.lang.download,
			cls: 'x-btn-text-icon',
			handler: function(){
				document.location.replace(this.imgEl.dom.src);
			},
			scope: this
		}/*,'-',
		{
			iconCls: 'btn-save',
			text: 'Ware grootte',
			cls: 'x-btn-text-icon',
			handler: function(){
				this.imgEl.setSize(this.originalImgSize.width, this.originalImgSize.height);
			},
			scope: this
		},{
			iconCls: 'btn-save',
			text: 'Passend',
			cls: 'x-btn-text-icon',
			handler: function(){
				this.syncImgSize();
			},
			scope: this
		}*/];
		
		GO.files.ImageViewer.superclass.initComponent.call(this);
		this.on('resize', this.syncImgSize, this);
	},

	
	show : function(images, index)
	{
		GO.files.ImageViewer.superclass.show.call(this);
		
		this.viewerImages = images;
		
		this.loadImage(index);		
	},
	
	loadImage : function(index)
	{
		this.body.mask(GO.lang.waitMsgLoad);
		
		this.setTitle(this.viewerImages[index].name);
		
		this.currentImgIndex = index;
		
		if(this.imgEl)
		{
			this.imgEl.remove();
		}
		
		this.originalImgSize=false;
		this.imgEl = this.body.createChild({
			tag:'img',
			src: this.viewerImages[index].src,
			cls:'fs-img-viewer'			
		});
		
		this.syncImgSize();
		
		this.previousButton.setDisabled(index==0);
		this.nextButton.setDisabled(index==(this.viewerImages.length-1));
	},
	
	syncImgSize : function(){	
		
		if(this.imgEl)
		{
			if(!this.imgEl.dom.complete)
			{
				this.syncImgSize.defer(100, this);
			}else
			{			
				var imgSize = this.imgEl.getSize();
				var ar = imgSize.width/imgSize.height;
				
				if(!this.originalImgSize)
				{
					this.originalImgSize = imgSize;
				}
				
				var bodySize = {width:this.getInnerWidth(), height:this.getInnerHeight()};
				
				if(ar > 1)
				{
					//landscape img
					var w = this.originalImgSize.width > bodySize.width ? bodySize.width : this.originalImgSize.width;
					this.imgEl.setWidth(w);
					
					var h = this.imgEl.getHeight();
				}else 
				{
					var h = this.originalImgSize.height > bodySize.height ? bodySize.height : this.originalImgSize.height;
					this.imgEl.setHeight(h);
				}
				
				var topMargin = (bodySize.height-h)/2;
				this.imgEl.setStyle('margin-top', topMargin+'px');
				
				this.body.unmask();
			}
		}
	}/*,
	
	onResize : function(w, h){
		
		this.syncImgSize();
		
		 GO.files.ImageViewer.superclass.onResize.call(this, [w, h]);
	}*/
	
});