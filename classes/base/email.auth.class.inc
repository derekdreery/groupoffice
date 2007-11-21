<?php
/**
 * @copyright Copyright &copy; Intermesh 2003
 * @version $Revision: 1.34 $ $Date: 2006/10/27 12:23:17 $
 * @since Group-Office 2.05
 * 
 * @author Markus Schabel <markus.schabel@tgm.ac.at>
 * @author Merijn Schering <mschering@intermesh.nl>

   This file is part of Group-Office.

   Group-Office is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   Group-Office is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Group-Office; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 */

/*
 * We have to declare that $GO_CONFIG is a global variable, because it cannot
 * be guaranteed that this file is included from a global context. It should,
 * but the current implementation of phpUnit loads this file dynamically from
 * inside a function, so that it doesn't find this variable otherwise.
 */
global $GO_CONFIG;

/*
 * This file is overriding some of the functions that are defined in the
 * base_auth class. So we need to include this class.
 */
require_once( $GO_CONFIG->class_path.'base/base.auth.class.inc' );

/*
 * To authenticate a user against an IMAP or POP3 server, the corresponding
 * methods are needed. So we have to load the IMAP class.
 */
require_once( $GO_CONFIG->class_path.'mail/imap.class.inc' );

/**
 * Implementation of POP3 and IMAP Authentication.
 * 
 * This class provides the login-function for an IMAP based authentication,
 * which means that it makes possible to authenticate users against a mail
 * server - IMAP and POP3 are supported.
 * 
 * @package Framework
 * @subpackage Usermanagement
 * @category Authentication
 * 
 * @access protected
 * 
 * @uses base_auth
 */
class email_auth extends base_auth
{
	/**
	 * Authenticate the user against an eMAIL server.
	 * 
	 * This function authenticates a given user and password against the MAIL
	 * server. When this works, we indicate our success with a return result
	 * of true, otherwise we return false.
	 * 
	 * @access private
	 * 
	 * @param string $username is the username we should authenticate.
	 * @param string $password is the user's password, we should use.
	 * 
	 * @return boolean true if the authentication was successful, and false if
	 * the authentication has failed.
	 * 
	 * @todo we have to add the $params parameter to this function!
	 */
	function authenticate( $username, $password ) {
		// We fetch the domain from the parameters, because it is needed
		// multiple times.
		$domain = $params['domain'];

		// If it is configured to add the domain to the username, we have to
		// do this before we start the authentication.
		if ( $params['add_domain_to_username'] ) {
			$username .= '@' . $domain;
		}

		// It is possible to check the validity of the domain that has been
		// given at the login, when using a local postfix installation.
		if ( isset( $params['postfix_virtual_file'] ) ) {
			$pvf = $params['postfix_virtual_file'];
			if ( file_exists( $pvf ) ) {
				if ( !$this->validate_user_domain( $pvf, $username, $domain ) ) {
					return false;
				}
			}
		}

		$imap = new imap();
		if ( !$imap->open( $params['host'], $params['proto'], $params['port'], $username, $password, 'INBOX', 0, $params['ssl'], $params['novalidate_cert'] ) ) {
			return false;
		}

		return true;
	}

	/**
   * This function logs a user in using a mail server
   * 
   * @access public
   * 
   * @param string $username
   * @param string $password
   * @param array $params	The authentication source specified in auth_sources.inc
   * 
   * @return bool
   */
	function login($username, $password, $params)
	{
		global $GO_CONFIG, $GO_SECURITY, $GO_LANGUAGE, $GO_USERS, $GO_GROUPS,
		$GO_MODULES;

		$GO_SECURITY->user_id = 0;

		require_once($GO_CONFIG->class_path.'mail/imap.class.inc');
		$imap = new imap();

		$email_address = $username.'@'.$params['domain'];
		if ($params['add_domain_to_username']) {
			$username = $email_address;
		}
		
		if(isset($params['postfix_virtual_file']) && file_exists($params['postfix_virtual_file']))
		{
			if(!$this->validate_user_domain($params['postfix_virtual_file'], $username, $params['domain']))
			{
				return false;				
			}
		}
		
		

		if ($imap->open($params['host'], $params['proto'], $params['port'],
		$username, $password, 'INBOX', 0, $params['ssl'], $params['novalidate_cert']))
		{
			$imap->close();

			if ( $profile = $GO_USERS->get_user_by_username( $username ) ) {
				$user_id = $profile['id'];
				if($profile['enabled'] != '1')
				{
					return false;
				}
			} else {
				$user['email'] =$email_address;
				$user['username'] = $username;
				$user['password'] = $password;
				$user['sex'] = 'M';
				// the user does not exist, so we have to add him.
				if ( !$user_id = $GO_USERS->add_user(
						$user, 
						$GO_GROUPS->groupnames_to_ids($params['groups']), 
						$GO_GROUPS->groupnames_to_ids($params['visible_groups']), 
						$params['modules_read'], 
						$params['modules_write']))
				{
					die('FATAL ERROR: Failed adding mail user to Group-Office');
				} else {
				
					$old_umask = umask( 000 );
					@mkdir( $GO_CONFIG->file_storage_path.'users/'.$email_address, $GO_CONFIG->create_mode );
					umask($old_umask);

					if ($params['create_email_account'])
					{
						$email_module = $GO_MODULES->get_module('email');

						if($email_module)
						{
							require_once($email_module['class_path']."email.class.inc");
							require_once($GO_LANGUAGE->get_language_file('email'));
							$email_client = new email();					
							if (!$account_id = $email_client->add_account($user_id,
							$params['proto'],
							$params['host'],
							$params['port'],
							$params['ssl'],
							$params['novalidate_cert'],
							$params['mbroot'],
							$username,
							$password,
							$username,
							$email_address,
							"",
							$params['auto_check_email']
							))
							{
								echo "<p class=\"Error\">".$registration_email_error."</p>";
								echo "<p class=\"Error\">".$email_client->last_error."</p>";
							}else
							{
								$account = $email_client->get_account($account_id);
								$email_client->synchronize_folders($account);
							}
						}
					}
					$profile = $GO_USERS->get_user($user_id);
				}
			}
			//$username = $email_address;
			//if local password is different from external the update it
			if(md5($password) != $profile['password'])
			{
				$GO_USERS->update_password($user_id, $password);

				$email_module = $GO_MODULES->get_module('email');

				if($email_module)
				{
					require_once($email_module['class_path']."email.class.inc");
					$email_client = new email();
					$email_client->update_password($params['host'], $username, $password);
				}
			}

			
			// Actualise session and other necessary things.
			$this->updateAfterLogin( $user_id );

			return true;
		}
		return false;
	}
	
	function validate_user_domain($virtual_file, $username, $domain)
	{
		$lines = file($virtual_file);

		foreach ($lines as $line) 
		{
			if(preg_match('/.*'.$domain.'\s+'.$username.'/i', $line))
			{
				return true;
			}
		}
	}	
}
