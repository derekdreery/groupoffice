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

GO.email.AttachmentContextMenu = function(config)
{
	if(!config)
		config = {};

	config['shadow']='frame';
	config['minWidth']=180;
	
	this.downloadButton = new Ext.menu.Item({
					iconCls: 'btn-download',
					text: GO.lang.download,
					cls: 'x-btn-text-icon',
					handler: function(){						
						GO.email.openAttachment(
							this.attachment,
							this.messagePanel,
							true);
					},
					scope: this
				});
	config.items=[this.downloadButton];
	if(GO.files && !config.removeSaveButton)
	{
		this.saveButton = new Ext.menu.Item({
					iconCls: 'btn-save',
					text: GO.lang.cmdSave,
					cls: 'x-btn-text-icon',
					handler: function(){
						GO.email.saveAttachment(this.attachment,this.messagePanel);
					},
					scope: this
				});
		config.items.push(this.saveButton);
	}						

					
	GO.email.AttachmentContextMenu.superclass.constructor.call(this, config);	
}

Ext.extend(GO.email.AttachmentContextMenu, Ext.menu.Menu,{
	attachment : false,

	showAt : function(xy, attachment)
	{ 	
		this.attachment = attachment;
		
		GO.email.AttachmentContextMenu.superclass.showAt.call(this, xy);
	}	
});