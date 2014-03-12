<?php
namespace GO\Site\Tag;

use Site;

class Thumb implements TagInterface{	
	static function render($params, $tag){
		
		$html = '';
		
		if(empty($params['path'])){
			return "Error: path attribute must be set in img tag!";
		}
		
		//Change Tickets.png into public/site/1/files/Tickets.png
		
		$folder = new \GO\Base\Fs\Folder(Site::model()->getPublicPath());
		
		$fullRelPath = $folder->stripFileStoragePath().'/files/'.$params['path'];
//		var_dump($p);
		
		$thumbParams = $params;
		unset($thumbParams['path'], 
						$thumbParams['alt'], 
						$thumbParams['class'],
						$thumbParams['style'], 
						$thumbParams['astyle'], 
						$thumbParams['caption'],
						$thumbParams['aclass']);
		
		$thumb = Site::thumb($fullRelPath, $thumbParams);
		
		if(!isset($params['alt'])){
			$params['alt']=isset($params['caption']) ? $params['caption'] :  basename($tag['params']['path']);
		}
		
		$html .= '<img src="' . $thumb . '" alt="' . $params['alt'] . '"';
		
		if(!isset($params['class'])){
			$params['class']='thumb-img';
		}
		
		$html .= 'class="'.$params['class'].'"';
		
		if(isset($params['style'])){
			$html .= 'style="'.$params['style'].'"';
		}
	
		
		$html .= ' />';
		
		
		if(isset($params['lightbox'])){
			$a = '<a';
			
			if(isset($params['caption'])){
				$html .= ' title="'.$params['caption'].'"';
			}

			if(!isset($params['aclass'])){
				$params['aclass']='thumb-a';
			}
			
			$a .= ' class="'.$params['aclass'].'"';

			if(isset($params['style'])){
				$a .= ' style="'.$params['astyle'].'"';
			}
			
			$a .= ' data-lightbox="'.$params['lightbox'].'" href="'.\Site::file($params['path'], false).'">'.$html.'</a>'; // Create an url to the original image
			
			$html= $a;
		}
		
		if(isset($params['caption'])){
			$html .= '<div class="thumb-caption">'.$params['caption'].'</div>';
		}
		
		
		$html = '<div class="thumb-wrap">'.$html.'</div>';
		
		return $html;
	}
}