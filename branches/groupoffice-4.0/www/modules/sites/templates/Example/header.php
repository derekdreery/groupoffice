<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta name="verify-v1" content="sDWH/1D6qBQ831OBnlpa7yRoemRF68f4UGUDyfIaCDo=" />
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="robots" content="all,index,follow" />
		<meta name="keywords" content="welcome, intermesh, software, customers, webshop, purchase, register, download, updates, group-office, upgrade" />
		<meta name="description" content="Welcome to the Intermesh Software Shop
					Here you can purchase all Intermesh Software. To make purchases you need to register once. In the shop you can:

					Download your purchased products immediately
					Download product updates at any..." />
		<title><?php echo $this->getPage()->title; ?> - <?php echo $this->getSite()->name; ?></title>
		<link href="<?php echo $this->getRootTemplateUrl(); ?>css/stylesheet.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getRootTemplateUrl(); ?>css/buttons.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getRootTemplateUrl(); ?>css/tabs.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getRootTemplateUrl(); ?>css/webshop.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getRootTemplateUrl(); ?>css/notifications.css" rel="stylesheet" type="text/css" />
		<?php	echo $this->getPage()->renderHeaderIncludes(); ?>

	</head>

	<body>
		<div class="main-container">

			<div id="login">
				<?php if(!GO::user()) : ?>
					<a href="<?php echo $this->pageUrl("login"); ?>"><?php echo $this->t('login'); ?></a> | <a href="<?php echo $this->pageUrl("register"); ?>"><?php echo $this->t('register'); ?></a>
				<?php else: ?>
					Welcome <?php echo GO::user()->name; ?> | <a href="<?php echo $this->pageUrl('profile'); ?>"><?php echo $this->t('youraccount'); ?></a> | <a href="<?php echo $this->pageUrl('logout'); ?>"><?php echo $this->t('logout'); ?></a>
				<?php endif; ?>
			</div>

			<div class="header">
				<!--		<div class="language">EN | NL</div> -->
				<div class="topmenu-container">
					<div id="topmenu-item-center_43" class="topmenu-item-center topmenu-item-center_0">						
					<?php
					$stmt = $this->getSite()->pages();
					while ($page = $stmt->fetch()) {
						?>					
						<div class="topmenu-item-left <?php if($page->id==$this->getPage()->id)echo 'selected'; ?> ">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a>
							</div>
						</div>
						<?
					}
					?>				
					</div>
				</div>
			</div>
			<div class="hoofd-kader">		
				<div class="hoofd-kader-menu">

					<div class="hoofd-tab-left">
						<div class="hoofd-tab-right">
							<a class="hoofd-tab-center" href="#">
								<?php echo $this->getPage()->name; ?>
							</a>
						</div>
					</div>

				</div>		
				<div class="hoofd-kader-top"></div>

				<div class="hoofd-kader-center">