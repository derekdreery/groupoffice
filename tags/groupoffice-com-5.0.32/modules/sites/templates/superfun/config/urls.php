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
 * @package GO.sites
 * @copyright Copyright Intermesh
 * @version $Id config.php 2012-06-07 12:37:50 mdhart $ 
 * @author Michael de Hart <mdehart@intermesh.nl> 
 */
return array(
		''=>'reservation/front/reservate',
		//'reserveren'=>'reservation/front/reservation',
		'register'=>'reservation/front/register',
		'payment/*'=>'reservation/front/payment',
		'paymentreturn/*'=>'reservation/front/paymentreturn',
		'account'=>'reservation/front/account',
		'reservering/<id:\d+>'=>'reservation/front/reservation',
		'<action:(login|logout|account|recoverPassword|resetPassword)>' => 'sites/site/<action>',
		//'<module:\w+>/<controller:\w+>/<action:\w+>/'=>*'<module>/<controller>/<action>'
			
);

?>
