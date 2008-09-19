/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: ComboReset.js 1857 2008-04-29 13:57:06Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.form.ComboBoxReset = Ext.extend(GO.form.ComboBox, {

	validationEvent:false,
  validateOnBlur:false,
  trigger1Class:'x-form-clear-trigger',
  trigger2Class:'',
  //hideTrigger1:true,
  width:180,
  hasSearch : false,
  paramName : 'query',
  
	initComponent : function(){
      GO.form.ComboBoxReset.superclass.initComponent.call(this);

      this.triggerConfig = {
          tag:'span', cls:'x-form-twin-triggers', cn:[
          {tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger " + this.trigger1Class},
          {tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger " + this.trigger2Class}
      ]};
  },

  getTrigger : function(index){
      return this.triggers[index];
  },

  initTrigger : function(){
      var ts = this.trigger.select('.x-form-trigger', true);
      this.wrap.setStyle('overflow', 'hidden');
      var triggerField = this;
      ts.each(function(t, all, index){
          t.hide = function(){
              var w = triggerField.wrap.getWidth();
              this.dom.style.display = 'none';
              triggerField.el.setWidth(w-triggerField.trigger.getWidth());
          };
          t.show = function(){
              var w = triggerField.wrap.getWidth();
              this.dom.style.display = '';
              triggerField.el.setWidth(w-triggerField.trigger.getWidth());
          };
          var triggerIndex = 'Trigger'+(index+1);

          if(this['hide'+triggerIndex]){
              t.dom.style.display = 'none';
          }
          t.on("click", this['on'+triggerIndex+'Click'], this, {preventDefault:true});
          t.addClassOnOver('x-form-trigger-over');
          t.addClassOnClick('x-form-trigger-click');
      }, this);
      this.triggers = ts.elements;
  },  

  onTrigger1Click : function(){      
      this.reset();
      this.hasSearch = false;
  },

  onTrigger2Click : function(){
      this.onTriggerClick();
  }
});