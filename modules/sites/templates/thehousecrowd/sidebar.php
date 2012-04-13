<h1>Shopping cart</h1>

<?php 
$webshop = GO_Webshop_Model_Webshop::getByController($this);
echo $webshop->getWebshopOrder()->getCart()->getSmallTable(); ?>

<div class="button-green-side" onmouseover="this.className='button-green-side-hover';"  onmouseout="this.className='button-green-side';">
	<div class="button-green-side-right">
		<a href="<?php echo $this->pageUrl($webshop->getCartPath()); ?>" class="button-green-side-center"> 
			<?php echo $this->t('webshop_checkout'); ?>
		</a>
	</div>
</div>