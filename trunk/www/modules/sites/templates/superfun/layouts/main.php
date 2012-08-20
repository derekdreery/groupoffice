<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="robots" content="all,index,follow" />
		<meta name="keywords" content="key, words" />
		<meta name="description" content="my description" />
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
		<link href="<?php echo $this->getTemplateUrl(); ?>css/jquery-ui.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/style.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/bootstrap.css" rel="stylesheet" type="text/css" />
		
		<title><?php echo $this->getPageTitle() ." - ". GOS::site()->name; ?></title>
	</head>

	<body>
		<div class="wrapper">

			<div id="login">
				<?php if(!GO::user()) : ?>
					<a class="btn btn-inverse" href="<?php echo $this->createUrl("/sites/site/login"); ?>">Inloggen</a> 
					| <a class="btn btn-inverse" href="<?php echo $this->createUrl("/reservation/front/register"); ?>">Registreren</a>
				<?php else: ?>
					<span class="hello">Welkom <?php echo GO::user()->name; ?> </span>
					| <a class="btn btn-inverse" href="<?php echo $this->createUrl('/sites/site/logout'); ?>">Uitloggen</a>
					| <a class="btn btn-inverse" href="<?php echo $this->createUrl('/reservation/front/account'); ?>">Mijn account</a>
				<?php endif; ?>
					| <a class="btn btn-inverse" href="<?php echo $this->createUrl('/reservation/front/reservate'); ?>">Reserveren</a>
			</div>

			<div class="main">
				
				<?php echo $content; ?>
				
			</div>
			<div id="rightbar">
				
			<div class="cart_panel">
				<h1>Mijn Reservering</h1>
				

				<!-- begin shopping cart widget -->
				<ul id="sidecart">
				<?php if(GOS::site()->notifier->hasMessage('carterror')): ?>
						<li class="errorMessage">
							<h3>Fout</h3>
							<?php foreach(GOS::site()->notifier->getMessage('carterror') as $att => $error): ?>
								<?php echo $error; ?><br />
							<?php endforeach; ?>
						</li>
				<?php endif; ?>
				<?php $cart = new GO_Webshop_Components_ShoppingCart();
				foreach($cart->getItems() as $item): ?>
					<li <?php echo ($item->getItem()->hasValidationErrors()) ? 'class="errorMessage"' : ''; ?>><h3><?php echo $item->getItem()->activity->planboard->name; ?></h3>
						<div class="price">
							<b>Resource:</b> <?php echo $item->getItem()->resource->name; ?><br>
							<b>Prijs:</b> <?php echo $item->getSumPriceText(); ?>
						</div>
						met <?php echo $item->getItem()->getPersonCount(); ?> personen<br>
						van <?php echo $item->getItem()->time_from; ?> tot <?php echo $item->getItem()->getTimeTillText(); ?><br>
						
						<a class="btn btn-mini btn-danger" href="<?php echo $this->createUrl('/reservation/front/removeCartItem', array('id'=>$item->getId())); ?>">Wissen</a>
					</li>
				<?php endforeach; ?>
					
				</ul>
				<div id="carttotal">
				<ul>
					<li>Totaal kosten: <?php echo $cart->getTotalText(); ?></li>
				</ul>
				
				<?php if(!$cart->isEmpty()): ?>
				<a class="btn btn-success" href="<?php echo $this->createurl('/reservation/front/overview'); ?>">Reserveren</a>
				<?php endif; ?>
				</div>
				<!-- end shopingcart widget -->
			</div>
			</div>
			
			<div class="footer">		
				<Br />
					<a href="http://www.superfun.nl"> Bezoek superfun.nl</a>
					<div class="copyright">Copyright Superfun <?php echo date('Y'); ?><br /><br />
					 Middelhoefseweg 10, 3819 AA  AMERSFOORT | tel. 033-4951831<br />BTW Nr. NL.8512.97.420B01 | KVK nr. 54418607
					</div>
			</div>
	  </div>
</body>
</html>