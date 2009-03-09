<?php 
require($GO_MODULES->modules['summary']['class_path'].'summary.class.inc.php');
$summary = new summary();

$announcement['due_time']=Date::date_add(time(),2);
$announcement['title']='Group-Office updated to '.$GO_CONFIG->version;
$announcement['content']='Dear Group-Office user,<br><br>The Group-Office system has been updated. The following features have been added:<br><ol><li>Set a default reminder and background color for appointments and tasks</li><li>When a new OpenOffice document is created from a template in the addressbook it is&nbsp; saved at the contact and the GOTA is launched.</li><li>Calendar scale with 15 minute interval</li><li>Compose multiple e-mails at once</li><li>Autosave e-mails. When e-mail is saved to drafts it will replace the existing draft and is removed when sent. Every 2 minutes the mail will automatically be saved to drafts.</li><li>Forum module. It\'s not possible to integrate the PhpBB3 forum software with Group-Office.</li></ol>This message will automatically dissapppear 2 days after the upgrade.<br>';
$announcement['user_id']=$GO_SECURITY->user_id;
$summary->add_announcement($announcement);