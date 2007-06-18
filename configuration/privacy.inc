<?php
if ($task == 'authorize')
{
	if ($user)
	{
		if ($req_user = $GO_USERS->get_user_by_email($_POST['auth_email_address']))
		{
			$mail_body  = '<html><body>';

			$middle_name = $req_user['middle_name'] == '' ? '' : $req_user['middle_name'].' ';
			$req_user_name = $middle_name.$req_user['last_name'];

			$middle_name = $user['middle_name'] == '' ? '' : $user['middle_name'].' ';
			$user_name = $user['first_name'].' '.$middle_name.$user['last_name'];

			$mail_body .= $ac_salutation.' '.$sir_madam[$req_user['sex']].' '.$req_user_name.',<br /><br />';
			$mail_body .= $user_name.' '.$ac_request_auth.'<br /><br />';

			$mail_body .= '<a href="'.$GO_CONFIG->full_url.'configuration/index.php?requesting_user_id='.$GO_SECURITY->user_id.'&authcode='.$user['authcode'].'&requested_user_id='.$req_user['id'].'&task=accept'.'">'.$ac_accept.'</a>&nbsp;&nbsp;&nbsp;';
			$mail_body .= '<a href="'.$GO_CONFIG->full_url.'configuration/index.php?requesting_user_id='.$GO_SECURITY->user_id.'&authcode='.$user['authcode'].'&requested_user_id='.$req_user['id'].'&task=decline'.'">'.$ac_decline.'</a>&nbsp;&nbsp;&nbsp;';
			$mail_body .= '</body></html>';
			if(!sendmail($req_user['email'], $user['email'], $GO_CONFIG->title, $ac_request_auth_mail_title, $mail_body,'3 (Normal)', 'text/html'))
			{
				$feedback = '<p class="Error">'.$strEmailFail.'</p>';
				$task = 'add_user';
			}

		}else
		{
			$feedback =$ac_invalid_email;
			$task = 'add_user';
		}
	}else
	{
		$feedback = $strDataError;
		$task = 'add_user';
	}
}

if (isset($feedback))
{
	echo $feedback;
}

switch($task)
{
	case 'add_user':	
	
		if (isset($feedback))
		{
		  $p = new html_element('p', $feedback);
		  $p->set_attribute('class','Error');
		  $tabstrip->add_html_element($p);
		}
			
		
		$p = new html_element('p', $ac_auth_text);
		$tabstrip->add_html_element($p);
		
		$input = new input('text', 'auth_email_address');
		$input->set_attribute('style', 'width:300px;');		
		$p = new html_element('p', $strEmail.': '.$input->get_html());
		$tabstrip->add_html_element($p);
		
		$button = new button($cmdOk, "javascript:_save('send_request');");
		$tabstrip->add_html_element($button);
		$button = new button($cmdCancel, "javascript:_save('privacy');");
		$tabstrip->add_html_element($button);
	break;

	case 'authorize':
		$p = new html_element('p', $ac_request_mail_body_prefix.' '.$req_user['first_name'].' '.$req_user_name.' '.$ac_request_mail_body_suffix);
		$tabstrip->add_html_element($p);
		$button = new button($cmdContinue, "javascript:save('privacy');");
		$tabstrip->add_html_element($button);
	break;


	default:
	
		$hyperlink = new hyperlink("javascript:_save('add_user');", $ac_add_user);
		$hyperlink->set_attribute('class', 'normal');
		$p = new html_element('p',$privacy_text);
		
		$tabstrip->add_html_element($hyperlink);
		$tabstrip->add_html_element($p);
		$tabstrip->innerHTML .= get_acl($user['acl_id'], false);
	break;
}
