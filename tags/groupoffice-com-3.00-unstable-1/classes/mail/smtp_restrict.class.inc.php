<?php
class smtp_restrict extends db{
	
	var $hosts; 
	
	function __construct()
	{
		$this->db();
		
		$this->hosts = $this->get_hosts();
	}
	
	function get_hosts()
	{
		global $GO_CONFIG;
		
		$hosts = array();
		if(!empty($GO_CONFIG->restrict_smtp_hosts))
		{
			$expl = explode(',', $GO_CONFIG->restrict_smtp_hosts);
			
			foreach($expl as $restriction)
			{
				
				$arr = explode(':', $restriction);
				
				$hosts[$arr[0]]=$arr[1];
			}
		}
		return $hosts;
	}
	
	function is_allowed($host)
	{
		$host = gethostbyname($host);
		
		//debug(var_export($this->hosts, true));
		//debug($host);
		
		
		if(!isset($this->hosts[$host]))
		{
			return true;
		}else
		{
			$counter = $this->get_counter($host);			
			if(!$counter)
			{
				$counter['host']=$host;
				$counter['date']=date('Y-m-d');
				$counter['count']=1;
				$this->add_counter($counter);
			}else
			{
				if($counter['date']!=date('Y-m-d'))
				{
					$counter['date']=date('Y-m-d');
					$counter['count']=1;	
				}else
				{
					$counter['count']++;					
				}
				$this->update_counter($counter);
			}
			return $counter['count']<=$this->hosts[$host];
		}
	}
	
	/**
	 * Add a Counter
	 *
	 * @param Array $counter Associative array of record fields
	 *
	 * @access public
	 * @return int New record ID created
	 */

	function add_counter($counter)
	{		
		return $this->insert_row('go_mail_counter', $counter);
	}

	/**
	 * Update a Counter
	 *
	 * @param Array $counter Associative array of record fields
	 *
	 * @access public
	 * @return bool True on success
	 */

	function update_counter($counter)
	{		
		return $this->update_row('go_mail_counter', 'host', $counter);
	}


	/**
	 * Delete a Counter
	 *
	 * @param Int $counter_id ID of the counter
	 *
	 * @access public
	 * @return bool True on success
	 */

	function delete_counter($host)
	{		
		return $this->query("DELETE FROM go_mail_counter WHERE host='$host'");
	}


	/**
	 * Gets a Counter record
	 *
	 * @param Int $counter_id ID of the counter
	 *
	 * @access public
	 * @return Array Record properties
	 */

	function get_counter($host)
	{
		$this->query("SELECT * FROM go_mail_counter WHERE host='$host'");
		if($this->next_record())
		{
			return $this->Record;
		}else
		{
			return false;
		}
	}
	
	
	
}
?>