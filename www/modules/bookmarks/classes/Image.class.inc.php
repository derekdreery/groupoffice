<?php

// /www/classes Image class lokaal gezet voor bookmark doeleinden
// Image = bmImage
//

class bmImage {

	var $image;
	var $image_type;

	public function __construct($filename=false){
		if($filename)
			$this->load($filename);
	}

	public function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}
	public function save($filename, $image_type=false, $compression=75, $permissions=null) {
		if(!$image_type){
			$image_type=$this->image_type;
		}
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
	}
	public function output($image_type=false) {
		if(!$image_type){
			$image_type=$this->image_type;
		}
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	public function getWidth() {
		return imagesx($this->image);
	}
	public function getHeight() {
		return imagesy($this->image);
	}
	public function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	public function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	public function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}
	public function resize($width,$height) {
		$current_width=$this->getWidth();
		$current_height=$this->getHeight();
   // var_dump(1);
		$new_image = imagecreatetruecolor($width, $height);

  //----------------------------------------------------------------------------
  // PNG & GIF Transparency
  //

		if (($this->image_type==IMAGETYPE_PNG)||($this->image_type==IMAGETYPE_GIF))
		{
			
			$trnprt_indx = imagecolortransparent($this->image);

      // If we have a specific transparent color
      if ($trnprt_indx >= 0) {

        // Get the original image's transparent color's RGB values
        $trnprt_color    = imagecolorsforindex($this->image, $trnprt_indx);

        // Allocate the same color in the new image resource
        $trnprt_indx    = imagecolorallocate($new_image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);

        // Completely fill the background of the new image with allocated color.
        imagefill($new_image, 0, 0, $trnprt_indx);

        // Set the background color for new image to transparent
        imagecolortransparent($new_image, $trnprt_indx);

		}

		elseif ($this->image_type==IMAGETYPE_PNG) {

        // Turn off transparency blending (temporarily)
        imagealphablending($new_image, false);

        // Create a new transparent color for image
        $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);

        // Completely fill the background of the new image with allocated color.
        imagefill($new_image, 0, 0, $color);

        // Restore transparency blending
        imagesavealpha($new_image, true);
      }



		

		}


		imagecopyresampled($new_image, $this->image, 0, 0, 0,0, $width, $height, $current_width, $current_height);
		$this->image = $new_image;
	}

//------------------------------------------------------------------------------
//
//
//


	public function zoomcrop($thumbnail_width,$thumbnail_height) { //$imgSrc is a FILE - Returns an image resource.

		$width_orig=$this->getWidth();
		$height_orig=$this->getHeight();

		//getting the image dimensions
		$ratio_orig = $width_orig/$height_orig;

		if ($thumbnail_width/$thumbnail_height > $ratio_orig) {
			$new_height = $thumbnail_width/$ratio_orig;
			$new_width = $thumbnail_width;
		} else {
			$new_width = $thumbnail_height*$ratio_orig;
			$new_height = $thumbnail_height;
		}

		$x_mid = $new_width/2;  //horizontal middle
		$y_mid = $new_height/2; //vertical middle

		$process = imagecreatetruecolor(round($new_width), round($new_height));

		imagecopyresampled($process, $this->image, 0, 0, 0, 0, $new_width, $new_height, $width_orig, $height_orig);

		$thumb = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
		imagecopyresampled($thumb, $process, 0, 0, ($x_mid-($thumbnail_width/2)), ($y_mid-($thumbnail_height/2)), $thumbnail_width, $thumbnail_height, $thumbnail_width, $thumbnail_height);

		imagedestroy($process);
		$this->image=$thumb;
	}







}