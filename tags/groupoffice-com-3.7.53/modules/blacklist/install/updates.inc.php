<?php

$updates[]= "ALTER TABLE `bl_ips` ADD `userid` INT( 11 ) NOT NULL";

$updates[] = "ALTER TABLE `bl_ips`
  DROP PRIMARY KEY,
   ADD PRIMARY KEY(
     `ip`,
     `userid`);";