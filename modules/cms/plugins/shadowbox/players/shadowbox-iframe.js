(function(a){a.iframe=function(c){this.obj=c;var b=document.getElementById("sb-overlay");this.height=c.height?parseInt(c.height,10):b.offsetHeight;this.width=c.width?parseInt(c.width,10):b.offsetWidth};a.iframe.prototype={append:function(b,e,d){this.id=e;var c='<iframe id="'+e+'" name="'+e+'" height="100%" width="100%" frameborder="0" marginwidth="0" marginheight="0" scrolling="auto"';if(a.client.isIE){c+=' allowtransparency="true"';if(a.client.isIE6){c+=" src=\"javascript:false;document.write('');\""}}c+="></iframe>";b.innerHTML=c},remove:function(){var b=document.getElementById(this.id);if(b){a.lib.remove(b);if(a.client.isGecko){delete window.frames[this.id]}}},onLoad:function(){var b=a.client.isIE?document.getElementById(this.id).contentWindow:window.frames[this.id];b.location.href=this.obj.content}}})(Shadowbox);