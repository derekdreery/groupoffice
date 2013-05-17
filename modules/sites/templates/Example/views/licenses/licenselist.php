<?php
$this->setPageTitle("Download");
?>
<div class="subkader-big-top">
	<div class="subkader-big-bottom">
		<div class="subkader-big-center">						
			<h1>Licenses</h1>
			<p>
				Click on download package to download the latest version of the software. Download your license file and put it in the root folder of the installation directory.
				<br />
				For security reasons the Ioncube loader needs to be installed. You can obtain the loader here:
				<br />
				<a href="http://www.ioncube.com/loaders.php" target="_blank">http://www.ioncube.com/loaders.php</a>
			</p>
			<br />
			<p>The license files that you can download here must be placed in the root folder of the software package.</p>
		</div>
	</div>
</div>

<?php if($pager->models): ?>
	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">								

				<table class="ticket-models-table" style="border-collapse: collapse;" width="100%">
					<tr>
						<th align="left" width="250">Name</th><th align="left" width="120">Upgrades until</th><th align="left" width="180">Details</th><th align="left"></th>
					</tr>
					<?php $i = 0; ?>
					<?php foreach($pager->models as $license): ?>
						<?php
							if($i%2!=0)
								$style = 'greytable-odd';
							else
								$style = 'greytable-even';
							$i++;
						?>

						<tr  class="model-row <?php echo $style; ?>" style="border-collapse: collapse;">
							<td><?php echo $license->name; ?></td>
							<td colspan="1"><?php echo empty($license->upgrades)?"Allways":$license->getAttribute('upgrades','raw')>time()?$license->getAttribute("upgrades","formatted"):'Expired'; ?></td>
							<td colspan="2">
								<?php  if($license->new):?>										
									<a href="<?php echo $this->createUrl('/licenses/site/setLicense',array('license_id'=>$license->id)); ?>"><b style="color:red; text-decoration:underline;">Set license details first</b></a>
								<?php else: ?>
									<a href="<?php echo $this->createUrl('/licenses/site/viewLicense',array('license_id'=>$license->id)); ?>">View details</a>
								<?php endif; ?>
							</td>
						</tr>

						<?php $packages = $license->packages; ?>

						<?php while($package=$packages->fetch()): ?>
							<tr  class="model-row <?php echo $style; ?>" style="border-collapse: collapse;">
								<td colspan="3">
									&nbsp;&nbsp;&nbsp;<?php echo $package->package_name; ?>
								</td>
								<td>
									<?php if(!$license->new): ?>
										<a target="_blank" href="<?php echo GO::url('licenses/license/downloadLicenseFile',array('package_id'=>$package->id,'license_id'=>$license->id),true,true); ?>">Download license</a> |
									<?php endif; ?>
										<a target="_blank" href="<?php echo GO::url("licenses/package/downloadPackageFile",array('package_id'=>$package->id),true,true); ?>">Download package</a>
								</td>
							</tr>
						<?php endwhile; ?>
					<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>

	<div class="subkader-big-top">
		<div class="subkader-big-bottom">
			<div class="subkader-big-center">				
				<?php $pager->render(); ?>
			</div>
		</div>
	</div>

	<?php else: ?>
		<div class="subkader-big-top">
			<div class="subkader-big-bottom">
				<div class="subkader-big-center">			
					<p>You don't have any licenses yet. Your purchased licenses will be available for download here when you purchase a software product.</p>
				</div>
			</div>
		</div>
	<?php endif; ?>	
		