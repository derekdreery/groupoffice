<?php
$sm_config['install_path']='/var/www/groupoffice/';
$sm_config['domain']='{domain}';
$sm_config['apache_conf']='/etc/apache2/sites-enabled/';
$sm_config['apache_user']='www-data';
$sm_config['mysql_host']='localhost';
$sm_config['mysql_user']='{db_user}';
$sm_config['mysql_pass']='{db_pass}';
$sm_config['source']='/usr/share/groupoffice-servermanager/groupoffice';
$sm_config['protocol']='http://';

$sm_config['sender_email']='noreply@{domain}';
$sm_config['sender_name']='Group-Office trial';
$sm_config['install_modules']='summary,email,calendar,tasks,addressbook,notes,billing,files,modules,users,groups,tools,customfields,sync,serverclient,projects,mailings,gota,links';

$default_config['quota']='512';
$default_config['max_users']='3';
$default_config['allow_themes']=true;
$default_config['allow_password_change']=true;
#create a mailbox automatically username@intermeshdev.nl
$default_config['serverclient_domains']='';
#restrict outgoing e-mails
$default_config['restrict_smtp_hosts']='127.0.0.1:10';


$static_config['debug']=false;
$static_config['log']=true;
$static_config['db_type']='mysql';
$static_config['db_host']='localhost';
$static_config['max_file_size']='10000000';
$static_config['smtp_server']='localhost';
$static_config['smtp_port']='25';
$static_config['smtp_username']='';
$static_config['smtp_password']='';
$static_config['email_connectstring_options']='';
$static_config['max_attachment_size']='10000000';
$static_config['cmd_zip']='/usr/bin/zip';
$static_config['cmd_unzip']='/usr/bin/unzip';
$static_config['cmd_tar']='/bin/tar';
$static_config['cmd_chpasswd']='/usr/sbin/chpasswd';
$static_config['cmd_sudo']='/usr/bin/sudo';
$static_config['cmd_quota']='';
$static_config['cmd_alias']='/usr/local/bin/alias.sh';
$static_config['cmd_edquota']='';
$static_config['cmd_wbxml2xml']='/usr/bin/wbxml2xml';
$static_config['cmd_xml2wbxml']='/usr/bin/xml2wbxml';
$static_config['cmd_tnef']='/usr/bin/tnef';
$static_config['quota_protouser']='';
$static_config['registration_fields']='';
$static_config['required_registration_fields']='';
$static_config['register_modules_read']='addressbook,summary,email,notes,projects,billing,tasks,files,calendar,sync,mailings,gota';
$static_config['register_modules_write']='';
$static_config['register_user_groups']='Internal';
$static_config['register_visible_user_groups']='Everyone,Internal';
$static_config['phpMyAdminUrl']='';