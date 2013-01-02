GO.mainLayout.onReady(function(){

	GO.moduleManager.addSettingsPanel('regional', GO.users.RegionalSettingsPanel);
	GO.moduleManager.addSettingsPanel('look_and_feel', GO.users.LookAndFeelPanel);
	GO.moduleManager.addSettingsPanel('user_settings', GO.users.UserSettingsTab);

	if(GO.settings.config.allow_password_change && (!GO.ldapemail || (GO.ldapemail && !GO.ldapemail.ldapUser)))
	{
		GO.moduleManager.addSettingsPanel('password', GO.users.PasswordPanel);
	}
});