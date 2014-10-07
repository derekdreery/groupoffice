<?php
$cron = new GO_Base_Cron_CronJob();
		
$cron->name = 'Calendar publisher';
$cron->active = true;
$cron->runonce = false;
$cron->minutes = '0';
$cron->hours = '*';
$cron->monthdays = '*';
$cron->months = '*';
$cron->weekdays = '*';
$cron->job = 'GO_Calendar_Cron_CalendarPublisher';		

$cron->save();