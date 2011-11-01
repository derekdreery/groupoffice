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
		<title><?php echo $this->page->title; ?> - <?php echo $this->site->name; ?></title>
		<link href="<?php echo $this->templateUrl; ?>css/stylesheet.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->templateUrl; ?>css/buttons.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->templateUrl; ?>css/tabs.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->templateUrl; ?>css/webshop.css" rel="stylesheet" type="text/css" />
	</head>

	<body>


		<div class="main-container">

			<div id="login">
				<a href="<?php echo self::pageUrl("login"); ?>">Login / Register</a>
			</div>

			<div class="header">
				<!--		<div class="language">EN | NL</div> -->
				<div class="topmenu-container">
					<div id="topmenu-item-center_43" class="topmenu-item-center topmenu-item-center_0">						
					<?php
					$stmt = $this->site->pages();
					while ($page = $stmt->fetch()) {
						?>
						<?php if($page->id==$this->page->id)echo '<div class="selected">'; ?> 
						<div class="topmenu-item-left">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $page->url; ?>"><?php echo $page->name; ?></a>
							</div>
						</div>
						<?php if($page->id==$this->page->id)echo '</div>'; ?> 
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
								<?php echo $this->page->name; ?>
							</a>
						</div>
					</div>

				</div>		
				<div class="hoofd-kader-top"></div>

				<div class="hoofd-kader-center">

				