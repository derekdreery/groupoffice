<?php

$cron = new GO_Base_Cron_CronJob();

$cron->name = 'Email Reminders';
$cron->active = true;
$cron->runonce = false;
$cron->minutes = '0,5,10,15,20,25,30,35,40,45,50,55';
$cron->hours = '*';
$cron->monthdays = '*';
$cron->months = '*';
$cron->weekdays = '*';
$cron->job = 'GO_Base_Cron_EmailReminders';

$cron->save();