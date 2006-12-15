<?php

	class forum extends db
	{
		function forum()
		{
			$this->db();
		}
		
		function add_forum($name,$description,$owner_id,$mailing_list="",$ad_type='1',$custom1='',$custom2='',$custom3='',$ctime=0,$date_access=0)
		{
			$name = mysql_escape_string($name);
			$description = mysql_escape_string($description);
			$custom1 = mysql_escape_string($custom1);
			$custom2 = mysql_escape_string($custom2);
			$custom3 = mysql_escape_string($custom3);
						
			global $GO_SECURITY;
   			$acl_read = $GO_SECURITY->get_new_acl('forum read: '.$name);
   			$acl_write = $GO_SECURITY->get_new_acl('forum write: '.$name);
    		if ($acl_read > 0 && $acl_write > 0)
    		{
    			if ($ctime == 0) $ctime =  get_gmt_time();
  				$id = $this->nextid('frm_forums');
				if ( $id > 0 )
				{
					$sql = "INSERT INTO frm_forums ";
					$sql .= "(id,name,description,owner_id,acl_read,acl_write,ctime,mtime,mailing_list,ad_type,custom1,custom2,custom3,date_access) ";
					$sql .= "VALUES ('$id','$name','$description','$owner_id','$acl_read','$acl_write','$ctime','$ctime','$mailing_list','$ad_type','$custom1','$custom2','$custom3','$date_access');";
					//echo $sql;
					if ($this->query($sql))
					{
						$GO_SECURITY->add_user_to_acl($owner_id,$acl_write);
	  					return $id;
      				}else
      				{
						$GO_SECURITY->delete_acl($acl_read);
						$GO_SECURITY->delete_acl($acl_write);
						return false;
   					 }
				}
				return false;
			}
			return false;
		}
		
		function get_mailing_list($fid)
		{
			if ($this->query("SELECT mailing_list FROM frm_forums WHERE id=$fid"))
			{
				$this->next_record();
				return $this->Record['mailing_list'];
			}
			return false;
		}
		
		function get_forum($id)
		{
			if ($this->query("SELECT * FROM frm_forums WHERE id='$id'") )
			{
				$this->next_record();
				return $this->Record;
			}
			return false;
			
		}
		function update_forum($id,$name,$description,$mailing_list="",$ad_type='1',$custom1="",$custom2="",$custom3="",$mtime=0,$date_access=0)
		{
			$name = mysql_escape_string($name);
			$description = mysql_escape_string($description);
			$custom1 = mysql_escape_string($custom1);
			$custom2 = mysql_escape_string($custom2);
			$custom3 = mysql_escape_string($custom3);
			
			if ($mtime == 0) $mtime =  get_gmt_time();
			$sql = "UPDATE frm_forums SET name='$name',description='$description',mailing_list='$mailing_list',ad_type='$ad_type',custom1='$custom1',custom2='$custom2',custom3='$custom3',date_access='$date_access' WHERE id='$id'";
			//echo $sql;				
    		return $this->query($sql);
		}
		function delete_forum($id)
		{
			$sql = "DELETE from frm_forums where id='$id'";
			return $this->query($sql);		
		}
		function get_authorised_forums($user_id,$sort="name",$order="ASC")
		{
			$sql = "SELECT DISTINCT frm_forums.* FROM frm_forums, acl, users_groups ";
			$sql .= " WHERE (".
     		"frm_forums.acl_read = acl.acl_id OR frm_forums.acl_write = acl.acl_id".
      		") AND ( ( acl.group_id = users_groups.group_id AND users_groups.user_id = '$user_id' AND acl.user_id = 0 ) OR (".
  	  		"acl.group_id = 0 AND acl.user_id = '$user_id' ) ) ";
			$sql .= "ORDER BY frm_forums.$sort $order";
  		  	//echo $sql;
			if ( $this->query($sql) )
			{
				return $this->num_rows();
			}
			return false;
		}
		function get_read_messages_total($user_id, $forum_id)
		{
			$sql = "SELECT msg.id FROM frm_messages msg ";
			$sql .= "INNER JOIN frm_msg_views AS views ON msg.id = views.message_id ";
			$sql .= "WHERE msg.forum_id = '$forum_id' AND msg.user_id='$user_id' ";
			$sql .= "GROUP BY views.message_id,views.user_id ";
			echo $sql.'<br />';
			$f = new forum();
			if ($f->query($sql))
			{
				return $f->num_rows();
			}
			return -1;
		}
		
		function get_read_subjects_total($user_id,$forum_id)
		{
			$sql = "SELECT msg.id FROM frm_messages msg ";
			$sql .= "INNER JOIN frm_msg_views AS views ON msg.id = views.message_id ";
			$sql .= "WHERE msg.forum_id = '$forum_id' AND msg.user_id='$user_id' AND msg.parent_id = '0' ";
			$sql .= "GROUP BY views.message_id,views.user_id";
			$f = new forum();
			if ($f->query($sql))
			{
				return $f->num_rows();
			}
			return -1;
		}
		
		function get_messages_total($forum_id)
		{
			$f = new forum();
			$sql = "SELECT * FROM frm_messages WHERE frm_messages.forum_id ='$forum_id' and frm_messages.parent_id != 0";
			if ( $f->query($sql) )
			{
				$count = $f->num_rows();
			}else
			{
				$count = 0;
			}
			unset($f);
			return $count;
		}
		
		function get_subjects_total($forum_id)
		{
			$f = new forum();
			$sql = "SELECT * FROM frm_messages WHERE frm_messages.forum_id ='$forum_id' AND frm_messages.parent_id='0' ";
			if ( $f->query($sql) )
			{
				$count = $f->num_rows();
			}else
			{
				$count = 0;
			}
			unset($f);
			return $count;
		}
		
		function get_owned_forums($user_id)
		{
			$sql = "SELECT DISTINCT frm_forums.* FROM frm_forums where owner_id='$user_id';";
			if ( $this->query($sql) )
			{
				return $this->num_rows();
			}
			return false;
		}
		function forum_exist($name)
		{
			$name = mysql_escape_string($name);
			$sql = "SELECT * FROM frm_forums WHERE name='$name'";
			$this->query($sql);
			if ( $this->num_rows() > 0)
			{
				$this->next_record();
				return $this->Record['id'];
			}else
			{
				return 0;
			}
		}
		
		function add_message($user_id,$title,$message,$forum_id,$parent_id=0,$custom1= '',$custom2='',$custom3='',$ctime=0)
		{
			$custom1 = mysql_escape_string($custom1);
			$custom2 = mysql_escape_string($custom2);
			$custom3 = mysql_escape_string($custom3);
			
			//$title = mysql_escape_string($title);
			//$message = mysql_escape_string($message);
			
			$id = $this->nextid('frm_messages');
			if ( $id > 0 )
			{
				if ($ctime == 0) $ctime =  get_gmt_time();
  				
				$sql = "INSERT INTO frm_messages ";
				$sql .= "(id,user_id,parent_id,title,message,forum_id,ctime,custom1,custom2,custom3) ";
				$sql .= "VALUES ('$id','$user_id','$parent_id','$title','$message','$forum_id','$ctime','$custom1','$custom2','$custom3');";
				if ( $this->query($sql) )
				{
					return $id;
				}
			}
			return false;
		}
		function add_settings($user_id,$forum_id)
		{
			$sql = "INSERT INTO frm_settings (user_id,forum_id) ";
			$sql .= "VALUES ('$user_id','$forum_id') ";
			return $this->query($sql);
		}
		function get_settings($user_id,$forum_id)
		{
			$sql = "SELECT * FROM frm_settings where user_id='$user_id' AND forum_id='$forum_id'";
			$this->query($sql);
			if ($this->next_record())
			{
				return $this->Record;
			}
			return false;
		}
		function get_msg_ids($forum_id)
		{
			$f=new forum();
			$array = '';
			if ($f->get_messages($forum_id))
			{
				$debut = true;
				while($f->next_record())
				{
					if ( !$debut ) $array .= ',';
					else $debut = false;
					$array .= "'".$f->f('id')."'";
					
				}
			}
			return $array;
		}
		
		function set_settings($user_id,$forum_id,$display='1',$sort='m.title',$order='DESC',$theme='1')
		{
			$sort = mysql_escape_string($sort);
			
			$sql = "UPDATE frm_settings SET `order`='$order',display='$display',sort='$sort',theme='$theme' ";
			$sql .= "WHERE user_id='$user_id' AND forum_id='$forum_id'";
			return $this->query($sql);
		}
		
		function get_message($id)
		{
			$sql = "SELECT * FROM frm_messages WHERE id='$id';";
			if ( $this->query($sql) && $this->next_record() )
			{
				return $this->Record;
			}
			return false;
 		}
		function get_messages($forum_id,$sort="m.title",$order="ASC")
		{
			$sql = "SELECT m.*,u.first_name,u.last_name FROM frm_messages m,users u WHERE u.id = m.user_id AND m.forum_id='$forum_id' ";
			$sql .= "ORDER BY $sort $order";
			if ( $this->query($sql) )
			{
				return $this->num_rows();
			}
			return false;
		}
		function update_message($id,$title,$message,$custom1='',$custom2='',$custom3='')
		{
			$custom1 = mysql_escape_string($custom1);
			$custom2 = mysql_escape_string($custom2);
			$custom3 = mysql_escape_string($custom3);
			$ctime = get_gmt_time();
			$sql = "UPDATE frm_messages SET title='$title',message='$message',custom1='$custom1',custom2='$custom2',custom3='$custom3' WHERE id='$id'";
			if ( $this->query($sql))
			{
				$this->query($sql);
				$this->send_new_message_ad($id);
				return $this->query("DELETE FROM frm_msg_views WHERE message_id='$id'");
			}
		}
		function delete_message($id,$recursive = false)
		{
			global $GO_CONFIG;
			$f = new forum();
			$obj = $f->get_message($id);
			$rep = $GO_CONFIG->file_storage_path.'forum/'.$obj['forum_id'].'/'.$id;
			
			$sql = "DELETE FROM frm_messages WHERE id='$id'";
			$f->query($sql);
			//echo $rep;
			$this->rmdirr($rep);
				
			if ( $recursive )
			{
				if ( $f->get_messages_from_parent($id) )
				{
					while( $f->next_record() )
					{
						$this->delete_message($f->Record['id'],true);
					}
				}
				
				
			}
			unset($f);
			return true;
			
		}	
		
		
		function get_messages_from_parent($parent_id,$forum_id=0)
		{
				if (isset($_SESSION['forum_sort']))
				{
					$sort = "ASC";
				}else
				{
					$sort = "DESC";
				}
				
				$sql = "SELECT * FROM frm_messages WHERE parent_id='$parent_id' ";
				if ( $forum_id > 0 ) $sql .= "AND forum_id='$forum_id' ";
				$sql .= "ORDER BY ctime $sort";  
				//echo $sql;
				if ( $this->query($sql) )
				{
					return $this->num_rows();
				}
				return false;
		}
		
		function get_tree($msg_id,$forum_id=0)
		{
			global $GO_USERS,$GO_CONFIG,$GO_SECURITY,$GO_THEME,$frm_reply,$image;
			

			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			$forum = new forum();
				
			$tree = '';
			if ($forum->get_messages_from_parent($msg_id,$forum_id) > 0) {
				$tree .= '<ul>';
				
				while ( $forum->next_record() )
				{

					$obj = $forum->Record;
					$fid = $obj['forum_id']; $mid = $obj['id'];
					$rep = $GO_CONFIG->file_storage_path."forum/$fid/$mid/";
			
					$user = $GO_USERS->get_user($obj['user_id']);
					$str_user = $user['last_name'].' '.$user['first_name'];
					$datec = date($_SESSION['GO_SESSION']['date_format'].', G:i', $obj['ctime']+get_timezone_offset()*3600);
					
					$files = $fs->get_files($rep);
					$count = count($files);
					$new = new forum();
					if ( $this->is_expanded($mid))
					{
						$tree .= '<li>';
						$tree .= '<a id="'.$obj['id'].'" href="javascript:unexpand(\''.$mid.'\');" >'.'<img src="'.$GO_THEME->images['min_node'].'" border="0" align="absmiddle" />';
					}else
					{
						$tree .= '<li>';
						$tree .= '<a id="'.$obj['id'].'" href="javascript:expand(\''.$mid.'\');" >'.'<img src="'.$GO_THEME->images['plus_node'].'" border="0" align="absmiddle" />';
					}
					//$tree .= '<a href="javascript:change__msg(\''.$obj['id'].'\');" >';
					 
					
					$f = new forum();
					$li_class = $f->user_has_seen_message($GO_SECURITY->user_id,$mid) ? "li_title_seen" : "li_title_unseen";
					$tree .= '<span class="'.$li_class.'">'.$obj['title'].' </span></a>';
					//$tree .= $rep;
					if ( $count > 0)
					{
						for ($i=0;$i<$count;$i++)
						{
							$ext = get_extension($files[$i]['name']);
							$image = get_filetype_image($ext);
							$fname=$files[$i]['name'];
							$tree .= '<a href ="download.php?path='.$rep.$files[$i]['name'].'"><img title="'.$files[$i]['name'].'" src="'.$image.'" border="0" /></a> ';
						}
					}
					$tree .= '<span class="li_author">'.$str_user.' </span>';	
					$tree .= '<span class="li_date">'.$datec.' </span>';
					if ( $this->user_can_post($GO_SECURITY->user_id,$fid))
					{
						$tree .= '<a href="javascript:popup(\'post.php?forum_id='.$fid.'&reply_to='.$mid.'\',500,350);" title="'.$frm_reply.'" ><img src="'.$GO_THEME->images['forward_small'].'" border="0" /></a>';
					}
					
					if ( $this->is_expanded($mid) )
					{
						$obj['message'] = str_replace("\n","<br />",$obj['message']);
						$tree .= '<p class="ub_visible" nowrap>'.$obj['message'].'</p>';
					}
					
					if ( $this->is_expanded($mid ) )
					{
						$tree .= $f->get_tree($obj['id']);
					}
					$tree .= '</li>';
					
				}	
				$tree .= '</ul>';
			}
			unset($new);
			unset($fs);
			unset($forum);
			unset($f);
					
			return $tree;
		}
		function get_list($forum_id,$sort='m.ctime',$order='DESC')
		{
			global $GO_USERS,$GO_CONFIG,$GO_SECURITY,$GO_THEME,$frm_reply,$frm_edit,$frm_delete,$image;
			
			$forum = new forum();
			$tree = '';
			if ( !$forum->get_messages($forum_id,$sort,$order)) return '';
			$tree .= '<ul>';
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			
			$fs = new filesystem();
			$new = new forum();
				
			while( $forum->next_record() )
			{
					
					$obj = $forum->Record;
					//foreach($obj as $key=>$value) echo "$key=>$value<br />";
					$fid = $obj['forum_id']; $mid = $obj['id'];
					$rep = $GO_CONFIG->file_storage_path."forum/$fid/$mid/";
					//echo $mid;
					$str_user = $obj['last_name'].' '.$obj['first_name'];
					$datec = date($_SESSION['GO_SESSION']['date_format'].', G:i', $obj['ctime']+get_timezone_offset()*3600);
					$files = $fs->get_files($rep);
					$count = count($files);
					$cls='pair';
					if (!$new->user_has_seen_message($GO_SECURITY->user_id,$mid))
					{
						$cls = 'new_'.$cls;
					} 
					
					$tree .= '<div id="'.$mid.'" class="'.$cls.'">';
					$tree .= '<li>';
					$style='li_title_alone';
					$tree .= '<div id="title_'.$mid.'" class="nothing" >';
					$f = new forum();
					$li_class = $f->user_has_seen_message($GO_SECURITY->user_id,$mid) ? "li_title_seen" : "li_title_unseen";
					$tree .= '<a href="javascript:toggleText('.$mid.');"><span class="'.$style.'" >'.$obj['4'].'</span></a>';
						
					if ( $count > 0)
					{
						for ($i=0;$i<$count;$i++)
						{
							$ext = get_extension($files[$i]['name']);
							$image = get_filetype_image($ext);
							$fname=$files[$i]['name'];
							$tree .= '<a href="download.php?path='.$rep.$files[$i]['name'].'"> <img title="'.$files[$i]['name'].'" src="'.$image.'" border="0" /></a> ';
						}
					}
					$tree .= '</div>';
					//$tree .= $rep;
					
					$tree .= '<span class="right">';
					$tree .= '<span class="li_author">'.$str_user.' </span>';
					$tree .= '<span class="li_date">'.$datec.' </span>';
					if ( $this->user_can_post($GO_SECURITY->user_id,$fid) || $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
					{
						if ( $obj['user_id'] == $GO_SECURITY->user_id || $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id) )
						{
							$tree .= '<a href="javascript:popup(\'post.php?forum_id='.$fid.'&edit='.$mid.'\',500,270);" title="'.$frm_edit.'" ><img src="'.$GO_THEME->images['edit'].'" border="0" /></a>&nbsp;';
							$tree .= '<a href="javascript:delete_message(\''.$mid.'\');" title="'.$frm_delete.'" ><img src="'.$GO_THEME->images['delete'].'" border="0" /></a>&nbsp;';
						}
						$tree .= '<a href="javascript:popup(\'post.php?forum_id='.$fid.'&reply_to='.$mid.'\',500,350);" title="'.$frm_reply.'" ><img src="'.$GO_THEME->images['forward_small'].'" border="0" /></a>';
					}
					$tree .= '</span>';
					$obj['message'] = str_replace("\n","<br />",$obj['message']);
					$tree .= '<div id="text_of_'.$mid.'" class="pnothing" style="display:none;">';
					$tree .= '<span class="msg_message">';
					// Custom fields
						$tree .= '<span class="custom_fields">';
						$plox = '';
						$the_forum = $f->get_forum($obj['forum_id']);
						for ($i=1;$i<4;$i++)
    					{
    						if ( $the_forum["custom$i"] != '' )
    						{
    							$j = $i - 1; 
    							if ( isset($obj["custom$j"]) && $obj["custom$j"] != '' && $obj["custom$i"] != '') $plox .= ',';
    							$plox .= '&nbsp;'.$obj["custom$i"].'&nbsp;';
    						}
    					}
						$tree .= $plox;
						$tree .= '</span><br />'.$obj['message'].'</span></div>';
						
					$tree .= '</li></div>';
			}	
			$tree .= '</ul>';
			
			return $tree;
		
		}
		
		function get_parent_id($msg_id,$recursive = false)
		{
			$sql = "SELECT parent_id FROM frm_messages WHERE id='$msg_id'";
			if ( $this->query($sql) && $this->next_record() );
			{
				if ( $recursive )
				{
					if ( $this->Record['parent_id'] == 0 )	return $msg_id;
					return $this->get_parent_id($this->Record['parent_id'],true);
				}
				return $this->Record['parent_id'];
			}
		}
		// tree using javascript remote scripting
		function get_jsrs_tree($msg_id,$forum_id=0,$bool=false,$topic_closed=false)
		{
			global $GO_USERS,$GO_CONFIG,$GO_SECURITY,$GO_THEME,$frm_reply,$frm_edit,$frm_delete,$frm_close_topic,$image;
			global $frm_closed_topic,$frm_reopen_topic;
			$bool = !$bool;

			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$fs = new filesystem();
			$forum = new forum();
				
			$tree = '';
			if ($forum->get_messages_from_parent($msg_id,$forum_id) > 0) {
				$tree .= '<ul>';
				$cls = $bool ? 'pair':'impair';
				$new = new forum();
				while ( $forum->next_record() )
				{
					$cls = $bool ? 'pair':'impair';
					$obj = $forum->Record;
					$fid = $obj['forum_id']; $mid = $obj['id'];
					$rep = $GO_CONFIG->file_storage_path."forum/$fid/$mid/";
			
					$user = $GO_USERS->get_user($obj['user_id']);
					$str_user = $user['last_name'].' '.$user['first_name'];
					$datec = date($_SESSION['GO_SESSION']['date_format'].', G:i', $obj['ctime']+get_timezone_offset()*3600);
					
					$files = $fs->get_files($rep);
					$count = count($files);
					if (!$new->user_has_seen_message($GO_SECURITY->user_id,$mid))
					{
						$cls = 'new_'.$cls;
					} 
					$tree .= '<div id="'.$mid.'" class="'.$cls.'">';
					$cls='';
					$tree .= '<li>';
					
					$flox = new forum();
					$has_sub = ( $flox->get_messages_from_parent($mid,$fid) > 0) ? true : false;
					$style='li_title_alone';
					if ( $has_sub )
					{
						$tree .= '<div id="image_div_'.$mid.'" class="div_image"><a href="javascript:toggleDiv(\''.$mid.'\');" ><img name="image_of_'.$mid.'" src="images/min_node.gif" class="image" /></a></div>';
						$style='li_title';
					}
					$tree .= '<div id="title_'.$mid.'" class="nothing" >';
					
					unset($flox);
						//$tree .= '<a href="javascript:change__msg(\''.$obj['id'].'\');" >';
					 
					
					$f = new forum();
					$the_forum = $f->get_forum($obj['forum_id']);
					$li_class = $f->user_has_seen_message($GO_SECURITY->user_id,$mid) ? "li_title_seen" : "li_title_unseen";
					$closed = '';
					if ( $obj['parent_id']==0 && $obj['closed'] == 'y' )
					{
						$closed = "[$frm_closed_topic] ";
					}
					$tree .= '<a href="javascript:toggleText('.$mid.');"><span class="'.$style.'" >'.$closed.$obj['title'].'</span></a>';
					if ( $count > 0)
					{
						for ($i=0;$i<$count;$i++)
						{
							$ext = get_extension($files[$i]['name']);
							$image = get_filetype_image($ext);
							$fname=$files[$i]['name'];
							$tree .= '<a href="download.php?path='.$rep.$files[$i]['name'].'"> <img title="'.$files[$i]['name'].'" src="'.$image.'" border="0" /></a> ';
						}
					}
					//$tree .= $rep;
					$tree .= '</div>';
					$tree .= '<span class="right">';
					$tree .= '<span class="li_author">'.$str_user.' </span>';
					$tree .= '<span class="li_date">'.$datec.' </span>';
					if ( ($this->user_can_post($GO_SECURITY->user_id,$fid) || $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id)) && $obj['closed'] == 'n' && !$topic_closed)
					{
						if ( $obj['user_id'] == $GO_SECURITY->user_id || $GO_SECURITY->has_admin_permission($GO_SECURITY->user_id))
						{
							if ( $obj['parent_id'] == 0 )
							{
								$tree .= '<a href="javascript:close_topic(\''.$mid.'\');" title="'.$frm_close_topic.'" ><img src="'.$GO_THEME->images['unsubscribe_event'].'" border="0" /></a>&nbsp;';
							}
							$tree .= '<a href="javascript:popup(\'post.php?forum_id='.$fid.'&edit='.$mid.'\',500,270);" title="'.$frm_edit.'" ><img src="'.$GO_THEME->images['edit'].'" border="0" /></a>&nbsp;';
							$tree .= '<a href="javascript:delete_message(\''.$mid.'\');" title="'.$frm_delete.'" ><img src="'.$GO_THEME->images['delete'].'" border="0" /></a>&nbsp;';
						}
						$tree .= '<a href="javascript:popup(\'post.php?forum_id='.$fid.'&reply_to='.$mid.'\',500,350);" title="'.$frm_reply.'" ><img src="'.$GO_THEME->images['forward_small'].'" border="0" /></a>';
					}else if ( $obj['parent_id']==0 && $obj['user_id']==$GO_SECURITY->user_id  )
					{
						$tree .= '<a href="javascript:reopen_topic(\''.$mid.'\');" title="'.$frm_reopen_topic.'" ><img src="'.$GO_THEME->images['ok'].'" border="0" /></a>&nbsp;';
					}
					$tree .= '</span>';
					
						$obj['message'] = str_replace("\n","<br />",$obj['message']);
						$tree .= '<div id="text_of_'.$mid.'" class="pnothing" style="display:none;"><span class="msg_message">';
						
						// Custom fields
						$tree .= '<span class="custom_fields">';
						$plox = '';
						for ($i=1;$i<4;$i++)
    					{
    						if ( $the_forum["custom$i"] != '' )
    						{
    							$j = $i - 1; 
    							if ( isset($obj["custom$j"]) && $obj["custom$j"] != '' && $obj["custom$i"] != '') $plox .= ',';
    							$plox .= '&nbsp;'.$obj["custom$i"].'&nbsp;';
    						}
    					}
						$tree .= $plox;
						$tree .= '</span><br />';
					
						/*for ($i=1;$i<4;$i++)
						{
							if ( $the_forum["custom$i"] != '' )
							{
								$tree .= '<span class="custom_fields">'.$the_forum["custom$i"].': '.$obj["custom$i"].'</span><br />';
							}
						}*/
						
						$tree .= $obj['message'];
						$tree .= '</span></div>';
						
						$tree .= '<div id="children_of_'.$mid.'" class="nothing">';
						if ( $obj['closed'] == 'y' || $topic_closed ) 
						{
							$tree .= $f->get_jsrs_tree($obj['id'],'',$bool,true);
						}else
						{
							$tree .= $f->get_jsrs_tree($obj['id'],'',$bool);
						}
						
						$tree .= '</div>';
						$tree .= '</li></div>';
					
				}	
				$tree .= '</ul>';
			}
			unset($new);
			unset($fs);
			unset($forum);
			unset($f);
					
			return $tree;
		}
		
		function close_topic($msg_id)
		{
			$sql = "UPDATE frm_messages SET closed='y' WHERE id='$msg_id'";
			return $this->query($sql);
		}
		
		function reopen_topic($msg_id)
		{
			$sql = "UPDATE frm_messages SET closed='n' WHERE id='$msg_id'";
			return $this->query($sql);
		}
		
		
		function is_expanded($msg_id)
		{	
			if ( !isset($_SESSION['expanded_forums'][$msg_id]))
			{
				return false;
			}
			return true;
		}
		function expand($msg_id)
		{
		
		}
		
		function unexpand($msg_id)
		{
		
		}
		
		function user_can_post($user_id,$forum_id)
		{
			global $GO_SECURITY;
			$f = new forum();
			$obj = $f->get_forum($forum_id);
			unset($f);
			return $GO_SECURITY->has_permission($user_id,$obj['acl_write']);
		}
		
		function user_saw_message($user_id,$msg_id,$ctime=0)
		{	
			if ( $ctime == 0) $ctime = get_gmt_time();
			if ( $this->user_has_seen_message($user_id,$msg_id) )
			{
				$sql = "UPDATE frm_msg_views SET ctime='$ctime' ";
				$sql .= "WHERE user_id='$user_id' AND message_id='$msg_id'";
				return $this->query($sql);
			}else
			{
				$sql = "INSERT INTO frm_msg_views ";
				$sql .= "(user_id,message_id,ctime) ";
				$sql .= "VALUES ('$user_id','$msg_id','$ctime') ";
				return $this->query($sql);
			}
			
		}
		
		function user_has_seen_message($user_id,$msg_id)
		{
			$sql = "SELECT * FROM frm_msg_views WHERE user_id='$user_id' AND message_id='$msg_id'";
			if ( $this->query($sql) )
			{
				if ( $this->num_rows() > 0 )
				{
					return true;
				}
			}
			return false;
		}
		
		/* return an array of users id of people who are part of a forum but
		haven't been emailed yet */
		function check_unemailed_users($forum_id)
		{
			$f = new forum();
			$advertise = array();
			$users = $f->get_users_from_forum($forum_id);
			foreach($users as $user_id)
			{
				if ( !$f->get_settings($user_id,$forum_id))
				{
					$f->add_settings($user_id,$forum_id);
					$advertise[] = $user_id;				
				}
			}
			$list = $f->get_mailing_list_from_array($advertise);
			if ( count($users) > 0 ) $f->send_new_forum_ad($list);
			return $advertise;
		
		}
		
		function send_new_forum_ad($mailing_list)
		{
			
			
		}
		function get_template($template)
		{
			$lang = 'fr';
			if ( !$fp = fopen('templates/'.$lang.'/'.$template, 'r') ) {
    			exit( "Failed to open '$template' template" );
 			}
  			$body = fread($fp, filesize('templates/'.$lang.'/'.$template));
  			if (strlen( $body ) == 0 ) {
    			exit( "Failed to read from '$template' template" );
  			}
  			fclose($fp);
			return $body;
	}
		function send_import_mail($forum_id,$count,$user_id=0)
		{
			global $frm_new_s,$frm_reply_s,$frm_mail_subject3,$frm_mail_body3,$GO_USERS,$GO_CONFIG,$GO_MODULES,$mail_style;
			$f = new forum();
			$datec = date($_SESSION['GO_SESSION']['date_format'], get_gmt_time()+get_timezone_offset()*3600);
			$time = date('G:i',get_gmt_time()+get_timezone_offset()*3600);
			
			$forum = $f->get_forum($forum_id);
			$mailing_list = $f->get_whole_mailing_list($forum_id);
			$user=$GO_USERS->get_user($user_id);
			
			if ( $count == 1 )
			{
				$new_s = $frm_new_s[0];
				$answer_s = $frm_reply_s[0];
			}else
			{
				$new_s = $frm_new_s[1];
				$reply_s = $frm_reply_s[1];
			}
			$frm_mail_subject3 = str_replace('%forum_name%',$forum['name'],$frm_mail_subject3);
    		$frm_mail_subject3 = str_replace('%count%',$count,$frm_mail_subject3);
    		$frm_mail_subject3 = str_replace('%new_s%',$new_s,$frm_mail_subject3);
    		$frm_mail_subject3 = str_replace('%reply_s%',$reply_s,$frm_mail_subject3);
    		
			$frm_mail_body3 = str_replace('%user%',$user['first_name'].' '.$user['last_name'],$frm_mail_body3);
    		$frm_mail_body3 = str_replace('%style%',$mail_style,$frm_mail_body3);
    		$frm_mail_body3 = str_replace('%date%',$datec,$frm_mail_body3);
			$frm_mail_body3 = str_replace('%time%',$time,$frm_mail_body3);
			
			$frm_mail_body3 = str_replace('%count%',$count,$frm_mail_body3);
			$frm_mail_body3 = str_replace('%new_s%',$new_s,$frm_mail_body3);
			$frm_mail_body3 = str_replace('%reply_s%',$reply_s,$frm_mail_body3);
			
			$frm_mail_body3 = str_replace('%url%',$GO_CONFIG->full_url.'index.php?return_to='.urlencode($GO_MODULES->modules['forum']['url'].'forum_view.php?id='.$forum_id),$frm_mail_body3);
    				//echo $mailing_list;
			sendmail($mailing_list, 'support@lccy.net', $GO_CONFIG->title, $frm_mail_subject3,$frm_mail_body3,'3','text/html');
    				//new subject
				
		}
		
		function send_new_message_ad($msg_id)
		{
			global $frm_mail_subject,$frm_mail_subject2,$frm_mail_body,$frm_mail_body2,$GO_USERS,$GO_CONFIG,$GO_MODULES,$mail_style;
			$f = new forum();
			
			$obj = $f->get_message($msg_id);
			
			$datec = date($_SESSION['GO_SESSION']['date_format'], $obj['ctime']+get_timezone_offset()*3600);
			$time = date('G:i',$obj['ctime']+get_timezone_offset()*3600);
			$body = $obj['message'];
			$title = $obj['title'];
			$user = $GO_USERS->get_user($obj['user_id']);

			$forum = $f->get_forum($obj['forum_id']);
			if ( $forum['ad_type'] == 3) return false;
			$mailing_list = $f->get_whole_mailing_list($forum['id']);
			if ( $obj['parent_id'] == 0 )
			{
				if ( $forum['ad_type'] == 1 || $forum['ad_type'] == 2 )
				{
					$body =str_replace('\r\n','<br />',$body);
					
    				$frm_mail_subject = str_replace('%forum_name%',$forum['name'],$frm_mail_subject);
    				$frm_mail_body = str_replace('%user%',$user['first_name'].' '.$user['last_name'],$frm_mail_body);
    				$frm_mail_body = str_replace('%style%',$mail_style,$frm_mail_body);
    				$frm_mail_body = str_replace('%msg_title%',$title,$frm_mail_body);
    				$frm_mail_body = str_replace('%msg_body%',$body,$frm_mail_body);
					$frm_mail_body = str_replace('%date%',$datec,$frm_mail_body);
					$frm_mail_body = str_replace('%time%',$time,$frm_mail_body);
					
    				$frm_mail_body = str_replace('%url%',$GO_CONFIG->full_url.'index.php?return_to='.urlencode($GO_MODULES->modules['forum']['url'].'forum_view.php?id='.$forum['id'].'#'.$obj['id']),$frm_mail_body);
    				//echo $mailing_list;
			
    				sendmail($mailing_list, 'support@lccy.net', $GO_CONFIG->title, $frm_mail_subject,$frm_mail_body,'3','text/html');
    				//new subject
			
				}
			}else
			{
				if ( $forum['ad_type'] == 2)
				{
					//new message
					$body =str_replace('\r\n','<br />',$body);
    				$frm_mail_subject = str_replace('%forum_name%',$forum['name'],$frm_mail_subject2);
    				$frm_mail_body = str_replace('%style%',$mail_style,$frm_mail_body2);
    				$frm_mail_body = str_replace('%user%',$user['first_name'].' '.$user['last_name'],$frm_mail_body);
    				$frm_mail_body = str_replace('%msg_title%',$title,$frm_mail_body);
    				$frm_mail_body = str_replace('%msg_body%',$body,$frm_mail_body);
    				$frm_mail_body = str_replace('%date%',$datec,$frm_mail_body);
					$frm_mail_body = str_replace('%time%',$time,$frm_mail_body);
					
					$frm_mail_body = str_replace('%url%',$GO_CONFIG->full_url.'index.php?return_to='.urlencode($GO_MODULES->modules['forum']['url'].'forum_view.php?id='.$forum['id'].'#'.$obj['id']),$frm_mail_body);
    				
					//echo $mailing_list;
					//echo "<script language=\"javascript\">alert('$mailing_list');</script>";
				
    				sendmail($mailing_list, 'support@lccy.net', $GO_CONFIG->title, $frm_mail_subject,$frm_mail_body,'3','text/html');
    				//new subject
				
				}
			}		
		
		}
		
		
		function get_mailing_list_from_acl($forum_id)
		{
			$f = new forum();
			$users = $f->get_users_from_forum($forum_id);
			return $f->get_mailing_list_from_array($users);
		}
		
		function get_mailing_list_from_array($array)
		{
			global $GO_USERS;
			$mailing_list = '';$begin = true;
			foreach($array as $user_id)
			{
				$user = $GO_USERS->get_user($user_id);
				if ( !$begin ) $mailing_list .= ',';$begin = false;
				$mailing_list .= $user['email'];
				
			}
			return $mailing_list;
		}
		function add_csv_to_forum($csv,$forum_id,$sep=',',$erase_all=false,$date1='',$date2='')
		{
			global $GO_SECURITY;
			$row = 0;
			$out = array();
            $handle = fopen($csv, "r");
            while (($data = fgetcsv($handle, 1000, $sep)) !== FALSE) {
               $num = count($data);
			   for ($c=0; $c < $num; $c++) {
                   $out[$row][] = $data[$c];
				   //echo $data[$c] . "<br />\n";
               }
			   $row++;
               
            }
            fclose($handle);
			$user_id = $GO_SECURITY->user_id;
			$add = 0;
			
			if ( $erase_all )
			{
				$this->delete_messages_from_forum($forum_id);
			}
			
			$date1 = ($date1 != '') ? date_to_unixtime($date1) : '';
			$date2 = ($date2 != '') ? date_to_unixtime($date2) : '';
			$ok2test = ($date1 != '' && $date2 != '');
			
			$add=0;
			foreach ( $out as $id=>$array)
			{
				$date = $array['0'];
				$heure = $array['1'];
				$title = smart_addslashes(enc_utf8($array['2']));
				$message = smart_addslashes(enc_utf8($array['3']));
				$interlocuteur = smart_addslashes(enc_utf8($array['4']));
				$duree = enc_utf8($array['5']);
				$km = isset($array['6']) ? enc_utf8($array['6']) : '';
				
				$jour = substr($date,0,2);
				$mois = substr($date,3,2);
				$annee = substr($date,6,4);
				
				$h = substr($heure,0,2);
				$m = substr($heure,3,2);
				
				//echo "$jour $mois $annee $h $m <br />";
				$h = $h-get_timezone_offset();
				
				$ctime = mktime($h,$m,0,$mois,$jour,$annee);
				$cstime = date_to_unixtime($date.' '.$heure);
				//echo 'date1:'.$date1.' date2:'.$date2.' ctime'.$cstime.'<br />';
				//$datec = date($_SESSION['GO_SESSION']['date_format'].', G:i', $ctime+get_timezone_offset()*3600).'<br />';
				//echo$datec;		
				if ( $ok2test )
				{
					if ( $cstime >= $date1 && $cstime <= $date2 )
					{
						if ( $this->add_message($user_id,$title,$message,$forum_id,0,$interlocuteur,$duree,$km,$ctime))
						{
							$add++;
						}
					}
				}else
				{
					if ( $this->add_message($user_id,$title,$message,$forum_id,0,$interlocuteur,$duree,$km,$ctime))
					{
						$add++;
					}
		
				}
				
			}
			return $add;
		}	
		function delete_messages_from_forum($forum_id)
		{
			return $this->query("DELETE FROM frm_messages WHERE forum_id = $forum_id");
		}
		function get_users_from_forum($forum_id)
		{
			global $GO_SECURITY,$GO_GROUPS,$GO_USERS;
			$f = new forum();
			$f->get_forum($forum_id);
			$acl_read = $f->f('acl_read');
			$acl_write = $f->f('acl_write');
			
			$array = array();
						
			$GO_SECURITY->get_users_in_acl($acl_read);
			while($GO_SECURITY->next_record())
			{
				if ($GO_SECURITY->f('user_id') > 0 )
				$array[] = $GO_SECURITY->f('user_id');
			}
			$GO_SECURITY->get_users_in_acl($acl_write);
			while($GO_SECURITY->next_record())
			{
				if ($GO_SECURITY->f('user_id') > 0 )
				$array[] = $GO_SECURITY->f('user_id');
			}
			
			$GO_SECURITY->get_groups_in_acl($acl_read);
			while($GO_SECURITY->next_record())
			{
				if ( $GO_GROUPS->get_users_in_group($GO_SECURITY->f('id')))
				while($GO_GROUPS->next_record())
				{
					$array[] = $GO_GROUPS->f('id');
				}
				
			}
			
			$GO_SECURITY->get_groups_in_acl($acl_write);
			while($GO_SECURITY->next_record())
			{
				if ($GO_GROUPS->get_users_in_group($GO_SECURITY->f('id')))
				while($GO_GROUPS->next_record())
				{
					$array[] = $GO_GROUPS->f('id');
				}
			}
			$array = array_unique($array);
			
			return $array;
			
		}
		
		function get_files_from_message($mid)
		{
			global $GO_CONFIG;
			require_once($GO_CONFIG->class_path.'filesystem.class.inc');
			$f = new forum();
			$fs = new filesystem();	
			$obj = $f->get_message($mid);
			$fid = $obj['forum_id'];
			$rep = $GO_CONFIG->file_storage_path."forum/$fid/$mid/";
			return $fs->get_files($rep);
		}
		
		function get_whole_mailing_list($forum_id)
		{
			$f = new forum();
			$obj = $f->get_forum($forum_id);
			$whole = $f->get_mailing_list_from_acl($forum_id);
			if ( trim($obj['mailing_list']) != "" )
			{
				$whole .= ','.$obj['mailing_list'];
			}
			return $whole;
		}
		function get_last_message($forum_id)
		{
			$f = new forum();
			$sql = "SELECT * FROM frm_messages WHERE forum_id='$forum_id' ORDER BY ctime DESC";
			if ( $f->query($sql) && $f->next_record() )
			{
				return $f->Record;
			}
			return false;
		}
		
		function get_read_count($user_id,$forum_id,$is_subject=false)
		{
			$f = new forum();
			$sql = "SELECT DISTINCT count( v.message_id ) AS `cnt` FROM frm_msg_views v, frm_messages m ";
			$sql .= "WHERE v.message_id = m.id AND v.user_id='$user_id' AND m.forum_id ='$forum_id' ";
			if ( $is_subject ) $sql .= "AND m.parent_id='0' ";
			else $sql .= "AND m.parent_id != '0' ";
			$sql .= "GROUP BY m.forum_id";
			if ( $f->query($sql) && $f->next_record() )
			{
				return $f->Record['cnt'];
			}
			return false;
		}
		
        /**
         * Delete a file, or a folder and its contents
         *
         * @author      Aidan Lister <aidan@php.net>
         * @version     1.0.2
         * @param       string   $dirname    Directory to delete
         * @return      bool     Returns TRUE on success, FALSE on failure
         */
        function rmdirr($dirname)
        {
            // Sanity check
            if (!file_exists($dirname)) {
                return false;
            }
         
            // Simple delete for a file
            if (is_file($dirname)) {
                return unlink($dirname);
            }
         
            // Loop through the folder
            $dir = dir($dirname);
            while (false !== $entry = $dir->read()) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
         
                // Recurse
                rmdirr("$dirname/$entry");
            }
         
            // Clean up
            $dir->close();
            return rmdir($dirname);
        }
	}
?>