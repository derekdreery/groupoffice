this.selectUser = new GO.form.SelectUser({
				fieldLabel: GO.lang['strUser'],
				disabled: !GO.settings.modules['{module}']['write_permission'],
				value: GO.settings.user_id,
				anchor: '-20'
			})