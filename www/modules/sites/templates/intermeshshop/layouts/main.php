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
		<title><?php echo $this->getPageTitle() . " - " . GOS::site()->getName(); ?></title>
		<link href="<?php echo $this->getTemplateUrl(); ?>css/stylesheet.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/buttons.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/tabs.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/webshop.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/notifications.css" rel="stylesheet" type="text/css" />

	</head>

	<body>
		<div class="main-container">

			<div id="login">
				<?php if(!GO::user()) : ?>
					<a href="<?php echo $this->createUrl("/sites/site/login"); ?>"><?php echo GOS::t('login'); ?></a> | <a href="<?php echo $this->createUrl("sites/site/register"); ?>"><?php echo GOS::t('register'); ?></a>
				<?php else: ?>
					Welcome <?php echo GO::user()->name; ?> | <a href="<?php echo $this->createUrl('/sites/site/profile'); ?>"><?php echo GOS::t('youraccount'); ?></a> | <a href="<?php echo $this->createUrl('/sites/site/logout'); ?>"><?php echo GOS::t('logout'); ?></a>
				<?php endif; ?>
			</div>
			
			<div class="header">
				<!--		<div class="language">EN | NL</div> -->
				<div class="topmenu-container">
					<div id="topmenu-item-center_43" class="topmenu-item-center topmenu-item-center_0">						
				
						
						<div class="topmenu-item-left <?php if(GOS::site()->route=='billing/site/products')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('/billing/site/products'); ?>">Products</a>
							</div>
						</div>
						<div class="topmenu-item-left <?php if(GOS::site()->route=='billing/site/invoices')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('/billing/site/invoices'); ?>">Invoices</a>
							</div>
						</div>
						<div class="topmenu-item-left <?php if(GOS::site()->route=='licenses/site/licenseList')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('/licenses/site/licenseList'); ?>">Download</a>
							</div>
						</div>
						
						<div class="topmenu-item-left <?php if(isset($_GET['slug']) && $_GET['slug']=='requirements')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('/sites/site/content', array('slug'=>'requirements')); ?>">Requirements</a>
							</div>
						</div>
						
						<div class="topmenu-item-left <?php if(GOS::site()->route=='tickets/site/ticketlist')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('tickets/site/ticketlist'); ?>">Support</a>
							</div>
						</div>
						
						<div class="topmenu-item-left <?php if(isset($_GET['slug']) && $_GET['slug']=='contact')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('/sites/site/content', array('slug'=>'contact')); ?>">Contact</a>
							</div>
						</div>
				
					</div>
				</div>
			</div>
			<div class="hoofd-kader">
				
				<div class="hoofd-kader-menu">

			<div class="hoofd-tab-left">
				<div class="hoofd-tab-right">
					<a class="hoofd-tab-center" href="#">
						<?php echo $this->getPageTitle(); ?>
					</a>
				</div>
			</div>

			</div>
				<div class="hoofd-kader-top"></div>
				<div class="hoofd-kader-center">
				
					<?php echo $content; ?>
					
					</div>
			<div class="hoofd-kader-bottom"></div>
				
			</div>


			<div class="onder-kader-top"></div>		
			<div class="onder-kader-center">	
				<div class="onder-kader-kolom">
					<h1>Group-Office website</h1>
					<p>Find out more about Group-Office</p>

					<div class="btn-blue" onmouseover="this.className='btn-blue-hover';"  onmouseout="this.className='btn-blue';">
						<div class="btn-blue-right">
							<a class="btn-blue-center" href="http://www.group-office.com"> 
								Visit group-office.com
							</a>

						</div>
					</div>		
				</div>
			</div>
			<div class="onder-kader-bottom"></div>

			<div class="copyright"><i>Group-Office</i> is a product of <a href="http://www.intermesh.nl/en/" target="_blank">Intermesh</a></div>
			<div class="sflogo"><a href="http://sourceforge.net"><img src="https://sflogo.sourceforge.net/sflogo.php?group_id=76359&amp;type=2" width="125" height="37" border="0" alt="SourceForge.net Logo" /></a></div>

			<div style="clear:both;"></div>	
		</div>



</body>
</html>