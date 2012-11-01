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
		'invoices'=> 'billing/site/invoices', //TODO
		'orderfromtrail'=>'billing/site/orderfromtrail', //TODO
		
		'support' => 'tickets/site/ticketlist', //TODO
		'createticket' => 'tickets/site/createticket', //TODO
		'ticket'=>'tickets/site/ticket', //TODO
		
		'products'=>'billing/site/products', //TODO
		'cart'=>'webshop/site/cart', //TODO
		'checkout' => 'webshop/site/checkout', //TODO
		'payment'=>'webshop/site/payment', //TODO
		'summery'=>'webshop/site/summery', //TODO
		'paymentreturn' => 'webshop/site/paymentreturn', //TODO
		
		'setlicense'=>'licenses/site/setlicense', //TODO
		'download' => 'licenses/site/licenseList', //TODO : listenselist
		'viewlicense' => 'licenses/site/viewlicense', //TODO
		'<action:(login|logout|register|profile|recover|resetpassword)>' => 'sites/user/<action>',//TODO: login, logout, profile resetpassword, register, recover/lostpassword
		'<slug>'=>'sites/site/content', //TODO: requirements, contact	
		
		'<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>'
			
);

?>
