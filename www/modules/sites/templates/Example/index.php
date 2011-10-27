<gocms>
	<blocks>
		<block name="blok1" type="html" />
		<block name="blok2" />
	</blocks>
</gocms>

<div>
	
	<div name="header" gocms="type:video">
		echo $page->getItem('introfilm');
	</div>
	
	<div name="sidebar" gocms="type:editable">
		echo $page->getItem('blok1');
		
		
		echo $page->getItem('cart');
		
		
		echo $page->getBlock('sidebar');
	</div>
	
	<?php
	echo $page->getBlock('blok1');
	
	
	?>
	
</div>
	




echo 
