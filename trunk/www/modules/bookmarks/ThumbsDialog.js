/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id$
 * @copyright Copyright Intermesh
 * @author Twan Verhofstad
 */



GO.bookmarks.ThumbsDialog = function(config){

	this.chosenlogo="";													// pad naar gekozen logo
	this.is_publiclogo=config.pubicon.dom.value;// public logo
	var clearicon=0;														// variabele voor het wissen van het logo

	if(!config)
	{
		config = {};
	}
	
	config.height=350;
	config.width=800;
	config.layout='border';
	config.title=GO.bookmarks.lang.chooseIcon;
	config.buttons=[{
		text: GO.lang['cmdOk'],
		handler: function(){
			
			if (clearicon==0){ // normale setting, als er een logo gekozen is
				this.iconfield.setValue(this.chosenlogo); // pad naar logo
				Ext.get('thumbX').dom.innerHTML='<div class="thumb-wrap" >'+
				'<div class="thumb" style="background:url(modules/bookmarks/themes/Default/images/bmbackground2.png) no-repeat center center;">'+
				'<div id="dialog_thumb" class="thumb-name" style="background-image:url('+BaseHref+'modules/bookmarks/bmthumb.php?src='+this.chosenlogo+'&h=32&w=32&pub='+this.is_publiclogo+')"> '+config.thumbtitle+'</div>'+'</div>'
				+'</div>';
			}

			else{ // als een logo gecleared is
				this.iconfield.reset(); // pad naar logo = leeg
				Ext.get('thumbX').dom.innerHTML='<div class="thumb-wrap" >'+
				'<div class="thumb" style="background:url(modules/bookmarks/themes/Default/images/bmbackground2.png) no-repeat center center;">'+
				'<div id="dialog_thumb" class="thumb-name" > '+config.thumbtitle+'</div>'+'</div>'
				+'</div>';
			
			}
			
			config.pubicon.dom.value=this.is_publiclogo; // public logo
			this.close();
		},
		scope: this
	}

	,{
		text: GO.lang['cmdClose'],
		handler: function(){
			this.close();
		},
		scope:this
	}
	
	];

 	// laat alle public logo's op deze manier zien, in centerPanel van de dialog
	
	this.logolist  = new Ext.XTemplate(
		'<tpl for=".">',
		'<div class="icons">', '<img src="'+BaseHref+'modules/bookmarks/icons/{filename}"/> </div>',
		'</tpl>',
		'<div style="clear:both"></div>');
	
	this.publiclogoView = new Ext.DataView({
		store: GO.bookmarks.thumbstore,
		tpl: this.logolist,
		cls: 'thumbnails',
		itemSelector:'div.icons',
		multiSelect: false,
		singleSelect: true,
		trackOver:true,
		border: false,
		style: {
			marginLeft: '13px',
			marginTop: '13px',
			marginRight: '6px'
		}
	});
	
	// verander voorbeeld thumb als er een logo in centerPanel word aangeklikt.

	this.publiclogoView.on('click',function(DV, index, node, e) {
		var record = this.publiclogoView.getRecord(node); // waar hebben we op geklikt?
		this.is_publiclogo=1;
		this.example = Ext.get('one-thumb'); // voorbeeld thumb in westPanel
		this.newicon = "background-image: url("+BaseHref+"modules/bookmarks/bmthumb.php?src=icons/" + record.data.filename + "&h=32&w=32&pub="+this.is_publiclogo+")";
		this.example.dom.style.cssText=this.newicon;
		this.chosenlogo="icons/" + record.data.filename;
		
	},this)

	// voorbeeld thumb, in westPanel
	
	this.bookmarkExample = new Ext.Component({
		style: {
			marginLeft:'13px'
		},
		autoEl:{
			cls: 'thumbnails',
			html:	new Ext.XTemplate('<div class="thumb-wrap" >'+
				'<div class="thumb">'+
				'<div id="one-thumb" class="thumb-name" style="background-image:url('+BaseHref+'modules/bookmarks/bmthumb.php?src='+config.thumbicon+'&h=32&w=32&pub='+this.is_publiclogo+')"><h1>'+GO.bookmarks.lang.title+'</h1>'+GO.bookmarks.lang.description+'</div>'+'</div>'
				+'</div>').apply()
		}
	})

	// upload logo button in westPanel

	this.uploadFile = new GO.form.UploadFile({ // aangepast met fileAdded event.
		style: {
			marginTop: '6px'
		},
		width: '90px',
		border: false,
		inputName : 'attachments',
		addText : GO.bookmarks.lang.uploadLogo,
		max: 1		// maar 1 tegelijk, overwrite event word meteen ge-fired.
	});

	// clear logo button in westPanel

	this.clearLogo = new Ext.Button({
		height: '21px',
		text : GO.bookmarks.lang.clearLogo,
		border: false,
		handler: function() {
			clearicon=1;
			Ext.get('one-thumb').dom.style.backgroundImage=''; // verwijder logo uit voorbeeld bookmark
		}
	});


	// de twee knoppen in een tabel zetten, voor uitlijning
	this.buttonTable = new Ext.Panel({
		border: false,
		layout: 'table',
		layoutConfig: {
			columns: 2
		},
		items: [{
			items: [this.uploadFile],
			border: false
		},{
			items: [this.clearLogo],
			border: false
		}
		]
	})

	
	// knoppen in een form, voor upload submit
	this.uploadForm = new Ext.form.FormPanel({
		border: false,
		cls : 'go-form-panel',
		fileUpload : true,
		waitMsgTarget : true,
		autoScroll:true,
		baseParams: {
			task: 'upload'
		},
		items : [this.buttonTable]
	});


	// knoppen + voorbeeld in westPanel
	this.westPanel= new Ext.Panel({
		region: 'west',
		//layout: 'form',
		border: true,
		header:false,
		width: 215,
		items: [this.uploadForm,this.bookmarkExample]
	})

	
	// public logo's in centerPanel'
	this.centerPanel= new Ext.Panel({
		region: 'center',
		autoScroll: true,
		items: [this.publiclogoView]
	})

	Ext.apply(config, {
		listeners:{
			render:function(){
				GO.bookmarks.thumbstore.load();
			}
		}
	});

	// uploadknop fired upload event
	this.uploadFile.on('fileAdded',function(){
		this.is_publiclogo=0; // ge-uploade logo's zijn niet public
		this.uploadHandler(); // fired upload event
	},this )


	config.items=[this.centerPanel,this.westPanel];
	GO.bookmarks.ThumbsDialog.superclass.constructor.call(this, config);
	

	// upload event roept automatisch overwrite aan om icon in goede map te zetten
	this.on('upload', function(){
		this.sendOverwrite({
			task: 'overwrite',
			thumb_id:   this.thumb_id,
			folder_id : this.folder_id
		});
	},this);

	this.addEvents({
		'upload' : true
	 });
}

Ext.extend(GO.bookmarks.ThumbsDialog, Ext.Window, {

  // kopie / aanpassing uit Files module
	uploadHandler : function(){
		this.uploadForm.form.submit({
			url:GO.settings.modules.bookmarks.url+'action.php',
			waitMsg : GO.lang.waitMsgUpload,
			
			success:function(form, action){
				this.uploadFile.clearQueue();
				this.fireEvent('upload', action);
			},
			failure:function(form, action)
			{
				var error = '';
				if(action.failureType=='client')
				{
					error = GO.lang['strErrorsInForm'];
				}else
				{
					error = action.result.feedback;
				}

				Ext.MessageBox.alert(GO.lang['strError'], error);
			},
			scope: this
		});
	},

 // kopie / aanpassing uit Files module
	sendOverwrite : function(params){ // word aangeroepen na upload, overschrijft altijd

		if(!params.command)
		{
			params.command='ask';
		}

		this.overwriteParams = params;

		Ext.Ajax.request({
			url: GO.settings.modules.bookmarks.url+'action.php',
			params:this.overwriteParams,
			callback: function(options, success, response){
				if(!success)
				{
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strRequestError']);
				}else
				{
					var responseParams = Ext.decode(response.responseText);

					//----------------------------------------------------------------
					this.example = Ext.get('one-thumb');
					this.newicon = "background-image: url("+BaseHref+"modules/bookmarks/bmthumb.php?src="+responseParams.path+"&h=32&w=32&pub=0)";//&mtime="+this.time.format('U')+")";
					this.example.dom.style.cssText=this.newicon;
					this.chosenlogo= responseParams.path;
          //-----------------------------------------------------------------
					
					if(!responseParams.success && !responseParams.file_exists)
					{
						Ext.MessageBox.alert(GO.lang['strError'], responseParams.feedback);
					}else
					{
						if(responseParams.file_exists)
						{
							this.overwriteParams.command='yes';
							this.sendOverwrite(this.overwriteParams);
						}
					}
				}
			},
			scope: this
		});
	}
});