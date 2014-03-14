<?php
use GO\Site\Widget\TOC;
use GO\Site\Widget\Breadcrumb;

$breadcrump = new Breadcrumb(array('content'=>$content));
echo $breadcrump->render();
?>



<div class="row">
  <div class="col-md-3">
			
			<div data-spy="affix" data-offset-top="100" class="panel panel-default toc">
				<div class="panel-heading">Table of contents</div>
				<div class="panel-body">
				<?php

				$toc = new TOC(array('content'=>$content));
				
				echo $toc->render();

				?>
				<div class="nav-bottom">
					<!--<div class="top-link"><a title="Jump to the top of the page" href="#header"><span class="glyphicon glyphicon-chevron-up"></span> Jump to top</a></div>-->				
					<div class="home-link"><a title="Go to home page" href="<?php echo Site::urlManager()->getHomeUrl(); ?>"><span class="glyphicon glyphicon-chevron-left"></span> Home</a></div>
				</div>
				</div>
			</div>
	
	</div>

	<div class="col-md-9">

		<?php
		
		function printContentRecursive($content, $level=1){
		
			echo '<h'.$level.' id="'.$content->baseslug.'">' . $content->title . '</h'.$level.'>';

			echo '<div>' . $content->getHtml() . '</div>';			
			
//			echo '<div class="top-link"><a title="Jump to the top of the page" href="#top"><span class="glyphicon glyphicon-chevron-up"></span></a></div>';

			foreach ($content->children() as $child) {

				printContentRecursive($child, $level+1);
			}
		}
		
		printContentRecursive($content);
		?>
	</div>
</div>


<div id="top-link" data-spy="affix" data-offset-top="400">
	<a title="Jump to the top of the page" href="#header">
		<span class="glyphicon glyphicon-chevron-up"></span>
	</a>
</div>

<?php
Site::scripts()->registerScript('scrollspy', "$('body').scrollspy({ target: '.toc' });$('body').scrollspy({ target: '#top-link' })");