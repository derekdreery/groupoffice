<?php

/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

class GO_Summary_Controller_RssFeed extends GO_Base_Controller_AbstractModelController{
	
	protected $model = 'GO_Summary_Model_RssFeed';
	
	protected function actionSaveFeeds($params) {
		
		$feeds = json_decode($params['feeds'], true);
		$ids = array();
		
		$response['data'] = array();
		foreach($feeds as $feed)
		{
			$feed['user_id'] = GO::user()->id;
			// Hack for the table being updated correctly.
			if(isset($feed['summary']) && $feed['summary'] === true)
				$feed['summary'] = 1;
			else
				$feed['summary'] = 0;

			if ($feed['feedId']>0) {
				$feed['id'] = $feed['feedId'];
			}
			unset($feed['feedId']);
				
			$feedModel = new GO_Summary_Model_RssFeed();
			$feedModel->setAttributes($feed);
			$feedModel->setIsNew(!isset($feed['id']));
			$feedModel->save();
			$feed['id'] = $feedModel->id;
			
			$ids[] = $feed['id'];
			$response['data'][$feed['id']]=$feed;
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
		$response['success']=true;
		
		return $response;
	}
	
	protected function beforeStoreStatement(array &$response, array &$params, GO_Base_Data_AbstractStore &$store, GO_Base_Db_FindParams $storeParams) {
		$storeParams->getCriteria()->addCondition('user_id',GO::user()->id);
		return parent::beforeStoreStatement($response, $params, $store, $storeParams);
	}
	
	protected function actionWebFeeds($params) {
//		if(isset($_POST['delete_keys']))
//		{
//			try{
//				$response['deleteSuccess']=true;
//				$delete_webfeeds = json_decode(($_POST['delete_keys']));
//				foreach($delete_webfeeds as $webfeed_id)
//				{
//					$summary->delete_webfeed($webfeed_id);
//				}
//			}catch(Exception $e)
//			{
//				$response['deleteSuccess']=false;
//				$response['deleteFeedback']=$e->getMessage();
//			}
//		}
		
		$getActive = isset($_POST['active']) && $_POST['active']=='true';
		$response['total'] = 0;
		$response['results'] = array();
		
		$findCriteria = GO_Base_Db_FindCriteria::newInstance()
			->addCondition('user_id',GO::user()->id);
		if ($getActive)
			$findCriteria
				->mergeWith(
					GO_Base_Db_FindCriteria::newInstance()
						->addCondition('due_time','UNIX_TIMESTAMP()','>')
						->addCondition('due_time','0','=',false)
				);
		
		$feedsStmt = GO_Summary_Model_RssFeed::model()
			->find(
				GO_Base_Db_FindParams::newInstance()
					->criteria($findCriteria)
			);
		while ($feedModel = $feedsStmt->fetch()) {
			$response['total']+=1;
			$response['results'][] = array(
				'feedId' => $feedModel->id,
				'user_id' => $feedModel->user_id,
				'title' => $feedModel->title,
				'url' => $feedModel->url
			);
		}

		$response['success'] = true;
		return $response;
	}
	
}

