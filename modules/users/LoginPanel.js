/** 
 * Copyright Intermesh
 * 
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 * 
 * If you have questions write an e-mail to info@intermesh.nl
 * 
 * @version $Id: LoginPanel.js 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
 
GO.users.LoginPanel = function(config)
{
	if(!config)
	{
		config={};
	}
	
	config.autoScroll=true;
	config.border=false;
	config.hideLabel=true;
	config.title = GO.users.lang.loginInfo;
	config.layout='form';
	config.defaults={anchor:'100%'};
	config.defaultType = 'textfield';
	config.cls='go-form-panel';
	config.labelWidth=140;
	
	config.items=[
		new GO.form.PlainField({
			fieldLabel: GO.users.lang.cmdFormLabelRegistrationTime,
			id: 'registration_time'
		}),
		new GO.form.PlainField({
			fieldLabel: GO.users.lang.cmdFormLabelLastLogin,
			id: 'lastlogin'
		}),
		new GO.form.PlainField({
			fieldLabel: GO.users.lang.numberOfLogins,
			id: 'logins'
		})
		
	];

	GO.users.LoginPanel.superclass.constructor.call(this, config);		
}


Ext.extend(GO.users.LoginPanel, Ext.Panel,{
	

});			