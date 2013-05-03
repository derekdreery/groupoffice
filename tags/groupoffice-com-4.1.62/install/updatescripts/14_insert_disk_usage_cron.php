<?php
$cron = new GO_Base_Cron_CronJob();
		
$cron->name = 'Calculate disk usage';
$cron->active = true;
$cron->runonce = false;
$cron->minutes = '0';
$cron->hours = '0';
$cron->monthdays = '*';
$cron->months = '*';
$cron->weekdays = '*';
$cron->job = 'GO_Base_Cron_CalculateDiskUsage';		

$cron->save();