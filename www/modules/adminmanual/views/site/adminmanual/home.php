<div class="row">
	
	<div class="jumbotron">
  <h1>Welcome!</h1>
	<?php echo $content->getHtml(); ?>
	</div>	
	
	<?php	
	foreach($content->children as $child){
		echo '<div class="col-md-4">';
		echo '<a href="'.$child->getUrl().'">'.$child->title.'</a>';
		echo '<p>'.$child->shortText.'</p>';
		echo '</div>';
	}	
	?>
</div>