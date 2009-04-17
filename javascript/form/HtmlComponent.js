GO.form.HtmlComponent = Ext.extend(Ext.BoxComponent, {
     onRender : function(ct, position){
          this.el = ct.createChild({tag: 'div', html: this.html, cls: this.cls, style:this.style});
     }
});

Ext.reg('htmlcomponent', GO.form.HtmlComponent);