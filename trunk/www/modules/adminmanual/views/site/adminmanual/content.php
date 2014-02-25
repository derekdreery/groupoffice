<?php
use GO\Site\Widget\TOC;
?>

<ol class="breadcrumb">
	<?php
	$count=0;
	$crumbs = array($content);
	$slugContent=$content;
	while($parent = $slugContent->parent){
		$crumbs[]=$parent;
		
		$slugContent=$parent;
	}
	
	$crumbs=array_reverse($crumbs);
	
	foreach($crumbs as $crumb){
		if($crumb->id==$content->id){
			echo '<li class="active">'.$crumb->title.'</li>';
		}  else {
			echo '<li><a href="'.$crumb->getUrl().'">'.$crumb->title.'</a></li>';
		}
	}

	?>
</ol>

<div class="row">
  <div class="col-md-3">
	
			
			<div data-spy="affix" data-offset-top="100" class="panel panel-default toc" style="width:240px;">
				<div class="panel-heading">Table of contents</div>
				<div class="panel-body">
			<?php

				$toc = new TOC(array('content'=>$content));
				
				echo $toc->render();

				?>
				</div>
			</div>
	
	</div>

	<div class="col-md-9">

		<?php
		
		function printContentRecursive($content, $level=1){
		
			echo '<h'.$level.' id="'.$content->baseslug.'">' . $content->title . '</h'.$level.'>';

			echo '<div>' . $content->getHtml() . '</div>';

			foreach ($content->children() as $child) {

				printContentRecursive($child, $level+1);
			}
		}
		
		printContentRecursive($content);
		?>
	</div>
</div>

<?php
Site::scripts()->registerScript('scrollspy', "$('body').scrollspy({ target: '.toc' })");