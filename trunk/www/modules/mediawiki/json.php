<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: json.php 5426 2009-08-04 15:01:52Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('timeregistration');

require_once($GO_MODULES->modules['mediawiki']['class_path'].'mediawiki.class.inc.php');
$mw = new mediawiki();

//define('MW_PATH','/var/www/mediawiki/');
//require_once(MW_PATH.'includes/User.php');
//$User = new User();

$task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try{
	switch($task)
	{

		case 'login':
/*
			header("Content-type: text/plain;");

			$name = 'testpersoon';//urlencode( "spi{$user_id}" );
			$password = 'q1w2e3';//urlencode( $password );
			$ch = curl_init( "http://localhost/mediawiki/api.php" );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, "action=login&lgname={$name}&lgpassword={$password}&format=xml" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$str = curl_exec( $ch );

			$xml = simplexml_load_string( $str );
			$token = $xml->login->attributes()->token;
			$cookieprefix = $xml->login->attributes()->cookieprefix;
			$sessionid = $xml->login->attributes()->sessionid;

			//some versions of mediawiki are buggy and give a blank cookie prefix
			//so detect that and find the real prefix
			if( $cookieprefix == '' ) {
					curl_setopt( $ch, CURLOPT_HEADER, true );
					$str = curl_exec( $ch );
					curl_setopt( $ch, CURLOPT_HEADER, false );
					preg_match( '/ (.*?)_session=/', $str, $m );
					$cookieprefix = $m[1];
			}

			$cookie = "{$cookieprefix}_session={$sessionid}";

			curl_setopt( $ch, CURLOPT_COOKIE, $cookie );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, "action=login&lgname={$name}&lgpassword={$password}&lgtoken={$token}&format=json" );
			$response = json_decode(curl_exec( $ch ));
			curl_close( $ch );
*/
			/*
			require_once($GO_MODULES->modules['mediawiki']['path'].'snoopy/Snoopy.class.php');

			$snoopy = new Snoopy();

			$snoopy = new Snoopy;
			$snoopy->curl_path="/usr/bin/curl";
			$wikiroot = "http://localhost/mediawiki/";
			$api_url = $wikiroot . "api.php";

			# Login via api.php
			$login_vars = array(
				'action' => 'login',
				'lgname' => 'testpersoon',
				'lgpassword' => 'q1w2e3',
				'format' => 'php'
						);
			
			## First part
			$snoopy->submit($api_url,$login_vars);
			$response = unserialize($snoopy->results);
			$login_vars['lgtoken'] = $response['login']['token'];
			$snoopy->cookies[$response['login']['cookieprefix'].'_session'] = $response['login']['sessionid']; // You may have to change 'wiki_session' to something else on your Wiki
			## Second part, now that we have the token
			$login_vars['format'] = 'json';
			$snoopy->submit($api_url,$login_vars);
			$response = json_decode($snoopy->results);
			*/

			/*
			# Fetch the page
			$login_vars['action'] = "render";
			$urlpart='Main_Page';
			$snoopy->submit($wikiroot . "index.php?title=" . $urlpart, $login_vars);
			print($snoopy->results);
			*/

			$_data = array(
				'action' => 'login',
				'lgname' => 'Testpersoon',
				'lgpassword' => 'q1w2e3',
				'format' => 'json'
						);

			list($header,$response) = mediawiki::postRequest('http://localhost/mediawiki/api.php', 'http://localhost/', $_data);

			$response = json_decode($response);
			$header = array("Set-Cookie: ".$response->login->cookieprefix."_session=".$response->login->sessionid.'; domain='.'localhost; '.' path=/; HttpOnly');

			if ($response->login->result=='NeedToken') {
				$_data = array(
					'action' => 'login',
					'lgname' => 'testpersoon',
					'lgpassword' => 'q1w2e3',
					'lgtoken' => $response->login->token,
					'format' => 'json'
				);
				list($header,$response) = mediawiki::postRequest('http://localhost/mediawiki/api.php', 'http://localhost/', $_data,$header);

				$response = json_decode($response);
			}

			$response->url = 'http://localhost/mediawiki/index.php';

			/*
			global $GO_USERS, $GO_SECURITY;
			$user = $GO_USERS->get_user($GO_SECURITY->user_id);
			//$_SESSION['GO_SESSION']['wsUserID'] = $GO_SECURITY->user_id;
			$response['url'] = 'http://localhost/mediawiki/index.php?wsUserID='.$GO_SECURITY->user_id;//?title=Special:UserLogin&action=submitlogin&type=login&wpName='.$user['username'].'&wpPassword='.$user['password'].'&wpLoginAttempt=Log+in';
			//$response['url'] = 'http://localhost/mediawiki/index.php?wpName='.$user['username'].'&wpPassword='.$user['password'].'&wpLoginAttempt=Log+in';
			$response['success'] = true;
*/

			break;

/* {TASKSWITCH} */
	}
}catch(Exception $e)
{
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);