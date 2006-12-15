<?php
  //addmessage
  $chatroomid = $_GET["chatroomid"];
  $username = $_GET["username"];
  $message = $_GET["message"];
  $private = $_GET["private"];
  $color = $_GET["color"];

  if ($chatroomid && $username && $message) {
    //check lengths and qality
    $username = str_replace("'", "\\'", $username);
    if (strlen($username) > 20) $username = substr($username, 0, 20);
    $message = str_replace("'", "\\'", $message);


    if ($private) {
      $result = query("select chatroom from chat_users where id=$private");
      if ($result) {
        if ($row = fetch_object($result)) {
          $now = time();
          $chatroomid = $row->chatroom;
          query("insert into chat_messages (id, date, username, color, message, chatroom, private) values (".(($CONNECTION == "pgsql")?("nextval('chat_messages_sequence')"):("''")).", $now, '$username', $color, '$message', $chatroomid, $private)");
        }
      }
    }
    else {
      $now = time();
      query("insert into chat_messages (id, date, username, color, message, chatroom, private) values (".(($CONNECTION == "pgsql")?("nextval('chat_messages_sequence')"):("''")).", $now, '$username', $color, '$message', $chatroomid, 0)");
    }
  }
?>
