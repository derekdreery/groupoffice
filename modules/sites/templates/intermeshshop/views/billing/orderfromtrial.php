<div class="subkader-small-top">
	<div class="subkader-small-bottom">
		<div class="subkader-small-center">						

			<h1>Thank you for choosing Group-Office!</h1>

			Please provide us with your billing address and the number of users and diskspace you desire.


			<?php
			if (GOS::site()->notifier->hasMessage('success')) {
				echo '<div class="notification notice-ok">' . GOS::site()->notifier->getMessage('success') . '</div>';
			} else if (GOS::site()->notifier->hasMessage('error')) {
				echo '<div class="notification notice-error">' . GOS::site()->notifier->getMessage('error') . '</div>';
			}
			?>

			<div class="form">
				<?php echo GO_Sites_Components_Html::beginForm(); ?>
				
				<?php echo GO_Sites_Components_Html::hiddenField('reference', $reference); ?>

				<div class="row">
					<?php echo GO_Sites_Components_Html::label("Payment period", 'payment_period'); ?>
					<?php
					echo GO_Sites_Components_Html::dropDownList("payment_period", "Year", array(
							"year" => "Year",
							"halfyear" => "Half year",
							"quarter" => "Quarter"
					));
					?>					
				</div>

				<div class="row">
					<?php echo GO_Sites_Components_Html::label("Number of users (exluding the admin user)", 'number_of_users'); ?>
					<?php
					echo GO_Sites_Components_Html::dropDownList("number_of_users", "1", array(
							"1" => "1 user (€ 10 p.m.)",
							"2" => "2 users (€ 19 p.m.)",
							"3" => "3 users (€ 28 p.m.)",
							"4" => "4 users (€ 36 p.m.)",
							"5" => "5 users (€ 43 p.m.)",
							"6" => "6 users (€ 50 p.m.)",
							"7" => "7 users (€ 57 p.m.)",
							"8" => "8 users (€ 63 p.m.)",
							"9" => "9 users (€ 69 p.m.)",
							"10" => "10 users (€ 74 p.m.)",
							"15" => "15 users (€ 95 p.m.)",
							"20" => "20 users (€ 109 p.m.)",
							"25" => "25 users (€ 123 p.m.)",
							"30" => "30 users (€ 134 p.m.)",
							"35" => "35 users (€ 141 p.m.)",
							"40" => "40 users (€ 146 p.m.)",
							"45" => "45 users (€ 148 p.m.)",
							"50" => "50 users (€ 150 p.m.)"
					));
					?>					
				</div>

				<p> You'll need diskspace for files and or e-mail. It costs € 1,- per GB per month.</p>

				<div class="row">
					<?php echo GO_Sites_Components_Html::label("Amount of diskspace (GB)", 'diskspace'); ?>
					<?php echo GO_Sites_Components_Html::textField("diskspace", "1"); ?>					
				</div>


				<div class="row">
					<?php echo GO_Sites_Components_Html::label("Billing module (+€20,- pm.)", 'billing'); ?>
					<?php echo GO_Sites_Components_Html::checkBox("billing"); ?>					
				</div>

				<div class="row">
					<?php echo GO_Sites_Components_Html::label("Documents module (+€15 pm.)", 'documents'); ?>
					<?php echo GO_Sites_Components_Html::checkBox("documents"); ?>					
				</div>
				<div class="row buttons">
					<?php echo GO_Sites_Components_Html::submitButton('Save'); ?>
				</div>
				<div style="clear:both;"></div>
					<?php echo GO_Sites_Components_Html::endForm(); ?>
			</div>
		</div>
	</div>

</div>


<div class="subkader-right">
	<h1>Secure login</h1>
	<p>SSL secured connection verified by Equifax Secure Inc. </p>
</div>
