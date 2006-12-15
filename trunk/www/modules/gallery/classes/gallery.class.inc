<?php
/**
 * @copyright Intermesh 2006
 * @author Merijn Schering <mschering@intermesh.nl>
 * @version $Revision: 1.3 $ $Date: 2006/09/06 11:36:13 $
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 2 of the License, or (at your
 * option) any later version.
 */

class gallery extends db {
	function gallery()
	{
		$this->db();
	}
	
	function resize_image($src, $maxsize)
	{    
	    //De hoogte en breedte ophalen van het plaatje
	    $dimensions = getimagesize($src); 
	    
	    
	    //Hoogte en breedte toekennnen aan nieuwe variabelen
	    $old_width = $dimensions[0]; 
	    $old_height  = $dimensions[1];
	    
	    if($old_width>$old_height)
	    {	
	    	$new_width=$maxsize;    
	    	//De nieuwe hoogte berekenen aan de gegevens van het oude plaatje en de doel breedte
	    	$new_height = ($old_height * $new_width) / $old_width;
	    		    
	    	//De hoogte, als het nodig is, afronden
	    	$new_height = round($new_height, 0);
	    }else {
	    	$new_height=$maxsize;	    	
	    	$new_width = ($old_width * $new_height) / $old_height;
	    	$new_width = round($new_width, 0);
	    }
	
	    
	    //Het plaatje inlezen in de variabele $image
	    $image = imagecreatefromjpeg($src);
	    
	    //een nieuw klein plaatje maken met de gewenste grootte
	    $destination = imagecreatetruecolor($new_width, $new_height);
	    
	    //Het nieuwe plaatje vullen met verkleinde plaatje
	    imagecopyresampled($destination, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
	    
	    //Het plaatje weergeven
	    imagejpeg($destination, $src);
	    
	    //Het bronplaatje verwijderen
	    imagedestroy($image);
	    
	    //Het doelplaatje verwijderen
	    imagedestroy($destination); 	
	
	}
	
	function add_image($image)
	{		
		$image['id'] = $this->nextid('ig_images');
		if($this->insert_row('ig_images', $image))
		{
			return $image['id'];
		}			
		return false;
	}
	
	function update_image($image)
	{		
		return $this->update_row('ig_images', 'id', $image);
	}
	
	function get_image($image_id)
	{
		$this->query("SELECT * FROM ig_images WHERE id='$image_id'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	function delete_image($image_id)
	{
		global $GO_CONFIG;
		
		if($image = $this->get_image($image_id))
		{
			unlink($GO_CONFIG->local_path.'gallery/'.$image['gallery_id'].'/'.$image['filename']);
		
			$sql = "DELETE FROM ig_images WHERE id='$image_id'";
			return $this->query($sql);
		}
		return false;
	}
	
	function get_images($gallery_id, $start=0, $offset=0)
	{
		$sql = "SELECT * FROM ig_images WHERE gallery_id=$gallery_id";
		
		$this->query($sql);
		$count = $this->num_rows();
		
		if($offset>0)
		{
			$sql .= " LIMIT $start, $offset";
			$this->query($sql);
		}		
		return $count;
	}
	
	function image_exists($gallery_id, $filename)
	{
		$sql = "SELECT * FROM ig_images WHERE gallery_id='$gallery_id' AND filename='$filename'";
		$this->query($sql);
		return $this->next_record();
	}
	
	function add_gallery($gallery)
	{
		global $GO_SECURITY;
			
		$gallery['id'] = $this->nextid('ig_galleries');
		if($this->insert_row('ig_galleries', $gallery))
		{
			return $gallery['id'];
		}			
		return false;
	}
	
	function get_gallery_by_name($name)
	{
		$this->query("SELECT * FROM ig_galleries WHERE name='$name'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
	
	function update_gallery($gallery)
	{
		return $this->update_row('ig_galleries', 'id', $gallery);
	}
	
	function get_authorized_galleries($user_id)
	{
		$sql = "SELECT DISTINCT ig_galleries.* FROM ig_galleries ".
			"INNER JOIN acl ON (ig_galleries.acl_read = acl.acl_id ".
			"OR ig_galleries.acl_write = acl.acl_id) ".
			"LEFT JOIN users_groups ON acl.group_id = users_groups.group_id ".
			"WHERE acl.user_id=$user_id ".
			"OR users_groups.user_id=$user_id ".
			"ORDER BY ig_galleries.name ASC";				
		$this->query($sql);
		return $this->num_rows();
	}
	
	function get_gallery($gallery_id)
	{
		$this->query("SELECT * FROM ig_galleries WHERE id='$gallery_id'");
		if($this->next_record())
		{
			return $this->Record;
		}
		return false;
	}
		
	function delete_gallery($gallery_id)
	{
		$ig = new gallery();
		$this->get_images($gallery_id);
		while($this->next_record())
		{
			$ig->delete_image($this->f('id'));
		}
		return $this->query("DELETE FROM ig_galleries WHERE id='$gallery_id'");
	}
}
?>
