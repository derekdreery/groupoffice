<?php
  //adduser
  $username = $_GET["username"];

  if ($username) {
    //check lengths and qality
    $username = str_replace("'", "\\'", $username);
    if (strlen($username) > 20) $username = substr($username, 0, 20);

    //check if username is allowed
    if (isUsernameAllowed($username)) {
      //check for duplicate username
      $result = query("select id from chat_users where username='$username'");
      $originalusername = substr($username, 0, 17);
      $counter = 1;
      while ($result && fetch_object($result)) {
        $username = $originalusername."_".$counter++;
        $result = query("select id from chat_users where username='$username'");
      }

      $now = time();
      query("insert into chat_users (id, username, lastactive, chatroom) values (".(($CONNECTION == "pgsql")?("nextval('chat_users_sequence')"):("''")).", '$username', $now, 1)");

      $result = query("select id from chat_users where username='$username' and lastactive=$now and chatroom=1");
      if ($result) {
        $row = fetch_object($result);
        if ($row) echo ($row->id)."|".($username); else echo "0";
      } else echo "0";
    } else echo "0";
  }
