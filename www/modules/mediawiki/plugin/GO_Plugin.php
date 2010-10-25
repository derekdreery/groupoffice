<?php
// First check if class and interface has already been defined.
if (!class_exists('AuthPlugin') || !interface_exists('iAuthPlugin')) {
	/**
	 * Auth Plug-in
	 *
	 */
	require_once './includes/AuthPlugin.php';

	/**
	 * Auth Plug-in Interface
	 *
	 */
	require_once './extensions/iAuthPlugin.php';

}

class GO_Plugin extends AuthPlugin implements iAuthPlugin {

	private $go_config;
	private $go_auth;
	private $go_users;
	private $user;

	function __construct($config) {
		global $GO_AUTH, $GO_USERS, $GO_SECURITY, $GO_MODULES;
		$this->go_config = $config;
		$this->go_auth = $GO_AUTH;
		$this->go_users = $GO_USERS;
		$this->go_security = $GO_SECURITY;
		$this->user = $GO_USERS->get_user($GO_SECURITY->user_id);

		// Load Hooks
		$GLOBALS['wgHooks']['UserLoginForm'][]      = array($this, 'onUserLoginForm', false);
		$GLOBALS['wgHooks']['UserLoginComplete'][]  = $this;
		$GLOBALS['wgHooks']['UserLogout'][]         = $this;
	}

	/**
	 * Check whether there exists a user account with the given name.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @return bool
	 * @public
	 */
	public function userExists( $username ) {
		$go_user = $this->go_users->get_user_by_username($username);
		return (!empty($go_user));
	}

	/**
	 * Check if a username+password pair is a valid login.
	 * The name will be normalized to MediaWiki's requirements, so
	 * you might need to munge it (for instance, for lowercase initial
	 * letters).
	 *
	 * @param $username String: username.
	 * @param $password String: user password.
	 * @return bool
	 * @public
	 */
	public function authenticate( $username, $password ) {
		$auth = $this->go_auth->authenticate($username,$password);
		return $auth>0;
	}

	/**
	 * Modify options in the login template.
	 *
	 * @param $template UserLoginTemplate object.
	 * @public
	 */
	public function modifyUITemplate( &$template ) {
		$template->set('usedomain',   false); // We do not want a domain name.
		$template->set('create',      false); // Remove option to create new accounts from the wiki.
		$template->set('useemail',    false); // Disable the mail new password box.
	}

	/**
	 * Set the domain this plugin is supposed to use when authenticating.
	 *
	 * @param $domain String: authentication domain.
	 * @public
	 */
	public function setDomain( $domain ) {

	}

	/**
	 * Check to see if the specific domain is a valid domain.
	 *
	 * @param $domain String: authentication domain.
	 * @return bool
	 * @public
	 */
	public function validDomain( $domain ) {
		return true;
	}

	/**
	 * When a user logs in, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param User $user
	 * @public
	 */
	public function updateUser( &$user ) {
		return true;
	}

	/**
	 * Return true if the wiki should create a new local account automatically
	 * when asked to login a user who doesn't exist locally but does in the
	 * external auth database.
	 *
	 * If you don't automatically create accounts, you must still create
	 * accounts in some way. It's not possible to authenticate without
	 * a local account.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return bool
	 * @public
	 */
	public function autoCreate() {
		return true;
	}

	/**
	 * Can users change their passwords?
	 *
	 * @return bool
	 */
	public function allowPasswordChange() {
		return true;
	}

	/**
	 * Set the given password in the authentication database.
	 * As a special case, the password may be set to null to request
	 * locking the password to an unusable value, with the expectation
	 * that it will be set later through a mail reset or other method.
	 *
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @param $password String: password.
	 * @return bool
	 * @public
	 */
	public function setPassword( $user, $password ) {
		return true;
	}

	/**
	 * Update user information in the external authentication database.
	 * Return true if successful.
	 *
	 * @param $user User object.
	 * @return bool
	 * @public
	 */
	public function updateExternalDB( $user ) {
		return false;
	}

	/**
	 * Check to see if external accounts can be created.
	 * Return true if external accounts can be created.
	 * @return bool
	 * @public
	 */
	public function canCreateAccounts() {
		return false;
	}

	/**
	 * Add a user to the external authentication database.
	 * Return true if successful.
	 *
	 * @param User $user - only the name should be assumed valid at this point
	 * @param string $password
	 * @param string $email
	 * @param string $realname
	 * @return bool
	 * @public
	 */
	public function addUser( $user, $password, $email='', $realname='' ) {
		return false;
	}

	/**
	 * Return true to prevent logins that don't authenticate here from being
	 * checked against the local database's password fields.
	 *
	 * This is just a question, and shouldn't perform any actions.
	 *
	 * @return bool
	 * @public
	 */
	public function strict() {
		return true;
	}

	/**
	 * When creating a user account, optionally fill in preferences and such.
	 * For instance, you might pull the email address or real name from the
	 * external user database.
	 *
	 * The User object is passed by reference so it can be modified; don't
	 * forget the & on your function declaration.
	 *
	 * @param $user User object.
	 * @param $autocreate bool True if user is being autocreated on login
	 * @public
	 */
	public function initUser( &$user, $autocreate=false ) {
		$go_user = $this->go_users->get_user_by_username($user->mName);
		$user->mEmail = $go_user['email'];
		$user->mRealName = 'I need to Update My Profile';
	}

	/**
	 * If you want to munge the case of an account name before the final
	 * check, now is your chance.
	 */
	public function getCanonicalName( $username ) {
		return $username;
	}

	/**
	 * This is the hook that runs when a user logs in. This is where the
	 * code to auto log-in a user to phpBB should go.
	 *
	 * Note: Right now it does nothing,
	 *
	 * @param object $user
	 * @return bool
	 */
	public function onUserLoginComplete(&$user) {
		return true;
	}


	/**
	 * Here we add some text to the login screen telling the user
	 * they need a phpBB account to login to the wiki.
	 *
	 * Note: This is a hook.
	 *
	 * @param string $errorMessage
	 * @param object $template
	 * @return bool
	 */
	public function onUserLoginForm($errorMessage = false, $template) {
		$template->data['message'] = 'Running from Group-Office. Can only log in as current Group-Office user.';
		$template->data['messagetype'] = 'notice';
		return true;
	}


	/**
	 * This is the Hook that gets called when a user logs out.
	 *
	 * @param object $user
	 */
	public function onUserLogout(&$user) {
		// User logs out of the wiki we want to log them out of the form too.
		if (!isset($this->_SessionTB)) {
			return true; // If the value is not set just return true and move on.
		}
		return true;
		// @todo: Add code here to delete the session.
	}
}
?>
