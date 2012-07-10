<div id="login">
				<?php if(!GO::user()) : ?>
					<a href="<?php echo $this->createUrl("sites/default/login"); ?>">Login</a> | <a href="<?php echo $this->createUrl("/sites/default/register"); ?>">Registreer</a>
				<?php else: ?>
					Welcome <?php echo GO::user()->name; ?> | <a href="<?php echo $this->createUrl('reservation/front/reservation'); ?>">Account</a> | <a href="<?php echo $this->createUrl('/sites/default/logout'); ?>">Logout</a>
				<?php endif; ?>
			</div>
