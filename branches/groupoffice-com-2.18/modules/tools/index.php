<?php
/*
   Copyright Intermesh 2007
   Author: Merijn Schering <mschering@intermesh.nl>
   Version: 1.0 Release date: 07 March 2007

   This program is free software; you can redistribute it and/or modify it
   under the terms of the GNU General Public License as published by the
   Free Software Foundation; either version 2 of the License, or (at your
   option) any later version.
 */

require_once("../../Group-Office.php");
$GO_SECURITY->authenticate();
$GO_MODULES->authenticate('tools');
require_once($GO_LANGUAGE->get_language_file('tools'));

load_basic_controls();




$form = new form('tools_form');

$form->add_html_element(new html_element('h1', $lang_modules['tools']));

$form->add_html_element(new html_element('p', 'AVISO! Use estes utilit&aacute;rios por sua conta e risco. N&atilde;o se assumem responsabilidades por QUAISQUER danos. Leia, por favor, a licen&ccedil;a GPL inclu&iacute;da com o software.')); /* WARNING! Use these tools at own risk. Intermesh is not liable to you for ANY damages. Please read the GPL license included with the software. */

$table = new table();

$row = new table_row();
$row->add_cell(new table_cell('C&oacute;pia da base de dados')); /* Backup database -> in another place to translate? */
$button = new button($cmdRun, "javascript:document.location='backupdb.php';");
$button->set_attribute('style','margin:0px;margin-top:0px;');
$row->add_cell(new table_cell($button->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell('Verificar base de dados')); /* Check database */
$button = new button($cmdRun, "javascript:document.location='dbcheck.php';");
$button->set_attribute('style','margin:0px;margin-top:0px;');
$row->add_cell(new table_cell($button->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell('Importar utilizadores')); /* Import users */
$button = new button($cmdRun, "javascript:document.location='importusers.php';");
$button->set_attribute('style','margin:0px;margin-top:0px;');
$row->add_cell(new table_cell($button->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell('Remover compromissos e contactos duplicados')); /* Remove duplicate events and contacts */
$button = new button($cmdRun, "javascript:document.location='rm_duplicates.php';");
$button->set_attribute('style','margin:0px;margin-top:0px;');
$row->add_cell(new table_cell($button->get_html()));
$table->add_row($row);

$row = new table_row();
$row->add_cell(new table_cell('Executar query na base de dados')); /* Execute database query */
$button = new button($cmdRun, "javascript:document.location='query.php';");
$button->set_attribute('style','margin:0px;margin-top:0px;');
$row->add_cell(new table_cell($button->get_html()));
$table->add_row($row);
/*
$row = new table_row();
$row->add_cell(new table_cell('Show db scheme'));
$button = new button($cmdRun, "javascript:document.location='create_db_scheme.php';");
$row->add_cell(new table_cell($button->get_html()));
$table->add_row($row);
*/
$form->add_html_element($table);

require_once($GO_THEME->theme_path."header.inc");
echo $form->get_html();
require_once($GO_THEME->theme_path."footer.inc");