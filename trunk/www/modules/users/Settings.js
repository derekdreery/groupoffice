GO.mainLayout.onReady(function(){
	GO.moduleManager.addSettingsPanel('regional', GO.users.RegionalSettingsPanel);
	GO.moduleManager.addSettingsPanel('look_and_feel', GO.users.LookAndFeelPanel);
	GO.moduleManager.addSettingsPanel('profile', GO.users.ProfilePanel);
	
	if(GO.settings.config.allow_password_change)
	{
		GO.moduleManager.addSettingsPanel('password', GO.users.PasswordPanel);
	}
});