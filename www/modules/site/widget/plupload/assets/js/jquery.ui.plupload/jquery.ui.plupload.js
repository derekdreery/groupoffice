(function(f,a,c,g,e){var h={};function b(i){return c.translate(i)||i}function d(i){i.html('<div class="plupload_wrapper"><div class="ui-widget-content plupload_container"><div class="plupload"><div class="ui-state-default ui-widget-header plupload_header"><div class="plupload_header_content"><div class="plupload_header_title">'+b("Select files")+'</div><div class="plupload_header_text">'+b("Add files to the upload queue and click the start button.")+'</div></div></div><div class="plupload_content"><table class="plupload_filelist"><tr class="ui-widget-header plupload_filelist_header"><td class="plupload_cell plupload_file_name">'+b("Filename")+'</td><td class="plupload_cell plupload_file_status">'+b("Status")+'</td><td class="plupload_cell plupload_file_size">'+b("Size")+'</td><td class="plupload_cell plupload_file_action">&nbsp;</td></tr></table><div class="plupload_scroll"><table class="plupload_filelist_content"></table></div><table class="plupload_filelist"><tr class="ui-widget-header ui-widget-content plupload_filelist_footer"><td class="plupload_cell plupload_file_name"><div class="plupload_buttons"><!-- Visible --><a class="plupload_button plupload_add">'+b("Add Files")+'</a>&nbsp;<a class="plupload_button plupload_start">'+b("Start Upload")+'</a>&nbsp;<a class="plupload_button plupload_stop plupload_hidden">'+b("Stop Upload")+'</a>&nbsp;</div><div class="plupload_started plupload_hidden"><!-- Hidden --><div class="plupload_progress plupload_right"><div class="plupload_progress_container"></div></div><div class="plupload_cell plupload_upload_status"></div><div class="plupload_clearer">&nbsp;</div></div></td><td class="plupload_file_status"><span class="plupload_total_status">0%</span></td><td class="plupload_file_size"><span class="plupload_total_file_size">0 kb</span></td><td class="plupload_file_action"></td></tr></table></div></div></div><input class="plupload_count" value="0" type="hidden"></div>')}g.widget("ui.plupload",{contents_bak:"",runtime:null,options:{browse_button_hover:"ui-state-hover",browse_button_active:"ui-state-active",dragdrop:true,multiple_queues:true,buttons:{browse:true,start:true,stop:true},autostart:false,sortable:false,rename:false,max_file_count:0},FILE_COUNT_ERROR:-9001,_create:function(){var i=this,k,j;k=this.element.attr("id");if(!k){k=c.guid();this.element.attr("id",k)}this.id=k;this.contents_bak=this.element.html();d(this.element);this.container=g(".plupload_container",this.element).attr("id",k+"_container");this.filelist=g(".plupload_filelist_content",this.container).attr({id:k+"_filelist",unselectable:"on"});this.browse_button=g(".plupload_add",this.container).attr("id",k+"_browse");this.start_button=g(".plupload_start",this.container).attr("id",k+"_start");this.stop_button=g(".plupload_stop",this.container).attr("id",k+"_stop");if(g.ui.button){this.browse_button.button({icons:{primary:"ui-icon-circle-plus"}});this.start_button.button({icons:{primary:"ui-icon-circle-arrow-e"},disabled:true});this.stop_button.button({icons:{primary:"ui-icon-circle-close"}})}this.progressbar=g(".plupload_progress_container",this.container);if(g.ui.progressbar){this.progressbar.progressbar()}this.counter=g(".plupload_count",this.element).attr({id:k+"_count",name:k+"_count"});j=this.uploader=h[k]=new c.Uploader(g.extend({container:k,browse_button:k+"_browse"},this.options));j.bind("Error",function(l,m){if(m.code===c.INIT_ERROR){i.destroy()}});j.bind("Init",function(l,m){if(!i.options.buttons.browse){i.browse_button.button("disable").hide();l.disableBrowse(true)}if(!i.options.buttons.start){i.start_button.button("disable").hide()}if(!i.options.buttons.stop){i.stop_button.button("disable").hide()}if(!i.options.unique_names&&i.options.rename){i._enableRenaming()}if(j.features.dragdrop&&i.options.dragdrop){i._enableDragAndDrop()}i.container.attr("title",b("Using runtime: ")+(i.runtime=m.runtime));i.start_button.click(function(n){if(!g(this).button("option","disabled")){i.start()}n.preventDefault()});i.stop_button.click(function(n){i.stop();n.preventDefault()})});if(i.options.max_file_count){j.bind("FilesAdded",function(l,n){var o=[],m=n.length;var p=l.files.length+m-i.options.max_file_count;if(p>0){o=n.splice(m-p,p);l.trigger("Error",{code:i.FILE_COUNT_ERROR,message:b("File count error."),file:o})}})}j.init();j.bind("FilesAdded",function(l,m){i._trigger("selected",null,{up:l,files:m});if(i.options.autostart){setTimeout(function(){i.start()},10)}});j.bind("FilesRemoved",function(l,m){i._trigger("removed",null,{up:l,files:m})});j.bind("QueueChanged",function(){i._updateFileList()});j.bind("StateChanged",function(){i._handleState()});j.bind("UploadFile",function(l,m){i._handleFileStatus(m)});j.bind("FileUploaded",function(l,m){i._handleFileStatus(m);i._trigger("uploaded",null,{up:l,file:m})});j.bind("UploadProgress",function(l,m){g("#"+m.id).find(".plupload_file_status").html(m.percent+"%").end().find(".plupload_file_size").html(c.formatSize(m.size));i._handleFileStatus(m);i._updateTotalProgress();i._trigger("progress",null,{up:l,file:m})});j.bind("UploadComplete",function(l,m){i._trigger("complete",null,{up:l,files:m})});j.bind("Error",function(l,p){var n=p.file,o,m;if(n){o="<strong>"+p.message+"</strong>";m=p.details;if(m){o+=" <br /><i>"+p.details+"</i>"}else{switch(p.code){case c.FILE_EXTENSION_ERROR:m=b("File: %s").replace("%s",n.name);break;case c.FILE_SIZE_ERROR:m=b("File: %f, size: %s, max file size: %m").replace(/%([fsm])/g,function(r,q){switch(q){case"f":return n.name;case"s":return n.size;case"m":return c.parseSize(i.options.max_file_size)}});break;case i.FILE_COUNT_ERROR:m=b("Upload element accepts only %d file(s) at a time. Extra files were stripped.").replace("%d",i.options.max_file_count);break;case c.IMAGE_FORMAT_ERROR:m=c.translate("Image format either wrong or not supported.");break;case c.IMAGE_MEMORY_ERROR:m=c.translate("Runtime ran out of available memory.");break;case c.IMAGE_DIMENSIONS_ERROR:m=c.translate("Resoultion out of boundaries! <b>%s</b> runtime supports images only up to %wx%hpx.").replace(/%([swh])/g,function(r,q){switch(q){case"s":return l.runtime;case"w":return l.features.maxWidth;case"h":return l.features.maxHeight}});break;case c.HTTP_ERROR:m=b("Upload URL might be wrong or doesn't exist");break}o+=" <br /><i>"+m+"</i>"}i.notify("error",o);i._trigger("error",null,{up:l,file:n,error:o})}})},_setOption:function(j,k){var i=this;if(j=="buttons"&&typeof(k)=="object"){k=g.extend(i.options.buttons,k);if(!k.browse){i.browse_button.button("disable").hide();up.disableBrowse(true)}else{i.browse_button.button("enable").show();up.disableBrowse(false)}if(!k.start){i.start_button.button("disable").hide()}else{i.start_button.button("enable").show()}if(!k.stop){i.stop_button.button("disable").hide()}else{i.start_button.button("enable").show()}}i.uploader.settings[j]=k},start:function(){this.uploader.start();this._trigger("start",null)},stop:function(){this.uploader.stop();this._trigger("stop",null)},getFile:function(j){var i;if(typeof j==="number"){i=this.uploader.files[j]}else{i=this.uploader.getFile(j)}return i},removeFile:function(j){var i=this.getFile(j);if(i){this.uploader.removeFile(i)}},clearQueue:function(){this.uploader.splice()},getUploader:function(){return this.uploader},refresh:function(){this.uploader.refresh()},_handleState:function(){var j=this,i=this.uploader;if(i.state===c.STARTED){g(j.start_button).button("disable");g([]).add(j.stop_button).add(".plupload_started").removeClass("plupload_hidden");g(".plupload_upload_status",j.element).html(b("Uploaded %d/%d files").replace("%d/%d",i.total.uploaded+"/"+i.files.length));g(".plupload_header_content",j.element).addClass("plupload_header_content_bw")}else{g([]).add(j.stop_button).add(".plupload_started").addClass("plupload_hidden");if(j.options.multiple_queues){g(j.start_button).button("enable");g(".plupload_header_content",j.element).removeClass("plupload_header_content_bw")}j._updateFileList()}},_handleFileStatus:function(l){var n,j;if(!g("#"+l.id).length){return}switch(l.status){case c.DONE:n="plupload_done";j="ui-icon ui-icon-circle-check";break;case c.FAILED:n="ui-state-error plupload_failed";j="ui-icon ui-icon-alert";break;case c.QUEUED:n="plupload_delete";j="ui-icon ui-icon-circle-minus";break;case c.UPLOADING:n="ui-state-highlight plupload_uploading";j="ui-icon ui-icon-circle-arrow-w";var i=g(".plupload_scroll",this.container),m=i.scrollTop(),o=i.height(),k=g("#"+l.id).position().top+g("#"+l.id).height();if(o<k){i.scrollTop(m+k-o)}break}n+=" ui-state-default plupload_file";g("#"+l.id).attr("class",n).find(".ui-icon").attr("class",j)},_updateTotalProgress:function(){var i=this.uploader;this.progressbar.progressbar("value",i.total.percent);this.element.find(".plupload_total_status").html(i.total.percent+"%").end().find(".plupload_total_file_size").html(c.formatSize(i.total.size)).end().find(".plupload_upload_status").html(b("Uploaded %d/%d files").replace("%d/%d",i.total.uploaded+"/"+i.files.length))},_updateFileList:function(){var k=this,j=this.uploader,m=this.filelist,l=0,o,n=this.id+"_",i;if(g.ui.sortable&&this.options.sortable){g("tbody.ui-sortable",m).sortable("destroy")}m.empty();g.each(j.files,function(q,p){i="";o=n+l;if(p.status===c.DONE){if(p.target_name){i+='<input type="hidden" name="'+o+'_tmpname" value="'+c.xmlEncode(p.target_name)+'" />'}i+='<input type="hidden" name="'+o+'_name" value="'+c.xmlEncode(p.name)+'" />';i+='<input type="hidden" name="'+o+'_status" value="'+(p.status===c.DONE?"done":"failed")+'" />';l++;k.counter.val(l)}m.append('<tr class="ui-state-default plupload_file" id="'+p.id+'"><td class="plupload_cell plupload_file_name"><span>'+p.name+'</span></td><td class="plupload_cell plupload_file_status">'+p.percent+'%</td><td class="plupload_cell plupload_file_size">'+c.formatSize(p.size)+'</td><td class="plupload_cell plupload_file_action"><div class="ui-icon"></div>'+i+"</td></tr>");k._handleFileStatus(p);g("#"+p.id+".plupload_delete .ui-icon, #"+p.id+".plupload_done .ui-icon").click(function(r){g("#"+p.id).remove();j.removeFile(p);r.preventDefault()});k._trigger("updatelist",null,m)});if(j.total.queued===0){g(".ui-button-text",k.browse_button).html(b("Add Files"))}else{g(".ui-button-text",k.browse_button).html(b("%d files queued").replace("%d",j.total.queued))}if(j.files.length===(j.total.uploaded+j.total.failed)){k.start_button.button("disable")}else{k.start_button.button("enable")}m[0].scrollTop=m[0].scrollHeight;k._updateTotalProgress();if(!j.files.length&&j.features.dragdrop&&j.settings.dragdrop){g("#"+o+"_filelist").append('<tr><td class="plupload_droptext">'+b("Drag files here.")+"</td></tr>")}else{if(k.options.sortable&&g.ui.sortable){k._enableSortingList()}}},_enableRenaming:function(){var i=this;this.filelist.on("click",".plupload_delete .plupload_file_name span",function(o){var m=g(o.target),k,n,j,l="";k=i.uploader.getFile(m.parents("tr")[0].id);j=k.name;n=/^(.+)(\.[^.]+)$/.exec(j);if(n){j=n[1];l=n[2]}m.hide().after('<input class="plupload_file_rename" type="text" />');m.next().val(j).focus().blur(function(){m.show().next().remove()}).keydown(function(q){var p=g(this);if(g.inArray(q.keyCode,[13,27])!==-1){q.preventDefault();if(q.keyCode===13){k.name=p.val()+l;m.html(k.name)}p.blur()}})})},_enableDragAndDrop:function(){this.filelist.append('<tr><td class="plupload_droptext">'+b("Drag files here.")+"</td></tr>");this.filelist.parent().attr("id",this.id+"_dropbox");this.uploader.settings.drop_element=this.options.drop_element=this.id+"_dropbox"},_enableSortingList:function(){var j,i=this;if(g("tbody tr",this.filelist).length<2){return}g("tbody",this.filelist).sortable({containment:"parent",items:".plupload_delete",helper:function(l,k){return k.clone(true).find("td:not(.plupload_file_name)").remove().end().css("width","100%")},stop:function(p,o){var l,n,k,m=[];g.each(g(this).sortable("toArray"),function(q,r){m[m.length]=i.uploader.getFile(r)});m.unshift(m.length);m.unshift(0);Array.prototype.splice.apply(i.uploader.files,m)}})},notify:function(j,k){var i=g('<div class="plupload_message"><span class="plupload_message_close ui-icon ui-icon-circle-close" title="'+b("Close")+'"></span><p><span class="ui-icon"></span>'+k+"</p></div>");i.addClass("ui-state-"+(j==="error"?"error":"highlight")).find("p .ui-icon").addClass("ui-icon-"+(j==="error"?"alert":"info")).end().find(".plupload_message_close").click(function(){i.remove()}).end();g(".plupload_header_content",this.container).append(i)},destroy:function(){g(".plupload_button",this.element).unbind();if(g.ui.button){g(".plupload_add, .plupload_start, .plupload_stop",this.container).button("destroy")}if(g.ui.progressbar){this.progressbar.progressbar("destroy")}if(g.ui.sortable&&this.options.sortable){g("tbody",this.filelist).sortable("destroy")}this.uploader.destroy();this.element.empty().html(this.contents_bak);this.contents_bak="";g.Widget.prototype.destroy.apply(this)}})}(window,document,plupload,jQuery));