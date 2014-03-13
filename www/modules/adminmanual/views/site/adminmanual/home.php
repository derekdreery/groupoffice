<?php
use GO\Site\Widget\TOC;
?>



<div class="row">

	
	<div class="jumbotron">
  <h1>Welcome!</h1>
	<?php echo $content->getHtml(); ?>
	</div>
	
	<blockquote style="margin:10px auto; display:block; width:300px;">
  <h4>Under construction!</h4>
  
  <p>This manual is incomplete and we're working on it every day.</p>
	</blockquote>
	
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