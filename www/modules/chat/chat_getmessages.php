<?php
  //getmessages
  $chatroomid = $_GET["chatroomid"];
  $userid = $_GET["userid"];
  $lastid = $_GET["lastid"];

  if ($chatroomid && $userid) {
    if (!$lastid) $lastid=0;
    $now = time();
    query("update chat_users set lastactive=$now where id=$userid");

    $result = query("select * from chat_messages where id>$lastid and chatroom=$chatroomid order by id asc");
    if ($result) {
      while ($row = fetch_object($result)) {
        echo ($row->id)."|".($row->date)."|".($row->username)."|".($row->color)."|".($row->message)."|".($row->chatroom)."|".($row->private)."\n";
      }
    }
    echo "done\n";
  }
