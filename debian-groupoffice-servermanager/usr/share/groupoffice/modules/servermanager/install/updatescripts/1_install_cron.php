<?php
$cron = new GO_Base_Cron_CronJob();
		
$cron->name = 'Subcron';
$cron->active = true;
$cron->runonce = false;
$cron->minutes = '*';
$cron->hours = '*';
$cron->monthdays = '*';
$cron->months = '*';
$cron->weekdays = '*';
$cron->job = 'GO_Servermanager_Cron_SubCron';		

$cron->save();