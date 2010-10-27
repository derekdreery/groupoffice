<?php
require_once('imapauth.config.php');
$domains = json_encode(explode(',',$config[0]['imapauth_combo_domains']));
$default_domain = $config[0]['imapauth_default_domain'];

if(!empty($domains) && !empty($default_domain)){ ?>

<script type='text/javascript'>
Ext.override(GO.dialog.LoginDialog, {
	initComponent : GO.dialog.LoginDialog.prototype.initComponent.createSequence(function(){
		var domains = <?php echo $domains ?>;
		domains.push('');
		var domainData = new Array();
		domainData[0] = ['no domain', domains[i]]
		for (var i=0; i<domains.length; i++) {
			if (domains[i]!='')
				domainData[i+1] = [domains[i], domains[i]];
		}

		var usernameField = this.formPanel.items.get('username');
		var fieldLabel = usernameField.fieldLabel;
		delete usernameField.fieldLabel;
		usernameField.flex=1;

		this.usernameCompositeField = new Ext.form.CompositeField({
			anchor:'100%',
			fieldLabel: fieldLabel,
			items:[
				usernameField,
				{
					flex:1,
					xtype:'combo',
					hideLabel: true,
					triggerAction : 'all',
					editable : false,
					selectOnFocus : true,
					width : 144,
					forceSelection : true,
					mode : 'local',
					//value : GO.monkeytown.roundMinutes,
					hiddenName : 'domain',
					valueField : 'value',
					displayField : 'name',
					store : new Ext.data.SimpleStore({
							fields: ['name', 'value'],
							data: domainData
						})
				}
			]
		})

		this.formPanel.insert(2,this.usernameCompositeField);
	}),
	show : function(){
		this.formPanel.load({	url : BaseHref+'modules/imapauth/get_domains.php?task=default_domain' });
		GO.dialog.LoginDialog.superclass.show.call(this);
	}
});
</script>

<?php }
	?>