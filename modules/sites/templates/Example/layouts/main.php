<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="robots" content="all,index,follow" />
		<meta name="description" content="<?php echo $this->description; ?>" />
		<title><?php echo $this->getPageTitle() . " - " . GOS::site()->getName(); ?></title>
		<link href="<?php echo $this->getTemplateUrl(); ?>css/stylesheet.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/buttons.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/tabs.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/webshop.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo $this->getTemplateUrl(); ?>css/notifications.css" rel="stylesheet" type="text/css" />

	</head>

	<body>
		<div class="main-container">

			<?php if($this->getAction()!='newtrial') : ?>
				<div id="login">
					<?php if(!GO::user()) : ?>
						<a href="<?php echo $this->createUrl("/sites/site/login"); ?>"><?php echo GOS::t('login'); ?></a> | <a href="<?php echo $this->createUrl("sites/site/register"); ?>"><?php echo GOS::t('register'); ?></a>
					<?php else: ?>
						Welcome <?php echo GO::user()->name; ?> | <a href="<?php echo $this->createUrl('/sites/site/profile'); ?>"><?php echo GOS::t('youraccount'); ?></a> | <a href="<?php echo $this->createUrl('/sites/site/logout'); ?>"><?php echo GOS::t('logout'); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			
			<div class="header">
				<!--		<div class="language">EN | NL</div> -->
				<div class="topmenu-container">
					<div id="topmenu-item-center_43" class="topmenu-item-center topmenu-item-center_0">						
				
						<?php if(GO::modules()->isInstalled("webshop")): ?>
						<div class="topmenu-item-left <?php if(GOS::site()->route=='webshop/site/products')echo 'selected'; ?>">
							<div class="topmenu-item-right">
								<a class="topmenu-item-center" href="<?php echo $this->createUrl('/webshop/site/products'); ?>">Products</a>
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
						<?php endif; ?>
						<?php if(GO::modules()->isInstalled("tickets")): ?>
							<div class="topmenu-item-left <?php if(GOS::site()->route=='tickets/site/ticketlist' || GOS::site()->route=='tickets/site/ticket') echo 'selected'; ?>">
								<div class="topmenu-item-right">
									<a class="topmenu-item-center" href="<?php echo GO::user() ? $this->createUrl('tickets/site/ticketlist'): $this->createUrl('tickets/site/ticketLogin'); ?>">Support</a>
								</div>
							</div>
						<?php endif; ?>
						
						<?php if(GO::modules()->isInstalled("addressbook")): ?>
							<div class="topmenu-item-left <?php if(GOS::site()->route=='addressbook/site/contact' || GOS::site()->route=='tickets/site/ticket') echo 'selected'; ?>">
								<div class="topmenu-item-right">
									<a class="topmenu-item-center" href="<?php echo $this->createUrl('addressbook/site/contact'); ?>">Contact</a>
								</div>
							</div>
						<?php endif; ?>
						
						<?php
						$stmt = GOS::site()->getSite()->content();
						foreach($stmt as $contentModel):						
						?>						
							<div class="topmenu-item-left <?php if(isset($_REQUEST['slug']) && $_REQUEST['slug']==$contentModel->slug) echo 'selected'; ?>">
								<div class="topmenu-item-right">
									<a class="topmenu-item-center" href="<?php echo $this->createUrl('sites/site/content', array('slug'=>$contentModel->slug)); ?>"><?php echo $contentModel->title; ?></a>
								</div>
							</div>						
						<?php endforeach; ?>				
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