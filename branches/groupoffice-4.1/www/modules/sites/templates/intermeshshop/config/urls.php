<?php
/*
 * Copyright Intermesh BV
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 */

/**
 * Rewrite rules for SEO friendly urls
 *
 * @package GO.sites.templates.intermeshshop.config
 * @copyright Copyright Intermesh
 * @version $Id config.php 2012-06-07 12:37:50 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
return array(
		''=>'webshop/site/products',
		'invoices'=> 'billing/site/invoices',
		'orderfromtrial'=>'billing/site/orderfromtrial', //TODO
		
		'support' => 'tickets/site/ticketlist',
		'createticket' => 'tickets/site/createTicket',
		'ticket'=>'tickets/site/showTicket',
		
		'products'=>'billing/site/products',
		'cart'=>'webshop/site/cart',
		'checkout' => 'webshop/site/checkout',
		'payment'=>'webshop/site/payment',
		'summery'=>'webshop/site/summery',
		'paymentreturn' => 'webshop/site/paymentreturn',
		'paymentnotification' => 'webshop/site/paymentnotification',
		
		'setlicense'=>'licenses/site/setlicense', 
		'download' => 'licenses/site/licenseList',
		'viewlicense' => 'licenses/site/viewlicense',
		'<action:(login|logout|register|profile|resetpassword|recoverpassword)>' => 'sites/site/<action>',//TODO: login, logout, profile resetpassword, register, recover/lostpassword
		'<slug>'=>'sites/site/content', //TODO: requirements, contact	
		
		'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>'
			
);

?>
