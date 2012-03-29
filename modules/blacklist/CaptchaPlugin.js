Ext.override(GO.dialog.LoginDialog, {
		initComponent : GO.dialog.LoginDialog.prototype.initComponent.createSequence(function(){
			this.formPanel.add(this.captchaPanel = new Ext.Panel({
				border:false,
				hidden:true,
				layout:'form',
				waitMsgTarget:true,
				items:[
					this.captchaText = new Ext.form.TextField({
						fieldLabel: GO.lang.captcha,
						labelWidth: 120,
						name: 'captcha',
						anchor:'73%'
					}),
					this.captchaImage = new GO.CaptchaImage(),
					this.captchaLink  = new GO.CaptchaLink()
				]
			}))
		}),

		requireCaptcha : false,

		doLogin : function(){

			if(this.requireCaptcha && this.captchaText.getValue() == '')
			{
				alert(GO.lang.captchaEmpty);
			}else
			{
				this.formPanel.form.submit({
					url:BaseHref+'action.php',
					params: {
						'task' : 'login'
					},
					waitMsg:GO.lang.waitMsgLoad,
					success:function(form, action){

						this.requireCaptcha = false;
						//Another user logs in after a session expire
						if(GO.settings.user_id>0 && action.result.user_id!=GO.settings.user_id)
						{
							document.location=document.location;
							return true;
						}

						if(action.result.name=='')
						{
							this.completeProfileDialog();
							this.profileFormPanel.form.setValues({email:action.result.email});
						}else
						{
							this.handleCallbacks();
						}

						if(this.hideDialog)
							this.hide();

					},

					failure: function(form, action) {
						if(action.result)
						{
							Ext.MessageBox.alert(GO.lang['strError'], action.result.feedback, function(){
								this.formPanel.form.findField('username').focus(true);
							},this);

							this.requireCaptcha = (action.result.require_captcha) ? action.result.require_captcha : false;

							this.captchaPanel.setVisible(this.requireCaptcha);
							this.doLayout();
						}
						if(this.requireCaptcha)
						{
							generateLink();
						}
						this.captchaText.reset();
					},
					scope: this
				});
			}
		}
	});

	GO.CaptchaImage = Ext.extend(Ext.BoxComponent, {
		onRender : function(ct, position){
			this.el = ct.createChild({
				cls:'go-login-captcha',
				tag: 'img',
				id:'captcha',
				src: BaseHref+'classes/securimage/securimage_show.php'
			});
		}
	});
	GO.CaptchaLink = Ext.extend(Ext.BoxComponent, {
		onRender : function(ct, position){
			this.el = ct.createChild({
				cls:'go-login-captcha-link',
				tag: 'img',
				onclick:"generateLink();",
				src: BaseHref+'classes/securimage/images/refresh.gif'
			});
		}
	});

	function generateLink()
	{
		document.getElementById('captcha').src = 'classes/securimage/securimage_show.php?'+Math.random();
	}

	// next override is needed to unhide the component in Chrome
//	Ext.override(Ext.Component, {
//		onShow : function(){
//
//			this.getVisibilityEl().removeClass('x-hide-' + this.hideMode);
//
//			if(Ext.isWebKit) {
//				this.getVisibilityEl().show();
//			}
//    },
//		onHide : function(){
//
//			this.getVisibilityEl().addClass('x-hide-' + this.hideMode);
//
//			if(Ext.isWebKit) {
//				this.getVisibilityEl().hide();
//			}
//    }
//	});
