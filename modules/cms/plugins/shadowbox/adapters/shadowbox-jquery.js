if(typeof jQuery=="undefined"){throw"Unable to load Shadowbox adapter, jQuery not found"}if(typeof Shadowbox=="undefined"){throw"Unable to load Shadowbox adapter, Shadowbox not found"}(function(b,a){a.lib={getStyle:function(d,c){return b(d).css(c)},remove:function(c){b(c).remove()},getTarget:function(c){return c.target},getPageXY:function(c){return[c.pageX,c.pageY]},preventDefault:function(c){c.preventDefault()},keyCode:function(c){return c.keyCode},addEvent:function(e,c,d){b(e).bind(c,d)},removeEvent:function(e,c,d){b(e).unbind(c,d)},append:function(d,c){b(d).append(c)}}})(jQuery,Shadowbox);(function(a){a.fn.shadowbox=function(b){return this.each(function(){var d=a(this);var e=a.extend({},b||{},a.metadata?d.metadata():a.meta?d.data():{});var c=this.className||"";e.width=parseInt((c.match(/w:(\d+)/)||[])[1])||e.width;e.height=parseInt((c.match(/h:(\d+)/)||[])[1])||e.height;Shadowbox.setup(d,e)})}})(jQuery);