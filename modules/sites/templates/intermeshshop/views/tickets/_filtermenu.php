<div class="filter-list">
	
<ul>
	<li><?php echo GOS::t('tickets_filter'); ?></li>:
	<li><a href="<?php echo GOS::site()->urlManager->createUrl('tickets/site/ticketlist',array('filter'=>'all')); ?>"><?php echo GOS::t('tickets_filter_all'); ?></a></li>
	-
	<li><a href="<?php echo GOS::site()->urlManager->createUrl('tickets/site/ticketlist',array('filter'=>'openprogress')); ?>"><?php echo GOS::t('tickets_filter_openprogress'); ?></a></li>
	-
	<li><a href="<?php echo GOS::site()->urlManager->createUrl('tickets/site/ticketlist',array('filter'=>'open')); ?>"><?php echo GOS::t('tickets_filter_open'); ?></a></li>
	-
	<li><a href="<?php echo GOS::site()->urlManager->createUrl('tickets/site/ticketlist',array('filter'=>'progress')); ?>"><?php echo GOS::t('tickets_filter_progress'); ?></a></li>
	-
	<li><a href="<?php echo GOS::site()->urlManager->createUrl('tickets/site/ticketlist',array('filter'=>'closed')); ?>"><?php echo GOS::t('tickets_filter_closed'); ?></a></li>
</ul>

</div>
<div style="clear:both;"></div>
