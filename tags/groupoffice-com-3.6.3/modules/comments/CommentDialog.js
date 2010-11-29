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
 * @author Merijn Schering <mschering@intermesh.nl>
 */
GO.comments.CommentDialog = function(config){
	if(!config)
	{
		config={};
	}
	this.buildForm();
	var focusFirstField = function(){
		this.formPanel.items.items[0].focus();
	};
	config.collapsible=true;
	config.layout='fit';
	config.modal=false;
	config.resizable=false;
	config.width=600;
	config.autoHeight=true;
	
	config.closeAction='hide';
	config.title= GO.comments.lang.comment;					
	config.items= this.formPanel;
	config.focus= focusFirstField.createDelegate(this);
	config.buttons=[{
			text: GO.lang['cmdOk'],
			handler: function(){
				this.submitForm(true);
			},
			scope: this
		},{
			text: GO.lang['cmdClose'],
			handler: function(){
				this.hide();
			},
			scope:this
		}					
	];
	GO.comments.CommentDialog.superclass.constructor.call(this, config);
	this.addEvents({'save' : true});	
}
Ext.extend(GO.comments.CommentDialog, Ext.Window,{
	show : function (comment_id, config) {
		if(!this.rendered)
		{
			this.render(Ext.getBody());
		}
		if(!comment_id)
		{
			comment_id=0;			
		}
		this.setCommentId(comment_id);
		
		delete this.link_config;
		
		if(this.comment_id>0)
		{
			this.formPanel.load({
				url : GO.settings.modules.comments.url+'json.php',
				waitMsg:GO.lang['waitMsgLoad'],
				success:function(form, action)
				{
					GO.comments.CommentDialog.superclass.show.call(this);
				},
				failure:function(form, action)
				{
					Ext.Msg.alert(GO.lang['strError'], action.result.feedback)
				},
				scope: this
			});
		}else 
		{
			this.formPanel.form.reset();
			GO.comments.CommentDialog.superclass.show.call(this);
		}
		if(config && config.link_config)
		{
			this.link_config=config.link_config;
			
			this.formPanel.baseParams.link_id=config.link_config.id;
			this.formPanel.baseParams.link_type=config.link_config.type;
		}
	},
	setCommentId : function(comment_id)
	{
		this.formPanel.form.baseParams['comment_id']=comment_id;
		this.comment_id=comment_id;
	},
	submitForm : function(hide){
		this.formPanel.form.submit(
		{
			url:GO.settings.modules.comments.url+'action.php',
			params: {'task' : 'save_comment'},
			waitMsg:GO.lang['waitMsgSave'],
			success:function(form, action){
				this.fireEvent('save', this);
				if(hide)
				{
					this.hide();	
				}else
				{
					if(action.result.comment_id)
					{
						this.setCommentId(action.result.comment_id);
					}
				}
				
				if(this.link_config && this.link_config.callback)
				{					
					this.link_config.callback.call(this);					
				}
				
			},		
			failure: function(form, action) {
				if(action.failureType == 'client')
				{					
					Ext.MessageBox.alert(GO.lang['strError'], GO.lang['strErrorsInForm']);			
				} else {
					Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback);
				}
			},
			scope: this
		});
	},
	buildForm : function () {
		    
    this.formPanel = new Ext.form.FormPanel({
	    waitMsgTarget:true,
			url: GO.settings.modules.comments.url+'action.php',
			border: false,
			autoHeight: true,
			cls:'go-form-panel',
			baseParams: {task: 'comment'},				
			items:[{
				xtype: 'textarea',
			  name: 'comments',
				anchor: '100%',
				height: 200,
				hideLabel:true
			}]				
		});
	}
});


GO.comments.showCommentDialog = function(comment_id, config){

	if(!GO.comments.commentDialog)
		GO.comments.commentDialog = new GO.comments.CommentDialog();

	if(GO.comments.commentDialogListeners){
		GO.comments.commentDialog.on(GO.comments.commentDialogListeners);
		delete GO.comments.commentDialogListeners;
	}

	GO.comments.commentDialog.show(comment_id, config);
}

GO.comments.browseComments= function (link_id, link_type)
{
	if(!GO.comments.commentsBrowser)
	{
		GO.comments.commentsBrowser = new GO.comments.CommentsBrowser();
	}
	if(GO.comments.commentDialogListeners){
		GO.comments.commentsBrowser.on(GO.comments.commentDialogListeners);
	}
	
	GO.comments.commentsBrowser.show({link_id: link_id, link_type:link_type});
};


GO.newMenuItems.push({
	text: GO.comments.lang.comment,
	iconCls: 'go-menu-icon-comments',
	handler:function(item, e){				
		GO.comments.showCommentDialog(0, {
			link_config: item.parentMenu.link_config			
		});
	}
});
