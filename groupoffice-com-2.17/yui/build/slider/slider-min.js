/* Copyright (c) 2006, Yahoo! Inc. All rights reserved.Code licensed under the BSD License:http://developer.yahoo.net/yui/license.txt version: 0.12.0 */ YAHOO.widget.Slider=function(_1,_2,_3,_4){if(_1){this.type=_4;this.init(_1,_2,true);var _5=this;this.createEvent("change",this);this.createEvent("slideStart",this);this.createEvent("slideEnd",this);this.thumb=_3;_3.onChange=function(){_5.handleThumbChange();};this.isTarget=false;this.animate=YAHOO.widget.Slider.ANIM_AVAIL;this.backgroundEnabled=true;this.tickPause=40;this.enableKeys=true;this.keyIncrement=20;this.moveComplete=true;this.animationDuration=0.2;if(_3._isHoriz&&_3.xTicks&&_3.xTicks.length){this.tickPause=Math.round(360/_3.xTicks.length);}else{if(_3.yTicks&&_3.yTicks.length){this.tickPause=Math.round(360/_3.yTicks.length);}}_3.onMouseDown=function(){return _5.focus();};_3.onMouseUp=function(){_5.thumbMouseUp();};_3.onDrag=function(){_5.fireEvents();};_3.onAvailable=function(){return _5.setStartSliderState();};}};YAHOO.widget.Slider.getHorizSlider=function(_6,_7,_8,_9,_10){return new YAHOO.widget.Slider(_6,_6,new YAHOO.widget.SliderThumb(_7,_6,_8,_9,0,0,_10),"horiz");};YAHOO.widget.Slider.getVertSlider=function(_11,_12,iUp,_14,_15){return new YAHOO.widget.Slider(_11,_11,new YAHOO.widget.SliderThumb(_12,_11,0,0,iUp,_14,_15),"vert");};YAHOO.widget.Slider.getSliderRegion=function(_16,_17,_18,_19,iUp,_20,_21){return new YAHOO.widget.Slider(_16,_16,new YAHOO.widget.SliderThumb(_17,_16,_18,_19,iUp,_20,_21),"region");};YAHOO.widget.Slider.ANIM_AVAIL=true;YAHOO.extend(YAHOO.widget.Slider,YAHOO.util.DragDrop,{onAvailable:function(){var _22=YAHOO.util.Event;_22.on(this.id,"keydown",this.handleKeyDown,this,true);_22.on(this.id,"keypress",this.handleKeyPress,this,true);},handleKeyPress:function(e){if(this.enableKeys){var _24=YAHOO.util.Event;var kc=_24.getCharCode(e);switch(kc){case 37:case 38:case 39:case 40:case 36:case 35:_24.preventDefault(e);break;default:}}},handleKeyDown:function(e){if(this.enableKeys){var _26=YAHOO.util.Event;var kc=_26.getCharCode(e),t=this.thumb;var h=this.getXValue(),v=this.getYValue();var _28=false;var _29=true;switch(kc){case 37:h-=this.keyIncrement;break;case 38:v-=this.keyIncrement;break;case 39:h+=this.keyIncrement;break;case 40:v+=this.keyIncrement;break;case 36:h=t.leftConstraint;v=t.topConstraint;break;case 35:h=t.rightConstraint;v=t.bottomConstraint;break;default:_29=false;}if(_29){if(t._isRegion){this.setRegionValue(h,v,true);}else{var _30=(t._isHoriz)?h:v;this.setValue(_30,true);}_26.stopEvent(e);}}},setStartSliderState:function(){this.setThumbCenterPoint();this.baselinePos=YAHOO.util.Dom.getXY(this.getEl());this.thumb.startOffset=this.thumb.getOffsetFromParent(this.baselinePos);if(this.thumb._isRegion){if(this.deferredSetRegionValue){this.setRegionValue.apply(this,this.deferredSetRegionValue,true);this.deferredSetRegionValue=null;}else{this.setRegionValue(0,0,true);}}else{if(this.deferredSetValue){this.setValue.apply(this,this.deferredSetValue,true);this.deferredSetValue=null;}else{this.setValue(0,true,true);}}},setThumbCenterPoint:function(){var el=this.thumb.getEl();if(el){this.thumbCenterPoint={x:parseInt(el.offsetWidth/2,10),y:parseInt(el.offsetHeight/2,10)};}},lock:function(){this.thumb.lock();this.locked=true;},unlock:function(){this.thumb.unlock();this.locked=false;},thumbMouseUp:function(){if(!this.isLocked()&&!this.moveComplete){this.endMove();}},getThumb:function(){return this.thumb;},focus:function(){var el=this.getEl();if(el.focus){try{el.focus();}catch(e){}}this.verifyOffset();if(this.isLocked()){return false;}else{this.onSlideStart();return true;}},onChange:function(_32,_33){},onSlideStart:function(){},onSlideEnd:function(){},getValue:function(){return this.thumb.getValue();},getXValue:function(){return this.thumb.getXValue();},getYValue:function(){return this.thumb.getYValue();},handleThumbChange:function(){var t=this.thumb;if(t._isRegion){t.onChange(t.getXValue(),t.getYValue());this.fireEvent("change",{x:t.getXValue(),y:t.getYValue()});}else{t.onChange(t.getValue());this.fireEvent("change",t.getValue());}},setValue:function(_35,_36,_37){if(!this.thumb.available){this.deferredSetValue=arguments;return false;}if(this.isLocked()&&!_37){return false;}if(isNaN(_35)){return false;}var t=this.thumb;var _38,newY;this.verifyOffset();if(t._isRegion){return false;}else{if(t._isHoriz){this.onSlideStart();_38=t.initPageX+_35+this.thumbCenterPoint.x;this.moveThumb(_38,t.initPageY,_36);}else{this.onSlideStart();newY=t.initPageY+_35+this.thumbCenterPoint.y;this.moveThumb(t.initPageX,newY,_36);}}return true;},setRegionValue:function(_39,_40,_41){if(!this.thumb.available){this.deferredSetRegionValue=arguments;return false;}if(this.isLocked()&&!force){return false;}if(isNaN(_39)){return false;}var t=this.thumb;if(t._isRegion){this.onSlideStart();var _42=t.initPageX+_39+this.thumbCenterPoint.x;var _43=t.initPageY+_40+this.thumbCenterPoint.y;this.moveThumb(_42,_43,_41);return true;}return false;},verifyOffset:function(){var _44=YAHOO.util.Dom.getXY(this.getEl());if(_44[0]!=this.baselinePos[0]||_44[1]!=this.baselinePos[1]){this.thumb.resetConstraints();this.baselinePos=_44;return false;}return true;},moveThumb:function(x,y,_47){var t=this.thumb;var _48=this;if(!t.available){return;}t.setDelta(this.thumbCenterPoint.x,this.thumbCenterPoint.y);var _p=t.getTargetCoord(x,y);var p=[_p.x,_p.y];this.fireEvent("slideStart");if(this.animate&&YAHOO.widget.Slider.ANIM_AVAIL&&t._graduated&&!_47){this.lock();setTimeout(function(){_48.moveOneTick(p);},this.tickPause);}else{if(this.animate&&YAHOO.widget.Slider.ANIM_AVAIL&&!_47){this.lock();var _51=new YAHOO.util.Motion(t.id,{points:{to:p}},this.animationDuration,YAHOO.util.Easing.easeOut);_51.onComplete.subscribe(function(){_48.endMove();});_51.animate();}else{t.setDragElPos(x,y);this.endMove();}}},moveOneTick:function(_52){var t=this.thumb;var _53=YAHOO.util.Dom.getXY(t.getEl());var tmp;var _55=null;if(t._isRegion){_55=this._getNextX(_53,_52);var _56=(_55)?_55[0]:_53[0];_55=this._getNextY([_56,_53[1]],_52);}else{if(t._isHoriz){_55=this._getNextX(_53,_52);}else{_55=this._getNextY(_53,_52);}}if(_55){this.thumb.alignElWithMouse(t.getEl(),_55[0],_55[1]);if(!(_55[0]==_52[0]&&_55[1]==_52[1])){var _57=this;setTimeout(function(){_57.moveOneTick(_52);},this.tickPause);}else{this.endMove();}}else{this.endMove();}},_getNextX:function(_58,_59){var t=this.thumb;var _60;var tmp=[];var _61=null;if(_58[0]>_59[0]){_60=t.tickSize-this.thumbCenterPoint.x;tmp=t.getTargetCoord(_58[0]-_60,_58[1]);_61=[tmp.x,tmp.y];}else{if(_58[0]<_59[0]){_60=t.tickSize+this.thumbCenterPoint.x;tmp=t.getTargetCoord(_58[0]+_60,_58[1]);_61=[tmp.x,tmp.y];}else{}}return _61;},_getNextY:function(_62,_63){var t=this.thumb;var _64;var tmp=[];var _65=null;if(_62[1]>_63[1]){_64=t.tickSize-this.thumbCenterPoint.y;tmp=t.getTargetCoord(_62[0],_62[1]-_64);_65=[tmp.x,tmp.y];}else{if(_62[1]<_63[1]){_64=t.tickSize+this.thumbCenterPoint.y;tmp=t.getTargetCoord(_62[0],_62[1]+_64);_65=[tmp.x,tmp.y];}else{}}return _65;},b4MouseDown:function(e){this.thumb.autoOffset();this.thumb.resetConstraints();},onMouseDown:function(e){if(!this.isLocked()&&this.backgroundEnabled){var x=YAHOO.util.Event.getPageX(e);var y=YAHOO.util.Event.getPageY(e);this.focus();this.moveThumb(x,y);}},onDrag:function(e){if(!this.isLocked()){var x=YAHOO.util.Event.getPageX(e);var y=YAHOO.util.Event.getPageY(e);this.moveThumb(x,y,true);}},endMove:function(){this.unlock();this.moveComplete=true;this.fireEvents();},fireEvents:function(){var t=this.thumb;t.cachePosition();if(!this.isLocked()){if(t._isRegion){var _66=t.getXValue();var _67=t.getYValue();if(_66!=this.previousX||_67!=this.previousY){this.onChange(_66,_67);this.fireEvent("change",{x:_66,y:_67});}this.previousX=_66;this.previousY=_67;}else{var _68=t.getValue();if(_68!=this.previousVal){this.onChange(_68);this.fireEvent("change",_68);}this.previousVal=_68;}if(this.moveComplete){this.onSlideEnd();this.fireEvent("slideEnd");this.moveComplete=false;}}},toString:function(){return ("Slider ("+this.type+") "+this.id);}});YAHOO.augment(YAHOO.widget.Slider,YAHOO.util.EventProvider);YAHOO.widget.SliderThumb=function(id,_70,_71,_72,iUp,_73,_74){if(id){this.init(id,_70);this.parentElId=_70;}this.isTarget=false;this.tickSize=_74;this.maintainOffset=true;this.initSlider(_71,_72,iUp,_73,_74);this.scroll=false;};YAHOO.extend(YAHOO.widget.SliderThumb,YAHOO.util.DD,{startOffset:null,_isHoriz:false,_prevVal:0,_graduated:false,getOffsetFromParent:function(_75){var _76=YAHOO.util.Dom.getXY(this.getEl());var _77=_75||YAHOO.util.Dom.getXY(this.parentElId);return [(_76[0]-_77[0]),(_76[1]-_77[1])];},initSlider:function(_78,_79,iUp,_80,_81){this.setXConstraint(_78,_79,_81);this.setYConstraint(iUp,_80,_81);if(_81&&_81>1){this._graduated=true;}this._isHoriz=(_78||_79);this._isVert=(iUp||_80);this._isRegion=(this._isHoriz&&this._isVert);},clearTicks:function(){YAHOO.widget.SliderThumb.superclass.clearTicks.call(this);this._graduated=false;},getValue:function(){if(!this.available){return 0;}var val=(this._isHoriz)?this.getXValue():this.getYValue();return val;},getXValue:function(){if(!this.available){return 0;}var _83=this.getOffsetFromParent();return (_83[0]-this.startOffset[0]);},getYValue:function(){if(!this.available){return 0;}var _84=this.getOffsetFromParent();return (_84[1]-this.startOffset[1]);},toString:function(){return "SliderThumb "+this.id;},onChange:function(x,y){}});if("undefined"==typeof YAHOO.util.Anim){YAHOO.widget.Slider.ANIM_AVAIL=false;}