<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @copyright Copyright Intermesh
 * @version $Id$
 * @author Merijn Schering <mschering@intermesh.nl>
 */
//require_once(\GO::config()->root_path."Group-Office.php");

extract($data);


$this->render('externalHeader');
?>
<p><?php echo \GO::t('delete_success','addressbook'); ?></p>
<?php
$this->render('externalFooter');

