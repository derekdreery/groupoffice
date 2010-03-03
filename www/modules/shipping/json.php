<?php
/*
 Copyright Intermesh 2003
 Author: Merijn Schering <mschering@intermesh.nl>
 Version: 1.0 Release date: 08 July 2003

 This program is free software; you can redistribute it and/or modify it
 under the terms of the GNU General Public License as published by the
 Free Software Foundation; either version 2 of the License, or (at your
 option) any later version.
*/

require('../../Group-Office.php');

$GO_SECURITY->json_authenticate('shipping');



require_once ($GO_MODULES->modules['shipping']['class_path']."shipping.class.inc.php");
$sh = new shipping();

$_task=isset($_REQUEST['task']) ? ($_REQUEST['task']) : '';

try {

	switch($_task) {
		case 'jobs':

			$start = isset($_POST['start']) ? $_POST['start'] : 0;
			$limit = isset($_POST['limit']) ? $_POST['limit'] : 0;

			$response['total'] = $sh->get_jobs($start,$limit);
			while($job = $sh->next_record()) {
				$response['results'][] = $job;
			}
			$response['success']=true;
			break;

		case 'job_packages':
			$start = isset($_POST['start']) ? $_POST['start'] : 0;
			$limit = isset($_POST['limit']) ? $_POST['limit'] : 0;
			$anode = isset($_REQUEST['anode']) ? $_REQUEST['anode'] : 0;
			if (!$anode) {
				$response['total'] = $sh->get_jobs($start,$limit);
				while($job = $sh->next_record()) {
					//$job['_level'] = 1;
					$manager = $GO_USERS->get_user($job['user_id']);
					$job['manager'] = String::format_name($manager);
					$packer = $GO_USERS->get_user($job['packer_user_id']);
					$job['packer'] = String::format_name($packer);
					$job['_is_leaf'] = false;
					$job['_parent'] = null;
					$job['_id'] = $job['id'];
					$job['status'] = $job['packed'].'/'.$job['items'].' packed';
					$job['ctime'] = date("Y-m-d h:i:s a",$job['ctime']);
					//$job['_is_loaded'] = true;
					$response['results'][] = $job;
				}
			} else {
				$job_id = $anode;
				$response['total'] = $sh->get_job_packages($job_id);
				$sh2 = new shipping();
				while($jobpackage = $sh->next_record()) {
					//$jobpackage['_level'] = 2;
					$container = $sh2->get_container($jobpackage['container_id']);
					$jobpackage['container_no'] = $container['container_no'];
					$manager = $GO_USERS->get_user($jobpackage['user_id']);
					$jobpackage['manager'] = String::format_name($manager);
					$packer = $GO_USERS->get_user($jobpackage['packer_user_id']);
					$jobpackage['packer'] = String::format_name($packer);
					$jobpackage['_is_leaf'] = true;
					$jobpackage['_parent'] = $job_id;
					$jobpackage['_id'] = $job_id.$jobpackage['id']; // $jobpackage['package_no']; //$job_id.'-'.$jobpackage['id'];
					$jobpackage['id'] = $jobpackage['package_no'];
					$jobpackage['ctime'] = date("Y-m-d h:i:s a",$jobpackage['ctime']);
					//$jobpackage['_is_loaded'] = true;
					$response['results'][] = $jobpackage;
				}
			}
			$response['success']=true;

			break;

		case 'jobs':

			$start = isset($_POST['start']) ? $_POST['start'] : 0;
			$limit = isset($_POST['limit']) ? $_POST['limit'] : 0;

			$response['total'] = $sh->get_containers($start,$limit);
			while($job = $sh->next_record()) {
				$response['results'][] = $job;
			}
			$response['success']=true;
			break;
	}
}catch(Exception $e) {
	$response['feedback']=$e->getMessage();
	$response['success']=false;
}
echo json_encode($response);
