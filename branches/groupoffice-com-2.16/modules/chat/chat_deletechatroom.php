<?php
  //deletechatroom
  $chatroomid = $_GET["chatroomid"];

  if ($chatroomid) {
    query("delete from chat_chatrooms where id=$chatroomid");
  }
  echo "done";
?>
