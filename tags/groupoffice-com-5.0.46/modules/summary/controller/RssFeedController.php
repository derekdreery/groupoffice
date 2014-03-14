<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */
class GO_Summary_Controller_RssFeed extends GO_Base_Controller_AbstractModelController {

	protected $model = 'GO_Summary_Model_RssFeed';

	protected function actionSaveFeeds($params) {

		$feeds = json_decode($params['feeds'], true);
		$ids = array();

		$response['data'] = array();
		foreach ($feeds as $feed) {
			//$feed['user_id'] = GO::user()->id;
			
			if(!empty($feed['id']))
				$feedModel = GO_Summary_Model_RssFeed::model()->findByPk($feed['id']);
			else
				$feedModel = new GO_Summary_Model_RssFeed();
			
			$feedModel->setAttributes($feed);
			$feedModel->save();
			$feed['id'] = $feedModel->id;

			$ids[] = $feed['id'];
			$response['data'][$feed['id']] = $feed;
		}

		// delete other feeds
		$feedStmt = GO_Summary_Model_RssFeed::model()
						->find(
						GO_Base_Db_FindParams::newInstance()
						->criteria(
										GO_Base_Db_FindCriteria::newInstance()
										->addCondition('user_id', GO::user()->id)
										->addInCondition('id', $ids, 't', true, true)
						)
		);
		while ($deleteFeedModel = $feedStmt->fetch())
			$deleteFeedModel->delete();

		$response['ids'] = $ids;
		$response['success'] = true;

		return $response;
	}

	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		$storeParams->getCriteria()->addCondition('user_id', GO::user()->id);
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
	protected function getStoreParams($params) {
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
						->addCondition('user_id', GO::user()->id);
		return GO_Base_Db_FindParams::newInstance()
						->criteria($findCriteria);
	}

	
	protected function actionProxy($params) {
		$feed = $params['feed'];
		if ($feed != '' && strpos($feed, 'http') === 0) {
			header('Content-Type: text/xml');

			if (function_exists('curl_init')) {				
				$httpclient = new GO_Base_Util_HttpClient();
				$xml = $httpclient->request($feed);
			} else {
				if (!GO_Base_Fs_File::checkPathInput($feed))
					throw new Exception("Invalid request");

				$xml = @file_get_contents($feed);
			}

			if ($xml) {				
				//fix relative images
				preg_match('/(.*:\/\/[^\/]+)\//',$feed, $matches);				
				$baseUrl = $matches[1];				
				$xml = str_replace('src=&quot;/', 'src=&quot;'.$baseUrl.'/', $xml);
				$xml = str_replace('src="/', 'src=&quot;'.$baseUrl.'/', $xml);
				
				$xml = str_replace('href=&quot;/', 'href=&quot;'.$baseUrl.'/', $xml);
				$xml = str_replace('href="/', 'href="'.$baseUrl.'/', $xml);
				
				$xml = str_replace('<content:encoded>', '<content>', $xml);
				$xml = str_replace('</content:encoded>', '</content>', $xml);
				$xml = str_replace('</dc:creator>', '</author>', $xml);
				echo str_replace('<dc:creator', '<author', $xml);
			}
		}
	}

}

