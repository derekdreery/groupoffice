<?php
/************************************************************************/
/* TTS: Ticket tracking system                                          */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2002 by Meir Michanie                                  */
/* http://www.riunx.com                                                 */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
include("configure.php");
include("actions.php");
include("classes/security.php");
include("classes/configure.php");
include("classes/opentts.php");
include("classes/statistics.php");
include("classes/common.php");
include("classes/agents.php");
include("classes/ticket.php");
include("classes/task.php");
include("classes/theme.php");
include("classes/search.php");
include("classes/sql.php");
include("classes/modules.php");
include("themes/Aqua/header.php");
if (!class_exists('Email')) include("classes/email.php");
?>
