<?php
  //login
  $chatroomid = $_GET["chatroomid"];
  $userid = $_GET["userid"];

  if ($chatroomid && $userid) {
    $now = time();
    query("update chat_users set lastactive=$now, chatroom=$chatroomid where id=$userid");

    $result = query("select id from chat_messages where chatroom=$chatroomid order by id desc ".(($CONNECTION == "pgsql")?("limit 1"):("limit 0, 1")));
    if ($result) {
      if ($row = fetch_object($result)) {
        echo $row->id;
      }
      else echo "0";
    }
    else echo "0";
  }

  query("delete from chat_messages where date<".(time() - 120)."");
?>
