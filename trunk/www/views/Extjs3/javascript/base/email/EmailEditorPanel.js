/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @copyright Copyright Intermesh
 * @author Wilmar van Beusekom <wilmar@intermesh.nl>
 */

// parameter attachments must be passed by reference

/**
 * This is necessary in the corresponding controller:
 * 	protected function beforeSubmit(&$response, &$model, &$params) {
		
		$message = new GO_Base_Mail_Message();
		$message->handleEmailFormInput($params);
		
		$model->content = $message->toString();
		
		return parent::beforeSubmit($response, $model, $params);
	}
	
	
	protected function afterLoad(&$response, &$model, &$params) {
		
		// create message model from client's content field, turned into HTML format
		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($model->content);
	
		$response['data'] = array_merge($response['data'], $message->toOutputArray());

		return parent::afterLoad($response, $model, $params);
	}
 */

GO.base.email.EmailEditorPanel = function(config){
	
	config = config || {};
	 
	//Ext.apply(this, config);	
	
	config.htmlEditorConfig = config.htmlEditorConfig || {};
			
	this._initItems(config);
	
	config.layout='form';
	config.border=false;
	
	config.listeners = {
		render:function(){
			var formPanel = this.findParentByType(Ext.form.FormPanel);
			formPanel.form.on('actioncomplete', function(form, action){
				if(action.type=='load'){
					this._afterLoad(action);
				}
			}, this);
		},
		scope:this
	}
	
	GO.base.email.EmailEditorPanel.superclass.constructor.call(this,config);
	
};

Ext.extend(GO.base.email.EmailEditorPanel, Ext.Panel, {

	// [ [url:"",tmp_file:"relative/path"]]
	inlineAttachments : [],
	
	_afterLoad : function(action){
		this.setInlineAttachments(action.result.data.inlineAttachments);
	},
	
	_initItems : function(config) {

		config.items = config.items || new Array();

		var htmlEditorConfig = Ext.apply({
			hideLabel: true,
			name: 'body',
			defaultFont:'arial',
			border: false,				
			style: 'font: 12px Arial, Helvetica, sans-serif;',
			anchor: '100% 100%'
		}, config.htmlEditorConfig);

		if (htmlEditorConfig.enableInlineAttachments) {
			this.inlineAttachments = new Array();
			this.hiddenInlineImagesField = new Ext.form.TextField({
				hidden: true,
				name: 'inlineAttachments'
			});
			config.items.push(this.inlineAttachments);
			config.items.push(this.hiddenInlineImagesField);
			
			htmlEditorConfig.plugins = this._initHtmlEditorPlugins();
		}

		this.htmlEditor = new Ext.form.HtmlEditor(htmlEditorConfig);

		config.items.push(this.htmlEditor);

	},
	
	_initHtmlEditorPlugins : function(htmlEditorConfig) {
		htmlEditorConfig = htmlEditorConfig || {};
		
		if (typeof(this.inlineAttachments)!='undefined') {
			// optional image attachment
			this.imageAttachPlugin = new GO.plugins.HtmlEditorImageInsert();
			this.imageAttachPlugin.on('insert', function(plugin, path, url) {
				this.inlineAttachments.push({
					tmp_file : path,
					url : url
				});
				
				this.setInlineAttachments(this.inlineAttachments);
				
	
			}, this);

			if (htmlEditorConfig.plugins)
				htmlEditorConfig.plugins.push(this.imageAttachPlugin);
			else
				htmlEditorConfig.plugins = new Array(this.imageAttachPlugin);
		} else {
			htmlEditorConfig.plugins = htmlEditorConfig.plugins || new Array();
		}
		return htmlEditorConfig.plugins;
	},
	
	setInlineAttachments : function(inlineAttachments){
		this.inlineAttachments = inlineAttachments;
		this.hiddenInlineImagesField.setValue(Ext.encode(this.inlineAttachments));
	}
});



