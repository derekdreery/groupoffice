<?php \Site::scripts()->registerCssFile(\Site::file('css/ticket.css')); ?>

<section id="support" class="page">

	<div class="external-ticket-page ticketlist">
		<div class="wrapper">

			<header><h2><?php echo \GO::t('ticketList','defaultsite'); ?></h2></header>

			<article>
			
				<a class="black-button rounded-corners create-ticket-button" href="<?php echo \Site::urlManager()->createUrl('tickets/externalpage/newticket'); ?>"><?php echo \GO::t('ticketNewTicket','defaultsite'); ?></a>

				<?php $pager = new \GO\Site\Widget\Pager(array(
					'previousPageClass'=>'pagination-arrow-right',
					'nextPageClass'=>'pagination-arrow-left',
					'store'=>$ticketstore
					)); 
				?>

				<table class="table-ticketlist">

					<caption>
						<span class="caption-header">Your Tickets</span> 
						<span id="ticket-filter">	
							Filter:
							<a class="<?php if($filter=='all') echo 'active'; ?>" href="<?php echo \Site::urlManager()->createUrl('groupofficecom/ticket/index',array('filter'=>'all')); ?>"><?php echo GO::t('ticketFilterAll','defaultsite'); ?></a>
							-
							<a class="<?php if($filter=='openprogress') echo 'active'; ?>" href="<?php echo \Site::urlManager()->createUrl('groupofficecom/ticket/index',array('filter'=>'openprogress')); ?>"><?php echo GO::t('ticketFilterOpenInProgress','defaultsite'); ?></a>
							-
							<a class="<?php if($filter=='open') echo 'active'; ?>" href="<?php echo \Site::urlManager()->createUrl('groupofficecom/ticket/index',array('filter'=>'open')); ?>"><?php echo GO::t('ticketFilterOpen','defaultsite'); ?></a>
							-
							<a class="<?php if($filter=='progress') echo 'active'; ?>" href="<?php echo \Site::urlManager()->createUrl('groupofficecom/ticket/index',array('filter'=>'progress')); ?>"><?php echo GO::t('ticketFilterInProgress','defaultsite'); ?></a>
							-
							<a class="<?php if($filter=='closed') echo 'active'; ?>" href="<?php echo \Site::urlManager()->createUrl('groupofficecom/ticket/index',array('filter'=>'closed')); ?>"><?php echo GO::t('ticketFilterClose','defaultsite'); ?></a>

						</span>
					</caption>

					<tr>
						<th><?php echo \GO::t('ticketNumber','defaultsite'); ?></th>
						<th><?php echo \GO::t('ticketSubject','defaultsite'); ?></th>
						<th><?php echo \GO::t('ticketStatus','defaultsite'); ?></th>
						<th><?php echo \GO::t('ticketAgent','defaultsite'); ?></th>
						<th><?php echo \GO::t('ticketCreated','defaultsite'); ?></th>
					</tr>
					<tfoot>
						<th colspan="5"><?php echo $pager->render(); ?></th>
					</tfoot>

					<?php if(!$pager->getItems()): ?>
						<tr><td colspan="5"><?php echo \GO::t('ticketNoneFound','defaultsite'); ?></td></tr>
					<?php else: ?>
						<?php foreach($pager->getItems() as $i => $ticket): ?>
							<tr class="<?php echo($i%2)?'even':'odd'; ?>">
								<td><?php echo '<a href="'.\Site::urlManager()->createUrl("tickets/externalpage/ticket",array("ticket_number"=>$ticket->ticket_number,"ticket_verifier"=>$ticket->ticket_verifier)).'">'.$ticket->ticket_number.'</a>'; ?></td>
								<td><?php echo $ticket->subject; ?></td>
								<td><?php echo $ticket->getStatusName(); ?></td>
								<td><?php echo $ticket->agent?$ticket->agent->name:""; ?></td>
								<td><?php echo $ticket->getAttribute("ctime","formatted"); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</table>

			</article>
			
		</div>
	</div>

</section>