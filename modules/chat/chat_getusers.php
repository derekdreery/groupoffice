<?php
  //getusers

  
  //pre-deletion of users not active in 60 secs
  $todelete = time()-60;
  $result = query("select id from chat_users where lastactive<$todelete");
  if ($result) {
    while ($user = fetch_object($result)) {
      //delete the user
      query("delete from chat_users where id=".($user->id)."");
      //delete his chatrooms (if any)
      query("delete from chat_chatrooms where fromuser=".($user->id)."");
    }
  }
  

  $result = query("select * from chat_users order by id asc");
  if ($result) {
    while ($row = fetch_object($result)) {
      echo ($row->id)."|".($row->username)."|".($row->chatroom)."\n";
    }
    echo "done\n";
  }
