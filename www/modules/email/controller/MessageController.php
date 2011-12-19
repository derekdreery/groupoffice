<?php

class GO_Email_Controller_Message extends GO_Base_Controller_AbstractController {

	public function actionSend($params) {
		
	}

	public function actionTemplate($params) {
		$template = GO_Addressbook_Model_Template::model()->findByPk($template_id);

		$message = GO_Email_Model_SavedMessage::model()->createFromMimeData($template->content);
		$response['data'] = $message->toOutputArray();

		$presetbody = isset($params['body']) ? $params['body'] : '';
		if (!empty($presetbody) && strpos($response['data']['body'], '{body}') == false) {
			$response['data']['body'] = $params['body'] . '<br />' . $response['data']['body'];
		} else {
			$response['data']['body'] = str_replace('{body}', $presetbody, $response['data']['body']);
		}

		unset($response['data']['to'], $response['data']['cc'], $response['data']['bcc'], $response['data']['subject']);

		if (empty($params['keepTags'])) {
			$values = array();
			//$contact_id=0;
			//if contact_id is not set but email is check if there's contact info available
			if (!empty($params['to']) || !empty($params['contact_id'])) {

				if (!empty($params['contact_id'])) {
					$contact = GO_Addressbook_Model_Contact::model()->findByPk($params['contact_id']);
				} else {
					$email = GO_Base_Util_String::get_email_from_string($params['to']);
					$contact = GO_Addressbook_Model_Contact::model()->findSingleByAttribute('email', $email);
				}

				if ($contact) {
					$response['data']['body'] = GO_Addressbook_Model_Template::model()->replaceContactTags($response['data']['body'], $contact);
				} else {
					$response['data']['body'] = GO_Addressbook_Model_Template::model()->replaceUserTags($response['data']['body']);
				}
			} else {
				$response['data']['body'] = GO_Addressbook_Model_Template::model()->replaceUserTags($response['data']['body']);
			}
		}

		if ($params['content_type'] == 'plain') {
			$response['data']['body'] = String::html_to_text($response['data']['body'], false);
		}
		
		return $response;
	}

}