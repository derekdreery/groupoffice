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
 * @author Michael de Hart <mdhart@intermesh.nl>
 */
GO.summary.AnnouncementDialog = Ext.extend(GO.dialog.TabbedFormDialog, {

	initComponent: function() {

		Ext.apply(this, {
			goDialogId: 'summaryAnnouncement',
			layout: 'fit',
			title: GO.summary.lang['announcement'],
			width: 700,
			height: 600,
			resizable: false,
			formControllerUrl: 'summary/announcement'
		});

		GO.summary.AnnouncementDialog.superclass.initComponent.call(this);
	},
	buildForm: function() {

		this.formPanel = new Ext.Panel({
			cls: 'go-form-panel',
			layout: 'form',
			title: GO.lang['strProperties'],
			labelWidth: 100,
			items:[{
				xtype: 'datefield',
				name: 'due_time',
				minValue:new Date(),
				anchor: '-5',
				format: GO.settings.date_format,
				fieldLabel: GO.summary.lang.dueTime
			},{
				xtype: 'textfield',
				name: 'title',
				anchor: '-5',
				fieldLabel: GO.summary.lang.title
			},{
				xtype: 'htmleditor',
				name: 'content',
				anchor: '-5 -60',
				hideLabel:true
			}]
		});

		this.addPanel(this.formPanel);
		this.addPermissionsPanel(new GO.grid.PermissionsPanel());
	}
});