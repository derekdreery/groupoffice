<?php
/**
 * Copyright Intermesh
 *
 * This file is part of Group-Office. You should have received a copy of the
 * Group-Office license along with Group-Office. See the file /LICENSE.TXT
 *
 * If you have questions write an e-mail to info@intermesh.nl
 *
 * @version $Id: action.tpl 1858 2008-04-29 14:09:19Z mschering $
 * @copyright Copyright Intermesh
 * @author Merijn Schering <mschering@intermesh.nl>
 */
class summary extends db{
	
	function __construct()
	{
		$this->db();
	}
	
	function __on_add_user($params)
	{
		global $GO_SECURITY, $GO_LANGUAGE, $GO_CONFIG;

		require($GO_LANGUAGE->get_language_file('summary'));
		
		$feed['user_id']=$params['user']['id'];
		$feed['url']=addslashes($lang['summary']['default_rss_url']);
		
		$this->add_feed($feed);
	}
	
	
	function get_note($user_id)
	{
		
		$sql = "SELECT * FROM su_notes WHERE user_id=$user_id";
		$this->query($sql);
		
		if(!$this->next_record())
		{
			$note['user_id']=$user_id;
			$this->insert_row('su_notes', $note);
			return $this->get_note($user_id);
		}else
		{
			return $this->f('text');
		}						
	}
	
	function update_note($note)
	{
		return $this->update_row('su_notes','user_id', $note);
	}
	
	
	
	function get_feed($user_id)
	{
		
		$sql = "SELECT * FROM su_rss_feeds WHERE user_id=$user_id";
		$this->query($sql);
		
		if(!$this->next_record())
		{
			$feed['user_id']=$user_id;
			$this->insert_row('su_rss_feeds', $feed);
			return $this->get_feed($user_id);
		}else
		{
			return $this->f('url');
		}						
	}
	
	function add_feed($feed)
	{
		return $this->insert_row('su_rss_feeds', $feed);
	}
	
	function update_feed($feed)
	{
		return $this->update_row('su_rss_feeds','user_id', $feed);
	}
	
	/* {CLASSFUNCTIONS} */
}