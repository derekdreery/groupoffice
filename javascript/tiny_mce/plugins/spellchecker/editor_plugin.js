(function(){var a=tinymce.util.JSONRequest,c=tinymce.each,b=tinymce.DOM;tinymce.create("tinymce.plugins.SpellcheckerPlugin",{getInfo:function(){return{longname:"Spellchecker",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/spellchecker",version:tinymce.majorVersion+"."+tinymce.minorVersion}},init:function(e,f){var g=this,d;g.url=f;g.editor=e;e.addCommand("mceSpellCheck",function(){if(!g.active){e.setProgressState(1);g._sendRPC("checkWords",[g.selectedLang,g._getWords()],function(h){if(h.length>0){g.active=1;g._markWords(h);e.setProgressState(0);e.nodeChanged()}else{e.setProgressState(0);e.windowManager.alert("spellchecker.no_mpell")}})}else{g._done()}});e.onInit.add(function(){if(e.settings.content_css!==false){e.dom.loadCSS(f+"/css/content.css")}});e.onClick.add(g._showMenu,g);e.onContextMenu.add(g._showMenu,g);e.onBeforeGetContent.add(function(){if(g.active){g._removeWords()}});e.onNodeChange.add(function(i,h){h.setActive("spellchecker",g.active)});e.onSetContent.add(function(){g._done()});e.onBeforeGetContent.add(function(){g._done()});e.onBeforeExecCommand.add(function(h,i){if(i=="mceFullScreen"){g._done()}});g.languages={};c(e.getParam("spellchecker_languages","+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv","hash"),function(i,h){if(h.indexOf("+")===0){h=h.substring(1);g.selectedLang=i}g.languages[h]=i})},createControl:function(h,d){var f=this,g,e=f.editor;if(h=="spellchecker"){g=d.createSplitButton(h,{title:"spellchecker.desc",cmd:"mceSpellCheck",scope:f});g.onRenderMenu.add(function(j,i){i.add({title:"spellchecker.langs","class":"mceMenuItemTitle"}).setDisabled(1);c(f.languages,function(n,m){var p={icon:1},l;p.onclick=function(){l.setSelected(1);f.selectedItem.setSelected(0);f.selectedItem=l;f.selectedLang=n};p.title=m;l=i.add(p);l.setSelected(n==f.selectedLang);if(n==f.selectedLang){f.selectedItem=l}})});return g}},_walk:function(i,g){var h=this.editor.getDoc(),e;if(h.createTreeWalker){e=h.createTreeWalker(i,NodeFilter.SHOW_TEXT,null,false);while((i=e.nextNode())!=null){g.call(this,i)}}else{tinymce.walk(i,g,"childNodes")}},_getSeparators:function(){var e="",d,f=this.editor.getParam("spellchecker_word_separator_chars",'\\s!"#$%&()*+,-./:;<=>?@[]^_{|}����������������\u201d\u201c');for(d=0;d<f.length;d++){e+="\\"+f.charAt(d)}return e},_getWords:function(){var e=this.editor,g=[],d="",f={};this._walk(e.getBody(),function(h){if(h.nodeType==3){d+=h.nodeValue+" "}});d=d.replace(new RegExp("([0-9]|["+this._getSeparators()+"])","g")," ");d=tinymce.trim(d.replace(/(\s+)/g," "));c(d.split(" "),function(h){if(!f[h]){g.push(h);f[h]=1}});return g},_removeWords:function(e){var f=this.editor,h=f.dom,g=f.selection,d=g.getBookmark();c(h.select("span").reverse(),function(i){if(i&&(h.hasClass(i,"mceItemHiddenSpellWord")||h.hasClass(i,"mceItemHidden"))){if(!e||h.decode(i.innerHTML)==e){h.remove(i,1)}}});g.moveToBookmark(d)},_markWords:function(o){var i,h,g,f,e,n="",k=this.editor,p=this._getSeparators(),j=k.dom,d=[];var l=k.selection,m=l.getBookmark();c(o,function(q){n+=(n?"|":"")+q});i=new RegExp("(["+p+"])("+n+")(["+p+"])","g");h=new RegExp("^("+n+")","g");g=new RegExp("("+n+")(["+p+"]?)$","g");f=new RegExp("^("+n+")(["+p+"]?)$","g");e=new RegExp("("+n+")(["+p+"])","g");this._walk(this.editor.getBody(),function(q){if(q.nodeType==3){d.push(q)}});c(d,function(r){var q;if(r.nodeType==3){q=r.nodeValue;if(i.test(q)||h.test(q)||g.test(q)||f.test(q)){q=j.encode(q);q=q.replace(e,'<span class="mceItemHiddenSpellWord">$1</span>$2');q=q.replace(g,'<span class="mceItemHiddenSpellWord">$1</span>$2');j.replace(j.create("span",{"class":"mceItemHidden"},q),r)}}});l.moveToBookmark(m)},_showMenu:function(g,i){var h=this,g=h.editor,d=h._menu,k,j=g.dom,f=j.getViewPort(g.getWin());if(!d){k=b.getPos(g.getContentAreaContainer());d=g.controlManager.createDropMenu("spellcheckermenu",{offset_x:k.x,offset_y:k.y,"class":"mceNoIcons"});h._menu=d}if(j.hasClass(i.target,"mceItemHiddenSpellWord")){d.removeAll();d.add({title:"spellchecker.wait","class":"mceMenuItemTitle"}).setDisabled(1);h._sendRPC("getSuggestions",[h.selectedLang,j.decode(i.target.innerHTML)],function(e){d.removeAll();if(e.length>0){d.add({title:"spellchecker.sug","class":"mceMenuItemTitle"}).setDisabled(1);c(e,function(l){d.add({title:l,onclick:function(){j.replace(g.getDoc().createTextNode(l),i.target);h._checkDone()}})});d.addSeparator()}else{d.add({title:"spellchecker.no_sug","class":"mceMenuItemTitle"}).setDisabled(1)}d.add({title:"spellchecker.ignore_word",onclick:function(){j.remove(i.target,1);h._checkDone()}});d.add({title:"spellchecker.ignore_words",onclick:function(){h._removeWords(j.decode(i.target.innerHTML));h._checkDone()}});d.update()});g.selection.select(i.target);k=j.getPos(i.target);d.showMenu(k.x,k.y+i.target.offsetHeight-f.y);return tinymce.dom.Event.cancel(i)}else{d.hideMenu()}},_checkDone:function(){var e=this,d=e.editor,g=d.dom,f;c(g.select("span"),function(h){if(h&&g.hasClass(h,"mceItemHiddenSpellWord")){f=true;return false}});if(!f){e._done()}},_done:function(){var d=this,e=d.active;if(d.active){d.active=0;d._removeWords();if(d._menu){d._menu.hideMenu()}if(e){d.editor.nodeChanged()}}},_sendRPC:function(e,h,d){var g=this,f=g.editor.getParam("spellchecker_rpc_url","{backend}");if(f=="{backend}"){g.editor.setProgressState(0);alert("Please specify: spellchecker_rpc_url");return}a.sendRPC({url:f,method:e,params:h,success:d,error:function(j,i){g.editor.setProgressState(0);g.editor.windowManager.alert(j.errstr||("Error response: "+i.responseText))}})}});tinymce.PluginManager.add("spellchecker",tinymce.plugins.SpellcheckerPlugin)})();