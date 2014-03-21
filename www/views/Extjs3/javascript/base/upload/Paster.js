GO.base.upload.Paster = function(config) {

	Ext.apply(this, config);
	this.init();
};
Ext.apply(GO.base.upload.Paster.prototype, {
	pasteEl: null,
	init: function() {
		if (window.Clipboard) {
			//IE11, Chrome, Safari
			this.pasteEl.on('paste', this.handlePaste, this);
		} else
		{
			
//			var map = new Ext.KeyMap(this.pasteEl,{	
//					key: Ext.EventObject.V,
//					ctrl:true,
//					fn: function() {
//							console.log("key map");
//							this.pasteCatcher.focus();
//					},
//					scope:this					
//			});
			//Firefox
			this.canvas = document.createElement('canvas');
			this.pasteCatcher = document.createElement("div");
			this.pasteCatcher.setAttribute("id", "paste_ff");
			this.pasteCatcher.setAttribute("contenteditable", "");
			this.pasteCatcher.style.cssText = 'opacity:0;position:fixed;top:0px;left:0px;';
			this.pasteCatcher.style.marginLeft = "-20px";
			document.body.appendChild(this.pasteCatcher);
//			this.pasteCatcher.focus();


			this.pasteEl.on('blur', function() {

				this.pasteCatcher.focus();
			}, this);
			
			Ext.get(this.pasteCatcher).on('paste', function(event) {

				this.findImageEl();
			}, this);
		}
	},
	
	
	dataURItoBlob: function(dataURI, callback) {
// convert base64 to raw binary data held in a string
// doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
		var byteString = atob(dataURI.split(',')[1]);
// separate out the mime component
		var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

// write the bytes of the string to an ArrayBuffer
		var ab = new ArrayBuffer(byteString.length);
		var ia = new Uint8Array(ab);
		for (var i = 0; i < byteString.length; i++) {
			ia[i] = byteString.charCodeAt(i);
		}

// write the ArrayBuffer to a blob, and you're done
		
		return new Blob([ia],{type: mimeString});
	},
	
	findImageEl: function() {

		if (this.pasteCatcher.children.length > 0) {
			
			var file = this.dataURItoBlob(this.pasteCatcher.firstElementChild.src);
			this.uploadFile(file);
			this.pasteCatcher.innerHTML = '';
		} else
		{
			Ext.defer(this.findImageEl, 100, this);
		}
	},
	
	handlePaste: function(e) {
		var bE = e.browserEvent;

		for (var i = 0; i < bE.clipboardData.items.length; i++) {
			var item = bE.clipboardData.items[i];
			if (item.kind === "file") {
				this.uploadFile(item.getAsFile());
			}
		}
	},
	uploadFile: function(file) {

		var xhr = new XMLHttpRequest();
		xhr.upload.onprogress = function(e) {
			var percentComplete = (e.loaded / e.total) * 100;

			progress.updateProgress(percentComplete);
		};
		var self = this;
		xhr.onload = function() {
			if (xhr.status === 200) {
//				alert("Sucess! Upload completed");
			} else {
				alert("Error! Upload failed");
			}
		};
		xhr.onerror = function() {
			alert("Error! Upload failed. Can not connect to server.");
		};
		xhr.onreadystatechange = function()
		{
			progress.hide();
			if (xhr.readyState === 4 && xhr.status === 200)
			{
				var result = Ext.decode(xhr.responseText);
				if (self.callback) {
					self.callback.call(self.scope || self, self, result, xhr);
				}
			}
		};
		var dt = new Date();
		var filename = prompt("Please enter the file name", "Pasted image " + dt.format("Y-m-d H:i:s"));
		
		if(filename){
			var progress = Ext.MessageBox.progress("Uploading", "pasted file");
		
			xhr.open("POST", GO.url('core/pasteUpload', {
				model_name: this.model_name,
				model_id: this.model_id,
				filename: filename,
				filetype: file.type
			}));
			var formData = new FormData();
			formData.append("pastedFile", file);
			xhr.send(formData);
		}
	}
});