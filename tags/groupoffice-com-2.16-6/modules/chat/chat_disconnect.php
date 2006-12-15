<?php
  //disconnect
  $userid = $_GET["userid"];

  if ($userid) {
    query("delete from chat_users where id=".($userid)."");
    query("delete from chat_chatrooms where fromuser=".($userid)."");
  }
  echo "done";
?>
