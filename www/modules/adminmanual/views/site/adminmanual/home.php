<?php
use GO\Site\Widget\TOC;
?>



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

//	$toc = new TOC(array(
//			'content'=>$content,
//			'maxLevels'=>1,
//			'linkTemplate'=>'<a href="{url}" class="">{chapter} {title}</a>'
//					));				
//	echo $toc->render();
	
	
	
	?>



</div>


<div id="footer">
	Copyright Intermesh BV.<br /><a href="https://www.group-office.com">https://www.group-office.com</a>
</div>